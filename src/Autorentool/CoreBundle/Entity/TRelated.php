<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * TRelated
 *
 * @ORM\Table(name="t_related", uniqueConstraints={@ORM\UniqueConstraint(name="fk_t_assessment_item_id", columns={"fk_t_assessment_item_id"})})
 * @ORM\Entity
 */
class TRelated
{
    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="text", length=65535, nullable=true)
     */
    private $uuid;

    /**
     * @var integer
     *
     * @ORM\Column(name="creation_timestamp", type="integer", nullable=true)
     */
    private $creationTimestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="text", length=65535, nullable=true)
     */
    private $tittle;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TGroupItem", mappedBy="fkTRelated",cascade={"persist"})
     **/
    private $groupItems;

    /**
     *
     * @ORM\Column(name="category_tags", type="text", length=65535, nullable=true)
     **/
    private $categoryTags;

    /**
     * @var \Autorentool\CoreBundle\Entity\TAssessmentItem
     *
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TAssessmentItem", inversedBy="related")
     * @ORM\JoinColumn(name="fk_t_assessment_item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $fkTAssessmentItem;


    public function __construct()
    {
        $this->groupItems = new ArrayCollection();
    }

    /**
     * Get values
     *
     */
    public function getGroupItems()
    {
        return $this->groupItems;
    }

    /**
     * Set values
     *
     */
    public function setGroupItems(\Autorentool\CoreBundle\Entity\TGroupItem $groupItems)
    {
        $this->groupItems[] = $groupItems;
        $groupItems->setFkTRelated($this);

        return $this;
    }

    /**
     * Get categoryTags
     *
     */
    public function getCategoryTags()
    {
        return $this->categoryTags;
    }

    /**
     * Set categoryTags
     *
     */
    public function setCategoryTags($categoryTags)
    {
        $this->categoryTags = $categoryTags;
        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return TRelated
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get creationTimestamp
     *
     * @return integer
     */
    public function getCreationTimestamp()
    {
        return $this->creationTimestamp;
    }

    /**
     * Set creationTimestamp
     *
     * @param integer $creationTimestamp
     *
     * @return TRelated
     */
    public function setCreationTimestamp($creationTimestamp)
    {
        $this->creationTimestamp = $creationTimestamp;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTittle()
    {
        return $this->tittle;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return TRelated
     */
    public function setTittle($tittle)
    {
        $this->tittle = $tittle;

        return $this;
    }

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
     * @return TRelated
     */
    public function setFkTAssessmentItem(\Autorentool\CoreBundle\Entity\TAssessmentItem $fkTAssessmentItem)
    {
        $this->fkTAssessmentItem = $fkTAssessmentItem;

        return $this;
    }
}
