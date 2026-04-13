<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<a href="<?php echo $back; ?>" data-toggle="tooltip" title="<?php echo $button_back; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
			</div>
			<h1><?php echo $heading_invoices; ?></h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb) { ?>
				<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="container-fluid">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading_invoices; ?></h3>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover">
						<thead>
							<tr>
								<td class="text-left"><?php echo $column_order_id; ?></td>
								<td class="text-left"><?php echo $column_invoice_id; ?></td>
								<td class="text-left"><?php echo $column_amount; ?></td>
								<td class="text-left"><?php echo $column_currency; ?></td>
								<td class="text-left"><?php echo $column_invoice_status; ?></td>
								<td class="text-left"><?php echo $column_transaction_state; ?></td>
								<td class="text-left"><?php echo $column_date_created; ?></td>
								<td class="text-left"><?php echo $column_date_modified; ?></td>
							</tr>
						</thead>
						<tbody>
							<?php if ($invoice_records) { ?>
								<?php foreach ($invoice_records as $invoice_record) { ?>
								<tr>
									<td class="text-left"><?php echo $invoice_record['order_id']; ?></td>
									<td class="text-left"><?php echo $invoice_record['invoice_id']; ?></td>
									<td class="text-left"><?php echo $invoice_record['amount']; ?></td>
									<td class="text-left"><?php echo $invoice_record['currency']; ?></td>
									<td class="text-left"><?php echo $invoice_record['invoice_status']; ?></td>
									<td class="text-left"><?php echo $invoice_record['transaction_state']; ?></td>
									<td class="text-left"><?php echo $invoice_record['date_created']; ?></td>
									<td class="text-left"><?php echo $invoice_record['date_modified']; ?></td>
								</tr>
								<?php } ?>
							<?php } else { ?>
							<tr>
								<td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>
