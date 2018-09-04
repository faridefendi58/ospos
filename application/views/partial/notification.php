<div class="row ml-auto pull-right hide" id="notification-container"
     style="position:fixed; top: 50px !important; right: 25px !important;z-index:999;opacity: 0.95;">
    <div class="alert alert-dismissable alert-template hide">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <strong>Warning!</strong> <a id="text-message" href="#" data-dismiss="alert" style="color: white;"></a>
    </div>
    <div class="alert-group" style="width:100%" id="notif-alert-group">
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
                    var i; var j =0; var u_class = new Array();
                    console.log("Length :"+ data.messages.length);
                    for (i = 0; i < data.messages.length; i++) {
                        var unique_class = data.messages[i]['id']+"-"+i;
                        var a_dismisable = $("#notification-container").find('div.alert-template');
                        if (u_class.includes(unique_class) == false) {
                            u_class.push(unique_class);
                            a_dismisable
                                .clone()
                                .removeClass('hide')
                                .removeClass('alert-template')
                                .addClass(""+data.messages[i]['class_name'])
                                .appendTo( "#notif-alert-group" )
                                .find("#text-message")
                                .html(data.messages[i]['msg']).attr('href', data.messages[i]['href']);

                            var dismis = $('.alert-group').find('div.alert-dismissable:last-child')
                                .find('a[href="'+ data.messages[i]['href'] +'"]')
                                .parent();

                            dismis.find('button.close').attr('id', data.messages[i]['id']);
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
            window.location.href = $(this).attr('href');
            //return false;
        });
        $('#notif-alert-group').find('button.close').click(function () {
            var id = $(this).attr('id');
            $.ajax({
                type: 'POST',
                url: "<?php echo site_url('item_expiration_dates/close_notification'); ?>/"+id,
                dataType: 'json',
                data: {'id':id},
                success: function(data) {
                    if (data.success == 1) { }
                }
            });
        });
    }
</script>