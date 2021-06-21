<?php

namespace EasyUpload\file;

class File
{
    public $name;
    public $type;
    public $tmpName;
    public $error;
    public $size;
    public $sizeKb;
    public $sizeMb;
    public $saveName;
    public $resultPath;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getTmpName()
    {
        return $this->tmpName;
    }

    /**
     * @param mixed $tmpName
     */
    public function setTmpName($tmpName)
    {
        $this->tmpName = $tmpName;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getSizeKb()
    {
        return $this->sizeKb;
    }

    /**
     * @param mixed $sizeKb
     */
    public function setSizeKb($sizeKb)
    {
        $this->sizeKb = $sizeKb;
    }

    /**
     * @return mixed
     */
    public function getSizeMb()
    {
        return $this->sizeMb;
    }

    /**
     * @param mixed $sizeMb
     */
    public function setSizeMb($sizeMb)
    {
        $this->sizeMb = $sizeMb;
    }

    /**
     * @return mixed
     */
    public function getSaveName()
    {
        return $this->saveName;
    }

    /**
     * @param mixed $saveName
     */
    public function setSaveName($saveName)
    {
        $this->saveName = $saveName;
    }

    /**
     * @return mixed
     */
    public function getResultPath()
    {
        return $this->resultPath;
    }

    /**
     * @param mixed $resultPath
     */
    public function setResultPath($resultPath)
    {
        $this->resultPath = $resultPath;
    }

}