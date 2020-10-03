<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminClientController extends AbstractController
{
    /**
     * @Route("/admin/client", name="admin_client")
     * @param ClientRepository $repo
     * @return Response
     */
    public function listerClients(ClientRepository $repo)
    {
        return $this->render('admin_client/liste.html.twig', [
            'clients' => $repo->findAll(),
        ]);
    }
}
