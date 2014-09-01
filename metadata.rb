name              'Cornerstone'
maintainer        'Robert Johnson'
maintainer_email  'rjohnson@turbine.com'
license           'BSD'
description       'Core Cornerstone ZF2 Module'
long_description  IO.read(File.join(File.dirname(__FILE__), 'README.md'))
version           '5.0.0'

supports 'ubuntu'

depends 'php-webserver'
depends 'cornerstone-vagrant'
depends 'nodejs'
depends 'grunt_cookbook'
depends 'apache2', '= 1.10.4'
