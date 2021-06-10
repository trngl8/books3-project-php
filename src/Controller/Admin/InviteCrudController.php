<?php

namespace App\Controller\Admin;

use App\Entity\Invite;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class InviteCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Invite::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email'),
        ];
    }
}