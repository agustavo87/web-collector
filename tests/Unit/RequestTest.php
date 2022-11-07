<?php

namespace Tests\Unit;

use AGustavo87\WebCollector\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /** @test */
    public function it_is_createtable_by_array()
    {
        $request = Request::fromArray([
            'type' => 'GET',
            'path' => '/foo',
            'query' => [
                'bar' => 'baz'
            ]
            ]);

        $this->assertEquals($request->getPath(), '/foo');
        $this->assertEquals($request->getParam('bar'), 'baz');
        $this->assertEquals('GET', $request->getMethod());
    }
}