<?php

namespace WpRobot\Robo\Plugin\Commands\Deploy;

use Robo\Contract\VerbosityThresholdInterface;
use WpRobot\Robo\Plugin\Commands\BaseCommand;

class DeployCommands extends BaseCommand {

  /**
   * Builds and deploys the application to a given branch.
   *
   * @param $branch_name
   *   Branch name to deploy to.
   * @param $opts
   *   Options for this command.
   *
   * @throws \Exception
   *
   * @command deploy:branch
   *
   * @description Builds and deploys the application to a given branch.
   */
  public function deployToBranch($branch_name, $opts = ['dry-run' => false]) {
    // For now, hard code the pantheon environment.
    $environment = 'pantheon';

    $this->botSay("Beginning deploy of $branch_name branch to $environment environment.");

    $exit_code = $this->invokeCommand('artifact:wipe');

    if ($exit_code == 0) {
      $exit_code = $this->gitPrepTasks($branch_name);
    }

    if ($exit_code == 0) {
      $exit_code = $this->invokeCommand('artifact:build', [$environment]);
    }

    if ($exit_code == 0) {
      $exit_code = $this->gitMergeUpstream($branch_name);
    }

    if ($exit_code == 0) {
      $exit_code = $this->gitCommit($branch_name, $environment);

      // If this isn't a dry run, push the changes.
      if ($exit_code == 0 && !$opts['dry-run']) {
        $exit_code = $this->gitPush($branch_name, $environment);
      }
      else {
        $this->botSay('Dry run deployment. No changes will be pushed to the repo.');
      }

      $this->botYell('Deploy complete!');
    }
    else {
      $this->botError("An error occurred during the build process. Deploy failed.");
    }

    return $exit_code;
  }

  /**
   * Used for the initial deploy to a Pantheon environment. Builds and deploys application to a given branch.
   *
   * @param $branch_name
   *   Branch name to deploy to.
   * @param bool[] $opts
   *   Optional arguments for the command.
   *
   * @throws \Robo\Exception\TaskException
   *
   * @command deploy:initial
   */
  public function deployToPantheonInitial($branch_name, $opts = ['dry-run' => false]) {
    // For now, hard code the pantheon environment.
    $environment = 'pantheon';

    $this->botYell("Beginning initial deploy of $branch_name branch to $environment environment.");

    $this->invokeCommand('artifact:wipe');
    $this->gitPrepTasks($branch_name);
    $exit_code = $this->invokeCommand('artifact:build', [$environment]);

    if ($exit_code == 0) {
      $this->gitCommit($branch_name, $environment);

      // If this isn't a dry run, push the changes.
      if (!$opts['dry-run']) {
        $this->gitPush($branch_name, $environment);
      }
      else {
        $this->botWarning('Dry run deployment. No changes will be pushed to the repo.');
      }

      $this->botYell('Deploy complete!');
    }
    else {
      $this->botError("An error occurred during the build process. Deploy failed.");
    }

    return $exit_code;
  }

  /**
   * Prepares the deploy directory for a Git deploy.
   *
   * @param $branch_name
   *   Branch name to deploy to.
   *
   * @throws \Robo\Exception\TaskException
   */
  protected function gitPrepTasks($branch_name) {
    $this->botSay("Initializing the Git repo in the deploy directory.");

    $deploy_directory = $this->getConfig()->get('deploy.dir');

    $remote_url = $this->getConfig()->get('deploy.remote');
    $remote_name = md5($remote_url);

    // Initialize git in the deploy directory.
    $exit_code = $this->taskExecStack()
      ->dir($deploy_directory)
      ->stopOnFail()
      ->exec("git init")
      ->exec("git config --local core.excludesfile false")
      ->exec("git config --local core.fileMode true")
      ->run()
      ->getExitCode();

    if ($exit_code == 0) {
      // Add the deploy git remote.
      $exit_code = $this->taskExecStack()
        ->stopOnFail()
        ->dir($deploy_directory)
        ->exec("git remote add $remote_name $remote_url")
        ->exec("git fetch ")
        ->run()
        ->getExitCode();
    }

    if ($exit_code == 0) {
      // Checkout the deploy branch.
      $exit_code = $this->taskExecStack()
        ->dir($deploy_directory)
        ->stopOnFail(FALSE)
        ->exec("git fetch $remote_name $branch_name --depth=1")
        ->exec("git checkout -b $branch_name")
        ->exec("git branch --track master $remote_name/$branch_name")
        ->run()
        ->getExitCode();
    }

    return $exit_code;
  }

  protected function gitMergeUpstream($branch_name) {
    $this->botSay("Merging upstream deploy repo changes.");

    $deploy_directory = $this->getConfig()->get('deploy.dir');

    $remote_url = $this->getConfig()->get('deploy.remote');
    $remote_name = md5($remote_url);

    // Merge upstream changes into the deploy artifact.
    $result = $this->taskExecStack()
      ->dir($deploy_directory)
      // This branch may not exist upstream, so we do not fail the build if a
      // merge fails.
      ->stopOnFail(FALSE)
      ->exec("git merge $remote_name/$branch_name")
      ->run();

    return $result->getExitCode();
  }

  /**
   * Commits the build artifact to the local repo.
   *
   * @param $branch_name
   *   The name of the branch to commit to.
   * @param $environment
   *   The environment the build is targeted for.
   *
   * @throws \Robo\Exception\TaskException
   */
  protected function gitCommit($branch_name, $environment) {
    $this->botSay('Committing deploy build changes to local repo.');

    $deploy_directory = $this->getConfig()->get('deploy.dir');

    $result = $this->taskExecStack()
      ->dir($deploy_directory)
      ->exec("git rm -r --cached --ignore-unmatch --quiet .")
      ->exec("git add -A")
      ->exec("git commit --quiet -m 'Manual deploy of $branch_name branch to $environment environment.'")
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE)
      ->run();

    return $result->getExitCode();
  }

  /**
   * Pushes the deploy changes to the remote repo.
   *
   * @param $branch_name
   *   The name of the branch to commit to.
   * @param $environment
   *   The environment the build is targeted for.
   *
   * @throws \Robo\Exception\TaskException
   */
  protected function gitPush($branch_name, $environment) {
    $this->botSay("Pushing deploy up to $environment.");

    $deploy_directory = $this->getConfig()->get('deploy.dir');

    $remote_url = $this->getConfig()->get('deploy.remote');
    $remote_name = md5($remote_url);

    $result = $this->taskExecStack()
      ->dir($deploy_directory)
      ->exec("git push $remote_name $branch_name")
      ->run();

    return $result->getExitCode();
  }

}
