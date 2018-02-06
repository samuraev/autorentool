<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TItemBody
 *
 * @ORM\Table(name="t_item_body",
 *     uniqueConstraints={@ORM\UniqueConstraint(name="fk_t_assessment_item_id", columns={"fk_t_assessment_item_id"})})
 * @ORM\Entity
 */
class TItemBody
{
    /**
     * @var string
     *
     * @ORM\Column(name="paragraph", type="text", length=65535, nullable=true)
     */
    private $paragraph;

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
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TAssessmentItem", inversedBy="itemBody")
     * @ORM\JoinColumn(name="fk_t_assessment_item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $fkTAssessmentItem;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TChoiceInteraction",
     *     mappedBy="fkTItemBody",cascade={"persist"})
     **/
    private $choiceInteraction;

    /**
     * @var string
     *
     * @ORM\Column(name="img_src", type="text", length=65535, nullable=true)
     */
    private $imgSrc;

    private $removeImgSrcState;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\THotspotInteraction", mappedBy="fkTItemBody",cascade={"persist"})
     **/
    private $hotspotInteraction;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TTableInteraction", mappedBy="fkTItemBody",cascade={"persist"})
     **/
    private $tableInteraction;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TDragInteraction", mappedBy="fkTItemBody",cascade={"persist"})
     **/
    private $dragInteraction;


    /**
     * Get tableInteraction
     *
     */
    public function getTableInteraction()
    {
        return $this->tableInteraction;
    }

    /**
     * Set tableInteraction
     *
     */
    public function setTableInteraction(\Autorentool\CoreBundle\Entity\TTableInteraction $tableInteraction)
    {
        $this->tableInteraction = $tableInteraction;
        $tableInteraction->setFkTItemBody($this);

        return $this;
    }

    /**
     * Get dragInteraction
     *
     */
    public function getDragInteraction()
    {
        return $this->dragInteraction;
    }

    /**
     * Set dragInteraction
     *
     */
    public function setDragInteraction(\Autorentool\CoreBundle\Entity\TDragInteraction $dragInteraction)
    {
        $this->dragInteraction = $dragInteraction;
        $dragInteraction->setFkTItemBody($this);

        return $this;
    }

    /**
     * Get choiceInteraction
     *
     */
    public function getHotspotInteraction()
    {
        return $this->hotspotInteraction;
    }

    /**
     * Set choiceInteraction
     *
     */
    public function setHotspotInteraction(\Autorentool\CoreBundle\Entity\THotspotInteraction $hotspotInteraction)
    {
        $this->hotspotInteraction = $hotspotInteraction;
        $hotspotInteraction->setFkTItemBody($this);

        return $this;
    }

    /**
     * Get state of remove image by edit
     *
     * @return bool
     */
    public function getRemoveImgSrcState()
    {
        return $this->removeImgSrcState;
    }

    /**
     * Set state of remove image by edit
     *
     * @param bool $removeImgSrcState
     *
     * @return TItemBody
     */
    public function setRemoveImgSrcState($removeImgSrcState)
    {
        $this->removeImgSrcState = $removeImgSrcState;

        return $this;
    }

    /**
     * Get imgSrc
     *
     * @return string
     */
    public function getImgSrc()
    {
        return $this->imgSrc;
    }

    /**
     * Set imgSrc
     *
     * @param string $imgSrc
     *
     * @return TItemBody
     */
    public function setImgSrc($imgSrc)
    {
        $this->imgSrc = $imgSrc;

        return $this;
    }

    /**
     * Get choiceInteraction
     *
     */
    public function getChoiceInteraction()
    {
        return $this->choiceInteraction;
    }

    /**
     * Set choiceInteraction
     *
     */
    public function setChoiceInteraction(\Autorentool\CoreBundle\Entity\TChoiceInteraction $choiceInteraction)
    {
        $this->choiceInteraction = $choiceInteraction;
        $choiceInteraction->setFkTItemBody($this);

        return $this;
    }

    /**
     * Get paragraphs
     *
     * @return string
     */
    public function getParagraph()
    {
        return $this->paragraph;
    }

    /**
     * Set paragraph
     *
     * @param string $paragraph
     *
     * @return TItemBody
     */
    public function setParagraph($paragraph)
    {
        $this->paragraph = $paragraph;
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
     * @return TItemBody
     */
    public function setFkTAssessmentItem(\Autorentool\CoreBundle\Entity\TAssessmentItem $fkTAssessmentItem)
    {
        $this->fkTAssessmentItem = $fkTAssessmentItem;

        return $this;
    }

}