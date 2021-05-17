<?php

import(MODEL.'Wallet');
import(MODEL.'Transaction');
import(SERVICE.'IRecharge');
import(SERVICE.'GladePay');

class UserController extends Controller
{
    public function register()
    {
        $request = new Request;
        $response = new Response;

        $data = [
            'firstname' => $request->input('firstname'),
            'middlename' =>  $request->input('middlename'),
            'lastname' => $request->input('lastname'),
            'phonenumber' => $request->input('phonenumber'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'verified' => true
        ];

        if (!$data['firstname'] || !$data['lastname'] || !$data['phonenumber'] || !$data['email'] || !$data['password']) return $response->Error('Invalid Entries');

        $register = Auth::register($data);

        return $register;
    }

    public function login()
    {
        $request = new Request;
        $response = new Response;
        //echo json_encode($request->data);
        //echo json_encode($request->header());
        
        $request->validate([
            'phonenumber|string',
            'password|string'
        ]);

        if (!$request->input('phonenumber') || !$request->input('password')) return $response->Error('Invalid Entries!');

        $login = Auth::login($request->input('phonenumber'), $request->input('password'));

        if ($login->status) {
            $wallets = new Wallet;
            $wallet = $wallets->where('holder', $request->input('phonenumber'))->get();
            if (!isset($wallet[0])) {
                $wallets->Create([
                    'holder' => $request->input('phonenumber')
                ])->save();
            }
        }
        
        return $login;
    }

    public function user()
    {
        $response = new Response;
        if (!Auth::User()) return $response->Error('Invalid user');
        return $response->Success('okay', Auth::User());
    }

    public function wallet()
    {
        $response = new Response;
        $user = Auth::User();
        if (!$user) return $response->Error('Invalid user');

        $wallets = new Wallet;
        $wallet = $wallets->where('holder', $user->phonenumber)->get();
        if (!isset($wallet[0])) return $response->Error('Could not resolve wallet');
        $wallet = $wallet[0];

        return $response->Success('okay', $wallet);
    }

    public function verify_meter()
    {
        $request = new Request;
        $meter = $request->input('meter');
        $disco = $request->input('disco');
        $user = Auth::User();

        if (!$user) die();

        $purchase = IRecharge::verifymeter($meter, $disco);

        if ($purchase->status) {
            $data = $purchase->data;
            $transactions = new Transaction;
            $transaction = $transactions->where('reference', $data['ref'])->get();

            if (!isset($transaction[0])) {
                $transaction = $transactions->Create([
                    'name' => 'Electrical Power Purchase',
                    'method' => 'IRecharge',
                    'type' => 'meter',
                    'amount' => 0,
                    'sender' => $user->phonenumber,
                    'recipient' => $disco.' '.$meter,
                    'reference' => $data['ref'],
                    'trackid' => $data['token'],
                    'body' => 'Purchase awaiting approval',
                    'author' => 'self',
                    'status' => 'pending'
                ])->save();
            }
        }

        return $purchase;
    }

    public function vend_meter()
    {
        $request = new Request;
        $response = new Response;
        $meter = $request->input('meter');
        $disco = $request->input('disco');
        $ref = $request->input('ref');
        $amount = $request->input('amount');
        $user = Auth::User();

        if (!$user) die('Invalid User');

        $wallets = new Wallet;
        $wallet = $wallets->where('holder', $user->phonenumber)->get();
        if (!isset($wallet[0])) return $response->Error('Could not resolve wallet');
        $wallet = $wallet[0];

        $price = intval($amount) + intval($wallet->outstanding) + self::getSettings()->metercharge;
        if ($amount > $wallet->balance) return $response->Error('Insufficient Fund. Deposit Now!');
        $wallet->balance = intval($wallet->balance) - $price;
        $wallet->outstanding = intval($wallet->outstanding) - intval($wallet->outstanding);
        $wallet->update();

        $transactions = new Transaction;
        $transaction = $transactions->where('reference', $ref)->get();
        if (!isset($transaction[0])) die("Transaction not found");
        $transaction = $transaction[0];

        $purchase = IRecharge::vendmeter($meter, $disco, $ref, $transaction->trackid, $amount, $user->phonenumber, $user->email);
        if ($purchase->status) {
            $transaction->amount = $amount;
            $transaction->body = "Purchase succussfully approved";
            $transaction->status = "completed";
            $transaction->update();
        }

        return $purchase;
    }

    // public function payment()
    // {
    //     $request = new Request;
    //     $type = $request->input('type');
    //     $data = $request->input('data');
    //     $amount = $request->input('amount');

    //     $user = Auth::User();

    //     if (!$user) die();


    //     switch ($type) {
    //         case "card":
    //             // $payment = GladePay::payment([
    //             //     'action' => 'initiate',
    //             //     'paymentType' => $type,
    //             //     'user' => [
    //             //         'firstname' => $user->firstname,
    //             //         'lastname' => $user->lastname,
    //             //         'email' => $user->email
    //             //     ],
    //             //     'card' => $data,
    //             //     'amount' => $amount,
    //             //     'country' => 'NG',
    //             //     'currency' => 'NGN'
    //             // ]);

    //             // if ($payment->status) {
    //             //     $reference = nums_from_date(12);
    //             //     $transactions = new Transaction;
    //             //     $transaction = $transactions->where('reference', $reference)->get();

    //             //     if (!isset($transaction[0])) {
    //             //         $transaction = $transactions->Create([
    //             //             'name' => 'Card Payment',
    //             //             'method' => 'GladePay',
    //             //             'type' => 'deposit',
    //             //             'amount' => $amount,
    //             //             'sender' => $user->phonenumber,
    //             //             'recipient' => 'wallet',
    //             //             'reference' => $reference,
    //             //             'trackid' => $payment->data['txnRef'],
    //             //             'body' => 'Payment awaiting approval',
    //             //             'author' => 'self',
    //             //             'status' => 'pending'
    //             //         ])->save();
    //             //     }

    //             //     unset($payment->data['txnRef']);
    //             //     $payment->data['ref'] = $reference;

    //             //     if ($transaction) {
    //             //         return $payment;
    //             //     }

    //             //     $response = new Response;
    //             //     return $response->Error('Payment could not be proccesed. Try Again!');
    //             // }

    //             break;
    //     }
    // }

    public function payment()
    {
        $request = new Request;
        $type = $request->input('type');
        $action = $request->input('action');
        $data = $request->input('data');
        $amount = $request->input('amount');
        $ref = $request->input('ref');
        $save = $request->input('save');

        $user = Auth::User();

        if (!$user) die('Invalid user');

        $transactions = new Transaction;

        switch ($type) {
            case "card":
                switch ($action) {
                    case "initiate":
                        $payment = GladePay::payment([
                            'action' => 'initiate',
                            'paymentType' => 'card',
                            'user' => [
                                'firstname' => $user->firstname,
                                'lastname' => $user->lastname,
                                'email' => $user->email
                            ],
                            'card' => $data,
                            'amount' => $amount,
                            'country' => 'NG',
                            'currency' => 'NGN'
                        ]);

                        if ($payment->status) {
                            $reference = nums_from_date(12);
                            $transactions = new Transaction;
                            $transaction = $transactions->where('reference', $reference)->get();

                            if (!isset($transaction[0])) {
                                $transaction = $transactions->Create([
                                    'name' => 'Card Payment',
                                    'method' => 'GladePay',
                                    'type' => 'deposit',
                                    'amount' => $amount,
                                    'sender' => $user->phonenumber,
                                    'recipient' => 'wallet',
                                    'reference' => $reference,
                                    'trackid' => $payment->data['txnRef'],
                                    'body' => 'Payment awaiting approval',
                                    'author' => 'self',
                                    'status' => 'pending'
                                ])->save();
                            }

                            unset($payment->data['txnRef']);
                            $payment->data['ref'] = $reference;

                            return $payment;

                            $response = new Response;
                            return $response->Error('Payment could not be proccesed. Try Again!');
                        }
                        break;

                    case "charge":
                        $transaction = $transactions->where('reference', $ref)->get();
                        if (!isset($transaction[0])) die('Transaction not found');
                        $transaction = $transaction[0];

                        $payment = GladePay::payment([
                            'action' => 'charge',
                            'paymentType' => $type,
                            'user' => [
                                'firstname' => $user->firstname,
                                'lastname' => $user->lastname,
                                'email' => $user->email
                            ],
                            'card' => $data,
                            'amount' => $transaction->amount,
                            'country' => 'NG',
                            'currency' => 'NGN',
                            'txtRef' => $transaction->trackid,
                            'auth_type' => 'PIN'
                        ]);

                        if ($payment->status) {
                            if (isset($payment->data['auth'])) {
                                $transaction->trackid = $payment->data['txnRef'];
                            } else {
                                $transaction->author = "self";
                                $transaction->status = "completed";
                                $transaction->body = "Payment approved";

                                if ($save) {
                                    $cards = new Card;
                                    $cards->Create([
                                        'maskname' => $payment->data->mast_name,
                                        'type' => $payment->data->type,
                                        'token' => $payment->data->token
                                    ])->save();
                                }
                            }

                            $transaction->update();

                            unset($payment->data['txnRef']);                            
                            $payment->data['ref'] = $ref;
                        }

                        return $payment;
                        break;

                    case "validate":
                        $transaction = $transactions->where('reference', $ref)->get();
                        if (!isset($transaction[0])) die('Transaction not found');
                        $transaction = $transaction[0];

                        $otp = $request->input('otp')?$request->input('otp'):"";

                        $payment = GladePay::payment([
                            'action' => 'validate',
                            'txtRef' => $transaction->trackid,
                            'otp' => $otp
                        ]);

                        if ($payment->status) {
                            $transaction->author = "self";
                            $transaction->status = "completed";
                            $transaction->body = "Payment approved";
                            $transaction->update();
                            
                            $payment->data['ref'] = $ref;
                        }

                        return $payment;
                        break;

                    case "verify":
                        $transaction = $transactions->where('reference', $ref)->get();
                        if (!isset($transaction[0])) die('Transaction not found');
                        $transaction = $transaction[0];

                        $payment = GladePay::payment([
                            'action' => 'verify',
                            'txtRef' => $transaction->trackid
                        ]);

                        if ($payment->status) {
                            $transaction->author = "self";
                            $transaction->status = "completed";
                            $transaction->body = "Payment approved";
                            $transaction->update();

                            $payment->data['ref'] = $ref;
                        }

                        return $payment;
                        break;
                }
                break;
        }
    }
}

?>