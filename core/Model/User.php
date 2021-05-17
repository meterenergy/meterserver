<?php

import(MODEL.'Model');

class User extends Model
{
    public $id;
    public $firstname;
    public $middlename;
    public $lastname;
    public $phonenumber;
    public $email;
    public $password;
    public $status;
    public $verified;
    public $joined;

    public function __construct()
    {
        $this->model = $this;
        $this->table = "users";
    }

    // public function Create($firstname, $middlename, $lastname, $phonenumber, $email, $password) {
    //     $this->firstname = $firstname;
    //     $this->middlename = $middlename;
    //     $this->lastname = $lastname;
    //     $this->phonenumber = $phonenumber;
    //     $this->email = $email;
    //     $this->password = password_hash($password, PASSWORD_BCRYPT);
    //     return $this;
    // }

    public function save()
    {   
        $save = $this->insert([
            'firstname' => $this->firstname,
            'middlename' => $this->middlename,
            'lastname' => $this->lastname,
            'phonenumber' => $this->phonenumber,
            'email' => $this->email,
            'password' => password_hash($this->password, PASSWORD_BCRYPT)
        ]);
        return $save;
    }

    public function check_password($password_signin, $user) {
        $response = FALSE;
        $password = password_verify($password_signin, $user->password);
        return $password;
    }
}

?>