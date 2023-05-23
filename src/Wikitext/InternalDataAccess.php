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
        return null;
    }

    private function normTitle(string $title): string
    {
        return strtr($title, ' ', '_');
    }

    public function fetchTemplateSource(PageConfig $pageConfig, string $title): ?PageContent
    {
        $normTitle = $this->normTitle($title);
    }

    public function getFileInfo(PageConfig $pageConfig, array $files): array
    {
        
    }

    public function getPageInfo(PageConfig $pageConfig, array $titles): array
    {
        $ret = [];

        // database
        $iterator = $this->repositoy->search(['title' => ['$in' => $titles]], ['content']);
        foreach ($iterator as $vertex) {
            $ret[$vertex->getTitle()] = [
                'pageId' => $vertex->getPk(),
                'revId' => 1,
                'missing' => false,
                'known' => true,
                'redirect' => false,
                'linkclasses' => []
            ];
        }

        // fill the missing
        foreach ($titles as $title) {
            if (!key_exists($title, $ret)) {
                $ret[$title] = [
                    'pageId' => null,
                    'revId' => null,
                    'missing' => true,
                    'known' => false,
                    'redirect' => false,
                    'linkclasses' => [],
                ];
            }
        }

        return $ret;
    }

    public function logLinterData(PageConfig $pageConfig, array $lints): void
    {
        // nothing
    }

    public function parseWikitext(PageConfig $pageConfig, ContentMetadataCollector $metadata, string $wikitext): string
    {
        return '';
    }

    public function preprocessWikitext(PageConfig $pageConfig, ContentMetadataCollector $metadata, string $wikitext): string
    {
        return '';
    }

}
