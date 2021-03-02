<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BIPController extends AbstractController
{
    /**
     * @Route("/bip", name="bip")
     */
    public function index(): Response
    {
        return $this->render('bip/index.html.twig', [
            'controller_name' => 'BIPController',
        ]);
    }
}
