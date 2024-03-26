

import View from 'view';
import $ from 'jquery';


class PopupNotificationView extends View {

    type = 'default'
    style = 'default'
    closeButton = true
    soundPath = 'client/sounds/pop_cork'

    init() {
        super.init();

        const id = this.options.id;
        const containerSelector = this.containerSelector = '#' + id;

        this.setSelector(containerSelector);

        this.notificationSoundsDisabled = this.getConfig().get('notificationSoundsDisabled');

        this.soundPath = this.getBasePath() +
            (this.getConfig().get('popupNotificationSound') || this.soundPath);

        this.on('render', () => {
            $(containerSelector).remove();

            const className = 'popup-notification-' + Espo.Utils.toDom(this.type);

            $('<div>')
                .attr('id', id)
                .addClass('popup-notification')
                .addClass(className)
                .addClass('popup-notification-' + this.style)
                .appendTo('#popup-notifications-container');

            this.setElement(containerSelector);
        });

        this.on('after:render', () => {
            this.$el.find('[data-action="close"]').on('click', () =>{
                this.resolveCancel();
            });
        });

        this.once('after:render', () => {
            this.onShow();
        });

        this.once('remove', function () {
            $(containerSelector).remove();
        });

        this.notificationData = this.options.notificationData;
        this.notificationId = this.options.notificationId;
        this.id = this.options.id;
    }

    data() {
        return {
            closeButton: this.closeButton,
            notificationData: this.notificationData,
            notificationId: this.notificationId,
        };
    }

    playSound() {
        if (this.notificationSoundsDisabled) {
            return;
        }

        const html =
            '<audio autoplay="autoplay">' +
            '<source src="' + this.soundPath + '.mp3" type="audio/mpeg" />' +
            '<source src="' + this.soundPath + '.ogg" type="audio/ogg" />' +
            '<embed hidden="true" autostart="true" loop="false" src="' + this.soundPath + '.mp3" />' +
            '</audio>';

        const $audio = $(html);

        $audio.get(0).volume = 0.3;
        
        $audio.get(0).play();
    }

    
    onShow() {
        if (!this.options.isFirstCheck) {
            this.playSound();
        }
    }

    
    onConfirm() {}

    
    onCancel() {}

    resolveConfirm() {
        this.onConfirm();
        this.trigger('confirm');
        this.remove();
    }

    resolveCancel() {
        this.onCancel();
        this.trigger('cancel');
        this.remove();
    }

    
    
    confirm() {
        console.warn(`Method 'confirm' in views/popup-notification is deprecated. Use 'resolveConfirm' instead.`);

        this.resolveConfirm();
    }

    
    cancel() {
        console.warn(`Method 'cancel' in views/popup-notification is deprecated. Use 'resolveCancel' instead.`);

        this.resolveCancel();
    }
}

export default PopupNotificationView;
