

import BaseFieldView from 'views/fields/base';

class MapFieldView extends BaseFieldView {

    type = 'map'

    detailTemplate = 'fields/map/detail'
    listTemplate = 'fields/map/detail'

    
    addressField
    
    provider
    height = 300

    DEFAULT_PROVIDER = 'Google';

    
    data() {
        const data = super.data();

        data.hasAddress = this.hasAddress();

        
        return data;
    }

    setup() {
        this.addressField = this.name.slice(0, this.name.length - 3);

        this.provider = this.provider || this.getConfig().get('mapProvider') || this.DEFAULT_PROVIDER;
        this.height = this.options.height || this.params.height || this.height;

        const addressAttributeList = Object.keys(this.getMetadata().get('fields.address.fields') || {})
            .map(a => this.addressField + Espo.Utils.upperCaseFirst(a));

        this.listenTo(this.model, 'sync', model => {
            let isChanged = false;

            addressAttributeList.forEach(attribute => {
                if (model.hasChanged(attribute)) {
                    isChanged = true;
                }
            });

            if (isChanged && this.isRendered()) {
                this.reRender();
            }
        });

        this.listenTo(this.model, 'after:save', () => {
            if (this.isRendered()) {
                this.reRender();
            }
        });
    }

    hasAddress() {
        return !!this.model.get(this.addressField + 'City') ||
            !!this.model.get(this.addressField + 'PostalCode');
    }

    onRemove() {
        $(window).off('resize.' + this.cid);
    }

    afterRender() {
        this.addressData = {
            city: this.model.get(this.addressField + 'City'),
            street: this.model.get(this.addressField + 'Street'),
            postalCode: this.model.get(this.addressField + 'PostalCode'),
            country: this.model.get(this.addressField + 'Country'),
            state: this.model.get(this.addressField + 'State'),
        };

        this.$map = this.$el.find('.map');

        if (this.hasAddress()) {
            this.renderMap();
        }
    }

    renderMap() {
        this.processSetHeight(true);

        if (this.height === 'auto') {
            $(window).off('resize.' + this.cid);
            $(window).on('resize.' + this.cid, this.processSetHeight.bind(this));
        }

        const rendererId = this.getMetadata().get(['app', 'mapProviders', this.provider, 'renderer']);

        if (rendererId) {
            Espo.loader.require(rendererId, Renderer => {
                (new Renderer(this)).render(this.addressData);
            });

            return;
        }

        const methodName = 'afterRender' + this.provider.replace(/\s+/g, '');

        if (typeof this[methodName] === 'function') {
            this[methodName]();

            return;
        }

        
        
        const implId = this.getMetadata().get(['clientDefs', 'AddressMap', 'implementations', this.provider]);

        if (implId) {
            Espo.loader.require(implId, impl => impl.render(this));
        }
    }

    processSetHeight(init) {
        let height = this.height;

        if (this.height === 'auto') {
            height = this.$el.parent().height();

            if (init && height <= 0) {
                setTimeout(() => this.processSetHeight(true), 50);

                return;
            }
        }

        this.$map.css('height', height + 'px');
    }
}

export default MapFieldView;
