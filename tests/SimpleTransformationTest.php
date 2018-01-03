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
    public function testCallbackTransform()
    {
        $ht = new HT([
              'tribe' => 'nation',
              'roots' => ['mixOf', function($origin) {
                   return implode(', ', $origin['roots']); 
               }],
        ]);

        $origin = [ 'tribe' => 'Mexica', 'roots' => ['Chichimeca','Acolhua'] ];
        $expected = [ 'nation' => 'Mexica', 'mixOf' => 'Chichimeca, Acolhua' ];

        $transformed = $ht->transform($origin);

        $this->assertEquals($expected, $transformed);
    }
}
