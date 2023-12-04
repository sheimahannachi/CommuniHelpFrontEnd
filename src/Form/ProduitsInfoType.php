<?php

namespace App\Form;

use App\Entity\ProduitsInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitsInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomProd')
            ->add('prixProd')
            ->add('descProd')
            ->add('statutProd', ChoiceType::class, [
                'choices' => [
                    'Available' => 'available',
                    'Not Available' => 'not_available',
                ],
                'placeholder' => 'Choose the status',
                'label' => 'Status',
                'required' => true, // Set as per your requirement
                'attr' => ['class' => 'form-control'], // Customize CSS class if needed
            ])            ->add('image', FileType::class, [
                'label' => 'Image (JPEG, PNG, or GIF file)',
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'mapped' => false, // Ne pas mapper le champ avec une propriété de l'entité
                'required' => false, // Facultatif, si vous ne voulez pas rendre le champ obligatoire
            ])
            ->add('save', SubmitType::class, [
                'label' => 'confirmer',
            ]);

        $builder->get('image')->addModelTransformer(new class() implements DataTransformerInterface {
            public function transform($value)
            {
                // Transform the value from your database to a format usable by the form field
                return null; // Modify this according to your data
            }

            public function reverseTransform($value)
            {
                // Transform the value from the form to the value saved in your database
                return $value; // Modify this according to your data
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProduitsInfo::class,
        ]);
    }
}
