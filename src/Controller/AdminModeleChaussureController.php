<?php

namespace App\Controller;

use App\Controller\Services\Helpers;
use App\Entity\ModeleChaussure;
use App\Repository\ClientRepository;
use App\Repository\MarqueRepository;
use App\Repository\ModeleChaussureRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminModeleChaussureController extends AbstractController
{
    /**
     * @var ClientRepository
     */
    private $marqueRepository;
    private $clientRepository;
    private $helpers;
    function __construct(MarqueRepository $marqueRepository,ClientRepository $clientRepository, Helpers $helpers)
    {
        $this->marqueRepository = $marqueRepository;
        $this->clientRepository=$clientRepository;
        $this->helpers = $helpers;
    }

    /**
     * @Route("/admin/modeleChaussure", name="admin_modele_chaussure")
     * @param ModeleChaussureRepository $repo
     * @param StockRepository $stockRepository
     * @return Response
     */
    public function index(ModeleChaussureRepository $repo, StockRepository $stockRepository)
    {
        $list = $this->marqueRepository->findAll();
        return $this->render('admin/admin_modele_chaussure/index.html.twig', [
            'modeleChaussures'=>$repo->findAll(),
            'list' =>$list,
            'carts' => $this->helpers->getProduct(),
            'stocks' => $stockRepository->findall()
        ]);
    }

    /**
     * @Route("/admin/chaussures{id}/delete",name="admin_chaussures_delete")
     *
     * @param ModeleChaussure $chaussure
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */

    public function  deleteShoes(ModeleChaussure $chaussure,EntityManagerInterface $manager)
    {
        if (count($chaussure->getCommandes())>0){
            $this->addFlash(
                'warning',
                "you can't delete this shoe<stong>{$chaussure->getNom()}</stong>it has been already ordered!"
            );
        }else{
            $manager->remove($chaussure);
            $manager->flush();
            $this->addFlash(
                'success',
                "the shoe<strong>{$chaussure->getNom()}</strong>a bien été supprimé"
            );
        }
        return $this->redirectToRoute(' admin_modele_chaussure');
    }

}
