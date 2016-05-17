RSS feed publisher
==================

Creates sitewide and/or contenttype specific feeds for your Bolt website.

After installation, a configuration file will be created as
`app/config/extensions/rssfeed.bolt.yml`, where you can define how the RSS feeds
should be created. Depending on your set up in the extension's configuration,
you can access RSS feeds either by sitewide or contenttype specific URLs:

 - **Site wide**: `/rss/feed.{extension}`
 - **Contenttype**: `/{contenttypeslug}/rss/feed.{extension}`

Where:
  - `{contenttypeslug}` is the slug of your contenttype.
  - `{extension}` is either 'xml' or 'rss'.

See the comments in your `rssfeed.bolt.yml` for more details.

**Tip:** To allow RSS aggregators like Feedly to easily find your site's feed, you
should add an autodiscovery link to the `<head>`-section of your site. For
example:

```html
<link rel="alternate" type="application/rss+xml" href="/rss/feed.xml" />
```
