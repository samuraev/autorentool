<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TPalcommands
 *
 * @ORM\Table(name="t_palcommands", indexes={@ORM\Index(name="fk_t_palcommand_groups", columns={"fk_t_palcommand_groups"})})
 * @ORM\Entity
 */
class TPalcommands
{
    /**
     * @var string
     *
     * @ORM\Column(name="command_name", type="text", length=65535, nullable=false)
     */
    private $commandName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Autorentool\CoreBundle\Entity\TPalcommandGroups
     *
     * @ORM\ManyToOne(targetEntity="Autorentool\CoreBundle\Entity\TPalcommandGroups", inversedBy="commands")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fk_t_palcommand_groups", referencedColumnName="id", onDelete="CASCADE")
     * })
     */
    private $fkTPalcommandGroups;

    /**
     * Get command
     *
     * @return string
     */
    public function getCommandName()
    {
        return $this->commandName;
    }

    /**
     * Set command
     *
     * @param string $command
     *
     * @return TPalcommands
     */
    public function setCommandName($commandName)
    {
        $this->commandName = $commandName;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return TPalcommands
     */
    public function setDescription($description)
    {
        $this->description = $description;

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
     * Get fkTPalcommandGroups
     *
     * @return \Autorentool\CoreBundle\Entity\TPalcommandGroups
     */
    public function getFkTPalcommandGroups()
    {
        return $this->fkTPalcommandGroups;
    }

    /**
     * Set fkTPalcommandGroups
     *
     * @param \Autorentool\CoreBundle\Entity\TPalcommandGroups $fkTPalcommandGroups
     *
     * @return TPalcommands
     */
    public function setFkTPalcommandGroups(\Autorentool\CoreBundle\Entity\TPalcommandGroups $fkTPalcommandGroups)
    {
        $this->fkTPalcommandGroups = $fkTPalcommandGroups;

        return $this;
    }
}
