<?php
$in='async.php/get_graphdata/week?snpids=1';
	  preg_match('@/([a-zA-Z]+)\?snpids=([0-9,]+)@',$in , $matches);
	  echo "\r\n";
		print_r($matches);
	  echo "\r\n";
	  echo strtoupper($matches[1])."\r\n";
	  print_r( explode(',', $matches[2]));
	  preg_match('/start=([0-9\- ]+)/', $in, $matches);
	  echo "\r\n";
		print_r($matches);
      $panel_found=true;
?>
