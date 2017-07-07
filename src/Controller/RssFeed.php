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

        // Site-wide feed
        $app->get('/atom/feed.{extension}', [$this, 'atom'])
            ->assert('extension', '(atom|xml)')
            ->bind('feedAtom')
        ;
        $app->get('/rss/feed.{extension}', [$this, 'feed'])
            ->assert('extension', '(rss|xml)')
            ->bind('feedRss')
        ;
        $app->get('/json/feed.json', [$this, 'json'])
            ->bind('feedJson')
        ;

        // ContentType specific feed(s)
        $app->get('/{contentTypeName}/atom/feed.{extension}', [$this, 'atom'])
            ->assert('extension', '(atom|xml)')
            ->assert('contentTypeName', $this->getContentTypeAssert($app))
            ->bind('feedAtomSingle')
        ;
        $app->get('/{contentTypeName}/rss/feed.{extension}', [$this, 'feed'])
            ->assert('extension', '(rss|xml)')
            ->assert('contentTypeName', $this->getContentTypeAssert($app))
            ->bind('feedRssSingle')
        ;

        $app->get('/{contentTypeName}/json/feed.json', [$this, 'json'])
            ->assert('contentTypeName', $this->getContentTypeAssert($app))
            ->bind('feedJsonSingle')
        ;

        return $ctr;
    }

    /**
     * @param Application $app
     * @param string      $contentTypeName
     *
     * @return Response
     */
    public function atom(Application $app, $contentTypeName = null)
    {
        $atom = $app['rssfeed.generator']->getAtom($contentTypeName);

        $response = new Response($atom, Response::HTTP_OK);
        $response->setCharset('utf-8')
            ->setPublic()
            ->setSharedMaxAge(3600)
            ->headers->set('Content-Type', 'application/atom+xml;charset=UTF-8')
        ;

        return $response;
    }

    /**
     * @param Application $app
     * @param string      $contentTypeName
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
            ->headers->set('Content-Type', 'application/rss+xml;charset=UTF-8')
        ;

        return $response;
    }

    /**
     * @param Application $app
     * @param string      $contentTypeName
     *
     * @return Response
     */
    public function json(Application $app, $contentTypeName = null)
    {
        $json = $app['rssfeed.generator']->getJson($contentTypeName);

        // Put our own JSON together, instead of using JSONResponse, because pretty printing is nice.
        $json = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $response = new Response($json, Response::HTTP_OK);
        $response->setCharset('utf-8')
            ->setPublic()
            ->setSharedMaxAge(3600)
            ->headers->set('Content-Type', 'application/json;charset=UTF-8')
        ;

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
