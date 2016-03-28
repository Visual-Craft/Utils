<?php

describe('VisualCraft\\Utils\\TextBlockManager\\TextBlockManager', function() {
    beforeEach(function () {
        $this->getManager = function ($marker) {
            return new \VisualCraft\Utils\TextBlockManager\TextBlockManager($marker);
        };
        $this->manager = $this->getManager('marker');
    });

    describe('->__construct()', function() {
        it('should reject marker with invalid characters', function () {
            expect(function () {
                $this->getManager('>');
            })->toThrow(new \InvalidArgumentException("Marker should contain only letters, digits and '_', but '>' given."));
        });
    });

    describe('->update()', function() {
        $samples = [
//            [
//                "message",
//                "content",
//                "block content",
//                "expected",
//                ?"marker",
//            ],
            [
                'should correctly add block to empty content',
                '',
                'block content',
                <<<EXPECTED

# <marker>
block content
# </marker>

EXPECTED
                ,
            ],
            [
                'should correctly add block to existing content',
                <<<CONTENT
existing
content
CONTENT
                ,
                'block content',
                <<<EXPECTED
existing
content
# <marker>
block content
# </marker>

EXPECTED
                ,
            ],
            [
                'should correctly update block at beginning',
                <<<CONTENT
# <marker>
block content
# </marker>
other
content

CONTENT
                ,
                'updated block',
                <<<EXPECTED
# <marker>
updated block
# </marker>
other
content

EXPECTED
                ,
            ],
            [
                'should correctly update block in middle',
                <<<CONTENT
existing
content
# <marker>
block content
# </marker>
other
content

CONTENT
                ,
                'updated block',
                <<<EXPECTED
existing
content
# <marker>
updated block
# </marker>
other
content

EXPECTED
                ,
            ],
            [
                'should correctly update block at the end',
                <<<CONTENT
existing
content
# <marker>
block content
# </marker>

CONTENT
                ,
                'updated block',
                <<<EXPECTED
existing
content
# <marker>
updated block
# </marker>

EXPECTED
                ,
            ],
            [
                'should correctly update duplicated blocks',
                <<<CONTENT
existing
content
# <marker>
block content
# </marker>
# <marker>
block content 2
# </marker>
other
content

CONTENT
                ,
                'updated block',
                <<<EXPECTED
existing
content
# <marker>
updated block
# </marker>
other
content

EXPECTED
                ,
            ],
            [
                'should not touch existing block with other marker',
                <<<CONTENT
existing
content
# <other_marker>
other content
# </other_marker>

CONTENT
                ,
                'block content',
                <<<EXPECTED
existing
content
# <other_marker>
other content
# </other_marker>

# <marker>
block content
# </marker>

EXPECTED
                ,
            ],
            [
                'should correctly handle marker with unicode letters',
                '',
                'block content',
                <<<EXPECTED

# <märker>
block content
# </märker>

EXPECTED
                ,
                'märker',
            ],
        ];

        foreach ($samples as $sample) {
            it($sample[0], function () use ($sample) {
                /** @var \VisualCraft\Utils\TextBlockManager\TextBlockManager $manager */

                if (isset($sample[4])) {
                    $manager = $this->getManager($sample[4]);
                } else {
                    $manager = $this->manager;
                }

                expect($manager->update($sample[1], $sample[2]))->toBe($sample[3]);
            });
        }

        it('should not change content if called multiple times with same arguments', function () {
            $expected = $this->manager->update('existing content', 'block content');
            $content = $expected;
            expect($content = $this->manager->update($content, 'block content'))->toBe($expected);
            expect($content = $this->manager->update($content, 'block content'))->toBe($expected);
        });
    });

    describe("->remove()", function() {
        $samples = [
//            [
//                "message",
//                "content",
//                "expected",
//                ?"marker",
//            ],
            [
                'should not touch empty content',
                '',
                <<<EXPECTED

EXPECTED
                ,
            ],
            [
                'should correctly remove block',
                <<<CONTENT
# <marker>
block
# </marker>

CONTENT
                ,
                <<<EXPECTED

EXPECTED
                ,
            ],
            [
                'should correctly remove block at beginning',
                <<<CONTENT
# <marker>
block
# </marker>
other
content

CONTENT
                ,
                <<<EXPECTED
other
content

EXPECTED
                ,
            ],
            [
                'should correctly remove block at the end',
                <<<CONTENT
existing
content
# <marker>
block
# </marker>

CONTENT
                ,
                <<<EXPECTED
existing
content

EXPECTED
                ,
            ],
            [
                'should correctly remove block in middle',
                <<<CONTENT
existing
content
# <marker>
block
# </marker>
other
content

CONTENT
                ,
                <<<EXPECTED
existing
content
other
content

EXPECTED
                ,
            ],
            [
                'should correctly remove duplicated blocks',
                <<<CONTENT
existing
content
# <marker>
block
# </marker>
# <marker>
other block
# </marker>
other
content

CONTENT
                ,
                <<<EXPECTED
existing
content
other
content

EXPECTED
                ,
            ],
            [
                'should correctly remove block and do not touch unrelated block',
                <<<CONTENT
existing
content
# <marker>
block
# </marker>
# <other_marker>
other content
# </other_marker>
other
content

CONTENT
                ,
                <<<EXPECTED
existing
content
# <other_marker>
other content
# </other_marker>
other
content

EXPECTED
                ,
            ],
            [
                'should correctly handle marker with unicode letters',
                <<<CONTENT
existing
content
# <märker>
block
# </märker>
other
content

CONTENT
                ,
                <<<EXPECTED
existing
content
other
content

EXPECTED
                ,
                'märker',
            ],
        ];

        foreach ($samples as $sample) {
            it($sample[0], function () use ($sample) {
                /** @var \VisualCraft\Utils\TextBlockManager\TextBlockManager $manager */

                if (isset($sample[3])) {
                    $manager = $this->getManager($sample[3]);
                } else {
                    $manager = $this->manager;
                }

                expect($manager->remove($sample[1]))->toBe($sample[2]);
            });
        }

        it('should not change content if called multiple times', function () {
            $content = <<<CONTENT
existing
content
# <marker>
block
# </marker>
other
content

CONTENT;
            $expected = $this->manager->remove($content);
            $content = $expected;
            expect($content = $this->manager->remove($content))->toBe($expected);
            expect($content = $this->manager->remove($content))->toBe($expected);
        });
    });
});
