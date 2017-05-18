<?php

namespace Modules\ProvVoip\Entities;

use Illuminate\Support\Collection;

// Model not found? execute composer dump-autoload in lara root dir
class Phonenumber extends \BaseModel {

    // The associated SQL table for this Model
    public $table = 'phonenumber';

	// Add your validation rules here
	public static function rules($id=null)
	{
		$ret = array(
			'country_code' => 'required|numeric',
			'prefix_number' => 'required|numeric',
			'number' => 'required|numeric',
			'mta_id' => 'required|exists:mta,id|min:1',
			'port' => 'required|numeric|min:1',
			/* 'active' => 'required|boolean', */
			// TODO: check if password is secure and matches needs of external APIs (e.g. Envia)
		);

		// inject id to rules (so it is passed to prepare_rules)
		$ret['id'] = $id;

		return $ret;
	}


	// Name of View
	public static function view_headline()
	{
		return 'Phonenumbers';
	}


	// link title in index view
    public function view_index_label(){

		$management = $this->phonenumbermanagement;

		if (is_null($management)) {
			if ($this->active) {
				$state = 'Active.';
			}
			else {
				$state = 'Deactivated.';
			}
			$bsclass = 'warning';
			$act = 'n/a';
			$deact = 'n/a';
			$state .= ' No PhonenumberManagement existing!';
		}
		else {
			$act = $management->activation_date;
			$deact = $management->deactivation_date;

			// deal with legacy problem of zero dates
			if ($act == '0000-00-00') {
				$act = null;
			}
			if ($deact == '0000-00-00') {
				$deact = null;
			}

			if (!boolval($act)) {
				$state = 'No activation date set!';
				$bsclass = 'danger';
			}
			elseif ($act > date('c')) {
				$state = 'Waiting for activation.';
				$bsclass = 'warning';
			}
			else {
				if (!boolval($deact)) {
					$state = 'Active.';
					$bsclass = 'success';
				}
				else {
					if ($deact > date('c')) {
						$state = 'Active. Deactivation date set but not reached yet.';
						$bsclass = 'warning';
					}
					else {
						$state = 'Deactivated.';
						$bsclass = 'info';
					}
				}
			}

			if (boolval($management->autogenerated)) {
				$state .= ' – PhonenumberManagement generated automatically!';
			}
		}

		// reuse dates for view
		if (is_null($act)) $act = '-';
		if (is_null($deact)) $deact = '-';

        // TODO: use mta states.
        //       Maybe use fast ping to test if online in this function?

        return ['index' => [$this->prefix_number.'/'.$this->number, $act, $deact, $state],
                'index_header' => ['Number', 'Activation date', 'Deactivation date', 'State'],
                'bsclass' => $bsclass,
                'header' => 'Port '.$this->port.': '.$this->prefix_number."/".$this->number];
    }

	/**
	 * ALL RELATIONS
	 * link with mtas
	 */
	public function mta()
	{
		return $this->belongsTo('Modules\ProvVoip\Entities\Mta', 'mta_id');
	}

	// belongs to an mta
	public function view_belongs_to ()
	{
		return $this->mta;
	}

	// View Relation.
	public function view_has_many()
	{
		$ret = array();
		if (\PPModule::is_active('provvoip')) {

			$relation = $this->phonenumbermanagement;

			// can be created if no one exists, can be deleted if one exists
			if (is_null($relation)) {
				$ret['Main']['PhonenumberManagement']['relation'] = new Collection();
				$ret['Main']['PhonenumberManagement']['options']['hide_delete_button'] = 1;
			}
			else {
				$ret['Main']['PhonenumberManagement']['relation'] = [$relation];
				$ret['Main']['PhonenumberManagement']['options']['hide_create_button'] = 1;
			}

			$ret['Main']['PhonenumberManagement']['class'] = 'PhonenumberManagement';
		}

		if (\PPModule::is_active('provvoipenvia')) {
			// TODO: auth - loading controller from model could be a security issue ?
			$ret['Main']['Envia API']['html'] = '<h4>Available Envia API jobs</h4>';
			$ret['Main']['Envia API']['view']['view'] = 'provvoipenvia::ProvVoipEnvia.actions';
			$ret['Main']['Envia API']['view']['vars']['extra_data'] = \Modules\ProvVoip\Http\Controllers\PhonenumberController::_get_envia_management_jobs($this);
		}

		if (\PPModule::is_active('voipmon')) {
			$ret['Monitoring']['Cdr'] = $this->cdrs;
		}

		return $ret;
	}

