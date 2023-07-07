<?php

namespace Trismegiste\Bronze\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

/**
 * Scrape
 */
#[AsCommand('scrape')]
class Scrape extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = HttpClient::create();
        $response = $client->request(
                'GET',
                'https://app-backend.electricitymap.org/v6/details/hourly/FR',
                [
                    'headers' => [
                        "User-Agent" => "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/115.0",
                        "Accept" => "*/*",
                        "Accept-Language" => "fr,fr-FR;q=0.8,en-US;q=0.5,en;q=0.3",
                        "electricitymap-token" => "kUp26@Zg4fv$9Pm",
                        "x-request-timestamp" => "1688713817661",
                        "x-signature" => "e2e3b0d4becf6df4011f9ea0c0d4e027d93c531ea04a1a482d21609785f21081",
                        "Sec-Fetch-Dest" => "empty",
                        "Sec-Fetch-Mode" => "cors",
                        "Sec-Fetch-Site" => "cross-site",
                        "Pragma" => "no-cache",
                        "Cache-Control" => "no-cache",
                        "Referer" => "https://app.electricitymaps.com/",
                        'Origin' => 'https://app.electricitymaps.com',
                        'TE' => 'trailers'
                    ]
                ]
        );

        $stats = json_decode($response->getContent());
        $prodPerHour = (array) $stats->data->zoneStates;
        ksort($prodPerHour);
        $ts = array_key_last($prodPerHour);

        $output->writeln($prodPerHour[$ts]->co2intensityProduction);

        return self::SUCCESS;
    }

}
