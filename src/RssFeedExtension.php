<?php

namespace Bolt\Extension\Bolt\RssFeed;

use Bolt\Asset\Snippet\Snippet;
use Bolt\Asset\Target;
use Bolt\Controller\Zone;
use Bolt\Extension\Bolt\RssFeed\Controller\RssFeed;
use Bolt\Extension\SimpleExtension;
use Bolt\Storage\Entity\Content;
use Silex\Application;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
                    $app['twig'],
                    $app['url_generator'],
                    $app['config']->get('general/sitename'),
                    $app['config']->get('general/payoff')
                );
            }
        );

        $app['controller.rssfeed'] = $app->share(
            function () {
                return new RssFeed();
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function registerFrontendControllers()
    {
        $app = $this->getContainer();

        return [
            '/' => $app['controller.rssfeed'],
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
            'feeds' => [
                'rss' => true,
                'json' => true,
                'atom' => true
            ],
            'autodiscovery' => true,
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
        $parseContent = new ParseContent();

        return $parseContent->rssSafe($record, $fields, $excerptLength, $isRss);
    }

    /**
     * {@inheritdoc}
     */
    protected function registerAssets()
    {
        $app = $this->getContainer();

        if (!$app['rssfeed.config']->getAutodiscovery()) {
            return;
        }

        $snippet = new Snippet();
        $snippet
            ->setLocation(Target::END_OF_HEAD)
            ->setZone(Zone::FRONTEND)
            ->setCallback([$this, 'autodiscoverySnippet'])
        ;

        return [
            $snippet,
        ];
    }

    public function autodiscoverySnippet()
    {
        $app = $this->getContainer();
        $feeds = $app['rssfeed.config']->getEnabledFeeds();

        $snippet = [];

        if ($feeds['rss']) {
            $snippet[] = sprintf(
                '<link rel="alternate" type="application/rss+xml" title="RSS feed" href="%srss/feed.xml">',
                $app['url_generator']->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL)
            );
        }
        if ($feeds['json']) {
            $snippet[] = sprintf(
                '<link rel="alternate" type="application/json" title="JSON feed" href="%sjson/feed.json">',
                $app['url_generator']->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL)
            );
        }
        if ($feeds['atom']) {
            $snippet[] = sprintf(
                '<link rel="alternate" type="application/atom+xml" title="Atom feed" href="%satom/feed.xml">',
                $app['url_generator']->generate('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL)
            );
        }

        return implode("\n", $snippet);
    }
}
