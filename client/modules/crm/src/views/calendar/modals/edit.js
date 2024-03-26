

define('crm:views/calendar/modals/edit', ['views/modals/edit'], function (Dep) {

    return Dep.extend({

        template: 'crm:calendar/modals/edit',

        scopeList: [
            'Meeting',
            'Call',
            'Task',
        ],

        data: function () {
            return {
                scopeList: this.scopeList,
                scope: this.scope,
                isNew: !(this.id),
            };
        },

        additionalEvents: {
            'change .scope-switcher input[name="scope"]': function () {
                Espo.Ui.notify(' ... ');

                let scope = $('.scope-switcher input[name="scope"]:checked').val();
                this.scope = scope;

                this.getModelFactory().create(this.scope, model => {
                    model.populateDefaults();

                    let attributes = this.getRecordView().fetch();

                    attributes = {...attributes, ...this.getRecordView().model.getClonedAttributes()};

                    this.filterAttributesForEntityType(attributes, scope);

                    model.set(attributes);

                    this.model = model;

                    this.createRecordView(model, (view) => {
                        view.render();
                        view.notify(false);
                    });

                    this.handleAccess(model);
                });
            },
        },

        filterAttributesForEntityType: function (attributes, entityType) {
            this.getHelper()
                .fieldManager
                .getEntityTypeFieldList(entityType, {type: 'enum'})
                .forEach(field => {
                    if (!(field in attributes)) {
                        return;
                    }

                    let options = this.getMetadata().get(['entityDefs', entityType, 'fields', field, 'options']) || [];

                    let value = attributes[field];

                    if (!~options.indexOf(value)) {
                        delete attributes[field];
                    }
                });
        },

        createRecordView: function (model, callback) {
            if (!this.id && !this.dateIsChanged) {
                if (this.options.dateStart && this.options.dateEnd) {
                    this.model.set('dateStart', this.options.dateStart);
                    this.model.set('dateEnd', this.options.dateEnd);
                }

                if (this.options.allDay) {
                    var allDayScopeList = this.getMetadata().get('clientDefs.Calendar.allDayScopeList') || [];

                    if (~allDayScopeList.indexOf(this.scope)) {
                        this.model.set('dateStart', null);
                        this.model.set('dateEnd', null);
                        this.model.set('dateStartDate', null);
                        this.model.set('dateEndDate', this.options.dateEndDate);

                        if (this.options.dateEndDate !== this.options.dateStartDate) {
                            this.model.set('dateStartDate', this.options.dateStartDate);
                        }
                    }
                    else if (this.getMetadata().get(['entityDefs', this.scope, 'fields', 'dateStartDate'])) {
                        this.model.set('dateStart', null);
                        this.model.set('dateEnd', null);
                        this.model.set('dateStartDate', this.options.dateStartDate);
                        this.model.set('dateEndDate', this.options.dateEndDate);
                        this.model.set('isAllDay', true);
                    }
                    else {
                        this.model.set('isAllDay', false);
                        this.model.set('dateStartDate', null);
                        this.model.set('dateEndDate', null);
                    }
                }
            }

            this.listenTo(this.model, 'change:dateStart', (m, value, o) => {
                if (o.ui) {
                    this.dateIsChanged = true;
                }
            });

            this.listenTo(this.model, 'change:dateEnd', (m, value, o) => {
                if (o.ui || o.updatedByDuration) {
                    this.dateIsChanged = true;
                }
            });

            Dep.prototype.createRecordView.call(this, model, callback);
        },

        handleAccess: function (model) {
            if (
                this.id &&
                !this.getAcl().checkModel(model, 'edit') || !this.id &&
                !this.getAcl().checkModel(model, 'create')
            ) {
                this.hideButton('save');
                this.hideButton('fullForm');

                this.$el.find('button[data-name="save"]').addClass('hidden');
                this.$el.find('button[data-name="fullForm"]').addClass('hidden');
            }
            else {
                this.showButton('save');
                this.showButton('fullForm');
            }

            if (!this.getAcl().checkModel(model, 'delete')) {
                this.hideButton('remove');
            } else {
                this.showButton('remove');
            }
        },

        afterRender: function () {
            Dep.prototype.afterRender.call(this);

            if (this.hasView('edit')) {
                var model = this.getView('edit').model;

                if (model) {
                    this.handleAccess(model);
                }
            }
        },

        setup: function () {
            this.events = {
                ...this.additionalEvents,
                ...this.events,
            };

            this.scopeList = Espo.Utils.clone(this.options.scopeList || this.scopeList);
            this.enabledScopeList = this.options.enabledScopeList || this.scopeList;

            if (!this.options.id && !this.options.scope) {
                var scopeList = [];

                this.scopeList.forEach((scope) => {
                    if (this.getAcl().check(scope, 'create')) {
                        if (~this.enabledScopeList.indexOf(scope)) {
                            scopeList.push(scope);
                        }
                    }
                });

                this.scopeList = scopeList;

                var calendarDefaultEntity = scopeList[0];

                if (calendarDefaultEntity && ~this.scopeList.indexOf(calendarDefaultEntity)) {
                    this.options.scope = calendarDefaultEntity;
                } else {
                    this.options.scope = this.scopeList[0] || null;
                }

                if (this.scopeList.length === 0) {
                    this.remove();
                    return;
                }
            }

            Dep.prototype.setup.call(this);

            if (!this.id) {
                this.$header = $('<a>')
                    .attr('title', this.translate('Full Form'))
                    .attr('role', 'button')
                    .attr('data-action', 'fullForm')
                    .addClass('action')
                    .text(this.translate('Create', 'labels', 'Calendar'));
            }

            if (this.id) {
                this.buttonList.splice(1, 0, {
                    name: 'remove',
                    text: this.translate('Remove')
                });
            }

            this.once('after:save', () => {
                this.$el.find('.scope-switcher').remove();
            })
        },

        actionRemove: function () {
            let model = this.getView('edit').model;

            this.confirm(this.translate('removeRecordConfirmation', 'messages'), () => {
                let $buttons = this.dialog.$el.find('.modal-footer button');

                $buttons.addClass('disabled');

                model.destroy()
                    .then(() => {
                        this.trigger('after:destroy', model);
                        this.dialog.close();
                    })
                    .catch(() => {
                        $buttons.removeClass('disabled');
                    });
            });
        },
    });
});
