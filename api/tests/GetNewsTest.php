<?php

use PHPUnit\Framework\TestCase;

final class GetNewsTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'http://localhost'
        ]);
    }

    public function testToGetListOfNews(): void
    {
        $response = $this->client->get('/api/news');
        $data = json_decode($response->getBody(), true);

        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
        $this->assertGreaterThan(1, count($data));
    }

    public function testToCallInvalidUrl(): void
    {
        $response = $this->client->get('/api/news/15/author/stefan');
        $data = json_decode($response->getBody(), true);

        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Route not found', $data['error']);
    }
}