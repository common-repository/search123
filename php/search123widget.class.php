<?php
/**
 * @package search123
 * @author Tim Zylinski
 * @version 2.15
 */

class Search123Widget extends WP_Widget {
	/** constructor */
	function Search123Widget() {
		parent::WP_Widget(false, $name = 'Search123');
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		global $search123o;

		extract( $args );

		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget; 
	
		if ( $title )
			echo $before_title . $title . $after_title;
		
		echo $search123o->getAds($instance['count'], "", $instance['alignment']);

		echo $after_widget; 
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		$title = esc_attr($instance['title']);
		$count = esc_attr($instance['count']);
		$alignment = esc_attr($instance['alignment']);
		?>
<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
	name="<?php echo $this->get_field_name('title'); ?>" type="text"
	value="<?php echo $title; ?>" /></label></p>

<p><label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('number of ads', S123_TEXTDOMAIN); ?>:
<select name="<?php echo $this->get_field_name('count'); ?>">
<?php  for( $i = 2; $i <= 8; $i++ ) { ?>
	<option <?php if( $count == $i ) echo 'selected'; ?>><?php echo $i ?></option>
	<?php } ?>
</select></label></p>

<p><label for="<?php echo $this->get_field_id('alignment'); ?>"><?php _e('display', S123_TEXTDOMAIN); ?>:
<select name="<?php echo $this->get_field_name('alignment'); ?>">
	<option <?php if( $alignment == "vertikal" ) echo 'selected'; ?>
		value="vertikal"><?php _e('vertical', S123_TEXTDOMAIN); ?></option>
	<option <?php if( $alignment == "horizontal" ) echo 'selected'; ?>
		value="horizontal"><?php _e('horizontal', S123_TEXTDOMAIN); ?></option>
</select></label></p>
	<?php
	}
}

?>