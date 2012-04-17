<?php

/**
 * Handles user interactions within the app
 *
 * PHP version 5
 *
 * @author Alex Yau
 * @author Timothy Tse
 * @copyright 
 * @license  
 *
 */

 class GSNews
{
    /**
     * The database object
     *
     * @var object
     */
    private $_db;
 
    /**
     * Checks for a database object and creates one if none is found
     *
     * @param object $db
     * @return void
     */
    public function __construct($db=NULL)
    {
        if(is_object($db))
        {
            $this->_db = $db;
        }
        else
        {
            $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
            $this->_db = new PDO($dsn, DB_USER, DB_PASS);
        }
    } 
	function getNews(){
		$news = new GSNews();
		$news->getTR();		
		$string = $news->getTracking();
		$time = $news->getLastNewsVisit();
		$news->getPostOnBoard($time);
		$news->getnewsContent($string);
	}
	function getMiniNews(){
		$news = new GSNews();
		$news->getMiniTR();
		$string = $news->getTracking();
		$time = $news->getLastNewsVisit();
		if ($string=="UserID="){
			echo "Seems like you have no friends.";
		}else{
		$post = $news->getMiniPost($string, $time);
		$record = $news->getMiniRecord($string, $time);
		$contact = $news->getMiniContact($string, $time);
		$gender = $news->getMiniGender($string, $time);
		if(!$post && !$record && !$contact && !$gender){
			echo "<br /><br />Nothing much has happened since you last logged in.<br/><br />Consider getting more friends";
		}
	}
	}
	
