<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class RealisationMarkRegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', NumberType::class)
            ->add('submit', SubmitType::class, ['label' => 'Noter'])
        ;
    }

    public function getName()
    {
        return 'app_bundle_realisation_mark_register';
    }
}
