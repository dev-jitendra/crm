

import Controller from 'controller';

class CalendarController extends Controller {

    checkAccess() {
        if (this.getAcl().check('Calendar')) {
            return true;
        }

        return false;
    }

    
    actionShow(options) {
        this.actionIndex(options);
    }

    actionIndex(options) {
        this.handleCheckAccess('');

        this.main('crm:views/calendar/calendar-page', {
            date: options.date,
            mode: options.mode,
            userId: options.userId,
            userName: options.userName,
        });
    }
}

export default CalendarController;
