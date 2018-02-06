<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TResponseDeclaration
 *
 * @ORM\Table(name="t_response_declaration", indexes={@ORM\Index(name="fk_t_assessment_item_id", columns={"fk_t_assessment_item_id"})})
 * @ORM\Entity
 */
class TResponseDeclaration
{
    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="text", length=65535, nullable=true)
     */
    private $identifier;

    /**
     * @var string
     *
     * @ORM\Column(name="cardinality", type="text", length=65535, nullable=true)
     */
    private $cardinality;

    /**
     * @var string
     *
     * @ORM\Column(name="base_type", type="text", length=65535, nullable=true)
     */
    private $baseType;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TAssessmentItem
     *
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TAssessmentItem", inversedBy="responseDeclaration")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_assessment_item_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTAssessmentItem;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TCorrectResponse", mappedBy="fkTResponseDeclaration",cascade={"persist"})
     **/
    private $correctResponce;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TMapping", mappedBy="fkTResponseDeclaration", cascade={"persist"})
     **/
    private $mapping;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TAreaMapping", mappedBy="fkTResponseDeclaration",cascade={"persist"})
     **/
    private $areaMapping;

    /**
     * Get areaMapping
     *
     */
    public function getAreaMapping()
    {
        return $this->areaMapping;
    }

    /**
     * Set areaMapping
     *
     */
    public function setAreaMapping(\Autorentool\CoreBundle\Entity\TAreaMapping $areaMapping)
    {
        $this->areaMapping = $areaMapping;
        $areaMapping->setFkTResponseDeclaration($this);

        return $this;
    }

    /**
     * Get mapping
     *
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * Set mapping
     *
     */
    public function setMapping(\Autorentool\CoreBundle\Entity\TMapping $mapping)
    {
        $this->mapping = $mapping;
        $mapping->setFkTResponseDeclaration($this);

        return $this;
    }

    /**
     * Get correctResponce
     *
     */
    public function getCorrectResponce()
    {
        return $this->correctResponce;
    }

    /**
     * Set correctResponce
     *
     */
    public function setCorrectResponce(\Autorentool\CoreBundle\Entity\TCorrectResponse $correctResponce)
    {
        $this->correctResponce = $correctResponce;
        $correctResponce->setFkTResponseDeclaration($this);

        return $this;
    }

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
     * @return TResponseDeclaration
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get cardinality
     *
     * @return string
     */
    public function getCardinality()
    {
        return $this->cardinality;
    }

    /**
     * Set cardinality
     *
     * @param string $cardinality
     *
     * @return TResponseDeclaration
     */
    public function setCardinality($cardinality)
    {
        $this->cardinality = $cardinality;

        return $this;
    }

    /**
     * Get baseType
     *
     * @return string
     */
    public function getBaseType()
    {
        return $this->baseType;
    }

    /**
     * Set baseType
     *
     * @param string $baseType
     *
     * @return TResponseDeclaration
     */
    public function setBaseType($baseType)
    {
        $this->baseType = $baseType;

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
     * Get fkTAssessmentItem
     *
     * @return \Autorentool\CoreBundle\Entity\TAssessmentItem
     */
    public function getFkTAssessmentItem()
    {
        return $this->fkTAssessmentItem;
    }

    /**
     * Set fkTAssessmentItem
     *
     * @param \Autorentool\CoreBundle\Entity\TAssessmentItem $fkTAssessmentItem
     *
     * @return TResponseDeclaration
     */
    public function setFkTAssessmentItem(\Autorentool\CoreBundle\Entity\TAssessmentItem $fkTAssessmentItem = null)
    {
        $this->fkTAssessmentItem = $fkTAssessmentItem;

        return $this;
    }
}
