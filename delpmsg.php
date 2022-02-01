<?php
/***************************************************************************
                          delpmsg.php  -  description
                             -------------------
    begin                : Wed June 19 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org

    $Id: delpmsg.php,v 1.5 2000/11/15 04:57:15 thefinn Exp $

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
 * delpmsg.php - Nathan Codding
 * - Used for deleting private messages by users of the BB.
 */
include('extention.inc');
include('functions.'.$phpEx);
include('config.'.$phpEx);
require('auth.'.$phpEx);
$pagetitle = "Private Messages";
$pagetype = "privmsgs";
include('page_header.'.$phpEx);


if (!$submit && !$user_logged_in) {
?>

Please enter your username and password to delete the private message
<FORM ACTION="<?php echo $PHP_SELF?>?msgid=<?php echo $msgid?>" METHOD="POST">
<b>Username: </b><INPUT TYPE="TEXT" NAME="username" SIZE="25" MAXLENGTH="40" VALUE="<?php echo $userdata[username]?>"><BR>
<b>Password: </b><INPUT TYPE="PASSWORD" NAME="password" SIZE="25" MAXLENGTH="25"><br><br>
<INPUT TYPE="SUBMIT" NAME="submit" VALUE="Submit">&nbsp;&nbsp;&nbsp;<INPUT TYPE="RESET" VALUE="Clear">
</FORM>

<?php
} else {
	if (!$user_logged_in) {
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
	
		/* throw away user data from the cookie, use username from the form to get new data */
		$userdata = get_userdata($username, $db);
	}

	$sql = "SELECT to_userid FROM priv_msgs WHERE (msg_id = $msgid)";
	$resultID = mysql_query($sql);
	if (!$resultID) {
		echo mysql_error() . "<br>\n";
		die("Error during DB query (checking msg ownership)");
	}
	$row = mysql_fetch_array($resultID);
	if ($userdata[user_id] != $row[to_userid]) {
		die("That's not your message. You can't delete it.");
	}

	$deleteSQL = "DELETE FROM priv_msgs WHERE (msg_id = $msgid)";
	$success = mysql_query($deleteSQL);
	if (!$success) {
		echo mysql_error() . "<br>\n";
		die("Error deleting from DB.");
	}

	echo "Deletion successful. Click <a href=\"$url_phpbb/viewpmsg.$phpEx\">here</a> to go back to your messages.<br><br>\n";

} // if/else (if submit)

require('page_tail.'.$phpEx);
?>
