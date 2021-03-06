<?php
/**********************************************************************
    Copyright (C) FrontAccounting, LLC.
	Released under the terms of the GNU General Public License, GPL, 
	as published by the Free Software Foundation, either version 3 
	of the License, or (at your option) any later version.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
    See the License here <http://www.gnu.org/licenses/gpl-3.0.html>.
***********************************************************************/
//-----------------------------------------------------------------------------
//
//	Entry/Modify Sales Quotations
//	Entry/Modify Sales Order
//	Entry Direct Delivery
//	Entry Direct Invoice
//

$path_to_root = "..";
$page_security = 'SA_OPNBALANCE';

include_once($path_to_root . "/sales/includes/cart_class.inc");
include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/sales/includes/sales_ui.inc");
include_once($path_to_root . "/sales/includes/ui/sales_order_ui.inc");
include_once($path_to_root . "/sales/includes/sales_db.inc");
include_once($path_to_root . "/sales/includes/db/sales_types_db.inc");
include_once($path_to_root . "/reporting/includes/reporting.inc");


$js = '';

if ($use_popup_windows) {
	$js .= get_js_open_window(900, 500);
}

if ($use_date_picker) {
	$js .= get_js_date_picker();
}

$_SESSION['page_title'] = _($help_context = "Opening Balance");
page($_SESSION['page_title'], false, false, "", $js);

if (isset($_GET['OpeningBalance'])) {
	$_POST['taxi_customer'] = $_GET['OpeningBalance'];
	create_cart(ST_SALESINVOICE, 0);
}
//-----------------------------------------------------------------------------

if (list_updated('branch_id')) {
	// when branch is selected via external editor also customer can change
	$br = get_branch(get_post('branch_id'));
	$_POST['customer_id'] = $br['debtor_no'];
	$Ajax->activate('customer_id');
}

if (isset($_GET['AddedDI'])) {
	$invoice = $_GET['AddedDI'];

	display_notification_centered(sprintf(_("Invoice # %d has been entered."), $invoice));

	submenu_view(_("&View This Invoice"), ST_SALESINVOICE, $invoice);

	submenu_print(_("&Print Sales Invoice"), ST_SALESINVOICE, $invoice."-".ST_SALESINVOICE, 'prtopt');
	submenu_print(_("&Email Sales Invoice"), ST_SALESINVOICE, $invoice."-".ST_SALESINVOICE, null, 1);
	set_focus('prtopt');
	
	$sql = "SELECT trans_type_from, trans_no_from FROM ".TB_PREF."cust_allocations
			WHERE trans_type_to=".ST_SALESINVOICE." AND trans_no_to=".db_escape($invoice);
	$result = db_query($sql, "could not retrieve customer allocation");
	$row = db_fetch($result);
	if ($row !== false)
		submenu_print(_("Print &Receipt"), $row['trans_type_from'], $row['trans_no_from']."-".$row['trans_type_from'], 'prtopt');

	display_note(get_gl_view_str(ST_SALESINVOICE, $invoice, _("View the GL &Journal Entries for this Invoice")),0, 1);

	if ((isset($_GET['Type']) && $_GET['Type'] == 1))
		submenu_option(_("Enter a &New Template Invoice"), 
			"/sales/inquiry/sales_orders_view.php?InvoiceTemplates=Yes");
	else
		submenu_option(_("Enter a &New Direct Invoice"),
			"/sales/sales_order_entry.php?NewInvoice=0");

	if ($row === false)
		submenu_option(_("Entry &customer payment for this invoice"), "/sales/customer_payments.php?SInvoice=".$invoice);

	submenu_option(_("Add an Attachment"), "/admin/attachments.php?filterType=".ST_SALESINVOICE."&trans_no=$invoice");

	display_footer_exit();
}elseif (isset($_GET['AddedOP'])) {
	$invoice = $_GET['AddedOP'];

	display_notification_centered(sprintf(_("Opening Balance # %d has been entered."), $invoice));

	submenu_view(_("&View This Invoice"), ST_SALESINVOICE, $invoice);

	display_footer_exit();
}
 else
	check_edit_conflicts();
