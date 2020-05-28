<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('username')
            //->add('password')
            ->add('nom')
            ->add('prenom')
            ->add('datenaissance', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('nationalite', CountryType::class)
            ->add('adresse', TextareaType::class)
            ->add('avatar', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            //'data_class' => null,
            ->add('postal_code')
            ->add('civility', ChoiceType::class,[
                'choices' => [
                    'monsieur' => 'monsieur',
                    'madame' => 'madame',
                    'autre' => 'autre',
                ],
            ])
            ->add('tel')
            ->add('level',TextType::class)
            //'data_class' => 'Symfony\Component\HttpFoundation\File\File', 'property_path' => 'picture

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
