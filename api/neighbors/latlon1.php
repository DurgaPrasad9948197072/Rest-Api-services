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

  $neighbors->lat = $data->latitude;
  $neighbors->lon = $data->longitude;
  $neighbors->subarea ='';
  $neighbors->subarea2 ='';
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
  
  $neighbors->explodeAdd();   
  echo "<br> Address \n".$neighbors->address;
  echo "<br> subarea \n".$neighbors->subarea;
  echo "<br> subarea2 \n".$neighbors->subarea2;

  if($neighbors->saveloc()) {
    echo json_encode(
      array('message' => 'Address Saved')
    );
  } else {
    echo json_encode(
      array('message' => 'Failed to save address')
    );
  }

  
  

