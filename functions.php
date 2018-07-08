<?php
/**
 * Githuber theme functions and definitions
 *
 * @author Terry Lin <terry.developer@email.com>
 * @link https://terryl.in
 *
 * @package WordPress
 * @subpackage Githuber
 * @since 1.0.0
 */

// Load Bootstrap 4 Walker.
require_once dirname( __FILE__ ) . '/inc/class-bootstrap4-walker.php';

if ( function_exists( 'add_theme_support' ) ) {
	// Add Menu Support.
	add_theme_support( 'menus' );
	add_theme_support( 'title-tag' );

	// Add Thumbnail Theme Support.
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'large', 700, '', true );        // Large Thumbnail.
	add_image_size( 'medium', 250, '', true );       // Medium Thumbnail.
	add_image_size( 'small', 120, '', true );        // Small Thumbnail.
	add_image_size( 'custom-size', 700, 200, true ); // Custom Thumbnail Size call using the_post_thumbnail( 'custom-size'); .

	// Enables post and comment RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// Move admin bar to page bottom.
	add_theme_support( 'admin-bar', array( 'callback' => 'githuner_admin_bar' ) );

	// Localisation Support.
	load_theme_textdomain( 'githuber', get_template_directory() . '/languages' );
}

/**
 *  Githuber navigation.
 */
function githuber_nav() {
	wp_nav_menu(
		array(
			'theme_location'  => 'header-menu',
			'container'       => 'div',
			'container_class' => 'collapse navbar-collapse',
			'container_id'    => 'githuber-nav-bar',
			'menu_class'      => 'navbar-nav mr-auto',
			'menu_id'         => false,
			'depth'           => 2,
			'fallback_cb'     => 'Bootstrap4_Walker::fallback',
			'walker'          => new Bootstrap4_Walker(),
		)
	);
}

/**
 * Add scripts
 */
function githuber_header_scripts() {
	if ( 'wp-login.php' !== $GLOBALS['pagenow'] && ! is_admin() ) {

		wp_register_script( 'bootstrap', get_template_directory_uri() . '/vendor/bootstrap/js/bootstrap.bundle.min.js', array( 'jquery' ), '4.1.0' );
		wp_enqueue_script( 'bootstrap' );

		wp_register_script( 'bootstrap-toc', get_template_directory_uri() . '/vendor/bootstrap-toc/bootstrap-toc.min.js', array( 'jquery' ), '1.0.0' );
		wp_enqueue_script( 'bootstrap-toc' );

		wp_register_script( 'githuber-script', get_template_directory_uri() . '/js/scripts.js', array( 'jquery' ), '1.0.0' );
		wp_enqueue_script( 'githuber-script' );
	}
}

add_action( 'init', 'githuber_header_scripts' );

/**
 * Add styles
 */
function githuber_styles() {
	wp_register_style( 'bootstrap', get_template_directory_uri() . '/vendor/bootstrap/css/bootstrap.min.css', array(), '4.1.o', 'all' );
	wp_enqueue_style( 'bootstrap' );

	wp_register_style( 'fontawesome', get_template_directory_uri() . '/vendor/fontawesome/css/fontawesome.min.css', array(), '5.0.12', 'all' );
	wp_enqueue_style( 'fontawesome' );

	wp_register_style( 'fontawesome-solid', get_template_directory_uri() . '/vendor/fontawesome/css/fa-solid.min.css', array(), '5.0.12', 'all' );
	wp_enqueue_style( 'fontawesome-solid' );

	wp_register_style( 'githuber', get_template_directory_uri() . '/style.css', array(), '1.0', 'all' );
	wp_enqueue_style( 'githuber' );

	wp_register_style( 'markdown', get_template_directory_uri() . '/markdown_themes/markdown_github.css', array(), '1.0', 'all' );
	wp_enqueue_style( 'markdown' );
}

add_action( 'wp_enqueue_scripts', 'githuber_styles' );

/**
 * Enqueue the javascript that performs in-link comment reply fanciness
 *
 * @return void
 */
