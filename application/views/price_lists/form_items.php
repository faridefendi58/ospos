<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('price_lists/save/'.$price_list_info->id, array('id'=>'item_price_form', 'class'=>'form-horizontal')); ?>
<fieldset id="item_kit_basic_info">
    <div class="form-group form-group-sm">
        <?php echo form_label($this->lang->line('price_lists_unit_price'), 'unit_price', array('class'=>'required control-label col-xs-3')); ?>
        <div class='col-xs-8'>
            <?php echo form_input(array(
                    'name' => 'unit_price',
                    'id' => 'unit_price',
                    'class' => 'form-control input-sm',
                    'value' => $price_list_info->unit_price)
            );?>
        </div>
    </div>

    <div class="form-group form-group-sm">
        <?php echo form_label($this->lang->line('price_lists_item_name'), 'item_name', array('class'=>'required control-label col-xs-3')); ?>
        <div class='col-xs-8'>
            <?php echo form_input(array(
                    'name'=>'code',
                    'id'=>'code',
                    'class'=>'form-control input-sm',
                    'value'=>$price_list_info->item_name)
            );?>
        </div>
    </div>

</fieldset>

<?php echo form_close(); ?>

<script type="text/javascript">
    //validation and submit handling
    $(document).ready(function()
    {
        var fill_value = function(event, ui) {
            event.preventDefault();
            $("input[name='item_name']").val(ui.item.label);
        };


        $('#item_price_form').validate($.extend({
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
    });
</script>