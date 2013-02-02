<?php

/*
 * lemonMp3 - simple php mp3 scraper based on lemonScrap.
 * by sphinxid
 * 01/25/2013
 */

include_once 'lemonScrapWrapper.php';

//$key = 'bruno mars';
$key = trim(str_replace(' ','-', $key));

$targetURL = "http://www.dilandau.com";
$targetPath = "/download-mp3/[STRING]-1.html";

$newURL = $targetURL.$targetPath;
$newURL = str_replace('[STRING]', $key, $newURL);

$rules1 = array();

$rules1[] = array(
    //'startFrom' => '',
    'max' => 1,
    'key' => 'content',
    //'skipIfNotContainString' => '/read/',
    'regex' => '%<script type="text/javascript" charset="utf-8">var playlist = (.*?)</script>%s',    
    'filters' => array('trim' => TRUE)        
);

// ----------------------------------------------------------------------------- //

$tmpData = lemonScrapMp3Wrapper($newURL, $rules1);
$str = utf8_encode($tmpData[0]['content'][0]);
//print_r($str);
preg_match_all('/artist : "(.*?)",title : "(.*?)",.+?file : "(.*?)"/s', $str, $result, PREG_PATTERN_ORDER);
//print_r($result);

$n = 0;
foreach($result[1] as &$t) {
    $str2['title'][$n] = trim($t).' '.trim($result[2][$n]);
    $str2['title'][$n] = trim(str_replace('  ',' ', $str2['title'][$n]));
    $str2['url'][$n] = $result[3][$n];
    $n++;
}

$data[] = $str2;

unset($str);
unset($str2);
unset($tmpData);
//print_r($data);
// ----------------------------------------------------------------------------- //
