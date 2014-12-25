<?php

class LemonPlayList {

  private $data;

  function __construct($data)
  {
    $this->data = $data;
  }

  public function generateHeader()
  {
    if (is_array($this->data) && count($this->data) <= 0)
      return;

    $template = '

<link href="jPlayer-2.9.2/dist/skin/blue.monday/css/jplayer.blue.monday.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="jPlayer-2.9.2/lib/jquery.min.js"></script>
<script type="text/javascript" src="jPlayer-2.9.2/dist/jplayer/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="jPlayer-2.9.2/dist/add-on/jplayer.playlist.min.js"></script>

<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){

	var myPlaylist = new jPlayerPlaylist({
		jPlayer: "#jquery_jplayer_N",
		cssSelectorAncestor: "#jp_container_N"
	}, [[PLAYLIST]]

	, {
		playlistOptions: {
			enableRemoveControls: true
		},
		swfPath: "jPlayer-2.9.2/dist/jplayer",
		supplied: "mp3",
		useStateClassSkin: true,
		autoBlur: false,
		smoothPlayBar: true,
		keyEnabled: false,
		audioFullScreen: false
	});


});


//]]>
</script>
';

/*
	[
		{
			title:"LumpuhkanLah Ingatanku",
			free: true,
			mp3:"http://sphinx.cloudapp.net/lemonMp3/link.php?o=f14geK2nJtPKprZMfRG7%2BwmbQo4WKA2QZeNXjLehU6n3uG1rUGOvv7ycKpc6dvL3vAdCG%2FxRUUhGKpEc0sfFY0I7AErOhz57WzT9DT9Fam6Slr3aiO7wNvPOpTnWE%2FTl2yd%2BoPVnpzLKanAqTQkths3jxHoGV1zX6fKHDcvRhNEg%3D%3D",
		},
                {
                        title:"Lumpuhkan INgatanku",
			free: true,
                        mp3:"http://sphinx.cloudapp.net/lemonMp3/link.php?o=bxXETWwPxX%2B2iebpRwR9vAud059IgiFeE1jJFtWMCjIfEz4oHypnn63OXBATwcD%2BcgV4wBl0JXFG%2F06MoZn1F72qseeBaS5mjSBgn4On5oEw%3D%3D",
                },

	]
*/

    $i = 0;
    $hostname = $_SERVER['SERVER_NAME'];

    $x = 0;
    foreach($this->data as &$d)
    {
      $max = count($d['title']);
      for($i=0;$i<$max;$i++)
      {
        $list[$x]['title'] = $d['title'][$i];
        $list[$x]['free'] = true;

          $str = rawurlencode(encrypt($d['url'][$i], '1234567890987654321', '!@#$%$#@!QWERTREWQ'));
          $link = sprintf("http://%s/lemonMp3/link.php?o=%s", $hostname, $str);

        $list[$x]['mp3'] = $link;

        $x++;
      }
    }

    // print_r($list); die;

    $list = json_encode($list);

    $finalTemplate = str_replace('[[PLAYLIST]]', $list, $template);
    return $finalTemplate;

  }

  public function generateBody()
  {
    if (is_array($this->data) && count($this->data) <= 0)
      return;

    $template = '
<div style="margin: 10px auto 10px;" id="jp_container_N" class="jp-video jp-video-270p" role="application" aria-label="media player">
	<div class="jp-type-playlist">
		<div id="jquery_jplayer_N" class="jp-jplayer"></div>
		<div class="jp-gui">
			<div class="jp-video-play">
				<button class="jp-video-play-icon" role="button" tabindex="0">play</button>
			</div>
			<div class="jp-interface">
				<div class="jp-progress">
					<div class="jp-seek-bar">
						<div class="jp-play-bar"></div>
					</div>
				</div>
				<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
				<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
				<div class="jp-controls-holder">
					<div class="jp-controls">
						<button class="jp-previous" role="button" tabindex="0">previous</button>
						<button class="jp-play" role="button" tabindex="0">play</button>
						<button class="jp-next" role="button" tabindex="0">next</button>
						<button class="jp-stop" role="button" tabindex="0">stop</button>
					</div>
					<div class="jp-volume-controls">
						<button class="jp-mute" role="button" tabindex="0">mute</button>
						<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
						<div class="jp-volume-bar">
							<div class="jp-volume-bar-value"></div>
						</div>
					</div>
					<div class="jp-toggles">
						<button class="jp-repeat" role="button" tabindex="0">repeat</button>
						<button class="jp-shuffle" role="button" tabindex="0">shuffle</button>
						<button class="jp-full-screen" role="button" tabindex="0">full screen</button>
					</div>
				</div>
				<div class="jp-details">
					<div class="jp-title" aria-label="title">&nbsp;</div>
				</div>
			</div>
		</div>
		<div class="jp-playlist">
			<ul>
				<!-- The method Playlist.displayPlaylist() uses this unordered list -->
				<li>&nbsp;</li>
			</ul>
		</div>
		<div class="jp-no-solution">
			<span>Update Required</span>
			To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
		</div>
	</div>
</div>
    ';

    $finalTemplate = $template;
    return $finalTemplate;

  }
}
