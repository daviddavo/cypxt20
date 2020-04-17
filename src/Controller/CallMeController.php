<?php


namespace App\Controller;
use App\Entity\OnlineCall;
use App\Form\OnlineCallType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CallMeController extends AbstractController
{
    public function main(Request $request, $subdomain) {
        $call = new OnlineCall();

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
                "Oído cocina", "Recibido"
            ];
            $this->addFlash('success', "¡{$xdarr[array_rand($xdarr)]}! {$data->getName()}, ¿quieres que le recitemos un poema a alguien más?");

            return $this->redirect($request->getUri());
        }

        return $this->render('pxt/index.html.twig', [
            'title' => ($subdomain=='cxt')?'Cuentos por Teléfono':'Poemas por Teléfono',
            'hashtag' => ($subdomain=='cxt')?'poemasxtelefono':'cuentosxtelefono',
            'open' => true,
            'form' => $form->createView()]);
    }

}