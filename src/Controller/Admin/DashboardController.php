<?php

namespace App\Controller\Admin;

use App\Entity\Line;
use App\Entity\User;
use App\Entity\OnlineCall;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('cypxt')
            ->setTranslationDomain('admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-dashboard');
        yield MenuItem::linkToRoute('Configuración', 'fa fa-gear', 'config');
        yield MenuItem::linkToCrud('Líneas', 'fas fa-phone', Line::class);
        // yield MenuItem::linkToCrud('Usuarios', 'fa fa-user', User::class);
        yield MenuItem::linkToCrud('Datos formulario', 'fas fa-table', OnlineCall::class)->setPermission('ROLE_PERSONALDATA');
        yield MenuItem::linkToRoute('Centralita', 'fas fa-tty', 'lineas');
        yield MenuItem::linkToLogout('Logout', 'fa fa-exit');
    }

    public function configureAssets() : Assets
    {
        return parent::configureAssets()
            ->addWebpackEncoreEntry('admin');
    }
}
