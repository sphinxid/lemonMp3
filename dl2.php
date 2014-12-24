<?php

/*
 * lemonMp3 - simple php mp3 scraper based on lemonScrap.
 * by sphinxid
 * 01/25/2013
 */

include_once 'lemonScrapWrapper.php';

if (empty($_REQUEST['q']))
  return;

$key = $_REQUEST['q'];
$key = urldecode($key);
$key = 'http://songs.tubidy.im/'.$key;
$key = str_replace('.mp3', '.html', $key);

$newURL = $key;

$rules1 = array();

$rules1[] = array(
    'startFrom' => '',
    'max' => 1,
    'key' => 'url',
    'regex' => array(
                '/<audio controls style="width:100%;" src="(.+?)" type="audio/s',
    ),
    'filters' => array('trim' => TRUE)        
);



// ----------------------------------------------------------------------------- //

//print_r($newURL);

$tmpData = lemonScrapMp3Wrapper($newURL, $rules1);
// print_r($tmpData);

if (!empty($tmpData))
{
  $url = 'http://songs.tubidy.im' . $tmpData[0]['url'][0];
  header("Location: $url");
}

die;


// ----------------------------------------------------------------------------- //
