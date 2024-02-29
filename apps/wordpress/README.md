# Wordpress Starter Kit
A starter kit for any wordpress site, built upon [Bedrock](https://roots.io/docs/bedrock/master/installation/) project structure. It features:
- Dependency management via Composer.
- Ease of managing environment configuration.
- Pantheon support of the Bedrock directory structure out of the box.
- Supports both [Drupal VM](https://www.drupalvm.com/) and [Lando](https://docs.lando.dev/) for local development.
- A custom task running script built upon [Robo](https://robo.li/).


# Creating a New Wordpress Application

## Initial Usage
Copy/export this project to a directory of your choice. Take care noticing/changing:
- `composer.json` with information suited to your project needs.
- Environment specific configuration found in `config/environments`.
- Application specific configuration for local development:
  - `.lando.yml` for Lando configuration.
  - `box/drupal-vm/config.yml` for Drupal VM configuration.
- Pantheon configuration found in `pantheon.yml` and `pantheon.upstream.yml`.

## Automated Setup with WP CLI Command
A custom WP_CLI command is available for ease in setting up a new site. The process is:
1. Go through the install process. Setting up an admin name, password, email, etc.
2. Run this WP_CLI command: `wp it:site init`
3. The Wordpress site should now have the default plugins and admin theme installed.

## Manual Setup
1. Go through the install process. Setting up an admin name, password, email, etc.
2. Activate WP-CFM plugin.
3. Go to WP-CFM settings under Settings > WP-CFM
4. For *All Bundles*, click the *Pull* button.


# Local Development
Both Drupal VM and Lando are supported for local development, with Drupal VM being the recommended choice for performance reasons, especially if debugging is used.

## Drupal VM
A pre-configured Drupal VM environment is available for use. To get the Drupal VM up and running:
1. Run `composer install` to install all dependencies.
1. Run `./wpr setup:local:drupalvm` to copy a preconfigured `.env` file.
1. Run `vagrant up` to initialize and run the Vagrant virtual machine.
1. Visit the site in your browser.

To execute commands from inside the virtual machine, run `vagrant ssh`. WP CLI is available, and commands can be executed from the root of the project directory inside the virtual machine.

## Lando (Deprecated)
A pre-configured Lando environment is available for use. To get the Lando environment up and running:
1. Run `composer install` to install all dependencies.
1. Run `./wpr setup:local:lando` to copy a preconfigured `.env` file.
1. Adjust the `.env` file as needed for a custom setup.
1. Visit the site in your browser.

Additionally, here are some helpful Lando commands:
```
lando start - Starts your Lando instance.
lando stop - Stops your Lando instance.
lando restart
lando rebuild - Rebuilds your Lando instance. Needed if your Lando configuration changes.
lando destroy - Destroys your Lando instance. The database is deleted.
lando info - Get information about the Lando setup, including URLs and internal host names and passwords.
```

Some custom Lando commands are available:
```
lando xdebug:on - Turns on xdebug for PHP debugging.
lando xdebug:off - Turns off xdebug.
```

Additionally, Lando has WP CLI installed for use:
```
lando wp cache flush - Flush Wordpress cache.
lando wp rewrite flush - Flush Wordpress permalinks.
```


# Wordpress Robot
A command line tool for task running is available for use as well. It is built upon [Robo](https://robo.li/).

## Local Tasks
There are two tasks available for local setup:

For a local Apache setup:
```
./wpr setup:local:apache
```

For a local Drupal VM setup:
```
./wpr setup:local:drupalvm
```

## Deploy Tasks
Currently, only deploying to a Pantheon environment is supported.

To deploy: 
1. Update the `config/wp_robot/config.yml` file with the `deploy.remote` value. This is the URL of the Git repo deploys will be pushed to.
2. Execute the following command and supply the remote branch name that the code needs to be pushed to.
3. The deploy command executes against your current branch/code.
```
./wpr deploy:branch remote-branch-name
```

# Configuration Management
Please see the [configuration README](docs/configuration.md) for more details.