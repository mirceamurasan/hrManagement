<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use APY\DataGridBundle\Grid\Source\Entity;
use FullSix\ProjectForecastBundle\Entity\Periods;
use FullSix\ProjectForecastBundle\Entity\Vacations;
use APY\DataGridBundle\Grid\Action\RowAction;
use FullSix\ProjectForecastBundle\Form\VacationsType;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Vacations controller.
 *
 *
 */
class VacationsController extends Controller
{

    /**
     * Displays a form to create a new Vacations entity.
     *
     * @Route("vacations/{id}/new", name="vacations_new")
     * @Template()
     */
    public function newAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Vacations();
        $worker = $em->getRepository('FullSixProjectForecastBundle:Workers')->find($id);
        $entity->setWorker($worker);

        $form = $this->createForm(new VacationsType(), $entity);

        $request = $this->getRequest();

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($entity);
                $em->flush();

                return new Response(json_encode(array('status' => "OK")));
            } else {
                return new Response(json_encode(array('status' => "NOT_OK",
                        'messages' => $this->container->get('fullsix_projectforecast.translation_utils')
                                ->buildErrorArrayForForm($form))));
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'worker' => $worker
        );
    }

    /**
     * Finds and displays a Vacations entity.
     *
     * @Route("/vacations/{id}/show", name="vacations_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Vacations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Users entity.');
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Vacations entity.
     *
     * @Route("/vacations/{id}/edit", name="vacations_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Vacations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Periods entity.');
        }

        $editForm = $this->createForm(new VacationsType(), $entity);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Periods entity.
     *
     * @Route("vacations/{id}/update", name="vacations_update")
     */
    public function updateAction($id)
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Vacations')->find($id);
        $entity->setWorker($entity->getVacationperiods()->getIdcontract()->getIdworker());

        $editForm = $this->createForm(new VacationsType(), $entity);

        if ($request->isMethod('POST')) {
            $editForm->bind($request);
            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                return new Response(json_encode(array('status' => "OK")));
            } else {
                return new Response(json_encode(array('status' => "NOT_OK",
                        'messages' => $this->container->get('fullsix_projectforecast.translation_utils')
                                ->buildErrorArrayForForm($editForm))));
            }

        }
        return new Response(json_encode(array('status' => "OK")));
    }

    /**
     * @Route("vacations/{id}/activate/{page}", name="vacations_activation")
     * @Template
     */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Vacations')->find($id);

        $err = $entity->checkActivation();
        if(!empty($err)){
            $data = $this->container->get('fullsix_projectforecast.translation_utils')->buildErrorHtml($err);
            return new JsonResponse(array('status' => false, 'html' => $data), 200);
        }

        $entity->performActivationAndSideEffects();

        $em->persist($entity);
        $em->flush();

        return new JsonResponse(array('status' => true), 200);

    }

}