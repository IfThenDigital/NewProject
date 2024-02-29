<?php

namespace WpRobot\Robo\Plugin\Commands\Frontend;

use Robo\Exception\TaskException;
use WpRobot\Robo\Plugin\Commands\BaseCommand;

/**
 * Class FrontendCommands.
 *
 * @package WpRobot\Robo\Plugin\Commands\Frontend
 */
class FrontendCommands extends BaseCommand {

  /**
   * Runs any necessary setup commands for the theme.
   *
   * @command frontend:setup
   *
   * @throws \Robo\Exception\TaskException
   */
  public function setup() {
    $theme_directory = $this->getThemeDirectory();

    $this->taskExecStack()
      ->stopOnFail()
      ->dir($theme_directory)
      ->exec('npm install')
      ->exec('npm rebuild node-sass')
      ->run();
  }

  /**
   * Refreshes the theme directory to start anew.
   *
   * @command frontend:refresh
   *
   * @throws \Robo\Exception\TaskException
   */
  public function refresh() {
    $theme_directory = $this->getThemeDirectory();

    $this->taskExecStack()
      ->dir($theme_directory)
      ->exec('rm -r node_modules')
      ->run();

    $this->setup();
  }

  /**
   * Executes the frontend build tasks.
   *
   * @param $environment
   *   The environment the build is targeted for.
   *
   * @command frontend:build
   *
   * @description Execute frontend build tasks.
   *
   * @throws \Robo\Exception\TaskException
   */
  public function build($environment) {
    $this->botSay("Running frontend build for environment: $environment");

    $theme_directory = $this->getThemeDirectory();

    $npm_command = 'npm run serve';

    if ($environment == 'pantheon') {
      $npm_command = 'npm run build';
    }

    $this->taskExecStack()
      ->stopOnFail()
      ->dir($theme_directory)
      ->exec($npm_command)
      ->run();

    $this->botSay('Frontend build complete.');
  }

  /**
   * Runs the storybook instance for use locally.
   *
   * @command frontend:storybook
   *
   * @throws \Robo\Exception\TaskException
   */
  public function storybook() {
    $theme_directory = $this->getThemeDirectory();

    $this->taskExecStack()
      ->stopOnFail()
      ->dir($theme_directory)
      ->exec('npm run storybook')
      ->run();
  }

  /**
   * Builds the storybook instance for use.
   *
   * @command frontend:storybook:build
   *
   * @throws \Robo\Exception\TaskException
   */
  public function storybookBuild() {
    $theme_directory = $this->getThemeDirectory();

    $this->taskExecStack()
      ->stopOnFail()
      ->dir($theme_directory)
      ->exec('npm run build-storybook')
      ->run();
  }

  /**
   * Creates a new Vue component.
   *
   * @command frontend:component:new
   *
   * @throws \Robo\Exception\TaskException
   */
  public function newComponent() {
    $theme_directory = $this->getThemeDirectory();

    $result = $this->taskExecStack()
      ->stopOnFail()
      ->dir("$theme_directory/src/components")
      ->exec('npm run new-component.js')
      ->run();

    return $result->getExitCode();
  }

  /**
   * Executes JS linting for the theme.
   *
   * @command frontend:lint:js
   *
   * @throws \Robo\Exception\TaskException
   */
  public function lintJS() {
    $theme_directory = $this->getThemeDirectory();

    $result = $this->taskExecStack()
      ->stopOnFail()
      ->dir($theme_directory)
      ->exec('npm run lint')
      ->run();

    return $result->getExitCode();
  }

  /**
   * Executes CSS linting for the theme.
   *
   * @command frontend:lint:styles
   *
   * @throws \Robo\Exception\TaskException
   */
  public function lintStyles() {
    $theme_directory = $this->getThemeDirectory();

    $result = $this->taskExecStack()
      ->stopOnFail()
      ->dir($theme_directory)
      ->exec('npm run lint-styles')
      ->run();

    return $result->getExitCode();
  }

  /**
   * Runs unit tests for the theme.
   *
   * @command frontend:tests:unit
   *
   * @throws \Robo\Exception\TaskException
   */
  public function runUnitTests() {
    $theme_directory = $this->getThemeDirectory();

    $result = $this->taskExecStack()
      ->stopOnFail()
      ->dir($theme_directory)
      ->exec('npm run test:unit')
      ->run();

    return $result->getExitCode();
  }

  /**
   * Runs end to end tests for the theme.
   *
   * @command frontend:tests:e2e
   *
   * @throws \Robo\Exception\TaskException
   */
  public function runEndToEndTests() {
    $theme_directory = $this->getThemeDirectory();

    $result = $this->taskExecStack()
      ->stopOnFail()
      ->dir($theme_directory)
      ->exec('npm run test:e2e')
      ->run();

    return $result->getExitCode();
  }

}
