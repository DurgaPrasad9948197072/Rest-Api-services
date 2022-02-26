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
  $groups->groupid = $data->groupid;
  $groups->token = $data->token;
 
  $groups->isadmin();
  $admin = $groups->admin;

  //echo "<\n Admin ?:[".$admin."]";
  if ($admin ==''){
  		echo json_encode(
          array(
            'status' => 'false',
            'message' => 'Group not found.'
          )
      	);
     exit;
  }
  // Users read query
  if($admin) {
	  if($groups->deletegroupmembers()){
		  // Users read query
		  if($groups->deletegroup()) {
		  		echo json_encode(
					array(
		            'status' => 'true',
		            'message' => 'Group deleted successfully.'
		          )
		        );
		  } else{
		  		echo json_encode(
		  		  array(
		            'status' => 'false',
		            'message' => 'Unable to delete Group.'
		          )
		        );
		  }
		} else {
			echo json_encode(
		  		  array(
		            'status' => 'false',
		            'message' => 'Unable to delete members from Group. Failed to delete Group.'
		          )
		    );
		}
  } else {
		echo json_encode(
          array(
            'status' => 'false',
            'message' => 'Only Group admin can update Group.'
          )
      	);
  }


