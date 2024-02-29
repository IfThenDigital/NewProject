<?php


namespace WpRobot\Robo\Plugin\Commands\Git;


use WpRobot\Robo\Plugin\Commands\BaseCommand;

class GitCommands extends BaseCommand {

  /**
   * Adds git hooks to the .git folder.
   *
   * @return int
   *   Exit code.
   *
   * @command git:hooks:add
   */
  public function addGitHooks() {
    $result = $this->taskFilesystemStack()
      ->stopOnFail()
      ->copy(
        $this->getRepoRoot() . "/scripts/git-hooks/pre-commit",
        $this->getRepoRoot() . '/.git/hooks/pre-commit'
      )
      ->run();

    $this->botYell("Added git hooks.");

    return $result->getExitCode();
  }

  /**
   * Removes git hooks from the .git folder.
   *
   * @return int
   *   Exit code.
   *
   * @command git:hooks:remove
   */
  public function removeGitHooks() {
    $result = $this->taskFilesystemStack()
      ->stopOnFail()
      ->remove($this->getRepoRoot() . '/.git/hooks/pre-commit')
      ->run();

    $this->botYell("Removed git hooks.");

    return $result->getExitCode();
  }

}
