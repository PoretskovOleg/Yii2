function AdditionalGoodsModel() {
    this.container_selector = '#additional_goods_table';
    this.result_input_selector = '#additional_goods_storage';

    this.items = [];
    this.initReload = false;

    this.initLoadedItem = function(item) {
        item.quantity = parseInt(item.quantity);
        item.price = parseFloat(item.price);
        item.margin_percent = parseFloat(item.margin_percent);
        item.end_price = parseFloat(item.end_price);

        item.margin = (item.end_price - item.price) * item.quantity;

        item.total = item.end_price * item.quantity;
    };

    this.calculateItem = function(item) {
        if (!item.name) item.name = '(нет)';
        if (!item.quantity) item.quantity = 0;
        if (!item.delivery_period) item.delivery_period = 0;
        if (!item.price) item.price = 0;
        if (!item.end_price) item.end_price = 0;
        if (!item.margin_percent) item.margin_percent = 0;

        item.total = item.end_price * item.quantity;

        if (item.price > 0) {
            item.margin_percent = (item.end_price - item.price) / item.price * 100;
        } else {
            item.margin_percent = 0;
        }
        item.margin = (item.end_price - item.price) * item.quantity;
    };

    this.addItem = function(item) {
        var model = this;
        model.items.push(item);
        model.calculateItem(model.items[model.items.length - 1]);
        model.refreshTable();
    };

    this.refreshRowByQuantity= function(index) {
        var row = $(this.container.find('.row_' + index).first());
        var item = this.items[index];

        var quantity = parseInt(row.find('.input-quantity').first().val());
        if (quantity < 0) {
            quantity = 0;
        }
        item.quantity = quantity;
        row.find('.input-quantity').first().val(quantity);

        item.total = item.end_price * item.quantity;
        item.margin = (item.end_price - item.price) * item.quantity;

        this.items[index] = item;
        this.refreshTable();
    };

    this.refreshRowByPrice = function(index) {
        var row = $(this.container.find('.row_' + index).first());
        var item = this.items[index];

        item.price = parseDecimal(row.find('.input-price').first().val());
        item.margin_percent = (item.end_price - item.price) / item.price * 100;
        item.margin = (item.end_price - item.price) * item.quantity;

        this.items[index] = item;
        this.refreshTable();
    };

    this.refreshRowByEndPrice = function(index) {
        var row = $(this.container.find('.row_' + index).first());
        var item = this.items[index];

        item.end_price = parseDecimal(row.find('.input-end-price').first().val());
        item.total = item.end_price * item.quantity;
        item.margin_percent = (item.end_price - item.price) / item.price * 100;
        item.margin = (item.end_price - item.price) * item.quantity;

        this.items[index] = item;
        this.refreshTable();
    };

    this.refreshRowByMarginPercent = function(index) {
        var row = $(this.container.find('.row_' + index).first());
        var item = this.items[index];

        item.margin_percent = parseDecimal(row.find('.input-margin-percent').first().val());
        item.end_price = (item.margin_percent / 100 * item.price) + item.price;
        item.total = item.end_price * item.quantity;
        item.margin = (item.end_price - item.price) * item.quantity;

        this.items[index] = item;
        this.refreshTable();
    };

    this.updateListeners = function() {
        var model = this;
        this.container.find('.fa-arrow-up').click(function() {
            model.moveItemUp($(this).data('index'));
        });
        this.container.find('.fa-arrow-down').click(function() {
            model.moveItemDown($(this).data('index'));
        });
        this.container.find('.fa-times').click(function() {
            var index = $(this).data('index');
            if (confirm('Вы действительно хотите удалить позицию ' + model.items[index].name + ' из списка?')) {
                model.deleteItem(index);
            }
        });
        this.container.find('.editable').click(function() {
            $(this).css('display', 'none');
            $(this).siblings().css('display', 'block').focus();
        });
        this.container.find('.edit').blur(function() {
            $(this).siblings().css('display', 'inline');
            $(this).css('display', 'none');
            model.refreshRowStandart($(this).data('index'));
        });
        this.container.find('.edit-price').blur(function() {
            $(this).siblings().css('display', 'inline');
            $(this).css('display', 'none');
            model.refreshRowByPrice($(this).data('index'));
        });
        this.container.find('.edit-end-price').blur(function() {
            $(this).siblings().css('display', 'inline');
            $(this).css('display', 'none');
            model.refreshRowByEndPrice($(this).data('index'));
        });
        this.container.find('.edit-margin-percent').blur(function() {
            $(this).siblings().css('display', 'inline');
            $(this).css('display', 'none');
            model.refreshRowByMarginPercent($(this).data('index'));
        });
        this.container.find('.edit-quantity').blur(function() {
            $(this).siblings().css('display', 'inline');
            $(this).css('display', 'none');
            model.refreshRowByQuantity($(this).data('index'));
        });

        this.container.find('.edit-name').blur(function() {
            $(this).siblings().css('display', 'inline');
            $(this).css('display', 'none');
            model.items[$(this).data('index')].name = $(this).val();
            model.refreshTable();
        });

        this.container.find('.edit-unit').blur(function() {
            $(this).siblings().css('display', 'inline');
            $(this).css('display', 'none');
            model.items[$(this).data('index')].unit_id = $(this).val();
            model.refreshTable();
        });

        this.container.find('.edit-period').blur(function() {
            $(this).siblings().css('display', 'inline');
            $(this).css('display', 'none');
            model.items[$(this).data('index')].delivery_period = $(this).val();
            model.refreshTable();
        });
    };

    this.refreshTable = function() {
        var container = this.container;
        container.html('');
        var model = this;

        this.items.forEach(function(item, index, items) {
            var row = $('<tr>').addClass('row-editable row_' + index);

            var control = $('<td>').addClass('cell-control');
            if (index > 0) control.append($('<div>').addClass('control-container').append($('<span>').addClass('fa fa-arrow-up').data('index', index)));
            if (index < items.length - 1) control.append($('<div>').addClass('control-container').append($('<span>').addClass('fa fa-arrow-down').data('index', index)));
            control.append($('<div>').addClass('control-container').append($('<span>').addClass('fa fa-times').data('index', index)));
            row.append(control);

            row.append($('<td>').css('vertical-align', 'middle').append($('<div>').addClass('index-contrainer').html(index + 1)));

            var name = {
                input: $('<input>').addClass('edit-name input-name').css('display', 'none').attr('type', 'text').data('index', index).val(item.name),
                div: $('<div>').addClass('editable div-name').html(item.name)
            };
            row.append($('<td>').append(name.input).append(name.div));

            var quantity = {
                input: $('<input>').addClass('edit-quantity input-quantity').css('display', 'none').attr('type', 'text').data('index', index).val(item.quantity),
                div: $('<div>').addClass('editable div-quantity').html(item.quantity)
            };
            row.append($('<td>').append(quantity.input).append(quantity.div));

            var unit_select = {
                input: $('<select>').addClass('edit-unit input-unit').css('display', 'none').data('index', index),
                div: $('<div>').addClass('editable div-unit').html(window.units[item.unit_id])
            };
            $.each(window.units, function (index, unit) {
                unit_select.input.append($('<option>').val(index).html(unit));
            });
            unit_select.input.val(item.unit_id);
            row.append($('<td>').append(unit_select.input).append(unit_select.div));

            var delivery_period = {
                input: $('<input>').addClass('edit-period input-period').css('display', 'none').attr('type', 'text').data('index', index).val(item.delivery_period),
                div: $('<div>').addClass('editable div-period').html(item.delivery_period)
            };
            row.append($('<td>').append(delivery_period.input).append(delivery_period.div));

            var price = {
                input: $('<input>').addClass('edit-price input-price').css('display', 'none').attr('type', 'text').data('index', index).val(toDecimalString(item.price)),
                div: $('<div>').addClass('editable div-price').html(toDecimalString(item.price))
            };
            row.append($('<td>').append(price.input).append(price.div));

            var end_price = {
                input: $('<input>').addClass('edit-end-price input-end-price').css('display', 'none').attr('type', 'text').data('index', index).val(toDecimalString(item.end_price)),
                div: $('<div>').addClass('editable div-end-price').html(toDecimalString(item.end_price))
            };
            row.append($('<td>').append(end_price.input).append(end_price.div));

            row.append($('<td>').addClass('cell-total').html(toDecimalString(item.total)));

            var margin_percent = {
                input: $('<input>').addClass('edit-margin-percent input-margin-percent').css('display', 'none').attr('type', 'text').data('index', index).val(toDecimalString(item.margin_percent)),
                div: $('<div>').addClass('editable div-margin-percent').html(toDecimalString(item.margin_percent))
            };
            row.append($('<td>').append(margin_percent.input).append(margin_percent.div));
            row.append($('<td>').html(toDecimalString(item.margin)));
            container.append(row);
        });

        this.updateListeners();
        $.each(this.items, function(index, item) {
            item.index = index;
        });
        this.result_input.val(JSON.stringify(this.items));
        window.totals_model.reload(this.initReload);
        if (this.initReload) {
            this.initReload = false;
        }
    };

    this.init = function() {
        this.container = $(this.container_selector);
        this.result_input = $(this.result_input_selector);

        if (window.model_id) {
            this.initReload = true;
            var model = this;
            $.ajax({
                method: 'POST',
                url: 'get-goods',
                dataType: 'json',
                data: {id: window.model_id, type: 'additional'},
                success: function(data) {
                    data.forEach(function(item) {
                        model.initLoadedItem(item);
                    });
                    model.items = data;
                    model.refreshTable();
                }
            });
        }
    };

    this.moveItemUp = function(index) {
        var temp = this.items[index];
        this.items[index] = this.items[index - 1];
        this.items[index - 1] = temp;

        this.refreshTable();
    };

    this.moveItemDown = function(index) {
        var temp = this.items[index];
        this.items[index] = this.items[index + 1];
        this.items[index + 1] = temp;

        this.refreshTable();
    };

    this.deleteItem = function(index) {
        this.items.splice(index, 1);
        this.refreshTable();
    };
}