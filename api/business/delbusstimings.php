<?php 
 // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Business.php';
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $business = new Business($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  $business->uid = $data->uid;
  $business->bustimingsid = $data->bustimingsid;
  $business->token = $data->token;
  $business->busid = $data->busid;
  /*
  echo "\n uid:".$data->uid;
  echo "\n bustimingsid:".$data->bustimingsid;
  echo "\n token:".$data->token;
  echo "\n busid:".$data->busid;
  */
  
  // Users read query
 
	  if($business->delbusstimings()){
		  		echo json_encode(
					array(
		            'status' => 'true',
		            'message' => 'Business timing deleted successfully.'
		          )
		        );
		} else {
			echo json_encode(
		  		  array(
		            'status' => 'false',
		            'message' => 'Unable to delete.'
		          )
		    );
		}





