<?php

namespace Modules\BillingBase\Entities;

use Storage;
use Modules\ProvBase\Entities\Contract;

class Salesman extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'salesman';

	// Add your validation rules here
	public static function rules($id = null)
	{
		return array(
			'firstname' 	=> 'required',
			'lastname' 		=> 'required',
			'commission'	=> 'required|numeric|between:0,100',
			'products' 		=> 'product',
		);
	}


	/**
	 * View related stuff
	 */

	// Name of View
	public static function view_headline()
	{
		return 'Salesman';
	}

	// link title in index view
	public function view_index_label()
	{
		return $this->firstname.' '.$this->lastname;
	}

	// Return a pre-formated index list
	public function index_list ()
	{
		return $this->orderBy('id')->get();
	}

	// View Relation.
	public function view_has_many()
	{
		return array(
			'Contract' => $this->contracts,
			);
	}


	/**
	 * Relationships:
	 */
	public function contracts ()
	{
		return $this->hasMany('Modules\ProvBase\Entities\Contract');
	}



	/**
	 * BILLING STUFF
	 */
	public $all_prod_types = [];				// array (list) of all possible types of products -
	protected $total_commission = 0;			// total commission amount during actual billing cycle
	protected $item_names = [];					// all names of items he gets commission for (in actual billing cycle)
	public $filename = 'salesmen_commission.txt';
	public $dir;								// set during init of accounting command


	// example - $item->product->name == 'Credit Device'
	public function add_item($item)
	{
		$types = explode(',', $this->products);
		foreach ($types as $key => $value)
			$types[$key] = trim($value);

		if ($item->product->type == 'Credit')
		{
			// get credit type from product name
			$credit_type = '';
			foreach ($this->all_prod_types as $type)
			{
				if (strpos($item->product->name, $type) !== false)
					$credit_type = $type;
			}

			// if type is assigned - only add amount if type is in salesmans product list
			if ($credit_type)
			{
				if (in_array($credit_type, $types))
					goto add;
				return;
			}
add:
			// add all other credits - default
			$this->total_commission -= $item->charge;
			array_push($this->item_names, $item->product->name);
			return;
		}

		// all other types that the salesman gets commission for
		if (in_array($item->product->type, $types))
		{
			// $count = $item->count ? $item->count : 1;
			$this->total_commission += $item->charge;
			array_push($this->item_names, $item->count.'x '.$item->product->name);
		}

		return;
	}

	public function prepare_output_file()
	{
		Storage::put($this->dir.$this->filename, "ID\tName\tCommission in %\tTotal Fee\tCommission Amount\tItems\n");
	}


	// id, name, commission %, commission amount, all added items as string
	public function print_commission()
	{
		if ($this->total_commission == 0)
			return;

		$file = $this->dir.$this->filename;

		Storage::append($file, $this->id."\t".$this->firstname.' '.$this->lastname."\t".$this->commission."\t".$this->total_commission."\t".round($this->total_commission * $this->commission / 100, 2)."\t".implode(', ', $this->item_names));
		echo "stored salesmen commissions in $file\n";
	}



}


/**
 * Observer Class
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 */
class ContractObserver
{
	public function creating($salesman)
	{
		$salesman->products = str_replace(['/', '|', ';'], ',', $salesman->products);
	}

	public function updating($salesman)
	{
		$salesman->products = str_replace(['/', '|', ';'], ',', $salesman->products);
	}

}