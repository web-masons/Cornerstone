# Cornerstone
Cornerstone is a Zend Framework 2 module that is meant to replace the
default Application module that the ZF2 skeleton application starts with.

It has been created to provide a default set of tools that many sites
can build from. The hope is that it will provide a collection of base
tools and utilities to standardize the creation of ZF2 Modules and
Applications while also making getting started easier.

You will likely notice that this Module is also itself a standalone
"site" that can be configured and run. All of my modules that I develop
are built upon Cornerstone so that each of them can be as independent
as possible and aren't themselves required to be composed into another
module to make sure they work. When composed into another application
however, it will work exactly as any other ZF2 module would and will
not need to be specifically configured (though your parent application
will be).

## Requirements
This module requires the use of Composer and Zend Framework 2.2 or
higher. You will find additional software requirements in the
packaged composer.json file.

## Usage

### Creating a new Site using Cornerstone
It is recommended that any site built to use Cornerstone be built on
the Cornerstone [application blueprint](https://github.com/web-masons/application-blueprint)
from [Packagist](https://packagist.org/packages/web-masons/application-blueprint).

```
composer create-project web-masons/application-blueprint
```

### Adding Cornerstone to your existing project
If you are not using the application-blueprint above, then you should be
composing in Cornerstone. To do so, add lines similar to the following
to your project's composer.json file.

```
"require": {
    "php": ">=5.4",
    "zendframework/zendframework": ">=2.2",
    "web-masons/Cornerstone" : "0.*",
},

"repositories": [ {
    "type": "vcs",
    "url": "https://github.com/web-masons/Cornerstone"
}],

```
## Documentation
All documentation can be found in the [doc](doc) folder.

## Contributors

Collaborators:
* [@Oakensoul](https://github.com/oakensoul)


## Contributing

* [Getting Started](doc/CONTRIBUTING.md)
* [Bug Reports](doc/CONTRIBUTING.md#bug-reports)
* [Feature Requests](doc/CONTRIBUTING.md#feature-requests)
* [Pull Requests](doc/CONTRIBUTING.md#pull-requests)

# LICENSE
This module is licensed using the Apache-2.0 License:

```
Copyright (c) 2013, github.com/web-masons Contributors
```
