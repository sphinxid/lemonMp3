<?php

/*
 * lemonMp3 - simple php mp3 scraper based on lemonScrap.
 * by sphinxid
 * 01/25/2013
 */

@include_once('link.php');
$source = array('s-dilandau', 's-zing', 's-hulkshare');
$keyword = '';
$hasil = '';
$found = false;

if(!empty($_REQUEST['q'])) {
    $keyword = urldecode($_REQUEST['q']);
}

if(!empty($keyword)) {

    foreach($source as &$s) {
        $key = $keyword;
        @include_once($s.'.php');
    }

        $i = 0;
        $found = false;
        $total = count($data);
        for($i=0;$i<$total;$i++) {
            $x = 0;
            foreach($data[$i]['title'] as $title) {
            $found = true;

	    $str = rawurlencode(encrypt($data[$i]['url'][$x], '1234567890987654321', '!@#$%$#@!QWERTREWQ'));
            $hasil .= '<div><strong>'.$title.'</strong> - <a href="link.php?o='.$str.'">download</a></div>';
            $x++;
        }}

        if($found != true) {
            $hasil = "not found";
        }

} ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>LemonMp3</title>
<style type="text/css">

#container {
	width: 700px;
	margin-top: 20px;
	margin-left: auto;
	margin-right: auto;
}

#content {
	width: 690px;
        margin-left: auto;
        margin-right: auto;
	margin-top: 15px;
	padding-bottom: 15px;
	text-align: center;
	border-top-width: 1px;
	border-bottom-width: 1px;
	border-color: black;
	border-style: solid;
	border-left-width: 0px;
	border-right-width: 0px;
}

#footer {
	margin-top: 5px;
	text-align: center;
	font-size: 9pt;
}

#box {
	width: 690px;
	padding: 10px 10px 20px 10px;
	margin-left: auto;
	margin-right: auto;
	margin-bottom: auto;
	text-align: center;
	background: #39C;
	border-style: solid;
	border-width: 2px;
	border-color: black;
}
.searchbar {
	border-width: 2px;
	border-color: #36C;
	border-style: solid;
	padding: 0 10px 0 10px;
	font-size: 16px;
	width: 500px;
	height: 30px;
	margin-bottom: 5px;
}
.searchbutton {
	height: 30px;
}
</style>
</head>

<body>

<div id="container">
    <div id="box">    
    	<h1>Lemon Mp3</h1>    
        <form action="" method="get">
        <input name="q" type="text" class="searchbar" /><br />
        <input name="" value="search mp3" type="submit" class="searchbutton" />
        </form>
    </div>
    
    <div id="footer">
    &copy; 2013 <a href="https://github.com/sphinxid/lemonMp3">lemonMp3</a> by sphinxid
    </div>

    <?php if(!empty($keyword)) {
    ?>
    <div id="content">
	<h1><?php echo $keyword;?></h1>
	<?php echo $hasil; ?>
    </div>
    <?php } ?>

    
</div>
    
   

</body>
</html>
