<?php if ($methods_mode) { ?>
<fieldset id="paydo-methods" style="margin-bottom:15px;">
    <legend style="font-size:16px;margin-bottom:10px;"><?php echo $text_choose_method; ?></legend>
    <?php if ($hosted_page_enabled) { ?>
    <label style="display:flex;align-items:center;gap:10px;padding:9px 10px;margin:6px 0;border:1px solid #ddd;border-radius:6px;cursor:pointer;">
        <input type="radio" name="paydo_method" value="hosted" style="margin:0;"/>
        <span><strong><?php echo $text_hosted_page; ?></strong><br/><small><?php echo $text_hosted_page_description; ?></small></span>
    </label>
    <?php } ?>
    <?php foreach ($payment_methods as $method) { ?>
    <label style="display:flex;align-items:center;gap:10px;padding:9px 10px;margin:6px 0;border:1px solid #ddd;border-radius:6px;cursor:pointer;">
        <input type="radio" name="paydo_method" value="<?php echo htmlspecialchars((string)$method['identifier'], ENT_QUOTES, 'UTF-8'); ?>" style="margin:0;"/>
        <?php if (!empty($method['logo'])) { ?>
        <img src="<?php echo htmlspecialchars((string)$method['logo'], ENT_QUOTES, 'UTF-8'); ?>" alt="" style="height:24px;width:auto;object-fit:contain;"/>
        <?php } ?>
        <span><?php echo htmlspecialchars((string)$method['title'], ENT_QUOTES, 'UTF-8'); ?></span>
    </label>
    <?php } ?>
</fieldset>
<?php } ?>

<div class="buttons">
    <div class="pull-right">
        <button type="button" id="button-paydo" class="btn btn-primary" data-loading-text="<?php echo $button_pay; ?>">
            <?php echo $button_pay; ?>
        </button>
    </div>
</div>

<script type="text/javascript">
$('#button-paydo').on('click', function () {
    var $btn = $(this);
    var selectedMethod = $('input[name="paydo_method"]:checked').val() || '';

    <?php if ($methods_mode) { ?>
    if (!selectedMethod) {
        alert(<?php echo json_encode($error_choose_method); ?>);
        return;
    }
    <?php } ?>

    $btn.button('loading');

    $.ajax({
        url: '<?php echo $paydo_url; ?>',
        type: 'post',
        data: { paydo_method: selectedMethod },
        dataType: 'json',
        success: function (json) {
            if (json && json.redirect) {
                location = json.redirect;
                return;
            }

            $btn.button('reset');
            alert((json && json.error) ? json.error : 'Payment error');
        },
        error: function () {
            $btn.button('reset');
            alert('Payment error');
        }
    });
});
</script>
