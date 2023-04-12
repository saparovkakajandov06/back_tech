<?php

//function create($class, $attributes=[])
//{
//    return factory($class)->create($attributes);
//}
//
//function make($class, $attributes=[])
//{
//    return factory($class)->make($attributes);
//}

use Illuminate\Testing\AssertableJsonString;

if (! function_exists('assert_json')) {

    function assert_json($response, $data)
    {
        (new AssertableJsonString($response->body()))
            ->assertSubset($data);
    }
}
