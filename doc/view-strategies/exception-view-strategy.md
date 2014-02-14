# Exception View Strategy
The base Cornerstone module registers the "View\Http\ExceptionStrategy" for handling
the unhandled Exceptions before Zend gets its grubby paws on it and swallows it
whole. It uses the "Default\Logger" service to write those exceptions to Syslog
by default, but the service can be replaced for other means as well.