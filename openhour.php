<?php
if (!defined('ABSPATH')) exit; // Restrict direct access
	/*
		Plugin Name: WP Open Hour Widget
		Plugin URI: http://topfreelancers.esy.es/wp-open-hour-widget/
		Description: Open Hour Widget you can use into Sidebar
		Author: Prashant Rawal
		Version: 1.0
		License: GPLv2 or later
		License URI: http://www.gnu.org/licenses/gpl-2.0.html
		Author URI: http://skyseainfo.com
		Skype Id: rawalprashant26
	*/

	define('WP_OPENHOUR_PLUGIN_PATH', plugin_dir_url(__FILE__));

	function wp_openhour_include_js() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('wp-opwnhour-county', WP_OPENHOUR_PLUGIN_PATH . '/js/county.js');
		wp_enqueue_style('wp-openhour-cutom', WP_OPENHOUR_PLUGIN_PATH . '/css/styles.css');
	}
	add_action('wp_head', 'wp_openhour_include_js');

	function wp_openhour_activate() {
		global $wpdb;
		$wpdb->query('CREATE TABLE IF NOT EXISTS ' . $wpdb->prefix . 'openhour (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `open_text` VARCHAR(255), `close_text` VARCHAR(255), `timing` VARCHAR(255) );');
		$wpdb->insert(  $wpdb->prefix.'openhour', 
						array (
								'open_text' => 'We are open now and we are ready to answer your emails asap.', 
								'close_text' => 'We are closed now. We can not answer your emails now.' , 
								'timing' => json_encode(
															array(	
																	'monday' => array('open' => '8', 'close' => '19'),
																	'tuesday' => array('open' => '8', 'close' => '19'),
																	'wednesday' => array('open' => '8', 'close' => '19'),
																	'thursday' => array('open' => '8', 'close' => '19'),
																	'friday' => array('open' => '8', 'close' => '19'),
																	'saturday' => array('open' => '8', 'close' => '12'),
																	'sunday' => array('open' => '0', 'close' => '0')
																)
													   )
							  )
					 );
		add_option('wp_openhour_individual', false);
	}
	register_activation_hook(__FILE__, 'wp_openhour_activate');
	
	function wp_openhour_deactivate() {
		global $wpdb;
		$wpdb->query('DROP TABLE `'.$wpdb->prefix.'openhour`');
		delete_option('wp_openhour_individual');
	}
	register_deactivation_hook(__FILE__, 'wp_openhour_deactivate');
	
	function wp_openhour_menu(){
		global $wpdb;
		
		if ($_POST['wp_openhour_saved'] == 1)
		{
			global $wpdb;
			$wpdb->update(
							$wpdb->prefix.'openhour',
							array ('open_text' => $_POST['open_text'], 'close_text' => $_POST['close_text'] ,'timing' => json_encode($_POST['timing'])),
							array( 'id' => 1 )
						 );
			unset($_POST);
			$edited = true;
		}
		
		$data = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'openhour');
		$timing = json_decode($data->timing);
	?>
		<div class="wrap">
			<h2>WP Openhour</h2>
			<p>Here you can manage the wp openhour settings</p>
			<?php 
				if (isset($edited) && $edited)
					echo '<p>Record Edited</p>';
			?>
			<form method="post" name="wp_openhour_edit" action="">
				<table>
					<tr>
						<td><b>Open text: </b></td>
						<td><textarea name="open_text" cols="50" rows="3"><?php echo $data->open_text; ?></textarea></td>
					</tr>
					<tr>
						<td><b>Close text: </b></td>
						<td><textarea name="close_text" cols="50" rows="3"><?php echo $data->close_text; ?></textarea></td>
					</tr>
					<tr>
						<td><b>Timetable: </b></td>
						<td>
							<table>
								<?php 
									foreach($timing as $k => $v) { ?>
										<tr>
											<td><br/><b><?php echo ucfirst($k)?>:</b></td>
											<td><br/>
												Open: <input type="text" name="timing[<?php echo $k; ?>][open]]" value="<?php echo $v->open; ?>">
												Close: <input type="text" name="timing[<?php echo $k; ?>][close]]" value="<?php echo $v->close; ?>">
											</td>
										</tr>
								<?php } ?>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<input type="hidden" name="wp_openhour_saved" value="1" />
							<input type="submit" name="wp_openhour_submit" value="Update" />
						</td>
					</tr>
				</table>
			</form>
		</div>
	<?php 
	}
	
	function add_wp_openhour_in_option_tab() {
		add_submenu_page("options-general.php", "WP Openhour", "WP Openhour", 1, "wp-openhour", "wp_openhour_menu");
	}
	add_action('admin_menu', 'add_wp_openhour_in_option_tab' );
	
	require_once 'wp_openhour_widget.php';
?>