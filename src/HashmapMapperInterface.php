<?php

namespace Jefrancomix\Sohot;

interface HashmapMapperInterface
{
    /**
     * @param mixed $object In its simplest form is an associative array, Hashmap for friends
     * @param mixed $sourceContext the HashMap onto which the map is applied
     * @return mixed
     */
    public function apply($object, $sourceContext = null);

    /**
     * @return HashmapMapperInterface a mapper that applies this same rules to every item passed in an array
     */
    public function getCollectionMapper();
}
