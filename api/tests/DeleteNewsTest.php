<?php

use PHPUnit\Framework\TestCase;

final class DeleteNewsTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'http://localhost'
        ]);
    }

//    public function testDeleteNewsById(): void
//    {
//        $newsId = 48;
//        $response = $this->client->delete('/api/news/' . $newsId);
//        $data = json_decode($response->getBody(), true);
//
//        $this->loadDefaultDeleteAsserts($data, 'Record was deleted successfully', 'message');
//    }

    public function testDeleteNewsNotExists(): void
    {
        $newsId = 1;
        $response = $this->client->delete('/api/news/' . $newsId);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultDeleteAsserts($data, 'Record not exists', 'message');
    }

    public function testDeleteNewsByZeroId(): void
    {
        $response = $this->client->delete('/api/news/0');
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultDeleteAsserts($data, 'Record not exists', 'message');
    }

    public function testDeleteNewsInvalidId(): void
    {
        $response = $this->client->delete('/api/news/test');
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultDeleteAsserts($data, 'Route not found', 'error');
    }

    private function loadDefaultDeleteAsserts($data, $expected, $key)
    {
        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
        $this->assertArrayHasKey($key, $data);
        $this->assertEquals($expected, $data[$key]);
    }
}