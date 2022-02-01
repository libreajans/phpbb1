<?php
/***************************************************************************
                           functions.php  -  description
                             -------------------
    begin                : Sat June 17 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org

    $Id: functions.php,v 1.58 2000/12/09 20:19:09 thefinn Exp $

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
 * Start session-management functions - Nathan Codding, July 21, 2000.
 */

/**
 * new_session()
 * Adds a new session to the database for the given userid.
 * Returns the new session ID.
 * Also deletes all expired sessions from the database, based on the given session lifespan.
 */
function new_session($userid, $remote_ip, $lifespan, $db) {

	mt_srand((double)microtime()*1000000);
	$sessid = mt_rand();
      
	$currtime = (string) (time());
	$expirytime = (string) (time() - $lifespan);

	$deleteSQL = "DELETE FROM sessions WHERE (start_time < $expirytime)";
	$delresult = mysql_query($deleteSQL, $db);

	if (!$delresult) {
		die("Delete failed in new_session()");
	}

	$sql = "INSERT INTO sessions (sess_id, user_id, start_time, remote_ip) VALUES ($sessid, $userid, $currtime, '$remote_ip')";
	
	$result = mysql_query($sql, $db);
	
	if ($result) {
		return $sessid;
	} else {
		echo mysql_errno().": ".mysql_error()."<BR>";
		die("Insert failed in new_session()");
	} // if/else

} // new_session()

/**
 * Sets the sessID cookie for the given session ID. the $cookietime parameter
 * is no longer used, but just hasn't been removed yet. It'll break all the modules
 * (just login) that call this code when it gets removed. 
 * Sets a cookie with no specified expiry time. This makes the cookie last until the
 * user's browser is closed. (at last that's the case in IE5 and NS4.7.. Haven't tried
 * it with anything else.)
 */
function set_session_cookie($sessid, $cookietime, $cookiename, $cookiepath, $cookiedomain, $cookiesecure) {

	// This sets a cookie that will persist until the user closes their browser window.
	// since session expiry is handled on the server-side, cookie expiry time isn't a big deal.
	setcookie($cookiename,$sessid,'',$cookiepath,$cookiedomain,$cookiesecure);

} // set_session_cookie()


/**
 * Returns the userID associated with the given session, based on
 * the given session lifespan $cookietime and the given remote IP
 * address. If no match found, returns 0.
 */
function get_userid_from_session($sessid, $cookietime, $remote_ip, $db) {

	$mintime = time() - $cookietime;
	$sql = "SELECT user_id FROM sessions WHERE (sess_id = $sessid) AND (start_time > $mintime) AND (remote_ip = '$remote_ip')";
	$result = mysql_query($sql, $db);
	if (!$result) {
		echo mysql_error() . "<br>\n";
		die("Error doing DB query in get_userid_from_session()");
	}
	$row = mysql_fetch_array($result);
	
	if (!$row) {
		return 0;
	} else {
		return $row[user_id];
	}
	
} // get_userid_from_session()

/**
 * Refresh the start_time of the given session in the database.
 * This is called whenever a page is hit by a user with a valid session.
 */
function update_session_time($sessid, $db) {
	
	$newtime = (string) time();
	$sql = "UPDATE sessions SET start_time=$newtime WHERE (sess_id = $sessid)";
	$result = mysql_query($sql, $db);
	if (!$result) {
		echo mysql_error() . "<br>\n";
		die("Error doing DB update in update_session_time()");
	}
	return 1;

} // update_session_time()

/**
 * Delete the given session from the database. Used by the logout page.
 */
function end_user_session($userid, $db) {

	$sql = "DELETE FROM sessions WHERE (user_id = $userid)";
	$result = mysql_query($sql, $db);
	if (!$result) {
		echo mysql_error() . "<br>\n";
		die("Delete failed in end_user_session()");
	}
	return 1;
	
} // end_session()

/**
 * Prints either "logged in as [username]. Log out." or 
 * "Not logged in. Log in.", depending on the value of
 * $user_logged_in.
 */
