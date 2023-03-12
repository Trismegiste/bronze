<?php

/*
 * Bronze
 */

namespace Trismegiste\Bronze\Command;

use MongoDB\Driver\Manager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Trismegiste\Bronze\Entity\MagicEntity;
use Trismegiste\Strangelove\MongoDb\RepositoryFactory;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Generate PHP entity from the DB
 */
class GenerateEntity extends Command
{

    protected static $defaultName = 'generate:entity:fromdb';

    protected function configure()
    {
        $this
                ->setDescription('Generates PHP entity from the DB')
                ->addArgument('database', InputArgument::REQUIRED, 'Database name')
                ->addArgument('collection', InputArgument::REQUIRED, 'Collection name')
                ->addOption('filter', 'f', InputOption::VALUE_REQUIRED, 'Filter on entity name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $mongo = new Manager();
        $fac = new RepositoryFactory($mongo, $input->getArgument('database'));
        $repository = $fac->create($input->getArgument('collection'));
        $filter = !empty($input->getOption('filter')) ? ['__entity' => $input->getOption('filter')] : [];
        $iter = $repository->search($filter, [], '_id');
        $rows = [];
        foreach ($iter as $entity) {
            $attr = $entity->getAttributes();
            unset($attr[array_search('__entity', $attr)]);
            $rows[] = [
                count($rows), $entity->getPk(), $entity->__entity, implode(', ', $attr)
            ];
        }

        $io->table(['idx', 'pk', 'entity', 'properties'], $rows);
        $idx = $io->ask('Which document do you want to transform into PHP entity', 0);
        $pk = $rows[$idx][1];
        $io->confirm("You're about to generate a new class into Entity folder for document $pk", true);

        $found = $repository->load($pk);
        file_put_contents(__DIR__ . '/../Entity/' . ucfirst($found->__entity) . '.php', $this->generate($found));

        return self::SUCCESS;
    }

    protected function generate(MagicEntity $entity): string
    {
        $attr = $entity->getAttributes();
        $entity_alias = $entity->__entity;

        // managing type
        $properties = [];
        foreach ($attr as $key) {
            if ('__entity' === $key) {
                continue;
            }
            $properties[$key] = $this->guessType($entity->$key);
        }

        $twig = new Environment(new FilesystemLoader(__DIR__));

        return $twig->render('magic_entity.php.twig', ['entity_alias' => $entity_alias, 'properties' => $properties]);
    }

    private function guessType($value): string
    {
        if (is_string($value)) {
            return 'string';
        }

        if (is_float($value)) {
            return 'float';
        }

        if (is_integer($value)) {
            return 'int';
        }

        return '';
    }

}
