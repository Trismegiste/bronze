<?php

/*
 * Bronze
 */

namespace Trismegiste\Bronze\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wikimedia\Parsoid\Mocks\MockDataAccess;
use Wikimedia\Parsoid\Mocks\MockPageConfig;
use Wikimedia\Parsoid\Mocks\MockPageContent;
use Wikimedia\Parsoid\Mocks\MockSiteConfig;
use Wikimedia\Parsoid\Parsoid;

/**
 * Description of Parsoid
 *
 * @author florent
 */
#[AsCommand(name: 'parsoid')]
class Parser extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Parsoid');
        $opts = [];

        $parserOpts = [
            'body_only' => true,
            'wrapSections' => false,
        ];

        $siteConfig = new MockSiteConfig($opts);
        $dataAccess = new MockDataAccess($opts);
        $parsoid = new Parsoid($siteConfig, $dataAccess);

        $pageContent = new MockPageContent(['main' => "{|\n|tab\n|}'''YOLO [[toto|titi]]'''"]);
        $pageConfig = new MockPageConfig($opts, $pageContent);
        $out = $parsoid->wikitext2html($pageConfig, $parserOpts);

        var_dump($out);

        return self::SUCCESS;
    }

}
