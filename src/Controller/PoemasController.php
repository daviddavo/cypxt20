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
        $call->setName("Juanito");
        $call->setAge(23);
        $call->setNumber("916 79 01 80");

        $form = $this->createForm(OnlineCallType::class, $call);

        return $this->render('pxt/index.html.twig', [
            'title' => 'Poemas por Teléfono',
            'hashtag'=>'poemasxtelefono',
            'form' => $form->createView()]);
    }

}