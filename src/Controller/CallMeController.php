<?php


namespace App\Controller;
use App\Entity\OnlineCall;
use App\Form\OnlineCallType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CallMeController extends AbstractController
{
    public function main(Request $request) {
        $call = new OnlineCall();
        $subdomain = explode('.', $request->getHost())[0];

        $form = $this->createForm(OnlineCallType::class, $call, [
            'singular' => ($subdomain=='cxt')?'cuento':'poema',
            'plural' => ($subdomain=='cxt')?'cuentos':'poemas'
        ]);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $entityManager->persist($data);
            $entityManager->flush();

            $xdarr = [
                "Oído cocina", "Recibido", "Marchando"
            ];
            $this->addFlash('success', "¡{$xdarr[array_rand($xdarr)]}! {$data->getName()}, ¿quieres que le " . (($subdomain=='cxt')?'contemos un cuento':'recitemos un poema') . " a alguien más?");

            return $this->redirect($request->getUri());
        }

        $repo = $this->getDoctrine()->getRepository(OnlineCall::class);
        $cnt = $repo->getTotalCnt();

        return $this->render('pxt/index.html.twig', [
            'title' => ($subdomain=='cxt')?'Cuentos por Teléfono':'Poemas por Teléfono',
            'hashtag' => ($subdomain=='cxt')?'cuentosxtelefono':'poemasxtelefono',
            'open' => $cnt <= 60,
            'fecha' => "Lunes, 22 de Marzo de 2021",
            'form' => $form->createView()]);
    }

}
