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

    public function testGetMapperForCollectionOfSameShapedHashmaps()
    {
        $mapper = new HM([
            'date' => ['date_string', function($date) {
                return $date->format(DATE_ISO8601);
            }],
            'title' => ['title', function($title) {
                return $title['rendered'];
            }],
            'link' => ['url', function($slug) {
                return "http://example.com/{$slug}/";
            }]
        ]);


        $source = [
            '1' => [
                'date' => new \DateTime('2000-03-06'),
                'title' => ['rendered' => 'PFP attacks UNAM'],
                'link' => 'pfp-attacks-unam',
            ],
            '2' => [
                'date' => new \DateTime('2006-05-04'),
                'title' => ['rendered' => 'PFP attacks Atenco'],
                'link' => 'pfp-attacks-atenco',
            ],
            '3' => [
                'date' => new \DateTime('2015-09-27'),
                'title' => ['rendered' => 'Iguala mass kidnapping'],
                'link' => 'iguala-mass-kidnapping',
            ],
        ];
        $expected = [
            '1' => [
                'date_string' => '2000-03-06T00:00:00+0000',
                'title' => 'PFP attacks UNAM',
                'url' => 'http://example.com/pfp-attacks-unam/',
            ],
            '2' => [
                'date_string' => '2006-05-04T00:00:00+0000',
                'title' => 'PFP attacks Atenco',
                'url' => 'http://example.com/pfp-attacks-atenco/',
            ],
            '3' => [
                'date_string' => '2015-09-27T00:00:00+0000',
                'title' => 'Iguala mass kidnapping',
                'url' => 'http://example.com/iguala-mass-kidnapping/',
            ],
        ];
        foreach($source as $key => $item) {
            $this->assertEquals($expected[$key], $mapper->map($item));
        }
        $collectionMapper = $mapper->getCollectionMapper();
        $this->assertEquals($expected, $collectionMapper->map($source));
    }

}
