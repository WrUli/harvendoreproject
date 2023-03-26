<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index()
    {
        return $this->redirectToRoute('home_page');
    }

    #[Route('/home', name: 'home_page')]
    public function home()
    {
        return $this->render('home/home.html.twig');
    }
}
