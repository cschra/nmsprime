<?php namespace Modules\ProvBase\Entities;

class Domain extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'domain';

	// Name of View
	public static function view_headline()
	{
		return 'Domains';
	}

	// View Icon
	public static function view_icon()
	{
		return '<i class="fa fa-tag"></i>';
	}

	// There are no validation rules
	public static function rules($id=null)
	{
		return array(
			'name' => 'regex:/^[0-9A-Za-z\.\-\_]+$/|required',
			'type' => 'required'
		);
	}

	// Link title in index view
	public function view_index_label()
	{
		return ['index' =>	[$this->name, $this->type, $this->alias],
			'index_header' =>	['Name', 'Type', 'Alias'],
			'bsclass' => 'success',
			'header' => 'Domain: '.$this->name.' (Type: '.$this->type.')'];
	}

	// AJAX Index list function
	// generates datatable content and classes for model
	public function view_index_label_ajax()
	{
		$bsclass = $this->get_bsclass();

		return ['table' => $this->table,
				'index_header' => [$this->table.'.name', $this->table.'.type', $this->table.'.alias'],
				'header' =>  'Domain: '.$this->name.' (Type: '.$this->type.')',
				'bsclass' => $bsclass,
				'orderBy' => ['0' => 'asc']];
	}

	public function get_bsclass()
	{
		$bsclass = 'success';

		return $bsclass;
	}
}