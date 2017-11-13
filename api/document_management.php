<?php

/********* Create Document   ******/
$app->post("/addDocument", function () use ($app) {
    $db = new DbHandler();
    $response = array();
	$document_tag=array();

    try {
        $db->setAutoCommit(FALSE);
        if(!isset($_POST['document_detail'])){
            throw new Exception("Please enter valid document_detail as post data.");
        }
        if(!isset($_FILES['file1'])){
            throw new Exception("Please upload valid document as post data.");
        }
		if(!isset($_FILES['file2'])){
            throw new Exception("Please upload valid thumbnail image as post data.");
        }

        $request = json_decode($_POST['document_detail']);

        if(verifyRequiredParams(array('document_name','document_author','document_format'), $request)){
            return ;
        }

        $document_name=$request->document_name;
        $document_author=$request->document_author;
        $document_format=$request->document_format;
		$document_tag=$request->document_tag;
        $document_tag_string="";
		$cur_date=Utils::getCurrentDate();
		
		for($i=0;$i<count($document_tag);$i++){
            if($i==0){
                $document_tag_string=$document_tag[$i];
            }else{
                $document_tag_string=$document_tag_string.",".$document_tag[$i];
            }
        }

        $sql_query_ins_document = "INSERT INTO document (doc_title,author,doc_format,tags,lastmodified) VALUES (?,?,?,?,?)";

        if (!($stmt = $db->conn->prepare($sql_query_ins_document))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->bind_param("sssss",$document_name,$document_author,$document_format,$document_tag_string,$cur_date)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }

        $document_id=$stmt->insert_id;
		

		//add document
        $document_object=$_FILES['file1'];
        $document_uid=$document_id . "document";
        $target_document_path="../documents/";
        $target_document_name=Utils::getNewFileName($document_object,$target_document_path,$document_uid);
        $document_status=Utils::uploadPDFFile($document_object,$target_document_path,$target_document_name);


        if(strcmp($document_status,"success")!=0){
            throw new Exception("System is unable to upload document.");
        }
		
		//add thumbnail
		$thumbnail_object=$_FILES['file2'];
        $thumbnail_uid=$document_id . "thumbnail";
        $target_thumbnail_path="../thumbnail/";
        $target_thumbnail_name=Utils::getNewFileName($thumbnail_object,$target_thumbnail_path,$thumbnail_uid);
        $thumbnail_status=Utils::uploadPDFFile($thumbnail_object,$target_thumbnail_path,$target_thumbnail_name);


        if(strcmp($thumbnail_status,"success")!=0){
            throw new Exception("System is unable to upload document.");
        }

        $sql_query_update_document = "UPDATE document SET link_addr = ?,thumbnail_addr = ? WHERE ID = ?";


        if (!($stmt = $db->conn->prepare($sql_query_update_document))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->bind_param("ssi",$target_document_name,$target_thumbnail_name,$document_id)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }


        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }

        $db->commit();
        $response["code"] = 200;
        $response["message"] = "Document added successfully.";
        $response["cause"] = "";
        $response["response"]['document_id'] = $document_id;
        echoResponse(200, $response);

    } catch (Exception $e) {
        $db->rollback();
        $response["code"] = 201;
        $response["cause"] = "error";
        $response["message"] = $e->getMessage();
        $response["response"] = json_decode("{}");//"Trace:" .$e->getTraceAsString();
        echoResponse(201, $response);
    }

    $db->setAutoCommit(TRUE);
});


/********* Display all Documents   ******/

$app->post("/viewAllDocument", function () use ($app)  {

    $db = new DbHandler();
    $DOC_BASE_URL=Utils::getDocBucketURL();
    $IMG_BASE_URL=Utils::getImageBucketURL();
    $sql_query = "select
                      ID AS document_id,
                      doc_title AS document_name,
                      CONCAT('$DOC_BASE_URL',link_addr) AS document_url,
                      CONCAT('$IMG_BASE_URL',thumbnail_addr) AS document_thumbnail_url,
                      author AS document_author,
                      tags AS document_tag,
                      lastmodified AS document_date
                      FROM document";

    $r = $db->conn->query($sql_query) or die($this->mysqli->error.__LINE__);
    if ($r->num_rows > 0) {
        $result = array();
        while ($row = $r->fetch_assoc()) {
            $result[] = $row;
        }
        $response["code"] = 200;
        $response["message"] = "Document list successfully fetched.";
        $response["cause"] = "";
        $response["response"] = $result;
        echoResponse(200, $response);
    } else {
        $response["code"] = 201;
        $response["message"] = "Data Not Found.";
        $response["cause"] = "";
        $response["response"] = [];
        echoResponse(200, $response);
    }
});