//-----------------------------------------------------------------------------

function copy_to_cart()
{
	$cart = &$_SESSION['Items'];

	$cart->reference = $_POST['ref'];

	$cart->Comments =  $_POST['Comments'];

	$cart->line_total =  $_POST['line_total'];

	$cart->document_date = $_POST['OrderDate'];

	$newpayment = false;

	if (isset($_POST['payment']) && ($cart->payment != $_POST['payment'])) {
		$cart->payment = $_POST['payment'];
		$cart->payment_terms = get_payment_terms($_POST['payment']);
		$newpayment = true;
	}
	if ($cart->payment_terms['cash_sale']) {
		if ($newpayment) {
			$cart->due_date = $cart->document_date;
			$cart->phone = $cart->cust_ref = $cart->delivery_address = '';
			$cart->ship_via = 0;
			$cart->deliver_to = '';
		}
	} else {
		$cart->due_date = $_POST['delivery_date'];
		$cart->cust_ref = $_POST['cust_ref'];
		$cart->deliver_to = $_POST['deliver_to'];
		$cart->delivery_address = $_POST['delivery_address'];
		$cart->phone = $_POST['phone'];
		$cart->ship_via = $_POST['ship_via'];
	}
	$cart->Location = $_POST['Location'];
	$cart->freight_cost = input_num('freight_cost');
	if (isset($_POST['email']))
		$cart->email =$_POST['email'];
	else
		$cart->email = '';
	$cart->customer_id	= $_POST['customer_id'];
	$cart->Branch = $_POST['branch_id'];
	$cart->sales_type = $_POST['sales_type'];

	if ($cart->trans_type!=ST_SALESORDER && $cart->trans_type!=ST_SALESQUOTE) { // 2008-11-12 Joe Hunt
		$cart->dimension_id = $_POST['dimension_id'];
		$cart->dimension2_id = $_POST['dimension2_id'];
	}
	$cart->ex_rate = input_num('_ex_rate', null);
}

//-----------------------------------------------------------------------------

function copy_from_cart()
{
	$cart = &$_SESSION['Items'];
	$_POST['ref'] = $cart->reference;
	$_POST['Comments'] = $cart->Comments;

	$_POST['OrderDate'] = $cart->document_date;
	$_POST['delivery_date'] = $cart->due_date;
	$_POST['cust_ref'] = $cart->cust_ref;
	$_POST['freight_cost'] = price_format($cart->freight_cost);

	$_POST['deliver_to'] = $cart->deliver_to;
	$_POST['delivery_address'] = $cart->delivery_address;
	$_POST['phone'] = $cart->phone;
	$_POST['Location'] = $cart->Location;
	$_POST['ship_via'] = $cart->ship_via;

	$_POST['customer_id'] = $cart->customer_id;

	$_POST['branch_id'] = $cart->Branch;
	$_POST['sales_type'] = $cart->sales_type;
	// POS 
	$_POST['payment'] = $cart->payment;
	if ($cart->trans_type!=ST_SALESORDER && $cart->trans_type!=ST_SALESQUOTE) { // 2008-11-12 Joe Hunt
		$_POST['dimension_id'] = $cart->dimension_id;
		$_POST['dimension2_id'] = $cart->dimension2_id;
	}
	$_POST['cart_id'] = $cart->cart_id;
	$_POST['_ex_rate'] = $cart->ex_rate;
}
//--------------------------------------------------------------------------------

function line_start_focus() {
  global 	$Ajax;

  $Ajax->activate('items_table');
  set_focus('_stock_id_edit');
}

//--------------------------------------------------------------------------------

