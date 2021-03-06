<?php

function DOC_getDetails($filename) {
    if(mime_content_type(PATH_CV.$filename) != 'application/msword') {
        return null;
    }
    
    $data['poid'] = filesize(PATH_CV.$filename).'; ';

    $debugText = file_get_contents(PATH_CV.$filename);
    $handle = fopen(PATH_CV.$filename, "r");
    $debugText = fread($handle, 3000);
    fclose($handle);
    
    if (mb_strpos($debugText, 'image du CV')) {
        $data['is_image'] = 1;
    }else{
        $data['is_image'] = 0;
    }

    $data['binary_file'] = $debugText;

    return $data;
}

function DOC_getText($filename){
    return shell_exec('antiword '.PATH_CV.$filename);
}