<?php

namespace EzSystems\UserGeneratedContent\Form\Type;

use eZ\Publish\API\Repository\ContentTypeService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class ContentFormType extends AbstractType
{
    /** @var \eZ\Publish\API\Repository\ContentTypeService */
    private $contentTypeService;

    public function __construct(
        ContentTypeService $contentTypeService
    ) {
        $this->contentTypeService = $contentTypeService;
    }

    public function getBlockPrefix(): string
    {
        return 'ezplatform_fieldtype_ezugc';
    }

    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ) {
        $builder
            ->add('content_type_identifier', ChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'choices' => $this->getContentTypeChoices(),
            ])
            ->add('fields', CollectionType::class, [
                'entry_type' => ContentFieldFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true,
            ]);
    }

    private function getContentTypeChoices(): array
    {
        $choices = [];

        $groups = $this->contentTypeService->loadContentTypeGroups();
        foreach ($groups as $group) {
            $contentTypes = $this->contentTypeService->loadContentTypes($group);
            foreach ($contentTypes as $contentType) {
                $choices[$group->identifier][$contentType->getName()] = $contentType->identifier;
            }
        }

        return $choices;
    }
}
