<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Input;
use Illuminate\Support\Facades\Cache;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testAccount()
    {
        $data = [
            'body' => '{ "account": { "activeCard": true, "availableLimit": 100 } }',
        ];
        $this->post(route('posts.store'), $data)
            ->assertStatus(200);
    }

    /**
     * A basic functional test example.
     *
     * @return void

    public function testTransaction()
    {
        $account = json_decode('{ "account": { "activeCard": true, "availableLimit": 100 } }');
        Cache::put('account', $account,  now()->addMinutes(10));

        $data = [
            'body' => '{ "transaction": { "merchant": "Habbib\'s", "amount": 90, "time": "2019-11-05T11:00:00.000Z" } }',
        ];
        $this->post(route('posts.transaction'), $data)
            ->assertStatus(200);
    }
     */
}