function print_login_status($user_logged_in, $username, $url_phpbb) {
	global $phpEx;
	if($user_logged_in) {
		echo "<b>Logged in as $username. <a href=\"$url_phpbb/logout.$phpEx\">Log out.</a></b><br>\n";
	} else {
		echo "<b>Not logged in. <a href=\"$url_phpbb/login.$phpEx\">Log in.</a></b><br>\n";
	}
} // print_login_status()

/**
 * Prints a link to either login.php or logout.php, depending
 * on whether the user's logged in or not.
 */
function make_login_logout_link($user_logged_in, $url_phpbb) {
	global $phpEx;
	if ($user_logged_in) {
		$link = "<a href=\"$url_phpbb/logout.$phpEx\">Logout</a>";
	} else {
		$link = "<a href=\"$url_phpbb/login.$phpEx\">Login</a>";
	}
	return $link;
} // make_login_logout_link()

/**
 * End session-management functions
 */

/*
 * Gets the total number of topics in a form
 */
function get_total_topics($forum_id, $db) {
	$sql = "SELECT count(*) AS total FROM topics WHERE forum_id = '$forum_id'";
	if(!$result = mysql_query($sql, $db))
		return("ERROR");
	if(!$myrow = mysql_fetch_array($result))
		return("ERROR");
	
	return($myrow[total]);
}
/*
 * Shows the 'header' data from the header/meta/footer table
 */
function showheader($db) {
        $sql = "SELECT header FROM headermetafooter";
        if($result = mysql_query($sql, $db)) {
	        if($header = mysql_fetch_array($result)) {
		        echo stripslashes($header[header]);
		}
	}
}
/*
 * Shows the meta information from the header/meta/footer table
 */
function showmeta($db) {
        $sql = "SELECT meta FROM headermetafooter";
        if($result = mysql_query($sql, $db)) {
	        if($meta = mysql_fetch_array($result)) {
	                echo stripslashes($meta[meta]);
		}
	}
}
/*
 * Show the footer from the header/meta/footer table
 */
function showfooter($db) {
        $sql = "SELECT footer FROM headermetafooter";
        if($result = mysql_query($sql, $db)) {
	        if($footer = mysql_fetch_array($result)) {
		        echo stripslashes($footer[footer]);
		}
	}
} 

/*
 * Used to keep track of all the people viewing the forum at this time
 * Anyone who's been on the board within the last 300 seconds will be 
 * returned. Any data older then 300 seconds will be removed
 */
function get_whosonline($IP, $username, $forum, $db) {
	if($username == '')
		$username = "Guest";

	$time= explode(  " ", microtime());
	$userusec= (double)$time[0];
	$usersec= (double)$time[1];
	$deleteuser= mysql_query( "delete from whosonline where date < $usersec - 300", $db);
	$userlog= mysql_fetch_row(MYSQL_QUERY( "SELECT * FROM whosonline where IP = '$IP'", $db));
	if($userlog == false) {
		$ok= @mysql_query( "insert INTO whosonline (ID,IP,DATE,username,forum) VALUES('$User_Id','$IP','$usersec', '$username', '$forum')", $db)or die( "Unable to query db!");
	}
	$resultlogtab   = mysql_query("SELECT Count(*) as total FROM whosonline", $db);
	$numberlogtab   = mysql_fetch_array($resultlogtab);
	return($numberlogtab[total]);

}

/*
 * Returns the total number of posts in the whole system, a forum, or a topic
 * Also can return the number of users on the system.
 */ 
function get_total_posts($id, $db, $type) {
   switch($type) {
    case 'users':
      $sql = "SELECT count(*) AS total FROM users WHERE user_id != -1";
      break;
    case 'all':
      $sql = "SELECT count(*) AS total FROM posts";
      break;
    case 'forum':
      $sql = "SELECT count(*) AS total FROM posts WHERE forum_id = '$id'";
      break;
    case 'topic':
      $sql = "SELECT count(*) AS total FROM posts WHERE topic_id = '$id'";
      break;
   // Old, we should never get this.   
    case 'user':
      die("Should be using the users.user_posts column for this.");
   }
   if(!$result = mysql_query($sql, $db))
     return("ERROR");
   if(!$myrow = mysql_fetch_array($result))
     return("0");
   
   return($myrow[total]);
   
}

