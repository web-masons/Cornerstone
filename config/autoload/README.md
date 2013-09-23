Environment Configuration Files
===========================

Introduction
------------
The application skeleton is configured by default to support two different kinds of "autoload"
configuration. The Global configuration and Environment specific configuration.

Global Configuration
------------
The Global configuration file is for exactly what you think it might be. Global configuration for
the ZF2 application. The only words of wisdom (?) here woudl be to keep as much out of the global
configuration and in module configuration as possible. These values really should represent the
bare minimum of mandatory overrides from module configuration or things that really can't be
defined in module configuration (and aren't environment specific).

Environment Specific Configuration
------------
The environment specific configuration files are for settings that change between your development
and your testing or production environments. This is a good place to put data access locations such
as database settings etc. For those of you that don't like to check in your environment specific
settings into source control, you can create a local.php and add it to your .gitignore.

The other item of note is that I chose to use json files for environment specific configuration so
that other non-php applications could potentially read them. If you don't need or want json config
files you can simply update your application.config.php file and change the extension to .php instead
of .json and it should all just work.