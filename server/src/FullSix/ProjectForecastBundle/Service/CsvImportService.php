<?php

namespace FullSix\ProjectForecastBundle\Service;

use FullSix\ProjectForecastBundle\Entity\Customers;
use FullSix\ProjectForecastBundle\Entity\CustomersSapIds;
use FullSix\ProjectForecastBundle\Entity\ProjectImportReports;
use FullSix\ProjectForecastBundle\Entity\Projects;
use FullSix\ProjectForecastBundle\Entity\ProjectStatuses;
use FullSix\ProjectForecastBundle\Repository\ProjectsRepository;
use FullSix\ProjectForecastBundle\Repository\CustomersRepository;
use FullSix\ProjectForecastBundle\Repository\ProjectStatusesRepository;
use FullSix\ProjectForecastBundle\Repository\ProjectImportReportsRepository;
use FullSix\ProjectForecastBundle\Helper\ProjectImportReportHelper;

class CsvImportService {

    const COL_SAP_NUMBER = 5;
    const COL_STATUS = 7;
    const COL_PROJ_NAME = 13;
    const COL_CLIENT_ID = 18;
    const COL_CLIENT_NAME = 29;

    protected $doctrine;
    protected $container;
    protected $csvSourceServer;
    protected $csvSourceFtp;
    protected $csvTarget;
    protected $pdfPath;
    protected $ftpStream;
    protected $pdfName;

    public function __construct($doctrine, $container, $csvSourceServer, $csvSourceFtp, $csvTarget,
                                $pdfPath, $ftpStream, $pdfName) {
        $this->doctrine = $doctrine;
        $this->container = $container;
        $this->csvSourceServer = $csvSourceServer;
        $this->csvSourceFtp = $csvSourceFtp;
        $this->csvTarget = $csvTarget;
        $this->pdfPath = $pdfPath;
        $this->ftpStream = $ftpStream;
        $this->pdfName = $pdfName;
    }

