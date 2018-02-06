<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TPalcommandGroups
 *
 * @ORM\Table(name="t_palcommand_groups", indexes={@ORM\Index(name="fk_t_palcommand_types", columns={"fk_t_palcommand_types"})})
 * @ORM\Entity
 */
class TPalcommandGroups
{
    /**
     * @var string
     *
     * @ORM\Column(name="group_name", type="text", length=65535, nullable=false)
     */
    private $groupName;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TPalcommands", mappedBy="fkTPalcommandGroups",cascade={"persist"})
     **/
    private $commands;

    /**
     * @var \Autorentool\CoreBundle\Entity\TPalcommandTypes
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TPalcommandTypes", inversedBy="commandGroups")
     * @ORM\JoinColumn(name="fk_t_palcommand_types", referencedColumnName="id", onDelete="CASCADE")
     */
    private $fkTPalcommandTypes;

    public function __construct()
    {
        $this->commands = new ArrayCollection();
    }

    /**
     * Get categoryTags
     *
     */
    public function getCommands()
    {
        return $this->commands;
    }

    /**
     * Set categoryTags
     *
     */
    public function setCommands(\Autorentool\CoreBundle\Entity\TPalcommands $commands)
    {
        $this->commands[] = $commands;
        $commands->setFkTPalcommandGroups($this);

        return $this;
    }

    /**
     * Get command
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     * Set command
     *
     * @param string $groupName
     *
     * @return TPalcommandGroups
     */
    public function setCommand($groupName)
    {
        $this->groupName = $groupName;

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
     * Get fkTPalcommandTypes
     *
     * @return \Autorentool\CoreBundle\Entity\TPalcommandTypes
     */
    public function getFkTPalcommandTypes()
    {
        return $this->fkTPalcommandTypes;
    }

    /**
     * Set fkTPalcommandTypes
     *
     * @param \Autorentool\CoreBundle\Entity\TPalcommandTypes $fkTPalcommandTypes
     *
     * @return TPalcommandGroups
     */
    public function setFkTPalcommandTypes(\Autorentool\CoreBundle\Entity\TPalcommandTypes $fkTPalcommandTypes)
    {
        $this->fkTPalcommandTypes = $fkTPalcommandTypes;

        return $this;
    }
}
