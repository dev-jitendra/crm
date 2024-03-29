

define('crm:views/calendar/modals/shared-options', ['views/modal', 'model'], function (Dep, Model) {

    return Dep.extend({

        className: 'dialog dialog-record',

        template: 'crm:calendar/modals/shared-options',

        buttonList: [
            {
                name: 'save',
                label: 'Save',
                style: 'primary',
            },
            {
                name: 'cancel',
                label: 'Cancel',
            },
        ],

        setup: function () {
            var userList = this.options.userList || [];

            var userIdList = [];
            var userNames = {};

            userList.forEach(item => {
                userIdList.push(item.id);
                userNames[item.id] = item.name;
            });

            var model = new Model();

            model.name = 'SharedCalendarOptions';

            model.set({
                usersIds: userIdList,
                usersNames: userNames
            });

            this.createView('record', 'crm:views/calendar/record/shared-options', {
                selector: '.record-container',
                model: model,
            });
        },

        actionSave: function () {
            var data = this.getView('record').fetch();

            var userList = [];

            (data.usersIds || []).forEach(id => {
                userList.push({
                    id: id,
                    name: (data.usersNames || {})[id] || id
                });
            });

            this.trigger('save', {
                userList: userList
            });

            this.remove();
        },
    });
});
