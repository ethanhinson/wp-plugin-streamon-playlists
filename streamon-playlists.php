<?php

/*
  Plugin Name: StreamOn API Playlists
  Plugin URI: http://www.bluetent.com/
  Description: A simple plugin to maintain StreamOn API playlists
  Version: 0.1a
  Author: Ethan Hinson (ethan@bluetent.com)
  Author URI: http://www.bluetent.com/
  License: GPL2

  Copyright 2014  Ethan Hinson  (email : ethan@bluetent.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


global $stream_on;

class StreamOn {

  public function __construct() {
    // Load plugin text domain
    add_action('init', array($this, 'plugin_textdomain'));
    //Register post types
    add_action('init', array($this, 'register_post_types'), 100);
    //Register taxonomies
    add_action('init', array($this, 'register_taxonomies'), 100);
    // Add settings
    require_once('includes/settings.php');
    // Add CRON
    if (!wp_next_scheduled('update_streamon_playlists')) {
      wp_schedule_event(time(), 'twicedaily', 'update_streamon_playlists');
    }
    add_action('update_streamon_playlists', array($this, 'update'));
  }

  /**
   * Loads the plugin text domain for translation
   */
  public function plugin_textdomain() {
    $domain = 'streamon-playlists';
    $locale = apply_filters('plugin_locale', get_locale(), $domain);
    load_textdomain($domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo');
    load_plugin_textdomain($domain, FALSE, dirname(plugin_basename(__FILE__)) . '/lang/');
  }

  /**
   * Register Custom Post Types
   */
  public function register_post_types() {
    //Require Extra files
    require_once( 'includes/post-types.php' );
    //Loops the $post_types var and register
    foreach ($post_types as $type_slug => $args) {
      register_post_type($type_slug, $args);
    }
  }

  /**
   * Register Custom Taxonomies
   */
  public function register_taxonomies() {
    //Require Extra files
    require_once( 'includes/taxonomies.php' );
    //Loops the $taxonomies var and register
    foreach ($taxonomies as $tax_slug => $args) {
      register_taxonomy($tax_slug, $taxonomies[$tax_slug]['supports'], $args);
    }
  }

  /**
   * Update Playlists
   */
  public function update() {
    $user = get_option('streamon_user');
    $pass = get_option('streamon_api_pass');
    $endpoint = get_option('streamon_api_url');
    // If any of the authentication params are empty...just return
    if(empty($user) || empty($pass) || empty($endpoint)) {
      return;
    } 
    $url = 'http://'. $user .':' . $pass . '@' . $endpoint . '/api/v1/playlist/?format=json&permanent=true';
    // Let processing run for one minute.
    $response = wp_remote_get($url, array('timeout' => 60000));
    $local_items = $this->local_items();
    if (!is_wp_error($response) && isset($response['response']['code']) && 200 === $response['response']['code']) {
      $body = wp_remote_retrieve_body($response);
      $result = json_decode($body);
      foreach ($result->objects as $item) {
	$post = array(
	    'post_title' => $item->name,
	    'post_content' => $item->description,
	    'post_type' => 'streamon_playlists',
	    'post_status' => 'publish',
	    'tax_input' => array('playlist_categories' => $item->categories), // This needs work for sure for sure.
	);
	if (array_key_exists($item->id, $local_items)) {
	  $post['ID'] = $local_items[$item->id]['pid'];
	  $last_updated = $item->timestamp_updated;
	}

	if (!isset($post['ID']) || $last_updated > $local[$item->id]['updated']) {
	  $pid = wp_insert_post($post);
	  if ($pid) {
	    update_post_meta($pid, 'streamon_id', $item->id);
	    update_post_meta($pid, 'download_url', $item->download_url);
	    update_post_meta($pid, 'duration', $item->duration);
	    update_post_meta($pid, 'player_url', $item->player_url);
	    update_post_meta($pid, 'short_url', $item->short_url);
	    update_post_meta($pid, 'timestamp_updated', $item->timestamp_updated);
	  }
	}
      }
    }
  }

  /**
   * Returns all local playlists keyed by their streamon id. Contains other update info.
   * @return array an array keyed by streamon_id
   */
  protected function local_items() {
    $local_items = new WP_Query(array('post_type' => 'streamon_playlists', 'posts_per_page' => -1, 'post_status' => 'any'));
    $return = array();
    foreach ($local_items->posts as $item) {
      $pid = $item->ID;
      $sid = get_post_meta($pid, 'streamon_id', TRUE);
      $return[$sid] = array(
	  'pid' => $pid,
	  'updated' => get_post_meta($pid, 'timestamp_updated', TRUE)
      );
    }
    return $return;
  }

  /**
   * Purely a debug utility
   */
  public function delete_all() {
    $local_items = new WP_Query(array('post_type' => 'streamon_playlists', 'posts_per_page' => -1));
    foreach ($local_items->posts as $i) {
      wp_delete_post($i->ID, TRUE);
    }
  }
}

$stream_on = new StreamOn();
?>
