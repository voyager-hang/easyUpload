<?php

namespace EasyUpload\struct;

use EasyUpload\file\File;

class FileArrStruct implements \Iterator
{
    private int $position = 0;
    private array $array = [];

    public function __construct($array = [])
    {
        $this->position = 0;
        $this->array = $array;
    }

    /**
     * @return mixed
     */
    public function current(): File
    {
        return $this->array[$this->position];
    }

    /**
     *
     */
    public function next()
    {
        ++$this->position;
    }

    /**
     * @return float|int|null
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->array[$this->position]);
    }

    /**
     *
     */
    public function rewind()
    {
        $this->position = 0;
    }
}