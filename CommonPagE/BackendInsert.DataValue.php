<?php
$created_by=$LoginReGiSterSession;
$created_at=$current_time;
$updated_by=$LoginReGiSterSession;
$updated_at=$current_time;
$deleted_by=$LoginReGiSterSession;
$deleted_at=$current_time;

require_once("BDB/Auth.php");
require_once __DIR__ . '/Emergency_Request.Action.php';

if(!function_exists('requestionApprovalCsrfToken')){
function requestionApprovalCsrfToken(){
	if(empty($_SESSION['requestion_approval_csrf'])){
		$_SESSION['requestion_approval_csrf']=bin2hex(random_bytes(32));
	}
	return $_SESSION['requestion_approval_csrf'];
}
}

if(!function_exists('requestionApprovalVerifyCsrf')){
function requestionApprovalVerifyCsrf($token){
	$sessionToken=isset($_SESSION['requestion_approval_csrf']) ? (string)$_SESSION['requestion_approval_csrf'] : '';
	if($sessionToken==='' || !is_string($token) || !hash_equals($sessionToken,$token)){
		throw new DomainException('Your form session expired. Refresh the page and try again.');
	}
}
}

if(!function_exists('requestionApprovalDetailInsertPrefix')){
function requestionApprovalDetailInsertPrefix($pdo){
	$column_information = $pdo->query("SHOW COLUMNS FROM requestion_approval_detail LIKE 'id'");
	$rowdataColumn_information = $column_information->fetch();
	if(!empty($rowdataColumn_information["Extra"]) && stripos($rowdataColumn_information["Extra"], "auto_increment") !== false){
		return array('', '');
	}
	$next_information = $pdo->query("SELECT COALESCE(MAX(id),0)+1 AS next_id FROM requestion_approval_detail");
	$rowdataNext_information = $next_information->fetch();
	$next_id = !empty($rowdataNext_information["next_id"]) ? $rowdataNext_information["next_id"] : 1;
	return array('`id`, ', "'".$next_id."', ");
}
}

if(!function_exists('requestionApprovalColumnExists')){
function requestionApprovalColumnExists($pdo, $table, $column){
	$safeTable=preg_replace('/[^A-Za-z0-9_]/','',$table);
	$statement=$pdo->query("SHOW COLUMNS FROM `".$safeTable."` LIKE ".$pdo->quote($column));
	return (bool)$statement->fetch();
}
}

if(!function_exists('requestionApprovalFlowTableExists')){
function requestionApprovalFlowTableExists($pdo){
	$statement=$pdo->query("SHOW TABLES LIKE 'project_material_approval_flow'");
	return (bool)$statement->fetch();
}
}

if(!function_exists('requestionApprovalPathSnapshotTableExists')){
function requestionApprovalPathSnapshotTableExists($pdo){
	$statement=$pdo->query("SHOW TABLES LIKE 'requisition_approval_path_steps'");
	return (bool)$statement->fetch();
}
}

if(!function_exists('requestionApprovalFinalApproverName')){
function requestionApprovalFinalApproverName($pdo){
	$statement=$pdo->query("SELECT name_en FROM employee_information WHERE id='1' AND deleted_at IS NULL LIMIT 1");
	$row=$statement ? $statement->fetch() : array();
	return !empty($row['name_en']) ? $row['name_en'] : 'Zulfiquer Haider';
}
}

if(!function_exists('requestionApprovalPathSteps')){
function requestionApprovalPathSteps($pdo,$invoice_id,$project_id,$approval_path_name_id){
	$pathRows=array();
	if(requestionApprovalPathSnapshotTableExists($pdo)){
		$statement=$pdo->prepare("SELECT step_order,employee_id FROM requisition_approval_path_steps WHERE invoice_id=:invoice_id AND project_id=:project_id AND deleted_at IS NULL ORDER BY step_order ASC,id ASC");
		$statement->execute(array(':invoice_id'=>$invoice_id,':project_id'=>$project_id));
		$rows=$statement->fetchAll();
		if(!empty($rows)){
			$pathRows=$rows;
		}
	}
	$statement=$pdo->prepare("SELECT id AS step_order,employee_id FROM project_approval_path_inforamtion WHERE approval_path_name=:approval_path_name AND project_id=:project_id AND deleted_at IS NULL ORDER BY id ASC");
	$statement->execute(array(':approval_path_name'=>$approval_path_name_id,':project_id'=>$project_id));
	$savedPathRows=$statement->fetchAll();
	if(empty($pathRows)){
		return $savedPathRows;
	}
	$existingEmployeeIds=array_map('intval',array_column($pathRows,'employee_id'));
	$nextOrder=(int)max(array_map('intval',array_column($pathRows,'step_order')));
	foreach($savedPathRows as $savedPathRow){
		$savedEmployeeId=(int)$savedPathRow['employee_id'];
		if(!in_array($savedEmployeeId,$existingEmployeeIds,true)){
			$nextOrder++;
			$pathRows[]=array('step_order'=>$nextOrder,'employee_id'=>$savedEmployeeId);
			$existingEmployeeIds[]=$savedEmployeeId;
		}
	}
	return $pathRows;
}
}

if(!function_exists('requestionApprovalPathHasFinalEmployee')){
function requestionApprovalPathHasFinalEmployee($pdo,$approval_path_name_id,$project_id){
	$statement=$pdo->prepare("SELECT employee_id FROM project_approval_path_inforamtion WHERE approval_path_name=:approval_path_name AND project_id=:project_id AND deleted_at IS NULL ORDER BY id ASC");
	$statement->execute(array(':approval_path_name'=>$approval_path_name_id,':project_id'=>$project_id));
	$employees=$statement->fetchAll();
	if(empty($employees)){
		return false;
	}
	$last=end($employees);
	return (int)$last['employee_id']===1;
}
}

if(!function_exists('requestionApprovalCreatePathSnapshot')){
function requestionApprovalCreatePathSnapshot($pdo,$invoice_id,$project_id,$approval_path_name_id,$created_by,$current_time){
	if(!requestionApprovalPathSnapshotTableExists($pdo)){
		return;
	}
	$delete=$pdo->prepare("UPDATE requisition_approval_path_steps SET deleted_by=:deleted_by,deleted_at=:deleted_at WHERE invoice_id=:invoice_id AND project_id=:project_id AND deleted_at IS NULL");
	$delete->execute(array(':deleted_by'=>$created_by,':deleted_at'=>$current_time,':invoice_id'=>$invoice_id,':project_id'=>$project_id));
	$steps=$pdo->prepare("SELECT employee_id FROM project_approval_path_inforamtion WHERE approval_path_name=:approval_path_name AND project_id=:project_id AND deleted_at IS NULL ORDER BY id ASC");
	$steps->execute(array(':approval_path_name'=>$approval_path_name_id,':project_id'=>$project_id));
	$insert=$pdo->prepare("INSERT INTO requisition_approval_path_steps(invoice_id,project_id,approval_path_name_id,step_order,employee_id,created_by,created_at) VALUES (:invoice_id,:project_id,:approval_path_name_id,:step_order,:employee_id,:created_by,:created_at)");
	$stepOrder=1;
	while($step=$steps->fetch()){
		$insert->execute(array(':invoice_id'=>$invoice_id,':project_id'=>$project_id,':approval_path_name_id'=>$approval_path_name_id,':step_order'=>$stepOrder,':employee_id'=>$step['employee_id'],':created_by'=>$created_by,':created_at'=>$current_time));
		$stepOrder++;
	}
}
}

if(!function_exists('requestionApprovalAllowedEmployeeIds')){
function requestionApprovalAllowedEmployeeIds($pdo,$invoice_id,$project_id,$approval_path_name_id,$current_employee_id,$direction){
	$steps=requestionApprovalPathSteps($pdo,$invoice_id,$project_id,$approval_path_name_id);
	$fallbackAllowedEmployees=function($direction) use ($pdo,$invoice_id,$project_id,$current_employee_id){
		if($direction==='forward'){
			$unsignedStatement=$pdo->prepare("SELECT DISTINCT employee_id FROM project_material_aproval_status WHERE invoice_id=:invoice_id AND project_id=:project_id AND approval_id IS NULL AND deleted_at IS NULL AND employee_id<>:employee_id AND (approval_status IS NULL OR approval_status='Pending') ORDER BY id ASC");
			$unsignedStatement->execute(array(':invoice_id'=>$invoice_id,':project_id'=>$project_id,':employee_id'=>$current_employee_id));
			return array_map('intval',$unsignedStatement->fetchAll(PDO::FETCH_COLUMN));
		}
		if($direction==='return'){
			$approvedStatement=$pdo->prepare("SELECT DISTINCT employee_id FROM project_material_aproval_status WHERE invoice_id=:invoice_id AND project_id=:project_id AND approval_status='Approve' AND approval_id IS NOT NULL AND deleted_at IS NULL AND employee_id<>:employee_id ORDER BY id ASC");
			$approvedStatement->execute(array(':invoice_id'=>$invoice_id,':project_id'=>$project_id,':employee_id'=>$current_employee_id));
			return array_map('intval',$approvedStatement->fetchAll(PDO::FETCH_COLUMN));
		}
		return array();
	};
	$currentOrder=null;
	foreach($steps as $step){
		if((int)$step['employee_id']===(int)$current_employee_id){
			$currentOrder=(int)$step['step_order'];
		}
	}
	if($currentOrder===null){
		return $fallbackAllowedEmployees($direction);
	}
	$approvedStatement=$pdo->prepare("SELECT DISTINCT employee_id FROM project_material_aproval_status WHERE invoice_id=:invoice_id AND project_id=:project_id AND approval_status='Approve' AND approval_id IS NOT NULL AND deleted_at IS NULL");
	$approvedStatement->execute(array(':invoice_id'=>$invoice_id,':project_id'=>$project_id));
	$approvedIds=array_map('intval',$approvedStatement->fetchAll(PDO::FETCH_COLUMN));
	$allowed=array();
	foreach($steps as $step){
		$employeeId=(int)$step['employee_id'];
		$stepOrder=(int)$step['step_order'];
		if($employeeId===(int)$current_employee_id){
			continue;
		}
		if($direction==='forward' && $stepOrder>$currentOrder && !in_array($employeeId,$approvedIds,true)){
			$allowed[]=$employeeId;
		}
		if($direction==='return' && $stepOrder<$currentOrder && in_array($employeeId,$approvedIds,true)){
			$allowed[]=$employeeId;
		}
	}
	$allowed=array_values(array_unique($allowed));
	if(empty($allowed)){
		$allowed=$fallbackAllowedEmployees($direction);
	}
	return $allowed;
}
}

if(!function_exists('requestionApprovalFirstUnsignedEmployeeId')){
function requestionApprovalFirstUnsignedEmployeeId($pdo,$invoice_id,$project_id,$approval_path_name_id){
	$steps=requestionApprovalPathSteps($pdo,$invoice_id,$project_id,$approval_path_name_id);
	if(empty($steps)){
		return 0;
	}
	$approvedStatement=$pdo->prepare("SELECT DISTINCT employee_id FROM project_material_aproval_status WHERE invoice_id=:invoice_id AND project_id=:project_id AND approval_status='Approve' AND approval_id IS NOT NULL AND deleted_at IS NULL");
	$approvedStatement->execute(array(':invoice_id'=>$invoice_id,':project_id'=>$project_id));
	$approvedIds=array_map('intval',$approvedStatement->fetchAll(PDO::FETCH_COLUMN));
	foreach($steps as $step){
		$employeeId=(int)$step['employee_id'];
		if(!in_array($employeeId,$approvedIds,true)){
			return $employeeId;
		}
	}
	return 0;
}
}

if(!function_exists('requestionApprovalAttachmentDirectory')){
function requestionApprovalAttachmentDirectory(){
	return dirname(__DIR__).'/RequistionAttachment';
}
}

if(!function_exists('requestionApprovalDeleteUploadedFiles')){
function requestionApprovalDeleteUploadedFiles($files,$destination=null){
	$destination=$destination ?: requestionApprovalAttachmentDirectory();
	foreach((array)$files as $file){
		$safeFile=basename((string)$file);
		$path=rtrim($destination,'/').'/'.$safeFile;
		if($safeFile!=='' && is_file($path)){
			@unlink($path);
		}
	}
}
}

if(!function_exists('requestionApprovalUploadRateAttachment')){
function requestionApprovalUploadRateAttachment($upload=null,$destination=null){
	$destination=$destination ?: requestionApprovalAttachmentDirectory();
	if($upload===null){
		$upload=isset($_FILES['rate_attachment']) ? $_FILES['rate_attachment'] : array();
	}
	if(empty($upload['name'])){
		return array();
	}
	$allowed=array('jpg','jpeg','png','pdf','doc','docx','xls','xlsx');
	if(!is_dir($destination) && !mkdir($destination,0775,true)){
		return false;
	}
	if(!is_writable($destination)){
		return false;
	}
	$fileNames=is_array($upload['name']) ? $upload['name'] : array($upload['name']);
	$fileTmps=is_array($upload['tmp_name']) ? $upload['tmp_name'] : array($upload['tmp_name']);
	$fileErrors=isset($upload['error']) && is_array($upload['error']) ? $upload['error'] : array(isset($upload['error']) ? $upload['error'] : UPLOAD_ERR_OK);
	$fileSizes=isset($upload['size']) && is_array($upload['size']) ? $upload['size'] : array(isset($upload['size']) ? $upload['size'] : 0);
	$allowedMimes=array(
		'jpg'=>array('image/jpeg'),
		'jpeg'=>array('image/jpeg'),
		'png'=>array('image/png'),
		'pdf'=>array('application/pdf'),
		'doc'=>array('application/msword','application/octet-stream'),
		'docx'=>array('application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/zip','application/octet-stream'),
		'xls'=>array('application/vnd.ms-excel','application/octet-stream'),
		'xlsx'=>array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/zip','application/octet-stream')
	);
	$maxFileSize=10*1024*1024;
	$uploadedFiles=array();
	foreach($fileNames as $fileIndex=>$fileName){
		if((string)$fileName===''){
			continue;
		}
		$error=isset($fileErrors[$fileIndex]) ? (int)$fileErrors[$fileIndex] : UPLOAD_ERR_OK;
		$tmp=isset($fileTmps[$fileIndex]) ? $fileTmps[$fileIndex] : '';
		$size=isset($fileSizes[$fileIndex]) ? (int)$fileSizes[$fileIndex] : ($tmp!=='' && is_file($tmp) ? (int)filesize($tmp) : 0);
		$extension=strtolower(pathinfo($fileName,PATHINFO_EXTENSION));
		$mime='';
		if($tmp!=='' && is_file($tmp) && function_exists('finfo_open')){
			$finfo=finfo_open(FILEINFO_MIME_TYPE);
			$mime=$finfo ? (string)finfo_file($finfo,$tmp) : '';
			if($finfo){ finfo_close($finfo); }
		}
		if($error!==UPLOAD_ERR_OK || $tmp==='' || !is_file($tmp) || $size<=0 || $size>$maxFileSize || !in_array($extension,$allowed,true) || ($mime!=='' && !in_array($mime,$allowedMimes[$extension],true))){
			requestionApprovalDeleteUploadedFiles($uploadedFiles,$destination);
			return false;
		}
		$safeBase=trim(preg_replace('/[^A-Za-z0-9_-]/','_',pathinfo($fileName,PATHINFO_FILENAME)),'_');
		if($safeBase===''){
			$safeBase='rate_proof';
		}
		try{
			$suffix=bin2hex(random_bytes(8));
		}catch(Exception $exception){
			$suffix=str_replace('.','',uniqid('',true));
		}
		$newfilename=$safeBase.'_'.$suffix.'_'.$fileIndex.'.'.$extension;
		$target=rtrim($destination,'/').'/'.$newfilename;
		$moved=is_uploaded_file($tmp) ? move_uploaded_file($tmp,$target) : (PHP_SAPI==='cli' ? rename($tmp,$target) : false);
		if(!$moved || !is_file($target)){
			requestionApprovalDeleteUploadedFiles($uploadedFiles,$destination);
			return false;
		}
		$uploadedFiles[]=$newfilename;
	}
	return $uploadedFiles;
}
}

if(!function_exists('requestionApprovalEncodeRateAttachments')){
function requestionApprovalEncodeRateAttachments($files){
	return !empty($files) ? json_encode(array_values($files)) : '';
}
}

if(!function_exists('distributionRemainingDueTotal')){
function distributionRemainingDueTotal($pdo,$invoice_id,$requisition_type='Material'){
	$dueColumn=($requisition_type==='Fund') ? 'due_amount' : 'due_quantity';
	$statement=$pdo->prepare("SELECT COALESCE(SUM(GREATEST(CAST(COALESCE(NULLIF(".$dueColumn.",''),0) AS DECIMAL(18,4)),0)),0) FROM requestion_detail WHERE invoice_id=:invoice_id AND deleted_at IS NULL");
	$statement->execute(array(':invoice_id'=>$invoice_id));
	return (float)$statement->fetchColumn();
}
}

if(!function_exists('distributionSyncRequisitionStatus')){
function distributionSyncRequisitionStatus($pdo,$invoice_id,$requisition_type,$distributed_by,$distributed_at){
	$status=distributionRemainingDueTotal($pdo,$invoice_id,$requisition_type)>0 ? 'Pending' : 'Complete';
	$statement=$pdo->prepare("UPDATE requestion_histiory SET distribution_status=:distribution_status,distribution_by=:distribution_by,distribution_at=:distribution_at WHERE invoice_id=:invoice_id AND deleted_at IS NULL");
	$statement->execute(array(
		':distribution_status'=>$status,
		':distribution_by'=>$distributed_by,
		':distribution_at'=>$distributed_at,
		':invoice_id'=>$invoice_id
	));
	return $status;
}
}

if(!function_exists('distributionAvailableStock')){
function distributionAvailableStock($pdo,$product_id,$store_id){
	$statement=$pdo->prepare("SELECT COALESCE(SUM(CAST(COALESCE(NULLIF(stock,''),0) AS DECIMAL(18,4))),0) FROM stock_information WHERE product_id=:product_id AND store_id=:store_id AND deleted_at IS NULL");
	$statement->execute(array(':product_id'=>$product_id,':store_id'=>$store_id));
	return (float)$statement->fetchColumn();
}
}

if(!function_exists('distributionConsumeStock')){
function distributionConsumeStock($pdo,$product_id,$store_id,$quantity){
	$remaining=max(0,(float)$quantity);
	if($remaining<=0){
		return 0;
	}
	$consumed=0;
	$stockRows=$pdo->prepare("SELECT id,stock,distribution FROM stock_information WHERE product_id=:product_id AND store_id=:store_id AND deleted_at IS NULL AND CAST(COALESCE(NULLIF(stock,''),0) AS DECIMAL(18,4))>0 ORDER BY id ASC");
	$stockRows->execute(array(':product_id'=>$product_id,':store_id'=>$store_id));
	$updateStock=$pdo->prepare("UPDATE stock_information SET distribution=:distribution,stock=:stock WHERE id=:id");
	while($remaining>0 && $stockRow=$stockRows->fetch()){
		$currentStock=max(0,(float)$stockRow['stock']);
		$take=min($remaining,$currentStock);
		if($take<=0){
			continue;
		}
		$newDistribution=(float)$stockRow['distribution']+$take;
		$newStock=$currentStock-$take;
		$updateStock->execute(array(':distribution'=>$newDistribution,':stock'=>$newStock,':id'=>$stockRow['id']));
		$remaining-=$take;
		$consumed+=$take;
	}
	return $consumed;
}
}

if(!function_exists('distributionTableUsesManualId')){
function distributionTableUsesManualId($pdo,$table){
	$safeTable=preg_replace('/[^A-Za-z0-9_]/','',$table);
	$statement=$pdo->query("SHOW COLUMNS FROM `".$safeTable."` LIKE 'id'");
	$row=$statement ? $statement->fetch() : array();
	return empty($row['Extra']) || stripos($row['Extra'],'auto_increment')===false;
}
}

if(!function_exists('distributionNextManualId')){
function distributionNextManualId($pdo,$table){
	$safeTable=preg_replace('/[^A-Za-z0-9_]/','',$table);
	$statement=$pdo->query("SELECT COALESCE(MAX(id),0)+1 AS next_id FROM `".$safeTable."`");
	$row=$statement ? $statement->fetch() : array();
	return !empty($row['next_id']) ? (int)$row['next_id'] : 1;
}
}

if(!function_exists('requestionApprovalAttachmentFiles')){
function requestionApprovalAttachmentFiles($value){
	$value=trim((string)$value);
	if($value===''){
		return array();
	}
	$decoded=json_decode($value,true);
	return is_array($decoded) ? array_values(array_filter($decoded)) : array($value);
}
}

if(!function_exists('requestionApprovalMergeRateAttachments')){
function requestionApprovalMergeRateAttachments($existingValue,$newFiles){
	$files=array_merge(requestionApprovalAttachmentFiles($existingValue),(array)$newFiles);
	$files=array_values(array_unique(array_filter(array_map('basename',$files))));
	return requestionApprovalEncodeRateAttachments($files);
}
}

if(!function_exists('requestionApprovalRatesDiffer')){
function requestionApprovalRatesDiffer($submitted,$stored){
	$submitted=trim((string)$submitted);
	$stored=trim((string)$stored);
	if($submitted==='' && $stored===''){
		return false;
	}
	if(is_numeric($submitted) && is_numeric($stored)){
		return abs((float)$submitted-(float)$stored)>0.000001;
	}
	return $submitted!==$stored;
}
}

if(!function_exists('requestionApprovalUploadProvided')){
function requestionApprovalUploadProvided($upload=null){
	if($upload===null){
		$upload=isset($_FILES['rate_attachment']) ? $_FILES['rate_attachment'] : array();
	}
	if(empty($upload['name'])){
		return false;
	}
	$names=is_array($upload['name']) ? $upload['name'] : array($upload['name']);
	foreach($names as $name){
		if(trim((string)$name)!==''){
			return true;
		}
	}
	return false;
}
}

if(!function_exists('requestionApprovalStoredRateProofExists')){
function requestionApprovalStoredRateProofExists($pdo,$invoice_id,$project_id,$product_id,$rate,$destination=null){
	$destination=$destination ?: requestionApprovalAttachmentDirectory();
	$statement=$pdo->prepare("SELECT final_rate,rate_attachment FROM requestion_approval_detail WHERE invoice_id=:invoice_id AND project_id=:project_id AND product_id=:product_id AND rate_attachment IS NOT NULL AND TRIM(rate_attachment)<>'' AND deleted_at IS NULL ORDER BY id DESC");
	$statement->execute(array(':invoice_id'=>$invoice_id,':project_id'=>$project_id,':product_id'=>$product_id));
	while($row=$statement->fetch()){
		if(requestionApprovalRatesDiffer($rate,$row['final_rate'])){
			continue;
		}
		foreach(requestionApprovalAttachmentFiles($row['rate_attachment']) as $file){
			if(is_file(rtrim($destination,'/').'/'.basename($file))){
				return true;
			}
		}
	}
	return false;
}
}

if(!function_exists('requestionApprovalHasRateChange')){
function requestionApprovalHasRateChange($pdo,$invoice_id,$project_id,$number_count,$rateFieldPrefix){
	$statement=$pdo->prepare("SELECT product_id,COALESCE(NULLIF(final_rate,''),NULLIF(requistion_rate,''),'') AS current_rate,COALESCE(requistion_rate,'') AS requisition_rate FROM requestion_detail WHERE id=:id AND invoice_id=:invoice_id AND deleted_at IS NULL LIMIT 1");
	for($i=1;$i<=$number_count;$i++){
		if(empty($_POST["edit_id".$i])){
			continue;
		}
		$statement->execute(array(':id'=>(int)$_POST["edit_id".$i],':invoice_id'=>$invoice_id));
		$current=$statement->fetch();
		$submitted=isset($_POST[$rateFieldPrefix.$i]) ? $_POST[$rateFieldPrefix.$i] : '';
		if($current
			&& (requestionApprovalRatesDiffer($submitted,$current['current_rate']) || requestionApprovalRatesDiffer($submitted,$current['requisition_rate']))
			&& !requestionApprovalStoredRateProofExists($pdo,$invoice_id,$project_id,$current['product_id'],$submitted)){
			return true;
		}
	}
	return false;
}
}

if(!function_exists('requestionApprovalDetailExists')){
function requestionApprovalDetailExists($pdo,$invoice_id,$project_id,$employee_id,$product_id){
	$statement=$pdo->prepare("SELECT id FROM requestion_approval_detail WHERE invoice_id=:invoice_id AND project_id=:project_id AND employee_id=:employee_id AND product_id=:product_id LIMIT 1");
	$statement->execute(array(
		':invoice_id'=>$invoice_id,
		':project_id'=>$project_id,
		':employee_id'=>$employee_id,
		':product_id'=>$product_id
	));
	return (bool)$statement->fetch();
}
}

if(!function_exists('requestionApprovalNotifyEmployee')){
function requestionApprovalNotifyEmployee($pdo,$employee_id,$invoice_id,$project_id,$base_url,$sms_send_url,$apikey,$sender_id,$organization_name,$company_email_address,$created_by){
	$Staff_information=$pdo->prepare("SELECT employee_information.*,hr_designation.name AS designation FROM employee_information LEFT JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE employee_information.id=:employee_id AND employee_information.deleted_at IS NULL LIMIT 1");
	$Staff_information->execute(array(':employee_id'=>$employee_id));
	$rowdataStaff_information=$Staff_information->fetch();
	if(empty($rowdataStaff_information)){
		return;
	}
	$Requestion_information=$pdo->prepare("SELECT employee_information.name_en AS name_en,hr_designation.name AS designation FROM employee_information INNER JOIN hr_designation ON employee_information.designation=hr_designation.id WHERE employee_information.id=:created_by LIMIT 1");
	$Requestion_information->execute(array(':created_by'=>$created_by));
	$rowdataRequestion_information=$Requestion_information->fetch();
	$Project_information=$pdo->prepare("SELECT * FROM project_information WHERE id=:project_id LIMIT 1");
	$Project_information->execute(array(':project_id'=>$project_id));
	$rowdataProject_information=$Project_information->fetch();
	$rowdataRequestion_information=$rowdataRequestion_information ?: array('name_en'=>'','designation'=>'');
	$rowdataProject_information=$rowdataProject_information ?: array('name'=>'');

	if(!empty($rowdataStaff_information["mobile"]) && !empty($sms_send_url)){
		$number="88".$rowdataStaff_information["mobile"];
		$sms_message="একটি রিকুইজিশন আপনার অনুমোদনের জন্য অপেক্ষা করছে। Link :".$base_url."?Requestion_History_Detail/".$invoice_id;
		$message_url=$sms_send_url."?api_key=".$apikey."&type=unicode&contacts=".urlencode($number)."&senderid=".$sender_id."&msg=".urlencode($sms_message);
		$curl=curl_init();
		curl_setopt($curl,CURLOPT_URL,$message_url);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
		curl_exec($curl);
		curl_close($curl);
	}

	if(!empty($rowdataStaff_information["email"])){
		$to=$rowdataStaff_information["email"];
		$subject="Material Requisition Request for Project No ".$rowdataProject_information["name"];
		$message="
<html>
<head><title>".$organization_name.":: Material Requestion</title></head>
<body>
<table width='100%' cellpadding='10' cellspacing='5' bgcolor='#fff'>
  <tr><td style='text-align:left; font-size:14px; font-weight:bold;' colspan='2'>Requisition Approval Request</td></tr>
  <tr><th>Name</th><td>".$rowdataRequestion_information["name_en"]."</td></tr>
  <tr><th>Designation</th><td>".$rowdataRequestion_information["designation"]."</td></tr>
  <tr><th>Subject</th><td>Material Requisition Request for Project No ".$rowdataProject_information["name"]."</td></tr>
  <tr><th>Message</th><td><a href='".$base_url."?Requestion_History_Detail/".$invoice_id."' style='color:green;display:block;width:115px;height:25px;background:#4E9CAF;padding:10px;text-align:center;border-radius:5px;color:white;font-weight:bold;line-height:25px;'>Click here to View</a><br>Link: ".$base_url."?Requestion_History_Detail/".$invoice_id."<br><br>Greetings, request No. ".$invoice_id." for project ".$rowdataProject_information["name"]." is waiting for your action.<br><br>Thanks.</td></tr>
</table>
</body>
</html>";
		$headers="MIME-Version: 1.0"."\r\n";
		$headers.="Content-type:text/html;charset=UTF-8"."\r\n";
		$headers.='From: <'.$company_email_address.'>'."\r\n";
		mail($to,$subject,$message,$headers);
	}
}
}

