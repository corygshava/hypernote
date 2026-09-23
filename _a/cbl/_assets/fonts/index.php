<?php
    // any files or folders you want hidden
    $tohide = [
        '.git'
    ];
    $dir = './';                            // location of the folder
    $folder_name = basename(__DIR__);       // the title of the folder
    $show_files = true;

    // /*
    // uncomment this corian strip to allow this to be called by any page or modify accordingly
    // just make sure you know what you are doing first
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: content-type,bearer');
    // */
    include $_SERVER['DOCUMENT_ROOT'].'/lister/browser.php';
?>