<?php
/*
Plugin Name: Geo Data
Plugin URI: http://
Description: Declares a plugin that will add new type of the post with type geo_data
Version: 0.1
Author: Andrey Yeshchenko
Author URI: http://
License: GPLv2 
*/
add_action( 'init', 'create_geo_data' );

function create_geo_data() {
  register_post_type( 'geo_data',
        array(
            'labels' => array(
                'name' => 'Geo Data',
                'singular_name' => 'Geo Data',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Geo Data',
                'edit' => 'Edit',
                'edit_item' => 'Edit Geo Data',
                'new_item' => 'New Geo Data',
                'view' => 'View',
                'view_item' => 'View Geo Data',
                'search_items' => 'Search Geo Data',
                'not_found' => 'No Geo Data found',
                'not_found_in_trash' => 'No Geo Data found in Trash',
                'parent' => 'Parent Geo Data'
            ),
 
            'public' => true,
            'menu_position' => 15,
            'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),
            'taxonomies' => array( '' ),
            'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
            'has_archive' => true
        )
    );
}

function register_geo_data_styles () {
    // Registring stylesheet files
    wp_register_style ('geo_data_style', plugins_url ('/assets/css/geo_data.css', __FILE__), array (), '20210905', 'all');	
    wp_enqueue_style ('geo_data_style');
}
function register_geo_data_scripts () {
	// Registering scripts files
	wp_register_script ('geo_data_script', plugins_url ('/assets/js/geo_data.js', __FILE__));
	wp_enqueue_script ('geo_data_script');
	wp_register_script ('geo_data_google_api', 'http://maps.google.com/maps/api/js?libraries=drawing&callback=initMap', __FILE__);
	
	// function for add async attribute for Google Map API download
	function add_async_attribute( $tag, $handle ) {
    if ( 'geo_data_google_api' !== $handle ) {
        return $tag;
    }
     return str_replace( ' src', ' async="async" src', $tag );
}
add_filter( 'script_loader_tag', 'add_async_attribute', 10, 2 );
	
	wp_enqueue_script ('geo_data_google_api');
}

add_action ('admin_enqueue_scripts', 'register_geo_data_styles');
add_action ('admin_enqueue_scripts', 'register_geo_data_scripts');

add_action( 'admin_init', 'my_admin' );

function my_admin() {
    add_meta_box( 'geo_data_meta_box_1',
        'Personalizer',
        'display_geo_data_personalizer',
        'geo_data', 'normal', 'default'
    );
	
	add_meta_box( 'geo_data_meta_box_2',
        'Rule Editor',
        'display_geo_data_rule_editor',
        'geo_data', 'normal', 'default'
    );
	
	add_meta_box( 'geo_data_meta_box_3',
        'Expiration Date',
        'display_geo_data_expiration_date',
        'geo_data', 'normal', 'default'
    );
	
	add_meta_box( 'geo_data_meta_box_4',
        'Dealer Inspire Author',
        'display_geo_data_dealer_inspire_author',
        'geo_data', 'normal', 'default'
    );
	
	add_meta_box( 'geo_data_meta_box_5',
        'Geofencing Editor',
        'display_geo_data_geofencing_editor',
        'geo_data', 'normal', 'default'
    );
}
function get_geo_data_personalizer_tags ($geo_data) {
	// Tags are stored in DB, separated commas
		$tags = esc_html( get_post_meta( $geo_data->ID, 'tagsId', true ) );
		return $tags;
}
function display_geo_data_personalizer( $geo_data ) {
    // Retrieve Personalizer based on geo_data ID    
	$geo_data_personalizer_tag = get_geo_data_personalizer_tags( $geo_data );
	$data_personalizer_default_tags = [ "Day of the Week Test", "Geofence DiHowdol", "Test", "Viewed Accord",
        "Mobile", "Tablet", "Desktop", "UTM Campaign Name", "UTM Medium", "UTM Source", "Return Visitor", "Civic" ];		
	$geo_data_personalizer_ruleset = "";
	echo '<input type="hidden" id="tagsId" name="geo_data_tagsId" value="'.$geo_data_personalizer_tag.'">';
	echo <<<EOT
	<p>
		<strong>Tag&nbsp;<span style="color:red">*</span></strong>
		<br>
		<span style="color: gray; font-size: 0.95em;">
			A tag allows one to easily connect a set of rules.
		</span>
	</p>	
	
	<div class="container">
	<ul id="list"></ul>
	<p>
		<input type = "text" id="txt" list = "tags" size=50 placeholder="Type or choose and press Enter ...">
	</p>	
	<datalist id = "tags">
EOT;

	foreach ( $data_personalizer_default_tags as $options_value ) {
		echo '<option value = "'.$options_value.'">';
	}
  	echo <<<EOT
	</datalist>	
	</div>
	<hr>
	<p>
	<strong>Ruleset&nbsp;<span style="color:red">*</span></strong>
		<br>
		<span style="color: gray; font-size: 0.95em;">
			Drag and Drop from the Rule Editor.
		</span>
	</p>	
	<div class="container" ondragenter="return dragEnter(event)" ondrop="return dragDrop(event)" 
    ondragover="return dragOver(event)">
		<ul id="ruleset">
		<div ></div>
	</ul>
	</div>
EOT;
}

