<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\Campus;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileModifierType extends AbstractType
{      private $security;
    /** @var User $user */
    private $user;

    public function __construct(Security $security)
    {
        $this->security = $security;

        $this->user = $this->security->getUser();
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastName', TextType::class, [
                'label'=>'Nom: ',
                'label_attr' => ['class'=>'labels'],
                'attr'=>[
                    'pattern'=>"[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,}",
                    'value'=>$this->user->getLastName(),
                    'class'=>'form-control'
                ]

            ])
            ->add('firstName',TextType::class,[
                'label'=>'Prénom: ',
                'label_attr' => ['class'=>'labels'],
                'attr'=>[
                    'pattern'=>"[A-Za-zÀ-ÖØ-öø-ÿ\s'-]{2,}",
                    'value'=>$this->user->getFirstName(),
                    'class'=>'form-control'
                ]
            ] )
            ->add('email', EmailType::class, [
                'label' => 'Email: ',
                'required' => false,
                'mapped' => false,
                'attr'=>[
                     'value'=>$this->user->getEmail(),
                    'class'=>'form-control'

                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field form-control']],
                'required' => false,
                'first_options'  => ['label' => 'Mot de passe: ', 'label_attr'=>['class'=>'labels'], 'attr'=>['class'=>'form-control']],
                'second_options' => ['label' => 'Confirmation du mot de passe: ', 'label_attr'=>['class'=>'labels'], 'attr'=>['class'=>'form-control']],

            ])

            ->add('phone', TextType::class, [
                'label' => 'Numéro de téléphone: ',
                'label_attr' => ['class'=>'labels'],
                'mapped'=>false,
                'attr'=>[
                    'value'=>$this->user->getPhone(),
                    'class'=>'form-control',
                    'pattern'=>'/^(0|\+33)[1-9]([-. ]?[0-9]{2}){4}$/'
                ]
            ])
            ->add('avatarFile',FileType::class, [
                'label'=>'Modifier votre photo de profil',
                'label_attr' => ['class'=>'labels'],
                'mapped' => false,
                'required' => false,

            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
