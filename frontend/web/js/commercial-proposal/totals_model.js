function TotalsModel() {
    this.container_selector = '#totals_table';

    this.total = 0;
    this.tax = 0;
    this.margin = 0;
    this.weight = 0;
    this.volume = 0;

    this.onReload = function() {};

    this.init = function() {
        this.container = $(this.container_selector);
    };

    this.reload = function(initial) {
        this.total = 0;
        this.tax = 0;
        this.margin = 0;
        this.weight = 0;
        this.volume = 0;

        var model = this;
        window.goods_model.items.forEach(function (item) {
            model.total += item.total;
            model.margin += item.margin;
            model.weight += item.weight * item.quantity;
            model.volume += item.volume * item.quantity;
        });

        window.additional_goods_model.items.forEach(function (item) {
            model.total += item.total;
            model.margin += item.margin;
        });

        this.tax = this.total - this.total / 1.18;

        this.refresh();


        if (!initial) {
            this.onReload(model.total + model.tax);
        }
    };

    this.refresh = function() {
        this.container.html('');

        var row = $('<tr>');
        row.append($('<td>').html('ИТОГО'));
        row.append($('<td>').html(toDecimalString(this.total)));
        this.container.append(row);

        var row = $('<tr>');
        row.append($('<td>').html('В том числе НДС 18%'));
        row.append($('<td>').html(toDecimalString(this.tax)));
        this.container.append(row);

        var row = $('<tr>');
        row.append($('<td>').html('Всего к оплате'));
        row.append($('<td>').html(toDecimalString(this.total)));
        this.container.append(row);

        var row = $('<tr>');
        row.append($('<td>').html('Маржа'));
        row.append($('<td>').html(toDecimalString(this.margin)));
        this.container.append(row);

        var weight = this.weight.toFixed(1).replace('.', ',');
        var volume = this.volume.toFixed(1).replace('.', ',');

        var row = $('<tr>');
        row.append($('<td>').html('Масса: ' + weight + ' кг'));
        row.append($('<td>').html('Объём: ' + volume + ' м3'));
        this.container.append(row);
    };
}
