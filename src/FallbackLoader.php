<?php

/*
 * Bronze
 */

namespace Trismegiste\Bronze;

use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Loader for default LCRUD templates
 * Design pattern : Decorator
 */
class FallbackLoader implements LoaderInterface
{

    protected LoaderInterface $wrapped;

    public function __construct()
    {
        $this->wrapped = new FilesystemLoader(__DIR__ . '/Resources/templates');
    }

    protected function extractTemplateName(string $name): string
    {
        if (preg_match('#^[A-Za-z0-9_-]+/(show|form|delete|create|edit).html.twig$#', $name, $matches)) {
            return $matches[1] . '.html.twig';
        }

        return $name;
    }

    public function exists(string $name): bool
    {
        return $this->wrapped->exists($this->extractTemplateName($name));
    }

    public function getCacheKey(string $name): string
    {
        return $this->wrapped->getCacheKey($this->extractTemplateName($name));
    }

    public function getSourceContext(string $name): Source
    {
        return $this->wrapped->getSourceContext($this->extractTemplateName($name));
    }

    public function isFresh($name, int $time): bool
    {
        return $this->wrapped->isFresh($this->extractTemplateName($name), $time);
    }

}
