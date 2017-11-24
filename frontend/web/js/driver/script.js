$('select#drivertrip-typeoftrip').change(function(event) {
    $('#listProducts').prop('hidden', true);
    $('input#drivertrip-weightorder').val('');
    if ($(this).val() && $(this).val() != 4) {
        $('.order_id').prop('hidden', false);
        $('.order_id div').addClass('required');
        $('.order_id input').prop('required', true);
        $('.order_id input').val('');
        if ($(this).val() == 1) var label = 'Номер заказа';
        if ($(this).val() == 2) var label = 'Номер закупки';
        if ($(this).val() == 3) var label = 'Номер перемещения';
        $('.order_id div label').text(label);
    }
    else {
        $('.order_id').prop('hidden', true);
        $('.order_id input').prop('required', false);
    }
});

$('.create_trip .order_id span.input-group-btn').click(function() {
    $('#listProducts').prop('hidden', false);
    var data = {
        id: $('input#drivertrip-ordernumber').val(),
        type: $('select#drivertrip-typeoftrip').val() || $('.create_trip #type_trip').text()
    };

    $.ajax({
        url: '/driver-trips/create',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (data) {
            $('.create_trip #listProducts table tbody').html('');
            $('.create_trip #listProducts table + div').remove();
            
            if (!$('#status').text()) {
                $('input#drivertrip-weightorder').val('');
                $('input#drivertrip-consignername').val('');
                $('input#drivertrip-consignerinn').val('');
                $('input#drivertrip-consignerphone').val('');
                $('input#drivertrip-consigneruser').val('');
                $('input#drivertrip-consigneruserphone').val('');
                $('input#drivertrip-consigneename').val('');
                $('input#drivertrip-consigneeinn').val('');
                $('input#drivertrip-consigneephone').val('');
                $('input#drivertrip-consigneeuser').val('');
                $('input#drivertrip-consigneeuserphone').val('');
            }
            
            if (data.goods.length == 0) {
                $('.create_trip #listProducts table').after(
                    '<div style="margin-bottom: 20px;"> <b>Внимание! Такого заказа не существует!!!</b> </div>'
                );
            } else {
                var weight = 0;
                data.goods.forEach(function (good) {
                    $('.create_trip #listProducts table tbody').append(
                        '<tr>' +
                            '<td>' + good.id + '</td>' +
                            '<td>' + good.name + '</td>' +
                            '<td>' + good.amount + '</td>' +
                            '<td>' + good.unit + '</td>' +
                        '</tr>'
                    );
                    if (good.weight) weight += good.amount * good.weight;
                });
                if (!$('#status').text()) {
                    if (weight) $('input#drivertrip-weightorder').val(Math.round(weight * 100) / 100);
                    $('input#drivertrip-consignername').val(data.info.consignerName);
                    $('input#drivertrip-consignerinn').val(data.info.consignerInn);
                    $('input#drivertrip-consignerphone').val(data.info.consignerPhone);
                    $('input#drivertrip-consigneruser').val(data.info.consignerUser);
                    $('input#drivertrip-consigneruserphone').val(data.info.consignerUserPhone);
                    $('input#drivertrip-consigneename').val(data.info.consigneeName);
                    $('input#drivertrip-consigneeinn').val(data.info.consigneeInn);
                    $('input#drivertrip-consigneephone').val(data.info.consigneePhone);
                    $('input#drivertrip-consigneeuser').val(data.info.consigneeUser);
                    $('input#drivertrip-consigneeuserphone').val(data.info.consigneeUserPhone);
                }
            }
        },
        error: function(xhr, status, error) {
          console.log('Ошибка: ', error);
        }
    });
});

if ($('#drivertrip-length').val()) {
    var volume = +$('#drivertrip-length').val().replace(',', '.') * 
                 +$('#drivertrip-width').val().replace(',', '.') *
                 +$('#drivertrip-height').val().replace(',', '.');
    $('.create_trip #volume b').text(Math.round(volume * 100) / 100);
}

$('.create_trip #size').change(function (event) {
    var volume = +$('#drivertrip-length').val().replace(',', '.') * 
                 +$('#drivertrip-width').val().replace(',', '.') *
                 +$('#drivertrip-height').val().replace(',', '.');
    $('.create_trip #volume b').text(Math.round(volume * 100) / 100);
});