function can_process() { 
	global $Refs;
	
	
	if (!get_post('customer_id')) 
	{
		display_error(_("There is no customer selected."));
		set_focus('customer_id');
		return false;
	} 
	
	/*if (!get_post('branch_id')) 
	{
		display_error(_("This customer has no branch defined."));
		set_focus('branch_id');
		return false;
	} 
	
	if (!is_date($_POST['OrderDate'])) {
		display_error(_("The entered date is invalid."));
		set_focus('OrderDate');
		return false;
	}*/
	if ($_SESSION['Items']->trans_type!=ST_SALESORDER && $_SESSION['Items']->trans_type!=ST_SALESQUOTE && !is_date_in_fiscalyear($_POST['OrderDate'])) {
		display_error(_("The entered date is not in fiscal year"));
		set_focus('OrderDate');
		return false;
	}
	if (count($_SESSION['Items']->line_items) == 0)	{
		display_error(_("You must enter at least one non empty item line."));
		set_focus('AddItem');
		return false;
	}
	if ($_SESSION['Items']->payment_terms['cash_sale'] == 0) {
	if (strlen($_POST['deliver_to']) <= 1) {
		display_error(_("You must enter the person or company to whom delivery should be made to."));
		set_focus('deliver_to');
		return false;
	}


		/*if ($_SESSION['Items']->trans_type != ST_SALESQUOTE && strlen($_POST['delivery_address']) <= 1) {
			display_error( _("You should enter the street address in the box provided. Orders cannot be accepted without a valid street address."));
			set_focus('delivery_address');
			return false;
		}*/

		if ($_POST['freight_cost'] == "")
			$_POST['freight_cost'] = price_format(0);

		if (!check_num('freight_cost',0)) {
			display_error(_("The shipping cost entered is expected to be numeric."));
			set_focus('freight_cost');
			return false;
		}
		if (!is_date($_POST['delivery_date'])) {
			if ($_SESSION['Items']->trans_type==ST_SALESQUOTE)
				display_error(_("The Valid date is invalid."));
			else	
				display_error(_("The delivery date is invalid."));
			set_focus('delivery_date');
			return false;
		}
		//if (date1_greater_date2($_SESSION['Items']->document_date, $_POST['delivery_date'])) {
		if (date1_greater_date2($_POST['OrderDate'], $_POST['delivery_date'])) {
			if ($_SESSION['Items']->trans_type==ST_SALESQUOTE)
				display_error(_("The requested valid date is before the date of the quotation."));
			else	
				display_error(_("The requested delivery date is before the date of the order."));
			set_focus('delivery_date');
			return false;
		}
	}
	else
	{
		if (!db_has_cash_accounts())
		{
			display_error(_("You need to define a cash account for your Sales Point."));
			return false;
		}	
	}	
	if (!$Refs->is_valid($_POST['ref'])) {
		display_error(_("You must enter a reference."));
		set_focus('ref');
		return false;
	}
	/*
	if (!db_has_currency_rates($_SESSION['Items']->customer_currency, $_POST['OrderDate']))
		return false;
	*/
   	if ($_SESSION['Items']->get_items_total() < 0) {
		display_error("Invoice total amount cannot be less than zero.");
		return false;
	}
	return true;
}

//-----------------------------------------------------------------------------
//echo "<pre>";print_r($_SESSION['Items']);echo "</pre>";exit;

if (isset($_POST['update'])) {
	copy_to_cart();
	$Ajax->activate('items_table');
}

if (isset($_POST['ProcessOrder']) && can_process()) {
	
	
	
	copy_to_cart();
	$modified = ($_SESSION['Items']->trans_no != 0);
	$so_type = $_SESSION['Items']->so_type;

	//echo "<pre>";print_r($_SESSION['Items']);echo "</pre>";exit;

	$ret = $_SESSION['Items']->write(1);
	if ($ret == -1)
	{
		display_error(_("The entered reference is already in use."));
		$ref = get_next_reference($_SESSION['Items']->trans_type);
		if ($ref != $_SESSION['Items']->reference)
		{
			display_error(_("The reference number field has been increased. Please save the document again."));
			$_POST['ref'] = $_SESSION['Items']->reference = $ref;
			$Ajax->activate('ref');
		}	
		set_focus('ref');
	}
	else
	{
		if (count($messages)) { // abort on failure or error messages are lost
			$Ajax->activate('_page_body');
			display_footer_exit();
		}
		$trans_no = key($_SESSION['Items']->trans_no);
		$trans_type = $_SESSION['Items']->trans_type;
		new_doc_date($_SESSION['Items']->document_date);
		processing_end();
		
		meta_forward($_SERVER['PHP_SELF'], "AddedOP=$trans_no&Type=$so_type");
		
	}	
}

