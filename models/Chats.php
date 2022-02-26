<?php

  class Chats {
    // DB Stuff
    private $conn;
    private $table = 'chats';

    // Properties
    public $chatid;
    public $chattype;
    public $senderid;
    public $receiverid;
    public $thread;
    public $status;
    public $doc;
    public $dou;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

   public function getuserchats() {
      // Create query
      $query = 'SELECT
        id,
        chat_type,
        sender_id,
        reciever_id,
        status,
        doc,
        dou
      FROM
        ' . $this->table. ' 
      WHERE reciever_id  =:uid';  

      // Prepare statement
      $stmt = $this->conn->prepare($query);
      $stmt->bindParam(':uid', $this->uid);
      //echo "From getgroupinfo:".$this->groupid."\n"; 
      // Execute query
      $stmt->execute();

      return $stmt;
    }


    // Is usr Admin?
    public function isfollower(){
    // Create query
    $query = 'SELECT
        count(1) cnt
       FROM
          Following
      WHERE FromUsrId = :senderid 
      AND ToUserId = :uid';

      //Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(':senderid', $this->senderid);
      $stmt->bindParam(':uid', $this->uid);

      // Execute query
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // set properties
      $this->admin = $row['cnt'];
      //echo "\n admin from isadmin".$this->admin;

    }


  // Delete Groups
  public function deletechat() {
    // Create query
    $query = 'DELETE FROM messages WHERE chat_id = :chatid';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // clean data
    $this->chatid = htmlspecialchars(strip_tags($this->chatid));

    // Bind Data
    $stmt-> bindParam(':chatid', $this->chatid);

    // Execute query
    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);

    return false;
  }

  public function deletemessage() {
    // Create query
    
    $query = 'DELETE FROM messages WHERE id = :messageid';

    // Prepare Statement
    $stmt = $this->conn->prepare($query);

    // clean data
    $this->messageid = htmlspecialchars(strip_tags($this->messageid));

    // Bind Data
    $stmt-> bindParam(':messageid', $this->messageid);

    //echo "groupid:".$this->groupid;

    // Execute query
    if($stmt->execute()) {
      return true;
    }

    // Print error if something goes wrong
    printf("Error: $s.\n", $stmt->error);
 
    return false;
  }


  public function latestchats() {

    // Create query
     $query ="select id,chat_type, sender_id, reciever_id, thread , max(dou) 
                     from chats 
                where (sender_id=? or reciever_id =?)  group by 1,2,3,4,5  order by 6 desc;";

    // echo "\n Query ". $query;
     // Prepare statement
     $stmt = $this->conn->prepare($query);
     //echo "\n uid: ".$this->uid;
     
     // Bind ID
     $stmt->bindParam(1, $this->uid);
     $stmt->bindParam(2, $this->uid);
     //echo "\n uid2: ".$this->uid;
     // Execute query
     if($stmt->execute()) {
        //echo "\n returning...";
        return $stmt;
      }
  
      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);
   
      
     
     return false;

  }

  public function getlatestmessage(){
    $query = "SELECT text txt
    from messages 
    where chat_type=3 and sender_id = ? and chat_id =? order by dou desc limit 1";
    
    $stmt = $this->conn->prepare($query);

     // Bind ID
     $stmt->bindParam(1, $this->senderid);
     $stmt->bindParam(2, $this->chatid);

     // Execute query
     $stmt->execute();
    
     $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // set properties
     $this->message = $row['txt'];
     //return $stmt;

  }

 }
