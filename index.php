<?php

/*
 * lemonMp3 - simple php mp3 scraper based on lemonScrap.
 * by sphinxid
 * 01/25/2013
 */

@include_once('link.php');
$source = array('s-dilandau', 's-zing', 's-hulkshare');
$keyword = '';

if(!empty($_REQUEST['q'])) {
    $keyword = urldecode($_REQUEST['q']);
}    

if(!empty($keyword)) {
    
    foreach($source as &$s) {       
        $key = $keyword;        
        @include_once($s.'.php');
    }
    
    if(!empty($data)) {
        ?>
        <html>
        <head>
        <title><?php echo $keyword; ?></title>
        </head>
        
        <body>
        <center>
        <h1><?php echo $keyword; ?></h1>
        <p>
    <form method="get">
        cari lagu? <input type="text" name="q" size="20"> <input type="submit">
    </form>
	</p>
        
        <?php
        $i = 0;
        $found = false;
        $total = count($data);
        for($i=0;$i<$total;$i++) {
            $x = 0;
            foreach($data[$i]['title'] as $title) {
                $found = true;
            ?>            
            <div><strong><?php echo $title;?></strong> - <a href="link.php?o=<?php 
		$str = rawurlencode(encrypt($data[$i]['url'][$x], '1234567890987654321', '!@#$%$#@!QWERTREWQ'));
		echo $str; ?>">download</a></div>
            <?php $x++;
        }}
        
        if(!$found) {
            echo "not found";
        }
        
        ?>
        </center>        
        
        <?php
    } 
} else { ?>
    
    <html>
    <head>
    <title>mp3 search</title>
    </head>
    <body>
    <form method="get">
        cari lagu? <input type="text" name="q" size="20"> <input type="submit">
    </form>
    <?php
} ?>
        <p><br /></p>
        &copy; 2013 <a href="https://github.com/sphinxid/lemonMp3">lemonMp3</a> by sphinxid
    </body>
    </html>

