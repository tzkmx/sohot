<?php

namespace Jefrancomix\Sohot\Test;
use PHPUnit\Framework\TestCase;
use Jefrancomix\Sohot\HashmapMapper as HM;

class SimpleTransformationTest extends TestCase
{
    public function testSimplestMapping()
    {
        $hm = new HM(['origin' => 'roots']);

        $origin = [ 'origin' => 'Africa' ];
        $expected = ['roots' => 'Africa'];

        $transformed = $hm->map($origin);

        $this->assertEquals($expected, $transformed);
    }

    /**
     * @dataProvider examplesOfCallbackMappings
     */
    public function testCallableMapping($source, $transformRules, $expected)
    {
        $hm = new HM($transformRules);

        $transformed = $hm->map($source);

        $this->assertEquals($expected, $transformed);
    }
    public function examplesOfCallbackMappings()
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
        $hm = new HM([
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

        $target = $hm->map($source);

        $this->assertEquals($expectedTarget, $target);
    }
}