    /**
     * Imports the csv data into the corresponding tables.
     */
    public function import() {

        $success = true;
        $failReason = '';

        // Step 1 - Download the files to the server
        if (!empty($this->ftpStream))
        {
            // First try to get the from the FTP
            $newFileCount = $this->manageFtpDownload();
        } else {
            // If no FTP, copy them from another server location
            $newFileCount = $this->manageLocalDownload();
        }

        $em = $this->getDoctrine()->getManager();

        if ($newFileCount == 0)
        {
            // Set an error and specify the reason
            $importReport = new ProjectImportReports();

            $importReport->setImportDate(\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
            $importReport->setSuccess(false);
            $importReport->setFailReason("No new file");

            $em->persist($importReport);
            $em->flush();
        }else{
            // Step 2 - Import data from each file on the server

            $localFileList = scandir($this->csvTarget);

            // Remove current dir and parent dir from array
            $localFileList = array_slice($localFileList, 2);
            if (count($localFileList) > 0)
            {
                foreach ($localFileList as $dirIndex => $localFile)
                {
                    if (!$this->isLocalNewFile($this->csvTarget . '/' . $localFile))
                    {
                        unset($localFileList[$dirIndex]);
                    }
                }
            }

            if (count($localFileList) > 0)
            {
                foreach ($localFileList as $key => $localFile)
                {

                    $countCreatedProjects = 0;
                    $countUpdatedProjects = 0;
                    $countErrors = 0;
                    $countCreatedStatuses = 0;
                    $countCreatedClients = 0;

                    // Check if the file exists
                    if (file_exists($this->csvTarget .
                                    '/' .
                                    $localFile))
                    {
                        // First of all, parse the csv and get the data
                        $data = $this->parseCsv($localFile);
                        if (null === $data)
                        {
                            // If no data, the file couldn't be opened
                            $success = false;
                            $failReason = "Cannot open file";
                        }
                    } else {
                        // The file doesn't exist => set an error and specify the reason
                        $data = null;
                        $success = false;
                        $failReason = "No file found";
                    }

                    $reportHelper = new ProjectImportReportHelper();
                    $importReport = new ProjectImportReports();

                    /** @var ProjectsRepository $projectRepo */
                    $projectRepo = $em->getRepository('FullSixProjectForecastBundle:Projects');
                    /** @var CustomersRepository $customerRepo */
                    $customerRepo = $em->getRepository('FullSixProjectForecastBundle:Customers');
                    /** @var ProjectStatusesRepository $projectStatusRepo */
                    $projectStatusRepo = $em->getRepository('FullSixProjectForecastBundle:ProjectStatuses');

                    if (!empty($data))
                    {
                        // Take each row individually
                        foreach ($data as $lineNb => $entry)
                        {
                            // If the required fields are not set, move on to the next line
                            if (true == $this->checkErrors($entry))
                            {
                                $countErrors++;
                                $reportHelper->addEntry($importReport, $lineNb + 1, $entry);
                                continue;
                            }

                            // If we try to add a customer without a name, move on to the next line
                            $customer = $customerRepo->findCustomerBySapId($entry[self::COL_CLIENT_ID]);
                            if (empty($customer) && empty($entry[self::COL_CLIENT_NAME]))
                            {
                                $countErrors++;
                                $reportHelper->addEntry($importReport, $lineNb + 1, $entry);
                                continue;
                            }

                            // We first check if the project already exists
                            $project = $projectRepo->getProjectsBySapNumber($entry[self::COL_SAP_NUMBER]);
                            if (empty($project))
                            {
                                $countCreatedProjects++;
                                // If it doesn't, we create it
                                $project = new Projects();
                            } else {
                                $countUpdatedProjects++;
                            }
                            $project->setSapnumber($entry[self::COL_SAP_NUMBER]);
                            $project->setProjectsname($entry[self::COL_PROJ_NAME]);
                            // Set the additional required fields which have no default and are not fetched from the CSV
                            $project->setProjectssuccess(0);
                            $project->setIsOutsourcing(false);

                            $em->persist($project);

                            // Next, we check the status of the project
                            // If it already exists, we don't update it
                            $status = $projectStatusRepo->findStatusByLabel($entry[self::COL_STATUS]);
                            if (empty($status) && !empty($entry[self::COL_STATUS]))
                            {
                                $countCreatedStatuses++;
                                // If it doesn't exist, we create it and set the label
                                $status = new ProjectStatuses();
                                $status->setProjectStatuseslabel($entry[self::COL_STATUS]);

                                $em->persist($status);
                            }

                            if (!empty($status))
                            {
                                $project->setProjectstatus($status);
                                if($status->getIsTrashed()){
                                    //force reactivate
                                    $status->setIsTrashed(false);
                                    $em->persist($status);
                                }

                                $em->persist($project);
                            }

                            // Finally, we check if the customer does not exist
                            // If it does, we leave it like that; if not, we create it
                            if (empty($customer)) {
                                $countCreatedClients++;
                                $customer = new Customers();

                                // First set the sapId
                                $customerSapId = new CustomersSapIds();
                                $customerSapId->setSapid($entry[self::COL_CLIENT_ID]);
                                $customerSapId->setCustomers($customer);

                                $em->persist($customerSapId);

                                // Then set the customer's name
                                $customer->setSapids(array($customerSapId));
                                $customer->setCustomersname($entry[self::COL_CLIENT_NAME]);

                                $em->persist($customer);
                            }

                            if ($customer instanceof Customers)
                            {
                                $project->setCustomersidcustomers($customer);
                                $em->persist($project);
                                $em->flush();
                            }
                        }
                    }
                    $importReport->setImportDate(\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s')));
                    $importReport->setCsvPath($this->csvTarget . '/' . $localFile);
                    $importReport->setPdfPath($this->pdfPath . '/' . $this->pdfName . '_' . date('YmdHi') . '_' . $key . '.pdf');
                    $importReport->setSuccess($success);
                    $importReport->setFailReason($failReason);
                    $importReport->setCountProjectsCreated($countCreatedProjects);
                    $importReport->setCountProjectsUpdated($countUpdatedProjects);
                    $importReport->setCountErrors($countErrors);
                    $importReport->setCountStatusesCreated($countCreatedStatuses);
                    $importReport->setCountClientsCreated($countCreatedClients);

                    $this->getContainer()->get('knp_snappy.pdf')->generateFromHtml(
                         $this->getContainer()->get('templating')->render(
                              'FullSixProjectForecastBundle::projectsImportReport.html.twig',
                              array(
                                  'entity'  => $importReport
                              )
                         ),
                         $this->pdfPath . '/' . $this->pdfName . '_' . date('YmdHi') . '_' . $key . '.pdf'
                    );

                    $em->persist($importReport);
                    $em->flush();
                }
            }
        }
    }

    /**
     * Opens the csv file, parses it and returns the containing data.
     *
     * @param string $fileName
     *
     * @return array
     */
    protected function parseCsv($fileName) {
        $dataArray = array();
        if (false !== ($fileHandler = fopen($this->csvTarget . '/' . $fileName, 'r'))) {
            while (false !== ($rawData = fgetcsv($fileHandler, 0, ';'))) {
                $dataArray[] = $rawData;
            }
            fclose($fileHandler);
        } else {
            return null;
        }


        // force array key to start from 1 instead of 0
        foreach ($dataArray as $rowNb => $row)
        {
            array_unshift($row, "");
            unset($row[0]);
            $dataArray[$rowNb] = $row;
        }

        return $dataArray;
    }

    /**
     * Checks if a file from the ftp is newer than the latest import
     *
     * @param string $remoteFilePath
     *
     * @return bool
     */
    protected function isNewFile($ftpConnection, $remoteFilePath) {
        $em = $this->getDoctrine()->getManager();

        $projectImportRepo = $em->getRepository('FullSixProjectForecastBundle:ProjectImportReports');
        $latestEntry = $projectImportRepo->findLatest();
        if (!empty($latestEntry))
        {
            // Check if the modified date for the file is older than the latest import date
            $modifiedTime = ftp_mdtm($ftpConnection, $remoteFilePath);
            $latestImportTime = strtotime($latestEntry->getImportDate());
            if ($modifiedTime < $latestImportTime) {
                return false;
            }
        }
        return true;
    }

    /**
     * Checks if a local file is newer than the latest import
     *
     * @param string $filePath
     *
     * @return bool
     */
    protected function isLocalNewFile($filePath) {
        $em = $this->getDoctrine()->getManager();

        $projectImportRepo = $em->getRepository('FullSixProjectForecastBundle:ProjectImportReports');
        $latestEntry = $projectImportRepo->findLatest();
        if (!empty($latestEntry))
        {
            // check if the modified date for the local file is older than the latest import date
            $modifiedTime = filemtime($filePath);
            $latestImportTime = strtotime($latestEntry->getImportDate()->format('Y-m-d H:i:s'));
            if ($modifiedTime < $latestImportTime) {
                return false;
            }
        }
        return true;
    }


    /**
     * Connects to the FTP and returns the resource
     *
     * @return resource
     */
    protected function ftpConnect() {
        $ftpConnection = ftp_connect($this->ftpStream);
        return $ftpConnection;
    }

    /**
     * Tries to copy a file from an ftp to a specific location
     *
     * @param string $remoteFilePath
     * @param string $localFilePath
     *
     * @return bool
     */
    protected function downloadFileFromFtp($ftpConnection, $remoteFilePath, $localFilePath) {
        if (!empty($ftpConnection)) {
            return ftp_get($ftpConnection, $localFilePath, $remoteFilePath, FTP_ASCII);
        }
        return false;
    }

    /**
     * Downloads the content of a remote directory to the server and returns the number of files
     *
     * @return int
     */
    protected function manageFtpDownload() {
        $ftpConnection = $this->ftpConnect();
        // Scan the directory and store the file names
        $fileList = ftp_nlist($ftpConnection, $this->csvSourceFtp);
        $newFileCount = 0;
        if (!empty($fileList))
        {
            foreach ($fileList as $fileName)
            {
                $absolutePath = $this->csvSourceFtp . '/' . $fileName;
                // Check if the file is new
                if (true == $this->isNewFile($ftpConnection, $absolutePath))
                {
                    $newFileCount++;
                    $this->downloadFileFromFtp($ftpConnection, $absolutePath, $this->csvTarget . '/' . basename($fileName, '.csv') . '_' . date('YmdHi') . '.csv');
                }
            }
        }

        return $newFileCount;
    }

    /**
     * Copies the content of a local source directory to a local target directory
     *
     * @return int
     */
    protected function manageLocalDownload() {
        $fileList = scandir($this->csvSourceServer);
        // Remove crt dir and parent dir
        $fileList = array_slice($fileList, 2);

        $newFileCount = 0;

        if (count($fileList) > 0)
        {
            foreach ($fileList as $fileName)
            {
                $absolutePath = $this->csvSourceServer . '/' . $fileName;
                if (true == $this->isLocalNewFile($absolutePath))
                {
                    // Copy the file only if it's new
                    $newFileCount++;
                    copy($absolutePath, $this->csvTarget . '/' . basename($fileName, '.csv') . '_' . date('YmdHi') . '.csv');
                }
            }
        }
        return $newFileCount;
    }

    /**
     * Check if there are any issues regarding an entry from the csv
     *
     * @param array $entry
     *
     * @return bool
     */
    protected function checkErrors($entry) {
        return (empty($entry[self::COL_SAP_NUMBER]) ||
            empty($entry[self::COL_PROJ_NAME]) ||
            empty($entry[self::COL_CLIENT_ID]));
    }

    /**
     * Returns the Doctrine instance.
     *
     * @return mixed
     */
    protected function getDoctrine() {
        return $this->doctrine;
    }

    /**
     * Returns the ServiceContainer instance
     *
     * @return mixed
     */
    protected function getContainer() {
        return $this->container;
    }
}