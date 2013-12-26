<?php

/*
 * lemonMp3 - simple php mp3 scraper based on lemonScrap.
 * by sphinxid
 * 01/25/2013
 */

include_once 'lemonScrapWrapper.php';

//$key = 'bruno mars';
//$key = trim(str_replace(' ','-', $key));
$key = rawurlencode($key);

$targetURL = "http://www.dilandau.eu";
$targetPath = "/download-songs-mp3/[STRING]/1.html";

$newURL = $targetURL.$targetPath;
$newURL = str_replace('[STRING]', $key, $newURL);

$rules1 = array();

$rules1[] = array(
    //'startFrom' => '<div class="stitle-container">',
    'max' => 1,
    'key' => 'content',
    //'skipIfNotContainString' => '/read/',
    //'regex' => '%<table class="glist">(.+?)</table>%s',
    'filters' => array('trim' => TRUE)
);

// ----------------------------------------------------------------------------- //

$tmpData = lemonScrapMp3Wrapper($newURL, $rules1);
$str = utf8_encode($tmpData[0]['content'][0]);

//echo $newURL."\n";
//print_r($tmpData);

preg_match_all('/title="(.+?)".*EMBEDCODE.showCode\(.+?,\'(.+?)\'/s', $str, $result, PREG_PATTERN_ORDER);
//print_r($result);

$n = 0;
foreach($result[1] as &$t) {
    $str2['title'][$n] = trim($t).' '.trim($result[2][$n]);
    $str2['title'][$n] = trim(str_replace('  ',' ', $str2['title'][$n]));
    $str2['url'][$n] = $result[3][$n];
    $str2['url'][$n] = str_replace("' + '", '', $str2['url'][$n]);
    $n++;
}

$data[] = $str2;

unset($str);
unset($str2);
unset($tmpData);
//print_r($data);
// ----------------------------------------------------------------------------- //
