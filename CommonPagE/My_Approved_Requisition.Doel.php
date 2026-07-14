<section class="content">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">My Approved Requisition</h3>
      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
          <i class="fas fa-minus"></i>
        </button>
        <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    <div class="card-body p-0">
      <table id="example1" class="table table-bordered table-striped">
        <thead>
          <tr data-product-ids="<?php echo productUsageFilterIds($pdo, 'requisition', $rowdatapurchage["invoice_id"]); ?>">
            <th>SL</th>
            <th>Serial No</th>
            <th>Date</th>
            <th>Project</th>
            <th>Store</th>
            <th>Approval Date</th>
            <th>Status</th>
            <th>Option</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $approved_user_id=$_SESSION['LoginReGiSterSession'];
          $informationpurchage = $pdo->query("SELECT requestion_histiory.invoice_id,requestion_histiory.date,requestion_histiory.approval_status,requestion_histiory.distribution_status,project_information.name AS project_name,store_information.name AS store_name,project_material_aproval_status.approval_date FROM project_material_aproval_status INNER JOIN requestion_histiory ON project_material_aproval_status.invoice_id=requestion_histiory.invoice_id INNER JOIN project_information ON requestion_histiory.project_id=project_information.id INNER JOIN store_information ON requestion_histiory.store_id=store_information.id WHERE project_material_aproval_status.employee_id='".$approved_user_id."' AND project_material_aproval_status.approval_status='Approve' AND project_material_aproval_status.deleted_at IS NULL AND requestion_histiory.deleted_at IS NULL ORDER BY project_material_aproval_status.id DESC");
          $i=1;
          while ($rowdatapurchage = $informationpurchage->fetch()){
          ?>
          <tr data-product-ids="<?php echo productUsageFilterIds($pdo, 'requisition', $rowdatapurchage["invoice_id"]); ?>">
            <td><?php echo $i++; ?></td>
            <td><a href="?Requestion_History_Detail/<?php echo $rowdatapurchage["invoice_id"]; ?>"><?php echo $rowdatapurchage["invoice_id"]; ?></a></td>
            <td><?php echo date("d-m-Y", strtotime($rowdatapurchage["date"])); ?></td>
            <td><?php echo $rowdatapurchage["project_name"]; ?></td>
            <td><?php echo $rowdatapurchage["store_name"]; ?></td>
            <td><?php echo !empty($rowdatapurchage["approval_date"]) ? date("d-m-Y", strtotime($rowdatapurchage["approval_date"])) : "-"; ?></td>
            <td>
              <?php
              if(!empty($rowdatapurchage["approval_status"]) && $rowdatapurchage["approval_status"]=='Approve'){
                echo "Approved";
              }else{
                echo "Pending";
              }
              if(!empty($rowdatapurchage["distribution_status"])){
                echo " / ".$rowdatapurchage["distribution_status"];
              }
              ?>
            </td>
            <td class="text-center">
              <a class="btn btn-primary btn-sm" href="?Requestion_History_Detail/<?php echo $rowdatapurchage["invoice_id"]; ?>">
                <i class="fas fa-eye"></i> View
              </a>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</section>