	/**
	 * return all mta objects
	 */
	public function mtas()
	{
		$dummies = Mta::withTrashed()->where('is_dummy', True)->get();
		$mtas = Mta::get();
		return array('dummies' => $dummies, 'mtas' => $mtas);
	}

	/**
	 * return a list [id => hostname] of all mtas
	 */
	public function mtas_list()
	{
		$ret = array();
		foreach ($this->mtas()['mtas'] as $mta)
		{
			$ret[$mta->id] = $mta->hostname;
		}

		return $ret;
	}

	/**
	 * return a list [id => hostname] of all mtas
	 */
	public function mtas_list_with_dummies()
	{
		$ret = array();
		foreach ($this->mtas() as $mta_tmp)
		{
			foreach ($mta_tmp as $mta)
			{
				$ret[$mta->id] = $mta->hostname;
			}
		}

		return $ret;
	}


	/**
	 * return a list [id => hostname, mac and contract information] of all mtas assigned to a contract
	 */
	public function mtas_list_only_contract_assigned()
	{
		$ret = array();
		foreach ($this->mtas()['mtas'] as $mta)
		{
			$contract = $mta->modem->contract;
			if (is_null($contract)) {
				continue;
			}
			else {
				$ret[$mta->id] = $mta->hostname.' ('.$mta->mac.") ⇒ ".$contract->number.": ".$contract->lastname.", ".$contract->firstname;
			}
		}

		return $ret;
	}


	/**
	 * link to management
	 */
	public function phonenumbermanagement() {
		return $this->hasOne('Modules\ProvVoip\Entities\PhonenumberManagement');
	}

	/**
	 * Phonenumbers can be related to EnviaOrders – if this module is active.
	 *
	 * @param	$withTrashed boolean; if true return also soft deleted orders; default is false
	 * @param	$whereStatement raw SQL query; default is returning of all orders
	 *				Attention: Syntax of given string has to meet SQL syntax!
	 * @return	EnviaOrders if module ProvVoipEnvia is enabled, else “null”
	 *
	 * @author Patrick Reichel
	 */
	public function enviaorders($withTrashed=False, $whereStatement="1") {

		if (!\PPModule::is_active('provvoipenvia')) {
			return null;
		}

		if ($withTrashed) {
			$orders = $this->belongsToMany('Modules\ProvVoipEnvia\Entities\EnviaOrder', 'enviaorder_phonenumber', 'phonenumber_id', 'enviaorder_id')->withTrashed()->whereRaw($whereStatement);
		}
		else {
			$orders = $this->belongsToMany('Modules\ProvVoipEnvia\Entities\EnviaOrder', 'enviaorder_phonenumber', 'phonenumber_id', 'enviaorder_id')->whereRaw($whereStatement);
			/* $orders = $this->belongsToMany('Modules\ProvVoipEnvia\Entities\EnviaOrder', 'enviaorder_phonenumber', 'phonenumber_id', 'enviaorder_id'); */
		}

		return $orders;
	}


	/**
	 * link to monitoring
	 *
	 * @author Ole Ernst
	 */
	public function cdrs()
	{
		if (\PPModule::is_active('voipmon')) {
			return $this->hasMany('Modules\VoipMon\Entities\Cdr');
		}

		return null;
	}

	/**
	 * Daily conversion (called by cron job)
	 *
	 * @author Patrick Reichel
	 */
	public function daily_conversion() {

		$this->set_active_state();
	}


