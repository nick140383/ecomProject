<?php

namespace App\Controller;

use App\Controller\Services\Helpers;
use App\Entity\ModeleChaussure;
use App\Entity\Photo;
use App\Entity\Promotion;
use App\Entity\Ville;
use App\Form\ModeleChaussureType;
use App\Form\PromotionType;
use App\Form\VilleType;
use App\Repository\ClientRepository;
use App\Repository\MarqueRepository;
use App\Repository\ModeleChaussureRepository;
use App\Repository\PromotionRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminVilleController extends AbstractController
{
    /**
     * @var ClientRepository
     */
    private $marqueRepository;
    private $promotionRepository;
    private $chaussureRepository;
    private $manager;

    //private $clientRepository;
    function __construct(MarqueRepository $marqueRepository, PromotionRepository $promotionRepository, ModeleChaussureRepository $chaussureRepository, EntityManagerInterface $manager)
    {
        $this->marqueRepository = $marqueRepository;
        //$this->clientRepository=$clientRepository;
        $this->promotionRepository = $promotionRepository;
        $this->chaussureRepository = $chaussureRepository;
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/list/ville", name="admin_ville_list")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param VilleRepository $repo
     * @param ModeleChaussureRepository $chaussures
     * @return Response
     */
    public function index(VilleRepository $repo, ModeleChaussureRepository $chaussures)
    {
        $list = $this->marqueRepository->findAll();
        return $this->render('admin_ville/list.html.twig', [
            'list' => $list,
            'villes' => $repo->findall()
        ]);
    }

    /**
     * @Route("/admin/ville", name="admin_ville")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function CreerVille(Request $request, EntityManagerInterface $manager, Helpers $helpers)
    {
        $ville = new Ville();
        $list = $this->marqueRepository->findAll();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $manager->persist($ville);
            $manager->flush();
            $this->addFlash('success', 'ville Creé avec success');
            return $this->redirectToRoute('admin_ville_list');

        }

            return $this->render('admin_ville/index.html.twig', [
                'list' => $list,
                'form' => $form->createView(),
            ]);
        }

    /**
     * @Route("/ville/{id}/edit" ,name="ville_edit",methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @param Ville $ville
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Request $request, Ville $ville, EntityManagerInterface $manager)
    {
        $list = $this->marqueRepository->findAll();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $manager = $this->getDoctrine()->getManager();
            $manager->persist($ville);
            $manager->flush();
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                "Bravo <strong class='text-danger'>{$ville->getNom()}</strong> Modification reussie"
            );
            return $this->redirectToRoute('admin_ville_list');

        }

        return $this->render('admin_ville/edit.html.twig', [
            'form' => $form->createView(),
           'list' => $list,

        ]);
    }


}

