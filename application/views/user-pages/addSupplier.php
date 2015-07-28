 <?php 
$owner_id=gINVALID;
$own_name='';
$own_address='';
$own_mob='';
$own_mail='';
$own_dob='';
$own_user='';
$own_pas='';
			
if($this->mysession->get('owner_post_all')!=null ){
	$data=$this->mysession->get('owner_post_all');
	$owner_id=$this->mysession->get('owner_id');
	$own_name=$data['name'];
	$own_address=$data['address'];
	$own_mob=$data['mobile'];
	$own_mail=$data['email'];
	$own_dob=$data['dob'];
	$own_user=$data['username'];
	$own_pas=$data['password'];
	$this->mysession->delete('owner_post_all');

}else if(isset($profile)&& $profile!=null){
	$owner_id=$profile['id'];
	$own_name=$profile['name'];
	$own_address=$profile['address'];
	$own_mob=$profile['mobile'];
	$own_mail=$profile['email'];
	$own_dob=$profile['dob'];
	$own_user=$profile['username'];
	$own_pas=$profile['password'];
	$h_pass=$profile['password'];
}
?>
<?php if($this->mysession->get('owner_Success') != '') { ?>
        <div class="success-message">
			
            <div class="alert alert-success alert-dismissable">
                <i class="fa fa-check"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php 
                echo $this->mysession->get('owner_Success');
                $this->mysession->set('owner_Success','');
                ?>
           </div>
       </div>
<?php    } ?>


