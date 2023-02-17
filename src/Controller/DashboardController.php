<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard', name: 'app.dashboard.')]
class DashboardController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig');
    }
}
