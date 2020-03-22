<?php

use PHPUnit\Framework\TestCase;

final class SearchNewsTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'http://localhost'
        ]);
    }

    public function testSearchNewsById(): void
    {
        $newsId = 15;
        $response = $this->client->get('/api/news/' . $newsId);
        $data = json_decode($response->getBody(), true);

        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
        $this->assertEquals($newsId, $data[0]['id']);
    }

    public function testSearchNewsForValidKeys(): void
    {
        $newsId = 15;
        $response = $this->client->get('/api/news/' . $newsId);
        $data = json_decode($response->getBody(), true);

        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('title', $data[0]);
        $this->assertArrayHasKey('date', $data[0]);
        $this->assertArrayHasKey('text', $data[0]);
    }

    public function testSearchNewsForNotEmptyValues(): void
    {
        $newsId = 15;
        $response = $this->client->get('/api/news/' . $newsId);
        $data = json_decode($response->getBody(), true);

        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
        $this->assertNotEmpty($data[0]['id']);
        $this->assertNotEmpty($data[0]['title']);
        $this->assertNotEmpty($data[0]['date']);
        $this->assertNotEmpty($data[0]['text']);
    }

    public function testToGetNotExistingNews(): void
    {
        $response = $this->client->get('/api/news/2');
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultSearchAsserts($data, 'No results found', 'message');
    }

    public function testToGetNewsByInvalidId(): void
    {
        $response = $this->client->get('/api/news/test');
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultSearchAsserts($data, 'Route not found', 'error');
    }

    public function testSearchNewsByZeroId(): void
    {
        $response = $this->client->get('/api/news/0');
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultSearchAsserts($data, 'No results found', 'message');
    }

    public function testSearchNewsInvalidUrl(): void
    {
        $response = $this->client->get('/api/news/15/author/stefan');
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultSearchAsserts($data, 'Route not found', 'error');
    }

    private function loadDefaultSearchAsserts($data, $expected, $key)
    {
        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
        $this->assertArrayHasKey($key, $data);
        $this->assertEquals($expected, $data[$key]);
    }
}