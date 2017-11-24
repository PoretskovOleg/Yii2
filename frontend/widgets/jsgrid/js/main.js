var basePriceField = function(config) {
    jsGrid.Field.call(this, config);
};

basePriceField.prototype = new jsGrid.Field({
    editTemplate: function(value) {
        return '<input type=\"text\" value=\"' + value.match(/.+(?=\/)/)[0] + '\">';
    },

    editValue: function(value) {
        return this._value;
    }
});

jsGrid.fields.basePriceField = basePriceField;
