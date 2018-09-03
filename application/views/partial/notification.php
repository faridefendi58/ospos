<div class="row ml-auto pull-right hide" id="notification-container"
     style="position:fixed; top: 50px !important; right: 25px !important;z-index:999;opacity: 0.95;">
    <div class="alert-group" style="width:100%" id="notif-alert-group">
        <div class="alert alert-warning alert-dismissable">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <strong>Warning!</strong> <a id="text-message" href="#" data-dismiss="alert" style="color: white;"></a>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $.ajax({
            type: 'GET',
            url: "<?php echo site_url('item_expiration_dates/warning'); ?>",
            dataType: 'json',
            success: function(data) {
                if (data.success == 1) {
                    var i; var j =0;
                    for (i = 0; i < data.messages.length; i++) {
                        if (i > 0) {
                            $("#notification-container").find('.alert-dismissable').clone()
                                .appendTo( "#notif-alert-group" )
                                .find("#text-message")
                                .html(data.messages[i]['msg']).attr('href', data.messages[i]['href']);
                            j = j+1;
                        } else {
                            $("#notification-container")
                                .find("#text-message")
                                .html(data.messages[i]['msg'])
                                .attr('href', data.messages[i]['href']);
                            j = j+1;
                        }
                    }
                    if (j > 0) {
                        $( "#notification-container" ).removeClass("hide");
                        dismising();
                    }
                }
            }
        });
    });
    function dismising() {
        $('a[id="text-message"]').click(function () {
            $(this).parent().find("button").trigger("click");
            return false;
        });
    }
</script>