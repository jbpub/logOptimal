<?php



$api_key="axbQznyh5HLC2ZmpMMfNFVKxHBUchg2AVTRL8Gu4P76FWhvjvSx3n8qw9ARhgHh5";
define('API_URL',"https://www.havelockinvestments.com/api/index.php");
define('API_CONNECTTIMEOUT', 15);
define('API_TIMEOUT', 30/*0*/);

$api_ch=curl_init() or exit ("cannot initialize curl");
curl_setopt($api_ch,CURLOPT_RETURNTRANSFER,1);


function api_init($pkey)
{
    global $api_ch;
    global $api_url;
    global $api_key;

    if ($pkey) $api_key=$pkey;
    return $api_ch;
}

function api($cmd,$vars=array()) {
    global $api_ch;
    global $api_url;
    global $api_key;
    curl_setopt($api_ch,CURLOPT_URL, API_URL);
    $fields=array("key"=>$api_key,
            "cmd"=>$cmd);
    $fields_string="";
    $fields=array_merge($fields,$vars);
    foreach($fields as $k=>$value) { $fields_string .= $k.'='.urlencode($value).'&'; }

    $fields_string=rtrim($fields_string, '&');

    curl_setopt($api_ch,CURLOPT_POST, count($fields));
    curl_setopt($api_ch,CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($api_ch, CURLOPT_CONNECTTIMEOUT, API_CONNECTTIMEOUT);
    curl_setopt($api_ch, CURLOPT_TIMEOUT, API_TIMEOUT);
//    curl_setopt($api_ch, CURLOPT_HEADER,  1);
//    curl_setopt($api_ch, CURLINFO_HEADER_OUT,  1);

    $tries = 0;
    do {
        $ret=curl_exec($api_ch);
        if (curl_errno($api_ch) == 28) {
            echo "try ". (string)($tries+1) ."\n";
            if (++$tries >= 5) break;
            continue;
        }
        break;
    } while (true);
    if ($ret == NULL) {
       echo "curl exec failed - try $tries";
       exit(1);
    }
   
//    var_dump($ret);
    return json_decode($ret,true);

}
function api_close()
{
    global $api_ch;
    curl_close($api_ch);
    $api_ch=null;
}
function api_errno()
{
    global $api_ch;
    return  curl_errno($api_ch);
}
?>
