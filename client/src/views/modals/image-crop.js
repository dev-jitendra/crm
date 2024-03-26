

import ModalView from 'views/modal';

class ImageCropModalView extends ModalView {

    template = 'modals/image-crop'

    cssName = 'image-crop'

    events = {
        
        'click [data-action="zoomIn"]': function () {
            this.$img.cropper('zoom', 0.1);
        },
        
        'click [data-action="zoomOut"]': function () {
            this.$img.cropper('zoom', -0.1);
        },
    }

    setup() {
        this.buttonList = [
            {
                name: 'crop',
                label: 'Submit',
                style: 'primary',
            },
            {
                name: 'cancel',
                label: 'Cancel',
            },
        ];

        this.wait(
            Espo.loader.requirePromise('lib!cropper')
        );

        this.on('remove', () => {
            if (this.$img.length) {
                this.$img.cropper('destroy');
                this.$img.parent().empty();
            }
        });
    }

    afterRender() {
        
        let $img = this.$img = $(`<img>`)
            .attr('src', this.options.contents)
            .addClass('hidden');

        this.$el.find('.image-container').append($img);

        setTimeout(() => {
            $img.cropper({
                aspectRatio: 1,
                movable: true,
                resizable: true,
                rotatable: false,
            });
        }, 50);
    }

    
    actionCrop() {
        let dataUrl = this.$img.cropper('getDataURL', 'image/jpeg');

        this.trigger('crop', dataUrl);
        this.close();
    }
}

export default ImageCropModalView;
