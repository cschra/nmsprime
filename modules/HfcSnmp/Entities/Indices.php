<?php

namespace Modules\HfcSnmp\Entities;

class Indices extends \BaseModel {

	public $table = 'indices';

	// public $guarded = ['name', 'table'];


	public static function boot()
	{
		parent::boot();

		Indices::observe(new IndicesObserver);
	}


	// Add your validation rules here
	public static function rules($id = null)
	{
		if (\Request::has('netelement_id'))
		{
			return array(
				// netelement_id & parameter_id combination must be unique
				'parameter_id' => 'unique:indices,parameter_id,'.$id.',id,deleted_at,NULL,netelement_id,'.\Request::input('netelement_id'),
			);
		}

		return [];
	}

	// Name of View
	public static function view_headline()
	{
		return 'Indices';
	}

	// View Icon
	public static function view_icon()
	{
	  return '<i class="fa fa-header"></i>'; 
	}

	// link title in index view
	public function view_index_label()
	{
		return ['index' => [$this->parameter->oid->name],
				'index_header' => ['OID Name'],
				'header' => $this->parameter->id.': '.$this->parameter->oid->name];
	}

	// AJAX Index list function
	// generates datatable content and classes for model
	public function view_index_label_ajax()
	{
		$header = isset($this->parameter) ? $this->parameter->id.': '.$this->parameter->oid->name : '';

		return ['table' => $this->table,
				'index_header' => ['parameter.oid.name'],
				'header' => $header,
				'eager_loading' => ['parameter']];
	}

	public function view_belongs_to ()
	{
		return $this->parameter;
	}


	/**
	 * Relations
	 */
	public function parameter()
	{
		return $this->belongsTo('Modules\HfcSnmp\Entities\Parameter')->with('oid');
	}

	public function netelement()
	{
		return $this->belongsTo('Modules\HfcReq\Entities\NetElement', 'netelement_id');
	}


}


class IndicesObserver {

	public function creating($indices)
	{
		$indices->indices = str_replace([' ', "\t"], '', $indices->indices);
	}

	public function updating($indices)
	{
		$indices->indices = str_replace([' ', "\t"], '', $indices->indices);
	}

}