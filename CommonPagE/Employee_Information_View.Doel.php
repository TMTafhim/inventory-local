<style>

@media all {
    .page-break { display: none; }
}

@media print {
    .page-break { display: block; page-break-before: always; }
}

.header-area{
	padding: 0px 10px;	
	color: #112233;
	text-align: center;
	border-bottom: 1px solid #5499C7;
	
}
.header-area .logo-image{
	height: 100px;
	width: 100px;
	float: left;
}
.header-area .student-image{
	height: 100px;
	width: 100px;
	float: right;
	background-image: url(../images/profile.png);
}
.header-area h1{
	margin: 0px;
	font-size: 28px;
}


 .my-info-area{
	-moz-box-shadow:    inset 0 0 10px  #9CCAF4;
	-webkit-box-shadow: inset 0 0 10px #9CCAF4;
	box-shadow:         inset 0 0 10px #9CCAF4; 
	border: 1px solid #5499C7;
 }
 
 
 
.details-div-area{
	padding: 15px 20px;
	//border-bottom: 0.5px solid #5499C7;
	font-style: italic;
}
.details-div-area tr{
	height: 30px;
}
.data-row{
	border-bottom: 1px dotted #5499C7;
}
.present-address{
	padding-left: 20px;
	vertical-align: top;
}
.verify-div .signature-1{
	border-top: 1px dotted #000;
	padding: 3px;	
	float: left;
}
.verify-div .signature-2{
	border-top: 1px dotted #000;
	padding: 3px;	
	float: right;
}	 



/*----------------------------Title Area--------------------------------------*/
.title-name {
	text-align: center;
	margin-left: auto;
	margin-right: auto;
	margin-top: 10px;
	border: 0.5px solid #5499C7;
	width: 350px;
	padding: 5px;
	//border-radius: 10px;	
	color: #112233;	
	font-size: 15px;
}
/*----------------------------Information Area--------------------------------------*/
.info-div-area{
	//border-bottom: 0.5px solid #5499C7;	
	font-style: italic;
	padding: 15px 18px;
}
.info-div-area table{
	color: #112233;
	font-size: 15px;
}

.info-div-area td{
	border: 0.5px solid #fff;
	padding: 5px 0px;
}
.info-div-area tr{
	background-color: #EAECEE;
	height: 25px;
}
.info-div-area tr:nth-child(even){
	background-color: #F7F7F7;
}
.table-div-area th{
	padding: 5px 0px;
	border: 0.5px solid #AED6F1;
	font-size: 13px;
	font-style: italic;
	text-align: center;
}


.table-div-area td{
	padding: 5px 0px;
	border: 0.5px solid #AED6F1;
	font-size: 14px;
	font-style: italic;
}


/*----------------------------Verify / Signature Area--------------------------------------*/
.verify-div{
	border-bottom: 0.5px solid #5499C7;
	padding: 100px 60px 20px 60px;
	font-size: 14px;
	font-style: italic;
}
.leave_info_table{
	text-align: center;
}
.leave_info_table th{
	font-weight: normal;
	background: #F7FCFF;
}
</style>

<section class="content ">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
     <script type="text/javascript">
  function PrintRedirect(){
      var printpagebutton=document.getElementById("printpagebutton").value;
      var dataString = 'printpagebutton='+printpagebutton+'&redirect_page_title=Employee_Information/HR';
    $.ajax
      ({
      type: "POST",
      url: "Ajax_PrintValidation.php",
      data: dataString,
      cache: false,
      success: function(html)
      {
        $("#PrintPageRedirection").html(html);  
          
      } 
      });
    } 

</script> 
<style>
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
		
}
</style>
<style>
table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  
}

th, td {
  text-align: left;
  padding: 8px;
  border: 1px solid #ddd;
}

