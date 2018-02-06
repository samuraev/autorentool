<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TDropZone
 *
 * @ORM\Table(name="t_drop_zone", indexes={@ORM\Index(name="fk_t_cell_id", columns={"fk_t_cell_id"})})
 * @ORM\Entity
 */
class TDropZone
{
    /**
     * @var string
     *
     * @ORM\Column(name="value_identifier", type="text", length=65535, nullable=true)
     */
    private $valueIdentifier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TCell
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TCell", inversedBy="dropZone")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_cell_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTCell;

    /**
     * Get valueIdentifier
     *
     * @return string
     */
    public function getValueIdentifier()
    {
        return $this->valueIdentifier;
    }

    /**
     * Set valueIdentifier
     *
     * @param string $valueIdentifier
     *
     * @return TDropZone
     */
    public function setValueIdentifier($valueIdentifier)
    {
        $this->valueIdentifier = $valueIdentifier;

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
     * Get fkTCell
     *
     * @return \Autorentool\CoreBundle\Entity\TCell
     */
    public function getFkTCell()
    {
        return $this->fkTCell;
    }

    /**
     * Set fkTCell
     *
     * @param \Autorentool\CoreBundle\Entity\TCell $fkTCell
     *
     * @return TDropZone
     */
    public function setFkTCell(\Autorentool\CoreBundle\Entity\TCell $fkTCell = null)
    {
        $this->fkTCell = $fkTCell;

        return $this;
    }
}
