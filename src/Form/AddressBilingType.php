<?php

namespace App\Form;

use App\Entity\AddressBiling;
use App\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressBilingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('addressLine1', TextType::class, [
                'label' => 'Address',
            ])
            ->add('addressLine2', TextType::class, [
                'label' => 'Complete adr.',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('zipecode')
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
            ])
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'name',
           
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddressBiling::class,
            'allow_extra_field' => true,
        ]);
    }
}