//--------------------------------------------------------------------------------

function check_item_data()
{
	global $SysPrefs, $allow_negative_prices;
	
	$is_inventory_item = is_inventory_item(get_post('stock_id'));
	if(!get_post('stock_id_text', true)) {
		display_error( _("Item description cannot be empty."));
		set_focus('stock_id_edit');
		return false;
	}
	elseif (!check_num('qty', 0) || !check_num('Disc', 0, 100)) {
		display_error( _("The item could not be updated because you are attempting to set the quantity ordered to less than 0, or the discount percent to more than 100."));
		set_focus('qty');
		return false;
	} elseif (!check_num('price', 0) && (!$allow_negative_prices || $is_inventory_item)) {
		display_error( _("Price for inventory item must be entered and can not be less than 0"));
		set_focus('price');
		return false;
	} elseif (isset($_POST['LineNo']) && isset($_SESSION['Items']->line_items[$_POST['LineNo']])
	    && !check_num('qty', $_SESSION['Items']->line_items[$_POST['LineNo']]->qty_done)) {

		set_focus('qty');
		display_error(_("You attempting to make the quantity ordered a quantity less than has already been delivered. The quantity delivered cannot be modified retrospectively."));
		return false;
	} // Joe Hunt added 2008-09-22 -------------------------
	elseif ($is_inventory_item && $_SESSION['Items']->trans_type!=ST_SALESORDER && $_SESSION['Items']->trans_type!=ST_SALESQUOTE 
		&& !$SysPrefs->allow_negative_stock())
	{
		$qoh = get_qoh_on_date($_POST['stock_id'], $_POST['Location'], $_POST['OrderDate']);
		if (input_num('qty') > $qoh)
		{
			$stock = get_item($_POST['stock_id']);
			display_error(_("The delivery cannot be processed because there is an insufficient quantity for item:") .
				" " . $stock['stock_id'] . " - " . $stock['description'] . " - " .
				_("Quantity On Hand") . " = " . number_format2($qoh, get_qty_dec($_POST['stock_id'])));
			return false;
		}
		return true;
	}
	$cost_home = get_standard_cost(get_post('stock_id')); // Added 2011-03-27 Joe Hunt
	$cost = $cost_home / get_exchange_rate_from_home_currency($_SESSION['Items']->customer_currency, $_SESSION['Items']->document_date);
	if (input_num('price') < $cost)
	{
		$dec = user_price_dec();
		$curr = $_SESSION['Items']->customer_currency;
		$price = number_format2(input_num('price'), $dec);
		if ($cost_home == $cost)
			$std_cost = number_format2($cost_home, $dec);
		else
		{
			$price = $curr . " " . $price;
			$std_cost = $curr . " " . number_format2($cost, $dec);
		}
		display_warning(sprintf(_("Price %s is below Standard Cost %s"), $price, $std_cost));
	}	
	return true;
}

//--------------------------------------------------------------------------------

function handle_update_item()
{
	if ($_POST['UpdateItem'] != '' && check_item_data()) {
		$_SESSION['Items']->update_cart_item($_POST['LineNo'],
		 input_num('qty'), input_num('price'),
		 input_num('Disc') / 100, $_POST['item_description'] );
	}
	page_modified();
  line_start_focus();
}

//--------------------------------------------------------------------------------

function handle_delete_item($line_no)
{
    if ($_SESSION['Items']->some_already_delivered($line_no) == 0) {
	    $_SESSION['Items']->remove_from_cart($line_no);
    } else {
	display_error(_("This item cannot be deleted because some of it has already been delivered."));
    }
    line_start_focus();
}

