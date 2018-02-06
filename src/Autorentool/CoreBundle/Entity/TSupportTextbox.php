<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TSupportTextbox
 *
 * @ORM\Table(name="t_support_textbox", indexes={@ORM\Index(name="fk_t_support_id", columns={"fk_t_support_id"})})
 * @ORM\Entity
 */
class TSupportTextbox
{
    /**
     * @var string
     *
     * @ORM\Column(name="textbox_content", type="text", length=65535, nullable=true)
     */
    private $textboxContent;

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
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TSupport", inversedBy="supportTextbox")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_support_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTSupport;

    /**
     * Get textboxContent
     *
     * @return string
     */
    public function getTextboxContent()
    {
        return $this->textboxContent;
    }

    /**
     * Set textboxContent
     *
     * @param string $textboxContent
     *
     * @return TSupportTextbox
     */
    public function setTextboxContent($textboxContent)
    {
        $this->textboxContent = $textboxContent;

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
     * @return TSupportTextbox
     */
    public function setFkTSupport(\Autorentool\CoreBundle\Entity\TSupport $fkTSupport = null)
    {
        $this->fkTSupport = $fkTSupport;

        return $this;
    }
}
