<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/info', name: 'app.info.')]
class InfoController extends AbstractController
{
    #[Route(path: '/credits', name: 'credits', methods: 'GET')]
    public function credits(): Response
    {
        $credits = [
            'Icons' => [
                [
                    "Link" => "https://thenounproject.com/icon/home-1560062/",
                    "Title" => "Home",
                    "AuthorLink" => "https://thenounproject.com/il.capitano/",
                    "Author" => "il Capitano",
                    "CorporationLink" => "https://thenounproject.com/",
                    "Corporation" => "Noun Project"
                ],
                [
                    "Link" => "https://thenounproject.com/icon/electric-bill-2864536/",
                    "Title" => "Electric Bill",
                    "AuthorLink" => "https://thenounproject.com/auliausu/",
                    "Author" => "Aulia Rahman",
                    "CorporationLink" => "https://thenounproject.com/",
                    "Corporation" => "Noun Project"
                ],
                [
                    "Link" => "https://thenounproject.com/icon/solar-powered-home-1271326/",
                    "Title" => "Solar Powered Home",
                    "AuthorLink" => "https://thenounproject.com/symbolon/",
                    "Author" => "Symbolon",
                    "CorporationLink" => "https://thenounproject.com/",
                    "Corporation" => "Noun Project"
                ]
            ]
        ];

        return $this->render('info/credits.html.twig', ['credits' => $credits]);
    }
}