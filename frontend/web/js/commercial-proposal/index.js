$(document).ready(function() {
    $('.filter-checkboxlist input').prop('checked', true);

    $('.check-all').change(function() {
        $(this).parent().parent().find('input').prop('checked', $(this).prop('checked'));
    });

    $('.filter-checkboxlist > div input').change(function() {
        if (!$(this).prop('checked')) {
            $(this).parent().parent().parent().find('.check-all').prop('checked', false);
        }
    });

    $('#reset_form').click(function(e) {
        e.preventDefault();
        $('input.form-control').val('');
        $('.filter-checkboxlist input').prop('checked', true);
        $('form').submit();
    });
});

$(document).on('pjax:complete', function() {
    $('#reset_form').click(function(e) {
        e.preventDefault();
        $('input.form-control').val('');
        $('.filter-checkboxlist input').prop('checked', true);
        $('form').submit();
    });

    $('.filter-checkboxlist').each(function (i, elem) {
        var check_all = true;

        $(elem).find('div input').each(function (i, elem) {
            check_all = check_all && $(elem).prop('checked');
        });

        $(elem).find('.check-all').prop('checked', check_all);
    });

    $('.check-all').change(function() {
        $(this).parent().parent().find('input').prop('checked', $(this).prop('checked'));
    });

    $('.filter-checkboxlist > div input').change(function() {
        if (!$(this).prop('checked')) {
            $(this).parent().parent().parent().find('.check-all').prop('checked', false);
        }
    });
});