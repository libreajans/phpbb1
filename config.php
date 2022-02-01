<?php
/***************************************************************************
                          config.php  -  description
                             -------------------
    begin                : Sat June 17 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org

    $Id: config.php,v 1.39 2000/12/09 20:04:47 thefinn Exp $

 ***************************************************************************/

/***************************************************************************
 *                                         				                                
 *   This program is free software; you can redistribute it and/or modify  	
 *   it under the terms of the GNU General Public License as published by  
 *   the Free Software Foundation; either version 2 of the License, or	    	
 *   (at your option) any later version.
 *
 ***************************************************************************/

/* -- Let's Set A Few URL Paths -- */
$url_images = "http://$SERVER_NAME/phpBB/images"; // Do NOT include closing / mark!
$url_phpbb = "http://$SERVER_NAME/phpBB"; // Do NOT include closing / mark!
$url_admin = "http://$SERVER_NAME/phpBB/admin"; // Do NOT include closing / mark!
$url_smiles = "$url_images/smiles";

/* -- Email Settings -- */
// The following settings will effect all Email that phpBB sends.
// The name to sign all email's with
// To do a carrage return enter /r/n in the variable. eg James Atkinson\r\nAdministrator
$email_sig = "Yours Truely,\r\nMr. Admin";
// The From: address on emails
$email_from = "phpBB@$SERVER_NAME";


/* Stuff for priv msgs - not in DB yet: */
$allow_pmsg_bbcode = 1;
$allow_pmsg_html = 1;

/* -- Cookie settings (lastvisit, userid) -- */
$cookiedomain = "www.totalgeek.org";  // This is the only thing you need to change, set it to your domain.
$cookiename = "phpBB";
$cookiepath = "/phpBB";
$cookiesecure = false;

/* -- Cookie settings (sessions) -- */
$sesscookiename = "phpBBsession";
$sesscookietime = 3600; // number of seconds each session lasts. This is refreshed every time the logged-in user views a page.


/* -- You shouldn't have to change anything after this point, this data is obsoleat is and only used if the themes table in the DB can't be found 
      This information is set by the install script with it is run and entered into the database.
*/
$sitename = "Test.org";  // Set this to the name of your site, since you probably arn't totalgeek.org :)
$allow_html = 1; // 1 = yes, 0 = no
$allow_bbcode = 1; // 1 = yes, 0 = no
$allow_sig = 1; // 1 = yes, 0 = no
$allow_theme_create = 1;
$posts_per_page = 15; // # of posts to display per topic page.
$topics_per_page = 50;
$hot_threshold = 15;

/* -- Cosmetic Settings -- */
$bgcolor = "#00000"; // Default Black
$table_bgcolor = "#001100";
$textcolor = "#FFFFFF"; 
$linkcolor = "#11C6BD";
$vlinkcolor = "#11C6BD";
$color1 = "#6C706D"; 
$color2 = "#2E4460"; // Default Robins Egg Blue
$FontFace = "Verdana,Tahoma";
$FontSize1 = "1";
$FontSize2 = "2";
$FontSize3 = "-2";
$FontSize4 = "+1";
$FontColor = "#FFFFFF";
$TableWidth = "95%";  // Table Width - Can be either % or # 
$textcolorMessage = "#FFFFFF";  // Message Font Text Color
$FontSizeMessage = "1";  // Message Font Text Size
$FontFaceMessage = "Arial";  // Message Font Text Face

/* -- Images -- */
$header_image = "$url_images/header-dark.jpg";  // Default image that comes with phpBB
$newtopic_image = "$url_images/new_topic-dark.jpg"; //Default 'New Topic' image
$reply_image = "$url_images/reply-dark.jpg"; //Defult 'Reply' image
$reply_wquote_image = "$url_images/quote.gif";
$folder_image = "$url_images/folder.gif";
$hot_folder_image = "$url_images/hot_folder.gif";
$newposts_image = "$url_images/red_folder.gif";
$hot_newposts_image = "$url_images/hot_red_folder.gif";
$posticon = "$url_images/posticon.gif";
$edit_image = "$url_images/edit.gif";
$profile_image = "$url_images/profile.gif";
$email_image = "$url_images/email.gif";
$locked_image = "$url_images/lock.gif";
$reply_locked_image = "$url_images/reply_locked-dark.jpg";
$locktopic_image = "$url_images/lock_topic.gif";
$deltopic_image = "$url_images/del_topic.gif";
$movetopic_image = "$url_images/move_topic.gif";
$unlocktopic_image = "$url_images/unlock_topic.gif";
$www_image = "$url_images/www_icon.gif";
$icq_add_image = "$url_images/icq_add.gif";
$images_aim = "$url_images/aim.gif";
$images_yim = "$url_images/yim.gif";
$images_msnm = "$url_images/msnm.gif";
$ip_image = "$url_images/ip_logged.gif";

/* -- Other Settings -- */
$phpbbversion = "1.0.0";
