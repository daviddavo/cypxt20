<?php

namespace App\Controller\Admin;

use App\Entity\Line;
use App\Entity\User;
use App\Entity\OnlineCall;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Sherlockode\ConfigurationBundle\Form\Type\ParametersType;
use Sherlockode\ConfigurationBundle\Manager\ParameterManagerInterface;

class ConfigController extends AbstractController
{
    public function __construct(private ParameterManagerInterface $parameterManager) {}

    #[Route('/config', name: 'config')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(Request $request): Response
    {
        $data = $this->parameterManager->getAll();

        $form = $this->createForm(ParametersType::class, $data)
            ->add('Guardar', SubmitType::class);

        // handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $params = $form->getData();
            foreach ($params as $path => $value) {
                $this->parameterManager->set($path, $value);
            }
            $this->parameterManager->save();
        }

        return $this->render('admin/config.html.twig', [
            'appConfigForm' => $form->createView(),
        ]);
    }
}
