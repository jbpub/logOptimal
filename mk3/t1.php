<?php
$in='_TB_bmp_h_renoir.jpg';
echo 'in='.$in."\r\n";
//$s=preg_replace('/_TB_([^_]+)_([.]+)\.jpg$/','$2$1',$in);
$s=preg_replace('/'.$argv[1].'/','zz$2zz$1zz',$in);
print_r(error_get_last());
echo 'reg='.$s."\r\n";
//print_r(
//$s
//);

?>
