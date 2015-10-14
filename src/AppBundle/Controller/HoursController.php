<?php

namespace AppBundle\Controller;

use AppBundle\WebsiteNotification;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class HoursController extends Controller
{

    /**
     * @Route("/test/hours", name="test_hours")
     * @Template()
     */
    public function testHoursAction(Request $request)
    {
        $form = $this->_getForm(["date" => new \DateTime()]);
        $form->handleRequest($request);
        if($form->isValid())
        {
            $service = $this->get('business_hours_manager');
            /** @var \DateTime $date */
            $date = $form->getData()["date"];
            $result = $service->isDateTimeInBusinessHours($form->getData()["date"]);
            $session = $this->get('session');
            $message = sprintf("Date %s is in business hours? %s",
                $date->format("d/m/Y H:i"),
                ($result? "YES": "NO"));
            $session->getFlashBag()->add(WebsiteNotification::SUCCESS, $message);
        }

        return $this->render("@App/Public/newInvoice.html.twig", [
            "form" => $form->createView(),
        ]);
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    protected function _getForm($data = null)
    {
        $form = $this->createFormBuilder($data)
            ->add('date', 'datetime')
            ->add("submit", 'submit')
            ->getForm();
        return $form;
    }
}
