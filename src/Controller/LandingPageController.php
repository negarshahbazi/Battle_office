<?php

namespace App\Controller;

use Stripe\Stripe;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use App\Entity\Client;
use App\Entity\Order;
use App\Form\ClientType;
use App\Repository\AddressBilingRepository;
use App\Repository\AddressShippingRepository;
use App\Repository\PaymentMethodRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class LandingPageController extends AbstractController
{
    public function calculateTotalPrice(float $originalPrice, float $price): float
    {
        return $originalPrice - $price;
    }


// enregistre de command in api
    public function enregistrementDeLaCommande(Order $order, EntityManagerInterface $entityManager)
    {
        // dd($order);
        // Instancier le client Guzzle
        $client = new \GuzzleHttp\Client();
        // Préparer le header avec le token d'authentification
        $headers = [
            'Authorization' => 'Bearer mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX',
        ];
        // Définir une méthode de paiement par défaut si elle n'est pas définie
        // $paymentMethod = $order->getPaymentMethod()->getName() ?? 'Stripe'; // Par exemple, utiliser 'paypal' par défaut
        // dd( $paymentMethod);

        // Envoyer la requête POST à l'API Centrale
        $response = $client->request('POST', 'https://api-commerce.simplon-roanne.com/order', [
            'headers' => $headers,
            'json' => [
                'order' => [
                    'id' => $order->getId(),
                    'product' => $order->getProduct()->getName(),
                    'payment_method' => $order->getPaymentMethod()->getName(),
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

        $entityManager->persist($order);
        $entityManager->flush();

        // Obtenir le corps de la réponse
        $responseData = $response->getBody()->getContents();

        // Traiter les données de réponse, enregistrer l'ID de commande dans la BDD locale, par exemple :
        $responseData = json_decode($responseData);
        if (isset($responseData->success) && isset($responseData->order_id)) {
            // Obtenir l'ID de la commande
            $orderId = $responseData->order_id;
        }
            // Rediriger vers la page de paiement avec l'ID de l'Order provenant de l'API
            return new RedirectResponse($this->generateUrl('process_payment', ['id' => $orderId]));
        // dd( $orderId->order_id);

  
       
    }


    #[Route('/', name: 'landing_page')]
    public function index(Request $request,PaymentMethodRepository $paymentMethodRepository, AddressBilingRepository $addressBiling, AddressShippingRepository $addressShipping, EntityManagerInterface $entityManager, ProductRepository $productRespitory): Response
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
            $paymentMethod =$orderData['payment_method'];
            // dd( $paymentMethod );
            // Maintenant, vous pouvez utiliser $formData, $requestData, $orderData comme vous le souhaitez
            $addressBiling = $formClient->get('addressBiling')->getData();
            $addressShipping = $formClient->get('addressShipping')->getData();
            $client->setAddressBiling($addressBiling);
            $client->setAddressShipping($addressShipping);
            if (!$addressShipping) {
                $client->getAddressShipping()->setAddressLine1($client->getAddressBiling()->getAddressLine1());
                $client->getAddressShipping()->setAddressLine2($client->getAddressBiling()->getAddressLine2());
                $client->getAddressShipping()->setCity($client->getAddressBiling()->getCity());
                $client->getAddressShipping()->setZipecode($client->getAddressBiling()->getZipecode());
                $client->getAddressShipping()->setCountry($client->getAddressBiling()->getCountry());
                $client->getAddressShipping()->setPhone($client->getAddressBiling()->getPhone());
                $client->getAddressShipping()->setNom($client->getFirstName());
                $client->getAddressShipping()->setPrenom($client->getLastName());
            }
        

            $product = $productRespitory->findOneBy(['id' => $productId]);
            $payment = $paymentMethodRepository->findOneBy(['name'=>$paymentMethod]);
            // dd($payment);
            $entityManager->persist($client);
            $entityManager->flush();
            
            // D'abord ON AJOUTE DES CLIENTS ET UN ORDER DANS LA BDD      
            $order = new Order();
            $order->setClient($client);
            $order->setProduct($product);
            $order->setPaymentMethod($payment);
        
            // its very important API
            $this->enregistrementDeLaCommande($order, $entityManager);
            $this->processPayment($order);
            
            $entityManager->persist($order);
            $entityManager->flush();
            return $this->redirectToRoute('process_payment', ['id' => $order->getId()],Response::HTTP_SEE_OTHER);

        }
        // PROMOTION
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



    #[Route('/process-payment/{id}', name: 'process_payment')]
    public function processPayment(Order $order)
    {    $paymentMethos=$order->getPaymentMethod()->getName();
        // Traitement du paiement avec Stripe ici
        if($paymentMethos==='Stripe'){

        Stripe::setApiKey($this->getParameter('stripe_key'));
        $YOUR_DOMAIN = 'http://127.0.0.1:8001/';

        $paymentIntent = \Stripe\Checkout\Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'unit_amount' => $order->getProduct()->getPrice()*100,
                        'product_data' => ['name' => $order->getProduct()->getName()],
                        'currency' => 'eur',
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/confirmation',
            'cancel_url' => $YOUR_DOMAIN . '/',
        ]);

        return $this->redirect($paymentIntent->url);
    }else if($paymentMethos==='Paypal'){
        $YOUR_DOMAIN = 'http://127.0.0.1:8001/';


              // Remplacez 'sandbox' par 'live' si vous utilisez un environnement de production
              $environment = new SandboxEnvironment($this->getParameter('paypalClientId'), $this->getParameter('paypalSecret'));
              $client = new PayPalHttpClient($environment);
            //   dd($client);
            $request = new OrdersCreateRequest();
            $request->prefer('return=representation');
            $request->body = [
                "intent" => "CAPTURE",
                "purchase_units" => [[
                    "amount" => [
                        "currency_code" => "EUR",
                        "value" =>  $order->getProduct()->getPrice() // Montant à payer
                    ]
                ]],
                'success_url' => $YOUR_DOMAIN . '/confirmation',
                'cancel_url' => $YOUR_DOMAIN . '/',
            ];
            $response = $client->execute($request);
            // Obtenez l'URL d'approbation PayPal
            $approvalUrl = $response->result->links[1]->href; 

            // Rediriger vers l'URL d'approbation PayPal
            return new RedirectResponse($approvalUrl);
        



    }
       // Add a default response in case neither Stripe nor Paypal payment method is found
    //    return new Response('Invalid payment method', Response::HTTP_BAD_REQUEST);

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
