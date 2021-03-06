<?php

// Add the Page meta box
function kt_add_page_metabox() {
    add_meta_box('kt_page_meta', 'Page Meta Data', 'kt_page_meta', 'page', 'normal', 'default');
}
add_action('add_meta_boxes', 'kt_add_page_metabox');

// TODO: to be completed
// Add the Page meta box
function kt_add_TR_metabox()
{

    $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
    $template_file = get_post_meta($post_id, '_wp_page_template', true);

    // check for a template type
    if ($template_file == 'templates/technical_resources.php') {
        // add_meta_box('kt_bod_meta', 'Board Members', 'kt_bod_meta', 'page', 'normal', 'default');
    }

}
// add_action( 'add_meta_boxes', 'kt_add_TR_metabox' );

function kt_add_post_metabox()
{

    $screens = array('post');
    foreach ($screens as $screen) {
        add_meta_box('kt_post_meta', 'Post Meta', 'kt_post_meta', 'post', 'side', 'default');
    }
}
add_action('add_meta_boxes', 'kt_add_post_metabox');

function kt_add_bod_metabox($post_type, $post )
{

    $post_id = isset($post_type, $post) ? $_GET['post'] : $_POST['post_ID'];
    $template_file = get_post_meta($post_id, '_wp_page_template', true);

    // check for a template type
    if ($template_file == 'templates/board-of-directors.php') {
        add_meta_box('kt_bod_meta', 'Board Members', 'kt_bod_meta', 'page', 'normal', 'default');
    }
}
// add_action('add_meta_boxes', 'kt_add_bod_metabox', 10, 2);

// The edit page Meta box
function by_the_numbers()
{
    global $post;

    $btns_good = get_post_meta($post->ID, 'btn_good', false);
    $btns_good = $btns_good[0];
    $btns_bad = get_post_meta($post->ID, 'btn_bad', false);
    $btns_bad = $btns_bad[0];

    $btn_meta = '';

    $btn_meta .= '<div class="clear">';

    // THE GOOD
    $btn_meta .= '<ul id="btn_good" class="btn_ul sortable clear" style="margin-right:4%">';
    $btn_meta .= '<li class="btn_ul_title ignore"><h2 style="color:#28a234">' . __('The Good', 'kwik') . '</h2></li>';
    if (!empty($btns_good)) {
        $i = 0;
        foreach ($btns_good as $btn_good) {

            $btn_meta .= '<li><span class="move_btn">&nbsp;</span><div class="btn_nums">';
            $btn_meta .= '<input type="text" value="' . $btn_good[0] . '" placeholder="' . __('Big Number', 'kwik') . '" name="btn_good[' . $i . '][]" />';
            $btn_meta .= '<input type="text" value="' . $btn_good[1] . '" placeholder="' . __('Small Text', 'kwik') . '" name="btn_good[' . $i . '][]" />';
            $btn_meta .= '</div>';
            $btn_meta .= '<textarea name="btn_good[' . $i . '][]" placeholder="' . __('Description', 'kwik') . '" >' . $btn_good[2] . '</textarea>';
            $btn_meta .= '<span class="remove_btn">×</span></li>';
            $i++;

        }
    } else {
        $btn_meta .= '<li><span class="move_btn">&nbsp;</span>
							<div class="btn_nums"><input type="text" value="" placeholder="' . __('Big Number', 'kwik') . '" name="btn_good[]" /><input type="text" value="" placeholder="' . __('Small Text', 'kwik') . '" name="btn_good[]" /></div>
							<textarea name="btn_good[]" placeholder="' . __('Description', 'kwik') . '" ></textarea><span class="remove_btn">×</span>
						</li>';

    }// is_array
    $btn_meta .= '<li class="ignore"><span class="add_btn">+</span></li>';
    $btn_meta .= '</ul>';
    // THE BAD
    $btn_meta .= '<ul id="btn_bad" class="btn_ul" >';
    $btn_meta .= '<li class="btn_ul_title ignore"><h2 style="color:#990000">' . __('The Bad', 'kwik') . '</h2></li>';
    if (!empty($btns_bad)) {
        $i = 0;
        foreach ($btns_bad as $btn_bad) {

            $btn_meta .= '<li><span class="move_btn">&nbsp;</span><div class="btn_nums">';
            $btn_meta .= '<input type="text" value="' . $btn_bad[0] . '" placeholder="' . __('Big Number', 'kwik') . '" name="btn_bad[' . $i . '][]" />';
            $btn_meta .= '<input type="text" value="' . $btn_bad[1] . '" placeholder="' . __('Small Text', 'kwik') . '" name="btn_bad[' . $i . '][]" />';
            $btn_meta .= '</div>';
            $btn_meta .= '<textarea name="btn_bad[' . $i . '][]" placeholder="' . __('Description', 'kwik') . '" >' . $btn_bad[2] . '</textarea>';
            $btn_meta .= '<span class="remove_btn">×</span></li>';

            $i++;

        }
    } else {
        $btn_meta .= '<li><span class="move_btn">&nbsp;</span>
				<div class="btn_nums"><input type="text" value="" placeholder="' . __('Big Number', 'kwik') . '" name="btn_bad[]" /><input type="text" value="" placeholder="' . __('Small Text', 'kwik') . '" name="btn_bad[]" /></div>
				<textarea name="btn_bad[]" placeholder="' . __('Description', 'kwik') . '" ></textarea><span class="remove_btn">×</span>
			</li>';

    }// is_array
    $btn_meta .= '<li class="ignore"><span class="add_btn">+</span></li>';
    $btn_meta .= '</ul>';

    $btn_meta .= '</div>';

    echo $btn_meta;

}

