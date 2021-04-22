<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://solucionesweb.work
 * @since             1.0.0
 * @package           tagsforall
 *
 * @tagsforall
 * Plugin Name:       Tags For All  - All about SEO for your website
 * Plugin URI:        http://solucionesweb.work
 * Description:       Enables all tags for your SEO in page and integrates Social networks
 * Version:           1.0.0
 * Author:            Soluciones Web
 * Author URI:        http://solucionesweb.work
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       tagsforall
 * Domain Path:       /languages
 */

	// If this file is called directly, abort.
	if ( ! defined('WPINC') ) {
		die;
	}

	/**
	 * Currently plugin version.
	 * Start at version 1.0.0 and use SemVer - https://semver.org
	 * Rename this for your plugin and update it as you release new versions.
	 */
	define('TAGSFORALL_VERSION', '1.0.1');

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-tagsforall-activator.php
	 */
	function activate_tagsforall() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-tagsforall-activator.php';
		tagsforall_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-tagsforall-deactivator.php
	 */
	function deactivate_tagsforall() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-tagsforall-deactivator.php';
		tagsforall_Deactivator::deactivate();
	}

	register_activation_hook( __FILE__, 'activate_tagsforall');
	register_deactivation_hook( __FILE__, 'deactivate_tagsforall');

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-tagsforall.php';
	function add_meta_seo() {
		global $post;
		$mypost_id = intval($post->ID);
		$post_getdata=get_post($post->ID);
		$descripciongeneral=get_option('webdesc');
		$datatitle=get_post_meta($post_id, "titletagsforall");
		$datadesc=get_post_meta($post_id, "descriptiontagsforall");
		$datatype=get_post_meta($post_id, "typetagsforall");
		$datawebs=get_post_meta($post_id, "websitetagsforall");
		$dataimag=get_post_meta($post_id, "imagetagsforall");
		$datakeywords=get_post_meta($post_id, "keywordstagsforall");

	    ?>
	    	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	    	<?php
	    		if($descripciongeneral==''){
	    			$descriptfinal=get_option('descripttfs');
	    		}
				else{
					$descriptfinal=get_option('webdesc');
				}
	    	?>
	    	<meta name="description" content="<?php echo($descriptfinal); ?>">
			<meta name="keywords" content="<?php echo esc_attr( get_option('keywordstfs') ); ?>">
	    	<meta name="language" content="<?php echo(get_option('weblang')); ?>">
			<meta name="author" content="<?php echo(get_option('webauthor')); ?>">
			<meta name="subject" content="<?php echo(get_option('websubject')); ?>">
			<meta name="classification" content="<?php echo(get_option('webclass')); ?>">
			<meta http-equiv="Expires" content="<?php echo(get_option('webexpires')); ?>">
			<meta name="copyright" content="<?php echo(get_option('webcopy')); ?>">
			<meta name="designer" content="<?php echo(get_option('webdesign')); ?>">
			<meta name="publisher" content="<?php echo(get_option('webpublish')); ?>">
			<meta name="revisit-after" content="<?php echo(get_option('webrevisit')); ?>">
			<meta name="distribution" content="<?php echo(get_option('webdist')); ?>">
			<meta name="robots" content="<?php echo(get_option('webrobots')); ?>">
			<meta name="city" content="<?php echo(get_option('webcity')); ?>">
			<meta name="country" content="<?php echo(get_option('webcountry')); ?>">
			<meta name="geography" content="<?php echo(get_option('webcity').",".get_option('webcountry')); ?>">
	    <?php
	    if(!empty($mypost_id)){
	    	if(is_numeric($mypost_id)){
	    		if($mypost_id>0){
						if($datatitle!=''){
							?>
								<meta property="og:title" content="<?php echo($datatitle); ?>">
								<meta property="twitter:title" content="<?php echo($datatitle); ?>">
						    	<meta name="title" content="<?php echo($datatitle); ?>">
							<?php
						}
						else{
							?>
								<meta property="og:title" content="<?php echo($post_getdata->post_title); ?>">
								<meta property="twitter:title" content="<?php echo($post_getdata->post_title); ?>">
						    	<meta name="title" content="<?php echo($post_getdata->post_title); ?>">
							<?php
						}
						if($datadesc!=''){
							?>
								<meta property="og:description" content="<?php echo($datadesc); ?>">
								<meta property="twitter:description" content="<?php echo($datadesc); ?>">
						    	<meta name="description" content="<?php echo($datadesc); ?>">
							<?php
						}
						else{
							?>
								<meta property="og:description" content="<?php echo($post_getdata->post_title); ?>">
								<meta property="twitter:description" content="<?php echo($post_getdata->post_title); ?>">
						    	<meta name="description" content="<?php echo($post_getdata->post_title); ?>">
							<?php
						}
						if($datakeywords!=''){
							?>
								<meta property="og:keywords" content="<?php echo($datakeywords); ?>">
								<meta property="twitter:keywords" content="<?php echo($datakeywords); ?>">
						    	<meta name="keywords" content="<?php echo($datakeywords); ?>">
							<?php
						}
						if($post_getdata->post_excerpt!=''){
							?>
								<meta property="og:description" content="<?php echo($post_getdata->post_excerpt); ?>">
								<meta property="twitter:description" content="<?php echo($post_getdata->post_excerpt); ?>">
							<?php
						}
						else{
							?>
								<meta property="og:description" content="<?php echo(strip_tags($post_getdata->post_content)); ?>">
								<meta property="twitter:description" content="<?php echo(strip_tags($post_getdata->post_content)); ?>">
							<?php
						}
						if($post_getdata->post_type!=''){
							?>
								<meta property="og:type" content="<?php echo($post_getdata->post_type); ?>">
							<?php
						}
						else{
							?>
								<meta property="og:type" content="<?php echo($post_getdata->post_type); ?>">
							<?php
						}
						if($mypost_id!=''>0){
							?>
								<meta property="og:website" content="<?php echo(get_post_permalink($mypost_id)); ?>">
								<meta property="og:url" content="<?php echo(get_post_permalink($mypost_id)); ?>">
								<meta property="twitter:site" content="<?php echo(get_post_permalink($mypost_id)); ?>">
							<?php
						}
						if($mypost_id!=''){
							?>
								<meta property="og:image" content="<?php echo(get_the_post_thumbnail_url($mypost_id)); ?>">
								<meta property="twitter:image" content="<?php echo(get_the_post_thumbnail_url($mypost_id)); ?>">
							<?php
						}
	    		}
	    	}
	    }
	}
	function add_meta_footer() {
	    ?><?php
	}
	function my_menu_tfa(){
		add_menu_page('Tags For All', 'Tags For All', 'manage_options', 'tagsforall_menu_plugin_slug', 'output_menu_tfa','dashicons-admin-site');
	}
	function global_notice_meta_box_tfa() {
	    add_meta_box(
	        'tagsforall-info-post',
	        'Tags4All Meta Info',
	        'global_notice_meta_box_callback',
	        $screen
	    );
	}

	function global_notice_meta_box_callback($post){
		wp_nonce_field('tagsforall-info-post', 'tagsforall-info-post');
		?>
			<label>Title</label><br />
            <input name="title-meta-box-text" id="title-meta-box-text" type="text" value="<?php echo(get_post_meta( $post->ID, 'titletagsforall', true )); ?>"><br />
            <label>Description</label><br />
            <input name="description-meta-box-text" id="description-meta-box-text" type="text" value="<?php echo(get_post_meta( $post->ID, 'descriptiontagsforall', true )); ?>"><br />
            <label>Keywords</label><br />
            <input name="keywords-meta-box-text" id="keywords-meta-box-text" type="text" value="<?php echo(get_post_meta( $post->ID, 'keywordstagsforall', true )); ?>"><br />
            <label>Type</label><br />
            <input name="type-meta-box-text" id="type-meta-box-text" type="text" value="<?php echo(get_post_meta( $post->ID, 'typetagsforall', true )); ?>"><br />
            <label>Image (URL)</label><br />
            <input name="image-meta-box-text" id="image-meta-box-text" type="text" value="<?php echo(get_post_meta( $post->ID, 'imagetagsforall', true )); ?>" placeholder="https://placeholder.com/image.jpg"><br />
            <label>Website</label><br />
            <input name="website-meta-box-text" id="website-meta-box-text" type="text" value="<?php echo(get_permalink($post->ID)); ?>" placeholder="https://example.com"><br />
        <?php
	}
	function save_articulos_meta_tfa( $post_id, $post, $update){
		$datatitle=get_post_meta($post_id, "titletagsforall");
		$datadesc=get_post_meta($post_id, "descriptiontagsforall");
		$datatype=get_post_meta($post_id, "typetagsforall");
		$datawebs=get_post_meta($post_id, "websitetagsforall");
		$dataimag=get_post_meta($post_id, "imagetagsforall");
		$datakeywords=get_post_meta($post_id, "keywordstagsforall");
		if(!empty($datatitle)){
			update_post_meta($post_id, "titletagsforall",$_POST["title-meta-box-text"]);
		}
		else{
			add_post_meta($post_id, "titletagsforall",$_POST["title-meta-box-text"], false);
		}
		if(!empty($datadesc)){
			update_post_meta($post_id, "descriptiontagsforall",$_POST["description-meta-box-text"]);
		}
		else{
			add_post_meta($post_id, "descriptiontagsforall",$_POST["description-meta-box-text"], false);
		}
		if(!empty($datatype)){
			update_post_meta($post_id, "typetagsforall",$_POST["type-meta-box-text"]);
		}
		else{
			add_post_meta($post_id, "typetagsforall",$_POST["type-meta-box-text"], false);
		}
		if(!empty($datawebs)){
			update_post_meta($post_id, "websitetagsforall",$_POST["website-meta-box-text"]);
		}
		else{
			add_post_meta($post_id, "websitetagsforall",$_POST["website-meta-box-text"], false);
		}
		if(!empty($dataimag)){
			update_post_meta($post_id, "imagetagsforall",$_POST["image-meta-box-text"]);
		}
		else{
			add_post_meta($post_id, "imagetagsforall",$_POST["image-meta-box-text"], false);
		}
		if(!empty($datakeywords)){
			update_post_meta($post_id, "keywordstagsforall",$_POST["keywords-meta-box-text"]);
		}
		else{
			add_post_meta($post_id, "keywordstagsforall",$_POST["keywords-meta-box-text"], false);
		}
	}

	function my_sub_menu_tfa(){
		add_submenu_page('tagsforall_menu_plugin_slug', 'General', 'Test de submenú', 'manage_options', 'tagsforall_submenu_plugin_slug', 'output_submenu_tfa');
	}
	function output_menu_tfa() {
		if ( !current_user_can('manage_options') )  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	?>
	<div class="wrap">
		<h1>Tags For All - All about SEO for your website</h1>
		<h2>Options</h2>
		<p></p>
		  <?php
		  	//Seleccionar tabs
			if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			} // end if
			$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
		  ?>
		<h2 class="nav-tab-wrapper">
			<a href="?page=<?php echo($_GET['page']); ?>&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
			<a href="?page=<?php echo($_GET['page']); ?>&tab=keywords" class="nav-tab <?php echo $active_tab == 'keywords' ? 'nav-tab-active' : ''; ?>">Keywords</a>
			<a href="?page=<?php echo($_GET['page']); ?>&tab=google" class="nav-tab <?php echo $active_tab == 'google' ? 'nav-tab-active' : ''; ?>">Google Stuff</a>
			<a href="?page=<?php echo($_GET['page']); ?>&tab=facebook" class="nav-tab <?php echo $active_tab == 'facebook' ? 'nav-tab-active' : ''; ?>">Facebook Integration</a>
			<a href="?page=<?php echo($_GET['page']); ?>&tab=instagram" class="nav-tab <?php echo $active_tab == 'instagram' ? 'nav-tab-active' : ''; ?>">Instagram Things</a>
			<a href="?page=<?php echo($_GET['page']); ?>&tab=twitter" class="nav-tab <?php echo $active_tab == 'twitter' ? 'nav-tab-active' : ''; ?>">Twitter API</a>
			<a href="?page=<?php echo($_GET['page']); ?>&tab=youtube" class="nav-tab <?php echo $active_tab == 'youtube' ? 'nav-tab-active' : ''; ?>">Youtube API</a>
			<a href="?page=<?php echo($_GET['page']); ?>&tab=hubspot" class="nav-tab <?php echo $active_tab == 'youtube' ? 'nav-tab-active' : ''; ?>">Hubspot API</a>
        </h2>
		<div class="adentro_div">
			<form method="post" action="options.php">
				<?php
					settings_errors();
					// This prints out all hidden setting fields
				  	switch($active_tab){
						case "general":
							?>
							  	<label>Website Title</label><br>
							  	<input name="webtitle" id="webtitle" type="text" value="<?php echo esc_attr( get_option('webtitle') ); ?>"/><br>
							  	<label>Website Description</label><br>
							  	<textarea name="webdesc" id="webdesc"><?php echo esc_attr( get_option('webdesc') ); ?></textarea><br>
							  	<label>Author</label><br>
							  	<input name="webauthor" id="webauthor" type="text" value="<?php echo esc_attr( get_option('webauthor') ); ?>" /><br>
							  	<label>Subject</label><br>
							  	<input name="websubject" id="websubject" type="text" value="<?php echo esc_attr( get_option('websubject') ); ?>" /><br>
							  	<label>Classification</label><br>
							  	<input name="webclass" id="webclass" type="text" value="<?php echo esc_attr( get_option('webclass') ); ?>" placeholder="Category: Subcategory, Category: subcategory" /><br>
							  	<label>Language</label><br>
							  	<input name="weblang" id="weblang" type="text" value="<?php echo esc_attr( get_option('weblang') ); ?>" placeholder="English, Spanish" /><br>
							  	<label>Copyright</label><br>
							  	<input name="webcopy" id="webcopy" type="text" value="<?php echo esc_attr( get_option('webcopy') ); ?>" /><br>
							  	<label>Designer</label><br>
							  	<input name="webdesign" id="webdesign" type="text" value="<?php echo esc_attr( get_option('webdesign') ); ?>" /><br>
							  	<label>Publisher</label><br>
							  	<input name="webpublish" id="webpublish" type="text" value="<?php echo esc_attr( get_option('webpublish') ); ?>" /><br>
							  	<label>Revisit After</label><br>
							  	<input name="webrevisit" id="webrevisit" type="text" value="<?php echo esc_attr( get_option('webrevisit') ); ?>" placeholder="7 days" /><br>
							  	<label>Robots</label><br>
							  	<input name="webrobots" id="webrobots" type="text" value="<?php echo esc_attr( get_option('webrobots') ); ?>" placeholder="INDEX,FOLLOW" /><br>
							  	<label>Distribution</label><br>
							  	<input name="webdist" id="webdist" type="text" value="<?php echo esc_attr( get_option('webdist') ); ?>" placeholder="global" /><br>
							  	<label>City</label><br>
							  	<input name="webcity" id="webcity" type="text" value="<?php echo esc_attr( get_option('webcity') ); ?>" placeholder="Miami" /><br>
							  	<label>Country</label><br>
							  	<input name="webcountry" id="webcountry" type="text" value="<?php echo esc_attr(get_option('webcountry')); ?>" placeholder="USA" /><br>
							  	<label>Expires</label><br>
							  	<input name="webexpires" id="webexpires" type="text" value="<?php echo esc_attr(get_option('webexpires')); ?>" placeholder="7 days" /><br>
							<?php
							settings_fields('general-tagsforall-group');
				  			do_settings_sections('general-tagsforall-group');
							break;
						case "keywords":
							?>
							  	<label>Keywords</label><br>
							  	<textarea name="keywordstfs" id="keywordstfs"><?php echo esc_attr( get_option('keywordstfs') ); ?></textarea><br>
							  	<label>Description</label><br>
							  	<label>The keywords will be used on every page no tags are defined. You have used 0 of 1000 description chars. The background color will help you to assess the optimized char count.</label><br>
							  	<textarea name="descripttfs" id="descripttfs"><?php echo esc_attr( get_option('descripttfs') ); ?></textarea><br>
							<?php
							settings_fields('keyw-tagsforall-group');
						  	do_settings_sections('keyw-tagsforall-group');
							break;
						case "google":
							?>
							  	<label>Google Analytics Traking ID</label><br>
							  	<input name="gatracking" id="gatracking" type="text" value="<?php echo esc_attr( get_option('gatracking') ); ?>" /><br>
							  	<label>Google Console ID (Google Webmaster Tools)</label><br>
							  	<input name="webmastert" id="webmastert" type="text" value="<?php echo esc_attr( get_option('webmastert') ); ?>" /><br>
							<?php
							settings_fields('google-tagsforall-group');
						  	do_settings_sections('google-tagsforall-group');
							break;
						case "facebook":
							?>
							  	<label>FB APP ID</label><br>
							  	<input name="fbappid" id="fbappid" type="text" value="<?php echo esc_attr( get_option('fbappid') ); ?>" placeholder="02347802834902839049234"/><br>
							  	<label>Secret key</label><br>
							  	<input name="fbsecretkeyapi" id="fbsecretkeyapi" type="text" value="<?php echo esc_attr( get_option('fbsecretkeyapi') ); ?>" placeholder="02347802834902839049234" /><br>
							<?php
							settings_fields('facebook-tagsforall-group');
						  	do_settings_sections('facebook-tagsforall-group');
							break;
						case "instagram":
							?>
							  	<label>Instagram API KEY</label><br>
							  	<input name="inappid" id="inappid" type="text" value="<?php echo esc_attr( get_option('inappid') ); ?>" placeholder="02347802834902839049234"/><br>
							  	<label>Secret key</label><br>
							  	<input name="insecretkeyapi" id="insecretkeyapi" type="text" value="<?php echo esc_attr( get_option('insecretkeyapi') ); ?>" placeholder="02347802834902839049234" /><br>
							<?php
							settings_fields('instagram-tagsforall-group');
						  	do_settings_sections('instagram-tagsforall-group');
							break;
						case "twitter":
							?>
							  	<label>Twitter API KEY</label><br>
							  	<input name="twappid" id="twappid" type="text" value="<?php echo esc_attr( get_option('twappid') ); ?>" placeholder="02347802834902839049234"/><br>
							  	<label>Secret key</label><br>
							  	<input name="twsecretkeyapi" id="twsecretkeyapi" type="text" value="<?php echo esc_attr( get_option('twsecretkeyapi') ); ?>" placeholder="02347802834902839049234" /><br>
							<?php
							settings_fields('twitter-tagsforall-group');
						  	do_settings_sections('twitter-tagsforall-group');
							break;
						case "youtube":
							?>
							  	<label>Youtube API KEY</label><br>
							  	<input name="ytappid" id="ytappid" type="text" value="<?php echo esc_attr( get_option('ytappid') ); ?>" placeholder="02347802834902839049234"/><br>
							  	<label>Secret key</label><br>
							  	<input name="ytsecretkeyapi" id="ytsecretkeyapi" type="text" value="<?php echo esc_attr( get_option('ytsecretkeyapi') ); ?>" placeholder="02347802834902839049234" /><br>
							<?php
							settings_fields('youtube-tagsforall-group');
						  	do_settings_sections('youtube-tagsforall-group');
							break;
					case "hubspot":
							?>
							  	<label>Account Number Hubspot</label><br>
							  	<input name="hubappid" id="hubappid" type="text" value="<?php echo esc_attr( get_option('hubappid') ); ?>" placeholder="02347802834902839049234"/><br>
							<?php
							settings_fields('hubspot-tagsforall-group');
						  	do_settings_sections('hubspot-tagsforall-group');
							break;
				  	}
					submit_button();
				  ?>
			  </form>
			</div>
		</div>
	  <?php
	}
	function register_mysettings_tfa() { // whitelist options
		//GENERAL
		register_setting(
            'general-tagsforall-group', // Option group
            'webtitle', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'general-tagsforall-group', // Option group
            'webdesc', // Option name
            array('sanitize') // Sanitize
        );
		//Redes Sociales
		register_setting(
            'general-tagsforall-group', // Option group
            'webauthor', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'general-tagsforall-group', // Option group
            'websubject', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'general-tagsforall-group', // Option group
            'webclass', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'general-tagsforall-group', // Option group
            'weblang', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'general-tagsforall-group', // Option group
            'webcopy', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'general-tagsforall-group', // Option group
            'webdesign', // Option name
            array('sanitize') // Sanitize
        );
		//Imágenes
		register_setting(
            'general-tagsforall-group', // Option group
            'webpublish', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'general-tagsforall-group', // Option group
            'webrevisit', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'general-tagsforall-group', // Option group
            'webrobots', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'general-tagsforall-group', // Option group
            'webdist', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'general-tagsforall-group', // Option group
            'webcity', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'general-tagsforall-group', // Option group
            'webcountry', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'general-tagsforall-group', // Option group
            'webexpires', // Option name
            array('sanitize') // Sanitize
        );
		//KEYWORDS
		register_setting(
            'keyw-tagsforall-group', // Option group
            'keywordstfs', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'keyw-tagsforall-group', // Option group
            'descripttfs', // Option name
            array('sanitize') // Sanitize
        );
		//GOOGLE
		register_setting(
            'google-tagsforall-group', // Option group
            'webmastert', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'google-tagsforall-group', // Option group
            'gatracking', // Option name
            array('sanitize') // Sanitize
        );
		//TWITTER
		register_setting(
            'twitter-tagsforall-group', // Option group
            'twappid', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'twitter-tagsforall-group', // Option group
            'twsecretkeyapi', // Option name
            array('sanitize') // Sanitize
        );
		//FACEBOOK
		register_setting(
            'facebook-tagsforall-group', // Option group
            'fbappid', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'facebook-tagsforall-group', // Option group
            'fbsecretkeyapi', // Option name
            array('sanitize') // Sanitize
        );
		//YOUTUBE
		register_setting(
            'youtube-tagsforall-group', // Option group
            'ytappid', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'youtube-tagsforall-group', // Option group
            'ytsecretkeyapi', // Option name
            array('sanitize') // Sanitize
        );
		register_setting(
            'hubspot-tagsforall-group', // Option group
            'hubappid', // Option name
            array('sanitize') // Sanitize
        );
	}
	function output_submenu_tfa() {
	  echo '<h1>Este es el submenú</h1>';
	}
	function your_admin_notices_action_tfa() {
	    settings_errors('your-settings-error-slug');
	}
	function add_action_links_tfa ( $links ) {
		$mylinks = array('<a href="'.admin_url('admin.php?page=tagsforall_menu_plugin_slug').'">Settings</a>');
		return array_merge( $links, $mylinks );
	}
	function add_scripts_tracking_tfa(){
        $google=get_option('gatracking');

		if(!empty($google)){
			?>
				<!-- Global site tag (gtag.js) - Google Analytics -->
				<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo($google); ?>"></script>
				<script>
					window.dataLayer = window.dataLayer || [];
				  	function gtag(){
				  		dataLayer.push(arguments);
				  	}
				  	gtag('js', new Date());
				  	gtag('config', '<?php echo($google); ?>');
				</script>
			<?php
		}
        $hubspot=get_option('hubappid');

		if(!empty($hubspot)){
			?>
				<!-- Start of HubSpot Embed Code -->
				<script type="text/javascript" id="hs-script-loader" async defer src="//js.hs-scripts.com/<?php echo($hubspot); ?>.js"></script>
				<!-- End of HubSpot Embed Code -->
			<?php
		}
	}

	function add_meta_webmastertools(){
		$webmastertool=get_option('webmastert');
		if(!empty($webmastertool)){
			?>
				<meta name="google-site-verification" content="<?php echo($webmastertool); ?>" />
			<?php
		}
	}

	add_action('wp_footer', 'add_scripts_tracking_tfa');
	add_action('admin_notices', 'your_admin_notices_action_tfa');
	add_action('wp_head', 'add_meta_seo');
	add_action('wp_head', 'add_meta_webmastertools');
	add_action('admin_menu', 'my_menu_tfa');
	add_action('admin_init', 'register_mysettings_tfa');
	add_action('add_meta_boxes', 'global_notice_meta_box_tfa');
	add_action('save_post', 'save_articulos_meta_tfa', 10, 3 );
	//add_action('admin_menu', 'my_sub_menu');
	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links_tfa');/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tagsforall() {

	$plugin = new tagsforall();
	$plugin->run();

}
run_tagsforall();
