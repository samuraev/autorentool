<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * TTable
 *
 * @ORM\Table(name="t_table", indexes={@ORM\Index(name="fk_t_table_interaction_id", columns={"fk_t_table_interaction_id"}), @ORM\Index(name="fk_t_drag_interaction_id", columns={"fk_t_drag_interaction_id"})})
 * @ORM\Entity
 */
class TTable
{
    /**
     * @var string
     *
     * @ORM\Column(name="response_identifier", type="text", length=65535, nullable=true)
     */
    private $responseIdentifier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TTableInteraction
     *
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TTableInteraction", inversedBy="table")
     * @ORM\JoinColumn(name="fk_t_table_interaction_id", referencedColumnName="id", onDelete="CASCADE")
     *
     */
    private $fkTTableInteraction;


    /**
     * @var \Autorentool\CoreBundle\Entity\TDragInteraction
     *
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TDragInteraction", inversedBy="dragTable")
     * @ORM\JoinColumn(name="fk_t_drag_interaction_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $fkTDragInteraction;

    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TRow",
     *     mappedBy="fkTTable", cascade={"persist"},
     *     orphanRemoval=true)
     **/
    private $row;

    public function __construct()
    {
        $this->row = new ArrayCollection();
    }

    /**
     * Get row
     *
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Set row
     *
     */
    public function setRow($row)
    {
        $this->row = $row;
        foreach ($row as $item) {
            $item->setFkTTable($this);
        }

        return $this;
    }

    public function addRow(\Autorentool\CoreBundle\Entity\TRow $row)
    {
        $row->setFkTTable($this);
        $this->row->add($row);
    }

    public function removeRow(\Autorentool\CoreBundle\Entity\TRow $row)
    {
        $this->row->removeElement($row);
    }

    /**
     * Get responseIdentifier
     *
     * @return string
     */
    public function getResponseIdentifier()
    {
        return $this->responseIdentifier;
    }

    /**
     * Set responseIdentifier
     *
     * @param string $responseIdentifier
     *
     * @return TTable
     */
    public function setResponseIdentifier($responseIdentifier)
    {
        $this->responseIdentifier = $responseIdentifier;

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
     * Get fkTTableInteraction
     *
     * @return \Autorentool\CoreBundle\Entity\TTableInteraction
     */
    public function getFkTTableInteraction()
    {
        return $this->fkTTableInteraction;
    }

    /**
     * Set fkTTableInteraction
     *
     * @param \Autorentool\CoreBundle\Entity\TTableInteraction $fkTTableInteraction
     *
     * @return TTable
     */
    public function setFkTTableInteraction(\Autorentool\CoreBundle\Entity\TTableInteraction $fkTTableInteraction = null)
    {
        $this->fkTTableInteraction = $fkTTableInteraction;

        return $this;
    }

    /**
     * Get fkTDragInteraction
     *
     * @return \Autorentool\CoreBundle\Entity\TDragInteraction
     */
    public function getFkTDragInteraction()
    {
        return $this->fkTDragInteraction;
    }

    /**
     * Set fkTDragInteraction
     *
     * @param \Autorentool\CoreBundle\Entity\TDragInteraction $fkTDragInteraction
     *
     * @return TTable
     */
    public function setFkTDragInteraction(\Autorentool\CoreBundle\Entity\TDragInteraction $fkTDragInteraction = null)
    {
        $this->fkTDragInteraction = $fkTDragInteraction;

        return $this;
    }
}
