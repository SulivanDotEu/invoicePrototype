<?php

namespace AppBundle\Form;

use AppBundle\Entity\Invoice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class InvoiceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dueDate', null, [
                "constraints" => [
                    new GreaterThanOrEqual("today")
                ]
            ])
            ->add('amount', "money", [
                "divisor" => 100
            ])
            ->add("status", "choice", [
                "constraints" => [
                    new NotBlank()
                ],
                "choices" => [
                    Invoice::STATUS_INITIAL => Invoice::STATUS_INITIAL,
                    Invoice::STATUS_REJECTED => Invoice::STATUS_REJECTED,
                    Invoice::STATUS_APPROVED => Invoice::STATUS_APPROVED,
                    Invoice::STATUS_SENT => Invoice::STATUS_SENT,
                ]
            ])
            ->add('reference', null, [
                "constraints" => [
                    new NotBlank()
                ]
            ])
            ->add('sellerEmail', "email", [
                "constraints" => [
                    new NotBlank()
                ]
            ])
            ->add('debtorEmail', "email", [
                "constraints" => [
                    new NotBlank()
                ]
            ])
            ->add('sent')
            ->add('sendEmailAfter')
            ->add('sentDate');
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Invoice'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_invoice';
    }
}
