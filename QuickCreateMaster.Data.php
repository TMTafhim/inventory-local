<?php
$quickCreateCategories = array();
$quickCreateCategoryStatement = $pdo->query("SELECT id, name FROM product_category WHERE deleted_at IS NULL ORDER BY name ASC");
while ($quickCreateCategory = $quickCreateCategoryStatement->fetch()) {
  $quickCreateCategories[] = array('value' => $quickCreateCategory['id'], 'text' => $quickCreateCategory['name']);
}
$quickCreateUnits = array();
$quickCreateUnitStatement = $pdo->query("SELECT name FROM product_unit WHERE deleted_at IS NULL ORDER BY name ASC");
while ($quickCreateUnit = $quickCreateUnitStatement->fetch()) {
  $quickCreateUnits[] = array('value' => $quickCreateUnit['name'], 'text' => $quickCreateUnit['name']);
}
?>
<style>
  .quick-create-result {
    align-items: center;
    color: #1d4ed8;
    display: flex;
    gap: 8px;
    padding: 5px 2px;
  }
  .quick-create-note {
    background: #eff6ff;
    border-left: 4px solid #2563eb;
    color: #1e3a8a;
    font-size: 13px;
    margin-bottom: 16px;
    padding: 10px 12px;
  }
  #quick-create-feedback { display: none; }
</style>

<div class="modal fade" id="quick-create-master-modal" tabindex="-1" role="dialog" aria-labelledby="quick-create-master-title" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="quick-create-master-form" novalidate>
        <div class="modal-header">
          <h5 class="modal-title" id="quick-create-master-title"><i class="fas fa-plus-circle mr-2"></i>Create New Record</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
          <div class="quick-create-note">Complete the required information. The new record will be selected automatically.</div>
          <div class="alert alert-danger" id="quick-create-feedback" role="alert"></div>
          <input type="hidden" name="entity" id="quick-create-entity">
          <div id="quick-create-fields"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="quick-create-save"><i class="fas fa-save mr-1"></i>Create</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
