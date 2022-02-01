<?php
/***************************************************************************
                            bb_memberlist.php  -  description
                             -------------------
    begin                : Sat June 17 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org

    $Id: bb_memberlist.php,v 1.18 2000/11/23 08:00:59 thefinn Exp $

 ***************************************************************************/

/***************************************************************************
 *                                         				                                
 *   This program is free software; you can redistribute it and/or modify  	
 *   it under the terms of the GNU General Public License as published by  
 *   the Free Software Foundation; either version 2 of the License, or	    	
 *   (at your option) any later version.
 *
 ***************************************************************************/

/*
*  This Page Created By:  Yokhannan
*  Email:  support@4cm.com
*  Created On: Saturday, July 22, 2000
*
*  Edited: Thursday, October 19, 2000
*    Added a better ICQ method.
*    Changed all the Font Settings to use Variables
*    Made some minor PHP-HTML changes
*  
* Oct 27, 2000 
*    Added pagination
*       - James
*/
include('extention.inc');
include('functions.'.$phpEx);
include('config.'.$phpEx);
require('auth.'.$phpEx);
$pagetitle = "Members List";
$pagetype = "other";
include("page_header.$phpEx");

/**
 * July 25, 2000 - Nathan.
 * Sorting by # of posts would be cool.. but I can't find a way to do it
 * without requiring 1 or 2 additional SQL queries per user. Yuck.
 */

switch ($sortby) {
	case '':
		$sortby = "user_id ASC";
	break;
	case 'user':
		$sortby = "username ASC";
	break;
	case 'from':
		$sortby = "user_from ASC";
	break;
	case 'posts':
		$sortby = "user_posts DESC";
	break;
}

if(!$start) $start = 0;

$sql = "SELECT * FROM users WHERE user_id != -1 AND user_level != -1 ORDER BY $sortby LIMIT $start, $topics_per_page";
if(!$result = mysql_query($sql, $db))
	die("An Error Occured<HR>Could not contact the database. Please check and make sure you have supplied the correct user name and password for the database<BR>$sql");

?>
<TABLE BORDER="0" CELLPADDING="1" CELLSPACING="0" ALIGN="CENTER" VALIGN="TOP" WIDTH="<?php echo $tablewidth?>">
    <tr> 
      <td align=right>
<?php
  $sql = "SELECT count(*) AS total FROM users WHERE user_level != -1";
if(!$r = mysql_query($sql, $db))
  die("Error could not contact the database!</TABLE></TABLE>");
list($all_topics) = mysql_fetch_array($r);
$count = 1;
$next = $start + $topics_per_page;
if($all_topics > $topics_per_page) {
   echo "<font size=-1>\n<a href=\"bb_memberlist.$phpEx?start=$next&sortby=$sortby\">Next Page</a> | ";
   for($x = 0; $x < $all_topics; $x++) {
      if(!($x % $topics_per_page)) {
	 if($x == $start)
	   echo "$count\n";
	 else
	   echo "<a href=\"bb_memberlist.$phpEx?&start=$x&sortby=$sortby\">$count</a>\n";
	 $count++;
	 if(!($count % 10)) echo "<BR>";
      }
   }
}
$next = 0;

?>
  <table width="100%" border="0" cellspacing="2" cellpadding="0" bordercolor="#000000" bgcolor="#000000">
          <tr nowrap> 
            <td>

<?PHP

$row = mysql_fetch_array($result);