function enqueue_comment_reply() {
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'enqueue_comment_reply' );

/**
 * Register githuber Navigation
 *
 * @return void
 */
function register_githuber_menu() {
	register_nav_menus(
		// Using array to specify more menus if needed.
		array(
			'header-menu'  => __( 'Header Menu', 'githuber' ),
			'sidebar-menu' => __( 'Sidebar Menu', 'githuber' ),
		)
	);
}

add_action( 'init', 'register_githuber_menu' );

/**
 * Remove surrounding <div> from WP Navigation to cleanup markup
 *
 * @param array $args - for example: $args['container'] = false .
 * @return string|array
 */
function remove_menu_args( $args = '' ) {
	return $args;
}
add_filter( 'wp_nav_menu_args', 'remove_menu_args' );

/**
 * Remove Injected classes, ID's and Page ID's from Navigation <li> items
 *
 * @param array $var - string or array.
 * @return string|array
 */
function remove_list_item_attributes( $var ) {
	return is_array( $var ) ? array() : '';
}

add_filter( 'nav_menu_item_id', 'remove_list_item_attributes', 100, 1 );
add_filter( 'page_css_class', 'remove_list_item_attributes', 100, 1 );

/**
 * Remove invalid rel attribute values in the categorylist
 * Valid attribute values:
 * http://microformats.org/wiki/existing-rel-values
 *
 * @param array $var - string or array.
 * @return string|array
 */
function remove_invalid_rel_for_category( $var ) {
	return str_replace( 'rel="category tag"', 'rel="tag"', $var );
}

add_filter( 'the_category', 'remove_invalid_rel_for_category' );

/**
 * Add page slug to body class for better customization if need.
 *
 * @param array $classes - html class name.
 * @return array
 */
function add_slug_to_body_class( $classes ) {
	global $post;
	if ( is_home() ) {
		$key = array_search( 'blog', $classes, true );
		if ( $key > -1 ) {
			unset( $classes[ $key ] );
		}
	} elseif ( is_page() ) {
		$classes[] = sanitize_html_class( $post->post_name );
	} elseif ( is_singular() ) {
		$classes[] = sanitize_html_class( $post->post_name );
	}
	return $classes;
}

add_filter( 'body_class', 'add_slug_to_body_class' );

/**
 * Register widget area.
 */
function githuber_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'githuber' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here to appear in your sidebar on blog posts and archive pages.', 'githuber' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer', 'githuber' ),
		'id'            => 'sidebar-2',
		'description'   => __( 'Add widgets here to appear in your footer.', 'githuber' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}

add_action( 'widgets_init', 'githuber_widgets_init' );

/**
 * Remove wp_head() injected Recent Comment styles.
 *
 * @return void
 */
function remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array(
		$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
		'recent_comments_style',
	) );
}

add_action( 'widgets_init', 'remove_recent_comments_style' );

/**
 * Bootstrap 4 Pagination
 *
 * @param integer $range - range of pagination to show previous and next pages.
 * @return void
 */
