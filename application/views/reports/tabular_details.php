<?php $this->load->view("partial/header"); ?>

<div id="page_title"><?php echo $title ?></div>

<div id="page_subtitle"><?php echo $subtitle ?></div>

<div id="title_bar" class="button-toolbar no-print">
    <a href="javascript:printdocument();"><div class="btn btn-info btn-sm", id="show_print_button"><?php echo '<span class="glyphicon glyphicon-print">&nbsp</span>' . $this->lang->line('common_print'); ?></div></a>
</div>

<div id="table_holder">
	<table id="table"></table>
</div>

<div id="report_summary">
    <?php
    $sum_data = 0;
    if (isset($total_each_status) && is_array($total_each_status)):?>
        <?php foreach ($total_each_status as $status => $tot_status): ?>
            <div class="summary_row">
                <?php echo '<b>Total '. $status. '</b>: '.to_currency($tot_status); ?>
            </div>
            <?php $sum_data = $sum_data + $tot_status; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="summary_row">
        <?php echo '<b>'.$this->lang->line('reports_total'). '</b>: '.to_currency($sum_data); ?>
    </div>

	<?php
	/*foreach($overall_summary_data as $name=>$value)
	{
	?>
		<div class="summary_row"><?php echo '<b>'.$this->lang->line('reports_'.$name). '</b>: '.to_currency($value); ?></div>
	<?php
	}*/
	?>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
	 	<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

		var details_data = <?php echo json_encode($details_data); ?>;
		<?php
		if($this->config->item('customer_reward_enable') == TRUE && !empty($details_data_rewards))
		{
		?>
			var details_data_rewards = <?php echo json_encode($details_data_rewards); ?>;
		<?php
		}
		?>
		var init_dialog = function() {
			<?php
			if(isset($editable))
			{
			?>
				table_support.submit_handler('<?php echo site_url("reports/get_detailed_" . $editable . "_row")?>');
				dialog_support.init("a.modal-dlg");
			<?php
			}
			?>
		};

		$('#table').bootstrapTable({
			columns: <?php echo transform_headers($headers['summary'], TRUE); ?>,
			stickyHeader: true,
			pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
			striped: true,
			pagination: true,
			sortable: true,
			showColumns: true,
			uniqueId: 'id',
			showExport: true,
			exportDataType: 'all',
			exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'pdf'],
			data: <?php echo json_encode($summary_data); ?>,
			iconSize: 'sm',
			paginationVAlign: 'bottom',
			detailView: true,
			escape: false,
			onPageChange: init_dialog,
			onPostBody: function() {
				dialog_support.init("a.modal-dlg");
			},
			onExpandRow: function (index, row, $detail) {
				$detail.html('<table></table>').find("table").bootstrapTable({
					columns: <?php echo transform_headers_readonly($headers['details']); ?>,
					data: details_data[(!isNaN(row.id) && row.id) || $(row[0] || row.id).text().replace(/(POS|RECV)\s*/g, '')]
				});

				<?php
				if($this->config->item('customer_reward_enable') == TRUE && !empty($details_data_rewards))
				{
				?>
					$detail.append('<table></table>').find("table").bootstrapTable({
						columns: <?php echo transform_headers_readonly($headers['details_rewards']); ?>,
						data: details_data_rewards[(!isNaN(row.id) && row.id) || $(row[0] || row.id).text().replace(/(POS|RECV)\s*/g, '')]
					});
				<?php
				}
				?>
			}
		});

		init_dialog();
	});
    function printdocument()
    {
        // install firefox addon in order to use this plugin
        if (window.jsPrintSetup)
        {
            // set top margins in millimeters
            jsPrintSetup.setOption('marginTop', '<?php echo $this->config->item('print_top_margin'); ?>');
            jsPrintSetup.setOption('marginLeft', '<?php echo $this->config->item('print_left_margin'); ?>');
            jsPrintSetup.setOption('marginBottom', '<?php echo $this->config->item('print_bottom_margin'); ?>');
            jsPrintSetup.setOption('marginRight', '<?php echo $this->config->item('print_right_margin'); ?>');

            <?php if (!$this->config->item('print_header'))
            {
            ?>
            // set page header
            jsPrintSetup.setOption('headerStrLeft', '');
            jsPrintSetup.setOption('headerStrCenter', '');
            jsPrintSetup.setOption('headerStrRight', '');
            <?php
            }
            if (!$this->config->item('print_footer'))
            {
            ?>
            // set empty page footer
            jsPrintSetup.setOption('footerStrLeft', '');
            jsPrintSetup.setOption('footerStrCenter', '');
            jsPrintSetup.setOption('footerStrRight', '');
            <?php
            }
            ?>

            var printers = jsPrintSetup.getPrintersList().split(',');
            // get right printer here..
            for(var index in printers) {
                var default_ticket_printer = window.localStorage && localStorage['invoice_printer'];
                var selected_printer = printers[index];
                if (selected_printer == default_ticket_printer) {
                    // select epson label printer
                    jsPrintSetup.setPrinter(selected_printer);
                    // clears user preferences always silent print value
                    // to enable using 'printSilent' option
                    jsPrintSetup.clearSilentPrint();
                    <?php if (!$this->config->item('print_silently'))
                    {
                    ?>
                    // Suppress print dialog (for this context only)
                    jsPrintSetup.setOption('printSilent', 1);
                    <?php
                    }
                    ?>
                    // Do Print
                    // When print is submitted it is executed asynchronous and
                    // script flow continues after print independently of completetion of print process!
                    jsPrintSetup.print();
                }
            }
        }
        else
        {
            window.print();
        }
    }
</script>

<?php $this->load->view("partial/footer"); ?>
