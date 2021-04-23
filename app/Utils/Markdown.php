<?php

namespace Core\Utils;

use Parsedown;
use HTMLPurifier;
use League\HTMLToMarkdown\HtmlConverter;

class Markdown
{
    /**
     * Markdown to html parser.
     *
     * @var \Parsedown
     */
    protected $parsedown;

    /**
     * Html to Markdown parser.
     *
     * @var \League\HTMLToMarkdown\HtmlConverter
     */
    protected $htmlConverter;

    /**
     * Create the Markdown util instance.
     *
     * @param \Parsedown $parsedown
     * @param \League\HTMLToMarkdown\HtmlConverter $htmlConverter
     * @author GEO <dev@kaifa.me>
     */
    public function __construct(Parsedown $parsedown, HtmlConverter $htmlConverter)
    {
        $this->parsedown = $parsedown;
        $this->htmlConverter = $htmlConverter;
    }

    /**
     * Markdown to html.
     *
     * @param string $markdown
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function toHtml(string $markdown): string
    {
        return $this->parsedown->parse($markdown);
    }

    /**
     * Html to markdown.
     *
     * @param string $html
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function toMarkdown(string $html): string
    {
        return $this->htmlConverter->convert($html);
    }

    /**
     * Get safety markdown string.
     *
     * @param string $markdown
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function safetyMarkdown(string $markdown): string
    {
        $html = $this->toHtml($markdown);
        $html = $this->safetyHtml($html);

        return $this->toMarkdown($html);
    }

    /**
     * Get safety html string.
     *
     * @param string $html
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function safetyHtml(string $html): string
    {
        return $this->filterHtml($html);
    }

    /**
     * Filter html.
     *
     * @param string $html
     * @return string
     * @author GEO <dev@kaifa.me>
     */
    public function filterHtml(string $html): string
    {
        return app(HTMLPurifier::class)->purify($html);
    }
}
