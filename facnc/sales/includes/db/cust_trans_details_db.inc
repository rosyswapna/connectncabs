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
//----------------------------------------------------------------------------------------

function get_cnc_trans_details($transno,$type) {

	$sql = "SELECT voucher.voucher_no AS trip_voucher,voucher.trip_id,trip.pick_up_date AS trip_date
		FROM ".TB_PREF."debtor_trans_details dtd,trip_vouchers voucher,trips trip
		WHERE dtd.trip_voucher = voucher.id AND voucher.trip_id=trip.id AND dtd.debtor_trans_no=".db_escape($transno)."
		AND dtd.debtor_trans_type=".db_escape($type)." ORDER BY trip.pick_up_date ASC";

	return db_query($sql, "The debtor transaction detail could not be queried");

	
}



/*
function get_customer_trans_details($debtor_trans_type, $debtor_trans_no,$order_by ='')
{
if (!is_array($debtor_trans_no))
	$debtor_trans_no = array( 0=>$debtor_trans_no );

	$sql = "SELECT ".TB_PREF."debtor_trans_details.*,
		".TB_PREF."debtor_trans_details.unit_price+".TB_PREF."debtor_trans_details.unit_tax AS FullUnitPrice,
		".TB_PREF."debtor_trans_details.description As StockDescription,
		".TB_PREF."stock_master.units, ".TB_PREF."stock_master.mb_flag
		FROM ".TB_PREF."debtor_trans_details, ".TB_PREF."stock_master,trip_vouchers,trips
		
		
		WHERE trip_vouchers.id = ".TB_PREF."debtor_trans_details.trip_voucher 
			AND trip_vouchers.trip_id = trips.id AND (";
//LEFT JOIN trip_vouchers voucher ON voucher.id = ".TB_PREF."debtor_trans_details.trip_voucher
//LEFT JOIN trips trip ON voucher.trip_id = trip.id
	$tr=array();
	foreach ($debtor_trans_no as $trans_no)
		$tr[] = 'debtor_trans_no='.db_escape($trans_no);

	$sql .= implode(' OR ', $tr);


	$sql.=	") AND debtor_trans_type=".db_escape($debtor_trans_type)."
		AND ".TB_PREF."stock_master.stock_id=".TB_PREF."debtor_trans_details.stock_id";

	if($order_by != ''){
		$sql .= " ORDER BY ".$order_by;
	}else{
		$sql .= " ORDER BY ".TB_PREF."debtor_trans_details.id";
	}
	

	return db_query($sql, "The debtor transaction detail could not be queried");
}*/

function get_customer_trans_details($debtor_trans_type, $debtor_trans_no,$order_by ='')
{
if (!is_array($debtor_trans_no))
	$debtor_trans_no = array( 0=>$debtor_trans_no );

	$sql = "SELECT ".TB_PREF."debtor_trans_details.*,
		".TB_PREF."debtor_trans_details.unit_price+".TB_PREF."debtor_trans_details.unit_tax AS FullUnitPrice,
		".TB_PREF."debtor_trans_details.description As StockDescription,
		".TB_PREF."stock_master.units, ".TB_PREF."stock_master.mb_flag
		FROM  ".TB_PREF."stock_master,".TB_PREF."debtor_trans_details
		
		LEFT JOIN trip_vouchers ON trip_vouchers.id = ".TB_PREF."debtor_trans_details.trip_voucher
		LEFT JOIN trips ON trip_vouchers.trip_id = trips.id
		
		";

	$tr=array();
	foreach ($debtor_trans_no as $trans_no)
		$tr[] = 'debtor_trans_no='.db_escape($trans_no);

	$sql .=" WHERE (".implode(' OR ', $tr);


	$sql.=	") AND debtor_trans_type=".db_escape($debtor_trans_type)."
		AND ".TB_PREF."stock_master.stock_id=".TB_PREF."debtor_trans_details.stock_id";

	if($order_by != ''){
		$sql .= " ORDER BY ".$order_by;
	}else{
		$sql .= " ORDER BY ".TB_PREF."debtor_trans_details.id";
	}
	

	return db_query($sql, "The debtor transaction detail could not be queried");
}

//----------------------------------------------------------------------------------------

function void_customer_trans_details($type, $type_no)
{
	$sql = "UPDATE ".TB_PREF."debtor_trans_details SET quantity=0, unit_price=0,
		unit_tax=0, discount_percent=0, standard_cost=0, src_id=0
		WHERE debtor_trans_no=".db_escape($type_no)."
		AND debtor_trans_type=".db_escape($type);

	db_query($sql, "The debtor transaction details could not be voided");

	// clear the stock move items
	void_stock_move($type, $type_no);
}
//----------------------------------------------------------------------------------------


function release_delivery($id){
	$sql = "UPDATE ".TB_PREF."debtor_trans_details SET qty_done= 0 WHERE id=".db_escape($id);
	db_query($sql, "The debtor transaction detail could not be written");
}

function write_customer_trans_detail_item($debtor_trans_type, $debtor_trans_no, $stock_id, $description,
	$quantity, $unit_price, $unit_tax, $discount_percent, $std_cost, $src_id, $line_id=0,$trip_voucher=0,$unit_taxable_amount)
{

	
	if ($line_id!=0)
		$sql = "UPDATE ".TB_PREF."debtor_trans_details SET
			stock_id=".db_escape($stock_id).",
			description=".db_escape($description).",
			quantity=$quantity,
			unit_price=$unit_price,
			unit_tax=$unit_tax,
			discount_percent=$discount_percent,
			standard_cost=$std_cost,
			trip_voucher=$trip_voucher,
			src_id=".db_escape($src_id)." WHERE
			id=".db_escape($line_id);
	else
			$sql = "INSERT INTO ".TB_PREF."debtor_trans_details (debtor_trans_no,
				debtor_trans_type, stock_id, description, quantity, unit_price,
				unit_tax, discount_percent, standard_cost, src_id,trip_voucher,unit_taxable_amount)
			VALUES (".db_escape($debtor_trans_no).", ".db_escape($debtor_trans_type).", ".db_escape($stock_id).
			", ".db_escape($description).",
				$quantity, $unit_price, $unit_tax, 
				$discount_percent, $std_cost,".db_escape($src_id).",".db_escape($trip_voucher).",$unit_taxable_amount)";

	//echo $sql;exit;

	db_query($sql, "The debtor transaction detail could not be written");
}

?>
