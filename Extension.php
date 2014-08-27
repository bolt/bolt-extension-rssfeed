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
    const NAME = 'RSSFeed';

    public function getName()
    {
        return Extension::NAME;
    }

    public function initialize()
    {
        // Set up routes
        $this->setController();
    }

    protected function getDefaultConfig()
    {
        return array(
            'sitewide' => array(
                'enabled' => true,
                'feed_records' => 10,
                'feed_template' => 'rss.twig',
                'content_length' => 0,
                'content_types' => array('pages')
                )
        );
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
