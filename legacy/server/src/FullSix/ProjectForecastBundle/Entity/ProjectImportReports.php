<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;
use JMS\SecurityExtraBundle\Tests\Security\Authorization\Expression\Fixture\Issue22\Project;

/**
 * ProjectImportReports
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\ProjectImportReportsRepository")
 * @GRID\Source(columns="id, importDate, csvPath, pdfPath, success, failReason, countProjectsCreated, countProjectsUpdated, countErrors, countStatusesCreated, countClientsCreated")
 */
class ProjectImportReports
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @GRID\Column(visible=false, filterable=false)
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="importDate", type="datetime")
     * @GRID\Column(title="Date")
     */
    protected $importDate;

    /**
     * @var string
     *
     * @ORM\Column(name="csvPath", type="string", length=255, nullable=true)
     * @GRID\Column(title="reporting.importReports.csvPath")
     */
    protected $csvPath;

    /**
     * @var string
     *
     * @ORM\Column(name="pdfPath", type="string", length=255, nullable=true)
     * @GRID\Column(title="reporting.importReports.pdfPath")
     */
    protected $pdfPath;

    /**
     * @var boolean
     *
     * @ORM\Column(name="success", type="boolean", nullable=false)
     * @GRID\Column(title="Success")
     */
    protected $success;

    /**
     * @var string
     *
     * @ORM\Column(name="failReason", type="string", length=255, nullable=true)
     * @GRID\Column(title="reporting.importReports.failReason")
     */
    protected $failReason;

    /**
     * @var integer
     *
     * @ORM\Column(name="countProjectsCreated", type="integer", nullable=true)
     * @GRID\Column(title="reporting.importReports.countProjectsCreated")
     */
    protected $countProjectsCreated;

    /**
     * @var integer
     *
     * @ORM\Column(name="countProjectsUpdated", type="integer", nullable=true)
     * @GRID\Column(title="reporting.importReports.countProjectsUpdated")
     */
    protected $countProjectsUpdated;

    /**
     * @var integer
     *
     * @ORM\Column(name="countErrors", type="integer", nullable=true)
     * @GRID\Column(title="reporting.importReports.countErrors")
     */
    protected $countErrors;

    /**
     * @var integer
     *
     * @ORM\Column(name="countStatusesCreated", type="integer", nullable=true)
     * @GRID\Column(title="reporting.importReports.countStatusesCreated")
     */
    protected $countStatusesCreated;

    /**
     * @var integer
     *
     * @ORM\Column(name="countClientsCreated", type="integer", nullable=true)
     * @GRID\Column(title="reporting.importReports.countClientsCreated")
     */
    protected $countClientsCreated;


    public function __construct()
    {
        $this->importDate = new \DateTime();
        $this->errors = array();
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
     * Set importDate
     *
     * @param \DateTime $importDate
     *
     * @return ProjectImportReports
     */
    public function setImportDate($importDate)
    {
        $this->importDate = $importDate;

        return $this;
    }

    /**
     * Get importDate
     *
     * @return \DateTime
     */
    public function getImportDate()
    {
        return $this->importDate;
    }

    /**
     * Set csvPath
     *
     * @param string $csvPath
     *
     * @return ProjectImportReports
     */
    public function setCsvPath($csv)
    {
        $this->csvPath = $csv;

        return $this;
    }

    /**
     * Get csvPath
     *
     * @return string
     */
    public function getCsvPath()
    {
        return $this->csvPath;
    }

    /**
     * Set pdfPath
     *
     * @param string $pdfPath
     *
     * @return ProjectImportReports
     */
    public function setPdfPath($pdf)
    {
        $this->pdfPath = $pdf;

        return $this;
    }

    /**
     * Get pdfPath
     *
     * @return string
     */
    public function getPdfPath()
    {
        return $this->pdfPath;
    }

    /**
     * Set success
     *
     * @param boolean $success
     *
     * @return ProjectImportReports
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Get success
     *
     * @return boolean
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Set failReason
     *
     * @param string $failReason
     *
     * @return ProjectImportReports
     */
    public function setFailReason($failReason)
    {
        $this->failReason = $failReason;

        return $this;
    }

    /**
     * Get failReason
     *
     * @return string
     */
    public function getFailReason()
    {
        return $this->failReason;
    }

    /**
     * Set countProjectsCreated
     *
     * @param integer $countProjectsCreated
     *
     * @return ProjectImportReports
     */
    public function setCountProjectsCreated($countProjectsCreated)
    {
        $this->countProjectsCreated = $countProjectsCreated;

        return $this;
    }

    /**
     * Get countProjectsCreated
     *
     * @return integer
     */
    public function getCountProjectsCreated()
    {
        return $this->countProjectsCreated;
    }

    /**
     * Set countProjectsUpdated
     *
     * @param integer $countProjectsUpdated
     *
     * @return ProjectImportReports
     */
    public function setCountProjectsUpdated($countProjectsUpdated)
    {
        $this->countProjectsUpdated = $countProjectsUpdated;

        return $this;
    }

    /**
     * Get countUsersUpdated
     *
     * @return integer
     */
    public function getCountProjectsUpdated()
    {
        return $this->countProjectsUpdated;
    }

    /**
     * Set countErrors
     *
     * @param integer $countErrors
     *
     * @return ProjectImportReports
     */
    public function setCountErrors($countErrors)
    {
        $this->countErrors = $countErrors;

        return $this;
    }

    /**
     * Get countErrors
     *
     * @return integer
     */
    public function getCountErrors()
    {
        return $this->countErrors;
    }


    /**
     * Add error
     *
     * @param ProjectImportErrors $error
     *
     * @return ProjectImportReports
     */
    public function addErrors(ProjectImportErrors $error)
    {
        $this->errors[] = $error;

        return $this;
    }


    /**
     * Get errors
     *
     * @return ProjectImportReports
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Set countStatusesCreated
     *
     * @param integer $countStatusesCreated
     *
     * @return ProjectImportReports
     */
    public function setCountStatusesCreated($countStatusesCreated)
    {
        $this->countStatusesCreated = $countStatusesCreated;

        return $this;
    }

    /**
     * Get countStatusesCreated
     *
     * @return integer
     */
    public function getCountStatusesCreated()
    {
        return $this->countStatusesCreated;
    }

    /**
     * Set countClientsCreated
     *
     * @param integer $countClientsCreated
     *
     * @return ProjectImportReports
     */
    public function setCountClientsCreated($countClientsCreated)
    {
        $this->countClientsCreated = $countClientsCreated;

        return $this;
    }

    /**
     * Get countClientsCreated
     *
     * @return integer
     */
    public function getCountClientsCreated()
    {
        return $this->countClientsCreated;
    }
}

