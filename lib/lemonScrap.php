<?php

/*
 * lemonScrap.php
 * by sphinxid <cool01@gmail.com>
 * https://github.com/sphinxid/lemonScrap
 * 01/10/2013
 *
 */
 
class LemonScrap {
    
    private $firstURL;
    private $urlRef;
    private $output;
    private $userAgent;
    
    private $firstData;
    private $rules = array();
    private $container = array();
    
    function LemonScrap() {
        @define("DEFAULT_USER_AGENT", "Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 6.0)");
    }
    
    function setFirstURL($url, $ref = '') {
        $this->firstURL = $url;
        $this->urlRef = $ref;
    }
    
    function getFirstURL() {
        return $this->firstURL;
    }
    
    function getURLRef() {
        return $this->urlRef;
    }
    
    function setUserAgent($userAgent) {
        $this->userAgent = $userAgent;
    }
    
    function getUserAgent() {
        return $this->userAgent;
    }
    
    function getDataFromURL($url, $ref = '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getUserAgent());
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if(!empty($ref))
            curl_setopt($ch, CURLOPT_REFERER, $ref);

        $res = curl_exec($ch);
        curl_close($ch);
        
        return utf8_encode($res);        
    }
    
    function scrap() {
        $this->firstData = $this->getDataFromURL($this->getFirstURL(), $this->getURLRef());
        $this->parseRules();                
    }
    
    function getFirstData() {
        return $this->firstData;
    }
    
    function setRules($rules) {
        $this->rules = $rules;
    }

    function getResults() {
        return $this->container;
    }

    function getResultsInJSON() {
        return json_encode($this->container);
    }

    function printDebug() {
        echo $this->getFirstURL()."\n";
        print_r($this->rules);
        print_r($this->container);
    }
    
