# Scheme View Strategy
The base Cornerstone module registers the "View\Http\SchemeStrategy" for handling
SSL traffic. When ssl route enforcement is enabled, the strategy will take any
port 80 traffic and force it to the appropriate route over SSL.

This can also be enabled by wrapping it in a check to see if the user is authenticated,
since you likely won't want SSL enforced all the time. (Though you can if you need/want.)

## Configuration
To enable or disable SSL forwarding for any route, configure the following:

$config['routes']['example']['options']['defaults']['force_https_scheme'] = true | false;

Keep in mind, this will affect all child routes of the configured route.
