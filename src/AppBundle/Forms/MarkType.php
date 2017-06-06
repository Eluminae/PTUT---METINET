<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Dtos\RealisationMarkDto;
use AppBundle\Models\Notation;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

class MarkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('value', IntegerType::class)
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $markTypeDto = $event->getData();
                $form = $event->getForm();

                if ($markTypeDto->realisation->getCampaign()->getNotation()->getMarkType() === Notation::NUMBER) {
                    $form->add('value', IntegerType::class, [
                        'label_format' => $markTypeDto->realisation->getName(),
                        'attr' => [
                            'class' => 'mark⁻input',
                            'min' => 0,
                            'max' => $markTypeDto->realisation->getCampaign()->getNotation()->getMarkTypeNumber(),
                        ],
                    ]);
                } elseif ($markTypeDto->realisation->getCampaign()->getNotation()->getMarkType() === Notation::RANKING) {
                    $form->add('value', IntegerType::class, [
                        'label_format' => $markTypeDto->realisation->getName(),
                        'attr' => [
                            'class' => 'mark⁻input',
                            'min' => 1,
                            'disabled' => true,
                        ],
                    ]);
                }
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
