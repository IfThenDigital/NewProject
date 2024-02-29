<?php


namespace WpRobot\Robo\Plugin\Config;


use Consolidation\Config\Loader\ConfigProcessor;
use Consolidation\Config\Loader\YamlConfigLoader;
use Robo\Config\Config;

class WpRobotConfig {
  protected $config;

  public function __construct($repoRoot) {
    $this->config = new Config();

    $yaml_loader = new YamlConfigLoader();
    $config_processor = new ConfigProcessor();

    // Load default configuration, then the application configuration.
    $config_processor->extend($yaml_loader->load($repoRoot . '/src/Robo/default.config.yml'));
    $config_processor->extend($yaml_loader->load($repoRoot . '/config/wp_robot/config.yml'));

    $this->config->import($config_processor->export());
  }

  public function get($key) {
    return $this->config->get($key);
  }

  public function getConfigArray() {
    return $this->config->export();
  }
}