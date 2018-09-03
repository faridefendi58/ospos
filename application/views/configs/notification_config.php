<?php echo form_open('config/save_notification/', array('id' => 'notification_config_form', 'class' => 'form-horizontal')); ?>
<div id="config_wrapper">
    <fieldset id="config_info">
        <div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>
        <ul id="notification_error_message_box" class="error_message_box"></ul>

        <div class="form-group form-group-sm">
            <?php echo form_label($this->lang->line('config_notification_enable'), 'notification_enable', array('class' => 'control-label col-xs-4')); ?>
            <div class='col-xs-1'>
                <?php echo form_checkbox(array(
                    'name' => 'notification_enable',
                    'value' => 'notification_enable',
                    'id' => 'notification_enable',
                    'checked' => $this->config->item('notification_enable')));?>
            </div>
        </div>

        <div class="form-group form-group-sm">
            <?php echo form_label($this->lang->line('config_max_day_before_expired'), 'max_day_before_expired', array('class' => 'control-label col-xs-4')); ?>
            <div class='col-xs-2'>
                <?php echo form_input(array(
                    'type' => 'number',
                    'name' => 'notif_max_day_before_expired',
                    'id' => 'notif_max_day_before_expired',
                    'class' => 'form-control input-sm required',
                    'value' => $this->config->item('notif_max_day_before_expired'))); ?>
            </div>
        </div>

        <div class="form-group form-group-sm">
            <?php echo form_label($this->lang->line('config_notif_limit_quantity'), 'notif_limit_quantity', array('class' => 'control-label col-xs-4')); ?>
            <div class='col-xs-2'>
                <?php echo form_input(array(
                    'type' => 'number',
                    'name' => 'notif_limit_quantity',
                    'id' => 'notif_limit_quantity',
                    'class' => 'form-control input-sm required',
                    'value' => $this->config->item('notif_limit_quantity'))); ?>
            </div>
        </div>

        <?php echo form_submit(array(
            'name' => 'submit_notification',
            'id' => 'submit_notification',
            'value' => $this->lang->line('common_submit'),
            'class' => 'btn btn-primary btn-sm pull-right'));?>
    </fieldset>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    //validation and submit handling
    $(document).ready(function()
    {
        $("#notification_config_form").validate($.extend(form_support.handler, {

            errorLabelContainer: "#notification_error_message_box",

            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    beforeSerialize: function(arr, $form, options) {
                        $("#notif_max_day_before_expired, #notif_limit_quantity").prop("disabled", false);
                        return true;
                    },
                    success: function(response) {
                        $.notify(response.message, { type: response.success ? 'success' : 'danger'} );
                    },
                    dataType: 'json'
                });
            }
        }));
    });
</script>
