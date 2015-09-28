<?php    if($this->session->userdata('dbSuccess') != '') { 
?>

        <div class="success-message">
            <div class="alert alert-success alert-dismissable">
                <i class="fa fa-check"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php 
                echo $this->session->userdata('dbSuccess');
                $this->session->set_userdata(array('dbSuccess'=>''));
                ?>
           </div>
       </div>
       <?php    } ?>
	  <?php 
	  //search?>

<div class="page-outer" id= "supplier-list-div">    
	<fieldset class="body-border">
		<legend class="body-head">List Supplier</legend>
		<div class="box-body table-responsive no-padding">
			<?php echo form_open(base_url().'organization/front-desk/list-supplier');?>
			<table class="table list-org-table">
				<tbody> 
				<tr>
					<td><?php echo form_input(array('name'=>'supplier_name','class'=>'form-control','id'=>'supplier_name','placeholder'=>'By Name','size'=>30,'value'=>$supplier_name));?>
					<ul class="auto-fill autofill-supplier-name"></ul> </td>
					<td><?php echo form_input(array('name'=>'supplier_mobile','class'=>'form-control','id'=>'supplier_mobile','placeholder'=>'By Mobile','size'=>30,'value'=>$supplier_mobile));?> </td>
					<td><?php echo form_submit("search","Search","class='btn btn-primary'");?></td>
			<?php echo form_close();?>
						<td><?php echo nbs(55); ?></td>
						<td><?php echo nbs(35); ?></td>
						
						<td><?php echo form_open( base_url().'organization/front-desk/supplier-profile');
								  echo form_submit("add","Add","class='btn btn-primary'");
								  echo form_close(); ?></td>
						<td><?php //echo form_button('print-supplier','Print',"class='btn btn-primary print-supplier'"); ?></td>
					</tr>
				</tbody>
			</table>
		</div>
		
		<div class="msg"> <?php 
			if (isset($result)){ echo $result;} else {?></div>
	
		
		<div class="box-body table-responsive no-padding driver-list-div">
			<table class="table table-hover table-bordered table-with-20-percent-td">
				<tbody>
				<tr>
					<th>Supplier</th>
					<th>Contact Details</th>
					<th>Vehicle Details</th>
					
				</tr>
				<?php 
				$attributes=array('class'=>'label-font-style');
				if(isset($suppliers)){ 
					foreach ($suppliers as $supplier):
					$phone_numbers='';
				?>
				<tr>
					<td><?php echo anchor(base_url().'organization/front-desk/supplier-profile/'.$supplier['id'],$supplier['name']).nbs(3);?>
					</td>

					<td><?php
						if($supplier['mobile']!=''){echo $supplier['mobile'].br();}
						if($supplier['email']!=''){echo $supplier['email'].br();}
					        if($supplier['address']!=''){echo $supplier['address'].br();}
						?>
					</td>	

					<td>
					<?php
					$vh = explode(',',$supplier['vehicle_details']);
					foreach($vh as $v){
						echo $v.br();
					}
					?>
					</td>
					
				</tr>
				<?php endforeach;
				}
				?>
				</tbody>
			</table><?php echo $page_links;?>
		</div>
		<?php } ?>
	</fieldset>
</div>

