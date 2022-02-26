<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Neighbors.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate Users object
  $neighbors = new Neighbors($db);

  $data = json_decode(file_get_contents("php://input"));
  //print_r($data);
  $neighbors->uid = $data->uid;          //User id
  $neighbors->token = $data->token;      //Auth token

  $neighbors->lat = $data->latitude;
  $neighbors->lon = $data->longitude;

  $neighbors->subarea ='';
  $neighbors->subarea2 ='';
  
  // Save JSON
  $fname ="../../jsons/".$neighbors->uid."latlon.json";
  //echo $fname;
  $content = json_encode($data);

  $file = fopen($fname,'w+');
  fwrite($file, $content);
  fclose($file);


  // Validate request
  if(!$neighbors->uid){
    echo json_encode(
        array(
          'status' => 'false',
          'message' => 'User identification missing.'
        )
    );
    $error = 1;
  } else if(!$neighbors->token){  // Check if token exists
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
  $result = $neighbors->valtoken();
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

  $url  = "https://reverse.geocoder.ls.hereapi.com/6.2/reversegeocode.json?prox=".$data->latitude.",".$data->longitude."&mode=retrieveAddresses&maxresults=1&gen=9&apiKey=ihs_UxrA0mBIA32SDikkuA7RUmKrxwJXm6fPWHS5a2M";


  $json = file_get_contents($url);
  $data = json_decode($json,true);
  //print_r($data);

  $neighbors->country = $data['Response']['View']['0']['Result']['0']['Location']['Address']['AdditionalData']['0']['value'];
  $neighbors->state   = $data['Response']['View']['0']['Result']['0']['Location']['Address']['AdditionalData']['1']['value'];
  $neighbors->district= $data['Response']['View']['0']['Result']['0']['Location']['Address']['County'];
  $neighbors->city    = $data['Response']['View']['0']['Result']['0']['Location']['Address']['City'];
  $neighbors->suburb  = $data['Response']['View']['0']['Result']['0']['Location']['Address']['District'];
  $neighbors->pincode = $data['Response']['View']['0']['Result']['0']['Location']['Address']['PostalCode'];
  $neighbors->address = $data['Response']['View']['0']['Result']['0']['Location']['Address']['Label'];
  
  if(!$neighbors->suburb)
    $neighbors->suburb = $neighbors->city;

  if($neighbors->pincode)
  {
      // explode add ress and fetch subarea and subarea2
      $neighbors->explodeAdd();

      // Save  location and address
      $neighbors->saveloc();

      $result = $neighbors->read();

  } else {
      // Users read query
      $result = $neighbors->latlon();
      
        $neighbors_arr = array(
        'id' => $neighbors->id,
        'title' => $neighbors->title,
        'pincode' => $neighbors->pincode,
        'district' => $neighbors->district
      );

      $result = $neighbors->read();
  }
  // Get row count
  $num = $result->rowCount();

  // Check if any categories
  if($num > 0) {
        // Cat array
        $neighbors_arr = array();
        $neighbors_arr['data'] = array();
        $neighbors_arr2['location'] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
          extract($row);

          $neighbors_item = array(
            'id' => $id,
            'title' => $title
          );

          // Push to "data"
          array_push($neighbors_arr['data'], $neighbors_item);
        }

        $neighbors_item = array(
          'location' => $neighbors->address
        );
        array_push($neighbors_arr2['location'], $neighbors_item);

        $result = array_merge($neighbors_arr2,$neighbors_arr);
        // Turn to JSON & output
        echo json_encode($result);

  } else {
        // No Users
          echo json_encode(
          array(
            'status' => 'false',
            'message' => 'Neighborhoods not Found. Try search Neighborhoods.'
          )
        );
  }
  

