<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(private Security $security) {}

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // where to redirect to
        $target_path = $request->get('target_path');
        if (empty($target_path)) {
            $target_path = $request->headers->get('referer');
        }

        if (empty($target_path))
        {
            $target_path = $this->generateUrl('login_redirect');
        }

        if ($this->security->isGranted('ROLE_USER')) {
            return $this->redirect($target_path);
        }

        // return $this->render('@EasyAdmin/page/login.html.twig', [
        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'target_path'   => $target_path,
        ]);
    }

    /**
     * @Route("/login/redirect", name="login_redirect")
     */
    public function login_redirect(): Response
    {
        if ($this->security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin');
        }

        if ($this->security->isGranted('ROLE_CENTRALITA')) {
            return $this->redirectToRoute('lineas');
        }

        return $this->redirect('/');
    }

    /**
     * @Route("/logout", name="logout", methods={"GET"})
     */
    public function logout(): void
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}

