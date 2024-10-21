<?php
/*
Plugin Name: Custom Popup with Gravity Form
Description: A simple plugin to add a popup triggered by a class with a Gravity Form inside.
Version: 1.0
Author: Andrew Shelton
*/

// Enqueue styles for the popup
function custom_popup_form_enqueue_styles() {
  wp_enqueue_style('custom-popup-style', plugin_dir_url(__FILE__) . 'css/stylesheet.css');
}
add_action('wp_enqueue_scripts', 'custom_popup_form_enqueue_styles');

// Add popup HTML to the footer of every page
function custom_popup_add_to_footer() {
  // Get the user-defined Gravity Form ID and text from settings
  $gravity_form_id = get_option('custom_popup_gravity_form_id', 1); // Default to form ID 1 if not set
  $popup_title = get_option('custom_popup_title', 'test');
  $popup_content = get_option('custom_popup_content', 'test');
  $popup_disclaimer = get_option('custom_popup_disclaimer', 'test');

  ?>
  <!-- Popup container (automatically inserted into the footer) -->

  <!-- Set to pull in divi -->



  <div id="popupForm" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); padding:20px; z-index:9999; border-radius:8px;">
    <span id="popupClose" style="cursor:pointer; position:absolute; top:10px; right:10px; font-size:20px;">&times;</span>
    
    <div class="et_pb_row et_pb_row_8 align_tems_cmn et_pb_equal_columns et_pb_gutters1 et_had_animation">
      <div class="et_pb_column et_pb_column_2_3 et_pb_column_13 et_pb_css_mix_blend_mode_passthrough">
        <div class="et_pb_module et_pb_blurb et_pb_blurb_0 cmn_blurb et_pb_text_align_left et_pb_blurb_position_left et_pb_bg_layout_light">
          <div class="et_pb_blurb_content">
            <div class="et_pb_main_blurb_image">
              <span class="et_pb_image_wrap et_pb_only_image_mode_wrap">
                <img loading="lazy" decoding="async" width="48" height="49" src="" alt="download icon" class="et-waypoint et_pb_animation_top">
              </span>
            </div>
            <div class="et_pb_blurb_container">
              <h2 class="et_pb_module_header"><span><?php echo esc_html($popup_title); ?></span></h2>
            </div>
          </div>
        </div>
        
        <div class="et_pb_module et_pb_text et_pb_text_12 font_fix et_pb_text_align_left et_pb_bg_layout_light">
          <div class="et_pb_text_inner"><p><?php echo esc_html($popup_content); ?></p></div>
        </div>
      </div>
      
      <div class="et_pb_column et_pb_column_1_3 et_pb_column_14 et_pb_css_mix_blend_mode_passthrough et-last-child">
        <?php echo do_shortcode('[gravityform id="' . esc_attr($gravity_form_id) . '" title="false" description="false" ajax="true"]'); ?>
      </div>
    </div>
    
    <div class="et_pb_text_inner">
      <p><?php echo esc_html($popup_disclaimer); ?></p>
    </div>
  </div>

  <div id="popupOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0, 0, 0, 0.5); z-index:9998;"></div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var triggerElements = document.querySelectorAll('.trigger-popup');

      if (triggerElements.length) {
        triggerElements.forEach(function(trigger) {
          trigger.addEventListener('click', function() {
            document.getElementById('popupForm').style.display = 'block';
            document.getElementById('popupOverlay').style.display = 'block';
          });
        });
      }

      document.getElementById('popupClose').addEventListener('click', function() {
        document.getElementById('popupForm').style.display = 'none';
        document.getElementById('popupOverlay').style.display = 'none';
      });

      document.getElementById('popupOverlay').addEventListener('click', function() {
        document.getElementById('popupForm').style.display = 'none';
        document.getElementById('popupOverlay').style.display = 'none';
      });
    });
  </script>
  <?php
}
add_action('wp_footer', 'custom_popup_add_to_footer');

// Add settings menu item
function custom_popup_add_settings_menu() {
  add_options_page(
    'Custom Popup Settings',          // Page title
    'Custom Popup Settings',          // Menu title
    'manage_options',                 // Capability required
    'custom-popup-settings',          // Menu slug
    'custom_popup_settings_page'      // Function to display settings page
  );
}
add_action('admin_menu', 'custom_popup_add_settings_menu');

// Display settings page
function custom_popup_settings_page() {
  ?>
  <div class="wrap">
    <h1>Custom Popup Settings</h1>
    <form method="post" action="options.php">
      <?php
      settings_fields('custom_popup_settings_group'); // Same group name as in register_setting
      do_settings_sections('custom-popup-settings');  // Page slug
      submit_button();
      ?>
    </form>
  </div>
  <?php
}

// Register settings, section, and field
function custom_popup_register_settings() {
  register_setting('custom_popup_settings_group', 'custom_popup_gravity_form_id');
  register_setting('custom_popup_settings_group', 'custom_popup_title');
  register_setting('custom_popup_settings_group', 'custom_popup_content');
  register_setting('custom_popup_settings_group', 'custom_popup_disclaimer');

  add_settings_section('custom_popup_settings_section', 'Popup Settings', null, 'custom-popup-settings');

  add_settings_field('custom_popup_gravity_form_id', 'Gravity Form ID', 'custom_popup_gravity_form_id_field', 'custom-popup-settings', 'custom_popup_settings_section');
  add_settings_field('custom_popup_title', 'Popup Title', 'custom_popup_title_field', 'custom-popup-settings', 'custom_popup_settings_section');
  add_settings_field('custom_popup_content', 'Popup Content', 'custom_popup_content_field', 'custom-popup-settings', 'custom_popup_settings_section');
  add_settings_field('custom_popup_disclaimer', 'Popup Disclaimer', 'custom_popup_disclaimer_field', 'custom-popup-settings', 'custom_popup_settings_section');
}
add_action('admin_init', 'custom_popup_register_settings');

// Input field for Gravity Form ID
function custom_popup_gravity_form_id_field() {
  $gravity_form_id = get_option('custom_popup_gravity_form_id', 1);
  echo '<input type="text" name="custom_popup_gravity_form_id" value="' . esc_attr($gravity_form_id) . '" />';
}

// Input field for Popup Title
function custom_popup_title_field() {
  $popup_title = get_option('custom_popup_title', 'Subscribe to our newsletter');
  echo '<input type="text" name="custom_popup_title" value="' . esc_attr($popup_title) . '" />';
}

// Input field for Popup Content
function custom_popup_content_field() {
  $popup_content = get_option('custom_popup_content', 'Subscribe to our newsletter for free by filling out the simple form below!');
  echo '<textarea name="custom_popup_content" rows="4" cols="50">' . esc_textarea($popup_content) . '</textarea>';
}

// Input field for Popup Disclaimer
function custom_popup_disclaimer_field() {
  $popup_disclaimer = get_option('custom_popup_disclaimer', 'NOTE: By clicking the submit button, you are agreeing to receive email updates. You can unsubscribe at any time.');
  echo '<textarea name="custom_popup_disclaimer" rows="4" cols="50">' . esc_textarea($popup_disclaimer) . '</textarea>';
}
