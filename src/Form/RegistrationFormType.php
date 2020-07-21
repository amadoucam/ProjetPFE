<?php

namespace App\Form;

use App\Entity\Recruteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom_entreprise', TextType::class)
            ->add('adresse', TextareaType::class)
            ->add('code_postal', NumberType::class)
            ->add('ville', TextType::class)
            ->add('secteur_activite')
            ->add('pays', CountryType::class)
            ->add('description', TextareaType::class)
            ->add('civility', ChoiceType::class,[
                'choices' => [
                    'monsieur' => 'monsieur',
                    'madame' => 'madame',
                    'autre' => 'autre',
                ],
            ])
            ->add('phone', TelType::class)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('confirm_password', PasswordType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recruteur::class,
        ]);
    }
}