tr:nth-child(even){background-color: #f2f2f2}
</style>
		 
<?php
$PatientInformation = $pdo->query(" SELECT employee_information.*,hr_department.name AS department,hr_designation.name AS designation FROM employee_information inner join hr_designation ON employee_information.designation=hr_designation.id INNER JOIN hr_department ON employee_information.department=hr_department.id WHERE  employee_information.id='$DocumentData'");
$RowdataPatientInformation = $PatientInformation->fetch();

$InformationRoster = $pdo->query("SELECT * FROM `hr_rostering_information` WHERE employee_id='".$RowdataPatientInformation["id"]."' and  DELETED_AT is NULL");
$rowDataInformationRoster= $InformationRoster->fetch();

?>

<section class="content ">
      <div class="container-fluid">
        <!-- SELECT2 EXAMPLE -->
        
        <!-- /.card -->

        <!-- SELECT2 EXAMPLE -->
        <div class="card card-default">
          <div class="card-header no-print">
            <h3 class="card-title">

              <p id="PrintPageRedirection"></p>
              <button id="printpagebutton" type="button" autofocus class="btn btn-success btn-xl" style="margin-left:5px; color:#FFF;" onclick="PrintRedirect();window.print();return false;" />
                        <i class="fa fa-print"></i>
                       Print
                    </button> </h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
			  
			<div class="row">
			  <div class="col-sm-12">
				<div class="row" >  
				
					
		 <div class="col-md-12">
		     
		     
             <div class="row" > 
            
            <div class="col-md-12">  
            <div class=" mt-3 pb-3 mb-3 d-flex">
        <div class="image" style="padding-right: 10px;margin-top:5px;">
          <img src="image/Doel e-Services.png" class=" elevation-2" alt="User Image" style="width:80px">
        </div>
        <div class="info">
          <p class="d-block" style="font-size:23px;font-weight:bold;margin:0px;"><?php echo $organization_name; ?></p>
          <p style="font-size:18px;margin:0px;"><?php echo $organization_address; ?></p>
          <p style="font-size:18px;margin:0px;"><?php echo $organization_contact_address; ?></p>
        </div>
      </div>
           </div> 
           
           <div class="col-md-12 title-div" style="margin:10px 0px;">		
								<div class="title-name" style="font-size:30px;"><strong><?php echo $RowdataPatientInformation["name_bn"]; ?></strong></div>						
							</div>
							
           
             </div>
              </div>
					
			<div class="col-md-10">
		     
		     
             <div class="row" > 		
					
               <div class="col-md-2">
                <div class="form-group">
					<p style="margin:0px;"><b>Office ID</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['office_id']; ?></p>
				   </div>
             
              </div>
					
			  <div class="col-md-2">
                <div class="form-group">
					<p style="margin:0px;"><b>NID</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['nid']; ?></p>
				   </div>
             
              </div>		
            
			 <div class="col-md-4">
                <div class="form-group">
					<p style="margin:0px;"><b>Name (Bangla)</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['name_bn']; ?></p>
				   </div>
             
              </div>
			<div class="col-md-4">
                <div class="form-group">
					<p style="margin:0px;"><b>Name (English)</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['name_en']; ?></p>
				   </div>
             
              </div>	 
				 
			   </div>   
					
					</div>
					<div class="col-md-2">
						
					<?php if(!empty($RowdataPatientInformation["photo"])){ ?> <img src="HRPhoto/<?php echo $RowdataPatientInformation["photo"]; ?> " style="height: 80px;width: 100px;" ><?php } ?> 
					</div>		
					
			<div class="col-md-12">
		     
		     
             <div class="row" > 		
					
               <div class="col-md-6">
                <div class="form-group">
					<p style="margin:0px;"><b>Father Name</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['father_name']; ?></p>
				   </div>
             
              </div>
				
			  <div class="col-md-6">
                <div class="form-group">
					<p style="margin:0px;"><b>Mother Name</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['mother_name']; ?></p>
				   </div>
             
              </div>
			<div class="col-md-4">
                <div class="form-group">
					<p style="margin:0px;"><b>Mobile No</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['mobile']; ?></p>
				   </div>
             
              </div>
		<div class="col-md-4">
                <div class="form-group">
					<p style="margin:0px;"><b>Mobile No(Secondary)</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['mobile_secondary']; ?></p>
				   </div>
             
              </div>
				 
		<div class="col-md-4">
                <div class="form-group">
					<p style="margin:0px;"><b>Email Address</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['email']; ?></p>
				   </div>
             
              </div>
		     <div class="col-md-3">
                <div class="form-group">
					<p style="margin:0px;"><b>Date of Joining</b>&nbsp;:&nbsp;<?php if(!empty($RowdataPatientInformation['date_of_joining'])){ echo date("d/m/Y", strtotime($RowdataPatientInformation['date_of_joining']));} ?></p>
				   </div>
             
              </div>
			<div class="col-md-3">
                <div class="form-group">
					<p style="margin:0px;"><b>Department</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['department']; ?></p>
				   </div>
             
              </div>
			<div class="col-md-3">
                <div class="form-group">
					<p style="margin:0px;"><b>Designation</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['designation']; ?></p>
				   </div>
             
              </div>
			<div class="col-md-3">
                <div class="form-group">
					<p style="margin:0px;"><b>Date of Birth</b>&nbsp;:&nbsp;<?php if(!empty($RowdataPatientInformation['date_of_birth'])){ echo date("d/m/Y", strtotime($RowdataPatientInformation['date_of_birth'])); } ?></p>
				   </div>
             
              </div>	 
			<div class="col-md-3">
                <div class="form-group">
					<p style="margin:0px;"><b>Blood Group</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['blood_group']; ?></p>
				   </div>
             
              </div>
			<div class="col-md-3">
                <div class="form-group">
					<p style="margin:0px;"><b>Gender</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['gender']; ?></p>
				   </div>
             
              </div>
			<div class="col-md-3">
                <div class="form-group">
					<p style="margin:0px;"><b>Religion</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['religion']; ?></p>
				   </div>
             
              </div>
			<div class="col-md-3">
                <div class="form-group">
					<p style="margin:0px;"><b>Maritial Status</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['marital_status']; ?></p>
				   </div>
             
              </div>	 
				 
				 
				 
				 
				 
				 
				 
            
			 <div class="col-md-4">
                <div class="form-group">
					<p style="margin:0px;"><b>Last Education</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['last_education']; ?></p>
				   </div>
             
              </div>
				 <div class="col-md-3">
                <div class="form-group">
					<p style="margin:0px;"><b>Day Off</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['off_days']; ?></p>
				   </div>
             
              </div>
			 <div class="col-md-6">
                <div class="form-group">
					<p style="margin:0px;"><b>Present Address</b>&nbsp;:&nbsp;<?php echo nl2br($RowdataPatientInformation['present_address']); ?></p>
				   </div>
             
              </div>
			<div class="col-md-6">
                <div class="form-group">
					<p style="margin:0px;"><b>Permanent Address</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['village']; if(!empty($RowdataPatientInformation['post_office'])){ echo ", ".$RowdataPatientInformation['post_office'];}if(!empty($RowdataPatientInformation['thana'])){ echo ", ".$RowdataPatientInformation['thana'];}if(!empty($RowdataPatientInformation['district'])){ echo ", ".$RowdataPatientInformation['district'];}if(!empty($RowdataPatientInformation['division'])){ echo ", ".$RowdataPatientInformation['division'];} ?></p>
				   </div>
             
              </div>
			<div class="col-md-12">
				<p style="font-size: 18px;font-weight: bold;">Emergency Contact</p>
				</div>	 
				 
			<div class="col-md-6">
                <div class="form-group">
					<p style="margin:0px;"><b>Name</b>&nbsp;:&nbsp;<?php echo nl2br($RowdataPatientInformation['emergency_name']); ?></p>
				   </div>
             
              </div>
				 
			<div class="col-md-6">
                <div class="form-group">
					<p style="margin:0px;"><b>Mobile No</b>&nbsp;:&nbsp;<?php echo $RowdataPatientInformation['emergency_mobile']; ?></p>
				   </div>
             
              </div>	<?php if(!empty($RowdataPatientInformation["project_name"])){
	$InformationProject = $pdo->query("SELECT * FROM `project_information` WHERE id='".$RowdataPatientInformation["project_name"]."' ");
$rowDataInformationProject= $InformationProject->fetch();
				
				   ?>
			<div class="col-md-8">
						
			<div class="form-group">
					<p style="margin:0px;"><b>Project Name</b>&nbsp;:&nbsp;<?php echo $rowDataInformationProject['name']; ?></p>
				   </div>
					</div>	 
				 <?php } ?> 
			 
		 	<div class="col-md-4 d-print-none">
						
					<?php if(!empty($RowdataPatientInformation["hr_cv"])){ ?><p style="margin:0px;"><b>CV Download</b>&nbsp;:&nbsp;</p> <a class="btn btn-success" href="HRCV/<?php echo $RowdataPatientInformation["hr_cv"]; ?> " >Download</a><?php } ?> 
					</div>	
				
			<style>
table {
  border-collapse: collapse;
  border-spacing: 0;
  width: 100%;
  border: 1px solid #ddd;
}

th, td {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2}
</style>	 
			<div class="col-md-12">
                <div class="form-group">
					<p style="margin:0px;"><b>Rostering Information</b></p>
				   </div>
				   
			<div style="overflow-x:auto;">
  <table>
    <tr>
    <?php if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Saturday'){ ?>
      <th>Saturday</th>
      <?php } if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Sunday'){ ?>
      <th>Sunday</th>
      <?php } if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Monday'){ ?>
      <th>Monday</th>
      <?php } if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Tuesday'){ ?>
      <th>Tuesday</th>
      <?php } if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Wednesday'){ ?>
      <th>Wednesday</th>
      <?php } if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Thursday'){ ?>
      <th>Thursday</th>
      <?php } if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Friday'){ ?>
      <th>Friday</th>
      <?php } ?>
    </tr>
    <tr>
    <?php if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Saturday'){ ?>    
      <td><?php echo $rowDataInformationRoster["saturday"]; ?></td>
      <?php } if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Sunday'){ ?>
      <td><?php echo $rowDataInformationRoster["sunday"]; ?></td>
      <?php } if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Monday'){ ?>
      <td><?php echo $rowDataInformationRoster["monday"]; ?></td>
      <?php } if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Tuesday'){ ?>
      <td><?php echo $rowDataInformationRoster["tuesday"]; ?></td>
      <?php } if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Wednesday'){ ?>
      <td><?php echo $rowDataInformationRoster["wednesday"]; ?></td>
      <?php } if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Thursday'){ ?>
      <td><?php echo $rowDataInformationRoster["thursday"]; ?></td>
      <?php } if(!empty($RowdataPatientInformation['off_days']) && $RowdataPatientInformation['off_days']!='Friday'){ ?>
      <td><?php echo $rowDataInformationRoster["friday"]; ?></td>
      
      <?php } ?>
    </tr>
    
  </table>
