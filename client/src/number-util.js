




class NumberUtil {

    
    constructor(config, preferences) {
        
        this.config = config;

        
        this.preferences = preferences;

        
        this.thousandSeparator = null;

        
        this.decimalMark = null;

        this.config.on('change', () => {
            this.thousandSeparator = null;
            this.decimalMark = null;
        });

        this.preferences.on('change', () => {
            this.thousandSeparator = null;
            this.decimalMark = null;
        });

        
        this.maxDecimalPlaces = 10;
    }

    
    formatInt(value) {
        if (value === null || value === undefined) {
            return '';
        }

        let stringValue = value.toString();

        stringValue = stringValue.replace(/\B(?=(\d{3})+(?!\d))/g, this.getThousandSeparator());

        return stringValue;
    }

    
    
    formatFloat(value, decimalPlaces) {
        if (value === null || value === undefined) {
            return '';
        }

        if (decimalPlaces === 0) {
            value = Math.round(value);
        }
        else if (decimalPlaces) {
            value = Math.round(value * Math.pow(10, decimalPlaces)) / (Math.pow(10, decimalPlaces));
        }
        else {
            value = Math.round(
                value * Math.pow(10, this.maxDecimalPlaces)) / (Math.pow(10, this.maxDecimalPlaces)
            );
        }

        const parts = value.toString().split('.');

        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, this.getThousandSeparator());

        if (decimalPlaces === 0) {
            return parts[0];
        }

        if (decimalPlaces) {
            let decimalPartLength = 0;

            if (parts.length > 1) {
                decimalPartLength = parts[1].length;
            }
            else {
                parts[1] = '';
            }

            if (decimalPlaces && decimalPartLength < decimalPlaces) {
                const limit = decimalPlaces - decimalPartLength;

                for (let i = 0; i < limit; i++) {
                    parts[1] += '0';
                }
            }
        }

        return parts.join(this.getDecimalMark());
    }

    
    getThousandSeparator() {
        if (this.thousandSeparator !== null) {
            return this.thousandSeparator;
        }

        let thousandSeparator = '.';

        if (this.preferences.has('thousandSeparator')) {
            thousandSeparator = this.preferences.get('thousandSeparator');
        }
        else if (this.config.has('thousandSeparator')) {
            thousandSeparator = this.config.get('thousandSeparator');
        }

        
        this.thousandSeparator = thousandSeparator;

        return thousandSeparator;
    }

    
    getDecimalMark() {
        if (this.decimalMark !== null) {
            return this.decimalMark;
        }

        let decimalMark = '.';

        if (this.preferences.has('decimalMark')) {
            decimalMark = this.preferences.get('decimalMark');
        }
        else {
            if (this.config.has('decimalMark')) {
                decimalMark = this.config.get('decimalMark');
            }
        }

        
        this.decimalMark = decimalMark;

        return decimalMark;
    }
}

export default NumberUtil;
