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

  $groups->uid = $data->uid;
  $groups->token = $data->token;
  $groups->name = $data->name;
  $groups->description = $data->description;
  $groups->state = $data->state;   #public or private group
  $groups->status = $data->status;
  $groups->picurl = $data->picurl;
  

  // Create Group
  if(!$groups->creategroup()){
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Unable to create Group.'
        )
    );
  }
  $groups->admin =1;

  if(!$groups->addgroupadmin()) {
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Unable to add Group Admin.'
        )
    );
  }

  echo json_encode(
      array(
        'status' => 'true',
        'message' => 'Group Created Successfully',
        'groupid' => $groups->groupid
      )
  );
  
  