	/**
	 * (De)Activate phonenumber depending on existance and (de)activation dates in PhonenumberManagement
	 *
	 * @author Patrick Reichel
	 */
	public function set_active_state() {

		$changed = False;

		$management = $this->phonenumbermanagement;

		if (is_null($management)) {

			// if there is still no management: deactivate the number
			// TODO: decide if a phonenumbermanagement is required in each case or not
			// until then: don't change the state on missing management
			/* if ($this->active) { */
			/* 	$this->active = False; */
			/* 	$changed = True; */
			/* } */
			\Log::info('No PhonenumberManagement for phonenumber '.$this->prefix_number.'/'.$this->number.' (ID '.$this->id.') – will not change the active state.');
		}
		else {

			// get the dates for this number
			$act = $management->activation_date;
			$deact = $management->deactivation_date;

			if (!boolval($act)) {

				// Activation date not yet reached: deactivate
				if ($this->active) {
					$this->active = False;
					$changed = True;
				}
			}
			elseif ($act > date('c')) {

				// Activation date not yet reached: deactivate
				if ($this->active) {
					$this->active = False;
					$changed = True;
				}
			}
			else {
				if (!boolval($deact)) {

					// activation date today or in the past, no deactivation date: activate
					if (!$this->active) {
						$this->active = True;
						$changed = True;
					}
				}
				else {
					if ($deact > date('c')) {

						// activation date today or in the past, deactivation date in the future: activate
						if (!$this->active) {
							$this->active = True;
							$changed = True;
						}
					}
					else {

						// deactivation date today or in the past: deactivate
						if ($this->active) {
							$this->active = False;
							$changed = True;
						}
					}
				}
			}
		}
		// write to database if there are changes
		if ($changed) {
			if ($this->active) {
				\Log::info('Activating phonenumber '.$this->prefix_number.'/'.$this->number.' (ID '.$this->id.').');
			}
			else {
				\Log::info('Deactivating phonenumber '.$this->prefix_number.'/'.$this->number.' (ID '.$this->id.').');
			}

			$this->save();
		};

	}


	/**
	 * Before deleting a modem and all children we have to check some things
	 *
	 * @author Patrick Reichel
	 */
	public function delete() {

		// deletion of modems with attached phonenumbers is not allowed with enabled Envia module
		// prevent user from (recursive and implicite) deletion of phonenumbers before termination at Envia!!
		// we have to check this here as using ModemObserver::deleting() with return false does not prevent the monster from deleting child model instances!
		/* d(\URL::previous()); */
		if (
			(\PPModule::is_active('ProvVoipEnvia'))
			&&
			(!is_null($this->phonenumbermanagement))
		) {

			// check from where the deletion request has been triggered and set the correct var to show information
			$prev = explode('?', \URL::previous())[0];
			$prev = \Str::lower($prev);
			$msg = "You are not allowed to delete a phonenumber with attached phonenumbermanagement!";
			if (\Str::endsWith($prev, 'edit')) {
				\Session::push('tmp_info_above_relations', $msg);
			}
			elseif (\Str::endsWith($prev, 'phonenumber')) {
				\Session::push('tmp_info_above_index_list', $msg);
			}

			return false;
		}

		// when arriving here: start the standard deletion procedure
		return parent::delete();
	}


	/**
	 * BOOT:
	 * - init phone observer
	 */
	public static function boot()
	{
		parent::boot();

		Phonenumber::observe(new PhonenumberObserver);
	}
}


/**
 * Phonenumber Observer Class
 * Handles changes on Phonenumbers
 *
 * can handle   'creating', 'created', 'updating', 'updated',
 *              'deleting', 'deleted', 'saving', 'saved',
 *              'restoring', 'restored',
 *
 * @author Patrick Reichel
 */
class PhonenumberObserver
{

	/**
	 * For Envia API we create username and login if not given.
	 * Otherwise Envia will do this – so we would have to ask for this data…
	 *
	 * @author Patrick Reichel
	 */
	protected function _create_login_data($phonenumber) {

		if (\PPModule::is_active('provvoipenvia') && ($phonenumber->mta->type == 'sip')) {

			if (!boolval($phonenumber->password)) {
				$phonenumber->password = \Acme\php\Password::generate_password(15, 'envia');
			}

			// username at Envia defaults to prefixnumber + number – we also do so
			if (!boolval($phonenumber->username)) {
				$phonenumber->username = $phonenumber->prefix_number.$phonenumber->number;
			}

		}
	}


	public function creating($phonenumber) {

		// TODO: ATM we don't force the creation of phonenumbermanagements – if we change our mind we can activate this line again
		// on creating there can not be a phonenumbermanagement – so we can set active state to false in each case
		// $phonenumber->active = 0;

		$this->_create_login_data($phonenumber);
	}


