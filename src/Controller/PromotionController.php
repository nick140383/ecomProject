<?php

namespace App\Controller;

use App\Controller\Services\Helpers;
use App\Entity\Promotion;
use App\Entity\Stock;
use App\Form\PromotionType;
use App\Form\StockType;
use App\Repository\ClientRepository;
use App\Repository\MarqueRepository;
use App\Repository\ModeleChaussureRepository;
use App\Repository\PromotionRepository;
use App\Repository\StockRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PromotionController extends AbstractController
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
     * @Route("/admin/promotion", name="admin_promotion_list")
     * @param PromotionRepository $repo
     * @return Response
     */
    public function index(PromotionRepository $repo)
    {
        $list = $this->marqueRepository->findAll();
        return $this->render('admin/promotion/list.html.twig', [

            'list' =>$list,
            'promotions' => $repo->findall()
        ]);
    }
    /**
     * @Route("promotion/add", name="promotion_add")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Helpers $helpers
     * @return Response
     */
    public function createPromotion(Request $request,EntityManagerInterface $manager,Helpers $helpers)
    {
        $promotion = new Promotion();
        $list = $this->marqueRepository->findAll();
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // recupere data de request
            $data = $form->getData();

            // recupere la promotion par data pour verifie si deja exist
            $promomotions = $this->promotionRepository->findBy(['modeleChaussure' => $data->getModeleChaussure()]);

            // si le stock recupere est vide (n'exist pas)
            if (empty($promomotions)) {
                $manager->persist($promotion);
                $manager->flush();
                $this->addFlash('success', 'promotion Creé avec success');
                return $this->redirectToRoute('marque');

                // si le stock exist deja
            } else {
                $this->addFlash('info', 'Veuillez faire mise à jour ici.');
                return $this->redirectToRoute('promotion_editFromHeader', [
                    'stock_id' => $promomotions[0]->getId(),
                    'chaussure_slug' => $promomotions[0]->getModeleChaussure()->getNom(),
                    'chaussure_id' => $promomotions[0]->getModeleChaussure()->getId(),
                ]);
            }

        }
        return $this->render('admin/promotion/ajouter.html.twig', [
            'list'=>$list,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("promotion/edit/{id}", name="promotion_editFromHeader")
     * @param Request $request
     * @param Helpers $helpers
     * @return RedirectResponse|Response
     */
    public function editFromHeader($id, Request $request, Helpers $helpers)
    {
        $list = $this->marqueRepository->findAll();
        $promotion = $this->promotionRepository->findOneBy(['id' => $id]);
        $chaussure = $this->chaussureRepository->findAll();

        // recupere la chaussure de cette promotion
        $modeleChaussure = $promotion->getModeleChaussure();

        $form = $this->createForm(PromotionType::class, $promotion); //  cest promotion type ????
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // dd($request->request->get('promotion')['dateDebut']);
            // $promotion = $this->promotionRepository->findOneBy(['modeleChaussure' => $request->request->get('promotion')['modeleChaussure']]);
            //  'dateDebut' => $request->request->get('promotion')['dateDebut'],
            //  'dateFin' => $request->request->get('promotion')['dateFin']]);
            $dateDebut = $request->request->get('promotion')['dateDebut']['year'] . '-' . $request->request->get('promotion')['dateDebut']['month'] . '-' . $request->request->get('promotion')['dateDebut']['day'];
            $dateFin = $request->request->get('promotion')['dateDebut']['year'] . '-' . $request->request->get('promotion')['dateDebut']['month'] . '-' . $request->request->get('promotion')['dateDebut']['day'];
            // dd($dateFin);
            $promotion->setDateDebut(\DateTime::createFromFormat('Y-m-d', $dateDebut));
            $promotion->setDateFin(\DateTime::createFromFormat('Y-m-d', $dateFin));
            $promotion->setPourcentage($request->request->get('promotion')['pourcentage']);

            $promotion->setModeleChaussure($modeleChaussure);

            $this->manager->persist($promotion);

            $this->addFlash('success', 'les modifications sont bien enregistrées.');
            $this->manager->flush();
            return $this->redirectToRoute('admin_promotion_list');
        }
        return $this->render('admin/promotion/modifier.html.twig', [
            'form' => $form->createView(),
            'list' => $list,
            'chaussure' => $chaussure,
            'promotion' => $promotion
        ]);
    }
}
