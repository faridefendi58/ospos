<?php $this->load->view("partial/header"); ?>

<script type="text/javascript">
	dialog_support.init("a.modal-dlg");
</script>

<div id="page_title"><?php echo $title ?></div>

<div id="page_subtitle"><?php echo $subtitle ?></div>

<div id="title_bar" class="button-toolbar">
    <a href="javascript:printdocument();"><div class="btn btn-info btn-sm", id="show_print_button"><?php echo '<span class="glyphicon glyphicon-print">&nbsp</span>' . $this->lang->line('common_print'); ?></div></a>
</div>

<div id="table_holder">
	<table id="table"></table>
</div>

<div id="report_summary">
	<?php
	foreach($summary_data as $name => $value)
	{ 
		if($name == "total_quantity")
		{
	?>
			<div class="summary_row"><?php echo $this->lang->line('reports_'.$name) . ': ' .$value; ?></div>
	<?php
		}
		else
		{
	?>
			<div class="summary_row"><?php echo $this->lang->line('reports_'.$name) . ': ' . to_currency($value); ?></div>
	<?php
		}
	}
	?>
</div>

<script type="text/javascript">
	$(document).ready(function()
	{
		<?php $this->load->view('partial/bootstrap_tables_locale'); ?>

		$('#table').bootstrapTable({
			columns: <?php echo transform_headers($headers, TRUE, FALSE); ?>,
			stickyHeader: true,
			pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
			striped: true,
			sortable: true,
			showExport: true,
			exportDataType: 'all',
			exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'pdf'],
			pagination: true,
			showColumns: true,
			data: <?php echo json_encode($data); ?>,
			iconSize: 'sm',
			paginationVAlign: 'bottom',
			escape: false
		});

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
