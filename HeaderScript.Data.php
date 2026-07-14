<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php if(!empty($organization_name)){ echo $organization_name; }else{ echo "Doel e-Services"; }  ?> | <?php if(!empty($MenuName)){ echo $MenuName; }else if(!empty($page_title)){ echo $page_title; }else{ echo "Login"; }  ?> </title>
  <link rel="icon" type="image/png" href="<?php if(!empty($feviconicon)){ echo $feviconicon; }else{ echo "image/e_Services.png"; }  ?>"/>
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="<?php if(!empty($feviconicon)){ echo $feviconicon; }else{ echo "image/e_Services.png"; }  ?>"/>

   <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Source+Sans+Pro:300,400,400i,700&display=swap">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- BS Stepper -->
  <link rel="stylesheet" href="plugins/bs-stepper/css/bs-stepper.min.css">
  <!-- dropzonejs -->
  <link rel="stylesheet" href="plugins/dropzone/min/dropzone.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
	
 <!-- Datatables Start-->
<!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
 <!--Datatables End-->	
 <link rel="stylesheet" href="Custom/app-shell.css?v=20260714-global-confirm">
	
	<style>
.jodit_toolbar_btn-about{
	display:none;
	}
</style>
<link rel="stylesheet" href="editor/build/jodit.min.css"/>
<link rel="stylesheet" href="editor/assets/prism.css"/>
<link rel="stylesheet" href="editor/assets/app.css"/>
<!--Custom File Start-->	
	<script src="plugins/jquery/jquery.min.js"></script>
	<link rel="stylesheet" href="plugins/toastr/toastr.min.css">
	<script src="plugins/toastr/toastr.min.js"></script>
	<style>
	.app-alert-overlay{
		position:fixed;
		inset:0;
		z-index:99999;
		display:none;
		align-items:center;
		justify-content:center;
		padding:22px;
		background:rgba(15,23,42,.54);
	}
	.app-alert-overlay.is-visible{
		display:flex;
	}
	.app-alert-dialog{
		width:min(620px, 94vw);
		background:#fff;
		border-radius:8px;
		box-shadow:0 24px 70px rgba(15,23,42,.30);
		overflow:hidden;
		text-align:center;
		border:1px solid #e5e7eb;
	}
	.app-alert-head{
		padding:22px 24px 12px;
	}
	.app-alert-icon{
		width:64px;
		height:64px;
		border-radius:50%;
		display:inline-flex;
		align-items:center;
		justify-content:center;
		margin-bottom:12px;
		color:#fff;
		font-size:30px;
		background:#dc2626;
	}
	.app-alert-dialog.success .app-alert-icon{
		background:#16a34a;
	}
	.app-alert-title{
		margin:0;
		color:#111827;
		font-size:28px;
		font-weight:900;
		letter-spacing:0;
	}
	.app-alert-body{
		padding:4px 34px 26px;
		color:#1f2937;
		font-size:20px;
		font-weight:700;
		line-height:1.45;
		white-space:pre-line;
	}
	.app-alert-actions{
		padding:0 24px 24px;
		display:flex;
		align-items:center;
		justify-content:center;
		gap:12px;
	}
	.app-alert-ok,
	.app-alert-cancel,
	.app-alert-confirm{
		min-width:128px;
		border:0;
		border-radius:8px;
		padding:12px 22px;
		background:#0ea5e9;
		color:#fff;
		font-size:16px;
		font-weight:800;
		cursor:pointer;
	}
	.app-alert-cancel{
		background:#e2e8f0;
		color:#334155;
	}
	.app-alert-confirm{
		background:#dc2626;
		color:#fff;
	}
	.app-alert-cancel:hover,
	.app-alert-cancel:focus{
		background:#cbd5e1;
	}
	.app-alert-confirm:hover,
	.app-alert-confirm:focus{
		background:#b91c1c;
	}
	.app-alert-dialog.error .app-alert-ok,
	.app-alert-dialog.warning .app-alert-ok{
		background:#dc2626;
	}
	</style>
	<script>
		toastr.options = {
			"closeButton": true,
			"progressBar": true,
			"positionClass": "toast-top-right",
			"timeOut": "5000",
			"preventDuplicates": true
		};
		window.appShowAlert = function(message, type, title) {
			type = type || 'warning';
			title = title || (type === 'success' ? 'Success' : 'Attention');
			var showDialog = function(){
				var overlay = document.getElementById('appAlertOverlay');
				if(!overlay){
					overlay = document.createElement('div');
					overlay.id = 'appAlertOverlay';
					overlay.className = 'app-alert-overlay';
					overlay.innerHTML =
						'<div class="app-alert-dialog" role="alertdialog" aria-modal="true">' +
							'<div class="app-alert-head">' +
								'<div class="app-alert-icon"><i class="fas fa-exclamation"></i></div>' +
								'<h2 class="app-alert-title"></h2>' +
							'</div>' +
							'<div class="app-alert-body"></div>' +
							'<div class="app-alert-actions"><button type="button" class="app-alert-ok">OK</button></div>' +
						'</div>';
					document.body.appendChild(overlay);
					overlay.querySelector('.app-alert-ok').addEventListener('click', function(){
						overlay.classList.remove('is-visible');
					});
				}
				var dialog = overlay.querySelector('.app-alert-dialog');
				dialog.className = 'app-alert-dialog ' + type;
				overlay.querySelector('.app-alert-icon').innerHTML = type === 'success' ? '<i class="fas fa-check"></i>' : '<i class="fas fa-exclamation"></i>';
				overlay.querySelector('.app-alert-title').textContent = title;
				overlay.querySelector('.app-alert-body').textContent = message;
				overlay.classList.add('is-visible');
			};
			if(document.body){
				showDialog();
			}else{
				document.addEventListener('DOMContentLoaded', showDialog);
			}
		};
		window.appConfirmAction = function(message, options) {
			options = options || {};
			return new Promise(function(resolve) {
				var showDialog = function() {
					var existingOverlay = document.getElementById('appConfirmOverlay');
					if (existingOverlay) {
						existingOverlay.remove();
					}
					var overlay = document.createElement('div');
					overlay.id = 'appConfirmOverlay';
					overlay.className = 'app-alert-overlay is-visible';
					overlay.innerHTML =
						'<div class="app-alert-dialog warning" role="alertdialog" aria-modal="true" aria-labelledby="appConfirmTitle">' +
							'<div class="app-alert-head">' +
								'<div class="app-alert-icon"><i class="fas fa-exclamation-triangle"></i></div>' +
								'<h2 class="app-alert-title" id="appConfirmTitle"></h2>' +
							'</div>' +
							'<div class="app-alert-body"></div>' +
							'<div class="app-alert-actions">' +
								'<button type="button" class="app-alert-cancel"></button>' +
								'<button type="button" class="app-alert-confirm"></button>' +
							'</div>' +
						'</div>';
					document.body.appendChild(overlay);
					overlay.querySelector('.app-alert-title').textContent = options.title || 'Please confirm';
					overlay.querySelector('.app-alert-body').textContent = message || 'Are you sure you want to continue?';
					var cancelButton = overlay.querySelector('.app-alert-cancel');
					var confirmButton = overlay.querySelector('.app-alert-confirm');
					cancelButton.textContent = options.cancelText || 'No, go back';
					confirmButton.textContent = options.confirmText || 'Yes, continue';

					var closeDialog = function(result) {
						document.removeEventListener('keydown', escapeHandler);
						overlay.remove();
						resolve(result);
					};
					var escapeHandler = function(event) {
						if (event.key === 'Escape') {
							closeDialog(false);
						}
					};
					cancelButton.addEventListener('click', function() { closeDialog(false); });
					confirmButton.addEventListener('click', function() { closeDialog(true); });
					overlay.addEventListener('click', function(event) {
						if (event.target === overlay) {
							closeDialog(false);
						}
					});
					document.addEventListener('keydown', escapeHandler);
					cancelButton.focus();
				};
				if (document.body) {
					showDialog();
				} else {
					document.addEventListener('DOMContentLoaded', showDialog, { once: true });
				}
			});
		};

		(function() {
			function normalizedActionText(element) {
				return ((element.getAttribute('value') || '') + ' ' + (element.getAttribute('name') || '') + ' ' + (element.textContent || ''))
					.replace(/\s+/g, ' ')
					.trim()
					.toLowerCase();
			}

			function actionConfirmation(element) {
				var explicitElement = element.closest('[data-app-confirm]');
				if (explicitElement) {
					return {
						message: explicitElement.getAttribute('data-app-confirm'),
						title: explicitElement.getAttribute('data-confirm-title') || 'Please confirm',
						confirmText: explicitElement.getAttribute('data-confirm-button') || 'Yes, continue'
					};
				}

				var href = element.getAttribute('href') || '';
				var text = normalizedActionText(element);
				var className = typeof element.className === 'string' ? element.className.toLowerCase() : '';
				var isRowRemoval = /(^|\s)(deleterow|ibtndel|delete-row|remove-[\w-]+)(\s|$)/.test(className);
				var hasTrashIcon = !!element.querySelector('.fa-trash, .fa-trash-alt');

				if (/sessiondistroy/i.test(href) || /(^|\s)sign\s*out(\s|$)/.test(text)) {
					return { message: 'Are you sure you want to sign out of the system?', title: 'Sign out?', confirmText: 'Yes, sign out' };
				}
				if (/\/delete(?:_|\/|$)/i.test(href) || /btn-delete-action/.test(className)) {
					return { message: 'This record will be removed. Are you sure you want to continue?', title: 'Delete record?', confirmText: 'Yes, delete' };
				}
				if (isRowRemoval || hasTrashIcon) {
					return { message: 'Remove this item from the current form?', title: 'Remove item?', confirmText: 'Yes, remove' };
				}
				if (/\breject\b/.test(text)) {
					return { message: 'This action will reject the request. Are you sure you want to continue?', title: 'Reject request?', confirmText: 'Yes, reject' };
				}
				if (/\bapprove\b/.test(text)) {
					return { message: 'Please confirm that you want to approve this request.', title: 'Approve request?', confirmText: 'Yes, approve' };
				}
				if (/merge/.test(text)) {
					return { message: 'The old product will be removed and its references will move to the selected master product. Continue?', title: 'Merge products?', confirmText: 'Yes, merge' };
				}
				if (/issue stock|final submit/.test(text)) {
					return { message: 'This action changes operational records and may not be reversible. Continue?', title: 'Confirm action?', confirmText: 'Yes, continue' };
				}
				if (/history\.back/i.test(element.getAttribute('onclick') || '')) {
					return { message: 'Any unsaved changes on this page will be lost. Do you want to leave?', title: 'Leave this page?', confirmText: 'Yes, leave' };
				}
				return null;
			}

			document.addEventListener('click', function(event) {
				if (!event.target || typeof event.target.closest !== 'function') {
					return;
				}
				var trigger = event.target.closest('a, button, input[type="button"], input[type="submit"]');
				if (!trigger || trigger.hasAttribute('data-app-confirm-skip')) {
					return;
				}
				if (trigger.getAttribute('data-app-confirm-bypass') === '1') {
					trigger.removeAttribute('data-app-confirm-bypass');
					return;
				}
				var confirmation = actionConfirmation(trigger);
				if (!confirmation) {
					return;
				}
				event.preventDefault();
				event.stopImmediatePropagation();
				window.appConfirmAction(confirmation.message, confirmation).then(function(confirmed) {
					if (!confirmed) {
						return;
					}
					var href = trigger.getAttribute('href') || '';
					if (/history\.back/i.test(trigger.getAttribute('onclick') || '')) {
						window.history.back();
						return;
					}
					if (trigger.tagName === 'A' && href && href !== '#') {
						window.location.assign(href);
						return;
					}
					if ((trigger.type || '').toLowerCase() === 'submit' && trigger.form) {
						if (typeof trigger.form.requestSubmit === 'function') {
							trigger.form.requestSubmit(trigger);
						} else {
							trigger.form.submit();
						}
						return;
					}
					trigger.setAttribute('data-app-confirm-bypass', '1');
					trigger.click();
				});
			}, true);
		})();
		window.alert = function(message) {
			window.appShowAlert(message, 'warning', 'Attention');
		};
	</script>
	<link rel="stylesheet" type="text/css" href="Custom/amsify.suggestags.css">
	<script type="text/javascript" src="Custom/jquery.amsify.suggestags.js"></script>
	<link rel="stylesheet" href="plugins/jquery-ui/jquery-ui.min.css">
	
<!--Enter Key form submit disible start-->			
		
	
<!--Custom File End-->	

</head>
