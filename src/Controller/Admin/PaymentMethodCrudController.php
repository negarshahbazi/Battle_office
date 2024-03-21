<?php

namespace App\Controller\Admin;

use App\Entity\PaymentMethod;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\JsonResponse;

class PaymentMethodCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PaymentMethod::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [

            TextField::new('name'),

        ];
    }


  
}
