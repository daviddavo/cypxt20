<?php

namespace App\Controller;

use App\Entity\OnlineCall;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    /**
     * @Route("/stats", name="stats")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(OnlineCall::class);

        return $this->render('stats/index.html.twig', [
            'title' => 'Estadísticas',
            'hashtag' => 'poemasxtelefono',
            'ageCounts' => $repo->getAgeCounts()
        ]);
    }

    public function api()
    {
        return new JSONResponse([
            "ages" => $this->getDoctrine()->getRepository(OnlineCall::class)->getAgeCounts()
        ]);
    }
}