<?php

namespace App\Controller\Admin;

use App\Entity\Client;
use App\Entity\DayGeneration;
use App\Form\ReportGenerationType;
use App\Repository\ClientRepository;
use App\Repository\DayGenerationRepository;
use Doctrine\Persistence\ManagerRegistry;
use NystronSolar\GrowattSpreadsheet\GrowattSpreadsheet;
use NystronSolar\GrowattSpreadsheet\Reader\ReaderFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormInterface;
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
            $reportSpreadsheet = $this->getReportFile($form);

            foreach ($reportSpreadsheet->getClients() as $spreadsheetClient) {
                $username = $spreadsheetClient->getUserAccountName();
                $dbClient = $clientRepository->findOneBy(['growattName' => $username]);

                if (is_null($dbClient)) {
                    $this->addFlash('error', sprintf('Client %s Not Founded in Database.', $username));

                    return $this->redirectToRoute('app.admin.uploads.reports.generation');
                }

                foreach ($spreadsheetClient->getGenerationDays() as $generationDay) {
                    $dayGeneration = new DayGeneration();

                    $dayGeneration->setGeneration($generationDay->getGeneration());
                    $dayGeneration->setHours($generationDay->getHours());
                    $dayGeneration->setDate($generationDay->getDate());

                    $dbClient->addDayGeneration($dayGeneration);

                    $dayGenerationRepository->save($dayGeneration);

                    $entityManager->flush();
                }
            }

            // return $this->redirectToRoute('app.admin.clients.generation.show', ['client' => $client->getId()]);
        }

        $errors = $form->getErrors();

        return $this->render('admin/upload/reports/generation.html.twig', [
            'form' => $form,
            'errors' => $errors,
        ]);
    }

    private function getReportFile(FormInterface $form): GrowattSpreadsheet
    {
        /** @var UploadedFile $report */
        $report = $form->get('report')->getData();

        $reportFolder = $this->getParameter('kernel.project_dir').'/var/uploads/';
        $reportFileName = $report->getClientOriginalName();
        $report->move($reportFolder, $reportFileName);

        $reader = ReaderFactory::fromFile($reportFolder.$reportFileName);
        $reportSpreadsheet = $reader->search();

        $filesystem = new Filesystem();
        $filesystem->remove($reportFolder.$reportFileName);

        return $reportSpreadsheet;
    }
}
