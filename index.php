<?php

require_once 'checker/functions.php';



extensionAvailable("phalcon");
extensionAvailable("phddalcon");
extensionAvailable("mysql");
mysqlIsReachable("localhost","root","root");
phpNumberVersionIs(">=", "5.4");
dirIsWritable("checker");
fileIsWritable("checker");

renderAllRequirements();
