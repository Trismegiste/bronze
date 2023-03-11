<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

use Tests\Trismegiste\Bronze\Core\AppTestCase;
use Trismegiste\Bronze\Core\App;
use Trismegiste\Bronze\Core\WebApp;

class WebAppTest extends AppTestCase
{

    protected function createApp(): App
    {
        return new WebApp();
    }

    public function testTwigRender()
    {
        $this->sut->get('/', function () {
            return $this->render('base.html.twig');
        });
        $this->client->request('GET', '/');
        $this->assertStatusCodeEquals(200);
        $this->assertResponseContainsString('<html');
    }

    public function testNotFoundWithTwig()
    {
        $this->client->request('GET', '/');
        $this->assertStatusCodeEquals(404);
        $this->assertResponseContainsString('<html');
        $this->assertResponseContainsString('Exception');
    }

    public function testCatchError()
    {
        $this->sut->get('/', function () {
            return new NotFoundClass();
        });
        ob_start();
        $this->sut->run();
        $response = ob_get_clean();
        $this->assertStringContainsString('<html', $response);
    }

}