if(!function_exists('requestionApprovalRecordFlow')){
function requestionApprovalRecordFlow($pdo,$invoice_id,$project_id,$action,$from_employee_id,$to_employee_id,$note,$created_by,$current_time){
	if(!requestionApprovalFlowTableExists($pdo)){
		return;
	}
	$statement=$pdo->prepare("INSERT INTO project_material_approval_flow(invoice_id,project_id,action,from_employee_id,to_employee_id,note,created_by,created_at) VALUES (:invoice_id,:project_id,:action,:from_employee_id,:to_employee_id,:note,:created_by,:created_at)");
	$statement->execute(array(
		':invoice_id'=>$invoice_id,
		':project_id'=>$project_id,
		':action'=>$action,
		':from_employee_id'=>$from_employee_id,
		':to_employee_id'=>$to_employee_id,
		':note'=>$note,
		':created_by'=>$created_by,
		':created_at'=>$current_time
	));
}
}

if(!function_exists('requestionApprovalValidateAction')){
function requestionApprovalValidateAction($pdo,$invoice_id,$project_id,$action,$forward_employee_id,$return_employee_id,$created_by){
	if(!in_array($action,array('recommend','return','reject'),true)){
		throw new DomainException('Invalid requisition approval action.');
	}
	$lockClause=$pdo->inTransaction() ? ' FOR UPDATE' : '';
	$pendingStatement=$pdo->prepare("SELECT * FROM project_material_aproval_status WHERE invoice_id=:invoice_id AND project_id=:project_id AND employee_id=:employee_id AND approval_status='Pending' AND deleted_at IS NULL ORDER BY id DESC LIMIT 1".$lockClause);
	$pendingStatement->execute(array(':invoice_id'=>$invoice_id,':project_id'=>$project_id,':employee_id'=>$created_by));
	$currentPending=$pendingStatement->fetch();
	if(empty($currentPending)){
		throw new DomainException('This requisition is not currently pending for your approval.');
	}
	$approvalPathNameId=!empty($currentPending['approval_path_name_id']) ? $currentPending['approval_path_name_id'] : 0;
	$hasPath=!empty($approvalPathNameId);
	$forwardAllowedIds=$hasPath ? requestionApprovalAllowedEmployeeIds($pdo,$invoice_id,$project_id,$approvalPathNameId,$created_by,'forward') : array();
	$returnAllowedIds=$hasPath ? requestionApprovalAllowedEmployeeIds($pdo,$invoice_id,$project_id,$approvalPathNameId,$created_by,'return') : array();
	if($action==='recommend' && (int)$created_by!==1){
		if(empty($forward_employee_id)){
			throw new DomainException('Please select the employee you want to recommend/forward to.');
		}
		if($hasPath && !in_array((int)$forward_employee_id,$forwardAllowedIds,true)){
			throw new DomainException('Forward is allowed only to an unsigned employee ahead in this approval path.');
		}
	}
	if($action==='return'){
		if(empty($return_employee_id)){
			throw new DomainException('Please select the employee you want to return this requisition to.');
		}
		if($hasPath && !in_array((int)$return_employee_id,$returnAllowedIds,true)){
			throw new DomainException('Return is allowed only to a previous signer in this approval path.');
		}
	}
	return array(
		'current_pending'=>$currentPending,
		'approval_path_name_id'=>$approvalPathNameId,
		'has_path'=>$hasPath,
		'forward_allowed_ids'=>$forwardAllowedIds,
		'return_allowed_ids'=>$returnAllowedIds
	);
}
}

if(!function_exists('requestionApprovalHandleDynamicAction')){
function requestionApprovalHandleDynamicAction($pdo,$invoice_id,$project_id,$store_id,$note,$action,$forward_employee_id,$return_employee_id,$created_by,$created_at,$current_date,$current_time,$base_url,$sms_send_url,$apikey,$sender_id,$organization_name,$company_email_address,$validationContext=null,$sendNotification=true){
	$context=$validationContext ?: requestionApprovalValidateAction($pdo,$invoice_id,$project_id,$action,$forward_employee_id,$return_employee_id,$created_by);
	$currentPending=$context['current_pending'];
	$approvalPathNameId=$context['approval_path_name_id'];
	$createdByColumn=requestionApprovalColumnExists($pdo,'project_material_aproval_status','created_by') ? ', created_by' : '';
	$createdByValue=$createdByColumn ? ', :created_by' : '';
	$createdAtColumn=requestionApprovalColumnExists($pdo,'project_material_aproval_status','created_at') ? ', created_at' : '';
	$createdAtValue=$createdAtColumn ? ', :created_at' : '';
	$insertPendingSql="INSERT INTO project_material_aproval_status(approval_path_name_id,invoice_id,project_id,employee_id,assign_employee_id,approval_status,asign_date,asign_time".$createdByColumn.$createdAtColumn.") VALUES (:approval_path_name_id,:invoice_id,:project_id,:employee_id,:assign_employee_id,'Pending',:asign_date,:asign_time".$createdByValue.$createdAtValue.")";
	$insertPending=$pdo->prepare($insertPendingSql);

	if($action==='reject'){
		$rejectStatement=$pdo->prepare("UPDATE project_material_aproval_status SET note=:note,approval_status='Reject',updated_by=:updated_by,updated_at=:updated_at WHERE id=:id AND approval_status='Pending'");
		$rejectStatement->execute(array(':note'=>$note,':updated_by'=>$created_by,':updated_at'=>$created_at,':id'=>$currentPending['id']));
		$pdo->prepare("UPDATE requestion_histiory SET approval_status='Reject',updated_by=:updated_by,updated_at=:updated_at WHERE invoice_id=:invoice_id AND project_id=:project_id")->execute(array(':updated_by'=>$created_by,':updated_at'=>$created_at,':invoice_id'=>$invoice_id,':project_id'=>$project_id));
		requestionApprovalRecordFlow($pdo,$invoice_id,$project_id,'reject',$created_by,null,$note,$created_by,$created_at);
		return array('action'=>'reject','notify_employee_id'=>0);
	}

	if($action==='return'){
		$returnStatement=$pdo->prepare("UPDATE project_material_aproval_status SET note=:note,approval_status='Returned',updated_by=:updated_by,updated_at=:updated_at WHERE id=:id AND approval_status='Pending'");
		$returnStatement->execute(array(':note'=>$note,':updated_by'=>$created_by,':updated_at'=>$created_at,':id'=>$currentPending['id']));
		$params=array(':approval_path_name_id'=>$approvalPathNameId,':invoice_id'=>$invoice_id,':project_id'=>$project_id,':employee_id'=>$return_employee_id,':assign_employee_id'=>$created_by,':asign_date'=>$current_date,':asign_time'=>$created_at);
		if($createdByColumn){ $params[':created_by']=$created_by; }
		if($createdAtColumn){ $params[':created_at']=$created_at; }
		$insertPending->execute($params);
		requestionApprovalRecordFlow($pdo,$invoice_id,$project_id,'return',$created_by,$return_employee_id,$note,$created_by,$created_at);
		if($sendNotification){
			requestionApprovalNotifyEmployee($pdo,$return_employee_id,$invoice_id,$project_id,$base_url,$sms_send_url,$apikey,$sender_id,$organization_name,$company_email_address,$created_by);
		}
		return array('action'=>'return','notify_employee_id'=>(int)$return_employee_id);
	}

	$hasApprovedStatement=$pdo->prepare("SELECT id FROM project_material_aproval_status WHERE invoice_id=:invoice_id AND project_id=:project_id AND employee_id=:employee_id AND approval_status='Approve' AND approval_id IS NOT NULL AND deleted_at IS NULL LIMIT 1");
	$hasApprovedStatement->execute(array(':invoice_id'=>$invoice_id,':project_id'=>$project_id,':employee_id'=>$created_by));
	if($hasApprovedStatement->fetch()){
		$forwardedStatement=$pdo->prepare("UPDATE project_material_aproval_status SET note=:note,approval_status='Forwarded',updated_by=:updated_by,updated_at=:updated_at WHERE id=:id AND approval_status='Pending'");
		$forwardedStatement->execute(array(':note'=>$note,':updated_by'=>$created_by,':updated_at'=>$created_at,':id'=>$currentPending['id']));
	}else{
		$approveStatement=$pdo->prepare("UPDATE project_material_aproval_status SET approval_id=:approval_id,note=:note,approval_time=:approval_time,approval_date=:approval_date,approval_status='Approve',updated_by=:updated_by,updated_at=:updated_at WHERE id=:id AND approval_status='Pending'");
		$approveStatement->execute(array(':approval_id'=>$created_by,':note'=>$note,':approval_time'=>$created_at,':approval_date'=>$current_date,':updated_by'=>$created_by,':updated_at'=>$created_at,':id'=>$currentPending['id']));
	}

	if((int)$created_by===1){
		$pdo->prepare("UPDATE requestion_histiory SET approval_status='Approve',updated_by=:updated_by,updated_at=:updated_at WHERE invoice_id=:invoice_id AND project_id=:project_id")->execute(array(':updated_by'=>$created_by,':updated_at'=>$created_at,':invoice_id'=>$invoice_id,':project_id'=>$project_id));
		requestionApprovalRecordFlow($pdo,$invoice_id,$project_id,'final_approve',$created_by,null,$note,$created_by,$created_at);
		return array('action'=>'final_approve','notify_employee_id'=>0);
	}

	$params=array(':approval_path_name_id'=>$approvalPathNameId,':invoice_id'=>$invoice_id,':project_id'=>$project_id,':employee_id'=>$forward_employee_id,':assign_employee_id'=>$created_by,':asign_date'=>$current_date,':asign_time'=>$created_at);
	if($createdByColumn){ $params[':created_by']=$created_by; }
	if($createdAtColumn){ $params[':created_at']=$created_at; }
	$insertPending->execute($params);
	requestionApprovalRecordFlow($pdo,$invoice_id,$project_id,'forward',$created_by,$forward_employee_id,$note,$created_by,$created_at);
	if($sendNotification){
		requestionApprovalNotifyEmployee($pdo,$forward_employee_id,$invoice_id,$project_id,$base_url,$sms_send_url,$apikey,$sender_id,$organization_name,$company_email_address,$created_by);
	}
	return array('action'=>'forward','notify_employee_id'=>(int)$forward_employee_id);
}
}

if(!function_exists('requestionApprovalProcessPostedAction')){
function requestionApprovalProcessPostedAction($pdo,$buttonName,$isFund,$created_by,$created_at,$current_date,$current_time,$notificationConfig=array()){
	$invoice_id=isset($_POST['invoice_id']) ? trim($_POST['invoice_id']) : '';
	$project_id=isset($_POST['project_id']) ? trim($_POST['project_id']) : '';
	$store_id=isset($_POST['store_id']) ? trim($_POST['store_id']) : '';
	$note=isset($_POST['note']) ? trim($_POST['note']) : '';
	$action=!empty($_POST[$buttonName]) ? $_POST[$buttonName] : 'recommend';
	$forwardEmployeeId=!empty($_POST['forward_employee_id']) ? (int)$_POST['forward_employee_id'] : 0;
	$returnEmployeeId=!empty($_POST['return_employee_id']) ? (int)$_POST['return_employee_id'] : 0;
	$numberCount=!empty($_POST['number_count']) ? (int)$_POST['number_count'] : 0;
	$rateFieldPrefix=$isFund ? 'rate' : 'final_rate';
	$uploadedFiles=array();
	try{
		requestionApprovalVerifyCsrf(isset($_POST['requestion_approval_csrf']) ? $_POST['requestion_approval_csrf'] : '');
		$context=requestionApprovalValidateAction($pdo,$invoice_id,$project_id,$action,$forwardEmployeeId,$returnEmployeeId,$created_by);
		if(!requestionApprovalColumnExists($pdo,'requestion_approval_detail','rate_attachment')){
			throw new DomainException('Rate proof storage is not installed. Run the requisition approval database migration first.');
		}
		$rateChanged=$action==='recommend' && requestionApprovalHasRateChange($pdo,$invoice_id,$project_id,$numberCount,$rateFieldPrefix);
		$proofUploadProvided=$action==='recommend' && requestionApprovalUploadProvided();
		if($rateChanged || $proofUploadProvided){
			$uploadedFiles=requestionApprovalUploadRateAttachment();
			if($proofUploadProvided && empty($uploadedFiles)){
				throw new DomainException('Proof file upload failed. Please choose a valid file and try again.');
			}
			if($rateChanged && empty($uploadedFiles)){
				throw new DomainException('আপনি রেট পরিবর্তন করেছেন। সফলভাবে আপলোড করা প্রমাণ ফাইল ছাড়া রেট পরিবর্তন করা যাবে না।');
			}
		}

		$pdo->beginTransaction();
		$context=requestionApprovalValidateAction($pdo,$invoice_id,$project_id,$action,$forwardEmployeeId,$returnEmployeeId,$created_by);
		if($action==='recommend'){
			$findApproval=$pdo->prepare("SELECT id,rate_attachment FROM requestion_approval_detail WHERE invoice_id=:invoice_id AND project_id=:project_id AND employee_id=:employee_id AND product_id=:product_id AND deleted_at IS NULL ORDER BY id DESC LIMIT 1");
			$insertMaterial=$pdo->prepare("INSERT INTO requestion_approval_detail(invoice_id,date,employee_id,project_id,product_id,requestion_quantity,final_quantity,final_rate,rate_attachment,created_by,created_at) VALUES (:invoice_id,:date,:employee_id,:project_id,:product_id,:requestion_quantity,:final_quantity,:final_rate,:rate_attachment,:created_by,:created_at)");
			$insertFund=$pdo->prepare("INSERT INTO requestion_approval_detail(invoice_id,date,employee_id,project_id,product_id,requestion_amount,final_amount,requestion_quantity,requistion_rate,final_quantity,final_rate,rate_attachment,created_by,created_at) VALUES (:invoice_id,:date,:employee_id,:project_id,:product_id,:requestion_amount,:final_amount,:requestion_quantity,:requistion_rate,:final_quantity,:final_rate,:rate_attachment,:created_by,:created_at)");
			$updateApproval=$pdo->prepare("UPDATE requestion_approval_detail SET final_amount=:final_amount,final_quantity=:final_quantity,final_rate=:final_rate,rate_attachment=:rate_attachment,updated_by=:updated_by,updated_at=:updated_at WHERE id=:id");
			$updateMaterialDetail=$pdo->prepare("UPDATE requestion_detail SET final_quantity=GREATEST(:final_quantity,COALESCE(emergency_quantity,0)),due_quantity=GREATEST(:due_quantity-COALESCE(emergency_quantity,0),0),final_rate=:final_rate WHERE id=:id AND invoice_id=:invoice_id AND deleted_at IS NULL");
			$updateFundDetail=$pdo->prepare("UPDATE requestion_detail SET final_amount=:final_amount,due_amount=:due_amount,final_quantity=:final_quantity,final_rate=:final_rate WHERE id=:id AND invoice_id=:invoice_id AND deleted_at IS NULL");

			for($i=1;$i<=$numberCount;$i++){
				if(empty($_POST['edit_id'.$i]) || empty($_POST['product_id'.$i])){
					continue;
				}
				$editId=(int)$_POST['edit_id'.$i];
				$productId=trim($_POST['product_id'.$i]);
				$requestionQuantity=isset($_POST['requestion_quantity'.$i]) ? trim($_POST['requestion_quantity'.$i]) : '';
				$finalQuantity=isset($_POST[($isFund ? 'quantity' : 'final_quantity').$i]) ? trim($_POST[($isFund ? 'quantity' : 'final_quantity').$i]) : '';
				$finalRate=isset($_POST[$rateFieldPrefix.$i]) ? trim($_POST[$rateFieldPrefix.$i]) : '';
				$requestionAmount=$isFund && isset($_POST['requestion_amount'.$i]) ? trim($_POST['requestion_amount'.$i]) : null;
				$finalAmount=$isFund && isset($_POST['final_amount'.$i]) ? trim($_POST['final_amount'.$i]) : null;
				$requistionRate=$isFund && isset($_POST['requistion_rate'.$i]) ? trim($_POST['requistion_rate'.$i]) : null;

				$findApproval->execute(array(':invoice_id'=>$invoice_id,':project_id'=>$project_id,':employee_id'=>$created_by,':product_id'=>$productId));
				$existing=$findApproval->fetch();
				$mergedAttachments=requestionApprovalMergeRateAttachments($existing ? $existing['rate_attachment'] : '',$uploadedFiles);
				if($existing){
					$updateApproval->execute(array(':final_amount'=>$finalAmount,':final_quantity'=>$finalQuantity,':final_rate'=>$finalRate,':rate_attachment'=>$mergedAttachments,':updated_by'=>$created_by,':updated_at'=>$created_at,':id'=>$existing['id']));
				}elseif($isFund){
					$insertFund->execute(array(':invoice_id'=>$invoice_id,':date'=>$current_date,':employee_id'=>$created_by,':project_id'=>$project_id,':product_id'=>$productId,':requestion_amount'=>$requestionAmount,':final_amount'=>$finalAmount,':requestion_quantity'=>$requestionQuantity,':requistion_rate'=>$requistionRate,':final_quantity'=>$finalQuantity,':final_rate'=>$finalRate,':rate_attachment'=>$mergedAttachments,':created_by'=>$created_by,':created_at'=>$created_at));
				}else{
					$insertMaterial->execute(array(':invoice_id'=>$invoice_id,':date'=>$current_date,':employee_id'=>$created_by,':project_id'=>$project_id,':product_id'=>$productId,':requestion_quantity'=>$requestionQuantity,':final_quantity'=>$finalQuantity,':final_rate'=>$finalRate,':rate_attachment'=>$mergedAttachments,':created_by'=>$created_by,':created_at'=>$created_at));
				}
				if($isFund){
					$updateFundDetail->execute(array(':final_amount'=>$finalAmount,':due_amount'=>$finalAmount,':final_quantity'=>$finalQuantity,':final_rate'=>$finalRate,':id'=>$editId,':invoice_id'=>$invoice_id));
				}else{
					$updateMaterialDetail->execute(array(':final_quantity'=>$finalQuantity,':due_quantity'=>$finalQuantity,':final_rate'=>$finalRate,':id'=>$editId,':invoice_id'=>$invoice_id));
				}
			}
		}

		$result=requestionApprovalHandleDynamicAction(
			$pdo,$invoice_id,$project_id,$store_id,$note,$action,$forwardEmployeeId,$returnEmployeeId,$created_by,$created_at,$current_date,$current_time,
			isset($notificationConfig['base_url']) ? $notificationConfig['base_url'] : '',
			isset($notificationConfig['sms_send_url']) ? $notificationConfig['sms_send_url'] : '',
			isset($notificationConfig['apikey']) ? $notificationConfig['apikey'] : '',
			isset($notificationConfig['sender_id']) ? $notificationConfig['sender_id'] : '',
			isset($notificationConfig['organization_name']) ? $notificationConfig['organization_name'] : '',
			isset($notificationConfig['company_email_address']) ? $notificationConfig['company_email_address'] : '',
			$context,false
		);
		$pdo->commit();
		$notifyEmployeeId=!empty($result['notify_employee_id']) ? (int)$result['notify_employee_id'] : 0;
		if($notifyEmployeeId && (!isset($notificationConfig['send']) || $notificationConfig['send'])){
			try{
				requestionApprovalNotifyEmployee(
					$pdo,$notifyEmployeeId,$invoice_id,$project_id,
					isset($notificationConfig['base_url']) ? $notificationConfig['base_url'] : '',
					isset($notificationConfig['sms_send_url']) ? $notificationConfig['sms_send_url'] : '',
					isset($notificationConfig['apikey']) ? $notificationConfig['apikey'] : '',
					isset($notificationConfig['sender_id']) ? $notificationConfig['sender_id'] : '',
					isset($notificationConfig['organization_name']) ? $notificationConfig['organization_name'] : '',
					isset($notificationConfig['company_email_address']) ? $notificationConfig['company_email_address'] : '',
					$created_by
				);
			}catch(Throwable $notificationException){
				error_log('Requisition notification failed after commit: '.$notificationException->getMessage());
			}
		}
		return array('ok'=>true,'invoice_id'=>$invoice_id,'action'=>$result['action']);
	}catch(Throwable $exception){
		if($pdo->inTransaction()){
			$pdo->rollBack();
		}
		requestionApprovalDeleteUploadedFiles($uploadedFiles);
		error_log('Requisition approval failed: '.$exception->getMessage());
		return array(
			'ok'=>false,
			'invoice_id'=>$invoice_id,
			'message'=>$exception instanceof DomainException ? $exception->getMessage() : 'The requisition action could not be completed. No changes were saved.'
		);
	}
}
}