$('select#drivertrip-from').change(function() {
    var data = { 
        id: $(this).val()
    };

    $.ajax({
        url: './create',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (data) {
            if (data.name != 'Другое') {
                $('input#drivertrip-adressfrom').val(data.address);
                $('input[name="isAddressFrom"]').val(1);
                $('select#drivertrip-zonefrom').find('option[value="'+data.region+'"]').prop('selected', true);
                var address = $('#drivertrip-adressfrom').val();
                ymaps.geocode(address).then(
                    function (res) {
                        if (!res.metaData.geocoder.found || !!res.metaData.geocoder.suggest) $('#drivertrip-adressfrom').val('');
                    },
                    function (err) {
                        alert('Ошибка!');
                    }
                );
            }
            else {
                $('input#drivertrip-adressfrom').val('');
                $('input[name="isAddressFrom"]').val(0);
                $('select#drivertrip-zonefrom option').prop('selected', false);
            }
        },
        error: function(xhr, status, error) {
          console.log('Ошибка: ', error);
        }
    });
});

$('#drivertrip-adressfrom').change(function() {
    var address = $(this).val();
    
    ymaps.geocode(address, {
        results: 1,
        json: true,
        // boundedBy: [[55.55945544545429, 37.13268871914181], [55.946698202860325, 38.085747336675574]],
        // strictBounds: true
    }).then(
        function (res) {
            if ( res.GeoObjectCollection.featureMember.length > 0 && 
                res.GeoObjectCollection.featureMember[0].GeoObject.metaDataProperty.GeocoderMetaData.kind == 'house' &&
                res.GeoObjectCollection.featureMember[0].GeoObject.metaDataProperty.GeocoderMetaData.precision == 'exact' &&
                !res.GeoObjectCollection.metaDataProperty.GeocoderResponseMetaData.suggest) {

                $('input[name="isAddressFrom"]').val(1);
                if ($('.field-drivertrip-adressfrom div').eq(0).hasClass('address-error')) {
                        $('.field-drivertrip-adressfrom div').eq(0).remove();
                        $('#drivertrip-adressfrom').removeClass('input-error');
                }
            }
            else {
                    $('input[name="isAddressFrom"]').val(0);
                    if (!$('.field-drivertrip-adressfrom div').hasClass('address-error')) {
                        $('.field-drivertrip-adressfrom').prepend('<div class="col-sm-9 col-sm-offset-2 address-error">Проверьте адрес!</div>');
                        $('#drivertrip-adressfrom').addClass('input-error');
                    }
            }
        },
        function (err) {
            alert('Ошибка доступа с Яндекс картам!');
        }
    );
});

$('#drivertrip-adressto').change(function() {
    var address = $(this).val();

    ymaps.geocode(address, {
        results: 1,
        json: true,
        // boundedBy: [[55.55945544545429, 37.13268871914181], [55.946698202860325, 38.085747336675574]],
        // strictBounds: true
    }).then(
        function (res) {
            if ( res.GeoObjectCollection.featureMember.length > 0 && 
                res.GeoObjectCollection.featureMember[0].GeoObject.metaDataProperty.GeocoderMetaData.kind == 'house' &&
                res.GeoObjectCollection.featureMember[0].GeoObject.metaDataProperty.GeocoderMetaData.precision == 'exact' &&
                !res.GeoObjectCollection.metaDataProperty.GeocoderResponseMetaData.suggest) {

                $('input[name="isAddressTo"]').val(1);
                if ($('.field-drivertrip-adressto div').eq(0).hasClass('address-error')) {
                        $('.field-drivertrip-adressto div').eq(0).remove();
                        $('#drivertrip-adressto').removeClass('input-error');
                }
            }
            else {
                    $('input[name="isAddressTo"]').val(0);
                    if (!$('.field-drivertrip-adressto div').hasClass('address-error')) {
                        $('.field-drivertrip-adressto').prepend('<div class="col-sm-9 col-sm-offset-2 address-error">Проверьте адрес!</div>');
                        $('#drivertrip-adressto').addClass('input-error');
                    }
            }
        },
        function (err) {
            alert('Ошибка доступа с Яндекс картам!');
        }
    );
});

