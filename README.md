[![Build Status](https://travis-ci.org/tzkmx/sohot.svg?branch=master)](https://travis-ci.org/tzkmx/sohot) [![Maintainability](https://api.codeclimate.com/v1/badges/bdc511cc3f3a39dca72b/maintainability)](https://codeclimate.com/github/tzkmx/sohot/maintainability)

# Objective

This is a minimalistic library aimed to reuse logic
on HashTables mapping, that i use over and over i.e.
consuming API results to pass to Twig views.

Currently, it supports transformation through simple
key mapping and callbacks.

## Simple key mapping

```php
use Jefrancomix\Sohot\HashmapMapper as HM;

$source = ['origin' => 'Africa'];

/*
 * I want in $target key "roots" instead of "origin":
 * so I pass a simple dictionary mapping
 */

$mapper = new HM(['origin' => 'roots']);
$target = $mapper->map($source);

var_dump($target);
/*
 * It results in:
 array(1) {
     ["roots"]=>
     string(6) "Africa"
 }
 */
```

## Callback key mapping

For somewhat complex transforms, you can use a function
that will receive as arguments:
- the value of the source hashmap specified at key:
- the whole hashmap if you need other values of it

```php
use Jefrancomix\Sohot\HashmapMapper as HM;

$source = [
    'date' => [
        'year' => 2006,
        'month' => 5,
        'day' => 4,
    ],
    'place' => 'San Salvador Atenco',
];

/*
 * I want in $target a single string date:
 * so I pass a callable for the key in source,
 * and the key I want to appear in the target
 */

$mapper = new HM([
    'place' => 'Caso CIDH',
    'date' => [
        'fecha',
        function($date, $source) {
            extract($date);
            $date = date_create_from_format('Y/m/d', "{$year}/{$month}/{$day}");
            return $date->format('Y-m-d');
        }
    ],
]);

$target = $mapper->map($source);

var_dump($target);
/*
 * It results in:
 array(2) {
     ["Caso CIDH"]=>
     string(19) "San Salvador Atenco",
     ["fecha"]=>
     string(10) "2006-05-04"
 }
 */
```

## Use another HashMapper 

If you have a complex subkey that is not easily mapped with a simple function,
you could use another HashMapper with the spec for that subkey, as the mapper
for that key.

```php
$source = [
    'sourceKey' => [
        'value' => 'to pass to HashMapper',
        'ignored' => 'it should not appear'
    ],
];

$sourceKeyMapper = new HM([ 'value' => 'legend' ]);

$sourceMapper = new HM([ 'sourceKey' => $sourceKeyMapper ]);

$target = $sourceMapper->map($source);

var_dump($target);

/*
array(1) {
  'sourceKey' =>
  array(1) {
    'legend' =>
    string(21) "to pass to HashMapper"
  }
}
*/
```

## Spread Operator Mapping with Callable

If you want to take a key with subkeys of the source and _spread it_ (copy
the dictionary of key and values it contains) on the target hashmap, you
can pass a tuple with the string `'...'` as the target key, and your chosen callable.

```php
$source = [
    'wp:term' => [
         'id' => 31925,
         'link' => 'http://example.com/category/test-term/',
         'name' => 'Test term',
         'slug' => 'test-term',
         'taxonomy' => 'category',
    ],
    'ignored' => 'right'
];

$mapper = new HM([
    'wp:term' => ['...', 'Jefrancomix\Sohot\identity']
]);

$target = $mapper->map($source);

var_export($target);
    
/* Result:
array (
  'id' => 31925,
  'link' => 'http://example.com/category/test-term/',
  'name' => 'Test term',
  'slug' => 'test-term',
  'taxonomy' => 'category',
)
*/
```

## Implicit spread (not specifying '...' key)

If you need all the dictionaries inside top level keys be spread into the
target, rather than writing your mapping spec as a tuple, you can give it
only a callable, specifying the option `implicitSpread => true` in the
constructor to the Mapper functor.

```php
$source = [
    'wp:term' => [
        [
            'id' => 31925,
            'link' => 'http://example.com/category/test-term/',
            'name' => 'Test term',
            'slug' => 'test-term',
            'taxonomy' => 'category',
        ]
    ],
];

$hm = new HM(
    [
        'wp:term' => compose('Jefrancomix\Sohot\head', 'Jefrancomix\Sohot\identity'),
    ],
    [
        'implicitSpread' => true
    ]
);

$target = $hm->map($source);

var_dump($target);

/* Result:
array(5) {
  'id' =>
  int(31925)
  'link' =>
  string(38) "http://example.com/category/test-term/"
  'name' =>
  string(9) "Test term"
  'slug' =>
  string(9) "test-term"
  'taxonomy' =>
  string(8) "category"
}
*/
 ``` 