//--------------------------------------------------------------------------------

function handle_new_item()
{

	if (!check_item_data()) {
			return;
	}
	
	
	add_to_order($_SESSION['Items'], get_post('stock_id'), input_num('qty'),
		input_num('price'), input_num('Disc') / 100, get_post('stock_id_text'),get_post('trip_voucher'));

	unset($_POST['_stock_id_edit'], $_POST['stock_id']);
	page_modified();
	line_start_focus();
}

//--------------------------------------------------------------------------------

function  handle_cancel_order($taxi_customer)
{
	global $path_to_root, $Ajax;


	if ($_SESSION['Items']->trans_type == ST_CUSTDELIVERY) {
		display_notification(_("Direct delivery entry has been cancelled as requested."), 1);
		submenu_option(_("Enter a New Sales Delivery"),	"/sales/sales_order_entry.php?NewDelivery=1");
	} elseif ($_SESSION['Items']->trans_type == ST_SALESINVOICE) {
		display_notification(_("Opening Balance entry has been cancelled as requested."), 1);
		submenu_option(_("Enter a New Opening Balance"),"/sales/customer_opening_balance.php?OpeningBalance=".$taxi_customer);
	} elseif ($_SESSION['Items']->trans_type == ST_SALESQUOTE)
	{
		if ($_SESSION['Items']->trans_no != 0) 
			delete_sales_order(key($_SESSION['Items']->trans_no), $_SESSION['Items']->trans_type);
		display_notification(_("This sales quotation has been cancelled as requested."), 1);
		submenu_option(_("Enter a New Sales Quotation"), "/sales/sales_order_entry.php?NewQuotation=Yes");
	} else { // sales order
		if ($_SESSION['Items']->trans_no != 0) {
			$order_no = key($_SESSION['Items']->trans_no);
			if (sales_order_has_deliveries($order_no))
			{
				close_sales_order($order_no);
				display_notification(_("Undelivered part of order has been cancelled as requested."), 1);
				submenu_option(_("Select Another Sales Order for Edition"), "/sales/inquiry/sales_orders_view.php?type=".ST_SALESORDER);
			} else {
				delete_sales_order(key($_SESSION['Items']->trans_no), $_SESSION['Items']->trans_type);

				display_notification(_("This sales order has been cancelled as requested."), 1);
				submenu_option(_("Enter a New Sales Order"), "/sales/sales_order_entry.php?NewOrder=Yes");
			}
		} else {
			processing_end();
			meta_forward($path_to_root.'/index.php','application=orders');
		}
	}
	$Ajax->activate('_page_body');
	processing_end();
	display_footer_exit();
}

//--------------------------------------------------------------------------------

function create_cart($type, $trans_no,$trip_voucher=0)
{ 
	global $Refs;

	if (!$_SESSION['SysPrefs']->db_ok) // create_cart is called before page() where the check is done
		return;

	

	processing_start();

	if (isset($_GET['NewQuoteToSalesOrder']))
	{
		$trans_no = $_GET['NewQuoteToSalesOrder'];
		$doc = new Cart(ST_SALESQUOTE, $trans_no, true);
		$doc->Comments = _("Sales Quotation") . " # " . $trans_no;
		$_SESSION['Items'] = $doc;
	}	
	elseif($type != ST_SALESORDER && $type != ST_SALESQUOTE && $trans_no != 0) { // this is template

		$doc = new Cart(ST_SALESORDER, array($trans_no));
		$doc->trans_type = $type;
		$doc->trans_no = 0;
		
		
		
		$doc->document_date = new_doc_date();
		if ($type == ST_SALESINVOICE) {
			$doc->due_date = get_invoice_duedate($doc->payment, $doc->document_date);
			$doc->pos = get_sales_point(user_pos());
		} else
			$doc->due_date = $doc->document_date;
		$doc->reference = $Refs->get_next($doc->trans_type);
		//$doc->Comments='';
		foreach($doc->line_items as $line_no => $line) {
			$doc->line_items[$line_no]->qty_done = 0;
		}
		$_SESSION['Items'] = $doc;
	} else{
		$_SESSION['Items'] = new Cart($type, array($trans_no),false,$trip_voucher);
		
	}
	copy_from_cart();
}

