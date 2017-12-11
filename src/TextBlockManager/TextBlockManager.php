<?php

namespace VisualCraft\Utils\TextBlockManager;

class TextBlockManager
{
    /**
     * @var string
     */
    private $regexpTemplate = '/^%2$s <%1$s>$.+^%2$s <\/%1$s>$\n?/smu';

    /**
     * @var string
     */
    private $marker;

    /**
     * @var string
     */
    private $comment;

    /**
     * @param string $marker
     * @param string $comment
     */
    public function __construct($marker, $comment = '#')
    {
        if (!preg_match('/^[\w\p{L}:\-]+$/u', $marker)) {
            throw new \InvalidArgumentException(sprintf("Marker should contain only letters, digits, '_', '-' and ':', but '%s' given.", $marker));
        }

        $this->marker = $marker;
        $this->comment = $comment;
    }

    /**
     * @param string $content
     * @param string $block
     * @return string
     */
    public function update($content, $block)
    {
        $block = $this->wrapBlock($block) . "\n";

        if ($content === '') {
            return $block;
        }

        $count = null;
        $newContent = preg_replace($this->getRegexp(), $block, $content, -1, $count);

        if ($count === 0) {
            $newContent .= "\n" . $block;
        }

        return $newContent;
    }

    /**
     * @param string $content
     * @return string
     */
    public function remove($content)
    {
        return preg_replace($this->getRegexp(), '', $content, -1);
    }

    /**
     * @return string
     */
    private function getRegexp()
    {
        return sprintf($this->regexpTemplate, preg_quote($this->marker), preg_quote($this->comment, '/'));
    }

    /**
     * @param string $block
     * @return string
     */
    private function wrapBlock($block)
    {
        return implode("\n", [
            "{$this->comment} <{$this->marker}>",
            $block,
            "{$this->comment} </{$this->marker}>"
        ]);
    }
}
