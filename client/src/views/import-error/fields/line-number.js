

define('views/import-error/fields/line-number', ['views/fields/int'], (Dep) => {

    return Dep.extend({

        disableFormatting: true,

        data: function () {
            let data = Dep.prototype.data.call(this);

            data.valueIsSet = this.model.has(this.sourceName);
            data.isNotEmpty = this.model.has(this.sourceName);

            return data;
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            this.sourceName = this.name === 'exportLineNumber' ?
                'exportRowIndex' :
                'rowIndex';
        },

        getAttributeList: function () {
            return [this.sourceName];
        },

        getValueForDisplay: function () {
            let value = this.model.get(this.sourceName);

            value++;

            return this.formatNumber(value);
        },
    });
});
