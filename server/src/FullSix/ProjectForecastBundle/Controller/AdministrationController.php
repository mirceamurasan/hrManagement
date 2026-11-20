<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Administration controller
 *
 * @Route("/administration")
 */
class AdministrationController extends Controller {

    /**
     * @Route("/", name="administration")
     * @Template("FullSixProjectForecastBundle:Administration:administration.html.twig")
     */
    public function indexAction() {
        return array();
    }

    /**
     * @Route("/general", name="administration_general")
     * @Template()
     */
    public function generalAction() {
        return array();
    }

}
