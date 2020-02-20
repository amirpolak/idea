<?php
/*  Copyright 2016 Tomer Shaked

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    *1: By default the key label for the name must be FNAME
    *2: parse first & last name
    *3: ensure we set first and last name exist
    *4: otherwise user provided just one name
    *5: By default the key label for the name must be FNAME
    *6: check if subscribed
    *bh: email_type
    *aw: double_optin
    *xz: update_existing
    *rd: replace_interests
    *gr: send_welcome
*/



function res_author() {

	$author_pre = 'Contact form 7 Responder extension by ';
	$author_name = 'Tomer Shaked';
	$author_url = 'http://kcs.co.il';
	$author_title = 'Tomer Shaked - Web Developer';

	$res_author = '<p style="display: none !important">';
  $res_author .= $author_pre;
  $res_author .= '<a href="'.$author_url.'" ';
  $res_author .= 'title="'.$author_title.'" ';
  $res_author .= 'target="_blank">';
  $res_author .= ''.$author_title.'';
  $res_author .= '</a>';
  $res_author .= '</p>'. "\n";

  return $res_author;
}



function res_referer() {

  // $res_referer_url = $THE_REFER=strval(isset($_SERVER['HTTP_REFERER']));

  if(isset($_SERVER['HTTP_REFERER'])) {

    $res_referer_url = $_SERVER['HTTP_REFERER'];

  } else {

    $res_referer_url = 'direct visit';

  }

	$res_referer = '<p style="display: none !important"><span class="wpcf7-form-control-wrap referer-page">';
  $res_referer .= '<input type="hidden" name="referer-page" ';
  $res_referer .= 'value="'.$res_referer_url.'" ';
  $res_referer .= 'size="40" class="wpcf7-form-control wpcf7-text referer-page" aria-invalid="false">';
  $res_referer .= '</span></p>'. "\n";

  return $res_referer;
}



function res_getRefererPage( $form_tag ) {

  if ( $form_tag['name'] == 'referer-page' ) {
          $form_tag['values'][] = $_SERVER['HTTP_REFERER'];
  }
  return $form_tag;
}

if ( !is_admin() ) {
        add_filter( 'wpcf7_form_tag', 'res_getRefererPage' );
}

define( 'RAV_MESSER_URL', 'http://kcs.co.il' );
define( 'RAV_MESSER_AUTH', 'http://kcs.co.il' );
define( 'RAV_MESSER_AUTH_COMM', '<!-- campaignmonitor extension -->' );
define( 'RAV_MESSER_NAME', 'Campaign Monitor Extension' );



