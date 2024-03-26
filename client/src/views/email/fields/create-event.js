

define('views/email/fields/create-event', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        detailTemplate: 'email/fields/create-event/detail',

        eventEntityType: 'Meeting',

        getAttributeList: function () {
            return [
                'icsEventData',
                'createdEventId',
            ];
        },

        events: {
            'click [data-action="createEvent"]': function () {
                this.createEvent();
            },
        },

        createEvent: function () {
            let viewName = this.getMetadata().get(['clientDefs', this.eventEntityType, 'modalViews', 'edit']) ||
                'views/modals/edit';

            let eventData = this.model.get('icsEventData') || {};

            let attributes = Espo.Utils.cloneDeep(eventData.valueMap || {});

            attributes.parentId = this.model.get('parentId');
            attributes.parentType = this.model.get('parentType');
            attributes.parentName = this.model.get('parentName');

            this.addFromAddressToAttributes(attributes);

            this.createView('dialog', viewName, {
                attributes: attributes,
                scope: this.eventEntityType,
            })
                .then(view => {
                    view.render();

                    this.listenToOnce(view, 'after:save', () => {
                        this.model
                            .fetch()
                            .then(() =>
                                Espo.Ui.success(this.translate('Done'))
                            );
                    });
                });
        },

        addFromAddressToAttributes: function (attributes) {
            let fromAddress = this.model.get('from');
            let idHash = this.model.get('idHash') || {};
            let typeHash = this.model.get('typeHash') || {};
            let nameHash = this.model.get('nameHash') || {};

            let fromId = null;
            let fromType = null;
            let fromName = null;

            if (!fromAddress) {
                return;
            }

            fromId = idHash[fromAddress] || null;
            fromType = typeHash[fromAddress] || null;
            fromName = nameHash[fromAddress] || null;

            let attendeeLink = this.getAttendeeLink(fromType);

            if (!attendeeLink) {
                return;
            }

            attributes[attendeeLink + 'Ids'] = attributes[attendeeLink + 'Ids'] || [];
            attributes[attendeeLink + 'Names'] = attributes[attendeeLink + 'Names'] || {};

            if (~attributes[attendeeLink + 'Ids'].indexOf(fromId)) {
                return;
            }

            attributes[attendeeLink + 'Ids'].push(fromId);
            attributes[attendeeLink + 'Names'][fromId] = fromName;
        },

        getAttendeeLink: function (entityType) {
            if (entityType === 'User') {
                return 'users';
            }

            if (entityType === 'Contact') {
                return 'contacts';
            }

            if (entityType === 'Lead') {
                return 'leads';
            }

            return null;
        },

    });
});
