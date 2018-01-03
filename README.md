# Objective

This is a minimalistic library aimed to reuse logic
on HashTables mapping, that i use over and over i.e.
consuming API results to pass to Twig views.

Currently, it supports transformation through simple
key mapping and callbacks.

## Simple key mapping

```php
use Jefrancomix\Sohot\HashmapTransformer as HT;

$source = ['origin' => 'Africa'];

/*
 * I want in $target key "roots" instead of "origin":
 * so I pass a simple dictionary mapping
 */

$transformer = new HT(['origin' => 'roots']);
$target = $transformer->transform($source);

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
use Jefrancomix\Sohot\HashmapTransformer as HT;

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

$transformer = new HT([
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

$target = $transformer->transform($source);

var_dump($target);
/*
 * It results in:
 array(1) {
     ["roots"]=>
     string(6) "Africa"
 }
 */
```

[![Maintainability](https://api.codeclimate.com/v1/badges/bdc511cc3f3a39dca72b/maintainability)](https://codeclimate.com/github/tzkmx/sohot/maintainability)
