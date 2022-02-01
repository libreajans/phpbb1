<?php
/***************************************************************************
                            bb_register.php  -  description
                             -------------------
    begin                : Sat June 17 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org

    $Id: bb_register.php,v 1.21 2000/11/25 23:52:31 thefinn Exp $

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
$pagetitle = "Register";
$pagetype = "Register";

if($submit) {
   if($password == '' || $username == '' || $email == '') {
      include('page_header.'.$phpEx);
      die("Error - you did not fill in all the required fields, please go back and fill them in.");
   }
   
   if (check_username($username, $db)) {                                                                                         
      die("The username you chose \"$username\" has been taken. Please go back and try another name");
   }      
   if(validate_username($username, $db) == 1) {
     include('page_header.'.$phpEx);
     die("The username you chose, \"$username\" has been disallowed by the administrator. Please go back and try another name");
   }
   
   if($password != $password_rep) {
      include('page_header.'.$phpEx);
      die("The passwords you entered do not match. Please go back and try again");
   }
   
   $sig = str_replace("\n", "<BR>", $sig);
   $sig = addslashes($sig);
   $occ = addslashes($occ);
   $intrest = addslashes($intrest);
   $from = addslashes($from);
   $passwd = md5($password);
   $regdate = date("M d, Y");
   
   if($website == "http://")
     $website = "";
   
   if($viewemail == 1) {
      $sqlviewemail = "1";
   }
   else {
      $sqlviewemail = "0";
   }
   $sql = "SELECT max(user_id) AS total FROM users";
   if(!$r = mysql_query($sql, $db))
     die("Error connecting to the database.");
   list($total) = mysql_fetch_array($r);
   $total += 1;
   $sql = "INSERT INTO users (user_id, username, user_regdate, user_email, user_icq, user_password, user_occ, user_intrest, user_from, user_website, user_sig, user_aim, user_viewemail, user_yim, user_msnm) VALUES ('$total', '$username', '$regdate', '$email', '$icq', '$passwd', '$occ', '$intrest', '$from', '$website', '$sig', '$aim', '$sqlviewemail', '$yim', '$msnm')";
   if(!$result = mysql_query($sql, $db)) {
      include('page_header.'.$phpEx);
      die("An Error Occured while trying to add the information into the database. Please go back and try again. <BR>$sql<BR>$mysql_error()");
   }

   if($cookie_username) {
      $time = (time() + 3600 * 24 * 30 * 12);
      setcookie($cookiename, $total, $time, $cookiepath, $cookiedomain, $cookiesecure);
   }
   include('page_header.'.$phpEx);
   
   $message = "Welcome to $sitename forums!\nPlease keep this email for your records!\n\n";
   $message  .= "Your account information is as follows:\n";
   $message .= "----------------------------\n";
	$message .= "Username: $username\n";
   $message .= "Password: $password\n";
   $message .="\nPlease do not forget your password as it has been encrypted in our database and we cannot retrive it for you.";
   $message .= " However, should you forget your password we provide an easy to use script to generate and email a new, random, password.\nThank you for registering.";
   $message .= "\r\n$email_sig";
		 
   mail($email, "Welcome to $sitename Forums", $message, "From: $email_from");
   echo "<p>You have been added to the database.<p>Click <a href=\"$url_phpbb/index.$phpEx\">here</a> to return to the forums index page.<br>Thank you for registering!<p><br>";
}
else {
   include('page_header.'.$phpEx);
   ?>
	<FORM ACTION="<?php echo $PHP_SELF?>" METHOD="POST">
	<TABLE BORDER="0" WIDTH="<?php echo $TableWidth?>" CELLPADDING="1" CELLSPACING="0" ALIGN="CENTER" VALIGN="TOP"><TR><TD  BGCOLOR="<?php echo $table_bgcolor?>">
	<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="1" WIDTH="100%">
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>"  width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>User Name: *</b></FONT><br><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">(Must be unique. No 2 users can have the same Username)</FONT></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="TEXT" NAME="username" SIZE="25" MAXLENGTH="40"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Password: *</b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="PASSWORD" NAME="password" SIZE="25" MAXLENGTH="25"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Retype Password: *</b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="PASSWORD" NAME="password_rep" SIZE="25" MAXLENGTH="25"></TD>
	</TR>

	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>"  width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>EMail Address: *<b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="TEXT" NAME="email" SIZE="25" MAXLENGTH="50"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>"  width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>ICQ Number: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="TEXT" NAME="icq" SIZE="10" MAXLENGTH="15"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>"  width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>AIM Name: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="TEXT" NAME="aim" SIZE="15" MAXLENGTH="18"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>"  width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Yahoo Messanger: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="TEXT" NAME="yim" SIZE="25" MAXLENGTH="25"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>"  width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>MSN Messanger: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="TEXT" NAME="msnm" SIZE="25" MAXLENGTH="25"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>"  width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Web Site Address: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="TEXT" NAME="website" SIZE="25" MAXLENGTH="40" VALUE="http://"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>"  width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Location: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="TEXT" NAME="from" SIZE="25" MAXLENGTH="40"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>"  width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Occupation: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="TEXT" NAME="occ" SIZE="25" MAXLENGTH="40"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>"  width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Intrests: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="TEXT" NAME="intrest" SIZE="25" MAXLENGTH="40"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Signature:</b><br><font size=-2>This is a block of text that can be added to posts you make.<BR>255 chars max!</font></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><TEXTAREA NAME="sig" ROWS=6 COLS=45></TEXTAREA></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Options:</b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><INPUT TYPE="CHECKBOX" NAME="viewemail" VALUE="1"> Allow other users to view my email address<BR>
		<INPUT TYPE="CHECKBOX" NAME="cookie_username" VALUE="1"> Store username in a cookie for 1 year.<BR>
		</TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" colspan="2"><font size=-1>Items marked with a * are required</font></TD>
	</TR>
	<TR>
		<TD  BGCOLOR="<?php echo $color1?>" colspan="2" ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="forum" VALUE="<?php echo $forum?>">
		<INPUT TYPE="HIDDEN" NAME="topic_id" VALUE="<?php echo $topic?>">
		<INPUT TYPE="SUBMIT" NAME="submit" VALUE="Submit">&nbsp;<INPUT TYPE="RESET" VALUE="Clear">
	</TR>
	</TABLE></TD></TR></TABLE>
	</FORM>
<?php
}
require('page_tail.'.$phpEx);
?>