// The edit page Meta box
function kt_page_meta($post) {

    $page_meta_fields = array(
        'banner_img' => array(
            'type' => 'img',
            'title' => __('Banner Image: ', 'kwik'),
            'value' => null,
            'attrs' => array(
                'button-text'=> __("Set Banner Image", 'kwik'),
                'img_size' => 'header_img'
                )
        ),
        'banner_img_height' => array(
            'type' => 'spinner',
            'title' => __('Banner Height: ', 'kwik'),
            'value' => '200',
            'desc' => __('How tall should this banner be? defaults to 200px', 'kwik'),
            'attrs' => array('min'=>10, 'max' => 1000)
        ),
        'banner_text' => array(
            'type' => 'text',
            'title' => __('Banner Text: ', 'kwik'),
            'value' => '',
            'desc' => __('The title is shown by default but entering text here will override that', 'kwik')
        ),
        'scroll_effect' => array(
            'type' => 'select',
            'title' => __('Scroll Effect', 'kwik'),
            'value' => 'blur',
            'options' => array(
            	'blur' => 'Blur',
            	'darken' => 'Darken',
            	'none' => 'None'
            	),
            'desc' => __('Set the type of effect the image will have', 'kwik'),
            // 'attrs' => array('checked' => null, 'id' => 'scroll_effect')
        )
    );

    $meta = new KwikMeta();
    echo $meta->get_fields($post, 'page_meta_fields', $page_meta_fields);

}


function kt_post_meta()
{
    global $post;

    $source = get_post_meta($post->ID, '_source', true);
    $source_link = get_post_meta($post->ID, '_source_link', true);

    $post_meta = '';
    // Noncename for security check on data origin
    $post_meta .= '<input type="hidden" name="kt_post_meta_noncename" id="kt_post_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
    $post_meta .= '<div class="post_meta meta_wrap">';
    $post_meta .= '<label><strong>' . __('Source', 'kwik') . ':</strong></label>';
    $post_meta .= '<input type="text" name="post_source" class="kt_text" value="' . $source . '" />';
    $post_meta .= '<label><strong>' . __('Link', 'kwik') . ':</strong></label>';
    $post_meta .= '<input type="text" name="post_source_link" class="kt_text" value="' . $source_link . '" />';
    $post_meta .= '</div>';

    echo $post_meta;
}

