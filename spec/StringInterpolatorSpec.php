<?php

describe('VisualCraft\\Utils\\StringInterpolator\\StringInterpolator', function() {
    $samples = [
        ['test test test', 'test test test', 'test test test', []],
        ['test $foo test', 'test boo test', 'test foo_var test', ['foo']],
        [' $foo $goo ', ' boo fff ', ' foo_var goo_var ', ['foo', 'goo']],
        ['$foo $goo $foo', 'boo fff boo', 'foo_var goo_var foo_var', ['foo', 'goo']],
        ['${foo}', 'boo', 'foo_var', ['foo']],
        ['${foo}${goo}', 'boofff', 'foo_vargoo_var', ['foo', 'goo']],
        ['ff${foo}test', 'ffbootest', 'fffoo_vartest', ['foo']],
        ['\$foo', '$foo', '$foo', []],
        ['${foo}\$foo', 'boo$foo', 'foo_var$foo', ['foo']],
        ['$fö', 'value', 'fö_var', ['fö']],
        ['${fö}', 'value', 'fö_var', ['fö']],
        ['${go⤗o}', '${go⤗o}', '${go⤗o}', []],
        ['test$%&test', 'test$%&test', 'test$%&test', []],
        ['test \\\\$foo test \\\\${goo}', 'test \boo test \fff', 'test \foo_var test \goo_var', ['foo', 'goo']],
        ['\\\\\\$foo', '\\\\$foo', '\\\\$foo', []],
        ['\\\\\\\\$foo', '\\\\\\boo', '\\\\\\foo_var', ['foo']],
        [['$foo', 'test', ' $goo'], ['boo', 'test', ' fff'], ['foo_var', 'test', ' goo_var'], ['foo', 'goo']],
    ];
    $should = function () {
        $args = func_get_args();

        for ($i = 1, $argsCount = count($args); $i < $argsCount; $i++) {
            $args[$i] = json_encode($args[$i], JSON_UNESCAPED_UNICODE);
        }

        return 'should ' . call_user_func_array('sprintf', $args);
    };

    beforeEach(function () {
        $this->interpolator = new \VisualCraft\Utils\StringInterpolator\StringInterpolator();
    });

    describe('->interpolate() with array', function() use ($samples, $should) {
        beforeEach(function () {
            $this->interpolate = function ($subject) {
                return $this->interpolator->interpolate($subject, [
                    'foo' => 'boo',
                    'goo' => 'fff',
                    'test' => 'tset',
                    'fö' => 'value',
                ]);
            };
        });

        foreach ($samples as $sample) {
            it($should("return '%s' for '%s'", $sample[1], $sample[0]), function() use ($sample) {
                expect($this->interpolate($sample[0]))->toBe($sample[1]);
            });
        }

        foreach (['$fo' => 'fo', '${bo}' => 'bo', 'test $fo test' => 'fo', '$föo' => 'föo', '${föo}' => 'föo'] as $arg => $name) {
            it($should("throw exception if called with: '%s'", $arg), function() use ($arg, $name) {
                expect(function () use ($arg) {
                    $this->interpolate($arg);
                })->toThrow(new \VisualCraft\Utils\StringInterpolator\MissingVariableException(sprintf("Missing variable '%s'.", $name)));
            });
        }
    });

    describe('->interpolate() with callable', function() use ($samples, $should) {
        beforeEach(function () {
            $this->interpolate = function ($subject) {
                return $this->interpolator->interpolate($subject, function ($name) {
                    return $name . '_var';
                });
            };
        });

        foreach ($samples as $sample) {
            it($should("return '%s' for '%s'", $sample[2], $sample[0]), function() use ($sample) {
                expect($this->interpolate($sample[0]))->toBe($sample[2]);
            });
        }
    });

    describe('->getNames()', function() use ($samples, $should) {
        beforeEach(function () {
            $this->getNames = function ($subject) {
                return $this->interpolator->getNames($subject);
            };
        });

        foreach ($samples as $sample) {
            it($should("return '%s' for '%s'", $sample[3], $sample[0]), function() use ($sample) {
                expect($this->getNames($sample[0]))->toBe($sample[3]);
            });
        }
    });
});
