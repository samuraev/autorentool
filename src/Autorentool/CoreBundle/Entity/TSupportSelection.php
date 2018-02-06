<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * TSupportSelection
 *
 * @ORM\Table(name="t_support_selection", uniqueConstraints={@ORM\UniqueConstraint(name="fk_t_support_id", columns={"fk_t_support_id"})})
 * @ORM\Entity
 */
class TSupportSelection
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
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TSupport", inversedBy="supportSelection")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_support_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTSupport;

    /**
     *
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TSelectionItem", mappedBy="fkTSupportSelection", cascade={"persist"})
     **/
    private $selectionItem;

    private $currentSelection;

    public function __construct()
    {
        $this->selectionItem = new ArrayCollection();
    }

    /**
     * Get curentCategory
     *
     */
    public function getCurrentSelection()
    {
        return $this->currentSelection;
    }

    /**
     * Set curentCategory
     *
     */
    public function setCurrentSelection($currentSelection)
    {
        $this->currentSelection = $currentSelection;

        return $this;
    }

    /**
     * Get supportItem
     *
     */
    public function getSelectionItem()
    {
        return $this->selectionItem;
    }

    /**
     * Set supportItem
     *
     */
    public function setSelectionItem($selectionItem)
    {
        $this->selectionItem = $selectionItem;
        foreach ($selectionItem as $item) {
            $item->setFkTSupportSelection($this);
        }

        return $this;
    }

    public function addSelectionItem(\Autorentool\CoreBundle\Entity\TSelectionItem $selectionItem)
    {
        $selectionItem->setFkTSupportSelection($this);
        $this->selectionItem->add($selectionItem);
    }

    public function removeSelectionItem(\Autorentool\CoreBundle\Entity\TSelectionItem $selectionItem)
    {
        $this->selectionItem->removeElement($selectionItem);
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
     * @return TSupportSelection
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
     * @return TSupportSelection
     */
    public function setFkTSupport(\Autorentool\CoreBundle\Entity\TSupport $fkTSupport = null)
    {
        $this->fkTSupport = $fkTSupport;

        return $this;
    }
}
