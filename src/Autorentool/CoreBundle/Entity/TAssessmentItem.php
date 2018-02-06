<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * TAssessmentItem
 *
 * @ORM\Table(name="t_assessment_item", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})})
 * @ORM\Entity
 */
class TAssessmentItem
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
     * @ORM\OneToMany(targetEntity="Autorentool\CoreBundle\Entity\TCategoryTags", mappedBy="fkTAssessmentItem",cascade={"persist"})
     **/
    private $categoryTags;

    /**
     * @var string
     *
     * @ORM\Column(name="creation_timestamp", type="text", length=65535, nullable=true)
     */
    private $creationTimestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="text", length=65535, nullable=true)
     */
    private $identifier;

    /**
     * @var string
     *
     * @ORM\Column(name="tittle", type="text", length=65535, nullable=true)
     */
    private $tittle;

    /**
     * @var integer
     *
     * @ORM\Column(name="adaptive", type="integer", nullable=true)
     */
    private $adaptive;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="boolean", nullable=true)
     */
    private $stateOfTask;

    /**
     * @var string
     *
     * @ORM\Column(name="factory_name", type="text", nullable=false)
     */
    private $factoryName;

    /**
     * @var integer
     *
     * @ORM\Column(name="time_dependent", type="integer", nullable=true)
     */
    private $timeDependent;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TItemBody", mappedBy="fkTAssessmentItem",cascade={"persist"})
     **/
    private $itemBody;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TRelated", mappedBy="fkTAssessmentItem",cascade={"persist"})
     **/
    private $related;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TResponseDeclaration", mappedBy="fkTAssessmentItem",cascade={"persist"})
     *
     **/
    private $responseDeclaration;

    /**
     * @ORM\OneToOne(targetEntity="Autorentool\CoreBundle\Entity\TSupport", mappedBy="fkTAssessmentItem",cascade={"persist"})
     **/
    private $support;

    private $currentCategory;

    public function __construct()
    {
        $this->categoryTags = new ArrayCollection();
    }

    /**
     * Get curentCategory
     *
     */
    public function getCurrentCategory()
    {
        return $this->currentCategory;
    }

    /**
     * Set curentCategory
     *
     */
    public function setCurrentCategory($currentCategory)
    {
        $this->currentCategory = $currentCategory;

        return $this;
    }

    /**
     * Get support
     *
     */
    public function getSupport()
    {
        return $this->support;
    }

    /**
     * remove support
     *
     */
    public function removeSupport()
    {
        $this->support = null;
    }

    /**
     * Set support
     *
     */
    public function setSupport(\Autorentool\CoreBundle\Entity\TSupport $support)
    {
        $this->support = $support;
        $support->setFkTAssessmentItem($this);

        return $this;
    }

    /**
     * Get responseDeclaration
     *
     */
    public function getResponseDeclaration()
    {
        return $this->responseDeclaration;
    }

    /**
     * Set responseDeclaration
     *
     */
    public function setResponseDeclaration(\Autorentool\CoreBundle\Entity\TResponseDeclaration $responseDeclaration)
    {
        $this->responseDeclaration = $responseDeclaration;
        $responseDeclaration->setFkTAssessmentItem($this);

        return $this;
    }

    /**
     * Get itemBody
     *
     */
    public function getItemBody()
    {
        return $this->itemBody;
    }

    /**
     * Set itemBody
     *
     */
    public function setItemBody(\Autorentool\CoreBundle\Entity\TItemBody $itemBody)
    {
        $this->itemBody = $itemBody;
        $itemBody->setFkTAssessmentItem($this);

        return $this;
    }

    /**
     * Get related
     *
     */
    public function getRelated()
    {
        return $this->related;
    }

    /**
     * Set related
     *
     */
    public function setRelated(\Autorentool\CoreBundle\Entity\TRelated $related)
    {
        $this->related = $related;
        $related->setFkTAssessmentItem($this);

        return $this;
    }

    /**
     * Remove related
     *
     */
    public function removeRelated()
    {
        $this->related = null;
    }

    /**
     * Get categoryTags
     *
     */
    public function getCategoryTags()
    {
        return $this->categoryTags;
    }

    /**
     * Remove categoryTags
     *
     */
    public function removeCategoryTags()
    {
        $this->categoryTags = null;
    }

    /**
     * Get categoryTagsString
     *
     * @return string
     */
    public function getCategoryTagsString()
    {
        $categoryTagsString = "";
        foreach ($this->categoryTags as $categoryTag) {
            $categoryTagsString .= $categoryTag->getTagName().',';
        }

        return trim($categoryTagsString, ',');
    }

    /**
     * Set categoryTags
     *
     */
    public function setCategoryTags(\Autorentool\CoreBundle\Entity\TCategoryTags $categoryTags)
    {
        $this->categoryTags[] = $categoryTags;
        $categoryTags->setFkTAssessmentItem($this);

        return $this;
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
     * @return TAssessmentItem
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get state of task
     *
     * @return bool
     */
    public function getStateOfTask()
    {
        return $this->stateOfTask;
    }

    /**
     * Set state of task
     *
     * @param bool $stateOfTask
     *
     * @return TAssessmentItem
     */
    public function setStateOfTask($stateOfTask)
    {
        $this->stateOfTask = $stateOfTask;

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
     * @return TAssessmentItem
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
     * @return TAssessmentItem
     */
    public function setCreationTimestamp($creationTimestamp)
    {
        $this->creationTimestamp = $creationTimestamp;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     *
     * @return TAssessmentItem
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

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
     * @return TAssessmentItem
     */
    public function setTittle($tittle)
    {
        $this->tittle = $tittle;

        return $this;
    }

    /**
     * Get adaptive
     *
     * @return integer
     */
    public function getAdaptive()
    {
        return $this->adaptive;
    }

    /**
     * Get adaptive string
     *
     * @return string
     */
    public function getAdaptiveString()
    {
        return boolval($this->adaptive) ? 'true' : 'false';
    }

    /**
     * Set adaptive
     *
     * @param integer $adaptive
     *
     * @return TAssessmentItem
     */
    public function setAdaptive($adaptive)
    {
        $this->adaptive = $adaptive;

        return $this;
    }

    /**
     * Get timeDependent
     *
     * @return integer
     */
    public function getTimeDependent()
    {
        return $this->timeDependent;
    }

    /**
     * Get timeDependent string
     *
     * @return string
     */
    public function getTimeDependentString()
    {
        return boolval($this->timeDependent) ? 'true' : 'false';
    }

    /**
     * Set timeDependent
     *
     * @param integer $timeDependent
     *
     * @return TAssessmentItem
     */
    public function setTimeDependent($timeDependent)
    {
        $this->timeDependent = $timeDependent;

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
