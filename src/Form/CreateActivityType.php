<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\Place;
use App\Entity\User;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('startingDateTime', DateType::class, [
                'label' => 'Date et heure de la sortie',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['name' => 'startDate',]
            ])
            ->add('inscriptionLimitDate', DateType::class, [
                'label' => 'Date limite d\'inscription',
                'widget' => 'single_text',
                'required' => false,
                'attr' => ['name' => 'endDate',]
            ])
            ->add('maxInscription', TextType::class, [
                'label' => 'Nombre de places'
            ])
            ->add('duration', TimeType::class, [
                'label' => 'Durée (en minutes)',
            ])
            ->add('description')
            ->add('state')
            ->add('users', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'name',
            ])
            ->add('place', EntityType::class, [
                'class' => Place::class,
                'choice_label' => 'adress',
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
