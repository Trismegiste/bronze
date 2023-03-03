<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

use PHPUnit\Framework\TestCase;
use Trismegiste\Bronze\Browser;
use Trismegiste\Bronze\BusinessApp;

class BusinessAppTest extends TestCase
{

    protected $sut;
    protected $client;

    protected function setUp(): void
    {
        $this->sut = new BusinessApp();
        $this->client = new Browser();
        $this->client->setApp($this->sut);
    }

    public function testHumanCreate()
    {
        $this->sut->patch('/yolo', function () {
            return new Symfony\Component\HttpFoundation\Response('YOLO');
        });

        $this->client->request('PATCH', '/yolo');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('YOLO', $this->client->getResponse()->getContent());
    }

}
