<?php
/**
 * Created by PhpStorm.
 * User: Benjamin
 * Date: 13/10/2015
 * Time: 15:08
 */

namespace AppBundle\Service\Business;

use AppBundle\Entity\Invoice;
use AppBundle\Exception\BusinessException;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\Service;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class InvoiceManager
 * @package AppBundle\Service\Business
 * @Service("invoice_manager")
 */
class InvoiceManager
{
    const INITIAL_DELAY = "PT4H";

    /**
     * @var BusinessHoursManager
     * @Inject("business_hours_manager")
     */
    public $businessHoursManager;

    /**
     * @var RegistryInterface
     * @Inject("doctrine")
     */
    public $doctrine;

    public function approveInvoice(Invoice $invoice, \DateTime $when = null)
    {
        if($invoice->getStatus() != Invoice::STATUS_INITIAL)
        {
            throw new BusinessException("only initial invoice can be approved.");
        }
        // if when is null, set it to now
        if ($when == null) {
            $when = new \DateTime();
        }
        if($invoice->getDueDate()->getTimestamp() < $when->getTimestamp())
        {
            throw new BusinessException("dueDast has past.");
        }
        $sendAfterDate = $this->computeSendInvoiceAfter($invoice, $when);
        $invoice->setSendEmailAfter($sendAfterDate);
        $invoice->setStatus(Invoice::STATUS_APPROVED);
        $this->saveInvoice($invoice);
        return true;

    }

    public function computeSendInvoiceAfter(Invoice $invoice, \DateTime $when)
    {
        $sendAfterDate = clone $when;
        $sendAfterDate->add(new \DateInterval(self::INITIAL_DELAY));
        return $sendAfterDate;
    }

    public function isDateTimeInBusinessHours(\DateTime $date)
    {
        return $this->businessHoursManager->isDateTimeInBusinessHours($date);
    }

    public function saveInvoice(Invoice $invoice)
    {
        // call the invoice persister... SHORTCUT!
        $entityManager = $this->doctrine->getEntityManager();
        $entityManager->persist($invoice);
        $entityManager->flush();
        return $invoice;
    }

}