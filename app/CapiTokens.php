<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CapiTokens extends Model
{
    public $timestamps = true;
    protected $table = 'CapiTokens';
	
	protected $guarded = [];
}