function bootstrap4_pagination( $range = 2 ) {
	global $paged, $wp_query;

	$pages      = $wp_query->max_num_pages;
	$pagi_items = ( $range * 2 ) + 1;

	if ( empty( $pages ) ) {
		$pages = $wp_query->max_num_pages;

		if ( empty( $pages ) ) {
			$pages = 1;
		}
	}

	if ( 1 !== $pages ) { ?>

		<nav aria-label="Page navigation" role="navigation">
			<span class="sr-only"><?php esc_html_e( 'Page navigation', 'githuber' ); ?></span>
			<ul class="pagination justify-content-center ft-wpbs">

				<li class="page-item disabled hidden-md-down d-none d-lg-block">
					<span class="page-link"><?php echo esc_html( $paged ); ?> / <?php echo esc_html( $pages ); ?></span>
				</li>

				<?php if ( $paged > 2 && $paged > $range + 1 && $pagi_items < $pages ) : ?>

				<li class="page-item">
					<a class="page-link" href="<?php echo esc_url( get_pagenum_link( 1 ) ); ?>" aria-label="<?php esc_html_e( 'First Page', 'githuber' ); ?>">
						&laquo; <span class="hidden-sm-down d-none d-md-block"> First</span>
					</a>
				</li>
				<?php endif; ?>

				<?php if ( $paged > 1 && $pagi_items < $pages ) : ?>
					<li class="page-item">
						<a class="page-link" href="<?php echo esc_url( get_pagenum_link( $paged - 1 ) ); ?>" aria-label="<?php esc_html_e( 'Previous Page', 'githuber' ); ?>">
							&lsaquo; <span class="hidden-sm-down d-none d-md-block"> Previous</span>
						</a>
					</li>
				<?php endif; ?>

				<?php for ( $i = 1; $i <= $pages; $i++ ) : ?>
					<?php if ( 1 !== $pages && ( ! ( $i >= $paged + $range + 1 || $i <= ( $paged - $range ) - 1 ) || $pages <= $pagi_items ) ) : ?>
						<?php if ( $paged === $i ) : ?>
							<li class="page-item active">
								<span class="page-link">
									<span class="sr-only"><?php esc_html_e( 'Current Page', 'githuber' ); ?></span>
									<?php echo esc_html( $i ); ?>
								</span>
							</li>
						<?php else : ?>
							<li class="page-item">
								<a class="page-link" href="<?php echo esc_url( get_pagenum_link( $i ) ); ?>'">
									<span class="sr-only">Page </span>
									<?php echo esc_html( $i ); ?>
								</a>
							</li>
						<?php endif; ?>
					<?php endif; ?>
				<?php endfor; ?>

				<?php if ( $paged < $pages && $pagi_items < $pages ) : ?>
					<li class="page-item">
						<a class="page-link" href="<?php echo esc_url( et_pagenum_link( $paged + 1 ) ); ?>" aria-label="Next Page">
							<span class="hidden-sm-down d-none d-md-block">Next </span>&rsaquo;
						</a>
					</li>
				<?php endif; ?>

				<?php if ( $paged < $pages - 1 && $paged + $range - 1 < $pages && $pagi_items < $pages ) : ?>
					<li class="page-item">
						<a class="page-link" href="<?php echo esc_url( get_pagenum_link( $pages ) ); ?>" aria-label="Last Page">
							<span class="hidden-sm-down d-none d-md-block">Last </span>&raquo;
						</a>
					</li>';
				<?php endif; ?>
			</ul>
		</nav>

		<?php
	}
}

/**
 * Post excerpt lenth.
 *
 * @param integer $length Post excerpt lenth.
 * @return integer
 */
function githuber_excerpt_length( $length ) {
	return 20;
}

add_filter( 'excerpt_length', 'githuber_excerpt_length' );

/**
 * Get the excerpt.
 *
 * @return void
 */
function githuber_excerpt() {
	global $post;

	$output = get_the_excerpt();
	$output = apply_filters( 'wptexturize', $output );
	$output = apply_filters( 'convert_chars', $output );
	echo '<p>' . esc_html( $output ) . '</p>';
}

/**
 * Show excetps ... read more
 *
 * @param string $more Pass to excerpt_more filiter.
 * @return string
 */
function githuber_read_more( $more ) {
	global $post;
	return '... <a class="view-article" href="' . get_permalink( $post->ID ) . '">' . __( 'View Article', 'githuber' ) . '</a>';
}

add_filter( 'excerpt_more', 'githuber_read_more' );

/**
 * Remove 'text/css' from enqueued stylesheet
 *
 * @param string $html HTML string.
 * @return string
 */
function remove_style_type( $html ) {
	return preg_replace( '~\s+type=["\'][^"\']++["\']~', '', $html );
}

add_filter( 'style_loader_tag', 'remove_style_type' );

/**
 * Remove width and height dynamic attributes to thumbnails that prevent fluid images in the_thumbnail.
 *
 * @param string $html HTML string.
 * @return string
 */
