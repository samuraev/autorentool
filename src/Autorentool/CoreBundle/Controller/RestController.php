<?php

/**
 * Controller class for REST request of Tasks.
 *
 * @author Alexey Zamuraev
 * @version 0.02
 */

namespace Autorentool\CoreBundle\Controller;

use Autorentool\CoreBundle\Entity\TAssessmentItem;
use Autorentool\CoreBundle\GenerateTasks\GenerateXML;
use Autorentool\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @property  serializer
 */
class RestController extends Controller
{
    /**
     * @Route("/alltasks", name="getalltasks")
     */
    public function ajaxGetAllTasksAction(Request $request)
    {
        $data = json_decode($request->getContent());

        if ($data) {
            $username = $data->username;
            $password = $data->password;

            $user = $this->checkUser($username, $password);

            if ($user) {
                $assessmentItems = $this->getDoctrine()
                    ->getRepository(TAssessmentItem::class)
                    ->findByFactoryName($user->getFactoryName());

                $returnDataArray = array();

                if ($assessmentItems) {
                    foreach ($assessmentItems as $assessmentItem) {

                        $returnAssessmentArray = array();

                        $data = array('uuid' => $assessmentItem->getUuid(),
                            'title' => $assessmentItem->getTittle(),
                            'creationTimeStamp' => $assessmentItem->getCreationTimestamp(),
                            'identifier' => $assessmentItem->getIdentifier());

                        $returnAssessmentArray["assessment"] = $data;
                        array_push($returnDataArray, $returnAssessmentArray);
                    }

                    $jsonContent = json_encode($returnDataArray);
                    return new JsonResponse(array('data' => $jsonContent, "status" => "success"));
                }
            }
        }

        return new JsonResponse(array('data' => null, "status" => "Benutzername/Passwort ist falsch oder Aufgaben wurden nicht gefunden"));
    }

    /**
     * @Route("/task", name="gettask")
     */
    public function ajaxGetTaskAction(Request $request)
    {
        $data = json_decode($request->getContent());

        if ($data) {
            $username = $data->username;
            $password = $data->password;

            $user = $this->checkUser($username, $password);

            if ($user) {
                $uuid = $data->uuid;
                
                $assessmentItem = $this->getDoctrine()
                    ->getRepository(TAssessmentItem::class)
                    ->findOneByUuid($uuid);

                if ($assessmentItem) {
                    // set url for media in assessment
                    $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
                    $this->setMediaUrls($assessmentItem, $baseurl);

                    // generate xml objects
                    $generateXML = new GenerateXML();
                    $returnDataArray = array();

                    $identifier = $assessmentItem->getIdentifier();
                    $returnAssessmentArray = array();

                    $xml = $generateXML->GenerateAssessmentXML($identifier, $assessmentItem);
                    $returnAssessmentArray["assessment"] = $xml;

                    if ($assessmentItem->getRelated() != NULL) {
                        $xml = $generateXML->GenerateAssessmentXML("related", $assessmentItem->getRelated());
                        $returnAssessmentArray["related"] = $xml;
                    }

                    if ($assessmentItem->getSupport() != NULL) {
                        $xml = $generateXML->GenerateAssessmentXML("support", $assessmentItem->getSupport());
                        $returnAssessmentArray["support"] = $xml;
                    }

                    array_push($returnDataArray, $returnAssessmentArray);

                    $jsonContent = json_encode($returnDataArray);
                    return new JsonResponse(array('data' => $jsonContent, "status" => "success"));
                }
            }
        }

        return new JsonResponse(array('data' => null, "status" => "Benutzername, Passwort oder AufgabeUuid ist falsch"));
    }

    private function checkUser($username, $password)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em
            ->createQueryBuilder('u')
            ->from('AutorentoolUserBundle:User','u')
            ->where('u.username = :username')
            ->setParameter('username', $username)
            ->select('u')
            ->getQuery()
            ->getOneOrNullResult();

        if ($user) {
            // Get the encoder for the users password
            $encoder_service = $this->get('security.encoder_factory');
            $encoder = $encoder_service->getEncoder($user);

            if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
                return $user;
            }
        }

        return null;
    }

    private function setMediaUrls($assessmentItem, $baseurl) {

        if ($assessmentItem->getItemBody()->getImgSrc() != null) {
            $path ='uploads/img/'. $assessmentItem->getIdentifier().'/'.$assessmentItem->getItemBody()->getImgSrc();

            $url = $baseurl.'/'.$path;
            $assessmentItem->getItemBody()->setImgSrc($url);
        }

        if ($assessmentItem->getIdentifier() === 'choice' or
            $assessmentItem->getIdentifier() === 'choiceMultiple') {

            $simpleChoices = $assessmentItem->getItemBody()->getChoiceInteraction()->getSimpleChoices();
            foreach ($simpleChoices as $simpleChoice) {
                if ($simpleChoice->getImgSrc() != null) {
                    $path ='uploads/img/'. $assessmentItem->getIdentifier().'/'.$simpleChoice->getImgSrc();

                    $url = $baseurl.'/'.$path;
                    $simpleChoice->setImgSrc($url);
                }
            }
        }

         if ($assessmentItem->getIdentifier() === 'positionObjects') {
             $outerObject = $assessmentItem->getItemBody()->getHotspotInteraction()->getOuterObject();
             $fileName = $outerObject->getData();

             if ($fileName and file_exists($this->getParameter('hotspot_img_directory').'/'.$fileName)) {
                 $path ='uploads/img/'. $assessmentItem->getIdentifier().'/'.$fileName;

                 $url = $baseurl.'/'.$path;
                 $outerObject->setData($url);
             }

             $innerObjects = $assessmentItem->getItemBody()->getHotspotInteraction()->getInnerObject();
             if ($innerObjects != null) {
                 foreach ($innerObjects as $innerObject) {
                     $fileName = $innerObject->getData();

                     if ($fileName and file_exists($this->getParameter('hotspot_img_directory').'/'.$fileName)) {
                         $path ='uploads/img/'. $assessmentItem->getIdentifier().'/'.$fileName;

                         $url = $baseurl.'/'.$path;
                         $innerObject->setData($url);
                     }
                 }
             }
         }

        if ($assessmentItem->getSupport() != NULL) {
            $support = $assessmentItem->getSupport();

            if ($support->getSupportMedia() != null) {
                $path ='uploads/support/'.$support->getSupportMedia()->getMediaSource();

                $url = $baseurl.'/'.$path;
                $support->getSupportMedia()->setMediaSource($url);
            }
        }
    }
}