/*
 * Returns the most recent post in a forum, or a topic
 */
function get_last_post($id, $db, $type) {
   switch($type) {
    case 'forum':
      $sql = "SELECT p.post_time, p.poster_id, u.username FROM posts p, users u WHERE p.forum_id = '$id' AND p.poster_id = u.user_id ORDER BY post_time DESC";
      break;
    case 'topic':
      $sql = "SELECT p.post_time, u.username FROM posts p, users u WHERE p.topic_id = '$id' AND p.poster_id = u.user_id ORDER BY post_time DESC";
      break;
    case 'user':
      $sql = "SELECT p.post_time FROM posts p WHERE p.poster_id = '$id'";
      break;
   }
   if(!$result = mysql_query($sql, $db))
     return("ERROR");
   
   if(!$myrow = mysql_fetch_array($result))
     return("No posts");
   if($type != "user")
     $val = sprintf("%s <br> by %s", $myrow[post_time], $myrow[username]);
   else
     $val = $myrow[post_time];
   
   return($val);
}

/*
 * Returns an array of all the moderators of a forum
 */
function get_moderators($forum_id, $db) {
   $sql = "SELECT u.user_id, u.username FROM users u, forum_mods f WHERE f.forum_id = '$forum_id' and f.user_id = u.user_id";
    if(!$result = mysql_query($sql, $db))
     return("-1");
   if(!$myrow = mysql_fetch_array($result))
     return("-1");
   do {
      $array[] = array("$myrow[user_id]" => "$myrow[username]");
   } while($myrow = mysql_fetch_array($result));
   return($array);
}

/*
 * Checks if a user (user_id) is a moderator of a perticular forum (forum_id)
 * Retruns 1 if TRUE, 0 if FALSE or Error
 */
function is_moderator($forum_id, $user_id, $db) {
   $sql = "SELECT user_id FROM forum_mods WHERE forum_id = '$forum_id' AND user_id = '$user_id'";
   if(!$result = mysql_query($sql, $db))
     return("0");
   if(!$myrow = mysql_fetch_array($result))
     return("0");
   if($myrow[user_id] != '')
     return("1");
   else
     return("0");
}

/**
 * Nathan Codding - July 19, 2000
 * Checks the given password against the DB for the given username. Returns true if good, false if not.
 */
function check_user_pw($username, $password, $db) {
	$password = md5($password);
	$sql = "SELECT user_id FROM users WHERE (username = '$username') AND (user_password = '$password')";
	$resultID = mysql_query($sql, $db);
	if (!$resultID) {
		echo mysql_error() . "<br>";
		die("Error doing DB query in check_user_pw()");
	}
	return mysql_num_rows($resultID);
} // check_user_pw()


/**
 * Nathan Codding - July 19, 2000
 * Returns a count of the given userid's private messages.
 */
function get_pmsg_count($user_id, $db) {
	$sql = "SELECT msg_id FROM priv_msgs WHERE (to_userid = $user_id)";
	$resultID = mysql_query($sql);
	if (!$resultID) {
		echo mysql_error() . "<br>";
		die("Error doing DB query in get_pmsg_count");
	}
	return mysql_num_rows($resultID);
} // get_pmsg_count()


/**
 * Nathan Codding - July 19, 2000
 * Checks if a given username exists in the DB. Returns true if so, false if not.
 */
function check_username($username, $db) {
	$sql = "SELECT user_id FROM users WHERE (username = '$username') AND (user_level != '-1')";
	$resultID = mysql_query($sql);
	if (!$resultID) {
		echo mysql_error() . "<br>";
		die("Error doing DB query in check_username()");
	}
	return mysql_num_rows($resultID);
} // check_username()


/**
 * Nathan Codding, July 19/2000
 * Get a user's data, given their user ID. 
 */

function get_userdata_from_id($userid, $db) {
	
	$sql = "SELECT * FROM users WHERE user_id = $userid";
	if(!$result = mysql_query($sql, $db)) {
		$userdata = array("error" => "1");
		return ($userdata);
	}
	if(!$myrow = mysql_fetch_array($result)) {
		$userdata = array("error" => "1");
		return ($userdata);
	}
	
	return($myrow);
}

