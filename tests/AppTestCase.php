<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

namespace Tests\Trismegiste\Bronze;

use PHPUnit\Framework\TestCase;
use Trismegiste\Bronze\App;

/**
 * Template Method for testing an app
 */
abstract class AppTestCase extends TestCase
{

    protected App $sut;
    protected Browser $client;

    protected function setUp(): void
    {
        $this->sut = $this->createApp();
        $this->client = new Browser();
        $this->client->setApp($this->sut);
    }

    abstract protected function createApp(): App;

    protected function assertStatusCodeEquals(int $code, string $message = '')
    {
        $this->assertEquals($code, $this->client->getResponse()->getStatusCode(), $message);
    }

    protected function assertResponseContainsString(string $str, string $message = '')
    {
        $this->assertStringContainsString($str, (string) $this->client->getResponse()->getContent(), $message);
    }

}
