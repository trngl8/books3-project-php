<?php

namespace App\Controller\Admin;

use App\Entity\Rescript;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RescriptCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Rescript::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('card'),
            Field::new('condition'),
            TextField::new('uri'),
        ];
    }
}