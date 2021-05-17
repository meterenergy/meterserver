<?php

import(DB.'Database');
import(HTTP.'Request');
import(AUTH.'Auth');
import(LIB.'Passport/model/PassportAuth');

class Passport
{
    private $secret_key = "jwtsecretkey";
    public $token;
    public $author;

    public function __construct(string $author = '')
    {
        $this->author = $author;

        if ($author) {
            $auth = $this->validate($author);
            if ($auth) {
                $this->token = $auth->token;
            }
        }
    }

    public function apply(array $payload)
    {
        $auth = $this->validate($this->author);
        if (!$auth) {
            $token = $this->genToken($payload);
            $passport = new PassportAuth();
            $passport->Create([
                'authorid' => $this->author,
                'token' => $token
            ])->save();
        }
    }

    public function commit(array $payload)
    {
        $auth = $this->validate($this->author);
        if ($auth) {
            $token = $this->genToken($payload);
            $auth->token = $token;
            $auth->update();
        }  
    }

    public function pass()
    {
        $passport = new PassportAuth();
        $Request = new Request;
        if (isset($Request->header()['Authorization'])) {
            $token = str_replace('Bearer ', '', $Request->header()['Authorization']);
            $auth = $passport->where('token', $token)->get();
            if (!$auth) return;
            $this->valToken($token);
            if ($this->valToken($token) || count($auth) == 1) {
                return $auth[0]->authorid;
            }
        }
        return;
    }

    private function genToken(array $payload)
    {
        $header = base64_encode(json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256',
            'iat' => time()
        ]));
        $payload = base64_encode(json_encode($payload));
        $unsignedToken = $header.".".$payload;
        $signature = base64_encode(hash_hmac('sha256', $unsignedToken, $this->secret_key, true));
        $token = str_replace(['+', '/', '='], ['-', '_', ''], $unsignedToken.".".$signature);
        return $token;
    }

    private function valToken(string $token)
    {
        $token = str_replace(['-', '_', ''], ['+', '/', '='], $token);
        $combined = explode('.', $token);
        $signature = $combined[2];
        $unsignedToken = $combined[0].".".$combined[1];

        $ex_signature = base64_encode(hash_hmac('sha256', $unsignedToken, $this->secret_key, true));

        if ($signature == $ex_signature) return true;
        return false;
    }

    private function validate(string $author)
    {
        $PassportAuth = new PassportAuth;
        $auth = $PassportAuth->where('authorid', $author)->get();
        if (isset($auth[0])) {
            return $auth[0];
        }
        return;
    }
}

?>