<?php

namespace Tests\TF;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Testing\AssertableJsonString;

class TFResponse
{
    /**
     * @var \Illuminate\Http\Client\Response
     */
    public $res;

    public function __construct($res)
    {
        $this->res = $res;
    }

    public function __call($name, $args)
    {
        return $this->res->$name($args);
    }

    public function dd()
    {
        dd($this->res->json());

        return $this;
    }

    public function assertStatus(int $status): self
    {
        TestCase::assertEquals($status, $this->res->status(), $this->res->body());
        return $this;
    }

    public function assertJson($data): self
    {
        (new AssertableJsonString($this->res->body()))
            ->assertSubset($data);
        return $this;
    }

    public function assertJsonFieldMatchesRegex($field, $regex): self {
        $val = $this->getData($field);
        TestCase::assertTrue(
            (boolean)preg_match($regex, $val),
            strval("response body field '${field}' does not match regex '${regex}'")
        );
        return $this;
    }

    /**
     * get data from response by locator
     * @param $locator
     * @return string
     */
    public function getData($locator): string {
        $locators = preg_split("/\./", $locator);
        $val = '';
        if ($locators) {
            $this->$val = $this->res[$locators[0]];
            for ($i = 1; $i < count($locators); $i++) {
                $this->$val = $this->$val[$locators[$i]];
            }
        } else {
            $this->$val = strval($this->res->offsetGet($locator));
        }
        return $this->$val;
    }

    public function json()
    {
        return $this->res->json();
    }

    public function assertStatusSuccess(): self
    {
        $r = $this->res->json();
        TestCase::assertEquals('success', $r['status'], json_encode($r));
        return $this;
    }

    public function assertStatusError(): self
    {
        $r = $this->res->json();
        TestCase::assertEquals('error', $r['status'], json_encode($r));
        return $this;
    }

    public function assertUnauthenticated(): self
    {
        $this->assertJson([
            'status' => 'error',
            'data' => [
                'message' => 'Unauthenticated',
            ],
        ]);
        return $this;
    }
}
