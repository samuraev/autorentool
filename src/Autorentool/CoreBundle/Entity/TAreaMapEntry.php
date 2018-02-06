<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TAreaMapEntry
 *
 * @ORM\Table(name="t_area_map_entry", indexes={@ORM\Index(name="fk_t_area_mapping_id", columns={"fk_t_area_mapping_id"})})
 * @ORM\Entity
 */
class TAreaMapEntry
{
    /**
     * @var string
     *
     * @ORM\Column(name="shape", type="text", length=65535, nullable=true)
     */
    private $shape;

    /**
     * @var string
     *
     * @ORM\Column(name="coords", type="text", length=65535, nullable=true)
     */
    private $coords;

    /**
     * @var integer
     *
     * @ORM\Column(name="mapped_value", type="integer", nullable=true)
     */
    private $mappedValue;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TAreaMapping
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TAreaMapping", inversedBy="areaMapEntry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_area_mapping_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTAreaMapping;

    private $current;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", length=65535, nullable=true)
     */
    private $data;

    /**
     * Get data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return TAreaMapEntry
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    public function getCurrent()
    {
        return $this->current;
    }

    public function setCurrent($current)
    {
        $this->current = $current;
    }

    /**
     * Get shape
     *
     * @return string
     */
    public function getShape()
    {
        return $this->shape;
    }

    /**
     * Set shape
     *
     * @param string $shape
     *
     * @return TAreaMapEntry
     */
    public function setShape($shape)
    {
        $this->shape = $shape;

        return $this;
    }

    /**
     * Get coords
     *
     * @return string
     */
    public function getCoords()
    {
        return $this->coords;
    }

    /**
     * Set coords
     *
     * @param string $coords
     *
     * @return TAreaMapEntry
     */
    public function setCoords($coords)
    {
        $this->coords = $coords;

        return $this;
    }

    /**
     * Get mappedValue
     *
     * @return integer
     */
    public function getMappedValue()
    {
        return $this->mappedValue;
    }

    /**
     * Set mappedValue
     *
     * @param integer $mappedValue
     *
     * @return TAreaMapEntry
     */
    public function setMappedValue($mappedValue)
    {
        $this->mappedValue = $mappedValue;

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
     * Get fkTAreaMapping
     *
     * @return \Autorentool\CoreBundle\Entity\TAreaMapping
     */
    public function getFkTAreaMapping()
    {
        return $this->fkTAreaMapping;
    }

    /**
     * Set fkTAreaMapping
     *
     * @param \Autorentool\CoreBundle\Entity\TAreaMapping $fkTAreaMapping
     *
     * @return TAreaMapEntry
     */
    public function setFkTAreaMapping(\Autorentool\CoreBundle\Entity\TAreaMapping $fkTAreaMapping = null)
    {
        $this->fkTAreaMapping = $fkTAreaMapping;

        return $this;
    }
}
