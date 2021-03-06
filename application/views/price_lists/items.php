<?php $this->load->view("partial/header"); ?>

    <script type="text/javascript">
        $(document).ready(function() {
            <?php $this->load->view('partial/bootstrap_tables_locale'); ?>
            table_support.init({
                resource: '<?php echo site_url($controller_name);?>',
                custom_resource: '<?php echo site_url($controller_name.'/search_items/'.$price_list_id);?>',
                delete_resource: '<?php echo site_url($controller_name.'/delete_items');?>',
                headers: <?php echo $table_headers; ?>,
                pageSize: <?php echo $this->config->item('lines_per_page'); ?>,
                uniqueId: 'id'
            });
        });

    </script>

    <div id="title_bar" class="btn-toolbar">
        <button class='btn btn-info btn-sm pull-right modal-dlg' data-btn-submit='<?php echo $this->lang->line('common_submit') ?>'
                data-href='<?php echo site_url($controller_name."/excel_import/".$price_list_id); ?>'
                title='<?php echo $this->lang->line('items_import_items_excel'); ?>'>
            <span class="glyphicon glyphicon-import">&nbsp</span><?php echo $this->lang->line('common_import_excel'); ?>
        </button>

        <button
            class='btn btn-info btn-sm pull-right modal-dlg'
            data-btn-submit='<?php echo $this->lang->line('common_submit') ?>'
            data-href='<?php echo site_url($controller_name ."/view_list/". $price_list_id); ?>'
                title='<?php echo $this->lang->line($controller_name. '_new'); ?>'>
            <span class="glyphicon glyphicon-tags">&nbsp</span><?php echo $this->lang->line($controller_name. '_item_new'); ?>
        </button>
    </div>

    <div id="toolbar">
        <div class="pull-left btn-toolbar">
            <button id="delete" class="btn btn-default btn-sm">
                <span class="glyphicon glyphicon-trash">&nbsp</span><?php echo $this->lang->line("common_delete"); ?>
            </button>
        </div>
    </div>

    <div id="table_holder">
        <table id="table"></table>
    </div>

<?php $this->load->view("partial/footer"); ?>