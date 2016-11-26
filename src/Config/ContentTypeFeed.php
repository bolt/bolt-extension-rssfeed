<?php

namespace Bolt\Extension\Bolt\RssFeed\Config;

/**
 * ContentType feed configuration.
 *
 * @author Gawain Lynch <gawain.lynch@gmail.com>
 */
class ContentTypeFeed
{
    /** @var bool */
    protected $enabled;
    /** @var int */
    protected $feedRecords;
    /** @var string */
    protected $atomTemplate;
    /** @var string */
    protected $feedTemplate;
    /** @var int */
    protected $contentLength;

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $config += $this->getDefaults();

        $this->enabled = $config['enabled'];
        $this->feedRecords = $config['feed_records'];
        $this->atomTemplate = $config['atom_template'];
        $this->feedTemplate = $config['feed_template'];
        $this->contentLength = $config['content_length'];
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param boolean $enabled
     *
     * @return ContentTypeFeed
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return int
     */
    public function getFeedRecords()
    {
        return $this->feedRecords;
    }

    /**
     * @param int $feedRecords
     *
     * @return ContentTypeFeed
     */
    public function setFeedRecords($feedRecords)
    {
        $this->feedRecords = (int) $feedRecords;

        return $this;
    }

    /**
     * @return string
     */
    public function getAtomTemplate()
    {
        return $this->atomTemplate;
    }

    /**
     * @param string $feedTemplate
     *
     * @return ContentTypeFeed
     */
    public function setAtomTemplate($feedTemplate)
    {
        $this->atomTemplate = $feedTemplate;

        return $this;
    }

    /**
     * @return string
     */
    public function getFeedTemplate()
    {
        return $this->feedTemplate;
    }

    /**
     * @param string $feedTemplate
     *
     * @return ContentTypeFeed
     */
    public function setFeedTemplate($feedTemplate)
    {
        $this->feedTemplate = $feedTemplate;

        return $this;
    }

    /**
     * @return int
     */
    public function getContentLength()
    {
        return $this->contentLength;
    }

    /**
     * @param int $contentLength
     *
     * @return ContentTypeFeed
     */
    public function setContentLength($contentLength)
    {
        $this->contentLength = (int) $contentLength;

        return $this;
    }

    /**
     * @return array
     */
    protected function getDefaults()
    {
        return [
            'enabled'        => false,
            'feed_records'   => 10,
            'atom_template'  => 'atom.twig',
            'feed_template'  => 'rss.twig',
            'content_length' => 0,
        ];
    }
}
