<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Trismegiste\Bronze\Twig;

use Trismegiste\Bronze\Entity\MagicEntity;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Extension for paths
 */
class PathExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('crud_create', [$this, 'create']),
            new TwigFunction('crud_list', [$this, 'list']),
            new TwigFunction('crud_show', [$this, 'show']),
            new TwigFunction('crud_edit', [$this, 'edit']),
            new TwigFunction('crud_delete', [$this, 'delete']),
        ];
    }

    public function create(string $entityAlias): string
    {
        return "/$entityAlias/new/create";
    }

    public function list(string $entityAlias): string
    {
        return "/$entityAlias";
    }

    public function show(MagicEntity $entity): string
    {
        return "/{$entity->__entity}/{$entity->getPk()}/show";
    }

    public function edit(MagicEntity $entity): string
    {
        return "/{$entity->__entity}/{$entity->getPk()}/edit";
    }

    public function delete(MagicEntity $entity): string
    {
        return "/{$entity->__entity}/{$entity->getPk()}/delete";
    }

}
