<?php

use PHPUnit\Framework\TestCase;

final class CreateNewsTest extends TestCase
{
    protected $client;

    protected function setUp(): void
    {
        $this->client = new GuzzleHttp\Client([
            'base_uri' => 'http://localhost'
        ]);
    }

    public function testTryToCreateNews(): void
    {
        $currentDateTime = date("Y-m-d H:i:s", time());
        $randomTitle = "Test Random Title $currentDateTime";
        $randomText = "Test Random Text $currentDateTime";
        $response = $this->client->post('/api/news', [
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
        $this->assertCount(1, $data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertEquals($randomTitle, $data[0]['title']);
        $this->assertEquals($currentDateTime, $data[0]['date']);
        $this->assertEquals($randomText, $data[0]['text']);
    }

    public function testTryToCreateNewsOnBulk(): void
    {
        $currentDateTime = date("Y-m-d H:i:s", time());
        $response = $this->client->post('/api/news', [
            'json' => [
                [
                    'title' => "Test Random Title 5 $currentDateTime",
                    'date' => str_replace('2020', '2021', $currentDateTime),
                    'text' => "Test Random Text 5 $currentDateTime"
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
        $this->assertArrayHasKey('message', $data);
        $this->assertEquals("Bulk Insert is NOT supported", $data['message']);
    }

    public function testTryToCreateNewsWithExistingTitle(): void
    {
        $response = $this->client->post('/api/news', [
            'json' => [
                'title' => 'My Random Title',
                'date' => '2019-02-28 14:56:24',
                'text' => 'My Random Text 2'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultCreateAsserts($data, 'Title already exists', 'title', 'message');
    }

    public function testTryToCreateNewsWithMissingRequiredTitle(): void
    {
        $response = $this->client->post('/api/news', [
            'json' => [
                'date' => '2019-02-28 14:56:24',
                'text' => 'My Random Text 2'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultCreateAsserts($data, "Parameter 'title' is required", 'title', 'message');
    }

    public function testTryToCreateNewsWithMissingRequiredDate(): void
    {
        $response = $this->client->post('/api/news', [
            'json' => [
                'title' => 'My Random Title 2',
                'text' => 'My Random Text 2'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultCreateAsserts($data, "Parameter 'date' is required", 'date', 'message');
    }

    public function testTryToCreateNewsWithMissingRequiredText(): void
    {
        $response = $this->client->post('/api/news', [
            'json' => [
                'title' => 'My Random Title 2',
                'date' => '2019-02-28 14:56:24'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultCreateAsserts($data, "Parameter 'text' is required", 'text', 'message');
    }

    public function testTryToCreateNewsWithEmptyTitle(): void
    {
        $response = $this->client->post('/api/news', [
            'json' => [
                'title' => '',
                'date' => '2019-02-28 14:56:24',
                'text' => 'My Random Text 2'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultCreateAsserts($data, "Field cannot be empty", 'title', 'message');
    }

    public function testTryToCreateNewsWithEmptyDate(): void
    {
        $response = $this->client->post('/api/news', [
            'json' => [
                'title' => 'My Random Title 2',
                'date' => '',
                'text' => 'My Random Text 2'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultCreateAsserts($data, "Field cannot be empty", 'date', 'message');
    }

    public function testTryToCreateNewsWithEmptyText(): void
    {
        $response = $this->client->post('/api/news', [
            'json' => [
                'title' => 'My Random Title 2',
                'date' => '2019-02-28 14:56:24',
                'text' => ''
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultCreateAsserts($data, "Field cannot be empty", 'text', 'message');
    }

    public function testTryToCreateNewsWithTooLongTitle(): void
    {
        $longTitle = str_repeat("title", 200); // 1000 characters
        $response = $this->client->post('/api/news', [
            'json' => [
                'title' => $longTitle,
                'date' => '2019-02-28 14:56:24',
                'text' => 'My Random Text 2'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultCreateAsserts($data, "Title is too long. Max allowed length is 255 characters", 'title', 'message');
    }

    public function testTryToCreateNewsWithInvalidDateFormat(): void
    {
        $response = $this->client->post('/api/news', [
            'json' => [
                'title' => 'My Random Title 2',
                'date' => '2019-02-28 14:56:24 invalid',
                'text' => 'My Random Text 2'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultCreateAsserts($data, "Date format is not valid", 'date', 'message');
    }

    public function testTryToCreateNewsWithTooLongText(): void
    {
        $longText = str_repeat("text", 625); // 2500 characters
        $response = $this->client->post('/api/news', [
            'json' => [
                'title' => 'My Random Title 2',
                'date' => '2019-02-28 14:56:24',
                'text' => $longText
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultCreateAsserts($data, "Text is too long. Max allowed length is 2000 characters", 'text', 'message');
    }

    public function testTryToCreateNewsWithInvalidData(): void
    {
        $response = $this->client->post('/api/news', [
            'json' => [
                'title' => 'My Random Title 2',
                'date' => '2019-02-28 14:56:24',
                'text' => 'My Random Text 2',
                'invalid' => 'data'
            ]
        ]);
        $data = json_decode($response->getBody(), true);

        $this->loadDefaultCreateAsserts($data, "Invalid parameter 'invalid'", 'invalid', 'message');
    }

    public function testTryToCreateNewsMaliciously(): void
    {
        $currentDateTime = date("Y-m-d H:i:s", time());
        $randomTitle = "<div> Test Random Title $currentDateTime </div>";
        $randomText = "<script>alert('Test Random Text');</script> $currentDateTime";
        $response = $this->client->post('/api/news', [
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
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertEquals(htmlspecialchars($randomTitle), $data[0]['title']);
        $this->assertEquals($currentDateTime, $data[0]['date']);
        $this->assertEquals(htmlspecialchars($randomText), $data[0]['text']);
    }

    public function testTryToCreateNewsEmptySpaces(): void
    {
        $currentDateTime = date("Y-m-d H:i:s", time());
        $randomTitle = "       Test Random Title $currentDateTime    ";
        $randomText = "     Test Random Text $currentDateTime";
        $response = $this->client->post('/api/news', [
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
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertEquals(trim($randomTitle), $data[0]['title']);
        $this->assertEquals($currentDateTime, $data[0]['date']);
        $this->assertEquals(trim($randomText), $data[0]['text']);
    }

    private function loadDefaultCreateAsserts($data, $expected, $key1, $key2)
    {
        $this->assertNotNull($data);
        $this->assertNotEmpty($data);
        $this->assertIsArray($data);
        $this->assertArrayHasKey($key1, $data);
        $this->assertArrayHasKey($key2, $data[$key1]);
        $this->assertEquals($expected, $data[$key1][$key2]);
    }
}