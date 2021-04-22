<?php

namespace Core\FileStorage\Exceptions;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class NotAllowUploadMimeTypeException extends UnprocessableEntityHttpException
{
    /**
     * Create a not allow upload mime type exception.
     */
    public function __construct()
    {
        parent::__construct('该文件类型不允许上传');
    }
}
