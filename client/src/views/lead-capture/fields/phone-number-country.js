

import EnumFieldView from 'views/fields/enum';

import intlTelInputGlobals from 'intl-tel-input-globals';

class LeadCapturePhoneNumberCountry extends EnumFieldView {

    setupOptions() {
        this.params.options = ['', ...intlTelInputGlobals.getCountryData().map(item => item.iso2)];

        this.translatedOptions = intlTelInputGlobals.getCountryData()
            .reduce((map, item) => {
                map[item.iso2] = `${item.iso2.toUpperCase()} +${item.dialCode}`;

                return map;
            }, {});
    }
}

export default LeadCapturePhoneNumberCountry;
