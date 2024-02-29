<?php

namespace WpRobot\Robo\Plugin\Commands\Artifact;

use Robo\Contract\VerbosityThresholdInterface;
use WpRobot\Robo\Plugin\Commands\BaseCommand;

/**
 * Class ArtifactCommands.
 *
 * Provides commands for building a deploy artifact.
 *
 * @package WpRobot\Robo\Plugin\Commands\Artifact
 */
class ArtifactCommands extends BaseCommand {


  /**
   * Creates a build artifact in the build directory.
   *
   * @param $environment
   *   The environment the build is targeted for.
   *
   * @command artifact:build
   *
   * @description Builds an artifact for deploy.
   */
  public function build($environment) {
    $deploy_directory = $this->getConfig()->get('deploy.dir');

    $this->botYell("Starting build for environment: $environment");

    $this->botSay("Installing composer dependencies.");

    $exit_code = $this->composerInstall($this->getRepoRoot());
    $this->botWarning("Composer install exit code: $exit_code");

    // Build storybook as well.
    if ($exit_code == 0) {
      $exit_code = $this->invokeCommand('frontend:setup');
    }

    // Execute frontend linting.
    if ($exit_code == 0) {
      $exit_code = $this->invokeCommand('frontend:lint:js');
    }

    if ($exit_code == 0) {
      $exit_code = $this->invokeCommand('frontend:lint:styles');
    }

    if ($exit_code == 0) {
      // Execute frontend unit tests.
      //$exit_code = $this->invokeCommand('frontend:tests:unit');
    }

    if ($exit_code == 0) {
      // Execute the frontend build first.
      $exit_code = $this->invokeCommand('frontend:build', [$environment]);
    }

    if ($exit_code == 0) {
      // Build storybook as well.
      $exit_code = $this->invokeCommand('frontend:storybook:build');
    }

    if ($exit_code == 0) {
      // Copy all build files.
      $exit_code = $this->copyFiles($environment);
    }

    if ($exit_code == 0) {
      // Run composer install against the deploy directory.
      $exit_code = $this->composerInstall($deploy_directory);
    }

    if ($exit_code == 0) {
      // Copy files after composer install.
      $exit_code = $this->afterComposerFileCopy($environment);
    }

    if ($exit_code == 0) {
      // Clean the build directory.
      $exit_code = $this->cleanArtifact();
    }

    if ($exit_code == 0) {
      $this->botYell("Build complete.");
    }

    return $exit_code;
  }

  /**
   * Deletes and re-creates the artifact build directory.
   *
   * @command artifact:wipe
   *
   * @description Deletes and re-creates the artifact build directory.
   */
  public function prepareDirectory() {
    $this->botSay('Preparing deploy directory.');

    $deploy_directory = $this->getConfig()->get('deploy.dir');

    $this->taskDeleteDir($deploy_directory)
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
      ->run();

    $this->taskFilesystemStack()
      ->mkdir($deploy_directory)
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
      ->stopOnFail()
      ->run();

    $this->botYell('Deploy directory ready.');
  }

