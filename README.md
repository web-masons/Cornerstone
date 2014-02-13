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
my [application skeleton](https://github.com/oakensoul/application-skeleton)
from [Packagist](https://packagist.org/packages/oakensoul/application-skeleton).

```
composer create-project oakensoul/application-skeleton
```

### Adding Cornerstone to your existing project
If you are not using the application-skeleton above, then you should be
composing in Cornerstone. To do so, add lines similar to the following
to your project's composer.json file.

```
"require": {
    "php": ">=5.4",
    "zendframework/zendframework": ">=2.2",
    "oakensoul/Cornerstone" : "0.*",
},
    
"repositories": [ {
    "type": "vcs",
    "url": "https://github.com/oakensoul/Cornerstone"
}],

```
## Documentation
All documentation can be found in the [doc](doc) folder.
 
## Contributing
* [Coding Style Guidelines](doc/CONTRIBUTING.md#coding-style-guidelines)
* [Bug Reports](doc/CONTRIBUTING.md#bugs)
* [Feature Requests](doc/CONTRIBUTING.md#features)
* [Pull Requests](doc/CONTRIBUTING.md#pull-requests)

# LICENSE
This module is licensed using the BSD 2-Clause License:

```
Copyright (c) 2013 Robert Gunnar Johnson Jr.
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:

- Redistributions of source code must retain the above copyright notice, this
  list of conditions and the following disclaimer.
- Redistributions in binary form must reproduce the above copyright notice,
  this list of conditions and the following disclaimer in the documentation
  and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
```