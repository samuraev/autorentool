<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * TAreaMapping
 *
 * @ORM\Table(name="t_area_mapping", uniqueConstraints={@ORM\UniqueConstraint(name="fk_t_response_declaration_id", columns={"fk_t_response_declaration_id"})})
 * @ORM\Entity
 */
class TAreaMapping
{
    /**
     * @var integer
     *
     * @ORM\Column(name="default_value", type="integer", nullable=true)
     */
    private $defaultValue;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TResponseDeclaration
     *
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TResponseDeclaration", inversedBy="areaMapping")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_response_declaration_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTResponseDeclaration;

    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TAreaMapEntry",
     *     mappedBy="fkTAreaMapping", cascade={"persist"},
     *     orphanRemoval=true)
     **/
    private $areaMapEntry;

    public function __construct()
    {
        $this->areaMapEntry = new ArrayCollection();
    }

    /**
     * Get areaMapEntry
     *
     */
    public function getAreaMapEntry()
    {
        return $this->areaMapEntry;
    }

    /**
     * Set areaMapEntry
     *
     */
    public function setAreaMapEntry($areaMapEntry)
    {
        $this->areaMapEntry = $areaMapEntry;
        foreach ($areaMapEntry as $item) {
            $item->setFkTAreaMapping($this);
        }

        return $this;
    }

    public function addAreaMapEntry(\Autorentool\CoreBundle\Entity\TAreaMapEntry $areaMapEntry)
    {
        $areaMapEntry->setFkTAreaMapping($this);
        $this->areaMapEntry->add($areaMapEntry);
    }

    public function removeAreaMapEntry(\Autorentool\CoreBundle\Entity\TAreaMapEntry $areaMapEntry)
    {
        $this->areaMapEntry->removeElement($areaMapEntry);
    }

    /**
     * Get defaultValue
     *
     * @return integer
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set defaultValue
     *
     * @param integer $defaultValue
     *
     * @return TAreaMapping
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;

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
     * Get fkTResponseDeclaration
     *
     * @return \Autorentool\CoreBundle\Entity\TResponseDeclaration
     */
    public function getFkTResponseDeclaration()
    {
        return $this->fkTResponseDeclaration;
    }

    /**
     * Set fkTResponseDeclaration
     *
     * @param \Autorentool\CoreBundle\Entity\TResponseDeclaration $fkTResponseDeclaration
     *
     * @return TAreaMapping
     */
    public function setFkTResponseDeclaration(\Autorentool\CoreBundle\Entity\TResponseDeclaration $fkTResponseDeclaration = null)
    {
        $this->fkTResponseDeclaration = $fkTResponseDeclaration;

        return $this;
    }
}
