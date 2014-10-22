<?php





function add_clients_script($hook) {

	$screen = get_current_screen();

	$post_types_array = array(
		"clients",
		"clients_page_slide-order"
	);

    // Check screen hook and current post type
    if ( in_array($screen->post_type, $post_types_array) ){
		wp_enqueue_script('jquery-ui-autocomplete');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script( 'cpt_as',  get_template_directory_uri() . '/inc/cpt_clients.js', array('jquery-ui-autocomplete', 'jquery-ui-sortable', 'jquery'), NULL, true );
    }
}
add_action( 'admin_enqueue_scripts', 'add_clients_script');


add_action( 'init', 'clients_create_post_type' );
function clients_create_post_type() {

	create_clients_taxonomies();

	register_post_type( 'clients',
		array(
			'labels' => array(
				'name' => __( 'Clients', 'kwik' ),
				'all_items' => __( 'Clients', 'kwik' ),
				'singular_name' => __( 'Client', 'kwik' ),
				'add_new' => __( 'Add Client', 'kwik' ),
				'add_new_item' => __( 'Add New Client', 'kwik' ),
				'edit_item' => __( 'Edit Client', 'kwik' ),
				'menu_name' => __( 'Clients', 'kwik' )
			),
			'menu_icon' => 'dashicons-awards',
			'menu_position' => 5,

		'supports' => array('title','editor','thumbnail', 'author', 'comments' ),
		'public' => true,
		'exclude_from_search' => false,
		'has_archive' => true,
		'taxonomies' => array('client_sector', 'client_levels', 'post_tag'),
		// 'register_meta_box_cb' => 'add_clients_metabox',
		'rewrite' => array('slug' => 'clients'),
		'query_var' => true
		)
	);

	add_image_size( 'client_logo', 240, 240, false );
	flush_rewrite_rules(false);

}