  /**
   * Copy the necessary files for the build into the build directory.
   *
   * @param $environment
   *   The environment the build is targeted for.
   */
  protected function copyFiles($environment) {
    $this->botSay("Copying files to deploy directory.");

    $deploy_directory = $this->getConfig()->get('deploy.dir');

    // RSync directories.
    $this->botSay("RSyncing primary directories.");
    $this->rsyncDirectory("{$this->getRepoRoot()}/scripts", "$deploy_directory");
    $this->rsyncDirectory("{$this->getRepoRoot()}/web/private", "$deploy_directory/web");
    $this->rsyncDirectory("{$this->getRepoRoot()}/web/app/mu-plugins", "$deploy_directory/web/app");
    $this->rsyncDirectory("{$this->getRepoRoot()}/web/app/plugins", "$deploy_directory/web/app");
    $this->rsyncDirectory("{$this->getRepoRoot()}/web/app/themes", "$deploy_directory/web/app");
    $this->rsyncDirectory("{$this->getRepoRoot()}/web/app/config", "$deploy_directory/web/app");
    $this->rsyncDirectory("{$this->getRepoRoot()}/storybook", "$deploy_directory/web");

    // Pantheon specific deployment files/directories.
    if ($environment == 'pantheon') {
      $this->botSay("Pantheon environment detected.");

      // This is necessary for Pantheon. Config must be in this directory in order for config imports to work.
      $this->botSay("Create config folder symlink for Pantheon environment.");
      $this->taskFilesystemStack()
        ->symlink("../app/config", "$deploy_directory/web/private/config")
        ->run();

      // Ensure Pantheon has access to the scripts.
      $this->botSay("Create config folder symlink for Pantheon environment.");
      $this->taskFilesystemStack()
        ->symlink("../../scripts", "$deploy_directory/web/private/scripts")
        ->run();

      // Another Pantheon necessity. The uploads folder lives in a different location, so we need a symlink for uploads to work.
      // We do this only on deploy to not affect local development.
      $this->botSay("Create uploads folder symlink for Pantheon environment.");
      $this->taskFilesystemStack()
        ->symlink("../../../files", "$deploy_directory/web/app/uploads")
        ->run();
    }

    // Copy individual files.
    $this->botSay("Copying individual files.");
    $result = $this->taskFilesystemStack()
      ->stopOnFail(FALSE)
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
      ->copy("{$this->getRepoRoot()}/composer.json", "$deploy_directory/composer.json", TRUE)
      ->copy("{$this->getRepoRoot()}/composer.lock", "$deploy_directory/composer.lock", TRUE)
      ->copy("{$this->getRepoRoot()}/pantheon.yml", "$deploy_directory/pantheon.yml", TRUE)
      ->copy("{$this->getRepoRoot()}/pantheon.upstream.yml", "$deploy_directory/pantheon.upstream.yml", TRUE)
      ->copy("{$this->getRepoRoot()}/wp-cli.yml", "$deploy_directory/wp-cli.yml", TRUE)
      ->copy("{$this->getRepoRoot()}/web/index.php", "$deploy_directory/web/index.php", TRUE)
      ->copy("{$this->getRepoRoot()}/web/wp-config.php", "$deploy_directory/web/wp-config.php", TRUE)
      ->copy("{$this->getRepoRoot()}/web/favicon.ico", "$deploy_directory/web/favicon.ico", TRUE)
      ->copy("{$this->getRepoRoot()}/web/icon.svg", "$deploy_directory/web/icon.svg", TRUE)
      ->copy("{$this->getRepoRoot()}/web/apple-touch-icon.png", "$deploy_directory/web/apple-touch-icon.png", TRUE)
      ->copy("{$this->getRepoRoot()}/web/safari-pinned-tab.svg", "$deploy_directory/web/safari-pinned-tab.svg", TRUE)
      ->copy("{$this->getRepoRoot()}/config/application.php", "$deploy_directory/config/application.php", TRUE)
      ->copy("{$this->getRepoRoot()}/config/environments/overrides/$environment.php", "$deploy_directory/config/environments/overrides/$environment.php", TRUE)
      ->copy("{$this->getRepoRoot()}/config/environments/env/.env.$environment", "$deploy_directory/.env", TRUE)
      ->copy("{$this->getRepoRoot()}/config/deploy/.gitignore", "$deploy_directory/.gitignore", TRUE)
      ->copy("{$this->getRepoRoot()}/config/deploy/.gitignore-theme", "$deploy_directory/web/app/themes/wabe/.gitignore", TRUE)
      ->copy("{$this->getRepoRoot()}/web/app/ajax-handler.php", "$deploy_directory/web/app/ajax-handler.php", TRUE)
      ->copy("{$this->getRepoRoot()}/web/ed9f5457af76311e303ab5bdc46a4d4d.txt", "$deploy_directory/web/ed9f5457af76311e303ab5bdc46a4d4d.txt", TRUE)
      ->run();

    $this->botYell("File copy complete.");

    return  $result->getExitCode();
  }

  public function afterComposerFileCopy( $environment ) {
    $deploy_directory = $this->getConfig()->get('deploy.dir');

    // Copy individual files.
    $this->botSay("Copying individual files after composer install.");
    $result = $this->taskFilesystemStack()
      ->stopOnFail(FALSE)
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
      ->copy("{$this->getRepoRoot()}/config/deploy/object-cache.php", "$deploy_directory/web/wp/wp-content/object-cache.php", TRUE)
      ->run();

    $this->botYell("After composer install file copy complete.");

    return $result->getExitCode();
  }


  /**
   * Removes any unwanted files from the artifact after the build is complete.
   */
  protected function cleanArtifact() {
    $deploy_directory = $this->getConfig()->get('deploy.dir');

    $result = $this->taskDeleteDir([
      "$deploy_directory/web/wp/wp-content/themes/twentyten",
      "$deploy_directory/web/wp/wp-content/themes/twentyeleven",
      "$deploy_directory/web/wp/wp-content/themes/twentytwelve",
      "$deploy_directory/web/wp/wp-content/themes/twentythirteen",
      "$deploy_directory/web/wp/wp-content/themes/twentyfourteen",
      "$deploy_directory/web/wp/wp-content/themes/twentyfifteen",
      "$deploy_directory/web/wp/wp-content/themes/twentysixteen",
      "$deploy_directory/web/wp/wp-content/themes/twentyseventeen",
      "$deploy_directory/web/wp/wp-content/themes/twentyeighteen",
      "$deploy_directory/web/wp/wp-content/themes/twentynineteen",
      "$deploy_directory/web/wp/wp-content/themes/twentytwenty",
      //"$deploy_directory/web/wp/wp-content/themes/twentytwentyone",
    ])->run();

    $this->botYell("Artifact clean complete.");

    return $result->getExitCode();
  }

  protected function rsyncDirectory($from, $to) {
    $this->taskRsync()
      ->fromPath($from)
      ->toPath($to)
      ->option('links')
      ->exclude('node_modules')
      ->recursive()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
      ->progress()
      ->delete()
      ->run();
  }
}
