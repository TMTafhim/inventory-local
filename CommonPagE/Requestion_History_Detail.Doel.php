<?php
require_once("BDB/Auth.php");
if(!empty($MenuName)){
	$Detail=$MenuName;

$information = $pdo->query("SELECT employee_information.name_en AS name,project_information.name AS project_name,note,date,invoice_id,project_coordinator,project_coordinator_note,project_coordinator_time,project_director,project_director_note,project_director_time,managing_director,managing_director_note,managing_director_time,project_id,requestion_histiory.store_id AS store_id,approval_status,requistion_type,previous_cash_in_hand,project_serial_no,approval_path_name_id FROM `requestion_histiory` INNER JOIN employee_information ON requestion_histiory.employee_id=employee_information.id INNER JOIN project_information ON requestion_histiory.project_id=project_information.id where requestion_histiory.invoice_id='$Detail' and requestion_histiory.deleted_at is NULL");
$rowdata = $information->fetch();

$requisitionPurchaseStatement = $pdo->prepare(
	"SELECT invoice_id, purchase_id, date
	 FROM purchase_history
	 WHERE requisition_invoice_id = :requisition_invoice_id
	   AND deleted_at IS NULL
	 ORDER BY id DESC"
);
$requisitionPurchaseStatement->execute(array(':requisition_invoice_id' => $Detail));
$requisitionPurchaseOrders = $requisitionPurchaseStatement->fetchAll();

	$informationApproval_check = $pdo->prepare("SELECT * FROM project_material_aproval_status WHERE invoice_id=:invoice_id AND employee_id=:employee_id AND approval_status='Pending' AND deleted_at IS NULL ORDER BY id DESC LIMIT 1");
	$informationApproval_check->execute(array(':invoice_id'=>$Detail,':employee_id'=>$LoginReGiSterSession));
	$rowdataApproval_check = $informationApproval_check->fetch();

$requestion_super_admin_can_edit=authCanEditRequisitionHistory($LoginReGiSterSession);

function requestionHistoryHtml($value){
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function requestionApprovalActionLabel($action){
	$action=(string)$action;
	if($action==='final_approve'){
		return 'Final Approve';
	}
	return ucwords(str_replace('_',' ',$action));
}

$requestion_projects=array();
$Project_info_edit = $pdo->query("SELECT id,name FROM project_information WHERE deleted_at IS NULL ORDER BY name ASC");
while($row_project_edit=$Project_info_edit->fetch()){
	$requestion_projects[]=$row_project_edit;
}

$requestion_stores=array();
$Store_info_edit = $pdo->query("SELECT id,name FROM store_information WHERE deleted_at IS NULL ORDER BY name ASC");
while($row_store_edit=$Store_info_edit->fetch()){
	$requestion_stores[]=$row_store_edit;
}

$requestion_products=array();
$Product_info_edit = $pdo->query("SELECT id,name,unit,code FROM product_information WHERE deleted_at IS NULL ORDER BY name ASC");
while($row_product_edit=$Product_info_edit->fetch()){
	$requestion_products[]=$row_product_edit;
}

$product_options_html='<option value="">Select Product</option>';
foreach($requestion_products as $product_option){
	$product_options_html.='<option value="'.requestionHistoryHtml($product_option["id"]).'" data-unit="'.requestionHistoryHtml($product_option["unit"]).'">'.requestionHistoryHtml($product_option["name"]).(!empty($product_option["code"]) ? ' - '.requestionHistoryHtml($product_option["code"]) : '').'</option>';
}

$approvalFlowRows=array();
$approvalFlowTableCheck=$pdo->query("SHOW TABLES LIKE 'project_material_approval_flow'");
if($approvalFlowTableCheck->fetch()){
	$approvalFlowStatement=$pdo->prepare("SELECT approval_flow.*,from_employee.name_en AS from_employee_name,to_employee.name_en AS to_employee_name
		FROM project_material_approval_flow approval_flow
		LEFT JOIN employee_information from_employee ON approval_flow.from_employee_id=from_employee.id
		LEFT JOIN employee_information to_employee ON approval_flow.to_employee_id=to_employee.id
		WHERE approval_flow.invoice_id=:invoice_id AND approval_flow.project_id=:project_id
		ORDER BY approval_flow.id ASC");
	$approvalFlowStatement->execute(array(':invoice_id'=>$rowdata["invoice_id"],':project_id'=>$rowdata["project_id"]));
	$approvalFlowRows=$approvalFlowStatement->fetchAll();
}
if(empty($approvalFlowRows)){
	$approvalFlowStatement=$pdo->prepare("SELECT approval_status_rows.*,employee_information.name_en AS to_employee_name,assign_employee.name_en AS from_employee_name
		FROM project_material_aproval_status approval_status_rows
		LEFT JOIN employee_information ON approval_status_rows.employee_id=employee_information.id
		LEFT JOIN employee_information assign_employee ON approval_status_rows.assign_employee_id=assign_employee.id
		WHERE approval_status_rows.invoice_id=:invoice_id AND approval_status_rows.project_id=:project_id AND approval_status_rows.deleted_at IS NULL
		ORDER BY approval_status_rows.id ASC");
	$approvalFlowStatement->execute(array(':invoice_id'=>$rowdata["invoice_id"],':project_id'=>$rowdata["project_id"]));
	$approvalFlowRows=$approvalFlowStatement->fetchAll();
}

$approvalNoteRows=array();
foreach($approvalFlowRows as $approvalFlowRow){
	if(empty($approvalFlowRow["note"])){
		continue;
	}
	$flowAction=!empty($approvalFlowRow["action"]) ? $approvalFlowRow["action"] : (!empty($approvalFlowRow["approval_status"]) ? $approvalFlowRow["approval_status"] : 'Assigned');
	if(array_key_exists("action",$approvalFlowRow)){
		$fromName=!empty($approvalFlowRow["from_employee_name"]) ? $approvalFlowRow["from_employee_name"] : 'System';
		$toName=!empty($approvalFlowRow["to_employee_name"]) ? $approvalFlowRow["to_employee_name"] : '';
	}else{
		$fromName=!empty($approvalFlowRow["to_employee_name"]) ? $approvalFlowRow["to_employee_name"] : 'System';
		$toName='';
	}
	$flowTime=!empty($approvalFlowRow["created_at"]) ? $approvalFlowRow["created_at"] : (!empty($approvalFlowRow["updated_at"]) ? $approvalFlowRow["updated_at"] : (!empty($approvalFlowRow["asign_time"]) ? $approvalFlowRow["asign_time"] : ''));
	$approvalNoteRows[]=array(
		'action'=>requestionApprovalActionLabel($flowAction),
		'from_name'=>$fromName,
		'to_name'=>$toName,
		'time'=>$flowTime,
		'note'=>$approvalFlowRow["note"]
	);
}

$statusNoteStatement=$pdo->prepare("SELECT approval_status_rows.*,employee_information.name_en AS employee_name,assign_employee.name_en AS assign_employee_name
	FROM project_material_aproval_status approval_status_rows
	LEFT JOIN employee_information ON approval_status_rows.employee_id=employee_information.id
	LEFT JOIN employee_information assign_employee ON approval_status_rows.assign_employee_id=assign_employee.id
	WHERE approval_status_rows.invoice_id=:invoice_id
	  AND approval_status_rows.project_id=:project_id
	  AND approval_status_rows.note IS NOT NULL
	  AND TRIM(approval_status_rows.note)<>''
	  AND approval_status_rows.deleted_at IS NULL
	ORDER BY approval_status_rows.id ASC");
$statusNoteStatement->execute(array(':invoice_id'=>$rowdata["invoice_id"],':project_id'=>$rowdata["project_id"]));
while($statusNoteRow=$statusNoteStatement->fetch()){
	$exists=false;
	foreach($approvalNoteRows as $approvalNoteRow){
		if($approvalNoteRow["note"]===$statusNoteRow["note"] && $approvalNoteRow["from_name"]===$statusNoteRow["employee_name"]){
			$exists=true;
			break;
		}
	}
	if($exists){
		continue;
	}
	$approvalNoteRows[]=array(
		'action'=>requestionApprovalActionLabel(!empty($statusNoteRow["approval_status"]) ? $statusNoteRow["approval_status"] : 'Note'),
		'from_name'=>!empty($statusNoteRow["employee_name"]) ? $statusNoteRow["employee_name"] : 'System',
		'to_name'=>'',
		'time'=>!empty($statusNoteRow["updated_at"]) ? $statusNoteRow["updated_at"] : (!empty($statusNoteRow["approval_time"]) ? $statusNoteRow["approval_time"] : $statusNoteRow["asign_time"]),
		'note'=>$statusNoteRow["note"]
	);
}

function requestionRateProofFilesFromValue($value){
	$value=trim((string)$value);
	if($value===''){
		return array();
	}
	$decoded=json_decode($value,true);
	if(is_array($decoded)){
		return array_values(array_filter($decoded));
	}
	return array($value);
}

function requestionRateProofUrl($fileName){
	return 'RequistionAttachment/'.rawurlencode($fileName);
}

function requestionRateProofPath($fileName){
	return dirname(__DIR__).'/RequistionAttachment/'.basename($fileName);
}

function requestionRateProofExists($fileName){
	return is_file(requestionRateProofPath($fileName));
}

function requestionRateProofType($fileName){
	$extension=strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
	if(in_array($extension,array('jpg','jpeg','png','gif','webp'),true)){
		return 'image';
	}
	if($extension==='pdf'){
		return 'pdf';
	}
	return 'file';
}

$rateProofRows=array();
$rateProofFiles=array();
$rateProofColumnCheck=$pdo->query("SHOW COLUMNS FROM requestion_approval_detail LIKE 'rate_attachment'");
if($rateProofColumnCheck->fetch()){
	$rateProofStatement=$pdo->prepare("SELECT approval_detail.*,employee_information.name_en AS approver_name
		FROM requestion_approval_detail approval_detail
		LEFT JOIN employee_information ON approval_detail.employee_id=employee_information.id
		WHERE approval_detail.invoice_id=:invoice_id
		  AND approval_detail.project_id=:project_id
		  AND approval_detail.rate_attachment IS NOT NULL
		  AND TRIM(approval_detail.rate_attachment)<>''
		  AND approval_detail.deleted_at IS NULL
		ORDER BY approval_detail.id ASC");
	$rateProofStatement->execute(array(':invoice_id'=>$rowdata["invoice_id"],':project_id'=>$rowdata["project_id"]));
	$rateProofRows=$rateProofStatement->fetchAll();
	foreach($rateProofRows as $rateProofRow){
		foreach(requestionRateProofFilesFromValue($rateProofRow["rate_attachment"]) as $rateProofFileName){
			if(isset($rateProofFiles[$rateProofFileName])){
				continue;
			}
			$rateProofFiles[$rateProofFileName]=array(
				'file_name'=>$rateProofFileName,
				'approver_name'=>!empty($rateProofRow["approver_name"]) ? $rateProofRow["approver_name"] : '',
				'created_at'=>!empty($rateProofRow["created_at"]) ? $rateProofRow["created_at"] : ''
			);
		}
	}
	$rateProofFiles=array_values($rateProofFiles);
}

$currentPendingApprovalRow=array();
$currentPendingApprovalStatement=$pdo->prepare("SELECT * FROM project_material_aproval_status WHERE invoice_id=:invoice_id AND project_id=:project_id AND employee_id=:employee_id AND approval_status='Pending' AND deleted_at IS NULL ORDER BY id DESC LIMIT 1");
$currentPendingApprovalStatement->execute(array(':invoice_id'=>$rowdata["invoice_id"],':project_id'=>$rowdata["project_id"],':employee_id'=>$LoginReGiSterSession));
$currentPendingApprovalRow=$currentPendingApprovalStatement->fetch();
$currentApprovalPathNameId=!empty($currentPendingApprovalRow["approval_path_name_id"]) ? $currentPendingApprovalRow["approval_path_name_id"] : (!empty($rowdata["approval_path_name_id"]) ? $rowdata["approval_path_name_id"] : 0);
$requestionFullApprovalPathRows=array();
$requestionFullApprovalPathName='';
if(authIsSuperAdmin($LoginReGiSterSession) && !empty($currentApprovalPathNameId) && function_exists('requestionApprovalPathSteps')){
	$pathNameStatement=$pdo->prepare("SELECT approval_path_name FROM project_approval_path_name WHERE id=:id AND deleted_at IS NULL LIMIT 1");
	$pathNameStatement->execute(array(':id'=>$currentApprovalPathNameId));
	$pathNameRow=$pathNameStatement->fetch();
	$requestionFullApprovalPathName=!empty($pathNameRow["approval_path_name"]) ? $pathNameRow["approval_path_name"] : 'Approval Path #'.$currentApprovalPathNameId;
	$pathStatusStatement=$pdo->prepare("SELECT approval_status,approval_date,approval_time,asign_time,note,assign_employee_id FROM project_material_aproval_status WHERE invoice_id=:invoice_id AND project_id=:project_id AND employee_id=:employee_id AND deleted_at IS NULL ORDER BY id DESC LIMIT 1");
	$pathEmployeeStatement=$pdo->prepare("SELECT employee_information.id,employee_information.name_en,hr_designation.name AS designation_name FROM employee_information LEFT JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE employee_information.id=:employee_id AND employee_information.deleted_at IS NULL LIMIT 1");
	foreach(requestionApprovalPathSteps($pdo,$rowdata["invoice_id"],$rowdata["project_id"],$currentApprovalPathNameId) as $pathStep){
		$pathEmployeeStatement->execute(array(':employee_id'=>(int)$pathStep["employee_id"]));
		$pathEmployee=$pathEmployeeStatement->fetch();
		if(empty($pathEmployee)){
			continue;
		}
		$pathStatusStatement->execute(array(':invoice_id'=>$rowdata["invoice_id"],':project_id'=>$rowdata["project_id"],':employee_id'=>(int)$pathStep["employee_id"]));
		$pathStatus=$pathStatusStatement->fetch();
		$requestionFullApprovalPathRows[]=array(
			'step_order'=>(int)$pathStep["step_order"],
			'employee_id'=>$pathEmployee["id"],
			'name_en'=>$pathEmployee["name_en"],
			'designation_name'=>$pathEmployee["designation_name"],
			'approval_status'=>!empty($pathStatus["approval_status"]) ? $pathStatus["approval_status"] : 'Not Assigned',
			'approval_time'=>!empty($pathStatus["approval_time"]) ? $pathStatus["approval_time"] : (!empty($pathStatus["approval_date"]) ? $pathStatus["approval_date"] : (!empty($pathStatus["asign_time"]) ? $pathStatus["asign_time"] : '')),
			'note'=>!empty($pathStatus["note"]) ? $pathStatus["note"] : ''
		);
	}
}
$approvalContextEmployeeId=$LoginReGiSterSession;
$forwardAllowedIds=function_exists('requestionApprovalAllowedEmployeeIds') ? requestionApprovalAllowedEmployeeIds($pdo,$rowdata["invoice_id"],$rowdata["project_id"],$currentApprovalPathNameId,$approvalContextEmployeeId,'forward') : array();
$returnAllowedIds=function_exists('requestionApprovalAllowedEmployeeIds') ? requestionApprovalAllowedEmployeeIds($pdo,$rowdata["invoice_id"],$rowdata["project_id"],$currentApprovalPathNameId,$approvalContextEmployeeId,'return') : array();

if(!function_exists('requestionApprovalEmployeeRows')){
function requestionApprovalEmployeeRows($pdo,$employeeIds){
	if(empty($employeeIds)){
		return array();
	}
	$ids=array_values(array_unique(array_map('intval',$employeeIds)));
	$idList=implode(',',$ids);
	$statement=$pdo->query("SELECT employee_information.id,employee_information.name_en,hr_designation.name AS designation_name FROM employee_information LEFT JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE employee_information.deleted_at IS NULL AND employee_information.id IN ($idList) ORDER BY FIELD(employee_information.id,$idList)");
	return $statement ? $statement->fetchAll() : array();
}
}

$forwardEmployeeRows=requestionApprovalEmployeeRows($pdo,$forwardAllowedIds);
$returnEmployeeRows=requestionApprovalEmployeeRows($pdo,$returnAllowedIds);
if(empty($forwardEmployeeRows) && !empty($currentPendingApprovalRow)){
	$forwardUnsignedFallbackStatement=$pdo->prepare("SELECT DISTINCT employee_id FROM project_material_aproval_status WHERE invoice_id=:invoice_id AND project_id=:project_id AND approval_id IS NULL AND deleted_at IS NULL AND employee_id<>:employee_id AND (approval_status IS NULL OR approval_status='Pending') ORDER BY id ASC");
	$forwardUnsignedFallbackStatement->execute(array(':invoice_id'=>$rowdata["invoice_id"],':project_id'=>$rowdata["project_id"],':employee_id'=>$LoginReGiSterSession));
	$forwardEmployeeRows=requestionApprovalEmployeeRows($pdo,$forwardUnsignedFallbackStatement->fetchAll(PDO::FETCH_COLUMN));
}
if(empty($returnEmployeeRows) && !empty($currentPendingApprovalRow)){
	$returnApprovedFallbackStatement=$pdo->prepare("SELECT DISTINCT employee_id FROM project_material_aproval_status WHERE invoice_id=:invoice_id AND project_id=:project_id AND approval_status='Approve' AND approval_id IS NOT NULL AND deleted_at IS NULL AND employee_id<>:employee_id ORDER BY id ASC");
	$returnApprovedFallbackStatement->execute(array(':invoice_id'=>$rowdata["invoice_id"],':project_id'=>$rowdata["project_id"],':employee_id'=>$LoginReGiSterSession));
	$returnEmployeeRows=requestionApprovalEmployeeRows($pdo,$returnApprovedFallbackStatement->fetchAll(PDO::FETCH_COLUMN));
}
if(empty($currentApprovalPathNameId)){
	$fallbackForwardStatement=$pdo->query("SELECT employee_information.id,employee_information.name_en,hr_designation.name AS designation_name FROM employee_information LEFT JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE employee_information.user_status='Active' AND employee_information.deleted_at IS NULL AND employee_information.id<>'".$LoginReGiSterSession."' ORDER BY employee_information.name_en ASC");
	$forwardEmployeeRows=$fallbackForwardStatement ? $fallbackForwardStatement->fetchAll() : array();
	$fallbackReturnStatement=$pdo->query("SELECT employee_information.id,employee_information.name_en,hr_designation.name AS designation_name FROM employee_information LEFT JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE employee_information.user_status='Active' AND employee_information.deleted_at IS NULL AND employee_information.id<>'".$LoginReGiSterSession."' ORDER BY employee_information.name_en ASC");
	$returnEmployeeRows=$fallbackReturnStatement ? $fallbackReturnStatement->fetchAll() : array();
}

 ?>
<style type="text/css" media='print'>
@media print{
    .printable{
        font-size: 11px;
    }
    .logo{
        margin-bottom: 20pt;
    }
    .break-before{
        page-break-before: always !important;
    }
    .break-after{
        page-break-after: always !important;
    }
    .no-break{
        page-break-inside: avoid !important;
    }
    .with-border{
        border: none !important;
    }
    .box{
        border: none !important;
        box-shadow: none !important;
    }
    .table-data {
        overflow: visible !important;
    }
    #printPageButton {
    display: none;
  }

}

@media print
{
    .no-print, .no-print *
    {
        display: none !important;
    }
}th{
		text-align:center;
		}
		.table-responsive{

			}
</style>

<style>
.div-design-print{
	background: white;
}
body{
	background: #fff;
}
.approval-flow-panel .timeline-item{
	border-left:3px solid #007bff;
	padding:0 0 12px 14px;
	margin-left:6px;
}
.approval-flow-panel .timeline-item:last-child{
	padding-bottom:0;
}
.approval-flow-panel .timeline-title{
	font-weight:600;
	margin-bottom:2px;
}
.approval-flow-panel .timeline-meta{
	color:#6c757d;
	font-size:13px;
}
.super-admin-path-panel{
	margin:18px 0 0;
	border:1px solid #dbe7f3;
	border-radius:8px;
	background:#fff;
	box-shadow:0 10px 24px rgba(15,23,42,.06);
	overflow:hidden;
}
.super-admin-path-panel .path-panel-head{
	display:flex;
	align-items:center;
	justify-content:space-between;
	gap:10px;
	padding:12px 14px;
	background:#f8fafc;
	border-bottom:1px solid #edf2f7;
}
.super-admin-path-panel h4{
	margin:0;
	color:#172033;
	font-size:15px;
	font-weight:850;
}
.super-admin-path-panel .path-name{
	color:#2563eb;
	font-size:12px;
	font-weight:850;
	text-align:right;
}
.super-admin-path-table{
	width:100%;
	margin:0;
	border-collapse:collapse;
}
.super-admin-path-table th,
.super-admin-path-table td{
	padding:8px 10px !important;
	border:1px solid #dbe7f3 !important;
	font-size:12px !important;
	vertical-align:middle !important;
}
.super-admin-path-table th{
	background:#f1f6fb;
	color:#334155;
	font-weight:850;
}
.path-status-badge{
	display:inline-block;
	padding:4px 8px;
	border-radius:999px;
	background:#eef2f7;
	color:#334155;
	font-size:11px;
	font-weight:850;
}
.path-status-approve{background:#ecfdf3;color:#15803d;}
.path-status-pending{background:#fff7d6;color:#a16207;}
.path-status-returned,.path-status-forwarded{background:#eff6ff;color:#1d4ed8;}
.path-status-reject{background:#fee2e2;color:#b91c1c;}


th,td{
    font-size:18px !important;
}
@media all {
.page-break { display: none; }
}

@media print {
.page-break { display: block; page-break-before: always; }
.approval-flow-panel,
.approval-action-panel{
	display:none !important;
}
}
</style>

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
	 padding: 20px;
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
	width: 300px;
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
.super-admin-edit-panel{
	margin: 16px 18px;
	border: 1px solid #dbe5ef;
	border-radius: 8px;
	background: #fff;
	box-shadow: 0 12px 32px rgba(15,23,42,.08);
	overflow: hidden;
}
.super-admin-edit-panel .edit-panel-head{
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 12px;
	padding: 14px 16px;
	border-bottom: 1px solid #edf2f7;
	background: #f8fafc;
}
.super-admin-edit-panel h4{
	margin: 0;
	color: #172033;
	font-size: 16px;
	font-weight: 800;
}
.super-admin-edit-panel .edit-panel-badge{
	display: inline-flex;
	align-items: center;
	gap: 7px;
	padding: 6px 9px;
	border-radius: 8px;
	background: #eaf8f0;
	color: #15803d;
	font-size: 12px;
	font-weight: 800;
}
.super-admin-edit-panel .edit-panel-body{
	padding: 16px;
}
.super-admin-table{
	min-width: 980px;
}
.super-admin-table th{
	background: #f8fafc;
	color: #52616f;
	font-size: 12px !important;
	font-weight: 800;
	white-space: nowrap;
}
.super-admin-table td{
	font-size: 13px !important;
	vertical-align: middle !important;
}
.signature-grid{
	display: grid;
	grid-template-columns: repeat(3, minmax(180px, 1fr));
	gap: 18px;
	width: 100%;
	margin-top: 34px;
}
.signature-card{
	min-height: 126px;
	display: flex;
	flex-direction: column;
	justify-content: flex-end;
	padding: 12px;
	border-top: 1px solid #d9e3ee;
}
.signature-card img{
	width: 110px;
	height: 36px;
	object-fit: contain;
	margin-bottom: 6px;
}
.signature-card .signature-date{
	margin: 0 0 4px;
	font-size: 14px !important;
	color: #334155;
}
.signature-card .signature-name{
	margin: 0 0 2px;
	font-size: 15px !important;
	font-weight: 800;
	color: #172033;
}
.signature-card .signature-designation{
	margin: 0;
	font-size: 12px !important;
	color: #52616f;
}
@media screen and (max-width: 767.98px){
	.super-admin-edit-panel{
		margin: 10px 8px;
	}
	.super-admin-edit-panel .edit-panel-head{
		display: block;
	}
	.super-admin-edit-panel .edit-panel-badge{
		margin-top: 8px;
	}
	.signature-grid{
		grid-template-columns: 1fr;
		gap: 10px;
	}
}
@media print{
	.super-admin-edit-panel{
		display:none !important;
	}
	body,
	.content,
	.card,
	.card-body,
	.container,
	.row,
	.my-info-area{
		background:#fff !important;
		box-shadow:none !important;
	}
	.content,
	.card,
	.card-body,
	.container{
		margin:0 !important;
		padding:0 !important;
		width:100% !important;
		max-width:none !important;
	}
	.my-info-area{
		margin:0 !important;
		padding:14px 18px !important;
		min-height:0 !important;
		page-break-inside:auto !important;
	}
	.details-div-area{
		padding:10px 8px !important;
	}
	.table-div-area{
		padding-top:6px !important;
		page-break-after:auto !important;
	}
	.table-div-area br{
		display:none !important;
	}
	.title-div{
		margin-bottom:4px !important;
	}
	.signature-grid{
		display:grid !important;
		grid-template-columns:repeat(5, minmax(0, 1fr)) !important;
		gap:10px 14px !important;
		width:100% !important;
		margin-top:18px !important;
		page-break-inside:avoid !important;
		break-inside:avoid !important;
	}
	.signature-card{
		min-height:74px !important;
		padding:6px 4px 0 !important;
		border-top:1px solid #b7d8ef !important;
		page-break-inside:avoid !important;
		break-inside:avoid !important;
	}
	.signature-card img{
		width:76px !important;
		height:24px !important;
		margin-bottom:4px !important;
	}
	.signature-card .signature-date{
		font-size:11px !important;
		margin-bottom:2px !important;
	}
	.signature-card .signature-name{
		font-size:12px !important;
		line-height:1.15 !important;
		margin-bottom:2px !important;
	}
	.signature-card .signature-designation{
		font-size:9px !important;
		line-height:1.15 !important;
	}
	.col-sm-12{
		width:100% !important;
		max-width:100% !important;
		flex:0 0 100% !important;
	}
}

.requisition-detail-page{
	--doc-ink:#1f2937;
	--doc-muted:#64748b;
	--doc-line:#d8e4f0;
	--doc-soft:#f6f8fb;
	--doc-accent:#2563eb;
	--doc-accent-dark:#1d4ed8;
	background:#f4f7fb;
	padding:8px 12px 14px;
}
.requisition-detail-card{
	border:0;
	border-radius:8px;
	background:transparent;
	box-shadow:none;
}
.document-toolbar{
	display:flex;
	align-items:center;
	justify-content:flex-end;
	gap:16px;
	margin:0 0 8px;
	padding:0;
	border:0;
	border-radius:0;
	background:transparent;
	box-shadow:none;
}
.document-toolbar h3{
	display:none;
}
.document-toolbar p{
	display:none;
}
.document-toolbar-actions{
	display:flex;
	align-items:center;
	gap:8px;
}
.document-toolbar .btn{
	min-height:36px;
	border-radius:8px;
	font-weight:800;
	box-shadow:none;
}
.document-toolbar .btn-success{
	border-color:#16a34a;
	background:#16a34a;
}
.document-toolbar .btn-light{
	border-color:#dbe5ef;
	background:#f8fafc;
	color:#334155;
}
.requisition-document-stage{
	padding:0;
}
.requisition-document-wrap{
	width:100%;
	max-width:none;
	margin:0 auto;
	padding:0;
}
.requisition-paper{
	width:100%;
	max-width:none;
	min-height:0;
	margin:0 auto 14px !important;
	padding:20px 24px !important;
	display:block;
	background:#fff;
	color:var(--doc-ink);
	border:1px solid #dbe7f3;
	border-radius:8px;
	box-shadow:0 24px 60px rgba(15,23,42,.12);
}
.requisition-paper:before,
.requisition-paper:after{
	content:none !important;
}
.requisition-paper .header-area{
	padding:0 0 10px !important;
	margin:0 0 10px;
	border-bottom:1px solid var(--doc-line);
	text-align:center;
	color:var(--doc-ink);
}
.print-company-name{
	margin:0;
	color:#1d4ed8;
	font-size:24px !important;
	line-height:1.15;
	font-weight:850;
	letter-spacing:0;
}
.print-company-address{
	display:none;
	margin:8px auto 0;
	max-width:760px;
	color:#475569;
			font-size:15px !important;
	line-height:1.45;
}
.print-document-title{
	display:none;
	align-items:center;
	justify-content:center;
	margin:13px 0 0;
	padding:7px 13px;
	border:1px solid #c7d7ea;
	border-radius:8px;
	background:#f8fbff;
	color:#172033;
	font-size:18px !important;
	line-height:1.2;
	font-weight:850;
	text-decoration:none !important;
}
.print-document-title u{
	text-decoration:none !important;
}
.title-div{
	margin:0 0 10px;
	padding:8px 12px;
	border:1px solid #dbe7f3;
	border-radius:8px;
	background:#f8fafc;
	color:#172033;
	font-size:14px !important;
	line-height:1.45;
}
.requisition-paper .details-div-area{
	margin:0 0 12px;
	padding:0 !important;
	font-style:normal;
}
.requisition-paper .details-div-area table{
	width:100%;
	border-collapse:separate;
	border-spacing:0;
}
.requisition-paper .details-div-area td{
	padding:6px 8px !important;
	color:#334155;
	font-size:13px !important;
	line-height:1.35;
	vertical-align:top;
	border-bottom:1px solid #edf2f7;
}
.requisition-paper .details-div-area td:nth-child(odd){
	width:16%;
	color:#64748b;
	font-weight:800;
}
.requisition-paper .details-div-area .data-row{
	color:#1f2937;
	font-weight:700;
	border-bottom:1px solid #dbe7f3;
}
.table-div-area{
	padding:0 !important;
	font-style:normal;
	overflow:visible;
}
.table-div-area > br{
	display:none;
}
.table-div-area table,
.requisition-paper .order-list{
	width:100% !important;
	border-collapse:collapse;
	table-layout:fixed;
	margin:0;
	background:#fff;
}
.table-div-area th,
.table-div-area td,
.requisition-paper .order-list th,
.requisition-paper .order-list td{
	padding:7px 7px !important;
	border:1px solid var(--doc-line) !important;
	color:#273647;
	font-size:12px !important;
	line-height:1.35;
	font-style:normal !important;
	vertical-align:middle !important;
	word-break:normal;
	overflow-wrap:anywhere;
}
.table-div-area th,
.requisition-paper .order-list th{
	background:#f1f6fb !important;
	color:#334155;
	font-weight:850 !important;
	text-align:center;
}
.table-div-area td:first-child{
	width:34px;
}
.table-div-area tr:nth-child(even) td{
	background:#fbfdff;
}
.requisition-items-table.material-items col.col-sl{
	width:32px;
}
.requisition-items-table.material-items col.col-name{
	width:50%;
}
.requisition-items-table.material-items col.col-total{
	width:96px;
}
.requisition-items-table.material-items col.col-emergency{
	width:94px;
}
.requisition-items-table.material-items col.col-distribution{
	width:112px;
}
.requisition-items-table.material-items col.col-due{
	width:88px;
}
.requisition-items-table.material-items col.col-rate{
	width:64px;
}
.requisition-items-table.material-items col.col-amount{
	width:76px;
}
.requisition-items-table.material-items col.col-remarks{
	width:72px;
}
.requisition-items-table.material-items td:nth-child(2),
.requisition-items-table.material-items th:nth-child(2){
	text-align:left;
}
.requisition-items-table.material-items td:nth-child(2){
	font-weight:700;
}
.requisition-items-table.material-items .total-amount-label{
	text-align:right !important;
	padding-right:12px !important;
}
.fund-total-label{
	text-align:right !important;
	padding-right:12px !important;
}
.fund-total-amount{
	text-align:center !important;
}
.requisition-paper .form-control{
	min-height:32px;
	border-color:#dbe7f3;
	border-radius:6px;
	color:#273647;
	font-size:13px !important;
	box-shadow:none;
}
.approval-table-scroll{
	width:100%;
	overflow-x:auto;
	-webkit-overflow-scrolling:touch;
	border:1px solid #dbe7f3;
	border-radius:8px;
	background:#fff;
}
.requisition-paper .order-list.requisition-approval-table{
	min-width:1040px;
	border:0;
	table-layout:fixed;
}
.requisition-paper .order-list.requisition-approval-table th,
.requisition-paper .order-list.requisition-approval-table td{
	padding:9px 10px !important;
	overflow-wrap:normal;
	word-break:normal;
}
.requisition-paper .order-list.requisition-approval-table th:first-child,
.requisition-paper .order-list.requisition-approval-table td:first-child{
	width:auto !important;
	min-width:0 !important;
	max-width:none !important;
	padding-left:10px !important;
	padding-right:10px !important;
	text-align:left !important;
	white-space:normal !important;
}
.requisition-paper .order-list.requisition-approval-table th{
	white-space:nowrap;
}
.requisition-paper .order-list.requisition-approval-table td{
	background:#fff;
}
.requisition-paper .order-list.requisition-approval-table tbody tr:nth-child(even) td{
	background:#fbfdff;
}
.requisition-paper .order-list.requisition-approval-table .item-name{
	color:#1f2937;
	font-weight:750;
	line-height:1.35;
}
.requisition-paper .order-list.requisition-approval-table .serial-cell{
	color:#334155;
	font-weight:750;
	text-align:center !important;
	white-space:nowrap;
}
.requisition-paper .order-list.requisition-approval-table th.serial-cell,
.requisition-paper .order-list.requisition-approval-table td.serial-cell{
	width:46px !important;
	min-width:46px !important;
	max-width:46px !important;
	padding-left:6px !important;
	padding-right:6px !important;
}
.requisition-paper .order-list.requisition-approval-table th.item-name,
.requisition-paper .order-list.requisition-approval-table td.item-name{
	text-align:left !important;
}
.requisition-paper .order-list.requisition-approval-table .unit-cell{
	color:#475569;
	font-weight:750;
	text-align:center;
	white-space:nowrap;
}
.requisition-paper .order-list.requisition-approval-table .number-cell{
	text-align:right;
	white-space:nowrap;
}
.requisition-paper .order-list.requisition-approval-table .remarks-cell{
	line-height:1.35;
	overflow-wrap:anywhere;
}
.requisition-paper .order-list.requisition-approval-table .form-control{
	width:100%;
	min-height:34px;
	padding:6px 8px;
	text-align:center;
	background:#fff;
}
.requisition-paper .order-list.requisition-approval-table .form-control[readonly]{
	background:#eef2f7;
	color:#334155;
	font-weight:750;
}
.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-sl,
.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-sl{
	width:46px;
}
.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-name{
	width:43%;
}
.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-unit{
	width:80px;
}
.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-stock{
	width:95px;
}
.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-qty{
	width:105px;
}
.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-rate{
	width:85px;
}
.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-remarks{
	width:155px;
}
.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-description{
	width:31%;
}
.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-amount{
	width:145px;
}
.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-qty{
	width:105px;
}
.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-rate{
	width:95px;
}
.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-approval{
	width:145px;
}
.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-remarks{
	width:170px;
}
.requisition-paper .form-group label,
.requisition-paper label{
	color:#334155;
	font-size:13px !important;
	font-weight:850;
}
.requisition-paper .form-group:has(label[for="note"]){
	margin:18px 0 8px !important;
	padding:15px 16px !important;
	border:1px solid #dbe7f3 !important;
	border-left:4px solid #2563eb !important;
	border-radius:8px !important;
	background:linear-gradient(180deg, #f8fbff, #ffffff) !important;
	box-shadow:0 10px 24px rgba(15,23,42,.05) !important;
}
.requisition-paper .form-group:has(label[for="note"]) label{
	display:flex !important;
	align-items:center !important;
	gap:8px !important;
	margin:0 0 8px !important;
	color:#1f3b63 !important;
	font-size:14px !important;
}
.requisition-paper .form-group:has(label[for="note"]) p{
	margin:0 !important;
	color:#172033 !important;
	font-size:15px !important;
	line-height:1.55 !important;
}
.requisition-paper .form-group:has(label[for="note"]) textarea{
	min-height:88px !important;
	border-color:#cbd9e8 !important;
	border-radius:8px !important;
	font-size:14px !important;
}
.approval-note-grid{
	display:grid;
	grid-template-columns:repeat(4, minmax(0, 1fr));
	gap:8px;
	width:100%;
	margin:12px 0 8px;
	padding:0 15px;
}
.approval-note-list{
	display:contents;
}
.approval-note-title{
	grid-column:1 / -1;
	padding:6px 8px;
	background:#f3f7fb;
	border:1px solid #d9e5f2;
	border-radius:5px;
	color:#1f3b63;
	font-size:12px;
	font-weight:800;
}
.approval-note-row{
	min-width:0;
	padding:7px 8px;
	border:1px solid #d9e5f2;
	border-left:3px solid #2563eb;
	border-radius:5px;
	background:#fff;
}
.approval-note-meta{
	color:#50627a;
	font-size:10.5px;
	line-height:1.25;
	margin-bottom:4px;
}
.approval-note-meta strong{
	display:block;
	color:#172033;
	font-size:11px;
}
.approval-note-text{
	color:#172033;
	font-size:11px;
	line-height:1.35;
	word-break:break-word;
}
.rate-proof-grid{
	display:grid;
	grid-template-columns:repeat(4, minmax(180px, 1fr));
	gap:12px;
	width:100%;
	margin:14px 0 12px;
	padding:0 15px;
}
.rate-proof-title{
	grid-column:1 / -1;
	padding:8px 10px;
	background:#f3f7fb;
	border:1px solid #d9e5f2;
	border-radius:5px;
	color:#1f3b63;
	font-size:13px;
	font-weight:900;
}
.rate-proof-card{
	min-width:0;
	border:1px solid #d9e5f2;
	border-left:4px solid #2563eb;
	border-radius:6px;
	background:#fff;
	overflow:hidden;
	box-shadow:0 8px 18px rgba(15,23,42,.05);
}
.rate-proof-preview{
	height:220px;
	background:#f8fafc;
	border-bottom:1px solid #e5edf5;
	display:flex;
	align-items:center;
	justify-content:center;
	overflow:hidden;
}
.rate-proof-preview img,
.rate-proof-preview iframe{
	width:100%;
	height:100%;
	border:0;
	object-fit:contain;
	background:#fff;
}
.rate-proof-file-icon{
	color:#64748b;
	font-size:44px;
}
.rate-proof-info{
	padding:9px 10px 11px;
}
.rate-proof-meta{
	color:#50627a;
	font-size:11px;
	line-height:1.35;
	margin-bottom:7px;
}
.rate-proof-name{
	color:#172033;
	font-size:12px;
	font-weight:800;
	word-break:break-word;
}
@media screen and (max-width: 1399.98px){
	.approval-note-grid{
		grid-template-columns:repeat(3, minmax(0, 1fr));
	}
	.rate-proof-grid{
		grid-template-columns:repeat(3, minmax(180px, 1fr));
	}
}
@media screen and (max-width: 991.98px){
	.approval-note-grid{
		grid-template-columns:repeat(2, minmax(0, 1fr));
	}
	.rate-proof-grid{
		grid-template-columns:repeat(2, minmax(180px, 1fr));
	}
}
@media screen and (max-width: 575.98px){
	.approval-note-grid{
		grid-template-columns:1fr;
		padding:0;
	}
	.rate-proof-grid{
		grid-template-columns:1fr;
		padding:0;
	}
}
@media print{
	.rate-proof-grid{
		display:none !important;
	}
	.approval-note-grid{
		display:grid !important;
		grid-template-columns:repeat(3, minmax(0, 1fr)) !important;
		gap:4px !important;
		margin:6px 0 !important;
		padding:0 !important;
	}
	.approval-note-title{
		grid-column:1 / -1 !important;
		padding:5px 7px !important;
		font-size:12px !important;
	}
	.approval-note-row{
		padding:7px 8px !important;
		border-left-width:2px !important;
	}
	.approval-note-meta{
		font-size:10.5px !important;
		line-height:1.35 !important;
		margin-bottom:5px !important;
	}
	.approval-note-text{
		font-size:11.5px !important;
		line-height:1.45 !important;
	}
	.approval-note-meta strong{
		font-size:11px !important;
	}
}
.signature-grid{
	margin-top:30px;
	padding-top:48px;
	border-top:1px solid #dbe7f3;
	position:relative;
}
.signature-grid:before{
	content:"Approval Signatures";
	position:absolute;
	top:14px;
	left:0;
	color:#172033;
	font-size:16px;
	font-weight:850;
}
.signature-grid:after{
	content:"Verified approval chain";
	position:absolute;
	top:17px;
	right:0;
	color:#64748b;
	font-size:12px;
	font-weight:750;
}
.signature-card{
	min-height:142px;
	justify-content:space-between;
	padding:14px 16px;
	border:1px solid #dbe7f3;
	border-radius:8px;
	background:linear-gradient(180deg, #fbfdff, #ffffff);
	box-shadow:0 10px 24px rgba(15,23,42,.05);
	position:relative;
}
.signature-card:before{
	content:"Approved";
	align-self:flex-start;
	margin-bottom:8px;
	padding:4px 8px;
	border-radius:999px;
	background:#ecfdf3;
	color:#15803d;
	font-size:10px;
	font-weight:850;
}
.signature-card .signature-date{
	color:#64748b;
	font-size:14px !important;
}
.signature-card .signature-name{
	color:#172033;
	font-size:18px !important;
}
.signature-card .signature-designation{
	color:#64748b;
	font-size:14px !important;
}
@media screen and (max-width: 991.98px){
	.requisition-detail-page{
		padding:10px;
	}
	.document-toolbar{
		display:block;
	}
	.document-toolbar-actions{
		margin-top:12px;
	}
	.requisition-paper{
		padding:18px !important;
		min-height:0;
	}
	.print-company-name{
		font-size:22px !important;
	}
}
.approval-action-panel{
	width:100%;
	max-width:100%;
}
.card-footer .approval-action-panel{
	float:none !important;
	display:flex;
	align-items:center;
	justify-content:flex-end;
	flex-wrap:wrap;
	gap:8px;
	width:100%;
	margin:0;
}
.card-footer .approval-action-panel .btn{
	min-height:38px;
	margin:0 !important;
	border-radius:7px;
	font-weight:800;
	white-space:nowrap;
}
.approval-flow-panel{
	border-radius:8px;
	overflow:hidden;
}
.approval-flow-panel .card-header{
	padding:14px 16px;
}
.approval-flow-panel .card-title{
	margin:0;
	font-size:18px;
	font-weight:850;
}
.approval-flow-panel .card-body{
	padding:16px 18px;
}
@media screen and (max-width: 767.98px){
	.requisition-detail-page{
		padding:8px;
	}
	.document-toolbar-actions{
		display:grid;
		grid-template-columns:1fr 1fr;
		gap:8px;
		width:100%;
	}
	.document-toolbar .btn{
		width:100%;
	}
	.requisition-paper{
		padding:14px !important;
		border-radius:8px;
		box-shadow:0 12px 28px rgba(15,23,42,.08);
	}
	.print-company-name{
		font-size:20px !important;
		overflow-wrap:anywhere;
	}
	.title-div{
		padding:8px 10px;
		font-size:13px !important;
	}
	.requisition-paper .details-div-area table,
	.requisition-paper .details-div-area tbody,
	.requisition-paper .details-div-area tr,
	.requisition-paper .details-div-area td{
		display:block;
		width:100% !important;
	}
	.requisition-paper .details-div-area tr{
		padding:8px 0;
		border-bottom:1px solid #edf2f7;
	}
	.requisition-paper .details-div-area td{
		padding:2px 0 !important;
		border-bottom:0;
	}
	.requisition-paper .details-div-area td:nth-child(odd){
		width:100% !important;
		font-size:12px !important;
	}
	.table-div-area,
	.approval-table-scroll{
		overflow-x:auto;
		-webkit-overflow-scrolling:touch;
	}
	.signature-grid{
		grid-template-columns:1fr !important;
		gap:10px !important;
		margin-top:20px !important;
		padding-top:66px !important;
	}
	.signature-grid:before,
	.signature-grid:after{
		position:absolute !important;
		left:0 !important;
		right:auto !important;
		display:block !important;
		width:100% !important;
		white-space:normal !important;
		overflow-wrap:anywhere !important;
	}
	.signature-grid:before{
		top:12px !important;
		font-size:18px !important;
		line-height:1.15 !important;
	}
	.signature-grid:after{
		top:40px !important;
		font-size:12px !important;
		line-height:1.2 !important;
	}
	.signature-card{
		min-height:118px;
		padding:13px 14px;
	}
	.signature-card .signature-name{
		font-size:17px !important;
		overflow-wrap:anywhere;
	}
	.approval-action-panel .row{
		margin-left:-6px;
		margin-right:-6px;
	}
	.approval-action-panel .row > [class*="col-"]{
		padding-left:6px;
		padding-right:6px;
	}
	.card-footer{
		padding:12px;
	}
	.card-footer .approval-action-panel{
		display:grid;
		grid-template-columns:repeat(2, minmax(0, 1fr));
		gap:8px;
	}
	.card-footer .approval-action-panel .btn{
		width:100%;
		min-width:0;
		min-height:44px;
		padding:9px 8px;
		font-size:15px;
		line-height:1.2;
		white-space:normal;
	}
	.card-footer .approval-action-panel .btn i{
		margin-right:5px;
	}
	.approval-flow-panel{
		margin:12px 0 0;
		border-radius:8px;
	}
	.approval-flow-panel .card-header{
		padding:12px 14px;
	}
	.approval-flow-panel .card-title{
		font-size:18px;
		line-height:1.2;
	}
	.approval-flow-panel .card-body{
		padding:14px 14px 14px 18px;
	}
	.approval-flow-panel .timeline-item{
		padding:0 0 14px 12px;
		margin-left:2px;
	}
	.approval-flow-panel .timeline-title{
		font-size:16px;
		line-height:1.25;
		overflow-wrap:anywhere;
	}
	.approval-flow-panel .timeline-meta{
		font-size:13px;
		line-height:1.35;
		overflow-wrap:anywhere;
	}
}
@media screen and (max-width: 420px){
	.requisition-paper{
		padding:12px !important;
	}
	.card-footer .approval-action-panel{
		grid-template-columns:1fr 1fr;
	}
	.card-footer .approval-action-panel .btn{
		font-size:14px;
		padding-left:6px;
		padding-right:6px;
	}
	.signature-grid{
		padding-top:70px !important;
	}
	.signature-grid:before{
		font-size:17px !important;
	}
	.signature-card .signature-date{
		font-size:13px !important;
	}
	.signature-card .signature-name{
		font-size:16px !important;
	}
	.signature-card .signature-designation{
		font-size:13px !important;
	}
}
@media print{
	@page{
		size:A4 portrait;
		margin:9mm;
	}
	html,
	body{
		width:100% !important;
		min-height:auto !important;
		background:#fff !important;
	}
	body{
		margin:0 !important;
		-webkit-print-color-adjust:exact !important;
		print-color-adjust:exact !important;
	}
	.main-header,
	.main-sidebar,
	.content-header,
	.main-footer,
	.document-toolbar,
	.card-header,
	.card-footer,
	.d-print-none,
	.no-print,
	.no-print *{
		display:none !important;
	}
	.content-wrapper,
	.requisition-detail-page,
	.requisition-detail-card,
	.requisition-document-stage,
	.requisition-document-wrap{
		width:100% !important;
		max-width:none !important;
		min-height:0 !important;
		margin:0 !important;
		padding:0 !important;
		background:#fff !important;
		box-shadow:none !important;
		border:0 !important;
	}
		.requisition-paper{
			width:100% !important;
			max-width:none !important;
			min-height:0 !important;
			margin:0 !important;
		padding:0 !important;
			border:0 !important;
			border-radius:0 !important;
			box-shadow:none !important;
			font-size:15px !important;
		}
	.print-company-name{
			font-size:30px !important;
		color:#1f4ecb !important;
	}
	.print-company-address{
		display:block !important;
		width:100% !important;
		max-width:none !important;
		margin:2mm auto 0 !important;
		color:#475569 !important;
			font-size:15px !important;
		line-height:1.25 !important;
		text-align:center !important;
		white-space:nowrap !important;
	}
	.print-document-title{
		display:block !important;
		margin-top:8px !important;
		padding:0 !important;
		border:0 !important;
		background:transparent !important;
			font-size:22px !important;
		text-decoration:underline !important;
	}
	.print-document-title u{
		text-decoration:underline !important;
	}
	.title-div{
		margin:2mm 0 3mm !important;
		padding:2mm 3mm !important;
		border-color:#d8e4f0 !important;
		border-radius:0 !important;
			font-size:20px !important;
		line-height:1.3 !important;
	}
	.requisition-paper .header-area{
		margin-bottom:3mm !important;
		padding-bottom:3mm !important;
	}
	.requisition-paper .details-div-area{
		margin-bottom:4mm !important;
	}
	.requisition-paper .details-div-area td{
		padding:1.3mm 1.8mm !important;
			font-size:15px !important;
		line-height:1.2 !important;
	}
	.table-div-area table,
	.requisition-paper .order-list{
		table-layout:fixed !important;
		page-break-inside:auto !important;
	}
	.table-div-area tr,
	.requisition-paper .order-list tr{
		page-break-inside:avoid !important;
		break-inside:avoid !important;
	}
	.table-div-area th,
	.table-div-area td,
	.requisition-paper .order-list th,
	.requisition-paper .order-list td{
		padding:1.4mm 1mm !important;
			font-size:15px !important;
		line-height:1.18 !important;
		border-color:#bfd6ea !important;
	}
	.table-div-area th,
	.table-div-area td,
	.requisition-paper .order-list th,
	.requisition-paper .order-list td{
			font-size:15px !important;
	}
	.table-div-area th,
	.requisition-paper .order-list th{
		background:#eef5fb !important;
	}
	.requisition-paper .requisition-items-table th,
	.requisition-paper .requisition-items-table td{
		word-break: normal !important;
		overflow-wrap: normal !important;
		hyphens: none !important;
		white-space: normal !important;
	}
	.requisition-paper .order-list.requisition-approval-table{
		width:100% !important;
		table-layout:fixed !important;
	}
	.requisition-paper .order-list.requisition-approval-table th,
	.requisition-paper .order-list.requisition-approval-table td{
		padding:1mm .8mm !important;
		line-height:1.15 !important;
		white-space:normal !important;
		word-break:normal !important;
		overflow-wrap:anywhere !important;
		hyphens:auto !important;
		vertical-align:middle !important;
	}
	.requisition-paper .order-list.requisition-approval-table th{
		text-align:center !important;
	}
	.requisition-paper .order-list.requisition-approval-table td:first-child,
	.requisition-paper .order-list.requisition-approval-table th:first-child{
		text-align:center !important;
	}
	.requisition-paper .order-list.requisition-approval-table .form-control{
		width:100% !important;
		max-width:100% !important;
		line-height:1.15 !important;
		text-align:center !important;
		white-space:normal !important;
		overflow-wrap:anywhere !important;
	}
	.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-sl{
		width:5% !important;
	}
	.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-description{
		width:32% !important;
	}
	.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-amount{
		width:13% !important;
	}
	.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-qty{
		width:8% !important;
	}
	.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-rate{
		width:9% !important;
	}
	.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-approval{
		width:14% !important;
	}
	.requisition-paper .order-list.requisition-approval-table.fund-approval-table col.col-remarks{
		width:19% !important;
	}
	.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-sl{
		width:5% !important;
	}
	.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-name{
		width:28% !important;
	}
	.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-unit{
		width:8% !important;
	}
	.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-stock,
	.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-qty,
	.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-rate{
		width:10% !important;
	}
	.requisition-paper .order-list.requisition-approval-table.material-approval-table col.col-remarks{
		width:19% !important;
	}
	.requisition-items-table.material-items col.col-sl{
		width:3%;
	}
	.requisition-items-table.material-items col.col-name{
		width:32%;
	}
	.requisition-items-table.material-items col.col-total{
		width:10%;
	}
	.requisition-items-table.material-items col.col-emergency{
		width:11%;
	}
	.requisition-items-table.material-items col.col-distribution{
		width:11%;
	}
	.requisition-items-table.material-items col.col-due{
		width:10%;
	}
	.requisition-items-table.material-items col.col-rate{
		width:6%;
	}
	.requisition-items-table.material-items col.col-amount{
		width:7%;
	}
	.requisition-items-table.material-items col.col-remarks{
		width:10%;
	}
	.requisition-paper .form-control{
		height:auto !important;
		min-height:0 !important;
		padding:0 !important;
		border:0 !important;
		background:transparent !important;
			font-size:15px !important;
	}
	.requisition-paper a{
		color:#172033 !important;
		text-decoration:none !important;
	}
	.requisition-paper .form-group:has(label[for="note"]){
		margin:4mm 0 5mm !important;
		padding:3mm 4mm !important;
		border:1px solid #d6e5f3 !important;
		border-left:3px solid #2563eb !important;
		border-radius:0 !important;
		background:#f8fbff !important;
		box-shadow:none !important;
	}
	.requisition-paper .form-group:has(label[for="note"]) label{
		margin-bottom:1.5mm !important;
		color:#1f3b63 !important;
			font-size:15px !important;
		font-weight:850 !important;
	}
	.requisition-paper .form-group:has(label[for="note"]) p{
		color:#172033 !important;
			font-size:15px !important;
		line-height:1.45 !important;
	}
	.signature-grid{
		grid-template-columns:repeat(3, minmax(0, 1fr)) !important;
		gap:5mm !important;
		margin-top:8mm !important;
		padding-top:9mm !important;
		border-top:1px solid #dbe7f3 !important;
	}
	.signature-grid:before{
		top:2mm !important;
		left:0 !important;
			font-size:20px !important;
	}
	.signature-grid:after{
		display:none !important;
	}
	.signature-card{
		min-height:28mm !important;
		padding:3mm !important;
		border:1px solid #c9dced !important;
		border-radius:0 !important;
		background:#fff !important;
		box-shadow:none !important;
	}
	.signature-card:before{
		margin-bottom:2mm !important;
		padding:0 !important;
		background:transparent !important;
		color:#15803d !important;
		font-size:11px !important;
	}
	.signature-card img{
		width:28mm !important;
		height:9mm !important;
		margin-bottom:1.5mm !important;
	}
	.signature-card .signature-date{
			font-size:13px !important;
	}
	.signature-card .signature-name{
			font-size:15px !important;
	}
	.signature-card .signature-designation{
			font-size:13px !important;
	}
}
</style>

<section class="content requisition-detail-page">

      <!-- Default box -->
      <div class="card requisition-detail-card">
        <div class="document-toolbar d-print-none">
          <div>
            <h3><?php echo str_replace("_"," ",$page_title); ?></h3>
            <p>Professional requisition summary prepared for review, approval, and A4 printing.</p>
          </div>
          <div class="document-toolbar-actions">
            <button type="button" class="btn btn-light" onclick="history.back(-1)"><i class="fas fa-arrow-left"></i> Back</button>
            <button id="printpagebutton" type="button" class="btn btn-success" onclick="window.print();return false;"><i class="fa fa-print"></i> Print</button>
          </div>
        </div>
        <div class="card-body requisition-document-stage">
			<?php if(!empty($requestion_super_admin_can_edit) && !empty($rowdata)){ ?>
			<div class="super-admin-edit-panel d-print-none">
				<div class="edit-panel-head">
					<h4><i class="fas fa-user-shield"></i> Super Admin Edit</h4>
					<span class="edit-panel-badge"><i class="fas fa-lock-open"></i> Employee ID 121 only</span>
				</div>
					<form method="post" action="" class="edit-panel-body" enctype="multipart/form-data">
						<input type="hidden" name="requestion_approval_csrf" value="<?php echo requestionHistoryHtml(requestionApprovalCsrfToken()); ?>">
						<input type="hidden" name="Super_Admin_Requestion_History_Edit" value="1">
						<input type="hidden" name="invoice_id" value="<?php echo requestionHistoryHtml($Detail); ?>">
					<input type="hidden" name="requistion_type" value="<?php echo requestionHistoryHtml($rowdata["requistion_type"]); ?>">
					<input type="hidden" name="number_count" id="super_admin_number_count" value="0">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>Project</label>
								<select name="project_id" class="form-control select2" style="width:100%;" required>
									<?php foreach($requestion_projects as $project_edit){ ?>
									<option value="<?php echo requestionHistoryHtml($project_edit["id"]); ?>" <?php if($project_edit["id"]==$rowdata["project_id"]){ echo "selected"; } ?>><?php echo requestionHistoryHtml($project_edit["name"]); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Store</label>
								<select name="store_id" class="form-control select2" style="width:100%;" required>
									<?php foreach($requestion_stores as $store_edit){ ?>
									<option value="<?php echo requestionHistoryHtml($store_edit["id"]); ?>" <?php if($store_edit["id"]==$rowdata["store_id"]){ echo "selected"; } ?>><?php echo requestionHistoryHtml($store_edit["name"]); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Date</label>
								<input type="date" name="date" class="form-control" value="<?php echo requestionHistoryHtml(date("Y-m-d", strtotime($rowdata["date"]))); ?>" required>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label>Cash In Hand</label>
								<input type="text" name="previous_cash_in_hand" class="form-control" value="<?php echo requestionHistoryHtml($rowdata["previous_cash_in_hand"]); ?>">
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label>Note / Purpose</label>
								<textarea name="note" class="form-control" rows="2"><?php echo requestionHistoryHtml($rowdata["note"]); ?></textarea>
							</div>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-bordered super-admin-table" id="superAdminRequisitionTable">
							<thead>
								<tr>
									<th style="width:40px;">SL</th>
									<th style="min-width:240px;">Product</th>
									<th>Detail</th>
									<th>Req Qty</th>
									<th>Req Rate</th>
									<th>Req Amount</th>
									<th>Final Qty</th>
									<th>Final Rate</th>
									<th>Final Amount</th>
									<th>Remarks</th>
									<th style="width:80px;">Delete</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$super_admin_products = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name,product_information.unit AS product_unit FROM requestion_detail LEFT JOIN product_information ON requestion_detail.product_id=product_information.id WHERE requestion_detail.invoice_id='$Detail' AND requestion_detail.deleted_at IS NULL ORDER BY requestion_detail.id ASC");
								$super_admin_serial=1;
								while($row_super_admin_product=$super_admin_products->fetch()){
									$row_requestion_amount=!empty($row_super_admin_product["requestion_amount"]) ? $row_super_admin_product["requestion_amount"] : ((float)$row_super_admin_product["requestion_quantity"]*(float)$row_super_admin_product["requistion_rate"]);
									$row_final_amount=!empty($row_super_admin_product["final_amount"]) ? $row_super_admin_product["final_amount"] : ((float)$row_super_admin_product["final_quantity"]*(float)$row_super_admin_product["final_rate"]);
								?>
								<tr class="super-admin-product-row">
									<td class="super-admin-sl"><?php echo $super_admin_serial; ?></td>
									<td>
										<input type="hidden" name="edit_id<?php echo $super_admin_serial; ?>" value="<?php echo requestionHistoryHtml($row_super_admin_product["id"]); ?>">
										<select name="product_id<?php echo $super_admin_serial; ?>" class="form-control select2 super-admin-product-select" style="width:100%;" required>
											<option value="">Select Product</option>
											<?php foreach($requestion_products as $product_edit){ ?>
											<option value="<?php echo requestionHistoryHtml($product_edit["id"]); ?>" <?php if($product_edit["id"]==$row_super_admin_product["product_id"]){ echo "selected"; } ?>><?php echo requestionHistoryHtml($product_edit["name"]); ?><?php if(!empty($product_edit["code"])){ echo " - ".requestionHistoryHtml($product_edit["code"]); } ?></option>
											<?php } ?>
										</select>
									</td>
									<td><input type="text" name="detail<?php echo $super_admin_serial; ?>" class="form-control" value="<?php echo requestionHistoryHtml($row_super_admin_product["detail"]); ?>"></td>
									<td><input type="number" step="any" name="requestion_quantity<?php echo $super_admin_serial; ?>" class="form-control calc-field req-qty" value="<?php echo requestionHistoryHtml($row_super_admin_product["requestion_quantity"]); ?>"></td>
										<td><input type="number" step="any" name="requistion_rate<?php echo $super_admin_serial; ?>" class="form-control calc-field req-rate super-admin-rate-input" data-original-rate="<?php echo requestionHistoryHtml($row_super_admin_product["requistion_rate"]); ?>" value="<?php echo requestionHistoryHtml($row_super_admin_product["requistion_rate"]); ?>"></td>
									<td><input type="number" step="any" name="requestion_amount<?php echo $super_admin_serial; ?>" class="form-control req-amount" value="<?php echo requestionHistoryHtml($row_requestion_amount); ?>"></td>
									<td><input type="number" step="any" name="final_quantity<?php echo $super_admin_serial; ?>" class="form-control calc-field final-qty" value="<?php echo requestionHistoryHtml($row_super_admin_product["final_quantity"]); ?>"></td>
										<td><input type="number" step="any" name="final_rate<?php echo $super_admin_serial; ?>" class="form-control calc-field final-rate super-admin-rate-input" data-original-rate="<?php echo requestionHistoryHtml($row_super_admin_product["final_rate"]); ?>" value="<?php echo requestionHistoryHtml($row_super_admin_product["final_rate"]); ?>"></td>
									<td><input type="number" step="any" name="final_amount<?php echo $super_admin_serial; ?>" class="form-control final-amount" value="<?php echo requestionHistoryHtml($row_final_amount); ?>"></td>
									<td><input type="text" name="comment<?php echo $super_admin_serial; ?>" class="form-control" value="<?php echo requestionHistoryHtml($row_super_admin_product["comment"]); ?>"></td>
									<td class="text-center">
										<div class="icheck-danger">
											<input type="checkbox" name="delete_detail[]" value="<?php echo requestionHistoryHtml($row_super_admin_product["id"]); ?>" id="delete_detail_<?php echo $super_admin_serial; ?>">
											<label for="delete_detail_<?php echo $super_admin_serial; ?>"></label>
										</div>
									</td>
								</tr>
								<?php $super_admin_serial++; } ?>
							</tbody>
						</table>
						</div>
						<div class="form-group mt-3">
							<label for="super_admin_rate_attachment">Rate Change Proof</label>
							<input type="file" class="form-control" name="super_admin_rate_attachment[]" id="super_admin_rate_attachment" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" multiple>
							<small class="form-text text-muted">Request অথবা final rate পরিবর্তন করলে proof file বাধ্যতামূলক।</small>
						</div>
						<input type="hidden" name="super_admin_form_complete" value="1">
						<div class="d-flex justify-content-between align-items-center">
						<button type="button" class="btn btn-outline-primary" id="addSuperAdminProduct"><i class="fas fa-plus"></i> Add Product</button>
						<button type="submit" class="btn btn-primary" onclick="return confirm('Save requisition changes?');"><i class="fas fa-save"></i> Save Changes</button>
					</div>
				</form>
			</div>
			<?php } ?>





		<div class="container requisition-document-wrap">
					    <div class="row my-info-area requisition-paper">
							<?php include("PrintTitle.php"); ?>
							<div class="col-xl-12 title-div" style="text-align:center;font-weight:bold;">
								<?php echo nl2br($rowdata["note"]);  ?>
							</div>

							<div class="col-xl-12 details-div-area">
								<table width="100%">


									<tbody>
									<tr>
										<td width="15%">Project Name</td>
										<td width="85%" class="data-row" colspan="3">: &nbsp;<?php echo $rowdata["project_name"]; ?></td>

									</tr>


										<tr>
										<td width="15%">Requisition No</td>
										<td width="35%" class="data-row">: &nbsp;<?php echo date("Y"); ?> / <?php echo $rowdata["invoice_id"]; if(!empty($rowdata["project_serial_no"])){ ?>/<?php echo $rowdata["project_serial_no"]; } ?></td>
										<td width="10%">Created by</td>
										<td width="32%" class="data-row">: &nbsp;<?php echo $rowdata["name"]; ; ?></td>
									</tr>


	                                    <tr>
											<td width="15%"> Date</td>
											<td width="35%" class="data-row">: &nbsp;<?php echo date("d-m-Y", strtotime($rowdata["date"])); ?></td>
											<td width="10%">Purchase Order</td>
											<td width="32%" class="data-row">: &nbsp;
												<?php if ($requisitionPurchaseOrders) {
													$purchaseOrderLinks = array();
													foreach ($requisitionPurchaseOrders as $requisitionPurchaseOrder) {
														$purchaseOrderLinks[] = '<a href="?Purchase_HistoryDetail/' . urlencode($requisitionPurchaseOrder['invoice_id']) . '"><strong>' . requestionHistoryHtml($requisitionPurchaseOrder['purchase_id']) . '</strong></a>';
													}
													echo implode(', ', $purchaseOrderLinks);
												} else { echo 'Not purchased yet'; } ?>
											</td>
										</tr>

								</tbody></table>




							</div>

			<?php if(!empty($rowdataApproval_check["employee_id"]) && $rowdataApproval_check["employee_id"]==$LoginReGiSterSession){ ?>

			<div class="col-sm-12">
								<div class="row">

								<form method="post" action="" enctype="multipart/form-data" style="width:100%;">
		<input type="hidden" name="requestion_approval_csrf" value="<?php echo requestionHistoryHtml(requestionApprovalCsrfToken()); ?>">

		<input type="hidden" name="invoice_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $Detail; ?>" ><input type="hidden" name="project_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["project_id"]; ?>" >

<input type="hidden" name="store_id"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["store_id"]; ?>" >
<input type="hidden" name="requistion_type"  placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdata["requistion_type"]; ?>" >
        <!-- SELECT2 EXAMPLE -->
        <div class="">
          <div class="row">
  <div class="col-sm-12">
  <p>Requisition Detail :<span style="color:#FF0000">*</span></p>
    <div class="approval-table-scroll">
	<?php if(!empty($rowdata["requistion_type"]) && $rowdata["requistion_type"]=='Fund'){ ?>

<table id="myTable" class="table order-list requisition-approval-table fund-approval-table">
	<colgroup>
		<col class="col-sl">
		<col class="col-description">
		<col class="col-amount">
		<col class="col-qty">
		<col class="col-rate">
		<col class="col-approval">
		<col class="col-remarks">
	</colgroup>
    <thead>
        <tr>

			<th class="serial-cell">SL</th>
			<th>Description</th>
            <th>Requisition Amount(Tk)</th>
            <th>Qty</th>
            <th>Rate</th>
			<th>Approval Amount (Tk)</th>
			<th>Remarks</th>
        </tr>
    </thead>
    <tbody>

		<?php
		$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name,product_information.unit AS product_unit   FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='".$Detail."' and requestion_detail.deleted_at is NULL");
			$serial=1;
		$total_requestion_amount=0;
		$total_final_amount=0;
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){
         $total_requestion_amount+=$rowdataProduct_Detail["requestion_amount"];
		$total_final_amount+=$rowdataProduct_Detail["final_amount"];

				?>

				<script>

       function calculate<?php echo $serial; ?>() {
       var quantity = document.getElementById('quantity<?php echo $serial; ?>').value;
       var rate = document.getElementById('rate<?php echo $serial; ?>').value;

       var total_amount<?php echo $serial; ?> = document.getElementById('total_amount<?php echo $serial; ?>');
       var i = Math.round(Number(quantity) * Number(rate)) ;

       total_amount<?php echo $serial; ?>.value = i;


       }

		</script>
        <tr>

			   <td class="serial-cell"><?php echo $serial; ?></td>
			   <td class="item-name"><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/>

				    <input name="product_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_id"]; ?>" >

				   <input name="edit_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["id"]; ?>" readonly>

				   <input class="form-control " placeholder="Quantity Here ...."   name="requestion_quantity<?php echo $serial; ?>" id="requestion_quantity<?php echo $serial; ?>" type="hidden" required  value="<?php echo $rowdataProduct_Detail["requestion_quantity"]; ?>" >
				   <input class="form-control " placeholder="Quantity Here ...."   name="requistion_rate<?php echo $serial; ?>" id="requistion_rate<?php echo $serial; ?>" type="hidden" required  value="<?php echo $rowdataProduct_Detail["requistion_rate"]; ?>" >

				   <input name="name<?php echo $serial; ?>" type="hidden" id="username_1" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_name"]; ?>" readonly><?php echo $rowdataProduct_Detail["product_name"]; if(!empty($rowdataProduct_Detail["detail"])){ echo " ( ".$rowdataProduct_Detail["detail"]." ) "; } ?>
            </td>
            <td class="number-cell">
			<input class="form-control " placeholder="Amount Here ...."   name="requestion_amount<?php echo $serial; ?>" id="requestion_amount<?php echo $serial; ?>" type="text" required  value="<?php echo $rowdataProduct_Detail["requestion_amount"]; ?>" readonly>
			</td>
		   <td>
                <input type="text" id="quantity<?php echo $serial; ?>" name="quantity<?php echo $serial; ?>" value="<?php if(!empty($rowdataProduct_Detail["final_quantity"])){ echo $rowdataProduct_Detail["final_quantity"]; }else{ echo $rowdataProduct_Detail["requestion_quantity"]; } ?>"  class="form-control "  onkeyup="get_total_vaue();" oninput="calculate<?php echo $serial; ?>()"  placeholder="Quantity Here .." required/>
            </td>
            <td>
	                <?php $effectiveFundRate=($rowdataProduct_Detail["final_rate"]!==null && $rowdataProduct_Detail["final_rate"]!=='') ? $rowdataProduct_Detail["final_rate"] : $rowdataProduct_Detail["requistion_rate"]; ?>
	                <input type="text" id="rate<?php echo $serial; ?>" name="rate<?php echo $serial; ?>" value="<?php echo requestionHistoryHtml($effectiveFundRate); ?>" data-original-rate="<?php echo requestionHistoryHtml($effectiveFundRate); ?>" class="form-control approval-rate-input" onkeyup="get_total_vaue();" oninput="calculate<?php echo $serial; ?>()" placeholder="Rate Here .." required/>
            </td>

            <td> <input class="form-control cm_cls"  placeholder="Approval Amount Here ...." id="total_amount<?php echo $serial; ?>"  value="<?php echo $rowdataProduct_Detail["final_amount"]; ?>" name="final_amount<?php echo $serial; ?>" type="text" required ></td>

	<td class="remarks-cell"><?php echo $rowdataProduct_Detail["comment"]; ?></td>
        </tr>
		<?php
			$serial++;
			} ?>

    </tbody>

   <tfoot>

        <tr>


              <td colspan="2" style="text-align:right;">
			Total Amount
            </td>
		   <td><strong><?php echo $total_requestion_amount; ?></strong></td>
		   <td colspan="2"></td>
            <td>
                <input type="text" id="totalPrice" class="form-control" placeholder="Amount Here .." value="<?php echo $total_final_amount; ?>" required readonly/>
            </td>
		<td></td>

        </tr>

        <tr>
		   <td colspan="5" style="text-align:right;"><strong>Cash in Hand</strong></td>
            <td>
             <strong><?php if(!empty($rowdata["previous_cash_in_hand"])){ echo $rowdata["previous_cash_in_hand"]; }else{ echo "0";} ?></strong>
            </td>
		<td></td>

        </tr>






    </tfoot>

</table>

<script>

	 function get_total_vaue() {
		var sum = 0;
		$('.cm_cls').each(function() {
        sum += Number($(this).val());
		$('#totalPrice').val(sum);
		$('#subTotal').val(sum);
		$('#duePrice').val(sum);
    });
	 }
</script>
	<?php }else{ ?>
	<table id="myTable" class="table order-list requisition-approval-table material-approval-table">
	<colgroup>
		<col class="col-sl">
		<col class="col-name">
		<col class="col-unit">
		<col class="col-stock">
		<col class="col-qty">
		<col class="col-qty">
		<col class="col-rate">
		<col class="col-remarks">
	</colgroup>
    <thead>
        <tr>

			<th class="serial-cell">SL</th>
			<th>Name</th>
			<th>Unit</th>
			<th>Available Qty</th>
            <th>Requisition Qty</th>
			<th>Approval Qty</th>
			<th>Rate</th>
			<th>Remarks</th>
        </tr>
    </thead>
    <tbody>

		<?php
		$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name,product_information.unit AS product_unit   FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='".$Detail."' and requestion_detail.deleted_at is NULL");
			$serial=1;
        while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){

         $Stock_Detail = $pdo->query("SELECT SUM(stock) AS total_stock  FROM `stock_information`  where  product_id='".$rowdataProduct_Detail["product_id"]."'  and stock_information.deleted_at is NULL");
		$rowdataStock_Detail = $Stock_Detail->fetch();

				?>
        <tr>

			   <td class="serial-cell"><?php echo $serial; ?></td>
			   <td class="item-name"><input type="hidden" name="number_count" class="form-control"  value="<?php echo $serial; ?>"/>

				    <input name="product_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class=" form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_id"]; ?>" >

				   <input name="edit_id<?php echo $serial; ?>"  type="hidden" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["id"]; ?>" readonly>

				   <input name="name<?php echo $serial; ?>" type="hidden" id="username_1" placeholder="Name Here"  class="username form-control" style="width:100%;" required="required" value="<?php echo $rowdataProduct_Detail["product_name"]; ?>" readonly><?php echo $rowdataProduct_Detail["product_name"]; ?>
            </td>

         <td class="unit-cell"><?php echo $rowdataProduct_Detail["product_unit"]; ?></td>
         <td class="number-cell"><?php echo $rowdataStock_Detail["total_stock"]; ?></td>
			<td>
			<input class="form-control " placeholder="Quantity Here ...."   name="requestion_quantity<?php echo $serial; ?>" id="requestion_quantity<?php echo $serial; ?>" type="text" required  value="<?php echo $rowdataProduct_Detail["requestion_quantity"]; ?>" readonly>
			</td>
            <td> <input class="form-control " placeholder="Approval Quantity Here ...." id="final_quantity<?php echo $serial; ?>"  value="<?php echo $rowdataProduct_Detail["final_quantity"]; ?>" name="final_quantity<?php echo $serial; ?>" type="text" required ></td>
	             <?php $effectiveMaterialRate=($rowdataProduct_Detail["final_rate"]!==null && $rowdataProduct_Detail["final_rate"]!=='') ? $rowdataProduct_Detail["final_rate"] : $rowdataProduct_Detail["requistion_rate"]; ?>
	             <td> <input class="form-control approval-rate-input" placeholder="Rate Here ...." id="final_rate<?php echo $serial; ?>" value="<?php echo requestionHistoryHtml($effectiveMaterialRate); ?>" data-original-rate="<?php echo requestionHistoryHtml($effectiveMaterialRate); ?>" name="final_rate<?php echo $serial; ?>" type="text"></td>
           <td class="remarks-cell"><?php echo $rowdataProduct_Detail["comment"]; ?></td>


        </tr>
		<?php
			$serial++;
			} ?>

    </tbody>



</table>


	<?php } ?>



 </div>
</div>

			<?php if(!empty($rateProofFiles)){ ?>
			<div class="rate-proof-grid no-print d-print-none">
				<div class="rate-proof-title">রেট প্রুফ ফাইল</div>
				<?php foreach($rateProofFiles as $rateProofFile){ 
					$proofType=requestionRateProofType($rateProofFile["file_name"]);
					$proofUrl=requestionRateProofUrl($rateProofFile["file_name"]);
					$proofExists=requestionRateProofExists($rateProofFile["file_name"]);
				?>
					<div class="rate-proof-card">
						<div class="rate-proof-preview">
							<?php if(!$proofExists){ ?>
								<div class="rate-proof-file-icon"><i class="fa fa-exclamation-triangle"></i></div>
							<?php }else if($proofType==='image'){ ?>
								<a href="<?php echo $proofUrl; ?>" target="_blank"><img src="<?php echo $proofUrl; ?>" alt="Rate proof"></a>
							<?php }else if($proofType==='pdf'){ ?>
								<iframe src="<?php echo $proofUrl; ?>#toolbar=0"></iframe>
							<?php }else{ ?>
								<a class="rate-proof-file-icon" href="<?php echo $proofUrl; ?>" target="_blank"><i class="fa fa-file"></i></a>
							<?php } ?>
						</div>
						<div class="rate-proof-info">
							<div class="rate-proof-meta">
								<strong><?php echo requestionHistoryHtml(!empty($rateProofFile["approver_name"]) ? $rateProofFile["approver_name"] : 'Approver'); ?></strong>
								<?php if(!empty($rateProofFile["created_at"])){ ?><br><?php echo requestionHistoryHtml($rateProofFile["created_at"]); ?><?php } ?>
							</div>
							<div class="rate-proof-name"><?php echo requestionHistoryHtml($rateProofFile["file_name"]); ?></div>
							<?php if($proofExists){ ?>
							<a class="btn btn-sm btn-success mt-2" href="download.php?path=RequistionAttachment/&download_file=<?php echo urlencode($rateProofFile["file_name"]); ?>"><i class="fa fa-download"></i> Download</a>
							<?php }else{ ?>
							<span class="badge badge-warning mt-2">File missing</span>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			</div>
			<?php } ?>

			<?php if(!empty($approvalNoteRows)){ ?>
			<div class="approval-note-grid">
				<div class="approval-note-list">
					<div class="approval-note-title">Approval Notes</div>
					<?php foreach($approvalNoteRows as $approvalNoteRow){ ?>
					<div class="approval-note-row">
						<div class="approval-note-meta">
							<strong><?php echo requestionHistoryHtml($approvalNoteRow["from_name"]); ?></strong>
							<?php echo requestionHistoryHtml($approvalNoteRow["action"]); ?><?php if(!empty($approvalNoteRow["to_name"])){ ?> to <?php echo requestionHistoryHtml($approvalNoteRow["to_name"]); ?><?php } ?>
							<?php if(!empty($approvalNoteRow["time"])){ ?><br><?php echo requestionHistoryHtml($approvalNoteRow["time"]); ?><?php } ?>
						</div>
						<div class="approval-note-text"><?php echo nl2br(requestionHistoryHtml($approvalNoteRow["note"])); ?></div>
					</div>
					<?php } ?>
				</div>
			</div>
			<?php } ?>



 <div class="col-sm-12 approval-action-panel">
        <div class="form-group">
            <label for="note">Comment / Note</label>
            <textarea class="form-control"  placeholder="Note Here ...." name="note" id="note" type="text" ></textarea>
        </div>
    </div>
 <div class="col-sm-12 approval-action-panel">
        <div class="form-group">
            <label for="rate_attachment">Rate Proof Attachment</label>
	            <input class="form-control" name="rate_attachment[]" id="rate_attachment" type="file" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx" multiple>
	            <small class="form-text text-muted">Rate পরিবর্তন করলে এক বা একাধিক proof file অবশ্যই upload করতে হবে।</small>
	        </div>
	    </div>
	<script>
	document.addEventListener('DOMContentLoaded', function(){
		var proofInput=document.getElementById('rate_attachment');
		if(!proofInput || !proofInput.form){ return; }
		proofInput.form.addEventListener('submit', function(event){
			var submitter=event.submitter;
			if(!submitter || submitter.value!=='recommend'){ return; }
			var changed=Array.prototype.some.call(document.querySelectorAll('.approval-rate-input'), function(input){
				var original=parseFloat(input.getAttribute('data-original-rate') || '0');
				var current=parseFloat(input.value || '0');
				return Math.abs(original-current)>0.000001;
			});
			if(changed && (!proofInput.files || proofInput.files.length===0)){
				event.preventDefault();
				if(typeof appShowAlert==='function'){
					appShowAlert('Rate পরিবর্তন করলে proof file upload করা বাধ্যতামূলক।','warning','গুরুত্বপূর্ণ সতর্কতা');
				}else{
					alert('Rate পরিবর্তন করলে proof file upload করা বাধ্যতামূলক।');
				}
			}
		});
	});
	</script>

<div class="col-sm-12 approval-action-panel">
	<div class="row">
		<?php if((int)$LoginReGiSterSession!==1){ ?>
		<div class="col-md-6">
			<div class="form-group">
				<label for="forward_employee_id">Recommend / Forward To</label>
				<select name="forward_employee_id" id="forward_employee_id" class="form-control select2" style="width:100%;">
					<option value="">Select Employee</option>
					<?php
					foreach($forwardEmployeeRows as $rowForwardEmployeeInfo){
					?>
					<option value="<?php echo $rowForwardEmployeeInfo["id"]; ?>"><?php echo $rowForwardEmployeeInfo["name_en"]; ?><?php if(!empty($rowForwardEmployeeInfo["designation_name"])){ echo " - ".$rowForwardEmployeeInfo["designation_name"]; } ?></option>
					<?php } ?>
				</select>
				<?php if(empty($forwardEmployeeRows)){ ?><small class="form-text text-muted">No unsigned next approver remains in this path.</small><?php } ?>
			</div>
		</div>
		<?php } ?>
		<div class="<?php echo ((int)$LoginReGiSterSession!==1) ? 'col-md-6' : 'col-md-12'; ?>">
			<div class="form-group">
				<label for="return_employee_id">Return To</label>
				<select name="return_employee_id" id="return_employee_id" class="form-control select2" style="width:100%;">
					<option value="">Select Employee</option>
					<?php
					foreach($returnEmployeeRows as $rowReturnEmployeeInfo){
					?>
					<option value="<?php echo $rowReturnEmployeeInfo["id"]; ?>"><?php echo $rowReturnEmployeeInfo["name_en"]; ?><?php if(!empty($rowReturnEmployeeInfo["designation_name"])){ echo " - ".$rowReturnEmployeeInfo["designation_name"]; } ?></option>
					<?php } ?>
				</select>
				<?php if(empty($returnEmployeeRows)){ ?><small class="form-text text-muted">No previous signer is available for return.</small><?php } ?>
			</div>
		</div>
	</div>
</div>

<div class="col-sm-12">
<div class="signature-grid">


								<?php $Staff_information_signature = $pdo->prepare("SELECT employee_information.*,hr_designation.name AS designation_name,project_material_aproval_status.approval_date AS approval_date FROM project_material_aproval_status INNER JOIN employee_information ON project_material_aproval_status.employee_id=employee_information.id INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where  project_material_aproval_status.invoice_id=:invoice_id and project_material_aproval_status.project_id=:project_id and approval_status='Approve' and project_material_aproval_status.approval_id is not NULL ORDER BY COALESCE(STR_TO_DATE(project_material_aproval_status.approval_time, '%Y-%m-%d %h:%i:%s%p'), STR_TO_DATE(project_material_aproval_status.approval_date, '%Y-%m-%d'), project_material_aproval_status.updated_at) ASC, project_material_aproval_status.id ASC");
                    $Staff_information_signature->execute(array(':invoice_id'=>$Detail,':project_id'=>$rowdata["project_id"]));
                    while($rowdataStaff_information_signature = $Staff_information_signature->fetch()){
							?>
							<div class="signature-card">
							<?php if(!empty($rowdataStaff_information_signature["signature"])){ ?>
							<img src="Signature/<?php echo $rowdataStaff_information_signature["signature"]; ?>">

								<?php } ?>
								<p class="signature-date"><?php echo date("d/m/Y", strtotime($rowdataStaff_information_signature["approval_date"])); ?></p>
							<p class="signature-name"><?php echo $rowdataStaff_information_signature["name_en"]; ?></p>
							<p class="signature-designation"><?php echo $rowdataStaff_information_signature["designation_name"]; ?></p>
							</div>
							<?php } ?>


						</div>




		</div>






  </div>

	          <!-- /.card-body -->
	          <div class="card-footer">
	          <div class="box-tools pull-right approval-action-panel">
	       <button type="button" class="btn btn-warning" onclick="history.back(-1)"><i class="fa fa-window-close"></i> Cancel</button>
	       <?php if(!empty($rowdata["requistion_type"]) && $rowdata["requistion_type"]=='Fund'){ ?>
	           <button class="btn btn-danger" type="submit" name="Product_requistion_Fund_approval_Start" value="reject"><i class="fa fa-times"></i> Reject</button>
           <button class="btn btn-warning" type="submit" name="Product_requistion_Fund_approval_Start" value="return"><i class="fa fa-reply"></i> Return</button>
           <button class="btn btn-primary" type="submit" name="Product_requistion_Fund_approval_Start" value="recommend"> <i class="fa fa-share"></i><?php if((int)$LoginReGiSterSession===1) { echo " Approve"; }else{ echo " Recommended"; } ?> </button>
            <?php }else{ ?>
            <button class="btn btn-danger" type="submit" name="Product_requistion_approval_Start" value="reject"><i class="fa fa-times"></i> Reject</button>
            <button class="btn btn-warning" type="submit" name="Product_requistion_approval_Start" value="return"><i class="fa fa-reply"></i> Return</button>
            <button class="btn btn-primary" type="submit" name="Product_requistion_approval_Start" value="recommend"> <i class="fa fa-share"></i> <?php if((int)$LoginReGiSterSession===1) { echo "Approve"; }else{ echo "Recommended"; } ?> </button>
            <?php } ?>

                </div>
          </div>
        </div>
        <!-- /.card -->
</form>




								</div>




							</div>


					<?php }else{ ?>

                         <div class="col-xl-12 table-div-area"><br>
        <?php if(!empty($rowdata["requistion_type"]) && $rowdata["requistion_type"]=='Fund'){ ?>

			<table style="width: 100%;">
					<tr>
						<th>SL</th>
						 <th>Description</th>
						 <th >Qty</th>
						 <th >Rate&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
						 <th>Amount(Tk)</th>
						<?php if(!empty($rowdata["approval_status"]) && $rowdata["approval_status"]=='Approve'){ ?>
						<th>Distribution Amount</th>
						<th>Due Amount</th>
						<?php } ?>
						<th>Remarks</th>
						</tr>
					   <?php

			$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name,product_information.unit AS product_unit  FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='$Detail' and requestion_detail.deleted_at is NULL");
			$serial=1;
			$amount_sum=0;
			$received_amount_total=0;
			$due_amount_total=0;
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){

			$amount_sum+=$rowdataProduct_Detail["final_amount"];

			if(!empty($rowdata["approval_status"]) && $rowdata["approval_status"]=='Approve'){
			$received_amount_total+=$rowdataProduct_Detail["received_amount"];
			$due_amount_total+=$rowdataProduct_Detail["due_amount"];

			}



						?>

                        <tr>
                    <td text align="center"><?php echo $serial; ?></td>
					<td text align="left" style="padding-left: 10px;"><?php if(!empty($rowdataProduct_Detail["product_name"])){ echo $rowdataProduct_Detail["product_name"]; } if(!empty($rowdataProduct_Detail["detail"])){ echo " ( ".$rowdataProduct_Detail["detail"]." ) "; } ?></td>
					<td text align="center"><?php if(!empty($rowdataProduct_Detail["final_quantity"])){ echo $rowdataProduct_Detail["final_quantity"]; }else{ echo $rowdataProduct_Detail["requestion_quantity"]; } ?></td>
					<td text align="center"><?php if(!empty($rowdataProduct_Detail["final_rate"])){ echo $rowdataProduct_Detail["final_rate"]; }else{ echo $rowdataProduct_Detail["requistion_rate"]; } ?></td>

					<td text align="center"><?php echo $rowdataProduct_Detail["final_amount"]; ?></td>

					<?php if(!empty($rowdata["approval_status"]) && $rowdata["approval_status"]=='Approve'){ ?>
						<td text align="center"><?php echo $rowdataProduct_Detail["received_amount"]; ?></td>
						<td text align="center"><?php echo $rowdataProduct_Detail["due_amount"]; ?></td>
						<?php } ?>
						<td style="text-align:center;"><?php echo $rowdataProduct_Detail["comment"]; ?></td>
                        </tr>


                        <?php
                        $serial++;
                        } ?>
					<tr>

					 <td colspan="4" class="fund-total-label">Total&nbsp;:&nbsp;&nbsp;</td>
					 <td class="fund-total-amount"><strong><?php echo $amount_sum; ?></strong></td>
					 <?php if(!empty($rowdata["approval_status"]) && $rowdata["approval_status"]=='Approve'){ ?>
					 <td class="fund-total-amount"><strong><?php echo $received_amount_total; ?></strong></td>
					 <td class="fund-total-amount"><strong><?php echo $due_amount_total; ?></strong></td>
					 <?php } ?>
					  <td ></td>
					</tr>

					<tr>
					 <td colspan="6">&nbsp;&nbsp;&nbsp;&nbsp;

   <?php
  if(!empty($amount_sum)){
   function convertNumberToWordsForIndia($number){
    //A function to convert numbers into Indian readable words with Cores, Lakhs and Thousands.
    $words = array(
    '0'=> '' ,'1'=> 'one' ,'2'=> 'two' ,'3' => 'three','4' => 'four','5' => 'five',
    '6' => 'six','7' => 'seven','8' => 'eight','9' => 'nine','10' => 'ten',
    '11' => 'eleven','12' => 'twelve','13' => 'thirteen','14' => 'fouteen','15' => 'fifteen',
    '16' => 'sixteen','17' => 'seventeen','18' => 'eighteen','19' => 'nineteen','20' => 'twenty',
    '30' => 'thirty','40' => 'fourty','50' => 'fifty','60' => 'sixty','70' => 'seventy',
    '80' => 'eighty','90' => 'ninty');

    //First find the length of the number
    $number_length = strlen($number);
    //Initialize an empty array
    $number_array = array(0,0,0,0,0,0,0,0,0);
    $received_number_array = array();

    //Store all received numbers into an array
    for($i=0;$i<$number_length;$i++){
        $received_number_array[$i] = substr($number,$i,1);
    }

    //Populate the empty array with the numbers received - most critical operation
    for($i=9-$number_length,$j=0;$i<9;$i++,$j++){
        $number_array[$i] = $received_number_array[$j];
    }

    $number_to_words_string = "";
    //Finding out whether it is teen ? and then multiply by 10, example 17 is seventeen, so if 1 is preceeded with 7 multiply 1 by 10 and add 7 to it.
    for($i=0,$j=1;$i<9;$i++,$j++){
        //"01,23,45,6,78"
        //"00,10,06,7,42"
        //"00,01,90,0,00"
        if($i==0 || $i==2 || $i==4 || $i==7){
            if($number_array[$j]==0 || $number_array[$i] == "1"){
                $number_array[$j] = intval($number_array[$i])*10+$number_array[$j];
                $number_array[$i] = 0;
            }

        }
    }

    $value = "";
    for($i=0;$i<9;$i++){
        if($i==0 || $i==2 || $i==4 || $i==7){
            $value = $number_array[$i]*10;
        }
        else{
            $value = $number_array[$i];
        }
        if($value!=0)         {    $number_to_words_string.= $words["$value"]." "; }
        if($i==1 && $value!=0){    $number_to_words_string.= "Crores "; }
        if($i==3 && $value!=0){    $number_to_words_string.= "Lakhs ";    }
        if($i==5 && $value!=0){    $number_to_words_string.= "Thousand "; }
        if($i==6 && $value!=0){    $number_to_words_string.= "Hundred "; }

    }
    if($number_length>9){ $number_to_words_string = "Sorry This does not support more than 99 Crores"; }
    return ucwords(strtolower($number_to_words_string)." Only.");
}


  echo "<b>In words(Tk) &nbsp;:&nbsp;</b>".convertNumberToWordsForIndia($amount_sum);
}
?>  </td>
					</tr>

			 <tr>
		   <td colspan="4" style="text-align:right;"><strong>Cash in Hand&nbsp;:&nbsp;</strong></td>
            <td style="text-align:center;">
             <strong><?php if(!empty($rowdata["previous_cash_in_hand"])){ echo $rowdata["previous_cash_in_hand"]; }else{ echo "0";} ?></strong>
            </td>
		<td></td>

        </tr>

						</table>

					<?php }else{ ?>
					<?php
					$hasEmergencyIssued=false;
					$emergencyIssuedCheck=$pdo->prepare("SELECT COALESCE(SUM(emergency_quantity),0) AS total_emergency_quantity FROM requestion_detail WHERE invoice_id=:invoice_id AND deleted_at IS NULL");
					$emergencyIssuedCheck->execute(array(':invoice_id'=>$Detail));
					$emergencyIssuedRow=$emergencyIssuedCheck->fetch();
					if(!empty($emergencyIssuedRow) && (float)$emergencyIssuedRow["total_emergency_quantity"]>0){
						$hasEmergencyIssued=true;
					}
					$isApprovedRequisition=!empty($rowdata["approval_status"]) && $rowdata["approval_status"]=='Approve';
					$totalAmountLabelColspan=4+($hasEmergencyIssued ? 1 : 0)+($isApprovedRequisition ? 2 : 0);
					?>
					<table class="requisition-items-table material-items" style="width: 100%;">
					<colgroup>
						<col class="col-sl">
						<col class="col-name">
						<col class="col-total">
						<?php if($hasEmergencyIssued){ ?>
						<col class="col-emergency">
						<?php } ?>
						<?php if($isApprovedRequisition){ ?>
						<col class="col-distribution">
						<col class="col-due">
						<?php } ?>
						<col class="col-rate">
						<col class="col-amount">
						<col class="col-remarks">
					</colgroup>
					<tr>
						<th>SL</th>
						 <th>Name</th>
						 <th>Total Quantity</th>
						<?php if($hasEmergencyIssued){ ?>
						 <th>Emergency Issued</th>
						<?php } ?>
						<?php if($isApprovedRequisition){ ?>
						<th>Distribution Quantity</th>
						<th>Due Quantity</th>
						<?php } ?>
						 <th>Rate</th>
						  <th>Amount</th>
						<th>Remarks</th>
						</tr>
					   <?php

			$informationProduct_Detail = $pdo->query("SELECT requestion_detail.*,product_information.name AS product_name,product_information.unit AS product_unit  FROM `requestion_detail` INNER JOIN product_information ON requestion_detail.product_id=product_information.id where  requestion_detail.invoice_id='$Detail' and requestion_detail.deleted_at is NULL");
			$serial=1;
			$amount_sum=0;
            while ($rowdataProduct_Detail = $informationProduct_Detail->fetch()){

			if(!empty($rowdataProduct_Detail["final_rate"]) && !empty($rowdataProduct_Detail["final_quantity"])){
			    $amount_sum+=$rowdataProduct_Detail["final_rate"]*$rowdataProduct_Detail["final_quantity"]; }else if(!empty($rowdataProduct_Detail["final_rate"]) && !empty($rowdataProduct_Detail["requestion_quantity"])){
			    $amount_sum+=$rowdataProduct_Detail["final_rate"]*$rowdataProduct_Detail["requestion_quantity"]; }

						?>

                        <tr>
                    <td text align="center"><?php echo $serial; ?></td>
					<td text align="left" style="padding-left: 10px;"><?php if(!empty($rowdataProduct_Detail["product_name"])){ echo $rowdataProduct_Detail["product_name"]; }  ?></td>


					<td text align="center"><?php echo $rowdataProduct_Detail["final_quantity"]; ?> <?php echo $rowdataProduct_Detail["product_unit"]; ?></td>
					<?php if($hasEmergencyIssued){ ?>
					<td text align="center"><?php echo (float)$rowdataProduct_Detail["emergency_quantity"]>0 ? $rowdataProduct_Detail["emergency_quantity"].' '.$rowdataProduct_Detail["product_unit"] : '-'; ?></td>
					<?php } ?>

					<?php if($isApprovedRequisition){ ?>
						<td text align="center"><?php echo $rowdataProduct_Detail["distribution_quantity"]; ?></td>
						<td text align="center"><?php echo $rowdataProduct_Detail["due_quantity"]; ?></td>
						<?php } ?>

						<td text align="center"><?php echo $rowdataProduct_Detail["final_rate"]; ?></td>
					<td ><?php if(!empty($rowdataProduct_Detail["final_rate"]) && !empty($rowdataProduct_Detail["final_quantity"])){ echo $rowdataProduct_Detail["final_rate"]*$rowdataProduct_Detail["final_quantity"]; } else if(!empty($rowdataProduct_Detail["final_rate"]) && !empty($rowdataProduct_Detail["requestion_quantity"])){ echo $rowdataProduct_Detail["final_rate"]*$rowdataProduct_Detail["requestion_quantity"]; } ?></td>
					<td ><?php echo $rowdataProduct_Detail["comment"]; ?></td>
                        </tr>


                        <?php
                        $serial++;
                        } ?>

						<?php if(!empty($amount_sum) && $amount_sum>0){ ?>
					<tr>
					 <td colspan="<?php echo $totalAmountLabelColspan; ?>" class="total-amount-label"> Total Amount&nbsp;:&nbsp;</td>
					 <td text align="center"><b><?php echo $amount_sum; ?></b></td>
					 <td></td>
					</tr>
					<?php } ?>

						</table>
					<?php } ?>
								</div>
							<?php if(!empty($rateProofFiles)){ ?>
							<div class="rate-proof-grid no-print d-print-none">
								<div class="rate-proof-title">রেট প্রুফ ফাইল</div>
								<?php foreach($rateProofFiles as $rateProofFile){ 
									$proofType=requestionRateProofType($rateProofFile["file_name"]);
									$proofUrl=requestionRateProofUrl($rateProofFile["file_name"]);
									$proofExists=requestionRateProofExists($rateProofFile["file_name"]);
								?>
									<div class="rate-proof-card">
										<div class="rate-proof-preview">
											<?php if(!$proofExists){ ?>
												<div class="rate-proof-file-icon"><i class="fa fa-exclamation-triangle"></i></div>
											<?php }else if($proofType==='image'){ ?>
												<a href="<?php echo $proofUrl; ?>" target="_blank"><img src="<?php echo $proofUrl; ?>" alt="Rate proof"></a>
											<?php }else if($proofType==='pdf'){ ?>
												<iframe src="<?php echo $proofUrl; ?>#toolbar=0"></iframe>
											<?php }else{ ?>
												<a class="rate-proof-file-icon" href="<?php echo $proofUrl; ?>" target="_blank"><i class="fa fa-file"></i></a>
											<?php } ?>
										</div>
										<div class="rate-proof-info">
											<div class="rate-proof-meta">
												<strong><?php echo requestionHistoryHtml(!empty($rateProofFile["approver_name"]) ? $rateProofFile["approver_name"] : 'Approver'); ?></strong>
												<?php if(!empty($rateProofFile["created_at"])){ ?><br><?php echo requestionHistoryHtml($rateProofFile["created_at"]); ?><?php } ?>
											</div>
											<div class="rate-proof-name"><?php echo requestionHistoryHtml($rateProofFile["file_name"]); ?></div>
											<?php if($proofExists){ ?>
											<a class="btn btn-sm btn-success mt-2" href="download.php?path=RequistionAttachment/&download_file=<?php echo urlencode($rateProofFile["file_name"]); ?>"><i class="fa fa-download"></i> Download</a>
											<?php }else{ ?>
											<span class="badge badge-warning mt-2">File missing</span>
											<?php } ?>
										</div>
									</div>
								<?php } ?>
							</div>
							<?php } ?>
							<?php if(!empty($approvalNoteRows)){ ?>
							<div class="approval-note-grid">
								<div class="approval-note-list">
									<div class="approval-note-title">Approval Notes</div>
									<?php foreach($approvalNoteRows as $approvalNoteRow){ ?>
									<div class="approval-note-row">
										<div class="approval-note-meta">
											<strong><?php echo requestionHistoryHtml($approvalNoteRow["from_name"]); ?></strong>
											<?php echo requestionHistoryHtml($approvalNoteRow["action"]); ?><?php if(!empty($approvalNoteRow["to_name"])){ ?> to <?php echo requestionHistoryHtml($approvalNoteRow["to_name"]); ?><?php } ?>
											<?php if(!empty($approvalNoteRow["time"])){ ?><br><?php echo requestionHistoryHtml($approvalNoteRow["time"]); ?><?php } ?>
										</div>
										<div class="approval-note-text"><?php echo nl2br(requestionHistoryHtml($approvalNoteRow["note"])); ?></div>
									</div>
									<?php } ?>
								</div>
							</div>
							<?php } ?>


<div class="col-sm-12">
<div class="signature-grid">


								<?php $Staff_information_signature = $pdo->query("SELECT employee_information.*,hr_designation.name AS designation_name,project_material_aproval_status.approval_date AS approval_date FROM project_material_aproval_status INNER JOIN employee_information ON project_material_aproval_status.employee_id=employee_information.id INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where  project_material_aproval_status.invoice_id='".$rowdata["invoice_id"]."' and project_material_aproval_status.project_id='".$rowdata["project_id"]."' and approval_status='Approve' and project_material_aproval_status.approval_id is not NULL ORDER BY COALESCE(STR_TO_DATE(project_material_aproval_status.approval_time, '%Y-%m-%d %h:%i:%s%p'), STR_TO_DATE(project_material_aproval_status.approval_date, '%Y-%m-%d'), project_material_aproval_status.updated_at) ASC, project_material_aproval_status.id ASC");
                    while($rowdataStaff_information_signature = $Staff_information_signature->fetch()){
							?>
							<div class="signature-card">
							<?php if(!empty($rowdataStaff_information_signature["signature"])){ ?>
							<img src="Signature/<?php echo $rowdataStaff_information_signature["signature"]; ?>">

								<?php } ?>
								<p class="signature-date"><?php echo date("d/m/Y", strtotime($rowdataStaff_information_signature["approval_date"])); ?></p>
							<p class="signature-name"><?php echo $rowdataStaff_information_signature["name_en"]; ?></p>
							<p class="signature-designation"><?php echo $rowdataStaff_information_signature["designation_name"]; ?></p>
							</div>
							<?php } ?>






					<?php } ?>

						</div>




		</div></div>
			</div>




<?php if(authIsSuperAdmin($LoginReGiSterSession) && !empty($requestionFullApprovalPathRows)){ ?>
	<div class="super-admin-path-panel no-print d-print-none">
		<div class="path-panel-head">
			<h4><i class="fas fa-route"></i> Full Approval Path</h4>
			<div class="path-name"><?php echo requestionHistoryHtml($requestionFullApprovalPathName); ?></div>
		</div>
		<div class="table-responsive">
			<table class="super-admin-path-table">
				<thead>
					<tr>
						<th style="width:70px;">Step</th>
						<th>Employee</th>
						<th>Designation</th>
						<th style="width:130px;">Status</th>
						<th style="width:170px;">Time</th>
						<th>Note</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($requestionFullApprovalPathRows as $pathRow){ 
						$pathStatusClass='path-status-'.strtolower(preg_replace('/[^A-Za-z0-9]+/','-',$pathRow["approval_status"]));
					?>
					<tr>
						<td class="text-center"><?php echo requestionHistoryHtml($pathRow["step_order"]); ?></td>
						<td>
							<strong><?php echo requestionHistoryHtml($pathRow["name_en"]); ?></strong>
							<div class="text-muted">Employee ID: <?php echo requestionHistoryHtml($pathRow["employee_id"]); ?></div>
						</td>
						<td><?php echo requestionHistoryHtml($pathRow["designation_name"]); ?></td>
						<td><span class="path-status-badge <?php echo requestionHistoryHtml($pathStatusClass); ?>"><?php echo requestionHistoryHtml($pathRow["approval_status"]); ?></span></td>
						<td><?php echo requestionHistoryHtml($pathRow["approval_time"]); ?></td>
						<td><?php echo nl2br(requestionHistoryHtml($pathRow["note"])); ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
<?php } ?>

<?php if(!empty($approvalFlowRows)){ ?>
	<div class="card approval-flow-panel no-print">
		<div class="card-header">
			<h3 class="card-title">Approval Track</h3>
		</div>
		<div class="card-body">
			<?php foreach($approvalFlowRows as $approvalFlowRow){ ?>
				<?php
				$flowAction=!empty($approvalFlowRow["action"]) ? $approvalFlowRow["action"] : (!empty($approvalFlowRow["approval_status"]) ? $approvalFlowRow["approval_status"] : 'Assigned');
				$flowActionLabel=requestionApprovalActionLabel($flowAction);
				$fromName=!empty($approvalFlowRow["from_employee_name"]) ? $approvalFlowRow["from_employee_name"] : 'System';
				$toName=!empty($approvalFlowRow["to_employee_name"]) ? $approvalFlowRow["to_employee_name"] : '';
				$flowTime=!empty($approvalFlowRow["created_at"]) ? $approvalFlowRow["created_at"] : (!empty($approvalFlowRow["updated_at"]) ? $approvalFlowRow["updated_at"] : (!empty($approvalFlowRow["asign_time"]) ? $approvalFlowRow["asign_time"] : ''));
				?>
				<div class="timeline-item">
					<div class="timeline-title"><?php echo requestionHistoryHtml($flowActionLabel); ?><?php if(!empty($toName)){ ?> to <?php echo requestionHistoryHtml($toName); ?><?php } ?></div>
					<div class="timeline-meta"><?php echo requestionHistoryHtml($fromName); ?><?php if(!empty($flowTime)){ ?> | <?php echo requestionHistoryHtml($flowTime); ?><?php } ?></div>
					<?php if(!empty($approvalFlowRow["note"])){ ?><div><?php echo nl2br(requestionHistoryHtml($approvalFlowRow["note"])); ?></div><?php } ?>
				</div>
			<?php } ?>
		</div>
	</div>
<?php } ?>

        </div>
        <!-- /.card-body -->
      </div>
      <!-- /.card -->

    </section>
<script>
$(document).ready(function(){
	var productOptions = <?php echo json_encode($product_options_html); ?>;
	var superAdminCounter = $('#superAdminRequisitionTable tbody tr').length;

	function refreshSuperAdminRows(){
		var count = 0;
		$('#superAdminRequisitionTable tbody tr').each(function(){
			count++;
			$(this).find('.super-admin-sl').text(count);
		});
		$('#super_admin_number_count').val(superAdminCounter);
	}

	function buildSuperAdminRow(index){
		return '<tr class="super-admin-product-row">'
			+ '<td class="super-admin-sl"></td>'
			+ '<td><input type="hidden" name="edit_id' + index + '" value=""><select name="product_id' + index + '" class="form-control select2 super-admin-product-select" style="width:100%;" required>' + productOptions + '</select></td>'
			+ '<td><input type="text" name="detail' + index + '" class="form-control"></td>'
			+ '<td><input type="number" step="any" name="requestion_quantity' + index + '" class="form-control calc-field req-qty"></td>'
			+ '<td><input type="number" step="any" name="requistion_rate' + index + '" class="form-control calc-field req-rate"></td>'
			+ '<td><input type="number" step="any" name="requestion_amount' + index + '" class="form-control req-amount"></td>'
			+ '<td><input type="number" step="any" name="final_quantity' + index + '" class="form-control calc-field final-qty"></td>'
			+ '<td><input type="number" step="any" name="final_rate' + index + '" class="form-control calc-field final-rate"></td>'
			+ '<td><input type="number" step="any" name="final_amount' + index + '" class="form-control final-amount"></td>'
			+ '<td><input type="text" name="comment' + index + '" class="form-control"></td>'
			+ '<td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-super-admin-row"><i class="fas fa-trash"></i></button></td>'
			+ '</tr>';
	}

	$('#addSuperAdminProduct').on('click', function(){
		superAdminCounter++;
		$('#superAdminRequisitionTable tbody').append(buildSuperAdminRow(superAdminCounter));
		$('#superAdminRequisitionTable tbody tr:last .select2').select2();
		refreshSuperAdminRows();
	});

	$('#superAdminRequisitionTable').on('click', '.remove-super-admin-row', function(){
		$(this).closest('tr').remove();
		refreshSuperAdminRows();
	});

	$('#superAdminRequisitionTable').on('input', '.calc-field', function(){
		var row = $(this).closest('tr');
		var reqQty = Number(row.find('.req-qty').val());
		var reqRate = Number(row.find('.req-rate').val());
		var finalQty = Number(row.find('.final-qty').val());
		var finalRate = Number(row.find('.final-rate').val());
		if(reqQty || reqRate){
			row.find('.req-amount').val(reqQty * reqRate);
		}
		if(finalQty || finalRate){
			row.find('.final-amount').val(finalQty * finalRate);
		}
	});

	refreshSuperAdminRows();
});
</script>

<?php } ?>