//--------------------------------------------------------------------------------

if (isset($_POST['CancelOrder']))
	handle_cancel_order($_POST['taxi_customer']);

$id = find_submit('Delete');
if ($id!=-1)
	handle_delete_item($id);

if (isset($_POST['UpdateItem']))
	handle_update_item();

if (isset($_POST['AddItem']))
	handle_new_item();

if (isset($_POST['CancelItemChanges'])) {
	line_start_focus();
}

//--------------------------------------------------------------------------------
//check_db_has_stock_items(_("There are no inventory items defined in the system."));

check_db_has_customer_branches(_("There are no customers, or there are no customers with branches. Please define customers and customer branches."));

if ($_SESSION['Items']->trans_type == ST_SALESINVOICE) {
	$idate = _("Date:");
	$orderitems = _("Opening Balance Details");
	$deliverydetails = _("Enter Delivery Details and Confirm Invoice");
	
	
}
start_form();

//cnc code----------------------
$cnc_voucher = false;
if(isset($_GET['OpeningBalance']) ){

	$_SESSION['Items']->customer_id = get_cnc_customer_id($_GET['OpeningBalance']);
	$customer = get_customer($_SESSION['Items']->customer_id);
	$_SESSION['Items']->customer_name = $customer['name'];


	$_SESSION['Items']->customer_currency = @$customer['curr_code'] ;
	$_SESSION['Items']->sales_type = @$customer['sales_type'] ;
	$_SESSION['Items']->Branch = get_cnc_customer_branch($_SESSION['Items']->customer_id);

	$_SESSION['Items']->trip_voucher = 0;

	$_SESSION['Items']->tax_group_from_cnc = TAX_GROUP_EX;
}
//-----------------------------------

hidden('cart_id');
hidden('taxi_customer');
//$customer_error = display_opening_balance_header($_SESSION['Items'],
	//($_SESSION['Items']->any_already_delivered() == 0), $idate,$cnc_voucher);

$customer_error = display_opening_balance_header($_SESSION['Items'],
	($_SESSION['Items']->any_already_delivered() == 0), $idate,$cnc_voucher);



if ($customer_error == "") {
	start_table(TABLESTYLE, "width=100%", 10);
	echo "<tr><td>";
	display_opening_balance_summary($orderitems, $_SESSION['Items'], true);
	echo "</td></tr>";
	//echo "<tr><td>";
	//display_delivery_details($_SESSION['Items']);
	//echo "</td></tr>";
	end_table(1);

	if ($_SESSION['Items']->trans_no == 0) {

		//submit_center_first('ProcessOrder', $porder,
		 //   _('Check entered data and save document'), 'default');
		 
		submit_center_first('ProcessOrder', 'Place Opening Balance',
		    _('Check entered data and save document'));
		submit_center_last('CancelOrder', 'Cancel',
	   		_('Cancels document entry or removes sales order when editing an old document'), true);
		submit_js_confirm('CancelOrder', _('You are about to void this Document.\nDo you want to continue?'));
	} else {
		submit_center_first('ProcessOrder', 'Place Opening Balance',
		    _('Validate changes and update document'), 'default');
		submit_center_last('CancelOrder', 'Cancel',
	   		_('Cancels document entry or removes sales order when editing an old document'), true);
		if ($_SESSION['Items']->trans_type==ST_SALESORDER)
			submit_js_confirm('CancelOrder', _('You are about to cancel undelivered part of this order.\nDo you want to continue?'));
		else
			submit_js_confirm('CancelOrder', _('You are about to void this Document.\nDo you want to continue?'));
	}

} else {
	display_error($customer_error);
}

end_form();
end_page();
?>
