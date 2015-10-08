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
$page_security = 'SA_GLTRANSVIEW';
$path_to_root = "../..";
include_once($path_to_root . "/includes/session.inc");


include_once($path_to_root . "/admin/db/fiscalyears_db.inc");
include_once($path_to_root . "/includes/date_functions.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/includes/data_checks.inc");

include_once($path_to_root . "/gl/includes/gl_db.inc");

$js = '';
set_focus('account');
if ($use_popup_windows)
	$js .= get_js_open_window(800, 500);
if ($use_date_picker)
	$js .= get_js_date_picker();

page(_($help_context = "General Ledger Inquiry"), false, false, '', $js);
//----------------------------------------------------------------------------------------------------
// Ajax updates
//
if (get_post('Show')) 
{
	$Ajax->activate('trans_tbl');
}

if(isset($_GET['DriverGLInquiry'])){

	$_POST["Supplier"] = get_cnc_supplier_id($_GET['DriverGLInquiry']);

	
}


if (isset($_GET["TransFromDate"]))
	$_POST["TransFromDate"] = $_GET["TransFromDate"];
if (isset($_GET["TransToDate"]))
	$_POST["TransToDate"] = $_GET["TransToDate"];

if (isset($_GET["Supplier"]))
	$_POST["Supplier"] = $_GET["Supplier"];

//----------------------------------------------------------------------------------------------------

function gl_inquiry_controls()
{
    start_form();

    start_table(TABLESTYLE_NOBORDER);
	start_row();
   
		date_cells(_("From:"), 'TransFromDate', '', null, -30);
		date_cells(_("To:"), 'TransToDate');
		hidden('Supplier');
  		submit_cells('Show',_("Show"),'','', 'default');
	
	end_row();
	end_table();

	echo '<hr>';
    end_form();
}

//----------------------------------------------------------------------------------------------------

function show_results()
{
	global $path_to_root, $systypes_array;

	
	//$result = get_gl_transactions($_POST['TransFromDate'], $_POST['TransToDate']);

	$result = get_person_gl_transactions($_POST['TransFromDate'], $_POST['TransToDate'],PT_SUPPLIER,$_POST['Supplier']);

	$colspan = ($dim == 2 ? "6" : ($dim == 1 ? "5" : "4"));

	

	// Only show balances if an account is specified AND we're not filtering by amounts
	
	start_table(TABLESTYLE);
	
	    
	$th = array(_("Type"), _("Type No"), _("Date"),_("GL Account"), _("Debit"), _("Credit"), _("Narration"));
			
	table_header($th);
	
	$begin = get_fiscalyear_begin_for_date($_POST['TransFromDate']);
	if (date1_greater_date2($begin, $_POST['TransFromDate']))
		$begin = $_POST['TransFromDate'];
	$begin = add_days($begin, -1);
	

	$bfw = 0;
	
	$running_total = $bfw;
	$j = 1;
	$k = 0; //row colour counter

	while ($myrow = db_fetch($result))
	{

	    	alt_table_row_color($k);

		    	$running_total += $myrow["amount"];

		    	$trandate = sql2date($myrow["tran_date"]);

		    	label_cell($systypes_array[$myrow["type"]]);
				label_cell(get_gl_view_str($myrow["type"], $myrow["type_no"], $myrow["type_no"], true));
		    	label_cell($trandate);
		    	
			label_cell($myrow["account"] . ' ' . get_gl_account_name($myrow["account"]));
		    	
			display_debit_or_credit_cells($myrow["amount"]);
	
			if ($myrow['memo_'] == "")
				$myrow['memo_'] = get_comments_string($myrow['type'], $myrow['type_no']);
		    	label_cell($myrow['memo_']);
	    	end_row();

	    	$j++;
	    	if ($j == 12)
	    	{
	    		$j = 1;
	    		table_header($th);
	    	}
	}
	//end of while loop

	end_table(2);
	if (db_num_rows($result) == 0)
		display_note(_("No general ledger transactions have been created for the specified criteria."), 0, 1);

}

//----------------------------------------------------------------------------------------------------

gl_inquiry_controls();

div_start('trans_tbl');

if (get_post('Show'))
    show_results();

div_end();

//----------------------------------------------------------------------------------------------------

end_page();

?>

