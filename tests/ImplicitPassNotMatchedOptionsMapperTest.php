<?php

namespace Jefrancomix\Sohot\Test;
use PHPUnit\Framework\TestCase;
use Jefrancomix\Sohot\HashmapMapper as HM;

class ImplicitPassNotMatchedOptionsMapperTest extends TestCase
{
    /**
     * @dataProvider provideExamplesExplicitAndImplicitPass
     */
    public function testNotMatchedOptionsSimplyPassToTargetHashmap($source, $expected, $passNotMatchedKeys)
    {
        $mapper = new HM([
            'matched' => ['matchedReverse', function($array) {
                return array_reverse($array);
            }],
            'matchedMap' => ['matchedToLowercase', function($array) {
                return array_map(function($letter) {
                    return strtolower($letter);
                }, $array);
            }],
        ], ['passNotMatchedKeys' => $passNotMatchedKeys]);

        $this->assertEquals($expected, $mapper->map($source));
    }

    public function provideExamplesExplicitAndImplicitPass()
    {
        $date = new \DateTimeImmutable();
        return [
            [
                [
                    'matched' => [1, 2, 3],
                    'matchedMap' => ['A', 'B', 'C'],
                    'notMatched' => 'passes simply',
                    'date' => $date,
                ],
                [
                    'matchedReverse' => [3, 2, 1],
                    'matchedToLowercase' => ['a', 'b', 'c'],
                    'notMatched' => 'passes simply',
                    'date' => $date,
                ],
                true,
            ],
            [
                [
                    'matched' => [1, 2, 3],
                    'matchedMap' => ['A', 'B', 'C'],
                    'notMatched' => 'passes simply',
                    'date' => $date,
                ],
                [
                    'matchedReverse' => [3, 2, 1],
                    'matchedToLowercase' => ['a', 'b', 'c'],
                ],
                false,
            ],
        ];

    }

}
