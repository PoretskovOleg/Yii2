$(document).ready(function() {
    $('.tech-dep-difficulty table tbody tr.calc-tr').change(function(event) {
        var sum = 0;
        $(this).find('input').each(function() {
            if ($(this).prop('name') != 'project[]')
            sum += +$(this).val();
        });
        $(this).find('td.project input').val(sum);
    });

    $('.tech-dep-project-search .selection').click(function(event) {
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

    $('.tech-dep-project-form select#techdepproject-type').change(function() {
        if ($(this).val() == 2)
            $('.tech-dep-project-form div.changes').prop('hidden', false);
        else $('.tech-dep-project-form div.changes').prop('hidden', true);
        if ($(this).val() == 4) {
            $('.tech-dep-project-form div.goodId').prop('hidden', true);
            $('.tech-dep-project-form div.goodName').prop('hidden', false);
        } else {
            $('.tech-dep-project-form div.goodId').prop('hidden', false);
            $('.tech-dep-project-form div.goodName').prop('hidden', true);
        }
        var difficulty = $('.tech-dep-project-form select#techdepproject-difficulty').val();
        if (difficulty > 0 && difficulty < 6) {
            var data = {
                difficulty: difficulty,
                type: $(this).val()
            };

            $.ajax({
                url: 'create',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (data) {
                    $('input#techdepproject-dedline').val(data.date);
                },
                error: function(xhr, status, error) {
                  console.log('Ошибка: ', error);
                }
            });
        } else {
            $('input#techdepproject-dedline').val('');
        }
    });

    $('.field-techdepproject-ordernumber span.input-group-btn').click(function() {
        $('select#techdepproject-goodid').empty();
        $('select#techdepproject-goodid').append('<option></option>');
        var data = {
            id: $('input#techdepproject-ordernumber').val(),
            type: $('select#techdepproject-type').val(),
            project: window.location.search ? window.location.search.split('=')[1] : 0
        };

        console.log(data);

        if (data.type && data.type != 4)
            $.ajax({
                url: 'create',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (data) {
                    data.map(function (good) {
                        $('select#techdepproject-goodid').append('<option value = "' + good.id + '">' + good.name + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                  console.log('Ошибка: ', error);
                }
            });
    });

    $('select#techdepproject-difficulty').change(function () {
        if ($(this).val() > 0 && $(this).val() < 6) {
            var data = {
                difficulty: $(this).val(),
                type: $('select#techdepproject-type').val() || 0
            };

            $.ajax({
                url: 'create',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (data) {
                    $('input#techdepproject-dedline').val(data.date);
                    if ($('.tech-dep-project-planning table tr').hasClass('dedline-time')) {
                        $('.tech-dep-project-planning table tr.dedline-time').empty();
                        $('.tech-dep-project-planning table tr.pure-time').empty();
                        trDedline = '<td>Дедлайн</td>';
                        trPure = '<td>Чистое</td>';
                        for (var key in data.dedlineTime) {
                            trDedline += '<td>' + data.dedlineTime[key] + '</td>';
                            trPure += '<td>' + data.pureTime[key] + '</td>';
                        }
                        trDedline += '<td class="total-dedline-setup"></td>';
                        trPure += '<td class="total-pure-setup"></td>';
                        $('.tech-dep-project-planning table tr.dedline-time').append(trDedline);
                        $('.tech-dep-project-planning table tr.pure-time').append(trPure);

                        $('.tech-dep-project-planning div#stagesProject input').each(function () {
                            if ($(this).prop('checked')) {
                                $('.tech-dep-project-planning table tr.dedline-time td').eq($(this).val()).addClass('choice');
                                $('.tech-dep-project-planning table tr.pure-time td').eq($(this).val()).addClass('choice');
                                $('.tech-dep-project-planning table tr.dedline-plan td input[name="dedline_' + $(this).val() + '"]').parent().addClass('choice');
                                $('.tech-dep-project-planning table tr.pure-plan td input[name="pure_' + $(this).val() + '"]').parent().addClass('choice');
                            }
                        });

                        var totalDedlineSetup = 0;
                        var totalPureSetup = 0;
                        $('.tech-dep-project-planning table tr.pure-time td.choice').each(function () {
                            totalPureSetup += +$(this).text();
                        });
                        $('.tech-dep-project-planning table tr.dedline-time td.choice').each(function () {
                            if (totalDedlineSetup < +$(this).text()) totalDedlineSetup = +$(this).text();
                        });

                        $('.total-dedline-setup').text(totalDedlineSetup ? totalDedlineSetup : '');
                        $('.total-pure-setup').text(totalPureSetup ? totalPureSetup : '');
                        if (+$('.total-dedline-plan').text() > totalDedlineSetup) $('.total-dedline-plan').addClass('red-text');
                        else $('.total-dedline-plan').removeClass('red-text');
                        if (+$('.total-pure-plan').text() > totalPureSetup) $('.total-pure-plan').addClass('red-text');
                        else $('.total-pure-plan').removeClass('red-text');
                    }
                },
                error: function(xhr, status, error) {
                  console.log('Ошибка: ', error);
                }
            });
        } else if ($('.tech-dep-project-planning table tr').hasClass('dedline-time')) {
            $('.tech-dep-project-planning table tr.dedline-time').empty();
            $('.tech-dep-project-planning table tr.pure-time').empty();
            trDedline = '<td>Дедлайн</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
            trPure = '<td>Чистое</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>';
            trDedline += '<td class="total-dedline-setup"></td>';
            trPure += '<td class="total-pure-setup"></td>';
            $('.tech-dep-project-planning table tr.dedline-time').append(trDedline);
            $('.tech-dep-project-planning table tr.pure-time').append(trPure);
        }

        $('#difficultyModal').modal('show');
    });

    $('#btn-difficulty').click(function() {
        if ($('#difficulty_comment').val()) {
            $.ajax({
                url: 'planning?id=' + window.location.search.split('=')[1],
                type: 'POST',
                data: {comment: $('#difficulty_comment').val(), difficulty: $('select#techdepproject-difficulty').val()},
                dataType: 'json',
                success: function(data) {
                    $('#difficulty_comment').val('');
                    $('#difficultyModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    console.log('Ошибка: ', error);
                }
            });
            
        }
    });

    $('.tech-dep-project-planning div#stagesProject input').each(function () {
        if ($(this).prop('checked')) {
            $('.tech-dep-project-planning table tr.dedline-time td').eq($(this).val()).addClass('choice');
            $('.tech-dep-project-planning table tr.pure-time td').eq($(this).val()).addClass('choice');
            $('.tech-dep-project-planning table tr.dedline-plan td input[name="dedline_' + $(this).val() + '"]').parent().addClass('choice');
            $('.tech-dep-project-planning table tr.pure-plan td input[name="pure_' + $(this).val() + '"]').parent().addClass('choice');
        }
    });

    var totalDedlinePlan = 0;
    var totalPurePlan = 0;
    $('.tech-dep-project-planning table tr.dedline-plan td.choice input').each(function () {
        if (totalDedlinePlan < +$(this).val()) totalDedlinePlan = +$(this).val();
    });
    $('.tech-dep-project-planning table tr.pure-plan td.choice input').each(function () {
        totalPurePlan += +$(this).val();
    });

    $('.total-dedline-plan').text(totalDedlinePlan ? totalDedlinePlan : '');
    $('.total-pure-plan').text(totalPurePlan ? totalPurePlan : '');

    if ($('select#techdepproject-difficulty').val() > 0 && $('select#techdepproject-difficulty').val() < 6) {
        var totalDedlineSetup = 0;
        var totalPureSetup = 0;
        $('.tech-dep-project-planning table tr.pure-time td.choice').each(function () {
            totalPureSetup += +$(this).text();
        });
        $('.tech-dep-project-planning table tr.dedline-time td.choice').each(function () {
            if (totalDedlineSetup < +$(this).text()) totalDedlineSetup = +$(this).text();
        });

        $('.total-dedline-setup').text(totalDedlineSetup ? totalDedlineSetup : '');
        $('.total-pure-setup').text(totalPureSetup ? totalPureSetup : '');

        if (totalDedlinePlan > totalDedlineSetup) $('.total-dedline-plan').addClass('red-text');
        else $('.total-dedline-plan').removeClass('red-text');
        if (totalPurePlan > totalPureSetup) $('.total-pure-plan').addClass('red-text');
        else $('.total-pure-plan').removeClass('red-text');
    }

    $('.tech-dep-project-planning div#stagesProject').change(function(event) {
        $('.tech-dep-project-planning table tbody tr.stages input[value="'+ $(event.target).val() +'"]').prop('checked', $(event.target).prop('checked'));
        if ($(event.target).prop('checked')) {
            $('.tech-dep-project-planning table tr.dedline-time td').eq($(event.target).val()).addClass('choice');
            $('.tech-dep-project-planning table tr.pure-time td').eq($(event.target).val()).addClass('choice');
            $('.tech-dep-project-planning table tr.dedline-plan td input[name="dedline_' + $(event.target).val() + '"]').parent().addClass('choice');
            $('.tech-dep-project-planning table tr.pure-plan td input[name="pure_' + $(event.target).val() + '"]').parent().addClass('choice');
        } else {
            $('.tech-dep-project-planning table tr.dedline-plan td input[name="dedline_' + $(event.target).val() + '"]').parent().removeClass('choice');
            $('.tech-dep-project-planning table tr.pure-plan td input[name="pure_' + $(event.target).val() + '"]').parent().removeClass('choice');
            $('.tech-dep-project-planning table tr.dedline-time td').eq($(event.target).val()).removeClass('choice');
            $('.tech-dep-project-planning table tr.pure-time td').eq($(event.target).val()).removeClass('choice');
        }

        var totalDedlinePlan = 0;
        var totalPurePlan = 0;
        $('.tech-dep-project-planning table tr.dedline-plan td.choice input').each(function () {
            if (totalDedlinePlan < +$(this).val()) totalDedlinePlan = +$(this).val();
        });
        $('.tech-dep-project-planning table tr.pure-plan td.choice input').each(function () {
            totalPurePlan += +$(this).val();
        });

        $('.total-dedline-plan').text(totalDedlinePlan ? totalDedlinePlan : '');
        $('.total-pure-plan').text(totalPurePlan ? totalPurePlan : '');

        if ($('select#techdepproject-difficulty').val() > 0 && $('select#techdepproject-difficulty').val() < 6) {
            var totalDedlineSetup = 0;
            var totalPureSetup = 0;
            $('.tech-dep-project-planning table tr.pure-time td.choice').each(function () {
                totalPureSetup += +$(this).text();
            });
            $('.tech-dep-project-planning table tr.dedline-time td.choice').each(function () {
                if (totalDedlineSetup < +$(this).text()) totalDedlineSetup = +$(this).text();
            });

            $('.total-dedline-setup').text(totalDedlineSetup ? totalDedlineSetup : '');
            $('.total-pure-setup').text(totalPureSetup ? totalPureSetup : '');

            if (totalDedlinePlan > totalDedlineSetup) $('.total-dedline-plan').addClass('red-text');
            else $('.total-dedline-plan').removeClass('red-text');
            if (totalPurePlan > totalPureSetup) $('.total-pure-plan').addClass('red-text');
            else $('.total-pure-plan').removeClass('red-text');
        }
    });

    $('.tech-dep-project-planning table tr.stages').change(function (event) {
        $('.tech-dep-project-planning div#stagesProject input[value="'+ $(event.target).val() +'"]').prop('checked', $(event.target).prop('checked'));
        if ($(event.target).prop('checked')) {
            $('.tech-dep-project-planning table tr.dedline-time td').eq($(event.target).val()).addClass('choice');
            $('.tech-dep-project-planning table tr.pure-time td').eq($(event.target).val()).addClass('choice');
            $('.tech-dep-project-planning table tr.dedline-plan td input[name="dedline_' + $(event.target).val() + '"]').parent().addClass('choice');
            $('.tech-dep-project-planning table tr.pure-plan td input[name="pure_' + $(event.target).val() + '"]').parent().addClass('choice');
        } else {
            $('.tech-dep-project-planning table tr.dedline-plan td input[name="dedline_' + $(event.target).val() + '"]').parent().removeClass('choice');
            $('.tech-dep-project-planning table tr.pure-plan td input[name="pure_' + $(event.target).val() + '"]').parent().removeClass('choice');
            $('.tech-dep-project-planning table tr.dedline-time td').eq($(event.target).val()).removeClass('choice');
            $('.tech-dep-project-planning table tr.pure-time td').eq($(event.target).val()).removeClass('choice');
        }
        
        var totalDedlinePlan = 0;
        var totalPurePlan = 0;
        $('.tech-dep-project-planning table tr.dedline-plan td.choice input').each(function () {
            if (totalDedlinePlan < +$(this).val()) totalDedlinePlan = +$(this).val();
        });
        $('.tech-dep-project-planning table tr.pure-plan td.choice input').each(function () {
            totalPurePlan += +$(this).val();
        });

        $('.total-dedline-plan').text(totalDedlinePlan ? totalDedlinePlan : '');
        $('.total-pure-plan').text(totalPurePlan ? totalPurePlan : '');

        if ($('select#techdepproject-difficulty').val() > 0 && $('select#techdepproject-difficulty').val() < 6) {
            var totalDedlineSetup = 0;
            var totalPureSetup = 0;
            $('.tech-dep-project-planning table tr.pure-time td.choice').each(function () {
                totalPureSetup += +$(this).text();
            });
            $('.tech-dep-project-planning table tr.dedline-time td.choice').each(function () {
                if (totalDedlineSetup < +$(this).text()) totalDedlineSetup = +$(this).text();
            });

            $('.total-dedline-setup').text(totalDedlineSetup ? totalDedlineSetup : '');
            $('.total-pure-setup').text(totalPureSetup ? totalPureSetup : '');

            if (totalDedlinePlan > totalDedlineSetup) $('.total-dedline-plan').addClass('red-text');
            else $('.total-dedline-plan').removeClass('red-text');
            if (totalPurePlan > totalPureSetup) $('.total-pure-plan').addClass('red-text');
            else $('.total-pure-plan').removeClass('red-text');
        }
    });

    $('.tech-dep-project-planning table tr.dedline-plan').change(function (event) {
        var totalDedlinePlan = 0;
        $(this).find('td.choice input').each(function () {
            if (totalDedlinePlan < +$(this).val()) totalDedlinePlan = +$(this).val();
        });
        $('.total-dedline-plan').text(totalDedlinePlan ? totalDedlinePlan : '');
        if ($('select#techdepproject-difficulty').val() > 0 && $('select#techdepproject-difficulty').val() < 6) {
            if (totalDedlinePlan > +$('.total-dedline-setup').text()) $('.total-dedline-plan').addClass('red-text');
            else $('.total-dedline-plan').removeClass('red-text');
        }
    });

    $('.tech-dep-project-planning table tr.pure-plan').change(function (event) {
        var totalPurePlan = 0;
        $(this).find('td.choice input').each(function () {
            totalPurePlan += +$(this).val();
        });
        $('.total-pure-plan').text(totalPurePlan ? totalPurePlan : '');
        if ($('select#techdepproject-difficulty').val() > 0 && $('select#techdepproject-difficulty').val() < 6) {
            if (totalPurePlan > +$('.total-pure-setup').text()) $('.total-pure-plan').addClass('red-text');
            else $('.total-pure-plan').removeClass('red-text');
        }
    });

    $('.tech-dep-project-form span.glyphicon-trash').click(function(event) {
        if (event.target.id.split('_')[1] == 'stage') {
            var url = 'stage?project=1&stage=1';
            var id = event.target.id.split('_')[2];
        } else {
            var url = 'update?id=' + window.location.search.split('=')[1];
            var id = event.target.id.split('_')[1];
        }
        $.ajax({
            url: url,
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

    $('.tech-dep-project-stage span.glyphicon-trash').click(function(event) {
        $.ajax({
            url: 'stage?project=' + window.location.search.split('&')[0].split('=')[1] + '&stage=' + window.location.search.split('&')[1].split('=')[1],
            type: 'POST',
            data: {id: event.target.id.split('_')[1]},
            dataType: 'json',
            success: function(data) {
                window.location.reload();
            },
            error: function(xhr, status, error) {
                console.log('Ошибка: ', error)
            }
        });
    });

    $('.tech-dep-project-index .see-comment').click(function (event) {
        $('.tech-dep-project-index #view_comments_tbody').empty();
        $.ajax({
            url: 'index',
            type: 'POST',
            data: {id: event.target.id.split('_')[1]},
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
                } else tr = '<tr><td colspan="3">Ничего не найдено</td></tr>';
                $('.tech-dep-project-index #view_comments_tbody').append(tr);
                $('.tech-dep-project-index #commentProject').modal('show');
            },
            error: function(xhr, status, error) {
                console.log('Ошибка: ', error)
            }
        });

    });

    $('.tech-dep-project-reference #add_material').click(function (event) {
        var data = {
                id: $(event.target).attr('data-good-id'),
            };
        if (data.id) {
            data.q = $(event.target).parent().prev().find('input').val();
            data.name = $(event.target).parent().parent().find('td.good-name').text();
            data.unit = $(event.target).parent().parent().find('td.good-avm-stock-balance').text().replace(/\d/g, '').trim();

            $.ajax({
                url: 'reference?project=' + window.location.search.split('=')[1],
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.type == 'add') {
                        var tr = 
                        '<tr>' +
                            '<td class="text-center">' +
                                '<span class="glyphicon glyphicon-arrow-up" style="margin-right: 5px"></span>' +
                                '<span class="glyphicon glyphicon-arrow-down" style="margin-right: 5px"></span>' +
                                '<span class="glyphicon glyphicon-pencil" style="margin-right: 5px"></span>' +
                                '<span class="glyphicon glyphicon-remove"></span>' +
                            '</td>' +
                            '<td class="position">' + data.material.position + '</td>' +
                            '<td class="id">' + data.material.id + '</td>' +
                            '<td class="name">' + data.material.name + '</td>' +
                            '<td>' + data.material.unit + '</td>' +
                            '<td class="quantity">' + data.material.quantity +'</td>' +
                            '<td class="price">' + data.material.price.toFixed(2).replace(/(\D)/g, ",").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1 ") +'</td>' +
                            '<td class="sum">' + ((data.material.quantity * data.material.price).toFixed(2).replace(/(\D)/g, ",").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1 ")) +'</td>' +
                        '</tr>';
                        $('.tech-dep-project-reference table.data-table tbody').append(tr);
                    } else if (data.type == 'edit') {
                        $('.tech-dep-project-reference table.data-table tbody').find('td.id').each(function() {
                            if (+$(this).text() == data.id) {
                                $(this).parent().find('td.quantity').text(data.quantity);
                                $(this).parent().find('td.sum').text((data.quantity * data.price).toFixed(2).replace(/(\D)/g, ",").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1 "));
                            }
                        })
                    }
                    $('.tech-dep-project-reference span.total').text(calcSumReference());
                },
                error: function(xhr, status, error) {
                    console.log('Ошибка: ', error)
                }
            });
        }
    });

    $('.tech-dep-project-reference table.data-table tbody').click(function(event) {
        var tr = $(event.target).parent().parent();
        var table = this;
        if ($(event.target).hasClass('glyphicon-remove')) {
            if (confirm("Вы действительно хотите удалить позицию " + $(event.target).parent().parent().find('td.name').text())) {
                var id = +$(event.target).parent().parent().find('td.id').text();
                var position = +$(event.target).parent().parent().find('td.position').text();
                var data = {
                    type: 'remove',
                    id: id,
                    position: position
                }
            }
        }
        if ($(event.target).hasClass('glyphicon-arrow-up')) {
            var id = +$(event.target).parent().parent().find('td.id').text();
            var position = +$(event.target).parent().parent().find('td.position').text();
            var data = {
                type: 'up',
                id: id,
                position: position
            }
        }
        if ($(event.target).hasClass('glyphicon-arrow-down')) {
            var id = +$(event.target).parent().parent().find('td.id').text();
            var position = +$(event.target).parent().parent().find('td.position').text();
            var data = {
                type: 'down',
                id: id,
                position: position
            }
        }
        if ($(event.target).hasClass('glyphicon-pencil')) {
            var quantity = (tr).find('td.quantity').text();
            $(tr).find('td.quantity').empty().append(
                '<input type="number" min="0" step="any" value="' + quantity + '" style="width: 50px"> <button class="btn btn-sm btn-success change-value">ОК</button>'
                );
        }
        if ($(event.target).hasClass('change-value')) {
            var data = {
                q: $(event.target).prev().val(),
                id: +$(event.target).parent().parent().find('td.id').text(),
                type: 'edit'
            }
        }

        if (data)
            $.ajax({
                url: 'reference?project=' + window.location.search.split('=')[1],
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (data) {
                    if (data.remove == 'success') {
                        $(event.target).parent().parent().remove();
                        $(table).find('td.position').each(function() {
                            if (+$(this).text() > position) {
                                $(this).text(+$(this).text() - 1);
                            }
                        });
                        $('.tech-dep-project-reference span.total').text(calcSumReference());
                    }
                    if (data.up == 'success') {
                        $(tr).find('td.position').empty();
                        $(tr).find('td.position').text(position - 1);
                        $(tr).prev().find('td.position').empty();
                        $(tr).prev().find('td.position').text(position);
                        $($(tr).prev()).before(tr);
                    }
                    if (data.down == 'success') {
                        $(tr).find('td.position').empty();
                        $(tr).find('td.position').text(position + 1);
                        $(tr).next().find('td.position').empty();
                        $(tr).next().find('td.position').text(position);
                        $($(tr).next()).after(tr);
                    }
                    if (data.edit == 'success') {
                        $(event.target).parent().parent().find('td.sum').text((data.quantity * data.price).toFixed(2).replace(/(\D)/g, ",").replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1 "));
                        $(event.target).parent().empty().text(data.quantity);
                        $('.tech-dep-project-reference span.total').text(calcSumReference());
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Ошибка: ', error)
                }
            });
    });

    function calcSumReference() {
        var sum = 0;
        $('.tech-dep-project-reference table.data-table tbody').find('td.sum').each(function() {
            sum += parseFloat($(this).text().replace(' ', '').replace(',', '.'));
        });
        var price = sum.toFixed(2);
            price = price.replace(/(\D)/g, ",");
            price = price.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1 ");

        return price;
    }

    $('.tech-dep-project-reference .good-search-input').change(function() {
        $('#good_search_selected_page').val('0');
    });

    $('.goods-pagination-link').off('click').click(function (e) {
        e.preventDefault();
        var page = $(this).data('page');
        $('#good_search_selected_page').val(page);
        $('#good_search_form').submit();
    });

    $('.tech-dep-project-form div.buttons button[name="delete"]').click(function(e) {
        e.preventDefault();
        if (confirm('Вы действительно хотите удалить этот проект?'))
            $('.tech-dep-project-form div.buttons button[name="delete"]').submit();
    });
});

$(document).ready(function() {
    $(document).on('pjax:complete', function() {

        $('.tech-dep-project-reference .good-search-input').off('change').change(function() {
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