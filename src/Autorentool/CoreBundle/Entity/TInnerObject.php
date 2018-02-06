<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TInnerObject
 *
 * @ORM\Table(name="t_inner_object", indexes={@ORM\Index(name="fk_t_hotspot_interaction_id", columns={"fk_t_hotspot_interaction_id"})})
 *
 * @ORM\Entity
 */
class TInnerObject
{
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="text", length=65535, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", length=65535, nullable=true)
     */
    private $data;

    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="integer", nullable=true)
     */
    private $width;

    /**
     * @var integer
     *
     * @ORM\Column(name="height", type="integer", nullable=true)
     */
    private $height;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\THotspotInteraction
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\THotspotInteraction", inversedBy="innerObject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_hotspot_interaction_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTHotspotInteraction;

    /**
     * Get fkTHotspotInteraction
     *
     * @return \Autorentool\CoreBundle\Entity\THotspotInteraction
     */
    public function getFkTHotspotInteraction()
    {
        return $this->fkTHotspotInteraction;
    }

    /**
     * Set fkTHotspotInteraction
     *
     * @param \Autorentool\CoreBundle\Entity\THotspotInteraction $fkTHotspotInteraction
     *
     * @return TInnerObject
     */
    public function setFkTHotspotInteraction(\Autorentool\CoreBundle\Entity\THotspotInteraction $fkTHotspotInteraction = null)
    {
        $this->fkTHotspotInteraction = $fkTHotspotInteraction;

        return $this;
    }


    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return TInnerObject
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

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
     * @return TInnerObject
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set width
     *
     * @param integer $width
     *
     * @return TInnerObject
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set height
     *
     * @param integer $height
     *
     * @return TInnerObject
     */
    public function setHeight($height)
    {
        $this->height = $height;

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
