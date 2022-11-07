<?php

namespace Tests\Integration;
// use Giberti\PHPUnitLocalServer\LocalServerTestCase;

class RequestTest extends LocalServerTestCase
{
    protected static $bootServer = false;

    /** @test */
    public function it_captures_path()
    {
        $this->injectInLocalServer(<<<'END'
            use AGustavo87\WebCollector\Request;
            $request = new Request();
            $request->capture();
            echo $request->getPath();
            END
        );

        $content = $this->get('/foo?take=8');

        $this->assertEquals('/foo', $content, 'Incorrect path returned');
    }

    /** @test */
    public function it_captures_query_parameters()
    {
        $this->injectInLocalServer(<<<'END'
            use AGustavo87\WebCollector\Request;
            $request = new Request();
            $request->capture();
            echo $request->getParam('take');
            END
        );

        $content = $this->get('/foo?take=8');

        $this->assertEquals('8', $content, 'Incorrect path returned');
    }

    /** @test */
    public function it_captures_headers()
    {
        $this->injectInLocalServer(<<<'END'
            use AGustavo87\WebCollector\Request;
            $request = new Request();
            $request->capture();
            echo $request->getHeaders()['Host'];
            END
        );

        $content = $this->get('/');

        $this->assertStringContainsString($content, $this->root);
    }
}