function display_geo_data_rule_editor( $geo_data ) {
    // Retrieve Rule Editor based on geo_data ID    
	$rules = [['Picture','Page'], ['Picture','Returning Visitors'], ['Picture','Viewed Vehicle Make'], 
	['Picture','Viewed Vehicle Model'], ['Picture','Viewed Vehicle Vin'], ['Picture','Searched Vehicle Make'], 
	['Picture','Searched Vehicle Model'], ['Picture','Viewed Page'], ['Picture','IP Address'], ['Picture','Day of the Week'], 
	['Picture','Time of Day'], ['Picture','Referrer URL'], ['Picture','Refered By Organic Search'], ['Picture','Refered by PPC'], 
	['Picture','Refered by Google'], ['Picture','Refered by Yahoo'], ['Picture','Refered by Bing'], ['Picture','Refered by AutoTrader'],
	['Picture','Refered by CarsDotCom'], ['Picture','Refered by Facebook']];
	echo '<section class="container"  ondragenter="return dragEnter(event)" 
    ondrop="return dragDrop(event)" ondragover="return dragOver(event)">';
	echo '<div id="rule_editor">';
		foreach ($rules as $key=>$rule) {
			echo <<<EOT
		<div class="rule-editor" id="box-$key" draggable="true" ondragstart="return dragStart(event)">
		<p> $rule[0]</p>
		<div style="color: white;">
		$rule[1]
		</div></div>
EOT;
		}
		echo <<<EOT
			</div>
	</section>
EOT;

}


function display_geo_data_expiration_date( $geo_data ) {
    // Retrieve Expiration Date based on geo_data ID
    $exp_date = esc_html( get_post_meta( $geo_data->ID, 'exp_date', true ) );
	$date_parse = explode ( '-' , $exp_date );
	$geo_data_exp_date_day = $date_parse[0];
	$geo_data_exp_date_month = $date_parse[1];
	$geo_data_exp_date_year = $date_parse[2];
	
	echo <<<EOT
    Post expires at the end of <input type="number" name="geo_data_exp_date_month" min="1" max="12" style="width: 50px;"  value="$geo_data_exp_date_month" />(Month)
				<input type="number" name="geo_data_exp_date_day" min="1" max="31" style="width: 50px;" value="$geo_data_exp_date_day" />(Day)
				<input type="number" name="geo_data_exp_date_year" style="width: 75px;" min="2021" max="2031" value="$geo_data_exp_date_year" />(Year)
	<p>	Leave blank for no expiration date.</p>
	
EOT;

}

function display_geo_data_dealer_inspire_author( $geo_data ) {
    // Retrieve Inspire Author based on geo_data ID    
	$geo_data_inspire_author = esc_html( get_post_meta( $geo_data->ID, 'inspire_author', true ) );
	echo <<<EOT
    This article was written by &nbsp;<input type="text" name="geo_data_inspire_author" size="20"  value="$geo_data_inspire_author" />	
EOT;

}

function display_geo_data_geofencing_editor( $geo_data ) {
    // Retrieve Geofencing data based on geo_data ID	
	echo <<<EOT
    <div id="panel">
            <div id="color-palette"></div>
            <div>
                <button id="delete-button" type="button">Delete Selected Shape</button>
            </div>
    </div>
    <div id="map"></div>
	
EOT;

}


add_action( 'save_post', 'add_geo_data_fields', 10, 2 );

function add_geo_data_fields( $geo_data_id, $geo_data ) {
	
	// Check user rights if he can edit records
	if ( !current_user_can( 'edit_post', $post_id ) )
	{
		return $post_id;
	}
	
    // Check post type for Geo Data
    if ( $geo_data->post_type == 'geo_data' ) {
        
		// Store data in post meta table if present in post data
        if ( isset( $_POST['geo_data_tagsId'] ) ) 
		{
            update_post_meta( $geo_data_id, 'tagsId', $_POST['geo_data_tagsId'] );
        }
				
        if ( 	( isset( $_POST['geo_data_exp_date_day'] ) && $_POST['geo_data_exp_date_day'] != '' ) &&
				( isset( $_POST['geo_data_exp_date_month'] ) && $_POST['geo_data_exp_date_month'] != '' ) &&
				( isset( $_POST['geo_data_exp_date_year'] ) && $_POST['geo_data_exp_date_year'] != '' ) )
		{
            update_post_meta( $geo_data_id, 'exp_date', $_POST['geo_data_exp_date_day'].'-'.$_POST['geo_data_exp_date_month'].'-'.$_POST['geo_data_exp_date_year'] );
        }
		// if all fields set, but contents '', than reset esxparation date
		if ( 	( isset( $_POST['geo_data_exp_date_day'] ) && $_POST['geo_data_exp_date_day'] == '' ) &&
				( isset( $_POST['geo_data_exp_date_month'] ) && $_POST['geo_data_exp_date_month'] == '' ) &&
				( isset( $_POST['geo_data_exp_date_year'] ) && $_POST['geo_data_exp_date_year'] == '' ) )
		{
            update_post_meta( $geo_data_id, 'exp_date', '' );
        }
		
		if ( isset( $_POST['geo_data_inspire_author'] ) && $_POST['geo_data_inspire_author'] != '' )
        {
            update_post_meta( $geo_data_id, 'inspire_author', $_POST['geo_data_inspire_author'] );
        }
		// delete Inspire Author, if user enter empty field
		if ( isset( $_POST['geo_data_inspire_author'] ) && $_POST['geo_data_inspire_author'] == '' )
        {
            update_post_meta( $geo_data_id, 'inspire_author', '' );
        }
    }
}

	
add_filter( 'template_include', 'include_template_function', 1 );

function include_template_function( $template_path ) {
    if ( get_post_type() == 'geo_data' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-geo-data.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-geo-data.php';
            }
        }
    }
    return $template_path;
}

?>