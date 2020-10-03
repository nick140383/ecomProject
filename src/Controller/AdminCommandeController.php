<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use App\Repository\FactureRepository;
use App\Repository\LigneCommandeRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCommandeController extends AbstractController
{
    private $ligneCommandes;
    public function __construct(LigneCommandeRepository $ligneCommandeRepository)
    {
        $this->ligneCommandes = $ligneCommandeRepository;
    }

   /**
    * @Route("/admin/commandes", name="admin_commandes")
    *
    * @return void
    */
   public function adminCommandes()
   {
       return $this->render('admin_commande/index.html.twig', [
           'commandes' => $this->ligneCommandes->findAll([], ['id' => 'asc' ]),
       ]);
   }

   /**
    * @Route("/admin/facture/{id_commande}", name="admin_commande_facture", requirements={"id_commande": "\d+"})
    *
    * @return void
    */
   public function adminCommandesFacture($id_commande, Request $request, FactureRepository $factureRepository, CommandeRepository $commandeRepository)
   {
       $facture = $factureRepository->findOneBy(['commande' => $id_commande]);
       if ($facture == null) {

           $this->addFlash('danger', 'Cette Commande n\'a pas de facture !');
           return new RedirectResponse($request->headers->get('referer'));
       }

       $ligneCommande = $this->ligneCommandes->findBy(['commande' => $id_commande]);
       $commande = $commandeRepository->findOneBy(['id' => $id_commande]);

       $pdfOptions = new Options();

       $pdfOptions->set('defaultFont', 'Arial');

       $dompdf = new Dompdf($pdfOptions);

       $html = $this->renderView('admin_commande/facture.html.twig', [
           'facture' => $facture,
           'ligneCommandes' => $ligneCommande,
           'client' => $commande->getClient(),
       ]);

       // Load HTML to Dompdf
       $dompdf->loadHtml($html);

       // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
       $dompdf->setPaper('A4', 'portrait');

       // Render the HTML as PDF
       $dompdf->render();

       // Output the generated PDF to Browser (force download)
       $dompdf->stream("pdf N° : 000" . $facture->getId() . ".pdf", [
           "Attachment" => false 
       ]);
    //    return new HttpFoundationResponse();
   }
}
