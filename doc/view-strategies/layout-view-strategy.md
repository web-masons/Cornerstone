# Layout View Strategy
The base Cornerstone module registers the "View\Http\LayoutStrategy" for handling
route based layout toggling.  Each route can define a default `layout` value
that matches a view manager's `template_map` and that layout will be used instead
of the default.


## Configuration
To change the layout for a route, configure the following:

```php
$view_manager['template_map'][<LAYOUT NAME>] => 'path to layout';

$router['routes']['<ROUTE NAME>']['options']['default'][layout'] = '<LAYOUT NAME>'

```

Keep in mind, this will affect all child routes of the configured route.  If no `layout`
is provided the default layout is used.
