<?php

$requirementsBag=array();
$uniqueRequirementsBag=array();

function addRequirement($name,$works){
    global $requirementsBag;
    $requirementsBag[]=array("works"=>$works,"name"=>$name);
}

function addUniqueRequirement($key,$name,$works){
    global $uniqueRequirementsBag;
    
    $uniqueRequirementsBag[$key]=array("works"=>$works,"name"=>$name);
    
}

function renderWorks($name){
    
    include(__DIR__."/requirementTrue.php");
    
}

function renderBroken($name){
    
    include(__DIR__.'/requirementFalse.php');
    
}

function renderAllRequirements(){
    if("cli" == php_sapi_name())
        renderCli();
    else 
        renderHtml();
}

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

function phpNumberVersionIs($operator,$number){
    $version= floatval(substr(phpversion(),0,3));
    
    $availableOperators = array("==",">=","<=",">","<");
    
    if( in_array($operator, $availableOperators) ){
        
        $number=  floatval($number);
        
        
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

function extensionAvailable($name){
    $isLoaded=extension_loaded($name);
    
    if($isLoaded)
        addUniqueRequirement("1:EXTENSION:$name","Extension ".$name.' was correctly loaded',true);
    else
        addUniqueRequirement("1:EXTENSION:$name","Extension ".$name.' was not loaded',false);
    
        
    
    return $isLoaded;
    
}


function apacheModuleAvailable($name){
    $moduleAvailable=in_array($name, apache_get_modules());
    
    if($moduleAvailable)
        addUniqueRequirement("2:APACHE:$name","Apache Module ".$name.' is available',true);
    else
        addUniqueRequirement("2:APACHE:$name","Apache Module ".$name.' is not available',false);
}


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
