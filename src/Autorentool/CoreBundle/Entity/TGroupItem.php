<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TGroupItem
 *
 * @ORM\Table(name="t_group_item", indexes={@ORM\Index(name="fk_t_related_id", columns={"fk_t_related_id"})})
 * @ORM\Entity
 */
class TGroupItem
{
    /**
     * @var string
     *
     * @ORM\Column(name="assessment_uuid", type="text", length=65535, nullable=true)
     */
    private $assessmentUuid;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TRelated
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TRelated", inversedBy="groupItems")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_related_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTRelated;

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
     * @return TGroupItem
     */
    public function setAssessmentUuid($assessmentUuid)
    {
        $this->assessmentUuid = $assessmentUuid;

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
     * Get fkTRelated
     *
     * @return \Autorentool\CoreBundle\Entity\TRelated
     */
    public function getFkTRelated()
    {
        return $this->fkTRelated;
    }

    /**
     * Set fkTRelated
     *
     * @param \Autorentool\CoreBundle\Entity\TRelated $fkTRelated
     *
     * @return TGroupItem
     */
    public function setFkTRelated(\Autorentool\CoreBundle\Entity\TRelated $fkTRelated = null)
    {
        $this->fkTRelated = $fkTRelated;

        return $this;
    }
}
