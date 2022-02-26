<?php
  #
  # Check contacts registered with clozbii
  #
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

  //$groups->uid = $data->uid;
  

  $uniq = date("Y-m-d-H-i-s");
  $fname ="../../jsons/".$uniq."-checkcontacts.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);

  
  $groups_arr = array();
  $groups_arr['data'] = array();

  foreach($data as $rec){
			//echo $rec->phone_number.'--'.$rec->name.'<br>';

			$groups->mobile = str_replace("-", "", $rec->phone_number);
			$groups->mobile = str_replace("(", "", $groups->mobile);
			$groups->mobile = str_replace(")", "", $groups->mobile);
			$groups->mobile = str_replace("+", "", $groups->mobile);
			$groups->mobile = str_replace(" ", "", $groups->mobile);
			$groups->mobile = substr($groups->mobile,-10);
      
			//echo "<br> mobile is".$groups->mobile;
			//echo "mobile :[".$groups->mobile."]";

			$result = $groups->userexists();
			//$result = $neighbors->valtoken();
			$num = $result->rowCount();
			//echo "num :[".$num."]";

			if($num>0){
				$groups_item = array(
					'name' => $rec->name,
					'phone_number' => $rec->phone_number,
					'uid' =>  $groups->uid,
          'picurl' => $groups->picurl,
          'device' => $groups->device,
          'fcmtoken' => $groups->fcmtoken
	      );
	      		array_push($groups_arr['data'], $groups_item);
			}
			// Push to "data"
	        
  }
  ksort($groups_arr,2);
  echo json_encode($groups_arr);
  
  ?>
