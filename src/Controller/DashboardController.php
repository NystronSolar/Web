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

        $start = new \DateTime('-13 months');
        $start->setDate((int) $start->format('Y'), (int) $start->format('m'), 1);
        $start = \DateTime::createFromFormat('Y-m-d', $start->format('Y-m-d'));

        $end = new \DateTime('-1 month');
        $end->setDate((int) $end->format('Y'), (int) $end->format('m'), (int) $end->format('t'));
        $end = \DateTime::createFromFormat('Y-m-d', $end->format('Y-m-d'));

        $fullSummary = [];
        $generation = '0';
        $hours = '0';
        $daysCounter = 0;

        /** @var DayGeneration[] $yearGenerations */
        $yearGenerations = $dayGenerationRepository->findGenerationBetweenDates($start, $end, $client);
        foreach ($yearGenerations as $dayGeneration) {
            ++$daysCounter;

            $generation = bcadd($generation, $dayGeneration->getGeneration(), 1);
            $hours = bcadd($hours, $dayGeneration->getHours(), 1);

            $dayGenerationDate = $dayGeneration->getDate();
            $daysInMonth = $dayGenerationDate->format('t');

            if ($daysInMonth == $daysCounter) {
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

                $daysCounter = 0;
                $generation = '';
                $hours = '';
            }
        }

        $fullSummary = array_reverse($fullSummary);

        return $this->render('dashboard/index.html.twig', ['is_admin' => $isAdmin, 'summary' => $fullSummary]);
    }

    #[Route(path: '/generation', name: 'generation', methods: 'GET')]
    public function generation(Request $request): Response
    {
        $client = $this->getUser();

        $dayGenerations = $client->getDayGenerations()->map(function (DayGeneration $dayGeneration) use ($client) {
            $seconds = bcmul($dayGeneration->getHours(), 3600);
            $hours = gmdate('H:i', (int) $seconds);

            return [
                'id' => $dayGeneration->getId(),
                'date' => $dayGeneration->getDate(),
                'generation' => $dayGeneration->getGeneration(),
                'hours' => $hours,
            ];
        });

        return $this->render('dashboard/generation.html.twig', [
            'dayGenerations' => $dayGenerations
        ]);
    }
}
