<?php
/***************************************************************************
                          logout.php  -  description
                             -------------------
    begin                : Wed June 19 2000
    copyright            : (C) 2000 by James Atkinson
    email                : james@totalgeek.org

    $Id: logout.php,v 1.6 2000/11/15 04:57:15 thefinn Exp $

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
 * logout.php - Nathan Codding
 * - Used for logging out a user and deleting a session.
 */
include('extention.inc');
include('functions.'.$phpEx);
include('config.'.$phpEx);
require('auth.'.$phpEx);
$pagetitle = "Logout";
$pagetype = "logout";

/* Note: page_header.php is included later on, because this page needs to be able to send a cookie. */

if ($user_logged_in) {
	end_user_session($userdata[user_id], $db);
}

	header("Location: $url_phpbb");
require('page_tail.'.$phpEx);
?>