function remove_thumbnail_dimensions( $html ) {
	$html = preg_replace( '/(width|height)=\"\d*\"\s/', '', $html );
	return $html;
}

add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 );

/**
 * Custom avatar
 *
 * @param array $avatar_defaults Avatar.
 * @return array
 */
function custom_gravatar( $avatar_defaults ) {
	// Change filename here.
	$myavatar = get_template_directory_uri() . '/img/gravatar.jpg';
	// Name of the gavatar image.
	$avatar_defaults[ $myavatar ] = 'Custom Gravatar';
	return $avatar_defaults;
}

add_filter( 'avatar_defaults', 'custom_gravatar' );

/**
 * Threaded Comments
 *
 * @return void
 */
function enable_threaded_comments() {
	if ( ! is_admin() ) {
		if ( is_singular() && comments_open() && ( 1 === get_option( 'thread_comments' ) ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
}

add_action( 'get_header', 'enable_threaded_comments' );

// GitHub style comment blocks.
if ( ! function_exists( 'githuber_comment' ) ) {
	/**
	 * Template for comments and pingbacks.
	 *
	 * @param array   $comment (optional) Array obtained by get_comments query.
	 * @param array   $args    (optional) The options for the function.
	 * @param integer $depth   (optional).
	 * @return void
	 */
	function githuber_comment( $comment, $args, $depth ) {
		global $post;

		switch ( $comment->comment_type ) {
			case 'pingback':
			case 'trackback':
				// Display trackbacks differently than normal comments.
				?>

				<div <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">

					<article id="comment-<?php comment_ID(); ?>" class="pingback">
						<p><?php esc_html_e( 'Pingback:', 'githuber' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( esc_html__( '(Edit)', 'githuber' ), '<span class="edit-link">', '</span>' ); ?></p>
					</article>

				</div>

				<?php
				break;
			default:
				// Proceed with normal comments.
				?>

				<div <?php comment_class( 'comment-wrapper' ); ?> id="comment-<?php comment_ID(); ?>">
					<div class="comment-avatar">
						<?php echo get_avatar( $comment, 44 ); ?>
					</div>
					<div class="comment-container"> 
						<div class="comment-header">
							<?php edit_comment_link( '<i class="fas fa-pencil-alt"></i>', '<div class="comment-header-btn edit">', '</div>' ); ?>
							<div class="comment-header-btn reply">
								<?php
									comment_reply_link(
										array_merge(
											$args,
											array(
												'reply_text' => '<i class="fas fa-reply"></i>',
												'depth' => $depth,
												'max_depth' => $args['max_depth'],
											)
										)
									);
								?>
							</div>

							<?php

							if ( $comment->user_id === $post->post_author ) {
								$commenter_type_css = 'author';

							?>

								<div class="comment-label">
									<?php esc_html_e( 'Author', 'githuber' ); ?>
								</div>

							<?php

							} else {
								$commenter_type_css = 'reader';
							}

							?>

							<div class="comment-header-text f-14">
								<?php

								printf( '<cite class="' . esc_attr( $commenter_type_css ) . '">%1$s</cite>&nbsp;',
									get_comment_author_link()
								);

								printf( '<a href="%1$s" title="commented %2$s" class="comment-link"><time itemprop="datePublished" datetime="%3$s">%4$s</time></a>',
									esc_url( get_comment_link( $comment->comment_ID ) ),
									sprintf( '%1$s @ %2$s', get_comment_date(), get_comment_time() ),
									get_comment_time( 'c' ),
									sprintf(
										/* translators: %s: days */
										esc_html__( 'commented %s ago', 'githuber' ),
										esc_html( human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) )
									)
								);

								?>
							</div>
						</div>
						<div class="comment-body">
							<?php if ( '0' === $comment->comment_approved ) { ?>
								<p class="comment-awaiting-moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'githuber' ); ?></p>
							<?php } ?>
							<div class="comment-content comment">
								<?php comment_text(); ?>	
							</div>
						</div>
					</div>
				</div>

				<?php
				break;
		}
		// end - switch.
	}
}

/**
 * Overwrite the default WordPress comment form.
 * Build a custom Githuber style form.
 *
 * @return array
 */
function githuber_comment_form() {
	$comment_field = '
		<div class="form-row">
			<div class="col-sm-12 my-1">
				<textarea id="comment" name="comment" class="form-control" aria-required="true"></textarea>
			</div>
		</div>
	';

	$comments_args = array(
		'label_submit'         => __( 'Send', 'githuber' ),
		'title_reply'          => __( 'Write a Reply or Comment', 'githuber' ),
		'comment_notes_after'  => '',
		'comment_field'        => $comment_field,
		'title_reply_before'   => '<div id="reply-title" class="comment-reply-title">',
		'title_reply_after'    => '</div>',
		'class_submit'         => 'btn btn-green my-1',
		'comment_notes_before' => '<p class="comment-notes">' . __( 'Your email address will not be published.' ) . '</p>',
	);
	return $comments_args;
}

add_filter( 'comment_form_defaults', 'githuber_comment_form' );

/**
 * Comment fields
 */
function githuber_comment_fileds() {
	$commenter = wp_get_current_commenter();

	$author = '
		<div class="col-sm-6 my-1">
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text"><i class="fas fa-user"></i></div>
				</div>
				<input id="author" class="form-control" placeholder="' . __( 'Name' ) . '" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" maxlength="245" />
			</div>
		</div>
	';

	$email = '
		<div class="col-sm-6 my-1">
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text"><i class="fas fa-envelope"></i></div>
				</div>
				<input id="email" class="form-control" placeholder="' . __( 'Email' ) . '" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" maxlength="100" aria-describedby="email-notes" />
			</div>
		</div>
	';

	$url = '
		<div class="col-sm-12 my-1">
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text"><i class="fas fa-globe"></i></div>
				</div>
				<input id="url" class="form-control" placeholder="' . __( 'Website' ) . '" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) . '" maxlength="200" />
			</div>
		</div>
	';

	$fields = array(
		'before' => '<div class="form-row">',
		'author' => $author,
		'email'  => $email,
		'url'    => $url,	
		'after'  => '</div>',
	);

	return $fields;
}

add_filter( 'comment_form_default_fields', 'githuber_comment_fileds' );

/**
 * Move fields from bottom to top.
 *
 * @param array $fields Comment form fields.
 * @return array
 */
function move_comment_field_to_bottom( $fields ) {
	$comment_field = $fields['comment'];
	unset( $fields['comment'] );
	$fields['comment'] = $comment_field;
	return $fields;
}

add_filter( 'comment_form_fields', 'move_comment_field_to_bottom' );

/**
 * Register custom post type: Repository.
 *
 * @return void
 */
function create_post_type_repository() {
	register_post_type( 'repository',
		array(
			'labels' => array(
				'name'               => __( 'Repositories', 'githuber' ),
				'singular_name'      => __( 'Repository', 'githuber' ),
				'add_new'            => __( 'Add New', 'githuber' ),
				'add_new_item'       => __( 'Add New Repository', 'githuber' ),
				'edit'               => __( 'Edit', 'githuber' ),
				'edit_item'          => __( 'Edit Repository', 'githuber' ),
				'new_item'           => __( 'New Repository', 'githuber' ),
				'view'               => __( 'View Repository', 'githuber' ),
				'view_item'          => __( 'View Repository', 'githuber' ),
				'search_items'       => __( 'Search Repository', 'githuber' ),
				'not_found'          => __( 'No Repository Posts found', 'githuber' ),
				'not_found_in_trash' => __( 'No Repository Posts found in Trash', 'githuber' ),
			),

			'public'       => true,
			'hierarchical' => true,
			'has_archive'  => true,
			'can_export'   => true,
			'menu_icon'    => 'dashicons-lightbulb',
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'taxonomies'   => array( 'post_tag', 'category' ),
		)
	);

	register_taxonomy_for_object_type( 'category', 'repository' );
	register_taxonomy_for_object_type( 'post_tag', 'repository' );
}

add_action( 'init', 'create_post_type_repository' );

/**
 * Create Custom meta box for Repository
 *
 * @return void
 */
function add_repository_meta_box() {
	add_meta_box(
		'repository_meta_box',             // id.
		'GitHub Repository',               // title.
		'show_repository_fields_meta_box', // callback.
		'repository',                      // screen.
		'normal',                          // context.
		'high'                             // priority.
	);
}

add_action( 'add_meta_boxes', 'add_repository_meta_box' );

/**
 * Show custom meta box for Repository
 *
 * @return void
 */
function show_repository_fields_meta_box() {
	global $post;
	$meta = get_post_meta( $post->ID, 'github_repository', true );
?>

	<input type="hidden" name="metabox_nonce" value="<?php echo esc_html( wp_create_nonce( basename( __FILE__ ) ) ); ?>">

	<table>
		<tr>
			<td><strong>URL</strong></td>
			<td><input type="text" name="github_repository[url]" style="width: 100%" value="<?php echo esc_url( $meta['url'] ); ?>"></td>
		</tr>
		<tr>
			<td><strong>Buttons</strong></td>
			<td>
				<?php

				foreach ( array( 'star', 'fork', 'watch', 'issue', 'download' ) as $v ) :
					$checked = '';
					if ( ! empty( $meta[ $v ] ) ) {
						$checked = 'checked';
					}
				?>

				<label class="selectit"><input type="checkbox" name="github_repository[<?php echo $v; ?>]" value="<?php echo $v; ?>" <?php echo $checked; ?>> <?php echo ucfirst($v); ?></label> &nbsp;
				<?php endforeach; ?>
			</td>
		</tr>
	</table>
	</p>

<?php
}
/**
 * Save custom meta box for Repository
 *
 * @param integer $post_id Post's ID.
 * @return integer if return.
 */
function save_repository_meta( $post_id ) {
	// verify nonce.
	if ( ! empty( $_POST['metabox_nonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['metabox_nonce'] ), basename( __FILE__ ) ) ) {
		return $post_id;
	}
	// check autosave.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	// check permissions.
	if ( ! empty( $_POST['post_type'] ) && 'page' === $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}
	}

	$old = get_post_meta( $post_id, 'github_repository', true );
	$new = $_POST['github_repository'];

	if ( $new && $new !== $old ) {
		update_post_meta( $post_id, 'github_repository', $new );
	} elseif ( '' === $new && $old ) {
		delete_post_meta( $post_id, 'github_repository', $old );
	}
}

add_action( 'save_post', 'save_repository_meta' );

/**
 * Register GitHub button script.
 *
 * @return void
 */
function post_repository_script() {
	if ( is_single() && 'repository' === get_post_type() ) {
		wp_enqueue_script( 'github-buttons', 'https://buttons.github.io/buttons.js', [], false, true );
	}
}
add_action( 'wp_enqueue_scripts', 'post_repository_script' );

/**
 * Article reading progress bar.
 *
 * @return void
 */
function post_progress_title_script() {
	if ( is_single() ) {
?>
	<script>

	jQuery( document ).ready(function( $ ) {
		const win = $( window );
		const doc = $( document );
		const progressBar = $( 'progress' );
		const progressLabel = $( '.progress-label' );
		const setValue = () => win.scrollTop();
		const setMax = () => doc.height() - win.height();
		const setPercent = () => Math.round( win.scrollTop() / (doc.height() - win.height()) * 100 );
		const pageTitle = $( '#post-title' );
		const pageTitleTop = pageTitle.offset().top;
		const progressBarContainer = $( '.page-single-title-bar' );
		const headerNavBrand = $( '.header .navbar-brand' );
		const progressTitle = $( '#progress-title' );
		const headerBarContainer = $( '.header .container' );

		progressLabel.text( setPercent() + '%' );
		progressBar.attr(
			{
				value: setValue(),
				max: setMax() 
			}
		);

		doc.on( 'scroll', () => {
			progressLabel.text( setPercent() + '%' );
			progressBar.attr(
				{
					value: setValue()
				} 
			);

			if ( doc.scrollTop() > headerNavBrand.height() ) {
				//headerBarContainer.fadeOut( 800 );
				progressTitle.html( pageTitle.html() );
				progressBarContainer.find( '.navbar-brand' ).html( headerNavBrand.html() );
				progressBarContainer.fadeIn( 100 );
				progressBarContainer.addClass( 'fixed-top' );
				progressBarContainer.addClass( 'slide-down' );
			} else {
				//headerBarContainer.fadeIn( 800 );
				progressBarContainer.removeClass( 'slide-down' );
				progressBarContainer.fadeOut( 100 );

				if ( progressBarContainer.hasClass( 'fixed-top' ) ) {
					setTimeout(function() {
						progressBarContainer.removeClass( 'fixed-top' );
					}, 500);
				}
			}
		});

		win.on( 'resize', () => {
			progressLabel.text( setPercent() + '%' );
			progressBar.attr(
				{
					value: setValue(), 
					max: setMax()
				} 
			);
		});
	});

	</script>
<?php
	}
}

add_action( 'wp_footer', 'post_progress_title_script', 1, 1 );

/**
 * Show title progress bar.
 *
 * @return void
 */
function title_progress_bar() {
	?>
		<div class="page-single-title-bar clear" role="banner">
			<nav class="navbar navbar-expand-lg navbar-dark" role="navigation">
				<div class="container">
					<a class="navbar-brand" href="<?php echo esc_url( home_url() ); ?>"></a>
					<div id="progress-title"></div>
				</div>	
			</nav>
			<div class="progress-wrapper">
				<div class="progress-label"></div>
				<progress></progress>
			</div>
		</div>
	<?php
}

// Add support for WP Editor.md plugin.
add_post_type_support( 'repository', 'wpcom-markdown' );

/**
 * Custom edit button with GitHub style.
 *
 * @return void
 */
function the_edit_button() {
	global $post;

	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return;
	}

	echo '
		<a href="' . esc_url( get_edit_post_link() ) . '">
			<div class="btn-counter text-only">		
				<div class="btn btn-green">' . esc_html__( 'Edit', 'githuber' ) . '</div>
			</div>
		</a>
	';
}

/**
 * Bootstrap 4 styled category buttons.
 */
function githuber_category() {
	$categories = get_the_category();

	foreach ( $categories as $category ) {
		echo '<a class="btn btn-xs btn-green" href="' . esc_url( get_category_link( $category->term_id ) ) . '"><i class="fas fa-star"></i>' . esc_html( $category->cat_name ) . '</a>';
	}
}

/**
 * Replace the default admin bar callback.
 * Move it to page bottom, because I would like to stick the page title progress bar on page top.
 */
function githuner_admin_bar() {

?>

	<style type="text/css" media="screen" id="githuner-admin-bar">
		html { margin-top: 0px !important; margin-bottom: 32px !important; }
		* html body { margin-top: 0px !important; margin-bottom: 32px !important; }
		@media screen and ( max-width: 782px ) {
			html { margin-top: 0px !important; margin-bottom: 46px !important; }
			* html body { margin-top: 0px !important; margin-bottom: 46px !important; }
		}
	</style>

<?php
}


// Display the links to the extra feeds such as category feeds.
remove_action( 'wp_head', 'feed_links_extra', 3 );
// Display the links to the general feeds: Post and Comment Feed.
remove_action( 'wp_head', 'feed_links', 2 );
// Display the link to the Really Simple Discovery service endpoint, EditURI link.
remove_action( 'wp_head', 'rsd_link' );
// Display the link to the Windows Live Writer manifest file.
remove_action( 'wp_head', 'wlwmanifest_link' );
// Index link.
remove_action( 'wp_head', 'index_rel_link' );
// Prev link.
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
// Start link.
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
// Display relational links for the posts adjacent to the current post.
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
// Display the XHTML generator that is generated on the wp_head hook, WP version.
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

