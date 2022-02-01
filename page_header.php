<?php
/***************************************************************************
                          page_header.php  -  description
                             -------------------
    begin                : Sat June 17 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org
 
    $Id: page_header.php,v 1.44 2000/11/18 05:41:54 thefinn Exp $ 

 ***************************************************************************/

/***************************************************************************
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 ***************************************************************************/

$mtime = microtime();
$mtime = explode(" ",$mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime;

/* Who's Online Hack */
$IP=$REMOTE_ADDR;

if($pagetype == "index") {
	$users_online = get_whosonline($IP, $userdata[username], 0, $db);
}
if($pagetype == "viewforum" || $pagetype == "viewtopic") {
	$users_online = get_whosonline($IP, $userdata[username], $forum, $db);
}

$login_logout_link = make_login_logout_link($user_logged_in, $url_phpbb);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Strict//EN">
<HTML>
<HEAD>
<TITLE>phpBB - <?php echo $pagetitle?></TITLE>
<?php
if($forward) {
	echo "<META HTTP-EQUIV=\"refresh\" content=\"3;URL=$url_phpbb/viewtopic.$phpEx?topic=$topic&forum=$forum\">";
} 
$meta = showmeta($db);
?>
<?php echo $meta?>
</HEAD>
<BODY BGCOLOR="<?php echo $bgcolor?>" TEXT="<?php echo $textcolor?>" LINK="<?php echo $linkcolor?>" VLINK="<?php echo $vlinkcolor?>">

<?php

showheader($db);

switch($pagetype) {
	case 'index':
	$total_posts = get_total_posts("0", $db, "all");
	$total_users = get_total_posts("0", $db, "users");
	$sql = "SELECT username, user_id FROM users ORDER BY user_id DESC";
	$res = mysql_query($sql, $db);
	$row = mysql_fetch_array($res);
	$newest_user = $row["username"];
	$newest_user_id = $row["user_id"];

?>
<TABLE BORDER=0 WIDTH="<?php echo $TableWidth?>" CELLPADDING="5" ALIGN="CENTER">
<TR>
	<TD ALIGN="CENTER" WIDTH="50%"><a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>"><IMG SRC="<?php echo $header_image?>" border="0"></a></TD>
	<TD ALIGN="CENTER" WIDTH="50%">
		<TABLE BORDER="0">
		<TR ALIGN="CENTER" VALIGN="MIDDLE">
			<TD ALIGN="CENTER"><FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize4?>" COLOR="<?php echo $textcolor?>"><?php echo $sitename?> Forums</font>	</TD>
		</TR>
		<TR ALIGN="CENTER" VALIGN="MIDDLE">
			<TD ALIGN="CENTER">
			<FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">
			[<a href="<?php echo $url_phpbb?>/bb_register.<?php echo $phpEx ?>?mode=agreement">Register</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_profile.<?php echo $phpEx ?>?mode=edit">Edit Profile</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/prefs.<?php echo $phpEx ?>">Edit Preferences</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/search.<?php echo $phpEx ?>">Search</a>]<br>
			[<a href="<?php echo $url_phpbb?>/viewpmsg.<?php echo $phpEx ?>">Private Messages</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_memberlist.<?php echo $phpEx ?>">Members List</a>]&nbsp;
                        [<a href="<?php echo $url_phpbb?>/faq.<?php echo $phpEx ?>">FAQ</a>]&nbsp;
			[<?php echo $login_logout_link?>]
			</font>
			</TD>
		</TR>
		</TABLE>
	</TD>
</TR>
<TR>
	<TD COLSPAN="2" ALIGN="RIGHT">
		<FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">
		Our Users have posted a total of -<b><?php echo $total_posts?></b>- messages.<BR>
		We have -<b><?php echo $total_users?></b>- Registered users.<BR>
		The newest Registered user is -<b><a href="<?php echo $url_phpbb?>/bb_profile.<?php echo $phpEx ?>?mode=view&user=<?php echo $newest_user_id?>"><?php echo $newest_user?></a>-</b>.<BR>
<?php
	if($users_online == 1) {
		$word = "user is";
	} else {
		$word = "users are";
	}
?>
		<B><?php echo $users_online?></B> <?php echo $word?> <a href="whosonline.<?php echo $phpEx ?>">currently browsing</a> this forum.<br>
<?php
		print_login_status($user_logged_in, $userdata[username], $url_phpbb);
?>

		</font>
	</TD>
</TR>
</TABLE>
<?php
	break;
	case 'newtopic':
?>
<TABLE BORDER=0 WIDTH="<?php echo $TableWidth?>" CELLPADDING="5" ALIGN="CENTER">
<TR>
	<TD ALIGN="CENTER" WIDTH="50%"><a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>"><IMG SRC="<?php echo $header_image?>" border="0"></a></TD>
	<TD ALIGN="CENTER" WIDTH="50%"><FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Post New Topic in:<BR>
		<a href="<?php echo $url_phpbb?>/viewforum.<?php echo $phpEx ?>?forum=<?php echo $forum?>"><?php echo $forum_name?></a></b>
		</font>
	</TD>
</TR>
</TABLE>
<?php
	break;
	case 'viewforum':
?>
<TABLE BORDER=0 WIDTH="<?php echo $TableWidth?>" CELLPADDING="5" ALIGN="CENTER">
<TR>
	<TD ALIGN="LEFT" WIDTH="50%"><a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>"><IMG SRC="<?php echo $header_image?>" border="0"></a>
	<BR><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b><?php echo $forum_name?></b><BR><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">Moderated By:
<?php
$count = 0;     
$forum_moderators = get_moderators($forum, $db);
   while(list($null, $mods) = each($forum_moderators)) {
      while(list($mod_id, $mod_name) = each($mods)) {
	 if($count > 0)
	   echo ", ";
	 echo "<a href=\"bb_profile.$phpEx?mode=view&user=$mod_id\">".trim($mod_name)."</a>";
	 $count++;
      }
   }
?></font></TD>
	<TD>
	<TABLE BORDER="0">
		<TR ALIGN="CENTER" VALIGN="MIDDLE">
			<TD ALIGN="CENTER" WIDTH="50%"><a href="<?php echo $url_phpbb?>/newtopic.<?php echo $phpEx ?>?forum=<?php echo $forum?>"><IMG SRC="<?php echo $newtopic_image?>" BORDER="0"></a></TD>
		</TR>
		<TR ALIGN="CENTER" VALIGN="MIDDLE">
			<TD>
			<FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">
			[<a href="<?php echo $url_phpbb?>/bb_register.<?php echo $phpEx ?>?mode=agreement">Register</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_profile.<?php echo $phpEx ?>?mode=edit">Edit Profile</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/prefs.<?php echo $phpEx ?>">Edit Preferences</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/search.<?php echo $phpEx ?>">Search</a>]<br>
			[<a href="<?php echo $url_phpbb?>/viewpmsg.<?php echo $phpEx ?>">Private Messages</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_memberlist.<?php echo $phpEx ?>">Members List</a>]&nbsp;
                        [<a href="<?php echo $url_phpbb?>/faq.<?php echo $phpEx ?>">FAQ</a>]&nbsp;
			[<?php echo $login_logout_link?>]
			</font>
			</TD>
		</TR>
	</TABLE>
	</TD>
</TR>
<TR>
	<TD colspan="2">
	<BR><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>"><a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>"><?php echo $sitename?> Forum Index</a> <b>» »</b>
<a href="<?php echo $url_phpbb?>/viewforum.<?php echo $phpEx ?>?forum=<?php echo $forum?>"><?php echo stripslashes($forum_name)?></a></FONT></TD>
</TABLE>
<?php
	break;
	case 'viewtopic':
?>
<TABLE BORDER=0 WIDTH="<?php echo $TableWidth?>" CELLPADDING="5" ALIGN="CENTER">
<TR>
	<TD ALIGN="LEFT" WIDTH="50%"><a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>"><IMG SRC="<?php echo $header_image?>" border="0"></a></TD>
	<TD>
	<TABLE BORDER="0">
		<TR ALIGN="CENTER" VALIGN="MIDDLE">
			<TD ALIGN="CENTER" WIDTH="50%"><a href="<?php echo $url_phpbb?>/newtopic.<?php echo $phpEx ?>?forum=<?php echo $forum?>"><IMG SRC="<?php echo $newtopic_image?>" BORDER="0"></a>&nbsp;&nbsp;
<?php
	if($lock_state != 1) {
?>
			<a href="<?php echo $url_phpbb?>/reply.<?php echo $phpEx ?>?topic=<?php echo $topic?>&forum=<?php echo $forum?>"><IMG SRC="<?php echo $reply_image?>" BORDER="0"></a></TD>
<?php
	}
	else
			echo "<img src=\"$reply_locked_image\" BORDER=0>\n";
?>
	</TR>
		<TR ALIGN="CENTER" VALIGN="MIDDLE">
			<TD>
			<FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">
			[<a href="<?php echo $url_phpbb?>/bb_register.<?php echo $phpEx ?>?mode=agreement">Register</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_profile.<?php echo $phpEx ?>?mode=edit">Edit Profile</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/prefs.<?php echo $phpEx ?>">Edit Preferences</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/search.<?php echo $phpEx ?>">Search</a>]<br>
			[<a href="<?php echo $url_phpbb?>/viewpmsg.<?php echo $phpEx ?>">Private Messages</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_memberlist.<?php echo $phpEx ?>">Members List</a>]&nbsp;
                        [<a href="<?php echo $url_phpbb?>/faq.<?php echo $phpEx ?>">FAQ</a>]&nbsp; 
			[<?php echo $login_logout_link?>]
			</font>
			</TD>
		</TR>
	</TABLE>
	</TD>
</TR>
<TR>
	<TD colspan="2">
	<FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>"><a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>"><?php echo $sitename?> Forum Index</a> <b>» »</b> <a href="<?php echo $url_phpbb?>/viewforum.<?php echo $phpEx ?>?forum=<?php echo $forum?>"><?php echo stripslashes($forum_name)?></a> »» <?php echo $topic_subject?>
	</TD>
</TABLE>

<?php
	break;
	case 'reply':
?>
<TABLE BORDER=0 WIDTH="<?php echo $TableWidth?>" CELLPADDING="5" ALIGN="CENTER">
<TR>
	<TD ALIGN="CENTER" WIDTH="50%"><a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>"><IMG SRC="<?php echo $header_image?>" border="0"></a></TD>
	<TD ALIGN="CENTER" WIDTH="50%"><b>Post Reply in:<BR>
		<a href="<?php echo $url_phpbb?>/viewforum.<?php echo $phpEx ?>?forum=<?php echo $forum?>"><?php echo stripslashes($forum_name)?></a></b>
		</font>
	</TD>
</TR>
</TABLE>
<?php
	break;
	case 'Register':
?>
<TABLE BORDER=0 WIDTH="<?php echo $TableWidth?>" CELLPADDING="5" ALIGN="CENTER">
<TR>
	<TD ALIGN="CENTER" WIDTH="50%"><a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>"><IMG SRC="<?php echo $header_image?>" border="0"></a></TD>
	<TD>
		<TABLE BORDER="0">
		<TR ALIGN="CENTER" VALIGN="MIDDLE">
			<TD ALIGN="CENTER"><FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize4?>" COLOR="<?php echo $textcolor?>"><?php echo $sitename?> Forums</font>	</TD>
		</TR>
		<TR ALIGN="CENTER" VALIGN="MIDDLE">
			<TD ALIGN="CENTER">
			<FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">
			[<a href="<?php echo $url_phpbb?>/bb_register.<?php echo $phpEx ?>?mode=agreement">Register</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_profile.<?php echo $phpEx ?>?mode=edit">Edit Profile</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/prefs.<?php echo $phpEx ?>">Edit Preferences</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/search.<?php echo $phpEx ?>">Search</a>]<br>
			[<a href="<?php echo $url_phpbb?>/viewpmsg.<?php echo $phpEx ?>">Private Messages</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_memberlist.<?php echo $phpEx ?>">Members List</a>]&nbsp;
                        [<a href="<?php echo $url_phpbb?>/faq.<?php echo $phpEx ?>">FAQ</a>]&nbsp;
			[<?php echo $login_logout_link?>]
			</font>
			</TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>
<BR>
<?php
	break;
	case 'Edit Profile':
?>
<TABLE BORDER=0 WIDTH="<?php echo $TableWidth?>" CELLPADDING="5" ALIGN="CENTER">
<TR>
	<TD ALIGN="CENTER" WIDTH="50%"><a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>"><IMG SRC="<?php echo $header_image?>" border="0"></a></TD>
	<TD ALIGN="CENTER" WIDTH="50%"><FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize4?>" COLOR="<?php echo $textcolor?>"><?php echo $sitename?> Forums</font><BR>
			<FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">
			[<a href="<?php echo $url_phpbb?>/bb_register.<?php echo $phpEx ?>?mode=agreement">Register</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_profile.<?php echo $phpEx ?>?mode=edit">Edit Profile</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/prefs.<?php echo $phpEx ?>">Edit Preferences</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/search.<?php echo $phpEx ?>">Search</a>]<br>
			[<a href="<?php echo $url_phpbb?>/viewpmsg.<?php echo $phpEx ?>">Private Messages</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_memberlist.<?php echo $phpEx ?>">Members List</a>]&nbsp;
                        [<a href="<?php echo $url_phpbb?>/faq.<?php echo $phpEx ?>">FAQ</a>]&nbsp;
                        [<?php echo $login_logout_link?>]
			</font>

	</TD>
</TR>
</TABLE>
<BR>
<?php
	break;
	case 'bbcode_ref':
?>
<TABLE BORDER=0 WIDTH="<?php echo $TableWidth?>" CELLPADDING="5" ALIGN="CENTER">
<TR>
	<TD ALIGN="CENTER" WIDTH="50%"><a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>"><IMG SRC="<?php echo $header_image?>" border="0"></a></TD>
	<TD ALIGN="CENTER" WIDTH="50%">
		<TABLE BORDER="0">
		<TR ALIGN="CENTER" VALIGN="MIDDLE">
			<TD ALIGN="CENTER"><FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize4?>" COLOR="<?php echo $textcolor?>"><?php echo $sitename?> Forums</font>	</TD>
		</TR>
		<TR ALIGN="CENTER" VALIGN="MIDDLE">
			<TD ALIGN="CENTER">
			<FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">
			[<a href="<?php echo $url_phpbb?>/bb_register.<?php echo $phpEx ?>?mode=agreement">Register</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_profile.<?php echo $phpEx ?>?mode=edit">Edit Profile</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/prefs.<?php echo $phpEx ?>">Edit Preferences</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/search.<?php echo $phpEx ?>">Search</a>]<br>
			[<a href="<?php echo $url_phpbb?>/viewpmsg.<?php echo $phpEx ?>">Private Messages</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_memberlist.<?php echo $phpEx ?>">Members List</a>]&nbsp;
                        [<a href="<?php echo $url_phpbb?>/faq.<?php echo $phpEx ?>">FAQ</a>]&nbsp;
                        [<?php echo $login_logout_link?>]
			</font>
			</TD>
		</TR>
		</TABLE>
	</TD>
</TR>
</TABLE>
<?php
	break;
	case 'other':
?>
<TABLE BORDER=0 WIDTH="<?php echo $TableWidth?>" CELLPADDING="5" ALIGN="CENTER">
<TR>                    
        <TD ALIGN="CENTER" WIDTH="50%"><a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>"><IMG SRC="<?php echo $header_image?>" border="0"></a></TD>
        <TD ALIGN="CENTER" WIDTH="50%">
                <TABLE BORDER="0">   
                <TR ALIGN="CENTER" VALIGN="MIDDLE">
                        <TD ALIGN="CENTER"><FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize4?>" COLOR="<?php echo $textcolor?>"><?php echo $sitename?> Forums</font>   </TD>
                </TR>
                <TR ALIGN="CENTER" VALIGN="MIDDLE">
                        <TD ALIGN="CENTER">
			<FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">
			[<a href="<?php echo $url_phpbb?>/bb_register.<?php echo $phpEx ?>?mode=agreement">Register</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_profile.<?php echo $phpEx ?>?mode=edit">Edit Profile</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/prefs.<?php echo $phpEx ?>">Edit Preferences</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/search.<?php echo $phpEx ?>">Search</a>]<br>
			[<a href="<?php echo $url_phpbb?>/viewpmsg.<?php echo $phpEx ?>">Private Messages</a>]&nbsp;
			[<a href="<?php echo $url_phpbb?>/bb_memberlist.<?php echo $phpEx ?>">Members List</a>]&nbsp;
                        [<a href="<?php echo $url_phpbb?>/faq.<?php echo $phpEx ?>">FAQ</a>]&nbsp;
			[<?php echo $login_logout_link?>]
			</font>
                        </TD>  
                </TR>
                </TABLE>
        </TD>
</TR>
</TABLE>
<?php
	break;
case 'privmsgs';
?>
<TABLE BORDER=0 WIDTH="<?php echo $TableWidth?>" CELLPADDING="5" ALIGN="CENTER">
<TR>
        <TD ALIGN="CENTER" WIDTH="50%"><IMG SRC="<?php echo $header_image?>"></TD>
        <TD ALIGN="CENTER" WIDTH="50%"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>View your private messages</b><BR>
			[<a href="<?php echo $url_phpbb?>/sendpmsg.<?php echo $phpEx ?>">Send a private message</a>]&nbsp;
            [<a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>">Forum index</a>]
        </TD>
</TR>
</TABLE>
<?PHP
        break;
	case 'admin':
?>
<TABLE BORDER=0 WIDTH="<?php echo $TableWidth?>" CELLPADDING="5" ALIGN="CENTER">
<TR>
        <TD ALIGN="CENTER" WIDTH="50%"><a href="<?php echo $url_phpbb?>/index.<?php echo $phpEx ?>"><IMG SRC="<?php echo "$url_phpbb/$header_image"; ?>" border="0"></a></TD>
        <TD ALIGN="CENTER" WIDTH="50%">
                <TABLE BORDER="0">
                <TR ALIGN="CENTER" VALIGN="MIDDLE">
                        <TD ALIGN="CENTER"><FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize4?>" COLOR="<?php echo $textcolor?>"><?php echo $sitename?> Forums</font>   </TD>
                </TR>
                <TR ALIGN="CENTER" VALIGN="MIDDLE">
                        <TD ALIGN="CENTER">
                        <FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize1?>" COLOR="<?php echo $textcolor?>">
                        [<a href="<?php echo $url_phpbb ?>/bb_register.<?php echo $phpEx ?>?mode=agreement">Register</a>]&nbsp;
                        [<a href="<?php echo $url_phpbb ?>/bb_profile.<?php echo $phpEx ?>?mode=edit">Edit Profile</a>]&nbsp;
                        [<a href="<?php echo $url_phpbb ?>/prefs.<?php echo $phpEx ?>">Edit Preferences</a>]&nbsp;
                        [<a href="<?php echo $url_phpbb?>/search.<?php echo $phpEx ?>">Search</a>]&nbsp;<BR>
                        [<a href="<?php echo $url_phpbb ?>/viewpmsg.<?php echo $phpEx ?>">Private Messages</a>]&nbsp;
                        [<a href="<?php echo $url_phpbb ?>/bb_memberlist.<?php echo $phpEx ?>">Members List</a>]&nbsp;
                        [<a href="<?php echo $url_phpbb?>/faq.<?php echo $phpEx ?>">FAQ</a>]&nbsp;
                        [<?php echo $login_logout_link ?>]&nbsp;
                        </font>
                        </TD>
                </TR>   
                </TABLE>
        </TD>   
</TR>   
</TABLE>
<?php
	break;
}
?>
