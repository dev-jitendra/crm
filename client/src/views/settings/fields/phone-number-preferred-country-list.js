

import MultiEnumFieldView from 'views/fields/multi-enum';

import intlTelInputGlobals from 'intl-tel-input-globals';

class SettingsPhoneNumberPreferredCountryListFieldView extends MultiEnumFieldView {

    setupOptions() {
        try {
            const dataList = intlTelInputGlobals.getCountryData();

            this.params.options = dataList
                .map(item => item.iso2);

            this.translatedOptions = dataList.reduce((map, item) => {
                map[item.iso2] = `${item.iso2.toUpperCase()} +${item.dialCode}`;

                return map;
            }, {});
        }
        catch (e) {
            console.error(e);
        }
    }
}


export default SettingsPhoneNumberPreferredCountryListFieldView;
