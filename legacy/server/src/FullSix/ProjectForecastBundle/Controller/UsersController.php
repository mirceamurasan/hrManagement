<?php

namespace FullSix\ProjectForecastBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FullSix\ProjectForecastBundle\Entity\Users;
use FullSix\ProjectForecastBundle\Form\UsersType;
use FullSix\ProjectForecastBundle\Form\UsersAccountType;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use Symfony\Component\HttpFoundation\Response;
use FullSix\ProjectForecastBundle\Form\LoginPasswordType;
use FullSix\ProjectForecastBundle\Entity\Login;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * Users controller.
 *
 *
 */
class UsersController extends Controller
{

/*
-------------------------------------------
            < ADMINISTRATION >
-------------------------------------------
*/
    /**
     * Lists all Users entities.
     *
     * @Route("/workers/users/", name="users")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $source = new Entity('FullSixProjectForecastBundle:Users');
        $grid = $this->get('grid');
        $grid->setSource($source);

        //hide workers
        $grid->setDefaultFilters(array(
                'worker.id' => array('operator' => 'isNull') ));

        $grid->setActionsColumnSeparator("&nbsp;&nbsp;");
        $rowAction_show = new RowAction("book", "users_show", false, "_self",
            array("title"=>$this->get('translator')->trans('common.action.show')));
        $rowAction_show->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_show);

        $rowAction_edit = new RowAction("edit", "users_edit", false, "_self",
            array("title"=>$this->get('translator')->trans('common.action.edit')));
        $rowAction_edit->setRouteParameters(array('id'));
        $grid->addRowAction($rowAction_edit);

        $rowAction_hire = new RowAction("user", "hire_worker", false, "_self",
            array("title"=>$this->get('translator')->trans('user.hire')));
        $rowAction_hire
            ->setRouteParameters(array('id'))
            ->manipulateRender(function(\APY\DataGridBundle\Grid\Action\RowAction $action, \APY\DataGridBundle\Grid\Row $row) {
                if ($row->getField('worker.id')) {
                    $action->setAttributes(array('hidden' => true));
                }
                return $action;
            });
        $grid->addRowAction($rowAction_hire);

        $grid->setDefaultOrder("login.username", "desc");

        $grid->getColumn('login.roles')->manipulateRenderCell(function($value, \APY\DataGridBundle\Grid\Row $row, $router) use ($em) {
            if($value){
                foreach ($value as $element) {
                    echo $element." ";
                }
            }
        });

        $grid->getColumn('login.expiresAt')->manipulateRenderCell(function($value, $row, $router) {
           return empty($value) || $value >= new \DateTime() ? 'Oui' : 'Non';
        });

        return $grid->getGridResponse('FullSixProjectForecastBundle::grid.html.twig',
            array(
                'title' => 'Users',
                'entity_new' => "users_new"
                )
            );
    }

    /**
     * Finds and displays a Users entity.
     *
     * @Route("/workers/users/{id}/show", name="users_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Users')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Users entity.');
        }

        $message = '';
        if($entity->getLogin()){
            if ($entity->getLogin()->isDirty()) {
                $message = 'user.warning.is_dirty';
            }else {
                $alternateLogin = $em->getRepository('FullSixProjectForecastBundle:Login')->findOneBy(array('username' => $entity->getLogin()->getUsername(), 'dirty' => true));
                if ($alternateLogin instanceof UserInterface) {
                    $message = 'user.warning.existsDirty';
                }
            }
        }

        return array(
            'entity'    => $entity,
            'message'   => $message
        );
    }

    /**
     * Displays a form to create a new Users entity.
     *
     * @Route("/workers/users/new", name="users_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Users();
        $em = $this->getDoctrine()->getManager();
        $roles = $em->getRepository('FullSixProjectForecastBundle:Roles')->getRolesAsArray(false);

        $form   = $this->createForm(new UsersType($roles), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Users entity.
     *
     * @Route("/workers/users/create", name="users_create")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Users:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Users();
        $em = $this->getDoctrine()->getManager();
        $roles = $em->getRepository('FullSixProjectForecastBundle:Roles')->getRolesAsArray(false);
        $form = $this->createForm(new UsersType($roles), $entity);

        $form->bind($request);
//var_dump($form->isValid());
//var_dump($form->getErrorsAsString());
//die("createAction");
        if ($form->isValid()) {
            if($entity->getLogin()){
                $login = $entity->getLogin()->setEmail($entity->getEmail());
                $login->setEnabled(true);
                $em->persist($login);
                $em->flush();
                $entity->setLogin($login);
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('users'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Users entity.
     *
     * @Route("/workers/users/{id}/edit", name="users_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Users')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Users entity.');
        }
        $message = '';
        if($entity->getLogin()){
            if ($entity->getLogin()->isDirty()) {
                $message = 'user.warning.is_dirty';
            }else {
                $alternateLogin = $em->getRepository('FullSixProjectForecastBundle:Login')->findOneBy(array('username' => $entity->getLogin()->getUsername(), 'dirty' => true));
                if ($alternateLogin instanceof UserInterface) {
                    $message = 'user.warning.existsDirty';
                }
            }
        }

        $requiredLogin = ( !$entity->getWorker() || $entity->getLogin());
        /*if the user already have a login => edit login action => no password
        * otherwise new login => password needed
        */
        $showPass = ( false || !$entity->getLogin());
        $readonly = ($entity->getLogin() && $entity->isAdLinked());

