<?php

namespace Bolt\Extension\Bolt\RSSFeed;

use Bolt\Helpers\Html;
use Maid\Maid;

/**
 * RSS feeds extension for Bolt, originally by WeDesignIt, Patrick van Kouteren
 *
 * @author Patrick van Kouteren <info@wedesignit.nl>
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class Extension extends \Bolt\BaseExtension
{
    const NAME = 'RSSFeed';

    /**
     * @return string
     */
    public function getName()
    {
        return Extension::NAME;
    }

    public function initialize()
    {
        // Set up routes
        $this->setController();

        // Add Twig filter
        $this->addTwigFilter('rss_safe', 'rssSafe');
    }

    /**
     * Set default config
     *
     * @return array
     */
    protected function getDefaultConfig()
    {
        return array(
            'sitewide' => array(
                'enabled'        => true,
                'feed_records'   => 10,
                'feed_template'  => 'rss.twig',
                'content_length' => 0,
                'content_types'  => array('pages')
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
            ->assert('contenttypeslug', $this->getContentTypeAssert());
    }

    /**
     * Get a value to use in 'assert() with the available contenttypes
     *
     * @param boolean $includesingular
     *
     * @return string $contenttypes
     */
    private function getContentTypeAssert($includesingular = false)
    {
        $slugs = array();
        foreach ($this->app['config']->get('contenttypes') as $type) {
            $slugs[] = $type['slug'];
            if ($includesingular) {
                $slugs[] = $type['singular_slug'];
            }
        }

        return implode('|', $slugs);
    }

    /**
     * Creates RSS safe content. Wraps it in CDATA tags, strips style and
     * scripts out. Can optionally also return a (cleaned) excerpt.
     *
     * @param \Bolt\Content  $record        Bolt Content object
     * @param string         $fields        Comma separated list of fields to clean up
     * @param integer        $excerptLength Number of chars of the excerpt
     *
     * @return string                       RSS safe string
     */
    public function rssSafe($record, $fields = '', $excerptLength = 0)
    {
        // Make sure we have an array of fields. Even if it's only one.
        if (!is_array($fields)) {
            $fields = explode(',', $fields);
        }
        $fields = array_map('trim', $fields);

        $result = '';

        foreach ($fields as $field) {
            if (array_key_exists($field, $record->values)) {

                // Completely remove style and script blocks
                $maid = new Maid(
                    array(
                        'output-format'   => 'html',
                        'allowed-tags'    => array('a', 'b', 'br', 'hr', 'h1', 'h2', 'h3', 'h4', 'p', 'strong', 'em', 'i', 'u', 'strike', 'ul', 'ol', 'li', 'img'),
                        'allowed-attribs' => array('id', 'class', 'name', 'value', 'href', 'src')
                    )
                );

                $result .= $maid->clean($record->values[$field]);
            }
        }

        if ($excerptLength > 0) {
            $result = Html::trimText($result, $excerptLength);
        }

        return new \Twig_Markup('<![CDATA[ ' . $result . ' ]]>', 'utf-8');
    }

}
