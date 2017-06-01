<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Dtos\RealisationMarkDto;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class MarkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('idRealisation', HiddenType::class)
            ->add('value', IntegerType::class)
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            	$markTypeDto = $event->getData();
            	$form = $event->getForm();

            	$form->add('value', IntegerType::class, [
            		'label_format' => $markTypeDto->realisation->getName(),
            	]);
        	})
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => RealisationMarkDto::class,
        ));
    }
}
