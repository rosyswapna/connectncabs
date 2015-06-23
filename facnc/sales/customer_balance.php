<?php 

$page_security = 'SA_SALESCUSTBALANCE';
$path_to_root = "..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");

include_once($path_to_root . "/reporting/includes/reporting.inc");


$js = "";
if ($use_popup_windows) {
	$js .= get_js_open_window(900, 500);
}
if ($use_date_picker) {
	$js .= get_js_date_picker();
}

page(_($help_context = "Customer Balance"), false, false, "", $js);




//-----------------------------------------------------------------------------------


//-----------------------------------------------------------------------------------
if(isset($_POST['PrintCustomerBalance'])){

	global $Ajax;

	$url = $path_to_root.'/reporting/prn_redirect.php?';
	$pars = array();
	foreach($_POST['PARAM'] as $key=>$val){
		$pars['PARAM_'.$key] = $val;
	}
	redirect_to_print_popup(101,$pars);
	
}

//-----------------------------------------------------------------------------------
start_form(false, false, $_SERVER['PHP_SELF']);

	start_table(TABLESTYLE_NOBORDER);


		start_row();
			
			date_cells(_("From:"), 'PARAM[0]', '', null, -30);

			date_cells(_("To:"), 'PARAM[1]', '', null);
		end_row();

		start_row();

			customer_list_cells(_("Customer:"), 'PARAM[2]', '', _("All Customers"));
			yesno_list_cells("Show Balance",'PARAM[3]');
			
		end_row();

		start_row();
			currencies_list_cells(_("Currency"),'PARAM[4]');
			yesno_list_cells("Suppress Zeros",'PARAM[5]');

		end_row();

		start_row();
			textarea_cells(_('Comments'),'PARAM[6]',NULL,30,4);
			orientation_list_cells("Orientation",'PARAM[7]');
		end_row();

		start_row();
		end_row();
		
		//print_destination_list_row(_("Destination"),'PARAM[8]');
		hidden('PARAM[8]',1);

	end_table();

	submit_center('PrintCustomerBalance', _("Print") ,true, _("Print Customer Balance"),'default');
		
end_form();
//-----------------------------------------------------------------------------------

end_page();


?>
