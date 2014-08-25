<?php

namespace Bolt\Extension\Bolt\RSSFeed;

/**
 * RSS feeds extension for Bolt, originally by WeDesignIt, Patrick van Kouteren
 *
 * @author Patrick van Kouteren <info@wedesignit.nl>
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class Extension extends \Bolt\BaseExtension
{
    const NAME = 'rssfeed';

    public function getName()
    {
        return Extension::NAME;
    }

    public function initialize()
    {
        // Set up routes
        $this->setController();
    }

    /**
     * Create controller and define routes
     */
    private function setController()
    {
        // Create controller object
        $this->controller = new Controller($this->app);

        // Sitewide feed
        $this->app->match('/rss/feed.{extension}', array($this->controller, 'feed'))
                    ->assert('extension', '(xml|rss)');

        // Contenttype specific feed(s)
        $this->app->match('/{contenttypeslug}/rss/feed.{extension}', array($this->controller, 'feed'))
                    ->assert('extension', '(xml|rss)')
                    ->assert('contenttypeslug', $this->app['storage']->getContentTypeAssert());
    }
}
