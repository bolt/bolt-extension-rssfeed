RSS, JSON and Atom feed publisher
=================================

Creates sitewide and/or contenttype specific feeds for your Bolt website.

After installation, a configuration file will be created as
`app/config/extensions/rssfeed.bolt.yml`, where you can define how the various 
feeds should be created. Depending on your set up in the extension's 
configuration, you can access RSS feeds either by sitewide or contenttype 
specific URLs:

 - **Site wide**: `/rss/feed.{extension}`
 - **Contenttype**: `/{contenttypeslug}/rss/feed.{extension}`

Where:
  - `{contenttypeslug}` is the slug of your contenttype.
  - `{extension}` is either 'xml' or 'rss'.

See the comments in your `rssfeed.bolt.yml` for more details.

To allow RSS and other Feed aggregators like Feedly to easily find your site's
feed, you should add an autodiscovery link to the `<head>`-section of your site.
Do this by simply setting `autodiscovery: true` in the configuration file.

Configuring routing
-------------------

This extension automatically sets up routing to match the URL patterns mentioned above (like `/rss/feed.xml`). If you have need of a specific URL to publish the feed on, you must add the route to your `routing.yml` file. For example:

```
my_rss_feed:
    path: /news.xml
    defaults:
        _controller: controller.rssfeed:feed
        contenttypeslug: news
```
