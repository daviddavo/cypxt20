<?php


namespace App\Controller;
use App\Entity\Line;
use App\Entity\OnlineCall;
use App\Service\CypxtParams;
use App\Form\OnlineCallType;

use DateTime;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sherlockode\ConfigurationBundle\Manager\ParameterManagerInterface;

use Doctrine\Persistence\ManagerRegistry;

class CallMeController extends AbstractController
{
    public function __construct(
        private ManagerRegistry $doctrine, 
        private CypxtParams $params, 
        private RequestStack $requestStack, 
        private Security $security,
        private ParameterManagerInterface $pmi,
    ) {}

    private function isOpen() : bool {
        $query = $this->requestStack->getCurrentRequest()->query;
        if ($query->has('forceClose')) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        $repo = $this->doctrine->getRepository(OnlineCall::class);
        $cnt = $repo->getTotalCnt();

        return $cnt < $this->pmi->get('max_applications');
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
        if (!$this->isOpen()) {
            return $this->closed();
        }

        return $this->render('pxt/welcome.html.twig', [
            'maxapp' => $this->pmi->get('max_applications_perperson')
        ]);
    }

    /**
     * @Route("/form", name="form")
     * @return Response
     */
    public function form(Request $request) {
        $call = new OnlineCall();

        // Creating the form
        $form = $this->createForm(OnlineCallType::class, $call, [
            'singular' => $this->params->cop_sing(),
            'plural' => $this->params->cop_plural(),
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
                $this->addFlash('success', "¡{$xdarr[array_rand($xdarr)]}! {$data->getFromName()}, ¿quieres que le " . $this->params->cop_phrase_question() . " a alguien más?");
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
        $maxapplications = $this->pmi->get('max_applications');
        $remaining = $maxapplications - $repo->countByIp($request->getClientIp());

        if ($remaining == 1) {
           $this->addFlash('warning', "Esta es tu ultima petición, así que ¡piensátelo bien!");
        } else if ($remaining <= 0) {
            return $this->closed(false);
        }

        return $this->render('pxt/index.html.twig', [
            'title' => $this->params->title(),
            'form' => $form->createView()]);
    }

}
