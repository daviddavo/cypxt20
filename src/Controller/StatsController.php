<?php

namespace App\Controller;

use App\Entity\OnlineCall;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\Persistence\ManagerRegistry;

class StatsController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}

    /**
     * @Route("/stats", name="stats")
     */
    public function index(Request $request)
    {
        $repo = $this->doctrine->getRepository(OnlineCall::class);
        $subdomain = explode('.', $request->getHost())[0];
        $configparams = $this->getParameter($subdomain=='cxt'?'app.cxt':'app.pxt');

        return $this->render('stats/index.html.twig', [
            'title' => 'Estadísticas',
            'ageCounts' => $repo->getAgeCounts()
        ]);
    }

    public function api()
    {
        $repo = $this->doctrine->getRepository(OnlineCall::class);
        return new JSONResponse([
            "ages" => $repo->getAgeCounts(),
            "expected" => $this->getParameter('app.maxapplications')
        ]);
    }
}
