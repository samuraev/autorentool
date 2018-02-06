<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * TDragInteraction
 *
 * @ORM\Table(name="t_drag_interaction", uniqueConstraints={@ORM\UniqueConstraint(name="fk_t_item_body_id", columns={"fk_t_item_body_id"})})
 * @ORM\Entity
 */
class TDragInteraction
{
    /**
     * @var string
     *
     * @ORM\Column(name="prompt", type="text", length=65535, nullable=true)
     */
    private $prompt;

    /**
     * @var string
     *
     * @ORM\Column(name="mode", type="text", length=65535, nullable=true)
     */
    private $mode;

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
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TItemBody", inversedBy="dragInteraction")
     * @ORM\JoinColumn(name="fk_t_item_body_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $fkTItemBody;

    /**
     *
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TTable", mappedBy="fkTDragInteraction", cascade={"persist"})
     **/
    private $dragTable;

    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TColumnIdentifier",
     *     mappedBy="fkTDragInteraction", cascade={"persist"},
     *     orphanRemoval=true)
     **/
    private $columnIdentifier;

    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TRowIdentifier",
     *     mappedBy="fkTDragInteraction", cascade={"persist"},
     *     orphanRemoval=true)
     **/
    private $rowIdentifier;

    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TDragItem",
     *     mappedBy="fkTDragInteraction", cascade={"persist"},
     *     orphanRemoval=true)
     **/
    private $dragItem;

    public function __construct()
    {
        $this->columnIdentifier = new ArrayCollection();
        $this->rowIdentifier = new ArrayCollection();
        $this->dragItem = new ArrayCollection();
    }

    /**
     * Get dragItem
     *
     */
    public function getDragItem()
    {
        return $this->dragItem;
    }

    /**
     * Set dragItem
     *
     */
    public function setDragItem($dragItem)
    {
        $this->dragItem = $dragItem;
        foreach ($dragItem as $item) {
            $item->setFkTDragInteraction($this);
        }

        return $this;
    }

    public function addDragItem(\Autorentool\CoreBundle\Entity\TDragItem $dragItem)
    {
        $dragItem->setFkTDragInteraction($this);
        $this->dragItem->add($dragItem);
    }

    public function removeDragItem(\Autorentool\CoreBundle\Entity\TDragItem $dragItem)
    {
        $this->dragItem->removeElement($dragItem);
    }

    /**
     * Get columnIdentifier
     *
     */
    public function getColumnIdentifier()
    {
        return $this->columnIdentifier;
    }

    /**
     * Set columnIdentifier
     *
     */
    public function setColumnIdentifier($columnIdentifier)
    {
        $this->columnIdentifier = $columnIdentifier;
        foreach ($columnIdentifier as $item) {
            $item->setFkTDragInteraction($this);
        }

        return $this;
    }

    public function addColumnIdentifier(\Autorentool\CoreBundle\Entity\TColumnIdentifier $columnIdentifier)
    {
        $columnIdentifier->setFkTDragInteraction($this);
        $this->columnIdentifier->add($columnIdentifier);
    }

    public function removeColumnIdentifier(\Autorentool\CoreBundle\Entity\TColumnIdentifier $columnIdentifier)
    {
        $this->columnIdentifier->removeElement($columnIdentifier);
    }

    /**
     * Get rowIdentifier
     *
     */
    public function getRowIdentifier()
    {
        return $this->rowIdentifier;
    }

    /**
     * Set rowIdentifier
     *
     */
    public function setRowIdentifier($rowIdentifier)
    {
        $this->rowIdentifier = $rowIdentifier;
        foreach ($rowIdentifier as $item) {
            $item->setFkTDragInteraction($this);
        }

        return $this;
    }

    public function addRowIdentifier(\Autorentool\CoreBundle\Entity\TRowIdentifier $rowIdentifier)
    {
        $rowIdentifier->setFkTDragInteraction($this);
        $this->rowIdentifier->add($rowIdentifier);
    }

    public function removeRowIdentifier(\Autorentool\CoreBundle\Entity\TRowIdentifier $rowIdentifier)
    {
        $this->rowIdentifier->removeElement($rowIdentifier);
    }

    /**
     * Get table for drag
     *
     */
    public function getDragTable()
    {
        return $this->dragTable;
    }

    /**
     * Set table for drag
     *
     */
    public function setDragTable($dragTable)
    {
        $this->dragTable = $dragTable;
        $dragTable->setFkTDragInteraction($this);

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
     * @return TDragInteraction
     */
    public function setPrompt($prompt)
    {
        $this->prompt = $prompt;

        return $this;
    }

    /**
     * Get mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set mode
     *
     * @param string $mode
     *
     * @return TDragInteraction
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

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
     * @return TDragInteraction
     */
    public function setFkTItemBody(\Autorentool\CoreBundle\Entity\TItemBody $fkTItemBody = null)
    {
        $this->fkTItemBody = $fkTItemBody;

        return $this;
    }
}
