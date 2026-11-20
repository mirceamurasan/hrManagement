<?php

namespace FullSix\ProjectForecastBundle\Entity;

/**
 * ProjectImportErrors
 */
class ProjectImportErrors
{
    /**
     * @var string
     */
    protected $lineNumber;

    /**
     * @var string
     */
    protected $idSAP;

    /**
     * @var string
     */
    protected $projectName;

    /**
     * @var string
     */
    protected $projectStatus;

    /**
     * @var string
     */
    protected $clientSAPId;

    /**
     * @var string
     */
    protected $clientName;

    /**
     * Set lineNumber
     *
     * @param string $lineNumber
     *
     * @return ProjectImportErrors
     */
    public function setLineNumber($lineNumber)
    {
        $this->lineNumber = $lineNumber;

        return $this;
    }

    /**
     * Get lineNumber
     *
     * @return string
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * Set idSAP
     *
     * @param string $idSAP
     *
     * @return ProjectImportErrors
     */
    public function setIdSAP($idSAP)
    {
        $this->idSAP = $idSAP;

        return $this;
    }

    /**
     * Get idSAP
     *
     * @return string
     */
    public function getIdSAP()
    {
        return $this->idSAP;
    }

    /**
     * Set projectName
     *
     * @param string $projectName
     *
     * @return ProjectImportErrors
     */
    public function setProjectName($projectName)
    {
        $this->projectName = $projectName;

        return $this;
    }

    /**
     * Get projectName
     *
     * @return string
     */
    public function getProjectName()
    {
        return $this->projectName;
    }

    /**
     * Set projectStatus
     *
     * @param string $projectStatus
     *
     * @return ProjectImportErrors
     */
    public function setProjectStatus($projectStatus)
    {
        $this->projectStatus = $projectStatus;

        return $this;
    }

    /**
     * Get projectStatus
     *
     * @return string
     */
    public function getProjectStatus()
    {
        return $this->projectStatus;
    }

    /**
     * Set clientSAPId
     *
     * @param string $clientSAPId
     *
     * @return ProjectImportErrors
     */
    public function setClientSAPId($clientSAPId)
    {
        $this->clientSAPId = $clientSAPId;

        return $this;
    }

    /**
     * Get clientSAPId
     *
     * @return string
     */
    public function getClientSAPId()
    {
        return $this->clientSAPId;
    }

    /**
     * Set clientName
     *
     * @param string $clientName
     *
     * @return ProjectImportErrors
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * Get clientName
     *
     * @return string
     */
    public function getClientName()
    {
        return $this->clientName;
    }
}

