<?php

namespace FullSix\ProjectForecastBundle\Service;

use Symfony\Component\HttpFoundation\Response;

class FileDownloadService {

    protected $doctrine;
    protected $container;

    public function __construct($doctrine, $container) {
        $this->doctrine = $doctrine;
        $this->container = $container;
    }

    /**
     * Downloads the source csv file for ProjectImport
     *
     * @param int $id
     *
     * @return null|Response
     */
    public function downloadProjectImportCsv($id) {
        return $this->downloadProjectImportFile($id, 'csv');
    }

    /**
     * Downloads a log file for ProjectImport
     *
     * @param int $id
     *
     * @return null|Response
     */
    public function downloadProjectImportPdf($id) {
        return $this->downloadProjectImportFile($id, 'pdf');
    }

    /**
     * Downloads a log file for UsersImport
     *
     * @param int $id
     *
     * @return mixed
     */
    public function downloadUserImportPdf($id) {
        return $this->downloadUsersImportFile($id, 'pdf');
    }

    /**
     * Downloads a file related to ProjectImportReports
     * It can be either csv or pdf
     *
     * @param int $id
     * @param string $mode
     *
     * @return null|Response
     */
    protected function downloadProjectImportFile($id, $mode = 'csv') {
        $em = $this->getDoctrine()->getManager();

        $projectImportReportRepo = $em->getRepository('FullSixProjectForecastBundle:ProjectImportReports');
        $projectImportReport = $projectImportReportRepo->find($id);

        if ($mode == 'pdf')
        {
            $absPath = $projectImportReport->getPdfPath();
            $contentType = 'application/pdf';
        } else {
            $absPath = $projectImportReport->getCsvPath();
            $contentType = 'test/csv';
        }

        return $this->downloadFile($absPath, $contentType);
    }

    /**
     * Downloads a file related to UsersImportReports
     * For now, it can be only pdf
     *
     * @param int $id
     * @param string $mode
     *
     * @return null|Response
     */
    protected function downloadUsersImportFile($id, $mode='pdf') {
        $em = $this->getDoctrine()->getManager();

        $usersImportReportsRepo = $em->getRepository('FullSixProjectForecastBundle:UsersImportReports');
        $usersImportReport = $usersImportReportsRepo->find($id);

        $baseDir = $this->getContainer()->getParameter('knp_snappy.path_reports.import_users');

        if ($mode == 'pdf')
        {
            $absPath = '../' . $baseDir . '/' . $usersImportReport->getPdf();
            $contentType = 'application/pdf';
        }

        if (!empty($absPath) && !empty($contentType)) {
            return $this->downloadFile($absPath, $contentType);
        } else {
            return array('errorCode' => -2, 'message' => 'Path or content type not set.');
        }
    }

    /**
     * Performs the actual file download
     *
     * @param string $path
     * @param string $contentType
     *
     * @return null|Response
     */
    protected function downloadFile($path, $contentType) {

        if (file_exists($path)) {

            $content = file_get_contents($path);

            $response = new Response();

            $response->headers->set('Content-Type', $contentType);
            $response->headers->set('Content-Disposition', 'attachment;filename="' . basename($path) . '"');

            $response->setContent($content);

            return $response;
        }
        return array('errorCode' => -1, 'message' => 'L\'application ne parvient pas à trouver : ' . $path );
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