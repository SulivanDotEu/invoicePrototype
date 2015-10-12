<?php

namespace AppBundle\Controller;

use AppBundle\WebsiteNotification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PublicController extends Controller
{
    /**
     * @Route("/zz", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
        ));
    }

    /**
     * @Route("/", name="homepage")
     * @Template()
     */
    public function homeAction(Request $request)
    {
        return [];
    }

    /**
     * @Route("/send-invoice", name="send_invoice")
     * @Template()
     */
    public function newInvoiceAction(Request $request)
    {
        $form = $this->_getInvoiceForm();
        $form->handleRequest($request);
        if($form->isValid())
        {
            $session = $this->get('session');
            $session->getFlashBag()->add(WebsiteNotification::SUCCESS, "your invoice has been sent for approval.");
            return $this->redirect($this->generateUrl("homepage"));
        }
        return [
            "form" => $form->createView(),
        ];
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function _getInvoiceForm()
    {
        $form = $this->createFormBuilder()
            ->add('name', 'text')
            ->add("customer", 'email')
            ->add("owner", 'email')
            ->add("submit", 'submit')
            ->getForm();
        return $form;
    }
}
