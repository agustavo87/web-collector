<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase; 
use AGustavo87\WebCollector\Configuration;

class ConfigTest extends TestCase
{

    /** @test */
    public function config_provider_returns_exception_on_inexistent_boot_path()
    {
        $this->expectExceptionCode(1);
        $configProvider = new Configuration([
            __DIR__ . DIRECTORY_SEPARATOR . 'mocks' . DIRECTORY_SEPARATOR . 'nonexistent'
        ]);
        
    }

    /** @test */
    public function config_provider_returns_nested_value_by_filename_and_dot_path_of_array()
    {
        $configProvider = new Configuration([
            __DIR__ . DIRECTORY_SEPARATOR . 'mocks' . DIRECTORY_SEPARATOR . 'config'
        ]);
        $this->assertEquals(
            'value',
            $configProvider('namespace.some.deep.path.to_a')
        );
    }

    /** @test */
    public function config_provider_gives_error_if_namespace_is_non_existent()
    {
        $this->expectExceptionCode(2);
        $configProvider = new Configuration([
            __DIR__ . DIRECTORY_SEPARATOR . 'mocks' . DIRECTORY_SEPARATOR . 'config'
        ]);
        $this->assertEquals(
            'value',
            $configProvider('nonexistentnamespace.some.deep.path.to_a')
        );
    }

    /** @test */
    public function config_provider_gives_error_if_some_path_key_is_non_existent()
    {
        $this->expectExceptionCode(3);
        $configProvider = new Configuration([
            __DIR__ . DIRECTORY_SEPARATOR . 'mocks' . DIRECTORY_SEPARATOR . 'config'
        ]);
        $this->assertEquals(
            'value',
            $configProvider('namespace.some.deep.nonexsitentkey.to_a')
        );
    }
}