$('select#drivertrip-to').change(function() {
    var data = { 
        id: $(this).val()
    };

    $.ajax({
        url: './create',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function (data) {
            if (data.name != 'Другое') {
                $('input#drivertrip-adressto').val(data.address);
                $('input[name="isAddressTo"]').val(1);
                $('select#drivertrip-zoneto').find('option[value="'+data.region+'"]').prop('selected', true);
                var address = $('#drivertrip-adressto').val();
                ymaps.geocode(address).then(
                    function (res) {
                        if (!res.metaData.geocoder.found || !!res.metaData.geocoder.suggest) $('#drivertrips-adressto').val('');
                    },
                    function (err) {
                        alert('Ошибка!');
                    }
                );
            }
            else {
                $('input#drivertrip-adressto').val('');
                $('input[name="isAddressTo"]').val(0);
                $('select#drivertrip-zoneto option').prop('selected', false);
            }

            if (data.tk && data.address) {
                $('div.field-drivertrip-terminaltc').addClass('required');
                $('input#drivertrip-terminaltc').prop('required', true);
                $('.address_tk').prop('hidden', false);
            } else {
                $('input#drivertrip-terminaltc').prop('required', false);
                $('.address_tk').prop('hidden', true);
            }
        },
        error: function(xhr, status, error) {
          console.log('Ошибка: ', error);
        }
    });
});

$('.create_trip input#drivertrip-consignerphone').mask('(999)999-9999');
$('.create_trip input#drivertrip-consigneruserphone').mask('(999)999-9999');
$('.create_trip input#drivertrip-consigneeuserphone').mask('(999)999-9999');
$('.create_trip input#drivertrip-consigneephone').mask('(999)999-9999');
$('.create_trip input#drivertrip-consignerinn').mask('9999999999?99');
$('.create_trip input#drivertrip-consigneeinn').mask('9999999999?99');

// Событие клик для кнопок ВСЕ.
$('div#typeTrip label:first-child input').click(function () {
    $('div#typeTrip label input').prop('checked', this.checked);
});

$('div#typeTrip label:not(:first-child) input').click(function () {
    if (!this.checked) $('div#typeTrip label:first-child input').prop('checked', false);
});

$('div#from label:first-child input').click(function () {
    $('div#from label input').prop('checked', this.checked);
});

$('div#from label:not(:first-child) input').click(function () {
    if (!this.checked) $('div#from label:first-child input').prop('checked', false);
});

$('div#to label:first-child input').click(function () {
    $('div#to label input').prop('checked', this.checked);
});

$('div#to label:not(:first-child) input').click(function () {
    if (!this.checked) $('div#to label:first-child input').prop('checked', false);
});

$('div#priority label:first-child input').click(function () {
    $('div#priority label input').prop('checked', this.checked);
});

$('div#priority label:not(:first-child) input').click(function () {
    if (!this.checked) $('div#priority label:first-child input').prop('checked', false);
});

$('div#region label:first-child input').click(function () {
    $('div#region label input').prop('checked', this.checked);
});

$('div#region label:not(:first-child) input').click(function () {
    if (!this.checked) $('div#region label:first-child input').prop('checked', false);
});

$('div#author label:first-child input').click(function () {
    $('div#author label input').prop('checked', this.checked);
});

$('div#author label:not(:first-child) input').click(function () {
    if (!this.checked) $('div#author label:first-child input').prop('checked', false);
});

$('div#status label:first-child input').click(function () {
    $('div#status label input').prop('checked', this.checked);
});

$('div#status label:not(:first-child) input').click(function () {
    if (!this.checked) $('div#status label:first-child input').prop('checked', false);
});

$('div#driver label:first-child input').click(function () {
    $('div#driver label input').prop('checked', this.checked);
});

$('div#driver label:not(:first-child) input').click(function () {
    if (!this.checked) $('div#driver label:first-child input').prop('checked', false);
});

$('div#car label:first-child input').click(function () {
    $('div#car label input').prop('checked', this.checked);
});

$('div#car label:not(:first-child) input').click(function () {
    if (!this.checked) $('div#car label:first-child input').prop('checked', false);
});

function getGoods(id, element) {
    var content = 
        '<table class="table table-bordered">' +
            '<thead>' +
                '<tr>' +
                    '<th>id</th>' +
                    '<th>Наименование</th>' +
                    '<th>Кол-во</th>' +
                    '<th>Ед.изм.</th>' +
                '</tr>' +
            '</thead>' +
            '<tbody>';

    $.ajax({
        url: '/driver-trips/index',
        type: 'POST',
        data: {id: id},
        dataType: 'json',
        success: function (data) {
            if (data.goods.length == 0) {
                content = '<b>Данный заказ пуст!!!</b>';
            } else {
                data.goods.forEach(function (good) {
                    content +=
                        '<tr>' +
                            '<td>' + good.id + '</td>' +
                            '<td>' + good.name + '</td>' +
                            '<td>' + good.amount + '</td>' +
                            '<td>' + good.unit + '</td>' +
                        '</tr>'

                });
                content += '</tbody></table>';
            }

            $(element).popover({
                html: true,
                placement: 'right',
                content: content
            });
            $(element).popover('show');
        },
        error: function(xhr, status, error) {
          console.log('Ошибка: ', error);
        }
    });
}