// board of directors meta
function kt_bod_meta()
{
    global $post;

    $bod = get_post_meta($post->ID, '_board_members', false);

    $no_avatar = get_bloginfo('template_url') . '/inc/images/no_avatar.jpg';
    $bod = $bod ? $bod[0] : "";

    $bod_meta = '';
    // Noncename for security check on data origin
    $bod_meta .= '<input type="hidden" name="kt_bod_meta_noncename" id="kt_bod_meta_noncename" value="' . wp_create_nonce(plugin_basename(__FILE__)) . '" />';
    $bod_meta .= '<div class="post_meta meta_wrap">';
    $bod_meta .= '<ul id="bod" class="sortable">';
    $i = 0;
    if (!empty($bod)) {
        foreach ($bod as $b) {
            $mem_img = wp_get_attachment_image_src($b['img'], 'medium');
            $mem_img = $mem_img['0'];
            $img_src = $mem_img ? $mem_img : $no_avatar;

            $bod_meta .= '<li class="bod_mem clear">';
            $bod_meta .= '<span class="remove_btn">Remove</span>';
            $bod_meta .= '<img src="' . $img_src . '" class="img_prev" width="150">';
            $bod_meta .= '<input type="hidden" name="bod[' . $i . '][img]" class="img_id" value="' . $b['img'] . '">';
            $bod_meta .= '<input type="text" name="bod[' . $i . '][name]" placeholder="' . __('Director Name, Position', 'kwik') . '" class="kt_text" value="' . $b['name'] . '" /><br/>';
            $bod_meta .= '<input type="text" placeholder="' . __('Company', 'kwik') . '" class="kt_text kwik_ac-clients ignore" name="bod[' . $i . '][company_name]" value="' . $b['company_name'] . '" /><input type="hidden" class="kwik_ac_val" name="bod[' . $i . '][company]" value="' . $b['company'] . '"><br/>';
            $bod_meta .= '<textarea name="bod[' . $i . '][bio]" class="bod_bio" placeholder="' . __('Director Name, Position', 'kwik') . '">' . $b['bio'] . '</textarea>';
            $bod_meta .= '</li>';
            $i++;
        }
    } else {
        $bod_meta .= '<li class="bod_mem clear">';
        $bod_meta .= '<span class="remove_btn">Remove</span>';
        $bod_meta .= '<img src="' . $no_avatar . '" class="img_prev" width="150">';
        $bod_meta .= '<input type="hidden" name="bod[0][img]" class="img_id" value="">';
        $bod_meta .= '<input type="text" name="bod[0][name]" placeholder="' . __('Director Name, Position', 'kwik') . '" class="kt_text" value="" /><br/>';
        $bod_meta .= '<input type="text" placeholder="' . __('Company', 'kwik') . '" class="kt_text kwik_ac-clients ignore" name="bod[0][company_name]" value="" /><input type="hidden" class="kwik_acval" name="bod[0][company]" value=""><br/>';
        $bod_meta .= '<textarea name="bod[0][bio]" class="bod_bio" placeholder="' . __('Director Name, Position', 'kwik') . '"></textarea>';
        $bod_meta .= '</li>';
    }
    $bod_meta .= '<li class="ignore clear"><span class="add_btn">' . __('Add Member', 'kwik') . '</span></li>';
    $bod_meta .= '</ul>';
    $bod_meta .= '</div>';

    echo $bod_meta;
}

// Save the Metabox Data
function kt_save_page_meta($post_id, $post)
{

    if($post->post_status =='auto-draft' || $post->post_type !== 'page') return;

    $meta = new KwikMeta();
    $meta->save_meta($post, 'page_meta_fields');

}
add_action('save_post', 'kt_save_page_meta', 1, 2);



// Save the Metabox Data
function kt_save_post_meta($post_id, $post)
{

    if ($post->post_status == 'auto-draft') {
        return;
    }

    if ($post->post_type != 'post') {
        return $post->ID;
    }

    // make sure there is no conflict with other post save function and verify the noncename
    if ( ! isset($_POST['kt_post_meta_noncename']) || ! wp_verify_nonce($_POST['kt_post_meta_noncename'], plugin_basename(__FILE__))) {
        return $post->ID;
    }

    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID)) {
        return $post->ID;
    }

    $page_meta = array(
        '_source' => $_POST['post_source'],
        '_source_link' => $_POST['post_source_link'],
    );

    // Add values of $belt_meta as custom fields
    foreach ($page_meta as $key => $value) {
        if ($post->post_type == 'revision') {
            return;
        }

        KwikMeta::update_meta($post->ID, $key, $value);
    }

}
add_action('save_post', 'kt_save_post_meta', 1, 2);


// Add the meta box
function add_kt_left_menu() {
	$menus = get_terms('nav_menu');
	$available_menus = array( '' => '-- Select' );
	foreach ($menus as $menu) {
		$available_menus[$menu->slug] = $menu->name;
	}
    add_meta_box('kt_left_menu', 'Left Menu', 'get_kt_left_menu', 'page', 'side', 'default');
	$page_left_menu = array(
		'left_menu_links' => array(
			'type' => 'select',
			'value' => null,
			'title' => __('Custom Menu: ', 'kwik'),
			'options' => $available_menus
		),
	);

	set_transient('page_left_menu', $page_left_menu, WEEK_IN_SECONDS );
}
add_action('add_meta_boxes', 'add_kt_left_menu');


function get_kt_left_menu($post) {
	$meta = new KwikMeta();
	echo $meta->get_fields($post, 'page_left_menu');
}

// Save the Metabox Data
function save_kt_left_menu($post_id, $post)
{
	if( $post->post_status == 'auto-draft' || $post->post_type !== 'page' ) return;

	$meta = new KwikMeta();
	$meta->save_meta($post, 'page_left_menu');
}
add_action('save_post', 'save_kt_left_menu', 1, 2);