/********* Update Document   ******/
$app->post("/updateDocument", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    try {
        $db->setAutoCommit(FALSE);
        if(!isset($_POST['document_detail'])){
            throw new Exception("Please enter valid document_detail as post data.");
        }

        $request = json_decode($_POST['document_detail']);

        if(verifyRequiredParams(array('document_id','document_name'), $request)){
            return ;
        }

		$document_id=$request->document_id;
        $document_name=$request->document_name;
        $document_tag=$request->document_tag;
        if(!isset($document_tag)){
            $document_tag="";
        }
        $document_tag_string="";
		$cur_date=Utils::getCurrentDate();

        for($i=0;$i<count($document_tag);$i++){
            if($i==0){
                $document_tag_string=$document_tag[$i];
            }else{
                $document_tag_string=$document_tag_string.",".$document_tag[$i];
            }
        }

        $sql_query_update_document = "UPDATE document SET doc_title = ?,lastmodified = ?,tags = ? WHERE ID = ?";


        if (!($stmt = $db->conn->prepare($sql_query_update_document))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->bind_param("sssi",$document_name,$cur_date,$document_tag_string,$document_id)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }


        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }

        $db->commit();
        $response["code"] = 200;
        $response["message"] = "Document updated successfully.";
        $response["cause"] = "";
        $response["response"]['document_id'] = $document_id;
        echoResponse(200, $response);

    } catch (Exception $e) {
        $db->rollback();
        $response["code"] = 201;
        $response["cause"] = "error";
        $response["message"] = $e->getMessage();
        $response["response"] = json_decode("{}");//"Trace:" .$e->getTraceAsString();
        echoResponse(201, $response);
    }

    $db->setAutoCommit(TRUE);
});

/**********   DELETE a Document  *********/
$app->post("/deleteDocument", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    try {
        $db->setAutoCommit(FALSE);
        if(!isset($_POST['document_detail'])){
            throw new Exception("Please enter valid document_detail as post data.");
        }

        $request = json_decode($_POST['document_detail']);

        if(verifyRequiredParams(array('document_id'), $request)){
            return ;
        }

		$document_id=$request->document_id;
		
		//Delete the file from documents directory
        $sql_query = "select link_addr,thumbnail_addr FROM document where ID = ".$document_id;
		$r = $db->conn->query($sql_query);
		$result = array();
        while ($row = $r->fetch_assoc()) {
            $result[] = $row;
        }
		$fileDestination="../documents/";
        $fileDestination2="../thumbnail/";

        $sql_query_update_document = "DELETE FROM document WHERE ID = ?";


        if (!($stmt = $db->conn->prepare($sql_query_update_document))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->bind_param("i",$document_id)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }


        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }

		Utils::deleteFile($fileDestination,$result[0]["link_addr"]);
        Utils::deleteFile($fileDestination2,$result[0]["thumbnail_addr"]);
        $db->commit();
        $response["code"] = 200;
        $response["message"] = "Document deleted successfully.";
        $response["cause"] = "";
        $response["response"]['document_id'] = $document_id;
        echoResponse(200, $response);

    } catch (Exception $e) {
        $db->rollback();
        $response["code"] = 201;
        $response["cause"] = "error";
        $response["message"] = $e->getMessage();
        $response["response"] = json_decode("{}");//"Trace:" .$e->getTraceAsString();
        echoResponse(201, $response);
    }

    $db->setAutoCommit(TRUE);
});



/********* Search Document   ******/
$app->post("/searchDocument", function () use ($app) {

$db = new DbHandler();
    $response = array();
    try {
        $db->setAutoCommit(FALSE);
        /*if(!isset($_POST['document_detail'])){
            throw new Exception("Please enter valid name.");
        }*/
		
		$request = json_decode($app->request->getBody());

        if(verifyRequiredParams(array('document_name'), $request)){
            return ;
        }
		$document_name=$request->document_name;
		$f=$request->filter;
		
		$DOC_BASE_URL=Utils::getDocBucketURL();
        $IMG_BASE_URL=Utils::getImageBucketURL();
		if($f==1){
        $sql_query = "select
                      ID AS document_id,
                      doc_title AS document_name,
                      CONCAT('$DOC_BASE_URL',link_addr) AS document_url,
                      CONCAT('$IMG_BASE_URL',thumbnail_addr) AS document_thumbnail_url,
                      author AS document_author,
                      tags AS document_tag,
                      lastmodified AS document_date
                      FROM document WHERE doc_title LIKE '%".$document_name."%'";
		}
		else if($f==2){
        $sql_query = "select
                      ID AS document_id,
                      doc_title AS document_name,
                      CONCAT('$DOC_BASE_URL',link_addr) AS document_url,
                      CONCAT('$IMG_BASE_URL',thumbnail_addr) AS document_thumbnail_url,
                      author AS document_author,
                      tags AS document_tag,
                      lastmodified AS document_date
                      FROM document WHERE tags LIKE '%".$document_name."%'";
		}
		else if($f==3){
        $sql_query = "select
                      ID AS document_id,
                      doc_title AS document_name,
                      CONCAT('$DOC_BASE_URL',link_addr) AS document_url,
                      CONCAT('$IMG_BASE_URL',thumbnail_addr) AS document_thumbnail_url,
                      author AS document_author,
                      tags AS document_tag,
                      lastmodified AS document_date
                      FROM document WHERE author LIKE '%".$document_name."%'";
		}

    $r = $db->conn->query($sql_query) or die($this->mysqli->error.__LINE__);
    if ($r->num_rows > 0) {
        $result = array();
        while ($row = $r->fetch_assoc()) {
            $result[] = $row;
        }
        $response["code"] = 200;
        $response["message"] = "Document successfully fetched.";
        $response["cause"] = "";
        $response["response"] = $result;
        echoResponse(200, $response);
    } else {
        $response["code"] = 201;
        $response["message"] = "Data Not Found.";
        $response["cause"] = "";
        $response["response"] = [];
        echoResponse(200, $response);
    }

    } catch (Exception $e) {
        $db->rollback();
        $response["code"] = 201;
        $response["cause"] = "error";
        $response["message"] = $e->getMessage();
        $response["response"] = json_decode("{}");//"Trace:" .$e->getTraceAsString();
        echoResponse(201, $response);
    }

    $db->setAutoCommit(TRUE);
});

