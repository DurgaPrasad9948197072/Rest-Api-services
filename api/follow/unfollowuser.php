<?php
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Following.php';
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $following = new Following($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  //UsrId,GroupId,Admin

  $following->uid = $data->uid;
  $following->token = $data->token;
  $following->fromuserid = $data->uid;
  $following->touserid = $data->unfollowusr;

   // Save JSON
  $fname ="../../".$following->uid."unfollowuser.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);


  if(!$following->unfollowuser()) {
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Unable to unfollow the user.'
        )
    );
  }else {
	  echo json_encode(
	      array(
	        'status' => 'true',
	        'message' => 'Unfollowed Successfully.'
	      )
	  );
  }
  
