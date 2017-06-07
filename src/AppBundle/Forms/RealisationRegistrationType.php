<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Count;

class RealisationRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // ->add('identities', CollectionType::class, array(
            //     'entry_type' => IdentityRegistrationType::class
            // ))
            ->add('file', FileType::class, array(
                'required' => true,
                'constraints' => array(
                    new File([
                        'mimeTypes' => array(
                            'application/pdf',
                            'application/x-pdf',
                            'image/bmp',
                            'image/jpeg',
                            'image/pjpeg',
                            'image/png',
                            'image/x-compressed',
                            'image/x-zip-compressed',
                            'image/zip',
                            'image/x-zip',
                        ),
                        'mimeTypesMessage' => 'Bad file extension',
                    ]),
                ),
            ))
            ->add('name', TextType::class)
            ->add('candidates', CollectionType::class, [
                'entry_type' => IdentityRegistrationType::class,
                'entry_options' => array(
                    'label' => false,
                ),
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'minMessage' => 'Vous devez indiquer au moins un candidat',
                    ]),
                ],
            ])
        ;
    }
}