//add_action( 'init', 'create_clients_taxonomies');
function create_clients_taxonomies() {

	$client_sector_labels = array(
		'name' => _x( 'Sector', 'taxonomy general name' ),
		'singular_name' => _x( 'Sector', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Sectors' ),
		'all_items' => __( 'All Sectors' ),
		'edit_item' => __( 'Edit Sector' ),
		'update_item' => __( 'Update Sector' ),
		'add_new_item' => __( 'Add New Sector' ),
		'new_item_name' => __( 'New Sector' ),
	);

	register_taxonomy( 'client_sector', array( 'clients' ), array(
		'hierarchical' => true,
		'labels' => $client_sector_labels,
		'show_ui' => true,
		'query_var' => true,
		'show_admin_column' => true,
		'rewrite' => array('slug' => 'submission-type', 'hierarchical' => true)
	));


	$client_levels_labels = array(
		'name' => _x( 'Level', 'taxonomy general name' ),
		'singular_name' => _x( 'Level', 'taxonomy singular name' ),
		'search_items' =>  __( 'Search Levels' ),
		'all_items' => __( 'All Levels' ),
		'edit_item' => __( 'Edit Level' ),
		'update_item' => __( 'Update Level' ),
		'add_new_item' => __( 'Add New Level' ),
		'new_item_name' => __( 'New Level' ),
	);

	register_taxonomy( 'client_levels', array( 'clients' ), array(
		'hierarchical' => true,
		'labels' => $client_levels_labels,
		'show_ui' => true,
		'query_var' => true,
		'show_admin_column' => true,
		'rewrite' => array('slug' => 'submission-type', 'hierarchical' => true)
	));


}


function cpt_clients_activation() {
    clients_create_post_type();
    flush_rewrite_rules();
}
// for plugin
//register_activation_hook( __FILE__, 'cpt_clients_activation');
// for theme
add_action( 'after_switch_theme', 'cpt_clients_activation' );

function cpt_clients_deactivation() {
    flush_rewrite_rules();
}
//register_activation_hook( __FILE__, 'cpt_clients_deactivation');
add_action( 'switch_theme', 'cpt_clients_deactivation' );



function array_insert_at_position($array, $values, $pivot, $position = 'after'){
  $offset = 0;
  foreach($array as $key => $value){
    ++$offset;
    if ($key == $pivot){
      break;
    }
  }

  if ($position == 'before'){
    --$offset;
  }

  return
    array_slice($array, 0, $offset, TRUE)
    + $values
    + array_slice($array, $offset, NULL, TRUE)
  ;
}



// ADD NEW COLUMN
function add_clients_columns($columns) {

	$columns = array_insert_at_position($columns, array('featured_image' => __('Image')), 'cb');
	return $columns;

}
add_filter('manage_clients_posts_columns' , 'add_clients_columns');


// SHOW THE FEATURED IMAGE
function clients_columns_content($column_name, $post_ID) {
	switch ( $column_name ) {
	  case "featured_image":
			$thumb = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium' );
			$thumb = $thumb['0'];
			if ($thumb) {
				echo '<img width="75" src="' . $thumb . '" />';
			}
		break;
	}
}
add_action('manage_clients_posts_custom_column', 'clients_columns_content', 10, 2);




function clients_in_right_now() {

	$post_type = 'clients';

	if (!post_type_exists($post_type)) {
             return;
    }
	$num_posts = wp_count_posts( $post_type );
	echo '';
	$num = number_format_i18n( $num_posts->publish );
	$text = _n( 'User Submission', 'User Submissions', $num_posts->publish );
	if ( current_user_can( 'edit_posts' ) ) {
            $num = '<a href="edit.php?post_type='.$post_type.'">'.$num.'</a>';
            $text = '<a href="edit.php?post_type='.$post_type.'">'.$text.'</a>';
    }
	echo '<td class="first b b-clients">' . $num . '</td>';
	echo '<td class="t clients">' . $text . '</td>';
if ($num_posts->pending > 0) {
            $num = number_format_i18n( $num_posts->pending );
            $text = _n( 'User Submission Pending', 'User Submissions Pending', intval($num_posts->pending) );
            if ( current_user_can( 'edit_posts' ) ) {
                $num = '<a href="edit.php?post_status=pending&post_type='.$post_type.'">'.$num.'</a>';
                $text = '<a href="edit.php?post_status=pending&post_type='.$post_type.'">'.$text.'</a>';
            }
            echo '<td class="first b b-clients">' . $num . '</td>';
            echo '<td class="t clients">' . $text . '</td>';
        }

         echo '</tr>';
}
add_action('right_now_content_table_end','clients_in_right_now');




// Add the meta box
function add_clients_metabox()
{
    add_meta_box('clients_meta', 'Client Meta Data', 'clients_meta', 'clients', 'normal', 'default');

}




// The Belt edit page Meta box
function clients_meta()
{
    global $post;

	$post_link = get_post_meta($post->ID, '_post_link', true);
	$user_info = get_post_meta($post->ID, '_user_info', false);
	$user_info = (is_array($user_info) && !empty($user_info) ? $user_info[0] : '');


	$clients_meta = '';
    // Noncename for security check on data origin
    $clients_meta .= '<input type="hidden" name="clients_meta_noncename" id="clients_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
	$clients_meta .= '<div class="meta_wrap">';
	$clients_meta .= '<ul>';
		//$clients_meta .= '<li><strong>'.__('Belt Link','kwik').':</strong></li>';
		$clients_meta .= '<li><label>'.__('Post Link','kwik').'</label><input type="text" name="_post_link_title" id="post_link_title"" value="'.($post_link != "" ? get_the_title($post_link) : "").'" /><input type="hidden" id="post_link_id" name="_post_link" value="'.$post_link.'" /><label>&nbsp;</label><small>Type the name of the linked content and select from list</small></li>';
	$clients_meta .= '</ul>';
	$clients_meta .= '</div>';

	$clients_meta .= '<div class="meta_wrap user_info">';
	$clients_meta .= '<h4>'.__('Customer Info','kwik').':</h4>';
	$clients_meta .= (isset($user_info[1]) ? get_avatar( $user_info[1], 200 ) : '');
	$clients_meta .= '<ul>';

		$clients_meta .= '<li><label>'.__('Name','kwik').'</label><input type="text" name="_user_info[]" value="'.(isset($user_info[0]) ? $user_info[0] : '').'" /></li>';
		$clients_meta .= '<li><label>'.(isset($user_info[1]) ? '<a href="mailto:'.$user_info[1].'?subject=Your%20submission%20has%20been%20approved!&body=Check%20out%20your%20Action%20Shot%20on%20TopRopeBelts.com%20here: '.get_permalink($post->ID).'" title="'.(isset($user_info[0]) ? 'Send email to '.$user_info[0] : '').'">'.__('Email','kwik').'</a>' : __('Email','kwik')).'</label><input type="text" name="_user_info[]" value="'.(isset($user_info[1]) ? $user_info[1] : '').'" /></li>';
		$clients_meta .= '<li><label>'.__('Phone','kwik').'</label><input type="text" name="_user_info[]" value="'.(isset($user_info[2]) ? $user_info[2] : '').'" /></li>';
		$clients_meta .= '<li><label>'.__('URL','kwik').'</label><input type="text" name="_user_info[]" value="'.(isset($user_info[3]) ? $user_info[3] : '').'" /></li>';
		$clients_meta .= '<li><label>'.__('Twitter','kwik').'</label><input type="text" name="_user_info[]" value="'.(isset($user_info[4]) ? $user_info[4] : '').'" /></li>';
		$clients_meta .= '<li><label>'.__('Publish User Info?','kwik').'</label><input type="checkbox" name="_user_info[]" '. checked( 1, $user_info[5], false ) . ' value="1" /></li>';
	$clients_meta .= '</ul>';
	$clients_meta .= '</div>';


	$clients_meta .= '<br class="clear"/>';

	echo  $clients_meta;

}


// Save the Metabox Data
function save_clients_meta($post_id, $post){


	if($post->post_status =='auto-draft') return;


	if($post->post_type!='clients') return $post->ID;
    // make sure there is no conflict with other post save function and verify the noncename
    if (!wp_verify_nonce($_POST['clients_meta_noncename'], plugin_basename(__FILE__))) {
        return $post->ID;
    }

	$_POST['_user_info'][3] = (preg_match("#https?://#", $_POST['_user_info'][3]) === 0 && !empty($_POST['_user_info'][3]) ? 'http://' . $_POST['_user_info'][3] : $_POST['_user_info'][3]);
	$_POST['_user_info'][4] = (preg_match("/\@[a-z0-9_]+/i", $_POST['_user_info'][4]) != 0 ? str_replace('@', '', $_POST['_user_info'][4]) : $_POST['_user_info'][4]);


    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID)) return $post->ID;

    $clients_meta = array(
		'_post_link' => $_POST['_post_link'],
		'_user_info' => $_POST['_user_info']
    );

    // Add values of $clients_meta as custom fields
    foreach ($clients_meta as $key => $value) {
        if( $post->post_type == 'revision' ) return;
        __update_post_meta( $post->ID, $key, $value );
    }

}
add_action('save_post', 'save_clients_meta', 1, 2);







