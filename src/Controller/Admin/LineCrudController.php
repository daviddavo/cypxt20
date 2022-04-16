<?php

namespace App\Controller\Admin;

use App\Entity\Line;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class LineCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Line::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('status')
            ->setFormType(ChoiceType::class)
            ->setFormTypeOptions([
                'choices' => ['Abierto'=>'idle', 'Cerrado'=>'busy'],
            ]);
        yield TextField::new('description');
        yield DateTimeField::new('last_open');
        yield DateTimeField::new('last_close');
        yield TextField::new('phone_number');
    }
}
