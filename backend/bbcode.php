<?php
//
//  TorrentTrader v2.x
//      $LastChangedDate: 2010-02-14 00:15:45 +0000 (Sun, 14 Feb 2010) $
//      $LastChangedBy: torrentialstorm $
//
//      http://www.torrenttrader.org
//
//

function textbbcode($form,$name,$content="") {
	//$form = form name
	//$name = textarea name
	//$content = textarea content (only for edit pages etc)
?>
<script language=javascript>
function SmileIT(smile,form,text){
    document.forms[form].elements[text].value = document.forms[form].elements[text].value+" "+smile+" ";
    document.forms[form].elements[text].focus();
}

function PopMoreSmiles(form,name) {
         link='backend/smilies.php?action=display&form='+form+'&text='+name
         newWin=window.open(link,'moresmile','height=500,width=450,resizable=no,scrollbars=yes');
         if (window.focus) {newWin.focus()}
}

function PopMoreTags(form,name) {
         link='tags.php?form='+form+'&text='+name
         newWin=window.open(link,'moresmile','height=500,width=775,resizable=no,scrollbars=yes');
         if (window.focus) {newWin.focus()}
}


function BBTag(tag,s,text,form){
switch(tag)
    {
    case '[quote]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substring(0, start);
		var middle = "[quote]" + body.substring(start, end) + "[/quote]";
		var rightpos = start + body.substring(start, end).length;
		var right = body.substring(rightpos, end);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[quote][/quote]";
	}
        break;
    case '[img]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substring(0, start);
		var middle = "[img]" + body.substring(start, end) + "[/img]";
		var rightpos = start + body.substring(start, end).length;
		var right = body.substring(rightpos, end);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[img][/img]";
	}
        break;
    case '[url]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substring(0, start);
		var middle = "[url]" + body.substring(start, end) + "[/url]";
		var rightpos = start + body.substring(start, end).length;
		var right = body.substring(rightpos, end);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[url][/url]";
	}
        break;
    case '[*]':
        document.forms[form].elements[text].value = document.forms[form].elements[text].value+"[*]";
        break;
    case '[b]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substring(0, start);
		var middle = "[b]" + body.substring(start, end) + "[/b]";
		var rightpos = start + body.substring(start, end).length;
		var right = body.substring(rightpos, end);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[b][/b]";
	}
        break;
    case '[i]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substring(0, start);
		var middle = "[i]" + body.substring(start, end) + "[/i]";
		var rightpos = start + body.substring(start, end).length;
		var right = body.substring(rightpos, end);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[i][/i]";
	}
        break;
    case '[u]':
	var start = document.forms[form].elements[text].selectionStart;
	var end = document.forms[form].elements[text].selectionEnd;
	if (start != end) {
		var body = document.forms[form].elements[text].value;
		var left = body.substring(0, start);
		var middle = "[u]" + body.substring(start, end) + "[/u]";
		var rightpos = start + body.substring(start, end).length;
		var right = body.substring(rightpos, end);
		document.forms[form].elements[text].value = left + middle + right;
	} else {
		document.forms[form].elements[text].value = document.forms[form].elements[text].value + "[u][/u]";
	}
        break;
    }
    document.forms[form].elements[text].focus();
}

</script>

  <CENTER>
  <table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
      <td colspan="2" align=center>
		  <table cellpadding="2" cellspacing="1">
		  <tr>
		  <td><input style="font-weight: bold;" type="button" name="bold" value="B " onclick="javascript: BBTag('[b]','bold','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
		  <td><input style="font-style: italic;" type="button" name="italic" value="I " onclick="javascript: BBTag('[i]','italic','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
		  <td><input style="text-decoration: underline;" type="button" name="underline" value="U " onclick="javascript: BBTag('[u]','underline','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
		  <td><input type="button" name="li" value="List " onclick="javascript: BBTag('[*]','li','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
		  <td><input type="button" name="quote" value="QUOTE " onclick="javascript: BBTag('[quote]','quote','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
		  <td><input type="button" name="url" value="URL " onclick="javascript: BBTag('[url]','url','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
		  <td><input type="button" name="img" value="IMG " onclick="javascript: BBTag('[img]','img','<?php echo $name; ?>','<?php echo $form; ?>')" /></td>
		  <td>&nbsp;<a href="javascript: PopMoreTags('<?php echo $form; ?>','<?php echo $name; ?>')"><?php echo "[".T_("MORE_TAGS")."]";?></a></td>
		  </tr>
		  </table>
	</td>
	</tr>

	<tr>
    <td align=center>
	<textarea name="<?php echo $name; ?>" rows="10" cols="50"><?php echo $content; ?></textarea>
	</td>    
	<td>
		<center><table border="0" cellpadding=0 cellspacing=0>
		<tr>
		<td width=26><a href="javascript:SmileIT(':)','<?php echo $form?>','<?php echo $name?>')"><img src="images/smilies/smile1.gif" border="0" alt=':)'></a></td>
		<td width=26><a href="javascript:SmileIT(';)','<?php echo $form?>','<?php echo $name?>')"><img src="images/smilies/wink.gif" border="0" alt=';)'></a></td>
		<td width=26><a href="javascript:SmileIT(':D','<?php echo $form?>','<?php echo $name?>')"><img src="images/smilies/grin.gif" border="0" alt=':D'></a></td>
		</tr>
		<tr>
		<td width=26><a href="javascript:SmileIT(':P','<?php echo $form?>','<?php echo $name?>')"><img src="images/smilies/tongue.gif" border="0" alt=':P'></a></td>
		<td width=26><a href="javascript:SmileIT(':lol:','<?php echo $form?>','<?php echo $name?>')"><img src="images/smilies/laugh.gif" border="0" alt=':lol:'></a></td>
		<td width=26><a href="javascript:SmileIT(':yes:','<?php echo $form?>','<?php echo $name?>')"><img src="images/smilies/yes.gif" border="0" alt=':yes:'></a></td>
		</tr>
		<tr>
		<td width=26><a href="javascript:SmileIT(':no:','<?php echo $form?>','<?php echo $name?>')"><img src="images/smilies/no.gif" border="0" alt=':no:'></a></td>
		<td width=26><a href="javascript:SmileIT(':wave:','<?php echo $form?>','<?php echo $name?>')"><img src="images/smilies/wave.gif" border="0" alt=':wave:'></a></td>
		<td width=26><a href="javascript:SmileIT(':ras:','<?php echo $form?>','<?php echo $name?>')"><img src="images/smilies/ras.gif" border="0" alt=':ras:'></a></td>
		</tr>
		<tr>
		<td width=26><a href="javascript:SmileIT(':sick:','<?php echo $form?>','<?php echo $name?>')"><img src="images/smilies/sick.gif" border="0" alt=':sick:'></a></td>
		<td width=26><a href="javascript:SmileIT(':yucky:','<?php echo $form?>','<?php echo $name?>')"><img src="images/smilies/yucky.gif" border="0" alt=':yucky:'></a></td>
		<td width=26><a href="javascript:SmileIT(':rolleyes:','<?php echo $form?>','<?php echo $name?>')"><img src=images/smilies/rolleyes.gif border="0" alt=':rolleyes:'></a></td>
		</tr>
		</table>
	<br>
	<a href="javascript: PopMoreSmiles('<?php echo $form; ?>','<?php echo $name; ?>')"><?php echo "[".T_("MORE_SMILIES")."]";?></a></a><br><br></center>
   </td>  
   </tr>
</table>
<?php
}
?>
