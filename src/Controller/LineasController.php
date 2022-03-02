<?php
namespace App\Controller;

use DateTime;
use App\Entity\Line;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

use Doctrine\Persistence\ManagerRegistry;

class LineasController extends AbstractController {
    public function __construct(private ManagerRegistry $doctrine) {}

    /**
     * @Route("/lineas", name="lineas")
     */
    public function main(Request $request) {
        $repo = $this->doctrine->getRepository(Line::class);
        $subdomain = explode('.', $request->getHost())[0];

        return $this->render('pxt/lineas.html.twig', [
            'lineas' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/lineas/{id}", name="linea_id", methods={"GET"})
     */
    public function mainLinea(Request $request, int $id) {
        $repo = $this->doctrine->getRepository(Line::class);
        $prev = null;
        $linea = null;
        $next = null;

        // TODO: Optimize this
        foreach ($repo->findAll() as $i) {
            if (!is_null($linea)) {
                $next = $i;
                break;
            }

            if ($i->getId() == $id) {
                $linea = $i;
            } else {
                $prev = $i;
            }
        }

        if (is_null($linea)) {
            throw $this->createNotFoundException("Linea no encontrada");
        }

        return $this->render('pxt/linea.html.twig', [
            'linea' => $linea,
            'prev' => $prev,
            'next' => $next,
            'hidden' => $request->query->has('hideUsage')?'hidden':'',
        ]);
    }

    /**
     * @Route("/api/lineas", name="api_lineas", methods={"GET"})
     */
    public function api() {
        $repo = $this->doctrine->getRepository(Line::class);
        $r = [];
        foreach ($repo->findAll() as $item) {
            $r[$item->getId()] = [
                'id' => $item->getId(),
                'description' => $item->getDescription(),
                'status' => $item->getStatus(),
                'last_open' => $item->getLastOpen()->format(DateTime::ISO8601),
                'last_close' => $item->getLastClose()->format(DateTime::ISO8601),
                'phone_number' => $item->getPhoneNumber(),
            ];
        }
        return new JsonResponse($r);
    }

    /**
     * @Route("/api/lineas/toggle/{id}", name="api_linea_toggle", methods={"GET", "PUT"})
     */
    public function api_linea_toggle(int $id) {
        $repo = $this->doctrine->getRepository(Line::class);
        $l = $repo->find($id);
        if (is_null($l)) {
            throw $this->createNotFoundException("Linea no encontrada");
        }

        $l->toggleStatus();

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($l);
        $entityManager->flush();

        return new JsonResponse(["status"=>"OK"]);
    }
}