<?php

import(MODEL.'Model');

class Transaction extends Model
{
    public $id;
    public $name;
    public $method;
    public $type;
    public $amount;
    public $sender;
    public $recipient;
    public $reference;
    public $trackid;
    public $body;
    public $author;
    public $status;
    public $date;

    public function __construct()
    {
        $this->model = $this;
        $this->table = "transactions";
    }

    public function save()
    {   
        $save = $this->insert([
            'name' => $this->name,
            'method' => $this->method,
            'type' => $this->type,
            'amount' => $this->amount,
            'sender' => $this->sender,
            'recipient' => $this->recipient,
            'reference' => $this->reference,
            'trackid' => $this->trackid,
            'body' => $this->body,
            'author' => $this->author,
            'status' => $this->status
        ]);
        return $save;
    }
}

?>