<?php

namespace App\Controller;

use App\DataProvider\ClientDataProvider;
use App\DataProvider\DayGenerationDataProvider;
use App\Entity\Client;
use App\Repository\ClientRepository;
use App\Repository\DayGenerationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard', name: 'app.dashboard.')]
class DashboardController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: 'GET')]
    public function index(Request $request, ClientRepository $clientRepository, DayGenerationRepository $dayGenerationRepository, TranslatorInterface $translator): Response
    {
        /** @var Client $client */
        $client = $this->getUser();
        $isAdmin = array_search('ROLE_ADMIN', $client->getRoles()) ? true : false;
        $provider = new DayGenerationDataProvider($translator);

        $yearGenerations = $dayGenerationRepository->findLastYearGeneration($client);
        $fullSummary = $provider->groupByMonth($yearGenerations, true, true);

        return $this->render('dashboard/index.html.twig', ['is_admin' => $isAdmin, 'summary' => $fullSummary]);
    }

    #[Route(path: '/generation', name: 'generation', methods: 'GET')]
    public function generation(Request $request, TranslatorInterface $translator): Response
    {
        $client = $this->getUser();

        $clientProvider = new ClientDataProvider($translator);
        $dayGenerationProvider = new DayGenerationDataProvider($translator);

        $chart = $clientProvider->getClientGenerationChart($client);
        $dayGenerations = $client->getDayGenerations()->map(\Closure::fromCallable([$dayGenerationProvider, 'styleDayGeneration']));

        return $this->render('dashboard/generation.html.twig', [
            'dayGenerations' => $dayGenerations,
            'chart' => $chart,
        ]);
    }
}
