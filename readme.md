RSS Feed
========

Creates sitewide and/or contenttype specific feeds for your Bolt site.

Depending on your set up in the extension's config.yml you can access RSS feeds
either by sitewide or contenttype specific URLs

**Site wide**
`/rss/feed.{extension}`

**Contenttype**
`/{contenttypeslug}/rss/feed.{extension}`

Where:
  - {contenttypeslug} is the slug of your contenttype
  - {extension} is either 'xml' or 'rss'


See the comments in `config.yml` for more details

