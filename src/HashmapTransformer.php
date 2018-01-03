<?php

namespace Jefrancomix\Sohot;

class HashmapTransformer
{
    protected $rules;
    protected $transformed;
    
    public function __construct($rules)
    {
        $this->rules = $rules;
    }
    
    public function transform($hashmap)
    {
        $this->transformed = [];
        array_walk($this->rules, function($rule, $key, $hashmap) {
            if(array_key_exists($key, $hashmap)) {
                $this->applyRule($rule, $key, $hashmap);
            }
        }, $hashmap);
        return $this->transformed;
    }

    protected function applyRule($rule, $key, $hashmap)
    {
        $hashmapValueAtKey = $hashmap[$key];
        if(is_array($rule) && is_callable($rule[1])) {
            $this->transformed[$rule[0]] = call_user_func($rule[1], $hashmapValueAtKey, $hashmap);
        }
        if(is_string($rule)) {
            $this->transformed[$rule] = $hashmapValueAtKey;
        }
    }
}
