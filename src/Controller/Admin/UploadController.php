<?php

namespace App\Controller\Admin;

use App\Entity\DayGeneration;
use App\Form\ReportGenerationType;
use App\Repository\ClientRepository;
use App\Repository\DayGenerationRepository;
use Doctrine\Persistence\ManagerRegistry;
use NystronSolar\GrowattSpreadsheet\Reader\ReaderFactory;
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

            $reader = ReaderFactory::fromFile($reportFolder.$reportFileName);
            $reportSpreadsheet = $reader->search();

            $filesystem = new Filesystem();
            $filesystem->remove($reportFolder.$reportFileName);

            foreach ($reportSpreadsheet->getClients() as $spreadsheetClient) {
                $spreadsheetClient->getUserAccountName();

                $userAccountName = $spreadsheetClient->getUserAccountName();
                $client = $clientRepository->findOneBy(['growattName' => $userAccountName]);
                if (is_null($client)) {
                    $this->addFlash('error', sprintf('Client %s Not Founded in Database.', $userAccountName));

                    return $this->redirectToRoute('app.admin.uploads.reports.generation');
                }

                foreach ($spreadsheetClient->getGenerationDays() as $generationDay) {
                    $dayGeneration = new DayGeneration();

                    $dayGeneration->setGeneration($generationDay->getGeneration());
                    $dayGeneration->setHours($generationDay->getHours());
                    $dayGeneration->setDate($generationDay->getDate());

                    $client->addDayGeneration($dayGeneration);

                    $dayGenerationRepository->save($dayGeneration);

                    $entityManager->flush();
                }
            }

            $filesystem = new Filesystem();
            $filesystem->remove($reportFolder.$reportFileName);

            dd($reportSpreadsheet);

            // return $this->redirectToRoute('app.admin.clients.show', ['client' => $client->getId()]);
        }

        $errors = $form->getErrors();

        return $this->render('admin/upload/reports/generation.html.twig', [
            'form' => $form,
            'errors' => $errors,
        ]);
    }
}
