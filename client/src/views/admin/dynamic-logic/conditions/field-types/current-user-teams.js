

import LinkMultiple from 'views/admin/dynamic-logic/conditions/field-types/link-multiple';

export default class extends LinkMultiple {

    translateLeftString() {
        return '$' + this.translate('User', 'scopeNames') + '.' + super.translateLeftString();
    }

    fetch() {
        const data = super.fetch();

        data.attribute = '$user.teamsIds';

        return data;
    }
}
