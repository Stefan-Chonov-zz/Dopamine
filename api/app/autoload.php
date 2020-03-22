<?php

spl_autoload_register(function($className){
    $base_directory = "../";
    $filenameStartPosition = strrpos($className, "\\");
    $filenamePath = strtolower(substr($className, 0,$filenameStartPosition + 1));
    $filename = substr($className, $filenameStartPosition + 1, strlen($className) - strlen($filenameStartPosition) - 1);
    $filenameExtension = '.php';
    $file = $base_directory . $filenamePath . $filename . $filenameExtension;
    $file = str_replace('\\', '/', $file);

    if(file_exists($file)) {
        require $file;
    }
});

if(file_exists('../app/env.php')) {
    include '../app/env.php';
}

if(!function_exists('env')) {
    function env($key, $default = null)
    {
        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        return $value;
    }
}

?>