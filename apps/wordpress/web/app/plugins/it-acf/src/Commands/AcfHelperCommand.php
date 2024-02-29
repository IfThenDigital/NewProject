<?php

namespace IfThen\Acf\Commands;

use ACF_Local_JSON;
use WP_CLI;

/**
 * Class AcfHelperCommand.
 *
 * Provides import and export commands for ACF field groups. Leverages
 * ACF code for the import and export mechanisms.
 */
class AcfHelperCommand {

  /**
   * ACF JSON sync class/service.
   *
   * @var ACF_Local_JSON
   */
  protected $jsonSyncService;

  public function __construct() {
    $this->register();
    $this->jsonSyncService = new ACF_Local_JSON();
  }

  /**
   * Registers the command with WP CLI.
   *
   * @throws \Exception
   */
  public function register() {
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
      WP_CLI::add_command( 'it:acf', self::class );
    }
  }

  /**
   * Imports ACF configuration.
   *
   * ## EXAMPLES
   *
   *     wp it:acf import
   *
   */
  public function import() {
    // Get all current field groups.
    $current_field_groups = acf_get_field_groups();

    // Retrieve the json import/load paths from acf settings.
    $json_config_paths = acf_get_setting('load_json');

    // Initialize array to hold paths for all files to be loaded.
    $json_file_paths = [];

    // Loop through the paths and get a file path for all *.json files.
    foreach ($json_config_paths as $json_config_path) {
      foreach (glob($json_config_path . '/*.json') as $file_path) {
        $json_file_paths[] = $file_path;
      }
    }

    if (!empty($json_file_paths)) {
      WP_CLI::line("Field groups found for import. Starting import.");
    }

    // Loop through the .json file paths.
    foreach ($json_file_paths as $json_file_path) {
      // Get the field group JSON data.
      $field_group_json_data = json_decode(file_get_contents($json_file_path), TRUE);

      // Make sure we don't import an empty file.
      if(!$field_group_json_data || !is_array($field_group_json_data)) {
        continue;
      }

      // Search database for existing field group.
      $post = acf_get_field_group_post($field_group_json_data['key']);
      if( $post ) {
        $field_group_json_data['ID'] = $post->ID;
      }

      // This prevents a new file from being created from the import.
      acf_update_setting('json', false);

      // Import field group.
      acf_import_field_group($field_group_json_data);

      WP_CLI::line("Field group file $json_file_path imported.");

      // Lastly, remove this field from the list of current field groups.
      $current_field_groups = array_filter($current_field_groups, function($field_group) use($field_group_json_data) {
        // We want to remove only matches.
        if ($field_group['ID'] == $field_group_json_data['ID']) {
          return false;
        }

        return true;
      });
    }

    WP_CLI::line('Deleting any remaining field groups from the database not found in configuration.');

    // Any left over fields from the current group do not have a matching JSON file.
    // Delete those.
    if (!empty($current_field_groups)) {
      foreach ($current_field_groups as $field_group) {
        acf_delete_field_group($field_group['ID']);

        WP_CLI::line("Field group deleted. Field name: {$field_group['title']}");
      }
    }
    else {
      WP_CLI::line('No field groups found for deletion.');
    }


    WP_CLI::success('ACF JSON import complete!');
  }

  /**
   * Imports ACF configuration.
   *
   * ## EXAMPLES
   *
   *     wp it:acf export
   *
   */
  public function export() {
    // Get all field groups.
    $field_groups = acf_get_field_groups();

    // Loop through the field groups and export each one.
    foreach ($field_groups as $field_group) {
      WP_CLI::line(print_r($field_group, true));
      $this->jsonSyncService->update_field_group($field_group);
    }

    WP_CLI::success('ACF JSON export complete!');
  }

}