        $onlyUserRoles =  $entity->getWorker() ? false : true;
        $roles = $em->getRepository('FullSixProjectForecastBundle:Roles')->getRolesAsArray($onlyUserRoles);
        $editForm = $this->createForm(new UsersType($roles, $requiredLogin, $showPass, $readonly), $entity);

        return array(
            'entity'      => $entity,
            'message'      => $message,
            'form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Users entity.
     *
     * @Route("/workers/users/{id}/update", name="users_update")
     * @Method("POST")
     * @Template("FullSixProjectForecastBundle:Users:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('FullSixProjectForecastBundle:Users')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Users entity.');
        }

        $requiredLogin = ( !$entity->getWorker() || $entity->getLogin());
        /*if the user already have a login => edit login action => no password
        * otherwise new login => password needed
        */
        $showPass = ( false || !$entity->getLogin());
        $readonly = ($entity->getLogin() && $entity->isAdLinked());

        $onlyUserRoles =  $entity->getWorker() ? false : true;
        $roles = $em->getRepository('FullSixProjectForecastBundle:Roles')->getRolesAsArray($onlyUserRoles);
        $editForm = $this->createForm(new UsersType($roles, $requiredLogin, $showPass, $readonly), $entity);

        $editForm->bind($request);

        if ($editForm->isValid()) {
            if($entity->getLogin()){
                $em->persist($entity->getLogin()->setEmail($entity->getEmail()));
            }
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('users_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
        );
    }


/*
-------------------------------------------
            </ ADMINISTRATION >
-------------------------------------------
*/






/*
-------------------------------------------
            < FO USERS >
-------------------------------------------
*/

    /**
     * Displays a form to edit an existing Users entity.
     *
     * @Route("/users/account/edit", name="users_account")
     * @Template()
     */
    public function accountAction(Request $request)
    {
        $login = $this->getUser();
        $user = $login->getUser();
        $em = $this->getDoctrine()->getManager();

       if (!$user) {
            throw $this->createNotFoundException('Unable to find Users entity.');
        }

        $readonly = ($user->getLogin() && $user->isAdLinked());
        $editForm = $this->createForm(new UsersAccountType($readonly), $user);

        if( $request->isMethod('POST') )
        {
            $editForm->bind($request);
            if ($editForm->isValid())
            {
                if($user->getLogin()){
                    $em->persist($user->getLogin()->setEmail($user->getEmail()));
                }
                $em->persist($user);
                $em->flush();
                return $this->redirect($this->generateUrl('users_account'));
            }
        }

        return array(
            'entity' => $user,
            'form'   => $editForm->createView()
        );

    }

    /**
     * Popup.
     *
     * @Route("/merge-popup/{id}", name="users_merge_popup")
     */
    public function popupMergeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Users')->find($id);

        if ($entity->getWorker()) {
            $users = $em->getRepository('FullSixProjectForecastBundle:Users')->getUsersForMergeWhichAreNotWorkers($id);
        }else{
            $users = $em->getRepository('FullSixProjectForecastBundle:Users')->getUsersForMerge($id);
        }

        return $this->render('FullSixProjectForecastBundle:Users:mergePopup.html.twig', array(
            'entity' => $entity,
            'users' => $users,
        ));
    }

