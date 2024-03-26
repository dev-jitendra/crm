

define('crm:views/contact/fields/account-role', ['views/fields/varchar'], function (Dep) {

    return Dep.extend({

        detailTemplate: 'crm:contact/fields/account-role/detail',
        listTemplate: 'crm:contact/fields/account-role/detail',

        setup: function () {
            Dep.prototype.setup.call(this);

            this.listenTo(this.model, 'change:title', () => {
                this.model.set('accountRole', this.model.get('title'));
            });
        },

        getAttributeList: function () {
            var list = Dep.prototype.getAttributeList.call(this);

            list.push('title');
            list.push('accountIsInactive');

            return list;
        },

        data: function () {
            var data = Dep.prototype.data.call(this);

            if (this.model.has('accountIsInactive')) {
                data.accountIsInactive = this.model.get('accountIsInactive');
            }

            return data;
        }
    });
});
