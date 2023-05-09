<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Trismegiste\Bronze\Wikitext;

use Trismegiste\Strangelove\MongoDb\Repository;
use Wikimedia\Parsoid\Config\DataAccess;
use Wikimedia\Parsoid\Config\PageConfig;
use Wikimedia\Parsoid\Config\PageContent;
use Wikimedia\Parsoid\Core\ContentMetadataCollector;

/**
 * Description of InternalDataAccess
 *
 * @author flo
 */
class InternalDataAccess extends DataAccess
{

    public function __construct(protected Repository $repositoy)
    {
        
    }

    public function doPst(PageConfig $pageConfig, string $wikitext): string
    {
        return preg_replace('/\{\{subst:1x\|([^}]+)\}\}/', '$1', $wikitext, 1);
    }

    public function fetchTemplateData(PageConfig $pageConfig, string $title): ?array
    {
        
    }

    public function fetchTemplateSource(PageConfig $pageConfig, string $title): ?PageContent
    {
        
    }

    public function getFileInfo(PageConfig $pageConfig, array $files): array
    {
        
    }

    public function getPageInfo(PageConfig $pageConfig, string $titles): array
    {
        
    }

    public function logLinterData(PageConfig $pageConfig, array $lints): void
    {
        
    }

    public function parseWikitext(PageConfig $pageConfig, ContentMetadataCollector $metadata, string $wikitext): string
    {
        
    }

    public function preprocessWikitext(PageConfig $pageConfig, ContentMetadataCollector $metadata, string $wikitext): string
    {
        
    }

}
