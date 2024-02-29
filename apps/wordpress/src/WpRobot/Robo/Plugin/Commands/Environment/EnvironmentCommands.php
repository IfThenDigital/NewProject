<?php


namespace WpRobot\Robo\Plugin\Commands\Environment;

use Robo\Exception\TaskException;
use WpRobot\Robo\Plugin\Commands\BaseCommand;

class EnvironmentCommands extends BaseCommand {

  /**
   * Refreshes the local environment.
   *
   * @command env:refresh
   */
  public function refresh() {
    // Composer install.
    $this->composerInstall($this->getRepoRoot());

    // Frontend build.
    $this->invokeCommand('frontend:build', ['local']);

    // Build storybook.
    $this->invokeCommand('frontend:storybook:build');
  }

  /**
   * Refreshes the local Lando environment. Includes syncing with the remove environment.
   *
   * @command env:lando:refresh:full
   *
   * @param string $remote_environment
   *   The remote environment to pull data and files from during the refresh.
   *
   * @throws TaskException
   */
  public function refreshLando($remote_environment = 'dev') {
    $this->taskExecStack()
      ->dir($this->getRepoRoot())
      ->exec("lando pull --code=none --files=none --database={$remote_environment}")
      ->run();

    $this->taskExecStack()
      ->dir($this->getRepoRoot())
      ->exec('lando ssh -c "rm -f /tmp/files.tar.gz"')
      ->exec("lando terminus backup:create atlantahistory.{$remote_environment} --element=files")
      ->exec("lando terminus backup:get atlantahistory.{$remote_environment} --element=files --to=/tmp/files.tar.gz")
      ->exec('lando ssh -c "tar -xzvf /tmp/files.tar.gz -C /app/web/app/uploads --strip-components 1"')
      ->exec('lando ssh -c "rm -f /tmp/files.tar.gz"')
      ->run();

    $this->refreshLandoLite();
  }

  /**
   * Refreshes the local Lando environment. Does not sync with the remote environment.
   *
   * @command env:lando:refresh
   *
   * @throws TaskException
   */
  public function refreshLandoLite() {
    // Composer install.
    $this->composerInstall($this->getRepoRoot());

    // Import configuration.
    $this->taskExecStack()
      ->dir($this->getRepoRoot())
      ->exec('lando wp config pull all')
      ->exec('lando wp it:acf import')
      ->exec('lando wp cache flush')
      ->exec('lando wp rewrite flush')
      ->run();

    // Frontend build.
    $this->invokeCommand('frontend:setup');

    // Frontend build.
    $this->invokeCommand('frontend:build', ['local']);

    // Build storybook.
    $this->invokeCommand('frontend:storybook:build');
  }

  /**
   * Refreshes the local Vagrant environment. Does not sync with the remote environment.
   *
   * @command env:vm:refresh
   *
   * @throws TaskException
   */
  public function refreshVmLite() {
    // Composer install.
    $this->composerInstall($this->getRepoRoot());

    // Import configuration.
    $this->taskExecStack()
      ->dir($this->getRepoRoot())
      ->exec('wp config pull all')
      ->exec('wp it:acf import')
      ->exec('wp cache flush')
      ->exec('wp rewrite flush')
      ->run();

    // Frontend build.
    $this->invokeCommand('frontend:setup');

    // Frontend build.
    $this->invokeCommand('frontend:build', ['local']);

    // Build storybook.
    $this->invokeCommand('frontend:storybook:build');
  }

}