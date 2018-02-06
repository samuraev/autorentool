<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TCategoryTags
 *
 * @ORM\Table(name="t_category_tags", indexes={@ORM\Index(name="fk_t_assessment_item_id", columns={"fk_t_assessment_item_id"})})
 * @ORM\Entity
 */
class TCategoryTags
{
    /**
     * @var string
     *
     * @ORM\Column(name="tag_name", type="text", length=65535, nullable=true)
     */
    private $tagName;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TAssessmentItem
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TAssessmentItem", inversedBy="categoryTags")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_assessment_item_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTAssessmentItem;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get fkTAssessmentItem
     *
     * @return \Autorentool\CoreBundle\Entity\TAssessmentItem
     */
    public function getFkTAssessmentItem()
    {
        return $this->fkTAssessmentItem;
    }

    /**
     * Set fkTAssessmentItem
     *
     * @param \Autorentool\CoreBundle\Entity\TAssessmentItem $fkTAssessmentItem
     *
     * @return TCategoryTags
     */
    public function setFkTAssessmentItem(\Autorentool\CoreBundle\Entity\TAssessmentItem $fkTAssessmentItem)
    {
        $this->fkTAssessmentItem = $fkTAssessmentItem;

        return $this;
    }

    /**
     * Get tagName
     *
     * @return string
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * Set tagName
     *
     * @param string $tagName
     *
     * @return TCategoryTags
     */
    public function setTagName($tagName)
    {
        $this->tagName = $tagName;

        return $this;
    }
}
