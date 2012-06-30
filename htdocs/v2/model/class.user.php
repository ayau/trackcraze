<?php
 
/**
 * Controller for the model user
 *
 * PHP version 5
 *
 * @author Alex Yau
 * @copyright 
 * @license  
 *
 */

class User{

	private $_db;
 
    //Checks for a database object and creates one if none is found

    public function __construct($db=NULL)
    {
        if(is_object($db))
        {
            $this->_db = $db;
        }
        else
        {
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";port=".DB_PORT;
            $this->_db = new PDO($dsn, DB_USER, DB_PASS);
        }
    }

    //Sets the session for the user when 
    private function setSession($uid){
        $_SESSION['loggedin'] = true;
        $_SESSION['uid'] = $uid;
    }

    public function destroySession(){
        $_SESSION['loggedin'] = false;
        $_SESSION['uid'] = 0;
    }

    //Given a facebook graph, log the user in (if account exists) or sign user up
    public function facebook_login(){
        $data = $_POST['data'];
        $profile = json_decode($data, true);

        if($profile != NULL){
            $fid = $profile['id'];
            //return $fid;
            $id = $this -> getIdByFacebook($fid);
            if($id != 0){
                $this -> setSession($id);
                return true;
            }else{
                return $this -> facebookCreateAccount($profile);
            }
        }

        //set sessions.
        return false;
    }

    //Given a facebook graph, create a user model. Calls createProfile after.
    private function facebookCreateAccount($profile){

        $facebook_id = $profile['id'];
        $first_name = $profile['first_name'];
        $last_name = $profile['last_name'];
        $gender = $profile['gender'];
        $profile_pic = "https://graph.facebook.com/".$facebook_id."/picture?type=large";

        //Set current datetime
        date_default_timezone_set( 'Europe/London');
        $datetime = date("Y-m-d H:i:s", mktime());

        $vercode = hash('sha256', $datetime);

        $sql = "INSERT INTO users(first_name, last_name, vercode, facebook_id, created_at)
                VALUES(:fn, :ln, :ver, :fid, :time)";
            try{
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":fn", $first_name, PDO::PARAM_STR);
            $stmt->bindParam(":ln", $last_name, PDO::PARAM_STR);
            $stmt->bindParam(":ver",$vercode, PDO::PARAM_STR);
            $stmt->bindParam(":fid",$facebook_id, PDO::PARAM_INT);
            $stmt->bindParam(":time",$datetime,  PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();

            $uid = $this->_db->lastInsertId();
            
            if($uid != 0)
                return $this -> createProfile($uid, $gender, $profile_pic);
            else
                return false;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }
    }

    private function createProfile($uid, $gender = NULL, $profile_pic = NULL){

        if($gender == 'male')
            $gen = 0;
        else if ($gender == 'female')
            $gen = 1;
        else
            $gen = 2;

        $sql = "INSERT INTO profile(user_id, profile_pic, gender)
                VALUES(:uid, :pic, :gender)";
        try{
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":uid", $uid, PDO::PARAM_INT);
            $stmt->bindParam(":pic", $profile_pic, PDO::PARAM_STR);
            $stmt->bindParam(":gender",$gen, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();

            $this -> setSession($uid);
            return true;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }
    }

    //Given an email, check if user already exists
    private function checkExistByEmail($email){
        if($email != NULL){
            $sql = "SELECT id AS count
                    FROM users
                    WHERE email=:em";
            if($stmt = $this->_db->prepare($sql)) {
                $stmt->bindParam(":em", $email, PDO::PARAM_STR);
                $stmt->execute();
                if($row = $stmt->fetch()){
                    $stmt->closeCursor();
                    return true;
                }
                $stmt->closeCursor();
                return false;
            }
        }
        return false;
    }

    //Given a facebook_id, check if user already exists
    private function getIdByFacebook($facebook_id){
        if($facebook_id != NULL){
            $sql = "SELECT id
                    FROM users
                    WHERE facebook_id=:fid";
            if($stmt = $this->_db->prepare($sql)) {
                $stmt->bindParam(":fid", $facebook_id, PDO::PARAM_INT);
                $stmt->execute();
                if($row = $stmt->fetch()){
                    $stmt->closeCursor();
                    return $row['id'];
                }
                $stmt->closeCursor();
                return 0;
            }
        }
        return 0;
    }


    //Given a fb token or something, return me.
    public function getMe($facebook_id){
        $sql = "SELECT id, first_name, last_name, profile_pic, gender
                    FROM users
                    LEFT JOIN profile
                    ON users.id = profile.user_id
                    WHERE facebook_id=:fid";
            if($stmt = $this->_db->prepare($sql)) {
                $stmt->bindParam(":fid", $facebook_id, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt -> fetch();
                $stmt->closeCursor();
                return $row;
            }
    }
} 	
    	
    