<?php
/**
 * Plugin Name: Remote Downloads for SureMembers
 * Plugin URI: https://github.com/jthum/suremembers-remote-downloads
 * Description: An add-on for SureMembers to serve private downloads from remote storage.
 * Author: Jayadeep Thum
 * Author URI: https://wird.co/
 * Version: 0.1
 * License: GPL v2
 */


use SureMembers\Inc\Access_Groups;


function download_remote_file( $url ) {
	
	$headers = get_headers( $url, 1 );
	$headers = array_change_key_case( $headers );
	$filesize  = isset( $headers['content-length'] ) ? $headers['content-length'] : -1;
	$last_modified = $headers['last-modified'];

	header( 'Content-Description: File Transfer' );
    header( 'Content-Type: application/octet-stream' );
	header( 'Cache-Control: max-age=2592000, public' );
	header( 'Last-Modified: ' . $last_modified );
	header( 'Expires: ' . gmdate( 'D, d M Y H:i:s', time() + 2592000 ) . ' GMT' );
	header( 'Content-Disposition: attachment; filename="' . basename( $url ) .'"' );
	header( 'Content-Length: ' . $filesize );
	ob_end_clean();
	readfile( $url );
	exit;

}


add_action( 'template_redirect', function() {

	$download_id = intval( get_query_var( 'suremembers-download-id' ) );
	$token = sanitize_text_field( get_query_var( 'suremembers-download-token' ) );

	if ( $download_id && $token ) {

		$associated_access_groups = Access_Groups::by_download_id( $download_id );
		$check_user_has_access = Access_Groups::check_if_user_has_access( $associated_access_groups );

		if ( current_user_can( 'administrator' ) || $check_user_has_access ) {

			$attachment = get_attached_file( $download_id );

			if ( strpos( $attachment, '.url_.txt' ) !== false ) {

				$attachment = file_get_contents( $attachment );
				$attachment_type = wp_check_filetype( $attachment );

				download_remote_file($attachment);

			}

		} else {

			$get_priority_id = Access_Groups::get_priority_id( $associated_access_groups );
			$restriction_meta = get_post_meta( $get_priority_id, SUREMEMBERS_PLAN_RULES, true );
			$restriction_data = $restriction_meta['restrict'];

			$url = SUREMEMBERS_DIR . 'inc/restricted-template.php';

			if ( file_exists( $url ) ) {
				load_template( $url, true, $restriction_data );
				die();
			}

		}
	}

});