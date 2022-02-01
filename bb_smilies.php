<?php
  /***************************************************************************
   *                             bb_smilies.php  -  description
   *                              -------------------
   *     begin                : Monday, September 11, 2000
   *     copyright            : (C) 2000 by James Atkinson
   *     email                : james@totalgeek.org
   * 
   *     $Id: bb_smilies.php,v 1.6 2000/11/15 04:57:15 thefinn Exp $
   * 
   *  ***************************************************************************/
  
  /***************************************************************************
   *  *                                                                                                      
   *  *   This program is free software; you can redistribute it and/or modify       
   *  *   it under the terms of the GNU General Public License as published by  
   *  *   the Free Software Foundation; either version 2 of the License, or          
   *  *   (at your option) any later version.
   *  *
   *  ***************************************************************************/
include('extention.inc');
include('functions.'.$phpEx);
include('config.'.$phpEx);
require('auth.'.$phpEx);
$pagetitle = "Smilies List";
$pagetype = "other";
include('page_header.'.$phpEx);
?>
  <p><br><p>
  <center>
  <TABLE width="<?php echo $table_width?>">
  <tr><td colspan=3 bgcolor="<?php echo $color1?>">
  <FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize2?>" COLOR="<?php echo $textcolor?>">
  <center>
  S M I L I E S
  </CENTER>
  <p>
  Smilies are small graphical images that can be used to convey an emotion or feeling.
  <P>
  </td></tr>
  <TR bgcolor="<?php echo $color2?>">
  <TR bgcolor="<?php echo $color2?>">
  <TD><FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>What to Type</b></FONT></td>
  <TD><FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Emotion</b></FONT></td>
  <TD><FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><b>Graphic That Will Appear</b></FONT></td>
  </tr>
  <?php
  
  if ($getsmiles = mysql_query("SELECT * FROM smiles")) {
     while ($smile = mysql_fetch_array($getsmiles)) {
	?>
	  
	  <TR BGCOLOR="<?php echo $color2?>"><TD><FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize2?>" COLOR="<?php echo $textcolor?>"><?php echo $smile[code]?></FONT></td><td><FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize2?>" COLOR="<?php echo $textcolor?>"> <?php echo $smile[emotion]?>&nbsp;</FONT></td><td> <IMG SRC="<? echo "$url_smiles/$smile[smile_url]";?>"></td></tr>
	  
	  <?php
     }
     
  } else {
     echo "Could not retrieve from the smile database.";
  }

?>
  <tr><td colspan=3 bgcolor="<?php echo $color1?>">
  <FONT FACE="<?php echo $FontFace?>" SIZE="<?php echo $FontSize2?>" COLOR="<?php echo $textcolor?>">
  <center>
  <P>
  Note: you may disable smilies in any post you are making, if you like.  Look for the "Disable Smilies" box on each post page, if you want to turn off smilie conversion in your particular post.
  </font>
  </td></tr>
  </table>
  </center>
  
  <p><br><p>
<?php
  
include('page_tail.'.$phpEx);
?>
  
