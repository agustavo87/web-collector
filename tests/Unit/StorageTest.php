<?php

namespace Tests\Unit;

use AGustavo87\WebCollector\Services\Storage;
use PHPUnit\Framework\TestCase;

class StorageTest extends TestCase
{
    protected $mockStorageRoot;

    protected function setUp(): void
    {
        $this->mockStorageRoot =  __DIR__ . DIRECTORY_SEPARATOR . 'mocks/storage';
    }

    protected function tearDown(): void
    {
        $this->cleanMockDirectory([
            $this->mockStorageRoot,
            $this->mockStorageRoot . '/pages/',
        ]);
    }

    protected function cleanMockDirectory(array $directories)
    {
        foreach ($directories as $path) {
            $files = array_filter(scandir($path), function($value) {
                return preg_match('/^[\w,\s-]+\.[A-Za-z]{3}$/', $value);
            });
            foreach ($files as $file) {
                unlink($path . DIRECTORY_SEPARATOR . $file);
            }
        }
    }
    
    /** @test */
    public function it_returns_default_root_path()
    {
        $storage = new Storage();
        $this->assertStringContainsString('storage', $storage->path('/'));
    }

    /** @test */
    public function the_root_storage_is_customizable()
    {
        $mockStorage = __DIR__ . DIRECTORY_SEPARATOR . 'storage';
        $storage = new Storage('default', $mockStorage);
        $this->assertStringContainsString($mockStorage, $storage->path('/'));
    }

    protected function getMockStorage($storage = 'default')
    {
        return new Storage($storage, $this->mockStorageRoot);
    }

    /** @test */
    public function stores_data_in_storage()
    {
        $storage = $this->getMockStorage();
        $storage->put('file.txt', 'some data');
        $data = file_get_contents($this->mockStorageRoot .DIRECTORY_SEPARATOR .'file.txt');
        $this->assertEquals('some data', $data);
    }

    /** @test */
    public function retrieves_data_in_storage()
    {
        $storage = $this->getMockStorage();
        file_put_contents($this->mockStorageRoot .DIRECTORY_SEPARATOR . 'putfile.txt', 'some data');
        $data = $storage->get('putfile.txt');
        $this->assertEquals('some data', $data);
    }

    /** @test */
    public function stores_at_a_disk()
    {
        Storage::createDisk([
            'name'  => 'pages',
            'root'  => 'pages'
        ]);

        $storage = $this->getMockStorage('pages');
        $storage->put('file.txt', 'some data');
        $data = file_get_contents(
            $this->mockStorageRoot .DIRECTORY_SEPARATOR 
            .'pages'.DIRECTORY_SEPARATOR
            .'file.txt'
        );
        
        $this->assertEquals('some data', $data);
    }
    
    /** @test */
    public function retrieves_at_a_disk()
    {
        Storage::createDisk([
            'name'  => 'pages',
            'root'  => 'pages'
        ]);

        file_put_contents(
            $this->mockStorageRoot .DIRECTORY_SEPARATOR 
            .'pages'.DIRECTORY_SEPARATOR
            .'fileput.txt',
            'some data'
        );

        $storage = $this->getMockStorage('pages');
        $data = $storage->get('fileput.txt');

        $this->assertEquals('some data', $data);
    }
}