add_action('admin_menu', 'register_clients_menu');
function register_clients_menu(){
    add_submenu_page('edit.php?post_type=clients', 'Order Clients', 'Order', 'edit_pages', 'clients-order', 'clients_order_page');
}

function clients_order_page(){
?>

	<div class="wrap">

		<h2>Sort Clients</h2>

		<p>Simply drag the client up or down and they will be saved in the order the appear here.</p>

	<?php

	$terms = get_terms("client_levels", 'orderby=id&hide_empty=1' );

        foreach ($terms as $term) {
		    $clients = new WP_Query(array(
		        'post_type' => 'clients',
		        'posts_per_page' => -1,
            'tax_query' => array(
              array(
                'taxonomy' => $term->taxonomy,
                'field' => 'id',
                'terms' => $term->term_id, // Where term_id of Term 1 is "1".
                'include_children' => false
              )
            ),
		        'order' => 'ASC',
		        'orderby' => 'menu_order'
		    ));
		    echo '<h1>'.$term->name.' Level</h1>';
		    if ($clients->have_posts()): ?>
				<table class="wp-list-table widefat fixed posts" id="sortable-table">
					<thead>
						<tr>
							<th class="column-order">Order</th>
							<th class="column-thumbnail">Thumbnail</th>
							<th class="column-title">Title</th>
						</tr>
					</thead>
					<tbody data-post-type="clients">
					<?php
		            while ($clients->have_posts()): $clients->the_post();
		            ?>

						<tr id="post-<?php the_ID(); ?>">
							<td class="column-order"><img src="<?php echo get_stylesheet_directory_uri() . '/images/icons/move.png'; ?>" title="" alt="Move Slide" width="30" height="30" class="" /></td>
							<td class="column-thumbnail"><?php the_post_thumbnail('client_logo'); ?></td>
							<td class="column-title">
		                        <strong><?php the_title();?></strong>
		                        <div class="excerpt"><?php the_excerpt(); ?></div>
		                    </td>
						</tr>
					<?php endwhile; ?>
					</tbody>
					<tfoot>
						<tr>
							<th class="column-order">Order</th>
							<th class="column-thumbnail">Thumbnail</th>
							<th class="column-title">Title</th>
						</tr>
					</tfoot>
				</table>

	<?php else: ?>
		<p>No clients found, why not <a href="post-new.php?post_type=clients">add one?</a></p>
	<?php endif; ?>

	<?php wp_reset_postdata(); // Don't forget to reset again!
	}?>

	<style>
		#sortable-table td { background: white; }
		#sortable-table .column-order { padding: 3px 10px; width: 50px; }
		#sortable-table .column-order img { cursor: move; }
		#sortable-table td.column-order { vertical-align: middle; text-align: center; }
		#sortable-table .column-thumbnail { width: 160px; }
		#sortable-table .column-thumbnail img{ width: 100%; height:auto; }
	</style>

	</div><!-- .wrap -->



<?php
}
add_action('wp_ajax_clients_update_post_order', 'clients_update_post_order');
function clients_update_post_order()
{
    global $wpdb;
    $post_type = $_POST['postType'];
    $order     = $_POST['order'];
    /**
     *    Expect: $sorted = array(
     *                menu_order => post-XX
     *            );
     */
    foreach ($order as $menu_order => $post_id) {
        $post_id    = intval(str_ireplace('post-', '', $post_id));
        $menu_order = intval($menu_order);
        wp_update_post(array(
            'ID' => $post_id,
            'menu_order' => $menu_order
        ));
    }
    die('1');
}





