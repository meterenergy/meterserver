<?php

class IRecharge
{
    private static $SERVER = "https://irecharge.com.ng/pwr_api_sandbox/v2/";
    // private static $SERVER = "https://irecharge.com.ng/pwr_api_live/v2/";
    private static $vendor_code = "18012AFC93";
    private static $public_key = "e1a165e29b7c61c7055f04d34a21330b";
    // private static $public_key = "034abcfe123eff591ea9747cd5fd730c";
    private static $private_key = "202449bf53d9d45b68444bf532248785f3f4e14679410b8a85ad2aceb8d0d371d390c5064e1643eba7ee0515322f7890052327baa16f35a668d2d81dab294455";
    // private static $private_key = "6d0e62fb1657b003ddbe421053194242520c73fb5208487faa0a23d880a24b99a4df1f323ae1a16b942727f82b7661e2509784a82b9e441f31d3e2130eabcfe4";

    public static function getdiscos($response_format = 'json')
    {
        $response = new Response;
        $curl = curl_init();
        $curlopt = array(
            CURLOPT_URL => self::$SERVER."get_electric_disco.php?response_format=".$response_format,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET'
        );
        curl_setopt_array($curl, $curlopt);
        $res = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);
        if ($error) {
            return $response->Error('Could not resolve discos');
        } else {
            $decoded = json_decode(substr($res, 3, strlen($res)));
            return $response->Success('okay', $decoded->bundles);
        }
    }

    public static function verifymeter(string $meter, string $disco, $response_format = 'json')
    {
        $response = new Response;
        $reference_id = nums_from_date(12);
        $combined_string = self::$vendor_code."|".$reference_id."|".$meter."|".$disco."|".self::$public_key;
        $hash = hash_hmac("sha1", $combined_string, self::$private_key);
        $url = self::$SERVER . "get_meter_info.php?vendor_code=".urlencode(self::$vendor_code)."&meter=".urlencode($meter)."&reference_id=".urlencode($reference_id)."&disco=".urlencode($disco)."&response_format=".urlencode($response_format)."&hash=".urlencode($hash);
        $curl = curl_init();
        $curlopt = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET'
        );
        curl_setopt_array($curl, $curlopt);
        $res = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return $response->Error('Could not resolve meter');
        } else {
            $decoded = json_decode(substr($res, 3, strlen($res)));
            $access_token = isset($decoded->access_token)?$decoded->access_token:"";
            $customer = isset($decoded->customer)?$decoded->customer:[];
            $data = [
                'token' => $access_token,
                'ref' => $reference_id,
                'customer' => $customer
            ];
            return $response->Success('okay', $data);
        }
    }

    public static function vendmeter(string $meter, string $disco, string $ref, string $access_token, int $amount, string $phone, string $email, $response_format = 'json')
    {
        $response = new Response;
        $combined_string = self::$vendor_code."|".$ref."|".$meter."|".$disco."|".$amount."|".$access_token."|".self::$public_key;
        $hash = hash_hmac("sha1", $combined_string, self::$private_key);
        $url = self::$SERVER."vend_power.php?vendor_code=".urlencode(self::$vendor_code)."&meter=".urlencode($meter)."&reference_id=".urlencode($ref)."&disco=".urlencode($disco)."&response_format=".urlencode($response_format)."&access_token=".$access_token."&amount=".$amount."&phone=".$phone."&email=".$email."&hash=".urlencode($hash);
        $curl = curl_init();
        $curlopt = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'GET'
        );
        curl_setopt_array($curl, $curlopt);
        $res = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return $response->Error('Could not resolve meter');
        } else {
            $decoded = json_decode(substr($res, 3, strlen($res)));
            $amount = isset($decoded->amount)?$decoded->amount:"";
            $units = isset($decoded->units)?$decoded->units:"";
            $meter_token = isset($decoded->meter_token)?$decoded->meter_token:"";
            $address = isset($decoded->address)?$decoded->address:"";
            $data = [
                'ref' => $ref,
                'amount' => $amount,
                'units' => $units,
                'token' => $meter_token,
                'address' => $address
            ];
            return $response->Success($decoded->message, $data);
        }
    }

}

?>