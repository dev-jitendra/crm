

(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
  typeof define === 'function' && define.amd ? define(['exports'], factory) :
  (global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.vis = global.vis || {}));
})(this, (function (exports) {
  function _classCallCheck(instance, Constructor) {
    if (!(instance instanceof Constructor)) {
      throw new TypeError("Cannot call a class as a function");
    }
  }

  var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

  function getDefaultExportFromCjs (x) {
  	return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, 'default') ? x['default'] : x;
  }

  var definePropertyExports$3 = {};
  var defineProperty$f = {
    get exports(){ return definePropertyExports$3; },
    set exports(v){ definePropertyExports$3 = v; },
  };

  var definePropertyExports$2 = {};
  var defineProperty$e = {
    get exports(){ return definePropertyExports$2; },
    set exports(v){ definePropertyExports$2 = v; },
  };

  var definePropertyExports$1 = {};
  var defineProperty$d = {
    get exports(){ return definePropertyExports$1; },
    set exports(v){ definePropertyExports$1 = v; },
  };

  var check = function (it) {
    return it && it.Math == Math && it;
  };

  
  var global$n =
    
    check(typeof globalThis == 'object' && globalThis) ||
    check(typeof window == 'object' && window) ||
    
    check(typeof self == 'object' && self) ||
    check(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
    
    (function () { return this; })() || Function('return this')();

  var fails$w = function (exec) {
    try {
      return !!exec();
    } catch (error) {
      return true;
    }
  };

  var fails$v = fails$w;

  var functionBindNative = !fails$v(function () {
    
    var test = (function () {  }).bind();
    
    return typeof test != 'function' || test.hasOwnProperty('prototype');
  });

  var NATIVE_BIND$4 = functionBindNative;

  var FunctionPrototype$3 = Function.prototype;
  var apply$6 = FunctionPrototype$3.apply;
  var call$k = FunctionPrototype$3.call;

  
  var functionApply = typeof Reflect == 'object' && Reflect.apply || (NATIVE_BIND$4 ? call$k.bind(apply$6) : function () {
    return call$k.apply(apply$6, arguments);
  });

  var NATIVE_BIND$3 = functionBindNative;

  var FunctionPrototype$2 = Function.prototype;
  var call$j = FunctionPrototype$2.call;
  var uncurryThisWithBind = NATIVE_BIND$3 && FunctionPrototype$2.bind.bind(call$j, call$j);

  var functionUncurryThis = NATIVE_BIND$3 ? uncurryThisWithBind : function (fn) {
    return function () {
      return call$j.apply(fn, arguments);
    };
  };

  var uncurryThis$w = functionUncurryThis;

  var toString$c = uncurryThis$w({}.toString);
  var stringSlice$1 = uncurryThis$w(''.slice);

  var classofRaw$2 = function (it) {
    return stringSlice$1(toString$c(it), 8, -1);
  };

  var classofRaw$1 = classofRaw$2;
  var uncurryThis$v = functionUncurryThis;

  var functionUncurryThisClause = function (fn) {
    
    
    
    if (classofRaw$1(fn) === 'Function') return uncurryThis$v(fn);
  };

  var documentAll$2 = typeof document == 'object' && document.all;

  
  
  var IS_HTMLDDA = typeof documentAll$2 == 'undefined' && documentAll$2 !== undefined;

  var documentAll_1 = {
    all: documentAll$2,
    IS_HTMLDDA: IS_HTMLDDA
  };

  var $documentAll$1 = documentAll_1;

  var documentAll$1 = $documentAll$1.all;

  
  
  var isCallable$m = $documentAll$1.IS_HTMLDDA ? function (argument) {
    return typeof argument == 'function' || argument === documentAll$1;
  } : function (argument) {
    return typeof argument == 'function';
  };

  var objectGetOwnPropertyDescriptor = {};

  var fails$u = fails$w;

  
  var descriptors = !fails$u(function () {
    
    return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] != 7;
  });

  var NATIVE_BIND$2 = functionBindNative;

  var call$i = Function.prototype.call;

  var functionCall = NATIVE_BIND$2 ? call$i.bind(call$i) : function () {
    return call$i.apply(call$i, arguments);
  };

  var objectPropertyIsEnumerable = {};

  var $propertyIsEnumerable$2 = {}.propertyIsEnumerable;
  
  var getOwnPropertyDescriptor$7 = Object.getOwnPropertyDescriptor;

  
  var NASHORN_BUG = getOwnPropertyDescriptor$7 && !$propertyIsEnumerable$2.call({ 1: 2 }, 1);

  
  
  objectPropertyIsEnumerable.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
    var descriptor = getOwnPropertyDescriptor$7(this, V);
    return !!descriptor && descriptor.enumerable;
  } : $propertyIsEnumerable$2;

  var createPropertyDescriptor$7 = function (bitmap, value) {
    return {
      enumerable: !(bitmap & 1),
      configurable: !(bitmap & 2),
      writable: !(bitmap & 4),
      value: value
    };
  };

  var uncurryThis$u = functionUncurryThis;
  var fails$t = fails$w;
  var classof$g = classofRaw$2;

  var $Object$4 = Object;
  var split = uncurryThis$u(''.split);

  
  var indexedObject = fails$t(function () {
    
    
    return !$Object$4('z').propertyIsEnumerable(0);
  }) ? function (it) {
    return classof$g(it) == 'String' ? split(it, '') : $Object$4(it);
  } : $Object$4;

  
  
  var isNullOrUndefined$5 = function (it) {
    return it === null || it === undefined;
  };

  var isNullOrUndefined$4 = isNullOrUndefined$5;

  var $TypeError$h = TypeError;

  
  
  var requireObjectCoercible$5 = function (it) {
    if (isNullOrUndefined$4(it)) throw $TypeError$h("Can't call method on " + it);
    return it;
  };

  
  var IndexedObject$3 = indexedObject;
  var requireObjectCoercible$4 = requireObjectCoercible$5;

  var toIndexedObject$b = function (it) {
    return IndexedObject$3(requireObjectCoercible$4(it));
  };

  var isCallable$l = isCallable$m;
  var $documentAll = documentAll_1;

  var documentAll = $documentAll.all;

  var isObject$i = $documentAll.IS_HTMLDDA ? function (it) {
    return typeof it == 'object' ? it !== null : isCallable$l(it) || it === documentAll;
  } : function (it) {
    return typeof it == 'object' ? it !== null : isCallable$l(it);
  };

  var path$r = {};

  var path$q = path$r;
  var global$m = global$n;
  var isCallable$k = isCallable$m;

  var aFunction = function (variable) {
    return isCallable$k(variable) ? variable : undefined;
  };

  var getBuiltIn$f = function (namespace, method) {
    return arguments.length < 2 ? aFunction(path$q[namespace]) || aFunction(global$m[namespace])
      : path$q[namespace] && path$q[namespace][method] || global$m[namespace] && global$m[namespace][method];
  };

  var uncurryThis$t = functionUncurryThis;

  var objectIsPrototypeOf = uncurryThis$t({}.isPrototypeOf);

  var engineUserAgent = typeof navigator != 'undefined' && String(navigator.userAgent) || '';

  var global$l = global$n;
  var userAgent$5 = engineUserAgent;

  var process$4 = global$l.process;
  var Deno$1 = global$l.Deno;
  var versions = process$4 && process$4.versions || Deno$1 && Deno$1.version;
  var v8 = versions && versions.v8;
  var match, version;

  if (v8) {
    match = v8.split('.');
    
    
    version = match[0] > 0 && match[0] < 4 ? 1 : +(match[0] + match[1]);
  }

  
  
  if (!version && userAgent$5) {
    match = userAgent$5.match(/Edge\/(\d+)/);
    if (!match || match[1] >= 74) {
      match = userAgent$5.match(/Chrome\/(\d+)/);
      if (match) version = +match[1];
    }
  }

  var engineV8Version = version;

  

  var V8_VERSION$3 = engineV8Version;
  var fails$s = fails$w;

  
  var symbolConstructorDetection = !!Object.getOwnPropertySymbols && !fails$s(function () {
    var symbol = Symbol();
    
    
    return !String(symbol) || !(Object(symbol) instanceof Symbol) ||
      
      !Symbol.sham && V8_VERSION$3 && V8_VERSION$3 < 41;
  });

  

  var NATIVE_SYMBOL$5 = symbolConstructorDetection;

  var useSymbolAsUid = NATIVE_SYMBOL$5
    && !Symbol.sham
    && typeof Symbol.iterator == 'symbol';

  var getBuiltIn$e = getBuiltIn$f;
  var isCallable$j = isCallable$m;
  var isPrototypeOf$n = objectIsPrototypeOf;
  var USE_SYMBOL_AS_UID$1 = useSymbolAsUid;

  var $Object$3 = Object;

  var isSymbol$5 = USE_SYMBOL_AS_UID$1 ? function (it) {
    return typeof it == 'symbol';
  } : function (it) {
    var $Symbol = getBuiltIn$e('Symbol');
    return isCallable$j($Symbol) && isPrototypeOf$n($Symbol.prototype, $Object$3(it));
  };

  var $String$4 = String;

  var tryToString$6 = function (argument) {
    try {
      return $String$4(argument);
    } catch (error) {
      return 'Object';
    }
  };

  var isCallable$i = isCallable$m;
  var tryToString$5 = tryToString$6;

  var $TypeError$g = TypeError;

  
  var aCallable$e = function (argument) {
    if (isCallable$i(argument)) return argument;
    throw $TypeError$g(tryToString$5(argument) + ' is not a function');
  };

  var aCallable$d = aCallable$e;
  var isNullOrUndefined$3 = isNullOrUndefined$5;

  
  
  var getMethod$3 = function (V, P) {
    var func = V[P];
    return isNullOrUndefined$3(func) ? undefined : aCallable$d(func);
  };

  var call$h = functionCall;
  var isCallable$h = isCallable$m;
  var isObject$h = isObject$i;

  var $TypeError$f = TypeError;

  
  
  var ordinaryToPrimitive$1 = function (input, pref) {
    var fn, val;
    if (pref === 'string' && isCallable$h(fn = input.toString) && !isObject$h(val = call$h(fn, input))) return val;
    if (isCallable$h(fn = input.valueOf) && !isObject$h(val = call$h(fn, input))) return val;
    if (pref !== 'string' && isCallable$h(fn = input.toString) && !isObject$h(val = call$h(fn, input))) return val;
    throw $TypeError$f("Can't convert object to primitive value");
  };

  var sharedExports = {};
  var shared$7 = {
    get exports(){ return sharedExports; },
    set exports(v){ sharedExports = v; },
  };

  var isPure = true;

  var global$k = global$n;

  
  var defineProperty$c = Object.defineProperty;

  var defineGlobalProperty$1 = function (key, value) {
    try {
      defineProperty$c(global$k, key, { value: value, configurable: true, writable: true });
    } catch (error) {
      global$k[key] = value;
    } return value;
  };

  var global$j = global$n;
  var defineGlobalProperty = defineGlobalProperty$1;

  var SHARED = '__core-js_shared__';
  var store$3 = global$j[SHARED] || defineGlobalProperty(SHARED, {});

  var sharedStore = store$3;

  var store$2 = sharedStore;

  (shared$7.exports = function (key, value) {
    return store$2[key] || (store$2[key] = value !== undefined ? value : {});
  })('versions', []).push({
    version: '3.29.0',
    mode: 'pure' ,
    copyright: '© 2014-2023 Denis Pushkarev (zloirock.ru)',
    license: 'https:
    source: 'https:
  });

  var requireObjectCoercible$3 = requireObjectCoercible$5;

  var $Object$2 = Object;

  
  
  var toObject$e = function (argument) {
    return $Object$2(requireObjectCoercible$3(argument));
  };

  var uncurryThis$s = functionUncurryThis;
  var toObject$d = toObject$e;

  var hasOwnProperty = uncurryThis$s({}.hasOwnProperty);

  
  
  
  var hasOwnProperty_1 = Object.hasOwn || function hasOwn(it, key) {
    return hasOwnProperty(toObject$d(it), key);
  };

  var uncurryThis$r = functionUncurryThis;

  var id$1 = 0;
  var postfix = Math.random();
  var toString$b = uncurryThis$r(1.0.toString);

  var uid$4 = function (key) {
    return 'Symbol(' + (key === undefined ? '' : key) + ')_' + toString$b(++id$1 + postfix, 36);
  };

  var global$i = global$n;
  var shared$6 = sharedExports;
  var hasOwn$j = hasOwnProperty_1;
  var uid$3 = uid$4;
  var NATIVE_SYMBOL$4 = symbolConstructorDetection;
  var USE_SYMBOL_AS_UID = useSymbolAsUid;

  var Symbol$4 = global$i.Symbol;
  var WellKnownSymbolsStore$2 = shared$6('wks');
  var createWellKnownSymbol = USE_SYMBOL_AS_UID ? Symbol$4['for'] || Symbol$4 : Symbol$4 && Symbol$4.withoutSetter || uid$3;

  var wellKnownSymbol$o = function (name) {
    if (!hasOwn$j(WellKnownSymbolsStore$2, name)) {
      WellKnownSymbolsStore$2[name] = NATIVE_SYMBOL$4 && hasOwn$j(Symbol$4, name)
        ? Symbol$4[name]
        : createWellKnownSymbol('Symbol.' + name);
    } return WellKnownSymbolsStore$2[name];
  };

  var call$g = functionCall;
  var isObject$g = isObject$i;
  var isSymbol$4 = isSymbol$5;
  var getMethod$2 = getMethod$3;
  var ordinaryToPrimitive = ordinaryToPrimitive$1;
  var wellKnownSymbol$n = wellKnownSymbol$o;

  var $TypeError$e = TypeError;
  var TO_PRIMITIVE = wellKnownSymbol$n('toPrimitive');

  
  
  var toPrimitive$7 = function (input, pref) {
    if (!isObject$g(input) || isSymbol$4(input)) return input;
    var exoticToPrim = getMethod$2(input, TO_PRIMITIVE);
    var result;
    if (exoticToPrim) {
      if (pref === undefined) pref = 'default';
      result = call$g(exoticToPrim, input, pref);
      if (!isObject$g(result) || isSymbol$4(result)) return result;
      throw $TypeError$e("Can't convert object to primitive value");
    }
    if (pref === undefined) pref = 'number';
    return ordinaryToPrimitive(input, pref);
  };

  var toPrimitive$6 = toPrimitive$7;
  var isSymbol$3 = isSymbol$5;

  
  
  var toPropertyKey$4 = function (argument) {
    var key = toPrimitive$6(argument, 'string');
    return isSymbol$3(key) ? key : key + '';
  };

  var global$h = global$n;
  var isObject$f = isObject$i;

  var document$3 = global$h.document;
  
  var EXISTS$1 = isObject$f(document$3) && isObject$f(document$3.createElement);

  var documentCreateElement$1 = function (it) {
    return EXISTS$1 ? document$3.createElement(it) : {};
  };

  var DESCRIPTORS$i = descriptors;
  var fails$r = fails$w;
  var createElement$1 = documentCreateElement$1;

  
  var ie8DomDefine = !DESCRIPTORS$i && !fails$r(function () {
    
    return Object.defineProperty(createElement$1('div'), 'a', {
      get: function () { return 7; }
    }).a != 7;
  });

  var DESCRIPTORS$h = descriptors;
  var call$f = functionCall;
  var propertyIsEnumerableModule$2 = objectPropertyIsEnumerable;
  var createPropertyDescriptor$6 = createPropertyDescriptor$7;
  var toIndexedObject$a = toIndexedObject$b;
  var toPropertyKey$3 = toPropertyKey$4;
  var hasOwn$i = hasOwnProperty_1;
  var IE8_DOM_DEFINE$1 = ie8DomDefine;

  
  var $getOwnPropertyDescriptor$2 = Object.getOwnPropertyDescriptor;

  
  
  objectGetOwnPropertyDescriptor.f = DESCRIPTORS$h ? $getOwnPropertyDescriptor$2 : function getOwnPropertyDescriptor(O, P) {
    O = toIndexedObject$a(O);
    P = toPropertyKey$3(P);
    if (IE8_DOM_DEFINE$1) try {
      return $getOwnPropertyDescriptor$2(O, P);
    } catch (error) {  }
    if (hasOwn$i(O, P)) return createPropertyDescriptor$6(!call$f(propertyIsEnumerableModule$2.f, O, P), O[P]);
  };

  var fails$q = fails$w;
  var isCallable$g = isCallable$m;

  var replacement = /#|\.prototype\./;

  var isForced$2 = function (feature, detection) {
    var value = data[normalize(feature)];
    return value == POLYFILL ? true
      : value == NATIVE ? false
      : isCallable$g(detection) ? fails$q(detection)
      : !!detection;
  };

  var normalize = isForced$2.normalize = function (string) {
    return String(string).replace(replacement, '.').toLowerCase();
  };

  var data = isForced$2.data = {};
  var NATIVE = isForced$2.NATIVE = 'N';
  var POLYFILL = isForced$2.POLYFILL = 'P';

  var isForced_1 = isForced$2;

  var uncurryThis$q = functionUncurryThisClause;
  var aCallable$c = aCallable$e;
  var NATIVE_BIND$1 = functionBindNative;

  var bind$j = uncurryThis$q(uncurryThis$q.bind);

  
  var functionBindContext = function (fn, that) {
    aCallable$c(fn);
    return that === undefined ? fn : NATIVE_BIND$1 ? bind$j(fn, that) : function () {
      return fn.apply(that, arguments);
    };
  };

  var objectDefineProperty = {};

  var DESCRIPTORS$g = descriptors;
  var fails$p = fails$w;

  
  
  var v8PrototypeDefineBug = DESCRIPTORS$g && fails$p(function () {
    
    return Object.defineProperty(function () {  }, 'prototype', {
      value: 42,
      writable: false
    }).prototype != 42;
  });

  var isObject$e = isObject$i;

  var $String$3 = String;
  var $TypeError$d = TypeError;

  
  var anObject$d = function (argument) {
    if (isObject$e(argument)) return argument;
    throw $TypeError$d($String$3(argument) + ' is not an object');
  };

  var DESCRIPTORS$f = descriptors;
  var IE8_DOM_DEFINE = ie8DomDefine;
  var V8_PROTOTYPE_DEFINE_BUG$1 = v8PrototypeDefineBug;
  var anObject$c = anObject$d;
  var toPropertyKey$2 = toPropertyKey$4;

  var $TypeError$c = TypeError;
  
  var $defineProperty$1 = Object.defineProperty;
  
  var $getOwnPropertyDescriptor$1 = Object.getOwnPropertyDescriptor;
  var ENUMERABLE = 'enumerable';
  var CONFIGURABLE$1 = 'configurable';
  var WRITABLE = 'writable';

  
  
  objectDefineProperty.f = DESCRIPTORS$f ? V8_PROTOTYPE_DEFINE_BUG$1 ? function defineProperty(O, P, Attributes) {
    anObject$c(O);
    P = toPropertyKey$2(P);
    anObject$c(Attributes);
    if (typeof O === 'function' && P === 'prototype' && 'value' in Attributes && WRITABLE in Attributes && !Attributes[WRITABLE]) {
      var current = $getOwnPropertyDescriptor$1(O, P);
      if (current && current[WRITABLE]) {
        O[P] = Attributes.value;
        Attributes = {
          configurable: CONFIGURABLE$1 in Attributes ? Attributes[CONFIGURABLE$1] : current[CONFIGURABLE$1],
          enumerable: ENUMERABLE in Attributes ? Attributes[ENUMERABLE] : current[ENUMERABLE],
          writable: false
        };
      }
    } return $defineProperty$1(O, P, Attributes);
  } : $defineProperty$1 : function defineProperty(O, P, Attributes) {
    anObject$c(O);
    P = toPropertyKey$2(P);
    anObject$c(Attributes);
    if (IE8_DOM_DEFINE) try {
      return $defineProperty$1(O, P, Attributes);
    } catch (error) {  }
    if ('get' in Attributes || 'set' in Attributes) throw $TypeError$c('Accessors not supported');
    if ('value' in Attributes) O[P] = Attributes.value;
    return O;
  };

  var DESCRIPTORS$e = descriptors;
  var definePropertyModule$4 = objectDefineProperty;
  var createPropertyDescriptor$5 = createPropertyDescriptor$7;

  var createNonEnumerableProperty$9 = DESCRIPTORS$e ? function (object, key, value) {
    return definePropertyModule$4.f(object, key, createPropertyDescriptor$5(1, value));
  } : function (object, key, value) {
    object[key] = value;
    return object;
  };

  var global$g = global$n;
  var apply$5 = functionApply;
  var uncurryThis$p = functionUncurryThisClause;
  var isCallable$f = isCallable$m;
  var getOwnPropertyDescriptor$6 = objectGetOwnPropertyDescriptor.f;
  var isForced$1 = isForced_1;
  var path$p = path$r;
  var bind$i = functionBindContext;
  var createNonEnumerableProperty$8 = createNonEnumerableProperty$9;
  var hasOwn$h = hasOwnProperty_1;

  var wrapConstructor = function (NativeConstructor) {
    var Wrapper = function (a, b, c) {
      if (this instanceof Wrapper) {
        switch (arguments.length) {
          case 0: return new NativeConstructor();
          case 1: return new NativeConstructor(a);
          case 2: return new NativeConstructor(a, b);
        } return new NativeConstructor(a, b, c);
      } return apply$5(NativeConstructor, this, arguments);
    };
    Wrapper.prototype = NativeConstructor.prototype;
    return Wrapper;
  };

  
  var _export = function (options, source) {
    var TARGET = options.target;
    var GLOBAL = options.global;
    var STATIC = options.stat;
    var PROTO = options.proto;

    var nativeSource = GLOBAL ? global$g : STATIC ? global$g[TARGET] : (global$g[TARGET] || {}).prototype;

    var target = GLOBAL ? path$p : path$p[TARGET] || createNonEnumerableProperty$8(path$p, TARGET, {})[TARGET];
    var targetPrototype = target.prototype;

    var FORCED, USE_NATIVE, VIRTUAL_PROTOTYPE;
    var key, sourceProperty, targetProperty, nativeProperty, resultProperty, descriptor;

    for (key in source) {
      FORCED = isForced$1(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
      
      USE_NATIVE = !FORCED && nativeSource && hasOwn$h(nativeSource, key);

      targetProperty = target[key];

      if (USE_NATIVE) if (options.dontCallGetSet) {
        descriptor = getOwnPropertyDescriptor$6(nativeSource, key);
        nativeProperty = descriptor && descriptor.value;
      } else nativeProperty = nativeSource[key];

      
      sourceProperty = (USE_NATIVE && nativeProperty) ? nativeProperty : source[key];

      if (USE_NATIVE && typeof targetProperty == typeof sourceProperty) continue;

      
      if (options.bind && USE_NATIVE) resultProperty = bind$i(sourceProperty, global$g);
      
      else if (options.wrap && USE_NATIVE) resultProperty = wrapConstructor(sourceProperty);
      
      else if (PROTO && isCallable$f(sourceProperty)) resultProperty = uncurryThis$p(sourceProperty);
      
      else resultProperty = sourceProperty;

      
      if (options.sham || (sourceProperty && sourceProperty.sham) || (targetProperty && targetProperty.sham)) {
        createNonEnumerableProperty$8(resultProperty, 'sham', true);
      }

      createNonEnumerableProperty$8(target, key, resultProperty);

      if (PROTO) {
        VIRTUAL_PROTOTYPE = TARGET + 'Prototype';
        if (!hasOwn$h(path$p, VIRTUAL_PROTOTYPE)) {
          createNonEnumerableProperty$8(path$p, VIRTUAL_PROTOTYPE, {});
        }
        
        createNonEnumerableProperty$8(path$p[VIRTUAL_PROTOTYPE], key, sourceProperty);
        
        if (options.real && targetPrototype && (FORCED || !targetPrototype[key])) {
          createNonEnumerableProperty$8(targetPrototype, key, sourceProperty);
        }
      }
    }
  };

  var $$T = _export;
  var DESCRIPTORS$d = descriptors;
  var defineProperty$b = objectDefineProperty.f;

  
  
  
  $$T({ target: 'Object', stat: true, forced: Object.defineProperty !== defineProperty$b, sham: !DESCRIPTORS$d }, {
    defineProperty: defineProperty$b
  });

  var path$o = path$r;

  var Object$4 = path$o.Object;

  var defineProperty$a = defineProperty$d.exports = function defineProperty(it, key, desc) {
    return Object$4.defineProperty(it, key, desc);
  };

  if (Object$4.defineProperty.sham) defineProperty$a.sham = true;

  var parent$1c = definePropertyExports$1;

  var defineProperty$9 = parent$1c;

  var parent$1b = defineProperty$9;

  var defineProperty$8 = parent$1b;

  var parent$1a = defineProperty$8;

  var defineProperty$7 = parent$1a;

  (function (module) {
  	module.exports = defineProperty$7;
  } (defineProperty$e));

  (function (module) {
  	module.exports = definePropertyExports$2;
  } (defineProperty$f));

  var _Object$defineProperty$1 = getDefaultExportFromCjs(definePropertyExports$3);

  var symbolExports$2 = {};
  var symbol$6 = {
    get exports(){ return symbolExports$2; },
    set exports(v){ symbolExports$2 = v; },
  };

  var symbolExports$1 = {};
  var symbol$5 = {
    get exports(){ return symbolExports$1; },
    set exports(v){ symbolExports$1 = v; },
  };

  var classof$f = classofRaw$2;

  
  
  
  var isArray$f = Array.isArray || function isArray(argument) {
    return classof$f(argument) == 'Array';
  };

  var ceil = Math.ceil;
  var floor$1 = Math.floor;

  
  
  
  var mathTrunc = Math.trunc || function trunc(x) {
    var n = +x;
    return (n > 0 ? floor$1 : ceil)(n);
  };

  var trunc = mathTrunc;

  
  
  var toIntegerOrInfinity$4 = function (argument) {
    var number = +argument;
    
    return number !== number || number === 0 ? 0 : trunc(number);
  };

  var toIntegerOrInfinity$3 = toIntegerOrInfinity$4;

  var min$2 = Math.min;

  
  
  var toLength$1 = function (argument) {
    return argument > 0 ? min$2(toIntegerOrInfinity$3(argument), 0x1FFFFFFFFFFFFF) : 0; 
  };

  var toLength = toLength$1;

  
  
  var lengthOfArrayLike$d = function (obj) {
    return toLength(obj.length);
  };

  var $TypeError$b = TypeError;
  var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF; 

  var doesNotExceedSafeInteger$3 = function (it) {
    if (it > MAX_SAFE_INTEGER) throw $TypeError$b('Maximum allowed index exceeded');
    return it;
  };

  var toPropertyKey$1 = toPropertyKey$4;
  var definePropertyModule$3 = objectDefineProperty;
  var createPropertyDescriptor$4 = createPropertyDescriptor$7;

  var createProperty$6 = function (object, key, value) {
    var propertyKey = toPropertyKey$1(key);
    if (propertyKey in object) definePropertyModule$3.f(object, propertyKey, createPropertyDescriptor$4(0, value));
    else object[propertyKey] = value;
  };

  var wellKnownSymbol$m = wellKnownSymbol$o;

  var TO_STRING_TAG$4 = wellKnownSymbol$m('toStringTag');
  var test$2 = {};

  test$2[TO_STRING_TAG$4] = 'z';

  var toStringTagSupport = String(test$2) === '[object z]';

  var TO_STRING_TAG_SUPPORT$2 = toStringTagSupport;
  var isCallable$e = isCallable$m;
  var classofRaw = classofRaw$2;
  var wellKnownSymbol$l = wellKnownSymbol$o;

  var TO_STRING_TAG$3 = wellKnownSymbol$l('toStringTag');
  var $Object$1 = Object;

  
  var CORRECT_ARGUMENTS = classofRaw(function () { return arguments; }()) == 'Arguments';

  
  var tryGet = function (it, key) {
    try {
      return it[key];
    } catch (error) {  }
  };

  
  var classof$e = TO_STRING_TAG_SUPPORT$2 ? classofRaw : function (it) {
    var O, tag, result;
    return it === undefined ? 'Undefined' : it === null ? 'Null'
      
      : typeof (tag = tryGet(O = $Object$1(it), TO_STRING_TAG$3)) == 'string' ? tag
      
      : CORRECT_ARGUMENTS ? classofRaw(O)
      
      : (result = classofRaw(O)) == 'Object' && isCallable$e(O.callee) ? 'Arguments' : result;
  };

  var uncurryThis$o = functionUncurryThis;
  var isCallable$d = isCallable$m;
  var store$1 = sharedStore;

  var functionToString = uncurryThis$o(Function.toString);

  
  if (!isCallable$d(store$1.inspectSource)) {
    store$1.inspectSource = function (it) {
      return functionToString(it);
    };
  }

  var inspectSource$2 = store$1.inspectSource;

  var uncurryThis$n = functionUncurryThis;
  var fails$o = fails$w;
  var isCallable$c = isCallable$m;
  var classof$d = classof$e;
  var getBuiltIn$d = getBuiltIn$f;
  var inspectSource$1 = inspectSource$2;

  var noop = function () {  };
  var empty = [];
  var construct$4 = getBuiltIn$d('Reflect', 'construct');
  var constructorRegExp = /^\s*(?:class|function)\b/;
  var exec$2 = uncurryThis$n(constructorRegExp.exec);
  var INCORRECT_TO_STRING = !constructorRegExp.exec(noop);

  var isConstructorModern = function isConstructor(argument) {
    if (!isCallable$c(argument)) return false;
    try {
      construct$4(noop, empty, argument);
      return true;
    } catch (error) {
      return false;
    }
  };

  var isConstructorLegacy = function isConstructor(argument) {
    if (!isCallable$c(argument)) return false;
    switch (classof$d(argument)) {
      case 'AsyncFunction':
      case 'GeneratorFunction':
      case 'AsyncGeneratorFunction': return false;
    }
    try {
      
      
      
      return INCORRECT_TO_STRING || !!exec$2(constructorRegExp, inspectSource$1(argument));
    } catch (error) {
      return true;
    }
  };

  isConstructorLegacy.sham = true;

  
  
  var isConstructor$4 = !construct$4 || fails$o(function () {
    var called;
    return isConstructorModern(isConstructorModern.call)
      || !isConstructorModern(Object)
      || !isConstructorModern(function () { called = true; })
      || called;
  }) ? isConstructorLegacy : isConstructorModern;

  var isArray$e = isArray$f;
  var isConstructor$3 = isConstructor$4;
  var isObject$d = isObject$i;
  var wellKnownSymbol$k = wellKnownSymbol$o;

  var SPECIES$5 = wellKnownSymbol$k('species');
  var $Array$3 = Array;

  
  
  var arraySpeciesConstructor$1 = function (originalArray) {
    var C;
    if (isArray$e(originalArray)) {
      C = originalArray.constructor;
      
      if (isConstructor$3(C) && (C === $Array$3 || isArray$e(C.prototype))) C = undefined;
      else if (isObject$d(C)) {
        C = C[SPECIES$5];
        if (C === null) C = undefined;
      }
    } return C === undefined ? $Array$3 : C;
  };

  var arraySpeciesConstructor = arraySpeciesConstructor$1;

  
  
  var arraySpeciesCreate$4 = function (originalArray, length) {
    return new (arraySpeciesConstructor(originalArray))(length === 0 ? 0 : length);
  };

  var fails$n = fails$w;
  var wellKnownSymbol$j = wellKnownSymbol$o;
  var V8_VERSION$2 = engineV8Version;

  var SPECIES$4 = wellKnownSymbol$j('species');

  var arrayMethodHasSpeciesSupport$5 = function (METHOD_NAME) {
    
    
    
    return V8_VERSION$2 >= 51 || !fails$n(function () {
      var array = [];
      var constructor = array.constructor = {};
      constructor[SPECIES$4] = function () {
        return { foo: 1 };
      };
      return array[METHOD_NAME](Boolean).foo !== 1;
    });
  };

  var $$S = _export;
  var fails$m = fails$w;
  var isArray$d = isArray$f;
  var isObject$c = isObject$i;
  var toObject$c = toObject$e;
  var lengthOfArrayLike$c = lengthOfArrayLike$d;
  var doesNotExceedSafeInteger$2 = doesNotExceedSafeInteger$3;
  var createProperty$5 = createProperty$6;
  var arraySpeciesCreate$3 = arraySpeciesCreate$4;
  var arrayMethodHasSpeciesSupport$4 = arrayMethodHasSpeciesSupport$5;
  var wellKnownSymbol$i = wellKnownSymbol$o;
  var V8_VERSION$1 = engineV8Version;

  var IS_CONCAT_SPREADABLE = wellKnownSymbol$i('isConcatSpreadable');

  
  
  
  var IS_CONCAT_SPREADABLE_SUPPORT = V8_VERSION$1 >= 51 || !fails$m(function () {
    var array = [];
    array[IS_CONCAT_SPREADABLE] = false;
    return array.concat()[0] !== array;
  });

  var isConcatSpreadable = function (O) {
    if (!isObject$c(O)) return false;
    var spreadable = O[IS_CONCAT_SPREADABLE];
    return spreadable !== undefined ? !!spreadable : isArray$d(O);
  };

  var FORCED$7 = !IS_CONCAT_SPREADABLE_SUPPORT || !arrayMethodHasSpeciesSupport$4('concat');

  
  
  
  $$S({ target: 'Array', proto: true, arity: 1, forced: FORCED$7 }, {
    
    concat: function concat(arg) {
      var O = toObject$c(this);
      var A = arraySpeciesCreate$3(O, 0);
      var n = 0;
      var i, k, length, len, E;
      for (i = -1, length = arguments.length; i < length; i++) {
        E = i === -1 ? O : arguments[i];
        if (isConcatSpreadable(E)) {
          len = lengthOfArrayLike$c(E);
          doesNotExceedSafeInteger$2(n + len);
          for (k = 0; k < len; k++, n++) if (k in E) createProperty$5(A, n, E[k]);
        } else {
          doesNotExceedSafeInteger$2(n + 1);
          createProperty$5(A, n++, E);
        }
      }
      A.length = n;
      return A;
    }
  });

  var classof$c = classof$e;

  var $String$2 = String;

  var toString$a = function (argument) {
    if (classof$c(argument) === 'Symbol') throw TypeError('Cannot convert a Symbol value to a string');
    return $String$2(argument);
  };

  var objectDefineProperties = {};

  var toIntegerOrInfinity$2 = toIntegerOrInfinity$4;

  var max$3 = Math.max;
  var min$1 = Math.min;

  
  
  
  var toAbsoluteIndex$5 = function (index, length) {
    var integer = toIntegerOrInfinity$2(index);
    return integer < 0 ? max$3(integer + length, 0) : min$1(integer, length);
  };

  var toIndexedObject$9 = toIndexedObject$b;
  var toAbsoluteIndex$4 = toAbsoluteIndex$5;
  var lengthOfArrayLike$b = lengthOfArrayLike$d;

  
  var createMethod$5 = function (IS_INCLUDES) {
    return function ($this, el, fromIndex) {
      var O = toIndexedObject$9($this);
      var length = lengthOfArrayLike$b(O);
      var index = toAbsoluteIndex$4(fromIndex, length);
      var value;
      
      
      if (IS_INCLUDES && el != el) while (length > index) {
        value = O[index++];
        
        if (value != value) return true;
      
      } else for (;length > index; index++) {
        if ((IS_INCLUDES || index in O) && O[index] === el) return IS_INCLUDES || index || 0;
      } return !IS_INCLUDES && -1;
    };
  };

  var arrayIncludes = {
    
    
    includes: createMethod$5(true),
    
    
    indexOf: createMethod$5(false)
  };

  var hiddenKeys$6 = {};

  var uncurryThis$m = functionUncurryThis;
  var hasOwn$g = hasOwnProperty_1;
  var toIndexedObject$8 = toIndexedObject$b;
  var indexOf$4 = arrayIncludes.indexOf;
  var hiddenKeys$5 = hiddenKeys$6;

  var push$7 = uncurryThis$m([].push);

  var objectKeysInternal = function (object, names) {
    var O = toIndexedObject$8(object);
    var i = 0;
    var result = [];
    var key;
    for (key in O) !hasOwn$g(hiddenKeys$5, key) && hasOwn$g(O, key) && push$7(result, key);
    
    while (names.length > i) if (hasOwn$g(O, key = names[i++])) {
      ~indexOf$4(result, key) || push$7(result, key);
    }
    return result;
  };

  
  var enumBugKeys$3 = [
    'constructor',
    'hasOwnProperty',
    'isPrototypeOf',
    'propertyIsEnumerable',
    'toLocaleString',
    'toString',
    'valueOf'
  ];

  var internalObjectKeys$1 = objectKeysInternal;
  var enumBugKeys$2 = enumBugKeys$3;

  
  
  
  var objectKeys$4 = Object.keys || function keys(O) {
    return internalObjectKeys$1(O, enumBugKeys$2);
  };

  var DESCRIPTORS$c = descriptors;
  var V8_PROTOTYPE_DEFINE_BUG = v8PrototypeDefineBug;
  var definePropertyModule$2 = objectDefineProperty;
  var anObject$b = anObject$d;
  var toIndexedObject$7 = toIndexedObject$b;
  var objectKeys$3 = objectKeys$4;

  
  
  
  objectDefineProperties.f = DESCRIPTORS$c && !V8_PROTOTYPE_DEFINE_BUG ? Object.defineProperties : function defineProperties(O, Properties) {
    anObject$b(O);
    var props = toIndexedObject$7(Properties);
    var keys = objectKeys$3(Properties);
    var length = keys.length;
    var index = 0;
    var key;
    while (length > index) definePropertyModule$2.f(O, key = keys[index++], props[key]);
    return O;
  };

  var getBuiltIn$c = getBuiltIn$f;

  var html$2 = getBuiltIn$c('document', 'documentElement');

  var shared$5 = sharedExports;
  var uid$2 = uid$4;

  var keys$7 = shared$5('keys');

  var sharedKey$4 = function (key) {
    return keys$7[key] || (keys$7[key] = uid$2(key));
  };

  

  var anObject$a = anObject$d;
  var definePropertiesModule$1 = objectDefineProperties;
  var enumBugKeys$1 = enumBugKeys$3;
  var hiddenKeys$4 = hiddenKeys$6;
  var html$1 = html$2;
  var documentCreateElement = documentCreateElement$1;
  var sharedKey$3 = sharedKey$4;

  var GT = '>';
  var LT = '<';
  var PROTOTYPE$1 = 'prototype';
  var SCRIPT = 'script';
  var IE_PROTO$1 = sharedKey$3('IE_PROTO');

  var EmptyConstructor = function () {  };

  var scriptTag = function (content) {
    return LT + SCRIPT + GT + content + LT + '/' + SCRIPT + GT;
  };

  
  var NullProtoObjectViaActiveX = function (activeXDocument) {
    activeXDocument.write(scriptTag(''));
    activeXDocument.close();
    var temp = activeXDocument.parentWindow.Object;
    activeXDocument = null; 
    return temp;
  };

  
  var NullProtoObjectViaIFrame = function () {
    
    var iframe = documentCreateElement('iframe');
    var JS = 'java' + SCRIPT + ':';
    var iframeDocument;
    iframe.style.display = 'none';
    html$1.appendChild(iframe);
    
    iframe.src = String(JS);
    iframeDocument = iframe.contentWindow.document;
    iframeDocument.open();
    iframeDocument.write(scriptTag('document.F=Object'));
    iframeDocument.close();
    return iframeDocument.F;
  };

  
  
  
  
  
  var activeXDocument;
  var NullProtoObject = function () {
    try {
      activeXDocument = new ActiveXObject('htmlfile');
    } catch (error) {  }
    NullProtoObject = typeof document != 'undefined'
      ? document.domain && activeXDocument
        ? NullProtoObjectViaActiveX(activeXDocument) 
        : NullProtoObjectViaIFrame()
      : NullProtoObjectViaActiveX(activeXDocument); 
    var length = enumBugKeys$1.length;
    while (length--) delete NullProtoObject[PROTOTYPE$1][enumBugKeys$1[length]];
    return NullProtoObject();
  };

  hiddenKeys$4[IE_PROTO$1] = true;

  
  
  
  var objectCreate = Object.create || function create(O, Properties) {
    var result;
    if (O !== null) {
      EmptyConstructor[PROTOTYPE$1] = anObject$a(O);
      result = new EmptyConstructor();
      EmptyConstructor[PROTOTYPE$1] = null;
      
      result[IE_PROTO$1] = O;
    } else result = NullProtoObject();
    return Properties === undefined ? result : definePropertiesModule$1.f(result, Properties);
  };

  var objectGetOwnPropertyNames = {};

  var internalObjectKeys = objectKeysInternal;
  var enumBugKeys = enumBugKeys$3;

  var hiddenKeys$3 = enumBugKeys.concat('length', 'prototype');

  
  
  
  objectGetOwnPropertyNames.f = Object.getOwnPropertyNames || function getOwnPropertyNames(O) {
    return internalObjectKeys(O, hiddenKeys$3);
  };

  var objectGetOwnPropertyNamesExternal = {};

  var toAbsoluteIndex$3 = toAbsoluteIndex$5;
  var lengthOfArrayLike$a = lengthOfArrayLike$d;
  var createProperty$4 = createProperty$6;

  var $Array$2 = Array;
  var max$2 = Math.max;

  var arraySliceSimple = function (O, start, end) {
    var length = lengthOfArrayLike$a(O);
    var k = toAbsoluteIndex$3(start, length);
    var fin = toAbsoluteIndex$3(end === undefined ? length : end, length);
    var result = $Array$2(max$2(fin - k, 0));
    for (var n = 0; k < fin; k++, n++) createProperty$4(result, n, O[k]);
    result.length = n;
    return result;
  };

  

  var classof$b = classofRaw$2;
  var toIndexedObject$6 = toIndexedObject$b;
  var $getOwnPropertyNames$1 = objectGetOwnPropertyNames.f;
  var arraySlice$6 = arraySliceSimple;

  var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
    ? Object.getOwnPropertyNames(window) : [];

  var getWindowNames = function (it) {
    try {
      return $getOwnPropertyNames$1(it);
    } catch (error) {
      return arraySlice$6(windowNames);
    }
  };

  
  objectGetOwnPropertyNamesExternal.f = function getOwnPropertyNames(it) {
    return windowNames && classof$b(it) == 'Window'
      ? getWindowNames(it)
      : $getOwnPropertyNames$1(toIndexedObject$6(it));
  };

  var objectGetOwnPropertySymbols = {};

  
  objectGetOwnPropertySymbols.f = Object.getOwnPropertySymbols;

  var createNonEnumerableProperty$7 = createNonEnumerableProperty$9;

  var defineBuiltIn$6 = function (target, key, value, options) {
    if (options && options.enumerable) target[key] = value;
    else createNonEnumerableProperty$7(target, key, value);
    return target;
  };

  var defineProperty$6 = objectDefineProperty;

  var defineBuiltInAccessor$3 = function (target, name, descriptor) {
    return defineProperty$6.f(target, name, descriptor);
  };

  var wellKnownSymbolWrapped = {};

  var wellKnownSymbol$h = wellKnownSymbol$o;

  wellKnownSymbolWrapped.f = wellKnownSymbol$h;

  var path$n = path$r;
  var hasOwn$f = hasOwnProperty_1;
  var wrappedWellKnownSymbolModule$1 = wellKnownSymbolWrapped;
  var defineProperty$5 = objectDefineProperty.f;

  var wellKnownSymbolDefine = function (NAME) {
    var Symbol = path$n.Symbol || (path$n.Symbol = {});
    if (!hasOwn$f(Symbol, NAME)) defineProperty$5(Symbol, NAME, {
      value: wrappedWellKnownSymbolModule$1.f(NAME)
    });
  };

  var call$e = functionCall;
  var getBuiltIn$b = getBuiltIn$f;
  var wellKnownSymbol$g = wellKnownSymbol$o;
  var defineBuiltIn$5 = defineBuiltIn$6;

  var symbolDefineToPrimitive = function () {
    var Symbol = getBuiltIn$b('Symbol');
    var SymbolPrototype = Symbol && Symbol.prototype;
    var valueOf = SymbolPrototype && SymbolPrototype.valueOf;
    var TO_PRIMITIVE = wellKnownSymbol$g('toPrimitive');

    if (SymbolPrototype && !SymbolPrototype[TO_PRIMITIVE]) {
      
      
      
      defineBuiltIn$5(SymbolPrototype, TO_PRIMITIVE, function (hint) {
        return call$e(valueOf, this);
      }, { arity: 1 });
    }
  };

  var TO_STRING_TAG_SUPPORT$1 = toStringTagSupport;
  var classof$a = classof$e;

  
  
  var objectToString = TO_STRING_TAG_SUPPORT$1 ? {}.toString : function toString() {
    return '[object ' + classof$a(this) + ']';
  };

  var TO_STRING_TAG_SUPPORT = toStringTagSupport;
  var defineProperty$4 = objectDefineProperty.f;
  var createNonEnumerableProperty$6 = createNonEnumerableProperty$9;
  var hasOwn$e = hasOwnProperty_1;
  var toString$9 = objectToString;
  var wellKnownSymbol$f = wellKnownSymbol$o;

  var TO_STRING_TAG$2 = wellKnownSymbol$f('toStringTag');

  var setToStringTag$7 = function (it, TAG, STATIC, SET_METHOD) {
    if (it) {
      var target = STATIC ? it : it.prototype;
      if (!hasOwn$e(target, TO_STRING_TAG$2)) {
        defineProperty$4(target, TO_STRING_TAG$2, { configurable: true, value: TAG });
      }
      if (SET_METHOD && !TO_STRING_TAG_SUPPORT) {
        createNonEnumerableProperty$6(target, 'toString', toString$9);
      }
    }
  };

  var global$f = global$n;
  var isCallable$b = isCallable$m;

  var WeakMap$1 = global$f.WeakMap;

  var weakMapBasicDetection = isCallable$b(WeakMap$1) && /native code/.test(String(WeakMap$1));

  var NATIVE_WEAK_MAP = weakMapBasicDetection;
  var global$e = global$n;
  var isObject$b = isObject$i;
  var createNonEnumerableProperty$5 = createNonEnumerableProperty$9;
  var hasOwn$d = hasOwnProperty_1;
  var shared$4 = sharedStore;
  var sharedKey$2 = sharedKey$4;
  var hiddenKeys$2 = hiddenKeys$6;

  var OBJECT_ALREADY_INITIALIZED = 'Object already initialized';
  var TypeError$3 = global$e.TypeError;
  var WeakMap = global$e.WeakMap;
  var set$4, get, has;

  var enforce = function (it) {
    return has(it) ? get(it) : set$4(it, {});
  };

  var getterFor = function (TYPE) {
    return function (it) {
      var state;
      if (!isObject$b(it) || (state = get(it)).type !== TYPE) {
        throw TypeError$3('Incompatible receiver, ' + TYPE + ' required');
      } return state;
    };
  };

  if (NATIVE_WEAK_MAP || shared$4.state) {
    var store = shared$4.state || (shared$4.state = new WeakMap());
    
    store.get = store.get;
    store.has = store.has;
    store.set = store.set;
    
    set$4 = function (it, metadata) {
      if (store.has(it)) throw TypeError$3(OBJECT_ALREADY_INITIALIZED);
      metadata.facade = it;
      store.set(it, metadata);
      return metadata;
    };
    get = function (it) {
      return store.get(it) || {};
    };
    has = function (it) {
      return store.has(it);
    };
  } else {
    var STATE = sharedKey$2('state');
    hiddenKeys$2[STATE] = true;
    set$4 = function (it, metadata) {
      if (hasOwn$d(it, STATE)) throw TypeError$3(OBJECT_ALREADY_INITIALIZED);
      metadata.facade = it;
      createNonEnumerableProperty$5(it, STATE, metadata);
      return metadata;
    };
    get = function (it) {
      return hasOwn$d(it, STATE) ? it[STATE] : {};
    };
    has = function (it) {
      return hasOwn$d(it, STATE);
    };
  }

  var internalState = {
    set: set$4,
    get: get,
    has: has,
    enforce: enforce,
    getterFor: getterFor
  };

  var bind$h = functionBindContext;
  var uncurryThis$l = functionUncurryThis;
  var IndexedObject$2 = indexedObject;
  var toObject$b = toObject$e;
  var lengthOfArrayLike$9 = lengthOfArrayLike$d;
  var arraySpeciesCreate$2 = arraySpeciesCreate$4;

  var push$6 = uncurryThis$l([].push);

  
  var createMethod$4 = function (TYPE) {
    var IS_MAP = TYPE == 1;
    var IS_FILTER = TYPE == 2;
    var IS_SOME = TYPE == 3;
    var IS_EVERY = TYPE == 4;
    var IS_FIND_INDEX = TYPE == 6;
    var IS_FILTER_REJECT = TYPE == 7;
    var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
    return function ($this, callbackfn, that, specificCreate) {
      var O = toObject$b($this);
      var self = IndexedObject$2(O);
      var boundFunction = bind$h(callbackfn, that);
      var length = lengthOfArrayLike$9(self);
      var index = 0;
      var create = specificCreate || arraySpeciesCreate$2;
      var target = IS_MAP ? create($this, length) : IS_FILTER || IS_FILTER_REJECT ? create($this, 0) : undefined;
      var value, result;
      for (;length > index; index++) if (NO_HOLES || index in self) {
        value = self[index];
        result = boundFunction(value, index, O);
        if (TYPE) {
          if (IS_MAP) target[index] = result; 
          else if (result) switch (TYPE) {
            case 3: return true;              
            case 5: return value;             
            case 6: return index;             
            case 2: push$6(target, value);      
          } else switch (TYPE) {
            case 4: return false;             
            case 7: push$6(target, value);      
          }
        }
      }
      return IS_FIND_INDEX ? -1 : IS_SOME || IS_EVERY ? IS_EVERY : target;
    };
  };

  var arrayIteration = {
    
    
    forEach: createMethod$4(0),
    
    
    map: createMethod$4(1),
    
    
    filter: createMethod$4(2),
    
    
    some: createMethod$4(3),
    
    
    every: createMethod$4(4),
    
    
    find: createMethod$4(5),
    
    
    findIndex: createMethod$4(6),
    
    
    filterReject: createMethod$4(7)
  };

  var $$R = _export;
  var global$d = global$n;
  var call$d = functionCall;
  var uncurryThis$k = functionUncurryThis;
  var DESCRIPTORS$b = descriptors;
  var NATIVE_SYMBOL$3 = symbolConstructorDetection;
  var fails$l = fails$w;
  var hasOwn$c = hasOwnProperty_1;
  var isPrototypeOf$m = objectIsPrototypeOf;
  var anObject$9 = anObject$d;
  var toIndexedObject$5 = toIndexedObject$b;
  var toPropertyKey = toPropertyKey$4;
  var $toString = toString$a;
  var createPropertyDescriptor$3 = createPropertyDescriptor$7;
  var nativeObjectCreate = objectCreate;
  var objectKeys$2 = objectKeys$4;
  var getOwnPropertyNamesModule$2 = objectGetOwnPropertyNames;
  var getOwnPropertyNamesExternal = objectGetOwnPropertyNamesExternal;
  var getOwnPropertySymbolsModule$3 = objectGetOwnPropertySymbols;
  var getOwnPropertyDescriptorModule$2 = objectGetOwnPropertyDescriptor;
  var definePropertyModule$1 = objectDefineProperty;
  var definePropertiesModule = objectDefineProperties;
  var propertyIsEnumerableModule$1 = objectPropertyIsEnumerable;
  var defineBuiltIn$4 = defineBuiltIn$6;
  var defineBuiltInAccessor$2 = defineBuiltInAccessor$3;
  var shared$3 = sharedExports;
  var sharedKey$1 = sharedKey$4;
  var hiddenKeys$1 = hiddenKeys$6;
  var uid$1 = uid$4;
  var wellKnownSymbol$e = wellKnownSymbol$o;
  var wrappedWellKnownSymbolModule = wellKnownSymbolWrapped;
  var defineWellKnownSymbol$l = wellKnownSymbolDefine;
  var defineSymbolToPrimitive$1 = symbolDefineToPrimitive;
  var setToStringTag$6 = setToStringTag$7;
  var InternalStateModule$5 = internalState;
  var $forEach$1 = arrayIteration.forEach;

  var HIDDEN = sharedKey$1('hidden');
  var SYMBOL = 'Symbol';
  var PROTOTYPE = 'prototype';

  var setInternalState$5 = InternalStateModule$5.set;
  var getInternalState$2 = InternalStateModule$5.getterFor(SYMBOL);

  var ObjectPrototype$2 = Object[PROTOTYPE];
  var $Symbol = global$d.Symbol;
  var SymbolPrototype = $Symbol && $Symbol[PROTOTYPE];
  var TypeError$2 = global$d.TypeError;
  var QObject = global$d.QObject;
  var nativeGetOwnPropertyDescriptor$1 = getOwnPropertyDescriptorModule$2.f;
  var nativeDefineProperty = definePropertyModule$1.f;
  var nativeGetOwnPropertyNames = getOwnPropertyNamesExternal.f;
  var nativePropertyIsEnumerable = propertyIsEnumerableModule$1.f;
  var push$5 = uncurryThis$k([].push);

  var AllSymbols = shared$3('symbols');
  var ObjectPrototypeSymbols = shared$3('op-symbols');
  var WellKnownSymbolsStore$1 = shared$3('wks');

  
  var USE_SETTER = !QObject || !QObject[PROTOTYPE] || !QObject[PROTOTYPE].findChild;

  
  var setSymbolDescriptor = DESCRIPTORS$b && fails$l(function () {
    return nativeObjectCreate(nativeDefineProperty({}, 'a', {
      get: function () { return nativeDefineProperty(this, 'a', { value: 7 }).a; }
    })).a != 7;
  }) ? function (O, P, Attributes) {
    var ObjectPrototypeDescriptor = nativeGetOwnPropertyDescriptor$1(ObjectPrototype$2, P);
    if (ObjectPrototypeDescriptor) delete ObjectPrototype$2[P];
    nativeDefineProperty(O, P, Attributes);
    if (ObjectPrototypeDescriptor && O !== ObjectPrototype$2) {
      nativeDefineProperty(ObjectPrototype$2, P, ObjectPrototypeDescriptor);
    }
  } : nativeDefineProperty;

  var wrap = function (tag, description) {
    var symbol = AllSymbols[tag] = nativeObjectCreate(SymbolPrototype);
    setInternalState$5(symbol, {
      type: SYMBOL,
      tag: tag,
      description: description
    });
    if (!DESCRIPTORS$b) symbol.description = description;
    return symbol;
  };

  var $defineProperty = function defineProperty(O, P, Attributes) {
    if (O === ObjectPrototype$2) $defineProperty(ObjectPrototypeSymbols, P, Attributes);
    anObject$9(O);
    var key = toPropertyKey(P);
    anObject$9(Attributes);
    if (hasOwn$c(AllSymbols, key)) {
      if (!Attributes.enumerable) {
        if (!hasOwn$c(O, HIDDEN)) nativeDefineProperty(O, HIDDEN, createPropertyDescriptor$3(1, {}));
        O[HIDDEN][key] = true;
      } else {
        if (hasOwn$c(O, HIDDEN) && O[HIDDEN][key]) O[HIDDEN][key] = false;
        Attributes = nativeObjectCreate(Attributes, { enumerable: createPropertyDescriptor$3(0, false) });
      } return setSymbolDescriptor(O, key, Attributes);
    } return nativeDefineProperty(O, key, Attributes);
  };

  var $defineProperties = function defineProperties(O, Properties) {
    anObject$9(O);
    var properties = toIndexedObject$5(Properties);
    var keys = objectKeys$2(properties).concat($getOwnPropertySymbols(properties));
    $forEach$1(keys, function (key) {
      if (!DESCRIPTORS$b || call$d($propertyIsEnumerable$1, properties, key)) $defineProperty(O, key, properties[key]);
    });
    return O;
  };

  var $create = function create(O, Properties) {
    return Properties === undefined ? nativeObjectCreate(O) : $defineProperties(nativeObjectCreate(O), Properties);
  };

  var $propertyIsEnumerable$1 = function propertyIsEnumerable(V) {
    var P = toPropertyKey(V);
    var enumerable = call$d(nativePropertyIsEnumerable, this, P);
    if (this === ObjectPrototype$2 && hasOwn$c(AllSymbols, P) && !hasOwn$c(ObjectPrototypeSymbols, P)) return false;
    return enumerable || !hasOwn$c(this, P) || !hasOwn$c(AllSymbols, P) || hasOwn$c(this, HIDDEN) && this[HIDDEN][P]
      ? enumerable : true;
  };

  var $getOwnPropertyDescriptor = function getOwnPropertyDescriptor(O, P) {
    var it = toIndexedObject$5(O);
    var key = toPropertyKey(P);
    if (it === ObjectPrototype$2 && hasOwn$c(AllSymbols, key) && !hasOwn$c(ObjectPrototypeSymbols, key)) return;
    var descriptor = nativeGetOwnPropertyDescriptor$1(it, key);
    if (descriptor && hasOwn$c(AllSymbols, key) && !(hasOwn$c(it, HIDDEN) && it[HIDDEN][key])) {
      descriptor.enumerable = true;
    }
    return descriptor;
  };

  var $getOwnPropertyNames = function getOwnPropertyNames(O) {
    var names = nativeGetOwnPropertyNames(toIndexedObject$5(O));
    var result = [];
    $forEach$1(names, function (key) {
      if (!hasOwn$c(AllSymbols, key) && !hasOwn$c(hiddenKeys$1, key)) push$5(result, key);
    });
    return result;
  };

  var $getOwnPropertySymbols = function (O) {
    var IS_OBJECT_PROTOTYPE = O === ObjectPrototype$2;
    var names = nativeGetOwnPropertyNames(IS_OBJECT_PROTOTYPE ? ObjectPrototypeSymbols : toIndexedObject$5(O));
    var result = [];
    $forEach$1(names, function (key) {
      if (hasOwn$c(AllSymbols, key) && (!IS_OBJECT_PROTOTYPE || hasOwn$c(ObjectPrototype$2, key))) {
        push$5(result, AllSymbols[key]);
      }
    });
    return result;
  };

  
  
  if (!NATIVE_SYMBOL$3) {
    $Symbol = function Symbol() {
      if (isPrototypeOf$m(SymbolPrototype, this)) throw TypeError$2('Symbol is not a constructor');
      var description = !arguments.length || arguments[0] === undefined ? undefined : $toString(arguments[0]);
      var tag = uid$1(description);
      var setter = function (value) {
        if (this === ObjectPrototype$2) call$d(setter, ObjectPrototypeSymbols, value);
        if (hasOwn$c(this, HIDDEN) && hasOwn$c(this[HIDDEN], tag)) this[HIDDEN][tag] = false;
        setSymbolDescriptor(this, tag, createPropertyDescriptor$3(1, value));
      };
      if (DESCRIPTORS$b && USE_SETTER) setSymbolDescriptor(ObjectPrototype$2, tag, { configurable: true, set: setter });
      return wrap(tag, description);
    };

    SymbolPrototype = $Symbol[PROTOTYPE];

    defineBuiltIn$4(SymbolPrototype, 'toString', function toString() {
      return getInternalState$2(this).tag;
    });

    defineBuiltIn$4($Symbol, 'withoutSetter', function (description) {
      return wrap(uid$1(description), description);
    });

    propertyIsEnumerableModule$1.f = $propertyIsEnumerable$1;
    definePropertyModule$1.f = $defineProperty;
    definePropertiesModule.f = $defineProperties;
    getOwnPropertyDescriptorModule$2.f = $getOwnPropertyDescriptor;
    getOwnPropertyNamesModule$2.f = getOwnPropertyNamesExternal.f = $getOwnPropertyNames;
    getOwnPropertySymbolsModule$3.f = $getOwnPropertySymbols;

    wrappedWellKnownSymbolModule.f = function (name) {
      return wrap(wellKnownSymbol$e(name), name);
    };

    if (DESCRIPTORS$b) {
      
      defineBuiltInAccessor$2(SymbolPrototype, 'description', {
        configurable: true,
        get: function description() {
          return getInternalState$2(this).description;
        }
      });
    }
  }

  $$R({ global: true, constructor: true, wrap: true, forced: !NATIVE_SYMBOL$3, sham: !NATIVE_SYMBOL$3 }, {
    Symbol: $Symbol
  });

  $forEach$1(objectKeys$2(WellKnownSymbolsStore$1), function (name) {
    defineWellKnownSymbol$l(name);
  });

  $$R({ target: SYMBOL, stat: true, forced: !NATIVE_SYMBOL$3 }, {
    useSetter: function () { USE_SETTER = true; },
    useSimple: function () { USE_SETTER = false; }
  });

  $$R({ target: 'Object', stat: true, forced: !NATIVE_SYMBOL$3, sham: !DESCRIPTORS$b }, {
    
    
    create: $create,
    
    
    defineProperty: $defineProperty,
    
    
    defineProperties: $defineProperties,
    
    
    getOwnPropertyDescriptor: $getOwnPropertyDescriptor
  });

  $$R({ target: 'Object', stat: true, forced: !NATIVE_SYMBOL$3 }, {
    
    
    getOwnPropertyNames: $getOwnPropertyNames
  });

  
  
  defineSymbolToPrimitive$1();

  
  
  setToStringTag$6($Symbol, SYMBOL);

  hiddenKeys$1[HIDDEN] = true;

  var NATIVE_SYMBOL$2 = symbolConstructorDetection;

  
  var symbolRegistryDetection = NATIVE_SYMBOL$2 && !!Symbol['for'] && !!Symbol.keyFor;

  var $$Q = _export;
  var getBuiltIn$a = getBuiltIn$f;
  var hasOwn$b = hasOwnProperty_1;
  var toString$8 = toString$a;
  var shared$2 = sharedExports;
  var NATIVE_SYMBOL_REGISTRY$1 = symbolRegistryDetection;

  var StringToSymbolRegistry = shared$2('string-to-symbol-registry');
  var SymbolToStringRegistry$1 = shared$2('symbol-to-string-registry');

  
  
  $$Q({ target: 'Symbol', stat: true, forced: !NATIVE_SYMBOL_REGISTRY$1 }, {
    'for': function (key) {
      var string = toString$8(key);
      if (hasOwn$b(StringToSymbolRegistry, string)) return StringToSymbolRegistry[string];
      var symbol = getBuiltIn$a('Symbol')(string);
      StringToSymbolRegistry[string] = symbol;
      SymbolToStringRegistry$1[symbol] = string;
      return symbol;
    }
  });

  var $$P = _export;
  var hasOwn$a = hasOwnProperty_1;
  var isSymbol$2 = isSymbol$5;
  var tryToString$4 = tryToString$6;
  var shared$1 = sharedExports;
  var NATIVE_SYMBOL_REGISTRY = symbolRegistryDetection;

  var SymbolToStringRegistry = shared$1('symbol-to-string-registry');

  
  
  $$P({ target: 'Symbol', stat: true, forced: !NATIVE_SYMBOL_REGISTRY }, {
    keyFor: function keyFor(sym) {
      if (!isSymbol$2(sym)) throw TypeError(tryToString$4(sym) + ' is not a symbol');
      if (hasOwn$a(SymbolToStringRegistry, sym)) return SymbolToStringRegistry[sym];
    }
  });

  var uncurryThis$j = functionUncurryThis;

  var arraySlice$5 = uncurryThis$j([].slice);

  var uncurryThis$i = functionUncurryThis;
  var isArray$c = isArray$f;
  var isCallable$a = isCallable$m;
  var classof$9 = classofRaw$2;
  var toString$7 = toString$a;

  var push$4 = uncurryThis$i([].push);

  var getJsonReplacerFunction = function (replacer) {
    if (isCallable$a(replacer)) return replacer;
    if (!isArray$c(replacer)) return;
    var rawLength = replacer.length;
    var keys = [];
    for (var i = 0; i < rawLength; i++) {
      var element = replacer[i];
      if (typeof element == 'string') push$4(keys, element);
      else if (typeof element == 'number' || classof$9(element) == 'Number' || classof$9(element) == 'String') push$4(keys, toString$7(element));
    }
    var keysLength = keys.length;
    var root = true;
    return function (key, value) {
      if (root) {
        root = false;
        return value;
      }
      if (isArray$c(this)) return value;
      for (var j = 0; j < keysLength; j++) if (keys[j] === key) return value;
    };
  };

  var $$O = _export;
  var getBuiltIn$9 = getBuiltIn$f;
  var apply$4 = functionApply;
  var call$c = functionCall;
  var uncurryThis$h = functionUncurryThis;
  var fails$k = fails$w;
  var isCallable$9 = isCallable$m;
  var isSymbol$1 = isSymbol$5;
  var arraySlice$4 = arraySlice$5;
  var getReplacerFunction = getJsonReplacerFunction;
  var NATIVE_SYMBOL$1 = symbolConstructorDetection;

  var $String$1 = String;
  var $stringify = getBuiltIn$9('JSON', 'stringify');
  var exec$1 = uncurryThis$h(/./.exec);
  var charAt$2 = uncurryThis$h(''.charAt);
  var charCodeAt$1 = uncurryThis$h(''.charCodeAt);
  var replace$2 = uncurryThis$h(''.replace);
  var numberToString = uncurryThis$h(1.0.toString);

  var tester = /[\uD800-\uDFFF]/g;
  var low = /^[\uD800-\uDBFF]$/;
  var hi = /^[\uDC00-\uDFFF]$/;

  var WRONG_SYMBOLS_CONVERSION = !NATIVE_SYMBOL$1 || fails$k(function () {
    var symbol = getBuiltIn$9('Symbol')();
    
    return $stringify([symbol]) != '[null]'
      
      || $stringify({ a: symbol }) != '{}'
      
      || $stringify(Object(symbol)) != '{}';
  });

  
  var ILL_FORMED_UNICODE = fails$k(function () {
    return $stringify('\uDF06\uD834') !== '"\\udf06\\ud834"'
      || $stringify('\uDEAD') !== '"\\udead"';
  });

  var stringifyWithSymbolsFix = function (it, replacer) {
    var args = arraySlice$4(arguments);
    var $replacer = getReplacerFunction(replacer);
    if (!isCallable$9($replacer) && (it === undefined || isSymbol$1(it))) return; 
    args[1] = function (key, value) {
      
      if (isCallable$9($replacer)) value = call$c($replacer, this, $String$1(key), value);
      if (!isSymbol$1(value)) return value;
    };
    return apply$4($stringify, null, args);
  };

  var fixIllFormed = function (match, offset, string) {
    var prev = charAt$2(string, offset - 1);
    var next = charAt$2(string, offset + 1);
    if ((exec$1(low, match) && !exec$1(hi, next)) || (exec$1(hi, match) && !exec$1(low, prev))) {
      return '\\u' + numberToString(charCodeAt$1(match, 0), 16);
    } return match;
  };

  if ($stringify) {
    
    
    $$O({ target: 'JSON', stat: true, arity: 3, forced: WRONG_SYMBOLS_CONVERSION || ILL_FORMED_UNICODE }, {
      
      stringify: function stringify(it, replacer, space) {
        var args = arraySlice$4(arguments);
        var result = apply$4(WRONG_SYMBOLS_CONVERSION ? stringifyWithSymbolsFix : $stringify, null, args);
        return ILL_FORMED_UNICODE && typeof result == 'string' ? replace$2(result, tester, fixIllFormed) : result;
      }
    });
  }

  var $$N = _export;
  var NATIVE_SYMBOL = symbolConstructorDetection;
  var fails$j = fails$w;
  var getOwnPropertySymbolsModule$2 = objectGetOwnPropertySymbols;
  var toObject$a = toObject$e;

  
  
  var FORCED$6 = !NATIVE_SYMBOL || fails$j(function () { getOwnPropertySymbolsModule$2.f(1); });

  
  
  $$N({ target: 'Object', stat: true, forced: FORCED$6 }, {
    getOwnPropertySymbols: function getOwnPropertySymbols(it) {
      var $getOwnPropertySymbols = getOwnPropertySymbolsModule$2.f;
      return $getOwnPropertySymbols ? $getOwnPropertySymbols(toObject$a(it)) : [];
    }
  });

  var defineWellKnownSymbol$k = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$k('asyncIterator');

  var defineWellKnownSymbol$j = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$j('hasInstance');

  var defineWellKnownSymbol$i = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$i('isConcatSpreadable');

  var defineWellKnownSymbol$h = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$h('iterator');

  var defineWellKnownSymbol$g = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$g('match');

  var defineWellKnownSymbol$f = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$f('matchAll');

  var defineWellKnownSymbol$e = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$e('replace');

  var defineWellKnownSymbol$d = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$d('search');

  var defineWellKnownSymbol$c = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$c('species');

  var defineWellKnownSymbol$b = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$b('split');

  var defineWellKnownSymbol$a = wellKnownSymbolDefine;
  var defineSymbolToPrimitive = symbolDefineToPrimitive;

  
  
  defineWellKnownSymbol$a('toPrimitive');

  
  
  defineSymbolToPrimitive();

  var getBuiltIn$8 = getBuiltIn$f;
  var defineWellKnownSymbol$9 = wellKnownSymbolDefine;
  var setToStringTag$5 = setToStringTag$7;

  
  
  defineWellKnownSymbol$9('toStringTag');

  
  
  setToStringTag$5(getBuiltIn$8('Symbol'), 'Symbol');

  var defineWellKnownSymbol$8 = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$8('unscopables');

  var global$c = global$n;
  var setToStringTag$4 = setToStringTag$7;

  
  
  setToStringTag$4(global$c.JSON, 'JSON', true);

  var path$m = path$r;

  var symbol$4 = path$m.Symbol;

  var iterators = {};

  var DESCRIPTORS$a = descriptors;
  var hasOwn$9 = hasOwnProperty_1;

  var FunctionPrototype$1 = Function.prototype;
  
  var getDescriptor = DESCRIPTORS$a && Object.getOwnPropertyDescriptor;

  var EXISTS = hasOwn$9(FunctionPrototype$1, 'name');
  
  var PROPER = EXISTS && (function something() {  }).name === 'something';
  var CONFIGURABLE = EXISTS && (!DESCRIPTORS$a || (DESCRIPTORS$a && getDescriptor(FunctionPrototype$1, 'name').configurable));

  var functionName = {
    EXISTS: EXISTS,
    PROPER: PROPER,
    CONFIGURABLE: CONFIGURABLE
  };

  var fails$i = fails$w;

  var correctPrototypeGetter = !fails$i(function () {
    function F() {  }
    F.prototype.constructor = null;
    
    return Object.getPrototypeOf(new F()) !== F.prototype;
  });

  var hasOwn$8 = hasOwnProperty_1;
  var isCallable$8 = isCallable$m;
  var toObject$9 = toObject$e;
  var sharedKey = sharedKey$4;
  var CORRECT_PROTOTYPE_GETTER$1 = correctPrototypeGetter;

  var IE_PROTO = sharedKey('IE_PROTO');
  var $Object = Object;
  var ObjectPrototype$1 = $Object.prototype;

  
  
  
  var objectGetPrototypeOf = CORRECT_PROTOTYPE_GETTER$1 ? $Object.getPrototypeOf : function (O) {
    var object = toObject$9(O);
    if (hasOwn$8(object, IE_PROTO)) return object[IE_PROTO];
    var constructor = object.constructor;
    if (isCallable$8(constructor) && object instanceof constructor) {
      return constructor.prototype;
    } return object instanceof $Object ? ObjectPrototype$1 : null;
  };

  var fails$h = fails$w;
  var isCallable$7 = isCallable$m;
  var isObject$a = isObject$i;
  var create$c = objectCreate;
  var getPrototypeOf$9 = objectGetPrototypeOf;
  var defineBuiltIn$3 = defineBuiltIn$6;
  var wellKnownSymbol$d = wellKnownSymbol$o;

  var ITERATOR$5 = wellKnownSymbol$d('iterator');
  var BUGGY_SAFARI_ITERATORS$1 = false;

  
  
  var IteratorPrototype$1, PrototypeOfArrayIteratorPrototype, arrayIterator;

  
  if ([].keys) {
    arrayIterator = [].keys();
    
    if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS$1 = true;
    else {
      PrototypeOfArrayIteratorPrototype = getPrototypeOf$9(getPrototypeOf$9(arrayIterator));
      if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype$1 = PrototypeOfArrayIteratorPrototype;
    }
  }

  var NEW_ITERATOR_PROTOTYPE = !isObject$a(IteratorPrototype$1) || fails$h(function () {
    var test = {};
    
    return IteratorPrototype$1[ITERATOR$5].call(test) !== test;
  });

  if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype$1 = {};
  else IteratorPrototype$1 = create$c(IteratorPrototype$1);

  
  
  if (!isCallable$7(IteratorPrototype$1[ITERATOR$5])) {
    defineBuiltIn$3(IteratorPrototype$1, ITERATOR$5, function () {
      return this;
    });
  }

  var iteratorsCore = {
    IteratorPrototype: IteratorPrototype$1,
    BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS$1
  };

  var IteratorPrototype = iteratorsCore.IteratorPrototype;
  var create$b = objectCreate;
  var createPropertyDescriptor$2 = createPropertyDescriptor$7;
  var setToStringTag$3 = setToStringTag$7;
  var Iterators$5 = iterators;

  var returnThis$1 = function () { return this; };

  var iteratorCreateConstructor = function (IteratorConstructor, NAME, next, ENUMERABLE_NEXT) {
    var TO_STRING_TAG = NAME + ' Iterator';
    IteratorConstructor.prototype = create$b(IteratorPrototype, { next: createPropertyDescriptor$2(+!ENUMERABLE_NEXT, next) });
    setToStringTag$3(IteratorConstructor, TO_STRING_TAG, false, true);
    Iterators$5[TO_STRING_TAG] = returnThis$1;
    return IteratorConstructor;
  };

  var uncurryThis$g = functionUncurryThis;
  var aCallable$b = aCallable$e;

  var functionUncurryThisAccessor = function (object, key, method) {
    try {
      
      return uncurryThis$g(aCallable$b(Object.getOwnPropertyDescriptor(object, key)[method]));
    } catch (error) {  }
  };

  var isCallable$6 = isCallable$m;

  var $String = String;
  var $TypeError$a = TypeError;

  var aPossiblePrototype$1 = function (argument) {
    if (typeof argument == 'object' || isCallable$6(argument)) return argument;
    throw $TypeError$a("Can't set " + $String(argument) + ' as a prototype');
  };

  

  var uncurryThisAccessor = functionUncurryThisAccessor;
  var anObject$8 = anObject$d;
  var aPossiblePrototype = aPossiblePrototype$1;

  
  
  
  
  var objectSetPrototypeOf = Object.setPrototypeOf || ('__proto__' in {} ? function () {
    var CORRECT_SETTER = false;
    var test = {};
    var setter;
    try {
      setter = uncurryThisAccessor(Object.prototype, '__proto__', 'set');
      setter(test, []);
      CORRECT_SETTER = test instanceof Array;
    } catch (error) {  }
    return function setPrototypeOf(O, proto) {
      anObject$8(O);
      aPossiblePrototype(proto);
      if (CORRECT_SETTER) setter(O, proto);
      else O.__proto__ = proto;
      return O;
    };
  }() : undefined);

  var $$M = _export;
  var call$b = functionCall;
  var FunctionName = functionName;
  var createIteratorConstructor = iteratorCreateConstructor;
  var getPrototypeOf$8 = objectGetPrototypeOf;
  var setToStringTag$2 = setToStringTag$7;
  var defineBuiltIn$2 = defineBuiltIn$6;
  var wellKnownSymbol$c = wellKnownSymbol$o;
  var Iterators$4 = iterators;
  var IteratorsCore = iteratorsCore;

  var PROPER_FUNCTION_NAME$1 = FunctionName.PROPER;
  var BUGGY_SAFARI_ITERATORS = IteratorsCore.BUGGY_SAFARI_ITERATORS;
  var ITERATOR$4 = wellKnownSymbol$c('iterator');
  var KEYS = 'keys';
  var VALUES = 'values';
  var ENTRIES = 'entries';

  var returnThis = function () { return this; };

  var iteratorDefine = function (Iterable, NAME, IteratorConstructor, next, DEFAULT, IS_SET, FORCED) {
    createIteratorConstructor(IteratorConstructor, NAME, next);

    var getIterationMethod = function (KIND) {
      if (KIND === DEFAULT && defaultIterator) return defaultIterator;
      if (!BUGGY_SAFARI_ITERATORS && KIND in IterablePrototype) return IterablePrototype[KIND];
      switch (KIND) {
        case KEYS: return function keys() { return new IteratorConstructor(this, KIND); };
        case VALUES: return function values() { return new IteratorConstructor(this, KIND); };
        case ENTRIES: return function entries() { return new IteratorConstructor(this, KIND); };
      } return function () { return new IteratorConstructor(this); };
    };

    var TO_STRING_TAG = NAME + ' Iterator';
    var INCORRECT_VALUES_NAME = false;
    var IterablePrototype = Iterable.prototype;
    var nativeIterator = IterablePrototype[ITERATOR$4]
      || IterablePrototype['@@iterator']
      || DEFAULT && IterablePrototype[DEFAULT];
    var defaultIterator = !BUGGY_SAFARI_ITERATORS && nativeIterator || getIterationMethod(DEFAULT);
    var anyNativeIterator = NAME == 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
    var CurrentIteratorPrototype, methods, KEY;

    
    if (anyNativeIterator) {
      CurrentIteratorPrototype = getPrototypeOf$8(anyNativeIterator.call(new Iterable()));
      if (CurrentIteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
        
        setToStringTag$2(CurrentIteratorPrototype, TO_STRING_TAG, true, true);
        Iterators$4[TO_STRING_TAG] = returnThis;
      }
    }

    
    if (PROPER_FUNCTION_NAME$1 && DEFAULT == VALUES && nativeIterator && nativeIterator.name !== VALUES) {
      {
        INCORRECT_VALUES_NAME = true;
        defaultIterator = function values() { return call$b(nativeIterator, this); };
      }
    }

    
    if (DEFAULT) {
      methods = {
        values: getIterationMethod(VALUES),
        keys: IS_SET ? defaultIterator : getIterationMethod(KEYS),
        entries: getIterationMethod(ENTRIES)
      };
      if (FORCED) for (KEY in methods) {
        if (BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME || !(KEY in IterablePrototype)) {
          defineBuiltIn$2(IterablePrototype, KEY, methods[KEY]);
        }
      } else $$M({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME }, methods);
    }

    
    if ((FORCED) && IterablePrototype[ITERATOR$4] !== defaultIterator) {
      defineBuiltIn$2(IterablePrototype, ITERATOR$4, defaultIterator, { name: DEFAULT });
    }
    Iterators$4[NAME] = defaultIterator;

    return methods;
  };

  
  
  var createIterResultObject$3 = function (value, done) {
    return { value: value, done: done };
  };

  var toIndexedObject$4 = toIndexedObject$b;
  var Iterators$3 = iterators;
  var InternalStateModule$4 = internalState;
  objectDefineProperty.f;
  var defineIterator$2 = iteratorDefine;
  var createIterResultObject$2 = createIterResultObject$3;

  var ARRAY_ITERATOR = 'Array Iterator';
  var setInternalState$4 = InternalStateModule$4.set;
  var getInternalState$1 = InternalStateModule$4.getterFor(ARRAY_ITERATOR);

  
  
  
  
  
  
  
  
  
  
  defineIterator$2(Array, 'Array', function (iterated, kind) {
    setInternalState$4(this, {
      type: ARRAY_ITERATOR,
      target: toIndexedObject$4(iterated), 
      index: 0,                          
      kind: kind                         
    });
  
  
  }, function () {
    var state = getInternalState$1(this);
    var target = state.target;
    var kind = state.kind;
    var index = state.index++;
    if (!target || index >= target.length) {
      state.target = undefined;
      return createIterResultObject$2(undefined, true);
    }
    if (kind == 'keys') return createIterResultObject$2(index, false);
    if (kind == 'values') return createIterResultObject$2(target[index], false);
    return createIterResultObject$2([index, target[index]], false);
  }, 'values');

  
  
  
  Iterators$3.Arguments = Iterators$3.Array;

  
  
  var domIterables = {
    CSSRuleList: 0,
    CSSStyleDeclaration: 0,
    CSSValueList: 0,
    ClientRectList: 0,
    DOMRectList: 0,
    DOMStringList: 0,
    DOMTokenList: 1,
    DataTransferItemList: 0,
    FileList: 0,
    HTMLAllCollection: 0,
    HTMLCollection: 0,
    HTMLFormElement: 0,
    HTMLSelectElement: 0,
    MediaList: 0,
    MimeTypeArray: 0,
    NamedNodeMap: 0,
    NodeList: 1,
    PaintRequestList: 0,
    Plugin: 0,
    PluginArray: 0,
    SVGLengthList: 0,
    SVGNumberList: 0,
    SVGPathSegList: 0,
    SVGPointList: 0,
    SVGStringList: 0,
    SVGTransformList: 0,
    SourceBufferList: 0,
    StyleSheetList: 0,
    TextTrackCueList: 0,
    TextTrackList: 0,
    TouchList: 0
  };

  var DOMIterables$4 = domIterables;
  var global$b = global$n;
  var classof$8 = classof$e;
  var createNonEnumerableProperty$4 = createNonEnumerableProperty$9;
  var Iterators$2 = iterators;
  var wellKnownSymbol$b = wellKnownSymbol$o;

  var TO_STRING_TAG$1 = wellKnownSymbol$b('toStringTag');

  for (var COLLECTION_NAME in DOMIterables$4) {
    var Collection = global$b[COLLECTION_NAME];
    var CollectionPrototype = Collection && Collection.prototype;
    if (CollectionPrototype && classof$8(CollectionPrototype) !== TO_STRING_TAG$1) {
      createNonEnumerableProperty$4(CollectionPrototype, TO_STRING_TAG$1, COLLECTION_NAME);
    }
    Iterators$2[COLLECTION_NAME] = Iterators$2.Array;
  }

  var parent$19 = symbol$4;


  var symbol$3 = parent$19;

  var defineWellKnownSymbol$7 = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$7('dispose');

  var parent$18 = symbol$3;



  var symbol$2 = parent$18;

  var defineWellKnownSymbol$6 = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$6('asyncDispose');

  var $$L = _export;
  var getBuiltIn$7 = getBuiltIn$f;
  var uncurryThis$f = functionUncurryThis;

  var Symbol$3 = getBuiltIn$7('Symbol');
  var keyFor = Symbol$3.keyFor;
  var thisSymbolValue$1 = uncurryThis$f(Symbol$3.prototype.valueOf);

  
  
  $$L({ target: 'Symbol', stat: true }, {
    isRegistered: function isRegistered(value) {
      try {
        return keyFor(thisSymbolValue$1(value)) !== undefined;
      } catch (error) {
        return false;
      }
    }
  });

  var $$K = _export;
  var shared = sharedExports;
  var getBuiltIn$6 = getBuiltIn$f;
  var uncurryThis$e = functionUncurryThis;
  var isSymbol = isSymbol$5;
  var wellKnownSymbol$a = wellKnownSymbol$o;

  var Symbol$2 = getBuiltIn$6('Symbol');
  var $isWellKnown = Symbol$2.isWellKnown;
  var getOwnPropertyNames = getBuiltIn$6('Object', 'getOwnPropertyNames');
  var thisSymbolValue = uncurryThis$e(Symbol$2.prototype.valueOf);
  var WellKnownSymbolsStore = shared('wks');

  for (var i = 0, symbolKeys = getOwnPropertyNames(Symbol$2), symbolKeysLength = symbolKeys.length; i < symbolKeysLength; i++) {
    
    try {
      var symbolKey = symbolKeys[i];
      if (isSymbol(Symbol$2[symbolKey])) wellKnownSymbol$a(symbolKey);
    } catch (error) {  }
  }

  
  
  
  $$K({ target: 'Symbol', stat: true, forced: true }, {
    isWellKnown: function isWellKnown(value) {
      if ($isWellKnown && $isWellKnown(value)) return true;
      try {
        var symbol = thisSymbolValue(value);
        for (var j = 0, keys = getOwnPropertyNames(WellKnownSymbolsStore), keysLength = keys.length; j < keysLength; j++) {
          if (WellKnownSymbolsStore[keys[j]] == symbol) return true;
        }
      } catch (error) {  }
      return false;
    }
  });

  var defineWellKnownSymbol$5 = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$5('matcher');

  var defineWellKnownSymbol$4 = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$4('metadataKey');

  var defineWellKnownSymbol$3 = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$3('observable');

  
  var defineWellKnownSymbol$2 = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$2('metadata');

  
  var defineWellKnownSymbol$1 = wellKnownSymbolDefine;

  
  
  defineWellKnownSymbol$1('patternMatch');

  
  var defineWellKnownSymbol = wellKnownSymbolDefine;

  defineWellKnownSymbol('replaceAll');

  var parent$17 = symbol$2;






  




  var symbol$1 = parent$17;

  (function (module) {
  	module.exports = symbol$1;
  } (symbol$5));

  (function (module) {
  	module.exports = symbolExports$1;
  } (symbol$6));

  var _Symbol$1 = getDefaultExportFromCjs(symbolExports$2);

  var iteratorExports$2 = {};
  var iterator$6 = {
    get exports(){ return iteratorExports$2; },
    set exports(v){ iteratorExports$2 = v; },
  };

  var iteratorExports$1 = {};
  var iterator$5 = {
    get exports(){ return iteratorExports$1; },
    set exports(v){ iteratorExports$1 = v; },
  };

  var uncurryThis$d = functionUncurryThis;
  var toIntegerOrInfinity$1 = toIntegerOrInfinity$4;
  var toString$6 = toString$a;
  var requireObjectCoercible$2 = requireObjectCoercible$5;

  var charAt$1 = uncurryThis$d(''.charAt);
  var charCodeAt = uncurryThis$d(''.charCodeAt);
  var stringSlice = uncurryThis$d(''.slice);

  var createMethod$3 = function (CONVERT_TO_STRING) {
    return function ($this, pos) {
      var S = toString$6(requireObjectCoercible$2($this));
      var position = toIntegerOrInfinity$1(pos);
      var size = S.length;
      var first, second;
      if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
      first = charCodeAt(S, position);
      return first < 0xD800 || first > 0xDBFF || position + 1 === size
        || (second = charCodeAt(S, position + 1)) < 0xDC00 || second > 0xDFFF
          ? CONVERT_TO_STRING
            ? charAt$1(S, position)
            : first
          : CONVERT_TO_STRING
            ? stringSlice(S, position, position + 2)
            : (first - 0xD800 << 10) + (second - 0xDC00) + 0x10000;
    };
  };

  var stringMultibyte = {
    
    
    codeAt: createMethod$3(false),
    
    
    charAt: createMethod$3(true)
  };

  var charAt = stringMultibyte.charAt;
  var toString$5 = toString$a;
  var InternalStateModule$3 = internalState;
  var defineIterator$1 = iteratorDefine;
  var createIterResultObject$1 = createIterResultObject$3;

  var STRING_ITERATOR = 'String Iterator';
  var setInternalState$3 = InternalStateModule$3.set;
  var getInternalState = InternalStateModule$3.getterFor(STRING_ITERATOR);

  
  
  defineIterator$1(String, 'String', function (iterated) {
    setInternalState$3(this, {
      type: STRING_ITERATOR,
      string: toString$5(iterated),
      index: 0
    });
  
  
  }, function next() {
    var state = getInternalState(this);
    var string = state.string;
    var index = state.index;
    var point;
    if (index >= string.length) return createIterResultObject$1(undefined, true);
    point = charAt(string, index);
    state.index += point.length;
    return createIterResultObject$1(point, false);
  });

  var WrappedWellKnownSymbolModule$1 = wellKnownSymbolWrapped;

  var iterator$4 = WrappedWellKnownSymbolModule$1.f('iterator');

  var parent$16 = iterator$4;


  var iterator$3 = parent$16;

  var parent$15 = iterator$3;

  var iterator$2 = parent$15;

  var parent$14 = iterator$2;

  var iterator$1 = parent$14;

  (function (module) {
  	module.exports = iterator$1;
  } (iterator$5));

  (function (module) {
  	module.exports = iteratorExports$1;
  } (iterator$6));

  var _Symbol$iterator$2 = getDefaultExportFromCjs(iteratorExports$2);

  function _typeof$1(obj) {
    "@babel/helpers - typeof";

    return _typeof$1 = "function" == typeof _Symbol$1 && "symbol" == typeof _Symbol$iterator$2 ? function (obj) {
      return typeof obj;
    } : function (obj) {
      return obj && "function" == typeof _Symbol$1 && obj.constructor === _Symbol$1 && obj !== _Symbol$1.prototype ? "symbol" : typeof obj;
    }, _typeof$1(obj);
  }

  var toPrimitiveExports$1 = {};
  var toPrimitive$5 = {
    get exports(){ return toPrimitiveExports$1; },
    set exports(v){ toPrimitiveExports$1 = v; },
  };

  var toPrimitiveExports = {};
  var toPrimitive$4 = {
    get exports(){ return toPrimitiveExports; },
    set exports(v){ toPrimitiveExports = v; },
  };

  var WrappedWellKnownSymbolModule = wellKnownSymbolWrapped;

  var toPrimitive$3 = WrappedWellKnownSymbolModule.f('toPrimitive');

  var parent$13 = toPrimitive$3;

  var toPrimitive$2 = parent$13;

  var parent$12 = toPrimitive$2;

  var toPrimitive$1 = parent$12;

  var parent$11 = toPrimitive$1;

  var toPrimitive = parent$11;

  (function (module) {
  	module.exports = toPrimitive;
  } (toPrimitive$4));

  (function (module) {
  	module.exports = toPrimitiveExports;
  } (toPrimitive$5));

  var _Symbol$toPrimitive = getDefaultExportFromCjs(toPrimitiveExports$1);

  function _toPrimitive(input, hint) {
    if (_typeof$1(input) !== "object" || input === null) return input;
    var prim = input[_Symbol$toPrimitive];
    if (prim !== undefined) {
      var res = prim.call(input, hint || "default");
      if (_typeof$1(res) !== "object") return res;
      throw new TypeError("@@toPrimitive must return a primitive value.");
    }
    return (hint === "string" ? String : Number)(input);
  }

  function _toPropertyKey(arg) {
    var key = _toPrimitive(arg, "string");
    return _typeof$1(key) === "symbol" ? key : String(key);
  }

  function _defineProperties(target, props) {
    for (var i = 0; i < props.length; i++) {
      var descriptor = props[i];
      descriptor.enumerable = descriptor.enumerable || false;
      descriptor.configurable = true;
      if ("value" in descriptor) descriptor.writable = true;
      _Object$defineProperty$1(target, _toPropertyKey(descriptor.key), descriptor);
    }
  }
  function _createClass(Constructor, protoProps, staticProps) {
    if (protoProps) _defineProperties(Constructor.prototype, protoProps);
    if (staticProps) _defineProperties(Constructor, staticProps);
    _Object$defineProperty$1(Constructor, "prototype", {
      writable: false
    });
    return Constructor;
  }

  function _defineProperty(obj, key, value) {
    key = _toPropertyKey(key);
    if (key in obj) {
      _Object$defineProperty$1(obj, key, {
        value: value,
        enumerable: true,
        configurable: true,
        writable: true
      });
    } else {
      obj[key] = value;
    }
    return obj;
  }

  var bindExports$2 = {};
  var bind$g = {
    get exports(){ return bindExports$2; },
    set exports(v){ bindExports$2 = v; },
  };

  var uncurryThis$c = functionUncurryThis;
  var aCallable$a = aCallable$e;
  var isObject$9 = isObject$i;
  var hasOwn$7 = hasOwnProperty_1;
  var arraySlice$3 = arraySlice$5;
  var NATIVE_BIND = functionBindNative;

  var $Function = Function;
  var concat$6 = uncurryThis$c([].concat);
  var join = uncurryThis$c([].join);
  var factories = {};

  var construct$3 = function (C, argsLength, args) {
    if (!hasOwn$7(factories, argsLength)) {
      for (var list = [], i = 0; i < argsLength; i++) list[i] = 'a[' + i + ']';
      factories[argsLength] = $Function('C,a', 'return new C(' + join(list, ',') + ')');
    } return factories[argsLength](C, args);
  };

  
  
  
  var functionBind = NATIVE_BIND ? $Function.bind : function bind(that ) {
    var F = aCallable$a(this);
    var Prototype = F.prototype;
    var partArgs = arraySlice$3(arguments, 1);
    var boundFunction = function bound() {
      var args = concat$6(partArgs, arraySlice$3(arguments));
      return this instanceof boundFunction ? construct$3(F, args.length, args) : F.apply(that, args);
    };
    if (isObject$9(Prototype)) boundFunction.prototype = Prototype;
    return boundFunction;
  };

  
  var $$J = _export;
  var bind$f = functionBind;

  
  
  
  $$J({ target: 'Function', proto: true, forced: Function.bind !== bind$f }, {
    bind: bind$f
  });

  var path$l = path$r;

  var entryVirtual$k = function (CONSTRUCTOR) {
    return path$l[CONSTRUCTOR + 'Prototype'];
  };

  var entryVirtual$j = entryVirtual$k;

  var bind$e = entryVirtual$j('Function').bind;

  var isPrototypeOf$l = objectIsPrototypeOf;
  var method$h = bind$e;

  var FunctionPrototype = Function.prototype;

  var bind$d = function (it) {
    var own = it.bind;
    return it === FunctionPrototype || (isPrototypeOf$l(FunctionPrototype, it) && own === FunctionPrototype.bind) ? method$h : own;
  };

  var parent$10 = bind$d;

  var bind$c = parent$10;

  (function (module) {
  	module.exports = bind$c;
  } (bind$g));

  var _bindInstanceProperty$1 = getDefaultExportFromCjs(bindExports$2);

  var reduceExports = {};
  var reduce$3 = {
    get exports(){ return reduceExports; },
    set exports(v){ reduceExports = v; },
  };

  var aCallable$9 = aCallable$e;
  var toObject$8 = toObject$e;
  var IndexedObject$1 = indexedObject;
  var lengthOfArrayLike$8 = lengthOfArrayLike$d;

  var $TypeError$9 = TypeError;

  
  var createMethod$2 = function (IS_RIGHT) {
    return function (that, callbackfn, argumentsLength, memo) {
      aCallable$9(callbackfn);
      var O = toObject$8(that);
      var self = IndexedObject$1(O);
      var length = lengthOfArrayLike$8(O);
      var index = IS_RIGHT ? length - 1 : 0;
      var i = IS_RIGHT ? -1 : 1;
      if (argumentsLength < 2) while (true) {
        if (index in self) {
          memo = self[index];
          index += i;
          break;
        }
        index += i;
        if (IS_RIGHT ? index < 0 : length <= index) {
          throw $TypeError$9('Reduce of empty array with no initial value');
        }
      }
      for (;IS_RIGHT ? index >= 0 : length > index; index += i) if (index in self) {
        memo = callbackfn(memo, self[index], index, O);
      }
      return memo;
    };
  };

  var arrayReduce = {
    
    
    left: createMethod$2(false),
    
    
    right: createMethod$2(true)
  };

  var fails$g = fails$w;

  var arrayMethodIsStrict$5 = function (METHOD_NAME, argument) {
    var method = [][METHOD_NAME];
    return !!method && fails$g(function () {
      
      method.call(null, argument || function () { return 1; }, 1);
    });
  };

  var classof$7 = classofRaw$2;

  var engineIsNode = typeof process != 'undefined' && classof$7(process) == 'process';

  var $$I = _export;
  var $reduce = arrayReduce.left;
  var arrayMethodIsStrict$4 = arrayMethodIsStrict$5;
  var CHROME_VERSION = engineV8Version;
  var IS_NODE$4 = engineIsNode;

  
  
  var CHROME_BUG = !IS_NODE$4 && CHROME_VERSION > 79 && CHROME_VERSION < 83;
  var FORCED$5 = CHROME_BUG || !arrayMethodIsStrict$4('reduce');

  
  
  $$I({ target: 'Array', proto: true, forced: FORCED$5 }, {
    reduce: function reduce(callbackfn ) {
      var length = arguments.length;
      return $reduce(this, callbackfn, length, length > 1 ? arguments[1] : undefined);
    }
  });

  var entryVirtual$i = entryVirtual$k;

  var reduce$2 = entryVirtual$i('Array').reduce;

  var isPrototypeOf$k = objectIsPrototypeOf;
  var method$g = reduce$2;

  var ArrayPrototype$h = Array.prototype;

  var reduce$1 = function (it) {
    var own = it.reduce;
    return it === ArrayPrototype$h || (isPrototypeOf$k(ArrayPrototype$h, it) && own === ArrayPrototype$h.reduce) ? method$g : own;
  };

  var parent$$ = reduce$1;

  var reduce = parent$$;

  (function (module) {
  	module.exports = reduce;
  } (reduce$3));

  var _reduceInstanceProperty = getDefaultExportFromCjs(reduceExports);

  var filterExports = {};
  var filter$3 = {
    get exports(){ return filterExports; },
    set exports(v){ filterExports = v; },
  };

  var $$H = _export;
  var $filter = arrayIteration.filter;
  var arrayMethodHasSpeciesSupport$3 = arrayMethodHasSpeciesSupport$5;

  var HAS_SPECIES_SUPPORT$3 = arrayMethodHasSpeciesSupport$3('filter');

  
  
  
  $$H({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$3 }, {
    filter: function filter(callbackfn ) {
      return $filter(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  var entryVirtual$h = entryVirtual$k;

  var filter$2 = entryVirtual$h('Array').filter;

  var isPrototypeOf$j = objectIsPrototypeOf;
  var method$f = filter$2;

  var ArrayPrototype$g = Array.prototype;

  var filter$1 = function (it) {
    var own = it.filter;
    return it === ArrayPrototype$g || (isPrototypeOf$j(ArrayPrototype$g, it) && own === ArrayPrototype$g.filter) ? method$f : own;
  };

  var parent$_ = filter$1;

  var filter = parent$_;

  (function (module) {
  	module.exports = filter;
  } (filter$3));

  var _filterInstanceProperty = getDefaultExportFromCjs(filterExports);

  var mapExports$1 = {};
  var map$6 = {
    get exports(){ return mapExports$1; },
    set exports(v){ mapExports$1 = v; },
  };

  var $$G = _export;
  var $map = arrayIteration.map;
  var arrayMethodHasSpeciesSupport$2 = arrayMethodHasSpeciesSupport$5;

  var HAS_SPECIES_SUPPORT$2 = arrayMethodHasSpeciesSupport$2('map');

  
  
  
  $$G({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$2 }, {
    map: function map(callbackfn ) {
      return $map(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  var entryVirtual$g = entryVirtual$k;

  var map$5 = entryVirtual$g('Array').map;

  var isPrototypeOf$i = objectIsPrototypeOf;
  var method$e = map$5;

  var ArrayPrototype$f = Array.prototype;

  var map$4 = function (it) {
    var own = it.map;
    return it === ArrayPrototype$f || (isPrototypeOf$i(ArrayPrototype$f, it) && own === ArrayPrototype$f.map) ? method$e : own;
  };

  var parent$Z = map$4;

  var map$3 = parent$Z;

  (function (module) {
  	module.exports = map$3;
  } (map$6));

  var _mapInstanceProperty = getDefaultExportFromCjs(mapExports$1);

  var flatMapExports = {};
  var flatMap$3 = {
    get exports(){ return flatMapExports; },
    set exports(v){ flatMapExports = v; },
  };

  var isArray$b = isArray$f;
  var lengthOfArrayLike$7 = lengthOfArrayLike$d;
  var doesNotExceedSafeInteger$1 = doesNotExceedSafeInteger$3;
  var bind$b = functionBindContext;

  
  
  var flattenIntoArray$1 = function (target, original, source, sourceLen, start, depth, mapper, thisArg) {
    var targetIndex = start;
    var sourceIndex = 0;
    var mapFn = mapper ? bind$b(mapper, thisArg) : false;
    var element, elementLen;

    while (sourceIndex < sourceLen) {
      if (sourceIndex in source) {
        element = mapFn ? mapFn(source[sourceIndex], sourceIndex, original) : source[sourceIndex];

        if (depth > 0 && isArray$b(element)) {
          elementLen = lengthOfArrayLike$7(element);
          targetIndex = flattenIntoArray$1(target, original, element, elementLen, targetIndex, depth - 1) - 1;
        } else {
          doesNotExceedSafeInteger$1(targetIndex + 1);
          target[targetIndex] = element;
        }

        targetIndex++;
      }
      sourceIndex++;
    }
    return targetIndex;
  };

  var flattenIntoArray_1 = flattenIntoArray$1;

  var $$F = _export;
  var flattenIntoArray = flattenIntoArray_1;
  var aCallable$8 = aCallable$e;
  var toObject$7 = toObject$e;
  var lengthOfArrayLike$6 = lengthOfArrayLike$d;
  var arraySpeciesCreate$1 = arraySpeciesCreate$4;

  
  
  $$F({ target: 'Array', proto: true }, {
    flatMap: function flatMap(callbackfn ) {
      var O = toObject$7(this);
      var sourceLen = lengthOfArrayLike$6(O);
      var A;
      aCallable$8(callbackfn);
      A = arraySpeciesCreate$1(O, 0);
      A.length = flattenIntoArray(A, O, O, sourceLen, 0, 1, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
      return A;
    }
  });

  var entryVirtual$f = entryVirtual$k;

  var flatMap$2 = entryVirtual$f('Array').flatMap;

  var isPrototypeOf$h = objectIsPrototypeOf;
  var method$d = flatMap$2;

  var ArrayPrototype$e = Array.prototype;

  var flatMap$1 = function (it) {
    var own = it.flatMap;
    return it === ArrayPrototype$e || (isPrototypeOf$h(ArrayPrototype$e, it) && own === ArrayPrototype$e.flatMap) ? method$d : own;
  };

  var parent$Y = flatMap$1;

  var flatMap = parent$Y;

  (function (module) {
  	module.exports = flatMap;
  } (flatMap$3));

  var _flatMapInstanceProperty = getDefaultExportFromCjs(flatMapExports);

  
  function createNewDataPipeFrom(from) {
    return new DataPipeUnderConstruction(from);
  }
  
  var SimpleDataPipe = function () {
    

    
    function SimpleDataPipe(_source, _transformers, _target) {
      var _context, _context2, _context3;
      _classCallCheck(this, SimpleDataPipe);
      _defineProperty(this, "_source", void 0);
      _defineProperty(this, "_transformers", void 0);
      _defineProperty(this, "_target", void 0);
      _defineProperty(this, "_listeners", {
        add: _bindInstanceProperty$1(_context = this._add).call(_context, this),
        remove: _bindInstanceProperty$1(_context2 = this._remove).call(_context2, this),
        update: _bindInstanceProperty$1(_context3 = this._update).call(_context3, this)
      });
      this._source = _source;
      this._transformers = _transformers;
      this._target = _target;
    }
    
    _createClass(SimpleDataPipe, [{
      key: "all",
      value: function all() {
        this._target.update(this._transformItems(this._source.get()));
        return this;
      }
      
    }, {
      key: "start",
      value: function start() {
        this._source.on("add", this._listeners.add);
        this._source.on("remove", this._listeners.remove);
        this._source.on("update", this._listeners.update);
        return this;
      }
      
    }, {
      key: "stop",
      value: function stop() {
        this._source.off("add", this._listeners.add);
        this._source.off("remove", this._listeners.remove);
        this._source.off("update", this._listeners.update);
        return this;
      }
      
    }, {
      key: "_transformItems",
      value: function _transformItems(items) {
        var _context4;
        return _reduceInstanceProperty(_context4 = this._transformers).call(_context4, function (items, transform) {
          return transform(items);
        }, items);
      }
      
    }, {
      key: "_add",
      value: function _add(_name, payload) {
        if (payload == null) {
          return;
        }
        this._target.add(this._transformItems(this._source.get(payload.items)));
      }
      
    }, {
      key: "_update",
      value: function _update(_name, payload) {
        if (payload == null) {
          return;
        }
        this._target.update(this._transformItems(this._source.get(payload.items)));
      }
      
    }, {
      key: "_remove",
      value: function _remove(_name, payload) {
        if (payload == null) {
          return;
        }
        this._target.remove(this._transformItems(payload.oldData));
      }
    }]);
    return SimpleDataPipe;
  }();
  
  var DataPipeUnderConstruction = function () {
    

    
    function DataPipeUnderConstruction(_source) {
      _classCallCheck(this, DataPipeUnderConstruction);
      _defineProperty(this, "_source", void 0);
      _defineProperty(this, "_transformers", []);
      this._source = _source;
    }
    
    _createClass(DataPipeUnderConstruction, [{
      key: "filter",
      value: function filter(callback) {
        this._transformers.push(function (input) {
          return _filterInstanceProperty(input).call(input, callback);
        });
        return this;
      }
      
    }, {
      key: "map",
      value: function map(callback) {
        this._transformers.push(function (input) {
          return _mapInstanceProperty(input).call(input, callback);
        });
        return this;
      }
      
    }, {
      key: "flatMap",
      value: function flatMap(callback) {
        this._transformers.push(function (input) {
          return _flatMapInstanceProperty(input).call(input, callback);
        });
        return this;
      }
      
    }, {
      key: "to",
      value: function to(target) {
        return new SimpleDataPipe(this._source, this._transformers, target);
      }
    }]);
    return DataPipeUnderConstruction;
  }();

  var fromExports$2 = {};
  var from$7 = {
    get exports(){ return fromExports$2; },
    set exports(v){ fromExports$2 = v; },
  };

  var call$a = functionCall;
  var anObject$7 = anObject$d;
  var getMethod$1 = getMethod$3;

  var iteratorClose$2 = function (iterator, kind, value) {
    var innerResult, innerError;
    anObject$7(iterator);
    try {
      innerResult = getMethod$1(iterator, 'return');
      if (!innerResult) {
        if (kind === 'throw') throw value;
        return value;
      }
      innerResult = call$a(innerResult, iterator);
    } catch (error) {
      innerError = true;
      innerResult = error;
    }
    if (kind === 'throw') throw value;
    if (innerError) throw innerResult;
    anObject$7(innerResult);
    return value;
  };

  var anObject$6 = anObject$d;
  var iteratorClose$1 = iteratorClose$2;

  
  var callWithSafeIterationClosing$1 = function (iterator, fn, value, ENTRIES) {
    try {
      return ENTRIES ? fn(anObject$6(value)[0], value[1]) : fn(value);
    } catch (error) {
      iteratorClose$1(iterator, 'throw', error);
    }
  };

  var wellKnownSymbol$9 = wellKnownSymbol$o;
  var Iterators$1 = iterators;

  var ITERATOR$3 = wellKnownSymbol$9('iterator');
  var ArrayPrototype$d = Array.prototype;

  
  var isArrayIteratorMethod$2 = function (it) {
    return it !== undefined && (Iterators$1.Array === it || ArrayPrototype$d[ITERATOR$3] === it);
  };

  var classof$6 = classof$e;
  var getMethod = getMethod$3;
  var isNullOrUndefined$2 = isNullOrUndefined$5;
  var Iterators = iterators;
  var wellKnownSymbol$8 = wellKnownSymbol$o;

  var ITERATOR$2 = wellKnownSymbol$8('iterator');

  var getIteratorMethod$9 = function (it) {
    if (!isNullOrUndefined$2(it)) return getMethod(it, ITERATOR$2)
      || getMethod(it, '@@iterator')
      || Iterators[classof$6(it)];
  };

  var call$9 = functionCall;
  var aCallable$7 = aCallable$e;
  var anObject$5 = anObject$d;
  var tryToString$3 = tryToString$6;
  var getIteratorMethod$8 = getIteratorMethod$9;

  var $TypeError$8 = TypeError;

  var getIterator$8 = function (argument, usingIterator) {
    var iteratorMethod = arguments.length < 2 ? getIteratorMethod$8(argument) : usingIterator;
    if (aCallable$7(iteratorMethod)) return anObject$5(call$9(iteratorMethod, argument));
    throw $TypeError$8(tryToString$3(argument) + ' is not iterable');
  };

  var bind$a = functionBindContext;
  var call$8 = functionCall;
  var toObject$6 = toObject$e;
  var callWithSafeIterationClosing = callWithSafeIterationClosing$1;
  var isArrayIteratorMethod$1 = isArrayIteratorMethod$2;
  var isConstructor$2 = isConstructor$4;
  var lengthOfArrayLike$5 = lengthOfArrayLike$d;
  var createProperty$3 = createProperty$6;
  var getIterator$7 = getIterator$8;
  var getIteratorMethod$7 = getIteratorMethod$9;

  var $Array$1 = Array;

  
  
  var arrayFrom = function from(arrayLike ) {
    var O = toObject$6(arrayLike);
    var IS_CONSTRUCTOR = isConstructor$2(this);
    var argumentsLength = arguments.length;
    var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
    var mapping = mapfn !== undefined;
    if (mapping) mapfn = bind$a(mapfn, argumentsLength > 2 ? arguments[2] : undefined);
    var iteratorMethod = getIteratorMethod$7(O);
    var index = 0;
    var length, result, step, iterator, next, value;
    
    if (iteratorMethod && !(this === $Array$1 && isArrayIteratorMethod$1(iteratorMethod))) {
      iterator = getIterator$7(O, iteratorMethod);
      next = iterator.next;
      result = IS_CONSTRUCTOR ? new this() : [];
      for (;!(step = call$8(next, iterator)).done; index++) {
        value = mapping ? callWithSafeIterationClosing(iterator, mapfn, [step.value, index], true) : step.value;
        createProperty$3(result, index, value);
      }
    } else {
      length = lengthOfArrayLike$5(O);
      result = IS_CONSTRUCTOR ? new this(length) : $Array$1(length);
      for (;length > index; index++) {
        value = mapping ? mapfn(O[index], index) : O[index];
        createProperty$3(result, index, value);
      }
    }
    result.length = index;
    return result;
  };

  var wellKnownSymbol$7 = wellKnownSymbol$o;

  var ITERATOR$1 = wellKnownSymbol$7('iterator');
  var SAFE_CLOSING = false;

  try {
    var called = 0;
    var iteratorWithReturn = {
      next: function () {
        return { done: !!called++ };
      },
      'return': function () {
        SAFE_CLOSING = true;
      }
    };
    iteratorWithReturn[ITERATOR$1] = function () {
      return this;
    };
    
    Array.from(iteratorWithReturn, function () { throw 2; });
  } catch (error) {  }

  var checkCorrectnessOfIteration$2 = function (exec, SKIP_CLOSING) {
    if (!SKIP_CLOSING && !SAFE_CLOSING) return false;
    var ITERATION_SUPPORT = false;
    try {
      var object = {};
      object[ITERATOR$1] = function () {
        return {
          next: function () {
            return { done: ITERATION_SUPPORT = true };
          }
        };
      };
      exec(object);
    } catch (error) {  }
    return ITERATION_SUPPORT;
  };

  var $$E = _export;
  var from$6 = arrayFrom;
  var checkCorrectnessOfIteration$1 = checkCorrectnessOfIteration$2;

  var INCORRECT_ITERATION = !checkCorrectnessOfIteration$1(function (iterable) {
    
    Array.from(iterable);
  });

  
  
  $$E({ target: 'Array', stat: true, forced: INCORRECT_ITERATION }, {
    from: from$6
  });

  var path$k = path$r;

  var from$5 = path$k.Array.from;

  var parent$X = from$5;

  var from$4 = parent$X;

  (function (module) {
  	module.exports = from$4;
  } (from$7));

  var _Array$from$1 = getDefaultExportFromCjs(fromExports$2);

  var getIteratorMethodExports$1 = {};
  var getIteratorMethod$6 = {
    get exports(){ return getIteratorMethodExports$1; },
    set exports(v){ getIteratorMethodExports$1 = v; },
  };

  var getIteratorMethodExports = {};
  var getIteratorMethod$5 = {
    get exports(){ return getIteratorMethodExports; },
    set exports(v){ getIteratorMethodExports = v; },
  };

  var getIteratorMethod$4 = getIteratorMethod$9;

  var getIteratorMethod_1 = getIteratorMethod$4;

  var parent$W = getIteratorMethod_1;


  var getIteratorMethod$3 = parent$W;

  var parent$V = getIteratorMethod$3;

  var getIteratorMethod$2 = parent$V;

  var parent$U = getIteratorMethod$2;

  var getIteratorMethod$1 = parent$U;

  (function (module) {
  	module.exports = getIteratorMethod$1;
  } (getIteratorMethod$5));

  (function (module) {
  	module.exports = getIteratorMethodExports;
  } (getIteratorMethod$6));

  var _getIteratorMethod = getDefaultExportFromCjs(getIteratorMethodExports$1);

  var getOwnPropertySymbolsExports = {};
  var getOwnPropertySymbols$2 = {
    get exports(){ return getOwnPropertySymbolsExports; },
    set exports(v){ getOwnPropertySymbolsExports = v; },
  };

  var path$j = path$r;

  var getOwnPropertySymbols$1 = path$j.Object.getOwnPropertySymbols;

  var parent$T = getOwnPropertySymbols$1;

  var getOwnPropertySymbols = parent$T;

  (function (module) {
  	module.exports = getOwnPropertySymbols;
  } (getOwnPropertySymbols$2));

  var _Object$getOwnPropertySymbols = getDefaultExportFromCjs(getOwnPropertySymbolsExports);

  var getOwnPropertyDescriptorExports$1 = {};
  var getOwnPropertyDescriptor$5 = {
    get exports(){ return getOwnPropertyDescriptorExports$1; },
    set exports(v){ getOwnPropertyDescriptorExports$1 = v; },
  };

  var getOwnPropertyDescriptorExports = {};
  var getOwnPropertyDescriptor$4 = {
    get exports(){ return getOwnPropertyDescriptorExports; },
    set exports(v){ getOwnPropertyDescriptorExports = v; },
  };

  var $$D = _export;
  var fails$f = fails$w;
  var toIndexedObject$3 = toIndexedObject$b;
  var nativeGetOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
  var DESCRIPTORS$9 = descriptors;

  var FORCED$4 = !DESCRIPTORS$9 || fails$f(function () { nativeGetOwnPropertyDescriptor(1); });

  
  
  $$D({ target: 'Object', stat: true, forced: FORCED$4, sham: !DESCRIPTORS$9 }, {
    getOwnPropertyDescriptor: function getOwnPropertyDescriptor(it, key) {
      return nativeGetOwnPropertyDescriptor(toIndexedObject$3(it), key);
    }
  });

  var path$i = path$r;

  var Object$3 = path$i.Object;

  var getOwnPropertyDescriptor$3 = getOwnPropertyDescriptor$4.exports = function getOwnPropertyDescriptor(it, key) {
    return Object$3.getOwnPropertyDescriptor(it, key);
  };

  if (Object$3.getOwnPropertyDescriptor.sham) getOwnPropertyDescriptor$3.sham = true;

  var parent$S = getOwnPropertyDescriptorExports;

  var getOwnPropertyDescriptor$2 = parent$S;

  (function (module) {
  	module.exports = getOwnPropertyDescriptor$2;
  } (getOwnPropertyDescriptor$5));

  var _Object$getOwnPropertyDescriptor = getDefaultExportFromCjs(getOwnPropertyDescriptorExports$1);

  var getOwnPropertyDescriptorsExports = {};
  var getOwnPropertyDescriptors$2 = {
    get exports(){ return getOwnPropertyDescriptorsExports; },
    set exports(v){ getOwnPropertyDescriptorsExports = v; },
  };

  var getBuiltIn$5 = getBuiltIn$f;
  var uncurryThis$b = functionUncurryThis;
  var getOwnPropertyNamesModule$1 = objectGetOwnPropertyNames;
  var getOwnPropertySymbolsModule$1 = objectGetOwnPropertySymbols;
  var anObject$4 = anObject$d;

  var concat$5 = uncurryThis$b([].concat);

  
  var ownKeys$7 = getBuiltIn$5('Reflect', 'ownKeys') || function ownKeys(it) {
    var keys = getOwnPropertyNamesModule$1.f(anObject$4(it));
    var getOwnPropertySymbols = getOwnPropertySymbolsModule$1.f;
    return getOwnPropertySymbols ? concat$5(keys, getOwnPropertySymbols(it)) : keys;
  };

  var $$C = _export;
  var DESCRIPTORS$8 = descriptors;
  var ownKeys$6 = ownKeys$7;
  var toIndexedObject$2 = toIndexedObject$b;
  var getOwnPropertyDescriptorModule$1 = objectGetOwnPropertyDescriptor;
  var createProperty$2 = createProperty$6;

  
  
  $$C({ target: 'Object', stat: true, sham: !DESCRIPTORS$8 }, {
    getOwnPropertyDescriptors: function getOwnPropertyDescriptors(object) {
      var O = toIndexedObject$2(object);
      var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule$1.f;
      var keys = ownKeys$6(O);
      var result = {};
      var index = 0;
      var key, descriptor;
      while (keys.length > index) {
        descriptor = getOwnPropertyDescriptor(O, key = keys[index++]);
        if (descriptor !== undefined) createProperty$2(result, key, descriptor);
      }
      return result;
    }
  });

  var path$h = path$r;

  var getOwnPropertyDescriptors$1 = path$h.Object.getOwnPropertyDescriptors;

  var parent$R = getOwnPropertyDescriptors$1;

  var getOwnPropertyDescriptors = parent$R;

  (function (module) {
  	module.exports = getOwnPropertyDescriptors;
  } (getOwnPropertyDescriptors$2));

  var _Object$getOwnPropertyDescriptors = getDefaultExportFromCjs(getOwnPropertyDescriptorsExports);

  var definePropertiesExports$1 = {};
  var defineProperties$4 = {
    get exports(){ return definePropertiesExports$1; },
    set exports(v){ definePropertiesExports$1 = v; },
  };

  var definePropertiesExports = {};
  var defineProperties$3 = {
    get exports(){ return definePropertiesExports; },
    set exports(v){ definePropertiesExports = v; },
  };

  var $$B = _export;
  var DESCRIPTORS$7 = descriptors;
  var defineProperties$2 = objectDefineProperties.f;

  
  
  
  $$B({ target: 'Object', stat: true, forced: Object.defineProperties !== defineProperties$2, sham: !DESCRIPTORS$7 }, {
    defineProperties: defineProperties$2
  });

  var path$g = path$r;

  var Object$2 = path$g.Object;

  var defineProperties$1 = defineProperties$3.exports = function defineProperties(T, D) {
    return Object$2.defineProperties(T, D);
  };

  if (Object$2.defineProperties.sham) defineProperties$1.sham = true;

  var parent$Q = definePropertiesExports;

  var defineProperties = parent$Q;

  (function (module) {
  	module.exports = defineProperties;
  } (defineProperties$4));

  var _Object$defineProperties = getDefaultExportFromCjs(definePropertiesExports$1);

  var definePropertyExports = {};
  var defineProperty$3 = {
    get exports(){ return definePropertyExports; },
    set exports(v){ definePropertyExports = v; },
  };

  (function (module) {
  	module.exports = defineProperty$9;
  } (defineProperty$3));

  var _Object$defineProperty = getDefaultExportFromCjs(definePropertyExports);

  var isArrayExports$2 = {};
  var isArray$a = {
    get exports(){ return isArrayExports$2; },
    set exports(v){ isArrayExports$2 = v; },
  };

  var isArrayExports$1 = {};
  var isArray$9 = {
    get exports(){ return isArrayExports$1; },
    set exports(v){ isArrayExports$1 = v; },
  };

  var $$A = _export;
  var isArray$8 = isArray$f;

  
  
  $$A({ target: 'Array', stat: true }, {
    isArray: isArray$8
  });

  var path$f = path$r;

  var isArray$7 = path$f.Array.isArray;

  var parent$P = isArray$7;

  var isArray$6 = parent$P;

  var parent$O = isArray$6;

  var isArray$5 = parent$O;

  var parent$N = isArray$5;

  var isArray$4 = parent$N;

  (function (module) {
  	module.exports = isArray$4;
  } (isArray$9));

  (function (module) {
  	module.exports = isArrayExports$1;
  } (isArray$a));

  var _Array$isArray$1 = getDefaultExportFromCjs(isArrayExports$2);

  function _arrayWithHoles(arr) {
    if (_Array$isArray$1(arr)) return arr;
  }

  function _iterableToArrayLimit(arr, i) {
    var _i = null == arr ? null : "undefined" != typeof _Symbol$1 && _getIteratorMethod(arr) || arr["@@iterator"];
    if (null != _i) {
      var _s,
        _e,
        _x,
        _r,
        _arr = [],
        _n = !0,
        _d = !1;
      try {
        if (_x = (_i = _i.call(arr)).next, 0 === i) {
          if (Object(_i) !== _i) return;
          _n = !1;
        } else for (; !(_n = (_s = _x.call(_i)).done) && (_arr.push(_s.value), _arr.length !== i); _n = !0);
      } catch (err) {
        _d = !0, _e = err;
      } finally {
        try {
          if (!_n && null != _i["return"] && (_r = _i["return"](), Object(_r) !== _r)) return;
        } finally {
          if (_d) throw _e;
        }
      }
      return _arr;
    }
  }

  var sliceExports$2 = {};
  var slice$7 = {
    get exports(){ return sliceExports$2; },
    set exports(v){ sliceExports$2 = v; },
  };

  var sliceExports$1 = {};
  var slice$6 = {
    get exports(){ return sliceExports$1; },
    set exports(v){ sliceExports$1 = v; },
  };

  var $$z = _export;
  var isArray$3 = isArray$f;
  var isConstructor$1 = isConstructor$4;
  var isObject$8 = isObject$i;
  var toAbsoluteIndex$2 = toAbsoluteIndex$5;
  var lengthOfArrayLike$4 = lengthOfArrayLike$d;
  var toIndexedObject$1 = toIndexedObject$b;
  var createProperty$1 = createProperty$6;
  var wellKnownSymbol$6 = wellKnownSymbol$o;
  var arrayMethodHasSpeciesSupport$1 = arrayMethodHasSpeciesSupport$5;
  var nativeSlice = arraySlice$5;

  var HAS_SPECIES_SUPPORT$1 = arrayMethodHasSpeciesSupport$1('slice');

  var SPECIES$3 = wellKnownSymbol$6('species');
  var $Array = Array;
  var max$1 = Math.max;

  
  
  
  $$z({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$1 }, {
    slice: function slice(start, end) {
      var O = toIndexedObject$1(this);
      var length = lengthOfArrayLike$4(O);
      var k = toAbsoluteIndex$2(start, length);
      var fin = toAbsoluteIndex$2(end === undefined ? length : end, length);
      
      var Constructor, result, n;
      if (isArray$3(O)) {
        Constructor = O.constructor;
        
        if (isConstructor$1(Constructor) && (Constructor === $Array || isArray$3(Constructor.prototype))) {
          Constructor = undefined;
        } else if (isObject$8(Constructor)) {
          Constructor = Constructor[SPECIES$3];
          if (Constructor === null) Constructor = undefined;
        }
        if (Constructor === $Array || Constructor === undefined) {
          return nativeSlice(O, k, fin);
        }
      }
      result = new (Constructor === undefined ? $Array : Constructor)(max$1(fin - k, 0));
      for (n = 0; k < fin; k++, n++) if (k in O) createProperty$1(result, n, O[k]);
      result.length = n;
      return result;
    }
  });

  var entryVirtual$e = entryVirtual$k;

  var slice$5 = entryVirtual$e('Array').slice;

  var isPrototypeOf$g = objectIsPrototypeOf;
  var method$c = slice$5;

  var ArrayPrototype$c = Array.prototype;

  var slice$4 = function (it) {
    var own = it.slice;
    return it === ArrayPrototype$c || (isPrototypeOf$g(ArrayPrototype$c, it) && own === ArrayPrototype$c.slice) ? method$c : own;
  };

  var parent$M = slice$4;

  var slice$3 = parent$M;

  var parent$L = slice$3;

  var slice$2 = parent$L;

  var parent$K = slice$2;

  var slice$1 = parent$K;

  (function (module) {
  	module.exports = slice$1;
  } (slice$6));

  (function (module) {
  	module.exports = sliceExports$1;
  } (slice$7));

  var _sliceInstanceProperty$1 = getDefaultExportFromCjs(sliceExports$2);

  var fromExports$1 = {};
  var from$3 = {
    get exports(){ return fromExports$1; },
    set exports(v){ fromExports$1 = v; },
  };

  var fromExports = {};
  var from$2 = {
    get exports(){ return fromExports; },
    set exports(v){ fromExports = v; },
  };

  var parent$J = from$4;

  var from$1 = parent$J;

  var parent$I = from$1;

  var from = parent$I;

  (function (module) {
  	module.exports = from;
  } (from$2));

  (function (module) {
  	module.exports = fromExports;
  } (from$3));

  var _Array$from = getDefaultExportFromCjs(fromExports$1);

  function _arrayLikeToArray$4(arr, len) {
    if (len == null || len > arr.length) len = arr.length;
    for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];
    return arr2;
  }

  function _unsupportedIterableToArray$4(o, minLen) {
    var _context;
    if (!o) return;
    if (typeof o === "string") return _arrayLikeToArray$4(o, minLen);
    var n = _sliceInstanceProperty$1(_context = Object.prototype.toString.call(o)).call(_context, 8, -1);
    if (n === "Object" && o.constructor) n = o.constructor.name;
    if (n === "Map" || n === "Set") return _Array$from(o);
    if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray$4(o, minLen);
  }

  function _nonIterableRest() {
    throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
  }

  function _slicedToArray(arr, i) {
    return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray$4(arr, i) || _nonIterableRest();
  }

  function _arrayWithoutHoles(arr) {
    if (_Array$isArray$1(arr)) return _arrayLikeToArray$4(arr);
  }

  function _iterableToArray(iter) {
    if (typeof _Symbol$1 !== "undefined" && _getIteratorMethod(iter) != null || iter["@@iterator"] != null) return _Array$from(iter);
  }

  function _nonIterableSpread() {
    throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
  }

  function _toConsumableArray(arr) {
    return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray$4(arr) || _nonIterableSpread();
  }

  var symbolExports = {};
  var symbol = {
    get exports(){ return symbolExports; },
    set exports(v){ symbolExports = v; },
  };

  (function (module) {
  	module.exports = symbol$3;
  } (symbol));

  var _Symbol = getDefaultExportFromCjs(symbolExports);

  var concatExports = {};
  var concat$4 = {
    get exports(){ return concatExports; },
    set exports(v){ concatExports = v; },
  };

  var entryVirtual$d = entryVirtual$k;

  var concat$3 = entryVirtual$d('Array').concat;

  var isPrototypeOf$f = objectIsPrototypeOf;
  var method$b = concat$3;

  var ArrayPrototype$b = Array.prototype;

  var concat$2 = function (it) {
    var own = it.concat;
    return it === ArrayPrototype$b || (isPrototypeOf$f(ArrayPrototype$b, it) && own === ArrayPrototype$b.concat) ? method$b : own;
  };

  var parent$H = concat$2;

  var concat$1 = parent$H;

  (function (module) {
  	module.exports = concat$1;
  } (concat$4));

  var _concatInstanceProperty = getDefaultExportFromCjs(concatExports);

  var sliceExports = {};
  var slice = {
    get exports(){ return sliceExports; },
    set exports(v){ sliceExports = v; },
  };

  (function (module) {
  	module.exports = slice$3;
  } (slice));

  var _sliceInstanceProperty = getDefaultExportFromCjs(sliceExports);

  var ownKeysExports = {};
  var ownKeys$5 = {
    get exports(){ return ownKeysExports; },
    set exports(v){ ownKeysExports = v; },
  };

  var $$y = _export;
  var ownKeys$4 = ownKeys$7;

  
  
  $$y({ target: 'Reflect', stat: true }, {
    ownKeys: ownKeys$4
  });

  var path$e = path$r;

  var ownKeys$3 = path$e.Reflect.ownKeys;

  var parent$G = ownKeys$3;

  var ownKeys$2 = parent$G;

  (function (module) {
  	module.exports = ownKeys$2;
  } (ownKeys$5));

  var _Reflect$ownKeys = getDefaultExportFromCjs(ownKeysExports);

  var isArrayExports = {};
  var isArray$2 = {
    get exports(){ return isArrayExports; },
    set exports(v){ isArrayExports = v; },
  };

  (function (module) {
  	module.exports = isArray$6;
  } (isArray$2));

  var _Array$isArray = getDefaultExportFromCjs(isArrayExports);

  var keysExports$1 = {};
  var keys$6 = {
    get exports(){ return keysExports$1; },
    set exports(v){ keysExports$1 = v; },
  };

  var $$x = _export;
  var toObject$5 = toObject$e;
  var nativeKeys = objectKeys$4;
  var fails$e = fails$w;

  var FAILS_ON_PRIMITIVES$2 = fails$e(function () { nativeKeys(1); });

  
  
  $$x({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES$2 }, {
    keys: function keys(it) {
      return nativeKeys(toObject$5(it));
    }
  });

  var path$d = path$r;

  var keys$5 = path$d.Object.keys;

  var parent$F = keys$5;

  var keys$4 = parent$F;

  (function (module) {
  	module.exports = keys$4;
  } (keys$6));

  var _Object$keys = getDefaultExportFromCjs(keysExports$1);

  var nowExports = {};
  var now$3 = {
    get exports(){ return nowExports; },
    set exports(v){ nowExports = v; },
  };

  
  var $$w = _export;
  var uncurryThis$a = functionUncurryThis;

  var $Date = Date;
  var thisTimeValue = uncurryThis$a($Date.prototype.getTime);

  
  
  $$w({ target: 'Date', stat: true }, {
    now: function now() {
      return thisTimeValue(new $Date());
    }
  });

  var path$c = path$r;

  var now$2 = path$c.Date.now;

  var parent$E = now$2;

  var now$1 = parent$E;

  (function (module) {
  	module.exports = now$1;
  } (now$3));

  var forEachExports$2 = {};
  var forEach$9 = {
    get exports(){ return forEachExports$2; },
    set exports(v){ forEachExports$2 = v; },
  };

  var $forEach = arrayIteration.forEach;
  var arrayMethodIsStrict$3 = arrayMethodIsStrict$5;

  var STRICT_METHOD$2 = arrayMethodIsStrict$3('forEach');

  
  
  var arrayForEach = !STRICT_METHOD$2 ? function forEach(callbackfn ) {
    return $forEach(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
  
  } : [].forEach;

  var $$v = _export;
  var forEach$8 = arrayForEach;

  
  
  
  $$v({ target: 'Array', proto: true, forced: [].forEach != forEach$8 }, {
    forEach: forEach$8
  });

  var entryVirtual$c = entryVirtual$k;

  var forEach$7 = entryVirtual$c('Array').forEach;

  var parent$D = forEach$7;

  var forEach$6 = parent$D;

  var classof$5 = classof$e;
  var hasOwn$6 = hasOwnProperty_1;
  var isPrototypeOf$e = objectIsPrototypeOf;
  var method$a = forEach$6;

  var ArrayPrototype$a = Array.prototype;

  var DOMIterables$3 = {
    DOMTokenList: true,
    NodeList: true
  };

  var forEach$5 = function (it) {
    var own = it.forEach;
    return it === ArrayPrototype$a || (isPrototypeOf$e(ArrayPrototype$a, it) && own === ArrayPrototype$a.forEach)
      || hasOwn$6(DOMIterables$3, classof$5(it)) ? method$a : own;
  };

  (function (module) {
  	module.exports = forEach$5;
  } (forEach$9));

  var _forEachInstanceProperty = getDefaultExportFromCjs(forEachExports$2);

  var reverseExports$2 = {};
  var reverse$7 = {
    get exports(){ return reverseExports$2; },
    set exports(v){ reverseExports$2 = v; },
  };

  var $$u = _export;
  var uncurryThis$9 = functionUncurryThis;
  var isArray$1 = isArray$f;

  var nativeReverse = uncurryThis$9([].reverse);
  var test$1 = [1, 2];

  
  
  
  
  $$u({ target: 'Array', proto: true, forced: String(test$1) === String(test$1.reverse()) }, {
    reverse: function reverse() {
      
      if (isArray$1(this)) this.length = this.length;
      return nativeReverse(this);
    }
  });

  var entryVirtual$b = entryVirtual$k;

  var reverse$6 = entryVirtual$b('Array').reverse;

  var isPrototypeOf$d = objectIsPrototypeOf;
  var method$9 = reverse$6;

  var ArrayPrototype$9 = Array.prototype;

  var reverse$5 = function (it) {
    var own = it.reverse;
    return it === ArrayPrototype$9 || (isPrototypeOf$d(ArrayPrototype$9, it) && own === ArrayPrototype$9.reverse) ? method$9 : own;
  };

  var parent$C = reverse$5;

  var reverse$4 = parent$C;

  (function (module) {
  	module.exports = reverse$4;
  } (reverse$7));

  var _reverseInstanceProperty = getDefaultExportFromCjs(reverseExports$2);

  var spliceExports = {};
  var splice$3 = {
    get exports(){ return spliceExports; },
    set exports(v){ spliceExports = v; },
  };

  var DESCRIPTORS$6 = descriptors;
  var isArray = isArray$f;

  var $TypeError$7 = TypeError;
  
  var getOwnPropertyDescriptor$1 = Object.getOwnPropertyDescriptor;

  
  var SILENT_ON_NON_WRITABLE_LENGTH_SET = DESCRIPTORS$6 && !function () {
    
    if (this !== undefined) return true;
    try {
      
      Object.defineProperty([], 'length', { writable: false }).length = 1;
    } catch (error) {
      return error instanceof TypeError;
    }
  }();

  var arraySetLength = SILENT_ON_NON_WRITABLE_LENGTH_SET ? function (O, length) {
    if (isArray(O) && !getOwnPropertyDescriptor$1(O, 'length').writable) {
      throw $TypeError$7('Cannot set read only .length');
    } return O.length = length;
  } : function (O, length) {
    return O.length = length;
  };

  var tryToString$2 = tryToString$6;

  var $TypeError$6 = TypeError;

  var deletePropertyOrThrow$2 = function (O, P) {
    if (!delete O[P]) throw $TypeError$6('Cannot delete property ' + tryToString$2(P) + ' of ' + tryToString$2(O));
  };

  var $$t = _export;
  var toObject$4 = toObject$e;
  var toAbsoluteIndex$1 = toAbsoluteIndex$5;
  var toIntegerOrInfinity = toIntegerOrInfinity$4;
  var lengthOfArrayLike$3 = lengthOfArrayLike$d;
  var setArrayLength = arraySetLength;
  var doesNotExceedSafeInteger = doesNotExceedSafeInteger$3;
  var arraySpeciesCreate = arraySpeciesCreate$4;
  var createProperty = createProperty$6;
  var deletePropertyOrThrow$1 = deletePropertyOrThrow$2;
  var arrayMethodHasSpeciesSupport = arrayMethodHasSpeciesSupport$5;

  var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('splice');

  var max = Math.max;
  var min = Math.min;

  
  
  
  $$t({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
    splice: function splice(start, deleteCount ) {
      var O = toObject$4(this);
      var len = lengthOfArrayLike$3(O);
      var actualStart = toAbsoluteIndex$1(start, len);
      var argumentsLength = arguments.length;
      var insertCount, actualDeleteCount, A, k, from, to;
      if (argumentsLength === 0) {
        insertCount = actualDeleteCount = 0;
      } else if (argumentsLength === 1) {
        insertCount = 0;
        actualDeleteCount = len - actualStart;
      } else {
        insertCount = argumentsLength - 2;
        actualDeleteCount = min(max(toIntegerOrInfinity(deleteCount), 0), len - actualStart);
      }
      doesNotExceedSafeInteger(len + insertCount - actualDeleteCount);
      A = arraySpeciesCreate(O, actualDeleteCount);
      for (k = 0; k < actualDeleteCount; k++) {
        from = actualStart + k;
        if (from in O) createProperty(A, k, O[from]);
      }
      A.length = actualDeleteCount;
      if (insertCount < actualDeleteCount) {
        for (k = actualStart; k < len - actualDeleteCount; k++) {
          from = k + actualDeleteCount;
          to = k + insertCount;
          if (from in O) O[to] = O[from];
          else deletePropertyOrThrow$1(O, to);
        }
        for (k = len; k > len - actualDeleteCount + insertCount; k--) deletePropertyOrThrow$1(O, k - 1);
      } else if (insertCount > actualDeleteCount) {
        for (k = len - actualDeleteCount; k > actualStart; k--) {
          from = k + actualDeleteCount - 1;
          to = k + insertCount - 1;
          if (from in O) O[to] = O[from];
          else deletePropertyOrThrow$1(O, to);
        }
      }
      for (k = 0; k < insertCount; k++) {
        O[k + actualStart] = arguments[k + 2];
      }
      setArrayLength(O, len - actualDeleteCount + insertCount);
      return A;
    }
  });

  var entryVirtual$a = entryVirtual$k;

  var splice$2 = entryVirtual$a('Array').splice;

  var isPrototypeOf$c = objectIsPrototypeOf;
  var method$8 = splice$2;

  var ArrayPrototype$8 = Array.prototype;

  var splice$1 = function (it) {
    var own = it.splice;
    return it === ArrayPrototype$8 || (isPrototypeOf$c(ArrayPrototype$8, it) && own === ArrayPrototype$8.splice) ? method$8 : own;
  };

  var parent$B = splice$1;

  var splice = parent$B;

  (function (module) {
  	module.exports = splice;
  } (splice$3));

  var _spliceInstanceProperty = getDefaultExportFromCjs(spliceExports);

  var assignExports = {};
  var assign$5 = {
    get exports(){ return assignExports; },
    set exports(v){ assignExports = v; },
  };

  var DESCRIPTORS$5 = descriptors;
  var uncurryThis$8 = functionUncurryThis;
  var call$7 = functionCall;
  var fails$d = fails$w;
  var objectKeys$1 = objectKeys$4;
  var getOwnPropertySymbolsModule = objectGetOwnPropertySymbols;
  var propertyIsEnumerableModule = objectPropertyIsEnumerable;
  var toObject$3 = toObject$e;
  var IndexedObject = indexedObject;

  
  var $assign = Object.assign;
  
  var defineProperty$2 = Object.defineProperty;
  var concat = uncurryThis$8([].concat);

  
  
  var objectAssign = !$assign || fails$d(function () {
    
    if (DESCRIPTORS$5 && $assign({ b: 1 }, $assign(defineProperty$2({}, 'a', {
      enumerable: true,
      get: function () {
        defineProperty$2(this, 'b', {
          value: 3,
          enumerable: false
        });
      }
    }), { b: 2 })).b !== 1) return true;
    
    var A = {};
    var B = {};
    
    var symbol = Symbol();
    var alphabet = 'abcdefghijklmnopqrst';
    A[symbol] = 7;
    alphabet.split('').forEach(function (chr) { B[chr] = chr; });
    return $assign({}, A)[symbol] != 7 || objectKeys$1($assign({}, B)).join('') != alphabet;
  }) ? function assign(target, source) { 
    var T = toObject$3(target);
    var argumentsLength = arguments.length;
    var index = 1;
    var getOwnPropertySymbols = getOwnPropertySymbolsModule.f;
    var propertyIsEnumerable = propertyIsEnumerableModule.f;
    while (argumentsLength > index) {
      var S = IndexedObject(arguments[index++]);
      var keys = getOwnPropertySymbols ? concat(objectKeys$1(S), getOwnPropertySymbols(S)) : objectKeys$1(S);
      var length = keys.length;
      var j = 0;
      var key;
      while (length > j) {
        key = keys[j++];
        if (!DESCRIPTORS$5 || call$7(propertyIsEnumerable, S, key)) T[key] = S[key];
      }
    } return T;
  } : $assign;

  var $$s = _export;
  var assign$4 = objectAssign;

  
  
  
  $$s({ target: 'Object', stat: true, arity: 2, forced: Object.assign !== assign$4 }, {
    assign: assign$4
  });

  var path$b = path$r;

  var assign$3 = path$b.Object.assign;

  var parent$A = assign$3;

  var assign$2 = parent$A;

  (function (module) {
  	module.exports = assign$2;
  } (assign$5));

  var _Object$assign = getDefaultExportFromCjs(assignExports);

  var includesExports = {};
  var includes$4 = {
    get exports(){ return includesExports; },
    set exports(v){ includesExports = v; },
  };

  var $$r = _export;
  var $includes = arrayIncludes.includes;
  var fails$c = fails$w;

  
  var BROKEN_ON_SPARSE = fails$c(function () {
    
    return !Array(1).includes();
  });

  
  
  $$r({ target: 'Array', proto: true, forced: BROKEN_ON_SPARSE }, {
    includes: function includes(el ) {
      return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  var entryVirtual$9 = entryVirtual$k;

  var includes$3 = entryVirtual$9('Array').includes;

  var isObject$7 = isObject$i;
  var classof$4 = classofRaw$2;
  var wellKnownSymbol$5 = wellKnownSymbol$o;

  var MATCH$1 = wellKnownSymbol$5('match');

  
  
  var isRegexp = function (it) {
    var isRegExp;
    return isObject$7(it) && ((isRegExp = it[MATCH$1]) !== undefined ? !!isRegExp : classof$4(it) == 'RegExp');
  };

  var isRegExp = isRegexp;

  var $TypeError$5 = TypeError;

  var notARegexp = function (it) {
    if (isRegExp(it)) {
      throw $TypeError$5("The method doesn't accept regular expressions");
    } return it;
  };

  var wellKnownSymbol$4 = wellKnownSymbol$o;

  var MATCH = wellKnownSymbol$4('match');

  var correctIsRegexpLogic = function (METHOD_NAME) {
    var regexp = /./;
    try {
      '/./'[METHOD_NAME](regexp);
    } catch (error1) {
      try {
        regexp[MATCH] = false;
        return '/./'[METHOD_NAME](regexp);
      } catch (error2) {  }
    } return false;
  };

  var $$q = _export;
  var uncurryThis$7 = functionUncurryThis;
  var notARegExp = notARegexp;
  var requireObjectCoercible$1 = requireObjectCoercible$5;
  var toString$4 = toString$a;
  var correctIsRegExpLogic = correctIsRegexpLogic;

  var stringIndexOf = uncurryThis$7(''.indexOf);

  
  
  $$q({ target: 'String', proto: true, forced: !correctIsRegExpLogic('includes') }, {
    includes: function includes(searchString ) {
      return !!~stringIndexOf(
        toString$4(requireObjectCoercible$1(this)),
        toString$4(notARegExp(searchString)),
        arguments.length > 1 ? arguments[1] : undefined
      );
    }
  });

  var entryVirtual$8 = entryVirtual$k;

  var includes$2 = entryVirtual$8('String').includes;

  var isPrototypeOf$b = objectIsPrototypeOf;
  var arrayMethod = includes$3;
  var stringMethod = includes$2;

  var ArrayPrototype$7 = Array.prototype;
  var StringPrototype$1 = String.prototype;

  var includes$1 = function (it) {
    var own = it.includes;
    if (it === ArrayPrototype$7 || (isPrototypeOf$b(ArrayPrototype$7, it) && own === ArrayPrototype$7.includes)) return arrayMethod;
    if (typeof it == 'string' || it === StringPrototype$1 || (isPrototypeOf$b(StringPrototype$1, it) && own === StringPrototype$1.includes)) {
      return stringMethod;
    } return own;
  };

  var parent$z = includes$1;

  var includes = parent$z;

  (function (module) {
  	module.exports = includes;
  } (includes$4));

  var getPrototypeOfExports$2 = {};
  var getPrototypeOf$7 = {
    get exports(){ return getPrototypeOfExports$2; },
    set exports(v){ getPrototypeOfExports$2 = v; },
  };

  var $$p = _export;
  var fails$b = fails$w;
  var toObject$2 = toObject$e;
  var nativeGetPrototypeOf = objectGetPrototypeOf;
  var CORRECT_PROTOTYPE_GETTER = correctPrototypeGetter;

  var FAILS_ON_PRIMITIVES$1 = fails$b(function () { nativeGetPrototypeOf(1); });

  
  
  $$p({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES$1, sham: !CORRECT_PROTOTYPE_GETTER }, {
    getPrototypeOf: function getPrototypeOf(it) {
      return nativeGetPrototypeOf(toObject$2(it));
    }
  });

  var path$a = path$r;

  var getPrototypeOf$6 = path$a.Object.getPrototypeOf;

  var parent$y = getPrototypeOf$6;

  var getPrototypeOf$5 = parent$y;

  (function (module) {
  	module.exports = getPrototypeOf$5;
  } (getPrototypeOf$7));

  var valuesExports$1 = {};
  var values$6 = {
    get exports(){ return valuesExports$1; },
    set exports(v){ valuesExports$1 = v; },
  };

  var DESCRIPTORS$4 = descriptors;
  var uncurryThis$6 = functionUncurryThis;
  var objectKeys = objectKeys$4;
  var toIndexedObject = toIndexedObject$b;
  var $propertyIsEnumerable = objectPropertyIsEnumerable.f;

  var propertyIsEnumerable = uncurryThis$6($propertyIsEnumerable);
  var push$3 = uncurryThis$6([].push);

  
  var createMethod$1 = function (TO_ENTRIES) {
    return function (it) {
      var O = toIndexedObject(it);
      var keys = objectKeys(O);
      var length = keys.length;
      var i = 0;
      var result = [];
      var key;
      while (length > i) {
        key = keys[i++];
        if (!DESCRIPTORS$4 || propertyIsEnumerable(O, key)) {
          push$3(result, TO_ENTRIES ? [key, O[key]] : O[key]);
        }
      }
      return result;
    };
  };

  var objectToArray = {
    
    
    entries: createMethod$1(true),
    
    
    values: createMethod$1(false)
  };

  var $$o = _export;
  var $values = objectToArray.values;

  
  
  $$o({ target: 'Object', stat: true }, {
    values: function values(O) {
      return $values(O);
    }
  });

  var path$9 = path$r;

  var values$5 = path$9.Object.values;

  var parent$x = values$5;

  var values$4 = parent$x;

  (function (module) {
  	module.exports = values$4;
  } (values$6));

  var _parseIntExports = {};
  var _parseInt$2 = {
    get exports(){ return _parseIntExports; },
    set exports(v){ _parseIntExports = v; },
  };

  
  var whitespaces$3 = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002' +
    '\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';

  var uncurryThis$5 = functionUncurryThis;
  var requireObjectCoercible = requireObjectCoercible$5;
  var toString$3 = toString$a;
  var whitespaces$2 = whitespaces$3;

  var replace$1 = uncurryThis$5(''.replace);
  var ltrim = RegExp('^[' + whitespaces$2 + ']+');
  var rtrim = RegExp('(^|[^' + whitespaces$2 + '])[' + whitespaces$2 + ']+$');

  
  var createMethod = function (TYPE) {
    return function ($this) {
      var string = toString$3(requireObjectCoercible($this));
      if (TYPE & 1) string = replace$1(string, ltrim, '');
      if (TYPE & 2) string = replace$1(string, rtrim, '$1');
      return string;
    };
  };

  var stringTrim = {
    
    
    start: createMethod(1),
    
    
    end: createMethod(2),
    
    
    trim: createMethod(3)
  };

  var global$a = global$n;
  var fails$a = fails$w;
  var uncurryThis$4 = functionUncurryThis;
  var toString$2 = toString$a;
  var trim$4 = stringTrim.trim;
  var whitespaces$1 = whitespaces$3;

  var $parseInt$1 = global$a.parseInt;
  var Symbol$1 = global$a.Symbol;
  var ITERATOR = Symbol$1 && Symbol$1.iterator;
  var hex = /^[+-]?0x/i;
  var exec = uncurryThis$4(hex.exec);
  var FORCED$3 = $parseInt$1(whitespaces$1 + '08') !== 8 || $parseInt$1(whitespaces$1 + '0x16') !== 22
    
    || (ITERATOR && !fails$a(function () { $parseInt$1(Object(ITERATOR)); }));

  
  
  var numberParseInt = FORCED$3 ? function parseInt(string, radix) {
    var S = trim$4(toString$2(string));
    return $parseInt$1(S, (radix >>> 0) || (exec(hex, S) ? 16 : 10));
  } : $parseInt$1;

  var $$n = _export;
  var $parseInt = numberParseInt;

  
  
  $$n({ global: true, forced: parseInt != $parseInt }, {
    parseInt: $parseInt
  });

  var path$8 = path$r;

  var _parseInt$1 = path$8.parseInt;

  var parent$w = _parseInt$1;

  var _parseInt = parent$w;

  (function (module) {
  	module.exports = _parseInt;
  } (_parseInt$2));

  var indexOfExports = {};
  var indexOf$3 = {
    get exports(){ return indexOfExports; },
    set exports(v){ indexOfExports = v; },
  };

  
  var $$m = _export;
  var uncurryThis$3 = functionUncurryThisClause;
  var $indexOf = arrayIncludes.indexOf;
  var arrayMethodIsStrict$2 = arrayMethodIsStrict$5;

  var nativeIndexOf = uncurryThis$3([].indexOf);

  var NEGATIVE_ZERO = !!nativeIndexOf && 1 / nativeIndexOf([1], 1, -0) < 0;
  var FORCED$2 = NEGATIVE_ZERO || !arrayMethodIsStrict$2('indexOf');

  
  
  $$m({ target: 'Array', proto: true, forced: FORCED$2 }, {
    indexOf: function indexOf(searchElement ) {
      var fromIndex = arguments.length > 1 ? arguments[1] : undefined;
      return NEGATIVE_ZERO
        
        ? nativeIndexOf(this, searchElement, fromIndex) || 0
        : $indexOf(this, searchElement, fromIndex);
    }
  });

  var entryVirtual$7 = entryVirtual$k;

  var indexOf$2 = entryVirtual$7('Array').indexOf;

  var isPrototypeOf$a = objectIsPrototypeOf;
  var method$7 = indexOf$2;

  var ArrayPrototype$6 = Array.prototype;

  var indexOf$1 = function (it) {
    var own = it.indexOf;
    return it === ArrayPrototype$6 || (isPrototypeOf$a(ArrayPrototype$6, it) && own === ArrayPrototype$6.indexOf) ? method$7 : own;
  };

  var parent$v = indexOf$1;

  var indexOf = parent$v;

  (function (module) {
  	module.exports = indexOf;
  } (indexOf$3));

  var trimExports = {};
  var trim$3 = {
    get exports(){ return trimExports; },
    set exports(v){ trimExports = v; },
  };

  var PROPER_FUNCTION_NAME = functionName.PROPER;
  var fails$9 = fails$w;
  var whitespaces = whitespaces$3;

  var non = '\u200B\u0085\u180E';

  
  
  var stringTrimForced = function (METHOD_NAME) {
    return fails$9(function () {
      return !!whitespaces[METHOD_NAME]()
        || non[METHOD_NAME]() !== non
        || (PROPER_FUNCTION_NAME && whitespaces[METHOD_NAME].name !== METHOD_NAME);
    });
  };

  var $$l = _export;
  var $trim = stringTrim.trim;
  var forcedStringTrimMethod = stringTrimForced;

  
  
  $$l({ target: 'String', proto: true, forced: forcedStringTrimMethod('trim') }, {
    trim: function trim() {
      return $trim(this);
    }
  });

  var entryVirtual$6 = entryVirtual$k;

  var trim$2 = entryVirtual$6('String').trim;

  var isPrototypeOf$9 = objectIsPrototypeOf;
  var method$6 = trim$2;

  var StringPrototype = String.prototype;

  var trim$1 = function (it) {
    var own = it.trim;
    return typeof it == 'string' || it === StringPrototype
      || (isPrototypeOf$9(StringPrototype, it) && own === StringPrototype.trim) ? method$6 : own;
  };

  var parent$u = trim$1;

  var trim = parent$u;

  (function (module) {
  	module.exports = trim;
  } (trim$3));

  var createExports$2 = {};
  var create$a = {
    get exports(){ return createExports$2; },
    set exports(v){ createExports$2 = v; },
  };

  
  var $$k = _export;
  var DESCRIPTORS$3 = descriptors;
  var create$9 = objectCreate;

  
  
  $$k({ target: 'Object', stat: true, sham: !DESCRIPTORS$3 }, {
    create: create$9
  });

  var path$7 = path$r;

  var Object$1 = path$7.Object;

  var create$8 = function create(P, D) {
    return Object$1.create(P, D);
  };

  var parent$t = create$8;

  var create$7 = parent$t;

  (function (module) {
  	module.exports = create$7;
  } (create$a));

  var _Object$create$1 = getDefaultExportFromCjs(createExports$2);

  var stringifyExports = {};
  var stringify$2 = {
    get exports(){ return stringifyExports; },
    set exports(v){ stringifyExports = v; },
  };

  var path$6 = path$r;
  var apply$3 = functionApply;

  
  if (!path$6.JSON) path$6.JSON = { stringify: JSON.stringify };

  
  var stringify$1 = function stringify(it, replacer, space) {
    return apply$3(path$6.JSON.stringify, null, arguments);
  };

  var parent$s = stringify$1;

  var stringify = parent$s;

  (function (module) {
  	module.exports = stringify;
  } (stringify$2));

  var _JSON$stringify = getDefaultExportFromCjs(stringifyExports);

  var setTimeoutExports = {};
  var setTimeout$3 = {
    get exports(){ return setTimeoutExports; },
    set exports(v){ setTimeoutExports = v; },
  };

  

  var engineIsBun = typeof Bun == 'function' && Bun && typeof Bun.version == 'string';

  var $TypeError$4 = TypeError;

  var validateArgumentsLength$2 = function (passed, required) {
    if (passed < required) throw $TypeError$4('Not enough arguments');
    return passed;
  };

  var global$9 = global$n;
  var apply$2 = functionApply;
  var isCallable$5 = isCallable$m;
  var ENGINE_IS_BUN = engineIsBun;
  var USER_AGENT = engineUserAgent;
  var arraySlice$2 = arraySlice$5;
  var validateArgumentsLength$1 = validateArgumentsLength$2;

  var Function$2 = global$9.Function;
  
  var WRAP = /MSIE .\./.test(USER_AGENT) || ENGINE_IS_BUN && (function () {
    var version = global$9.Bun.version.split('.');
    return version.length < 3 || version[0] == 0 && (version[1] < 3 || version[1] == 3 && version[2] == 0);
  })();

  
  
  
  var schedulersFix$2 = function (scheduler, hasTimeArg) {
    var firstParamIndex = hasTimeArg ? 2 : 1;
    return WRAP ? function (handler, timeout ) {
      var boundArgs = validateArgumentsLength$1(arguments.length, 1) > firstParamIndex;
      var fn = isCallable$5(handler) ? handler : Function$2(handler);
      var params = boundArgs ? arraySlice$2(arguments, firstParamIndex) : [];
      var callback = boundArgs ? function () {
        apply$2(fn, this, params);
      } : fn;
      return hasTimeArg ? scheduler(callback, timeout) : scheduler(callback);
    } : scheduler;
  };

  var $$j = _export;
  var global$8 = global$n;
  var schedulersFix$1 = schedulersFix$2;

  var setInterval = schedulersFix$1(global$8.setInterval, true);

  
  
  $$j({ global: true, bind: true, forced: global$8.setInterval !== setInterval }, {
    setInterval: setInterval
  });

  var $$i = _export;
  var global$7 = global$n;
  var schedulersFix = schedulersFix$2;

  var setTimeout$2 = schedulersFix(global$7.setTimeout, true);

  
  
  $$i({ global: true, bind: true, forced: global$7.setTimeout !== setTimeout$2 }, {
    setTimeout: setTimeout$2
  });

  var path$5 = path$r;

  var setTimeout$1 = path$5.setTimeout;

  (function (module) {
  	module.exports = setTimeout$1;
  } (setTimeout$3));

  var _setTimeout = getDefaultExportFromCjs(setTimeoutExports);

  var fillExports = {};
  var fill$4 = {
    get exports(){ return fillExports; },
    set exports(v){ fillExports = v; },
  };

  var toObject$1 = toObject$e;
  var toAbsoluteIndex = toAbsoluteIndex$5;
  var lengthOfArrayLike$2 = lengthOfArrayLike$d;

  
  
  var arrayFill = function fill(value ) {
    var O = toObject$1(this);
    var length = lengthOfArrayLike$2(O);
    var argumentsLength = arguments.length;
    var index = toAbsoluteIndex(argumentsLength > 1 ? arguments[1] : undefined, length);
    var end = argumentsLength > 2 ? arguments[2] : undefined;
    var endPos = end === undefined ? length : toAbsoluteIndex(end, length);
    while (endPos > index) O[index++] = value;
    return O;
  };

  var $$h = _export;
  var fill$3 = arrayFill;

  
  
  $$h({ target: 'Array', proto: true }, {
    fill: fill$3
  });

  var entryVirtual$5 = entryVirtual$k;

  var fill$2 = entryVirtual$5('Array').fill;

  var isPrototypeOf$8 = objectIsPrototypeOf;
  var method$5 = fill$2;

  var ArrayPrototype$5 = Array.prototype;

  var fill$1 = function (it) {
    var own = it.fill;
    return it === ArrayPrototype$5 || (isPrototypeOf$8(ArrayPrototype$5, it) && own === ArrayPrototype$5.fill) ? method$5 : own;
  };

  var parent$r = fill$1;

  var fill = parent$r;

  (function (module) {
  	module.exports = fill;
  } (fill$4));

  var componentEmitterExports = {};
  var componentEmitter = {
    get exports(){ return componentEmitterExports; },
    set exports(v){ componentEmitterExports = v; },
  };

  (function (module) {
  	

  	{
  	  module.exports = Emitter;
  	}

  	

  	function Emitter(obj) {
  	  if (obj) return mixin(obj);
  	}
  	

  	function mixin(obj) {
  	  for (var key in Emitter.prototype) {
  	    obj[key] = Emitter.prototype[key];
  	  }
  	  return obj;
  	}

  	

  	Emitter.prototype.on =
  	Emitter.prototype.addEventListener = function(event, fn){
  	  this._callbacks = this._callbacks || {};
  	  (this._callbacks['$' + event] = this._callbacks['$' + event] || [])
  	    .push(fn);
  	  return this;
  	};

  	

  	Emitter.prototype.once = function(event, fn){
  	  function on() {
  	    this.off(event, on);
  	    fn.apply(this, arguments);
  	  }

  	  on.fn = fn;
  	  this.on(event, on);
  	  return this;
  	};

  	

  	Emitter.prototype.off =
  	Emitter.prototype.removeListener =
  	Emitter.prototype.removeAllListeners =
  	Emitter.prototype.removeEventListener = function(event, fn){
  	  this._callbacks = this._callbacks || {};

  	  
  	  if (0 == arguments.length) {
  	    this._callbacks = {};
  	    return this;
  	  }

  	  
  	  var callbacks = this._callbacks['$' + event];
  	  if (!callbacks) return this;

  	  
  	  if (1 == arguments.length) {
  	    delete this._callbacks['$' + event];
  	    return this;
  	  }

  	  
  	  var cb;
  	  for (var i = 0; i < callbacks.length; i++) {
  	    cb = callbacks[i];
  	    if (cb === fn || cb.fn === fn) {
  	      callbacks.splice(i, 1);
  	      break;
  	    }
  	  }

  	  
  	  
  	  if (callbacks.length === 0) {
  	    delete this._callbacks['$' + event];
  	  }

  	  return this;
  	};

  	

  	Emitter.prototype.emit = function(event){
  	  this._callbacks = this._callbacks || {};

  	  var args = new Array(arguments.length - 1)
  	    , callbacks = this._callbacks['$' + event];

  	  for (var i = 1; i < arguments.length; i++) {
  	    args[i - 1] = arguments[i];
  	  }

  	  if (callbacks) {
  	    callbacks = callbacks.slice(0);
  	    for (var i = 0, len = callbacks.length; i < len; ++i) {
  	      callbacks[i].apply(this, args);
  	    }
  	  }

  	  return this;
  	};

  	

  	Emitter.prototype.listeners = function(event){
  	  this._callbacks = this._callbacks || {};
  	  return this._callbacks['$' + event] || [];
  	};

  	

  	Emitter.prototype.hasListeners = function(event){
  	  return !! this.listeners(event).length;
  	};
  } (componentEmitter));

  var Emitter = componentEmitterExports;

  
  function _extends() {
    _extends = Object.assign || function (target) {
      for (var i = 1; i < arguments.length; i++) {
        var source = arguments[i];

        for (var key in source) {
          if (Object.prototype.hasOwnProperty.call(source, key)) {
            target[key] = source[key];
          }
        }
      }

      return target;
    };

    return _extends.apply(this, arguments);
  }

  function _inheritsLoose(subClass, superClass) {
    subClass.prototype = Object.create(superClass.prototype);
    subClass.prototype.constructor = subClass;
    subClass.__proto__ = superClass;
  }

  function _assertThisInitialized$1(self) {
    if (self === void 0) {
      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    }

    return self;
  }

  
  var assign;

  if (typeof Object.assign !== 'function') {
    assign = function assign(target) {
      if (target === undefined || target === null) {
        throw new TypeError('Cannot convert undefined or null to object');
      }

      var output = Object(target);

      for (var index = 1; index < arguments.length; index++) {
        var source = arguments[index];

        if (source !== undefined && source !== null) {
          for (var nextKey in source) {
            if (source.hasOwnProperty(nextKey)) {
              output[nextKey] = source[nextKey];
            }
          }
        }
      }

      return output;
    };
  } else {
    assign = Object.assign;
  }

  var assign$1 = assign;

  var VENDOR_PREFIXES = ['', 'webkit', 'Moz', 'MS', 'ms', 'o'];
  var TEST_ELEMENT = typeof document === "undefined" ? {
    style: {}
  } : document.createElement('div');
  var TYPE_FUNCTION = 'function';
  var round = Math.round,
      abs = Math.abs;
  var now = Date.now;

  

  function prefixed(obj, property) {
    var prefix;
    var prop;
    var camelProp = property[0].toUpperCase() + property.slice(1);
    var i = 0;

    while (i < VENDOR_PREFIXES.length) {
      prefix = VENDOR_PREFIXES[i];
      prop = prefix ? prefix + camelProp : property;

      if (prop in obj) {
        return prop;
      }

      i++;
    }

    return undefined;
  }

  
  var win;

  if (typeof window === "undefined") {
    
    win = {};
  } else {
    win = window;
  }

  var PREFIXED_TOUCH_ACTION = prefixed(TEST_ELEMENT.style, 'touchAction');
  var NATIVE_TOUCH_ACTION = PREFIXED_TOUCH_ACTION !== undefined;
  function getTouchActionProps() {
    if (!NATIVE_TOUCH_ACTION) {
      return false;
    }

    var touchMap = {};
    var cssSupports = win.CSS && win.CSS.supports;
    ['auto', 'manipulation', 'pan-y', 'pan-x', 'pan-x pan-y', 'none'].forEach(function (val) {
      
      
      return touchMap[val] = cssSupports ? win.CSS.supports('touch-action', val) : true;
    });
    return touchMap;
  }

  var TOUCH_ACTION_COMPUTE = 'compute';
  var TOUCH_ACTION_AUTO = 'auto';
  var TOUCH_ACTION_MANIPULATION = 'manipulation'; 

  var TOUCH_ACTION_NONE = 'none';
  var TOUCH_ACTION_PAN_X = 'pan-x';
  var TOUCH_ACTION_PAN_Y = 'pan-y';
  var TOUCH_ACTION_MAP = getTouchActionProps();

  var MOBILE_REGEX = /mobile|tablet|ip(ad|hone|od)|android/i;
  var SUPPORT_TOUCH = 'ontouchstart' in win;
  var SUPPORT_POINTER_EVENTS = prefixed(win, 'PointerEvent') !== undefined;
  var SUPPORT_ONLY_TOUCH = SUPPORT_TOUCH && MOBILE_REGEX.test(navigator.userAgent);
  var INPUT_TYPE_TOUCH = 'touch';
  var INPUT_TYPE_PEN = 'pen';
  var INPUT_TYPE_MOUSE = 'mouse';
  var INPUT_TYPE_KINECT = 'kinect';
  var COMPUTE_INTERVAL = 25;
  var INPUT_START = 1;
  var INPUT_MOVE = 2;
  var INPUT_END = 4;
  var INPUT_CANCEL = 8;
  var DIRECTION_NONE = 1;
  var DIRECTION_LEFT = 2;
  var DIRECTION_RIGHT = 4;
  var DIRECTION_UP = 8;
  var DIRECTION_DOWN = 16;
  var DIRECTION_HORIZONTAL = DIRECTION_LEFT | DIRECTION_RIGHT;
  var DIRECTION_VERTICAL = DIRECTION_UP | DIRECTION_DOWN;
  var DIRECTION_ALL = DIRECTION_HORIZONTAL | DIRECTION_VERTICAL;
  var PROPS_XY = ['x', 'y'];
  var PROPS_CLIENT_XY = ['clientX', 'clientY'];

  
  function each(obj, iterator, context) {
    var i;

    if (!obj) {
      return;
    }

    if (obj.forEach) {
      obj.forEach(iterator, context);
    } else if (obj.length !== undefined) {
      i = 0;

      while (i < obj.length) {
        iterator.call(context, obj[i], i, obj);
        i++;
      }
    } else {
      for (i in obj) {
        obj.hasOwnProperty(i) && iterator.call(context, obj[i], i, obj);
      }
    }
  }

  

  function boolOrFn(val, args) {
    if (typeof val === TYPE_FUNCTION) {
      return val.apply(args ? args[0] || undefined : undefined, args);
    }

    return val;
  }

  
  function inStr(str, find) {
    return str.indexOf(find) > -1;
  }

  

  function cleanTouchActions(actions) {
    
    if (inStr(actions, TOUCH_ACTION_NONE)) {
      return TOUCH_ACTION_NONE;
    }

    var hasPanX = inStr(actions, TOUCH_ACTION_PAN_X);
    var hasPanY = inStr(actions, TOUCH_ACTION_PAN_Y); 
    
    
    

    if (hasPanX && hasPanY) {
      return TOUCH_ACTION_NONE;
    } 


    if (hasPanX || hasPanY) {
      return hasPanX ? TOUCH_ACTION_PAN_X : TOUCH_ACTION_PAN_Y;
    } 


    if (inStr(actions, TOUCH_ACTION_MANIPULATION)) {
      return TOUCH_ACTION_MANIPULATION;
    }

    return TOUCH_ACTION_AUTO;
  }

  

  var TouchAction =
  
  function () {
    function TouchAction(manager, value) {
      this.manager = manager;
      this.set(value);
    }
    


    var _proto = TouchAction.prototype;

    _proto.set = function set(value) {
      
      if (value === TOUCH_ACTION_COMPUTE) {
        value = this.compute();
      }

      if (NATIVE_TOUCH_ACTION && this.manager.element.style && TOUCH_ACTION_MAP[value]) {
        this.manager.element.style[PREFIXED_TOUCH_ACTION] = value;
      }

      this.actions = value.toLowerCase().trim();
    };
    


    _proto.update = function update() {
      this.set(this.manager.options.touchAction);
    };
    


    _proto.compute = function compute() {
      var actions = [];
      each(this.manager.recognizers, function (recognizer) {
        if (boolOrFn(recognizer.options.enable, [recognizer])) {
          actions = actions.concat(recognizer.getTouchAction());
        }
      });
      return cleanTouchActions(actions.join(' '));
    };
    


    _proto.preventDefaults = function preventDefaults(input) {
      var srcEvent = input.srcEvent;
      var direction = input.offsetDirection; 

      if (this.manager.session.prevented) {
        srcEvent.preventDefault();
        return;
      }

      var actions = this.actions;
      var hasNone = inStr(actions, TOUCH_ACTION_NONE) && !TOUCH_ACTION_MAP[TOUCH_ACTION_NONE];
      var hasPanY = inStr(actions, TOUCH_ACTION_PAN_Y) && !TOUCH_ACTION_MAP[TOUCH_ACTION_PAN_Y];
      var hasPanX = inStr(actions, TOUCH_ACTION_PAN_X) && !TOUCH_ACTION_MAP[TOUCH_ACTION_PAN_X];

      if (hasNone) {
        
        var isTapPointer = input.pointers.length === 1;
        var isTapMovement = input.distance < 2;
        var isTapTouchTime = input.deltaTime < 250;

        if (isTapPointer && isTapMovement && isTapTouchTime) {
          return;
        }
      }

      if (hasPanX && hasPanY) {
        
        return;
      }

      if (hasNone || hasPanY && direction & DIRECTION_HORIZONTAL || hasPanX && direction & DIRECTION_VERTICAL) {
        return this.preventSrc(srcEvent);
      }
    };
    


    _proto.preventSrc = function preventSrc(srcEvent) {
      this.manager.session.prevented = true;
      srcEvent.preventDefault();
    };

    return TouchAction;
  }();

  
  function hasParent(node, parent) {
    while (node) {
      if (node === parent) {
        return true;
      }

      node = node.parentNode;
    }

    return false;
  }

  

  function getCenter(pointers) {
    var pointersLength = pointers.length; 

    if (pointersLength === 1) {
      return {
        x: round(pointers[0].clientX),
        y: round(pointers[0].clientY)
      };
    }

    var x = 0;
    var y = 0;
    var i = 0;

    while (i < pointersLength) {
      x += pointers[i].clientX;
      y += pointers[i].clientY;
      i++;
    }

    return {
      x: round(x / pointersLength),
      y: round(y / pointersLength)
    };
  }

  

  function simpleCloneInputData(input) {
    
    
    var pointers = [];
    var i = 0;

    while (i < input.pointers.length) {
      pointers[i] = {
        clientX: round(input.pointers[i].clientX),
        clientY: round(input.pointers[i].clientY)
      };
      i++;
    }

    return {
      timeStamp: now(),
      pointers: pointers,
      center: getCenter(pointers),
      deltaX: input.deltaX,
      deltaY: input.deltaY
    };
  }

  

  function getDistance(p1, p2, props) {
    if (!props) {
      props = PROPS_XY;
    }

    var x = p2[props[0]] - p1[props[0]];
    var y = p2[props[1]] - p1[props[1]];
    return Math.sqrt(x * x + y * y);
  }

  

  function getAngle(p1, p2, props) {
    if (!props) {
      props = PROPS_XY;
    }

    var x = p2[props[0]] - p1[props[0]];
    var y = p2[props[1]] - p1[props[1]];
    return Math.atan2(y, x) * 180 / Math.PI;
  }

  

  function getDirection(x, y) {
    if (x === y) {
      return DIRECTION_NONE;
    }

    if (abs(x) >= abs(y)) {
      return x < 0 ? DIRECTION_LEFT : DIRECTION_RIGHT;
    }

    return y < 0 ? DIRECTION_UP : DIRECTION_DOWN;
  }

  function computeDeltaXY(session, input) {
    var center = input.center; 
    

    var offset = session.offsetDelta || {};
    var prevDelta = session.prevDelta || {};
    var prevInput = session.prevInput || {};

    if (input.eventType === INPUT_START || prevInput.eventType === INPUT_END) {
      prevDelta = session.prevDelta = {
        x: prevInput.deltaX || 0,
        y: prevInput.deltaY || 0
      };
      offset = session.offsetDelta = {
        x: center.x,
        y: center.y
      };
    }

    input.deltaX = prevDelta.x + (center.x - offset.x);
    input.deltaY = prevDelta.y + (center.y - offset.y);
  }

  
  function getVelocity(deltaTime, x, y) {
    return {
      x: x / deltaTime || 0,
      y: y / deltaTime || 0
    };
  }

  

  function getScale(start, end) {
    return getDistance(end[0], end[1], PROPS_CLIENT_XY) / getDistance(start[0], start[1], PROPS_CLIENT_XY);
  }

  

  function getRotation(start, end) {
    return getAngle(end[1], end[0], PROPS_CLIENT_XY) + getAngle(start[1], start[0], PROPS_CLIENT_XY);
  }

  

  function computeIntervalInputData(session, input) {
    var last = session.lastInterval || input;
    var deltaTime = input.timeStamp - last.timeStamp;
    var velocity;
    var velocityX;
    var velocityY;
    var direction;

    if (input.eventType !== INPUT_CANCEL && (deltaTime > COMPUTE_INTERVAL || last.velocity === undefined)) {
      var deltaX = input.deltaX - last.deltaX;
      var deltaY = input.deltaY - last.deltaY;
      var v = getVelocity(deltaTime, deltaX, deltaY);
      velocityX = v.x;
      velocityY = v.y;
      velocity = abs(v.x) > abs(v.y) ? v.x : v.y;
      direction = getDirection(deltaX, deltaY);
      session.lastInterval = input;
    } else {
      
      velocity = last.velocity;
      velocityX = last.velocityX;
      velocityY = last.velocityY;
      direction = last.direction;
    }

    input.velocity = velocity;
    input.velocityX = velocityX;
    input.velocityY = velocityY;
    input.direction = direction;
  }

  

  function computeInputData(manager, input) {
    var session = manager.session;
    var pointers = input.pointers;
    var pointersLength = pointers.length; 

    if (!session.firstInput) {
      session.firstInput = simpleCloneInputData(input);
    } 


    if (pointersLength > 1 && !session.firstMultiple) {
      session.firstMultiple = simpleCloneInputData(input);
    } else if (pointersLength === 1) {
      session.firstMultiple = false;
    }

    var firstInput = session.firstInput,
        firstMultiple = session.firstMultiple;
    var offsetCenter = firstMultiple ? firstMultiple.center : firstInput.center;
    var center = input.center = getCenter(pointers);
    input.timeStamp = now();
    input.deltaTime = input.timeStamp - firstInput.timeStamp;
    input.angle = getAngle(offsetCenter, center);
    input.distance = getDistance(offsetCenter, center);
    computeDeltaXY(session, input);
    input.offsetDirection = getDirection(input.deltaX, input.deltaY);
    var overallVelocity = getVelocity(input.deltaTime, input.deltaX, input.deltaY);
    input.overallVelocityX = overallVelocity.x;
    input.overallVelocityY = overallVelocity.y;
    input.overallVelocity = abs(overallVelocity.x) > abs(overallVelocity.y) ? overallVelocity.x : overallVelocity.y;
    input.scale = firstMultiple ? getScale(firstMultiple.pointers, pointers) : 1;
    input.rotation = firstMultiple ? getRotation(firstMultiple.pointers, pointers) : 0;
    input.maxPointers = !session.prevInput ? input.pointers.length : input.pointers.length > session.prevInput.maxPointers ? input.pointers.length : session.prevInput.maxPointers;
    computeIntervalInputData(session, input); 

    var target = manager.element;
    var srcEvent = input.srcEvent;
    var srcEventTarget;

    if (srcEvent.composedPath) {
      srcEventTarget = srcEvent.composedPath()[0];
    } else if (srcEvent.path) {
      srcEventTarget = srcEvent.path[0];
    } else {
      srcEventTarget = srcEvent.target;
    }

    if (hasParent(srcEventTarget, target)) {
      target = srcEventTarget;
    }

    input.target = target;
  }

  

  function inputHandler(manager, eventType, input) {
    var pointersLen = input.pointers.length;
    var changedPointersLen = input.changedPointers.length;
    var isFirst = eventType & INPUT_START && pointersLen - changedPointersLen === 0;
    var isFinal = eventType & (INPUT_END | INPUT_CANCEL) && pointersLen - changedPointersLen === 0;
    input.isFirst = !!isFirst;
    input.isFinal = !!isFinal;

    if (isFirst) {
      manager.session = {};
    } 
    


    input.eventType = eventType; 

    computeInputData(manager, input); 

    manager.emit('hammer.input', input);
    manager.recognize(input);
    manager.session.prevInput = input;
  }

  
  function splitStr(str) {
    return str.trim().split(/\s+/g);
  }

  

  function addEventListeners(target, types, handler) {
    each(splitStr(types), function (type) {
      target.addEventListener(type, handler, false);
    });
  }

  

  function removeEventListeners(target, types, handler) {
    each(splitStr(types), function (type) {
      target.removeEventListener(type, handler, false);
    });
  }

  
  function getWindowForElement(element) {
    var doc = element.ownerDocument || element;
    return doc.defaultView || doc.parentWindow || window;
  }

  

  var Input =
  
  function () {
    function Input(manager, callback) {
      var self = this;
      this.manager = manager;
      this.callback = callback;
      this.element = manager.element;
      this.target = manager.options.inputTarget; 
      

      this.domHandler = function (ev) {
        if (boolOrFn(manager.options.enable, [manager])) {
          self.handler(ev);
        }
      };

      this.init();
    }
    


    var _proto = Input.prototype;

    _proto.handler = function handler() {};
    


    _proto.init = function init() {
      this.evEl && addEventListeners(this.element, this.evEl, this.domHandler);
      this.evTarget && addEventListeners(this.target, this.evTarget, this.domHandler);
      this.evWin && addEventListeners(getWindowForElement(this.element), this.evWin, this.domHandler);
    };
    


    _proto.destroy = function destroy() {
      this.evEl && removeEventListeners(this.element, this.evEl, this.domHandler);
      this.evTarget && removeEventListeners(this.target, this.evTarget, this.domHandler);
      this.evWin && removeEventListeners(getWindowForElement(this.element), this.evWin, this.domHandler);
    };

    return Input;
  }();

  
  function inArray(src, find, findByKey) {
    if (src.indexOf && !findByKey) {
      return src.indexOf(find);
    } else {
      var i = 0;

      while (i < src.length) {
        if (findByKey && src[i][findByKey] == find || !findByKey && src[i] === find) {
          
          return i;
        }

        i++;
      }

      return -1;
    }
  }

  var POINTER_INPUT_MAP = {
    pointerdown: INPUT_START,
    pointermove: INPUT_MOVE,
    pointerup: INPUT_END,
    pointercancel: INPUT_CANCEL,
    pointerout: INPUT_CANCEL
  }; 

  var IE10_POINTER_TYPE_ENUM = {
    2: INPUT_TYPE_TOUCH,
    3: INPUT_TYPE_PEN,
    4: INPUT_TYPE_MOUSE,
    5: INPUT_TYPE_KINECT 

  };
  var POINTER_ELEMENT_EVENTS = 'pointerdown';
  var POINTER_WINDOW_EVENTS = 'pointermove pointerup pointercancel'; 

  if (win.MSPointerEvent && !win.PointerEvent) {
    POINTER_ELEMENT_EVENTS = 'MSPointerDown';
    POINTER_WINDOW_EVENTS = 'MSPointerMove MSPointerUp MSPointerCancel';
  }
  


  var PointerEventInput =
  
  function (_Input) {
    _inheritsLoose(PointerEventInput, _Input);

    function PointerEventInput() {
      var _this;

      var proto = PointerEventInput.prototype;
      proto.evEl = POINTER_ELEMENT_EVENTS;
      proto.evWin = POINTER_WINDOW_EVENTS;
      _this = _Input.apply(this, arguments) || this;
      _this.store = _this.manager.session.pointerEvents = [];
      return _this;
    }
    


    var _proto = PointerEventInput.prototype;

    _proto.handler = function handler(ev) {
      var store = this.store;
      var removePointer = false;
      var eventTypeNormalized = ev.type.toLowerCase().replace('ms', '');
      var eventType = POINTER_INPUT_MAP[eventTypeNormalized];
      var pointerType = IE10_POINTER_TYPE_ENUM[ev.pointerType] || ev.pointerType;
      var isTouch = pointerType === INPUT_TYPE_TOUCH; 

      var storeIndex = inArray(store, ev.pointerId, 'pointerId'); 

      if (eventType & INPUT_START && (ev.button === 0 || isTouch)) {
        if (storeIndex < 0) {
          store.push(ev);
          storeIndex = store.length - 1;
        }
      } else if (eventType & (INPUT_END | INPUT_CANCEL)) {
        removePointer = true;
      } 


      if (storeIndex < 0) {
        return;
      } 


      store[storeIndex] = ev;
      this.callback(this.manager, eventType, {
        pointers: store,
        changedPointers: [ev],
        pointerType: pointerType,
        srcEvent: ev
      });

      if (removePointer) {
        
        store.splice(storeIndex, 1);
      }
    };

    return PointerEventInput;
  }(Input);

  
  function toArray(obj) {
    return Array.prototype.slice.call(obj, 0);
  }

  

  function uniqueArray(src, key, sort) {
    var results = [];
    var values = [];
    var i = 0;

    while (i < src.length) {
      var val = key ? src[i][key] : src[i];

      if (inArray(values, val) < 0) {
        results.push(src[i]);
      }

      values[i] = val;
      i++;
    }

    if (sort) {
      if (!key) {
        results = results.sort();
      } else {
        results = results.sort(function (a, b) {
          return a[key] > b[key];
        });
      }
    }

    return results;
  }

  var TOUCH_INPUT_MAP = {
    touchstart: INPUT_START,
    touchmove: INPUT_MOVE,
    touchend: INPUT_END,
    touchcancel: INPUT_CANCEL
  };
  var TOUCH_TARGET_EVENTS = 'touchstart touchmove touchend touchcancel';
  

  var TouchInput =
  
  function (_Input) {
    _inheritsLoose(TouchInput, _Input);

    function TouchInput() {
      var _this;

      TouchInput.prototype.evTarget = TOUCH_TARGET_EVENTS;
      _this = _Input.apply(this, arguments) || this;
      _this.targetIds = {}; 

      return _this;
    }

    var _proto = TouchInput.prototype;

    _proto.handler = function handler(ev) {
      var type = TOUCH_INPUT_MAP[ev.type];
      var touches = getTouches.call(this, ev, type);

      if (!touches) {
        return;
      }

      this.callback(this.manager, type, {
        pointers: touches[0],
        changedPointers: touches[1],
        pointerType: INPUT_TYPE_TOUCH,
        srcEvent: ev
      });
    };

    return TouchInput;
  }(Input);

  function getTouches(ev, type) {
    var allTouches = toArray(ev.touches);
    var targetIds = this.targetIds; 

    if (type & (INPUT_START | INPUT_MOVE) && allTouches.length === 1) {
      targetIds[allTouches[0].identifier] = true;
      return [allTouches, allTouches];
    }

    var i;
    var targetTouches;
    var changedTouches = toArray(ev.changedTouches);
    var changedTargetTouches = [];
    var target = this.target; 

    targetTouches = allTouches.filter(function (touch) {
      return hasParent(touch.target, target);
    }); 

    if (type === INPUT_START) {
      i = 0;

      while (i < targetTouches.length) {
        targetIds[targetTouches[i].identifier] = true;
        i++;
      }
    } 


    i = 0;

    while (i < changedTouches.length) {
      if (targetIds[changedTouches[i].identifier]) {
        changedTargetTouches.push(changedTouches[i]);
      } 


      if (type & (INPUT_END | INPUT_CANCEL)) {
        delete targetIds[changedTouches[i].identifier];
      }

      i++;
    }

    if (!changedTargetTouches.length) {
      return;
    }

    return [
    uniqueArray(targetTouches.concat(changedTargetTouches), 'identifier', true), changedTargetTouches];
  }

  var MOUSE_INPUT_MAP = {
    mousedown: INPUT_START,
    mousemove: INPUT_MOVE,
    mouseup: INPUT_END
  };
  var MOUSE_ELEMENT_EVENTS = 'mousedown';
  var MOUSE_WINDOW_EVENTS = 'mousemove mouseup';
  

  var MouseInput =
  
  function (_Input) {
    _inheritsLoose(MouseInput, _Input);

    function MouseInput() {
      var _this;

      var proto = MouseInput.prototype;
      proto.evEl = MOUSE_ELEMENT_EVENTS;
      proto.evWin = MOUSE_WINDOW_EVENTS;
      _this = _Input.apply(this, arguments) || this;
      _this.pressed = false; 

      return _this;
    }
    


    var _proto = MouseInput.prototype;

    _proto.handler = function handler(ev) {
      var eventType = MOUSE_INPUT_MAP[ev.type]; 

      if (eventType & INPUT_START && ev.button === 0) {
        this.pressed = true;
      }

      if (eventType & INPUT_MOVE && ev.which !== 1) {
        eventType = INPUT_END;
      } 


      if (!this.pressed) {
        return;
      }

      if (eventType & INPUT_END) {
        this.pressed = false;
      }

      this.callback(this.manager, eventType, {
        pointers: [ev],
        changedPointers: [ev],
        pointerType: INPUT_TYPE_MOUSE,
        srcEvent: ev
      });
    };

    return MouseInput;
  }(Input);

  

  var DEDUP_TIMEOUT = 2500;
  var DEDUP_DISTANCE = 25;

  function setLastTouch(eventData) {
    var _eventData$changedPoi = eventData.changedPointers,
        touch = _eventData$changedPoi[0];

    if (touch.identifier === this.primaryTouch) {
      var lastTouch = {
        x: touch.clientX,
        y: touch.clientY
      };
      var lts = this.lastTouches;
      this.lastTouches.push(lastTouch);

      var removeLastTouch = function removeLastTouch() {
        var i = lts.indexOf(lastTouch);

        if (i > -1) {
          lts.splice(i, 1);
        }
      };

      setTimeout(removeLastTouch, DEDUP_TIMEOUT);
    }
  }

  function recordTouches(eventType, eventData) {
    if (eventType & INPUT_START) {
      this.primaryTouch = eventData.changedPointers[0].identifier;
      setLastTouch.call(this, eventData);
    } else if (eventType & (INPUT_END | INPUT_CANCEL)) {
      setLastTouch.call(this, eventData);
    }
  }

  function isSyntheticEvent(eventData) {
    var x = eventData.srcEvent.clientX;
    var y = eventData.srcEvent.clientY;

    for (var i = 0; i < this.lastTouches.length; i++) {
      var t = this.lastTouches[i];
      var dx = Math.abs(x - t.x);
      var dy = Math.abs(y - t.y);

      if (dx <= DEDUP_DISTANCE && dy <= DEDUP_DISTANCE) {
        return true;
      }
    }

    return false;
  }

  var TouchMouseInput =
  
  function () {
    var TouchMouseInput =
    
    function (_Input) {
      _inheritsLoose(TouchMouseInput, _Input);

      function TouchMouseInput(_manager, callback) {
        var _this;

        _this = _Input.call(this, _manager, callback) || this;

        _this.handler = function (manager, inputEvent, inputData) {
          var isTouch = inputData.pointerType === INPUT_TYPE_TOUCH;
          var isMouse = inputData.pointerType === INPUT_TYPE_MOUSE;

          if (isMouse && inputData.sourceCapabilities && inputData.sourceCapabilities.firesTouchEvents) {
            return;
          } 


          if (isTouch) {
            recordTouches.call(_assertThisInitialized$1(_assertThisInitialized$1(_this)), inputEvent, inputData);
          } else if (isMouse && isSyntheticEvent.call(_assertThisInitialized$1(_assertThisInitialized$1(_this)), inputData)) {
            return;
          }

          _this.callback(manager, inputEvent, inputData);
        };

        _this.touch = new TouchInput(_this.manager, _this.handler);
        _this.mouse = new MouseInput(_this.manager, _this.handler);
        _this.primaryTouch = null;
        _this.lastTouches = [];
        return _this;
      }
      


      var _proto = TouchMouseInput.prototype;

      
      _proto.destroy = function destroy() {
        this.touch.destroy();
        this.mouse.destroy();
      };

      return TouchMouseInput;
    }(Input);

    return TouchMouseInput;
  }();

  

  function createInputInstance(manager) {
    var Type; 

    var inputClass = manager.options.inputClass;

    if (inputClass) {
      Type = inputClass;
    } else if (SUPPORT_POINTER_EVENTS) {
      Type = PointerEventInput;
    } else if (SUPPORT_ONLY_TOUCH) {
      Type = TouchInput;
    } else if (!SUPPORT_TOUCH) {
      Type = MouseInput;
    } else {
      Type = TouchMouseInput;
    }

    return new Type(manager, inputHandler);
  }

  

  function invokeArrayArg(arg, fn, context) {
    if (Array.isArray(arg)) {
      each(arg, context[fn], context);
      return true;
    }

    return false;
  }

  var STATE_POSSIBLE = 1;
  var STATE_BEGAN = 2;
  var STATE_CHANGED = 4;
  var STATE_ENDED = 8;
  var STATE_RECOGNIZED = STATE_ENDED;
  var STATE_CANCELLED = 16;
  var STATE_FAILED = 32;

  
  var _uniqueId = 1;
  function uniqueId() {
    return _uniqueId++;
  }

  
  function getRecognizerByNameIfManager(otherRecognizer, recognizer) {
    var manager = recognizer.manager;

    if (manager) {
      return manager.get(otherRecognizer);
    }

    return otherRecognizer;
  }

  

  function stateStr(state) {
    if (state & STATE_CANCELLED) {
      return 'cancel';
    } else if (state & STATE_ENDED) {
      return 'end';
    } else if (state & STATE_CHANGED) {
      return 'move';
    } else if (state & STATE_BEGAN) {
      return 'start';
    }

    return '';
  }

  

  

  var Recognizer =
  
  function () {
    function Recognizer(options) {
      if (options === void 0) {
        options = {};
      }

      this.options = _extends({
        enable: true
      }, options);
      this.id = uniqueId();
      this.manager = null; 

      this.state = STATE_POSSIBLE;
      this.simultaneous = {};
      this.requireFail = [];
    }
    


    var _proto = Recognizer.prototype;

    _proto.set = function set(options) {
      assign$1(this.options, options); 

      this.manager && this.manager.touchAction.update();
      return this;
    };
    


    _proto.recognizeWith = function recognizeWith(otherRecognizer) {
      if (invokeArrayArg(otherRecognizer, 'recognizeWith', this)) {
        return this;
      }

      var simultaneous = this.simultaneous;
      otherRecognizer = getRecognizerByNameIfManager(otherRecognizer, this);

      if (!simultaneous[otherRecognizer.id]) {
        simultaneous[otherRecognizer.id] = otherRecognizer;
        otherRecognizer.recognizeWith(this);
      }

      return this;
    };
    


    _proto.dropRecognizeWith = function dropRecognizeWith(otherRecognizer) {
      if (invokeArrayArg(otherRecognizer, 'dropRecognizeWith', this)) {
        return this;
      }

      otherRecognizer = getRecognizerByNameIfManager(otherRecognizer, this);
      delete this.simultaneous[otherRecognizer.id];
      return this;
    };
    


    _proto.requireFailure = function requireFailure(otherRecognizer) {
      if (invokeArrayArg(otherRecognizer, 'requireFailure', this)) {
        return this;
      }

      var requireFail = this.requireFail;
      otherRecognizer = getRecognizerByNameIfManager(otherRecognizer, this);

      if (inArray(requireFail, otherRecognizer) === -1) {
        requireFail.push(otherRecognizer);
        otherRecognizer.requireFailure(this);
      }

      return this;
    };
    


    _proto.dropRequireFailure = function dropRequireFailure(otherRecognizer) {
      if (invokeArrayArg(otherRecognizer, 'dropRequireFailure', this)) {
        return this;
      }

      otherRecognizer = getRecognizerByNameIfManager(otherRecognizer, this);
      var index = inArray(this.requireFail, otherRecognizer);

      if (index > -1) {
        this.requireFail.splice(index, 1);
      }

      return this;
    };
    


    _proto.hasRequireFailures = function hasRequireFailures() {
      return this.requireFail.length > 0;
    };
    


    _proto.canRecognizeWith = function canRecognizeWith(otherRecognizer) {
      return !!this.simultaneous[otherRecognizer.id];
    };
    


    _proto.emit = function emit(input) {
      var self = this;
      var state = this.state;

      function emit(event) {
        self.manager.emit(event, input);
      } 


      if (state < STATE_ENDED) {
        emit(self.options.event + stateStr(state));
      }

      emit(self.options.event); 

      if (input.additionalEvent) {
        
        emit(input.additionalEvent);
      } 


      if (state >= STATE_ENDED) {
        emit(self.options.event + stateStr(state));
      }
    };
    


    _proto.tryEmit = function tryEmit(input) {
      if (this.canEmit()) {
        return this.emit(input);
      } 


      this.state = STATE_FAILED;
    };
    


    _proto.canEmit = function canEmit() {
      var i = 0;

      while (i < this.requireFail.length) {
        if (!(this.requireFail[i].state & (STATE_FAILED | STATE_POSSIBLE))) {
          return false;
        }

        i++;
      }

      return true;
    };
    


    _proto.recognize = function recognize(inputData) {
      
      
      var inputDataClone = assign$1({}, inputData); 

      if (!boolOrFn(this.options.enable, [this, inputDataClone])) {
        this.reset();
        this.state = STATE_FAILED;
        return;
      } 


      if (this.state & (STATE_RECOGNIZED | STATE_CANCELLED | STATE_FAILED)) {
        this.state = STATE_POSSIBLE;
      }

      this.state = this.process(inputDataClone); 
      

      if (this.state & (STATE_BEGAN | STATE_CHANGED | STATE_ENDED | STATE_CANCELLED)) {
        this.tryEmit(inputDataClone);
      }
    };
    

    


    _proto.process = function process(inputData) {};
    

    


    _proto.getTouchAction = function getTouchAction() {};
    


    _proto.reset = function reset() {};

    return Recognizer;
  }();

  

  var TapRecognizer =
  
  function (_Recognizer) {
    _inheritsLoose(TapRecognizer, _Recognizer);

    function TapRecognizer(options) {
      var _this;

      if (options === void 0) {
        options = {};
      }

      _this = _Recognizer.call(this, _extends({
        event: 'tap',
        pointers: 1,
        taps: 1,
        interval: 300,
        
        time: 250,
        
        threshold: 9,
        
        posThreshold: 10
      }, options)) || this; 
      

      _this.pTime = false;
      _this.pCenter = false;
      _this._timer = null;
      _this._input = null;
      _this.count = 0;
      return _this;
    }

    var _proto = TapRecognizer.prototype;

    _proto.getTouchAction = function getTouchAction() {
      return [TOUCH_ACTION_MANIPULATION];
    };

    _proto.process = function process(input) {
      var _this2 = this;

      var options = this.options;
      var validPointers = input.pointers.length === options.pointers;
      var validMovement = input.distance < options.threshold;
      var validTouchTime = input.deltaTime < options.time;
      this.reset();

      if (input.eventType & INPUT_START && this.count === 0) {
        return this.failTimeout();
      } 
      


      if (validMovement && validTouchTime && validPointers) {
        if (input.eventType !== INPUT_END) {
          return this.failTimeout();
        }

        var validInterval = this.pTime ? input.timeStamp - this.pTime < options.interval : true;
        var validMultiTap = !this.pCenter || getDistance(this.pCenter, input.center) < options.posThreshold;
        this.pTime = input.timeStamp;
        this.pCenter = input.center;

        if (!validMultiTap || !validInterval) {
          this.count = 1;
        } else {
          this.count += 1;
        }

        this._input = input; 
        

        var tapCount = this.count % options.taps;

        if (tapCount === 0) {
          
          
          if (!this.hasRequireFailures()) {
            return STATE_RECOGNIZED;
          } else {
            this._timer = setTimeout(function () {
              _this2.state = STATE_RECOGNIZED;

              _this2.tryEmit();
            }, options.interval);
            return STATE_BEGAN;
          }
        }
      }

      return STATE_FAILED;
    };

    _proto.failTimeout = function failTimeout() {
      var _this3 = this;

      this._timer = setTimeout(function () {
        _this3.state = STATE_FAILED;
      }, this.options.interval);
      return STATE_FAILED;
    };

    _proto.reset = function reset() {
      clearTimeout(this._timer);
    };

    _proto.emit = function emit() {
      if (this.state === STATE_RECOGNIZED) {
        this._input.tapCount = this.count;
        this.manager.emit(this.options.event, this._input);
      }
    };

    return TapRecognizer;
  }(Recognizer);

  

  var AttrRecognizer =
  
  function (_Recognizer) {
    _inheritsLoose(AttrRecognizer, _Recognizer);

    function AttrRecognizer(options) {
      if (options === void 0) {
        options = {};
      }

      return _Recognizer.call(this, _extends({
        pointers: 1
      }, options)) || this;
    }
    


    var _proto = AttrRecognizer.prototype;

    _proto.attrTest = function attrTest(input) {
      var optionPointers = this.options.pointers;
      return optionPointers === 0 || input.pointers.length === optionPointers;
    };
    


    _proto.process = function process(input) {
      var state = this.state;
      var eventType = input.eventType;
      var isRecognized = state & (STATE_BEGAN | STATE_CHANGED);
      var isValid = this.attrTest(input); 

      if (isRecognized && (eventType & INPUT_CANCEL || !isValid)) {
        return state | STATE_CANCELLED;
      } else if (isRecognized || isValid) {
        if (eventType & INPUT_END) {
          return state | STATE_ENDED;
        } else if (!(state & STATE_BEGAN)) {
          return STATE_BEGAN;
        }

        return state | STATE_CHANGED;
      }

      return STATE_FAILED;
    };

    return AttrRecognizer;
  }(Recognizer);

  

  function directionStr(direction) {
    if (direction === DIRECTION_DOWN) {
      return 'down';
    } else if (direction === DIRECTION_UP) {
      return 'up';
    } else if (direction === DIRECTION_LEFT) {
      return 'left';
    } else if (direction === DIRECTION_RIGHT) {
      return 'right';
    }

    return '';
  }

  

  var PanRecognizer =
  
  function (_AttrRecognizer) {
    _inheritsLoose(PanRecognizer, _AttrRecognizer);

    function PanRecognizer(options) {
      var _this;

      if (options === void 0) {
        options = {};
      }

      _this = _AttrRecognizer.call(this, _extends({
        event: 'pan',
        threshold: 10,
        pointers: 1,
        direction: DIRECTION_ALL
      }, options)) || this;
      _this.pX = null;
      _this.pY = null;
      return _this;
    }

    var _proto = PanRecognizer.prototype;

    _proto.getTouchAction = function getTouchAction() {
      var direction = this.options.direction;
      var actions = [];

      if (direction & DIRECTION_HORIZONTAL) {
        actions.push(TOUCH_ACTION_PAN_Y);
      }

      if (direction & DIRECTION_VERTICAL) {
        actions.push(TOUCH_ACTION_PAN_X);
      }

      return actions;
    };

    _proto.directionTest = function directionTest(input) {
      var options = this.options;
      var hasMoved = true;
      var distance = input.distance;
      var direction = input.direction;
      var x = input.deltaX;
      var y = input.deltaY; 

      if (!(direction & options.direction)) {
        if (options.direction & DIRECTION_HORIZONTAL) {
          direction = x === 0 ? DIRECTION_NONE : x < 0 ? DIRECTION_LEFT : DIRECTION_RIGHT;
          hasMoved = x !== this.pX;
          distance = Math.abs(input.deltaX);
        } else {
          direction = y === 0 ? DIRECTION_NONE : y < 0 ? DIRECTION_UP : DIRECTION_DOWN;
          hasMoved = y !== this.pY;
          distance = Math.abs(input.deltaY);
        }
      }

      input.direction = direction;
      return hasMoved && distance > options.threshold && direction & options.direction;
    };

    _proto.attrTest = function attrTest(input) {
      return AttrRecognizer.prototype.attrTest.call(this, input) && ( 
      this.state & STATE_BEGAN || !(this.state & STATE_BEGAN) && this.directionTest(input));
    };

    _proto.emit = function emit(input) {
      this.pX = input.deltaX;
      this.pY = input.deltaY;
      var direction = directionStr(input.direction);

      if (direction) {
        input.additionalEvent = this.options.event + direction;
      }

      _AttrRecognizer.prototype.emit.call(this, input);
    };

    return PanRecognizer;
  }(AttrRecognizer);

  

  var SwipeRecognizer =
  
  function (_AttrRecognizer) {
    _inheritsLoose(SwipeRecognizer, _AttrRecognizer);

    function SwipeRecognizer(options) {
      if (options === void 0) {
        options = {};
      }

      return _AttrRecognizer.call(this, _extends({
        event: 'swipe',
        threshold: 10,
        velocity: 0.3,
        direction: DIRECTION_HORIZONTAL | DIRECTION_VERTICAL,
        pointers: 1
      }, options)) || this;
    }

    var _proto = SwipeRecognizer.prototype;

    _proto.getTouchAction = function getTouchAction() {
      return PanRecognizer.prototype.getTouchAction.call(this);
    };

    _proto.attrTest = function attrTest(input) {
      var direction = this.options.direction;
      var velocity;

      if (direction & (DIRECTION_HORIZONTAL | DIRECTION_VERTICAL)) {
        velocity = input.overallVelocity;
      } else if (direction & DIRECTION_HORIZONTAL) {
        velocity = input.overallVelocityX;
      } else if (direction & DIRECTION_VERTICAL) {
        velocity = input.overallVelocityY;
      }

      return _AttrRecognizer.prototype.attrTest.call(this, input) && direction & input.offsetDirection && input.distance > this.options.threshold && input.maxPointers === this.options.pointers && abs(velocity) > this.options.velocity && input.eventType & INPUT_END;
    };

    _proto.emit = function emit(input) {
      var direction = directionStr(input.offsetDirection);

      if (direction) {
        this.manager.emit(this.options.event + direction, input);
      }

      this.manager.emit(this.options.event, input);
    };

    return SwipeRecognizer;
  }(AttrRecognizer);

  

  var PinchRecognizer =
  
  function (_AttrRecognizer) {
    _inheritsLoose(PinchRecognizer, _AttrRecognizer);

    function PinchRecognizer(options) {
      if (options === void 0) {
        options = {};
      }

      return _AttrRecognizer.call(this, _extends({
        event: 'pinch',
        threshold: 0,
        pointers: 2
      }, options)) || this;
    }

    var _proto = PinchRecognizer.prototype;

    _proto.getTouchAction = function getTouchAction() {
      return [TOUCH_ACTION_NONE];
    };

    _proto.attrTest = function attrTest(input) {
      return _AttrRecognizer.prototype.attrTest.call(this, input) && (Math.abs(input.scale - 1) > this.options.threshold || this.state & STATE_BEGAN);
    };

    _proto.emit = function emit(input) {
      if (input.scale !== 1) {
        var inOut = input.scale < 1 ? 'in' : 'out';
        input.additionalEvent = this.options.event + inOut;
      }

      _AttrRecognizer.prototype.emit.call(this, input);
    };

    return PinchRecognizer;
  }(AttrRecognizer);

  

  var RotateRecognizer =
  
  function (_AttrRecognizer) {
    _inheritsLoose(RotateRecognizer, _AttrRecognizer);

    function RotateRecognizer(options) {
      if (options === void 0) {
        options = {};
      }

      return _AttrRecognizer.call(this, _extends({
        event: 'rotate',
        threshold: 0,
        pointers: 2
      }, options)) || this;
    }

    var _proto = RotateRecognizer.prototype;

    _proto.getTouchAction = function getTouchAction() {
      return [TOUCH_ACTION_NONE];
    };

    _proto.attrTest = function attrTest(input) {
      return _AttrRecognizer.prototype.attrTest.call(this, input) && (Math.abs(input.rotation) > this.options.threshold || this.state & STATE_BEGAN);
    };

    return RotateRecognizer;
  }(AttrRecognizer);

  

  var PressRecognizer =
  
  function (_Recognizer) {
    _inheritsLoose(PressRecognizer, _Recognizer);

    function PressRecognizer(options) {
      var _this;

      if (options === void 0) {
        options = {};
      }

      _this = _Recognizer.call(this, _extends({
        event: 'press',
        pointers: 1,
        time: 251,
        
        threshold: 9
      }, options)) || this;
      _this._timer = null;
      _this._input = null;
      return _this;
    }

    var _proto = PressRecognizer.prototype;

    _proto.getTouchAction = function getTouchAction() {
      return [TOUCH_ACTION_AUTO];
    };

    _proto.process = function process(input) {
      var _this2 = this;

      var options = this.options;
      var validPointers = input.pointers.length === options.pointers;
      var validMovement = input.distance < options.threshold;
      var validTime = input.deltaTime > options.time;
      this._input = input; 
      

      if (!validMovement || !validPointers || input.eventType & (INPUT_END | INPUT_CANCEL) && !validTime) {
        this.reset();
      } else if (input.eventType & INPUT_START) {
        this.reset();
        this._timer = setTimeout(function () {
          _this2.state = STATE_RECOGNIZED;

          _this2.tryEmit();
        }, options.time);
      } else if (input.eventType & INPUT_END) {
        return STATE_RECOGNIZED;
      }

      return STATE_FAILED;
    };

    _proto.reset = function reset() {
      clearTimeout(this._timer);
    };

    _proto.emit = function emit(input) {
      if (this.state !== STATE_RECOGNIZED) {
        return;
      }

      if (input && input.eventType & INPUT_END) {
        this.manager.emit(this.options.event + "up", input);
      } else {
        this._input.timeStamp = now();
        this.manager.emit(this.options.event, this._input);
      }
    };

    return PressRecognizer;
  }(Recognizer);

  var defaults = {
    
    domEvents: false,

    
    touchAction: TOUCH_ACTION_COMPUTE,

    
    enable: true,

    
    inputTarget: null,

    
    inputClass: null,

    
    cssProps: {
      
      userSelect: "none",

      
      touchSelect: "none",

      
      touchCallout: "none",

      
      contentZooming: "none",

      
      userDrag: "none",

      
      tapHighlightColor: "rgba(0,0,0,0)"
    }
  };
  

  var preset = [[RotateRecognizer, {
    enable: false
  }], [PinchRecognizer, {
    enable: false
  }, ['rotate']], [SwipeRecognizer, {
    direction: DIRECTION_HORIZONTAL
  }], [PanRecognizer, {
    direction: DIRECTION_HORIZONTAL
  }, ['swipe']], [TapRecognizer], [TapRecognizer, {
    event: 'doubletap',
    taps: 2
  }, ['tap']], [PressRecognizer]];

  var STOP = 1;
  var FORCED_STOP = 2;
  

  function toggleCssProps(manager, add) {
    var element = manager.element;

    if (!element.style) {
      return;
    }

    var prop;
    each(manager.options.cssProps, function (value, name) {
      prop = prefixed(element.style, name);

      if (add) {
        manager.oldCssProps[prop] = element.style[prop];
        element.style[prop] = value;
      } else {
        element.style[prop] = manager.oldCssProps[prop] || "";
      }
    });

    if (!add) {
      manager.oldCssProps = {};
    }
  }
  


  function triggerDomEvent(event, data) {
    var gestureEvent = document.createEvent("Event");
    gestureEvent.initEvent(event, true, true);
    gestureEvent.gesture = data;
    data.target.dispatchEvent(gestureEvent);
  }
  


  var Manager =
  
  function () {
    function Manager(element, options) {
      var _this = this;

      this.options = assign$1({}, defaults, options || {});
      this.options.inputTarget = this.options.inputTarget || element;
      this.handlers = {};
      this.session = {};
      this.recognizers = [];
      this.oldCssProps = {};
      this.element = element;
      this.input = createInputInstance(this);
      this.touchAction = new TouchAction(this, this.options.touchAction);
      toggleCssProps(this, true);
      each(this.options.recognizers, function (item) {
        var recognizer = _this.add(new item[0](item[1]));

        item[2] && recognizer.recognizeWith(item[2]);
        item[3] && recognizer.requireFailure(item[3]);
      }, this);
    }
    


    var _proto = Manager.prototype;

    _proto.set = function set(options) {
      assign$1(this.options, options); 

      if (options.touchAction) {
        this.touchAction.update();
      }

      if (options.inputTarget) {
        
        this.input.destroy();
        this.input.target = options.inputTarget;
        this.input.init();
      }

      return this;
    };
    


    _proto.stop = function stop(force) {
      this.session.stopped = force ? FORCED_STOP : STOP;
    };
    


    _proto.recognize = function recognize(inputData) {
      var session = this.session;

      if (session.stopped) {
        return;
      } 


      this.touchAction.preventDefaults(inputData);
      var recognizer;
      var recognizers = this.recognizers; 
      
      

      var curRecognizer = session.curRecognizer; 
      

      if (!curRecognizer || curRecognizer && curRecognizer.state & STATE_RECOGNIZED) {
        session.curRecognizer = null;
        curRecognizer = null;
      }

      var i = 0;

      while (i < recognizers.length) {
        recognizer = recognizers[i]; 
        
        
        
        
        

        if (session.stopped !== FORCED_STOP && ( 
        !curRecognizer || recognizer === curRecognizer || 
        recognizer.canRecognizeWith(curRecognizer))) {
          
          recognizer.recognize(inputData);
        } else {
          recognizer.reset();
        } 
        


        if (!curRecognizer && recognizer.state & (STATE_BEGAN | STATE_CHANGED | STATE_ENDED)) {
          session.curRecognizer = recognizer;
          curRecognizer = recognizer;
        }

        i++;
      }
    };
    


    _proto.get = function get(recognizer) {
      if (recognizer instanceof Recognizer) {
        return recognizer;
      }

      var recognizers = this.recognizers;

      for (var i = 0; i < recognizers.length; i++) {
        if (recognizers[i].options.event === recognizer) {
          return recognizers[i];
        }
      }

      return null;
    };
    


    _proto.add = function add(recognizer) {
      if (invokeArrayArg(recognizer, "add", this)) {
        return this;
      } 


      var existing = this.get(recognizer.options.event);

      if (existing) {
        this.remove(existing);
      }

      this.recognizers.push(recognizer);
      recognizer.manager = this;
      this.touchAction.update();
      return recognizer;
    };
    


    _proto.remove = function remove(recognizer) {
      if (invokeArrayArg(recognizer, "remove", this)) {
        return this;
      }

      var targetRecognizer = this.get(recognizer); 

      if (recognizer) {
        var recognizers = this.recognizers;
        var index = inArray(recognizers, targetRecognizer);

        if (index !== -1) {
          recognizers.splice(index, 1);
          this.touchAction.update();
        }
      }

      return this;
    };
    


    _proto.on = function on(events, handler) {
      if (events === undefined || handler === undefined) {
        return this;
      }

      var handlers = this.handlers;
      each(splitStr(events), function (event) {
        handlers[event] = handlers[event] || [];
        handlers[event].push(handler);
      });
      return this;
    };
    


    _proto.off = function off(events, handler) {
      if (events === undefined) {
        return this;
      }

      var handlers = this.handlers;
      each(splitStr(events), function (event) {
        if (!handler) {
          delete handlers[event];
        } else {
          handlers[event] && handlers[event].splice(inArray(handlers[event], handler), 1);
        }
      });
      return this;
    };
    


    _proto.emit = function emit(event, data) {
      
      if (this.options.domEvents) {
        triggerDomEvent(event, data);
      } 


      var handlers = this.handlers[event] && this.handlers[event].slice();

      if (!handlers || !handlers.length) {
        return;
      }

      data.type = event;

      data.preventDefault = function () {
        data.srcEvent.preventDefault();
      };

      var i = 0;

      while (i < handlers.length) {
        handlers[i](data);
        i++;
      }
    };
    


    _proto.destroy = function destroy() {
      this.element && toggleCssProps(this, false);
      this.handlers = {};
      this.session = {};
      this.input.destroy();
      this.element = null;
    };

    return Manager;
  }();

  var SINGLE_TOUCH_INPUT_MAP = {
    touchstart: INPUT_START,
    touchmove: INPUT_MOVE,
    touchend: INPUT_END,
    touchcancel: INPUT_CANCEL
  };
  var SINGLE_TOUCH_TARGET_EVENTS = 'touchstart';
  var SINGLE_TOUCH_WINDOW_EVENTS = 'touchstart touchmove touchend touchcancel';
  

  var SingleTouchInput =
  
  function (_Input) {
    _inheritsLoose(SingleTouchInput, _Input);

    function SingleTouchInput() {
      var _this;

      var proto = SingleTouchInput.prototype;
      proto.evTarget = SINGLE_TOUCH_TARGET_EVENTS;
      proto.evWin = SINGLE_TOUCH_WINDOW_EVENTS;
      _this = _Input.apply(this, arguments) || this;
      _this.started = false;
      return _this;
    }

    var _proto = SingleTouchInput.prototype;

    _proto.handler = function handler(ev) {
      var type = SINGLE_TOUCH_INPUT_MAP[ev.type]; 

      if (type === INPUT_START) {
        this.started = true;
      }

      if (!this.started) {
        return;
      }

      var touches = normalizeSingleTouches.call(this, ev, type); 

      if (type & (INPUT_END | INPUT_CANCEL) && touches[0].length - touches[1].length === 0) {
        this.started = false;
      }

      this.callback(this.manager, type, {
        pointers: touches[0],
        changedPointers: touches[1],
        pointerType: INPUT_TYPE_TOUCH,
        srcEvent: ev
      });
    };

    return SingleTouchInput;
  }(Input);

  function normalizeSingleTouches(ev, type) {
    var all = toArray(ev.touches);
    var changed = toArray(ev.changedTouches);

    if (type & (INPUT_END | INPUT_CANCEL)) {
      all = uniqueArray(all.concat(changed), 'identifier', true);
    }

    return [all, changed];
  }

  
  function deprecate(method, name, message) {
    var deprecationMessage = "DEPRECATED METHOD: " + name + "\n" + message + " AT \n";
    return function () {
      var e = new Error('get-stack-trace');
      var stack = e && e.stack ? e.stack.replace(/^[^\(]+?[\n$]/gm, '').replace(/^\s+at\s+/gm, '').replace(/^Object.<anonymous>\s*\(/gm, '{anonymous}()@') : 'Unknown Stack Trace';
      var log = window.console && (window.console.warn || window.console.log);

      if (log) {
        log.call(window.console, deprecationMessage, stack);
      }

      return method.apply(this, arguments);
    };
  }

  

  var extend = deprecate(function (dest, src, merge) {
    var keys = Object.keys(src);
    var i = 0;

    while (i < keys.length) {
      if (!merge || merge && dest[keys[i]] === undefined) {
        dest[keys[i]] = src[keys[i]];
      }

      i++;
    }

    return dest;
  }, 'extend', 'Use `assign`.');

  

  var merge$1 = deprecate(function (dest, src) {
    return extend(dest, src, true);
  }, 'merge', 'Use `assign`.');

  

  function inherit(child, base, properties) {
    var baseP = base.prototype;
    var childP;
    childP = child.prototype = Object.create(baseP);
    childP.constructor = child;
    childP._super = baseP;

    if (properties) {
      assign$1(childP, properties);
    }
  }

  
  function bindFn(fn, context) {
    return function boundFn() {
      return fn.apply(context, arguments);
    };
  }

  

  var Hammer =
  
  function () {
    var Hammer =
    
    function Hammer(element, options) {
      if (options === void 0) {
        options = {};
      }

      return new Manager(element, _extends({
        recognizers: preset.concat()
      }, options));
    };

    Hammer.VERSION = "2.0.17-rc";
    Hammer.DIRECTION_ALL = DIRECTION_ALL;
    Hammer.DIRECTION_DOWN = DIRECTION_DOWN;
    Hammer.DIRECTION_LEFT = DIRECTION_LEFT;
    Hammer.DIRECTION_RIGHT = DIRECTION_RIGHT;
    Hammer.DIRECTION_UP = DIRECTION_UP;
    Hammer.DIRECTION_HORIZONTAL = DIRECTION_HORIZONTAL;
    Hammer.DIRECTION_VERTICAL = DIRECTION_VERTICAL;
    Hammer.DIRECTION_NONE = DIRECTION_NONE;
    Hammer.DIRECTION_DOWN = DIRECTION_DOWN;
    Hammer.INPUT_START = INPUT_START;
    Hammer.INPUT_MOVE = INPUT_MOVE;
    Hammer.INPUT_END = INPUT_END;
    Hammer.INPUT_CANCEL = INPUT_CANCEL;
    Hammer.STATE_POSSIBLE = STATE_POSSIBLE;
    Hammer.STATE_BEGAN = STATE_BEGAN;
    Hammer.STATE_CHANGED = STATE_CHANGED;
    Hammer.STATE_ENDED = STATE_ENDED;
    Hammer.STATE_RECOGNIZED = STATE_RECOGNIZED;
    Hammer.STATE_CANCELLED = STATE_CANCELLED;
    Hammer.STATE_FAILED = STATE_FAILED;
    Hammer.Manager = Manager;
    Hammer.Input = Input;
    Hammer.TouchAction = TouchAction;
    Hammer.TouchInput = TouchInput;
    Hammer.MouseInput = MouseInput;
    Hammer.PointerEventInput = PointerEventInput;
    Hammer.TouchMouseInput = TouchMouseInput;
    Hammer.SingleTouchInput = SingleTouchInput;
    Hammer.Recognizer = Recognizer;
    Hammer.AttrRecognizer = AttrRecognizer;
    Hammer.Tap = TapRecognizer;
    Hammer.Pan = PanRecognizer;
    Hammer.Swipe = SwipeRecognizer;
    Hammer.Pinch = PinchRecognizer;
    Hammer.Rotate = RotateRecognizer;
    Hammer.Press = PressRecognizer;
    Hammer.on = addEventListeners;
    Hammer.off = removeEventListeners;
    Hammer.each = each;
    Hammer.merge = merge$1;
    Hammer.extend = extend;
    Hammer.bindFn = bindFn;
    Hammer.assign = assign$1;
    Hammer.inherit = inherit;
    Hammer.bindFn = bindFn;
    Hammer.prefixed = prefixed;
    Hammer.toArray = toArray;
    Hammer.inArray = inArray;
    Hammer.uniqueArray = uniqueArray;
    Hammer.splitStr = splitStr;
    Hammer.boolOrFn = boolOrFn;
    Hammer.hasParent = hasParent;
    Hammer.addEventListeners = addEventListeners;
    Hammer.removeEventListeners = removeEventListeners;
    Hammer.defaults = assign$1({}, defaults, {
      preset: preset
    });
    return Hammer;
  }();

  var RealHammer = Hammer;

  function _createForOfIteratorHelper$3(o, allowArrayLike) { var it = typeof _Symbol !== "undefined" && _getIteratorMethod(o) || o["@@iterator"]; if (!it) { if (_Array$isArray(o) || (it = _unsupportedIterableToArray$3(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }
  function _unsupportedIterableToArray$3(o, minLen) { var _context21; if (!o) return; if (typeof o === "string") return _arrayLikeToArray$3(o, minLen); var n = _sliceInstanceProperty(_context21 = Object.prototype.toString.call(o)).call(_context21, 8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return _Array$from$1(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray$3(o, minLen); }
  function _arrayLikeToArray$3(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }

  
  var DELETE = _Symbol("DELETE");
  
  function pureDeepObjectAssign(base) {
    var _context;
    for (var _len = arguments.length, updates = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      updates[_key - 1] = arguments[_key];
    }
    return deepObjectAssign.apply(void 0, _concatInstanceProperty(_context = [{}, base]).call(_context, updates));
  }
  
  function deepObjectAssign() {
    var merged = deepObjectAssignNonentry.apply(void 0, arguments);
    stripDelete(merged);
    return merged;
  }
  
  function deepObjectAssignNonentry() {
    for (var _len2 = arguments.length, values = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
      values[_key2] = arguments[_key2];
    }
    if (values.length < 2) {
      return values[0];
    } else if (values.length > 2) {
      var _context2;
      return deepObjectAssignNonentry.apply(void 0, _concatInstanceProperty(_context2 = [deepObjectAssign(values[0], values[1])]).call(_context2, _toConsumableArray(_sliceInstanceProperty(values).call(values, 2))));
    }
    var a = values[0];
    var b = values[1];
    var _iterator = _createForOfIteratorHelper$3(_Reflect$ownKeys(b)),
      _step;
    try {
      for (_iterator.s(); !(_step = _iterator.n()).done;) {
        var prop = _step.value;
        if (!Object.prototype.propertyIsEnumerable.call(b, prop)) ;else if (b[prop] === DELETE) {
          delete a[prop];
        } else if (a[prop] !== null && b[prop] !== null && _typeof$1(a[prop]) === "object" && _typeof$1(b[prop]) === "object" && !_Array$isArray(a[prop]) && !_Array$isArray(b[prop])) {
          a[prop] = deepObjectAssignNonentry(a[prop], b[prop]);
        } else {
          a[prop] = clone(b[prop]);
        }
      }
    } catch (err) {
      _iterator.e(err);
    } finally {
      _iterator.f();
    }
    return a;
  }
  
  function clone(a) {
    if (_Array$isArray(a)) {
      return _mapInstanceProperty(a).call(a, function (value) {
        return clone(value);
      });
    } else if (_typeof$1(a) === "object" && a !== null) {
      return deepObjectAssignNonentry({}, a);
    } else {
      return a;
    }
  }
  
  function stripDelete(a) {
    for (var _i = 0, _Object$keys$1 = _Object$keys(a); _i < _Object$keys$1.length; _i++) {
      var prop = _Object$keys$1[_i];
      if (a[prop] === DELETE) {
        delete a[prop];
      } else if (_typeof$1(a[prop]) === "object" && a[prop] !== null) {
        stripDelete(a[prop]);
      }
    }
  }

  
  function hammerMock() {
    var noop = function noop() {};
    return {
      on: noop,
      off: noop,
      destroy: noop,
      emit: noop,
      get: function get() {
        return {
          set: noop
        };
      }
    };
  }
  var Hammer$1 = typeof window !== "undefined" ? window.Hammer || RealHammer : function () {
    
    return hammerMock();
  };

  
  function Activator$1(container) {
    var _this = this,
      _context3;
    this._cleanupQueue = [];
    this.active = false;
    this._dom = {
      container: container,
      overlay: document.createElement("div")
    };
    this._dom.overlay.classList.add("vis-overlay");
    this._dom.container.appendChild(this._dom.overlay);
    this._cleanupQueue.push(function () {
      _this._dom.overlay.parentNode.removeChild(_this._dom.overlay);
    });
    var hammer = Hammer$1(this._dom.overlay);
    hammer.on("tap", _bindInstanceProperty$1(_context3 = this._onTapOverlay).call(_context3, this));
    this._cleanupQueue.push(function () {
      hammer.destroy();
      
      
    });

    
    var events = ["tap", "doubletap", "press", "pinch", "pan", "panstart", "panmove", "panend"];
    _forEachInstanceProperty(events).call(events, function (event) {
      hammer.on(event, function (event) {
        event.srcEvent.stopPropagation();
      });
    });

    
    if (document && document.body) {
      this._onClick = function (event) {
        if (!_hasParent(event.target, container)) {
          _this.deactivate();
        }
      };
      document.body.addEventListener("click", this._onClick);
      this._cleanupQueue.push(function () {
        document.body.removeEventListener("click", _this._onClick);
      });
    }

    
    this._escListener = function (event) {
      if ("key" in event ? event.key === "Escape" : event.keyCode === 27 ) {
        _this.deactivate();
      }
    };
  }

  
  Emitter(Activator$1.prototype);

  
  Activator$1.current = null;

  
  Activator$1.prototype.destroy = function () {
    var _context4, _context5;
    this.deactivate();
    var _iterator2 = _createForOfIteratorHelper$3(_reverseInstanceProperty(_context4 = _spliceInstanceProperty(_context5 = this._cleanupQueue).call(_context5, 0)).call(_context4)),
      _step2;
    try {
      for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
        var callback = _step2.value;
        callback();
      }
    } catch (err) {
      _iterator2.e(err);
    } finally {
      _iterator2.f();
    }
  };

  
  Activator$1.prototype.activate = function () {
    
    if (Activator$1.current) {
      Activator$1.current.deactivate();
    }
    Activator$1.current = this;
    this.active = true;
    this._dom.overlay.style.display = "none";
    this._dom.container.classList.add("vis-active");
    this.emit("change");
    this.emit("activate");

    
    
    document.body.addEventListener("keydown", this._escListener);
  };

  
  Activator$1.prototype.deactivate = function () {
    this.active = false;
    this._dom.overlay.style.display = "block";
    this._dom.container.classList.remove("vis-active");
    document.body.removeEventListener("keydown", this._escListener);
    this.emit("change");
    this.emit("deactivate");
  };

  
  Activator$1.prototype._onTapOverlay = function (event) {
    
    this.activate();
    event.srcEvent.stopPropagation();
  };

  
  function _hasParent(element, parent) {
    while (element) {
      if (element === parent) {
        return true;
      }
      element = element.parentNode;
    }
    return false;
  }

  var constructExports = {};
  var construct$2 = {
    get exports(){ return constructExports; },
    set exports(v){ constructExports = v; },
  };

  var isConstructor = isConstructor$4;
  var tryToString$1 = tryToString$6;

  var $TypeError$3 = TypeError;

  
  var aConstructor$2 = function (argument) {
    if (isConstructor(argument)) return argument;
    throw $TypeError$3(tryToString$1(argument) + ' is not a constructor');
  };

  var $$g = _export;
  var getBuiltIn$4 = getBuiltIn$f;
  var apply$1 = functionApply;
  var bind$9 = functionBind;
  var aConstructor$1 = aConstructor$2;
  var anObject$3 = anObject$d;
  var isObject$6 = isObject$i;
  var create$6 = objectCreate;
  var fails$8 = fails$w;

  var nativeConstruct = getBuiltIn$4('Reflect', 'construct');
  var ObjectPrototype = Object.prototype;
  var push$2 = [].push;

  
  
  
  
  var NEW_TARGET_BUG = fails$8(function () {
    function F() {  }
    return !(nativeConstruct(function () {  }, [], F) instanceof F);
  });

  var ARGS_BUG = !fails$8(function () {
    nativeConstruct(function () {  });
  });

  var FORCED$1 = NEW_TARGET_BUG || ARGS_BUG;

  $$g({ target: 'Reflect', stat: true, forced: FORCED$1, sham: FORCED$1 }, {
    construct: function construct(Target, args ) {
      aConstructor$1(Target);
      anObject$3(args);
      var newTarget = arguments.length < 3 ? Target : aConstructor$1(arguments[2]);
      if (ARGS_BUG && !NEW_TARGET_BUG) return nativeConstruct(Target, args, newTarget);
      if (Target == newTarget) {
        
        switch (args.length) {
          case 0: return new Target();
          case 1: return new Target(args[0]);
          case 2: return new Target(args[0], args[1]);
          case 3: return new Target(args[0], args[1], args[2]);
          case 4: return new Target(args[0], args[1], args[2], args[3]);
        }
        
        var $args = [null];
        apply$1(push$2, $args, args);
        return new (apply$1(bind$9, Target, $args))();
      }
      
      var proto = newTarget.prototype;
      var instance = create$6(isObject$6(proto) ? proto : ObjectPrototype);
      var result = apply$1(Target, instance, args);
      return isObject$6(result) ? result : instance;
    }
  });

  var path$4 = path$r;

  var construct$1 = path$4.Reflect.construct;

  var parent$q = construct$1;

  var construct = parent$q;

  (function (module) {
  	module.exports = construct;
  } (construct$2));

  var _Reflect$construct = getDefaultExportFromCjs(constructExports);

  function _assertThisInitialized(self) {
    if (self === void 0) {
      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
    }
    return self;
  }

  var createExports$1 = {};
  var create$5 = {
    get exports(){ return createExports$1; },
    set exports(v){ createExports$1 = v; },
  };

  var createExports = {};
  var create$4 = {
    get exports(){ return createExports; },
    set exports(v){ createExports = v; },
  };

  var parent$p = create$7;

  var create$3 = parent$p;

  var parent$o = create$3;

  var create$2 = parent$o;

  (function (module) {
  	module.exports = create$2;
  } (create$4));

  (function (module) {
  	module.exports = createExports;
  } (create$5));

  var _Object$create = getDefaultExportFromCjs(createExports$1);

  var setPrototypeOfExports$1 = {};
  var setPrototypeOf$7 = {
    get exports(){ return setPrototypeOfExports$1; },
    set exports(v){ setPrototypeOfExports$1 = v; },
  };

  var setPrototypeOfExports = {};
  var setPrototypeOf$6 = {
    get exports(){ return setPrototypeOfExports; },
    set exports(v){ setPrototypeOfExports = v; },
  };

  var $$f = _export;
  var setPrototypeOf$5 = objectSetPrototypeOf;

  
  
  $$f({ target: 'Object', stat: true }, {
    setPrototypeOf: setPrototypeOf$5
  });

  var path$3 = path$r;

  var setPrototypeOf$4 = path$3.Object.setPrototypeOf;

  var parent$n = setPrototypeOf$4;

  var setPrototypeOf$3 = parent$n;

  var parent$m = setPrototypeOf$3;

  var setPrototypeOf$2 = parent$m;

  var parent$l = setPrototypeOf$2;

  var setPrototypeOf$1 = parent$l;

  (function (module) {
  	module.exports = setPrototypeOf$1;
  } (setPrototypeOf$6));

  (function (module) {
  	module.exports = setPrototypeOfExports;
  } (setPrototypeOf$7));

  var _Object$setPrototypeOf = getDefaultExportFromCjs(setPrototypeOfExports$1);

  var bindExports$1 = {};
  var bind$8 = {
    get exports(){ return bindExports$1; },
    set exports(v){ bindExports$1 = v; },
  };

  var bindExports = {};
  var bind$7 = {
    get exports(){ return bindExports; },
    set exports(v){ bindExports = v; },
  };

  var parent$k = bind$c;

  var bind$6 = parent$k;

  var parent$j = bind$6;

  var bind$5 = parent$j;

  (function (module) {
  	module.exports = bind$5;
  } (bind$7));

  (function (module) {
  	module.exports = bindExports;
  } (bind$8));

  var _bindInstanceProperty = getDefaultExportFromCjs(bindExports$1);

  function _setPrototypeOf(o, p) {
    var _context;
    _setPrototypeOf = _Object$setPrototypeOf ? _bindInstanceProperty(_context = _Object$setPrototypeOf).call(_context) : function _setPrototypeOf(o, p) {
      o.__proto__ = p;
      return o;
    };
    return _setPrototypeOf(o, p);
  }

  function _inherits(subClass, superClass) {
    if (typeof superClass !== "function" && superClass !== null) {
      throw new TypeError("Super expression must either be null or a function");
    }
    subClass.prototype = _Object$create(superClass && superClass.prototype, {
      constructor: {
        value: subClass,
        writable: true,
        configurable: true
      }
    });
    _Object$defineProperty$1(subClass, "prototype", {
      writable: false
    });
    if (superClass) _setPrototypeOf(subClass, superClass);
  }

  function _possibleConstructorReturn(self, call) {
    if (call && (_typeof$1(call) === "object" || typeof call === "function")) {
      return call;
    } else if (call !== void 0) {
      throw new TypeError("Derived constructors may only return object or undefined");
    }
    return _assertThisInitialized(self);
  }

  var getPrototypeOfExports$1 = {};
  var getPrototypeOf$4 = {
    get exports(){ return getPrototypeOfExports$1; },
    set exports(v){ getPrototypeOfExports$1 = v; },
  };

  var getPrototypeOfExports = {};
  var getPrototypeOf$3 = {
    get exports(){ return getPrototypeOfExports; },
    set exports(v){ getPrototypeOfExports = v; },
  };

  var parent$i = getPrototypeOf$5;

  var getPrototypeOf$2 = parent$i;

  var parent$h = getPrototypeOf$2;

  var getPrototypeOf$1 = parent$h;

  (function (module) {
  	module.exports = getPrototypeOf$1;
  } (getPrototypeOf$3));

  (function (module) {
  	module.exports = getPrototypeOfExports;
  } (getPrototypeOf$4));

  var _Object$getPrototypeOf = getDefaultExportFromCjs(getPrototypeOfExports$1);

  function _getPrototypeOf(o) {
    var _context;
    _getPrototypeOf = _Object$setPrototypeOf ? _bindInstanceProperty(_context = _Object$getPrototypeOf).call(_context) : function _getPrototypeOf(o) {
      return o.__proto__ || _Object$getPrototypeOf(o);
    };
    return _getPrototypeOf(o);
  }

  var regeneratorRuntimeExports = {};
  var regeneratorRuntime$1 = {
    get exports(){ return regeneratorRuntimeExports; },
    set exports(v){ regeneratorRuntimeExports = v; },
  };

  var _typeofExports = {};
  var _typeof = {
    get exports(){ return _typeofExports; },
    set exports(v){ _typeofExports = v; },
  };

  (function (module) {
  	var _Symbol = symbolExports$2;
  	var _Symbol$iterator = iteratorExports$2;
  	function _typeof(obj) {
  	  "@babel/helpers - typeof";

  	  return (module.exports = _typeof = "function" == typeof _Symbol && "symbol" == typeof _Symbol$iterator ? function (obj) {
  	    return typeof obj;
  	  } : function (obj) {
  	    return obj && "function" == typeof _Symbol && obj.constructor === _Symbol && obj !== _Symbol.prototype ? "symbol" : typeof obj;
  	  }, module.exports.__esModule = true, module.exports["default"] = module.exports), _typeof(obj);
  	}
  	module.exports = _typeof, module.exports.__esModule = true, module.exports["default"] = module.exports;
  } (_typeof));

  var forEachExports$1 = {};
  var forEach$4 = {
    get exports(){ return forEachExports$1; },
    set exports(v){ forEachExports$1 = v; },
  };

  var forEachExports = {};
  var forEach$3 = {
    get exports(){ return forEachExports; },
    set exports(v){ forEachExports = v; },
  };

  var parent$g = forEach$5;

  var forEach$2 = parent$g;

  var parent$f = forEach$2;

  var forEach$1 = parent$f;

  (function (module) {
  	module.exports = forEach$1;
  } (forEach$3));

  (function (module) {
  	module.exports = forEachExports;
  } (forEach$4));

  var promiseExports$1 = {};
  var promise$6 = {
    get exports(){ return promiseExports$1; },
    set exports(v){ promiseExports$1 = v; },
  };

  var promiseExports = {};
  var promise$5 = {
    get exports(){ return promiseExports; },
    set exports(v){ promiseExports = v; },
  };

  var hasOwn$5 = hasOwnProperty_1;
  var ownKeys$1 = ownKeys$7;
  var getOwnPropertyDescriptorModule = objectGetOwnPropertyDescriptor;
  var definePropertyModule = objectDefineProperty;

  var copyConstructorProperties$1 = function (target, source, exceptions) {
    var keys = ownKeys$1(source);
    var defineProperty = definePropertyModule.f;
    var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
    for (var i = 0; i < keys.length; i++) {
      var key = keys[i];
      if (!hasOwn$5(target, key) && !(exceptions && hasOwn$5(exceptions, key))) {
        defineProperty(target, key, getOwnPropertyDescriptor(source, key));
      }
    }
  };

  var isObject$5 = isObject$i;
  var createNonEnumerableProperty$3 = createNonEnumerableProperty$9;

  
  
  var installErrorCause$1 = function (O, options) {
    if (isObject$5(options) && 'cause' in options) {
      createNonEnumerableProperty$3(O, 'cause', options.cause);
    }
  };

  var uncurryThis$2 = functionUncurryThis;

  var $Error$1 = Error;
  var replace = uncurryThis$2(''.replace);

  var TEST = (function (arg) { return String($Error$1(arg).stack); })('zxcasd');
  
  var V8_OR_CHAKRA_STACK_ENTRY = /\n\s*at [^:]*:[^\n]*/;
  var IS_V8_OR_CHAKRA_STACK = V8_OR_CHAKRA_STACK_ENTRY.test(TEST);

  var errorStackClear = function (stack, dropEntries) {
    if (IS_V8_OR_CHAKRA_STACK && typeof stack == 'string' && !$Error$1.prepareStackTrace) {
      while (dropEntries--) stack = replace(stack, V8_OR_CHAKRA_STACK_ENTRY, '');
    } return stack;
  };

  var fails$7 = fails$w;
  var createPropertyDescriptor$1 = createPropertyDescriptor$7;

  var errorStackInstallable = !fails$7(function () {
    var error = Error('a');
    if (!('stack' in error)) return true;
    
    Object.defineProperty(error, 'stack', createPropertyDescriptor$1(1, 7));
    return error.stack !== 7;
  });

  var createNonEnumerableProperty$2 = createNonEnumerableProperty$9;
  var clearErrorStack = errorStackClear;
  var ERROR_STACK_INSTALLABLE = errorStackInstallable;

  
  var captureStackTrace = Error.captureStackTrace;

  var errorStackInstall = function (error, C, stack, dropEntries) {
    if (ERROR_STACK_INSTALLABLE) {
      if (captureStackTrace) captureStackTrace(error, C);
      else createNonEnumerableProperty$2(error, 'stack', clearErrorStack(stack, dropEntries));
    }
  };

  var bind$4 = functionBindContext;
  var call$6 = functionCall;
  var anObject$2 = anObject$d;
  var tryToString = tryToString$6;
  var isArrayIteratorMethod = isArrayIteratorMethod$2;
  var lengthOfArrayLike$1 = lengthOfArrayLike$d;
  var isPrototypeOf$7 = objectIsPrototypeOf;
  var getIterator$6 = getIterator$8;
  var getIteratorMethod = getIteratorMethod$9;
  var iteratorClose = iteratorClose$2;

  var $TypeError$2 = TypeError;

  var Result = function (stopped, result) {
    this.stopped = stopped;
    this.result = result;
  };

  var ResultPrototype = Result.prototype;

  var iterate$7 = function (iterable, unboundFunction, options) {
    var that = options && options.that;
    var AS_ENTRIES = !!(options && options.AS_ENTRIES);
    var IS_RECORD = !!(options && options.IS_RECORD);
    var IS_ITERATOR = !!(options && options.IS_ITERATOR);
    var INTERRUPTED = !!(options && options.INTERRUPTED);
    var fn = bind$4(unboundFunction, that);
    var iterator, iterFn, index, length, result, next, step;

    var stop = function (condition) {
      if (iterator) iteratorClose(iterator, 'normal', condition);
      return new Result(true, condition);
    };

    var callFn = function (value) {
      if (AS_ENTRIES) {
        anObject$2(value);
        return INTERRUPTED ? fn(value[0], value[1], stop) : fn(value[0], value[1]);
      } return INTERRUPTED ? fn(value, stop) : fn(value);
    };

    if (IS_RECORD) {
      iterator = iterable.iterator;
    } else if (IS_ITERATOR) {
      iterator = iterable;
    } else {
      iterFn = getIteratorMethod(iterable);
      if (!iterFn) throw $TypeError$2(tryToString(iterable) + ' is not iterable');
      
      if (isArrayIteratorMethod(iterFn)) {
        for (index = 0, length = lengthOfArrayLike$1(iterable); length > index; index++) {
          result = callFn(iterable[index]);
          if (result && isPrototypeOf$7(ResultPrototype, result)) return result;
        } return new Result(false);
      }
      iterator = getIterator$6(iterable, iterFn);
    }

    next = IS_RECORD ? iterable.next : iterator.next;
    while (!(step = call$6(next, iterator)).done) {
      try {
        result = callFn(step.value);
      } catch (error) {
        iteratorClose(iterator, 'throw', error);
      }
      if (typeof result == 'object' && result && isPrototypeOf$7(ResultPrototype, result)) return result;
    } return new Result(false);
  };

  var toString$1 = toString$a;

  var normalizeStringArgument$1 = function (argument, $default) {
    return argument === undefined ? arguments.length < 2 ? '' : $default : toString$1(argument);
  };

  var $$e = _export;
  var isPrototypeOf$6 = objectIsPrototypeOf;
  var getPrototypeOf = objectGetPrototypeOf;
  var setPrototypeOf = objectSetPrototypeOf;
  var copyConstructorProperties = copyConstructorProperties$1;
  var create$1 = objectCreate;
  var createNonEnumerableProperty$1 = createNonEnumerableProperty$9;
  var createPropertyDescriptor = createPropertyDescriptor$7;
  var installErrorCause = installErrorCause$1;
  var installErrorStack = errorStackInstall;
  var iterate$6 = iterate$7;
  var normalizeStringArgument = normalizeStringArgument$1;
  var wellKnownSymbol$3 = wellKnownSymbol$o;

  var TO_STRING_TAG = wellKnownSymbol$3('toStringTag');
  var $Error = Error;
  var push$1 = [].push;

  var $AggregateError = function AggregateError(errors, message ) {
    var isInstance = isPrototypeOf$6(AggregateErrorPrototype, this);
    var that;
    if (setPrototypeOf) {
      that = setPrototypeOf($Error(), isInstance ? getPrototypeOf(this) : AggregateErrorPrototype);
    } else {
      that = isInstance ? this : create$1(AggregateErrorPrototype);
      createNonEnumerableProperty$1(that, TO_STRING_TAG, 'Error');
    }
    if (message !== undefined) createNonEnumerableProperty$1(that, 'message', normalizeStringArgument(message));
    installErrorStack(that, $AggregateError, that.stack, 1);
    if (arguments.length > 2) installErrorCause(that, arguments[2]);
    var errorsArray = [];
    iterate$6(errors, push$1, { that: errorsArray });
    createNonEnumerableProperty$1(that, 'errors', errorsArray);
    return that;
  };

  if (setPrototypeOf) setPrototypeOf($AggregateError, $Error);
  else copyConstructorProperties($AggregateError, $Error, { name: true });

  var AggregateErrorPrototype = $AggregateError.prototype = create$1($Error.prototype, {
    constructor: createPropertyDescriptor(1, $AggregateError),
    message: createPropertyDescriptor(1, ''),
    name: createPropertyDescriptor(1, 'AggregateError')
  });

  
  
  $$e({ global: true, constructor: true, arity: 2 }, {
    AggregateError: $AggregateError
  });

  var getBuiltIn$3 = getBuiltIn$f;
  var defineBuiltInAccessor$1 = defineBuiltInAccessor$3;
  var wellKnownSymbol$2 = wellKnownSymbol$o;
  var DESCRIPTORS$2 = descriptors;

  var SPECIES$2 = wellKnownSymbol$2('species');

  var setSpecies$2 = function (CONSTRUCTOR_NAME) {
    var Constructor = getBuiltIn$3(CONSTRUCTOR_NAME);

    if (DESCRIPTORS$2 && Constructor && !Constructor[SPECIES$2]) {
      defineBuiltInAccessor$1(Constructor, SPECIES$2, {
        configurable: true,
        get: function () { return this; }
      });
    }
  };

  var isPrototypeOf$5 = objectIsPrototypeOf;

  var $TypeError$1 = TypeError;

  var anInstance$3 = function (it, Prototype) {
    if (isPrototypeOf$5(Prototype, it)) return it;
    throw $TypeError$1('Incorrect invocation');
  };

  var anObject$1 = anObject$d;
  var aConstructor = aConstructor$2;
  var isNullOrUndefined$1 = isNullOrUndefined$5;
  var wellKnownSymbol$1 = wellKnownSymbol$o;

  var SPECIES$1 = wellKnownSymbol$1('species');

  
  
  var speciesConstructor$2 = function (O, defaultConstructor) {
    var C = anObject$1(O).constructor;
    var S;
    return C === undefined || isNullOrUndefined$1(S = anObject$1(C)[SPECIES$1]) ? defaultConstructor : aConstructor(S);
  };

  var userAgent$4 = engineUserAgent;

  
  var engineIsIos = /(?:ipad|iphone|ipod).*applewebkit/i.test(userAgent$4);

  var global$6 = global$n;
  var apply = functionApply;
  var bind$3 = functionBindContext;
  var isCallable$4 = isCallable$m;
  var hasOwn$4 = hasOwnProperty_1;
  var fails$6 = fails$w;
  var html = html$2;
  var arraySlice$1 = arraySlice$5;
  var createElement = documentCreateElement$1;
  var validateArgumentsLength = validateArgumentsLength$2;
  var IS_IOS$1 = engineIsIos;
  var IS_NODE$3 = engineIsNode;

  var set$3 = global$6.setImmediate;
  var clear = global$6.clearImmediate;
  var process$3 = global$6.process;
  var Dispatch = global$6.Dispatch;
  var Function$1 = global$6.Function;
  var MessageChannel = global$6.MessageChannel;
  var String$1 = global$6.String;
  var counter = 0;
  var queue$2 = {};
  var ONREADYSTATECHANGE = 'onreadystatechange';
  var $location, defer, channel, port;

  fails$6(function () {
    
    $location = global$6.location;
  });

  var run = function (id) {
    if (hasOwn$4(queue$2, id)) {
      var fn = queue$2[id];
      delete queue$2[id];
      fn();
    }
  };

  var runner = function (id) {
    return function () {
      run(id);
    };
  };

  var eventListener = function (event) {
    run(event.data);
  };

  var globalPostMessageDefer = function (id) {
    
    global$6.postMessage(String$1(id), $location.protocol + '
  };

  
  if (!set$3 || !clear) {
    set$3 = function setImmediate(handler) {
      validateArgumentsLength(arguments.length, 1);
      var fn = isCallable$4(handler) ? handler : Function$1(handler);
      var args = arraySlice$1(arguments, 1);
      queue$2[++counter] = function () {
        apply(fn, undefined, args);
      };
      defer(counter);
      return counter;
    };
    clear = function clearImmediate(id) {
      delete queue$2[id];
    };
    
    if (IS_NODE$3) {
      defer = function (id) {
        process$3.nextTick(runner(id));
      };
    
    } else if (Dispatch && Dispatch.now) {
      defer = function (id) {
        Dispatch.now(runner(id));
      };
    
    
    } else if (MessageChannel && !IS_IOS$1) {
      channel = new MessageChannel();
      port = channel.port2;
      channel.port1.onmessage = eventListener;
      defer = bind$3(port.postMessage, port);
    
    
    } else if (
      global$6.addEventListener &&
      isCallable$4(global$6.postMessage) &&
      !global$6.importScripts &&
      $location && $location.protocol !== 'file:' &&
      !fails$6(globalPostMessageDefer)
    ) {
      defer = globalPostMessageDefer;
      global$6.addEventListener('message', eventListener, false);
    
    } else if (ONREADYSTATECHANGE in createElement('script')) {
      defer = function (id) {
        html.appendChild(createElement('script'))[ONREADYSTATECHANGE] = function () {
          html.removeChild(this);
          run(id);
        };
      };
    
    } else {
      defer = function (id) {
        setTimeout(runner(id), 0);
      };
    }
  }

  var task$1 = {
    set: set$3,
    clear: clear
  };

  var Queue$3 = function () {
    this.head = null;
    this.tail = null;
  };

  Queue$3.prototype = {
    add: function (item) {
      var entry = { item: item, next: null };
      var tail = this.tail;
      if (tail) tail.next = entry;
      else this.head = entry;
      this.tail = entry;
    },
    get: function () {
      var entry = this.head;
      if (entry) {
        var next = this.head = entry.next;
        if (next === null) this.tail = null;
        return entry.item;
      }
    }
  };

  var queue$1 = Queue$3;

  var userAgent$3 = engineUserAgent;

  var engineIsIosPebble = /ipad|iphone|ipod/i.test(userAgent$3) && typeof Pebble != 'undefined';

  var userAgent$2 = engineUserAgent;

  var engineIsWebosWebkit = /web0s(?!.*chrome)/i.test(userAgent$2);

  var global$5 = global$n;
  var bind$2 = functionBindContext;
  var getOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
  var macrotask = task$1.set;
  var Queue$2 = queue$1;
  var IS_IOS = engineIsIos;
  var IS_IOS_PEBBLE = engineIsIosPebble;
  var IS_WEBOS_WEBKIT = engineIsWebosWebkit;
  var IS_NODE$2 = engineIsNode;

  var MutationObserver = global$5.MutationObserver || global$5.WebKitMutationObserver;
  var document$2 = global$5.document;
  var process$2 = global$5.process;
  var Promise$1 = global$5.Promise;
  
  var queueMicrotaskDescriptor = getOwnPropertyDescriptor(global$5, 'queueMicrotask');
  var microtask$1 = queueMicrotaskDescriptor && queueMicrotaskDescriptor.value;
  var notify$1, toggle, node, promise$4, then;

  
  if (!microtask$1) {
    var queue = new Queue$2();

    var flush = function () {
      var parent, fn;
      if (IS_NODE$2 && (parent = process$2.domain)) parent.exit();
      while (fn = queue.get()) try {
        fn();
      } catch (error) {
        if (queue.head) notify$1();
        throw error;
      }
      if (parent) parent.enter();
    };

    
    
    if (!IS_IOS && !IS_NODE$2 && !IS_WEBOS_WEBKIT && MutationObserver && document$2) {
      toggle = true;
      node = document$2.createTextNode('');
      new MutationObserver(flush).observe(node, { characterData: true });
      notify$1 = function () {
        node.data = toggle = !toggle;
      };
    
    } else if (!IS_IOS_PEBBLE && Promise$1 && Promise$1.resolve) {
      
      promise$4 = Promise$1.resolve(undefined);
      
      promise$4.constructor = Promise$1;
      then = bind$2(promise$4.then, promise$4);
      notify$1 = function () {
        then(flush);
      };
    
    } else if (IS_NODE$2) {
      notify$1 = function () {
        process$2.nextTick(flush);
      };
    
    
    
    
    
    
    } else {
      
      macrotask = bind$2(macrotask, global$5);
      notify$1 = function () {
        macrotask(flush);
      };
    }

    microtask$1 = function (fn) {
      if (!queue.head) notify$1();
      queue.add(fn);
    };
  }

  var microtask_1 = microtask$1;

  var hostReportErrors$1 = function (a, b) {
    try {
      
      arguments.length == 1 ? console.error(a) : console.error(a, b);
    } catch (error) {  }
  };

  var perform$6 = function (exec) {
    try {
      return { error: false, value: exec() };
    } catch (error) {
      return { error: true, value: error };
    }
  };

  var global$4 = global$n;

  var promiseNativeConstructor = global$4.Promise;

  

  var engineIsDeno = typeof Deno == 'object' && Deno && typeof Deno.version == 'object';

  var IS_DENO$1 = engineIsDeno;
  var IS_NODE$1 = engineIsNode;

  var engineIsBrowser = !IS_DENO$1 && !IS_NODE$1
    && typeof window == 'object'
    && typeof document == 'object';

  var global$3 = global$n;
  var NativePromiseConstructor$5 = promiseNativeConstructor;
  var isCallable$3 = isCallable$m;
  var isForced = isForced_1;
  var inspectSource = inspectSource$2;
  var wellKnownSymbol = wellKnownSymbol$o;
  var IS_BROWSER = engineIsBrowser;
  var IS_DENO = engineIsDeno;
  var V8_VERSION = engineV8Version;

  var NativePromisePrototype$2 = NativePromiseConstructor$5 && NativePromiseConstructor$5.prototype;
  var SPECIES = wellKnownSymbol('species');
  var SUBCLASSING = false;
  var NATIVE_PROMISE_REJECTION_EVENT$1 = isCallable$3(global$3.PromiseRejectionEvent);

  var FORCED_PROMISE_CONSTRUCTOR$5 = isForced('Promise', function () {
    var PROMISE_CONSTRUCTOR_SOURCE = inspectSource(NativePromiseConstructor$5);
    var GLOBAL_CORE_JS_PROMISE = PROMISE_CONSTRUCTOR_SOURCE !== String(NativePromiseConstructor$5);
    
    
    
    if (!GLOBAL_CORE_JS_PROMISE && V8_VERSION === 66) return true;
    
    if (!(NativePromisePrototype$2['catch'] && NativePromisePrototype$2['finally'])) return true;
    
    
    
    if (!V8_VERSION || V8_VERSION < 51 || !/native code/.test(PROMISE_CONSTRUCTOR_SOURCE)) {
      
      var promise = new NativePromiseConstructor$5(function (resolve) { resolve(1); });
      var FakePromise = function (exec) {
        exec(function () {  }, function () {  });
      };
      var constructor = promise.constructor = {};
      constructor[SPECIES] = FakePromise;
      SUBCLASSING = promise.then(function () {  }) instanceof FakePromise;
      if (!SUBCLASSING) return true;
    
    } return !GLOBAL_CORE_JS_PROMISE && (IS_BROWSER || IS_DENO) && !NATIVE_PROMISE_REJECTION_EVENT$1;
  });

  var promiseConstructorDetection = {
    CONSTRUCTOR: FORCED_PROMISE_CONSTRUCTOR$5,
    REJECTION_EVENT: NATIVE_PROMISE_REJECTION_EVENT$1,
    SUBCLASSING: SUBCLASSING
  };

  var newPromiseCapability$2 = {};

  var aCallable$6 = aCallable$e;

  var $TypeError = TypeError;

  var PromiseCapability = function (C) {
    var resolve, reject;
    this.promise = new C(function ($$resolve, $$reject) {
      if (resolve !== undefined || reject !== undefined) throw $TypeError('Bad Promise constructor');
      resolve = $$resolve;
      reject = $$reject;
    });
    this.resolve = aCallable$6(resolve);
    this.reject = aCallable$6(reject);
  };

  
  
  newPromiseCapability$2.f = function (C) {
    return new PromiseCapability(C);
  };

  var $$d = _export;
  var IS_NODE = engineIsNode;
  var global$2 = global$n;
  var call$5 = functionCall;
  var defineBuiltIn$1 = defineBuiltIn$6;
  var setToStringTag$1 = setToStringTag$7;
  var setSpecies$1 = setSpecies$2;
  var aCallable$5 = aCallable$e;
  var isCallable$2 = isCallable$m;
  var isObject$4 = isObject$i;
  var anInstance$2 = anInstance$3;
  var speciesConstructor$1 = speciesConstructor$2;
  var task = task$1.set;
  var microtask = microtask_1;
  var hostReportErrors = hostReportErrors$1;
  var perform$5 = perform$6;
  var Queue$1 = queue$1;
  var InternalStateModule$2 = internalState;
  var NativePromiseConstructor$4 = promiseNativeConstructor;
  var PromiseConstructorDetection = promiseConstructorDetection;
  var newPromiseCapabilityModule$6 = newPromiseCapability$2;

  var PROMISE = 'Promise';
  var FORCED_PROMISE_CONSTRUCTOR$4 = PromiseConstructorDetection.CONSTRUCTOR;
  var NATIVE_PROMISE_REJECTION_EVENT = PromiseConstructorDetection.REJECTION_EVENT;
  var getInternalPromiseState = InternalStateModule$2.getterFor(PROMISE);
  var setInternalState$2 = InternalStateModule$2.set;
  var NativePromisePrototype$1 = NativePromiseConstructor$4 && NativePromiseConstructor$4.prototype;
  var PromiseConstructor = NativePromiseConstructor$4;
  var PromisePrototype = NativePromisePrototype$1;
  var TypeError$1 = global$2.TypeError;
  var document$1 = global$2.document;
  var process$1 = global$2.process;
  var newPromiseCapability$1 = newPromiseCapabilityModule$6.f;
  var newGenericPromiseCapability = newPromiseCapability$1;

  var DISPATCH_EVENT = !!(document$1 && document$1.createEvent && global$2.dispatchEvent);
  var UNHANDLED_REJECTION = 'unhandledrejection';
  var REJECTION_HANDLED = 'rejectionhandled';
  var PENDING = 0;
  var FULFILLED = 1;
  var REJECTED = 2;
  var HANDLED = 1;
  var UNHANDLED = 2;

  var Internal, OwnPromiseCapability, PromiseWrapper;

  
  var isThenable = function (it) {
    var then;
    return isObject$4(it) && isCallable$2(then = it.then) ? then : false;
  };

  var callReaction = function (reaction, state) {
    var value = state.value;
    var ok = state.state == FULFILLED;
    var handler = ok ? reaction.ok : reaction.fail;
    var resolve = reaction.resolve;
    var reject = reaction.reject;
    var domain = reaction.domain;
    var result, then, exited;
    try {
      if (handler) {
        if (!ok) {
          if (state.rejection === UNHANDLED) onHandleUnhandled(state);
          state.rejection = HANDLED;
        }
        if (handler === true) result = value;
        else {
          if (domain) domain.enter();
          result = handler(value); 
          if (domain) {
            domain.exit();
            exited = true;
          }
        }
        if (result === reaction.promise) {
          reject(TypeError$1('Promise-chain cycle'));
        } else if (then = isThenable(result)) {
          call$5(then, result, resolve, reject);
        } else resolve(result);
      } else reject(value);
    } catch (error) {
      if (domain && !exited) domain.exit();
      reject(error);
    }
  };

  var notify = function (state, isReject) {
    if (state.notified) return;
    state.notified = true;
    microtask(function () {
      var reactions = state.reactions;
      var reaction;
      while (reaction = reactions.get()) {
        callReaction(reaction, state);
      }
      state.notified = false;
      if (isReject && !state.rejection) onUnhandled(state);
    });
  };

  var dispatchEvent = function (name, promise, reason) {
    var event, handler;
    if (DISPATCH_EVENT) {
      event = document$1.createEvent('Event');
      event.promise = promise;
      event.reason = reason;
      event.initEvent(name, false, true);
      global$2.dispatchEvent(event);
    } else event = { promise: promise, reason: reason };
    if (!NATIVE_PROMISE_REJECTION_EVENT && (handler = global$2['on' + name])) handler(event);
    else if (name === UNHANDLED_REJECTION) hostReportErrors('Unhandled promise rejection', reason);
  };

  var onUnhandled = function (state) {
    call$5(task, global$2, function () {
      var promise = state.facade;
      var value = state.value;
      var IS_UNHANDLED = isUnhandled(state);
      var result;
      if (IS_UNHANDLED) {
        result = perform$5(function () {
          if (IS_NODE) {
            process$1.emit('unhandledRejection', value, promise);
          } else dispatchEvent(UNHANDLED_REJECTION, promise, value);
        });
        
        state.rejection = IS_NODE || isUnhandled(state) ? UNHANDLED : HANDLED;
        if (result.error) throw result.value;
      }
    });
  };

  var isUnhandled = function (state) {
    return state.rejection !== HANDLED && !state.parent;
  };

  var onHandleUnhandled = function (state) {
    call$5(task, global$2, function () {
      var promise = state.facade;
      if (IS_NODE) {
        process$1.emit('rejectionHandled', promise);
      } else dispatchEvent(REJECTION_HANDLED, promise, state.value);
    });
  };

  var bind$1 = function (fn, state, unwrap) {
    return function (value) {
      fn(state, value, unwrap);
    };
  };

  var internalReject = function (state, value, unwrap) {
    if (state.done) return;
    state.done = true;
    if (unwrap) state = unwrap;
    state.value = value;
    state.state = REJECTED;
    notify(state, true);
  };

  var internalResolve = function (state, value, unwrap) {
    if (state.done) return;
    state.done = true;
    if (unwrap) state = unwrap;
    try {
      if (state.facade === value) throw TypeError$1("Promise can't be resolved itself");
      var then = isThenable(value);
      if (then) {
        microtask(function () {
          var wrapper = { done: false };
          try {
            call$5(then, value,
              bind$1(internalResolve, wrapper, state),
              bind$1(internalReject, wrapper, state)
            );
          } catch (error) {
            internalReject(wrapper, error, state);
          }
        });
      } else {
        state.value = value;
        state.state = FULFILLED;
        notify(state, false);
      }
    } catch (error) {
      internalReject({ done: false }, error, state);
    }
  };

  
  if (FORCED_PROMISE_CONSTRUCTOR$4) {
    
    PromiseConstructor = function Promise(executor) {
      anInstance$2(this, PromisePrototype);
      aCallable$5(executor);
      call$5(Internal, this);
      var state = getInternalPromiseState(this);
      try {
        executor(bind$1(internalResolve, state), bind$1(internalReject, state));
      } catch (error) {
        internalReject(state, error);
      }
    };

    PromisePrototype = PromiseConstructor.prototype;

    
    Internal = function Promise(executor) {
      setInternalState$2(this, {
        type: PROMISE,
        done: false,
        notified: false,
        parent: false,
        reactions: new Queue$1(),
        rejection: false,
        state: PENDING,
        value: undefined
      });
    };

    
    
    Internal.prototype = defineBuiltIn$1(PromisePrototype, 'then', function then(onFulfilled, onRejected) {
      var state = getInternalPromiseState(this);
      var reaction = newPromiseCapability$1(speciesConstructor$1(this, PromiseConstructor));
      state.parent = true;
      reaction.ok = isCallable$2(onFulfilled) ? onFulfilled : true;
      reaction.fail = isCallable$2(onRejected) && onRejected;
      reaction.domain = IS_NODE ? process$1.domain : undefined;
      if (state.state == PENDING) state.reactions.add(reaction);
      else microtask(function () {
        callReaction(reaction, state);
      });
      return reaction.promise;
    });

    OwnPromiseCapability = function () {
      var promise = new Internal();
      var state = getInternalPromiseState(promise);
      this.promise = promise;
      this.resolve = bind$1(internalResolve, state);
      this.reject = bind$1(internalReject, state);
    };

    newPromiseCapabilityModule$6.f = newPromiseCapability$1 = function (C) {
      return C === PromiseConstructor || C === PromiseWrapper
        ? new OwnPromiseCapability(C)
        : newGenericPromiseCapability(C);
    };
  }

  $$d({ global: true, constructor: true, wrap: true, forced: FORCED_PROMISE_CONSTRUCTOR$4 }, {
    Promise: PromiseConstructor
  });

  setToStringTag$1(PromiseConstructor, PROMISE, false, true);
  setSpecies$1(PROMISE);

  var NativePromiseConstructor$3 = promiseNativeConstructor;
  var checkCorrectnessOfIteration = checkCorrectnessOfIteration$2;
  var FORCED_PROMISE_CONSTRUCTOR$3 = promiseConstructorDetection.CONSTRUCTOR;

  var promiseStaticsIncorrectIteration = FORCED_PROMISE_CONSTRUCTOR$3 || !checkCorrectnessOfIteration(function (iterable) {
    NativePromiseConstructor$3.all(iterable).then(undefined, function () {  });
  });

  var $$c = _export;
  var call$4 = functionCall;
  var aCallable$4 = aCallable$e;
  var newPromiseCapabilityModule$5 = newPromiseCapability$2;
  var perform$4 = perform$6;
  var iterate$5 = iterate$7;
  var PROMISE_STATICS_INCORRECT_ITERATION$3 = promiseStaticsIncorrectIteration;

  
  
  $$c({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION$3 }, {
    all: function all(iterable) {
      var C = this;
      var capability = newPromiseCapabilityModule$5.f(C);
      var resolve = capability.resolve;
      var reject = capability.reject;
      var result = perform$4(function () {
        var $promiseResolve = aCallable$4(C.resolve);
        var values = [];
        var counter = 0;
        var remaining = 1;
        iterate$5(iterable, function (promise) {
          var index = counter++;
          var alreadyCalled = false;
          remaining++;
          call$4($promiseResolve, C, promise).then(function (value) {
            if (alreadyCalled) return;
            alreadyCalled = true;
            values[index] = value;
            --remaining || resolve(values);
          }, reject);
        });
        --remaining || resolve(values);
      });
      if (result.error) reject(result.value);
      return capability.promise;
    }
  });

  var $$b = _export;
  var FORCED_PROMISE_CONSTRUCTOR$2 = promiseConstructorDetection.CONSTRUCTOR;
  var NativePromiseConstructor$2 = promiseNativeConstructor;

  NativePromiseConstructor$2 && NativePromiseConstructor$2.prototype;

  
  
  $$b({ target: 'Promise', proto: true, forced: FORCED_PROMISE_CONSTRUCTOR$2, real: true }, {
    'catch': function (onRejected) {
      return this.then(undefined, onRejected);
    }
  });

  var $$a = _export;
  var call$3 = functionCall;
  var aCallable$3 = aCallable$e;
  var newPromiseCapabilityModule$4 = newPromiseCapability$2;
  var perform$3 = perform$6;
  var iterate$4 = iterate$7;
  var PROMISE_STATICS_INCORRECT_ITERATION$2 = promiseStaticsIncorrectIteration;

  
  
  $$a({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION$2 }, {
    race: function race(iterable) {
      var C = this;
      var capability = newPromiseCapabilityModule$4.f(C);
      var reject = capability.reject;
      var result = perform$3(function () {
        var $promiseResolve = aCallable$3(C.resolve);
        iterate$4(iterable, function (promise) {
          call$3($promiseResolve, C, promise).then(capability.resolve, reject);
        });
      });
      if (result.error) reject(result.value);
      return capability.promise;
    }
  });

  var $$9 = _export;
  var call$2 = functionCall;
  var newPromiseCapabilityModule$3 = newPromiseCapability$2;
  var FORCED_PROMISE_CONSTRUCTOR$1 = promiseConstructorDetection.CONSTRUCTOR;

  
  
  $$9({ target: 'Promise', stat: true, forced: FORCED_PROMISE_CONSTRUCTOR$1 }, {
    reject: function reject(r) {
      var capability = newPromiseCapabilityModule$3.f(this);
      call$2(capability.reject, undefined, r);
      return capability.promise;
    }
  });

  var anObject = anObject$d;
  var isObject$3 = isObject$i;
  var newPromiseCapability = newPromiseCapability$2;

  var promiseResolve$2 = function (C, x) {
    anObject(C);
    if (isObject$3(x) && x.constructor === C) return x;
    var promiseCapability = newPromiseCapability.f(C);
    var resolve = promiseCapability.resolve;
    resolve(x);
    return promiseCapability.promise;
  };

  var $$8 = _export;
  var getBuiltIn$2 = getBuiltIn$f;
  var IS_PURE = isPure;
  var NativePromiseConstructor$1 = promiseNativeConstructor;
  var FORCED_PROMISE_CONSTRUCTOR = promiseConstructorDetection.CONSTRUCTOR;
  var promiseResolve$1 = promiseResolve$2;

  var PromiseConstructorWrapper = getBuiltIn$2('Promise');
  var CHECK_WRAPPER = !FORCED_PROMISE_CONSTRUCTOR;

  
  
  $$8({ target: 'Promise', stat: true, forced: IS_PURE  }, {
    resolve: function resolve(x) {
      return promiseResolve$1(CHECK_WRAPPER && this === PromiseConstructorWrapper ? NativePromiseConstructor$1 : this, x);
    }
  });

  var $$7 = _export;
  var call$1 = functionCall;
  var aCallable$2 = aCallable$e;
  var newPromiseCapabilityModule$2 = newPromiseCapability$2;
  var perform$2 = perform$6;
  var iterate$3 = iterate$7;
  var PROMISE_STATICS_INCORRECT_ITERATION$1 = promiseStaticsIncorrectIteration;

  
  
  $$7({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION$1 }, {
    allSettled: function allSettled(iterable) {
      var C = this;
      var capability = newPromiseCapabilityModule$2.f(C);
      var resolve = capability.resolve;
      var reject = capability.reject;
      var result = perform$2(function () {
        var promiseResolve = aCallable$2(C.resolve);
        var values = [];
        var counter = 0;
        var remaining = 1;
        iterate$3(iterable, function (promise) {
          var index = counter++;
          var alreadyCalled = false;
          remaining++;
          call$1(promiseResolve, C, promise).then(function (value) {
            if (alreadyCalled) return;
            alreadyCalled = true;
            values[index] = { status: 'fulfilled', value: value };
            --remaining || resolve(values);
          }, function (error) {
            if (alreadyCalled) return;
            alreadyCalled = true;
            values[index] = { status: 'rejected', reason: error };
            --remaining || resolve(values);
          });
        });
        --remaining || resolve(values);
      });
      if (result.error) reject(result.value);
      return capability.promise;
    }
  });

  var $$6 = _export;
  var call = functionCall;
  var aCallable$1 = aCallable$e;
  var getBuiltIn$1 = getBuiltIn$f;
  var newPromiseCapabilityModule$1 = newPromiseCapability$2;
  var perform$1 = perform$6;
  var iterate$2 = iterate$7;
  var PROMISE_STATICS_INCORRECT_ITERATION = promiseStaticsIncorrectIteration;

  var PROMISE_ANY_ERROR = 'No one promise resolved';

  
  
  $$6({ target: 'Promise', stat: true, forced: PROMISE_STATICS_INCORRECT_ITERATION }, {
    any: function any(iterable) {
      var C = this;
      var AggregateError = getBuiltIn$1('AggregateError');
      var capability = newPromiseCapabilityModule$1.f(C);
      var resolve = capability.resolve;
      var reject = capability.reject;
      var result = perform$1(function () {
        var promiseResolve = aCallable$1(C.resolve);
        var errors = [];
        var counter = 0;
        var remaining = 1;
        var alreadyResolved = false;
        iterate$2(iterable, function (promise) {
          var index = counter++;
          var alreadyRejected = false;
          remaining++;
          call(promiseResolve, C, promise).then(function (value) {
            if (alreadyRejected || alreadyResolved) return;
            alreadyResolved = true;
            resolve(value);
          }, function (error) {
            if (alreadyRejected || alreadyResolved) return;
            alreadyRejected = true;
            errors[index] = error;
            --remaining || reject(new AggregateError(errors, PROMISE_ANY_ERROR));
          });
        });
        --remaining || reject(new AggregateError(errors, PROMISE_ANY_ERROR));
      });
      if (result.error) reject(result.value);
      return capability.promise;
    }
  });

  var $$5 = _export;
  var NativePromiseConstructor = promiseNativeConstructor;
  var fails$5 = fails$w;
  var getBuiltIn = getBuiltIn$f;
  var isCallable$1 = isCallable$m;
  var speciesConstructor = speciesConstructor$2;
  var promiseResolve = promiseResolve$2;

  var NativePromisePrototype = NativePromiseConstructor && NativePromiseConstructor.prototype;

  
  var NON_GENERIC = !!NativePromiseConstructor && fails$5(function () {
    
    NativePromisePrototype['finally'].call({ then: function () {  } }, function () {  });
  });

  
  
  $$5({ target: 'Promise', proto: true, real: true, forced: NON_GENERIC }, {
    'finally': function (onFinally) {
      var C = speciesConstructor(this, getBuiltIn('Promise'));
      var isFunction = isCallable$1(onFinally);
      return this.then(
        isFunction ? function (x) {
          return promiseResolve(C, onFinally()).then(function () { return x; });
        } : onFinally,
        isFunction ? function (e) {
          return promiseResolve(C, onFinally()).then(function () { throw e; });
        } : onFinally
      );
    }
  });

  var path$2 = path$r;

  var promise$3 = path$2.Promise;

  var parent$e = promise$3;


  var promise$2 = parent$e;

  var parent$d = promise$2;

  var promise$1 = parent$d;

  
  var $$4 = _export;
  var newPromiseCapabilityModule = newPromiseCapability$2;
  var perform = perform$6;

  
  
  $$4({ target: 'Promise', stat: true, forced: true }, {
    'try': function (callbackfn) {
      var promiseCapability = newPromiseCapabilityModule.f(this);
      var result = perform(callbackfn);
      (result.error ? promiseCapability.reject : promiseCapability.resolve)(result.value);
      return promiseCapability.promise;
    }
  });

  var parent$c = promise$1;

  




  var promise = parent$c;

  (function (module) {
  	module.exports = promise;
  } (promise$5));

  (function (module) {
  	module.exports = promiseExports;
  } (promise$6));

  var reverseExports$1 = {};
  var reverse$3 = {
    get exports(){ return reverseExports$1; },
    set exports(v){ reverseExports$1 = v; },
  };

  var reverseExports = {};
  var reverse$2 = {
    get exports(){ return reverseExports; },
    set exports(v){ reverseExports = v; },
  };

  var parent$b = reverse$4;

  var reverse$1 = parent$b;

  var parent$a = reverse$1;

  var reverse = parent$a;

  (function (module) {
  	module.exports = reverse;
  } (reverse$2));

  (function (module) {
  	module.exports = reverseExports;
  } (reverse$3));

  (function (module) {
  	var _typeof = _typeofExports["default"];
  	var _Object$defineProperty = definePropertyExports$3;
  	var _Symbol = symbolExports$2;
  	var _Object$create = createExports$1;
  	var _Object$getPrototypeOf = getPrototypeOfExports$1;
  	var _forEachInstanceProperty = forEachExports$1;
  	var _Object$setPrototypeOf = setPrototypeOfExports$1;
  	var _Promise = promiseExports$1;
  	var _reverseInstanceProperty = reverseExports$1;
  	var _sliceInstanceProperty = sliceExports$2;
  	function _regeneratorRuntime() {
  	  module.exports = _regeneratorRuntime = function _regeneratorRuntime() {
  	    return exports;
  	  }, module.exports.__esModule = true, module.exports["default"] = module.exports;
  	  var exports = {},
  	    Op = Object.prototype,
  	    hasOwn = Op.hasOwnProperty,
  	    defineProperty = _Object$defineProperty || function (obj, key, desc) {
  	      obj[key] = desc.value;
  	    },
  	    $Symbol = "function" == typeof _Symbol ? _Symbol : {},
  	    iteratorSymbol = $Symbol.iterator || "@@iterator",
  	    asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator",
  	    toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";
  	  function define(obj, key, value) {
  	    return _Object$defineProperty(obj, key, {
  	      value: value,
  	      enumerable: !0,
  	      configurable: !0,
  	      writable: !0
  	    }), obj[key];
  	  }
  	  try {
  	    define({}, "");
  	  } catch (err) {
  	    define = function define(obj, key, value) {
  	      return obj[key] = value;
  	    };
  	  }
  	  function wrap(innerFn, outerFn, self, tryLocsList) {
  	    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator,
  	      generator = _Object$create(protoGenerator.prototype),
  	      context = new Context(tryLocsList || []);
  	    return defineProperty(generator, "_invoke", {
  	      value: makeInvokeMethod(innerFn, self, context)
  	    }), generator;
  	  }
  	  function tryCatch(fn, obj, arg) {
  	    try {
  	      return {
  	        type: "normal",
  	        arg: fn.call(obj, arg)
  	      };
  	    } catch (err) {
  	      return {
  	        type: "throw",
  	        arg: err
  	      };
  	    }
  	  }
  	  exports.wrap = wrap;
  	  var ContinueSentinel = {};
  	  function Generator() {}
  	  function GeneratorFunction() {}
  	  function GeneratorFunctionPrototype() {}
  	  var IteratorPrototype = {};
  	  define(IteratorPrototype, iteratorSymbol, function () {
  	    return this;
  	  });
  	  var getProto = _Object$getPrototypeOf,
  	    NativeIteratorPrototype = getProto && getProto(getProto(values([])));
  	  NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype);
  	  var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = _Object$create(IteratorPrototype);
  	  function defineIteratorMethods(prototype) {
  	    var _context;
  	    _forEachInstanceProperty(_context = ["next", "throw", "return"]).call(_context, function (method) {
  	      define(prototype, method, function (arg) {
  	        return this._invoke(method, arg);
  	      });
  	    });
  	  }
  	  function AsyncIterator(generator, PromiseImpl) {
  	    function invoke(method, arg, resolve, reject) {
  	      var record = tryCatch(generator[method], generator, arg);
  	      if ("throw" !== record.type) {
  	        var result = record.arg,
  	          value = result.value;
  	        return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) {
  	          invoke("next", value, resolve, reject);
  	        }, function (err) {
  	          invoke("throw", err, resolve, reject);
  	        }) : PromiseImpl.resolve(value).then(function (unwrapped) {
  	          result.value = unwrapped, resolve(result);
  	        }, function (error) {
  	          return invoke("throw", error, resolve, reject);
  	        });
  	      }
  	      reject(record.arg);
  	    }
  	    var previousPromise;
  	    defineProperty(this, "_invoke", {
  	      value: function value(method, arg) {
  	        function callInvokeWithMethodAndArg() {
  	          return new PromiseImpl(function (resolve, reject) {
  	            invoke(method, arg, resolve, reject);
  	          });
  	        }
  	        return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg();
  	      }
  	    });
  	  }
  	  function makeInvokeMethod(innerFn, self, context) {
  	    var state = "suspendedStart";
  	    return function (method, arg) {
  	      if ("executing" === state) throw new Error("Generator is already running");
  	      if ("completed" === state) {
  	        if ("throw" === method) throw arg;
  	        return doneResult();
  	      }
  	      for (context.method = method, context.arg = arg;;) {
  	        var delegate = context.delegate;
  	        if (delegate) {
  	          var delegateResult = maybeInvokeDelegate(delegate, context);
  	          if (delegateResult) {
  	            if (delegateResult === ContinueSentinel) continue;
  	            return delegateResult;
  	          }
  	        }
  	        if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) {
  	          if ("suspendedStart" === state) throw state = "completed", context.arg;
  	          context.dispatchException(context.arg);
  	        } else "return" === context.method && context.abrupt("return", context.arg);
  	        state = "executing";
  	        var record = tryCatch(innerFn, self, context);
  	        if ("normal" === record.type) {
  	          if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue;
  	          return {
  	            value: record.arg,
  	            done: context.done
  	          };
  	        }
  	        "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg);
  	      }
  	    };
  	  }
  	  function maybeInvokeDelegate(delegate, context) {
  	    var methodName = context.method,
  	      method = delegate.iterator[methodName];
  	    if (undefined === method) return context.delegate = null, "throw" === methodName && delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method) || "return" !== methodName && (context.method = "throw", context.arg = new TypeError("The iterator does not provide a '" + methodName + "' method")), ContinueSentinel;
  	    var record = tryCatch(method, delegate.iterator, context.arg);
  	    if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel;
  	    var info = record.arg;
  	    return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel);
  	  }
  	  function pushTryEntry(locs) {
  	    var entry = {
  	      tryLoc: locs[0]
  	    };
  	    1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry);
  	  }
  	  function resetTryEntry(entry) {
  	    var record = entry.completion || {};
  	    record.type = "normal", delete record.arg, entry.completion = record;
  	  }
  	  function Context(tryLocsList) {
  	    this.tryEntries = [{
  	      tryLoc: "root"
  	    }], _forEachInstanceProperty(tryLocsList).call(tryLocsList, pushTryEntry, this), this.reset(!0);
  	  }
  	  function values(iterable) {
  	    if (iterable) {
  	      var iteratorMethod = iterable[iteratorSymbol];
  	      if (iteratorMethod) return iteratorMethod.call(iterable);
  	      if ("function" == typeof iterable.next) return iterable;
  	      if (!isNaN(iterable.length)) {
  	        var i = -1,
  	          next = function next() {
  	            for (; ++i < iterable.length;) if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next;
  	            return next.value = undefined, next.done = !0, next;
  	          };
  	        return next.next = next;
  	      }
  	    }
  	    return {
  	      next: doneResult
  	    };
  	  }
  	  function doneResult() {
  	    return {
  	      value: undefined,
  	      done: !0
  	    };
  	  }
  	  return GeneratorFunction.prototype = GeneratorFunctionPrototype, defineProperty(Gp, "constructor", {
  	    value: GeneratorFunctionPrototype,
  	    configurable: !0
  	  }), defineProperty(GeneratorFunctionPrototype, "constructor", {
  	    value: GeneratorFunction,
  	    configurable: !0
  	  }), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) {
  	    var ctor = "function" == typeof genFun && genFun.constructor;
  	    return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name));
  	  }, exports.mark = function (genFun) {
  	    return _Object$setPrototypeOf ? _Object$setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = _Object$create(Gp), genFun;
  	  }, exports.awrap = function (arg) {
  	    return {
  	      __await: arg
  	    };
  	  }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () {
  	    return this;
  	  }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) {
  	    void 0 === PromiseImpl && (PromiseImpl = _Promise);
  	    var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl);
  	    return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) {
  	      return result.done ? result.value : iter.next();
  	    });
  	  }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () {
  	    return this;
  	  }), define(Gp, "toString", function () {
  	    return "[object Generator]";
  	  }), exports.keys = function (val) {
  	    var object = Object(val),
  	      keys = [];
  	    for (var key in object) keys.push(key);
  	    return _reverseInstanceProperty(keys).call(keys), function next() {
  	      for (; keys.length;) {
  	        var key = keys.pop();
  	        if (key in object) return next.value = key, next.done = !1, next;
  	      }
  	      return next.done = !0, next;
  	    };
  	  }, exports.values = values, Context.prototype = {
  	    constructor: Context,
  	    reset: function reset(skipTempReset) {
  	      var _context2;
  	      if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, _forEachInstanceProperty(_context2 = this.tryEntries).call(_context2, resetTryEntry), !skipTempReset) for (var name in this) "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+_sliceInstanceProperty(name).call(name, 1)) && (this[name] = undefined);
  	    },
  	    stop: function stop() {
  	      this.done = !0;
  	      var rootRecord = this.tryEntries[0].completion;
  	      if ("throw" === rootRecord.type) throw rootRecord.arg;
  	      return this.rval;
  	    },
  	    dispatchException: function dispatchException(exception) {
  	      if (this.done) throw exception;
  	      var context = this;
  	      function handle(loc, caught) {
  	        return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught;
  	      }
  	      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
  	        var entry = this.tryEntries[i],
  	          record = entry.completion;
  	        if ("root" === entry.tryLoc) return handle("end");
  	        if (entry.tryLoc <= this.prev) {
  	          var hasCatch = hasOwn.call(entry, "catchLoc"),
  	            hasFinally = hasOwn.call(entry, "finallyLoc");
  	          if (hasCatch && hasFinally) {
  	            if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0);
  	            if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc);
  	          } else if (hasCatch) {
  	            if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0);
  	          } else {
  	            if (!hasFinally) throw new Error("try statement without catch or finally");
  	            if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc);
  	          }
  	        }
  	      }
  	    },
  	    abrupt: function abrupt(type, arg) {
  	      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
  	        var entry = this.tryEntries[i];
  	        if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) {
  	          var finallyEntry = entry;
  	          break;
  	        }
  	      }
  	      finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null);
  	      var record = finallyEntry ? finallyEntry.completion : {};
  	      return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record);
  	    },
  	    complete: function complete(record, afterLoc) {
  	      if ("throw" === record.type) throw record.arg;
  	      return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel;
  	    },
  	    finish: function finish(finallyLoc) {
  	      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
  	        var entry = this.tryEntries[i];
  	        if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel;
  	      }
  	    },
  	    "catch": function _catch(tryLoc) {
  	      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
  	        var entry = this.tryEntries[i];
  	        if (entry.tryLoc === tryLoc) {
  	          var record = entry.completion;
  	          if ("throw" === record.type) {
  	            var thrown = record.arg;
  	            resetTryEntry(entry);
  	          }
  	          return thrown;
  	        }
  	      }
  	      throw new Error("illegal catch attempt");
  	    },
  	    delegateYield: function delegateYield(iterable, resultName, nextLoc) {
  	      return this.delegate = {
  	        iterator: values(iterable),
  	        resultName: resultName,
  	        nextLoc: nextLoc
  	      }, "next" === this.method && (this.arg = undefined), ContinueSentinel;
  	    }
  	  }, exports;
  	}
  	module.exports = _regeneratorRuntime, module.exports.__esModule = true, module.exports["default"] = module.exports;
  } (regeneratorRuntime$1));

  

  var runtime = regeneratorRuntimeExports();
  var regenerator = runtime;

  
  try {
    regeneratorRuntime = runtime;
  } catch (accidentalStrictMode) {
    if (typeof globalThis === "object") {
      globalThis.regeneratorRuntime = runtime;
    } else {
      Function("r", "regeneratorRuntime = r")(runtime);
    }
  }

  var mapExports = {};
  var map$2 = {
    get exports(){ return mapExports; },
    set exports(v){ mapExports = v; },
  };

  var internalMetadataExports = {};
  var internalMetadata = {
    get exports(){ return internalMetadataExports; },
    set exports(v){ internalMetadataExports = v; },
  };

  
  var fails$4 = fails$w;

  var arrayBufferNonExtensible = fails$4(function () {
    if (typeof ArrayBuffer == 'function') {
      var buffer = new ArrayBuffer(8);
      
      if (Object.isExtensible(buffer)) Object.defineProperty(buffer, 'a', { value: 8 });
    }
  });

  var fails$3 = fails$w;
  var isObject$2 = isObject$i;
  var classof$3 = classofRaw$2;
  var ARRAY_BUFFER_NON_EXTENSIBLE = arrayBufferNonExtensible;

  
  var $isExtensible = Object.isExtensible;
  var FAILS_ON_PRIMITIVES = fails$3(function () { $isExtensible(1); });

  
  
  var objectIsExtensible = (FAILS_ON_PRIMITIVES || ARRAY_BUFFER_NON_EXTENSIBLE) ? function isExtensible(it) {
    if (!isObject$2(it)) return false;
    if (ARRAY_BUFFER_NON_EXTENSIBLE && classof$3(it) == 'ArrayBuffer') return false;
    return $isExtensible ? $isExtensible(it) : true;
  } : $isExtensible;

  var fails$2 = fails$w;

  var freezing = !fails$2(function () {
    
    return Object.isExtensible(Object.preventExtensions({}));
  });

  var $$3 = _export;
  var uncurryThis$1 = functionUncurryThis;
  var hiddenKeys = hiddenKeys$6;
  var isObject$1 = isObject$i;
  var hasOwn$3 = hasOwnProperty_1;
  var defineProperty$1 = objectDefineProperty.f;
  var getOwnPropertyNamesModule = objectGetOwnPropertyNames;
  var getOwnPropertyNamesExternalModule = objectGetOwnPropertyNamesExternal;
  var isExtensible = objectIsExtensible;
  var uid = uid$4;
  var FREEZING = freezing;

  var REQUIRED = false;
  var METADATA = uid('meta');
  var id = 0;

  var setMetadata = function (it) {
    defineProperty$1(it, METADATA, { value: {
      objectID: 'O' + id++, 
      weakData: {}          
    } });
  };

  var fastKey$1 = function (it, create) {
    
    if (!isObject$1(it)) return typeof it == 'symbol' ? it : (typeof it == 'string' ? 'S' : 'P') + it;
    if (!hasOwn$3(it, METADATA)) {
      
      if (!isExtensible(it)) return 'F';
      
      if (!create) return 'E';
      
      setMetadata(it);
    
    } return it[METADATA].objectID;
  };

  var getWeakData = function (it, create) {
    if (!hasOwn$3(it, METADATA)) {
      
      if (!isExtensible(it)) return true;
      
      if (!create) return false;
      
      setMetadata(it);
    
    } return it[METADATA].weakData;
  };

  
  var onFreeze = function (it) {
    if (FREEZING && REQUIRED && isExtensible(it) && !hasOwn$3(it, METADATA)) setMetadata(it);
    return it;
  };

  var enable = function () {
    meta.enable = function () {  };
    REQUIRED = true;
    var getOwnPropertyNames = getOwnPropertyNamesModule.f;
    var splice = uncurryThis$1([].splice);
    var test = {};
    test[METADATA] = 1;

    
    if (getOwnPropertyNames(test).length) {
      getOwnPropertyNamesModule.f = function (it) {
        var result = getOwnPropertyNames(it);
        for (var i = 0, length = result.length; i < length; i++) {
          if (result[i] === METADATA) {
            splice(result, i, 1);
            break;
          }
        } return result;
      };

      $$3({ target: 'Object', stat: true, forced: true }, {
        getOwnPropertyNames: getOwnPropertyNamesExternalModule.f
      });
    }
  };

  var meta = internalMetadata.exports = {
    enable: enable,
    fastKey: fastKey$1,
    getWeakData: getWeakData,
    onFreeze: onFreeze
  };

  hiddenKeys[METADATA] = true;

  var $$2 = _export;
  var global$1 = global$n;
  var InternalMetadataModule = internalMetadataExports;
  var fails$1 = fails$w;
  var createNonEnumerableProperty = createNonEnumerableProperty$9;
  var iterate$1 = iterate$7;
  var anInstance$1 = anInstance$3;
  var isCallable = isCallable$m;
  var isObject = isObject$i;
  var setToStringTag = setToStringTag$7;
  var defineProperty = objectDefineProperty.f;
  var forEach = arrayIteration.forEach;
  var DESCRIPTORS$1 = descriptors;
  var InternalStateModule$1 = internalState;

  var setInternalState$1 = InternalStateModule$1.set;
  var internalStateGetterFor$1 = InternalStateModule$1.getterFor;

  var collection$2 = function (CONSTRUCTOR_NAME, wrapper, common) {
    var IS_MAP = CONSTRUCTOR_NAME.indexOf('Map') !== -1;
    var IS_WEAK = CONSTRUCTOR_NAME.indexOf('Weak') !== -1;
    var ADDER = IS_MAP ? 'set' : 'add';
    var NativeConstructor = global$1[CONSTRUCTOR_NAME];
    var NativePrototype = NativeConstructor && NativeConstructor.prototype;
    var exported = {};
    var Constructor;

    if (!DESCRIPTORS$1 || !isCallable(NativeConstructor)
      || !(IS_WEAK || NativePrototype.forEach && !fails$1(function () { new NativeConstructor().entries().next(); }))
    ) {
      
      Constructor = common.getConstructor(wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER);
      InternalMetadataModule.enable();
    } else {
      Constructor = wrapper(function (target, iterable) {
        setInternalState$1(anInstance$1(target, Prototype), {
          type: CONSTRUCTOR_NAME,
          collection: new NativeConstructor()
        });
        if (iterable != undefined) iterate$1(iterable, target[ADDER], { that: target, AS_ENTRIES: IS_MAP });
      });

      var Prototype = Constructor.prototype;

      var getInternalState = internalStateGetterFor$1(CONSTRUCTOR_NAME);

      forEach(['add', 'clear', 'delete', 'forEach', 'get', 'has', 'set', 'keys', 'values', 'entries'], function (KEY) {
        var IS_ADDER = KEY == 'add' || KEY == 'set';
        if (KEY in NativePrototype && !(IS_WEAK && KEY == 'clear')) {
          createNonEnumerableProperty(Prototype, KEY, function (a, b) {
            var collection = getInternalState(this).collection;
            if (!IS_ADDER && IS_WEAK && !isObject(a)) return KEY == 'get' ? undefined : false;
            var result = collection[KEY](a === 0 ? 0 : a, b);
            return IS_ADDER ? this : result;
          });
        }
      });

      IS_WEAK || defineProperty(Prototype, 'size', {
        configurable: true,
        get: function () {
          return getInternalState(this).collection.size;
        }
      });
    }

    setToStringTag(Constructor, CONSTRUCTOR_NAME, false, true);

    exported[CONSTRUCTOR_NAME] = Constructor;
    $$2({ global: true, forced: true }, exported);

    if (!IS_WEAK) common.setStrong(Constructor, CONSTRUCTOR_NAME, IS_MAP);

    return Constructor;
  };

  var defineBuiltIn = defineBuiltIn$6;

  var defineBuiltIns$1 = function (target, src, options) {
    for (var key in src) {
      if (options && options.unsafe && target[key]) target[key] = src[key];
      else defineBuiltIn(target, key, src[key], options);
    } return target;
  };

  var create = objectCreate;
  var defineBuiltInAccessor = defineBuiltInAccessor$3;
  var defineBuiltIns = defineBuiltIns$1;
  var bind = functionBindContext;
  var anInstance = anInstance$3;
  var isNullOrUndefined = isNullOrUndefined$5;
  var iterate = iterate$7;
  var defineIterator = iteratorDefine;
  var createIterResultObject = createIterResultObject$3;
  var setSpecies = setSpecies$2;
  var DESCRIPTORS = descriptors;
  var fastKey = internalMetadataExports.fastKey;
  var InternalStateModule = internalState;

  var setInternalState = InternalStateModule.set;
  var internalStateGetterFor = InternalStateModule.getterFor;

  var collectionStrong$2 = {
    getConstructor: function (wrapper, CONSTRUCTOR_NAME, IS_MAP, ADDER) {
      var Constructor = wrapper(function (that, iterable) {
        anInstance(that, Prototype);
        setInternalState(that, {
          type: CONSTRUCTOR_NAME,
          index: create(null),
          first: undefined,
          last: undefined,
          size: 0
        });
        if (!DESCRIPTORS) that.size = 0;
        if (!isNullOrUndefined(iterable)) iterate(iterable, that[ADDER], { that: that, AS_ENTRIES: IS_MAP });
      });

      var Prototype = Constructor.prototype;

      var getInternalState = internalStateGetterFor(CONSTRUCTOR_NAME);

      var define = function (that, key, value) {
        var state = getInternalState(that);
        var entry = getEntry(that, key);
        var previous, index;
        
        if (entry) {
          entry.value = value;
        
        } else {
          state.last = entry = {
            index: index = fastKey(key, true),
            key: key,
            value: value,
            previous: previous = state.last,
            next: undefined,
            removed: false
          };
          if (!state.first) state.first = entry;
          if (previous) previous.next = entry;
          if (DESCRIPTORS) state.size++;
          else that.size++;
          
          if (index !== 'F') state.index[index] = entry;
        } return that;
      };

      var getEntry = function (that, key) {
        var state = getInternalState(that);
        
        var index = fastKey(key);
        var entry;
        if (index !== 'F') return state.index[index];
        
        for (entry = state.first; entry; entry = entry.next) {
          if (entry.key == key) return entry;
        }
      };

      defineBuiltIns(Prototype, {
        
        
        
        clear: function clear() {
          var that = this;
          var state = getInternalState(that);
          var data = state.index;
          var entry = state.first;
          while (entry) {
            entry.removed = true;
            if (entry.previous) entry.previous = entry.previous.next = undefined;
            delete data[entry.index];
            entry = entry.next;
          }
          state.first = state.last = undefined;
          if (DESCRIPTORS) state.size = 0;
          else that.size = 0;
        },
        
        
        
        'delete': function (key) {
          var that = this;
          var state = getInternalState(that);
          var entry = getEntry(that, key);
          if (entry) {
            var next = entry.next;
            var prev = entry.previous;
            delete state.index[entry.index];
            entry.removed = true;
            if (prev) prev.next = next;
            if (next) next.previous = prev;
            if (state.first == entry) state.first = next;
            if (state.last == entry) state.last = prev;
            if (DESCRIPTORS) state.size--;
            else that.size--;
          } return !!entry;
        },
        
        
        
        forEach: function forEach(callbackfn ) {
          var state = getInternalState(this);
          var boundFunction = bind(callbackfn, arguments.length > 1 ? arguments[1] : undefined);
          var entry;
          while (entry = entry ? entry.next : state.first) {
            boundFunction(entry.value, entry.key, this);
            
            while (entry && entry.removed) entry = entry.previous;
          }
        },
        
        
        
        has: function has(key) {
          return !!getEntry(this, key);
        }
      });

      defineBuiltIns(Prototype, IS_MAP ? {
        
        
        get: function get(key) {
          var entry = getEntry(this, key);
          return entry && entry.value;
        },
        
        
        set: function set(key, value) {
          return define(this, key === 0 ? 0 : key, value);
        }
      } : {
        
        
        add: function add(value) {
          return define(this, value = value === 0 ? 0 : value, value);
        }
      });
      if (DESCRIPTORS) defineBuiltInAccessor(Prototype, 'size', {
        configurable: true,
        get: function () {
          return getInternalState(this).size;
        }
      });
      return Constructor;
    },
    setStrong: function (Constructor, CONSTRUCTOR_NAME, IS_MAP) {
      var ITERATOR_NAME = CONSTRUCTOR_NAME + ' Iterator';
      var getInternalCollectionState = internalStateGetterFor(CONSTRUCTOR_NAME);
      var getInternalIteratorState = internalStateGetterFor(ITERATOR_NAME);
      
      
      
      
      
      
      
      
      
      defineIterator(Constructor, CONSTRUCTOR_NAME, function (iterated, kind) {
        setInternalState(this, {
          type: ITERATOR_NAME,
          target: iterated,
          state: getInternalCollectionState(iterated),
          kind: kind,
          last: undefined
        });
      }, function () {
        var state = getInternalIteratorState(this);
        var kind = state.kind;
        var entry = state.last;
        
        while (entry && entry.removed) entry = entry.previous;
        
        if (!state.target || !(state.last = entry = entry ? entry.next : state.state.first)) {
          
          state.target = undefined;
          return createIterResultObject(undefined, true);
        }
        
        if (kind == 'keys') return createIterResultObject(entry.key, false);
        if (kind == 'values') return createIterResultObject(entry.value, false);
        return createIterResultObject([entry.key, entry.value], false);
      }, IS_MAP ? 'entries' : 'values', !IS_MAP, true);

      
      
      
      setSpecies(CONSTRUCTOR_NAME);
    }
  };

  var collection$1 = collection$2;
  var collectionStrong$1 = collectionStrong$2;

  
  
  collection$1('Map', function (init) {
    return function Map() { return init(this, arguments.length ? arguments[0] : undefined); };
  }, collectionStrong$1);

  var path$1 = path$r;

  var map$1 = path$1.Map;

  var parent$9 = map$1;


  var map = parent$9;

  (function (module) {
  	module.exports = map;
  } (map$2));

  var _Map = getDefaultExportFromCjs(mapExports);

  var someExports = {};
  var some$3 = {
    get exports(){ return someExports; },
    set exports(v){ someExports = v; },
  };

  var $$1 = _export;
  var $some = arrayIteration.some;
  var arrayMethodIsStrict$1 = arrayMethodIsStrict$5;

  var STRICT_METHOD$1 = arrayMethodIsStrict$1('some');

  
  
  $$1({ target: 'Array', proto: true, forced: !STRICT_METHOD$1 }, {
    some: function some(callbackfn ) {
      return $some(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
    }
  });

  var entryVirtual$4 = entryVirtual$k;

  var some$2 = entryVirtual$4('Array').some;

  var isPrototypeOf$4 = objectIsPrototypeOf;
  var method$4 = some$2;

  var ArrayPrototype$4 = Array.prototype;

  var some$1 = function (it) {
    var own = it.some;
    return it === ArrayPrototype$4 || (isPrototypeOf$4(ArrayPrototype$4, it) && own === ArrayPrototype$4.some) ? method$4 : own;
  };

  var parent$8 = some$1;

  var some = parent$8;

  (function (module) {
  	module.exports = some;
  } (some$3));

  var _someInstanceProperty = getDefaultExportFromCjs(someExports);

  var keysExports = {};
  var keys$3 = {
    get exports(){ return keysExports; },
    set exports(v){ keysExports = v; },
  };

  var entryVirtual$3 = entryVirtual$k;

  var keys$2 = entryVirtual$3('Array').keys;

  var parent$7 = keys$2;

  var keys$1 = parent$7;

  var classof$2 = classof$e;
  var hasOwn$2 = hasOwnProperty_1;
  var isPrototypeOf$3 = objectIsPrototypeOf;
  var method$3 = keys$1;

  var ArrayPrototype$3 = Array.prototype;

  var DOMIterables$2 = {
    DOMTokenList: true,
    NodeList: true
  };

  var keys = function (it) {
    var own = it.keys;
    return it === ArrayPrototype$3 || (isPrototypeOf$3(ArrayPrototype$3, it) && own === ArrayPrototype$3.keys)
      || hasOwn$2(DOMIterables$2, classof$2(it)) ? method$3 : own;
  };

  (function (module) {
  	module.exports = keys;
  } (keys$3));

  var _keysInstanceProperty = getDefaultExportFromCjs(keysExports);

  var sortExports = {};
  var sort$3 = {
    get exports(){ return sortExports; },
    set exports(v){ sortExports = v; },
  };

  var arraySlice = arraySliceSimple;

  var floor = Math.floor;

  var mergeSort = function (array, comparefn) {
    var length = array.length;
    var middle = floor(length / 2);
    return length < 8 ? insertionSort(array, comparefn) : merge(
      array,
      mergeSort(arraySlice(array, 0, middle), comparefn),
      mergeSort(arraySlice(array, middle), comparefn),
      comparefn
    );
  };

  var insertionSort = function (array, comparefn) {
    var length = array.length;
    var i = 1;
    var element, j;

    while (i < length) {
      j = i;
      element = array[i];
      while (j && comparefn(array[j - 1], element) > 0) {
        array[j] = array[--j];
      }
      if (j !== i++) array[j] = element;
    } return array;
  };

  var merge = function (array, left, right, comparefn) {
    var llength = left.length;
    var rlength = right.length;
    var lindex = 0;
    var rindex = 0;

    while (lindex < llength || rindex < rlength) {
      array[lindex + rindex] = (lindex < llength && rindex < rlength)
        ? comparefn(left[lindex], right[rindex]) <= 0 ? left[lindex++] : right[rindex++]
        : lindex < llength ? left[lindex++] : right[rindex++];
    } return array;
  };

  var arraySort = mergeSort;

  var userAgent$1 = engineUserAgent;

  var firefox = userAgent$1.match(/firefox\/(\d+)/i);

  var engineFfVersion = !!firefox && +firefox[1];

  var UA = engineUserAgent;

  var engineIsIeOrEdge = /MSIE|Trident/.test(UA);

  var userAgent = engineUserAgent;

  var webkit = userAgent.match(/AppleWebKit\/(\d+)\./);

  var engineWebkitVersion = !!webkit && +webkit[1];

  var $ = _export;
  var uncurryThis = functionUncurryThis;
  var aCallable = aCallable$e;
  var toObject = toObject$e;
  var lengthOfArrayLike = lengthOfArrayLike$d;
  var deletePropertyOrThrow = deletePropertyOrThrow$2;
  var toString = toString$a;
  var fails = fails$w;
  var internalSort = arraySort;
  var arrayMethodIsStrict = arrayMethodIsStrict$5;
  var FF = engineFfVersion;
  var IE_OR_EDGE = engineIsIeOrEdge;
  var V8 = engineV8Version;
  var WEBKIT = engineWebkitVersion;

  var test = [];
  var nativeSort = uncurryThis(test.sort);
  var push = uncurryThis(test.push);

  
  var FAILS_ON_UNDEFINED = fails(function () {
    test.sort(undefined);
  });
  
  var FAILS_ON_NULL = fails(function () {
    test.sort(null);
  });
  
  var STRICT_METHOD = arrayMethodIsStrict('sort');

  var STABLE_SORT = !fails(function () {
    
    if (V8) return V8 < 70;
    if (FF && FF > 3) return;
    if (IE_OR_EDGE) return true;
    if (WEBKIT) return WEBKIT < 603;

    var result = '';
    var code, chr, value, index;

    
    for (code = 65; code < 76; code++) {
      chr = String.fromCharCode(code);

      switch (code) {
        case 66: case 69: case 70: case 72: value = 3; break;
        case 68: case 71: value = 4; break;
        default: value = 2;
      }

      for (index = 0; index < 47; index++) {
        test.push({ k: chr + index, v: value });
      }
    }

    test.sort(function (a, b) { return b.v - a.v; });

    for (index = 0; index < test.length; index++) {
      chr = test[index].k.charAt(0);
      if (result.charAt(result.length - 1) !== chr) result += chr;
    }

    return result !== 'DGBEFHACIJK';
  });

  var FORCED = FAILS_ON_UNDEFINED || !FAILS_ON_NULL || !STRICT_METHOD || !STABLE_SORT;

  var getSortCompare = function (comparefn) {
    return function (x, y) {
      if (y === undefined) return -1;
      if (x === undefined) return 1;
      if (comparefn !== undefined) return +comparefn(x, y) || 0;
      return toString(x) > toString(y) ? 1 : -1;
    };
  };

  
  
  $({ target: 'Array', proto: true, forced: FORCED }, {
    sort: function sort(comparefn) {
      if (comparefn !== undefined) aCallable(comparefn);

      var array = toObject(this);

      if (STABLE_SORT) return comparefn === undefined ? nativeSort(array) : nativeSort(array, comparefn);

      var items = [];
      var arrayLength = lengthOfArrayLike(array);
      var itemsLength, index;

      for (index = 0; index < arrayLength; index++) {
        if (index in array) push(items, array[index]);
      }

      internalSort(items, getSortCompare(comparefn));

      itemsLength = lengthOfArrayLike(items);
      index = 0;

      while (index < itemsLength) array[index] = items[index++];
      while (index < arrayLength) deletePropertyOrThrow(array, index++);

      return array;
    }
  });

  var entryVirtual$2 = entryVirtual$k;

  var sort$2 = entryVirtual$2('Array').sort;

  var isPrototypeOf$2 = objectIsPrototypeOf;
  var method$2 = sort$2;

  var ArrayPrototype$2 = Array.prototype;

  var sort$1 = function (it) {
    var own = it.sort;
    return it === ArrayPrototype$2 || (isPrototypeOf$2(ArrayPrototype$2, it) && own === ArrayPrototype$2.sort) ? method$2 : own;
  };

  var parent$6 = sort$1;

  var sort = parent$6;

  (function (module) {
  	module.exports = sort;
  } (sort$3));

  var _sortInstanceProperty = getDefaultExportFromCjs(sortExports);

  var valuesExports = {};
  var values$3 = {
    get exports(){ return valuesExports; },
    set exports(v){ valuesExports = v; },
  };

  var entryVirtual$1 = entryVirtual$k;

  var values$2 = entryVirtual$1('Array').values;

  var parent$5 = values$2;

  var values$1 = parent$5;

  var classof$1 = classof$e;
  var hasOwn$1 = hasOwnProperty_1;
  var isPrototypeOf$1 = objectIsPrototypeOf;
  var method$1 = values$1;

  var ArrayPrototype$1 = Array.prototype;

  var DOMIterables$1 = {
    DOMTokenList: true,
    NodeList: true
  };

  var values = function (it) {
    var own = it.values;
    return it === ArrayPrototype$1 || (isPrototypeOf$1(ArrayPrototype$1, it) && own === ArrayPrototype$1.values)
      || hasOwn$1(DOMIterables$1, classof$1(it)) ? method$1 : own;
  };

  (function (module) {
  	module.exports = values;
  } (values$3));

  var _valuesInstanceProperty = getDefaultExportFromCjs(valuesExports);

  var iteratorExports = {};
  var iterator = {
    get exports(){ return iteratorExports; },
    set exports(v){ iteratorExports = v; },
  };

  (function (module) {
  	module.exports = iterator$3;
  } (iterator));

  var _Symbol$iterator$1 = getDefaultExportFromCjs(iteratorExports);

  var entriesExports = {};
  var entries$3 = {
    get exports(){ return entriesExports; },
    set exports(v){ entriesExports = v; },
  };

  var entryVirtual = entryVirtual$k;

  var entries$2 = entryVirtual('Array').entries;

  var parent$4 = entries$2;

  var entries$1 = parent$4;

  var classof = classof$e;
  var hasOwn = hasOwnProperty_1;
  var isPrototypeOf = objectIsPrototypeOf;
  var method = entries$1;

  var ArrayPrototype = Array.prototype;

  var DOMIterables = {
    DOMTokenList: true,
    NodeList: true
  };

  var entries = function (it) {
    var own = it.entries;
    return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.entries)
      || hasOwn(DOMIterables, classof(it)) ? method : own;
  };

  (function (module) {
  	module.exports = entries;
  } (entries$3));

  var _entriesInstanceProperty = getDefaultExportFromCjs(entriesExports);

  
  
  
  let getRandomValues;
  const rnds8 = new Uint8Array(16);
  function rng() {
    
    if (!getRandomValues) {
      
      getRandomValues = typeof crypto !== 'undefined' && crypto.getRandomValues && crypto.getRandomValues.bind(crypto);

      if (!getRandomValues) {
        throw new Error('crypto.getRandomValues() not supported. See https:
      }
    }

    return getRandomValues(rnds8);
  }

  

  const byteToHex = [];

  for (let i = 0; i < 256; ++i) {
    byteToHex.push((i + 0x100).toString(16).slice(1));
  }

  function unsafeStringify(arr, offset = 0) {
    
    
    return (byteToHex[arr[offset + 0]] + byteToHex[arr[offset + 1]] + byteToHex[arr[offset + 2]] + byteToHex[arr[offset + 3]] + '-' + byteToHex[arr[offset + 4]] + byteToHex[arr[offset + 5]] + '-' + byteToHex[arr[offset + 6]] + byteToHex[arr[offset + 7]] + '-' + byteToHex[arr[offset + 8]] + byteToHex[arr[offset + 9]] + '-' + byteToHex[arr[offset + 10]] + byteToHex[arr[offset + 11]] + byteToHex[arr[offset + 12]] + byteToHex[arr[offset + 13]] + byteToHex[arr[offset + 14]] + byteToHex[arr[offset + 15]]).toLowerCase();
  }

  const randomUUID = typeof crypto !== 'undefined' && crypto.randomUUID && crypto.randomUUID.bind(crypto);
  var native = {
    randomUUID
  };

  function v4(options, buf, offset) {
    if (native.randomUUID && !buf && !options) {
      return native.randomUUID();
    }

    options = options || {};
    const rnds = options.random || (options.rng || rng)(); 

    rnds[6] = rnds[6] & 0x0f | 0x40;
    rnds[8] = rnds[8] & 0x3f | 0x80; 

    if (buf) {
      offset = offset || 0;

      for (let i = 0; i < 16; ++i) {
        buf[offset + i] = rnds[i];
      }

      return buf;
    }

    return unsafeStringify(rnds);
  }

  
  function isId(value) {
    return typeof value === "string" || typeof value === "number";
  }

  
  var Queue = function () {
    

    

    
    function Queue(options) {
      _classCallCheck(this, Queue);
      _defineProperty(this, "delay", void 0);
      _defineProperty(this, "max", void 0);
      _defineProperty(this, "_queue", []);
      _defineProperty(this, "_timeout", null);
      _defineProperty(this, "_extended", null);
      
      this.delay = null;
      this.max = Infinity;
      this.setOptions(options);
    }
    
    _createClass(Queue, [{
      key: "setOptions",
      value: function setOptions(options) {
        if (options && typeof options.delay !== "undefined") {
          this.delay = options.delay;
        }
        if (options && typeof options.max !== "undefined") {
          this.max = options.max;
        }
        this._flushIfNeeded();
      }
      
    }, {
      key: "destroy",
      value:
      
      function destroy() {
        this.flush();
        if (this._extended) {
          var object = this._extended.object;
          var methods = this._extended.methods;
          for (var i = 0; i < methods.length; i++) {
            var method = methods[i];
            if (method.original) {
              
              object[method.name] = method.original;
            } else {
              
              delete object[method.name];
            }
          }
          this._extended = null;
        }
      }
      
    }, {
      key: "replace",
      value: function replace(object, method) {
        
        var me = this;
        var original = object[method];
        if (!original) {
          throw new Error("Method " + method + " undefined");
        }
        object[method] = function () {
          for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
            args[_key] = arguments[_key];
          }
          
          me.queue({
            args: args,
            fn: original,
            context: this
          });
        };
      }
      
    }, {
      key: "queue",
      value: function queue(entry) {
        if (typeof entry === "function") {
          this._queue.push({
            fn: entry
          });
        } else {
          this._queue.push(entry);
        }
        this._flushIfNeeded();
      }
      
    }, {
      key: "_flushIfNeeded",
      value: function _flushIfNeeded() {
        var _this = this;
        
        if (this._queue.length > this.max) {
          this.flush();
        }
        
        if (this._timeout != null) {
          clearTimeout(this._timeout);
          this._timeout = null;
        }
        if (this.queue.length > 0 && typeof this.delay === "number") {
          this._timeout = _setTimeout(function () {
            _this.flush();
          }, this.delay);
        }
      }
      
    }, {
      key: "flush",
      value: function flush() {
        var _context, _context2;
        _forEachInstanceProperty(_context = _spliceInstanceProperty(_context2 = this._queue).call(_context2, 0)).call(_context, function (entry) {
          entry.fn.apply(entry.context || entry.fn, entry.args || []);
        });
      }
    }], [{
      key: "extend",
      value: function extend(object, options) {
        var queue = new Queue(options);
        if (object.flush !== undefined) {
          throw new Error("Target object already has a property flush");
        }
        object.flush = function () {
          queue.flush();
        };
        var methods = [{
          name: "flush",
          original: undefined
        }];
        if (options && options.replace) {
          for (var i = 0; i < options.replace.length; i++) {
            var name = options.replace[i];
            methods.push({
              name: name,
              
              original: object[name]
            });
            
            queue.replace(object, name);
          }
        }
        queue._extended = {
          object: object,
          methods: methods
        };
        return queue;
      }
    }]);
    return Queue;
  }();

  
  var DataSetPart = function () {
    function DataSetPart() {
      _classCallCheck(this, DataSetPart);
      _defineProperty(this, "_subscribers", {
        "*": [],
        add: [],
        remove: [],
        update: []
      });
      
      _defineProperty(this, "subscribe", DataSetPart.prototype.on);
      
      _defineProperty(this, "unsubscribe", DataSetPart.prototype.off);
    }
    _createClass(DataSetPart, [{
      key: "_trigger",
      value:
      
      function _trigger(event, payload, senderId) {
        var _context, _context2;
        if (event === "*") {
          throw new Error("Cannot trigger event *");
        }
        _forEachInstanceProperty(_context = _concatInstanceProperty(_context2 = []).call(_context2, _toConsumableArray(this._subscribers[event]), _toConsumableArray(this._subscribers["*"]))).call(_context, function (subscriber) {
          subscriber(event, payload, senderId != null ? senderId : null);
        });
      }
      
    }, {
      key: "on",
      value: function on(event, callback) {
        if (typeof callback === "function") {
          this._subscribers[event].push(callback);
        }
        
      }
      
    }, {
      key: "off",
      value: function off(event, callback) {
        var _context3;
        this._subscribers[event] = _filterInstanceProperty(_context3 = this._subscribers[event]).call(_context3, function (subscriber) {
          return subscriber !== callback;
        });
      }
    }]);
    return DataSetPart;
  }();

  var setExports = {};
  var set$2 = {
    get exports(){ return setExports; },
    set exports(v){ setExports = v; },
  };

  var collection = collection$2;
  var collectionStrong = collectionStrong$2;

  
  
  collection('Set', function (init) {
    return function Set() { return init(this, arguments.length ? arguments[0] : undefined); };
  }, collectionStrong);

  var path = path$r;

  var set$1 = path.Set;

  var parent$3 = set$1;


  var set = parent$3;

  (function (module) {
  	module.exports = set;
  } (set$2));

  var _Set = getDefaultExportFromCjs(setExports);

  var getIteratorExports$1 = {};
  var getIterator$5 = {
    get exports(){ return getIteratorExports$1; },
    set exports(v){ getIteratorExports$1 = v; },
  };

  var getIteratorExports = {};
  var getIterator$4 = {
    get exports(){ return getIteratorExports; },
    set exports(v){ getIteratorExports = v; },
  };

  var getIterator$3 = getIterator$8;

  var getIterator_1 = getIterator$3;

  var parent$2 = getIterator_1;


  var getIterator$2 = parent$2;

  var parent$1 = getIterator$2;

  var getIterator$1 = parent$1;

  var parent = getIterator$1;

  var getIterator = parent;

  (function (module) {
  	module.exports = getIterator;
  } (getIterator$4));

  (function (module) {
  	module.exports = getIteratorExports;
  } (getIterator$5));

  var _getIterator = getDefaultExportFromCjs(getIteratorExports$1);

  var _Symbol$iterator;
  function _createForOfIteratorHelper$2(o, allowArrayLike) { var it = typeof _Symbol !== "undefined" && _getIteratorMethod(o) || o["@@iterator"]; if (!it) { if (_Array$isArray(o) || (it = _unsupportedIterableToArray$2(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }
  function _unsupportedIterableToArray$2(o, minLen) { var _context10; if (!o) return; if (typeof o === "string") return _arrayLikeToArray$2(o, minLen); var n = _sliceInstanceProperty(_context10 = Object.prototype.toString.call(o)).call(_context10, 8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return _Array$from$1(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray$2(o, minLen); }
  function _arrayLikeToArray$2(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
  _Symbol$iterator = _Symbol$iterator$1;
  
  var DataStream = function () {
    
    function DataStream(pairs) {
      _classCallCheck(this, DataStream);
      _defineProperty(this, "_pairs", void 0);
      this._pairs = pairs;
    }
    
    _createClass(DataStream, [{
      key: _Symbol$iterator,
      value:
      
      regenerator.mark(function value() {
        var _iterator, _step, _step$value, id, item;
        return regenerator.wrap(function value$(_context) {
          while (1) switch (_context.prev = _context.next) {
            case 0:
              _iterator = _createForOfIteratorHelper$2(this._pairs);
              _context.prev = 1;
              _iterator.s();
            case 3:
              if ((_step = _iterator.n()).done) {
                _context.next = 9;
                break;
              }
              _step$value = _slicedToArray(_step.value, 2), id = _step$value[0], item = _step$value[1];
              _context.next = 7;
              return [id, item];
            case 7:
              _context.next = 3;
              break;
            case 9:
              _context.next = 14;
              break;
            case 11:
              _context.prev = 11;
              _context.t0 = _context["catch"](1);
              _iterator.e(_context.t0);
            case 14:
              _context.prev = 14;
              _iterator.f();
              return _context.finish(14);
            case 17:
            case "end":
              return _context.stop();
          }
        }, value, this, [[1, 11, 14, 17]]);
      })
      
    }, {
      key: "entries",
      value:
      
      regenerator.mark(function entries() {
        var _iterator2, _step2, _step2$value, id, item;
        return regenerator.wrap(function entries$(_context2) {
          while (1) switch (_context2.prev = _context2.next) {
            case 0:
              _iterator2 = _createForOfIteratorHelper$2(this._pairs);
              _context2.prev = 1;
              _iterator2.s();
            case 3:
              if ((_step2 = _iterator2.n()).done) {
                _context2.next = 9;
                break;
              }
              _step2$value = _slicedToArray(_step2.value, 2), id = _step2$value[0], item = _step2$value[1];
              _context2.next = 7;
              return [id, item];
            case 7:
              _context2.next = 3;
              break;
            case 9:
              _context2.next = 14;
              break;
            case 11:
              _context2.prev = 11;
              _context2.t0 = _context2["catch"](1);
              _iterator2.e(_context2.t0);
            case 14:
              _context2.prev = 14;
              _iterator2.f();
              return _context2.finish(14);
            case 17:
            case "end":
              return _context2.stop();
          }
        }, entries, this, [[1, 11, 14, 17]]);
      })
      
    }, {
      key: "keys",
      value:
      
      regenerator.mark(function keys() {
        var _iterator3, _step3, _step3$value, id;
        return regenerator.wrap(function keys$(_context3) {
          while (1) switch (_context3.prev = _context3.next) {
            case 0:
              _iterator3 = _createForOfIteratorHelper$2(this._pairs);
              _context3.prev = 1;
              _iterator3.s();
            case 3:
              if ((_step3 = _iterator3.n()).done) {
                _context3.next = 9;
                break;
              }
              _step3$value = _slicedToArray(_step3.value, 1), id = _step3$value[0];
              _context3.next = 7;
              return id;
            case 7:
              _context3.next = 3;
              break;
            case 9:
              _context3.next = 14;
              break;
            case 11:
              _context3.prev = 11;
              _context3.t0 = _context3["catch"](1);
              _iterator3.e(_context3.t0);
            case 14:
              _context3.prev = 14;
              _iterator3.f();
              return _context3.finish(14);
            case 17:
            case "end":
              return _context3.stop();
          }
        }, keys, this, [[1, 11, 14, 17]]);
      })
      
    }, {
      key: "values",
      value:
      
      regenerator.mark(function values() {
        var _iterator4, _step4, _step4$value, item;
        return regenerator.wrap(function values$(_context4) {
          while (1) switch (_context4.prev = _context4.next) {
            case 0:
              _iterator4 = _createForOfIteratorHelper$2(this._pairs);
              _context4.prev = 1;
              _iterator4.s();
            case 3:
              if ((_step4 = _iterator4.n()).done) {
                _context4.next = 9;
                break;
              }
              _step4$value = _slicedToArray(_step4.value, 2), item = _step4$value[1];
              _context4.next = 7;
              return item;
            case 7:
              _context4.next = 3;
              break;
            case 9:
              _context4.next = 14;
              break;
            case 11:
              _context4.prev = 11;
              _context4.t0 = _context4["catch"](1);
              _iterator4.e(_context4.t0);
            case 14:
              _context4.prev = 14;
              _iterator4.f();
              return _context4.finish(14);
            case 17:
            case "end":
              return _context4.stop();
          }
        }, values, this, [[1, 11, 14, 17]]);
      })
      
    }, {
      key: "toIdArray",
      value: function toIdArray() {
        var _context5;
        return _mapInstanceProperty(_context5 = _toConsumableArray(this._pairs)).call(_context5, function (pair) {
          return pair[0];
        });
      }
      
    }, {
      key: "toItemArray",
      value: function toItemArray() {
        var _context6;
        return _mapInstanceProperty(_context6 = _toConsumableArray(this._pairs)).call(_context6, function (pair) {
          return pair[1];
        });
      }
      
    }, {
      key: "toEntryArray",
      value: function toEntryArray() {
        return _toConsumableArray(this._pairs);
      }
      
    }, {
      key: "toObjectMap",
      value: function toObjectMap() {
        var map = _Object$create$1(null);
        var _iterator5 = _createForOfIteratorHelper$2(this._pairs),
          _step5;
        try {
          for (_iterator5.s(); !(_step5 = _iterator5.n()).done;) {
            var _step5$value = _slicedToArray(_step5.value, 2),
              id = _step5$value[0],
              item = _step5$value[1];
            map[id] = item;
          }
        } catch (err) {
          _iterator5.e(err);
        } finally {
          _iterator5.f();
        }
        return map;
      }
      
    }, {
      key: "toMap",
      value: function toMap() {
        return new _Map(this._pairs);
      }
      
    }, {
      key: "toIdSet",
      value: function toIdSet() {
        return new _Set(this.toIdArray());
      }
      
    }, {
      key: "toItemSet",
      value: function toItemSet() {
        return new _Set(this.toItemArray());
      }
      
    }, {
      key: "cache",
      value: function cache() {
        return new DataStream(_toConsumableArray(this._pairs));
      }
      
    }, {
      key: "distinct",
      value: function distinct(callback) {
        var set = new _Set();
        var _iterator6 = _createForOfIteratorHelper$2(this._pairs),
          _step6;
        try {
          for (_iterator6.s(); !(_step6 = _iterator6.n()).done;) {
            var _step6$value = _slicedToArray(_step6.value, 2),
              id = _step6$value[0],
              item = _step6$value[1];
            set.add(callback(item, id));
          }
        } catch (err) {
          _iterator6.e(err);
        } finally {
          _iterator6.f();
        }
        return set;
      }
      
    }, {
      key: "filter",
      value: function filter(callback) {
        var pairs = this._pairs;
        return new DataStream(_defineProperty({}, _Symbol$iterator$1, regenerator.mark(function _callee() {
          var _iterator7, _step7, _step7$value, id, item;
          return regenerator.wrap(function _callee$(_context7) {
            while (1) switch (_context7.prev = _context7.next) {
              case 0:
                _iterator7 = _createForOfIteratorHelper$2(pairs);
                _context7.prev = 1;
                _iterator7.s();
              case 3:
                if ((_step7 = _iterator7.n()).done) {
                  _context7.next = 10;
                  break;
                }
                _step7$value = _slicedToArray(_step7.value, 2), id = _step7$value[0], item = _step7$value[1];
                if (!callback(item, id)) {
                  _context7.next = 8;
                  break;
                }
                _context7.next = 8;
                return [id, item];
              case 8:
                _context7.next = 3;
                break;
              case 10:
                _context7.next = 15;
                break;
              case 12:
                _context7.prev = 12;
                _context7.t0 = _context7["catch"](1);
                _iterator7.e(_context7.t0);
              case 15:
                _context7.prev = 15;
                _iterator7.f();
                return _context7.finish(15);
              case 18:
              case "end":
                return _context7.stop();
            }
          }, _callee, null, [[1, 12, 15, 18]]);
        })));
      }
      
    }, {
      key: "forEach",
      value: function forEach(callback) {
        var _iterator8 = _createForOfIteratorHelper$2(this._pairs),
          _step8;
        try {
          for (_iterator8.s(); !(_step8 = _iterator8.n()).done;) {
            var _step8$value = _slicedToArray(_step8.value, 2),
              id = _step8$value[0],
              item = _step8$value[1];
            callback(item, id);
          }
        } catch (err) {
          _iterator8.e(err);
        } finally {
          _iterator8.f();
        }
      }
      
    }, {
      key: "map",
      value: function map(callback) {
        var pairs = this._pairs;
        return new DataStream(_defineProperty({}, _Symbol$iterator$1, regenerator.mark(function _callee2() {
          var _iterator9, _step9, _step9$value, id, item;
          return regenerator.wrap(function _callee2$(_context8) {
            while (1) switch (_context8.prev = _context8.next) {
              case 0:
                _iterator9 = _createForOfIteratorHelper$2(pairs);
                _context8.prev = 1;
                _iterator9.s();
              case 3:
                if ((_step9 = _iterator9.n()).done) {
                  _context8.next = 9;
                  break;
                }
                _step9$value = _slicedToArray(_step9.value, 2), id = _step9$value[0], item = _step9$value[1];
                _context8.next = 7;
                return [id, callback(item, id)];
              case 7:
                _context8.next = 3;
                break;
              case 9:
                _context8.next = 14;
                break;
              case 11:
                _context8.prev = 11;
                _context8.t0 = _context8["catch"](1);
                _iterator9.e(_context8.t0);
              case 14:
                _context8.prev = 14;
                _iterator9.f();
                return _context8.finish(14);
              case 17:
              case "end":
                return _context8.stop();
            }
          }, _callee2, null, [[1, 11, 14, 17]]);
        })));
      }
      
    }, {
      key: "max",
      value: function max(callback) {
        var iter = _getIterator(this._pairs);
        var curr = iter.next();
        if (curr.done) {
          return null;
        }
        var maxItem = curr.value[1];
        var maxValue = callback(curr.value[1], curr.value[0]);
        while (!(curr = iter.next()).done) {
          var _curr$value = _slicedToArray(curr.value, 2),
            id = _curr$value[0],
            item = _curr$value[1];
          var _value = callback(item, id);
          if (_value > maxValue) {
            maxValue = _value;
            maxItem = item;
          }
        }
        return maxItem;
      }
      
    }, {
      key: "min",
      value: function min(callback) {
        var iter = _getIterator(this._pairs);
        var curr = iter.next();
        if (curr.done) {
          return null;
        }
        var minItem = curr.value[1];
        var minValue = callback(curr.value[1], curr.value[0]);
        while (!(curr = iter.next()).done) {
          var _curr$value2 = _slicedToArray(curr.value, 2),
            id = _curr$value2[0],
            item = _curr$value2[1];
          var _value2 = callback(item, id);
          if (_value2 < minValue) {
            minValue = _value2;
            minItem = item;
          }
        }
        return minItem;
      }
      
    }, {
      key: "reduce",
      value: function reduce(callback, accumulator) {
        var _iterator10 = _createForOfIteratorHelper$2(this._pairs),
          _step10;
        try {
          for (_iterator10.s(); !(_step10 = _iterator10.n()).done;) {
            var _step10$value = _slicedToArray(_step10.value, 2),
              id = _step10$value[0],
              item = _step10$value[1];
            accumulator = callback(accumulator, item, id);
          }
        } catch (err) {
          _iterator10.e(err);
        } finally {
          _iterator10.f();
        }
        return accumulator;
      }
      
    }, {
      key: "sort",
      value: function sort(callback) {
        var _this = this;
        return new DataStream(_defineProperty({}, _Symbol$iterator$1, function () {
          var _context9;
          return _getIterator(_sortInstanceProperty(_context9 = _toConsumableArray(_this._pairs)).call(_context9, function (_ref, _ref2) {
            var _ref3 = _slicedToArray(_ref, 2),
              idA = _ref3[0],
              itemA = _ref3[1];
            var _ref4 = _slicedToArray(_ref2, 2),
              idB = _ref4[0],
              itemB = _ref4[1];
            return callback(itemA, itemB, idA, idB);
          }));
        }));
      }
    }]);
    return DataStream;
  }();

  function ownKeys(object, enumerableOnly) { var keys = _Object$keys(object); if (_Object$getOwnPropertySymbols) { var symbols = _Object$getOwnPropertySymbols(object); enumerableOnly && (symbols = _filterInstanceProperty(symbols).call(symbols, function (sym) { return _Object$getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
  function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var _context10, _context11; var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? _forEachInstanceProperty(_context10 = ownKeys(Object(source), !0)).call(_context10, function (key) { _defineProperty(target, key, source[key]); }) : _Object$getOwnPropertyDescriptors ? _Object$defineProperties(target, _Object$getOwnPropertyDescriptors(source)) : _forEachInstanceProperty(_context11 = ownKeys(Object(source))).call(_context11, function (key) { _Object$defineProperty(target, key, _Object$getOwnPropertyDescriptor(source, key)); }); } return target; }
  function _createForOfIteratorHelper$1(o, allowArrayLike) { var it = typeof _Symbol !== "undefined" && _getIteratorMethod(o) || o["@@iterator"]; if (!it) { if (_Array$isArray(o) || (it = _unsupportedIterableToArray$1(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }
  function _unsupportedIterableToArray$1(o, minLen) { var _context9; if (!o) return; if (typeof o === "string") return _arrayLikeToArray$1(o, minLen); var n = _sliceInstanceProperty(_context9 = Object.prototype.toString.call(o)).call(_context9, 8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return _Array$from$1(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray$1(o, minLen); }
  function _arrayLikeToArray$1(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
  function _createSuper$1(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$1(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
  function _isNativeReflectConstruct$1() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
  
  function ensureFullItem(item, idProp) {
    if (item[idProp] == null) {
      
      item[idProp] = v4();
    }
    return item;
  }
  
  var DataSet = function (_DataSetPart) {
    _inherits(DataSet, _DataSetPart);
    var _super = _createSuper$1(DataSet);
    
    function DataSet(data, options) {
      var _this;
      _classCallCheck(this, DataSet);
      _this = _super.call(this);
      
      
      _defineProperty(_assertThisInitialized(_this), "flush", void 0);
      
      _defineProperty(_assertThisInitialized(_this), "length", void 0);
      _defineProperty(_assertThisInitialized(_this), "_options", void 0);
      _defineProperty(_assertThisInitialized(_this), "_data", void 0);
      _defineProperty(_assertThisInitialized(_this), "_idProp", void 0);
      _defineProperty(_assertThisInitialized(_this), "_queue", null);
      if (data && !_Array$isArray(data)) {
        options = data;
        data = [];
      }
      _this._options = options || {};
      _this._data = new _Map(); 
      _this.length = 0; 
      _this._idProp = _this._options.fieldId || "id"; 
      
      if (data && data.length) {
        _this.add(data);
      }
      _this.setOptions(options);
      return _this;
    }
    
    _createClass(DataSet, [{
      key: "idProp",
      get:
      
      function get() {
        return this._idProp;
      }
    }, {
      key: "setOptions",
      value: function setOptions(options) {
        if (options && options.queue !== undefined) {
          if (options.queue === false) {
            
            if (this._queue) {
              this._queue.destroy();
              this._queue = null;
            }
          } else {
            
            if (!this._queue) {
              this._queue = Queue.extend(this, {
                replace: ["add", "update", "remove"]
              });
            }
            if (options.queue && _typeof$1(options.queue) === "object") {
              this._queue.setOptions(options.queue);
            }
          }
        }
      }
      
    }, {
      key: "add",
      value: function add(data, senderId) {
        var _this2 = this;
        var addedIds = [];
        var id;
        if (_Array$isArray(data)) {
          
          var idsToAdd = _mapInstanceProperty(data).call(data, function (d) {
            return d[_this2._idProp];
          });
          if (_someInstanceProperty(idsToAdd).call(idsToAdd, function (id) {
            return _this2._data.has(id);
          })) {
            throw new Error("A duplicate id was found in the parameter array.");
          }
          for (var i = 0, len = data.length; i < len; i++) {
            id = this._addItem(data[i]);
            addedIds.push(id);
          }
        } else if (data && _typeof$1(data) === "object") {
          
          id = this._addItem(data);
          addedIds.push(id);
        } else {
          throw new Error("Unknown dataType");
        }
        if (addedIds.length) {
          this._trigger("add", {
            items: addedIds
          }, senderId);
        }
        return addedIds;
      }
      
    }, {
      key: "update",
      value: function update(data, senderId) {
        var _this3 = this;
        var addedIds = [];
        var updatedIds = [];
        var oldData = [];
        var updatedData = [];
        var idProp = this._idProp;
        var addOrUpdate = function addOrUpdate(item) {
          var origId = item[idProp];
          if (origId != null && _this3._data.has(origId)) {
            var fullItem = item; 
            var oldItem = _Object$assign({}, _this3._data.get(origId));
            
            var id = _this3._updateItem(fullItem);
            updatedIds.push(id);
            updatedData.push(fullItem);
            oldData.push(oldItem);
          } else {
            
            var _id = _this3._addItem(item);
            addedIds.push(_id);
          }
        };
        if (_Array$isArray(data)) {
          
          for (var i = 0, len = data.length; i < len; i++) {
            if (data[i] && _typeof$1(data[i]) === "object") {
              addOrUpdate(data[i]);
            } else {
              console.warn("Ignoring input item, which is not an object at index " + i);
            }
          }
        } else if (data && _typeof$1(data) === "object") {
          
          addOrUpdate(data);
        } else {
          throw new Error("Unknown dataType");
        }
        if (addedIds.length) {
          this._trigger("add", {
            items: addedIds
          }, senderId);
        }
        if (updatedIds.length) {
          var props = {
            items: updatedIds,
            oldData: oldData,
            data: updatedData
          };
          
          
          
          
          
          
          
          this._trigger("update", props, senderId);
        }
        return _concatInstanceProperty(addedIds).call(addedIds, updatedIds);
      }
      
    }, {
      key: "updateOnly",
      value: function updateOnly(data, senderId) {
        var _context,
          _this4 = this;
        if (!_Array$isArray(data)) {
          data = [data];
        }
        var updateEventData = _mapInstanceProperty(_context = _mapInstanceProperty(data).call(data, function (update) {
          var oldData = _this4._data.get(update[_this4._idProp]);
          if (oldData == null) {
            throw new Error("Updating non-existent items is not allowed.");
          }
          return {
            oldData: oldData,
            update: update
          };
        })).call(_context, function (_ref) {
          var oldData = _ref.oldData,
            update = _ref.update;
          var id = oldData[_this4._idProp];
          var updatedData = pureDeepObjectAssign(oldData, update);
          _this4._data.set(id, updatedData);
          return {
            id: id,
            oldData: oldData,
            updatedData: updatedData
          };
        });
        if (updateEventData.length) {
          var props = {
            items: _mapInstanceProperty(updateEventData).call(updateEventData, function (value) {
              return value.id;
            }),
            oldData: _mapInstanceProperty(updateEventData).call(updateEventData, function (value) {
              return value.oldData;
            }),
            data: _mapInstanceProperty(updateEventData).call(updateEventData, function (value) {
              return value.updatedData;
            })
          };
          
          
          
          
          
          
          
          this._trigger("update", props, senderId);
          return props.items;
        } else {
          return [];
        }
      }
      
    }, {
      key: "get",
      value: function get(first, second) {
        
        
        var id = undefined;
        var ids = undefined;
        var options = undefined;
        if (isId(first)) {
          
          id = first;
          options = second;
        } else if (_Array$isArray(first)) {
          
          ids = first;
          options = second;
        } else {
          
          options = first;
        }
        
        var returnType = options && options.returnType === "Object" ? "Object" : "Array";
        
        
        
        
        
        
        
        
        
        
        
        
        var filter = options && _filterInstanceProperty(options);
        var items = [];
        var item = undefined;
        var itemIds = undefined;
        var itemId = undefined;
        
        if (id != null) {
          
          item = this._data.get(id);
          if (item && filter && !filter(item)) {
            item = undefined;
          }
        } else if (ids != null) {
          
          for (var i = 0, len = ids.length; i < len; i++) {
            item = this._data.get(ids[i]);
            if (item != null && (!filter || filter(item))) {
              items.push(item);
            }
          }
        } else {
          var _context2;
          
          itemIds = _toConsumableArray(_keysInstanceProperty(_context2 = this._data).call(_context2));
          for (var _i = 0, _len = itemIds.length; _i < _len; _i++) {
            itemId = itemIds[_i];
            item = this._data.get(itemId);
            if (item != null && (!filter || filter(item))) {
              items.push(item);
            }
          }
        }
        
        if (options && options.order && id == undefined) {
          this._sort(items, options.order);
        }
        
        if (options && options.fields) {
          var fields = options.fields;
          if (id != undefined && item != null) {
            item = this._filterFields(item, fields);
          } else {
            for (var _i2 = 0, _len2 = items.length; _i2 < _len2; _i2++) {
              items[_i2] = this._filterFields(items[_i2], fields);
            }
          }
        }
        
        if (returnType == "Object") {
          var result = {};
          for (var _i3 = 0, _len3 = items.length; _i3 < _len3; _i3++) {
            var resultant = items[_i3];
            
            
            var _id2 = resultant[this._idProp];
            result[_id2] = resultant;
          }
          return result;
        } else {
          if (id != null) {
            var _item;
            
            return (_item = item) !== null && _item !== void 0 ? _item : null;
          } else {
            
            return items;
          }
        }
      }
      
    }, {
      key: "getIds",
      value: function getIds(options) {
        var data = this._data;
        var filter = options && _filterInstanceProperty(options);
        var order = options && options.order;
        var itemIds = _toConsumableArray(_keysInstanceProperty(data).call(data));
        var ids = [];
        if (filter) {
          
          if (order) {
            
            var items = [];
            for (var i = 0, len = itemIds.length; i < len; i++) {
              var id = itemIds[i];
              var item = this._data.get(id);
              if (item != null && filter(item)) {
                items.push(item);
              }
            }
            this._sort(items, order);
            for (var _i4 = 0, _len4 = items.length; _i4 < _len4; _i4++) {
              ids.push(items[_i4][this._idProp]);
            }
          } else {
            
            for (var _i5 = 0, _len5 = itemIds.length; _i5 < _len5; _i5++) {
              var _id3 = itemIds[_i5];
              var _item2 = this._data.get(_id3);
              if (_item2 != null && filter(_item2)) {
                ids.push(_item2[this._idProp]);
              }
            }
          }
        } else {
          
          if (order) {
            
            var _items = [];
            for (var _i6 = 0, _len6 = itemIds.length; _i6 < _len6; _i6++) {
              var _id4 = itemIds[_i6];
              _items.push(data.get(_id4));
            }
            this._sort(_items, order);
            for (var _i7 = 0, _len7 = _items.length; _i7 < _len7; _i7++) {
              ids.push(_items[_i7][this._idProp]);
            }
          } else {
            
            for (var _i8 = 0, _len8 = itemIds.length; _i8 < _len8; _i8++) {
              var _id5 = itemIds[_i8];
              var _item3 = data.get(_id5);
              if (_item3 != null) {
                ids.push(_item3[this._idProp]);
              }
            }
          }
        }
        return ids;
      }
      
    }, {
      key: "getDataSet",
      value: function getDataSet() {
        return this;
      }
      
    }, {
      key: "forEach",
      value: function forEach(callback, options) {
        var filter = options && _filterInstanceProperty(options);
        var data = this._data;
        var itemIds = _toConsumableArray(_keysInstanceProperty(data).call(data));
        if (options && options.order) {
          
          var items = this.get(options);
          for (var i = 0, len = items.length; i < len; i++) {
            var item = items[i];
            var id = item[this._idProp];
            callback(item, id);
          }
        } else {
          
          for (var _i9 = 0, _len9 = itemIds.length; _i9 < _len9; _i9++) {
            var _id6 = itemIds[_i9];
            var _item4 = this._data.get(_id6);
            if (_item4 != null && (!filter || filter(_item4))) {
              callback(_item4, _id6);
            }
          }
        }
      }
      
    }, {
      key: "map",
      value: function map(callback, options) {
        var filter = options && _filterInstanceProperty(options);
        var mappedItems = [];
        var data = this._data;
        var itemIds = _toConsumableArray(_keysInstanceProperty(data).call(data));
        
        for (var i = 0, len = itemIds.length; i < len; i++) {
          var id = itemIds[i];
          var item = this._data.get(id);
          if (item != null && (!filter || filter(item))) {
            mappedItems.push(callback(item, id));
          }
        }
        
        if (options && options.order) {
          this._sort(mappedItems, options.order);
        }
        return mappedItems;
      }
      
    }, {
      key: "_filterFields",
      value: function _filterFields(item, fields) {
        var _context3;
        if (!item) {
          
          return item;
        }
        return _reduceInstanceProperty(_context3 = _Array$isArray(fields) ?
        
        fields :
        
        _Object$keys(fields)).call(_context3, function (filteredItem, field) {
          filteredItem[field] = item[field];
          return filteredItem;
        }, {});
      }
      
    }, {
      key: "_sort",
      value: function _sort(items, order) {
        if (typeof order === "string") {
          
          var name = order; 
          _sortInstanceProperty(items).call(items, function (a, b) {
            
            var av = a[name];
            var bv = b[name];
            return av > bv ? 1 : av < bv ? -1 : 0;
          });
        } else if (typeof order === "function") {
          
          _sortInstanceProperty(items).call(items, order);
        } else {
          
          
          throw new TypeError("Order must be a function or a string");
        }
      }
      
    }, {
      key: "remove",
      value: function remove(id, senderId) {
        var removedIds = [];
        var removedItems = [];
        
        var ids = _Array$isArray(id) ? id : [id];
        for (var i = 0, len = ids.length; i < len; i++) {
          var item = this._remove(ids[i]);
          if (item) {
            var itemId = item[this._idProp];
            if (itemId != null) {
              removedIds.push(itemId);
              removedItems.push(item);
            }
          }
        }
        if (removedIds.length) {
          this._trigger("remove", {
            items: removedIds,
            oldData: removedItems
          }, senderId);
        }
        return removedIds;
      }
      
    }, {
      key: "_remove",
      value: function _remove(id) {
        
        
        var ident;
        
        if (isId(id)) {
          ident = id;
        } else if (id && _typeof$1(id) === "object") {
          ident = id[this._idProp]; 
        }
        
        if (ident != null && this._data.has(ident)) {
          var item = this._data.get(ident) || null;
          this._data.delete(ident);
          --this.length;
          return item;
        }
        return null;
      }
      
    }, {
      key: "clear",
      value: function clear(senderId) {
        var _context4;
        var ids = _toConsumableArray(_keysInstanceProperty(_context4 = this._data).call(_context4));
        var items = [];
        for (var i = 0, len = ids.length; i < len; i++) {
          items.push(this._data.get(ids[i]));
        }
        this._data.clear();
        this.length = 0;
        this._trigger("remove", {
          items: ids,
          oldData: items
        }, senderId);
        return ids;
      }
      
    }, {
      key: "max",
      value: function max(field) {
        var _context5;
        var max = null;
        var maxField = null;
        var _iterator = _createForOfIteratorHelper$1(_valuesInstanceProperty(_context5 = this._data).call(_context5)),
          _step;
        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var item = _step.value;
            var itemField = item[field];
            if (typeof itemField === "number" && (maxField == null || itemField > maxField)) {
              max = item;
              maxField = itemField;
            }
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
        return max || null;
      }
      
    }, {
      key: "min",
      value: function min(field) {
        var _context6;
        var min = null;
        var minField = null;
        var _iterator2 = _createForOfIteratorHelper$1(_valuesInstanceProperty(_context6 = this._data).call(_context6)),
          _step2;
        try {
          for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
            var item = _step2.value;
            var itemField = item[field];
            if (typeof itemField === "number" && (minField == null || itemField < minField)) {
              min = item;
              minField = itemField;
            }
          }
        } catch (err) {
          _iterator2.e(err);
        } finally {
          _iterator2.f();
        }
        return min || null;
      }
      
    }, {
      key: "distinct",
      value: function distinct(prop) {
        var data = this._data;
        var itemIds = _toConsumableArray(_keysInstanceProperty(data).call(data));
        var values = [];
        var count = 0;
        for (var i = 0, len = itemIds.length; i < len; i++) {
          var id = itemIds[i];
          var item = data.get(id);
          var value = item[prop];
          var exists = false;
          for (var j = 0; j < count; j++) {
            if (values[j] == value) {
              exists = true;
              break;
            }
          }
          if (!exists && value !== undefined) {
            values[count] = value;
            count++;
          }
        }
        return values;
      }
      
    }, {
      key: "_addItem",
      value: function _addItem(item) {
        var fullItem = ensureFullItem(item, this._idProp);
        var id = fullItem[this._idProp];
        
        if (this._data.has(id)) {
          
          throw new Error("Cannot add item: item with id " + id + " already exists");
        }
        this._data.set(id, fullItem);
        ++this.length;
        return id;
      }
      
    }, {
      key: "_updateItem",
      value: function _updateItem(update) {
        var id = update[this._idProp];
        if (id == null) {
          throw new Error("Cannot update item: item has no id (item: " + _JSON$stringify(update) + ")");
        }
        var item = this._data.get(id);
        if (!item) {
          
          throw new Error("Cannot update item: no item with id " + id + " found");
        }
        this._data.set(id, _objectSpread(_objectSpread({}, item), update));
        return id;
      }
      
    }, {
      key: "stream",
      value: function stream(ids) {
        if (ids) {
          var data = this._data;
          return new DataStream(_defineProperty({}, _Symbol$iterator$1, regenerator.mark(function _callee() {
            var _iterator3, _step3, id, item;
            return regenerator.wrap(function _callee$(_context7) {
              while (1) switch (_context7.prev = _context7.next) {
                case 0:
                  _iterator3 = _createForOfIteratorHelper$1(ids);
                  _context7.prev = 1;
                  _iterator3.s();
                case 3:
                  if ((_step3 = _iterator3.n()).done) {
                    _context7.next = 11;
                    break;
                  }
                  id = _step3.value;
                  item = data.get(id);
                  if (!(item != null)) {
                    _context7.next = 9;
                    break;
                  }
                  _context7.next = 9;
                  return [id, item];
                case 9:
                  _context7.next = 3;
                  break;
                case 11:
                  _context7.next = 16;
                  break;
                case 13:
                  _context7.prev = 13;
                  _context7.t0 = _context7["catch"](1);
                  _iterator3.e(_context7.t0);
                case 16:
                  _context7.prev = 16;
                  _iterator3.f();
                  return _context7.finish(16);
                case 19:
                case "end":
                  return _context7.stop();
              }
            }, _callee, null, [[1, 13, 16, 19]]);
          })));
        } else {
          var _context8;
          return new DataStream(_defineProperty({}, _Symbol$iterator$1, _bindInstanceProperty$1(_context8 = _entriesInstanceProperty(this._data)).call(_context8, this._data)));
        }
      }
    }]);
    return DataSet;
  }(DataSetPart);

  function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof _Symbol !== "undefined" && _getIteratorMethod(o) || o["@@iterator"]; if (!it) { if (_Array$isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }
  function _unsupportedIterableToArray(o, minLen) { var _context5; if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = _sliceInstanceProperty(_context5 = Object.prototype.toString.call(o)).call(_context5, 8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return _Array$from$1(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
  function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
  function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
  function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
  
  var DataView = function (_DataSetPart) {
    _inherits(DataView, _DataSetPart);
    var _super = _createSuper(DataView);
    
    function DataView(data, options) {
      var _context;
      var _this;
      _classCallCheck(this, DataView);
      _this = _super.call(this);
      
      _defineProperty(_assertThisInitialized(_this), "length", 0);
      _defineProperty(_assertThisInitialized(_this), "_listener", void 0);
      _defineProperty(_assertThisInitialized(_this), "_data", void 0);
      
      _defineProperty(_assertThisInitialized(_this), "_ids", new _Set());
      
      _defineProperty(_assertThisInitialized(_this), "_options", void 0);
      _this._options = options || {};
      _this._listener = _bindInstanceProperty$1(_context = _this._onEvent).call(_context, _assertThisInitialized(_this));
      _this.setData(data);
      return _this;
    }
    
    
    
    _createClass(DataView, [{
      key: "idProp",
      get:
      
      function get() {
        return this.getDataSet().idProp;
      }
    }, {
      key: "setData",
      value: function setData(data) {
        if (this._data) {
          
          if (this._data.off) {
            this._data.off("*", this._listener);
          }
          
          var ids = this._data.getIds({
            filter: _filterInstanceProperty(this._options)
          });
          var items = this._data.get(ids);
          this._ids.clear();
          this.length = 0;
          this._trigger("remove", {
            items: ids,
            oldData: items
          });
        }
        if (data != null) {
          this._data = data;
          
          var _ids = this._data.getIds({
            filter: _filterInstanceProperty(this._options)
          });
          for (var i = 0, len = _ids.length; i < len; i++) {
            var id = _ids[i];
            this._ids.add(id);
          }
          this.length = _ids.length;
          this._trigger("add", {
            items: _ids
          });
        } else {
          this._data = new DataSet();
        }
        
        if (this._data.on) {
          this._data.on("*", this._listener);
        }
      }
      
    }, {
      key: "refresh",
      value: function refresh() {
        var ids = this._data.getIds({
          filter: _filterInstanceProperty(this._options)
        });
        var oldIds = _toConsumableArray(this._ids);
        var newIds = {};
        var addedIds = [];
        var removedIds = [];
        var removedItems = [];
        
        for (var i = 0, len = ids.length; i < len; i++) {
          var id = ids[i];
          newIds[id] = true;
          if (!this._ids.has(id)) {
            addedIds.push(id);
            this._ids.add(id);
          }
        }
        
        for (var _i = 0, _len = oldIds.length; _i < _len; _i++) {
          var _id = oldIds[_i];
          var item = this._data.get(_id);
          if (item == null) {
            
            
            
            
            console.error("If you see this, report it please.");
          } else if (!newIds[_id]) {
            removedIds.push(_id);
            removedItems.push(item);
            this._ids.delete(_id);
          }
        }
        this.length += addedIds.length - removedIds.length;
        
        if (addedIds.length) {
          this._trigger("add", {
            items: addedIds
          });
        }
        if (removedIds.length) {
          this._trigger("remove", {
            items: removedIds,
            oldData: removedItems
          });
        }
      }
      
    }, {
      key: "get",
      value: function get(first, second) {
        if (this._data == null) {
          return null;
        }
        
        var ids = null;
        var options;
        if (isId(first) || _Array$isArray(first)) {
          ids = first;
          options = second;
        } else {
          options = first;
        }
        
        var viewOptions = _Object$assign({}, this._options, options);
        
        var thisFilter = _filterInstanceProperty(this._options);
        var optionsFilter = options && _filterInstanceProperty(options);
        if (thisFilter && optionsFilter) {
          viewOptions.filter = function (item) {
            return thisFilter(item) && optionsFilter(item);
          };
        }
        if (ids == null) {
          return this._data.get(viewOptions);
        } else {
          return this._data.get(ids, viewOptions);
        }
      }
      
    }, {
      key: "getIds",
      value: function getIds(options) {
        if (this._data.length) {
          var defaultFilter = _filterInstanceProperty(this._options);
          var optionsFilter = options != null ? _filterInstanceProperty(options) : null;
          var filter;
          if (optionsFilter) {
            if (defaultFilter) {
              filter = function filter(item) {
                return defaultFilter(item) && optionsFilter(item);
              };
            } else {
              filter = optionsFilter;
            }
          } else {
            filter = defaultFilter;
          }
          return this._data.getIds({
            filter: filter,
            order: options && options.order
          });
        } else {
          return [];
        }
      }
      
    }, {
      key: "forEach",
      value: function forEach(callback, options) {
        if (this._data) {
          var _context2;
          var defaultFilter = _filterInstanceProperty(this._options);
          var optionsFilter = options && _filterInstanceProperty(options);
          var filter;
          if (optionsFilter) {
            if (defaultFilter) {
              filter = function filter(item) {
                return defaultFilter(item) && optionsFilter(item);
              };
            } else {
              filter = optionsFilter;
            }
          } else {
            filter = defaultFilter;
          }
          _forEachInstanceProperty(_context2 = this._data).call(_context2, callback, {
            filter: filter,
            order: options && options.order
          });
        }
      }
      
    }, {
      key: "map",
      value: function map(callback, options) {
        if (this._data) {
          var _context3;
          var defaultFilter = _filterInstanceProperty(this._options);
          var optionsFilter = options && _filterInstanceProperty(options);
          var filter;
          if (optionsFilter) {
            if (defaultFilter) {
              filter = function filter(item) {
                return defaultFilter(item) && optionsFilter(item);
              };
            } else {
              filter = optionsFilter;
            }
          } else {
            filter = defaultFilter;
          }
          return _mapInstanceProperty(_context3 = this._data).call(_context3, callback, {
            filter: filter,
            order: options && options.order
          });
        } else {
          return [];
        }
      }
      
    }, {
      key: "getDataSet",
      value: function getDataSet() {
        return this._data.getDataSet();
      }
      
    }, {
      key: "stream",
      value: function stream(ids) {
        var _context4;
        return this._data.stream(ids || _defineProperty({}, _Symbol$iterator$1, _bindInstanceProperty$1(_context4 = _keysInstanceProperty(this._ids)).call(_context4, this._ids)));
      }
      
    }, {
      key: "dispose",
      value: function dispose() {
        var _this$_data;
        if ((_this$_data = this._data) !== null && _this$_data !== void 0 && _this$_data.off) {
          this._data.off("*", this._listener);
        }
        var message = "This data view has already been disposed of.";
        var replacement = {
          get: function get() {
            throw new Error(message);
          },
          set: function set() {
            throw new Error(message);
          },
          configurable: false
        };
        var _iterator = _createForOfIteratorHelper(_Reflect$ownKeys(DataView.prototype)),
          _step;
        try {
          for (_iterator.s(); !(_step = _iterator.n()).done;) {
            var key = _step.value;
            _Object$defineProperty(this, key, replacement);
          }
        } catch (err) {
          _iterator.e(err);
        } finally {
          _iterator.f();
        }
      }
      
    }, {
      key: "_onEvent",
      value: function _onEvent(event, params, senderId) {
        if (!params || !params.items || !this._data) {
          return;
        }
        var ids = params.items;
        var addedIds = [];
        var updatedIds = [];
        var removedIds = [];
        var oldItems = [];
        var updatedItems = [];
        var removedItems = [];
        switch (event) {
          case "add":
            
            for (var i = 0, len = ids.length; i < len; i++) {
              var id = ids[i];
              var item = this.get(id);
              if (item) {
                this._ids.add(id);
                addedIds.push(id);
              }
            }
            break;
          case "update":
            
            
            for (var _i2 = 0, _len2 = ids.length; _i2 < _len2; _i2++) {
              var _id2 = ids[_i2];
              var _item = this.get(_id2);
              if (_item) {
                if (this._ids.has(_id2)) {
                  updatedIds.push(_id2);
                  updatedItems.push(params.data[_i2]);
                  oldItems.push(params.oldData[_i2]);
                } else {
                  this._ids.add(_id2);
                  addedIds.push(_id2);
                }
              } else {
                if (this._ids.has(_id2)) {
                  this._ids.delete(_id2);
                  removedIds.push(_id2);
                  removedItems.push(params.oldData[_i2]);
                }
              }
            }
            break;
          case "remove":
            
            for (var _i3 = 0, _len3 = ids.length; _i3 < _len3; _i3++) {
              var _id3 = ids[_i3];
              if (this._ids.has(_id3)) {
                this._ids.delete(_id3);
                removedIds.push(_id3);
                removedItems.push(params.oldData[_i3]);
              }
            }
            break;
        }
        this.length += addedIds.length - removedIds.length;
        if (addedIds.length) {
          this._trigger("add", {
            items: addedIds
          }, senderId);
        }
        if (updatedIds.length) {
          this._trigger("update", {
            items: updatedIds,
            oldData: oldItems,
            data: updatedItems
          }, senderId);
        }
        if (removedIds.length) {
          this._trigger("remove", {
            items: removedIds,
            oldData: removedItems
          }, senderId);
        }
      }
    }]);
    return DataView;
  }(DataSetPart);

  
  function isDataSetLike(idProp, v) {
    return _typeof$1(v) === "object" && v !== null && idProp === v.idProp && typeof v.add === "function" && typeof v.clear === "function" && typeof v.distinct === "function" && typeof _forEachInstanceProperty(v) === "function" && typeof v.get === "function" && typeof v.getDataSet === "function" && typeof v.getIds === "function" && typeof v.length === "number" && typeof _mapInstanceProperty(v) === "function" && typeof v.max === "function" && typeof v.min === "function" && typeof v.off === "function" && typeof v.on === "function" && typeof v.remove === "function" && typeof v.setOptions === "function" && typeof v.stream === "function" && typeof v.update === "function" && typeof v.updateOnly === "function";
  }

  
  function isDataViewLike(idProp, v) {
    return _typeof$1(v) === "object" && v !== null && idProp === v.idProp && typeof _forEachInstanceProperty(v) === "function" && typeof v.get === "function" && typeof v.getDataSet === "function" && typeof v.getIds === "function" && typeof v.length === "number" && typeof _mapInstanceProperty(v) === "function" && typeof v.off === "function" && typeof v.on === "function" && typeof v.stream === "function" && isDataSetLike(idProp, v.getDataSet());
  }

  exports.DELETE = DELETE;
  exports.DataSet = DataSet;
  exports.DataStream = DataStream;
  exports.DataView = DataView;
  exports.Queue = Queue;
  exports.createNewDataPipeFrom = createNewDataPipeFrom;
  exports.isDataSetLike = isDataSetLike;
  exports.isDataViewLike = isDataViewLike;

}));

