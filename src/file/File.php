<?php

namespace EasyUpload\file;

class File
{
    private string $name;
    private string $type;
    private string $tmpName;
    private string $error;
    private float $width = 0;
    private float $height = 0;
    private float $size;
    private float $sizeKb;
    private float $sizeMb;
    private string $saveName;
    private string $resultPath;
    private string $ext;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getTmpName(): string
    {
        return $this->tmpName;
    }

    /**
     * @param string $tmpName
     */
    public function setTmpName(string $tmpName): void
    {
        $this->tmpName = $tmpName;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param string $error
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * @return float|int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param float|int $width
     */
    public function setWidth($width): void
    {
        $this->width = $width;
    }

    /**
     * @return float|int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param float|int $height
     */
    public function setHeight($height): void
    {
        $this->height = $height;
    }

    /**
     * @return float
     */
    public function getSize(): float
    {
        return $this->size;
    }

    /**
     * @param float $size
     */
    public function setSize(float $size): void
    {
        $this->size = $size;
    }

    /**
     * @return float
     */
    public function getSizeKb(): float
    {
        return $this->sizeKb;
    }

    /**
     * @param float $sizeKb
     */
    public function setSizeKb(float $sizeKb): void
    {
        $this->sizeKb = $sizeKb;
    }

    /**
     * @return float
     */
    public function getSizeMb(): float
    {
        return $this->sizeMb;
    }

    /**
     * @param float $sizeMb
     */
    public function setSizeMb(float $sizeMb): void
    {
        $this->sizeMb = $sizeMb;
    }

    /**
     * @return string
     */
    public function getSaveName(): string
    {
        return $this->saveName;
    }

    /**
     * @param string $saveName
     */
    public function setSaveName(string $saveName): void
    {
        $this->saveName = $saveName;
    }

    /**
     * @return string
     */
    public function getResultPath(): string
    {
        return $this->resultPath;
    }

    /**
     * @param string $resultPath
     */
    public function setResultPath(string $resultPath): void
    {
        $this->resultPath = $resultPath;
    }

    /**
     * @return string
     */
    public function getExt(): string
    {
        return $this->ext;
    }

    /**
     * @param string $ext
     */
    public function setExt(string $ext): void
    {
        $this->ext = $ext;
    }


}