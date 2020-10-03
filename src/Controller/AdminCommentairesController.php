<?php

namespace App\Controller;

use App\Entity\Commentaire;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CommentaireRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentairesController extends AbstractController
{
    /**
     * @Route("/admin/commentaires", name="admin_commentaires")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function listerCommentaires(CommentaireRepository $repo)
    {
        return $this->render('admin_commentaires/lister.html.twig', [

            'commentaires' => $repo->findall()
        ]);
    }
      /**
     * @Route("/admin/commentaires{id}/delete",name="admin_commentaires_delete")
     * @Security("is_granted('ROLE_ADMIN')")
     * @param Commentaire $comment
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */

    public function  deletecomment(Commentaire $comment,EntityManagerInterface $manager)
    {
  
            $manager->remove($comment);
            $manager->flush();
            $this->addFlash(
                'success',
                "le commantaire de <strong>{$comment->getClient()->getNom()}</strong>a bien été supprimé"
            );
        
        return $this->redirectToRoute(' admin_modele_chaussure');
    

}
}
