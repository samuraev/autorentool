<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * THotspotInteraction
 *
 * @ORM\Table(name="t_hotspot_interaction", uniqueConstraints={@ORM\UniqueConstraint(name="fk_t_item_body_id", columns={"fk_t_item_body_id"})})
 * @ORM\Entity
 */
class THotspotInteraction
{
    /**
     * @var string
     *
     * @ORM\Column(name="response_identifier", type="text", length=65535, nullable=true)
     */
    private $responseIdentifier;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_choices", type="integer", nullable=true)
     */
    private $maxChoices;

    /**
     * @var string
     *
     * @ORM\Column(name="promt", type="text", length=65535, nullable=true)
     */
    private $promt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TItemBody
     *
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TItemBody", inversedBy="hotspotInteraction")
     * @ORM\JoinColumn(name="fk_t_item_body_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $fkTItemBody;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TOuterObject", mappedBy="fkTHotspotInteraction",cascade={"persist"})
     **/
    private $outerObject;

    /**
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TInnerObject", mappedBy="fkTHotspotInteraction",cascade={"persist"})
     **/
    private $innerObject;

    /**
     * Get outerObject
     *
     */
    public function getOuterObject()
    {
        return $this->outerObject;
    }

    /**
     * Set outerObject
     *
     */
    public function setOuterObject(\Autorentool\CoreBundle\Entity\TOuterObject $outerObject)
    {
        $this->outerObject = $outerObject;
        $outerObject->setFkTHotspotInteraction($this);

        return $this;
    }


    public function __construct()
    {
        $this->innerObject = new ArrayCollection();
    }

    /**
     * Get innerObject
     *
     */
    public function getInnerObject()
    {
        return $this->innerObject;
    }

    /**
     * Set outerObject
     *
     */
    public function setInnerObject(\Autorentool\CoreBundle\Entity\TInnerObject $innerObject)
    {
        $this->innerObject[] = $innerObject;
        $innerObject->setFkTHotspotInteraction($this);

        return $this;
    }

    /**
     * Get responseIdentifier
     *
     * @return string
     */
    public function getResponseIdentifier()
    {
        return $this->responseIdentifier;
    }

    /**
     * Set responseIdentifier
     *
     * @param string $responseIdentifier
     *
     * @return THotspotInteraction
     */
    public function setResponseIdentifier($responseIdentifier)
    {
        $this->responseIdentifier = $responseIdentifier;

        return $this;
    }

    /**
     * Get maxChoices
     *
     * @return integer
     */
    public function getMaxChoices()
    {
        return $this->maxChoices;
    }

    /**
     * Set maxChoices
     *
     * @param integer $maxChoices
     *
     * @return THotspotInteraction
     */
    public function setMaxChoices($maxChoices)
    {
        $this->maxChoices = $maxChoices;

        return $this;
    }

    /**
     * Get promt
     *
     * @return string
     */
    public function getPromt()
    {
        return $this->promt;
    }

    /**
     * Set promt
     *
     * @param string $promt
     *
     * @return THotspotInteraction
     */
    public function setPromt($promt)
    {
        $this->promt = $promt;

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
     * Get fkTItemBody
     *
     * @return \Autorentool\CoreBundle\Entity\TItemBody
     */
    public function getFkTItemBody()
    {
        return $this->fkTItemBody;
    }

    /**
     * Set fkTItemBody
     *
     * @param \Autorentool\CoreBundle\Entity\TItemBody $fkTItemBody
     *
     * @return THotspotInteraction
     */
    public function setFkTItemBody(\Autorentool\CoreBundle\Entity\TItemBody $fkTItemBody = null)
    {
        $this->fkTItemBody = $fkTItemBody;

        return $this;
    }


}
