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
 
  // Users read query
  $result = $groups->listpublicgroups();
  
  // Get row count
  $num = $result->rowCount();

  // Check if any categories
  if($num > 0) {
        // Cat array
        $grp_arr = array();
        $grp_arr['data'] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
          extract($row);
          
          $groups->groupid = $group_id;

          // echo "\n Group Id :",$group_id;

          $groups->isadmin();
          $groups->getgroupinfo();

          $grp_item = array(
            'groupid' => $groups->groupid,
            'name' => $groups->name,
            'description' => $groups->description,
            'status' => $groups->status,
            'state'  => $groups->state,
            'picurl' => $groups->picurl,
            'adminuser' => $groups->admin,
            'doc'  => $groups->doc
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
            'message' => 'No Public Groups Found. Go ahead and create one'
          )
        );
  }
