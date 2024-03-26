

import ModalView from 'views/modal';

class ViewMapModalView extends ModalView {

    templateContent = `<div class="map-container no-side-margin">{{{map}}}</div>`

    backdrop = true

    setup() {
        const field = this.options.field;

        const url = '#AddressMap/view/' + this.model.entityType + '/' + this.model.id + '/' + field;
        const fieldLabel = this.translate(field, 'fields', this.model.entityType);

        this.headerElement =
            $('<a>')
                .attr('href', '#' + url)
                .text(fieldLabel)
                .get(0);

        const viewName = this.model.getFieldParam(field + 'Map', 'view') ||
            this.getFieldManager().getViewName('map');

        this.createView('map', viewName, {
            model: this.model,
            name: field + 'Map',
            selector: '.map-container',
            height: 'auto',
        });
    }
}

export default ViewMapModalView;
