<?php

/*
 * lemonMp3 - simple php mp3 scraper based on lemonScrap.
 * by sphinxid
 * 01/25/2013
 */

include './lib/lemonScrap.php';

$myUserAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.97 Safari/537.11';

function lemonScrapMp3Wrapper($targetURL, $rules1, $rules2 = '') {
    
    global $myUserAgent;
    
    $ls = new LemonScrap();
    $ls->setFirstURL($targetURL);
    $ls->setUserAgent($myUserAgent);
    $ls->setRules($rules1);
    $ls->scrap();
    
    $data[0] = $ls->getResults();
    
    if(!empty($rules2)) {
        foreach($data1['url'] as $url) {    
            $ls2 = new LemonScrap();
            $ls2->setFirstURL($url);
            $ls2->setUserAgent($myUserAgent);
            $ls2->setRules($rules2);
            $ls2->scrap();
            
            $data[1][] = $ls2->getResults();
            unset($ls2);
        } 
    }
    unset($ls);
    return $data;
}

function json_js_php($string){

    $string = str_replace("{",'{"',$string);
    $string = str_replace(":'",'":"',$string);
    $string = str_replace("',",'","',$string);
    $string = str_replace("'}",'"}',$string);
    return $string;

}
