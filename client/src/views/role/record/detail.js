

define('views/role/record/detail', ['views/record/detail'], function (Dep) {

    return Dep.extend({

        tableView: 'views/role/record/table',

        sideView: false,
        isWide: true,
        editModeDisabled: true,
        stickButtonsContainerAllTheWay: true,

        setup: function () {
            Dep.prototype.setup.call(this);

            this.createView('extra', this.tableView, {
                selector: '.extra',
                model: this.model
            });
        },
    });
});
