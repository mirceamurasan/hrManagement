<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FullSix\ProjectForecastBundle\Entity\KeyDates;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Form\KeyDatesType;

class KeyDatesController extends Controller
{
    /**
     * Displays a form to create a new KeyDates entity.
     *
     * @Route("/new-keydate/{projectId}", name="keydates_new")
     */
    public function newAction($projectId)
    {
        $entity = new KeyDates();
        $form = $this->createForm(new KeyDatesType(), $entity);

        $request = $this->getRequest();

        if ($request->isMethod('POST')) {

            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $project = $em->getRepository('FullSixProjectForecastBundle:Projects')->find($projectId);

                $entity->setProjects($project);

                $em->persist($entity);

                $em->flush();

                return $this->redirect($this->generateUrl('projects_details', array('id' => $projectId)));
            }
        }

        return $this->render('FullSixProjectForecastBundle:KeyDates:newPopup.html.twig', array(
            'projectId' => $projectId,
            'entity' => $entity,
            'form' => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing KeyDates entity.
     *
     * @Route("/edit-keydate/{id}/{projectId}", name="keydates_edit")
     */
    public function editAction($id, $projectId)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:KeyDates')->find($id);

        $editForm = $this->createForm(new KeyDatesType(), $entity);

        $request = $this->getRequest();

        if ($request->isMethod('POST')) {
            $editForm->bind($request);
            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                return $this->redirect($this->generateUrl('projects_details', array('id' => $projectId)));
            }
        }

        return $this->render('FullSixProjectForecastBundle:KeyDates:editPopup.html.twig', array(
            'entity' => $entity,
            'projectId' => $projectId,
            'form' => $editForm->createView(),
        ));
    }

}
