<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="<?php echo $invoices; ?>" data-toggle="tooltip" title="<?php echo $button_invoices; ?>"
					class="btn btn-info"><i class="fa fa-list"></i></a>
				<button type="submit" form="form-paydo" data-toggle="tooltip" title="<?php echo $button_save; ?>"
						class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
					class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_title; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="container-fluid">
		<?php if ($error_warning) { ?>
		<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
			<button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
		<?php } ?>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-paydo" class="form-horizontal">

					<div class="form-group">
						<label class="col-sm-2 control-label" for="entry-ipn_url">IPN URL</label>
						<div class="col-sm-10">
							<input type="text" value="<?php echo $ipn_url; ?>" readonly id="entry-ipn_url" class="form-control"/>
						</div>
					</div>

					<div class="form-group required">
						<label class="col-sm-2 control-label" for="input-public"><?php echo $entry_public; ?></label>
						<div class="col-sm-10">
							<input type="text"
								name="paydo_public_id"
								value="<?php echo $paydo_public_id; ?>"
								placeholder="<?php echo $entry_public; ?>"
								id="input-public"
								class="form-control"
								autocomplete="off"/>
							<?php if ($error_public_id) { ?>
							<div class="text-danger"><?php echo $error_public_id; ?></div>
							<?php } ?>
						</div>
					</div>

					<div class="form-group required">
						<label class="col-sm-2 control-label" for="input-secret"><?php echo $entry_secret; ?></label>
						<div class="col-sm-10">
							<input type="text"
								name="paydo_secret_key"
								value="<?php echo $paydo_secret_key; ?>"
								placeholder="<?php echo $entry_secret; ?>"
								id="input-secret"
								class="form-control"
								autocomplete="off"/>
							<?php if ($error_secret_key) { ?>
							<div class="text-danger"><?php echo $error_secret_key; ?></div>
							<?php } ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-methods-mode">
							<span data-toggle="tooltip" title="<?php echo htmlspecialchars($help_methods_mode, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $entry_methods_mode; ?></span>
						</label>
						<div class="col-sm-10">
							<input type="hidden" name="paydo_methods_mode" value="0"/>
							<label class="checkbox-inline">
								<input type="checkbox" name="paydo_methods_mode" value="1" id="input-methods-mode"<?php echo $paydo_methods_mode ? ' checked="checked"' : ''; ?>/>
								<?php echo $help_methods_mode; ?>
							</label>
						</div>
					</div>

					<div id="paydo-method-settings">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-hosted-page">
								<span data-toggle="tooltip" title="<?php echo htmlspecialchars($help_hosted_page, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $entry_hosted_page; ?></span>
							</label>
							<div class="col-sm-10">
								<input type="hidden" name="paydo_hosted_page_enabled" value="0"/>
								<label class="checkbox-inline">
									<input type="checkbox" name="paydo_hosted_page_enabled" value="1" id="input-hosted-page"<?php echo $paydo_hosted_page_enabled ? ' checked="checked"' : ''; ?>/>
									<?php echo $help_hosted_page; ?>
								</label>
							</div>
						</div>

						<div class="form-group<?php echo $error_project_id ? ' has-error' : ''; ?>">
							<label class="col-sm-2 control-label" for="input-project-id">
								<span data-toggle="tooltip" title="<?php echo htmlspecialchars($help_project_id, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $entry_project_id; ?></span>
							</label>
							<div class="col-sm-10">
								<input type="text" name="paydo_project_id" value="<?php echo htmlspecialchars((string)$paydo_project_id, ENT_QUOTES, 'UTF-8'); ?>" placeholder="<?php echo $entry_project_id; ?>" id="input-project-id" class="form-control" autocomplete="off"/>
								<?php if ($error_project_id) { ?><div class="text-danger"><?php echo $error_project_id; ?></div><?php } ?>
							</div>
						</div>

						<div class="form-group<?php echo $error_jwt_token ? ' has-error' : ''; ?>">
							<label class="col-sm-2 control-label" for="input-jwt-token">
								<span data-toggle="tooltip" title="<?php echo htmlspecialchars($help_jwt_token, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $entry_jwt_token; ?></span>
							</label>
							<div class="col-sm-10">
								<textarea name="paydo_jwt_token" rows="3" placeholder="<?php echo $entry_jwt_token; ?>" id="input-jwt-token" class="form-control" autocomplete="off"><?php echo htmlspecialchars((string)$paydo_jwt_token, ENT_QUOTES, 'UTF-8'); ?></textarea>
								<?php if ($error_jwt_token) { ?><div class="text-danger"><?php echo $error_jwt_token; ?></div><?php } ?>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"><?php echo $button_sync_methods; ?></label>
							<div class="col-sm-10">
								<button type="button" id="button-sync-methods" class="btn btn-default"><i class="fa fa-refresh"></i> <?php echo $button_sync_methods; ?></button>
								<span id="paydo-sync-status" class="help-block" style="display:inline-block;margin:0 0 0 10px;"></span>
							</div>
						</div>

						<div class="form-group<?php echo $error_enabled_methods ? ' has-error' : ''; ?>">
							<label class="col-sm-2 control-label" for="input-method-search">
								<span data-toggle="tooltip" title="<?php echo htmlspecialchars($help_enabled_methods, ENT_QUOTES, 'UTF-8'); ?>"><?php echo $entry_enabled_methods; ?></span>
							</label>
							<div class="col-sm-10">
								<input type="text" id="input-method-search" class="form-control" placeholder="<?php echo htmlspecialchars($text_search_method, ENT_QUOTES, 'UTF-8'); ?>" style="max-width:520px;margin-bottom:10px;"/>
								<div id="paydo-methods-list" class="well well-sm" style="max-width:520px;max-height:320px;overflow:auto;">
									<?php if ($paydo_available_methods) { ?>
										<?php foreach ($paydo_available_methods as $identifier => $method) { ?>
											<?php
											$method_identifier = isset($method['identifier']) ? (string)$method['identifier'] : (string)$identifier;
											$method_title = isset($method['title']) ? (string)$method['title'] : ('#' . $method_identifier);
											$method_logo = isset($method['logo']) ? (string)$method['logo'] : '';
											?>
											<label class="paydo-method-row" data-title="<?php echo htmlspecialchars(strtolower($method_title), ENT_QUOTES, 'UTF-8'); ?>" style="display:flex;align-items:center;gap:10px;padding:7px;margin:0;">
												<input type="checkbox" name="paydo_enabled_methods[]" value="<?php echo htmlspecialchars($method_identifier, ENT_QUOTES, 'UTF-8'); ?>"<?php echo in_array($method_identifier, array_map('strval', $paydo_enabled_methods), true) ? ' checked="checked"' : ''; ?>/>
												<?php if ($method_logo) { ?><img src="<?php echo htmlspecialchars($method_logo, ENT_QUOTES, 'UTF-8'); ?>" alt="" style="width:auto;height:24px;object-fit:contain;"/><?php } ?>
												<span><strong><?php echo htmlspecialchars($method_title, ENT_QUOTES, 'UTF-8'); ?></strong><br/><small>#<?php echo htmlspecialchars($method_identifier, ENT_QUOTES, 'UTF-8'); ?></small></span>
											</label>
										<?php } ?>
									<?php } else { ?>
										<div class="paydo-no-methods text-muted"><?php echo $text_no_methods; ?></div>
									<?php } ?>
								</div>
								<?php if ($error_enabled_methods) { ?><div class="text-danger"><?php echo $error_enabled_methods; ?></div><?php } ?>
								<p class="help-block"><?php echo $help_enabled_methods; ?></p>
							</div>
						</div>
					</div>

					<!-- Success -->
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-complete-status"><?php echo $entry_complete_status; ?></label>
						<div class="col-sm-10">
							<select name="paydo_order_status_success" id="input-complete-status" class="form-control">
								<?php foreach ($order_statuses as $order_status) { ?>
									<?php if ($order_status['order_status_id'] == $paydo_order_status_success) { ?>
										<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
									<?php } else { ?>
										<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
									<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>

					<!-- Pending -->
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-pending-status"><?php echo $entry_pending_status; ?></label>
						<div class="col-sm-10">
							<select name="paydo_order_status_wait" id="input-pending-status" class="form-control">
								<?php foreach ($order_statuses as $order_status) { ?>
									<?php if ($order_status['order_status_id'] == $paydo_order_status_wait) { ?>
										<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
									<?php } else { ?>
										<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
									<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>

					<!-- Failed -->
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-failed-status"><?php echo $entry_failed_status; ?></label>
						<div class="col-sm-10">
							<select name="paydo_order_status_error" id="input-failed-status" class="form-control">
								<?php foreach ($order_statuses as $order_status) { ?>
									<?php if ($order_status['order_status_id'] == $paydo_order_status_error) { ?>
										<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
									<?php } else { ?>
										<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
									<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>

					<!-- Enabled/Disabled -->
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
						<div class="col-sm-10">
							<select name="paydo_status" id="input-status" class="form-control">
								<?php if ($paydo_status) { ?>
									<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
									<option value="0"><?php echo $text_disabled; ?></option>
								<?php } else { ?>
									<option value="1"><?php echo $text_enabled; ?></option>
									<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<!-- Sort order -->
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
						<div class="col-sm-10">
							<input type="text" name="paydo_sort_order" value="<?php echo $paydo_sort_order; ?>"
								placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control"/>
						</div>
					</div>

				</form>
			</div>
		</div>

	</div>
</div>
<script type="text/javascript">
(function ($) {
	'use strict';

	var strings = {
		syncing: <?php echo json_encode($text_syncing); ?>,
		success: <?php echo json_encode($text_sync_success); ?>,
		error: <?php echo json_encode($text_sync_error); ?>,
		empty: <?php echo json_encode($text_no_methods); ?>
	};

	function toggleMethodSettings() {
		$('#paydo-method-settings').toggle($('#input-methods-mode').is(':checked'));
	}

	function filterMethods() {
		var query = $.trim($('#input-method-search').val()).toLowerCase();
		$('#paydo-methods-list .paydo-method-row').each(function () {
			$(this).toggle(!query || $(this).text().toLowerCase().indexOf(query) !== -1);
		});
	}

	function renderMethods(methods) {
		var $list = $('#paydo-methods-list');
		var checked = {};
		$list.find('input[name="paydo_enabled_methods[]"]:checked').each(function () {
			checked[String(this.value)] = true;
		});
		$list.empty();

		if (!methods || !methods.length) {
			$('<div class="paydo-no-methods text-muted"></div>').text(strings.empty).appendTo($list);
			return;
		}

		$.each(methods, function (index, method) {
			var identifier = String(method.identifier || '');
			var title = String(method.title || ('#' + identifier));
			var $row = $('<label class="paydo-method-row"></label>')
				.attr('data-title', title.toLowerCase())
				.css({display: 'flex', alignItems: 'center', gap: '10px', padding: '7px', margin: 0});
			$('<input type="checkbox" name="paydo_enabled_methods[]"/>')
				.val(identifier)
				.prop('checked', !!checked[identifier])
				.appendTo($row);
			if (method.logo) {
				$('<img alt=""/>').attr('src', String(method.logo)).css({width: 'auto', height: '24px', objectFit: 'contain'}).appendTo($row);
			}
			$('<span></span>').append($('<strong></strong>').text(title), $('<br/>'), $('<small></small>').text('#' + identifier)).appendTo($row);
			$row.appendTo($list);
		});

		filterMethods();
	}

	$('#input-methods-mode').on('change', toggleMethodSettings);
	$('#input-method-search').on('input', filterMethods);
	$('#button-sync-methods').on('click', function () {
		var $button = $(this);
		var $status = $('#paydo-sync-status');
		$button.prop('disabled', true);
		$status.removeClass('text-danger text-success').text(strings.syncing);

		$.ajax({
			url: <?php echo json_encode($sync_methods_url); ?>,
			type: 'post',
			dataType: 'json',
			data: {
				project_id: $('#input-project-id').val(),
				jwt_token: $('#input-jwt-token').val()
			}
		}).done(function (json) {
			if (!json || !json.success) {
				$status.addClass('text-danger').text(json && json.error ? json.error : strings.error);
				return;
			}

			renderMethods(json.methods || []);
			$status.addClass('text-success').text(strings.success.replace('%s', String(json.count || 0)));
		}).fail(function () {
			$status.addClass('text-danger').text(strings.error);
		}).always(function () {
			$button.prop('disabled', false);
		});
	});

	toggleMethodSettings();
})(jQuery);
</script>
<?php echo $footer; ?>