    /**
     * Merging Action
     *
     * @Route("/merge/{id}", name="users_merge")
     * @Method("POST");
     */
    public function mergeAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->get('user')) {
            if ($request->get('merge') == "destination") {
               $merge = $this->container->get('fullsix_projectforecast.users_helper')->mergeTwoUsers($id, $request->get('user'));
            } elseif ($request->get('merge') == "source") {
                $merge = $this->container->get('fullsix_projectforecast.users_helper')->mergeTwoUsers($request->get('user'), $id);
            } else {
                return new Response(json_encode(array('status' => "NOT_OK", 'messages' => $this->get('translator')->trans('user.mergeError.source'))));
            }
        } else {
            return new Response(json_encode(array('status' => "NOT_OK", 'messages' => $this->get('translator')->trans('user.mergeError.user'))));
        }
        if ($merge == false) {
            return new Response(json_encode(array('status' => "NOT_OK", 'messages' => $this->get('translator')->trans('user.mergeError.workers'))));
        }

        $em->flush();

        return new Response(json_encode(array('status' => "OK")));
    }

     /**
     * Change password action
     *
     * @Route("/change-password-popup/{id}", name="users_change_password_popup")
     */
    public function changePasswordPopupAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('FullSixProjectForecastBundle:Login')->find($id);

        $form   = $this->createForm(new LoginPasswordType(), $entity);

        return $this->render('FullSixProjectForecastBundle:Login:changePassword.html.twig', array(
            'form' => $form->createView(),
            'entity' => $entity,
        ));
    }

    /**
     * Change password action
     *
     * @Route("/change-password/{id}", name="users_change_password")
     */
    public function changePasswordAction(Request $request, $id)
    {
        $entity = new Login();
        $form = $this->createForm(new LoginPasswordType(), $entity);
         if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $login = $em->getRepository('FullSixProjectForecastBundle:Login')->find($id);
                $login->setPassword(null);
                $login->setPlainPassword($entity->getPlainPassword());

                $em->persist($login);
                $em->flush();

                return new Response(json_encode(array('status' => "OK")));
            } else {
                return new Response(json_encode(array('status' => "NOT_OK", 'messages' => $this->get('translator')->trans('login.label.samePassword'))));
            }
         }

    }

    /**
     * Change password action
     *
     * @Route("/change-password-account/", name="users_change_password_account")
     */
    public function changePasswordAccountAction()
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        $form = $this->container->get('fos_user.change_password.form');
        $formHandler = $this->container->get('fos_user.change_password.form.handler');

        $process = $formHandler->process($user);
        if ($process) {
            $this->get('session')->getFlashBag()->add('success','Votre mot de passe a été changé avec succès');

            return $this->redirect($this->generateUrl('users_account'));
        }

        return $this->container->get('templating')->renderResponse(
            'FullSixProjectForecastBundle:Users:changePassword.html.'.$this->container->getParameter('fos_user.template.engine'),
            array('form' => $form->createView())
        );
    }
}
