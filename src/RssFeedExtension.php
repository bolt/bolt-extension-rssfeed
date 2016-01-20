<?php

namespace Bolt\Extension\Bolt\RssFeed;

use Bolt\Extension\SimpleExtension;
use Bolt\Helpers\Html;
use Bolt\Legacy\Content;
use Maid\Maid;

/**
 * RSS feeds extension for Bolt, originally by WeDesignIt, Patrick van Kouteren
 *
 * @author Patrick van Kouteren <info@wedesignit.nl>
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class RssFeedExtension extends SimpleExtension
{
    /**
     * {@inheritdoc}
     */
    protected function registerFrontendControllers()
    {
        return [
            '/' => new RssFeedController($this->getContainer(), $this->getConfig()),
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function registerTwigFilters()
    {
        return ['rss_safe' => 'rssSafe'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultConfig()
    {
        return [
            'sitewide' => [
                'enabled'        => true,
                'feed_records'   => 10,
                'feed_template'  => 'rss.twig',
                'content_length' => 0,
                'content_types'  => ['pages'],
                ],
        ];
    }

    /**
     * Creates RSS safe content. Wraps it in CDATA tags, strips style and
     * scripts out. Can optionally also return a (cleaned) excerpt.
     *
     * @param Content $record        Bolt Content object
     * @param string  $fields        Comma separated list of fields to clean up
     * @param integer $excerptLength Number of chars of the excerpt
     *
     * @return string RSS safe string
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
            if (!array_key_exists($field, $record->values)) {
                continue;
            }
            // Completely remove style and script blocks
            $maid = new Maid(
                [
                    'output-format'   => 'html',
                    'allowed-tags'    => ['a', 'b', 'br', 'hr', 'h1', 'h2', 'h3', 'h4', 'p', 'strong', 'em', 'i', 'u', 'strike', 'ul', 'ol', 'li', 'img'],
                    'allowed-attribs' => ['id', 'class', 'name', 'value', 'href', 'src'],
                ]
            );
            $result .= $maid->clean($record->values[$field]);
        }

        if ($excerptLength > 0) {
            $result = Html::trimText($result, $excerptLength);
        }

        return new \Twig_Markup('<![CDATA[ ' . $result . ' ]]>', 'utf-8');
    }
}
