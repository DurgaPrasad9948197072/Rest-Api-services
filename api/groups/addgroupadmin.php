<?php
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Groups.php';
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $groups = new Groups($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  //UsrId,GroupId,Admin

  $groups->uid = $data->uid;
  $groups->token = $data->token;
  $groups->groupid = $data->groupid;
  $groups->admin = $data->admin;
  

  if(!$groups->addgroupadmin()) {
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Unable to add Group Admin.'
        )
    );
  }else {
	  echo json_encode(
	      array(
	        'status' => 'true',
	        'message' => 'Group Admin Added Successfully'
	      )
	  );
  }
