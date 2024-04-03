<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Place;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreatePlaceFromActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('name', TextType::class,[
                'label' => 'Nom du lieu',
                'label_attr' => [
                    'class' => 'name-label'
                ],
                'attr' => [
                    'class' => 'form-control name'
                ]
            ])
            ->add('adress', TextType::class, [
                'label' => 'Adresse',
                'label_attr' => [
                    'class' => 'adress-label'
                ],
                'attr' => [
                    'class' => 'form-control adress'
                ]
            ])
            ->add('latitude', NumberType::class, [
                'scale' => 8,
                'label' => 'Latitude',
                'label_attr' => [
                    'class' => 'latitude-label'
                ],
                'attr' => [
                    'class' => 'form-control latitude'
                ]
            ])
            ->add('longitude', NumberType::class, [
                'scale' => 8,
                'label' => 'Longitude',
                'label_attr' => [
                    'class' => 'longitude-label'
                ],
                'attr' => [
                    'class' => 'form-control longitude'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'attr' => [
                    'value' => 'Créer'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
        ]);
    }
}
