<?php

namespace FullSix\ProjectForecastBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * UsersImportReports
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="FullSix\ProjectForecastBundle\Repository\UsersImportReportsRepository")
 * @GRID\Source(columns="id, importDate, success, countUsersCreated, countUsersUpdated, countUsersMarkedDirty, pdf, countErrors")
 */
class UsersImportReports
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
     * @var boolean $success
     *
     * @ORM\Column(name="success", type="boolean", nullable=false)
     * @GRID\Column(title="Success")
     */
    protected $success;

    /**
     * @var string
     *
     * @ORM\Column(name="pdf", type="string", length=255)
     * @GRID\Column(title="reporting.importReports.pdfPath")
     */
    protected $pdf;

    /**
     * @var string
     *
     * @ORM\Column(name="failReason", type="string", length=255)
     */
    protected $failReason;

    /**
     * @var integer
     *
     * @ORM\Column(name="countUsersCreated", type="integer")
     * @GRID\Column(title="reporting.importReports.countUsersCreated")
     */
    protected $countUsersCreated;

    /**
     * @var integer
     *
     * @ORM\Column(name="countUsersUpdated", type="integer")
     * @GRID\Column(title="reporting.importReports.countUsersUpdated")
     */
    protected $countUsersUpdated;

    /**
     * @var integer
     *
     * @ORM\Column(name="countUsersMarkedDirty", type="integer")
     * @GRID\Column(title="reporting.importReports.countUsersMarkedDirty")
     */
    protected $countUsersMarkedDirty;

    /**
     * @var string
     *
     * @ORM\Column(name="countErrors", type="string", length=255)
     * @GRID\Column(title="reporting.importReports.countErrors")
     */
    protected $countErrors;

    /**
     * @var array
     */
    protected $errors;


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
     * @return UsersImportReports
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
     * Set pdf
     *
     * @param string $pdf
     *
     * @return UsersImportReports
     */
    public function setPdf($pdf)
    {
        $this->pdf = $pdf;

        return $this;
    }

    /**
     * Get pdf
     *
     * @return string
     */
    public function getPdf()
    {
        return $this->pdf;
    }

    /**
     * Set success
     *
     * @param boolean $success
     *
     * @return UsersImportReports
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
     * @return UsersImportReports
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
     * Set countUsersCreated
     *
     * @param integer $countUsersCreated
     *
     * @return UsersImportReports
     */
    public function setCountUsersCreated($countUsersCreated)
    {
        $this->countUsersCreated = $countUsersCreated;

        return $this;
    }

    /**
     * Get countUsersCreated
     *
     * @return integer
     */
    public function getCountUsersCreated()
    {
        return $this->countUsersCreated;
    }

    /**
     * Set countUsersUpdated
     *
     * @param integer $countUsersUpdated
     *
     * @return UsersImportReports
     */
    public function setCountUsersUpdated($countUsersUpdated)
    {
        $this->countUsersUpdated = $countUsersUpdated;

        return $this;
    }

    /**
     * Get countUsersUpdated
     *
     * @return integer
     */
    public function getCountUsersUpdated()
    {
        return $this->countUsersUpdated;
    }

    /**
     * Set countUsersMarkedDirty
     *
     * @param integer $countUsersMarkedDirty
     *
     * @return UsersImportReports
     */
    public function setCountUsersMarkedDirty($countUsersMarkedDirty)
    {
        $this->countUsersMarkedDirty = $countUsersMarkedDirty;

        return $this;
    }

    /**
     * Get countUsersMarkedDirty
     *
     * @return integer
     */
    public function getCountUsersMarkedDirty()
    {
        return $this->countUsersMarkedDirty;
    }

    /**
     * Set countErrors
     *
     * @param integer $countErrors
     *
     * @return UsersImportReports
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
     * @param UsersImportErrors $error
     *
     * @return UsersImportReports
     */
    public function addErrors(UsersImportErrors $error)
    {
        $this->errors[] = $error;

        return $this;
    }


    /**
     * Get errors
     *
     * @return UsersImportErrors
     */
    public function getErrors()
    {
        return $this->errors;
    }
}

