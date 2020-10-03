<?php

namespace App\Controller;

use App\Controller\Services\Helpers;
use App\Repository\ClientRepository;
use App\Repository\MarqueRepository;
use App\Repository\ModeleChaussureRepository;
use App\Repository\PromotionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    /**
     * @var ClientRepository
     */
    private $marqueRepository;
    function __construct(MarqueRepository $marqueRepository)
    {
        $this->marqueRepository = $marqueRepository;
    }


    /**
     * @Route("/", name="home")
     * @param Helpers $helpers
     * @param ModeleChaussureRepository $modeleChaussureRepository
     * @param PromotionRepository $promotionRepository
     * @return Response
     */
    public function index(Helpers $helpers, ModeleChaussureRepository$modeleChaussureRepository, PromotionRepository $promotionRepository)
    {
        return $this->render('home/index.html.twig', [
            'list' => $this->marqueRepository->findAll(), // liste des marques
            'chaussures' => $modeleChaussureRepository->findAll(),
            'promo' => $promotionRepository->findAll()
        ]);
    }
}
