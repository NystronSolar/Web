<?php

namespace App\Controller;

use NystronSolar\GrowattSpreadsheet\Reader\ReaderFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
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
        /** @var UploadedFile $report */
        $report = $request->files->all()['report'];

        $reportFolder = $this->getParameter('kernel.project_dir') . '/var/uploads/';
        $reportFileName = $report->getClientOriginalName();
        $report->move($reportFolder, $reportFileName);

        $reader = ReaderFactory::fromFile($reportFolder . $reportFileName);
        $reportSpreadsheet = $reader->search();

        $filesystem = new Filesystem();
        $filesystem->remove($reportFolder . $reportFileName);

        dd($reportSpreadsheet);
    }
}