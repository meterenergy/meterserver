<?php

import(MODEL.'Model');

class PassportAuth extends Model
{
    public $id;
    public $authorid;
    public $token;
    public $created;
    public $recreated;

    public function __construct()
    {
        $this->model = $this;
        $this->table = "auth";
        $this->migrate(LIB.'Passport/migrate/auth.sql', true);
    }

    public function save()
    {   
        $save = $this->insert([
            'authorid' => $this->authorid,
            'token' => $this->token
        ]);
        return $save;
    }
}

?>