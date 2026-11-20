<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;


/**
 * Users controller.
 *
 *
 */
class UsersImportReportsController extends Controller
{
    /**
     * Lists all Users entities.
     *
     * @Route("/reporting/usersimportreports/", name="users_import_reports")
     * @Template()
     */
    public function indexAction()
    {
        $source = new Entity('FullSixProjectForecastBundle:UsersImportReports');
        $grid = $this->get('grid');
        $grid->setSource($source);
        $grid->setLimits(10);

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");

        $grid->setDefaultOrder("importDate", "asc");
        $grid->getColumn('pdf')->manipulateRenderCell(function($value, $row, $router) {
                return basename($value);
            });

        $rowActionLog = new RowAction("download", 'user_import_log', false, "_self",
                                      array("title" => "Download Log File"));

        $grid->addRowAction($rowActionLog);

        return $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig',
            array(
                'title' => 'Users Import Logs',
                )
            );
    }


    /**
     * Downloads log file
     *
     * @Route("/reporting/usersimportreports/download/log/{id}", name="user_import_log")
     * @Template()
     */
    public function downloadlogAction($id) {

        $fileDownloadService = $this->container->get('fullsix_projectforecast.file_download');
        $response = $fileDownloadService->downloadUserImportPdf($id);

        if (is_array($response) && !empty($response['message'])) {
            $this->get('session')->getFlashBag()->add('errors', $response['message']);
            return $this->redirect($this->generateUrl('users_import_reports'));
        } else {
            return $response;
        }

    }
}
