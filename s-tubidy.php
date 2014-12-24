<?php

/*
 * lemonMp3 - simple php mp3 scraper based on lemonScrap.
 * by sphinxid
 * 01/25/2013
 */

include_once 'lemonScrapWrapper.php';

// $key = 'maroon 5';
// $key = urlencode($key);

// http://songs.tubidy.im/list/maroon-5-payphone.html

$targetURL = "http://songs.tubidy.im/list/";
$targetPath = "[STRING].html";

$keyseo = preg_replace("/[^a-z0-9.]+/i", "-", $key);
$keyseo = str_replace("--", "-", $keyseo);

$newURL = $targetURL.$targetPath;
$newURL = str_replace('[STRING]', $keyseo, $newURL);

$rules1 = array();

$rules1[] = array(
    'startFrom' => '',
    'max' => 20,
    'key' => 'title',
    'regex' => array(
                '%<img src="/img/mp3-d.gif" alt="icons"/>.+?">(.*?)</a>%s',
    ),
    'filters' => array('trim' => TRUE)        
);

$rules1[] = array(
    'startFrom' => '',
    'max' => 20,
    'key' => 'url',
    'regex' => array(
                '%alt="icons"/> <a href="(.+?)">%s',
    ),
    'filters' => array('trim' => TRUE)        
);



// ----------------------------------------------------------------------------- //

//print_r($newURL);

$tmpData = lemonScrapMp3Wrapper($newURL, $rules1);
//print_r($tmpData);

$n = 0;
foreach($tmpData[0]['title'] as &$t) {

    $t = trim(str_replace('  ',' ', $t));

    $str['title'][$n] = $t;

    $t = trim(str_replace(' ','_', $t));

    $str['url'][$n] = 'dl2.php?q='.$tmpData[0]['url'][$n];
    $str['url'][$n] = str_replace('.html', '.mp3',  $str['url'][$n]);

    $n++;
}

$data[] = $str;

unset($str);
unset($tmpData);

// print_r($data);
// ----------------------------------------------------------------------------- //
