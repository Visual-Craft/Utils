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
     * @throws \InvalidArgumentException
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
            if (isset($matches['escape'])) {
                if (strlen($matches['escape']) % 2 !== 0) {
                    return substr($matches[0], 1);
                }

                $prefix = substr($matches['escape'], 1);
            } else {
                $prefix = '';
            }

            if ($callable !== null) {
                $value = $callable($matches['key']);
            } elseif (isset($variables[$matches['key']])) {
                $value = $variables[$matches['key']];
            } else {
                $value = null;
            }

            if ($value === null) {
                throw new MissingVariableException(sprintf("Missing variable '%s'.", $matches['key']));
            }

            return $prefix . $value;
        }, $subject);
    }

    /**
     * @param string|string[] $subject
     *
     * @return array
     */
    public function getNames($subject)
    {
        return array_keys($this->getCounts($subject));
    }

    /**
     * @param string|string[] $subject
     *
     * @return array
     */
    public function getCounts($subject)
    {
        $result = [];

        preg_replace_callback($this->regexp, function ($matches) use (&$result) {
            if (!isset($matches['escape']) || strlen($matches['escape']) % 2 === 0) {
                if (!isset($result[$matches['key']])) {
                    $result[$matches['key']] = 1;
                } else {
                    $result[$matches['key']]++;
                }
            }
        }, $subject);

        return $result;
    }
}
