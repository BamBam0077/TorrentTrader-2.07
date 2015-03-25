<?php
//
//  TorrentTrader v2.x
//	This file was last updated: 29/July/2007
//	
//	http://www.torrenttrader.org
//
//
error_reporting(E_ALL ^ E_NOTICE);
require_once("config.php");  //Get Site Settings and Vars ($site_config)

$smilies = array(
  ":)" => "smile1.gif",
  ";)" => "wink.gif",
  ":D" => "grin.gif",
  ":P" => "tongue.gif",
  ":(" => "sad.gif",
  ":'(" => "cry.gif",
  ":|" => "noexpression.gif",
  ":-/" => "confused.gif",
  ":-O" => "ohmy.gif",
  "8)" => "cool1.gif",
  "O:-" => "angel.gif",
  "-_-" => "sleep.gif",
  ":grrr:" => "angry.gif",
  ":smile:" => "smile2.gif",
  ":lol:" => "laugh.gif",
  ":cool:" => "cool2.gif",
  ":fun:" => "fun.gif",
  ":thumbsup:" => "thumbsup.gif",
  ":thumbsdown:" => "thumbsdown.gif",
  ":blush:" => "blush.gif",
  ":weep:" => "weep.gif",
  ":unsure:" => "unsure.gif",
  ":closedeyes:" => "closedeyes.gif",
  ":yes:" => "yes.gif",
  ":no:" => "no.gif",
  ":love:" => "love.gif",
  ":?:" => "question.gif",
  ":!:" => "excl.gif",
  ":idea:" => "idea.gif",
  ":arrow:" => "arrow.gif",
  ":hmm:" => "hmm.gif",
  ":huh:" => "huh.gif",
  ":w00t:" => "w00t.gif",
  ":geek:" => "geek.gif",
  ":look:" => "look.gif",
  ":rolleyes:" => "rolleyes.gif",
  ":kiss:" => "kiss.gif",
  ":shifty:" => "shifty.gif",
  ":blink:" => "blink.gif",
  ":smartass:" => "smartass.gif",
  ":sick:" => "sick.gif",
  ":crazy:" => "crazy.gif",
  ":wacko:" => "wacko.gif",
  ":alien:" => "alien.gif",
  ":wizard:" => "wizard.gif",
  ":wave:" => "wave.gif",
  ":wavecry:" => "wavecry.gif",
  ":baby:" => "baby.gif",
  ":ras:" => "ras.gif",
  ":sly:" => "sly.gif",
  ":devil:" => "devil.gif",
  ":evil:" => "evil.gif",
  ":evilmad:" => "evilmad.gif",
  ":yucky:" => "yucky.gif",
  ":nugget:" => "nugget.gif",
  ":sneaky:" => "sneaky.gif",
  ":smart:" => "smart.gif",
  ":shutup:" => "shutup.gif",
  ":shutup2:" => "shutup2.gif",
  ":yikes:" => "yikes.gif",
  ":flowers:" => "flowers.gif",
  ":wub:" => "wub.gif",
  ":osama:" => "osama.gif",
  ":saddam:" => "saddam.gif",
  ":santa:" => "santa.gif",
  ":indian:" => "indian.gif",
  ":guns:" => "guns.gif",
  ":crockett:" => "crockett.gif",
  ":zorro:" => "zorro.gif",
  ":snap:" => "snap.gif",
  ":beer:" => "beer.gif",
  ":drunk:" => "drunk.gif",
  ":sleeping:" => "sleeping.gif",
  ":mama:" => "mama.gif",
  ":pepsi:" => "pepsi.gif",
  ":medieval:" => "medieval.gif",
  ":rambo:" => "rambo.gif",
  ":ninja:" => "ninja.gif",
  ":hannibal:" => "hannibal.gif",
  ":party:" => "party.gif",
  ":snorkle:" => "snorkle.gif",
  ":evo:" => "evo.gif",
  ":king:" => "king.gif",
  ":chef:" => "chef.gif",
  ":mario:" => "mario.gif",
  ":pope:" => "pope.gif",
  ":fez:" => "fez.gif",
  ":cap:" => "cap.gif",
  ":cowboy:" => "cowboy.gif",
  ":pirate:" => "pirate.gif",
  ":rock:" => "rock.gif",
  ":cigar:" => "cigar.gif",
  ":icecream:" => "icecream.gif",
  ":oldtimer:" => "oldtimer.gif",
  ":wolverine:" => "wolverine.gif",
  ":strongbench:" => "strongbench.gif",
  ":weakbench:" => "weakbench.gif",
  ":bike:" => "bike.gif",
  ":music:" => "music.gif",
  ":book:" => "book.gif",
  ":fish:" => "fish.gif",
  ":whistle:" => "whistle.gif",
  ":stupid:" => "stupid.gif",
  ":dots:" => "dots.gif",
  ":axe:" => "axe.gif",
  ":hooray:" => "hooray.gif",
  ":yay:" => "yay.gif",
  ":cake:" => "cake.gif",
  ":hbd:" => "hbd.gif",
  ":hi:" => "hi.gif",
  ":offtopic:" => "offtopic.gif",
  ":band:" => "band.gif",
  ":hump:" => "hump.gif",
  ":punk:" => "punk.gif",
  ":bounce:" => "bounce.gif",
  ":group:" => "group.gif",
  ":console:" => "console.gif",
  ":smurf:" => "smurf.gif",
  ":soldiers:" => "soldiers.gif",
  ":spidey:" => "spidey.gif",
  ":smurf:" => "smurf.gif",
  ":rant:" => "rant.gif",
  ":pimp:" => "pimp.gif",
  ":nuke:" => "nuke.gif",
  ":judge:" => "judge.gif",
  ":jacko:" => "jacko.gif",
  ":ike:" => "ike.gif",
  ":greedy:" => "greedy.gif",
  ":dumbells:" => "dumbells.gif",
  ":clover:" => "clover.gif",
  ":shit:" => "shit.gif",
);

/* OLD
function insert_smilies_frame(){
    global $site_config, $smilies;

	print("<table><tr><td class=colhead>Type...</td><td class=colhead>To make a...</td></tr>\n");

	while (list($code, $url) = each($smilies)){
		print("<tr><td>$code</td><td><img src=" . $site_config['SITEURL'] . "/images/smilies/$url></td>\n");
	}

	print("</table>\n");
}*/

// New (TorrentialStorm)
function insert_smilies_frame () {
	GLOBAL $site_config, $smilies;

	echo "<table><tr><td class=\"colhead\">Type...</td><td class=\"colhead\">To make a...</td></tr>";
	foreach ($smilies as $code => $url) {
		echo "<tr><td>$code</td><td><a href=\"javascript:window.opener.SmileIT('$code', '".htmlspecialchars($_GET[form])."', '".htmlspecialchars($_GET[text])."')\"><img src=\"$site_config[SITEURL]/images/smilies/$url\" alt=\"$code\" title=\"$code\" border=\"0\"></a></td></tr>";
	}
	echo "</table>";
}

if ($_GET['action'] == "display"){
	insert_smilies_frame();
}

?>