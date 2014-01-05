<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'WP Blogging' );
define( 'CHILD_THEME_URL', 'http://www.nhatdong.com/' );
define( 'CHILD_THEME_VERSION', '1.0.0' );

//* Add HTML5 markup structure
add_theme_support( 'html5' );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Add support for custom background
add_theme_support( 'custom-background' );

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

//* Reposition the site description
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

//* Reposition the primary navigation menu
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_before_header', 'genesis_do_nav' );

/** Display related posts in Genesis based on Tags */
function related_posts_tags () {
  if ( is_single ( ) ) {
    global $post;
      $count = 0;
      $postIDs = array( $post->ID );
      $related = '';
      $tags = wp_get_post_tags( $post->ID );
    foreach ( $tags as $tag ) {
    $tagID[] = $tag->term_id;
}
$args = array(
      'tag__in' => $tagID,
      'post__not_in' => $postIDs,
      'showposts' => 7,
      'ignore_sticky_posts' => 1,
      'tax_query' => array(
      array(
      'taxonomy' => 'post_format',
      'field' => 'slug',
      'terms' => array(
      'post-format-link',
      'post-format-status',
      'post-format-aside',
      'post-format-quote'
      ),
      'operator' => 'NOT IN'
      )
      )
      );
$tag_query = new WP_Query( $args );
if ( $tag_query->have_posts() ) {
while ( $tag_query->have_posts() ) {
$tag_query->the_post();
$related .= '<li><a href="' . get_permalink() . '" rel="bookmark" title="' . get_the_title() . '">' . get_the_title() . '</a></li>';
$postIDs[] = $post->ID;
$count++;
}
}
if ( $related ) {
printf( '<div class="related-posts"><h3>Related Posts</h3><ul>%s</ul></div>', $related );
}
wp_reset_query();
}
}
add_action( 'genesis_after_entry_content', 'related_posts_tags' );

//* Display Descriptions in WordPress Menu
function be_add_description( $item_output, $item ) {
 	$description = $item->post_content;
 if (' ' !== $description ) 
 return preg_replace( '/(<a.*?>[^<]*?)</', '$1' . '<span class="menu-description">' . $description . '</span><', $item_output);
 else
 return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'be_add_description', 10, 2 );

genesis_register_sidebar( array(
  'id'          => 'after-entry',
  'name'        => __( 'After Entry', 'sinhvienmag' ),
  'description' => __( 'This is the after entry section.', 'sinhvienmag' ),
) );

//* Hooks after-entry widget area to single posts
add_action( 'genesis_entry_footer', 'sinhvienmag_after_post', 12 ); 
function sinhvienmag_after_post() {
    if ( ! is_singular( 'post' ) )
      return;
    genesis_widget_area( 'after-entry', array(
    'before' => '<div class="after-entry widget-area"><div class="wrap">',
    'after'  => '</div></div>',
    ) );
}

//* Redirect author link to about page
add_filter( 'author_link', 'my_author_link' );
function my_author_link() {
  return home_url( 'gioi-thieu' );
}

//* Remove elements with post formats
add_action( 'genesis_before_entry', 'remove_elements' );
function remove_elements() {
  
  //* Remove if post has format
  if ( get_post_format() ) {
    remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
    remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
    remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
    remove_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
  }
  //* Add back, as post has no format
  else {
    add_action( 'genesis_entry_header', 'genesis_do_post_title' );
    add_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_open', 5 );
    add_action( 'genesis_entry_footer', 'genesis_post_meta' );
    add_action( 'genesis_entry_footer', 'genesis_entry_footer_markup_close', 15 );
  }

}

//* Remove the entry header
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );

//* Customize the entry meta in the entry footer
add_filter( 'genesis_post_meta', 'sp_post_meta_filter' );
function sp_post_meta_filter( $post_meta ) {
  $post_meta = 'Posted on [post_date] by [post_author_posts_link] under [post_categories before=""] with tag [post_tags before=""][post_edit]';
  return $post_meta;
}

//* Create portfolio custom post type
add_action( 'init', 'sinhvienmag_portfolio_post_type' );
function sinhvienmag_portfolio_post_type() {

  register_post_type( 'portfolio',
    array(
      'labels' => array(
        'name'          => __( 'Portfolio', 'sinhvienmag' ),
        'singular_name' => __( 'Portfolio', 'sinhvienmag' ),
      ),
      'exclude_from_search' => true,
      'has_archive'         => true,
      'hierarchical'        => true,
      'menu_icon'           => get_stylesheet_directory_uri() . '/images/portfolio.png',
      'public'              => true,
      'rewrite'             => array( 'slug' => 'portfolio' ),
      'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'genesis-seo' ),
    )
  );
  
}


//* Register widget areas
genesis_register_sidebar( array(
  'id'          => 'home-top',
  'name'        => __( 'Home Top', 'winport' ),
  'description' => __( 'This is the Home Top section.', 'winport' ),
) );

genesis_register_sidebar( array(
  'id'          => 'welcome-feature-1',
  'name'        => __( 'Welcome Featured 1', 'winport' ),
  'description' => __( 'This is the Home Welcome section.', 'winport' ),
) );
genesis_register_sidebar( array(
  'id'          => 'welcome-feature-2',
  'name'        => __( 'Welcome Featured 2', 'winport' ),
  'description' => __( 'This is the Home Featured 1 - Left section.', 'winport' ),
) );
genesis_register_sidebar( array(
  'id'          => 'welcome-feature-3',
  'name'        => __( 'Welcome Featured 3', 'winport' ),
  'description' => __( 'This is the Home Featured 1 - Middle section.', 'winport' ),
) );

genesis_register_sidebar( array(
  'id'          => 'home-full-width',
  'name'        => __( 'Home Full Width', 'winport' ),
  'description' => __( 'This is the Home Full Width section.', 'winport' ),
) );

genesis_register_sidebar( array(
  'id'          => 'home-featured-1',
  'name'        => __( 'Home Featured 1', 'winport' ),
  'description' => __( 'This is the Home Featured 1 - Right section.', 'winport' ),
) );
genesis_register_sidebar( array(
  'id'          => 'home-featured-2',
  'name'        => __( 'Home Featured 2', 'winport' ),
  'description' => __( 'This is the Home Featured 2 section.', 'winport' ),
) );
genesis_register_sidebar( array(
  'id'          => 'home-featured-3',
  'name'        => __( 'Home Featured 3', 'winport' ),
  'description' => __( 'This is the Home Featured 3 section.', 'winport' ),
) );
genesis_register_sidebar( array(
  'id'          => 'home-featured-4',
  'name'        => __( 'Home Featured 4', 'winport' ),
  'description' => __( 'This is the Home Featured 4 section.', 'winport' ),
) );

genesis_register_sidebar( array(
  'id'          => 'home-bottom',
  'name'        => __( 'Home Bottom', 'winport' ),
  'description' => __( 'This is the Home Bottom section.', 'winport' ),
) );
