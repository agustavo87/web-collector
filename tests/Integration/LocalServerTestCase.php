<?php

namespace Tests\Integration;

use Giberti\PHPUnitLocalServer\LocalServerTestCase as GibertiTestCase;

class LocalServerTestCase extends GibertiTestCase
{
    protected $root = null;
    protected static $bootServer = true;

    public static function setupBeforeClass():void {
        if (self::$bootServer) {
            static::createServerWithRouter(__DIR__ . '/../../router.php');
        }
    }

    protected function setUp():void
    {
        parent::setUp();
        if (self::$bootServer) {
            $this->root = $this->getLocalServerUrl();
        }
    }

    protected function injectInLocalServer(string $content)
    {
        copy(__DIR__.'/localhost/index.php.stub', __DIR__.'/localhost/index.php');
        file_put_contents(__DIR__.'/localhost/index.php',$content , FILE_APPEND);
        static::createServerWithDocroot(__DIR__.'/localhost');
        $this->root = $this->getLocalServerUrl();
    }

    protected function get(string $uri)
    {
        $uri = $this->root . $uri;
        return file_get_contents($uri);
    }
}