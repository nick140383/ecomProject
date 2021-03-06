<?php

namespace App\Controller;

use App\Controller\Services\Helpers;
use App\Repository\ModeleChaussureRepository;
use App\Repository\PromotionRepository;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CheckoutController extends AbstractController
{
    /**
     * @Route("/checkout", name="checkout_index")
     * @param Helpers $helpers
     * @return RedirectResponse|Response
     */
    public function index(Helpers $helpers)
    {
        // le client n'a pas l'access de cette page (checkout) sauf si le panier n'est pas vide
        if (count($helpers->getProduct()) == 0) {
            $this->addFlash('error', 'votre panier est totalemnt vide !.');
            return $this->redirectToRoute('cart_index');
        }
        return $this->render('checkout/index.html.twig', [
            'list' => $helpers->getList(), // liste des marque
            'items' => $helpers->getProduct(), // liste des produits
            'total' => $helpers->getTotal() // total
        ]);
    } // 

    /**
     * Methode qui permet de passer le payment avec stripe
     * @Route("/checkout/create", name="checkout_add")
     * @param Request $request
     * @param Helpers $helpers
     * @param Security $security
     * @return RedirectResponse
     * @throws ApiErrorException
     */
    public function add(Request $request, Helpers $helpers, Security $security)
    {
        // mise a jour le stock (qte diminue ...)
        $helpers->updateStock();

        // recupre les info des produit et client 
        $content = new JsonResponse($helpers->getProductForStripe());

        // mettre ton apikey de strie ** IMPORTANT ** aller à checkout/index.html.twig et mettre l'autre key dans la partie javascript
        \Stripe\Stripe::setApiKey('sk_test_51HAAleJZeTjJHZS4LQs1woXQvLbw1CreXElOEmfcoCWl8V4O8dlLbY8gmlp7KGCnYgLwrBmcsdASgZw3e4shMNnK000lVBx62J');

        \Stripe\Charge::create([
            'amount' => $helpers->getTotal() * 100, // total
            'currency' => 'eur', // divse
            'description' => 'Order',
            'source' => $request->request->get('stripeToken'), // token
            'receipt_email' => $security->getUser()->getEmail(), // email de client
            'metadata' => [
                'content' => $content // info de payement
            ]
        ]);

        // ADD livraison  
        $helpers->addLivraison();

        // ADD COMMANDE 
        $helpers->addCommande();

        // ADD lignecommande 
        $helpers->addLigneCommande();
        
        $this->addFlash('success', 'Your payment has been successfully accepted !');
        return $this->redirectToRoute('thankyou');
    }
}