window.addEventListener('load', function () {
(function ($) {
  var productCategories = <?php echo json_encode($quickCreateCategories, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  var productUnits = <?php echo json_encode($quickCreateUnits, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
  var definitions = {
    product: {label: 'Product', fields: [
      {name: 'product_category', label: 'Category', type: 'select', options: productCategories},
      {name: 'name', label: 'Product Name'},
      {name: 'code_no', label: 'Code No', type: 'hidden', required: false},
      {name: 'code', label: 'Code', required: false},
      {name: 'unit', label: 'Unit', type: 'select', options: productUnits},
      {name: 'description', label: 'Description', required: false}
    ]},
    supplier: {label: 'Supplier', fields: [
      {name: 'organization', label: 'Organization'},
      {name: 'name', label: 'Contact Person'},
      {name: 'mobile', label: 'Mobile Number', type: 'text', pattern: '[0-9]{11}', maxlength: '11'},
      {name: 'email', label: 'Email Address', type: 'email', required: false},
      {name: 'address', label: 'Address', required: false}
    ]},
    product_category: {label: 'Product Category', fields: [{name: 'name', label: 'Category Name'}, {name: 'code', label: 'Category Code'}]},
    product_unit: {label: 'Product Unit', fields: [{name: 'name', label: 'Unit Name'}]},
    project: {label: 'Project', fields: [{name: 'name', label: 'Project Name'}, {name: 'location', label: 'Location'}]},
    department: {label: 'Department', fields: [{name: 'name', label: 'Department Name'}]},
    designation: {label: 'Designation', fields: [{name: 'name', label: 'Designation Name'}]},
    leave_type: {label: 'Leave Type', fields: [{name: 'name', label: 'Leave Type'}, {name: 'number_of_days', label: 'Number of Days', type: 'number', min: '1'}]}
  };
  var activeSelect = null;
  var activeProductInput = null;

  function escapeHtml(value) {
    return $('<div>').text(value == null ? '' : value).html();
  }

  function initializeQuickCreateSelect($select) {
    var entity = $select.data('quick-create');
    var definition = definitions[entity];
    if (!definition || $select.data('quick-create-ready')) return;
    if ($select.hasClass('select2-hidden-accessible')) $select.select2('destroy');
    $select.data('quick-create-ready', true).select2({
      width: '100%',
      tags: true,
      createTag: function (params) {
        var term = $.trim(params.term);
        if (!term) return null;
        return {id: '__quick_create__', text: term, newRecord: true, recordName: term};
      },
      templateResult: function (item) {
        if (!item.newRecord) return item.text;
        return $('<span class="quick-create-result"><i class="fas fa-plus-circle"></i><span>Create new ' + definition.label + ': <strong></strong></span></span>')
          .find('strong').text(item.recordName).end();
      }
    }).on('select2:select.quickCreate', function (event) {
      var item = event.params.data;
      if (!item.newRecord) return;
      activeSelect = $select;
      $select.find('option[value="__quick_create__"]').remove().val('').trigger('change');
      openQuickCreateModal(entity, item.recordName);
    });
  }

  function openQuickCreateModal(entity, initialName) {
    var definition = definitions[entity];
    var fields = '';
    definition.fields.forEach(function (field, index) {
      var isRequired = field.required !== false;
      fields += '<div class="form-group"><label for="quick_create_' + field.name + '">' + field.label + (isRequired ? ' <span class="text-danger">*</span>' : '') + '</label>';
      if (field.type === 'select') {
        fields += '<select class="form-control" id="quick_create_' + field.name + '" name="' + field.name + '"' + (isRequired ? ' required' : '') + '><option value="">Select ' + field.label + '</option>';
        (field.options || []).forEach(function (option) {
          fields += '<option value="' + escapeHtml(option.value) + '">' + escapeHtml(option.text) + '</option>';
        });
        fields += '</select>';
      } else if (field.type === 'hidden') {
        fields += '<input id="quick_create_' + field.name + '" name="' + field.name + '" type="hidden">';
      } else {
        fields += '<input class="form-control" id="quick_create_' + field.name + '" name="' + field.name + '" type="' + (field.type || 'text') + '"' +
          (field.min ? ' min="' + field.min + '"' : '') + (field.pattern ? ' pattern="' + field.pattern + '"' : '') +
          (field.maxlength ? ' maxlength="' + field.maxlength + '"' : '') + (isRequired ? ' required' : '') + '>';
      }
      fields += '</div>';
    });
    $('#quick-create-master-title').html('<i class="fas fa-plus-circle mr-2"></i>Create New ' + definition.label);
    $('#quick-create-entity').val(entity);
    $('#quick-create-fields').html(fields);
    $('#quick-create-feedback').hide().text('');
    $('#quick_create_name').val(initialName);
    if (entity === 'supplier') {
      $('#quick_create_organization, #quick_create_name').val(initialName);
    }
    if (entity === 'product') {
      bindQuickProductCode();
    }
    $('#quick-create-master-modal').modal('show');
    setTimeout(function () {
      var nextField = entity === 'supplier' ? 'mobile' : (definition.fields.length > 1 ? definition.fields[1].name : definition.fields[0].name);
      $('#quick_create_' + nextField).trigger('focus');
    }, 250);
  }

  function bindQuickProductCode() {
    var $category = $('#quick_create_product_category');
    var $code = $('#quick_create_code');
    var $codeNo = $('#quick_create_code_no');
    function refreshCode() {
      var categoryId = $category.val();
      $code.val('');
      $codeNo.val('');
      if (!categoryId) return;
      $.ajax({
        url: 'ajax_Product_Category.php',
        type: 'POST',
        dataType: 'json',
        data: {product_category: categoryId, response_type: 'json'}
      }).done(function (response) {
        if (response && response.code) {
          $code.val(response.code);
          $codeNo.val(response.code_no || '');
        }
      });
    }
    $category.off('change.quickProductCode').on('change.quickProductCode', refreshCode);
    refreshCode();
  }

  $('[data-quick-create]').each(function () { initializeQuickCreateSelect($(this)); });

  $(document).on('autocompleteresponse', '[data-quick-create-product]', function (event, ui) {
    var term = $.trim($(this).val());
    ui.content = ui.content || [];
    if (term && !ui.content.length) {
      ui.content.push({label: 'Create new product: ' + term, value: term, quickCreateProduct: true});
    }
  });
  $(document).on('autocompleteselect', '[data-quick-create-product]', function (event, ui) {
    if (!ui.item.quickCreateProduct) return;
    event.preventDefault();
    activeSelect = null;
    activeProductInput = $(this);
    openQuickCreateModal('product', ui.item.value);
  });

  $('#quick-create-master-form').on('submit', function (event) {
    event.preventDefault();
    if (!this.checkValidity()) { this.reportValidity(); return; }
    var $button = $('#quick-create-save');
    var $feedback = $('#quick-create-feedback');
    $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Creating...');
    $.ajax({url: 'ajax_Quick_Create_Master.php', type: 'POST', dataType: 'json', data: $(this).serialize()})
      .done(function (response) {
        if (activeProductInput) {
          activeProductInput.val(response.item.text).trigger('change');
          activeProductInput = null;
        } else if (activeSelect) {
          var option = new Option(response.item.text, response.item.value, true, true);
          activeSelect.append(option).trigger('change');
        }
        $('#quick-create-master-modal').modal('hide');
      })
      .fail(function (xhr) {
        $feedback.text(xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'Unable to create the record. Please try again.').show();
      })
      .always(function () {
        $button.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Create');
      });
  });
})(window.jQuery);
});
</script>
