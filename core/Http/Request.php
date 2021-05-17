<?php

class Request
{
    private $params;
    private $requestMethod;
    private $contentType;
    private $requestUri;
    private $data = [];
    // public $data = [];

    public function __construct($params = [])
    {
        $this->params = $params;
        $this->requestMethod = trim($_SERVER['REQUEST_METHOD']);
        $this->contentType = !empty($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : "";

        if ($this->requestMethod == "POST" && strpos($this->contentType, 'application/json') != 0) {
            $body = [];
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }

            $this->data = $body;
        }

        if ($this->requestMethod == "POST" && strpos($this->contentType, 'application/json') == 0) {
            $content = trim(file_get_contents("php://input"));
            $decoded = json_decode($content);
            
            $this->data = (array)$decoded;
        }
    }

    public function header()
    {
        return getallheaders();
    }

    public function input($param = "")
    {
        if ($param && isset($this->data[$param])) return $this->data[$param];
        if ($param && !isset($this->data[$param])) return;
        else $this->data;
    }

    public function validate($invalids)
    {
        $valids = [];

        if (!$this->data) return false;

        foreach ($invalids as $invalid) {
            $filter = explode('|', preg_replace('/\s+/', '', $invalid));
            
            if (count($filter) == 2 && strtolower($filter[1]) == "string") {
                //echo $this->data->{$filter[0]};
                if (isset($this->data[$filter[0]])) {
                    array_push($valids, filter_var($this->data[$filter[0]], FILTER_SANITIZE_STRING));
                }
            }
        }

        if (count($invalids) == count($valids)) {
            return true;
        }

        else {
            return false;
        }
    }
    
}
