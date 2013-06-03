<?php

require_once 'checker/functions.php';

// IN THIS EXAMPLE ALL EXISTING CHECKING FUNCTION ARE PRESENT

extensionAvailable("ctype");            // check if 'ctype' php extension is loaded
extensionAvailable("a fake extension"); // check if 'a fake extension' php extension is loaded
extensionAvailable("mysql");            // check if 'mysql' php extension is loaded

apacheModuleAvailable("mod_rewrite"); // is ignored when not in apach context (e.g. cli, not tryed with nginx)  // check if apache module 'mod_rewrite' is enabled

mysqlIsReachable("localhost","root","root"); // will also check for mysql extension

phpNumberVersionIs(">=", "5.4"); // Check php version. Also available with other operators :   ==   >  <   =<  =>

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
dirIsWritable("checker"); // check if a directory is writable. Mays ouput that directory doesnt exists
fileIsWritable("checker");// check if a file is writable. Mays ouput that file doesnt exists



// ADVANCED USAGES :   
// ;)

// you also can discreetly check your requirements...
requirementStopStack();
$myCustomExtesionExists=  extensionAvailable("myExtension"); // wont output
// ...and loudely check again...
requirementStackAgain();
// ...and finally do yourselve the requirements
if($myCustomExtesionExists == true){
    addRequirement("YES IT WORKS !!!",true); // "YES IT WORKS" is the message to print. True means that the requirement works
}else{
    addRequirement("SH*T ! this extension is not available... :( "); 
}

// END OF ADVANCED USAGE. Now you can take a break... Oh wait.. It is almost done ! :)


// FINALLY RENDER THE REQUIREMENTS RESULTS

renderAllRequirements();// will render the requirement (html if browser, or CLI formatted if cli)

//ALSO AVAILABLE :
// $backup = resetRequirements(); // reset all requirements and returns a backup of the actual that can be loaded latter
// echo countBadRequirements();   // this method returns how many requirements fail. It allows to use it as an API to just check how many requirements are missing
// loadRequirements($bu);         // load some backed up requirements


