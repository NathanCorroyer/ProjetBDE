<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\Place;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('startingDateTime', null, [
                'widget' => 'single_text',
            ])
            ->add('duration', null, [
                'widget' => 'single_text',
            ])
            ->add('inscriptionLimitDate', null, [
                'widget' => 'single_text',
            ])
            ->add('maxInscription')
            ->add('description')
            ->add('state')
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('planner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'id',
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
