<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class IdentityRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Prenom',
                    'class' => 'wc-admin-inputForm',
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'wc-admin-inputForm',
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Email',
                    'class' => 'wc-admin-inputForm',
                ]
            ])
            ->add('officialGroup', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Entité de rattachement',
                    'class' => 'wc-admin-inputForm',
                ]
            ])
        ;
    }
}
