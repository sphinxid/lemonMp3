<?php

/*
 * lemonMp3 - simple php mp3 scraper based on lemonScrap.
 * by sphinxid
 * 01/25/2013
 */

include_once 'lemonScrapWrapper.php';

// $key = 'blink 182';
$key = urlencode($key);

$targetURL = "http://www.hulkshare.com";
$targetPath = "/search.php?q=[STRING]";

$newURL = $targetURL.$targetPath;
$newURL = str_replace('[STRING]', $key, $newURL);

$rules1 = array();

$rules1[] = array(
    'startFrom' => '<div class="searchResults">',
    'max' => 20,
    'key' => 'title',

    'regex' => array(
                '/<div class="searchResultsItem">.+?<input type="checkbox".+?selectedFiles.+?img alt="(.*?)"/s',
                '%<font class="filename">Filename: <b>(.*?)</b>%s',                
    ),
    'modify' => array(
                'addSuffix' => '.mp3',
    ),
    'filters' => array('trim' => TRUE)        
);

$rules1[] = array(
    'startFrom' => '<div class="searchResults">',
    'max' => 20,
    'key' => 'url',
    'xpath' => '//div[@class="userAv"]/a/@href',
    'modify' => array(
                'addPrefix' => 'http://trckr.hulkshare.com/hulkdl',
    ),
    'filters' => array('trim' => TRUE)        
);

// ----------------------------------------------------------------------------- //

$tmpData = lemonScrapMp3Wrapper($newURL, $rules1);
// print_r($tmpData);

$n = 0;
foreach($tmpData[0]['title'] as &$t) {

    if (!empty($tmpData[0]['url'][$n]))
    {
      $t = trim(str_replace('  ',' ', $t));
      $t2 = str_replace('.mp3', '', $t);
      $str['title'][$n] = $t2;
      $t = trim(str_replace(' ','_', $t));
      $str['url'][$n] = $tmpData[0]['url'][$n].'/'.urlencode($t).'?z=1';
      $n++;
    }
}
$data[] = $str;

unset($str);
unset($tmpData);

// print_r($data);
// ----------------------------------------------------------------------------- //
