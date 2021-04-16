<?php


namespace App\Controller;
use App\Entity\Line;
use App\Entity\OnlineCall;
use App\Form\OnlineCallType;

use DateTime;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CallMeController extends AbstractController
{
    private function getParamsFromHostname(Request $request) {
        $subdomain = explode('.', $request->getHost())[0];
        return $this->getParameter($subdomain=='cxt'?'app.cxt':'app.pxt');
    }

    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function main(Request $request) {
        $params = $this->getParamsFromHostname($request);

        return $this->render('pxt/welcome.html.twig', [
            'title' => $params['title'],
            'maxapp' => $this->getParameter('app.maxapplications_perperson'),
            'cypxt_params' => $params
        ]);
    }

    /**
     * @Route("/form", name="form")
     * @return Response
     */
    public function form(Request $request) {
        $call = new OnlineCall();
        $configparams = $this->getParamsFromHostname($request);

        // Creating the form
        $form = $this->createForm(OnlineCallType::class, $call, [
            'singular' => $configparams['cop_sing'],
            'plural' => $configparams['cop_plural']
        ]);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();

        // Form submission
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $data->setIp($request->getClientIp());
            $entityManager->persist($data);
            $entityManager->flush();

            $xdarr = [
                "Oído cocina", "Recibido", "Marchando"
            ];
            $this->addFlash('success', "¡{$xdarr[array_rand($xdarr)]}! {$data->getFromName()}, ¿quieres que le " . $configparams['cop_phrase_question'] . " a alguien más?");

            return $this->redirect($request->getUri());
        }

        // Getting total cnt and number of submissions
        $repo = $this->getDoctrine()->getRepository(OnlineCall::class);
        $cnt = $repo->getTotalCnt();
        $maxapplications = $this->getParameter('app.maxapplications_perperson');
        $remaining = $maxapplications - $repo->countByIp($request->getClientIp());

        // Getting lines (to show if failed)
        $repolines = $this->getDoctrine()->getRepository(Line::class);
        $phonenumbers = $repolines->getPhoneNumbers();

        if ($remaining == 1) {
           $this->addFlash('warning', "Esta es tu ultima petición, así que ¡piensátelo bien!");
        }

        return $this->render('pxt/index.html.twig', [
            'title' => $configparams['title'],
            'cypxt_params' => $configparams,
            'open' => $cnt <= 70 && (new DateTime() > new DateTime('2021-03-13 14:00')),
            'remaining' => $remaining,
            'phonenumbers' => $phonenumbers,
            'form' => $form->createView()]);
    }

}
