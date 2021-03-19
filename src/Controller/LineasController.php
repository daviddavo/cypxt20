<?php
namespace App\Controller;

use App\Entity\Line;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class LineasController extends AbstractController {
    /**
     * @Route("/lineas", name="lineas")
     */
    public function main() {
        $repo = $this->getDoctrine()->getRepository(Line::class);

        return $this->render('pxt/lineas.html.twig', [
            'lineas' => $repo->findAll()
        ]);
    }

    /**
     * @Route("/api/lineas", name="api_lineas")
     */
    public function api() {
        $repo = $this->getDoctrine()->getRepository(Line::class);
        return new JsonResponse($repo->getAll());
    }
}