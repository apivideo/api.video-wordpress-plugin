<?php 
// api.video register main page
add_action('admin_menu', 'register_library_subpage');
function register_library_subpage() {
  add_menu_page(
    __('api.video', 'api.video'),
    __('api.video', 'api.video'),
    'manage_options',
    'api.video-library',
    'av_library',
    'dashicons-format-video',
    10
  );
}

// api.video Library page
add_action('admin_menu', 'library_submenu_page');
function library_submenu_page() {
  add_submenu_page(
    'api.video-library',
    '',
    'Library',
    'manage_options',
    'api.video-library',
    'av_library'
  ); 
}

// api.video Add New page
add_action('admin_menu', 'register_addnew_subpage');
function register_addnew_subpage() {
  add_submenu_page(
    'api.video-library',
    'Add New',
    'Add New',
    'manage_options',
    'add-new-video',
    'av_add_new_video'
  );
}

// api.video settings page
add_action('admin_menu', 'register_addnew_subpagee');
function register_addnew_subpagee() {
  add_submenu_page(
    'api.video-library',
    'Settings',
    'Settings',
    'manage_options',
    'settings-api-video',
    'api_settings_page'
  );
}
?>
