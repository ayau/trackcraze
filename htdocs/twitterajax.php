<?php
// Date function (this could be included in a seperate script to keep it clean)

// Work out the Date plus 8 hours
// get the current timestamp into an array
$timestamp = time();
$date_time_array = getdate($timestamp);
 
$hours = $date_time_array['hours'];
$minutes = $date_time_array['minutes'];
$seconds = $date_time_array['seconds'];
$month = $date_time_array['mon'];
$day = $date_time_array['mday'];
$year = $date_time_array['year'];
 
// use mktime to recreate the unix timestamp
// adding 19 hours to $hours
$timestamp = mktime($hours + 0,$minutes,$seconds,$month,$day,$year);
$theDate = strftime('%Y-%m-%d %H:%M:%S',$timestamp);    

// END DATE FUNCTION

$count = 0;
$num_tweets = array();
$timestamp_H = floor($timestamp/3600)*3600;
$hour = strftime("%H",$timestamp_H);
if($hour ==23){
	$hour_next = 0;
}else{
	$hour_next = $hour+1;
}
$hour_toString = strftime('%m-%d %H',$timestamp_H)."-".$hour_next;
 
//Search API Script
 
$q=$_GET['q'];
 
if($_GET['q']==''){
 
$q = 'nyan cat';}

$q = urlencode($q); 

echo "<h3>Twitter search results for '".$q."'</h3>";
echo "<div>".$hour_toString."</div>";
for($i=1;$i<=15;$i++){
	
	$search = "http://search.twitter.com/search.atom?q=".$q."&page=".$i."&rpp=100";
 
$tw = curl_init();

curl_setopt($tw, CURLOPT_URL, $search);
curl_setopt($tw, CURLOPT_RETURNTRANSFER, TRUE);
$twi = curl_exec($tw);

$search_res = new SimpleXMLElement($twi);
 
## Echo the Search Data

foreach ($search_res->entry as $twit1) {
 
$description = $twit1->content;
 
$description = preg_replace("#(^|[\n ])@([^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://www.twitter.com/\\2\" >@\\2</a>'", $description);
$description = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a href=\"\\2\" >\\2</a>'", $description);
$description = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "'\\1<a href=\"http://\\2\" >\\2</a>'", $description);
 
$retweet = strip_tags($description);
 
$date =  strtotime($twit1->updated);
$dayMonth = date('d M', $date);
$year = date('y', $date);
$message = $row['content'];
//$datediff = date_difference($theDate, $date);
if($date>=$timestamp_H){
	$count++;
}else{
	$num_tweets[$hour_toString] = $count;
	echo $count;
	$count = 0;
	$timestamp_H = $timestamp_H-3600;
	$hour = strftime("%H",$timestamp_H);
	if($hour ==23){
		$hour_next = 0;
	}else{
		$hour_next = $hour+1;
	}
	$hour_toString = strftime('%m-%d %H',$timestamp_H)."-".$hour_next;
	echo "<div>".$hour_toString."</div>";
}
 
//echo "<div class='user'><a href=\"",$twit1->author->uri,"\" target=\"_blank\"><img border=\"0\" width=\"48\" class=\"twitter_thumb\" src=\"",$twit1->link[1]->attributes()->href,"\" title=\"", $twit1->author->name, "\" /></a>\n";
//echo "<div class='text'>".$description."<div class='description'>From: ", $twit1->author->name," <a href='http://twitter.com/home?status=RT: ".$retweet."' target='_blank'>Retweet!</a></div><strong>".$datediff."</strong></div><div class='clear'></div></div>";
 
}
}
echo $count;
curl_close($tw);

?>