<?php

namespace Jefrancomix\Sohot;

class HashmapMapper implements HashmapMapperInterface
{
    protected $rules;
    protected $transformed;
    
    public function __construct($rules)
    {
        $this->rules = $rules;
    }
    
    public function map($hashmap, $sourceContext = null)
    {
        $this->transformed = [];
        array_walk($this->rules, function($rule, $key, $hashmap) use($sourceContext) {
            if(array_key_exists($key, $hashmap)) {
                $this->applyRule($rule, $key, $hashmap, $sourceContext);
            }
        }, $hashmap);
        return $this->transformed;
    }

    protected function applyRule($rule, $key, $hashmap, $sourceContext = null)
    {
        $hashmapValueAtKey = $hashmap[$key];
        if(is_null($sourceContext)) {
            $sourceContext = $hashmap;
        }
        if(is_array($rule) && is_callable($rule[1])) {
            $this->transformed[$rule[0]] = call_user_func($rule[1], $hashmapValueAtKey, $sourceContext);
        }
        if(is_string($rule)) {
            $this->transformed[$rule] = $hashmapValueAtKey;
        }
        if(is_array($rule) && $rule[1] instanceof HashmapMapperInterface) {
            $this->transformed[$rule[0]] = $rule[1]->map($hashmapValueAtKey, $sourceContext);
        }
    }
}
