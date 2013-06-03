<?php

require_once 'checker/functions.php';

// IN THIS EXAMPLE ALL EXISTING CHECKING FUNCTION ARE PRESENT

extensionAvailable("ctype");
extensionAvailable("a fake extension");
extensionAvailable("mysql");

apacheModuleAvailable("mod_rewrite");

mysqlIsReachable("localhost","root","root"); // will also check for mysql extension but only render it once

phpNumberVersionIs(">=", "5.4"); // also available operators :   ==   >  <   =<  =>



/********************
 * 
 * ABOUT FILE SYSTEM : 
 * 
 * due to the system user right,
 * be aware that checking directory,
 * mays output different results,
 * if it is done from cli or from webserver (browser)
 * 
 *********************/
dirIsWritable("checker");
fileIsWritable("checker");


// will render the requirement (html if browser, or CLI formatted if cli)
renderAllRequirements();

//ALSO AVAILABLE :
// $backup = resetRequirements(); // reset all requirements and returns a backup of the actual that can be loaded latter
// echo countBadRequirements();   // this method returns how many requirements fail. It allows to use it as an API to just check how many requirements are missing
// loadRequirements($bu);         // load some backed up requirements
