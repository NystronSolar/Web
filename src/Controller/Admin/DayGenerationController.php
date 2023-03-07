<?php

namespace App\Controller\Admin;

use App\Entity\DayGeneration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/day_generations', name: 'app.admin.day_generations.')]
class DayGenerationController extends AbstractController
{
    #[Route(path: '/{dayGeneration}', name: 'show', methods: 'GET', requirements: ['day_generation' => '\d+'])]
    public function show(Request $request, DayGeneration $dayGeneration): Response
    {
        dd($dayGeneration);

        // return $this->render('admin/dayGeneration/show.html.twig', [
        //     'dayGeneration' => $dayGeneration,
        // ]);
    }
}
