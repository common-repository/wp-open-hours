<?php
class WpOpenhourWidget extends WP_Widget {
	
	public function WpOpenhourWidget() {
		$widget_ops = array('classname' => 'WpOpenhourWidget', 'description' => 'WP Open Hour Widget' );
		$this->WP_Widget('WpOpenHourWidget', 'Wp Open Hour', $widget_ops);
	}
	
	public function form($instance) {
	?>
		<p>
			<label for="<?php echo $this->get_field_id( 'layout' ); ?>"><?php _e( 'Select Layout:' ); ?></label>
			<?php $counter = attribute_escape($instance['layout']);?>
			<select id="<?php echo $this->get_field_id('layout'); ?>" name="<?php echo $this->get_field_name('layout'); ?>" >
				<option value="1" <?php if(1==$counter){ ?> selected <?php } ?>>Simple</option>
				<option value="2" <?php if(2==$counter){ ?> selected <?php } ?>>Business</option>
				<option value="3" <?php if(3==$counter){ ?> selected <?php } ?>>Cafe</option>
				<option value="4" <?php if(4==$counter){ ?> selected <?php } ?>>Cafe Teria</option>
				<option value="5" <?php if(5==$counter){ ?> selected <?php } ?>>Cofee Bar</option>
				<option value="6" <?php if(6==$counter){ ?> selected <?php } ?>>Coktail Bar</option>
				<option value="7" <?php if(7==$counter){ ?> selected <?php } ?>>Restaurant</option>
				<option value="8" <?php if(8==$counter){ ?> selected <?php } ?>>Restaurant signboard</option>
				<option value="9" <?php if(9==$counter){ ?> selected <?php } ?>>Shop</option>
				<option value="10" <?php if(10==$counter){ ?> selected <?php } ?>>Snack Bar</option>
				<option value="11" <?php if(11==$counter){ ?> selected <?php } ?>>Social</option>
				<option value="12" <?php if(12==$counter){ ?> selected <?php } ?>>Cafe Dark</option>
			</select>
	</p>
	
	<?php
	}
	
	public function widget($args, $instance) {
		extract($args, EXTR_SKIP);
		$title = $instance['title'] ? $instance['title'] : 'WP Openhour';
		$layout = $instance['layout'] ? $instance['layout'] : '1';
		
		global $wpdb;
		$data = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'openhour');
		$timing = json_decode($data->timing);
		
		date_default_timezone_set('Europe/Ljubljana');
		//date_default_timezone_set('Asia/Kolkata');
		
		$today = date('l');
		$today_name = strtolower($today);
		$t = $timing->$today_name;
		$current_hour = date('H');
		$current_min = date('i');
		/* echo "<pre>open: ";
		print_r($t->open);
		echo '<br/>close:';
		print_r($t->close);
		echo '<br/>current h:';
		print_r($current_hour);
		echo '<br/>current m:';
		print_r($current_min);
		echo '<br/>close in';
		print_r(($t->close - $current_hour - 1) . ':'.(60-$current_min));
		echo "</pre>"; */
		
		$is_open = false;
		$cl_min = 60 - $current_min;
		if ($cl_min < 10)
			$cl_min = '0' . $cl_min;
		if ($current_hour >= $t->open && $current_hour < $t->close)
		{
			$is_open = true;
			$time_to_open_close = abs($t->close - $current_hour - 1) . ' hr ' . $cl_min . ' mins';
		}
		else
			$time_to_open_close = abs($current_hour - $t->open - 1) . ' hr ' . $cl_min . '  mins';
		
		$template_arr = array( 1 => 'simple', 2 => 'business', 3 => 'cafe', 4 => 'cafeteria', 5 => 'cofee-bar', 6 => 'coktail-bar', 7 => 'restaurant', 8 => 'restaurant-signboard', 9 => 'shop', 10 => 'snack-bar', 11 => 'social', 12 => 'cafe_dark' );
		$show = (in_array($layout, array(8, 9, 10))) ? 'style="display: none"' : '';
	?>
		<div id="mod-fwopenhours" class="<?php echo ($is_open) ? ('mod-fwopenhours-open') : ('mod-fwopenhours-closed'); ?>">
			<div class="mod-fwoh-<?php echo $template_arr[$layout]; ?>">
				<div class="mod-fwoh-<?php echo $template_arr[$layout]; ?>-time-label">Current Time</div>
				<div class="mod-fwoh-<?php echo $template_arr[$layout]; ?>-time"><span id="mod-fwopenhours-date-time"></span></div>
				<div class="mod-fwoh-<?php echo $template_arr[$layout]; ?>-status">
					<span <?php echo $show; ?>>We are</span> 
					<span id="mod-fwopenhours-status"><?php echo ($is_open) ? 'OPEN' : 'CLOSE'; ?></span>
				</div>
				<div id="mod-fwopenhours-text" class="mod-fwoh-<?php echo $template_arr[$layout]; ?>-text">
					<?php echo ($is_open) ? $data->open_text : $data->close_text; ?>
				</div>
				<div class="mod-fwoh-<?php echo $template_arr[$layout]; ?>-opening">
					<span id="mod-fwopenhours-time">
						<?php echo ($is_open) ? 'Closing in <b id="dhour">'.$time_to_open_close.'</b>' : 'Opening in <b id="dhour">'.$time_to_open_close.'</b>'; ?>
					</span>
				</div>
			</div>
		</div>
		<script>
			jQuery(document).ready(function(){
				function getTime(){
					var d = new Date();
					var offset = 1;
					utc = d.getTime() + (d.getTimezoneOffset() * 60000);
					nd = new Date(utc + (3600000*offset));
					var todayh = nd.getHours();
					var todaymin = nd.getMinutes();
					if (todaymin < 10)
						todaymin = '0'+todaymin;
					jQuery('#mod-fwopenhours-date-time').html(todayh+":"+todaymin);

					<?php if ($is_open) { ?>					
						remaining_h = Math.abs(<?php echo $t->close; ?> - todayh - 1 );
						remaining_m = 60 - todaymin;
					<?php } else { ?>
						remaining_h = Math.abs(24 - Math.abs(todayh - <?php echo $t->open; ?>) );
						remaining_m = 60 - todaymin;
					<?php } ?>

					if (remaining_h == 0)
						location.href = '';
					
					jQuery('#dhour').html(remaining_h + ' hr ' + remaining_m + ' mins');
				}
				function time()
				{
					//jQuery('#mod-fwopenhours-date-time').load('<?php echo plugins_url(); ?>/wp_openhour/time.php');
					getTime();
					setTimeout(time,60000);
				}
				time();
			});
		</script>
	<?php
	}
	
	/*public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['layout'] = $new_instance['layout'];

		return $instance;
	}*/
}

function register_wp_openhour_widget(){
	register_widget( 'WpOpenhourWidget' );
}

add_action( 'widgets_init', 'register_wp_openhour_widget');

?>