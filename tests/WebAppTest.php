<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

class WebAppTest extends PHPUnit\Framework\TestCase
{

    protected $sut;

    protected function setUp(): void
    {
        $this->sut = new \Trismegiste\Bronze\WebApp();
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
            return $this->render('blog.html.twig', ['name' => 'Yolo']);
        });

        $this->assertStringContainsString('Welcome to the page Yolo', $this->getResponse());
    }

}
