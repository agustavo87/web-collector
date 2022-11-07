<?php

namespace AGustavo87\WebCollector;

class Configuration
{
    protected array $namespaces;

    public function __construct(array $paths)
    {
        foreach ($paths as $path) {
            $this->register($path);
        }
    }

    public function register($path)
    {
        if (!file_exists($path)) {
            throw new \Exception('Configuration: Inexistent file or directory to start from.',1);
        }
        $configFiles = preg_grep("/.php$/", scandir($path));
        foreach ($configFiles as $configFile) {
            $this->namespaces[basename($configFile, '.php')] = require $path . DIRECTORY_SEPARATOR . $configFile;
        }
    }

    public function __invoke($configPath)
    {
        $keys = explode('.', $configPath);
        $namespace = array_shift($keys);
        if (!key_exists($namespace, $this->namespaces)) {
            throw new \Exception('Configuration: Inexistent namespace', 2);
        }
        $config = $this->namespaces[$namespace];
        foreach ($keys as $key) {
            if (!key_exists($key, $config)) {
                throw new \Exception('Configuration: Inexistent congiguration key', 3);
            }
            $config = $config[$key];
        }
        return $config;
    }
}