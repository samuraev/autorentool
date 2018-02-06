<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * TCorrectResponse
 *
 * @ORM\Table(name="t_correct_response", uniqueConstraints={@ORM\UniqueConstraint(name="fk_t_response_declaration_id", columns={"fk_t_response_declaration_id"})})
 * @ORM\Entity
 */
class TCorrectResponse
{
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
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TResponseDeclaration", inversedBy="correctResponce")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_response_declaration_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTResponseDeclaration;

    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TValue", mappedBy="fkTCorrectResponse",cascade={"persist"})
     **/
    private $values;

    public function __construct()
    {
        $this->values = new ArrayCollection();
    }

    /**
     * Get values
     *
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set values
     *
     */
    public function setValues(\Autorentool\CoreBundle\Entity\TValue $values)
    {
        $this->values[] = $values;
        $values->setFkTCorrectResponse($this);

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
     * @return TCorrectResponse
     */
    public function setFkTResponseDeclaration(\Autorentool\CoreBundle\Entity\TResponseDeclaration $fkTResponseDeclaration)
    {
        $this->fkTResponseDeclaration = $fkTResponseDeclaration;

        return $this;
    }
}
