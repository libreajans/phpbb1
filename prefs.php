<?php
/***************************************************************************
                            prefs.php  -  description
                             -------------------
    begin                : Sat June 17 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org
 
    $Id: prefs.php,v 1.16 2000/12/06 22:33:11 thefinn Exp $
 
 ***************************************************************************/

/***************************************************************************
 *                                         				                                
 *   This program is free software; you can redistribute it and/or modify  	
 *   it under the terms of the GNU General Public License as published by  
 *   the Free Software Foundation; either version 2 of the License, or	    	
 *   (at your option) any later version.
 *
 ***************************************************************************/
include('extention.inc');
include('functions.'.$phpEx);
include('config.'.$phpEx);
require('auth.'.$phpEx);
$pagetitle = "Preferences";
$pagetype = "index";

if($submit || $user_logged_in) {
	if($save) {
		if (!$user_logged_in) {
			// no valid session, need to check user/pass.
			if($user == '' || $passwd == '') {
				die("You must enter your username and password. Go back and do so.");
			}
			$md_pass = md5($passwd);
			$userdata = get_userdata($user, $db);
			if($md_pass != $userdata["user_password"]) {
				die("You have entered an incorrect password. Go back and try again.");
			}	
		   if(is_banned($userdata[user_id], "username", $db))
		     die("You have been banned from this forum. Contact the system administrator if you have any quesions.");
		   // Log them in, they are authed!
		   $sessid = new_session($userdata[user_id], $REMOTE_ADDR, $sesscookietime, $db);
		   set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);
		}
		
		if($savecookie == 1) {
			$time = (time() + 3600 * 24 * 30 * 12);
			setcookie($cookiename, $userdata[user_id], $time, $cookiepath, $cookiedomain, $cookiesecure);
		}
		include('page_header.'.$phpEx);
		$sql = "UPDATE users SET user_viewemail='$viewemail', user_theme='$themes', user_attachsig = '$sig', user_desmile = '$smile', user_html = '$dishtml', user_bbcode = '$disbbcode' WHERE (user_id = '$userdata[user_id]')";
	   if(!$result = mysql_query($sql, $db)) {
			die("An Error Occured<hr>Could not update the database. Please go back and try again.");
		}
		echo "Preferences updated. Click <a href=\"index.$phpEx\">here</a> to return to the forum index.";
	} else {
		
		if (!$user_logged_in) {
			// no valid session, need to check user/pass.
			if($user == '' || $passwd == '') {
				die("You must enter your username and password. Go back and do so.");
			}
			$md_pass = md5($passwd);
			$userdata = get_userdata($user, $db);
			if($md_pass != $userdata["user_password"]) {
			   include('page_header.'.$phpEx);
			   die("You have entered an incorrect password. Go back and try again.");
			}	
		   if(is_banned($userdata[user_id], "username", $db))
		     die("You have been banned from this forum. Contact the system administrator if you have any quesions.");
		   $sessid = new_session($userdata[user_id], $REMOTE_ADDR, $sesscookietime, $db);
		   set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);
		}
		include('page_header.'.$phpEx);
		if($userdata[user_viewemail] == 1) {
			$y = "CHECKED";
		} else {
			$n = "CHECKED";
		}
	   
	   if($userdata[user_attachsig] == 1) 
	     $allways_sig = "CHECKED";
	   else
	     $no_allways_sig = "CHECKED";
	   
	   if($userdata[user_desmile] == 1)
	     $never_smile = "CHECKED";
	   else
	     $no_never_smile = "CHECKED";
	   
	   if($userdata[user_html] == 1)
	     $never_html = "CHECKED";
	   else
	     $no_never_html = "CHECKED";
	   
	   if($userdata[user_bbcode] == 1)
	     $never_bbcode = "CHECKED";
	   else
	     $no_never_bbcode = "CHECKED";
	   
	   if(isset($HTTP_COOKIE_VARS[$cookiename])) {
	      $user_cookie = "CHECKED";
	   } else {
	      $user_nocookie = "CHECKED";
	   }
?>
<FORM ACTION="<?php echo $PHP_SELF?>" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="1" CELLSPACEING="0" ALIGN="CENTER" VALIGN="TOP" WIDTH="<?php echo $tablewidth?>"><TR><TD  BGCOLOR="<?php echo $table_bgcolor?>">
<TABLE BORDER="0" CALLPADDING="1" CELLSPACEING="1" WIDTH="100%">
<TR BGCOLOR="<?php echo $color1?>" ALIGN="LEFT">
	<TD COLSPAN="2" ALIGN="CENTER"><b>Edit Your Preferences</b></TD>
</TR>
<TR BGCOLOR="<?php echo $color1?>" ALIGN="LEFT">
	<TD COLSPAN="2" ALIGN="CENTER"><font size=-1>NOTE: In order to use themes you MUST have cookies enabled.</font></TD>
</TR>
<TR BGCOLOR="<?php echo $color2?>" ALIGN="LEFT">
	<TD>Username:</TD>
	<TD><?php echo $userdata[username]?></TD>
