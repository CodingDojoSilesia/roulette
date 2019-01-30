<?php
namespace errors;

/**
 * Describes a standard HTTP exception
 */
class HttpException extends \Exception
{
    public $statusCode;
    public $data;

    public function __construct($status, $message = null, $data = null, $code = 0, \Exception $previous = null)
    {
        $this->statusCode = $status;
        $this->data = $data;
        parent::__construct($message, $code, $previous);
    }
}
