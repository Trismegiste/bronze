<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

use Symfony\Component\HttpFoundation\Response;
use Trismegiste\Bronze\App;
use Trismegiste\Bronze\AppTestCase;

class AppTest extends AppTestCase
{

    protected function createApp(): App
    {
        return new App();
    }

    public function getHttpMethod(): array
    {
        return [
            ['get'],
            ['post'],
            ['put'],
            ['patch'],
            ['delete'],
        ];
    }

    /** @dataProvider getHttpMethod */
    public function testPatch(string $method)
    {
        call_user_func([$this->sut, $method], '/yolo', function () {
            return new Response('YOLO');
        });

        $this->client->request($method, '/yolo');
        $this->assertStatusCodeEquals(200);
        $this->assertEquals('YOLO', $this->client->getResponse()->getContent());
    }

    public function testRedirect()
    {
        $this->sut->get('/yolo', function () {
            return $this->redirectTo('/elsewhere');
        });
        $this->client->followRedirects(false);
        $this->client->request('GET', '/yolo');
        $this->assertStatusCodeEquals(302);
        $this->assertEquals('/elsewhere', $this->client->getResponse()->getHeader('location'));
    }

}
