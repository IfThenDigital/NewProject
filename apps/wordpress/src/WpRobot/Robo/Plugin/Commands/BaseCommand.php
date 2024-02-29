<?php

namespace WpRobot\Robo\Plugin\Commands;

use Robo\Tasks;
use WpRobot\Robo\Plugin\Config\WpRobotConfig;

/**
 * Class BaseCommand.
 *
 * A base command for all other Wordpress Robot commands. Provides common functionality and
 * settings.
 *
 * @package WpRobot\Robo\Plugin\Commands
 */
class BaseCommand extends Tasks {

  /**
   * The root of the project Git repo.
   *
   * @var string
   */
  protected $repoRoot = '';

  /**
   * The path to the theme directory.
   *
   * @var string
   */
  protected $themeDirectory = '';

  /**
   * Configuration for the robot commands.
   *
   * @var WpRobotConfig
   */
  protected $config = NULL;

  /**
   * Robo executable location.
   *
   * @var string
   */
  protected $robo = './vendor/bin/robo';

  /**
   * Outputs text to the screen.
   *
   * @param $text
   *   The text to write to output.
   */
  protected function botSay($text) {
    $format = "<fg=black;bg=white>%s</fg=black;bg=white>";
    $this->formattedOutput($text, 40, $format);
  }

  /**
   * Outputs text to the screen with emphasis.
   *
   * @param $text
   *   The text to write to output.
   */
  protected function botYell($text) {
    $format = "<fg=black;bg=green>%s</fg=black;bg=green>";
    $this->formattedOutput($text, 40, $format);
  }

  /**
   * Outputs warning text to the screen.
   *
   * @param $text
   *   The text to write to output.
   */
  protected function botWarning($text) {
    $format = "<fg=black;bg=yellow>%s</fg=black;bg=yellow>";
    $this->formattedOutput($text, 40, $format);
  }

  /**
   * Outputs error text to the screen.
   *
   * @param $text
   *   The text to write to output.
   */
  protected function botError($text) {
    $format = "<fg=white;bg=red;options=bold>%s</fg=white;bg=red;options=bold>";
    $this->formattedOutput($text, 40, $format);
  }

  /**
   * Determines and returns the root of the Git repo.
   *
   * @return string
   *   The absolute path to the root of the project.
   */
  protected function getRepoRoot() {

    if (empty($this->repoRoot)) {

      $result = $this->taskExec('git rev-parse --show-toplevel')
        ->silent(true)
        ->run();

      $this->repoRoot = $result->getMessage();
    }

    return $this->repoRoot;
  }

  /**
   * Determines the theme directory path.
   *
   * @return string
   *   The absolute path to the theme directory.
   */
  protected function getThemeDirectory() {

    if (empty($this->themeDirectory)) {
      $this->themeDirectory = $this->getRepoRoot() . $this->getConfig()->get('wordpress.theme.dir');
    }

    return $this->themeDirectory;
  }

  /**
   * Returns the current application config.
   *
   * @return WpRobotConfig
   */
  protected function getConfig() {
    if ($this->config == NULL) {
      $this->config = new WpRobotConfig($this->getRepoRoot());
    }

    return $this->config;
  }

  /**
   * Allows for invoking other application commands.
   *
   * @param $command
   *   The command to execute.
   * @param array $args
   *   Any command arguments to pass along.
   *
   * @return int
   *   Exit code. 1 or 0.
   *
   * @throws \Robo\Exception\TaskException
   */
  protected function invokeCommand($command, $args = []) {
    if (!empty($args)) {
      foreach ($args as $arg) {
        $command .= " $arg";
      }
    }

    $result = $this->taskExecStack()
      ->stopOnFail()
      ->dir($this->getRepoRoot())
      ->exec("./wpr $command")
      ->run();

    return $result->getExitCode();
  }

  /**
   * Executes composer install in the build directory.
   *
   * @param $directory
   *   The directory to run `composer install` against.
   */
  protected function composerInstall($directory) {
    $result = $this->taskComposerInstall()
      ->dir($directory)
      ->run();

    return $result->getExitCode();
  }

}