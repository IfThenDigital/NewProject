<?php

namespace IfThen\Utility\WPCFM;

class WPCFMSettings {

  protected $configDirectory;

  public function __construct() {
    $this->configDirectory = WP_CONTENT_DIR . '/config';

    add_filter( 'wpcfm_config_dir', [ $this, 'changeWPCFMDirectory'] );
  }

  public function changeWPCFMDirectory($configDirectory) {
    return $this->configDirectory;
  }

  public function setConfigDirectory($configDirectory) {
    $this->configDirectory = $configDirectory;
  }

}
