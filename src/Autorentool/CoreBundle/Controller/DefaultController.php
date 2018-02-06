<?php

/**
 * Main controller class.
 *
 * @author Alexey Zamuraev
 * @version 0.05
 */

namespace Autorentool\CoreBundle\Controller;

use Autorentool\CoreBundle\Entity\TCategories;
use Autorentool\CoreBundle\Entity\TPalcommandGroups;
use Autorentool\CoreBundle\Entity\TAreaMapEntry;
use Autorentool\CoreBundle\Entity\TAreaMapping;
use Autorentool\CoreBundle\Entity\TAssessmentItem;
use Autorentool\CoreBundle\Entity\TCategoryTags;
use Autorentool\CoreBundle\Entity\TCell;
use Autorentool\CoreBundle\Entity\TChoiceInteraction;
use Autorentool\CoreBundle\Entity\TColumnIdentifier;
use Autorentool\CoreBundle\Entity\TCorrectResponse;
use Autorentool\CoreBundle\Entity\TDragInteraction;
use Autorentool\CoreBundle\Entity\TDragItem;
use Autorentool\CoreBundle\Entity\TGroupItem;
use Autorentool\CoreBundle\Entity\THotspotInteraction;
use Autorentool\CoreBundle\Entity\TInnerObject;
use Autorentool\CoreBundle\Entity\TItemBody;
use Autorentool\CoreBundle\Entity\TMapEntry;
use Autorentool\CoreBundle\Entity\TMapping;
use Autorentool\CoreBundle\Entity\TOuterObject;
use Autorentool\CoreBundle\Entity\TPalcommandTypes;
use Autorentool\CoreBundle\Entity\TRelated;
use Autorentool\CoreBundle\Entity\TResponseDeclaration;
use Autorentool\CoreBundle\Entity\TRow;
use Autorentool\CoreBundle\Entity\TSelectionItem;
use Autorentool\CoreBundle\Entity\TSimpleChoice;
use Autorentool\CoreBundle\Entity\TSupport;
use Autorentool\CoreBundle\Entity\TSupportMedia;
use Autorentool\CoreBundle\Entity\TSupportSelection;
use Autorentool\CoreBundle\Entity\TSupportTable;
use Autorentool\CoreBundle\Entity\TSupportTextbox;
use Autorentool\CoreBundle\Entity\TTable;
use Autorentool\CoreBundle\Entity\TTableInteraction;
use Autorentool\CoreBundle\Entity\TTaskPackages;
use Autorentool\CoreBundle\Entity\TValue;
use Autorentool\CoreBundle\Form\TAssessmentItemType;
use Autorentool\CoreBundle\GenerateTasks\GenerateXML;
use Autorentool\CoreBundle\HelpFunctions\HelpFunctions;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Expr\Value;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\Query;
use finfo;
use FOS\UserBundle\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="taskspool")
     */
    public function taskspoolAction()
    {
        $user = $this->getUser();

        $assessmentItems = $this->getDoctrine()
            ->getRepository(TAssessmentItem::class)
            ->findByFactoryName($user->getFactoryName());

        return $this->render('AutorentoolCoreBundle:Taskspool:taskspool.html.twig', array(
            'assessemntItems' => $assessmentItems
        ));
    }

    /**
     * @Route("/taskspackages", name="taskspackages")
     */
    public function taskspackagesAction(Request $request)
    {
        $user = $this->getUser();

        $tasksPackages = $this->getDoctrine()
            ->getRepository(TTaskPackages::class)
            ->findByFactoryName($user->getFactoryName());

        return $this->render('AutorentoolCoreBundle:Taskspackagespool:taskspackagespool.html.twig', array(
            'taskspackages' => $tasksPackages
        ));
    }

    /**
     * @Route("/deletepackage/{uuid}", name="deletepackage")
     */
    public function deletepackageAction($uuid)
    {
        $em = $this->getDoctrine()->getManager();
        $helpFunctions = new HelpFunctions();

        // get package object
        $tasksPackages = $em->getRepository(TTaskPackages::class)->findOneByUuid($uuid);

        // create path to package in system
        $tasksPackagePath = $this->getParameter('packages_directory').'/'.$uuid;

        if (file_exists($tasksPackagePath)) {
            // delete package folder with all data from system
            $helpFunctions->deleteDir($tasksPackagePath);
        }

        // remove package from db
        $em->remove($tasksPackages);
        $em->flush();

        return $this->redirectToRoute('taskspackages');
    }

    /**
     * @Route("/stateoftask/{uuid}", name="stateoftask")
     */
    public function stateoftaskAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();
        $assessmentItem = $em->getRepository(TAssessmentItem::class)->findOneByUuid($uuid);

        if ($assessmentItem != null) {
            if ($assessmentItem->getStateOfTask()) {
                return new JsonResponse(array('status' => true));
            } else {
                return new JsonResponse(array('status' => false));
            }
        } else {
            return new JsonResponse(array('status' => -1));
        }
    }

    /**
     * @Route("/taskaktiv/{uuid}", name="taskaktiv")
     */
    public function taskaktivAction(Request $request, $uuid)
    {
        // NOT used
        $em = $this->getDoctrine()->getManager();
        $assessmentItem = $em->getRepository(TAssessmentItem::class)->findOneByUuid($uuid);

        if ($assessmentItem) {
            if ($assessmentItem->getStateOfTask()) {
                return $this->redirectToRoute('taskspool');
            }

            $assessmentItem->setStateOfTask(true);
            $em->persist($assessmentItem);
            $em->flush();
        }

        return $this->redirectToRoute('taskspool');
    }

    /**
     * Delete Task by Uuid
     * @Route("/deletetask/{uuid}", name="deletetask")
     */
    public function deletetaskAction($uuid)
    {
        $em = $this->getDoctrine()->getManager();
        $assessmentItem = $em->getRepository(TAssessmentItem::class)->findOneByUuid($uuid);

        if ($assessmentItem) {
            // Remove single-/miltiplechoice task from file system
            if ($assessmentItem->getIdentifier() === 'choice' or $assessmentItem->getIdentifier() === 'choiceMultiple') {
                $imagesPath = "";
                if ($assessmentItem->getIdentifier() === 'choice') {
                    $imagesPath = $this->getParameter('singleChoice_img_directory');
                } elseif ($assessmentItem->getIdentifier() === 'choiceMultiple') {
                    $imagesPath = $this->getParameter('multipleChoice_img_directory');
                }

                if ($imagesPath) {
                    if ($assessmentItem->getItemBody()->getImgSrc() != null) {
                        $fileFullPath = $imagesPath . '/' . $assessmentItem->getItemBody()->getImgSrc();
                        if (file_exists($fileFullPath)) {
                            $fs = new Filesystem();
                            $fs->remove($fileFullPath);
                        }
                    }

                    $simpleChoice = $assessmentItem->getItemBody()->getChoiceInteraction()->getSimpleChoices();
                    foreach ($simpleChoice as $item) {
                        if ($item->getImgSrc() != null) {
                            $fileFullPath = $imagesPath . '/' . $item->getImgSrc();
                            if (file_exists($fileFullPath)) {
                                $fs = new Filesystem();
                                $fs->remove($fileFullPath);
                            }
                        }
                    }
                }
            }

            // Remove hotspot task from file system
            if ($assessmentItem->getIdentifier() === 'positionObjects') {
                if ($assessmentItem->getItemBody()->getHotspotInteraction()->getOuterObject()->getData() != null) {
                    $imagesPath = $this->getParameter('hotspot_img_directory');
                    $fileFullPath = $imagesPath . '/' . $assessmentItem->getItemBody()->getHotspotInteraction()->getOuterObject()->getData();
                    if (file_exists($fileFullPath)) {
                        $fs = new Filesystem();
                        $fs->remove($fileFullPath);
                    }
                }

                foreach ($assessmentItem->getItemBody()->getHotspotInteraction()->getInnerObject() as $item) {
                    if ($item->getData() != null) {
                        $fileFullPath = $imagesPath . '/' . $item->getData();
                        if (file_exists($fileFullPath)) {
                            $fs = new Filesystem();
                            $fs->remove($fileFullPath);
                        }
                    }
                }
            }

            // Remove table or dragndropTable task images from file system
            if ($assessmentItem->getIdentifier() === 'table' or $assessmentItem->getIdentifier() === 'dragndropTable') {
                $imagesPath = "";
                if ($assessmentItem->getIdentifier() === 'table') {
                    $imagesPath = $this->getParameter('table_img_directory');
                } elseif ($assessmentItem->getIdentifier() === 'dragndropTable') {
                    $imagesPath = $this->getParameter('dragndropTable_img_directory');
                }

                if ($imagesPath) {
                    if ($assessmentItem->getItemBody()->getImgSrc() != null) {
                        $fileFullPath = $imagesPath . '/' . $assessmentItem->getItemBody()->getImgSrc();
                        if (file_exists($fileFullPath)) {
                            $fs = new Filesystem();
                            $fs->remove($fileFullPath);
                        }
                    }
                }
            }

            // delete support images or video
            if ($assessmentItem->getSupport() != null) {
                if ($assessmentItem->getSupport()->getSupportMedia() != null) {
                    if ($assessmentItem->getSupport()->getSupportMedia()->getMediaSource() != null) {
                        $mediaPath = $this->getParameter('support_directory');

                        if ($mediaPath) {
                            $fileFullPath = $mediaPath . '/' . $assessmentItem->getSupport()->getSupportMedia()->getMediaSource();
                            if (file_exists($fileFullPath)) {
                                $fs = new Filesystem();
                                $fs->remove($fileFullPath);
                            }
                        }
                    }
                }
            }

            $em->remove($assessmentItem);
            $em->flush();
        }
        return $this->redirectToRoute('taskspool');
    }

    /**
     * @Route("/taskedit/multiple/{uuid}", name="taskeditmultiple")
     */
    public function taskeditMultipleChoiceAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();
        $assessmentItem = $em->getRepository(TAssessmentItem::class)->findOneByUuid($uuid);

        if ($assessmentItem) {
            $assessmentItem->setStateOfTask(false);
            $em->persist($assessmentItem);
            $em->flush();
        } else {
            return $this->redirectToRoute('taskspool');
        }

        $categories = $em->getRepository(TCategories::class)->findAll();
        $values = $assessmentItem->getResponseDeclaration()->getCorrectResponce()->getValues();
        $simpleChoicesOld = $assessmentItem->getItemBody()->getChoiceInteraction()->getSimpleChoices();

        foreach ($values as $value) {
            foreach ($simpleChoicesOld as $simpleChoice) {
                if ($simpleChoice->getIdentifier() === $value->getValue()) {
                    $simpleChoice->setIsrightanswer(true);
                }
            }
        }

        $imagesPath = $this->getParameter('multipleChoice_img_directory');

        // image name in db for tittle
        $oldFileName = $assessmentItem->getItemBody()->getImgSrc();
        // create file object if image name exists in db and in file system
        if ($oldFileName and file_exists($imagesPath.'/'.$oldFileName)) {
            $uploadedFile = new UploadedFile($imagesPath.'/'.$oldFileName, "");
            $assessmentItem->getItemBody()->setImgSrc($uploadedFile);
        } else {
            $assessmentItem->getItemBody()->setImgSrc(null);
        }

        // image name in db for answers
        $simpleChoiceImages = new ArrayCollection();
        foreach ($simpleChoicesOld as $simpleChoice) {
            // create file object if image name exists in db and in file system
            if ($simpleChoice->getImgSrc() and file_exists($imagesPath.'/'
                    .$simpleChoice->getImgSrc())) {
                $simpleChoiceImages->set($simpleChoice->getId(), $simpleChoice->getImgSrc());

                $uploadedFile = new UploadedFile($imagesPath.'/'.$simpleChoice
                        ->getImgSrc(), "");
                $simpleChoice->setImgSrc($uploadedFile);
            } else {
                $simpleChoiceImages->set($simpleChoice->getId(), null);
                $simpleChoice->setImgSrc(null);
            }
        }

        $currentCategories = [];
        foreach ($assessmentItem->getCategoryTags() as $category) {
            array_push($currentCategories, $category->getTagName());
        }
        $assessmentItem->setCurrentCategory($currentCategories);

        $returnSelAndOldFileName = $this->prepareSupportForEdit($assessmentItem);
        $currentSelections = $returnSelAndOldFileName[0];
        $oldFileSupportMediaName = $returnSelAndOldFileName[1];

        $form = $this->createForm(TAssessmentItemType::class, $assessmentItem, array(
            'categories' => $categories,
            'selection' => $currentSelections,
            'tasktype' => "multiple",

        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // add or remove category tags
            $currentCategories = array();
            if ($assessmentItem->getCurrentCategory() != null) {
                $currentCategories = explode(",", $assessmentItem->getCurrentCategory());

                foreach ($assessmentItem->getCategoryTags() as $categoryTag) {
                    if (in_array($categoryTag->getTagName(), $currentCategories)) {
                        $index = array_search($categoryTag->getTagName(), $currentCategories);
                        array_splice($currentCategories, $index, 1);

                    } elseif (!in_array($categoryTag->getTagName(), $currentCategories)) {
                        $em->remove($categoryTag);
                    }
                }

                if (count($currentCategories) > 0) {
                    foreach ($currentCategories as $category) {
                        $categoryTag = new TCategoryTags();
                        $categoryTag->setTagName($category);
                        $assessmentItem->setCategoryTags($categoryTag);
                    }
                    // add new category in db categories
                    $this->addNewCategoryInDB($categories, $currentCategories, $em);
                }
            } else {
                // remove all values
                foreach ($assessmentItem->getCategoryTags() as $value) {
                    $em->remove($value);
                }

                $assessmentItem->removeCategoryTags();
            }


            if ($assessmentItem->getCategoryTags() != null and $assessmentItem->getRelated() == null) {
                //var_dump("a");die;
                $this->setRelated($assessmentItem, $em);
            } elseif ($assessmentItem->getCategoryTags() == null and $assessmentItem->getRelated() != null) {
                //var_dump("b");die;
                $em->remove($assessmentItem->getRelated());
                $assessmentItem->removeRelated();
            } elseif ($assessmentItem->getCategoryTags() != null and $assessmentItem->getRelated() != null) {
                $this->setRelated($assessmentItem, $em);
            }

            //support
            $this->editSupportAssessment($assessmentItem, $em, $oldFileSupportMediaName);

            $simpleChoicesNew = $assessmentItem->getItemBody()->getChoiceInteraction()->getSimpleChoices();

            // leave old image of tittle (it is already in db and in file system)
            if ( ($assessmentItem->getItemBody()->getRemoveImgSrcState() === false) and
                ($assessmentItem->getItemBody()->getImgSrc() === NULL) ) {
                // set path of old image in object
                $assessmentItem->getItemBody()->setImgSrc($oldFileName);
            } elseif ($assessmentItem->getItemBody()->getRemoveImgSrcState() === true) {
                // remove new set image from object (it is not in filesystem
                if ($assessmentItem->getItemBody()->getImgSrc() != null) {
                    $assessmentItem->getItemBody()->setImgSrc(null);
                } else {
                    // remove old image from filesystem
                    if (file_exists($imagesPath.'/'.$oldFileName)){
                        $fs = new Filesystem();
                        $fs->remove($imagesPath.'/'.$oldFileName);
                    }
                }
            } else {
                // set new image path and save in filesystem
                // remove old file if it was changed with new
                if ($oldFileName and file_exists($imagesPath.'/'.$oldFileName)) {
                    $fs = new Filesystem();
                    $fs->remove($imagesPath.'/'.$oldFileName);
                }

                // $file stores the uploaded file
                $file = $assessmentItem->getItemBody()->getImgSrc();
                if ($file != null) {
                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $file->move($imagesPath, $fileName);

                    // Update the 'imgSrc' property to store the image file name
                    // instead of its contents
                    $assessmentItem->getItemBody()->setImgSrc($fileName);
                }
            }

            // leave old image of antworts (it is already in db and in file system)
            foreach ($simpleChoicesNew as $simpleChoice) {
                // leave old image (it is already in db and in file system
                if (($simpleChoice->getRemoveImgSrcState() === false) and
                    ($simpleChoice->getImgSrc() === NULL)) {

                    // set path of old image in object
                    $simpleChoice->setImgSrc($simpleChoiceImages->get($simpleChoice->getId()));

                } elseif ($simpleChoice->getRemoveImgSrcState() === true) {
                    // remove new set image from object (it is not in filesystem)
                    if ($simpleChoice->getImgSrc() != null) {
                        $simpleChoice->setImgSrc(null);
                    } else {
                        // remove old image from filesystem
                        if (file_exists($imagesPath.'/'.$simpleChoiceImages
                                ->get($simpleChoice->getId()))) {
                            $fs = new Filesystem();
                            $fs->remove($imagesPath . '/' . $simpleChoiceImages
                                    ->get($simpleChoice->getId()));
                        }
                    }
                } else {
                    // set new image path and save in filesystem
                    // remove old file if it was changed with new
                    if ($simpleChoiceImages->get($simpleChoice->getId()) and
                        file_exists($imagesPath . '/'.$simpleChoiceImages
                                ->get($simpleChoice->getId()))) {

                        $fs = new Filesystem();
                        $fs->remove($imagesPath.'/'.$simpleChoiceImages
                                ->get($simpleChoice->getId()));
                    }

                    // $file stores the uploaded file
                    $file = $simpleChoice->getImgSrc();
                    if ($file != null) {
                        // Generate a unique name for the file before saving it
                        $fileName = md5(uniqid()).'.'.$file->guessExtension();

                        // Move the file to the directory where brochures are stored
                        $file->move($imagesPath, $fileName);

                        // Update the 'imgSrc' property to store the image file name
                        // instead of its contents
                        $simpleChoice->setImgSrc($fileName);
                    }
                }
            }

            // remove all values
            foreach ($values as $value) {
                $em->remove($value);
            }

            // remove old answers from db and images
            foreach ($simpleChoicesOld as $simpleChoice) {
                $deleted = true;

                foreach ($simpleChoicesNew as $item) {
                    if ($simpleChoice->getIdentifier() === $item->getIdentifier()) {
                        $deleted = false;
                    }
                }

                if ($deleted) {
                    if ($simpleChoice->getImgSrc()->getFileName() and
                        file_exists($imagesPath . '/'.$simpleChoice->getImgSrc()->getFileName())) {
                        $fs = new Filesystem();
                        $fs->remove($imagesPath.'/'.$simpleChoice->getImgSrc()->getFileName());
                    }
                    $em->remove($simpleChoice);
                }
            }

            $mapping = $assessmentItem->getResponseDeclaration()->getMapping();
            foreach ($mapping->getMapEntry() as $mapEntry) {
                $em->remove($mapEntry);
            }

            // add new
            $maxChoiceCount = 0;
            foreach ($simpleChoicesNew as $simpleChoice) {
                if ($simpleChoice->getIdentifier() === null) {
                    $simpleChoice->setIdentifier(uniqid());
                }

                $mapEntry = new TMapEntry();
                $mapEntry->setMapKey($simpleChoice->getIdentifier());
                if ($simpleChoice->getIsrightanswer() === true) {
                    $value = new TValue();
                    $value->setValue($simpleChoice->getIdentifier());
                    $assessmentItem->getResponseDeclaration()->getCorrectResponce()->setValues($value);

                    $mapEntry->setMappedValue(1);
                    $maxChoiceCount += 1;
                } else {
                    $mapEntry->setMappedValue(-1);
                }
                $mapping->setMapEntry($mapEntry);
            }

            $mapping->setLowerBound($maxChoiceCount);
            $mapping->setUpperBound($maxChoiceCount);
            $mapping->setDefaultValue(0);

            $assessmentItem->setStateOfTask(true);

            $em->flush();
            return $this->redirectToRoute('taskspool');
        }

        return $this->render('AutorentoolCoreBundle:Tasks:newtask.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Create new Single-Choice Task
     * @Route("/newtask/singlechoice", name="newtasksinglechoice")
     */
    public function singlechoiceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(TCategories::class)->findAll();

        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $assessmentItem = new TAssessmentItem();

        $helpFunctions = new HelpFunctions();
        $assessmentItem->setUuid($helpFunctions->generateUUIDv4());

        $assessmentItem->setCreationTimestamp((string)$timestamp);
        $assessmentItem->setIdentifier('choice');
        $assessmentItem->setTimeDependent(false);
        $assessmentItem->setAdaptive(false);

        $support = new TSupport();
        $supportTextBox = new TSupportTextbox();
        $supportMedia = new TSupportMedia();
        $supportSelection = new TSupportSelection();
        $supportTable = new TSupportTable();

        $support->setSupportType('media');
        $support->setSupportTextbox($supportTextBox);
        $support->setSupportMedia($supportMedia);
        $support->setSupportSelection($supportSelection);

        $supportRow = new TRow();
        $supportCell1 = new TCell();
        //$supportCell1->setCellIdentifier(uniqid());
        $supportRow->addCell($supportCell1);
        $supportCell2 = new TCell();
        //$supportCell2->setCellIdentifier(uniqid());
        $supportRow->addCell($supportCell2);
        $supportTable->addRow($supportRow);
        $support->setSupportTable($supportTable);
        $assessmentItem->setSupport($support);

        $user = $this->getUser();
        $assessmentItem->setFactoryName($user->getFactoryName());

        $tItemBody = new TItemBody();

        $choiceInteraction = new TChoiceInteraction();
        $choiceInteraction->setShuffle(false);

        $simpleChoices = new TSimpleChoice();
        $simpleChoices->setIdentifier(uniqid());
        $simpleChoices2 = new TSimpleChoice();
        $simpleChoices2->setIdentifier(uniqid());

        $choiceInteraction->setResponseIdentifier("RESPONSE");
        $choiceInteraction->addSimpleChoices($simpleChoices);
        $choiceInteraction->addSimpleChoices($simpleChoices2);
        $choiceInteraction->setMaxChoices(1);

        $responseDeclaration = new TResponseDeclaration();
        $responseDeclaration->setIdentifier($choiceInteraction->getResponseIdentifier());
        $responseDeclaration->setCardinality("single");
        $responseDeclaration->setBaseType("identifier");

        $correctResponce = new TCorrectResponse();
        $assessmentItem->setResponseDeclaration($responseDeclaration);
        $tItemBody->setChoiceInteraction($choiceInteraction);
        $assessmentItem->setItemBody($tItemBody);

        $form = $this->createForm(TAssessmentItemType::class, $assessmentItem, array(
            'categories' => $categories,
            'tasktype' => "single"
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // add categories to assessment
            $currentCategories = $this->addCategoriesAssessment($assessmentItem);
            // add new category in db categories
            $this->addNewCategoryInDB($categories, $currentCategories, $em);

            // $file stores the uploaded file
            $file = $tItemBody->getImgSrc();
            $imagesPath = $this->getParameter('singleChoice_img_directory');
            if ($file != null) {
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move($imagesPath, $fileName);
                // Update the 'imgSrc' property to store the image file name
                // instead of its contents
                $tItemBody->setImgSrc($fileName);
            }

            // support
            $isSupport = $this->setNewSupportAssessment($support);
            if ($isSupport) {
                $support->setUuid($helpFunctions->generateUUIDv4());
                $support->setAssessmentUuid($assessmentItem->getUuid());
                $support->setCreationTimestamp((string)$timestamp);
            } else {
                $assessmentItem->removeSupport();
            }

            foreach ($choiceInteraction->getSimpleChoices() as $simpleChoice) {
                // $file stores the uploaded file
                $file = $simpleChoice->getImgSrc();

                if ($file != null) {
                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $file->move($imagesPath, $fileName
                    );
                    // Update the 'imgSrc' property to store the image file name
                    // instead of its contents
                    $simpleChoice->setImgSrc($fileName);
                }

                if ($simpleChoice->getIdentifier() === null) {
                    $simpleChoice->setIdentifier(uniqid());
                }

                if ($simpleChoice->getIsrightanswer() === true) {
                    $value = new TValue();
                    $value->setValue($simpleChoice->getIdentifier());
                    $correctResponce->setValues($value);
                }
            }

            $responseDeclaration->setCorrectResponce($correctResponce);

            $this->setRelated($assessmentItem, $em);

            $em->persist($assessmentItem);
            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            return $this->redirect($this->generateUrl('taskspool'));
        }

        return $this->render('AutorentoolCoreBundle:Tasks:newtask.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Edit Single-Choice Task by Uuid
     * @Route("/taskedit/single/{uuid}", name="taskeditsingle")
     */
    public function taskeditSingleChoiceAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();
        $assessmentItem = $em->getRepository(TAssessmentItem::class)->findOneByUuid($uuid);

        if ($assessmentItem) {
            $assessmentItem->setStateOfTask(false);
            $em->persist($assessmentItem);
            $em->flush();
        } else {
            return $this->redirectToRoute('taskspool');
        }

        $categories = $em->getRepository(TCategories::class)->findAll();
        $values = $assessmentItem->getResponseDeclaration()->getCorrectResponce()->getValues();
        $simpleChoicesOld = $assessmentItem->getItemBody()->getChoiceInteraction()->getSimpleChoices();

        foreach ($values as $value) {
            foreach ($simpleChoicesOld as $simpleChoice) {
                if ($simpleChoice->getIdentifier() === $value->getValue()) {
                    $simpleChoice->setIsrightanswer(true);
                } else {
                    $simpleChoice->setIsrightanswer(false);
                }
            }
        }

        $imagesPath = $this->getParameter('singleChoice_img_directory');

        // image name in db for tittle
        $oldFileName = $assessmentItem->getItemBody()->getImgSrc();

        // create file object if image name exists in db and in file system
        if ($oldFileName and file_exists($imagesPath.'/'.$oldFileName)) {
            $uploadedFile = new UploadedFile($imagesPath.'/'.$oldFileName, "");
            $assessmentItem->getItemBody()->setImgSrc($uploadedFile);
        } else {
            $assessmentItem->getItemBody()->setImgSrc(null);
        }

        // image name in db for answers
        $simpleChoiceImages = new ArrayCollection();
        foreach ($simpleChoicesOld as $simpleChoice) {
            // create file object if image name exists in db and in file system
            if ($simpleChoice->getImgSrc() and file_exists($imagesPath.'/'.$simpleChoice->getImgSrc())) {
                $simpleChoiceImages->set($simpleChoice->getId(), $simpleChoice->getImgSrc());

                $uploadedFile = new UploadedFile($imagesPath.'/'.$simpleChoice->getImgSrc(), "");
                $simpleChoice->setImgSrc($uploadedFile);
            } else {
                $simpleChoiceImages->set($simpleChoice->getId(), null);
                $simpleChoice->setImgSrc(null);
            }
        }

        $currentCategories = [];
        foreach ($assessmentItem->getCategoryTags() as $category) {
            array_push($currentCategories, $category->getTagName());
        }
        $assessmentItem->setCurrentCategory($currentCategories);

        $returnSelAndOldFileName = $this->prepareSupportForEdit($assessmentItem);
        $currentSelections = $returnSelAndOldFileName[0];
        $oldFileSupportMediaName = $returnSelAndOldFileName[1];

        $form = $this->createForm(TAssessmentItemType::class, $assessmentItem, array(
            'categories' => $categories,
            'selection' => $currentSelections,
            'tasktype' => "single",
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // add or remove category tags
            $currentCategories = array();
            if ($assessmentItem->getCurrentCategory() != null) {
                $currentCategories = explode(",", $assessmentItem->getCurrentCategory());

                foreach ($assessmentItem->getCategoryTags() as $categoryTag) {
                    if (in_array($categoryTag->getTagName(), $currentCategories)) {
                        $index = array_search($categoryTag->getTagName(), $currentCategories);
                        array_splice($currentCategories, $index, 1);

                    } elseif (!in_array($categoryTag->getTagName(), $currentCategories)) {
                        $em->remove($categoryTag);
                    }
                }

                if (count($currentCategories) > 0) {
                    foreach ($currentCategories as $category) {
                        $categoryTag = new TCategoryTags();
                        $categoryTag->setTagName($category);
                        $assessmentItem->setCategoryTags($categoryTag);
                    }
                    // add new category in db categories
                    $this->addNewCategoryInDB($categories, $currentCategories, $em);
                }
            } else {
                // remove all values
                foreach ($assessmentItem->getCategoryTags() as $value) {
                    $em->remove($value);
                }

                $assessmentItem->removeCategoryTags();
            }


            if ($assessmentItem->getCategoryTags() != null and $assessmentItem->getRelated() == null) {
                //var_dump("a");die;
                $this->setRelated($assessmentItem, $em);
            } elseif ($assessmentItem->getCategoryTags() == null and $assessmentItem->getRelated() != null) {
                //var_dump("b");die;
                $em->remove($assessmentItem->getRelated());
                $assessmentItem->removeRelated();
            } elseif ($assessmentItem->getCategoryTags() != null and $assessmentItem->getRelated() != null) {
                $this->setRelated($assessmentItem, $em);
            }

            //support
            $this->editSupportAssessment($assessmentItem, $em, $oldFileSupportMediaName);

            $simpleChoicesNew = $assessmentItem->getItemBody()->getChoiceInteraction()->getSimpleChoices();

            // leave old image of tittle (it is already in db and in file system)
            if (($assessmentItem->getItemBody()->getRemoveImgSrcState() === false) and
                ($assessmentItem->getItemBody()->getImgSrc() === NULL)) {
                // set path of old image in object
                $assessmentItem->getItemBody()->setImgSrc($oldFileName);
            } elseif ($assessmentItem->getItemBody()->getRemoveImgSrcState() === true) {
                // remove new set image from object (it is not in filesystem
                if ($assessmentItem->getItemBody()->getImgSrc() != null) {
                    $assessmentItem->getItemBody()->setImgSrc(null);
                } else {
                    // remove old image from filesystem
                    if (file_exists($imagesPath . '/' . $oldFileName)) {
                        $fs = new Filesystem();
                        $fs->remove($imagesPath . '/' . $oldFileName);
                    }
                }
            } else {
                // set new image path and save in filesystem
                // remove old file if it was changed with new
                if ($oldFileName and file_exists($imagesPath . '/' . $oldFileName)) {
                    $fs = new Filesystem();
                    $fs->remove($imagesPath . '/' . $oldFileName);
                }

                // $file stores the uploaded file
                $file = $assessmentItem->getItemBody()->getImgSrc();
                if ($file != null) {
                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $file->move($imagesPath, $fileName);

                    // Update the 'imgSrc' property to store the image file name
                    // instead of its contents
                    $assessmentItem->getItemBody()->setImgSrc($fileName);
                }
            }

            // leave old image of antworts (it is already in db and in file system)
            foreach ($simpleChoicesNew as $simpleChoice) {
                // leave old image (it is already in db and in file system
                if (($simpleChoice->getRemoveImgSrcState() === false) and
                    ($simpleChoice->getImgSrc() === NULL)) {

                    // set path of old image in object
                    $simpleChoice->setImgSrc($simpleChoiceImages->get($simpleChoice->getId()));

                } elseif ($simpleChoice->getRemoveImgSrcState() === true) {
                    // remove new set image from object (it is not in filesystem)
                    if ($simpleChoice->getImgSrc() != null) {
                        $simpleChoice->setImgSrc(null);
                    } else {
                        // remove old image from filesystem
                        if (file_exists($imagesPath . '/' . $simpleChoiceImages
                                ->get($simpleChoice->getId()))) {
                            $fs = new Filesystem();
                            $fs->remove($imagesPath . '/' . $simpleChoiceImages
                                    ->get($simpleChoice->getId()));
                        }
                    }
                } else {
                    // set new image path and save in filesystem
                    // remove old file if it was changed with new
                    if ($simpleChoiceImages->get($simpleChoice->getId()) and
                        file_exists($imagesPath . '/' . $simpleChoiceImages
                                ->get($simpleChoice->getId()))) {

                        $fs = new Filesystem();
                        $fs->remove($imagesPath . '/' . $simpleChoiceImages
                                ->get($simpleChoice->getId()));
                    }

                    // $file stores the uploaded file
                    $file = $simpleChoice->getImgSrc();
                    if ($file != null) {
                        // Generate a unique name for the file before saving it
                        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                        // Move the file to the directory where brochures are stored
                        $file->move($imagesPath, $fileName);

                        // Update the 'imgSrc' property to store the image file name
                        // instead of its contents
                        $simpleChoice->setImgSrc($fileName);
                    }
                }
            }

            // remove all values
            foreach ($values as $value) {
                $em->remove($value);
            }

            // remove old answers from db and images
            foreach ($simpleChoicesOld as $simpleChoice) {
                $deleted = true;

                foreach ($simpleChoicesNew as $item) {
                    if ($simpleChoice->getIdentifier() === $item->getIdentifier()) {
                        $deleted = false;
                    }
                }

                if ($deleted) {
                    if ($simpleChoice->getImgSrc()) {
                        if ($simpleChoice->getImgSrc()->getFileName() and
                            file_exists($imagesPath . '/' . $simpleChoice->getImgSrc()->getFileName())) {
                            $fs = new Filesystem();
                            $fs->remove($imagesPath . '/' . $simpleChoice->getImgSrc()->getFileName());
                        }
                    }
                    $em->remove($simpleChoice);
                }
            }

            // set udentifier by new answers and refresh values
            foreach ($simpleChoicesNew as $simpleChoice) {
                if ($simpleChoice->getIdentifier() === null) {
                    $simpleChoice->setIdentifier(uniqid());
                }
                if ($simpleChoice->getIsrightanswer() === true) {
                    $value = new TValue();
                    $value->setValue($simpleChoice->getIdentifier());
                    $assessmentItem->getResponseDeclaration()->getCorrectResponce()->setValues($value);
                }
            }

            $em->flush();
            return $this->redirectToRoute('taskspool');
        }

        return $this->render('AutorentoolCoreBundle:Tasks:newtask.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/newtask/multiplechoice", name="newtaskmultiplechoice")
     */
    public function multiplechoiceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(TCategories::class)->findAll(); // get the profiles = $em->getRepository(Palcommands::class)->findAll(); // get the profiles

        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $assessmentItem = new TAssessmentItem();

        $helpFunctions = new HelpFunctions();
        $assessmentItem->setUuid($helpFunctions->generateUUIDv4());

        $assessmentItem->setCreationTimestamp((string)$timestamp);
        $assessmentItem->setIdentifier('choiceMultiple');
        $assessmentItem->setTimeDependent(false);
        $assessmentItem->setAdaptive(false);

        $support = new TSupport();
        $supportTextBox = new TSupportTextbox();
        $supportMedia = new TSupportMedia();
        $supportSelection = new TSupportSelection();
        $supportTable = new TSupportTable();

        $support->setSupportType('media');
        $support->setSupportTextbox($supportTextBox);
        $support->setSupportMedia($supportMedia);
        $support->setSupportSelection($supportSelection);

        $supportRow = new TRow();
        $supportCell1 = new TCell();
        //$supportCell1->setCellIdentifier(uniqid());
        $supportRow->addCell($supportCell1);
        $supportCell2 = new TCell();
        //$supportCell2->setCellIdentifier(uniqid());
        $supportRow->addCell($supportCell2);
        $supportTable->addRow($supportRow);
        $support->setSupportTable($supportTable);
        $assessmentItem->setSupport($support);

        $user = $this->getUser();
        $assessmentItem->setFactoryName($user->getFactoryName());

        $tItemBody = new TItemBody();

        $choiceInteraction = new TChoiceInteraction();
        $choiceInteraction->setShuffle(false);

        $simpleChoices = new TSimpleChoice();
        $simpleChoices->setIdentifier(uniqid());
        $simpleChoices2 = new TSimpleChoice();
        $simpleChoices2->setIdentifier(uniqid());

        $choiceInteraction->setResponseIdentifier("RESPONSE");
        $choiceInteraction->addSimpleChoices($simpleChoices);
        $choiceInteraction->addSimpleChoices($simpleChoices2);

        $responseDeclaration = new TResponseDeclaration();
        $responseDeclaration->setIdentifier($choiceInteraction->getResponseIdentifier());
        $responseDeclaration->setCardinality("multiple");
        $responseDeclaration->setBaseType("identifier");

        $correctResponce = new TCorrectResponse();
        $responseDeclaration->setCorrectResponce($correctResponce);

        $mapping = new TMapping();
        $responseDeclaration->setMapping($mapping);

        $assessmentItem->setResponseDeclaration($responseDeclaration);
        $tItemBody->setChoiceInteraction($choiceInteraction);
        $assessmentItem->setItemBody($tItemBody);

        $form = $this->createForm(TAssessmentItemType::class, $assessmentItem, array(
            'categories' => $categories,
            'tasktype' => "multiple",

        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // add categories to assessment
            $currentCategories = $this->addCategoriesAssessment($assessmentItem);
            // add new category in db categories
            $this->addNewCategoryInDB($categories, $currentCategories, $em);

            // $file stores the uploaded file
            $file = $tItemBody->getImgSrc();
            $imagesPath = $this->getParameter('multipleChoice_img_directory');
            if ($file != null) {
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move($imagesPath, $fileName);
                // Update the 'imgSrc' property to store the image file name
                // instead of its contents
                $tItemBody->setImgSrc($fileName);
            }

            // support
            $isSupport = $this->setNewSupportAssessment($support);
            if ($isSupport) {
                $support->setUuid($helpFunctions->generateUUIDv4());
                $support->setAssessmentUuid($assessmentItem->getUuid());
                $support->setCreationTimestamp((string)$timestamp);
            } else {
                $assessmentItem->removeSupport();
            }

            $maxChoiceCount = 0;
            foreach ($choiceInteraction->getSimpleChoices() as $simpleChoice) {
                // $file stores the uploaded file
                $file = $simpleChoice->getImgSrc();

                if ($file != null) {
                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $file->move($imagesPath, $fileName);
                    // Update the 'imgSrc' property to store the image file name
                    // instead of its contents
                    $simpleChoice->setImgSrc($fileName);
                }

                if ($simpleChoice->getIdentifier() === null) {
                    $simpleChoice->setIdentifier(uniqid());
                }

                $mapEntry = new TMapEntry();
                $mapEntry->setMapKey($simpleChoice->getIdentifier());
                if ($simpleChoice->getIsrightanswer() === true) {
                    $values = new TValue();
                    $values->setValue($simpleChoice->getIdentifier());
                    $correctResponce->setValues($values);
                    $mapEntry->setMappedValue(1);
                    $maxChoiceCount += 1;
                } else {
                    $mapEntry->setMappedValue(-1);
                }

                $mapping->setMapEntry($mapEntry);

            }

            $mapping->setLowerBound($maxChoiceCount);
            $mapping->setUpperBound($maxChoiceCount);
            $mapping->setDefaultValue(0);

            $choiceInteraction->setMaxChoices($maxChoiceCount);
            $responseDeclaration->setCorrectResponce($correctResponce);
            $responseDeclaration->setMapping($mapping);

            $this->setRelated($assessmentItem, $em);

            $em->persist($assessmentItem);
            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            return $this->redirect($this->generateUrl('taskspool'));
        }

        return $this->render('AutorentoolCoreBundle:Tasks:newtask.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/taskedit/hotspot/{uuid}", name="taskedithotspot")
     */
    public function taskeditHotspotAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();
        $assessmentItem = $em->getRepository(TAssessmentItem::class)->findOneByUuid($uuid);

        if ($assessmentItem) {
            $assessmentItem->setStateOfTask(false);
            $em->persist($assessmentItem);
            $em->flush();
        } else {
            return $this->redirectToRoute('taskspool');
        }

        $categories = $em->getRepository(TCategories::class)->findAll();

        $outerObject = $assessmentItem->getItemBody()->getHotspotInteraction()->getOuterObject();
        $oldFileName = $outerObject->getData();

        $imagesPath = $this->getParameter('hotspot_img_directory');

        // create file object if image name exists in db and in file system
        if ($oldFileName and file_exists($imagesPath.'/'.$oldFileName)) {
            $uploadedFile = new UploadedFile($imagesPath.'/'.$oldFileName,
                $outerObject->getDataOrigName());
            $outerObject->setData($uploadedFile);
        } else {
            $outerObject->setData(null);
        }

        // image name in db for answers
        $areaMapEntityOldImages = [];

        // remove shapes, they are would be recreated in client from coordinates
        // save old file names in array for the removing from system by submit
        $areaMapEntryOld = $assessmentItem->getResponseDeclaration()->getAreaMapping()->getAreaMapEntry();
        foreach ($areaMapEntryOld as $item) {
            $oldFileNameAreaMapEntry = $item->getData();
            $oldFilePath = $imagesPath.'/'.$oldFileNameAreaMapEntry;

            if ($oldFileNameAreaMapEntry and file_exists($oldFilePath)) {
                array_push($areaMapEntityOldImages, $oldFileNameAreaMapEntry);
                $item->setData(null);
            }
        }

        $currentCategories = [];
        foreach ($assessmentItem->getCategoryTags() as $category) {
            array_push($currentCategories, $category->getTagName());
        }
        $assessmentItem->setCurrentCategory($currentCategories);

        $returnSelAndOldFileName = $this->prepareSupportForEdit($assessmentItem);
        $currentSelections = $returnSelAndOldFileName[0];
        $oldFileSupportMediaName = $returnSelAndOldFileName[1];

        $form = $this->createForm(TAssessmentItemType::class, $assessmentItem, array(
            'categories' => $categories,
            'selection' => $currentSelections,
            'tasktype' => "point",
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // add or remove category tags
            $currentCategories = array();
            if ($assessmentItem->getCurrentCategory() != null) {
                $currentCategories = explode(",", $assessmentItem->getCurrentCategory());

                foreach ($assessmentItem->getCategoryTags() as $categoryTag) {
                    if (in_array($categoryTag->getTagName(), $currentCategories)) {
                        $index = array_search($categoryTag->getTagName(), $currentCategories);
                        array_splice($currentCategories, $index, 1);

                    } elseif (!in_array($categoryTag->getTagName(), $currentCategories)) {
                        $em->remove($categoryTag);
                    }
                }

                if (count($currentCategories) > 0) {
                    foreach ($currentCategories as $category) {
                        $categoryTag = new TCategoryTags();
                        $categoryTag->setTagName($category);
                        $assessmentItem->setCategoryTags($categoryTag);
                    }
                    // add new category in db categories
                    $this->addNewCategoryInDB($categories, $currentCategories, $em);
                }
            } else {
                // remove all values
                foreach ($assessmentItem->getCategoryTags() as $value) {
                    $em->remove($value);
                }

                $assessmentItem->removeCategoryTags();
            }


            if ($assessmentItem->getCategoryTags() != null and $assessmentItem->getRelated() == null) {
                $this->setRelated($assessmentItem, $em);
            } elseif ($assessmentItem->getCategoryTags() == null and $assessmentItem->getRelated() != null) {
                $em->remove($assessmentItem->getRelated());
                $assessmentItem->removeRelated();
            } elseif ($assessmentItem->getCategoryTags() != null and $assessmentItem->getRelated() != null) {
                $this->setRelated($assessmentItem, $em);
            }

            //support
            $this->editSupportAssessment($assessmentItem, $em, $oldFileSupportMediaName);

            // leave old image (it is already in db and in file system)
            $outerObject = $assessmentItem->getItemBody()->getHotspotInteraction()->getOuterObject();
            $areaMapEntries = $assessmentItem->getResponseDeclaration()->getAreaMapping()->getAreaMapEntry();

            if ( $outerObject->getDataOrigName() and
                ($outerObject->getData() === NULL) ) {

                // set path of old image in object
                $outerObject->setData($oldFileName);
            } elseif ($outerObject->getDataOrigName() == NULL) {
                // remove new set image from object (it is not in filesystem
                if ($outerObject->getData() != null) {
                    $outerObject->setData(null);
                } else {
                    // remove old image from filesystem
                    if (file_exists($imagesPath.'/'.$oldFileName)){
                        $fs = new Filesystem();
                        $fs->remove($imagesPath.'/'.$oldFileName);
                    }
                }
            } else {
                // set new image path and save in filesystem
                // remove old file if it was changed with new
                if ($oldFileName and file_exists($imagesPath.'/'.$oldFileName)) {
                    $fs = new Filesystem();
                    $fs->remove($imagesPath.'/'.$oldFileName);
                }

                // $file stores the uploaded file
                $file = $outerObject->getData();
                if ($file != null) {
                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $file->move($imagesPath, $fileName);

                    // Update the 'imgSrc' property to store the image file name
                    // instead of its contents
                    $outerObject->setData($fileName);
                }
            }

            $values = $assessmentItem->getResponseDeclaration()->getCorrectResponce()->getValues();
            foreach ($values as $value) {
                $em->remove($value);
            }

            $innerObjects = $assessmentItem->getItemBody()->getHotspotInteraction()->getInnerObject();
            foreach ($innerObjects as $innerObject) {
                $em->remove($innerObject);
            }

            //delete old shapes images
            foreach ($areaMapEntityOldImages as $item) {
                if (file_exists($imagesPath.'/'.$item)){
                    $fs = new Filesystem();
                    $fs->remove($imagesPath.'/'.$item);
                }
            }

            $correctResponse = $assessmentItem->getResponseDeclaration()->getCorrectResponce();
            $hotspotInteraction = $assessmentItem->getItemBody()->getHotspotInteraction();

            foreach ($areaMapEntries as $areaMapEntry) {

                $value = new TValue();

                $coords = $areaMapEntry->getCoords();
                // circle has following representation: "x1,y1,radius"
                // oval has following representation: "x1,y1,radiusX,radiusY"
                if ($areaMapEntry->getShape() === 'circle') {
                    $coords = substr($coords, 0, strrpos( $coords, ','));
                    $value->setValue(str_replace(","," ", $coords));
                }

                if ($areaMapEntry->getShape() === 'ellipse') {
                    $value->setValue(str_replace(","," ", $coords));
                }

                // poly and rect have following representation: "x1,y1;x2,y2,..."
                if ($areaMapEntry->getShape() === 'rect') {
                    $value->setValue(str_replace(","," ", $coords));
                }

                if ($areaMapEntry->getShape() === 'poly') {
                    $value->setValue(str_replace(","," ", $coords));
                }
                $correctResponse->setValues($value);

                // set mapped_value to default = 1
                $areaMapEntry->setMappedValue(1);


                if ($areaMapEntry->getData()) {
                    // Generate a unique name for the file before saving it
                    $fileNameWithoutExtention = md5(uniqid());
                    $helpFunctions = new HelpFunctions();
                    $fileName = $helpFunctions->base64ToPNG($areaMapEntry->getData(),
                        $fileNameWithoutExtention, $imagesPath);
                    // set inner object
                    $objectInner = new TInnerObject();
                    $objectInner->setType('image/png');
                    $objectInner->setData($fileName);

                    $areaMapEntry->setData($fileName);

                    // set width and height of image of inner object
                    $size = getimagesize($imagesPath . '/' . $fileName);
                    $objectInner->setWidth($size[0]);
                    $objectInner->setHeight($size[1]);
                    $hotspotInteraction->setInnerObject($objectInner);
                } else {
                    $objectInner = new TInnerObject();
                    $hotspotInteraction->setInnerObject($objectInner);
                }
            }

            $hotspotInteraction->setMaxChoices(count($assessmentItem->getResponseDeclaration()->getAreaMapping()->getAreaMapEntry()));
            $hotspotInteraction->setResponseIdentifier('RESPONSE');

            $assessmentItem->getResponseDeclaration()->setCorrectResponce($correctResponse);

            $em->flush();

            return $this->redirectToRoute('taskspool');
        }

        return $this->render('AutorentoolCoreBundle:Tasks:newtask.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    /**
     * @Route("/newtask/hotspot", name="newtaskhotspot")
     */
    public function hotspotAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(TCategories::class)->findAll(); // get the profiles = $em->getRepository(Palcommands::class)->findAll(); // get the profiles

        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $assessmentItem = new TAssessmentItem();

        $helpFunctions = new HelpFunctions();
        $assessmentItem->setUuid($helpFunctions->generateUUIDv4());

        $assessmentItem->setCreationTimestamp((string)$timestamp);
        $assessmentItem->setIdentifier('positionObjects');
        $assessmentItem->setTimeDependent(false);
        $assessmentItem->setAdaptive(false);

        $support = new TSupport();
        $supportTextBox = new TSupportTextbox();
        $supportMedia = new TSupportMedia();
        $supportSelection = new TSupportSelection();
        $supportTable = new TSupportTable();

        $support->setSupportType('media');
        $support->setSupportTextbox($supportTextBox);
        $support->setSupportMedia($supportMedia);
        $support->setSupportSelection($supportSelection);

        $supportRow = new TRow();
        $supportCell1 = new TCell();
        $supportCell1->setCellIdentifier(uniqid());
        $supportRow->addCell($supportCell1);
        $supportCell2 = new TCell();
        $supportCell2->setCellIdentifier(uniqid());
        $supportRow->addCell($supportCell2);
        $supportTable->addRow($supportRow);
        $support->setSupportTable($supportTable);
        $assessmentItem->setSupport($support);

        $user = $this->getUser();
        $assessmentItem->setFactoryName($user->getFactoryName());

        $tItemBody = new TItemBody();

        $hotspotInteraction = new THotspotInteraction();

        $objectOuter = new TOuterObject();
        $hotspotInteraction->setOuterObject($objectOuter);

        // default by creating a hotspottask, late would be changed according number of answers
        $hotspotInteraction->setMaxChoices(1);

        $responseDeclaration = new TResponseDeclaration();
        $responseDeclaration->setIdentifier('RESPONSE');
        $responseDeclaration->setCardinality("multiple");
        $responseDeclaration->setBaseType("point");

        $areaMapping = new TAreaMapping();
        $areaMapping->setDefaultValue(0);  // default
        $areaMapEntry = new TAreaMapEntry();
        $areaMapEntry->setMappedValue(1);  // default
        $areaMapping->addAreaMapEntry($areaMapEntry);

        $responseDeclaration->setAreaMapping($areaMapping);

        $assessmentItem->setResponseDeclaration($responseDeclaration);
        $tItemBody->setHotspotInteraction($hotspotInteraction);
        $assessmentItem->setItemBody($tItemBody);

        $form = $this->createForm(TAssessmentItemType::class, $assessmentItem, array(
            'categories' => $categories,
            'tasktype' => "point",

        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // add categories to assessment
            $currentCategories = $this->addCategoriesAssessment($assessmentItem);
            // add new category in db categories
            $this->addNewCategoryInDB($categories, $currentCategories, $em);

            $imagesPath = $this->getParameter('hotspot_img_directory');
            $file = $objectOuter->getData();
            if ($file != null) {
                $objectOuter->setType('image/png');
                $objectOuter->setDataOrigName($file->getClientOriginalName());

                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move($imagesPath, $fileName);
                // Update the 'imgSrc' property to store the image file name
                // instead of its contents
                $objectOuter->setData($fileName);

                // set width and height of image
                $size = getimagesize($imagesPath . '/' . $fileName);
                $objectOuter->setWidth($size[0]);
                $objectOuter->setHeight($size[1]);
            }

            // support
            $isSupport = $this->setNewSupportAssessment($support);
            if ($isSupport) {
                $support->setUuid($helpFunctions->generateUUIDv4());
                $support->setAssessmentUuid($assessmentItem->getUuid());
                $support->setCreationTimestamp((string)$timestamp);
            } else {
                $assessmentItem->removeSupport();
            }

            $correctResponse = new TCorrectResponse();
            foreach ($areaMapping->getAreaMapEntry() as $areaMapEntry) {
                $value = new TValue();

                $coords = $areaMapEntry->getCoords();
                // circle has following representation: "x1,y1,radius"
                // oval has following representation: "x1,y1,radiusX,radiusY"
                if ($areaMapEntry->getShape() === 'circle') {
                    $coords = substr($coords, 0, strrpos( $coords, ','));
                    $value->setValue(str_replace(","," ", $coords));
                }

                if ($areaMapEntry->getShape() === 'ellipse') {
                    $value->setValue(str_replace(","," ", $coords));
                }

                // poly and rect have following representation: "x1,y1;x2,y2,..."
                if ($areaMapEntry->getShape() === 'rect') {
                    $value->setValue(str_replace(","," ", $coords));
                }

                if ($areaMapEntry->getShape() === 'poly') {
                    $value->setValue(str_replace(","," ", $coords));
                }
                $correctResponse->setValues($value);

                // set mapped_value to default = 1
                $areaMapEntry->setMappedValue(1);


                if ($areaMapEntry->getData()) {
                    // Generate a unique name for the file before saving it
                    $fileNameWithoutExtention = md5(uniqid());
                    $helpFunctions = new HelpFunctions();
                    $fileName = $helpFunctions->base64ToPNG($areaMapEntry->getData(),
                        $fileNameWithoutExtention, $imagesPath);
                    // set inner object
                    $objectInner = new TInnerObject();
                    $objectInner->setType('image/png');
                    $objectInner->setData($fileName);
                    $areaMapEntry->setData($fileName);
                    // set width and height of image of inner object
                    $size = getimagesize($imagesPath . '/' . $fileName);
                    $objectInner->setWidth($size[0]);
                    $objectInner->setHeight($size[1]);
                    $hotspotInteraction->setInnerObject($objectInner);
                } else {
                    $objectInner = new TInnerObject();
                    $hotspotInteraction->setInnerObject($objectInner);
                }
            }

            $hotspotInteraction->setMaxChoices(count($areaMapping->getAreaMapEntry()));
            $hotspotInteraction->setResponseIdentifier('RESPONSE');

            $responseDeclaration->setCorrectResponce($correctResponse);

            $this->setRelated($assessmentItem, $em);

            $em->persist($assessmentItem);
            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            return $this->redirect($this->generateUrl('taskspool'));
        }

        return $this->render('AutorentoolCoreBundle:Tasks:newtask.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/taskedit/table/{uuid}", name="taskedittable")
     */
    public function taskedittableAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();
        $assessmentItem = $em->getRepository(TAssessmentItem::class)->findOneByUuid($uuid);

        if ($assessmentItem) {
            $assessmentItem->setStateOfTask(false);
            $em->persist($assessmentItem);
            $em->flush();
        } else {
            return $this->redirectToRoute('taskspool');
        }

        $pals_raw = $em->getRepository(TPalcommandTypes::class)->findAll();
        $pals = array();
        foreach ($pals_raw as $palType){
            $groups = array();

            foreach ($palType->getCommandGroups() as $palGroup) {
                $comands = array();
                foreach ($palGroup->getCommands() as $pal) {
                    $comands[$pal->getCommandName()] = $pal->getDescription();
                }
                $groups[$palGroup->getGroupName()] = $comands;
            }

            $pals[$palType->getTypeName()] = $groups;
        }

        $categories = $em->getRepository(TCategories::class)->findAll(); // get the profiles = $em->getRepository(Palcommands::class)->findAll(); // get the profiles
        $table = $assessmentItem->getItemBody()->getTableInteraction()->getTable();
        $correctResponce = $assessmentItem->getResponseDeclaration()->getCorrectResponce();
        $values = $correctResponce->getValues();
        $imagesPath = $this->getParameter('table_img_directory');

        // image name in db for tittle
        $oldFileName = $assessmentItem->getItemBody()->getImgSrc();
        // create file object if image name exists in db and in file system
        if ($oldFileName and file_exists($imagesPath.'/'.$oldFileName)) {
            $uploadedFile = new UploadedFile($imagesPath.'/'.$oldFileName, "");
            $assessmentItem->getItemBody()->setImgSrc($uploadedFile);
        } else {
            $assessmentItem->getItemBody()->setImgSrc(null);
        }

        foreach ($values as $value) {
            foreach ($table->getRow() as $row) {
                foreach ($row->getCell() as $cell) {
                    if ($cell->getCellIdentifier() === $value->getCellIdentifier()) {
                        $cell->setWriteable(true);
                    }
                }
            }
        }

        $currentCategories = [];
        foreach ($assessmentItem->getCategoryTags() as $category) {
            array_push($currentCategories, $category->getTagName());
        }
        $assessmentItem->setCurrentCategory($currentCategories);

        $returnSelAndOldFileName = $this->prepareSupportForEdit($assessmentItem);
        $currentSelections = $returnSelAndOldFileName[0];
        $oldFileSupportMediaName = $returnSelAndOldFileName[1];

        $form = $this->createForm(TAssessmentItemType::class, $assessmentItem, array(
            'categories' => $categories,
            'selection' => $currentSelections,
            'tasktype' => 'table',

        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // add or remove category tags
            $currentCategories = array();
            if ($assessmentItem->getCurrentCategory() != null) {
                $currentCategories = explode(",", $assessmentItem->getCurrentCategory());

                foreach ($assessmentItem->getCategoryTags() as $categoryTag) {
                    if (in_array($categoryTag->getTagName(), $currentCategories)) {
                        $index = array_search($categoryTag->getTagName(), $currentCategories);
                        array_splice($currentCategories, $index, 1);

                    } elseif (!in_array($categoryTag->getTagName(), $currentCategories)) {
                        $em->remove($categoryTag);
                    }
                }

                if (count($currentCategories) > 0) {
                    foreach ($currentCategories as $category) {
                        $categoryTag = new TCategoryTags();
                        $categoryTag->setTagName($category);
                        $assessmentItem->setCategoryTags($categoryTag);
                    }
                    // add new category in db categories
                    $this->addNewCategoryInDB($categories, $currentCategories, $em);
                }
            } else {
                // remove all values
                foreach ($assessmentItem->getCategoryTags() as $value) {
                    $em->remove($value);
                }
                $assessmentItem->removeCategoryTags();
            }

            if ($assessmentItem->getCategoryTags() != null and $assessmentItem->getRelated() == null) {
                $this->setRelated($assessmentItem, $em);
            } elseif ($assessmentItem->getCategoryTags() == null and $assessmentItem->getRelated() != null) {
                $em->remove($assessmentItem->getRelated());
                $assessmentItem->removeRelated();
            } elseif ($assessmentItem->getCategoryTags() != null and $assessmentItem->getRelated() != null) {
                $this->setRelated($assessmentItem, $em);
            }

            //support
            $this->editSupportAssessment($assessmentItem, $em, $oldFileSupportMediaName);

            // leave old image of tittle (it is already in db and in file system)
            if ( ($assessmentItem->getItemBody()->getRemoveImgSrcState() === false) and
                ($assessmentItem->getItemBody()->getImgSrc() === NULL) ) {
                // set path of old image in object
                $assessmentItem->getItemBody()->setImgSrc($oldFileName);
            } elseif ($assessmentItem->getItemBody()->getRemoveImgSrcState() === true) {
                // remove new set image from object (it is not in filesystem
                if ($assessmentItem->getItemBody()->getImgSrc() != null) {
                    $assessmentItem->getItemBody()->setImgSrc(null);
                } else {
                    // remove old image from filesystem
                    if (file_exists($imagesPath.'/'.$oldFileName)){
                        $fs = new Filesystem();
                        $fs->remove($imagesPath.'/'.$oldFileName);
                    }
                }
            } else {
                // set new image path and save in filesystem
                // remove old file if it was changed with new
                if ($oldFileName and file_exists($imagesPath.'/'.$oldFileName)) {
                    $fs = new Filesystem();
                    $fs->remove($imagesPath.'/'.$oldFileName);
                }

                // $file stores the uploaded file
                $file = $assessmentItem->getItemBody()->getImgSrc();
                if ($file != null) {
                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $file->move($imagesPath, $fileName);

                    // Update the 'imgSrc' property to store the image file name
                    // instead of its contents
                    $assessmentItem->getItemBody()->setImgSrc($fileName);
                }
            }

            $table = $assessmentItem->getItemBody()->getTableInteraction()->getTable();
            $correctResponce = $assessmentItem->getResponseDeclaration()->getCorrectResponce();

            $values = $correctResponce->getValues();
            foreach ($values as $value) {
                $em->remove($value);
            }

            foreach ($table->getRow() as $itemRow) {
                foreach ($itemRow->getCell() as $itemCell) {
                    if ($itemCell->getCellIdentifier() === NULL) {
                        $itemCell->setCellIdentifier(uniqid());
                    }

                    if ($itemCell->getWriteable()) {
                        $value = new TValue();
                        $value->setCellIdentifier($itemCell->getCellIdentifier());
                        $value->setValue($itemCell->getValue());

                        $correctResponce->setValues($value);
                    }
                }
            }

            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            return $this->redirect($this->generateUrl('taskspool'));
        }

        return $this->render('AutorentoolCoreBundle:Tasks:newtask.html.twig', array(
            'form' => $form->createView(),
            'pals' => $pals
        ));
    }

    /**
     * @Route("/newtask/table", name="newtasktable")
     */
    public function tableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(TCategories::class)->findAll();
        $pals_raw = $em->getRepository(TPalcommandTypes::class)->findAll();

        $pals = array();
        foreach ($pals_raw as $palType){
            $groups = array();

            foreach ($palType->getCommandGroups() as $palGroup) {
                $comands = array();
                foreach ($palGroup->getCommands() as $pal) {
                    $comands[$pal->getCommandName()] = $pal->getDescription();
                }
                $groups[$palGroup->getGroupName()] = $comands;
            }

            $pals[$palType->getTypeName()] = $groups;
        }

        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $assessmentItem = new TAssessmentItem();

        $helpFunctions = new HelpFunctions();
        $assessmentItem->setUuid($helpFunctions->generateUUIDv4());

        $assessmentItem->setCreationTimestamp((string)$timestamp);
        $assessmentItem->setIdentifier('table');
        $assessmentItem->setTimeDependent(false);
        $assessmentItem->setAdaptive(false);

        $support = new TSupport();
        $supportTextBox = new TSupportTextbox();
        $supportMedia = new TSupportMedia();
        $supportSelection = new TSupportSelection();
        $supportTable = new TSupportTable();

        $support->setSupportType('media');
        $support->setSupportTextbox($supportTextBox);
        $support->setSupportMedia($supportMedia);
        $support->setSupportSelection($supportSelection);

        $supportRow = new TRow();
        $supportCell1 = new TCell();
        //$supportCell1->setCellIdentifier(uniqid());
        $supportRow->addCell($supportCell1);
        $supportCell2 = new TCell();
        //$supportCell2->setCellIdentifier(uniqid());
        $supportRow->addCell($supportCell2);
        $supportTable->addRow($supportRow);
        $support->setSupportTable($supportTable);
        $assessmentItem->setSupport($support);

        $user = $this->getUser();
        $assessmentItem->setFactoryName($user->getFactoryName());

        $tItemBody = new TItemBody();
        $tableInteraction = new TTableInteraction();
        $tableInteraction->setResponseIdentifier("RESPONSE");
        $table = new TTable();
        $row = new TRow();

        $cell1 = new TCell();
        $cell1->setCellIdentifier(uniqid());
        $row->addCell($cell1);
        $cell2 = new TCell();
        $cell2->setCellIdentifier(uniqid());
        $row->addCell($cell2);

        $table->addRow($row);
        $table->setResponseIdentifier($tableInteraction->getResponseIdentifier());

        $tableInteraction->setTable($table);
        $tItemBody->setTableInteraction($tableInteraction);
        $assessmentItem->setItemBody($tItemBody);

        $responseDeclaration = new TResponseDeclaration();
        $responseDeclaration->setIdentifier($tableInteraction->getResponseIdentifier());
        $responseDeclaration->setBaseType("identifier");

        $correctResponce = new TCorrectResponse();
        $responseDeclaration->setCorrectResponce($correctResponce);
        $assessmentItem->setResponseDeclaration($responseDeclaration);

        $form = $this->createForm(TAssessmentItemType::class, $assessmentItem, array(
            'categories' => $categories,
            'tasktype' => 'table',
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // add categories to assessment
            $currentCategories = $this->addCategoriesAssessment($assessmentItem);
            // add new category in db categories
            $this->addNewCategoryInDB($categories, $currentCategories, $em);

            // $file stores the uploaded file
            $file = $tItemBody->getImgSrc();
            $imagesPath = $this->getParameter('table_img_directory');
            if ($file != null) {
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move($imagesPath, $fileName);
                // Update the 'imgSrc' property to store the image file name
                // instead of its contents
                $tItemBody->setImgSrc($fileName);
            }

            // support
            $isSupport = $this->setNewSupportAssessment($support);
            if ($isSupport) {
                $support->setUuid($helpFunctions->generateUUIDv4());
                $support->setAssessmentUuid($assessmentItem->getUuid());
                $support->setCreationTimestamp((string)$timestamp);
            } else {
                $assessmentItem->removeSupport();
            }

            foreach ($table->getRow() as $itemRow) {
                foreach ($itemRow->getCell() as $itemCell) {
                    if ($itemCell->getCellIdentifier() === NULL) {
                        $itemCell->setCellIdentifier(uniqid());
                    }

                    if ($itemCell->getWriteable()) {
                        $value = new TValue();
                        $value->setCellIdentifier($itemCell->getCellIdentifier());
                        $value->setValue($itemCell->getValue());

                        $correctResponce->setValues($value);
                    }
                }
            }

            $this->setRelated($assessmentItem, $em);

            $em->persist($assessmentItem);
            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            return $this->redirect($this->generateUrl('taskspool'));
        }


        return $this->render('AutorentoolCoreBundle:Tasks:newtask.html.twig', array(
            'form' => $form->createView(),
            'pals' => $pals
        ));
    }

    /**
     * @Route("/taskedit/dragtable/{uuid}", name="taskeditdragtable")
     */
    public function taskeditdragtableAction(Request $request, $uuid)
    {
        $em = $this->getDoctrine()->getManager();
        $assessmentItem = $em->getRepository(TAssessmentItem::class)->findOneByUuid($uuid);

        if ($assessmentItem) {
            $assessmentItem->setStateOfTask(false);
            $em->persist($assessmentItem);
            $em->flush();
        } else {
            return $this->redirectToRoute('taskspool');
        }

        $categories = $em->getRepository(TCategories::class)->findAll(); // get the profiles = $em->getRepository(Palcommands::class)->findAll(); // get the profiles
        $imagesPath = $this->getParameter('dragndropTable_img_directory');

        // image name in db for tittle
        $oldFileName = $assessmentItem->getItemBody()->getImgSrc();
        // create file object if image name exists in db and in file system
        if ($oldFileName and file_exists($imagesPath.'/'.$oldFileName)) {
            $uploadedFile = new UploadedFile($imagesPath.'/'.$oldFileName, "");
            $assessmentItem->getItemBody()->setImgSrc($uploadedFile);
        } else {
            $assessmentItem->getItemBody()->setImgSrc(null);
        }

        $currentCategories = [];
        foreach ($assessmentItem->getCategoryTags() as $category) {
            array_push($currentCategories, $category->getTagName());
        }
        $assessmentItem->setCurrentCategory($currentCategories);

        $returnSelAndOldFileName = $this->prepareSupportForEdit($assessmentItem);
        $currentSelections = $returnSelAndOldFileName[0];
        $oldFileSupportMediaName = $returnSelAndOldFileName[1];

        $form = $this->createForm(TAssessmentItemType::class, $assessmentItem, array(
            'categories' => $categories,
            'selection' => $currentSelections,
            'tasktype' => 'dragndropTable',

        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // add or remove category tags
            $currentCategories = array();
            if ($assessmentItem->getCurrentCategory() != null) {
                $currentCategories = explode(",", $assessmentItem->getCurrentCategory());

                foreach ($assessmentItem->getCategoryTags() as $categoryTag) {
                    if (in_array($categoryTag->getTagName(), $currentCategories)) {
                        $index = array_search($categoryTag->getTagName(), $currentCategories);
                        array_splice($currentCategories, $index, 1);

                    } elseif (!in_array($categoryTag->getTagName(), $currentCategories)) {
                        $em->remove($categoryTag);
                    }
                }

                if (count($currentCategories) > 0) {
                    foreach ($currentCategories as $category) {
                        $categoryTag = new TCategoryTags();
                        $categoryTag->setTagName($category);
                        $assessmentItem->setCategoryTags($categoryTag);
                    }
                    // add new category in db categories
                    $this->addNewCategoryInDB($categories, $currentCategories, $em);
                }
            } else {
                // remove all values
                foreach ($assessmentItem->getCategoryTags() as $value) {
                    $em->remove($value);
                }

                $assessmentItem->removeCategoryTags();
            }


            if ($assessmentItem->getCategoryTags() != null and $assessmentItem->getRelated() == null) {
                //var_dump("a");die;
                $this->setRelated($assessmentItem, $em);
            } elseif ($assessmentItem->getCategoryTags() == null and $assessmentItem->getRelated() != null) {
                //var_dump("b");die;
                $em->remove($assessmentItem->getRelated());
                $assessmentItem->removeRelated();
            } elseif ($assessmentItem->getCategoryTags() != null and $assessmentItem->getRelated() != null) {
                $this->setRelated($assessmentItem, $em);
            }

            // add new category in db categories
            $this->addNewCategoryInDB($categories, $currentCategories, $em);

            //support
            $this->editSupportAssessment($assessmentItem, $em, $oldFileSupportMediaName);

            // leave old image of tittle (it is already in db and in file system)
            if ( ($assessmentItem->getItemBody()->getRemoveImgSrcState() === false) and
                ($assessmentItem->getItemBody()->getImgSrc() === NULL) ) {
                // set path of old image in object
                $assessmentItem->getItemBody()->setImgSrc($oldFileName);
            } elseif ($assessmentItem->getItemBody()->getRemoveImgSrcState() === true) {
                // remove new set image from object (it is not in filesystem
                if ($assessmentItem->getItemBody()->getImgSrc() != null) {
                    $assessmentItem->getItemBody()->setImgSrc(null);
                } else {
                    // remove old image from filesystem
                    if (file_exists($imagesPath.'/'.$oldFileName)){
                        $fs = new Filesystem();
                        $fs->remove($imagesPath.'/'.$oldFileName);
                    }
                }
            } else {
                // set new image path and save in filesystem
                // remove old file if it was changed with new
                if ($oldFileName and file_exists($imagesPath.'/'.$oldFileName)) {
                    $fs = new Filesystem();
                    $fs->remove($imagesPath.'/'.$oldFileName);
                }

                // $file stores the uploaded file
                $file = $assessmentItem->getItemBody()->getImgSrc();
                if ($file != null) {
                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$file->guessExtension();

                    // Move the file to the directory where brochures are stored
                    $file->move($imagesPath, $fileName);

                    // Update the 'imgSrc' property to store the image file name
                    // instead of its contents
                    $assessmentItem->getItemBody()->setImgSrc($fileName);
                }
            }

            $table = $assessmentItem->getItemBody()->getDragInteraction()->getDragTable();
            $dragInteraction = $assessmentItem->getItemBody()->getDragInteraction();

            $dragItems = $dragInteraction->getDragItem();

            foreach ($dragItems as $dragItem) {
                $em->remove($dragItem);
            }

            if ($dragInteraction->getMode() === 'row') {

                foreach ($table->getRow() as $itemRow) {
                    $ident = uniqid();

                    foreach ($itemRow->getCell() as $itemCell) {

                        $itemCell->setRowIdentifier($ident);
                        $itemCell->setColumnIdentifier(null);

                        if ($itemCell->getHead() === false and $itemCell->getValue() != null) {
                            $dragItem = new TDragItem();
                            $dragItem->setIdentifier($ident);
                            $dragItem->setValue($itemCell->getValue());
                            $dragInteraction->addDragItem($dragItem);
                        }
                    }
                }
            }

            if ($dragInteraction->getMode() === 'column') {
                $out = array();

                // transform row to column
                for ($i = 0; $i < count($table->getRow()); $i++){
                    for ($j = 0; $j < count($table->getRow()[$i]->getCell()); $j++){
                        $out[$j][$i]=$table->getRow()[$i]->getCell()[$j];
                    }
                }

                $identArray = array();

                for ($i = 0; $i < count($out); $i++){
                    $identArray[$i] = uniqid();
                }


                for ($i = 0; $i < count($out); $i++){

                    for ($k = 0; $k < count($out[$i]); $k++){

                        $out[$i][$k]->setColumnIdentifier($identArray[$i]);

                        if ($out[$i][$k]->getHead() === false and $out[$i][$k]->getValue() != null) {
                            $dragItem = new TDragItem();
                            $dragItem->setIdentifier($identArray[$i]);
                            $dragItem->setValue($out[$i][$k]->getValue());
                            $dragInteraction->addDragItem($dragItem);
                        }
                    }
                }

                foreach ($table->getRow() as $itemRow) {
                    foreach ($itemRow->getCell() as $itemCell) {
                        $itemCell->setRowIdentifier(null);
                    }
                }
            }

            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            return $this->redirect($this->generateUrl('taskspool'));
        }


        return $this->render('AutorentoolCoreBundle:Tasks:newtask.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/newtask/dragtable", name="newtaskdragtable")
     */
    public function newtaskdragtableAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository(TCategories::class)->findAll(); // get the profiles = $em->getRepository(Palcommands::class)->findAll(); // get the profiles

        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $assessmentItem = new TAssessmentItem();

        $helpFunctions = new HelpFunctions();
        $assessmentItem->setUuid($helpFunctions->generateUUIDv4());

        $user = $this->getUser();
        $assessmentItem->setFactoryName($user->getFactoryName());

        $assessmentItem->setCreationTimestamp((string)$timestamp);
        $assessmentItem->setIdentifier('dragndropTable');
        $assessmentItem->setTimeDependent(false);
        $assessmentItem->setAdaptive(false);

        $support = new TSupport();
        $supportTextBox = new TSupportTextbox();
        $supportMedia = new TSupportMedia();
        $supportSelection = new TSupportSelection();
        $supportTable = new TSupportTable();

        $support->setSupportType('media');
        $support->setSupportTextbox($supportTextBox);
        $support->setSupportMedia($supportMedia);
        $support->setSupportSelection($supportSelection);

        $supportRow = new TRow();
        $supportCell1 = new TCell();
        //$supportCell1->setCellIdentifier(uniqid());
        $supportRow->addCell($supportCell1);
        $supportCell2 = new TCell();
        //$supportCell2->setCellIdentifier(uniqid());
        $supportRow->addCell($supportCell2);
        $supportTable->addRow($supportRow);
        $support->setSupportTable($supportTable);
        $assessmentItem->setSupport($support);

        $user = $this->getUser();
        $assessmentItem->setFactoryName($user->getFactoryName());

        $table = new TTable();
        $row = new TRow();
        $cell11 = new TCell();
        $row->addCell($cell11);
        $cell22 = new TCell();
        $row->addCell($cell22);

        $table->addRow($row);

        $row2 = new TRow();
        $cell21 = new TCell();
        $row2->addCell($cell21);
        $cell22 = new TCell();
        $row2->addCell($cell22);

        $table->addRow($row2);

        $dragInteraction = new TDragInteraction();
        $dragInteraction->setDragTable($table);
        $tItemBody = new TItemBody();
        $tItemBody->setDragInteraction($dragInteraction);
        $assessmentItem->setItemBody($tItemBody);

        $form = $this->createForm(TAssessmentItemType::class, $assessmentItem, array(
            'categories' => $categories,
            'tasktype' => 'dragndropTable',

        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // add categories to assessment
            $currentCategories = $this->addCategoriesAssessment($assessmentItem);
            // add new category in db categories
            $this->addNewCategoryInDB($categories, $currentCategories, $em);

            // $file stores the uploaded file
            $file = $tItemBody->getImgSrc();
            $imagesPath = $this->getParameter('dragndropTable_img_directory');
            if ($file != null) {
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                // Move the file to the directory where brochures are stored
                $file->move($imagesPath, $fileName);
                // Update the 'imgSrc' property to store the image file name
                // instead of its contents
                $tItemBody->setImgSrc($fileName);
            }

            // support
            $isSupport = $this->setNewSupportAssessment($support);
            if ($isSupport) {
                $support->setUuid($helpFunctions->generateUUIDv4());
                $support->setAssessmentUuid($assessmentItem->getUuid());
                $support->setCreationTimestamp((string)$timestamp);
            } else {
                $assessmentItem->removeSupport();
            }

            if ($dragInteraction->getMode() === 'row') {
                foreach ($table->getRow() as $itemRow) {
                    $ident = uniqid();

                    foreach ($itemRow->getCell() as $itemCell) {
                        $itemCell->setRowIdentifier($ident);

                        if ($itemCell->getHead() === false and $itemCell->getValue() != null) {
                            $dragItem = new TDragItem();
                            $dragItem->setIdentifier($ident);
                            $dragItem->setValue($itemCell->getValue());
                            $dragInteraction->addDragItem($dragItem);
                        }
                    }
                }
            }

            if ($dragInteraction->getMode() === 'column') {
                $out = array();

                // transform row to column
                for ($i = 0; $i < count($table->getRow()); $i++){
                    for ($j = 0; $j < count($table->getRow()[$i]->getCell()); $j++){
                        $out[$j][$i]=$table->getRow()[$i]->getCell()[$j];
                    }
                }

                $identArray = array();

                for ($i = 0; $i < count($out); $i++){
                    $identArray[$i] = uniqid();
                }

                for ($i = 0; $i < count($out); $i++){

                    for ($k = 0; $k < count($out[$i]); $k++){

                        $out[$i][$k]->setColumnIdentifier($identArray[$i]);

                        if ($out[$i][$k]->getHead() === false and $out[$i][$k]->getValue() != null) {
                            $dragItem = new TDragItem();
                            $dragItem->setIdentifier($identArray[$i]);
                            $dragItem->setValue($out[$i][$k]->getValue());
                            $dragInteraction->addDragItem($dragItem);
                        }
                    }
                }
            }

            $this->setRelated($assessmentItem, $em);

            $em->persist($assessmentItem);
            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            return $this->redirect($this->generateUrl('taskspool'));
        }

        return $this->render('AutorentoolCoreBundle:Tasks:newtask.html.twig', array(
            'form' => $form->createView(),
        ));
    }



    /**
     * @Route("/download/{uuid}", name="download")
     */
    public function downloadAction(Request $request, $uuid)
    {
        $helpFunctions = new HelpFunctions();

        // create path to package in system
        $tasksPackagePath = $this->getParameter('packages_directory').'/'.$uuid.'/package';
        $zipFileFullPath = $this->getParameter('packages_directory').'/'.$uuid.'/'.$this->getParameter('zip_file_name');

        if(file_exists($zipFileFullPath)) {
            return $this->file($zipFileFullPath);
        } elseif (file_exists($tasksPackagePath)) {
            // create zip package
            $helpFunctions->zipFolderContent($tasksPackagePath, $zipFileFullPath);

            if(file_exists($zipFileFullPath)) {
                return $this->file($zipFileFullPath);
            }
        }

        return $this->redirectToRoute('taskspackages');
    }

    /**
     * @Route("/newpackage", name="newpackage")
     */
    public function newpackageAction(Request $request)
    {
        /*$helpFunctions = new HelpFunctions();
        $packageDirectoryPath = $this->getParameter('packages_directory') . '/' . '1efea7ff-0a8e-48f1-97b2-58f18c9a90fb/package';
        $xmlExtendedFullPath = $packageDirectoryPath . '/assessments_standard.xml';

        // validate created 'assessments_standard.xml'
        $valid = $helpFunctions->validate($xmlExtendedFullPath, $packageDirectoryPath.'/dtd_standard.dtd');
        if ($valid === true) {
            var_dump($valid);die;
        } else {
            var_dump($valid);die;
        }*/


        $user = $this->getUser();

        $assessmentItem = $this->getDoctrine()
            ->getRepository('AutorentoolCoreBundle:TAssessmentItem')
            ->findBy(
                array('factoryName' => $user->getFactoryName(), 'stateOfTask' => true),
                array('tittle' => 'ASC')
            );

        return $this->render('AutorentoolCoreBundle:NewPackage:newpackage.html.twig', array(
            'assessemntItems' => $assessmentItem
        ));
    }

    /**
     * @Route("/newpackage/create", name="createnewpackage")
     */
    public function createnewpackageAction(Request $request)
    {
        $packageinfo = $request->request->get('packageinfo');
        $valid = false;

        if ($packageinfo) {
            $generateXML = new GenerateXML();
            $helpFunctions = new HelpFunctions();

            $assesmentStandartArray = array();
            $assesmentExtendedArray = array();
            $assesmentRelatedArray = array();
            $assesmentSupportArray = array();

            // get entity manager
            $em = $this->getDoctrine()->getManager();
            // decode json string from ajax request
            $packageinfo = json_decode($packageinfo);
            $tasksuuids = $packageinfo[1];

            $date = new DateTime();
            $timestamp = $date->getTimestamp();

            // generate uniq folder path for package
            $uniqPackageFolderName = $helpFunctions->generateUUIDv4();
            $packageRootPath = $this->getParameter('packages_directory') . '/' . $uniqPackageFolderName;
            $packageDirectoryPath = $packageRootPath.'/package';

            // separate tasks uuids into standart and extendet
            foreach ($tasksuuids as $key => $value) {
                $assessmentItem = $em->getRepository(TAssessmentItem::class)->findOneByUuid($value);

                // standart uuid tasks
                if ($assessmentItem->getIdentifier() === 'choice' or
                    $assessmentItem->getIdentifier() === 'choiceMultiple' or
                    $assessmentItem->getIdentifier() === 'positionObjects') {

                    $assesmentStandartArray[] = array(
                        "identifier" => $assessmentItem->getIdentifier(),
                        "assessmentItem" => $assessmentItem
                    );
                }
                // extendet uuid tasks
                if ($assessmentItem->getIdentifier() === 'table' or
                    $assessmentItem->getIdentifier() === 'dragndropTable') {

                    $assesmentExtendedArray[] = array(
                        "identifier" => $assessmentItem->getIdentifier(),
                        "assessmentItem" => $assessmentItem
                    );
                }

                if ($assessmentItem->getRelated() != NULL) {
                    array_push($assesmentRelatedArray, $assessmentItem->getRelated());
                }

                if ($assessmentItem->getSupport() != NULL) {
                    array_push($assesmentSupportArray, $assessmentItem->getSupport());
                }
            }

            //create validate and save xml standard file if standard tasks exist
            if ($assesmentStandartArray) {
                // create xml standart file
                $xmlStandart = $generateXML->GenerateAssessmentStandartXML($assesmentStandartArray);
                $xmlStandardFullPath = $packageDirectoryPath . '/assessments_standard.xml';

                // save xml standart file in uuid folder
                $helpFunctions->saveXmlFile($xmlStandardFullPath, $xmlStandart);

                // copy dtd_standart in package folder
                $dtdStandartPathSource = $this->getParameter('dtd_directory') . '/dtd_standard.dtd';
                $dtdStandartPathDest = $packageDirectoryPath;
                $helpFunctions->copyFile($dtdStandartPathSource, $dtdStandartPathDest);

                // validate created 'assessments_standard.xml'
                $valid = $helpFunctions->validate($xmlStandardFullPath, $packageDirectoryPath.'/dtd_standard.dtd');

                if ($valid !== true) {
                    $msg = 'Fehler bei der Validierung der "assessments_standard.xml"-Datei. Paket kann nicht erstellt werden.';

                    foreach ($valid as $item) {
                        $msg .= ' ' . $item;
                    }

                    if (file_exists($packageRootPath)) {
                        $helpFunctions->deleteDir($packageRootPath);
                    }

                    $responce = array('status' => 'error',
                                      'message'=> $msg);
                    return new JsonResponse($responce);
                }

                // copy media files in package folder
                foreach ($assesmentStandartArray as $item) {
                    $assesmentItem = $item["assessmentItem"];

                    // current package uniq path
                    $imagePackagePathDest = $packageDirectoryPath.'/media/assessments/'.$assesmentItem->getUuid();

                    // itemBody mediafile
                    if ($assesmentItem->getItemBody()->getImgSrc() != NULL) {
                        $imageItemBodyFileSource = $this->getParameter('img_directory') . '/' . $item["identifier"] . '/' .
                            $assesmentItem->getItemBody()->getImgSrc();
                        $helpFunctions->copyFile($imageItemBodyFileSource, $imagePackagePathDest);
                    }

                    $mediaPathSource = null;
                    if ($item['identifier'] === 'choice' or $item['identifier'] === 'choiceMultiple') {
                        if ($item['identifier'] === 'choice') {
                            $mediaPathSource = $this->getParameter('singleChoice_img_directory');
                            $objects = $assesmentItem->getItemBody()->getChoiceInteraction()->getSimpleChoices();
                        } else {
                            $mediaPathSource = $this->getParameter('multipleChoice_img_directory');
                            $objects = $assesmentItem->getItemBody()->getChoiceInteraction()->getSimpleChoices();
                        }
                        // copy media files for each answer
                        foreach ($objects as $object) {
                            if ($object->getImgSrc() != NULL) {
                                $mediaFilePathSource = $mediaPathSource . '/' . $object->getImgSrc();
                                $helpFunctions->copyFile($mediaFilePathSource, $imagePackagePathDest);
                            }
                        }
                    } elseif ($item['identifier'] === 'positionObjects') {
                        $mediaPathSource = $this->getParameter('hotspot_img_directory');
                        // main mediafile of hotspot (outerObject)
                        if ($assesmentItem->getItemBody()->getHotspotInteraction()->getOuterObject()->getData() != NULL) {
                            $imageOuterObjectFileSource = $this->getParameter('img_directory') . '/' . $item["identifier"] . '/' .
                                $assesmentItem->getItemBody()->getHotspotInteraction()->getOuterObject()->getData();
                            $helpFunctions->copyFile($imageOuterObjectFileSource, $imagePackagePathDest);
                        }

                        $innerObjects = $assesmentItem->getItemBody()->getHotspotInteraction()->getInnerObject();

                        // copy media files for each answer
                        foreach ($innerObjects as $object) {
                            if ($object->getData() != NULL) {
                                $mediaFilePathSource = $mediaPathSource . '/' . $object->getData();
                                $helpFunctions->copyFile($mediaFilePathSource, $imagePackagePathDest);
                            }
                        }
                    }
                }
            }

            //create validate and save xml extendet files if extended tasks exist
            if ($assesmentExtendedArray) {
                // create xml standart file
                $xmlExtended = $generateXML->GenerateAssessmentExtendedXML($assesmentExtendedArray);
                $xmlExtendedFullPath = $packageDirectoryPath . '/assessments_extended.xml';

                // save xml standart file in uuid folder
                $helpFunctions->saveXmlFile($xmlExtendedFullPath, $xmlExtended);

                // copy dtd_standart in package folder
                $dtdExtendedPathSource = $this->getParameter('dtd_directory') . '/dtd_extended.dtd';
                $dtdExtendedPathDest = $packageDirectoryPath;
                $helpFunctions->copyFile($dtdExtendedPathSource, $dtdExtendedPathDest);

                // validate created 'assessments_standard.xml'
                $valid = $helpFunctions->validate($xmlExtendedFullPath,$packageDirectoryPath.'/dtd_extended.dtd');

                if ($valid !== true) {

                    $msg = 'Fehler bei der Validierung der "assessments_extended.xml"-Datei. Paket kann nicht erstellt werden.';

                    foreach ($valid as $item) {
                        $msg .= ' ' . $item;
                    }

                    if (file_exists($packageRootPath)) {
                        $helpFunctions->deleteDir($packageRootPath);
                    }
                    $responce = array('status' => 'error',
                                      'message'=> $msg);
                    return new JsonResponse($responce);
                }

                // copy media files in package folder
                foreach ($assesmentExtendedArray as $item) {
                    $assesmentItem = $item["assessmentItem"];

                    // current package uniq path
                    $imagePackagePathDest = $packageDirectoryPath.'/media/assessments/'.$assesmentItem->getUuid();

                    // itemBody mediafile
                    if ($assesmentItem->getItemBody()->getImgSrc() != NULL) {
                        $imageItemBodyFileSource = $this->getParameter('img_directory') . '/' . $item["identifier"] . '/' .
                            $assesmentItem->getItemBody()->getImgSrc();
                        $helpFunctions->copyFile($imageItemBodyFileSource, $imagePackagePathDest);
                    }
                }
            }

            // create, validate and save xml related file if related tasks exist
            if ($assesmentRelatedArray) {
                $xmlRelated = $generateXML->GenerateAssessmentRelatedXML($assesmentRelatedArray);
                $xmlRelatedFullPath = $packageDirectoryPath . '/assessments_related.xml';
                $helpFunctions->saveXmlFile($xmlRelatedFullPath, $xmlRelated);

                // copy dtd_related in package folder
                $dtdRelatedPathSource = $this->getParameter('dtd_directory') . '/dtd_related.dtd';
                $dtdRelatedPathDest = $packageDirectoryPath;
                $helpFunctions->copyFile($dtdRelatedPathSource, $dtdRelatedPathDest);

                // validate created 'assessments_related.xml'
                $valid = $helpFunctions->validate($xmlRelatedFullPath,$packageDirectoryPath.'/dtd_related.dtd');

                if ($valid !== true) {

                    $msg = 'Fehler bei der Validierung der "assessments_related.xml"-Datei. Paket kann nicht erstellt werden.';

                    foreach ($valid as $item) {
                        $msg .= ' ' . $item;
                    }

                    if (file_exists($packageRootPath)) {
                        $helpFunctions->deleteDir($packageRootPath);
                    }
                    $responce = array('status' => 'error',
                        'message'=> $msg);
                    return new JsonResponse($responce);
                }
            }

            // create, validate and save support
            if ($assesmentSupportArray) {

                $xmlSupport = $generateXML->GenerateAssessmentSupportsXML($assesmentSupportArray);
                $xmlSupportFullPath = $packageDirectoryPath . '/assessments_support.xml';
                $helpFunctions->saveXmlFile($xmlSupportFullPath, $xmlSupport);

                // copy dtd_related in package folder
                $dtdSupportPathSource = $this->getParameter('dtd_directory') . '/dtd_support.dtd';
                $dtdSupportPathDest = $packageDirectoryPath;
                $helpFunctions->copyFile($dtdSupportPathSource, $dtdSupportPathDest);

                // validate created 'assessments_related.xml'
                $valid = $helpFunctions->validate($xmlSupportFullPath,$packageDirectoryPath.'/dtd_support.dtd');

                if ($valid !== true) {

                    $msg = 'Fehler bei der Validierung der "assessments_support.xml"-Datei. Paket kann nicht erstellt werden.';

                    foreach ($valid as $item) {
                        $msg .= ' ' . $item;
                    }

                    if (file_exists($packageRootPath)) {
                        $helpFunctions->deleteDir($packageRootPath);
                    }
                    $responce = array('status' => 'error',
                        'message'=> $msg);
                    return new JsonResponse($responce);
                }

                // copy media files in package folder
                foreach ($assesmentSupportArray as $support) {
                    // current package uniq path
                    $imagePackagePathDest = $packageDirectoryPath.'/media/support/'.$support->getUuid();

                    // itemBody mediafile
                    if ($support->getSupportMedia() != NULL) {
                        $imageSupportMediaFileSource = $this->getParameter('support_directory') . '/' .
                            $support->getSupportMedia()->getMediaSource();
                        $helpFunctions->copyFile($imageSupportMediaFileSource, $imagePackagePathDest);
                    }
                }
            }

            if ($valid) {
                // save package in db
                $newTasksPackage = new TTaskPackages();
                // set tittel of a package
                $newTasksPackage->setTittle($packageinfo[0]->tittle);
                $newTasksPackage->setDescription($packageinfo[0]->description);
                $newTasksPackage->setUuid($uniqPackageFolderName);

                $newTasksPackage->setCreationTimestamp((string)$timestamp);

                $user = $this->getUser();
                $newTasksPackage->setFactoryName($user->getFactoryName());

                $em->persist($newTasksPackage);
                $em->flush();

                $responce = array('status' => 'success',
                                  'message' => 'Paket wurde erfolgreich erstellt.');
                return new JsonResponse($responce);
            } else {
                $responce = array('status' => 'error',
                                  'message'=> 'Fehler! Paket kann nicht erstellt werden.');
                return new JsonResponse($responce);
            }
        } else {
            $responce = array('status' => 'error',
                              'message'=> 'Fehler! Die Daten vom Request kann nicht gelesen werden. Paket wurde nicht erstellt.');
            return new JsonResponse($responce);
        }
    }

    private function setRelated(TAssessmentItem $assessmentItem, $em)
    {

        $user = $this->getUser();
        $assessmentItems = $this->getDoctrine()
            ->getRepository(TAssessmentItem::class)
            ->findByFactoryName($user->getFactoryName());

        $currentCat = [];
        foreach ($assessmentItem->getCategoryTags() as $tag) {
            $currentCat[] = $tag->getTagName();
        }


        $relatedAssessmentsUuids = [];
        foreach ($assessmentItems as $item) {
            if ($item->getUuid() != $assessmentItem->getUuid()) {
                $category = $item->getCategoryTags();

                foreach ($category as $cat) {
                    if (in_array($cat->getTagName(), $currentCat)) {
                        $relatedAssessmentsUuids[] = $item->getUuid();
                    }
                }
            }
        }


        if ($relatedAssessmentsUuids) {

            if ($assessmentItem->getRelated() != null) {
                $currentRelatedItems = $assessmentItem->getRelated()->getGroupItems();

                foreach ($currentRelatedItems as $item) {
                    if (in_array($item->getAssessmentUuid(), $relatedAssessmentsUuids)) {

                        $index = array_search($item->getAssessmentUuid(), $relatedAssessmentsUuids);
                        $relatedAssessmentsUuids = array_unique($relatedAssessmentsUuids);
                        array_splice($relatedAssessmentsUuids, $index, 1);

                    } elseif (!in_array($item->getAssessmentUuid(), $relatedAssessmentsUuids)) {
                        $em->remove($item);
                    }
                }

                if (count($relatedAssessmentsUuids) > 0) {
                    $related = $assessmentItem->getRelated();

                    foreach ($relatedAssessmentsUuids as $uuid) {
                        $group = new TGroupItem();
                        $group->setAssessmentUuid($uuid);

                        $related->setGroupItems($group);
                    }

                    $assessmentItem->setRelated($related);
                }
            } else {

                $related = new TRelated();
                $related->setUuid($assessmentItem->getUuid());
                $related->setCreationTimestamp($assessmentItem->getCreationTimestamp());
                $related->setTittle($assessmentItem->getTittle());
                $related->setCategoryTags(implode(',', $currentCat));

                foreach ($relatedAssessmentsUuids as $uuid) {
                    $group = new TGroupItem();
                    $group->setAssessmentUuid($uuid);

                    $related->setGroupItems($group);
                }

                $assessmentItem->setRelated($related);
            }

        }
    }

    private function setSupportMedia(TSupport $support, $oldFileSupportMediaName) {

        if ($support->getSupportType() === "media") {

            // leave old image (it is already in db and in file system)
            if ( $support->getSupportMedia()->getMediaSourceOrigName() and
                ($support->getSupportMedia()->getMediaSource() === NULL) ) {
                // set path of old image in object
                $support->getSupportMedia()->setMediaSource($oldFileSupportMediaName);
            } elseif ($support->getSupportMedia()->getMediaSourceOrigName() == NULL) {
                // remove new set image from object (it is not in filesystem
                if ($support->getSupportMedia()->getMediaSource() != null) {
                    $support->getSupportMedia()->setMediaSource(null);
                } else {

                    $path = $this->getParameter('support_directory');
                    if ($support->getIdentifier() == 'image') {
                        $support->setIdentifier('image');
                    } elseif ($support->getIdentifier() == 'video') {
                        $support->setIdentifier('video');
                    }

                    // remove old image from filesystem
                    if ($oldFileSupportMediaName and file_exists($path.'/'.$oldFileSupportMediaName)){
                        $fs = new Filesystem();
                        $fs->remove($path.'/'.$oldFileSupportMediaName);
                    }
                }
            } else {
                // $file stores the uploaded file
                $file = $support->getSupportMedia()->getMediaSource();
                if ($file != null) {
                    $typeOfFile = substr($file->getMimeType(), 0, 5);

                    $path = $this->getParameter('support_directory');
                    if ($typeOfFile == 'image' or $typeOfFile == 'video') {
                        if ($typeOfFile == 'image') {

                            $support->setIdentifier('image');
                        } elseif ($typeOfFile == 'video') {
                            $support->setIdentifier('video');
                        }

                        // set new image path and save in filesystem
                        // remove old file if it was changed with new
                        if ($oldFileSupportMediaName and file_exists($path.'/'.$oldFileSupportMediaName)) {
                            $fs = new Filesystem();
                            $fs->remove($path.'/'.$oldFileSupportMediaName);
                        }

                        $support->getSupportMedia()->setMediaSourceOrigName($file->getClientOriginalName());

                        // Generate a unique name for the file before saving it
                        $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                        // Move the file to the directory where brochures are stored
                        $file->move($path, $fileName);
                        // Update the 'imgSrc' property to store the image file name
                        // instead of its contents
                        $support->getSupportMedia()->setMediaSource($fileName);
                    }
                }
            }
        }
    }

    private function addNewCategoryInDB($oldCategories, $currentCategories, $em){
        // find out which categories are not exist in db
        foreach ($oldCategories as $oldCategory) {
            if (in_array($oldCategory->getCategoryName(), $currentCategories)) {
                $index = array_search($oldCategory->getCategoryName(), $currentCategories);
                array_splice($currentCategories, $index, 1);
            }
        }

        // add new category in db categories
        if ($currentCategories) {
            foreach ($currentCategories as $category) {
                $newCategory = new TCategories();
                $newCategory->setCategoryName($category);
                $em->persist($newCategory);
            }
        }
    }

    private function addCategoriesAssessment($assessmentItem)
    {
        // add or remove category tags
        $currentCategories = array();
        if ($assessmentItem->getCurrentCategory() != null) {
            $currentCategories = explode(",", $assessmentItem->getCurrentCategory());
        }

        foreach ($currentCategories as $category) {
            $categoryTag = new TCategoryTags();
            $categoryTag->setTagName($category);
            $assessmentItem->setCategoryTags($categoryTag);
        }
        return $currentCategories;
    }

    private function editSupportAssessment($assessmentItem, $em, $oldFileSupportMediaName)
    {
        $support = $assessmentItem->getSupport();
        // edit support selection
        if ($support != null) {
            if ($support->getSupportType() === 'selection') {

                if ( $oldFileSupportMediaName != null) {
                    $path = $this->getParameter('support_directory');
                    if ($support->getIdentifier() == 'image') {
                        $support->setIdentifier('image');
                    } elseif ($support->getIdentifier() == 'video') {
                        $support->setIdentifier('video');
                    }

                    // remove old image from filesystem
                    if (file_exists($path . '/' . $oldFileSupportMediaName)) {
                        $fs = new Filesystem();
                        $fs->remove($path . '/' . $oldFileSupportMediaName);
                    }
                }

                if ($support->getSupportSelection() != null) {
                    $currentSelections = array();

                    if ($support->getSupportSelection()->getCurrentSelection() != null) {
                        $currentSelections = explode(",", $support->getSupportSelection()->getCurrentSelection());

                        foreach ($support->getSupportSelection()->getSelectionItem() as $selectionItem) {
                            if (in_array($selectionItem->getSelectValue(), $currentSelections)) {
                                $index = array_search($selectionItem->getSelectValue(), $currentSelections);
                                array_splice($currentSelections, $index, 1);

                            } elseif (!in_array($selectionItem->getSelectValue(), $currentSelections)) {
                                $em->remove($selectionItem);
                            }
                        }
                        if (count($currentSelections) > 0) {
                            foreach ($currentSelections as $currentSelection) {
                                $selectionItem = new TSelectionItem();
                                $selectionItem->setSelectValue($currentSelection);
                                $support->getSupportSelection()->addSelectionItem($selectionItem);
                            }
                        }

                        $support->setIdentifier('selection');

                        $em->remove($support->getSupportMedia());
                        $em->remove($support->getSupportTextbox());
                        $em->remove($support->getSupportTable());

                        $support->removeSupportMedia();
                        $support->removeSupportTextbox();
                        $support->removeSupportTable();

                    } else {
                        $assessmentItem->removeSupport();
                        $em->remove($support);
                    }
                }
            }

            if ($support->getSupportType() === 'media') {

                if ($support->getSupportMedia() != null) {
                    $this->setSupportMedia($support, $oldFileSupportMediaName);
                    if ($support->getSupportMedia()->getMediaSourceOrigName() != null) {

                        $em->remove($support->getSupportSelection());
                        $em->remove($support->getSupportTextbox());
                        $em->remove($support->getSupportTable());

                        $support->removeSupportSelection();
                        $support->removeSupportTextbox();
                        $support->removeSupportTable();

                    } else {
                        $assessmentItem->removeSupport();
                        $em->remove($support);
                    }
                }
            }

            if ($support->getSupportType() === 'textbox') {

                if ( $oldFileSupportMediaName != null) {
                    $path = $this->getParameter('support_directory');
                    if ($support->getIdentifier() == 'image') {
                        $support->setIdentifier('image');
                    } elseif ($support->getIdentifier() == 'video') {

                        $support->setIdentifier('video');
                    }

                    // remove old image from filesystem
                    if (file_exists($path . '/' . $oldFileSupportMediaName)) {
                        $fs = new Filesystem();
                        $fs->remove($path . '/' . $oldFileSupportMediaName);
                    }
                }

                if ($support->getSupportTextbox() != null and $support->getSupportTextbox()->getTextboxContent() != null) {

                    $support->setIdentifier('textbox');

                    $em->remove($support->getSupportMedia());
                    $em->remove($support->getSupportSelection());
                    $support->removeSupportTable();

                    $support->removeSupportMedia();
                    $support->removeSupportSelection();
                    $support->removeSupportTable();
                } else {
                    $assessmentItem->removeSupport();
                    $em->remove($support);
                }
            }

            if ($support->getSupportType() === 'table') {

                if ( $oldFileSupportMediaName != null) {
                    $path = $this->getParameter('support_directory');
                    if ($support->getIdentifier() == 'image') {
                        $support->setIdentifier('image');
                    } elseif ($support->getIdentifier() == 'video') {
                        $support->setIdentifier('video');
                    }

                    // remove old image from filesystem
                    if (file_exists($path . '/' . $oldFileSupportMediaName)) {
                        $fs = new Filesystem();
                        $fs->remove($path . '/' . $oldFileSupportMediaName);
                    }
                }

                $isSupportTableUsed = false;
                foreach ($support->getSupportTable()->getRow() as $row) {
                    if ($row->getCell() != null) {
                        foreach ($row->getCell() as $cell) {
                            if ($cell->getValue() != null) {
                                $isSupportTableUsed = true;
                                break;
                            }
                        }
                    }
                }
                if ($isSupportTableUsed === false) {
                    $assessmentItem->removeSupport();
                    $em->remove($support);
                } else {

                    $support->setIdentifier('table');
                    $em->remove($support->getSupportMedia());
                    $em->remove($support->getSupportSelection());
                    $em->remove($support->getSupportTextbox());

                    $support->removeSupportMedia();
                    $support->removeSupportTextbox();
                    $support->removeSupportSelection();
                }
            }
        }
    }

    private function setNewSupportAssessment($support)
    {
        $isSupport = true;

        if ($support->getSupportType() === "media") {
            if ($support->getSupportMedia()->getMediaSource() === null) {
                $support->removeSupportMedia();
                $isSupport = false;
            } else {
                $this->setSupportMedia($support, null);
            }
            $support->removeSupportTextbox();
            $support->removeSupportSelection();
            $support->removeSupportTable();
        } elseif ($support->getSupportType() === "textbox") {
            if ($support->getSupportTextbox()->getTextboxContent() === null) {
                $support->removeSupportTextbox();
                $isSupport = false;
            } else {
                $support->setIdentifier('textbox');
            }
            $support->removeSupportMedia();
            $support->removeSupportSelection();
            $support->removeSupportTable();
        } elseif ($support->getSupportType() === "selection") {
            if ($support->getSupportSelection()->getCurrentSelection() === null) {
                $support->removeSupportSelection();
                $isSupport = false;
            } else {
                $support->setIdentifier('selection');
                $currentSelectionItems = explode(",", $support->getSupportSelection()->getCurrentSelection());
                foreach ($currentSelectionItems as $item) {
                    $selectionItem = new TSelectionItem();
                    $selectionItem->setSelectValue($item);
                    $support->getSupportSelection()->addSelectionItem($selectionItem);
                }
            }
            $support->removeSupportMedia();
            $support->removeSupportTextbox();
            $support->removeSupportTable();
        } elseif ($support->getSupportType() === "table") {

            $isSupportTableUsed = false;
            foreach ($support->getSupportTable()->getRow() as $row) {
                if ($row->getCell() != null) {
                    foreach ($row->getCell() as $cell) {
                        if ($cell->getValue() != null) {
                            $isSupportTableUsed = true;
                            break;
                        }
                    }
                }
            }

            if ($isSupportTableUsed === false) {
                $support->removeSupportTable();
                $isSupport = false;
            } else {
                $support->setIdentifier('table');
            }

            $support->removeSupportMedia();
            $support->removeSupportSelection();
            $support->removeSupportTextbox();
        }

        return $isSupport;
    }

    private function prepareSupportForEdit($assessmentItem) {

        $supportMediaPath = $this->getParameter('support_directory');
        $support = $assessmentItem->getSupport();
        $oldFileSupportMediaName = null;
        $currentSelections = [];
        if ($support != null) {
            if ($support->getSupportType() === 'selection') {
                if ($support->getSupportSelection() != null) {
                    foreach ($support->getSupportSelection()->getSelectionItem() as $item) {
                        $currentSelections[$item->getSelectValue()] = $item->getSelectValue();
                    }
                    $support->getSupportSelection()->setCurrentSelection($currentSelections);
                }
            } elseif ($support->getSupportType() === 'media') {
                if ($support->getSupportMedia() != null) {
                    // image name in db for support
                    $oldFileSupportMediaName = $support->getSupportMedia()->getMediaSource();
                    $mediaPath = '';
                    if ($support->getIdentifier() === 'image') {
                        $mediaPath = $supportMediaPath . '/' . $oldFileSupportMediaName;
                    } elseif ($support->getIdentifier() === 'video') {
                        $mediaPath = $supportMediaPath . '/' . $oldFileSupportMediaName;
                    }

                    // create file object if image name exists in db and in file system
                    if ($oldFileSupportMediaName and file_exists($mediaPath)) {
                        $uploadedFile = new UploadedFile($mediaPath, $support->getSupportMedia()->getMediaSourceOrigName());
                        $support->getSupportMedia()->setMediaSource($uploadedFile);
                    } else {
                        $support->getSupportMedia()->setMediaSource(null);
                    }
                }
            }

            if ($support->getSupportType() !== 'table') {
                $supportTable = new TSupportTable();
                $supportRow = new TRow();
                $supportCell1 = new TCell();
                $supportRow->addCell($supportCell1);
                $supportCell2 = new TCell();
                $supportRow->addCell($supportCell2);
                $supportTable->addRow($supportRow);

                $support->setSupportTable($supportTable);
            }


        } else {
            $support = new TSupport();
            $supportTextBox = new TSupportTextbox();
            $supportMedia = new TSupportMedia();
            $supportSelection = new TSupportSelection();
            $supportTable = new TSupportTable();

            $supportRow = new TRow();
            $supportCell1 = new TCell();
            $supportRow->addCell($supportCell1);
            $supportCell2 = new TCell();
            $supportRow->addCell($supportCell2);
            $supportTable->addRow($supportRow);

            $support->setSupportType('media');
            $support->setSupportTextbox($supportTextBox);
            $support->setSupportMedia($supportMedia);
            $support->setSupportSelection($supportSelection);
            $support->setSupportTable($supportTable);

            $helpFunctions = new HelpFunctions();
            $date = new DateTime();
            $timestampNew = $date->getTimestamp();

            $support->setUuid($helpFunctions->generateUUIDv4());
            $support->setAssessmentUuid($assessmentItem->getUuid());
            $support->setCreationTimestamp((string)$timestampNew);

            $assessmentItem->setSupport($support);
        }

        $returnSelAndOldFileName[] = $currentSelections;
        $returnSelAndOldFileName[] = $oldFileSupportMediaName;

        return $returnSelAndOldFileName;
    }
}
