<?php

import(MODEL.'User');
import(HTTP.'Response');
import (LIB.'Passport/Passport');

class Auth
{
    private static $author;
    private static $authid = "phonenumber";

    // public function __construct()
    // {
    //     self::$author = isset($_SESSION['_auth994800390_sessionauthid']) ? $_SESSION['_auth994800390_sessionauthid'] : "";
    // }

    public function login(string $phonenumber, string $password)
    {
        $response = new Response;
        $user = new User;
        $user = $user->where('phonenumber', $phonenumber)->get();

        if (!isset($user[0])) return $response->Error('User does not exist');

        if (isset($user[0])) {
            if (!User::check_password($password, $user[0])) {
                return $response->Error('Phone number or password incorrect'); 
            } 
            else {
                $passport = new Passport($phonenumber);
                $passport->apply($user);
                //self::$author = $phonenumber;
                $_SESSION['_auth994800390_sessionauthid'] = $phonenumber;
                return $response->Success('Login Successfully', [
                    'token' => $passport->token
                ]);
            }
        }
    }

    public function register(array $data)
    {
        $response = new Response;
        try {
            $user = new User;
            $ex_user = $user->where(self::$authid, $data['phonenumber'])->get();

            if (isset($ex_user[0])) return $response->Error('User already exist');

            if ($user->Create($data)->save()) {
                $_SESSION['_auth994800390_sessionauthid'] = $data['phonenumber'];
                return $response->Success('User created successfully', []);
            }
            return $response->Error('Cannot create User');
        } catch (\Throwable $th) {
            echo $th;
            return $response->Error('An Error occurs');
        }
    }

    public function User()
    {
        $passport = new Passport();
        $author = $_SESSION['_auth994800390_sessionauthid'] ?? $passport->pass();
        $users = new User;
        $users = $users->where(self::$authid, $author)->get();
        return count($users) == 1 ? $users[0] : "";
    }

    public function ID()
    {
        return self::$authid;
    }

    // public function Authorize($author) {
    //     $_SESSION['_auth994800390_sessionauthid'] = $author;
    // }

}

?>