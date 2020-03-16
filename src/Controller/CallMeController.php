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
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($form->getData());
            $entityManager->flush();

            return $this->redirectToRoute('callme_success');
        }

        return $this->render('pxt/index.html.twig', [
            'title' => 'Poemas por Teléfono',
            'hashtag'=>'poemasxtelefono',
            'form' => $form->createView()]);
    }

}