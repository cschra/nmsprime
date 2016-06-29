<?php

namespace Modules\ProvVoipEnvia\Entities;
use Modules\ProvBase\Entities\Contract;
use Modules\ProvVoip\Entities\Phonenumber;

// Model not found? execute composer dump-autoload in lara root dir
class EnviaOrder extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'enviaorder';

	// collect all order related informations ⇒ later we can use subarrays of this array to get needed informations
	// mark missing data with value null
	protected static $meta = array(

		// TODO: Process the list with all possible ordertypes ⇒ hope to get this from envia…
		'orders' => array(
			array(
				'ordertype' => 'Neuschaltung envia TEL voip reselling',
				'ordertype_id' => null,
				'method' => 'contract/create',
				'phonenumber_related' => False,
			),
			array(
				'ordertype' => 'Neuschaltung einer Rufnummer',
				'ordertype_id' => 19,
				'method' => 'voip_account/create',
				'phonenumber_related' => True,
			),
			array(
				'ordertype' => 'Sprachtarif wird geändert',
				'ordertype_id' => null,
				'method' => 'contract/change_tariff',
				'phonenumber_related' => False,
			),
			array(
				'ordertype' => 'Stornierung eines Auftrags',
				'ordertype_id' => null,
				'method' => 'order/cancel',
				'phonenumber_related' => False,
			),
		),
		'states' => array(
			array(
				'orderstatus_id' => 1000,
				'orderstatus' => 'in Bearbeitung',
				'state_type' => 'info',
				'final' => False,
			),
			array(
				'orderstatus_id' => 1001,
				'orderstatus' => 'erfolgreich verarbeitet',
				'state_type' => 'success',
				'final' => True,
			),
			array(
				'orderstatus_id' => 1009,
				'orderstatus' => 'Warte auf Portierungserklärung',
				'state_type' => 'warning',
				'final' => False,
			),
			array(
				'orderstatus_id' => 1010,
				'orderstatus' => 'Terminverschiebung',
				'state_type' => 'warning',
				'final' => False,
			),
			array(
				'orderstatus_id' => 1012,
				'orderstatus' => 'Dokument fehlerhaft oder nicht lesbar',
				'state_type' => 'danger',
				'final' => False,
			),
			array(
				'orderstatus_id' => 1013,
				'orderstatus' => 'Warte auf Portierungsbestätigung',
				'state_type' => 'warning',
				'final' => False,
			),
			array(
				'orderstatus_id' => 1014,
				'orderstatus' => 'Fehlgeschlagen, Details siehe Bemerkung',
				'state_type' => 'danger',
				'final' => True,
			),
			array(
				'orderstatus_id' => 1015,
				'orderstatus' => 'Schaltung bestätigt zum Zieltermin',
				'state_type' => 'success',
				'final' => True,
			),
			array(
				'orderstatus_id' => 1017,
				'orderstatus' => 'Stornierung bestätigt',
				'state_type' => 'success',
				'final' => True,
			),
			array(
				'orderstatus_id' => 1018,
				'orderstatus' => 'Stornierung nicht möglich',
				'state_type' => 'danger',
				'final' => True,
			),
			array(
				'orderstatus_id' => 1019,
				'orderstatus' => 'Warte auf Zieltermin',
				'state_type' => 'warning',
				'final' => False,
			),
			array(
				'orderstatus_id' => 1036,
				'orderstatus' => 'Eskalationsstufe 1 - Warte auf Portierungsbestätigung',
				'state_type' => 'danger',
				'final' => False,
			),
			array(
				'orderstatus_id' => 1037,
				'orderstatus' => 'Eskalationsstufe 2 - Warte auf Portierungsbestätigung',
				'state_type' => 'danger',
				'final' => False,
			),
			array(
				'orderstatus_id' => 1038,
				'orderstatus' => 'Portierungsablehnung, siehe Bemerkung',
				'state_type' => 'danger',
				'final' => True,
			),
			array(
				'orderstatus_id' => 1039,
				'orderstatus' => 'Warte auf Zieltermin kleiner gleich 180 Kalendertage',
				'state_type' => 'warning',
				'final' => False,
			),
		),

	);

	// Add your validation rules here
	public static function rules($id=null) {

		return array(
			// Prevent users from creating orders (table enviaorder is only changable through Envia API!)
			// TODO: later remove delete button
			'orderid' => 'required|integer|min:1',
			'related_order_id' => 'exists:enviaorder,id',
		);

	}

	// Don't forget to fill this array
	protected $fillable = [
		'orderid',
		'method',
		'ordertype_id',
		'ordertype',
		'orderstatus_id',
		'orderstatus',
		'orderdate',
		'ordercomment',
		'related_order_id',
		'customerreference',
		'contractreference',
		'contract_id',
		'phonenumber_id',
	];


	/**
	 * Get the order subarray from meta
	 *
	 * @author Patrick Reichel
	 *
	 * @return array containing metadata for all order types:
	 *			<str> ordertype
	 *			<int> ordertype_id
	 *			<str> method
	 *			<bool> phonenumber_related
	 */
	public static function get_orders_metadata() {
		return self::$meta['orders'];
	}


	/**
	 * Get the stats subarray from meta
	 *
	 * @author Patrick Reichel
	 *
	 * @return array containing metadata for all order states:
	 *			<int> orderstatus_id
	 *			<str> orderstatus
	 *			<str> state_type
	 *			<bool> final
	 */
	public static function get_states_metadata() {
		return self::$meta['states'];
	}


	/**
	 * Checks if an orderstatus is final
	 *
	 * @author Patrick Reichel
	 *
	 * @param $order to check
	 *
	 * @return true if orderstatus is final (will not change anymore), else false
	 */
	public static function orderstate_is_final($order) {

		$finals = array();
		foreach (self::$meta['states'] as $state_meta) {
			$final = $state_meta['final'];
			$type = $state_meta['orderstatus'];
			$id = $state_meta['orderstatus_id'];

			if ($final) {
				if (!is_null($type))
					array_push($finals, $type);
				if (!is_null($id))
					array_push($finals, $id);
			}
		};

		$final_state = (
			in_array($order->orderstatus_id, $finals)
			||
			in_array($order->orderstatus, $finals)
		);

		return $final_state;
	}


	/**
	 * Checks if a given ordertype is phonenmumber related
	 *
	 * @param order to check
	 *
	 * @author Patrick Reichel
	 */
	public static function ordertype_is_phonenumber_related($order) {

		$relates = array();
		foreach (self::$meta['orders'] as $order_meta) {

			$related = $order_meta['phonenumber_related'];
			$type = $order_meta['ordertype'];
			$id = $order_meta['ordertype_id'];

			if ($related) {
				if (!is_null($type))
					array_push($relates, $type);
				if (!is_null($id))
					array_push($relates, $id);
			}
		}

		$related_state = (
			in_array($order->ordertype, $relates)
			||
			in_array($order->ordertype_id, $relates)
			||
			in_array($order->method, $relates)
		);

		return $related_state;
	}


	/**
	 * Checks if an order is related to a given method.
	 * Call this in your specialized methods
	 *
	 * @author Patrick Reichel
	 */
	protected static function _order_mapped_to_method($order, $method) {

		$matches = array();
		foreach (self::$meta['orders'] as $order_meta) {
			$cur_method = $order_meta['method'];
			if ($cur_method == $method) {
				$cur_type = $order_meta['ordertype'];
				$cur_id = $order_meta['ordertype_id'];

				array_push($matches, $cur_method);
				if (!is_null($cur_type))
					array_push($matches, $cur_type);
				if (!is_null($cur_id))
					array_push($matches, $cur_id);
			}
		}

		$mapped_to_method = (
			in_array($order->ordertype, $matches)
			||
			in_array($order->ordertype_id, $matches)
			||
			in_array($order->method, $matches)
		);

		return $mapped_to_method;
	}


	/**
	 * Checks if an order matches a given state_type.
	 * Use this in your concrete checks
	 *
	 * @author Patrick Reichel
	 */
	protected static function _order_mapped_to_state_type($order, $state_type) {

		$matches = array();
		foreach (self::$meta['states'] as $state_meta) {
			$cur_state_type = $state_meta['state_type'];
			if ($cur_state_type == $state_type) {
				$cur_state = $state_meta['orderstatus'];
				$cur_id = $state_meta['orderstatus_id'];
				if (!is_null($cur_state))
					array_push($matches, $cur_state);
				if (!is_null($cur_id))
					array_push($matches, $cur_id);
			}
		}

		$mapped_to_state_type = (
			in_array($order->orderstatus, $matches)
			||
			in_array($order->orderstatus_id, $matches)
		);

		return $mapped_to_state_type;
	}


	/**
	 * Check if order is successfully processed.
	 *
	 * @author Patrick Reichel
	 */
	public static function order_state_is_success($order) {
		return self::_order_mapped_to_state_type($order, 'success');
	}


	/**
	 * Checks if order is related to creation of a phonenumber
	 *
	 * @author Patrick Reichel
	 */
	public static function order_creates_voip_account($order) {
		return self::_order_mapped_to_method($order, 'voip_account/create');
	}

	// Name of View
	public static function view_headline()
	{
		return 'EnviaOrders';
	}

	// link title in index view
	public function view_index_label()
	{
		// combine all possible orderstatus IDs with GUI colors
		$colors = array();
		foreach (self::$meta['states'] as $state) {
			$colors[$state['orderstatus_id']] = $state['state_type'];
		}

		// this is used to group the orders by their escalation levels (so later on we can sort them by these levels)
		$escalations = [
			'success' => 0,
			'info' => 1,
			'warning' => 2,
			'danger' => 3,
		];

		if (!boolval($this->orderstatus_id)) {
			$bsclass = 'info';
		}
		else {
	        $bsclass = $colors[$this->orderstatus_id];
		}
		$escalation_level = $escalations[$bsclass].' – '.$bsclass;

		$contract_nr = Contract::findOrFail($this->contract_id)->number;
		$contract_nr = '<a href="'.\URL::route('Contract.edit', array($this->contract_id)).'" target="_blank">'.$contract_nr.'</a>';

		if (boolval($this->phonenumber_id)) {
			$phonenumber = Phonenumber::findOrFail($this->phonenumber_id);
			$phonenumbermanagement_id = $phonenumber->phonenumbermanagement->id;
			$phonenumber_nr = $phonenumber->prefix_number.'/'.$phonenumber->number;
			$phonenumber_nr = '<a href="'.\URL::route('PhonenumberManagement.edit', array($phonenumbermanagement_id)).'" target="_blank">'.$phonenumber_nr.'</a>';
		}
		else {
			$phonenumber_nr = '–';
		}

        return ['index' => [$this->ordertype, $this->orderstatus, $escalation_level, $contract_nr, $phonenumber_nr, $this->created_at, $this->updated_at],
                'index_header' => ['Ordertype', 'Orderstatus', 'Escalation', 'Contract&nbsp;Nr.', 'Phonenumber', 'Created at', 'Updated at'],
                'bsclass' => $bsclass,
				'header' => $this->orderid.': '.$this->ordertype.' ('.$this->orderstatus.')',
		];
	}

	// belongs to a modem - see BaseModel for explanation
	public function view_belongs_to ()
	{
		if (boolval($this->phonenumber_id)) {
			return $this->phonenumber->phonenumbermanagement;
		}
		else {
			return $this->contract;
		}
	}

	// returns all objects that are related to an EnviaOrder
	public function view_has_many()
	{
		if (\PPModule::is_active('provvoipenvia')) {
			$ret['Envia']['EnviaOrderDocument']['class'] = 'EnviaOrderDocument';
			$ret['Envia']['EnviaOrderDocument']['relation'] = $this->enviaorderdocument;
			$ret['Envia']['EnviaOrderDocument']['method'] = 'show';
			$ret['Envia']['EnviaOrderDocument']['options']['hide_delete_button'] = '1';
		}
		else {
			$ret = array();
		}

		return $ret;
	}

	public function contract() {
		return $this->belongsTo('Modules\ProvBase\Entities\Contract');
	}

	public function phonenumber() {
		return $this->belongsTo('Modules\ProvVoip\Entities\Phonenumber');
	}

	public function enviaorderdocument() {
		return $this->hasMany('Modules\ProvVoipEnvia\Entities\EnviaOrderDocument', 'enviaorder_id')->orderBy('created_at');
	}

}
