<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TValue
 *
 * @ORM\Table(name="t_value", indexes={@ORM\Index(name="fk_t_correct_response_id", columns={"fk_t_correct_response_id"})})
 * @ORM\Entity
 */
class TValue
{
    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", length=65535, nullable=true)
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="value2", type="text", length=65535, nullable=true)
     */
    private $value2;

    /**
     * @var string
     *
     * @ORM\Column(name="cell_identifier", type="text", length=65535, nullable=true)
     */
    private $cellIdentifier;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TCorrectResponse
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TCorrectResponse", inversedBy="values")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_correct_response_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTCorrectResponse;


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
     * @return TValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value2
     *
     * @return string
     */
    public function getValue2()
    {
        return $this->value2;
    }

    /**
     * Set value2
     *
     * @param string $value2
     *
     * @return TValue
     */
    public function setValue2($value2)
    {
        $this->value2 = $value2;

        return $this;
    }

    /**
     * Get cellIdentifier
     *
     * @return string
     */
    public function getCellIdentifier()
    {
        return $this->cellIdentifier;
    }

    /**
     * Set cellIdentifier
     *
     * @param string $cellIdentifier
     *
     * @return TValue
     */
    public function setCellIdentifier($cellIdentifier)
    {
        $this->cellIdentifier = $cellIdentifier;

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
     * Get fkTCorrectResponse
     *
     * @return \Autorentool\CoreBundle\Entity\TCorrectResponse
     */
    public function getFkTCorrectResponse()
    {
        return $this->fkTCorrectResponse;
    }

    /**
     * Set fkTCorrectResponse
     *
     * @param \Autorentool\CoreBundle\Entity\TCorrectResponse $fkTCorrectResponse
     *
     * @return TValue
     */
    public function setFkTCorrectResponse(\Autorentool\CoreBundle\Entity\TCorrectResponse $fkTCorrectResponse = null)
    {
        $this->fkTCorrectResponse = $fkTCorrectResponse;

        return $this;
    }
}
