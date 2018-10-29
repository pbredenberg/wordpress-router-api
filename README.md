# WordPress Router API

Simple WordPress plugin to provide a router-like interface with applications consuming the WordPress REST API.

## Installing

Simply copy the project directory into `wp-content/plugins` and activate it via the plugins admin page, or drop `wordpress-router-api.php` into the `wp-content/mu-plugins` directory.

## Usage

The plugin exposes a single route to the API at the following URL via a GET request:

`http://MYWORDPRESSURL/wp-json/wp-router-api/v1/by/path`

It accept a single paramater named `path`.

Pass the whole path of a post you'd like to retrieve:

`http://MYWORDPRESSURL/wp-json/wp-router-api/v1/by/path?path=/path/to/post`

The response will be a standard WordPress post object, with the following additional properties:

* `children`: An array of the returned posts children (in case you're unaware, that includes attachments).
* `fields`: An array of custom fields associated with the post (any data stored as an array will be unserialized).

## Technical Notes

Tested with WordPress v4.9.8, PHP 7.1.6

In some situations your path string may need to be a UTF-8 encoded string such as the result of encodeURI in JS or urlencode in PHP.
