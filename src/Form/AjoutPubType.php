<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Publications;
use App\Entity\User;

class AjoutPubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('texte', TextType::class, [
                'label' => 'Text',
                'attr' => ['class' => 'form-control custom-size'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Text ne doit pas etre vide .']),
                ],
            ])
            ->add('datedepublication', DateTimeType::class, [
                'label' => 'Date de Publication',
                'widget' => 'single_text', // Render as a single text input
                'html5' => true,            // Use HTML5 date input
                'attr' => [
                    'class' => 'form-control datepicker-input',
                    'data-toggle' => 'datepicker',
                    'data-target' => '#datepicker1',
                ],
                'invalid_message' => 'Entrez la date dans le format yyyy-MM-dd',
            ])
                

            
            ->add('specialiteAssocie', ChoiceType::class, [
                'label' => 'Specialite Associe',
                'choices' => [
                    'PEDIATRE' => 'PEDIATRE',
                    'CARDIOLOGUE' => 'CARDIOLOGUE',
                    'DERMATOLOGUE' => 'DERMATOLOGUE',
                    'GYNECOLOGUE' => 'GYNECOLOGUE',
                    'NEUROLOGUE' => 'NEUROLOGUE',
                    'OPHTALMOLOGUE' => 'OPHTALMOLOGUE',
                    'PSYCHIATRE' => 'PSYCHIATRE',
                ],
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'selectionner une specialite s il vous plait .']),
                ],
            ])
            ->add('tags', TextType::class, [
                'label' => 'Tags',
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Tags ne doivent pas etre vides.']),
                    new Assert\Regex([
                        'pattern' => '/^#/',
                        'message' => 'Tags doivent commencer par un #.',
                    ]),
                ],
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Select an Image',
                'required' => false,
                'mapped' => false,
            ])



           
            ->add('Save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publications::class,
        ]);
    }
}