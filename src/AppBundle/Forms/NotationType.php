<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class NotationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('markType', ChoiceType::class, array(
                'choices' => array(
                    'Évaluer par note' => '2',
                    'Évaluer par classement' => '1'
                ),
                'expanded' => true,
                'multiple' => false
            ))
            ->add('markTypeNumber', TextType::class)
        ;
    }
}
