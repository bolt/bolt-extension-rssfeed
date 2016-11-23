<?php

namespace Bolt\Extension\Bolt\RssFeed\Controller;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * RSSFeed Controller.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class RssFeed implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /** @var $ctr ControllerCollection */
        $ctr = $app['controllers_factory'];

        // Sitewide feed
        $app->match('/rss/feed.{extension}', [$this, 'feed'])
            ->assert('extension', '(xml|rss)')
        ;

        // ContentType specific feed(s)
        $app->match('/{contentTypeName}/rss/feed.{extension}', [$this, 'feed'])
            ->assert('extension', '(xml|rss)')
            ->assert('contentTypeName', $this->getContentTypeAssert($app))
        ;

        return $ctr;
    }

    /**
     * @param string $contentTypeName
     *
     * @return Response
     */
    public function feed(Application $app, $contentTypeName = null)
    {
        $feed = $app['rssfeed.generator']->getFeed($contentTypeName);

        $response = new Response($feed, Response::HTTP_OK);
        $response->setCharset('utf-8')
            ->setPublic()
            ->setSharedMaxAge(3600)
            ->headers->set('Content-Type', 'application/rss+xml;charset=UTF-8');

        return $response;
    }

    /**
     * Get a value to use in 'assert() with the available ContentTypes
     *
     * @param Application $app
     * @param boolean     $includeSingular
     *
     * @return string
     */
    private function getContentTypeAssert(Application $app, $includeSingular = false)
    {
        $slugs = [];
        foreach ($app['config']->get('contenttypes') as $type) {
            $slugs[] = $type['slug'];
            if ($includeSingular) {
                $slugs[] = $type['singular_slug'];
            }
        }

        return implode('|', $slugs);
    }
}
