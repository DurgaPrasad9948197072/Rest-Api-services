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
  //$groups->admin = $data->isadmin;
  $groups->members = $data->members;
  $groups->members = json_encode($groups->members,true);
  //print_r($groups->members);
  //echo "Start :".$groups->members."\n";

  $groups->checkgroup();
  $groupexists = $groups->chkgroupid;

  
  //echo "group exists".$groupexists;

  if($groupexists){
    
    //$arr = array($groups->members);
    //print_r($arr);
    $myArray = explode(',', $groups->members);
    //print_r($myArray);

    foreach ($myArray as $value) {
        $groups->admin=1;

        $groups->memid = $value;
        $groups->memid = str_replace('"', '', $groups->memid);

        //echo "MemberId :".$groups->memid."\n";
        //echo "groupid :".$groups->groupid."\n";
        //echo "admin :".$groups->admin."\n";

        if($groups->memid != $groups->uid){
         //echo "Not reg user:".$groups->memid."\n";
         
         $status = $groups->addgroupmem();
         //echo "status".$status."\n";

          if(!$status) {
            echo json_encode(
                array(
                  'status' => 'false',
                  'message' => 'Unable to add member to Group.'
                )
            );
          }
          
        }

        //echo "next\n";
    }

    echo json_encode(
        array(
          'status' => 'true',
          'message' => 'Members Added to Group.'
        )
    );


  } else {
    echo json_encode(
          array(
            'status' => 'false',
            'message' => 'Invalid Group.'
          )
      );
  }
  


  
