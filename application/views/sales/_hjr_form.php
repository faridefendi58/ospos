<div class="form-group">
    <label for="no_faktur" class="control-label">Data Resep (Optional)</label>
    <!--<input type="text" name="no_faktur" class="form-control input-sm" id="no_faktur" placeholder="No. Faktur">-->
</div>
<div class="form-group">
    <input type="text"
           name="nama_pasien"
           class="form-control input-sm"
           id="nama-pasien"
           placeholder="Nama Pasien"
           value="<?php echo (isset($partners['nama_pasien']))? $partners['nama_pasien'] : ''; ?>">
</div>
<div class="form-group">
    <input type="text"
           name="nama_dokter"
           class="form-control input-sm"
           id="nama-dokter"
           placeholder="Nama Dokter"
           value="<?php echo (isset($partners['nama_dokter']))? $partners['nama_dokter'] : ''; ?>">
</div>
<div class="clearfix" style="margin-bottom: 10px;"></div>

<script type="text/javascript">
    $(function () {
        $("#nama-pasien").autocomplete(
            {
                source: "<?php echo site_url("sales/suggest_patient"); ?>",
                minChars: 0,
                delay: 10,
                select: function (a, ui) {
                    $(this).val(ui.item.value);
                    setCookie('sales_patient', ui.item.value, 1);
                }
            });
        $("#nama-dokter").autocomplete(
            {
                source: "<?php echo site_url("sales/suggest_doctor"); ?>",
                minChars: 0,
                delay: 10,
                select: function (a, ui) {
                    $(this).val(ui.item.value);
                    setCookie('sales_doctor', ui.item.value, 1);
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