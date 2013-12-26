<?php

/*
 * lemonMp3 - simple php mp3 scraper based on lemonScrap.
 * by sphinxid
 * 01/25/2013
 */

include_once 'lemonScrapWrapper.php';

//$key = 'maroon 5 payhone';
$key = urlencode($key);

$targetURL = "http://mp3.zing.vn";
$targetPath = "/tim-kiem/bai-hat.html?q=[STRING]";

$newURL = $targetURL.$targetPath;
$newURL = str_replace('[STRING]', $key, $newURL);

$rules1 = array();

$rules1[] = array(
    'startFrom' => '<div class="content" id="_content">',
    'max' => 20,
    'key' => 'artist',
    'regex' => array(
                '%: <a href="/tim-kiem/bai-hat\.html\?q=.+?class="txtBlue">(.+?)</a>%s',
    ),
    'filters' => array('trim' => TRUE)        
);

$rules1[] = array(
    'startFrom' => '<div class="content" id="_content">',
    'max' => 20,
    'key' => 'title',

    'regex' => array(
                '%<a class="_trackLink" tracking="_frombox=search_song".+?">(.+?)</a>%',
    ),
    'filters' => array('trim' => TRUE)        
);

$rules1[] = array(
    'startFrom' => '<div class="content" id="_content">',
    'max' => 20,
    'key' => 'url',
    'regex' => array(
                '/document\.write\(\'<div class="music-function"><a title="Download.+? href="(.+?)"/',
    ),
    'filters' => array('trim' => TRUE)        
);

// ----------------------------------------------------------------------------- //

$tmpData = lemonScrapMp3Wrapper($newURL, $rules1);
//print_r($tmpData);
$n = 0;
foreach($tmpData[0]['title'] as &$t) {

    if(strstr($tmpData[0]['url'][$n], 'mp3.zing.vn'))
    {
      $t = trim(str_replace('  ',' ', $t));
      $str['title'][$n] = $tmpData[0]['artist'][$n].' - '.$t;
      $t = trim(str_replace(' ','_', $t));
      $str['url'][$n] = $tmpData[0]['url'][$n].'/'.urlencode($t).'?z=1';
      $n++;
    }
}
$data[] = $str;

unset($str);
unset($tmpData);

//print_r($data);
// ----------------------------------------------------------------------------- //
