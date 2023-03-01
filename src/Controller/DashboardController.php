<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\DayGeneration;
use App\Repository\ClientRepository;
use App\Repository\DayGenerationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard', name: 'app.dashboard.')]
class DashboardController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: 'GET')]
    public function index(Request $request, ClientRepository $clientRepository, DayGenerationRepository $dayGenerationRepository): Response
    {
        /** @var Client $client */
        $client = $this->getUser();
        $isAdmin = array_search('ROLE_ADMIN', $client->getRoles()) ? true : false;

        $start = new \DateTime('-1 year');
        $start = \DateTime::createFromFormat('Y-m-d', $start->format('Y-m-01'));

        $end = new \DateTime();
        $end = \DateTime::createFromFormat('Y-m-d', $end->format('Y-m-01'));

        $fullSummary = [];
        $generation = '0';
        $hours = '0';

        /** @var $yearGenerations DayGeneration[] */
        $yearGenerations = $dayGenerationRepository->findGenerationBetweenDates($start, $end, $client);
        foreach ($yearGenerations as $dayGeneration) {
            $generation = bcadd($generation, $dayGeneration->getGeneration(), 1);
            $hours = bcadd($hours, $dayGeneration->getHours(), 1);

            $dayGenerationDate = $dayGeneration->getDate();
            $daysInMonth = $dayGenerationDate->format('t');

            $day = $dayGenerationDate->format('d');

            if ($day === $daysInMonth) {
                $month = $dayGenerationDate->format('m');
                $year = $dayGenerationDate->format('Y');

                $fullSummary[] = [
                    'date' => [
                        'month' => $month,
                        'year' => $year,
                    ],
                    'generation' => $generation,
                    'hours' => $hours,
                ];

                $generation = '';
                $hours = '';
            }
        }

        return $this->render('dashboard/index.html.twig', ['is_admin' => $isAdmin, 'summary' => $fullSummary]);
    }
}
