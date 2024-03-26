

define('views/dashboard-template/detail', ['views/detail'], function (Dep) {

    return Dep.extend({

        actionDeployToUsers: function () {
            this.createView('dialog', 'views/dashboard-template/modals/deploy-to-users', {
                model: this.model,
            }, function (view) {
                view.render();
            }, this);
        },

        actionDeployToTeam: function () {
            this.createView('dialog', 'views/dashboard-template/modals/deploy-to-team', {
                model: this.model,
            }, function (view) {
                view.render();
            }, this);
        },
    });
});
