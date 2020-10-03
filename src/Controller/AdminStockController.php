<?php

namespace App\Controller;

use App\Controller\Services\Helpers;
use App\Entity\Stock;
use App\Form\StockType;
use App\Repository\MarqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ModeleChaussureRepository;
use App\Repository\StockRepository;

class AdminStockController extends AbstractController
{
    private $modeleChaussure;
    private $stocks;
    private $marques;
    private $helpers;
    public function __construct(ModeleChaussureRepository $modeleChaussureRepository, StockRepository $stockRepository, MarqueRepository $marqueRepository, Helpers $helpers)
    {
        $this->modeleChaussure = $modeleChaussureRepository;   
        $this->stocks = $stockRepository;   
        $this->marques = $marqueRepository;   
        $this->helpers = $helpers;
    }
     /**
     * *****************************************************************
     * *********************** METHODES DE STOCKS **********************
     * *****************************************************************
     */

    /**
     * @Route("/admin/stocks", name="admin_stocks")
     *
     * @return void
     */
    public function adminStocks(Request $request, EntityManagerInterface $em)
    {
        $stock = new Stock();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // recupere data de request
            $data = $form->getData();

            // recupere le stock par data pour verifie si deja exist
            $stocks = $this->stocks->findBy(['modeleChaussure' => $data->getModeleChaussure(), 'taille' => $data->getTaille()]);

            // si le stock recupere est vide (n'exist pas)
            if (empty($stocks)) {
                $em->persist($stock);
                $em->flush();

                $this->addFlash('success', 'Stock Creé avec success');
                return new RedirectResponse($request->headers->get("referer"));

                // si le stock exist deja 
            } else {

                // si la quantite est 0 (faite mise a jour au niveau add stock)
                if ($data->getQuantite() == 0) {
                    $stocks[0]->setQuantite($data->getQuantite());
                    $this->manager->flush();
                    $this->addFlash('success', 'Le Stock a été ajoutée avec succès !');
                    return new RedirectResponse($request->headers->get("referer"));

                    // si la quantite est deffirent de 0 (faite mise a jour au niveau update stock)
                } else {
                    $this->addFlash('info', 'Veuillez faire mise a jour ici.');
                    return $this->redirectToRoute('stock_edit', [
                        'stock_id' => $stocks[0]->getId(),
                        'chaussure_slug' => $stocks[0]->getModeleChaussure()->getNom(),
                        'chaussure_id' => $stocks[0]->getModeleChaussure()->getId(),
                    ]);
                }
            }
        }
        return $this->render('admin_stock/index.html.twig', [
            'modeleChaussures' => $this->modeleChaussure->findAll(),
            'stocks' => $this->stocks->findAll(),
            'list' => $this->marques->findAll(),
            'carts' => $this->helpers->getProduct(),
            'form' => $form->createView()
        ]);
    }

    /**
     * METHODE PERMET DE MODIFIER UN STOCK
     * @Route("/stock/{stock_id}/edit/{chaussure_slug}-{chaussure_id}" ,name="admin_edit_stock")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public function AdminEditStock($stock_id, Request $request, $chaussure_id, EntityManagerInterface $em, Helpers $helpers)
    {
        $stock = $this->stocks->findOneBy(['id' => $stock_id]);
        $chaussure = $this->modeleChaussure->findOneBy(['id' => $chaussure_id]);
        $taille = $stock->getTaille();
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // dd($form->getData());
            $stock->setModeleChaussure($chaussure);
            $stock->setTaille($taille);
            $stock->setQuantite($form->getData()->getQuantite());
            $em->persist($stock);
            $em->flush();

            $this->addFlash('success', 'Le Stock a été modifiée avec succès !');
            return $this->redirectToRoute('admin_stocks');
        }
        return $this->render('admin_stock/edit.html.twig', [
            'form' => $form->createView(),
            'list' => $this->marques->findAll(),
            'chaussure' => $chaussure,
            'stock' => $stock
        ]);
    }

    /**
     * METHODE PERMET DE SUPPRIMER UN STOCK
     * @Route("/admin/stock/{id}/delete", name="admin_delete_stock")
     *
     * @param [type] $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return void
     */
    public function adminDeleteStock($id, Request $request, EntityManagerInterface $em)
    {
        $em->remove($this->stocks->findOneBy(['id' => $id]));
        $em->flush();
        $this->addFlash('success', 'Le Stock a été supprimée avec succès !');
        return new RedirectResponse($request->headers->get("referer"));
    }


     /**
     * Methode edit from admin 
     * @Route("/stock/{stock_id}/{chaussure_slug}/edit/{chaussure_id}" ,name="edit_from_admin",methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public function editFromAdmin($stock_id, Request $request, $chaussure_id, Helpers $helpers)
    {
        $list = $this->marqueRepository->findAll();
        $stock = $this->stockRepository->findOneBy(['id' => $stock_id]);
        $chaussure = $this->chaussureRepository->findOneBy(['id' => $chaussure_id]);


        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($stock);
            $this->manager->flush();

            $this->addFlash('success', 'les modifications sont bien enregistré.');
            return $this->redirectToRoute('admin_modele_chaussure');
        }
        return $this->render('stock/edition.html.twig', [
            'form' => $form->createView(),
            'list' => $list,
            'chaussure' => $chaussure,
            'stock' => $stock
        ]);
    }

}
