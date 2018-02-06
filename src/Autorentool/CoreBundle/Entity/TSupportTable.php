<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TSupportTable
 *
 * @ORM\Table(name="t_support_table", uniqueConstraints={@ORM\UniqueConstraint(name="fk_t_support_id", columns={"fk_t_support_id"})})
 * @ORM\Entity
 */
class TSupportTable
{
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
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TSupport", inversedBy="supportTable")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_support_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTSupport;

    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TRow",
     *     mappedBy="fkTSupportTable", cascade={"persist"},
     *     orphanRemoval=true)
     **/
    private $row;

    public function __construct()
    {
        $this->row = new ArrayCollection();
    }

    /**
     * Get row
     *
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * Set row
     *
     */
    public function setRow($row)
    {
        $this->row = $row;
        foreach ($row as $item) {
            $item->setFkTSupportTable($this);
        }

        return $this;
    }

    public function addRow(\Autorentool\CoreBundle\Entity\TRow $row)
    {
        $row->setFkTSupportTable($this);
        $this->row->add($row);
    }

    public function removeRow(\Autorentool\CoreBundle\Entity\TRow $row)
    {
        $this->row->removeElement($row);
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
     * @return TSupportTable
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
     * @return TSupportTable
     */
    public function setFkTSupport(\Autorentool\CoreBundle\Entity\TSupport $fkTSupport = null)
    {
        $this->fkTSupport = $fkTSupport;

        return $this;
    }
}