<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TSupport
 *
 * @ORM\Table(name="t_support", uniqueConstraints={@ORM\UniqueConstraint(name="fk_t_assessment_item_id", columns={"fk_t_assessment_item_id"})})
 * @ORM\Entity
 */
class TSupport
{
    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="text", length=65535, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="assessment_uuid", type="text", length=65535, nullable=true)
     */
    private $assessmentUuid;

    /**
     * @var integer
     *
     * @ORM\Column(name="creation_timestamp", type="integer", nullable=true)
     */
    private $creationTimestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="text", length=65535, nullable=true)
     */
    private $identifier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TSupportTextbox", mappedBy="fkTSupport",cascade={"persist"})
     **/
    private $supportTextbox;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TSupportMedia", mappedBy="fkTSupport",cascade={"persist"})
     **/
    private $supportMedia;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TSupportSelection", mappedBy="fkTSupport",cascade={"persist"})
     **/
    private $supportSelection;

    /**
     * @var \Autorentool\CoreBundle\Entity\TAssessmentItem
     *
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TAssessmentItem", inversedBy="support")
     * @ORM\JoinColumn(name="fk_t_assessment_item_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $fkTAssessmentItem;

    /**
     * @var string
     *
     * @ORM\Column(name="support_type", type="text", length=65535, nullable=true)
     */
    protected $supportType;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TSupportTable", mappedBy="fkTSupport",cascade={"persist"})
     **/
    private $supportTable;

    /**
     * Get supportTable
     *
     */
    public function getSupportTable()
    {
        return $this->supportTable;
    }

    /**
     * Set supportTable
     *
     */
    public function setSupportTable(\Autorentool\CoreBundle\Entity\TSupportTable $supportTable)
    {
        $this->supportTable = $supportTable;
        $supportTable->setFkTSupport($this);

        return $this;
    }

    public function removeSupportTable()
    {
        $this->supportTable = null;
    }

    /**
     * Get supportType
     *
     * @return string
     */
    public function getSupportType()
    {
        return $this->supportType;
    }

    /**
     * Set supportType
     *
     * @param string $supportType
     *
     * @return TSupport
     */
    public function setSupportType($supportType)
    {
        $this->supportType = $supportType;

        return $this;
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
     * @return TSupport
     */
    public function setFkTAssessmentItem(\Autorentool\CoreBundle\Entity\TAssessmentItem $fkTAssessmentItem)
    {
        $this->fkTAssessmentItem = $fkTAssessmentItem;

        return $this;
    }

    /**
     * Get supportMedia
     *
     */
    public function getSupportMedia()
    {
        return $this->supportMedia;
    }

    /**
     * Set supportMedia
     *
     */
    public function setSupportMedia(\Autorentool\CoreBundle\Entity\TSupportMedia $supportMedia)
    {
        $this->supportMedia = $supportMedia;
        $supportMedia->setFkTSupport($this);

        return $this;
    }

    public function removeSupportMedia()
    {
        $this->supportMedia = null;
    }

    /**
     * Get supportSelection
     *
     */
    public function getSupportSelection()
    {
        return $this->supportSelection;
    }

    /**
     * Set supportSelection
     *
     */
    public function setSupportSelection(\Autorentool\CoreBundle\Entity\TSupportSelection $supportSelection)
    {
        $this->supportSelection = $supportSelection;
        $supportSelection->setFkTSupport($this);

        return $this;
    }

    public function removeSupportSelection()
    {
        $this->supportSelection = null;
    }
    /**
     * Get supportTextbox
     *
     */
    public function getSupportTextbox()
    {
        return $this->supportTextbox;
    }

    /**
     * Set supportTextbox
     *
     */
    public function setSupportTextbox(\Autorentool\CoreBundle\Entity\TSupportTextbox $supportTextbox)
    {
        $this->supportTextbox = $supportTextbox;
        $supportTextbox->setFkTSupport($this);

        return $this;
    }

    public function removeSupportTextbox()
    {
        $this->supportTextbox = null;
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
     * @return TSupport
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get assessmentUuid
     *
     * @return string
     */
    public function getAssessmentUuid()
    {
        return $this->assessmentUuid;
    }

    /**
     * Set assessmentUuid
     *
     * @param string $assessmentUuid
     *
     * @return TSupport
     */
    public function setAssessmentUuid($assessmentUuid)
    {
        $this->assessmentUuid = $assessmentUuid;

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
     * @return TSupport
     */
    public function setCreationTimestamp($creationTimestamp)
    {
        $this->creationTimestamp = $creationTimestamp;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     *
     * @return TSupport
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

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
}
