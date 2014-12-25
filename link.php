<?php

include_once('lib/link.php');

if(!empty($_REQUEST['o'])) {
  $o = rawurldecode ($_REQUEST['o']);
  $str = decrypt($o, '1234567890987654321', '!@#$%$#@!QWERTREWQ');
  header("Location: {$str}");
}
