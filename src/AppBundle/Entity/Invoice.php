<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invoice
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\InvoiceRepository")
 */
class Invoice
{

    const STATUS_INITIAL = "initial";
    const STATUS_REJECTED = "rejected";
    const STATUS_APPROVED = "approved";
    const STATUS_SENT = "sent";

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dueDate", type="datetime")
     */
    private $dueDate;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="sellerEmail", type="string", length=255)
     */
    private $sellerEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="debtorEmail", type="string", length=255)
     */
    private $debtorEmail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sent", type="boolean", nullable=true)
     */
    private $sent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sendEmailAfter", type="datetime", nullable=true)
     */
    private $sendEmailAfter;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sentDate", type="datetime", nullable=true)
     */
    private $sentDate;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="estimated_sending_date", type="datetime", nullable=true)
     */
    private $estimatedSendingDate;

    function __construct()
    {
        $this->setDueDate(new \DateTime("tomorrow"));
        $this->setStatus(self::STATUS_INITIAL);
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     *
     * @return Invoice
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Invoice
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Invoice
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set sellerEmail
     *
     * @param string $sellerEmail
     *
     * @return Invoice
     */
    public function setSellerEmail($sellerEmail)
    {
        $this->sellerEmail = $sellerEmail;

        return $this;
    }

    /**
     * Get sellerEmail
     *
     * @return string
     */
    public function getSellerEmail()
    {
        return $this->sellerEmail;
    }

    /**
     * Set debtorEmail
     *
     * @param string $debtorEmail
     *
     * @return Invoice
     */
    public function setDebtorEmail($debtorEmail)
    {
        $this->debtorEmail = $debtorEmail;

        return $this;
    }

    /**
     * Get debtorEmail
     *
     * @return string
     */
    public function getDebtorEmail()
    {
        return $this->debtorEmail;
    }

    /**
     * Set sent
     *
     * @param boolean $sent
     *
     * @return Invoice
     */
    public function setSent($sent)
    {
        $this->sent = $sent;

        return $this;
    }

    /**
     * Get sent
     *
     * @return boolean
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * Set sendEmailAfter
     *
     * @param \DateTime $sendEmailAfter
     *
     * @return Invoice
     */
    public function setSendEmailAfter($sendEmailAfter)
    {
        $this->sendEmailAfter = $sendEmailAfter;

        return $this;
    }

    /**
     * Get sendEmailAfter
     *
     * @return \DateTime
     */
    public function getSendEmailAfter()
    {
        return $this->sendEmailAfter;
    }

    /**
     * Set sentDate
     *
     * @param \DateTime $sentDate
     *
     * @return Invoice
     */
    public function setSentDate($sentDate)
    {
        $this->sentDate = $sentDate;

        return $this;
    }

    /**
     * Get sentDate
     *
     * @return \DateTime
     */
    public function getSentDate()
    {
        return $this->sentDate;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param $status
     * @return Invoice
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEstimatedSendingDate()
    {
        return $this->estimatedSendingDate;
    }

    /**
     * @param \DateTime $estimatedSendingDate
     */
    public function setEstimatedSendingDate($estimatedSendingDate)
    {
        $this->estimatedSendingDate = $estimatedSendingDate;
    }

}

