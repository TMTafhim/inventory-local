<?php
if((string)$LoginReGiSterSession!=='121'){
    http_response_code(403);
    ?>
    <section class="content">
      <div class="container-fluid">
        <div class="card border-0 shadow-sm">
          <div class="card-body text-center py-5">
            <div class="mb-3 text-danger"><i class="fas fa-lock fa-2x"></i></div>
            <h4 class="mb-2">Access restricted</h4>
            <p class="text-muted mb-0">This workspace is available only for employee ID 121.</p>
          </div>
        </div>
      </div>
    </section>
    <?php
    return;
}

$accessStudioRedirect='?Access_Control_Studio/Administration';
if(isset($_POST['acs_designation_save'])){
    $designationId=!empty($_POST['designation_id']) ? (int)$_POST['designation_id'] : 0;
    $designationName=trim($_POST['designation_name']);
    if($designationName===''){
        $_SESSION['warning_message']='Designation name is required.';
    }elseif($designationId>0){
        $statement=$pdo->prepare("UPDATE hr_designation SET name=:name,updated_by=:updated_by,updated_at=:updated_at WHERE id=:id AND deleted_at IS NULL");
        $statement->execute(array(':name'=>$designationName,':updated_by'=>$LoginReGiSterSession,':updated_at'=>$current_time,':id'=>$designationId));
        $_SESSION['success_message']='Designation updated successfully.';
    }else{
        $statement=$pdo->prepare("INSERT INTO hr_designation(name,created_by,created_at) VALUES (:name,:created_by,:created_at)");
        $statement->execute(array(':name'=>$designationName,':created_by'=>$LoginReGiSterSession,':created_at'=>$current_time));
        $_SESSION['success_message']='Designation created successfully.';
    }
    echo "<script>window.open('$accessStudioRedirect','_self')</script>";
    return;
}

if(isset($_POST['acs_designation_delete'])){
    $designationId=!empty($_POST['designation_id']) ? (int)$_POST['designation_id'] : 0;
    $usedStatement=$pdo->prepare("SELECT COUNT(id) FROM employee_information WHERE designation=:designation_id AND deleted_at IS NULL");
    $usedStatement->execute(array(':designation_id'=>$designationId));
    if($designationId<=0){
        $_SESSION['warning_message']='Invalid designation selected.';
    }elseif((int)$usedStatement->fetchColumn()>0){
        $_SESSION['warning_message']='This designation is assigned to employee(s). Reassign them before deleting.';
    }else{
        $statement=$pdo->prepare("UPDATE hr_designation SET deleted_by=:deleted_by,deleted_at=:deleted_at WHERE id=:id");
        $statement->execute(array(':deleted_by'=>$LoginReGiSterSession,':deleted_at'=>$current_time,':id'=>$designationId));
        $_SESSION['success_message']='Designation deleted successfully.';
    }
    echo "<script>window.open('$accessStudioRedirect','_self')</script>";
    return;
}

if(isset($_POST['acs_employee_access_save'])){
    $employeeId=!empty($_POST['employee_id']) ? (int)$_POST['employee_id'] : 0;
    $selectedRolePermissions=!empty($_POST['role_permission']) && is_array($_POST['role_permission']) ? $_POST['role_permission'] : array();
    $allowedRolePermissions=array('Create','Update','Delete','View','Distribution');
    $rolePermissions=array_values(array_intersect($allowedRolePermissions,$selectedRolePermissions));
    $menuPermissions=!empty($_POST['menu_access']) && is_array($_POST['menu_access']) ? $_POST['menu_access'] : array();
    $menuPermissions=array_values(array_unique(array_map(function($menuName){
        return $menuName==='Purchase History/Input' ? 'Purchase History' : trim($menuName);
    },$menuPermissions)));
    $menuPermissions=array_values(array_filter($menuPermissions,function($menuName){ return $menuName!==''; }));

    $statement=$pdo->prepare("UPDATE employee_information SET designation=:designation,department=:department,store_id=:store_id,user_type=:user_type,user_status=:user_status,role_permission=:role_permission,menu_access=:menu_access,updated_by=:updated_by,updated_at=:updated_at WHERE id=:employee_id AND deleted_at IS NULL");
    $statement->execute(array(
        ':designation'=>!empty($_POST['designation']) ? (int)$_POST['designation'] : null,
        ':department'=>!empty($_POST['department']) ? (int)$_POST['department'] : null,
        ':store_id'=>!empty($_POST['store_id']) ? (int)$_POST['store_id'] : null,
        ':user_type'=>!empty($_POST['user_type']) ? $_POST['user_type'] : 'User',
        ':user_status'=>!empty($_POST['user_status']) ? $_POST['user_status'] : 'Active',
        ':role_permission'=>implode(', ',$rolePermissions),
        ':menu_access'=>json_encode($menuPermissions),
        ':updated_by'=>$LoginReGiSterSession,
        ':updated_at'=>$current_time,
        ':employee_id'=>$employeeId
    ));
    $_SESSION['success_message']='Employee access updated successfully.';
    echo "<script>window.open('$accessStudioRedirect/$employeeId','_self')</script>";
    return;
}