function memberTable(){ ?>
	<div class="member_table">
	<?php $terms = get_terms("client_levels", 'orderby=id&hide_empty=0' );
        foreach ($terms as $term) {
		    $clients = new WP_Query(array(
	        'post_type' => 'clients',
	        'posts_per_page' => -1,
	        $term->taxonomy => $term->slug,
	        'order' => 'ASC',
	        'orderby' => 'menu_order'
		    ));
		    echo '<h3>'.$term->name.' Level</h3>';

		    if ($clients->have_posts()): ?>
				<ul class="mem_level-<?php echo $term->slug; ?> clear">
					<?php while ($clients->have_posts()): $clients->the_post(); ?>
						<li><?php if($term->term_id != 27){ ?><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php }

            if(has_post_thumbnail()) {
              the_post_thumbnail('client_logo');
            } else{
              echo "<span>";
              the_title();
              echo "</span>";
            }
            ?><?php if($term->term_id != 27){ ?></a><?php } ?></li>
					<?php endwhile; ?>
				</ul>
			<?php else:
				echo '<p>'.$term->name.' membership level available</p>';
			endif; ?>
			<?php wp_reset_postdata(); // Don't forget to reset again!
		} ?>
	</div><?php
}  // memberTable()



function membershipTable(){

	$terms = get_terms("client_levels", 'orderby=id&hide_empty=0' ); ?>

	<table class="mem_table" cellpadding="5">
		<thead>
			<tr>
				<th class="column-mem_level"><?php _e('Membership Level','kwik') ?></th>
				<th class="column-fee"><?php _e('Annual Fee', 'kwik' ); ?></th>
				<th class="column-fte"><?php _e('FTEs', 'kwik' ); ?></th>
				<th class="column-ipc"><?php _e('IP Contribution', 'kwik' ); ?></th>
				<th class="column-tsc"><?php _e('Technical Steering Commitee', 'kwik' ); ?></th>
				<th class="column-position"><?php _e('Board/Voting <br/>Position','kwik') ?></th>
			</tr>
		</thead>
		<tbody data-post-type="clients">

<?php
    foreach ($terms as $term) {
		$t_id = $term->term_id;
		$term_meta = get_option( "taxonomy_$t_id" );?>
		<tr style="border-top:1px solid #cecece">
			<td><?php echo $term->name; ?></td>
			<td><?php echo esc_attr( $term_meta['fee'][0] ) ? esc_attr( $term_meta['fee'][0] ) : ''; ?></td>
			<td><?php echo esc_attr( $term_meta['fte'] ) ? esc_attr( $term_meta['fte'] ) : ''; ?></td>
			<td><?php echo esc_attr( $term_meta['ipc'] ) ? esc_attr( $term_meta['ipc'] ) : ''; ?></td>
			<td><?php echo esc_attr( $term_meta['tsc'] ) ? esc_attr( $term_meta['tsc'] ) : ''; ?></td>
			<td><?php echo esc_attr( $term_meta['position'] ) ? esc_attr( $term_meta['position'] ) : ''; ?></td>
		</tr><?php
	} ?>
		</tbody>
	</table>
<?php
}



