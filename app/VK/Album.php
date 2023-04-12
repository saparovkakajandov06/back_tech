<?php

namespace App\VK;

class Album
{
    public $id, $owner_id, $title, $description, $size;

    public function __construct($id, $owner_id, $title, $description, $size) {
        $this->id = $id;
        $this->owner_id = $owner_id;
        $this->title = $title;
        $this->description = $description;
        $this->size = $size;
    }
}
