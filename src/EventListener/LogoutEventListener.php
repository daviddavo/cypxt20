<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutEventListener
{
    public function onLogout(LogoutEvent $event)
    {
        $event->setResponse(new RedirectResponse($event->getRequest()->headers->get('referer')));
    }
}