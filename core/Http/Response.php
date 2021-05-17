<?php

class Response
{
    public $status;
    public $message;
    public $data;

    public function Error(string $message)
    {
        $this->status = false;
        $this->message = $message;
        return $this;
    }

    public function Success(string $message, $data)
    {
        $this->status = true;
        $this->message = $message;
        $this->data = $data;
        return $this;
    }
    // private $status_code = 200;

    // public function status(int $code)
    // {
    //     $this->status_code = $code;
    //     return $this;
    // }

    // public function getBody($data = [])
    // {
    //     return json_encode($data);
    // }
    
    // public function getJSON($data = [])
    // {
    //     http_response_code($this->status);
    //     header('Content-Type: application/json');
    //     echo json_encode($data);
    // }
}
