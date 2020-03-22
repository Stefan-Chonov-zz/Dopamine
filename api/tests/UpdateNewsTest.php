<?php

use PHPUnit\Framework\TestCase;

final class UpdateNewsTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'http://localhost'
        ]);
    }

    public function testTryToUpdateNewsOnBulk(): void
    {
        $currentDateTime = date("Y-m-d H:i:s", time());
        $response = $this->client->post('/api/news/5', [
            'json' => [
                [
                    'title' => "Test Random Title 5 $currentDateTime"
                ],
                [
                    'title' => "Test Random Title 6 $currentDateTime",
                    'date' => str_replace('2020', '2022', $currentDateTime),
                    'text' => "Test Random Text 6 $currentDateTime"
                ]
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("Bulk Update is NOT supported", $data['message']);
    }

    public function testTryToUpdateNewsWithExistingTitle(): void
    {
        $response = $this->client->post('/api/news/5', [
            'json' => [
                'title' => 'My Random Title'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultUpdateAsserts($data, 'Title already exists', 'title', 'message');
    }

    public function testTryToUpdateNewsWithEmptyTitle(): void
    {
        $response = $this->client->post('/api/news/5', [
            'json' => [
                'title' => ''
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultUpdateAsserts($data, 'Field cannot be empty', 'title', 'message');
    }

    public function testTryToUpdateNewsWithEmptyDate(): void
    {
        $response = $this->client->post('/api/news/5', [
            'json' => [
                'date' => ''
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultUpdateAsserts($data, 'Field cannot be empty', 'date', 'message');
    }

    public function testTryToUpdateNewsWithEmptyText(): void
    {
        $response = $this->client->post('/api/news/5', [
            'json' => [
                'text' => ''
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultUpdateAsserts($data, 'Field cannot be empty', 'text', 'message');
    }

    public function testTryToInsertNewsWithTooLongTitle(): void
    {
        $longTitle = str_repeat("title", 200); // 1000 characters
        $response = $this->client->post('/api/news/5', [
            'json' => [
                'title' => $longTitle
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultUpdateAsserts($data, 'Title is too long. Max allowed length is 255 characters', 'title', 'message');
    }

    public function testTryToUpdateNewsWithInvalidDateFormat(): void
    {
        $response = $this->client->post('/api/news/5', [
            'json' => [
                'date' => '2019-02-28 14:56:24 invalid'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultUpdateAsserts($data, 'Date format is not valid', 'date', 'message');
    }

    public function testTryToInsertNewsWithTooLongText(): void
    {
        $longText = str_repeat("text", 625); // 2500 characters
        $response = $this->client->post('/api/news/5', [
            'json' => [
                'text' => $longText
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultUpdateAsserts($data, 'Text is too long. Max allowed length is 2000 characters', 'text', 'message');
    }

    public function testTryToUpdateNewsWithInvalidData(): void
    {
        $response = $this->client->post('/api/news/5', [
            'json' => [
                'title' => 'My Random Title 2',
                'invalid' => 'data'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultUpdateAsserts($data, "Invalid parameter 'invalid'", 'invalid', 'message');
    }

    public function testTryToUpdateNewsMultiFields(): void
    {
        $currentDateTime = date("Y-m-d H:i:s", time());
        $randomTitle = "Test Random Title 99 $currentDateTime";
        $randomText = "Test Random Text 99 $currentDateTime";
        $response = $this->client->post('/api/news/5', [
            'json' => [
                'title' => $randomTitle,
                'date' => $currentDateTime,
                'text' => $randomText
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("Record was updated successfully", $data['message']);
    }

    public function testTryToUpdateNewsMultiFieldsInvalidData(): void
    {
        $currentDateTime = date("Y-m-d H:i:s", time());
        $randomTitle = "Test Random Title 3 $currentDateTime";
        $response = $this->client->post('/api/news/5', [
            'json' => [
                'title' => $randomTitle,
                'date' => $currentDateTime,
                'invalid' => 'data'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultUpdateAsserts($data, "Invalid parameter 'invalid'", 'invalid', 'message');
    }

    public function testTryToUpdateNotExistingNews(): void
    {
        $response = $this->client->post('/api/news/2', [
            'json' => [
                'text' => 'My Random Text'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("Nothing to update", $data['message']);
    }

    private function loadDefaultUpdateAsserts($data, $expected, $key1, $key2)
    {
        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
        $this->assertArrayHasKey($key1, $data);
        $this->assertArrayHasKey($key2, $data[$key1]);
        $this->assertEquals($expected, $data[$key1][$key2]);
    }
}