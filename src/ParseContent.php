<?php

namespace Bolt\Extension\Bolt\RssFeed;

use Bolt\Helpers\Html;
use Bolt\Storage\Entity\Content;
use Maid\Maid;

class ParseContent {

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
                'allowed-tags'    => ['a', 'b', 'br', 'div', 'hr', 'h1', 'h2', 'h3', 'h4', 'p', 'strong', 'em', 'i', 'u', 'strike', 'ul', 'ol', 'li', 'img', 'blockquote', 'cite'],
                'allowed-attribs' => ['id', 'class', 'name', 'value', 'href', 'src'],
            ]
        );

        $fieldTypes = $record->getContentType()->getFields();
        $result = '';

        foreach ($fields as $field) {
            $fieldValue = $record->get($field);
            if (!empty($fieldTypes[$field]['type']) && $fieldTypes[$field]['type'] === 'markdown' ) {
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
