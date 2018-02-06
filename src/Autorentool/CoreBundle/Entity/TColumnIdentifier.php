<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TColumnIdentifier
 *
 * @ORM\Table(name="t_column_identifier", indexes={@ORM\Index(name="fk_t_drag_interaction_id", columns={"fk_t_drag_interaction_id"})})
 * @ORM\Entity
 */
class TColumnIdentifier
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id2", type="integer", nullable=true)
     */
    private $id2;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", length=65535, nullable=true)
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TDragInteraction
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TDragInteraction", inversedBy="columnIdentifier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_drag_interaction_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTDragInteraction;

    /**
     * Get id2
     *
     * @return integer
     */
    public function getId2()
    {
        return $this->id2;
    }

    /**
     * Set id2
     *
     * @param integer $id2
     *
     * @return TColumnIdentifier
     */
    public function setId2($id2)
    {
        $this->id2 = $id2;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return TColumnIdentifier
     */
    public function setValue($value)
    {
        $this->value = $value;

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
     * @return TColumnIdentifier
     */
    public function setFkTDragInteraction(\Autorentool\CoreBundle\Entity\TDragInteraction $fkTDragInteraction = null)
    {
        $this->fkTDragInteraction = $fkTDragInteraction;

        return $this;
    }
}
