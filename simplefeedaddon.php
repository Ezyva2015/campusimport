<?php
/*
Plugin Name: Campus Order Importer
Plugin URI: http://www.gravityforms.com
Description: A simple add-on to demonstrate the use of the Add-On Framework
Version: 2.0
Author: Rocketgenius
Author URI: http://www.rocketgenius.com

------------------------------------------------------------------------
Copyright 2012-2016 Rocketgenius Inc.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

define( 'GF_SIMPLE_FEED_ADDON_VERSION', '2.0' );

add_action( 'gform_loaded', array( 'GF_Simple_Feed_AddOn_Bootstrap', 'load' ), 5 );

class GF_Simple_Feed_AddOn_Bootstrap {

	public static function load() {

		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			return;
		}

		require_once( 'class-gfsimplefeedaddon.php' );

		GFAddOn::register( 'GFSimpleFeedAddOn' );
	}

}

function gf_simple_feed_addon() {
	return GFSimpleFeedAddOn::get_instance();
}





/**
 * Local development
 */
add_shortcode("gf_debugging", "gf_debugging_func");
function gf_debugging_func(){
	require_once( 'class-gfsimplefeedaddon.php' );
	$a = new GFSimpleFeedAddOn();
	$a->plugin_page();
}





/**
 * Auto generate plugin
 */


register_activation_hook( __FILE__, 'sf_campus_install' );
register_activation_hook( __FILE__, 'sf_campus_install_data' );

global $sf_campus_db_version;
$sf_campus_db_version = '1.0';

function sf_campus_install () {

	global $wpdb;
	$table_name = $wpdb->prefix . "rg_campus_order_data_importer";

	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
      id int(12) NOT NULL AUTO_INCREMENT,
      created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      form_id int(12) NOT NULL,
      gravity_form text NOT NULL,
      source_platform text NOT NULL,
      data text NOT NULL,
      UNIQUE KEY id (id)
    ) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'sf_campus_db_version', $sf_campus_db_version );
}

function sf_campus_install_data() {
	global $wpdb;

	$data = '{"Reference":"137","Fund_Name":"2","{"Reference":"137","Fund_Name":"2","State_Law_To_Govern_The_Fund":"9","Fund_Address":"139","Fund_Address_1_Hidden":"366","Fund_Address_2_Hidden":"367","Fund_Address_3_Hidden":"368","Fund_Address_4_Hidden":"369","Fund_Address_5_Hidden":"370","Trustee_Meeting_Address_Select":"337","Trustee_Meeting_Address_Text":"330","Trustee_Metting_Address_1_Hidden":"371","Trustee_Metting_Address_2_Hidden":"372","Trustee_Metting_Address_3_Hidden":"373","Trustee_Metting_Address_4_Hidden":"374","Trustee_Metting_Address_5_Hidden":"375","How_many_Members_will_the_Fund_have":"12","Title_1":"71","Given_Names_1":"14","Family_Name_1":"72","Gender_1":"417","Date_of_Birth_1":"358","TFN_1":"81","Member_1_Residential_Address":"17","Member_1_Address_Search":"246","Member_1_Address_1_Hidden":"376","Member_1_Address_2_Hidden":"380","Member_1_Address_3_Hidden":"379","Member_1_Address_4_Hidden":"378","Member_1_Address_5_Hidden":"377","Title_2":"73","Given_Names_2":"25","Family_Name_2":"25","Gender_2":"418","Date_of_Birth_2":"137","TFN_2":"82","Member_2_Residential_Address":"169","Member_2_Address_Search":"247","Member_2_Address_1_Hidden":"381","Member_2_Address_2_Hidden":"382","Member_2_Address_3_Hidden":"383","Member_2_Address_4_Hidden":"384","Member_2_Address_5_Hidden":"385","Title_3":"75","Given_Names_3":"33","Family_Name_3":"33","Gender_3":"419","Date_of_Birth_3":"137","TFN_3":"83","Member_3_Residential_Address":"170","Member_3_Address_Search":"248","Member_3_Address_1_Hidden":"386","Member_3_Address_2_Hidden":"387","Member_3_Address_3_Hidden":"388","Member_3_Address_4_Hidden":"389","Member_3_Address_5_Hidden":"390","Title_4":"77","Given_Names_4":"42","Family_Name_4":"78","Gender_4":"420","Date_of_Birth_4":"137","TFN_4":"84","Member_4_Residential_Address":"184","Member_4_Address_Search":"249","Member_4_Address_1_Hidden":"391","Member_4_Address_2_Hidden":"392","Member_4_Address_3_Hidden":"393","Member_4_Address_4_Hidden":"394","Member_4_Address_5_Hidden":"395","Trustee_Type":"111","Individual_Title":"111","Individual_Trustee_2_-_Given_Names":"119","Individual_Trustee_2_-_Family_Name":"120","Individual_Gender":"421","Corporate_Trustee_Name":"137","Corporate_Trustee_ACN":"137","Corporate_Date_of_Incorporation":"137","Corporate_Trustee_Registered_Address":"137","Does_the_Company_have_an_additional_Director_who_is_not_a_Member_of_the_Fund?":"137","saveFeed":"saveFeed","platform":"BGL Simple Fund Desktop","form_id":"6","save_now":"Update"}';
	$source_platform = 'BGL Simple Fund Desktop';
	$form_id = 6;
	$created_at = current_time( 'mysql' );

	$table_name = $wpdb->prefix . 'rg_campus_order_data_importer';

	$wpdb->insert(
			$table_name,
			array(
					'form_id'=>$form_id,
					'source_platform'=>$source_platform,
					'data' => $data,
					'created_at' => $created_at,
			)
	);
}














