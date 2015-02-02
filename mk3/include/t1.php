<?php
$dnm =dirname(__DIR__).'/photos/_TB_*';
$flist =glob($dnm);
echo '<!-- '.$dnm.' -->';
$dlen=strlen($dnm)-12;
for ($i=0; $i < sizeof($flist); $i++) {
  echo '<!-- '. substr($flist[$i],$dlen) .' -->'."\r\n";
}
echo '<!-- ';
echo ' -->'."\r\n";

