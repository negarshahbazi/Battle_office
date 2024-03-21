<?php

namespace App\Form;

use App\Entity\AddressShipping;
use App\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressShippingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addressLine1', TextType::class, [
                'label' => 'Address',
                'required' => false,// Indiquer que ce champ n'est pas obligatoire
            ])
            ->add('addressLine2', TextType::class, [
                'label' => 'Complete adr.',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => false,// Indiquer que ce champ n'est pas obligatoire
            ])
            ->add('zipecode', TextType::class, [
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,// Indiquer que ce champ n'est pas obligatoire
            ])
            ->add('nom', TextType::class, [
                'required' => false,// Indiquer que ce champ n'est pas obligatoire
            ])
            ->add('prenom', TextType::class, [
                'required' => false,// Indiquer que ce champ n'est pas obligatoire
            ])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name',
                'required' => false,// Indiquer que ce champ n'est pas obligatoire
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddressShipping::class,
            'allow_extra_field' => true,
        ]);
    }
}
