<div id="body-wrap">
	<?php
	if($this->session->userdata('isLoggedIn')==true ){
		switch($this->session->userdata('type')){
			case SYSTEM_ADMINISTRATOR:$dashboard_url=base_url().'admin';break;
			case ORGANISATION_ADMINISTRATOR:$dashboard_url=base_url().'organization/admin';break;
			case FRONT_DESK:$dashboard_url=base_url().'organization/front-desk';break;
			case CUSTOMER:$dashboard_url=base_url().'customer/home';break;
			case DRIVER:$dashboard_url=base_url().'driver/home';break;
			case VEHICLE_OWNER:$dashboard_url=base_url().'vehicle/home';break;
			default:$dashboard_url=base_url();
		}
	}
	?>
	<section class="content">
	 
	    <div class="error-page">
		<h2 class="headline text-info"> 404</h2>
		<div class="error-content">
		    <h3><i class="fa fa-warning text-yellow"></i> Oops! Page not found.</h3>
		    <p>
		        We could not find the page you were looking for. 
		        Meanwhile, you may <a href="<?php echo $dashboard_url; ?>">return to dashboard</a> 
		    </p>
		   
		</div><!-- /.error-content -->
	    </div><!-- /.error-page -->

	</section><!-- /.content -->
</div>
