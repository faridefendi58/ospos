<div id="required_fields_message"><?php echo $this->lang->line('common_fields_required_message'); ?></div>

<ul id="error_message_box" class="error_message_box"></ul>

<?php echo form_open('price_lists/save/'.$price_list_info->id, array('id'=>'item_price_form', 'class'=>'form-horizontal')); ?>
	<fieldset id="item_kit_basic_info">
		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('price_lists_name'), 'name', array('class'=>'required control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_input(array(
						'name'=>'name',
						'id'=>'name',
						'class'=>'form-control input-sm',
						'value'=>$price_list_info->name)
						);?>
			</div>
		</div>

        <div class="form-group form-group-sm">
            <?php echo form_label($this->lang->line('price_lists_code'), 'code', array('class'=>'required control-label col-xs-3')); ?>
            <div class='col-xs-8'>
                <?php echo form_input(array(
                        'name'=>'code',
                        'id'=>'code',
                        'class'=>'form-control input-sm',
                        'value'=>$price_list_info->code)
                );?>
            </div>
        </div>

		<div class="form-group form-group-sm">
			<?php echo form_label($this->lang->line('price_lists_description'), 'description', array('class'=>'control-label col-xs-3')); ?>
			<div class='col-xs-8'>
				<?php echo form_textarea(array(
						'name'=>'description',
						'id'=>'description',
						'class'=>'form-control input-sm',
						'rows' => 3,
						'value'=>$price_list_info->description)
						);?>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="enabled" <?php if ($price_list_info->enabled) : ?>checked="checked"<?php endif; ?>> <?php echo $this->lang->line('price_lists_enabled'); ?>
                    </label>
                </div>
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
