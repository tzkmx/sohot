<?php

namespace Jefrancomix\Sohot\Test;
use PHPUnit\Framework\TestCase;
use Jefrancomix\Sohot\HashmapMapper as HM;

class ImplicitSpreadMapperTest extends TestCase
{
    public function testSpreadCallableMapping()
    {
        $mockAux = $this->getMockBuilder(stdClass::class)
            ->setMethods(['mapperCallable'])
            ->getMock();

        $termData = [
            'id' => 31925,
            'link' => 'http://example.com/category/test-term/',
            'name' => 'Test term',
            'slug' => 'test-term',
            'taxonomy' => 'category',
        ];
        $source = [
            'wp:term' => [ $termData ],
        ];
        $expectedTarget = $termData;

        $hm = new HM([
            'wp:term' => [$mockAux, 'mapperCallable'],
        ], ['implicitSpread' => true]);

        $mockAux->expects($this->once())
            ->method('mapperCallable')
            ->with(
                $this->equalTo([ 0 => $termData ]),
                $this->equalTo($source)
            )->willReturn($termData);

        $target = $hm->map($source);

        $this->assertEquals($expectedTarget, $target);
    }

    /**
     * @dataProvider Jefrancomix\Sohot\Test\SpreadMappingTest::spreadMappingDataProvider
     */
    public function testHashMapperReusedReturnsOk($source, $expected)
    {
        $options = [ 'implicitSpread' => true ];

        $mediaMapper = new HM([
            'wp:featuredmedia' => function($featuredMedia){
                $media = $featuredMedia[0];
                foreach($media['media_details']['sizes'] as $key => $size) {
                    if($key === 'thumbnail') {
                        $pictureUrl = $size['source_url'];
                    }
                }
                return [
                    'img' => $pictureUrl,
                    'alttext' => $media['alt_text'],
                ];
            },
        ], $options);
        $postMapper = new HM([
            'title' => 'title',
            'link' => 'permalink',
            '_embedded' => $mediaMapper,
        ], $options);

        $this->assertEquals($expected, $postMapper->map($source));
    }
}