if (!$row) {
	echo "<P><font face=Verdana Size=1>We Presently Have No Members To Report!";
} else {
?>
	<table width="100%"" border="0" cellspacing="1" cellpadding="0">
	<TR>
		<td bgcolor="<?php echo $color2?>" width="25%" height="25" nowrap><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>">&nbsp;<B><a href="<?php echo $PHP_SELF?>?sortby=user&start=<?php echo $start?>">User Name</a></B></TD>
		<td bgcolor="<?php echo $color2?>" width="30%" height="25"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>">&nbsp;<B><a href="<?php echo $PHP_SELF?>?sortby=from&start=<?php echo $start?>">From</a></B></TD>
		<td bgcolor="<?php echo $color2?>" width="8%" height="25" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><B>Date Joined</B></TD>
		<td bgcolor="<?php echo $color2?>" width="8%" height="25" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b><a href="<?php echo $PHP_SELF?>?sortby=posts&start=<?php echo $start?>">Posts</a></b></td>
		<td bgcolor="<?php echo $color2?>" width="8%" height="25" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><B>Email</B></TD>
		<td bgcolor="<?php echo $color2?>" width="6%" height="25" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><B>URL</B></TD>
		<td bgcolor="<?php echo $color2?>" width="6%" height="25" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><B>ICQ</B></TD>
		<td bgcolor="<?php echo $color2?>" width="6%" height="25" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><B>AIM</B></TD>
		<td bgcolor="<?php echo $color2?>" width="5%" height="25" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><B>YIM</B></TD>
		<td bgcolor="<?php echo $color2?>" width="6%" height="25" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><B>MSNM</B></TD>
	</TR>
<?

	do {
		if ($row[user_viewemail]) {
			$email = "<a href=\"mailto:$row[user_email]\"><img src=\"$email_image\" width=\"33\" height=\"17\" border=\"0\" alt=\"Email $row[username]\"></a>";
		} else {
			$email = "&nbsp;";
		}
		if ($row[user_website]) {
			$www = "<a href=\"$row[user_website]\"><img src=\"$www_image\" width=\"34\" height=\"17\" border=\"0\" alt=\"Visit $row[username]'s Web Site\"></a>";
		} else {
			$www = "&nbsp;";
		}
		if ($row[user_icq]) {
			$icq = "<a href=\"http://wwp.icq.com/scripts/search.dll?to=$row[user_icq]\"><img src=\"$icq_add_image\" width=\"32\" height=\"17\" border=\"0\" alt=\"Add $row[username]\"></a>";
		} else {
			$icq = "&nbsp;";
		}
		if ($row[user_aim]) {
			$aim = "<a href=\"aim:goim?screenname=$row[user_aim]&message=Hi+$row[user_aim].+Are+you+there?\"><img src=\"$images_aim\" width=\"30\" height=\"17\" border=\"0\" alt=\"AIM $row[user_aim]\"></a></TD>";
		} else {
			$aim = "&nbsp;";
		}
		if ($row[user_yim]) {
			$yim = "<a href=\"http://edit.yahoo.com/config/send_webmesg?.target=$row[user_yim]&.src=pg\"><img src=\"$images_yim\" width=\"16\" height=\"16\" border=\"0\" alt=\"YIM $row[user_yim]\"></a>";
		} else {
			$yim = "&nbsp;";
		}
		if ($row[user_msnm]) {
			$msnm = "<a href=\"$url_phpbb/bb_profile.$phpEx?mode=view&user=$row[user_id]\"><img src=\"$images_msnm\" width=\"16\" height=\"16\" border=\"0\" alt=\"MSNM $row[user_msnm]\"></a>";
		} else {
			$msnm = "&nbsp;";
		}
		if ($row[user_regdate]) 
			$regdate = $row[user_regdate];
		else
			$regdate = "&nbsp;";
?>
	<TR>
		<td bgcolor="<?php echo $color2?>" width="25%" height="30" nowrap><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>">&nbsp;<a href="<?php echo $url_phpbb?>/bb_profile.<?php echo $phpEx?>?mode=view&user=<?php echo $row[user_id]?>"><?php echo $row[username]?></a></TD>
		<td bgcolor="<?php echo $color1?>" width="30%" height="30"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>">&nbsp;<?php echo $row[user_from]?></TD>
		<td bgcolor="<?php echo $color2?>" width="8%" height="30" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"> <?php echo $regdate?> </td>
		<td bgcolor="<?php echo $color1?>" width="8%" height="30" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"> <?php echo $row[user_posts]?> </td>
		<td bgcolor="<?php echo $color2?>" width="8%" height="30" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"> <?php echo $email?> </TD>
		<td bgcolor="<?php echo $color1?>" width="6%" height="30" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"> <?php echo $www?> </TD>
		<td bgcolor="<?php echo $color2?>" width="6%" height="30" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"> <?php echo $icq?> </TD>
		<td bgcolor="<?php echo $color1?>" width="6%" height="30" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"> <?php echo $aim?> </TD>
		<td bgcolor="<?php echo $color2?>" width="5%" height="30" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"> <?php echo $yim?> </TD>
		<td bgcolor="<?php echo $color1?>" width="6%" height="30" nowrap align="center"><FONT FACE="<?php echo $FontFace?>" SIZE="<? echo $FontSize2?>" COLOR="<?php echo $textcolor?>"> <?php echo $msnm?> </TD>
	</TR>
<?
	} while ($row = mysql_fetch_array($result));
	echo "</table> \n";
}
$count = 1;
$next = $start + $topics_per_page;
if($all_topics > $topics_per_page) {
   echo "<font size=-1>\n<a href=\"bb_memberlist.$phpEx?start=$next&sortby=$sortby\">Next Page</a> | ";
   for($x = 0; $x < $all_topics; $x++) {
      if(!($x % $topics_per_page)) {
	 if($x == $start)
	   echo "$count\n";
	 else
	   echo "<a href=\"bb_memberlist.$phpEx?&start=$x&sortby=$sortby\">$count</a>\n";
	 $count++;
	 if(!($count % 10)) echo "<BR>";
      }
   }
}
echo "<BR>\n";
?>
           </td>
            </tr>
          </table>
        </td>                                   
    </tr>
  </table>
  
  
<?php
include('page_tail.'.$phpEx);
?>
