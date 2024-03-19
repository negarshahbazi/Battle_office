<?php

namespace App\Form;

use App\Entity\AddressBiling;
use App\Entity\AddressShipping;
use App\Entity\Client;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'type' =>'text'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('email')
            ->add('confirmationEmail')
            ->add('addressBiling', AddressBilingType::class)
            ->add('addressShipping', AddressShippingType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
            'allow_extra_field' => true,
        ]);
    }
}
