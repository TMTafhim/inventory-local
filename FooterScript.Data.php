



<!-- jQuery UI 1.11.4 -->
<script src="plugins/jquery-ui/jquery-ui.min.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="plugins/chart.js/Chart.min.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- Select2 -->
<script src="plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="plugins/moment/moment.min.js"></script>
<script src="plugins/daterangepicker/daterangepicker.js"></script>

<script src="plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap color picker -->
<script src="plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="plugins/summernote/summernote-bs4.min.js"></script>

<!-- overlayScrollbars -->


<!-- Bootstrap Switch -->
<script src="plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- BS-Stepper -->
<script src="plugins/bs-stepper/js/bs-stepper.min.js"></script>
<!-- dropzonejs -->
<script src="plugins/dropzone/min/dropzone.min.js"></script>

<!-- DataTables  & Plugins -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="plugins/jszip/jszip.min.js"></script>
<script src="plugins/pdfmake/pdfmake.min.js"></script>
<script src="plugins/pdfmake/vfs_fonts.js"></script>
<script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
<script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>




<script src="editor/build/jodit.min.js"></script>
<script src="editor/assets/prism.js"></script>
<script src="editor/assets/app.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof window.Jodit === 'undefined') return;
  window.appJoditEditors = window.appJoditEditors || {};
  ['area_editor1', 'area_editor2', 'area_editor3', 'area_editor4'].forEach(function (editorId) {
    var editorElement = document.getElementById(editorId);
    if (!editorElement || window.appJoditEditors[editorId]) return;
    window.appJoditEditors[editorId] = new Jodit(editorElement, {
      autofocus: true,
      iframe: true,
      uploader: { insertImageAsBase64URI: true },
      spellcheck: false
    });
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var textColumns = /^(name|product|description|details?|item|material|particulars?|note)$/i;
  var qtyColumns = /(qty|quantity|stock|issued|received|distribution|due|available|reconciled|outstanding)/i;
  var rateColumns = /^rate$/i;
  var amountColumns = /(amount|total|paid|bill|due amount|cash)/i;
  var dateColumns = /date/i;
  var statusColumns = /status/i;
  var remarksColumns = /(remarks?|comments?)$/i;

  function cleanHeader(value) {
    return (value || '').replace(/\s+/g, ' ').trim();
  }

  function columnClass(label, index) {
    var value = cleanHeader(label);
    if (index === 0 || /^(sl|s\/l|serial|#)$/i.test(value)) return 'doc-col-sl';
    if (textColumns.test(value)) return 'doc-col-text';
    if (remarksColumns.test(value)) return 'doc-col-remarks';
    if (rateColumns.test(value)) return 'doc-col-rate';
    if (amountColumns.test(value)) return 'doc-col-amount';
    if (qtyColumns.test(value)) return 'doc-col-qty';
    if (dateColumns.test(value)) return 'doc-col-date';
    if (statusColumns.test(value)) return 'doc-col-status';
    return '';
  }

  document.querySelectorAll('.my-info-area .table-div-area table, .my-info-area table.table, .d-print-block table.table').forEach(function (table) {
    var firstRow = table.querySelector('tr');
    if (!firstRow) return;
    var headers = Array.prototype.slice.call(firstRow.children);
    var classes = headers.map(function (cell, index) {
      return columnClass(cell.textContent, index);
    });

    Array.prototype.slice.call(table.rows).forEach(function (row) {
      Array.prototype.slice.call(row.children).forEach(function (cell, index) {
        if (classes[index]) {
          cell.classList.add(classes[index]);
        }
      });
    });
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  var pageTitle = document.querySelector('.app-page-titlecopy h1');
  if (!pageTitle) return;

  var normalizedPageTitle = pageTitle.textContent.replace(/\s+/g, ' ').trim().toLowerCase();
  if (!normalizedPageTitle) return;

  document.querySelectorAll('.content-wrapper .card-header .card-title').forEach(function (title) {
    var normalizedCardTitle = title.textContent.replace(/\s+/g, ' ').trim().toLowerCase();
    if (normalizedCardTitle !== normalizedPageTitle) return;

    title.classList.add('app-duplicate-title');

    var header = title.closest('.card-header');
    if (!header) return;

    var hasHeaderTools = header.querySelector('.card-tools, .box-tools, .pull-right, a.btn, button.btn, .btn-group');
    if (!hasHeaderTools) {
      header.classList.add('app-duplicate-title-only');
    }
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.content-wrapper table.table').forEach(function (table) {
    if (table.closest('.table-responsive, .app-responsive-table-wrap, .app-datatable-scroll, .dataTables_scroll, .my-info-area')) {
      return;
    }

    var wrapper = document.createElement('div');
    wrapper.className = 'app-responsive-table-wrap';
    table.parentNode.insertBefore(wrapper, table);
    wrapper.appendChild(table);
  });
});
</script>




<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="dist/js/demo.js"></script>
<!-- Page specific script -->

	<script>
	  $(function () {
	    var startTableEnhancements = window.requestIdleCallback
	      ? function (callback) { return window.requestIdleCallback(callback, { timeout: 350 }); }
	      : function (callback) { return window.setTimeout(callback, 1); };
	    startTableEnhancements(function () {
	    var example1Table = null;
	    var example2Table = null;
    function sanitizeText(value) {
      return $('<div>').html(value || '').text().replace(/\s+/g, ' ').trim();
    }

    function enhanceDataTableSearch(api) {
      var $table = $(api.table().node());
      var $wrapper = $(api.table().container());
      var $filter = $wrapper.find('.dataTables_filter');
      if (!$filter.length || $filter.data('appSearchReady')) return;
      $filter.data('appSearchReady', true);
      var hasProductRows = $table.find('tbody tr[data-product-ids]').length > 0;
      $wrapper.toggleClass('app-has-product-filter', hasProductRows);
      $filter.toggleClass('app-has-product-filter', hasProductRows);

      var searchId = $table.attr('id') + '-app-search';
      var scopeId = $table.attr('id') + '-app-search-scope';
      var productSearchId = $table.attr('id') + '-app-product-filter';
      var productIdId = $table.attr('id') + '-app-product-id';
      var columnOptions = '<option value="">All columns</option>';
      api.columns().every(function (index) {
        var label = sanitizeText($(this.header()).html());
        if (!label || /^option$/i.test(label)) return;
        columnOptions += '<option value="' + index + '">' + $('<div>').text(label).html() + '</option>';
      });

      $filter.empty().append(
        '<div class="app-table-search' + (hasProductRows ? ' app-table-search-with-product' : '') + '" role="search">' +
          (hasProductRows ? '<div class="app-table-search-control app-table-product-filter">' +
            '<span class="app-table-search-icon"><i class="fas fa-box"></i></span>' +
            '<input id="' + productSearchId + '" type="search" autocomplete="off" placeholder="Filter by product">' +
            '<input id="' + productIdId + '" type="hidden" value="">' +
          '</div>' : '') +
          '<div class="app-table-search-control app-table-search-field">' +
            '<span class="app-table-search-icon"><i class="fas fa-search"></i></span>' +
            '<input id="' + searchId + '" type="search" autocomplete="off" placeholder="Search table">' +
          '</div>' +
          '<div class="app-table-search-control app-table-search-scope">' +
            '<span class="app-table-search-icon"><i class="fas fa-filter"></i></span>' +
            '<select id="' + scopeId + '" aria-label="Search scope">' + columnOptions + '</select>' +
          '</div>' +
          '<button type="button" class="app-table-search-clear" aria-label="Clear search"><i class="fas fa-times-circle"></i><span>Clear</span></button>' +
        '</div>' +
        '<div class="app-table-search-meta" aria-live="polite"></div>'
      );

      var $input = $('#' + searchId);
      var $scope = $('#' + scopeId);
      var $productInput = $('#' + productSearchId);
      var $productId = $('#' + productIdId);
      var $meta = $filter.find('.app-table-search-meta');
      var debounceTimer = null;

      function runSearch() {
        var query = $input.val();
        var scope = $scope.val();
        api.search('');
        api.columns().search('');
        if (scope !== '') {
          api.column(Number(scope)).search(query, false, true);
        } else {
          api.search(query, false, true);
        }
        api.draw();
      }

      function updateMeta() {
        var info = api.page.info();
        var query = $input.val();
        var productQuery = (hasProductRows && $productId.val()) ? $productInput.val() : '';
        var scopeText = $scope.find('option:selected').text();
        var visible = info.recordsDisplay;
        var total = info.recordsTotal;
        if (query || productQuery) {
          var pieces = [];
          if (productQuery) pieces.push('Product: ' + productQuery);
          if (query) pieces.push(scopeText);
          var suffix = pieces.length ? ' ¡¤ ' + pieces.join(' ¡¤ ') : '';
          $meta.html('<strong>' + visible + '</strong> / ' + total + ' matched' + suffix);
        } else {
          $meta.html('<strong>' + total + '</strong> records ready');
        }
      }

      if (hasProductRows && $.fn.dataTable && !$.fn.dataTable.ext.search._appProductUsageFilterReady) {
        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
          var tableNode = api.table().node();
          if (settings.nTable !== tableNode) return true;
          var selectedProductId = $productId.val();
          if (!selectedProductId) return true;
          var rowNode = api.row(dataIndex).node();
          var rowProducts = ($(rowNode).attr('data-product-ids') || '').split(',');
          return rowProducts.indexOf(selectedProductId) !== -1;
        });
        $.fn.dataTable.ext.search._appProductUsageFilterReady = true;
      }

      if (hasProductRows && $productInput.length) {
        $productInput.autocomplete({
          minLength: 1,
          delay: 180,
          source: function(request, response) {
            $.getJSON('ajax_Product_Usage_Filter.php', { term: request.term }).done(response).fail(function() {
              response([]);
            });
          },
          select: function(event, ui) {
            $productInput.val(ui.item.value);
            $productId.val(ui.item.id);
            api.draw();
            return false;
          }
        });
        $productInput.on('input', function() {
          if (!$productInput.val() || $productId.val()) {
            $productId.val('');
            api.draw();
          }
        });
      }

      $input.on('input', function () {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(runSearch, 180);
      });
      $scope.on('change', runSearch);
      $filter.find('.app-table-search-clear').on('click', function () {
        $input.val('');
        $scope.val('');
        if (hasProductRows) {
          $productInput.val('');
          $productId.val('');
        }
        runSearch();
        $input.trigger('focus');
      });
      api.on('draw', updateMeta);
      updateMeta();
    }

    if ($("#example1").length) {
	      example1Table = $("#example1").DataTable({
	        "responsive": false,
	        "lengthChange": false,
	        "autoWidth": false,
	        "deferRender": true,
	        "processing": true,
	        "pageLength": 25,
	        "searchDelay": 180,
        "language": {
          "emptyTable": "No records available",
          "zeroRecords": "No matching records found"
        },
	        "buttons": [
	          { extend: "copy", text: '<i class="far fa-copy"></i><span>Copy</span>' },
	          { extend: "csv", text: '<i class="fas fa-file-csv"></i><span>CSV</span>' },
	          { extend: "excel", text: '<i class="far fa-file-excel"></i><span>Excel</span>' },
	          { extend: "pdf", text: '<i class="far fa-file-pdf"></i><span>PDF</span>' },
	          { extend: "print", text: '<i class="fas fa-print"></i><span>Print</span>' },
	          { extend: "colvis", text: '<i class="fas fa-sliders-h"></i><span>Columns</span>' }
	        ]
      });
      example1Table.buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      enhanceDataTableSearch(example1Table);
    }
    if ($('#example2').length) {
	      example2Table = $('#example2').DataTable({
	        "paging": true,
	        "lengthChange": false,
	        "searching": false,
	        "ordering": true,
	        "info": true,
	        "autoWidth": false,
	        "responsive": false,
	        "deferRender": true,
	        "processing": true,
	        "pageLength": 25
	      });
    }

    function wrapDataTable(api) {
      var table = $(api.table().node());
      if (table.length && !table.parent().hasClass('app-datatable-scroll')) {
        table.wrap('<div class="app-datatable-scroll"></div>');
      }
    }

    if (example1Table) wrapDataTable(example1Table);
    if (example2Table) wrapDataTable(example2Table);

    var resizeTimer;
    function adjustDataTableColumns() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        $.fn.dataTable
          .tables({ visible: true, api: true })
          .columns.adjust();
      }, 180);
    }

    adjustDataTableColumns();
    $(window).on('resize orientationchange', adjustDataTableColumns);
	    $(document).on('collapsed.lte.pushmenu shown.lte.pushmenu expanded.lte.pushmenu', function () {
	      setTimeout(adjustDataTableColumns, 320);
	    });
	    });
	  });
	</script>
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

    //Datemask dd/mm/yyyy
    $('#datemask').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' })
    //Datemask2 mm/dd/yyyy
    $('#datemask2').inputmask('mm/dd/yyyy', { 'placeholder': 'mm/dd/yyyy' })
    //Money Euro
    $('[data-mask]').inputmask()

    //Date picker
    $('#reservationdate').datetimepicker({
        format: 'L'
    });

    //Date and time picker
    $('#reservationdatetime').datetimepicker({ icons: { time: 'far fa-clock' } });

    //Date range picker
    $('#reservation').daterangepicker()
    //Date range picker with time picker
    $('#reservationtime').daterangepicker({
      timePicker: true,
      timePickerIncrement: 30,
      locale: {
        format: 'MM/DD/YYYY hh:mm A'
      }
    })
    //Date range as a button
    $('#daterange-btn').daterangepicker(
      {
        ranges   : {
          'Today'       : [moment(), moment()],
          'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
          'Last 30 Days': [moment().subtract(29, 'days'), moment()],
          'This Month'  : [moment().startOf('month'), moment().endOf('month')],
          'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'days'),
        endDate  : moment()
      },
      function (start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
      }
    )

    //Timepicker
    $('#timepicker').datetimepicker({
      format: 'LT'
    })

    //Bootstrap Duallistbox
    $('.duallistbox').bootstrapDualListbox()

    //Colorpicker
    $('.my-colorpicker1').colorpicker()
    //color picker with addon
    $('.my-colorpicker2').colorpicker()

    $('.my-colorpicker2').on('colorpickerChange', function(event) {
      $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
    })

    $("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    })

  })
  // BS-Stepper Init
  document.addEventListener('DOMContentLoaded', function () {
    var stepperElement = document.querySelector('.bs-stepper');
    if (stepperElement && typeof window.Stepper !== 'undefined') {
      window.stepper = new Stepper(stepperElement);
    }
  })

  // DropzoneJS Demo Code Start
  Dropzone.autoDiscover = false

  // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
  var previewNode = document.querySelector("#template")
  var actionsNode = document.querySelector("#actions")
  var previewsNode = document.querySelector("#previews")
  var totalProgressNode = document.querySelector("#total-progress")
  var totalProgressBar = document.querySelector("#total-progress .progress-bar")
  var startAllButton = document.querySelector("#actions .start")
  var cancelAllButton = document.querySelector("#actions .cancel")
  if (previewNode && actionsNode && previewsNode && totalProgressNode && totalProgressBar && startAllButton && cancelAllButton && typeof window.Dropzone !== 'undefined') {
  previewNode.id = ""
  var previewTemplate = previewNode.parentNode.innerHTML
  previewNode.parentNode.removeChild(previewNode)

  var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
    url: "/target-url", // Set the url
    thumbnailWidth: 80,
    thumbnailHeight: 80,
    parallelUploads: 20,
    previewTemplate: previewTemplate,
    autoQueue: false, // Make sure the files aren't queued until manually added
    previewsContainer: "#previews", // Define the container to display the previews
    clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
  })

  myDropzone.on("addedfile", function(file) {
    // Hookup the start button
    file.previewElement.querySelector(".start").onclick = function() { myDropzone.enqueueFile(file) }
  })

  // Update the total progress bar
  myDropzone.on("totaluploadprogress", function(progress) {
    totalProgressBar.style.width = progress + "%"
  })

  myDropzone.on("sending", function(file) {
    // Show the total progress bar when upload starts
    totalProgressNode.style.opacity = "1"
    // And disable the start button
    file.previewElement.querySelector(".start").setAttribute("disabled", "disabled")
  })

  // Hide the total progress bar when nothing's uploading anymore
  myDropzone.on("queuecomplete", function(progress) {
    totalProgressNode.style.opacity = "0"
  })

  // Setup the buttons for all transfers
  // The "add files" button doesn't need to be setup because the config
  // `clickable` has already been specified.
  startAllButton.onclick = function() {
    myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED))
  }
  cancelAllButton.onclick = function() {
    myDropzone.removeAllFiles(true)
  }
  }
  // DropzoneJS Demo Code End
</script>
<?php include_once("QuickCreateMaster.Data.php"); ?>
