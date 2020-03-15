<?php


namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PoemasController extends AbstractController
{
    public function main() {
        return $this->render('pxt/index.html.twig', ['msg' => 'Hello World!', 'year'=>2020]);
    }

}