



import $ from 'jquery';


class PageTitle {

    
    constructor(config) {

        
        this.displayNotificationNumber = config.get('newNotificationCountInTitle') || false;

        
        this.title = $('head title').text() || '';

        
        this.notificationNumber = 0;
    }

    
    setTitle(title) {
        this.title = title;

        this.update();
    }

    
    setNotificationNumber(notificationNumber) {
        this.notificationNumber = notificationNumber;

        if (this.displayNotificationNumber) {
            this.update();
        }
    }

    
    update() {
        let value = '';

        if (this.displayNotificationNumber && this.notificationNumber) {
            value = '(' + this.notificationNumber.toString() + ')';

            if (this.title) {
                value += ' ';
            }
        }

        value += this.title;

        $('head title').text(value);
    }
}

export default PageTitle;