$('.driver-trips-index table tbody tr').click(function(event) {
    if ($(event.target).hasClass('glyphicon-shopping-cart')) {

        if ( !$(event.target).attr('aria-describedby') ) {
            var tripId = $(this).find('td:eq(1) div:eq(0)').text();
            getGoods(tripId, event.target);
        }
    }
});

$('.driver-trips-index table tbody').click(function(event) {
    if ($(event.target).attr('type') == 'checkbox') {
        var quantity = $('.driver-trips-index table tbody tr').find('input:checkbox:checked').length;
        if ( quantity > 0) {
            if ($(event.target).prop('checked')) {
                var volume = +$('.driver-trips-index .create_trip_ticket span.volume').text() + 
                    +$('.driver-trips-index table tbody tr[data-key="'+$(event.target).val()+'"]').find('td:eq(5) div:eq(1) span').text();
                var weight = +$('.driver-trips-index .create_trip_ticket span.weight').text() + 
                    +$('.driver-trips-index table tbody tr[data-key="'+$(event.target).val()+'"]').find('td:eq(5) div:eq(2) span').text();
            } else {
                var volume = +$('.driver-trips-index .create_trip_ticket span.volume').text() - 
                    +$('.driver-trips-index table tbody tr[data-key="'+$(event.target).val()+'"]').find('td:eq(5) div:eq(1) span').text();
                var weight = +$('.driver-trips-index .create_trip_ticket span.weight').text() - 
                    +$('.driver-trips-index table tbody tr[data-key="'+$(event.target).val()+'"]').find('td:eq(5) div:eq(2) span').text();
            }

            $('.driver-trips-index .create_trip_ticket span.quantity').text(quantity);
            $('.driver-trips-index .create_trip_ticket span.volume').text(volume);
            $('.driver-trips-index .create_trip_ticket span.weight').text(weight);

            $('.driver-trips-index div.create_trip_ticket').prop('hidden', false);
            $('.driver-trips-index div.create_trip_ticket').next().removeClass('col-sm-12').addClass('col-sm-2');
        } else  {
            $('.driver-trips-index div.create_trip_ticket').prop('hidden', true);
            $('.driver-trips-index div.create_trip_ticket').next().removeClass('col-sm-2').addClass('col-sm-12');
            $('.driver-trips-index .create_trip_ticket span.volume').text(0);
            $('.driver-trips-index .create_trip_ticket span.weight').text(0);
        }
    }
});

$('.driver-trip-tickets-create table tbody tr').click(function (event) {
    if ($(event.target).hasClass('glyphicon-shopping-cart')) {

        if ( !$(event.target).attr('aria-describedby') ) {
            var tripId = $(this).find('td:eq(2) div:eq(0) a').text();
            getGoods(tripId, event.target);
        }
    }

    if ($(event.target).hasClass('glyphicon-remove')) {
        var data = {
            type: 0,
            id: $(this).find('td:eq(2) div:eq(0) a').text()
        }
    }

    if ($(event.target).hasClass('glyphicon-arrow-up')) {
        var data = {
            type: 1,
            id: $(this).find('td:eq(2) div:eq(0) a').text()
        }
    }

    if ($(event.target).hasClass('glyphicon-arrow-down')) {
        var data = {
            type: 2,
            id: $(this).find('td:eq(2) div:eq(0) a').text()
        }
    }
    
    if (data) {
        var tr = this;

        $.ajax({
            url: 'create',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data.remove) $(tr).remove();

                if (data.up == 'success') {
                    var number = +$(tr).find('td:eq(1)').text();
                    $(tr).find('td:eq(1)').empty();
                    $(tr).find('td:eq(1)').text(number-1);
                    $(tr).prev().find('td:eq(1)').empty();
                    $(tr).prev().find('td:eq(1)').text(number);
                    $($(tr).prev()).before(tr);
                }
                if (data.down == 'success') {
                    var number = +$(tr).find('td:eq(1)').text();
                    $(tr).find('td:eq(1)').empty();
                    $(tr).find('td:eq(1)').text(number+1);
                    $(tr).next().find('td:eq(1)').empty();
                    $(tr).next().find('td:eq(1)').text(number);
                    $($(tr).next()).after(tr);
                }
            },
            error: function(xhr, status, error) {
                console.log('Ошибка: ', error);
            }
        });
    }
});