	public function created($phonenumber)
	{
		$phonenumber->mta->make_configfile();
		$phonenumber->mta->modem->restart_modem();

	}


	public function updating($phonenumber) {

		$this->_create_login_data($phonenumber);
	}


	public function updated($phonenumber)
	{

		$this->_create_login_data($phonenumber);

		// check if we have a MTA change
		$this->_check_and_process_mta_change($phonenumber);

		// changes on SIP data (username, password, sipdomain) have to be sent to external providers, too
		$this->_check_and_process_sip_data_change($phonenumber);

		// rebuild the current mta's configfile and restart the modem – has to be done in each case
		$phonenumber->mta->make_configfile();
		$phonenumber->mta->modem->restart_modem();

	}


	/**
	 * Apply changes on assigning a phonenumber to a new MTA.
	 *
	 * @author Patrick Reichel
	 */
	protected function _check_and_process_mta_change($phonenumber) {

		$old_mta_id = intval($phonenumber['original']['mta_id']);
		$new_mta_id = intval($phonenumber->mta_id);

		// if the MTA has not been changed we have nothing to do :-)
		if ($old_mta_id == $new_mta_id) {
			return;
		}

		// get an instance of both MTAs for easier access
		$old_mta = MTA::findOrFail($old_mta_id);
		$new_mta = $phonenumber->mta;

		// rebuild old MTA's config and restart the modem (we have to remove all information about this phonenumber)
		$old_mta->make_configfile();
		$old_mta->modem->restart_modem();

		// for all possible external providers we have to check if there is data to update, too
		$this->_check_and_process_mta_change_for_envia($phonenumber, $old_mta, $new_mta);

	}

	/**
	 * Change Envia related data on assigning a phonenumber to a new MTA.
	 * Here we have to decide if the change is permanent (customer got new modem) or temporary (e.g. for testing reasons).
	 *
	 * @author Patrick Reichel
	 */
	protected function _check_and_process_mta_change_for_envia($phonenumber, $old_mta, $new_mta) {

		// check if module is enabled
		if (!\PPModule::is_active('provvoipenvia')) {
			return;
		}

		// we need some helpers for easier access
		$old_modem = $old_mta->modem;
		$old_contract = $old_modem->contract;
		$new_modem = $new_mta->modem;
		$new_contract = $new_modem->contract;

		// check if new mta is assigned to another contract than the old one
		// if so: we assume that this is a temporary change only – we don't change any Envia data
		if ($old_contract->id != $new_contract->id) {

			$tmp_title = $old_contract->id.': '.$old_contract->firstname.' '.$old_contract->lastname.', '.$old_contract->city;
			$old_contract_href = \HTML::linkRoute('Contract.edit', $tmp_title, [$old_contract->id], ['target' => '_blank']);
			$tmp_title = $new_contract->id.': '.$new_contract->firstname.' '.$new_contract->lastname.', '.$new_contract->city;
			$new_contract_href = \HTML::linkRoute('Contract.edit', $tmp_title, [$new_contract->id], ['target' => '_blank']);

			\Session::push('tmp_info_above_form', 'New MTA belongs to another contract ('.$new_contract_href.') than the previous one ('.$old_contract_href.')<br>This seems to part of a a test only – so no Envia related data will be changed.<br>Make sure that the number finally is attached to the right MTA, especially BEFORE performing actions against Envia API!!');

			return;
		}

		// the moment we get here we take for sure that we have a permanent switch (defective old modem)
		// now we have to do a bunch of Envia data related work

		// first: get all the orders related to the number or the old modem
		// and overwrite the modem_id with the new modem's id
		$phonenumber_related_orders = $phonenumber->enviaorders(true)->get();
		$contract_related_orders = \Modules\ProvVoipEnvia\Entities\EnviaOrder::withTrashed()->where('modem_id', $old_modem->id)->get();

		// build a collection of all orders that need to be changed
		// this are all orders related to the current phonenumber or related to contract but not related to phonenumber (e.g. orders that created other phonenumbers)
		$related_orders = $phonenumber_related_orders;
		while ($tmp_order = $contract_related_orders->pop()) {
			$related_numbers = $tmp_order->phonenumbers;
			if ($related_numbers->isEmpty() || $related_numbers->contains($phonenumber)) {
				$related_orders->push($tmp_order);
			}
		}
		$related_orders = $related_orders->unique();

		// change the modem id to the value of the new modem
		foreach ($related_orders as $order) {
			$order->modem_id = $new_modem->id;
			$order->save();
		}

		// second: write all Envia related data from the old to the new modem
		$new_modem->contract_external_id = $old_modem->contract_external_id;
		$new_modem->contract_ext_creation_date = $old_modem->contract_ext_creation_date;
		$new_modem->contract_ext_termination_date = $old_modem->contract_ext_termination_date;
		$new_modem->installation_address_change_date = $old_modem->installation_address_change_date;
		$new_modem->save();

		// third: if there are no more numbers attached to the old modem: remove all Envia related data
		if (!$old_modem->has_phonenumbers_attached()) {
			$old_modem->remove_envia_related_data();
		}
		else {
			$attributes = ['target'=>'_blank'];

			// prepare the link (for view) for old modem (this may be useful as we get the breadcrumb for the new modem on our return to phonenumber.edit)
			$parameters = [
				'modem' => $old_modem->id,
			];
			$title = 'modem '.$old_modem->id. ' ('.$old_modem->mac.')';
			$modem_href = \HTML::linkRoute('Modem.edit', $title, $parameters, $attributes);

			// prepare the links to the phonenumbers still related to old modem (they probably also have to be moved to another MTA)
			$numbers = [];
			foreach ($old_modem->mtas as $tmp_mta) {
				foreach ($tmp_mta->phonenumbers->all() as $tmp_phonenumber) {
					$tmp_parameters = [
						'phonenumber' => $tmp_phonenumber->id,
					];
					$tmp_title = $tmp_phonenumber->prefix_number.'/'.$tmp_phonenumber->number;
					$tmp_href = \HTML::linkRoute('Phonenumber.edit', $tmp_title, $tmp_parameters, $attributes);
					array_push($numbers, $tmp_href);
				}
			}
			$numbers = '<br>&nbsp;&nbsp;'.implode('<br>&nbsp;&nbsp;', $numbers);

			\Session::push('tmp_info_above_form', "There are still phonenumbers attached to ".$modem_href."! Don't forget to move them, too:".$numbers);
		}

	}


