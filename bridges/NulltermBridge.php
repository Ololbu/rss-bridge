<?php

class NulltermBridge extends BridgeAbstract
{
    const NAME = 'nullterm.io Blog';
    const URI = 'https://nullterm.io';
    const DESCRIPTION = 'Returns last blogposts.';
    const MAINTAINER = 'Ololbu';
    const CACHE_TIMEOUT = '3600';

    const PARAMETERS = [
        'Blogposts' => [
            'include_contents' => [
                'type' => 'checkbox',
                'name' => 'Include contents',
                'title' => 'Includes contents of article if checked.'
            ]
        ]
    ];

    protected $html;

    public function getURI() {
        return self::URI . '/blog.html';
    }

    public function getName() {
        // FIXME: need only with generated feed
        // currently workaround with unnecessary context is used
        if ($this->queriedContext) {
            $this->html = ($this->html) ?: getSimpleHTMLDOM($this->getURI());
            return $this->html->find('title', 0)->plaintext;
        } else {
            return self::NAME;
        }
    }

    public function collectData() {
        $this->html = ($this->html) ?: getSimpleHTMLDOM($this->getURI());

        // FIXME
        // Gets first occurrence of digit-only ????-??-?? to ${1} backreference
        $timestamp_pattern = '/(?:.+)(\d{4}-\d{2}-\d{2})+?(?:.+)/';

        foreach($this->html->find('li.blog-entry') as $entry) {
            $item = [];
            $item['uri']       = self::URI . $entry->find('a', 0)->href;
            $item['title']     = $entry->find('a', 0)->plaintext;
            $item['timestamp'] = strtotime(preg_replace($timestamp_pattern, '${1}', $entry->find('a', 0)->href));
            $item['author']    = $entry->find('span.by', 0)->plaintext;
            $item['content']   = (!$this->getInput('include_contents')) ?:
                                 getSimpleHTMLDOM($item['uri'])->find('article.blog-post', 0)->innertext;
            $this->items[] = $item;
        }
    }
}
