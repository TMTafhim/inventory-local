<?php
function authIsSuperAdmin($employee_id = null) {
    if ($employee_id === null && !empty($_SESSION['LoginReGiSterSession'])) {
        $employee_id = $_SESSION['LoginReGiSterSession'];
    }
    return ((string)$employee_id === '121');
}

function authCanEditRequisitionHistory($employee_id = null) {
    return authIsSuperAdmin($employee_id);
}
?>
