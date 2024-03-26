Espo.loader.setContextId('lib!autonumeric');
(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory();
	else if(typeof define === 'function' && define.amd)
		define([], factory);
	else if(typeof exports === 'object')
		exports["AutoNumeric"] = factory();
	else
		root["AutoNumeric"] = factory();
})(this, function() {
return  (function(modules) { 
 	
 	var installedModules = {};

 	
 	function __webpack_require__(moduleId) {

 		
 		if(installedModules[moduleId]) {
 			return installedModules[moduleId].exports;
 		}
 		
 		var module = installedModules[moduleId] = {
 			i: moduleId,
 			l: false,
 			exports: {}
 		};

 		
 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

 		
 		module.l = true;

 		
 		return module.exports;
 	}


 	
 	__webpack_require__.m = modules;

 	
 	__webpack_require__.c = installedModules;

 	
 	__webpack_require__.d = function(exports, name, getter) {
 		if(!__webpack_require__.o(exports, name)) {
 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
 		}
 	};

 	
 	__webpack_require__.r = function(exports) {
 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
 		}
 		Object.defineProperty(exports, '__esModule', { value: true });
 	};

 	
 	
 	
 	
 	
 	__webpack_require__.t = function(value, mode) {
 		if(mode & 1) value = __webpack_require__(value);
 		if(mode & 8) return value;
 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
 		var ns = Object.create(null);
 		__webpack_require__.r(ns);
 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
 		return ns;
 	};

 	
 	__webpack_require__.n = function(module) {
 		var getter = module && module.__esModule ?
 			function getDefault() { return module['default']; } :
 			function getModuleExports() { return module; };
 		__webpack_require__.d(getter, 'a', getter);
 		return getter;
 	};

 	
 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };

 	
 	__webpack_require__.p = "";


 	
 	return __webpack_require__(__webpack_require__.s = "./src/main.js");
 })

 ({

 "./src/AutoNumeric.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
 __webpack_require__.d(__webpack_exports__, "default", function() { return AutoNumeric; });
 var _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__( "./src/AutoNumericHelper.js");
 var _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__( "./src/AutoNumericEnum.js");
 var _maths_Evaluator__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__( "./src/maths/Evaluator.js");
 var _maths_Parser__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__( "./src/maths/Parser.js");
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(n); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return; var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }









var AutoNumeric = function () {
  
  function AutoNumeric() {
    var _this = this;

    var arg1 = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
    var arg2 = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
    var arg3 = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;

    _classCallCheck(this, AutoNumeric);

    
    
    
    var _AutoNumeric$_setArgu = AutoNumeric._setArgumentsValues(arg1, arg2, arg3),
        domElement = _AutoNumeric$_setArgu.domElement,
        initialValue = _AutoNumeric$_setArgu.initialValue,
        userOptions = _AutoNumeric$_setArgu.userOptions; 


    this.domElement = domElement; 

    this.defaultRawValue = ''; 

    this._setSettings(userOptions, false); 
    


    this._checkElement(); 
    


    this.savedCancellableValue = null; 

    this.historyTable = []; 

    this.historyTableIndex = -1; 

    this.onGoingRedo = false; 
    

    this.parentForm = this._getParentForm(); 

    if (!this.runOnce && this.settings.formatOnPageLoad) {
      
      this._formatDefaultValueOnPageLoad(initialValue);
    } else {
      
      var valueToSet;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(initialValue)) {
        switch (this.settings.emptyInputBehavior) {
          case AutoNumeric.options.emptyInputBehavior.min:
            valueToSet = this.settings.minimumValue;
            break;

          case AutoNumeric.options.emptyInputBehavior.max:
            valueToSet = this.settings.maximumValue;
            break;

          case AutoNumeric.options.emptyInputBehavior.zero:
            valueToSet = '0';
            break;
          

          case AutoNumeric.options.emptyInputBehavior.focus:
          case AutoNumeric.options.emptyInputBehavior.press:
          case AutoNumeric.options.emptyInputBehavior.always:
          case AutoNumeric.options.emptyInputBehavior["null"]:
            valueToSet = '';
            break;
          

          default:
            valueToSet = this.settings.emptyInputBehavior;
        }
      } else {
        valueToSet = initialValue;
      }

      this._setElementAndRawValue(valueToSet);
    }

    this.runOnce = true; 

    this.hasEventListeners = false;

    if (this.isInputElement || this.isContentEditable) {
      if (!this.settings.noEventListeners) {
        
        this._createEventListeners();
      }

      this._setWritePermissions(true);
    } 


    this._saveInitialValues(initialValue); 


    this.sessionStorageAvailable = this.constructor._storageTest();
    this.storageNamePrefix = 'AUTO_'; 

    this._setPersistentStorageName(); 
    


    this.validState = true; 

    this.isFocused = false; 

    this.isWheelEvent = false; 

    this.isDropEvent = false; 

    this.isEditing = false; 

    this.rawValueOnFocus = void 0; 
    

    this.internalModification = false; 

    this.attributeToWatch = this._getAttributeToWatch();
    this.getterSetter = Object.getOwnPropertyDescriptor(this.domElement.__proto__, this.attributeToWatch);

    this._addWatcher();

    if (this.settings.createLocalList) {
      
      this._createLocalList();
    } 


    this.constructor._addToGlobalList(this); 
    
    


    this.global = {
      
      set: function set(newValue) {
        var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

        _this.autoNumericLocalList.forEach(function (aNObject) {
          aNObject.set(newValue, options);
        });
      },

      
      setUnformatted: function setUnformatted(value) {
        var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

        _this.autoNumericLocalList.forEach(function (aNObject) {
          aNObject.setUnformatted(value, options);
        });
      },

      
      get: function get() {
        var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        var result = [];

        _this.autoNumericLocalList.forEach(function (aNObject) {
          result.push(aNObject.get());
        });

        _this._executeCallback(result, callback);

        return result;
      },

      
      getNumericString: function getNumericString() {
        var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        var result = [];

        _this.autoNumericLocalList.forEach(function (aNObject) {
          result.push(aNObject.getNumericString());
        });

        _this._executeCallback(result, callback);

        return result;
      },

      
      getFormatted: function getFormatted() {
        var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        var result = [];

        _this.autoNumericLocalList.forEach(function (aNObject) {
          result.push(aNObject.getFormatted());
        });

        _this._executeCallback(result, callback);

        return result;
      },

      
      getNumber: function getNumber() {
        var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        var result = [];

        _this.autoNumericLocalList.forEach(function (aNObject) {
          result.push(aNObject.getNumber());
        });

        _this._executeCallback(result, callback);

        return result;
      },

      
      getLocalized: function getLocalized() {
        var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
        var result = [];

        _this.autoNumericLocalList.forEach(function (aNObject) {
          result.push(aNObject.getLocalized());
        });

        _this._executeCallback(result, callback);

        return result;
      },

      
      reformat: function reformat() {
        _this.autoNumericLocalList.forEach(function (aNObject) {
          aNObject.reformat();
        });
      },

      
      unformat: function unformat() {
        _this.autoNumericLocalList.forEach(function (aNObject) {
          aNObject.unformat();
        });
      },

      
      unformatLocalized: function unformatLocalized() {
        var forcedOutputFormat = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

        _this.autoNumericLocalList.forEach(function (aNObject) {
          aNObject.unformatLocalized(forcedOutputFormat);
        });
      },

      
      update: function update() {
        for (var _len = arguments.length, newOptions = new Array(_len), _key = 0; _key < _len; _key++) {
          newOptions[_key] = arguments[_key];
        }

        _this.autoNumericLocalList.forEach(function (aNObject) {
          aNObject.update.apply(aNObject, newOptions);
        });
      },

      
      isPristine: function isPristine() {
        var checkOnlyRawValue = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
        var isPristine = true;

        _this.autoNumericLocalList.forEach(function (aNObject) {
          if (isPristine && !aNObject.isPristine(checkOnlyRawValue)) {
            isPristine = false;
          }
        });

        return isPristine;
      },

      
      clear: function clear() {
        var forceClearAll = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

        _this.autoNumericLocalList.forEach(function (aNObject) {
          aNObject.clear(forceClearAll);
        });
      },

      
      remove: function remove() {
        _this.autoNumericLocalList.forEach(function (aNObject) {
          aNObject.remove();
        });
      },

      
      wipe: function wipe() {
        _this.autoNumericLocalList.forEach(function (aNObject) {
          aNObject.wipe();
        });
      },

      
      nuke: function nuke() {
        _this.autoNumericLocalList.forEach(function (aNObject) {
          aNObject.nuke();
        });
      },

      
      has: function has(domElementOrAutoNumericObject) {
        var result;

        if (domElementOrAutoNumericObject instanceof AutoNumeric) {
          result = _this.autoNumericLocalList.has(domElementOrAutoNumericObject.node());
        } else {
          result = _this.autoNumericLocalList.has(domElementOrAutoNumericObject);
        }

        return result;
      },

      
      addObject: function addObject(domElementOrAutoNumericObject) {
        
        var domElement;
        var otherAutoNumericObject;

        if (domElementOrAutoNumericObject instanceof AutoNumeric) {
          domElement = domElementOrAutoNumericObject.node();
          otherAutoNumericObject = domElementOrAutoNumericObject;
        } else {
          domElement = domElementOrAutoNumericObject;
          otherAutoNumericObject = AutoNumeric.getAutoNumericElement(domElement);
        } 


        if (!_this._hasLocalList()) {
          _this._createLocalList();
        } 


        var otherANLocalList = otherAutoNumericObject._getLocalList();

        if (otherANLocalList.size === 0) {
          
          otherAutoNumericObject._createLocalList();

          otherANLocalList = otherAutoNumericObject._getLocalList(); 
        }

        var mergedLocalLists;

        if (otherANLocalList instanceof Map) {
          
          mergedLocalLists = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].mergeMaps(_this._getLocalList(), otherANLocalList);
        } else {
          
          
          _this._addToLocalList(domElement, otherAutoNumericObject);

          mergedLocalLists = _this._getLocalList();
        } 


        mergedLocalLists.forEach(function (aNObject) {
          aNObject._setLocalList(mergedLocalLists);
        });
      },

      
      removeObject: function removeObject(domElementOrAutoNumericObject) {
        var keepCurrentANObject = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
        
        var domElement;
        var otherAutoNumericObject;

        if (domElementOrAutoNumericObject instanceof AutoNumeric) {
          domElement = domElementOrAutoNumericObject.node();
          otherAutoNumericObject = domElementOrAutoNumericObject;
        } else {
          domElement = domElementOrAutoNumericObject;
          otherAutoNumericObject = AutoNumeric.getAutoNumericElement(domElement);
        } 


        var initialCompleteLocalList = _this.autoNumericLocalList;

        _this.autoNumericLocalList["delete"](domElement); 


        initialCompleteLocalList.forEach(function (aNObject) {
          aNObject._setLocalList(_this.autoNumericLocalList);
        });

        if (!keepCurrentANObject && domElement === _this.node()) {
          
          
          otherAutoNumericObject._setLocalList(new Map());
        } else {
          
          
          otherAutoNumericObject._createLocalList();
        }
      },

      
      empty: function empty() {
        var keepEachANObjectInItsOwnList = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
        var initialCompleteLocalList = _this.autoNumericLocalList; 

        initialCompleteLocalList.forEach(function (aNObject) {
          if (keepEachANObjectInItsOwnList) {
            aNObject._createLocalList();
          } else {
            aNObject._setLocalList(new Map());
          }
        });
      },

      
      elements: function elements() {
        var result = [];

        _this.autoNumericLocalList.forEach(function (aNObject) {
          result.push(aNObject.node());
        });

        return result;
      },

      
      getList: function getList() {
        return _this.autoNumericLocalList;
      },

      
      size: function size() {
        return _this.autoNumericLocalList.size;
      }
    }; 

    

    this.options = {
      
      reset: function reset() {
        
        _this.settings = {
          rawValue: _this.defaultRawValue
        }; 

        _this.update(AutoNumeric.defaultSettings);

        return _this;
      },
      allowDecimalPadding: function allowDecimalPadding(_allowDecimalPadding) {
        _this.update({
          allowDecimalPadding: _allowDecimalPadding
        });

        return _this;
      },
      alwaysAllowDecimalCharacter: function alwaysAllowDecimalCharacter(_alwaysAllowDecimalCharacter) {
        
        _this.update({
          alwaysAllowDecimalCharacter: _alwaysAllowDecimalCharacter
        });

        return _this;
      },
      caretPositionOnFocus: function caretPositionOnFocus(_caretPositionOnFocus) {
        
        _this.settings.caretPositionOnFocus = _caretPositionOnFocus;
        return _this;
      },
      createLocalList: function createLocalList(_createLocalList2) {
        _this.settings.createLocalList = _createLocalList2; 

        if (_this.settings.createLocalList) {
          if (!_this._hasLocalList()) {
            _this._createLocalList();
          }
        } else {
          _this._deleteLocalList();
        }

        return _this;
      },
      currencySymbol: function currencySymbol(_currencySymbol) {
        _this.update({
          currencySymbol: _currencySymbol
        });

        return _this;
      },
      currencySymbolPlacement: function currencySymbolPlacement(_currencySymbolPlacement) {
        _this.update({
          currencySymbolPlacement: _currencySymbolPlacement
        });

        return _this;
      },
      decimalCharacter: function decimalCharacter(_decimalCharacter) {
        _this.update({
          decimalCharacter: _decimalCharacter
        });

        return _this;
      },
      decimalCharacterAlternative: function decimalCharacterAlternative(_decimalCharacterAlternative) {
        _this.settings.decimalCharacterAlternative = _decimalCharacterAlternative;
        return _this;
      },

      
      decimalPlaces: function decimalPlaces(_decimalPlaces) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning('Using `options.decimalPlaces()` instead of calling the specific `options.decimalPlacesRawValue()`, `options.decimalPlacesShownOnFocus()` and `options.decimalPlacesShownOnBlur()` methods will reset those options.\nPlease call the specific methods if you do not want to reset those.', _this.settings.showWarnings);

        _this.update({
          decimalPlaces: _decimalPlaces
        });

        return _this;
      },
      decimalPlacesRawValue: function decimalPlacesRawValue(_decimalPlacesRawValue) {
        
        _this.update({
          decimalPlacesRawValue: _decimalPlacesRawValue
        });

        return _this;
      },
      decimalPlacesShownOnBlur: function decimalPlacesShownOnBlur(_decimalPlacesShownOnBlur) {
        _this.update({
          decimalPlacesShownOnBlur: _decimalPlacesShownOnBlur
        });

        return _this;
      },
      decimalPlacesShownOnFocus: function decimalPlacesShownOnFocus(_decimalPlacesShownOnFocus) {
        _this.update({
          decimalPlacesShownOnFocus: _decimalPlacesShownOnFocus
        });

        return _this;
      },
      defaultValueOverride: function defaultValueOverride(_defaultValueOverride) {
        _this.update({
          defaultValueOverride: _defaultValueOverride
        });

        return _this;
      },
      digitalGroupSpacing: function digitalGroupSpacing(_digitalGroupSpacing) {
        _this.update({
          digitalGroupSpacing: _digitalGroupSpacing
        });

        return _this;
      },
      digitGroupSeparator: function digitGroupSeparator(_digitGroupSeparator) {
        _this.update({
          digitGroupSeparator: _digitGroupSeparator
        });

        return _this;
      },
      divisorWhenUnfocused: function divisorWhenUnfocused(_divisorWhenUnfocused) {
        _this.update({
          divisorWhenUnfocused: _divisorWhenUnfocused
        });

        return _this;
      },
      emptyInputBehavior: function emptyInputBehavior(_emptyInputBehavior) {
        if (_this.rawValue === null && _emptyInputBehavior !== AutoNumeric.options.emptyInputBehavior["null"]) {
          
          
          
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("You are trying to modify the `emptyInputBehavior` option to something different than `'null'` (".concat(_emptyInputBehavior, "), but the element raw value is currently set to `null`. This would result in an invalid `rawValue`. In order to fix that, the element value has been changed to the empty string `''`."), _this.settings.showWarnings);
          _this.rawValue = '';
        }

        _this.update({
          emptyInputBehavior: _emptyInputBehavior
        });

        return _this;
      },
      eventBubbles: function eventBubbles(_eventBubbles) {
        _this.settings.eventBubbles = _eventBubbles;
        return _this;
      },
      eventIsCancelable: function eventIsCancelable(_eventIsCancelable) {
        _this.settings.eventIsCancelable = _eventIsCancelable;
        return _this;
      },
      failOnUnknownOption: function failOnUnknownOption(_failOnUnknownOption) {
        _this.settings.failOnUnknownOption = _failOnUnknownOption; 

        return _this;
      },
      formatOnPageLoad: function formatOnPageLoad(_formatOnPageLoad) {
        _this.settings.formatOnPageLoad = _formatOnPageLoad; 

        return _this;
      },
      formulaMode: function formulaMode(_formulaMode) {
        _this.settings.formulaMode = _formulaMode; 

        return _this;
      },
      historySize: function historySize(_historySize) {
        _this.settings.historySize = _historySize;
        return _this;
      },
      invalidClass: function invalidClass(_invalidClass) {
        _this.settings.invalidClass = _invalidClass; 

        return _this;
      },
      isCancellable: function isCancellable(_isCancellable) {
        _this.settings.isCancellable = _isCancellable; 

        return _this;
      },
      leadingZero: function leadingZero(_leadingZero) {
        _this.update({
          leadingZero: _leadingZero
        });

        return _this;
      },
      maximumValue: function maximumValue(_maximumValue) {
        _this.update({
          maximumValue: _maximumValue
        });

        return _this;
      },
      minimumValue: function minimumValue(_minimumValue) {
        _this.update({
          minimumValue: _minimumValue
        });

        return _this;
      },
      modifyValueOnWheel: function modifyValueOnWheel(_modifyValueOnWheel) {
        _this.settings.modifyValueOnWheel = _modifyValueOnWheel; 

        return _this;
      },
      negativeBracketsTypeOnBlur: function negativeBracketsTypeOnBlur(_negativeBracketsTypeOnBlur) {
        _this.update({
          negativeBracketsTypeOnBlur: _negativeBracketsTypeOnBlur
        });

        return _this;
      },
      negativePositiveSignPlacement: function negativePositiveSignPlacement(_negativePositiveSignPlacement) {
        _this.update({
          negativePositiveSignPlacement: _negativePositiveSignPlacement
        });

        return _this;
      },
      negativeSignCharacter: function negativeSignCharacter(_negativeSignCharacter) {
        _this.update({
          negativeSignCharacter: _negativeSignCharacter
        });

        return _this;
      },
      noEventListeners: function noEventListeners(_noEventListeners) {
        
        if (_noEventListeners === AutoNumeric.options.noEventListeners.noEvents && _this.settings.noEventListeners === AutoNumeric.options.noEventListeners.addEvents) {
          
          _this._removeEventListeners();
        }

        _this.update({
          noEventListeners: _noEventListeners
        });

        return _this;
      },
      onInvalidPaste: function onInvalidPaste(_onInvalidPaste) {
        _this.settings.onInvalidPaste = _onInvalidPaste; 

        return _this;
      },
      outputFormat: function outputFormat(_outputFormat) {
        _this.settings.outputFormat = _outputFormat;
        return _this;
      },
      overrideMinMaxLimits: function overrideMinMaxLimits(_overrideMinMaxLimits) {
        _this.update({
          overrideMinMaxLimits: _overrideMinMaxLimits
        });

        return _this;
      },
      positiveSignCharacter: function positiveSignCharacter(_positiveSignCharacter) {
        _this.update({
          positiveSignCharacter: _positiveSignCharacter
        });

        return _this;
      },
      rawValueDivisor: function rawValueDivisor(_rawValueDivisor) {
        _this.update({
          rawValueDivisor: _rawValueDivisor
        });

        return _this;
      },
      readOnly: function readOnly(_readOnly) {
        
        _this.settings.readOnly = _readOnly;

        _this._setWritePermissions();

        return _this;
      },
      roundingMethod: function roundingMethod(_roundingMethod) {
        _this.update({
          roundingMethod: _roundingMethod
        });

        return _this;
      },
      saveValueToSessionStorage: function saveValueToSessionStorage(_saveValueToSessionStorage) {
        _this.update({
          saveValueToSessionStorage: _saveValueToSessionStorage
        });

        return _this;
      },
      symbolWhenUnfocused: function symbolWhenUnfocused(_symbolWhenUnfocused) {
        _this.update({
          symbolWhenUnfocused: _symbolWhenUnfocused
        });

        return _this;
      },
      selectNumberOnly: function selectNumberOnly(_selectNumberOnly) {
        _this.settings.selectNumberOnly = _selectNumberOnly; 

        return _this;
      },
      selectOnFocus: function selectOnFocus(_selectOnFocus) {
        _this.settings.selectOnFocus = _selectOnFocus; 

        return _this;
      },
      serializeSpaces: function serializeSpaces(_serializeSpaces) {
        _this.settings.serializeSpaces = _serializeSpaces; 

        return _this;
      },
      showOnlyNumbersOnFocus: function showOnlyNumbersOnFocus(_showOnlyNumbersOnFocus) {
        _this.update({
          showOnlyNumbersOnFocus: _showOnlyNumbersOnFocus
        });

        return _this;
      },
      showPositiveSign: function showPositiveSign(_showPositiveSign) {
        _this.update({
          showPositiveSign: _showPositiveSign
        });

        return _this;
      },
      showWarnings: function showWarnings(_showWarnings) {
        _this.settings.showWarnings = _showWarnings; 

        return _this;
      },
      styleRules: function styleRules(_styleRules) {
        _this.update({
          styleRules: _styleRules
        });

        return _this;
      },
      suffixText: function suffixText(_suffixText) {
        _this.update({
          suffixText: _suffixText
        });

        return _this;
      },
      unformatOnHover: function unformatOnHover(_unformatOnHover) {
        _this.settings.unformatOnHover = _unformatOnHover; 

        return _this;
      },
      unformatOnSubmit: function unformatOnSubmit(_unformatOnSubmit2) {
        _this.settings.unformatOnSubmit = _unformatOnSubmit2; 

        return _this;
      },
      valuesToStrings: function valuesToStrings(_valuesToStrings) {
        _this.update({
          valuesToStrings: _valuesToStrings
        });

        return _this;
      },
      watchExternalChanges: function watchExternalChanges(_watchExternalChanges) {
        
        _this.update({
          watchExternalChanges: _watchExternalChanges
        });

        return _this;
      },
      wheelOn: function wheelOn(_wheelOn) {
        _this.settings.wheelOn = _wheelOn; 

        return _this;
      },
      wheelStep: function wheelStep(_wheelStep) {
        _this.settings.wheelStep = _wheelStep; 

        return _this;
      }
    }; 
    

    this._triggerEvent(AutoNumeric.events.initialized, this.domElement, {
      newValue: _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement),
      newRawValue: this.rawValue,
      error: null,
      aNElement: this
    });
  }
  


  _createClass(AutoNumeric, [{
    key: "_saveInitialValues",

    
    value: function _saveInitialValues(initialValue) {
      
      
      this.initialValueHtmlAttribute = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].scientificToDecimal(this.domElement.getAttribute('value'));

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(this.initialValueHtmlAttribute)) {
        
        this.initialValueHtmlAttribute = '';
      } 


      this.initialValue = initialValue;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(this.initialValue)) {
        
        this.initialValue = '';
      }
    }
    

  }, {
    key: "_createEventListeners",
    value: function _createEventListeners() {
      var _this2 = this;

      this.formulaMode = false; 
      

      this._onFocusInFunc = function (e) {
        _this2._onFocusIn(e);
      };

      this._onFocusInAndMouseEnterFunc = function (e) {
        _this2._onFocusInAndMouseEnter(e);
      };

      this._onFocusFunc = function () {
        _this2._onFocus();
      };

      this._onKeydownFunc = function (e) {
        _this2._onKeydown(e);
      };

      this._onKeypressFunc = function (e) {
        _this2._onKeypress(e);
      };

      this._onKeyupFunc = function (e) {
        _this2._onKeyup(e);
      };

      this._onFocusOutAndMouseLeaveFunc = function (e) {
        _this2._onFocusOutAndMouseLeave(e);
      };

      this._onPasteFunc = function (e) {
        _this2._onPaste(e);
      };

      this._onWheelFunc = function (e) {
        _this2._onWheel(e);
      };

      this._onDropFunc = function (e) {
        _this2._onDrop(e);
      };

      this._onKeydownGlobalFunc = function (e) {
        _this2._onKeydownGlobal(e);
      };

      this._onKeyupGlobalFunc = function (e) {
        _this2._onKeyupGlobal(e);
      }; 


      this.domElement.addEventListener('focusin', this._onFocusInFunc, false);
      this.domElement.addEventListener('focus', this._onFocusInAndMouseEnterFunc, false);
      this.domElement.addEventListener('focus', this._onFocusFunc, false);
      this.domElement.addEventListener('mouseenter', this._onFocusInAndMouseEnterFunc, false);
      this.domElement.addEventListener('keydown', this._onKeydownFunc, false);
      this.domElement.addEventListener('keypress', this._onKeypressFunc, false);
      this.domElement.addEventListener('keyup', this._onKeyupFunc, false);
      this.domElement.addEventListener('blur', this._onFocusOutAndMouseLeaveFunc, false);
      this.domElement.addEventListener('mouseleave', this._onFocusOutAndMouseLeaveFunc, false);
      this.domElement.addEventListener('paste', this._onPasteFunc, false);
      this.domElement.addEventListener('wheel', this._onWheelFunc, false);
      this.domElement.addEventListener('drop', this._onDropFunc, false);

      this._setupFormListener(); 


      this.hasEventListeners = true; 

      if (!AutoNumeric._doesGlobalListExists()) {
        document.addEventListener('keydown', this._onKeydownGlobalFunc, false);
        document.addEventListener('keyup', this._onKeyupGlobalFunc, false);
      }
    }
    

  }, {
    key: "_removeEventListeners",
    value: function _removeEventListeners() {
      this.domElement.removeEventListener('focusin', this._onFocusInFunc, false);
      this.domElement.removeEventListener('focus', this._onFocusInAndMouseEnterFunc, false);
      this.domElement.removeEventListener('focus', this._onFocusFunc, false);
      this.domElement.removeEventListener('mouseenter', this._onFocusInAndMouseEnterFunc, false);
      this.domElement.removeEventListener('blur', this._onFocusOutAndMouseLeaveFunc, false);
      this.domElement.removeEventListener('mouseleave', this._onFocusOutAndMouseLeaveFunc, false);
      this.domElement.removeEventListener('keydown', this._onKeydownFunc, false);
      this.domElement.removeEventListener('keypress', this._onKeypressFunc, false);
      this.domElement.removeEventListener('keyup', this._onKeyupFunc, false);
      this.domElement.removeEventListener('paste', this._onPasteFunc, false);
      this.domElement.removeEventListener('wheel', this._onWheelFunc, false);
      this.domElement.removeEventListener('drop', this._onDropFunc, false);

      this._removeFormListener(); 


      this.hasEventListeners = false;
      document.removeEventListener('keydown', this._onKeydownGlobalFunc, false);
      document.removeEventListener('keyup', this._onKeyupGlobalFunc, false);
    }
    

  }, {
    key: "_updateEventListeners",
    value: function _updateEventListeners() {
      if (!this.settings.noEventListeners && !this.hasEventListeners) {
        
        
        this._createEventListeners();
      }

      if (this.settings.noEventListeners && this.hasEventListeners) {
        this._removeEventListeners();
      }
    }
    

  }, {
    key: "_setupFormListener",
    value: function _setupFormListener() {
      var _this3 = this;

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(this.parentForm)) {
        
        this._onFormSubmitFunc = function () {
          _this3._onFormSubmit();
        };

        this._onFormResetFunc = function () {
          _this3._onFormReset();
        }; 


        if (this._hasParentFormCounter()) {
          this._incrementParentFormCounter();
        } else {
          
          this._initializeFormCounterToOne(); 


          this.parentForm.addEventListener('submit', this._onFormSubmitFunc, false);
          this.parentForm.addEventListener('reset', this._onFormResetFunc, false); 

          this._storeFormHandlerFunction();
        }
      }
    }
    

  }, {
    key: "_removeFormListener",
    value: function _removeFormListener() {
      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(this.parentForm)) {
        
        var anCount = this._getParentFormCounter();

        if (anCount === 1) {
          
          this.parentForm.removeEventListener('submit', this._getFormHandlerFunction().submitFn, false);
          this.parentForm.removeEventListener('reset', this._getFormHandlerFunction().resetFn, false); 

          this._removeFormDataSetInfo();
        } else if (anCount > 1) {
          
          this._decrementParentFormCounter();
        } else {
          
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The AutoNumeric object count on the form is incoherent.");
        }
      }
    }
    

  }, {
    key: "_hasParentFormCounter",
    value: function _hasParentFormCounter() {
      return 'anCount' in this.parentForm.dataset;
    }
    

  }, {
    key: "_getParentFormCounter",
    value: function _getParentFormCounter() {
      return Number(this.parentForm.dataset.anCount);
    }
    

  }, {
    key: "_initializeFormCounterToOne",
    value: function _initializeFormCounterToOne() {
      var formElement = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      this._getFormElement(formElement).dataset.anCount = 1;
    }
    

  }, {
    key: "_incrementParentFormCounter",
    value: function _incrementParentFormCounter() {
      var formElement = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      this._getFormElement(formElement).dataset.anCount++;
    }
    

  }, {
    key: "_decrementParentFormCounter",
    value: function _decrementParentFormCounter() {
      this.parentForm.dataset.anCount--;
    }
    

  }, {
    key: "_hasFormHandlerFunction",

    
    value: function _hasFormHandlerFunction() {
      var formElement = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      return 'anFormHandler' in this._getFormElement(formElement).dataset;
    }
    

  }, {
    key: "_getFormElement",
    value: function _getFormElement() {
      var formElement = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var formElementToUse;

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(formElement)) {
        formElementToUse = formElement;
      } else {
        formElementToUse = this.parentForm;
      }

      return formElementToUse;
    }
    

  }, {
    key: "_storeFormHandlerFunction",
    value: function _storeFormHandlerFunction() {
      var formElement = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      
      if (!this.constructor._doesFormHandlerListExists()) {
        this.constructor._createFormHandlerList();
      } 


      var formHandlerName = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].randomString();
      this._getFormElement(formElement).dataset.anFormHandler = formHandlerName; 

      window.aNFormHandlerMap.set(formHandlerName, {
        submitFn: this._onFormSubmitFunc,
        resetFn: this._onFormResetFunc
      });
    }
    

  }, {
    key: "_getFormHandlerKey",
    value: function _getFormHandlerKey() {
      if (!this._hasFormHandlerFunction()) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("Unable to retrieve the form handler name");
      }

      var formHandlerName = this.parentForm.dataset.anFormHandler;

      if (formHandlerName === '') {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The form handler name is invalid");
      }

      return formHandlerName;
    }
    

  }, {
    key: "_getFormHandlerFunction",
    value: function _getFormHandlerFunction() {
      var formHandlerName = this._getFormHandlerKey();

      return window.aNFormHandlerMap.get(formHandlerName);
    }
    

  }, {
    key: "_removeFormDataSetInfo",
    value: function _removeFormDataSetInfo() {
      
      this._decrementParentFormCounter(); 


      window.aNFormHandlerMap["delete"](this._getFormHandlerKey()); 

      this.parentForm.removeAttribute('data-an-count');
      this.parentForm.removeAttribute('data-an-form-handler');
    }
    

  }, {
    key: "_setWritePermissions",
    value: function _setWritePermissions() {
      var useHtmlAttribute = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

      if (useHtmlAttribute && this.domElement.readOnly || this.settings.readOnly) {
        this._setReadOnly();
      } else {
        this._setReadWrite();
      }
    }
    

  }, {
    key: "_setReadOnly",
    value: function _setReadOnly() {
      if (this.isInputElement) {
        this.domElement.readOnly = true;
      } else {
        this.domElement.setAttribute('contenteditable', false);
      }
    }
    

  }, {
    key: "_setReadWrite",
    value: function _setReadWrite() {
      if (this.isInputElement) {
        this.domElement.readOnly = false;
      } else {
        this.domElement.setAttribute('contenteditable', true);
      }
    }
    

  }, {
    key: "_addWatcher",
    value: function _addWatcher() {
      var _this4 = this;

      
      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(this.getterSetter)) {
        var _this$getterSetter = this.getterSetter,
            setter = _this$getterSetter.set,
            getter = _this$getterSetter.get;
        Object.defineProperty(this.domElement, this.attributeToWatch, {
          configurable: true,
          
          get: function get() {
            return getter.call(_this4.domElement);
          },
          set: function set(val) {
            setter.call(_this4.domElement, val); 

            if (_this4.settings.watchExternalChanges && !_this4.internalModification) {
              _this4.set(val);
            }
          }
        });
      } 

      

    }
    

  }, {
    key: "_removeWatcher",
    value: function _removeWatcher() {
      var _this5 = this;

      
      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(this.getterSetter)) {
        var _this$getterSetter2 = this.getterSetter,
            setter = _this$getterSetter2.set,
            getter = _this$getterSetter2.get;
        Object.defineProperty(this.domElement, this.attributeToWatch, {
          configurable: true,
          
          get: function get() {
            return getter.call(_this5.domElement);
          },
          set: function set(val) {
            setter.call(_this5.domElement, val);
          }
        });
      } 

      

    }
    

  }, {
    key: "_getAttributeToWatch",
    value: function _getAttributeToWatch() {
      var attributeToWatch;

      if (this.isInputElement) {
        attributeToWatch = 'value';
      } else {
        var nodeType = this.domElement.nodeType;

        if (nodeType === Node.ELEMENT_NODE || nodeType === Node.DOCUMENT_NODE || nodeType === Node.DOCUMENT_FRAGMENT_NODE) {
          attributeToWatch = 'textContent';
        } else if (nodeType === Node.TEXT_NODE) {
          attributeToWatch = 'nodeValue';
        }
      }

      return attributeToWatch;
    }
    

  }, {
    key: "_historyTableAdd",
    value: function _historyTableAdd() {
      
      var isEmptyHistoryTable = this.historyTable.length === 0; 

      if (isEmptyHistoryTable || this.rawValue !== this._historyTableCurrentValueUsed()) {
        
        var addNewHistoryState = true;

        if (!isEmptyHistoryTable) {
          
          var nextHistoryStateIndex = this.historyTableIndex + 1;

          if (nextHistoryStateIndex < this.historyTable.length && this.rawValue === this.historyTable[nextHistoryStateIndex].value) {
            
            addNewHistoryState = false;
          } else {
            
            _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].arrayTrim(this.historyTable, this.historyTableIndex + 1);
          }
        } 


        this.historyTableIndex++; 

        if (addNewHistoryState) {
          
          var selection = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementSelection(this.domElement);
          this.selectionStart = selection.start;
          this.selectionEnd = selection.end; 

          this.historyTable.push({
            
            value: this.rawValue,
            
            
            
            
            start: this.selectionStart + 1,
            
            end: this.selectionEnd + 1
          }); 

          if (this.historyTable.length > 1) {
            this.historyTable[this.historyTableIndex - 1].start = this.selectionStart;
            this.historyTable[this.historyTableIndex - 1].end = this.selectionEnd;
          }
        } 


        if (this.historyTable.length > this.settings.historySize) {
          this._historyTableForget();
        }
      }
    }
    

    

    

  }, {
    key: "_historyTableUndoOrRedo",
    value: function _historyTableUndoOrRedo() {
      var undo = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
      var check;

      if (undo) {
        
        check = this.historyTableIndex > 0;

        if (check) {
          this.historyTableIndex--;
        }
      } else {
        
        check = this.historyTableIndex + 1 < this.historyTable.length;

        if (check) {
          this.historyTableIndex++;
        }
      }

      if (check) {
        
        var undoInfo = this.historyTable[this.historyTableIndex];
        this.set(undoInfo.value, null, false); 
        

        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(this.domElement, undoInfo.start, undoInfo.end);
      }
    }
    

  }, {
    key: "_historyTableUndo",
    value: function _historyTableUndo() {
      this._historyTableUndoOrRedo(true);
    }
    

  }, {
    key: "_historyTableRedo",
    value: function _historyTableRedo() {
      this._historyTableUndoOrRedo(false);
    }
    

    

    

  }, {
    key: "_historyTableForget",
    value: function _historyTableForget() {
      var numberOfEntriesToForget = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 1;
      var shiftedAway = [];

      for (var i = 0; i < numberOfEntriesToForget; i++) {
        shiftedAway.push(this.historyTable.shift()); 

        this.historyTableIndex--;

        if (this.historyTableIndex < 0) {
          
          this.historyTableIndex = 0;
        }
      }

      if (shiftedAway.length === 1) {
        return shiftedAway[0];
      }

      return shiftedAway;
    }
    

  }, {
    key: "_historyTableCurrentValueUsed",
    value: function _historyTableCurrentValueUsed() {
      var indexToUse = this.historyTableIndex;

      if (indexToUse < 0) {
        indexToUse = 0;
      }

      var result;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(this.historyTable[indexToUse])) {
        result = '';
      } else {
        result = this.historyTable[indexToUse].value;
      }

      return result;
    }
    

  }, {
    key: "_parseStyleRules",
    value: function _parseStyleRules() {
      var _this6 = this;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(this.settings.styleRules) || this.rawValue === '') {
        return;
      } 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(this.settings.styleRules.positive)) {
        if (this.rawValue >= 0) {
          this._addCSSClass(this.settings.styleRules.positive);
        } else {
          this._removeCSSClass(this.settings.styleRules.positive);
        }
      } 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(this.settings.styleRules.negative)) {
        if (this.rawValue < 0) {
          this._addCSSClass(this.settings.styleRules.negative);
        } else {
          this._removeCSSClass(this.settings.styleRules.negative);
        }
      } 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(this.settings.styleRules.ranges) && this.settings.styleRules.ranges.length !== 0) {
        this.settings.styleRules.ranges.forEach(function (range) {
          if (_this6.rawValue >= range.min && _this6.rawValue < range.max) {
            _this6._addCSSClass(range["class"]);
          } else {
            _this6._removeCSSClass(range["class"]);
          }
        });
      } 
      


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(this.settings.styleRules.userDefined) && this.settings.styleRules.userDefined.length !== 0) {
        this.settings.styleRules.userDefined.forEach(function (userObject) {
          if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(userObject.callback)) {
            
            if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(userObject.classes)) {
              
              if (userObject.callback(_this6.rawValue)) {
                _this6._addCSSClass(userObject.classes);
              } else {
                _this6._removeCSSClass(userObject.classes);
              }
            } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isArray(userObject.classes)) {
              if (userObject.classes.length === 2) {
                
                if (userObject.callback(_this6.rawValue)) {
                  _this6._addCSSClass(userObject.classes[0]);

                  _this6._removeCSSClass(userObject.classes[1]);
                } else {
                  _this6._removeCSSClass(userObject.classes[0]);

                  _this6._addCSSClass(userObject.classes[1]);
                }
              } else if (userObject.classes.length > 2) {
                
                var callbackResult = userObject.callback(_this6.rawValue);

                if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isArray(callbackResult)) {
                  
                  userObject.classes.forEach(function (userClass, index) {
                    if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(index, callbackResult)) {
                      _this6._addCSSClass(userClass);
                    } else {
                      _this6._removeCSSClass(userClass);
                    }
                  });
                } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInt(callbackResult)) {
                  
                  userObject.classes.forEach(function (userClass, index) {
                    if (index === callbackResult) {
                      _this6._addCSSClass(userClass);
                    } else {
                      _this6._removeCSSClass(userClass);
                    }
                  });
                } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(callbackResult)) {
                  
                  userObject.classes.forEach(function (userClass) {
                    _this6._removeCSSClass(userClass);
                  });
                } else {
                  _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The callback result is not an array nor a valid array index, ".concat(_typeof(callbackResult), " given."));
                }
              } else {
                _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError('The classes attribute is not valid for the `styleRules` option.');
              }
            } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(userObject.classes)) {
              
              userObject.callback(_this6);
            } else {
              _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError('The callback/classes structure is not valid for the `styleRules` option.');
            }
          } else {
            _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("The given `styleRules` callback is not a function, ".concat(typeof callback === "undefined" ? "undefined" : _typeof(callback), " given."), _this6.settings.showWarnings);
          }
        });
      }
    }
    

  }, {
    key: "_addCSSClass",
    value: function _addCSSClass(cssClassName) {
      this.domElement.classList.add(cssClassName);
    }
    

  }, {
    key: "_removeCSSClass",
    value: function _removeCSSClass(cssClassName) {
      this.domElement.classList.remove(cssClassName);
    } 

    

  }, {
    key: "update",
    value: function update() {
      var _this7 = this;

      for (var _len2 = arguments.length, newOptions = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
        newOptions[_key2] = arguments[_key2];
      }

      if (Array.isArray(newOptions) && Array.isArray(newOptions[0])) {
        
        newOptions = newOptions[0];
      } 


      var originalSettings = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].cloneObject(this.settings); 
      

      var numericString = this.rawValue; 

      var optionsToUse = {};

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(newOptions) || newOptions.length === 0) {
        optionsToUse = null;
      } else if (newOptions.length >= 1) {
        newOptions.forEach(function (optionObject) {
          if (_this7.constructor._isPreDefinedOptionValid(optionObject)) {
            
            optionObject = _this7.constructor._getOptionObject(optionObject);
          }

          _extends(optionsToUse, optionObject);
        });
      } 


      try {
        this._setSettings(optionsToUse, true);

        this._setWritePermissions(); 


        this._updateEventListeners(); 
        


        this.set(numericString);
      } catch (error) {
        
        this._setSettings(originalSettings, true); 


        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("Unable to update the settings, those are invalid: [".concat(error, "]"));
        return this;
      }

      return this;
    }
    

  }, {
    key: "getSettings",
    value: function getSettings() {
      return this.settings;
    }
    

  }, {
    key: "set",
    value: function set(newValue) {
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var saveChangeToHistory = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;

      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(newValue)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("You are trying to set an 'undefined' value ; an error could have occurred.", this.settings.showWarnings);
        return this;
      } 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options)) {
        this._setSettings(options, true); 

      }

      if (newValue === null && this.settings.emptyInputBehavior !== AutoNumeric.options.emptyInputBehavior["null"]) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("You are trying to set the `null` value while the `emptyInputBehavior` option is set to ".concat(this.settings.emptyInputBehavior, ". If you want to be able to set the `null` value, you need to change the 'emptyInputBehavior' option to `'null'`."), this.settings.showWarnings);
        return this;
      }

      var value;

      if (newValue === null) {
        
        
        this._setElementAndRawValue(null, null, saveChangeToHistory);

        this._saveValueToPersistentStorage();

        return this;
      }

      value = this.constructor._toNumericValue(newValue, this.settings);

      if (isNaN(Number(value))) {
        
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("The value you are trying to set results in `NaN`. The element value is set to the empty string instead.", this.settings.showWarnings);
        this.setValue('', saveChangeToHistory);
        return this;
      }

      if (value === '') {
        switch (this.settings.emptyInputBehavior) {
          case AutoNumeric.options.emptyInputBehavior.zero:
            value = 0;
            break;

          case AutoNumeric.options.emptyInputBehavior.min:
            value = this.settings.minimumValue;
            break;

          case AutoNumeric.options.emptyInputBehavior.max:
            value = this.settings.maximumValue;
            break;

          default:
            if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(this.settings.emptyInputBehavior)) {
              value = Number(this.settings.emptyInputBehavior);
            }

        }
      }

      if (value !== '') {
        var _this$constructor$_ch = this.constructor._checkIfInRangeWithOverrideOption(value, this.settings),
            _this$constructor$_ch2 = _slicedToArray(_this$constructor$_ch, 2),
            minTest = _this$constructor$_ch2[0],
            maxTest = _this$constructor$_ch2[1]; 


        if (minTest && maxTest && this.settings.valuesToStrings && this._checkValuesToStrings(value)) {
          
          this._setElementAndRawValue(this.settings.valuesToStrings[value], value, saveChangeToHistory);

          this._saveValueToPersistentStorage();

          return this;
        } 


        var isZero = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isZeroOrHasNoValue(value);

        if (isZero) {
          value = '0';
        }

        if (minTest && maxTest) {
          var forcedRawValue = this.constructor._roundRawValue(value, this.settings);

          forcedRawValue = this._trimLeadingAndTrailingZeros(forcedRawValue.replace(this.settings.decimalCharacter, '.')); 

          value = this._getRawValueToFormat(value); 
          

          if (this.isFocused) {
            value = this.constructor._roundFormattedValueShownOnFocus(value, this.settings);
          } else {
            if (this.settings.divisorWhenUnfocused) {
              value = value / this.settings.divisorWhenUnfocused;
              value = value.toString();
            }

            value = this.constructor._roundFormattedValueShownOnBlur(value, this.settings);
          }

          value = this.constructor._modifyNegativeSignAndDecimalCharacterForFormattedValue(value, this.settings);
          value = this.constructor._addGroupSeparators(value, this.settings, this.isFocused, this.rawValue, forcedRawValue);

          if (!this.isFocused && this.settings.symbolWhenUnfocused) {
            value = "".concat(value).concat(this.settings.symbolWhenUnfocused);
          }

          if (this.settings.decimalPlacesShownOnFocus || this.settings.divisorWhenUnfocused) {
            this._saveValueToPersistentStorage();
          }

          this._setElementAndRawValue(value, forcedRawValue, saveChangeToHistory); 


          this._setValidOrInvalidState(forcedRawValue);

          return this;
        } else {
          this._triggerRangeEvents(minTest, maxTest);

          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The value [".concat(value, "] being set falls outside of the minimumValue [").concat(this.settings.minimumValue, "] and maximumValue [").concat(this.settings.maximumValue, "] range set for this element"));

          this._removeValueFromPersistentStorage();

          this.setValue('', saveChangeToHistory); 

          return this;
        }
      } else {
        
        var result;

        if (this.settings.emptyInputBehavior === AutoNumeric.options.emptyInputBehavior.always) {
          
          result = this.settings.currencySymbol;
        } else {
          result = '';
        }

        this._setElementAndRawValue(result, '', saveChangeToHistory);

        return this;
      }
    }
    

  }, {
    key: "setUnformatted",
    value: function setUnformatted(value) {
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      
      if (value === null || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(value)) {
        return this;
      } 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options)) {
        this._setSettings(options, true); 

      }

      var strippedValue = this.constructor._removeBrackets(value, this.settings);

      var normalizedValue = this.constructor._stripAllNonNumberCharacters(strippedValue, this.settings, true, this.isFocused);

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(normalizedValue)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The value is not a valid one, it's not a numeric string nor a recognized currency.");
      }

      if (this.constructor._isWithinRangeWithOverrideOption(normalizedValue, this.settings)) {
        
        this.setValue(value);
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The value is out of the range limits [".concat(this.settings.minimumValue, ", ").concat(this.settings.maximumValue, "]."));
      }

      return this;
    }
    

  }, {
    key: "setValue",
    value: function setValue(newValue) {
      var saveChangeToHistory = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

      this._setElementAndRawValue(newValue, saveChangeToHistory);

      return this;
    }
    

  }, {
    key: "_setRawValue",
    value: function _setRawValue(rawValue) {
      var saveChangeToHistory = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

      
      if (this.rawValue !== rawValue) {
        
        var oldRawValue = this.rawValue; 

        this.rawValue = rawValue; 

        if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(this.settings.rawValueDivisor) && this.settings.rawValueDivisor !== 0 && 
        rawValue !== '' && rawValue !== null && 
        this._isUserManuallyEditingTheValue()) {
          
          this.rawValue /= this.settings.rawValueDivisor;
        } 


        this._triggerEvent(AutoNumeric.events.rawValueModified, this.domElement, {
          oldRawValue: oldRawValue,
          newRawValue: this.rawValue,
          isPristine: this.isPristine(true),
          error: null,
          aNElement: this
        }); 


        this._parseStyleRules();

        if (saveChangeToHistory) {
          
          this._historyTableAdd();
        }
      }
    }
    

  }, {
    key: "_setElementValue",
    value: function _setElementValue(newElementValue) {
      var sendFormattedEvent = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
      
      var oldElementValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement); 

      if (newElementValue !== oldElementValue) {
        this.internalModification = true;
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementValue(this.domElement, newElementValue);
        this.internalModification = false;

        if (sendFormattedEvent) {
          this._triggerEvent(AutoNumeric.events.formatted, this.domElement, {
            oldValue: oldElementValue,
            newValue: newElementValue,
            oldRawValue: this.rawValue,
            newRawValue: this.rawValue,
            isPristine: this.isPristine(false),
            error: null,
            aNElement: this
          });
        }
      }

      return this;
    }
    

  }, {
    key: "_setElementAndRawValue",
    value: function _setElementAndRawValue(newElementValue) {
      var rawValue = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var saveChangeToHistory = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(rawValue)) {
        rawValue = newElementValue;
      } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(rawValue)) {
        saveChangeToHistory = rawValue;
        rawValue = newElementValue;
      } 
      


      this._setElementValue(newElementValue);

      this._setRawValue(rawValue, saveChangeToHistory);

      return this;
    }
    

  }, {
    key: "_getRawValueToFormat",
    value: function _getRawValueToFormat(rawValue) {
      var rawValueForTheElementValue;

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(this.settings.rawValueDivisor) && this.settings.rawValueDivisor !== 0 && 
      rawValue !== '' && rawValue !== null) {
        
        
        rawValueForTheElementValue = rawValue * this.settings.rawValueDivisor;
      } else {
        rawValueForTheElementValue = rawValue;
      }

      return rawValueForTheElementValue;
    }
    

  }, {
    key: "_checkValuesToStrings",
    value: function _checkValuesToStrings(value) {
      return this.constructor._checkValuesToStringsArray(value, this.valuesToStringsKeys);
    }
    

  }, {
    key: "_isUserManuallyEditingTheValue",

    
    value: function _isUserManuallyEditingTheValue() {
      
      return this.isFocused && this.isEditing || this.isDropEvent;
    }
    

  }, {
    key: "_executeCallback",
    value: function _executeCallback(result, callback) {
      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(callback) && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(callback)) {
        callback(result, this);
      }
    }
    

  }, {
    key: "_triggerEvent",
    value: function _triggerEvent(eventName) {
      var element = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : document;
      var detail = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].triggerEvent(eventName, element, detail, this.settings.eventBubbles, this.settings.eventIsCancelable);
    }
    

  }, {
    key: "get",
    value: function get() {
      var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      return this.getNumericString(callback);
    }
    

  }, {
    key: "getNumericString",
    value: function getNumericString() {
      var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var result;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(this.rawValue)) {
        result = null;
      } else {
        
        
        result = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].trimPaddedZerosFromDecimalPlaces(this.rawValue);
      }

      this._executeCallback(result, callback);

      return result;
    }
    

  }, {
    key: "getFormatted",
    value: function getFormatted() {
      var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      if (!('value' in this.domElement || 'textContent' in this.domElement)) {
        
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError('Unable to get the formatted string from the element.');
      }

      var result = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement);

      this._executeCallback(result, callback);

      return result;
    }
    

  }, {
    key: "getNumber",
    value: function getNumber() {
      var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var result;

      if (this.rawValue === null) {
        result = null;
      } else {
        result = this.constructor._toLocale(this.getNumericString(), 'number', this.settings);
      }

      this._executeCallback(result, callback);

      return result;
    }
    

  }, {
    key: "getLocalized",
    value: function getLocalized() {
      var forcedOutputFormat = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(forcedOutputFormat) && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(callback)) {
        callback = forcedOutputFormat;
        forcedOutputFormat = null;
      } 


      var value;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isEmptyString(this.rawValue)) {
        value = '';
      } else {
        
        
        value = '' + Number(this.rawValue);
      }

      if (value !== '' && Number(value) === 0 && this.settings.leadingZero !== AutoNumeric.options.leadingZero.keep) {
        value = '0';
      }

      var outputFormatToUse;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(forcedOutputFormat)) {
        outputFormatToUse = this.settings.outputFormat;
      } else {
        outputFormatToUse = forcedOutputFormat;
      }

      var result = this.constructor._toLocale(value, outputFormatToUse, this.settings);

      this._executeCallback(result, callback);

      return result;
    }
    

  }, {
    key: "reformat",
    value: function reformat() {
      
      this.set(this.rawValue);
      return this;
    }
    

  }, {
    key: "unformat",
    value: function unformat() {
      this._setElementValue(this.getNumericString());

      return this;
    }
    

  }, {
    key: "unformatLocalized",
    value: function unformatLocalized() {
      var forcedOutputFormat = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      this._setElementValue(this.getLocalized(forcedOutputFormat));

      return this;
    }
    

  }, {
    key: "isPristine",
    value: function isPristine() {
      var checkOnlyRawValue = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
      var result;

      if (checkOnlyRawValue) {
        result = this.initialValue === this.getNumericString();
      } else {
        result = this.initialValueHtmlAttribute === this.getFormatted();
      }

      return result;
    }
    

  }, {
    key: "select",
    value: function select() {
      if (this.settings.selectNumberOnly) {
        this.selectNumber();
      } else {
        this._defaultSelectAll();
      }

      return this;
    }
    

  }, {
    key: "_defaultSelectAll",
    value: function _defaultSelectAll() {
      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(this.domElement, 0, _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement).length);
    }
    

  }, {
    key: "selectNumber",
    value: function selectNumber() {
      
      var unformattedValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement);
      var valueLen = unformattedValue.length;
      var currencySymbolSize = this.settings.currencySymbol.length;
      var currencySymbolPlacement = this.settings.currencySymbolPlacement;
      var negLen = !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(unformattedValue, this.settings.negativeSignCharacter) ? 0 : 1;
      var suffixTextLen = this.settings.suffixText.length;
      var start;

      if (currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix) {
        start = 0;
      } else if (this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.left && negLen === 1 && currencySymbolSize > 0) {
        start = currencySymbolSize + 1;
      } else {
        start = currencySymbolSize;
      }

      var end;

      if (currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.prefix) {
        end = valueLen - suffixTextLen;
      } else {
        switch (this.settings.negativePositiveSignPlacement) {
          case AutoNumeric.options.negativePositiveSignPlacement.left:
            end = valueLen - (suffixTextLen + currencySymbolSize);
            break;

          case AutoNumeric.options.negativePositiveSignPlacement.right:
            if (currencySymbolSize > 0) {
              end = valueLen - (currencySymbolSize + negLen + suffixTextLen);
            } else {
              end = valueLen - (currencySymbolSize + suffixTextLen);
            }

            break;

          default:
            end = valueLen - (currencySymbolSize + suffixTextLen);
        }
      }

      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(this.domElement, start, end);
      return this;
    }
    

  }, {
    key: "selectInteger",
    value: function selectInteger() {
      var start = 0;
      var isPositive = this.rawValue >= 0; 

      if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.prefix || this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix && (this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.prefix || this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.none)) {
        if (this.settings.showPositiveSign && isPositive || 
        !isPositive && this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.prefix && this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.left) {
          
          start = start + 1;
        }
      } 


      if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.prefix) {
        start = start + this.settings.currencySymbol.length;
      } 


      var elementValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement);
      var end = elementValue.indexOf(this.settings.decimalCharacter);

      if (end === -1) {
        
        if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix) {
          end = elementValue.length - this.settings.currencySymbol.length;
        } else {
          end = elementValue.length;
        } 


        if (!isPositive && (this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.suffix || this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix)) {
          end = end - 1;
        } 


        end = end - this.settings.suffixText.length;
      }

      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(this.domElement, start, end);
      return this;
    }
    

  }, {
    key: "selectDecimal",
    value: function selectDecimal() {
      var start = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement).indexOf(this.settings.decimalCharacter);
      var end;

      if (start === -1) {
        
        start = 0;
        end = 0;
      } else {
        
        start = start + 1; 

        var decimalCount;

        if (this.isFocused) {
          decimalCount = this.settings.decimalPlacesShownOnFocus;
        } else {
          decimalCount = this.settings.decimalPlacesShownOnBlur;
        }

        end = start + Number(decimalCount);
      }

      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(this.domElement, start, end);
      return this;
    }
    

  }, {
    key: "node",
    value: function node() {
      return this.domElement;
    }
    

  }, {
    key: "parent",
    value: function parent() {
      return this.domElement.parentNode;
    }
    

  }, {
    key: "detach",
    value: function detach() {
      var otherAnElement = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      
      var domElementToDetach;

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(otherAnElement)) {
        domElementToDetach = otherAnElement.node();
      } else {
        domElementToDetach = this.domElement;
      }

      this._removeFromLocalList(domElementToDetach); 


      return this;
    }
    

  }, {
    key: "attach",
    value: function attach(otherAnElement) {
      var reFormat = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

      
      this._addToLocalList(otherAnElement.node()); 


      if (reFormat) {
        otherAnElement.update(this.settings);
      }

      return this;
    }
    

  }, {
    key: "formatOther",
    value: function formatOther(valueOrElement) {
      var optionOverride = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      
      return this._formatOrUnformatOther(true, valueOrElement, optionOverride);
    }
    

  }, {
    key: "unformatOther",
    value: function unformatOther(stringOrElement) {
      var optionOverride = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      
      return this._formatOrUnformatOther(false, stringOrElement, optionOverride);
    }
    

  }, {
    key: "_formatOrUnformatOther",
    value: function _formatOrUnformatOther(isFormatting, valueOrStringOrElement) {
      var optionOverride = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      
      
      var settingsToUse;

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(optionOverride)) {
        settingsToUse = this._cloneAndMergeSettings(optionOverride);
      } else {
        settingsToUse = this.settings;
      } 


      var result;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isElement(valueOrStringOrElement)) {
        
        var elementValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(valueOrStringOrElement);

        if (isFormatting) {
          result = AutoNumeric.format(elementValue, settingsToUse);
        } else {
          result = AutoNumeric.unformat(elementValue, settingsToUse);
        }

        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementValue(valueOrStringOrElement, result); 

        return null;
      } 


      if (isFormatting) {
        result = AutoNumeric.format(valueOrStringOrElement, settingsToUse);
      } else {
        result = AutoNumeric.unformat(valueOrStringOrElement, settingsToUse);
      }

      return result;
    }
    

  }, {
    key: "init",
    value: function init(domElementOrArrayOrString) {
      var _this8 = this;

      var attached = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
      var returnASingleAutoNumericObject = false; 

      var domElementsArray = [];

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(domElementOrArrayOrString)) {
        domElementsArray = _toConsumableArray(document.querySelectorAll(domElementOrArrayOrString)); 
      } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isElement(domElementOrArrayOrString)) {
        domElementsArray.push(domElementOrArrayOrString);
        returnASingleAutoNumericObject = true; 
      } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isArray(domElementOrArrayOrString)) {
        domElementsArray = domElementOrArrayOrString;
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The given parameters to the 'init' function are invalid.");
      }

      if (domElementsArray.length === 0) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("No valid DOM elements were given hence no AutoNumeric object were instantiated.", true);
        return [];
      }

      var currentLocalList = this._getLocalList();

      var autoNumericObjectsArray = []; 

      domElementsArray.forEach(function (domElement) {
        
        var originalCreateLocalListSetting = _this8.settings.createLocalList;

        if (attached) {
          
          _this8.settings.createLocalList = false;
        }

        var newAutoNumericElement = new AutoNumeric(domElement, _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(domElement), _this8.settings); 
        

        if (attached) {
          
          newAutoNumericElement._setLocalList(currentLocalList); 


          _this8._addToLocalList(domElement, newAutoNumericElement); 


          _this8.settings.createLocalList = originalCreateLocalListSetting;
        }

        autoNumericObjectsArray.push(newAutoNumericElement);
      });

      if (returnASingleAutoNumericObject) {
        
        return autoNumericObjectsArray[0];
      } 


      return autoNumericObjectsArray;
    }
    

  }, {
    key: "clear",
    value: function clear() {
      var forceClearAll = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

      if (forceClearAll) {
        var temporaryForcedOptions = {
          emptyInputBehavior: AutoNumeric.options.emptyInputBehavior.focus
        };
        this.set('', temporaryForcedOptions);
      } else {
        this.set('');
      }

      return this;
    }
    

  }, {
    key: "remove",
    value: function remove() {
      this._removeValueFromPersistentStorage();

      this._removeEventListeners();

      this._removeWatcher(); 


      this._removeFromLocalList(this.domElement); 


      this.constructor._removeFromGlobalList(this);
    }
    

  }, {
    key: "wipe",
    value: function wipe() {
      this._setElementValue('', false); 


      this.remove();
    }
    

  }, {
    key: "nuke",
    value: function nuke() {
      this.remove(); 

      this.domElement.parentNode.removeChild(this.domElement);
    } 

    

  }, {
    key: "form",
    value: function form() {
      var forceSearch = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;

      if (forceSearch || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(this.parentForm)) {
        var newParentForm = this._getParentForm();

        if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(newParentForm) && newParentForm !== this.parentForm) {
          
          
          var oldANChildren = this._getFormAutoNumericChildren(this.parentForm); 


          this.parentForm.dataset.anCount = oldANChildren.length; 

          if (this._hasFormHandlerFunction(newParentForm)) {
            this._incrementParentFormCounter(newParentForm); 

          } else {
            
            this._storeFormHandlerFunction(newParentForm);

            this._initializeFormCounterToOne(newParentForm);
          }
        }

        this.parentForm = newParentForm;
      }

      return this.parentForm;
    }
    

  }, {
    key: "_getFormAutoNumericChildren",
    value: function _getFormAutoNumericChildren(formElement) {
      var _this9 = this;

      
      
      var inputList = _toConsumableArray(formElement.querySelectorAll('input'));

      return inputList.filter(function (input) {
        return _this9.constructor.isManagedByAutoNumeric(input);
      });
    }
    

  }, {
    key: "_getParentForm",
    value: function _getParentForm() {
      if (this.domElement.tagName.toLowerCase() === 'body') {
        return null;
      }

      var node = this.domElement;
      var tagName;

      do {
        node = node.parentNode;

        if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(node)) {
          
          return null;
        }

        if (node.tagName) {
          tagName = node.tagName.toLowerCase();
        } else {
          tagName = '';
        }

        if (tagName === 'body') {
          
          break;
        }
      } while (tagName !== 'form');

      if (tagName === 'form') {
        return node;
      } else {
        return null;
      }
    }
    

  }, {
    key: "formNumericString",
    value: function formNumericString() {
      return this.constructor._serializeNumericString(this.form(), this.settings.serializeSpaces);
    }
    

  }, {
    key: "formFormatted",
    value: function formFormatted() {
      return this.constructor._serializeFormatted(this.form(), this.settings.serializeSpaces);
    }
    

  }, {
    key: "formLocalized",
    value: function formLocalized() {
      var forcedOutputFormat = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var outputFormatToUse;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(forcedOutputFormat)) {
        outputFormatToUse = this.settings.outputFormat;
      } else {
        outputFormatToUse = forcedOutputFormat;
      }

      return this.constructor._serializeLocalized(this.form(), this.settings.serializeSpaces, outputFormatToUse);
    }
    

  }, {
    key: "formArrayNumericString",
    value: function formArrayNumericString() {
      return this.constructor._serializeNumericStringArray(this.form(), this.settings.serializeSpaces);
    }
    

  }, {
    key: "formArrayFormatted",
    value: function formArrayFormatted() {
      return this.constructor._serializeFormattedArray(this.form(), this.settings.serializeSpaces);
    }
    

  }, {
    key: "formArrayLocalized",
    value: function formArrayLocalized() {
      var forcedOutputFormat = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var outputFormatToUse;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(forcedOutputFormat)) {
        outputFormatToUse = this.settings.outputFormat;
      } else {
        outputFormatToUse = forcedOutputFormat;
      }

      return this.constructor._serializeLocalizedArray(this.form(), this.settings.serializeSpaces, outputFormatToUse);
    }
    

  }, {
    key: "formJsonNumericString",
    value: function formJsonNumericString() {
      return JSON.stringify(this.formArrayNumericString());
    }
    

  }, {
    key: "formJsonFormatted",
    value: function formJsonFormatted() {
      return JSON.stringify(this.formArrayFormatted());
    }
    

  }, {
    key: "formJsonLocalized",
    value: function formJsonLocalized() {
      var forcedOutputFormat = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      return JSON.stringify(this.formArrayLocalized(forcedOutputFormat));
    }
    

  }, {
    key: "formUnformat",
    value: function formUnformat() {
      
      var inputs = this.constructor._getChildANInputElement(this.form());

      inputs.forEach(function (input) {
        AutoNumeric.getAutoNumericElement(input).unformat();
      });
      return this;
    }
    

  }, {
    key: "formUnformatLocalized",
    value: function formUnformatLocalized() {
      
      var inputs = this.constructor._getChildANInputElement(this.form());

      inputs.forEach(function (input) {
        AutoNumeric.getAutoNumericElement(input).unformatLocalized();
      });
      return this;
    }
    

  }, {
    key: "formReformat",
    value: function formReformat() {
      
      var inputs = this.constructor._getChildANInputElement(this.form());

      inputs.forEach(function (input) {
        AutoNumeric.getAutoNumericElement(input).reformat();
      });
      return this;
    }
    

  }, {
    key: "formSubmitNumericString",
    value: function formSubmitNumericString() {
      var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(callback)) {
        this.formUnformat();
        this.form().submit();
        this.formReformat();
      } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(callback)) {
        callback(this.formNumericString());
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The given callback is not a function.");
      }

      return this;
    }
    

  }, {
    key: "formSubmitFormatted",
    value: function formSubmitFormatted() {
      var callback = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(callback)) {
        this.form().submit();
      } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(callback)) {
        callback(this.formFormatted());
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The given callback is not a function.");
      }

      return this;
    }
    

  }, {
    key: "formSubmitLocalized",
    value: function formSubmitLocalized() {
      var forcedOutputFormat = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(callback)) {
        this.formUnformatLocalized();
        this.form().submit();
        this.formReformat();
      } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(callback)) {
        callback(this.formLocalized(forcedOutputFormat));
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The given callback is not a function.");
      }

      return this;
    }
    

  }, {
    key: "formSubmitArrayNumericString",
    value: function formSubmitArrayNumericString(callback) {
      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(callback)) {
        callback(this.formArrayNumericString());
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The given callback is not a function.");
      }

      return this;
    }
    

  }, {
    key: "formSubmitArrayFormatted",
    value: function formSubmitArrayFormatted(callback) {
      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(callback)) {
        callback(this.formArrayFormatted());
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The given callback is not a function.");
      }

      return this;
    }
    

  }, {
    key: "formSubmitArrayLocalized",
    value: function formSubmitArrayLocalized(callback) {
      var forcedOutputFormat = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(callback)) {
        callback(this.formArrayLocalized(forcedOutputFormat));
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The given callback is not a function.");
      }

      return this;
    }
    

  }, {
    key: "formSubmitJsonNumericString",
    value: function formSubmitJsonNumericString(callback) {
      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(callback)) {
        callback(this.formJsonNumericString());
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The given callback is not a function.");
      }

      return this;
    }
    

  }, {
    key: "formSubmitJsonFormatted",
    value: function formSubmitJsonFormatted(callback) {
      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(callback)) {
        callback(this.formJsonFormatted());
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The given callback is not a function.");
      }

      return this;
    }
    

  }, {
    key: "formSubmitJsonLocalized",
    value: function formSubmitJsonLocalized(callback) {
      var forcedOutputFormat = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(callback)) {
        callback(this.formJsonLocalized(forcedOutputFormat));
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The given callback is not a function.");
      }

      return this;
    }
    

  }, {
    key: "_createLocalList",

    
    value: function _createLocalList() {
      this.autoNumericLocalList = new Map();

      this._addToLocalList(this.domElement);
    }
    

  }, {
    key: "_deleteLocalList",
    value: function _deleteLocalList() {
      delete this.autoNumericLocalList;
    }
    

  }, {
    key: "_setLocalList",
    value: function _setLocalList(localList) {
      this.autoNumericLocalList = localList;
    }
    

  }, {
    key: "_getLocalList",
    value: function _getLocalList() {
      return this.autoNumericLocalList;
    }
    

  }, {
    key: "_hasLocalList",
    value: function _hasLocalList() {
      return this.autoNumericLocalList instanceof Map && this.autoNumericLocalList.size !== 0;
    }
    

  }, {
    key: "_addToLocalList",
    value: function _addToLocalList(domElement) {
      var autoNumericObject = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(autoNumericObject)) {
        autoNumericObject = this;
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(this.autoNumericLocalList)) {
        this.autoNumericLocalList.set(domElement, autoNumericObject); 
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The local list provided does not exists when trying to add an element. [".concat(this.autoNumericLocalList, "] given."));
      }
    }
    

  }, {
    key: "_removeFromLocalList",
    value: function _removeFromLocalList(domElement) {
      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(this.autoNumericLocalList)) {
        this.autoNumericLocalList["delete"](domElement);
      } else if (this.settings.createLocalList) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The local list provided does not exists when trying to remove an element. [".concat(this.autoNumericLocalList, "] given."));
      }
    }
    

  }, {
    key: "_mergeSettings",
    value: function _mergeSettings() {
      for (var _len3 = arguments.length, newSettings = new Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
        newSettings[_key3] = arguments[_key3];
      }

      _extends.apply(void 0, [this.settings].concat(newSettings));
    }
    

  }, {
    key: "_cloneAndMergeSettings",
    value: function _cloneAndMergeSettings() {
      var result = {};

      for (var _len4 = arguments.length, newSettings = new Array(_len4), _key4 = 0; _key4 < _len4; _key4++) {
        newSettings[_key4] = arguments[_key4];
      }

      _extends.apply(void 0, [result, this.settings].concat(newSettings));

      return result;
    }
    

  }, {
    key: "_updatePredefinedOptions",
    
    

    
    value: function _updatePredefinedOptions(predefinedOption) {
      var optionOverride = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(optionOverride)) {
        this._mergeSettings(predefinedOption, optionOverride);

        this.update(this.settings);
      } else {
        this.update(predefinedOption);
      }

      return this;
    }
    

  }, {
    key: "french",
    value: function french() {
      var optionOverride = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      this._updatePredefinedOptions(AutoNumeric.getPredefinedOptions().French, optionOverride);

      return this;
    }
    

  }, {
    key: "northAmerican",
    value: function northAmerican() {
      var optionOverride = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      this._updatePredefinedOptions(AutoNumeric.getPredefinedOptions().NorthAmerican, optionOverride);

      return this;
    }
    

  }, {
    key: "british",
    value: function british() {
      var optionOverride = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      this._updatePredefinedOptions(AutoNumeric.getPredefinedOptions().British, optionOverride);

      return this;
    }
    

  }, {
    key: "swiss",
    value: function swiss() {
      var optionOverride = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      this._updatePredefinedOptions(AutoNumeric.getPredefinedOptions().Swiss, optionOverride);

      return this;
    }
    

  }, {
    key: "japanese",
    value: function japanese() {
      var optionOverride = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      this._updatePredefinedOptions(AutoNumeric.getPredefinedOptions().Japanese, optionOverride);

      return this;
    }
    

  }, {
    key: "spanish",
    value: function spanish() {
      var optionOverride = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      this._updatePredefinedOptions(AutoNumeric.getPredefinedOptions().Spanish, optionOverride);

      return this;
    }
    

  }, {
    key: "chinese",
    value: function chinese() {
      var optionOverride = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      this._updatePredefinedOptions(AutoNumeric.getPredefinedOptions().Chinese, optionOverride);

      return this;
    }
    

  }, {
    key: "brazilian",
    value: function brazilian() {
      var optionOverride = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;

      this._updatePredefinedOptions(AutoNumeric.getPredefinedOptions().Brazilian, optionOverride);

      return this;
    } 

    

  }, {
    key: "_runCallbacksFoundInTheSettingsObject",
    value: function _runCallbacksFoundInTheSettingsObject() {
      
      
      for (var key in this.settings) {
        if (Object.prototype.hasOwnProperty.call(this.settings, key)) {
          var value = this.settings[key];

          if (typeof value === 'function') {
            this.settings[key] = value(this, key);
          } else {
            
            var htmlAttribute = this.domElement.getAttribute(key); 

            htmlAttribute = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].camelize(htmlAttribute);

            if (typeof this.settings[htmlAttribute] === 'function') {
              this.settings[key] = htmlAttribute(this, key);
            }
          }
        }
      }
    }
    

  }, {
    key: "_setTrailingNegativeSignInfo",
    value: function _setTrailingNegativeSignInfo() {
      this.isTrailingNegative = this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.prefix && this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.suffix || this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix && (this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.left || this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.right);
    }
    

  }, {
    key: "_modifyNegativeSignAndDecimalCharacterForRawValue",

    
    value: function _modifyNegativeSignAndDecimalCharacterForRawValue(s) {
      if (this.settings.decimalCharacter !== '.') {
        s = s.replace(this.settings.decimalCharacter, '.');
      }

      if (this.settings.negativeSignCharacter !== '-' && this.settings.isNegativeSignAllowed) {
        s = s.replace(this.settings.negativeSignCharacter, '-');
      }

      if (!s.match(/\d/)) {
        
        s += '0';
      }

      return s;
    }
    

  }, {
    key: "_initialCaretPosition",

    
    value: function _initialCaretPosition(value) {
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(this.settings.caretPositionOnFocus) && this.settings.selectOnFocus === AutoNumeric.options.selectOnFocus.doNotSelect) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError('`_initialCaretPosition()` should never be called when the `caretPositionOnFocus` option is `null`.');
      }

      var isValueNegative = this.rawValue < 0;
      var isZeroOrHasNoValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isZeroOrHasNoValue(value);
      var totalLength = value.length;
      var valueSize = 0;
      var integerSize = 0;
      var hasDecimalChar = false;
      var offsetDecimalChar = 0;

      if (this.settings.caretPositionOnFocus !== AutoNumeric.options.caretPositionOnFocus.start) {
        value = value.replace(this.settings.negativeSignCharacter, '');
        value = value.replace(this.settings.positiveSignCharacter, '');
        value = value.replace(this.settings.currencySymbol, '');
        valueSize = value.length;
        hasDecimalChar = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(value, this.settings.decimalCharacter);

        if (this.settings.caretPositionOnFocus === AutoNumeric.options.caretPositionOnFocus.decimalLeft || this.settings.caretPositionOnFocus === AutoNumeric.options.caretPositionOnFocus.decimalRight) {
          if (hasDecimalChar) {
            integerSize = value.indexOf(this.settings.decimalCharacter);
            offsetDecimalChar = this.settings.decimalCharacter.length;
          } else {
            integerSize = valueSize;
            offsetDecimalChar = 0;
          }
        }
      }

      var signToUse = '';

      if (isValueNegative) {
        signToUse = this.settings.negativeSignCharacter;
      } else if (this.settings.showPositiveSign && !isZeroOrHasNoValue) {
        signToUse = this.settings.positiveSignCharacter;
      }

      var positiveNegativeSignSize = signToUse.length;
      var currencySymbolSize = this.settings.currencySymbol.length; 

      var caretPosition;

      if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.prefix) {
        if (this.settings.caretPositionOnFocus === AutoNumeric.options.caretPositionOnFocus.start) {
          if (this.settings.negativePositiveSignPlacement !== AutoNumeric.options.negativePositiveSignPlacement.none && (isValueNegative || !isValueNegative && this.settings.showPositiveSign && !isZeroOrHasNoValue)) {
            switch (this.settings.negativePositiveSignPlacement) {
              case AutoNumeric.options.negativePositiveSignPlacement.prefix: 

              case AutoNumeric.options.negativePositiveSignPlacement.left: 

              case AutoNumeric.options.negativePositiveSignPlacement.right:
                
                caretPosition = positiveNegativeSignSize + currencySymbolSize;
                break;

              case AutoNumeric.options.negativePositiveSignPlacement.suffix:
                
                caretPosition = currencySymbolSize;
                break;
            }
          } else {
            
            caretPosition = currencySymbolSize;
          }
        } else if (this.settings.caretPositionOnFocus === AutoNumeric.options.caretPositionOnFocus.end) {
          if (this.settings.negativePositiveSignPlacement !== AutoNumeric.options.negativePositiveSignPlacement.none && (isValueNegative || !isValueNegative && this.settings.showPositiveSign && !isZeroOrHasNoValue)) {
            switch (this.settings.negativePositiveSignPlacement) {
              case AutoNumeric.options.negativePositiveSignPlacement.prefix: 

              case AutoNumeric.options.negativePositiveSignPlacement.left: 

              case AutoNumeric.options.negativePositiveSignPlacement.right:
                
                caretPosition = totalLength;
                break;

              case AutoNumeric.options.negativePositiveSignPlacement.suffix:
                
                caretPosition = currencySymbolSize + valueSize;
                break;
            }
          } else {
            
            caretPosition = totalLength;
          }
        } else if (this.settings.caretPositionOnFocus === AutoNumeric.options.caretPositionOnFocus.decimalLeft) {
          if (this.settings.negativePositiveSignPlacement !== AutoNumeric.options.negativePositiveSignPlacement.none && (isValueNegative || !isValueNegative && this.settings.showPositiveSign && !isZeroOrHasNoValue)) {
            switch (this.settings.negativePositiveSignPlacement) {
              case AutoNumeric.options.negativePositiveSignPlacement.prefix: 

              case AutoNumeric.options.negativePositiveSignPlacement.left: 

              case AutoNumeric.options.negativePositiveSignPlacement.right:
                
                caretPosition = positiveNegativeSignSize + currencySymbolSize + integerSize;
                break;

              case AutoNumeric.options.negativePositiveSignPlacement.suffix:
                
                caretPosition = currencySymbolSize + integerSize;
                break;
            }
          } else {
            
            caretPosition = currencySymbolSize + integerSize;
          }
        } else if (this.settings.caretPositionOnFocus === AutoNumeric.options.caretPositionOnFocus.decimalRight) {
          if (this.settings.negativePositiveSignPlacement !== AutoNumeric.options.negativePositiveSignPlacement.none && (isValueNegative || !isValueNegative && this.settings.showPositiveSign && !isZeroOrHasNoValue)) {
            switch (this.settings.negativePositiveSignPlacement) {
              case AutoNumeric.options.negativePositiveSignPlacement.prefix: 

              case AutoNumeric.options.negativePositiveSignPlacement.left: 

              case AutoNumeric.options.negativePositiveSignPlacement.right:
                
                caretPosition = positiveNegativeSignSize + currencySymbolSize + integerSize + offsetDecimalChar;
                break;

              case AutoNumeric.options.negativePositiveSignPlacement.suffix:
                
                caretPosition = currencySymbolSize + integerSize + offsetDecimalChar;
                break;
            }
          } else {
            
            caretPosition = currencySymbolSize + integerSize + offsetDecimalChar;
          }
        }
      } else if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix) {
        if (this.settings.caretPositionOnFocus === AutoNumeric.options.caretPositionOnFocus.start) {
          if (this.settings.negativePositiveSignPlacement !== AutoNumeric.options.negativePositiveSignPlacement.none && (isValueNegative || !isValueNegative && this.settings.showPositiveSign && !isZeroOrHasNoValue)) {
            switch (this.settings.negativePositiveSignPlacement) {
              case AutoNumeric.options.negativePositiveSignPlacement.suffix: 

              case AutoNumeric.options.negativePositiveSignPlacement.right: 

              case AutoNumeric.options.negativePositiveSignPlacement.left:
                
                caretPosition = 0;
                break;

              case AutoNumeric.options.negativePositiveSignPlacement.prefix:
                
                caretPosition = positiveNegativeSignSize;
                break;
            }
          } else {
            
            caretPosition = 0;
          }
        } else if (this.settings.caretPositionOnFocus === AutoNumeric.options.caretPositionOnFocus.end) {
          if (this.settings.negativePositiveSignPlacement !== AutoNumeric.options.negativePositiveSignPlacement.none && (isValueNegative || !isValueNegative && this.settings.showPositiveSign && !isZeroOrHasNoValue)) {
            switch (this.settings.negativePositiveSignPlacement) {
              case AutoNumeric.options.negativePositiveSignPlacement.suffix: 

              case AutoNumeric.options.negativePositiveSignPlacement.right: 

              case AutoNumeric.options.negativePositiveSignPlacement.left:
                
                caretPosition = valueSize;
                break;

              case AutoNumeric.options.negativePositiveSignPlacement.prefix:
                
                caretPosition = positiveNegativeSignSize + valueSize;
                break;
            }
          } else {
            
            caretPosition = valueSize;
          }
        } else if (this.settings.caretPositionOnFocus === AutoNumeric.options.caretPositionOnFocus.decimalLeft) {
          if (this.settings.negativePositiveSignPlacement !== AutoNumeric.options.negativePositiveSignPlacement.none && (isValueNegative || !isValueNegative && this.settings.showPositiveSign && !isZeroOrHasNoValue)) {
            switch (this.settings.negativePositiveSignPlacement) {
              case AutoNumeric.options.negativePositiveSignPlacement.suffix: 

              case AutoNumeric.options.negativePositiveSignPlacement.right: 

              case AutoNumeric.options.negativePositiveSignPlacement.left:
                
                caretPosition = integerSize;
                break;

              case AutoNumeric.options.negativePositiveSignPlacement.prefix:
                
                caretPosition = positiveNegativeSignSize + integerSize;
                break;
            }
          } else {
            
            caretPosition = integerSize;
          }
        } else if (this.settings.caretPositionOnFocus === AutoNumeric.options.caretPositionOnFocus.decimalRight) {
          if (this.settings.negativePositiveSignPlacement !== AutoNumeric.options.negativePositiveSignPlacement.none && (isValueNegative || !isValueNegative && this.settings.showPositiveSign && !isZeroOrHasNoValue)) {
            switch (this.settings.negativePositiveSignPlacement) {
              case AutoNumeric.options.negativePositiveSignPlacement.suffix: 

              case AutoNumeric.options.negativePositiveSignPlacement.right: 

              case AutoNumeric.options.negativePositiveSignPlacement.left:
                
                caretPosition = integerSize + offsetDecimalChar;
                break;

              case AutoNumeric.options.negativePositiveSignPlacement.prefix:
                
                caretPosition = positiveNegativeSignSize + integerSize + offsetDecimalChar;
                break;
            }
          } else {
            
            caretPosition = integerSize + offsetDecimalChar;
          }
        }
      }

      return caretPosition;
    }
    

  }, {
    key: "_triggerRangeEvents",

    
    value: function _triggerRangeEvents(minTest, maxTest) {
      if (!minTest) {
        this._triggerEvent(AutoNumeric.events.minRangeExceeded, this.domElement);
      }

      if (!maxTest) {
        this._triggerEvent(AutoNumeric.events.maxRangeExceeded, this.domElement);
      }
    }
    

  }, {
    key: "_setInvalidState",
    value: function _setInvalidState() {
      if (this.isInputElement) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setInvalidState(this.domElement);
      } else {
        this._addCSSClass(this.settings.invalidClass);
      }

      this._triggerEvent(AutoNumeric.events.invalidValue, this.domElement);

      this.validState = false;
    }
    

  }, {
    key: "_setValidState",
    value: function _setValidState() {
      if (this.isInputElement) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setValidState(this.domElement);
      } else {
        this._removeCSSClass(this.settings.invalidClass);
      }

      if (!this.validState) {
        this._triggerEvent(AutoNumeric.events.correctedValue, this.domElement);
      }

      this.validState = true;
    }
    

  }, {
    key: "_setValidOrInvalidState",
    value: function _setValidOrInvalidState(value) {
      if (this.settings.overrideMinMaxLimits === AutoNumeric.options.overrideMinMaxLimits.invalid) {
        var minRangeOk = this.constructor._isMinimumRangeRespected(value, this.settings);

        var maxRangeOk = this.constructor._isMaximumRangeRespected(value, this.settings);

        if (minRangeOk && maxRangeOk) {
          this._setValidState();
        } else {
          this._setInvalidState();
        }

        this._triggerRangeEvents(minRangeOk, maxRangeOk);
      }
    }
    

  }, {
    key: "_keepAnOriginalSettingsCopy",
    value: function _keepAnOriginalSettingsCopy() {
      this.originalDigitGroupSeparator = this.settings.digitGroupSeparator;
      this.originalCurrencySymbol = this.settings.currencySymbol;
      this.originalSuffixText = this.settings.suffixText;
    }
    

  }, {
    key: "_trimLeadingAndTrailingZeros",

    
    value: function _trimLeadingAndTrailingZeros(value) {
      
      if (value === '' || value === null) {
        return value;
      }

      if (this.settings.leadingZero !== AutoNumeric.options.leadingZero.keep) {
        if (Number(value) === 0) {
          
          return '0';
        } 


        value = value.replace(/^(-)?0+(?=\d)/g, '$1');
      } 
      


      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(value, '.')) {
        value = value.replace(/(\.[0-9]*?)0+$/, '$1');
      } 


      value = value.replace(/\.$/, '');
      return value;
    }
    

  }, {
    key: "_setPersistentStorageName",
    value: function _setPersistentStorageName() {
      if (this.settings.saveValueToSessionStorage) {
        if (this.domElement.name !== '' && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(this.domElement.name)) {
          this.rawValueStorageName = "".concat(this.storageNamePrefix).concat(decodeURIComponent(this.domElement.name));
        } else {
          this.rawValueStorageName = "".concat(this.storageNamePrefix).concat(this.domElement.id);
        }
      }
    }
    

  }, {
    key: "_saveValueToPersistentStorage",
    value: function _saveValueToPersistentStorage() {
      if (this.settings.saveValueToSessionStorage) {
        if (this.sessionStorageAvailable) {
          sessionStorage.setItem(this.rawValueStorageName, this.rawValue);
        } else {
          
          document.cookie = "".concat(this.rawValueStorageName, "=").concat(this.rawValue, "; expires= ; path=/");
        }
      }
    }
    

  }, {
    key: "_getValueFromPersistentStorage",
    value: function _getValueFromPersistentStorage() {
      if (this.settings.saveValueToSessionStorage) {
        var result;

        if (this.sessionStorageAvailable) {
          result = sessionStorage.getItem(this.rawValueStorageName);
        } else {
          result = this.constructor._readCookie(this.rawValueStorageName);
        }

        return result;
      }

      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning('`_getValueFromPersistentStorage()` is called but `settings.saveValueToSessionStorage` is false. There must be an error that needs fixing.', this.settings.showWarnings);
      return null;
    }
    

  }, {
    key: "_removeValueFromPersistentStorage",
    value: function _removeValueFromPersistentStorage() {
      if (this.settings.saveValueToSessionStorage) {
        if (this.sessionStorageAvailable) {
          sessionStorage.removeItem(this.rawValueStorageName);
        } else {
          var date = new Date();
          date.setTime(date.getTime() - 86400000); 

          var expires = "; expires=".concat(date.toUTCString());
          document.cookie = "".concat(this.rawValueStorageName, "='' ;").concat(expires, "; path=/");
        }
      }
    }
    

  }, {
    key: "_getDefaultValue",
    value: function _getDefaultValue(domElement) {
      
      
      var value = domElement.getAttribute('value');

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(value)) {
        return '';
      }

      return value;
    }
    

  }, {
    key: "_onFocusInAndMouseEnter",
    value: function _onFocusInAndMouseEnter(e) {
      
      this.isEditing = false; 

      if (!this.formulaMode && this.settings.unformatOnHover && e.type === 'mouseenter' && e.altKey) {
        this.constructor._unformatAltHovered(this);

        return;
      }

      if (e.type === 'focus') {
        
        
        this.isFocused = true;
        this.rawValueOnFocus = this.rawValue; 
      }

      if (e.type === 'focus' && this.settings.unformatOnHover && this.hoveredWithAlt) {
        this.constructor._reformatAltHovered(this);
      }

      if (e.type === 'focus' || e.type === 'mouseenter' && !this.isFocused) {
        var elementValueToSet = null; 

        if (this.settings.emptyInputBehavior === AutoNumeric.options.emptyInputBehavior.focus && this.rawValue < 0 && this.settings.negativeBracketsTypeOnBlur !== null && this.settings.isNegativeSignAllowed) {
          
          
          elementValueToSet = this.constructor._removeBrackets(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement), this.settings); 
        } 


        var rawValueToFormat = this._getRawValueToFormat(this.rawValue); 


        if (rawValueToFormat !== '') {
          
          var roundedValue = this.constructor._roundFormattedValueShownOnFocusOrBlur(rawValueToFormat, this.settings, this.isFocused);

          if (this.settings.showOnlyNumbersOnFocus === AutoNumeric.options.showOnlyNumbersOnFocus.onlyNumbers) {
            
            this.settings.digitGroupSeparator = '';
            this.settings.currencySymbol = '';
            this.settings.suffixText = '';
            elementValueToSet = roundedValue.replace('.', this.settings.decimalCharacter);
          } else {
            var formattedValue;

            if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(roundedValue)) {
              formattedValue = '';
            } else {
              formattedValue = this.constructor._addGroupSeparators(roundedValue.replace('.', this.settings.decimalCharacter), this.settings, this.isFocused, rawValueToFormat);
            }

            elementValueToSet = formattedValue;
          }
        } 


        if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(elementValueToSet)) {
          this.valueOnFocus = '';
        } else {
          this.valueOnFocus = elementValueToSet;
        }

        this.lastVal = this.valueOnFocus;

        var isEmptyValue = this.constructor._isElementValueEmptyOrOnlyTheNegativeSign(this.valueOnFocus, this.settings);

        var orderedValue = this.constructor._orderValueCurrencySymbolAndSuffixText(this.valueOnFocus, this.settings, true); 


        var orderedValueTest = isEmptyValue && orderedValue !== '' && this.settings.emptyInputBehavior === AutoNumeric.options.emptyInputBehavior.focus;

        if (orderedValueTest) {
          elementValueToSet = orderedValue;
        }

        if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(elementValueToSet)) {
          this._setElementValue(elementValueToSet);
        }

        if (orderedValueTest && orderedValue === this.settings.currencySymbol && this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix) {
          
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(e.target, 0);
        }
      }
    }
    

  }, {
    key: "_onFocus",
    value: function _onFocus() {
      if (this.settings.isCancellable) {
        
        this._saveCancellableValue();
      }
    }
    

  }, {
    key: "_onFocusIn",
    value: function _onFocusIn(e) {
      if (this.settings.selectOnFocus) {
        
        
        this.select();
      } else {
        
        if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(this.settings.caretPositionOnFocus)) {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(e.target, this._initialCaretPosition(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement)));
        }
      }
    }
    

  }, {
    key: "_enterFormulaMode",
    value: function _enterFormulaMode() {
      if (this.settings.formulaMode) {
        this.formulaMode = true; 
        

        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementValue(this.domElement, '='); 

        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(this.domElement, 1);
      }
    }
    

  }, {
    key: "_exitFormulaMode",
    value: function _exitFormulaMode() {
      
      var formula = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement);
      formula = formula.replace(/^\s*=/, ''); 

      var result;

      try {
        var ast = new _maths_Parser__WEBPACK_IMPORTED_MODULE_3__["default"](formula, this.settings.decimalCharacter);
        result = new _maths_Evaluator__WEBPACK_IMPORTED_MODULE_2__["default"]().evaluate(ast);
      } catch (e) {
        
        this._triggerEvent(AutoNumeric.events.invalidFormula, this.domElement, {
          formula: formula,
          aNElement: this
        });

        this.reformat();
        this.formulaMode = false;
        return;
      } 


      this._triggerEvent(AutoNumeric.events.validFormula, this.domElement, {
        formula: formula,
        result: result,
        aNElement: this
      });

      this.set(result); 

      this.formulaMode = false;
    }
    

  }, {
    key: "_acceptNonPrintableKeysInFormulaMode",
    value: function _acceptNonPrintableKeysInFormulaMode() {
      return this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Backspace || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Delete || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.LeftArrow || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.RightArrow || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Home || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.End;
    }
    

  }, {
    key: "_onKeydown",
    value: function _onKeydown(e) {
      this.formatted = false; 

      this.isEditing = true; 

      if (!this.formulaMode && !this.isFocused && this.settings.unformatOnHover && e.altKey && this.domElement === _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getHoveredElement()) {
        
        this.constructor._unformatAltHovered(this);

        return;
      }

      this._updateEventKeyInfo(e);

      this.keydownEventCounter += 1; 

      if (this.keydownEventCounter === 1) {
        this.initialValueOnFirstKeydown = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(e.target); 

        this.initialRawValueOnFirstKeydown = this.rawValue;
      }

      if (this.formulaMode) {
        if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Esc) {
          
          this.formulaMode = false;
          this.reformat();
          return;
        }

        if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Enter) {
          
          this._exitFormulaMode();

          return;
        } 


        if (this._acceptNonPrintableKeysInFormulaMode()) {
          return;
        } 
        

      } else if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Equal) {
        this._enterFormulaMode();

        return;
      }

      if (this.domElement.readOnly || this.settings.readOnly || this.domElement.disabled) {
        this.processed = true;
        return;
      }

      if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Esc) {
        
        e.preventDefault();

        if (this.settings.isCancellable) {
          
          
          if (this.rawValue !== this.savedCancellableValue) {
            
            this.set(this.savedCancellableValue); 

            this._triggerEvent(AutoNumeric.events["native"].input, e.target);
          }
        } 


        this.select(); 
      } 


      var targetValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(e.target);

      if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Enter && this.rawValue !== this.rawValueOnFocus) {
        this._triggerEvent(AutoNumeric.events["native"].change, e.target);

        this.valueOnFocus = targetValue;
        this.rawValueOnFocus = this.rawValue;

        if (this.settings.isCancellable) {
          
          this._saveCancellableValue();
        }
      }

      this._updateInternalProperties(e);

      if (this._processNonPrintableKeysAndShortcuts(e)) {
        this.processed = true;
        return;
      } 


      if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Backspace || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Delete) {
        var isDeletionAllowed = this._processCharacterDeletion(); 


        this.processed = true;

        if (!isDeletionAllowed) {
          
          e.preventDefault();
          return;
        }

        this._formatValue(e); 


        targetValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(e.target); 

        if (targetValue !== this.lastVal && this.throwInput) {
          
          this._triggerEvent(AutoNumeric.events["native"].input, e.target);

          e.preventDefault(); 
        }

        this.lastVal = targetValue;
        this.throwInput = true;
      }
    }
    

  }, {
    key: "_onKeypress",
    value: function _onKeypress(e) {
      if (this.formulaMode) {
        
        if (this._acceptNonPrintableKeysInFormulaMode()) {
          return;
        } 


        if (this.settings.formulaChars.test(this.eventKey)) {
          
          return; 
        } else {
          e.preventDefault(); 
        }

        return;
      }

      if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Insert) {
        return;
      }

      var processed = this.processed;

      this._updateInternalProperties(e);

      if (this._processNonPrintableKeysAndShortcuts(e)) {
        return;
      }

      if (processed) {
        e.preventDefault();
        return;
      }

      var isCharacterInsertionAllowed = this._processCharacterInsertion();

      if (isCharacterInsertionAllowed) {
        this._formatValue(e);

        var targetValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(e.target);

        if (targetValue !== this.lastVal && this.throwInput) {
          
          this._triggerEvent(AutoNumeric.events["native"].input, e.target);

          e.preventDefault(); 
        } else {
          if ((this.eventKey === this.settings.decimalCharacter || this.eventKey === this.settings.decimalCharacterAlternative) && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementSelection(e.target).start === _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementSelection(e.target).end && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementSelection(e.target).start === targetValue.indexOf(this.settings.decimalCharacter)) {
            var position = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementSelection(e.target).start + 1;
            _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(e.target, position);
          }

          e.preventDefault();
        }

        this.lastVal = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(e.target);
        this.throwInput = true;

        this._setValidOrInvalidState(this.rawValue); 


        return;
      }

      e.preventDefault();
    }
    

  }, {
    key: "_onKeyup",
    value: function _onKeyup(e) {
      this.isEditing = false;
      this.keydownEventCounter = 0; 

      if (this.formulaMode) {
        return;
      }

      if (this.settings.isCancellable && this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Esc) {
        
        e.preventDefault();
        return;
      } 


      if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Z || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.z) {
        if (e.ctrlKey && e.shiftKey) {
          
          e.preventDefault();

          this._historyTableRedo();

          this.onGoingRedo = true;
          return;
        } else if (e.ctrlKey && !e.shiftKey) {
          if (this.onGoingRedo) {
            
            this.onGoingRedo = false;
          } else {
            e.preventDefault(); 

            this._historyTableUndo();

            return;
          }
        }
      }

      if (this.onGoingRedo && (e.ctrlKey || e.shiftKey)) {
        
        this.onGoingRedo = false;
      } 


      if ((e.ctrlKey || e.metaKey) && this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.x) {
        
        var caretPosition = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementSelection(this.domElement).start; 

        var cutNumber = this.constructor._toNumericValue(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(e.target), this.settings); 


        this.set(cutNumber); 

        this._setCaretPosition(caretPosition);
      } 


      if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Alt && this.settings.unformatOnHover && this.hoveredWithAlt) {
        this.constructor._reformatAltHovered(this);

        return;
      } 


      if ((e.ctrlKey || e.metaKey) && (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Backspace || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Delete)) {
        var _targetValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(e.target);

        this._setRawValue(this._formatOrUnformatOther(false, _targetValue));

        return;
      }

      this._updateInternalProperties(e);

      var skip = this._processNonPrintableKeysAndShortcuts(e);

      delete this.valuePartsBeforePaste;
      var targetValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(e.target);

      if (skip || targetValue === '' && this.initialValueOnFirstKeydown === '') {
        
        return;
      } 


      if (targetValue === this.settings.currencySymbol) {
        if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix) {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(e.target, 0);
        } else {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(e.target, this.settings.currencySymbol.length);
        }
      } else if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Tab) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(e.target, 0, targetValue.length);
      }

      if (targetValue === this.settings.suffixText || this.rawValue === '' && this.settings.currencySymbol !== '' && this.settings.suffixText !== '') {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(e.target, 0);
      } 


      if (this.settings.decimalPlacesShownOnFocus !== null) {
        this._saveValueToPersistentStorage();
      }

      if (!this.formatted) {
        
        this._formatValue(e);
      }

      this._setValidOrInvalidState(this.rawValue); 


      this._saveRawValueForAndroid(); 


      if (targetValue !== this.initialValueOnFirstKeydown) {
        
        this._triggerEvent(AutoNumeric.events.formatted, e.target, {
          oldValue: this.initialValueOnFirstKeydown,
          newValue: targetValue,
          oldRawValue: this.initialRawValueOnFirstKeydown,
          newRawValue: this.rawValue,
          isPristine: this.isPristine(false),
          error: null,
          aNElement: this
        });
      } 


      if (this.historyTable.length > 1) {
        var selection = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementSelection(this.domElement);
        this.selectionStart = selection.start;
        this.selectionEnd = selection.end;
        this.historyTable[this.historyTableIndex].start = this.selectionStart;
        this.historyTable[this.historyTableIndex].end = this.selectionEnd;
      }
    }
    

  }, {
    key: "_saveRawValueForAndroid",
    value: function _saveRawValueForAndroid() {
      if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.AndroidDefault) {
        var normalizedValue = this.constructor._stripAllNonNumberCharactersExceptCustomDecimalChar(this.getFormatted(), this.settings, true, this.isFocused);

        normalizedValue = this.constructor._convertToNumericString(normalizedValue, this.settings);

        this._setRawValue(normalizedValue);
      }
    }
    

  }, {
    key: "_onFocusOutAndMouseLeave",
    value: function _onFocusOutAndMouseLeave(e) {
      
      this.isEditing = false; 

      if (e.type === 'mouseleave' && this.formulaMode) {
        return;
      } 


      if (this.settings.unformatOnHover && e.type === 'mouseleave' && this.hoveredWithAlt) {
        this.constructor._reformatAltHovered(this);

        return;
      }

      if (e.type === 'mouseleave' && !this.isFocused || e.type === 'blur') {
        if (e.type === 'blur' && this.formulaMode) {
          this._exitFormulaMode();
        }

        this._saveValueToPersistentStorage();

        if (this.settings.showOnlyNumbersOnFocus === AutoNumeric.options.showOnlyNumbersOnFocus.onlyNumbers) {
          this.settings.digitGroupSeparator = this.originalDigitGroupSeparator;
          this.settings.currencySymbol = this.originalCurrencySymbol;
          this.settings.suffixText = this.originalSuffixText;
        } 


        var rawValueToFormat = this._getRawValueToFormat(this.rawValue);

        var isRawValueNull = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(rawValueToFormat);

        var _this$constructor$_ch3 = this.constructor._checkIfInRangeWithOverrideOption(rawValueToFormat, this.settings),
            _this$constructor$_ch4 = _slicedToArray(_this$constructor$_ch3, 2),
            minTest = _this$constructor$_ch4[0],
            maxTest = _this$constructor$_ch4[1]; 


        var elementValueIsAlreadySet = false;

        if (rawValueToFormat !== '' && !isRawValueNull) {
          this._triggerRangeEvents(minTest, maxTest);

          if (this.settings.valuesToStrings && this._checkValuesToStrings(rawValueToFormat)) {
            
            this._setElementValue(this.settings.valuesToStrings[rawValueToFormat]);

            elementValueIsAlreadySet = true;
          }
        } 


        if (!elementValueIsAlreadySet) {
          var value;

          if (isRawValueNull || rawValueToFormat === '') {
            value = rawValueToFormat;
          } else {
            value = String(rawValueToFormat);
          }

          if (rawValueToFormat !== '' && !isRawValueNull) {
            if (minTest && maxTest && !this.constructor._isElementValueEmptyOrOnlyTheNegativeSign(rawValueToFormat, this.settings)) {
              value = this._modifyNegativeSignAndDecimalCharacterForRawValue(value);

              if (this.settings.divisorWhenUnfocused && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(value)) {
                value = value / this.settings.divisorWhenUnfocused;
                value = value.toString();
              }

              value = this.constructor._roundFormattedValueShownOnBlur(value, this.settings);
              value = this.constructor._modifyNegativeSignAndDecimalCharacterForFormattedValue(value, this.settings);
            } else {
              this._triggerRangeEvents(minTest, maxTest);
            }
          } else if (rawValueToFormat === '') {
            switch (this.settings.emptyInputBehavior) {
              case AutoNumeric.options.emptyInputBehavior.zero:
                this._setRawValue('0');

                value = this.constructor._roundValue('0', this.settings, 0);
                break;

              case AutoNumeric.options.emptyInputBehavior.min:
                this._setRawValue(this.settings.minimumValue);

                value = this.constructor._roundFormattedValueShownOnFocusOrBlur(this.settings.minimumValue, this.settings, this.isFocused);
                break;

              case AutoNumeric.options.emptyInputBehavior.max:
                this._setRawValue(this.settings.maximumValue);

                value = this.constructor._roundFormattedValueShownOnFocusOrBlur(this.settings.maximumValue, this.settings, this.isFocused);
                break;

              default:
                if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(this.settings.emptyInputBehavior)) {
                  this._setRawValue(this.settings.emptyInputBehavior);

                  value = this.constructor._roundFormattedValueShownOnFocusOrBlur(this.settings.emptyInputBehavior, this.settings, this.isFocused);
                }

            }
          }

          var groupedValue = this.constructor._orderValueCurrencySymbolAndSuffixText(value, this.settings, false);

          if (!(this.constructor._isElementValueEmptyOrOnlyTheNegativeSign(value, this.settings) || isRawValueNull && this.settings.emptyInputBehavior === AutoNumeric.options.emptyInputBehavior["null"])) {
            groupedValue = this.constructor._addGroupSeparators(value, this.settings, false, rawValueToFormat);
          } 


          if (groupedValue !== rawValueToFormat || rawValueToFormat === '' || 
          this.settings.allowDecimalPadding === AutoNumeric.options.allowDecimalPadding.never || this.settings.allowDecimalPadding === AutoNumeric.options.allowDecimalPadding.floats) {
            if (this.settings.symbolWhenUnfocused && rawValueToFormat !== '' && rawValueToFormat !== null) {
              groupedValue = "".concat(groupedValue).concat(this.settings.symbolWhenUnfocused);
            }

            this._setElementValue(groupedValue);
          }
        }

        this._setValidOrInvalidState(this.rawValue);

        if (e.type === 'blur') {
          
          this._onBlur(e);
        }
      }
    }
    

  }, {
    key: "_onPaste",
    value: function _onPaste(e) {
      
      
      e.preventDefault();

      if (this.settings.readOnly || this.domElement.readOnly || this.domElement.disabled) {
        
        return;
      }

      var rawPastedText;

      if (window.clipboardData && window.clipboardData.getData) {
        
        rawPastedText = window.clipboardData.getData('Text');
      } else if (e.clipboardData && e.clipboardData.getData) {
        
        rawPastedText = e.clipboardData.getData('text/plain');
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError('Unable to retrieve the pasted value. Please use a modern browser (ie. Firefox or Chromium).');
      } 


      var eventTarget;

      if (!e.target.tagName) {
        eventTarget = e.explicitOriginalTarget;
      } else {
        eventTarget = e.target;
      } 


      var initialFormattedValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(eventTarget);
      var selectionStart = eventTarget.selectionStart || 0;
      var selectionEnd = eventTarget.selectionEnd || 0;
      var selectionSize = selectionEnd - selectionStart;

      if (selectionSize === initialFormattedValue.length) {
        
        
        
        
        var _untranslatedPastedText = this._preparePastedText(rawPastedText);

        var pastedRawValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].arabicToLatinNumbers(_untranslatedPastedText, false, false, false); 
        

        if (pastedRawValue === '.' || pastedRawValue === '' || pastedRawValue !== '.' && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(pastedRawValue)) {
          this.formatted = true; 
          

          if (this.settings.onInvalidPaste === AutoNumeric.options.onInvalidPaste.error) {
            _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The pasted value '".concat(rawPastedText, "' is not a valid paste content."));
          }

          return;
        } 


        this.set(pastedRawValue);
        this.formatted = true; 

        this._triggerEvent(AutoNumeric.events["native"].input, eventTarget); 


        return;
      } 


      var isPasteNegative = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegativeStrict(rawPastedText, this.settings.negativeSignCharacter);

      if (isPasteNegative) {
        
        rawPastedText = rawPastedText.slice(1, rawPastedText.length);
      } 


      var untranslatedPastedText = this._preparePastedText(rawPastedText);

      var pastedText;

      if (untranslatedPastedText === '.') {
        
        pastedText = '.';
      } else {
        
        
        pastedText = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].arabicToLatinNumbers(untranslatedPastedText, false, false, false);
      } 


      if (pastedText !== '.' && (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(pastedText) || pastedText === '')) {
        this.formatted = true; 

        if (this.settings.onInvalidPaste === AutoNumeric.options.onInvalidPaste.error) {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The pasted value '".concat(rawPastedText, "' is not a valid paste content."));
        }

        return;
      } 


      var caretPositionOnInitialTextAfterPasting;
      var isInitialValueNegative = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegativeStrict(this.getNumericString(), this.settings.negativeSignCharacter);
      var isPasteNegativeAndInitialValueIsPositive;
      var result; 

      if (isPasteNegative && !isInitialValueNegative) {
        isInitialValueNegative = true;
        isPasteNegativeAndInitialValueIsPositive = true;
      } else {
        isPasteNegativeAndInitialValueIsPositive = false;
      } 


      var leftFormattedPart = initialFormattedValue.slice(0, selectionStart);
      var rightFormattedPart = initialFormattedValue.slice(selectionEnd, initialFormattedValue.length);

      if (selectionStart !== selectionEnd) {
        
        result = this._preparePastedText(leftFormattedPart + rightFormattedPart);
      } else {
        
        result = this._preparePastedText(initialFormattedValue);
      } 


      if (isInitialValueNegative) {
        result = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setRawNegativeSign(result);
      } 


      caretPositionOnInitialTextAfterPasting = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].convertCharacterCountToIndexPosition(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].countNumberCharactersOnTheCaretLeftSide(initialFormattedValue, selectionStart, this.settings.decimalCharacter));

      if (isPasteNegativeAndInitialValueIsPositive) {
        
        caretPositionOnInitialTextAfterPasting++; 
        
      }

      var leftPart = result.slice(0, caretPositionOnInitialTextAfterPasting);
      var rightPart = result.slice(caretPositionOnInitialTextAfterPasting, result.length);
      var leftPartContainedADot = false;

      if (pastedText === '.') {
        if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(leftPart, '.')) {
          
          
          leftPartContainedADot = true;
          leftPart = leftPart.replace('.', '');
        }

        rightPart = rightPart.replace('.', '');
      } 


      var negativePasteOnNegativeNumber = false;

      if (leftPart === '' && rightPart === '-') {
        leftPart = '-';
        rightPart = ''; 

        negativePasteOnNegativeNumber = true;
      } 


      switch (this.settings.onInvalidPaste) {
        

        
        case AutoNumeric.options.onInvalidPaste.truncate:
        case AutoNumeric.options.onInvalidPaste.replace:
          
          
          var minParse = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].parseStr(this.settings.minimumValue);
          var maxParse = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].parseStr(this.settings.maximumValue);
          var lastGoodKnownResult = result; 

          var pastedTextIndex = 0;
          var modifiedLeftPart = leftPart;

          while (pastedTextIndex < pastedText.length) {
            
            modifiedLeftPart += pastedText[pastedTextIndex];
            result = modifiedLeftPart + rightPart; 

            if (!this.constructor._checkIfInRange(result, minParse, maxParse)) {
              
              break;
            } 


            lastGoodKnownResult = result; 

            pastedTextIndex++;
          } 


          caretPositionOnInitialTextAfterPasting += pastedTextIndex;
          if (negativePasteOnNegativeNumber) caretPositionOnInitialTextAfterPasting++; 

          if (this.settings.onInvalidPaste === AutoNumeric.options.onInvalidPaste.truncate) {
            
            result = lastGoodKnownResult;

            if (leftPartContainedADot) {
              
              caretPositionOnInitialTextAfterPasting--;
            }

            break;
          } 
          
          
          
          
          


          var lastGoodKnownResultIndex = caretPositionOnInitialTextAfterPasting;
          var lastGoodKnownResultSize = lastGoodKnownResult.length;

          while (pastedTextIndex < pastedText.length && lastGoodKnownResultIndex < lastGoodKnownResultSize) {
            if (lastGoodKnownResult[lastGoodKnownResultIndex] === '.') {
              
              lastGoodKnownResultIndex++;
              continue;
            } 


            result = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].replaceCharAt(lastGoodKnownResult, lastGoodKnownResultIndex, pastedText[pastedTextIndex]); 

            if (!this.constructor._checkIfInRange(result, minParse, maxParse)) {
              
              break;
            } 


            lastGoodKnownResult = result; 

            pastedTextIndex++;
            lastGoodKnownResultIndex++;
          } 


          caretPositionOnInitialTextAfterPasting = lastGoodKnownResultIndex;

          if (leftPartContainedADot) {
            
            caretPositionOnInitialTextAfterPasting--;
          }

          result = lastGoodKnownResult;
          break;

        

        case AutoNumeric.options.onInvalidPaste.error:
        case AutoNumeric.options.onInvalidPaste.ignore:
        case AutoNumeric.options.onInvalidPaste.clamp:
        default:
          
          result = "".concat(leftPart).concat(pastedText).concat(rightPart); 

          if (selectionStart === selectionEnd) {
            
            var indexWherePastedTextHasBeenInserted = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].convertCharacterCountToIndexPosition(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].countNumberCharactersOnTheCaretLeftSide(initialFormattedValue, selectionStart, this.settings.decimalCharacter));
            caretPositionOnInitialTextAfterPasting = indexWherePastedTextHasBeenInserted + pastedText.length; 
          } else if (rightPart === '') {
            
            caretPositionOnInitialTextAfterPasting = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].convertCharacterCountToIndexPosition(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].countNumberCharactersOnTheCaretLeftSide(initialFormattedValue, selectionStart, this.settings.decimalCharacter)) + pastedText.length;
            if (negativePasteOnNegativeNumber) caretPositionOnInitialTextAfterPasting++;
          } else {
            
            var indexSelectionEndInRawValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].convertCharacterCountToIndexPosition(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].countNumberCharactersOnTheCaretLeftSide(initialFormattedValue, selectionEnd, this.settings.decimalCharacter)); 

            var selectedText = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(eventTarget).slice(selectionStart, selectionEnd);
            caretPositionOnInitialTextAfterPasting = indexSelectionEndInRawValue - selectionSize + _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].countCharInText(this.settings.digitGroupSeparator, selectedText) + pastedText.length;
          } 


          if (isPasteNegativeAndInitialValueIsPositive) {
            
            caretPositionOnInitialTextAfterPasting++;
          }

          if (leftPartContainedADot) {
            
            caretPositionOnInitialTextAfterPasting--;
          }

      } 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(result) || result === '') {
        if (this.settings.onInvalidPaste === AutoNumeric.options.onInvalidPaste.error) {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The pasted value '".concat(rawPastedText, "' would result into an invalid content '").concat(result, "'.")); 
          
        }

        return;
      } 

      


      var valueHasBeenSet = false;
      var valueHasBeenClamped = false;

      try {
        this.set(result);
        valueHasBeenSet = true;
      } catch (error) {
        var clampedValue;

        switch (this.settings.onInvalidPaste) {
          case AutoNumeric.options.onInvalidPaste.clamp:
            clampedValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].clampToRangeLimits(result, this.settings);

            try {
              this.set(clampedValue);
            } catch (error) {
              _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("Fatal error: Unable to set the clamped value '".concat(clampedValue, "'."));
            }

            valueHasBeenClamped = true;
            valueHasBeenSet = true;
            result = clampedValue; 

            break;

          case AutoNumeric.options.onInvalidPaste.error:
          case AutoNumeric.options.onInvalidPaste.truncate:
          case AutoNumeric.options.onInvalidPaste.replace:
            
            _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The pasted value '".concat(rawPastedText, "' results in a value '").concat(result, "' that is outside of the minimum [").concat(this.settings.minimumValue, "] and maximum [").concat(this.settings.maximumValue, "] value range."));
          

          case AutoNumeric.options.onInvalidPaste.ignore: 
          

          default:
            return;
          
        }
      } 


      var targetValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(eventTarget);
      var caretPositionInFormattedNumber;

      if (valueHasBeenSet) {
        switch (this.settings.onInvalidPaste) {
          case AutoNumeric.options.onInvalidPaste.clamp:
            if (valueHasBeenClamped) {
              if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix) {
                _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(eventTarget, targetValue.length - this.settings.currencySymbol.length); 
              } else {
                _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(eventTarget, targetValue.length); 
              }

              break;
            }

          
          

          case AutoNumeric.options.onInvalidPaste.error:
          case AutoNumeric.options.onInvalidPaste.ignore:
          case AutoNumeric.options.onInvalidPaste.truncate:
          case AutoNumeric.options.onInvalidPaste.replace:
          default:
            
            caretPositionInFormattedNumber = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].findCaretPositionInFormattedNumber(result, caretPositionOnInitialTextAfterPasting, targetValue, this.settings.decimalCharacter);
            _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(eventTarget, caretPositionInFormattedNumber);
        }
      } 


      if (valueHasBeenSet && initialFormattedValue !== targetValue) {
        
        this._triggerEvent(AutoNumeric.events["native"].input, eventTarget);
      }
    }
    

  }, {
    key: "_onBlur",
    value: function _onBlur(e) {
      
      this.isFocused = false; 

      this.isEditing = false; 

      if (this.rawValue !== this.rawValueOnFocus) {
        this._triggerEvent(AutoNumeric.events["native"].change, e.target);
      }

      this.rawValueOnFocus = void 0; 
    }
    

  }, {
    key: "_onWheel",
    value: function _onWheel(e) {
      if (this.formulaMode) {
        return;
      }

      if (this.settings.readOnly || this.domElement.readOnly || this.domElement.disabled) {
        
        return;
      }

      if (this.settings.modifyValueOnWheel) {
        if (this.settings.wheelOn === AutoNumeric.options.wheelOn.focus) {
          if (this.isFocused) {
            if (!e.shiftKey) {
              this.wheelAction(e);
            }
          } else if (e.shiftKey) {
            this.wheelAction(e);
          }
        } else if (this.settings.wheelOn === AutoNumeric.options.wheelOn.hover) {
          if (!e.shiftKey) {
            this.wheelAction(e);
          } else {
            
            
            e.preventDefault(); 
            

            window.scrollBy(0, _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegativeStrict(String(e.deltaY)) ? -50 : 50); 
          }
        } else {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError('Unknown `wheelOn` option.');
        }
      }
    }
    

  }, {
    key: "wheelAction",
    value: function wheelAction(e) {
      this.isWheelEvent = true; 
      

      var selectionStart = e.target.selectionStart || 0;
      var selectionEnd = e.target.selectionEnd || 0; 

      var currentUnformattedValue = this.rawValue;
      var result;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(currentUnformattedValue)) {
        
        if (this.settings.minimumValue > 0 || this.settings.maximumValue < 0) {
          
          if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isWheelUpEvent(e)) {
            result = this.settings.minimumValue;
          } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isWheelDownEvent(e)) {
            result = this.settings.maximumValue;
          } else {
            _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The event is not a 'wheel' event.");
          }
        } else {
          result = 0;
        }
      } else {
        result = currentUnformattedValue;
      }

      result = +result; 
      
      

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(this.settings.wheelStep)) {
        var step = +this.settings.wheelStep; 
        
        

        if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isWheelUpEvent(e)) {
          
          result += step;
        } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isWheelDownEvent(e)) {
          
          result -= step;
        }
      } else {
        
        
        
        
        if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isWheelUpEvent(e)) {
          
          result = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].addAndRoundToNearestAuto(result, this.settings.decimalPlacesRawValue);
        } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isWheelDownEvent(e)) {
          
          result = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].subtractAndRoundToNearestAuto(result, this.settings.decimalPlacesRawValue);
        }
      } 
      


      result = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].clampToRangeLimits(result, this.settings);

      if (result !== +currentUnformattedValue) {
        
        this.set(result); 

        this._triggerEvent(AutoNumeric.events["native"].input, e.target);
      } 


      e.preventDefault(); 
      
      

      this._setSelection(selectionStart, selectionEnd);

      this.isWheelEvent = false; 
    }
    

  }, {
    key: "_onDrop",
    value: function _onDrop(e) {
      if (this.formulaMode) {
        
        return;
      } 


      this.isDropEvent = true;
      e.preventDefault();
      var format;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isIE11()) {
        format = 'text';
      } else {
        format = 'text/plain';
      }

      var droppedText = e.dataTransfer.getData(format);
      var cleanedValue = this.unformatOther(droppedText);
      this.set(cleanedValue);
      this.isDropEvent = false;
    }
    

  }, {
    key: "_onFormSubmit",
    value: function _onFormSubmit() {
      var _this10 = this;

      
      var inputElements = this._getFormAutoNumericChildren(this.parentForm);

      var aNElements = inputElements.map(function (aNElement) {
        return _this10.constructor.getAutoNumericElement(aNElement);
      });
      aNElements.forEach(function (aNElement) {
        return aNElement._unformatOnSubmit();
      });
      return true;
    }
    

  }, {
    key: "_onFormReset",
    value: function _onFormReset() {
      var _this11 = this;

      var inputElements = this._getFormAutoNumericChildren(this.parentForm);

      var aNElements = inputElements.map(function (aNElement) {
        return _this11.constructor.getAutoNumericElement(aNElement);
      }); 

      aNElements.forEach(function (aNElement) {
        var val = _this11._getDefaultValue(aNElement.node()); 


        setTimeout(function () {
          return aNElement.set(val);
        }, 0); 
      });
    }
    

  }, {
    key: "_unformatOnSubmit",
    value: function _unformatOnSubmit() {
      if (this.settings.unformatOnSubmit) {
        this._setElementValue(this.rawValue);
      }
    }
    

  }, {
    key: "_onKeydownGlobal",
    value: function _onKeydownGlobal(e) {
      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].character(e) === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Alt) {
        var hoveredElement = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getHoveredElement();

        if (AutoNumeric.isManagedByAutoNumeric(hoveredElement)) {
          var anElement = AutoNumeric.getAutoNumericElement(hoveredElement);

          if (!anElement.formulaMode && anElement.settings.unformatOnHover) {
            this.constructor._unformatAltHovered(anElement);
          }
        }
      }
    }
    

  }, {
    key: "_onKeyupGlobal",
    value: function _onKeyupGlobal(e) {
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].character(e) === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Alt) {
        var hoveredElement = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getHoveredElement();

        if (AutoNumeric.isManagedByAutoNumeric(hoveredElement)) {
          var anElement = AutoNumeric.getAutoNumericElement(hoveredElement);

          if (anElement.formulaMode || !anElement.settings.unformatOnHover) {
            return;
          }

          this.constructor._reformatAltHovered(anElement);
        }
      }
    }
    

  }, {
    key: "_isElementTagSupported",
    value: function _isElementTagSupported() {
      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isElement(this.domElement)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The DOM element is not valid, ".concat(this.domElement, " given."));
      }

      return _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(this.domElement.tagName.toLowerCase(), this.allowedTagList);
    }
    

  }, {
    key: "_isInputElement",
    value: function _isInputElement() {
      return this.domElement.tagName.toLowerCase() === 'input';
    }
    

  }, {
    key: "_isInputTypeSupported",
    value: function _isInputTypeSupported() {
      return this.domElement.type === 'text' || this.domElement.type === 'hidden' || this.domElement.type === 'tel' || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(this.domElement.type);
    }
    

  }, {
    key: "_checkElement",
    value: function _checkElement() {
      var currentElementTag = this.domElement.tagName.toLowerCase();

      if (!this._isElementTagSupported()) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The <".concat(currentElementTag, "> tag is not supported by autoNumeric"));
      }

      if (this._isInputElement()) {
        if (!this._isInputTypeSupported()) {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The input type \"".concat(this.domElement.type, "\" is not supported by autoNumeric"));
        }

        this.isInputElement = true;
      } else {
        this.isInputElement = false;
        this.isContentEditable = this.domElement.hasAttribute('contenteditable') && this.domElement.getAttribute('contenteditable') === 'true';
      }
    }
    

  }, {
    key: "_formatDefaultValueOnPageLoad",
    value: function _formatDefaultValueOnPageLoad() {
      var forcedInitialValue = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
      var setValue = true;
      var currentValue;

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(forcedInitialValue)) {
        currentValue = forcedInitialValue;
      } else {
        
        currentValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement).trim(); 

        this.domElement.setAttribute('value', currentValue);
      }

      if (this.isInputElement || this.isContentEditable) {
        
        var unLocalizedCurrentValue = this.constructor._toNumericValue(currentValue, this.settings); 


        if (!this.domElement.hasAttribute('value') || this.domElement.getAttribute('value') === '') {
          
          if (!isNaN(Number(unLocalizedCurrentValue)) && Infinity !== unLocalizedCurrentValue) {
            this.set(unLocalizedCurrentValue);
            setValue = false;
          } else {
            
            _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The value [".concat(currentValue, "] used in the input is not a valid value autoNumeric can work with."));
          }
        } else {
          
          if (this.settings.defaultValueOverride !== null && this.settings.defaultValueOverride.toString() !== currentValue || this.settings.defaultValueOverride === null && currentValue !== '' && currentValue !== this.domElement.getAttribute('value') || currentValue !== '' && this.domElement.getAttribute('type') === 'hidden' && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(unLocalizedCurrentValue)) {
            if (this.settings.saveValueToSessionStorage && (this.settings.decimalPlacesShownOnFocus !== null || this.settings.divisorWhenUnfocused)) {
              this._setRawValue(this._getValueFromPersistentStorage());
            } 


            if (!this.settings.saveValueToSessionStorage) {
              var toStrip = this.constructor._removeBrackets(currentValue, this.settings);

              if ((this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.suffix || this.settings.negativePositiveSignPlacement !== AutoNumeric.options.negativePositiveSignPlacement.prefix && this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix) && this.settings.negativeSignCharacter !== '' && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(currentValue, this.settings.negativeSignCharacter)) {
                this._setRawValue("-".concat(this.constructor._stripAllNonNumberCharacters(toStrip, this.settings, true, this.isFocused)));
              } else {
                this._setRawValue(this.constructor._stripAllNonNumberCharacters(toStrip, this.settings, true, this.isFocused));
              }
            }

            setValue = false;
          }
        }

        if (currentValue === '') {
          switch (this.settings.emptyInputBehavior) {
            case AutoNumeric.options.emptyInputBehavior.focus:
            case AutoNumeric.options.emptyInputBehavior["null"]:
            case AutoNumeric.options.emptyInputBehavior.press:
              break;

            case AutoNumeric.options.emptyInputBehavior.always:
              this._setElementValue(this.settings.currencySymbol);

              break;

            case AutoNumeric.options.emptyInputBehavior.min:
              this.set(this.settings.minimumValue);
              break;

            case AutoNumeric.options.emptyInputBehavior.max:
              this.set(this.settings.maximumValue);
              break;

            case AutoNumeric.options.emptyInputBehavior.zero:
              this.set('0');
              break;
            

            default:
              this.set(this.settings.emptyInputBehavior);
          }
        } else if (setValue && currentValue === this.domElement.getAttribute('value')) {
          this.set(currentValue);
        }
      } else if (this.settings.defaultValueOverride === null || this.settings.defaultValueOverride === currentValue) {
        this.set(currentValue);
      }
    }
    

  }, {
    key: "_calculateVMinAndVMaxIntegerSizes",

    
    value: function _calculateVMinAndVMaxIntegerSizes() {
      var _this$settings$maximu = this.settings.maximumValue.toString().split('.'),
          _this$settings$maximu2 = _slicedToArray(_this$settings$maximu, 1),
          maximumValueIntegerPart = _this$settings$maximu2[0];

      var _ref = !this.settings.minimumValue && this.settings.minimumValue !== 0 ? [] : this.settings.minimumValue.toString().split('.'),
          _ref2 = _slicedToArray(_ref, 1),
          minimumValueIntegerPart = _ref2[0];

      maximumValueIntegerPart = maximumValueIntegerPart.replace(this.settings.negativeSignCharacter, '');
      minimumValueIntegerPart = minimumValueIntegerPart.replace(this.settings.negativeSignCharacter, '');
      this.settings.mIntPos = Math.max(maximumValueIntegerPart.length, 1);
      this.settings.mIntNeg = Math.max(minimumValueIntegerPart.length, 1);
    }
    

  }, {
    key: "_calculateValuesToStringsKeys",
    value: function _calculateValuesToStringsKeys() {
      if (this.settings.valuesToStrings) {
        this.valuesToStringsKeys = Object.keys(this.settings.valuesToStrings);
      } else {
        this.valuesToStringsKeys = [];
      }
    }
    

  }, {
    key: "_transformOptionsValuesToDefaultTypes",

    
    value: function _transformOptionsValuesToDefaultTypes() {
      for (var key in this.settings) {
        if (Object.prototype.hasOwnProperty.call(this.settings, key)) {
          var value = this.settings[key]; 

          if (value === 'true' || value === 'false') {
            this.settings[key] = value === 'true';
          } 
          


          if (typeof value === 'number') {
            this.settings[key] = value.toString();
          }
        }
      }
    }
    

  }, {
    key: "_setSettings",

    
    value: function _setSettings(options) {
      var update = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

      
      if (update || !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options)) {
        this.constructor._convertOldOptionsToNewOnes(options);
      }

      if (update) {
        
        
        var decimalPlacesRawValueInOptions = ('decimalPlacesRawValue' in options);

        if (decimalPlacesRawValueInOptions) {
          this.settings.originalDecimalPlacesRawValue = options.decimalPlacesRawValue;
        }

        var decimalPlacesInOptions = ('decimalPlaces' in options);

        if (decimalPlacesInOptions) {
          this.settings.originalDecimalPlaces = options.decimalPlaces;
        } 


        this.constructor._calculateDecimalPlacesOnUpdate(options, this.settings); 


        this._mergeSettings(options); 

      } else {
        
        this.settings = {}; 

        this._mergeSettings(this.constructor.getDefaultConfig(), this.domElement.dataset, options, {
          rawValue: this.defaultRawValue
        });

        this.caretFix = false;
        this.throwInput = true; 

        this.allowedTagList = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].allowedTagList;
        this.runOnce = false;
        this.hoveredWithAlt = false; 
      } 


      this._transformOptionsValuesToDefaultTypes(); 


      this._runCallbacksFoundInTheSettingsObject(); 


      this.constructor._correctNegativePositiveSignPlacementOption(this.settings); 
      


      this.constructor._correctCaretPositionOnFocusAndSelectOnFocusOptions(this.settings); 


      this.constructor._setNegativePositiveSignPermissions(this.settings); 


      if (!update) {
        
        if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options) || !options.decimalPlaces) {
          this.settings.originalDecimalPlaces = null;
        } else {
          this.settings.originalDecimalPlaces = options.decimalPlaces;
        } 


        this.settings.originalDecimalPlacesRawValue = this.settings.decimalPlacesRawValue; 

        this.constructor._calculateDecimalPlacesOnInit(this.settings);
      } 


      this._calculateVMinAndVMaxIntegerSizes();

      this._setTrailingNegativeSignInfo();

      this.regex = {}; 

      this.constructor._cachesUsualRegularExpressions(this.settings, this.regex);

      this.constructor._setBrackets(this.settings);

      this._calculateValuesToStringsKeys(); 


      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isEmptyObj(this.settings)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError('Unable to set the settings, those are invalid ; an empty object was given.');
      }

      this.constructor.validate(this.settings, false, options); 

      this._keepAnOriginalSettingsCopy();
    }
    

  }, {
    key: "_preparePastedText",

    
    value: function _preparePastedText(text) {
      return this.constructor._stripAllNonNumberCharacters(text, this.settings, true, this.isFocused);
    }
    

  }, {
    key: "_updateInternalProperties",

    
    value: function _updateInternalProperties() {
      this.selection = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementSelection(this.domElement);
      this.processed = false;
    }
    

  }, {
    key: "_updateEventKeyInfo",
    value: function _updateEventKeyInfo(e) {
      this.eventKey = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].character(e);
    }
    

  }, {
    key: "_saveCancellableValue",
    value: function _saveCancellableValue() {
      this.savedCancellableValue = this.rawValue;
    }
    

  }, {
    key: "_setSelection",
    value: function _setSelection(start, end) {
      
      start = Math.max(start, 0);
      end = Math.min(end, _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement).length);
      this.selection = {
        start: start,
        end: end,
        length: end - start
      };
      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementSelection(this.domElement, start, end);
    }
    

  }, {
    key: "_setCaretPosition",
    value: function _setCaretPosition(position) {
      this._setSelection(position, position);
    }
    

  }, {
    key: "_getLeftAndRightPartAroundTheSelection",
    value: function _getLeftAndRightPartAroundTheSelection() {
      var value = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement);
      var left = value.substring(0, this.selection.start);
      var right = value.substring(this.selection.end, value.length);
      return [left, right];
    }
    

  }, {
    key: "_getUnformattedLeftAndRightPartAroundTheSelection",
    value: function _getUnformattedLeftAndRightPartAroundTheSelection() {
      var _this$_getLeftAndRigh = this._getLeftAndRightPartAroundTheSelection(),
          _this$_getLeftAndRigh2 = _slicedToArray(_this$_getLeftAndRigh, 2),
          left = _this$_getLeftAndRigh2[0],
          right = _this$_getLeftAndRigh2[1];

      if (left === '' && right === '') {
        return ['', ''];
      } 


      var stripZeros = true;

      if ((this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Hyphen || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Minus) && Number(left) === 0) {
        stripZeros = false;
      } 


      if (this.isTrailingNegative && (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(right, this.settings.negativeSignCharacter) && 
      !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(left, this.settings.negativeSignCharacter) || right === '' && 
      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(left, this.settings.negativeSignCharacter, true))) {
        left = left.replace(this.settings.negativeSignCharacter, '');
        right = right.replace(this.settings.negativeSignCharacter, ''); 

        left = left.replace('-', '');
        right = right.replace('-', ''); 

        left = "-".concat(left);
      }

      left = AutoNumeric._stripAllNonNumberCharactersExceptCustomDecimalChar(left, this.settings, stripZeros, this.isFocused);
      right = AutoNumeric._stripAllNonNumberCharactersExceptCustomDecimalChar(right, this.settings, false, this.isFocused);
      return [left, right];
    }
    

  }, {
    key: "_normalizeParts",
    value: function _normalizeParts(left, right) {
      
      
      var stripZeros = true;

      if ((this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Hyphen || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Minus) && Number(left) === 0) {
        stripZeros = false;
      }

      if (this.isTrailingNegative && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(right, this.settings.negativeSignCharacter) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(left, this.settings.negativeSignCharacter)) {
        
        left = "-".concat(left);
        right = right.replace(this.settings.negativeSignCharacter, '');
      }

      left = AutoNumeric._stripAllNonNumberCharactersExceptCustomDecimalChar(left, this.settings, stripZeros, this.isFocused);
      right = AutoNumeric._stripAllNonNumberCharactersExceptCustomDecimalChar(right, this.settings, false, this.isFocused); 

      if (this.settings.leadingZero === AutoNumeric.options.leadingZero.deny && (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.num0 || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.numpad0) && Number(left) === 0 && 
      !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(left, this.settings.decimalCharacter) && right !== '') {
        left = left.substring(0, left.length - 1);
      } 


      var newValue = left + right;

      if (this.settings.decimalCharacter) {
        var m = newValue.match(new RegExp("^".concat(this.regex.aNegRegAutoStrip, "\\").concat(this.settings.decimalCharacter)));

        if (m) {
          left = left.replace(m[1], m[1] + '0');
          newValue = left + right;
        }
      }

      return [left, right, newValue];
    }
    

  }, {
    key: "_setValueParts",
    value: function _setValueParts(left, right) {
      var isPaste = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;

      var _this$_normalizeParts = this._normalizeParts(left, right),
          _this$_normalizeParts2 = _slicedToArray(_this$_normalizeParts, 3),
          normalizedLeft = _this$_normalizeParts2[0],
          normalizedRight = _this$_normalizeParts2[1],
          normalizedNewValue = _this$_normalizeParts2[2];

      var _AutoNumeric$_checkIf = AutoNumeric._checkIfInRangeWithOverrideOption(normalizedNewValue, this.settings),
          _AutoNumeric$_checkIf2 = _slicedToArray(_AutoNumeric$_checkIf, 2),
          minTest = _AutoNumeric$_checkIf2[0],
          maxTest = _AutoNumeric$_checkIf2[1];

      if (minTest && maxTest) {
        
        var roundedRawValue = AutoNumeric._truncateDecimalPlaces(normalizedNewValue, this.settings, isPaste, this.settings.decimalPlacesRawValue);

        var testValue = roundedRawValue.replace(this.settings.decimalCharacter, '.');

        if (testValue === '' || testValue === this.settings.negativeSignCharacter) {
          var valueToSetOnEmpty;

          switch (this.settings.emptyInputBehavior) {
            case AutoNumeric.options.emptyInputBehavior.focus:
            case AutoNumeric.options.emptyInputBehavior.press:
            case AutoNumeric.options.emptyInputBehavior.always:
              valueToSetOnEmpty = '';
              break;

            case AutoNumeric.options.emptyInputBehavior.min:
              valueToSetOnEmpty = this.settings.minimumValue;
              break;

            case AutoNumeric.options.emptyInputBehavior.max:
              valueToSetOnEmpty = this.settings.maximumValue;
              break;

            case AutoNumeric.options.emptyInputBehavior.zero:
              valueToSetOnEmpty = '0';
              break;

            case AutoNumeric.options.emptyInputBehavior["null"]:
              valueToSetOnEmpty = null;
              break;
            

            default:
              valueToSetOnEmpty = this.settings.emptyInputBehavior;
          }

          this._setRawValue(valueToSetOnEmpty);
        } else {
          this._setRawValue(this._trimLeadingAndTrailingZeros(testValue));
        } 


        var roundedValueToShow = AutoNumeric._truncateDecimalPlaces(normalizedNewValue, this.settings, isPaste, this.settings.decimalPlacesShownOnFocus);

        var position = normalizedLeft.length;

        if (position > roundedValueToShow.length) {
          position = roundedValueToShow.length;
        } 


        if (position === 1 && normalizedLeft === '0' && this.settings.leadingZero === AutoNumeric.options.leadingZero.deny) {
          
          if (normalizedRight === '' || normalizedLeft === '0' && normalizedRight !== '') {
            position = 1;
          } else {
            position = 0;
          }
        }

        this._setElementValue(roundedValueToShow, false);

        this._setCaretPosition(position);

        return true;
      }

      this._triggerRangeEvents(minTest, maxTest);

      return false;
    }
    

  }, {
    key: "_getSignPosition",
    value: function _getSignPosition() {
      var result;

      if (this.settings.currencySymbol) {
        var currencySymbolLen = this.settings.currencySymbol.length;
        var value = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement);

        if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.prefix) {
          var hasNeg = this.settings.negativeSignCharacter && value && value.charAt(0) === this.settings.negativeSignCharacter;

          if (hasNeg) {
            result = [1, currencySymbolLen + 1];
          } else {
            result = [0, currencySymbolLen];
          }
        } else {
          var valueLen = value.length;
          result = [valueLen - currencySymbolLen, valueLen];
        }
      } else {
        result = [1000, -1];
      }

      return result;
    }
    

  }, {
    key: "_expandSelectionOnSign",
    value: function _expandSelectionOnSign() {
      var _this$_getSignPositio = this._getSignPosition(),
          _this$_getSignPositio2 = _slicedToArray(_this$_getSignPositio, 2),
          signPosition = _this$_getSignPositio2[0],
          currencySymbolPosition = _this$_getSignPositio2[1];

      var selection = this.selection; 

      if (selection.start < currencySymbolPosition && selection.end > signPosition) {
        
        if ((selection.start < signPosition || selection.end > currencySymbolPosition) && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement).substring(Math.max(selection.start, signPosition), Math.min(selection.end, currencySymbolPosition)).match(/^\s*$/)) {
          if (selection.start < signPosition) {
            this._setSelection(selection.start, signPosition);
          } else {
            this._setSelection(currencySymbolPosition, selection.end);
          }
        } else {
          
          this._setSelection(Math.min(selection.start, signPosition), Math.max(selection.end, currencySymbolPosition));
        }
      }
    }
    

  }, {
    key: "_checkPaste",
    value: function _checkPaste() {
      
      if (this.formatted) {
        return;
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(this.valuePartsBeforePaste)) {
        var oldParts = this.valuePartsBeforePaste;

        var _this$_getLeftAndRigh3 = this._getLeftAndRightPartAroundTheSelection(),
            _this$_getLeftAndRigh4 = _slicedToArray(_this$_getLeftAndRigh3, 2),
            left = _this$_getLeftAndRigh4[0],
            right = _this$_getLeftAndRigh4[1]; 


        delete this.valuePartsBeforePaste;

        var modifiedLeftPart = left.substr(0, oldParts[0].length) + AutoNumeric._stripAllNonNumberCharactersExceptCustomDecimalChar(left.substr(oldParts[0].length), this.settings, true, this.isFocused);

        if (!this._setValueParts(modifiedLeftPart, right, true)) {
          this._setElementValue(oldParts.join(''), false);

          this._setCaretPosition(oldParts[0].length);
        }
      }
    }
    

  }, {
    key: "_processNonPrintableKeysAndShortcuts",

    
    value: function _processNonPrintableKeysAndShortcuts(e) {
      
      if ((e.ctrlKey || e.metaKey) && e.type === 'keyup' && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(this.valuePartsBeforePaste) || e.shiftKey && this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Insert) {
        
        this._checkPaste();

        return false;
      } 


      if (this.constructor._shouldSkipEventKey(this.eventKey)) {
        return true;
      } 


      if ((e.ctrlKey || e.metaKey) && this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.a) {
        if (this.settings.selectNumberOnly) {
          
          e.preventDefault(); 

          this.selectNumber();
        }

        return true;
      } 


      if ((e.ctrlKey || e.metaKey) && (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.c || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.v || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.x)) {
        if (e.type === 'keydown') {
          this._expandSelectionOnSign();
        } 


        if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.v || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Insert) {
          if (e.type === 'keydown' || e.type === 'keypress') {
            if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(this.valuePartsBeforePaste)) {
              this.valuePartsBeforePaste = this._getLeftAndRightPartAroundTheSelection();
            }
          } else {
            this._checkPaste();
          }
        }

        return e.type === 'keydown' || e.type === 'keypress' || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.c;
      } 


      if (e.ctrlKey || e.metaKey) {
        return !(this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Z || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.z);
      } 
      


      if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.LeftArrow || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.RightArrow) {
        if (e.type === 'keydown' && !e.shiftKey) {
          var value = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement);

          if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.LeftArrow && (value.charAt(this.selection.start - 2) === this.settings.digitGroupSeparator || value.charAt(this.selection.start - 2) === this.settings.decimalCharacter)) {
            this._setCaretPosition(this.selection.start - 1);
          } else if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.RightArrow && (value.charAt(this.selection.start + 1) === this.settings.digitGroupSeparator || value.charAt(this.selection.start + 1) === this.settings.decimalCharacter)) {
            this._setCaretPosition(this.selection.start + 1);
          }
        }

        return true;
      }

      return _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(this.eventKey, _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName._directionKeys);
    }
    

  }, {
    key: "_processCharacterDeletionIfTrailingNegativeSign",
    value: function _processCharacterDeletionIfTrailingNegativeSign(_ref3) {
      var _ref4 = _slicedToArray(_ref3, 2),
          left = _ref4[0],
          right = _ref4[1];

      var value = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement);
      var isValNegative = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(value, this.settings.negativeSignCharacter);

      if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.prefix && this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.suffix) {
        if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Backspace) {
          this.caretFix = this.selection.start >= value.indexOf(this.settings.suffixText) && this.settings.suffixText !== '';

          if (value.charAt(this.selection.start - 1) === '-') {
            left = left.substring(1);
          } else if (this.selection.start <= value.length - this.settings.suffixText.length) {
            left = left.substring(0, left.length - 1);
          }
        } else {
          this.caretFix = this.selection.start >= value.indexOf(this.settings.suffixText) && this.settings.suffixText !== '';

          if (this.selection.start >= value.indexOf(this.settings.currencySymbol) + this.settings.currencySymbol.length) {
            right = right.substring(1, right.length);
          }

          if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(left, this.settings.negativeSignCharacter) && value.charAt(this.selection.start) === '-') {
            left = left.substring(1);
          }
        }
      }

      if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix) {
        switch (this.settings.negativePositiveSignPlacement) {
          case AutoNumeric.options.negativePositiveSignPlacement.left:
            this.caretFix = this.selection.start >= value.indexOf(this.settings.negativeSignCharacter) + this.settings.negativeSignCharacter.length;

            if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Backspace) {
              if (this.selection.start === value.indexOf(this.settings.negativeSignCharacter) + this.settings.negativeSignCharacter.length && isValNegative) {
                left = left.substring(1);
              } else if (left !== '-' && (this.selection.start <= value.indexOf(this.settings.negativeSignCharacter) || !isValNegative)) {
                left = left.substring(0, left.length - 1);
              }
            } else {
              if (left[0] === '-') {
                right = right.substring(1);
              }

              if (this.selection.start === value.indexOf(this.settings.negativeSignCharacter) && isValNegative) {
                left = left.substring(1);
              }
            }

            break;

          case AutoNumeric.options.negativePositiveSignPlacement.right:
            this.caretFix = this.selection.start >= value.indexOf(this.settings.negativeSignCharacter) + this.settings.negativeSignCharacter.length;

            if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Backspace) {
              if (this.selection.start === value.indexOf(this.settings.negativeSignCharacter) + this.settings.negativeSignCharacter.length) {
                left = left.substring(1);
              } else if (left !== '-' && this.selection.start <= value.indexOf(this.settings.negativeSignCharacter) - this.settings.currencySymbol.length) {
                left = left.substring(0, left.length - 1);
              } else if (left !== '' && !isValNegative) {
                left = left.substring(0, left.length - 1);
              }
            } else {
              this.caretFix = this.selection.start >= value.indexOf(this.settings.currencySymbol) && this.settings.currencySymbol !== '';

              if (this.selection.start === value.indexOf(this.settings.negativeSignCharacter)) {
                left = left.substring(1);
              }

              right = right.substring(1);
            }

            break;
        }
      }

      return [left, right];
    }
    

  }, {
    key: "_processCharacterDeletion",
    value: function _processCharacterDeletion() {
      var left;
      var right;

      if (!this.selection.length) {
        var _this$_getUnformatted = this._getUnformattedLeftAndRightPartAroundTheSelection();

        var _this$_getUnformatted2 = _slicedToArray(_this$_getUnformatted, 2);

        left = _this$_getUnformatted2[0];
        right = _this$_getUnformatted2[1];

        if (left === '' && right === '') {
          this.throwInput = false;
        }

        if (this.isTrailingNegative && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement), this.settings.negativeSignCharacter)) {
          var _this$_processCharact = this._processCharacterDeletionIfTrailingNegativeSign([left, right]);

          var _this$_processCharact2 = _slicedToArray(_this$_processCharact, 2);

          left = _this$_processCharact2[0];
          right = _this$_processCharact2[1];
        } else {
          if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Backspace) {
            left = left.substring(0, left.length - 1);
          } else {
            right = right.substring(1, right.length);
          }
        }
      } else {
        this._expandSelectionOnSign();

        var _this$_getUnformatted3 = this._getUnformattedLeftAndRightPartAroundTheSelection();

        var _this$_getUnformatted4 = _slicedToArray(_this$_getUnformatted3, 2);

        left = _this$_getUnformatted4[0];
        right = _this$_getUnformatted4[1];
      }

      if (!this.constructor._isWithinRangeWithOverrideOption("".concat(left).concat(right), this.settings)) {
        
        return false;
      }

      this._setValueParts(left, right);

      return true;
    }
    

  }, {
    key: "_isDecimalCharacterInsertionAllowed",
    value: function _isDecimalCharacterInsertionAllowed() {
      return String(this.settings.decimalPlacesShownOnFocus) !== String(AutoNumeric.options.decimalPlacesShownOnFocus.none) && String(this.settings.decimalPlaces) !== String(AutoNumeric.options.decimalPlaces.none);
    }
    

  }, {
    key: "_processCharacterInsertion",
    value: function _processCharacterInsertion() {
      var _this$_getUnformatted5 = this._getUnformattedLeftAndRightPartAroundTheSelection(),
          _this$_getUnformatted6 = _slicedToArray(_this$_getUnformatted5, 2),
          left = _this$_getUnformatted6[0],
          right = _this$_getUnformatted6[1];

      if (this.eventKey !== _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.AndroidDefault) {
        this.throwInput = true;
      } 
      


      if (this.eventKey === this.settings.decimalCharacter || this.settings.decimalCharacterAlternative && this.eventKey === this.settings.decimalCharacterAlternative) {
        if (!this._isDecimalCharacterInsertionAllowed() || !this.settings.decimalCharacter) {
          return false;
        }

        if (this.settings.alwaysAllowDecimalCharacter) {
          
          left = left.replace(this.settings.decimalCharacter, '');
          right = right.replace(this.settings.decimalCharacter, '');
        } else {
          
          if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(left, this.settings.decimalCharacter)) {
            return true;
          } 


          if (right.indexOf(this.settings.decimalCharacter) > 0) {
            return true;
          } 


          if (right.indexOf(this.settings.decimalCharacter) === 0) {
            right = right.substr(1);
          }
        } 


        if (this.settings.negativeSignCharacter && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(right, this.settings.negativeSignCharacter)) {
          
          left = "".concat(this.settings.negativeSignCharacter).concat(left);
          right = right.replace(this.settings.negativeSignCharacter, '');
        }

        this._setValueParts(left + this.settings.decimalCharacter, right);

        return true;
      } 


      if ((this.eventKey === '-' || this.eventKey === '+') && this.settings.isNegativeSignAllowed) {
        
        if (left === '' && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(right, '-')) {
          
          right = right.replace('-', '');
        } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegativeStrict(left, '-')) {
          
          
          left = left.replace('-', ''); 
        } else {
          
          left = "".concat(this.settings.negativeSignCharacter).concat(left);
        }

        this._setValueParts(left, right);

        return true;
      }

      var eventNumber = Number(this.eventKey);

      if (eventNumber >= 0 && eventNumber <= 9) {
        
        if (this.settings.isNegativeSignAllowed && left === '' && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(right, '-')) {
          
          left = '-';
          right = right.substring(1, right.length);
        }

        if (this.settings.maximumValue <= 0 && this.settings.minimumValue < this.settings.maximumValue && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement), this.settings.negativeSignCharacter) && this.eventKey !== '0') {
          left = "-".concat(left);
        }

        this._setValueParts("".concat(left).concat(this.eventKey), right);

        return true;
      } 


      this.throwInput = false;
      return false;
    }
    

  }, {
    key: "_formatValue",
    value: function _formatValue(e) {
      
      var elementValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(this.domElement);

      var _this$_getUnformatted7 = this._getUnformattedLeftAndRightPartAroundTheSelection(),
          _this$_getUnformatted8 = _slicedToArray(_this$_getUnformatted7, 1),
          left = _this$_getUnformatted8[0]; 


      if ((this.settings.digitGroupSeparator === '' || this.settings.digitGroupSeparator !== '' && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(elementValue, this.settings.digitGroupSeparator)) && (this.settings.currencySymbol === '' || this.settings.currencySymbol !== '' && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(elementValue, this.settings.currencySymbol))) {
        var _elementValue$split = elementValue.split(this.settings.decimalCharacter),
            _elementValue$split2 = _slicedToArray(_elementValue$split, 1),
            subParts = _elementValue$split2[0];

        var negativeSign = '';

        if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(subParts, this.settings.negativeSignCharacter)) {
          negativeSign = this.settings.negativeSignCharacter;
          subParts = subParts.replace(this.settings.negativeSignCharacter, '');
          left = left.replace('-', ''); 
        } 


        if (negativeSign === '' && subParts.length > this.settings.mIntPos && left.charAt(0) === '0') {
          left = left.slice(1);
        } 


        if (negativeSign === this.settings.negativeSignCharacter && subParts.length > this.settings.mIntNeg && left.charAt(0) === '0') {
          left = left.slice(1);
        }

        if (!this.isTrailingNegative) {
          
          left = "".concat(negativeSign).concat(left);
        }
      }

      var value = this.constructor._addGroupSeparators(elementValue, this.settings, this.isFocused, this.rawValue);

      var position = value.length;

      if (value) {
        
        var leftAr = left.split(''); 

        if ((this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.suffix || this.settings.negativePositiveSignPlacement !== AutoNumeric.options.negativePositiveSignPlacement.prefix && this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix) && leftAr[0] === this.settings.negativeSignCharacter && !this.settings.isNegativeSignAllowed) {
          leftAr.shift(); 

          if ((this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Backspace || this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Delete) && this.caretFix) {
            if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix && this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.left || this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.prefix && this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.suffix) {
              leftAr.push(this.settings.negativeSignCharacter);
              this.caretFix = e.type === 'keydown';
            }

            if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix && this.settings.negativePositiveSignPlacement === AutoNumeric.options.negativePositiveSignPlacement.right) {
              var signParts = this.settings.currencySymbol.split('');
              var escapeChr = ['\\', '^', '$', '.', '|', '?', '*', '+', '(', ')', '['];
              var escapedParts = [];
              signParts.forEach(function (i, miniParts) {
                miniParts = signParts[i];

                if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(miniParts, escapeChr)) {
                  escapedParts.push('\\' + miniParts);
                } else {
                  escapedParts.push(miniParts);
                }
              });

              if (this.eventKey === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Backspace && this.settings.negativeSignCharacter === '-') {
                escapedParts.push('-');
              } 


              leftAr.push(escapedParts.join(''));
              this.caretFix = e.type === 'keydown';
            }
          }
        }

        for (var i = 0; i < leftAr.length; i++) {
          if (!leftAr[i].match('\\d')) {
            leftAr[i] = '\\' + leftAr[i];
          }
        }

        var leftReg;

        if (this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix) {
          leftReg = new RegExp("^.*?".concat(leftAr.join('.*?')));
        } else {
          
          leftReg = new RegExp("^.*?".concat(this.settings.currencySymbol).concat(leftAr.join('.*?'))); 
        } 


        var newLeft = value.match(leftReg);

        if (newLeft) {
          position = newLeft[0].length; 

          if (this.settings.showPositiveSign) {
            if (position === 0 && newLeft.input.charAt(0) === this.settings.positiveSignCharacter) {
              position = newLeft.input.indexOf(this.settings.currencySymbol) === 1 ? this.settings.currencySymbol.length + 1 : 1;
            }

            if (position === 0 && newLeft.input.charAt(this.settings.currencySymbol.length) === this.settings.positiveSignCharacter) {
              position = this.settings.currencySymbol.length + 1;
            }
          } 


          if ((position === 0 && value.charAt(0) !== this.settings.negativeSignCharacter || position === 1 && value.charAt(0) === this.settings.negativeSignCharacter) && this.settings.currencySymbol && this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.prefix) {
            
            
            position = this.settings.currencySymbol.length + (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegativeStrict(value, this.settings.negativeSignCharacter) ? 1 : 0);
          }
        } else {
          if (this.settings.currencySymbol && this.settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix) {
            
            
            position -= this.settings.currencySymbol.length;
          }

          if (this.settings.suffixText) {
            
            
            position -= this.settings.suffixText.length;
          }
        }
      } 


      if (value !== elementValue) {
        this._setElementValue(value, false);

        this._setCaretPosition(position);
      }

      this.formatted = true; 
    }
    

  }], [{
    key: "version",
    value: function version() {
      return '4.6.0';
    }
    

  }, {
    key: "_setArgumentsValues",
    value: function _setArgumentsValues(arg1, arg2, arg3) {
      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(arg1)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError('At least one valid parameter is needed in order to initialize an AutoNumeric object');
      } 
      


      var isArg1Element = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isElement(arg1);
      var isArg1String = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(arg1);
      var isArg2Object = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isObject(arg2);
      var isArg2Array = Array.isArray(arg2) && arg2.length > 0;
      var isArg2Number = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumberOrArabic(arg2) || arg2 === '';

      var isArg2PreDefinedOptionName = this._isPreDefinedOptionValid(arg2);

      var isArg2Null = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(arg2);
      var isArg2EmptyString = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isEmptyString(arg2);
      var isArg3Object = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isObject(arg3);
      var isArg3Array = Array.isArray(arg3) && arg3.length > 0;
      var isArg3Null = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(arg3);

      var isArg3PreDefinedOptionName = this._isPreDefinedOptionValid(arg3); 


      var domElement;
      var userOptions;
      var initialValue; 

      if (isArg1Element && isArg2Null && isArg3Null) {
        
        domElement = arg1;
        initialValue = null;
        userOptions = null;
      } else if (isArg1Element && isArg2Number && isArg3Null) {
        
        
        domElement = arg1;
        initialValue = arg2;
        userOptions = null;
      } else if (isArg1Element && isArg2Object && isArg3Null) {
        
        domElement = arg1;
        initialValue = null;
        userOptions = arg2;
      } else if (isArg1Element && isArg2PreDefinedOptionName && isArg3Null) {
        
        domElement = arg1;
        initialValue = null;
        userOptions = this._getOptionObject(arg2);
      } else if (isArg1Element && isArg2Array && isArg3Null) {
        
        domElement = arg1;
        initialValue = null;
        userOptions = this.mergeOptions(arg2);
      } else if (isArg1Element && (isArg2Null || isArg2EmptyString) && isArg3Object) {
        
        domElement = arg1;
        initialValue = null;
        userOptions = arg3;
      } else if (isArg1Element && (isArg2Null || isArg2EmptyString) && isArg3Array) {
        
        domElement = arg1;
        initialValue = null;
        userOptions = this.mergeOptions(arg3);
      } else if (isArg1String && isArg2Null && isArg3Null) {
        
        domElement = document.querySelector(arg1);
        initialValue = null;
        userOptions = null;
      } else if (isArg1String && isArg2Object && isArg3Null) {
        
        domElement = document.querySelector(arg1);
        initialValue = null;
        userOptions = arg2;
      } else if (isArg1String && isArg2PreDefinedOptionName && isArg3Null) {
        
        domElement = document.querySelector(arg1);
        initialValue = null;
        userOptions = this._getOptionObject(arg2);
      } else if (isArg1String && isArg2Array && isArg3Null) {
        
        domElement = document.querySelector(arg1);
        initialValue = null;
        userOptions = this.mergeOptions(arg2);
      } else if (isArg1String && (isArg2Null || isArg2EmptyString) && isArg3Object) {
        
        domElement = document.querySelector(arg1);
        initialValue = null;
        userOptions = arg3;
      } else if (isArg1String && (isArg2Null || isArg2EmptyString) && isArg3Array) {
        
        domElement = document.querySelector(arg1);
        initialValue = null;
        userOptions = this.mergeOptions(arg3);
      } else if (isArg1String && isArg2Number && isArg3Null) {
        
        
        
        domElement = document.querySelector(arg1);
        initialValue = arg2;
        userOptions = null;
      } else if (isArg1String && isArg2Number && isArg3Object) {
        
        
        
        domElement = document.querySelector(arg1);
        initialValue = arg2;
        userOptions = arg3;
      } else if (isArg1String && isArg2Number && isArg3PreDefinedOptionName) {
        
        
        
        domElement = document.querySelector(arg1);
        initialValue = arg2;
        userOptions = this._getOptionObject(arg3);
      } else if (isArg1String && isArg2Number && isArg3Array) {
        
        
        
        domElement = document.querySelector(arg1);
        initialValue = arg2;
        userOptions = this.mergeOptions(arg3);
      } else if (isArg1Element && isArg2Number && isArg3Object) {
        
        
        
        domElement = arg1;
        initialValue = arg2;
        userOptions = arg3;
      } else if (isArg1Element && isArg2Number && isArg3PreDefinedOptionName) {
        
        
        
        domElement = arg1;
        initialValue = arg2;
        userOptions = this._getOptionObject(arg3);
      } else if (isArg1Element && isArg2Number && isArg3Array) {
        
        
        
        domElement = arg1;
        initialValue = arg2;
        userOptions = this.mergeOptions(arg3);
      } else {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The parameters given to the AutoNumeric object are not valid, '".concat(arg1, "', '").concat(arg2, "' and '").concat(arg3, "' given."));
      }

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(domElement)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The selector '".concat(arg1, "' did not select any valid DOM element. Please check on which element you called AutoNumeric."));
      }

      return {
        domElement: domElement,
        initialValue: initialValue,
        userOptions: userOptions
      };
    }
    

  }, {
    key: "mergeOptions",
    value: function mergeOptions(optionsArray) {
      var _this12 = this;

      
      var mergedOptions = {};
      optionsArray.forEach(function (optionObjectOrPredefinedOptionString) {
        _extends(mergedOptions, _this12._getOptionObject(optionObjectOrPredefinedOptionString));
      });
      return mergedOptions;
    }
    

  }, {
    key: "_isPreDefinedOptionValid",
    value: function _isPreDefinedOptionValid(preDefinedOptionName) {
      return Object.prototype.hasOwnProperty.call(AutoNumeric.predefinedOptions, preDefinedOptionName);
    }
    

  }, {
    key: "_getOptionObject",
    value: function _getOptionObject(optionObjectOrPredefinedName) {
      var options;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(optionObjectOrPredefinedName)) {
        options = AutoNumeric.getPredefinedOptions()[optionObjectOrPredefinedName];

        if (options === void 0 || options === null) {
          
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("The given pre-defined option [".concat(optionObjectOrPredefinedName, "] is not recognized by autoNumeric. Please check that pre-defined option name."), true);
        }
      } else {
        
        options = optionObjectOrPredefinedName;
      }

      return options;
    }
  }, {
    key: "_doesFormHandlerListExists",
    value: function _doesFormHandlerListExists() {
      var type = _typeof(window.aNFormHandlerMap);

      return type !== 'undefined' && type === 'object';
    }
    

  }, {
    key: "_createFormHandlerList",
    value: function _createFormHandlerList() {
      window.aNFormHandlerMap = new Map(); 
    }
  }, {
    key: "_checkValuesToStringsArray",
    value: function _checkValuesToStringsArray(key, stringsArray) {
      return _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(String(key), stringsArray);
    }
    

  }, {
    key: "_checkValuesToStringsSettings",
    value: function _checkValuesToStringsSettings(key, settings) {
      return this._checkValuesToStringsArray(key, Object.keys(settings.valuesToStrings));
    }
    

  }, {
    key: "_checkStringsToValuesSettings",
    value: function _checkStringsToValuesSettings(value, settings) {
      return this._checkValuesToStringsArray(value, Object.values(settings.valuesToStrings));
    }
  }, {
    key: "_unformatAltHovered",
    value: function _unformatAltHovered(anElement) {
      anElement.hoveredWithAlt = true;
      anElement.unformat();
    }
    

  }, {
    key: "_reformatAltHovered",
    value: function _reformatAltHovered(anElement) {
      anElement.hoveredWithAlt = false;
      anElement.reformat();
    }
    

  }, {
    key: "_getChildANInputElement",
    value: function _getChildANInputElement(formNode) {
      var _this13 = this;

      
      var inputList = formNode.getElementsByTagName('input'); 

      var autoNumericInputs = [];
      var inputElements = Array.prototype.slice.call(inputList, 0);
      inputElements.forEach(function (input) {
        if (_this13.test(input)) {
          autoNumericInputs.push(input);
        }
      });
      return autoNumericInputs;
    } 

    

  }, {
    key: "test",
    value: function test(domElementOrSelector) {
      return this._isInGlobalList(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].domElement(domElementOrSelector));
    }
    

  }, {
    key: "_createWeakMap",
    value: function _createWeakMap(weakMapName) {
      window[weakMapName] = new WeakMap();
    }
    

  }, {
    key: "_createGlobalList",
    value: function _createGlobalList() {
      
      this.autoNumericGlobalListName = 'autoNumericGlobalList'; 
      

      this._createWeakMap(this.autoNumericGlobalListName);
    }
    

  }, {
    key: "_doesGlobalListExists",
    value: function _doesGlobalListExists() {
      var type = _typeof(window[this.autoNumericGlobalListName]);

      return type !== 'undefined' && type === 'object';
    }
    

  }, {
    key: "_addToGlobalList",
    value: function _addToGlobalList(autoNumericObject) {
      if (!this._doesGlobalListExists()) {
        this._createGlobalList();
      }

      var domElement = autoNumericObject.node(); 
      

      if (this._isInGlobalList(domElement)) {
        if (this._getFromGlobalList(domElement) === this) {
          
          return;
        } else {
          
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("A reference to the DOM element you just initialized already exists in the global AutoNumeric element list. Please make sure to not initialize the same DOM element multiple times.", autoNumericObject.getSettings().showWarnings);
        }
      }

      window[this.autoNumericGlobalListName].set(domElement, autoNumericObject);
    }
    

  }, {
    key: "_removeFromGlobalList",
    value: function _removeFromGlobalList(autoNumericObject) {
      
      if (this._doesGlobalListExists()) {
        window[this.autoNumericGlobalListName]["delete"](autoNumericObject.node());
      }
    }
    

  }, {
    key: "_getFromGlobalList",
    value: function _getFromGlobalList(domElement) {
      
      if (this._doesGlobalListExists()) {
        return window[this.autoNumericGlobalListName].get(domElement);
      }

      return null;
    }
    

  }, {
    key: "_isInGlobalList",
    value: function _isInGlobalList(domElement) {
      
      if (!this._doesGlobalListExists()) {
        return false;
      }

      return window[this.autoNumericGlobalListName].has(domElement);
    }
  }, {
    key: "validate",
    value: function validate(userOptions) {
      var shouldExtendDefaultOptions = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
      var originalOptions = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(userOptions) || !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isObject(userOptions)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The userOptions are invalid ; it should be a valid object, [".concat(userOptions, "] given."));
      }

      var isOriginalOptionAnObject = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isObject(originalOptions);

      if (!isOriginalOptionAnObject && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(originalOptions)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The 'originalOptions' parameter is invalid ; it should either be a valid option object or `null`, [".concat(userOptions, "] given."));
      } 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(userOptions)) {
        this._convertOldOptionsToNewOnes(userOptions);
      } 


      var options;

      if (shouldExtendDefaultOptions) {
        options = _extends({}, this.getDefaultConfig(), userOptions);
      } else {
        options = userOptions;
      } 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.showWarnings) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.showWarnings)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The debug option 'showWarnings' is invalid ; it should be either 'true' or 'false', [".concat(options.showWarnings, "] given."));
      } 


      var testPositiveInteger = /^[0-9]+$/;
      var testNumericalCharacters = /[0-9]+/; 

      var testFloatOrIntegerAndPossibleNegativeSign = /^-?[0-9]+(\.?[0-9]+)?$/;
      var testPositiveFloatOrInteger = /^[0-9]+(\.?[0-9]+)?$/; 

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.allowDecimalPadding) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.allowDecimalPadding) && options.allowDecimalPadding !== AutoNumeric.options.allowDecimalPadding.floats) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The decimal padding option 'allowDecimalPadding' is invalid ; it should either be `false`, `true` or `'floats'`, [".concat(options.allowDecimalPadding, "] given."));
      }

      if ((options.allowDecimalPadding === AutoNumeric.options.allowDecimalPadding.never || options.allowDecimalPadding === 'false') && (options.decimalPlaces !== AutoNumeric.options.decimalPlaces.none || options.decimalPlacesShownOnBlur !== AutoNumeric.options.decimalPlacesShownOnBlur.none || options.decimalPlacesShownOnFocus !== AutoNumeric.options.decimalPlacesShownOnFocus.none)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("Setting 'allowDecimalPadding' to [".concat(options.allowDecimalPadding, "] will override the current 'decimalPlaces*' settings [").concat(options.decimalPlaces, ", ").concat(options.decimalPlacesShownOnBlur, " and ").concat(options.decimalPlacesShownOnFocus, "]."), options.showWarnings);
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.alwaysAllowDecimalCharacter) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.alwaysAllowDecimalCharacter)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The option 'alwaysAllowDecimalCharacter' is invalid ; it should either be `true` or `false`, [".concat(options.alwaysAllowDecimalCharacter, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.caretPositionOnFocus) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.caretPositionOnFocus, [AutoNumeric.options.caretPositionOnFocus.start, AutoNumeric.options.caretPositionOnFocus.end, AutoNumeric.options.caretPositionOnFocus.decimalLeft, AutoNumeric.options.caretPositionOnFocus.decimalRight])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The display on empty string option 'caretPositionOnFocus' is invalid ; it should either be `null`, 'focus', 'press', 'always' or 'zero', [".concat(options.caretPositionOnFocus, "] given."));
      } 


      var optionsToUse;

      if (isOriginalOptionAnObject) {
        optionsToUse = originalOptions;
      } else {
        optionsToUse = this._correctCaretPositionOnFocusAndSelectOnFocusOptions(userOptions);
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(optionsToUse) && optionsToUse.caretPositionOnFocus !== AutoNumeric.options.caretPositionOnFocus.doNoForceCaretPosition && optionsToUse.selectOnFocus === AutoNumeric.options.selectOnFocus.select) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("The 'selectOnFocus' option is set to 'select', which is in conflict with the 'caretPositionOnFocus' which is set to '".concat(optionsToUse.caretPositionOnFocus, "'. As a result, if this has been called when instantiating an AutoNumeric object, the 'selectOnFocus' option is forced to 'doNotSelect'."), options.showWarnings);
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.digitGroupSeparator, [AutoNumeric.options.digitGroupSeparator.comma, AutoNumeric.options.digitGroupSeparator.dot, AutoNumeric.options.digitGroupSeparator.normalSpace, AutoNumeric.options.digitGroupSeparator.thinSpace, AutoNumeric.options.digitGroupSeparator.narrowNoBreakSpace, AutoNumeric.options.digitGroupSeparator.noBreakSpace, AutoNumeric.options.digitGroupSeparator.noSeparator, AutoNumeric.options.digitGroupSeparator.apostrophe, AutoNumeric.options.digitGroupSeparator.arabicThousandsSeparator, AutoNumeric.options.digitGroupSeparator.dotAbove, AutoNumeric.options.digitGroupSeparator.privateUseTwo])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The thousand separator character option 'digitGroupSeparator' is invalid ; it should be ',', '.', '\u066C', '\u02D9', \"'\", '\x92', ' ', '\u2009', '\u202F', '\xA0' or empty (''), [".concat(options.digitGroupSeparator, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.showOnlyNumbersOnFocus) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.showOnlyNumbersOnFocus)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The 'showOnlyNumbersOnFocus' option is invalid ; it should be either 'true' or 'false', [".concat(options.showOnlyNumbersOnFocus, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.digitalGroupSpacing, [AutoNumeric.options.digitalGroupSpacing.two, AutoNumeric.options.digitalGroupSpacing.twoScaled, AutoNumeric.options.digitalGroupSpacing.three, AutoNumeric.options.digitalGroupSpacing.four]) && !(options.digitalGroupSpacing >= 2 && options.digitalGroupSpacing <= 4)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The grouping separator option for thousands 'digitalGroupSpacing' is invalid ; it should be '2', '2s', '3', or '4', [".concat(options.digitalGroupSpacing, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.decimalCharacter, [AutoNumeric.options.decimalCharacter.comma, AutoNumeric.options.decimalCharacter.dot, AutoNumeric.options.decimalCharacter.middleDot, AutoNumeric.options.decimalCharacter.arabicDecimalSeparator, AutoNumeric.options.decimalCharacter.decimalSeparatorKeySymbol])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The decimal separator character option 'decimalCharacter' is invalid ; it should be '.', ',', '\xB7', '\u2396' or '\u066B', [".concat(options.decimalCharacter, "] given."));
      } 


      if (options.decimalCharacter === options.digitGroupSeparator) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("autoNumeric will not function properly when the decimal character 'decimalCharacter' [".concat(options.decimalCharacter, "] and the thousand separator 'digitGroupSeparator' [").concat(options.digitGroupSeparator, "] are the same character."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.decimalCharacterAlternative) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(options.decimalCharacterAlternative)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The alternate decimal separator character option 'decimalCharacterAlternative' is invalid ; it should be a string, [".concat(options.decimalCharacterAlternative, "] given."));
      }

      if (options.currencySymbol !== '' && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(options.currencySymbol)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The currency symbol option 'currencySymbol' is invalid ; it should be a string, [".concat(options.currencySymbol, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.currencySymbolPlacement, [AutoNumeric.options.currencySymbolPlacement.prefix, AutoNumeric.options.currencySymbolPlacement.suffix])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The placement of the currency sign option 'currencySymbolPlacement' is invalid ; it should either be 'p' (prefix) or 's' (suffix), [".concat(options.currencySymbolPlacement, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.negativePositiveSignPlacement, [AutoNumeric.options.negativePositiveSignPlacement.prefix, AutoNumeric.options.negativePositiveSignPlacement.suffix, AutoNumeric.options.negativePositiveSignPlacement.left, AutoNumeric.options.negativePositiveSignPlacement.right, AutoNumeric.options.negativePositiveSignPlacement.none])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The placement of the negative sign option 'negativePositiveSignPlacement' is invalid ; it should either be 'p' (prefix), 's' (suffix), 'l' (left), 'r' (right) or 'null', [".concat(options.negativePositiveSignPlacement, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.showPositiveSign) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.showPositiveSign)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The show positive sign option 'showPositiveSign' is invalid ; it should be either 'true' or 'false', [".concat(options.showPositiveSign, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(options.suffixText) || options.suffixText !== '' && (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(options.suffixText, options.negativeSignCharacter) || testNumericalCharacters.test(options.suffixText))) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The additional suffix option 'suffixText' is invalid ; it should not contains the negative sign '".concat(options.negativeSignCharacter, "' nor any numerical characters, [").concat(options.suffixText, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(options.negativeSignCharacter) || options.negativeSignCharacter.length !== 1 || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(options.negativeSignCharacter) || testNumericalCharacters.test(options.negativeSignCharacter)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The negative sign character option 'negativeSignCharacter' is invalid ; it should be a single character, and cannot be any numerical characters, [".concat(options.negativeSignCharacter, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(options.positiveSignCharacter) || options.positiveSignCharacter.length !== 1 || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(options.positiveSignCharacter) || testNumericalCharacters.test(options.positiveSignCharacter)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The positive sign character option 'positiveSignCharacter' is invalid ; it should be a single character, and cannot be any numerical characters, [".concat(options.positiveSignCharacter, "] given.\nIf you want to hide the positive sign character, you need to set the `showPositiveSign` option to `true`."));
      }

      if (options.negativeSignCharacter === options.positiveSignCharacter) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The positive 'positiveSignCharacter' and negative 'negativeSignCharacter' sign characters cannot be identical ; [".concat(options.negativeSignCharacter, "] given."));
      }

      var _ref5 = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.negativeBracketsTypeOnBlur) ? ['', ''] : options.negativeBracketsTypeOnBlur.split(','),
          _ref6 = _slicedToArray(_ref5, 2),
          leftBracket = _ref6[0],
          rightBracket = _ref6[1];

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(options.digitGroupSeparator, options.negativeSignCharacter) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(options.decimalCharacter, options.negativeSignCharacter) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(options.decimalCharacterAlternative, options.negativeSignCharacter) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(leftBracket, options.negativeSignCharacter) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(rightBracket, options.negativeSignCharacter) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(options.suffixText, options.negativeSignCharacter)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The negative sign character option 'negativeSignCharacter' is invalid ; it should not be equal or a part of the digit separator, the decimal character, the decimal character alternative, the negative brackets or the suffix text, [".concat(options.negativeSignCharacter, "] given."));
      }

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(options.digitGroupSeparator, options.positiveSignCharacter) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(options.decimalCharacter, options.positiveSignCharacter) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(options.decimalCharacterAlternative, options.positiveSignCharacter) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(leftBracket, options.positiveSignCharacter) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(rightBracket, options.positiveSignCharacter) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(options.suffixText, options.positiveSignCharacter)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The positive sign character option 'positiveSignCharacter' is invalid ; it should not be equal or a part of the digit separator, the decimal character, the decimal character alternative, the negative brackets or the suffix text, [".concat(options.positiveSignCharacter, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.overrideMinMaxLimits) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.overrideMinMaxLimits, [AutoNumeric.options.overrideMinMaxLimits.ceiling, AutoNumeric.options.overrideMinMaxLimits.floor, AutoNumeric.options.overrideMinMaxLimits.ignore, AutoNumeric.options.overrideMinMaxLimits.invalid])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The override min & max limits option 'overrideMinMaxLimits' is invalid ; it should either be 'ceiling', 'floor', 'ignore' or 'invalid', [".concat(options.overrideMinMaxLimits, "] given."));
      }

      if (options.overrideMinMaxLimits !== AutoNumeric.options.overrideMinMaxLimits.invalid && options.overrideMinMaxLimits !== AutoNumeric.options.overrideMinMaxLimits.ignore && (options.minimumValue > 0 || options.maximumValue < 0)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("You've set a `minimumValue` or a `maximumValue` excluding the value `0`. AutoNumeric will force the users to always have a valid value in the input, hence preventing them to clear the field. If you want to allow for temporary invalid values (ie. out-of-range), you should use the 'invalid' option for the 'overrideMinMaxLimits' setting.");
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(options.maximumValue) || !testFloatOrIntegerAndPossibleNegativeSign.test(options.maximumValue)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The maximum possible value option 'maximumValue' is invalid ; it should be a string that represents a positive or negative number, [".concat(options.maximumValue, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(options.minimumValue) || !testFloatOrIntegerAndPossibleNegativeSign.test(options.minimumValue)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The minimum possible value option 'minimumValue' is invalid ; it should be a string that represents a positive or negative number, [".concat(options.minimumValue, "] given."));
      }

      if (parseFloat(options.minimumValue) > parseFloat(options.maximumValue)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The minimum possible value option is greater than the maximum possible value option ; 'minimumValue' [".concat(options.minimumValue, "] should be smaller than 'maximumValue' [").concat(options.maximumValue, "]."));
      }

      if (!(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInt(options.decimalPlaces) && options.decimalPlaces >= 0 || 
      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(options.decimalPlaces) && testPositiveInteger.test(options.decimalPlaces)) 
      ) {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The number of decimal places option 'decimalPlaces' is invalid ; it should be a positive integer, [".concat(options.decimalPlaces, "] given."));
        }

      if (!(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.decimalPlacesRawValue) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInt(options.decimalPlacesRawValue) && options.decimalPlacesRawValue >= 0 || 
      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(options.decimalPlacesRawValue) && testPositiveInteger.test(options.decimalPlacesRawValue)) 
      ) {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The number of decimal places for the raw value option 'decimalPlacesRawValue' is invalid ; it should be a positive integer or `null`, [".concat(options.decimalPlacesRawValue, "] given."));
        } 


      this._validateDecimalPlacesRawValue(options);

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.decimalPlacesShownOnFocus) && !testPositiveInteger.test(String(options.decimalPlacesShownOnFocus))) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The number of expanded decimal places option 'decimalPlacesShownOnFocus' is invalid ; it should be a positive integer or `null`, [".concat(options.decimalPlacesShownOnFocus, "] given."));
      } 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.decimalPlacesShownOnFocus) && Number(options.decimalPlaces) > Number(options.decimalPlacesShownOnFocus)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("The extended decimal places 'decimalPlacesShownOnFocus' [".concat(options.decimalPlacesShownOnFocus, "] should be greater than the 'decimalPlaces' [").concat(options.decimalPlaces, "] value. Currently, this will limit the ability of your user to manually change some of the decimal places. Do you really want to do that?"), options.showWarnings);
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.divisorWhenUnfocused) && !testPositiveFloatOrInteger.test(options.divisorWhenUnfocused) || options.divisorWhenUnfocused === 0 || options.divisorWhenUnfocused === '0' || options.divisorWhenUnfocused === 1 || options.divisorWhenUnfocused === '1') {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The divisor option 'divisorWhenUnfocused' is invalid ; it should be a positive number higher than one, preferably an integer, [".concat(options.divisorWhenUnfocused, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.decimalPlacesShownOnBlur) && !testPositiveInteger.test(options.decimalPlacesShownOnBlur)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The number of decimals shown when unfocused option 'decimalPlacesShownOnBlur' is invalid ; it should be a positive integer or `null`, [".concat(options.decimalPlacesShownOnBlur, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.symbolWhenUnfocused) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(options.symbolWhenUnfocused)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The symbol to show when unfocused option 'symbolWhenUnfocused' is invalid ; it should be a string, [".concat(options.symbolWhenUnfocused, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.saveValueToSessionStorage) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.saveValueToSessionStorage)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The save to session storage option 'saveValueToSessionStorage' is invalid ; it should be either 'true' or 'false', [".concat(options.saveValueToSessionStorage, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.onInvalidPaste, [AutoNumeric.options.onInvalidPaste.error, AutoNumeric.options.onInvalidPaste.ignore, AutoNumeric.options.onInvalidPaste.clamp, AutoNumeric.options.onInvalidPaste.truncate, AutoNumeric.options.onInvalidPaste.replace])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The paste behavior option 'onInvalidPaste' is invalid ; it should either be 'error', 'ignore', 'clamp', 'truncate' or 'replace' (cf. documentation), [".concat(options.onInvalidPaste, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.roundingMethod, [AutoNumeric.options.roundingMethod.halfUpSymmetric, AutoNumeric.options.roundingMethod.halfUpAsymmetric, AutoNumeric.options.roundingMethod.halfDownSymmetric, AutoNumeric.options.roundingMethod.halfDownAsymmetric, AutoNumeric.options.roundingMethod.halfEvenBankersRounding, AutoNumeric.options.roundingMethod.upRoundAwayFromZero, AutoNumeric.options.roundingMethod.downRoundTowardZero, AutoNumeric.options.roundingMethod.toCeilingTowardPositiveInfinity, AutoNumeric.options.roundingMethod.toFloorTowardNegativeInfinity, AutoNumeric.options.roundingMethod.toNearest05, AutoNumeric.options.roundingMethod.toNearest05Alt, AutoNumeric.options.roundingMethod.upToNext05, AutoNumeric.options.roundingMethod.downToNext05])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The rounding method option 'roundingMethod' is invalid ; it should either be 'S', 'A', 's', 'a', 'B', 'U', 'D', 'C', 'F', 'N05', 'CHF', 'U05' or 'D05' (cf. documentation), [".concat(options.roundingMethod, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.negativeBracketsTypeOnBlur) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.negativeBracketsTypeOnBlur, [AutoNumeric.options.negativeBracketsTypeOnBlur.parentheses, AutoNumeric.options.negativeBracketsTypeOnBlur.brackets, AutoNumeric.options.negativeBracketsTypeOnBlur.chevrons, AutoNumeric.options.negativeBracketsTypeOnBlur.curlyBraces, AutoNumeric.options.negativeBracketsTypeOnBlur.angleBrackets, AutoNumeric.options.negativeBracketsTypeOnBlur.japaneseQuotationMarks, AutoNumeric.options.negativeBracketsTypeOnBlur.halfBrackets, AutoNumeric.options.negativeBracketsTypeOnBlur.whiteSquareBrackets, AutoNumeric.options.negativeBracketsTypeOnBlur.quotationMarks, AutoNumeric.options.negativeBracketsTypeOnBlur.guillemets])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The brackets for negative values option 'negativeBracketsTypeOnBlur' is invalid ; it should either be '(,)', '[,]', '<,>', '{,}', '\u3008,\u3009', '\uFF62,\uFF63', '\u2E24,\u2E25', '\u27E6,\u27E7', '\u2039,\u203A' or '\xAB,\xBB', [".concat(options.negativeBracketsTypeOnBlur, "] given."));
      }

      if (!(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(options.emptyInputBehavior) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(options.emptyInputBehavior)) || !(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.emptyInputBehavior, [AutoNumeric.options.emptyInputBehavior.focus, AutoNumeric.options.emptyInputBehavior.press, AutoNumeric.options.emptyInputBehavior.always, AutoNumeric.options.emptyInputBehavior.min, AutoNumeric.options.emptyInputBehavior.max, AutoNumeric.options.emptyInputBehavior.zero, AutoNumeric.options.emptyInputBehavior["null"]]) || testFloatOrIntegerAndPossibleNegativeSign.test(options.emptyInputBehavior))) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The display on empty string option 'emptyInputBehavior' is invalid ; it should either be 'focus', 'press', 'always', 'min', 'max', 'zero', 'null', a number, or a string that represents a number, [".concat(options.emptyInputBehavior, "] given."));
      }

      if (options.emptyInputBehavior === AutoNumeric.options.emptyInputBehavior.zero && (options.minimumValue > 0 || options.maximumValue < 0)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The 'emptyInputBehavior' option is set to 'zero', but this value is outside of the range defined by 'minimumValue' and 'maximumValue' [".concat(options.minimumValue, ", ").concat(options.maximumValue, "]."));
      }

      if (testFloatOrIntegerAndPossibleNegativeSign.test(String(options.emptyInputBehavior))) {
        if (!this._isWithinRangeWithOverrideOption(options.emptyInputBehavior, options)) {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The 'emptyInputBehavior' option is set to a number or a string that represents a number, but its value [".concat(options.emptyInputBehavior, "] is outside of the range defined by the 'minimumValue' and 'maximumValue' options [").concat(options.minimumValue, ", ").concat(options.maximumValue, "]."));
        }
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.eventBubbles) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.eventBubbles)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The event bubbles option 'eventBubbles' is invalid ; it should be either 'true' or 'false', [".concat(options.eventBubbles, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.eventIsCancelable) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.eventIsCancelable)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The event is cancelable option 'eventIsCancelable' is invalid ; it should be either 'true' or 'false', [".concat(options.eventIsCancelable, "] given."));
      }

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.invalidClass) || !/^-?[_a-zA-Z]+[_a-zA-Z0-9-]*$/.test(options.invalidClass)) {
        
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The name of the 'invalidClass' option is not a valid CSS class name ; it should not be empty, and should follow the '^-?[_a-zA-Z]+[_a-zA-Z0-9-]*$' regex, [".concat(options.invalidClass, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.leadingZero, [AutoNumeric.options.leadingZero.allow, AutoNumeric.options.leadingZero.deny, AutoNumeric.options.leadingZero.keep])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The leading zero behavior option 'leadingZero' is invalid ; it should either be 'allow', 'deny' or 'keep', [".concat(options.leadingZero, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.formatOnPageLoad) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.formatOnPageLoad)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The format on initialization option 'formatOnPageLoad' is invalid ; it should be either 'true' or 'false', [".concat(options.formatOnPageLoad, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.formulaMode) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.formulaMode)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The formula mode option 'formulaMode' is invalid ; it should be either 'true' or 'false', [".concat(options.formulaMode, "] given."));
      }

      if (!testPositiveInteger.test(options.historySize) || options.historySize === 0) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The history size option 'historySize' is invalid ; it should be a positive integer, [".concat(options.historySize, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.selectNumberOnly) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.selectNumberOnly)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The select number only option 'selectNumberOnly' is invalid ; it should be either 'true' or 'false', [".concat(options.selectNumberOnly, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.selectOnFocus) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.selectOnFocus)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The select on focus option 'selectOnFocus' is invalid ; it should be either 'true' or 'false', [".concat(options.selectOnFocus, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.defaultValueOverride) && options.defaultValueOverride !== '' && !testFloatOrIntegerAndPossibleNegativeSign.test(options.defaultValueOverride)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The unformatted default value option 'defaultValueOverride' is invalid ; it should be a string that represents a positive or negative number, [".concat(options.defaultValueOverride, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.unformatOnSubmit) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.unformatOnSubmit)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The remove formatting on submit option 'unformatOnSubmit' is invalid ; it should be either 'true' or 'false', [".concat(options.unformatOnSubmit, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.valuesToStrings) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isObject(options.valuesToStrings)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The option 'valuesToStrings' is invalid ; it should be an object, ideally with 'key -> value' entries, [".concat(options.valuesToStrings, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.outputFormat) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.outputFormat, [AutoNumeric.options.outputFormat.string, AutoNumeric.options.outputFormat.number, AutoNumeric.options.outputFormat.dot, AutoNumeric.options.outputFormat.negativeDot, AutoNumeric.options.outputFormat.comma, AutoNumeric.options.outputFormat.negativeComma, AutoNumeric.options.outputFormat.dotNegative, AutoNumeric.options.outputFormat.commaNegative])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The custom locale format option 'outputFormat' is invalid ; it should either be null, 'string', 'number', '.', '-.', ',', '-,', '.-' or ',-', [".concat(options.outputFormat, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.isCancellable) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.isCancellable)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The cancellable behavior option 'isCancellable' is invalid ; it should be either 'true' or 'false', [".concat(options.isCancellable, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.modifyValueOnWheel) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.modifyValueOnWheel)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The increment/decrement on mouse wheel option 'modifyValueOnWheel' is invalid ; it should be either 'true' or 'false', [".concat(options.modifyValueOnWheel, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.watchExternalChanges) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.watchExternalChanges)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The option 'watchExternalChanges' is invalid ; it should be either 'true' or 'false', [".concat(options.watchExternalChanges, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.wheelOn, [AutoNumeric.options.wheelOn.focus, AutoNumeric.options.wheelOn.hover])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The wheel behavior option 'wheelOn' is invalid ; it should either be 'focus' or 'hover', [".concat(options.wheelOn, "] given."));
      }

      if (!(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(options.wheelStep) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(options.wheelStep)) || options.wheelStep !== 'progressive' && !testPositiveFloatOrInteger.test(options.wheelStep) || Number(options.wheelStep) === 0) {
        
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The wheel step value option 'wheelStep' is invalid ; it should either be the string 'progressive', or a number or a string that represents a positive number (excluding zero), [".concat(options.wheelStep, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(options.serializeSpaces, [AutoNumeric.options.serializeSpaces.plus, AutoNumeric.options.serializeSpaces.percent])) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The space replacement character option 'serializeSpaces' is invalid ; it should either be '+' or '%20', [".concat(options.serializeSpaces, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.noEventListeners) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.noEventListeners)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The option 'noEventListeners' that prevent the creation of event listeners is invalid ; it should be either 'true' or 'false', [".concat(options.noEventListeners, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.styleRules) && !(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isObject(options.styleRules) && (Object.prototype.hasOwnProperty.call(options.styleRules, 'positive') || Object.prototype.hasOwnProperty.call(options.styleRules, 'negative') || Object.prototype.hasOwnProperty.call(options.styleRules, 'ranges') || Object.prototype.hasOwnProperty.call(options.styleRules, 'userDefined')))) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The option 'styleRules' is invalid ; it should be a correctly structured object, with one or more 'positive', 'negative', 'ranges' or 'userDefined' attributes, [".concat(options.styleRules, "] given."));
      } 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.styleRules) && Object.prototype.hasOwnProperty.call(options.styleRules, 'userDefined') && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.styleRules.userDefined)) {
        options.styleRules.userDefined.forEach(function (rule) {
          if (Object.prototype.hasOwnProperty.call(rule, 'callback') && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isFunction(rule.callback)) {
            _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The callback defined in the `userDefined` attribute is not a function, ".concat(_typeof(rule.callback), " given."));
          }
        });
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.rawValueDivisor) && !testPositiveFloatOrInteger.test(options.rawValueDivisor) || options.rawValueDivisor === 0 || options.rawValueDivisor === '0' || options.rawValueDivisor === 1 || options.rawValueDivisor === '1') {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The raw value divisor option 'rawValueDivisor' is invalid ; it should be a positive number higher than one, preferably an integer, [".concat(options.rawValueDivisor, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.readOnly) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.readOnly)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The option 'readOnly' is invalid ; it should be either 'true' or 'false', [".concat(options.readOnly, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.unformatOnHover) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.unformatOnHover)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The option 'unformatOnHover' is invalid ; it should be either 'true' or 'false', [".concat(options.unformatOnHover, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.failOnUnknownOption) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.failOnUnknownOption)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The debug option 'failOnUnknownOption' is invalid ; it should be either 'true' or 'false', [".concat(options.failOnUnknownOption, "] given."));
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isTrueOrFalseString(options.createLocalList) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.createLocalList)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The debug option 'createLocalList' is invalid ; it should be either 'true' or 'false', [".concat(options.createLocalList, "] given."));
      }
    }
    

  }, {
    key: "_validateDecimalPlacesRawValue",
    value: function _validateDecimalPlacesRawValue(options) {
      
      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options.decimalPlacesRawValue)) {
        if (options.decimalPlacesRawValue < options.decimalPlaces) {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("The number of decimal places to store in the raw value [".concat(options.decimalPlacesRawValue, "] is lower than the ones to display [").concat(options.decimalPlaces, "]. This will likely confuse your users.\nTo solve that, you'd need to either set `decimalPlacesRawValue` to `null`, or set a number of decimal places for the raw value equal of bigger than `decimalPlaces`."), options.showWarnings);
        }

        if (options.decimalPlacesRawValue < options.decimalPlacesShownOnFocus) {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("The number of decimal places to store in the raw value [".concat(options.decimalPlacesRawValue, "] is lower than the ones shown on focus [").concat(options.decimalPlacesShownOnFocus, "]. This will likely confuse your users.\nTo solve that, you'd need to either set `decimalPlacesRawValue` to `null`, or set a number of decimal places for the raw value equal of bigger than `decimalPlacesShownOnFocus`."), options.showWarnings);
        }

        if (options.decimalPlacesRawValue < options.decimalPlacesShownOnBlur) {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("The number of decimal places to store in the raw value [".concat(options.decimalPlacesRawValue, "] is lower than the ones shown when unfocused [").concat(options.decimalPlacesShownOnBlur, "]. This will likely confuse your users.\nTo solve that, you'd need to either set `decimalPlacesRawValue` to `null`, or set a number of decimal places for the raw value equal of bigger than `decimalPlacesShownOnBlur`."), options.showWarnings);
        }
      }
    }
    

  }, {
    key: "areSettingsValid",
    value: function areSettingsValid(options) {
      var isValid = true;

      try {
        this.validate(options, true);
      } catch (error) {
        isValid = false;
      }

      return isValid;
    }
    

  }, {
    key: "getDefaultConfig",
    value: function getDefaultConfig() {
      return AutoNumeric.defaultSettings;
    }
    

  }, {
    key: "getPredefinedOptions",
    value: function getPredefinedOptions() {
      return AutoNumeric.predefinedOptions;
    }
    

  }, {
    key: "_generateOptionsObjectFromOptionsArray",
    value: function _generateOptionsObjectFromOptionsArray(options) {
      var _this14 = this;

      var optionsResult;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(options) || options.length === 0) {
        optionsResult = null;
      } else {
        optionsResult = {};

        if (options.length === 1 && Array.isArray(options[0])) {
          options[0].forEach(function (optionObject) {
            
            _extends(optionsResult, _this14._getOptionObject(optionObject));
          });
        } else if (options.length >= 1) {
          options.forEach(function (optionObject) {
            _extends(optionsResult, _this14._getOptionObject(optionObject));
          });
        }
      }

      return optionsResult;
    }
    

  }, {
    key: "format",
    value: function format(numericStringOrDomElement) {
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(numericStringOrDomElement) || numericStringOrDomElement === null) {
        return null;
      } 


      var value;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isElement(numericStringOrDomElement)) {
        value = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(numericStringOrDomElement);
      } else {
        value = numericStringOrDomElement;
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(value) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(value)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The value \"".concat(value, "\" being \"set\" is not numeric and therefore cannot be used appropriately."));
      } 


      for (var _len5 = arguments.length, options = new Array(_len5 > 1 ? _len5 - 1 : 0), _key5 = 1; _key5 < _len5; _key5++) {
        options[_key5 - 1] = arguments[_key5];
      }

      var optionsToUse = this._generateOptionsObjectFromOptionsArray(options); 


      var settings = _extends({}, this.getDefaultConfig(), optionsToUse);

      settings.isNegativeSignAllowed = value < 0;
      settings.isPositiveSignAllowed = value >= 0;

      this._setBrackets(settings);

      var regex = {};

      this._cachesUsualRegularExpressions(settings, regex); 
      
      


      var valueString = this._toNumericValue(value, settings);

      if (isNaN(Number(valueString))) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The value [".concat(valueString, "] that you are trying to format is not a recognized number."));
      } 


      if (!this._isWithinRangeWithOverrideOption(valueString, settings)) {
        
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].triggerEvent(AutoNumeric.events.formatted, document, {
          oldValue: null,
          newValue: null,
          oldRawValue: null,
          newRawValue: null,
          isPristine: null,
          error: 'Range test failed',
          aNElement: null
        }, true, true);
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The value [".concat(valueString, "] being set falls outside of the minimumValue [").concat(settings.minimumValue, "] and maximumValue [").concat(settings.maximumValue, "] range set for this element"));
      } 


      if (settings.valuesToStrings && this._checkValuesToStringsSettings(value, settings)) {
        return settings.valuesToStrings[value];
      } 


      this._correctNegativePositiveSignPlacementOption(settings); 


      this._calculateDecimalPlacesOnInit(settings); 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(settings.rawValueDivisor) && settings.rawValueDivisor !== 0 && 
      valueString !== '' && valueString !== null) {
        
        valueString *= settings.rawValueDivisor;
      } 


      valueString = this._roundFormattedValueShownOnFocus(valueString, settings);
      valueString = this._modifyNegativeSignAndDecimalCharacterForFormattedValue(valueString, settings);
      valueString = this._addGroupSeparators(valueString, settings, false, valueString);
      return valueString;
    }
    

  }, {
    key: "formatAndSet",
    value: function formatAndSet(domElement) {
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      
      var formattedValue = this.format(domElement, options);
      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementValue(domElement, formattedValue);
      return formattedValue;
    }
    

  }, {
    key: "unformat",
    value: function unformat(numericStringOrDomElement) {
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumberStrict(numericStringOrDomElement)) {
        
        return numericStringOrDomElement;
      } 


      var value;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isElement(numericStringOrDomElement)) {
        value = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(numericStringOrDomElement);
      } else {
        value = numericStringOrDomElement;
      }

      if (value === '') {
        
        return '';
      }

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(value) || value === null) {
        return null;
      }

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isArray(value) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isObject(value)) {
        
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("A number or a string representing a number is needed to be able to unformat it, [".concat(value, "] given."));
      } 


      for (var _len6 = arguments.length, options = new Array(_len6 > 1 ? _len6 - 1 : 0), _key6 = 1; _key6 < _len6; _key6++) {
        options[_key6 - 1] = arguments[_key6];
      }

      var optionsToUse = this._generateOptionsObjectFromOptionsArray(options); 


      var settings = _extends({}, this.getDefaultConfig(), optionsToUse);

      settings.isNegativeSignAllowed = false;
      settings.isPositiveSignAllowed = true;
      value = value.toString(); 

      if (settings.valuesToStrings && this._checkStringsToValuesSettings(value, settings)) {
        return _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].objectKeyLookup(settings.valuesToStrings, value);
      } 


      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(value, settings.negativeSignCharacter)) {
        settings.isNegativeSignAllowed = true;
        settings.isPositiveSignAllowed = false;
      } else if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(settings.negativeBracketsTypeOnBlur)) {
        var _settings$negativeBra = settings.negativeBracketsTypeOnBlur.split(',');

        var _settings$negativeBra2 = _slicedToArray(_settings$negativeBra, 2);

        settings.firstBracket = _settings$negativeBra2[0];
        settings.lastBracket = _settings$negativeBra2[1];

        if (value.charAt(0) === settings.firstBracket && value.charAt(value.length - 1) === settings.lastBracket) {
          settings.isNegativeSignAllowed = true;
          settings.isPositiveSignAllowed = false;
          value = this._removeBrackets(value, settings, false);
        }
      }

      value = this._convertToNumericString(value, settings);
      var unwantedCharacters = new RegExp("[^+-0123456789.]", 'gi');

      if (unwantedCharacters.test(value)) {
        return NaN;
      } 


      this._correctNegativePositiveSignPlacementOption(settings); 


      if (settings.decimalPlacesRawValue) {
        
        settings.originalDecimalPlacesRawValue = settings.decimalPlacesRawValue;
      } else {
        settings.originalDecimalPlacesRawValue = settings.decimalPlaces;
      }

      this._calculateDecimalPlacesOnInit(settings); 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(settings.rawValueDivisor) && settings.rawValueDivisor !== 0 && 
      value !== '' && value !== null) {
        
        value /= settings.rawValueDivisor;
      }

      value = this._roundRawValue(value, settings);
      value = value.replace(settings.decimalCharacter, '.'); 

      value = this._toLocale(value, settings.outputFormat, settings);
      return value;
    }
    

  }, {
    key: "unformatAndSet",
    value: function unformatAndSet(domElement) {
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      
      var unformattedValue = this.unformat(domElement, options);
      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementValue(domElement, unformattedValue);
      return unformattedValue;
    }
    

  }, {
    key: "localize",
    value: function localize(numericStringOrDomElement) {
      var settings = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var value;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isElement(numericStringOrDomElement)) {
        value = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].getElementValue(numericStringOrDomElement);
      } else {
        value = numericStringOrDomElement;
      }

      if (value === '') {
        
        return '';
      }

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(settings)) {
        settings = AutoNumeric.defaultSettings;
      }

      value = this.unformat(value, settings); 

      if (Number(value) === 0 && settings.leadingZero !== AutoNumeric.options.leadingZero.keep) {
        value = '0';
      }

      var outputFormatToUse;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(settings)) {
        outputFormatToUse = settings.outputFormat;
      } else {
        outputFormatToUse = AutoNumeric.defaultSettings.outputFormat;
      }

      return this._toLocale(value, outputFormatToUse, settings);
    }
  }, {
    key: "localizeAndSet",
    value: function localizeAndSet(domElement) {
      var options = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      
      var localizedValue = this.localize(domElement, options);
      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].setElementValue(domElement, localizedValue);
      return localizedValue;
    }
    

  }, {
    key: "isManagedByAutoNumeric",
    value: function isManagedByAutoNumeric(domElementOrSelector) {
      
      return this._isInGlobalList(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].domElement(domElementOrSelector));
    }
    

  }, {
    key: "getAutoNumericElement",
    value: function getAutoNumericElement(domElementOrSelector) {
      var domElement = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].domElement(domElementOrSelector);

      if (!this.isManagedByAutoNumeric(domElement)) {
        return null;
      }

      return this._getFromGlobalList(domElement);
    }
    

  }, {
    key: "set",
    value: function set(domElementOrSelector, newValue) {
      var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      var saveChangeToHistory = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : true;
      var domElement = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].domElement(domElementOrSelector);

      if (!this.isManagedByAutoNumeric(domElement)) {
        var showWarnings;

        if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options) && Object.prototype.hasOwnProperty.call(options, 'showWarnings')) {
          showWarnings = options.showWarnings;
        } else {
          showWarnings = true;
        }

        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("Impossible to find an AutoNumeric object for the given DOM element or selector.", showWarnings);
        return null;
      }

      return this.getAutoNumericElement(domElement).set(newValue, options, saveChangeToHistory);
    }
    

  }, {
    key: "getNumericString",
    value: function getNumericString(domElementOrSelector) {
      var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      return this._get(domElementOrSelector, 'getNumericString', callback);
    }
    

  }, {
    key: "getFormatted",
    value: function getFormatted(domElementOrSelector) {
      var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      return this._get(domElementOrSelector, 'getFormatted', callback);
    }
    

  }, {
    key: "getNumber",
    value: function getNumber(domElementOrSelector) {
      var callback = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      return this._get(domElementOrSelector, 'getNumber', callback);
    }
    

  }, {
    key: "_get",
    value: function _get(domElementOrSelector, getFunction) {
      var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      var domElement = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].domElement(domElementOrSelector);

      if (!this.isManagedByAutoNumeric(domElement)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("Impossible to find an AutoNumeric object for the given DOM element or selector.");
      }

      return this.getAutoNumericElement(domElement)[getFunction](callback);
    }
    

  }, {
    key: "getLocalized",
    value: function getLocalized(domElementOrSelector) {
      var forcedOutputFormat = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
      var callback = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      var domElement = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].domElement(domElementOrSelector);

      if (!this.isManagedByAutoNumeric(domElement)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("Impossible to find an AutoNumeric object for the given DOM element or selector.");
      }

      return this.getAutoNumericElement(domElement).getLocalized(forcedOutputFormat, callback);
    }
  }, {
    key: "_stripAllNonNumberCharacters",
    value: function _stripAllNonNumberCharacters(s, settings, stripZeros, isFocused) {
      return this._stripAllNonNumberCharactersExceptCustomDecimalChar(s, settings, stripZeros, isFocused).replace(settings.decimalCharacter, '.');
    }
    

  }, {
    key: "_stripAllNonNumberCharactersExceptCustomDecimalChar",
    value: function _stripAllNonNumberCharactersExceptCustomDecimalChar(s, settings, stripZeros, isFocused) {
      
      
      s = this._normalizeCurrencySuffixAndNegativeSignCharacters(s, settings); 

      s = s.replace(settings.allowedAutoStrip, ''); 

      var m = s.match(settings.numRegAutoStrip);
      s = m ? [m[1], m[2], m[3]].join('') : '';

      if (settings.leadingZero === AutoNumeric.options.leadingZero.allow || settings.leadingZero === AutoNumeric.options.leadingZero.keep) {
        var negativeSign = '';

        var _s$split = s.split(settings.decimalCharacter),
            _s$split2 = _slicedToArray(_s$split, 2),
            integerPart = _s$split2[0],
            decimalPart = _s$split2[1];

        var modifiedIntegerPart = integerPart;

        if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(modifiedIntegerPart, settings.negativeSignCharacter)) {
          negativeSign = settings.negativeSignCharacter;
          modifiedIntegerPart = modifiedIntegerPart.replace(settings.negativeSignCharacter, '');
        } 


        if (negativeSign === '' && modifiedIntegerPart.length > settings.mIntPos && modifiedIntegerPart.charAt(0) === '0') {
          modifiedIntegerPart = modifiedIntegerPart.slice(1);
        } 


        if (negativeSign !== '' && modifiedIntegerPart.length > settings.mIntNeg && modifiedIntegerPart.charAt(0) === '0') {
          modifiedIntegerPart = modifiedIntegerPart.slice(1);
        }

        s = "".concat(negativeSign).concat(modifiedIntegerPart).concat(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(decimalPart) ? '' : settings.decimalCharacter + decimalPart);
      }

      if (stripZeros && settings.leadingZero === AutoNumeric.options.leadingZero.deny || !isFocused && settings.leadingZero === AutoNumeric.options.leadingZero.allow) {
        s = s.replace(settings.stripReg, '$1$2');
      }

      return s;
    }
    

  }, {
    key: "_toggleNegativeBracket",
    value: function _toggleNegativeBracket(value, settings, isFocused) {
      
      var result;

      if (isFocused) {
        result = this._removeBrackets(value, settings);
      } else {
        result = this._addBrackets(value, settings);
      }

      return result;
    }
    

  }, {
    key: "_addBrackets",
    value: function _addBrackets(value, settings) {
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(settings.negativeBracketsTypeOnBlur)) {
        return value;
      }

      return "".concat(settings.firstBracket).concat(value.replace(settings.negativeSignCharacter, '')).concat(settings.lastBracket);
    }
    

  }, {
    key: "_removeBrackets",
    value: function _removeBrackets(value, settings) {
      var rearrangeSignsAndValueOrder = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
      var result;

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(settings.negativeBracketsTypeOnBlur) && value.charAt(0) === settings.firstBracket) {
        
        result = value.replace(settings.firstBracket, '');
        result = result.replace(settings.lastBracket, ''); 

        if (rearrangeSignsAndValueOrder) {
          
          result = result.replace(settings.currencySymbol, '');
          result = this._mergeCurrencySignNegativePositiveSignAndValue(result, settings, true, false); 
        } else {
          
          result = "".concat(settings.negativeSignCharacter).concat(result);
        }
      } else {
        result = value;
      }

      return result;
    }
    

  }, {
    key: "_setBrackets",
    value: function _setBrackets(settings) {
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(settings.negativeBracketsTypeOnBlur)) {
        settings.firstBracket = '';
        settings.lastBracket = '';
      } else {
        
        var _settings$negativeBra3 = settings.negativeBracketsTypeOnBlur.split(','),
            _settings$negativeBra4 = _slicedToArray(_settings$negativeBra3, 2),
            firstBracket = _settings$negativeBra4[0],
            lastBracket = _settings$negativeBra4[1];

        settings.firstBracket = firstBracket;
        settings.lastBracket = lastBracket;
      }
    }
    

  }, {
    key: "_convertToNumericString",
    value: function _convertToNumericString(s, settings) {
      
      s = this._removeBrackets(s, settings, false);
      s = this._normalizeCurrencySuffixAndNegativeSignCharacters(s, settings); 

      s = s.replace(new RegExp("[".concat(settings.digitGroupSeparator, "]"), 'g'), ''); 

      if (settings.decimalCharacter !== '.') {
        s = s.replace(settings.decimalCharacter, '.');
      } 


      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(s) && s.lastIndexOf('-') === s.length - 1) {
        s = s.replace('-', '');
        s = "-".concat(s);
      } 


      if (settings.showPositiveSign) {
        s = s.replace(settings.positiveSignCharacter, '');
      } 


      var convertToNumber = settings.leadingZero !== AutoNumeric.options.leadingZero.keep;
      var temp = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].arabicToLatinNumbers(s, convertToNumber, false, false);

      if (!isNaN(temp)) {
        s = temp.toString();
      }

      return s;
    }
    

  }, {
    key: "_normalizeCurrencySuffixAndNegativeSignCharacters",
    value: function _normalizeCurrencySuffixAndNegativeSignCharacters(s, settings) {
      s = String(s); 
      

      if (settings.currencySymbol !== AutoNumeric.options.currencySymbol.none) {
        s = s.replace(settings.currencySymbol, '');
      } 


      if (settings.suffixText !== AutoNumeric.options.suffixText.none) {
        s = s.replace(settings.suffixText, '');
      } 


      if (settings.negativeSignCharacter !== AutoNumeric.options.negativeSignCharacter.hyphen) {
        s = s.replace(settings.negativeSignCharacter, '-');
      }

      return s;
    }
    

  }, {
    key: "_toLocale",
    value: function _toLocale(value, locale, settings) {
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(locale) || locale === AutoNumeric.options.outputFormat.string) {
        return value;
      }

      var result;

      switch (locale) {
        case AutoNumeric.options.outputFormat.number:
          result = Number(value);
          break;

        case AutoNumeric.options.outputFormat.dotNegative:
          result = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(value) ? value.replace('-', '') + '-' : value;
          break;

        case AutoNumeric.options.outputFormat.comma:
        case AutoNumeric.options.outputFormat.negativeComma:
          result = value.replace('.', ',');
          break;

        case AutoNumeric.options.outputFormat.commaNegative:
          result = value.replace('.', ',');
          result = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(result) ? result.replace('-', '') + '-' : result;
          break;
        

        case AutoNumeric.options.outputFormat.dot:
        case AutoNumeric.options.outputFormat.negativeDot:
          result = value;
          break;

        default:
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The given outputFormat [".concat(locale, "] option is not recognized."));
      }

      if (locale !== AutoNumeric.options.outputFormat.number && settings.negativeSignCharacter !== '-') {
        
        result = result.replace('-', settings.negativeSignCharacter);
      }

      return result;
    }
  }, {
    key: "_modifyNegativeSignAndDecimalCharacterForFormattedValue",
    value: function _modifyNegativeSignAndDecimalCharacterForFormattedValue(s, settings) {
      
      if (settings.negativeSignCharacter !== '-') {
        s = s.replace('-', settings.negativeSignCharacter);
      }

      if (settings.decimalCharacter !== '.') {
        s = s.replace('.', settings.decimalCharacter);
      }

      return s;
    }
    

  }, {
    key: "_isElementValueEmptyOrOnlyTheNegativeSign",
    value: function _isElementValueEmptyOrOnlyTheNegativeSign(value, settings) {
      return value === '' || value === settings.negativeSignCharacter;
    }
    

  }, {
    key: "_orderValueCurrencySymbolAndSuffixText",
    value: function _orderValueCurrencySymbolAndSuffixText(value, settings, signOnEmpty) {
      var result;

      if (settings.emptyInputBehavior === AutoNumeric.options.emptyInputBehavior.always || signOnEmpty) {
        switch (settings.negativePositiveSignPlacement) {
          case AutoNumeric.options.negativePositiveSignPlacement.left:
          case AutoNumeric.options.negativePositiveSignPlacement.prefix:
          case AutoNumeric.options.negativePositiveSignPlacement.none:
            result = value + settings.currencySymbol + settings.suffixText;
            break;

          default:
            result = settings.currencySymbol + value + settings.suffixText;
        }
      } else {
        result = value;
      }

      return result;
    }
    

  }, {
    key: "_addGroupSeparators",
    value: function _addGroupSeparators(inputValue, settings, isFocused, currentRawValue) {
      var forcedRawValue = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;
      
      
      var isValueNegative;

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(forcedRawValue)) {
        
        isValueNegative = forcedRawValue < 0;
      } else {
        isValueNegative = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegative(inputValue, settings.negativeSignCharacter) || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegativeWithBrackets(inputValue, settings.firstBracket, settings.lastBracket); 
      }

      inputValue = this._stripAllNonNumberCharactersExceptCustomDecimalChar(inputValue, settings, false, isFocused);

      if (this._isElementValueEmptyOrOnlyTheNegativeSign(inputValue, settings)) {
        return this._orderValueCurrencySymbolAndSuffixText(inputValue, settings, true);
      }

      var isZeroOrHasNoValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isZeroOrHasNoValue(inputValue); 

      if (isValueNegative) {
        inputValue = inputValue.replace('-', ''); 
      }

      settings.digitalGroupSpacing = settings.digitalGroupSpacing.toString();
      var digitalGroup;

      switch (settings.digitalGroupSpacing) {
        case AutoNumeric.options.digitalGroupSpacing.two:
          digitalGroup = /(\d)((\d)(\d{2}?)+)$/;
          break;

        case AutoNumeric.options.digitalGroupSpacing.twoScaled:
          digitalGroup = /(\d)((?:\d{2}){0,2}\d{3}(?:(?:\d{2}){2}\d{3})*?)$/;
          break;

        case AutoNumeric.options.digitalGroupSpacing.four:
          digitalGroup = /(\d)((\d{4}?)+)$/;
          break;

        case AutoNumeric.options.digitalGroupSpacing.three:
        default:
          digitalGroup = /(\d)((\d{3}?)+)$/;
      } 


      var _inputValue$split = inputValue.split(settings.decimalCharacter),
          _inputValue$split2 = _slicedToArray(_inputValue$split, 2),
          integerPart = _inputValue$split2[0],
          decimalPart = _inputValue$split2[1];

      if (settings.decimalCharacterAlternative && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(decimalPart)) {
        var _inputValue$split3 = inputValue.split(settings.decimalCharacterAlternative);

        var _inputValue$split4 = _slicedToArray(_inputValue$split3, 2);

        integerPart = _inputValue$split4[0];
        decimalPart = _inputValue$split4[1];
      }

      if (settings.digitGroupSeparator !== '') {
        
        while (digitalGroup.test(integerPart)) {
          integerPart = integerPart.replace(digitalGroup, "$1".concat(settings.digitGroupSeparator, "$2"));
        }
      } 


      var decimalPlacesToRoundTo;

      if (isFocused) {
        decimalPlacesToRoundTo = settings.decimalPlacesShownOnFocus;
      } else {
        decimalPlacesToRoundTo = settings.decimalPlacesShownOnBlur;
      }

      if (decimalPlacesToRoundTo !== 0 && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(decimalPart)) {
        if (decimalPart.length > decimalPlacesToRoundTo) {
          
          decimalPart = decimalPart.substring(0, decimalPlacesToRoundTo);
        } 


        inputValue = "".concat(integerPart).concat(settings.decimalCharacter).concat(decimalPart);
      } else {
        
        inputValue = integerPart;
      } 


      inputValue = AutoNumeric._mergeCurrencySignNegativePositiveSignAndValue(inputValue, settings, isValueNegative, isZeroOrHasNoValue); 

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(forcedRawValue)) {
        
        forcedRawValue = currentRawValue;
      } 


      if (settings.negativeBracketsTypeOnBlur !== null && (forcedRawValue < 0 || _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegativeStrict(inputValue, settings.negativeSignCharacter))) {
        inputValue = this._toggleNegativeBracket(inputValue, settings, isFocused);
      }

      var result;

      if (settings.suffixText) {
        result = "".concat(inputValue).concat(settings.suffixText);
      } else {
        result = inputValue;
      }

      return result;
    }
    

  }, {
    key: "_mergeCurrencySignNegativePositiveSignAndValue",
    value: function _mergeCurrencySignNegativePositiveSignAndValue(inputValue, settings, isValueNegative, isZeroOrHasNoValue) {
      var signToUse = '';

      if (isValueNegative) {
        signToUse = settings.negativeSignCharacter;
      } else if (settings.showPositiveSign && !isZeroOrHasNoValue) {
        signToUse = settings.positiveSignCharacter;
      }

      var result;

      if (settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.prefix) {
        if (settings.negativePositiveSignPlacement !== AutoNumeric.options.negativePositiveSignPlacement.none && (isValueNegative || !isValueNegative && settings.showPositiveSign && !isZeroOrHasNoValue)) {
          switch (settings.negativePositiveSignPlacement) {
            case AutoNumeric.options.negativePositiveSignPlacement.prefix:
            case AutoNumeric.options.negativePositiveSignPlacement.left:
              result = "".concat(signToUse).concat(settings.currencySymbol).concat(inputValue);
              break;

            case AutoNumeric.options.negativePositiveSignPlacement.right:
              result = "".concat(settings.currencySymbol).concat(signToUse).concat(inputValue);
              break;

            case AutoNumeric.options.negativePositiveSignPlacement.suffix:
              result = "".concat(settings.currencySymbol).concat(inputValue).concat(signToUse);
              break;
          }
        } else {
          result = settings.currencySymbol + inputValue;
        }
      } else if (settings.currencySymbolPlacement === AutoNumeric.options.currencySymbolPlacement.suffix) {
        if (settings.negativePositiveSignPlacement !== AutoNumeric.options.negativePositiveSignPlacement.none && (isValueNegative || !isValueNegative && settings.showPositiveSign && !isZeroOrHasNoValue)) {
          switch (settings.negativePositiveSignPlacement) {
            case AutoNumeric.options.negativePositiveSignPlacement.suffix:
            case AutoNumeric.options.negativePositiveSignPlacement.right:
              result = "".concat(inputValue).concat(settings.currencySymbol).concat(signToUse);
              break;

            case AutoNumeric.options.negativePositiveSignPlacement.left:
              result = "".concat(inputValue).concat(signToUse).concat(settings.currencySymbol);
              break;

            case AutoNumeric.options.negativePositiveSignPlacement.prefix:
              result = "".concat(signToUse).concat(inputValue).concat(settings.currencySymbol);
              break;
          }
        } else {
          result = inputValue + settings.currencySymbol;
        }
      }

      return result;
    }
  }, {
    key: "_truncateZeros",
    value: function _truncateZeros(roundedInputValue, decimalPlacesNeeded) {
      var regex;

      switch (decimalPlacesNeeded) {
        case 0:
          
          regex = /(\.(?:\d*[1-9])?)0*$/;
          break;

        case 1:
          
          regex = /(\.\d(?:\d*[1-9])?)0*$/;
          break;

        default:
          
          regex = new RegExp("(\\.\\d{".concat(decimalPlacesNeeded, "}(?:\\d*[1-9])?)0*"));
      } 


      roundedInputValue = roundedInputValue.replace(regex, '$1');

      if (decimalPlacesNeeded === 0) {
        roundedInputValue = roundedInputValue.replace(/\.$/, '');
      }

      return roundedInputValue;
    }
    

  }, {
    key: "_roundRawValue",
    value: function _roundRawValue(value, settings) {
      return this._roundValue(value, settings, settings.decimalPlacesRawValue);
    }
    

  }, {
    key: "_roundFormattedValueShownOnFocus",
    value: function _roundFormattedValueShownOnFocus(value, settings) {
      return this._roundValue(value, settings, Number(settings.decimalPlacesShownOnFocus));
    }
    

  }, {
    key: "_roundFormattedValueShownOnBlur",
    value: function _roundFormattedValueShownOnBlur(value, settings) {
      return this._roundValue(value, settings, Number(settings.decimalPlacesShownOnBlur));
    }
    

  }, {
    key: "_roundFormattedValueShownOnFocusOrBlur",
    value: function _roundFormattedValueShownOnFocusOrBlur(value, settings, isFocused) {
      if (isFocused) {
        return this._roundFormattedValueShownOnFocus(value, settings);
      } else {
        return this._roundFormattedValueShownOnBlur(value, settings);
      }
    }
    

  }, {
    key: "_roundValue",
    value: function _roundValue(inputValue, settings, decimalPlacesToRoundTo) {
      
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(inputValue)) {
        
        return inputValue;
      } 


      inputValue = inputValue === '' ? '0' : inputValue.toString();

      if (settings.roundingMethod === AutoNumeric.options.roundingMethod.toNearest05 || settings.roundingMethod === AutoNumeric.options.roundingMethod.toNearest05Alt || settings.roundingMethod === AutoNumeric.options.roundingMethod.upToNext05 || settings.roundingMethod === AutoNumeric.options.roundingMethod.downToNext05) {
        return this._roundCloseTo05(inputValue, settings);
      }

      var _AutoNumeric$_prepare = AutoNumeric._prepareValueForRounding(inputValue, settings),
          _AutoNumeric$_prepare2 = _slicedToArray(_AutoNumeric$_prepare, 2),
          negativeSign = _AutoNumeric$_prepare2[0],
          preparedValue = _AutoNumeric$_prepare2[1];

      inputValue = preparedValue;
      var decimalCharacterPosition = inputValue.lastIndexOf('.');
      var inputValueHasNoDot = decimalCharacterPosition === -1; 

      var _inputValue$split5 = inputValue.split('.'),
          _inputValue$split6 = _slicedToArray(_inputValue$split5, 2),
          integerPart = _inputValue$split6[0],
          decimalPart = _inputValue$split6[1]; 


      var hasDecimals = decimalPart > 0; 

      if (!hasDecimals && (settings.allowDecimalPadding === AutoNumeric.options.allowDecimalPadding.never || settings.allowDecimalPadding === AutoNumeric.options.allowDecimalPadding.floats)) {
        
        return Number(inputValue) === 0 ? integerPart : "".concat(negativeSign).concat(integerPart);
      } 
      


      var temporaryDecimalPlacesOverride;

      if (settings.allowDecimalPadding === AutoNumeric.options.allowDecimalPadding.always || settings.allowDecimalPadding === AutoNumeric.options.allowDecimalPadding.floats) {
        temporaryDecimalPlacesOverride = decimalPlacesToRoundTo;
      } else {
        temporaryDecimalPlacesOverride = 0;
      } 


      var decimalPositionToUse = inputValueHasNoDot ? inputValue.length - 1 : decimalCharacterPosition; 

      var checkDecimalPlaces = inputValue.length - 1 - decimalPositionToUse;
      var inputValueRounded = ''; 

      if (checkDecimalPlaces <= decimalPlacesToRoundTo) {
        
        inputValueRounded = inputValue;

        if (checkDecimalPlaces < temporaryDecimalPlacesOverride) {
          if (inputValueHasNoDot) {
            inputValueRounded = "".concat(inputValueRounded).concat(settings.decimalCharacter);
          }

          var zeros = '000000'; 

          while (checkDecimalPlaces < temporaryDecimalPlacesOverride) {
            zeros = zeros.substring(0, temporaryDecimalPlacesOverride - checkDecimalPlaces);
            inputValueRounded += zeros;
            checkDecimalPlaces += zeros.length;
          }
        } else if (checkDecimalPlaces > temporaryDecimalPlacesOverride) {
          inputValueRounded = this._truncateZeros(inputValueRounded, temporaryDecimalPlacesOverride);
        } else if (checkDecimalPlaces === 0 && temporaryDecimalPlacesOverride === 0) {
          
          inputValueRounded = inputValueRounded.replace(/\.$/, '');
        }

        return Number(inputValueRounded) === 0 ? inputValueRounded : "".concat(negativeSign).concat(inputValueRounded);
      } 


      var roundedStrLength;

      if (inputValueHasNoDot) {
        roundedStrLength = decimalPlacesToRoundTo - 1;
      } else {
        roundedStrLength = Number(decimalPlacesToRoundTo) + Number(decimalCharacterPosition);
      }

      var lastDigit = Number(inputValue.charAt(roundedStrLength + 1));
      var inputValueArray = inputValue.substring(0, roundedStrLength + 1).split('');
      var odd;

      if (inputValue.charAt(roundedStrLength) === '.') {
        odd = inputValue.charAt(roundedStrLength - 1) % 2;
      } else {
        odd = inputValue.charAt(roundedStrLength) % 2;
      }

      if (this._shouldRoundUp(lastDigit, settings, negativeSign, odd)) {
        
        for (var i = inputValueArray.length - 1; i >= 0; i -= 1) {
          if (inputValueArray[i] !== '.') {
            inputValueArray[i] = +inputValueArray[i] + 1;

            if (inputValueArray[i] < 10) {
              break;
            }

            if (i > 0) {
              inputValueArray[i] = '0';
            }
          }
        }
      } 


      inputValueArray = inputValueArray.slice(0, roundedStrLength + 1); 

      inputValueRounded = this._truncateZeros(inputValueArray.join(''), temporaryDecimalPlacesOverride);
      return Number(inputValueRounded) === 0 ? inputValueRounded : "".concat(negativeSign).concat(inputValueRounded);
    }
    

  }, {
    key: "_roundCloseTo05",
    value: function _roundCloseTo05(value, settings) {
      switch (settings.roundingMethod) {
        case AutoNumeric.options.roundingMethod.toNearest05:
        case AutoNumeric.options.roundingMethod.toNearest05Alt:
          value = (Math.round(value * 20) / 20).toString();
          break;

        case AutoNumeric.options.roundingMethod.upToNext05:
          value = (Math.ceil(value * 20) / 20).toString();
          break;

        default:
          value = (Math.floor(value * 20) / 20).toString();
      }

      var result;

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].contains(value, '.')) {
        result = value + '.00';
      } else if (value.length - value.indexOf('.') < 3) {
        result = value + '0';
      } else {
        result = value;
      }

      return result;
    }
    

  }, {
    key: "_prepareValueForRounding",
    value: function _prepareValueForRounding(value, settings) {
      
      var negativeSign = '';

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNegativeStrict(value, '-')) {
        
        negativeSign = '-'; 

        value = value.replace('-', '');
      } 


      if (!value.match(/^\d/)) {
        value = "0".concat(value);
      } 


      if (Number(value) === 0) {
        negativeSign = '';
      } 


      if (Number(value) > 0 && settings.leadingZero !== AutoNumeric.options.leadingZero.keep || value.length > 0 && settings.leadingZero === AutoNumeric.options.leadingZero.allow) {
        value = value.replace(/^0*(\d)/, '$1');
      }

      return [negativeSign, value];
    }
    

  }, {
    key: "_shouldRoundUp",
    value: function _shouldRoundUp(lastDigit, settings, negativeSign, odd) {
      return lastDigit > 4 && settings.roundingMethod === AutoNumeric.options.roundingMethod.halfUpSymmetric || 
      lastDigit > 4 && settings.roundingMethod === AutoNumeric.options.roundingMethod.halfUpAsymmetric && negativeSign === '' || 
      lastDigit > 5 && settings.roundingMethod === AutoNumeric.options.roundingMethod.halfUpAsymmetric && negativeSign === '-' || 
      lastDigit > 5 && settings.roundingMethod === AutoNumeric.options.roundingMethod.halfDownSymmetric || 
      lastDigit > 5 && settings.roundingMethod === AutoNumeric.options.roundingMethod.halfDownAsymmetric && negativeSign === '' || 
      lastDigit > 4 && settings.roundingMethod === AutoNumeric.options.roundingMethod.halfDownAsymmetric && negativeSign === '-' || 
      lastDigit > 5 && settings.roundingMethod === AutoNumeric.options.roundingMethod.halfEvenBankersRounding || lastDigit === 5 && settings.roundingMethod === AutoNumeric.options.roundingMethod.halfEvenBankersRounding && odd === 1 || lastDigit > 0 && settings.roundingMethod === AutoNumeric.options.roundingMethod.toCeilingTowardPositiveInfinity && negativeSign === '' || lastDigit > 0 && settings.roundingMethod === AutoNumeric.options.roundingMethod.toFloorTowardNegativeInfinity && negativeSign === '-' || lastDigit > 0 && settings.roundingMethod === AutoNumeric.options.roundingMethod.upRoundAwayFromZero; 
    }
    

  }, {
    key: "_truncateDecimalPlaces",
    value: function _truncateDecimalPlaces(value, settings, isPaste, decimalPlacesToRoundTo) {
      if (isPaste) {
        value = this._roundFormattedValueShownOnFocus(value, settings);
      }

      var _value$split = value.split(settings.decimalCharacter),
          _value$split2 = _slicedToArray(_value$split, 2),
          integerPart = _value$split2[0],
          decimalPart = _value$split2[1]; 


      if (decimalPart && decimalPart.length > decimalPlacesToRoundTo) {
        if (decimalPlacesToRoundTo > 0) {
          var modifiedDecimalPart = decimalPart.substring(0, decimalPlacesToRoundTo);
          value = "".concat(integerPart).concat(settings.decimalCharacter).concat(modifiedDecimalPart);
        } else {
          value = integerPart;
        }
      }

      return value;
    }
    

  }, {
    key: "_checkIfInRangeWithOverrideOption",
    value: function _checkIfInRangeWithOverrideOption(value, settings) {
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(value) && settings.emptyInputBehavior === AutoNumeric.options.emptyInputBehavior["null"] || 
      settings.overrideMinMaxLimits === AutoNumeric.options.overrideMinMaxLimits.ignore || settings.overrideMinMaxLimits === AutoNumeric.options.overrideMinMaxLimits.invalid) {
        return [true, true];
      }

      value = value.toString();
      value = value.replace(',', '.');
      var minParse = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].parseStr(settings.minimumValue);
      var maxParse = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].parseStr(settings.maximumValue);
      var valParse = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].parseStr(value);
      var result;

      switch (settings.overrideMinMaxLimits) {
        case AutoNumeric.options.overrideMinMaxLimits.floor:
          result = [_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].testMinMax(minParse, valParse) > -1, true];
          break;

        case AutoNumeric.options.overrideMinMaxLimits.ceiling:
          result = [true, _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].testMinMax(maxParse, valParse) < 1];
          break;

        default:
          result = [_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].testMinMax(minParse, valParse) > -1, _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].testMinMax(maxParse, valParse) < 1];
      }

      return result;
    }
    

  }, {
    key: "_isWithinRangeWithOverrideOption",
    value: function _isWithinRangeWithOverrideOption(value, settings) {
      var _this$_checkIfInRange = this._checkIfInRangeWithOverrideOption(value, settings),
          _this$_checkIfInRange2 = _slicedToArray(_this$_checkIfInRange, 2),
          minTest = _this$_checkIfInRange2[0],
          maxTest = _this$_checkIfInRange2[1];

      return minTest && maxTest;
    }
    

  }, {
    key: "_cleanValueForRangeParse",
    value: function _cleanValueForRangeParse(value) {
      value = value.toString().replace(',', '.');
      return _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].parseStr(value);
    }
    

  }, {
    key: "_isMinimumRangeRespected",
    value: function _isMinimumRangeRespected(value, settings) {
      return _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].testMinMax(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].parseStr(settings.minimumValue), this._cleanValueForRangeParse(value)) > -1;
    }
    

  }, {
    key: "_isMaximumRangeRespected",
    value: function _isMaximumRangeRespected(value, settings) {
      return _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].testMinMax(_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].parseStr(settings.maximumValue), this._cleanValueForRangeParse(value)) < 1;
    }
  }, {
    key: "_readCookie",
    value: function _readCookie(name) {
      var nameEQ = name + '=';
      var ca = document.cookie.split(';');
      var c = '';

      for (var i = 0; i < ca.length; i += 1) {
        c = ca[i];

        while (c.charAt(0) === ' ') {
          c = c.substring(1, c.length);
        }

        if (c.indexOf(nameEQ) === 0) {
          return c.substring(nameEQ.length, c.length);
        }
      }

      return null;
    }
    

  }, {
    key: "_storageTest",
    value: function _storageTest() {
      var mod = 'modernizr';

      try {
        sessionStorage.setItem(mod, mod);
        sessionStorage.removeItem(mod);
        return true;
      } catch (e) {
        return false;
      }
    }
  }, {
    key: "_correctNegativePositiveSignPlacementOption",
    value: function _correctNegativePositiveSignPlacementOption(settings) {
      
      
      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(settings.negativePositiveSignPlacement)) {
        return;
      }

      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(settings) && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(settings.negativePositiveSignPlacement) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(settings.currencySymbol)) {
        switch (settings.currencySymbolPlacement) {
          case AutoNumeric.options.currencySymbolPlacement.suffix:
            settings.negativePositiveSignPlacement = AutoNumeric.options.negativePositiveSignPlacement.prefix; 

            break;

          case AutoNumeric.options.currencySymbolPlacement.prefix:
            settings.negativePositiveSignPlacement = AutoNumeric.options.negativePositiveSignPlacement.left; 

            break;

          default: 

        }
      } else {
        
        settings.negativePositiveSignPlacement = AutoNumeric.options.negativePositiveSignPlacement.left;
      }
    }
    

  }, {
    key: "_correctCaretPositionOnFocusAndSelectOnFocusOptions",
    value: function _correctCaretPositionOnFocusAndSelectOnFocusOptions(options) {
      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options)) {
        return null;
      } 


      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(options.caretPositionOnFocus) && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(options.selectOnFocus)) {
        options.selectOnFocus = AutoNumeric.options.selectOnFocus.doNotSelect;
      } 


      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(options.caretPositionOnFocus) && !_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefinedOrNullOrEmpty(options.selectOnFocus) && options.selectOnFocus === AutoNumeric.options.selectOnFocus.select) {
        options.caretPositionOnFocus = AutoNumeric.options.caretPositionOnFocus.doNoForceCaretPosition;
      }

      return options;
    }
    

  }, {
    key: "_calculateDecimalPlacesOnInit",
    value: function _calculateDecimalPlacesOnInit(settings) {
      
      this._validateDecimalPlacesRawValue(settings); 
      
      
      


      if (settings.decimalPlacesShownOnFocus === AutoNumeric.options.decimalPlacesShownOnFocus.useDefault) {
        settings.decimalPlacesShownOnFocus = settings.decimalPlaces;
      }

      if (settings.decimalPlacesShownOnBlur === AutoNumeric.options.decimalPlacesShownOnBlur.useDefault) {
        settings.decimalPlacesShownOnBlur = settings.decimalPlaces;
      }

      if (settings.decimalPlacesRawValue === AutoNumeric.options.decimalPlacesRawValue.useDefault) {
        settings.decimalPlacesRawValue = settings.decimalPlaces;
      } 


      var additionalDecimalPlacesRawValue = 0;

      if (settings.rawValueDivisor && settings.rawValueDivisor !== AutoNumeric.options.rawValueDivisor.none) {
        additionalDecimalPlacesRawValue = String(settings.rawValueDivisor).length - 1; 

        if (additionalDecimalPlacesRawValue < 0) {
          additionalDecimalPlacesRawValue = 0;
        }
      }

      settings.decimalPlacesRawValue = Math.max(Math.max(settings.decimalPlacesShownOnBlur, settings.decimalPlacesShownOnFocus) + additionalDecimalPlacesRawValue, Number(settings.originalDecimalPlacesRawValue) + additionalDecimalPlacesRawValue);
    }
    

  }, {
    key: "_calculateDecimalPlacesOnUpdate",
    value: function _calculateDecimalPlacesOnUpdate(settings) {
      var currentSettings = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      
      this._validateDecimalPlacesRawValue(settings); 


      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(currentSettings)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("When updating the settings, the previous ones should be passed as an argument.");
      }

      var decimalPlacesInOptions = ('decimalPlaces' in settings);

      if (!(decimalPlacesInOptions || 'decimalPlacesRawValue' in settings || 'decimalPlacesShownOnFocus' in settings || 'decimalPlacesShownOnBlur' in settings || 'rawValueDivisor' in settings)) {
        
        return;
      } 


      if (decimalPlacesInOptions) {
        if (!('decimalPlacesShownOnFocus' in settings) || settings.decimalPlacesShownOnFocus === AutoNumeric.options.decimalPlacesShownOnFocus.useDefault) {
          settings.decimalPlacesShownOnFocus = settings.decimalPlaces;
        }

        if (!('decimalPlacesShownOnBlur' in settings) || settings.decimalPlacesShownOnBlur === AutoNumeric.options.decimalPlacesShownOnBlur.useDefault) {
          settings.decimalPlacesShownOnBlur = settings.decimalPlaces;
        }

        if (!('decimalPlacesRawValue' in settings) || settings.decimalPlacesRawValue === AutoNumeric.options.decimalPlacesRawValue.useDefault) {
          settings.decimalPlacesRawValue = settings.decimalPlaces;
        }
      } else {
        if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(settings.decimalPlacesShownOnFocus)) {
          settings.decimalPlacesShownOnFocus = currentSettings.decimalPlacesShownOnFocus;
        }

        if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(settings.decimalPlacesShownOnBlur)) {
          settings.decimalPlacesShownOnBlur = currentSettings.decimalPlacesShownOnBlur;
        }
      } 


      var additionalDecimalPlacesRawValue = 0;

      if (settings.rawValueDivisor && settings.rawValueDivisor !== AutoNumeric.options.rawValueDivisor.none) {
        additionalDecimalPlacesRawValue = String(settings.rawValueDivisor).length - 1; 

        if (additionalDecimalPlacesRawValue < 0) {
          additionalDecimalPlacesRawValue = 0;
        }
      }

      if (!settings.decimalPlaces && !settings.decimalPlacesRawValue) {
        settings.decimalPlacesRawValue = Math.max(Math.max(settings.decimalPlacesShownOnBlur, settings.decimalPlacesShownOnFocus) + additionalDecimalPlacesRawValue, Number(currentSettings.originalDecimalPlacesRawValue) + additionalDecimalPlacesRawValue);
      } else {
        settings.decimalPlacesRawValue = Math.max(Math.max(settings.decimalPlacesShownOnBlur, settings.decimalPlacesShownOnFocus) + additionalDecimalPlacesRawValue, Number(settings.decimalPlacesRawValue) + additionalDecimalPlacesRawValue);
      }
    }
  }, {
    key: "_cachesUsualRegularExpressions",
    value: function _cachesUsualRegularExpressions(settings, regex) {
      
      var negativeSignReg;

      if (settings.negativeSignCharacter !== AutoNumeric.options.negativeSignCharacter.hyphen) {
        negativeSignReg = "([-\\".concat(settings.negativeSignCharacter, "]?)");
      } else {
        negativeSignReg = '(-?)';
      }

      regex.aNegRegAutoStrip = negativeSignReg;
      settings.allowedAutoStrip = new RegExp("[^-0123456789\\".concat(settings.decimalCharacter, "]"), 'g');
      settings.numRegAutoStrip = new RegExp("".concat(negativeSignReg, "(?:\\").concat(settings.decimalCharacter, "?([0-9]+\\").concat(settings.decimalCharacter, "[0-9]+)|([0-9]*(?:\\").concat(settings.decimalCharacter, "[0-9]*)?))")); 

      settings.stripReg = new RegExp("^".concat(regex.aNegRegAutoStrip, "0*([0-9])")); 

      settings.formulaChars = new RegExp("[0-9".concat(settings.decimalCharacter, "+\\-*/() ]"));
    }
  }, {
    key: "_convertOldOptionsToNewOnes",
    value: function _convertOldOptionsToNewOnes(options) {
      
      var oldOptionsConverter = {
        
        aSep: 'digitGroupSeparator',
        nSep: 'showOnlyNumbersOnFocus',
        dGroup: 'digitalGroupSpacing',
        aDec: 'decimalCharacter',
        altDec: 'decimalCharacterAlternative',
        aSign: 'currencySymbol',
        pSign: 'currencySymbolPlacement',
        pNeg: 'negativePositiveSignPlacement',
        aSuffix: 'suffixText',
        oLimits: 'overrideMinMaxLimits',
        vMax: 'maximumValue',
        vMin: 'minimumValue',
        mDec: 'decimalPlacesOverride',
        eDec: 'decimalPlacesShownOnFocus',
        scaleDecimal: 'decimalPlacesShownOnBlur',
        aStor: 'saveValueToSessionStorage',
        mRound: 'roundingMethod',
        aPad: 'allowDecimalPadding',
        nBracket: 'negativeBracketsTypeOnBlur',
        wEmpty: 'emptyInputBehavior',
        lZero: 'leadingZero',
        aForm: 'formatOnPageLoad',
        sNumber: 'selectNumberOnly',
        anDefault: 'defaultValueOverride',
        unSetOnSubmit: 'unformatOnSubmit',
        outputType: 'outputFormat',
        debug: 'showWarnings',
        
        allowDecimalPadding: true,
        alwaysAllowDecimalCharacter: true,
        caretPositionOnFocus: true,
        createLocalList: true,
        currencySymbol: true,
        currencySymbolPlacement: true,
        decimalCharacter: true,
        decimalCharacterAlternative: true,
        decimalPlaces: true,
        decimalPlacesRawValue: true,
        decimalPlacesShownOnBlur: true,
        decimalPlacesShownOnFocus: true,
        defaultValueOverride: true,
        digitalGroupSpacing: true,
        digitGroupSeparator: true,
        divisorWhenUnfocused: true,
        emptyInputBehavior: true,
        eventBubbles: true,
        eventIsCancelable: true,
        failOnUnknownOption: true,
        formatOnPageLoad: true,
        formulaMode: true,
        historySize: true,
        isCancellable: true,
        leadingZero: true,
        maximumValue: true,
        minimumValue: true,
        modifyValueOnWheel: true,
        negativeBracketsTypeOnBlur: true,
        negativePositiveSignPlacement: true,
        negativeSignCharacter: true,
        noEventListeners: true,
        onInvalidPaste: true,
        outputFormat: true,
        overrideMinMaxLimits: true,
        positiveSignCharacter: true,
        rawValueDivisor: true,
        readOnly: true,
        roundingMethod: true,
        saveValueToSessionStorage: true,
        selectNumberOnly: true,
        selectOnFocus: true,
        serializeSpaces: true,
        showOnlyNumbersOnFocus: true,
        showPositiveSign: true,
        showWarnings: true,
        styleRules: true,
        suffixText: true,
        symbolWhenUnfocused: true,
        unformatOnHover: true,
        unformatOnSubmit: true,
        valuesToStrings: true,
        watchExternalChanges: true,
        wheelOn: true,
        wheelStep: true,
        
        
        allowedAutoStrip: true,
        formulaChars: true,
        isNegativeSignAllowed: true,
        isPositiveSignAllowed: true,
        mIntNeg: true,
        mIntPos: true,
        numRegAutoStrip: true,
        originalDecimalPlaces: true,
        originalDecimalPlacesRawValue: true,
        stripReg: true
      };

      for (var option in options) {
        if (Object.prototype.hasOwnProperty.call(options, option)) {
          if (oldOptionsConverter[option] === true) {
            
            continue;
          }

          if (Object.prototype.hasOwnProperty.call(oldOptionsConverter, option)) {
            
            _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("You are using the deprecated option name '".concat(option, "'. Please use '").concat(oldOptionsConverter[option], "' instead from now on. The old option name will be dropped very soon\u2122."), true); 

            options[oldOptionsConverter[option]] = options[option];
            delete options[option];
          } else if (options.failOnUnknownOption) {
            
            _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("Option name '".concat(option, "' is unknown. Please fix the options passed to autoNumeric"));
          }
        }
      }

      if ('mDec' in options) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning('The old `mDec` option has been deprecated in favor of more accurate options ; `decimalPlaces`, `decimalPlacesRawValue`, `decimalPlacesShownOnFocus` and `decimalPlacesShownOnBlur`.', true);
      }
    }
  }, {
    key: "_setNegativePositiveSignPermissions",
    value: function _setNegativePositiveSignPermissions(settings) {
      settings.isNegativeSignAllowed = settings.minimumValue < 0;
      settings.isPositiveSignAllowed = settings.maximumValue >= 0;
    }
    

  }, {
    key: "_toNumericValue",
    value: function _toNumericValue(value, settings) {
      
      var result;

      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(Number(value))) {
        
        
        result = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].scientificToDecimal(value);
      } else {
        
        
        result = this._convertToNumericString(value.toString(), settings); 

        if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(Number(result))) {
          _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("The given value \"".concat(value, "\" cannot be converted to a numeric one and therefore cannot be used appropriately."), settings.showWarnings);
          result = NaN;
        }
      }

      return result;
    }
  }, {
    key: "_checkIfInRange",
    value: function _checkIfInRange(value, parsedMinValue, parsedMaxValue) {
      var parsedValue = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].parseStr(value);
      return _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].testMinMax(parsedMinValue, parsedValue) > -1 && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].testMinMax(parsedMaxValue, parsedValue) < 1;
    }
  }, {
    key: "_shouldSkipEventKey",
    value: function _shouldSkipEventKey(eventKeyName) {
      var isFnKeys = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(eventKeyName, _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName._allFnKeys);
      var isOSKeys = eventKeyName === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.OSLeft || eventKeyName === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.OSRight;
      var isContextMenu = eventKeyName === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.ContextMenu;
      var isSomeNonPrintableKeys = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isInArray(eventKeyName, _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName._someNonPrintableKeys);
      var isOtherNonPrintableKeys = eventKeyName === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.NumLock || eventKeyName === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.ScrollLock || eventKeyName === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Insert || eventKeyName === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Command;
      var isUnrecognizableKeys = eventKeyName === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_1__["default"].keyName.Unidentified;
      return isFnKeys || isOSKeys || isContextMenu || isSomeNonPrintableKeys || isUnrecognizableKeys || isOtherNonPrintableKeys;
    }
  }, {
    key: "_serialize",
    value: function _serialize(form) {
      var _this15 = this;

      var intoAnArray = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
      var formatType = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'unformatted';
      var serializedSpaceCharacter = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : '+';
      var forcedOutputFormat = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : null;
      var result = [];

      if (_typeof(form) === 'object' && form.nodeName.toLowerCase() === 'form') {
        Array.prototype.slice.call(form.elements).forEach(function (element) {
          if (element.name && !element.disabled && ['file', 'reset', 'submit', 'button'].indexOf(element.type) === -1) {
            if (element.type === 'select-multiple') {
              Array.prototype.slice.call(element.options).forEach(function (option) {
                if (option.selected) {
                  
                  if (intoAnArray) {
                    result.push({
                      name: element.name,
                      value: option.value
                    });
                  } else {
                    
                    result.push("".concat(encodeURIComponent(element.name), "=").concat(encodeURIComponent(option.value)));
                  }
                }
              });
            } else if (['checkbox', 'radio'].indexOf(element.type) === -1 || element.checked) {
              var valueResult;

              if (_this15.isManagedByAutoNumeric(element)) {
                var anObject;

                switch (formatType) {
                  case 'unformatted':
                    anObject = _this15.getAutoNumericElement(element);

                    if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(anObject)) {
                      valueResult = _this15.unformat(element, anObject.getSettings());
                    }

                    break;

                  case 'localized':
                    anObject = _this15.getAutoNumericElement(element);

                    if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(anObject)) {
                      
                      var currentSettings = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].cloneObject(anObject.getSettings());

                      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(forcedOutputFormat)) {
                        currentSettings.outputFormat = forcedOutputFormat;
                      }

                      valueResult = _this15.localize(element, currentSettings);
                    }

                    break;

                  case 'formatted':
                  default:
                    valueResult = element.value;
                }
              } else {
                valueResult = element.value;
              }

              if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isUndefined(valueResult)) {
                _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError('This error should never be hit. If it has, something really wrong happened!');
              }

              if (intoAnArray) {
                result.push({
                  name: element.name,
                  value: valueResult
                });
              } else {
                
                result.push("".concat(encodeURIComponent(element.name), "=").concat(encodeURIComponent(valueResult)));
              }
            }
          }
        });
      }

      var finalResult;

      if (intoAnArray) {
        
        
        finalResult = result;
      } else {
        
        finalResult = result.join('&');

        if ('+' === serializedSpaceCharacter) {
          finalResult = finalResult.replace(/%20/g, '+');
        }
      }

      return finalResult;
    }
    

  }, {
    key: "_serializeNumericString",
    value: function _serializeNumericString(form) {
      var serializedSpaceCharacter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '+';
      return this._serialize(form, false, 'unformatted', serializedSpaceCharacter);
    }
    

  }, {
    key: "_serializeFormatted",
    value: function _serializeFormatted(form) {
      var serializedSpaceCharacter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '+';
      return this._serialize(form, false, 'formatted', serializedSpaceCharacter);
    }
    

  }, {
    key: "_serializeLocalized",
    value: function _serializeLocalized(form) {
      var serializedSpaceCharacter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '+';
      var forcedOutputFormat = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      return this._serialize(form, false, 'localized', serializedSpaceCharacter, forcedOutputFormat);
    }
    

  }, {
    key: "_serializeNumericStringArray",
    value: function _serializeNumericStringArray(form) {
      var serializedSpaceCharacter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '+';
      return this._serialize(form, true, 'unformatted', serializedSpaceCharacter);
    }
    

  }, {
    key: "_serializeFormattedArray",
    value: function _serializeFormattedArray(form) {
      var serializedSpaceCharacter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '+';
      return this._serialize(form, true, 'formatted', serializedSpaceCharacter);
    }
    

  }, {
    key: "_serializeLocalizedArray",
    value: function _serializeLocalizedArray(form) {
      var serializedSpaceCharacter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '+';
      var forcedOutputFormat = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      return this._serialize(form, true, 'localized', serializedSpaceCharacter, forcedOutputFormat);
    }
  }]);

  return AutoNumeric;
}();





AutoNumeric.multiple = function (arg1) {
  var initialValue = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;
  var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
  var result = []; 

  if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isObject(initialValue)) {
    
    options = initialValue;
    initialValue = null;
  }

  if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isString(arg1)) {
    arg1 = _toConsumableArray(document.querySelectorAll(arg1)); 
  } else if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isObject(arg1)) {
    if (!Object.prototype.hasOwnProperty.call(arg1, 'rootElement')) {
      _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The object passed to the 'multiple' function is invalid ; no 'rootElement' attribute found.");
    } 


    var elements = _toConsumableArray(arg1.rootElement.querySelectorAll('input'));

    if (Object.prototype.hasOwnProperty.call(arg1, 'exclude')) {
      if (!Array.isArray(arg1.exclude)) {
        _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The 'exclude' array passed to the 'multiple' function is invalid.");
      } 


      arg1 = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].filterOut(elements, arg1.exclude);
    } else {
      arg1 = elements;
    }
  } else if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isArray(arg1)) {
    _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].throwError("The given parameters to the 'multiple' function are invalid.");
  }

  if (arg1.length === 0) {
    var showWarnings = true;

    if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNull(options) && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isBoolean(options.showWarnings)) {
      showWarnings = options.showWarnings;
    }

    _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].warning("No valid DOM elements were given hence no AutoNumeric objects were instantiated.", showWarnings);
    return [];
  } 
  
  
  
  


  var isInitialValueArray = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isArray(initialValue);
  var isInitialValueArrayAndNotEmpty = isInitialValueArray && initialValue.length >= 1;
  var secondArgumentIsInitialValueArray = false;
  var secondArgumentIsOptionArray = false; 

  if (isInitialValueArrayAndNotEmpty) {
    var typeOfFirstArrayElement = _typeof(Number(initialValue[0])); 


    secondArgumentIsInitialValueArray = typeOfFirstArrayElement === 'number' && !isNaN(Number(initialValue[0]));

    if (!secondArgumentIsInitialValueArray) {
      
      if (typeOfFirstArrayElement === 'string' || isNaN(typeOfFirstArrayElement) || typeOfFirstArrayElement === 'object') {
        secondArgumentIsOptionArray = true;
      }
    }
  } 


  var isOptionsArrayAndNotEmpty = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isArray(options) && options.length >= 1;
  var thirdArgumentIsOptionArray = false;

  if (isOptionsArrayAndNotEmpty) {
    var _typeOfFirstArrayElement = _typeof(options[0]);

    if (_typeOfFirstArrayElement === 'string' || _typeOfFirstArrayElement === 'object') {
      
      thirdArgumentIsOptionArray = true;
    }
  } 


  var optionsToUse;

  if (secondArgumentIsOptionArray) {
    optionsToUse = AutoNumeric.mergeOptions(initialValue);
  } else if (thirdArgumentIsOptionArray) {
    optionsToUse = AutoNumeric.mergeOptions(options);
  } else {
    optionsToUse = options;
  } 


  var isInitialValueNumber = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isNumber(initialValue);
  var initialValueArraySize;

  if (secondArgumentIsInitialValueArray) {
    initialValueArraySize = initialValue.length;
  } 


  arg1.forEach(function (domElement, index) {
    if (isInitialValueNumber) {
      
      result.push(new AutoNumeric(domElement, initialValue, optionsToUse));
    } else if (secondArgumentIsInitialValueArray && index <= initialValueArraySize) {
      result.push(new AutoNumeric(domElement, initialValue[index], optionsToUse));
    } else {
      result.push(new AutoNumeric(domElement, null, optionsToUse));
    }
  });
  return result;
};



(function () {
  
  if (!Array.from) {
    Array.from = function (object) {
      return [].slice.call(object);
    };
  } 


  if (typeof window === 'undefined' || typeof window.CustomEvent === 'function') {
    return false;
  }

  function CustomEvent(event, params) {
    params = params || {
      bubbles: false,
      cancelable: false,
      detail: void 0
    };
    var evt = document.createEvent('CustomEvent');
    evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
    return evt;
  }

  CustomEvent.prototype = window.Event.prototype;
  window.CustomEvent = CustomEvent;
})();

 }),

 "./src/AutoNumericDefaultSettings.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
 var _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__( "./src/AutoNumeric.js");
 var _AutoNumericOptions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__( "./src/AutoNumericOptions.js");







_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].defaultSettings = {
  allowDecimalPadding: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.allowDecimalPadding.always,
  alwaysAllowDecimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.alwaysAllowDecimalCharacter.doNotAllow,
  caretPositionOnFocus: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.caretPositionOnFocus.doNoForceCaretPosition,
  createLocalList: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.createLocalList.createList,
  currencySymbol: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbol.none,
  currencySymbolPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbolPlacement.prefix,
  decimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacter.dot,
  decimalCharacterAlternative: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacterAlternative.none,
  decimalPlaces: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalPlaces.two,
  decimalPlacesRawValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalPlacesRawValue.useDefault,
  decimalPlacesShownOnBlur: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalPlacesShownOnBlur.useDefault,
  decimalPlacesShownOnFocus: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalPlacesShownOnFocus.useDefault,
  defaultValueOverride: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.defaultValueOverride.doNotOverride,
  digitalGroupSpacing: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitalGroupSpacing.three,
  digitGroupSeparator: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.comma,
  divisorWhenUnfocused: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.divisorWhenUnfocused.none,
  emptyInputBehavior: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.emptyInputBehavior.focus,
  eventBubbles: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.eventBubbles.bubbles,
  eventIsCancelable: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.eventIsCancelable.isCancelable,
  failOnUnknownOption: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.failOnUnknownOption.ignore,
  formatOnPageLoad: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.formatOnPageLoad.format,
  formulaMode: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.formulaMode.disabled,
  historySize: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.historySize.medium,
  invalidClass: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.invalidClass,
  isCancellable: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.isCancellable.cancellable,
  leadingZero: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.leadingZero.deny,
  maximumValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.maximumValue.tenTrillions,
  minimumValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.minimumValue.tenTrillions,
  modifyValueOnWheel: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.modifyValueOnWheel.modifyValue,
  negativeBracketsTypeOnBlur: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativeBracketsTypeOnBlur.none,
  negativePositiveSignPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativePositiveSignPlacement.none,
  negativeSignCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativeSignCharacter.hyphen,
  noEventListeners: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.noEventListeners.addEvents,
  
  onInvalidPaste: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.onInvalidPaste.error,
  outputFormat: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.outputFormat.none,
  overrideMinMaxLimits: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.overrideMinMaxLimits.doNotOverride,
  positiveSignCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.positiveSignCharacter.plus,
  rawValueDivisor: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.rawValueDivisor.none,
  readOnly: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.readOnly.readWrite,
  roundingMethod: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.roundingMethod.halfUpSymmetric,
  saveValueToSessionStorage: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.saveValueToSessionStorage.doNotSave,
  selectNumberOnly: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.selectNumberOnly.selectNumbersOnly,
  selectOnFocus: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.selectOnFocus.select,
  serializeSpaces: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.serializeSpaces.plus,
  showOnlyNumbersOnFocus: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.showOnlyNumbersOnFocus.showAll,
  showPositiveSign: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.showPositiveSign.hide,
  showWarnings: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.showWarnings.show,
  styleRules: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.styleRules.none,
  suffixText: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.suffixText.none,
  symbolWhenUnfocused: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.symbolWhenUnfocused.none,
  unformatOnHover: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.unformatOnHover.unformat,
  unformatOnSubmit: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.unformatOnSubmit.keepCurrentValue,
  valuesToStrings: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.valuesToStrings.none,
  watchExternalChanges: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.watchExternalChanges.doNotWatch,
  wheelOn: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.wheelOn.focus,
  wheelStep: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.wheelStep.progressive
};
Object.freeze(_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].defaultSettings);
Object.defineProperty(_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"], 'defaultSettings', {
  configurable: false,
  writable: false
});

 }),

 "./src/AutoNumericEnum.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);



var AutoNumericEnum = {};


AutoNumericEnum.allowedTagList = ['b', 'caption', 'cite', 'code', 'const', 'dd', 'del', 'div', 'dfn', 'dt', 'em', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'input', 'ins', 'kdb', 'label', 'li', 'option', 'output', 'p', 'q', 's', 'sample', 'span', 'strong', 'td', 'th', 'u'];
Object.freeze(AutoNumericEnum.allowedTagList);
Object.defineProperty(AutoNumericEnum, 'allowedTagList', {
  configurable: false,
  writable: false
});


AutoNumericEnum.keyCode = {
  Backspace: 8,
  Tab: 9,
  
  
  
  Enter: 13,
  
  
  Shift: 16,
  Ctrl: 17,
  Alt: 18,
  Pause: 19,
  CapsLock: 20,
  
  
  Esc: 27,
  
  Space: 32,
  PageUp: 33,
  PageDown: 34,
  End: 35,
  Home: 36,
  LeftArrow: 37,
  UpArrow: 38,
  RightArrow: 39,
  DownArrow: 40,
  Insert: 45,
  Delete: 46,
  num0: 48,
  num1: 49,
  num2: 50,
  num3: 51,
  num4: 52,
  num5: 53,
  num6: 54,
  num7: 55,
  num8: 56,
  num9: 57,
  a: 65,
  b: 66,
  c: 67,
  d: 68,
  e: 69,
  f: 70,
  g: 71,
  h: 72,
  i: 73,
  j: 74,
  k: 75,
  l: 76,
  m: 77,
  n: 78,
  o: 79,
  p: 80,
  q: 81,
  r: 82,
  s: 83,
  t: 84,
  u: 85,
  v: 86,
  w: 87,
  x: 88,
  y: 89,
  z: 90,
  OSLeft: 91,
  OSRight: 92,
  ContextMenu: 93,
  numpad0: 96,
  numpad1: 97,
  numpad2: 98,
  numpad3: 99,
  numpad4: 100,
  numpad5: 101,
  numpad6: 102,
  numpad7: 103,
  numpad8: 104,
  numpad9: 105,
  MultiplyNumpad: 106,
  PlusNumpad: 107,
  MinusNumpad: 109,
  DotNumpad: 110,
  SlashNumpad: 111,
  F1: 112,
  F2: 113,
  F3: 114,
  F4: 115,
  F5: 116,
  F6: 117,
  F7: 118,
  F8: 119,
  F9: 120,
  F10: 121,
  F11: 122,
  F12: 123,
  NumLock: 144,
  ScrollLock: 145,
  HyphenFirefox: 173,
  
  MyComputer: 182,
  MyCalculator: 183,
  Semicolon: 186,
  Equal: 187,
  Comma: 188,
  Hyphen: 189,
  Dot: 190,
  Slash: 191,
  Backquote: 192,
  LeftBracket: 219,
  Backslash: 220,
  RightBracket: 221,
  Quote: 222,
  Command: 224,
  AltGraph: 225,
  AndroidDefault: 229 

};
Object.freeze(AutoNumericEnum.keyCode);
Object.defineProperty(AutoNumericEnum, 'keyCode', {
  configurable: false,
  writable: false
});


AutoNumericEnum.fromCharCodeKeyCode = {
  0: 'LaunchCalculator',
  8: 'Backspace',
  9: 'Tab',
  13: 'Enter',
  16: 'Shift',
  17: 'Ctrl',
  18: 'Alt',
  19: 'Pause',
  20: 'CapsLock',
  27: 'Escape',
  32: ' ',
  33: 'PageUp',
  34: 'PageDown',
  35: 'End',
  36: 'Home',
  37: 'ArrowLeft',
  38: 'ArrowUp',
  39: 'ArrowRight',
  40: 'ArrowDown',
  45: 'Insert',
  46: 'Delete',
  48: '0',
  49: '1',
  50: '2',
  51: '3',
  52: '4',
  53: '5',
  54: '6',
  55: '7',
  56: '8',
  57: '9',
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  91: 'OS',
  
  92: 'OSRight',
  93: 'ContextMenu',
  96: '0',
  97: '1',
  98: '2',
  99: '3',
  100: '4',
  101: '5',
  102: '6',
  103: '7',
  104: '8',
  105: '9',
  106: '*',
  107: '+',
  109: '-',
  
  110: '.',
  111: '/',
  112: 'F1',
  113: 'F2',
  114: 'F3',
  115: 'F4',
  116: 'F5',
  117: 'F6',
  118: 'F7',
  119: 'F8',
  120: 'F9',
  121: 'F10',
  122: 'F11',
  123: 'F12',
  144: 'NumLock',
  145: 'ScrollLock',
  173: '-',
  
  182: 'MyComputer',
  183: 'MyCalculator',
  186: ';',
  187: '=',
  188: ',',
  189: '-',
  
  190: '.',
  191: '/',
  192: '`',
  219: '[',
  220: '\\',
  221: ']',
  222: '\'',
  224: 'Meta',
  225: 'AltGraph'
};
Object.freeze(AutoNumericEnum.fromCharCodeKeyCode);
Object.defineProperty(AutoNumericEnum, 'fromCharCodeKeyCode', {
  configurable: false,
  writable: false
});


AutoNumericEnum.keyName = {
  
  Unidentified: 'Unidentified',
  AndroidDefault: 'AndroidDefault',
  
  Alt: 'Alt',
  AltGr: 'AltGraph',
  CapsLock: 'CapsLock',
  
  Ctrl: 'Control',
  Fn: 'Fn',
  FnLock: 'FnLock',
  Hyper: 'Hyper',
  
  Meta: 'Meta',
  OSLeft: 'OS',
  OSRight: 'OS',
  Command: 'OS',
  NumLock: 'NumLock',
  ScrollLock: 'ScrollLock',
  Shift: 'Shift',
  Super: 'Super',
  
  Symbol: 'Symbol',
  SymbolLock: 'SymbolLock',
  
  Enter: 'Enter',
  Tab: 'Tab',
  Space: ' ',
  
  
  LeftArrow: 'ArrowLeft',
  
  UpArrow: 'ArrowUp',
  
  RightArrow: 'ArrowRight',
  
  DownArrow: 'ArrowDown',
  
  End: 'End',
  Home: 'Home',
  PageUp: 'PageUp',
  PageDown: 'PageDown',
  
  Backspace: 'Backspace',
  Clear: 'Clear',
  Copy: 'Copy',
  CrSel: 'CrSel',
  
  Cut: 'Cut',
  Delete: 'Delete',
  
  EraseEof: 'EraseEof',
  ExSel: 'ExSel',
  
  Insert: 'Insert',
  Paste: 'Paste',
  Redo: 'Redo',
  Undo: 'Undo',
  
  Accept: 'Accept',
  Again: 'Again',
  Attn: 'Attn',
  
  Cancel: 'Cancel',
  ContextMenu: 'ContextMenu',
  
  Esc: 'Escape',
  
  Execute: 'Execute',
  Find: 'Find',
  Finish: 'Finish',
  
  Help: 'Help',
  Pause: 'Pause',
  Play: 'Play',
  Props: 'Props',
  Select: 'Select',
  ZoomIn: 'ZoomIn',
  ZoomOut: 'ZoomOut',
  
  BrightnessDown: 'BrightnessDown',
  BrightnessUp: 'BrightnessUp',
  Eject: 'Eject',
  LogOff: 'LogOff',
  Power: 'Power',
  PowerOff: 'PowerOff',
  PrintScreen: 'PrintScreen',
  Hibernate: 'Hibernate',
  
  Standby: 'Standby',
  
  WakeUp: 'WakeUp',
  
  Compose: 'Compose',
  Dead: 'Dead',
  
  F1: 'F1',
  F2: 'F2',
  F3: 'F3',
  F4: 'F4',
  F5: 'F5',
  F6: 'F6',
  F7: 'F7',
  F8: 'F8',
  F9: 'F9',
  F10: 'F10',
  F11: 'F11',
  F12: 'F12',
  
  Print: 'Print',
  
  num0: '0',
  num1: '1',
  num2: '2',
  num3: '3',
  num4: '4',
  num5: '5',
  num6: '6',
  num7: '7',
  num8: '8',
  num9: '9',
  a: 'a',
  b: 'b',
  c: 'c',
  d: 'd',
  e: 'e',
  f: 'f',
  g: 'g',
  h: 'h',
  i: 'i',
  j: 'j',
  k: 'k',
  l: 'l',
  m: 'm',
  n: 'n',
  o: 'o',
  p: 'p',
  q: 'q',
  r: 'r',
  s: 's',
  t: 't',
  u: 'u',
  v: 'v',
  w: 'w',
  x: 'x',
  y: 'y',
  z: 'z',
  A: 'A',
  B: 'B',
  C: 'C',
  D: 'D',
  E: 'E',
  F: 'F',
  G: 'G',
  H: 'H',
  I: 'I',
  J: 'J',
  K: 'K',
  L: 'L',
  M: 'M',
  N: 'N',
  O: 'O',
  P: 'P',
  Q: 'Q',
  R: 'R',
  S: 'S',
  T: 'T',
  U: 'U',
  V: 'V',
  W: 'W',
  X: 'X',
  Y: 'Y',
  Z: 'Z',
  Semicolon: ';',
  Equal: '=',
  Comma: ',',
  Hyphen: '-',
  Minus: '-',
  Plus: '+',
  Dot: '.',
  Slash: '/',
  Backquote: '`',
  LeftParenthesis: '(',
  RightParenthesis: ')',
  LeftBracket: '[',
  RightBracket: ']',
  Backslash: '\\',
  Quote: '\'',
  
  numpad0: '0',
  numpad1: '1',
  numpad2: '2',
  numpad3: '3',
  numpad4: '4',
  numpad5: '5',
  numpad6: '6',
  numpad7: '7',
  numpad8: '8',
  numpad9: '9',
  NumpadDot: '.',
  NumpadDotAlt: ',',
  
  NumpadMultiply: '*',
  NumpadPlus: '+',
  NumpadMinus: '-',
  NumpadSubtract: '-',
  NumpadSlash: '/',
  NumpadDotObsoleteBrowsers: 'Decimal',
  NumpadMultiplyObsoleteBrowsers: 'Multiply',
  NumpadPlusObsoleteBrowsers: 'Add',
  NumpadMinusObsoleteBrowsers: 'Subtract',
  NumpadSlashObsoleteBrowsers: 'Divide',
  
  _allFnKeys: ['F1', 'F2', 'F3', 'F4', 'F5', 'F6', 'F7', 'F8', 'F9', 'F10', 'F11', 'F12'],
  _someNonPrintableKeys: ['Tab', 'Enter', 'Shift', 'ShiftLeft', 'ShiftRight', 'Control', 'ControlLeft', 'ControlRight', 'Alt', 'AltLeft', 'AltRight', 'Pause', 'CapsLock', 'Escape'],
  _directionKeys: ['PageUp', 'PageDown', 'End', 'Home', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowUp']
};
Object.freeze(AutoNumericEnum.keyName._allFnKeys);
Object.freeze(AutoNumericEnum.keyName._someNonPrintableKeys);
Object.freeze(AutoNumericEnum.keyName._directionKeys);
Object.freeze(AutoNumericEnum.keyName);
Object.defineProperty(AutoNumericEnum, 'keyName', {
  configurable: false,
  writable: false
});
Object.freeze(AutoNumericEnum);
 __webpack_exports__["default"] = (AutoNumericEnum);

 }),

 "./src/AutoNumericEvents.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
 var _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__( "./src/AutoNumeric.js");




_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].events = {
  correctedValue: 'autoNumeric:correctedValue',
  initialized: 'autoNumeric:initialized',
  invalidFormula: 'autoNumeric:invalidFormula',
  invalidValue: 'autoNumeric:invalidValue',
  formatted: 'autoNumeric:formatted',
  rawValueModified: 'autoNumeric:rawValueModified',
  minRangeExceeded: 'autoNumeric:minExceeded',
  maxRangeExceeded: 'autoNumeric:maxExceeded',
  "native": {
    input: 'input',
    change: 'change'
  },
  validFormula: 'autoNumeric:validFormula'
};
Object.freeze(_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].events["native"]);
Object.freeze(_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].events);
Object.defineProperty(_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"], 'events', {
  configurable: false,
  writable: false
});

 }),

 "./src/AutoNumericHelper.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
 __webpack_require__.d(__webpack_exports__, "default", function() { return AutoNumericHelper; });
 var _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__( "./src/AutoNumericEnum.js");
function _toConsumableArray(arr) { return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray(arr) || _nonIterableSpread(); }

function _nonIterableSpread() { throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _iterableToArray(iter) { if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter); }

function _arrayWithoutHoles(arr) { if (Array.isArray(arr)) return _arrayLikeToArray(arr); }

function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _slicedToArray(arr, i) { return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray(arr, i) || _nonIterableRest(); }

function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(n); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function _iterableToArrayLimit(arr, i) { if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return; var _arr = []; var _n = true; var _d = false; var _e = undefined; try { for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) { _arr.push(_s.value); if (i && _arr.length === i) break; } } catch (err) { _d = true; _e = err; } finally { try { if (!_n && _i["return"] != null) _i["return"](); } finally { if (_d) throw _e; } } return _arr; }

function _arrayWithHoles(arr) { if (Array.isArray(arr)) return arr; }

function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }





var AutoNumericHelper = function () {
  function AutoNumericHelper() {
    _classCallCheck(this, AutoNumericHelper);
  }

  _createClass(AutoNumericHelper, null, [{
    key: "isNull",

    
    value: function isNull(value) {
      return value === null;
    }
    

  }, {
    key: "isUndefined",
    value: function isUndefined(value) {
      return value === void 0;
    }
    

  }, {
    key: "isUndefinedOrNullOrEmpty",
    value: function isUndefinedOrNullOrEmpty(value) {
      return value === null || value === void 0 || '' === value;
    }
    

  }, {
    key: "isString",
    value: function isString(str) {
      return typeof str === 'string' || str instanceof String;
    }
    

  }, {
    key: "isEmptyString",
    value: function isEmptyString(value) {
      return value === '';
    }
    

  }, {
    key: "isBoolean",
    value: function isBoolean(value) {
      return typeof value === 'boolean';
    }
    

  }, {
    key: "isTrueOrFalseString",
    value: function isTrueOrFalseString(value) {
      var lowercaseValue = String(value).toLowerCase();
      return lowercaseValue === 'true' || lowercaseValue === 'false';
    }
    

  }, {
    key: "isObject",
    value: function isObject(reference) {
      return _typeof(reference) === 'object' && reference !== null && !Array.isArray(reference);
    }
    

  }, {
    key: "isEmptyObj",
    value: function isEmptyObj(obj) {
      for (var prop in obj) {
        if (Object.prototype.hasOwnProperty.call(obj, prop)) {
          return false;
        }
      }

      return true;
    }
    

  }, {
    key: "isNumberStrict",
    value: function isNumberStrict(n) {
      return typeof n === 'number';
    }
    

  }, {
    key: "isNumber",
    value: function isNumber(n) {
      return !this.isArray(n) && !isNaN(parseFloat(n)) && isFinite(n);
    }
    

  }, {
    key: "isDigit",
    value: function isDigit(_char) {
      return /\d/.test(_char);
    }
    

  }, {
    key: "isNumberOrArabic",
    value: function isNumberOrArabic(n) {
      var latinConvertedNumber = this.arabicToLatinNumbers(n, false, true, true);
      return this.isNumber(latinConvertedNumber);
    }
    

  }, {
    key: "isInt",
    value: function isInt(n) {
      return typeof n === 'number' && parseFloat(n) === parseInt(n, 10) && !isNaN(n);
    }
    

  }, {
    key: "isFunction",
    value: function isFunction(func) {
      return typeof func === 'function';
    }
    

  }, {
    key: "isIE11",
    value: function isIE11() {
      
      return typeof window !== 'undefined' && !!window.MSInputMethodContext && !!document.documentMode;
    }
    

  }, {
    key: "contains",
    value: function contains(str, needle) {
      
      if (!this.isString(str) || !this.isString(needle) || str === '' || needle === '') {
        return false;
      }

      return str.indexOf(needle) !== -1;
    }
    

  }, {
    key: "isInArray",
    value: function isInArray(needle, array) {
      if (!this.isArray(array) || array === [] || this.isUndefined(needle)) {
        return false;
      }

      return array.indexOf(needle) !== -1;
    }
    

  }, {
    key: "isArray",
    value: function isArray(arr) {
      if (Object.prototype.toString.call([]) === '[object Array]') {
        
        
        return Array.isArray(arr) || _typeof(arr) === 'object' && Object.prototype.toString.call(arr) === '[object Array]';
      } else {
        throw new Error('toString message changed for Object Array'); 
      }
    }
    

  }, {
    key: "isElement",
    value: function isElement(obj) {
      
      
      
      if (typeof Element === 'undefined') {
        
        return false;
      }

      return obj instanceof Element;
    }
    

  }, {
    key: "isInputElement",
    value: function isInputElement(domElement) {
      return this.isElement(domElement) && domElement.tagName.toLowerCase() === 'input';
    }
    
    
    
    
    

    

  }, {
    key: "decimalPlaces",
    value: function decimalPlaces(str) {
      var _str$split = str.split('.'),
          _str$split2 = _slicedToArray(_str$split, 2),
          decimalPart = _str$split2[1];

      if (!this.isUndefined(decimalPart)) {
        return decimalPart.length;
      }

      return 0;
    }
    

  }, {
    key: "indexFirstNonZeroDecimalPlace",
    value: function indexFirstNonZeroDecimalPlace(value) {
      var _String$split = String(Math.abs(value)).split('.'),
          _String$split2 = _slicedToArray(_String$split, 2),
          decimalPart = _String$split2[1];

      if (this.isUndefined(decimalPart)) {
        return 0;
      }

      var result = decimalPart.lastIndexOf('0');

      if (result === -1) {
        result = 0;
      } else {
        result += 2;
      }

      return result;
    }
    

  }, {
    key: "keyCodeNumber",
    value: function keyCodeNumber(event) {
      
      
      return typeof event.which === 'undefined' ? event.keyCode : event.which;
    }
    

  }, {
    key: "character",
    value: function character(event) {
      var result;

      if (event.key === 'Unidentified' || event.key === void 0 || this.isSeleniumBot()) {
        
        
        var keyCode = this.keyCodeNumber(event);

        if (keyCode === _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyCode.AndroidDefault) {
          return _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.AndroidDefault;
        }

        var potentialResult = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].fromCharCodeKeyCode[keyCode];

        if (!AutoNumericHelper.isUndefinedOrNullOrEmpty(potentialResult)) {
          
          result = potentialResult;
        } else {
          result = String.fromCharCode(keyCode);
        }
      } else {
        var browser;

        switch (event.key) {
          
          case 'Add':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.NumpadPlus;
            break;

          case 'Apps':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.ContextMenu;
            break;

          case 'Crsel':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.CrSel;
            break;

          case 'Decimal':
            if (event["char"]) {
              
              result = event["char"];
            } else {
              result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.NumpadDot;
            }

            break;

          case 'Del':
            browser = this.browser();

            if (browser.name === 'firefox' && browser.version <= 36 || browser.name === 'ie' && browser.version <= 9) {
              
              
              result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.Dot;
            } else {
              result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.Delete;
            }

            break;

          case 'Divide':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.NumpadSlash;
            break;

          case 'Down':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.DownArrow;
            break;

          case 'Esc':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.Esc;
            break;

          case 'Exsel':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.ExSel;
            break;

          case 'Left':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.LeftArrow;
            break;

          case 'Meta':
          case 'Super':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.OSLeft;
            break;

          case 'Multiply':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.NumpadMultiply;
            break;

          case 'Right':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.RightArrow;
            break;

          case 'Spacebar':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.Space;
            break;

          case 'Subtract':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.NumpadMinus;
            break;

          case 'Up':
            result = _AutoNumericEnum__WEBPACK_IMPORTED_MODULE_0__["default"].keyName.UpArrow;
            break;

          default:
            
            result = event.key;
        }
      }

      return result;
    }
    

  }, {
    key: "browser",
    value: function browser() {
      var ua = navigator.userAgent;
      var tem;
      var M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];

      if (/trident/i.test(M[1])) {
        tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
        return {
          name: 'ie',
          version: tem[1] || ''
        };
      }

      if (M[1] === 'Chrome') {
        tem = ua.match(/\b(OPR|Edge)\/(\d+)/);

        if (tem !== null) {
          return {
            name: tem[1].replace('OPR', 'opera'),
            version: tem[2]
          };
        }
      }

      M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];

      if ((tem = ua.match(/version\/(\d+)/i)) !== null) {
        M.splice(1, 1, tem[1]);
      }

      return {
        name: M[0].toLowerCase(),
        version: M[1]
      };
    }
    

  }, {
    key: "isSeleniumBot",
    value: function isSeleniumBot() {
      
      return window.navigator.webdriver === true;
    }
    

  }, {
    key: "isNegative",
    value: function isNegative(numberOrNumericString) {
      var negativeSignCharacter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '-';
      var checkEverywhere = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;

      if (numberOrNumericString === negativeSignCharacter) {
        return true;
      }

      if (numberOrNumericString === '') {
        return false;
      }

      if (AutoNumericHelper.isNumber(numberOrNumericString)) {
        return numberOrNumericString < 0;
      }

      if (checkEverywhere) {
        return this.contains(numberOrNumericString, negativeSignCharacter);
      }

      return this.isNegativeStrict(numberOrNumericString, negativeSignCharacter);
    }
    

  }, {
    key: "isNegativeStrict",
    value: function isNegativeStrict(numericString) {
      var negativeSignCharacter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '-';
      return numericString.charAt(0) === negativeSignCharacter;
    }
    

  }, {
    key: "isNegativeWithBrackets",
    value: function isNegativeWithBrackets(valueString, leftBracket, rightBracket) {
      return valueString.charAt(0) === leftBracket && this.contains(valueString, rightBracket);
    }
    

  }, {
    key: "isZeroOrHasNoValue",
    value: function isZeroOrHasNoValue(numericString) {
      return !/[1-9]/g.test(numericString);
    }
    

  }, {
    key: "setRawNegativeSign",
    value: function setRawNegativeSign(value) {
      if (!this.isNegativeStrict(value, '-')) {
        return "-".concat(value);
      }

      return value;
    }
    

  }, {
    key: "replaceCharAt",
    value: function replaceCharAt(string, index, newCharacter) {
      return "".concat(string.substr(0, index)).concat(newCharacter).concat(string.substr(index + newCharacter.length));
    }
    

  }, {
    key: "clampToRangeLimits",
    value: function clampToRangeLimits(value, settings) {
      
      return Math.max(settings.minimumValue, Math.min(settings.maximumValue, value));
    }
    

  }, {
    key: "countNumberCharactersOnTheCaretLeftSide",
    value: function countNumberCharactersOnTheCaretLeftSide(formattedNumberString, caretPosition, decimalCharacter) {
      
      var numberDotOrNegativeSign = new RegExp("[0-9".concat(decimalCharacter, "-]")); 

      var numberDotAndNegativeSignCount = 0;

      for (var i = 0; i < caretPosition; i++) {
        
        if (numberDotOrNegativeSign.test(formattedNumberString[i])) {
          numberDotAndNegativeSignCount++;
        }
      }

      return numberDotAndNegativeSignCount;
    }
    

  }, {
    key: "findCaretPositionInFormattedNumber",
    value: function findCaretPositionInFormattedNumber(rawNumberString, caretPositionInRawValue, formattedNumberString, decimalCharacter) {
      var formattedNumberStringSize = formattedNumberString.length;
      var rawNumberStringSize = rawNumberString.length;
      var formattedNumberStringIndex;
      var rawNumberStringIndex = 0;

      for (formattedNumberStringIndex = 0; formattedNumberStringIndex < formattedNumberStringSize && rawNumberStringIndex < rawNumberStringSize && rawNumberStringIndex < caretPositionInRawValue; formattedNumberStringIndex++) {
        if (rawNumberString[rawNumberStringIndex] === formattedNumberString[formattedNumberStringIndex] || rawNumberString[rawNumberStringIndex] === '.' && formattedNumberString[formattedNumberStringIndex] === decimalCharacter) {
          rawNumberStringIndex++;
        }
      }

      return formattedNumberStringIndex;
    }
    

  }, {
    key: "countCharInText",
    value: function countCharInText(character, text) {
      var charCounter = 0;

      for (var i = 0; i < text.length; i++) {
        if (text[i] === character) {
          charCounter++;
        }
      }

      return charCounter;
    }
    

  }, {
    key: "convertCharacterCountToIndexPosition",
    value: function convertCharacterCountToIndexPosition(characterCount) {
      return Math.max(characterCount, characterCount - 1);
    }
    

  }, {
    key: "getElementSelection",
    value: function getElementSelection(element) {
      var position = {};
      var isSelectionStartUndefined;

      try {
        isSelectionStartUndefined = this.isUndefined(element.selectionStart);
      } catch (error) {
        isSelectionStartUndefined = false;
      }

      try {
        if (isSelectionStartUndefined) {
          var selection = window.getSelection();
          var selectionInfo = selection.getRangeAt(0);
          position.start = selectionInfo.startOffset;
          position.end = selectionInfo.endOffset;
          position.length = position.end - position.start;
        } else {
          position.start = element.selectionStart;
          position.end = element.selectionEnd;
          position.length = position.end - position.start;
        }
      } catch (error) {
        
        
        
        position.start = 0;
        position.end = 0;
        position.length = 0;
      }

      return position;
    }
    

  }, {
    key: "setElementSelection",
    value: function setElementSelection(element, start) {
      var end = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;

      if (this.isUndefinedOrNullOrEmpty(end)) {
        end = start;
      }

      if (this.isInputElement(element)) {
        element.setSelectionRange(start, end);
      } else if (!AutoNumericHelper.isNull(element.firstChild)) {
        var range = document.createRange();
        range.setStart(element.firstChild, start);
        range.setEnd(element.firstChild, end);
        var selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);
      }
    }
    

  }, {
    key: "throwError",
    value: function throwError(message) {
      throw new Error(message);
    }
    

  }, {
    key: "warning",
    value: function warning(message) {
      var showWarning = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

      if (showWarning) {
        
        console.warn("Warning: ".concat(message));
      }
    }
    

  }, {
    key: "isWheelUpEvent",
    value: function isWheelUpEvent(wheelEvent) {
      if (!wheelEvent.deltaY) {
        this.throwError("The event passed as a parameter is not a valid wheel event, '".concat(wheelEvent.type, "' given."));
      }

      return wheelEvent.deltaY < 0;
    }
    

  }, {
    key: "isWheelDownEvent",
    value: function isWheelDownEvent(wheelEvent) {
      if (!wheelEvent.deltaY) {
        this.throwError("The event passed as a parameter is not a valid wheel event, '".concat(wheelEvent.type, "' given."));
      }

      return wheelEvent.deltaY > 0;
    }
    

  }, {
    key: "forceDecimalPlaces",
    value: function forceDecimalPlaces(value, decimalPlaces) {
      
      var _String$split3 = String(value).split('.'),
          _String$split4 = _slicedToArray(_String$split3, 2),
          integerPart = _String$split4[0],
          decimalPart = _String$split4[1];

      if (!decimalPart) {
        return value;
      }

      return "".concat(integerPart, ".").concat(decimalPart.substr(0, decimalPlaces));
    }
    

  }, {
    key: "roundToNearest",
    value: function roundToNearest(value) {
      var stepPlace = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 1000;

      if (0 === value) {
        return 0;
      }

      if (stepPlace === 0) {
        this.throwError('The `stepPlace` used to round is equal to `0`. This value must not be equal to zero.');
      }

      return Math.round(value / stepPlace) * stepPlace;
    }
    

  }, {
    key: "modifyAndRoundToNearestAuto",
    value: function modifyAndRoundToNearestAuto(value, isAddition, decimalPlacesRawValue) {
      value = Number(this.forceDecimalPlaces(value, decimalPlacesRawValue)); 

      var absValue = Math.abs(value);

      if (absValue >= 0 && absValue < 1) {
        var rawValueMinimumOffset = Math.pow(10, -decimalPlacesRawValue);

        if (value === 0) {
          
          return isAddition ? rawValueMinimumOffset : -rawValueMinimumOffset;
        }

        var offset;
        var minimumOffsetFirstDecimalPlaceIndex = decimalPlacesRawValue; 

        var indexFirstNonZeroDecimalPlace = this.indexFirstNonZeroDecimalPlace(value);

        if (indexFirstNonZeroDecimalPlace >= minimumOffsetFirstDecimalPlaceIndex - 1) {
          
          offset = rawValueMinimumOffset;
        } else {
          offset = Math.pow(10, -(indexFirstNonZeroDecimalPlace + 1));
        }

        var result;

        if (isAddition) {
          result = value + offset;
        } else {
          result = value - offset;
        }

        return this.roundToNearest(result, offset);
      } else {
        
        value = parseInt(value, 10);
        var lengthValue = Math.abs(value).toString().length; 

        var pow;

        switch (lengthValue) {
          
          case 1:
            pow = 0;
            break;

          case 2:
          case 3:
            pow = 1;
            break;

          case 4:
          case 5:
            pow = 2;
            break;
          

          default:
            pow = lengthValue - 3;
        }

        var _offset = Math.pow(10, pow);

        var _result;

        if (isAddition) {
          _result = value + _offset;
        } else {
          _result = value - _offset;
        }

        if (_result <= 10 && _result >= -10) {
          return _result;
        }

        return this.roundToNearest(_result, _offset);
      }
    }
    

  }, {
    key: "addAndRoundToNearestAuto",
    value: function addAndRoundToNearestAuto(value, decimalPlacesLimit) {
      return this.modifyAndRoundToNearestAuto(value, true, decimalPlacesLimit);
    }
    

  }, {
    key: "subtractAndRoundToNearestAuto",
    value: function subtractAndRoundToNearestAuto(value, decimalPlacesLimit) {
      return this.modifyAndRoundToNearestAuto(value, false, decimalPlacesLimit);
    }
    

  }, {
    key: "arabicToLatinNumbers",
    value: function arabicToLatinNumbers(arabicNumbers) {
      var returnANumber = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
      var parseDecimalCharacter = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
      var parseThousandSeparator = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;

      if (this.isNull(arabicNumbers)) {
        return arabicNumbers;
      }

      var result = arabicNumbers.toString();

      if (result === '') {
        return arabicNumbers;
      }

      if (result.match(/[٠١٢٣٤٥٦٧٨٩۴۵۶]/g) === null) {
        
        if (returnANumber) {
          result = Number(result);
        }

        return result;
      }

      if (parseDecimalCharacter) {
        result = result.replace(/٫/, '.'); 
      }

      if (parseThousandSeparator) {
        result = result.replace(/٬/g, ''); 
      } 


      result = result.replace(/[٠١٢٣٤٥٦٧٨٩]/g, function (d) {
        return d.charCodeAt(0) - 1632;
      }) 
      .replace(/[۰۱۲۳۴۵۶۷۸۹]/g, function (d) {
        return d.charCodeAt(0) - 1776;
      }); 
      

      var resultAsNumber = Number(result);

      if (isNaN(resultAsNumber)) {
        return resultAsNumber;
      }

      if (returnANumber) {
        result = resultAsNumber;
      }

      return result;
    }
    

  }, {
    key: "triggerEvent",
    value: function triggerEvent(eventName) {
      var element = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : document;
      var detail = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : null;
      var bubbles = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : true;
      var cancelable = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : true;
      var event;

      if (window.CustomEvent) {
        event = new CustomEvent(eventName, {
          detail: detail,
          bubbles: bubbles,
          cancelable: cancelable
        }); 
      } else {
        event = document.createEvent('CustomEvent');
        event.initCustomEvent(eventName, bubbles, cancelable, {
          detail: detail
        });
      }

      element.dispatchEvent(event);
    }
    

  }, {
    key: "parseStr",
    value: function parseStr(n) {
      var x = {}; 

      var e;
      var i;
      var nL;
      var j; 

      if (n === 0 && 1 / n < 0) {
        n = '-0';
      } 


      n = n.toString();

      if (this.isNegativeStrict(n, '-')) {
        n = n.slice(1);
        x.s = -1;
      } else {
        x.s = 1;
      } 


      e = n.indexOf('.');

      if (e > -1) {
        n = n.replace('.', '');
      } 


      if (e < 0) {
        
        e = n.length;
      } 


      i = n.search(/[1-9]/i) === -1 ? n.length : n.search(/[1-9]/i);
      nL = n.length;

      if (i === nL) {
        
        x.e = 0;
        x.c = [0];
      } else {
        
        for (j = nL - 1; n.charAt(j) === '0'; j -= 1) {
          nL -= 1;
        }

        nL -= 1; 

        x.e = e - i - 1;
        x.c = []; 

        for (e = 0; i <= nL; i += 1) {
          x.c[e] = +n.charAt(i);
          e += 1;
        }
      }

      return x;
    }
    

  }, {
    key: "testMinMax",
    value: function testMinMax(y, x) {
      var xc = x.c;
      var yc = y.c;
      var i = x.s;
      var j = y.s;
      var k = x.e;
      var l = y.e; 

      if (!xc[0] || !yc[0]) {
        var _result2;

        if (!xc[0]) {
          _result2 = !yc[0] ? 0 : -j;
        } else {
          _result2 = i;
        }

        return _result2;
      } 


      if (i !== j) {
        return i;
      }

      var xNeg = i < 0; 

      if (k !== l) {
        return k > l ^ xNeg ? 1 : -1;
      }

      i = -1;
      k = xc.length;
      l = yc.length;
      j = k < l ? k : l; 

      for (i += 1; i < j; i += 1) {
        if (xc[i] !== yc[i]) {
          return xc[i] > yc[i] ^ xNeg ? 1 : -1;
        }
      } 


      var result;

      if (k === l) {
        result = 0;
      } else {
        result = k > l ^ xNeg ? 1 : -1;
      }

      return result;
    }
    

  }, {
    key: "randomString",
    value: function randomString() {
      var strLength = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 5;
      return Math.random().toString(36).substr(2, strLength);
    }
    

  }, {
    key: "domElement",
    value: function domElement(domElementOrSelector) {
      var domElement;

      if (AutoNumericHelper.isString(domElementOrSelector)) {
        domElement = document.querySelector(domElementOrSelector);
      } else {
        domElement = domElementOrSelector;
      }

      return domElement;
    }
    

  }, {
    key: "getElementValue",
    value: function getElementValue(element) {
      if (element.tagName.toLowerCase() === 'input') {
        return element.value;
      }

      return this.text(element);
    }
    

  }, {
    key: "setElementValue",
    value: function setElementValue(element) {
      var value = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : null;

      if (element.tagName.toLowerCase() === 'input') {
        element.value = value;
      } else {
        element.textContent = value;
      }
    }
    

  }, {
    key: "setInvalidState",
    value: function setInvalidState(element) {
      var message = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'Invalid';
      if (message === '' || this.isNull(message)) this.throwError('Cannot set the invalid state with an empty message.');
      element.setCustomValidity(message);
    }
    

  }, {
    key: "setValidState",
    value: function setValidState(element) {
      element.setCustomValidity('');
    }
    

  }, {
    key: "cloneObject",
    value: function cloneObject(obj) {
      return _extends({}, obj);
    }
    

  }, {
    key: "camelize",
    value: function camelize(str) {
      var separator = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '-';
      var removeData = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : true;
      var skipFirstWord = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : true;

      if (this.isNull(str)) {
        return null;
      }

      if (removeData) {
        str = str.replace(/^data-/, '');
      } 


      var words = str.split(separator); 

      var result = words.map(function (word) {
        return "".concat(word.charAt(0).toUpperCase()).concat(word.slice(1));
      }); 

      result = result.join('');

      if (skipFirstWord) {
        
        result = "".concat(result.charAt(0).toLowerCase()).concat(result.slice(1));
      }

      return result;
    }
    

  }, {
    key: "text",
    value: function text(domElement) {
      var nodeType = domElement.nodeType;
      var result; 

      if (nodeType === Node.ELEMENT_NODE || nodeType === Node.DOCUMENT_NODE || nodeType === Node.DOCUMENT_FRAGMENT_NODE) {
        result = domElement.textContent;
      } else if (nodeType === Node.TEXT_NODE) {
        result = domElement.nodeValue;
      } else {
        result = '';
      }

      return result;
    }
    

  }, {
    key: "setText",
    value: function setText(domElement, text) {
      var nodeType = domElement.nodeType;

      if (nodeType === Node.ELEMENT_NODE || nodeType === Node.DOCUMENT_NODE || nodeType === Node.DOCUMENT_FRAGMENT_NODE) {
        domElement.textContent = text;
      } 

    }
    

  }, {
    key: "filterOut",
    value: function filterOut(arr, excludedElements) {
      var _this = this;

      return arr.filter(function (element) {
        return !_this.isInArray(element, excludedElements);
      });
    }
    

  }, {
    key: "trimPaddedZerosFromDecimalPlaces",
    value: function trimPaddedZerosFromDecimalPlaces(numericString) {
      numericString = String(numericString);

      if (numericString === '') {
        return '';
      }

      var _numericString$split = numericString.split('.'),
          _numericString$split2 = _slicedToArray(_numericString$split, 2),
          integerPart = _numericString$split2[0],
          decimalPart = _numericString$split2[1];

      if (this.isUndefinedOrNullOrEmpty(decimalPart)) {
        return integerPart;
      }

      var trimmedDecimalPart = decimalPart.replace(/0+$/g, '');
      var result;

      if (trimmedDecimalPart === '') {
        result = integerPart;
      } else {
        result = "".concat(integerPart, ".").concat(trimmedDecimalPart);
      }

      return result;
    }
    

  }, {
    key: "getHoveredElement",
    value: function getHoveredElement() {
      var hoveredElements = _toConsumableArray(document.querySelectorAll(':hover'));

      return hoveredElements[hoveredElements.length - 1];
    }
    

  }, {
    key: "arrayTrim",
    value: function arrayTrim(array, length) {
      var arrLength = array.length;

      if (arrLength === 0 || length > arrLength) {
        
        return array;
      }

      if (length < 0) {
        return [];
      }

      array.length = parseInt(length, 10);
      return array;
    }
    

  }, {
    key: "arrayUnique",
    value: function arrayUnique() {
      var _ref;

      
      return _toConsumableArray(new Set((_ref = []).concat.apply(_ref, arguments)));
    }
    

  }, {
    key: "mergeMaps",
    value: function mergeMaps() {
      for (var _len = arguments.length, mapObjects = new Array(_len), _key = 0; _key < _len; _key++) {
        mapObjects[_key] = arguments[_key];
      }

      return new Map(mapObjects.reduce(function (as, b) {
        return as.concat(_toConsumableArray(b));
      }, []));
    }
    

  }, {
    key: "objectKeyLookup",
    value: function objectKeyLookup(obj, value) {
      var result = Object.entries(obj).find(function (array) {
        return array[1] === value;
      });
      var key = null;

      if (result !== void 0) {
        key = result[0];
      }

      return key;
    }
    

  }, {
    key: "insertAt",
    value: function insertAt(str, _char2, index) {
      str = String(str);

      if (index > str.length) {
        throw new Error("The given index is out of the string range.");
      }

      if (_char2.length !== 1) {
        throw new Error('The given string `char` should be only one character long.');
      }

      if (str === '' && index === 0) {
        return _char2;
      }

      return "".concat(str.slice(0, index)).concat(_char2).concat(str.slice(index));
    }
    

  }, {
    key: "scientificToDecimal",
    value: function scientificToDecimal(val) {
      
      var numericValue = Number(val);

      if (isNaN(numericValue)) {
        return NaN;
      } 


      val = String(val);
      var isScientific = this.contains(val, 'e') || this.contains(val, 'E');

      if (!isScientific) {
        return val;
      } 


      var _val$split = val.split(/e/i),
          _val$split2 = _slicedToArray(_val$split, 2),
          value = _val$split2[0],
          exponent = _val$split2[1];

      var isNegative = value < 0;

      if (isNegative) {
        value = value.replace('-', '');
      }

      var isNegativeExponent = +exponent < 0;

      if (isNegativeExponent) {
        exponent = exponent.replace('-', ''); 
      }

      var _value$split = value.split(/\./),
          _value$split2 = _slicedToArray(_value$split, 2),
          _int = _value$split2[0],
          _float = _value$split2[1];

      var result;

      if (isNegativeExponent) {
        if (_int.length > exponent) {
          
          result = this.insertAt(_int, '.', _int.length - exponent);
        } else {
          
          result = "0.".concat('0'.repeat(exponent - _int.length)).concat(_int);
        }

        result = "".concat(result).concat(_float ? _float : '');
      } else {
        
        if (_float) {
          value = "".concat(_int).concat(_float); 

          if (exponent < _float.length) {
            result = this.insertAt(value, '.', +exponent + _int.length);
          } else {
            result = "".concat(value).concat('0'.repeat(exponent - _float.length));
          }
        } else {
          value = value.replace('.', ''); 

          result = "".concat(value).concat('0'.repeat(Number(exponent)));
        }
      }

      if (isNegative) {
        
        result = "-".concat(result);
      }

      return result;
    }
  }]);

  return AutoNumericHelper;
}();



 }),

 "./src/AutoNumericOptions.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
 var _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__( "./src/AutoNumeric.js");
 var _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__( "./src/AutoNumericHelper.js");





_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options = {
  
  allowDecimalPadding: {
    always: true,
    never: false,
    floats: 'floats'
  },

  
  alwaysAllowDecimalCharacter: {
    alwaysAllow: true,
    doNotAllow: false
  },

  
  caretPositionOnFocus: {
    start: 'start',
    end: 'end',
    decimalLeft: 'decimalLeft',
    decimalRight: 'decimalRight',
    doNoForceCaretPosition: null
  },

  
  createLocalList: {
    createList: true,
    doNotCreateList: false
  },

  
  currencySymbol: {
    none: '',
    currencySign: '¤',
    austral: '₳',
    
    australCentavo: '¢',
    baht: '฿',
    
    cedi: '₵',
    
    cent: '¢',
    colon: '₡',
    
    cruzeiro: '₢',
    
    dollar: '$',
    dong: '₫',
    
    drachma: '₯',
    
    dram: '​֏',
    
    european: '₠',
    
    euro: '€',
    
    florin: 'ƒ',
    franc: '₣',
    
    guarani: '₲',
    
    hryvnia: '₴',
    
    kip: '₭',
    
    att: 'ອັດ',
    
    lepton: 'Λ.',
    
    lira: '₺',
    
    liraOld: '₤',
    lari: '₾',
    
    mark: 'ℳ',
    mill: '₥',
    naira: '₦',
    
    peseta: '₧',
    peso: '₱',
    
    pfennig: '₰',
    
    pound: '£',
    real: 'R$',
    
    riel: '៛',
    
    ruble: '₽',
    
    rupee: '₹',
    
    rupeeOld: '₨',
    shekel: '₪',
    shekelAlt: 'ש״ח‎‎',
    taka: '৳',
    
    tenge: '₸',
    
    togrog: '₮',
    
    won: '₩',
    yen: '¥'
  },

  
  currencySymbolPlacement: {
    prefix: 'p',
    suffix: 's'
  },

  
  decimalCharacter: {
    comma: ',',
    dot: '.',
    middleDot: '·',
    arabicDecimalSeparator: '٫',
    decimalSeparatorKeySymbol: '⎖'
  },

  
  decimalCharacterAlternative: {
    none: null,
    comma: ',',
    dot: '.'
  },

  
  decimalPlaces: {
    none: 0,
    one: 1,
    two: 2,
    three: 3,
    four: 4,
    five: 5,
    six: 6
  },

  
  decimalPlacesRawValue: {
    useDefault: null,
    none: 0,
    one: 1,
    two: 2,
    three: 3,
    four: 4,
    five: 5,
    six: 6
  },

  
  decimalPlacesShownOnBlur: {
    useDefault: null,
    none: 0,
    one: 1,
    two: 2,
    three: 3,
    four: 4,
    five: 5,
    six: 6
  },

  
  decimalPlacesShownOnFocus: {
    useDefault: null,
    none: 0,
    one: 1,
    two: 2,
    three: 3,
    four: 4,
    five: 5,
    six: 6
  },

  
  defaultValueOverride: {
    doNotOverride: null
  },

  
  digitalGroupSpacing: {
    two: '2',
    twoScaled: '2s',
    three: '3',
    four: '4'
  },

  
  digitGroupSeparator: {
    comma: ',',
    dot: '.',
    normalSpace: ' ',
    thinSpace: "\u2009",
    narrowNoBreakSpace: "\u202F",
    noBreakSpace: "\xA0",
    noSeparator: '',
    apostrophe: "'",
    arabicThousandsSeparator: '٬',
    dotAbove: '˙',
    privateUseTwo: '’' 

  },

  
  divisorWhenUnfocused: {
    none: null,
    percentage: 100,
    permille: 1000,
    basisPoint: 10000
  },

  
  emptyInputBehavior: {
    focus: 'focus',
    press: 'press',
    always: 'always',
    zero: 'zero',
    min: 'min',
    max: 'max',
    "null": 'null'
  },

  
  eventBubbles: {
    bubbles: true,
    doesNotBubble: false
  },

  
  eventIsCancelable: {
    isCancelable: true,
    isNotCancelable: false
  },

  
  failOnUnknownOption: {
    fail: true,
    ignore: false
  },

  
  formatOnPageLoad: {
    format: true,
    
    doNotFormat: false 

  },

  
  formulaMode: {
    enabled: true,
    disabled: false
  },

  
  historySize: {
    verySmall: 5,
    small: 10,
    medium: 20,
    large: 50,
    veryLarge: 100,
    insane: Number.MAX_SAFE_INTEGER
  },

  
  invalidClass: 'an-invalid',

  
  isCancellable: {
    cancellable: true,
    notCancellable: false
  },

  
  leadingZero: {
    allow: 'allow',
    deny: 'deny',
    keep: 'keep'
  },

  
  maximumValue: {
    tenTrillions: '10000000000000',
    
    oneBillion: '1000000000',
    zero: '0'
  },

  
  minimumValue: {
    tenTrillions: '-10000000000000',
    
    oneBillion: '-1000000000',
    zero: '0'
  },

  
  modifyValueOnWheel: {
    modifyValue: true,
    doNothing: false
  },

  
  negativeBracketsTypeOnBlur: {
    parentheses: '(,)',
    brackets: '[,]',
    chevrons: '<,>',
    curlyBraces: '{,}',
    angleBrackets: '〈,〉',
    japaneseQuotationMarks: '｢,｣',
    halfBrackets: '⸤,⸥',
    whiteSquareBrackets: '⟦,⟧',
    quotationMarks: '‹,›',
    guillemets: '«,»',
    none: null 

  },

  
  negativePositiveSignPlacement: {
    prefix: 'p',
    suffix: 's',
    left: 'l',
    right: 'r',
    none: null
  },

  
  negativeSignCharacter: {
    hyphen: '-',
    minus: '−',
    heavyMinus: '➖',
    fullWidthHyphen: '－',
    circledMinus: '⊖',
    squaredMinus: '⊟',
    triangleMinus: '⨺',
    plusMinus: '±',
    minusPlus: '∓',
    dotMinus: '∸',
    minusTilde: '≂',
    not: '¬'
  },

  
  noEventListeners: {
    noEvents: true,
    addEvents: false
  },

  
  onInvalidPaste: {
    error: 'error',
    ignore: 'ignore',
    clamp: 'clamp',
    truncate: 'truncate',
    replace: 'replace'
  },

  
  outputFormat: {
    string: 'string',
    number: 'number',
    dot: '.',
    negativeDot: '-.',
    comma: ',',
    negativeComma: '-,',
    dotNegative: '.-',
    commaNegative: ',-',
    none: null
  },

  
  overrideMinMaxLimits: {
    ceiling: 'ceiling',
    floor: 'floor',
    ignore: 'ignore',
    invalid: 'invalid',
    doNotOverride: null
  },

  
  positiveSignCharacter: {
    plus: '+',
    fullWidthPlus: '＋',
    heavyPlus: '➕',
    doublePlus: '⧺',
    triplePlus: '⧻',
    circledPlus: '⊕',
    squaredPlus: '⊞',
    trianglePlus: '⨹',
    plusMinus: '±',
    minusPlus: '∓',
    dotPlus: '∔',
    altHebrewPlus: '﬩',
    normalSpace: ' ',
    thinSpace: "\u2009",
    narrowNoBreakSpace: "\u202F",
    noBreakSpace: "\xA0"
  },

  
  rawValueDivisor: {
    none: null,
    percentage: 100,
    permille: 1000,
    basisPoint: 10000
  },

  
  readOnly: {
    readOnly: true,
    readWrite: false
  },

  
  roundingMethod: {
    halfUpSymmetric: 'S',
    halfUpAsymmetric: 'A',
    halfDownSymmetric: 's',
    halfDownAsymmetric: 'a',
    halfEvenBankersRounding: 'B',
    upRoundAwayFromZero: 'U',
    downRoundTowardZero: 'D',
    toCeilingTowardPositiveInfinity: 'C',
    toFloorTowardNegativeInfinity: 'F',
    toNearest05: 'N05',
    toNearest05Alt: 'CHF',
    upToNext05: 'U05',
    downToNext05: 'D05'
  },

  
  saveValueToSessionStorage: {
    save: true,
    doNotSave: false
  },

  
  selectNumberOnly: {
    selectNumbersOnly: true,
    selectAll: false
  },

  
  selectOnFocus: {
    select: true,
    doNotSelect: false
  },

  
  serializeSpaces: {
    plus: '+',
    percent: '%20'
  },

  
  showOnlyNumbersOnFocus: {
    onlyNumbers: true,
    showAll: false
  },

  
  showPositiveSign: {
    show: true,
    hide: false
  },

  
  showWarnings: {
    show: true,
    
    hide: false 

  },

  
  styleRules: {
    none: null,
    positiveNegative: {
      positive: 'autoNumeric-positive',
      negative: 'autoNumeric-negative'
    },
    range0To100With4Steps: {
      ranges: [{
        min: 0,
        max: 25,
        "class": 'autoNumeric-red'
      }, {
        min: 25,
        max: 50,
        "class": 'autoNumeric-orange'
      }, {
        min: 50,
        max: 75,
        "class": 'autoNumeric-yellow'
      }, {
        min: 75,
        max: 100,
        "class": 'autoNumeric-green'
      }]
    },
    evenOdd: {
      userDefined: [{
        callback: function callback(rawValue) {
          return rawValue % 2 === 0;
        },
        classes: ['autoNumeric-even', 'autoNumeric-odd']
      }]
    },
    rangeSmallAndZero: {
      userDefined: [{
        callback: function callback(rawValue) {
          if (rawValue >= -1 && rawValue < 0) {
            return 0;
          }

          if (Number(rawValue) === 0) {
            return 1;
          }

          if (rawValue > 0 && rawValue <= 1) {
            return 2;
          }

          return null; 
        },
        classes: ['autoNumeric-small-negative', 'autoNumeric-zero', 'autoNumeric-small-positive']
      }]
    }
  },

  
  suffixText: {
    none: '',
    percentage: '%',
    permille: '‰',
    basisPoint: '‱'
  },

  

  
  symbolWhenUnfocused: {
    none: null,
    percentage: '%',
    permille: '‰',
    basisPoint: '‱'
  },

  
  unformatOnHover: {
    unformat: true,
    doNotUnformat: false 

  },

  
  unformatOnSubmit: {
    unformat: true,
    keepCurrentValue: false
  },

  
  valuesToStrings: {
    none: null,
    zeroDash: {
      0: '-'
    },
    oneAroundZero: {
      '-1': 'Min',
      1: 'Max'
    }
  },

  
  watchExternalChanges: {
    watch: true,
    doNotWatch: false
  },

  
  wheelOn: {
    focus: 'focus',
    hover: 'hover'
  },

  
  wheelStep: {
    progressive: 'progressive'
  }
};


function freezeOptions(options) {
  
  Object.getOwnPropertyNames(options).forEach(function (optionName) {
    if (optionName === 'valuesToStrings') {
      var vsProps = Object.getOwnPropertyNames(options.valuesToStrings);
      vsProps.forEach(function (valuesToStringObjectName) {
        if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].isIE11() && options.valuesToStrings[valuesToStringObjectName] !== null) {
          Object.freeze(options.valuesToStrings[valuesToStringObjectName]);
        }
      });
    } else if (optionName !== 'styleRules') {
      if (!_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].isIE11() && options[optionName] !== null) {
        Object.freeze(options[optionName]);
      }
    }
  }); 

  return Object.freeze(options);
}

freezeOptions(_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options);
Object.defineProperty(_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"], 'options', {
  configurable: false,
  writable: false
});

 }),

 "./src/AutoNumericPredefinedOptions.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
 var _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__( "./src/AutoNumeric.js");
 var _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__( "./src/AutoNumericHelper.js");



var euro = {
  
  digitGroupSeparator: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.dot,
  
  decimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacter.comma,
  decimalCharacterAlternative: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacterAlternative.dot,
  currencySymbol: "\u202F\u20AC",
  currencySymbolPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbolPlacement.suffix,
  negativePositiveSignPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativePositiveSignPlacement.prefix
};
var dollar = {
  digitGroupSeparator: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.comma,
  decimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacter.dot,
  currencySymbol: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbol.dollar,
  currencySymbolPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbolPlacement.prefix,
  negativePositiveSignPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativePositiveSignPlacement.right
};
var japanese = {
  
  digitGroupSeparator: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.comma,
  decimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacter.dot,
  currencySymbol: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbol.yen,
  currencySymbolPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbolPlacement.prefix,
  negativePositiveSignPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativePositiveSignPlacement.right
}; 

var euroF = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(euro);
euroF.formulaMode = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.formulaMode.enabled;
var euroPos = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(euro);
euroPos.minimumValue = 0;
var euroNeg = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(euro);
euroNeg.maximumValue = 0;
euroNeg.negativePositiveSignPlacement = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativePositiveSignPlacement.prefix;
var euroSpace = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(euro);
euroSpace.digitGroupSeparator = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.normalSpace;
var euroSpacePos = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(euroSpace);
euroSpacePos.minimumValue = 0;
var euroSpaceNeg = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(euroSpace);
euroSpaceNeg.maximumValue = 0;
euroSpaceNeg.negativePositiveSignPlacement = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativePositiveSignPlacement.prefix;
var percentageEU2dec = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(euro);
percentageEU2dec.currencySymbol = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbol.none;
percentageEU2dec.suffixText = "\u202F".concat(_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.suffixText.percentage);
percentageEU2dec.wheelStep = 0.0001; 

percentageEU2dec.rawValueDivisor = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.rawValueDivisor.percentage;
var percentageEU2decPos = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(percentageEU2dec);
percentageEU2decPos.minimumValue = 0;
var percentageEU2decNeg = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(percentageEU2dec);
percentageEU2decNeg.maximumValue = 0;
percentageEU2decNeg.negativePositiveSignPlacement = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativePositiveSignPlacement.prefix;
var percentageEU3dec = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(percentageEU2dec);
percentageEU3dec.decimalPlaces = 3;
var percentageEU3decPos = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(percentageEU2decPos);
percentageEU3decPos.decimalPlaces = 3;
var percentageEU3decNeg = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(percentageEU2decNeg);
percentageEU3decNeg.decimalPlaces = 3;
var dollarF = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(dollar);
dollarF.formulaMode = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.formulaMode.enabled;
var dollarPos = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(dollar);
dollarPos.minimumValue = 0;
var dollarNeg = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(dollar);
dollarNeg.maximumValue = 0;
dollarNeg.negativePositiveSignPlacement = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativePositiveSignPlacement.prefix;
var dollarNegBrackets = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(dollarNeg);
dollarNegBrackets.negativeBracketsTypeOnBlur = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativeBracketsTypeOnBlur.parentheses;
var percentageUS2dec = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(dollar);
percentageUS2dec.currencySymbol = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbol.none;
percentageUS2dec.suffixText = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.suffixText.percentage;
percentageUS2dec.wheelStep = 0.0001;
percentageUS2dec.rawValueDivisor = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.rawValueDivisor.percentage;
var percentageUS2decPos = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(percentageUS2dec);
percentageUS2decPos.minimumValue = 0;
var percentageUS2decNeg = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(percentageUS2dec);
percentageUS2decNeg.maximumValue = 0;
percentageUS2decNeg.negativePositiveSignPlacement = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativePositiveSignPlacement.prefix;
var percentageUS3dec = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(percentageUS2dec);
percentageUS3dec.decimalPlaces = 3;
var percentageUS3decPos = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(percentageUS2decPos);
percentageUS3decPos.decimalPlaces = 3;
var percentageUS3decNeg = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(percentageUS2decNeg);
percentageUS3decNeg.decimalPlaces = 3;
var turkish = _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_1__["default"].cloneObject(euro);
turkish.currencySymbol = _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbol.lira;


_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].predefinedOptions = {
  euro: euro,
  euroPos: euroPos,
  euroNeg: euroNeg,
  euroSpace: euroSpace,
  euroSpacePos: euroSpacePos,
  euroSpaceNeg: euroSpaceNeg,
  percentageEU2dec: percentageEU2dec,
  percentageEU2decPos: percentageEU2decPos,
  percentageEU2decNeg: percentageEU2decNeg,
  percentageEU3dec: percentageEU3dec,
  percentageEU3decPos: percentageEU3decPos,
  percentageEU3decNeg: percentageEU3decNeg,
  dollar: dollar,
  dollarPos: dollarPos,
  dollarNeg: dollarNeg,
  dollarNegBrackets: dollarNegBrackets,
  percentageUS2dec: percentageUS2dec,
  percentageUS2decPos: percentageUS2decPos,
  percentageUS2decNeg: percentageUS2decNeg,
  percentageUS3dec: percentageUS3dec,
  percentageUS3decPos: percentageUS3decPos,
  percentageUS3decNeg: percentageUS3decNeg,
  French: euro,
  
  Spanish: euro,
  
  NorthAmerican: dollar,
  British: {
    digitGroupSeparator: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.comma,
    decimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacter.dot,
    currencySymbol: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbol.pound,
    currencySymbolPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbolPlacement.prefix,
    negativePositiveSignPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativePositiveSignPlacement.right
  },
  Swiss: {
    
    digitGroupSeparator: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.apostrophe,
    decimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacter.dot,
    currencySymbol: "\u202FCHF",
    currencySymbolPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbolPlacement.suffix,
    negativePositiveSignPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativePositiveSignPlacement.prefix
  },
  Japanese: japanese,
  
  Chinese: japanese,
  
  Brazilian: {
    digitGroupSeparator: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.dot,
    decimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacter.comma,
    currencySymbol: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbol.real,
    currencySymbolPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbolPlacement.prefix,
    negativePositiveSignPlacement: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.negativePositiveSignPlacement.right
  },
  Turkish: turkish,
  dotDecimalCharCommaSeparator: {
    digitGroupSeparator: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.comma,
    decimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacter.dot
  },
  commaDecimalCharDotSeparator: {
    digitGroupSeparator: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.dot,
    decimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacter.comma,
    decimalCharacterAlternative: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacterAlternative.dot
  },
  integer: {
    decimalPlaces: 0
  },
  integerPos: {
    minimumValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.minimumValue.zero,
    decimalPlaces: 0
  },
  integerNeg: {
    maximumValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.maximumValue.zero,
    decimalPlaces: 0
  },
  "float": {
    allowDecimalPadding: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.allowDecimalPadding.never
  },
  floatPos: {
    allowDecimalPadding: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.allowDecimalPadding.never,
    minimumValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.minimumValue.zero,
    maximumValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.maximumValue.tenTrillions
  },
  floatNeg: {
    allowDecimalPadding: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.allowDecimalPadding.never,
    minimumValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.minimumValue.tenTrillions,
    maximumValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.maximumValue.zero
  },
  numeric: {
    digitGroupSeparator: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.noSeparator,
    decimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacter.dot,
    currencySymbol: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbol.none
  },
  numericPos: {
    digitGroupSeparator: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.noSeparator,
    decimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacter.dot,
    currencySymbol: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbol.none,
    minimumValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.minimumValue.zero,
    maximumValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.maximumValue.tenTrillions
  },
  numericNeg: {
    digitGroupSeparator: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.digitGroupSeparator.noSeparator,
    decimalCharacter: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.decimalCharacter.dot,
    currencySymbol: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.currencySymbol.none,
    minimumValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.minimumValue.tenTrillions,
    maximumValue: _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].options.maximumValue.zero
  }
};
Object.getOwnPropertyNames(_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].predefinedOptions).forEach(function (optionName) {
  Object.freeze(_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].predefinedOptions[optionName]);
});
Object.freeze(_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"].predefinedOptions);
Object.defineProperty(_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"], 'predefinedOptions', {
  configurable: false,
  writable: false
});

 }),

 "./src/main.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
 var _AutoNumeric__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__( "./src/AutoNumeric.js");
 var _AutoNumericEvents__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__( "./src/AutoNumericEvents.js");
 var _AutoNumericOptions__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__( "./src/AutoNumericOptions.js");
 var _AutoNumericDefaultSettings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__( "./src/AutoNumericDefaultSettings.js");
 var _AutoNumericPredefinedOptions__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__( "./src/AutoNumericPredefinedOptions.js");










 __webpack_exports__["default"] = (_AutoNumeric__WEBPACK_IMPORTED_MODULE_0__["default"]);

 }),

 "./src/maths/ASTNode.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
 __webpack_require__.d(__webpack_exports__, "default", function() { return ASTNode; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }




var ASTNode = function () {
  function ASTNode() {
    _classCallCheck(this, ASTNode);
  }

  _createClass(ASTNode, null, [{
    key: "createNode",

    
    value: function createNode(type, left, right) {
      var node = new ASTNode();
      node.type = type;
      node.left = left;
      node.right = right;
      return node;
    }
  }, {
    key: "createUnaryNode",
    value: function createUnaryNode(left) {
      var node = new ASTNode();
      node.type = 'unaryMinus';
      node.left = left;
      node.right = null;
      return node;
    }
  }, {
    key: "createLeaf",
    value: function createLeaf(value) {
      var node = new ASTNode();
      node.type = 'number';
      node.value = value;
      return node;
    }
  }]);

  return ASTNode;
}();



 }),

 "./src/maths/Evaluator.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
 __webpack_require__.d(__webpack_exports__, "default", function() { return Evaluator; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }




var Evaluator = function () {
  function Evaluator(ast) {
    _classCallCheck(this, Evaluator);

    if (ast === null) {
      throw new Error("Invalid AST");
    } 

  }

  _createClass(Evaluator, [{
    key: "evaluate",
    value: function evaluate(subtree) {
      if (subtree === void 0 || subtree === null) {
        throw new Error("Invalid AST sub-tree");
      }

      if (subtree.type === 'number') {
        return subtree.value;
      } else if (subtree.type === 'unaryMinus') {
        return -this.evaluate(subtree.left);
      } else {
        var left = this.evaluate(subtree.left);
        var right = this.evaluate(subtree.right);

        switch (subtree.type) {
          case 'op_+':
            return Number(left) + Number(right);

          case 'op_-':
            return left - right;

          case 'op_*':
            return left * right;

          case 'op_/':
            return left / right;

          default:
            throw new Error("Invalid operator '".concat(subtree.type, "'"));
        }
      }
    }
  }]);

  return Evaluator;
}();



 }),

 "./src/maths/Lexer.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
 __webpack_require__.d(__webpack_exports__, "default", function() { return Lexer; });
 var _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__( "./src/AutoNumericHelper.js");
 var _Token__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__( "./src/maths/Token.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }





var Lexer = function () {
  function Lexer(text) {
    _classCallCheck(this, Lexer);

    this.text = text;
    this.textLength = text.length;
    this.index = 0;
    this.token = new _Token__WEBPACK_IMPORTED_MODULE_1__["default"]('Error', 0, 0);
  }
  


  _createClass(Lexer, [{
    key: "_skipSpaces",
    value: function _skipSpaces() {
      while (this.text[this.index] === ' ' && this.index <= this.textLength) {
        this.index++;
      }
    }
    

  }, {
    key: "getIndex",
    value: function getIndex() {
      return this.index;
    }
    

  }, {
    key: "getNextToken",
    value: function getNextToken() {
      var decimalCharacter = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '.';

      this._skipSpaces(); 


      if (this.textLength === this.index) {
        this.token.type = 'EOT'; 

        return this.token;
      } 


      if (_AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isDigit(this.text[this.index])) {
        this.token.type = 'num';
        this.token.value = this._getNumber(decimalCharacter);
        return this.token;
      } 


      this.token.type = 'Error';

      switch (this.text[this.index]) {
        case '+':
          this.token.type = '+';
          break;

        case '-':
          this.token.type = '-';
          break;

        case '*':
          this.token.type = '*';
          break;

        case '/':
          this.token.type = '/';
          break;

        case '(':
          this.token.type = '(';
          break;

        case ')':
          this.token.type = ')';
          break;
      }

      if (this.token.type !== 'Error') {
        this.token.symbol = this.text[this.index];
        this.index++;
      } else {
        throw new Error("Unexpected token '".concat(this.token.symbol, "' at position '").concat(this.token.index, "' in the token function"));
      }

      return this.token;
    }
    

  }, {
    key: "_getNumber",
    value: function _getNumber(decimalCharacter) {
      this._skipSpaces();

      var startIndex = this.index;

      while (this.index <= this.textLength && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isDigit(this.text[this.index])) {
        
        this.index++;
      }

      if (this.text[this.index] === decimalCharacter) {
        this.index++;
      }

      while (this.index <= this.textLength && _AutoNumericHelper__WEBPACK_IMPORTED_MODULE_0__["default"].isDigit(this.text[this.index])) {
        
        this.index++;
      }

      if (this.index === startIndex) {
        throw new Error("No number has been found while it was expected");
      } 


      return this.text.substring(startIndex, this.index).replace(decimalCharacter, '.');
    }
  }]);

  return Lexer;
}();



 }),

 "./src/maths/Parser.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
 __webpack_require__.d(__webpack_exports__, "default", function() { return Parser; });
 var _ASTNode__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__( "./src/maths/ASTNode.js");
 var _Lexer__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__( "./src/maths/Lexer.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }






var Parser = function () {
  
  function Parser(text) {
    var customDecimalCharacter = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '.';

    _classCallCheck(this, Parser);

    this.text = text;
    this.decimalCharacter = customDecimalCharacter;
    this.lexer = new _Lexer__WEBPACK_IMPORTED_MODULE_1__["default"](text);
    this.token = this.lexer.getNextToken(this.decimalCharacter);
    return this._exp();
  }

  _createClass(Parser, [{
    key: "_exp",
    value: function _exp() {
      var termNode = this._term();

      var exprNode = this._moreExp(); 


      return _ASTNode__WEBPACK_IMPORTED_MODULE_0__["default"].createNode('op_+', termNode, exprNode);
    }
  }, {
    key: "_moreExp",
    value: function _moreExp() {
      var termNode;
      var exprNode;

      switch (this.token.type) {
        case '+':
          this.token = this.lexer.getNextToken(this.decimalCharacter);
          termNode = this._term();
          exprNode = this._moreExp();
          return _ASTNode__WEBPACK_IMPORTED_MODULE_0__["default"].createNode('op_+', exprNode, termNode);

        case '-':
          this.token = this.lexer.getNextToken(this.decimalCharacter);
          termNode = this._term();
          exprNode = this._moreExp();
          return _ASTNode__WEBPACK_IMPORTED_MODULE_0__["default"].createNode('op_-', exprNode, termNode);
      }

      return _ASTNode__WEBPACK_IMPORTED_MODULE_0__["default"].createLeaf(0);
    }
  }, {
    key: "_term",
    value: function _term() {
      var factorNode = this._factor();

      var termsNode = this._moreTerms(); 


      return _ASTNode__WEBPACK_IMPORTED_MODULE_0__["default"].createNode('op_*', factorNode, termsNode);
    }
  }, {
    key: "_moreTerms",
    value: function _moreTerms() {
      var factorNode;
      var termsNode;

      switch (this.token.type) {
        case '*':
          this.token = this.lexer.getNextToken(this.decimalCharacter);
          factorNode = this._factor();
          termsNode = this._moreTerms();
          return _ASTNode__WEBPACK_IMPORTED_MODULE_0__["default"].createNode('op_*', termsNode, factorNode);

        case '/':
          this.token = this.lexer.getNextToken(this.decimalCharacter);
          factorNode = this._factor();
          termsNode = this._moreTerms();
          return _ASTNode__WEBPACK_IMPORTED_MODULE_0__["default"].createNode('op_/', termsNode, factorNode);
      }

      return _ASTNode__WEBPACK_IMPORTED_MODULE_0__["default"].createLeaf(1);
    }
  }, {
    key: "_factor",
    value: function _factor() {
      var expression;
      var factor;
      var value;

      switch (this.token.type) {
        case 'num':
          value = this.token.value;
          this.token = this.lexer.getNextToken(this.decimalCharacter);
          return _ASTNode__WEBPACK_IMPORTED_MODULE_0__["default"].createLeaf(value);

        case '-':
          this.token = this.lexer.getNextToken(this.decimalCharacter);
          factor = this._factor();
          return _ASTNode__WEBPACK_IMPORTED_MODULE_0__["default"].createUnaryNode(factor);

        case '(':
          this.token = this.lexer.getNextToken(this.decimalCharacter);
          expression = this._exp();

          this._match(')');

          return expression;

        default:
          {
            throw new Error("Unexpected token '".concat(this.token.symbol, "' with type '").concat(this.token.type, "' at position '").concat(this.token.index, "' in the factor function"));
          }
      }
    }
  }, {
    key: "_match",
    value: function _match(expected) {
      var index = this.lexer.getIndex() - 1;

      if (this.text[index] === expected) {
        this.token = this.lexer.getNextToken(this.decimalCharacter);
      } else {
        throw new Error("Unexpected token '".concat(this.token.symbol, "' at position '").concat(index, "' in the match function"));
      }
    }
  }]);

  return Parser;
}();



 }),

 "./src/maths/Token.js":


 (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
 __webpack_require__.d(__webpack_exports__, "default", function() { return Token; });
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }




var Token = function Token(type, value, symbol) {
  _classCallCheck(this, Token);

  this.type = type;
  this.value = value;
  this.symbol = symbol;
};



 })

 })["default"];
});

Espo.loader.setContextId(null);
