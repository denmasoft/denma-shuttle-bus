<?php
define ('MAX_SIZE','20000000');
function shuttlebus_get_file_extension( $str ) {
    $parts = explode( '.', $str );
    return end( $parts );
}
function uploadFile($file,$dir,$name){
    $error = null;
    if ($file)
    {
        $filename = stripslashes($file['name']);
        $extension = shuttlebus_get_file_extension($filename);
        $extension = strtolower($extension);
        if (($extension != "png") && ($extension != "jpg"))
        {
            $error=1;
        }
        else
        {
            $mime = array ("image/jpeg", "image/png", "image/gif", "image/x-ms-bmp");
            if (!in_array($file['type'],$mime))
            {
                $error = 1;
            }
            $size=filesize($file['tmp_name']);
            if ($size > MAX_SIZE)
            {
                $error = 2;
            }
            if ($error==null)
            {
                $_name = uniqid('shuttle_bus',true).'.'.$extension;
                $newname=$dir.'/'.$_name;
                $copied = copy($file['tmp_name'], $newname);
                if ($copied)
                {
                    return $_name;
                }
            }
        }
    }
    return $error;
}