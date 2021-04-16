<?php


namespace App\Controller;
use App\Entity\OnlineCall;
use App\Form\OnlineCallType;

use DateTime;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CallMeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function main(Request $request) {
        $call = new OnlineCall();
        $subdomain = explode('.', $request->getHost())[0];
        $configparams = $this->getParameter($subdomain=='cxt'?'app.cxt':'app.pxt');

        $form = $this->createForm(OnlineCallType::class, $call, [
            'singular' => $configparams['cop_sing'],
            'plural' => $configparams['cop_plural']
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
            $this->addFlash('success', "¡{$xdarr[array_rand($xdarr)]}! {$data->getFromName()}, ¿quieres que le " . (($subdomain=='cxt')?'contemos un cuento':'recitemos un poema') . " a alguien más?");

            return $this->redirect($request->getUri());
        }

        $repo = $this->getDoctrine()->getRepository(OnlineCall::class);
        $cnt = $repo->getTotalCnt();


        return $this->render('pxt/index.html.twig', [
            'title' => $configparams['title'],
            'cypxt_params' => $configparams,
            'open' => $cnt <= 70 && (new DateTime() > new DateTime('2021-03-13 14:00')),
            'form' => $form->createView()]);
    }

}
