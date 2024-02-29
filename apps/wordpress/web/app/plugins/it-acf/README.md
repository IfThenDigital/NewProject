# Advanced Custom Fields Helper - IfThen []
This plugin provides some helpful utility functionality in support of the Advanced Custom Fields plugin.

## Configuration Commands
The `AcfHelperCommand` provides two commands to help with importing and exporting ACF field groups.

- `wp it:acf import` - Imports all field group configuration found in the file system. *Note*: This will delete any field groups that are defined in Wordpress, but are not yet exported to a JSON configuration file.
- `wp it:acf export` - Exports all field group configuration currently in wordpress to the file system.

## Configuration File Location
This is defined in `it-acf.php`. The configuration files are stored in /web/app/config/acf.