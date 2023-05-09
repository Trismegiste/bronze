<?php

/*
 * Bronze
 */

namespace Trismegiste\Bronze\Wikitext;

use BadMethodCallException;
use Wikimedia\Parsoid\Config\SiteConfig;
use Wikimedia\Parsoid\Core\ContentMetadataCollector;
use Wikimedia\Parsoid\DOM\Document;

/**
 * Description of InternalSiteConfig
 *
 * @author florent
 */
class InternalSiteConfig extends SiteConfig
{

    protected $namespaceMap = [
        'media' => -2,
        'special' => -1,
        '' => 0,
        'file' => 6,
        'category' => 14,
    ];

    protected function getMagicWords(): array
    {
        return [];
    }

    protected function getNonNativeExtensionTags(): array
    {
        return [];
    }

    protected function getParameterizedAliasMatcher(string $words): callable
    {
        return fn($str) => null;
    }

    protected function getProtocols(): array
    {
        return ["http:", "https:"];
    }

    protected function getSpecialNSAliases(): array
    {
        return [];
    }

    protected function getSpecialPageAliases(string $specialPage): array
    {
        return [];
    }

    protected function getVariableIDs(): array
    {
        return []; // None for now
    }

    protected function linkTrail(): string
    {
        throw new BadMethodCallException('Should not be used. linkTrailRegex() is overridden here.');
    }

    public function allowedExternalImagePrefixes(): array
    {
        return [];
    }

    public function baseURI(): string
    {
        return '//127.0.0.1:8000/wiki';
    }

    public function bswRegexp(): string
    {
        return '/NOGLOBAL/';
    }

    public function canonicalNamespaceId(string $name): ?int
    {
        return $this->namespaceMap[$name] ?? null;
    }

    public function categoryRegexp(): string
    {
        return '/Category/';
    }

    public function exportMetadataToHead(Document $document, ContentMetadataCollector $metadata, string $defaultTitle, string $lang): void
    {
        
    }

    public function getExternalLinkTarget()
    {
        
    }

    public function getMagicWordMatcher(string $id): string
    {
        
    }

    public function getMaxTemplateDepth(): int
    {
        
    }

    public function getNoFollowConfig(): array
    {
        
    }

    public function interwikiMagic(): bool
    {
        
    }

    public function interwikiMap(): array
    {
        
    }

    public function iwp(): string
    {
        
    }

    public function lang(): string
    {
        
    }

    public function langConverterEnabled(string $lang): bool
    {
        
    }

    public function legalTitleChars(): string
    {
        
    }

    public function linkPrefixRegex(): ?string
    {
        
    }

    public function mainpage(): string
    {
        
    }

    public function namespaceCase(int $ns): string
    {
        
    }

    public function namespaceHasSubpages(int $ns): bool
    {
        
    }

    public function namespaceId(string $name): ?int
    {
        
    }

    public function namespaceName(int $ns): ?string
    {
        
    }

    public function redirectRegexp(): string
    {
        
    }

    public function responsiveReferences(): array
    {
        
    }

    public function rtl(): bool
    {
        
    }

    public function script(): string
    {
        
    }

    public function scriptpath(): string
    {
        
    }

    public function server(): string
    {
        
    }

    public function specialPageLocalName(string $alias): ?string
    {
        
    }

    public function timezoneOffset(): int
    {
        
    }

    public function variants(): array
    {
        
    }

    public function widthOption(): int
    {
        
    }

}
