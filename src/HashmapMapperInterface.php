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

    public function getCollectionMapper();
}