/* 
 * Gets user's data based on their username
 */
function get_userdata($username, $db) {
	$sql = "SELECT * FROM users WHERE username = '$username' AND user_level != -1";
	if(!$result = mysql_query($sql, $db))
		$userdata = array("error" => "1");
	if(!$myrow = mysql_fetch_array($result))
		$userdata = array("error" => "1");
	
	return($myrow);
}

/*
 * Returns all the rows in the themes table
 */
function setuptheme($theme, $db) {
	$sql = "SELECT * FROM themes WHERE theme_id = '$theme'";
	if(!$result = mysql_query($sql, $db))
		return(0);
	if(!$myrow = mysql_fetch_array($result))
		return(0);
	return($myrow);
}

/*
 * Checks if a forum or a topic exists in the database. Used to prevent
 * users from simply editing the URL to post to a non-existant forum or topic
 */
function does_exists($id, $db, $type) {
	switch($type) {
		case 'forum':
			$sql = "SELECT forum_id FROM forums WHERE forum_id = '$id'";
		break;
		case 'topic':
			$sql = "SELECT topic_id FROM topics WHERE topic_id = '$id'";
		break;
	}
	if(!$result = mysql_query($sql, $db))
		return(0);
	if(!$myrow = mysql_fetch_array($result)) 
		return(0);
	return(1);
}

/*
 * Checks if a topic is locked
 */
function is_locked($topic, $db) {
	$sql = "SELECT topic_status FROM topics WHERE topic_id = '$topic'";
	if(!$r = mysql_query($sql, $db))
		return(FALSE);
	if(!$m = mysql_fetch_array($r))
		return(FALSE);
	if($m[topic_status] == 1)
		return(TRUE);
	else
		return(FALSE);
}

/*
 * Changes :) to an <IMG> tag based on the smiles table in the database
 * TODO: Get rid of global variables.
 */
function smile($message) {
   global $db, $url_smiles;
   
   if ($getsmiles = mysql_query("SELECT * FROM smiles")){
      while ($smiles = mysql_fetch_array($getsmiles)) {
	 $message = str_replace($smiles[code], "<IMG SRC=\"$url_smiles/$smiles[smile_url]\">", $message);
      }
   }
   return($message);
}

/*
 * Changes a Smiliy <IMG> tag into its corrasponding smile
 * TODO: Get rid of golbal variables, and implement a method of distinguishing between :D and :grin: using the <IMG> tag
 */
function desmile($message) {
   // Ick Ick Global variables...remind me to fix these! - theFinn
   global $db, $url_smiles;
   
   if ($getsmiles = mysql_query("SELECT * FROM smiles")){
      while ($smiles = mysql_fetch_array($getsmiles)) {
	 $message = str_replace("<IMG SRC=\"$url_smiles/$smiles[smile_url]\">", $smiles[code], $message);
      }
   }
   return($message);
}

/**
 * bbdecode/bbencode functions:
 * Rewritten - Nathan Codding - Aug 24, 2000
 * Using Perl-Compatible regexps now. Won't kill special chars 
 * outside of a [code]...[/code] block now, and all BBCode tags
 * are implemented.
 * Note: the "i" matching switch is used, so BBCode tags are 
 * case-insensitive.
 */
