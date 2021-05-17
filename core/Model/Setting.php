<?php

import(MODEL.'Model');

class Setting extends Model
{
    public $id;
    public $metercharge;

    public function __construct()
    {
        $this->model = $this;
        $this->table = "settings";
    }

    public function save()
    {   
        $save = $this->insert([
            'metercharge' => $this->metercharge
        ]);
        return $save;
    }
}

?>