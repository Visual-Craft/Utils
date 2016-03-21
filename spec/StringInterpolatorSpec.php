<?php

describe("StringInterpolator", function() {
    $samples = [
        ['test test test', 'test test test', []],
        ['test $foo test', 'test boo test', ['foo']],
        [' $foo $goo ', ' boo fff ', ['foo', 'goo']],
        ['$foo $goo $foo', 'boo fff boo', ['foo', 'goo']],
        ['${foo}', 'boo', ['foo']],
        ['${foo}${goo}', 'boofff', ['foo', 'goo']],
        ['ff${foo}test', 'ffbootest', ['foo']],
        ['\$foo', '$foo', []],
        ['${foo}\$foo', 'boo$foo', ['foo']],
        ['${go⤗o}', '${go⤗o}', []],
        ['test$%&test', 'test$%&test', []],
    ];

    beforeEach(function () {
        $this->interpolator = new \VisualCraft\Utils\StringInterpolator\StringInterpolator();
    });

    describe("::interpolate()", function() use ($samples) {
        beforeEach(function () {
            $this->interpolate = function ($subject) {
                return $this->interpolator->interpolate($subject, [
                    'foo' => 'boo',
                    'goo' => 'fff',
                    'test' => 'tset',
                ]);
            };
        });

        foreach ($samples as $sample) {
            it("should return '{$sample[1]}' for '{$sample[0]}'", function() use ($sample) {
                expect($this->interpolate($sample[0]))->toBe($sample[1]);
            });
        }

        foreach (['$fo', '${bo}', 'test $fo test'] as $arg) {
            it("should throw exception if called with: '{$arg}'", function() use ($arg) {
                expect(function () use ($arg) {
                    $this->interpolate($arg);
                })->toThrow(new \VisualCraft\Utils\StringInterpolator\MissingVariableException());
            });
        }
    });

    describe("::getNames()", function() use ($samples) {
        beforeEach(function () {
            $this->getNames = function ($subject) {
                return $this->interpolator->getNames($subject);
            };
        });

        foreach ($samples as $sample) {
            $return = json_encode($sample[2]);
            it("should return '{$return}' for '{$sample[0]}'", function() use ($sample) {
                expect($this->getNames($sample[0]))->toBe($sample[2]);
            });
        }
    });
});
