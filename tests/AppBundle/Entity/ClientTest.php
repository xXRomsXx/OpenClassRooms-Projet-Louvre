<?php

namespace tests\AppBundle\Entity;

use AppBundle\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientTest extends WebTestCase {

    public function testClientEntityBoot() {

        $client = new Client();
        $client->setNom('Gravier');
        $client->setPrenom('Romain');
        $client->setTarifReduit(0);

        $this->assertEquals('Gravier', $client->getNom());
        $this->assertEquals('Romain', $client->getPrenom());
        $this->assertEquals(0, $client->getTarifReduit());

    }

}