<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TChoiceInteractionType
 *
 * @ORM\Table(name="t_choice_interaction", uniqueConstraints={@ORM\UniqueConstraint(name="fk_t_item_body_id", columns={"fk_t_item_body_id"})})
 * @ORM\Entity
 */
class TChoiceInteraction
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
     * @ORM\Column(name="shuffle", type="integer", nullable=true)
     */
    private $shuffle;

    /**
     * @var integer
     *
     * @ORM\Column(name="max_choices", type="integer", nullable=true)
     */
    private $maxChoices;

    /**
     * @var integer
     *
     * @ORM\Column(name="promt", type="integer", nullable=true)
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
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TItemBody", inversedBy="choiceInteraction")
     * @ORM\JoinColumn(name="fk_t_item_body_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $fkTItemBody;

    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TSimpleChoice", mappedBy="fkTChoiceInterection", cascade={"persist"})
     **/
    private $simpleChoices;

    public function __construct()
    {
        $this->simpleChoices = new ArrayCollection();
    }

    /**
     * Get categoryTags
     *
     */
    public function getSimpleChoices()
    {
        return $this->simpleChoices;
    }

    /**
     * Set categoryTags
     *
     */
    public function setSimpleChoices($simpleChoices)
    {
        $this->simpleChoices = $simpleChoices;
        foreach ($simpleChoices as $item) {
            $item->setFkTChoiceInterection($this);
        }
        return $this;
    }

    public function addSimpleChoices(\Autorentool\CoreBundle\Entity\TSimpleChoice $simpleChoices)
    {
        $simpleChoices->setFkTChoiceInterection($this);
        $this->simpleChoices->add($simpleChoices);
    }

    public function removeSimpleChoices(\Autorentool\CoreBundle\Entity\TSimpleChoice $simpleChoices)
    {
        $this->simpleChoices->removeElement($simpleChoices);
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
     * @return TChoiceInteraction
     */
    public function setResponseIdentifier($responseIdentifier)
    {
        $this->responseIdentifier = $responseIdentifier;

        return $this;
    }

    /**
     * Get shuffle
     *
     * @return integer
     */
    public function getShuffle()
    {
        return $this->shuffle;
    }

    /**
     * Get shuffle string
     *
     * @return string
     */
    public function getShuffleString()
    {
        return boolval($this->shuffle) ? 'true' : 'false';
    }

    /**
     * Set shuffle
     *
     * @param integer $shuffle
     *
     * @return TChoiceInteraction
     */
    public function setShuffle($shuffle)
    {
        $this->shuffle = $shuffle;

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
     * @return TChoiceInteraction
     */
    public function setMaxChoices($maxChoices)
    {
        $this->maxChoices = $maxChoices;

        return $this;
    }

    /**
     * Get promt
     *
     * @return integer
     */
    public function getPromt()
    {
        return $this->promt;
    }

    /**
     * Set promt
     *
     * @param integer $promt
     *
     * @return TChoiceInteraction
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
     * @return TChoiceInteraction
     */
    public function setFkTItemBody(\Autorentool\CoreBundle\Entity\TItemBody $fkTItemBody = null)
    {
        $this->fkTItemBody = $fkTItemBody;

        return $this;
    }
}
