<?php

const DOC_BUCKET_BASE="/prj1/documents/";
const IMG_BUCKET_BASE="/prj1/thumbnail/";

class Utils {

    public static function getCurrentDate(){
        date_default_timezone_set("Asia/Kolkata");
        return date("Y-m-d");
    }


    public static function fetchRowAsArray($result)
    {
        $array = array();

        if($result instanceof mysqli_stmt)
        {
            $result->store_result();

            $variables = array();
            $data = array();
            $meta = $result->result_metadata();

            while($field = $meta->fetch_field())
                $variables[] = &$data[$field->name]; // pass by reference

            call_user_func_array(array($result, 'bind_result'), $variables);

            $i=0;
            while($result->fetch())
            {
                $array[$i] = array();
                foreach($data as $k=>$v)
                    $array[$i][$k] = $v;
                $i++;

                // don't know why, but when I tried $array[] = $data, I got the same one result in all rows
            }
        }
        elseif($result instanceof mysqli_result)
        {
            while($row = $result->fetch_assoc())
                $array[] = $row;
        }

        return $array;
    }

    public static function getNewFileName($fileObject, $fileDestination, $new_filename)
    {

        $fileData = pathinfo(basename($fileObject["name"]));

        $fileName = uniqid() . '_' . $new_filename . '.' . $fileData['extension'];

        $target_path = ($fileDestination . $fileName);

        while (file_exists($target_path)) {
            $fileName = uniqid() . '_' . $new_filename . '.' . $fileData['extension'];
            $target_path = ($fileDestination . $fileName);
        }

        return $fileName;

    }

    public  static  function getBaseURL() {
        $isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
        $port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
        $port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
        $url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port;
        return $url;
    }

    public  static  function getImageBucketURL(){
        return Utils::getBaseURL().IMG_BUCKET_BASE;
    }

    public  static  function getDocBucketURL(){
        return Utils::getBaseURL().DOC_BUCKET_BASE;
    }

    public static function uploadImageFile($fileObject, $fileDestination, $new_filename)
    {

        //  5MB maximum file size
        $MAXIMUM_FILESIZE = 5 * 1024 * 1024;

        //  Valid file extensions (images, word, excel, powerpoint)
        $rEFileTypes =
            "/^\.(jpg|jpeg|png){1}$/i";


        $isFile = is_uploaded_file($fileObject['tmp_name']);
        if ($isFile)    //  do we have a file?
        {   //  check if under 5MB,
            //  check file extension for legal file types

            if ($fileObject['size'] <= $MAXIMUM_FILESIZE) {
                if (preg_match($rEFileTypes, strrchr($new_filename, '.'))) {
                    $isMove = move_uploaded_file($fileObject['tmp_name'], $fileDestination . $new_filename);
                    return "success";
                } else {
                    return "File Format is not supported.";
                }
            } else {
                return "File Size is greater then 5MB";
            }
        } else {
            return "File not found.";
        }
    }

    public static function uploadPDFFile($fileObject, $fileDestination, $new_filename)
    {
        $isFile = is_uploaded_file($fileObject['tmp_name']);
        if ($isFile)    //  do we have a file?
        {   
                    $isMove = move_uploaded_file($fileObject['tmp_name'], $fileDestination . $new_filename);
                    return "success";
        } else {
            return "File not found.";
        }
    }

    public static function uploadVideoFile($fileObject, $fileDestination, $new_filename)
    {

        //  5MB maximum file size
        $MAXIMUM_FILESIZE = 10 * 1024 * 1024;

        //  Valid file extensions (images, word, excel, powerpoint)
        $rEFileTypes =
            "/^\.(mp4|3gp|mkv){1}$/i";


        $isFile = is_uploaded_file($fileObject['tmp_name']);
        if ($isFile)    //  do we have a file?
        {   //  check if under 5MB,
            //  check file extension for legal file types

            if ($fileObject['size'] <= $MAXIMUM_FILESIZE) {
                if (preg_match($rEFileTypes, strrchr($new_filename, '.'))) {
                    $isMove = move_uploaded_file($fileObject['tmp_name'], $fileDestination . $new_filename);
                    return "success";
                } else {
                    return "File Format is not supported.";
                }
            } else {
                return "File Size is greater then 5MB";
            }
        } else {
            return "File not found.";
        }
    }
	
	public static function deleteFile($fileDestination,$fileName)
    {
		$file= $fileDestination."".$fileName;
            if (unlink($file)) {
                    return "File Deleted";
                }
             else {
                return "Error in deleting the file";
            }
    }
}