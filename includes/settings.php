<?php


class StreamOnSettings {
  public $section = 'streamon_auth';
  public $page_path = 'options-general.php?page=streamon_auth';
    
  public function __construct() {      
    add_action('admin_init', array(&$this, 'admin_init'));
    add_action('admin_menu', array(&$this, 'add_menu'));
  }
  
  public function admin_init() {
    add_settings_section(
      'streamon_auth', 
      'StreamOn API', 
      '', 
      $this->section
    );

    register_setting('streamon_auth', 'streamon_user');  
    add_settings_field(
      'streamon_user', 
      'Username', 
      array(&$this, 'field_streamon_user'), 
      $this->section, 'streamon_auth'
    );


    register_setting('streamon_auth', 'streamon_api_pass');
    add_settings_field(
      'streamon_api_pass', 
      'API Password', 
      array(&$this, 'field_streamon_pass'), 
      $this->section, 'streamon_auth'
    );
   
    register_setting('streamon_auth', 'streamon_api_url');
    add_settings_field(
      'streamon_api_url', 
      'API Endpoint', 
      array(&$this, 'field_streamon_url'), 
      $this->section, 'streamon_auth'
    );
 }
  
 public function add_menu() {
    global $stream_on;
    // Add a page to manage this plugin's settings
    add_options_page(
	'StreamOn', 
	'StreamOn API Settings', 
	'manage_options', 
	$this->section, 
	array(&$this, 'settings_page')
    );
    // Utils for updating/deleting
    if(isset($_GET['delete_all'])) {
      $stream_on->delete_all();
    }
    if(isset($_GET['update'])) {
      $stream_on->update();
    }
  }  
  
  public function settings_page() {
    ?><div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div>
	<h2>StreamOn API Settings</h2>
	<form method="post" action="options.php"> 

	    <?php @settings_fields($this->section); ?>
	    <table class="form-table">
		<?php @do_settings_fields($this->section, 'streamon_auth'); ?>
	    </table>

	    <?php @submit_button(); ?>

	</form>
	<h2>StreamOn API WordPress Utils</h2>
	<table class="form-table">
	  <tbody>
	    <tr>
	      <th scope="row">
		Refresh StreamOn Playlists
	      </th>
	      <td>
		<a class="button button-secondary" href="<?php print $this->page_path; ?>&update=1">Refresh API Data</a>
	      </td>
	    </tr>	    
	    <tr>
	      <th scope="row">
		Delete all StreamOn Playlists
	      </th>
	      <td>
		<a class="button button-secondary" href="<?php print $this->page_path; ?>&delete_all=1">Delete API Data</a>
		<br />
		<small style="color:#f30000">Caution: This action cannot be undone</small>
	      </td>
	    </tr>
	  </tbody>
	</table>
    </div><?php
  }

  public function field_streamon_user() {
    $setting = get_option('streamon_user');
    print '<input type="text" name="streamon_user" value="'. $setting .'" />';
  }
  
  public function field_streamon_pass() {
    $setting = get_option('streamon_api_pass');
    print '<input type="password" name="streamon_api_pass" value="'. $setting .'" />';
  }
  
  public function field_streamon_url() {
    $setting = get_option('streamon_api_url');
    print '<input type="text" name="streamon_api_url" value="'. $setting .'" />';
  }
  
}
$streamon_settings = new StreamOnSettings();
?>