$designations=$pdo->query("SELECT * FROM hr_designation WHERE deleted_at IS NULL ORDER BY name ASC")->fetchAll();
$departments=$pdo->query("SELECT * FROM hr_department WHERE deleted_at IS NULL ORDER BY name ASC")->fetchAll();
$stores=$pdo->query("SELECT * FROM store_information WHERE deleted_at IS NULL ORDER BY name ASC")->fetchAll();
$menus=$pdo->query("SELECT * FROM menu_information WHERE deleted_at IS NULL ORDER BY name ASC")->fetchAll();
$employees=$pdo->query("SELECT employee_information.*,hr_designation.name AS designation_name,hr_department.name AS department_name FROM employee_information LEFT JOIN hr_designation ON employee_information.designation=hr_designation.id LEFT JOIN hr_department ON employee_information.department=hr_department.id WHERE employee_information.deleted_at IS NULL ORDER BY employee_information.name_en ASC")->fetchAll();

$selectedEmployeeId=!empty($DocumentData) ? (int)$DocumentData : 0;
if($selectedEmployeeId<=0 && !empty($employees)){
    $selectedEmployeeId=(int)$employees[0]['id'];
}
$selectedEmployee=null;
foreach($employees as $employee){
    if((int)$employee['id']===$selectedEmployeeId){
        $selectedEmployee=$employee;
        break;
    }
}
$selectedMenuAccess=array();
if(!empty($selectedEmployee['menu_access'])){
    $selectedMenuAccess=json_decode($selectedEmployee['menu_access'],true);
    if(!is_array($selectedMenuAccess)){ $selectedMenuAccess=array(); }
}
$selectedRolePermission=!empty($selectedEmployee['role_permission']) ? $selectedEmployee['role_permission'] : '';
$roleOptions=array('Create','Update','Delete','View','Distribution');
$menuGroups=array(
  'Workflow'=>array('Requisition Draft','Emergency Request','Requestion','My Approved Requisition','Distribution','Distribution Pending','Material Received Status','Distribution List','Distribution History'),
  'Inventory'=>array('Purchase History','Stock','Indivisual Stock','Return History','Project Material Used History','Stock Transfer'),
  'Reports'=>array('Report'),
  'Master Data'=>array('Setting','Menu Information','Store Information','Product Unit','Product Category','Product Information','Asset Product Information','Supplier','Project Information','Project Material Approval Information'),
  'Administration'=>array('User Information','Asset Information','Stock Detail Information'),
  'Human Resources'=>array('HR')
);
if(!function_exists('accessStudioMenuGroupName')){
function accessStudioMenuGroupName($menuName,$menuGroups){
    foreach($menuGroups as $groupName=>$groupMenus){
        if(in_array($menuName,$groupMenus,true)){ return $groupName; }
    }
    return 'Other';
}
}
?>

