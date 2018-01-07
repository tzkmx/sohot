<?php

namespace Jefrancomix\Sohot;

class HashmapMapper implements HashmapMapperInterface
{
    /**
     * @var mixed associative array of sourceKeys (strings) to rules
     */
    protected $rules;
    /**
     * @var array that will store mapped keys from source
     */
    protected $mapped;
    /**
     * @var string with the name of matched key in source hashmap
     */
    protected $sourceKeyMatched;
    
    public function __construct($rules)
    {
        $this->rules = $rules;
    }
    
    public function map($hashmap, $sourceContext = null)
    {
        $this->mapped = [];
        array_walk($this->rules, function($rule, $key, $hashmap) use($sourceContext) {
            if(array_key_exists($key, $hashmap)) {
                $this->sourceKeyMatched = $key;
                $this->applyRule($rule, $hashmap, $sourceContext);
            }
        }, $hashmap);
        return $this->mapped;
    }

    protected function applyRule($rule, $hashmap, $sourceContext = null)
    {
        if(is_array($rule)) {
            $this->applyConsRule($rule[0], $rule[1], $hashmap, $sourceContext);
        }
        if(is_string($rule)) {
            $this->mapped[$rule] = $hashmap[$this->sourceKeyMatched];
        }
    }
    protected function applyConsRule($targetKey, $actualRule, $hashmap, $sourceContext)
    {
        if(is_null($sourceContext)) {
            $sourceContext = $hashmap;
        }
        $hashmapValueAtKey = $hashmap[$this->sourceKeyMatched];
        if(is_callable($actualRule)) {
            $this->receiveDataReturnedByRule($targetKey, call_user_func($actualRule, $hashmapValueAtKey, $sourceContext));
        }
        if($actualRule instanceof HashmapMapperInterface) {
            $this->receiveDataReturnedByRule($targetKey, $actualRule->map($hashmapValueAtKey, $sourceContext));
        }
    }

    protected function receiveDataReturnedByRule($targetKey, $returnValue)
    {
        if($targetKey === '...') {
            foreach($returnValue as $targetKey => $value) {
                $this->mapped[$targetKey] = $value;
            }
            return;
        }
        $this->mapped[$targetKey] = $returnValue;
    }

}
