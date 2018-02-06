<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TCell
 *
 * @ORM\Table(name="t_cell", indexes={@ORM\Index(name="fk_t_row_id", columns={"fk_t_row_id"})})
 * @ORM\Entity
 */
class TCell
{
    /**
     * @var string
     *
     * @ORM\Column(name="cell_identifier", type="text", length=65535, nullable=true)
     */
    private $cellIdentifier;

    /**
     * @var string
     *
     * @ORM\Column(name="column_identifier", type="text", length=65535, nullable=true)
     */
    private $columnIdentifier;

    /**
     * @var string
     *
     * @ORM\Column(name="row_identifier", type="text", length=65535, nullable=true)
     */
    private $rowIdentifier;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", length=65535, nullable=true)
     */
    private $value;

    /**
     * @var integer
     *
     * @ORM\Column(name="head", type="boolean", nullable=true)
     */
    private $head;

    /**
     * @var integer
     *
     * @ORM\Column(name="colspan", type="integer", nullable=true)
     */
    private $colspan;

    /**
     * @var integer
     *
     * @ORM\Column(name="writable", type="boolean", nullable=true)
     */
    private $writeable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TRow
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TRow", inversedBy="cell")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_row_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTRow;

    private $palCommands;


    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TDropZone",
     *     mappedBy="fkTCell", cascade={"persist"},
     *     orphanRemoval=true)
     **/
    private $dropZone;

    public function __construct()
    {
        $this->dropZone = new ArrayCollection();
    }

    /**
     * Get palCommands
     *
     */
    public function getPalCommands()
    {
        return $this->palCommands;
    }

    /**
     * Set palCommands
     *
     */
    public function setPalCommands($palCommands)
    {
        $this->palCommands = $palCommands;

        return $this;
    }

    /**
     * Get dropZone
     *
     */
    public function getdDropZone()
    {
        return $this->dropZone;
    }

    /**
     * Set dropZone
     *
     */
    public function setDropZone($dropZone)
    {
        $this->dropZone = $dropZone;
        foreach ($dropZone as $item) {
            $item->setFkTCell($this);
        }

        return $this;
    }

    public function addDropZone(\Autorentool\CoreBundle\Entity\TDropZone $dropZone)
    {
        $dropZone->setFkTCell($this);
        $this->dropZone->add($dropZone);
    }

    public function removeDropZone(\Autorentool\CoreBundle\Entity\TDropZone $dropZone)
    {
        $this->dropZone->removeElement($dropZone);
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
     * @return TCell
     */
    public function setCellIdentifier($cellIdentifier)
    {
        $this->cellIdentifier = $cellIdentifier;

        return $this;
    }

    /**
     * Get columnIdentifier
     *
     * @return string
     */
    public function getColumnIdentifier()
    {
        return $this->columnIdentifier;
    }

    /**
     * Set columnIdentifier
     *
     * @param string $columnIdentifier
     *
     * @return TCell
     */
    public function setColumnIdentifier($columnIdentifier)
    {
        $this->columnIdentifier = $columnIdentifier;

        return $this;
    }
    /**
     * Get rowIdentifier
     *
     * @return string
     */
    public function getRowIdentifier()
    {
        return $this->rowIdentifier;
    }

    /**
     * Set rowIdentifier
     *
     * @param string $rowIdentifier
     *
     * @return TCell
     */
    public function setRowIdentifier($rowIdentifier)
    {
        $this->rowIdentifier = $rowIdentifier;

        return $this;
    }

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
     * @return TCell
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get head
     *
     * @return bool
     */
    public function getHead()
    {
        return $this->head;
    }

    /**
     * Set head
     *
     * @param bool $head
     *
     * @return TCell
     */
    public function setHead($head)
    {
        $this->head = $head;

        return $this;
    }

    /**
     * Get head string
     *
     * @return string
     */
    public function getHeadString()
    {
        return boolval($this->head) ? 'true' : 'false';
    }

    /**
     * Get colspan
     *
     * @return integer
     */
    public function getColspan()
    {
        return $this->colspan;
    }

    /**
     * Set colspan
     *
     * @param integer $colspan
     *
     * @return TCell
     */
    public function setColspan($colspan)
    {
        $this->colspan = $colspan;

        return $this;
    }

    /**
     * Get writeable
     *
     * @return bool
     */
    public function getWriteable()
    {
        return $this->writeable;
    }

    /**
     * Set writable
     *
     * @param bool $writeable
     *
     * @return TCell
     */
    public function setWriteable($writeable)
    {
        $this->writeable = $writeable;

        return $this;
    }

    /**
     * Get head string
     *
     * @return string
     */
    public function getWriteableString()
    {
        return boolval($this->writeable) ? 'true' : 'false';
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
     * Get fkTRow
     *
     * @return \Autorentool\CoreBundle\Entity\TRow
     */
    public function getFkTRow()
    {
        return $this->fkTRow;
    }

    /**
     * Set fkTRow
     *
     * @param \Autorentool\CoreBundle\Entity\TRow $fkTRow
     *
     * @return TCell
     */
    public function setFkTRow(\Autorentool\CoreBundle\Entity\TRow $fkTRow = null)
    {
        $this->fkTRow = $fkTRow;

        return $this;
    }
}
