<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    /**
     * @Route("/stats", name="stats")
     */
    public function index()
    {
        return $this->render('stats/index.html.twig', [
            'controller_name' => 'StatsController',
            'title' => 'Estadísticas Poemas por Teléfono',
            'hashtag' => 'poemasxtelefono'
        ]);
    }
}
