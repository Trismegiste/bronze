<?php

/*
 * Bronze - Make your Proof of Concept with Swag
 */

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Tests\Trismegiste\Bronze\AppTestCase;
use Trismegiste\Bronze\App;
use Trismegiste\Bronze\BusinessApp;

class BusinessAppTest extends AppTestCase
{

    protected function createApp(): App
    {
        return new BusinessApp('test');
    }

    public function testForm()
    {
        $this->sut->form('/entity', function () {
            $form = $this->createFormBuilder()
                    ->add('firstname', TextType::class)
                    ->add('save', SubmitType::class)
                    ->getForm();

            return $this->render('form.html.twig', ['form' => $form->createView()]);
        });

        $crawler = $this->client->request('GET', '/entity');
        $this->assertStatusCodeEquals(200);
        $this->assertResponseContainsString('<form');

        $form = $crawler->selectButton('form[save]')->form();
        $this->client->submit($form, ['form[firstname]' => 'Motoko']);
        $this->assertStatusCodeEquals(200);
    }

}