function bbdecode($message) {
				
		// Undo [code]
		$message = preg_replace("#<!-- BBCode Start --><TABLE BORDER=0 ALIGN=CENTER WIDTH=85%><TR><TD><font size=-1>Code:</font><HR></TD></TR><TR><TD><FONT SIZE=-1><PRE>(.*?)</PRE></FONT></TD></TR><TR><TD><HR></TD></TR></TABLE><!-- BBCode End -->#s", "[code]\\1[/code]", $message);
				
		// Undo [quote]						
		$message = preg_replace("#<!-- BBCode Quote Start --><TABLE BORDER=0 ALIGN=CENTER WIDTH=85%><TR><TD><font size=-1>Quote:</font><HR></TD></TR><TR><TD><FONT SIZE=-1><BLOCKQUOTE>(.*?)</BLOCKQUOTE></FONT></TD></TR><TR><TD><HR></TD></TR></TABLE><!-- BBCode Quote End -->#s", "[quote]\\1[/quote]", $message);
		
		// Undo [b] and [i]
		$message = preg_replace("#<!-- BBCode Start --><B>(.*?)</B><!-- BBCode End -->#s", "[b]\\1[/b]", $message);
		$message = preg_replace("#<!-- BBCode Start --><I>(.*?)</I><!-- BBCode End -->#s", "[i]\\1[/i]", $message);
		
		// Undo [url] (both forms)
		$message = preg_replace("#<!-- BBCode Start --><A HREF=\"http://(.*?)\" TARGET=\"_blank\">(.*?)</A><!-- BBCode End -->#s", "[url=\\1]\\2[/url]", $message);
		
		// Undo [email]
		$message = preg_replace("#<!-- BBCode Start --><A HREF=\"mailto:(.*?)\">(.*?)</A><!-- BBCode End -->#s", "[email]\\1[/email]", $message);
		
		// Undo [img]
		$message = preg_replace("#<!-- BBCode Start --><IMG SRC=\"(.*?)\"><!-- BBCode End -->#s", "[img]\\1[/img]", $message);
		
		// Undo lists (unordered/ordered)
	
		// unordered list code..
		$matchCount = preg_match_all("#<!-- BBCode ulist Start --><UL>(.*?)</UL><!-- BBCode ulist End -->#s", $message, $matches);
	
		for ($i = 0; $i < $matchCount; $i++)
		{
			$currMatchTextBefore = preg_quote($matches[1][$i]);
			$currMatchTextBefore = escape_slashes($currMatchTextBefore);
			$currMatchTextAfter = preg_replace("#<LI>#s", "[*]", $matches[1][$i]);
		
			$message = preg_replace("#<!-- BBCode ulist Start --><UL>$currMatchTextBefore</UL><!-- BBCode ulist End -->#s", "[list]" . $currMatchTextAfter . "[/list]", $message);
		}
		
		// ordered list code..
		$matchCount = preg_match_all("#<!-- BBCode olist Start --><OL TYPE=([A1])>(.*?)</OL><!-- BBCode olist End -->#si", $message, $matches);
		
		for ($i = 0; $i < $matchCount; $i++)
		{
			$currMatchTextBefore = preg_quote($matches[2][$i]);
			$currMatchTextBefore = escape_slashes($currMatchTextBefore);
			$currMatchTextAfter = preg_replace("#<LI>#s", "[*]", $matches[2][$i]);
			
			$message = preg_replace("#<!-- BBCode olist Start --><OL TYPE=([A1])>$currMatchTextBefore</OL><!-- BBCode olist End -->#si", "[list=\\1]" . $currMatchTextAfter . "[/list]", $message);
		}	
		
		return($message);
}

