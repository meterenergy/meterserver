<?php

import(MODEL.'Setting');

class Controller
{
    public $settings;

    public function __construct()
    {
        $settings = new Setting;
        $setting = $settings->find(1);
        $this->settings = $setting;
    }

    public function settings()
    {
    	$response = new Response;
    	$controller = new Controller;
    	$settings = $controller->settings;
    	return $response->Success('okay', $settings);
    }

    public function getSettings()
    {
    	$controller = new Controller;
    	return $controller->settings;
    }
}

?>