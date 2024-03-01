# StoneHilt Blade
Adds blade directives that have not been integrated into the framework.

Overtime these directives may be deprecated and integrated into the core Laravel framework.

# Installation
Include this library:
```bash
~ composer require stonehilt/blade
```
The `StoneHiltBladeServiceProvider` will automatically be loaded and the new directives will be available.

**This overrides the creation of the Factory object with a custom version.**

If the project has extended the Factory object, please change the extension to use `StoneHilt\Blade\View\Factory`.

# Directives
### form
Generate an HTML form element.
The CSRF token is automatically included when the method if "**POST**".
Automatically mocks "**PUT**", "**PATCH**" and "**DELETE**" requests via a hidden "_**method**" input.


Signature: `@form(array $options)`
* `$options` is an associative array of attributes. Special Attributes:
  * _**method**_ Form method attribute (required if route not set)
  * _**action**_ Form action attribute (required if route not set)
  * _**route**_ Use a named route to determine Form method and action attributes
All other values are mapped directly to the HTML attribute.

Example:
```
@form(['method' => 'POST', 'action' => '/page/2', 'class' => 'class name'])
<!-- form contents -->
@endform
```

Signature: `@form(string $method, string $action)`
* `$method` Form method
* `$action` Form action

Example:
```
@form('POST', '/page/2')
<!-- form contents -->
@endform
```


### inherit
Inherit properties from the parent component into a child's component view.
This is useful when the child component needs to know the id or other key attributes of the parent component.

Signature: `@inherit(array $mapping)`
* `$mapping` is an associative array of parent component field to local alias

Example:
```
@inherit(['id' => 'parentId'])
{{ $parentId }}
```

### route
Return the route path based upon the name.

Signature: `@route(string $name, array $parameters = [])`
* `$name` Route name
* `$parameters` Parameters for the route (if applicable)

Example:
```
@route('post.update', ['page' => 2])
```

