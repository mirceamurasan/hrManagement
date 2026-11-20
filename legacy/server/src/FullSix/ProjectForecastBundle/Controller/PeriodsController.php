<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Component\HttpFoundation\RedirectResponse;
use FullSix\ProjectForecastBundle\Form\PeriodsType;
use FullSix\ProjectForecastBundle\Entity\Contracts;
use FullSix\ProjectForecastBundle\Entity\Periods;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Contracts controller.
 *
 * @Route("/periods")
 */
class PeriodsController extends Controller {

    /**
     * Displays a form to create a new Periods entity.
     *
     * @Route("/{id}/new", name="periods_new")
     * @Template()
     */
    public function newAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = new Periods();
        $contract = $em->getRepository('FullSixProjectForecastBundle:Contracts')->find($id);

        if (!$contract) {
            throw $this->createNotFoundException('Unable to find Contracts entity.');
        }

        $entity->setIdcontract($contract);
        $entity->setIstrashed(false);

        $isExternal = $contract->getContracttype()->getContractTypesIsExternal();
        $form = $this->createForm(new PeriodsType($isExternal), $entity);

        $request = $this->getRequest();

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                $entity = $form->getData();
                $em->persist($entity);
                $em->flush();

                $this->container->get('fullsix_projectforecast.denormalisation_helper')
                    ->crudPeriodDenormalisation($entity);

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
            'contract' => $contract,
        );
    }

    /**
     * Finds and displays a Periods entity.
     *
     * @Route("/{id}/show", name="periods_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Periods')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Periods entity.');
        }

        return array(
            'entity' => $entity,
        );
    }

     /**
     * Displays a form to edit an existing Periods entity.
     *
     * @Route("/{id}/edit", name="periods_edit")
     * @Template("FullSixProjectForecastBundle:Periods:edit.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Periods')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Periods entity.');
        }

        $isExternal = $entity->getIdContract()->getContracttype()->getContractTypesIsExternal();
        $editForm = $this->createForm(new PeriodsType($isExternal), $entity);

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Periods entity.
     *
     * @Route("/{id}/update", name="periods_update")
     */
    public function updateAction($id)
    {
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Periods')->find($id);

        $isExternal = $entity->getIdContract()->getContracttype()->getContractTypesIsExternal();
        $editForm = $this->createForm(new PeriodsType($isExternal), $entity);

        if ($request->isMethod('POST')) {
            $editForm->bind($request);
            if ($editForm->isValid()) {

                $entity = $editForm->getData();
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $this->container->get('fullsix_projectforecast.denormalisation_helper')->crudPeriodDenormalisation($entity);

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
     * @Route("/{id}/activate/{page}", name="periods_activation")
     * @Template
     */
    public function activationAction($id, $page = null)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Periods')->find($id);

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