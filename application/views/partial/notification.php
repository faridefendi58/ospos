<div class="bg-warning hide" id="notification-container">
    <div class="container">
        <div class="alert alert-dismissible alert-warning">
            <button type="button" class="close" data-dismiss="alert">×</button>
            <p id="text-message"></p>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $.get( "ajax/test.html", function( data ) {
            $( "#notification-container" ).html( data );
        });
        $.ajax({
            type: 'GET',
            url: "<?php echo site_url('item_expiration_dates/warning'); ?>",
            dataType: 'json',
            success: function(data) {
                if (data.success == 1) {
                    $( "#notification-container" ).find("#text-message").html( data.message );
                    $( "#notification-container" ).removeClass("hide");
                }
            }
        });
    });
</script>