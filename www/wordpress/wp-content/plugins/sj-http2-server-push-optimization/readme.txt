=== SJ HTTP/2 Server Push Optimization ===
Contributors: sandesh055
Tags: http2, server push, preload, performance, prefetch
Requires at least: 3.0
Tested up to: 4.6
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

SJ HTTP/2 Server Push Optimization for JavaScript and CSS resources enqueued in the page.

== Description ==

HTTP/2 is the new generation HTTP protocol that provide tremendous powers the web. HTTP/2 is future of web. The most powerful feature of HTTP/2 is *server push*. Most of popular hosting providers supports to Server Push. Server Push provides significant performance gains if used judiciously.

Check <a href="https://http2.akamai.com/demo">HTTP/2 Demo Here</a>.

Server Push allows the server to “bundle” assets that the client didn’t ask for.It send resources to the browser before it even realizes it needs them. This avoids the usual HTTP request/response cycle.

This plugin enabled WordPress to send a ```Link:<...> rel="prefetch"``` header to every enqueued script and style page. Is doesn't support who output their scripts directly into the page itself. This plugin doesn't affect any visual part of site.

Read more about <a href="https://blog.cloudflare.com/announcing-support-for-http-2-server-push-2/">server push here</a>

Note : It requires a web server that supports HTTP/2.

=== WordPress 4.6 and above ===
WordPress 4.6 introduced support for [resource hints](https://make.wordpress.org/core/2016/07/06/resource-hints-in-4-6/).
This plugin defers to WordPress 4.6 and theme/plugin developers to responsibly prefetch the right assets.

I've added a filter To restore the old behavior of resource hints on WordPress 4.6 and above. Use following filter in your functions.php file or in a custom plugin:

```add_filter('sjh_http2_resource_hints', '__return_true');```

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/sj-http2-server-push` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress


== Frequently Asked Questions ==

= What if my web server doesn't support HTTP/2 or "server push"? =

Server push is triggered by the same mechanism as *link prefetching*, which almost all major modern browsers support over HTTP 1.x. You can take advantage of "preload" even if you don't have HTTP/2's features. People visiting your site may still get a better experience from it. <a href="https://w3c.github.io/preload/">Read here about preload</a>

= How do I know if this is working? =

There are a couple ways:

1. [nghttp](https://www.nghttp2.org/documentation/nghttp.1.html) is an HTTP/2 client that ships with the nghttp2 suite. ```nghttp -v http://example.com``` will show all the HTTP/2 signalling packets, HTTP headers, content, and resources sent from the server in a single request. You can see ```PUSH PROMISE``` signals from the server and the pushed resources after the main page is sent.
2. In Google Chrome, [chrome://net-internals/#spdy](chrome://net-internals/#spdy) will show a history of server connections from the browser. Clicking on a connection will show the discussion between the browser and the server. Within that text, you can see ```PUSH PROMISE``` packets and the pushed resources.

== Screenshots ==

== Changelog ==

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0 =
Initial release