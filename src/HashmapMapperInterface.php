<?php

namespace Jefrancomix\Sohot;


interface HashmapMapperInterface
{
    /**
     * @param mixed $object In its simplest form is an associative array, Hashmap for friends
     * @return mixed
     */
    public function map($object);
}