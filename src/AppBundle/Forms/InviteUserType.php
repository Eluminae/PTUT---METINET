<?php

namespace AppBundle\Forms;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InviteUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'email',
                EmailType::class
            );

        if ($options['isAdmin']) {
            $builder->add(
                'role',
                ChoiceType::class,
                [
                    'choices' => [
                        'Administrateur' => 'ROLE_ADMIN',
                        'Administrateur de campagne' => 'ROLE_CAMPAIGN_ADMIN',
                    ],
                ]
            );
        }

        $builder->add('submit', SubmitType::class, ['label' => 'Inviter']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('isAdmin');

    }

    public function getName()
    {
        return 'app_bundle_invite_user_type';
    }
}
