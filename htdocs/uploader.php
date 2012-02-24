<?php
include_once "common/base.php"; 
include_once 'inc/class.news.inc.php';
if ((($_FILES["file"]["type"] == "image/gif")
|| ($_FILES["file"]["type"] == "image/jpeg")
|| ($_FILES["file"]["type"] == "image/pjpeg")
|| ($_FILES["file"]["type"]=="image/png"))
&& ($_FILES["file"]["size"] < 700000))
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
  else
    {
    echo "Upload: " . $_FILES["file"]["name"] . "<br />";
    echo "Type: " . $_FILES["file"]["type"] . "<br />";
    echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
    echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br />";
	
    $image =$_FILES["file"]["name"];
 	$uploadedfile = $_FILES['file']['tmp_name'];
    if($image){
    	$filename = stripslashes($_FILES['file']['name']);
        $extension = getExtension($filename);
  		$extension = strtolower($extension);
  		if($extension=="jpg" || $extension=="jpeg" ){
			$uploadedfile = $_FILES['file']['tmp_name'];
			$src = imagecreatefromjpeg($uploadedfile);
		}else if($extension=="png"){
			$uploadedfile = $_FILES['file']['tmp_name'];
			$src = imagecreatefrompng($uploadedfile);
		}else {
			$src = imagecreatefromgif($uploadedfile);
		}
 
		list($width,$height)=getimagesize($uploadedfile);
		if ($width > $height){ 
        	$newwidth=120; 
        	$newheight=($height/$width)*$newwidth; 
        	$tmp=imagecreatetruecolor($newwidth,$newheight); 
   		}else{ 
        	$newheight=120; 
        	$newwidth=($width/$height)*$newheight; 
        	$tmp=imagecreatetruecolor($newwidth,$newheight); 
    	} 
		

		imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,
 		$width,$height);
		$news = new GSNews($db);
		$lastid = $news->uploadPhoto($extension);
		$filename = "upload/".$lastid.".".$extension;
		imagejpeg($tmp,$filename,100);

		imagedestroy($src);
		imagedestroy($tmp);
	}
 	

    }
  }
else
  {
  echo "Invalid file";
  }
  function getExtension($str) {

         $i = strrpos($str,".");
         if (!$i) { return ""; } 
         $l = strlen($str) - $i;
         $ext = substr($str,$i+1,$l);
         return $ext;
 }
?>
