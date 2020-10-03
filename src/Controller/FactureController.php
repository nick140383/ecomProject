<?php

namespace App\Controller;

use App\Controller\Services\Helpers;
use App\Entity\Commande;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Facture;

use App\Repository\FactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use SessionIdInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class FactureController extends AbstractController
{

    //......


    /**
     * @Route("/facture", name="facture_pdf", methods={"GET"})
     * @param Helpers $helpers
     * @param SessionInterface $sessionInterface
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     * @throws \Exception
     */
    public function showPdf(Helpers $helpers, SessionInterface $sessionInterface,  EntityManagerInterface $em)
    {
        /*
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();

        $pdfOptions->set('defaultFont', 'Arial');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);

        // Retrieve the HTML generated in our twig file

        // AJOUTER LA FACTURE 
        if(!empty($sessionInterface->get('cart', []))  ){
            
            $facture = new Facture();
            $facture->setCommande($this->getDoctrine()->getRepository(Commande::class)->findOneBy([], ['id' => 'desc']));
            $facture->setDatePaiement(new \DateTime(date( 'Y-m-d H:i:s')));
            $facture->setDateFacture(new \DateTime(date( 'Y-m-d H:i:s')));
            $facture->setDateLimitePaiement(new \DateTime(date( 'Y-m-d H:i:s')));
            $facture->setBaseHTVA($helpers->getTotal() - ($helpers->getTotal() * 0.2));
            $facture->setMontantTVA(0.2);
            $facture->setTotalHTVA($helpers->getTotal());
            $facture->setTotalTTC($helpers->getTotalTTC());
            $em->persist($facture);
            $em->flush();
        }else{
            return $this->redirectToRoute('home'); //
        }
        
        $html = $this->renderView('facture/pdf.html.twig', [
            'items' => $helpers->getProduct(),
            'total' => $helpers->getTotal(),
            'totalTTC' => $helpers->getTotalTTC(),
            'livraision' => $helpers->getLivraison(),
            'facture_id' => $this->getDoctrine()->getRepository(Facture::class)->findOneBy([], ['id' => 'desc'])->getId(),
        ]);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // SUPPRIMER LA SESSION de panier
        $sessionInterface->set('cart', []);

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);
        return new Response("The PDF file has been succesfully generated !");
    }
    */
    
    
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('facture/pdf.html.twig', [
            'title' => "Welcome to our PDF Test",
            'items' => $helpers->getProduct(),
            'total' => $helpers->getTotal(),
            'totalTTC' => $helpers->getTotalTTC(),
            'livraision' => $helpers->getLivraison(),
            'facture_id' => $this->getDoctrine()->getRepository(Facture::class)->findOneBy([], ['id' => 'desc'])->getId(),
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("maFacture.pdf", [
            "Attachment" => true
        ]);
        return new Response("The PDF file has been succesfully generated !");
        if(!empty($sessionInterface->get('cart', []))  ){
            
            $facture = new Facture();
            $facture->setCommande($this->getDoctrine()->getRepository(Commande::class)->findOneBy([], ['id' => 'desc']));
            $facture->setDatePaiement(new \DateTime(date( 'Y-m-d H:i:s')));
            $facture->setDateFacture(new \DateTime(date( 'Y-m-d H:i:s')));
            $facture->setDateLimitePaiement(new \DateTime(date( 'Y-m-d H:i:s')));
            $facture->setBaseHTVA($helpers->getTotal() - ($helpers->getTotal() * 0.2));
            $facture->setMontantTVA(0.2);
            $facture->setTotalHTVA($helpers->getTotal());
            $facture->setTotalTTC($helpers->getTotalTTC());
            $em->persist($facture);
            $em->flush();
        }else{
            return $this->redirectToRoute('home'); //
        }
    }
}
