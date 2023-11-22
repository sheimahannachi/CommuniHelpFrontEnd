<?php

namespace App\Form;

use App\Entity\Test;
use Doctrine\DBAL\Types\DateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType as TypeDateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', null, [
            'label' => 'Nom événement', // Changer l'étiquette du champ "nom"
            'constraints' => [
                new Regex([
                    'pattern' => '/^\D+$/',
                    'message' => 'Le champ nom ne doit pas contenir de chiffres.',
                ]),
            ],
        ])
        ->add('lieu', null, [
            'label' => 'Lieu événement', // Changer l'étiquette du champ "lieu"
        ])
        ->add('date', null, [
            'label' => 'Date événement', // Garder l'étiquette du champ "date"
        ])
            
   ->add('path', FileType::class, [
       'label' => 'Importer une image',
       'required' => false, // Le champ n'est pas obligatoire
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
