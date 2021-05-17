<?php

class GladePay
{
    private static $SERVER = "https://demo.api.gladepay.com/";
    // private static $SERVER = "https://api.glade.ng/";
    private static $mid = "GP0000001";
    private static $key = "123456789";


    public static function payment($data)
    {
        $response = new Response;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::$SERVER . "payment",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "key: " . self::$key,
                "mid: " . self::$mid
            ),
        ));

        $res = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            return $response->Error('Could not resolve payment host');
        } else {
            $res = json_decode($res);
            switch ($res->status) {
                case 200:
                    $message = $res->message?$res->message:$res->txnStatus;

                    $data = [
                        'cardToken' => $res->cardToken,
                        'chargedAmount' => $res->chargedAmount
                    ];

                    if (isset($res->card)) $data['card'] = $res->card;

                    return $response->Success($message, $data);
                    break;

                case 202:
                    if (isset($res->auth_type) && $res->auth_type == 'OTP') return $response->Success($res->validate, [
                        'auth' => $res->auth_type,
                        'txnRef' => $res->txnRef
                    ]);

                    if (isset($res->apply_auth) && $res->apply_auth == 'OTP') return $response->Success('okay', [
                        'auth' => $res->apply_auth,
                        'txnRef' => $res->txnRef
                    ]);

                    if (isset($res->apply_auth) && $res->apply_auth == 'PIN') return $response->Success('okay', [
                        'auth' => $res->apply_auth,
                        'txnRef' => $res->txnRef
                    ]);

                    if (isset($res->authURL)) return $response->Success('okay', [
                        'auth' => '3DSecure',
                        'authURL' => $res->authURL,
                        'txnRef' => $res->txnRef
                    ]);
                    break;
                
                default:
                    return $response->Error($res->message);
                    break;
            }
        }
    }

    // public static function chargepayment($data)
    // {
    //     $response = new Response;
    //     $curl = curl_init();
    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL => self::$SERVER . "payment",
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING => "",
    //         CURLOPT_MAXREDIRS => 10,
    //         CURLOPT_TIMEOUT => 30,
    //         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST => "PUT",
    //         CURLOPT_POSTFIELDS => json_encode($data),
    //         CURLOPT_HTTPHEADER => array(
    //             "key: " . self::$key,
    //             "mid: " . self::$mid
    //         ),
    //     ));

    //     $res = curl_exec($curl);
    //     $err = curl_error($curl);
    //     curl_close($curl);

    //     if ($err) {
    //         return $response->Error('Could not resolve payment host');
    //     } else {
    //         die ($res);
    //         $res = json_decode($res);
    //         if ($res->status == 202 && isset($res->apply_auth) && $res->apply_auth == 'OTP') return $response->Success($res->message, [
    //             'auth' => $res->apply_auth,
    //             'txnRef' => $res->txnRef
    //         ]);

    //         if ($res->status == 202 && isset($res->apply_auth) && $res->apply_auth == 'PIN') return $response->Success($res->message, [
    //             'auth' => $res->apply_auth,
    //             'txnRef' => $res->txnRef
    //         ]);

    //         if ($res->status == 202 && isset($res->authURL)) return $response->Success('okay', [
    //             'auth' => '3DSecure',
    //             'authURL' => $res->authURL,
    //             'txnRef' => $res->txnRef
    //         ]);

    //         else return $response->Error('Invalid request');
    //     }
    // }
}

?>