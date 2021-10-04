<?php

namespace App\Controller\Admin;

use App\Entity\Card;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\BatchActionDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\LanguageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\Model\FileUploadState;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\Form\FormInterface;
use function Symfony\Component\String\u;

class CardCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Card::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('title'),
            TextField::new('description'),
            TextField::new('author'),
            TextField::new('isbn'),
            LanguageField::new('language'),
            IntegerField::new('year'),
            //TODO: set cover in true way
//            ImageField::new('cover')
//                ->setUploadDir('public/uploads/images/cover')
//                ->setBasePath('uploads/images/cover')
//                ->setUploadedFileNamePattern('[contenthash].[extension]')
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->addBatchAction(Action::new('approve', 'Export Cards')
                ->linkToCrudAction('export')
                ->addCssClass('btn btn-primary')
                ->setIcon('fa fa-user-check'))
            ;
    }

    protected function processUploadedFiles(FormInterface $form): void
    {
        /** @var FormInterface $child */
        foreach ($form as $child) {
            $config = $child->getConfig();

            if (!$config->getType()->getInnerType() instanceof FileUploadType) {
                if ($config->getCompound()) {
                    $this->processUploadedFiles($child);
                }

                continue;
            }

            /** @var FileUploadState $state */
            $state = $config->getAttribute('state');

            if (!$state->isModified()) {
                continue;
            }

            $uploadDelete = $config->getOption('upload_delete');

            if ($state->hasCurrentFiles() && ($state->isDelete() || (!$state->isAddAllowed() && $state->hasUploadedFiles()))) {
                foreach ($state->getCurrentFiles() as $file) {
                    $uploadDelete($file);
                }
                $state->setCurrentFiles([]);
            }
            $filePaths = (array) $child->getData();
            $uploadDir = $config->getOption('upload_dir');
            $uploadNew = $config->getOption('upload_new');

            foreach ($state->getUploadedFiles() as $index => $file) {
                $fileName = u($filePaths[$index])->replace($uploadDir, '')->toString();

                $uploadNew($file, $uploadDir, $fileName);
            }
        }
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb
            ->leftJoin('entity.rescripts', 'r')
            ->leftJoin('entity.orders', 'o')
        ;

        return $qb;
    }

    public function export(BatchActionDto $batchActionDto)
    {
        $entityManager = $this->getDoctrine()->getManagerForClass($batchActionDto->getEntityFqcn());
//        foreach ($batchActionDto->getEntityIds() as $id) {
//            $user = $entityManager->find($id);
//            $user->approve();
//        }
//
//        $entityManager->flush();

        $this->addFlash('success', 'Cards exported');

        //TODO: generate csv file
        return $this->redirect($batchActionDto->getReferrerUrl());
    }
}