if(isset($_POST["Super_Admin_Requestion_History_Edit"]) && authCanEditRequisitionHistory($LoginReGiSterSession)){
		$invoice_id=isset($_POST["invoice_id"]) ? trim($_POST["invoice_id"]) : (!empty($MenuName) ? trim($MenuName) : '');
		if(empty($_POST["super_admin_form_complete"])){
			$_SESSION['warning_message']='The requisition edit form was not fully submitted. Production PHP input limit may be too low. Please refresh and try again; if it repeats, increase max_input_vars/post_max_size on the server.';
			echo "<script>window.open('?Requestion_History_Detail/".htmlspecialchars($invoice_id,ENT_QUOTES,'UTF-8')."','_self')</script>";
			exit;
		}
		try{
			requestionApprovalVerifyCsrf(isset($_POST['requestion_approval_csrf']) ? $_POST['requestion_approval_csrf'] : '');
		}catch(DomainException $csrfException){
			$_SESSION['warning_message']=$csrfException->getMessage();
			echo "<script>window.open('?Requestion_History_Detail/".htmlspecialchars($invoice_id,ENT_QUOTES,'UTF-8')."','_self')</script>";
			exit;
		}
		$project_id=trim($_POST["project_id"]);
	$store_id=trim($_POST["store_id"]);
	$date=date("Y-m-d", strtotime($_POST["date"]));
		$note=!empty($_POST["note"]) ? trim($_POST["note"]) : "";
		$previous_cash_in_hand=!empty($_POST["previous_cash_in_hand"]) ? trim($_POST["previous_cash_in_hand"]) : "";
		$requistion_type=!empty($_POST["requistion_type"]) ? $_POST["requistion_type"] : "";
		$number_count=!empty($_POST["number_count"]) ? (int)$_POST["number_count"] : 0;
		$superAdminChangedRates=array();
		$superAdminProofFiles=array();
		$rateCheck=$pdo->prepare("SELECT product_id,requistion_rate,final_rate FROM requestion_detail WHERE id=:id AND invoice_id=:invoice_id AND deleted_at IS NULL LIMIT 1");
		for($i=1;$i<=$number_count;$i++){
			if(empty($_POST["edit_id".$i])){
				$newProductId=!empty($_POST["product_id".$i]) ? trim($_POST["product_id".$i]) : '';
				$newRequestRate=isset($_POST["requistion_rate".$i]) ? trim($_POST["requistion_rate".$i]) : '';
				$newFinalRate=isset($_POST["final_rate".$i]) ? trim($_POST["final_rate".$i]) : '';
				if($newProductId!=='' && (($newRequestRate!=='' && (float)$newRequestRate!==0.0) || ($newFinalRate!=='' && (float)$newFinalRate!==0.0))){
					$superAdminChangedRates[]=array(
						'product_id'=>$newProductId,
						'requestion_rate'=>$newRequestRate,
						'final_rate'=>$newFinalRate,
						'requestion_quantity'=>isset($_POST["requestion_quantity".$i]) ? trim($_POST["requestion_quantity".$i]) : '',
						'final_quantity'=>isset($_POST["final_quantity".$i]) ? trim($_POST["final_quantity".$i]) : ''
					);
				}
				continue;
			}
			$rateCheck->execute(array(':id'=>(int)$_POST["edit_id".$i],':invoice_id'=>$invoice_id));
			$storedRate=$rateCheck->fetch();
			if(!$storedRate){
				continue;
			}
			$submittedRequestRate=isset($_POST["requistion_rate".$i]) ? trim($_POST["requistion_rate".$i]) : '';
			$submittedFinalRate=isset($_POST["final_rate".$i]) ? trim($_POST["final_rate".$i]) : '';
			if(requestionApprovalRatesDiffer($submittedRequestRate,$storedRate['requistion_rate']) || requestionApprovalRatesDiffer($submittedFinalRate,$storedRate['final_rate'])){
				$superAdminChangedRates[]=array(
					'product_id'=>$storedRate['product_id'],
					'requestion_rate'=>$submittedRequestRate,
					'final_rate'=>$submittedFinalRate,
					'requestion_quantity'=>isset($_POST["requestion_quantity".$i]) ? trim($_POST["requestion_quantity".$i]) : '',
					'final_quantity'=>isset($_POST["final_quantity".$i]) ? trim($_POST["final_quantity".$i]) : ''
				);
			}
		}
		if(!empty($superAdminChangedRates)){
			if(!requestionApprovalColumnExists($pdo,'requestion_approval_detail','rate_attachment')){
				$_SESSION['warning_message']='Rate proof storage is not installed. Run the requisition approval database migration first.';
				echo "<script>window.open('?Requestion_History_Detail/".htmlspecialchars($invoice_id,ENT_QUOTES,'UTF-8')."','_self')</script>";
				exit;
			}
			$superAdminUpload=isset($_FILES['super_admin_rate_attachment']) ? $_FILES['super_admin_rate_attachment'] : array();
			$superAdminProofFiles=requestionApprovalUploadRateAttachment($superAdminUpload);
			if(empty($superAdminProofFiles)){
				$_SESSION['warning_message']='Rate পরিবর্তন করলে সফলভাবে upload করা proof file বাধ্যতামূলক।';
				echo "<script>window.open('?Requestion_History_Detail/".htmlspecialchars($invoice_id,ENT_QUOTES,'UTF-8')."','_self')</script>";
				exit;
			}
		}

		try{
		$pdo->beginTransaction();
		$history_statement=$pdo->prepare("UPDATE requestion_histiory SET project_id=:project_id,store_id=:store_id,date=:date,note=:note,previous_cash_in_hand=:previous_cash_in_hand,updated_by=:updated_by,updated_at=:updated_at WHERE invoice_id=:invoice_id AND deleted_at IS NULL");
	$history_statement->execute(array(
		':project_id'=>$project_id,
		':store_id'=>$store_id,
		':date'=>$date,
		':note'=>$note,
		':previous_cash_in_hand'=>$previous_cash_in_hand,
		':updated_by'=>$LoginReGiSterSession,
		':updated_at'=>$current_time,
		':invoice_id'=>$invoice_id
	));

	$sync_detail=$pdo->prepare("UPDATE requestion_detail SET project_id=:project_id,store_id=:store_id,date=:date,updated_by=:updated_by,updated_at=:updated_at WHERE invoice_id=:invoice_id AND deleted_at IS NULL");
	$sync_detail->execute(array(
		':project_id'=>$project_id,
		':store_id'=>$store_id,
		':date'=>$date,
		':updated_by'=>$LoginReGiSterSession,
		':updated_at'=>$current_time,
		':invoice_id'=>$invoice_id
	));

	$sync_approval=$pdo->prepare("UPDATE project_material_aproval_status SET project_id=:project_id WHERE invoice_id=:invoice_id AND deleted_at IS NULL");
	$sync_approval->execute(array(':project_id'=>$project_id, ':invoice_id'=>$invoice_id));

	$deleted_detail_ids=array();
	if(!empty($_POST["delete_detail"]) && is_array($_POST["delete_detail"])){
		$delete_statement=$pdo->prepare("UPDATE requestion_detail SET deleted_by=:deleted_by,deleted_at=:deleted_at WHERE id=:id AND invoice_id=:invoice_id");
		foreach($_POST["delete_detail"] as $delete_id){
			if(!empty($delete_id)){
				$deleted_detail_ids[]=(int)$delete_id;
				$delete_statement->execute(array(
					':deleted_by'=>$LoginReGiSterSession,
					':deleted_at'=>$current_time,
					':id'=>(int)$delete_id,
					':invoice_id'=>$invoice_id
				));
			}
		}
	}

	$existing_statement=$pdo->prepare("SELECT * FROM requestion_detail WHERE id=:id AND invoice_id=:invoice_id LIMIT 1");
	$update_statement=$pdo->prepare("UPDATE requestion_detail SET product_id=:product_id,detail=:detail,comment=:comment,requestion_quantity=:requestion_quantity,requistion_rate=:requistion_rate,final_quantity=:final_quantity,final_rate=:final_rate,requestion_amount=:requestion_amount,final_amount=:final_amount,due_quantity=:due_quantity,due_amount=:due_amount,project_id=:project_id,store_id=:store_id,date=:date,updated_by=:updated_by,updated_at=:updated_at,deleted_at=NULL WHERE id=:id AND invoice_id=:invoice_id");
	$insert_statement=$pdo->prepare("INSERT INTO requestion_detail(invoice_id,date,employee_id,project_id,store_id,product_id,detail,comment,requestion_quantity,requistion_rate,final_quantity,final_rate,due_quantity,requestion_amount,final_amount,due_amount,created_by,created_at) VALUES (:invoice_id,:date,:employee_id,:project_id,:store_id,:product_id,:detail,:comment,:requestion_quantity,:requistion_rate,:final_quantity,:final_rate,:due_quantity,:requestion_amount,:final_amount,:due_amount,:created_by,:created_at)");

		for($i=1;$i<=$number_count;$i++){
		$product_id=!empty($_POST["product_id".$i]) ? trim($_POST["product_id".$i]) : "";
		if(empty($product_id)){
			continue;
		}

		$detail=!empty($_POST["detail".$i]) ? trim($_POST["detail".$i]) : "";
		$comment=!empty($_POST["comment".$i]) ? trim($_POST["comment".$i]) : "";
		$requestion_quantity=!empty($_POST["requestion_quantity".$i]) ? trim($_POST["requestion_quantity".$i]) : "";
		$requistion_rate=!empty($_POST["requistion_rate".$i]) ? trim($_POST["requistion_rate".$i]) : "";
		$final_quantity=!empty($_POST["final_quantity".$i]) ? trim($_POST["final_quantity".$i]) : $requestion_quantity;
		$final_rate=!empty($_POST["final_rate".$i]) ? trim($_POST["final_rate".$i]) : $requistion_rate;
		$requestion_amount=!empty($_POST["requestion_amount".$i]) ? trim($_POST["requestion_amount".$i]) : "";
		$final_amount=!empty($_POST["final_amount".$i]) ? trim($_POST["final_amount".$i]) : "";

		if($requistion_type!='Fund'){
			$requestion_amount=($requestion_quantity!=='' && $requistion_rate!=='') ? ((float)$requestion_quantity*(float)$requistion_rate) : "";
			$final_amount=($final_quantity!=='' && $final_rate!=='') ? ((float)$final_quantity*(float)$final_rate) : "";
		}

		$edit_id=!empty($_POST["edit_id".$i]) ? (int)$_POST["edit_id".$i] : 0;
		if($edit_id>0 && in_array($edit_id, $deleted_detail_ids)){
			continue;
		}
		$distribution_quantity=0;
		$distribution_amount=0;
		$employee_id=$LoginReGiSterSession;
		if($edit_id>0){
			$existing_statement->execute(array(':id'=>$edit_id, ':invoice_id'=>$invoice_id));
			$row_existing=$existing_statement->fetch();
			if(!empty($row_existing)){
				$distribution_quantity=(float)$row_existing["distribution_quantity"];
				$distribution_amount=(float)$row_existing["distribution_amount"];
				$employee_id=$row_existing["employee_id"];
			}
		}

		$due_quantity=($final_quantity!=='') ? max(0, (float)$final_quantity-$distribution_quantity) : "";
		$due_amount=($final_amount!=='') ? max(0, (float)$final_amount-$distribution_amount) : "";

		$params=array(
			':product_id'=>$product_id,
			':detail'=>$detail,
			':comment'=>$comment,
			':requestion_quantity'=>$requestion_quantity,
			':requistion_rate'=>$requistion_rate,
			':final_quantity'=>$final_quantity,
			':final_rate'=>$final_rate,
			':requestion_amount'=>$requestion_amount,
			':final_amount'=>$final_amount,
			':due_quantity'=>$due_quantity,
			':due_amount'=>$due_amount,
			':project_id'=>$project_id,
			':store_id'=>$store_id,
			':date'=>$date
		);

		if($edit_id>0){
			$params[':updated_by']=$LoginReGiSterSession;
			$params[':updated_at']=$current_time;
			$params[':id']=$edit_id;
			$params[':invoice_id']=$invoice_id;
			$update_statement->execute($params);
		}else{
			$params[':invoice_id']=$invoice_id;
			$params[':employee_id']=$employee_id;
			$params[':created_by']=$LoginReGiSterSession;
			$params[':created_at']=$current_time;
			$insert_statement->execute($params);
			}
		}

		if(!empty($superAdminChangedRates)){
			$findProofRow=$pdo->prepare("SELECT id,rate_attachment FROM requestion_approval_detail WHERE invoice_id=:invoice_id AND project_id=:project_id AND employee_id=:employee_id AND product_id=:product_id AND deleted_at IS NULL ORDER BY id DESC LIMIT 1");
			$insertProofRow=$pdo->prepare("INSERT INTO requestion_approval_detail(invoice_id,date,employee_id,project_id,product_id,requestion_quantity,requistion_rate,final_quantity,final_rate,rate_attachment,created_by,created_at) VALUES (:invoice_id,:date,:employee_id,:project_id,:product_id,:requestion_quantity,:requistion_rate,:final_quantity,:final_rate,:rate_attachment,:created_by,:created_at)");
			$updateProofRow=$pdo->prepare("UPDATE requestion_approval_detail SET requestion_quantity=:requestion_quantity,requistion_rate=:requistion_rate,final_quantity=:final_quantity,final_rate=:final_rate,rate_attachment=:rate_attachment,updated_by=:updated_by,updated_at=:updated_at WHERE id=:id");
			foreach($superAdminChangedRates as $changedRate){
				$findProofRow->execute(array(':invoice_id'=>$invoice_id,':project_id'=>$project_id,':employee_id'=>$LoginReGiSterSession,':product_id'=>$changedRate['product_id']));
				$existingProof=$findProofRow->fetch();
				$mergedProof=requestionApprovalMergeRateAttachments($existingProof ? $existingProof['rate_attachment'] : '',$superAdminProofFiles);
				$proofParams=array(
					':requestion_quantity'=>$changedRate['requestion_quantity'],
					':requistion_rate'=>$changedRate['requestion_rate'],
					':final_quantity'=>$changedRate['final_quantity'],
					':final_rate'=>$changedRate['final_rate'],
					':rate_attachment'=>$mergedProof
				);
				if($existingProof){
					$proofParams[':updated_by']=$LoginReGiSterSession;
					$proofParams[':updated_at']=$current_time;
					$proofParams[':id']=$existingProof['id'];
					$updateProofRow->execute($proofParams);
				}else{
					$proofParams[':invoice_id']=$invoice_id;
					$proofParams[':date']=$current_date;
					$proofParams[':employee_id']=$LoginReGiSterSession;
					$proofParams[':project_id']=$project_id;
					$proofParams[':product_id']=$changedRate['product_id'];
					$proofParams[':created_by']=$LoginReGiSterSession;
					$proofParams[':created_at']=$current_time;
					$insertProofRow->execute($proofParams);
				}
			}
		}
		$pdo->commit();
		$_SESSION['success_message']="Requisition history updated successfully.";
		echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>";
		exit;
		}catch(Throwable $superAdminEditException){
			if($pdo->inTransaction()){
				$pdo->rollBack();
			}
			requestionApprovalDeleteUploadedFiles($superAdminProofFiles);
			error_log('Super admin requisition edit failed: '.$superAdminEditException->getMessage());
			$_SESSION['warning_message']='Requisition edit could not be saved. No changes were applied.';
			echo "<script>window.open('?Requestion_History_Detail/".htmlspecialchars($invoice_id,ENT_QUOTES,'UTF-8')."','_self')</script>";
			exit;
		}
	}
//HR Start
if(isset($_POST["Employee_Information_Create"])){
		
		// Array Insert Loop Start
	if(!empty($_FILES['photo']['name'])){
		    
	  $temp = explode(".", $_FILES['photo']['name']);
      $newfilename = $temp["0"].round(microtime(true)) . '.' . end($temp);
      $file_tmp= $_FILES['photo']['tmp_name'];
      move_uploaded_file($file_tmp, "HRPhoto/" . $newfilename);
	  $image_field_name=',`photo`';
	  $image_value=",'".$newfilename."'";
	  }else{
		$image_field_name='';
	    $image_value="";  
		  }
		
		
		 if(!empty($_FILES['hr_cv']['name'])){
		    
	  $temp = explode(".", $_FILES['hr_cv']['name']);
      $newfilenamehr_cv = $temp["0"].round(microtime(true)) . '.' . end($temp);
      $file_tmp= $_FILES['hr_cv']['tmp_name'];
      move_uploaded_file($file_tmp, "HRCV/" . $newfilenamehr_cv);
	  $image_field_name_cv=',`hr_cv`';
	  $image_value_cv=",'".$newfilenamehr_cv."'";
	  }else{
		$image_field_name_cv='';
	    $image_value_cv="";  
		  }
		
		
	$Employee_information_Verify=$pdo->query("SELECT * FROM  employee_information where mobile='".$_POST["mobile"]."' and deleted_at is NULL");
$rowEmployee_information_Verify= $Employee_information_Verify->fetch();
if(empty($rowEmployee_information_Verify)){
		
    
      $copyArray=$_POST;
      $sliced = array_slice($copyArray, 0, -1);
      $sql="INSERT INTO  `employee_information` (`".implode( "`,`", array_keys( $sliced ) ) ."`,`created_by`,`created_at`$image_field_name$image_field_name_cv) values (:".implode(",:",array_keys( $sliced ) ).",'$created_by','$current_time'$image_value$image_value_cv);";

     foreach( $sliced as $field => $value ) $params[":{$field}"]=$value;

     $statement = $pdo->prepare( $sql );
     $statement->execute( $params );
	 
	 $_SESSION['success_message']=$success_message_data;
    echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 
	}else{
	$_SESSION['warning_message']=$failure_message_data;
	echo "<script>window.open('?$page_title','_self')</script>";	

	}
	   
	 
	 

	}
	
if(isset($_POST["Leave_information_Create"])){
    
 $employee_id=$_POST["employee_id"];
 $leave_type=$_POST["leave_type"]; 
 $total_used_leave=$_POST["total_used_leave"]; 
 $available_leave=$_POST["available_leave"]; 
 $start_date=$_POST["start_date"]; 
 $end_date=$_POST["end_date"]; 
 
$date1=date_create($start_date);
$date2=date_create($end_date);
$diff=date_diff($date1,$date2);
//echo $diff->format("%R%a days");
$date_number=($diff->format("%a"))+1;

 $Information = $pdo->query("SELECT * FROM `hr_leave_information` WHERE employee_id='$employee_id' and  start_date='$start_date' and DELETED_AT is NULL");
$rowDataInformation= $Information->fetch();

if(empty($rowDataInformation)){

for($serial=1;$serial<=$date_number;$serial++){

if($serial==1){
$date_name=$start_date;      
}else{
$date_name=date('Y-m-d', strtotime($start_date. '+ '.$serial.' days'));    
} 

 $pdo->query("INSERT INTO `hr_leave_detial_information`(`employee_id`, `leave_type`, `leave_date`, `date`, `created_by`,`created_at`) VALUES ('$employee_id','$leave_type','$date_name','$current_date','$created_by','$current_time')  "); 
  
}
$total_used_leave_number=$total_used_leave+$date_number;
$total_available_leave=$available_leave-$date_number;

 $current_year=date("Y");
 $pdo->query("INSERT INTO `hr_leave_information`(`employee_id`, `leave_type`, `start_date`, `end_date`, `leave_days`, `total_used_leave`, `available_leave`, `current_year`, `created_by`, `created_at`) VALUES ('$employee_id','$leave_type','$start_date','$end_date','$date_number','$total_used_leave_number','$total_available_leave','$current_year','$created_by','$current_time')");  
 
 
 
  $_SESSION['success_message']=$success_message_data;
    echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 
}else{
    
 	$_SESSION['warning_message']=$failure_message_data;
	echo "<script>window.open('?$page_title/$MenuName','_self')</script>";   
}
 
    
}	
	
	
	

//HR End




// User Registration Start
if(isset($_POST["User_Registration_Start"])){
		
		// Array Insert Loop Start
	$InsertDataVerification = $pdo->query("SELECT COUNT(*) FROM user_login where email='".$_POST["email"]."' and deleted_at is NULL");
$DataVerificationNumber=$InsertDataVerification->fetchColumn();
if($DataVerificationNumber==0){
      if(!empty($_FILES['photo']['name'])){
		    
	  $temp = explode(".", $_FILES['photo']['name']);
      $newfilename = $temp["0"].round(microtime(true)) . '.' . end($temp);
      $file_tmp= $_FILES['photo']['tmp_name'];
      move_uploaded_file($file_tmp, "../image/" . $newfilename);
	  $image_field_name=',`photo`';
	  $image_value=",'".$newfilename."'";
	  }else{
		$image_field_name='';
	    $image_value="";  
		  }
		
		if(!empty($_POST["no_value"])){
		$new_password = password_hash($_POST["no_value"], PASSWORD_DEFAULT);	
		$password_name=',`password`';	
		$password_value=",'".$new_password."'";	
		}else{
		$password_name='';	
		$password_value="";		
		}
		
    
      $copyArray=$_POST;
      $sliced = array_slice($copyArray, 0, -1);
      $sql="INSERT INTO  `user_login` (`".implode( "`,`", array_keys( $sliced ) ) ."`,`created_by`,`created_at`$image_field_name$password_name) values (:".implode(",:",array_keys( $sliced ) ).",'$LoginReGiSterSession','$current_time'$image_value$password_value);";

     foreach( $sliced as $field => $value ) $params[":{$field}"]=$value;

     $statement = $pdo->prepare( $sql );
     $statement->execute( $params );
	 
	 $_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 
	 
	   
	  }else{
	$_SESSION['warning_message']=$failure_message_data;
	echo "<script>window.open('?$page_title.'_Create/'$MenuName','_self')</script>";	
	}
	 

	}

// User Registration End





// Others Registration Start
	/*if(isset($_POST["Insert_all"])){
		
		// Array Insert Loop Start
	
      if(!empty($_FILES['photo']['name'])){
		    
	  $temp = explode(".", $_FILES['photo']['name']);
      $newfilename = $temp["0"].round(microtime(true)) . '.' . end($temp);
      $file_tmp= $_FILES['photo']['tmp_name'];
      move_uploaded_file($file_tmp, "../image/" . $newfilename);
	  $image_field_name=',`photo`';
	  $image_value=",'".$newfilename."'";
	  }else{
		$image_field_name='';
	    $image_value="";  
		  }
		
		if(!empty($_POST["no_value"])){
		$new_password = password_hash($_POST["no_value"], PASSWORD_DEFAULT);	
		$password_name=',`password`';	
		$password_value=",'".$new_password."'";	
		}else{
		$password_name='';	
		$password_value="";		
		}
		
    
      $copyArray=$_POST;
      $sliced = array_slice($copyArray, 0, -1);
      $sql="INSERT INTO  `$database_table` (`".implode( "`,`", array_keys( $sliced ) ) ."`,`created_by`,`created_at`$image_field_name$password_name) values (:".implode(",:",array_keys( $sliced ) ).",'$personal_information_edit_id','$current_time'$image_value$password_value);";

     foreach( $sliced as $field => $value ) $params[":{$field}"]=$value;

     $statement = $pdo->prepare( $sql );
     $statement->execute( $params );
	 
	 $_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 
	 
	   
	 
	 

	}*/


	if(isset($_POST["Insert_all"])){
		
		// Array Insert Loop Start
	
      if(!empty($_FILES['photo']['name'])){
		    
	  $temp = explode(".", $_FILES['photo']['name']);
      $newfilename = $temp["0"].round(microtime(true)) . '.' . end($temp);
      $file_tmp= $_FILES['photo']['tmp_name'];
      move_uploaded_file($file_tmp, "../image/" . $newfilename);
	  $image_field_name=',`photo`';
	  $image_value=",'".$newfilename."'";
	  }else{
		$image_field_name='';
	    $image_value="";  
		  }
		
		if(!empty($_POST["no_value"])){
		$new_password = password_hash($_POST["no_value"], PASSWORD_DEFAULT);	
		$password_name=',`password`';	
		$password_value=",'".$new_password."'";	
		}else{
		$password_name='';	
		$password_value="";		
		}
		
    
      $copyArray=$_POST;
      $sliced = array_slice($copyArray, 0, -1);
      $sql="INSERT INTO  `$DocumentData` (`".implode( "`,`", array_keys( $sliced ) ) ."`,`created_by`,`created_at`$image_field_name$password_name) values (:".implode(",:",array_keys( $sliced ) ).",'$LoginReGiSterSession','$current_time'$image_value$password_value);";

     foreach( $sliced as $field => $value ) $params[":{$field}"]=$value;

     $statement = $pdo->prepare( $sql );
     $statement->execute( $params );
	 
	 $_SESSION['success_message']=$success_message_data;
    echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 
	 
	   
	 
	 

	}

// Other Registration End


//  Purchase Start
if(isset($_POST["Insert_Purchase_History"])){

	$purchase_type = isset($_POST["purchase_type"]) && $_POST["purchase_type"] === 'with_requisition'
		? 'with_requisition'
		: 'without_requisition';
	$requisition_invoice_id = $purchase_type === 'with_requisition'
		? trim($_POST["requisition_invoice_id"] ?? '')
		: null;
		$supplier_id=$_POST["supplier_id"];
	$date=$_POST["date"];
	$note=$_POST["note"];
	$store_id=$_POST["store_id"];
		$amount=0;
		$payment_amount=(float)$_POST["payment_amount"];
	    $date=date("Y-m-d", strtotime($_POST["date"]));
	$purchaseLockAcquired = false;
	$purchaseSaveError = function ($message) use (&$purchaseLockAcquired, $pdo) {
		if ($pdo->inTransaction()) {
			$pdo->rollBack();
		}
		if ($purchaseLockAcquired) {
			$pdo->query("SELECT RELEASE_LOCK('inventory_purchase_order_number')");
			$purchaseLockAcquired = false;
		}
		$_SESSION['warning_message'] = $message;
		echo '<script>history.back();</script>';
		exit;
	};

	$approvedRequisitionItems = array();
	if ($purchase_type === 'with_requisition') {
		if ($requisition_invoice_id === '') {
			$purchaseSaveError('Please select a fully approved requisition.');
		}

		$requisitionCheck = $pdo->prepare(
			"SELECT requestion_histiory.store_id
			 FROM requestion_histiory
			 LEFT JOIN purchase_history ON purchase_history.requisition_invoice_id = requestion_histiory.invoice_id
			                           AND purchase_history.deleted_at IS NULL
			 WHERE requestion_histiory.invoice_id = :invoice_id
			   AND requestion_histiory.approval_status = 'Approve'
			   AND requestion_histiory.deleted_at IS NULL
			   AND purchase_history.id IS NULL
			   AND EXISTS (
			       SELECT 1 FROM project_material_aproval_status approved_step
			       WHERE approved_step.invoice_id = requestion_histiory.invoice_id
			         AND TRIM(approved_step.approval_status) = 'Approve'
			         AND approved_step.deleted_at IS NULL
			   )
			   AND NOT EXISTS (
			       SELECT 1 FROM project_material_aproval_status incomplete_step
			       WHERE incomplete_step.invoice_id = requestion_histiory.invoice_id
			         AND COALESCE(TRIM(incomplete_step.approval_status), '') <> 'Approve'
			         AND incomplete_step.deleted_at IS NULL
			   )
			 LIMIT 1"
		);
		$requisitionCheck->execute(array(':invoice_id' => $requisition_invoice_id));
		$approvedRequisition = $requisitionCheck->fetch();
		if (!$approvedRequisition) {
			$purchaseSaveError('The selected requisition is not fully approved, has already been purchased, or is unavailable.');
		}
		if ((string)$approvedRequisition['store_id'] !== (string)$store_id) {
			$purchaseSaveError('The purchase store must match the requisition store.');
		}

		$requisitionItemsStatement = $pdo->prepare(
			"SELECT product_information.name,
			        COALESCE(NULLIF(requestion_detail.final_quantity, ''), requestion_detail.requestion_quantity) AS quantity
			 FROM requestion_detail
			 INNER JOIN product_information ON requestion_detail.product_id = product_information.id
			 WHERE requestion_detail.invoice_id = :invoice_id
			   AND requestion_detail.deleted_at IS NULL
			   AND CAST(COALESCE(NULLIF(requestion_detail.final_quantity, ''), requestion_detail.requestion_quantity, 0) AS DECIMAL(18,4)) > 0
			 ORDER BY requestion_detail.id ASC"
		);
		$requisitionItemsStatement->execute(array(':invoice_id' => $requisition_invoice_id));
		$approvedRequisitionItems = $requisitionItemsStatement->fetchAll();
		if (!$approvedRequisitionItems) {
			$purchaseSaveError('The selected requisition has no approved products.');
		}
	}
	$DoctorPHOTO = null;


	
if(!empty($_FILES['photo']['name'])){
	  $tempPHOTO = explode(".", $_FILES['photo']['name']);
      $DoctorPHOTO = $tempPHOTO["0"].round(microtime(true)) . '.' . end($tempPHOTO);
      $file_tmpPHOTO= $_FILES['photo']['tmp_name'];
      move_uploaded_file($file_tmpPHOTO, "PurchaseHistory/" . $DoctorPHOTO);
	  $image_field_namePHOTO=',photo';
	  $image_valuePHOTO=",'".$DoctorPHOTO."'";
	  }else{
		$image_field_namePHOTO='';
	    $image_valuePHOTO="";  
		  }
	

	
	$purchaseNumberLock = $pdo->query("SELECT GET_LOCK('inventory_purchase_order_number', 10) AS acquired")->fetch();
	if (empty($purchaseNumberLock['acquired'])) {
		$purchaseSaveError('Another purchase is being saved. Please try again shortly.');
	}
	$purchaseLockAcquired = true;
		$invoice_information = $pdo->query("select * from purchase_history order by id DESC limit 0,1");
    $invoicerowdata = $invoice_information->fetch();
		if(empty($invoicerowdata["invoice_id"])){
			$invoice_id="10001";
		}else{
			$invoice_id=$invoicerowdata["invoice_id"]+1;
		}
	$purchase_id = 'PO-' . date('Y') . '-' . str_pad($invoice_id, 5, '0', STR_PAD_LEFT);

	
	$resultitle = array();
	$transaction_title='';
	$multiple_title = '[]';
	
	try {
	$pdo->beginTransaction();
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	$approvedRequisitionQuantities = array();
	foreach ($approvedRequisitionItems as $approvedRequisitionItem) {
		$approvedName = (string)$approvedRequisitionItem['name'];
		$approvedQuantity = (float)$approvedRequisitionItem['quantity'];
		if (!isset($approvedRequisitionQuantities[$approvedName]) || $approvedQuantity > $approvedRequisitionQuantities[$approvedName]) {
			$approvedRequisitionQuantities[$approvedName] = $approvedQuantity;
		}
	}
		for($i=1;$i<=$number_count;$i++){
			if (!isset($_POST["name".$i]) || trim($_POST["name".$i]) === '') {
				continue;
			}

			$isRequisitionItem = $purchase_type === 'with_requisition'
				&& isset($_POST["requisition_item".$i])
				&& $_POST["requisition_item".$i] === '1';
			if ($isRequisitionItem) {
				$submittedName = trim(strip_tags($_POST["name".$i]));
				if (!isset($approvedRequisitionQuantities[$submittedName])) {
					$purchaseSaveError('A submitted product does not match the approved requisition.');
				}
				$name_input = $submittedName;
				$approved_quantity = $approvedRequisitionQuantities[$submittedName];
				$quantity = isset($_POST["quantity".$i]) && $_POST["quantity".$i] !== '' ? (float)$_POST["quantity".$i] : 0;
				if ($quantity > $approved_quantity) {
					$purchaseSaveError('Purchase quantity cannot exceed the approved requisition quantity.');
				}
			} else {
				$name_input=trim(strip_tags($_POST["name".$i]));
				$quantity=isset($_POST["quantity".$i]) && $_POST["quantity".$i] !== '' ? (float)$_POST["quantity".$i] : 0;
			}
			$rate=isset($_POST["rate".$i]) && $_POST["rate".$i] !== '' ? (float)$_POST["rate".$i] : 0;
			if ($quantity < 0 || $rate < 0) {
				$purchaseSaveError('Product quantity and rate cannot be negative.');
			}
			$total_amount=round($quantity * $rate, 2);
			$amount += $total_amount;
		
		$productLookupStatement = $pdo->prepare("SELECT * FROM `product_information` WHERE name = :name AND deleted_at IS NULL LIMIT 1");
		$productLookupStatement->execute(array(':name' => $name_input));
		$rowdataserach = $productLookupStatement->fetch();
		if(empty($rowdataserach)){
		$productCreateStatement = $pdo->prepare("INSERT INTO `product_information`(`name`,`created_by`,`created_at`) VALUES (:name,:created_by,:created_at)");
		$productCreateStatement->execute(array(':name' => $name_input, ':created_by' => $created_by, ':created_at' => $created_at));
	     }


		$productLookupStatement->execute(array(':name' => $name_input));
        $rowdataserach_output = $productLookupStatement->fetch();
		$name_id=$rowdataserach_output["id"];
		$unit=$rowdataserach_output["unit"];
	
		
		$stockStatement = $pdo->prepare("SELECT * FROM `stock_information` WHERE product_id = :product_id AND store_id = :store_id LIMIT 1");
		$stockStatement->execute(array(':product_id' => $name_id, ':store_id' => $store_id));
        $rowdataProduct_information = $stockStatement->fetch();
		
		if(!empty($rowdataProduct_information["product_id"])){
			
		$product_primary_id=$rowdataProduct_information["id"];	
		$new_previous=$rowdataProduct_information["new"];
		$total_previous=$rowdataProduct_information["total"];
		$stock_previous=$rowdataProduct_information["stock"];
			
			
			$new_current=$new_previous+$quantity;
			$new_total=$total_previous+$quantity;
			$stock_current=$stock_previous+$quantity;
			
		$stockUpdateStatement = $pdo->prepare("UPDATE `stock_information` SET new = :new_quantity,total = :total_quantity,stock = :stock_quantity WHERE id = :id");
		$stockUpdateStatement->execute(array(':new_quantity' => $new_current, ':total_quantity' => $new_total, ':stock_quantity' => $stock_current, ':id' => $product_primary_id));
			
		$purchaseDetailStatement = $pdo->prepare("INSERT INTO `purchase_detail`(`invoice_id`, `purchase_id`, `supplier_id`, `store_id`, `date`, `product_id`, `product_name`, `unit`, `before_quantity`, `after_quantity`, `quantity`, `rate`, `note`, `amount`,`created_by`,`created_at`) VALUES (:invoice_id,:purchase_id,:supplier_id,:store_id,:date,:product_id,:product_name,:unit,:before_quantity,:after_quantity,:quantity,:rate,:note,:amount,:created_by,:created_at)");
		$purchaseDetailStatement->execute(array(':invoice_id'=>$invoice_id, ':purchase_id'=>$purchase_id, ':supplier_id'=>$supplier_id, ':store_id'=>$store_id, ':date'=>$date, ':product_id'=>$name_id, ':product_name'=>$name_input, ':unit'=>$unit, ':before_quantity'=>$stock_previous, ':after_quantity'=>$quantity, ':quantity'=>$stock_current, ':rate'=>$rate, ':note'=>$note, ':amount'=>$total_amount, ':created_by'=>$created_by, ':created_at'=>$created_at));
			
			
		}else{
		$stockCreateStatement = $pdo->prepare("INSERT INTO `stock_information`(`store_id`, `product_id`,`new`, `total`, `stock`, `created_by`, `created_at`) VALUES (:store_id,:product_id,:new_quantity,:total_quantity,:stock_quantity,:created_by,:created_at)");
		$stockCreateStatement->execute(array(':store_id'=>$store_id, ':product_id'=>$name_id, ':new_quantity'=>$quantity, ':total_quantity'=>$quantity, ':stock_quantity'=>$quantity, ':created_by'=>$created_by, ':created_at'=>$created_at));
		$purchaseDetailStatement = $pdo->prepare("INSERT INTO `purchase_detail`(`invoice_id`, `purchase_id`, `supplier_id`, `store_id`, `date`, `product_id`, `product_name`, `unit`, `before_quantity`, `after_quantity`, `quantity`, `rate`, `note`, `amount`,`created_by`,`created_at`) VALUES (:invoice_id,:purchase_id,:supplier_id,:store_id,:date,:product_id,:product_name,:unit,:before_quantity,:after_quantity,:quantity,:rate,:note,:amount,:created_by,:created_at)");
		$purchaseDetailStatement->execute(array(':invoice_id'=>$invoice_id, ':purchase_id'=>$purchase_id, ':supplier_id'=>$supplier_id, ':store_id'=>$store_id, ':date'=>$date, ':product_id'=>$name_id, ':product_name'=>$name_input, ':unit'=>$unit, ':before_quantity'=>0, ':after_quantity'=>$quantity, ':quantity'=>$quantity, ':rate'=>$rate, ':note'=>$note, ':amount'=>$total_amount, ':created_by'=>$created_by, ':created_at'=>$created_at));
		
		}
		
					
	    array_push($resultitle,array("name"=>$name_input,"unit"=>$unit,"quantity"=>$quantity,"rate"=>$rate,"total_amount"=>$total_amount));
		
				
   
	     }
		if (!$resultitle) {
			$purchaseSaveError('At least one product must be added to the purchase order.');
		}
	
	$multiple_title=json_encode($resultitle);	
	
	}
	if(!empty($supplier_id)){
	$informationsupplier = $pdo->prepare("SELECT * FROM `supplier_information` WHERE id = :supplier_id LIMIT 1");
        $informationsupplier->execute(array(':supplier_id' => $supplier_id));
        $rowdatasupplier = $informationsupplier->fetch();	
		
		if(!empty($rowdatasupplier["amount"])){
		   $supplier_current_balance=$rowdatasupplier["amount"];
		}else{
		    $supplier_current_balance=0;
		}
		
	$due_amount=$supplier_current_balance+$amount-$payment_amount;
		
	$supplierUpdateStatement = $pdo->prepare("UPDATE `supplier_information` SET amount = :amount WHERE id = :supplier_id");
	$supplierUpdateStatement->execute(array(':amount' => $due_amount, ':supplier_id' => $supplier_id));
		
	}else{
	$supplier_current_balance=0;
	$due_amount=0;	
		
	}
	
	
			$sql="INSERT INTO `purchase_history`(`invoice_id`, `purchase_id`, `purchase_type`, `requisition_invoice_id`, `supplier_id`, `store_id`, `date`, `note`, `photo`, `purchase_detail`, `previous_due_amount`, `billamount`, `payment_amount`, `due_amount`,`created_by`,`created_at`) VALUES (:invoice_id,:purchase_id,:purchase_type,:requisition_invoice_id,:supplier_id,:store_id,:date,:note,:photo,:purchase_detail,:previous_due_amount,:billamount,:payment_amount,:due_amount,:created_by,:created_at)";

     $insert_data= $pdo->prepare($sql);
	 $insert_data->bindparam(":invoice_id", $invoice_id);
	     $insert_data->bindparam(":purchase_id", $purchase_id);
	     $insert_data->bindparam(":purchase_type", $purchase_type);
	     $insert_data->bindparam(":requisition_invoice_id", $requisition_invoice_id);
	 $insert_data->bindparam(":supplier_id", $supplier_id);
	 $insert_data->bindparam(":store_id", $store_id);
	 $insert_data->bindparam(":date", $date);
	 $insert_data->bindparam(":note", $note);	 
	 $insert_data->bindparam(":photo", $DoctorPHOTO);
	 $insert_data->bindparam(":purchase_detail", $multiple_title);
	 $insert_data->bindparam(":previous_due_amount", $supplier_current_balance);
	 $insert_data->bindparam(":billamount", $amount);
		 $purchase_due_amount = $amount - $payment_amount;
		 $insert_data->bindparam(":payment_amount", $payment_amount);
		 $insert_data->bindparam(":due_amount", $purchase_due_amount);
	 $insert_data->bindparam(":created_by", $created_by);
	 $insert_data->bindparam(":created_at", $created_at);
		 $insert_data->execute();
	$pdo->commit();
	$pdo->query("SELECT RELEASE_LOCK('inventory_purchase_order_number')");
	$purchaseLockAcquired = false;
	} catch (Exception $purchaseException) {
		$purchaseSaveError('Purchase could not be saved. Please check the information and try again.');
	}
	
	
	
	
	 $_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?Purchase_HistoryDetail/$invoice_id','_self')</script>"; 	

	
}


