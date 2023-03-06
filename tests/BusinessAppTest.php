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
        return new BusinessApp();
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

        $this->client->request('GET', '/entity');
        $this->assertStatusCodeEquals(200, $this->client->getResponse()->getContent());
        $this->assertResponseContainsString('<form');
    }

}
