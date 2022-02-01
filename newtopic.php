<?php
/***************************************************************************
                            newtopic.php  -  description
                             -------------------
    begin                : Sat June 17 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org

    $Id: newtopic.php,v 1.36 2000/12/06 22:33:11 thefinn Exp $

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
if($cancel) {
	header("Location: viewforum.$phpEx?forum=$forum");
}

include('functions.'.$phpEx);
include('config.'.$phpEx);
require('auth.'.$phpEx);
$pagetitle = "New Topic";
$pagetype = "newtopic";
$sql = "SELECT forum_name, forum_access FROM forums WHERE (forum_id = '$forum')";
if(!$result = mysql_query($sql, $db))
	die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>Could not connect to the forums database.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
$myrow = mysql_fetch_array($result);
$forum_name = $myrow[forum_name];
$forum_access = $myrow[forum_access];
$forum_id = $forum;

if(!does_exists($forum, $db, "forum")) {
	die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"95%\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>The forum you are attempting to post to does not exist. Please try again.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
}

if($submit) {
   if($message == '') 
     die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>You must type a message to post. You cannot post an empty topic, go back and enter a message</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
   if($subject == '')
     die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>All Topics must have a subject. Go back and enter one.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
   
   if (!$user_logged_in) {
      if($username == '' && $password == '' && $forum_access == 2) {
	 // Not logged in, and username and password are empty and forum_access is 2 (anon posting allowed)
	 $userdata = array("user_id" => -1); 
      }
      else {
	 // no valid session, need to check user/pass.
	 if($username == '' || $password == '') {
	    include('page_header.'.$phpEx);
	    die("You must enter your username and password. Go back and do so.");
	 }
	 $md_pass = md5($password);
	 $userdata = get_userdata($username, $db);
	 if($userdata[user_level] == -1) {
	    include('page_header.'.$phpEx);
	    die("User $userdata[username] has been removed.");
	 }
	 if($md_pass != $userdata["user_password"]) {
	    include('page_header.'.$phpEx);
	    die("You have entered an incorrect password. Go back and try again.");
	 }
	 if($forum_access == 3 && $userdata[user_level] < 2) {
	    include('page_header.'.$phpEx);
	    die("You do not have access to post to this forum");
	 }
	 if(is_banned($userdata[user_id], "username", $db))
	   die("You have been banned from this forum. Contact the system administrator if you have any quesions.");
      }
      if($userdata[user_id] != -1) {
	 // You've entered your password and username, we log you in.
	 $sessid = new_session($userdata[user_id], $REMOTE_ADDR, $sesscookietime, $db);
	 set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);
      }
   }
   else {
      if($forum_access == 3 && $userdata[user_level] < 2) {
	 include('page_header.'.$phpEx);
	 die("You do not have access to post to this forum");
      }
   }
   // Either valid user/pass, or valid session. continue with post.
	
   if($allow_html == 0 || isset($html))
     $message = htmlspecialchars($message);
   if($sig && $userdata[user_id] != -1) {
      $message .= "[addsig]";
   }
   if($allow_bbcode == 1 && !($HTTP_POST_VARS[bbcode]))
     $message = bbencode($message);
   $message = str_replace("\n", "<BR>", $message);
   if(!$smile) {
      $message = smile($message);
   }
   $message = make_clickable($message);

   $message = censor_string($message, $db);
   $message = addslashes($message);
   $subject = strip_tags($subject);
   $subject = censor_string($subject, $db);
   $subject = addslashes($subject);
   $poster_ip = $REMOTE_ADDR;
   $time = date("Y-m-d H:i");
   $sql = "INSERT INTO topics (topic_title, topic_poster, forum_id, topic_time, topic_notify) VALUES ('$subject', '$userdata[user_id]', '$forum', '$time'";
   if(isset($notify) && $userdata[user_id] != -1)	
     $sql .= ", '1'";
   else
     $sql .= ", '0'";
   $sql .= ")";
   if(!$result = mysql_query($sql, $db)) {
      die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>Could not enter data into the database. Please go back and try again.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
   }
   $topic_id = mysql_insert_id();
   $sql = "INSERT INTO posts (topic_id, forum_id, poster_id, post_text, post_time, poster_ip) VALUES ('$topic_id', '$forum', '$userdata[user_id]', '$message', '$time', '$poster_ip')";
   if(!$result = mysql_query($sql, $db)) {
      die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>Could not enter data into the database. Please go back and try again.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
   }
   if($userdata[user_id] != -1) {
      $sql = "UPDATE users SET user_posts=user_posts+1 WHERE (user_id = $userdata[user_id])";
      $result = mysql_query($sql, $db);
      if (!$result) {
	 echo mysql_error() . "<br>\n";
	 die("<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\"><TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\"><TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><p><br><font face=\"Verdana\" size=\"+1\">Error:</font><font face=\"Verdana\" size=\"2\"><ul>Could not update post count.</ul><P></font></TD></TR></TABLE></TD></TR></TABLE><br>");
      }
   }
   $forward = 1;
   $topic = $topic_id;
   include('page_header.'.$phpEx);
   echo "<br><TABLE BORDER=\"0\" CELLPADDING=\"1\" CELLSPACEING=\"0\" ALIGN=\"CENTER\" VALIGN=\"TOP\" WIDTH=\"$tablewidth\">";
   echo "<TR><TD  BGCOLOR=\"$table_bgcolor\"><TABLE BORDER=\"0\" CALLPADDING=\"1\" CELLSPACEING=\"1\" WIDTH=\"100%\">";
   echo "<TR BGCOLOR=\"$color1\" ALIGN=\"LEFT\"><TD><font face=\"Verdana\" size=\"2\"><P>";
   echo "<P><BR><center>Your Topic Has Been Posted<P>Please click <a href=\"viewtopic.$phpEx?topic=$topic_id&forum=$forum\">here</a> to view your post.</center><P></font>";
   echo "</TD></TR></TABLE></TD></TR></TABLE><br>";
   
   
} else {
   include('page_header.'.$phpEx);
?>

	<FORM ACTION="<?php echo $PHP_SELF?>" METHOD="POST">
	<TABLE BORDER="0" CELLPADDING="1" CELLSPACING=0" ALIGN="CENTER" VALIGN="TOP" WIDTH="<?php echo $tablewidth?>"><TR><TD  BGCOLOR="<?php echo $table_bgcolor?>">
	<TABLE BORDER="0" CELLPADDING="1" CELLSPACING=1" WIDTH="100%">
	<TR BGCOLOR="<?php echo $color1?>" ALIGN="LEFT">
		<TD width=25%><b>About Posting:</b></TD>
<?php
	if($forum_access == 1) {
?>
		<TD>All <b><a href="bb_register.<?php echo $phpEx?>?mode=agreement">registered</a></b> users can post new topics and replies to this forum</TD>
<?php
	}
	else if($forum_access == 2) {
?>
		<TD><b>Anonymous users</b> can post new topics and replies in this forum. (To post anonymously simply do not enter a username or password)</TD>
<?php
	}
	else if($forum_access == 3) {
?>
		<TD>Only <B>Moderators and Administrators</b> can post new topics and replies in this forum.</TD>
<?php
	}
?>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>"  width=25%><b>Username:<b></TD>
		<TD  BGCOLOR="<?php echo $color2?>">
<?PHP
	if ($user_logged_in) {
		echo $userdata[username] . " \n";
	} else {
		echo "<INPUT TYPE=\"TEXT\" NAME=\"username\" SIZE=\"25\" MAXLENGTH=\"40\" VALUE=\"$userdata[username]\"> \n";
	}
?>

		</TD>
	</TR>

<?PHP
	if (!$user_logged_in) { 
		// no session, need a password.
		echo "    <TR ALIGN=\"LEFT\"> \n";
		echo "        <TD BGCOLOR=\"$color1\" width=25%><b>Password:</b><BR><font size=\"$FontSize3\"><i>(Lost your password? <a href=\"sendpassword.$phpEx\" target=\"_blank\">Click Here</a>)</i></font></TD> \n";
		echo "        <TD BGCOLOR=\"$color2\"><INPUT TYPE=\"PASSWORD\" NAME=\"password\" SIZE=\"25\" MAXLENGTH=\"25\"></TD> \n";
		echo "    </TR> \n";
	}
?>

	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width=25%><b>Subject:</b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"> <INPUT TYPE="TEXT" NAME="subject" SIZE="50" MAXLENGTH="100"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width=25%><b>Message:</b><br><br>
		<font size=-1>
		<?php
		echo "HTML is: ";
		if($allow_html == 1)
			echo "On<BR>\n";
		else
			echo "Off<BR>\n";
		echo "<a href=\"bbcode_ref.<?php echo $phpEx?>\" TARGET=\"blank\">BBCode</a> is: ";
		if($allow_bbcode == 1)
			echo "On<br>\n";
		else
			echo "Off<BR>\n";
		?>
		</font></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><TEXTAREA NAME="message" ROWS=10 COLS=45 WRAP="VIRTUAL"></TEXTAREA></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width=25%><b>Options:</b></TD>
		<TD  BGCOLOR="<?php echo $color2?>" >
		<?php
			if($allow_html == 1) {
			   if($userdata[user_html] == 1)
			     $h = "CHECKED";
		?>	
				<INPUT TYPE="CHECKBOX" NAME="html" <?php echo $h?>>Disable HTML on this Post<BR>
		<?php
			}
		?>
		<?php
			if($allow_bbcode == 1) {
			   if($userdata[user_bbcode] == 1)
			     $b = "CHECKED";
		?>	
				<INPUT TYPE="CHECKBOX" NAME="bbcode" <?php echo $b?>>Disable <a href="bbcode_ref.<?php echo $phpEx?>" target="_blank"><i>BBCode</i></a> on this Post<BR>
		<?php
			}
                        if($userdata[user_desmile] == 1)
                           $ds = "CHECKED";
		?>

		<INPUT TYPE="CHECKBOX" NAME="smile" <?php echo $ds?>>Disable <a href="bb_smilies.<?php echo $phpEx?>" target="_blank"><i>smilies</i></a> on this post.<BR>
		<?php
			if($allow_sig == 1) {
				if($userdata[user_attachsig] == 1)
					$s = "CHECKED";
		?>
				<INPUT TYPE="CHECKBOX" NAME="sig" <?php echo $s?>>Show signature <font size=-2>(This can be altered or added in your profile)</font><BR>
		<?php
			}
		?>
		<INPUT TYPE="CHECKBOX" NAME="notify">Notify by email when replies are posted<BR>
		</TD>
	</TR>
	<TR>
		<TD  BGCOLOR="<?php echo $color1?>" colspan=2 ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="forum" VALUE="<?php echo $forum?>">
		<INPUT TYPE="SUBMIT" NAME="submit" VALUE="Submit">&nbsp;<INPUT TYPE="RESET" VALUE="Clear">
		&nbsp;<INPUT TYPE="SUBMIT" NAME="cancel" VALUE="Cancel Post">
		</FORM>
	</TR>
	</TABLE></TD></TR></TABLE>

<?php
}
require('page_tail.'.$phpEx);
?>