<style>
.access-studio { color:#111827; }
.access-studio .studio-shell { display:grid; grid-template-columns: 320px minmax(0,1fr); gap:16px; }
.access-studio .studio-card { border:1px solid #e2e8f0; border-radius:12px; background:#fff; box-shadow:0 14px 36px rgba(15,23,42,.06); overflow:hidden; }
.access-studio .studio-card-header { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:16px 18px; border-bottom:1px solid #eef2f7; background:#fbfdff; }
.access-studio .studio-card-title { margin:0; font-size:14px; font-weight:850; color:#111827; }
.access-studio .studio-card-subtitle { margin:3px 0 0; color:#667085; font-size:12px; font-weight:650; }
.access-studio .studio-card-body { padding:18px; }
.access-studio .studio-icon { width:36px; height:36px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; background:#ecfdf5; color:#0f766e; border:1px solid #ccfbf1; }
.access-studio .employee-list { display:flex; flex-direction:column; gap:8px; max-height:620px; overflow:auto; padding:10px; }
.access-studio .employee-item { display:flex; align-items:center; gap:10px; padding:10px; border:1px solid transparent; border-radius:10px; color:#334155; text-decoration:none; }
.access-studio .employee-item:hover { background:#f8fafc; color:#111827; text-decoration:none; }
.access-studio .employee-item.active { border-color:#bfdbfe; background:#eff6ff; color:#1d4ed8; }
.access-studio .employee-avatar { width:38px; height:38px; border-radius:10px; object-fit:cover; background:#e2e8f0; }
.access-studio .employee-name { display:block; font-weight:800; font-size:13px; line-height:1.2; }
.access-studio .employee-meta { display:block; color:#667085; font-size:12px; line-height:1.2; margin-top:3px; }
.access-studio .studio-grid { display:grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap:14px; }
.access-studio .field-label { color:#475467; font-size:12px; font-weight:800; margin-bottom:6px; }
.access-studio .form-control, .access-studio .custom-select { border-color:#d7e0ea; border-radius:8px; min-height:40px; font-size:13px; }
.access-studio .permission-strip { display:flex; flex-wrap:wrap; gap:8px; }
.access-studio .permission-chip { display:inline-flex; align-items:center; gap:7px; padding:8px 10px; border:1px solid #dbe4ef; border-radius:999px; background:#fff; color:#344054; font-size:12px; font-weight:750; cursor:pointer; }
.access-studio .permission-chip input { margin:0; }
.access-studio .menu-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:8px; max-height:360px; overflow:auto; padding:4px; }
.access-studio .menu-check { display:flex; align-items:flex-start; gap:8px; padding:10px; border:1px solid #e2e8f0; border-radius:10px; background:#fff; min-height:58px; cursor:pointer; }
.access-studio .menu-check:hover { background:#f8fafc; border-color:#cbd5e1; }
.access-studio .menu-check input { margin-top:3px; }
.access-studio .menu-name { display:block; color:#111827; font-size:12px; font-weight:800; line-height:1.2; }
.access-studio .menu-group { display:block; margin-top:4px; color:#667085; font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.04em; }
.access-studio .designation-row { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:8px; align-items:center; padding:10px 0; border-bottom:1px solid #eef2f7; }
.access-studio .designation-edit-form { display:grid; grid-template-columns:minmax(0,1fr) auto; gap:8px; align-items:center; }
.access-studio .designation-row:last-child { border-bottom:0; }
.access-studio .muted-count { color:#667085; font-size:12px; font-weight:750; }
.access-studio .studio-actions { display:flex; justify-content:flex-end; gap:10px; padding-top:16px; border-top:1px solid #eef2f7; margin-top:18px; }
@media screen and (max-width:1199.98px){ .access-studio .studio-shell{grid-template-columns:1fr;} .access-studio .menu-grid{grid-template-columns:repeat(2,minmax(0,1fr));} }
@media screen and (max-width:767.98px){ .access-studio .studio-grid,.access-studio .menu-grid{grid-template-columns:1fr;} .access-studio .studio-card-body{padding:14px;} }
</style>

<section class="content access-studio">
  <div class="container-fluid">
    <div class="studio-shell">
      <aside class="studio-card">
        <div class="studio-card-header">
          <div>
            <h3 class="studio-card-title">Employees</h3>
            <p class="studio-card-subtitle"><?php echo count($employees); ?> active profile(s)</p>
          </div>
          <span class="studio-icon"><i class="fas fa-users"></i></span>
        </div>
        <div class="employee-list">
          <?php foreach($employees as $employee){ ?>
            <a class="employee-item <?php if((int)$employee['id']===$selectedEmployeeId){ echo 'active'; } ?>" href="?Access_Control_Studio/Administration/<?php echo (int)$employee['id']; ?>">
              <?php if(!empty($employee['photo'])){ ?>
                <img class="employee-avatar" src="HRPhoto/<?php echo htmlspecialchars($employee['photo']); ?>" alt="">
              <?php }else{ ?>
                <span class="employee-avatar d-inline-flex align-items-center justify-content-center"><i class="fas fa-user"></i></span>
              <?php } ?>
              <span>
                <span class="employee-name"><?php echo htmlspecialchars($employee['name_en']); ?></span>
                <span class="employee-meta">ID <?php echo (int)$employee['id']; ?> · <?php echo htmlspecialchars($employee['designation_name'] ?: 'No designation'); ?></span>
              </span>
            </a>
          <?php } ?>
        </div>
      </aside>

      <main>
        <div class="studio-card mb-3">
          <div class="studio-card-header">
            <div>
              <h3 class="studio-card-title">Employee Access</h3>
              <p class="studio-card-subtitle">Designation, role, store, status, and menu permissions in one place.</p>
            </div>
            <span class="studio-icon"><i class="fas fa-user-shield"></i></span>
          </div>
          <div class="studio-card-body">
            <?php if(!empty($selectedEmployee)){ ?>
            <form method="post" action="?Access_Control_Studio/Administration/<?php echo (int)$selectedEmployee['id']; ?>">
              <input type="hidden" name="employee_id" value="<?php echo (int)$selectedEmployee['id']; ?>">
              <div class="studio-grid">
                <div>
                  <div class="field-label">Employee</div>
                  <input type="text" class="form-control" value="<?php echo htmlspecialchars($selectedEmployee['name_en'].' (ID '.$selectedEmployee['id'].')'); ?>" readonly>
                </div>
                <div>
                  <div class="field-label">User Status</div>
                  <select name="user_status" class="custom-select">
                    <?php foreach(array('Active','Inactive') as $status){ ?>
                      <option value="<?php echo $status; ?>" <?php if($selectedEmployee['user_status']===$status){ echo 'selected'; } ?>><?php echo $status; ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div>
                  <div class="field-label">Designation</div>
                  <select name="designation" class="custom-select" required>
                    <option value="">Select designation</option>
                    <?php foreach($designations as $designation){ ?>
                      <option value="<?php echo (int)$designation['id']; ?>" <?php if((int)$selectedEmployee['designation']===(int)$designation['id']){ echo 'selected'; } ?>><?php echo htmlspecialchars($designation['name']); ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div>
                  <div class="field-label">Department</div>
                  <select name="department" class="custom-select">
                    <option value="">Select department</option>
                    <?php foreach($departments as $department){ ?>
                      <option value="<?php echo (int)$department['id']; ?>" <?php if((int)$selectedEmployee['department']===(int)$department['id']){ echo 'selected'; } ?>><?php echo htmlspecialchars($department['name']); ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div>
                  <div class="field-label">Store</div>
                  <select name="store_id" class="custom-select">
                    <option value="">Select store</option>
                    <?php foreach($stores as $store){ ?>
                      <option value="<?php echo (int)$store['id']; ?>" <?php if((int)$selectedEmployee['store_id']===(int)$store['id']){ echo 'selected'; } ?>><?php echo htmlspecialchars($store['name']); ?></option>
                    <?php } ?>
                  </select>
                </div>
                <div>
                  <div class="field-label">User Type</div>
                  <select name="user_type" class="custom-select">
                    <?php foreach(array('User','Admin') as $userType){ ?>
                      <option value="<?php echo $userType; ?>" <?php if($selectedEmployee['user_type']===$userType){ echo 'selected'; } ?>><?php echo $userType; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="mt-4">
                <div class="field-label">Role Permissions</div>
                <div class="permission-strip">
                  <?php foreach($roleOptions as $roleOption){ ?>
                    <label class="permission-chip">
                      <input type="checkbox" name="role_permission[]" value="<?php echo $roleOption; ?>" <?php if(stripos($selectedRolePermission,$roleOption)!==false){ echo 'checked'; } ?>>
                      <?php echo $roleOption; ?>
                    </label>
                  <?php } ?>
                </div>
              </div>

              <div class="mt-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap mb-2">
                  <div class="field-label mb-0">Menu Access</div>
                  <span class="muted-count"><span id="acs-menu-count">0</span> selected</span>
                </div>
                <div class="menu-grid" id="acs-menu-grid">
                  <?php foreach($menus as $menu){ ?>
                    <label class="menu-check" data-menu-name="<?php echo htmlspecialchars(strtolower($menu['name'])); ?>">
                      <input type="checkbox" name="menu_access[]" value="<?php echo htmlspecialchars($menu['name']); ?>" <?php if(in_array($menu['name'],$selectedMenuAccess,true)){ echo 'checked'; } ?>>
                      <span>
                        <span class="menu-name"><?php echo htmlspecialchars($menu['name']); ?></span>
                        <span class="menu-group"><?php echo htmlspecialchars(accessStudioMenuGroupName($menu['name'],$menuGroups)); ?></span>
                      </span>
                    </label>
                  <?php } ?>
                </div>
              </div>

              <div class="studio-actions">
                <button type="button" class="btn btn-light" id="acs-select-all-menu"><i class="fas fa-check-double"></i> Select all</button>
                <button type="submit" class="btn btn-primary" name="acs_employee_access_save"><i class="fas fa-save"></i> Save access</button>
              </div>
            </form>
            <?php }else{ ?>
              <div class="text-muted">No employee found.</div>
            <?php } ?>
          </div>
        </div>

        <div class="studio-card">
          <div class="studio-card-header">
            <div>
              <h3 class="studio-card-title">Designations</h3>
              <p class="studio-card-subtitle">Create, rename, and remove unused designations.</p>
            </div>
            <span class="studio-icon"><i class="fas fa-id-badge"></i></span>
          </div>
          <div class="studio-card-body">
            <form method="post" action="?Access_Control_Studio/Administration" class="mb-3">
              <div class="input-group">
                <input type="text" name="designation_name" class="form-control" placeholder="New designation name" required>
                <div class="input-group-append">
                  <button class="btn btn-primary" type="submit" name="acs_designation_save"><i class="fas fa-plus"></i> Add</button>
                </div>
              </div>
            </form>
            <?php foreach($designations as $designation){ ?>
              <div class="designation-row">
                <form method="post" action="?Access_Control_Studio/Administration" class="designation-edit-form mb-0">
                  <input type="hidden" name="designation_id" value="<?php echo (int)$designation['id']; ?>">
                  <input type="text" name="designation_name" class="form-control" value="<?php echo htmlspecialchars($designation['name']); ?>" required>
                  <button class="btn btn-sm btn-outline-primary" type="submit" name="acs_designation_save"><i class="fas fa-save"></i></button>
                </form>
                <form method="post" action="?Access_Control_Studio/Administration" class="mb-0" onsubmit="return confirm('Delete this designation?');">
                  <input type="hidden" name="designation_id" value="<?php echo (int)$designation['id']; ?>">
                  <button class="btn btn-sm btn-outline-danger" type="submit" name="acs_designation_delete"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            <?php } ?>
          </div>
        </div>
      </main>
    </div>
  </div>
</section>

<script>
$(function(){
  function updateAccessStudioMenuCount(){
    $('#acs-menu-count').text($('#acs-menu-grid input[type="checkbox"]:checked').length);
  }
  $('#acs-menu-grid input[type="checkbox"]').on('change', updateAccessStudioMenuCount);
  $('#acs-select-all-menu').on('click', function(){
    var allChecked=$('#acs-menu-grid input[type="checkbox"]').length===$('#acs-menu-grid input[type="checkbox"]:checked').length;
    $('#acs-menu-grid input[type="checkbox"]').prop('checked', !allChecked);
    updateAccessStudioMenuCount();
  });
  $('.designation-edit-form input[name="designation_name"]').on('keydown', function(event){
    if(event.key==='Enter'){
      event.preventDefault();
      $(this).closest('form').trigger('submit');
    }
  });
  updateAccessStudioMenuCount();
});
</script>
