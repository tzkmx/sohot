<?php

namespace Jefrancomix\Sohot\Test;

use \PHPUnit\Framework\TestCase;
use function Jefrancomix\Sohot\{hashMapper, identity, head, compose};

class FunctionsTest extends TestCase
{
    public function testIdentity()
    {
        $id = new \DateTime();

        $maybeSameId = identity($id);

        $this->assertSame($id, $maybeSameId);
    }

    public function testHead()
    {
        $list = [1, 2, 3];

        $first = head($list);

        $this->assertEquals(1, $first);
    }

    public function testCompose()
    {
        $list = [
            ['A', 'B'],
            2,
            3
        ];

        $composedFn = compose('Jefrancomix\Sohot\head', 'Jefrancomix\Sohot\head');

        $this->assertTrue(is_callable($composedFn));

        $actual = $composedFn($list);

        $this->assertEquals('A', $actual);
    }

    public function testHashMapperFunctor()
    {
        $list = [
            [
                'head' => 1,
                'tail' => 2
            ],
            [
                'head' => 3,
                'tail' => 4
            ],
        ];
        $mapper = hashMapper(['head' => 'fst', 'tail' => 'snd']);

        $expectedFirstItemMapped = [ 'fst' => 1, 'snd' => 2];

        $this->assertEquals($expectedFirstItemMapped, $mapper($list[0]));

        $expectedTransformedList = [ [ 'fst' => 1, 'snd' => 2], [ 'fst' => 3, 'snd' => 4] ];

        $listMapper = $mapper->getCollectionMapper();

        $this->assertEquals($expectedTransformedList, $listMapper($list));
    }
}
