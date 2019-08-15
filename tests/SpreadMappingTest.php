<?php

namespace Jefrancomix\Sohot\Test;

use PHPUnit\Framework\TestCase;
use Jefrancomix\Sohot\HashmapMapper as HM;

class SpreadMappingTest extends TestCase
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
            'wp:term' => [$termData],
        ];
        $expectedTarget = $termData;

        $hm = new HM([
            'wp:term' => ['...', [$mockAux, 'mapperCallable']],
        ]);

        $mockAux->expects($this->once())
            ->method('mapperCallable')
            ->with(
                $this->equalTo([0 => $termData]),
                $this->equalTo($source)
            )->willReturn($termData);

        $target = $hm->map($source);

        $this->assertEquals($expectedTarget, $target);
    }

    public function testSpread()
    {
        $termData = [
          'id' => 31925,
          'link' => 'http://example.com/category/test-term/',
          'name' => 'Test term',
          'slug' => 'test-term',
          'taxonomy' => 'category',
        ];
        $source = [
            'wp:term' => $termData,
            'ignored' => 'right',
        ];
        $expectedTarget = $termData;

        $hm = new HM([
          'wp:term' => ['...', 'Jefrancomix\Sohot\identity'],
        ]);

        $target = $hm->map($source);

        $this->assertEquals($expectedTarget, $target);
    }

    /**
     * @dataProvider spreadMappingDataProvider
     */
    public function testHashMapperReusedReturnsOk($source, $expected)
    {
        $mediaMapper = new HM([
            'wp:featuredmedia' => ['...', function ($featuredMedia) {
                $media = $featuredMedia[0];
                foreach ($media['media_details']['sizes'] as $key => $size) {
                    if ($key === 'thumbnail') {
                        $pictureUrl = $size['source_url'];
                    }
                }
                return [
                    'img' => $pictureUrl,
                    'alttext' => $media['alt_text'],
                ];
            }],
        ]);
        $postMapper = new HM([
            'title' => 'title',
            'link' => 'permalink',
            '_embedded' => ['...', $mediaMapper],
        ]);

        $this->assertEquals($expected, $postMapper->map($source));
    }

    public function spreadMappingDataProvider()
    {
        return [
          [
              [
                  'title' => 'Hola IA',
                  'link' => 'http://example.com/hola-ia/',
                  '_embedded' => [
                      'wp:featuredmedia' => [
                          [
                              'id' => 241239,
                              'date' => '2017-12-27T13:52:15',
                              'slug' => 'inteligencia-artificial',
                              'type' => 'attachment',
                              'link' => 'http://example.com/inteligencia-artificial/',
                              'title' => [
                                  'rendered' => 'inteligencia artificial',
                              ],
                              'author' => 108,
                              'caption' => [
                                  'rendered' => '<p>Inteligencia Artificial</p>\n',
                              ],
                              'alt_text' => 'Inteligencia Artificial.',
                              'media_type' => 'image',
                              'mime_type' => 'image/jpeg',
                              'media_details' => [
                                  'width' => 700,
                                  'height' => 400,
                                  'file' => '2017/12/inteligencia-artificial.jpg',
                                  'sizes' => [
                                      'thumbnail' => [
                                          'file' => 'inteligencia-artificial-500x400.jpg',
                                          'width' => 500,
                                          'height' => 400,
                                          'mime_type' => 'image/jpeg',
                                          'source_url' => 'http://example.com/media/2017/12/inteligencia-artificial-500x400.jpg',
                                      ],
                                      'medium' => [
                                          'file' => 'inteligencia-artificial-300x171.jpg',
                                          'width' => 300,
                                          'height' => 171,
                                          'mime_type' => 'image/jpeg',
                                          'source_url' => 'http://example.com/media/2017/12/inteligencia-artificial-300x171.jpg',
                                      ],
                                      'full' => [
                                          'file' => 'inteligencia-artificial.jpg',
                                          'width' => 700,
                                          'height' => 400,
                                          'mime_type' => 'image/jpeg',
                                          'source_url' => 'http://example.com/media/2017/12/inteligencia-artificial.jpg',
                                      ],
                                  ],
                                  'image_meta' => [
                                      'aperture' => '0',
                                      'credit' => '',
                                      'camera' => '',
                                      'caption' => '',
                                      'created_timestamp' => '0',
                                      'copyright' => '',
                                      'focal_length' => '0',
                                      'iso' => '0',
                                      'shutter_speed' => '0',
                                      'title' => '',
                                      'orientation' => '0',
                                      'keywords' => [],
                                  ],
                              ],
                              'source_url' => 'http://example.com/media/2017/12/inteligencia-artificial.jpg',
                          ],
                      ],
                  ],
              ],
              [
                  'title' => 'Hola IA',
                  'permalink' => 'http://example.com/hola-ia/',
                  'img' => 'http://example.com/media/2017/12/inteligencia-artificial-500x400.jpg',
                  'alttext' => 'Inteligencia Artificial.',
              ],
          ],
        ];
    }
}
