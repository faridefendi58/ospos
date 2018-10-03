<div class="form-group">
    <label for="nama-dokter" class="control-label">Data Dokter (Optional)</label>
    <input type="text"
           name="nama_dokter"
           class="form-control input-sm"
           id="nama-dokter"
           placeholder="Nama Dokter"
           value="<?php echo (isset($partners['nama_dokter']))? $partners['nama_dokter'] : ''; ?>">
</div>
<div class="form-group">
    <input type="text"
           name="alamat_dokter"
           class="form-control input-sm"
           id="alamat-dokter"
           placeholder="Alamat Dokter"
           value="<?php echo (isset($partners['alamat_dokter']))? $partners['alamat_dokter'] : ''; ?>">
</div>
<div class="clearfix" style="margin-bottom: 10px;"></div>

<script type="text/javascript">
    $(function () {
        $("#nama-dokter").autocomplete(
            {
                source: "<?php echo site_url("sales/suggest_doctor"); ?>",
                minChars: 0,
                delay: 10,
                select: function (a, ui) {
                    $(this).val(ui.item.value);
                    setCookie('sales_doctor', ui.item.value, 1);
                    if (ui.item.address.length > 0) {
                        $('#alamat-dokter').val(ui.item.address);
                        setCookie('sales_doctor_address', ui.item.address);
                    }
                }
            });
        $('#alamat-dokter').blur(function () {
            if ($(this).val().length > 0) {
                setCookie('sales_doctor_address', $(this).val());
            }
        });
    });
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*60*60*1000));
        var expires = "expires="+ d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }
</script>