<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\City;
use App\Entity\Place;
use App\Entity\User;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

                'widget' => 'single_text',
                'required' => false,
                'attr' => ['name' => 'startDate',]
            ])
            ->add('inscriptionLimitDate', DateType::class, [

                'widget' => 'single_text',
                'required' => false,
                'attr' => ['name' => 'endDate',]
            ])
            ->add('maxInscription', IntegerType::class, [

            ])
            ->add('durationInMinutes', IntegerType::class, [
                'mapped'=>false,
                'label' => 'Durée (en minutes)',
                'required' => true, // ou false selon vos besoins
            ])

            ->add('city', EntityType::class, [
                'label' => 'Ville',
                'class' => City::class,
                'choice_label' => 'name',
                'mapped' => false,
                'required' => true,
                'attr'=>[
                    'class' => 'city-selector',
                ]
            ])
            ->add('place', EntityType::class, [
                        'class' => Place::class,
                        'placeholder'=>'Choisissez un lieu',
                        'required' =>true,
                        'choice_label'=>'name',
                'attr' => [
                    'class' => 'place-selector'
                ]
            ])

            ->add('adresse', TextType::class, [
                'mapped'=>false,
                'disabled'=>true,
                'label' => 'Adresse: ',
                'label_attr' => ['class'=>'labels-hidden label-adresse',
                    'style' => 'display:none'],
                'attr' => ['class' => 'form-control adresse',
                    'id'=>'adresse',
                    'style' => 'display:none'],
            ])
               ->add('zipcode', TextType::class, [
                    'mapped'=>false,
                    'label' => 'Code Postal: ',
                   'disabled'=>true,
                   'label_attr' => ['class'=>'labels-hidden label-zipcode',
                       'style' => 'display:none'],
                    'attr' => ['class' => 'form-control zipcode',
                    'id'=>'zipcode',
                    'style' => 'display:none'],
            ])
            ->add('coordinates', TextType::class, [
                'mapped'=>false,
                'disabled'=>true,
                'label' => 'Latitude/Longitude: ',
                'label_attr' => ['class'=>'labels-hidden label-coordinates',
                    'style' => 'display:none'],
                'attr' => ['class' => 'form-control coordinates',
                    'id'=>'coordinates',
                    'style' => 'display:none'],
            ])




            ->add('description')

            -> add('save', SubmitType::class, [
                'label' => 'Enregister',
                'attr' => [
                    'class' => 'btn btn-outline-primary',
                    'style' => 'margin-top:10%'
                ]
            ])

            -> add('publish', SubmitType::class, [
                'label' => 'Publier',
                'attr' => [
                    'class' => 'btn btn-primary',
                    'style' => 'margin-top:10%'
                ]
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
