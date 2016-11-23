<?php

namespace Bolt\Extension\Bolt\RssFeed\Config;

/**
 * Site-wide feed configuration.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class SiteWideFeed extends ContentTypeFeed
{
    /** @var array */
    protected $contentTypes;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->contentTypes = $config['content_types'];
    }

    /**
     * @return array
     */
    public function getContentTypes()
    {
        return $this->contentTypes;
    }

    /**
     * @param array $contentTypes
     *
     * @return SiteWideFeed
     */
    public function setContentTypes(array $contentTypes)
    {
        $this->contentTypes = $contentTypes;

        return $this;
    }
}
