<?php
/*
Plugin Name: WP-CFM Multi-environment
Description: Enables configuration management for multiple environments with WP-CFM.
*/

// If this file is called directly, abort.
if (!defined( 'WPINC' )) {
  die;
}

// Disable the multi environment configuration management.
add_filter( 'wpcfm_multi_env', function() {
  return [];
} );