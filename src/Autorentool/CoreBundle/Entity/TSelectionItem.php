<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TSelectionItem
 *
 * @ORM\Table(name="t_selection_item", indexes={@ORM\Index(name="fk_t_support_selection_id", columns={"fk_t_support_selection_id"})})
 * @ORM\Entity
 */
class TSelectionItem
{
    /**
     * @var string
     *
     * @ORM\Column(name="select_value", type="text", length=65535, nullable=true)
     */
    private $selectValue;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TSupportSelection
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TSupportSelection", inversedBy="selectionItem")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_support_selection_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTSupportSelection;


    /**
     * Get selectValue
     *
     * @return string
     */
    public function getSelectValue()
    {
        return $this->selectValue;
    }

    /**
     * Set selectValue
     *
     * @param string $selectValue
     *
     * @return TSelectionItem
     */
    public function setSelectValue($selectValue)
    {
        $this->selectValue = $selectValue;

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
     * Get fkTSupportSelection
     *
     * @return \Autorentool\CoreBundle\Entity\TSupportSelection
     */
    public function getFkTSupportSelection()
    {
        return $this->fkTSupportSelection;
    }

    /**
     * Set fkTSupportSelection
     *
     * @param \Autorentool\CoreBundle\Entity\TSupportSelection $fkTSupportSelection
     *
     * @return TSelectionItem
     */
    public function setFkTSupportSelection(\Autorentool\CoreBundle\Entity\TSupportSelection $fkTSupportSelection = null)
    {
        $this->fkTSupportSelection = $fkTSupportSelection;

        return $this;
    }
}
