<?php
$nextInvoiceStatement = $pdo->query("SELECT COALESCE(MAX(CAST(invoice_id AS UNSIGNED)), 10000) + 1 AS next_invoice_id FROM purchase_history");
$nextInvoiceId = $nextInvoiceStatement->fetch()['next_invoice_id'];
$suggestedPurchaseOrder = 'PO-' . date('Y') . '-' . str_pad($nextInvoiceId, 5, '0', STR_PAD_LEFT);

$requisitionStatement = $pdo->query(
    "SELECT requestion_histiory.invoice_id, requestion_histiory.date,
            project_information.name AS project_name, store_information.name AS store_name
     FROM requestion_histiory
     LEFT JOIN project_information ON requestion_histiory.project_id = project_information.id
     LEFT JOIN store_information ON requestion_histiory.store_id = store_information.id
     LEFT JOIN purchase_history ON purchase_history.requisition_invoice_id = requestion_histiory.invoice_id
                               AND purchase_history.deleted_at IS NULL
     WHERE requestion_histiory.approval_status = 'Approve'
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
     ORDER BY requestion_histiory.id DESC"
);
$availableRequisitions = $requisitionStatement->fetchAll();

$requisitionProductStatement = $pdo->query(
    "SELECT requestion_histiory.invoice_id, requestion_histiory.store_id,
            product_information.name,
            requestion_detail.requestion_quantity AS req_quantity,
            COALESCE(NULLIF(requestion_detail.final_quantity, ''), requestion_detail.requestion_quantity) AS quantity,
            COALESCE(NULLIF(requestion_detail.final_rate, ''), NULLIF(requestion_detail.requistion_rate, ''), 0) AS rate
     FROM requestion_histiory
     INNER JOIN requestion_detail ON requestion_detail.invoice_id = requestion_histiory.invoice_id
                              AND requestion_detail.deleted_at IS NULL
     INNER JOIN product_information ON requestion_detail.product_id = product_information.id
     LEFT JOIN purchase_history ON purchase_history.requisition_invoice_id = requestion_histiory.invoice_id
                               AND purchase_history.deleted_at IS NULL
     WHERE requestion_histiory.approval_status = 'Approve'
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
       AND CAST(COALESCE(NULLIF(requestion_detail.final_quantity, ''), requestion_detail.requestion_quantity, 0) AS DECIMAL(18,4)) > 0
     ORDER BY requestion_histiory.id DESC, requestion_detail.id ASC"
);
$requisitionProductMap = array();
while ($requisitionProduct = $requisitionProductStatement->fetch()) {
    $requisitionInvoiceId = (string)$requisitionProduct['invoice_id'];
    if (!isset($requisitionProductMap[$requisitionInvoiceId])) {
        $requisitionProductMap[$requisitionInvoiceId] = array(
            'store_id' => $requisitionProduct['store_id'],
            'items' => array(),
        );
    }
    $requisitionProductMap[$requisitionInvoiceId]['items'][] = array(
        'name' => $requisitionProduct['name'],
        'req_quantity' => $requisitionProduct['req_quantity'],
        'quantity' => $requisitionProduct['quantity'],
        'rate' => $requisitionProduct['rate'],
    );
}
?>
<style>
  .purchase-mode-wrap {
    border: 1px solid #d7dee8;
    border-radius: 8px;
    padding: 16px;
    background: #f8fafc;
  }
  .purchase-mode-label {
    color: #1f2937;
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 10px;
  }
  .purchase-mode-control {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
    max-width: 720px;
  }
  .purchase-mode-option {
    align-items: center;
    background: #fff;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    color: #334155;
    cursor: pointer;
    display: flex;
    gap: 12px;
    margin: 0;
    min-height: 66px;
    padding: 11px 14px;
    transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
  }
  .purchase-mode-option:hover {
    border-color: #3b82f6;
  }
  .purchase-mode-option.is-active {
    background: #eff6ff;
    border-color: #2563eb;
    box-shadow: 0 0 0 2px rgba(37, 99, 235, .12);
    color: #1d4ed8;
  }
  .purchase-mode-option input {
    position: absolute;
    opacity: 0;
    pointer-events: none;
  }
  .purchase-mode-icon {
    align-items: center;
    background: #e2e8f0;
    border-radius: 7px;
    display: flex;
    flex: 0 0 38px;
    height: 38px;
    justify-content: center;
  }
  .purchase-mode-option.is-active .purchase-mode-icon {
    background: #2563eb;
    color: #fff;
  }
  .purchase-mode-title,
  .purchase-mode-note {
    display: block;
    letter-spacing: 0;
  }
  .purchase-mode-title {
    font-size: 14px;
    font-weight: 700;
  }
  .purchase-mode-note {
    color: #64748b;
    font-size: 12px;
    font-weight: 400;
    line-height: 1.45;
    margin-top: 2px;
  }
  .purchase-guidance {
    align-items: flex-start;
    background: #ecfeff;
    border-left: 4px solid #0891b2;
    color: #164e63;
    display: flex;
    gap: 10px;
    margin: 12px 0 18px;
    padding: 11px 14px;
  }
  .purchase-guidance strong {
    display: block;
    font-size: 13px;
    margin-bottom: 2px;
  }
  .purchase-guidance span {
    font-size: 13px;
    line-height: 1.55;
  }
  .supplier-create-result {
    align-items: center;
    color: #1d4ed8;
    display: flex;
    gap: 8px;
    padding: 5px 2px;
  }
  .supplier-modal-note {
    background: #eff6ff;
    border-left: 4px solid #2563eb;
    color: #1e3a8a;
    font-size: 13px;
    margin-bottom: 16px;
    padding: 10px 12px;
  }
  #supplier-modal-feedback {
    display: none;
    font-size: 13px;
    margin-bottom: 12px;
    padding: 9px 11px;
  }
  @media screen and (max-width: 575.98px) {
    .purchase-mode-control { grid-template-columns: 1fr; }
  }
