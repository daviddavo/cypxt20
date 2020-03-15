<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LineasController extends AbstractController {
    public function main() {
        return $this->render('pxt/lineas.html.twig');
    }
}