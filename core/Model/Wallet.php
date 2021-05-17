<?php

import(MODEL.'Model');

class Wallet extends Model
{
    public $id;
    public $balance;
    public $outstanding;
    public $holder;
    public $status;
    public $date;

    public function __construct()
    {
        $this->model = $this;
        $this->table = "wallets";
    }

    public function save()
    {   
        $save = $this->insert([
            'holder' => $this->holder
        ]);
        return $save;
    }
}

?>