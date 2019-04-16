<?php

namespace App;

class ConcertFactory
{
    public static function createPublished($overrides = []): Concert
    {
        /** @var Concert $concert */
        $concert = factory(Concert::class)->create($overrides);
        $concert->publish();

        return $concert;
    }

    public static function createUnpublished($overrides = []): Concert
    {
        return $concert = factory(Concert::class)->states('unpublished')->create($overrides);
    }

}