//  Purchase End




//Requestion Start
if(isset($_POST["Insert_Requestion_History"])){



    $store_id=$_POST["store_id"];
	$project_id=$_POST["project_id"];
	$date=$_POST["date"];
	$note=$_POST["note"];
	$requistion_type=$_POST["requistion_type"];
    $date=date("Y-m-d", strtotime($_POST["date"]));


	
	$invoice_information = $pdo->query("select * from requestion_histiory order by id DESC limit 0,1");
    $invoicerowdata = $invoice_information->fetch();
	if(empty($invoicerowdata["invoice_id"])){ 
	$invoice_id="10001";
	}else{ 
	$invoice_id=$invoicerowdata["invoice_id"]+1;
	}

	
		$resultitle = array();
		$transaction_title='';

		if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		
		$name_input=strip_tags($_POST["name".$i]);
		$quantity=strip_tags($_POST["quantity".$i]);
		$comment=strip_tags($_POST["comment".$i]);
	
		
		
		$Product_name_output = $pdo->query("SELECT *  FROM `product_information` where name='$name_input'");
        $rowdataserach_output = $Product_name_output->fetch();
		$name_id=$rowdataserach_output["id"];
		$unit=$rowdataserach_output["unit"];
	
		
$pdo->query("INSERT INTO `requestion_detail`(`invoice_id`, `date`, `employee_id`, `project_id`, `store_id`, `product_id`, `requestion_quantity`,`final_quantity`, `due_quantity`, `created_by`,`created_at`,comment) VALUES ('$invoice_id','$date','$created_by','$project_id','$store_id','$name_id','$quantity','$quantity','$quantity','$created_by','$created_at','$comment')");	
		
				
   
     }
	
	
	}
	
	
	
    $sql="INSERT INTO `requestion_histiory`(`requistion_type`,`invoice_id`, `date`, `employee_id`, `project_id`, `store_id`,note,`created_by`,`created_at`) VALUES (:requistion_type,:invoice_id,:date,:employee_id,:project_id,:store_id,:note,:created_by,:created_at)";

     $insert_data= $pdo->prepare($sql);
     $insert_data->bindparam(":requistion_type", $requistion_type);
	 $insert_data->bindparam(":invoice_id", $invoice_id);
	 $insert_data->bindparam(":date", $date);
	 $insert_data->bindparam(":employee_id", $created_by);
	 $insert_data->bindparam(":project_id", $project_id);
	 $insert_data->bindparam(":store_id", $store_id);
	 $insert_data->bindparam(":note", $note);	 
	 $insert_data->bindparam(":created_by", $created_by);
	 $insert_data->bindparam(":created_at", $created_at);
	 $insert_data->execute();
	
	
	$Project_Approval_Path_Info = $pdo->query("SELECT * FROM project_approval_path_inforamtion  WHERE project_id='$project_id' and deleted_at is NULL");
	$path_serial=1;
	while($rowDataProject_Approval_Path_Info= $Project_Approval_Path_Info->fetch()){
		
	$employee_id=$rowDataProject_Approval_Path_Info["employee_id"];
	$approval_path_name=$rowDataProject_Approval_Path_Info["approval_path_name"];
		
	if($path_serial==1){
	$pdo->query("INSERT INTO `project_material_aproval_status`(approval_path_name_id,invoice_id,`project_id`, `employee_id`, `assign_employee_id`,approval_status, `asign_date`, `asign_time`, `created_by`, `created_at`) VALUES ('$approval_path_name','$invoice_id','$project_id','$employee_id','$created_by','Pending','$current_date','$current_time','$created_by','$current_time')");	
	}else{
	$pdo->query("INSERT INTO `project_material_aproval_status`(approval_path_name_id,invoice_id,`project_id`, `employee_id`, `assign_employee_id`, `asign_date`, `asign_time`, `created_by`, `created_at`) VALUES ('$approval_path_name','$invoice_id','$project_id','$employee_id','$created_by','$current_date','$current_time','$created_by','$current_time')");		
	}	
	
	$path_serial++;
	}
	

	
	
$Staff_information = $pdo->query("SELECT employee_information.*  FROM project_material_aproval_status INNER JOIN employee_information ON project_material_aproval_status.employee_id=employee_information.id where  project_material_aproval_status.invoice_id='$invoice_id' and project_material_aproval_status.project_id='$project_id' and project_material_aproval_status.approval_id is NULL");
$rowdataStaff_information = $Staff_information->fetch();
	
$Requestion_information = $pdo->query("SELECT employee_information.name_en AS name_en,hr_designation.name AS designation  FROM employee_information INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where employee_information.id='$created_by' ");
$rowdataRequestion_information= $Requestion_information->fetch();	
	
	
$Project_information = $pdo->query("SELECT *  FROM project_information where id='$project_id'");
$rowdataProject_information = $Project_information->fetch();

// SMS Send Start
$number="88".$rowdataStaff_information["mobile"];
$message="একটি রিকুইজিশন আপনার অনুমোদনের জন্য অপেক্ষা করছে। Link :".$base_url."?Requestion_History_Detail/".$invoice_id;

$message_url=$sms_send_url."?api_key=".$apikey."&type=unicode&contacts=".urlencode($number)."&senderid=".$sender_id."&msg=".urlencode($message);
    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, $message_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
    $result = curl_exec ($curl);
    curl_close ($curl);
    
    
$SMS_number="8801712193135";
$SMS_message="Material requisition request for the project of ".$rowdataProject_information["name"]." Link :".$base_url."?Requestion_History_Detail/".$invoice_id;

$SMS_url=$sms_send_url."?api_key=".$apikey."&type=unicode&contacts=".urlencode($SMS_number)."&senderid=".$sender_id."&msg=".urlencode($SMS_message);
    $curl_Send = curl_init();
    curl_setopt ($curl_Send, CURLOPT_URL, $SMS_url);
    curl_setopt($curl_Send, CURLOPT_RETURNTRANSFER, true);
	
    $result_Send = curl_exec ($curl_Send);
    curl_close ($curl_Send); 
    
// SMS Send End
	
	
$to = $rowdataStaff_information["email"];
$subject = "Material Requisition Request for Project No ".$rowdataProject_information["name"];



$message = "
<html>
<head>
<title>".$organization_name.":: Material Requestion</title>
</head>
<body>
<table width='100%' cellpadding='10' cellspacing='5' bgcolor='#fff' >
  <tr>
     <td style='text-align:left; font-size:14px; font-weight:bold;' colspan='2'>Enquiry Form</td>
  </tr>
  <tr>
     <th>Name</th>
     <td>".$rowdataRequestion_information["name_en"]."</td>
  </tr>
  <tr>
	 <th>Designation</th>
	 <td>".$rowdataRequestion_information["designation"]."</td>
  </tr>
  <tr>
     <th>Subject</th>
	 <td>Material Requisition Request for Project No ".$rowdataProject_information["name"]."</td>
  </tr>
  <tr>
	<th>Message</th>
	<td>
	<a href='".$base_url."?Requestion_History_Detail/".$invoice_id."' style='color:green;display: block;
    width: 115px;
    height: 25px;
    background: #4E9CAF;
    padding: 10px;
    text-align: center;
    border-radius: 5px;
    color: white;
    font-weight: bold;
    line-height: 25px;'>Click here to View</a> <br>Copy and Past Below Link:<br><br>
	Link:  ".$base_url."?Requestion_History_Detail/".$invoice_id."<br><br>
	
Greetings, Please note that the following request No. ".$invoice_id." For Project No .".$rowdataProject_information["name"].".is waiting for your kind action. 
<br><br>
Thanks.
</td>
  </tr>
</table>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <'.$company_email_address.'>' . "\r\n";

mail($to,$subject,$message,$headers);
	
	
	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>"; 	

	
}



//Requestion Start
if(isset($_POST["Insert_Fund_Requestion_History"])){



    $store_id=$_POST["store_id"];
	$project_id=$_POST["project_id"];
	$date=$_POST["date"];
	$note=$_POST["note"];
	$requistion_type=$_POST["requistion_type"];
	$previous_cash_in_hand=$_POST["previous_cash_in_hand"];
    $date=date("Y-m-d", strtotime($_POST["date"]));


	
	$invoice_information = $pdo->query("select * from requestion_histiory order by id DESC limit 0,1");
    $invoicerowdata = $invoice_information->fetch();
	if(empty($invoicerowdata["invoice_id"])){ 
	$invoice_id="10001";
	}else{ 
	$invoice_id=$invoicerowdata["invoice_id"]+1;
	}

	
	$resultitle = array();
	$transaction_title='';	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		
		$name_input=strip_tags($_POST["name".$i]);
		$quantity=strip_tags($_POST["requestion_amount".$i]);
		$detail=strip_tags($_POST["detail".$i]);
		$comment=strip_tags($_POST["comment".$i]);
		$requestion_quantity=strip_tags($_POST["quantity".$i]);
		$requistion_rate=strip_tags($_POST["rate".$i]);
	
		
		
		$Product_name_output = $pdo->query("SELECT *  FROM `product_information` where name='$name_input'");
        $rowdataserach_output = $Product_name_output->fetch();
		$name_id=$rowdataserach_output["id"];
		$unit=$rowdataserach_output["unit"];
	
		
$pdo->query("INSERT INTO `requestion_detail`(`invoice_id`, `date`, `employee_id`, `project_id`, `store_id`, `product_id`, `requestion_amount`,`final_amount`, `due_amount`,detail,comment, `created_by`,`created_at`,requestion_quantity,requistion_rate) VALUES ('$invoice_id','$date','$created_by','$project_id','$store_id','$name_id','$quantity','$quantity','$quantity','$detail','$comment','$created_by','$created_at','$requestion_quantity','$requistion_rate')");	
		
				
   
     }
	
	
	}
	
	
	
    $sql="INSERT INTO `requestion_histiory`(`requistion_type`,`invoice_id`, `date`, `employee_id`, `project_id`, `store_id`,note,`created_by`,`created_at`,`previous_cash_in_hand`) VALUES (:requistion_type,:invoice_id,:date,:employee_id,:project_id,:store_id,:note,:created_by,:created_at,:previous_cash_in_hand)";

     $insert_data= $pdo->prepare($sql);
     $insert_data->bindparam(":requistion_type", $requistion_type);
	 $insert_data->bindparam(":invoice_id", $invoice_id);
	 $insert_data->bindparam(":date", $date);
	 $insert_data->bindparam(":employee_id", $created_by);
	 $insert_data->bindparam(":project_id", $project_id);
	 $insert_data->bindparam(":store_id", $store_id);
	 $insert_data->bindparam(":note", $note);	 
	 $insert_data->bindparam(":created_by", $created_by);
	 $insert_data->bindparam(":created_at", $created_at);
	 $insert_data->bindparam(":previous_cash_in_hand", $previous_cash_in_hand);
	 $insert_data->execute();
	
	
	$Project_Approval_Path_Info = $pdo->query("SELECT * FROM project_approval_path_inforamtion  WHERE project_id='$project_id' and deleted_at is NULL");
	$path_serial=1;
	while($rowDataProject_Approval_Path_Info= $Project_Approval_Path_Info->fetch()){
		
	$employee_id=$rowDataProject_Approval_Path_Info["employee_id"];
		$approval_path_name=$rowDataProject_Approval_Path_Info["approval_path_name"];
		
	if($path_serial==1){
	$pdo->query("INSERT INTO `project_material_aproval_status`(approval_path_name_id,invoice_id,`project_id`, `employee_id`, `assign_employee_id`,approval_status, `asign_date`, `asign_time`, `created_by`, `created_at`) VALUES ('$approval_path_name','$invoice_id','$project_id','$employee_id','$created_by','Pending','$current_date','$current_time','$created_by','$current_time')");	
	}else{
	$pdo->query("INSERT INTO `project_material_aproval_status`(approval_path_name_id,invoice_id,`project_id`, `employee_id`, `assign_employee_id`, `asign_date`, `asign_time`, `created_by`, `created_at`) VALUES ('$approval_path_name','$invoice_id','$project_id','$employee_id','$created_by','$current_date','$current_time','$created_by','$current_time')");		
	}	
	
	$path_serial++;
	}
	

	
	
$Staff_information = $pdo->query("SELECT employee_information.*  FROM project_material_aproval_status INNER JOIN employee_information ON project_material_aproval_status.employee_id=employee_information.id where  project_material_aproval_status.invoice_id='$invoice_id' and project_material_aproval_status.project_id='$project_id' and project_material_aproval_status.approval_id is NULL");
$rowdataStaff_information = $Staff_information->fetch();
	
$Requestion_information = $pdo->query("SELECT employee_information.name_en AS name_en,hr_designation.name AS designation  FROM employee_information INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where employee_information.id='$created_by' ");
$rowdataRequestion_information= $Requestion_information->fetch();	
	
	
$Project_information = $pdo->query("SELECT *  FROM project_information where id='$project_id'");
$rowdataProject_information = $Project_information->fetch();


// SMS Send Start
$number="88".$rowdataStaff_information["mobile"];
$message="একটি রিকুইজিশন আপনার অনুমোদনের জন্য অপেক্ষা করছে। Link :".$base_url."?Requestion_History_Detail/".$invoice_id;

$message_url=$sms_send_url."?api_key=".$apikey."&type=unicode&contacts=".urlencode($number)."&senderid=".$sender_id."&msg=".urlencode($message);
    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, $message_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
    $result = curl_exec ($curl);
    curl_close ($curl);
    
$SMS_number="8801712193135";
$SMS_message="Fund requisition request for the project of ".$rowdataProject_information["name"]." Link :".$base_url."?Requestion_History_Detail/".$invoice_id;

$SMS_url=$sms_send_url."?api_key=".$apikey."&type=unicode&contacts=".urlencode($SMS_number)."&senderid=".$sender_id."&msg=".urlencode($SMS_message);
    $curl_Send = curl_init();
    curl_setopt ($curl_Send, CURLOPT_URL, $SMS_url);
    curl_setopt($curl_Send, CURLOPT_RETURNTRANSFER, true);
	
    $result_Send = curl_exec ($curl_Send);
    curl_close ($curl_Send);     
// SMS Send End
	
	
$to = $rowdataStaff_information["email"];
$subject = "Fund Requisition Request for Project No ".$rowdataProject_information["name"];

$message = "
<html>
<head>
<title>".$organization_name.":: Fund Requestion</title>
</head>
<body>
<table width='100%' cellpadding='10' cellspacing='5' bgcolor='#fff' >
  <tr>
     <td style='text-align:left; font-size:14px; font-weight:bold;' colspan='2'>Enquiry Form</td>
  </tr>
  <tr>
     <th>Name</th>
     <td>".$rowdataRequestion_information["name_en"]."</td>
  </tr>
  <tr>
	 <th>Designation</th>
	 <td>".$rowdataRequestion_information["designation"]."</td>
  </tr>
  <tr>
     <th>Subject</th>
	 <td>Fund Requisition Request for Project No ".$rowdataProject_information["name"]."</td>
  </tr>
  <tr>
	<th>Message</th>
	<td>
	<a href='".$base_url."?Requestion_History_Detail/".$invoice_id."' style='color:green;display: block;
    width: 115px;
    height: 25px;
    background: #4E9CAF;
    padding: 10px;
    text-align: center;
    border-radius: 5px;
    color: white;
    font-weight: bold;
    line-height: 25px;'>Click here to View</a> <br>Copy and Past Below Link:<br><br>
	Link:  ".$base_url."?Requestion_History_Detail/".$invoice_id."<br><br>
	
Greetings, Please note that the following request No. ".$invoice_id." For Project No .".$rowdataProject_information["name"].".is waiting for your kind action. 
<br><br>
Thanks.
</td>
  </tr>
</table>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <'.$company_email_address.'>' . "\r\n";

mail($to,$subject,$message,$headers);
	
	
	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>"; 	

	
}






//Product Reqistion Approval Start
if(isset($_POST["Product_requistion_approval_Start"])){
	$approvalResult=requestionApprovalProcessPostedAction($pdo,'Product_requistion_approval_Start',false,$created_by,$created_at,$current_date,$current_time,array(
		'base_url'=>$base_url,
		'sms_send_url'=>$sms_send_url,
		'apikey'=>$apikey,
		'sender_id'=>$sender_id,
		'organization_name'=>$organization_name,
		'company_email_address'=>$company_email_address
	));
	if($approvalResult['ok']){
		$_SESSION['success_message']=!empty($success_message_data) ? $success_message_data : 'Requisition action completed successfully.';
	}else{
		$_SESSION['warning_message']=$approvalResult['message'];
	}
	$approvalInvoiceId=$approvalResult['invoice_id'];
	echo "<script>window.open('?Requestion_History_Detail/".htmlspecialchars($approvalInvoiceId,ENT_QUOTES,'UTF-8')."','_self')</script>";
	exit;



    $note=$_POST["note"];
	$invoice_id=$_POST["invoice_id"];
	$project_id=$_POST["project_id"];
	$store_id=$_POST["store_id"];
	$approval_action=!empty($_POST["Product_requistion_approval_Start"]) ? $_POST["Product_requistion_approval_Start"] : 'recommend';
	$forward_employee_id=!empty($_POST["forward_employee_id"]) ? (int)$_POST["forward_employee_id"] : 0;
	$return_employee_id=!empty($_POST["return_employee_id"]) ? (int)$_POST["return_employee_id"] : 0;
	if($approval_action=='recommend' && (int)$created_by!==1 && $forward_employee_id<=0){
		$_SESSION['warning_message']='Please select the employee you want to recommend/forward to.';
		echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>";
		exit;
	}
	if($approval_action=='return' && $return_employee_id<=0){
		$_SESSION['warning_message']='Please select an employee to return this requisition.';
		echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>";
		exit;
	}


	
	$resultitle = array();
	$transaction_title='';	
	$rateAttachmentFiles=array();
	$rateAttachmentData='';
	$rateAttachmentColumn=requestionApprovalColumnExists($pdo,'requestion_approval_detail','rate_attachment');
	
	if($approval_action=='recommend' && !empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
	$rateChanged=false;
	for($i=1;$i<=$number_count;$i++){
		if(empty($_POST["edit_id".$i])){
			continue;
		}
		$edit_id=strip_tags($_POST["edit_id".$i]);
		$final_rate=isset($_POST["final_rate".$i]) ? trim($_POST["final_rate".$i]) : '';
		$currentRateStatement=$pdo->prepare("SELECT COALESCE(NULLIF(final_rate,''),NULLIF(requistion_rate,''),'0') AS current_rate FROM requestion_detail WHERE id=:id AND invoice_id=:invoice_id LIMIT 1");
		$currentRateStatement->execute(array(':id'=>$edit_id,':invoice_id'=>$invoice_id));
		$currentRateRow=$currentRateStatement->fetch();
		if($currentRateRow && (float)$final_rate!=(float)$currentRateRow['current_rate']){
			$rateChanged=true;
			break;
		}
	}
	if($rateChanged){
		$rateAttachmentFiles=requestionApprovalUploadRateAttachment();
		if(empty($rateAttachmentFiles)){
			$_SESSION['warning_message']='আপনি রেট পরিবর্তন করেছেন। রেট বসাতে বা পরিবর্তন করতে হলে অবশ্যই প্রমাণ হিসেবে ফাইল সংযুক্ত করতে হবে। অনুগ্রহ করে রেটের প্রুফ ফাইল সংযুক্ত করে আবার সাবমিট করুন।';
			echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>";
			exit;
		}
		$rateAttachmentData=requestionApprovalEncodeRateAttachments($rateAttachmentFiles);
	}
				
	for($i=1;$i<=$number_count;$i++){
		
		$edit_id=strip_tags($_POST["edit_id".$i]);
		$name_input=strip_tags($_POST["name".$i]);
		$requestion_quantity=strip_tags($_POST["requestion_quantity".$i]);
		$final_quantity=strip_tags($_POST["final_quantity".$i]);
		$product_id=strip_tags($_POST["product_id".$i]);
		$final_rate=strip_tags($_POST["final_rate".$i]);


	if(!requestionApprovalDetailExists($pdo,$invoice_id,$project_id,$created_by,$product_id)){
	list($approval_detail_id_field, $approval_detail_id_value) = requestionApprovalDetailInsertPrefix($pdo);
	$rateAttachmentField=$rateAttachmentColumn ? ", `rate_attachment`" : "";
	$rateAttachmentValue=$rateAttachmentColumn ? ", ".$pdo->quote($rateAttachmentData) : "";
	$pdo->query("INSERT INTO `requestion_approval_detail`($approval_detail_id_field`invoice_id`, `date`, `employee_id`, `project_id`,`product_id`, `requestion_quantity`, `final_quantity`,`final_rate`, `created_by`,  `created_at`$rateAttachmentField) VALUES ($approval_detail_id_value'$invoice_id','$current_date','$created_by','$project_id','$product_id','$requestion_quantity','$final_quantity','$final_rate','$created_by','$created_at'$rateAttachmentValue)");
	}else if($rateAttachmentColumn && !empty($rateAttachmentData)){
		$updateRateProof=$pdo->prepare("UPDATE requestion_approval_detail SET final_quantity=:final_quantity,final_rate=:final_rate,rate_attachment=:rate_attachment,updated_by=:updated_by,updated_at=:updated_at WHERE invoice_id=:invoice_id AND project_id=:project_id AND employee_id=:employee_id AND product_id=:product_id AND deleted_at IS NULL");
		$updateRateProof->execute(array(':final_quantity'=>$final_quantity,':final_rate'=>$final_rate,':rate_attachment'=>$rateAttachmentData,':updated_by'=>$created_by,':updated_at'=>$created_at,':invoice_id'=>$invoice_id,':project_id'=>$project_id,':employee_id'=>$created_by,':product_id'=>$product_id));
	}

$pdo->query("UPDATE `requestion_detail` set final_quantity=GREATEST('$final_quantity',COALESCE(emergency_quantity,0)),due_quantity=GREATEST('$final_quantity'-COALESCE(emergency_quantity,0),0),final_rate='$final_rate' where id='$edit_id'");
	
				
   
     }
	
	
	}
	
	
	
	requestionApprovalHandleDynamicAction($pdo,$invoice_id,$project_id,$store_id,$note,$approval_action,$forward_employee_id,$return_employee_id,$created_by,$created_at,$current_date,$current_time,$base_url,$sms_send_url,$apikey,$sender_id,$organization_name,$company_email_address);



	

	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>"; 	


	
}




