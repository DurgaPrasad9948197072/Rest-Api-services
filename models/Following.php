<?php
  class Following {
    // DB Stuff
    private $conn;
    private $table = 'Following';

    // Properties
    public $fromusrid;
    public $touserid;
    public $bussid;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Get User followers
    public function getuserfollowers() {
      //echo "From getuserfollowers\n";
      // Create query
      $query = 'SELECT
        Name,
        FromUsrId,
        ToUserId,
        PicUrl
      FROM
        ' . $this->table . ' a, Users b
      WHERE
        a.FromUsrId=b.Id and
        ToUserId = :touserid';

      //echo "\n query : ".$query;
      
      
      // Prepare statement
      $stmt = $this->conn->prepare($query);
      //$this->touserid = htmlspecialchars(strip_tags($this->touserid));

      $stmt-> bindParam(':touserid', $this->touserid);
      

      //echo "\n touserid : ".$this->touserid;
      // Execute query
      $stmt->execute();

      return $stmt;
    }

    // Get User followers
    public function getbusfollowers() {
      // Create query
      $query = 'SELECT
        Name,
        FromUsrId,
        BusId
      FROM
        ' . $this->table . ' a, Users b
      WHERE
        a.FromUsrId=b.Id and
        BusId = :busid';

      // Prepare statement
      $stmt = $this->conn->prepare($query);
      $this->bussid = htmlspecialchars(strip_tags($this->busid));

      $stmt-> bindParam(':busid', $this->busid);

      
      
      // Execute query
      $stmt->execute();

      return $stmt;
    }
  

  // Follow user
  public function followuser() {

    // Create Query
    $query = 'INSERT IGNORE INTO ' .
      $this->table . '
    (FromUsrId,ToUserId) VALUES (:fromuserid,:touserid)';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // Clean data
    $this->fromuserid = htmlspecialchars(strip_tags($this->fromuserid));
    $this->touserid = htmlspecialchars(strip_tags($this->touserid));

    // Bind data
    $stmt-> bindParam(':fromuserid', $this->fromuserid);
    $stmt-> bindParam(':touserid', $this->touserid);

    // Execute query

    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);

    return false;
  }

  // UnFollow user
  public function unfollowuser() {
    // Create Query
    $query = 'DELETE FROM ' .
      $this->table . '
    WHERE FromUsrId= :fromuserid and ToUserId = :touserid';

  // Prepare Statement
  $stmt = $this->conn->prepare($query);

  // Clean data
  $this->fromuserid = htmlspecialchars(strip_tags($this->fromuserid));
  $this->touserid = htmlspecialchars(strip_tags($this->touserid));

  // Bind data
  $stmt-> bindParam(':fromuserid', $this->fromuserid);
  $stmt-> bindParam(':touserid', $this->touserid);

  // Execute query
  if($stmt->execute()) {
    return true;
  }

  // Print error if something goes wrong
  printf("Error: $s.\n", $stmt->error);

  return false;
  }


 // Follow Business
  public function followbuss() {
    // Create Query
    $query = 'INSERT IGNORE INTO ' .
      $this->table . '
    (FromUsrId,BusId) VALUES (:fromuserid,:busid)';

  // Prepare Statement
  $stmt = $this->conn->prepare($query);

  // Clean data
  $this->fromuserid = htmlspecialchars(strip_tags($this->fromuserid));
  $this->busid = htmlspecialchars(strip_tags($this->busid));

  // Bind data
  $stmt-> bindParam(':fromuserid', $this->fromuserid);
  $stmt-> bindParam(':busid', $this->busid);

  // Execute query
  if($stmt->execute()) {
    return true;
  }

  // Print error if something goes wrong
  printf("Error: $s.\n", $stmt->error);

  return false;
  }

  // UnFollow Business
  public function unfollowbuss() {
    // Create Query
    $query = 'DELETE FROM ' .
      $this->table . '
    WHERE FromUsrId= :fromuserid and BusId = :busid';

  // Prepare Statement
  $stmt = $this->conn->prepare($query);

  // Clean data
  $this->fromuserid = htmlspecialchars(strip_tags($this->fromuserid));
  $this->busid = htmlspecialchars(strip_tags($this->busid));

  // Bind data
  $stmt-> bindParam(':fromuserid', $this->fromuserid);
  $stmt-> bindParam(':busid', $this->busid);

  // Execute query
  if($stmt->execute()) {
    return true;
  }

  // Print error if something goes wrong
  printf("Error: $s.\n", $stmt->error);

  return false;
  }

  // Get User followers
  public function getfollwedbuss() {
    // Create query
    // select Name,BusDesc,PicUrl,BusId from Business where BusId in (select BusId from Following where FromUsrId=5);
    $query = 'SELECT
      BusId,
      Name,
      BusDesc,
      UsrId,
      Address,
      PicUrl,
      PhoneId
    FROM
      Business
    WHERE
      BusId in (select BusId from Following where FromUsrId=:uid)';

    // Prepare statement
    $stmt = $this->conn->prepare($query);
    $this->uid = htmlspecialchars(strip_tags($this->uid));

    $stmt-> bindParam(':uid', $this->uid);

    //echo "uid:".$this->uid."\n";
    
    // Execute query
    $stmt->execute();

    return $stmt;
  }
  
  // Is usr Admin?
  public function isFollowingUser(){
  // Create query
  $query = 'SELECT count(1) cnt FROM
        Following
    WHERE FromUsrId = :fromusrid 
    AND ToUserId = :uid';

    //Prepare statement
    $stmt = $this->conn->prepare($query);

    // Bind ID
    $stmt->bindParam(':fromusrid', $this->fromusrid);
    $stmt->bindParam(':uid', $this->uid);

    //echo "FromUsrId".$this->fromusrid."\n";
    //echo "ToUserId".$this->uid."\n";
    // Execute query
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // set properties
    $this->cnt = $row['cnt']; 
    //echo "\n admin from isadmin".$this->admin;

  }

 }
