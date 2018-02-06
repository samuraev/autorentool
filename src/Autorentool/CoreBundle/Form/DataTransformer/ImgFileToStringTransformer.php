<?php
/**
 * Created by PhpStorm.
 * User: alexey
 * Date: 13.11.17
 * Time: 20:40
 */

namespace Autorentool\CoreBundle\Form\DataTransformer;

use Autorentool\CoreBundle\Entity\TAssessmentItem;
use Autorentool\CoreBundle\Entity\TCategoryTags;
use Autorentool\CoreBundle\Entity\TItemBody;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;

class ImgFileToStringTransformer implements DataTransformerInterface
{
    private $em;

        public function __construct(EntityManagerInterface $em)
        {
            $this->em = $em;
        }

    /**
     * Transforms an object (imgSrc) to an array.
     *
     * @param  string
     * @return UploadedFile|null
     */
    public function transform($path)
    {
        if ($path === null) {

            return null;
        }
        $mimeTypes = array( "image/jpg", "image/png", "image/jpeg");

        //$container = new Container();

        //$fullPath = $this->$container->getParameter('img_directory');

        $file = new UploadedFile("/Volumes/Data/MA/Project/autorentool/web/uploads/img/".$path, "");

        return $file;
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  UploadedFile|null
     * @return string
     * @throws TransformationFailedException if object ($imgSrc) is not found.
     */
    public function reverseTransform($file)
    {
        if ($file === null) {
            return null;
        }
        return $file;
    }
}