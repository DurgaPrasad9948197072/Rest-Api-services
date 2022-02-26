<?php

  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Business.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();
  
  $business = new Business($db);

  // Get post
  $data = json_decode(file_get_contents("php://input"));

  $business->uid = $data->uid;
  $business->busid = $data->busid;
  $business->token = $data->token;

  $result = $business->gettimings();

  $tmscnt = $result->rowCount();
  $timings_arr['data'] = array();
  // Check for languages list
  
  if($tmscnt > 0) {

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        //echo "tmscnt :".$tmscnt."\n";
        $timings_item = array(
            'bussid'        => $row['BusId'],
            'weekday' 		=> $row['WeekDay'],
            'starttime' 	=> $row['StartTime'],
            'endtime'  		=> $row['EndTime'],
            'busstimingsid' => $row['BussTimingsId']
        );
        //echo "Id :".$Id."\n";
        //echo "Name :".$Name."\n";
        //echo "Code :".$Code."\n";

        // Push to "data"
        array_push($timings_arr['data'], $timings_item);
    }

    // Turn to JSON & output
    echo json_encode($timings_arr);

} else {
    
    echo json_encode(
        array(
            'status' => 'false',
            'message' => 'Timings not set for this Business yet'
            )
    );
}
//print_r(json_encode($languages_arr));
