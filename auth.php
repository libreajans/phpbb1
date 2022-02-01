<?php
/***************************************************************************
                          auth.php  -  description
                             -------------------
    begin                : Sat June 17 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org
  
    $Id: auth.php,v 1.25 2000/12/06 22:33:11 thefinn Exp $

 ***************************************************************************/

/***************************************************************************
 *                                         				                                
 *   This program is free software; you can redistribute it and/or modify  	
 *   it under the terms of the GNU General Public License as published by  
 *   the Free Software Foundation; either version 2 of the License, or	    	
 *   (at your option) any later version.
 *
 ***************************************************************************/
// Make a database connection.
if(!$db = @mysql_connect("$dbhost", "$dbuser", "$dbpasswd"))
	die('<font size=+1>An Error Occured</font><hr>phpBB was unable to connect to the database. <BR>Please check $dbhost, $dbuser, and $dbpasswd in config.php.');
if(!@mysql_select_db("$dbname",$db))
	die("<font size=+1>An Error Occured</font><hr>phpBB was unable to find the database <b>$dbname</b> on your MySQL server. <br>Please make sure you ran the phpBB installation script.");

if(is_banned($REMOTE_ADDR, "ip", $db))
  die("You have been banned from this forum. If you have questions please contact the administrator.");
  
// Setup forum Options.
$sql = "SELECT * FROM config WHERE selected = 1";
if($result = mysql_query($sql, $db)) {
   if($myrow = mysql_fetch_array($result)) {
      $sitename = $myrow["sitename"];
      $allow_html = $myrow["allow_html"];
      $allow_bbcode = $myrow["allow_bbcode"];
      $allow_sig = $myrow["allow_sig"];
      $allow_namechange = $myrow["allow_namechange"];
      $posts_per_page = $myrow["posts_per_page"];
      $hot_threshold = $myrow["hot_threshold"];
      $topics_per_page = $myrow["topics_per_page"];
      $allow_theme_create = $myrow["allow_theme_create"];
      $override_user_themes = $myrow["override_themes"];
      $email_sig = stripslashes($myrow["email_sig"]);
      $email_from = $myrow["email_from"];
      
   }
}
// Check for a cookie on the users's machine.
// If the cookie exists, build an array of the users info and setup the theme.

//Old code for the permanent userid cookie..

if(isset($HTTP_COOKIE_VARS[$cookiename])) {
   $userdata = get_userdata_from_id($HTTP_COOKIE_VARS["$cookiename"], $db);
   if(is_banned($userdata[user_id], "username", $db))
     die("You have been banned from this forum, if you have questions please contact the administrator.");
   $theme = setuptheme($userdata["user_theme"], $db);
   if(!$theme == 0) {
      $bgcolor = $theme["bgcolor"];
      $table_bgcolor = $theme["table_bgcolor"];
      $textcolor = $theme["textcolor"];
      $color1 = $theme["color1"];
      $color2 = $theme["color2"];
      $header_image = $theme["header_image"];
      $newtopic_image = $theme["newtopic_image"];
      $reply_image = $theme["reply_image"];
      $linkcolor = $theme["linkcolor"];
      $vlinkcolor = $theme["vlinkcolor"];
      $FontFace = $theme["fontface"];
      $FontSize1 = $theme["fontsize1"];
      $FontSize2 = $theme["fontsize2"];
      $FontSize3 = $theme["fontsize3"];
      $FontSize4 = $theme["fontsize4"];
      $tablewidth = $theme["tablewidth"];
      $TableWidth = $tablewidth;
      $reply_locked_image = $theme["replylocked_image"];


	}
}

// new code for the session ID cookie..

if(isset($HTTP_COOKIE_VARS[$sesscookiename])) {
	$sessid = $HTTP_COOKIE_VARS[$sesscookiename];
	$userid = get_userid_from_session($sessid, $sesscookietime, $REMOTE_ADDR, $db);

	if (!$userid) {
		$user_logged_in = 0;
	} else {
	   $user_logged_in = 1;
	   update_session_time($sessid, $db);
	   
	   $userdata = get_userdata_from_id($userid, $db);
	   if(is_banned($userdata[user_id], "username", $db))
	     die("You have been banned from this forum, contact the system administrator if you have any questions");
	   $theme = setuptheme($userdata["user_theme"], $db);
	   if(!$theme == 0) {
	      $bgcolor = $theme["bgcolor"];
	      $table_bgcolor = $theme["table_bgcolor"];
	      $textcolor = $theme["textcolor"];
	      $color1 = $theme["color1"];
	      $color2 = $theme["color2"];
	      $header_image = $theme["header_image"];
	      $newtopic_image = $theme["newtopic_image"];
	      $reply_image = $theme["reply_image"];
	      $linkcolor = $theme["linkcolor"];
	      $vlinkcolor = $theme["vlinkcolor"];
	      $FontFace = $theme["fontface"];
	      $FontSize1 = $theme["fontsize1"];
	      $FontSize2 = $theme["fontsize2"];
	      $FontSize3 = $theme["fontsize3"];
	      $FontSize4 = $theme["fontsize4"];
	      $tablewidth = $theme["tablewidth"];
	      $TableWidth = $tablewidth;
	      $reply_locked_image = $theme["replylocked_image"];
	      
	   }		
	} // if/else

}

// Setup the default theme

if($override_user_themes == 1 || !$theme) {
   $sql = "SELECT * FROM themes WHERE theme_default = 1";
   if(!$r = mysql_query($sql, $db))
     die('<font size=+1>An Error Occured</font><hr>phpBB was unable to connect to the database. <BR>Please check $dbhost, $dbuser, and $dbpasswd in config.php.');
   if($theme = mysql_fetch_array($r)) {
      $bgcolor = $theme["bgcolor"];
      $table_bgcolor = $theme["table_bgcolor"];
      $textcolor = $theme["textcolor"];
      $color1 = $theme["color1"];
      $color2 = $theme["color2"];
      $header_image = $theme["header_image"];
      $newtopic_image = $theme["newtopic_image"];
      $reply_image = $theme["reply_image"];
      $linkcolor = $theme["linkcolor"];
      $vlinkcolor = $theme["vlinkcolor"];
      $FontFace = $theme["fontface"];
      $FontSize1 = $theme["fontsize1"];
      $FontSize2 = $theme["fontsize2"];
      $FontSize3 = $theme["fontsize3"];
      $FontSize4 = $theme["fontsize4"];
      $tablewidth = $theme["tablewidth"];
      $TableWidth = $tablewidth;
      $reply_locked_image = $theme["replylocked_image"];
   }
}


// Code for the LastVisit cookie..

if(isset($HTTP_COOKIE_VARS["LastVisit"])) {
	$userdata["lastvisit"] = $HTTP_COOKIE_VARS["LastVisit"];
} else {
	$value = date("Y-m-d H:i");
	$time = (time() + 3600 * 24 * 7 * 52); // one year 'til expiry..
	setcookie("LastVisit", $value, $time, $cookiepath, $cookiedomain, $cookiesecure);
}

list($days, $time) = split(" ", $userdata[lastvisit]);
list($hour, $min) = split(":", $time);
list($year, $month, $day) = explode("-", $days);
$sec = 00;
$last_visit = mktime($hour,$min, $sec, $month, $day, $year);
$now_time = mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y"));

if(($now_time - $last_visit) > 600) {
	$value = date("Y-m-d H:i");
	$time = (time() + 3600 * 24 * 7 * 52);
	setcookie("LastVisit", $value, $time,  $cookiepath, $cookiedomain, $cookiesecure);
}
?>