$('.driver-trip-tickets-update table tbody tr').click(function (event) {
    if ($(event.target).hasClass('glyphicon-shopping-cart')) {

        if ( !$(event.target).attr('aria-describedby') ) {
            var tripId = $(this).find('td:eq(2) div:eq(0) a').text();
            getGoods(tripId, event.target);
        }
    }

    if ($(event.target).hasClass('glyphicon-remove')) {
        var data = {
            type: 0,
            id: $(this).find('td:eq(2) div:eq(0) a').text()
        }
    }

    if ($(event.target).hasClass('glyphicon-arrow-up')) {
        var data = {
            type: 1,
            id: $(this).find('td:eq(2) div:eq(0) a').text()
        }
    }

    if ($(event.target).hasClass('glyphicon-arrow-down')) {
        var data = {
            type: 2,
            id: $(this).find('td:eq(2) div:eq(0) a').text()
        }
    }

    if (data) {
        var tr = this;

        $.ajax({
            url: 'create',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(data) {
                if (data.remove) $(tr).remove();

                if (data.up == 'success') {
                    var number = +$(tr).find('td:eq(1)').text();
                    $(tr).find('td:eq(1)').empty();
                    $(tr).find('td:eq(1)').text(number-1);
                    $(tr).prev().find('td:eq(1)').empty();
                    $(tr).prev().find('td:eq(1)').text(number);
                    $($(tr).prev()).before(tr);
                }
                if (data.down == 'success') {
                    var number = +$(tr).find('td:eq(1)').text();
                    $(tr).find('td:eq(1)').empty();
                    $(tr).find('td:eq(1)').text(number+1);
                    $(tr).next().find('td:eq(1)').empty();
                    $(tr).next().find('td:eq(1)').text(number);
                    $($(tr).next()).after(tr);
                }
            },
            error: function(xhr, status, error) {
                console.log('Ошибка: ', error);
            }
        });
    }
});

$('.driver-trip-tickets-update table button[name="yes"]').click(function(event) {
    $('.driver-trip-tickets-update #modalYes button[name="yes_success"]').val($(this).val());
    $('.driver-trip-tickets-update #modalYes').modal('show');
});

$('.driver-trip-tickets-update table button[name="no"]').click(function(event) {
    $('.driver-trip-tickets-update #modalNo button[name="no_success"]').val($(this).val());
    $('.driver-trip-tickets-update #modalNo').modal('show');
});

function printTripTicket(title) {
   $('#print').prop('hidden', true);
   $('.print .box-header').prepend('<h3>' + title + '</h3>');
   $('.container-fluid').addClass('no-padding');
   $('.content').addClass('no-padding');
   $('.content table').addClass('text-print');
   $('.content table td').addClass('padding-td');
   $('.content .box-header').addClass('text-print');
   $('.duration').css('text-decoration', 'none');
   print();
   $('.print .box-header h3').remove();
   $('#print').prop('hidden', false);
   $('.container-fluid').removeClass('no-padding');
   $('.content').removeClass('no-padding');
   $('.content table').removeClass('text-print');
   $('.content table td').removeClass('padding-td');
   $('.content .box-header').removeClass('text-print');
   $('.duration').css('text-decoration', 'underline');
};

