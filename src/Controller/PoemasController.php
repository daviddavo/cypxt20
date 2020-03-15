<?php


namespace App\Controller;
use App\Entity\OnlineCall;
use App\Form\OnlineCallType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PoemasController extends AbstractController
{
    public function main() {
        $call = new OnlineCall();
        $call->setName("Juanito Valderrama");

        $form = $this->createForm(OnlineCallType::class, $call);

        return $this->render('pxt/index.html.twig', [
            'msg' => 'Hello World!',
            'year'=>2020,
            'form' => $form->createView()]);
    }

}