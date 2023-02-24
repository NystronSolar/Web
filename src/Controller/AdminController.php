<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'app.admin.')]
class AdminController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: 'GET')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route(path: '/upload/generation', name: 'upload.generation.view', methods: 'GET')]
    public function uploadGenerationView(): Response
    {
        return $this->render('admin/upload/generation.html.twig');
    }

    #[Route(path: '/upload/generation', name: 'upload.generation.action', methods: 'POST')]
    public function uploadGenerationAction(Request $request): Response
    {
        $report = $request->files->all()['report'];

        return new Response("OK!");
    }
}