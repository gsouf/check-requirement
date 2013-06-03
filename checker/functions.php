<?php

$requirementsBag=array();
$uniqueRequirementsBag=array();
$doStackBag=true;


/**
 * Add a requirement to the list of renders
 * This function takes a global variable. Be safe when using it
 * @param string $name text to show
 * @param boolean $works true if the requirement works. 
 */
function addRequirement($name,$works=false){
    global $requirementsBag;
    global $doStackBag;
    if($doStackBag)
        $requirementsBag[]=array("works"=>$works,"name"=>$name);
    
}

/**
 * Add an unique requirement to the list.
 * 
 * Unique requirements are rendered before other requirements.
 * They are sorted by key name.
 * A straightforward naming convention for the names:
 *      "SORT_NUMBER:TYPE:UNIQUE_NAME"
 * E.G :   "0:EXTENSION:mysql" 
 * (0 then it appears first in the list, "EXTENSION" means that is an extension and not for example an APACHE_MODULE and my is the name of the extension)
 * 
 * This function takes a global variable. Be safe when using it
 * @global type $uniqueRequirementsBag
 * @param string $key unique key. See above in the docblok
 * @param string $name text to show
 * @param boolean $works true if it works
 */
function addUniqueRequirement($key,$name,$works=false){
    global $uniqueRequirementsBag;
    global $doStackBag;
    if($doStackBag)
        $uniqueRequirementsBag[$key]=array("works"=>$works,"name"=>$name);
}

/**
 * used for html render
 */
function renderWorks($name){
    
    include(__DIR__."/requirementTrue.php");
    
}

/**
 * used for html render
 */
function renderBroken($name){
    
    include(__DIR__.'/requirementFalse.php');
    
}

/**
 * render all depending if cli or browser
 */
function renderAllRequirements(){
    if("cli" == php_sapi_name())
        renderCli();
    else 
        renderHtml();
}

/**
 * used for html render
 */
function renderHtml(){
    global $requirementsBag;
    global $uniqueRequirementsBag;
    
    include 'checker/header.html';
    
    ksort($uniqueRequirementsBag);
    foreach($uniqueRequirementsBag as $r){
        if(true === $r["works"])
            renderWorks ($r["name"]);
        else
            renderBroken ($r["name"]);
    }
    
    foreach($requirementsBag as $r){
        if(true === $r["works"])
            renderWorks ($r["name"]);
        else
            renderBroken ($r["name"]);
    }
    
    include (__DIR__.'/checker/footer.html');
    
}


/**
 * used for CLI render
 */
function renderCli(){
    
    global $requirementsBag;
    global $uniqueRequirementsBag;
    echo PHP_EOL;
    echo PHP_EOL."=============REQUIREMENTS============";
    echo PHP_EOL;
    echo PHP_EOL;
    echo "|------------------------------------".PHP_EOL;
    $errors=0;
    ksort($uniqueRequirementsBag);
    $requirements=  array_merge($uniqueRequirementsBag,$requirementsBag);
    foreach($requirements as $r){
        if(true === $r["works"]){
            echo "|".$r["name"].PHP_EOL;
            echo "|====>";
            echo "\033[32m OK \033[0m".PHP_EOL.
                    "|------------------------------------".PHP_EOL;
            
            
            
        }else{
            echo "|".$r["name"].PHP_EOL;
            echo "|====>";
            echo "\033[1;31m ERROR \033[0m".PHP_EOL.
                    "|------------------------------------".PHP_EOL;
            $errors++;

        }
    }
    
    echo PHP_EOL;
    echo "REQUIREMENTS SUCCESSFULLY CHECKED ".PHP_EOL;
    if($errors>0)
        echo "\033[1;31m$errors errors found !\033[0m".PHP_EOL."See above";
    else
        echo "All requirement are ok";
    echo PHP_EOL;
    echo PHP_EOL;
    
}


/**
 * returns the number of broken requirements
 */
function countBadRequirements(){
    global $requirementsBag;
    global $uniqueRequirementsBag;
   
    
    $errors=0;
    
    foreach($uniqueRequirementsBag as $r){
        if(true !== $r["works"]){
            $errors++;
        }
        
    }
    
    foreach($requirementsBag as $r){
        if(true !== $r["works"]){
            $errors++;
        }
    }
    
    return $errors;
    
}

/**
 * reset all requirements and returns actual requirements that you can load later with load reuqirements
 */
function resetRequirements(){
    global $requirementsBag;
    global $uniqueRequirementsBag;
   
    $backup=array();
    $backup["unique"]=  $uniqueRequirementsBag;
    $backup["others"]=  $requirementsBag;
    
    $uniqueRequirementsBag=array();
    $requirementsBag=array();
    
    
    return $backup;
}

