<?php

namespace VisualCraft\Utils\CliArgsParser;

class CliArgsParser
{
    /**
     * @param array $argv
     * @return array
     */
    public function parse(array $argv)
    {
        $self = array_shift($argv);
        $args = [];
        $opts = [];
        $onlyArgs = false;

        foreach ($argv as $arg) {
            $len = strlen($arg);

            if ($onlyArgs || $arg[0] !== '-' || $len === 1) {
                $args[] = $arg;
            } elseif ($arg === '--') {
                $onlyArgs = true;
            } else {
                $long = $arg[1] === '-';
                $arg = ltrim($arg, '-');

                if ($long) {
                    $eqPos = strpos($arg, '=');

                    if ($eqPos === false) {
                        $name = $arg;
                        $value = true;
                    } else {
                        $name = substr($arg, 0, $eqPos);
                        $value = substr($arg, $eqPos + 1);
                    }
                } else {
                    $name = $arg[0];

                    if ($len > 2) {
                        $value = substr($arg, 1);
                    } else {
                        $value = true;
                    }
                }

                if (isset($opts[$name])) {
                    if (!is_array($opts[$name])) {
                        $opts[$name] = [$opts[$name]];
                    }

                    $opts[$name][] = $value;
                } else {
                    $opts[$name] = $value;
                }
            }
        }

        return [$self, $args, $opts];
    }
}
