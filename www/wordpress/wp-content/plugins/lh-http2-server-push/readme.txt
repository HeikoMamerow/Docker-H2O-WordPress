=== LH HTTP/2 Server Push ===
Contributors: shawfactor
Tags: http2, server push, preload, performance, prefetch
Requires at least: 3.0
Tested up to: 4.7
Stable tag: 1.00
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Does HTTP/2 Server Push for JavaScript and CSS resources properly.

== Description ==

Server Push provides significant performance gains if used judiciously. However other Wordpress plugins that handle server push can actually slow your site as they push files indiscrimentally.

Most push all files enqueued by wordpress to the broswer and use output buffering to achieve this.

However this plugin does it properly by only pushing the the assets that the browser requires for the initial user interaction. Namely the scripts and css in the head of the document.

Note : It requires a web server that supports HTTP/2.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/lh-http2-server-push` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress

== Frequently Asked Questions ==

= How do I know if this is working? =

There are a couple ways:

1. [nghttp](https://www.nghttp2.org/documentation/nghttp.1.html) is an HTTP/2 client that ships with the nghttp2 suite. ```nghttp -v http://example.com``` will show all the HTTP/2 signalling packets, HTTP headers, content, and resources sent from the server in a single request. You can see ```PUSH PROMISE``` signals from the server and the pushed resources after the main page is sent.
2. In Google Chrome, [chrome://net-internals/#spdy](chrome://net-internals/#spdy) will show a history of server connections from the browser. Clicking on a connection will show the discussion between the browser and the server. Within that text, you can see ```PUSH PROMISE``` packets and the pushed resources.

= Why aren't some css styles in the head of thye document pushed? =

The plugin relies on all assets being enqueued by the time the wp_enqueue_scripts action is run. However css styles can be enqueued and still appear in the head as late as the wp_print_styles action. This is porr design but IU can't control other plugins.

= Can I modify the urls that are pushed to the browser by this plugin? =

Yes there is a filter lh_htt2_server_push_urls. This filter takes the input of an associaytive array with the urls being the key and the file type being the value. By using this filter you can add, remove or modify all the pushed urls.

== Changelog ==

**1.00 May 15, 2017**  
Initial release.

**1.01 May 20, 2017**  
Is admin check.

**1.02 May 24, 2017**  
More robust code.
