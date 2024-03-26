

define('crm:views/dashlets/activities',
['views/dashlets/abstract/base', 'multi-collection'], function (Dep, MultiCollection) {

    return Dep.extend({

        name: 'Activities',

        
        templateContent: '<div class="list-container">{{{list}}}</div>',

        rowActionsView: 'crm:views/record/row-actions/activities-dashlet',

        defaultListLayout: {
            rows: [
                [
                    {
                        name: 'ico',
                        view: 'crm:views/fields/ico',
                        params: {
                            notRelationship: true,
                        },
                    },
                    {
                        name: 'name',
                        link: true,
                    },
                ],
                [
                    {name: 'dateStart'},
                ],
            ],
        },

        listLayoutEntityTypeMap: {
            Task: {
                rows: [
                    [
                        {
                            name: 'ico',
                            view: 'crm:views/fields/ico',
                            params: {
                                notRelationship: true
                            },
                        },
                        {
                            name: 'name',
                            link: true,
                        },
                    ],
                    [
                        {name: 'dateEnd'},
                        {
                            name: 'priority',
                            view: 'crm:views/task/fields/priority-for-dashlet',
                        },
                    ],
                ]
            }
        },

        init: function () {
            Dep.prototype.init.call(this);
        },

        setup: function () {
            this.seeds = {};

            this.scopeList = this.getOption('enabledScopeList') || [];

            this.listLayout = {};

            this.scopeList.forEach((item) => {
                if (item in this.listLayoutEntityTypeMap) {
                    this.listLayout[item] = this.listLayoutEntityTypeMap[item];

                    return;
                }

                this.listLayout[item] = this.defaultListLayout;
            });

            this.wait(true);
            var i = 0;

            this.scopeList.forEach(scope => {
                this.getModelFactory().create(scope, seed => {
                    this.seeds[scope] = seed;

                    i++;

                    if (i === this.scopeList.length) {
                        this.wait(false);
                    }
                });
            });

            this.scopeList.slice(0).reverse().forEach(scope => {
                if (this.getAcl().checkScope(scope, 'create')) {
                    this.actionList.unshift({
                        name: 'createActivity',
                        text: this.translate('Create ' + scope, 'labels', scope),
                        iconHtml: '<span class="fas fa-plus"></span>',
                        url: '#' + scope + '/create',
                        data: {
                            scope: scope,
                        },
                    });
                }
            });
        },

        afterRender: function () {
            this.collection = new MultiCollection();
            this.collection.seeds = this.seeds;
            this.collection.url = 'Activities/upcoming';
            this.collection.maxSize = this.getOption('displayRecords') ||
                this.getConfig().get('recordsPerPageSmall') || 5;
            this.collection.data.entityTypeList = this.scopeList;
            this.collection.data.futureDays = this.getOption('futureDays');

            this.listenToOnce(this.collection, 'sync', () => {
                this.createView('list', 'crm:views/record/list-activities-dashlet', {
                    selector: '> .list-container',
                    pagination: false,
                    type: 'list',
                    rowActionsView: this.rowActionsView,
                    checkboxes: false,
                    collection: this.collection,
                    listLayout: this.listLayout,
                }, view => {
                    view.render();
                });
            });

            this.collection.fetch();
        },

        actionRefresh: function () {
            this.collection.fetch({
                previousDataList: this.collection.models.map(model => {
                    return Espo.Utils.cloneDeep(model.attributes);
                }),
            });
        },

        actionCreateActivity: function (data) {
            var scope = data.scope;
            var attributes = {};

            this.populateAttributesAssignedUser(scope, attributes);

            Espo.Ui.notify(' ... ');

            var viewName = this.getMetadata().get('clientDefs.'+scope+'.modalViews.edit') || 'views/modals/edit';

            this.createView('quickCreate', viewName, {
                scope: scope,
                attributes: attributes,
            }, view => {
                view.render();
                view.notify(false);

                this.listenToOnce(view, 'after:save', () => {
                    this.actionRefresh();
                });
            });
        },

        actionCreateMeeting: function () {
            var attributes = {};

            this.populateAttributesAssignedUser('Meeting', attributes);

            Espo.Ui.notify(' ... ');

            var viewName = this.getMetadata().get('clientDefs.Meeting.modalViews.edit') || 'views/modals/edit';

            this.createView('quickCreate', viewName, {
                scope: 'Meeting',
                attributes: attributes,
            }, view => {
                view.render();
                view.notify(false);

                this.listenToOnce(view, 'after:save', () => {
                    this.actionRefresh();
                });
            });
        },

        actionCreateCall: function () {
            var attributes = {};

            this.populateAttributesAssignedUser('Call', attributes);

            Espo.Ui.notify(' ... ');

            var viewName = this.getMetadata().get('clientDefs.Call.modalViews.edit') || 'views/modals/edit';

            this.createView('quickCreate', viewName, {
                scope: 'Call',
                attributes: attributes,
            }, view => {
                view.render();
                view.notify(false);

                this.listenToOnce(view, 'after:save', () => {
                    this.actionRefresh();
                });
            });
        },

        populateAttributesAssignedUser: function (scope, attributes) {
            if (this.getMetadata().get(['entityDefs', scope, 'fields', 'assignedUsers'])) {
                attributes['assignedUsersIds'] = [this.getUser().id];
                attributes['assignedUsersNames'] = {};
                attributes['assignedUsersNames'][this.getUser().id] = this.getUser().get('name');
            } else {
                attributes['assignedUserId'] = this.getUser().id;
                attributes['assignedUserName'] = this.getUser().get('name');
            }
        },
    });
});