    function parseRules() {                        
        
        foreach($this->rules as $rule) {

            $tmpData = $this->getFirstData();            

            // 'key' is the array name inside $container for the resulting data.
            $savedKeyname = $rule['key'];

            // skip to 'startFrom' string so parsing will start there.
            if(!empty($rule['startFrom']))
                $tmpData = strstr($tmpData, $rule['startFrom']);

            // we iterates all the rule 
            for($i=0;$i<$rule['max'];$i++) {

                $tmpResultRawData = null;
                $tmpResultCleanData = null;

                foreach($rule as $key => &$val) {
                    switch($key) {
                        
                        case 'setValue': {
                            if(!empty($val)) {
                                $tmpResultRawData = $val;
                                $tmpResultCleanData = $tmpResultRawData;
                                break;
                            }
                        }
                        
                        case 'urlInfo': {
                            if($val === TRUE) {
                                $tmpResultRawData = $this->getFirstURL();
                                $tmpResultCleanData = $tmpResultRawData;
                            }
                            break;
                        }                        
                        
                        // this option is to get data from extracted url 'key' data
                        case 'child': {
                            $childKey = &$val['key'];
                            $childrules[] = &$val['rules'];
                            
                            $arrayNum = 0;
                            foreach($this->container[$childKey] as $childData) {                                
                                $ls = new LemonScrap();
                                $ls->setFirstURL($childData);
                                $ls->setUserAgent($this->getUserAgent());
                                $ls->setRules($childrules);
                                $ls->scrap();
                                
                                $this->container[$childKey]['child'][$arrayNum] = $ls->getResults();                                
                                $arrayNum++;
                                unset($ls);
                            }
                            
                            break;
                        }
                        
                        case 'regex': {
                            
                            if(!empty($this->container[$savedKeyname][$i]))
                                break;
                            
                            $pattern = $val;
                            
                            if(is_array($pattern)) {
                                foreach($pattern as &$p) {
                                    if (preg_match($p, $tmpData, $regs))
                                        break;
                                }
                            }
                            
                            else {
                                preg_match($pattern, $tmpData, $regs);
                            } 
                            
                            if(!empty($regs[1])) {
                                $tmpResultRawData = $regs[1];
                                $tmpResultCleanData = $tmpResultRawData;
                            }
                            break;
                        }
                        
                        case 'xpath': {

                            if(!empty($this->container[$savedKeyname][$i]))
                                break;

                            $pattern = $val;
                            
                            $doc = new DOMDocument();
                            $tmpData2 = $tmpData;
                            @$doc->loadHTML($tmpData2);
                            $xpath = new DOMXpath($doc);
                            
                            if(is_array($pattern)) {
                                foreach($pattern as &$p) {
                                    $o = $xpath->query($p);
                                    if( is_object($o->item(0)) && !empty($o->item(0)->nodeValue) )
                                    {                                        
                                        break;
                                    }                                    
                                }
                            }
                            
                            else {
                                $o = $xpath->query($pattern);
                            }
                            
                            if( is_object($o->item(0)) ) {
                                
                                if(!empty($rule['xpathRaw']) && $rule['xpathRaw']) {
                                    $innerHTML = '';
                                                                        
                                    foreach ($o as $node) {
                                        $innerHTML .= DOMinnerHTML($node);                               
                                    }
                                    
                                    $tmpResultRawData = $innerHTML;                                    
                                }
                                
                                else
                                    $tmpResultRawData = $o->item(0)->nodeValue;
                                    
                                $tmpResultCleanData = $tmpResultRawData;
                            }
                            unset($o);
                            unset($xpath);
                            unset($doc);
                            break;
                        }                        
                        
                        case 'modify': {

                            foreach($val as $key2 => &$val2) {
                                switch($key2) {                                    
                                    case 'addPrefix': $tmpResultCleanData = $val2.$tmpResultCleanData; break;
                                    case 'addSuffix': $tmpResultCleanData = $tmpResultCleanData.$val2; break;
                                    case 'stringReplace': {
                                        if(is_multi_array($val2)) {
                                            foreach($val2 as $vval2) {
                                                $tmpResultCleanData = str_replace($vval2['string1'], $vval2['string2'], $tmpResultCleanData);
                                            }
                                            unset($vval2);
                                        }
                                        else
                                            $tmpResultCleanData = str_replace($val2['string1'], $val2['string2'], $tmpResultCleanData); 
                                            
                                        break;
                                    }
                                    case 'regexReplace': {
                                        if(is_multi_array($val2)) {                                            
                                            foreach($val2 as $vval2) {
                                                $tmpResultCleanData = preg_replace($vval2['regex1'], $vval2['regex2'], $tmpResultCleanData);
                                            }
                                            unset($vval2);
                                        }
                                        else
                                            $tmpResultCleanData = preg_replace($val2['regex1'], $val2['regex2'], $tmpResultCleanData); 
                                            
                                        break;
                                    }
                                    default: break;
                                }
                            }
                            unset($key2);
                            unset($val2);
                            break;
                        }
                        
                        case 'filters': {
                            
                            foreach($val as $key2 => &$val2) {
                                switch($key2) {
                                    case 'trim': $tmpResultCleanData = trim($tmpResultCleanData); break;
                                    case 'striphtml': {
                                        $allowedhtmltags = '';                                        
                                        if(!empty($rule['filters']['allowedhtmltags'])) {
                                            $allowedhtmltags = $rule['filters']['allowedhtmltags'];                                            
                                        }
                                            
                                        $tmpResultCleanData = html2txt($tmpResultCleanData, $allowedhtmltags); 
                                        break;
                                    }
                                    default: break;
                                }
                            }
                            unset($key2);
                            unset($val2);
                            break;
                        }
                        
                        default: break;
                    } //end of switch
                } //end of foreach
                
                if(!empty($tmpResultRawData) && !empty($tmpResultCleanData)) {
                
                    // if it containt some 'string' that we specify, we skip the data
                    if(!empty($rule['skipIfNotContainString']) && @!strstr($tmpResultRawData, $rule['skipIfNotContainString'])) {
                        //do nothing
                    }                    
                    else 
                        $this->container[$savedKeyname][] = $tmpResultCleanData;                                    
                    
                    // we skip over the data we already parsed.
                    $tmpData = strstr($tmpData, $tmpResultRawData);
                    $tmpData = substr($tmpData, strlen($tmpResultRawData));

                    // debug
                    //echo substr($tmpData, 0, 500)."---------------------------\n";
                }
                
                unset($tmpResultRawData);
                unset($tmpResultCleanData);
                
            } // end of for

                
            
        } // end of rules
    }
}

function html2txt($document, $extra = '') {
    //$text = strip_html_tags($document, $extra);
    
    if(empty($extra)) {
        $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript 
                   '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags 
                   '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly 
                   '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA 
        ); 
        $text = preg_replace($search, ' ', $document); 
    }
    else {
        $text = strip_html_tags($document, $extra);
    } 
    return trim($text);
} 

function is_multi_array($a) {
    foreach ($a as $v) {
        if (is_array($v)) return true;
    }
    return false;
}

function strip_html_tags($text, $extra = '')
{
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',"$0", "$0", "$0", "$0", "$0", "$0","$0", "$0",), $text );
      
    return strip_tags($text, $extra);
}

function DOMinnerHTML($element) 
{ 
    $innerHTML = ""; 
    $children = $element->childNodes; 
    foreach ($children as $child) 
    { 
        $tmp_dom = new DOMDocument(); 
        $tmp_dom->appendChild($tmp_dom->importNode($child, true)); 
        $innerHTML.=trim($tmp_dom->saveHTML()); 
    } 
    return $innerHTML; 
}
