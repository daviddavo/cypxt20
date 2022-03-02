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

use Doctrine\Persistence\ManagerRegistry;

class CallMeController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}

    private function getParamsFromHostname(Request $request) {
        $subdomain = explode('.', $request->getHost())[0];
        return $this->getParameter($subdomain=='cxt'?'app.cxt':'app.pxt');
    }

    private function isOpen() : bool {
        $repo = $this->doctrine->getRepository(OnlineCall::class);
        $cnt = $repo->getTotalCnt();

        return $cnt < $this->getParameter('app.maxapplications');
    }

    public function closed($remaining=true) {

        $repolines = $this->doctrine->getRepository(Line::class);
        $phonenumbers = $repolines->getPhoneNumbers();

        return $this->render('pxt/closed.html.twig', [
            'phonenumbers' => $phonenumbers,
            'remaining' => $remaining
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
                'maxapp' => $this->getParameter('app.maxapplications_perperson'),
            ]);
        } else {
            return $this->closed();
        }

        return $this->render('pxt/welcome.html.twig', [
            'title' => $params['title'],
            'maxapp' => $this->getParameter('app.maxapplications_perperson'),
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
        
        $call = new OnlineCall();

        // Creating the form
        $form = $this->createForm(OnlineCallType::class, $call, [
            'singular' => $params['cop_sing'],
            'plural' => $params['cop_plural']
        ]);

        $form->handleRequest($request);
        $entityManager = $this->doctrine->getManager();

        // Form submission
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $data = $form->getData();
                $data->setIp($request->getClientIp());
                $entityManager->persist($data);
                $entityManager->flush();

                $xdarr = [
                    "Oído cocina", "Recibido", "Marchando"
                ];
                $this->addFlash('success', "¡{$xdarr[array_rand($xdarr)]}! {$data->getFromName()}, ¿quieres que le " . $params['cop_phrase_question'] . " a alguien más?");
            } else {
                $this->addFlash('error', 'Lo sentimos, ha habido un error al entregar el formulario...');
            }

            return $this->redirect($request->getUri());
        }

        if (!$this->isOpen()) {
            return $this->redirectToRoute("home", [], 307);
        }

        // Getting total cnt and number of submissions
        $repo = $this->doctrine->getRepository(OnlineCall::class);
        $maxapplications = $this->getParameter('app.maxapplications_perperson');
        $remaining = $maxapplications - $repo->countByIp($request->getClientIp());

        if ($remaining == 1) {
           $this->addFlash('warning', "Esta es tu ultima petición, así que ¡piensátelo bien!");
        } else if ($remaining <= 0) {
            return $this->closed(false);
        }

        return $this->render('pxt/index.html.twig', [
            'title' => $params['title'],
            'form' => $form->createView()]);
    }

}
