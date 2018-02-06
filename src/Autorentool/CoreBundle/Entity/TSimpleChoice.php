<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TSimpleChoice
 *
 * @ORM\Table(name="t_simple_choice", indexes={@ORM\Index(name="fk_t_choice_interection_id", columns={"fk_t_choice_interection_id"})})
 * @ORM\Entity
 */
class TSimpleChoice
{
    protected $isrightanswer;

    public function getIsrightanswer()
    {
        return $this->isrightanswer;
    }

    public function setIsrightanswer($isrightanswer)
    {
        $this->isrightanswer = $isrightanswer;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="text", length=65535, nullable=true)
     */
    private $identifier;

    /**
     * @var string
     *
     * @ORM\Column(name="caption", type="text", length=65535, nullable=true)
     */
    private $caption;

    /**
     * @var string
     *
     * @ORM\Column(name="img_src", type="text", length=65535, nullable=true)
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes={ "image/jpg", "image/png", "image/jpeg" },
     *     mimeTypesMessage = "Please upload a valid PDF"
     * )
     */
    private $imgSrc;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TChoiceInteraction
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TChoiceInteraction", inversedBy="simpleChoices")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_choice_interection_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTChoiceInterection;

    private $removeImgSrcState;

    /**
     * Get state of remove image by edit
     *
     * @return bool
     */
    public function getRemoveImgSrcState()
    {
        return $this->removeImgSrcState;
    }

    /**
     * Set state of remove image by edit
     *
     * @param bool $removeImgSrcState
     *
     * @return TSimpleChoice
     */
    public function setRemoveImgSrcState($removeImgSrcState)
    {
        $this->removeImgSrcState = $removeImgSrcState;

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
     * @return TSimpleChoice
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get caption
     *
     * @return string
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Set caption
     *
     * @param string $caption
     *
     * @return TSimpleChoice
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Get imgSrc
     *
     * @return string
     */
    public function getImgSrc()
    {
        return $this->imgSrc;
    }

    /**
     * Set imgSrc
     *
     * @param string $imgSrc
     *
     * @return TSimpleChoice
     */
    public function setImgSrc($imgSrc)
    {
        $this->imgSrc = $imgSrc;

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
     * Get fkTChoiceInterection
     *
     * @return \Autorentool\CoreBundle\Entity\TChoiceInteraction
     */
    public function getFkTChoiceInterection()
    {
        return $this->fkTChoiceInterection;
    }

    /**
     * Set fkTChoiceInterection
     *
     * @param \Autorentool\CoreBundle\Entity\TChoiceInteraction $fkTChoiceInterection
     *
     * @return TSimpleChoice
     */
    public function setFkTChoiceInterection(\Autorentool\CoreBundle\Entity\TChoiceInteraction $fkTChoiceInterection = null)
    {
        $this->fkTChoiceInterection = $fkTChoiceInterection;

        return $this;
    }
}
