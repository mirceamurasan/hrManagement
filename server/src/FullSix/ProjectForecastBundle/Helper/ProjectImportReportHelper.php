<?php

namespace FullSix\ProjectForecastBundle\Helper;

use FullSix\ProjectForecastBundle\Entity\ProjectImportErrors;
use FullSix\ProjectForecastBundle\Entity\ProjectImportReports;
use FullSix\ProjectForecastBundle\Service\CsvImportService;

class ProjectImportReportHelper {

    /**
     * Adds a line to the log with a specific status
     *
     * @param ProjectImportReports $reportObject
     * @param int $lineNb
     * @param array $entry
     * @param string $status
     */
    public function addEntry($reportObject, $lineNb, $entry) {
        $logEntry = new ProjectImportErrors();
        $logEntry->setLineNumber($lineNb);

        $logEntry->setIdSAP(
                            $entry[CsvImportService::COL_SAP_NUMBER] .
                            " " .
                            $this->checkIdSap($entry[CsvImportService::COL_SAP_NUMBER])
        );

        $logEntry->setProjectName(
                            $entry[CsvImportService::COL_PROJ_NAME] .
                            " " .
                            $this->checkProjectName($entry[CsvImportService::COL_PROJ_NAME])
        );

        $logEntry->setProjectStatus(
                            $entry[CsvImportService::COL_STATUS] .
                            " " .
                            $this->checkProjectStatus($entry[CsvImportService::COL_STATUS])
        );
        $logEntry->setClientSAPId(
                            $entry[CsvImportService::COL_CLIENT_ID] .
                            " " .
                            $this->checkClientId($entry[CsvImportService::COL_CLIENT_ID])
        );
        $logEntry->setClientName(
                            $entry[CsvImportService::COL_CLIENT_NAME] .
                            " " .
                            $this->checkClientName($entry[CsvImportService::COL_CLIENT_NAME])
        );

        $reportObject->addErrors($logEntry);
    }

    /**
     * Checks the validity of IdSap
     *
     * @param string $idSap
     *
     * @return string
     */
    protected function checkIdSap($idSap) {
        $status = 'ok';
        if (empty($idSap))
        {
            $status = 'missing';
        } elseif (strlen($idSap) > 50) {
            $status = 'invalid';
        }
        return $status;
    }

    /**
     * Checks the validity of the ProjectName
     *
     * @param string $projName
     *
     * @return string
     */
    protected function checkProjectName($projName) {
        $status = 'ok';
        if (empty($projName))
        {
            $status = 'missing';
        }
        return $status;
    }

    /**
     * Checks the validity of the ProjectStatus
     *
     * @param string $projStatus
     *
     * @return string
     */
    protected function checkProjectStatus($projStatus) {
        $status = 'ok';
        if (empty($projStatus))
        {
            $status = 'missing';
        } elseif (strlen($projStatus) > 45) {
            $status = 'invalid';
        }
        return $status;
    }

    /**
     * Checks the validity of the client id
     *
     * @param string $clientId
     *
     * @return string
     */
    protected function checkClientId($clientId) {
        $status = 'ok';
        if (empty($clientId))
        {
            $status = 'missing';
        } elseif (strlen($clientId) > 50) {
            $status = 'invalid';
        }
        return $status;
    }

    /**
     * Checks the validity of the client name
     *
     * @param string $clientName
     *
     * @return string
     */
    protected function checkClientName($clientName) {
        $status = 'ok';
        if (empty($clientName))
        {
            $status = 'missing';
        } elseif (strlen($clientName) > 50) {
            $status = 'invalid';
        }
        return $status;
    }
}