# Wordpress Configuration Files
This application uses two different forms of Wordpress configuration files:
* WP-CFM
* ACF Groups

## WP-CFM
The WP-CFM (Wordpress Configuration Management) plugin allows for storing database configuration in the file system. This allows for some changes to be held in source control, and handled through code changes rather than the database. This allows any configuration that is code dependent, and vice versa, to be enforced.

Importing configuration changes from files into the database is managed through a WP-CLI command:
```
wp config pull all
```

When deploying code changes to Pantheon there is a script that runs that will, by default, pull in all configuration bundles defined by WP-CFM. This script can be modified to meet the needs of a specific application.

All WP-CFM configuration bundles are stored in `/web/app/config`.

## Advanced Custom Fields
The ACF plugin allows for exporting and importing of field groups as files. This allows for ACF field group definitions to be stored in source control. Additionally, the importing and exporting of these fields is managed through WP-CLI commands:
```
wp it:acf import
wp it:acf export
```

When deploying code changes to Pantheon there is a script will import all defined ACF field groups.

All ACF field group configuration files are stored in `/web/app/config/acf`.