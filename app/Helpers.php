<?php
 $dotenv = Dotenv\Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
 $dotenv->safeLoad();

if (!function_exists('env')) {
    function env($key_name) {
       if (isset($_ENV[$key_name])) {
           return $_ENV[$key_name];
       }
       return null;
    }
}


if (!function_exists('imageupload')) {
    function imageupload($str,$targetfile)
    {
        $filename = $_FILES[$str]['name'];
        $filesize = $_FILES[$str]['size'];
        $filetype = $_FILES[$str]['type'];
        $tempname = $_FILES[$str]['tmp_name'];

        $allowed = array(
            'jpg' =>"image/jpg",
            'jpeg' =>"image/jpeg",  
            'png' =>"image/png",
            'gif' =>"image/gif"
        );

        $ext  =  pathinfo($filename,PATHINFO_EXTENSION);
                
        if (!array_key_exists($ext, $allowed)) {
            return [
                'status' =>  false,
                'msg'    =>  'Invalid Extensions'
            ];
        }

        $filenew 	= str_replace($filename, 'we_devs', $filename).'_'.time().".".$ext;
        $targetfile = $targetfile.$filenew;
        if (in_array($filetype, $allowed)) {
            if (file_exists($targetfile)) {
                return [
                    'status' =>  false,
                    'msg'    =>  'This file is already exist'
                ];
            } else {
                move_uploaded_file($tempname, $targetfile);
            }
        } else {
            return [
                'status' =>  false,
                'msg'    =>  'There is problem uploading your files'
            ];
        }
        return [
            'status' =>  true,
            'data'   =>  $filenew
        ];
    }
}