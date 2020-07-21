<?php

namespace App\Form;

use App\Entity\PropertySearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PropertySearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
         /*   ->add('title', TextType::class,[
                'label' => 'Titre',
            ])  */
            ->add('ville')
        /*    ->add('categorie', ChoiceType::class,[
                'choices' => [
                    '-- faite un choix' => '',
                    'gestion' => 'Gestion',
                    'informatique' => 'Informatique',
                    'economie' => 'Economie',
                ],
            ])  */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PropertySearch::class,
        ]);
    }
}