<div class="page-outer">
<fieldset class="body-border">
<legend class="body-head">Manage Supplier</legend>
	<div class="nav-tabs-custom">
		<ul class="nav nav-tabs">
		<?php 

		foreach($tabs as $tab=>$attr){
		echo '<li class="'.$attr['class'].'">
			<a href="#'.$attr['tab_id'].'" data-toggle="tab">'.$attr['text'].'</a>
		      </li>';
		}
		?>
		</ul>
		

    		<div class="tab-content">
        
        	<?php if (array_key_exists('o_tab', $tabs)) {?>
		<div class="<?php echo $tabs['o_tab']['content_class'];?>" id="<?php echo $tabs['o_tab']['tab_id'];?>">       
		
		
		<fieldset class="body-border-Driver-View border-style-Driver-view" >
		<legend class="body-head">Personal Details</legend>

		<!-----------------------------left block start------------------------------>

		<div class="div-with-50-percent-width-with-margin-10">
		<?php  echo form_open(base_url()."vehicle"); ?>
        			<div class="form-group">
				<?php echo form_label('Name','usernamelabel'); ?>
           			<?php echo form_input(array('name'=>'owner_name','class'=>'form-control','id'=>'total_amt','value'=>$own_name)); ?>
	   			<?php echo $this->form_functions->form_error_session('owner_name', '<p class="text-red">', '</p>'); ?>
        			</div>

				<div class="form-group">
				<?php echo form_label(' Address','usernamelabel'); ?>
				<?php echo form_textarea(array('name'=>'address','class'=>'form-control','id'=>'address','value'=>$own_address,'rows'=>4)); ?>
				<?php echo $this->form_functions->form_error_session('address', '<p class="text-red">', '</p>'); ?>
				</div>

				<div class="form-group">
				<?php echo form_label('Mobile','usernamelabel'); ?>
				<?php echo form_input(array('name'=>'mobile','class'=>'form-control','id'=>'mobile','value'=>$own_mob,'rows'=>4)); ?>
				<?php echo $this->form_functions->form_error_session('mobile', '<p class="text-red">', '</p>'); ?>
				</div><div class="hide-me"><?php echo form_input(array('name'=>'hphone_own','value'=>$own_mob));?>
				</div>

				<div class="form-group">
				<?php echo form_label(' Email','usernamelabel'); ?>
				<?php echo form_input(array('name'=>'mail','class'=>'form-control','id'=>'mail','value'=>$own_mail,'rows'=>4)); ?>
				<?php echo $this->form_functions->form_error_session('mail', '<p class="text-red">', '</p>'); ?>
				</div><div class="hide-me"><?php echo form_input(array('name'=>'hmail_own','value'=>$own_mail));?>
				</div>

				<div class="form-group">
				<?php echo form_label('Date of Birth','usernamelabel'); ?>
				<?php echo form_input(array('name'=>'dob','class'=>'fromdatepicker form-control' ,'value'=>$own_dob)); ?>
				<?php echo $this->form_functions->form_error_session('dob', '<p class="text-red">', '</p>'); ?>
				</div>
				
			</div>
			<!-----------------------------left block ends------------------------------>


			<!-----------------------------right block start------------------------------>
			<div class="div-with-50-percent-width-with-margin-10">
	
				<div class="form-group">
					   <?php echo form_label('Username','usernamelabel');
		
						echo form_input(array('name'=>'username','class'=>'form-control','id'=>'username','placeholder'=>'Enter Username','value'=>$own_user));
					   ?>			
					  <?php echo $this->form_functions->form_error_session('username', '<p class="text-red">', '</p>'); ?>
				<div class="hide-me"><?php echo form_input(array('name'=>'h_user','value'=>$own_user));?></div>		
				</div>
				<div class="form-group">
					   <?php echo form_label('Password','passwordlabel');  ?>
					   <?php echo form_password(array('name'=>'password','class'=>'form-control','id'=>'password','placeholder'=>'Enter Password','value'=>$own_pas)); ?>			
						<?php echo '<p class="text-red">'.$this->mysession->get('v_pwd_err').'</p>'; ?>
						<?php echo $this->form_functions->form_error_session('password', '<p class="text-red">', '</p>'); ?>
				</div>
				<div class="hide-me"><?php echo form_input(array('name'=>'h_pass','value'=>$h_pass)); ?></div>
					 <?php if($owner_id!='' && $owner_id>gINVALID){  echo '';}else{?>
				<div class="form-group">
					   <?php echo form_label('Confirm Password','cpasswordlabel'); ?>
					   <?php echo form_password(array('name'=>'cpassword','class'=>'form-control','id'=>'cpassword','placeholder'=>'Enter Confirm password')); ?>			
						<?php echo $this->form_functions->form_error_session('cpassword', '<p class="text-red">', '</p>'); ?>
				</div>
		
			<?php }?>
			<div class='hide-me'><?php 
				echo form_input(array('name'=>'hidden_owner_id','class'=>'form-control','value'=>$owner_id));?>		</div>

			<div class="box-footer">
				<?php if($owner_id==gINVALID){
					$btn_name='Save';
				 }else {
					$btn_name='Update';
				}
			echo form_submit("owner-add",$btn_name,"class='btn btn-primary'"); ?>
	 	<?php echo form_close(); ?>
		
			</div>
		</div>

		<!-----------------------------right block ends------------------------------>
		</fieldset>
		
	
        	</div>
		<?php }?>

		<?php if (array_key_exists('t_tab', $tabs)) {?>
		<div class="<?php echo $tabs['t_tab']['content_class'];?>" id="<?php echo $tabs['t_tab']['tab_id'];?>">
		<div class="page-outer">
			<fieldset class="body-border">
			<legend class="body-head">Trip</legend>
			<div class="form-group">
			<div class="box-body table-responsive no-padding"> 
				<?php  echo form_open(base_url().'organization/front-desk/supplier-profile/'.$owner_id.'/trip'); ?>
				<table>
				<td><?php echo form_input(array('name'=>'from_pick_date','class'=>'pickupdatepicker initialize-date-picker form-control' ,'placeholder'=>'From Date','value'=>'')); ?></td>
				<td><?php echo form_input(array('name'=>'to_pick_date','class'=>'pickupdatepicker initialize-date-picker form-control' ,'placeholder'=>'To Date','value'=>'')); ?></td>
				<td><?php echo form_submit("vdate_search","Search","class='btn btn-primary'");
				echo form_close();?></td>
				</table><br/>			


				<?php if($TripTableData){?>


					<?php  echo form_open(base_url()."account/driver_trip_save"); ?>

					<table class="table table-hover table-bordered">
					<tbody>
					<?php if(isset($TripTableData['theader'])){?>
					<tr style="background:#CCC">
					<?php foreach($TripTableData['theader'] as $thead){?>
					<th><?=$thead;?></th>			
					<?php }?>
					</tr>
					<?php }?>

					<?php foreach($TripTableData['tdata'] as $tr){?>
					<tr>
					<?php foreach($tr as $td){?>
					<td><?=$td;?></td>			
					<?php }?>
					</tr>
					<?php }?>

					<?php if(isset($TripTableData['tfooter'])){?>
					<tr style="background:#CCC">
					<?php foreach($TripTableData['tfooter'] as $td){?>
					<td><?php echo $td;?></td>			
					<?php }?>
					</tr>
					<?php }?>

					</tbody>
					</table>

					<?php if($TotalTable){?>
					<table class="table table-hover table-bordered">
					<tbody>

					<?php if(isset($TotalTable['theader'])){?>
					<tr style="background:#CCC">
					<?php foreach($TotalTable['theader'] as $thead){?>
					<?=$thead;?>			
					<?php }?>
					</tr>
					<?php }?>


					<?php foreach($TotalTable['tdata'] as $tr){?>
					<tr>
					<?php foreach($tr as $i=>$td){?>
					<td><?php echo ($i=='label')?$td:number_format($td,2);?></td>			
					<?php }?>
					</tr>
					<?php }?>

					<?php if(isset($TotalTable['tfooter'])){?>
					<tr style="background:#CCC">
					<?php foreach($TotalTable['tfooter'] as $td){?>
					<td><?php echo $td;?></td>			
					<?php }?>
					</tr>
					<?php }?>

					</tbody>
					</table>
					<?php }?>

				<?php }else{?><!--Trip table condition-->
					<div class="msg"> No Data</div>
				<?php }?>

			</div>
			</div>
			</fieldset>
		</div>
		</div>
		<?php } ?>


		<?php if (array_key_exists('p_tab', $tabs)) {?>

        	<div class="<?php echo $tabs['p_tab']['content_class'];?>" id="<?php echo $tabs['p_tab']['tab_id'];?>"> 

			<iframe src="<?php echo base_url().'account/front_desk/SupplierPayment/VW'.$owner_id.'/true';?>" height="600px" width="100%">
			<p>Browser not Support</p>
			</iframe>
	
        	</div>
		<?php }?>
		
		<?php if (array_key_exists('a_tab', $tabs)) {?>
		<div class="<?php echo $tabs['a_tab']['content_class'];?>" id="<?php echo $tabs['a_tab']['tab_id'];?>"> 
        		<iframe src="<?php echo base_url().'account/front_desk/DriverPaymentInquiry/VW'.$owner_id.'/true';?>" height="600px" width="100%">
			<p>Browser not Support</p>
			</iframe>
		</div>

		<?php }?>

	</div><!--tab content ends-->

</div><!--nav custom ends-->
</fieldset>
</div>