	//Sex: 0 - Male, 1 - Female, 2 - Undisclosed
	//Arguments - 0: his/her, 1: His/Her 2: he/she 3: He/She
	function getSex($UID, $word){
		
		$sql = "SELECT
					Gender
				FROM profile
				WHERE UserID=:uid";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			if($row = $stmt->fetch()){
				switch($row['Gender']){
					case 0:
						switch($word){
							case 0:
								return "his";
							break;
							case 1:
								return "His";
							break;
							case 2:
								return "he";
							break;
							case 3:
								return "He";
							break;
							case 4:
								return "him";
							break;
						}
					break;
					case 1:
						switch($word){
							case 0:
								return "her";
							break;
							case 1:
								return "Her";
							break;
							case 2:
								return "she";
							break;
							case 3:
								return "She";
							break;
							case 4:
								return "her";
							break;
						}
					break;
					case 2:
						switch($word){
							case 0:
								return "his/her";
							break;
							case 1:
								return "His/Her";
							break;
							case 2:
								return "he/she";
							break;
							case 3:
								return "He/She";
							break;
							case 4:
								return "him/her";
							break;
						}
					break;
				}	
			}
			$stmt->closeCursor();
			return $string;
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function getTracking(){
		$sql = "SELECT
					relationship.Trackee
				FROM relationship
				WHERE Tracker=:uid
				AND Verified=1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->execute();
			$string="UserID=";
			$row = $stmt->fetch();
			$string = $string.$row['Trackee'];
			while($row = $stmt->fetch())
			{
				$string = $string." OR UserID=".$row['Trackee'];	
			}
			$stmt->closeCursor();
			return $string;
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function getMiniRecord($string, $time){
		$sql = "SELECT
					COUNT(DISTINCT UserID) AS count
				FROM news
				WHERE newsType=4
				AND DATE_FORMAT(newsTime, '%Y-%m-%d %H:%i:%s')>=:time AND (".$string.")";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':time', $time, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			if($row['count']==1){
				echo "<div class='mininews'>".$row['count']." person has worked out since you last visited.</div>";
				return true;
			}else if($row['count']>1){
				echo "<div class='mininews'>".$row['count']." people have worked out since you last visited.</div>";
				return true;
			}else return false;
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	
	function getMiniContact($string, $time){
		$sql = "SELECT
					COUNT(DISTINCT UserID) AS count
				FROM news
				WHERE (newsType=9
				OR newsType =10
				OR newsType =11) 
				AND DATE_FORMAT(newsTime, '%Y-%m-%d %H:%i:%s')>=:time AND (".$string.")";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':time', $time, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			if($row['count']==1){
				echo "<div class='mininews'>Looks like ".$row['count']." person has updated their contact information.</div>";
				return true;
			}else if($row['count']>1){
				echo "<div class='mininews'>Looks like ".$row['count']." people have updated their contact information.</div>";
				return true;
			}else return false;
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function getMiniGender($string, $time){
		$sql = "SELECT
					COUNT(DISTINCT UserID) AS count
				FROM news
				WHERE newsType=15 
				AND DATE_FORMAT(newsTime, '%Y-%m-%d %H:%i:%s')>=:time AND (".$string.")";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':time', $time, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			if($row['count']==1){
				echo "<div class='mininews'>Looks like ".$row['count']." person has changed their sex.</div>";
				return true;
			}else if($row['count']>1){
				echo "<div class='mininews'>Looks like ".$row['count']." people have changed their sex.</div>";
				return true;
			}else return false;
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function getMiniPost($string, $time){
		$count=0;
		$sql = "SELECT
			newsTime, newsContent	
			FROM news
			WHERE UserID=:uid AND newsType=0 AND DATE_FORMAT(newsTime, '%Y-%m-%d %H:%i:%s')>=:time ORDER BY newsTime DESC";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->bindParam(':time', $time, PDO::PARAM_STR);
			$stmt->execute();
		
			while($row = $stmt->fetch()){
				$agotext = "<p class='agotext inline'>".$this->getTimeDiff($row["newsTime"])."</p>";
				$sql = "SELECT
					UserID, PostBy, PostText
							FROM posts
							WHERE PostID=:pid
								AND PostBy<>:uid
							LIMIT 1";
						if($stmt = $this->_db->prepare($sql))
						{
							$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
							$stmt->bindParam(':pid', $row['newsContent'], PDO::PARAM_INT);
							$stmt->execute();
							$news = new GSNews();
							if($row = $stmt->fetch()){
								$count++;
							}
						}
					}
				}

			if ($count<=3){
				$easteregg="";
			}else if ($count<=6){
				$easteregg="Look who's so popular!";
			} else if ($count<=10){
				$easteregg="You should try out for the Mr/Ms Popular next year.";
			} else {
				$easteregg="Either you're the most popular person in the world, or someone's spamming your board.";
			}
			if($count==1){
			echo "<div class='mininews'>Looks like you have ".$count." new note on your board. ".$easteregg."</div>";
			return true;
			}else if($count>1){
			echo "<div class='mininews'>Looks like you have ".$count." new notes on your board. ".$easteregg."</div>";
			return true;
			}else return false;
			$stmt->closeCursor();
	}
	function getMiniTR(){
		$sql = "SELECT COUNT(*) AS count
				FROM relationship
				WHERE Trackee=:uid
				AND Verified=0";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->execute();
			if($row = $stmt->fetch()){
				echo "<div>You have ".$row['count']." track requests.</div>";
			}
			$stmt->closeCursor();
			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function getTR(){
		$sql = "SELECT
					relationship.Tracker, Trackee, Verified
				FROM relationship
				WHERE Trackee=:uid
				AND Verified=0";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->execute();
			$news = new GSNews();
			if($row = $stmt->fetch()){
			$name = $news->getName($row['Tracker']);
			echo "<div id='TR'><div id='newsheader'>Look who is so popular!! (Track Request)</div>";
			echo "<div class=\"".$row['Tracker']."tag\">".$name." wants to track your progress. <div class=".$row['Tracker']."><a class=\"acceptTR\">Accept</a> <a class=\"ignoreTR\">Ignore</a></div></div>";
			
			while($row = $stmt->fetch())
			{
				$name = $news->getName($row['Tracker']);
				echo "<div class=\"".$row['Tracker']."tag\">".$name." wants to track your progress. <div class=".$row['Tracker']."><a class=\"acceptTR\">Accept</a> <a class=\"ignoreTR\">Ignore</a></div></div>";
			}
			echo "</div>";
		}
			$stmt->closeCursor();
			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function getTimeDiff($newsTime){
		$ago = time()-strtotime($newsTime);
				if ($ago<60){
					$agotext=$ago." seconds ago";
				} else if ($ago<3600){
					$agotext= floor($ago/60)." minutes ago";
				} else if ($ago<86400){
					$agotext= floor($ago/3600)." hours ago";
				} else if ($ago<604800){
					$agotext=floor($ago/86400)." days ago";
				} else if ($ago<2678400){
					$agotext=floor($ago/604800)." weeks ago";
				} else if ($ago<31536000){
					$agotext=floor($ago/2678400)." months ago";
				} else {
					$agotext=floor($ago/31536000)." years ago";
				}
			return $agotext;
	}
	
	function getPostOnBoard($time){
		
		$sql = "SELECT
			newsTime, newsContent	
			FROM news
			WHERE UserID=:uid AND newsType=0 AND DATE_FORMAT(newsTime, '%Y-%m-%d %H:%i:%s')>=:time ORDER BY newsTime DESC";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->bindParam(':time', $time, PDO::PARAM_STR);
			$stmt->execute();
		
			while($row = $stmt->fetch()){
				$agotext = "<p class='agotext inline'>".$this->getTimeDiff($row["newsTime"])."</p>";
				$sql = "SELECT
					UserID, PostBy, PostText
							FROM posts
							WHERE PostID=:pid
								AND PostBy<>:uid
							LIMIT 1";
						if($stmt = $this->_db->prepare($sql))
						{
							$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
							$stmt->bindParam(':pid', $row['newsContent'], PDO::PARAM_INT);
							$stmt->execute();
							$news = new GSNews();
							if($row = $stmt->fetch()){
								$name = $news->getName($row['PostBy'])." has";								
								$name1 = "<a class='link' href=\"/board.php?user=".$_SESSION['UserID']."\">your</a>";
						
								echo "<div class='story'>";
										$rand = rand (0,2);
										switch($rand){
											case 0:
											echo "<h3>New Post bitches!</h3>";
											break;
											case 1:
											echo "<h3>You so Popular!</h3>";
											break;
											case 2:
											echo "<h3>Someone left a message for you</h3>";
											break;
										}
									echo "<div class='postBox'>".$name." put a note on ".$name1." board:<div class='posttext newsPostText'>".$row['PostText']."</div>".$agotext."</div></div>";

							}
							$stmt->closeCursor();
						}
						else
						{
							echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
						}
					}
					}
						else
						{
							echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
						}
	}
	
	
	function getnewsContent($string){
		//$string = Either about you or who you're tracking

		$sql = "SELECT
				UserID, newsTime, newsType, newsContent	
				FROM news
					WHERE ".$string." ORDER BY newsTime DESC LIMIT 50";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->execute();
			$news = new GSNews();
			While($row = $stmt->fetch()){
				$name = $news->getName($row['UserID']);
				$his = $news->getSex($row['UserID'],0);
				$he = $news->getSex($row['UserID'],2);
				$Uhis = $news->getSex($row['UserID'],1);
				$Uhe = $news->getSex($row['UserID'],3);
				$him = $news->getSex($row['UserID'],4);
				$agotext = "<p class='agotext inline'>".$news->getTimeDiff($row["newsTime"])."</p>";
				switch($row['newsType']){
        			case '0':
        			echo $news->getnewspost($row['newsContent'], $agotext);
        			break;
        			case '1':
        			echo "<div>Massive</div>";
        			break;
        			case '2':
        			echo "<div>Loss</div>";
        			break;
        			case '3':
        			echo "<div>ProgramUpdate</div>";
        			break;
        			case '4':
        			echo "<div class='shortStory'>".$name." has just worked out. <a class='link' href=\"/progress.php?user=".$row['UserID']."&view=track&date=".$row['newsContent']."\">Check out what ".$he." did!</a> ".$agotext."</div>";
        			break;
        			case '5':
        			$program=$news->getprogramName($row['newsContent']);
        			echo "<div class='shortStory'>".$name." has changed ".$his." game plan. ".$Uhe." is now working out with the program: ".$program." ".$agotext."</div>";//check if program is changed!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        			break;
        			case '6':
        			$program=$news->getprogramName($row['newsContent']);
        			echo "<div class='shortStory'>".$name." is trying out something new. ".$Uhe." has just created the program: ".$program." ".$agotext."</div>";//GET THEIR SEX?
        			break;
        			case '7':
        			echo "<div class='shortStory'>".$name." has just achieved ".$his." goal of <u>".$row['newsContent']."</u> Congratulate ".$him."!!".$agotext."</div>";
        			break;
        			case '8':
        			echo "<div>Goal Change</div>";
        			break;
        			case '9':
        			echo "<div class='shortStory'>".$name." changed ".$his." location to <u>".$row['newsContent']."</u>. ".$agotext."</div>";
        			break;
					case '10':
        			echo "<div class='shortStory'>".$name." changed ".$his." email address to <u>".$row['newsContent']."</u>. ".$agotext."</div>";
        			break;
        			case '11':
        			echo "<div class='shortStory'>".$name." changed ".$his." phone number to <u>".$row['newsContent']."</u>. ".$agotext."</div>";
        			break;
        			case '12':
        			echo "<div class='shortStory'>".$name." is now ".$row['newsContent']."cm tall!! Congratulations. ".$agotext."</div>";
        			break;
        			case '13':
        			echo "<div>Sportsadd</div>";
        			break;
        			case '14':
        			echo "<div>SportsRemove</div>";
        			break;
        			case '15':
        			if ($row['newsContent']==2){
        				$gendertext=" doesn't want people to know his/her sex.";
        			}else if ($row['newsContent']==0){
        				$gendertext=' is now male.';
        			}else{
        				$gendertext=' is now female.';
        			}
        			echo "<div class='shortStory'>".$name.$gendertext." ".$agotext."</div>";
        			break;
        			case '20':
        				$trackee = $news->getName($row['newsContent']);
        				echo "<div class='shortStory'>".$name." is now tracking ".$trackee."</div>";
        			break;
			}
		}
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function getprogramName($PID){
		$sql = "SELECT
					lists.ProgramName
				FROM lists
				WHERE ProgramID=:pid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
			$stmt->execute();

			$row = $stmt->fetch();
			return "<a class='link' href=\"/program.php?program=".$PID."\">".$row["ProgramName"]."</a>";
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	
	//only for when other people update status or post on each other's walls. Not for posts on your own wall
	function getnewspost($nc, $agotext){
		$sql = "SELECT
						posts.UserID, PostBy, PostText
							FROM posts
							WHERE PostID=:pid
								AND PostBy<>:uid
							LIMIT 1";
						if($stmt = $this->_db->prepare($sql))
						{
							$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
							$stmt->bindParam(':pid', $nc, PDO::PARAM_INT);
							$stmt->execute();
							$news = new GSNews();
							if($row = $stmt->fetch()){
								
								$name = $news->getName($row['PostBy']);
								//if($row['PostBy']!=$_SESSION['UserID']){
								//post on his own wall
								if($row['UserID']==$row['PostBy']){
									echo "<div class='story'>";
									echo "<div class='postBox'>".$name." wants the whole world to know that:<div class='posttext newsPostText'>".$row['PostText']."</div>".$agotext."</div>";	
									echo "</div>";
								}else{
									$name = $name." has";
									if ($row['UserID']==$_SESSION['UserID']){
										$name1 = "<a class='link' href=\"/board.php?user=".$_SESSION['UserID']."\">your</a>";
									}else{
										$name1 = ($news->getName($row['UserID']))."'s";
									}
									if ($row['PostBy']==$_SESSION['UserID']){
										$name = "You have";
									}//USELESS? SINCE YOU NEVER TRACK YOURSELF							
									echo "<div class='story'>";
									if($row['UserID']==$_SESSION['UserID']){
										$rand = rand (0,2);
										switch($rand){
											case 0:
											echo "<h3>New Post bitches!</h3>";
											break;
											case 1:
											echo "<h3>You so Popular!</h3>";
											break;
											case 2:
											echo "<h3>Someone left a message for you</h3>";
											break;
										}
									}
									echo "<div class='postBox'>".$name." put a note on ".$name1." board:<div class='posttext newsPostText'>".$row['PostText']."</div>".$agotext."</div></div>";
								}
							}
						//}
							$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
						}
						else
						{
							echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
						}
	}
	function getName($id){
		$sql = "SELECT
					profile.Surname, Forename
				FROM profile
				WHERE UserID=:id
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':id', $id, PDO::PARAM_INT);
			$stmt->execute();

			$row = $stmt->fetch();
			return "<a class='link' href=\"/board.php?user=".$id."\">".$row["Forename"]." ".$row["Surname"]."</a>";
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function acceptTR(){
		$UID = $_POST['UID'];
		$sql = "UPDATE relationship
 					SET Verified =1
	 				WHERE Trackee=:sid
		 			AND Tracker=:uid
		            LIMIT 1"; 
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':sid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
			$news = new GSNews();
			echo $news->getName($UID);
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}		
	}
	function ignoreTR(){
		$UID = $_POST['UID'];
		$sql = "DELETE FROM relationship
                WHERE Tracker=:uid
		 		AND Trackee=:sid
		        LIMIT 1"; 
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':sid', $_SESSION['UserID'], PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            $news = new GSNews();
			echo $news->getName($UID);
        }
                catch(Exception $e)
        {
            return $e->getMessage();
        }	
	}
	function addPost(){
		$UID=$_POST['get'];
		$Post = strip_tags(urldecode(trim($_POST['post'])), WHITELIST);
		$Postby=$_SESSION['UserID'];
		$time = date("Y-m-d H:i:s");
		$sql = "INSERT INTO posts
 		(UserID, PostBy, PostText, PostTime)
 	VALUES (:uid, :pb, :text, :time)";
 		try
	 		{
		 		$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
				$stmt->bindParam(':pb', $Postby, PDO::PARAM_INT);
				$stmt->bindParam(':time', $time, PDO::PARAM_STR);
				$stmt->bindParam(':text', $Post, PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor();
				$lastid =$this->_db->lastInsertId();
				$news = new GSNews();
				$profilepic = $news->getProfilePic($Postby);
				$name = $news->getName($Postby);
				$agotext = $news->getTimeDiff($time);				
				echo $lastid.",".$name.",".$profilepic.",".$agotext;
			 }
	 		catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}
	function getPosts($UID,$page){
		$page=$page*10;
		$sql = "SELECT PostID, UserID, PostBy, PostText, PostTime
				FROM posts
				WHERE UserID=:uid
				ORDER BY PostTime DESC
				LIMIT $page,10";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			$news = new GSNews();
			while($row = $stmt->fetch()){
				$name = $news->getName($row['PostBy']);
				$profilepic = $news->getProfilePic($row['PostBy']);
				$agotext = $news->getTimeDiff($row["PostTime"]);
				if($_SESSION['UserID']==$UID||$_SESSION['UserID']==$row["PostBy"]){
					$deletetext="<div class='delete sp' hidden></div>";
				}else{
					$deletetext="";
				}
				echo "<div id=".$row['PostID']." class='postcontent'>".$deletetext."<div class='postHeader'><div class='miniphoto'>".$profilepic."</div><div class='postname'>".$name."</div><div class='posttext'>".$row['PostText']."<p class='agotext'>".$agotext."</p></div>";
				$news->checkkudos($row['PostID'],0,$row['PostBy']);
				echo " <a class='postcomment'>comment</a></div><div class='kudos'>";
				$news->getkudos($row['PostID'],0);
				echo "</div><div class='comments'>";
				$news->getComments($row['PostID'],$row['PostBy'],$UID);
				echo "</div><div class='break'></div></div>";
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function checkkudos($PID, $type, $UID){
		$sql = "SELECT COUNT(*) AS count
				FROM kudos
				WHERE PostID=:pid
					AND KudosType=:type
					AND UserID=:uid";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
			$stmt->bindParam(':type', $type, PDO::PARAM_INT);
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			if ($row['count']==0 && $UID!=$_SESSION['UserID']){
				echo "<a class='kudos2u'>Kudos2u</a>";
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function getkudos($PID, $type){
		$sql = "SELECT COUNT(*) AS count
				FROM kudos
				WHERE PostID=:pid
					AND KudosType=:type";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
			$stmt->bindParam(':type', $type, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			if ($row['count']!=0){
				echo "<div class='kudostext'>Kudos to you Sir!<div class='kudoscount'>x".$row['count']."</div></div>";
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function getComments($PID, $postby, $UID){
		$sql = "SELECT COUNT(*) AS count
				FROM comments
				WHERE PostID=:pid
					AND CommentType=0";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$expandcount=$row['count']-3;
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		$sql = "SELECT CommentID, CommentText, UserID, CommentDate
				FROM comments
				WHERE PostID=:pid
					AND CommentType=0
				ORDER BY CommentDate ASC
				LIMIT 5";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
			$stmt->execute();
			if($expandcount<=1){
				while($row = $stmt->fetch()){
					echo $this->formatcomments($row,$postby);
				}
			}else{
				$row = $stmt->fetch();
				echo $this->formatcomments($row,$postby);
				$row = $stmt->fetch();
				echo $this->formatcomments($row,$postby);
				echo "<div class='commentitem pointer expandcomment' expand='".$expandcount."' postby='".$postby."'><div class='commentbreak sp'></div></div>";
						$sql = "SELECT CommentID, CommentText, UserID, CommentDate
								FROM comments
								WHERE PostID=:pid
								AND CommentType=0
								ORDER BY CommentDate DESC
								LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			echo $this->formatcomments($row,$postby);
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
					}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function expandcomments(){
		$PID=$_POST['pid'];
		$pby=$_POST['pby'];
		$ecount=$_POST['ecount'];
		$sql = "SELECT CommentID, CommentText, UserID, CommentDate
					FROM comments
					WHERE PostID=:pid
					AND CommentType=0
					ORDER BY CommentDate ASC
						LIMIT ".$ecount;
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$row = $stmt->fetch();
			while($row = $stmt->fetch()){
				echo $this->formatcomments($row,$pby);
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function formatcomments($row,$postby){
		$news = new GSNews();
		$name = $news->getName($row['UserID']);
				//$profilepic = $news->getProfilePic($row['PostBy']);
				$agotext = $news->getTimeDiff($row["CommentDate"]);
				if ($_SESSION['UserID']==$row['UserID']||$_SESSION['UserID']==$postby||$_SESSION['UserID']==$UID){
					$deletetext = "<div class='delete sp' hidden></div>";
				}else{
					$deletetext="";
				}
				return "<div id=\"".$row['CommentID']."comment\" class='commentitem'>".$deletetext.$name."<p class='commenttext'>".$row['CommentText']."</p><p class='agotext'>".$agotext."</p></div>";
	}
	function deletePost(){
		$PID = $_POST['pid'];
		$UID = $_SESSION['UserID'];
		$sql = "DELETE FROM posts
                WHERE PostID=:pid
			AND (UserID=:uid OR PostBy=:uid)
		        LIMIT 1"; 
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':pid',$PID, PDO::PARAM_INT);
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            $sql = "DELETE FROM comments
                WHERE PostID=:pid
                AND CommentType=0"; 
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':pid',$PID, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            
        }
                catch(Exception $e)
        {
            return $e->getMessage();
        }
         $sql = "DELETE FROM news
                WHERE newsContent=:pid
                AND newsType=0"; 
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':pid',$PID, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
            
        }
                catch(Exception $e)
        {
            return $e->getMessage();
        }		
        }
                catch(Exception $e)
        {
            return $e->getMessage();
        }	
	}
	function loadprogramComments($PID, $type, $bool){
		$sql = "SELECT CommentID, CommentText, UserID, CommentDate
				FROM comments
				WHERE PostID=:pid
					AND CommentType=:type
				ORDER BY CommentDate DESC";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
			$stmt->bindParam(':type', $type, PDO::PARAM_INT);
			$stmt->execute();
			$news = new GSNews();
			while($row = $stmt->fetch()){
				$name = $news->getName($row['UserID']);
				//$profilepic = $news->getProfilePic($row['PostBy']);
				$agotext = $news->getTimeDiff($row["CommentDate"]);
				if ($row['UserID']==$_SESSION['UserID'] || $bool==true){
					$deletetext="<div class='delete sp' hidden></div>";
				}else{
					$deletetext="";
				}
				echo "<div id=\"".$row['CommentID']."comment\">".$deletetext.$name."<p class='commenttext'>".$row['CommentText']."</p><p class='agotext'>".$agotext."</p></div>";
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		
	}
	function addComment(){
		$UID = $_SESSION['UserID'];
		$type = $_POST['type'];
		$text = strip_tags(urldecode(trim($_POST['text'])), WHITELIST);
		$PID = $_POST['pid'];
		$time = date("Y-m-d H:i:s");
 		$sql = "INSERT INTO comments
 		(PostID, CommentText, UserID, CommentType, CommentDate)
 		VALUES (:pid, :text, :uid, :type, :time)";
 		try
	 		{
		 		$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
				$stmt->bindParam(':time', $time, PDO::PARAM_STR);
				$stmt->bindParam(':text', $text, PDO::PARAM_STR);
				$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
				$stmt->bindParam(':type', $type, PDO::PARAM_INT);
				$stmt->execute();
				$lastid = $this->_db->lastInsertId();
				$news = new GSNews();
				$name = $news->getName($UID);				
				echo "<div id=\"".$lastid."comment\" class='commentitem'><div class='delete sp' hidden></div>".$name."<p class='commenttext'>".$text."</p><p class='agotext'>0 seconds ago</p></div>";
				$stmt->closeCursor();
			 }
	 		catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}
	function deleteComment(){
		$CID = $_POST['cid'];
		$type = $_POST['type'];
		$sql = "DELETE FROM comments
                WHERE CommentID=:cid
				AND CommentType=:type
		        LIMIT 1"; 
        try
        {
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':cid',$CID, PDO::PARAM_INT);
			$stmt->bindParam(':type', $type, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
        }
                catch(Exception $e)
        {
            return $e->getMessage();
        }	
	}
	function givekudos(){
		$PID = $_POST['pid'];
		$type = $_POST['type'];
		$sql = "INSERT INTO kudos
 		(PostID, UserID, KudosType)
 		VALUES (:pid, :uid, :type)";
 		try
	 		{
		 		$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
				$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
				$stmt->bindParam(':type', $type, PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
			 }
	 		catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}
	function uploadPhoto($extension){
		$time = date("Y-m-d H:i:s");
 		$sql = "INSERT INTO photos
 		(UserID, photoDate, extension)
 		VALUES (:uid, :time, :extension)";
 		try
	 		{
		 		$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
				$stmt->bindParam(':time', $time, PDO::PARAM_STR);
				$stmt->bindParam(':extension', $extension, PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor();
				$lastid = $this->_db->lastInsertId();
				$news = new GSNews();
				$news->changeProfilePic($lastid);
				return $lastid;
			 }
	 		catch(PDOException $e)
		{
			return $e->getMessage();
		}
	}
	function checkProfilePic(){
		$PID = $_POST['pid'];
		$sql = "SELECT PhotoID
				FROM photos
				WHERE UserID=:uid
					AND PhotoID=:pid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->bindParam(':pid', $PID, PDO::PARAM_INT);
			$stmt->execute();
			while($row = $stmt->fetch()){
				$news = new GSNews();
				$news->changeProfilePic($PID);
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function changeProfilePic($pid){
		$sql = "UPDATE profile
			 	SET profilepic =:pid
				 WHERE UserID=:uid
		            LIMIT 1"; 
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->bindParam(':pid', $pid, PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}		
	}
	function getPhotos($UID){
		$sql = "SELECT PhotoID, photoDate, extension
				FROM photos
				WHERE UserID=:uid
				ORDER BY photoDate DESC";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			while($row = $stmt->fetch()){
				echo "<div class='photo'><img src=\"/upload/".$row['PhotoID'].".".$row['extension']."\" /></div>";
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	function getchoosePhotos($UID){
		$sql = "SELECT PhotoID, photoDate, extension
				FROM photos
				WHERE UserID=:uid
				ORDER BY photoDate DESC";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			while($row = $stmt->fetch()){
				echo "<div class='photo' id=\"".$row['PhotoID']."\"><img src=\"/upload/".$row['PhotoID'].".".$row['extension']."\" /></div>";
			}
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
	public function getProfilePic($UID){
 	$sql = "SELECT profilepic
				FROM profile
				WHERE UserID=:uid
				LIMIT 1";
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			$sql = "SELECT PhotoID, extension
				FROM photos
					WHERE PhotoID=:pid
						AND UserID=:uid
				LIMIT 1";
			if($row['profilepic']==NULL){
					return "<a href=\"/board.php?user=".$UID."\" ><img src='/images/profilepic.png' width='120' height='120'/></a>";
				}else{
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $UID, PDO::PARAM_INT);
			$stmt->bindParam(':pid', $row['profilepic'], PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
				return "<a href=\"/board.php?user=".$UID."\" ><img src=\"/upload/".$row['PhotoID'].".".$row['extension']."\" /></a>"; //CHECK IF PIC EXIST IN FOLDER WHAT IF WE MANUALLY DELETED THEIR PIC OR SOME SERVER ERROR
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
		}	
			$stmt->closeCursor();

			// If there aren't any list items saved, no list ID is returned
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
 }
 	function updateLastVisit(){
		$sql = "UPDATE users
			 	SET lastNewsVisit =  CURRENT_TIMESTAMP
				 WHERE UserID=:uid
		            LIMIT 1"; 
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}		
	}
	
	function getLastNewsVisit(){
		$sql = "SELECT
					lastNewsVisit
				FROM users
				WHERE UserID=:uid
		            LIMIT 1"; 
		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':uid', $_SESSION['UserID'], PDO::PARAM_INT);
			$stmt->execute();
			$row = $stmt->fetch();
			return $row['lastNewsVisit'];
			$stmt->closeCursor();
		}
		else
		{
			echo "\t\t\t\t<li> Something went wrong. ", $db->errorInfo, "</li>\n";
		}
	}
 
}