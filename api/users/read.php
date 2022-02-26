<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/Users.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate Users object
  $users = new Users($db);

  // Users read query
  $result = $users->read();
  
  // Get row count
  $num = $result->rowCount();

  // Check if any categories
  if($num > 0) {
        // Cat array
        $usr_arr = array();
        $usr_arr['data'] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
          extract($row);

          $usr_item = array(
            'Id' => $Id,
            'Name' => $Name,
            'Mobile' => $Mobile,
            'Status' => $Status
          );

          // Push to "data"
          array_push($usr_arr['data'], $usr_item);
        }

        // Turn to JSON & output
        echo json_encode($usr_arr);

  } else {
        // No Users
          echo json_encode(
          array('message' => 'No Users Found')
        );
  }
