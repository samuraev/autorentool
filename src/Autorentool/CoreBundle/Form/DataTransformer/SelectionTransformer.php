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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class SelectionTransformer implements DataTransformerInterface
{
    /**
     * Transforms an object (categoryTags) to an array.
     *
     * @param  string|null $categoryTagsString
     * @return array $categoryTagsArray
     */
    public function transform($categoryTagsArray)
    {
        if (null === $categoryTagsArray) {
            return [];
        }
        return $categoryTagsArray;
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  array $currentCategoryArray
     * @return string|null $currentCategory
     * @throws TransformationFailedException if object ($categoryTags) is not found.
     */
    public function reverseTransform($categoryTagsArray)
    {
        if (!$categoryTagsArray) {
            return null;
        }

        $currentCategory = implode(",", $categoryTagsArray);

        return $currentCategory;
    }
}