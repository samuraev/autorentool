<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * TMapping
 *
 * @ORM\Table(name="t_mapping", uniqueConstraints={@ORM\UniqueConstraint(name="fk_t_response_declaration_id", columns={"fk_t_response_declaration_id"})})
 * @ORM\Entity
 */
class TMapping
{
    /**
     * @var integer
     *
     * @ORM\Column(name="lower_bound", type="integer", nullable=true)
     */
    private $lowerBound;

    /**
     * @var integer
     *
     * @ORM\Column(name="upper_bound", type="integer", nullable=true)
     */
    private $upperBound;

    /**
     * @var integer
     *
     * @ORM\Column(name="default_Value", type="integer", nullable=true)
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
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TResponseDeclaration", inversedBy="mapping")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_response_declaration_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTResponseDeclaration;

    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TMapEntry", mappedBy="fkTMapping",cascade={"persist"})
     **/
    private $mapEntry;

    public function __construct()
    {
        $this->mapEntry = new ArrayCollection();
    }

    /**
     * Get mapEntry
     *
     */
    public function getMapEntry()
    {
        return $this->mapEntry;
    }

    /**
     * Set mapEntry
     *
     */
    public function setMapEntry(\Autorentool\CoreBundle\Entity\TMapEntry $mapEntry)
    {
        $this->mapEntry[] = $mapEntry;
        $mapEntry->setFkTMapping($this);

        return $this;
    }

    /**
     * Get lowerBound
     *
     * @return integer
     */
    public function getLowerBound()
    {
        return $this->lowerBound;
    }

    /**
     * Set lowerBound
     *
     * @param integer $lowerBound
     *
     * @return TMapping
     */
    public function setLowerBound($lowerBound)
    {
        $this->lowerBound = $lowerBound;

        return $this;
    }

    /**
     * Get upperBound
     *
     * @return integer
     */
    public function getUpperBound()
    {
        return $this->upperBound;
    }

    /**
     * Set upperBound
     *
     * @param integer $upperBound
     *
     * @return TMapping
     */
    public function setUpperBound($upperBound)
    {
        $this->upperBound = $upperBound;

        return $this;
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
     * @return TMapping
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
     * @return TMapping
     */
    public function setFkTResponseDeclaration(\Autorentool\CoreBundle\Entity\TResponseDeclaration $fkTResponseDeclaration)
    {
        $this->fkTResponseDeclaration = $fkTResponseDeclaration;

        return $this;
    }
}
