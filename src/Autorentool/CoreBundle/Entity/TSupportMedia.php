<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TSupportMedia
 *
 * @ORM\Table(name="t_support_media", indexes={@ORM\Index(name="fk_t_support_id", columns={"fk_t_support_id"})})
 * @ORM\Entity
 */
class TSupportMedia
{

    /**
     * @var string
     *
     * @ORM\Column(name="media_source", type="text", length=65535, nullable=true)
     */
    private $mediaSource;

    /**
     * @var string
     *
     * @ORM\Column(name="media_origname", type="text", length=65535, nullable=true)
     */
    private $mediaSourceOrigName;

    /**
     * @var string
     *
     * @ORM\Column(name="prompt", type="text", length=65535, nullable=true)
     */
    private $prompt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TSupport
     *
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TSupport", inversedBy="supportMedia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_support_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTSupport;

    /**
     * Get mediaSourceOrigName
     *
     * @return string
     */
    public function getMediaSourceOrigName()
    {
        return $this->mediaSourceOrigName;
    }

    /**
     * Set mediaSourceOrigName
     *
     * @param string $mediaSourceOrigName
     *
     * @return TSupportMedia
     */
    public function setMediaSourceOrigName($mediaSourceOrigName)
    {
        $this->mediaSourceOrigName = $mediaSourceOrigName;

        return $this;
    }

    /**
     * Get mediaSource
     *
     * @return string
     */
    public function getMediaSource()
    {
        return $this->mediaSource;
    }

    /**
     * Set mediaSource
     *
     * @param string $mediaSource
     *
     * @return TSupportMedia
     */
    public function setMediaSource($mediaSource)
    {
        $this->mediaSource = $mediaSource;

        return $this;
    }

    /**
     * Get prompt
     *
     * @return string
     */
    public function getPrompt()
    {
        return $this->prompt;
    }

    /**
     * Set prompt
     *
     * @param string $prompt
     *
     * @return TSupportMedia
     */
    public function setPrompt($prompt)
    {
        $this->prompt = $prompt;

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
     * Get fkTSupport
     *
     * @return \Autorentool\CoreBundle\Entity\TSupport
     */
    public function getFkTSupport()
    {
        return $this->fkTSupport;
    }

    /**
     * Set fkTSupport
     *
     * @param \Autorentool\CoreBundle\Entity\TSupport $fkTSupport
     *
     * @return TSupportMedia
     */
    public function setFkTSupport(\Autorentool\CoreBundle\Entity\TSupport $fkTSupport = null)
    {
        $this->fkTSupport = $fkTSupport;

        return $this;
    }
}
