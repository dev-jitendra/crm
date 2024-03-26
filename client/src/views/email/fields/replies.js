

define('views/email/fields/replies', ['views/fields/link-multiple'], function (Dep) {

    return Dep.extend({

        getAttributeList: function () {
            let attributeList = Dep.prototype.getAttributeList.call(this);

            attributeList.push(this.name + 'Columns');

            return attributeList;
        },

        getDetailLinkHtml: function (id) {
            let html = Dep.prototype.getDetailLinkHtml.call(this, id);

            let columns = this.model.get(this.name + 'Columns') || {};

            let status = (columns[id] || {})['status'];

            return $('<div>')
                .append(
                    $('<span>')
                        .addClass('fas fa-arrow-right fa-sm link-multiple-item-icon')
                        .addClass(status === 'Draft' ? 'text-warning' : 'text-success')
                )
                .append(html)
                .html();
        },
    });
});
