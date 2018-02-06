<?php

namespace Autorentool\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * TImport
 *
 * @ORM\Table(name="t_import")
 * @ORM\Entity
 */
class TImport
{
    /**
     * @var integer
     *
     * @ORM\Column(name="t_assessment_item_id", type="integer", nullable=false)
     */
    private $tAssessmentItemId;

    /**
     * @var integer
     *
     * @ORM\Column(name="import_timestamp", type="integer", nullable=true)
     */
    private $importTimestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="used_url", type="text", length=65535, nullable=true)
     */
    private $usedUrl;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * Get tAssessmentItemId
     *
     * @return integer
     */
    public function getTAssessmentItemId()
    {
        return $this->tAssessmentItemId;
    }

    /**
     * Set tAssessmentItemId
     *
     * @param integer $tAssessmentItemId
     *
     * @return TImport
     */
    public function setTAssessmentItemId($tAssessmentItemId)
    {
        $this->tAssessmentItemId = $tAssessmentItemId;

        return $this;
    }

    /**
     * Get importTimestamp
     *
     * @return integer
     */
    public function getImportTimestamp()
    {
        return $this->importTimestamp;
    }

    /**
     * Set importTimestamp
     *
     * @param integer $importTimestamp
     *
     * @return TImport
     */
    public function setImportTimestamp($importTimestamp)
    {
        $this->importTimestamp = $importTimestamp;

        return $this;
    }

    /**
     * Get usedUrl
     *
     * @return string
     */
    public function getUsedUrl()
    {
        return $this->usedUrl;
    }

    /**
     * Set usedUrl
     *
     * @param string $usedUrl
     *
     * @return TImport
     */
    public function setUsedUrl($usedUrl)
    {
        $this->usedUrl = $usedUrl;

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
