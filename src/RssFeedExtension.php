<?php

namespace Bolt\Extension\Bolt\RssFeed;

use Bolt\Extension\Bolt\RssFeed\Controller\RssFeed;
use Bolt\Extension\SimpleExtension;
use Bolt\Helpers\Html;
use Bolt\Storage\Entity\Content;
use Maid\Maid;
use Silex\Application;

/**
 * RSS feeds extension for Bolt, originally by WeDesignIt, Patrick van Kouteren
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class RssFeedExtension extends SimpleExtension
{
    /**
     * {@inheritdoc}
     */
    protected function registerServices(Application $app)
    {
        $app['rssfeed.config'] = $app->share(
            function () {
                return new Config\Config($this->getConfig());
            }
        );

        $app['rssfeed.generator'] = $app->share(
            function ($app) {
                return new Generator(
                    $app['rssfeed.config'],
                    $app['config']->get('contenttypes'),
                    $app['storage'],
                    $app['twig']
                );
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerFrontendControllers()
    {
        return [
            '/' => new RssFeed(),
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
    protected function registerTwigPaths()
    {
        return ['templates'];
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
     * @param bool    $isRss         Called from an ATOM render
     *
     * @return string RSS safe string
     */
    public function rssSafe($record, $fields = '', $excerptLength = 0, $isRss = true)
    {
        //get Parsedown to clean markdown entries
        $parseDown = new \Parsedown();

        // Make sure we have an array of fields. Even if it's only one.
        if (!is_array($fields)) {
            $fields = explode(',', $fields);
        }
        $fields = array_map('trim', $fields);

        // Completely remove style and script blocks
        $maid = new Maid(
            [
                'output-format'   => 'html',
                'allowed-tags'    => ['a', 'b', 'br', 'div', 'hr', 'h1', 'h2', 'h3', 'h4', 'p', 'strong', 'em', 'i', 'u', 'strike', 'ul', 'ol', 'li', 'img'],
                'allowed-attribs' => ['id', 'class', 'name', 'value', 'href', 'src'],
            ]
        );

        $fieldTypes = $record->getContentType()->getFields();
        $result = '';

        foreach ($fields as $field) {

            $fieldValue = $record->get($field);
            if (!empty($fieldTypes[$field]['type']) && $fieldTypes[$field]['type'] == 'markdown' ) {
                $fieldValue = $parseDown->parse($fieldValue);
            }

            $result .= $maid->clean($fieldValue);
        }

        if ($excerptLength > 0) {
            $result = Html::trimText($result, $excerptLength);
        }
        if ($isRss) {
            $result = '<![CDATA[ ' . $result . ' ]]>';
        }

        return new \Twig_Markup($result, 'utf-8');
    }
}
