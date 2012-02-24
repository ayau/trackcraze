<div id="search">
<form action="" method="get">

  Search twitter
  <input type="text" name="q" id="searchbox" />
  <input type="submit" name="submit" id="submit" value="Search" />
  Up to 
  <input type="text" name="days" id="days"/>
  of days
</form>
</div>
<div id='twitterDataChart'></div>
<script type="text/javascript" src="js/jquery-ui-1.7.2.custom.min.js"></script>
<script language="javascript" type="text/javascript" src="js/jqplot/jquery.jqplot.min.js"></script>
<script language="javascript" type="text/javascript" src="js/jqplot/jqplot.trendline.min.js"></script>
<script language="javascript" type="text/javascript" src="js/jqplot/jqplot.dateAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="js/jqplot/jqplot.highlighter.min.js"></script>
<link rel="stylesheet" type="text/css" href="js/jqplot/jquery.jqplot.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.1/jquery.min.js"></script>
<script type="text/javascript" src="twittersearch.js"></script>
<?php

$timestamp = time();	//timestamp right now

$theDate = strftime('%Y-%m-%d %H:%M:%S',$timestamp);    


$count = 0;
$timestamp_H = floor($timestamp/3600)*3600;
$hour = strftime("%H",$timestamp_H);
if($hour ==23){
	$hour_next = 0;
}else{
	$hour_next = $hour+1;
}
if($hour_next<10){
	$hour_next = "0".$hour_next;
}
$hour_toString = strftime('%m-%d %H:00',$timestamp_H)."-".$hour_next.":00";
 
 
$q=$_GET['q'];
 
if($_GET['q']==''){
 
$q = 'nyan cat';}

$q = urlencode($q); 

$days=$_GET['days'];
 
if($_GET['days']==''|| is_numeric($_GET['days'])==false){
 
$days = 10;}

echo "<h3>Twitter search results for '".$q."' up to '".$days."' days</h3>";
echo "<div>".$hour_toString."</div>";

$timestamp_D = $timestamp-(60*60*24*$days);	//days ago restriction

for($i=1;$i<=15;$i++){
	
	$search = "http://search.twitter.com/search.atom?q=".$q."&page=".$i."&rpp=100";
 
$tw = curl_init();

curl_setopt($tw, CURLOPT_URL, $search);
curl_setopt($tw, CURLOPT_RETURNTRANSFER, TRUE);
$twi = curl_exec($tw);

$search_res = new SimpleXMLElement($twi);
 
## Echo the Search Data

foreach ($search_res->entry as $twit1) {

$date =  strtotime($twit1->updated);
if($date<=$timestamp_D) break;
while($date<$timestamp_H){
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
	if($hour_next<10){
	$hour_next = "0".$hour_next;
}
$hour_toString = strftime('%m-%d %H:00',$timestamp_H)."-".$hour_next.":00";
	echo "<div>".$hour_toString."</div>";
}
	$count++;

	

}
}
echo $count;
curl_close($tw);

?>