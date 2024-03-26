<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\User;
use App\Repository\CampusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltersType extends AbstractType
{
    private $security;
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
            ->add('campus', EntityType::class, [
                'label' => 'Campus :',
                'query_builder' => function (CampusRepository $cr) {
                    return $cr->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC'); // Tri par le champ 'name' dans l'ordre alphabétique
                },
                'class' => Campus::class,
                'choice_label' => 'name',
                'placeholder' => $this->user->getCampus()->getName(),

            ])
            ->add('searchbar', TextType::class, [
                'label' => 'Le nom de la sortie contient :',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
