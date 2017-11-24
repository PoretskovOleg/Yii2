function CatalogGoodsModel() {
    this.container_selector = '#goods_table';
    this.result_input_selector = '#catalog_goods_storage';

    this.items = [];
    this.initReload = false;

    this.initLoadedItem = function(item) {
        item.weight = parseFloat(item.weight);
        item.volume = parseFloat(item.volume);

        item.quantity = parseInt(item.quantity);
        item.mrc_percent = parseFloat(item.mrc_percent);
        item.discount = parseFloat(item.discount);
        item.price = parseFloat(item.price);
        item.base_price_percent = parseFloat(item.base_price_percent);
        item.margin_percent = parseFloat(item.margin_percent);
        item.end_price = parseFloat(item.end_price);

        item.margin = (item.end_price - item.price) * item.quantity;
        item.base_price = item.price + (item.price / 100 * item.base_price_percent);
        item.mrc = item.price + (item.price / 100 * item.mrc_percent);

        item.total = item.end_price * item.quantity;
    };

    this.calculateItem = function(item) {
        item.deleted = false;
        item.discount = 0;
        item.mrc = item.price + (item.price / 100 * item.mrc_percent);
        item.base_price = item.price + (item.price / 100 * item.base_price_percent);
        item.base_price = item.base_price + (item.base_price / 100 * item.extra_charge_percent);
        item.end_price = item.base_price - item.base_price / 100 * item.discount;
        item.total = item.end_price * item.quantity;
        item.margin = (item.end_price - item.price) * item.quantity;

        if (item.price > 0) {
            item.margin_percent = (item.end_price - item.price) / item.price * 100;
        } else {
            item.margin_percent = 0;
        }
    };

    this.addGood = function(id, quantity) {
        var model = this;
        $.ajax({
            method: 'POST',
            url: 'get-good',
            dataType: 'json',
            data: {id: id},
            success: function(data) {
                data.quantity = quantity;
                model.items.push(data);
                model.calculateItem(model.items[model.items.length - 1]);
                model.refreshTable();
            }
        });
    };

    this.refreshRowByCount = function(index) {
        var row = $(this.container.find('.row_' + index).first());
        var item = this.items[index];

        var quantity = parseInt(row.find('.input-count').first().val());
        if (quantity < 0) {
            quantity = 0;
        }
        item.quantity = quantity;
        row.find('.input-count').first().val(quantity);

        item.total = item.end_price * item.quantity;
        item.margin = (item.end_price - item.price) * item.quantity;

        this.items[index] = item;
        this.refreshTable();
    };

    this.refreshRowByEndPrice = function(index) {
        var row = $(this.container.find('.row_' + index).first());
        var item = this.items[index];

        var end_price = parseDecimal(row.find('.input-end-price').first().val());
        if (end_price > item.base_price) {
            end_price = item.base_price;
        } else if (end_price < item.mrc) {
            end_price = item.mrc;
        }

        item.end_price = end_price;

        if (item.base_price > 0) {
            item.discount = (item.base_price - item.end_price) / item.base_price * 100;
        } else {
            item.discount = 0;
        }

        item.total = item.end_price * item.quantity;

        if (item.price > 0) {
            item.margin_percent = (item.end_price - item.price) / item.price * 100;
        } else {
            item.margin_percent = 0;
        }

        item.margin = (item.end_price - item.price) * item.quantity;

        this.items[index] = item;
        this.refreshTable();
    };

    this.refreshRowByMarginPercent = function(index) {
        var row = $(this.container.find('.row_' + index).first());
        var item = this.items[index];

        if (item.price > 0) {
            item.margin_percent = (item.end_price - item.price) / item.price * 100;
            var margin_percent = parseDecimal(row.find('.input-margin-percent').first().val());
            var min_margin_percent = item.mrc_percent;
            var max_margin_percent = item.base_price_percent;

            if (margin_percent > max_margin_percent) {
                margin_percent = max_margin_percent;
            } else if (margin_percent < min_margin_percent) {
                margin_percent = min_margin_percent;
            }

            item.margin_percent = margin_percent;
        } else {
            item.margin_percent = 0;
        }

        item.end_price = (item.margin_percent / 100 * item.price) + item.price;

        if (item.base_price > 0) {
            item.discount = (item.base_price - item.end_price) / item.base_price * 100;
        } else {
            item.discount = 0;
        }

        item.total = item.end_price * item.quantity;
        item.margin = (item.end_price - item.price) * item.quantity;

        this.items[index] = item;
        this.refreshTable();
    };

    this.refreshRowByDiscount = function(index) {
        var row = $(this.container.find('.row_' + index).first());
        var item = this.items[index];

        if (item.base_price > 0) {
            var discount = parseDecimal(row.find('.input-discount').first().val());
            var max_discount = (item.base_price - item.mrc) / item.base_price * 100;
            if (discount > max_discount) {
                discount = max_discount;
            } else if (discount < 0) {
                discount = 0;
            }
            item.discount = discount;
        } else {
            item.discount = 0;
        }

        item.end_price = item.base_price - (item.discount / 100) * item.base_price;

        //item.end_price = item.base_price - (item.base_price / 100 * item.discount);
        item.total = item.end_price * item.quantity;

        if (item.price > 0) {
            item.margin_percent = (item.end_price - item.price) / item.price * 100;
        } else {
            item.margin_percent = 0;
        }

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
        this.container.find('.edit-discount').blur(function() {
            $(this).siblings().css('display', 'inline');
            $(this).css('display', 'none');
            model.refreshRowByDiscount($(this).data('index'));
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
        this.container.find('.edit-count').blur(function() {
            $(this).siblings().css('display', 'inline');
            $(this).css('display', 'none');
            model.refreshRowByCount($(this).data('index'));
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
            if (item.deleted) return;

            var row = $('<tr>').addClass('row-editable row_' + index);

            var control = $('<td>').addClass('cell-control');
            if (index > 0) control.append($('<div>').addClass('control-container').append($('<span>').addClass('fa fa-arrow-up').data('index', index)));
            if (index < items.length - 1) control.append($('<div>').addClass('control-container').append($('<span>').addClass('fa fa-arrow-down').data('index', index)));
            control.append($('<div>').addClass('control-container').append($('<span>').addClass('fa fa-times').data('index', index)));
            row.append(control);

            row.append($('<td>').css('vertical-align', 'middle').append($('<div>').addClass('index-contrainer').html(index + 1)));

            row.append($('<td>').html(item.good_id));
            row.append($('<td>').html(item.name));

            var quantity = {
                input: $('<input>').addClass('edit-count input-count').css('display', 'none').attr('type', 'text').data('index', index).val(item.quantity),
                div: $('<div>').addClass('editable div-count').html(item.quantity)
            };
            row.append($('<td>').append(quantity.input).append(quantity.div));

            row.append($('<td>').html(window.units[item.unit_id]));
            var delivery_period = {
                input: $('<input>').addClass('edit-period input-period').css('display', 'none').attr('type', 'text').data('index', index).val(item.delivery_period),
                div: $('<div>').addClass('editable div-period').html(item.delivery_period)
            };
            row.append($('<td>').append(delivery_period.input).append(delivery_period.div));
            row.append($('<td>').html(item.price));
            row.append($('<td>').html(toDecimalString(item.mrc) + '/' + Math.round(item.mrc_percent) + '%'));
            row.append($('<td>').html(toDecimalString(item.base_price) + '/' + Math.round(item.base_price_percent) + '%'));

            var discount = {
                input: $('<input>').addClass('edit-discount input-discount').css('display', 'none').attr('type', 'text').data('index', index).val(toDecimalString(item.discount)),
                div: $('<div>').addClass('editable div-discount').html(toDecimalString(item.discount))
            };
            row.append($('<td>').append(discount.input).append(discount.div));

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
                data: {id: window.model_id, type: 'catalog'},
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