if(isset($_POST["Product_requistion_Fund_approval_Start"])){
	$approvalResult=requestionApprovalProcessPostedAction($pdo,'Product_requistion_Fund_approval_Start',true,$created_by,$created_at,$current_date,$current_time,array(
		'base_url'=>$base_url,
		'sms_send_url'=>$sms_send_url,
		'apikey'=>$apikey,
		'sender_id'=>$sender_id,
		'organization_name'=>$organization_name,
		'company_email_address'=>$company_email_address
	));
	if($approvalResult['ok']){
		$_SESSION['success_message']=!empty($success_message_data) ? $success_message_data : 'Requisition action completed successfully.';
	}else{
		$_SESSION['warning_message']=$approvalResult['message'];
	}
	$approvalInvoiceId=$approvalResult['invoice_id'];
	echo "<script>window.open('?Requestion_History_Detail/".htmlspecialchars($approvalInvoiceId,ENT_QUOTES,'UTF-8')."','_self')</script>";
	exit;



    $note=$_POST["note"];
	$invoice_id=$_POST["invoice_id"];
	$project_id=$_POST["project_id"];
	$store_id=$_POST["store_id"];
	$approval_action=!empty($_POST["Product_requistion_Fund_approval_Start"]) ? $_POST["Product_requistion_Fund_approval_Start"] : 'recommend';
	$forward_employee_id=!empty($_POST["forward_employee_id"]) ? (int)$_POST["forward_employee_id"] : 0;
	$return_employee_id=!empty($_POST["return_employee_id"]) ? (int)$_POST["return_employee_id"] : 0;
	if($approval_action=='recommend' && (int)$created_by!==1 && $forward_employee_id<=0){
		$_SESSION['warning_message']='Please select the employee you want to recommend/forward to.';
		echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>";
		exit;
	}
	if($approval_action=='return' && $return_employee_id<=0){
		$_SESSION['warning_message']='Please select an employee to return this requisition.';
		echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>";
		exit;
	}
	//$requistion_type=$_POST["requistion_type"];


	
	$resultitle = array();
	$transaction_title='';	
	$rateAttachmentFiles=array();
	$rateAttachmentData='';
	$rateAttachmentColumn=requestionApprovalColumnExists($pdo,'requestion_approval_detail','rate_attachment');
	
	if($approval_action=='recommend' && !empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
	$rateChanged=false;
	for($i=1;$i<=$number_count;$i++){
		if(empty($_POST["edit_id".$i])){
			continue;
		}
		$edit_id=strip_tags($_POST["edit_id".$i]);
		$final_rate=isset($_POST["rate".$i]) ? trim($_POST["rate".$i]) : '';
		$currentRateStatement=$pdo->prepare("SELECT COALESCE(NULLIF(final_rate,''),NULLIF(requistion_rate,''),'0') AS current_rate FROM requestion_detail WHERE id=:id AND invoice_id=:invoice_id LIMIT 1");
		$currentRateStatement->execute(array(':id'=>$edit_id,':invoice_id'=>$invoice_id));
		$currentRateRow=$currentRateStatement->fetch();
		if($currentRateRow && (float)$final_rate!=(float)$currentRateRow['current_rate']){
			$rateChanged=true;
			break;
		}
	}
	if($rateChanged){
		$rateAttachmentFiles=requestionApprovalUploadRateAttachment();
		if(empty($rateAttachmentFiles)){
			$_SESSION['warning_message']='আপনি রেট পরিবর্তন করেছেন। রেট বসাতে বা পরিবর্তন করতে হলে অবশ্যই প্রমাণ হিসেবে ফাইল সংযুক্ত করতে হবে। অনুগ্রহ করে রেটের প্রুফ ফাইল সংযুক্ত করে আবার সাবমিট করুন।';
			echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>";
			exit;
		}
		$rateAttachmentData=requestionApprovalEncodeRateAttachments($rateAttachmentFiles);
	}
				
	for($i=1;$i<=$number_count;$i++){
		
		$edit_id=strip_tags($_POST["edit_id".$i]);
		$name_input=strip_tags($_POST["name".$i]);
		$requestion_amount=strip_tags($_POST["requestion_amount".$i]);
		$final_amount=strip_tags($_POST["final_amount".$i]);
		$product_id=strip_tags($_POST["product_id".$i]);
		
		$final_quantity=strip_tags($_POST["quantity".$i]);
		$final_rate=strip_tags($_POST["rate".$i]);
		
		$requestion_quantity=strip_tags($_POST["requestion_quantity".$i]);
		$requistion_rate=strip_tags($_POST["requistion_rate".$i]);

	if(!requestionApprovalDetailExists($pdo,$invoice_id,$project_id,$created_by,$product_id)){
	list($approval_detail_id_field, $approval_detail_id_value) = requestionApprovalDetailInsertPrefix($pdo);
	$rateAttachmentField=$rateAttachmentColumn ? ", `rate_attachment`" : "";
	$rateAttachmentValue=$rateAttachmentColumn ? ", ".$pdo->quote($rateAttachmentData) : "";
	$pdo->query("INSERT INTO `requestion_approval_detail`($approval_detail_id_field`invoice_id`, `date`, `employee_id`, `project_id`,`product_id`, `requestion_amount`, `final_amount`, `created_by`,  `created_at`,requestion_quantity,requistion_rate,final_quantity,final_rate$rateAttachmentField) VALUES ($approval_detail_id_value'$invoice_id','$current_date','$created_by','$project_id','$product_id','$requestion_amount','$final_amount','$created_by','$created_at','$requestion_quantity','$requistion_rate','$final_quantity','$final_rate'$rateAttachmentValue)");
	}else if($rateAttachmentColumn && !empty($rateAttachmentData)){
		$updateRateProof=$pdo->prepare("UPDATE requestion_approval_detail SET final_amount=:final_amount,final_quantity=:final_quantity,final_rate=:final_rate,rate_attachment=:rate_attachment,updated_by=:updated_by,updated_at=:updated_at WHERE invoice_id=:invoice_id AND project_id=:project_id AND employee_id=:employee_id AND product_id=:product_id AND deleted_at IS NULL");
		$updateRateProof->execute(array(':final_amount'=>$final_amount,':final_quantity'=>$final_quantity,':final_rate'=>$final_rate,':rate_attachment'=>$rateAttachmentData,':updated_by'=>$created_by,':updated_at'=>$created_at,':invoice_id'=>$invoice_id,':project_id'=>$project_id,':employee_id'=>$created_by,':product_id'=>$product_id));
	}

$pdo->query("UPDATE `requestion_detail` set final_amount='$final_amount',due_amount='$final_amount',final_quantity='$final_quantity',final_rate='$final_rate' where id='$edit_id'");
	
				
   
     }
	
	
	}
	
	
	
	requestionApprovalHandleDynamicAction($pdo,$invoice_id,$project_id,$store_id,$note,$approval_action,$forward_employee_id,$return_employee_id,$created_by,$created_at,$current_date,$current_time,$base_url,$sms_send_url,$apikey,$sender_id,$organization_name,$company_email_address);



	

	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>"; 	


	
}







//Product Requestion Approval End



















if(isset($_POST["Approval_Project_Coordinator"])){



    $project_coordinator_note=$_POST["project_coordinator_note"];
	$invoice_id=$_POST["invoice_id"];
	$project_id=$_POST["project_id"];


	
	$resultitle = array();
	$transaction_title='';	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		
		$edit_id=strip_tags($_POST["edit_id".$i]);
		$name_input=strip_tags($_POST["name".$i]);
		$requestion_quantity=strip_tags($_POST["requestion_quantity".$i]);
		$final_quantity=strip_tags($_POST["final_quantity".$i]);
		
	
	
		
$pdo->query("UPDATE `requestion_detail` set final_quantity='$final_quantity',project_coordinator_quantity='$final_quantity' where id='$edit_id'");	
		
				
   
     }
	
	
	}
	
	
	
    $sql="UPDATE `requestion_histiory` set project_coordinator='$created_by',project_coordinator_note=:project_coordinator_note,project_coordinator_time='$created_at' where invoice_id='$invoice_id'";

     $insert_data= $pdo->prepare($sql);
	 $insert_data->bindparam(":project_coordinator_note", $project_coordinator_note);
	 $insert_data->execute();
	
$Approval_information = $pdo->query("SELECT *  FROM hr_designation where name='Project Director' and deleted_at is NULL");
$rowdataApproval_information = $Approval_information->fetch();	
	
	
$Staff_information = $pdo->query("SELECT *  FROM employee_information where project_name='$project_id' and designation='".$rowdataApproval_information["id"]."'");
$rowdataStaff_information = $Staff_information->fetch();
	
$Requestion_information = $pdo->query("SELECT employee_information.name_en AS name_en,hr_designation.name AS designation  FROM employee_information INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where employee_information.id='$created_by' ");
$rowdataRequestion_information= $Requestion_information->fetch();	
	
	
$Project_information = $pdo->query("SELECT *  FROM project_information where id='$project_id'");
$rowdataProject_information = $Project_information->fetch();
	
	
$to = $rowdataStaff_information["email"];
$subject = "Material Requisition Request for Project No ".$rowdataProject_information["name"];

$message = "
<html>
<head>
<title>".$organization_name.":: Material Requestion</title>
</head>
<body>
<table width='100%' cellpadding='10' cellspacing='5' bgcolor='#fff' >
  <tr>
     <td style='text-align:left; font-size:14px; font-weight:bold;' colspan='2'>Enquiry Form</td>
  </tr>
  <tr>
     <th>Name</th>
     <td>".$rowdataRequestion_information["name_en"]."</td>
  </tr>
  <tr>
	 <th>Designation</th>
	 <td>".$rowdataRequestion_information["designation"]."</td>
  </tr>
  <tr>
     <th>Subject</th>
	 <td>Material Requisition Request for Project No ".$rowdataProject_information["name"]."</td>
  </tr>
  <tr>
	<th>Message</th>
	<td>
	<a href='".$base_url."?Requestion_History_Detail/".$invoice_id."' style='color:green;display: block;
    width: 115px;
    height: 25px;
    background: #4E9CAF;
    padding: 10px;
    text-align: center;
    border-radius: 5px;
    color: white;
    font-weight: bold;
    line-height: 25px;'>Click here to View</a> <br>Copy and Past Below Link:<br><br>
	Link:  ".$base_url."?Requestion_History_Detail/".$invoice_id."<br><br>
	
Greetings, Please note that the following request No. ".$invoice_id." For Project No .".$rowdataProject_information["name"].".is waiting for your kind action. 
<br><br>
Thanks.
</td>
  </tr>
</table>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <'.$company_email_address.'>' . "\r\n";

mail($to,$subject,$message,$headers);
	
	
	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>"; 	

	
}



if(isset($_POST["Approval_Project_Director"])){



    $project_director_note=$_POST["project_director_note"];
	$invoice_id=$_POST["invoice_id"];
	$project_id=$_POST["project_id"];


	
	$resultitle = array();
	$transaction_title='';	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		
		$edit_id=strip_tags($_POST["edit_id".$i]);
		$name_input=strip_tags($_POST["name".$i]);
		$final_quantity=strip_tags($_POST["final_quantity".$i]);
		
	
	
		
$pdo->query("UPDATE `requestion_detail` set final_quantity='$final_quantity',project_director_quantity='$final_quantity' where id='$edit_id'");	
		
				
   
     }
	
	
	}
	
	
	
    $sql="UPDATE `requestion_histiory` set project_director='$created_by',project_director_note=:project_director_note,project_director_time='$created_at' where invoice_id='$invoice_id'";

     $insert_data= $pdo->prepare($sql);
	 $insert_data->bindparam(":project_director_note", $project_director_note);
	 $insert_data->execute();
	
$Approval_information = $pdo->query("SELECT *  FROM hr_designation where name='Managing Director' and deleted_at is NULL");
$rowdataApproval_information = $Approval_information->fetch();	
	
	
$Staff_information = $pdo->query("SELECT *  FROM employee_information where  designation='".$rowdataApproval_information["id"]."'");
$rowdataStaff_information = $Staff_information->fetch();
	
$Requestion_information = $pdo->query("SELECT employee_information.name_en AS name_en,hr_designation.name AS designation  FROM employee_information INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where employee_information.id='$created_by' ");
$rowdataRequestion_information= $Requestion_information->fetch();	
	
	
$Project_information = $pdo->query("SELECT *  FROM project_information where id='$project_id'");
$rowdataProject_information = $Project_information->fetch();
	
	
$to = $rowdataStaff_information["email"];
$subject = "Material Requisition Request for Project No ".$rowdataProject_information["name"];

$message = "
<html>
<head>
<title>".$organization_name.":: Material Requestion</title>
</head>
<body>
<table width='100%' cellpadding='10' cellspacing='5' bgcolor='#fff' >
  <tr>
     <td style='text-align:left; font-size:14px; font-weight:bold;' colspan='2'>Enquiry Form</td>
  </tr>
  <tr>
     <th>Name</th>
     <td>".$rowdataRequestion_information["name_en"]."</td>
  </tr>
  <tr>
	 <th>Designation</th>
	 <td>".$rowdataRequestion_information["designation"]."</td>
  </tr>
  <tr>
     <th>Subject</th>
	 <td>Material Requisition Request for Project No ".$rowdataProject_information["name"]."</td>
  </tr>
  <tr>
	<th>Message</th>
	<td>
	<a href='".$base_url."?Requestion_History_Detail/".$invoice_id."' style='color:green;display: block;
    width: 115px;
    height: 25px;
    background: #4E9CAF;
    padding: 10px;
    text-align: center;
    border-radius: 5px;
    color: white;
    font-weight: bold;
    line-height: 25px;'>Click here to View</a> <br>Copy and Past Below Link:<br><br>
	Link:  ".$base_url."?Requestion_History_Detail/".$invoice_id."<br><br>
	
Greetings, Please note that the following request No. ".$invoice_id." For Project No .".$rowdataProject_information["name"].".is waiting for your kind action. 
<br><br>
Thanks.
</td>
  </tr>
</table>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <'.$company_email_address.'>' . "\r\n";

mail($to,$subject,$message,$headers);
	
	
	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>"; 	

	
}



if(isset($_POST["Approval_Managing_Director"])){



    $managing_director_note=$_POST["managing_director_note"];
	$invoice_id=$_POST["invoice_id"];
	$project_id=$_POST["project_id"];
	$store_id=$_POST["store_id"];


	
	$resultitle = array();
	$transaction_title='';	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		
		$edit_id=strip_tags($_POST["edit_id".$i]);
		$name_input=strip_tags($_POST["name".$i]);
		$final_quantity=strip_tags($_POST["final_quantity".$i]);
		
	
	
		
$pdo->query("UPDATE `requestion_detail` set final_quantity=GREATEST('$final_quantity',COALESCE(emergency_quantity,0)),managing_director_quantity=GREATEST('$final_quantity',COALESCE(emergency_quantity,0)),due_quantity=GREATEST('$final_quantity'-COALESCE(emergency_quantity,0),0) where id='$edit_id'");
		
				
   
     }
	
	
	}
	
	
	
    $sql="UPDATE `requestion_histiory` set managing_director='$created_by',managing_director_note=:managing_director_note,managing_director_time='$created_at' where invoice_id='$invoice_id'";

     $insert_data= $pdo->prepare($sql);
	 $insert_data->bindparam(":managing_director_note", $managing_director_note);
	 $insert_data->execute();
	
	$Approval_information = $pdo->query("SELECT *  FROM hr_designation where name='Store Keeper' and deleted_at is NULL");
	$rowdataApproval_information = $Approval_information->fetch();
	$rowdataApproval_information = $rowdataApproval_information ?: array('id' => '');


	$Staff_information = $pdo->query("SELECT *  FROM employee_information where  designation='".$rowdataApproval_information["id"]."' and store_id='$store_id'");
	$rowdataStaff_information = $Staff_information->fetch();
	$rowdataStaff_information = $rowdataStaff_information ?: array('email' => '');
	
$Requestion_information = $pdo->query("SELECT employee_information.name_en AS name_en,hr_designation.name AS designation  FROM employee_information INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where employee_information.id='$created_by' ");
$rowdataRequestion_information= $Requestion_information->fetch();	
	
	
	$Project_information = $pdo->query("SELECT *  FROM project_information where id='$project_id'");
	$rowdataProject_information = $Project_information->fetch();
	$rowdataProject_information = $rowdataProject_information ?: array('name' => '');
	
	
$to = $rowdataStaff_information["email"];
$subject = "Material Requisition Request for Project No ".$rowdataProject_information["name"];

$message = "
<html>
<head>
<title>".$organization_name.":: Material Requestion</title>
</head>
<body>
<table width='100%' cellpadding='10' cellspacing='5' bgcolor='#fff' >
  <tr>
     <td style='text-align:left; font-size:14px; font-weight:bold;' colspan='2'>Enquiry Form</td>
  </tr>
  <tr>
     <th>Name</th>
     <td>".$rowdataRequestion_information["name_en"]."</td>
  </tr>
  <tr>
	 <th>Designation</th>
	 <td>".$rowdataRequestion_information["designation"]."</td>
  </tr>
  <tr>
     <th>Subject</th>
	 <td>Material Requisition Request for Project No ".$rowdataProject_information["name"]."</td>
  </tr>
  <tr>
	<th>Message</th>
	<td>
	
	
Greetings, Please note that the following request No. ".$invoice_id." For Project No .".$rowdataProject_information["name"].".is waiting for your kind action. 
<br><br>
Thanks.
</td>
  </tr>
</table>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <'.$company_email_address.'>' . "\r\n";

	if(!empty($to)){ mail($to,$subject,$message,$headers); }
	
	
	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>"; 	

	
}


//Requestion End

//  Distribution Start
if(isset($_POST["Distribution_Start"])){
	$invoice_id=$_POST["invoice_id"];
	$project_id=$_POST["project_id"];
	$store_id=$_POST["store_id"];
    $date=$current_date;

	$invoice_information = $pdo->query("select * from distribution_summary order by id DESC limit 0,1");
    $invoicerowdata = $invoice_information->fetch();
	if(empty($invoicerowdata["distribution_id"])){ 
	$distribution_id="10001";
	}else{ 
	$distribution_id=$invoicerowdata["distribution_id"]+1;
	}
	
	$distributed_line_count=0;
	$assign_receiver_id='';
	$historyManualId=distributionTableUsesManualId($pdo,'distribution_history');
	$historyNextId=$historyManualId ? distributionNextManualId($pdo,'distribution_history') : 0;
	$historyIdColumn=$historyManualId ? 'id,' : '';
	$historyIdValue=$historyManualId ? ':id,' : '';
	$loadDetailStatement=$pdo->prepare("SELECT * FROM requestion_detail WHERE id=:id AND invoice_id=:invoice_id AND deleted_at IS NULL LIMIT 1");
	$updateDetailStatement=$pdo->prepare("UPDATE requestion_detail SET distribution_quantity=:distribution_quantity,due_quantity=:due_quantity WHERE id=:id AND invoice_id=:invoice_id AND deleted_at IS NULL");
	$insertHistoryStatement=$pdo->prepare("INSERT INTO distribution_history(".$historyIdColumn."invoice_id,date,project_id,store_id,product_id,requestion_quantity,distribution_quantity,due_quantity,created_by,created_at,distribution_id,assign_receiver_id) VALUES (".$historyIdValue.":invoice_id,:date,:project_id,:store_id,:product_id,:requestion_quantity,:distribution_quantity,:due_quantity,:created_by,:created_at,:distribution_id,:assign_receiver_id)");
	
	if(!empty($_POST["number_count"])){
	$number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		if(!empty($_POST["distribution_quantity".$i])){
		$requested_distribution_quantity=max(0,(float)strip_tags($_POST["distribution_quantity".$i]));
		$edit_id=strip_tags($_POST["edit_id".$i]);
		if($requested_distribution_quantity<=0){
			continue;
		}
		$loadDetailStatement->execute(array(':id'=>$edit_id,':invoice_id'=>$invoice_id));
        $rowdataserach = $loadDetailStatement->fetch();
		if(empty($rowdataserach)){
			continue;
		}
		$final_quantity=$rowdataserach["final_quantity"];
		$due_quantity=max(0,(float)$rowdataserach["due_quantity"]);
			
			
		$distribution_quantity_requestion=(float)$rowdataserach["distribution_quantity"];
		$product_id=$rowdataserach["product_id"];
		$assign_receiver_id=$rowdataserach["created_by"];
		$available_stock=distributionAvailableStock($pdo,$product_id,$store_id);
		$distribution_quantity=min($requested_distribution_quantity,$due_quantity,$available_stock);
		if($distribution_quantity<=0){
			continue;
		}
		$consumed_stock=distributionConsumeStock($pdo,$product_id,$store_id,$distribution_quantity);
		if($consumed_stock<=0){
			continue;
		}
		$distribution_quantity=$consumed_stock;
		$new_due_quantity=max(0,$due_quantity-$distribution_quantity);
		$total_distribution_quantity=$distribution_quantity+$distribution_quantity_requestion;
			
		$updateDetailStatement->execute(array(':distribution_quantity'=>$total_distribution_quantity,':due_quantity'=>$new_due_quantity,':id'=>$edit_id,':invoice_id'=>$invoice_id));
		$historyParams=array(':invoice_id'=>$invoice_id,':date'=>$date,':project_id'=>$project_id,':store_id'=>$store_id,':product_id'=>$product_id,':requestion_quantity'=>$final_quantity,':distribution_quantity'=>$distribution_quantity,':due_quantity'=>$new_due_quantity,':created_by'=>$created_by,':created_at'=>$created_at,':distribution_id'=>$distribution_id,':assign_receiver_id'=>$assign_receiver_id);
		if($historyManualId){
			$historyParams[':id']=$historyNextId++;
		}
		$insertHistoryStatement->execute($historyParams);
		$distributed_line_count++;
   
     }
		
	
	}
	
	
	}
	
	if($distributed_line_count<=0){
		$_SESSION['warning_message']="No distributable quantity was saved. Please check due quantity and available stock.";
		echo "<script>window.open('?Distribution_History_Create/$invoice_id','_self')</script>";
		exit;
	}
	distributionSyncRequisitionStatus($pdo,$invoice_id,'Material',$created_by,$created_at);
	
	$pdo->query("INSERT INTO `distribution_summary`(`invoice_id`, `date`, `project_id`, `store_id`, `created_by`, `created_at`,distribution_id,employee_id,assign_receiver_id) VALUES ('$invoice_id','$date','$project_id','$store_id','$created_by','$created_at','$distribution_id','$created_by','$assign_receiver_id')");
	
	$_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?Distribution_History_Indivisual/$invoice_id/$distribution_id','_self')</script>"; 	

	

}

if(isset($_POST["Distribution_Fund_Start"])){
    $invoice_id=$_POST["invoice_id"];
	$project_id=$_POST["project_id"];
	$store_id=$_POST["store_id"];
    $date=$current_date;

$invoice_information = $pdo->query("select * from distribution_summary order by id DESC limit 0,1");
$invoicerowdata = $invoice_information->fetch();
if(empty($invoicerowdata["distribution_id"])){ 
	$distribution_id="10001";
	}else{ 
	$distribution_id=$invoicerowdata["distribution_id"]+1;
	}
	
	$distributed_line_count=0;
	$assign_receiver_id='';
	$historyManualId=distributionTableUsesManualId($pdo,'distribution_history');
	$historyNextId=$historyManualId ? distributionNextManualId($pdo,'distribution_history') : 0;
	$historyIdColumn=$historyManualId ? 'id,' : '';
	$historyIdValue=$historyManualId ? ':id,' : '';
	$loadDetailStatement=$pdo->prepare("SELECT * FROM requestion_detail WHERE id=:id AND invoice_id=:invoice_id AND deleted_at IS NULL LIMIT 1");
	$updateDetailStatement=$pdo->prepare("UPDATE requestion_detail SET distribution_amount=:distribution_amount,due_amount=:due_amount WHERE id=:id AND invoice_id=:invoice_id AND deleted_at IS NULL");
	$insertHistoryStatement=$pdo->prepare("INSERT INTO distribution_history(".$historyIdColumn."invoice_id,date,project_id,store_id,product_id,requestion_amount,distribution_amount,due_amount,created_by,created_at,distribution_id,assign_receiver_id,detail,comment) VALUES (".$historyIdValue.":invoice_id,:date,:project_id,:store_id,:product_id,:requestion_amount,:distribution_amount,:due_amount,:created_by,:created_at,:distribution_id,:assign_receiver_id,:detail,:comment)");
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		if(!empty($_POST["distribution_amount".$i])){
		$requested_distribution_amount=max(0,(float)strip_tags($_POST["distribution_amount".$i]));
		$edit_id=strip_tags($_POST["edit_id".$i]);
		if($requested_distribution_amount<=0){
			continue;
		}
		$loadDetailStatement->execute(array(':id'=>$edit_id,':invoice_id'=>$invoice_id));
        $rowdataserach = $loadDetailStatement->fetch();
		if(empty($rowdataserach)){
			continue;
		}
		$final_amount=$rowdataserach["final_amount"];
		$due_amount=max(0,(float)$rowdataserach["due_amount"]);
		$detail=$rowdataserach["detail"];
		$comment=$rowdataserach["comment"];
			
			
		$distribution_amount_requestion=(float)$rowdataserach["distribution_amount"];
		$product_id=$rowdataserach["product_id"];
		$assign_receiver_id=$rowdataserach["created_by"];
			
		$distribution_amount=min($requested_distribution_amount,$due_amount);
		if($distribution_amount<=0){
			continue;
		}
		$new_due_amount=max(0,$due_amount-$distribution_amount);
		$total_distribution_amount=$distribution_amount+$distribution_amount_requestion;
			
		$updateDetailStatement->execute(array(':distribution_amount'=>$total_distribution_amount,':due_amount'=>$new_due_amount,':id'=>$edit_id,':invoice_id'=>$invoice_id));
		$historyParams=array(':invoice_id'=>$invoice_id,':date'=>$date,':project_id'=>$project_id,':store_id'=>$store_id,':product_id'=>$product_id,':requestion_amount'=>$final_amount,':distribution_amount'=>$distribution_amount,':due_amount'=>$new_due_amount,':created_by'=>$created_by,':created_at'=>$created_at,':distribution_id'=>$distribution_id,':assign_receiver_id'=>$assign_receiver_id,':detail'=>$detail,':comment'=>$comment);
		if($historyManualId){
			$historyParams[':id']=$historyNextId++;
		}
		$insertHistoryStatement->execute($historyParams);
		$distributed_line_count++;
   
     }
	}
	}
	
	if($distributed_line_count<=0){
		$_SESSION['warning_message']="No distributable amount was saved. Please check due amount.";
		echo "<script>window.open('?Distribution_History_Create/$invoice_id','_self')</script>";
		exit;
	}
	distributionSyncRequisitionStatus($pdo,$invoice_id,'Fund',$created_by,$created_at);
	
	$pdo->query("INSERT INTO `distribution_summary`(`invoice_id`, `date`, `project_id`, `store_id`, `created_by`, `created_at`,distribution_id,employee_id,assign_receiver_id) VALUES ('$invoice_id','$date','$project_id','$store_id','$created_by','$created_at','$distribution_id','$created_by','$assign_receiver_id')");
	
	$_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?Distribution_History_Indivisual/$invoice_id/$distribution_id','_self')</script>"; 	

	
}


