<?php
/*
Plugin Name: [CERA] <abbr style="border-bottom: dotted;" title="Adsense Under Image">AUI</abbr> Reloaded
Plugin URI: http://bayu.freelancer.web.id/2010/02/02/adsense-under-image-reloaded/
Description: This plugin places adsense under image(s) in a post. Previously developed by <a href="">jstroh</a>.
Author: was: jstroh. now: Arief Bayu Purwanto
Version: 0.3
Author URI: http://bayu.freelancer.web.id/

Changes:

01/03/10: Version 0.1

	Initial takeover release.
	* Add multiple images option
	* Add display order option
	* Add how many ads option

01/31/10: Version 0.1

	Add CSS option


*/

/*
Copyright (C) 2007 jstroh (jstroh AT gmail DOT com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//You SHOULD modify these variable's value so that your site is UNIQUE enough.
//and that would prevent someone from hacking your adsense code.
$cr_aui_reloaded_ads_default_field_name = 'aui_adsense';

function cr_aui_implement_ads($content)
{
if(is_single())
{
	global $cr_aui_reloaded_ads_default_field_name;

    // Read in existing option value from database
    $opt_val = stripslashes(get_option( $cr_aui_reloaded_ads_default_field_name ));
    $cr_aui_reloaded_multiple_ads     = get_option('cr_aui_reloaded_multiple_ads', 'NO');
    $cr_aui_reloaded_howmany_ads      = get_option('cr_aui_reloaded_howmany_ads', 1);
    $cr_aui_reloaded_placement_option = get_option('cr_aui_reloaded_placement_option', 'sorted-up');

    if($opt_val=="")
    {
$opt_val = '
<script type="text/javascript"><!--
google_ad_client = "pub-1919341258809169";
/* 468x60, created 12/02/09 */
google_ad_slot = "9995763913";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
';
    }

$adsense_code = "<div class='cr_aui_reloaded_ads_wrapper'>$opt_val</div>";

	$pattern = '/<img[^>]*>/';
	preg_match_all($pattern, $content, $matches);

	//echo "<pre>".print_r($matches, true)."</pre>";

	$matches_image = $matches[0];

	if($cr_aui_reloaded_multiple_ads == "NO"){
		if($cr_aui_reloaded_placement_option == "sorted-up"){
			$content = str_replace($matches_image[0]."</a>",$matches_image[0]."</a>".$adsense_code,$content);
		} else if($cr_aui_reloaded_placement_option == "sorted-bottom"){
			$content = str_replace($matches_image[count($matches_image) - 1]."</a>",$matches_image[count($matches_image) - 1]."</a>".$adsense_code,$content);
		} else if($cr_aui_reloaded_placement_option == "random"){
			$content = str_replace($matches_image[rand(0, count($matches_image) - 1)]."</a>",$matches_image[rand(0, count($matches_image) - 1)]."</a>".$adsense_code,$content);
		}
	} else if($cr_aui_reloaded_multiple_ads == "YES"){
		if($cr_aui_reloaded_placement_option == "random"){
			$image_index = array();
			for($i = 0; $i<count($matches_image); $i++){
				$image_index[] = $i;
			}
			shuffle($image_index);
		}
		//echo "<pre>".print_r($image_index, true)."</pre>";
		for($i = 0; $i<$cr_aui_reloaded_howmany_ads; $i++){
			if($cr_aui_reloaded_placement_option == "sorted-up"){
				$content = str_replace($matches_image[$i]."</a>",$matches_image[$i]."</a>".$adsense_code,$content);
			} else if($cr_aui_reloaded_placement_option == "sorted-bottom"){
				$content = str_replace($matches_image[count($matches_image) - 1 - $i]."</a>",$matches_image[count($matches_image) - 1 - $i]."</a>".$adsense_code,$content);
			} else if($cr_aui_reloaded_placement_option == "random"){
				$content = str_replace($matches_image[$image_index[$i]]."</a>",$matches_image[$image_index[$i]]."</a>".$adsense_code,$content);
			}
		}
	}

	/*if(sizeof($matches)>0)
	{
		if(strstr($content,$matches[0]."</a>"))
			$content = str_replace($matches[0]."</a>",$matches[0]."</a>".$adsense_code,$content);
		else
			$content = str_replace($matches[0],$matches[0].$adsense_code,$content);
	}*/
}

    return $content;
}

function cr_aui_css_head(){
	echo "<style>
/*CR-Adsense-Under-Image-CSS-Description*/
.cr_aui_reloaded_ads_wrapper{
".get_option('cr_aui_reloaded_css_text', '')."
}
</style>";
}


add_action('wp_head','cr_aui_css_head');
add_filter('the_content','cr_aui_implement_ads');


// Hook for adding admin menus
add_action('admin_menu', 'cr_aui_add_pages');

// action function for above hook
function cr_aui_add_pages() {
    // Add a new submenu under Options:
    add_options_page('Adsense Under Image', 'Adsense Under Image', 8, 'auioptions', 'cr_aui_options_page');
}

