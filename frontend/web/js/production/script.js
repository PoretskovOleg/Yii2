$(document).ready(function() {
    $('form').keydown(function(event){
        if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
    });

    $('.production-order-search .selection').click(function(event) {
        if ($(event.target).prop('type') == 'checkbox' && $(event.target).val() == 0) {
            var checked = $(event.target).prop('checked');
            $(event.target).parent().parent().find('input[type="checkbox"]').each(function() {
                $(this).prop('checked', checked);
            })
        }

        if ($(event.target).prop('type') == 'checkbox' && $(event.target).val() != 0 && !$(event.target).prop('checked')) {
            $(event.target).parent().parent().find('input[value="0"]').prop('checked', false);
        }

    });

    $('.production-order-form .search-goods').click(function(event) {
        var data = {orderNumber: $('.production-order-form #orderNumber').val()}
        if (data.orderNumber) {
            $.ajax({
                url: 'create',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(data) {
                    $('.production-order-form #listGoods table tbody').empty();
                    $('.production-order-form #listGoods').prop('hidden', false);
                    var tr = '';
                    if (data.goods.length > 0)
                        data.goods.map(function(good) {
                            tr += '<tr>' + 
                                '<td>' + good.id + '<input name="ids[]" value="' + good.id + '" hidden></td>' +
                                '<td>' + good.name + '<input name="position_' + good.id + '" value="' + good.position + '" hidden></td>' +
                                '<td>' + 
                                    '<input type="number" step="1" name="countOrder_' + good.id + '" value="' + good.amount + '" min="1" max="' + good.amount + '" style="width: 100%; height: 23px;">' +
                                '</td>' +
                                '<td>' + good.unit + '</td>' +
                                '<td>' +
                                    '<select name="typeGood_' + good.id + '" style="height: 23px;">' +
                                        '<option value="1">Типовое</option>' +
                                        '<option value="2">Индивидуальное</option>' +
                                    '</select>' +
                                '</td>' +
                                '<td><span class="glyphicon glyphicon-remove"></span></td>'
                            '</tr>';
                        });
                    else tr +='<td colspan="6" class="text-center">Ничего не найдено</td>';
                    $('.production-order-form #listGoods table tbody').append(tr);
                    if (data.dedline) {
                        $('.production-order-form div.dedline input').val(data.dedline);
                    } else {
                        $('.production-order-form div.dedline input').val('');
                        $('.production-order-form .dedline-enabled').prop('hidden', false);
                        $('.production-order-form .dedline-disabled').prop('hidden', true);
                    }
                },
                error: function(xhr, status, error) {
                  console.log('Ошибка: ', error);
                }
            });
        }
    });

    $('.production-order-form #productionorder-target').click(function(event) {
        if ($(event.target).val() == 1) {
            $('.production-order-form #order').prop('hidden', false);
            $('.production-order-form #catalog').prop('hidden', true);
            $('.production-order-form #another').prop('hidden', true);
            $('.production-order-form #another input').val('');
            $('.production-order-form .dedline-enabled').prop('hidden', true);
            $('.production-order-form .dedline-disabled').prop('hidden', false);
            $('.production-order-form #listStockGoods table tbody').empty();
            $('.production-order-form #listStockGoods').prop('hidden', true);
        } else if ($(event.target).val() == 2) {
            $('.production-order-form #order').prop('hidden', true);
            $('.production-order-form #catalog').prop('hidden', false);
            $('.production-order-form #another').prop('hidden', true);
            $('.production-order-form #another input').val('');
            $('.production-order-form .dedline-enabled').prop('hidden', false);
            $('.production-order-form .dedline-disabled').prop('hidden', true);
            $('.production-order-form #listGoods table tbody').empty();
            $('.production-order-form #orderNumber').val('')
            $('.production-order-form #listGoods').prop('hidden', true);
        } else if ($(event.target).val() == 3) {
            $('.production-order-form #order').prop('hidden', true);
            $('.production-order-form #catalog').prop('hidden', true);
            $('.production-order-form #another').prop('hidden', false);
            $('.production-order-form .dedline-enabled').prop('hidden', false);
            $('.production-order-form .dedline-disabled').prop('hidden', true);
            $('.production-order-form #listGoods table tbody').empty();
            $('.production-order-form #orderNumber').val('')
            $('.production-order-form #listGoods').prop('hidden', true);
            $('.production-order-form #listStockGoods table tbody').empty();
            $('.production-order-form #listStockGoods').prop('hidden', true);
        }
    });

    $('.production-order-form table').click(function(event) {
        if ($(event.target).hasClass('glyphicon-remove'))
            $(event.target).parent().parent().remove();
    });

    $('.production-order-form .good-search-input').off('change').change(function() {
        $('#good_search_selected_page').val('0');
    });

    $('.goods-pagination-link').off('click').click(function (e) {
        e.preventDefault();
        var page = $(this).data('page');
        $('#good_search_selected_page').val(page);
        $('#good_search_form').submit();
    });

    $('.production-order-form #add_material').click(function (event) {
        var data = {
                id: $(event.target).attr('data-good-id'),
            };
        if (data.id) {
            if ($('.production-order-form #listStockGoods').prop('hidden')) {
                $('.production-order-form #listStockGoods').prop('hidden', false);
            }
            data.amount = $(event.target).parent().prev().find('input').val();
            data.name = $(event.target).parent().parent().find('td.good-name').text();
            data.unit = $(event.target).parent().parent().find('td.good-avm-stock-balance').text().replace(/\d/g, '').trim();

            tr = '<tr>' + 
                '<td>' + data.id + '<input name="ids[]" value="' + data.id + '" hidden></td>' +
                '<td>' + data.name + '</td>' +
                '<td>' + data.unit + '</td>' +
                '<td><input type="number" name="countStock_' + data.id + '" value="' + data.amount + '" min="1" step="1" style="width: 100%; height: 23px;"></td>' +
                '<td>' +
                    '<select name="typeGood_' + data.id + '" style="height: 23px;">' +
                        '<option value="1">Типовое</option>' +
                        '<option value="2">Индивидуальное</option>' +
                    '</select>' +
                '</td>' +
                '<td><span class="glyphicon glyphicon-remove"></span></td>'
            '</tr>';
            $('.production-order-form #listStockGoods table tbody').append(tr);
        }
    });

    $('.production-order-form div.buttons button[name="delete"]').click(function(e) {
        e.preventDefault();
        if (confirm('Вы действительно хотите удалить этот заказ-наряд?'))
            $('.production-order-form div.buttons button[name="delete"]').submit();
    });

    $('.production-order-index table tbody td button.prepare').click(function(event) {
        event.preventDefault();
        if (+$(this).data('id')) {
            $('.production-order-index #modalPrepareOrder button[name="approved"]').data('id', +$(this).data('id'));
            $('.production-order-index #modalPrepareOrder .modal-header h4').text('Вы подтверждаете проверку ' + $(this).attr('data-name') + '?');
            $('.production-order-index #modalPrepareOrder').modal('show');
        }
    });

    $('.production-order-index #modalPrepareOrder button[name="approved"]').click(function(event) {
        event.preventDefault();
        if (+$(this).data('id'))
            $.ajax({
                url: 'index',
                type: 'POST',
                data: {id: +$(this).data('id')},
                dataType: 'json',
                success: function(data) {
                    if (data.prepare == 'success')
                        $('.production-order-index table tbody tr[data-key="' + data.id + '"] button[data-id="' + $(event.target).data('id') + '"]')
                            .removeClass('btn-danger').addClass('btn-success');
                    if (data.canDo) {
                        $('.production-order-index table tbody')
                            .find('tr[data-key="' + data.id + '"] div.status')
                            .removeClass('yellow')
                            .addClass('pink')
                            .text('Можно делать');
                    }
                    $('.production-order-index #modalPrepareOrder').modal('hide');
                },
                error: function(xhr, status, error) {
                  console.log('Ошибка: ', error);
                }
            });
    });

    $('.production-order-index table tbody .see-comment').click(function (event) {
        $('.production-order-index #view_comments_tbody').empty();
        $.ajax({
            url: 'index',
            type: 'POST',
            data: {id: +$(this).attr('data-id'), type: 'comment'},
            dataType: 'json',
            success: function(data) {
                var tr = '';
                if (data.length > 0) {
                    data.map(function(comment) {
                        tr += '<tr>' +
                            '<td>' + comment.date + '</td>' +
                            '<td>' + comment.author + '</td>' +
                            '<td>' + comment.comment + '</td>' +
                        '</tr>';
                    });
                } else tr = '<tr><td class="text-center" colspan="3">Ничего не найдено</td></tr>';
                $('.production-order-index #view_comments_tbody').append(tr);
                $('.production-order-index #commentOrder form .modal-body input[name="id"]').val($(event.target).attr('data-id'));
                $('.production-order-index #commentOrder').modal('show');
            },
            error: function(xhr, status, error) {
                console.log('Ошибка: ', error)
            }
        });
    });

    $('.production-order-index table tbody select').change(function(event) {
        var data = {
            type: 'sequence',
            sequence: $(this).val(),
            id: $(event.target).parent().parent().data('key')
        }

        $.ajax({
            url: 'index',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(data) {
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.log('Ошибка: ', error)
            }
        });
    });

    $('.production-order-form #btn-planning').click(function() {
        $('.production-order-form #planning').prop('hidden', false);
        $('.production-order-form #planning input#productionorder-isplanning').val(1);
    });

    $('.production-order-form span.glyphicon-trash').click(function(event) {
        var id = event.target.id.split('_')[1];

        $.ajax({
            url: 'update?id=' + window.location.search.split('=')[1],
            type: 'POST',
            data: {id: id},
            dataType: 'json',
            success: function(data) {
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.log('Ошибка: ', error)
            }
        });
    });

});

$(document).ready(function() {
    $(document).on('pjax:complete', function() {

        $('.production-order-form .good-search-input').off('change').change(function() {
            $('#good_search_selected_page').val('0');
        });

        $('.goods-pagination-link').off('click').click(function (e) {
            e.preventDefault();
            var page = $(this).data('page');
            $('#good_search_selected_page').val(page);
            $('#good_search_form').submit();
        });

    });
});