	/**
	 * If SIP data has been changed there are probably changes at your provider needed!
	 *
	 * @author Patrick Reichel
	 */
	protected function _check_and_process_sip_data_change($phonenumber) {

		if (
			($phonenumber['original']['username'] != $phonenumber->username)
			||
			($phonenumber['original']['password'] != $phonenumber->password)
			||
			($phonenumber['original']['sipdomain'] != $phonenumber->sipdomain)
		) {
			$this->_check_and_process_sip_data_change_for_envia($phonenumber);
		}

	}


	/**
	 * If SIP data has been changed and module ProvVoipEnvia is enabled:
	 * Change this data at Envia, too
	 *
	 * @author Patrick Reichel
	 */
	protected function _check_and_process_sip_data_change_for_envia($phonenumber) {

		// check if module is enabled
		if (!\PPModule::is_active('provvoipenvia')) {
			return;
		}

		// TODO: check if this data can be changed automagically at Envia!
		$parameters = [
			'job' => 'voip_account_update',
			'origin' => urlencode(\URL::previous()),
			'phonenumber_id' => $phonenumber->id,
			];
		$title = 'DO THIS MANUALLY NOW!';
		$envia_href = \HTML::linkRoute('ProvVoipEnvia.request', $title, $parameters);

		\Session::push('tmp_info_above_form', 'Autochanging of SIP data at Envia is not implemented yet.<br>You have to '.$envia_href);

	}


	public function deleted($phonenumber)
	{
		$phonenumber->mta->make_configfile();
		$phonenumber->mta->modem->restart_modem();

		// check if this number has been the last on old modem ⇒ if so remove envia related data from modem
		if (!$phonenumber->mta->modem->has_phonenumbers_attached()) {
			$phonenumber->mta->modem->remove_envia_related_data();
		}
	}
}
