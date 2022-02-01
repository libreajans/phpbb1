<?php
/***************************************************************************
                          login.php  -  description
                             -------------------
    begin                : Wed June 19 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org

    $Id: login.php,v 1.8 2000/11/15 04:57:15 thefinn Exp $

 ***************************************************************************/

/***************************************************************************
 *                                         				                                
 *   This program is free software; you can redistribute it and/or modify  	
 *   it under the terms of the GNU General Public License as published by  
 *   the Free Software Foundation; either version 2 of the License, or	    	
 *   (at your option) any later version.
 *
 ***************************************************************************/

/**
 * login.php - Nathan Codding
 * - Used for logging in a user and setting up a session.
 */
include('extention.inc');
include('functions.'.$phpEx);
include('config.'.$phpEx);
require('auth.'.$phpEx);
$pagetitle = "Login";
$pagetype = "other";

/* Note: page_header.php is included later on, because this page needs to be able to send a cookie. */


if (!$submit) {
	include('page_header.'.$phpEx);
?>
	<TABLE BORDER="0" CELLPADDING="1" CELLSPACING=0" ALIGN="CENTER" VALIGN="TOP" WIDTH="<?php echo $TableWidth?>">
	<TR><TD  BGCOLOR="<?php echo $table_bgcolor?>">
	<TABLE BORDER="0" CELLPADDING="1" CELLSPACING=1" WIDTH="100%">
	<TR BGCOLOR="<?php echo $color1?>" ALIGN="LEFT">
	<TD><P><BR><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>">
	Please enter your username and password to login.<ul>
	<FORM ACTION="<?php echo $PHP_SELF?>" METHOD="POST">
	  <b>User Name: </b><INPUT TYPE="TEXT" NAME="username" SIZE="25" MAXLENGTH="40" VALUE="<?php echo $userdata[username]?>"><BR>
	  <b>Password: </b><INPUT TYPE="PASSWORD" NAME="password" SIZE="25" MAXLENGTH="25"><br><br>
	  <INPUT TYPE="SUBMIT" NAME="submit" VALUE="Submit">&nbsp;&nbsp;&nbsp;<INPUT TYPE="RESET" VALUE="Clear"></ul>
	</FORM>
	</TD></TR></TABLE></TD></TR></TABLE>

<?php
} else {
	if ($username == '') {
		die("You have to enter your username. Go back and do so.");
	}
	if ($password == '') {
		die("You have to enter your password. Go back and do so.");
	}
	if (!check_username($username, $db)) {
		die("Invalid username \"$username\". Go back and try again.");
	}
	if (!check_user_pw($username, $password, $db)) {
		die("Invalid password. Go back and try again.");
	}

	/* if we get here, user has entered a good username and password. */

	$userdata = get_userdata($username, $db);

	$sessid = new_session($userdata[user_id], $REMOTE_ADDR, $sesscookietime, $db);	

	set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);

	// Push back to the main index page, no need to tell the user they are logged in, they can figure that out on the index page.
	header("Location: $url_phpbb");
} // if/else (!$submit)


require('page_tail.'.$phpEx);
?>
