<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

class AppTest extends PHPUnit\Framework\TestCase
{

    protected $sut;

    protected function setUp(): void
    {
        $this->sut = new \Trismegiste\Bronze\App();
    }

    protected function getResponse(): string
    {
        ob_start();
        $this->sut->run();
        return ob_get_clean();
    }

    public function testRoute()
    {
        $this->sut->get('/', function () {
            return new Symfony\Component\HttpFoundation\Response('YOLO');
        });

        $this->assertEquals('YOLO', $this->getResponse());
    }

}
