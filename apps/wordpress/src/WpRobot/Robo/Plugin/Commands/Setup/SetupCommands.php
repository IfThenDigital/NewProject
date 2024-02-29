<?php

namespace WpRobot\Robo\Plugin\Commands\Setup;

use Robo\Contract\VerbosityThresholdInterface;
use WpRobot\Robo\Plugin\Commands\BaseCommand;

/**
 * Class SetupCommands.
 *
 * @package WpRobot\Robo\Plugin\Commands\Setup
 */
class SetupCommands extends BaseCommand {

  /**
   * Performs setup tasks for Drupal VM local development environment.
   *
   * @command setup:local:drupalvm
   *
   * @throws \Exception
   */
  public function setupLocalDrupalvm() {
    $this->botSay('Performing setup tasks for Drupal VM.');

    $exit_code = $this->copyEnvFile('.env.drupalvm');

    if ($exit_code == 0) {
      $exit_code = $this->setupApache();
    }

    if ($exit_code == 0) {
      $exit_code = $this->invokeCommand('git:hooks:add');
    }

    return $exit_code;
  }

  /**
   * Performs any needed setup tasks for local development with a custom setup.
   *
   * @command setup:local:custom
   *
   * @throws \Exception
   */
  public function setupLocalCustom() {
    $this->botSay('Performing setup tasks for custom local.');

    $exit_code = $this->copyEnvFile('.env.local');

    if ($exit_code == 0) {
      $exit_code = $this->setupApache();
    }

    if ($exit_code == 0) {
      $exit_code = $this->invokeCommand('git:hooks:add');
    }

    return $exit_code;
  }

  /**
   * Performs any setup tasks for an Apache server environment.
   *
   * @command setup:apache
   */
  public function setupApache() {
    $this->botSay('Performing setup tasks for Apache.');

    $result = $this->taskFilesystemStack()
      ->stopOnFail()
      ->copy(
        $this->getRepoRoot() . "/config/server/apache/.htaccess",
        $this->getRepoRoot() . '/web/.htaccess'
      )
      ->run();

    return $result->getExitCode();
  }

  /**
   * Copies an environment file into the root directory.
   *
   * @param $env_file
   *   The name of the env file to copy.
   *
   * @throws \Exception
   */
  protected function copyEnvFile($env_file) {
    $result = $this->taskFilesystemStack()
      ->stopOnFail()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
      ->copy(
        $this->getRepoRoot() . "/config/environments/env/$env_file",
        $this->getRepoRoot() . '/.env'
      )
      ->run();

    return $result->getExitCode();
  }

}
