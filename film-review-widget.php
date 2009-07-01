<?php
/*
Plugin Name: film_Widget_review
Plugin URI: http://www.film-review.it
Description: Cerca Film review Widget
Version: 0.01
Author: film review
Author http://www.film-review.it
*/

function widget_filmreview($args, $widget_args = 1) {
echo ' <script language="javascript" type="text/javascript"> numimage ="1";</script>
<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript"
src="http://www.film-review.it/commons/js/trovafilm.js"></SCRIPT>
       ';
}

function widget_filmreview_control($widget_args) {
	global $wp_registered_widgets;
	static $updated = false;

	if ( is_numeric($widget_args) )
		$widget_args = array( 'number' => $widget_args );
	$widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
	extract( $widget_args, EXTR_SKIP );

	$options = get_option('widget_filmreview');
	if ( !is_array($options) )
		$options = array();

	if ( !$updated && !empty($_POST['sidebar']) ) {
		$sidebar = (string) $_POST['sidebar'];

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset($sidebars_widgets[$sidebar]) )
			$this_sidebar =& $sidebars_widgets[$sidebar];
		else
			$this_sidebar = array();

		foreach ( $this_sidebar as $_widget_id ) {
			if ( 'widget_filmreview' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number']) ) {
				$widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
				if ( !in_array( "filmreview-$widget_number", $_POST['widget-id'] ) ) unset($options[$widget_number]);
			}
		}

		foreach ( (array) $_POST['widget-filmreview'] as $widget_number => $widget_text ) {
			$title = strip_tags(stripslashes($widget_text['title']));
			$category = strip_tags(stripslashes(implode(",", $widget_text['category'])));
			$reviewnum = strip_tags(stripslashes($widget_text['reviewnum']));
			$order = strip_tags(stripslashes($widget_text['order']));
			$options[$widget_number] = compact( 'title', 'category', 'reviewnum' ,'order');
		}

		update_option('widget_filmreview', $options);
		$updated = true;
	}

	if ( -1 == $number ) {
		$title = '';
		$category = '';
		$reviewnum = '';
		$order = 'id';
		$number = '%i%';
	} else {
		$title = attribute_escape($options[$number]['title']);
		$category = attribute_escape($options[$number]['category']);
		$reviewnum = attribute_escape($options[$number]['reviewnum']);
		$order = attribute_escape($options[$number]['order']);
	}
?>
		<dl>
    
			<dd><input type="hidden" id="filmreview-title-<?php echo $number; ?>" name="widget-filmreview[<?php echo $number; ?>][title]" value="<?php echo $title; ?>" /></dd>
           	<?php $categories = get_terms( 'review_category', array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 10, 'hierarchical' => false ) );
				   //$oldcategory= explode(',', $category ); 
				?>   
	 
      
 <input type="hidden" class="filminput" id="filmreview-reviewnum-<?php echo $number; ?>" name="widget-filmreview[<?php echo $number; ?>][reviewnum]" value="<?php echo $reviewnum; ?>"/>
<input type="hidden"  name="widget-filmreview[<?php echo $number; ?>][order]" value="id" /></dd>
  
		<input type="hidden" id="filmreview-submit-<?php echo $number; ?>" name="filmreview-submit-<?php echo $number; ?>" value="1" />
		<div id="wscolor" style="border: 1px solid rgb(193, 191, 192); background-color: rgb(240, 240, 240); margin-top: 10px; width: 183px;"><a href="http://www.film-review.it/"><img src="http://www.film-review.it/commons/images/film-review.jpg" border="0"></a><div style="padding-left: 7px;"><h3>Cerca Film</h3></div><form method="post" action="http://www.film-review.it/elencofilm"><table id="wscolor" style="padding: 4px; font-size: 13px; line-height: 20px;"><tbody><tr></tr><tr><td>Titolo</td><td><input style="border: 1px solid rgb(193, 191, 192);" size="8" name="titolo" type="text"></td></tr><tr><td>Regia</td><td><input style="border: 1px solid rgb(193, 191, 192);" size="8" name="regia" type="text"></td></tr><tr><td>Attore/attrice</td><td><input style="border: 1px solid rgb(193, 191, 192);" size="8" name="attore" type="text"></td><td></td><td></td></tr><tr><td valign="top">HomeVideo</td><td valign="top"><select style="border: 1px solid rgb(193, 191, 192); width: 80px;" name="homevideo"><option> </option><option>DVD</option><option value="Blue-Ray">Blu-ray</option><option value="DVD&amp;Blue-Ray">DVD e Blu-ray</option></select></td></tr><tr><td></td><td><div align="right"><input src="http://www.film-review.it/immagini/new/btsearch.gif" style="vertical-align: middle;" title="cerca" alt="cerca" border="0" type="image"></div></td></tr></tbody></table><input name="post" value="set" type="hidden"></form></div>
		
		
<?php
}

function widget_filmreview_register() {

	// Check for the required API functions
	if ( !function_exists('wp_register_sidebar_widget') || !function_exists('wp_register_widget_control') )
		return;

	if ( !$options = get_option('widget_filmreview') )
		$options = array();
	$widget_ops = array('classname' => 'widget_filmreview', 'description' => __('Cerca Film sul tuo Wordpress'));
	$control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'filmreview');
	$name = __('film review');

	$id = false;
	foreach ( array_keys($options) as $o ) {
		// Old widgets can have null values for some reason
		if ( !isset($options[$o]['title']) || !isset($options[$o]['category']) )
			continue;
		$id = "filmreview-$o"; // Never never never translate an id
		wp_register_sidebar_widget($id, $name, 'widget_filmreview', $widget_ops, array( 'number' => $o ));
		wp_register_widget_control($id, $name, 'widget_filmreview_control', $control_ops, array( 'number' => $o ));
	}
	
	// If there are none, we register the widget's existance with a generic template
	if ( !$id ) {
		wp_register_sidebar_widget( 'filmreview-1', $name, 'widget_filmreview', $widget_ops, array( 'number' => -1 ) );
		wp_register_widget_control( 'filmreview-1', $name, 'widget_filmreview_control', $control_ops, array( 'number' => -1 ) );
	}
	
}

add_action( 'widgets_init', 'widget_filmreview_register' );

?>