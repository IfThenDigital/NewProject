<?php


namespace WpRobot\Robo\Plugin\Commands\Tests;


use WpRobot\Robo\Plugin\Commands\BaseCommand;

class PHPTestsCommands extends BaseCommand {

  /**
   * Executes PHP unit tests
   *
   * @command php:tests:unit
   *
   * @return int
   * @throws \Robo\Exception\TaskException
   */
  public function runPluginTests() {
    $repo_root = $this->getRepoRoot();
    $plugins_directory = "web/app/plugins/";
    $plugins_with_tests = $this->getConfig()->get('tests.plugins');

    $exit_code = 0;
    $failed_tests = false;
    foreach ($plugins_with_tests as $plugin_dir) {
      $plugin_dir_path = "{$plugins_directory}{$plugin_dir}/tests";

      $result = $this->taskExecStack()
        ->dir($repo_root)
        ->exec("vendor/bin/phpunit $plugin_dir_path/.")
        ->run();

      if ($result->getExitCode() != 0) {
        $failed_tests = true;
      }
    }

    // A test failed. Return 1 for failure.
    if ($failed_tests) {
      return 1;
    }

    // Tests passed. Zero for success.
    return 0;
  }

}
