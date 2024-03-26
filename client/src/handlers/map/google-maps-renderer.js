

import MapRenderer from 'handlers/map/renderer';

class GoogleMapsRenderer extends MapRenderer {

    
    render(addressData) {
        if ('google' in window && window.google.maps) {
            this.initMapGoogle(addressData);

            return;
        }

        
        if (typeof window.mapapiloaded === 'function') {
            
            const mapapiloaded = window.mapapiloaded;

            
            window.mapapiloaded = () => {
                this.initMapGoogle(addressData);

                mapapiloaded();
            };

            return;
        }

        
        window.mapapiloaded = () => this.initMapGoogle(addressData);

        let src = 'https:
        const apiKey = this.view.getConfig().get('googleMapsApiKey');

        if (apiKey) {
            src += '&key=' + apiKey;
        }

        const scriptElement = document.createElement('script');

        scriptElement.setAttribute('async', 'async');
        scriptElement.src = src;

        document.head.appendChild(scriptElement);
    }

    
    initMapGoogle(addressData) {
        
        const geocoder = new google.maps.Geocoder();
        let map;

        try {
            
            map = new google.maps.Map(this.view.$el.find('.map').get(0), {
                zoom: 15,
                center: {lat: 0, lng: 0},
                scrollwheel: false,
            });
        }
        catch (e) {
            console.error(e.message);

            return;
        }

        let address = '';

        if (addressData.street) {
            address += addressData.street;
        }

        if (addressData.city) {
            if (address !== '') {
                address += ', ';
            }

            address += addressData.city;
        }

        if (addressData.state) {
            if (address !== '') {
                address += ', ';
            }

            address += addressData.state;
        }

        if (addressData.postalCode) {
            if (addressData.state || addressData.city) {
                address += ' ';
            }
            else {
                if (address) {
                    address += ', ';
                }
            }

            address += addressData.postalCode;
        }

        if (addressData.country) {
            if (address !== '') {
                address += ', ';
            }

            address += addressData.country;
        }

        
        geocoder.geocode({'address': address}, (results, status) => {
            
            if (status === google.maps.GeocoderStatus.OK) {
                
                map.setCenter(results[0].geometry.location);

                
                new google.maps.Marker({
                    map: map,
                    position: results[0].geometry.location,
                });
            }
        });
    }
}


export default GoogleMapsRenderer;

