<div class="form-group">
    <label for="nama-dokter" class="control-label">Data Dokter (Optional)</label>
    <input type="text"
           name="partner_code"
           class="form-control input-sm"
           id="partner-code"
           placeholder="ID Dokter (Kosongi untuk input data baru)"
           value="<?php echo (isset($partners['partner_code']))? $partners['partner_code'] : ''; ?>">
</div>
<div class="form-group">
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
<div class="clearfix" style="margin-bottom: 20px;"></div>

<script type="text/javascript">
    $(function () {
        var sales_doctor = 0;
        $("#nama-dokter").autocomplete(
            {
                source: "<?php echo site_url("sales/suggest_doctor"); ?>",
                minChars: 0,
                delay: 10,
                select: function (a, ui) {
                    $(this).val(ui.item.value);
                    setCookie('sales_doctor', ui.item.value, 1);
                    sales_doctor = 1;
                    if (ui.item.address.length > 0) {
                        $('#alamat-dokter').val(ui.item.address);
                        setCookie('sales_doctor_address', ui.item.address);
                    }
                }
            });
        $("#partner-code").autocomplete(
            {
                source: "<?php echo site_url("sales/suggest_doctor"); ?>?code=1",
                minChars: 0,
                delay: 10,
                select: function (a, ui) {
                    $(this).val(ui.item.value);
                    setCookie('partner_code', ui.item.value, 1);
                    setCookie('sales_doctor', ui.item.doctor_name, 1);
                    $('#nama-dokter').val(ui.item.doctor_name);
                    sales_doctor = 1;
                    if (ui.item.address.length > 0) {
                        $('#alamat-dokter').val(ui.item.address);
                        setCookie('sales_doctor_address', ui.item.address);
                    }
                }
            });
        $('#nama-dokter').blur(function () {
            if ($(this).val().length > 0 && sales_doctor == 0) {
                setCookie('sales_doctor', $(this).val());
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