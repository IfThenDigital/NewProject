<?php

namespace WpRobot\Robo\Plugin\Commands\Help;

use WpRobot\Robo\Plugin\Commands\BaseCommand;

/**
 * Class HelpCommands.
 *
 * @package WpRobot\Robo\Plugin\Commands\Help
 */
class HelpCommands extends BaseCommand {

  /**
   * Displays help info.
   *
   * @command help:info
   *
   * @description Displays help info.
   */
  public function info() {
    $this->botYell('Welcome to Wordpress Robot. I can perform some helpful tasks for you.');
  }

  /**
   * Displays the different message types.
   *
   * @command help:message-types
   */
  public function messageTypes() {
    $this->botSay("This is a bot say.");
    $this->botWarning("This is a bot warning.");
    $this->botError("This is a bot error.");
    $this->botYell("This is a bot yell.");
  }

}
