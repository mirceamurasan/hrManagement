<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\Roles;
use FullSix\ProjectForecastBundle\Form\RolesType;
use APY\DataGridBundle\Grid\Source\Entity;

/**
 * Roles controller.
 *
 * @Route("/administration/roles")
 */
class RolesController extends Controller
{
    /**
     * Lists all Roles entities.
     *
     * @Route("/", name="roles")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        foreach ($this->getSf2RolesList() as $sf2Role) {
            if ($em->getRepository('FullSixProjectForecastBundle:Roles')->findOneBy(array("value" => $sf2Role)) === null) {
                $entity  = new Roles();
                $entity->setName($sf2Role);
                $entity->setValue($sf2Role);
                $entity->setRoleForUser(false);
                $em->persist($entity);
            }
        }
        $em->flush();

        $source = new Entity('FullSixProjectForecastBundle:Roles');
        $grid = $this->get('grid');
        $grid->setSource($source);
        $grid->hideFilters();

        return $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig', 
            array(
                'title' => 'Roles',
            )
        );
    }

    /**
     * Create a list of SF2 Roles.
     *
     */
    public function getSf2RolesList()
    {
        $tab_sf2roles = array();
        $tab_roleshierarchy = $this->container->getParameter('security.role_hierarchy.roles');
        foreach ($tab_roleshierarchy as $rolename => $roleshierarchy) {
            $tab_sf2roles[$rolename] = $rolename;
            foreach ($roleshierarchy as $role) {
                $tab_sf2roles[$role] = $role;
            }
        }

        return $tab_sf2roles;
    }

}