//  Distribution End

//Stock Transfer Start
if(isset($_POST["Insert_Stock_Transfer"])){
$FROM_STORE_ID=$_POST["FROM_STORE_ID"];
$TO_STORE_ID=$_POST["TO_STORE_ID"];



$TRANSFER_DATE=$_POST["TRANSFER_DATE"];
$NOTE=$_POST["note"];

$invoice_information = $pdo->query("select * from stock_transfer_summary order by id DESC limit 0,1");
    $invoicerowdata = $invoice_information->fetch();
	if(empty($invoicerowdata["transfer_id"])){ 
	$transfer_id="10001";
	}else{ 
	$transfer_id=$invoicerowdata["transfer_id"]+1;
	}
	


if(!empty($_FILES['PHOTO']['name'])){
	  $tempPHOTO = explode(".", $_FILES['PHOTO']['name']);
      $DoctorPHOTO = $tempPHOTO["0"].round(microtime(true)) . '.' . end($tempPHOTO);
      $file_tmpPHOTO= $_FILES['PHOTO']['tmp_name'];
      move_uploaded_file($file_tmpPHOTO, "StockTransfer/" . $DoctorPHOTO);
	  $image_field_namePHOTO=',PHOTO';
	  $image_valuePHOTO=",'".$DoctorPHOTO."'";
	  }else{
		$image_field_namePHOTO='';
	    $image_valuePHOTO="";  
		  }

if(!empty($_POST["number_count"])){

$number_count=$_POST["number_count"];
 for($i=1;$i<=$number_count;$i++){
$product_id=$_POST['product_id'.$i];
$transfer_quantity=$_POST['transfer_quantity'.$i];


$From_Stock= $pdo->query("SELECT * FROM stock_information WHERE stock_information.product_id='$product_id' and store_id='$FROM_STORE_ID'");
$rowDataFrom_Stock= $From_Stock->fetch();

$product_id=$rowDataFrom_Stock["product_id"];
$Form_Current_Stock=$rowDataFrom_Stock["stock"];

$Form_New_Stock=$Form_Current_Stock-$transfer_quantity;
$pdo->query("UPDATE stock_information SET stock='$Form_New_Stock' WHERE stock_information.product_id='$product_id' and store_id='$FROM_STORE_ID'");

$TO_Stock= $pdo->query("SELECT * FROM stock_information WHERE stock_information.product_id='$product_id' and store_id='$TO_STORE_ID'");
$rowDataTO_Stock= $TO_Stock->fetch();

if(!empty($rowDataTO_Stock)){
$TO_Previous_New_Stock=$rowDataFrom_Medicine_Stock["new"]+$transfer_quantity;
$TO_Total_Stock=$rowDataFrom_Medicine_Stock["total"]+$transfer_quantity;
$TO_Current_Stock=$rowDataFrom_Medicine_Stock["stock"];
	

$TO_New_Stock=$TO_Current_Stock+$transfer_quantity;
$pdo->query("UPDATE stock_information SET stock='$TO_New_Stock',new='$TO_Previous_New_Stock',total='$TO_Total_Stock' WHERE stock_information.product_id='$product_id' and store_id='$TO_STORE_ID'");

}else{
$TO_Current_Stock=0;
$TO_New_Stock=$transfer_quantity;
$pdo->query("INSERT INTO `stock_information`(`store_id`, `product_id`,`new`, `total`, `stock`, `created_by`, `created_at`) VALUES ('$TO_STORE_ID','$product_id','$TO_New_Stock','$TO_New_Stock','$TO_New_Stock','$created_by','$created_at')");
	

}


$pdo->query("INSERT INTO stock_transfer_information (from_store_id,to_store_id,product_id,from_stock,to_stock,from_new_stock,to_new_stock,quantity,transfer_date,
    created_by,created_at,transfer_id) VALUES ('$FROM_STORE_ID','$TO_STORE_ID','$product_id','$Form_Current_Stock','$TO_Current_Stock','$Form_New_Stock','$TO_New_Stock','$transfer_quantity','$current_date','$created_by','$created_at','$transfer_id')")	;


  }
}


$pdo->query("INSERT INTO stock_transfer_summary (from_store_id,to_store_id,note,transfer_date,created_by,created_at,transfer_id$image_field_namePHOTO) VALUES ('$FROM_STORE_ID','$TO_STORE_ID','$NOTE','$TRANSFER_DATE','$LoginReGiSterSession','$current_time','$transfer_id'$image_valuePHOTO)")	;




$_SESSION['success_message']=$success_message_data;
 echo "<script>window.open('?Stock_Transfer_detail_vew/Stock Transfer/$transfer_id','_self')</script>"; 


}


// Stock Transfer End


// Project Approval Information Start
/*if(isset($_POST["Project_Material_Approval_Information_Create"])){
$project_id=$_POST["project_id"];
$number_count=$_POST["number_count"];
	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		if(!empty($_POST["employee_id".$i])){
		$employee_id=strip_tags($_POST["employee_id".$i]);
		
		$pdo->query("INSERT INTO `project_approval_path_inforamtion`(`project_id`, `employee_id`, `created_by`,`created_at`) VALUES ('$project_id','$employee_id','$LoginReGiSterSession','$current_time')");	
			
     }
		
	
	}
	
	
	}	
	
$_SESSION['success_message']=$success_message_data;
  echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 	
	
	
	
}

if(isset($_POST["Project_Material_Approval_Information_Edit"])){
$project_id=$_POST["project_id"];
$number_count=$_POST["number_count"];
	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		if(!empty($_POST["employee_id".$i])){
		$employee_id=strip_tags($_POST["employee_id".$i]);
		if(!empty($_POST["project_approval_path_id".$i])){
			
		$project_approval_path_id=$_POST["project_approval_path_id".$i];	
		$pdo->query("UPDATE `project_approval_path_inforamtion` set project_id='$project_id',employee_id='$employee_id',updated_by='$LoginReGiSterSession',updated_at='$current_time' where id='$project_approval_path_id'");	
				
		}else{
		$pdo->query("INSERT INTO `project_approval_path_inforamtion`(`project_id`, `employee_id`, `created_by`,`created_at`) VALUES ('$project_id','$employee_id','$LoginReGiSterSession','$current_time')");	
				
		}	
		
		
     }
		
	
	}
	
	
	}	
	
$_SESSION['success_message']=$success_message_data;
  echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 	
	
	
	
}*/


if(isset($_POST["Project_Material_Approval_Information_Create"])){
$project_id=$_POST["project_id"];
$number_count=$_POST["number_count"];
	$approval_path_name=$_POST["approval_path_name"];
$approval_path_employee_ids=array();
for($i=1;$i<=$number_count;$i++){
	if(!empty($_POST["employee_id".$i])){
		$approval_path_employee_ids[]=strip_tags($_POST["employee_id".$i]);
	}
}
if(empty($approval_path_employee_ids) || (int)end($approval_path_employee_ids)!==1){
	$_SESSION['warning_message']='Approval path must have '.requestionApprovalFinalApproverName($pdo).' as the last approver.';
	echo "<script>window.open('?Project_Material_Approval_Information_Create/$MenuName','_self')</script>";
	exit;
}
$pdo->query("INSERT INTO `project_approval_path_name`(approval_path_name,`project_id`, `created_by`,`created_at`) VALUES ('$approval_path_name','$project_id','$LoginReGiSterSession','$current_time')");

$approval_path_name_data = $pdo->query("SELECT *  FROM `project_approval_path_name` where approval_path_name='$approval_path_name' and project_id='$project_id' and  deleted_at is NULL");
$rowdaapproval_path_name_data = $approval_path_name_data->fetch();	
$approval_path_name_id=$rowdaapproval_path_name_data["id"];
	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		if(!empty($_POST["employee_id".$i])){
		$employee_id=strip_tags($_POST["employee_id".$i]);
		
		$pdo->query("INSERT INTO `project_approval_path_inforamtion`(approval_path_name,`project_id`, `employee_id`, `created_by`,`created_at`) VALUES ('$approval_path_name_id','$project_id','$employee_id','$LoginReGiSterSession','$current_time')");	
			
     }
		
	
	}
	
	
	}	
		
	
	
$_SESSION['success_message']=$success_message_data;
  echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 	
	
	
	
}

if(isset($_POST["Project_Material_Approval_Information_Edit"])){
$project_id=$_POST["project_id"];
$number_count=$_POST["number_count"];
$approval_path_name=$_POST["approval_path_name"];
$approval_path_name_previous=$_POST["approval_path_name_previous"];
$project_id_previous=$_POST["project_id_previous"];
$approval_path_employee_ids=array();
for($i=1;$i<=$number_count;$i++){
	if(!empty($_POST["employee_id".$i])){
		$approval_path_employee_ids[]=strip_tags($_POST["employee_id".$i]);
	}
}
if(empty($approval_path_employee_ids) || (int)end($approval_path_employee_ids)!==1){
	$_SESSION['warning_message']='Approval path must have '.requestionApprovalFinalApproverName($pdo).' as the last approver.';
	echo "<script>window.open('?Project_Material_Approval_Information_Edit/$MenuName/$DocumentData/project_approval_path_name','_self')</script>";
	exit;
}

if($approval_path_name!=$approval_path_name_previous){
$pdo->query("UPDATE `project_approval_path_name` set deleted_by='$LoginReGiSterSession',deleted_at='$current_time' where approval_path_name='$approval_path_name_previous' and project_id='$project_id_previous'");	  

$pdo->query("INSERT INTO `project_approval_path_name`(approval_path_name,`project_id`, `created_by`,`created_at`) VALUES ('$approval_path_name','$project_id','$LoginReGiSterSession','$current_time')");

$approval_path_name_data_insert = $pdo->query("SELECT *  FROM `project_approval_path_name` where approval_path_name='$approval_path_name' and project_id='$project_id' and deleted_at is NULL");
$rowdaapproval_path_name_data_insert = $approval_path_name_data_insert->fetch();
$approval_path_name_id=$rowdaapproval_path_name_data_insert["id"];
      
}else if($project_id!=$project_id_previous){
$pdo->query("UPDATE `project_approval_path_name` set deleted_by='$LoginReGiSterSession',deleted_at='$current_time' where approval_path_name='$approval_path_name_previous' and project_id='$project_id_previous'");	  

$pdo->query("INSERT INTO `project_approval_path_name`(approval_path_name,`project_id`, `created_by`,`created_at`) VALUES ('$approval_path_name','$project_id','$LoginReGiSterSession','$current_time')");

$approval_path_name_data_insert = $pdo->query("SELECT *  FROM `project_approval_path_name` where approval_path_name='$approval_path_name' and project_id='$project_id' and deleted_at is NULL");
$rowdaapproval_path_name_data_insert = $approval_path_name_data_insert->fetch();
$approval_path_name_id=$rowdaapproval_path_name_data_insert["id"];
      
}else{

$approval_path_name_data = $pdo->query("SELECT *  FROM `project_approval_path_name` where approval_path_name='$approval_path_name' and project_id='$project_id' and  deleted_at is NULL");
$rowdaapproval_path_name_data = $approval_path_name_data->fetch();
 
$approval_path_name_id=$rowdaapproval_path_name_data["id"];

}



	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		if(!empty($_POST["employee_id".$i])){
		$employee_id=strip_tags($_POST["employee_id".$i]);
		if(!empty($_POST["project_approval_path_id".$i])){
			
		$project_approval_path_id=$_POST["project_approval_path_id".$i];	
		$pdo->query("UPDATE `project_approval_path_inforamtion` set approval_path_name='$approval_path_name_id',project_id='$project_id',employee_id='$employee_id',updated_by='$LoginReGiSterSession',updated_at='$current_time' where id='$project_approval_path_id'");	
				
		}else{
		$pdo->query("INSERT INTO `project_approval_path_inforamtion`(approval_path_name,`project_id`, `employee_id`, `created_by`,`created_at`) VALUES ('$approval_path_name_id','$project_id','$employee_id','$LoginReGiSterSession','$current_time')");	
				
		}	
		
		
     }
		
	
	}
	
	
	}	
	
	
	
	
$_SESSION['success_message']=$success_message_data;
  echo "<script>window.open('?$page_title/$MenuName','_self')</script>"; 	
	
	
	
}



// Project Approval Information End

// Project Material Distribution Start

if(isset($_POST["Material_Received_Status_Create"])){
    
    
    
 
    $invoice_id=$_POST["invoice_id"];
	$project_id=$_POST["project_id"];
	$store_id=$_POST["store_id"];
    $date=$current_date;
    $distribution_id=$_POST["distribution_id"];

	
$distribution_comments='';
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		if(!empty($_POST["received_quantity".$i])){
		$received_quantity=strip_tags($_POST["received_quantity".$i]);
		$edit_id=strip_tags($_POST["edit_id".$i]);
		$product_id=strip_tags($_POST["product_id".$i]);
		
		$Product_name = $pdo->query("SELECT *  FROM `distribution_history` where id='$edit_id'");
        $rowdataserach = $Product_name->fetch();
		$distribution_quantity=$rowdataserach["distribution_quantity"];
		
		
		$Requistion_detail_identification = $pdo->query("SELECT *  FROM `requestion_detail` where product_id='$product_id' and invoice_id='$invoice_id' and project_id='$project_id' and store_id='$store_id'");
        $rowdaRequistion_detail_identification = $Requistion_detail_identification->fetch();
		$received_quantity_previous=$rowdaRequistion_detail_identification["received_quantity"];
		$total_received_quantity=$received_quantity_previous+$received_quantity;
		$pdo->query("UPDATE `requestion_detail` set received_quantity='$total_received_quantity'  where product_id='$product_id' and invoice_id='$invoice_id' and project_id='$project_id' and store_id='$store_id'");
			
		
			
		$new_due_quantity=$distribution_quantity-$received_quantity;	
		if($new_due_quantity!=0){
			$distribution_comments='Pending';
		}	
			
			
			
		$pdo->query("UPDATE `distribution_history` set received_quantity='$received_quantity',received_date='$current_date',received_by='$created_by',received_at='$created_at',received_status='Complete' where id='$edit_id'");	
		
					
   
     
		
	
	}
	
	}	
	}
	
	if(!empty($distribution_comments)){
	$pdo->query("UPDATE `distribution_summary` set received_status='$distribution_comments',received_date='$current_date',received_by='$created_by',received_at='$created_at'  where invoice_id='$invoice_id' and distribution_id='$distribution_id'");		
	}else{
	$pdo->query("UPDATE `distribution_summary` set received_status='Complete',received_date='$current_date',received_by='$created_by',received_at='$created_at'  where invoice_id='$invoice_id' and distribution_id='$distribution_id'");	
	}
	

	
	$_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?Received_History_Indivisual/$invoice_id/$distribution_id','_self')</script>"; 	   
    
    
    
}



if(isset($_POST["Fund_Material_Received_Status_Create"])){
    
    $invoice_id=$_POST["invoice_id"];
	$project_id=$_POST["project_id"];
	$store_id=$_POST["store_id"];
    $date=$current_date;
    $distribution_id=$_POST["distribution_id"];

	
$distribution_comments='';
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		if(!empty($_POST["received_amount".$i])){
		$received_amount=strip_tags($_POST["received_amount".$i]);
		$edit_id=strip_tags($_POST["edit_id".$i]);
		$product_id=strip_tags($_POST["product_id".$i]);
		
		
		
		$Product_name = $pdo->query("SELECT *  FROM `distribution_history` where id='$edit_id'");
        $rowdataserach = $Product_name->fetch();
		$distribution_amount=$rowdataserach["distribution_amount"];
		
		
		$Requistion_detail_identification = $pdo->query("SELECT *  FROM `requestion_detail` where product_id='$product_id' and invoice_id='$invoice_id' and project_id='$project_id' and store_id='$store_id'");
        $rowdaRequistion_detail_identification = $Requistion_detail_identification->fetch();
		$received_quantity_previous=$rowdaRequistion_detail_identification["received_amount"];
		$total_received_quantity=$received_quantity_previous+$received_amount;
		$pdo->query("UPDATE `requestion_detail` set received_amount='$total_received_quantity'  where product_id='$product_id' and invoice_id='$invoice_id' and project_id='$project_id' and store_id='$store_id'");
		
			
		
			
		$new_due_quantity=$distribution_amount-$received_amount;	
		if($new_due_quantity!=0){
			$distribution_comments='Pending';
		}	
			
			
			
		$pdo->query("UPDATE `distribution_history` set received_amount='$received_amount',received_date='$current_date',received_by='$created_by',received_at='$created_at',received_status='Complete' where id='$edit_id'");	
		
					
   
     
		
	
	}
	
	}	
	}
	
	if(!empty($distribution_comments)){
	$pdo->query("UPDATE `distribution_summary` set received_status='$distribution_comments',received_date='$current_date',received_by='$created_by',received_at='$created_at'  where invoice_id='$invoice_id' and distribution_id='$distribution_id'");		
	}else{
	$pdo->query("UPDATE `distribution_summary` set received_status='Complete',received_date='$current_date',received_by='$created_by',received_at='$created_at'  where invoice_id='$invoice_id' and distribution_id='$distribution_id'");	
	}
	

	
	$_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?Received_History_Indivisual/$invoice_id/$distribution_id','_self')</script>"; 	   
	
}




// Project Material Distribution End













// Return History Start
if(isset($_POST["Insert_Return_History_Create"])){



	$DoctorPHOTO=null;
    $store_id=$_POST["store_id"];
	$project_id=$_POST["project_id"];
	$date=$_POST["date"];
	$note=$_POST["note"];

    $date=date("Y-m-d", strtotime($_POST["date"]));


	
if(!empty($_FILES['photo']['name'])){
	  $tempPHOTO = explode(".", $_FILES['photo']['name']);
      $DoctorPHOTO = $tempPHOTO["0"].round(microtime(true)) . '.' . end($tempPHOTO);
      $file_tmpPHOTO= $_FILES['photo']['tmp_name'];
      move_uploaded_file($file_tmpPHOTO, "ReturnHistory/" . $DoctorPHOTO);
	  $image_field_namePHOTO=',photo';
	  $image_valuePHOTO=",'".$DoctorPHOTO."'";
	  }else{
		$image_field_namePHOTO='';
	    $image_valuePHOTO="";  
		  }
	

	
	$invoice_information = $pdo->query("select * from return_history order by id DESC limit 0,1");
    $invoicerowdata = $invoice_information->fetch();
	if(empty($invoicerowdata["invoice_id"])){ 
		$invoice_id="10001";
	}else{ 
		$invoice_id=$invoicerowdata["invoice_id"]+1;
	}

	
	$resultitle = array();
	$transaction_title='';	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		
		$name_input=strip_tags($_POST["name".$i]);
		$product_id=strip_tags($_POST["product_id".$i]);
		$distribution_quantity=strip_tags($_POST["distribution_quantity".$i]);
		$return_quantity=strip_tags($_POST["return_quantity".$i]);
		$used_quantity=strip_tags($_POST["used_quantity".$i]);
		$damage_quantity=strip_tags($_POST["damage_quantity".$i]);
		$total_quantity=strip_tags($_POST["total_quantity".$i]);
	
		
	
		
		$Product_information = $pdo->query("SELECT *  FROM `stock_information` where product_id='$product_id' and store_id='$store_id' ");
        $rowdataProduct_information = $Product_information->fetch();
		
		if(!empty($rowdataProduct_information["product_id"])){
			
		$product_primary_id=$rowdataProduct_information["id"];	
		$return_previous=$rowdataProduct_information["return"];
		$total_previous=$rowdataProduct_information["total"];
		$stock_previous=$rowdataProduct_information["stock"];
			
			
		$return_current=$return_previous+$return_quantity;
		$return_total=$total_previous+$return_quantity;
		$stock_current=$stock_previous+$return_quantity;

		$updateStockReturn=$pdo->prepare("UPDATE `stock_information` SET `return`=:return_quantity,`total`=:total_quantity,`stock`=:stock_quantity WHERE `id`=:id");
		$updateStockReturn->execute(array(
			':return_quantity'=>$return_current,
			':total_quantity'=>$return_total,
			':stock_quantity'=>$stock_current,
			':id'=>$product_primary_id
		));
			
		$pdo->query("INSERT INTO `return_history_detail`(`invoice_id`, `date`,`project_id`, `store_id`, `product_id`, `requestion_quantity`, `return_quantity`, `used_quantity`, `damage_quantity`, `total_quantity`, `created_by`, `created_at`) VALUES ('$invoice_id','$date','$project_id','$store_id','$product_id','$distribution_quantity','$return_quantity','$used_quantity','$damage_quantity','$total_quantity','$created_by','$created_at')");	
			
			
		}else{
		   $pdo->query("INSERT INTO `stock_information`(`store_id`, `product_id`,`return`, `total`, `stock`, `created_by`, `created_at`) VALUES ('$store_id','$product_id','$return_quantity','$return_quantity','$return_quantity','$created_by','$created_at')"); 
		$pdo->query("INSERT INTO `return_history_detail`(`invoice_id`, `date`,`project_id`, `store_id`, `product_id`, `requestion_quantity`, `return_quantity`, `used_quantity`, `damage_quantity`, `total_quantity`, `created_by`, `created_at`) VALUES ('$invoice_id','$date','$project_id','$store_id','$product_id','$distribution_quantity','$return_quantity','$used_quantity','$damage_quantity','$total_quantity','$created_by','$created_at')");	
			    
		}
		
					
    
		
				
   
     }
		
	
	}
	
	
	
	$sql="INSERT INTO `return_history`(`invoice_id`, `date`, `photo`,`note`,`project_id`, `store_id`, `created_by`, `created_at`) VALUES (:invoice_id,:date,:photo,:note,:project_id,:store_id,:created_by,:created_at)";

     $insert_data= $pdo->prepare($sql);
	 $insert_data->bindparam(":invoice_id", $invoice_id);
	 $insert_data->bindparam(":date", $date);
	 $insert_data->bindparam(":photo", $DoctorPHOTO);
	 $insert_data->bindparam(":note", $note);	 
	 $insert_data->bindparam(":project_id", $project_id);
	 $insert_data->bindparam(":store_id", $store_id);
	 $insert_data->bindparam(":created_by", $created_by);
	 $insert_data->bindparam(":created_at", $created_at);
	 $insert_data->execute(); 
	
	
	
	
	 $_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?Return_History_View/$invoice_id','_self')</script>"; 	

	
}


//  Return History End





// Stock Transfer Received Start

if(isset($_POST["Stock_Transfer_Received_Pending_List_Create"])){
    
    
    
 
    $transfer_id=$_POST["transfer_id"];
	

	
$distribution_comments='';
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		if(!empty($_POST["received_quantity".$i])){
		$received_quantity=strip_tags($_POST["received_quantity".$i]);
		$edit_id=strip_tags($_POST["edit_id".$i]);
		
		
		$Product_name = $pdo->query("SELECT *  FROM `stock_transfer_information` where id='$edit_id'");
        $rowdataserach = $Product_name->fetch();
		$distribution_quantity=$rowdataserach["quantity"];
			
		
			
		$new_due_quantity=$distribution_quantity-$received_quantity;	
		if($new_due_quantity!=0){
			$distribution_comments='Pending';
		}	
			
			
			
		$pdo->query("UPDATE `stock_transfer_information` set received_quantity='$received_quantity',received_date='$current_date',received_by='$created_by',received_at='$created_at',received_status='Complete' where id='$edit_id'");	
		
					
   
     
		
	
	}
	
	}	
	}
	
	if(!empty($distribution_comments)){
	$pdo->query("UPDATE `stock_transfer_summary` set received_status='$distribution_comments',received_date='$current_date',received_by='$created_by',received_at='$created_at'  where  transfer_id='$transfer_id'");		
	}else{
	$pdo->query("UPDATE `stock_transfer_summary` set received_status='Complete',received_date='$current_date',received_by='$created_by',received_at='$created_at'  where  transfer_id='$transfer_id'");	
	}
	

	
	$_SESSION['success_message']=$success_message_data;
     echo "<script>window.open('?Stock_Transfer_Received_History_Detail/$transfer_id/$transfer_id','_self')</script>"; 	   
    
    

    
}


//Stock Transfer Received  End


//Asset Information Create

if(isset($_POST["Insert_asset_information_Create"])){
$date=$_POST["date"];   


	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		if(!empty($_POST["name".$i])){
		$name=strip_tags($_POST["name".$i]);
		$using_person_name=strip_tags($_POST["using_person_name".$i]);
		$description=strip_tags($_POST["description".$i]);
		$quantity=strip_tags($_POST["quantity".$i]);
		
		
		$Product_name = $pdo->query("SELECT *  FROM `asset_product_information` where name='$name'");
        $rowdataserach = $Product_name->fetch();
		$product_id=$rowdataserach["id"];
		$stock=$rowdataserach["stock"];	
		$new_stock=$stock+$quantity;
			
		$pdo->query("UPDATE `asset_product_information` SET `stock`='$new_stock' WHERE id='$product_id'");	
		
	$sql="INSERT INTO `asset_product_detail_history`(`product_id`, `date`, `using_person_name`, `description`, `quantity`, `created_by`, `created_at`) VALUES (:product_id,:date,:using_person_name,:description,:quantity,:created_by,:created_at)";

     $insert_data= $pdo->prepare($sql);
	 $insert_data->bindparam(":product_id", $product_id);
	 $insert_data->bindparam(":date", $date);
	 $insert_data->bindparam(":using_person_name", $using_person_name);
	 $insert_data->bindparam(":description", $description);	 
	 $insert_data->bindparam(":quantity", $quantity);
	 $insert_data->bindparam(":created_by", $created_by);
	 $insert_data->bindparam(":created_at", $created_at);
	 $insert_data->execute(); 				
   
     
		
	
	}
	
	}	
	}    
    
 $_SESSION['success_message']=$success_message_data;
  echo "<script>window.open('?$page_title/$MenuName','_self')</script>";    
}



