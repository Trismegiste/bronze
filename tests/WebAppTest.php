<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

class WebAppTest extends Tests\Trismegiste\Bronze\AppTestCase
{

    protected function createApp(): \Trismegiste\Bronze\App
    {
        return new \Trismegiste\Bronze\WebApp();
    }

    public function testTwigRender()
    {
        $this->sut->get('/', function () {
            return $this->render('base.html.twig');
        });
        $this->client->request('GET', '/');
        $this->assertStatusCodeEquals(200);
        $this->assertStringContainsString('<html', (string) $this->client->getResponse()->getContent());
    }

    public function testNotFoundWithTwig()
    {
        $this->client->request('GET', '/');
        $this->assertStatusCodeEquals(404);
        $this->assertStringContainsString('<html', (string) $this->client->getResponse()->getContent());
        $this->assertStringContainsString('Exception', (string) $this->client->getResponse()->getContent());
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
