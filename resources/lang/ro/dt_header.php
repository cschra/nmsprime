<?php

return [
    // Index DataTable Header
    'id'            => 'ID',
    'prio'          => 'Priority',
    // Auth
    'users.login_name' => 'Login Name',
    'users.first_name' => 'Given Name',
    'users.last_name' => 'Family Name',
    'roles.title' => 'Nume',
    'roles.rank' => 'Rank',
    'roles.description' => 'Description',
    // GuiLog
    'guilog.created_at' => 'Time',
    'guilog.username' => 'User',
    'guilog.method' => 'Action',
    'guilog.model' => 'Model',
    'guilog.model_id' => 'Model ID',
    // Company
    'company.name' => 'Company Name',
    'company.city' => 'Oraș',
    'company.phone' => 'Mobile Number',
    'company.mail' => 'E-mail',
    // Costcenter
    'costcenter.name' => 'CostCenter',
    'costcenter.number' => 'Number',
    //Invoices
    'invoice.type' => 'Type',
    'invoice.year' => 'Year',
    'invoice.month' => 'Month',
    //Item
    'item.valid_from' => 'Item Valid from',
    'item.valid_from_fixed' => 'Item Valid from fixed',
    'item.valid_to' => 'Item Valid to',
    'item.valid_to_fixed' => 'Item Valid to fixed',
    'product' => [
        'proportional' => 'Proportionate',
        'type' => 'Type',
        'name' => 'Product Name',
        'price' => 'Price',
    ],
    // Salesman
    'salesman.id' => 'ID',
    'salesman_id' 		=> 'Salesman-ID',
    'salesman_firstname' => 'Prenume',
    'salesman_lastname' => 'Nume',
    'commission in %' 	=> 'Commission in %',
    'contract_nr' 		=> 'Nr de contract',
    'contract_name' 	=> 'Client',
    'contract_start' 	=> 'Contract start',
    'contract_end' 		=> 'Contract end',
    'product_name' 		=> 'Product',
    'product_type' 		=> 'Product type',
    'product_count' 	=> 'Count',
    'charge' 			=> 'Charge',
    'salesman.lastname' => 'Nume',
    'salesman.firstname' => 'Prenume',
    'salesman_commission' => 'Commission',
    'sepaaccount_id' 	=> 'SEPA-account',
    // SepaAccount
    'sepaaccount.name' => 'Account Name',
    'sepaaccount.institute' => 'Institute',
    'sepaaccount.iban' => 'IBAN',
    // SepaMandate
    'sepamandate.sepa_holder' => 'Account Holder',
    'sepamandate.sepa_valid_from' => 'Valid from',
    'sepamandate.sepa_valid_to' => 'Valid to',
    'sepamandate.reference' => 'Account reference',
    // SettlementRun
    'settlementrun.year' => 'Year',
    'settlementrun.month' => 'Month',
    'settlementrun.created_at' => 'Created at',
    'verified' => 'Verified?',
    // MPR
    'mpr.name' => 'Name',
    'mpr.id'    => 'ID',
    // NetElement
    'netelement.id' => 'ID',
    'netelement.name' => 'Netelement',
    'netelement.ip' => 'IP Adress',
    'netelement.state' => 'Judet',
    'netelement.pos' => 'Position',
    'netelement.options' => 'Options',
    // NetElementType
    'netelementtype.name' => 'Netelementtype',
    //HfcSnmp
    'parameter.oid.name' => 'OID Name',
    //Mibfile
    'mibfile.id' => 'ID',
    'mibfile.name' => 'Mibfile',
    'mibfile.version' => 'Versiune',
    // OID
    'oid.name_gui' => 'GUI Label',
    'oid.name' => 'OID Name',
    'oid.oid' => 'OID',
    'oid.access' => 'Access Type',
    //SnmpValue
    'snmpvalue.oid_index' => 'OID Index',
    'snmpvalue.value' => 'OID Value',
    // MAIL
    'email.localpart' => 'Local Part',
    'email.index' => 'Primary E-Mail?',
    'email.greylisting' => 'Greylisting active?',
    'email.blacklisting' => 'On Blacklist?',
    'email.forwardto' => 'Forward to:',
    // CMTS
    'cmts.id' => 'ID',
    'cmts.hostname' => 'Hostname',
    'cmts.ip' => 'Adresa IP',
    'cmts.company' => 'Producator',
    'cmts.type' => 'Type',
    // Contract
    'contract.company' => 'Companie',
    'contract.number' => 'Număr',
    'contract.firstname' => 'Prenume',
    'contract.lastname' => 'Surname',
    'contract.zip' => 'Cod Poștal',
    'contract.city' => 'Oraș',
    'contract.street' => 'Stradă',
    'contract.house_number' => 'Housenr',
    'contract.district' => 'Sector',
    'contract.contract_start' => 'Contract Start',
    'contract.contract_end' => 'Contract End',
    // Domain
    'domain.name' => 'Domain',
    'domain.type' => 'Type',
    'domain.alias' => 'Alias',
    // Endpoint
    'endpoint.ip' => 'IP',
    'endpoint.hostname' => 'Hostname',
    'endpoint.mac' => 'MAC',
    'endpoint.description' => 'Description',
    // IpPool
    'ippool.id' => 'ID',
    'ippool.type' => 'Type',
    'ippool.net' => 'Net',
    'ippool.netmask' => 'Netmask',
    'ippool.router_ip' => 'Router IP',
    'ippool.description' => 'Description',
    // Modem
    'modem.house_number' => 'Housenr',
    'modem.id' => 'Modem Number',
    'modem.mac' => 'MAC Address',
    'modem.model' => 'Model',
    'modem.sw_rev' => 'Firmware Version',
    'modem.name' => 'Modem Name',
    'modem.firstname' => 'First name',
    'modem.lastname' => 'Surname',
    'modem.city' => 'City',
    'modem.street' => 'Street',
    'modem.district' => 'District',
    'modem.us_pwr' => 'US level',
    'modem.geocode_source' => 'Geocode origin',
    'modem.inventar_num' => 'Serial Nr',
    'contract_valid' => 'Contract valid?',
    // QoS
    'qos.name' => 'QoS Name',
    'qos.ds_rate_max' => 'Maximum DS Speed',
    'qos.us_rate_max' => 'Maximum US Speed',
    // Mta
    'mta.hostname' => 'Hostname',
    'mta.mac' => 'MAC-Adress',
    'mta.type' => 'VOIP Protocol',
    // Configfile
    'configfile.name' => 'Configfile',
    // PhonebookEntry
    'phonebookentry.id' => 'ID',
    // Phonenumber
    'phonenumber.prefix_number' => 'Prefix',
    'phonenumber.number' => 'Number',
    'phonenr_act' => 'Activation date',
    'phonenr_deact' => 'Deactivation date',
    'phonenr_state' => 'Status',
    'modem_city' => 'Modem city',
    'sipdomain' => 'Registrar',
    // Phonenumbermanagement
    'phonenumbermanagement.id' => 'ID',
    'phonenumbermanagement.activation_date' => 'Activation date',
    'phonenumbermanagement.deactivation_date' => 'Deactivation date',
    // PhoneTariff
    'phonetariff.name' => 'Phone Tariff',
    'phonetariff.type' => 'Type',
    'phonetariff.description' => 'Description',
    'phonetariff.voip_protocol' => 'VOIP Protocol',
    'phonetariff.usable' => 'Usable?',
    // ENVIA enviaorder
    'enviaorder.ordertype'  => 'Order Type',
    'enviaorder.orderstatus'  => 'Order Status',
    'escalation_level' => 'Escalation Level',
    'enviaorder.created_at'  => 'Created at',
    'enviaorder.updated_at'  => 'Updated at',
    'enviaorder.orderdate'  => 'Order date',
    'enviaorder_current'  => 'Action needed?',
    //ENVIA Contract
    'enviacontract.envia_contract_reference' => 'envia TEL Contract reference',
    'enviacontract.state' => 'Status',
    'enviacontract.start_date' => 'Start Date',
    'enviacontract.end_date' => 'End Date',
    // CDR
    'cdr.calldate' => 'Call Date',
    'cdr.caller' => 'Caller',
    'cdr.called' => 'Called',
    'cdr.mos_min_mult10' => 'Minimum MOS',
    // Numberrange
    'numberrange.id' => 'ID',
    'numberrange.name' => 'Name',
    'numberrange.start' => 'Start',
    'numberrange.end' => 'End',
    'numberrange.prefix' => 'Prefix',
    'numberrange.suffix' => 'Suffix',
    'numberrange.type' => 'Type',
    'numberrange.costcenter.name' => 'Cost center',
    // Ticket
    'ticket.id' => 'ID',
    'ticket.name' => 'Title',
    'ticket.type' => 'Type',
    'ticket.priority' => 'Priority',
    'ticket.state' => 'State',
    'ticket.user_id' => 'Created by',
    'ticket.created_at' => 'Created at',
    'ticket.assigned_users' => 'Assigned Users',
    'assigned_users' => 'Assigned Users',
    'tickettypes.name' => 'Type',
];
