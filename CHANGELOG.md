# Changelog for RSS Feed

* 4.0.0 (2017-05-27)
 * Added: JSON feeds
 * Added: Autodiscovery

* 3.1.4 (2016-04-14)
 * Updated: Require Bolt 3.2.3+
 * Fixed Migrate remaining paths to Bolt v3 format
 * Added: ATOM feeds & template

* 3.1.3 (2016-11-27)
 * Fixed: Only show published records

* 3.1.2 (2016-11-26)
 * Fixed: Use correct named paramter in template link

* 3.1.1 (2016-11-26)
 * Fixed: Sitewide configuration would fail if specific ContentType were also not configured as well

* 3.1.0 (2016-11-26)
 * Refactor

* 3.0.4 (2016-08-15)
 * Added possibility to configure custom routes in routing.yml. (credit @bobdenotter)

* 3.0.3 (2016-08-31)
 * Fixed: Added charset=utf-8 to contenttype #13 (Credit @dforstercon)

* 3.0.2 (2016-08-15)
 * Fixed: Date sorting

* 3.0.1 (2016-06-14)
 * Ensure all taxonomies are output

* 3.0.0 (2016-05-17)
 * Bolt v3 compatible release

* 2.2.3 ()
 * Use configured locale instead of hardcoded value (credit @doenietzomoeilijk)

* 2.2.2 (2015-09-09)
 * The koala did it…

* 2.2.1 (2015-09-08)
 * Don't show "Warning:" when one of the contenttypes is empty. (credit @bobdenotter)

* 2.2.0 (2015-08-19)
 * Migrate the old Bolt core rss_safe() function into the extension as it is to be removed from Bolt

* 2.1.1 (2015-03-13)
 * Tidy response object creation

* 2.1.0 (2015-03-13)
 * Small PHPDoc & code tidy

* 2.0.1 (2015-02-10)

 * Add a getContentTypeAssert() function to cope with changes in Bolt 2.1

* 2.0.0 (2014-12-17)

 * Initial release for Bolt 2.0.0
