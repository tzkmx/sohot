<?php

namespace Jefrancomix\Sohot\Test;
use PHPUnit\Framework\TestCase;
use Jefrancomix\Sohot\HashmapTransformer as HT;

class SimpleTransformationTest extends TestCase
{
    public function testSimplestTransform()
    {
        $ht = new HT(['origin' => 'roots']);

        $origin = [ 'origin' => 'Africa' ];
        $expected = ['roots' => 'Africa'];

        $transformed = $ht->transform($origin);

        $this->assertEquals($expected, $transformed);
    }

    /**
     * @dataProvider examplesOfCallbackTransforms
     */
    public function testCallableTransform($source, $transformRules, $expected)
    {
        $ht = new HT($transformRules);

        $transformed = $ht->transform($source);

        $this->assertEquals($expected, $transformed);
    }
    public function examplesOfCallbackTransforms()
    {
        return [
            'implode' => [
                [ 'tribe' => 'Mexica', 'roots' => ['Chichimeca','Acolhua'] ],
                [
                    'tribe' => 'nation',
                    'roots' => ['mixOf', function($subkey) {
                        return implode(', ', $subkey);
                    }],
                ],
                [ 'nation' => 'Mexica', 'mixOf' => 'Chichimeca, Acolhua' ],
            ],
            'sprintf' => [
                [
                    'place' => 'San Salvador Atenco',
                    'date' => [ 'year' => 2006, 'month' => 5, 'day' => 4, ],
                ],
                [
                    'place' => 'Caso CIDH',
                    'date' => [
                        'fecha',
                        function($date) {
                            extract($date);
                            $date = \DateTime::createFromFormat('Y/m/d', "{$year}/{$month}/{$day}");
                            return $date->format('Y-m-d');
                        }
                    ],
                ],
                [
                    'Caso CIDH' => 'San Salvador Atenco',
                    'fecha' => '2006-05-04',
                ],
            ],
        ];
    }


    public function testCallablesCalledWithSubkeyAndHashmap()
    {
        $mockAux = $this->getMockBuilder(stdClass::class)
            ->setMethods(['getBirth', 'getDeath'])
            ->getMock();

        $source = [
            'name' => 'Innocent Child',
            'birth' => [ 'date' => '1981 '],
            'death' => [ 'date' => '2006' ],
        ];
        $expectedTarget = [
            'alias' => 'Innocent Child',
            'yearOfBirth' => 1981,
            'yearOfDeath' => 2006,
        ];
        $ht = new HT([
            'name' => 'alias',
            'birth' => [ 'yearOfBirth', [$mockAux, 'getBirth'] ],
            'death' => [ 'yearOfDeath', [$mockAux, 'getDeath'] ],
        ]);

        $mockAux->expects($this->once())
            ->method('getBirth')
            ->with(
                $this->equalTo($source['birth']),
                $this->equalTo($source)
            )->willReturn(1981);

        $mockAux->expects($this->once())
            ->method('getDeath')
            ->with(
                $this->equalTo($source['death']),
                $this->equalTo($source)
            )->willReturn(2006);

        $target = $ht->transform($source);

        $this->assertEquals($expectedTarget, $target);
    }
}
