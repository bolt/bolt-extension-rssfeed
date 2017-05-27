<?php

namespace Bolt\Extension\Bolt\RssFeed\Config;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Base extension configuration.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class Config
{
    /** @var ContentTypeFeed[] */
    protected $contentTypeFeed;
    /** @var SiteWideFeed */
    protected $siteWideFeed;

    protected $feeds;
    protected $autodiscovery;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        foreach ($config as $name => $values) {
            if ($name === 'sitewide') {
                $this->siteWideFeed = new SiteWideFeed((array) $values);
            } else if (!in_array($name, ['feeds', 'autodiscovery'])) {
                $this->contentTypeFeed[$name] = new ContentTypeFeed((array) $values);
            }
        }
        $this->feeds = (array) $config['feeds'];
        $this->autodiscovery = (boolean) $config['autodiscovery'];
    }

    /**
     * @return ContentTypeFeed[]
     */
    public function getContentTypeFeeds()
    {
        return $this->contentTypeFeed;
    }

    /**
     * @param $contentTypeName
     *
     * @return ContentTypeFeed
     */
    public function getContentTypeFeed($contentTypeName)
    {
        if (isset($this->contentTypeFeed[$contentTypeName])) {
            return $this->contentTypeFeed[$contentTypeName];
        }
        if ($this->siteWideFeed->has($contentTypeName)) {
            return $this->siteWideFeed;
        }

        throw new HttpException(Response::HTTP_NOT_FOUND, sprintf('Feed for "%s" not found.', $contentTypeName));
    }

    /**
     * @param string $name
     * @param array  $values
     *
     * @return Config
     */
    public function setContentTypeFeed($name, array $values)
    {
        $this->contentTypeFeed[$name] = $values;

        return $this;
    }

    /**
     * @return SiteWideFeed
     */
    public function getSiteWideFeed()
    {
        return $this->siteWideFeed;
    }

    /**
     * @param SiteWideFeed $siteWideFeed
     *
     * @return Config
     */
    public function setSiteWideFeed($siteWideFeed)
    {
        $this->siteWideFeed = $siteWideFeed;

        return $this;
    }

    /**
     * @return array
     */
    public function getEnabledFeeds()
    {
        return $this->feeds;
    }

    /**
     * @return boolean
     */
    public function getAutodiscovery()
    {
        return $this->autodiscovery;
    }
}
