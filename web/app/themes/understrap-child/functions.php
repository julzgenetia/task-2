<?php 
	add_action( 'wp_enqueue_scripts', 'understrap_child_enqueue_styles' );

	function understrap_child_enqueue_styles() {
		wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' ); 
	}

	//Disable Comments on Posts
	add_action('admin_init', function () {
		global $pagenow;
			
		if ($pagenow === 'edit-comments.php') {
			wp_safe_redirect(admin_url());
			exit;
		}

		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

		foreach (get_post_types() as $post_type) {
			if (post_type_supports($post_type, 'comments')) {
				remove_post_type_support($post_type, 'comments');
				remove_post_type_support($post_type, 'trackbacks');
			}
		}
	});		

	add_filter('comments_open', '__return_false', 20, 2);
	add_filter('pings_open', '__return_false', 20, 2);
	add_filter('comments_array', '__return_empty_array', 10, 2);
	add_action('admin_menu', function () {
		remove_menu_page('edit-comments.php');
	});
	add_action('init', function () {
		if (is_admin_bar_showing()) {
			remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
		}
	});

	//Limit the Excerpt Length to 30 Characters
	function sf_limit_excerpt( $excerpt ) {
		return substr( $excerpt, 0, 30 ) . '<p><a class="btn btn-secondary understrap-read-more-link" href="' . get_the_permalink() . '">Read More…<span class="screen-reader-text"> from Hello world!</span></a></p>';
	}
	add_filter( 'get_the_excerpt', 'sf_limit_excerpt' );

	//Remove Uploaded Media Files Once Post is Deleted
	/* Notes: Need the post to be permanently deleted. Affected media files are the ones which are uploaded to the Featured Image and Post Editor.*/ 
	function sf_removed_uploaded_media( $post_id ) {
		if( get_post_type($post_id) == "post" ) {
			$attachments = get_attached_media( '', $post_id );

			foreach ($attachments as $attachment) {
			wp_delete_attachment( $attachment->ID, 'true' );
			}
		}
	}
	add_action( 'before_delete_post', 'sf_removed_uploaded_media' );
 ?>