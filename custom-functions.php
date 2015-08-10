<?php

//Add zap button to Post Admin

add_action( 'post_submitbox_misc_actions', 'zlc_post_button' );

function zlc_post_button(){
		$url = 'http://dev.davidmnoll.com/zapchild/wp-admin/post.php?action=submit_zap&post-id='.get_the_ID().'&key=';
        $html  = '<div id="major-publishing-actions" style="overflow:hidden">';
        $html .= '<div id="publishing-action">';	
		$html .= '<a href="'.$url.'" accesskey="p" tabindex="5" value="ZAP" class="button-primary" id="zap-button" name="zap_id">ZAP</a>';
        $html .= '</div>';
        $html .= '</div>';
        echo $html;
		
}
/*add_action( 'update_post', 'zlc_admin_submit_zap' );
add_action( 'publish_post', 'zlc_admin_submit_zap' );
function zlc_admin_submit_zap() {
	global $post;
	$ID = $post->ID;	
	$submission = array(
		'action' => 'post_zap',
		'name' => $post->post_name,
		'title' => $post->post_title,
		'excerpt' => $post->post_excerpt,
		'burl' => get_site_url(),
		'purl' => get_permalink($ID)
	);

	return zlc_post_zap($submission);
	
}*/


//	HTTP post --params = post URL, excerpt    
//	Repeat HTTP post for each parent
function zlc_post_zap ($submission){
	
	$parent_blog = 'http://dev.davidmnoll.com/zapparent/wp-admin/admin-post.php';
	$key = '999999999';	
	
	$response = wp_remote_post( $parent_blog, array(
		'method' => 'POST',
		'timeout' => 45,
		'redirection' => 5,
		'httpversion' => '1.0',
		'blocking' => true,
//		'headers' => array(),
		'body' => $submission,
		'cookies' => array()
		)
	);

	if ( is_wp_error( $response ) ) {
		$error_message = $response->get_error_message();
		return "Something went wrong: $error_message";
	} else {
		return 'Response: '.$response;
	}
}



//Add options menu
add_action('admin_menu', 'zlc_plugin_menu');

function zlc_plugin_menu() {
	add_options_page( 'Zaplog Options', 'Zaplog', 'administrator', 'zaplog-options', 'zlc_options_page');
}

 function admin_init() {
    register_setting('zlc_options', 'zlc_parent');
	register_setting('zlc_options', 'zlc_key');
}


//Options Page
function zlc_options_page(){ ?>
   <div class="wrap">
<h2>Zaplog Options</h2>
   <form method="post" action="options-general.php?page=zaplog-options"> 
   
    <?php settings_fields( 'zlc_options' ); ?>
    <?php do_settings_sections( 'zlc_options' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Parent Blog URL</th>
        <td><input type="text" name="zlc_parent" value="<?php echo esc_attr( get_option('zlc_parent') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">Key</th>
        <td><input type="text" name="zlc_key" value="<?php echo esc_attr( get_option('zlc_key') ); ?>" /></td>
        </tr>
        
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php 
   
   
}	

