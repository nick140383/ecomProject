<?php

namespace App\Controller;

use App\Controller\Services\Helpers;
use App\Entity\Client;
use App\Entity\Commentaire;
use App\Entity\ModeleChaussure;
use App\Entity\Photo;
use App\Entity\Stock;
use App\Entity\Taille;
use App\Form\CommentaireType;
use App\Form\ModeleChaussureType;
use App\Repository\ClientRepository;
use App\Repository\MarqueRepository;
use App\Repository\ModeleChaussureRepository;
use App\Repository\PromotionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class DetailChaussureController extends AbstractController
{
    /**
     * @var ClientRepository
     */
    private $marqueRepository;
    private $clientRepository;
    private $promotion;
    private $modeleChaussure;
    function __construct(MarqueRepository $marqueRepository, ClientRepository $clientRepository, PromotionRepository $promotionRepository, ModeleChaussureRepository $modeleChaussureRepository)
    {
        $this->marqueRepository = $marqueRepository;
        $this->clientRepository = $clientRepository;
        $this->promotion = $promotionRepository;
        $this->modeleChaussure = $modeleChaussureRepository;
    }

    /**
     * @Route("/detail/chaussure/{id}", name="detail_chaussure")
     * @param Helpers $helpers
     * @param ModeleChaussureRepository $repository
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param int $id
     * @return Response
     */
    public function index(Helpers $helpers, ModeleChaussureRepository $repository, Request $request, EntityManagerInterface $manager, int $id)
    {
        $list = $this->marqueRepository->findAll();
        $chaussure = $repository->find($id);
        $comment = new Commentaire();



        $form = $this->createForm(CommentaireType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setDateCommentaire(new \DateTime())
                ->setModele($chaussure)
                ->setClient($this->getUser());
            $manager->persist($comment);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre message a bien été pris en compte!"
            );
        }

        $taille = $this->getDoctrine()->getRepository(Taille::class)->findAll();
        $stocks = $this->getDoctrine()->getRepository(Stock::class)->findBy(array('modeleChaussure' => $chaussure));

        $stocksArr = array();
        foreach ($stocks as $stock) {
            $stocksArr = array($stock->getTaille()->getId() => $stock->getQuantite());
        }

        // recupere les promotion
        $promotion = null;
        
        //si date fin de promotion est plus grand d'aujourd'hui
        if (count((array)$this->promotion->findOneBy(['modeleChaussure' => $chaussure])) > 0) {
            if ($this->promotion->findOneBy(['modeleChaussure' => $chaussure])->getDateFin()->format('Y-m-d') >= date('Y-m-d')) {
                $promotion = $this->promotion->findOneBy(['modeleChaussure' => $chaussure]);
            }
        }

        return $this->render('detail_chaussure/index.html.twig', [
            'chaussure' => $chaussure,
            'list' => $list,
            'commentForm' => $form->createView(), 
            'taille' => $taille,
            'stocksArr' => $stocksArr,
            'stocks' => $stocks,
            'promotion' => $promotion
        ]);
    }

    /**
     * @Route("/chaussure/new", name="chaussure_nouveau",methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Helpers $helpers
     * @return RedirectResponse|Response
     */
    public function create(Request $request, EntityManagerInterface $manager,Helpers $helpers)
    {
        $chaussure = new ModeleChaussure();
        $manager = $this->getDoctrine()->getManager();

        $form = $this->createForm(ModeleChaussureType::class, $chaussure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('coverImage')->getData();
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('coverImage_directory'),
                    $fileName
                );
            } catch (fileException $e) {
            }



            $chaussure->setCoverImage($fileName);
            $manager->persist($chaussure);
            $manager->flush();

            //on recupere les photos transmises
            $photos =   $form->get('photos')->getData();
            //on boucle sur les photos

            //   $photos =$chaussure->getPhotos();

            foreach ($photos as $photo) {

                //on genere un nouveau nom de fichier
                $fileName =  md5(uniqid()) . '.' . $photo->guessExtension();
                //on copie le fichier dans le dossier coverImage
                $photo->move(
                    $this->getParameter('coverImage_directory'),
                    $fileName

                );

                $picture = new Photo();
                $picture->setUrl($fileName);

                $chaussure->addPhoto($picture);
            }
            //on stocke la photo dans la base de donnees
            $this->getDoctrine()->getManager()->flush();
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($chaussure);
            $manager->flush();



            $manager = $this->getDoctrine()->getManager();
            $manager->persist($chaussure);
            $manager->flush();

            $this->addFlash(
                'success',
                "Bravo <strong class='text-danger'>{$chaussure->getNom()}</strong> Insertion reussie"
            );
            return $this->redirectToRoute('admin_modele_chaussure');
        }
        $list = $this->marqueRepository->findAll();

        return $this->render('detail_chaussure/nouvelle_chaussure.html.twig', [
            'form' => $form->createView(),
            'list' => $list,
        ]);
    }


    /**
     * @Route("/detail/chaussure/{id}/edit" ,name="modeleChaussures_edit",methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @param ModeleChaussure $chaussure
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Request $request, ModeleChaussure $chaussure, EntityManagerInterface $manager, Helpers $helpers)
    {
        $form = $this->createForm(ModeleChaussureType::class, $chaussure);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $this->getDoctrine()->getManager()->flush();
            //on recupere les photos transmises
            $photos = $form->get('photos')->getData();
            //on boucle sur les photos
            
            //   $photos =$chaussure->getPhotos();
            
            $cover_image = $form->getData()->getCoverImage();

            if ($cover_image) {
                $filename = md5(uniqid()) . '.' . $cover_image->guessClientExtension();
                try {
                    $cover_image->move($this->getParameter('coverImage_directory'), $filename);
                    $chaussure->setCoverImage($filename);
                } catch (\Throwable $th) {
                    $this->addFlash('danger', $th);
                }
            }

            foreach ($photos as $photo) {
                //on genere un nouveau nom de fichier
                $fileName = md5(uniqid()) . '.' . $photo->guessExtension();
                //on copie le fichier dans le dossier coverImage
                $photo->move(
                    $this->getParameter('coverImage_directory'),
                    $fileName

                );

                $picture = new Photo();
                $picture->setUrl($fileName);

                $chaussure->addPhoto($picture);
            }
            $this->getDoctrine()->getManager()->flush();






            $manager = $this->getDoctrine()->getManager();
            $manager->persist($chaussure);
            $manager->flush();
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                "Bravo <strong class='text-danger'>{$chaussure->getNom()}</strong> Modification reussie"
            );
            return $this->redirectToRoute('admin_modele_chaussure');
        }
        $list = $this->marqueRepository->findAll();

        return $this->render('detail_chaussure/edit.html.twig', [
            'form' => $form->createView(),
            'list' => $list,
            'chaussure' => $chaussure
        ]);
    }


    /**
     * @param Photo $photo 
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @return JsonResponse
     * @Route("detail/chaussure/supprime/photo/{id}",name="chaussures_photo_supprime",methods={"DELETE"})
     */
    public function delete(Photo $photo, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        //on verifie si le token est valide
        if ($this->isCsrfTokenValid('delete' . $photo->getId(), $data['_token'])) {
            //on recupere le nom de la photo
            $nom = $photo->getUrl();
            //on supprime le fichier
            unlink($this->getParameter('coverImage_directory') . '/' . $nom);
            //on supprime l'entrée de la base
            $manager = $this->getDoctrine()->getManager();
            $manager->remove($photo);
            $manager->flush();
            //on repond en json
            return new JsonResponse(['success' => 1]);
        } else {
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }
    
     /**
     * METHODE PERMET DE SUPPRIMER IMAGE COVER
     * @Route("/admin/cover-image/{id}/delete", name="admin_delete_image_cover")
     *
     * @param [type] $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return void
     */
    public function adminDeleteImageCoverByAxios($id,Request $request,  EntityManagerInterface $em)
    {
        $modeleChaussure = $this->modeleChaussure->findOneBy(['id' => $id]);
        $modeleChaussure->setCoverImage("null");
        $em->persist($modeleChaussure);
        $em->flush();
        return new Response();
        
    }


    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
