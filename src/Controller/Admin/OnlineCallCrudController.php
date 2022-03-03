<?php

namespace App\Controller\Admin;

use App\Entity\OnlineCall;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class OnlineCallCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OnlineCall::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('name', 'Nombre para');
        yield IntegerField::new('age');
        yield TextField::new('number');
        yield TextField::new('from_name', 'Nombre de');
        yield TextField::new('ip', 'Dirección IP');
        yield DateTimeField::new('created_at');
        yield TextareaField::new('comment');
    }
}
