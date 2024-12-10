# Gravatar for Bow Framework

[![Total Downloads](https://poser.pugx.org/bowphp/gravatar/d/total.svg)](https://packagist.org/packages/bowphp/gravatar)
[![Latest Stable Version](https://poser.pugx.org/bowphp/gravatar/v/stable.svg)](https://packagist.org/packages/bowphp/gravatar)
[![License](https://poser.pugx.org/bowphp/gravatar/license.svg)](https://packagist.org/packages/bowphp/gravatar)

## Installation

First, pull in the package through Composer via the command line:

```bash
composer require bowphp/gravatar
```

## Usage

Within your controllers or views, you can use

```php
Gravatar::get('email@example.com');
```

This will return the URL to the gravatar image of the specified email address.
In case of a non-existing gravatar, it will return return a URL to a placeholder image.
You can set the type of the placeholder in the configuration option `fallback`.
For more information, visit [gravatar.com](http://en.gravatar.com/site/implement/images/#default-image)

Alternatively, you can check for the existence of a gravatar image by using

```php
Gravatar::exists('email@example.com');
```

This will return a boolean (`true` or `false`).

Or you can pass a url to a custom image using the fallback method:

```php
Gravatar::fallback('http://urlto.example.com/avatar.jpg')->get('email@example.com');
```

## Configuration

You can create different configuration groups to use within your application and pass the group name as a second parameter to the `get`-method:

There is a default group in `config/gravatar.php` which will be used when you do not specify a second parameter.

If you would like to add more groups, feel free to edit the `config/gravatar.php` file. For example:

```php

return [
	'default' => [
		'size'   => 80,
		'fallback' => 'mm',
		'secure' => false,
		'maximumRating' => 'g',
		'forceDefault' => false,
		'forceExtension' => 'jpg',
 	],

 'small-secure' => [
    'size'   => 30,
    'secure' => true,
	],

	'medium' => [
    'size'   => 150,
	],
];
```

then you can use the following syntax:

```php
Gravatar::get('email@example.com', 'small-secure'); // will use the small-secure group
Gravatar::get('email@example.com', 'medium'); // will use the medium group
Gravatar::get('email@example.com', 'default'); // will use the default group
Gravatar::get('email@example.com'); // will use the default group
```

Alternatively, you could also pass an array directly as the second parameter as inline options. So, instead of passing a configuration key, you pass an array, which will be merged with the default group:

```php
Gravatar::get('email@example.com', ['size' => 200]); 
```
