<?php

namespace App\Controller;

use App\Entity\Marque;
use App\Controller\Services\Helpers;
use App\Form\MarqueType;
use App\Repository\MarqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminMarqueController extends AbstractController
{   /**
    * @Route("/admin/list/marque", name="admin_marque_list")
    * @param MarqueRepository $repo
    * @return Response
    */
   public function index(MarqueRepository $repo)
   {
      
       return $this->render('admin_marque/list.html.twig', [
          
           'marques' => $repo->findall()
       ]);
   }

    /**
     * @Route("/admin/marque", name="admin_marque")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function CreerMarque(Request $request, EntityManagerInterface $manager, Helpers $helpers)
    {
        $marque = new Marque();
    
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {


            $manager->persist($marque);
            $manager->flush();
            $this->addFlash('success', 'marque Creé avec success');
            return $this->redirectToRoute('admin_marque_list');

        }

            return $this->render('admin_marque/nouveau.html.twig', [
                
                'form' => $form->createView(),
            ]);
        }

    /**
     * @Route("/marque/{id}/edit" ,name="marque_edit",methods={"GET","POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Request $request
     * @param Marque $marque
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Request $request, Marque $marque, EntityManagerInterface $manager)
    {
       // $list = $this->marqueRepository->findAll();
        $form = $this->createForm(MarqueType::class, $marque);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $manager = $this->getDoctrine()->getManager();
            $manager->persist($marque);
            $manager->flush();
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash(
                'success',
                "Bravo <strong class='text-danger'>{$marque->getNom()}</strong> Modification reussie"
            );
            return $this->redirectToRoute('admin_marque_list');

        }

        return $this->render('admin_marque/edit.html.twig', [
            'form' => $form->createView(),
       

        ]);
    }


}
