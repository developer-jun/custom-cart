<?php

// $class is equal to: 'Cart\Database' from the request: [use \Cart\Database;]
spl_autoload_register(function ($class) {
    
    $prefix = '';
    $class_dir = '';
    $class_dir_alias = 'Cart';
    $class_dir_real_dir = 'classes';
    $traits_dir = 'traits/';
    $base_dir = APP_DIR . '/'. $class_dir;

    // do class first
    // remove prefix
    $relative_class = substr($class, strlen($prefix));

    // replace alias
    /*if(strpos($relative_class, $class_dir_alias) !== FALSE) {
        $relative_class = replaceAliasWithRealDir($relative_class, $class_dir_alias, $class_dir_real_dir);
    }*/

    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    //echo '<br /><br />class: '. $file;
    //if (strncmp($prefix, $class, strlen($prefix)) === 0) {
      
    
    // regular class test
    if (file_exists($file)) {
        //echo '<br />file exists.';
        //echo '<br />includefile: '. $file;
        require $file;

        return true;
    } else {
        // TRAITS section
        // if 'Cart\Database.php' does not exists, then try traits 'traits\Database' 
        $relative_class = substr($class, strlen($prefix));
        $class_pieces = convertToProperDirNames(explode('\\', $relative_class));
        
        if(count($class_pieces) > 1){
          //$class_file = array_pop($class_pieces); // remove the keyword Cart
          $class_file = array_shift($class_pieces);
          $file = $base_dir . str_replace('\\', '/', implode('/',$class_pieces)) . '.php';
          //echo '<br />include other-file: ['. $file .']';
          require $file;

          return true;
        }
    }  
});

function replaceAliasWithRealDir(string $full_class, string $alias = '', string $real_dir = ''): string {
    $class_pieces = explode('\\', $full_class);
    
    $class_modified = array_map(function ($class) use ($alias, $real_dir) {
        if($alias == $class) {
            return $real_dir;
        }
        return $class;
    }, $class_pieces);

    return implode('\\', $class_modified);
}

function convertToProperDirNames(array $dir_files): array {

    $new_dir = array_map(function ($dir) {
        if(is_dir(APP_DIR .'/'. strtolower($dir))) {
            return strtolower($dir);
        }
        return $dir;
    }, $dir_files);
    return $new_dir;
}

function includeFile($file): bool {
    if (file_exists($file)) {
        require $file;

        return true;
    }

    return false;
}
