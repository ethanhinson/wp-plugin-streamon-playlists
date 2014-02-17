<?php

/**
 * Big array of taxonom configuration arrays
 */

$taxonomies = array();

$taxonomies['playlist_categories'] = array( 
    'labels' => array(), // Just use some defaults
    'hierarchical' => true, 
    'label' => 'Playlist Categories',
    'show_ui' => true,
    'query_var' => true,
    'rewrite' => array('slug' => 'playlist-categories'),
    'singular_label' => 'Category'
   );

$taxonomies['playlist_categories']['supports'] = array('streamon_playlists');

?>