<?php

namespace AGustavo87\WebCollector\Services;

class Storage
{
    /**
     * Resource open
     *
     * @var resource
     */
    protected $resource;
    protected $root;
    protected static $disks = [
        'default' => [
            'root' => ''
        ]
    ];
    protected $disk;

    public function __construct(string $disk = 'default', ?string $root = null)
    {
        $this->disk = $disk;
        $this->root = $root ?? dirname(__FILE__,3) . DIRECTORY_SEPARATOR . 'storage';
        if(!is_dir($this->path(''))) {
            mkdir($this->path(''));
        }
    }

    public static function createDisk(array $data)
    {
        self::$disks[$data['name']] = $data;
    }

    public function path($path)
    {
        $root = self::$disks[$this->disk]['root'];
        $root = strlen($root) ? $this->normalize($root) . DIRECTORY_SEPARATOR : '';
        return $this->root . DIRECTORY_SEPARATOR . $root . $this->normalize($path);
    }

    public function normalize($path)
    {
        return preg_replace('/[\\\\\/]/', DIRECTORY_SEPARATOR,  $path);
    }

    public function put($path, $data)
    {
        file_put_contents($this->path($path), $data);
    }

    public function get($path)
    {
        return file_get_contents($this->path($path));
    }

    public function fopen($path, $flags, $use_incluse_path = false, $context = null): self
    {
        $this->resource = fopen(
            $this->path($path),
            $flags,
            $use_incluse_path,
            $context
        );
        return $this;
    }

    public function fbom(): self
    {
        fputs($this->resource, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        return $this;
    }

    public function fclose()
    {
        fclose($this->resource);
    }

    public function putcsv(array $fields)
    {
        fputcsv($this->resource, $fields);
    }

    public function storeCSV($path, $data, $columns)
    {
        $this->fopen($path, 'w')->fbom();
        $this->putcsv($columns);
        foreach($data as $unit) {
            $row = array_map(fn($column) => $unit[$column], $columns);
            $this->putcsv($row);
        }
        $this->fclose();
    }
}