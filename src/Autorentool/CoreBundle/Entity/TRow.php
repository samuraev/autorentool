<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * TRow
 *
 * @ORM\Table(name="t_row", indexes={@ORM\Index(name="fk_t_table_id", columns={"fk_t_table_id"}), @ORM\Index(name="fk_t_support_table_id", columns={"fk_t_support_table_id"})})
 * @ORM\Entity
 */
class TRow
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rowspan", type="integer", nullable=true)
     */
    private $rowspan;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TTable
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TTable", inversedBy="row")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_table_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTTable;

    /**
     * @var \Autorentool\CoreBundle\Entity\TSupportTable
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TSupportTable", inversedBy="row")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_support_table_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTSupportTable;

    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TCell",
     *     mappedBy="fkTRow", cascade={"persist"},
     *     orphanRemoval=true)
     **/
    private $cell;

    public function __construct()
    {
        $this->cell = new ArrayCollection();
    }

    /**
     * Get row
     *
     */
    public function getCell()
    {
        return $this->cell;
    }

    /**
     * Set row
     *
     */
    public function setCell($cell)
    {
        $this->cell = $cell;
        foreach ($cell as $item) {
            $item->setFkTRow($this);
        }

        return $this;
    }

    public function addCell(\Autorentool\CoreBundle\Entity\TCell $cell)
    {
        $cell->setFkTRow($this);
        $this->cell->add($cell);
    }

    public function removeCell(\Autorentool\CoreBundle\Entity\TCell $cell)
    {
        $this->cell->removeElement($cell);
    }

    /**
     * Get rowspan
     *
     * @return integer
     */
    public function getRowspan()
    {
        return $this->rowspan;
    }

    /**
     * Set rowspan
     *
     * @param integer $rowspan
     *
     * @return TRow
     */
    public function setRowspan($rowspan)
    {
        $this->rowspan = $rowspan;

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
     * Get fkTTable
     *
     * @return \Autorentool\CoreBundle\Entity\TTable
     */
    public function getFkTTable()
    {
        return $this->fkTTable;
    }

    /**
     * Set fkTTable
     *
     * @param \Autorentool\CoreBundle\Entity\TTable $fkTTable
     *
     * @return TRow
     */
    public function setFkTTable(\Autorentool\CoreBundle\Entity\TTable $fkTTable = null)
    {
        $this->fkTTable = $fkTTable;

        return $this;
    }

    /**
     * Get fkTSupportTable
     *
     * @return \Autorentool\CoreBundle\Entity\TSupportTable
     */
    public function getFkTSupportTable()
    {
        return $this->fkTSupportTable;
    }

    /**
     * Set fkTTable
     *
     * @param \Autorentool\CoreBundle\Entity\TSupportTable $fkTSupportTable
     *
     * @return TRow
     */
    public function setFkTSupportTable(\Autorentool\CoreBundle\Entity\TSupportTable $fkTSupportTable = null)
    {
        $this->fkTSupportTable = $fkTSupportTable;

        return $this;
    }
}
