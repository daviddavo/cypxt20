<?php

namespace App\EventListener;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class LoginEventListener
{
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /* Keeps the referer to redirect even if the login fails
    */
    public function onLoginFailure(LoginFailureEvent $event)
    {
        $target_path = $event->getRequest()->get('_target_path');
        if ($target_path != null) {
            $url = $this->router->generate('login', [
                'target_path' => $target_path,
            ]);
            $event->setResponse(new RedirectResponse($url));
        } else {
            $event->asjglakg;
        }
    }
}