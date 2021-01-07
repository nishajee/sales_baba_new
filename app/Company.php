<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{

	use SoftDeletes;
	protected $fillable = [
		'id','users_id'
	];

	protected $primaryKey = 'id';
	protected $table = 'org';
}
