<?php

  global $sql;


  $query = "SELECT * from joined_nodes;";
  $rows = array();
  $i = 0;
  $result =  db_query($query);
  while($row   = db_fetch($result)) {
	//$rows[$i++] = substr($row['address'],-10);
	$rows[$i++] = $row['address'];
  }
  if ($i) echo json_encode($rows);

?>
