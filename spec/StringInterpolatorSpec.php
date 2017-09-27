<?php

describe('VisualCraft\\Utils\\StringInterpolator\\StringInterpolator', function() {
    $this->samples = [
        [
            'test test test',
            'test test test',
            'test test test',
            [],
        ],
        [
            'test $foo test',
            'test boo test',
            'test foo_var test',
            ['foo'],
        ],
        [
            ' $foo $goo ',
            ' boo fff ',
            ' foo_var goo_var ',
            ['foo', 'goo'],
        ],
        [
            '$foo $goo $foo',
            'boo fff boo',
            'foo_var goo_var foo_var',
            ['foo', 'goo'],
        ],
        [
            '${foo}',
            'boo',
            'foo_var',
            ['foo'],
        ],
        [
            '${foo}${goo}',
            'boofff',
            'foo_vargoo_var',
            ['foo', 'goo'],
        ],
        [
            'ff${foo}test',
            'ffbootest',
            'fffoo_vartest',
            ['foo'],
        ],
        [
            '\\$foo',
            '$foo',
            '$foo',
            [],
        ],
        [
            '${foo}\\$foo',
            'boo$foo',
            'foo_var$foo',
            ['foo'],
        ],
        [
            '$fö',
            'value',
            'fö_var',
            ['fö'],
        ],
        [
            '${fö}',
            'value',
            'fö_var',
            ['fö'],
        ],
        [
            '${go⤗o}',
            '${go⤗o}',
            '${go⤗o}',
            [],
        ],
        [
            'test$%&test',
            'test$%&test',
            'test$%&test',
            [],
        ],
        [
            'test \\\\$foo test \\\\${goo}',
            'test \boo test \fff',
            'test \foo_var test \goo_var',
            ['foo', 'goo'],
        ],
        [
            '\\\\\\$foo',
            '\\\\$foo',
            '\\\\$foo',
            [],
        ],
        [
            '\\\\\\\\$foo',
            '\\\\\\boo',
            '\\\\\\foo_var',
            ['foo'],
        ],
        [
            ['$foo', 'test', ' $goo'],
            ['boo', 'test', ' fff'],
            ['foo_var', 'test', ' goo_var'],
            ['foo', 'goo'],
        ],
    ];
    $this->variables = [
        'foo' => 'boo',
        'goo' => 'fff',
        'test' => 'tset',
        'fö' => 'value',
    ];
    $this->interpolationCallable = function ($name) {
        return $name . '_var';
    };
    $this->should = function () {
        $args = func_get_args();

        for ($i = 1, $argsCount = count($args); $i < $argsCount; $i++) {
            $args[$i] = json_encode($args[$i], JSON_UNESCAPED_UNICODE);
        }

        return 'should ' . call_user_func_array('sprintf', $args);
    };

    beforeEach(function () {
        $this->interpolator = new \VisualCraft\Utils\StringInterpolator\StringInterpolator();
    });


    describe('->interpolate()', function() {
        describe('with variables', function() {
            foreach ($this->samples as list($sample, $expected)) {
                it($this->should("return '%s' for '%s'", $expected, $sample), function() use ($sample, $expected) {
                    expect($this->interpolator->interpolate($sample, $this->variables))->toBe($expected);
                });
            }

            foreach (['$fo' => 'fo', '${bo}' => 'bo', 'test $fo test' => 'fo', '$föo' => 'föo', '${föo}' => 'föo'] as $arg => $name) {
                it($this->should("throw exception if called with: '%s'", $arg), function() use ($arg, $name) {
                    expect(function () use ($arg) {
                        $this->interpolator->interpolate($arg, $this->variables);
                    })->toThrow(new \VisualCraft\Utils\StringInterpolator\MissingVariableException(sprintf("Missing variable '%s'.", $name)));
                });
            }
        });

        describe('with callable', function() {
            foreach ($this->samples as list($sample, $_, $expected)) {
                it($this->should("return '%s' for '%s'", $expected, $sample), function() use ($sample, $expected) {
                    expect($this->interpolator->interpolate($sample, $this->interpolationCallable))->toBe($expected);
                });
            }

            it("should throw exception if callable returns null", function() {
                expect(function () {
                    $this->interpolator->interpolate('$foo', function () {
                        return null;
                    });
                })->toThrow(new \VisualCraft\Utils\StringInterpolator\MissingVariableException(sprintf("Missing variable '%s'.", 'foo')));
            });
        });

        describe('with invalid type', function() {
            it("should throw exception if called with: not supported 2nd argument", function() {
                expect(function () {
                    $this->interpolator->interpolate('', 'foo');
                })->toThrow(new \InvalidArgumentException("Argument 'variablesOrCallable' should be array or callable but 'string' is given."));
            });
        });
    });

    describe('->getNames()', function() {
        foreach ($this->samples as list($sample, $_, $_, $expected)) {
            it($this->should("return '%s' for '%s'", $expected, $sample), function() use ($sample, $expected) {
                expect($this->interpolator->getNames($sample))->toBe($expected);
            });
        }
    });
});
