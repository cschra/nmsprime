<?php

namespace Modules\HfcSnmp\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class HfcSnmpController extends \BaseController {

	public function index()
	{
		return View::make('hfcsnmp::index');
	}

}