<?php
/*
  Plugin Name: Featured Post Block Type
  Version: 1.0
  Author: Bartek
  Text Domain: featured-professor
*/

if( ! defined( 'ABSPATH' ) ) exit;

require_once plugin_dir_path(__FILE__) . 'inc/generateProfessorHTML.php';
require_once plugin_dir_path(__FILE__) . 'inc/relatedPostsHTML.php';

class FeaturedProfessor {
  
  // Constructor
  function __construct() {
    add_action('init', [$this, 'onInit']);

    // REST API endpoint for professor HTML
    add_action('rest_api_init', [$this, 'profHTML']);

    // Filter to add related posts to the_content
    add_filter('the_content', [$this, 'addRelatedPosts']);
  }

  // Append related posts to content
  function addRelatedPosts($content) {
    if (is_singular('professor') && in_the_loop() && is_main_query()) {
      return $content . relatedPostsHTML(get_the_id());
    }
    return $content;
  }

  // Register REST API route
  function profHTML() {
    register_rest_route('featuredProfessor/v1', 'getHTML', array(
      'methods' => WP_REST_SERVER::READABLE,
      'callback' => [$this, 'getProfHTML']
    ));
  }

  // Callback for REST API
  function getProfHTML($data) {
    return generateProfessorHTML($data['profId']);
  }

  // Function hooked to WordPress init
  function onInit() {
    load_plugin_textdomain('featured-professor', false, dirname(plugin_basename(__FILE__)) . '/languages');

    // Register custom meta fields
    register_meta('post', 'featuredprofessor', array(
      'show_in_rest' => true,
      'type' => 'number',
      'single' => false
    ));

    // Scripts and styles
    wp_register_script('featuredProfessorScript', plugin_dir_url(__FILE__) . 'build/index.js', array('wp-blocks', 'wp-i18n', 'wp-editor'));
    wp_register_style('featuredProfessorStyle', plugin_dir_url(__FILE__) . 'build/index.css');

    // Translation for JavaScript
    wp_set_script_translations('featuredProfessorScript', 'featured-professor', plugin_dir_path(__FILE__) . '/languages');

    // Register block
    register_block_type('ourplugin/featured-professor', array(
      'render_callback' => [$this, 'renderCallback'],
      'editor_script' => 'featuredProfessorScript',
      'editor_style' => 'featuredProfessorStyle'
    ));
  }

  // Block rendering function
  function renderCallback($attributes) {
    if ($attributes['profId']) {
      wp_enqueue_style('featuredProfessorStyle');
      return generateProfessorHTML($attributes['profId']);
    } else {
      return NULL;
    }
  }

}

$featuredProfessor = new FeaturedProfessor();
