<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TDragItem
 *
 * @ORM\Table(name="t_drag_item", indexes={@ORM\Index(name="fk_t_drag_interaction_id", columns={"fk_t_drag_interaction_id"})})
 * @ORM\Entity
 */
class TDragItem
{
    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="text", length=65535, nullable=false)
     */
    private $identifier;

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
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TDragInteraction", inversedBy="dragItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_drag_interaction_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTDragInteraction;

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
     * @return TDragItem
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

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
     * @return TDragItem
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
     * @return TDragItem
     */
    public function setFkTDragInteraction(\Autorentool\CoreBundle\Entity\TDragInteraction $fkTDragInteraction = null)
    {
        $this->fkTDragInteraction = $fkTDragInteraction;

        return $this;
    }
}
