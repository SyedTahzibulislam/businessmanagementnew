<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class unitcoversion extends Model
{
    use HasFactory;
	
			 protected $fillable = [
        'name',
       'coversionamount',
	'softdelete',	'stockunit',	'buyingunit',	'sellingunit',
	'basicunit_id',
	
    ];
	
				 public function basicunit()
    {
    	return $this->belongsTo(basicunit::class);
    }	
	
}
