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
  $groups->groupid = $data->groupid;
  $groups->members = $data->members;
  
  $groups->isadmin();
  $admin = $groups->admin;

  //echo "Admin :[".$admin."]";

  // Users read query
  if(!$admin) {
      // Create Group
      if(!$groups->updategroup()){
        echo json_encode(
            array(
              'status' => 'false',
              'message' => 'Unable to update Group.'
            )
        );
      }

      $groups->members = json_encode($groups->members,true);
      
      //echo "group members".$groups->members."\n";

      $myArray = explode(',', $groups->members);
      //print_r($myArray);

      foreach ($myArray as $value) {
          //$groups->admin=1;

          $groups->memid = $value;
          $groups->memid = str_replace('"', '', $groups->memid);

          //echo "memid now is:".$groups->memid."\n";

          if($groups->memid != $groups->uid){
            $status = $groups->deletememberfromgroup();
          }

      }

      echo json_encode(
          array(
            'status' => 'true',
            'message' => 'Group Updated Successfully.'
          )
      );
  }else {
    echo json_encode(
          array(
            'status' => 'false',
            'message' => 'Only Group admin can update Group.'
          )
      );
  }
  
  
