<?php

namespace App\Controller;

use App\Repository\LigneCommandeRepository;
use App\Repository\RetourneProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminRetourController extends AbstractController
{
    private $retourne_commandes;
    private $ligneCommandes;
    public function __construct(RetourneProduitRepository $retourneProduitRepository, LigneCommandeRepository $ligneCommandeRepository)
    {
        $this->retourne_commandes = $retourneProduitRepository;
        $this->ligneCommandes = $ligneCommandeRepository;
    }
    /**
     * *****************************************************************
     * *************** METHODES DE COMMANDES RETOUREES *****************
     * *****************************************************************
     */

    /**
     * @Route("/admin/commandesRetournees", name="admin_commandes_retournes")
     *
     * @return void
     */
    public function adminCommandesRetounees()
    {
        return $this->render('admin_retour/reclamations.html.twig', [
            'commandes' => $this->retourne_commandes->findBy([], ['id' => 'desc']),
            'ligneCommandes' => $this->ligneCommandes->findAll()
        ]);
    }
    /**
     * METHODE PERMET DE SUPPRIMER UNE RECLAMATION
     * @Route("/admin/commandesRetournees/{id}/delete", name="admin_delete_commandeRetournee")
     *
     * @param [type] $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return void
     */
    public function adminDeleteCommandeRetourne($id, Request $request, EntityManagerInterface $em)
    {
        $em->remove($this->retourne_commandes->findOneBy(['id' => $id]));
        $em->flush();
        $this->addFlash('success', 'La reclamtion a été supprimée avec succès !');
        return new RedirectResponse($request->headers->get("referer"));
    }
}
