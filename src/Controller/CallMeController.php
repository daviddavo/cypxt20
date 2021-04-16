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

    private function isOpen() : bool {
        $repo = $this->getDoctrine()->getRepository(OnlineCall::class);
        $cnt = $repo->getTotalCnt();

        // return false;
        return $cnt <= $this->getParameter('app.maxapplications');
    }

    public function closed($params) {
        $repolines = $this->getDoctrine()->getRepository(Line::class);
        $phonenumbers = $repolines->getPhoneNumbers();

        return $this->render('pxt/closed.html.twig', [
            'cypxt_params' => $params,
            'phonenumbers' => $phonenumbers
        ]);
    }

    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function main(Request $request) {
        $params = $this->getParamsFromHostname($request);


        if ($this->isOpen()) {
            return $this->render('pxt/welcome.html.twig', [
                'title' => $params['title'],
                'maxapp' => $this->getParameter('app.maxapplications_perperson'),
                'cypxt_params' => $params
            ]);
        } else {
            return $this->closed($params);
        }

        return $this->render('pxt/welcome.html.twig', [
            'title' => $params['title'],
            'maxapp' => $this->getParameter('app.maxapplications_perperson'),
            'cypxt_params' => $params,
            'open' => $this->isOpen(),
            'phonenumbers' => $phonenumbers
        ]);
    }

    /**
     * @Route("/form", name="form")
     * @return Response
     */
    public function form(Request $request) {
        $params = $this->getParamsFromHostname($request);
        
        if (!$this->isOpen()) 
            return $this->closed($params);
        
        $call = new OnlineCall();

        // Creating the form
        $form = $this->createForm(OnlineCallType::class, $call, [
            'singular' => $params['cop_sing'],
            'plural' => $params['cop_plural']
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
            $this->addFlash('success', "¡{$xdarr[array_rand($xdarr)]}! {$data->getFromName()}, ¿quieres que le " . $params['cop_phrase_question'] . " a alguien más?");

            return $this->redirect($request->getUri());
        }

        // Getting total cnt and number of submissions
        $repo = $this->getDoctrine()->getRepository(OnlineCall::class);
        $maxapplications = $this->getParameter('app.maxapplications_perperson');
        $remaining = $maxapplications - $repo->countByIp($request->getClientIp());

        if ($remaining == 1) {
           $this->addFlash('warning', "Esta es tu ultima petición, así que ¡piensátelo bien!");
        }

        return $this->render('pxt/index.html.twig', [
            'title' => $params['title'],
            'cypxt_params' => $params,
            'remaining' => $remaining,
            'form' => $form->createView()]);
    }

}
