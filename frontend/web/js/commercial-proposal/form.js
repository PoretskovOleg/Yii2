$(document).ready(function() {
    window.goods_model = new CatalogGoodsModel();

    window.additional_goods_model = new AdditionalGoodsModel();
    window.additional_goods_model.init();

    window.totals_model = new TotalsModel();

    window.parseDecimal = function(value) {
        value = value.replace(',', '.');
        return parseFloat(value);
    };

    window.toDecimalString = function(value) {
        value = value.toFixed(2);
        return value.replace('.', ',');
    };

    updateListeners();
    $(document).off('pjax:complete').on('pjax:complete', function() {
        if (window.open_pdf) {
            window.open_pdf = false;
            window.open(window.pdf_url);
        }
        updateListeners();
    });
});

function updateListeners() {
    window.totals_model.onReload = function(amount) {
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: 'get-template-id',
            data: {'subject_id': $('#commercialproposal-subject').val(), 'amount': amount},
            success: function (data) {
                if (data) {
                    $('#commercialproposal-template').val(data);
                    $('#fake_template').val(data);
                    $('#fake_template').trigger('change');
                } else {
                    $('#commercialproposal-template').val('');
                    $('#fake_template').val('');
                    $('#fake_template').trigger('change');
                }
            }
        });
    };

    $('#add_additional_good_button').off('click').click(function() {
        var form = $('#add_additional_good_form');

        var item = {
            name: form.find('.name').first().val(),
            quantity: parseInt(form.find('.quantity').first().val()),
            unit_id: parseInt(form.find('.unit').first().val()),
            delivery_period: parseInt(form.find('.period').first().val()),
            price: parseFloat(form.find('.price').first().val().replace(',', '.')),
            end_price: parseFloat(form.find('.end-price').first().val().replace(',', '.'))
        };

        window.additional_goods_model.addItem(item);
        $('#add_additional_good_modal').modal('toggle');
        form[0].reset();
    });

    $('#send_email_button').off('click').on('click', function() {
        $('#send_email_modal').modal('show');
    });

    $('#sendEmail').off('pjax:beforeSend').on('pjax:beforeSend', function() {
        $("#send_email_overlay").show();
    });

    $('#sendEmail').off('pjax:complete').on('pjax:complete', function() {
        if ($('#change_status_from_email').length) {
            $('#new_status_input').val($('#change_status_from_email').val());
            $('#commercialproposal_submit').trigger('click');
        } else {
            $.pjax.reload('#commercial_proposal_edit');
        }
    });

    $('#commercial_proposal_edit').off('pjax:complete').on('pjax:complete', function() {
        if ($('#comments_block').length) {
            $.pjax.reload('#comments_block');
        }
    });


    $('#add_comment_form').off('pjax:complete').on('pjax:complete', function() {
        if (jQuery('#commercialproposalcomment-deadline').data('datetimepicker')) { jQuery('#commercialproposalcomment-deadline').datetimepicker('destroy'); }
        jQuery('#commercialproposalcomment-deadline-datetime').datetimepicker(datetimepicker_7b09a251);
        if ($('#comments_block').length) {
            $.pjax.reload('#comments_block');
        }
    });

    $('.btn-status').off('click').click(function() {
        var status_id = $(this).data('status');
        $('#new_status_input').val(status_id);
        $('#commercialproposal_submit').trigger('click');
    });

    var delivery_value = $('#commercialproposal-delivery').val();
    if (delivery_value === '0') {
        $('#select_delivery').hide();
        $('#select_stock').show();
    } else if (delivery_value === '1') {
        $('#select_delivery').show();
        $('#select_stock').hide();
    }

    $('#commercialproposal-delivery').off('change').change(function() {
        var val = $(this).val();

        if (val === '0') {
            $('#select_delivery').hide();
            $('#select_stock').show();
        } else if (val === '1') {
            $('#select_delivery').show();
            $('#select_stock').hide();
        }
    });

    $('#commercialproposal-subject').off('change').change(function() {
        var amount = window.totals_model.total + window.totals_model.tax;
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: 'get-template-id',
            data: {'subject_id': $('#commercialproposal-subject').val(), 'amount': amount},
            success: function (data) {
                if (data) {
                    $('#commercialproposal-template').val(data);
                    $('#fake_template').val(data);
                    $('#fake_template').trigger('change');
                } else {
                    $('#commercialproposal-template').val('');
                    $('#fake_template').val('');
                    $('#fake_template').trigger('change');
                }
            }
        });
    });

    $("#attachment_add_button").off('click').click(function(e) {
        e.preventDefault();
        $('#file_upload_row').clone().appendTo($("#attachments_table_body"));
    });

    $('.contractor-search-input').off('change').change(function() {
        $('#selected_page').val('0');
    });

    $('.pagination-link').off('click').click(function (e) {
        e.preventDefault();
        var page = $(this).data('page');
        $('#selected_page').val(page);
        $('#contractor_search_form').submit();
    });

    $('#fake_signer').off('change').change(function() {
        $("#commercialproposal-signer").val($(this).val());
    });

    $('#fake_organization').off('change').change(function() {
        $("#commercialproposal-organization").val($(this).val());
        $('#fake_signer').html('');
        if ($(this).val()) {
            $.ajax({
                type: 'get',
                dataType: 'json',
                url: 'get-signers',
                data: {'organization_id': $(this).val()},
                success: function (data) {
                    data.forEach(function (item) {
                        $('#fake_signer').append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                },
            });
        }
    });

    $('#fake_template').off('change').change(function() {
        $("#commercialproposal-template").val($(this).val());
        $('#fake_signer').html('');
        if ($(this).val()) {
            $.ajax({
                type: 'get',
                dataType: 'json',
                url: 'get-template-data',
                data: {'template_id': $(this).val()},
                success: function (data) {
                    $('#fake_organization').val(data.organization_id);
                    $('#commercialproposal-organization').val(data.organization_id);

                    data.signers.forEach(function (item) {
                        $('#fake_signer').append('<option value="' + item.id + '">' + item.name + '</option>');
                    });

                    $('#fake_signer').val(data.signer_id);
                    $('#commercialproposal-signer').val(data.signer_id);

                    $('#commercialproposal-prepayment_percentage').val(data.prepayment_percentage);
                    $('#commercialproposal-term_days').val(data.term_days);
                    $('#commercialproposal-delivery_stock').val(data.delivery_stock);
                    CKEDITOR.instances['commercialproposal-note'].setData(data.note);
                    $('#commercialproposal-attachments_pages_ids').val(data.attachments_pages_ids).trigger('change.select2');
                },
            });
        } else {
            $('#fake_organization').val('');
            $('#commercialproposal-organization').val('');

            $('#fake_signer').val('');
            $('#commercialproposal-signer').val('');

            $('#commercialproposal-prepayment_percentage').val('');
            $('#commercialproposal-term_days').val('');
            CKEDITOR.instances['commercialproposal-note'].setData('');
            $('#commercialproposal-attachments_pages_ids').val('').trigger('change.select2');
        }
    });

    $('.contractor-select-btn').off('click').click(function() {
        var contractor_id = $(this).data("contractor-id");

        $('#commercialproposal-payer').html('');
        $('#commercialproposal-contact_person').html('');

        if (contractor_id) {
            $.ajax({
                type: 'get',
                dataType: 'json',
                url: 'get-contractor-info',
                data: {'contractor_id': contractor_id},
                success: function (data) {
                   $('#commercialproposal-contractor').val(data.contractor.contractor_id);
                   $('#contractor_name').val(data.contractor.contractor_name);

                    data.payers.forEach(function (item) {
                        $('#commercialproposal-payer').append('<option value="' + item.id + '">' + item.name + '</option>');
                        $('#commercialproposal-payer').prop('disabled', false);

                        $('.organizations-radio-' + contractor_id).each(function(index, elem) {
                            if ($(elem).is(':checked')) {
                                var organization_id = $(elem).val();
                                if (organization_id) {
                                    $("#commercialproposal-payer").val(organization_id);
                                }
                            }
                        });
                    });

                    data.contact_persons.forEach(function (item) {
                        $('#commercialproposal-contact_person').append('<option value="' + item.id + '">' + item.name + '</option>');
                        $('#commercialproposal-contact_person').prop('disabled', false);

                        $('.contact_persons-radio-' + contractor_id).each(function(index, elem) {
                            if ($(elem).is(':checked')) {
                                var contact_person_id = $(elem).val();
                                if (contact_person_id) {
                                    $("#commercialproposal-contact_person").val(contact_person_id);
                                }
                            }
                        });
                    });

                    $('#contractor_modal').modal('toggle');
                },
            });
        }
    });

    $("#get_pdf_button").off('click').click(function() {
        if ($('#commercialproposal_submit').length) {
            window.open_pdf = true;
            $('#commercialproposal_submit').trigger("click");
        } else {
            window.open(window.pdf_url);
        }
    });

    $('.good-search-input').off('change').change(function() {
        $('#good_search_selected_page').val('0');
    });

    $('.goods-pagination-link').off('click').click(function (e) {
        e.preventDefault();
        var page = $(this).data('page');
        $('#good_search_selected_page').val(page);
        $('#good_search_form').submit();
    });
}

