<?php

namespace VisualCraft\Utils\StringInterpolator;

class StringInterpolator
{
    private $regexp =
        '/
            (?<escape>(\\\\)+)?
            \$
            (\{)?
                (?<key>\w+)
            (?(3)\})
        /ux'
    ;

    /**
     * @param string|string[] $subject
     * @param array $variables
     *
     * @return string|string[]
     *
     * @throws MissingVariableException
     */
    public function interpolate($subject, $variables)
    {
        return preg_replace_callback($this->regexp, function ($matches) use ($variables) {
            $escape = isset($matches['escape']) ? $matches['escape'] : '';
            $escapeLength = $escape !== '' ? strlen($escape) : 0;

            if ($escapeLength % 2 !== 0) {
                return substr($matches[0], 1);
            }

            if (isset($variables[$matches['key']])) {
                return substr($escape, 0, $escapeLength - 1) . $variables[$matches['key']];
            }

            throw new MissingVariableException(sprintf('Missing variable "%s".', $matches['key']));
        }, $subject);
    }

    /**
     * @param string|string[] $subject
     *
     * @return array
     */
    public function getNames($subject)
    {
        $names = [];

        preg_replace_callback($this->regexp, function ($matches) use (&$names) {
            $escapeLength = isset($matches['escape']) ? strlen($matches['escape']) : 0;

            if ($escapeLength % 2 === 0) {
                $names[$matches['key']] = true;
            }
        }, $subject);

        return array_keys($names);
    }
}