</div>	   
<div class="form-group">
					<p style="margin-top:10px;"><b>Leave Information</b></p>
				   </div>
<div style="overflow-x:auto;">
  <table>
    <tr>
      <th>SL</th>
      <th>Leave</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th>Number of Days</th>
      
    </tr>
    
    	 <?php
	$current_year=date("Y");		
	$Information_Leave = $pdo->query("SELECT * FROM `hr_leave_information` WHERE employee_id='".$RowdataPatientInformation["id"]."' and current_year='$current_year' and  DELETED_AT is NULL order by id DESC ");
				  $Leave_sl=1;
	 while($rowDataInformation_Leave= $Information_Leave->fetch()){
?>
    <tr>
      <td><?php echo $Leave_sl; ?></td>
      <td><?php echo $rowDataInformation_Leave["leave_type"]; ?></td>
      <td><?php echo date('d/m/Y', strtotime($rowDataInformation_Leave["start_date"])); ?></td>
      <td><?php echo date('d/m/Y', strtotime($rowDataInformation_Leave["end_date"])); ?></td>
      <td><?php echo $rowDataInformation_Leave["leave_days"]; ?></td>
                    
              
    </tr>
  <?php 
  
 $Leave_sl++; 
  } ?> 
  </table>				   
				   
				   
				   
				   
				   
				   
             
              </div>	 
			 	 
			
			   </div>   </div>	
					
			
				
	
					
				
					
					
			
							
								
					
					
					
					
					
					
					
			  	
				  
				  		  
				  
				  
				  
				  
				  </div>
		
				
			  
			  
			  </div>  
			  
   </div>
			  
	<!--Second Page Start-->
			  
    <!--Second Page End-->			  
			  
	<!--Third Page Start-->
			  
    <!--Third Page End-->				  
			  
			  
	<!--Forth Page Start-->
			  
	<!--Forth Page End-->		  
			  
		
			  
			  
			  
   </div> 
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
         
        </div>
        <!-- /.card -->

        <!-- /.card -->

        
        <!-- /.row -->
        
        <!-- /.row -->
        
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>

