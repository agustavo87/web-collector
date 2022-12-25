<?php
namespace Tests\Unit;

use AGustavo87\WebCollector\{App, Request, Router};
use AGustavo87\WebCollector\Responses\Response;
use PHPUnit\Framework\{Assert, TestCase};

class RouterTest extends TestCase
{
    /** @test */
    public function it_calls_method_on_specified_parameters()
    {
        $app = $this->createStub(App::class);
        $router = new Router($app);
        $router->register(
            [
                'GET' => '/find',
                'use' => '\Tests\Unit\FindController',
                'call' => 'find'
            ]
        );

        $router->handle(Request::fromArray([
            'type' => 'GET',
            'path' => '/find',
            'query' => [
                'key' => 'value'
            ]
        ]));

        $this->assertEquals(1, FindController::$calls);
    }
}

class FindController
{
    public static $calls = 0;
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function find(): Response
    {
        self::$calls++;
        Assert::assertTrue($this->request->getParam('key') == 'value', 'the key value is not as expected.');
        return new Response();
    }
}