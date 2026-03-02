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
    $btn.button('loading');

    $.ajax({
        url: '<?php echo $paydo_url; ?>',
        type: 'post',
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