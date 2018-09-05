<?php $this->load->view("partial/header"); ?>

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h1><?php echo $this->lang->line('item_expiration_dates_detail'); ?></h1>

            <table class="table table-striped table-hover ">
                <tbody>
                <tr>
                    <td><?php echo $this->lang->line('item_expiration_dates_id'); ?></td>
                    <td><?php echo $info->id; ?></td>
                </tr>
                <tr class="active">
                    <td><?php echo $this->lang->line('item_expiration_dates_item_name'); ?></td>
                    <td><?php echo $info->item_name; ?></td>
                </tr>
                <tr>
                    <td><?php echo $this->lang->line('item_expiration_dates_quantity'); ?></td>
                    <td><?php echo $info->quantity; ?></td>
                </tr>
                <tr class="active">
                    <td><?php echo $this->lang->line('item_expiration_dates_expired_at'); ?></td>
                    <td><?php echo date("d/M/Y", strtotime($info->expired_at)); ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

<?php $this->load->view("partial/footer"); ?>
<script type="text/javascript">
    $(function () {
        setTimeout(function () {
            $('#notif-alert-group').hide();
        }, 1000);
    });
</script>
