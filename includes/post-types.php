<?php

/*--------------------------------------------*
 * Custom Post Types
 *--------------------------------------------*/

$post_types = array();


$post_types['streamon_playlists'] = array(	
    'label' => 'Playlists',
    'description' => '',
    'public' => true,'show_ui' => true,
    'show_in_menu' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'rewrite' => array('slug' => 'playlists'),
    'query_var' => true,
    'exclude_from_search' => false,
    'supports' => array('title', 'custom-fields', 'editor'),
    'labels' => array (
      'name' => 'Playlists',
      'singular_name' => 'Playlist',
      'menu_name' => 'Playlists',
      'add_new' => 'Add Playlist',
      'add_new_item' => 'Add New Playlist',
      'edit' => 'Edit',
      'edit_item' => 'Edit Playlist',
      'new_item' => 'New Playlist',
      'view' => 'View Playlist',
      'view_item' => 'View Playlist',
      'search_items' => 'Search Playlists',
      'not_found' => 'No Playlists Found',
      'not_found_in_trash' => 'No Playlists Found in Trash',
      'parent' => 'Parent Playlist',
    ),
);
?>
