<?php

namespace App\Controller\Admin;

use App\Form\ReportGenerationType;
use App\Repository\ClientRepository;
use App\Repository\DayGenerationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/uploads', name: 'app.admin.uploads.')]
class UploadController extends AbstractController
{
    #[Route(path: '/reports/generation', name: 'reports.generation', methods: ['GET', 'POST'])]
    public function generationReport(Request $request, ClientRepository $clientRepository, DayGenerationRepository $dayGenerationRepository, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $form = $this->createForm(ReportGenerationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $report */
            $report = $form->get('report')->getData();

            $reportFolder = $this->getParameter('kernel.project_dir').'/var/uploads/';
            $reportFileName = $report->getClientOriginalName();
            $report->move($reportFolder, $reportFileName);

            $dayGenerationRepository->uploadGrowattSpreadsheet($reportFolder.$reportFileName);

            $filesystem = new Filesystem();
            $filesystem->remove($reportFolder.$reportFileName);

            $this->addFlash('success', sprintf('Report Uploaded'));

            return $this->redirectToRoute('app.admin.clients.index');
        }

        $errors = $form->getErrors();

        return $this->render('admin/upload/reports/generation.html.twig', [
            'form' => $form,
            'errors' => $errors,
        ]);
    }
}
