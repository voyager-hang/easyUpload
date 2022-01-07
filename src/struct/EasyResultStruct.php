<?php

namespace EasyUpload\struct;

use Exception;

class EasyResultStruct implements \ArrayAccess
{
    private bool $status = false;
    private $success;
    private $error;
    private array $resArray;

    public function __construct(bool $status, $success, $error)
    {
        $this->status = $status;
        $this->success = $success;
        $this->error = $error;
        $this->resArray = [
            'status' => $status,
            'success' => $success,
            'error' => $error
        ];
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getSuccess(): string
    {
        if (is_array($this->success)) {
            return $this->success[0] ?? '';
        }
        return $this->success;
    }

    /**
     * @param string $success
     */
    public function setSuccess(string $success): void
    {
        $this->success = $success;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        if (is_array($this->error)) {
            return $this->error[0] ?? '';
        }
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
     * @return array
     */
    public function getSuccessArr(): array
    {
        if (is_string($this->success)) {
            return [$this->success];
        }
        return $this->success;
    }

    /**
     * @param array $success
     */
    public function setSuccessArr(array $success): void
    {
        $this->success = $success;
    }

    /**
     * @return array
     */
    public function getErrorArr(): array
    {
        if (is_string($this->error)) {
            return [$this->error];
        }
        return $this->error;
    }

    /**
     * @param array $error
     */
    public function setErrorArr(array $error): void
    {
        $this->error = $error;
    }

    // 数组形式使用对象 兼容旧版使用方法
    public function offsetExists($offset): bool
    {
        return isset($this->resArray[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->resArray[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->resArray[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->resArray[$offset]);
    }
}