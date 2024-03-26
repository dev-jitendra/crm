

import CreateRelated from 'handlers/create-related';

class MassEmailsCreateHandler extends CreateRelated {

    getAttributes(model) {
        return Promise.resolve({
            name: model.get('name') + ' ' + this.viewHelper.dateTime.getToday(),
        });
    }
}

export default MassEmailsCreateHandler;
