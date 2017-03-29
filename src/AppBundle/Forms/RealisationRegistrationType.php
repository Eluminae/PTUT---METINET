<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RealisationRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('identities', CollectionType::class, array(
            //     'entry_type' => IdentityRegistrationType::class
            // ))
            ->add('identity', IdentityRegistrationType::class)
            ->add('file', FileType::class)
            ->add('name', TextType::class)
            ->add('submit', SubmitType::class)
        ;
    }
}
