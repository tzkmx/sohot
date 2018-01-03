<?php

namespace Jefrancomix\Sohot;

class HashmapTransformer
{
    protected $rules;
    
    public function __construct($rules)
    {
        $this->rules = $rules;
    }
    
    public function transform($hashmap)
    {
        $transformed = [];
        $rulesKeys = array_keys($this->rules);
        foreach($hashmap as $key => $value) {
            if(in_array($key, $rulesKeys)) {

                $rule = $this->rules[$key];
                if(is_array($rule) && is_callable($rule[1])) {
                    $transformed[$rule[0]] = call_user_func($rule[1], $hashmap);
                }
                if(is_string($rule)) {
                    $transformed[$this->rules[$key]] = $value;
                }
            }
        }
        return $transformed;
    }
}
