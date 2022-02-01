<?php
/***************************************************************************
                            bb_profile.php  -  description
                             -------------------
    begin                : Sat June 17 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org

    $Id: bb_profile.php,v 1.29 2000/12/06 22:33:11 thefinn Exp $

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
* Thursday, July 20, 2000 - Yokhannan - Just a few typo errors corrected
*
* Saturday, July 22, 2000 - Yokhannan - I added the YIM & MSNM code
*
* Tusday, Sept. 12, 2000 - theFinn - Added code to record the date the user registers.
*/
include('extention.inc');
include('functions.'.$phpEx);
include('config.'.$phpEx);
require('auth.'.$phpEx);
$pagetitle = "Profile";
$pagetype = "Edit Profile";


if($mode) {
	switch($mode) {
	 case 'view':
	   include('page_header.'.$phpEx);
	   $userdata = get_userdata_from_id($user, $db);
	   $total_posts = get_total_posts("0", $db, "all");
	   if($userdata[user_posts] != 0)
	     $user_percentage = $userdata[user_posts] / $total_posts * 100;
	   else
	     $user_percentage = 0;
	   
	   if (!$userdata[user_id]) {
	      die("User does not exist.");
	   }
	   if($userdata[user_level] == -1) {
?>
		<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" ALIGN="CENTER" VALIGN="TOP" WIDTH="<?php echo $TableWidth?>">
		<TR><TD  BGCOLOR="<?php echo  $table_bgcolor?>">
		<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="1" WIDTH="100%">                                                             
		<TR ALIGN="LEFT" BGCOLOR="<?php echo $color1?>">
		<TD ALIGN="CENTER"><font face="<?php echo $FontFace?>" size="<?php echo $FontSize2?>" color="<?php echo $textcolor?>"><B>Error</B></font></TD>
		</TR>
		<TR ALIGN="CENTER" BGCOLOR="<?php echo $color2?>"> 
		<TD><font face="<?php echo $FontFace?>" size="<?php echo $FontSize2?>" color="<?php echo $textcolor?>">This user has been removed from the <?php echo $sitename?> Forums database.</font></TD>
		</TR>
		</TABLE></TABLE>
<?php
		include('page_tail.'.$phpEx);
	      exit();
	   }
?>
	<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" ALIGN="CENTER" VALIGN="TOP" WIDTH="<?php echo $TableWidth?>"><TR><TD  BGCOLOR="<?php echo  $table_bgcolor?>">
	<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="1" WIDTH="100%">
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>User Name:</FONT></b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><?php echo $userdata[username]?></FONT>
	        <font size=-2>(<a href="search.<?php echo $phpEx?>?term=&addterms=any&forum=all&username=<?php echo $userdata[username]?>&sortby=p.post_time&searchboth=both&submit=Search">view posts by this user</a>)</font></TD>
	</TR>
	<TR ALIGN="LEFT">
                <TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Member Since:</FONT></b></TD>
                <TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><?php echo $userdata[user_regdate]?></FONT></TD>
        </TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Posts:</FONT></b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><?php echo $userdata[user_posts]?> (<?php printf("%.2f", $user_percentage) ?>% of total)</FONT></TD>
	</TR>

<?php
			if($userdata[user_viewemail] == 1) {
?>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Email Address:<b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><a href="mailto:<?php echo $userdata[user_email]?>"><?php echo $userdata[user_email]?></a></TD>
	</TR>
<?php
			}
?>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>ICQ Number: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><?php echo $userdata[user_icq]?></FONT>&nbsp;&nbsp;<font size=-2>(<a href="http://wwp.icq.com/scripts/search.dll?to=<?php echo $userdata[user_icq]?>">add</a>)</font>&nbsp;&nbsp;<font size=-2>(<a href="http://wwp.mirabilis.com/<?php echo $userdata[user_icq]?>" TARGET="_blank">pager</a>)</font></TD>

	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>AIM Handle: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><a href="aim:goim?screenname=<?php echo $userdata[user_aim]?>&message=Hi+<?php echo $userdata[user_aim]?>.+Are+you+there?"><?php echo $userdata[user_aim]?></a></FONT>&nbsp;</TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Yahoo Messanger: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><a href="http://edit.yahoo.com/config/send_webmesg?.target=<?php echo $userdata[user_yim]?>&.src=pg"><?php echo $userdata[user_yim]?></a></FONT>&nbsp;</TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>MSN Messanger: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><?php echo $userdata[user_msnm]?>&nbsp;</TD>
	</TR>

	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Web Site Address: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><a href="<?php echo $userdata[user_website]?>" target="_blank"><?php echo $userdata[user_website]?></a></FONT>&nbsp;</TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Location: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><?php echo $userdata[user_from]?>&nbsp;</TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Occupation: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><?php echo $userdata[user_occ]?>&nbsp;</TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Interests: <b></TD>
<?php
	$userdata[user_intrest] = stripslashes($userdata[user_intrest]);
?>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><?php echo $userdata[user_intrest]?>&nbsp;</TD>
	</TR>
	</TABLE></TD></TR></TABLE>
<?php
		
	break;
	case 'edit':

		if ($submit || $user_logged_in) {
			// ok.. either the user's entered their username and password, or they have a valid session.
			if ($save) {
			   // trying to save their profile information..
			   $userdata = get_userdata_from_id($user_id, $db);
			   if(is_banned($userdata[user_id], "username", $db))
			     die("You have been banned from this forum. Contact the system administrator if you have any quesions.");
				if (!$userdata[user_id]) {
				        include('page_header.'.$phpEx);
					die("Invalid userid.");
				}
				if ($password == '') {
				   include('page_header.'.$phpEx);
				   die("You must enter your password. Go back and do so.");
				}
				$md_pass = md5($password);
				if ($password2 != '') {
					if ($password != $password2)  {
					   include('page_header.'.$phpEx);
					   die("Passwords do not match. Go back and re-enter them.");
					}
				} else {
					// only one password, so make sure it's valid.
					if ($md_pass != $userdata[user_password]) {
					   include('page_header.'.$phpEx);
					   die("Invalid password. Go back and try again.");
					}
				}
				// whatever the case, $md_pass contains the password for the DB.
				// ready to save, they've authed just fine..
				if($allow_namechange && $user_name != $userdata[username]) {
				   if (check_username($user_name, $db)) {
				      die("The username you chose \"$user_name\" has been taken. Please go back and try another name");                              
				   }
				   if(validate_username($user_name, $db) == 1) {
				      include('page_header.'.$phpEx);
				      die("The username you chose, \"$user_name\" has been disallowed by the administrator. Please go back and try another name");
				   }
				   $new_name = 1;
				}
				$sig = str_replace("\n", "<BR>", $sig);
				$sig = addslashes($sig);
				$occ = addslashes($occ);
				$intrest = addslashes($intrest);
				$from = addslashes($from);
				$passwd = md5($password);
			        if($new_name) {
				   $sql = "UPDATE users SET username = '$user_name', user_password = '$md_pass', user_icq = '$icq', user_occ = '$occ', user_intrest = '$intrest', user_from = '$from', user_website = '$website', user_sig = '$sig', user_email = '$email', user_viewemail = '$viewemail', user_aim = '$aim', user_yim = '$yim', user_msnm = '$msnm' WHERE (user_id = '$user_id')";
				}
			        else {
				   $sql = "UPDATE users SET user_password = '$md_pass', user_icq = '$icq', user_occ = '$occ', user_intrest = '$intrest', user_from = '$from', user_website = '$website', user_sig = '$sig', user_email = '$email', user_viewemail = '$viewemail', user_aim = '$aim', user_yim = '$yim', user_msnm = '$msnm' WHERE (user_id = '$user_id')";
				}
				if(!$result = mysql_query($sql, $db)) {
				   include('page_header.'.$phpEx);
				   echo mysql_error() . "<br>";
				   die("Error doing db UPDATE.");
				}
			   // They have authed, log them in.
			   $sessid = new_session($userdata[user_id], $REMOTE_ADDR, $sesscookietime, $db);
			   set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);
			   include('page_header.'.$phpEx);
			   echo "Your Information has been updated.<br>Click <a href=\"index.$phpEx\">here</a> to return to the forum index.";
			} else { 
			   // not trying to save, so show the form.
			   if (!$user_logged_in) {
			      // no valid session, need to check user/pass.
			      if($user == '' || $passwd == '') {
				 include('page_header.'.$phpEx);
				 die("You must enter your User Name and Password. Go back and do so.");
			      }
			      $md_pass = md5($passwd);
			      $userdata = get_userdata($user, $db);
			      if(is_banned($userdata[user_id], "username", $db))
				die("You have been banned from this forum. Contact the system administrator if you have any quesions.");
			      if($md_pass != $userdata["user_password"]) {
				 include('page_header.'.$phpEx);
				 die("You have entered an incorrect password. Go back and try again.");
			      }	
			      // They have authed succecfully, log them in.
			      $sessid = new_session($userdata[user_id], $REMOTE_ADDR, $sesscookietime, $db);
			      set_session_cookie($sessid, $sesscookietime, $sesscookiename, $cookiepath, $cookiedomain, $cookiesecure);
			   }
			   include('page_header.'.$phpEx);
?>
	<FORM ACTION="<?php echo $PHP_SELF?>" METHOD="POST">
	<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" ALIGN="CENTER" VALIGN="TOP" WIDTH="<?php echo $TableWidth?>">
	<TR><TD  BGCOLOR="<?php echo $table_bgcolor?>">
	<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="1" WIDTH="100%">
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>User Name:</FONT></b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>">
<?php 
		if($allow_namechange) {
		   echo "<input type=\"text\" name=\"user_name\" size=\"35\" maxlength=\"40\" value=\"$userdata[username]\">";
		}
		else {
		   echo $userdata[username];
		}
?>
	       </FONT></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Password: *</FONT></b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><INPUT TYPE="PASSWORD" NAME="password" SIZE="25" MAXLENGTH="25"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Confirm Password:</b></FONT><br><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">(only required if being changed)</FONT></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><INPUT TYPE="PASSWORD" NAME="password2" SIZE="25" MAXLENGTH="25"></TD>
	</TR>

	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>EMail Address: *<b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><INPUT TYPE="TEXT" NAME="email" SIZE="25" MAXLENGTH="50" VALUE="<?php echo $userdata[user_email]?>"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>ICQ Number: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><INPUT TYPE="TEXT" NAME="icq" SIZE="10" MAXLENGTH="15" VALUE="<?php echo $userdata[user_icq]?>"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>AIM Handle: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><INPUT TYPE="TEXT" NAME="aim" SIZE="25" MAXLENGTH="18" VALUE="<?php echo $userdata[user_aim]?>"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Yahoo Messenger: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><INPUT TYPE="TEXT" NAME="yim" SIZE="25" MAXLENGTH="18" VALUE="<?php echo $userdata[user_yim]?>"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo  $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>MSN Messenger: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><INPUT TYPE="TEXT" NAME="msnm" SIZE="25" MAXLENGTH="18" VALUE="<?php echo $userdata[user_msnm]?>"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Web Site Address: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><INPUT TYPE="TEXT" NAME="website" SIZE="25" MAXLENGTH="40" VALUE="<?php echo $userdata[user_website]?>"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Location: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><INPUT TYPE="TEXT" NAME="from" SIZE="25" MAXLENGTH="40" VALUE="<?php echo $userdata[user_from]?>"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Occupation: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><INPUT TYPE="TEXT" NAME="occ" SIZE="25" MAXLENGTH="40" VALUE="<?php echo $userdata[user_occ]?>"></TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Intrests: <b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><INPUT TYPE="TEXT" NAME="intrest" SIZE="25" MAXLENGTH="40" VALUE="<?php echo $userdata[user_intrest]?>"></TD>
	</TR>
<?php
	$sig = str_replace("<BR>", "\n", $userdata[user_sig]);
?>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Signature:</b><br><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">This is a block of text that can be added to posts you make.<BR>255 characters max!</font></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><TEXTAREA NAME="sig" ROWS=6 COLS=45><?php echo $sig?></TEXTAREA></TD>
	</TR>
	<TR ALIGN="LEFT">
<?php
		if($userdata[user_viewemail] == 1)
			$s = " CHECKED";
?>
		<TD  BGCOLOR="<?php echo $color1?>" width="25%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Options:</FONT></b></TD>
		<TD  BGCOLOR="<?php echo $color2?>"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><INPUT TYPE="CHECKBOX" NAME="viewemail" VALUE="1" <?php echo $s?>> Allow other users to view my email address</TD>
	</TR>
	<TR ALIGN="LEFT">
		<TD  BGCOLOR="<?php echo $color1?>" colspan = 2><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize3?>" COLOR="<?php echo $textcolor?>">Items marked with a * are required</font></TD>
	</TR>
	<TR>
		<TD BGCOLOR="<?php echo $color1?>" colspan=2 ALIGN="CENTER">
		<INPUT TYPE="HIDDEN" NAME="mode" VALUE="edit">
		<INPUT TYPE="HIDDEN" NAME="save" VALUE="1">
		<INPUT TYPE="HIDDEN" NAME="user_id" VALUE="<?php echo $userdata[user_id]?>">
		<INPUT TYPE="SUBMIT" NAME="submit" VALUE="Submit">&nbsp;<INPUT TYPE="RESET" VALUE="Clear">
		</TD>
	</TR>
	</TABLE></TD></TR></TABLE></FORM>
<?PHP

			}
		} else {
			// no valid session, and they haven't submitted.
			// so, we need to get a user/pass.
	                include('page_header.'.$phpEx);
?>
<TABLE BORDER="0" WIDTH="<?php echo $TableWidth?>" CELLPADDING="1" CELLSPACING="0" ALIGN="CENTER" VALIGN="TOP">
<TR><TD  BGCOLOR="<?php echo $table_bgcolor?>">
<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="1" WIDTH="100%">
<TR BGCOLOR="<?php echo $color2?>" ALIGN="CENTER">
<TD><BR><BR><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>">
Please enter your User Name and Password to edit your profile information
<FORM ACTION="<?php echo $PHP_SELF?>" METHOD="POST">
<b>User Name: </b><INPUT TYPE="TEXT" NAME="user" SIZE="25" MAXLENGTH="40" VALUE="<?php echo $userdata[username]?>"><BR>
<b>Password: </b><INPUT TYPE="PASSWORD" NAME="passwd" SIZE="25" MAXLENGTH="25"><br></FONT>
<INPUT TYPE="HIDDEN" NAME="mode" VALUE="edit">
<INPUT TYPE="SUBMIT" NAME="submit" VALUE="Edit Information">&nbsp;<INPUT TYPE="RESET" VALUE="Clear">
</FORM><BR></TD></TR></TABLE></TD></TR></TABLE>
<?php
		}
	break;

	} // switch

} // if ($mode)
?>
<?php
include('page_tail.'.$phpEx);
?>
