<?php

namespace Dhru\Exceptions;

use Dhru\Lib\Output;

class ExceptionHandler extends \Exception
{

    public function __construct(string $message, $code = 0, array $data = [], array $info = [])
    {
        $code = $code ? $code : 0;
        $this->_data = $data;
        parent::__construct($message, $code);
        $output = new Output();
        $output->status = $code == 0 ? 'success' : 'error';
        $output->message = $message;
        $output->code = $code;
        $output->httpstatus = $code;
        $output->data = count($data) ? $data : null;
        $output->info = count($info) ? $info : null;
        $output->Done();
    }

}
