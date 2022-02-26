<?php
  class Neighbors {
    // DB Stuff
    private $conn;
    private $table = 'pincodes';

    // Properties
    public $id;
    public $title;
    public $pincode;
    public $state;
    public $district;
    public $lat;
    public $lon;
    public $uid;
    public $nid;
    public $token;

    // Constructor with DB
    public function __construct($db) {
      $this->conn = $db;
    }

    // Get Neighborhoods by pincode
    public function read() {
      // Create query
      $query = 'SELECT
        id,
        title,
        pincode,
        state,
        district
      FROM
        ' . $this->table . '
      WHERE pincode = ?';

      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
      $stmt->bindParam(1, $this->pincode);

      // Execute query
      $stmt->execute();

      return $stmt;
    }

    // Get Neighborhoods by title
    public function listnbrtitle() {
      //echo "inside\n";
      // Create query
      $query = 'SELECT
        id,
        title,
        pincode,
        state,
        district,
        division,
        taluk,
        Lon,
        Lat
      FROM
        ' . $this->table . '
      WHERE title like CONCAT(?,"%") OR taluk like CONCAT(?,"%") order by title';
      //WHERE title like CONCAT(?,"%") OR division like CONCAT(?,"%") OR taluk like CONCAT(?,"%") order by title';

      //echo "query:".$query."\n";
      // Prepare statement
      $stmt = $this->conn->prepare($query);
      $this->title = htmlspecialchars(strip_tags($this->title));
      //echo "title:".$this->title."\n";
      // Bind ID
      $stmt->bindParam(1, $this->title);
      $stmt->bindParam(2, $this->title);
      //$stmt->bindParam(3, $this->title);

      // Execute query
      $stmt->execute();

      return $stmt;
    }

     // Get Neighborhoods by pincode
    public function listnbrpin() {
      // Create query
      $query = 'SELECT
        id,
        title,
        pincode,
        state,
        district,
        division,
        taluk,
        Lon,
        Lat
      FROM
        ' . $this->table . '
      WHERE pincode = ? order by title';

      // Prepare statement
      $stmt = $this->conn->prepare($query);
      $this->pincode = htmlspecialchars(strip_tags($this->pincode));
      
      // Bind ID
      $stmt->bindParam(1, $this->pincode);

      // Execute query
      $stmt->execute();

      return $stmt;
    }

    // Get Neighborhoods by latitude & longitude
    
    public function explodeAdd(){
    //explode address part.
    
        $explode_sql = 'SELECT TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(:address1,",", -4),",",1)) as area1, TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(:address2,",", -5),",",1)) as area2,TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(:address3,",", -2),",",1)," ",-1)) as pincode FROM dual';

        //echo $explode_sql;
        $stmt = $this->conn->prepare($explode_sql);

        $stmt-> bindParam(':address1', $this->address);
        $stmt-> bindParam(':address2', $this->address);
        $stmt-> bindParam(':address3', $this->address);

        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set properties
        $this->subarea = $row['area1'];
        $this->subarea2 = $row['area2'];

    }

    public function saveloc() {
    
      // Create Query
      $query = 'INSERT IGNORE INTO  nbhr_col2 (country,state,district,city,suburb,subarea,subarea2,Lat,Lon,pincode,address)
      VALUES (:country,:state,:district,:city,:suburb,:subarea,:subarea2,:lat,:lon,:pincode,:address)';


      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      $this->country = htmlspecialchars(strip_tags($this->country));
      $this->state = htmlspecialchars(strip_tags($this->state));
      $this->district = htmlspecialchars(strip_tags($this->district));
      $this->city = htmlspecialchars(strip_tags($this->city));
      $this->suburb = htmlspecialchars(strip_tags($this->suburb));
      $this->subarea = htmlspecialchars(strip_tags($this->subarea));
      $this->subarea2 = htmlspecialchars(strip_tags($this->subarea2));
      $this->lat = htmlspecialchars(strip_tags($this->lat));
      $this->lon = htmlspecialchars(strip_tags($this->lon));
      $this->pincode = htmlspecialchars(strip_tags($this->pincode));
      $this->address = htmlspecialchars(strip_tags($this->address));

      // Bind data
      $stmt-> bindParam(':country', $this->country);
      $stmt-> bindParam(':state', $this->state);
      $stmt-> bindParam(':district', $this->district);
      $stmt-> bindParam(':city', $this->city);
      $stmt-> bindParam(':suburb', $this->suburb);
      $stmt-> bindParam(':subarea', $this->subarea);
      $stmt-> bindParam(':subarea2', $this->subarea2);
      $stmt-> bindParam(':lat', $this->lat);
      $stmt-> bindParam(':lon', $this->lon);
      $stmt-> bindParam(':pincode', $this->pincode);
      $stmt-> bindParam(':address', $this->address);

      // Execute query
      if($stmt->execute()) {
        return true;
      }

      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);

      return false;
    }
    

    // Get Neighborhoods by latitude & longitude
    public function latlon() {
      // Create query
      $query = 'SELECT
        id,
        title,
        pincode,
        state,
        district,
        taluk,
        SQRT(POW(69.1 * (lat - :lat), 2) + POW(69.1 * ((:lon - lon) * COS(lat / 57.3)), 2)) AS distance
      FROM
        ' . $this->table . '
      HAVING distance < 10  ORDER BY distance limit 0,1';


      // Prepare statement
      $stmt = $this->conn->prepare($query);

      // Bind ID
     
      $stmt-> bindParam(':lat', $this->lat);
      $stmt-> bindParam(':lon', $this->lon);
      // Execute query
      $stmt->execute();

      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // set properties
      $this->id = $row['id'];
      $this->title = $row['title'];
      $this->pincode = $row['pincode'];
      $this->state = $row['state'];
      $this->district = $row['district'];
      $this->taluk = $row['taluk'];
    }

    // Get Single Users
    public function read_single(){
      //echo "From Neighbors read_single\n";
      // Create query
      $query = 'SELECT
          id,
          title,
          pincode,
          state,
          district,
          Lat,
          Lon
         FROM
            ' . $this->table . '
        WHERE id = ?
        LIMIT 0,1';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->nid);

        //echo "Nid here is :".$this->nid."\n";
        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set properties
        $this->nid = $row['id'];
        $this->title = $row['title'];
        $this->pincode = $row['pincode'];
        $this->state = $row['state'];
        $this->district = $row['district'];
        //$this->refcode = $row['Refcode'];
        $this->lat = $row['Lat'];
        $this->lon = $row['Lon'];
        //echo "lat here is :".$this->lat."\n";
        //echo "lon here is :".$this->lon."\n";

    }
    
    public function closest_neighbor_latlan(){
      //echo "From Neighbors closest_neighbor_latlan\n";
      // Create query
      $query = 'SELECT
          id,
          title,
          pincode,
          state,
          district,
          Lat,
          Lon
         FROM
            ' . $this->table . '
        WHERE pincode = ?
        AND Lat is not null LIMIT 0,1';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->pincode);

        //echo "Pincode here is :".$this->pincode."\n";
        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set properties
        $this->nid = $row['id'];
        $this->title = $row['title'];
        $this->pincode = $row['pincode'];
        $this->state = $row['state'];
        $this->district = $row['district'];
        //$this->refcode = $row['Refcode'];
        $this->lat = $row['Lat'];
        $this->lon = $row['Lon'];
        //echo "lat here is :".$this->lat."\n";
        //echo "lon here is :".$this->lon."\n";

    }
     // Add to favs
    public function addfavnbr(){
      // Create Query
      $query = 'INSERT IGNORE INTO UsrNeigh  SET UsrId = :uid,Nid    = :nid';
      
      // Prepare Statement
      $stmt = $this->conn->prepare($query);

      // Clean data
      //$this->uid = htmlspecialchars(strip_tags($this->uid));
      //$this->nid = htmlspecialchars(strip_tags($this->nid));

      // Bind data
      $stmt-> bindParam(':uid', $this->uid);
      $stmt-> bindParam(':nid', $this->nid);

      // Execute query
      if($stmt->execute()){
        return true;
      }

      // Print error if something goes wrong
      printf("Error: $s.\n", $stmt->error);

      return false;
    
     }

    public function valtoken() {
      
      // Create query
      $query = 'SELECT
          Id,
          PhoneId
         FROM
            Users
         WHERE Id = :uid
         AND PhoneId = :token';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(':uid', $this->uid);
        $stmt->bindParam(':token', $this->token);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        //echo "\n uid :".$this->uid;
        //echo "\n token :".$this->token;

        // set properties
        $this->id = $row['Id'];
        $this->phoneId = $row['PhoneId'];

        return $stmt;
    }

    public function listfavnbr() {
       // Create query
        $query = 'SELECT
          id,
          title,
          Lon,
          Lat
        FROM
          ' . $this->table . '
        WHERE id in (select Nid from UsrNeigh where UsrId = ?)';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->uid);

        // Execute query
        $stmt->execute();
        
        return $stmt;

    }

    // List members of the neighborhood.
    public function listnbrmem() {
       // Create query
        $query = 'SELECT Id, Name, Device, FcmToken from Users where Id in (SELECT
          UsrId
        FROM
          UsrNeigh
        WHERE Nid = :nid and UsrId != :uid)';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(':nid', $this->nid);
        $stmt->bindParam(':uid', $this->uid);

        // Execute query
        $stmt->execute();

        return $stmt;

    }
    //select nei_id,max(dou) from messages where nei_id in (select Nid from UsrNeigh where UsrId = 86) group by 1 order by 2 desc;
    // List neighborhoods with latest discussion.
    public function latestnbrdiscussion() {
       // Create query
        $query = 'SELECT nei_id,max(dou) from messages where nei_id in (SELECT
          Nid
        FROM
          UsrNeigh
        WHERE  UsrId = :uid) group by 1 order by 2 desc';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(':uid', $this->uid);

        // Execute query
        $stmt->execute();

        return $stmt;

    }

    public function getneighborhood() {
       // Create query
        $query = 'SELECT id,title,Lat,Lon 
        FROM
          pincodes
        WHERE  Id = :nid';

        // Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(':nid', $this->nid);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set properties
        $this->nid = $row['id'];
        $this->name = $row['title'];
        $this->longitude = $row['Lon'];
        $this->latitude = $row['Lat'];

    }
    // select Id,Name from Users where Id in (select UsrId from UsrNeigh where Nid=154818);
    // Get Neighborhood followers
    public function getnbrfollowers(){

      //echo "from getnbrfollowers\n"; 
      // Create query
      $query = 'SELECT
        Id,Name,StatusMsg,Status,Invisible,PicUrl,Activity 
      FROM
        Users
      WHERE
        Id in (select UsrId from UsrNeigh where Nid = :nid) and Invisible !=1';

      //echo "\n query : ".$query;
      
      
      // Prepare statement
      $stmt = $this->conn->prepare($query);
      //$this->touserid = htmlspecialchars(strip_tags($this->touserid));

      $stmt-> bindParam(':nid', $this->nid);
      //$stmt-> bindParam(':uid', $this->uid);
      
      //echo "\n nid : ".$this->nid;
      
      // Execute query
      $stmt->execute();

      return $stmt;
    }


}