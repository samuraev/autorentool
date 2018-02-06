<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TMapEntry
 *
 * @ORM\Table(name="t_map_entry", indexes={@ORM\Index(name="fk_t_mapping_id", columns={"fk_t_mapping_id"})})
 * @ORM\Entity
 */
class TMapEntry
{
    /**
     * @var string
     *
     * @ORM\Column(name="map_key", type="text", length=65535, nullable=true)
     */
    private $mapKey;

    /**
     * @var string
     *
     * @ORM\Column(name="mapped_value", type="text", length=65535, nullable=true)
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
     * @var \Autorentool\CoreBundle\Entity\TMapping
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TMapping", inversedBy="mapEntry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_mapping_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTMapping;

    /**
     * Get mapKey
     *
     * @return string
     */
    public function getMapKey()
    {
        return $this->mapKey;
    }

    /**
     * Set mapKey
     *
     * @param string $mapKey
     *
     * @return TMapEntry
     */
    public function setMapKey($mapKey)
    {
        $this->mapKey = $mapKey;

        return $this;
    }

    /**
     * Get mappedValue
     *
     * @return string
     */
    public function getMappedValue()
    {
        return $this->mappedValue;
    }

    /**
     * Set mappedValue
     *
     * @param string $mappedValue
     *
     * @return TMapEntry
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
     * Get fkTMapping
     *
     * @return \Autorentool\CoreBundle\Entity\TMapping
     */
    public function getFkTMapping()
    {
        return $this->fkTMapping;
    }

    /**
     * Set fkTMapping
     *
     * @param \Autorentool\CoreBundle\Entity\TMapping $fkTMapping
     *
     * @return TMapEntry
     */
    public function setFkTMapping(\Autorentool\CoreBundle\Entity\TMapping $fkTMapping = null)
    {
        $this->fkTMapping = $fkTMapping;

        return $this;
    }
}
