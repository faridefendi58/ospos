<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('item_expiration_dates/save/'.$item_expiration_date_info->id, array('id'=>'item_expiration_date_form', 'class'=>'form-horizontal')); ?>
<fieldset id="item_kit_basic_info">

    <div class="form-group form-group-sm">
        <?php echo form_label($this->lang->line('item_expiration_dates_item_name'), 'item_name', array('class'=>'required control-label col-xs-3')); ?>
        <div class='col-xs-8'>
            <select name="item_id" class="form-control input-sm">
                <?php foreach ($items as $it => $item): ?>
                    <option
                        value="<?php echo $item->item_id; ?>"
                        <?php if ($item->item_id == $item_expiration_date_info->item_id): ?>selected="selected"<?php endif; ?>>
                        <?php echo $item->name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php if (!empty($item_expiration_date_info->id)): ?>
        <input type="hidden" name="id" value="<?php echo $item_expiration_date_info->id;?>">
    <?php endif; ?>

    <div class="form-group form-group-sm">
        <?php echo form_label($this->lang->line('item_expiration_dates_quantity'), 'quantity', array('class'=>'required control-label col-xs-3')); ?>
        <div class='col-xs-8'>
            <?php echo form_input(array(
                    'name' => 'quantity',
                    'id' => 'quantity',
                    'class' => 'form-control input-sm',
                    'value' => (empty($item_expiration_date_info->quantity))? 1 : $item_expiration_date_info->quantity)
            );?>
        </div>
    </div>

    <div class="form-group form-group-sm">
        <?php echo form_label($this->lang->line('item_expiration_dates_expired_at'), 'exp_date_range_label', array('class'=>'control-label col-xs-3 required')); ?>
        <div class="col-xs-8">
            <?php echo form_input(array(
                'name' => 'expired_at',
                'class' => 'form-control input-sm',
                'id' => 'datepicker',
                'value' => (!empty($item_expiration_date_info->expired_at))? date("m/d/Y", strtotime($item_expiration_date_info->expired_at)): '',
                )); ?>
        </div>
    </div>

    <div class="form-group form-group-sm">
        <?php echo form_label($this->lang->line('item_expiration_dates_notes'), 'notes', array('class'=>'control-label col-xs-3')); ?>
        <div class='col-xs-8'>
            <?php echo form_textarea(array(
                    'name' => 'notes',
                    'id' => 'notes',
                    'class' => 'form-control input-sm',
                    'rows' => 2,
                    'value' => $item_expiration_date_info->notes)
            );?>
            <?php if (!empty($item_expiration_date_info->id)): ?>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="enabled" <?php if ($item_expiration_date_info->enabled) : ?>checked="checked"<?php endif; ?>>
                    <?php echo $this->lang->line('item_expiration_dates_enabled'); ?>
                </label>
            </div>
            <?php endif; ?>
        </div>
    </div>
</fieldset>

<?php echo form_close(); ?>

<script type="text/javascript">
    //validation and submit handling
    $(document).ready(function() {
        var fill_value = function(event, ui) {
            event.preventDefault();
        };


        $('#item_expiration_date_form').validate($.extend({
            submitHandler: function(form) {
                $(form).ajaxSubmit({
                    success: function(response)
                    {
                        dialog_support.hide();
                        table_support.handle_submit("<?php echo site_url($controller_name); ?>", response);
                    },
                    dataType: 'json'
                });
            },

            errorLabelContainer: '#error_message_box',

            rules:
                {
                    name: 'required'
                },

            messages:
                {
                    name: "<?php echo $this->lang->line('items_name_required'); ?>"
                }
        }, form_support.error));

        $( "#datepicker" ).datepicker();
    });
</script>
