<?php

define('BASE', '/meter');
define('CORE', 'core/');
define('UTILS', 'utils');
define('HTTP', 'core/Http/');
define('LIB', 'core/Lib/');
define('CONTROLLER', 'core/Controller/');
define('DB', 'core/Database/');
define('MODEL', 'core/Model/');
define('AUTH', 'core/Auth/');
define('SERVICE', 'core/Service/');

function import($imported)
{
    return include_once($imported . '.php');
}

session_start();

import(UTILS);
import(DB.'Database');
import(CONTROLLER.'Controller');

new Database();

// new Passport();

?>