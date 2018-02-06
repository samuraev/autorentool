<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TPalcommandTypes
 *
 * @ORM\Table(name="t_palcommand_types")
 * @ORM\Entity
 */
class TPalcommandTypes
{
    /**
     * @var string
     *
     * @ORM\Column(name="type_name", type="text", length=65535, nullable=false)
     */
    private $typeName;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TPalcommandGroups", mappedBy="fkTPalcommandTypes",cascade={"persist"})
     **/
    private $commandGroups;

    public function __construct()
    {
        $this->commandGroups = new ArrayCollection();
    }

    /**
     * Get categoryTags
     *
     */
    public function getCommandGroups()
    {
        return $this->commandGroups;
    }

    /**
     * Set categoryTags
     *
     */
    public function setCommandGroups(\Autorentool\CoreBundle\Entity\TPalcommandGroups $commandGroups)
    {
        $this->commandGroups[] = $commandGroups;
        $commandGroups->setFkTPalcommandTypes($this);

        return $this;
    }

    /**
     * Get command
     *
     * @return string
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * Set command
     *
     * @param string $typeName
     *
     * @return TPalcommandTypes
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;

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
}
