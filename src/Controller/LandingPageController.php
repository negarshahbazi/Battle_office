<?php

namespace App\Controller;

use App\Entity\AddressBiling;
use App\Entity\AddressShipping;
use App\Entity\Client;
use App\Form\AddressLine1Type;
use App\Form\AddressLine2Type;
use App\Form\ClientType;
use App\Repository\AddressBilingRepository;
use App\Repository\AddressShippingRepository;
use App\Repository\ClientRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LandingPageController extends AbstractController
{
    public function calculateTotalPrice(float $originalPrice, float $price): float
    {
        return $originalPrice - $price;
    }  


    
    public function enregistrementDeLaCommande(): Response
    {
        // Instancier le client Guzzle
        $client = new \GuzzleHttp\Client();


        
     

        // Préparer le header avec le token d'authentification
        $headers = [
            'Authorization' => 'Bearer mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX',
        ];

        // Envoyer la requête POST à l'API Centrale
        $response = $client->request('POST', 'https://api-commerce.simplon-roanne.com/order', [
            'headers' => $headers,
            'json' => [
                'order' => [
                    'id' => 1,
                    'product' => 'Nerf Elite Jolt',
                    'payment_method' => 'paypal',
                    'status' => 'WAITING',
                    'client' => [
                        'firstname' => $client->getFirstName(), // 
                        'lastname' => $client->getlastName(),
                        'email' => $client->getEmail()
                    ],
                    'addresses' => [
                        'billing' => [
                            'address_line1' => '1, rue du test',
                            'address_line2' => '3ème étage',
                            'city' => 'Lyon',
                            'zipcode' => '69000',
                            'country' => 'France',
                            'phone' => 'string'
                        ],
                        'shipping' => [
                            'address_line1' => '1, rue du test',
                            'address_line2' => '3ème étage',
                            'city' => 'Lyon',
                            'zipcode' => '69000',
                            'country' => 'France',
                            'phone' => 'string'
                            ]
                            ]
                            ]
                        ]
                    ]);
                    
                        
                    // Traiter les données de réponse, enregistrer l'ID de commande dans la BDD locale, par exemple :
       
                    
                    // Rediriger vers la page de paiement avec l'ID de l'Order provenant de l'API
                    return $this->redirectToRoute('confirmation');
                
                
                }


    #[Route('/', name: 'landing_page')]
    public function index(Request $request, AddressBilingRepository $addressBiling, AddressShippingRepository $addressShipping ,EntityManagerInterface $entityManager,ProductRepository $productRespitory): Response
    {
        $products = $productRespitory -> findAll();
        $client = new Client();
        $formClient = $this->createForm(ClientType::class, $client);
        $formClient->handleRequest($request);
        if ($formClient->isSubmitted() && $formClient->isValid()) {
     
            
            $formClient->get('addressBiling')->getData();
            $formClient->get('addressShipping')->getData();
            $entityManager->persist($client);
            $entityManager->flush();
            dd();
            // D'abord ON AJOUTE DES CLIENTS ET UN ORDER DANS LA BDD
            // $order = new Order();
            // $order->setClient($client);
            // $order->addProduct($product);
            // ajouter method payment plus tard;
            // $this->enregistrementDeLaCommande($order);
            
            return $this->redirectToRoute('landing_page',[], Response::HTTP_SEE_OTHER);

        }
        $productTotalPrices = [];
        foreach ($products as $product) {
            $originalPrice = (float) $product->getFirstPrice();
            $price = (float) $product->getPrice();
            $productTotalPrices[$product->getId()] = $this->calculateTotalPrice($originalPrice, $price);
        }

  

    

        return $this->render('landing_page/index_new.html.twig', [
            'client' =>$client,
            'formClient' => $formClient,
            'products' => $products,
            'productTotalPrices' => $productTotalPrices,
        ]);
    }

    #[Route('/confirmation', name: 'confirmation')]
    public function confirmation(): Response
    {
        return $this->render('landing_page/confirmation.html.twig');
    }
}