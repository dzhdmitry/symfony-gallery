<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('#viewport')->count());
    }

    public function testAlbums()
    {
        $client = static::createClient();
        $client->request('GET', '/', [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        $albums = json_decode($client->getResponse()->getContent());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertInternalType('array', $albums);
        $this->assertCount(5, $albums);

        foreach ($albums as $i => $album) {
            $expectedCount = ($i == 0) ? 5 : 10;

            $this->assertCount($expectedCount, $album->images);
        }
    }

    public function testAlbum()
    {
        $client = static::createClient();
        $client->request('GET', '/album/2', [], [], [
            'HTTP_X-Requested-With' => 'XMLHttpRequest'
        ]);

        $images = json_decode($client->getResponse()->getContent());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertInternalType('array', $images->data);
        $this->assertCount(10, $images->data);
    }

    public function testAlbumPage()
    {
        $client = static::createClient();

        for ($i=2; $i<6; $i++) {
            $client->request('GET', '/album/' . $i . '/page/2', [], [], [
                'HTTP_X-Requested-With' => 'XMLHttpRequest'
            ]);

            $images = json_decode($client->getResponse()->getContent());

            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $this->assertInternalType('array', $images->data);
            $this->assertCount(10, $images->data);
        }
    }
}
