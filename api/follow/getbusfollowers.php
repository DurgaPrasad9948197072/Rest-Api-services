<?php 
 // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Following.php';
  include_once '../../models/Business.php';
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $following = new Following($db);
  $business = new Business($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  $following->uid = $data->uid;
  $following->token = $data->token;
  $business->busid = $data->busid;

  $following->touserid = $following->uid;

  // Users read query
  $result = $following->getbusfollowers();
  
  // Get row count
  $num = $result->rowCount();
  
  // Check if any categories
  if($num > 0) {
        // Cat array
        $fol_arr = array();
        $fol_arr['data'] = array();
        
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
          extract($row);
          
          $following->fromusrid = $FromUsrId;

          
          $following->isFollowingUser();

          $fol_item = array(
            'name' => $Name,
            'memid' => $FromUsrId,
            'follow' => $following->cnt
          );

          // Push to "data"
          array_push($fol_arr['data'], $fol_item);
        }
        //ksort($fol_item,0);
        // Turn to JSON & output
        echo json_encode($fol_arr);

  } else {
        // No Users
          echo json_encode(
          array(
            'status' => 'false',
            'message' => 'No followers for this User'
          )
        );
  }
