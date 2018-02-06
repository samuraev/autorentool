<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TTaskPackages
 *
 * @ORM\Table(name="t_task_packages")
 * @ORM\Entity
 */
class TTaskPackages
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="text", length=65535, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="factory_name", type="text", nullable=false)
     */
    private $factoryName;

    /**
     * @var string
     *
     * @ORM\Column(name="creation_timestamp", type="text", length=65535, nullable=true)
     */
    private $creationTimestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="tittle", type="text", length=65535, nullable=true)
     */
    private $tittle;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

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
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return TTaskPackages
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get factory name
     *
     * @return string
     */
    public function getFactoryName()
    {
        return $this->factoryName;
    }

    /**
     * Set factory name
     *
     * @param string $factoryName
     *
     * @return TTaskPackages
     */
    public function setFactoryName($factoryName)
    {
        $this->factoryName = $factoryName;

        return $this;
    }

    /**
     * Get creationTimestamp
     *
     * @return string
     */
    public function getCreationTimestamp()
    {
        return $this->creationTimestamp;
    }

    /**
     * Set creationTimestamp
     *
     * @param string $creationTimestamp
     *
     * @return TTaskPackages
     */
    public function setCreationTimestamp($creationTimestamp)
    {
        $this->creationTimestamp = $creationTimestamp;

        return $this;
    }

    /**
     * Get tittle
     *
     * @return string
     */
    public function getTittle()
    {
        return $this->tittle;
    }

    /**
     * Set tittle
     *
     * @param string $tittle
     *
     * @return TTaskPackages
     */
    public function setTittle($tittle)
    {
        $this->tittle = $tittle;

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
     * @return TTaskPackages
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }
}