</TR>
<?PHP
	if (!$user_logged_in) { 
		// no session, need a password.
		echo "    <TR BGCOLOR=\"$color2\" ALIGN=\"LEFT\"> \n";
		echo "        <TD><b>Password:</b></TD> \n";
		echo "        <TD><INPUT TYPE=\"PASSWORD\" NAME=\"passwd\" SIZE=\"25\" MAXLENGTH=\"25\"></TD> \n";
		echo "    </TR> \n";
	}
?>
<TR BGCOLOR="<?php echo $color2?>" ALIGN="LEFT">
	<TD>Allow others to see your email address:</TD>
	<TD><INPUT TYPE="RADIO" NAME="viewemail" VALUE="1" <?php echo $y?>>Yes <INPUT TYPE="RADIO" NAME="viewemail" VALUE="0" <?php echo $n?>>No</TD>
</TR>
<TR BGCOLOR="<?php echo $color2?>" ALIGN="LEFT">
	<TD>Store username in a cookie for 1 year:</TD>
	<TD><INPUT TYPE="RADIO" NAME="savecookie" VALUE="1" <?php echo $user_cookie?>>Yes <INPUT TYPE="RADIO" NAME="savecookie" VALUE="0" <?php echo $user_nocookie?>>No</TD>
</TR>
<TR BGCOLOR="<?php echo $color2?>" ALIGN="LEFT">
        <TD>Allways attach my signature:</TD>
        <TD><INPUT TYPE="RADIO" NAME="sig" VALUE="1" <?php echo $allways_sig?>>Yes <INPUT TYPE="RADIO" NAME="sig" VALUE="0" <?php echo $no_allways_sig?>>No</TD>
</TR>
<TR BGCOLOR="<?php echo $color2?>" ALIGN="LEFT">
	<TD>Allways disable 'smilies':</TD>
	<TD><INPUT TYPE="RADIO" NAME="smile" VALUE="1" <?php echo $never_smile?>>Yes 
	    <INPUT TYPE="RADIO" NAME="smile" VALUE="0" <?php echo $no_never_smile?>>No</TD>
</TR>	     
<TR BGCOLOR="<?php echo $color2?>" ALIGN="LEFT">
	     <TD>Allways disable HTML:</TD>
	     <TD><INPUT TYPE="RADIO" NAME="dishtml" VALUE="1" <?php echo $never_html?>>Yes
	     <INPUT TYPE="RADIO" NAME="dishtml" VALUE="0" <?php echo $no_never_html?>>No</TD>
</TR>
<TR BGCOLOR="<?php echo $color2?>" ALIGN="LEFT">
	     <TD>Allways disable BBCode:</TD>
	     <TD><INPUT TYPE="RADIO" NAME="disbbcode" VALUE="1" <?php echo $never_bbcode?>>Yes
	     <INPUT TYPE="RADIO" NAME="disbbcode" VALUE="0" <?php echo $no_never_bbcode?>>No</TD>
</TR>
<TR BGCOLOR="<?php echo $color2?>" ALIGN="LEFT">
	<TD>Board Theme:
<?php
		if($allow_theme_create == 1) {
?>
			<BR><font size=-2>Don't have one you like? <a href="createtheme.$phpEx?user=<?php echo $userdata[user_id]?>">Create One</a>
<?php
		}
?>
	</TD>
<?php
	$sql = "SELECT theme_id, theme_name FROM themes ORDER BY theme_name";
	if(!$result = mysql_query($sql, $db))
		die("Error");
	if($myrow = mysql_fetch_array($result)) {
		echo "<TD><SELECT NAME=\"themes\">\n";
		do {
			if($myrow[theme_id] == $userdata["user_theme"])
				$s = "SELECTED";
			echo "<OPTION VALUE=\"$myrow[theme_id]\" $s>$myrow[theme_name]</OPTION>\n";
		} while($myrow = mysql_fetch_array($result));
	}
	else {
		echo "No Themes In database";
	}
?>
	</SELECT></TD>
</TR>
<TR BGCOLOR="<?php echo $color1?>" ALIGN="LEFT">
	<TD COLSPAN="2" ALIGN="CENTER"><INPUT TYPE="HIDDEN" NAME="save" VALUE="1"><INPUT TYPE="HIDDEN" NAME="user" VALUE="<?php echo $user?>">
	<INPUT TYPE="SUBMIT" NAME="submit" VALUE="Save preferences"><INPUT TYPE="RESET" VALUE="Clear">
	</TD>
</TR>
</TABLE></TD></TR></TABLE>
<?php
	}
}
else {
	include('page_header.'.$phpEx);
?>
Please enter your Username and Password to edit your preferences.
<FORM ACTION="<?php echo $PHP_SELF?>" METHOD="POST">
<b>Username: </b><INPUT TYPE="TEXT" NAME="user" SIZE="25" MAXLENGTH="40" VALUE="<?php echo $userdata[username]?>"><BR>
<b>Password: </b><INPUT TYPE="PASSWORD" NAME="passwd" SIZE="25" MAXLENGTH="25"><br>
<INPUT TYPE="SUBMIT" NAME="submit" VALUE="Edit preferences">&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE="RESET" VALUE="Clear">
</FORM>
<?
}
include('page_tail.'.$phpEx);
?>
