<?php

namespace VisualCraft\Utils\StringInterpolator;

class StringInterpolator
{
    private $regexp =
        '/
            (?<escape>(\\\\)+)?
            \$
            (\{)?
                (?<key>[\w\p{L}]+)
            (?(3)\})
        /ux'
    ;

    /**
     * @param string|string[] $subject
     * @param array|callable $variablesOrCallable
     *
     * @return string|string[]
     *
     * @throws MissingVariableException
     */
    public function interpolate($subject, $variablesOrCallable)
    {
        $variables = null;
        $callable = null;

        if (is_array($variablesOrCallable)) {
            $variables = $variablesOrCallable;
        } elseif (is_callable($variablesOrCallable)) {
            $callable = $variablesOrCallable;
        } else {
            throw new \InvalidArgumentException(sprintf(
                "Argument 'variablesOrCallable' should be array or callable but '%s' is given.",
                is_object($variablesOrCallable) ? get_class($variablesOrCallable) : gettype($variablesOrCallable)
            ));
        }

        return preg_replace_callback($this->regexp, function ($matches) use ($variables, $callable) {
            $escape = isset($matches['escape']) ? $matches['escape'] : '';
            $escapeLength = $escape !== '' ? strlen($escape) : 0;

            if ($escapeLength % 2 !== 0) {
                return substr($matches[0], 1);
            }

            $prefix = substr($escape, 0, $escapeLength - 1);

            if ($callable) {
                return $prefix . $callable($matches['key']);
            } else {
                if (isset($variables[$matches['key']])) {
                    return $prefix . $variables[$matches['key']];
                }

                throw new MissingVariableException(sprintf("Missing variable '%s'.", $matches['key']));
            }
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
