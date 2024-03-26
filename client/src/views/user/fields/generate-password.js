

define('views/user/fields/generate-password', ['views/fields/base'], function (Dep) {

    return Dep.extend({

        templateContent: '<button type="button" class="btn btn-default" data-action="generatePassword">' +
            '{{translate \'Generate\' scope=\'User\'}}</button>',

        events: {
            'click [data-action="generatePassword"]': function () {
                this.actionGeneratePassword();
            },
        },

        setup: function () {
            Dep.prototype.setup.call(this);

            this.listenTo(this.model, 'change:password', (model, value, o) => {
                if (o.isGenerated) {
                    return;
                }

                this.model.set({
                    passwordPreview: '',
                });
            });

            this.strengthParams = this.options.strengthParams || {};

            this.passwordStrengthLength = this.strengthParams.passwordStrengthLength ||
                this.getConfig().get('passwordStrengthLength');

            this.passwordStrengthLetterCount = this.strengthParams.passwordStrengthLetterCount ||
                this.getConfig().get('passwordStrengthLetterCount');

            this.passwordStrengthNumberCount = this.strengthParams.passwordStrengthNumberCount ||
                this.getConfig().get('passwordStrengthNumberCount');

            this.passwordGenerateLength = this.strengthParams.passwordGenerateLength ||
                this.getConfig().get('passwordGenerateLength');

            this.passwordGenerateLetterCount = this.strengthParams.passwordGenerateLetterCount ||
                this.getConfig().get('passwordGenerateLetterCount');

            this.passwordGenerateNumberCount = this.strengthParams.passwordGenerateNumberCount ||
                this.getConfig().get('passwordGenerateNumberCount');
        },

        fetch: function () {
            return {};
        },

        actionGeneratePassword: function () {
            var length = this.passwordStrengthLength;
            var letterCount = this.passwordStrengthLetterCount;
            var numberCount = this.passwordStrengthNumberCount;

            var generateLength = this.passwordGenerateLength || 10;
            var generateLetterCount = this.passwordGenerateLetterCount || 4;
            var generateNumberCount = this.passwordGenerateNumberCount || 2;

            length = (typeof length === 'undefined') ? generateLength : length;
            letterCount = (typeof letterCount === 'undefined') ? generateLetterCount : letterCount;
            numberCount = (typeof numberCount === 'undefined') ? generateNumberCount : numberCount;

            if (length < generateLength) length = generateLength;
            if (letterCount < generateLetterCount) letterCount = generateLetterCount;
            if (numberCount < generateNumberCount) numberCount = generateNumberCount;

            var password = this.generatePassword(length, letterCount, numberCount, true);

            this.model.set({
                password: password,
                passwordConfirm: password,
                passwordPreview: password,
            }, {isGenerated: true});
        },

        generatePassword: function (length, letters, numbers, bothCases) {
            var chars = [
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz',
                '0123456789',
                'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
                'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
                'abcdefghijklmnopqrstuvwxyz',
            ];

            var upperCase = 0;
            var lowerCase = 0;

            if (bothCases) {
                upperCase = 1;
                lowerCase = 1;

                if (letters >= 2) {
                    letters = letters - 2;
                } else {
                    letters = 0;
                }
            }

            var either = length - (letters + numbers + upperCase + lowerCase);

            if (either < 0) {
                either = 0;
            }

            var setList = [letters, numbers, either, upperCase, lowerCase];

            var shuffle = function (array) {
                var currentIndex = array.length, temporaryValue, randomIndex;

                while (0 !== currentIndex) {
                    randomIndex = Math.floor(Math.random() * currentIndex);
                    currentIndex -= 1;
                    temporaryValue = array[currentIndex];
                    array[currentIndex] = array[randomIndex];
                    array[randomIndex] = temporaryValue;
                }

                return array;
            };

            var array = setList.map(
                function (len, i) {
                    return Array(len).fill(chars[i]).map(
                        function (x) {
                            return x[Math.floor(Math.random() * x.length)];
                        }
                    ).join('');
                }
            ).concat();

            return shuffle(array).join('');
        },

    });
});
