

define('views/record/list-pagination', ['view'], function (Dep) {

    return Dep.extend({

        template: 'record/list-pagination',

        data: function () {
            const previous = this.collection.offset > 0;
            const next = this.collection.total - this.collection.offset > this.collection.maxSize ||
                this.collection.total === -1;

            return {
                total: this.collection.total,
                from: this.collection.offset + 1 ,
                to: this.collection.offset + this.collection.length,
                previous: previous,
                next: next,
                noTotal: this.collection.total === -1 || this.collection.total === -2,
            };
        },
    });
});
