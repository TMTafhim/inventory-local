<div class="col-xl-12 header-area">
	<p class="print-company-name" style="color:#4169e1;text-align:center;font-size:35px;margin:0;font-weight:bold;"><?php echo $organization_name; ?></p>
	<p class="print-company-address" style="margin:0;text-align:center;"><?php echo $organization_address; ?></p>
	<!-- <p class="print-company-contact"><?php echo $organization_contact_address; ?></p> -->
	<p class="print-document-title" style="font-size:22px;text-align:center;"><u><?php $step_1=str_replace("_"," ",$page_title);
	echo str_replace("Requestion","Requisition",$step_1);
	?></u></p>
</div>