if(isset($_POST["asset_information_Data_Edit"])){
$product_id=$_POST["product_id"];   
$edit_id_data=$_POST["edit_id"]; 
$using_person_name=$_POST["using_person_name"];   
$description=$_POST["description"];   
$quantity=$_POST["quantity"];
$previous_quantity=$_POST["previous_quantity"];

		
$Product_name = $pdo->query("SELECT *  FROM `asset_product_information` where id='$product_id'");
$rowdataserach = $Product_name->fetch();
$product_id=$rowdataserach["id"];
$stock=$rowdataserach["stock"];	
$new_stock=($stock-$previous_quantity)+$quantity;
			
$pdo->query("UPDATE `asset_product_information` SET `stock`='$new_stock' WHERE id='$product_id'");	


		
$sql="UPDATE `asset_product_detail_history` SET `using_person_name`=:using_person_name,`description`=:description,`quantity`=:quantity,`updated_by`=:updated_by,`updated_at`=:updated_at  WHERE id='$edit_id_data'";

     $insert_data= $pdo->prepare($sql);
	 $insert_data->bindparam(":using_person_name", $using_person_name);
	 $insert_data->bindparam(":description", $description);	 
	 $insert_data->bindparam(":quantity", $quantity);
	 $insert_data->bindparam(":updated_by", $created_by);
	 $insert_data->bindparam(":updated_at", $created_at);
	 $insert_data->execute(); 				
   
    
    
 $_SESSION['success_message']=$success_message_data;
  echo "<script>window.open('?$page_title/$MenuName','_self')</script>";    
}

//Asset Information End


// Used Material Distribution History Start
if(isset($_POST["Material_Used_Distribution_Create"])){


	$project_id=$_POST["project_id"];
	$date=$_POST["date"];
	$note=$_POST["note"];
	$material_used_type=$_POST["material_used_type"];
    $date=date("Y-m-d", strtotime($_POST["date"]));


	
	$invoice_information = $pdo->query("select * from material_used_summary order by id DESC limit 0,1");
    $invoicerowdata = $invoice_information->fetch();
	if(empty($invoicerowdata["invoice_id"])){ 
	$invoice_id="10001";
	}else{ 
	$invoice_id=$invoicerowdata["invoice_id"]+1;
	}

	
	$resultitle = array();
	$transaction_title='';	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		
	
		$quantity=strip_tags($_POST["quantity".$i]);
		$product_id=strip_tags($_POST["product_id".$i]);
	

     $pdo->query("INSERT INTO `material_used_detail_history`(`invoice_id`, `date`, `employee_id`, `project_id`, `product_id`, `used_quantity`, `created_by`,`created_at`) VALUES ('$invoice_id','$date','$created_by','$project_id','$product_id','$quantity','$created_by','$created_at')");	

     }
	
	
	}
	
	
	
    $sql="INSERT INTO `material_used_summary`(`invoice_id`, `date`, `employee_id`, `project_id`, `material_used_type`,`note`, `created_by`, `created_at`) VALUES (:invoice_id,:date,:employee_id,:project_id,:material_used_type,:note,:created_by,:created_at)";

     $insert_data= $pdo->prepare($sql);
     $insert_data->bindparam(":invoice_id", $invoice_id);
	 $insert_data->bindparam(":date", $date);
	 $insert_data->bindparam(":employee_id", $created_by);
	 $insert_data->bindparam(":project_id", $project_id);
	 $insert_data->bindparam(":material_used_type", $material_used_type);
	 $insert_data->bindparam(":note", $note);	 
	 $insert_data->bindparam(":created_by", $created_by);
	 $insert_data->bindparam(":created_at", $created_at);
	 $insert_data->execute();
	
	
	
	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Project_Material_Used_History_View/$invoice_id','_self')</script>"; 	

	
}

if(isset($_POST["Fund_Used_Distribution_Create"])){


	$project_id=$_POST["project_id"];
	$date=$_POST["date"];
	$note=$_POST["note"];
	$material_used_type=$_POST["material_used_type"];
    $date=date("Y-m-d", strtotime($_POST["date"]));


	
	$invoice_information = $pdo->query("select * from material_used_summary order by id DESC limit 0,1");
    $invoicerowdata = $invoice_information->fetch();
	if(empty($invoicerowdata["invoice_id"])){ 
	$invoice_id="10001";
	}else{ 
	$invoice_id=$invoicerowdata["invoice_id"]+1;
	}

	
	$resultitle = array();
	$transaction_title='';	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		
	
		$quantity=strip_tags($_POST["used_amount".$i]);
		$product_id=strip_tags($_POST["product_id".$i]);
	

     $pdo->query("INSERT INTO `material_used_detail_history`(`invoice_id`, `date`, `employee_id`, `project_id`, `product_id`, `used_amount`, `created_by`,`created_at`) VALUES ('$invoice_id','$date','$created_by','$project_id','$product_id','$quantity','$created_by','$created_at')");	

     }
	
	
	}
	
	
	
    $sql="INSERT INTO `material_used_summary`(`invoice_id`, `date`, `employee_id`, `project_id`, `material_used_type`,`note`, `created_by`, `created_at`) VALUES (:invoice_id,:date,:employee_id,:project_id,:material_used_type,:note,:created_by,:created_at)";

     $insert_data= $pdo->prepare($sql);
     $insert_data->bindparam(":invoice_id", $invoice_id);
	 $insert_data->bindparam(":date", $date);
	 $insert_data->bindparam(":employee_id", $created_by);
	 $insert_data->bindparam(":project_id", $project_id);
	 $insert_data->bindparam(":material_used_type", $material_used_type);
	 $insert_data->bindparam(":note", $note);	 
	 $insert_data->bindparam(":created_by", $created_by);
	 $insert_data->bindparam(":created_at", $created_at);
	 $insert_data->execute();
	
	
	
	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Project_Material_Used_History_View/$invoice_id','_self')</script>"; 	

	
}

// Used Material Distribution History End


//Requistion History Draft Start
if(isset($_POST["Insert_Fund_Requestion_History_Draft"])){



    $store_id=$_POST["store_id"];
	$project_id=$_POST["project_id"];
	$date=$_POST["date"];
	$note=$_POST["note"];
	$requistion_type=$_POST["requistion_type"];
	$previous_cash_in_hand=$_POST["previous_cash_in_hand"];
	$sub_total_amount=$_POST["sub_total_amount"];
    $date=date("Y-m-d", strtotime($_POST["date"]));


	
	$invoice_information = $pdo->query("select * from requestion_draft_histiory order by id DESC limit 0,1");
    $invoicerowdata = $invoice_information->fetch();
	if(empty($invoicerowdata["invoice_id"])){ 
	$invoice_id="10001";
	}else{ 
	$invoice_id=$invoicerowdata["invoice_id"]+1;
	}
	
	
	if(!empty($_FILES['multiple_photo']['name'])){
        $countfiles = count($_FILES['multiple_photo']['name']);
        $result = array();
      for($i=0;$i<$countfiles;$i++){
      // Getting file name
            $filename = $_FILES['multiple_photo']['name'][$i];
            $temp = explode(".", $filename);
            $newfilenameMultiple = $temp["0"].round(microtime(true)) . '.' . end($temp);
            // Valid extension
            $valid_ext = array('png','jpeg','jpg','pdf');

            // Location
            $location = "RequistionAttachment/".$newfilenameMultiple;

            // file extension
            $file_extension = pathinfo($location, PATHINFO_EXTENSION);
            $file_extension = strtolower($file_extension);
            
            array_push($result,array("name"=>$newfilenameMultiple)
             );
          if(end($temp)=='pdf'){
           move_uploaded_file($_FILES['multiple_photo']['tmp_name'][$i], "RequistionAttachment/".$newfilenameMultiple);   
          } 

      }
	$image_data_multiple_photo=json_encode($result);	
     }else{
	  $image_data_multiple_photo="";  
	 }


	
	$resultitle = array();
	$transaction_title='';	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		
		$name_input=strip_tags($_POST["name".$i]);
		$quantity=strip_tags($_POST["requestion_amount".$i]);
		$detail=strip_tags($_POST["detail".$i]);
		$comment=strip_tags($_POST["comment".$i]);
		$requestion_quantity=strip_tags($_POST["quantity".$i]);
		$requistion_rate=strip_tags($_POST["rate".$i]);
	
		
		
		$Product_name_output = $pdo->query("SELECT *  FROM `product_information` where name='$name_input'");
        $rowdataserach_output = $Product_name_output->fetch();
		$name_id=$rowdataserach_output["id"];
		$unit=$rowdataserach_output["unit"];

		$pdo->query("INSERT INTO `requestion_draft_detail`(`invoice_id`, `date`, `employee_id`, `project_id`, `store_id`, `product_id`, `detail`, `comment`, `requestion_quantity`, `requistion_rate`, `requestion_amount`, `created_by`, `created_at`) VALUES ('$invoice_id','$date','$created_by','$project_id','$store_id','$name_id','$detail','$comment','$requestion_quantity','$requistion_rate','$quantity','$created_by','$created_at')");
		
				
   
     }
	
	
	}
	
    $sql="INSERT INTO `requestion_draft_histiory`(`invoice_id`, `date`, `employee_id`, `project_id`, `store_id`, `note`, `requistion_type`, `previous_cash_in_hand`, `sub_total_amount`, `created_by`,`created_at`,multiple_photo) VALUES (:invoice_id,:date,:employee_id,:project_id,:store_id,:note,:requistion_type,:previous_cash_in_hand,:sub_total_amount,:created_by,:created_at,:multiple_photo)";

     $insert_data= $pdo->prepare($sql);
     $insert_data->bindparam(":invoice_id", $invoice_id);
	 $insert_data->bindparam(":date", $date);
	 $insert_data->bindparam(":employee_id", $created_by);
	 $insert_data->bindparam(":project_id", $project_id);
	 $insert_data->bindparam(":store_id", $store_id);
	 $insert_data->bindparam(":note", $note);
	 $insert_data->bindparam(":requistion_type", $requistion_type);	
	 $insert_data->bindparam(":previous_cash_in_hand", $previous_cash_in_hand);
	 $insert_data->bindparam(":sub_total_amount", $sub_total_amount);
	 $insert_data->bindparam(":created_by", $created_by);
	 $insert_data->bindparam(":created_at", $created_at);
	 $insert_data->bindparam(":multiple_photo", $image_data_multiple_photo);
	 $insert_data->execute();
	
	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Requestion_Draft_History_Detail/$invoice_id','_self')</script>"; 	

	
}

if(isset($_POST["Insert_Requestion_History_Draft"])){


    $store_id=$_POST["store_id"];
	$project_id=$_POST["project_id"];
	$date=$_POST["date"];
	$note=$_POST["note"];
	$requistion_type=$_POST["requistion_type"];
    $date=date("Y-m-d", strtotime($_POST["date"]));


	
	$invoice_information = $pdo->query("select * from requestion_draft_histiory order by id DESC limit 0,1");
    $invoicerowdata = $invoice_information->fetch();
	if(empty($invoicerowdata["invoice_id"])){ 
	$invoice_id="10001";
	}else{ 
	$invoice_id=$invoicerowdata["invoice_id"]+1;
	}

	
		$resultitle = array();
		$transaction_title='';
		$savedDraftItemCount=0;
		$mandatoryEmergencyStatement=$pdo->prepare("SELECT emergency_request_detail.product_id,product_information.name FROM emergency_request_detail INNER JOIN emergency_request ON emergency_request.id=emergency_request_detail.emergency_request_id INNER JOIN product_information ON product_information.id=emergency_request_detail.product_id WHERE emergency_request.project_id=:project_id AND emergency_request.store_id=:store_id AND emergency_request.status='completed' AND emergency_request.deleted_at IS NULL AND emergency_request_detail.issued_quantity>emergency_request_detail.reconciled_quantity AND NOT EXISTS (SELECT 1 FROM requestion_draft_detail reserved_detail INNER JOIN requestion_draft_histiory reserved_header ON reserved_header.invoice_id=reserved_detail.invoice_id WHERE reserved_header.final_submit_status IS NULL AND reserved_header.deleted_at IS NULL AND reserved_detail.deleted_at IS NULL AND FIND_IN_SET(CAST(emergency_request_detail.id AS CHAR) COLLATE utf8mb4_unicode_ci,reserved_detail.emergency_detail_ids COLLATE utf8mb4_unicode_ci)) GROUP BY emergency_request_detail.product_id,product_information.name");
		$mandatoryEmergencyStatement->execute(array(':project_id'=>$project_id,':store_id'=>$store_id));
		$mandatoryEmergencyProducts=$mandatoryEmergencyStatement->fetchAll();
		foreach($mandatoryEmergencyProducts as $mandatoryEmergencyProduct){
			$mandatoryProductFound=false;
			$submittedRowCount=!empty($_POST["number_count"])?(int)$_POST["number_count"]:0;
			for($submittedIndex=1;$submittedIndex<=$submittedRowCount;$submittedIndex++){
				$submittedProductId=!empty($_POST["product_id".$submittedIndex])?(int)$_POST["product_id".$submittedIndex]:0;
				$submittedProductName=isset($_POST["name".$submittedIndex])?trim($_POST["name".$submittedIndex]):'';
				if($submittedProductId===(int)$mandatoryEmergencyProduct['product_id'] || strcasecmp($submittedProductName,$mandatoryEmergencyProduct['name'])===0){
					$mandatoryProductFound=true;
					break;
				}
			}
			if(!$mandatoryProductFound){
				$_SESSION['warning_message']='Outstanding emergency products are required and cannot be removed from the requisition draft.';
				echo "<script>window.open('?Requisition_Draft_Create','_self')</script>";
				exit;
			}
		}

		if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		if(empty($_POST["name".$i])){
			continue;
		}

		$name_input=trim(strip_tags($_POST["name".$i]));
		$product_code_input=isset($_POST["product_code".$i]) ? trim(strip_tags($_POST["product_code".$i])) : '';
		$quantity=isset($_POST["quantity".$i]) && $_POST["quantity".$i] !== '' ? (float)$_POST["quantity".$i] : 0;
		$requistion_rate=isset($_POST["requistion_rate".$i]) && $_POST["requistion_rate".$i] !== '' ? (float)$_POST["requistion_rate".$i] : 0;
		$comment=strip_tags($_POST["comment".$i]);
		$emergency_detail_ids=isset($_POST["emergency_detail_ids".$i]) ? preg_replace('/[^0-9,]/','',$_POST["emergency_detail_ids".$i]) : '';
		$emergency_quantity=isset($_POST["emergency_quantity".$i]) ? (float)$_POST["emergency_quantity".$i] : 0;
		if($quantity < 0 || $requistion_rate < 0){
			$_SESSION['warning_message']='Quantity and rate cannot be negative.';
			echo "<script>window.open('?Requisition_Draft_Create','_self')</script>";
			exit;
		}
		if($quantity < $emergency_quantity){
			$_SESSION['warning_message']='Total Required cannot be less than the emergency quantity already issued.';
			echo "<script>window.open('?Requisition_Draft_Create','_self')</script>";
			exit;
		}
	
		
		
		$name_id=isset($_POST["product_id".$i]) ? (int)$_POST["product_id".$i] : 0;
		$rowdataserach_output=false;
		if($name_id>0){
			$productLookup=$pdo->prepare("SELECT * FROM product_information WHERE id=:id AND deleted_at IS NULL LIMIT 1");
			$productLookup->execute(array(':id'=>$name_id));
			$rowdataserach_output=$productLookup->fetch();
		}
		if(!$rowdataserach_output && $product_code_input!==''){
			$productLookup=$pdo->prepare("SELECT * FROM product_information WHERE code=:code AND deleted_at IS NULL LIMIT 1");
			$productLookup->execute(array(':code'=>$product_code_input));
			$rowdataserach_output=$productLookup->fetch();
		}
		if(!$rowdataserach_output){
			$productLookup=$pdo->prepare("SELECT * FROM product_information WHERE name=:name AND deleted_at IS NULL LIMIT 1");
			$productLookup->execute(array(':name'=>$name_input));
			$rowdataserach_output=$productLookup->fetch();
		}
		if(!$rowdataserach_output){ continue; }
		$name_id=(int)$rowdataserach_output["id"];
		$unit=$rowdataserach_output["unit"];
		if($emergency_detail_ids===''){
			$automaticEmergency=$pdo->prepare("SELECT GROUP_CONCAT(emergency_request_detail.id ORDER BY emergency_request_detail.id) AS emergency_detail_ids,COALESCE(SUM(emergency_request_detail.issued_quantity-emergency_request_detail.reconciled_quantity),0) AS emergency_quantity FROM emergency_request_detail INNER JOIN emergency_request ON emergency_request.id=emergency_request_detail.emergency_request_id WHERE emergency_request.project_id=:project_id AND emergency_request.store_id=:store_id AND emergency_request_detail.product_id=:product_id AND emergency_request.status='completed' AND emergency_request.deleted_at IS NULL AND emergency_request_detail.issued_quantity>emergency_request_detail.reconciled_quantity AND NOT EXISTS (SELECT 1 FROM requestion_draft_detail reserved_detail INNER JOIN requestion_draft_histiory reserved_header ON reserved_header.invoice_id=reserved_detail.invoice_id WHERE reserved_header.final_submit_status IS NULL AND reserved_header.deleted_at IS NULL AND reserved_detail.deleted_at IS NULL AND FIND_IN_SET(CAST(emergency_request_detail.id AS CHAR) COLLATE utf8mb4_unicode_ci,reserved_detail.emergency_detail_ids COLLATE utf8mb4_unicode_ci))");
			$automaticEmergency->execute(array(':project_id'=>$project_id,':store_id'=>$store_id,':product_id'=>$name_id));
			$automaticEmergencyData=$automaticEmergency->fetch();
			if(!empty($automaticEmergencyData['emergency_detail_ids'])){
				$emergency_detail_ids=$automaticEmergencyData['emergency_detail_ids'];
				$emergency_quantity=(float)$automaticEmergencyData['emergency_quantity'];
			}
		}
		if((float)$quantity < $emergency_quantity){
			$_SESSION['warning_message']='Total Required cannot be less than the outstanding emergency quantity already issued.';
			echo "<script>window.open('?Requisition_Draft_Create','_self')</script>";
			exit;
		}
		if($emergency_detail_ids!==''){
			$emergencyIds=array_values(array_unique(array_filter(array_map('intval',explode(',',$emergency_detail_ids)))));
			$placeholders=implode(',',array_fill(0,count($emergencyIds),'?'));
			$emergencyValidation=$pdo->prepare("SELECT COUNT(*) AS item_count,COALESCE(SUM(emergency_request_detail.issued_quantity-emergency_request_detail.reconciled_quantity),0) AS outstanding_quantity FROM emergency_request_detail INNER JOIN emergency_request ON emergency_request.id=emergency_request_detail.emergency_request_id WHERE emergency_request_detail.id IN ($placeholders) AND emergency_request.project_id=? AND emergency_request.store_id=? AND emergency_request_detail.product_id=? AND emergency_request.status='completed' AND emergency_request.deleted_at IS NULL AND emergency_request_detail.issued_quantity>emergency_request_detail.reconciled_quantity AND NOT EXISTS (SELECT 1 FROM requestion_draft_detail reserved_detail INNER JOIN requestion_draft_histiory reserved_header ON reserved_header.invoice_id=reserved_detail.invoice_id WHERE reserved_header.final_submit_status IS NULL AND reserved_header.deleted_at IS NULL AND reserved_detail.deleted_at IS NULL AND FIND_IN_SET(CAST(emergency_request_detail.id AS CHAR) COLLATE utf8mb4_unicode_ci,reserved_detail.emergency_detail_ids COLLATE utf8mb4_unicode_ci))");
			$emergencyValidation->execute(array_merge($emergencyIds,array($project_id,$store_id,$name_id)));
			$emergencyValidationResult=$emergencyValidation->fetch();
			if((int)$emergencyValidationResult['item_count']!==count($emergencyIds) || abs((float)$emergencyValidationResult['outstanding_quantity']-$emergency_quantity)>0.0001){
				$_SESSION['warning_message']='This emergency quantity is already attached to another draft or has been finalized.';
				echo "<script>window.open('?Requisition_Draft_Create','_self')</script>";
				exit;
			}
			$emergency_detail_ids=implode(',',$emergencyIds);
		}
	
		$pdo->query("INSERT INTO `requestion_draft_detail`(`invoice_id`, `date`, `employee_id`, `project_id`, `store_id`, `product_id`, `emergency_detail_ids`, `emergency_quantity`, `comment`, `requestion_quantity`,`requistion_rate`,`created_by`, `created_at`) VALUES ('$invoice_id','$date','$created_by','$project_id','$store_id','$name_id','$emergency_detail_ids','$emergency_quantity','$comment','$quantity','$requistion_rate','$created_by','$created_at')");
		$savedDraftItemCount++;
		
				
   
     }
	

	}
	if($savedDraftItemCount===0){
		$_SESSION['warning_message']='A requisition draft must contain at least one valid product.';
		echo "<script>window.open('?Requisition_Draft_Create','_self')</script>";
		exit;
	}
	if(!empty($_FILES['multiple_photo']['name'])){
        $countfiles = count($_FILES['multiple_photo']['name']);
        $result = array();
      for($i=0;$i<$countfiles;$i++){
      // Getting file name
            $filename = $_FILES['multiple_photo']['name'][$i];
            $temp = explode(".", $filename);
            $newfilenameMultiple = $temp["0"].round(microtime(true)) . '.' . end($temp);
            // Valid extension
            $valid_ext = array('png','jpeg','jpg','pdf');

            // Location
            $location = "RequistionAttachment/".$newfilenameMultiple;

            // file extension
            $file_extension = pathinfo($location, PATHINFO_EXTENSION);
            $file_extension = strtolower($file_extension);
            
            array_push($result,array("name"=>$newfilenameMultiple)
             );
          if(end($temp)=='pdf'){
           move_uploaded_file($_FILES['multiple_photo']['tmp_name'][$i], "RequistionAttachment/".$newfilenameMultiple);   
          } 

      }
	$image_data_multiple_photo=json_encode($result);	
     }else{
	  $image_data_multiple_photo="";  
	 }
	$sql="INSERT INTO `requestion_draft_histiory`(`invoice_id`, `date`, `employee_id`, `project_id`, `store_id`, `note`, `requistion_type`,`created_by`,`created_at`,multiple_photo) VALUES (:invoice_id,:date,:employee_id,:project_id,:store_id,:note,:requistion_type,:created_by,:created_at,:multiple_photo)";

     $insert_data= $pdo->prepare($sql);
     $insert_data->bindparam(":invoice_id", $invoice_id);
	 $insert_data->bindparam(":date", $date);
	 $insert_data->bindparam(":employee_id", $created_by);
	 $insert_data->bindparam(":project_id", $project_id);
	 $insert_data->bindparam(":store_id", $store_id);
	 $insert_data->bindparam(":note", $note);
	 $insert_data->bindparam(":requistion_type", $requistion_type);	
	 $insert_data->bindparam(":created_by", $created_by);
	 $insert_data->bindparam(":created_at", $created_at);
	 $insert_data->bindparam(":multiple_photo", $image_data_multiple_photo);
	 $insert_data->execute();
	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Requestion_Draft_History_Detail/$invoice_id','_self')</script>"; 	

	
}


if(isset($_POST["Fund_Requestion_Draft_History_Edit"])){



    $store_id=$_POST["store_id"];
	$project_id=$_POST["project_id"];
	$date=$_POST["date"];
	$note=$_POST["note"];
	$requistion_type=$_POST["requistion_type"];
	$previous_cash_in_hand=$_POST["previous_cash_in_hand"];
	$sub_total_amount=$_POST["sub_total_amount"];
	$date=date("Y-m-d", strtotime($_POST["date"]));
	$invoice_id=$_POST["invoice_id"];
	$editableDraftStatement=$pdo->prepare("SELECT id FROM requestion_draft_histiory WHERE id=:id AND invoice_id=:invoice_id AND final_submit_status IS NULL AND deleted_at IS NULL");
	$editableDraftStatement->execute(array(':id'=>(int)$DocumentData,':invoice_id'=>$invoice_id));
	if(!$editableDraftStatement->fetchColumn()){
		$_SESSION['warning_message']='This requisition draft is no longer editable or has already been submitted.';
		echo "<script>window.open('?Requestion_Draft_History_Detail/$invoice_id','_self')</script>";
		exit;
	}


	

	
	$resultitle = array();
	$transaction_title='';	
	
	if(!empty($_POST["number_count"])){
   $number_count=$_POST["number_count"];
				
	for($i=1;$i<=$number_count;$i++){
		
		$name_input=strip_tags($_POST["name".$i]);
		$quantity=strip_tags($_POST["requestion_amount".$i]);
		$detail=strip_tags($_POST["detail".$i]);
		$comment=strip_tags($_POST["comment".$i]);
		$requestion_quantity=strip_tags($_POST["quantity".$i]);
		$requistion_rate=strip_tags($_POST["rate".$i]);
	
		
		$Product_name_output = $pdo->query("SELECT *  FROM `product_information` where name='$name_input'");
        $rowdataserach_output = $Product_name_output->fetch();
		$name_id=$rowdataserach_output["id"];	
		
		
		
	
		if(!empty($_POST["edit_id".$i])){
		$edit_id=$_POST["edit_id".$i];	
		$pdo->query("UPDATE `requestion_draft_detail` SET product_id='$name_id',`detail`='$detail',`comment`='$comment',`requestion_quantity`='$requestion_quantity',`requistion_rate`='$requistion_rate',`requestion_amount`='$quantity',`updated_by`='$created_by',`updated_at`='$created_at' WHERE id='$edit_id'");		
		}else{
		$pdo->query("INSERT INTO `requestion_draft_detail`(`invoice_id`, `date`, `employee_id`, `project_id`, `store_id`, `product_id`, `detail`, `comment`, `requestion_quantity`, `requistion_rate`, `requestion_amount`, `created_by`, `created_at`) VALUES ('$invoice_id','$date','$created_by','$project_id','$store_id','$name_id','$detail','$comment','$requestion_quantity','$requistion_rate','$quantity','$created_by','$created_at')");		
		}
		
		
				
   
     }
	
	
	}
	 
		
    $sql="UPDATE `requestion_draft_histiory` SET `project_id`=:project_id,`store_id`=:store_id,`note`=:note,`requistion_type`=:requistion_type,`previous_cash_in_hand`=:previous_cash_in_hand,`sub_total_amount`=:sub_total_amount,`updated_by`=:updated_by,`updated_at`=:updated_at WHERE id='$DocumentData'";

     $insert_data= $pdo->prepare($sql);
	 $insert_data->bindparam(":project_id", $project_id);
	 $insert_data->bindparam(":store_id", $store_id);
	 $insert_data->bindparam(":note", $note);
	 $insert_data->bindparam(":requistion_type", $requistion_type);	
	 $insert_data->bindparam(":previous_cash_in_hand", $previous_cash_in_hand);
	 $insert_data->bindparam(":sub_total_amount", $sub_total_amount);
	 $insert_data->bindparam(":updated_by", $created_by);
	 $insert_data->bindparam(":updated_at", $created_at);
	 $insert_data->execute();
	
	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Requestion_Draft_History_Detail/$invoice_id','_self')</script>"; 	

	
}


