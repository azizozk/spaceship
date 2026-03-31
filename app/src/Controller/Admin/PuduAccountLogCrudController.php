<?php

namespace App\Controller\Admin;

use App\Entity\PuduAccountLog;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CodeEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\NumericFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

class PuduAccountLogCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PuduAccountLog::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(Action::NEW);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            // ->add(EntityFilter::new('puduAccount'))
            ->add(TextFilter::new('method'))
            ->add(TextFilter::new('uri'))
            ->add(NumericFilter::new('responseCode'))
            ->add(DateTimeFilter::new('executedAt'));
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('method'),
            TextField::new('uri'),
            Field::new('body')->setSortable(false)
                ->formatValue(static fn ($v) => $v !== null ? json_encode($v, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE) : null),
            IntegerField::new('responseCode'),
            Field::new('responseBody')->setSortable(false)
                ->formatValue(static fn ($v) => $v !== null ? json_encode($v, \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE) : null),
            DateTimeField::new('executedAt'),
        ];
    }
}
