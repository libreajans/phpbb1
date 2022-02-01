<?php
/***************************************************************************
                            editpost.php  -  description
                             -------------------
    begin                : Sat June 17 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org

    $Id: editpost.php,v 1.42 2000/12/06 22:33:11 thefinn Exp $

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
$pagetitle = "Edit Post";
$pagetype = "index";

if($submit) {
   $posterdata = get_userdata_from_id($poster_id, $db);
   $date = date("Y-m-d H:i");
   if ($user_logged_in) {
      // valid session.. just check it's the right user.
      if($userdata[user_id] != $posterdata[user_id]) {
	 if ($userdata[user_level] == 1) {
	    include('page_header.'.$phpEx);
	    $die = 1;
	 }
	 else if($userdata[user_level] == 2 && !is_moderator($forum_id, $userdata[user_id], $db)) {
	    include('page_header.'.$phpEx);
	    die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>You can't edit a post that's not yours.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
	 }
      }
   }
   else {
      $userdata = get_userdata($username, $db);
      if(is_banned($userdata[user_id], "username", $db))
	die("You have been banned from this forum. Contact the system administrator if you have any quesions.");

      $md_passwd = md5($passwd);
      if($posterdata[user_id] == $userdata[user_id]) {
	 if($md_passwd != $posterdata[user_password]) {
	    $die = 1;
	 }
      }
      else if($userdata[user_level] == 2 && is_moderator($forum_id, $userdata[user_id], $db)) {
	 if($md_passwd != $userdata[user_password]) {
	    $die = 1;
	 }
      }
      else if($userdata[user_level] > 2) {
	 if($md_passwd != $userdata[user_password]) {
	    $die = 1;
	 }
      }
      else {
	 $die = 1;
      }
      if($die != 1) {
	 // You've entered your username and password, and no problems have been found, log you in!
	 $sessid = new_session($userdata[user_id], $REMOTE_ADDR, $sesscookietime, $db);
	 set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);
      }
   }
   if($die == 1) {
      include("page_header.$phpEx");
      die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>You did not supply the correct password or do not have permission to edit this post. Please go back and try again.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
   }
   // IF we made it this far we are allowed to edit this message, yay!
    
   if($allow_html == 0 || isset($html) )
     $message = htmlspecialchars($message);
   if($allow_bbcode == 1 && !isset($bbcode))
     $message = bbencode($message);
   if(!$smile) 
     $message = smile($message);

   $message = str_replace("\n", "<BR>", $message);

   $message .= "<BR><BR><font size=-1>[ This message was edited by: $username on $date ]</font>";
   $message = censor_string($message, $db);
   
   $message = addslashes($message);
   if(!$delete) {
      $forward = 1;
      $topic = $topic_id;
      $forum = $forum_id;
      include("page_header.$phpEx");
      $sql = "UPDATE posts SET post_text = '$message' WHERE (post_id = '$post_id')";
      if(!$result = mysql_query($sql, $db))
	die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewdith\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>Could not connect to the database. Please check your config file.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
      if(isset($subject)) {
	 if(!isset($notify))
	   $notify = 0;
	 else
	   $notify = 1;
	 $subject = censor_string($subject, $db);
	 $subject = addslashes($subject);
	 $sql = "UPDATE topics SET topic_title = '$subject', topic_notify = '$notify' WHERE topic_id = '$topic_id'";
	  if(!$result = mysql_query($sql, $db))
	   die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>Could not connect to the database. Please check your config file.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
      }
      echo "<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\">";
      echo "<TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\">";
      echo "<TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><font face=\"Verdana\" size=\"2\"><P>";
      echo "<P><BR><center>Your post has been updated.<ul>Click <a href=\"viewtopic.$phpEx?topic=$topic_id&forum=$forum_id\">here</a> to view the update.<P>Or click <a href=\"viewforum.$phpEx?forum=$forum_id\">here</a> to return to the forum topic listing.</ul></center><P></font>";
      echo "</TD></TR></TABLE></TD></TR></TABLE><br>";
   }
   else {
      $sql = "DELETE FROM posts WHERE post_id = '$post_id'";
      if(!$r = mysql_query($sql, $db))
	die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>Could not connect to the database. Please check your config file.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
      if(get_total_posts($topic_id, $db, "topic") == 0) {
	 $sql = "DELETE FROM topics WHERE topic_id = '$topic_id'";
	 if(!$r = mysql_query($sql, $db))
	   die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>Could not connect to the database. Please check your config file.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
      }
      echo "<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\">";
      echo "<TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\">";
      echo "<TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><font face=\"Verdana\" size=\"2\"><P>";
      echo "<P><BR><center>Your post has been deleted. Click <a href=\"viewforum.$phpEx?forum=$forum_id\">here</a> to return to the forum topic listing. Or click <a href=\"index.$phpEx\">here</a> to return to the forum index</center><P></font>";
      echo "</TD></TR></TABLE></TD></TR></TABLE><br>";
   }	
}
else {
   include('page_header.'.$phpEx);
   $sql = "SELECT p.*, u.username, u.user_id, u.user_sig, t.topic_title, t.topic_notify FROM posts p, users u, topics t WHERE (p.post_id = '$post_id') AND (p.topic_id = t.topic_id) AND (p.poster_id = u.user_id)";
   if(!$result = mysql_query($sql, $db))
     die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>Could not connect to the database. Please check your config file.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
   $myrow = mysql_fetch_array($result);
   // Freekin' ugly but I couldn't get it to work right as 1 big if 
   //          - James
   if ($user_logged_in) {
      if($userdata[user_level] <= 2) {
	 if($userdata[user_level] == 2 && !is_moderator($forum, $userdata[user_id], $db)) {
	    if($userdata[user_level] < 2 && ($userdata[user_id] != $myrow[user_id]))
	      die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>You can't edit a post that's not yours.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
	 }
      }
   }

   $message = $myrow[post_text];
   if(eregi("\[addsig]$", $message))
     $addsig = 1;
   else
     $addsig = 0;
   $message = eregi_replace("\[addsig]$", "\n-----------------\n" . $myrow[user_sig], $message);   
   $message = str_replace("<BR>", "\n", $message);
   $message = stripslashes($message);
   $message = desmile($message);
   $message = bbdecode($message);
   $message = undo_htmlspecialchars($message);
   list($day, $time) = split(" ", $myrow[post_time]);
?>
<FORM ACTION="<?php echo $PHP_SELF?>" METHOD="POST">
<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" ALIGN="CENTER" VALIGN="TOP" WIDTH="<?php echo $tablewidth?>"><TR><TD  BGCOLOR="<?php echo $table_bgcolor?>">
<TABLE BORDER="0" CELLPADDING="3" CELLSPACING="1" WIDTH="100%">
<TR BGCOLOR="<?php echo $color1?>" ALIGN="LEFT">
	<TD ALIGN="CENTER" COLSPAN="2">Editing Post</TD>
</TR>
<?php
     if(!$user_logged_in) {
?>
<TR>
	<TD BGCOLOR="<?php echo $color1?>">Username:</TD>
	<TD BGCOLOR="<?php echo $color2?>"><input type="text" name="username" value="<?php echo $userdata[username]?>"></TD>
</TR>	  
<?PHP
     }
   else {
?>
	<TD BGCOLOR="<?php echo $color1?>">Username:</TD>
	<TD BGCOLOR="<?php echo $color2?>"><?php echo $userdata[username]?></TD>
<?php
   }
	if (!$user_logged_in) {
		// ask for a password..
	   echo "<TR> \n";
	   echo "<TD BGCOLOR=\"$color1\">Password:<BR><font size=\"$FontSize3\"><i>(Lost your password? <a href=\"sendpassword.$phpEx\" target=\"_blank\">Click Here</a>)</i></font></TD>";
	   echo "<TD BGCOLOR=\"$color2\"><INPUT TYPE=\"PASSWORD\" NAME=\"passwd\" SIZE=\"25\" MAXLENGTH=\"25\"></TD> \n";
	   echo "</TR> \n";
	}
   $first_post = is_first_post($topic, $post_id, $db);
   if($first_post) {
?>
<TR>
	<TD BGCOLOR="<?php echo $color1?>" width=25%><b>Subject:</b></TD>
	<TD BGCOLOR="<?php echo $color2?>"><INPUT TYPE="TEXT" NAME="subject"  SIZE="50" MAXLENGTH="100" VALUE="<?php echo stripslashes($myrow[topic_title])?>"></TD>
</TR>
<?php
   }
?>
<TR>
     <TD  BGCOLOR="<?php echo $color1?>" width=25%><b>Message:</b><br><br>
     <font size=-1>
<?php
     echo "HTML is: ";
   if($allow_html == 1)
     echo "On<BR>\n";
   else
     echo "Off<BR>\n";
   echo "<a href=\"bbcode_ref.$phpEx\" TARGET=\"blank\">BBCode</a> is: ";
   if($allow_bbcode == 1)
     echo "On<br>\n";
   else
     echo "Off<BR>\n";
?>
     </font></TD>
     <TD BGCOLOR="<?php echo $color2?>"><TEXTAREA NAME="message" ROWS=10 COLS=45 WRAP="VIRTUAL"><?php echo $message?></TEXTAREA></TD>
</TR>
<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width=25%><b>Options:</b></TD>
		<TD  BGCOLOR="<?php echo $color2?>" >
		<?php
			$now_hour = date("H");
			$now_min = date("i");
			list($hour, $min) = split(":", $time);
			if((($now_hour == $hour && $min_now - 30 < $min) || ($now_hour == $hour +1 && $now_min - 30 > 0)) || ($userdata[user_level] > 2 || is_moderator($forum, $userdata[user_id], $db))) {
		?>
				<INPUT TYPE="CHECKBOX" NAME="delete">Delete this Post<BR>
		<?php
			}
		
			if($allow_html == 1) {
			   if($userdata[user_html] == 1)
			     $h = "CHECKED";
		?>	
				<INPUT TYPE="CHECKBOX" NAME="html" <?php echo $h?>>Disable HTML on this Post<BR>
		<?php
			}
		
			if($allow_bbcode == 1) {
			   if($userdata[user_bbcode] == 1)
			     $b = "CHECKED";
		?>	
				<INPUT TYPE="CHECKBOX" NAME="bbcode" <?php echo $b?>>Disable BBCode on this Post<BR>
		<?php
			}
                       if($userdata[user_desmile] == 1)
                          $ds = "CHECKED";
		?>

		<INPUT TYPE="CHECKBOX" NAME="smile" <?php echo $ds?>>Disable smilies on this post.<BR>
                 <?php
                 if($first_post) {
		    if($myrow[topic_notify] == 1)
		      $chk = "CHECKED";
		 ?>
		 <INPUT TYPE="CHECKBOX" NAME="notify" <?php echo $chk?>>Notify by email when replies are posted
		 <?php
		 }
                 ?>
            </TD>
	</TR>
<TR>
	<TD  BGCOLOR="<?php echo $color1?>" colspan=2 ALIGN="CENTER">
<?php if($user_logged_in) {
?>
     <INPUT TYPE="HIDDEN" NAME="username" VALUE="<?php echo $userdata[username]?>">
<?php
}
?>
        <INPUT TYPE="HIDDEN" NAME="post_id" VALUE="<?php echo $post_id?>">
	<INPUT TYPE="HIDDEN" NAME="forum_id" VALUE="<?php echo $forum?>">
	<INPUT TYPE="HIDDEN" NAME="topic_id" VALUE="<?php echo $topic?>">
        <INPUT TYPE="HIDDEN" NAME="poster_id" VALUE="<?php echo $myrow[poster_id]?>">
        <INPUT TYPE="SUBMIT" NAME="submit" VALUE="Submit">&nbsp;<INPUT TYPE="RESET" VALUE="Clear">
</TR>
</TABLE></TD></TR></TABLE>
<?php
}
include('page_tail.'.$phpEx);
?>
