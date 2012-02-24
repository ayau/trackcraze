<div id="ribbon">
	<a href='feedback.php'><div class='feedback sp'></div></a><p class='feedbacktext'>Tell us what &nbsp; you think!</p>
    <div id="rsidetitle"><p>Daily Tips</p></div>

	<ul>
         <?php
		$tips = "txt/tips.txt";
		$tips = file($tips);
		srand((double)microtime() * 1000000);
		for ($i=0;$i<4;$i++){
			$ranNum[$i] = rand(0, count($tips)-1);
			for ($j=0;$j<$i;$j++){
				while($ranNum[$i]==$ranNum[$j]){
					$ranNum[$i]=rand(0, count($tips)-1);
				}
			}
			echo "<li>".$tips[$ranNum[$i]]."</li>";
		}
?>
    </ul>

</div>