// aui_options_page() displays the page content for the Test Options submenu
function cr_aui_options_page() {
	global $cr_aui_reloaded_ads_default_field_name;
    // variables for the field and option names 
    $hidden_field_name = 'aui_submit_hidden';
    $data_field_name = 'aui_adsense';

    //echo "<pre>".print_r($_POST, true)."</pre>";

    // Read in existing option value from database
    $opt_val = stripslashes(get_option( $cr_aui_reloaded_ads_default_field_name ));

    if($opt_val=="")
    {
$opt_val = '
<script type="text/javascript"><!--
google_ad_client = "pub-1919341258809169";
/* 468x60, created 12/02/09 */
google_ad_slot = "9995763913";
google_ad_width = 468;
google_ad_height = 60;
//-->
</script>
<script type="text/javascript"
src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
</script>
';
    }

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = stripslashes($_POST[ $data_field_name ]);

        // Save the posted value in the database
        update_option( $cr_aui_reloaded_ads_default_field_name, $opt_val );
        update_option( 'cr_aui_reloaded_multiple_ads', $_POST['cr_aui_reloaded_multiple_ads'] );
        update_option( 'cr_aui_reloaded_howmany_ads', $_POST['cr_aui_reloaded_howmany_ads'] );
        update_option( 'cr_aui_reloaded_css_text', $_POST['cr_aui_reloaded_css_text'] );
        update_option( 'cr_aui_reloaded_placement_option', $_POST['cr_aui_reloaded_placement_option'] );

        // Put an options updated message on the screen

?>

<div class="updated"><p><strong><?php _e('Options saved.', 'aui_trans_domain' ); ?></strong></p></div>
<?php
    }
    $default_css = "padding-top: 15px;";
    $cr_aui_reloaded_multiple_ads     = get_option('cr_aui_reloaded_multiple_ads', 'NO');
    $cr_aui_reloaded_howmany_ads      = get_option('cr_aui_reloaded_howmany_ads', 1);
    $cr_aui_reloaded_css_text         = get_option('cr_aui_reloaded_css_text', $default_css);
    $cr_aui_reloaded_placement_option = get_option('cr_aui_reloaded_placement_option', 'sorted-up');
    // Now display the options editing screen
    echo '<div class="wrap">';
    // header
    echo "<h2>" . __( 'Adsense Under Image <sup>reloaded</sup> Options', 'aui_trans_domain' ) . "</h2>";
    // options form
    ?>

<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Show Multiple Ads:", 'aui_trans_domain' ); ?>
<select name='cr_aui_reloaded_multiple_ads' id='cr_aui_reloaded_multiple_ads'>
<option value='YES'<?php echo ($cr_aui_reloaded_multiple_ads == 'YES') ? ' selected="selected"' : ''; ?>><?php _e("YES", 'aui_trans_domain' ); ?></option>
<option value='NO'<?php echo ($cr_aui_reloaded_multiple_ads == 'NO') ? ' selected="selected"' : ''; ?>><?php _e("NO", 'aui_trans_domain' ); ?></option>
</select><br />
<p><?php _e("YES: If you want ads to be displayed on multiple images on that particular post.", 'aui_trans_domain' ); ?></p>
<br />
</p>

<p><?php _e("How Many Ads You Want To Be Shown:", 'aui_trans_domain' ); ?>
<input type='text' name='cr_aui_reloaded_howmany_ads' id='cr_aui_reloaded_howmany_ads' value='<?php echo $cr_aui_reloaded_howmany_ads; ?>' size='4' /><br />
<p><?php _e("If you choose 'NO' on above field, this field will have no effect.", 'aui_trans_domain' ); ?></p>
<br />
</p>

<p><?php _e("Ads Placement Option:", 'aui_trans_domain' ); ?>
<select name='cr_aui_reloaded_placement_option' id='cr_aui_reloaded_placement_option'>
<option value='sorted-up'<?php echo ($cr_aui_reloaded_placement_option == 'sorted-up') ? ' selected="selected"' : ''; ?>><?php _e("SORTED-UP", 'aui_trans_domain' ); ?></option>
<option value='sorted-bottom'<?php echo ($cr_aui_reloaded_placement_option == 'sorted-bottom') ? ' selected="selected"' : ''; ?>><?php _e("SORTED-BOTTOM", 'aui_trans_domain' ); ?></option>
<option value='random'<?php echo ($cr_aui_reloaded_placement_option == 'random') ? ' selected="selected"' : ''; ?>><?php _e("RANDOM", 'aui_trans_domain' ); ?></option>
</select><br />
<p><?php _e("SORTED-UP: Your ads will be displayed from first image(s).<br />
SORTED-BOTTOM: Your ads will be displayed from last image(s).<br />
RANDOM: Your ads will be displayed randomly.", 'aui_trans_domain' ); ?></p>
<br />
</p>

<p><?php _e("Your Adsense Code:", 'aui_trans_domain' ); ?> <br />
<textarea cols="60" rows="15" name="<?php echo $data_field_name; ?>"><?php echo $opt_val; ?></textarea>
</p>

<p><?php _e("CSS Display Code:", 'aui_trans_domain' ); ?> <br />
<textarea cols="60" rows="10" name="cr_aui_reloaded_css_text"><?php echo $cr_aui_reloaded_css_text; ?></textarea>
</p>

<p class="submit">
<input type="submit" name="Submit" value="<?php _e('Update Options', 'aui_trans_domain' ) ?>" />
</p>

</form>
</div>

<?php

}

?>
