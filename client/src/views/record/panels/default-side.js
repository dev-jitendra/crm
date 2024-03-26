

import SidePanelView from 'views/record/panels/side';


class DefaultSidePanelView extends SidePanelView {

    data() {
        const data = super.data();

        if (
            this.complexCreatedDisabled &&
            this.complexModifiedDisabled || (!this.hasComplexCreated && !this.hasComplexModified)
        ) {
            data.complexDateFieldsDisabled = true;
        }

        data.hasComplexCreated = this.hasComplexCreated;
        data.hasComplexModified = this.hasComplexModified;

        return data;
    }

    setup() {
        this.fieldList = Espo.Utils.cloneDeep(this.fieldList);

        this.hasComplexCreated =
            !!this.getMetadata().get(['entityDefs', this.model.entityType, 'fields', 'createdAt']) &&
            !!this.getMetadata().get(['entityDefs', this.model.entityType, 'fields', 'createdBy']);

        this.hasComplexModified =
            !!this.getMetadata().get(['entityDefs', this.model.entityType, 'fields', 'modifiedAt']) &&
            !!this.getMetadata().get(['entityDefs', this.model.entityType, 'fields', 'modifiedBy']);

        super.setup();
    }

    setupFields() {
        super.setupFields();

        if (!this.complexCreatedDisabled) {
            if (this.hasComplexCreated) {
                this.fieldList.push({
                    name: 'complexCreated',
                    labelText: this.translate('Created'),
                    isAdditional: true,
                    view: 'views/fields/complex-created',
                    readOnly: true,
                });

                if (!this.model.get('createdById')) {
                    this.recordViewObject.hideField('complexCreated');
                }
            }
        } else {
            this.recordViewObject.hideField('complexCreated');
        }

        if (!this.complexModifiedDisabled) {
            if (this.hasComplexModified) {
                this.fieldList.push({
                    name: 'complexModified',
                    labelText: this.translate('Modified'),
                    isAdditional: true,
                    view: 'views/fields/complex-created',
                    readOnly: true,
                    options: {
                        baseName: 'modified',
                    },
                });
            }
            if (!this.model.get('modifiedById')) {
                this.recordViewObject.hideField('complexModified');
            }
        } else {
            this.recordViewObject.hideField('complexModified');
        }

        if (!this.complexCreatedDisabled && this.hasComplexCreated) {
            this.listenTo(this.model, 'change:createdById', () => {
                if (!this.model.get('createdById')) {
                    return;
                }

                this.recordViewObject.showField('complexCreated');
            });
        }

        if (!this.complexModifiedDisabled && this.hasComplexModified) {
            this.listenTo(this.model, 'change:modifiedById', () => {
                if (!this.model.get('modifiedById')) {
                    return;
                }

                this.recordViewObject.showField('complexModified');
            });
        }

        if (this.getMetadata().get(['scopes', this.model.entityType ,'stream']) && !this.getUser().isPortal()) {
            this.fieldList.push({
                name: 'followers',
                labelText: this.translate('Followers'),
                isAdditional: true,
                view: 'views/fields/followers',
                readOnly: true,
            });

            this.controlFollowersField();

            this.listenTo(this.model, 'change:followersIds', () => this.controlFollowersField());
        }
    }

    controlFollowersField() {
        if (this.model.get('followersIds') && this.model.get('followersIds').length) {
            this.recordViewObject.showField('followers');
        } else {
            this.recordViewObject.hideField('followers');
        }
    }
}

export default DefaultSidePanelView;