function bbencode($message) {

	// [CODE] and [/CODE] for posting code (HTML, PHP, C etc etc) in your posts.
	$matchCount = preg_match_all("#\[code\](.*?)\[/code\]#si", $message, $matches);
	
	for ($i = 0; $i < $matchCount; $i++)
	{
		$currMatchTextBefore = preg_quote($matches[1][$i]);
		$currMatchTextBefore = escape_slashes($currMatchTextBefore);
		$currMatchTextAfter = htmlspecialchars($matches[1][$i]);
		
		$message = preg_replace("/\[code\]$currMatchTextBefore\[\/code\]/si", "<!-- BBCode Start --><TABLE BORDER=0 ALIGN=CENTER WIDTH=85%><TR><TD><font size=-1>Code:</font><HR></TD></TR><TR><TD><FONT SIZE=-1><PRE>$currMatchTextAfter</PRE></FONT></TD></TR><TR><TD><HR></TD></TR></TABLE><!-- BBCode End -->", $message);
	}

	// [QUOTE] and [/QUOTE] for posting replies with quote, or just for quoting stuff.	
	$message = preg_replace("/\[quote\](.*?)\[\/quote]/si", "<!-- BBCode Quote Start --><TABLE BORDER=0 ALIGN=CENTER WIDTH=85%><TR><TD><font size=-1>Quote:</font><HR></TD></TR><TR><TD><FONT SIZE=-1><BLOCKQUOTE>\\1</BLOCKQUOTE></FONT></TD></TR><TR><TD><HR></TD></TR></TABLE><!-- BBCode Quote End -->", $message);
	
	// [b] and [/b] for bolding text.
	$message = preg_replace("/\[b\](.*?)\[\/b\]/si", "<!-- BBCode Start --><B>\\1</B><!-- BBCode End -->", $message);
	
	// [i] and [/i] for italicizing text.
	$message = preg_replace("/\[i\](.*?)\[\/i\]/si", "<!-- BBCode Start --><I>\\1</I><!-- BBCode End -->", $message);
	
	// [url]www.phpbb.com[/url] code..
	$message = preg_replace("/\[url\](http:\/\/)?(.*?)\[\/url\]/si", "<!-- BBCode Start --><A HREF=\"http://\\2\" TARGET=\"_blank\">\\2</A><!-- BBCode End -->", $message);
	
	// [url=www.phpbb.com]phpBB[/url] code..
	$message = preg_replace("/\[url=(http:\/\/)?(.*?)\](.*?)\[\/url\]/si", "<!-- BBCode Start --><A HREF=\"http://\\2\" TARGET=\"_blank\">\\3</A><!-- BBCode End -->", $message);
	
	// [email]user@domain.tld[/email] code..
	$message = preg_replace("/\[email\](.*?)\[\/email\]/si", "<!-- BBCode Start --><A HREF=\"mailto:\\1\">\\1</A><!-- BBCode End -->", $message);
	
	// [img]image_url_here[/img] code..
	$message = preg_replace("/\[img\](.*?)\[\/img\]/si", "<!-- BBCode Start --><IMG SRC=\"\\1\"><!-- BBCode End -->", $message);
	
	// unordered list code..
	$matchCount = preg_match_all("/\[list\](.*?)\[\/list\]/si", $message, $matches);
	
	for ($i = 0; $i < $matchCount; $i++)
	{
		$currMatchTextBefore = preg_quote($matches[1][$i]);
		$currMatchTextBefore = escape_slashes($currMatchTextBefore);
		$currMatchTextAfter = preg_replace("#\[\*\]#si", "<LI>", $matches[1][$i]);
		
		$message = preg_replace("/\[list\]$currMatchTextBefore\[\/list\]/si", "<!-- BBCode ulist Start --><UL>$currMatchTextAfter</UL><!-- BBCode ulist End -->", $message);
	}
	
	// ordered list code..
	$matchCount = preg_match_all("/\[list=([a1])\](.*?)\[\/list\]/si", $message, $matches);
	
	for ($i = 0; $i < $matchCount; $i++)
	{
		$currMatchTextBefore = preg_quote($matches[2][$i]);
		$currMatchTextBefore = escape_slashes($currMatchTextBefore);
		$currMatchTextAfter = preg_replace("\\[\*\]/si", "<LI>", $matches[2][$i]);
		
		$message = preg_replace("/\[list=([a1])\]$currMatchTextBefore\[\/list\]/si", "<!-- BBCode olist Start --><OL TYPE=\\1>$currMatchTextAfter</OL><!-- BBCode olist End -->", $message);
	}
		
	return($message);
}


/**
 * Nathan Codding - Oct. 30, 2000
 *
 * Escapes the "/" character with "\/". This is useful when you need
 * to stick a runtime string into a PREG regexp that is being delimited 
 * with slashes.
 */
function escape_slashes($input)
{
	$output = preg_replace("#/#", "\/", $input);
	return $output;
}

/*
 * Returns the name of the forum based on ID number
 */
function get_forum_name($forum_id, $db) {
	$sql = "SELECT forum_name FROM forums WHERE forum_id = '$forum_id'";
	if(!$r = mysql_query($sql, $db))
		return("ERROR");
	if(!$m = mysql_fetch_array($r))
		return("None");
	return($m[forum_name]);
}


