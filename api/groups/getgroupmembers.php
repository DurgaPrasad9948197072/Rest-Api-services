<?php 
 // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Groups.php';
  include_once '../../models/Following.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $groups = new Groups($db);
  $following = new Following($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  $groups->uid = $data->uid;
  $groups->token = $data->token;
  $groups->groupid = $data->groupid;

  $following->uid = $data->uid;

  // Users read query
  $result = $groups->getgroupmembers();
  
  // Get row count
  $num = $result->rowCount();
 
  $grp_arr = array();
  $grp_arr['data'] = array();

  // Check if any categories
  if($num > 0) {

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
          extract($row);
      
          //$following->fromusrid = $Id;
          $following->fromusrid = $following->uid;
          $following->uid = $Id;

          $following->isFollowingUser();

          $grp_item = array(
            'MemId' => $Id,
            'Name' => $Name,
            'StatusMsg' => $StatusMsg,
            'Status'  => $Status,
            'Invisible' => $Invisible,
            'PicUrl' => $PicUrl,
            'Activity' => $Activity,
            'AdminUser' => $Admin,
            'Device' => $Device,
            'fcmToken' => $FcmToken,
            'follow' => $following->cnt
          );
          // Push to "data"
          array_push($grp_arr['data'], $grp_item);
        }

        // Turn to JSON & output
        echo json_encode($grp_arr);

  } else {
        // No Users
          echo json_encode(
          array(
            'status' => 'false',
            'message' => 'No members found for this Group'
          )
        );
  }
  
