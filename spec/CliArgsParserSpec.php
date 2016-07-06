<?php

describe('VisualCraft\\Utils\\CliArgsParser\\CliArgsParser', function() {
    $samples = [
        [[''], ['', [], []]],
        [['/path'], ['/path', [], []]],
        [['/path', 'a', 'b', 'c'], ['/path', ['a', 'b', 'c'], []]],
        [['/path', '-a', '-b', '-c'], ['/path', [], ['a' => true, 'b' => true, 'c' => true]]],
        [['/path', '--foo', '--boo', '--goo'], ['/path', [], ['foo' => true, 'boo' => true, 'goo' => true]]],
        [['/path', '-a1', '-b3', '-c2'], ['/path', [], ['a' => '1', 'b' => '3', 'c' => '2']]],
        [['/path', '--foo=1', '--boo=2', '--goo=3'], ['/path', [], ['foo' => '1', 'boo' => '2', 'goo' => '3']]],
        [['/path', '-f', '--foo', 'boo'], ['/path', ['boo'], ['f' => true, 'foo' => true]]],
        [['/path', '-f', '--foo', '--', '--boo', 'goo', '-m', '--foo'], ['/path', ['--boo', 'goo', '-m', '--foo'], ['f' => true, 'foo' => true]]],
        [['/path', '-f', '-f'], ['/path', [], ['f' => [true, true]]]],
        [['/path', '-f1', '-f2'], ['/path', [], ['f' => ['1', '2']]]],
        [['/path', '--foo', '--foo'], ['/path', [], ['foo' => [true, true]]]],
        [['/path', '--foo=a', '--foo=b'], ['/path', [], ['foo' => ['a', 'b']]]],
        [['/path', '--foo=a b c', '--foo=1 2 3'], ['/path', [], ['foo' => ['a b c', '1 2 3']]]],
    ];
    $should = function () {
        $args = func_get_args();

        for ($i = 1, $argsCount = count($args); $i < $argsCount; $i++) {
            $args[$i] = json_encode($args[$i], JSON_UNESCAPED_UNICODE);
        }

        return 'should ' . call_user_func_array('sprintf', $args);
    };

    beforeEach(function () {
        $this->parser = new \VisualCraft\Utils\CliArgsParser\CliArgsParser();
    });

    describe('->parse()', function() use ($samples, $should) {
        beforeEach(function () {
            $this->parse = function ($sample) {

                return $this->parser->parse($sample);
            };
        });

        foreach ($samples as $sample) {
            it($should("return '%s' for '%s'", $sample[1], $sample[0]), function() use ($sample) {
                expect($this->parse($sample[0]))->toBe($sample[1]);
            });
        }
    });
});
