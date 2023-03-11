<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Trismegiste\Bronze\Core\AppTestCase;
use Trismegiste\Bronze\Core\App;

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
    public function testRequest(string $method)
    {
        call_user_func([$this->sut, $method], '/yolo', function () {
            return new Response('YOLO');
        });

        $this->client->request($method, '/yolo');
        $this->assertStatusCodeEquals(200);
        $this->assertResponseContainsString('YOLO');
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

    public function testRunOk()
    {
        $this->sut->get('/', function () {
            return new Response('RUN OK');
        });
        ob_start();
        $this->sut->run();
        $response = ob_get_clean();
        $this->assertEquals('RUN OK', $response);
    }

    public function testNotFound()
    {
        $this->expectException(NotFoundHttpException::class);
        $this->sut->run();
    }

    public function testCatchError()
    {
        $this->expectException(Error::class);
        $this->sut->get('/', function () {
            return new NotFoundClass();
        });
        $this->sut->run();
    }

}
