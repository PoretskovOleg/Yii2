$(document).ready(function() {
    window.pdf_url = false;
    updateListeners();
    $(document).on("pjax:complete", function() {
        updateListeners();
        if (window.open_pdf) {
            window.open_pdf = false;
            window.open(window.pdf_url);
        }
    });
});

function updateListeners() {
    $('#invoicetemplate-organization').change(function() {
        $('#invoicetemplate-signer').html('');
        $('#invoicetemplate-signer').prop('disabled', true);
        if ($(this).val()) {
            $.ajax({
                type: 'get',
                dataType: 'json',
                url: 'get-signers',
                data: {'organization_id': $(this).val()},
                success: function (data) {
                    data.forEach(function (item) {
                        $('#invoicetemplate-signer').append('<option value="' + item.id + '">' + item.name + '</option>');
                        $('#invoicetemplate-signer').prop('disabled', false);
                    });
                },
            });
        }
    });

    $("#get_pdf_button").click(function() {
        window.open_pdf = true;
        $("#invoice-template_submit").trigger("click");
    });
}