</style>
<section class="content">
  <div class="container-fluid">
    <form method="post" action="?<?php echo substr($page_title, 0, -7); ?>/<?php echo $MenuName; ?>/MEDICINE_PURCHASE_HISTORY" enctype="multipart/form-data" id="purchase-form">
      <div class="card card-default">
        <div class="card-header">
          <h3 class="card-title"><button type="button" class="btn btn-warning" onclick="history.back()"><i class="fa fa-reply"></i> Back</button></h3>
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
            <button type="button" class="btn btn-tool" data-card-widget="remove"><i class="fas fa-times"></i></button>
          </div>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-12"><p class="text-center text-danger">All fields marked with an asterisk (*) are required.</p></div>
            <div class="col-sm-12">
              <div class="purchase-mode-wrap">
                <div class="purchase-mode-label">Select Purchase Type<span class="text-danger"> *</span></div>
                <div class="purchase-mode-control" role="radiogroup" aria-label="Purchase type">
                  <label class="purchase-mode-option is-active">
                    <input type="radio" name="purchase_type" value="without_requisition" checked>
                    <span class="purchase-mode-icon"><i class="fas fa-shopping-cart"></i></span>
                    <span>
                      <span class="purchase-mode-title">Without Requisition</span>
                      <span class="purchase-mode-note">Enter products and quantities manually</span>
                    </span>
                  </label>
                  <label class="purchase-mode-option">
                    <input type="radio" name="purchase_type" value="with_requisition">
                    <span class="purchase-mode-icon"><i class="fas fa-clipboard-check"></i></span>
                    <span>
                      <span class="purchase-mode-title">With Requisition</span>
                      <span class="purchase-mode-note">Load product names from an approved requisition</span>
                    </span>
                  </label>
                </div>
              </div>
              <div class="purchase-guidance" aria-live="polite">
                <i class="fas fa-info-circle mt-1"></i>
                <div>
                  <strong id="purchase-guidance-title">How to Purchase Without Requisition</strong>
                  <span id="purchase-guidance-text">সরবরাহকারী (Supplier), তারিখ, স্টোর ও সংযুক্তি (Attachment) নির্বাচন করুন। এরপর পণ্যের নাম, পরিমাণ, একক মূল্য (Rate) ও পরিশোধিত টাকা লিখে সংরক্ষণ (Save) করুন। PO নম্বর স্বয়ংক্রিয়ভাবে তৈরি হবে।</span>
                </div>
              </div>
            </div>
            <div class="col-sm-4" id="requisition-field" style="display:none">
              <div class="form-group">
                <label for="requisition_invoice_id">Requisition No<span class="text-danger">*</span></label>
                <select class="form-control select2" name="requisition_invoice_id" id="requisition_invoice_id" style="width:100%" onchange="loadPurchaseRequisition(this.value)">
                  <option value="">Select an approved requisition</option>
                  <?php foreach ($availableRequisitions as $requisition) { ?>
                    <option value="<?php echo htmlspecialchars($requisition['invoice_id']); ?>">
                      <?php echo htmlspecialchars($requisition['invoice_id'] . ' - ' . $requisition['project_name'] . ' - ' . $requisition['store_name'] . ' (' . date('d-m-Y', strtotime($requisition['date'])) . ')'); ?>
                    </option>
                  <?php } ?>
                </select>
                <small id="requisition-status" class="form-text"></small>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="purchase_id">Purchase Order No</label>
                <input class="form-control" name="purchase_id" id="purchase_id" value="<?php echo htmlspecialchars($suggestedPurchaseOrder); ?>" readonly>
                <small class="form-text text-muted">Generated automatically when the purchase is saved.</small>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="supplier_id">Supplier Information<span class="text-danger">*</span></label>
                <select name="supplier_id" id="supplier_id" class="form-control" style="width:100%" required>
                  <option value="">Select Supplier Information</option>
                  <?php
                  $suppliers = $pdo->query("SELECT * FROM supplier_information WHERE deleted_at IS NULL");
                  while ($supplier = $suppliers->fetch()) {
                  ?>
                    <option value="<?php echo $supplier['id']; ?>"><?php echo htmlspecialchars($supplier['organization']); ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="date">Date<span class="text-danger">*</span></label>
                <input class="form-control" name="date" id="date" type="date" value="<?php echo date('Y-m-d'); ?>" required>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="note">Note</label>
                <textarea class="form-control" placeholder="Note Here" name="note" id="note"></textarea>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="img">Attachment (Upload)<span class="text-danger">*</span></label>
                <input class="form-control" name="photo" id="img" type="file" accept=".jpeg,.jpg,.png,.bmp,.gif,.pdf" required>
              </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label for="store_id">Store Name<span class="text-danger">*</span></label>
                <?php if ($_SESSION['USER_TYPE'] == 'Admin') { ?>
                  <select class="select2" name="store_id" id="store_id" data-placeholder="Select Store" style="width:100%" required>
                    <option value="">Select Store</option>
                    <?php
                    $stores = $pdo->query("SELECT * FROM store_information WHERE deleted_at IS NULL");
                    while ($store = $stores->fetch()) {
                    ?>
                      <option value="<?php echo $store['id']; ?>"><?php echo htmlspecialchars($store['name']); ?></option>
                    <?php } ?>
                  </select>
                <?php } else {
                  $storeStatement = $pdo->prepare("SELECT name FROM store_information WHERE id = :id AND deleted_at IS NULL");
                  $storeStatement->execute([':id' => $row_Login_Datauser_information['store_id']]);
                  $store = $storeStatement->fetch();
                ?>
                  <p><?php echo htmlspecialchars($store['name']); ?></p>
                  <input name="store_id" id="store_id" type="hidden" value="<?php echo $row_Login_Datauser_information['store_id']; ?>">
                <?php } ?>
              </div>
            </div>
          </div>

          <p>Purchase History:<span class="text-danger">*</span></p>
          <div style="overflow-x:auto">
            <table class="table order-list">
              <thead><tr><th style="width:35%">Name</th><th class="req-quantity-column" style="display:none">Req Quantity</th><th>Quantity</th><th>Rate</th><th>Amount</th><th></th></tr></thead>
              <tbody id="purchase-items"></tbody>
              <tfoot>
                <tr id="add-row-container"><td colspan="6"><button type="button" class="btn btn-primary btn-block" id="addrow">Add More</button></td></tr>
                <tr><td colspan="4" class="text-right purchase-total-label">Total Amount</td><td><input class="form-control" name="amount" id="totalPrice" readonly required></td><td></td></tr>
                <tr><td colspan="4" class="text-right purchase-total-label">Paid Amount <span class="text-danger">*</span></td><td><input class="form-control" name="payment_amount" id="payment_amount" type="number" min="0" step="0.01" value="0" required></td><td></td></tr>
                <tr><td colspan="4" class="text-right purchase-total-label">Due Amount</td><td><input class="form-control" name="due_amount" id="due_amount" readonly required></td><td></td></tr>
              </tfoot>
            </table>
          </div>
          <input type="hidden" name="number_count" id="number_count" value="1">
        </div>
        <div class="card-footer">
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-warning" onclick="history.back()"><i class="fa fa-window-close"></i> Cancel</button>
            <button class="btn btn-primary" type="submit" name="Insert_Purchase_History"><i class="fa fa-save"></i> Save</button>
          </div>
        </div>
      </div>
    </form>

    <div class="modal fade" id="quick-supplier-modal" tabindex="-1" role="dialog" aria-labelledby="quick-supplier-title" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
          <form id="quick-supplier-form" novalidate>
            <div class="modal-header">
              <h5 class="modal-title" id="quick-supplier-title"><i class="fas fa-building mr-2"></i>Create New Supplier</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
              <div class="supplier-modal-note"><i class="fas fa-info-circle mr-1"></i> Complete the required information. The new supplier will be selected automatically.</div>
              <div id="supplier-modal-feedback" role="alert"></div>
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="quick_supplier_organization">Organization <span class="text-danger">*</span></label>
                    <input class="form-control" id="quick_supplier_organization" name="organization" maxlength="506" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="quick_supplier_name">Contact Person <span class="text-danger">*</span></label>
                    <input class="form-control" id="quick_supplier_name" name="name" maxlength="506" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="quick_supplier_mobile">Mobile Number <span class="text-danger">*</span></label>
                    <input class="form-control" id="quick_supplier_mobile" name="mobile" inputmode="numeric" pattern="[0-9]{11}" maxlength="11" placeholder="01XXXXXXXXX" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label for="quick_supplier_email">Email Address</label>
                    <input class="form-control" id="quick_supplier_email" name="email" type="email" maxlength="506">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group mb-0">
                    <label for="quick_supplier_address">Address</label>
                    <textarea class="form-control" id="quick_supplier_address" name="address" maxlength="506" rows="2"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary" id="quick-supplier-save"><i class="fas fa-save mr-1"></i> Create Supplier</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section><link rel="stylesheet" href="plugins/jquery-ui/jquery-ui.min.css">
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<script>
(function ($) {
  var rowCount = 0;
  var requisitionProducts = <?php echo json_encode($requisitionProductMap, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

  function escapeHtml(value) {
    return $('<div>').text(value == null ? '' : value).html();
  }

  function addRow(item, locked) {
    rowCount += 1;
    var name = item && item.name ? item.name : '';
    var quantity = item && item.quantity ? item.quantity : '';
    var rate = item && item.rate ? item.rate : '0';
    var approvedQuantity = locked && item && item.quantity ? Number(item.quantity) : 0;
    var reqQuantity = locked && item && item.req_quantity ? item.req_quantity : '';
    var nameReadonly = locked ? ' readonly' : '';
    var quantityMaximum = approvedQuantity > 0 ? ' max="' + approvedQuantity + '"' : '';
    var reqQuantityCell = '<td class="req-quantity-column"' + (locked ? '' : ' style="display:none"') + '><input class="form-control" value="' + escapeHtml(reqQuantity) + '" readonly></td>';
    var row = '<tr>' +
      '<td><input type="hidden" name="requisition_item' + rowCount + '" value="' + (locked ? '1' : '0') + '"><input name="name' + rowCount + '" id="username_' + rowCount + '" class="username form-control" data-quick-create-product="1" value="' + escapeHtml(name) + '" placeholder="Name Here" required' + nameReadonly + '></td>' +
      reqQuantityCell +
      '<td><input type="number" min="0" step="0.0001" name="quantity' + rowCount + '" class="quantity form-control" value="' + escapeHtml(quantity) + '" placeholder="Purchase Quantity"' + quantityMaximum + '></td>' +
      '<td><input type="number" min="0" step="0.01" name="rate' + rowCount + '" class="rate form-control" value="' + escapeHtml(rate) + '" placeholder="Rate"></td>' +
      '<td><input name="total_amount' + rowCount + '" class="line-total form-control" readonly required></td>' +
      '<td><button type="button" class="delete-row btn btn-danger" title="Delete product"><i class="fas fa-trash"></i></button></td>' +
      '</tr>';
    $('#purchase-items').append(row);
    $('#number_count').val(rowCount);
    calculateTotals();
  }

  function resetRows(items, locked) {
    $('#purchase-items').empty();
    rowCount = 0;
    $('.req-quantity-column').toggle(!!locked);
    $('.purchase-total-label').attr('colspan', locked ? 4 : 3);
    $('#add-row-container td').attr('colspan', locked ? 6 : 5);
    (items && items.length ? items : [{}]).forEach(function (item) { addRow(item, locked); });
    $('#add-row-container').show();
  }

  function calculateTotals() {
    var total = 0;
    $('#purchase-items tr').each(function () {
      var lineTotal = Number($(this).find('.quantity').val()) * Number($(this).find('.rate').val());
      $(this).find('.line-total').val(lineTotal.toFixed(2));
      total += lineTotal;
    });
    $('#totalPrice').val(total.toFixed(2));
    $('#due_amount').val((total - Number($('#payment_amount').val())).toFixed(2));
  }

  function loadRequisition(invoiceId) {
    if (!invoiceId) return;
    var requisition = requisitionProducts[String(invoiceId)];
    $('#requisition-status').removeClass('text-danger text-success');
    if (!requisition || !requisition.items || !requisition.items.length) {
      $('#requisition-status').addClass('text-danger').text('No approved products were found for this requisition.');
      $('#purchase-items').empty();
      rowCount = 0;
      $('#number_count').val(0);
      $('#add-row-container').hide();
      calculateTotals();
      return;
    }
    resetRows(requisition.items, true);
    $('#store_id').val(requisition.store_id).trigger('change.select2');
    $('#requisition-status').addClass('text-success').text(requisition.items.length + ' approved product(s) added successfully.');
  }

  window.loadPurchaseRequisition = loadRequisition;

  $(document).on('focus', '.username:not([readonly])', function () {
    if (!$(this).data('ui-autocomplete')) {
      $(this).autocomplete({
        source: function (request, response) {
          $.post('ajax_Product_name.php', {search: request.term, request: 1}, response, 'json');
        }
      });
    }
  });

  $(document).on('input', '.quantity, .rate, #payment_amount', calculateTotals);
  $('#addrow').on('click', function () { addRow({}, false); });
  $(document).on('click', '.delete-row', function () {
    $(this).closest('tr').remove();
    calculateTotals();
  });

  $('input[name="purchase_type"]').on('change', function () {
    var withRequisition = this.value === 'with_requisition';
    $('.purchase-mode-option').removeClass('is-active');
    $(this).closest('.purchase-mode-option').addClass('is-active');
    $('#requisition-field').toggle(withRequisition);
    $('#requisition_invoice_id').prop('required', withRequisition).val('').trigger('change.select2');
    $('#requisition-status').empty();
    if (withRequisition) {
      $('#purchase-guidance-title').text('How to Purchase With Requisition');
      $('#purchase-guidance-text').text('অনুমোদিত রিকুইজিশন নির্বাচন করলে শুধু পণ্যের নাম ও স্টোর স্বয়ংক্রিয়ভাবে যুক্ত হবে। ক্রয়কারী প্রয়োজন অনুযায়ী ক্রয়ের পরিমাণ ও একক মূল্য (Rate) লিখবেন। এরপর সরবরাহকারী, সংযুক্তি ও পরিশোধিত টাকা পূরণ করে সংরক্ষণ (Save) করুন।');
    } else {
      $('#purchase-guidance-title').text('How to Purchase Without Requisition');
      $('#purchase-guidance-text').text('সরবরাহকারী (Supplier), তারিখ, স্টোর ও সংযুক্তি (Attachment) নির্বাচন করুন। এরপর পণ্যের নাম, পরিমাণ, একক মূল্য (Rate) ও পরিশোধিত টাকা লিখে সংরক্ষণ (Save) করুন। PO নম্বর স্বয়ংক্রিয়ভাবে তৈরি হবে।');
    }
    resetRows(null, false);
  });

  $('#requisition_invoice_id').on('change', function () {
    loadRequisition($(this).val());
  });

  $('#img').on('change', function () {
    var file = this.files[0];
    if (file && file.size > 5002400) {
      alert('The maximum attachment size is 5 MB.');
      this.value = '';
    }
  });

  resetRows(null, false);
})(jQuery);
</script>
<script>
window.addEventListener('load', function () {
  var $ = window.jQuery;
  var $supplierSelect = $('#supplier_id');
  if (!$supplierSelect.length || !$.fn.select2) return;

  $supplierSelect.select2({
    width: '100%',
    tags: true,
    createTag: function (params) {
      var term = $.trim(params.term);
      if (!term) return null;
      return {
        id: '__create_supplier__',
        text: term,
        supplierName: term,
        newSupplier: true
      };
    },
    templateResult: function (supplier) {
      if (!supplier.newSupplier) return supplier.text;
      return $('<span class="supplier-create-result"><i class="fas fa-plus-circle"></i><span>Create new supplier: <strong></strong></span></span>')
        .find('strong').text(supplier.supplierName).end();
    }
  }).on('select2:select', function (event) {
    var supplier = event.params.data;
    if (!supplier.newSupplier) return;
    $(this).find('option[value="__create_supplier__"]').remove().val('').trigger('change');
    $('#quick-supplier-form')[0].reset();
    $('#quick_supplier_organization, #quick_supplier_name').val(supplier.supplierName);
    $('#supplier-modal-feedback').hide().removeClass('alert-danger alert-success').text('');
    $('#quick-supplier-modal').modal('show');
    setTimeout(function () { $('#quick_supplier_mobile').trigger('focus'); }, 250);
  });

  $('#quick-supplier-form').on('submit', function (event) {
    event.preventDefault();
    if (!this.checkValidity()) {
      this.reportValidity();
      return;
    }
    var $saveButton = $('#quick-supplier-save');
    var $feedback = $('#supplier-modal-feedback');
    $saveButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Creating...');
    $feedback.hide().removeClass('alert-danger alert-success');
    $.ajax({
      url: 'ajax_Create_Supplier.php',
      type: 'POST',
      dataType: 'json',
      data: $(this).serialize()
    }).done(function (response) {
      var supplier = response.supplier;
      var option = new Option(supplier.organization, supplier.id, true, true);
      $supplierSelect.append(option).trigger('change');
      $('#quick-supplier-modal').modal('hide');
    }).fail(function (xhr) {
      var message = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Unable to create the supplier. Please try again.';
      $feedback.addClass('alert alert-danger').text(message).show();
    }).always(function () {
      $saveButton.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Create Supplier');
    });
  });
});
</script>
