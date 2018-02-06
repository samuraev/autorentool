<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
/**
 * TTableInteraction
 *
 * @ORM\Table(name="t_table_interaction", uniqueConstraints={@ORM\UniqueConstraint(name="fk_t_item_body_id", columns={"fk_t_item_body_id"})})
 * @ORM\Entity
 */
class TTableInteraction
{
    /**
     * @var string
     *
     * @ORM\Column(name="response_identifier", type="text", length=65535, nullable=true)
     */
    private $responseIdentifier;

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
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TItemBody", inversedBy="tableInteraction")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_item_body_id", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTItemBody;

    /**
     *
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TTable", mappedBy="fkTTableInteraction", cascade={"persist"})
     **/
    private $table;

    /**
     * Get table
     *
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Set table
     *
     */
    public function setTable($table)
    {
        $this->table = $table;
        $table->setFkTTableInteraction($this);

        return $this;
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
     * @return TTableInteraction
     */
    public function setResponseIdentifier($responseIdentifier)
    {
        $this->responseIdentifier = $responseIdentifier;

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
     * @return TTableInteraction
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
     * @return TTableInteraction
     */
    public function setFkTItemBody(\Autorentool\CoreBundle\Entity\TItemBody $fkTItemBody = null)
    {
        $this->fkTItemBody = $fkTItemBody;

        return $this;
    }
}