/**
 * Modified by Nathan Codding - July 20, 2000.
 * Made it only work on URLs and e-mail addresses preceeded by a space, in order to stop
 * mangling HTML code.
 *
 * The Following function was taken from the Scriplets area of http://www.phpwizard.net, and was written by Tobias Ratschiller.
 *   Visit phpwizard.net today, its an excellent site! 
 */

function make_clickable($text) {
	$ret = eregi_replace(" ([[:alnum:]]+)://([^[:space:]]*)([[:alnum:]#?/&=])", " <a href=\"\\1://\\2\\3\" target=\"_blank\" target=\"_new\">\\1://\\2\\3</a>", $text);
        $ret = eregi_replace(" (([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)([[:alnum:]-]))", " <a href=\"mailto:\\1\" target=\"_new\">\\1</a>", $ret);
        return($ret);
}

/**
 * Nathan Codding - August 24, 2000.
 * Takes a string, and does the reverse of the PHP standard function
 * htmlspecialchars().
 */
function undo_htmlspecialchars($input) {
	$input = preg_replace("/&gt;/i", ">", $input);
	$input = preg_replace("/&lt;/i", "<", $input);
	$input = preg_replace("/&quot;/i", "\"", $input);
	$input = preg_replace("/&amp;/i", "&", $input);
	
	return $input;
}
/*
 * Make sure a username isn't on the disallow list
 */
function validate_username($username, $db) {
	$sql = "SELECT disallow_username FROM disallow WHERE disallow_username = '" . addslashes($username) . "'";
	if(!$r = mysql_query($sql, $db))
		return(0);
	if($m = mysql_fetch_array($r)) {
		if($m[disallow_username] == $username)
			return(1);
		else
			return(0);
	}
	return(0);
}
/*
 * Check if this is the first post in a topic. Used in editpost.php
 */
function is_first_post($topic_id, $post_id, $db) {
   $sql = "SELECT post_id FROM posts WHERE topic_id = '$topic_id' ORDER BY post_id LIMIT 1";
   if(!$r = mysql_query($sql, $db))
     return(0);
   if(!$m = mysql_fetch_array($r))
     return(0);
   if($m[post_id] == $post_id)
     return(1);
   else
     return(0);
}

/*
 * Replaces banned words in a string with their replacements
 */
function censor_string($string, $db) {
   $sql = "SELECT word, replacement FROM words";
   if(!$r = mysql_query($sql, $db))
      die("Error, could not contact the database! Please check your database settings in config.$phpEx");
   while($w = mysql_fetch_array($r)) {
      $word = stripslashes($w[word]);
      $replacement = stripslashes($w[replacement]);
      $string = eregi_replace(" $word", " $replacement", $string);
      $string = eregi_replace("^$word", "$replacement", $string);
      $string = eregi_replace("<BR>$word", "<BR>$replacement", $string);
   }
   return($string);
}

function is_banned($ipuser, $type, $db) {
   
   // Remove old bans
   $sql = "DELETE FROM banlist WHERE (ban_end < ". mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")).") AND (ban_end > 0)";
   @mysql_query($sql, $db);
   
   switch($type) {
    case "ip":
      $sql = "SELECT ban_ip FROM banlist";
      if($r = mysql_query($sql, $db)) {
	 while($iprow = mysql_fetch_array($r)) {
	    $ip = $iprow[ban_ip];
	    if($ip[strlen($ip) - 1] == ".") {
	       $db_ip = explode(".", $ip);
	       $this_ip = explode(".", $ipuser);
	       
	       for($x = 0; $x < count($db_ip) - 1; $x++) 
		 $my_ip .= $this_ip[$x] . ".";

	       if($my_ip == $ip)
		 return(TRUE);
	    }
	    else {
	       if($ipuser == $ip)
		 return(TRUE);
	    }
	 }
      }
      else 
	return(FALSE);
      break;
    case "username":
      $sql = "SELECT ban_userid FROM banlist WHERE ban_userid = '$ipuser'";
      if($r = mysql_query($sql, $db)) {
	 if(mysql_num_rows($r) > 0)
	   return(TRUE);
      }
      break;
   }
   
   return(FALSE);
}


?>
