<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Entity\AddressBiling;
use App\Entity\AddressShipping;
use App\Entity\Client;
use App\Entity\Order;
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



    public function enregistrementDeLaCommande(Order $order): Response
    {
        // dd($order);
        // Instancier le client Guzzle
        $client = new \GuzzleHttp\Client();





        // Préparer le header avec le token d'authentification
        $headers = [
            'Authorization' => 'Bearer mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX',
        ];
        // Définir une méthode de paiement par défaut si elle n'est pas définie
        $paymentMethod = $order->getPaymentMethod() ?? 'paypal'; // Par exemple, utiliser 'paypal' par défaut

        // Envoyer la requête POST à l'API Centrale
        $response = $client->request('POST', 'https://api-commerce.simplon-roanne.com/order', [
            'headers' => $headers,
            'json' => [
                'order' => [
                    'id' => $order->getId(),
                    'product' => $order->getProduct()->getName(),
                    'payment_method' => $paymentMethod,
                    'status' => $order->getStatus(),
                    'client' => [
                        'firstname' => $order->getClient()->getFirstName(),
                        'lastname' => $order->getClient()->getLastName(),
                        'email' => $order->getClient()->getEmail()
                    ],
                    'addresses' => [
                        'billing' => [
                            'address_line1' => $order->getClient()->getAddressBiling()->getAddressLine1(),
                            'address_line2' => $order->getClient()->getAddressBiling()->getAddressLine2(),
                            'city' => $order->getClient()->getAddressBiling()->getCity(),
                            'zipcode' => $order->getClient()->getAddressBiling()->getZipeCode(),
                            'country' => $order->getClient()->getAddressBiling()->getCountry()->getName(),
                            'phone' => $order->getClient()->getAddressBiling()->getPhone()
                        ],
                        'shipping' => [
                            'address_line1' => $order->getClient()->getAddressShipping()->getAddressLine1(),
                            'address_line2' => $order->getClient()->getAddressShipping()->getAddressLine2(),
                            'city' => $order->getClient()->getAddressShipping()->getCity(),
                            'zipcode' => $order->getClient()->getAddressShipping()->getZipeCode(),
                            'country' => $order->getClient()->getAddressShipping()->getCountry()->getName(),
                            'phone' => $order->getClient()->getAddressShipping()->getPhone()
                        ]
                    ]
                ]
            ]
        ]);

        // Obtenir le corps de la réponse
        $responseData = $response->getBody()->getContents();

        // Traiter les données de réponse, enregistrer l'ID de commande dans la BDD locale, par exemple :
        // $orderId = json_decode($responseData)->orderId;

        // Rediriger vers la page de paiement avec l'ID de l'Order provenant de l'API
        return $this->redirectToRoute('confirmation');
    }


    #[Route('/', name: 'landing_page')]
    public function index(Request $request, AddressBilingRepository $addressBiling, AddressShippingRepository $addressShipping, EntityManagerInterface $entityManager, ProductRepository $productRespitory): Response
    {
        $products = $productRespitory->findAll();
        $client = new Client();
        $formClient = $this->createForm(ClientType::class, $client);
        $formClient->handleRequest($request);

        if ($formClient->isSubmitted() && $formClient->isValid()) {

            // Récupérer les données de la requête directement
            $requestData = $request->request->all();

            // Accéder aux données de l'ordre du formulaire
            $orderData = $requestData['order'];
            $productId = $orderData['cart']['cart_products'];

            // $paymentMethod =

            // Maintenant, vous pouvez utiliser $formData, $requestData, $orderData comme vous le souhaitez
            $addressBiling = $formClient->get('addressBiling')->getData();
            $addressShipping = $formClient->get('addressShipping')->getData();
            $client->setAddressBiling($addressBiling);
            $client->setAddressShipping($addressShipping);
            if (!$addressShipping) {
                $client->setAddressShipping($addressBiling);
            }
            // $shipping->setAddress($addressBiling->getAddress());

            $product = $productRespitory->findOneBy(['id' => $productId]);
            // dd($product);


            $entityManager->persist($client);
            $entityManager->flush();

            // D'abord ON AJOUTE DES CLIENTS ET UN ORDER DANS LA BDD

            $order = new Order();
            $order->setClient($client);
            $order->setProduct($product);
            // $order->setPaymentMethod();

            // ajouter method payment plus tard;
            $this->enregistrementDeLaCommande($order);

            return $this->redirectToRoute('process_payment', [], Response::HTTP_SEE_OTHER);
        }
        $productTotalPrices = [];
        foreach ($products as $product) {
            $originalPrice = (float) $product->getFirstPrice();
            $price = (float) $product->getPrice();
            $productTotalPrices[$product->getId()] = $this->calculateTotalPrice($originalPrice, $price);
        }





        return $this->render('landing_page/index_new.html.twig', [
            'client' => $client,
            'formClient' => $formClient->createView(),
            'products' => $products,
            'productTotalPrices' => $productTotalPrices,
        ]);
    }








    // stripe
    #[Route('/checkout', name: 'checkout')]
    public function checkout(): Response
    {
        return $this->render('landing_page/confirmation.html.twig');
    }



    #[Route('/process-payment', name: 'process_payment')]
    public function processPayment()
    {
        // Traitement du paiement avec Stripe ici

        Stripe::setApiKey($this->getParameter('stripe_key'));
        $YOUR_DOMAIN = 'http://127.0.0.1:8001/';

        $paymentIntent = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'unit_amount' => 1000,
                        'product_data' => ['name' => 'Free t-shirt'],
                        'currency' => 'eur',
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/',
            'cancel_url' => $YOUR_DOMAIN . '/',
        ]);

        return $this->redirect($paymentIntent->url);
    }





    #[Route('/payment-success', name: 'payment_success')]
    public function paymentSuccess(): Response
    {
        return $this->render('landing_page/confirmation.html.twig');
    }





    #[Route('/confirmation', name: 'confirmation')]
    public function confirmation(Request $request): Response
    {

        return $this->render('landing_page/confirmation.html.twig');
    }
}
