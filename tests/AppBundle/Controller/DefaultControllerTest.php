<?php

namespace tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase {

    public function testHomePageIsUp() {

        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

    }

    public function testBilleterieIsUp() {

        $client = static::createClient();

        $client->request('GET', '/billetterie');

        $this->assertSame(200, $client->getResponse()->getStatusCode());

    }

    public function testBilletterieFormIsDisplayed() {

        $client = self::createClient();

        $crawler = $client->request('GET', '/billetterie');

        $this->assertSame(1, $crawler->filter('form')->count());

    }

    public function testAddReservationRedirection() {

        $client = static::createClient();

        $crawler = $client ->request('GET', '/billetterie');

        $form = $crawler->filter('form[name=appbundle_reservation]')->form(array(

            'appbundle_reservation[date]' => '15-11-2017',
            'appbundle_reservation[horaire]' => '15:00:00',
            'appbundle_reservation[type_ticket]' => 'journée',
            'appbundle_reservation[nombre_ticket]' => '2'

        ));

        $client->submit($form);

        $client->followRedirects();

        $this->assertEquals('AppBundle\Controller\DefaultController::addReservationAction', $client->getRequest()->attributes->get('_controller'));

    }

}