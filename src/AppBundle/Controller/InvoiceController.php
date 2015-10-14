<?php

namespace AppBundle\Controller;

use AppBundle\WebsiteNotification;
use Symfony\Component\HttpFoundation\Request;
use \Walva\CrudAdminBundle\Controller\CrudController as Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Invoice;
use AppBundle\Form\InvoiceType;

/**
 * Invoice controller.
 *
 * @Route("/admin/invoice")
 */
class InvoiceController extends Controller
{

    function __construct()
    {
        $this->setRoutes(array(
            self::$ROUTE_INDEX_ADD    => 'admin_invoice_new',
            self::$ROUTE_INDEX_INDEX  => 'admin_invoice',
            self::$ROUTE_INDEX_DELETE => 'admin_invoice_show',
            self::$ROUTE_INDEX_EDIT   => 'admin_invoice_edit',
            self::$ROUTE_INDEX_SHOW   => 'admin_invoice_show',
        ));

        $this->setLayoutPath('AppBundle:Invoice:layout.html.twig');
        $this->setIndexPath("AppBundle:Invoice:index.html.twig");
        $this->setShowPath("AppBundle:Invoice:show.html.twig");
        $this->setEditPath("AppBundle:Invoice:edit.html.twig");
        $this->setNewPath("AppBundle:Invoice:new.html.twig");

        $this->setColumnsHeader(array(
            "Id",
        ));
    }

    /**
     * Finds and displays a Invoice entity.
     *
     * @Route("/{invoice}/approve", name="admin_invoice_approve")
     * @Method("GET")
     */
    public function approveAction(Invoice $invoice)
    {
        $service = $this->get("invoice_manager");
        $service->approveInvoice($invoice);
        $session = $this->get('session');
        $session->getFlashBag()->add(WebsiteNotification::SUCCESS, "your invoice has been sent for approval.");
        return $this->redirect($this->generateUrl("admin_invoice_show", ["id" => $invoice->getId()]));
    }


    /**
     * Lists all Invoice entities.
     *
     * @Route("/", name="admin_invoice")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        return parent::indexAction();
    }

    /**
     * Creates a new Invoice entity.
     *
     * @Route("/", name="admin_invoice_create")
     * @Method("POST")
     * @Template("AppBundle:Invoice:new.html.twig")
     */
    public function createAction(Request $request)
    {
        return parent::createAction($request);

    }

    /**
     * Creates a form to create a Invoice entity.
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function createCreateForm(Invoice $entity)
    {
        $form = $this->createForm(new InvoiceType(), $entity, array(
            'action' => $this->generateUrl('admin_invoice_new'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Invoice entity.
     *
     * @Route("/new", name="admin_invoice_new")
     * @Method("GET|POST")
     * @Template()
     */
    public function new2Action(Request $request)
    {
        $entity = $this->createEntity();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        /* @var $form \Symfony\Component\Form\Form */

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl(
                $this->getRouteShow(), array('id' => $entity->getId())));
        }

        $params = $this->getRenderParams();
        $params['form'] = $form->createView();
        $params['entity'] = $entity;

        return $this->renderNewAction($params);


    }

    /**
     * Finds and displays a Invoice entity.
     *
     * @Route("/{id}", name="admin_invoice_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        return parent::showAction($id);
    }

    /**
     * Displays a form to edit an existing Invoice entity.
     *
     * @Route("/{id}/edit", name="admin_invoice_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        return parent::editAction($id);

    }

    /**
     * Creates a form to edit a Invoice entity.
     *
     * @param Invoice $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function createEditForm(Invoice $entity)
    {
        $form = $this->createForm(new InvoiceType(), $entity, array(
            'action' => $this->generateUrl('admin_invoice_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Invoice entity.
     *
     * @Route("/{id}", name="admin_invoice_update")
     * @Method("PUT")
     * @Template("AppBundle:Invoice:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        return parent::updateAction($request, $id);

    }

    /**
     * Deletes a Invoice entity.
     *
     * @Route("/{id}", name="admin_invoice_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        return parent::deleteAction($request, $id);

    }

    /**
     * Creates a form to delete a Invoice entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    public function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_invoice_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm();
    }


    public function createEntity()
    {
        return new Invoice();
    }

    public function getRepository()
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('AppBundle:Invoice');
    }
}
