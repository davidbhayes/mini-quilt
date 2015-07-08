<?php 
class Mini_Quilt_Widget extends WP_Widget {
	function __construct() {
		$widget_ops = array( 
			'classname' => 'mq', 
			'description' => 'A unique and visually interesting way to highlight recent or random posts.' 
		);
		$control_ops = array( 'id_base' => 'mq-widget' );
		parent::__construct( 'mq-widget', 'Mini Quilt', $widget_ops, $control_ops ); //makes the widget
	}
	
	function widget( $args, $instance ) {

		extract( $args );
		$widget_title = $instance['widget_title'];
		$rows_to_display = max( $instance['rows_to_display'], 1 ); //using max to keep safe from neg/nonint values
		$columns_to_display = max( $instance['columns_to_display'], 1 );
		$patch_width = $instance['patch_width'];
		$patch_height = $instance['patch_height'];
		$randomize = $instance['randomize'];
		$show_post_titles = $instance['show_post_titles'];

		$posts_to_display = $rows_to_display * $columns_to_display;
		$new_patch_width = max( 0, $patch_width - 10 ); //these correct for the padding which was
		$new_patch_height = max( 0, $patch_height - 4 );// necessary to make the text look ok		
		if ( $new_patch_height < 6 and !$show_post_titles ) { 
			$new_patch_height = 6; 
		}
		$main_width = ( $new_patch_width + 14 ) * $columns_to_display;

		echo $before_widget;
		echo $before_title.$widget_title.$after_title; ?>
		  <ul class="miniquiltbox" style="width: <?php echo $main_width ?>px;">
			<?php
				$recentPosts = new WP_Query();
				if ( $randomize ) {
				  $query = 'showposts='.$posts_to_display.'&ignore_sticky_posts=1&orderby=rand';
				}
				else {
				  $query = 'showposts='.$posts_to_display.'&ignore_sticky_posts=1';
				}
				$recentPosts->query( $query );
			 while ( $recentPosts->have_posts() ) : 
			 	$recentPosts->the_post(); 
				$test_id = get_the_ID(); ?>
				<?php if ( $show_post_titles ) { ?>
					<li><a style="background: #<?php if (is_single($test_id)) {echo 'bbb';} else {echo mq_date_to_color(get_the_time('z'), get_the_time('Y'));}; ?>;   width: <?php echo $new_patch_width; ?>px; height: <?php if ($new_patch_height>0) {echo $new_patch_height.'px';} else {echo 'auto';} ?>;" href="<?php the_permalink() ?>" rel="bookmark" title="&#8220;<?php the_title(); ?>&#8221; from <?php the_time('d M Y'); ?>"><?php the_title(); ?></a></li>
				<?php }
				else { ?>
					<li><a style="background: #<?php if (is_single($test_id)) {echo 'bbb';} else {echo mq_date_to_color(get_the_time('z'), get_the_time('Y'));}; ?>;   width: <?php echo $new_patch_width; ?>px; height: <?php echo $new_patch_height; ?>px;" href="<?php the_permalink() ?>" rel="bookmark" title="&#8220;<?php the_title(); ?>&#8221; from <?php the_time('d M Y'); ?>"></a></li>
			<?php	}
			 endwhile; ?>
		  </ul>
		<?php echo $after_widget;
	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// absint and strip_tags ensure nothing unsavory gets through
		$instance['widget_title'] = strip_tags( $new_instance['widget_title'] );
		$instance['rows_to_display'] = absint( $new_instance['rows_to_display'] );
		$instance['columns_to_display'] = absint( $new_instance['columns_to_display'] );
		$instance['patch_width'] = absint( $new_instance['patch_width'] );
		$instance['patch_height'] = absint( $new_instance['patch_height'] );
		$instance['randomize'] = isset($new_instance['randomize']);
		$instance['show_post_titles'] = isset($new_instance['show_post_titles']);

		return $instance;
	}
	
	function form( $instance ) {
		$defaults = array(
			'widget_title'=>'The Mini Quilt',
			'rows_to_display'=>5,
			'columns_to_display'=>6,
			'patch_width'=>20,
			'patch_height'=>20,
			'randomize'=>false,
			'show_post_titles'=>false
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'widget_title' ); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'widget_title' ); ?>" name="<?php echo $this->get_field_name( 'widget_title' ); ?>" value="<?php echo $instance['widget_title']; ?>" type="text" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'rows_to_display' ); ?>">Quilt Dimensions (rows x cols):</label>
			<input id="<?php echo $this->get_field_id( 'rows_to_display' ); ?>" name="<?php echo $this->get_field_name( 'rows_to_display' ); ?>" value="<?php echo $instance['rows_to_display']; ?>" type="text" size="3" /> x <input id="<?php echo $this->get_field_id( 'columns_to_display' ); ?>" name="<?php echo $this->get_field_name( 'columns_to_display' ); ?>" value="<?php echo $instance['columns_to_display']; ?>" type="text" size="3" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'patch_width' ); ?>">Patch Dimensions (width x height in <strong>px</strong>):</label>
			<input id="<?php echo $this->get_field_id( 'patch_width' ); ?>" name="<?php echo $this->get_field_name( 'patch_width' ); ?>" value="<?php echo $instance['patch_width']; ?>" type="text" size="3" /> x <input id="<?php echo $this->get_field_id( 'patch_height' ); ?>" name="<?php echo $this->get_field_name( 'patch_height' ); ?>" value="<?php echo $instance['patch_height']; ?>" type="text" size="3" />
		</p>
		
		<p>
		<em>To create a Mini Bar: set columns to 1, height to 0, and check Show Post Titles.</em>
		</p>
		
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['randomize'], 1 ); ?> id="<?php echo $this->get_field_id( 'randomize' ); ?>" name="<?php echo $this->get_field_name( 'randomize' ) ; ?>" />
			<label for="<?php echo $this->get_field_id( 'randomize' ); ?>">Randomize</label>
		</p>
		
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_post_titles'], 1 ); ?> id="<?php echo $this->get_field_id( 'show_post_titles' ); ?>" name="<?php echo $this->get_field_name( 'show_post_titles' ) ; ?>" />
			<label for="<?php echo $this->get_field_id( 'show_post_titles' ); ?>">Show Post Titles</label>
		</p>
		
<?php
	}
	
}