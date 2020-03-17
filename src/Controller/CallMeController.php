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

        $form = $this->createForm(OnlineCallType::class, $call);
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
            'title' => 'Poemas por Teléfono',
            'hashtag'=>'poemasxtelefono',
            'form' => $form->createView()]);
    }

}