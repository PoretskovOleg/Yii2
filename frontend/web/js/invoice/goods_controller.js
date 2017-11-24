$(document).ready(function () {
    $(document).on('pjax:success', '#goods', function() {
        $('.good-select-button').off('click').click(function() {
            var good_id = $(this).data("good-id");
            var good_count = $('#good-count-input-' + good_id).val();

            if (good_id) {
                goods_model.addGood(good_id, good_count);
                $('#add_good_modal').modal('toggle');
            }
        });
    });
});