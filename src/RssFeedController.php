<?php

namespace Bolt\Extension\Bolt\RssFeed;

use Bolt\Storage\EntityManager;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * RSSFeed Controller
 *
 * @author Patrick van Kouteren <info@wedesignit.nl>
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class RssFeedController implements ControllerProviderInterface
{
    /** @var Application $app */
    private $app;
    /** @var array $config */
    private $config;

    public function __construct(Application $app, array $config)
    {
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        /** @var $ctr ControllerCollection */
        $ctr = $app['controllers_factory'];

        // Sitewide feed
        $this->app->match('/rss/feed.{extension}', [$this, 'feed'])
            ->assert('extension', '(xml|rss)')
        ;

        // ContentType specific feed(s)
        $this->app->match('/{contenttypeslug}/rss/feed.{extension}', [$this, 'feed'])
            ->assert('extension', '(xml|rss)')
            ->assert('contenttypeslug', $this->getContentTypeAssert())
        ;

        return $ctr;
    }

    /**
     * @param string $contenttypeslug
     *
     * @return Response
     */
    public function feed($contenttypeslug = null)
    {
        /** @var EntityManager $storage */
        $storage = $this->app['storage'];
        /** @var \Twig_Loader_Filesystem $twigFilesystem */
        $twigFilesystem = $this->app['twig.loader.filesystem'];
        /** @var \Bolt\Render $renderer */
        $renderer = $this->app['render'];

        // Defaults for later
        $defaultFeedRecords = 5;
        $defaultTemplate = 'rss.twig';
        $content = [];
        $contentTypes = [];

        // If we're on the front page, use sitewide configuration
        $contentTypeName = $contenttypeslug ?: 'sitewide';

        if (!isset($this->config[$contentTypeName]['enabled'])
            || $this->config[$contentTypeName]['enabled'] != 'true'
        ) {
            $this->app->abort(404, "Feed for '$contentTypeName' not found.");
        }

        // Better safe than sorry: abs to prevent negative values
        $amount = (int) abs((!empty($this->config[$contentTypeName]['feed_records'])
            ? $this->config[$contentTypeName]['feed_records']
            : $defaultFeedRecords))
        ;
        // How much to display in the description. Value of 0 means full body!
        $contentLength = (int) abs(
            (!empty($this->config[$contentTypeName]['content_length'])
                ? $this->config[$contentTypeName]['content_length']
                : 0
            )
        );

        // Get our content
        if ($contentTypeName == 'sitewide') {
            $sitewideContentTypes = (array) $this->config['sitewide'];
            foreach ($sitewideContentTypes as $types) {
                $contentTypes[] = $storage->getContentType($types);
            }
        } else {
            $contentTypes[] = $storage->getContentType($contentTypeName);
        }

        // Get content for each contentType we've selected as an assoc. array
        // by content type
        foreach ($contentTypes as $contentType) {
            $content[$contentType['slug']] = $storage->getContent(
                $contentType['slug'],
                ['limit' => $amount, 'order' => 'datepublish desc']
            );
        }

        // Now narrow our content array to $amount based on date
        $tmp = [];
        foreach ($content as $slug => $recordid) {
            if (!empty($recordid)) {
                foreach ($recordid as $record) {
                    $key = strtotime($record->values['datepublish']) . $record->values['id'] . $slug;
                    $tmp[$key] = $record;
                }
            }
        }

        // Sort the array and return a reduced one
        krsort($tmp);
        $content = array_slice($tmp, 0, $amount);

        if (!$content) {
            $this->app->abort(404, "Feed for '$contentTypeName' not found.");
        }

        // Then, select which template to use, based on our
        // 'cascading templates rules'
        if (!empty($this->config[$contentTypeName]['feed_template'])) {
            $template = $this->config[$contentTypeName]['feed_template'];
        } else {
            $template = $defaultTemplate;
        }

        $twigFilesystem->addPath(dirname(__DIR__) . '/templates/');
        $body = $renderer->render($template, [
            'records'        => $content,
            'content_length' => $contentLength,
            $contentTypeName => $content,
        ]);

        $response = new Response($body, Response::HTTP_OK);
        $response->setCharset('utf-8')
            ->setPublic()
            ->setSharedMaxAge(3600)
            ->headers->set('Content-Type', 'application/rss+xml;charset=UTF-8');

        return $response;
    }

    /**
     * Get a value to use in 'assert() with the available ContentTypes
     *
     * @param boolean $includeSingular
     *
     * @return string
     */
    private function getContentTypeAssert($includeSingular = false)
    {
        $slugs = [];
        foreach ($this->app['config']->get('contenttypes') as $type) {
            $slugs[] = $type['slug'];
            if ($includeSingular) {
                $slugs[] = $type['singular_slug'];
            }
        }

        return implode('|', $slugs);
    }
}