/**
 * Load the given set of requirements
 */
function loadRequirements($bu){
    
    global $requirementsBag;
    global $uniqueRequirementsBag;
    
    if(isset($bu["unique"])){
        $uniqueRequirementsBag=  array_merge($uniqueRequirementsBag,$bu["unique"]);
    }
    
    if(isset($bu["others"])){
        $requirementsBag=  array_merge($requirementsBag,$bu["others"]);
    }
}

function requirementStopStack(){
    global $doStackBag;
    $doStackBag=false;
}

function requirementStackAgain(){
    global $doStackBag;
    $doStackBag=true;
}


/**
 * Check php version
 */
function phpNumberVersionIs($operator,$number){
    $version= floatval(substr(phpversion(),0,3));
    
    $availableOperators = array("==",">=","<=",">","<");
    
    if( in_array($operator, $availableOperators) ){
        
        $number=  floatval($number);
        
        // floatval and in_array ensure the eval call. Be care if you modify this function
        eval("\$numberVersionIsOk=".$version.$operator.$number.";");
        
        
        if($numberVersionIsOk)
            addUniqueRequirement("0:PHPVERSION","Php version is ".$operator." ".$number,true);
        else
            addUniqueRequirement("0:PHPVERSION","Php version is ".$operator." ".$number,false);
    
        return $numberVersionIsOk;
        
    }else
        trigger_error("parameters 1 of phpNumberVersion is expected to be among ".  implode(",", $availableOperators));
    
    
    return false;
    
}

/**
 * check if an extension is loaded
 */
function extensionAvailable($name){
    $isLoaded=extension_loaded($name);
    
    if($isLoaded)
        addUniqueRequirement("2:EXTENSION:$name","Extension '".$name."' was correctly loaded",true);
    else
        addUniqueRequirement("2:EXTENSION:$name","Extension '".$name."' was not loaded",false);
    
        
    
    return $isLoaded;
    
}


/**
 * check if an apache module is enabled
 */
function apacheModuleAvailable($name){
    $moduleAvailable=in_array($name, apache_get_modules());
    
    if($moduleAvailable)
        addUniqueRequirement("1:APACHE:$name","Apache Module '".$name."' is available",true);
    else
        addUniqueRequirement("1:APACHE:$name","Apache Module '".$name."' is not available",false);
}

/**
 * check if a dir is writable
 */
function dirIsWritable($path){
    $isDir=  is_dir($path);
    
    if($isDir){
        
        $writtable=is_writable($path);
        if($writtable){
            addUniqueRequirement("DIRECTORYWRITTABLE:$path","Directory $path is writtable",true);
            return true;
        }else{
            addUniqueRequirement("DIRECTORYWRITTABLE:$path","Directory $path exists but is not writable.",false);
            return false;
        }
    }else{
        if(is_file($path))
            addUniqueRequirement("DIRECTORYEXISTS:$path","$path is a file. It should be a directory.",false);
        else
            addUniqueRequirement("DIRECTORYEXISTS:$path","$path does not exist.",false);
        
        return false;
    }
}

/**
 * check if a file is writable
 */
function fileIsWritable($path){
    $isFile= is_file($path);
    
    if($isFile){
        
        $writtable=is_writable($path);
        if($writtable){
            addUniqueRequirement("FILEWRITTABLE:$path","File $path is writtable",true);
            return true;
        }else{
            addUniqueRequirement("FILEWRITTABLE:$path","File $path exists but is not writable.",false);
            return false;
        }
    }else{
        if(is_dir($path))
            addUniqueRequirement("FILEEXISTS:$path","$path is a directory. It should be a file.",false);
        else
            addUniqueRequirement("FILEEXISTS:$path","$path does not exist.",false);
        
        return false;
    }
}

/**
 * check if a mysql base is reachable
 */
function mysqlIsReachable($host,$user,$password){
    
    $loaded=  extensionAvailable("mysql");

    if($loaded){
        $db = mysql_connect($host, $user, $password);
        if ($db) {
            addRequirement("Mysql is reachable",true);
            return true;
        }else{
            addRequirement("Mysql is not reachable for  $user:$password@$host",false);
            return false;
        }
    }else{
        return false;
    }

}


// TODO DB EXISTS
// TODO TABLE EXISTS
// TODO MONGO IS REACHABLE
// TODO FILE EXISTS
// TODO DIR EXISTS
// TODO CLASS EXISTS
// TODO COMPOSER WAS LOADED