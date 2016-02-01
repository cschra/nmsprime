<?php

namespace Modules\HfcBase\Entities;

class HfcBase extends \BaseModel {

	// The associated SQL table for this Model
	protected $table = 'hfcbase';

	// Don't forget to fill this array
	protected $fillable = ['ro_community', 'rw_community'];

	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
		);
	}
	
	// Name of View
	public static function get_view_header()
	{
		return 'Hfc Base Config';
	}

	// link title in index view
	public function get_view_link_title()
	{
		return "HfcBase";
	}	


}