if(isset($_POST["Material_Requestion_Draft_History_Edit"])){



    $store_id=$_POST["store_id"];
	$project_id=$_POST["project_id"];
	$date=$_POST["date"];
	$note=$_POST["note"];
	$requistion_type=$_POST["requistion_type"];
    $date=date("Y-m-d", strtotime($_POST["date"]));
	$invoice_id=$_POST["invoice_id"];
	$editableDraftStatement=$pdo->prepare("SELECT id FROM requestion_draft_histiory WHERE id=:id AND invoice_id=:invoice_id AND final_submit_status IS NULL AND deleted_at IS NULL");
	$editableDraftStatement->execute(array(':id'=>(int)$DocumentData,':invoice_id'=>$invoice_id));
	if(!$editableDraftStatement->fetchColumn()){
		$_SESSION['warning_message']='This requisition draft is no longer editable or has already been submitted.';
		echo "<script>window.open('?Requestion_Draft_History_Detail/$invoice_id','_self')</script>";
		exit;
	}


	

	
	$resultitle = array();
	$transaction_title='';
	$savedDraftItemCount=0;
	$submittedDraftDetailIds=array();

	if(!empty($_POST["number_count"])){
	$number_count=(int)$_POST["number_count"];

	for($i=1;$i<=$number_count;$i++){
		if(!isset($_POST["name".$i]) || trim($_POST["name".$i])===''){ continue; }

		$name_input=trim(strip_tags($_POST["name".$i]));
		$product_code_input=isset($_POST["product_code".$i]) ? trim(strip_tags($_POST["product_code".$i])) : '';
		$comment=isset($_POST["comment".$i]) ? strip_tags($_POST["comment".$i]) : '';
		$requestion_quantity=isset($_POST["quantity".$i]) && $_POST["quantity".$i] !== '' ? (float)$_POST["quantity".$i] : 0;
		$requistion_rate=isset($_POST["requistion_rate".$i]) && $_POST["requistion_rate".$i] !== '' ? (float)$_POST["requistion_rate".$i] : 0;
		$edit_id=isset($_POST["edit_id".$i]) ? (int)$_POST["edit_id".$i] : 0;
		$existingDraftDetail=false;

		if($edit_id>0){
			$existingDraftDetailStatement=$pdo->prepare("SELECT * FROM requestion_draft_detail WHERE id=:id AND invoice_id=:invoice_id AND deleted_at IS NULL");
			$existingDraftDetailStatement->execute(array(':id'=>$edit_id,':invoice_id'=>$invoice_id));
			$existingDraftDetail=$existingDraftDetailStatement->fetch();
			if(!$existingDraftDetail){ continue; }
		}

		if($requestion_quantity < 0 || $requistion_rate < 0){
			$_SESSION['warning_message']='Quantity and rate cannot be negative.';
			echo "<script>window.open('?Requestion_Draft_History_Detail/$invoice_id','_self')</script>";
			exit;
		}

		if($existingDraftDetail && (float)$existingDraftDetail['emergency_quantity']>0){
			$name_id=(int)$existingDraftDetail['product_id'];
			if($requestion_quantity<(float)$existingDraftDetail['emergency_quantity']){
				$_SESSION['warning_message']='Total Required cannot be lower than the quantity already issued through an emergency request.';
				echo "<script>window.open('?Requestion_Draft_History_Detail/$invoice_id','_self')</script>";
				exit;
			}
		}else{
			$name_id=isset($_POST["product_id".$i]) ? (int)$_POST["product_id".$i] : 0;
			$productLookup=false;
			if($name_id>0){
				$productStatement=$pdo->prepare("SELECT id FROM product_information WHERE id=:id AND deleted_at IS NULL LIMIT 1");
				$productStatement->execute(array(':id'=>$name_id));
				$productLookup=$productStatement->fetchColumn();
			}
			if(!$productLookup && $product_code_input!==''){
				$productStatement=$pdo->prepare("SELECT id FROM product_information WHERE code=:code AND deleted_at IS NULL LIMIT 1");
				$productStatement->execute(array(':code'=>$product_code_input));
				$productLookup=$productStatement->fetchColumn();
			}
			if(!$productLookup){
				$productStatement=$pdo->prepare("SELECT id FROM product_information WHERE name=:name AND deleted_at IS NULL LIMIT 1");
				$productStatement->execute(array(':name'=>$name_input));
				$productLookup=$productStatement->fetchColumn();
			}
			$name_id=(int)$productLookup;
		}

		if($name_id<=0){ continue; }

		if($existingDraftDetail){
			$submittedDraftDetailIds[]=$edit_id;
			$updateDraftDetail=$pdo->prepare("UPDATE requestion_draft_detail SET product_id=:product_id,comment=:comment,requestion_quantity=:quantity,requistion_rate=:rate,updated_by=:updated_by,updated_at=:updated_at WHERE id=:id");
			$updateDraftDetail->execute(array(':product_id'=>$name_id,':comment'=>$comment,':quantity'=>$requestion_quantity,':rate'=>$requistion_rate,':updated_by'=>$created_by,':updated_at'=>$created_at,':id'=>$edit_id));
		}else{
			$insertDraftDetail=$pdo->prepare("INSERT INTO requestion_draft_detail (invoice_id,date,employee_id,project_id,store_id,product_id,comment,requestion_quantity,requistion_rate,created_by,created_at) VALUES (:invoice_id,:date,:employee_id,:project_id,:store_id,:product_id,:comment,:quantity,:rate,:created_by,:created_at)");
			$insertDraftDetail->execute(array(':invoice_id'=>$invoice_id,':date'=>$date,':employee_id'=>$created_by,':project_id'=>$project_id,':store_id'=>$store_id,':product_id'=>$name_id,':comment'=>$comment,':quantity'=>$requestion_quantity,':rate'=>$requistion_rate,':created_by'=>$created_by,':created_at'=>$created_at));
			$submittedDraftDetailIds[]=(int)$pdo->lastInsertId();
		}
		$savedDraftItemCount++;
     }


	}

	if($savedDraftItemCount===0){
		$_SESSION['warning_message']='At least one valid product is required.';
		echo "<script>window.open('?Requestion_Draft_History_Detail/$invoice_id','_self')</script>";
		exit;
	}

	$deleteParameters=array($created_by,$created_at,$invoice_id);
	$deleteExclusion='';
	if(!empty($submittedDraftDetailIds)){
		$deleteExclusion=' AND id NOT IN ('.implode(',',array_fill(0,count($submittedDraftDetailIds),'?')).')';
		$deleteParameters=array_merge($deleteParameters,$submittedDraftDetailIds);
	}
	$deleteRemovedRows=$pdo->prepare("UPDATE requestion_draft_detail SET deleted_by=?,deleted_at=? WHERE invoice_id=? AND emergency_quantity=0 AND deleted_at IS NULL".$deleteExclusion);
	$deleteRemovedRows->execute($deleteParameters);
	 
		
    $sql="UPDATE `requestion_draft_histiory` SET `project_id`=:project_id,`store_id`=:store_id,`note`=:note,`requistion_type`=:requistion_type,`updated_by`=:updated_by,`updated_at`=:updated_at WHERE id='$DocumentData'";

     $insert_data= $pdo->prepare($sql);
	 $insert_data->bindparam(":project_id", $project_id);
	 $insert_data->bindparam(":store_id", $store_id);
	 $insert_data->bindparam(":note", $note);
	 $insert_data->bindparam(":requistion_type", $requistion_type);	
	 $insert_data->bindparam(":updated_by", $created_by);
	 $insert_data->bindparam(":updated_at", $created_at);
	 $insert_data->execute();
	
	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Requestion_Draft_History_Detail/$invoice_id','_self')</script>"; 	

	
}





if(isset($_POST["Requisition_Approval_Path_Send_Data_Final"])){



    $store_id=$_POST["store_id"];
	$project_id=$_POST["project_id"];
	$date=$_POST["date"];
	$draft_requistion_id=$_POST["draft_requistion_id"];
	$requistion_type=$_POST["requistion_type"];
	$draft_invoice_id=$_POST["draft_invoice_id"];
    $date=date("Y-m-d", strtotime($_POST["date"]));
	$approval_path_mode='instant';
	$approval_path_name_id=0;

	$draftGuardStatement=$pdo->prepare("SELECT * FROM requestion_draft_histiory WHERE id=:id AND invoice_id=:invoice_id AND final_submit_status IS NULL AND deleted_at IS NULL LIMIT 1");
	$draftGuardStatement->execute(array(':id'=>(int)$draft_requistion_id,':invoice_id'=>$draft_invoice_id));
	$rowdataRequistion_info=$draftGuardStatement->fetch();
	if(!$rowdataRequistion_info){
		$_SESSION['warning_message']='This draft has already been submitted or is no longer available.';
		echo "<script>window.open('?Requisition_Draft','_self')</script>";
		exit;
	}
	$project_id=$rowdataRequistion_info['project_id'];
	$store_id=$rowdataRequistion_info['store_id'];
	$draft_invoice_id=$rowdataRequistion_info['invoice_id'];
	$requistion_type=$rowdataRequistion_info['requistion_type'];
	$draftItemCountStatement=$pdo->prepare("SELECT COUNT(*) FROM requestion_draft_detail WHERE invoice_id=:invoice_id AND project_id=:project_id AND deleted_at IS NULL");
	$draftItemCountStatement->execute(array(':invoice_id'=>$draft_invoice_id,':project_id'=>$project_id));
	if((int)$draftItemCountStatement->fetchColumn()===0){
		$_SESSION['warning_message']='This draft has no valid products and cannot be submitted.';
		echo "<script>window.open('?Requestion_Draft_History_Detail/$draft_invoice_id','_self')</script>";
		exit;
	}
	if($approval_path_mode==='instant'){
		$instantEmployees=array();
		$instantNumberCount=!empty($_POST["instant_number_count"]) ? (int)$_POST["instant_number_count"] : 0;
		for($i=1;$i<=$instantNumberCount;$i++){
			if(!empty($_POST["instant_employee_id".$i])){
				$instantEmployees[]=(int)$_POST["instant_employee_id".$i];
			}
		}
		$instantEmployees=array_values(array_filter($instantEmployees));
		if(empty($instantEmployees)){
			$_SESSION['warning_message']='Please select approval employees.';
			echo "<script>window.open('?Requisition_Approval_Path_Send/$draft_requistion_id','_self')</script>";
			exit;
		}
		if((int)end($instantEmployees)!==1){
			$_SESSION['warning_message']=requestionApprovalFinalApproverName($pdo).' must be the last approver.';
			echo "<script>window.open('?Requisition_Approval_Path_Send/$draft_requistion_id','_self')</script>";
			exit;
		}
		if(count($instantEmployees)!==count(array_unique($instantEmployees))){
			$_SESSION['warning_message']='Please select each approval employee only once.';
			echo "<script>window.open('?Requisition_Approval_Path_Send/$draft_requistion_id','_self')</script>";
			exit;
		}
		$instantPathName=!empty($_POST["instant_approval_path_name"]) ? trim($_POST["instant_approval_path_name"]) : '';
		if($instantPathName===''){
			$instantPathName='Instant Approval Path - '.$draft_invoice_id.' - '.date('YmdHis');
		}
		$insertInstantPath=$pdo->prepare("INSERT INTO project_approval_path_name(approval_path_name,project_id,created_by,created_at) VALUES (:approval_path_name,:project_id,:created_by,:created_at)");
		$insertInstantPath->execute(array(':approval_path_name'=>$instantPathName,':project_id'=>$project_id,':created_by'=>$created_by,':created_at'=>$current_time));
		$approval_path_name_id=$pdo->lastInsertId();
		$insertInstantStep=$pdo->prepare("INSERT INTO project_approval_path_inforamtion(approval_path_name,project_id,employee_id,created_by,created_at) VALUES (:approval_path_name,:project_id,:employee_id,:created_by,:created_at)");
		foreach($instantEmployees as $instantEmployeeId){
			$insertInstantStep->execute(array(':approval_path_name'=>$approval_path_name_id,':project_id'=>$project_id,':employee_id'=>$instantEmployeeId,':created_by'=>$created_by,':created_at'=>$current_time));
		}
	}
	if($approval_path_name_id<=0){
		$_SESSION['warning_message']='Please select or create an approval path.';
		echo "<script>window.open('?Requisition_Approval_Path_Send/$draft_requistion_id','_self')</script>";
		exit;
	}
	$approvalPathGuard=$pdo->prepare("SELECT id FROM project_approval_path_name WHERE id=:approval_path_name_id AND project_id=:project_id AND deleted_at IS NULL LIMIT 1");
	$approvalPathGuard->execute(array(':approval_path_name_id'=>$approval_path_name_id,':project_id'=>$project_id));
	if(!$approvalPathGuard->fetch()){
		$_SESSION['warning_message']='Selected approval path is not valid for this project.';
		echo "<script>window.open('?Requisition_Approval_Path_Send/$draft_requistion_id','_self')</script>";
		exit;
	}
	if(!requestionApprovalPathHasFinalEmployee($pdo,$approval_path_name_id,$project_id)){
		$_SESSION['warning_message']='Approval path must end with '.requestionApprovalFinalApproverName($pdo).' before sending.';
		echo "<script>window.open('?Requisition_Approval_Path_Send/$draft_requistion_id','_self')</script>";
		exit;
	}
	$firstApprovalPathStep=$pdo->prepare("SELECT employee_id FROM project_approval_path_inforamtion WHERE approval_path_name=:approval_path_name_id AND project_id=:project_id AND deleted_at IS NULL ORDER BY id ASC LIMIT 1");
	$firstApprovalPathStep->execute(array(':approval_path_name_id'=>$approval_path_name_id,':project_id'=>$project_id));
	$firstApprovalPathRow=$firstApprovalPathStep->fetch();
	$approval_employee_id=!empty($firstApprovalPathRow['employee_id']) ? (int)$firstApprovalPathRow['employee_id'] : 0;
	if($approval_employee_id<=0){
		$_SESSION['warning_message']='Selected approval path has no approver.';
		echo "<script>window.open('?Requisition_Approval_Path_Send/$draft_requistion_id','_self')</script>";
		exit;
	}


	
	$invoice_information = $pdo->query("select * from requestion_histiory order by id DESC limit 0,1");
    $invoicerowdata = $invoice_information->fetch();
	if(empty($invoicerowdata["invoice_id"])){ 
	$invoice_id="10001";
	}else{ 
	$invoice_id=$invoicerowdata["invoice_id"]+1;
	}
	
	$project_serial_no_information = $pdo->query("select * from requestion_histiory where project_id='$project_id' order by id DESC limit 0,1");
    $rowdataproject_serial_no = $project_serial_no_information->fetch();
	if(empty($rowdataproject_serial_no["project_serial_no"])){ 
	$project_serial_no="100001";
	}else{ 
	$project_serial_no=$rowdataproject_serial_no["project_serial_no"]+1;
	}
	
	
	
   $sql="INSERT INTO `requestion_histiory`(project_serial_no,approval_path_name_id,`invoice_id`, `date`, `employee_id`, `project_id`, `store_id`, `note`, `requistion_type`, `previous_cash_in_hand`, `created_by`, `created_at`,multiple_photo) VALUES ('$project_serial_no',:approval_path_name_id,:invoice_id,:date,:employee_id,:project_id,:store_id,:note,:requistion_type,:previous_cash_in_hand,:created_by,:created_at,:multiple_photo)";

     $insert_data= $pdo->prepare($sql);
     $insert_data->bindparam(":approval_path_name_id", $approval_path_name_id);
     $insert_data->bindparam(":invoice_id", $invoice_id);
	 $insert_data->bindparam(":date", $rowdataRequistion_info["date"]);
	 $insert_data->bindparam(":employee_id", $rowdataRequistion_info["employee_id"]);
	 $insert_data->bindparam(":project_id", $rowdataRequistion_info["project_id"]);
	 $insert_data->bindparam(":store_id", $rowdataRequistion_info["store_id"]);
	 $insert_data->bindparam(":note", $rowdataRequistion_info["note"]);
	 $insert_data->bindparam(":requistion_type", $rowdataRequistion_info["requistion_type"]);
	$insert_data->bindparam(":previous_cash_in_hand", $rowdataRequistion_info["previous_cash_in_hand"]);
	$insert_data->bindparam(":created_by", $created_by);
	 $insert_data->bindparam(":created_at", $created_at);
	 $insert_data->bindparam(":multiple_photo", $rowdataRequistion_info["multiple_photo"]);
	 $insert_data->execute();
	$pdo->query("UPDATE `requestion_draft_histiory` SET final_submit_status='Complete',approval_path_name_id='$approval_path_name_id',updated_by='$created_by',updated_at='$created_at' where id='$draft_requistion_id'");
	requestionApprovalCreatePathSnapshot($pdo,$invoice_id,$project_id,$approval_path_name_id,$created_by,$current_time);
	
	
	$Requistion_Detail_info = $pdo->query("SELECT * FROM `requestion_draft_detail` where invoice_id='$draft_invoice_id' and project_id='$project_id' and deleted_at is NULL");
    while($rowdataDetail_info = $Requistion_Detail_info->fetch()){
		
	$sqlDetail="INSERT INTO `requestion_detail`(`invoice_id`, `date`, `employee_id`, `project_id`, `store_id`, `product_id`, `emergency_detail_ids`, `emergency_quantity`, `detail`, `comment`, `requestion_quantity`, `requistion_rate`, `final_rate`, `final_quantity`, `due_quantity`, `requestion_amount`, `due_amount`, `final_amount`, `created_by`, `created_at`) VALUES (:invoice_id,:date,:employee_id,:project_id,:store_id,:product_id,:emergency_detail_ids,:emergency_quantity,:detail,:comment,:requestion_quantity,:requistion_rate,:final_rate,:final_quantity,:due_quantity,:requestion_amount,:due_amount,:final_amount,:created_by,:created_at)";

     $insert_data_detail= $pdo->prepare($sqlDetail);
     $insert_data_detail->bindparam(":invoice_id", $invoice_id);
	 $insert_data_detail->bindparam(":date", $rowdataDetail_info["date"]);
	 $insert_data_detail->bindparam(":employee_id", $rowdataDetail_info["employee_id"]);
	 $insert_data_detail->bindparam(":project_id", $rowdataDetail_info["project_id"]);
	 $insert_data_detail->bindparam(":store_id", $rowdataDetail_info["store_id"]);
	 $insert_data_detail->bindparam(":product_id", $rowdataDetail_info["product_id"]);
	 $insert_data_detail->bindparam(":emergency_detail_ids", $rowdataDetail_info["emergency_detail_ids"]);
	 $insert_data_detail->bindparam(":emergency_quantity", $rowdataDetail_info["emergency_quantity"]);
	 $insert_data_detail->bindparam(":detail", $rowdataDetail_info["detail"]);
	$insert_data_detail->bindparam(":comment", $rowdataDetail_info["comment"]);
	$insert_data_detail->bindparam(":requestion_quantity", $rowdataDetail_info["requestion_quantity"]);
	$insert_data_detail->bindparam(":requistion_rate", $rowdataDetail_info["requistion_rate"]);
	$insert_data_detail->bindparam(":final_rate", $rowdataDetail_info["requistion_rate"]);
	$insert_data_detail->bindparam(":final_quantity", $rowdataDetail_info["requestion_quantity"]);
	$normal_due_quantity=max(0,(float)$rowdataDetail_info["requestion_quantity"]-(float)$rowdataDetail_info["emergency_quantity"]);
	$insert_data_detail->bindparam(":due_quantity", $normal_due_quantity);
	$insert_data_detail->bindparam(":requestion_amount", $rowdataDetail_info["requestion_amount"]);
	$insert_data_detail->bindparam(":due_amount", $rowdataDetail_info["requestion_amount"]);
	$insert_data_detail->bindparam(":final_amount", $rowdataDetail_info["requestion_amount"]);
	$insert_data_detail->bindparam(":created_by", $created_by);
	 $insert_data_detail->bindparam(":created_at", $created_at);
	 $insert_data_detail->execute();

	if(!empty($rowdataDetail_info["emergency_detail_ids"])){
		$emergencyIds=array_filter(array_map('intval',explode(',',$rowdataDetail_info["emergency_detail_ids"])));
		$emergencyDetailSelect=$pdo->prepare("SELECT id,emergency_request_id,issued_quantity,reconciled_quantity FROM emergency_request_detail WHERE id=:id");
		$emergencyReconcileInsert=$pdo->prepare("INSERT IGNORE INTO emergency_request_reconciliation (emergency_request_detail_id,requestion_invoice_id,quantity,created_by,created_at) VALUES (:detail_id,:invoice_id,:quantity,:created_by,NOW())");
		$emergencyDetailUpdate=$pdo->prepare("UPDATE emergency_request_detail SET reconciled_quantity=issued_quantity WHERE id=:id");
		foreach($emergencyIds as $emergencyDetailId){
			$emergencyDetailSelect->execute(array(':id'=>$emergencyDetailId));
			$emergencyDetail=$emergencyDetailSelect->fetch();
			if(!$emergencyDetail){ continue; }
			$reconcileQuantity=max(0,(float)$emergencyDetail['issued_quantity']-(float)$emergencyDetail['reconciled_quantity']);
			if($reconcileQuantity<=0){ continue; }
			$emergencyReconcileInsert->execute(array(':detail_id'=>$emergencyDetailId,':invoice_id'=>$invoice_id,':quantity'=>$reconcileQuantity,':created_by'=>$created_by));
			$emergencyDetailUpdate->execute(array(':id'=>$emergencyDetailId));
			$pdo->prepare("UPDATE emergency_request SET status='finalized',updated_by=:user_id,updated_at=NOW() WHERE id=:request_id AND NOT EXISTS (SELECT 1 FROM emergency_request_detail pending_detail WHERE pending_detail.emergency_request_id=emergency_request.id AND pending_detail.issued_quantity>pending_detail.reconciled_quantity)")->execute(array(':user_id'=>$created_by,':request_id'=>$emergencyDetail['emergency_request_id']));
		}
	}
		
	}
	

	
	$firstApprovalStatement=$pdo->prepare("INSERT INTO `project_material_aproval_status`(approval_path_name_id,invoice_id,`project_id`, `employee_id`, `assign_employee_id`,approval_status, `asign_date`, `asign_time`, `created_by`, `created_at`) VALUES (:approval_path_name_id,:invoice_id,:project_id,:employee_id,:assign_employee_id,'Pending',:asign_date,:asign_time,:created_by,:created_at)");
	$firstApprovalStatement->execute(array(
		':approval_path_name_id'=>$approval_path_name_id,
		':invoice_id'=>$invoice_id,
		':project_id'=>$project_id,
		':employee_id'=>$approval_employee_id,
		':assign_employee_id'=>$created_by,
		':asign_date'=>$current_date,
		':asign_time'=>$current_time,
		':created_by'=>$created_by,
		':created_at'=>$current_time
	));
	requestionApprovalRecordFlow($pdo,$invoice_id,$project_id,'submit',$created_by,$approval_employee_id,'',$created_by,$current_time);
	

	
	
$Staff_information = $pdo->query("SELECT employee_information.*  FROM project_material_aproval_status INNER JOIN employee_information ON project_material_aproval_status.employee_id=employee_information.id where  project_material_aproval_status.invoice_id='$invoice_id' and project_material_aproval_status.project_id='$project_id' and project_material_aproval_status.approval_status='Pending' and project_material_aproval_status.approval_id is NULL");
$rowdataStaff_information = $Staff_information->fetch();
	
$Requestion_information = $pdo->query("SELECT employee_information.name_en AS name_en,hr_designation.name AS designation  FROM employee_information INNER JOIN hr_designation ON employee_information.designation=hr_designation.id where employee_information.id='$created_by' ");
$rowdataRequestion_information= $Requestion_information->fetch();	
	
	
$Project_information = $pdo->query("SELECT *  FROM project_information where id='$project_id'");
$rowdataProject_information = $Project_information->fetch();



// SMS Send Start
$number="88".$rowdataStaff_information["mobile"];
$message="একটি রিকুইজিশন আপনার অনুমোদনের জন্য অপেক্ষা করছে। Link :".$base_url."?Requestion_History_Detail/".$invoice_id;

$message_url=$sms_send_url."?api_key=".$apikey."&type=unicode&contacts=".urlencode($number)."&senderid=".$sender_id."&msg=".urlencode($message);
    $curl = curl_init();
    curl_setopt ($curl, CURLOPT_URL, $message_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
    $result = curl_exec ($curl);
    curl_close ($curl);
    
$SMS_number="8801712193135";
$SMS_message="Fund requisition request for the project of ".$rowdataProject_information["name"]." Link :".$base_url."?Requestion_History_Detail/".$invoice_id;

$SMS_url=$sms_send_url."?api_key=".$apikey."&type=unicode&contacts=".urlencode($SMS_number)."&senderid=".$sender_id."&msg=".urlencode($SMS_message);
    $curl_Send = curl_init();
    curl_setopt ($curl_Send, CURLOPT_URL, $SMS_url);
    curl_setopt($curl_Send, CURLOPT_RETURNTRANSFER, true);
	
    $result_Send = curl_exec ($curl_Send);
    curl_close ($curl_Send);     
// SMS Send End
	
	
$to = $rowdataStaff_information["email"];
$subject = "Material Requisition Request for Project No ".$rowdataProject_information["name"];

$message = "
<html>
<head>
<title>".$organization_name.":: Material Requestion</title>
</head>
<body>
<table width='100%' cellpadding='10' cellspacing='5' bgcolor='#fff' >
  <tr>
     <td style='text-align:left; font-size:14px; font-weight:bold;' colspan='2'>Enquiry Form</td>
  </tr>
  <tr>
     <th>Name</th>
     <td>".$rowdataRequestion_information["name_en"]."</td>
  </tr>
  <tr>
	 <th>Designation</th>
	 <td>".$rowdataRequestion_information["designation"]."</td>
  </tr>
  <tr>
     <th>Subject</th>
	 <td>Material Requisition Request for Project No ".$rowdataProject_information["name"]."</td>
  </tr>
  <tr>
	<th>Message</th>
	<td>
	<a href='".$base_url."?Requestion_History_Detail/".$invoice_id."' style='color:green;display: block;
    width: 115px;
    height: 25px;
    background: #4E9CAF;
    padding: 10px;
    text-align: center;
    border-radius: 5px;
    color: white;
    font-weight: bold;
    line-height: 25px;'>Click here to View</a> <br>Copy and Past Below Link:<br><br>
	Link:  ".$base_url."?Requestion_History_Detail/".$invoice_id."<br><br>
	
Greetings, Please note that the following request No. ".$invoice_id." For Project No .".$rowdataProject_information["name"].".is waiting for your kind action. 
<br><br>
Thanks.
</td>
  </tr>
</table>
</body>
</html>
";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <'.$company_email_address.'>' . "\r\n";

mail($to,$subject,$message,$headers);
	
	
	
$_SESSION['success_message']=$success_message_data;
echo "<script>window.open('?Requestion_History_Detail/$invoice_id','_self')</script>"; 	

	
}














//Requistion History Draft End

?>
