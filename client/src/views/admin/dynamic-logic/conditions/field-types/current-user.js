

import Base from 'views/admin/dynamic-logic/conditions/field-types/base';
import Model from 'model';

export default class extends Base {

    getValueViewName() {
        return 'views/fields/user';
    }

    getValueFieldName() {
        return 'link';
    }

    createModel() {
        const model = new Model();

        model.setDefs({
            fields: {
                link: {
                    type: 'link',
                    entity: 'User',
                },
            }
        });

        return Promise.resolve(model);
    }

    populateValues() {
        if (this.itemData.attribute) {
            this.model.set('linkId', this.itemData.value);
        }

        const name = (this.additionalData.values || {}).name;

        this.model.set('linkName', name);
    }

    translateLeftString() {
        return '$' + this.translate('User', 'scopeNames');
    }

    fetch() {
        const valueView = this.getView('value');

        valueView.fetchToModel();

        return {
            type: this.type,
            attribute: '$user.id',
            data: {
                values: {
                    name: this.model.get('linkName'),
                },
            },
            value: this.model.get('linkId'),
        };
    }
}

