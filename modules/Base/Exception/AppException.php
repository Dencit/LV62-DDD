<?php

namespace Modules\Base\Exception;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * notes: APP异常-基类
 * @author 陈鸿扬 | @date 2021/2/3 10:55
 */
class AppException extends \RuntimeException implements HttpExceptionInterface
{
    private $statusCode;
    private $headers;

    public function __construct( $statusCode,  $message = null, \Throwable $previous = null, array $headers = [], $code = 0)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set response headers.
     * @param array $headers Response headers
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
    }
}
