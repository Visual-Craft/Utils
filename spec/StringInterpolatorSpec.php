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
    ];

    beforeEach(function () {
        $this->interpolator = new \VisualCraft\Utils\StringInterpolator\StringInterpolator();
    });

    describe('->interpolate() with array', function() use ($samples) {
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
            it("should return '{$sample[1]}' for '{$sample[0]}'", function() use ($sample) {
                expect($this->interpolate($sample[0]))->toBe($sample[1]);
            });
        }

        foreach (['$fo' => 'fo', '${bo}' => 'bo', 'test $fo test' => 'fo', '$föo' => 'föo', '${föo}' => 'föo'] as $arg => $name) {
            it("should throw exception if called with: '{$arg}'", function() use ($arg, $name) {
                expect(function () use ($arg) {
                    $this->interpolate($arg);
                })->toThrow(new \VisualCraft\Utils\StringInterpolator\MissingVariableException(sprintf("Missing variable '%s'.", $name)));
            });
        }
    });

    describe('->interpolate() with callable', function() use ($samples) {
        beforeEach(function () {
            $this->interpolate = function ($subject) {
                return $this->interpolator->interpolate($subject, function ($name) {
                    return $name . '_var';
                });
            };
        });

        foreach ($samples as $sample) {
            it("should return '{$sample[2]}' for '{$sample[0]}'", function() use ($sample) {
                expect($this->interpolate($sample[0]))->toBe($sample[2]);
            });
        }
    });

    describe('->getNames()', function() use ($samples) {
        beforeEach(function () {
            $this->getNames = function ($subject) {
                return $this->interpolator->getNames($subject);
            };
        });

        foreach ($samples as $sample) {
            $return = json_encode($sample[3]);
            it("should return '{$return}' for '{$sample[0]}'", function() use ($sample) {
                expect($this->getNames($sample[0]))->toBe($sample[3]);
            });
        }
    });
});
