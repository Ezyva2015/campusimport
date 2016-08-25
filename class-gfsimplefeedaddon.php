<?php

require ("class-gfdbffields.php");
require ("class-gf_db_campus_order_data_importer.php");

use GF\GF_DBF_FIELDS;
use GF\GF_DB_CAMPUS_ORDER_DATA_IMPORTER;

GFForms::include_feed_addon_framework();

class GFSimpleFeedAddOn extends GFFeedAddOn {

	protected $_version = GF_SIMPLE_FEED_ADDON_VERSION;
	protected $_min_gravityforms_version = '1.9.16';
	protected $_slug = 'simplefeedaddon';
	protected $_path = 'simplefeedaddon/simplefeedaddon.php';
	protected $_full_path = __FILE__;
	protected $_title = 'Campus Order Data Importer';
	protected $_short_title = 'Campus Order Importer';

	private static $_instance = null;

	/**
	 * Get an instance of this class.
	 *
	 * @return GFSimpleFeedAddOn
	 */
	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new GFSimpleFeedAddOn();
		}

		return self::$_instance;
	}

	/**
	 * Plugin starting point. Handles hooks, loading of language files and PayPal delayed payment support.
	 */
	public function init() {

		parent::init();

		$this->add_delayed_payment_support(
			array(
				'option_label' => esc_html__( 'Subscribe contact to service x only when payment is received.', 'simplefeedaddon' )
			)
		);

	}


	// # FEED PROCESSING -----------------------------------------------------------------------------------------------

	/**
	 * Process the feed e.g. subscribe the user to a list.
	 *
	 * @param array $feed The feed object to be processed.
	 * @param array $entry The entry object currently being processed.
	 * @param array $form The form object currently being processed.
	 *
	 * @return bool|void
	 */
	public function process_feed( $feed, $entry, $form ) {
		$feedName  = $feed['meta']['feedName'];
		$mytextbox = $feed['meta']['mytextbox'];
		$checkbox  = $feed['meta']['mycheckbox'];

		// Retrieve the name => value pairs for all fields mapped in the 'mappedFields' field map.
		$field_map = $this->get_field_map_fields( $feed, 'mappedFields' );

		// Loop through the fields from the field map setting building an array of values to be passed to the third-party service.
		$merge_vars = array();
		foreach ( $field_map as $name => $field_id ) {

			// Get the field value for the specified field id
			$merge_vars[ $name ] = $this->get_field_value( $form, $entry, $field_id );

		}

		// Send the values to the third-party service.
	}

	/**
	 * Custom format the phone type field values before they are returned by $this->get_field_value().
	 *
	 * @param array $entry The Entry currently being processed.
	 * @param string $field_id The ID of the Field currently being processed.
	 * @param GF_Field_Phone $field The Field currently being processed.
	 *
	 * @return string
	 */
	public function get_phone_field_value( $entry, $field_id, $field ) {

		// Get the field value from the Entry Object.
		$field_value = rgar( $entry, $field_id );

		// If there is a value and the field phoneFormat setting is set to standard reformat the value.
		if ( ! empty( $field_value ) && $field->phoneFormat == 'standard' && preg_match( '/^\D?(\d{3})\D?\D?(\d{3})\D?(\d{4})$/', $field_value, $matches ) ) {
			$field_value = sprintf( '%s-%s-%s', $matches[1], $matches[2], $matches[3] );
		}

		return $field_value;
	}

	// # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

	/**
	 * Return the scripts which should be enqueued.
	 *
	 * @return array
	 */
	public function scripts() {
		$scripts = array(
			array(
				'handle'  => 'my_script_js',
				'src'     => $this->get_base_url() . '/js/my_script.js',
				'version' => $this->_version,
				'deps'    => array( 'jquery' ),
				'strings' => array(
					'first'  => esc_html__( 'First Choice', 'simplefeedaddon' ),
					'second' => esc_html__( 'Second Choice', 'simplefeedaddon' ),
					'third'  => esc_html__( 'Third Choice', 'simplefeedaddon' ),
				),
				'enqueue' => array(
					array(
						'admin_page' => array( 'form_settings' ),
						'tab'        => 'simplefeedaddon',
					),
				),
			),
		);

		return array_merge( parent::scripts(), $scripts );
	}

	/**
	 * Return the stylesheets which should be enqueued.
	 *
	 * @return array
	 */
	public function styles() {

		$styles = array(
			array(
				'handle'  => 'my_styles_css',
				'src'     => $this->get_base_url() . '/css/my_styles.css',
				'version' => $this->_version,
				'enqueue' => array(
					array( 'field_types' => array( 'poll' ) ),
				),
			),
		);

		return array_merge( parent::styles(), $styles );
	}

	// # ADMIN FUNCTIONS -----------------------------------------------------------------------------------------------

	/**
	 * Creates a custom page for this add-on.
	 */
	public function plugin_page() {

		$gf_dbf_fields = new GF_DBF_FIELDS();
		$gf_db_campus_order_data_importer = new GF_DB_CAMPUS_ORDER_DATA_IMPORTER();

		if(cmpi_is_local()) {
			$fullUrl = get_site_url() . '/' . 'gf-debugging-func/';
		} else {
			$fullUrl = get_site_url() . '/wp-admin/admin.php?page=simplefeedaddon';
		}

		//$inboundFields = array('Field A', 'Field B', 'Field C');

	    if(isset($_POST['saveFeed']))
		{


			//			if(!empty($_GET)) {
			//				$source_platform = $_GET['sourceselect'];
			//				$form_id 	     = $_GET['formselect'];
			//				$data      		 = json_encode($_GET);
			//			} else {
			$form_id   		 = $_POST['form_id'];
			$source_platform = $_POST['platform'];
			$data      		 = json_encode($_POST);


			//			print "<br><br><Br> form id " . $form_id;
			//			print '<br>  source name ' . $source_platform;
			//			print '<br><br>';
			print $gf_db_campus_order_data_importer->processMapping($form_id, $source_platform, "", $data);



			print'<a href="' . $fullUrl . '">';
			print "<button>Back to campus order importer home scree</button>";
			print "</a>";



		}
		else if(isset($_POST['formselect']) || $_GET['edit'] == true)
		{

			if($_GET['edit'] == true) {
				$form_id 		   = $_GET['formselect'];
				$source_platform   = $_GET['sourceselect'];
			} else {
				$form_id 		   = $_POST['formselect'];
				$source_platform   = $_POST['sourceselect'];
			}

			//			print "<br><br><Br> form id " . $form_id;
			//			print '<br>  source name ' . $source_platform;
			//			print '<br><br>';

			$form 			   = GFAPI::get_form($form_id);
			$fields  		   = $form['fields'];
			$fieldList 		   = '';

			////////////////////////////////////////////////////////////////
			//////////////Get specific selected data to mapp////////////////
			////////////////////////////////////////////////////////////////
			$editMapping = $gf_db_campus_order_data_importer->getMappedDataByFormIdAndSourcePlatFrom($form_id, $source_platform);
			foreach($fields as $field){
				$fieldList .= '<option value="'.$field->id.'">'.$field->label.' '.$field->type.'</option>';
			}
			echo '<form method="post" action="'.$_SERVER['$PHP_SELF'].'">';
			echo '<table class=" table table-striped">';
			foreach($gf_dbf_fields->dbf_fields() as $if) {
				if(!empty($editMapping))
				{
					$fieldList = $gf_db_campus_order_data_importer->mappEditedData($if, $editMapping[0]->data, $fields);
				}
				echo '<tr><td>'.$if.'</td><td><select id="'.$if.'" class="table_fields_dd" name="'.$if.'" >'.$fieldList.'</select></td></tr>';
			}
			echo '</table>';
			echo '<input type="hidden" name="saveFeed" id="saveFeed" value="saveFeed" />';
			echo '<input type="hidden" name="platform" id="platform" value="'.$source_platform.'" />';
			echo '<input type="hidden" name="form_id" id="form_id" value="'.$form_id.'" />';


			print "<a href='$fullUrl'>";
			print "<input type='button' value='Back' />";
			print "</a>";

			print "&nbsp;&nbsp";

			if(!empty($editMapping)) {
				echo '<input type="submit" value="Update" name="save_now" />';
			} else {
				echo '<input type="submit" value="Submit" name="save_now" />';
			}



			echo '</form>';


		}
		else
		{


			if($_GET['delete'] == true) {
				$source_platform = $_GET['sourceselect'];
				$form_id         = $_GET['formselect'];
				print "<br><br> deleting..";


				if($gf_db_campus_order_data_importer->delete_dual($form_id, $source_platform)){
					echo "<br><Br>successfully deleted";
				} else {
					print "<br><Br>failed to delete";
				}
			}

			echo '<form method="post" action="'.$_SERVER['$PHP_SELF'].'">';
			$platforms = array('Class Super', 'BGL Simple Fund 360', 'BGL Simple Fund Desktop');
			$forms = GFAPI::get_forms();
			echo 'Source Platform: ';
			echo '<select id="sourceselect" name="sourceselect" >';
			 foreach($platforms as $platform){
				echo '<option value="'.$platform.'">'.$platform.'</option>';
			 }
			echo '</select><br/>';
			echo 'Gravity Form: ';
			echo '<select id="formselect" name="formselect" >';
			 foreach($forms as $form){
				echo '<option value="'.$form['id'].'">'.$form['title'].'</option>';
			 }
			echo '</select>';
			echo '<input type="submit" value="Submit" />';
			echo '</form>';
			?>



			<?php $savedMapping = $gf_db_campus_order_data_importer->query(); ?>






		 	<br><br>
			<h4> Saved Mapping </h4>
			<table class="table table-bordered">
				<thead>
				<tr>
					<th>Source Platform</th>
					<th>Gravity Form</th>
					<th>Gravity Form Id</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
				</thead>
				<tbody>



						<?php



						?>


						<?php foreach($savedMapping as $key => $value) {



							if(cmpi_is_local()) {
								$edit_path = "?sourceselect=" . $value->source_platform . "&amp;formselect=" . $value->form_id . "&amp;edit=true";
								$delete_path = "?admin=1&amp;sourceselect=". $value->source_platform . "&amp;formselect=" . $value->form_id . "&amp;delete=true";
							} else {
								$edit_path =  get_site_url() . "/wp-admin/admin.php?page=simplefeedaddon&amp;sourceselect=" . $value->source_platform . "&amp;formselect=" . $value->form_id . "&amp;edit=true";
								$delete_path = get_site_url() . "/wp-admin/admin.php?page=simplefeedaddon&amp;admin=1&amp;sourceselect=". $value->source_platform . "&amp;formselect=" . $value->form_id . "&amp;delete=true";
							}



							$form = GFAPI::get_form($value->form_id);
							// echo "<pre>";
							// print_r($form);
							// echo "</pre>";
							// print "" . $form['title'];
							?>
							<tr>
								<td><?php print $value->source_platform; ?></td>
								<td><?php print $form['title']; ?></td>
								<td><?php print $value->form_id; ?></td>
								<td>
									<a href="<?php print $edit_path; ?>">
										<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span> <span class="glyphicon-class">Edit</span>
									</a>
								</td>
								<td>
									<a href="<?php print $delete_path; ?>">
									 	<span class="glyphicon glyphicon-trash" aria-hidden="true"></span> <span class="glyphicon-class">Delete</span>
									</a>
								</td>
							</tr>
						<?php } ?>
				</tbody>
			</table> <?php
		}
		?>

		<!-- This will form the table saved mapping -->
		<style>
				.hoverTable{
					width:100%;
					border-collapse:collapse;
				}
				.hoverTable td{
					padding:7px; border:#4e95f4 1px solid;
				}
				/* Define the default color for all the table rows */
				.hoverTable tr{
					background: #b8d1f3;
				}
				/* Define the hover highlight color for the table row */
				.hoverTable tr:hover {
					  background-color: #ffff99;
				}
				.table_fields_dd{
					padding:5px;
					float:right;
				}

			table{background-color:transparent}caption{padding-top:8px;padding-bottom:8px;color:#777;text-align:left} th{text-align:left}.table{width:100%;max-width:100%;margin-bottom:20px} .table>tbody>tr>td,.table>tbody>tr>th,.table>tfoot>tr>td,.table>tfoot>tr>th,.table>thead>tr>td,.table>thead>tr>th{padding:8px;line-height:1.42857143;vertical-align:top;border-top:1px solid #ddd}.table>thead>tr>th{vertical-align:bottom;border-bottom:2px solid #ddd}.table>caption+thead>tr:first-child>td,.table>caption+thead>tr:first-child>th,.table>colgroup+thead>tr:first-child>td,.table>colgroup+thead>tr:first-child>th,.table>thead:first-child>tr:first-child>td,.table>thead:first-child>tr:first-child>th{border-top:0}.table>tbody+tbody{border-top:2px solid #ddd}.table .table{background-color:#fff}.table-condensed>tbody>tr>td,.table-condensed>tbody>tr>th,.table-condensed>tfoot>tr>td,.table-condensed>tfoot>tr>th,.table-condensed>thead>tr>td,.table-condensed>thead>tr>th{padding:5px}.table-bordered{border:1px solid #ddd}.table-bordered>tbody>tr>td,.table-bordered>tbody>tr>th,.table-bordered>tfoot>tr>td,.table-bordered>tfoot>tr>th,.table-bordered>thead>tr>td,.table-bordered>thead>tr>th{border:1px solid #ddd}.table-bordered>thead>tr>td,.table-bordered>thead>tr>th{border-bottom-width:2px}.table-striped>tbody>tr:nth-of-type(odd){background-color:#f9f9f9}.table-hover>tbody>tr:hover{background-color:#f5f5f5}table col[class*=col-]{position:static;display:table-column;float:none}table td[class*=col-],table th[class*=col-]{position:static;display:table-cell;float:none}.table>tbody>tr.active>td,.table>tbody>tr.active>th,.table>tbody>tr>td.active,.table>tbody>tr>th.active,.table>tfoot>tr.active>td,.table>tfoot>tr.active>th,.table>tfoot>tr>td.active,.table>tfoot>tr>th.active,.table>thead>tr.active>td,.table>thead>tr.active>th,.table>thead>tr>td.active,.table>thead>tr>th.active{background-color:#f5f5f5}.table-hover>tbody>tr.active:hover>td,.table-hover>tbody>tr.active:hover>th,.table-hover>tbody>tr:hover>.active,.table-hover>tbody>tr>td.active:hover,.table-hover>tbody>tr>th.active:hover{background-color:#e8e8e8}.table>tbody>tr.success>td,.table>tbody>tr.success>th,.table>tbody>tr>td.success,.table>tbody>tr>th.success,.table>tfoot>tr.success>td,.table>tfoot>tr.success>th,.table>tfoot>tr>td.success,.table>tfoot>tr>th.success,.table>thead>tr.success>td,.table>thead>tr.success>th,.table>thead>tr>td.success,.table>thead>tr>th.success{background-color:#dff0d8}.table-hover>tbody>tr.success:hover>td,.table-hover>tbody>tr.success:hover>th,.table-hover>tbody>tr:hover>.success,.table-hover>tbody>tr>td.success:hover,.table-hover>tbody>tr>th.success:hover{background-color:#d0e9c6}.table>tbody>tr.info>td,.table>tbody>tr.info>th,.table>tbody>tr>td.info,.table>tbody>tr>th.info,.table>tfoot>tr.info>td,.table>tfoot>tr.info>th,.table>tfoot>tr>td.info,.table>tfoot>tr>th.info,.table>thead>tr.info>td,.table>thead>tr.info>th,.table>thead>tr>td.info,.table>thead>tr>th.info{background-color:#d9edf7}.table-hover>tbody>tr.info:hover>td,.table-hover>tbody>tr.info:hover>th,.table-hover>tbody>tr:hover>.info,.table-hover>tbody>tr>td.info:hover,.table-hover>tbody>tr>th.info:hover{background-color:#c4e3f3}.table>tbody>tr.warning>td,.table>tbody>tr.warning>th,.table>tbody>tr>td.warning,.table>tbody>tr>th.warning,.table>tfoot>tr.warning>td,.table>tfoot>tr.warning>th,.table>tfoot>tr>td.warning,.table>tfoot>tr>th.warning,.table>thead>tr.warning>td,.table>thead>tr.warning>th,.table>thead>tr>td.warning,.table>thead>tr>th.warning{background-color:#fcf8e3}.table-hover>tbody>tr.warning:hover>td,.table-hover>tbody>tr.warning:hover>th,.table-hover>tbody>tr:hover>.warning,.table-hover>tbody>tr>td.warning:hover,.table-hover>tbody>tr>th.warning:hover{background-color:#faf2cc}.table>tbody>tr.danger>td,.table>tbody>tr.danger>th,.table>tbody>tr>td.danger,.table>tbody>tr>th.danger,.table>tfoot>tr.danger>td,.table>tfoot>tr.danger>th,.table>tfoot>tr>td.danger,.table>tfoot>tr>th.danger,.table>thead>tr.danger>td,.table>thead>tr.danger>th,.table>thead>tr>td.danger,.table>thead>tr>th.danger{background-color:#f2dede}.table-hover>tbody>tr.danger:hover>td,.table-hover>tbody>tr.danger:hover>th,.table-hover>tbody>tr:hover>.danger,.table-hover>tbody>tr>td.danger:hover,.table-hover>tbody>tr>th.danger:hover{background-color:#ebcccc}.table-responsive{min-height:.01%;overflow-x:auto}@media screen and (max-width:767px){.table-responsive{width:100%;margin-bottom:15px;overflow-y:hidden;-ms-overflow-style:-ms-autohiding-scrollbar;border:1px solid #ddd}.table-responsive>.table{margin-bottom:0}.table-responsive>.table>tbody>tr>td,.table-responsive>.table>tbody>tr>th,.table-responsive>.table>tfoot>tr>td,.table-responsive>.table>tfoot>tr>th,.table-responsive>.table>thead>tr>td,.table-responsive>.table>thead>tr>th{white-space:nowrap}.table-responsive>.table-bordered{border:0}.table-responsive>.table-bordered>tbody>tr>td:first-child,.table-responsive>.table-bordered>tbody>tr>th:first-child,.table-responsive>.table-bordered>tfoot>tr>td:first-child,.table-responsive>.table-bordered>tfoot>tr>th:first-child,.table-responsive>.table-bordered>thead>tr>td:first-child,.table-responsive>.table-bordered>thead>tr>th:first-child{border-left:0}.table-responsive>.table-bordered>tbody>tr>td:last-child,.table-responsive>.table-bordered>tbody>tr>th:last-child,.table-responsive>.table-bordered>tfoot>tr>td:last-child,.table-responsive>.table-bordered>tfoot>tr>th:last-child,.table-responsive>.table-bordered>thead>tr>td:last-child,.table-responsive>.table-bordered>thead>tr>th:last-child{border-right:0}.table-responsive>.table-bordered>tbody>tr:last-child>td,.table-responsive>.table-bordered>tbody>tr:last-child>th,.table-responsive>.table-bordered>tfoot>tr:last-child>td,.table-responsive>.table-bordered>tfoot>tr:last-child>th{border-bottom:0}}
		</style>
		<?php

	}

	/**
	 * Configures the settings which should be rendered on the add-on settings tab.
	 *
	 * @return array
	 */
	public function plugin_settings_fields() {
		return array(
			array(
				'title'  => esc_html__( 'Simple Add-On Settings', 'simplefeedaddon' ),
				'fields' => array(
					array(
						'name'    => 'textbox',
						'tooltip' => esc_html__( 'This is the tooltip', 'simplefeedaddon' ),
						'label'   => esc_html__( 'This is the label', 'simplefeedaddon' ),
						'type'    => 'text',
						'class'   => 'small',
					),
				),
			),
		);
	}

	/**
	 * Configures the settings which should be rendered on the feed edit page in the Form Settings > Simple Feed Add-On area.
	 *
	 * @return array
	 */
	public function feed_settings_fields() {
		 
	
		return array(
			array(
				'title'  => esc_html__( 'Simple Feed Settings', 'simplefeedaddon' ),
				'fields' => array(
					array(
						'label'   => esc_html__( 'Feed name', 'simplefeedaddon' ),
						'type'    => 'text',
						'name'    => 'feedName',
						'tooltip' => esc_html__( 'This is the tooltip', 'simplefeedaddon' ),
						'class'   => 'small',
					),
					array(
						'label'   => esc_html__( 'Textbox', 'simplefeedaddon' ),
						'type'    => 'text',
						'name'    => 'mytextbox',
						'tooltip' => esc_html__( 'This is the tooltip', 'simplefeedaddon' ),
						'class'   => 'small',
					),
					array(
						'label'   => esc_html__( 'My checkbox', 'simplefeedaddon' ),
						'type'    => 'checkbox',
						'name'    => 'mycheckbox',
						'tooltip' => esc_html__( 'This is the tooltip', 'simplefeedaddon' ),
						'choices' => array(
							array(
								'label' => esc_html__( 'Enabled', 'simplefeedaddon' ),
								'name'  => 'mycheckbox',
							),
						),
					),
					array(
						'name'      => 'mappedFields',
						'label'     => esc_html__( 'Map Fields', 'simplefeedaddon' ),
						'type'      => 'field_map',
						'field_map' => array(
							array(
								'name'       => 'email',
								'label'      => esc_html__( 'Email', 'simplefeedaddon' ),
								'required'   => 0,
								'field_type' => array( 'email', 'hidden' ),
								'tooltip' => esc_html__( 'This is the tooltip', 'simplefeedaddon' ),
							),
							array(
								'name'     => 'name',
								'label'    => esc_html__( 'Name', 'simplefeedaddon' ),
								'required' => 0,
							),
							array(
								'name'       => 'phone',
								'label'      => esc_html__( 'Phone', 'simplefeedaddon' ),
								'required'   => 0,
								'field_type' => 'phone',
							),
						),
					),
					array(
						'name'           => 'condition',
						'label'          => esc_html__( 'Condition', 'simplefeedaddon' ),
						'type'           => 'feed_condition',
						'checkbox_label' => esc_html__( 'Enable Condition', 'simplefeedaddon' ),
						'instructions'   => esc_html__( 'Process this simple feed if', 'simplefeedaddon' ),
					),
				),
			),
		);
	}

	/**
	 * Configures which columns should be displayed on the feed list page.
	 *
	 * @return array
	 */
	public function feed_list_columns() {
		return array(
			'feedName'  => esc_html__( 'Name', 'simplefeedaddon' ),
			'mytextbox' => esc_html__( 'My Textbox', 'simplefeedaddon' ),
		);
	}

	/**
	 * Format the value to be displayed in the mytextbox column.
	 *
	 * @param array $feed The feed being included in the feed list.
	 *
	 * @return string
	 */
	public function get_column_value_mytextbox( $feed ) {
		return '<b>' . rgars( $feed, 'meta/mytextbox' ) . '</b>';
	}

	/**
	 * Prevent feeds being listed or created if an api key isn't valid.
	 *
	 * @return bool
	 */
	public function can_create_feed() {

		// Get the plugin settings.
		$settings = $this->get_plugin_settings();

		// Access a specific setting e.g. an api key
		$key = rgar( $settings, 'apiKey' );

		return true;
	}

}




if(!function_exists('cmpi_is_local')){
	function cmpi_is_local() {
		if($_SERVER['HTTP_HOST'] == 'localhost'
				|| substr($_SERVER['HTTP_HOST'],0,3) == '10.'
				|| substr($_SERVER['HTTP_HOST'],0,7) == '192.168') return true;
		return false;
	}
}
