<?php
 
/**
 * Controller for the model program
 *
 * PHP version 5
 *
 * @author Alex Yau
 * @copyright 
 * @license  
 *
 */

class Program{

	public $_db;
 
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


    public function createProgram(){
        $uid = $_SESSION['uid'];
        $name = $_POST['name'];
        $name = preg_replace( '/\n|\r/', ' ', $name);
        $name = preg_replace('/\s+/',' ',$name);
        if(strlen($name)<1)
            return;
        
        $name = ucfirst($name); //uppercase the first character

        //Set current datetime
        date_default_timezone_set( 'Europe/London');
        $datetime = date("Y-m-d H:i:s", mktime());

        $sql = "INSERT INTO programs(user_id, name, created_at)
                VALUES(:uid, :name, :time)";
        try{
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(":uid", $uid, PDO::PARAM_INT);
            $stmt->bindParam(":name", $name, PDO::PARAM_STR);
            $stmt->bindParam(":time",$datetime, PDO::PARAM_STR);
            $stmt->execute();
            $stmt->closeCursor();
            
            $pid = $this->_db->lastInsertId();
            return $pid;
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
            return false;
        }
    }

    public function getPrograms($uid){
        $sql = "SELECT *
                FROM programs
                WHERE user_id=:uid
                ORDER BY name ASC";
            if($stmt = $this->_db->prepare($sql)) {
                $stmt->bindParam(":uid", $uid, PDO::PARAM_INT);
                $stmt->execute();
                $programs = array();
                while($row = $stmt -> fetch())
                    array_push($programs, $row);
                $stmt->closeCursor();
                return $programs;
            }
    }

    public function getProgram($pid){
        $sql = "SELECT *
                FROM programs
                WHERE id=:pid
                LIMIT 1";
            if($stmt = $this->_db->prepare($sql)) {
                $stmt->bindParam(":pid", $pid, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt -> fetch();
                return $row;
            }
    }

    public function getOwner($pid){
        $sql = "SELECT user_id
                FROM programs
                WHERE id=:pid
                LIMIT 1";
            if($stmt = $this->_db->prepare($sql)) {
                $stmt->bindParam(":pid", $pid, PDO::PARAM_INT);
                $stmt->execute();
                $row = $stmt -> fetch();
                return $row['user_id'];
            }
    }

    public function deleteProgram(){
        $pid = $_POST['pid'];
        $uid = $_SESSION['uid'];

        if($this -> getOwner($pid) == $uid){
            //get main program from user
            if($pid == User::getMainProgram($uid)){
                //if so, update mainprogram and set it to 0
                User::setMainProgram(0);
            }
            $sql = "DELETE FROM programs
                WHERE id=:pid
                AND user_id=:uid
                LIMIT 1";
            try
            {
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
                $stmt->bindParam(':pid', $pid, PDO::PARAM_INT);
                $stmt->execute();
                $stmt->closeCursor();
                return true;
            }catch(Exception $e){
                return $e->getMessage();
            }
        }
    }

}