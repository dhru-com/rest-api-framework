<?php
namespace Dhru\Traits;

trait EndpointTrait
{
    public $para;
    public $token;
    public $querystring;
    public $schema;
    public $config;
    public $Base;


    function __construct($Base)
    {
        $this->para = $Base->parameters;
        $this->token = $Base->token;
        $this->querystring=$Base->querystring;
        $this->schema=$Base->schema;
        $this->config=$Base->config;
        $this->Base=$Base;

    }

}
