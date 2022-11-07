<?php

namespace Tests\Integration;

use Behat\Mink\Session as MinkSession;
use Behat\Mink\Driver\GoutteDriver;

class SessionTest extends LocalServerTestCase
{
    protected static $bootServer = false;
    protected MinkSession $browser;

    public function setUp(): void
    {
        parent::setUp();

        $this->browser = new MinkSession(new GoutteDriver());

        $this->browser->start();
    }

    /** @test */
    public function session_storage_works()
    {
        $sessionPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'session';
        $this->injectInLocalServer(<<<'END'
            use AGustavo87\WebCollector\Session;
            $session = new Session(dirname(__FILE__, 2) . '/mocksession');
            echo $session->get('p');
            $session->set('p', 'my session works');
        END);

        $this->browser->visit($this->root . '/');
        $content = $this->browser->getPage()->getContent();
        $this->assertStringNotContainsString('my session works', $content);

        $this->browser->visit($this->root . '/');
        $content = $this->browser->getPage()->getContent();
        $this->assertStringContainsString('my session works', $content);
    }
}