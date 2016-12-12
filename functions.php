/**
* You need to add this code in your functions.php child theme file.
**/

add_filter( 'groups_import_export_new_user_registration_subject', 'sb_we_groups_import_export_new_user_registration_subject', 10, 3);
function sb_we_groups_import_export_new_user_registration_subject ( $subject, $user_id, $plaintext ) {
	global $sb_we_home;

	$settings = get_option('sb_we_settings');
	$user_subject = apply_filters('sb_we_user_subject_template', $settings->user_subject);

	$user = new WP_User($user_id);
	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);
	$first_name = $user->first_name;
	$last_name = $user->last_name;

	$date = date(get_option('date_format'));
	$time = date(get_option('time_format'));

	$blog_name = get_option('blogname');

	$user_subject = str_replace('[blog_name]', $blog_name, $user_subject);
	$user_subject = str_replace('[site_url]', $sb_we_home, $user_subject);
	$user_subject = str_replace('[user_email]', $user_email, $user_subject);
	$user_subject = str_replace('[last_name]', $last_name, $user_subject);
	$user_subject = str_replace('[first_name]', $first_name, $user_subject);
	$user_subject = str_replace('[user_login]', $user_login, $user_subject);
	$user_subject = str_replace('[user_id]', $user_id, $user_subject);
	$user_subject = str_replace('[date]', $date, $user_subject);
	$user_subject = str_replace('[time]', $time, $user_subject);

	return $user_subject;
}

add_filter( 'groups_import_export_new_user_registration_message', 'sb_we_groups_import_export_new_user_registration_message', 10, 3);
function sb_we_groups_import_export_new_user_registration_message ( $message, $user_id, $plaintext ) {
	global $sb_we_home, $wpdb;

	$settings = get_option('sb_we_settings');
	$user_message = apply_filters('sb_we_user_body_template', $settings->user_body);

	$user = new WP_User($user_id);
	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);
	$first_name = $user->first_name;
	$last_name = $user->last_name;

	$admin_email = get_option('admin_email');

	$login_url = $reset_pass_url = wp_login_url();

	if (version_compare(get_bloginfo( 'version' ), '4.3') >= 0) {
		$key = wp_generate_password( 20, false );

		do_action( 'retrieve_password_key', $user->user_login, $key );

		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . WPINC . '/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}

		$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
		$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

		$login_url = $reset_pass_url = wp_login_url() . '?action=rp&key=' . $key . '&login=' . rawurlencode($user->user_login);
	}

	$date = date(get_option('date_format'));
	$time = date(get_option('time_format'));

	$blog_name = get_option('blogname');

	$user_message = str_replace('[admin_email]', $admin_email, $user_message);
	$user_message = str_replace('[site_url]', $sb_we_home, $user_message);
	$user_message = str_replace('[login_url]', $login_url, $user_message);
	$user_message = str_replace('[reset_pass_url]', $reset_pass_url, $user_message);
	$user_message = str_replace('[reset_pass_link]', '<a href="' . $reset_pass_url . '" target="_blank">' . __( 'Click to set', SB_WE_DOMAIN ) . '</a>', $user_message);
	$user_message = str_replace('[user_email]', $user_email, $user_message);
	$user_message = str_replace('[user_login]', $user_login, $user_message);
	$user_message = str_replace('[last_name]', $last_name, $user_message);
	$user_message = str_replace('[first_name]', $first_name, $user_message);
	$user_message = str_replace('[user_id]', $user_id, $user_message);
	$user_message = str_replace('[plaintext_password]', '*****', $user_message);
	$user_message = str_replace('[user_password]', '*****', $user_message);
	$user_message = str_replace('[blog_name]', $blog_name, $user_message);
	$user_message = str_replace('[date]', $date, $user_message);
	$user_message = str_replace('[time]', $time, $user_message);

	$user_message = apply_filters('sb_we_email_message', $user_message, $settings, $user_id);

	return $user_message;
}
