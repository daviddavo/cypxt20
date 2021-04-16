<?php

namespace App\Controller;

use App\Entity\OnlineCall;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class StatsController extends AbstractController
{
    /**
     * @Route("/stats", name="stats")
     */
    public function index(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository(OnlineCall::class);
        $subdomain = explode('.', $request->getHost())[0];
        $configparams = $this->getParameter($subdomain=='cxt'?'app.cxt':'app.pxt');

        return $this->render('stats/index.html.twig', [
            'title' => 'Estadísticas',
            'cypxt_params' => $configparams,
            'ageCounts' => $repo->getAgeCounts()
        ]);
    }

    public function api()
    {
        $repo = $this->getDoctrine()->getRepository(OnlineCall::class);
        return new JSONResponse([
            "ages" => $repo->getAgeCounts(),
            "hours" => $repo->getHourCounts(),
            "expected" => $this->getParameter('app.maxapplications')
        ]);
    }
}