if ( isNaN( $('.duration').eq(0).text() ) ) {
    var myMap;
    // Дождёмся загрузки API и готовности DOM.
    ymaps.ready(init);
    function init () {
        var referencePoints = [];
        $('.print').find('.address').each(function() {
            referencePoints.push($(this).text());
        });

        var multiRoute = new ymaps.multiRouter.MultiRoute({
            // Описание опорных точек мультимаршрута.
            referencePoints: referencePoints,
            // Параметры маршрутизации.
            params: {
                // Ограничение на максимальное количество маршрутов, возвращаемое маршрутизатором.
                results: 1,
                avoidTrafficJams: true
            }
        }, {
            // Автоматически устанавливать границы карты так, чтобы маршрут был виден целиком.
            boundsAutoApply: true
        });
        // Создание экземпляра карты и его привязка к контейнеру с заданным id ("map").
        myMap = new ymaps.Map('map', {
            center: [55.7515, 37.6189], // Москва
            zoom: 10
        });

        myMap.geoObjects.add(multiRoute);

        multiRoute.model.events.add("requestsuccess", function(){
            var data = {};
            for (var i = 0; i < referencePoints.length-1; i++) {
                var routeLength = multiRoute.getActiveRoute() ? multiRoute.getActiveRoute().getPaths().get(i).properties.get('distance').value : 0;
                var timeRoute =  multiRoute.getActiveRoute() ? multiRoute.getActiveRoute().getPaths().get(i).properties.get('durationInTraffic').value : 0;
                $('.distance').eq(i).text(Math.floor(routeLength/1000));
                $('.duration').eq(i).text(Math.floor(timeRoute/60));
                data[i] = {
                    num: i+1,
                    addressStart: referencePoints[i],
                    addressFinish: referencePoints[i+1],
                    duration: Math.floor(timeRoute/60),
                    distance: Math.floor(routeLength/1000)
                };
            }

            culcTime(0);
            if (window.location.search.split('=')[2] == 'yes')
                $.ajax({
                    url: 'print?id=' + window.location.search.split('&')[0].split('=')[1],
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
                    },
                    error: function(xhr, status, error) {
                        console.log('Ошибка: ', error);
                    }
                });
        });
    }
} else culcTime(0);

$('.duration').click(function(event) {
    $(this).next().find('input[name="duration"]').val($(this).text());
    $(this).next().prop('hidden', false);
});
$('.print button[name="duration"]').click(function(event) {
    $(this).parent().prev().text($(this).prev().val());
    $(this).parent().prop('hidden', true);
    var index = $('.duration').index($(this).parent().prev());
    culcTime(index);

    if (window.location.search.split('=')[2] == 'yes') {
        var data = {
            action: 'change',
            num: index+1,
            duration: $(this).prev().val()
        }

        $.ajax({
            url: 'print?id=' + window.location.search.split('&')[0].split('=')[1],
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(data) {
                console.log(data);
            },
            error: function(xhr, status, error) {
                console.log('Ошибка: ', error);
            }
        });
    }

});

function culcTime(index) {
    $('.print').find('.time_in').each(function(ind, value) {
        if (ind >= index) {
            var timeoutOld = $('.time_out').eq(ind).text().split(':');
            var timeout = +timeoutOld[0] * 3600 + +timeoutOld[1] * 60;
            var duration = +$('.duration').eq(ind).text() * 60;
            var timeLoad = +$('.time_load').eq(ind).text() * 60;
            var timeoutNew = timeout + duration + timeLoad;
            var h = Math.floor(timeoutNew / 3600) < 10 ? '0'+Math.floor(timeoutNew / 3600) : Math.floor(timeoutNew / 3600);
            var m = Math.floor((timeoutNew % 3600)/60) < 10 ? '0'+Math.floor((timeoutNew % 3600)/60) : Math.floor((timeoutNew % 3600)/60);

            $(this).text($('.time_out').eq(ind).text());
            $('.time_out').eq(ind + 1).text(h + ':' + m);
        }
    });
    $('.time_in').eq($('.time_in').length-1).text($('.time_out').eq($('.time_in').length-1).text());
    var timeStart = $('.time_start').eq(0).text().split(':');
    var timeStartNum = +timeStart[0]*60 + +timeStart[1];
    var timeFinish = $('.time_out').eq($('time_out').length-1).text().split(':');
    var timeFinishNum = +timeFinish[0]*60 + +timeFinish[1];
    var durationAll = timeFinishNum - timeStartNum;
    var h = Math.floor(durationAll / 60) < 10 ? '0'+Math.floor(durationAll / 60) : Math.floor(durationAll / 60);
    var m = Math.floor(durationAll % 60) < 10 ? '0'+Math.floor(durationAll % 60) : Math.floor(durationAll % 60);
    $('.duration_all').text(h + ':' + m);
    var distanceAll = 0;
    var points = 0;
    $('.distance').each(function() {
        distanceAll += +$(this).text();
        if (+$(this).text() != 0) points++;
    });
    $('.distance_all').text(distanceAll);
    $('.points_all').text(points);
}