// [membership_table foo="foo-value"]
function membership_table( $atts ) {
	extract( shortcode_atts( array(
		'foo' => 'something',
		'bar' => 'something else',
	), $atts ) );

	$memb_table = '<!-- BEGIN [membership_table] -->';
	$terms = get_terms("client_levels", 'orderby=id&hide_empty=0&exclude=27' );

	$memb_table .= '<table class="mem_table" cellpadding="5">
		<thead>
			<tr>';
				$memb_table .= '<th class="column-mem_level_img"></th>';
				$memb_table .= '<th class="column-mem_level">'.__('Membership Level','kwik').'</th>';
				$memb_table .= '<th class="column-fee">'.__('Annual Fee*', 'kwik' ).'</th>';
				$memb_table .= '<th class="column-fte">'.__('FTEs', 'kwik' ).'</th>';
				// $memb_table .= '<th class="column-ipc">'.__('IP Contribution', 'kwik' ).'</th>';
				$memb_table .= '<th class="column-tsc">'.__('Technical Steering Commitee', 'kwik' ).'</th>';
				$memb_table .= '<th class="column-position">'.__('Board/Voting <br/>Position','kwik').'</th>';
			$memb_table .= '</tr>
		</thead>
		<tbody data-post-type="client_levels">';

    foreach ($terms as $term) {
		$t_id = $term->term_id;
		$term_meta = get_option( "taxonomy_$t_id" );
		$img = '';

		if(function_exists('taxonomy_image_plugin_get_image_src'))	{
			$associations = taxonomy_image_plugin_get_associations();
			if ( isset( $associations[ $term->term_id ] ) ) {
				$attachment_id = (int) $associations[ $term->term_id ];
				$img = wp_get_attachment_image( $attachment_id, 'medium');
			}
		}

		$memb_table .= '<tr>';
		$memb_table .= '<td class="mem_level_img">'.$img.'</td>';
			$memb_table .= '<td class="mem_level_name">'.$term->name.'</td>';
      $memb_table .= '<td>'.(esc_attr( $term_meta['fee'][0] ) ? esc_attr( $term_meta['fee'][0] ) : '0');
      $memb_table .= (esc_attr( $term_meta['fee'][1] ) ? '<br><em>'.esc_attr( $term_meta['fee'][1] ).'</em>' : '');

      $memb_table .= '</td>';
			$memb_table .= '<td>'.(esc_attr( $term_meta['fte'] ) ? esc_attr( $term_meta['fte'] ) : '0').'</td>';
			// $memb_table .= '<td>'.(esc_attr( $term_meta['ipc'] ) ? esc_attr( $term_meta['ipc'] ) : '').'</td>';
			$memb_table .= '<td>'.(esc_attr( $term_meta['tsc'] ) ? esc_attr( $term_meta['tsc'] ) : '').'</td>';
			$memb_table .= '<td>'.(esc_attr( $term_meta['position'] ) ? esc_attr( $term_meta['position'] ) : '').'</td>';
		$memb_table .= '</tr>';
	}
		$memb_table .= '</tbody></table><em style="font-size: 12px;">*Fee in US Dollars</em>';
		$memb_table .= '<!-- END [membership_table] -->';

	return $memb_table;
}
add_shortcode( 'membership_table', 'membership_table' );

