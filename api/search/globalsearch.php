<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  //include_once '../../models/Search.php';
  include_once '../../models/Business.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate Users object
  //$search = new Search($db);
  $business = new Business($db);

  $data = json_decode(file_get_contents("php://input"));
  //print_r($data);
  $business->uid = $data->uid;          //User id
  $business->uid = $data->uid;
  $business->token = $data->token;      //Auth token

  $business->lat = $data->latitude;
  $business->lon = $data->longitude;
  $business->searchstr = $data->searchstr;
  
  // Save JSON
  $fname ="../../jsons/".$business->uid."globalsearch.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);


  // Validate request
  if(!$business->uid){
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'User identification missing.'
        )
    );
    $error = 1;
  } else if(!$business->token){  // Check if token exists
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'Auth token missing.'
        )
    );
    $error = 1;
  }

  if($error > 0)
   exit;

// Validate token against Users table.
  $result = $business->valtoken();
  $num = $result->rowCount();

  if($num ==0){
    echo json_encode(
      array(
        'status' => 'false',
        'message' => 'Invalid Auth token or User identification'
      )
    );
    exit;
  }

  // global search
  //$business->searchstr = $data->searchstr;
  $result = $business->searchbusiness();
  $num = $result->rowCount();

  // Check if any categories
  if($num > 0) {
        // Cat array
        $business_arr = array();
        $business_arr['data'] = array();
        
        $time_arr = array();
        //$time_arr['timings'] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
          extract($row);

          //Get if user is following this business.
          $business->fromusrid = $business->uid;
          $business->busid = $row['BusId'];
          $business->isFollowingBuss();

          $distance = round($row['distance'],5)." km";

          //Get business timings
          //unset($time_arr); // $foo is gone
          $time_arr = array(); // $foo is here again
          
          $result2 = $business->gettimings();
          $timecnt = $result2->rowCount();
          // echo "timings count :".$timecnt;
          if($timecnt > 0) {
            while($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
              extract($row2);
        
              $time_item = array(
                'bussid'        => $row2['BusId'],
                'weekday' 		  => $row2['WeekDay'],
                'starttime' 	  => $row2['StartTime'],
                'endtime'  		  => $row2['EndTime'],
                'busstimingsid' => $row2['BussTimingsId']
              );
              // Push to "data"
              // array_push($time_arr['timings'], $time_item);
              array_push($time_arr, $time_item);
              // echo json_encode($time_item);
            }

          } 


          $business_item = array(
            'busid'   => $row['BusId'],
            'name'    => $row['Name'],
            'busdesc' => $row['BusDesc'],
            'ownerid' => $row['UsrId'],
            'address' => $row['Address'],
            'picurl'  => $row['PicUrl'],
            'phoneid' => $row['PhoneId'],
            'distance' => $distance,
            'follow'   => $business->cnt,
            'timings'  => $time_arr
          );

          // Push to "data"
          array_push($business_arr['data'], $business_item);
          $time_arr = array();
        }
        
        // Turn to JSON & output
        echo json_encode($business_arr);      

  } else {
        // Check products table if searched for products
        $result = $business->searchproducts();
        $num = $result->rowCount();
      
        // Check if any categories
        if($num > 0) { 
            $prod_arr = array();
            $prod_arr['data'] = array();
            
            $time_arr = array();
            //$time_arr['timings'] = array();

            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            //Get if user is following this business.
            $business->fromusrid = $business->uid;
            $business->busid = $row['BusId'];
            $business->isFollowingBuss();

            $distance = round($row['distance'],5)." km";

            //Get business timings
            //unset($time_arr); // $foo is gone
            $time_arr = array(); // $foo is here again
            
            $result2 = $business->gettimings();
            $timecnt = $result2->rowCount();
            // echo "timings count :".$timecnt;
            if($timecnt > 0) {
                while($row2 = $result2->fetch(PDO::FETCH_ASSOC)) {
                extract($row2);
            
                $time_item = array(
                    'bussid'        => $row2['BusId'],
                    'weekday' 		  => $row2['WeekDay'],
                    'starttime' 	  => $row2['StartTime'],
                    'endtime'  		  => $row2['EndTime'],
                    'busstimingsid' => $row2['BussTimingsId']
                );
                // Push to "data"
                // array_push($time_arr['timings'], $time_item);
                array_push($time_arr, $time_item);
                // echo json_encode($time_item);
                }

            } 


            $prod_item = array(
                'busid'   => $row['BusId'],
                'name'    => $row['Name'],
                'busdesc' => $row['BusDesc'],
                'ownerid' => $row['UsrId'],
                'address' => $row['Address'],
                'picurl'  => $row['PicUrl'],
                'phoneid' => $row['PhoneId'],
                'distance' => $distance,
                'follow'   => $business->cnt,
                'timings'  => $time_arr
            );

            // Push to "data"
            array_push($prod_arr['data'], $prod_item);
            $time_arr = array();
            }
            
            // Turn to JSON & output
            echo json_encode($prod_arr);  
        } else {

          echo json_encode(
          array(
            'status' => 'false',
            'message' => 'No businesses found, try searching with product name or business name.'
          )
        );
    }
  }
  
  

