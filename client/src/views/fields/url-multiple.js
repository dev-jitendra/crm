

import ArrayFieldView from 'views/fields/array';


class UrlMultipleFieldView extends ArrayFieldView {

    type = 'urlMultiple'

    maxItemLength = 255
    displayAsList = true
    defaultProtocol = 'https:'

    setup() {
        super.setup();

        this.noEmptyString = true;
        this.params.pattern = '$uriOptionalProtocol';
    }

    addValueFromUi(value) {
        value = value.trim();

        if (this.params.strip) {
            value = this.strip(value);
        }

        if (value === decodeURI(value)) {
            value = encodeURI(value);
        }

        super.addValueFromUi(value);
    }

    
    strip(value) {
        if (value.indexOf('
            value = value.substring(value.indexOf('
        }

        value = value.replace(/\/+$/, '');

        return value;
    }

    prepareUrl(url) {
        if (url.indexOf('
            url = this.defaultProtocol + '
        }

        return url;
    }

    getValueForDisplay() {
        
        let $list = this.selected.map(value => {
            return $('<a>')
                .attr('href', this.prepareUrl(value))
                .attr('target', '_blank')
                .text(decodeURI(value));
        });

        return $list
            .map($item =>
                $('<div>')
                    .addClass('multi-enum-item-container')
                    .append($item)
                    .get(0).outerHTML
            )
            .join('');
    }

    getItemHtml(value) {
        let html = super.getItemHtml(value);

        let $item = $(html);

        $item.find('span.text').html(
            $('<a>')
                .attr('href', this.prepareUrl(value))
                .css('user-drag', 'none')
                .attr('target', '_blank')
                .text(decodeURI(value))
        );

        return $item.get(0).outerHTML;
    }
}

export default UrlMultipleFieldView;
