<?php

namespace VisualCraft\Utils\StringInterpolator;

class StringInterpolator
{
    private $regexp =
        '/
            (?<escape>\\\\)?
            \$
            (\{)?
                (?<key>\w+)
            (?(2)\})
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
            if (!empty($matches['escape'])) {
                return substr($matches[0], 1);
            }

            if (isset($variables[$matches['key']])) {
                return $variables[$matches['key']];
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
            if (empty($matches['escape'])) {
                $names[$matches['key']] = true;
            }
        }, $subject);

        return array_keys($names);
    }
}
