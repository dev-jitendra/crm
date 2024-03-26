

(function (global, factory) {
	typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports, require('moment'), require('vis-data/peer/umd/vis-data.js')) :
	typeof define === 'function' && define.amd ? define(['exports', 'moment', 'vis-data/peer/umd/vis-data.js'], factory) :
	(global = typeof globalThis !== 'undefined' ? globalThis : global || self, factory(global.vis = global.vis || {}, global.moment, global.vis));
})(this, (function (exports, moment$3, esnext) {
	var commonjsGlobal = typeof globalThis !== 'undefined' ? globalThis : typeof window !== 'undefined' ? window : typeof global !== 'undefined' ? global : typeof self !== 'undefined' ? self : {};

	function getDefaultExportFromCjs (x) {
		return x && x.__esModule && Object.prototype.hasOwnProperty.call(x, 'default') ? x['default'] : x;
	}

	var constructExports = {};
	var construct$4 = {
	  get exports(){ return constructExports; },
	  set exports(v){ constructExports = v; },
	};

	var check = function (it) {
	  return it && it.Math == Math && it;
	};

	
	var global$j =
	  
	  check(typeof globalThis == 'object' && globalThis) ||
	  check(typeof window == 'object' && window) ||
	  
	  check(typeof self == 'object' && self) ||
	  check(typeof commonjsGlobal == 'object' && commonjsGlobal) ||
	  
	  (function () { return this; })() || Function('return this')();

	var fails$u = function (exec) {
	  try {
	    return !!exec();
	  } catch (error) {
	    return true;
	  }
	};

	var fails$t = fails$u;

	var functionBindNative = !fails$t(function () {
	  
	  var test = (function () {  }).bind();
	  
	  return typeof test != 'function' || test.hasOwnProperty('prototype');
	});

	var NATIVE_BIND$4 = functionBindNative;

	var FunctionPrototype$3 = Function.prototype;
	var apply$5 = FunctionPrototype$3.apply;
	var call$e = FunctionPrototype$3.call;

	
	var functionApply = typeof Reflect == 'object' && Reflect.apply || (NATIVE_BIND$4 ? call$e.bind(apply$5) : function () {
	  return call$e.apply(apply$5, arguments);
	});

	var NATIVE_BIND$3 = functionBindNative;

	var FunctionPrototype$2 = Function.prototype;
	var call$d = FunctionPrototype$2.call;
	var uncurryThisWithBind = NATIVE_BIND$3 && FunctionPrototype$2.bind.bind(call$d, call$d);

	var functionUncurryThis = NATIVE_BIND$3 ? uncurryThisWithBind : function (fn) {
	  return function () {
	    return call$d.apply(fn, arguments);
	  };
	};

	var uncurryThis$w = functionUncurryThis;

	var toString$d = uncurryThis$w({}.toString);
	var stringSlice$1 = uncurryThis$w(''.slice);

	var classofRaw$2 = function (it) {
	  return stringSlice$1(toString$d(it), 8, -1);
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

	
	
	var isCallable$i = $documentAll$1.IS_HTMLDDA ? function (argument) {
	  return typeof argument == 'function' || argument === documentAll$1;
	} : function (argument) {
	  return typeof argument == 'function';
	};

	var objectGetOwnPropertyDescriptor = {};

	var fails$s = fails$u;

	
	var descriptors = !fails$s(function () {
	  
	  return Object.defineProperty({}, 1, { get: function () { return 7; } })[1] != 7;
	});

	var NATIVE_BIND$2 = functionBindNative;

	var call$c = Function.prototype.call;

	var functionCall = NATIVE_BIND$2 ? call$c.bind(call$c) : function () {
	  return call$c.apply(call$c, arguments);
	};

	var objectPropertyIsEnumerable = {};

	var $propertyIsEnumerable$2 = {}.propertyIsEnumerable;
	
	var getOwnPropertyDescriptor$6 = Object.getOwnPropertyDescriptor;

	
	var NASHORN_BUG = getOwnPropertyDescriptor$6 && !$propertyIsEnumerable$2.call({ 1: 2 }, 1);

	
	
	objectPropertyIsEnumerable.f = NASHORN_BUG ? function propertyIsEnumerable(V) {
	  var descriptor = getOwnPropertyDescriptor$6(this, V);
	  return !!descriptor && descriptor.enumerable;
	} : $propertyIsEnumerable$2;

	var createPropertyDescriptor$5 = function (bitmap, value) {
	  return {
	    enumerable: !(bitmap & 1),
	    configurable: !(bitmap & 2),
	    writable: !(bitmap & 4),
	    value: value
	  };
	};

	var uncurryThis$u = functionUncurryThis;
	var fails$r = fails$u;
	var classof$d = classofRaw$2;

	var $Object$4 = Object;
	var split = uncurryThis$u(''.split);

	
	var indexedObject = fails$r(function () {
	  
	  
	  return !$Object$4('z').propertyIsEnumerable(0);
	}) ? function (it) {
	  return classof$d(it) == 'String' ? split(it, '') : $Object$4(it);
	} : $Object$4;

	
	
	var isNullOrUndefined$4 = function (it) {
	  return it === null || it === undefined;
	};

	var isNullOrUndefined$3 = isNullOrUndefined$4;

	var $TypeError$g = TypeError;

	
	
	var requireObjectCoercible$6 = function (it) {
	  if (isNullOrUndefined$3(it)) throw $TypeError$g("Can't call method on " + it);
	  return it;
	};

	
	var IndexedObject$3 = indexedObject;
	var requireObjectCoercible$5 = requireObjectCoercible$6;

	var toIndexedObject$b = function (it) {
	  return IndexedObject$3(requireObjectCoercible$5(it));
	};

	var isCallable$h = isCallable$i;
	var $documentAll = documentAll_1;

	var documentAll = $documentAll.all;

	var isObject$g = $documentAll.IS_HTMLDDA ? function (it) {
	  return typeof it == 'object' ? it !== null : isCallable$h(it) || it === documentAll;
	} : function (it) {
	  return typeof it == 'object' ? it !== null : isCallable$h(it);
	};

	var path$r = {};

	var path$q = path$r;
	var global$i = global$j;
	var isCallable$g = isCallable$i;

	var aFunction = function (variable) {
	  return isCallable$g(variable) ? variable : undefined;
	};

	var getBuiltIn$c = function (namespace, method) {
	  return arguments.length < 2 ? aFunction(path$q[namespace]) || aFunction(global$i[namespace])
	    : path$q[namespace] && path$q[namespace][method] || global$i[namespace] && global$i[namespace][method];
	};

	var uncurryThis$t = functionUncurryThis;

	var objectIsPrototypeOf = uncurryThis$t({}.isPrototypeOf);

	var engineUserAgent = typeof navigator != 'undefined' && String(navigator.userAgent) || '';

	var global$h = global$j;
	var userAgent$2 = engineUserAgent;

	var process$1 = global$h.process;
	var Deno = global$h.Deno;
	var versions = process$1 && process$1.versions || Deno && Deno.version;
	var v8 = versions && versions.v8;
	var match, version;

	if (v8) {
	  match = v8.split('.');
	  
	  
	  version = match[0] > 0 && match[0] < 4 ? 1 : +(match[0] + match[1]);
	}

	
	
	if (!version && userAgent$2) {
	  match = userAgent$2.match(/Edge\/(\d+)/);
	  if (!match || match[1] >= 74) {
	    match = userAgent$2.match(/Chrome\/(\d+)/);
	    if (match) version = +match[1];
	  }
	}

	var engineV8Version = version;

	

	var V8_VERSION$2 = engineV8Version;
	var fails$q = fails$u;

	
	var symbolConstructorDetection = !!Object.getOwnPropertySymbols && !fails$q(function () {
	  var symbol = Symbol();
	  
	  
	  return !String(symbol) || !(Object(symbol) instanceof Symbol) ||
	    
	    !Symbol.sham && V8_VERSION$2 && V8_VERSION$2 < 41;
	});

	

	var NATIVE_SYMBOL$5 = symbolConstructorDetection;

	var useSymbolAsUid = NATIVE_SYMBOL$5
	  && !Symbol.sham
	  && typeof Symbol.iterator == 'symbol';

	var getBuiltIn$b = getBuiltIn$c;
	var isCallable$f = isCallable$i;
	var isPrototypeOf$m = objectIsPrototypeOf;
	var USE_SYMBOL_AS_UID$1 = useSymbolAsUid;

	var $Object$3 = Object;

	var isSymbol$5 = USE_SYMBOL_AS_UID$1 ? function (it) {
	  return typeof it == 'symbol';
	} : function (it) {
	  var $Symbol = getBuiltIn$b('Symbol');
	  return isCallable$f($Symbol) && isPrototypeOf$m($Symbol.prototype, $Object$3(it));
	};

	var $String$4 = String;

	var tryToString$6 = function (argument) {
	  try {
	    return $String$4(argument);
	  } catch (error) {
	    return 'Object';
	  }
	};

	var isCallable$e = isCallable$i;
	var tryToString$5 = tryToString$6;

	var $TypeError$f = TypeError;

	
	var aCallable$7 = function (argument) {
	  if (isCallable$e(argument)) return argument;
	  throw $TypeError$f(tryToString$5(argument) + ' is not a function');
	};

	var aCallable$6 = aCallable$7;
	var isNullOrUndefined$2 = isNullOrUndefined$4;

	
	
	var getMethod$3 = function (V, P) {
	  var func = V[P];
	  return isNullOrUndefined$2(func) ? undefined : aCallable$6(func);
	};

	var call$b = functionCall;
	var isCallable$d = isCallable$i;
	var isObject$f = isObject$g;

	var $TypeError$e = TypeError;

	
	
	var ordinaryToPrimitive$1 = function (input, pref) {
	  var fn, val;
	  if (pref === 'string' && isCallable$d(fn = input.toString) && !isObject$f(val = call$b(fn, input))) return val;
	  if (isCallable$d(fn = input.valueOf) && !isObject$f(val = call$b(fn, input))) return val;
	  if (pref !== 'string' && isCallable$d(fn = input.toString) && !isObject$f(val = call$b(fn, input))) return val;
	  throw $TypeError$e("Can't convert object to primitive value");
	};

	var sharedExports = {};
	var shared$7 = {
	  get exports(){ return sharedExports; },
	  set exports(v){ sharedExports = v; },
	};

	var global$g = global$j;

	
	var defineProperty$f = Object.defineProperty;

	var defineGlobalProperty$1 = function (key, value) {
	  try {
	    defineProperty$f(global$g, key, { value: value, configurable: true, writable: true });
	  } catch (error) {
	    global$g[key] = value;
	  } return value;
	};

	var global$f = global$j;
	var defineGlobalProperty = defineGlobalProperty$1;

	var SHARED = '__core-js_shared__';
	var store$3 = global$f[SHARED] || defineGlobalProperty(SHARED, {});

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

	var requireObjectCoercible$4 = requireObjectCoercible$6;

	var $Object$2 = Object;

	
	
	var toObject$d = function (argument) {
	  return $Object$2(requireObjectCoercible$4(argument));
	};

	var uncurryThis$s = functionUncurryThis;
	var toObject$c = toObject$d;

	var hasOwnProperty = uncurryThis$s({}.hasOwnProperty);

	
	
	
	var hasOwnProperty_1 = Object.hasOwn || function hasOwn(it, key) {
	  return hasOwnProperty(toObject$c(it), key);
	};

	var uncurryThis$r = functionUncurryThis;

	var id$1 = 0;
	var postfix = Math.random();
	var toString$c = uncurryThis$r(1.0.toString);

	var uid$4 = function (key) {
	  return 'Symbol(' + (key === undefined ? '' : key) + ')_' + toString$c(++id$1 + postfix, 36);
	};

	var global$e = global$j;
	var shared$6 = sharedExports;
	var hasOwn$e = hasOwnProperty_1;
	var uid$3 = uid$4;
	var NATIVE_SYMBOL$4 = symbolConstructorDetection;
	var USE_SYMBOL_AS_UID = useSymbolAsUid;

	var Symbol$5 = global$e.Symbol;
	var WellKnownSymbolsStore$2 = shared$6('wks');
	var createWellKnownSymbol = USE_SYMBOL_AS_UID ? Symbol$5['for'] || Symbol$5 : Symbol$5 && Symbol$5.withoutSetter || uid$3;

	var wellKnownSymbol$l = function (name) {
	  if (!hasOwn$e(WellKnownSymbolsStore$2, name)) {
	    WellKnownSymbolsStore$2[name] = NATIVE_SYMBOL$4 && hasOwn$e(Symbol$5, name)
	      ? Symbol$5[name]
	      : createWellKnownSymbol('Symbol.' + name);
	  } return WellKnownSymbolsStore$2[name];
	};

	var call$a = functionCall;
	var isObject$e = isObject$g;
	var isSymbol$4 = isSymbol$5;
	var getMethod$2 = getMethod$3;
	var ordinaryToPrimitive = ordinaryToPrimitive$1;
	var wellKnownSymbol$k = wellKnownSymbol$l;

	var $TypeError$d = TypeError;
	var TO_PRIMITIVE = wellKnownSymbol$k('toPrimitive');

	
	
	var toPrimitive$7 = function (input, pref) {
	  if (!isObject$e(input) || isSymbol$4(input)) return input;
	  var exoticToPrim = getMethod$2(input, TO_PRIMITIVE);
	  var result;
	  if (exoticToPrim) {
	    if (pref === undefined) pref = 'default';
	    result = call$a(exoticToPrim, input, pref);
	    if (!isObject$e(result) || isSymbol$4(result)) return result;
	    throw $TypeError$d("Can't convert object to primitive value");
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

	var global$d = global$j;
	var isObject$d = isObject$g;

	var document$1 = global$d.document;
	
	var EXISTS$1 = isObject$d(document$1) && isObject$d(document$1.createElement);

	var documentCreateElement$1 = function (it) {
	  return EXISTS$1 ? document$1.createElement(it) : {};
	};

	var DESCRIPTORS$i = descriptors;
	var fails$p = fails$u;
	var createElement = documentCreateElement$1;

	
	var ie8DomDefine = !DESCRIPTORS$i && !fails$p(function () {
	  
	  return Object.defineProperty(createElement('div'), 'a', {
	    get: function () { return 7; }
	  }).a != 7;
	});

	var DESCRIPTORS$h = descriptors;
	var call$9 = functionCall;
	var propertyIsEnumerableModule$2 = objectPropertyIsEnumerable;
	var createPropertyDescriptor$4 = createPropertyDescriptor$5;
	var toIndexedObject$a = toIndexedObject$b;
	var toPropertyKey$3 = toPropertyKey$4;
	var hasOwn$d = hasOwnProperty_1;
	var IE8_DOM_DEFINE$1 = ie8DomDefine;

	
	var $getOwnPropertyDescriptor$2 = Object.getOwnPropertyDescriptor;

	
	
	objectGetOwnPropertyDescriptor.f = DESCRIPTORS$h ? $getOwnPropertyDescriptor$2 : function getOwnPropertyDescriptor(O, P) {
	  O = toIndexedObject$a(O);
	  P = toPropertyKey$3(P);
	  if (IE8_DOM_DEFINE$1) try {
	    return $getOwnPropertyDescriptor$2(O, P);
	  } catch (error) {  }
	  if (hasOwn$d(O, P)) return createPropertyDescriptor$4(!call$9(propertyIsEnumerableModule$2.f, O, P), O[P]);
	};

	var fails$o = fails$u;
	var isCallable$c = isCallable$i;

	var replacement = /#|\.prototype\./;

	var isForced$1 = function (feature, detection) {
	  var value = data[normalize(feature)];
	  return value == POLYFILL ? true
	    : value == NATIVE ? false
	    : isCallable$c(detection) ? fails$o(detection)
	    : !!detection;
	};

	var normalize = isForced$1.normalize = function (string) {
	  return String(string).replace(replacement, '.').toLowerCase();
	};

	var data = isForced$1.data = {};
	var NATIVE = isForced$1.NATIVE = 'N';
	var POLYFILL = isForced$1.POLYFILL = 'P';

	var isForced_1 = isForced$1;

	var uncurryThis$q = functionUncurryThisClause;
	var aCallable$5 = aCallable$7;
	var NATIVE_BIND$1 = functionBindNative;

	var bind$f = uncurryThis$q(uncurryThis$q.bind);

	
	var functionBindContext = function (fn, that) {
	  aCallable$5(fn);
	  return that === undefined ? fn : NATIVE_BIND$1 ? bind$f(fn, that) : function () {
	    return fn.apply(that, arguments);
	  };
	};

	var objectDefineProperty = {};

	var DESCRIPTORS$g = descriptors;
	var fails$n = fails$u;

	
	
	var v8PrototypeDefineBug = DESCRIPTORS$g && fails$n(function () {
	  
	  return Object.defineProperty(function () {  }, 'prototype', {
	    value: 42,
	    writable: false
	  }).prototype != 42;
	});

	var isObject$c = isObject$g;

	var $String$3 = String;
	var $TypeError$c = TypeError;

	
	var anObject$b = function (argument) {
	  if (isObject$c(argument)) return argument;
	  throw $TypeError$c($String$3(argument) + ' is not an object');
	};

	var DESCRIPTORS$f = descriptors;
	var IE8_DOM_DEFINE = ie8DomDefine;
	var V8_PROTOTYPE_DEFINE_BUG$1 = v8PrototypeDefineBug;
	var anObject$a = anObject$b;
	var toPropertyKey$2 = toPropertyKey$4;

	var $TypeError$b = TypeError;
	
	var $defineProperty$1 = Object.defineProperty;
	
	var $getOwnPropertyDescriptor$1 = Object.getOwnPropertyDescriptor;
	var ENUMERABLE = 'enumerable';
	var CONFIGURABLE$1 = 'configurable';
	var WRITABLE = 'writable';

	
	
	objectDefineProperty.f = DESCRIPTORS$f ? V8_PROTOTYPE_DEFINE_BUG$1 ? function defineProperty(O, P, Attributes) {
	  anObject$a(O);
	  P = toPropertyKey$2(P);
	  anObject$a(Attributes);
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
	  anObject$a(O);
	  P = toPropertyKey$2(P);
	  anObject$a(Attributes);
	  if (IE8_DOM_DEFINE) try {
	    return $defineProperty$1(O, P, Attributes);
	  } catch (error) {  }
	  if ('get' in Attributes || 'set' in Attributes) throw $TypeError$b('Accessors not supported');
	  if ('value' in Attributes) O[P] = Attributes.value;
	  return O;
	};

	var DESCRIPTORS$e = descriptors;
	var definePropertyModule$3 = objectDefineProperty;
	var createPropertyDescriptor$3 = createPropertyDescriptor$5;

	var createNonEnumerableProperty$6 = DESCRIPTORS$e ? function (object, key, value) {
	  return definePropertyModule$3.f(object, key, createPropertyDescriptor$3(1, value));
	} : function (object, key, value) {
	  object[key] = value;
	  return object;
	};

	var global$c = global$j;
	var apply$4 = functionApply;
	var uncurryThis$p = functionUncurryThisClause;
	var isCallable$b = isCallable$i;
	var getOwnPropertyDescriptor$5 = objectGetOwnPropertyDescriptor.f;
	var isForced = isForced_1;
	var path$p = path$r;
	var bind$e = functionBindContext;
	var createNonEnumerableProperty$5 = createNonEnumerableProperty$6;
	var hasOwn$c = hasOwnProperty_1;

	var wrapConstructor = function (NativeConstructor) {
	  var Wrapper = function (a, b, c) {
	    if (this instanceof Wrapper) {
	      switch (arguments.length) {
	        case 0: return new NativeConstructor();
	        case 1: return new NativeConstructor(a);
	        case 2: return new NativeConstructor(a, b);
	      } return new NativeConstructor(a, b, c);
	    } return apply$4(NativeConstructor, this, arguments);
	  };
	  Wrapper.prototype = NativeConstructor.prototype;
	  return Wrapper;
	};

	
	var _export = function (options, source) {
	  var TARGET = options.target;
	  var GLOBAL = options.global;
	  var STATIC = options.stat;
	  var PROTO = options.proto;

	  var nativeSource = GLOBAL ? global$c : STATIC ? global$c[TARGET] : (global$c[TARGET] || {}).prototype;

	  var target = GLOBAL ? path$p : path$p[TARGET] || createNonEnumerableProperty$5(path$p, TARGET, {})[TARGET];
	  var targetPrototype = target.prototype;

	  var FORCED, USE_NATIVE, VIRTUAL_PROTOTYPE;
	  var key, sourceProperty, targetProperty, nativeProperty, resultProperty, descriptor;

	  for (key in source) {
	    FORCED = isForced(GLOBAL ? key : TARGET + (STATIC ? '.' : '#') + key, options.forced);
	    
	    USE_NATIVE = !FORCED && nativeSource && hasOwn$c(nativeSource, key);

	    targetProperty = target[key];

	    if (USE_NATIVE) if (options.dontCallGetSet) {
	      descriptor = getOwnPropertyDescriptor$5(nativeSource, key);
	      nativeProperty = descriptor && descriptor.value;
	    } else nativeProperty = nativeSource[key];

	    
	    sourceProperty = (USE_NATIVE && nativeProperty) ? nativeProperty : source[key];

	    if (USE_NATIVE && typeof targetProperty == typeof sourceProperty) continue;

	    
	    if (options.bind && USE_NATIVE) resultProperty = bind$e(sourceProperty, global$c);
	    
	    else if (options.wrap && USE_NATIVE) resultProperty = wrapConstructor(sourceProperty);
	    
	    else if (PROTO && isCallable$b(sourceProperty)) resultProperty = uncurryThis$p(sourceProperty);
	    
	    else resultProperty = sourceProperty;

	    
	    if (options.sham || (sourceProperty && sourceProperty.sham) || (targetProperty && targetProperty.sham)) {
	      createNonEnumerableProperty$5(resultProperty, 'sham', true);
	    }

	    createNonEnumerableProperty$5(target, key, resultProperty);

	    if (PROTO) {
	      VIRTUAL_PROTOTYPE = TARGET + 'Prototype';
	      if (!hasOwn$c(path$p, VIRTUAL_PROTOTYPE)) {
	        createNonEnumerableProperty$5(path$p, VIRTUAL_PROTOTYPE, {});
	      }
	      
	      createNonEnumerableProperty$5(path$p[VIRTUAL_PROTOTYPE], key, sourceProperty);
	      
	      if (options.real && targetPrototype && (FORCED || !targetPrototype[key])) {
	        createNonEnumerableProperty$5(targetPrototype, key, sourceProperty);
	      }
	    }
	  }
	};

	var uncurryThis$o = functionUncurryThis;

	var arraySlice$5 = uncurryThis$o([].slice);

	var uncurryThis$n = functionUncurryThis;
	var aCallable$4 = aCallable$7;
	var isObject$b = isObject$g;
	var hasOwn$b = hasOwnProperty_1;
	var arraySlice$4 = arraySlice$5;
	var NATIVE_BIND = functionBindNative;

	var $Function = Function;
	var concat$6 = uncurryThis$n([].concat);
	var join = uncurryThis$n([].join);
	var factories = {};

	var construct$3 = function (C, argsLength, args) {
	  if (!hasOwn$b(factories, argsLength)) {
	    for (var list = [], i = 0; i < argsLength; i++) list[i] = 'a[' + i + ']';
	    factories[argsLength] = $Function('C,a', 'return new C(' + join(list, ',') + ')');
	  } return factories[argsLength](C, args);
	};

	
	
	
	var functionBind = NATIVE_BIND ? $Function.bind : function bind(that ) {
	  var F = aCallable$4(this);
	  var Prototype = F.prototype;
	  var partArgs = arraySlice$4(arguments, 1);
	  var boundFunction = function bound() {
	    var args = concat$6(partArgs, arraySlice$4(arguments));
	    return this instanceof boundFunction ? construct$3(F, args.length, args) : F.apply(that, args);
	  };
	  if (isObject$b(Prototype)) boundFunction.prototype = Prototype;
	  return boundFunction;
	};

	var wellKnownSymbol$j = wellKnownSymbol$l;

	var TO_STRING_TAG$3 = wellKnownSymbol$j('toStringTag');
	var test$2 = {};

	test$2[TO_STRING_TAG$3] = 'z';

	var toStringTagSupport = String(test$2) === '[object z]';

	var TO_STRING_TAG_SUPPORT$2 = toStringTagSupport;
	var isCallable$a = isCallable$i;
	var classofRaw = classofRaw$2;
	var wellKnownSymbol$i = wellKnownSymbol$l;

	var TO_STRING_TAG$2 = wellKnownSymbol$i('toStringTag');
	var $Object$1 = Object;

	
	var CORRECT_ARGUMENTS = classofRaw(function () { return arguments; }()) == 'Arguments';

	
	var tryGet = function (it, key) {
	  try {
	    return it[key];
	  } catch (error) {  }
	};

	
	var classof$c = TO_STRING_TAG_SUPPORT$2 ? classofRaw : function (it) {
	  var O, tag, result;
	  return it === undefined ? 'Undefined' : it === null ? 'Null'
	    
	    : typeof (tag = tryGet(O = $Object$1(it), TO_STRING_TAG$2)) == 'string' ? tag
	    
	    : CORRECT_ARGUMENTS ? classofRaw(O)
	    
	    : (result = classofRaw(O)) == 'Object' && isCallable$a(O.callee) ? 'Arguments' : result;
	};

	var uncurryThis$m = functionUncurryThis;
	var isCallable$9 = isCallable$i;
	var store$1 = sharedStore;

	var functionToString = uncurryThis$m(Function.toString);

	
	if (!isCallable$9(store$1.inspectSource)) {
	  store$1.inspectSource = function (it) {
	    return functionToString(it);
	  };
	}

	var inspectSource$1 = store$1.inspectSource;

	var uncurryThis$l = functionUncurryThis;
	var fails$m = fails$u;
	var isCallable$8 = isCallable$i;
	var classof$b = classof$c;
	var getBuiltIn$a = getBuiltIn$c;
	var inspectSource = inspectSource$1;

	var noop = function () {  };
	var empty = [];
	var construct$2 = getBuiltIn$a('Reflect', 'construct');
	var constructorRegExp = /^\s*(?:class|function)\b/;
	var exec$2 = uncurryThis$l(constructorRegExp.exec);
	var INCORRECT_TO_STRING = !constructorRegExp.exec(noop);

	var isConstructorModern = function isConstructor(argument) {
	  if (!isCallable$8(argument)) return false;
	  try {
	    construct$2(noop, empty, argument);
	    return true;
	  } catch (error) {
	    return false;
	  }
	};

	var isConstructorLegacy = function isConstructor(argument) {
	  if (!isCallable$8(argument)) return false;
	  switch (classof$b(argument)) {
	    case 'AsyncFunction':
	    case 'GeneratorFunction':
	    case 'AsyncGeneratorFunction': return false;
	  }
	  try {
	    
	    
	    
	    return INCORRECT_TO_STRING || !!exec$2(constructorRegExp, inspectSource(argument));
	  } catch (error) {
	    return true;
	  }
	};

	isConstructorLegacy.sham = true;

	
	
	var isConstructor$4 = !construct$2 || fails$m(function () {
	  var called;
	  return isConstructorModern(isConstructorModern.call)
	    || !isConstructorModern(Object)
	    || !isConstructorModern(function () { called = true; })
	    || called;
	}) ? isConstructorLegacy : isConstructorModern;

	var isConstructor$3 = isConstructor$4;
	var tryToString$4 = tryToString$6;

	var $TypeError$a = TypeError;

	
	var aConstructor$1 = function (argument) {
	  if (isConstructor$3(argument)) return argument;
	  throw $TypeError$a(tryToString$4(argument) + ' is not a constructor');
	};

	var objectDefineProperties = {};

	var ceil = Math.ceil;
	var floor$1 = Math.floor;

	
	
	
	var mathTrunc = Math.trunc || function trunc(x) {
	  var n = +x;
	  return (n > 0 ? floor$1 : ceil)(n);
	};

	var trunc = mathTrunc;

	
	
	var toIntegerOrInfinity$5 = function (argument) {
	  var number = +argument;
	  
	  return number !== number || number === 0 ? 0 : trunc(number);
	};

	var toIntegerOrInfinity$4 = toIntegerOrInfinity$5;

	var max$3 = Math.max;
	var min$2 = Math.min;

	
	
	
	var toAbsoluteIndex$5 = function (index, length) {
	  var integer = toIntegerOrInfinity$4(index);
	  return integer < 0 ? max$3(integer + length, 0) : min$2(integer, length);
	};

	var toIntegerOrInfinity$3 = toIntegerOrInfinity$5;

	var min$1 = Math.min;

	
	
	var toLength$1 = function (argument) {
	  return argument > 0 ? min$1(toIntegerOrInfinity$3(argument), 0x1FFFFFFFFFFFFF) : 0; 
	};

	var toLength = toLength$1;

	
	
	var lengthOfArrayLike$b = function (obj) {
	  return toLength(obj.length);
	};

	var toIndexedObject$9 = toIndexedObject$b;
	var toAbsoluteIndex$4 = toAbsoluteIndex$5;
	var lengthOfArrayLike$a = lengthOfArrayLike$b;

	
	var createMethod$5 = function (IS_INCLUDES) {
	  return function ($this, el, fromIndex) {
	    var O = toIndexedObject$9($this);
	    var length = lengthOfArrayLike$a(O);
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

	var uncurryThis$k = functionUncurryThis;
	var hasOwn$a = hasOwnProperty_1;
	var toIndexedObject$8 = toIndexedObject$b;
	var indexOf$4 = arrayIncludes.indexOf;
	var hiddenKeys$5 = hiddenKeys$6;

	var push$6 = uncurryThis$k([].push);

	var objectKeysInternal = function (object, names) {
	  var O = toIndexedObject$8(object);
	  var i = 0;
	  var result = [];
	  var key;
	  for (key in O) !hasOwn$a(hiddenKeys$5, key) && hasOwn$a(O, key) && push$6(result, key);
	  
	  while (names.length > i) if (hasOwn$a(O, key = names[i++])) {
	    ~indexOf$4(result, key) || push$6(result, key);
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

	var DESCRIPTORS$d = descriptors;
	var V8_PROTOTYPE_DEFINE_BUG = v8PrototypeDefineBug;
	var definePropertyModule$2 = objectDefineProperty;
	var anObject$9 = anObject$b;
	var toIndexedObject$7 = toIndexedObject$b;
	var objectKeys$3 = objectKeys$4;

	
	
	
	objectDefineProperties.f = DESCRIPTORS$d && !V8_PROTOTYPE_DEFINE_BUG ? Object.defineProperties : function defineProperties(O, Properties) {
	  anObject$9(O);
	  var props = toIndexedObject$7(Properties);
	  var keys = objectKeys$3(Properties);
	  var length = keys.length;
	  var index = 0;
	  var key;
	  while (length > index) definePropertyModule$2.f(O, key = keys[index++], props[key]);
	  return O;
	};

	var getBuiltIn$9 = getBuiltIn$c;

	var html$1 = getBuiltIn$9('document', 'documentElement');

	var shared$5 = sharedExports;
	var uid$2 = uid$4;

	var keys$3 = shared$5('keys');

	var sharedKey$4 = function (key) {
	  return keys$3[key] || (keys$3[key] = uid$2(key));
	};

	

	var anObject$8 = anObject$b;
	var definePropertiesModule$1 = objectDefineProperties;
	var enumBugKeys$1 = enumBugKeys$3;
	var hiddenKeys$4 = hiddenKeys$6;
	var html = html$1;
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
	  html.appendChild(iframe);
	  
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
	    EmptyConstructor[PROTOTYPE$1] = anObject$8(O);
	    result = new EmptyConstructor();
	    EmptyConstructor[PROTOTYPE$1] = null;
	    
	    result[IE_PROTO$1] = O;
	  } else result = NullProtoObject();
	  return Properties === undefined ? result : definePropertiesModule$1.f(result, Properties);
	};

	var $$M = _export;
	var getBuiltIn$8 = getBuiltIn$c;
	var apply$3 = functionApply;
	var bind$d = functionBind;
	var aConstructor = aConstructor$1;
	var anObject$7 = anObject$b;
	var isObject$a = isObject$g;
	var create$b = objectCreate;
	var fails$l = fails$u;

	var nativeConstruct = getBuiltIn$8('Reflect', 'construct');
	var ObjectPrototype$2 = Object.prototype;
	var push$5 = [].push;

	
	
	
	
	var NEW_TARGET_BUG = fails$l(function () {
	  function F() {  }
	  return !(nativeConstruct(function () {  }, [], F) instanceof F);
	});

	var ARGS_BUG = !fails$l(function () {
	  nativeConstruct(function () {  });
	});

	var FORCED$8 = NEW_TARGET_BUG || ARGS_BUG;

	$$M({ target: 'Reflect', stat: true, forced: FORCED$8, sham: FORCED$8 }, {
	  construct: function construct(Target, args ) {
	    aConstructor(Target);
	    anObject$7(args);
	    var newTarget = arguments.length < 3 ? Target : aConstructor(arguments[2]);
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
	      apply$3(push$5, $args, args);
	      return new (apply$3(bind$d, Target, $args))();
	    }
	    
	    var proto = newTarget.prototype;
	    var instance = create$b(isObject$a(proto) ? proto : ObjectPrototype$2);
	    var result = apply$3(Target, instance, args);
	    return isObject$a(result) ? result : instance;
	  }
	});

	var path$o = path$r;

	var construct$1 = path$o.Reflect.construct;

	var parent$12 = construct$1;

	var construct = parent$12;

	(function (module) {
		module.exports = construct;
	} (construct$4));

	var _Reflect$construct = getDefaultExportFromCjs(constructExports);

	function _classCallCheck(instance, Constructor) {
	  if (!(instance instanceof Constructor)) {
	    throw new TypeError("Cannot call a class as a function");
	  }
	}

	var definePropertyExports$3 = {};
	var defineProperty$e = {
	  get exports(){ return definePropertyExports$3; },
	  set exports(v){ definePropertyExports$3 = v; },
	};

	var definePropertyExports$2 = {};
	var defineProperty$d = {
	  get exports(){ return definePropertyExports$2; },
	  set exports(v){ definePropertyExports$2 = v; },
	};

	var definePropertyExports$1 = {};
	var defineProperty$c = {
	  get exports(){ return definePropertyExports$1; },
	  set exports(v){ definePropertyExports$1 = v; },
	};

	var $$L = _export;
	var DESCRIPTORS$c = descriptors;
	var defineProperty$b = objectDefineProperty.f;

	
	
	
	$$L({ target: 'Object', stat: true, forced: Object.defineProperty !== defineProperty$b, sham: !DESCRIPTORS$c }, {
	  defineProperty: defineProperty$b
	});

	var path$n = path$r;

	var Object$4 = path$n.Object;

	var defineProperty$a = defineProperty$c.exports = function defineProperty(it, key, desc) {
	  return Object$4.defineProperty(it, key, desc);
	};

	if (Object$4.defineProperty.sham) defineProperty$a.sham = true;

	var parent$11 = definePropertyExports$1;

	var defineProperty$9 = parent$11;

	var parent$10 = defineProperty$9;

	var defineProperty$8 = parent$10;

	var parent$$ = defineProperty$8;

	var defineProperty$7 = parent$$;

	(function (module) {
		module.exports = defineProperty$7;
	} (defineProperty$d));

	(function (module) {
		module.exports = definePropertyExports$2;
	} (defineProperty$e));

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

	var classof$a = classofRaw$2;

	
	
	
	var isArray$e = Array.isArray || function isArray(argument) {
	  return classof$a(argument) == 'Array';
	};

	var $TypeError$9 = TypeError;
	var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF; 

	var doesNotExceedSafeInteger$2 = function (it) {
	  if (it > MAX_SAFE_INTEGER) throw $TypeError$9('Maximum allowed index exceeded');
	  return it;
	};

	var toPropertyKey$1 = toPropertyKey$4;
	var definePropertyModule$1 = objectDefineProperty;
	var createPropertyDescriptor$2 = createPropertyDescriptor$5;

	var createProperty$6 = function (object, key, value) {
	  var propertyKey = toPropertyKey$1(key);
	  if (propertyKey in object) definePropertyModule$1.f(object, propertyKey, createPropertyDescriptor$2(0, value));
	  else object[propertyKey] = value;
	};

	var isArray$d = isArray$e;
	var isConstructor$2 = isConstructor$4;
	var isObject$9 = isObject$g;
	var wellKnownSymbol$h = wellKnownSymbol$l;

	var SPECIES$3 = wellKnownSymbol$h('species');
	var $Array$3 = Array;

	
	
	var arraySpeciesConstructor$1 = function (originalArray) {
	  var C;
	  if (isArray$d(originalArray)) {
	    C = originalArray.constructor;
	    
	    if (isConstructor$2(C) && (C === $Array$3 || isArray$d(C.prototype))) C = undefined;
	    else if (isObject$9(C)) {
	      C = C[SPECIES$3];
	      if (C === null) C = undefined;
	    }
	  } return C === undefined ? $Array$3 : C;
	};

	var arraySpeciesConstructor = arraySpeciesConstructor$1;

	
	
	var arraySpeciesCreate$3 = function (originalArray, length) {
	  return new (arraySpeciesConstructor(originalArray))(length === 0 ? 0 : length);
	};

	var fails$k = fails$u;
	var wellKnownSymbol$g = wellKnownSymbol$l;
	var V8_VERSION$1 = engineV8Version;

	var SPECIES$2 = wellKnownSymbol$g('species');

	var arrayMethodHasSpeciesSupport$5 = function (METHOD_NAME) {
	  
	  
	  
	  return V8_VERSION$1 >= 51 || !fails$k(function () {
	    var array = [];
	    var constructor = array.constructor = {};
	    constructor[SPECIES$2] = function () {
	      return { foo: 1 };
	    };
	    return array[METHOD_NAME](Boolean).foo !== 1;
	  });
	};

	var $$K = _export;
	var fails$j = fails$u;
	var isArray$c = isArray$e;
	var isObject$8 = isObject$g;
	var toObject$b = toObject$d;
	var lengthOfArrayLike$9 = lengthOfArrayLike$b;
	var doesNotExceedSafeInteger$1 = doesNotExceedSafeInteger$2;
	var createProperty$5 = createProperty$6;
	var arraySpeciesCreate$2 = arraySpeciesCreate$3;
	var arrayMethodHasSpeciesSupport$4 = arrayMethodHasSpeciesSupport$5;
	var wellKnownSymbol$f = wellKnownSymbol$l;
	var V8_VERSION = engineV8Version;

	var IS_CONCAT_SPREADABLE = wellKnownSymbol$f('isConcatSpreadable');

	
	
	
	var IS_CONCAT_SPREADABLE_SUPPORT = V8_VERSION >= 51 || !fails$j(function () {
	  var array = [];
	  array[IS_CONCAT_SPREADABLE] = false;
	  return array.concat()[0] !== array;
	});

	var isConcatSpreadable = function (O) {
	  if (!isObject$8(O)) return false;
	  var spreadable = O[IS_CONCAT_SPREADABLE];
	  return spreadable !== undefined ? !!spreadable : isArray$c(O);
	};

	var FORCED$7 = !IS_CONCAT_SPREADABLE_SUPPORT || !arrayMethodHasSpeciesSupport$4('concat');

	
	
	
	$$K({ target: 'Array', proto: true, arity: 1, forced: FORCED$7 }, {
	  
	  concat: function concat(arg) {
	    var O = toObject$b(this);
	    var A = arraySpeciesCreate$2(O, 0);
	    var n = 0;
	    var i, k, length, len, E;
	    for (i = -1, length = arguments.length; i < length; i++) {
	      E = i === -1 ? O : arguments[i];
	      if (isConcatSpreadable(E)) {
	        len = lengthOfArrayLike$9(E);
	        doesNotExceedSafeInteger$1(n + len);
	        for (k = 0; k < len; k++, n++) if (k in E) createProperty$5(A, n, E[k]);
	      } else {
	        doesNotExceedSafeInteger$1(n + 1);
	        createProperty$5(A, n++, E);
	      }
	    }
	    A.length = n;
	    return A;
	  }
	});

	var classof$9 = classof$c;

	var $String$2 = String;

	var toString$b = function (argument) {
	  if (classof$9(argument) === 'Symbol') throw TypeError('Cannot convert a Symbol value to a string');
	  return $String$2(argument);
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
	var lengthOfArrayLike$8 = lengthOfArrayLike$b;
	var createProperty$4 = createProperty$6;

	var $Array$2 = Array;
	var max$2 = Math.max;

	var arraySliceSimple = function (O, start, end) {
	  var length = lengthOfArrayLike$8(O);
	  var k = toAbsoluteIndex$3(start, length);
	  var fin = toAbsoluteIndex$3(end === undefined ? length : end, length);
	  var result = $Array$2(max$2(fin - k, 0));
	  for (var n = 0; k < fin; k++, n++) createProperty$4(result, n, O[k]);
	  result.length = n;
	  return result;
	};

	

	var classof$8 = classofRaw$2;
	var toIndexedObject$6 = toIndexedObject$b;
	var $getOwnPropertyNames$1 = objectGetOwnPropertyNames.f;
	var arraySlice$3 = arraySliceSimple;

	var windowNames = typeof window == 'object' && window && Object.getOwnPropertyNames
	  ? Object.getOwnPropertyNames(window) : [];

	var getWindowNames = function (it) {
	  try {
	    return $getOwnPropertyNames$1(it);
	  } catch (error) {
	    return arraySlice$3(windowNames);
	  }
	};

	
	objectGetOwnPropertyNamesExternal.f = function getOwnPropertyNames(it) {
	  return windowNames && classof$8(it) == 'Window'
	    ? getWindowNames(it)
	    : $getOwnPropertyNames$1(toIndexedObject$6(it));
	};

	var objectGetOwnPropertySymbols = {};

	
	objectGetOwnPropertySymbols.f = Object.getOwnPropertySymbols;

	var createNonEnumerableProperty$4 = createNonEnumerableProperty$6;

	var defineBuiltIn$5 = function (target, key, value, options) {
	  if (options && options.enumerable) target[key] = value;
	  else createNonEnumerableProperty$4(target, key, value);
	  return target;
	};

	var defineProperty$6 = objectDefineProperty;

	var defineBuiltInAccessor$3 = function (target, name, descriptor) {
	  return defineProperty$6.f(target, name, descriptor);
	};

	var wellKnownSymbolWrapped = {};

	var wellKnownSymbol$e = wellKnownSymbol$l;

	wellKnownSymbolWrapped.f = wellKnownSymbol$e;

	var path$m = path$r;
	var hasOwn$9 = hasOwnProperty_1;
	var wrappedWellKnownSymbolModule$1 = wellKnownSymbolWrapped;
	var defineProperty$5 = objectDefineProperty.f;

	var wellKnownSymbolDefine = function (NAME) {
	  var Symbol = path$m.Symbol || (path$m.Symbol = {});
	  if (!hasOwn$9(Symbol, NAME)) defineProperty$5(Symbol, NAME, {
	    value: wrappedWellKnownSymbolModule$1.f(NAME)
	  });
	};

	var call$8 = functionCall;
	var getBuiltIn$7 = getBuiltIn$c;
	var wellKnownSymbol$d = wellKnownSymbol$l;
	var defineBuiltIn$4 = defineBuiltIn$5;

	var symbolDefineToPrimitive = function () {
	  var Symbol = getBuiltIn$7('Symbol');
	  var SymbolPrototype = Symbol && Symbol.prototype;
	  var valueOf = SymbolPrototype && SymbolPrototype.valueOf;
	  var TO_PRIMITIVE = wellKnownSymbol$d('toPrimitive');

	  if (SymbolPrototype && !SymbolPrototype[TO_PRIMITIVE]) {
	    
	    
	    
	    defineBuiltIn$4(SymbolPrototype, TO_PRIMITIVE, function (hint) {
	      return call$8(valueOf, this);
	    }, { arity: 1 });
	  }
	};

	var TO_STRING_TAG_SUPPORT$1 = toStringTagSupport;
	var classof$7 = classof$c;

	
	
	var objectToString = TO_STRING_TAG_SUPPORT$1 ? {}.toString : function toString() {
	  return '[object ' + classof$7(this) + ']';
	};

	var TO_STRING_TAG_SUPPORT = toStringTagSupport;
	var defineProperty$4 = objectDefineProperty.f;
	var createNonEnumerableProperty$3 = createNonEnumerableProperty$6;
	var hasOwn$8 = hasOwnProperty_1;
	var toString$a = objectToString;
	var wellKnownSymbol$c = wellKnownSymbol$l;

	var TO_STRING_TAG$1 = wellKnownSymbol$c('toStringTag');

	var setToStringTag$6 = function (it, TAG, STATIC, SET_METHOD) {
	  if (it) {
	    var target = STATIC ? it : it.prototype;
	    if (!hasOwn$8(target, TO_STRING_TAG$1)) {
	      defineProperty$4(target, TO_STRING_TAG$1, { configurable: true, value: TAG });
	    }
	    if (SET_METHOD && !TO_STRING_TAG_SUPPORT) {
	      createNonEnumerableProperty$3(target, 'toString', toString$a);
	    }
	  }
	};

	var global$b = global$j;
	var isCallable$7 = isCallable$i;

	var WeakMap$1 = global$b.WeakMap;

	var weakMapBasicDetection = isCallable$7(WeakMap$1) && /native code/.test(String(WeakMap$1));

	var NATIVE_WEAK_MAP = weakMapBasicDetection;
	var global$a = global$j;
	var isObject$7 = isObject$g;
	var createNonEnumerableProperty$2 = createNonEnumerableProperty$6;
	var hasOwn$7 = hasOwnProperty_1;
	var shared$4 = sharedStore;
	var sharedKey$2 = sharedKey$4;
	var hiddenKeys$2 = hiddenKeys$6;

	var OBJECT_ALREADY_INITIALIZED = 'Object already initialized';
	var TypeError$2 = global$a.TypeError;
	var WeakMap = global$a.WeakMap;
	var set$3, get, has;

	var enforce = function (it) {
	  return has(it) ? get(it) : set$3(it, {});
	};

	var getterFor = function (TYPE) {
	  return function (it) {
	    var state;
	    if (!isObject$7(it) || (state = get(it)).type !== TYPE) {
	      throw TypeError$2('Incompatible receiver, ' + TYPE + ' required');
	    } return state;
	  };
	};

	if (NATIVE_WEAK_MAP || shared$4.state) {
	  var store = shared$4.state || (shared$4.state = new WeakMap());
	  
	  store.get = store.get;
	  store.has = store.has;
	  store.set = store.set;
	  
	  set$3 = function (it, metadata) {
	    if (store.has(it)) throw TypeError$2(OBJECT_ALREADY_INITIALIZED);
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
	  set$3 = function (it, metadata) {
	    if (hasOwn$7(it, STATE)) throw TypeError$2(OBJECT_ALREADY_INITIALIZED);
	    metadata.facade = it;
	    createNonEnumerableProperty$2(it, STATE, metadata);
	    return metadata;
	  };
	  get = function (it) {
	    return hasOwn$7(it, STATE) ? it[STATE] : {};
	  };
	  has = function (it) {
	    return hasOwn$7(it, STATE);
	  };
	}

	var internalState = {
	  set: set$3,
	  get: get,
	  has: has,
	  enforce: enforce,
	  getterFor: getterFor
	};

	var bind$c = functionBindContext;
	var uncurryThis$j = functionUncurryThis;
	var IndexedObject$2 = indexedObject;
	var toObject$a = toObject$d;
	var lengthOfArrayLike$7 = lengthOfArrayLike$b;
	var arraySpeciesCreate$1 = arraySpeciesCreate$3;

	var push$4 = uncurryThis$j([].push);

	
	var createMethod$4 = function (TYPE) {
	  var IS_MAP = TYPE == 1;
	  var IS_FILTER = TYPE == 2;
	  var IS_SOME = TYPE == 3;
	  var IS_EVERY = TYPE == 4;
	  var IS_FIND_INDEX = TYPE == 6;
	  var IS_FILTER_REJECT = TYPE == 7;
	  var NO_HOLES = TYPE == 5 || IS_FIND_INDEX;
	  return function ($this, callbackfn, that, specificCreate) {
	    var O = toObject$a($this);
	    var self = IndexedObject$2(O);
	    var boundFunction = bind$c(callbackfn, that);
	    var length = lengthOfArrayLike$7(self);
	    var index = 0;
	    var create = specificCreate || arraySpeciesCreate$1;
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
	          case 2: push$4(target, value);      
	        } else switch (TYPE) {
	          case 4: return false;             
	          case 7: push$4(target, value);      
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

	var $$J = _export;
	var global$9 = global$j;
	var call$7 = functionCall;
	var uncurryThis$i = functionUncurryThis;
	var DESCRIPTORS$b = descriptors;
	var NATIVE_SYMBOL$3 = symbolConstructorDetection;
	var fails$i = fails$u;
	var hasOwn$6 = hasOwnProperty_1;
	var isPrototypeOf$l = objectIsPrototypeOf;
	var anObject$6 = anObject$b;
	var toIndexedObject$5 = toIndexedObject$b;
	var toPropertyKey = toPropertyKey$4;
	var $toString = toString$b;
	var createPropertyDescriptor$1 = createPropertyDescriptor$5;
	var nativeObjectCreate = objectCreate;
	var objectKeys$2 = objectKeys$4;
	var getOwnPropertyNamesModule$2 = objectGetOwnPropertyNames;
	var getOwnPropertyNamesExternal = objectGetOwnPropertyNamesExternal;
	var getOwnPropertySymbolsModule$3 = objectGetOwnPropertySymbols;
	var getOwnPropertyDescriptorModule$1 = objectGetOwnPropertyDescriptor;
	var definePropertyModule = objectDefineProperty;
	var definePropertiesModule = objectDefineProperties;
	var propertyIsEnumerableModule$1 = objectPropertyIsEnumerable;
	var defineBuiltIn$3 = defineBuiltIn$5;
	var defineBuiltInAccessor$2 = defineBuiltInAccessor$3;
	var shared$3 = sharedExports;
	var sharedKey$1 = sharedKey$4;
	var hiddenKeys$1 = hiddenKeys$6;
	var uid$1 = uid$4;
	var wellKnownSymbol$b = wellKnownSymbol$l;
	var wrappedWellKnownSymbolModule = wellKnownSymbolWrapped;
	var defineWellKnownSymbol$l = wellKnownSymbolDefine;
	var defineSymbolToPrimitive$1 = symbolDefineToPrimitive;
	var setToStringTag$5 = setToStringTag$6;
	var InternalStateModule$4 = internalState;
	var $forEach$1 = arrayIteration.forEach;

	var HIDDEN = sharedKey$1('hidden');
	var SYMBOL = 'Symbol';
	var PROTOTYPE = 'prototype';

	var setInternalState$4 = InternalStateModule$4.set;
	var getInternalState$2 = InternalStateModule$4.getterFor(SYMBOL);

	var ObjectPrototype$1 = Object[PROTOTYPE];
	var $Symbol = global$9.Symbol;
	var SymbolPrototype = $Symbol && $Symbol[PROTOTYPE];
	var TypeError$1 = global$9.TypeError;
	var QObject = global$9.QObject;
	var nativeGetOwnPropertyDescriptor$1 = getOwnPropertyDescriptorModule$1.f;
	var nativeDefineProperty = definePropertyModule.f;
	var nativeGetOwnPropertyNames = getOwnPropertyNamesExternal.f;
	var nativePropertyIsEnumerable = propertyIsEnumerableModule$1.f;
	var push$3 = uncurryThis$i([].push);

	var AllSymbols = shared$3('symbols');
	var ObjectPrototypeSymbols = shared$3('op-symbols');
	var WellKnownSymbolsStore$1 = shared$3('wks');

	
	var USE_SETTER = !QObject || !QObject[PROTOTYPE] || !QObject[PROTOTYPE].findChild;

	
	var setSymbolDescriptor = DESCRIPTORS$b && fails$i(function () {
	  return nativeObjectCreate(nativeDefineProperty({}, 'a', {
	    get: function () { return nativeDefineProperty(this, 'a', { value: 7 }).a; }
	  })).a != 7;
	}) ? function (O, P, Attributes) {
	  var ObjectPrototypeDescriptor = nativeGetOwnPropertyDescriptor$1(ObjectPrototype$1, P);
	  if (ObjectPrototypeDescriptor) delete ObjectPrototype$1[P];
	  nativeDefineProperty(O, P, Attributes);
	  if (ObjectPrototypeDescriptor && O !== ObjectPrototype$1) {
	    nativeDefineProperty(ObjectPrototype$1, P, ObjectPrototypeDescriptor);
	  }
	} : nativeDefineProperty;

	var wrap = function (tag, description) {
	  var symbol = AllSymbols[tag] = nativeObjectCreate(SymbolPrototype);
	  setInternalState$4(symbol, {
	    type: SYMBOL,
	    tag: tag,
	    description: description
	  });
	  if (!DESCRIPTORS$b) symbol.description = description;
	  return symbol;
	};

	var $defineProperty = function defineProperty(O, P, Attributes) {
	  if (O === ObjectPrototype$1) $defineProperty(ObjectPrototypeSymbols, P, Attributes);
	  anObject$6(O);
	  var key = toPropertyKey(P);
	  anObject$6(Attributes);
	  if (hasOwn$6(AllSymbols, key)) {
	    if (!Attributes.enumerable) {
	      if (!hasOwn$6(O, HIDDEN)) nativeDefineProperty(O, HIDDEN, createPropertyDescriptor$1(1, {}));
	      O[HIDDEN][key] = true;
	    } else {
	      if (hasOwn$6(O, HIDDEN) && O[HIDDEN][key]) O[HIDDEN][key] = false;
	      Attributes = nativeObjectCreate(Attributes, { enumerable: createPropertyDescriptor$1(0, false) });
	    } return setSymbolDescriptor(O, key, Attributes);
	  } return nativeDefineProperty(O, key, Attributes);
	};

	var $defineProperties = function defineProperties(O, Properties) {
	  anObject$6(O);
	  var properties = toIndexedObject$5(Properties);
	  var keys = objectKeys$2(properties).concat($getOwnPropertySymbols(properties));
	  $forEach$1(keys, function (key) {
	    if (!DESCRIPTORS$b || call$7($propertyIsEnumerable$1, properties, key)) $defineProperty(O, key, properties[key]);
	  });
	  return O;
	};

	var $create = function create(O, Properties) {
	  return Properties === undefined ? nativeObjectCreate(O) : $defineProperties(nativeObjectCreate(O), Properties);
	};

	var $propertyIsEnumerable$1 = function propertyIsEnumerable(V) {
	  var P = toPropertyKey(V);
	  var enumerable = call$7(nativePropertyIsEnumerable, this, P);
	  if (this === ObjectPrototype$1 && hasOwn$6(AllSymbols, P) && !hasOwn$6(ObjectPrototypeSymbols, P)) return false;
	  return enumerable || !hasOwn$6(this, P) || !hasOwn$6(AllSymbols, P) || hasOwn$6(this, HIDDEN) && this[HIDDEN][P]
	    ? enumerable : true;
	};

	var $getOwnPropertyDescriptor = function getOwnPropertyDescriptor(O, P) {
	  var it = toIndexedObject$5(O);
	  var key = toPropertyKey(P);
	  if (it === ObjectPrototype$1 && hasOwn$6(AllSymbols, key) && !hasOwn$6(ObjectPrototypeSymbols, key)) return;
	  var descriptor = nativeGetOwnPropertyDescriptor$1(it, key);
	  if (descriptor && hasOwn$6(AllSymbols, key) && !(hasOwn$6(it, HIDDEN) && it[HIDDEN][key])) {
	    descriptor.enumerable = true;
	  }
	  return descriptor;
	};

	var $getOwnPropertyNames = function getOwnPropertyNames(O) {
	  var names = nativeGetOwnPropertyNames(toIndexedObject$5(O));
	  var result = [];
	  $forEach$1(names, function (key) {
	    if (!hasOwn$6(AllSymbols, key) && !hasOwn$6(hiddenKeys$1, key)) push$3(result, key);
	  });
	  return result;
	};

	var $getOwnPropertySymbols = function (O) {
	  var IS_OBJECT_PROTOTYPE = O === ObjectPrototype$1;
	  var names = nativeGetOwnPropertyNames(IS_OBJECT_PROTOTYPE ? ObjectPrototypeSymbols : toIndexedObject$5(O));
	  var result = [];
	  $forEach$1(names, function (key) {
	    if (hasOwn$6(AllSymbols, key) && (!IS_OBJECT_PROTOTYPE || hasOwn$6(ObjectPrototype$1, key))) {
	      push$3(result, AllSymbols[key]);
	    }
	  });
	  return result;
	};

	
	
	if (!NATIVE_SYMBOL$3) {
	  $Symbol = function Symbol() {
	    if (isPrototypeOf$l(SymbolPrototype, this)) throw TypeError$1('Symbol is not a constructor');
	    var description = !arguments.length || arguments[0] === undefined ? undefined : $toString(arguments[0]);
	    var tag = uid$1(description);
	    var setter = function (value) {
	      if (this === ObjectPrototype$1) call$7(setter, ObjectPrototypeSymbols, value);
	      if (hasOwn$6(this, HIDDEN) && hasOwn$6(this[HIDDEN], tag)) this[HIDDEN][tag] = false;
	      setSymbolDescriptor(this, tag, createPropertyDescriptor$1(1, value));
	    };
	    if (DESCRIPTORS$b && USE_SETTER) setSymbolDescriptor(ObjectPrototype$1, tag, { configurable: true, set: setter });
	    return wrap(tag, description);
	  };

	  SymbolPrototype = $Symbol[PROTOTYPE];

	  defineBuiltIn$3(SymbolPrototype, 'toString', function toString() {
	    return getInternalState$2(this).tag;
	  });

	  defineBuiltIn$3($Symbol, 'withoutSetter', function (description) {
	    return wrap(uid$1(description), description);
	  });

	  propertyIsEnumerableModule$1.f = $propertyIsEnumerable$1;
	  definePropertyModule.f = $defineProperty;
	  definePropertiesModule.f = $defineProperties;
	  getOwnPropertyDescriptorModule$1.f = $getOwnPropertyDescriptor;
	  getOwnPropertyNamesModule$2.f = getOwnPropertyNamesExternal.f = $getOwnPropertyNames;
	  getOwnPropertySymbolsModule$3.f = $getOwnPropertySymbols;

	  wrappedWellKnownSymbolModule.f = function (name) {
	    return wrap(wellKnownSymbol$b(name), name);
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

	$$J({ global: true, constructor: true, wrap: true, forced: !NATIVE_SYMBOL$3, sham: !NATIVE_SYMBOL$3 }, {
	  Symbol: $Symbol
	});

	$forEach$1(objectKeys$2(WellKnownSymbolsStore$1), function (name) {
	  defineWellKnownSymbol$l(name);
	});

	$$J({ target: SYMBOL, stat: true, forced: !NATIVE_SYMBOL$3 }, {
	  useSetter: function () { USE_SETTER = true; },
	  useSimple: function () { USE_SETTER = false; }
	});

	$$J({ target: 'Object', stat: true, forced: !NATIVE_SYMBOL$3, sham: !DESCRIPTORS$b }, {
	  
	  
	  create: $create,
	  
	  
	  defineProperty: $defineProperty,
	  
	  
	  defineProperties: $defineProperties,
	  
	  
	  getOwnPropertyDescriptor: $getOwnPropertyDescriptor
	});

	$$J({ target: 'Object', stat: true, forced: !NATIVE_SYMBOL$3 }, {
	  
	  
	  getOwnPropertyNames: $getOwnPropertyNames
	});

	
	
	defineSymbolToPrimitive$1();

	
	
	setToStringTag$5($Symbol, SYMBOL);

	hiddenKeys$1[HIDDEN] = true;

	var NATIVE_SYMBOL$2 = symbolConstructorDetection;

	
	var symbolRegistryDetection = NATIVE_SYMBOL$2 && !!Symbol['for'] && !!Symbol.keyFor;

	var $$I = _export;
	var getBuiltIn$6 = getBuiltIn$c;
	var hasOwn$5 = hasOwnProperty_1;
	var toString$9 = toString$b;
	var shared$2 = sharedExports;
	var NATIVE_SYMBOL_REGISTRY$1 = symbolRegistryDetection;

	var StringToSymbolRegistry = shared$2('string-to-symbol-registry');
	var SymbolToStringRegistry$1 = shared$2('symbol-to-string-registry');

	
	
	$$I({ target: 'Symbol', stat: true, forced: !NATIVE_SYMBOL_REGISTRY$1 }, {
	  'for': function (key) {
	    var string = toString$9(key);
	    if (hasOwn$5(StringToSymbolRegistry, string)) return StringToSymbolRegistry[string];
	    var symbol = getBuiltIn$6('Symbol')(string);
	    StringToSymbolRegistry[string] = symbol;
	    SymbolToStringRegistry$1[symbol] = string;
	    return symbol;
	  }
	});

	var $$H = _export;
	var hasOwn$4 = hasOwnProperty_1;
	var isSymbol$2 = isSymbol$5;
	var tryToString$3 = tryToString$6;
	var shared$1 = sharedExports;
	var NATIVE_SYMBOL_REGISTRY = symbolRegistryDetection;

	var SymbolToStringRegistry = shared$1('symbol-to-string-registry');

	
	
	$$H({ target: 'Symbol', stat: true, forced: !NATIVE_SYMBOL_REGISTRY }, {
	  keyFor: function keyFor(sym) {
	    if (!isSymbol$2(sym)) throw TypeError(tryToString$3(sym) + ' is not a symbol');
	    if (hasOwn$4(SymbolToStringRegistry, sym)) return SymbolToStringRegistry[sym];
	  }
	});

	var uncurryThis$h = functionUncurryThis;
	var isArray$b = isArray$e;
	var isCallable$6 = isCallable$i;
	var classof$6 = classofRaw$2;
	var toString$8 = toString$b;

	var push$2 = uncurryThis$h([].push);

	var getJsonReplacerFunction = function (replacer) {
	  if (isCallable$6(replacer)) return replacer;
	  if (!isArray$b(replacer)) return;
	  var rawLength = replacer.length;
	  var keys = [];
	  for (var i = 0; i < rawLength; i++) {
	    var element = replacer[i];
	    if (typeof element == 'string') push$2(keys, element);
	    else if (typeof element == 'number' || classof$6(element) == 'Number' || classof$6(element) == 'String') push$2(keys, toString$8(element));
	  }
	  var keysLength = keys.length;
	  var root = true;
	  return function (key, value) {
	    if (root) {
	      root = false;
	      return value;
	    }
	    if (isArray$b(this)) return value;
	    for (var j = 0; j < keysLength; j++) if (keys[j] === key) return value;
	  };
	};

	var $$G = _export;
	var getBuiltIn$5 = getBuiltIn$c;
	var apply$2 = functionApply;
	var call$6 = functionCall;
	var uncurryThis$g = functionUncurryThis;
	var fails$h = fails$u;
	var isCallable$5 = isCallable$i;
	var isSymbol$1 = isSymbol$5;
	var arraySlice$2 = arraySlice$5;
	var getReplacerFunction = getJsonReplacerFunction;
	var NATIVE_SYMBOL$1 = symbolConstructorDetection;

	var $String$1 = String;
	var $stringify = getBuiltIn$5('JSON', 'stringify');
	var exec$1 = uncurryThis$g(/./.exec);
	var charAt$3 = uncurryThis$g(''.charAt);
	var charCodeAt$1 = uncurryThis$g(''.charCodeAt);
	var replace$1 = uncurryThis$g(''.replace);
	var numberToString = uncurryThis$g(1.0.toString);

	var tester = /[\uD800-\uDFFF]/g;
	var low = /^[\uD800-\uDBFF]$/;
	var hi = /^[\uDC00-\uDFFF]$/;

	var WRONG_SYMBOLS_CONVERSION = !NATIVE_SYMBOL$1 || fails$h(function () {
	  var symbol = getBuiltIn$5('Symbol')();
	  
	  return $stringify([symbol]) != '[null]'
	    
	    || $stringify({ a: symbol }) != '{}'
	    
	    || $stringify(Object(symbol)) != '{}';
	});

	
	var ILL_FORMED_UNICODE = fails$h(function () {
	  return $stringify('\uDF06\uD834') !== '"\\udf06\\ud834"'
	    || $stringify('\uDEAD') !== '"\\udead"';
	});

	var stringifyWithSymbolsFix = function (it, replacer) {
	  var args = arraySlice$2(arguments);
	  var $replacer = getReplacerFunction(replacer);
	  if (!isCallable$5($replacer) && (it === undefined || isSymbol$1(it))) return; 
	  args[1] = function (key, value) {
	    
	    if (isCallable$5($replacer)) value = call$6($replacer, this, $String$1(key), value);
	    if (!isSymbol$1(value)) return value;
	  };
	  return apply$2($stringify, null, args);
	};

	var fixIllFormed = function (match, offset, string) {
	  var prev = charAt$3(string, offset - 1);
	  var next = charAt$3(string, offset + 1);
	  if ((exec$1(low, match) && !exec$1(hi, next)) || (exec$1(hi, match) && !exec$1(low, prev))) {
	    return '\\u' + numberToString(charCodeAt$1(match, 0), 16);
	  } return match;
	};

	if ($stringify) {
	  
	  
	  $$G({ target: 'JSON', stat: true, arity: 3, forced: WRONG_SYMBOLS_CONVERSION || ILL_FORMED_UNICODE }, {
	    
	    stringify: function stringify(it, replacer, space) {
	      var args = arraySlice$2(arguments);
	      var result = apply$2(WRONG_SYMBOLS_CONVERSION ? stringifyWithSymbolsFix : $stringify, null, args);
	      return ILL_FORMED_UNICODE && typeof result == 'string' ? replace$1(result, tester, fixIllFormed) : result;
	    }
	  });
	}

	var $$F = _export;
	var NATIVE_SYMBOL = symbolConstructorDetection;
	var fails$g = fails$u;
	var getOwnPropertySymbolsModule$2 = objectGetOwnPropertySymbols;
	var toObject$9 = toObject$d;

	
	
	var FORCED$6 = !NATIVE_SYMBOL || fails$g(function () { getOwnPropertySymbolsModule$2.f(1); });

	
	
	$$F({ target: 'Object', stat: true, forced: FORCED$6 }, {
	  getOwnPropertySymbols: function getOwnPropertySymbols(it) {
	    var $getOwnPropertySymbols = getOwnPropertySymbolsModule$2.f;
	    return $getOwnPropertySymbols ? $getOwnPropertySymbols(toObject$9(it)) : [];
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

	var getBuiltIn$4 = getBuiltIn$c;
	var defineWellKnownSymbol$9 = wellKnownSymbolDefine;
	var setToStringTag$4 = setToStringTag$6;

	
	
	defineWellKnownSymbol$9('toStringTag');

	
	
	setToStringTag$4(getBuiltIn$4('Symbol'), 'Symbol');

	var defineWellKnownSymbol$8 = wellKnownSymbolDefine;

	
	
	defineWellKnownSymbol$8('unscopables');

	var global$8 = global$j;
	var setToStringTag$3 = setToStringTag$6;

	
	
	setToStringTag$3(global$8.JSON, 'JSON', true);

	var path$l = path$r;

	var symbol$4 = path$l.Symbol;

	var iterators = {};

	var DESCRIPTORS$a = descriptors;
	var hasOwn$3 = hasOwnProperty_1;

	var FunctionPrototype$1 = Function.prototype;
	
	var getDescriptor = DESCRIPTORS$a && Object.getOwnPropertyDescriptor;

	var EXISTS = hasOwn$3(FunctionPrototype$1, 'name');
	
	var PROPER = EXISTS && (function something() {  }).name === 'something';
	var CONFIGURABLE = EXISTS && (!DESCRIPTORS$a || (DESCRIPTORS$a && getDescriptor(FunctionPrototype$1, 'name').configurable));

	var functionName = {
	  EXISTS: EXISTS,
	  PROPER: PROPER,
	  CONFIGURABLE: CONFIGURABLE
	};

	var fails$f = fails$u;

	var correctPrototypeGetter = !fails$f(function () {
	  function F() {  }
	  F.prototype.constructor = null;
	  
	  return Object.getPrototypeOf(new F()) !== F.prototype;
	});

	var hasOwn$2 = hasOwnProperty_1;
	var isCallable$4 = isCallable$i;
	var toObject$8 = toObject$d;
	var sharedKey = sharedKey$4;
	var CORRECT_PROTOTYPE_GETTER$1 = correctPrototypeGetter;

	var IE_PROTO = sharedKey('IE_PROTO');
	var $Object = Object;
	var ObjectPrototype = $Object.prototype;

	
	
	
	var objectGetPrototypeOf = CORRECT_PROTOTYPE_GETTER$1 ? $Object.getPrototypeOf : function (O) {
	  var object = toObject$8(O);
	  if (hasOwn$2(object, IE_PROTO)) return object[IE_PROTO];
	  var constructor = object.constructor;
	  if (isCallable$4(constructor) && object instanceof constructor) {
	    return constructor.prototype;
	  } return object instanceof $Object ? ObjectPrototype : null;
	};

	var fails$e = fails$u;
	var isCallable$3 = isCallable$i;
	var isObject$6 = isObject$g;
	var create$a = objectCreate;
	var getPrototypeOf$8 = objectGetPrototypeOf;
	var defineBuiltIn$2 = defineBuiltIn$5;
	var wellKnownSymbol$a = wellKnownSymbol$l;

	var ITERATOR$6 = wellKnownSymbol$a('iterator');
	var BUGGY_SAFARI_ITERATORS$1 = false;

	
	
	var IteratorPrototype$1, PrototypeOfArrayIteratorPrototype, arrayIterator;

	
	if ([].keys) {
	  arrayIterator = [].keys();
	  
	  if (!('next' in arrayIterator)) BUGGY_SAFARI_ITERATORS$1 = true;
	  else {
	    PrototypeOfArrayIteratorPrototype = getPrototypeOf$8(getPrototypeOf$8(arrayIterator));
	    if (PrototypeOfArrayIteratorPrototype !== Object.prototype) IteratorPrototype$1 = PrototypeOfArrayIteratorPrototype;
	  }
	}

	var NEW_ITERATOR_PROTOTYPE = !isObject$6(IteratorPrototype$1) || fails$e(function () {
	  var test = {};
	  
	  return IteratorPrototype$1[ITERATOR$6].call(test) !== test;
	});

	if (NEW_ITERATOR_PROTOTYPE) IteratorPrototype$1 = {};
	else IteratorPrototype$1 = create$a(IteratorPrototype$1);

	
	
	if (!isCallable$3(IteratorPrototype$1[ITERATOR$6])) {
	  defineBuiltIn$2(IteratorPrototype$1, ITERATOR$6, function () {
	    return this;
	  });
	}

	var iteratorsCore = {
	  IteratorPrototype: IteratorPrototype$1,
	  BUGGY_SAFARI_ITERATORS: BUGGY_SAFARI_ITERATORS$1
	};

	var IteratorPrototype = iteratorsCore.IteratorPrototype;
	var create$9 = objectCreate;
	var createPropertyDescriptor = createPropertyDescriptor$5;
	var setToStringTag$2 = setToStringTag$6;
	var Iterators$5 = iterators;

	var returnThis$1 = function () { return this; };

	var iteratorCreateConstructor = function (IteratorConstructor, NAME, next, ENUMERABLE_NEXT) {
	  var TO_STRING_TAG = NAME + ' Iterator';
	  IteratorConstructor.prototype = create$9(IteratorPrototype, { next: createPropertyDescriptor(+!ENUMERABLE_NEXT, next) });
	  setToStringTag$2(IteratorConstructor, TO_STRING_TAG, false, true);
	  Iterators$5[TO_STRING_TAG] = returnThis$1;
	  return IteratorConstructor;
	};

	var uncurryThis$f = functionUncurryThis;
	var aCallable$3 = aCallable$7;

	var functionUncurryThisAccessor = function (object, key, method) {
	  try {
	    
	    return uncurryThis$f(aCallable$3(Object.getOwnPropertyDescriptor(object, key)[method]));
	  } catch (error) {  }
	};

	var isCallable$2 = isCallable$i;

	var $String = String;
	var $TypeError$8 = TypeError;

	var aPossiblePrototype$1 = function (argument) {
	  if (typeof argument == 'object' || isCallable$2(argument)) return argument;
	  throw $TypeError$8("Can't set " + $String(argument) + ' as a prototype');
	};

	

	var uncurryThisAccessor = functionUncurryThisAccessor;
	var anObject$5 = anObject$b;
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
	    anObject$5(O);
	    aPossiblePrototype(proto);
	    if (CORRECT_SETTER) setter(O, proto);
	    else O.__proto__ = proto;
	    return O;
	  };
	}() : undefined);

	var $$E = _export;
	var call$5 = functionCall;
	var FunctionName = functionName;
	var createIteratorConstructor = iteratorCreateConstructor;
	var getPrototypeOf$7 = objectGetPrototypeOf;
	var setToStringTag$1 = setToStringTag$6;
	var defineBuiltIn$1 = defineBuiltIn$5;
	var wellKnownSymbol$9 = wellKnownSymbol$l;
	var Iterators$4 = iterators;
	var IteratorsCore = iteratorsCore;

	var PROPER_FUNCTION_NAME$1 = FunctionName.PROPER;
	var BUGGY_SAFARI_ITERATORS = IteratorsCore.BUGGY_SAFARI_ITERATORS;
	var ITERATOR$5 = wellKnownSymbol$9('iterator');
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
	  var nativeIterator = IterablePrototype[ITERATOR$5]
	    || IterablePrototype['@@iterator']
	    || DEFAULT && IterablePrototype[DEFAULT];
	  var defaultIterator = !BUGGY_SAFARI_ITERATORS && nativeIterator || getIterationMethod(DEFAULT);
	  var anyNativeIterator = NAME == 'Array' ? IterablePrototype.entries || nativeIterator : nativeIterator;
	  var CurrentIteratorPrototype, methods, KEY;

	  
	  if (anyNativeIterator) {
	    CurrentIteratorPrototype = getPrototypeOf$7(anyNativeIterator.call(new Iterable()));
	    if (CurrentIteratorPrototype !== Object.prototype && CurrentIteratorPrototype.next) {
	      
	      setToStringTag$1(CurrentIteratorPrototype, TO_STRING_TAG, true, true);
	      Iterators$4[TO_STRING_TAG] = returnThis;
	    }
	  }

	  
	  if (PROPER_FUNCTION_NAME$1 && DEFAULT == VALUES && nativeIterator && nativeIterator.name !== VALUES) {
	    {
	      INCORRECT_VALUES_NAME = true;
	      defaultIterator = function values() { return call$5(nativeIterator, this); };
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
	        defineBuiltIn$1(IterablePrototype, KEY, methods[KEY]);
	      }
	    } else $$E({ target: NAME, proto: true, forced: BUGGY_SAFARI_ITERATORS || INCORRECT_VALUES_NAME }, methods);
	  }

	  
	  if ((FORCED) && IterablePrototype[ITERATOR$5] !== defaultIterator) {
	    defineBuiltIn$1(IterablePrototype, ITERATOR$5, defaultIterator, { name: DEFAULT });
	  }
	  Iterators$4[NAME] = defaultIterator;

	  return methods;
	};

	
	
	var createIterResultObject$3 = function (value, done) {
	  return { value: value, done: done };
	};

	var toIndexedObject$4 = toIndexedObject$b;
	var Iterators$3 = iterators;
	var InternalStateModule$3 = internalState;
	objectDefineProperty.f;
	var defineIterator$2 = iteratorDefine;
	var createIterResultObject$2 = createIterResultObject$3;

	var ARRAY_ITERATOR = 'Array Iterator';
	var setInternalState$3 = InternalStateModule$3.set;
	var getInternalState$1 = InternalStateModule$3.getterFor(ARRAY_ITERATOR);

	
	
	
	
	
	
	
	
	
	
	defineIterator$2(Array, 'Array', function (iterated, kind) {
	  setInternalState$3(this, {
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

	var DOMIterables$1 = domIterables;
	var global$7 = global$j;
	var classof$5 = classof$c;
	var createNonEnumerableProperty$1 = createNonEnumerableProperty$6;
	var Iterators$2 = iterators;
	var wellKnownSymbol$8 = wellKnownSymbol$l;

	var TO_STRING_TAG = wellKnownSymbol$8('toStringTag');

	for (var COLLECTION_NAME in DOMIterables$1) {
	  var Collection = global$7[COLLECTION_NAME];
	  var CollectionPrototype = Collection && Collection.prototype;
	  if (CollectionPrototype && classof$5(CollectionPrototype) !== TO_STRING_TAG) {
	    createNonEnumerableProperty$1(CollectionPrototype, TO_STRING_TAG, COLLECTION_NAME);
	  }
	  Iterators$2[COLLECTION_NAME] = Iterators$2.Array;
	}

	var parent$_ = symbol$4;


	var symbol$3 = parent$_;

	var defineWellKnownSymbol$7 = wellKnownSymbolDefine;

	
	
	defineWellKnownSymbol$7('dispose');

	var parent$Z = symbol$3;



	var symbol$2 = parent$Z;

	var defineWellKnownSymbol$6 = wellKnownSymbolDefine;

	
	
	defineWellKnownSymbol$6('asyncDispose');

	var $$D = _export;
	var getBuiltIn$3 = getBuiltIn$c;
	var uncurryThis$e = functionUncurryThis;

	var Symbol$4 = getBuiltIn$3('Symbol');
	var keyFor = Symbol$4.keyFor;
	var thisSymbolValue$1 = uncurryThis$e(Symbol$4.prototype.valueOf);

	
	
	$$D({ target: 'Symbol', stat: true }, {
	  isRegistered: function isRegistered(value) {
	    try {
	      return keyFor(thisSymbolValue$1(value)) !== undefined;
	    } catch (error) {
	      return false;
	    }
	  }
	});

	var $$C = _export;
	var shared = sharedExports;
	var getBuiltIn$2 = getBuiltIn$c;
	var uncurryThis$d = functionUncurryThis;
	var isSymbol = isSymbol$5;
	var wellKnownSymbol$7 = wellKnownSymbol$l;

	var Symbol$3 = getBuiltIn$2('Symbol');
	var $isWellKnown = Symbol$3.isWellKnown;
	var getOwnPropertyNames = getBuiltIn$2('Object', 'getOwnPropertyNames');
	var thisSymbolValue = uncurryThis$d(Symbol$3.prototype.valueOf);
	var WellKnownSymbolsStore = shared('wks');

	for (var i$1 = 0, symbolKeys = getOwnPropertyNames(Symbol$3), symbolKeysLength = symbolKeys.length; i$1 < symbolKeysLength; i$1++) {
	  
	  try {
	    var symbolKey = symbolKeys[i$1];
	    if (isSymbol(Symbol$3[symbolKey])) wellKnownSymbol$7(symbolKey);
	  } catch (error) {  }
	}

	
	
	
	$$C({ target: 'Symbol', stat: true, forced: true }, {
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

	var parent$Y = symbol$2;






	




	var symbol$1 = parent$Y;

	(function (module) {
		module.exports = symbol$1;
	} (symbol$5));

	(function (module) {
		module.exports = symbolExports$1;
	} (symbol$6));

	var _Symbol$1 = getDefaultExportFromCjs(symbolExports$2);

	var iteratorExports$1 = {};
	var iterator$5 = {
	  get exports(){ return iteratorExports$1; },
	  set exports(v){ iteratorExports$1 = v; },
	};

	var iteratorExports = {};
	var iterator$4 = {
	  get exports(){ return iteratorExports; },
	  set exports(v){ iteratorExports = v; },
	};

	var uncurryThis$c = functionUncurryThis;
	var toIntegerOrInfinity$2 = toIntegerOrInfinity$5;
	var toString$7 = toString$b;
	var requireObjectCoercible$3 = requireObjectCoercible$6;

	var charAt$2 = uncurryThis$c(''.charAt);
	var charCodeAt = uncurryThis$c(''.charCodeAt);
	var stringSlice = uncurryThis$c(''.slice);

	var createMethod$3 = function (CONVERT_TO_STRING) {
	  return function ($this, pos) {
	    var S = toString$7(requireObjectCoercible$3($this));
	    var position = toIntegerOrInfinity$2(pos);
	    var size = S.length;
	    var first, second;
	    if (position < 0 || position >= size) return CONVERT_TO_STRING ? '' : undefined;
	    first = charCodeAt(S, position);
	    return first < 0xD800 || first > 0xDBFF || position + 1 === size
	      || (second = charCodeAt(S, position + 1)) < 0xDC00 || second > 0xDFFF
	        ? CONVERT_TO_STRING
	          ? charAt$2(S, position)
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

	var charAt$1 = stringMultibyte.charAt;
	var toString$6 = toString$b;
	var InternalStateModule$2 = internalState;
	var defineIterator$1 = iteratorDefine;
	var createIterResultObject$1 = createIterResultObject$3;

	var STRING_ITERATOR = 'String Iterator';
	var setInternalState$2 = InternalStateModule$2.set;
	var getInternalState = InternalStateModule$2.getterFor(STRING_ITERATOR);

	
	
	defineIterator$1(String, 'String', function (iterated) {
	  setInternalState$2(this, {
	    type: STRING_ITERATOR,
	    string: toString$6(iterated),
	    index: 0
	  });
	
	
	}, function next() {
	  var state = getInternalState(this);
	  var string = state.string;
	  var index = state.index;
	  var point;
	  if (index >= string.length) return createIterResultObject$1(undefined, true);
	  point = charAt$1(string, index);
	  state.index += point.length;
	  return createIterResultObject$1(point, false);
	});

	var WrappedWellKnownSymbolModule$1 = wellKnownSymbolWrapped;

	var iterator$3 = WrappedWellKnownSymbolModule$1.f('iterator');

	var parent$X = iterator$3;


	var iterator$2 = parent$X;

	var parent$W = iterator$2;

	var iterator$1 = parent$W;

	var parent$V = iterator$1;

	var iterator = parent$V;

	(function (module) {
		module.exports = iterator;
	} (iterator$4));

	(function (module) {
		module.exports = iteratorExports;
	} (iterator$5));

	var _Symbol$iterator = getDefaultExportFromCjs(iteratorExports$1);

	function _typeof(obj) {
	  "@babel/helpers - typeof";

	  return _typeof = "function" == typeof _Symbol$1 && "symbol" == typeof _Symbol$iterator ? function (obj) {
	    return typeof obj;
	  } : function (obj) {
	    return obj && "function" == typeof _Symbol$1 && obj.constructor === _Symbol$1 && obj !== _Symbol$1.prototype ? "symbol" : typeof obj;
	  }, _typeof(obj);
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

	var parent$U = toPrimitive$3;

	var toPrimitive$2 = parent$U;

	var parent$T = toPrimitive$2;

	var toPrimitive$1 = parent$T;

	var parent$S = toPrimitive$1;

	var toPrimitive = parent$S;

	(function (module) {
		module.exports = toPrimitive;
	} (toPrimitive$4));

	(function (module) {
		module.exports = toPrimitiveExports;
	} (toPrimitive$5));

	var _Symbol$toPrimitive = getDefaultExportFromCjs(toPrimitiveExports$1);

	function _toPrimitive(input, hint) {
	  if (_typeof(input) !== "object" || input === null) return input;
	  var prim = input[_Symbol$toPrimitive];
	  if (prim !== undefined) {
	    var res = prim.call(input, hint || "default");
	    if (_typeof(res) !== "object") return res;
	    throw new TypeError("@@toPrimitive must return a primitive value.");
	  }
	  return (hint === "string" ? String : Number)(input);
	}

	function _toPropertyKey(arg) {
	  var key = _toPrimitive(arg, "string");
	  return _typeof(key) === "symbol" ? key : String(key);
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

	function _assertThisInitialized$1(self) {
	  if (self === void 0) {
	    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
	  }
	  return self;
	}

	var createExports$2 = {};
	var create$8 = {
	  get exports(){ return createExports$2; },
	  set exports(v){ createExports$2 = v; },
	};

	var createExports$1 = {};
	var create$7 = {
	  get exports(){ return createExports$1; },
	  set exports(v){ createExports$1 = v; },
	};

	
	var $$B = _export;
	var DESCRIPTORS$9 = descriptors;
	var create$6 = objectCreate;

	
	
	$$B({ target: 'Object', stat: true, sham: !DESCRIPTORS$9 }, {
	  create: create$6
	});

	var path$k = path$r;

	var Object$3 = path$k.Object;

	var create$5 = function create(P, D) {
	  return Object$3.create(P, D);
	};

	var parent$R = create$5;

	var create$4 = parent$R;

	var parent$Q = create$4;

	var create$3 = parent$Q;

	var parent$P = create$3;

	var create$2 = parent$P;

	(function (module) {
		module.exports = create$2;
	} (create$7));

	(function (module) {
		module.exports = createExports$1;
	} (create$8));

	var _Object$create$1 = getDefaultExportFromCjs(createExports$2);

	var setPrototypeOfExports$1 = {};
	var setPrototypeOf$6 = {
	  get exports(){ return setPrototypeOfExports$1; },
	  set exports(v){ setPrototypeOfExports$1 = v; },
	};

	var setPrototypeOfExports = {};
	var setPrototypeOf$5 = {
	  get exports(){ return setPrototypeOfExports; },
	  set exports(v){ setPrototypeOfExports = v; },
	};

	var $$A = _export;
	var setPrototypeOf$4 = objectSetPrototypeOf;

	
	
	$$A({ target: 'Object', stat: true }, {
	  setPrototypeOf: setPrototypeOf$4
	});

	var path$j = path$r;

	var setPrototypeOf$3 = path$j.Object.setPrototypeOf;

	var parent$O = setPrototypeOf$3;

	var setPrototypeOf$2 = parent$O;

	var parent$N = setPrototypeOf$2;

	var setPrototypeOf$1 = parent$N;

	var parent$M = setPrototypeOf$1;

	var setPrototypeOf = parent$M;

	(function (module) {
		module.exports = setPrototypeOf;
	} (setPrototypeOf$5));

	(function (module) {
		module.exports = setPrototypeOfExports;
	} (setPrototypeOf$6));

	var _Object$setPrototypeOf = getDefaultExportFromCjs(setPrototypeOfExports$1);

	var bindExports$2 = {};
	var bind$b = {
	  get exports(){ return bindExports$2; },
	  set exports(v){ bindExports$2 = v; },
	};

	var bindExports$1 = {};
	var bind$a = {
	  get exports(){ return bindExports$1; },
	  set exports(v){ bindExports$1 = v; },
	};

	
	var $$z = _export;
	var bind$9 = functionBind;

	
	
	
	$$z({ target: 'Function', proto: true, forced: Function.bind !== bind$9 }, {
	  bind: bind$9
	});

	var path$i = path$r;

	var entryVirtual$k = function (CONSTRUCTOR) {
	  return path$i[CONSTRUCTOR + 'Prototype'];
	};

	var entryVirtual$j = entryVirtual$k;

	var bind$8 = entryVirtual$j('Function').bind;

	var isPrototypeOf$k = objectIsPrototypeOf;
	var method$h = bind$8;

	var FunctionPrototype = Function.prototype;

	var bind$7 = function (it) {
	  var own = it.bind;
	  return it === FunctionPrototype || (isPrototypeOf$k(FunctionPrototype, it) && own === FunctionPrototype.bind) ? method$h : own;
	};

	var parent$L = bind$7;

	var bind$6 = parent$L;

	var parent$K = bind$6;

	var bind$5 = parent$K;

	var parent$J = bind$5;

	var bind$4 = parent$J;

	(function (module) {
		module.exports = bind$4;
	} (bind$a));

	(function (module) {
		module.exports = bindExports$1;
	} (bind$b));

	var _bindInstanceProperty$1 = getDefaultExportFromCjs(bindExports$2);

	function _setPrototypeOf(o, p) {
	  var _context;
	  _setPrototypeOf = _Object$setPrototypeOf ? _bindInstanceProperty$1(_context = _Object$setPrototypeOf).call(_context) : function _setPrototypeOf(o, p) {
	    o.__proto__ = p;
	    return o;
	  };
	  return _setPrototypeOf(o, p);
	}

	function _inherits(subClass, superClass) {
	  if (typeof superClass !== "function" && superClass !== null) {
	    throw new TypeError("Super expression must either be null or a function");
	  }
	  subClass.prototype = _Object$create$1(superClass && superClass.prototype, {
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
	  if (call && (_typeof(call) === "object" || typeof call === "function")) {
	    return call;
	  } else if (call !== void 0) {
	    throw new TypeError("Derived constructors may only return object or undefined");
	  }
	  return _assertThisInitialized$1(self);
	}

	var getPrototypeOfExports$2 = {};
	var getPrototypeOf$6 = {
	  get exports(){ return getPrototypeOfExports$2; },
	  set exports(v){ getPrototypeOfExports$2 = v; },
	};

	var getPrototypeOfExports$1 = {};
	var getPrototypeOf$5 = {
	  get exports(){ return getPrototypeOfExports$1; },
	  set exports(v){ getPrototypeOfExports$1 = v; },
	};

	var $$y = _export;
	var fails$d = fails$u;
	var toObject$7 = toObject$d;
	var nativeGetPrototypeOf = objectGetPrototypeOf;
	var CORRECT_PROTOTYPE_GETTER = correctPrototypeGetter;

	var FAILS_ON_PRIMITIVES$2 = fails$d(function () { nativeGetPrototypeOf(1); });

	
	
	$$y({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES$2, sham: !CORRECT_PROTOTYPE_GETTER }, {
	  getPrototypeOf: function getPrototypeOf(it) {
	    return nativeGetPrototypeOf(toObject$7(it));
	  }
	});

	var path$h = path$r;

	var getPrototypeOf$4 = path$h.Object.getPrototypeOf;

	var parent$I = getPrototypeOf$4;

	var getPrototypeOf$3 = parent$I;

	var parent$H = getPrototypeOf$3;

	var getPrototypeOf$2 = parent$H;

	var parent$G = getPrototypeOf$2;

	var getPrototypeOf$1 = parent$G;

	(function (module) {
		module.exports = getPrototypeOf$1;
	} (getPrototypeOf$5));

	(function (module) {
		module.exports = getPrototypeOfExports$1;
	} (getPrototypeOf$6));

	var _Object$getPrototypeOf$1 = getDefaultExportFromCjs(getPrototypeOfExports$2);

	function _getPrototypeOf(o) {
	  var _context;
	  _getPrototypeOf = _Object$setPrototypeOf ? _bindInstanceProperty$1(_context = _Object$getPrototypeOf$1).call(_context) : function _getPrototypeOf(o) {
	    return o.__proto__ || _Object$getPrototypeOf$1(o);
	  };
	  return _getPrototypeOf(o);
	}

	var isArrayExports$2 = {};
	var isArray$a = {
	  get exports(){ return isArrayExports$2; },
	  set exports(v){ isArrayExports$2 = v; },
	};

	var $$x = _export;
	var isArray$9 = isArray$e;

	
	
	$$x({ target: 'Array', stat: true }, {
	  isArray: isArray$9
	});

	var path$g = path$r;

	var isArray$8 = path$g.Array.isArray;

	var parent$F = isArray$8;

	var isArray$7 = parent$F;

	(function (module) {
		module.exports = isArray$7;
	} (isArray$a));

	var _Array$isArray$1 = getDefaultExportFromCjs(isArrayExports$2);

	var bindExports = {};
	var bind$3 = {
	  get exports(){ return bindExports; },
	  set exports(v){ bindExports = v; },
	};

	(function (module) {
		module.exports = bind$6;
	} (bind$3));

	var _bindInstanceProperty = getDefaultExportFromCjs(bindExports);

	var setTimeoutExports = {};
	var setTimeout$3 = {
	  get exports(){ return setTimeoutExports; },
	  set exports(v){ setTimeoutExports = v; },
	};

	

	var engineIsBun = typeof Bun == 'function' && Bun && typeof Bun.version == 'string';

	var $TypeError$7 = TypeError;

	var validateArgumentsLength$1 = function (passed, required) {
	  if (passed < required) throw $TypeError$7('Not enough arguments');
	  return passed;
	};

	var global$6 = global$j;
	var apply$1 = functionApply;
	var isCallable$1 = isCallable$i;
	var ENGINE_IS_BUN = engineIsBun;
	var USER_AGENT = engineUserAgent;
	var arraySlice$1 = arraySlice$5;
	var validateArgumentsLength = validateArgumentsLength$1;

	var Function$1 = global$6.Function;
	
	var WRAP = /MSIE .\./.test(USER_AGENT) || ENGINE_IS_BUN && (function () {
	  var version = global$6.Bun.version.split('.');
	  return version.length < 3 || version[0] == 0 && (version[1] < 3 || version[1] == 3 && version[2] == 0);
	})();

	
	
	
	var schedulersFix$2 = function (scheduler, hasTimeArg) {
	  var firstParamIndex = hasTimeArg ? 2 : 1;
	  return WRAP ? function (handler, timeout ) {
	    var boundArgs = validateArgumentsLength(arguments.length, 1) > firstParamIndex;
	    var fn = isCallable$1(handler) ? handler : Function$1(handler);
	    var params = boundArgs ? arraySlice$1(arguments, firstParamIndex) : [];
	    var callback = boundArgs ? function () {
	      apply$1(fn, this, params);
	    } : fn;
	    return hasTimeArg ? scheduler(callback, timeout) : scheduler(callback);
	  } : scheduler;
	};

	var $$w = _export;
	var global$5 = global$j;
	var schedulersFix$1 = schedulersFix$2;

	var setInterval$2 = schedulersFix$1(global$5.setInterval, true);

	
	
	$$w({ global: true, bind: true, forced: global$5.setInterval !== setInterval$2 }, {
	  setInterval: setInterval$2
	});

	var $$v = _export;
	var global$4 = global$j;
	var schedulersFix = schedulersFix$2;

	var setTimeout$2 = schedulersFix(global$4.setTimeout, true);

	
	
	$$v({ global: true, bind: true, forced: global$4.setTimeout !== setTimeout$2 }, {
	  setTimeout: setTimeout$2
	});

	var path$f = path$r;

	var setTimeout$1 = path$f.setTimeout;

	(function (module) {
		module.exports = setTimeout$1;
	} (setTimeout$3));

	var _setTimeout = getDefaultExportFromCjs(setTimeoutExports);

	var forEachExports = {};
	var forEach$6 = {
	  get exports(){ return forEachExports; },
	  set exports(v){ forEachExports = v; },
	};

	var fails$c = fails$u;

	var arrayMethodIsStrict$6 = function (METHOD_NAME, argument) {
	  var method = [][METHOD_NAME];
	  return !!method && fails$c(function () {
	    
	    method.call(null, argument || function () { return 1; }, 1);
	  });
	};

	var $forEach = arrayIteration.forEach;
	var arrayMethodIsStrict$5 = arrayMethodIsStrict$6;

	var STRICT_METHOD$3 = arrayMethodIsStrict$5('forEach');

	
	
	var arrayForEach = !STRICT_METHOD$3 ? function forEach(callbackfn ) {
	  return $forEach(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	
	} : [].forEach;

	var $$u = _export;
	var forEach$5 = arrayForEach;

	
	
	
	$$u({ target: 'Array', proto: true, forced: [].forEach != forEach$5 }, {
	  forEach: forEach$5
	});

	var entryVirtual$i = entryVirtual$k;

	var forEach$4 = entryVirtual$i('Array').forEach;

	var parent$E = forEach$4;

	var forEach$3 = parent$E;

	var classof$4 = classof$c;
	var hasOwn$1 = hasOwnProperty_1;
	var isPrototypeOf$j = objectIsPrototypeOf;
	var method$g = forEach$3;

	var ArrayPrototype$g = Array.prototype;

	var DOMIterables = {
	  DOMTokenList: true,
	  NodeList: true
	};

	var forEach$2 = function (it) {
	  var own = it.forEach;
	  return it === ArrayPrototype$g || (isPrototypeOf$j(ArrayPrototype$g, it) && own === ArrayPrototype$g.forEach)
	    || hasOwn$1(DOMIterables, classof$4(it)) ? method$g : own;
	};

	(function (module) {
		module.exports = forEach$2;
	} (forEach$6));

	var _forEachInstanceProperty = getDefaultExportFromCjs(forEachExports);

	
	
	
	
	var moment$2 = typeof window !== 'undefined' && window['moment'] || moment$3;

	var getOwnPropertySymbolsExports = {};
	var getOwnPropertySymbols$2 = {
	  get exports(){ return getOwnPropertySymbolsExports; },
	  set exports(v){ getOwnPropertySymbolsExports = v; },
	};

	var path$e = path$r;

	var getOwnPropertySymbols$1 = path$e.Object.getOwnPropertySymbols;

	var parent$D = getOwnPropertySymbols$1;

	var getOwnPropertySymbols = parent$D;

	(function (module) {
		module.exports = getOwnPropertySymbols;
	} (getOwnPropertySymbols$2));

	var _Object$getOwnPropertySymbols = getDefaultExportFromCjs(getOwnPropertySymbolsExports);

	var filterExports = {};
	var filter$3 = {
	  get exports(){ return filterExports; },
	  set exports(v){ filterExports = v; },
	};

	var $$t = _export;
	var $filter = arrayIteration.filter;
	var arrayMethodHasSpeciesSupport$3 = arrayMethodHasSpeciesSupport$5;

	var HAS_SPECIES_SUPPORT$3 = arrayMethodHasSpeciesSupport$3('filter');

	
	
	
	$$t({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$3 }, {
	  filter: function filter(callbackfn ) {
	    return $filter(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$h = entryVirtual$k;

	var filter$2 = entryVirtual$h('Array').filter;

	var isPrototypeOf$i = objectIsPrototypeOf;
	var method$f = filter$2;

	var ArrayPrototype$f = Array.prototype;

	var filter$1 = function (it) {
	  var own = it.filter;
	  return it === ArrayPrototype$f || (isPrototypeOf$i(ArrayPrototype$f, it) && own === ArrayPrototype$f.filter) ? method$f : own;
	};

	var parent$C = filter$1;

	var filter = parent$C;

	(function (module) {
		module.exports = filter;
	} (filter$3));

	var _filterInstanceProperty = getDefaultExportFromCjs(filterExports);

	var getOwnPropertyDescriptorExports$1 = {};
	var getOwnPropertyDescriptor$4 = {
	  get exports(){ return getOwnPropertyDescriptorExports$1; },
	  set exports(v){ getOwnPropertyDescriptorExports$1 = v; },
	};

	var getOwnPropertyDescriptorExports = {};
	var getOwnPropertyDescriptor$3 = {
	  get exports(){ return getOwnPropertyDescriptorExports; },
	  set exports(v){ getOwnPropertyDescriptorExports = v; },
	};

	var $$s = _export;
	var fails$b = fails$u;
	var toIndexedObject$3 = toIndexedObject$b;
	var nativeGetOwnPropertyDescriptor = objectGetOwnPropertyDescriptor.f;
	var DESCRIPTORS$8 = descriptors;

	var FORCED$5 = !DESCRIPTORS$8 || fails$b(function () { nativeGetOwnPropertyDescriptor(1); });

	
	
	$$s({ target: 'Object', stat: true, forced: FORCED$5, sham: !DESCRIPTORS$8 }, {
	  getOwnPropertyDescriptor: function getOwnPropertyDescriptor(it, key) {
	    return nativeGetOwnPropertyDescriptor(toIndexedObject$3(it), key);
	  }
	});

	var path$d = path$r;

	var Object$2 = path$d.Object;

	var getOwnPropertyDescriptor$2 = getOwnPropertyDescriptor$3.exports = function getOwnPropertyDescriptor(it, key) {
	  return Object$2.getOwnPropertyDescriptor(it, key);
	};

	if (Object$2.getOwnPropertyDescriptor.sham) getOwnPropertyDescriptor$2.sham = true;

	var parent$B = getOwnPropertyDescriptorExports;

	var getOwnPropertyDescriptor$1 = parent$B;

	(function (module) {
		module.exports = getOwnPropertyDescriptor$1;
	} (getOwnPropertyDescriptor$4));

	var _Object$getOwnPropertyDescriptor = getDefaultExportFromCjs(getOwnPropertyDescriptorExports$1);

	var getOwnPropertyDescriptorsExports = {};
	var getOwnPropertyDescriptors$2 = {
	  get exports(){ return getOwnPropertyDescriptorsExports; },
	  set exports(v){ getOwnPropertyDescriptorsExports = v; },
	};

	var getBuiltIn$1 = getBuiltIn$c;
	var uncurryThis$b = functionUncurryThis;
	var getOwnPropertyNamesModule$1 = objectGetOwnPropertyNames;
	var getOwnPropertySymbolsModule$1 = objectGetOwnPropertySymbols;
	var anObject$4 = anObject$b;

	var concat$5 = uncurryThis$b([].concat);

	
	var ownKeys$7 = getBuiltIn$1('Reflect', 'ownKeys') || function ownKeys(it) {
	  var keys = getOwnPropertyNamesModule$1.f(anObject$4(it));
	  var getOwnPropertySymbols = getOwnPropertySymbolsModule$1.f;
	  return getOwnPropertySymbols ? concat$5(keys, getOwnPropertySymbols(it)) : keys;
	};

	var $$r = _export;
	var DESCRIPTORS$7 = descriptors;
	var ownKeys$6 = ownKeys$7;
	var toIndexedObject$2 = toIndexedObject$b;
	var getOwnPropertyDescriptorModule = objectGetOwnPropertyDescriptor;
	var createProperty$3 = createProperty$6;

	
	
	$$r({ target: 'Object', stat: true, sham: !DESCRIPTORS$7 }, {
	  getOwnPropertyDescriptors: function getOwnPropertyDescriptors(object) {
	    var O = toIndexedObject$2(object);
	    var getOwnPropertyDescriptor = getOwnPropertyDescriptorModule.f;
	    var keys = ownKeys$6(O);
	    var result = {};
	    var index = 0;
	    var key, descriptor;
	    while (keys.length > index) {
	      descriptor = getOwnPropertyDescriptor(O, key = keys[index++]);
	      if (descriptor !== undefined) createProperty$3(result, key, descriptor);
	    }
	    return result;
	  }
	});

	var path$c = path$r;

	var getOwnPropertyDescriptors$1 = path$c.Object.getOwnPropertyDescriptors;

	var parent$A = getOwnPropertyDescriptors$1;

	var getOwnPropertyDescriptors = parent$A;

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

	var $$q = _export;
	var DESCRIPTORS$6 = descriptors;
	var defineProperties$2 = objectDefineProperties.f;

	
	
	
	$$q({ target: 'Object', stat: true, forced: Object.defineProperties !== defineProperties$2, sham: !DESCRIPTORS$6 }, {
	  defineProperties: defineProperties$2
	});

	var path$b = path$r;

	var Object$1 = path$b.Object;

	var defineProperties$1 = defineProperties$3.exports = function defineProperties(T, D) {
	  return Object$1.defineProperties(T, D);
	};

	if (Object$1.defineProperties.sham) defineProperties$1.sham = true;

	var parent$z = definePropertiesExports;

	var defineProperties = parent$z;

	(function (module) {
		module.exports = defineProperties;
	} (defineProperties$4));

	var _Object$defineProperties = getDefaultExportFromCjs(definePropertiesExports$1);

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

	var mapExports = {};
	var map$3 = {
	  get exports(){ return mapExports; },
	  set exports(v){ mapExports = v; },
	};

	var $$p = _export;
	var $map = arrayIteration.map;
	var arrayMethodHasSpeciesSupport$2 = arrayMethodHasSpeciesSupport$5;

	var HAS_SPECIES_SUPPORT$2 = arrayMethodHasSpeciesSupport$2('map');

	
	
	
	$$p({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$2 }, {
	  map: function map(callbackfn ) {
	    return $map(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$g = entryVirtual$k;

	var map$2 = entryVirtual$g('Array').map;

	var isPrototypeOf$h = objectIsPrototypeOf;
	var method$e = map$2;

	var ArrayPrototype$e = Array.prototype;

	var map$1 = function (it) {
	  var own = it.map;
	  return it === ArrayPrototype$e || (isPrototypeOf$h(ArrayPrototype$e, it) && own === ArrayPrototype$e.map) ? method$e : own;
	};

	var parent$y = map$1;

	var map = parent$y;

	(function (module) {
		module.exports = map;
	} (map$3));

	var _mapInstanceProperty = getDefaultExportFromCjs(mapExports);

	var reduceExports = {};
	var reduce$3 = {
	  get exports(){ return reduceExports; },
	  set exports(v){ reduceExports = v; },
	};

	var aCallable$2 = aCallable$7;
	var toObject$6 = toObject$d;
	var IndexedObject$1 = indexedObject;
	var lengthOfArrayLike$6 = lengthOfArrayLike$b;

	var $TypeError$6 = TypeError;

	
	var createMethod$2 = function (IS_RIGHT) {
	  return function (that, callbackfn, argumentsLength, memo) {
	    aCallable$2(callbackfn);
	    var O = toObject$6(that);
	    var self = IndexedObject$1(O);
	    var length = lengthOfArrayLike$6(O);
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
	        throw $TypeError$6('Reduce of empty array with no initial value');
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

	var classof$3 = classofRaw$2;

	var engineIsNode = typeof process != 'undefined' && classof$3(process) == 'process';

	var $$o = _export;
	var $reduce = arrayReduce.left;
	var arrayMethodIsStrict$4 = arrayMethodIsStrict$6;
	var CHROME_VERSION = engineV8Version;
	var IS_NODE = engineIsNode;

	
	
	var CHROME_BUG = !IS_NODE && CHROME_VERSION > 79 && CHROME_VERSION < 83;
	var FORCED$4 = CHROME_BUG || !arrayMethodIsStrict$4('reduce');

	
	
	$$o({ target: 'Array', proto: true, forced: FORCED$4 }, {
	  reduce: function reduce(callbackfn ) {
	    var length = arguments.length;
	    return $reduce(this, callbackfn, length, length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$f = entryVirtual$k;

	var reduce$2 = entryVirtual$f('Array').reduce;

	var isPrototypeOf$g = objectIsPrototypeOf;
	var method$d = reduce$2;

	var ArrayPrototype$d = Array.prototype;

	var reduce$1 = function (it) {
	  var own = it.reduce;
	  return it === ArrayPrototype$d || (isPrototypeOf$g(ArrayPrototype$d, it) && own === ArrayPrototype$d.reduce) ? method$d : own;
	};

	var parent$x = reduce$1;

	var reduce = parent$x;

	(function (module) {
		module.exports = reduce;
	} (reduce$3));

	var _reduceInstanceProperty = getDefaultExportFromCjs(reduceExports);

	var keysExports = {};
	var keys$2 = {
	  get exports(){ return keysExports; },
	  set exports(v){ keysExports = v; },
	};

	var $$n = _export;
	var toObject$5 = toObject$d;
	var nativeKeys = objectKeys$4;
	var fails$a = fails$u;

	var FAILS_ON_PRIMITIVES$1 = fails$a(function () { nativeKeys(1); });

	
	
	$$n({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES$1 }, {
	  keys: function keys(it) {
	    return nativeKeys(toObject$5(it));
	  }
	});

	var path$a = path$r;

	var keys$1 = path$a.Object.keys;

	var parent$w = keys$1;

	var keys = parent$w;

	(function (module) {
		module.exports = keys;
	} (keys$2));

	var _Object$keys = getDefaultExportFromCjs(keysExports);

	var definePropertyExports = {};
	var defineProperty$3 = {
	  get exports(){ return definePropertyExports; },
	  set exports(v){ definePropertyExports = v; },
	};

	(function (module) {
		module.exports = defineProperty$9;
	} (defineProperty$3));

	var _Object$defineProperty = getDefaultExportFromCjs(definePropertyExports);

	var fromExports$2 = {};
	var from$7 = {
	  get exports(){ return fromExports$2; },
	  set exports(v){ fromExports$2 = v; },
	};

	var call$4 = functionCall;
	var anObject$3 = anObject$b;
	var getMethod$1 = getMethod$3;

	var iteratorClose$2 = function (iterator, kind, value) {
	  var innerResult, innerError;
	  anObject$3(iterator);
	  try {
	    innerResult = getMethod$1(iterator, 'return');
	    if (!innerResult) {
	      if (kind === 'throw') throw value;
	      return value;
	    }
	    innerResult = call$4(innerResult, iterator);
	  } catch (error) {
	    innerError = true;
	    innerResult = error;
	  }
	  if (kind === 'throw') throw value;
	  if (innerError) throw innerResult;
	  anObject$3(innerResult);
	  return value;
	};

	var anObject$2 = anObject$b;
	var iteratorClose$1 = iteratorClose$2;

	
	var callWithSafeIterationClosing$1 = function (iterator, fn, value, ENTRIES) {
	  try {
	    return ENTRIES ? fn(anObject$2(value)[0], value[1]) : fn(value);
	  } catch (error) {
	    iteratorClose$1(iterator, 'throw', error);
	  }
	};

	var wellKnownSymbol$6 = wellKnownSymbol$l;
	var Iterators$1 = iterators;

	var ITERATOR$4 = wellKnownSymbol$6('iterator');
	var ArrayPrototype$c = Array.prototype;

	
	var isArrayIteratorMethod$2 = function (it) {
	  return it !== undefined && (Iterators$1.Array === it || ArrayPrototype$c[ITERATOR$4] === it);
	};

	var classof$2 = classof$c;
	var getMethod = getMethod$3;
	var isNullOrUndefined$1 = isNullOrUndefined$4;
	var Iterators = iterators;
	var wellKnownSymbol$5 = wellKnownSymbol$l;

	var ITERATOR$3 = wellKnownSymbol$5('iterator');

	var getIteratorMethod$9 = function (it) {
	  if (!isNullOrUndefined$1(it)) return getMethod(it, ITERATOR$3)
	    || getMethod(it, '@@iterator')
	    || Iterators[classof$2(it)];
	};

	var call$3 = functionCall;
	var aCallable$1 = aCallable$7;
	var anObject$1 = anObject$b;
	var tryToString$2 = tryToString$6;
	var getIteratorMethod$8 = getIteratorMethod$9;

	var $TypeError$5 = TypeError;

	var getIterator$2 = function (argument, usingIterator) {
	  var iteratorMethod = arguments.length < 2 ? getIteratorMethod$8(argument) : usingIterator;
	  if (aCallable$1(iteratorMethod)) return anObject$1(call$3(iteratorMethod, argument));
	  throw $TypeError$5(tryToString$2(argument) + ' is not iterable');
	};

	var bind$2 = functionBindContext;
	var call$2 = functionCall;
	var toObject$4 = toObject$d;
	var callWithSafeIterationClosing = callWithSafeIterationClosing$1;
	var isArrayIteratorMethod$1 = isArrayIteratorMethod$2;
	var isConstructor$1 = isConstructor$4;
	var lengthOfArrayLike$5 = lengthOfArrayLike$b;
	var createProperty$2 = createProperty$6;
	var getIterator$1 = getIterator$2;
	var getIteratorMethod$7 = getIteratorMethod$9;

	var $Array$1 = Array;

	
	
	var arrayFrom = function from(arrayLike ) {
	  var O = toObject$4(arrayLike);
	  var IS_CONSTRUCTOR = isConstructor$1(this);
	  var argumentsLength = arguments.length;
	  var mapfn = argumentsLength > 1 ? arguments[1] : undefined;
	  var mapping = mapfn !== undefined;
	  if (mapping) mapfn = bind$2(mapfn, argumentsLength > 2 ? arguments[2] : undefined);
	  var iteratorMethod = getIteratorMethod$7(O);
	  var index = 0;
	  var length, result, step, iterator, next, value;
	  
	  if (iteratorMethod && !(this === $Array$1 && isArrayIteratorMethod$1(iteratorMethod))) {
	    iterator = getIterator$1(O, iteratorMethod);
	    next = iterator.next;
	    result = IS_CONSTRUCTOR ? new this() : [];
	    for (;!(step = call$2(next, iterator)).done; index++) {
	      value = mapping ? callWithSafeIterationClosing(iterator, mapfn, [step.value, index], true) : step.value;
	      createProperty$2(result, index, value);
	    }
	  } else {
	    length = lengthOfArrayLike$5(O);
	    result = IS_CONSTRUCTOR ? new this(length) : $Array$1(length);
	    for (;length > index; index++) {
	      value = mapping ? mapfn(O[index], index) : O[index];
	      createProperty$2(result, index, value);
	    }
	  }
	  result.length = index;
	  return result;
	};

	var wellKnownSymbol$4 = wellKnownSymbol$l;

	var ITERATOR$2 = wellKnownSymbol$4('iterator');
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
	  iteratorWithReturn[ITERATOR$2] = function () {
	    return this;
	  };
	  
	  Array.from(iteratorWithReturn, function () { throw 2; });
	} catch (error) {  }

	var checkCorrectnessOfIteration$1 = function (exec, SKIP_CLOSING) {
	  if (!SKIP_CLOSING && !SAFE_CLOSING) return false;
	  var ITERATION_SUPPORT = false;
	  try {
	    var object = {};
	    object[ITERATOR$2] = function () {
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

	var $$m = _export;
	var from$6 = arrayFrom;
	var checkCorrectnessOfIteration = checkCorrectnessOfIteration$1;

	var INCORRECT_ITERATION = !checkCorrectnessOfIteration(function (iterable) {
	  
	  Array.from(iterable);
	});

	
	
	$$m({ target: 'Array', stat: true, forced: INCORRECT_ITERATION }, {
	  from: from$6
	});

	var path$9 = path$r;

	var from$5 = path$9.Array.from;

	var parent$v = from$5;

	var from$4 = parent$v;

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

	var parent$u = getIteratorMethod_1;


	var getIteratorMethod$3 = parent$u;

	var parent$t = getIteratorMethod$3;

	var getIteratorMethod$2 = parent$t;

	var parent$s = getIteratorMethod$2;

	var getIteratorMethod$1 = parent$s;

	(function (module) {
		module.exports = getIteratorMethod$1;
	} (getIteratorMethod$5));

	(function (module) {
		module.exports = getIteratorMethodExports;
	} (getIteratorMethod$6));

	var _getIteratorMethod = getDefaultExportFromCjs(getIteratorMethodExports$1);

	var isArrayExports$1 = {};
	var isArray$6 = {
	  get exports(){ return isArrayExports$1; },
	  set exports(v){ isArrayExports$1 = v; },
	};

	var isArrayExports = {};
	var isArray$5 = {
	  get exports(){ return isArrayExports; },
	  set exports(v){ isArrayExports = v; },
	};

	var parent$r = isArray$7;

	var isArray$4 = parent$r;

	var parent$q = isArray$4;

	var isArray$3 = parent$q;

	(function (module) {
		module.exports = isArray$3;
	} (isArray$5));

	(function (module) {
		module.exports = isArrayExports;
	} (isArray$6));

	var _Array$isArray = getDefaultExportFromCjs(isArrayExports$1);

	function _arrayWithHoles(arr) {
	  if (_Array$isArray(arr)) return arr;
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

	var $$l = _export;
	var isArray$2 = isArray$e;
	var isConstructor = isConstructor$4;
	var isObject$5 = isObject$g;
	var toAbsoluteIndex$2 = toAbsoluteIndex$5;
	var lengthOfArrayLike$4 = lengthOfArrayLike$b;
	var toIndexedObject$1 = toIndexedObject$b;
	var createProperty$1 = createProperty$6;
	var wellKnownSymbol$3 = wellKnownSymbol$l;
	var arrayMethodHasSpeciesSupport$1 = arrayMethodHasSpeciesSupport$5;
	var nativeSlice = arraySlice$5;

	var HAS_SPECIES_SUPPORT$1 = arrayMethodHasSpeciesSupport$1('slice');

	var SPECIES$1 = wellKnownSymbol$3('species');
	var $Array = Array;
	var max$1 = Math.max;

	
	
	
	$$l({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT$1 }, {
	  slice: function slice(start, end) {
	    var O = toIndexedObject$1(this);
	    var length = lengthOfArrayLike$4(O);
	    var k = toAbsoluteIndex$2(start, length);
	    var fin = toAbsoluteIndex$2(end === undefined ? length : end, length);
	    
	    var Constructor, result, n;
	    if (isArray$2(O)) {
	      Constructor = O.constructor;
	      
	      if (isConstructor(Constructor) && (Constructor === $Array || isArray$2(Constructor.prototype))) {
	        Constructor = undefined;
	      } else if (isObject$5(Constructor)) {
	        Constructor = Constructor[SPECIES$1];
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

	var isPrototypeOf$f = objectIsPrototypeOf;
	var method$c = slice$5;

	var ArrayPrototype$b = Array.prototype;

	var slice$4 = function (it) {
	  var own = it.slice;
	  return it === ArrayPrototype$b || (isPrototypeOf$f(ArrayPrototype$b, it) && own === ArrayPrototype$b.slice) ? method$c : own;
	};

	var parent$p = slice$4;

	var slice$3 = parent$p;

	var parent$o = slice$3;

	var slice$2 = parent$o;

	var parent$n = slice$2;

	var slice$1 = parent$n;

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

	var parent$m = from$4;

	var from$1 = parent$m;

	var parent$l = from$1;

	var from = parent$l;

	(function (module) {
		module.exports = from;
	} (from$2));

	(function (module) {
		module.exports = fromExports;
	} (from$3));

	var _Array$from = getDefaultExportFromCjs(fromExports$1);

	function _arrayLikeToArray$7(arr, len) {
	  if (len == null || len > arr.length) len = arr.length;
	  for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i];
	  return arr2;
	}

	function _unsupportedIterableToArray$7(o, minLen) {
	  var _context;
	  if (!o) return;
	  if (typeof o === "string") return _arrayLikeToArray$7(o, minLen);
	  var n = _sliceInstanceProperty$1(_context = Object.prototype.toString.call(o)).call(_context, 8, -1);
	  if (n === "Object" && o.constructor) n = o.constructor.name;
	  if (n === "Map" || n === "Set") return _Array$from(o);
	  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray$7(o, minLen);
	}

	function _nonIterableRest() {
	  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
	}

	function _slicedToArray(arr, i) {
	  return _arrayWithHoles(arr) || _iterableToArrayLimit(arr, i) || _unsupportedIterableToArray$7(arr, i) || _nonIterableRest();
	}

	function _arrayWithoutHoles(arr) {
	  if (_Array$isArray(arr)) return _arrayLikeToArray$7(arr);
	}

	function _iterableToArray(iter) {
	  if (typeof _Symbol$1 !== "undefined" && _getIteratorMethod(iter) != null || iter["@@iterator"] != null) return _Array$from(iter);
	}

	function _nonIterableSpread() {
	  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
	}

	function _toConsumableArray(arr) {
	  return _arrayWithoutHoles(arr) || _iterableToArray(arr) || _unsupportedIterableToArray$7(arr) || _nonIterableSpread();
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

	var isPrototypeOf$e = objectIsPrototypeOf;
	var method$b = concat$3;

	var ArrayPrototype$a = Array.prototype;

	var concat$2 = function (it) {
	  var own = it.concat;
	  return it === ArrayPrototype$a || (isPrototypeOf$e(ArrayPrototype$a, it) && own === ArrayPrototype$a.concat) ? method$b : own;
	};

	var parent$k = concat$2;

	var concat$1 = parent$k;

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

	var $$k = _export;
	var ownKeys$4 = ownKeys$7;

	
	
	$$k({ target: 'Reflect', stat: true }, {
	  ownKeys: ownKeys$4
	});

	var path$8 = path$r;

	var ownKeys$3 = path$8.Reflect.ownKeys;

	var parent$j = ownKeys$3;

	var ownKeys$2 = parent$j;

	(function (module) {
		module.exports = ownKeys$2;
	} (ownKeys$5));

	var _Reflect$ownKeys = getDefaultExportFromCjs(ownKeysExports);

	var nowExports = {};
	var now$3 = {
	  get exports(){ return nowExports; },
	  set exports(v){ nowExports = v; },
	};

	
	var $$j = _export;
	var uncurryThis$a = functionUncurryThis;

	var $Date = Date;
	var thisTimeValue = uncurryThis$a($Date.prototype.getTime);

	
	
	$$j({ target: 'Date', stat: true }, {
	  now: function now() {
	    return thisTimeValue(new $Date());
	  }
	});

	var path$7 = path$r;

	var now$2 = path$7.Date.now;

	var parent$i = now$2;

	var now$1 = parent$i;

	(function (module) {
		module.exports = now$1;
	} (now$3));

	var _Date$now = getDefaultExportFromCjs(nowExports);

	var reverseExports = {};
	var reverse$3 = {
	  get exports(){ return reverseExports; },
	  set exports(v){ reverseExports = v; },
	};

	var $$i = _export;
	var uncurryThis$9 = functionUncurryThis;
	var isArray$1 = isArray$e;

	var nativeReverse = uncurryThis$9([].reverse);
	var test$1 = [1, 2];

	
	
	
	
	$$i({ target: 'Array', proto: true, forced: String(test$1) === String(test$1.reverse()) }, {
	  reverse: function reverse() {
	    
	    if (isArray$1(this)) this.length = this.length;
	    return nativeReverse(this);
	  }
	});

	var entryVirtual$c = entryVirtual$k;

	var reverse$2 = entryVirtual$c('Array').reverse;

	var isPrototypeOf$d = objectIsPrototypeOf;
	var method$a = reverse$2;

	var ArrayPrototype$9 = Array.prototype;

	var reverse$1 = function (it) {
	  var own = it.reverse;
	  return it === ArrayPrototype$9 || (isPrototypeOf$d(ArrayPrototype$9, it) && own === ArrayPrototype$9.reverse) ? method$a : own;
	};

	var parent$h = reverse$1;

	var reverse = parent$h;

	(function (module) {
		module.exports = reverse;
	} (reverse$3));

	var _reverseInstanceProperty = getDefaultExportFromCjs(reverseExports);

	var spliceExports = {};
	var splice$3 = {
	  get exports(){ return spliceExports; },
	  set exports(v){ spliceExports = v; },
	};

	var DESCRIPTORS$5 = descriptors;
	var isArray = isArray$e;

	var $TypeError$4 = TypeError;
	
	var getOwnPropertyDescriptor = Object.getOwnPropertyDescriptor;

	
	var SILENT_ON_NON_WRITABLE_LENGTH_SET = DESCRIPTORS$5 && !function () {
	  
	  if (this !== undefined) return true;
	  try {
	    
	    Object.defineProperty([], 'length', { writable: false }).length = 1;
	  } catch (error) {
	    return error instanceof TypeError;
	  }
	}();

	var arraySetLength = SILENT_ON_NON_WRITABLE_LENGTH_SET ? function (O, length) {
	  if (isArray(O) && !getOwnPropertyDescriptor(O, 'length').writable) {
	    throw $TypeError$4('Cannot set read only .length');
	  } return O.length = length;
	} : function (O, length) {
	  return O.length = length;
	};

	var tryToString$1 = tryToString$6;

	var $TypeError$3 = TypeError;

	var deletePropertyOrThrow$2 = function (O, P) {
	  if (!delete O[P]) throw $TypeError$3('Cannot delete property ' + tryToString$1(P) + ' of ' + tryToString$1(O));
	};

	var $$h = _export;
	var toObject$3 = toObject$d;
	var toAbsoluteIndex$1 = toAbsoluteIndex$5;
	var toIntegerOrInfinity$1 = toIntegerOrInfinity$5;
	var lengthOfArrayLike$3 = lengthOfArrayLike$b;
	var setArrayLength = arraySetLength;
	var doesNotExceedSafeInteger = doesNotExceedSafeInteger$2;
	var arraySpeciesCreate = arraySpeciesCreate$3;
	var createProperty = createProperty$6;
	var deletePropertyOrThrow$1 = deletePropertyOrThrow$2;
	var arrayMethodHasSpeciesSupport = arrayMethodHasSpeciesSupport$5;

	var HAS_SPECIES_SUPPORT = arrayMethodHasSpeciesSupport('splice');

	var max = Math.max;
	var min = Math.min;

	
	
	
	$$h({ target: 'Array', proto: true, forced: !HAS_SPECIES_SUPPORT }, {
	  splice: function splice(start, deleteCount ) {
	    var O = toObject$3(this);
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
	      actualDeleteCount = min(max(toIntegerOrInfinity$1(deleteCount), 0), len - actualStart);
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

	var entryVirtual$b = entryVirtual$k;

	var splice$2 = entryVirtual$b('Array').splice;

	var isPrototypeOf$c = objectIsPrototypeOf;
	var method$9 = splice$2;

	var ArrayPrototype$8 = Array.prototype;

	var splice$1 = function (it) {
	  var own = it.splice;
	  return it === ArrayPrototype$8 || (isPrototypeOf$c(ArrayPrototype$8, it) && own === ArrayPrototype$8.splice) ? method$9 : own;
	};

	var parent$g = splice$1;

	var splice = parent$g;

	(function (module) {
		module.exports = splice;
	} (splice$3));

	var _spliceInstanceProperty = getDefaultExportFromCjs(spliceExports);

	var assignExports = {};
	var assign$5 = {
	  get exports(){ return assignExports; },
	  set exports(v){ assignExports = v; },
	};

	var DESCRIPTORS$4 = descriptors;
	var uncurryThis$8 = functionUncurryThis;
	var call$1 = functionCall;
	var fails$9 = fails$u;
	var objectKeys$1 = objectKeys$4;
	var getOwnPropertySymbolsModule = objectGetOwnPropertySymbols;
	var propertyIsEnumerableModule = objectPropertyIsEnumerable;
	var toObject$2 = toObject$d;
	var IndexedObject = indexedObject;

	
	var $assign = Object.assign;
	
	var defineProperty$2 = Object.defineProperty;
	var concat = uncurryThis$8([].concat);

	
	
	var objectAssign = !$assign || fails$9(function () {
	  
	  if (DESCRIPTORS$4 && $assign({ b: 1 }, $assign(defineProperty$2({}, 'a', {
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
	  var T = toObject$2(target);
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
	      if (!DESCRIPTORS$4 || call$1(propertyIsEnumerable, S, key)) T[key] = S[key];
	    }
	  } return T;
	} : $assign;

	var $$g = _export;
	var assign$4 = objectAssign;

	
	
	
	$$g({ target: 'Object', stat: true, arity: 2, forced: Object.assign !== assign$4 }, {
	  assign: assign$4
	});

	var path$6 = path$r;

	var assign$3 = path$6.Object.assign;

	var parent$f = assign$3;

	var assign$2 = parent$f;

	(function (module) {
		module.exports = assign$2;
	} (assign$5));

	var _Object$assign = getDefaultExportFromCjs(assignExports);

	var includesExports = {};
	var includes$4 = {
	  get exports(){ return includesExports; },
	  set exports(v){ includesExports = v; },
	};

	var $$f = _export;
	var $includes = arrayIncludes.includes;
	var fails$8 = fails$u;

	
	var BROKEN_ON_SPARSE = fails$8(function () {
	  
	  return !Array(1).includes();
	});

	
	
	$$f({ target: 'Array', proto: true, forced: BROKEN_ON_SPARSE }, {
	  includes: function includes(el ) {
	    return $includes(this, el, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$a = entryVirtual$k;

	var includes$3 = entryVirtual$a('Array').includes;

	var isObject$4 = isObject$g;
	var classof$1 = classofRaw$2;
	var wellKnownSymbol$2 = wellKnownSymbol$l;

	var MATCH$1 = wellKnownSymbol$2('match');

	
	
	var isRegexp = function (it) {
	  var isRegExp;
	  return isObject$4(it) && ((isRegExp = it[MATCH$1]) !== undefined ? !!isRegExp : classof$1(it) == 'RegExp');
	};

	var isRegExp = isRegexp;

	var $TypeError$2 = TypeError;

	var notARegexp = function (it) {
	  if (isRegExp(it)) {
	    throw $TypeError$2("The method doesn't accept regular expressions");
	  } return it;
	};

	var wellKnownSymbol$1 = wellKnownSymbol$l;

	var MATCH = wellKnownSymbol$1('match');

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

	var $$e = _export;
	var uncurryThis$7 = functionUncurryThis;
	var notARegExp = notARegexp;
	var requireObjectCoercible$2 = requireObjectCoercible$6;
	var toString$5 = toString$b;
	var correctIsRegExpLogic = correctIsRegexpLogic;

	var stringIndexOf = uncurryThis$7(''.indexOf);

	
	
	$$e({ target: 'String', proto: true, forced: !correctIsRegExpLogic('includes') }, {
	  includes: function includes(searchString ) {
	    return !!~stringIndexOf(
	      toString$5(requireObjectCoercible$2(this)),
	      toString$5(notARegExp(searchString)),
	      arguments.length > 1 ? arguments[1] : undefined
	    );
	  }
	});

	var entryVirtual$9 = entryVirtual$k;

	var includes$2 = entryVirtual$9('String').includes;

	var isPrototypeOf$b = objectIsPrototypeOf;
	var arrayMethod = includes$3;
	var stringMethod = includes$2;

	var ArrayPrototype$7 = Array.prototype;
	var StringPrototype$2 = String.prototype;

	var includes$1 = function (it) {
	  var own = it.includes;
	  if (it === ArrayPrototype$7 || (isPrototypeOf$b(ArrayPrototype$7, it) && own === ArrayPrototype$7.includes)) return arrayMethod;
	  if (typeof it == 'string' || it === StringPrototype$2 || (isPrototypeOf$b(StringPrototype$2, it) && own === StringPrototype$2.includes)) {
	    return stringMethod;
	  } return own;
	};

	var parent$e = includes$1;

	var includes = parent$e;

	(function (module) {
		module.exports = includes;
	} (includes$4));

	var _includesInstanceProperty = getDefaultExportFromCjs(includesExports);

	var getPrototypeOfExports = {};
	var getPrototypeOf = {
	  get exports(){ return getPrototypeOfExports; },
	  set exports(v){ getPrototypeOfExports = v; },
	};

	(function (module) {
		module.exports = getPrototypeOf$3;
	} (getPrototypeOf));

	var _Object$getPrototypeOf = getDefaultExportFromCjs(getPrototypeOfExports);

	var valuesExports = {};
	var values$2 = {
	  get exports(){ return valuesExports; },
	  set exports(v){ valuesExports = v; },
	};

	var DESCRIPTORS$3 = descriptors;
	var uncurryThis$6 = functionUncurryThis;
	var objectKeys = objectKeys$4;
	var toIndexedObject = toIndexedObject$b;
	var $propertyIsEnumerable = objectPropertyIsEnumerable.f;

	var propertyIsEnumerable = uncurryThis$6($propertyIsEnumerable);
	var push$1 = uncurryThis$6([].push);

	
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
	      if (!DESCRIPTORS$3 || propertyIsEnumerable(O, key)) {
	        push$1(result, TO_ENTRIES ? [key, O[key]] : O[key]);
	      }
	    }
	    return result;
	  };
	};

	var objectToArray = {
	  
	  
	  entries: createMethod$1(true),
	  
	  
	  values: createMethod$1(false)
	};

	var $$d = _export;
	var $values = objectToArray.values;

	
	
	$$d({ target: 'Object', stat: true }, {
	  values: function values(O) {
	    return $values(O);
	  }
	});

	var path$5 = path$r;

	var values$1 = path$5.Object.values;

	var parent$d = values$1;

	var values = parent$d;

	(function (module) {
		module.exports = values;
	} (values$2));

	var _Object$values2 = getDefaultExportFromCjs(valuesExports);

	var _parseIntExports = {};
	var _parseInt$3 = {
	  get exports(){ return _parseIntExports; },
	  set exports(v){ _parseIntExports = v; },
	};

	
	var whitespaces$4 = '\u0009\u000A\u000B\u000C\u000D\u0020\u00A0\u1680\u2000\u2001\u2002' +
	  '\u2003\u2004\u2005\u2006\u2007\u2008\u2009\u200A\u202F\u205F\u3000\u2028\u2029\uFEFF';

	var uncurryThis$5 = functionUncurryThis;
	var requireObjectCoercible$1 = requireObjectCoercible$6;
	var toString$4 = toString$b;
	var whitespaces$3 = whitespaces$4;

	var replace = uncurryThis$5(''.replace);
	var ltrim = RegExp('^[' + whitespaces$3 + ']+');
	var rtrim = RegExp('(^|[^' + whitespaces$3 + '])[' + whitespaces$3 + ']+$');

	
	var createMethod = function (TYPE) {
	  return function ($this) {
	    var string = toString$4(requireObjectCoercible$1($this));
	    if (TYPE & 1) string = replace(string, ltrim, '');
	    if (TYPE & 2) string = replace(string, rtrim, '$1');
	    return string;
	  };
	};

	var stringTrim = {
	  
	  
	  start: createMethod(1),
	  
	  
	  end: createMethod(2),
	  
	  
	  trim: createMethod(3)
	};

	var global$3 = global$j;
	var fails$7 = fails$u;
	var uncurryThis$4 = functionUncurryThis;
	var toString$3 = toString$b;
	var trim$5 = stringTrim.trim;
	var whitespaces$2 = whitespaces$4;

	var $parseInt$1 = global$3.parseInt;
	var Symbol$2 = global$3.Symbol;
	var ITERATOR$1 = Symbol$2 && Symbol$2.iterator;
	var hex = /^[+-]?0x/i;
	var exec = uncurryThis$4(hex.exec);
	var FORCED$3 = $parseInt$1(whitespaces$2 + '08') !== 8 || $parseInt$1(whitespaces$2 + '0x16') !== 22
	  
	  || (ITERATOR$1 && !fails$7(function () { $parseInt$1(Object(ITERATOR$1)); }));

	
	
	var numberParseInt = FORCED$3 ? function parseInt(string, radix) {
	  var S = trim$5(toString$3(string));
	  return $parseInt$1(S, (radix >>> 0) || (exec(hex, S) ? 16 : 10));
	} : $parseInt$1;

	var $$c = _export;
	var $parseInt = numberParseInt;

	
	
	$$c({ global: true, forced: parseInt != $parseInt }, {
	  parseInt: $parseInt
	});

	var path$4 = path$r;

	var _parseInt$2 = path$4.parseInt;

	var parent$c = _parseInt$2;

	var _parseInt$1 = parent$c;

	(function (module) {
		module.exports = _parseInt$1;
	} (_parseInt$3));

	var _parseInt = getDefaultExportFromCjs(_parseIntExports);

	var indexOfExports = {};
	var indexOf$3 = {
	  get exports(){ return indexOfExports; },
	  set exports(v){ indexOfExports = v; },
	};

	
	var $$b = _export;
	var uncurryThis$3 = functionUncurryThisClause;
	var $indexOf = arrayIncludes.indexOf;
	var arrayMethodIsStrict$3 = arrayMethodIsStrict$6;

	var nativeIndexOf = uncurryThis$3([].indexOf);

	var NEGATIVE_ZERO = !!nativeIndexOf && 1 / nativeIndexOf([1], 1, -0) < 0;
	var FORCED$2 = NEGATIVE_ZERO || !arrayMethodIsStrict$3('indexOf');

	
	
	$$b({ target: 'Array', proto: true, forced: FORCED$2 }, {
	  indexOf: function indexOf(searchElement ) {
	    var fromIndex = arguments.length > 1 ? arguments[1] : undefined;
	    return NEGATIVE_ZERO
	      
	      ? nativeIndexOf(this, searchElement, fromIndex) || 0
	      : $indexOf(this, searchElement, fromIndex);
	  }
	});

	var entryVirtual$8 = entryVirtual$k;

	var indexOf$2 = entryVirtual$8('Array').indexOf;

	var isPrototypeOf$a = objectIsPrototypeOf;
	var method$8 = indexOf$2;

	var ArrayPrototype$6 = Array.prototype;

	var indexOf$1 = function (it) {
	  var own = it.indexOf;
	  return it === ArrayPrototype$6 || (isPrototypeOf$a(ArrayPrototype$6, it) && own === ArrayPrototype$6.indexOf) ? method$8 : own;
	};

	var parent$b = indexOf$1;

	var indexOf = parent$b;

	(function (module) {
		module.exports = indexOf;
	} (indexOf$3));

	var _indexOfInstanceProperty = getDefaultExportFromCjs(indexOfExports);

	var trimExports = {};
	var trim$4 = {
	  get exports(){ return trimExports; },
	  set exports(v){ trimExports = v; },
	};

	var PROPER_FUNCTION_NAME = functionName.PROPER;
	var fails$6 = fails$u;
	var whitespaces$1 = whitespaces$4;

	var non = '\u200B\u0085\u180E';

	
	
	var stringTrimForced = function (METHOD_NAME) {
	  return fails$6(function () {
	    return !!whitespaces$1[METHOD_NAME]()
	      || non[METHOD_NAME]() !== non
	      || (PROPER_FUNCTION_NAME && whitespaces$1[METHOD_NAME].name !== METHOD_NAME);
	  });
	};

	var $$a = _export;
	var $trim = stringTrim.trim;
	var forcedStringTrimMethod = stringTrimForced;

	
	
	$$a({ target: 'String', proto: true, forced: forcedStringTrimMethod('trim') }, {
	  trim: function trim() {
	    return $trim(this);
	  }
	});

	var entryVirtual$7 = entryVirtual$k;

	var trim$3 = entryVirtual$7('String').trim;

	var isPrototypeOf$9 = objectIsPrototypeOf;
	var method$7 = trim$3;

	var StringPrototype$1 = String.prototype;

	var trim$2 = function (it) {
	  var own = it.trim;
	  return typeof it == 'string' || it === StringPrototype$1
	    || (isPrototypeOf$9(StringPrototype$1, it) && own === StringPrototype$1.trim) ? method$7 : own;
	};

	var parent$a = trim$2;

	var trim$1 = parent$a;

	(function (module) {
		module.exports = trim$1;
	} (trim$4));

	var _trimInstanceProperty = getDefaultExportFromCjs(trimExports);

	var createExports = {};
	var create$1 = {
	  get exports(){ return createExports; },
	  set exports(v){ createExports = v; },
	};

	(function (module) {
		module.exports = create$4;
	} (create$1));

	var _Object$create = getDefaultExportFromCjs(createExports);

	var stringifyExports = {};
	var stringify$2 = {
	  get exports(){ return stringifyExports; },
	  set exports(v){ stringifyExports = v; },
	};

	var path$3 = path$r;
	var apply = functionApply;

	
	if (!path$3.JSON) path$3.JSON = { stringify: JSON.stringify };

	
	var stringify$1 = function stringify(it, replacer, space) {
	  return apply(path$3.JSON.stringify, null, arguments);
	};

	var parent$9 = stringify$1;

	var stringify = parent$9;

	(function (module) {
		module.exports = stringify;
	} (stringify$2));

	var _JSON$stringify = getDefaultExportFromCjs(stringifyExports);

	var fillExports = {};
	var fill$4 = {
	  get exports(){ return fillExports; },
	  set exports(v){ fillExports = v; },
	};

	var toObject$1 = toObject$d;
	var toAbsoluteIndex = toAbsoluteIndex$5;
	var lengthOfArrayLike$2 = lengthOfArrayLike$b;

	
	
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

	var $$9 = _export;
	var fill$3 = arrayFill;

	
	
	$$9({ target: 'Array', proto: true }, {
	  fill: fill$3
	});

	var entryVirtual$6 = entryVirtual$k;

	var fill$2 = entryVirtual$6('Array').fill;

	var isPrototypeOf$8 = objectIsPrototypeOf;
	var method$6 = fill$2;

	var ArrayPrototype$5 = Array.prototype;

	var fill$1 = function (it) {
	  var own = it.fill;
	  return it === ArrayPrototype$5 || (isPrototypeOf$8(ArrayPrototype$5, it) && own === ArrayPrototype$5.fill) ? method$6 : own;
	};

	var parent$8 = fill$1;

	var fill = parent$8;

	(function (module) {
		module.exports = fill;
	} (fill$4));

	var _fillInstanceProperty = getDefaultExportFromCjs(fillExports);

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

	function _assertThisInitialized(self) {
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

	
	function hasParent$1(node, parent) {
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

	  if (hasParent$1(srcEventTarget, target)) {
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

	
	function toArray$1(obj) {
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
	  var allTouches = toArray$1(ev.touches);
	  var targetIds = this.targetIds; 

	  if (type & (INPUT_START | INPUT_MOVE) && allTouches.length === 1) {
	    targetIds[allTouches[0].identifier] = true;
	    return [allTouches, allTouches];
	  }

	  var i;
	  var targetTouches;
	  var changedTouches = toArray$1(ev.changedTouches);
	  var changedTargetTouches = [];
	  var target = this.target; 

	  targetTouches = allTouches.filter(function (touch) {
	    return hasParent$1(touch.target, target);
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
	          recordTouches.call(_assertThisInitialized(_assertThisInitialized(_this)), inputEvent, inputData);
	        } else if (isMouse && isSyntheticEvent.call(_assertThisInitialized(_assertThisInitialized(_this)), inputData)) {
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
	  var all = toArray$1(ev.touches);
	  var changed = toArray$1(ev.changedTouches);

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

	

	var extend$1 = deprecate(function (dest, src, merge) {
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
	  return extend$1(dest, src, true);
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

	

	var Hammer$3 =
	
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
	  Hammer.extend = extend$1;
	  Hammer.bindFn = bindFn;
	  Hammer.assign = assign$1;
	  Hammer.inherit = inherit;
	  Hammer.bindFn = bindFn;
	  Hammer.prefixed = prefixed;
	  Hammer.toArray = toArray$1;
	  Hammer.inArray = inArray;
	  Hammer.uniqueArray = uniqueArray;
	  Hammer.splitStr = splitStr;
	  Hammer.boolOrFn = boolOrFn;
	  Hammer.hasParent = hasParent$1;
	  Hammer.addEventListeners = addEventListeners;
	  Hammer.removeEventListeners = removeEventListeners;
	  Hammer.defaults = assign$1({}, defaults, {
	    preset: preset
	  });
	  return Hammer;
	}();

	

	Hammer$3.defaults;

	var Hammer$4 = Hammer$3;

	function ownKeys$1(object, enumerableOnly) { var keys = _Object$keys(object); if (_Object$getOwnPropertySymbols) { var symbols = _Object$getOwnPropertySymbols(object); enumerableOnly && (symbols = _filterInstanceProperty(symbols).call(symbols, function (sym) { return _Object$getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
	function _objectSpread$1(target) { for (var i = 1; i < arguments.length; i++) { var _context22, _context23; var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? _forEachInstanceProperty(_context22 = ownKeys$1(Object(source), !0)).call(_context22, function (key) { _defineProperty(target, key, source[key]); }) : _Object$getOwnPropertyDescriptors ? _Object$defineProperties(target, _Object$getOwnPropertyDescriptors(source)) : _forEachInstanceProperty(_context23 = ownKeys$1(Object(source))).call(_context23, function (key) { _Object$defineProperty(target, key, _Object$getOwnPropertyDescriptor(source, key)); }); } return target; }
	function _createForOfIteratorHelper$6(o, allowArrayLike) { var it = typeof _Symbol !== "undefined" && _getIteratorMethod(o) || o["@@iterator"]; if (!it) { if (_Array$isArray$1(o) || (it = _unsupportedIterableToArray$6(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }
	function _unsupportedIterableToArray$6(o, minLen) { var _context21; if (!o) return; if (typeof o === "string") return _arrayLikeToArray$6(o, minLen); var n = _sliceInstanceProperty(_context21 = Object.prototype.toString.call(o)).call(_context21, 8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return _Array$from$1(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray$6(o, minLen); }
	function _arrayLikeToArray$6(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }

	
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
	  var _iterator = _createForOfIteratorHelper$6(_Reflect$ownKeys(b)),
	    _step;
	  try {
	    for (_iterator.s(); !(_step = _iterator.n()).done;) {
	      var prop = _step.value;
	      if (!Object.prototype.propertyIsEnumerable.call(b, prop)) ;else if (b[prop] === DELETE) {
	        delete a[prop];
	      } else if (a[prop] !== null && b[prop] !== null && _typeof(a[prop]) === "object" && _typeof(b[prop]) === "object" && !_Array$isArray$1(a[prop]) && !_Array$isArray$1(b[prop])) {
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
	  if (_Array$isArray$1(a)) {
	    return _mapInstanceProperty(a).call(a, function (value) {
	      return clone(value);
	    });
	  } else if (_typeof(a) === "object" && a !== null) {
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
	    } else if (_typeof(a[prop]) === "object" && a[prop] !== null) {
	      stripDelete(a[prop]);
	    }
	  }
	}

	
	
	function Alea() {
	  for (var _len3 = arguments.length, seed = new Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
	    seed[_key3] = arguments[_key3];
	  }
	  return AleaImplementation(seed.length ? seed : [_Date$now()]);
	}
	
	function AleaImplementation(seed) {
	  var _mashSeed = mashSeed(seed),
	    _mashSeed2 = _slicedToArray(_mashSeed, 3),
	    s0 = _mashSeed2[0],
	    s1 = _mashSeed2[1],
	    s2 = _mashSeed2[2];
	  var c = 1;
	  var random = function random() {
	    var t = 2091639 * s0 + c * 2.3283064365386963e-10; 
	    s0 = s1;
	    s1 = s2;
	    return s2 = t - (c = t | 0);
	  };
	  random.uint32 = function () {
	    return random() * 0x100000000;
	  }; 
	  random.fract53 = function () {
	    return random() + (random() * 0x200000 | 0) * 1.1102230246251565e-16;
	  }; 
	  random.algorithm = "Alea";
	  random.seed = seed;
	  random.version = "0.9";
	  return random;
	}
	
	function mashSeed() {
	  var mash = Mash();
	  var s0 = mash(" ");
	  var s1 = mash(" ");
	  var s2 = mash(" ");
	  for (var i = 0; i < arguments.length; i++) {
	    s0 -= mash(i < 0 || arguments.length <= i ? undefined : arguments[i]);
	    if (s0 < 0) {
	      s0 += 1;
	    }
	    s1 -= mash(i < 0 || arguments.length <= i ? undefined : arguments[i]);
	    if (s1 < 0) {
	      s1 += 1;
	    }
	    s2 -= mash(i < 0 || arguments.length <= i ? undefined : arguments[i]);
	    if (s2 < 0) {
	      s2 += 1;
	    }
	  }
	  return [s0, s1, s2];
	}
	
	function Mash() {
	  var n = 0xefc8249d;
	  return function (data) {
	    var string = data.toString();
	    for (var i = 0; i < string.length; i++) {
	      n += string.charCodeAt(i);
	      var h = 0.02519603282416938 * n;
	      n = h >>> 0;
	      h -= n;
	      h *= n;
	      n = h >>> 0;
	      h -= n;
	      n += h * 0x100000000; 
	    }

	    return (n >>> 0) * 2.3283064365386963e-10; 
	  };
	}

	
	function hammerMock$1() {
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
	var Hammer$1 = typeof window !== "undefined" ? window.Hammer || Hammer$4 : function () {
	  
	  return hammerMock$1();
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
	  hammer.on("tap", _bindInstanceProperty(_context3 = this._onTapOverlay).call(_context3, this));
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
	      if (!_hasParent$1(event.target, container)) {
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
	  var _iterator2 = _createForOfIteratorHelper$6(_reverseInstanceProperty(_context4 = _spliceInstanceProperty(_context5 = this._cleanupQueue).call(_context5, 0)).call(_context4)),
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

	
	function _hasParent$1(element, parent) {
	  while (element) {
	    if (element === parent) {
	      return true;
	    }
	    element = element.parentNode;
	  }
	  return false;
	}

	
	
	
	
	var ASPDateRegex$1 = /^\/?Date\((-?\d+)/i;
	
	var fullHexRE = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i;
	var shortHexRE = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
	var rgbRE = /^rgb\( *(1?\d{1,2}|2[0-4]\d|25[0-5]) *, *(1?\d{1,2}|2[0-4]\d|25[0-5]) *, *(1?\d{1,2}|2[0-4]\d|25[0-5]) *\)$/i;
	var rgbaRE = /^rgba\( *(1?\d{1,2}|2[0-4]\d|25[0-5]) *, *(1?\d{1,2}|2[0-4]\d|25[0-5]) *, *(1?\d{1,2}|2[0-4]\d|25[0-5]) *, *([01]|0?\.\d+) *\)$/i;
	
	function isNumber(value) {
	  return value instanceof Number || typeof value === "number";
	}
	
	function recursiveDOMDelete(DOMobject) {
	  if (DOMobject) {
	    while (DOMobject.hasChildNodes() === true) {
	      var child = DOMobject.firstChild;
	      if (child) {
	        recursiveDOMDelete(child);
	        DOMobject.removeChild(child);
	      }
	    }
	  }
	}
	
	function isString(value) {
	  return value instanceof String || typeof value === "string";
	}
	
	function isObject$3(value) {
	  return _typeof(value) === "object" && value !== null;
	}
	
	function isDate(value) {
	  if (value instanceof Date) {
	    return true;
	  } else if (isString(value)) {
	    
	    var match = ASPDateRegex$1.exec(value);
	    if (match) {
	      return true;
	    } else if (!isNaN(Date.parse(value))) {
	      return true;
	    }
	  }
	  return false;
	}
	
	function copyOrDelete(a, b, prop, allowDeletion) {
	  var doDeletion = false;
	  if (allowDeletion === true) {
	    doDeletion = b[prop] === null && a[prop] !== undefined;
	  }
	  if (doDeletion) {
	    delete a[prop];
	  } else {
	    a[prop] = b[prop]; 
	  }
	}
	
	function fillIfDefined(a, b) {
	  var allowDeletion = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
	  
	  
	  for (var prop in a) {
	    if (b[prop] !== undefined) {
	      if (b[prop] === null || _typeof(b[prop]) !== "object") {
	        
	        copyOrDelete(a, b, prop, allowDeletion);
	      } else {
	        var aProp = a[prop];
	        var bProp = b[prop];
	        if (isObject$3(aProp) && isObject$3(bProp)) {
	          fillIfDefined(aProp, bProp, allowDeletion);
	        }
	      }
	    }
	  }
	}
	
	var extend = _Object$assign;
	
	function selectiveExtend(props, a) {
	  if (!_Array$isArray$1(props)) {
	    throw new Error("Array with property names expected as first argument");
	  }
	  for (var _len4 = arguments.length, others = new Array(_len4 > 2 ? _len4 - 2 : 0), _key4 = 2; _key4 < _len4; _key4++) {
	    others[_key4 - 2] = arguments[_key4];
	  }
	  for (var _i2 = 0, _others = others; _i2 < _others.length; _i2++) {
	    var other = _others[_i2];
	    for (var p = 0; p < props.length; p++) {
	      var prop = props[p];
	      if (other && Object.prototype.hasOwnProperty.call(other, prop)) {
	        a[prop] = other[prop];
	      }
	    }
	  }
	  return a;
	}
	
	function selectiveDeepExtend(props, a, b) {
	  var allowDeletion = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
	  
	  if (_Array$isArray$1(b)) {
	    throw new TypeError("Arrays are not supported by deepExtend");
	  }
	  for (var p = 0; p < props.length; p++) {
	    var prop = props[p];
	    if (Object.prototype.hasOwnProperty.call(b, prop)) {
	      if (b[prop] && b[prop].constructor === Object) {
	        if (a[prop] === undefined) {
	          a[prop] = {};
	        }
	        if (a[prop].constructor === Object) {
	          deepExtend(a[prop], b[prop], false, allowDeletion);
	        } else {
	          copyOrDelete(a, b, prop, allowDeletion);
	        }
	      } else if (_Array$isArray$1(b[prop])) {
	        throw new TypeError("Arrays are not supported by deepExtend");
	      } else {
	        copyOrDelete(a, b, prop, allowDeletion);
	      }
	    }
	  }
	  return a;
	}
	
	function selectiveNotDeepExtend(propsToExclude, a, b) {
	  var allowDeletion = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
	  
	  
	  if (_Array$isArray$1(b)) {
	    throw new TypeError("Arrays are not supported by deepExtend");
	  }
	  for (var prop in b) {
	    if (!Object.prototype.hasOwnProperty.call(b, prop)) {
	      continue;
	    } 
	    if (_includesInstanceProperty(propsToExclude).call(propsToExclude, prop)) {
	      continue;
	    } 
	    if (b[prop] && b[prop].constructor === Object) {
	      if (a[prop] === undefined) {
	        a[prop] = {};
	      }
	      if (a[prop].constructor === Object) {
	        deepExtend(a[prop], b[prop]); 
	      } else {
	        copyOrDelete(a, b, prop, allowDeletion);
	      }
	    } else if (_Array$isArray$1(b[prop])) {
	      a[prop] = [];
	      for (var i = 0; i < b[prop].length; i++) {
	        a[prop].push(b[prop][i]);
	      }
	    } else {
	      copyOrDelete(a, b, prop, allowDeletion);
	    }
	  }
	  return a;
	}
	
	function deepExtend(a, b) {
	  var protoExtend = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
	  var allowDeletion = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
	  for (var prop in b) {
	    if (Object.prototype.hasOwnProperty.call(b, prop) || protoExtend === true) {
	      if (_typeof(b[prop]) === "object" && b[prop] !== null && _Object$getPrototypeOf(b[prop]) === Object.prototype) {
	        if (a[prop] === undefined) {
	          a[prop] = deepExtend({}, b[prop], protoExtend); 
	        } else if (_typeof(a[prop]) === "object" && a[prop] !== null && _Object$getPrototypeOf(a[prop]) === Object.prototype) {
	          deepExtend(a[prop], b[prop], protoExtend); 
	        } else {
	          copyOrDelete(a, b, prop, allowDeletion);
	        }
	      } else if (_Array$isArray$1(b[prop])) {
	        var _context6;
	        a[prop] = _sliceInstanceProperty(_context6 = b[prop]).call(_context6);
	      } else {
	        copyOrDelete(a, b, prop, allowDeletion);
	      }
	    }
	  }
	  return a;
	}
	
	function equalArray(a, b) {
	  if (a.length !== b.length) {
	    return false;
	  }
	  for (var i = 0, len = a.length; i < len; i++) {
	    if (a[i] != b[i]) {
	      return false;
	    }
	  }
	  return true;
	}
	
	function getType(object) {
	  var type = _typeof(object);
	  if (type === "object") {
	    if (object === null) {
	      return "null";
	    }
	    if (object instanceof Boolean) {
	      return "Boolean";
	    }
	    if (object instanceof Number) {
	      return "Number";
	    }
	    if (object instanceof String) {
	      return "String";
	    }
	    if (_Array$isArray$1(object)) {
	      return "Array";
	    }
	    if (object instanceof Date) {
	      return "Date";
	    }
	    return "Object";
	  }
	  if (type === "number") {
	    return "Number";
	  }
	  if (type === "boolean") {
	    return "Boolean";
	  }
	  if (type === "string") {
	    return "String";
	  }
	  if (type === undefined) {
	    return "undefined";
	  }
	  return type;
	}
	
	function copyAndExtendArray(arr, newValue) {
	  var _context7;
	  return _concatInstanceProperty(_context7 = []).call(_context7, _toConsumableArray(arr), [newValue]);
	}
	
	function copyArray(arr) {
	  return _sliceInstanceProperty(arr).call(arr);
	}
	
	function getAbsoluteLeft(elem) {
	  return elem.getBoundingClientRect().left;
	}
	
	function getAbsoluteRight(elem) {
	  return elem.getBoundingClientRect().right;
	}
	
	function getAbsoluteTop(elem) {
	  return elem.getBoundingClientRect().top;
	}
	
	function addClassName(elem, classNames) {
	  var classes = elem.className.split(" ");
	  var newClasses = classNames.split(" ");
	  classes = _concatInstanceProperty(classes).call(classes, _filterInstanceProperty(newClasses).call(newClasses, function (className) {
	    return !_includesInstanceProperty(classes).call(classes, className);
	  }));
	  elem.className = classes.join(" ");
	}
	
	function removeClassName(elem, classNames) {
	  var classes = elem.className.split(" ");
	  var oldClasses = classNames.split(" ");
	  classes = _filterInstanceProperty(classes).call(classes, function (className) {
	    return !_includesInstanceProperty(oldClasses).call(oldClasses, className);
	  });
	  elem.className = classes.join(" ");
	}
	
	function forEach$1(object, callback) {
	  if (_Array$isArray$1(object)) {
	    
	    var len = object.length;
	    for (var i = 0; i < len; i++) {
	      callback(object[i], i, object);
	    }
	  } else {
	    
	    for (var key in object) {
	      if (Object.prototype.hasOwnProperty.call(object, key)) {
	        callback(object[key], key, object);
	      }
	    }
	  }
	}
	
	var toArray = _Object$values2;
	
	function updateProperty(object, key, value) {
	  if (object[key] !== value) {
	    object[key] = value;
	    return true;
	  } else {
	    return false;
	  }
	}
	
	function throttle(fn) {
	  var scheduled = false;
	  return function () {
	    if (!scheduled) {
	      scheduled = true;
	      requestAnimationFrame(function () {
	        scheduled = false;
	        fn();
	      });
	    }
	  };
	}
	
	function addEventListener(element, action, listener, useCapture) {
	  if (element.addEventListener) {
	    var _context8;
	    if (useCapture === undefined) {
	      useCapture = false;
	    }
	    if (action === "mousewheel" && _includesInstanceProperty(_context8 = navigator.userAgent).call(_context8, "Firefox")) {
	      action = "DOMMouseScroll"; 
	    }

	    element.addEventListener(action, listener, useCapture);
	  } else {
	    
	    element.attachEvent("on" + action, listener); 
	  }
	}
	
	function removeEventListener(element, action, listener, useCapture) {
	  if (element.removeEventListener) {
	    var _context9;
	    
	    if (useCapture === undefined) {
	      useCapture = false;
	    }
	    if (action === "mousewheel" && _includesInstanceProperty(_context9 = navigator.userAgent).call(_context9, "Firefox")) {
	      action = "DOMMouseScroll"; 
	    }

	    element.removeEventListener(action, listener, useCapture);
	  } else {
	    
	    element.detachEvent("on" + action, listener); 
	  }
	}
	
	function preventDefault(event) {
	  if (!event) {
	    event = window.event;
	  }
	  if (!event) ;else if (event.preventDefault) {
	    event.preventDefault(); 
	  } else {
	    
	    event.returnValue = false; 
	  }
	}
	
	function getTarget() {
	  var event = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : window.event;
	  
	  
	  var target = null;
	  if (!event) ;else if (event.target) {
	    target = event.target;
	  } else if (event.srcElement) {
	    target = event.srcElement;
	  }
	  if (!(target instanceof Element)) {
	    return null;
	  }
	  if (target.nodeType != null && target.nodeType == 3) {
	    
	    target = target.parentNode;
	    if (!(target instanceof Element)) {
	      return null;
	    }
	  }
	  return target;
	}
	
	function hasParent(element, parent) {
	  var elem = element;
	  while (elem) {
	    if (elem === parent) {
	      return true;
	    } else if (elem.parentNode) {
	      elem = elem.parentNode;
	    } else {
	      return false;
	    }
	  }
	  return false;
	}
	var option = {
	  
	  asBoolean: function asBoolean(value, defaultValue) {
	    if (typeof value == "function") {
	      value = value();
	    }
	    if (value != null) {
	      return value != false;
	    }
	    return defaultValue || null;
	  },
	  
	  asNumber: function asNumber(value, defaultValue) {
	    if (typeof value == "function") {
	      value = value();
	    }
	    if (value != null) {
	      return Number(value) || defaultValue || null;
	    }
	    return defaultValue || null;
	  },
	  
	  asString: function asString(value, defaultValue) {
	    if (typeof value == "function") {
	      value = value();
	    }
	    if (value != null) {
	      return String(value);
	    }
	    return defaultValue || null;
	  },
	  
	  asSize: function asSize(value, defaultValue) {
	    if (typeof value == "function") {
	      value = value();
	    }
	    if (isString(value)) {
	      return value;
	    } else if (isNumber(value)) {
	      return value + "px";
	    } else {
	      return defaultValue || null;
	    }
	  },
	  
	  asElement: function asElement(value, defaultValue) {
	    if (typeof value == "function") {
	      value = value();
	    }
	    return value || defaultValue || null;
	  }
	};
	
	function hexToRGB(hex) {
	  var result;
	  switch (hex.length) {
	    case 3:
	    case 4:
	      result = shortHexRE.exec(hex);
	      return result ? {
	        r: _parseInt(result[1] + result[1], 16),
	        g: _parseInt(result[2] + result[2], 16),
	        b: _parseInt(result[3] + result[3], 16)
	      } : null;
	    case 6:
	    case 7:
	      result = fullHexRE.exec(hex);
	      return result ? {
	        r: _parseInt(result[1], 16),
	        g: _parseInt(result[2], 16),
	        b: _parseInt(result[3], 16)
	      } : null;
	    default:
	      return null;
	  }
	}
	
	function overrideOpacity(color, opacity) {
	  if (_includesInstanceProperty(color).call(color, "rgba")) {
	    return color;
	  } else if (_includesInstanceProperty(color).call(color, "rgb")) {
	    var rgb = color.substr(_indexOfInstanceProperty(color).call(color, "(") + 1).replace(")", "").split(",");
	    return "rgba(" + rgb[0] + "," + rgb[1] + "," + rgb[2] + "," + opacity + ")";
	  } else {
	    var _rgb = hexToRGB(color);
	    if (_rgb == null) {
	      return color;
	    } else {
	      return "rgba(" + _rgb.r + "," + _rgb.g + "," + _rgb.b + "," + opacity + ")";
	    }
	  }
	}
	
	function RGBToHex(red, green, blue) {
	  var _context10;
	  return "#" + _sliceInstanceProperty(_context10 = ((1 << 24) + (red << 16) + (green << 8) + blue).toString(16)).call(_context10, 1);
	}
	
	function parseColor(inputColor, defaultColor) {
	  if (isString(inputColor)) {
	    var colorStr = inputColor;
	    if (isValidRGB(colorStr)) {
	      var _context11;
	      var rgb = _mapInstanceProperty(_context11 = colorStr.substr(4).substr(0, colorStr.length - 5).split(",")).call(_context11, function (value) {
	        return _parseInt(value);
	      });
	      colorStr = RGBToHex(rgb[0], rgb[1], rgb[2]);
	    }
	    if (isValidHex(colorStr) === true) {
	      var hsv = hexToHSV(colorStr);
	      var lighterColorHSV = {
	        h: hsv.h,
	        s: hsv.s * 0.8,
	        v: Math.min(1, hsv.v * 1.02)
	      };
	      var darkerColorHSV = {
	        h: hsv.h,
	        s: Math.min(1, hsv.s * 1.25),
	        v: hsv.v * 0.8
	      };
	      var darkerColorHex = HSVToHex(darkerColorHSV.h, darkerColorHSV.s, darkerColorHSV.v);
	      var lighterColorHex = HSVToHex(lighterColorHSV.h, lighterColorHSV.s, lighterColorHSV.v);
	      return {
	        background: colorStr,
	        border: darkerColorHex,
	        highlight: {
	          background: lighterColorHex,
	          border: darkerColorHex
	        },
	        hover: {
	          background: lighterColorHex,
	          border: darkerColorHex
	        }
	      };
	    } else {
	      return {
	        background: colorStr,
	        border: colorStr,
	        highlight: {
	          background: colorStr,
	          border: colorStr
	        },
	        hover: {
	          background: colorStr,
	          border: colorStr
	        }
	      };
	    }
	  } else {
	    if (defaultColor) {
	      var color = {
	        background: inputColor.background || defaultColor.background,
	        border: inputColor.border || defaultColor.border,
	        highlight: isString(inputColor.highlight) ? {
	          border: inputColor.highlight,
	          background: inputColor.highlight
	        } : {
	          background: inputColor.highlight && inputColor.highlight.background || defaultColor.highlight.background,
	          border: inputColor.highlight && inputColor.highlight.border || defaultColor.highlight.border
	        },
	        hover: isString(inputColor.hover) ? {
	          border: inputColor.hover,
	          background: inputColor.hover
	        } : {
	          border: inputColor.hover && inputColor.hover.border || defaultColor.hover.border,
	          background: inputColor.hover && inputColor.hover.background || defaultColor.hover.background
	        }
	      };
	      return color;
	    } else {
	      var _color = {
	        background: inputColor.background || undefined,
	        border: inputColor.border || undefined,
	        highlight: isString(inputColor.highlight) ? {
	          border: inputColor.highlight,
	          background: inputColor.highlight
	        } : {
	          background: inputColor.highlight && inputColor.highlight.background || undefined,
	          border: inputColor.highlight && inputColor.highlight.border || undefined
	        },
	        hover: isString(inputColor.hover) ? {
	          border: inputColor.hover,
	          background: inputColor.hover
	        } : {
	          border: inputColor.hover && inputColor.hover.border || undefined,
	          background: inputColor.hover && inputColor.hover.background || undefined
	        }
	      };
	      return _color;
	    }
	  }
	}
	
	function RGBToHSV(red, green, blue) {
	  red = red / 255;
	  green = green / 255;
	  blue = blue / 255;
	  var minRGB = Math.min(red, Math.min(green, blue));
	  var maxRGB = Math.max(red, Math.max(green, blue));
	  
	  if (minRGB === maxRGB) {
	    return {
	      h: 0,
	      s: 0,
	      v: minRGB
	    };
	  }
	  
	  var d = red === minRGB ? green - blue : blue === minRGB ? red - green : blue - red;
	  var h = red === minRGB ? 3 : blue === minRGB ? 1 : 5;
	  var hue = 60 * (h - d / (maxRGB - minRGB)) / 360;
	  var saturation = (maxRGB - minRGB) / maxRGB;
	  var value = maxRGB;
	  return {
	    h: hue,
	    s: saturation,
	    v: value
	  };
	}
	var cssUtil = {
	  
	  split: function split(cssText) {
	    var _context12;
	    var styles = {};
	    _forEachInstanceProperty(_context12 = cssText.split(";")).call(_context12, function (style) {
	      if (_trimInstanceProperty(style).call(style) != "") {
	        var _context13, _context14;
	        var parts = style.split(":");
	        var key = _trimInstanceProperty(_context13 = parts[0]).call(_context13);
	        var value = _trimInstanceProperty(_context14 = parts[1]).call(_context14);
	        styles[key] = value;
	      }
	    });
	    return styles;
	  },
	  
	  join: function join(styles) {
	    var _context15;
	    return _mapInstanceProperty(_context15 = _Object$keys(styles)).call(_context15, function (key) {
	      return key + ": " + styles[key];
	    }).join("; ");
	  }
	};
	
	function addCssText(element, cssText) {
	  var currentStyles = cssUtil.split(element.style.cssText);
	  var newStyles = cssUtil.split(cssText);
	  var styles = _objectSpread$1(_objectSpread$1({}, currentStyles), newStyles);
	  element.style.cssText = cssUtil.join(styles);
	}
	
	function removeCssText(element, cssText) {
	  var styles = cssUtil.split(element.style.cssText);
	  var removeStyles = cssUtil.split(cssText);
	  for (var key in removeStyles) {
	    if (Object.prototype.hasOwnProperty.call(removeStyles, key)) {
	      delete styles[key];
	    }
	  }
	  element.style.cssText = cssUtil.join(styles);
	}
	
	function HSVToRGB(h, s, v) {
	  var r;
	  var g;
	  var b;
	  var i = Math.floor(h * 6);
	  var f = h * 6 - i;
	  var p = v * (1 - s);
	  var q = v * (1 - f * s);
	  var t = v * (1 - (1 - f) * s);
	  switch (i % 6) {
	    case 0:
	      r = v, g = t, b = p;
	      break;
	    case 1:
	      r = q, g = v, b = p;
	      break;
	    case 2:
	      r = p, g = v, b = t;
	      break;
	    case 3:
	      r = p, g = q, b = v;
	      break;
	    case 4:
	      r = t, g = p, b = v;
	      break;
	    case 5:
	      r = v, g = p, b = q;
	      break;
	  }
	  return {
	    r: Math.floor(r * 255),
	    g: Math.floor(g * 255),
	    b: Math.floor(b * 255)
	  };
	}
	
	function HSVToHex(h, s, v) {
	  var rgb = HSVToRGB(h, s, v);
	  return RGBToHex(rgb.r, rgb.g, rgb.b);
	}
	
	function hexToHSV(hex) {
	  var rgb = hexToRGB(hex);
	  if (!rgb) {
	    throw new TypeError("'".concat(hex, "' is not a valid color."));
	  }
	  return RGBToHSV(rgb.r, rgb.g, rgb.b);
	}
	
	function isValidHex(hex) {
	  var isOk = /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(hex);
	  return isOk;
	}
	
	function isValidRGB(rgb) {
	  return rgbRE.test(rgb);
	}
	
	function isValidRGBA(rgba) {
	  return rgbaRE.test(rgba);
	}
	
	function selectiveBridgeObject(fields, referenceObject) {
	  if (referenceObject !== null && _typeof(referenceObject) === "object") {
	    
	    var objectTo = _Object$create(referenceObject);
	    for (var i = 0; i < fields.length; i++) {
	      if (Object.prototype.hasOwnProperty.call(referenceObject, fields[i])) {
	        if (_typeof(referenceObject[fields[i]]) == "object") {
	          objectTo[fields[i]] = bridgeObject(referenceObject[fields[i]]);
	        }
	      }
	    }
	    return objectTo;
	  } else {
	    return null;
	  }
	}
	
	function bridgeObject(referenceObject) {
	  if (referenceObject === null || _typeof(referenceObject) !== "object") {
	    return null;
	  }
	  if (referenceObject instanceof Element) {
	    
	    return referenceObject;
	  }
	  var objectTo = _Object$create(referenceObject);
	  for (var i in referenceObject) {
	    if (Object.prototype.hasOwnProperty.call(referenceObject, i)) {
	      if (_typeof(referenceObject[i]) == "object") {
	        objectTo[i] = bridgeObject(referenceObject[i]);
	      }
	    }
	  }
	  return objectTo;
	}
	
	function insertSort(a, compare) {
	  for (var i = 0; i < a.length; i++) {
	    var k = a[i];
	    var j = void 0;
	    for (j = i; j > 0 && compare(k, a[j - 1]) < 0; j--) {
	      a[j] = a[j - 1];
	    }
	    a[j] = k;
	  }
	  return a;
	}
	
	function mergeOptions(mergeTarget, options, option) {
	  var globalOptions = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
	  
	  var isPresent = function isPresent(obj) {
	    return obj !== null && obj !== undefined;
	  };
	  var isObject = function isObject(obj) {
	    return obj !== null && _typeof(obj) === "object";
	  };
	  
	  var isEmpty = function isEmpty(obj) {
	    for (var x in obj) {
	      if (Object.prototype.hasOwnProperty.call(obj, x)) {
	        return false;
	      }
	    }
	    return true;
	  };
	  
	  if (!isObject(mergeTarget)) {
	    throw new Error("Parameter mergeTarget must be an object");
	  }
	  if (!isObject(options)) {
	    throw new Error("Parameter options must be an object");
	  }
	  if (!isPresent(option)) {
	    throw new Error("Parameter option must have a value");
	  }
	  if (!isObject(globalOptions)) {
	    throw new Error("Parameter globalOptions must be an object");
	  }
	  
	  
	  
	  
	  var doMerge = function doMerge(target, options, option) {
	    if (!isObject(target[option])) {
	      target[option] = {};
	    }
	    var src = options[option];
	    var dst = target[option];
	    for (var prop in src) {
	      if (Object.prototype.hasOwnProperty.call(src, prop)) {
	        dst[prop] = src[prop];
	      }
	    }
	  };
	  
	  var srcOption = options[option];
	  var globalPassed = isObject(globalOptions) && !isEmpty(globalOptions);
	  var globalOption = globalPassed ? globalOptions[option] : undefined;
	  var globalEnabled = globalOption ? globalOption.enabled : undefined;
	  
	  
	  
	  if (srcOption === undefined) {
	    return; 
	  }

	  if (typeof srcOption === "boolean") {
	    if (!isObject(mergeTarget[option])) {
	      mergeTarget[option] = {};
	    }
	    mergeTarget[option].enabled = srcOption;
	    return;
	  }
	  if (srcOption === null && !isObject(mergeTarget[option])) {
	    
	    if (isPresent(globalOption)) {
	      mergeTarget[option] = _Object$create(globalOption);
	    } else {
	      return; 
	    }
	  }

	  if (!isObject(srcOption)) {
	    return;
	  }
	  
	  
	  
	  
	  var enabled = true; 
	  if (srcOption.enabled !== undefined) {
	    enabled = srcOption.enabled;
	  } else {
	    
	    if (globalEnabled !== undefined) {
	      enabled = globalOption.enabled;
	    }
	  }
	  doMerge(mergeTarget, options, option);
	  mergeTarget[option].enabled = enabled;
	}
	
	function binarySearchCustom(orderedItems, comparator, field, field2) {
	  var maxIterations = 10000;
	  var iteration = 0;
	  var low = 0;
	  var high = orderedItems.length - 1;
	  while (low <= high && iteration < maxIterations) {
	    var middle = Math.floor((low + high) / 2);
	    var item = orderedItems[middle];
	    var value = field2 === undefined ? item[field] : item[field][field2];
	    var searchResult = comparator(value);
	    if (searchResult == 0) {
	      
	      return middle;
	    } else if (searchResult == -1) {
	      
	      low = middle + 1;
	    } else {
	      
	      high = middle - 1;
	    }
	    iteration++;
	  }
	  return -1;
	}
	
	function binarySearchValue(orderedItems, target, field, sidePreference, comparator) {
	  var maxIterations = 10000;
	  var iteration = 0;
	  var low = 0;
	  var high = orderedItems.length - 1;
	  var prevValue;
	  var value;
	  var nextValue;
	  var middle;
	  comparator = comparator != undefined ? comparator : function (a, b) {
	    return a == b ? 0 : a < b ? -1 : 1;
	  };
	  while (low <= high && iteration < maxIterations) {
	    
	    middle = Math.floor(0.5 * (high + low));
	    prevValue = orderedItems[Math.max(0, middle - 1)][field];
	    value = orderedItems[middle][field];
	    nextValue = orderedItems[Math.min(orderedItems.length - 1, middle + 1)][field];
	    if (comparator(value, target) == 0) {
	      
	      return middle;
	    } else if (comparator(prevValue, target) < 0 && comparator(value, target) > 0) {
	      
	      return sidePreference == "before" ? Math.max(0, middle - 1) : middle;
	    } else if (comparator(value, target) < 0 && comparator(nextValue, target) > 0) {
	      
	      return sidePreference == "before" ? middle : Math.min(orderedItems.length - 1, middle + 1);
	    } else {
	      
	      if (comparator(value, target) < 0) {
	        
	        low = middle + 1;
	      } else {
	        
	        high = middle - 1;
	      }
	    }
	    iteration++;
	  }
	  
	  return -1;
	}
	
	var easingFunctions = {
	  
	  linear: function linear(t) {
	    return t;
	  },
	  
	  easeInQuad: function easeInQuad(t) {
	    return t * t;
	  },
	  
	  easeOutQuad: function easeOutQuad(t) {
	    return t * (2 - t);
	  },
	  
	  easeInOutQuad: function easeInOutQuad(t) {
	    return t < 0.5 ? 2 * t * t : -1 + (4 - 2 * t) * t;
	  },
	  
	  easeInCubic: function easeInCubic(t) {
	    return t * t * t;
	  },
	  
	  easeOutCubic: function easeOutCubic(t) {
	    return --t * t * t + 1;
	  },
	  
	  easeInOutCubic: function easeInOutCubic(t) {
	    return t < 0.5 ? 4 * t * t * t : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;
	  },
	  
	  easeInQuart: function easeInQuart(t) {
	    return t * t * t * t;
	  },
	  
	  easeOutQuart: function easeOutQuart(t) {
	    return 1 - --t * t * t * t;
	  },
	  
	  easeInOutQuart: function easeInOutQuart(t) {
	    return t < 0.5 ? 8 * t * t * t * t : 1 - 8 * --t * t * t * t;
	  },
	  
	  easeInQuint: function easeInQuint(t) {
	    return t * t * t * t * t;
	  },
	  
	  easeOutQuint: function easeOutQuint(t) {
	    return 1 + --t * t * t * t * t;
	  },
	  
	  easeInOutQuint: function easeInOutQuint(t) {
	    return t < 0.5 ? 16 * t * t * t * t * t : 1 + 16 * --t * t * t * t * t;
	  }
	};
	
	function getScrollBarWidth() {
	  var inner = document.createElement("p");
	  inner.style.width = "100%";
	  inner.style.height = "200px";
	  var outer = document.createElement("div");
	  outer.style.position = "absolute";
	  outer.style.top = "0px";
	  outer.style.left = "0px";
	  outer.style.visibility = "hidden";
	  outer.style.width = "200px";
	  outer.style.height = "150px";
	  outer.style.overflow = "hidden";
	  outer.appendChild(inner);
	  document.body.appendChild(outer);
	  var w1 = inner.offsetWidth;
	  outer.style.overflow = "scroll";
	  var w2 = inner.offsetWidth;
	  if (w1 == w2) {
	    w2 = outer.clientWidth;
	  }
	  document.body.removeChild(outer);
	  return w1 - w2;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	function topMost(pile, accessors) {
	  var candidate;
	  if (!_Array$isArray$1(accessors)) {
	    accessors = [accessors];
	  }
	  var _iterator3 = _createForOfIteratorHelper$6(pile),
	    _step3;
	  try {
	    for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
	      var member = _step3.value;
	      if (member) {
	        candidate = member[accessors[0]];
	        for (var i = 1; i < accessors.length; i++) {
	          if (candidate) {
	            candidate = candidate[accessors[i]];
	          }
	        }
	        if (typeof candidate !== "undefined") {
	          break;
	        }
	      }
	    }
	  } catch (err) {
	    _iterator3.e(err);
	  } finally {
	    _iterator3.f();
	  }
	  return candidate;
	}
	var htmlColors$1 = {
	  black: "#000000",
	  navy: "#000080",
	  darkblue: "#00008B",
	  mediumblue: "#0000CD",
	  blue: "#0000FF",
	  darkgreen: "#006400",
	  green: "#008000",
	  teal: "#008080",
	  darkcyan: "#008B8B",
	  deepskyblue: "#00BFFF",
	  darkturquoise: "#00CED1",
	  mediumspringgreen: "#00FA9A",
	  lime: "#00FF00",
	  springgreen: "#00FF7F",
	  aqua: "#00FFFF",
	  cyan: "#00FFFF",
	  midnightblue: "#191970",
	  dodgerblue: "#1E90FF",
	  lightseagreen: "#20B2AA",
	  forestgreen: "#228B22",
	  seagreen: "#2E8B57",
	  darkslategray: "#2F4F4F",
	  limegreen: "#32CD32",
	  mediumseagreen: "#3CB371",
	  turquoise: "#40E0D0",
	  royalblue: "#4169E1",
	  steelblue: "#4682B4",
	  darkslateblue: "#483D8B",
	  mediumturquoise: "#48D1CC",
	  indigo: "#4B0082",
	  darkolivegreen: "#556B2F",
	  cadetblue: "#5F9EA0",
	  cornflowerblue: "#6495ED",
	  mediumaquamarine: "#66CDAA",
	  dimgray: "#696969",
	  slateblue: "#6A5ACD",
	  olivedrab: "#6B8E23",
	  slategray: "#708090",
	  lightslategray: "#778899",
	  mediumslateblue: "#7B68EE",
	  lawngreen: "#7CFC00",
	  chartreuse: "#7FFF00",
	  aquamarine: "#7FFFD4",
	  maroon: "#800000",
	  purple: "#800080",
	  olive: "#808000",
	  gray: "#808080",
	  skyblue: "#87CEEB",
	  lightskyblue: "#87CEFA",
	  blueviolet: "#8A2BE2",
	  darkred: "#8B0000",
	  darkmagenta: "#8B008B",
	  saddlebrown: "#8B4513",
	  darkseagreen: "#8FBC8F",
	  lightgreen: "#90EE90",
	  mediumpurple: "#9370D8",
	  darkviolet: "#9400D3",
	  palegreen: "#98FB98",
	  darkorchid: "#9932CC",
	  yellowgreen: "#9ACD32",
	  sienna: "#A0522D",
	  brown: "#A52A2A",
	  darkgray: "#A9A9A9",
	  lightblue: "#ADD8E6",
	  greenyellow: "#ADFF2F",
	  paleturquoise: "#AFEEEE",
	  lightsteelblue: "#B0C4DE",
	  powderblue: "#B0E0E6",
	  firebrick: "#B22222",
	  darkgoldenrod: "#B8860B",
	  mediumorchid: "#BA55D3",
	  rosybrown: "#BC8F8F",
	  darkkhaki: "#BDB76B",
	  silver: "#C0C0C0",
	  mediumvioletred: "#C71585",
	  indianred: "#CD5C5C",
	  peru: "#CD853F",
	  chocolate: "#D2691E",
	  tan: "#D2B48C",
	  lightgrey: "#D3D3D3",
	  palevioletred: "#D87093",
	  thistle: "#D8BFD8",
	  orchid: "#DA70D6",
	  goldenrod: "#DAA520",
	  crimson: "#DC143C",
	  gainsboro: "#DCDCDC",
	  plum: "#DDA0DD",
	  burlywood: "#DEB887",
	  lightcyan: "#E0FFFF",
	  lavender: "#E6E6FA",
	  darksalmon: "#E9967A",
	  violet: "#EE82EE",
	  palegoldenrod: "#EEE8AA",
	  lightcoral: "#F08080",
	  khaki: "#F0E68C",
	  aliceblue: "#F0F8FF",
	  honeydew: "#F0FFF0",
	  azure: "#F0FFFF",
	  sandybrown: "#F4A460",
	  wheat: "#F5DEB3",
	  beige: "#F5F5DC",
	  whitesmoke: "#F5F5F5",
	  mintcream: "#F5FFFA",
	  ghostwhite: "#F8F8FF",
	  salmon: "#FA8072",
	  antiquewhite: "#FAEBD7",
	  linen: "#FAF0E6",
	  lightgoldenrodyellow: "#FAFAD2",
	  oldlace: "#FDF5E6",
	  red: "#FF0000",
	  fuchsia: "#FF00FF",
	  magenta: "#FF00FF",
	  deeppink: "#FF1493",
	  orangered: "#FF4500",
	  tomato: "#FF6347",
	  hotpink: "#FF69B4",
	  coral: "#FF7F50",
	  darkorange: "#FF8C00",
	  lightsalmon: "#FFA07A",
	  orange: "#FFA500",
	  lightpink: "#FFB6C1",
	  pink: "#FFC0CB",
	  gold: "#FFD700",
	  peachpuff: "#FFDAB9",
	  navajowhite: "#FFDEAD",
	  moccasin: "#FFE4B5",
	  bisque: "#FFE4C4",
	  mistyrose: "#FFE4E1",
	  blanchedalmond: "#FFEBCD",
	  papayawhip: "#FFEFD5",
	  lavenderblush: "#FFF0F5",
	  seashell: "#FFF5EE",
	  cornsilk: "#FFF8DC",
	  lemonchiffon: "#FFFACD",
	  floralwhite: "#FFFAF0",
	  snow: "#FFFAFA",
	  yellow: "#FFFF00",
	  lightyellow: "#FFFFE0",
	  ivory: "#FFFFF0",
	  white: "#FFFFFF"
	};

	
	var ColorPicker$1 = function () {
	  
	  function ColorPicker$1() {
	    var pixelRatio = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 1;
	    _classCallCheck(this, ColorPicker$1);
	    this.pixelRatio = pixelRatio;
	    this.generated = false;
	    this.centerCoordinates = {
	      x: 289 / 2,
	      y: 289 / 2
	    };
	    this.r = 289 * 0.49;
	    this.color = {
	      r: 255,
	      g: 255,
	      b: 255,
	      a: 1.0
	    };
	    this.hueCircle = undefined;
	    this.initialColor = {
	      r: 255,
	      g: 255,
	      b: 255,
	      a: 1.0
	    };
	    this.previousColor = undefined;
	    this.applied = false;

	    
	    this.updateCallback = function () {};
	    this.closeCallback = function () {};

	    
	    this._create();
	  }

	  
	  _createClass(ColorPicker$1, [{
	    key: "insertTo",
	    value: function insertTo(container) {
	      if (this.hammer !== undefined) {
	        this.hammer.destroy();
	        this.hammer = undefined;
	      }
	      this.container = container;
	      this.container.appendChild(this.frame);
	      this._bindHammer();
	      this._setSize();
	    }

	    
	  }, {
	    key: "setUpdateCallback",
	    value: function setUpdateCallback(callback) {
	      if (typeof callback === "function") {
	        this.updateCallback = callback;
	      } else {
	        throw new Error("Function attempted to set as colorPicker update callback is not a function.");
	      }
	    }

	    
	  }, {
	    key: "setCloseCallback",
	    value: function setCloseCallback(callback) {
	      if (typeof callback === "function") {
	        this.closeCallback = callback;
	      } else {
	        throw new Error("Function attempted to set as colorPicker closing callback is not a function.");
	      }
	    }

	    
	  }, {
	    key: "_isColorString",
	    value: function _isColorString(color) {
	      if (typeof color === "string") {
	        return htmlColors$1[color];
	      }
	    }

	    
	  }, {
	    key: "setColor",
	    value: function setColor(color) {
	      var setInitial = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
	      if (color === "none") {
	        return;
	      }
	      var rgba;

	      
	      var htmlColor = this._isColorString(color);
	      if (htmlColor !== undefined) {
	        color = htmlColor;
	      }

	      
	      if (isString(color) === true) {
	        if (isValidRGB(color) === true) {
	          var rgbaArray = color.substr(4).substr(0, color.length - 5).split(",");
	          rgba = {
	            r: rgbaArray[0],
	            g: rgbaArray[1],
	            b: rgbaArray[2],
	            a: 1.0
	          };
	        } else if (isValidRGBA(color) === true) {
	          var _rgbaArray = color.substr(5).substr(0, color.length - 6).split(",");
	          rgba = {
	            r: _rgbaArray[0],
	            g: _rgbaArray[1],
	            b: _rgbaArray[2],
	            a: _rgbaArray[3]
	          };
	        } else if (isValidHex(color) === true) {
	          var rgbObj = hexToRGB(color);
	          rgba = {
	            r: rgbObj.r,
	            g: rgbObj.g,
	            b: rgbObj.b,
	            a: 1.0
	          };
	        }
	      } else {
	        if (color instanceof Object) {
	          if (color.r !== undefined && color.g !== undefined && color.b !== undefined) {
	            var alpha = color.a !== undefined ? color.a : "1.0";
	            rgba = {
	              r: color.r,
	              g: color.g,
	              b: color.b,
	              a: alpha
	            };
	          }
	        }
	      }

	      
	      if (rgba === undefined) {
	        throw new Error("Unknown color passed to the colorPicker. Supported are strings: rgb, hex, rgba. Object: rgb ({r:r,g:g,b:b,[a:a]}). Supplied: " + _JSON$stringify(color));
	      } else {
	        this._setColor(rgba, setInitial);
	      }
	    }

	    
	  }, {
	    key: "show",
	    value: function show() {
	      if (this.closeCallback !== undefined) {
	        this.closeCallback();
	        this.closeCallback = undefined;
	      }
	      this.applied = false;
	      this.frame.style.display = "block";
	      this._generateHueCircle();
	    }

	    

	    
	  }, {
	    key: "_hide",
	    value: function _hide() {
	      var _this2 = this;
	      var storePrevious = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
	      
	      if (storePrevious === true) {
	        this.previousColor = _Object$assign({}, this.color);
	      }
	      if (this.applied === true) {
	        this.updateCallback(this.initialColor);
	      }
	      this.frame.style.display = "none";

	      
	      
	      _setTimeout(function () {
	        if (_this2.closeCallback !== undefined) {
	          _this2.closeCallback();
	          _this2.closeCallback = undefined;
	        }
	      }, 0);
	    }

	    
	  }, {
	    key: "_save",
	    value: function _save() {
	      this.updateCallback(this.color);
	      this.applied = false;
	      this._hide();
	    }

	    
	  }, {
	    key: "_apply",
	    value: function _apply() {
	      this.applied = true;
	      this.updateCallback(this.color);
	      this._updatePicker(this.color);
	    }

	    
	  }, {
	    key: "_loadLast",
	    value: function _loadLast() {
	      if (this.previousColor !== undefined) {
	        this.setColor(this.previousColor, false);
	      } else {
	        alert("There is no last color to load...");
	      }
	    }

	    
	  }, {
	    key: "_setColor",
	    value: function _setColor(rgba) {
	      var setInitial = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
	      
	      if (setInitial === true) {
	        this.initialColor = _Object$assign({}, rgba);
	      }
	      this.color = rgba;
	      var hsv = RGBToHSV(rgba.r, rgba.g, rgba.b);
	      var angleConvert = 2 * Math.PI;
	      var radius = this.r * hsv.s;
	      var x = this.centerCoordinates.x + radius * Math.sin(angleConvert * hsv.h);
	      var y = this.centerCoordinates.y + radius * Math.cos(angleConvert * hsv.h);
	      this.colorPickerSelector.style.left = x - 0.5 * this.colorPickerSelector.clientWidth + "px";
	      this.colorPickerSelector.style.top = y - 0.5 * this.colorPickerSelector.clientHeight + "px";
	      this._updatePicker(rgba);
	    }

	    
	  }, {
	    key: "_setOpacity",
	    value: function _setOpacity(value) {
	      this.color.a = value / 100;
	      this._updatePicker(this.color);
	    }

	    
	  }, {
	    key: "_setBrightness",
	    value: function _setBrightness(value) {
	      var hsv = RGBToHSV(this.color.r, this.color.g, this.color.b);
	      hsv.v = value / 100;
	      var rgba = HSVToRGB(hsv.h, hsv.s, hsv.v);
	      rgba["a"] = this.color.a;
	      this.color = rgba;
	      this._updatePicker();
	    }

	    
	  }, {
	    key: "_updatePicker",
	    value: function _updatePicker() {
	      var rgba = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.color;
	      var hsv = RGBToHSV(rgba.r, rgba.g, rgba.b);
	      var ctx = this.colorPickerCanvas.getContext("2d");
	      if (this.pixelRation === undefined) {
	        this.pixelRatio = (window.devicePixelRatio || 1) / (ctx.webkitBackingStorePixelRatio || ctx.mozBackingStorePixelRatio || ctx.msBackingStorePixelRatio || ctx.oBackingStorePixelRatio || ctx.backingStorePixelRatio || 1);
	      }
	      ctx.setTransform(this.pixelRatio, 0, 0, this.pixelRatio, 0, 0);

	      
	      var w = this.colorPickerCanvas.clientWidth;
	      var h = this.colorPickerCanvas.clientHeight;
	      ctx.clearRect(0, 0, w, h);
	      ctx.putImageData(this.hueCircle, 0, 0);
	      ctx.fillStyle = "rgba(0,0,0," + (1 - hsv.v) + ")";
	      ctx.circle(this.centerCoordinates.x, this.centerCoordinates.y, this.r);
	      _fillInstanceProperty(ctx).call(ctx);
	      this.brightnessRange.value = 100 * hsv.v;
	      this.opacityRange.value = 100 * rgba.a;
	      this.initialColorDiv.style.backgroundColor = "rgba(" + this.initialColor.r + "," + this.initialColor.g + "," + this.initialColor.b + "," + this.initialColor.a + ")";
	      this.newColorDiv.style.backgroundColor = "rgba(" + this.color.r + "," + this.color.g + "," + this.color.b + "," + this.color.a + ")";
	    }

	    
	  }, {
	    key: "_setSize",
	    value: function _setSize() {
	      this.colorPickerCanvas.style.width = "100%";
	      this.colorPickerCanvas.style.height = "100%";
	      this.colorPickerCanvas.width = 289 * this.pixelRatio;
	      this.colorPickerCanvas.height = 289 * this.pixelRatio;
	    }

	    
	  }, {
	    key: "_create",
	    value: function _create() {
	      var _context16, _context17, _context18, _context19;
	      this.frame = document.createElement("div");
	      this.frame.className = "vis-color-picker";
	      this.colorPickerDiv = document.createElement("div");
	      this.colorPickerSelector = document.createElement("div");
	      this.colorPickerSelector.className = "vis-selector";
	      this.colorPickerDiv.appendChild(this.colorPickerSelector);
	      this.colorPickerCanvas = document.createElement("canvas");
	      this.colorPickerDiv.appendChild(this.colorPickerCanvas);
	      if (!this.colorPickerCanvas.getContext) {
	        var noCanvas = document.createElement("DIV");
	        noCanvas.style.color = "red";
	        noCanvas.style.fontWeight = "bold";
	        noCanvas.style.padding = "10px";
	        noCanvas.innerText = "Error: your browser does not support HTML canvas";
	        this.colorPickerCanvas.appendChild(noCanvas);
	      } else {
	        var ctx = this.colorPickerCanvas.getContext("2d");
	        this.pixelRatio = (window.devicePixelRatio || 1) / (ctx.webkitBackingStorePixelRatio || ctx.mozBackingStorePixelRatio || ctx.msBackingStorePixelRatio || ctx.oBackingStorePixelRatio || ctx.backingStorePixelRatio || 1);
	        this.colorPickerCanvas.getContext("2d").setTransform(this.pixelRatio, 0, 0, this.pixelRatio, 0, 0);
	      }
	      this.colorPickerDiv.className = "vis-color";
	      this.opacityDiv = document.createElement("div");
	      this.opacityDiv.className = "vis-opacity";
	      this.brightnessDiv = document.createElement("div");
	      this.brightnessDiv.className = "vis-brightness";
	      this.arrowDiv = document.createElement("div");
	      this.arrowDiv.className = "vis-arrow";
	      this.opacityRange = document.createElement("input");
	      try {
	        this.opacityRange.type = "range"; 
	        this.opacityRange.min = "0";
	        this.opacityRange.max = "100";
	      } catch (err) {
	        
	      }
	      this.opacityRange.value = "100";
	      this.opacityRange.className = "vis-range";
	      this.brightnessRange = document.createElement("input");
	      try {
	        this.brightnessRange.type = "range"; 
	        this.brightnessRange.min = "0";
	        this.brightnessRange.max = "100";
	      } catch (err) {
	        
	      }
	      this.brightnessRange.value = "100";
	      this.brightnessRange.className = "vis-range";
	      this.opacityDiv.appendChild(this.opacityRange);
	      this.brightnessDiv.appendChild(this.brightnessRange);
	      var me = this;
	      this.opacityRange.onchange = function () {
	        me._setOpacity(this.value);
	      };
	      this.opacityRange.oninput = function () {
	        me._setOpacity(this.value);
	      };
	      this.brightnessRange.onchange = function () {
	        me._setBrightness(this.value);
	      };
	      this.brightnessRange.oninput = function () {
	        me._setBrightness(this.value);
	      };
	      this.brightnessLabel = document.createElement("div");
	      this.brightnessLabel.className = "vis-label vis-brightness";
	      this.brightnessLabel.innerText = "brightness:";
	      this.opacityLabel = document.createElement("div");
	      this.opacityLabel.className = "vis-label vis-opacity";
	      this.opacityLabel.innerText = "opacity:";
	      this.newColorDiv = document.createElement("div");
	      this.newColorDiv.className = "vis-new-color";
	      this.newColorDiv.innerText = "new";
	      this.initialColorDiv = document.createElement("div");
	      this.initialColorDiv.className = "vis-initial-color";
	      this.initialColorDiv.innerText = "initial";
	      this.cancelButton = document.createElement("div");
	      this.cancelButton.className = "vis-button vis-cancel";
	      this.cancelButton.innerText = "cancel";
	      this.cancelButton.onclick = _bindInstanceProperty(_context16 = this._hide).call(_context16, this, false);
	      this.applyButton = document.createElement("div");
	      this.applyButton.className = "vis-button vis-apply";
	      this.applyButton.innerText = "apply";
	      this.applyButton.onclick = _bindInstanceProperty(_context17 = this._apply).call(_context17, this);
	      this.saveButton = document.createElement("div");
	      this.saveButton.className = "vis-button vis-save";
	      this.saveButton.innerText = "save";
	      this.saveButton.onclick = _bindInstanceProperty(_context18 = this._save).call(_context18, this);
	      this.loadButton = document.createElement("div");
	      this.loadButton.className = "vis-button vis-load";
	      this.loadButton.innerText = "load last";
	      this.loadButton.onclick = _bindInstanceProperty(_context19 = this._loadLast).call(_context19, this);
	      this.frame.appendChild(this.colorPickerDiv);
	      this.frame.appendChild(this.arrowDiv);
	      this.frame.appendChild(this.brightnessLabel);
	      this.frame.appendChild(this.brightnessDiv);
	      this.frame.appendChild(this.opacityLabel);
	      this.frame.appendChild(this.opacityDiv);
	      this.frame.appendChild(this.newColorDiv);
	      this.frame.appendChild(this.initialColorDiv);
	      this.frame.appendChild(this.cancelButton);
	      this.frame.appendChild(this.applyButton);
	      this.frame.appendChild(this.saveButton);
	      this.frame.appendChild(this.loadButton);
	    }

	    
	  }, {
	    key: "_bindHammer",
	    value: function _bindHammer() {
	      var _this3 = this;
	      this.drag = {};
	      this.pinch = {};
	      this.hammer = new Hammer$1(this.colorPickerCanvas);
	      this.hammer.get("pinch").set({
	        enable: true
	      });
	      this.hammer.on("hammer.input", function (event) {
	        if (event.isFirst) {
	          _this3._moveSelector(event);
	        }
	      });
	      this.hammer.on("tap", function (event) {
	        _this3._moveSelector(event);
	      });
	      this.hammer.on("panstart", function (event) {
	        _this3._moveSelector(event);
	      });
	      this.hammer.on("panmove", function (event) {
	        _this3._moveSelector(event);
	      });
	      this.hammer.on("panend", function (event) {
	        _this3._moveSelector(event);
	      });
	    }

	    
	  }, {
	    key: "_generateHueCircle",
	    value: function _generateHueCircle() {
	      if (this.generated === false) {
	        var ctx = this.colorPickerCanvas.getContext("2d");
	        if (this.pixelRation === undefined) {
	          this.pixelRatio = (window.devicePixelRatio || 1) / (ctx.webkitBackingStorePixelRatio || ctx.mozBackingStorePixelRatio || ctx.msBackingStorePixelRatio || ctx.oBackingStorePixelRatio || ctx.backingStorePixelRatio || 1);
	        }
	        ctx.setTransform(this.pixelRatio, 0, 0, this.pixelRatio, 0, 0);

	        
	        var w = this.colorPickerCanvas.clientWidth;
	        var h = this.colorPickerCanvas.clientHeight;
	        ctx.clearRect(0, 0, w, h);

	        
	        var x, y, hue, sat;
	        this.centerCoordinates = {
	          x: w * 0.5,
	          y: h * 0.5
	        };
	        this.r = 0.49 * w;
	        var angleConvert = 2 * Math.PI / 360;
	        var hfac = 1 / 360;
	        var sfac = 1 / this.r;
	        var rgb;
	        for (hue = 0; hue < 360; hue++) {
	          for (sat = 0; sat < this.r; sat++) {
	            x = this.centerCoordinates.x + sat * Math.sin(angleConvert * hue);
	            y = this.centerCoordinates.y + sat * Math.cos(angleConvert * hue);
	            rgb = HSVToRGB(hue * hfac, sat * sfac, 1);
	            ctx.fillStyle = "rgb(" + rgb.r + "," + rgb.g + "," + rgb.b + ")";
	            ctx.fillRect(x - 0.5, y - 0.5, 2, 2);
	          }
	        }
	        ctx.strokeStyle = "rgba(0,0,0,1)";
	        ctx.circle(this.centerCoordinates.x, this.centerCoordinates.y, this.r);
	        ctx.stroke();
	        this.hueCircle = ctx.getImageData(0, 0, w, h);
	      }
	      this.generated = true;
	    }

	    
	  }, {
	    key: "_moveSelector",
	    value: function _moveSelector(event) {
	      var rect = this.colorPickerDiv.getBoundingClientRect();
	      var left = event.center.x - rect.left;
	      var top = event.center.y - rect.top;
	      var centerY = 0.5 * this.colorPickerDiv.clientHeight;
	      var centerX = 0.5 * this.colorPickerDiv.clientWidth;
	      var x = left - centerX;
	      var y = top - centerY;
	      var angle = Math.atan2(x, y);
	      var radius = 0.98 * Math.min(Math.sqrt(x * x + y * y), centerX);
	      var newTop = Math.cos(angle) * radius + centerY;
	      var newLeft = Math.sin(angle) * radius + centerX;
	      this.colorPickerSelector.style.top = newTop - 0.5 * this.colorPickerSelector.clientHeight + "px";
	      this.colorPickerSelector.style.left = newLeft - 0.5 * this.colorPickerSelector.clientWidth + "px";

	      
	      var h = angle / (2 * Math.PI);
	      h = h < 0 ? h + 1 : h;
	      var s = radius / this.r;
	      var hsv = RGBToHSV(this.color.r, this.color.g, this.color.b);
	      hsv.h = h;
	      hsv.s = s;
	      var rgba = HSVToRGB(hsv.h, hsv.s, hsv.v);
	      rgba["a"] = this.color.a;
	      this.color = rgba;

	      
	      this.initialColorDiv.style.backgroundColor = "rgba(" + this.initialColor.r + "," + this.initialColor.g + "," + this.initialColor.b + "," + this.initialColor.a + ")";
	      this.newColorDiv.style.backgroundColor = "rgba(" + this.color.r + "," + this.color.g + "," + this.color.b + "," + this.color.a + ")";
	    }
	  }]);
	  return ColorPicker$1;
	}();
	
	function wrapInTag() {
	  for (var _len5 = arguments.length, rest = new Array(_len5), _key5 = 0; _key5 < _len5; _key5++) {
	    rest[_key5] = arguments[_key5];
	  }
	  if (rest.length < 1) {
	    throw new TypeError("Invalid arguments.");
	  } else if (rest.length === 1) {
	    return document.createTextNode(rest[0]);
	  } else {
	    var element = document.createElement(rest[0]);
	    element.appendChild(wrapInTag.apply(void 0, _toConsumableArray(_sliceInstanceProperty(rest).call(rest, 1))));
	    return element;
	  }
	}

	
	var Configurator$1 = function () {
	  
	  function Configurator$1(parentModule, defaultContainer, configureOptions) {
	    var pixelRatio = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 1;
	    var hideOption = arguments.length > 4 && arguments[4] !== undefined ? arguments[4] : function () {
	      return false;
	    };
	    _classCallCheck(this, Configurator$1);
	    this.parent = parentModule;
	    this.changedOptions = [];
	    this.container = defaultContainer;
	    this.allowCreation = false;
	    this.hideOption = hideOption;
	    this.options = {};
	    this.initialized = false;
	    this.popupCounter = 0;
	    this.defaultOptions = {
	      enabled: false,
	      filter: true,
	      container: undefined,
	      showButton: true
	    };
	    _Object$assign(this.options, this.defaultOptions);
	    this.configureOptions = configureOptions;
	    this.moduleOptions = {};
	    this.domElements = [];
	    this.popupDiv = {};
	    this.popupLimit = 5;
	    this.popupHistory = {};
	    this.colorPicker = new ColorPicker$1(pixelRatio);
	    this.wrapper = undefined;
	  }

	  
	  _createClass(Configurator$1, [{
	    key: "setOptions",
	    value: function setOptions(options) {
	      if (options !== undefined) {
	        
	        this.popupHistory = {};
	        this._removePopup();
	        var enabled = true;
	        if (typeof options === "string") {
	          this.options.filter = options;
	        } else if (_Array$isArray$1(options)) {
	          this.options.filter = options.join();
	        } else if (_typeof(options) === "object") {
	          if (options == null) {
	            throw new TypeError("options cannot be null");
	          }
	          if (options.container !== undefined) {
	            this.options.container = options.container;
	          }
	          if (_filterInstanceProperty(options) !== undefined) {
	            this.options.filter = _filterInstanceProperty(options);
	          }
	          if (options.showButton !== undefined) {
	            this.options.showButton = options.showButton;
	          }
	          if (options.enabled !== undefined) {
	            enabled = options.enabled;
	          }
	        } else if (typeof options === "boolean") {
	          this.options.filter = true;
	          enabled = options;
	        } else if (typeof options === "function") {
	          this.options.filter = options;
	          enabled = true;
	        }
	        if (_filterInstanceProperty(this.options) === false) {
	          enabled = false;
	        }
	        this.options.enabled = enabled;
	      }
	      this._clean();
	    }

	    
	  }, {
	    key: "setModuleOptions",
	    value: function setModuleOptions(moduleOptions) {
	      this.moduleOptions = moduleOptions;
	      if (this.options.enabled === true) {
	        this._clean();
	        if (this.options.container !== undefined) {
	          this.container = this.options.container;
	        }
	        this._create();
	      }
	    }

	    
	  }, {
	    key: "_create",
	    value: function _create() {
	      this._clean();
	      this.changedOptions = [];
	      var filter = _filterInstanceProperty(this.options);
	      var counter = 0;
	      var show = false;
	      for (var _option in this.configureOptions) {
	        if (Object.prototype.hasOwnProperty.call(this.configureOptions, _option)) {
	          this.allowCreation = false;
	          show = false;
	          if (typeof filter === "function") {
	            show = filter(_option, []);
	            show = show || this._handleObject(this.configureOptions[_option], [_option], true);
	          } else if (filter === true || _indexOfInstanceProperty(filter).call(filter, _option) !== -1) {
	            show = true;
	          }
	          if (show !== false) {
	            this.allowCreation = true;

	            
	            if (counter > 0) {
	              this._makeItem([]);
	            }
	            
	            this._makeHeader(_option);

	            
	            this._handleObject(this.configureOptions[_option], [_option]);
	          }
	          counter++;
	        }
	      }
	      this._makeButton();
	      this._push();
	      
	    }

	    
	  }, {
	    key: "_push",
	    value: function _push() {
	      this.wrapper = document.createElement("div");
	      this.wrapper.className = "vis-configuration-wrapper";
	      this.container.appendChild(this.wrapper);
	      for (var i = 0; i < this.domElements.length; i++) {
	        this.wrapper.appendChild(this.domElements[i]);
	      }
	      this._showPopupIfNeeded();
	    }

	    
	  }, {
	    key: "_clean",
	    value: function _clean() {
	      for (var i = 0; i < this.domElements.length; i++) {
	        this.wrapper.removeChild(this.domElements[i]);
	      }
	      if (this.wrapper !== undefined) {
	        this.container.removeChild(this.wrapper);
	        this.wrapper = undefined;
	      }
	      this.domElements = [];
	      this._removePopup();
	    }

	    
	  }, {
	    key: "_getValue",
	    value: function _getValue(path) {
	      var base = this.moduleOptions;
	      for (var i = 0; i < path.length; i++) {
	        if (base[path[i]] !== undefined) {
	          base = base[path[i]];
	        } else {
	          base = undefined;
	          break;
	        }
	      }
	      return base;
	    }

	    
	  }, {
	    key: "_makeItem",
	    value: function _makeItem(path) {
	      if (this.allowCreation === true) {
	        var item = document.createElement("div");
	        item.className = "vis-configuration vis-config-item vis-config-s" + path.length;
	        for (var _len6 = arguments.length, domElements = new Array(_len6 > 1 ? _len6 - 1 : 0), _key6 = 1; _key6 < _len6; _key6++) {
	          domElements[_key6 - 1] = arguments[_key6];
	        }
	        _forEachInstanceProperty(domElements).call(domElements, function (element) {
	          item.appendChild(element);
	        });
	        this.domElements.push(item);
	        return this.domElements.length;
	      }
	      return 0;
	    }

	    
	  }, {
	    key: "_makeHeader",
	    value: function _makeHeader(name) {
	      var div = document.createElement("div");
	      div.className = "vis-configuration vis-config-header";
	      div.innerText = name;
	      this._makeItem([], div);
	    }

	    
	  }, {
	    key: "_makeLabel",
	    value: function _makeLabel(name, path) {
	      var objectLabel = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
	      var div = document.createElement("div");
	      div.className = "vis-configuration vis-config-label vis-config-s" + path.length;
	      if (objectLabel === true) {
	        while (div.firstChild) {
	          div.removeChild(div.firstChild);
	        }
	        div.appendChild(wrapInTag("i", "b", name));
	      } else {
	        div.innerText = name + ":";
	      }
	      return div;
	    }

	    
	  }, {
	    key: "_makeDropdown",
	    value: function _makeDropdown(arr, value, path) {
	      var select = document.createElement("select");
	      select.className = "vis-configuration vis-config-select";
	      var selectedValue = 0;
	      if (value !== undefined) {
	        if (_indexOfInstanceProperty(arr).call(arr, value) !== -1) {
	          selectedValue = _indexOfInstanceProperty(arr).call(arr, value);
	        }
	      }
	      for (var i = 0; i < arr.length; i++) {
	        var _option2 = document.createElement("option");
	        _option2.value = arr[i];
	        if (i === selectedValue) {
	          _option2.selected = "selected";
	        }
	        _option2.innerText = arr[i];
	        select.appendChild(_option2);
	      }
	      var me = this;
	      select.onchange = function () {
	        me._update(this.value, path);
	      };
	      var label = this._makeLabel(path[path.length - 1], path);
	      this._makeItem(path, label, select);
	    }

	    
	  }, {
	    key: "_makeRange",
	    value: function _makeRange(arr, value, path) {
	      var defaultValue = arr[0];
	      var min = arr[1];
	      var max = arr[2];
	      var step = arr[3];
	      var range = document.createElement("input");
	      range.className = "vis-configuration vis-config-range";
	      try {
	        range.type = "range"; 
	        range.min = min;
	        range.max = max;
	      } catch (err) {
	        
	      }
	      range.step = step;

	      
	      var popupString = "";
	      var popupValue = 0;
	      if (value !== undefined) {
	        var factor = 1.2;
	        if (value < 0 && value * factor < min) {
	          range.min = Math.ceil(value * factor);
	          popupValue = range.min;
	          popupString = "range increased";
	        } else if (value / factor < min) {
	          range.min = Math.ceil(value / factor);
	          popupValue = range.min;
	          popupString = "range increased";
	        }
	        if (value * factor > max && max !== 1) {
	          range.max = Math.ceil(value * factor);
	          popupValue = range.max;
	          popupString = "range increased";
	        }
	        range.value = value;
	      } else {
	        range.value = defaultValue;
	      }
	      var input = document.createElement("input");
	      input.className = "vis-configuration vis-config-rangeinput";
	      input.value = range.value;
	      var me = this;
	      range.onchange = function () {
	        input.value = this.value;
	        me._update(Number(this.value), path);
	      };
	      range.oninput = function () {
	        input.value = this.value;
	      };
	      var label = this._makeLabel(path[path.length - 1], path);
	      var itemIndex = this._makeItem(path, label, range, input);

	      
	      if (popupString !== "" && this.popupHistory[itemIndex] !== popupValue) {
	        this.popupHistory[itemIndex] = popupValue;
	        this._setupPopup(popupString, itemIndex);
	      }
	    }

	    
	  }, {
	    key: "_makeButton",
	    value: function _makeButton() {
	      var _this4 = this;
	      if (this.options.showButton === true) {
	        var generateButton = document.createElement("div");
	        generateButton.className = "vis-configuration vis-config-button";
	        generateButton.innerText = "generate options";
	        generateButton.onclick = function () {
	          _this4._printOptions();
	        };
	        generateButton.onmouseover = function () {
	          generateButton.className = "vis-configuration vis-config-button hover";
	        };
	        generateButton.onmouseout = function () {
	          generateButton.className = "vis-configuration vis-config-button";
	        };
	        this.optionsContainer = document.createElement("div");
	        this.optionsContainer.className = "vis-configuration vis-config-option-container";
	        this.domElements.push(this.optionsContainer);
	        this.domElements.push(generateButton);
	      }
	    }

	    
	  }, {
	    key: "_setupPopup",
	    value: function _setupPopup(string, index) {
	      var _this5 = this;
	      if (this.initialized === true && this.allowCreation === true && this.popupCounter < this.popupLimit) {
	        var div = document.createElement("div");
	        div.id = "vis-configuration-popup";
	        div.className = "vis-configuration-popup";
	        div.innerText = string;
	        div.onclick = function () {
	          _this5._removePopup();
	        };
	        this.popupCounter += 1;
	        this.popupDiv = {
	          html: div,
	          index: index
	        };
	      }
	    }

	    
	  }, {
	    key: "_removePopup",
	    value: function _removePopup() {
	      if (this.popupDiv.html !== undefined) {
	        this.popupDiv.html.parentNode.removeChild(this.popupDiv.html);
	        clearTimeout(this.popupDiv.hideTimeout);
	        clearTimeout(this.popupDiv.deleteTimeout);
	        this.popupDiv = {};
	      }
	    }

	    
	  }, {
	    key: "_showPopupIfNeeded",
	    value: function _showPopupIfNeeded() {
	      var _this6 = this;
	      if (this.popupDiv.html !== undefined) {
	        var correspondingElement = this.domElements[this.popupDiv.index];
	        var rect = correspondingElement.getBoundingClientRect();
	        this.popupDiv.html.style.left = rect.left + "px";
	        this.popupDiv.html.style.top = rect.top - 30 + "px"; 
	        document.body.appendChild(this.popupDiv.html);
	        this.popupDiv.hideTimeout = _setTimeout(function () {
	          _this6.popupDiv.html.style.opacity = 0;
	        }, 1500);
	        this.popupDiv.deleteTimeout = _setTimeout(function () {
	          _this6._removePopup();
	        }, 1800);
	      }
	    }

	    
	  }, {
	    key: "_makeCheckbox",
	    value: function _makeCheckbox(defaultValue, value, path) {
	      var checkbox = document.createElement("input");
	      checkbox.type = "checkbox";
	      checkbox.className = "vis-configuration vis-config-checkbox";
	      checkbox.checked = defaultValue;
	      if (value !== undefined) {
	        checkbox.checked = value;
	        if (value !== defaultValue) {
	          if (_typeof(defaultValue) === "object") {
	            if (value !== defaultValue.enabled) {
	              this.changedOptions.push({
	                path: path,
	                value: value
	              });
	            }
	          } else {
	            this.changedOptions.push({
	              path: path,
	              value: value
	            });
	          }
	        }
	      }
	      var me = this;
	      checkbox.onchange = function () {
	        me._update(this.checked, path);
	      };
	      var label = this._makeLabel(path[path.length - 1], path);
	      this._makeItem(path, label, checkbox);
	    }

	    
	  }, {
	    key: "_makeTextInput",
	    value: function _makeTextInput(defaultValue, value, path) {
	      var checkbox = document.createElement("input");
	      checkbox.type = "text";
	      checkbox.className = "vis-configuration vis-config-text";
	      checkbox.value = value;
	      if (value !== defaultValue) {
	        this.changedOptions.push({
	          path: path,
	          value: value
	        });
	      }
	      var me = this;
	      checkbox.onchange = function () {
	        me._update(this.value, path);
	      };
	      var label = this._makeLabel(path[path.length - 1], path);
	      this._makeItem(path, label, checkbox);
	    }

	    
	  }, {
	    key: "_makeColorField",
	    value: function _makeColorField(arr, value, path) {
	      var _this7 = this;
	      var defaultColor = arr[1];
	      var div = document.createElement("div");
	      value = value === undefined ? defaultColor : value;
	      if (value !== "none") {
	        div.className = "vis-configuration vis-config-colorBlock";
	        div.style.backgroundColor = value;
	      } else {
	        div.className = "vis-configuration vis-config-colorBlock none";
	      }
	      value = value === undefined ? defaultColor : value;
	      div.onclick = function () {
	        _this7._showColorPicker(value, div, path);
	      };
	      var label = this._makeLabel(path[path.length - 1], path);
	      this._makeItem(path, label, div);
	    }

	    
	  }, {
	    key: "_showColorPicker",
	    value: function _showColorPicker(value, div, path) {
	      var _this8 = this;
	      
	      div.onclick = function () {};
	      this.colorPicker.insertTo(div);
	      this.colorPicker.show();
	      this.colorPicker.setColor(value);
	      this.colorPicker.setUpdateCallback(function (color) {
	        var colorString = "rgba(" + color.r + "," + color.g + "," + color.b + "," + color.a + ")";
	        div.style.backgroundColor = colorString;
	        _this8._update(colorString, path);
	      });

	      
	      this.colorPicker.setCloseCallback(function () {
	        div.onclick = function () {
	          _this8._showColorPicker(value, div, path);
	        };
	      });
	    }

	    
	  }, {
	    key: "_handleObject",
	    value: function _handleObject(obj) {
	      var path = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
	      var checkOnly = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
	      var show = false;
	      var filter = _filterInstanceProperty(this.options);
	      var visibleInSet = false;
	      for (var subObj in obj) {
	        if (Object.prototype.hasOwnProperty.call(obj, subObj)) {
	          show = true;
	          var item = obj[subObj];
	          var newPath = copyAndExtendArray(path, subObj);
	          if (typeof filter === "function") {
	            show = filter(subObj, path);

	            
	            if (show === false) {
	              if (!_Array$isArray$1(item) && typeof item !== "string" && typeof item !== "boolean" && item instanceof Object) {
	                this.allowCreation = false;
	                show = this._handleObject(item, newPath, true);
	                this.allowCreation = checkOnly === false;
	              }
	            }
	          }
	          if (show !== false) {
	            visibleInSet = true;
	            var value = this._getValue(newPath);
	            if (_Array$isArray$1(item)) {
	              this._handleArray(item, value, newPath);
	            } else if (typeof item === "string") {
	              this._makeTextInput(item, value, newPath);
	            } else if (typeof item === "boolean") {
	              this._makeCheckbox(item, value, newPath);
	            } else if (item instanceof Object) {
	              
	              if (!this.hideOption(path, subObj, this.moduleOptions)) {
	                
	                if (item.enabled !== undefined) {
	                  var enabledPath = copyAndExtendArray(newPath, "enabled");
	                  var enabledValue = this._getValue(enabledPath);
	                  if (enabledValue === true) {
	                    var label = this._makeLabel(subObj, newPath, true);
	                    this._makeItem(newPath, label);
	                    visibleInSet = this._handleObject(item, newPath) || visibleInSet;
	                  } else {
	                    this._makeCheckbox(item, enabledValue, newPath);
	                  }
	                } else {
	                  var _label = this._makeLabel(subObj, newPath, true);
	                  this._makeItem(newPath, _label);
	                  visibleInSet = this._handleObject(item, newPath) || visibleInSet;
	                }
	              }
	            } else {
	              console.error("dont know how to handle", item, subObj, newPath);
	            }
	          }
	        }
	      }
	      return visibleInSet;
	    }

	    
	  }, {
	    key: "_handleArray",
	    value: function _handleArray(arr, value, path) {
	      if (typeof arr[0] === "string" && arr[0] === "color") {
	        this._makeColorField(arr, value, path);
	        if (arr[1] !== value) {
	          this.changedOptions.push({
	            path: path,
	            value: value
	          });
	        }
	      } else if (typeof arr[0] === "string") {
	        this._makeDropdown(arr, value, path);
	        if (arr[0] !== value) {
	          this.changedOptions.push({
	            path: path,
	            value: value
	          });
	        }
	      } else if (typeof arr[0] === "number") {
	        this._makeRange(arr, value, path);
	        if (arr[0] !== value) {
	          this.changedOptions.push({
	            path: path,
	            value: Number(value)
	          });
	        }
	      }
	    }

	    
	  }, {
	    key: "_update",
	    value: function _update(value, path) {
	      var options = this._constructOptions(value, path);
	      if (this.parent.body && this.parent.body.emitter && this.parent.body.emitter.emit) {
	        this.parent.body.emitter.emit("configChange", options);
	      }
	      this.initialized = true;
	      this.parent.setOptions(options);
	    }

	    
	  }, {
	    key: "_constructOptions",
	    value: function _constructOptions(value, path) {
	      var optionsObj = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
	      var pointer = optionsObj;

	      
	      value = value === "true" ? true : value;
	      value = value === "false" ? false : value;
	      for (var i = 0; i < path.length; i++) {
	        if (path[i] !== "global") {
	          if (pointer[path[i]] === undefined) {
	            pointer[path[i]] = {};
	          }
	          if (i !== path.length - 1) {
	            pointer = pointer[path[i]];
	          } else {
	            pointer[path[i]] = value;
	          }
	        }
	      }
	      return optionsObj;
	    }

	    
	  }, {
	    key: "_printOptions",
	    value: function _printOptions() {
	      var options = this.getOptions();
	      while (this.optionsContainer.firstChild) {
	        this.optionsContainer.removeChild(this.optionsContainer.firstChild);
	      }
	      this.optionsContainer.appendChild(wrapInTag("pre", "const options = " + _JSON$stringify(options, null, 2)));
	    }

	    
	  }, {
	    key: "getOptions",
	    value: function getOptions() {
	      var options = {};
	      for (var i = 0; i < this.changedOptions.length; i++) {
	        this._constructOptions(this.changedOptions[i].value, this.changedOptions[i].path, options);
	      }
	      return options;
	    }
	  }]);
	  return Configurator$1;
	}();
	
	var Popup$1 = function () {
	  
	  function Popup$1(container, overflowMethod) {
	    _classCallCheck(this, Popup$1);
	    this.container = container;
	    this.overflowMethod = overflowMethod || "cap";
	    this.x = 0;
	    this.y = 0;
	    this.padding = 5;
	    this.hidden = false;

	    
	    this.frame = document.createElement("div");
	    this.frame.className = "vis-tooltip";
	    this.container.appendChild(this.frame);
	  }

	  
	  _createClass(Popup$1, [{
	    key: "setPosition",
	    value: function setPosition(x, y) {
	      this.x = _parseInt(x);
	      this.y = _parseInt(y);
	    }

	    
	  }, {
	    key: "setText",
	    value: function setText(content) {
	      if (content instanceof Element) {
	        while (this.frame.firstChild) {
	          this.frame.removeChild(this.frame.firstChild);
	        }
	        this.frame.appendChild(content);
	      } else {
	        
	        
	        this.frame.innerText = content;
	      }
	    }

	    
	  }, {
	    key: "show",
	    value: function show(doShow) {
	      if (doShow === undefined) {
	        doShow = true;
	      }
	      if (doShow === true) {
	        var height = this.frame.clientHeight;
	        var width = this.frame.clientWidth;
	        var maxHeight = this.frame.parentNode.clientHeight;
	        var maxWidth = this.frame.parentNode.clientWidth;
	        var left = 0,
	          top = 0;
	        if (this.overflowMethod == "flip") {
	          var isLeft = false,
	            isTop = true; 

	          if (this.y - height < this.padding) {
	            isTop = false;
	          }
	          if (this.x + width > maxWidth - this.padding) {
	            isLeft = true;
	          }
	          if (isLeft) {
	            left = this.x - width;
	          } else {
	            left = this.x;
	          }
	          if (isTop) {
	            top = this.y - height;
	          } else {
	            top = this.y;
	          }
	        } else {
	          top = this.y - height;
	          if (top + height + this.padding > maxHeight) {
	            top = maxHeight - height - this.padding;
	          }
	          if (top < this.padding) {
	            top = this.padding;
	          }
	          left = this.x;
	          if (left + width + this.padding > maxWidth) {
	            left = maxWidth - width - this.padding;
	          }
	          if (left < this.padding) {
	            left = this.padding;
	          }
	        }
	        this.frame.style.left = left + "px";
	        this.frame.style.top = top + "px";
	        this.frame.style.visibility = "visible";
	        this.hidden = false;
	      } else {
	        this.hide();
	      }
	    }

	    
	  }, {
	    key: "hide",
	    value: function hide() {
	      this.hidden = true;
	      this.frame.style.left = "0";
	      this.frame.style.top = "0";
	      this.frame.style.visibility = "hidden";
	    }

	    
	  }, {
	    key: "destroy",
	    value: function destroy() {
	      this.frame.parentNode.removeChild(this.frame); 
	    }
	  }]);
	  return Popup$1;
	}();
	var errorFound$1 = false;
	var allOptions$3;
	var VALIDATOR_PRINT_STYLE$1 = "background: #FFeeee; color: #dd0000";

	
	var Validator$1 = function () {
	  function Validator$1() {
	    _classCallCheck(this, Validator$1);
	  }
	  _createClass(Validator$1, null, [{
	    key: "validate",
	    value:
	    
	    function validate(options, referenceOptions, subObject) {
	      errorFound$1 = false;
	      allOptions$3 = referenceOptions;
	      var usedOptions = referenceOptions;
	      if (subObject !== undefined) {
	        usedOptions = referenceOptions[subObject];
	      }
	      Validator$1.parse(options, usedOptions, []);
	      return errorFound$1;
	    }

	    
	  }, {
	    key: "parse",
	    value: function parse(options, referenceOptions, path) {
	      for (var _option3 in options) {
	        if (Object.prototype.hasOwnProperty.call(options, _option3)) {
	          Validator$1.check(_option3, options, referenceOptions, path);
	        }
	      }
	    }

	    
	  }, {
	    key: "check",
	    value: function check(option, options, referenceOptions, path) {
	      if (referenceOptions[option] === undefined && referenceOptions.__any__ === undefined) {
	        Validator$1.getSuggestion(option, referenceOptions, path);
	        return;
	      }
	      var referenceOption = option;
	      var is_object = true;
	      if (referenceOptions[option] === undefined && referenceOptions.__any__ !== undefined) {
	        
	        
	        

	        
	        referenceOption = "__any__";

	        
	        
	        is_object = Validator$1.getType(options[option]) === "object";
	      }
	      var refOptionObj = referenceOptions[referenceOption];
	      if (is_object && refOptionObj.__type__ !== undefined) {
	        refOptionObj = refOptionObj.__type__;
	      }
	      Validator$1.checkFields(option, options, referenceOptions, referenceOption, refOptionObj, path);
	    }

	    
	  }, {
	    key: "checkFields",
	    value: function checkFields(option, options, referenceOptions, referenceOption, refOptionObj, path) {
	      var log = function log(message) {
	        console.error("%c" + message + Validator$1.printLocation(path, option), VALIDATOR_PRINT_STYLE$1);
	      };
	      var optionType = Validator$1.getType(options[option]);
	      var refOptionType = refOptionObj[optionType];
	      if (refOptionType !== undefined) {
	        
	        if (Validator$1.getType(refOptionType) === "array" && _indexOfInstanceProperty(refOptionType).call(refOptionType, options[option]) === -1) {
	          log('Invalid option detected in "' + option + '".' + " Allowed values are:" + Validator$1.print(refOptionType) + ' not "' + options[option] + '". ');
	          errorFound$1 = true;
	        } else if (optionType === "object" && referenceOption !== "__any__") {
	          path = copyAndExtendArray(path, option);
	          Validator$1.parse(options[option], referenceOptions[referenceOption], path);
	        }
	      } else if (refOptionObj["any"] === undefined) {
	        
	        log('Invalid type received for "' + option + '". Expected: ' + Validator$1.print(_Object$keys(refOptionObj)) + ". Received [" + optionType + '] "' + options[option] + '"');
	        errorFound$1 = true;
	      }
	    }

	    
	  }, {
	    key: "getType",
	    value: function getType(object) {
	      var type = _typeof(object);
	      if (type === "object") {
	        if (object === null) {
	          return "null";
	        }
	        if (object instanceof Boolean) {
	          return "boolean";
	        }
	        if (object instanceof Number) {
	          return "number";
	        }
	        if (object instanceof String) {
	          return "string";
	        }
	        if (_Array$isArray$1(object)) {
	          return "array";
	        }
	        if (object instanceof Date) {
	          return "date";
	        }
	        if (object.nodeType !== undefined) {
	          return "dom";
	        }
	        if (object._isAMomentObject === true) {
	          return "moment";
	        }
	        return "object";
	      } else if (type === "number") {
	        return "number";
	      } else if (type === "boolean") {
	        return "boolean";
	      } else if (type === "string") {
	        return "string";
	      } else if (type === undefined) {
	        return "undefined";
	      }
	      return type;
	    }

	    
	  }, {
	    key: "getSuggestion",
	    value: function getSuggestion(option, options, path) {
	      var localSearch = Validator$1.findInOptions(option, options, path, false);
	      var globalSearch = Validator$1.findInOptions(option, allOptions$3, [], true);
	      var localSearchThreshold = 8;
	      var globalSearchThreshold = 4;
	      var msg;
	      if (localSearch.indexMatch !== undefined) {
	        msg = " in " + Validator$1.printLocation(localSearch.path, option, "") + 'Perhaps it was incomplete? Did you mean: "' + localSearch.indexMatch + '"?\n\n';
	      } else if (globalSearch.distance <= globalSearchThreshold && localSearch.distance > globalSearch.distance) {
	        msg = " in " + Validator$1.printLocation(localSearch.path, option, "") + "Perhaps it was misplaced? Matching option found at: " + Validator$1.printLocation(globalSearch.path, globalSearch.closestMatch, "");
	      } else if (localSearch.distance <= localSearchThreshold) {
	        msg = '. Did you mean "' + localSearch.closestMatch + '"?' + Validator$1.printLocation(localSearch.path, option);
	      } else {
	        msg = ". Did you mean one of these: " + Validator$1.print(_Object$keys(options)) + Validator$1.printLocation(path, option);
	      }
	      console.error('%cUnknown option detected: "' + option + '"' + msg, VALIDATOR_PRINT_STYLE$1);
	      errorFound$1 = true;
	    }

	    
	  }, {
	    key: "findInOptions",
	    value: function findInOptions(option, options, path) {
	      var recursive = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
	      var min = 1e9;
	      var closestMatch = "";
	      var closestMatchPath = [];
	      var lowerCaseOption = option.toLowerCase();
	      var indexMatch = undefined;
	      for (var op in options) {
	        var distance = void 0;
	        if (options[op].__type__ !== undefined && recursive === true) {
	          var result = Validator$1.findInOptions(option, options[op], copyAndExtendArray(path, op));
	          if (min > result.distance) {
	            closestMatch = result.closestMatch;
	            closestMatchPath = result.path;
	            min = result.distance;
	            indexMatch = result.indexMatch;
	          }
	        } else {
	          var _context20;
	          if (_indexOfInstanceProperty(_context20 = op.toLowerCase()).call(_context20, lowerCaseOption) !== -1) {
	            indexMatch = op;
	          }
	          distance = Validator$1.levenshteinDistance(option, op);
	          if (min > distance) {
	            closestMatch = op;
	            closestMatchPath = copyArray(path);
	            min = distance;
	          }
	        }
	      }
	      return {
	        closestMatch: closestMatch,
	        path: closestMatchPath,
	        distance: min,
	        indexMatch: indexMatch
	      };
	    }

	    
	  }, {
	    key: "printLocation",
	    value: function printLocation(path, option) {
	      var prefix = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : "Problem value found at: \n";
	      var str = "\n\n" + prefix + "options = {\n";
	      for (var i = 0; i < path.length; i++) {
	        for (var j = 0; j < i + 1; j++) {
	          str += "  ";
	        }
	        str += path[i] + ": {\n";
	      }
	      for (var _j = 0; _j < path.length + 1; _j++) {
	        str += "  ";
	      }
	      str += option + "\n";
	      for (var _i3 = 0; _i3 < path.length + 1; _i3++) {
	        for (var _j2 = 0; _j2 < path.length - _i3; _j2++) {
	          str += "  ";
	        }
	        str += "}\n";
	      }
	      return str + "\n\n";
	    }

	    
	  }, {
	    key: "print",
	    value: function print(options) {
	      return _JSON$stringify(options).replace(/(")|(\[)|(\])|(,"__type__")/g, "").replace(/(,)/g, ", ");
	    }

	    
	  }, {
	    key: "levenshteinDistance",
	    value: function levenshteinDistance(a, b) {
	      if (a.length === 0) return b.length;
	      if (b.length === 0) return a.length;
	      var matrix = [];

	      
	      var i;
	      for (i = 0; i <= b.length; i++) {
	        matrix[i] = [i];
	      }

	      
	      var j;
	      for (j = 0; j <= a.length; j++) {
	        matrix[0][j] = j;
	      }

	      
	      for (i = 1; i <= b.length; i++) {
	        for (j = 1; j <= a.length; j++) {
	          if (b.charAt(i - 1) == a.charAt(j - 1)) {
	            matrix[i][j] = matrix[i - 1][j - 1];
	          } else {
	            matrix[i][j] = Math.min(matrix[i - 1][j - 1] + 1,
	            
	            Math.min(matrix[i][j - 1] + 1,
	            
	            matrix[i - 1][j] + 1)); 
	          }
	        }
	      }

	      return matrix[b.length][a.length];
	    }
	  }]);
	  return Validator$1;
	}();
	var Activator$2 = Activator$1;
	var ColorPicker$2 = ColorPicker$1;
	var Configurator$2 = Configurator$1;
	var Hammer$2 = Hammer$1;
	var Popup$2 = Popup$1;
	var VALIDATOR_PRINT_STYLE = VALIDATOR_PRINT_STYLE$1;
	var Validator$2 = Validator$1;

	var util$2 = Object.freeze({
		__proto__: null,
		Activator: Activator$2,
		Alea: Alea,
		ColorPicker: ColorPicker$2,
		Configurator: Configurator$2,
		DELETE: DELETE,
		HSVToHex: HSVToHex,
		HSVToRGB: HSVToRGB,
		Hammer: Hammer$2,
		Popup: Popup$2,
		RGBToHSV: RGBToHSV,
		RGBToHex: RGBToHex,
		VALIDATOR_PRINT_STYLE: VALIDATOR_PRINT_STYLE,
		Validator: Validator$2,
		addClassName: addClassName,
		addCssText: addCssText,
		addEventListener: addEventListener,
		binarySearchCustom: binarySearchCustom,
		binarySearchValue: binarySearchValue,
		bridgeObject: bridgeObject,
		copyAndExtendArray: copyAndExtendArray,
		copyArray: copyArray,
		deepExtend: deepExtend,
		deepObjectAssign: deepObjectAssign,
		easingFunctions: easingFunctions,
		equalArray: equalArray,
		extend: extend,
		fillIfDefined: fillIfDefined,
		forEach: forEach$1,
		getAbsoluteLeft: getAbsoluteLeft,
		getAbsoluteRight: getAbsoluteRight,
		getAbsoluteTop: getAbsoluteTop,
		getScrollBarWidth: getScrollBarWidth,
		getTarget: getTarget,
		getType: getType,
		hasParent: hasParent,
		hexToHSV: hexToHSV,
		hexToRGB: hexToRGB,
		insertSort: insertSort,
		isDate: isDate,
		isNumber: isNumber,
		isObject: isObject$3,
		isString: isString,
		isValidHex: isValidHex,
		isValidRGB: isValidRGB,
		isValidRGBA: isValidRGBA,
		mergeOptions: mergeOptions,
		option: option,
		overrideOpacity: overrideOpacity,
		parseColor: parseColor,
		preventDefault: preventDefault,
		pureDeepObjectAssign: pureDeepObjectAssign,
		recursiveDOMDelete: recursiveDOMDelete,
		removeClassName: removeClassName,
		removeCssText: removeCssText,
		removeEventListener: removeEventListener,
		selectiveBridgeObject: selectiveBridgeObject,
		selectiveDeepExtend: selectiveDeepExtend,
		selectiveExtend: selectiveExtend,
		selectiveNotDeepExtend: selectiveNotDeepExtend,
		throttle: throttle,
		toArray: toArray,
		topMost: topMost,
		updateProperty: updateProperty
	});

	var libExports$1 = {};
	var lib$1 = {
	  get exports(){ return libExports$1; },
	  set exports(v){ libExports$1 = v; },
	};

	var _default$1 = {};

	var libExports = {};
	var lib = {
	  get exports(){ return libExports; },
	  set exports(v){ libExports = v; },
	};

	var _default = {};

	

	function getDefaultWhiteList$1 () {
	  
	  
	  
	  
	  
	  var whiteList = {};

	  whiteList['align-content'] = false; 
	  whiteList['align-items'] = false; 
	  whiteList['align-self'] = false; 
	  whiteList['alignment-adjust'] = false; 
	  whiteList['alignment-baseline'] = false; 
	  whiteList['all'] = false; 
	  whiteList['anchor-point'] = false; 
	  whiteList['animation'] = false; 
	  whiteList['animation-delay'] = false; 
	  whiteList['animation-direction'] = false; 
	  whiteList['animation-duration'] = false; 
	  whiteList['animation-fill-mode'] = false; 
	  whiteList['animation-iteration-count'] = false; 
	  whiteList['animation-name'] = false; 
	  whiteList['animation-play-state'] = false; 
	  whiteList['animation-timing-function'] = false; 
	  whiteList['azimuth'] = false; 
	  whiteList['backface-visibility'] = false; 
	  whiteList['background'] = true; 
	  whiteList['background-attachment'] = true; 
	  whiteList['background-clip'] = true; 
	  whiteList['background-color'] = true; 
	  whiteList['background-image'] = true; 
	  whiteList['background-origin'] = true; 
	  whiteList['background-position'] = true; 
	  whiteList['background-repeat'] = true; 
	  whiteList['background-size'] = true; 
	  whiteList['baseline-shift'] = false; 
	  whiteList['binding'] = false; 
	  whiteList['bleed'] = false; 
	  whiteList['bookmark-label'] = false; 
	  whiteList['bookmark-level'] = false; 
	  whiteList['bookmark-state'] = false; 
	  whiteList['border'] = true; 
	  whiteList['border-bottom'] = true; 
	  whiteList['border-bottom-color'] = true; 
	  whiteList['border-bottom-left-radius'] = true; 
	  whiteList['border-bottom-right-radius'] = true; 
	  whiteList['border-bottom-style'] = true; 
	  whiteList['border-bottom-width'] = true; 
	  whiteList['border-collapse'] = true; 
	  whiteList['border-color'] = true; 
	  whiteList['border-image'] = true; 
	  whiteList['border-image-outset'] = true; 
	  whiteList['border-image-repeat'] = true; 
	  whiteList['border-image-slice'] = true; 
	  whiteList['border-image-source'] = true; 
	  whiteList['border-image-width'] = true; 
	  whiteList['border-left'] = true; 
	  whiteList['border-left-color'] = true; 
	  whiteList['border-left-style'] = true; 
	  whiteList['border-left-width'] = true; 
	  whiteList['border-radius'] = true; 
	  whiteList['border-right'] = true; 
	  whiteList['border-right-color'] = true; 
	  whiteList['border-right-style'] = true; 
	  whiteList['border-right-width'] = true; 
	  whiteList['border-spacing'] = true; 
	  whiteList['border-style'] = true; 
	  whiteList['border-top'] = true; 
	  whiteList['border-top-color'] = true; 
	  whiteList['border-top-left-radius'] = true; 
	  whiteList['border-top-right-radius'] = true; 
	  whiteList['border-top-style'] = true; 
	  whiteList['border-top-width'] = true; 
	  whiteList['border-width'] = true; 
	  whiteList['bottom'] = false; 
	  whiteList['box-decoration-break'] = true; 
	  whiteList['box-shadow'] = true; 
	  whiteList['box-sizing'] = true; 
	  whiteList['box-snap'] = true; 
	  whiteList['box-suppress'] = true; 
	  whiteList['break-after'] = true; 
	  whiteList['break-before'] = true; 
	  whiteList['break-inside'] = true; 
	  whiteList['caption-side'] = false; 
	  whiteList['chains'] = false; 
	  whiteList['clear'] = true; 
	  whiteList['clip'] = false; 
	  whiteList['clip-path'] = false; 
	  whiteList['clip-rule'] = false; 
	  whiteList['color'] = true; 
	  whiteList['color-interpolation-filters'] = true; 
	  whiteList['column-count'] = false; 
	  whiteList['column-fill'] = false; 
	  whiteList['column-gap'] = false; 
	  whiteList['column-rule'] = false; 
	  whiteList['column-rule-color'] = false; 
	  whiteList['column-rule-style'] = false; 
	  whiteList['column-rule-width'] = false; 
	  whiteList['column-span'] = false; 
	  whiteList['column-width'] = false; 
	  whiteList['columns'] = false; 
	  whiteList['contain'] = false; 
	  whiteList['content'] = false; 
	  whiteList['counter-increment'] = false; 
	  whiteList['counter-reset'] = false; 
	  whiteList['counter-set'] = false; 
	  whiteList['crop'] = false; 
	  whiteList['cue'] = false; 
	  whiteList['cue-after'] = false; 
	  whiteList['cue-before'] = false; 
	  whiteList['cursor'] = false; 
	  whiteList['direction'] = false; 
	  whiteList['display'] = true; 
	  whiteList['display-inside'] = true; 
	  whiteList['display-list'] = true; 
	  whiteList['display-outside'] = true; 
	  whiteList['dominant-baseline'] = false; 
	  whiteList['elevation'] = false; 
	  whiteList['empty-cells'] = false; 
	  whiteList['filter'] = false; 
	  whiteList['flex'] = false; 
	  whiteList['flex-basis'] = false; 
	  whiteList['flex-direction'] = false; 
	  whiteList['flex-flow'] = false; 
	  whiteList['flex-grow'] = false; 
	  whiteList['flex-shrink'] = false; 
	  whiteList['flex-wrap'] = false; 
	  whiteList['float'] = false; 
	  whiteList['float-offset'] = false; 
	  whiteList['flood-color'] = false; 
	  whiteList['flood-opacity'] = false; 
	  whiteList['flow-from'] = false; 
	  whiteList['flow-into'] = false; 
	  whiteList['font'] = true; 
	  whiteList['font-family'] = true; 
	  whiteList['font-feature-settings'] = true; 
	  whiteList['font-kerning'] = true; 
	  whiteList['font-language-override'] = true; 
	  whiteList['font-size'] = true; 
	  whiteList['font-size-adjust'] = true; 
	  whiteList['font-stretch'] = true; 
	  whiteList['font-style'] = true; 
	  whiteList['font-synthesis'] = true; 
	  whiteList['font-variant'] = true; 
	  whiteList['font-variant-alternates'] = true; 
	  whiteList['font-variant-caps'] = true; 
	  whiteList['font-variant-east-asian'] = true; 
	  whiteList['font-variant-ligatures'] = true; 
	  whiteList['font-variant-numeric'] = true; 
	  whiteList['font-variant-position'] = true; 
	  whiteList['font-weight'] = true; 
	  whiteList['grid'] = false; 
	  whiteList['grid-area'] = false; 
	  whiteList['grid-auto-columns'] = false; 
	  whiteList['grid-auto-flow'] = false; 
	  whiteList['grid-auto-rows'] = false; 
	  whiteList['grid-column'] = false; 
	  whiteList['grid-column-end'] = false; 
	  whiteList['grid-column-start'] = false; 
	  whiteList['grid-row'] = false; 
	  whiteList['grid-row-end'] = false; 
	  whiteList['grid-row-start'] = false; 
	  whiteList['grid-template'] = false; 
	  whiteList['grid-template-areas'] = false; 
	  whiteList['grid-template-columns'] = false; 
	  whiteList['grid-template-rows'] = false; 
	  whiteList['hanging-punctuation'] = false; 
	  whiteList['height'] = true; 
	  whiteList['hyphens'] = false; 
	  whiteList['icon'] = false; 
	  whiteList['image-orientation'] = false; 
	  whiteList['image-resolution'] = false; 
	  whiteList['ime-mode'] = false; 
	  whiteList['initial-letters'] = false; 
	  whiteList['inline-box-align'] = false; 
	  whiteList['justify-content'] = false; 
	  whiteList['justify-items'] = false; 
	  whiteList['justify-self'] = false; 
	  whiteList['left'] = false; 
	  whiteList['letter-spacing'] = true; 
	  whiteList['lighting-color'] = true; 
	  whiteList['line-box-contain'] = false; 
	  whiteList['line-break'] = false; 
	  whiteList['line-grid'] = false; 
	  whiteList['line-height'] = false; 
	  whiteList['line-snap'] = false; 
	  whiteList['line-stacking'] = false; 
	  whiteList['line-stacking-ruby'] = false; 
	  whiteList['line-stacking-shift'] = false; 
	  whiteList['line-stacking-strategy'] = false; 
	  whiteList['list-style'] = true; 
	  whiteList['list-style-image'] = true; 
	  whiteList['list-style-position'] = true; 
	  whiteList['list-style-type'] = true; 
	  whiteList['margin'] = true; 
	  whiteList['margin-bottom'] = true; 
	  whiteList['margin-left'] = true; 
	  whiteList['margin-right'] = true; 
	  whiteList['margin-top'] = true; 
	  whiteList['marker-offset'] = false; 
	  whiteList['marker-side'] = false; 
	  whiteList['marks'] = false; 
	  whiteList['mask'] = false; 
	  whiteList['mask-box'] = false; 
	  whiteList['mask-box-outset'] = false; 
	  whiteList['mask-box-repeat'] = false; 
	  whiteList['mask-box-slice'] = false; 
	  whiteList['mask-box-source'] = false; 
	  whiteList['mask-box-width'] = false; 
	  whiteList['mask-clip'] = false; 
	  whiteList['mask-image'] = false; 
	  whiteList['mask-origin'] = false; 
	  whiteList['mask-position'] = false; 
	  whiteList['mask-repeat'] = false; 
	  whiteList['mask-size'] = false; 
	  whiteList['mask-source-type'] = false; 
	  whiteList['mask-type'] = false; 
	  whiteList['max-height'] = true; 
	  whiteList['max-lines'] = false; 
	  whiteList['max-width'] = true; 
	  whiteList['min-height'] = true; 
	  whiteList['min-width'] = true; 
	  whiteList['move-to'] = false; 
	  whiteList['nav-down'] = false; 
	  whiteList['nav-index'] = false; 
	  whiteList['nav-left'] = false; 
	  whiteList['nav-right'] = false; 
	  whiteList['nav-up'] = false; 
	  whiteList['object-fit'] = false; 
	  whiteList['object-position'] = false; 
	  whiteList['opacity'] = false; 
	  whiteList['order'] = false; 
	  whiteList['orphans'] = false; 
	  whiteList['outline'] = false; 
	  whiteList['outline-color'] = false; 
	  whiteList['outline-offset'] = false; 
	  whiteList['outline-style'] = false; 
	  whiteList['outline-width'] = false; 
	  whiteList['overflow'] = false; 
	  whiteList['overflow-wrap'] = false; 
	  whiteList['overflow-x'] = false; 
	  whiteList['overflow-y'] = false; 
	  whiteList['padding'] = true; 
	  whiteList['padding-bottom'] = true; 
	  whiteList['padding-left'] = true; 
	  whiteList['padding-right'] = true; 
	  whiteList['padding-top'] = true; 
	  whiteList['page'] = false; 
	  whiteList['page-break-after'] = false; 
	  whiteList['page-break-before'] = false; 
	  whiteList['page-break-inside'] = false; 
	  whiteList['page-policy'] = false; 
	  whiteList['pause'] = false; 
	  whiteList['pause-after'] = false; 
	  whiteList['pause-before'] = false; 
	  whiteList['perspective'] = false; 
	  whiteList['perspective-origin'] = false; 
	  whiteList['pitch'] = false; 
	  whiteList['pitch-range'] = false; 
	  whiteList['play-during'] = false; 
	  whiteList['position'] = false; 
	  whiteList['presentation-level'] = false; 
	  whiteList['quotes'] = false; 
	  whiteList['region-fragment'] = false; 
	  whiteList['resize'] = false; 
	  whiteList['rest'] = false; 
	  whiteList['rest-after'] = false; 
	  whiteList['rest-before'] = false; 
	  whiteList['richness'] = false; 
	  whiteList['right'] = false; 
	  whiteList['rotation'] = false; 
	  whiteList['rotation-point'] = false; 
	  whiteList['ruby-align'] = false; 
	  whiteList['ruby-merge'] = false; 
	  whiteList['ruby-position'] = false; 
	  whiteList['shape-image-threshold'] = false; 
	  whiteList['shape-outside'] = false; 
	  whiteList['shape-margin'] = false; 
	  whiteList['size'] = false; 
	  whiteList['speak'] = false; 
	  whiteList['speak-as'] = false; 
	  whiteList['speak-header'] = false; 
	  whiteList['speak-numeral'] = false; 
	  whiteList['speak-punctuation'] = false; 
	  whiteList['speech-rate'] = false; 
	  whiteList['stress'] = false; 
	  whiteList['string-set'] = false; 
	  whiteList['tab-size'] = false; 
	  whiteList['table-layout'] = false; 
	  whiteList['text-align'] = true; 
	  whiteList['text-align-last'] = true; 
	  whiteList['text-combine-upright'] = true; 
	  whiteList['text-decoration'] = true; 
	  whiteList['text-decoration-color'] = true; 
	  whiteList['text-decoration-line'] = true; 
	  whiteList['text-decoration-skip'] = true; 
	  whiteList['text-decoration-style'] = true; 
	  whiteList['text-emphasis'] = true; 
	  whiteList['text-emphasis-color'] = true; 
	  whiteList['text-emphasis-position'] = true; 
	  whiteList['text-emphasis-style'] = true; 
	  whiteList['text-height'] = true; 
	  whiteList['text-indent'] = true; 
	  whiteList['text-justify'] = true; 
	  whiteList['text-orientation'] = true; 
	  whiteList['text-overflow'] = true; 
	  whiteList['text-shadow'] = true; 
	  whiteList['text-space-collapse'] = true; 
	  whiteList['text-transform'] = true; 
	  whiteList['text-underline-position'] = true; 
	  whiteList['text-wrap'] = true; 
	  whiteList['top'] = false; 
	  whiteList['transform'] = false; 
	  whiteList['transform-origin'] = false; 
	  whiteList['transform-style'] = false; 
	  whiteList['transition'] = false; 
	  whiteList['transition-delay'] = false; 
	  whiteList['transition-duration'] = false; 
	  whiteList['transition-property'] = false; 
	  whiteList['transition-timing-function'] = false; 
	  whiteList['unicode-bidi'] = false; 
	  whiteList['vertical-align'] = false; 
	  whiteList['visibility'] = false; 
	  whiteList['voice-balance'] = false; 
	  whiteList['voice-duration'] = false; 
	  whiteList['voice-family'] = false; 
	  whiteList['voice-pitch'] = false; 
	  whiteList['voice-range'] = false; 
	  whiteList['voice-rate'] = false; 
	  whiteList['voice-stress'] = false; 
	  whiteList['voice-volume'] = false; 
	  whiteList['volume'] = false; 
	  whiteList['white-space'] = false; 
	  whiteList['widows'] = false; 
	  whiteList['width'] = true; 
	  whiteList['will-change'] = false; 
	  whiteList['word-break'] = true; 
	  whiteList['word-spacing'] = true; 
	  whiteList['word-wrap'] = true; 
	  whiteList['wrap-flow'] = false; 
	  whiteList['wrap-through'] = false; 
	  whiteList['writing-mode'] = false; 
	  whiteList['z-index'] = false; 

	  return whiteList;
	}


	
	function onAttr (name, value, options) {
	  
	}

	
	function onIgnoreAttr (name, value, options) {
	  
	}

	var REGEXP_URL_JAVASCRIPT = /javascript\s*\:/img;

	
	function safeAttrValue$1(name, value) {
	  if (REGEXP_URL_JAVASCRIPT.test(value)) return '';
	  return value;
	}


	_default.whiteList = getDefaultWhiteList$1();
	_default.getDefaultWhiteList = getDefaultWhiteList$1;
	_default.onAttr = onAttr;
	_default.onIgnoreAttr = onIgnoreAttr;
	_default.safeAttrValue = safeAttrValue$1;

	var util$1 = {
	  indexOf: function (arr, item) {
	    var i, j;
	    if (Array.prototype.indexOf) {
	      return arr.indexOf(item);
	    }
	    for (i = 0, j = arr.length; i < j; i++) {
	      if (arr[i] === item) {
	        return i;
	      }
	    }
	    return -1;
	  },
	  forEach: function (arr, fn, scope) {
	    var i, j;
	    if (Array.prototype.forEach) {
	      return arr.forEach(fn, scope);
	    }
	    for (i = 0, j = arr.length; i < j; i++) {
	      fn.call(scope, arr[i], i, arr);
	    }
	  },
	  trim: function (str) {
	    if (String.prototype.trim) {
	      return str.trim();
	    }
	    return str.replace(/(^\s*)|(\s*$)/g, '');
	  },
	  trimRight: function (str) {
	    if (String.prototype.trimRight) {
	      return str.trimRight();
	    }
	    return str.replace(/(\s*$)/g, '');
	  }
	};

	

	var _$3 = util$1;


	
	function parseStyle$1 (css, onAttr) {
	  css = _$3.trimRight(css);
	  if (css[css.length - 1] !== ';') css += ';';
	  var cssLength = css.length;
	  var isParenthesisOpen = false;
	  var lastPos = 0;
	  var i = 0;
	  var retCSS = '';

	  function addNewAttr () {
	    
	    if (!isParenthesisOpen) {
	      var source = _$3.trim(css.slice(lastPos, i));
	      var j = source.indexOf(':');
	      if (j !== -1) {
	        var name = _$3.trim(source.slice(0, j));
	        var value = _$3.trim(source.slice(j + 1));
	        
	        if (name) {
	          var ret = onAttr(lastPos, retCSS.length, name, value, source);
	          if (ret) retCSS += ret + '; ';
	        }
	      }
	    }
	    lastPos = i + 1;
	  }

	  for (; i < cssLength; i++) {
	    var c = css[i];
	    if (c === '/' && css[i + 1] === '*') {
	      
	      var j = css.indexOf('*/', i + 2);
	      
	      if (j === -1) break;
	      
	      i = j + 1;
	      lastPos = i + 1;
	      isParenthesisOpen = false;
	    } else if (c === '(') {
	      isParenthesisOpen = true;
	    } else if (c === ')') {
	      isParenthesisOpen = false;
	    } else if (c === ';') {
	      if (isParenthesisOpen) ; else {
	        addNewAttr();
	      }
	    } else if (c === '\n') {
	      addNewAttr();
	    }
	  }

	  return _$3.trim(retCSS);
	}

	var parser$2 = parseStyle$1;

	

	var DEFAULT$1 = _default;
	var parseStyle = parser$2;


	
	function isNull$1 (obj) {
	  return (obj === undefined || obj === null);
	}

	
	function shallowCopyObject$1 (obj) {
	  var ret = {};
	  for (var i in obj) {
	    ret[i] = obj[i];
	  }
	  return ret;
	}

	
	function FilterCSS$2 (options) {
	  options = shallowCopyObject$1(options || {});
	  options.whiteList = options.whiteList || DEFAULT$1.whiteList;
	  options.onAttr = options.onAttr || DEFAULT$1.onAttr;
	  options.onIgnoreAttr = options.onIgnoreAttr || DEFAULT$1.onIgnoreAttr;
	  options.safeAttrValue = options.safeAttrValue || DEFAULT$1.safeAttrValue;
	  this.options = options;
	}

	FilterCSS$2.prototype.process = function (css) {
	  
	  css = css || '';
	  css = css.toString();
	  if (!css) return '';

	  var me = this;
	  var options = me.options;
	  var whiteList = options.whiteList;
	  var onAttr = options.onAttr;
	  var onIgnoreAttr = options.onIgnoreAttr;
	  var safeAttrValue = options.safeAttrValue;

	  var retCSS = parseStyle(css, function (sourcePosition, position, name, value, source) {

	    var check = whiteList[name];
	    var isWhite = false;
	    if (check === true) isWhite = check;
	    else if (typeof check === 'function') isWhite = check(value);
	    else if (check instanceof RegExp) isWhite = check.test(value);
	    if (isWhite !== true) isWhite = false;

	    
	    value = safeAttrValue(name, value);
	    if (!value) return;

	    var opts = {
	      position: position,
	      sourcePosition: sourcePosition,
	      source: source,
	      isWhite: isWhite
	    };

	    if (isWhite) {

	      var ret = onAttr(name, value, opts);
	      if (isNull$1(ret)) {
	        return name + ':' + value;
	      } else {
	        return ret;
	      }

	    } else {

	      var ret = onIgnoreAttr(name, value, opts);
	      if (!isNull$1(ret)) {
	        return ret;
	      }

	    }
	  });

	  return retCSS;
	};


	var css = FilterCSS$2;

	

	(function (module, exports) {
		var DEFAULT = _default;
		var FilterCSS = css;


		
		function filterCSS (html, options) {
		  var xss = new FilterCSS(options);
		  return xss.process(html);
		}


		
		exports = module.exports = filterCSS;
		exports.FilterCSS = FilterCSS;
		for (var i in DEFAULT) exports[i] = DEFAULT[i];

		
		if (typeof window !== 'undefined') {
		  window.filterCSS = module.exports;
		}
	} (lib, libExports));

	var util = {
	  indexOf: function (arr, item) {
	    var i, j;
	    if (Array.prototype.indexOf) {
	      return arr.indexOf(item);
	    }
	    for (i = 0, j = arr.length; i < j; i++) {
	      if (arr[i] === item) {
	        return i;
	      }
	    }
	    return -1;
	  },
	  forEach: function (arr, fn, scope) {
	    var i, j;
	    if (Array.prototype.forEach) {
	      return arr.forEach(fn, scope);
	    }
	    for (i = 0, j = arr.length; i < j; i++) {
	      fn.call(scope, arr[i], i, arr);
	    }
	  },
	  trim: function (str) {
	    if (String.prototype.trim) {
	      return str.trim();
	    }
	    return str.replace(/(^\s*)|(\s*$)/g, "");
	  },
	  spaceIndex: function (str) {
	    var reg = /\s|\n|\t/;
	    var match = reg.exec(str);
	    return match ? match.index : -1;
	  },
	};

	

	var FilterCSS$1 = libExports.FilterCSS;
	var getDefaultCSSWhiteList = libExports.getDefaultWhiteList;
	var _$2 = util;

	function getDefaultWhiteList() {
	  return {
	    a: ["target", "href", "title"],
	    abbr: ["title"],
	    address: [],
	    area: ["shape", "coords", "href", "alt"],
	    article: [],
	    aside: [],
	    audio: [
	      "autoplay",
	      "controls",
	      "crossorigin",
	      "loop",
	      "muted",
	      "preload",
	      "src",
	    ],
	    b: [],
	    bdi: ["dir"],
	    bdo: ["dir"],
	    big: [],
	    blockquote: ["cite"],
	    br: [],
	    caption: [],
	    center: [],
	    cite: [],
	    code: [],
	    col: ["align", "valign", "span", "width"],
	    colgroup: ["align", "valign", "span", "width"],
	    dd: [],
	    del: ["datetime"],
	    details: ["open"],
	    div: [],
	    dl: [],
	    dt: [],
	    em: [],
	    figcaption: [],
	    figure: [],
	    font: ["color", "size", "face"],
	    footer: [],
	    h1: [],
	    h2: [],
	    h3: [],
	    h4: [],
	    h5: [],
	    h6: [],
	    header: [],
	    hr: [],
	    i: [],
	    img: ["src", "alt", "title", "width", "height"],
	    ins: ["datetime"],
	    li: [],
	    mark: [],
	    nav: [],
	    ol: [],
	    p: [],
	    pre: [],
	    s: [],
	    section: [],
	    small: [],
	    span: [],
	    sub: [],
	    summary: [],
	    sup: [],
	    strong: [],
	    strike: [],
	    table: ["width", "border", "align", "valign"],
	    tbody: ["align", "valign"],
	    td: ["width", "rowspan", "colspan", "align", "valign"],
	    tfoot: ["align", "valign"],
	    th: ["width", "rowspan", "colspan", "align", "valign"],
	    thead: ["align", "valign"],
	    tr: ["rowspan", "align", "valign"],
	    tt: [],
	    u: [],
	    ul: [],
	    video: [
	      "autoplay",
	      "controls",
	      "crossorigin",
	      "loop",
	      "muted",
	      "playsinline",
	      "poster",
	      "preload",
	      "src",
	      "height",
	      "width",
	    ],
	  };
	}

	var defaultCSSFilter = new FilterCSS$1();

	
	function onTag(tag, html, options) {
	  
	}

	
	function onIgnoreTag(tag, html, options) {
	  
	}

	
	function onTagAttr(tag, name, value) {
	  
	}

	
	function onIgnoreTagAttr(tag, name, value) {
	  
	}

	
	function escapeHtml(html) {
	  return html.replace(REGEXP_LT, "&lt;").replace(REGEXP_GT, "&gt;");
	}

	
	function safeAttrValue(tag, name, value, cssFilter) {
	  
	  value = friendlyAttrValue(value);

	  if (name === "href" || name === "src") {
	    
	    
	    value = _$2.trim(value);
	    if (value === "#") return "#";
	    if (
	      !(
	        value.substr(0, 7) === "http:
	        value.substr(0, 8) === "https:
	        value.substr(0, 7) === "mailto:" ||
	        value.substr(0, 4) === "tel:" ||
	        value.substr(0, 11) === "data:image/" ||
	        value.substr(0, 6) === "ftp:
	        value.substr(0, 2) === "./" ||
	        value.substr(0, 3) === "../" ||
	        value[0] === "#" ||
	        value[0] === "/"
	      )
	    ) {
	      return "";
	    }
	  } else if (name === "background") {
	    
	    
	    REGEXP_DEFAULT_ON_TAG_ATTR_4.lastIndex = 0;
	    if (REGEXP_DEFAULT_ON_TAG_ATTR_4.test(value)) {
	      return "";
	    }
	  } else if (name === "style") {
	    
	    REGEXP_DEFAULT_ON_TAG_ATTR_7.lastIndex = 0;
	    if (REGEXP_DEFAULT_ON_TAG_ATTR_7.test(value)) {
	      return "";
	    }
	    
	    REGEXP_DEFAULT_ON_TAG_ATTR_8.lastIndex = 0;
	    if (REGEXP_DEFAULT_ON_TAG_ATTR_8.test(value)) {
	      REGEXP_DEFAULT_ON_TAG_ATTR_4.lastIndex = 0;
	      if (REGEXP_DEFAULT_ON_TAG_ATTR_4.test(value)) {
	        return "";
	      }
	    }
	    if (cssFilter !== false) {
	      cssFilter = cssFilter || defaultCSSFilter;
	      value = cssFilter.process(value);
	    }
	  }

	  
	  value = escapeAttrValue(value);
	  return value;
	}

	
	var REGEXP_LT = /</g;
	var REGEXP_GT = />/g;
	var REGEXP_QUOTE = /"/g;
	var REGEXP_QUOTE_2 = /&quot;/g;
	var REGEXP_ATTR_VALUE_1 = /&#([a-zA-Z0-9]*);?/gim;
	var REGEXP_ATTR_VALUE_COLON = /&colon;?/gim;
	var REGEXP_ATTR_VALUE_NEWLINE = /&newline;?/gim;
	
	var REGEXP_DEFAULT_ON_TAG_ATTR_4 =
	  /((j\s*a\s*v\s*a|v\s*b|l\s*i\s*v\s*e)\s*s\s*c\s*r\s*i\s*p\s*t\s*|m\s*o\s*c\s*h\s*a):/gi;
	
	
	var REGEXP_DEFAULT_ON_TAG_ATTR_7 =
	  /e\s*x\s*p\s*r\s*e\s*s\s*s\s*i\s*o\s*n\s*\(.*/gi;
	var REGEXP_DEFAULT_ON_TAG_ATTR_8 = /u\s*r\s*l\s*\(.*/gi;

	
	function escapeQuote(str) {
	  return str.replace(REGEXP_QUOTE, "&quot;");
	}

	
	function unescapeQuote(str) {
	  return str.replace(REGEXP_QUOTE_2, '"');
	}

	
	function escapeHtmlEntities(str) {
	  return str.replace(REGEXP_ATTR_VALUE_1, function replaceUnicode(str, code) {
	    return code[0] === "x" || code[0] === "X"
	      ? String.fromCharCode(parseInt(code.substr(1), 16))
	      : String.fromCharCode(parseInt(code, 10));
	  });
	}

	
	function escapeDangerHtml5Entities(str) {
	  return str
	    .replace(REGEXP_ATTR_VALUE_COLON, ":")
	    .replace(REGEXP_ATTR_VALUE_NEWLINE, " ");
	}

	
	function clearNonPrintableCharacter(str) {
	  var str2 = "";
	  for (var i = 0, len = str.length; i < len; i++) {
	    str2 += str.charCodeAt(i) < 32 ? " " : str.charAt(i);
	  }
	  return _$2.trim(str2);
	}

	
	function friendlyAttrValue(str) {
	  str = unescapeQuote(str);
	  str = escapeHtmlEntities(str);
	  str = escapeDangerHtml5Entities(str);
	  str = clearNonPrintableCharacter(str);
	  return str;
	}

	
	function escapeAttrValue(str) {
	  str = escapeQuote(str);
	  str = escapeHtml(str);
	  return str;
	}

	
	function onIgnoreTagStripAll() {
	  return "";
	}

	
	function StripTagBody(tags, next) {
	  if (typeof next !== "function") {
	    next = function () {};
	  }

	  var isRemoveAllTag = !Array.isArray(tags);
	  function isRemoveTag(tag) {
	    if (isRemoveAllTag) return true;
	    return _$2.indexOf(tags, tag) !== -1;
	  }

	  var removeList = [];
	  var posStart = false;

	  return {
	    onIgnoreTag: function (tag, html, options) {
	      if (isRemoveTag(tag)) {
	        if (options.isClosing) {
	          var ret = "[/removed]";
	          var end = options.position + ret.length;
	          removeList.push([
	            posStart !== false ? posStart : options.position,
	            end,
	          ]);
	          posStart = false;
	          return ret;
	        } else {
	          if (!posStart) {
	            posStart = options.position;
	          }
	          return "[removed]";
	        }
	      } else {
	        return next(tag, html, options);
	      }
	    },
	    remove: function (html) {
	      var rethtml = "";
	      var lastPos = 0;
	      _$2.forEach(removeList, function (pos) {
	        rethtml += html.slice(lastPos, pos[0]);
	        lastPos = pos[1];
	      });
	      rethtml += html.slice(lastPos);
	      return rethtml;
	    },
	  };
	}

	
	function stripCommentTag(html) {
	  var retHtml = "";
	  var lastPos = 0;
	  while (lastPos < html.length) {
	    var i = html.indexOf("<!--", lastPos);
	    if (i === -1) {
	      retHtml += html.slice(lastPos);
	      break;
	    }
	    retHtml += html.slice(lastPos, i);
	    var j = html.indexOf("-->", i);
	    if (j === -1) {
	      break;
	    }
	    lastPos = j + 3;
	  }
	  return retHtml;
	}

	
	function stripBlankChar(html) {
	  var chars = html.split("");
	  chars = chars.filter(function (char) {
	    var c = char.charCodeAt(0);
	    if (c === 127) return false;
	    if (c <= 31) {
	      if (c === 10 || c === 13) return true;
	      return false;
	    }
	    return true;
	  });
	  return chars.join("");
	}

	_default$1.whiteList = getDefaultWhiteList();
	_default$1.getDefaultWhiteList = getDefaultWhiteList;
	_default$1.onTag = onTag;
	_default$1.onIgnoreTag = onIgnoreTag;
	_default$1.onTagAttr = onTagAttr;
	_default$1.onIgnoreTagAttr = onIgnoreTagAttr;
	_default$1.safeAttrValue = safeAttrValue;
	_default$1.escapeHtml = escapeHtml;
	_default$1.escapeQuote = escapeQuote;
	_default$1.unescapeQuote = unescapeQuote;
	_default$1.escapeHtmlEntities = escapeHtmlEntities;
	_default$1.escapeDangerHtml5Entities = escapeDangerHtml5Entities;
	_default$1.clearNonPrintableCharacter = clearNonPrintableCharacter;
	_default$1.friendlyAttrValue = friendlyAttrValue;
	_default$1.escapeAttrValue = escapeAttrValue;
	_default$1.onIgnoreTagStripAll = onIgnoreTagStripAll;
	_default$1.StripTagBody = StripTagBody;
	_default$1.stripCommentTag = stripCommentTag;
	_default$1.stripBlankChar = stripBlankChar;
	_default$1.cssFilter = defaultCSSFilter;
	_default$1.getDefaultCSSWhiteList = getDefaultCSSWhiteList;

	var parser$1 = {};

	

	var _$1 = util;

	
	function getTagName(html) {
	  var i = _$1.spaceIndex(html);
	  var tagName;
	  if (i === -1) {
	    tagName = html.slice(1, -1);
	  } else {
	    tagName = html.slice(1, i + 1);
	  }
	  tagName = _$1.trim(tagName).toLowerCase();
	  if (tagName.slice(0, 1) === "/") tagName = tagName.slice(1);
	  if (tagName.slice(-1) === "/") tagName = tagName.slice(0, -1);
	  return tagName;
	}

	
	function isClosing(html) {
	  return html.slice(0, 2) === "</";
	}

	
	function parseTag$1(html, onTag, escapeHtml) {

	  var rethtml = "";
	  var lastPos = 0;
	  var tagStart = false;
	  var quoteStart = false;
	  var currentPos = 0;
	  var len = html.length;
	  var currentTagName = "";
	  var currentHtml = "";

	  chariterator: for (currentPos = 0; currentPos < len; currentPos++) {
	    var c = html.charAt(currentPos);
	    if (tagStart === false) {
	      if (c === "<") {
	        tagStart = currentPos;
	        continue;
	      }
	    } else {
	      if (quoteStart === false) {
	        if (c === "<") {
	          rethtml += escapeHtml(html.slice(lastPos, currentPos));
	          tagStart = currentPos;
	          lastPos = currentPos;
	          continue;
	        }
	        if (c === ">" || currentPos === len - 1) {
	          rethtml += escapeHtml(html.slice(lastPos, tagStart));
	          currentHtml = html.slice(tagStart, currentPos + 1);
	          currentTagName = getTagName(currentHtml);
	          rethtml += onTag(
	            tagStart,
	            rethtml.length,
	            currentTagName,
	            currentHtml,
	            isClosing(currentHtml)
	          );
	          lastPos = currentPos + 1;
	          tagStart = false;
	          continue;
	        }
	        if (c === '"' || c === "'") {
	          var i = 1;
	          var ic = html.charAt(currentPos - i);

	          while (ic.trim() === "" || ic === "=") {
	            if (ic === "=") {
	              quoteStart = c;
	              continue chariterator;
	            }
	            ic = html.charAt(currentPos - ++i);
	          }
	        }
	      } else {
	        if (c === quoteStart) {
	          quoteStart = false;
	          continue;
	        }
	      }
	    }
	  }
	  if (lastPos < len) {
	    rethtml += escapeHtml(html.substr(lastPos));
	  }

	  return rethtml;
	}

	var REGEXP_ILLEGAL_ATTR_NAME = /[^a-zA-Z0-9\\_:.-]/gim;

	
	function parseAttr$1(html, onAttr) {

	  var lastPos = 0;
	  var lastMarkPos = 0;
	  var retAttrs = [];
	  var tmpName = false;
	  var len = html.length;

	  function addAttr(name, value) {
	    name = _$1.trim(name);
	    name = name.replace(REGEXP_ILLEGAL_ATTR_NAME, "").toLowerCase();
	    if (name.length < 1) return;
	    var ret = onAttr(name, value || "");
	    if (ret) retAttrs.push(ret);
	  }

	  
	  for (var i = 0; i < len; i++) {
	    var c = html.charAt(i);
	    var v, j;
	    if (tmpName === false && c === "=") {
	      tmpName = html.slice(lastPos, i);
	      lastPos = i + 1;
	      lastMarkPos = html.charAt(lastPos) === '"' || html.charAt(lastPos) === "'" ? lastPos : findNextQuotationMark(html, i + 1);
	      continue;
	    }
	    if (tmpName !== false) {
	      if (
	        i === lastMarkPos
	      ) {
	        j = html.indexOf(c, i + 1);
	        if (j === -1) {
	          break;
	        } else {
	          v = _$1.trim(html.slice(lastMarkPos + 1, j));
	          addAttr(tmpName, v);
	          tmpName = false;
	          i = j;
	          lastPos = i + 1;
	          continue;
	        }
	      }
	    }
	    if (/\s|\n|\t/.test(c)) {
	      html = html.replace(/\s|\n|\t/g, " ");
	      if (tmpName === false) {
	        j = findNextEqual(html, i);
	        if (j === -1) {
	          v = _$1.trim(html.slice(lastPos, i));
	          addAttr(v);
	          tmpName = false;
	          lastPos = i + 1;
	          continue;
	        } else {
	          i = j - 1;
	          continue;
	        }
	      } else {
	        j = findBeforeEqual(html, i - 1);
	        if (j === -1) {
	          v = _$1.trim(html.slice(lastPos, i));
	          v = stripQuoteWrap(v);
	          addAttr(tmpName, v);
	          tmpName = false;
	          lastPos = i + 1;
	          continue;
	        } else {
	          continue;
	        }
	      }
	    }
	  }

	  if (lastPos < html.length) {
	    if (tmpName === false) {
	      addAttr(html.slice(lastPos));
	    } else {
	      addAttr(tmpName, stripQuoteWrap(_$1.trim(html.slice(lastPos))));
	    }
	  }

	  return _$1.trim(retAttrs.join(" "));
	}

	function findNextEqual(str, i) {
	  for (; i < str.length; i++) {
	    var c = str[i];
	    if (c === " ") continue;
	    if (c === "=") return i;
	    return -1;
	  }
	}

	function findNextQuotationMark(str, i) {
	  for (; i < str.length; i++) {
	    var c = str[i];
	    if (c === " ") continue;
	    if (c === "'" || c === '"') return i;
	    return -1;
	  }
	}

	function findBeforeEqual(str, i) {
	  for (; i > 0; i--) {
	    var c = str[i];
	    if (c === " ") continue;
	    if (c === "=") return i;
	    return -1;
	  }
	}

	function isQuoteWrapString(text) {
	  if (
	    (text[0] === '"' && text[text.length - 1] === '"') ||
	    (text[0] === "'" && text[text.length - 1] === "'")
	  ) {
	    return true;
	  } else {
	    return false;
	  }
	}

	function stripQuoteWrap(text) {
	  if (isQuoteWrapString(text)) {
	    return text.substr(1, text.length - 2);
	  } else {
	    return text;
	  }
	}

	parser$1.parseTag = parseTag$1;
	parser$1.parseAttr = parseAttr$1;

	

	var FilterCSS = libExports.FilterCSS;
	var DEFAULT = _default$1;
	var parser = parser$1;
	var parseTag = parser.parseTag;
	var parseAttr = parser.parseAttr;
	var _ = util;

	
	function isNull(obj) {
	  return obj === undefined || obj === null;
	}

	
	function getAttrs(html) {
	  var i = _.spaceIndex(html);
	  if (i === -1) {
	    return {
	      html: "",
	      closing: html[html.length - 2] === "/",
	    };
	  }
	  html = _.trim(html.slice(i + 1, -1));
	  var isClosing = html[html.length - 1] === "/";
	  if (isClosing) html = _.trim(html.slice(0, -1));
	  return {
	    html: html,
	    closing: isClosing,
	  };
	}

	
	function shallowCopyObject(obj) {
	  var ret = {};
	  for (var i in obj) {
	    ret[i] = obj[i];
	  }
	  return ret;
	}

	function keysToLowerCase(obj) {
	  var ret = {};
	  for (var i in obj) {
	    if (Array.isArray(obj[i])) {
	      ret[i.toLowerCase()] = obj[i].map(function (item) {
	        return item.toLowerCase();
	      });
	    } else {
	      ret[i.toLowerCase()] = obj[i];
	    }
	  }
	  return ret;
	}

	
	function FilterXSS(options) {
	  options = shallowCopyObject(options || {});

	  if (options.stripIgnoreTag) {
	    if (options.onIgnoreTag) {
	      console.error(
	        'Notes: cannot use these two options "stripIgnoreTag" and "onIgnoreTag" at the same time'
	      );
	    }
	    options.onIgnoreTag = DEFAULT.onIgnoreTagStripAll;
	  }
	  if (options.whiteList || options.allowList) {
	    options.whiteList = keysToLowerCase(options.whiteList || options.allowList);
	  } else {
	    options.whiteList = DEFAULT.whiteList;
	  }

	  options.onTag = options.onTag || DEFAULT.onTag;
	  options.onTagAttr = options.onTagAttr || DEFAULT.onTagAttr;
	  options.onIgnoreTag = options.onIgnoreTag || DEFAULT.onIgnoreTag;
	  options.onIgnoreTagAttr = options.onIgnoreTagAttr || DEFAULT.onIgnoreTagAttr;
	  options.safeAttrValue = options.safeAttrValue || DEFAULT.safeAttrValue;
	  options.escapeHtml = options.escapeHtml || DEFAULT.escapeHtml;
	  this.options = options;

	  if (options.css === false) {
	    this.cssFilter = false;
	  } else {
	    options.css = options.css || {};
	    this.cssFilter = new FilterCSS(options.css);
	  }
	}

	
	FilterXSS.prototype.process = function (html) {
	  
	  html = html || "";
	  html = html.toString();
	  if (!html) return "";

	  var me = this;
	  var options = me.options;
	  var whiteList = options.whiteList;
	  var onTag = options.onTag;
	  var onIgnoreTag = options.onIgnoreTag;
	  var onTagAttr = options.onTagAttr;
	  var onIgnoreTagAttr = options.onIgnoreTagAttr;
	  var safeAttrValue = options.safeAttrValue;
	  var escapeHtml = options.escapeHtml;
	  var cssFilter = me.cssFilter;

	  
	  if (options.stripBlankChar) {
	    html = DEFAULT.stripBlankChar(html);
	  }

	  
	  if (!options.allowCommentTag) {
	    html = DEFAULT.stripCommentTag(html);
	  }

	  
	  var stripIgnoreTagBody = false;
	  if (options.stripIgnoreTagBody) {
	    stripIgnoreTagBody = DEFAULT.StripTagBody(
	      options.stripIgnoreTagBody,
	      onIgnoreTag
	    );
	    onIgnoreTag = stripIgnoreTagBody.onIgnoreTag;
	  }

	  var retHtml = parseTag(
	    html,
	    function (sourcePosition, position, tag, html, isClosing) {
	      var info = {
	        sourcePosition: sourcePosition,
	        position: position,
	        isClosing: isClosing,
	        isWhite: Object.prototype.hasOwnProperty.call(whiteList, tag),
	      };

	      
	      var ret = onTag(tag, html, info);
	      if (!isNull(ret)) return ret;

	      if (info.isWhite) {
	        if (info.isClosing) {
	          return "</" + tag + ">";
	        }

	        var attrs = getAttrs(html);
	        var whiteAttrList = whiteList[tag];
	        var attrsHtml = parseAttr(attrs.html, function (name, value) {
	          
	          var isWhiteAttr = _.indexOf(whiteAttrList, name) !== -1;
	          var ret = onTagAttr(tag, name, value, isWhiteAttr);
	          if (!isNull(ret)) return ret;

	          if (isWhiteAttr) {
	            
	            value = safeAttrValue(tag, name, value, cssFilter);
	            if (value) {
	              return name + '="' + value + '"';
	            } else {
	              return name;
	            }
	          } else {
	            
	            ret = onIgnoreTagAttr(tag, name, value, isWhiteAttr);
	            if (!isNull(ret)) return ret;
	            return;
	          }
	        });

	        
	        html = "<" + tag;
	        if (attrsHtml) html += " " + attrsHtml;
	        if (attrs.closing) html += " /";
	        html += ">";
	        return html;
	      } else {
	        
	        ret = onIgnoreTag(tag, html, info);
	        if (!isNull(ret)) return ret;
	        return escapeHtml(html);
	      }
	    },
	    escapeHtml
	  );

	  
	  if (stripIgnoreTagBody) {
	    retHtml = stripIgnoreTagBody.remove(retHtml);
	  }

	  return retHtml;
	};

	var xss = FilterXSS;

	

	(function (module, exports) {
		var DEFAULT = _default$1;
		var parser = parser$1;
		var FilterXSS = xss;

		
		function filterXSS(html, options) {
		  var xss = new FilterXSS(options);
		  return xss.process(html);
		}

		exports = module.exports = filterXSS;
		exports.filterXSS = filterXSS;
		exports.FilterXSS = FilterXSS;

		(function () {
		  for (var i in DEFAULT) {
		    exports[i] = DEFAULT[i];
		  }
		  for (var j in parser) {
		    exports[j] = parser[j];
		  }
		})();

		
		if (typeof window !== "undefined") {
		  window.filterXSS = module.exports;
		}

		
		function isWorkerEnv() {
		  return (
		    typeof self !== "undefined" &&
		    typeof DedicatedWorkerGlobalScope !== "undefined" &&
		    self instanceof DedicatedWorkerGlobalScope
		  );
		}
		if (isWorkerEnv()) {
		  self.filterXSS = module.exports;
		}
	} (lib$1, libExports$1));

	var xssFilter = libExports$1;

	
	
	
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

	function ownKeys(object, enumerableOnly) { var keys = _Object$keys(object); if (_Object$getOwnPropertySymbols) { var symbols = _Object$getOwnPropertySymbols(object); enumerableOnly && (symbols = _filterInstanceProperty(symbols).call(symbols, function (sym) { return _Object$getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }
	function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var _context8, _context9; var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? _forEachInstanceProperty(_context8 = ownKeys(Object(source), !0)).call(_context8, function (key) { _defineProperty(target, key, source[key]); }) : _Object$getOwnPropertyDescriptors ? _Object$defineProperties(target, _Object$getOwnPropertyDescriptors(source)) : _forEachInstanceProperty(_context9 = ownKeys(Object(source))).call(_context9, function (key) { _Object$defineProperty(target, key, _Object$getOwnPropertyDescriptor(source, key)); }); } return target; }
	
	function isDataViewLike(obj) {
	  var _obj$idProp;
	  if (!obj) {
	    return false;
	  }
	  var idProp = (_obj$idProp = obj.idProp) !== null && _obj$idProp !== void 0 ? _obj$idProp : obj._idProp;
	  if (!idProp) {
	    return false;
	  }
	  return esnext.isDataViewLike(idProp, obj);
	}

	
	
	
	var ASPDateRegex = /^\/?Date\((-?\d+)/i;
	var NumericRegex = /^\d+$/;
	
	function convert(object, type) {
	  var match;
	  if (object === undefined) {
	    return undefined;
	  }
	  if (object === null) {
	    return null;
	  }
	  if (!type) {
	    return object;
	  }
	  if (!(typeof type === "string") && !(type instanceof String)) {
	    throw new Error("Type must be a string");
	  }

	  
	  switch (type) {
	    case "boolean":
	    case "Boolean":
	      return Boolean(object);
	    case "number":
	    case "Number":
	      if (isString(object) && !isNaN(Date.parse(object))) {
	        return moment$3(object).valueOf();
	      } else {
	        
	        
	        
	        return Number(object.valueOf());
	      }
	    case "string":
	    case "String":
	      return String(object);
	    case "Date":
	      try {
	        return convert(object, "Moment").toDate();
	      } catch (e) {
	        if (e instanceof TypeError) {
	          throw new TypeError("Cannot convert object of type " + getType(object) + " to type " + type);
	        } else {
	          throw e;
	        }
	      }
	    case "Moment":
	      if (isNumber(object)) {
	        return moment$3(object);
	      }
	      if (object instanceof Date) {
	        return moment$3(object.valueOf());
	      } else if (moment$3.isMoment(object)) {
	        return moment$3(object);
	      }
	      if (isString(object)) {
	        match = ASPDateRegex.exec(object);
	        if (match) {
	          
	          return moment$3(Number(match[1])); 
	        }

	        match = NumericRegex.exec(object);
	        if (match) {
	          return moment$3(Number(object));
	        }
	        return moment$3(object); 
	      } else {
	        throw new TypeError("Cannot convert object of type " + getType(object) + " to type " + type);
	      }
	    case "ISODate":
	      if (isNumber(object)) {
	        return new Date(object);
	      } else if (object instanceof Date) {
	        return object.toISOString();
	      } else if (moment$3.isMoment(object)) {
	        return object.toDate().toISOString();
	      } else if (isString(object)) {
	        match = ASPDateRegex.exec(object);
	        if (match) {
	          
	          return new Date(Number(match[1])).toISOString(); 
	        } else {
	          return moment$3(object).format(); 
	        }
	      } else {
	        throw new Error("Cannot convert object of type " + getType(object) + " to type ISODate");
	      }
	    case "ASPDate":
	      if (isNumber(object)) {
	        return "/Date(" + object + ")/";
	      } else if (object instanceof Date || moment$3.isMoment(object)) {
	        return "/Date(" + object.valueOf() + ")/";
	      } else if (isString(object)) {
	        match = ASPDateRegex.exec(object);
	        var value;
	        if (match) {
	          
	          value = new Date(Number(match[1])).valueOf(); 
	        } else {
	          value = new Date(object).valueOf(); 
	        }

	        return "/Date(" + value + ")/";
	      } else {
	        throw new Error("Cannot convert object of type " + getType(object) + " to type ASPDate");
	      }
	    default:
	      throw new Error("Unknown type ".concat(type));
	  }
	}

	
	function typeCoerceDataSet(rawDS) {
	  var _context, _context3, _context4, _context5, _context6, _context7;
	  var type = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {
	    start: "Date",
	    end: "Date"
	  };
	  var idProp = rawDS._idProp;
	  var coercedDS = new esnext.DataSet({
	    fieldId: idProp
	  });
	  var pipe = _mapInstanceProperty(_context = esnext.createNewDataPipeFrom(rawDS)).call(_context, function (item) {
	    var _context2;
	    return _reduceInstanceProperty(_context2 = _Object$keys(item)).call(_context2, function (acc, key) {
	      acc[key] = convert(item[key], type[key]);
	      return acc;
	    }, {});
	  }).to(coercedDS);
	  pipe.all().start();
	  return {
	    
	    add: function add() {
	      var _rawDS$getDataSet;
	      return (_rawDS$getDataSet = rawDS.getDataSet()).add.apply(_rawDS$getDataSet, arguments);
	    },
	    remove: function remove() {
	      var _rawDS$getDataSet2;
	      return (_rawDS$getDataSet2 = rawDS.getDataSet()).remove.apply(_rawDS$getDataSet2, arguments);
	    },
	    update: function update() {
	      var _rawDS$getDataSet3;
	      return (_rawDS$getDataSet3 = rawDS.getDataSet()).update.apply(_rawDS$getDataSet3, arguments);
	    },
	    updateOnly: function updateOnly() {
	      var _rawDS$getDataSet4;
	      return (_rawDS$getDataSet4 = rawDS.getDataSet()).updateOnly.apply(_rawDS$getDataSet4, arguments);
	    },
	    clear: function clear() {
	      var _rawDS$getDataSet5;
	      return (_rawDS$getDataSet5 = rawDS.getDataSet()).clear.apply(_rawDS$getDataSet5, arguments);
	    },
	    
	    forEach: _bindInstanceProperty(_context3 = _forEachInstanceProperty(coercedDS)).call(_context3, coercedDS),
	    get: _bindInstanceProperty(_context4 = coercedDS.get).call(_context4, coercedDS),
	    getIds: _bindInstanceProperty(_context5 = coercedDS.getIds).call(_context5, coercedDS),
	    off: _bindInstanceProperty(_context6 = coercedDS.off).call(_context6, coercedDS),
	    on: _bindInstanceProperty(_context7 = coercedDS.on).call(_context7, coercedDS),
	    get length() {
	      return coercedDS.length;
	    },
	    
	    idProp: idProp,
	    type: type,
	    rawDS: rawDS,
	    coercedDS: coercedDS,
	    dispose: function dispose() {
	      return pipe.stop();
	    }
	  };
	}

	
	var setupXSSCleaner = function setupXSSCleaner(options) {
	  var customXSS = new xssFilter.FilterXSS(options);
	  return function (string) {
	    return customXSS.process(string);
	  };
	};
	var setupNoOpCleaner = function setupNoOpCleaner(string) {
	  return string;
	};

	
	var configuredXSSProtection = setupXSSCleaner();
	var setupXSSProtection = function setupXSSProtection(options) {
	  
	  if (!options) {
	    return;
	  }

	  
	  if (options.disabled === true) {
	    configuredXSSProtection = setupNoOpCleaner;
	    console.warn('You disabled XSS protection for vis-Timeline. I sure hope you know what you\'re doing!');
	  } else {
	    
	    
	    
	    if (options.filterOptions) {
	      configuredXSSProtection = setupXSSCleaner(options.filterOptions);
	    }
	  }
	};
	var availableUtils = _objectSpread(_objectSpread({}, util$2), {}, {
	  convert: convert,
	  setupXSSProtection: setupXSSProtection
	});
	_Object$defineProperty(availableUtils, 'xss', {
	  get: function get() {
	    return configuredXSSProtection;
	  }
	});

	var _parseFloatExports = {};
	var _parseFloat$3 = {
	  get exports(){ return _parseFloatExports; },
	  set exports(v){ _parseFloatExports = v; },
	};

	var global$2 = global$j;
	var fails$5 = fails$u;
	var uncurryThis$2 = functionUncurryThis;
	var toString$2 = toString$b;
	var trim = stringTrim.trim;
	var whitespaces = whitespaces$4;

	var charAt = uncurryThis$2(''.charAt);
	var $parseFloat$1 = global$2.parseFloat;
	var Symbol$1 = global$2.Symbol;
	var ITERATOR = Symbol$1 && Symbol$1.iterator;
	var FORCED$1 = 1 / $parseFloat$1(whitespaces + '-0') !== -Infinity
	  
	  || (ITERATOR && !fails$5(function () { $parseFloat$1(Object(ITERATOR)); }));

	
	
	var numberParseFloat = FORCED$1 ? function parseFloat(string) {
	  var trimmedString = trim(toString$2(string));
	  var result = $parseFloat$1(trimmedString);
	  return result === 0 && charAt(trimmedString, 0) == '-' ? -0 : result;
	} : $parseFloat$1;

	var $$8 = _export;
	var $parseFloat = numberParseFloat;

	
	
	$$8({ global: true, forced: parseFloat != $parseFloat }, {
	  parseFloat: $parseFloat
	});

	var path$2 = path$r;

	var _parseFloat$2 = path$2.parseFloat;

	var parent$7 = _parseFloat$2;

	var _parseFloat$1 = parent$7;

	(function (module) {
		module.exports = _parseFloat$1;
	} (_parseFloat$3));

	var _parseFloat = getDefaultExportFromCjs(_parseFloatExports);

	
	var Component = function () {
	  
	  function Component(body, options) {
	    _classCallCheck(this, Component);
	    
	    this.options = null;
	    this.props = null;
	  }

	  
	  _createClass(Component, [{
	    key: "setOptions",
	    value: function setOptions(options) {
	      if (options) {
	        availableUtils.extend(this.options, options);
	      }
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw() {
	      
	      return false;
	    }

	    
	  }, {
	    key: "destroy",
	    value: function destroy() {
	      
	    }

	    
	  }, {
	    key: "_isResized",
	    value: function _isResized() {
	      var resized = this.props._previousWidth !== this.props.width || this.props._previousHeight !== this.props.height;
	      this.props._previousWidth = this.props.width;
	      this.props._previousHeight = this.props.height;
	      return resized;
	    }
	  }]);
	  return Component;
	}();

	var repeatExports = {};
	var repeat$4 = {
	  get exports(){ return repeatExports; },
	  set exports(v){ repeatExports = v; },
	};

	var toIntegerOrInfinity = toIntegerOrInfinity$5;
	var toString$1 = toString$b;
	var requireObjectCoercible = requireObjectCoercible$6;

	var $RangeError = RangeError;

	
	
	var stringRepeat = function repeat(count) {
	  var str = toString$1(requireObjectCoercible(this));
	  var result = '';
	  var n = toIntegerOrInfinity(count);
	  if (n < 0 || n == Infinity) throw $RangeError('Wrong number of repetitions');
	  for (;n > 0; (n >>>= 1) && (str += str)) if (n & 1) result += str;
	  return result;
	};

	var $$7 = _export;
	var repeat$3 = stringRepeat;

	
	
	$$7({ target: 'String', proto: true }, {
	  repeat: repeat$3
	});

	var entryVirtual$5 = entryVirtual$k;

	var repeat$2 = entryVirtual$5('String').repeat;

	var isPrototypeOf$7 = objectIsPrototypeOf;
	var method$5 = repeat$2;

	var StringPrototype = String.prototype;

	var repeat$1 = function (it) {
	  var own = it.repeat;
	  return typeof it == 'string' || it === StringPrototype
	    || (isPrototypeOf$7(StringPrototype, it) && own === StringPrototype.repeat) ? method$5 : own;
	};

	var parent$6 = repeat$1;

	var repeat = parent$6;

	(function (module) {
		module.exports = repeat;
	} (repeat$4));

	var _repeatInstanceProperty = getDefaultExportFromCjs(repeatExports);

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

	var $$6 = _export;
	var uncurryThis$1 = functionUncurryThis;
	var aCallable = aCallable$7;
	var toObject = toObject$d;
	var lengthOfArrayLike$1 = lengthOfArrayLike$b;
	var deletePropertyOrThrow = deletePropertyOrThrow$2;
	var toString = toString$b;
	var fails$4 = fails$u;
	var internalSort = arraySort;
	var arrayMethodIsStrict$2 = arrayMethodIsStrict$6;
	var FF = engineFfVersion;
	var IE_OR_EDGE = engineIsIeOrEdge;
	var V8 = engineV8Version;
	var WEBKIT = engineWebkitVersion;

	var test = [];
	var nativeSort = uncurryThis$1(test.sort);
	var push = uncurryThis$1(test.push);

	
	var FAILS_ON_UNDEFINED = fails$4(function () {
	  test.sort(undefined);
	});
	
	var FAILS_ON_NULL = fails$4(function () {
	  test.sort(null);
	});
	
	var STRICT_METHOD$2 = arrayMethodIsStrict$2('sort');

	var STABLE_SORT = !fails$4(function () {
	  
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

	var FORCED = FAILS_ON_UNDEFINED || !FAILS_ON_NULL || !STRICT_METHOD$2 || !STABLE_SORT;

	var getSortCompare = function (comparefn) {
	  return function (x, y) {
	    if (y === undefined) return -1;
	    if (x === undefined) return 1;
	    if (comparefn !== undefined) return +comparefn(x, y) || 0;
	    return toString(x) > toString(y) ? 1 : -1;
	  };
	};

	
	
	$$6({ target: 'Array', proto: true, forced: FORCED }, {
	  sort: function sort(comparefn) {
	    if (comparefn !== undefined) aCallable(comparefn);

	    var array = toObject(this);

	    if (STABLE_SORT) return comparefn === undefined ? nativeSort(array) : nativeSort(array, comparefn);

	    var items = [];
	    var arrayLength = lengthOfArrayLike$1(array);
	    var itemsLength, index;

	    for (index = 0; index < arrayLength; index++) {
	      if (index in array) push(items, array[index]);
	    }

	    internalSort(items, getSortCompare(comparefn));

	    itemsLength = lengthOfArrayLike$1(items);
	    index = 0;

	    while (index < itemsLength) array[index] = items[index++];
	    while (index < arrayLength) deletePropertyOrThrow(array, index++);

	    return array;
	  }
	});

	var entryVirtual$4 = entryVirtual$k;

	var sort$2 = entryVirtual$4('Array').sort;

	var isPrototypeOf$6 = objectIsPrototypeOf;
	var method$4 = sort$2;

	var ArrayPrototype$4 = Array.prototype;

	var sort$1 = function (it) {
	  var own = it.sort;
	  return it === ArrayPrototype$4 || (isPrototypeOf$6(ArrayPrototype$4, it) && own === ArrayPrototype$4.sort) ? method$4 : own;
	};

	var parent$5 = sort$1;

	var sort = parent$5;

	(function (module) {
		module.exports = sort;
	} (sort$3));

	var _sortInstanceProperty = getDefaultExportFromCjs(sortExports);

	
	function convertHiddenOptions(moment, body, hiddenDates) {
	  if (hiddenDates && !_Array$isArray$1(hiddenDates)) {
	    return convertHiddenOptions(moment, body, [hiddenDates]);
	  }
	  body.hiddenDates = [];
	  if (hiddenDates) {
	    if (_Array$isArray$1(hiddenDates) == true) {
	      var _context;
	      for (var i = 0; i < hiddenDates.length; i++) {
	        if (_repeatInstanceProperty(hiddenDates[i]) === undefined) {
	          var dateItem = {};
	          dateItem.start = moment(hiddenDates[i].start).toDate().valueOf();
	          dateItem.end = moment(hiddenDates[i].end).toDate().valueOf();
	          body.hiddenDates.push(dateItem);
	        }
	      }
	      _sortInstanceProperty(_context = body.hiddenDates).call(_context, function (a, b) {
	        return a.start - b.start;
	      }); 
	    }
	  }
	}

	
	function updateHiddenDates(moment, body, hiddenDates) {
	  if (hiddenDates && !_Array$isArray$1(hiddenDates)) {
	    return updateHiddenDates(moment, body, [hiddenDates]);
	  }
	  if (hiddenDates && body.domProps.centerContainer.width !== undefined) {
	    convertHiddenOptions(moment, body, hiddenDates);
	    var start = moment(body.range.start);
	    var end = moment(body.range.end);
	    var totalRange = body.range.end - body.range.start;
	    var pixelTime = totalRange / body.domProps.centerContainer.width;
	    for (var i = 0; i < hiddenDates.length; i++) {
	      if (_repeatInstanceProperty(hiddenDates[i]) !== undefined) {
	        var startDate = moment(hiddenDates[i].start);
	        var endDate = moment(hiddenDates[i].end);
	        if (startDate._d == "Invalid Date") {
	          throw new Error("Supplied start date is not valid: ".concat(hiddenDates[i].start));
	        }
	        if (endDate._d == "Invalid Date") {
	          throw new Error("Supplied end date is not valid: ".concat(hiddenDates[i].end));
	        }
	        var duration = endDate - startDate;
	        if (duration >= 4 * pixelTime) {
	          var offset = 0;
	          var runUntil = end.clone();
	          switch (_repeatInstanceProperty(hiddenDates[i])) {
	            case "daily":
	              
	              if (startDate.day() != endDate.day()) {
	                offset = 1;
	              }
	              startDate.dayOfYear(start.dayOfYear());
	              startDate.year(start.year());
	              startDate.subtract(7, 'days');
	              endDate.dayOfYear(start.dayOfYear());
	              endDate.year(start.year());
	              endDate.subtract(7 - offset, 'days');
	              runUntil.add(1, 'weeks');
	              break;
	            case "weekly":
	              {
	                var dayOffset = endDate.diff(startDate, 'days');
	                var day = startDate.day();

	                
	                startDate.date(start.date());
	                startDate.month(start.month());
	                startDate.year(start.year());
	                endDate = startDate.clone();

	                
	                startDate.day(day);
	                endDate.day(day);
	                endDate.add(dayOffset, 'days');
	                startDate.subtract(1, 'weeks');
	                endDate.subtract(1, 'weeks');
	                runUntil.add(1, 'weeks');
	                break;
	              }
	            case "monthly":
	              if (startDate.month() != endDate.month()) {
	                offset = 1;
	              }
	              startDate.month(start.month());
	              startDate.year(start.year());
	              startDate.subtract(1, 'months');
	              endDate.month(start.month());
	              endDate.year(start.year());
	              endDate.subtract(1, 'months');
	              endDate.add(offset, 'months');
	              runUntil.add(1, 'months');
	              break;
	            case "yearly":
	              if (startDate.year() != endDate.year()) {
	                offset = 1;
	              }
	              startDate.year(start.year());
	              startDate.subtract(1, 'years');
	              endDate.year(start.year());
	              endDate.subtract(1, 'years');
	              endDate.add(offset, 'years');
	              runUntil.add(1, 'years');
	              break;
	            default:
	              console.log("Wrong repeat format, allowed are: daily, weekly, monthly, yearly. Given:", _repeatInstanceProperty(hiddenDates[i]));
	              return;
	          }
	          while (startDate < runUntil) {
	            body.hiddenDates.push({
	              start: startDate.valueOf(),
	              end: endDate.valueOf()
	            });
	            switch (_repeatInstanceProperty(hiddenDates[i])) {
	              case "daily":
	                startDate.add(1, 'days');
	                endDate.add(1, 'days');
	                break;
	              case "weekly":
	                startDate.add(1, 'weeks');
	                endDate.add(1, 'weeks');
	                break;
	              case "monthly":
	                startDate.add(1, 'months');
	                endDate.add(1, 'months');
	                break;
	              case "yearly":
	                startDate.add(1, 'y');
	                endDate.add(1, 'y');
	                break;
	              default:
	                console.log("Wrong repeat format, allowed are: daily, weekly, monthly, yearly. Given:", _repeatInstanceProperty(hiddenDates[i]));
	                return;
	            }
	          }
	          body.hiddenDates.push({
	            start: startDate.valueOf(),
	            end: endDate.valueOf()
	          });
	        }
	      }
	    }
	    
	    removeDuplicates(body);
	    
	    var startHidden = getIsHidden(body.range.start, body.hiddenDates);
	    var endHidden = getIsHidden(body.range.end, body.hiddenDates);
	    var rangeStart = body.range.start;
	    var rangeEnd = body.range.end;
	    if (startHidden.hidden == true) {
	      rangeStart = body.range.startToFront == true ? startHidden.startDate - 1 : startHidden.endDate + 1;
	    }
	    if (endHidden.hidden == true) {
	      rangeEnd = body.range.endToFront == true ? endHidden.startDate - 1 : endHidden.endDate + 1;
	    }
	    if (startHidden.hidden == true || endHidden.hidden == true) {
	      body.range._applyRange(rangeStart, rangeEnd);
	    }
	  }
	}

	
	function removeDuplicates(body) {
	  var _context2;
	  var hiddenDates = body.hiddenDates;
	  var safeDates = [];
	  for (var i = 0; i < hiddenDates.length; i++) {
	    for (var j = 0; j < hiddenDates.length; j++) {
	      if (i != j && hiddenDates[j].remove != true && hiddenDates[i].remove != true) {
	        
	        if (hiddenDates[j].start >= hiddenDates[i].start && hiddenDates[j].end <= hiddenDates[i].end) {
	          hiddenDates[j].remove = true;
	        }
	        
	        else if (hiddenDates[j].start >= hiddenDates[i].start && hiddenDates[j].start <= hiddenDates[i].end) {
	          hiddenDates[i].end = hiddenDates[j].end;
	          hiddenDates[j].remove = true;
	        }
	        
	        else if (hiddenDates[j].end >= hiddenDates[i].start && hiddenDates[j].end <= hiddenDates[i].end) {
	          hiddenDates[i].start = hiddenDates[j].start;
	          hiddenDates[j].remove = true;
	        }
	      }
	    }
	  }
	  for (i = 0; i < hiddenDates.length; i++) {
	    if (hiddenDates[i].remove !== true) {
	      safeDates.push(hiddenDates[i]);
	    }
	  }
	  body.hiddenDates = safeDates;
	  _sortInstanceProperty(_context2 = body.hiddenDates).call(_context2, function (a, b) {
	    return a.start - b.start;
	  }); 
	}

	
	function printDates(dates) {
	  for (var i = 0; i < dates.length; i++) {
	    console.log(i, new Date(dates[i].start), new Date(dates[i].end), dates[i].start, dates[i].end, dates[i].remove);
	  }
	}

	
	function stepOverHiddenDates(moment, timeStep, previousTime) {
	  var stepInHidden = false;
	  var currentValue = timeStep.current.valueOf();
	  for (var i = 0; i < timeStep.hiddenDates.length; i++) {
	    var startDate = timeStep.hiddenDates[i].start;
	    var endDate = timeStep.hiddenDates[i].end;
	    if (currentValue >= startDate && currentValue < endDate) {
	      stepInHidden = true;
	      break;
	    }
	  }
	  if (stepInHidden == true && currentValue < timeStep._end.valueOf() && currentValue != previousTime) {
	    var prevValue = moment(previousTime);
	    var newValue = moment(endDate);
	    
	    if (prevValue.year() != newValue.year()) {
	      timeStep.switchedYear = true;
	    } else if (prevValue.month() != newValue.month()) {
	      timeStep.switchedMonth = true;
	    } else if (prevValue.dayOfYear() != newValue.dayOfYear()) {
	      timeStep.switchedDay = true;
	    }
	    timeStep.current = newValue;
	  }
	}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

	
	function toScreen(Core, time, width) {
	  var conversion;
	  if (Core.body.hiddenDates.length == 0) {
	    conversion = Core.range.conversion(width);
	    return (time.valueOf() - conversion.offset) * conversion.scale;
	  } else {
	    var hidden = getIsHidden(time, Core.body.hiddenDates);
	    if (hidden.hidden == true) {
	      time = hidden.startDate;
	    }
	    var duration = getHiddenDurationBetween(Core.body.hiddenDates, Core.range.start, Core.range.end);
	    if (time < Core.range.start) {
	      conversion = Core.range.conversion(width, duration);
	      var hiddenBeforeStart = getHiddenDurationBeforeStart(Core.body.hiddenDates, time, conversion.offset);
	      time = Core.options.moment(time).toDate().valueOf();
	      time = time + hiddenBeforeStart;
	      return -(conversion.offset - time.valueOf()) * conversion.scale;
	    } else if (time > Core.range.end) {
	      var rangeAfterEnd = {
	        start: Core.range.start,
	        end: time
	      };
	      time = correctTimeForHidden(Core.options.moment, Core.body.hiddenDates, rangeAfterEnd, time);
	      conversion = Core.range.conversion(width, duration);
	      return (time.valueOf() - conversion.offset) * conversion.scale;
	    } else {
	      time = correctTimeForHidden(Core.options.moment, Core.body.hiddenDates, Core.range, time);
	      conversion = Core.range.conversion(width, duration);
	      return (time.valueOf() - conversion.offset) * conversion.scale;
	    }
	  }
	}

	
	function toTime(Core, x, width) {
	  if (Core.body.hiddenDates.length == 0) {
	    var conversion = Core.range.conversion(width);
	    return new Date(x / conversion.scale + conversion.offset);
	  } else {
	    var hiddenDuration = getHiddenDurationBetween(Core.body.hiddenDates, Core.range.start, Core.range.end);
	    var totalDuration = Core.range.end - Core.range.start - hiddenDuration;
	    var partialDuration = totalDuration * x / width;
	    var accumulatedHiddenDuration = getAccumulatedHiddenDuration(Core.body.hiddenDates, Core.range, partialDuration);
	    return new Date(accumulatedHiddenDuration + partialDuration + Core.range.start);
	  }
	}

	
	function getHiddenDurationBetween(hiddenDates, start, end) {
	  var duration = 0;
	  for (var i = 0; i < hiddenDates.length; i++) {
	    var startDate = hiddenDates[i].start;
	    var endDate = hiddenDates[i].end;
	    
	    if (startDate >= start && endDate < end) {
	      duration += endDate - startDate;
	    }
	  }
	  return duration;
	}

	
	function getHiddenDurationBeforeStart(hiddenDates, start, end) {
	  var duration = 0;
	  for (var i = 0; i < hiddenDates.length; i++) {
	    var startDate = hiddenDates[i].start;
	    var endDate = hiddenDates[i].end;
	    if (startDate >= start && endDate <= end) {
	      duration += endDate - startDate;
	    }
	  }
	  return duration;
	}

	
	function correctTimeForHidden(moment, hiddenDates, range, time) {
	  time = moment(time).toDate().valueOf();
	  time -= getHiddenDurationBefore(moment, hiddenDates, range, time);
	  return time;
	}

	
	function getHiddenDurationBefore(moment, hiddenDates, range, time) {
	  var timeOffset = 0;
	  time = moment(time).toDate().valueOf();
	  for (var i = 0; i < hiddenDates.length; i++) {
	    var startDate = hiddenDates[i].start;
	    var endDate = hiddenDates[i].end;
	    
	    if (startDate >= range.start && endDate < range.end) {
	      if (time >= endDate) {
	        timeOffset += endDate - startDate;
	      }
	    }
	  }
	  return timeOffset;
	}

	
	function getAccumulatedHiddenDuration(hiddenDates, range, requiredDuration) {
	  var hiddenDuration = 0;
	  var duration = 0;
	  var previousPoint = range.start;
	  
	  for (var i = 0; i < hiddenDates.length; i++) {
	    var startDate = hiddenDates[i].start;
	    var endDate = hiddenDates[i].end;
	    
	    if (startDate >= range.start && endDate < range.end) {
	      duration += startDate - previousPoint;
	      previousPoint = endDate;
	      if (duration >= requiredDuration) {
	        break;
	      } else {
	        hiddenDuration += endDate - startDate;
	      }
	    }
	  }
	  return hiddenDuration;
	}

	
	function snapAwayFromHidden(hiddenDates, time, direction, correctionEnabled) {
	  var isHidden = getIsHidden(time, hiddenDates);
	  if (isHidden.hidden == true) {
	    if (direction < 0) {
	      if (correctionEnabled == true) {
	        return isHidden.startDate - (isHidden.endDate - time) - 1;
	      } else {
	        return isHidden.startDate - 1;
	      }
	    } else {
	      if (correctionEnabled == true) {
	        return isHidden.endDate + (time - isHidden.startDate) + 1;
	      } else {
	        return isHidden.endDate + 1;
	      }
	    }
	  } else {
	    return time;
	  }
	}

	
	function getIsHidden(time, hiddenDates) {
	  for (var i = 0; i < hiddenDates.length; i++) {
	    var startDate = hiddenDates[i].start;
	    var endDate = hiddenDates[i].end;
	    if (time >= startDate && time < endDate) {
	      
	      return {
	        hidden: true,
	        startDate: startDate,
	        endDate: endDate
	      };
	    }
	  }
	  return {
	    hidden: false,
	    startDate: startDate,
	    endDate: endDate
	  };
	}

	var DateUtil = Object.freeze({
		__proto__: null,
		convertHiddenOptions: convertHiddenOptions,
		correctTimeForHidden: correctTimeForHidden,
		getAccumulatedHiddenDuration: getAccumulatedHiddenDuration,
		getHiddenDurationBefore: getHiddenDurationBefore,
		getHiddenDurationBeforeStart: getHiddenDurationBeforeStart,
		getHiddenDurationBetween: getHiddenDurationBetween,
		getIsHidden: getIsHidden,
		printDates: printDates,
		removeDuplicates: removeDuplicates,
		snapAwayFromHidden: snapAwayFromHidden,
		stepOverHiddenDates: stepOverHiddenDates,
		toScreen: toScreen,
		toTime: toTime,
		updateHiddenDates: updateHiddenDates
	});

	function _createSuper$c(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$c(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$c() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	
	var Range = function (_Component) {
	  _inherits(Range, _Component);
	  var _super = _createSuper$c(Range);
	  
	  function Range(body, options) {
	    var _context, _context2, _context3, _context4, _context5, _context6, _context7;
	    var _this;
	    _classCallCheck(this, Range);
	    _this = _super.call(this);
	    var now = moment$2().hours(0).minutes(0).seconds(0).milliseconds(0);
	    var start = now.clone().add(-3, 'days').valueOf();
	    var end = now.clone().add(3, 'days').valueOf();
	    _this.millisecondsPerPixelCache = undefined;
	    if (options === undefined) {
	      _this.start = start;
	      _this.end = end;
	    } else {
	      _this.start = options.start || start;
	      _this.end = options.end || end;
	    }
	    _this.rolling = false;
	    _this.body = body;
	    _this.deltaDifference = 0;
	    _this.scaleOffset = 0;
	    _this.startToFront = false;
	    _this.endToFront = true;

	    
	    _this.defaultOptions = {
	      rtl: false,
	      start: null,
	      end: null,
	      moment: moment$2,
	      direction: 'horizontal',
	      
	      moveable: true,
	      zoomable: true,
	      min: null,
	      max: null,
	      zoomMin: 10,
	      
	      zoomMax: 1000 * 60 * 60 * 24 * 365 * 10000,
	      
	      rollingMode: {
	        follow: false,
	        offset: 0.5
	      }
	    };
	    _this.options = availableUtils.extend({}, _this.defaultOptions);
	    _this.props = {
	      touch: {}
	    };
	    _this.animationTimer = null;

	    
	    _this.body.emitter.on('panstart', _bindInstanceProperty(_context = _this._onDragStart).call(_context, _assertThisInitialized$1(_this)));
	    _this.body.emitter.on('panmove', _bindInstanceProperty(_context2 = _this._onDrag).call(_context2, _assertThisInitialized$1(_this)));
	    _this.body.emitter.on('panend', _bindInstanceProperty(_context3 = _this._onDragEnd).call(_context3, _assertThisInitialized$1(_this)));

	    
	    _this.body.emitter.on('mousewheel', _bindInstanceProperty(_context4 = _this._onMouseWheel).call(_context4, _assertThisInitialized$1(_this)));

	    
	    _this.body.emitter.on('touch', _bindInstanceProperty(_context5 = _this._onTouch).call(_context5, _assertThisInitialized$1(_this)));
	    _this.body.emitter.on('pinch', _bindInstanceProperty(_context6 = _this._onPinch).call(_context6, _assertThisInitialized$1(_this)));

	    
	    _this.body.dom.rollingModeBtn.addEventListener('click', _bindInstanceProperty(_context7 = _this.startRolling).call(_context7, _assertThisInitialized$1(_this)));
	    _this.setOptions(options);
	    return _this;
	  }

	  
	  _createClass(Range, [{
	    key: "setOptions",
	    value: function setOptions(options) {
	      if (options) {
	        
	        var fields = ['animation', 'direction', 'min', 'max', 'zoomMin', 'zoomMax', 'moveable', 'zoomable', 'moment', 'activate', 'hiddenDates', 'zoomKey', 'zoomFriction', 'rtl', 'showCurrentTime', 'rollingMode', 'horizontalScroll'];
	        availableUtils.selectiveExtend(fields, this.options, options);
	        if (options.rollingMode && options.rollingMode.follow) {
	          this.startRolling();
	        }
	        if ('start' in options || 'end' in options) {
	          
	          this.setRange(options.start, options.end);
	        }
	      }
	    }

	    
	  }, {
	    key: "startRolling",
	    value: function startRolling() {
	      var me = this;

	      
	      function update() {
	        me.stopRolling();
	        me.rolling = true;
	        var interval = me.end - me.start;
	        var t = availableUtils.convert(new Date(), 'Date').valueOf();
	        var rollingModeOffset = me.options.rollingMode && me.options.rollingMode.offset || 0.5;
	        var start = t - interval * rollingModeOffset;
	        var end = t + interval * (1 - rollingModeOffset);
	        var options = {
	          animation: false
	        };
	        me.setRange(start, end, options);

	        
	        var scale = me.conversion(me.body.domProps.center.width).scale;
	        interval = 1 / scale / 10;
	        if (interval < 30) interval = 30;
	        if (interval > 1000) interval = 1000;
	        me.body.dom.rollingModeBtn.style.visibility = "hidden";
	        
	        me.currentTimeTimer = _setTimeout(update, interval);
	      }
	      update();
	    }

	    
	  }, {
	    key: "stopRolling",
	    value: function stopRolling() {
	      if (this.currentTimeTimer !== undefined) {
	        clearTimeout(this.currentTimeTimer);
	        this.rolling = false;
	        this.body.dom.rollingModeBtn.style.visibility = "visible";
	      }
	    }

	    
	  }, {
	    key: "setRange",
	    value: function setRange(start, end, options, callback, frameCallback) {
	      if (!options) {
	        options = {};
	      }
	      if (options.byUser !== true) {
	        options.byUser = false;
	      }
	      var me = this;
	      var finalStart = start != undefined ? availableUtils.convert(start, 'Date').valueOf() : null;
	      var finalEnd = end != undefined ? availableUtils.convert(end, 'Date').valueOf() : null;
	      this._cancelAnimation();
	      this.millisecondsPerPixelCache = undefined;
	      if (options.animation) {
	        
	        var initStart = this.start;
	        var initEnd = this.end;
	        var duration = _typeof(options.animation) === 'object' && 'duration' in options.animation ? options.animation.duration : 500;
	        var easingName = _typeof(options.animation) === 'object' && 'easingFunction' in options.animation ? options.animation.easingFunction : 'easeInOutQuad';
	        var easingFunction = availableUtils.easingFunctions[easingName];
	        if (!easingFunction) {
	          var _context8;
	          throw new Error(_concatInstanceProperty(_context8 = "Unknown easing function ".concat(_JSON$stringify(easingName), ". Choose from: ")).call(_context8, _Object$keys(availableUtils.easingFunctions).join(', ')));
	        }
	        var initTime = _Date$now();
	        var anyChanged = false;
	        var next = function next() {
	          if (!me.props.touch.dragging) {
	            var now = _Date$now();
	            var time = now - initTime;
	            var ease = easingFunction(time / duration);
	            var done = time > duration;
	            var s = done || finalStart === null ? finalStart : initStart + (finalStart - initStart) * ease;
	            var e = done || finalEnd === null ? finalEnd : initEnd + (finalEnd - initEnd) * ease;
	            changed = me._applyRange(s, e);
	            updateHiddenDates(me.options.moment, me.body, me.options.hiddenDates);
	            anyChanged = anyChanged || changed;
	            var params = {
	              start: new Date(me.start),
	              end: new Date(me.end),
	              byUser: options.byUser,
	              event: options.event
	            };
	            if (frameCallback) {
	              frameCallback(ease, changed, done);
	            }
	            if (changed) {
	              me.body.emitter.emit('rangechange', params);
	            }
	            if (done) {
	              if (anyChanged) {
	                me.body.emitter.emit('rangechanged', params);
	                if (callback) {
	                  return callback();
	                }
	              }
	            } else {
	              
	              
	              me.animationTimer = _setTimeout(next, 20);
	            }
	          }
	        };
	        return next();
	      } else {
	        var changed = this._applyRange(finalStart, finalEnd);
	        updateHiddenDates(this.options.moment, this.body, this.options.hiddenDates);
	        if (changed) {
	          var params = {
	            start: new Date(this.start),
	            end: new Date(this.end),
	            byUser: options.byUser,
	            event: options.event
	          };
	          this.body.emitter.emit('rangechange', params);
	          clearTimeout(me.timeoutID);
	          me.timeoutID = _setTimeout(function () {
	            me.body.emitter.emit('rangechanged', params);
	          }, 200);
	          if (callback) {
	            return callback();
	          }
	        }
	      }
	    }

	    
	  }, {
	    key: "getMillisecondsPerPixel",
	    value: function getMillisecondsPerPixel() {
	      if (this.millisecondsPerPixelCache === undefined) {
	        this.millisecondsPerPixelCache = (this.end - this.start) / this.body.dom.center.clientWidth;
	      }
	      return this.millisecondsPerPixelCache;
	    }

	    
	  }, {
	    key: "_cancelAnimation",
	    value: function _cancelAnimation() {
	      if (this.animationTimer) {
	        clearTimeout(this.animationTimer);
	        this.animationTimer = null;
	      }
	    }

	    
	  }, {
	    key: "_applyRange",
	    value: function _applyRange(start, end) {
	      var newStart = start != null ? availableUtils.convert(start, 'Date').valueOf() : this.start;
	      var newEnd = end != null ? availableUtils.convert(end, 'Date').valueOf() : this.end;
	      var max = this.options.max != null ? availableUtils.convert(this.options.max, 'Date').valueOf() : null;
	      var min = this.options.min != null ? availableUtils.convert(this.options.min, 'Date').valueOf() : null;
	      var diff;

	      
	      if (isNaN(newStart) || newStart === null) {
	        throw new Error("Invalid start \"".concat(start, "\""));
	      }
	      if (isNaN(newEnd) || newEnd === null) {
	        throw new Error("Invalid end \"".concat(end, "\""));
	      }

	      
	      if (newEnd < newStart) {
	        newEnd = newStart;
	      }

	      
	      if (min !== null) {
	        if (newStart < min) {
	          diff = min - newStart;
	          newStart += diff;
	          newEnd += diff;

	          
	          if (max != null) {
	            if (newEnd > max) {
	              newEnd = max;
	            }
	          }
	        }
	      }

	      
	      if (max !== null) {
	        if (newEnd > max) {
	          diff = newEnd - max;
	          newStart -= diff;
	          newEnd -= diff;

	          
	          if (min != null) {
	            if (newStart < min) {
	              newStart = min;
	            }
	          }
	        }
	      }

	      
	      if (this.options.zoomMin !== null) {
	        var zoomMin = _parseFloat(this.options.zoomMin);
	        if (zoomMin < 0) {
	          zoomMin = 0;
	        }
	        if (newEnd - newStart < zoomMin) {
	          
	          var compensation = 0.5;
	          if (this.end - this.start === zoomMin && newStart >= this.start - compensation && newEnd <= this.end) {
	            
	            newStart = this.start;
	            newEnd = this.end;
	          } else {
	            
	            diff = zoomMin - (newEnd - newStart);
	            newStart -= diff / 2;
	            newEnd += diff / 2;
	          }
	        }
	      }

	      
	      if (this.options.zoomMax !== null) {
	        var zoomMax = _parseFloat(this.options.zoomMax);
	        if (zoomMax < 0) {
	          zoomMax = 0;
	        }
	        if (newEnd - newStart > zoomMax) {
	          if (this.end - this.start === zoomMax && newStart < this.start && newEnd > this.end) {
	            
	            newStart = this.start;
	            newEnd = this.end;
	          } else {
	            
	            diff = newEnd - newStart - zoomMax;
	            newStart += diff / 2;
	            newEnd -= diff / 2;
	          }
	        }
	      }
	      var changed = this.start != newStart || this.end != newEnd;

	      
	      if (!(newStart >= this.start && newStart <= this.end || newEnd >= this.start && newEnd <= this.end) && !(this.start >= newStart && this.start <= newEnd || this.end >= newStart && this.end <= newEnd)) {
	        this.body.emitter.emit('checkRangedItems');
	      }
	      this.start = newStart;
	      this.end = newEnd;
	      return changed;
	    }

	    
	  }, {
	    key: "getRange",
	    value: function getRange() {
	      return {
	        start: this.start,
	        end: this.end
	      };
	    }

	    
	  }, {
	    key: "conversion",
	    value: function conversion(width, totalHidden) {
	      return Range.conversion(this.start, this.end, width, totalHidden);
	    }

	    
	  }, {
	    key: "_onDragStart",
	    value:
	    
	    function _onDragStart(event) {
	      this.deltaDifference = 0;
	      this.previousDelta = 0;

	      
	      if (!this.options.moveable) return;

	      
	      if (!this._isInsideRange(event)) return;

	      
	      
	      if (!this.props.touch.allowDragging) return;
	      this.stopRolling();
	      this.props.touch.start = this.start;
	      this.props.touch.end = this.end;
	      this.props.touch.dragging = true;
	      if (this.body.dom.root) {
	        this.body.dom.root.style.cursor = 'move';
	      }
	    }

	    
	  }, {
	    key: "_onDrag",
	    value: function _onDrag(event) {
	      if (!event) return;
	      if (!this.props.touch.dragging) return;

	      
	      if (!this.options.moveable) return;

	      
	      
	      
	      if (!this.props.touch.allowDragging) return;
	      var direction = this.options.direction;
	      validateDirection(direction);
	      var delta = direction == 'horizontal' ? event.deltaX : event.deltaY;
	      delta -= this.deltaDifference;
	      var interval = this.props.touch.end - this.props.touch.start;

	      
	      var duration = getHiddenDurationBetween(this.body.hiddenDates, this.start, this.end);
	      interval -= duration;
	      var width = direction == 'horizontal' ? this.body.domProps.center.width : this.body.domProps.center.height;
	      var diffRange;
	      if (this.options.rtl) {
	        diffRange = delta / width * interval;
	      } else {
	        diffRange = -delta / width * interval;
	      }
	      var newStart = this.props.touch.start + diffRange;
	      var newEnd = this.props.touch.end + diffRange;

	      
	      var safeStart = snapAwayFromHidden(this.body.hiddenDates, newStart, this.previousDelta - delta, true);
	      var safeEnd = snapAwayFromHidden(this.body.hiddenDates, newEnd, this.previousDelta - delta, true);
	      if (safeStart != newStart || safeEnd != newEnd) {
	        this.deltaDifference += delta;
	        this.props.touch.start = safeStart;
	        this.props.touch.end = safeEnd;
	        this._onDrag(event);
	        return;
	      }
	      this.previousDelta = delta;
	      this._applyRange(newStart, newEnd);
	      var startDate = new Date(this.start);
	      var endDate = new Date(this.end);

	      
	      this.body.emitter.emit('rangechange', {
	        start: startDate,
	        end: endDate,
	        byUser: true,
	        event: event
	      });

	      
	      this.body.emitter.emit('panmove');
	    }

	    
	  }, {
	    key: "_onDragEnd",
	    value: function _onDragEnd(event) {
	      if (!this.props.touch.dragging) return;

	      
	      if (!this.options.moveable) return;

	      
	      
	      
	      if (!this.props.touch.allowDragging) return;
	      this.props.touch.dragging = false;
	      if (this.body.dom.root) {
	        this.body.dom.root.style.cursor = 'auto';
	      }

	      
	      this.body.emitter.emit('rangechanged', {
	        start: new Date(this.start),
	        end: new Date(this.end),
	        byUser: true,
	        event: event
	      });
	    }

	    
	  }, {
	    key: "_onMouseWheel",
	    value: function _onMouseWheel(event) {
	      
	      var delta = 0;
	      if (event.wheelDelta) {
	        
	        delta = event.wheelDelta / 120;
	      } else if (event.detail) {
	        
	        
	        
	        delta = -event.detail / 3;
	      } else if (event.deltaY) {
	        delta = -event.deltaY / 3;
	      }

	      
	      if (this.options.zoomKey && !event[this.options.zoomKey] && this.options.zoomable || !this.options.zoomable && this.options.moveable) {
	        return;
	      }

	      
	      if (!(this.options.zoomable && this.options.moveable)) return;

	      
	      if (!this._isInsideRange(event)) return;

	      
	      
	      
	      if (delta) {
	        

	        
	        

	        var zoomFriction = this.options.zoomFriction || 5;
	        var scale;
	        if (delta < 0) {
	          scale = 1 - delta / zoomFriction;
	        } else {
	          scale = 1 / (1 + delta / zoomFriction);
	        }

	        
	        var pointerDate;
	        if (this.rolling) {
	          var rollingModeOffset = this.options.rollingMode && this.options.rollingMode.offset || 0.5;
	          pointerDate = this.start + (this.end - this.start) * rollingModeOffset;
	        } else {
	          var pointer = this.getPointer({
	            x: event.clientX,
	            y: event.clientY
	          }, this.body.dom.center);
	          pointerDate = this._pointerToDate(pointer);
	        }
	        this.zoom(scale, pointerDate, delta, event);

	        
	        
	        event.preventDefault();
	      }
	    }

	    
	  }, {
	    key: "_onTouch",
	    value: function _onTouch(event) {
	      
	      this.props.touch.start = this.start;
	      this.props.touch.end = this.end;
	      this.props.touch.allowDragging = true;
	      this.props.touch.center = null;
	      this.props.touch.centerDate = null;
	      this.scaleOffset = 0;
	      this.deltaDifference = 0;
	      
	      availableUtils.preventDefault(event);
	    }

	    
	  }, {
	    key: "_onPinch",
	    value: function _onPinch(event) {
	      
	      if (!(this.options.zoomable && this.options.moveable)) return;

	      
	      availableUtils.preventDefault(event);
	      this.props.touch.allowDragging = false;
	      if (!this.props.touch.center) {
	        this.props.touch.center = this.getPointer(event.center, this.body.dom.center);
	        this.props.touch.centerDate = this._pointerToDate(this.props.touch.center);
	      }
	      this.stopRolling();
	      var scale = 1 / (event.scale + this.scaleOffset);
	      var centerDate = this.props.touch.centerDate;
	      var hiddenDuration = getHiddenDurationBetween(this.body.hiddenDates, this.start, this.end);
	      var hiddenDurationBefore = getHiddenDurationBefore(this.options.moment, this.body.hiddenDates, this, centerDate);
	      var hiddenDurationAfter = hiddenDuration - hiddenDurationBefore;

	      
	      var newStart = centerDate - hiddenDurationBefore + (this.props.touch.start - (centerDate - hiddenDurationBefore)) * scale;
	      var newEnd = centerDate + hiddenDurationAfter + (this.props.touch.end - (centerDate + hiddenDurationAfter)) * scale;

	      
	      this.startToFront = 1 - scale <= 0; 
	      this.endToFront = scale - 1 <= 0; 

	      var safeStart = snapAwayFromHidden(this.body.hiddenDates, newStart, 1 - scale, true);
	      var safeEnd = snapAwayFromHidden(this.body.hiddenDates, newEnd, scale - 1, true);
	      if (safeStart != newStart || safeEnd != newEnd) {
	        this.props.touch.start = safeStart;
	        this.props.touch.end = safeEnd;
	        this.scaleOffset = 1 - event.scale;
	        newStart = safeStart;
	        newEnd = safeEnd;
	      }
	      var options = {
	        animation: false,
	        byUser: true,
	        event: event
	      };
	      this.setRange(newStart, newEnd, options);
	      this.startToFront = false; 
	      this.endToFront = true; 
	    }

	    
	  }, {
	    key: "_isInsideRange",
	    value: function _isInsideRange(event) {
	      
	      
	      var clientX = event.center ? event.center.x : event.clientX;
	      var centerContainerRect = this.body.dom.centerContainer.getBoundingClientRect();
	      var x = this.options.rtl ? clientX - centerContainerRect.left : centerContainerRect.right - clientX;
	      var time = this.body.util.toTime(x);
	      return time >= this.start && time <= this.end;
	    }

	    
	  }, {
	    key: "_pointerToDate",
	    value: function _pointerToDate(pointer) {
	      var conversion;
	      var direction = this.options.direction;
	      validateDirection(direction);
	      if (direction == 'horizontal') {
	        return this.body.util.toTime(pointer.x).valueOf();
	      } else {
	        var height = this.body.domProps.center.height;
	        conversion = this.conversion(height);
	        return pointer.y / conversion.scale + conversion.offset;
	      }
	    }

	    
	  }, {
	    key: "getPointer",
	    value: function getPointer(touch, element) {
	      var elementRect = element.getBoundingClientRect();
	      if (this.options.rtl) {
	        return {
	          x: elementRect.right - touch.x,
	          y: touch.y - elementRect.top
	        };
	      } else {
	        return {
	          x: touch.x - elementRect.left,
	          y: touch.y - elementRect.top
	        };
	      }
	    }

	    
	  }, {
	    key: "zoom",
	    value: function zoom(scale, center, delta, event) {
	      
	      if (center == null) {
	        center = (this.start + this.end) / 2;
	      }
	      var hiddenDuration = getHiddenDurationBetween(this.body.hiddenDates, this.start, this.end);
	      var hiddenDurationBefore = getHiddenDurationBefore(this.options.moment, this.body.hiddenDates, this, center);
	      var hiddenDurationAfter = hiddenDuration - hiddenDurationBefore;

	      
	      var newStart = center - hiddenDurationBefore + (this.start - (center - hiddenDurationBefore)) * scale;
	      var newEnd = center + hiddenDurationAfter + (this.end - (center + hiddenDurationAfter)) * scale;

	      
	      this.startToFront = delta > 0 ? false : true; 
	      this.endToFront = -delta > 0 ? false : true; 
	      var safeStart = snapAwayFromHidden(this.body.hiddenDates, newStart, delta, true);
	      var safeEnd = snapAwayFromHidden(this.body.hiddenDates, newEnd, -delta, true);
	      if (safeStart != newStart || safeEnd != newEnd) {
	        newStart = safeStart;
	        newEnd = safeEnd;
	      }
	      var options = {
	        animation: false,
	        byUser: true,
	        event: event
	      };
	      this.setRange(newStart, newEnd, options);
	      this.startToFront = false; 
	      this.endToFront = true; 
	    }

	    
	  }, {
	    key: "move",
	    value: function move(delta) {
	      
	      var diff = this.end - this.start;

	      
	      var newStart = this.start + diff * delta;
	      var newEnd = this.end + diff * delta;

	      

	      this.start = newStart;
	      this.end = newEnd;
	    }

	    
	  }, {
	    key: "moveTo",
	    value: function moveTo(_moveTo) {
	      var center = (this.start + this.end) / 2;
	      var diff = center - _moveTo;

	      
	      var newStart = this.start - diff;
	      var newEnd = this.end - diff;
	      var options = {
	        animation: false,
	        byUser: true,
	        event: null
	      };
	      this.setRange(newStart, newEnd, options);
	    }
	  }], [{
	    key: "conversion",
	    value: function conversion(start, end, width, totalHidden) {
	      if (totalHidden === undefined) {
	        totalHidden = 0;
	      }
	      if (width != 0 && end - start != 0) {
	        return {
	          offset: start,
	          scale: width / (end - start - totalHidden)
	        };
	      } else {
	        return {
	          offset: 0,
	          scale: 1
	        };
	      }
	    }
	  }]);
	  return Range;
	}(Component);
	function validateDirection(direction) {
	  if (direction != 'horizontal' && direction != 'vertical') {
	    throw new TypeError("Unknown direction \"".concat(direction, "\". Choose \"horizontal\" or \"vertical\"."));
	  }
	}

	var someExports = {};
	var some$3 = {
	  get exports(){ return someExports; },
	  set exports(v){ someExports = v; },
	};

	var $$5 = _export;
	var $some = arrayIteration.some;
	var arrayMethodIsStrict$1 = arrayMethodIsStrict$6;

	var STRICT_METHOD$1 = arrayMethodIsStrict$1('some');

	
	
	$$5({ target: 'Array', proto: true, forced: !STRICT_METHOD$1 }, {
	  some: function some(callbackfn ) {
	    return $some(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$3 = entryVirtual$k;

	var some$2 = entryVirtual$3('Array').some;

	var isPrototypeOf$5 = objectIsPrototypeOf;
	var method$3 = some$2;

	var ArrayPrototype$3 = Array.prototype;

	var some$1 = function (it) {
	  var own = it.some;
	  return it === ArrayPrototype$3 || (isPrototypeOf$5(ArrayPrototype$3, it) && own === ArrayPrototype$3.some) ? method$3 : own;
	};

	var parent$4 = some$1;

	var some = parent$4;

	(function (module) {
		module.exports = some;
	} (some$3));

	var _someInstanceProperty = getDefaultExportFromCjs(someExports);

	var setIntervalExports = {};
	var setInterval$1 = {
	  get exports(){ return setIntervalExports; },
	  set exports(v){ setIntervalExports = v; },
	};

	var path$1 = path$r;

	var setInterval = path$1.setInterval;

	(function (module) {
		module.exports = setInterval;
	} (setInterval$1));

	var _setInterval = getDefaultExportFromCjs(setIntervalExports);

	var _firstTarget = null; 

	
	function propagating(hammer, options) {
	  var _options = options || {
	    preventDefault: false
	  };

	  if (hammer.Manager) {
	    
	    
	    var Hammer = hammer;

	    var PropagatingHammer = function(element, options) {
	      var o = Object.create(_options);
	      if (options) Hammer.assign(o, options);
	      return propagating(new Hammer(element, o), o);
	    };
	    Hammer.assign(PropagatingHammer, Hammer);

	    PropagatingHammer.Manager = function (element, options) {
	      var o = Object.create(_options);
	      if (options) Hammer.assign(o, options);
	      return propagating(new Hammer.Manager(element, o), o);
	    };

	    return PropagatingHammer;
	  }

	  
	  
	  var wrapper = Object.create(hammer);

	  
	  var element = hammer.element;

	  if(!element.hammer) element.hammer = [];
	  element.hammer.push(wrapper);

	  
	  
	  hammer.on('hammer.input', function (event) {
	    if (_options.preventDefault === true || (_options.preventDefault === event.pointerType)) {
	      event.preventDefault();
	    }
	    if (event.isFirst) {
	      _firstTarget = event.target;
	    }
	  });

	  
	  wrapper._handlers = {};

	  
	  wrapper.on = function (events, handler) {
	    
	    split(events).forEach(function (event) {
	      var _handlers = wrapper._handlers[event];
	      if (!_handlers) {
	        wrapper._handlers[event] = _handlers = [];

	        
	        hammer.on(event, propagatedHandler);
	      }
	      _handlers.push(handler);
	    });

	    return wrapper;
	  };

	  
	  wrapper.off = function (events, handler) {
	    
	    split(events).forEach(function (event) {
	      var _handlers = wrapper._handlers[event];
	      if (_handlers) {
	        _handlers = handler ? _handlers.filter(function (h) {
	          return h !== handler;
	        }) : [];

	        if (_handlers.length > 0) {
	          wrapper._handlers[event] = _handlers;
	        }
	        else {
	          
	          hammer.off(event, propagatedHandler);
	          delete wrapper._handlers[event];
	        }
	      }
	    });

	    return wrapper;
	  };

	  
	  wrapper.emit = function(eventType, event) {
	    _firstTarget = event.target;
	    hammer.emit(eventType, event);
	  };

	  wrapper.destroy = function () {
	    
	    var hammers = hammer.element.hammer;
	    var idx = hammers.indexOf(wrapper);
	    if(idx !== -1) hammers.splice(idx,1);
	    if(!hammers.length) delete hammer.element.hammer;

	    
	    wrapper._handlers = {};

	    
	    hammer.destroy();
	  };

	  
	  function split(events) {
	    return events.match(/[^ ]+/g);
	  }

	  
	  function propagatedHandler(event) {
	    
	    if (event.type !== 'hammer.input') {
	      
	      
	      if (!event.srcEvent._handled) {
	        event.srcEvent._handled = {};
	      }

	      if (event.srcEvent._handled[event.type]) {
	        return;
	      }
	      else {
	        event.srcEvent._handled[event.type] = true;
	      }
	    }

	    
	    var stopped = false;
	    event.stopPropagation = function () {
	      stopped = true;
	    };

	    
	    var srcStop = event.srcEvent.stopPropagation.bind(event.srcEvent);
	    if(typeof srcStop == "function") {
	      event.srcEvent.stopPropagation = function(){
	        srcStop();
	        event.stopPropagation();
	      };
	    }

	    
	    event.firstTarget = _firstTarget;

	    
	    var elem = _firstTarget;
	    while (elem && !stopped) {
	      var elemHammer = elem.hammer;
	      if(elemHammer){
	        var _handlers;
	        for(var k = 0; k < elemHammer.length; k++){
	          _handlers = elemHammer[k]._handlers[event.type];
	          if(_handlers) for (var i = 0; i < _handlers.length && !stopped; i++) {
	            _handlers[i](event);
	          }
	        }
	      }
	      elem = elem.parentNode;
	    }
	  }

	  return wrapper;
	}

	
	function hammerMock() {
	  var noop = function noop() {};
	  return {
	    on: noop,
	    off: noop,
	    destroy: noop,
	    emit: noop,
	    get: function get(m) {
	      
	      return {
	        set: noop
	      };
	    }
	  };
	}
	var modifiedHammer;
	if (typeof window !== 'undefined') {
	  var OurHammer = window['Hammer'] || Hammer$4;
	  modifiedHammer = propagating(OurHammer, {
	    preventDefault: 'mouse'
	  });
	} else {
	  modifiedHammer = function modifiedHammer() {
	    return (
	      
	      hammerMock()
	    );
	  };
	}
	var Hammer = modifiedHammer;

	
	function onTouch(hammer, callback) {
	  callback.inputHandler = function (event) {
	    if (event.isFirst) {
	      callback(event);
	    }
	  };
	  hammer.on('hammer.input', callback.inputHandler);
	}

	
	function onRelease(hammer, callback) {
	  callback.inputHandler = function (event) {
	    if (event.isFinal) {
	      callback(event);
	    }
	  };
	  return hammer.on('hammer.input', callback.inputHandler);
	}

	
	function disablePreventDefaultVertically(pinchRecognizer) {
	  var TOUCH_ACTION_PAN_Y = 'pan-y';
	  pinchRecognizer.getTouchAction = function () {
	    
	    return [TOUCH_ACTION_PAN_Y];
	  };
	  return pinchRecognizer;
	}

	
	var TimeStep = function () {
	  
	  function TimeStep(start, end, minimumStep, hiddenDates, options) {
	    _classCallCheck(this, TimeStep);
	    this.moment = options && options.moment || moment$2;
	    this.options = options ? options : {};

	    
	    this.current = this.moment();
	    this._start = this.moment();
	    this._end = this.moment();
	    this.autoScale = true;
	    this.scale = 'day';
	    this.step = 1;

	    
	    this.setRange(start, end, minimumStep);

	    
	    this.switchedDay = false;
	    this.switchedMonth = false;
	    this.switchedYear = false;
	    if (_Array$isArray$1(hiddenDates)) {
	      this.hiddenDates = hiddenDates;
	    } else if (hiddenDates != undefined) {
	      this.hiddenDates = [hiddenDates];
	    } else {
	      this.hiddenDates = [];
	    }
	    this.format = TimeStep.FORMAT; 
	  }

	  
	  _createClass(TimeStep, [{
	    key: "setMoment",
	    value: function setMoment(moment) {
	      this.moment = moment;

	      
	      this.current = this.moment(this.current.valueOf());
	      this._start = this.moment(this._start.valueOf());
	      this._end = this.moment(this._end.valueOf());
	    }

	    
	  }, {
	    key: "setFormat",
	    value: function setFormat(format) {
	      var defaultFormat = availableUtils.deepExtend({}, TimeStep.FORMAT);
	      this.format = availableUtils.deepExtend(defaultFormat, format);
	    }

	    
	  }, {
	    key: "setRange",
	    value: function setRange(start, end, minimumStep) {
	      if (!(start instanceof Date) || !(end instanceof Date)) {
	        throw "No legal start or end date in method setRange";
	      }
	      this._start = start != undefined ? this.moment(start.valueOf()) : _Date$now();
	      this._end = end != undefined ? this.moment(end.valueOf()) : _Date$now();
	      if (this.autoScale) {
	        this.setMinimumStep(minimumStep);
	      }
	    }

	    
	  }, {
	    key: "start",
	    value: function start() {
	      this.current = this._start.clone();
	      this.roundToMinor();
	    }

	    
	  }, {
	    key: "roundToMinor",
	    value: function roundToMinor() {
	      
	      
	      if (this.scale == 'week') {
	        this.current.weekday(0);
	      }
	      
	      
	      switch (this.scale) {
	        case 'year':
	          this.current.year(this.step * Math.floor(this.current.year() / this.step));
	          this.current.month(0);
	        
	        case 'month':
	          this.current.date(1);
	        
	        case 'week': 
	        case 'day': 
	        case 'weekday':
	          this.current.hours(0);
	        
	        case 'hour':
	          this.current.minutes(0);
	        
	        case 'minute':
	          this.current.seconds(0);
	        
	        case 'second':
	          this.current.milliseconds(0);
	        
	        
	      }

	      if (this.step != 1) {
	        
	        var priorCurrent = this.current.clone();
	        switch (this.scale) {
	          case 'millisecond':
	            this.current.subtract(this.current.milliseconds() % this.step, 'milliseconds');
	            break;
	          case 'second':
	            this.current.subtract(this.current.seconds() % this.step, 'seconds');
	            break;
	          case 'minute':
	            this.current.subtract(this.current.minutes() % this.step, 'minutes');
	            break;
	          case 'hour':
	            this.current.subtract(this.current.hours() % this.step, 'hours');
	            break;
	          case 'weekday': 
	          case 'day':
	            this.current.subtract((this.current.date() - 1) % this.step, 'day');
	            break;
	          case 'week':
	            this.current.subtract(this.current.week() % this.step, 'week');
	            break;
	          case 'month':
	            this.current.subtract(this.current.month() % this.step, 'month');
	            break;
	          case 'year':
	            this.current.subtract(this.current.year() % this.step, 'year');
	            break;
	        }
	        if (!priorCurrent.isSame(this.current)) {
	          this.current = this.moment(snapAwayFromHidden(this.hiddenDates, this.current.valueOf(), -1, true));
	        }
	      }
	    }

	    
	  }, {
	    key: "hasNext",
	    value: function hasNext() {
	      return this.current.valueOf() <= this._end.valueOf();
	    }

	    
	  }, {
	    key: "next",
	    value: function next() {
	      var prev = this.current.valueOf();

	      
	      
	      switch (this.scale) {
	        case 'millisecond':
	          this.current.add(this.step, 'millisecond');
	          break;
	        case 'second':
	          this.current.add(this.step, 'second');
	          break;
	        case 'minute':
	          this.current.add(this.step, 'minute');
	          break;
	        case 'hour':
	          this.current.add(this.step, 'hour');
	          if (this.current.month() < 6) {
	            this.current.subtract(this.current.hours() % this.step, 'hour');
	          } else {
	            if (this.current.hours() % this.step !== 0) {
	              this.current.add(this.step - this.current.hours() % this.step, 'hour');
	            }
	          }
	          break;
	        case 'weekday': 
	        case 'day':
	          this.current.add(this.step, 'day');
	          break;
	        case 'week':
	          if (this.current.weekday() !== 0) {
	            
	            this.current.weekday(0); 
	            this.current.add(this.step, 'week');
	          } else if (this.options.showMajorLabels === false) {
	            this.current.add(this.step, 'week'); 
	          } else {
	            
	            var nextWeek = this.current.clone();
	            nextWeek.add(1, 'week');
	            if (nextWeek.isSame(this.current, 'month')) {
	              
	              this.current.add(this.step, 'week'); 
	            } else {
	              
	              this.current.add(this.step, 'week');
	              this.current.date(1);
	            }
	          }
	          break;
	        case 'month':
	          this.current.add(this.step, 'month');
	          break;
	        case 'year':
	          this.current.add(this.step, 'year');
	          break;
	      }
	      if (this.step != 1) {
	        
	        switch (this.scale) {
	          case 'millisecond':
	            if (this.current.milliseconds() > 0 && this.current.milliseconds() < this.step) this.current.milliseconds(0);
	            break;
	          case 'second':
	            if (this.current.seconds() > 0 && this.current.seconds() < this.step) this.current.seconds(0);
	            break;
	          case 'minute':
	            if (this.current.minutes() > 0 && this.current.minutes() < this.step) this.current.minutes(0);
	            break;
	          case 'hour':
	            if (this.current.hours() > 0 && this.current.hours() < this.step) this.current.hours(0);
	            break;
	          case 'weekday': 
	          case 'day':
	            if (this.current.date() < this.step + 1) this.current.date(1);
	            break;
	          case 'week':
	            if (this.current.week() < this.step) this.current.week(1);
	            break;
	          
	          case 'month':
	            if (this.current.month() < this.step) this.current.month(0);
	            break;
	        }
	      }

	      
	      if (this.current.valueOf() == prev) {
	        this.current = this._end.clone();
	      }

	      
	      this.switchedDay = false;
	      this.switchedMonth = false;
	      this.switchedYear = false;
	      stepOverHiddenDates(this.moment, this, prev);
	    }

	    
	  }, {
	    key: "getCurrent",
	    value: function getCurrent() {
	      return this.current.clone();
	    }

	    
	  }, {
	    key: "setScale",
	    value: function setScale(params) {
	      if (params && typeof params.scale == 'string') {
	        this.scale = params.scale;
	        this.step = params.step > 0 ? params.step : 1;
	        this.autoScale = false;
	      }
	    }

	    
	  }, {
	    key: "setAutoScale",
	    value: function setAutoScale(enable) {
	      this.autoScale = enable;
	    }

	    
	  }, {
	    key: "setMinimumStep",
	    value: function setMinimumStep(minimumStep) {
	      if (minimumStep == undefined) {
	        return;
	      }

	      

	      var stepYear = 1000 * 60 * 60 * 24 * 30 * 12;
	      var stepMonth = 1000 * 60 * 60 * 24 * 30;
	      var stepDay = 1000 * 60 * 60 * 24;
	      var stepHour = 1000 * 60 * 60;
	      var stepMinute = 1000 * 60;
	      var stepSecond = 1000;
	      var stepMillisecond = 1;

	      
	      if (stepYear * 1000 > minimumStep) {
	        this.scale = 'year';
	        this.step = 1000;
	      }
	      if (stepYear * 500 > minimumStep) {
	        this.scale = 'year';
	        this.step = 500;
	      }
	      if (stepYear * 100 > minimumStep) {
	        this.scale = 'year';
	        this.step = 100;
	      }
	      if (stepYear * 50 > minimumStep) {
	        this.scale = 'year';
	        this.step = 50;
	      }
	      if (stepYear * 10 > minimumStep) {
	        this.scale = 'year';
	        this.step = 10;
	      }
	      if (stepYear * 5 > minimumStep) {
	        this.scale = 'year';
	        this.step = 5;
	      }
	      if (stepYear > minimumStep) {
	        this.scale = 'year';
	        this.step = 1;
	      }
	      if (stepMonth * 3 > minimumStep) {
	        this.scale = 'month';
	        this.step = 3;
	      }
	      if (stepMonth > minimumStep) {
	        this.scale = 'month';
	        this.step = 1;
	      }
	      if (stepDay * 7 > minimumStep && this.options.showWeekScale) {
	        this.scale = 'week';
	        this.step = 1;
	      }
	      if (stepDay * 2 > minimumStep) {
	        this.scale = 'day';
	        this.step = 2;
	      }
	      if (stepDay > minimumStep) {
	        this.scale = 'day';
	        this.step = 1;
	      }
	      if (stepDay / 2 > minimumStep) {
	        this.scale = 'weekday';
	        this.step = 1;
	      }
	      if (stepHour * 4 > minimumStep) {
	        this.scale = 'hour';
	        this.step = 4;
	      }
	      if (stepHour > minimumStep) {
	        this.scale = 'hour';
	        this.step = 1;
	      }
	      if (stepMinute * 15 > minimumStep) {
	        this.scale = 'minute';
	        this.step = 15;
	      }
	      if (stepMinute * 10 > minimumStep) {
	        this.scale = 'minute';
	        this.step = 10;
	      }
	      if (stepMinute * 5 > minimumStep) {
	        this.scale = 'minute';
	        this.step = 5;
	      }
	      if (stepMinute > minimumStep) {
	        this.scale = 'minute';
	        this.step = 1;
	      }
	      if (stepSecond * 15 > minimumStep) {
	        this.scale = 'second';
	        this.step = 15;
	      }
	      if (stepSecond * 10 > minimumStep) {
	        this.scale = 'second';
	        this.step = 10;
	      }
	      if (stepSecond * 5 > minimumStep) {
	        this.scale = 'second';
	        this.step = 5;
	      }
	      if (stepSecond > minimumStep) {
	        this.scale = 'second';
	        this.step = 1;
	      }
	      if (stepMillisecond * 200 > minimumStep) {
	        this.scale = 'millisecond';
	        this.step = 200;
	      }
	      if (stepMillisecond * 100 > minimumStep) {
	        this.scale = 'millisecond';
	        this.step = 100;
	      }
	      if (stepMillisecond * 50 > minimumStep) {
	        this.scale = 'millisecond';
	        this.step = 50;
	      }
	      if (stepMillisecond * 10 > minimumStep) {
	        this.scale = 'millisecond';
	        this.step = 10;
	      }
	      if (stepMillisecond * 5 > minimumStep) {
	        this.scale = 'millisecond';
	        this.step = 5;
	      }
	      if (stepMillisecond > minimumStep) {
	        this.scale = 'millisecond';
	        this.step = 1;
	      }
	    }

	    
	  }, {
	    key: "isMajor",
	    value:
	    
	    function isMajor() {
	      if (this.switchedYear == true) {
	        switch (this.scale) {
	          case 'year':
	          case 'month':
	          case 'week':
	          case 'weekday':
	          case 'day':
	          case 'hour':
	          case 'minute':
	          case 'second':
	          case 'millisecond':
	            return true;
	          default:
	            return false;
	        }
	      } else if (this.switchedMonth == true) {
	        switch (this.scale) {
	          case 'week':
	          case 'weekday':
	          case 'day':
	          case 'hour':
	          case 'minute':
	          case 'second':
	          case 'millisecond':
	            return true;
	          default:
	            return false;
	        }
	      } else if (this.switchedDay == true) {
	        switch (this.scale) {
	          case 'millisecond':
	          case 'second':
	          case 'minute':
	          case 'hour':
	            return true;
	          default:
	            return false;
	        }
	      }
	      var date = this.moment(this.current);
	      switch (this.scale) {
	        case 'millisecond':
	          return date.milliseconds() == 0;
	        case 'second':
	          return date.seconds() == 0;
	        case 'minute':
	          return date.hours() == 0 && date.minutes() == 0;
	        case 'hour':
	          return date.hours() == 0;
	        case 'weekday': 
	        case 'day':
	          return this.options.showWeekScale ? date.isoWeekday() == 1 : date.date() == 1;
	        case 'week':
	          return date.date() == 1;
	        case 'month':
	          return date.month() == 0;
	        case 'year':
	          return false;
	        default:
	          return false;
	      }
	    }

	    
	  }, {
	    key: "getLabelMinor",
	    value: function getLabelMinor(date) {
	      if (date == undefined) {
	        date = this.current;
	      }
	      if (date instanceof Date) {
	        date = this.moment(date);
	      }
	      if (typeof this.format.minorLabels === "function") {
	        return this.format.minorLabels(date, this.scale, this.step);
	      }
	      var format = this.format.minorLabels[this.scale];
	      
	      switch (this.scale) {
	        case 'week':
	          
	          
	          if (date.date() === 1 && date.weekday() !== 0) {
	            return "";
	          }
	        default:
	          
	          return format && format.length > 0 ? this.moment(date).format(format) : '';
	      }
	    }

	    
	  }, {
	    key: "getLabelMajor",
	    value: function getLabelMajor(date) {
	      if (date == undefined) {
	        date = this.current;
	      }
	      if (date instanceof Date) {
	        date = this.moment(date);
	      }
	      if (typeof this.format.majorLabels === "function") {
	        return this.format.majorLabels(date, this.scale, this.step);
	      }
	      var format = this.format.majorLabels[this.scale];
	      return format && format.length > 0 ? this.moment(date).format(format) : '';
	    }

	    
	  }, {
	    key: "getClassName",
	    value: function getClassName() {
	      var _context;
	      var _moment = this.moment;
	      var m = this.moment(this.current);
	      var current = m.locale ? m.locale('en') : m.lang('en'); 
	      var step = this.step;
	      var classNames = [];

	      
	      function even(value) {
	        return value / step % 2 == 0 ? ' vis-even' : ' vis-odd';
	      }

	      
	      function today(date) {
	        if (date.isSame(_Date$now(), 'day')) {
	          return ' vis-today';
	        }
	        if (date.isSame(_moment().add(1, 'day'), 'day')) {
	          return ' vis-tomorrow';
	        }
	        if (date.isSame(_moment().add(-1, 'day'), 'day')) {
	          return ' vis-yesterday';
	        }
	        return '';
	      }

	      
	      function currentWeek(date) {
	        return date.isSame(_Date$now(), 'week') ? ' vis-current-week' : '';
	      }

	      
	      function currentMonth(date) {
	        return date.isSame(_Date$now(), 'month') ? ' vis-current-month' : '';
	      }

	      
	      function currentYear(date) {
	        return date.isSame(_Date$now(), 'year') ? ' vis-current-year' : '';
	      }
	      switch (this.scale) {
	        case 'millisecond':
	          classNames.push(today(current));
	          classNames.push(even(current.milliseconds()));
	          break;
	        case 'second':
	          classNames.push(today(current));
	          classNames.push(even(current.seconds()));
	          break;
	        case 'minute':
	          classNames.push(today(current));
	          classNames.push(even(current.minutes()));
	          break;
	        case 'hour':
	          classNames.push(_concatInstanceProperty(_context = "vis-h".concat(current.hours())).call(_context, this.step == 4 ? '-h' + (current.hours() + 4) : ''));
	          classNames.push(today(current));
	          classNames.push(even(current.hours()));
	          break;
	        case 'weekday':
	          classNames.push("vis-".concat(current.format('dddd').toLowerCase()));
	          classNames.push(today(current));
	          classNames.push(currentWeek(current));
	          classNames.push(even(current.date()));
	          break;
	        case 'day':
	          classNames.push("vis-day".concat(current.date()));
	          classNames.push("vis-".concat(current.format('MMMM').toLowerCase()));
	          classNames.push(today(current));
	          classNames.push(currentMonth(current));
	          classNames.push(this.step <= 2 ? today(current) : '');
	          classNames.push(this.step <= 2 ? "vis-".concat(current.format('dddd').toLowerCase()) : '');
	          classNames.push(even(current.date() - 1));
	          break;
	        case 'week':
	          classNames.push("vis-week".concat(current.format('w')));
	          classNames.push(currentWeek(current));
	          classNames.push(even(current.week()));
	          break;
	        case 'month':
	          classNames.push("vis-".concat(current.format('MMMM').toLowerCase()));
	          classNames.push(currentMonth(current));
	          classNames.push(even(current.month()));
	          break;
	        case 'year':
	          classNames.push("vis-year".concat(current.year()));
	          classNames.push(currentYear(current));
	          classNames.push(even(current.year()));
	          break;
	      }
	      return _filterInstanceProperty(classNames).call(classNames, String).join(" ");
	    }
	  }], [{
	    key: "snap",
	    value: function snap(date, scale, step) {
	      var clone = moment$2(date);
	      if (scale == 'year') {
	        var year = clone.year() + Math.round(clone.month() / 12);
	        clone.year(Math.round(year / step) * step);
	        clone.month(0);
	        clone.date(0);
	        clone.hours(0);
	        clone.minutes(0);
	        clone.seconds(0);
	        clone.milliseconds(0);
	      } else if (scale == 'month') {
	        if (clone.date() > 15) {
	          clone.date(1);
	          clone.add(1, 'month');
	          
	        } else {
	          clone.date(1);
	        }
	        clone.hours(0);
	        clone.minutes(0);
	        clone.seconds(0);
	        clone.milliseconds(0);
	      } else if (scale == 'week') {
	        if (clone.weekday() > 2) {
	          
	          clone.weekday(0);
	          clone.add(1, 'week');
	        } else {
	          clone.weekday(0);
	        }
	        clone.hours(0);
	        clone.minutes(0);
	        clone.seconds(0);
	        clone.milliseconds(0);
	      } else if (scale == 'day') {
	        
	        switch (step) {
	          case 5:
	          case 2:
	            clone.hours(Math.round(clone.hours() / 24) * 24);
	            break;
	          default:
	            clone.hours(Math.round(clone.hours() / 12) * 12);
	            break;
	        }
	        clone.minutes(0);
	        clone.seconds(0);
	        clone.milliseconds(0);
	      } else if (scale == 'weekday') {
	        
	        switch (step) {
	          case 5:
	          case 2:
	            clone.hours(Math.round(clone.hours() / 12) * 12);
	            break;
	          default:
	            clone.hours(Math.round(clone.hours() / 6) * 6);
	            break;
	        }
	        clone.minutes(0);
	        clone.seconds(0);
	        clone.milliseconds(0);
	      } else if (scale == 'hour') {
	        switch (step) {
	          case 4:
	            clone.minutes(Math.round(clone.minutes() / 60) * 60);
	            break;
	          default:
	            clone.minutes(Math.round(clone.minutes() / 30) * 30);
	            break;
	        }
	        clone.seconds(0);
	        clone.milliseconds(0);
	      } else if (scale == 'minute') {
	        
	        switch (step) {
	          case 15:
	          case 10:
	            clone.minutes(Math.round(clone.minutes() / 5) * 5);
	            clone.seconds(0);
	            break;
	          case 5:
	            clone.seconds(Math.round(clone.seconds() / 60) * 60);
	            break;
	          default:
	            clone.seconds(Math.round(clone.seconds() / 30) * 30);
	            break;
	        }
	        clone.milliseconds(0);
	      } else if (scale == 'second') {
	        
	        switch (step) {
	          case 15:
	          case 10:
	            clone.seconds(Math.round(clone.seconds() / 5) * 5);
	            clone.milliseconds(0);
	            break;
	          case 5:
	            clone.milliseconds(Math.round(clone.milliseconds() / 1000) * 1000);
	            break;
	          default:
	            clone.milliseconds(Math.round(clone.milliseconds() / 500) * 500);
	            break;
	        }
	      } else if (scale == 'millisecond') {
	        var _step = step > 5 ? step / 2 : 1;
	        clone.milliseconds(Math.round(clone.milliseconds() / _step) * _step);
	      }
	      return clone;
	    }
	  }]);
	  return TimeStep;
	}(); 
	TimeStep.FORMAT = {
	  minorLabels: {
	    millisecond: 'SSS',
	    second: 's',
	    minute: 'HH:mm',
	    hour: 'HH:mm',
	    weekday: 'ddd D',
	    day: 'D',
	    week: 'w',
	    month: 'MMM',
	    year: 'YYYY'
	  },
	  majorLabels: {
	    millisecond: 'HH:mm:ss',
	    second: 'D MMMM HH:mm',
	    minute: 'ddd D MMMM',
	    hour: 'ddd D MMMM',
	    weekday: 'MMMM YYYY',
	    day: 'MMMM YYYY',
	    week: 'MMMM YYYY',
	    month: 'YYYY',
	    year: ''
	  }
	};

	function _createSuper$b(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$b(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$b() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	
	var TimeAxis = function (_Component) {
	  _inherits(TimeAxis, _Component);
	  var _super = _createSuper$b(TimeAxis);
	  
	  function TimeAxis(body, options) {
	    var _this;
	    _classCallCheck(this, TimeAxis);
	    _this = _super.call(this);
	    _this.dom = {
	      foreground: null,
	      lines: [],
	      majorTexts: [],
	      minorTexts: [],
	      redundant: {
	        lines: [],
	        majorTexts: [],
	        minorTexts: []
	      }
	    };
	    _this.props = {
	      range: {
	        start: 0,
	        end: 0,
	        minimumStep: 0
	      },
	      lineTop: 0
	    };
	    _this.defaultOptions = {
	      orientation: {
	        axis: 'bottom'
	      },
	      
	      showMinorLabels: true,
	      showMajorLabels: true,
	      showWeekScale: false,
	      maxMinorChars: 7,
	      format: availableUtils.extend({}, TimeStep.FORMAT),
	      moment: moment$2,
	      timeAxis: null
	    };
	    _this.options = availableUtils.extend({}, _this.defaultOptions);
	    _this.body = body;

	    
	    _this._create();
	    _this.setOptions(options);
	    return _this;
	  }

	  
	  _createClass(TimeAxis, [{
	    key: "setOptions",
	    value: function setOptions(options) {
	      if (options) {
	        
	        availableUtils.selectiveExtend(['showMinorLabels', 'showMajorLabels', 'showWeekScale', 'maxMinorChars', 'hiddenDates', 'timeAxis', 'moment', 'rtl'], this.options, options);

	        
	        availableUtils.selectiveDeepExtend(['format'], this.options, options);
	        if ('orientation' in options) {
	          if (typeof options.orientation === 'string') {
	            this.options.orientation.axis = options.orientation;
	          } else if (_typeof(options.orientation) === 'object' && 'axis' in options.orientation) {
	            this.options.orientation.axis = options.orientation.axis;
	          }
	        }

	        
	        
	        if ('locale' in options) {
	          if (typeof moment$2.locale === 'function') {
	            
	            moment$2.locale(options.locale);
	          } else {
	            moment$2.lang(options.locale);
	          }
	        }
	      }
	    }

	    
	  }, {
	    key: "_create",
	    value: function _create() {
	      this.dom.foreground = document.createElement('div');
	      this.dom.background = document.createElement('div');
	      this.dom.foreground.className = 'vis-time-axis vis-foreground';
	      this.dom.background.className = 'vis-time-axis vis-background';
	    }

	    
	  }, {
	    key: "destroy",
	    value: function destroy() {
	      
	      if (this.dom.foreground.parentNode) {
	        this.dom.foreground.parentNode.removeChild(this.dom.foreground);
	      }
	      if (this.dom.background.parentNode) {
	        this.dom.background.parentNode.removeChild(this.dom.background);
	      }
	      this.body = null;
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw() {
	      var props = this.props;
	      var foreground = this.dom.foreground;
	      var background = this.dom.background;

	      
	      var parent = this.options.orientation.axis == 'top' ? this.body.dom.top : this.body.dom.bottom;
	      var parentChanged = foreground.parentNode !== parent;

	      
	      this._calculateCharSize();

	      
	      var showMinorLabels = this.options.showMinorLabels && this.options.orientation.axis !== 'none';
	      var showMajorLabels = this.options.showMajorLabels && this.options.orientation.axis !== 'none';

	      
	      props.minorLabelHeight = showMinorLabels ? props.minorCharHeight : 0;
	      props.majorLabelHeight = showMajorLabels ? props.majorCharHeight : 0;
	      props.height = props.minorLabelHeight + props.majorLabelHeight;
	      props.width = foreground.offsetWidth;
	      props.minorLineHeight = this.body.domProps.root.height - props.majorLabelHeight - (this.options.orientation.axis == 'top' ? this.body.domProps.bottom.height : this.body.domProps.top.height);
	      props.minorLineWidth = 1; 
	      props.majorLineHeight = props.minorLineHeight + props.majorLabelHeight;
	      props.majorLineWidth = 1; 

	      
	      var foregroundNextSibling = foreground.nextSibling;
	      var backgroundNextSibling = background.nextSibling;
	      foreground.parentNode && foreground.parentNode.removeChild(foreground);
	      background.parentNode && background.parentNode.removeChild(background);
	      foreground.style.height = "".concat(this.props.height, "px");
	      this._repaintLabels();

	      
	      if (foregroundNextSibling) {
	        parent.insertBefore(foreground, foregroundNextSibling);
	      } else {
	        parent.appendChild(foreground);
	      }
	      if (backgroundNextSibling) {
	        this.body.dom.backgroundVertical.insertBefore(background, backgroundNextSibling);
	      } else {
	        this.body.dom.backgroundVertical.appendChild(background);
	      }
	      return this._isResized() || parentChanged;
	    }

	    
	  }, {
	    key: "_repaintLabels",
	    value: function _repaintLabels() {
	      var orientation = this.options.orientation.axis;

	      
	      var start = availableUtils.convert(this.body.range.start, 'Number');
	      var end = availableUtils.convert(this.body.range.end, 'Number');
	      var timeLabelsize = this.body.util.toTime((this.props.minorCharWidth || 10) * this.options.maxMinorChars).valueOf();
	      var minimumStep = timeLabelsize - getHiddenDurationBefore(this.options.moment, this.body.hiddenDates, this.body.range, timeLabelsize);
	      minimumStep -= this.body.util.toTime(0).valueOf();
	      var step = new TimeStep(new Date(start), new Date(end), minimumStep, this.body.hiddenDates, this.options);
	      step.setMoment(this.options.moment);
	      if (this.options.format) {
	        step.setFormat(this.options.format);
	      }
	      if (this.options.timeAxis) {
	        step.setScale(this.options.timeAxis);
	      }
	      this.step = step;

	      
	      
	      
	      var dom = this.dom;
	      dom.redundant.lines = dom.lines;
	      dom.redundant.majorTexts = dom.majorTexts;
	      dom.redundant.minorTexts = dom.minorTexts;
	      dom.lines = [];
	      dom.majorTexts = [];
	      dom.minorTexts = [];
	      var current;
	      var next;
	      var x;
	      var xNext;
	      var isMajor;
	      var showMinorGrid;
	      var width = 0;
	      var prevWidth;
	      var line;
	      var xFirstMajorLabel = undefined;
	      var count = 0;
	      var MAX = 1000;
	      var className;
	      step.start();
	      next = step.getCurrent();
	      xNext = this.body.util.toScreen(next);
	      while (step.hasNext() && count < MAX) {
	        count++;
	        isMajor = step.isMajor();
	        className = step.getClassName();
	        current = next;
	        x = xNext;
	        step.next();
	        next = step.getCurrent();
	        xNext = this.body.util.toScreen(next);
	        prevWidth = width;
	        width = xNext - x;
	        switch (step.scale) {
	          case 'week':
	            showMinorGrid = true;
	            break;
	          default:
	            showMinorGrid = width >= prevWidth * 0.4;
	            break;
	          
	        }

	        if (this.options.showMinorLabels && showMinorGrid) {
	          var label = this._repaintMinorText(x, step.getLabelMinor(current), orientation, className);
	          label.style.width = "".concat(width, "px"); 
	        }

	        if (isMajor && this.options.showMajorLabels) {
	          if (x > 0) {
	            if (xFirstMajorLabel == undefined) {
	              xFirstMajorLabel = x;
	            }
	            label = this._repaintMajorText(x, step.getLabelMajor(current), orientation, className);
	          }
	          line = this._repaintMajorLine(x, width, orientation, className);
	        } else {
	          
	          if (showMinorGrid) {
	            line = this._repaintMinorLine(x, width, orientation, className);
	          } else {
	            if (line) {
	              
	              line.style.width = "".concat(_parseInt(line.style.width) + width, "px");
	            }
	          }
	        }
	      }
	      if (count === MAX && !warnedForOverflow) {
	        console.warn("Something is wrong with the Timeline scale. Limited drawing of grid lines to ".concat(MAX, " lines."));
	        warnedForOverflow = true;
	      }

	      
	      if (this.options.showMajorLabels) {
	        var leftTime = this.body.util.toTime(0); 
	        var leftText = step.getLabelMajor(leftTime);
	        var widthText = leftText.length * (this.props.majorCharWidth || 10) + 10;
	        if (xFirstMajorLabel == undefined || widthText < xFirstMajorLabel) {
	          this._repaintMajorText(0, leftText, orientation, className);
	        }
	      }

	      
	      _forEachInstanceProperty(availableUtils).call(availableUtils, this.dom.redundant, function (arr) {
	        while (arr.length) {
	          var elem = arr.pop();
	          if (elem && elem.parentNode) {
	            elem.parentNode.removeChild(elem);
	          }
	        }
	      });
	    }

	    
	  }, {
	    key: "_repaintMinorText",
	    value: function _repaintMinorText(x, text, orientation, className) {
	      
	      var label = this.dom.redundant.minorTexts.shift();
	      if (!label) {
	        
	        var content = document.createTextNode('');
	        label = document.createElement('div');
	        label.appendChild(content);
	        this.dom.foreground.appendChild(label);
	      }
	      this.dom.minorTexts.push(label);
	      label.innerHTML = availableUtils.xss(text);
	      var y = orientation == 'top' ? this.props.majorLabelHeight : 0;
	      this._setXY(label, x, y);
	      label.className = "vis-text vis-minor ".concat(className);
	      

	      return label;
	    }

	    
	  }, {
	    key: "_repaintMajorText",
	    value: function _repaintMajorText(x, text, orientation, className) {
	      
	      var label = this.dom.redundant.majorTexts.shift();
	      if (!label) {
	        
	        var content = document.createElement('div');
	        label = document.createElement('div');
	        label.appendChild(content);
	        this.dom.foreground.appendChild(label);
	      }
	      label.childNodes[0].innerHTML = availableUtils.xss(text);
	      label.className = "vis-text vis-major ".concat(className);
	      

	      var y = orientation == 'top' ? 0 : this.props.minorLabelHeight;
	      this._setXY(label, x, y);
	      this.dom.majorTexts.push(label);
	      return label;
	    }

	    
	  }, {
	    key: "_setXY",
	    value: function _setXY(label, x, y) {
	      var _context;
	      
	      var directionX = this.options.rtl ? x * -1 : x;
	      label.style.transform = _concatInstanceProperty(_context = "translate(".concat(directionX, "px, ")).call(_context, y, "px)");
	    }

	    
	  }, {
	    key: "_repaintMinorLine",
	    value: function _repaintMinorLine(left, width, orientation, className) {
	      var _context2;
	      
	      var line = this.dom.redundant.lines.shift();
	      if (!line) {
	        
	        line = document.createElement('div');
	        this.dom.background.appendChild(line);
	      }
	      this.dom.lines.push(line);
	      var props = this.props;
	      line.style.width = "".concat(width, "px");
	      line.style.height = "".concat(props.minorLineHeight, "px");
	      var y = orientation == 'top' ? props.majorLabelHeight : this.body.domProps.top.height;
	      var x = left - props.minorLineWidth / 2;
	      this._setXY(line, x, y);
	      line.className = _concatInstanceProperty(_context2 = "vis-grid ".concat(this.options.rtl ? 'vis-vertical-rtl' : 'vis-vertical', " vis-minor ")).call(_context2, className);
	      return line;
	    }

	    
	  }, {
	    key: "_repaintMajorLine",
	    value: function _repaintMajorLine(left, width, orientation, className) {
	      var _context3;
	      
	      var line = this.dom.redundant.lines.shift();
	      if (!line) {
	        
	        line = document.createElement('div');
	        this.dom.background.appendChild(line);
	      }
	      this.dom.lines.push(line);
	      var props = this.props;
	      line.style.width = "".concat(width, "px");
	      line.style.height = "".concat(props.majorLineHeight, "px");
	      var y = orientation == 'top' ? 0 : this.body.domProps.top.height;
	      var x = left - props.majorLineWidth / 2;
	      this._setXY(line, x, y);
	      line.className = _concatInstanceProperty(_context3 = "vis-grid ".concat(this.options.rtl ? 'vis-vertical-rtl' : 'vis-vertical', " vis-major ")).call(_context3, className);
	      return line;
	    }

	    
	  }, {
	    key: "_calculateCharSize",
	    value: function _calculateCharSize() {
	      
	      

	      
	      if (!this.dom.measureCharMinor) {
	        this.dom.measureCharMinor = document.createElement('DIV');
	        this.dom.measureCharMinor.className = 'vis-text vis-minor vis-measure';
	        this.dom.measureCharMinor.style.position = 'absolute';
	        this.dom.measureCharMinor.appendChild(document.createTextNode('0'));
	        this.dom.foreground.appendChild(this.dom.measureCharMinor);
	      }
	      this.props.minorCharHeight = this.dom.measureCharMinor.clientHeight;
	      this.props.minorCharWidth = this.dom.measureCharMinor.clientWidth;

	      
	      if (!this.dom.measureCharMajor) {
	        this.dom.measureCharMajor = document.createElement('DIV');
	        this.dom.measureCharMajor.className = 'vis-text vis-major vis-measure';
	        this.dom.measureCharMajor.style.position = 'absolute';
	        this.dom.measureCharMajor.appendChild(document.createTextNode('0'));
	        this.dom.foreground.appendChild(this.dom.measureCharMajor);
	      }
	      this.props.majorCharHeight = this.dom.measureCharMajor.clientHeight;
	      this.props.majorCharWidth = this.dom.measureCharMajor.clientWidth;
	    }
	  }]);
	  return TimeAxis;
	}(Component);
	var warnedForOverflow = false;

	
	function keycharm(options) {
	  var preventDefault = options && options.preventDefault || false;

	  var container = options && options.container || window;

	  var _exportFunctions = {};
	  var _bound = {keydown:{}, keyup:{}};
	  var _keys = {};
	  var i;

	  
	  for (i = 97; i <= 122; i++) {_keys[String.fromCharCode(i)] = {code:65 + (i - 97), shift: false};}
	  
	  for (i = 65; i <= 90; i++) {_keys[String.fromCharCode(i)] = {code:i, shift: true};}
	  
	  for (i = 0;  i <= 9;   i++) {_keys['' + i] = {code:48 + i, shift: false};}
	  
	  for (i = 1;  i <= 12;   i++) {_keys['F' + i] = {code:111 + i, shift: false};}
	  
	  for (i = 0;  i <= 9;   i++) {_keys['num' + i] = {code:96 + i, shift: false};}

	  
	  _keys['num*'] = {code:106, shift: false};
	  _keys['num+'] = {code:107, shift: false};
	  _keys['num-'] = {code:109, shift: false};
	  _keys['num/'] = {code:111, shift: false};
	  _keys['num.'] = {code:110, shift: false};
	  
	  _keys['left']  = {code:37, shift: false};
	  _keys['up']    = {code:38, shift: false};
	  _keys['right'] = {code:39, shift: false};
	  _keys['down']  = {code:40, shift: false};
	  
	  _keys['space'] = {code:32, shift: false};
	  _keys['enter'] = {code:13, shift: false};
	  _keys['shift'] = {code:16, shift: undefined};
	  _keys['esc']   = {code:27, shift: false};
	  _keys['backspace'] = {code:8, shift: false};
	  _keys['tab']       = {code:9, shift: false};
	  _keys['ctrl']      = {code:17, shift: false};
	  _keys['alt']       = {code:18, shift: false};
	  _keys['delete']    = {code:46, shift: false};
	  _keys['pageup']    = {code:33, shift: false};
	  _keys['pagedown']  = {code:34, shift: false};
	  
	  _keys['=']     = {code:187, shift: false};
	  _keys['-']     = {code:189, shift: false};
	  _keys[']']     = {code:221, shift: false};
	  _keys['[']     = {code:219, shift: false};



	  var down = function(event) {handleEvent(event,'keydown');};
	  var up = function(event) {handleEvent(event,'keyup');};

	  
	  var handleEvent = function(event,type) {
	    if (_bound[type][event.keyCode] !== undefined) {
	      var bound = _bound[type][event.keyCode];
	      for (var i = 0; i < bound.length; i++) {
	        if (bound[i].shift === undefined) {
	          bound[i].fn(event);
	        }
	        else if (bound[i].shift == true && event.shiftKey == true) {
	          bound[i].fn(event);
	        }
	        else if (bound[i].shift == false && event.shiftKey == false) {
	          bound[i].fn(event);
	        }
	      }

	      if (preventDefault == true) {
	        event.preventDefault();
	      }
	    }
	  };

	  
	  _exportFunctions.bind = function(key, callback, type) {
	    if (type === undefined) {
	      type = 'keydown';
	    }
	    if (_keys[key] === undefined) {
	      throw new Error("unsupported key: " + key);
	    }
	    if (_bound[type][_keys[key].code] === undefined) {
	      _bound[type][_keys[key].code] = [];
	    }
	    _bound[type][_keys[key].code].push({fn:callback, shift:_keys[key].shift});
	  };


	  
	  _exportFunctions.bindAll = function(callback, type) {
	    if (type === undefined) {
	      type = 'keydown';
	    }
	    for (var key in _keys) {
	      if (_keys.hasOwnProperty(key)) {
	        _exportFunctions.bind(key,callback,type);
	      }
	    }
	  };

	  
	  _exportFunctions.getKey = function(event) {
	    for (var key in _keys) {
	      if (_keys.hasOwnProperty(key)) {
	        if (event.shiftKey == true && _keys[key].shift == true && event.keyCode == _keys[key].code) {
	          return key;
	        }
	        else if (event.shiftKey == false && _keys[key].shift == false && event.keyCode == _keys[key].code) {
	          return key;
	        }
	        else if (event.keyCode == _keys[key].code && key == 'shift') {
	          return key;
	        }
	      }
	    }
	    return "unknown key, currently not supported";
	  };

	  
	  _exportFunctions.unbind = function(key, callback, type) {
	    if (type === undefined) {
	      type = 'keydown';
	    }
	    if (_keys[key] === undefined) {
	      throw new Error("unsupported key: " + key);
	    }
	    if (callback !== undefined) {
	      var newBindings = [];
	      var bound = _bound[type][_keys[key].code];
	      if (bound !== undefined) {
	        for (var i = 0; i < bound.length; i++) {
	          if (!(bound[i].fn == callback && bound[i].shift == _keys[key].shift)) {
	            newBindings.push(_bound[type][_keys[key].code][i]);
	          }
	        }
	      }
	      _bound[type][_keys[key].code] = newBindings;
	    }
	    else {
	      _bound[type][_keys[key].code] = [];
	    }
	  };

	  
	  _exportFunctions.reset = function() {
	    _bound = {keydown:{}, keyup:{}};
	  };

	  
	  _exportFunctions.destroy = function() {
	    _bound = {keydown:{}, keyup:{}};
	    container.removeEventListener('keydown', down, true);
	    container.removeEventListener('keyup', up, true);
	  };

	  
	  container.addEventListener('keydown',down,true);
	  container.addEventListener('keyup',up,true);

	  
	  return _exportFunctions;
	}

	
	function Activator(container) {
	  var _context, _context2;
	  this.active = false;
	  this.dom = {
	    container: container
	  };
	  this.dom.overlay = document.createElement('div');
	  this.dom.overlay.className = 'vis-overlay';
	  this.dom.container.appendChild(this.dom.overlay);
	  this.hammer = Hammer(this.dom.overlay);
	  this.hammer.on('tap', _bindInstanceProperty(_context = this._onTapOverlay).call(_context, this));

	  
	  var me = this;
	  var events = ['tap', 'doubletap', 'press', 'pinch', 'pan', 'panstart', 'panmove', 'panend'];
	  _forEachInstanceProperty(events).call(events, function (event) {
	    me.hammer.on(event, function (event) {
	      event.stopPropagation();
	    });
	  });

	  
	  if (document && document.body) {
	    this.onClick = function (event) {
	      if (!_hasParent(event.target, container)) {
	        me.deactivate();
	      }
	    };
	    document.body.addEventListener('click', this.onClick);
	  }
	  if (this.keycharm !== undefined) {
	    this.keycharm.destroy();
	  }
	  this.keycharm = keycharm();

	  
	  this.escListener = _bindInstanceProperty(_context2 = this.deactivate).call(_context2, this);
	}

	
	Emitter(Activator.prototype);

	
	Activator.current = null;

	
	Activator.prototype.destroy = function () {
	  this.deactivate();

	  
	  this.dom.overlay.parentNode.removeChild(this.dom.overlay);

	  
	  if (this.onClick) {
	    document.body.removeEventListener('click', this.onClick);
	  }
	  
	  if (this.keycharm !== undefined) {
	    this.keycharm.destroy();
	  }
	  this.keycharm = null;
	  
	  this.hammer.destroy();
	  this.hammer = null;
	  
	};

	
	Activator.prototype.activate = function () {
	  var _context3;
	  
	  if (Activator.current) {
	    Activator.current.deactivate();
	  }
	  Activator.current = this;
	  this.active = true;
	  this.dom.overlay.style.display = 'none';
	  availableUtils.addClassName(this.dom.container, 'vis-active');
	  this.emit('change');
	  this.emit('activate');

	  
	  
	  _bindInstanceProperty(_context3 = this.keycharm).call(_context3, 'esc', this.escListener);
	};

	
	Activator.prototype.deactivate = function () {
	  if (Activator.current === this) {
	    Activator.current = null;
	  }
	  this.active = false;
	  this.dom.overlay.style.display = '';
	  availableUtils.removeClassName(this.dom.container, 'vis-active');
	  this.keycharm.unbind('esc', this.escListener);
	  this.emit('change');
	  this.emit('deactivate');
	};

	
	Activator.prototype._onTapOverlay = function (event) {
	  
	  this.activate();
	  event.stopPropagation();
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

	

	
	var en = {
	  current: 'current',
	  time: 'time',
	  deleteSelected: 'Delete selected'
	};
	var en_EN = en;
	var en_US = en;

	
	var it = {
	  current: 'attuale',
	  time: 'tempo',
	  deleteSelected: 'Cancella la selezione'
	};
	var it_IT = it;
	var it_CH = it;

	
	var nl = {
	  current: 'huidige',
	  time: 'tijd',
	  deleteSelected: 'Selectie verwijderen'
	};
	var nl_NL = nl;
	var nl_BE = nl;

	
	var de = {
	  current: 'Aktuelle',
	  time: 'Zeit',
	  deleteSelected: "L\xF6sche Auswahl"
	};
	var de_DE = de;

	
	var fr = {
	  current: 'actuel',
	  time: 'heure',
	  deleteSelected: 'Effacer la selection'
	};
	var fr_FR = fr;
	var fr_CA = fr;
	var fr_BE = fr;

	
	var es = {
	  current: 'corriente',
	  time: 'hora',
	  deleteSelected: "Eliminar selecci\xF3n"
	};
	var es_ES = es;

	
	var uk = {
	  current: 'поточний',
	  time: 'час',
	  deleteSelected: 'Видалити обране'
	};
	var uk_UA = uk;

	
	var ru = {
	  current: 'текущее',
	  time: 'время',
	  deleteSelected: 'Удалить выбранное'
	};
	var ru_RU = ru;

	
	var pl = {
	  current: 'aktualny',
	  time: 'czas',
	  deleteSelected: 'Usuń wybrane'
	};
	var pl_PL = pl;

	
	var pt = {
	  current: 'atual',
	  time: 'data',
	  deleteSelected: 'Apagar selecionado'
	};
	var pt_BR = pt;
	var pt_PT = pt;

	
	var ja = {
	  current: '現在',
	  time: '時刻',
	  deleteSelected: '選択されたものを削除'
	};
	var ja_JP = ja;

	
	var sv = {
	  current: 'nuvarande',
	  time: 'tid',
	  deleteSelected: 'Radera valda'
	};
	var sv_SE = sv;

	
	var nb = {
	  current: 'nåværende',
	  time: 'tid',
	  deleteSelected: 'Slett valgte'
	};
	var nb_NO = nb;
	var nn = nb;
	var nn_NO = nb;

	
	var lt = {
	  current: 'einamas',
	  time: 'laikas',
	  deleteSelected: 'Pašalinti pasirinktą'
	};
	var lt_LT = lt;
	var locales = {
	  en: en,
	  en_EN: en_EN,
	  en_US: en_US,
	  it: it,
	  it_IT: it_IT,
	  it_CH: it_CH,
	  nl: nl,
	  nl_NL: nl_NL,
	  nl_BE: nl_BE,
	  de: de,
	  de_DE: de_DE,
	  fr: fr,
	  fr_FR: fr_FR,
	  fr_CA: fr_CA,
	  fr_BE: fr_BE,
	  es: es,
	  es_ES: es_ES,
	  uk: uk,
	  uk_UA: uk_UA,
	  ru: ru,
	  ru_RU: ru_RU,
	  pl: pl,
	  pl_PL: pl_PL,
	  pt: pt,
	  pt_BR: pt_BR,
	  pt_PT: pt_PT,
	  ja: ja,
	  ja_JP: ja_JP,
	  lt: lt,
	  lt_LT: lt_LT,
	  sv: sv,
	  sv_SE: sv_SE,
	  nb: nb,
	  nn: nn,
	  nb_NO: nb_NO,
	  nn_NO: nn_NO
	};

	function _createSuper$a(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$a(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$a() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	
	var CustomTime = function (_Component) {
	  _inherits(CustomTime, _Component);
	  var _super = _createSuper$a(CustomTime);
	  
	  function CustomTime(body, options) {
	    var _context;
	    var _this;
	    _classCallCheck(this, CustomTime);
	    _this = _super.call(this);
	    _this.body = body;

	    
	    _this.defaultOptions = {
	      moment: moment$2,
	      locales: locales,
	      locale: 'en',
	      id: undefined,
	      title: undefined
	    };
	    _this.options = availableUtils.extend({}, _this.defaultOptions);
	    _this.setOptions(options);
	    _this.options.locales = availableUtils.extend({}, locales, _this.options.locales);
	    var defaultLocales = _this.defaultOptions.locales[_this.defaultOptions.locale];
	    _forEachInstanceProperty(_context = _Object$keys(_this.options.locales)).call(_context, function (locale) {
	      _this.options.locales[locale] = availableUtils.extend({}, defaultLocales, _this.options.locales[locale]);
	    });
	    if (options && options.time != null) {
	      _this.customTime = options.time;
	    } else {
	      _this.customTime = new Date();
	    }
	    _this.eventParams = {}; 

	    
	    _this._create();
	    return _this;
	  }

	  
	  _createClass(CustomTime, [{
	    key: "setOptions",
	    value: function setOptions(options) {
	      if (options) {
	        
	        availableUtils.selectiveExtend(['moment', 'locale', 'locales', 'id', 'title', 'rtl', 'snap'], this.options, options);
	      }
	    }

	    
	  }, {
	    key: "_create",
	    value: function _create() {
	      var _context2, _context3, _context4;
	      var bar = document.createElement('div');
	      bar['custom-time'] = this;
	      bar.className = "vis-custom-time ".concat(this.options.id || '');
	      bar.style.position = 'absolute';
	      bar.style.top = '0px';
	      bar.style.height = '100%';
	      this.bar = bar;
	      var drag = document.createElement('div');
	      drag.style.position = 'relative';
	      drag.style.top = '0px';
	      if (this.options.rtl) {
	        drag.style.right = '-10px';
	      } else {
	        drag.style.left = '-10px';
	      }
	      drag.style.height = '100%';
	      drag.style.width = '20px';

	      
	      function onMouseWheel(e) {
	        this.body.range._onMouseWheel(e);
	      }
	      if (drag.addEventListener) {
	        
	        drag.addEventListener("mousewheel", _bindInstanceProperty(onMouseWheel).call(onMouseWheel, this), false);
	        
	        drag.addEventListener("DOMMouseScroll", _bindInstanceProperty(onMouseWheel).call(onMouseWheel, this), false);
	      } else {
	        
	        drag.attachEvent("onmousewheel", _bindInstanceProperty(onMouseWheel).call(onMouseWheel, this));
	      }
	      bar.appendChild(drag);
	      
	      this.hammer = new Hammer(drag);
	      this.hammer.on('panstart', _bindInstanceProperty(_context2 = this._onDragStart).call(_context2, this));
	      this.hammer.on('panmove', _bindInstanceProperty(_context3 = this._onDrag).call(_context3, this));
	      this.hammer.on('panend', _bindInstanceProperty(_context4 = this._onDragEnd).call(_context4, this));
	      this.hammer.get('pan').set({
	        threshold: 5,
	        direction: Hammer.DIRECTION_ALL
	      });
	      
	      this.hammer.get('press').set({
	        time: 10000
	      });
	    }

	    
	  }, {
	    key: "destroy",
	    value: function destroy() {
	      this.hide();
	      this.hammer.destroy();
	      this.hammer = null;
	      this.body = null;
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw() {
	      var parent = this.body.dom.backgroundVertical;
	      if (this.bar.parentNode != parent) {
	        
	        if (this.bar.parentNode) {
	          this.bar.parentNode.removeChild(this.bar);
	        }
	        parent.appendChild(this.bar);
	      }
	      var x = this.body.util.toScreen(this.customTime);
	      var locale = this.options.locales[this.options.locale];
	      if (!locale) {
	        if (!this.warned) {
	          console.warn("WARNING: options.locales['".concat(this.options.locale, "'] not found. See https:
	          this.warned = true;
	        }
	        locale = this.options.locales['en']; 
	      }

	      var title = this.options.title;
	      
	      if (title === undefined) {
	        var _context5;
	        title = _concatInstanceProperty(_context5 = "".concat(locale.time, ": ")).call(_context5, this.options.moment(this.customTime).format('dddd, MMMM Do YYYY, H:mm:ss'));
	        title = title.charAt(0).toUpperCase() + title.substring(1);
	      } else if (typeof title === "function") {
	        title = title.call(this, this.customTime);
	      }
	      this.options.rtl ? this.bar.style.right = "".concat(x, "px") : this.bar.style.left = "".concat(x, "px");
	      this.bar.title = title;
	      return false;
	    }

	    
	  }, {
	    key: "hide",
	    value: function hide() {
	      
	      if (this.bar.parentNode) {
	        this.bar.parentNode.removeChild(this.bar);
	      }
	    }

	    
	  }, {
	    key: "setCustomTime",
	    value: function setCustomTime(time) {
	      this.customTime = availableUtils.convert(time, 'Date');
	      this.redraw();
	    }

	    
	  }, {
	    key: "getCustomTime",
	    value: function getCustomTime() {
	      return new Date(this.customTime.valueOf());
	    }

	    
	  }, {
	    key: "setCustomMarker",
	    value: function setCustomMarker(title, editable) {
	      var marker = document.createElement('div');
	      marker.className = "vis-custom-time-marker";
	      marker.innerHTML = availableUtils.xss(title);
	      marker.style.position = 'absolute';
	      if (editable) {
	        var _context6, _context7;
	        marker.setAttribute('contenteditable', 'true');
	        marker.addEventListener('pointerdown', function () {
	          marker.focus();
	        });
	        marker.addEventListener('input', _bindInstanceProperty(_context6 = this._onMarkerChange).call(_context6, this));
	        
	        marker.title = title;
	        marker.addEventListener('blur', _bindInstanceProperty(_context7 = function _context7(event) {
	          if (this.title != event.target.innerHTML) {
	            this._onMarkerChanged(event);
	            this.title = event.target.innerHTML;
	          }
	        }).call(_context7, this));
	      }
	      this.bar.appendChild(marker);
	    }

	    
	  }, {
	    key: "setCustomTitle",
	    value: function setCustomTitle(title) {
	      this.options.title = title;
	    }

	    
	  }, {
	    key: "_onDragStart",
	    value: function _onDragStart(event) {
	      this.eventParams.dragging = true;
	      this.eventParams.customTime = this.customTime;
	      event.stopPropagation();
	    }

	    
	  }, {
	    key: "_onDrag",
	    value: function _onDrag(event) {
	      if (!this.eventParams.dragging) return;
	      var deltaX = this.options.rtl ? -1 * event.deltaX : event.deltaX;
	      var x = this.body.util.toScreen(this.eventParams.customTime) + deltaX;
	      var time = this.body.util.toTime(x);
	      var scale = this.body.util.getScale();
	      var step = this.body.util.getStep();
	      var snap = this.options.snap;
	      var snappedTime = snap ? snap(time, scale, step) : time;
	      this.setCustomTime(snappedTime);

	      
	      this.body.emitter.emit('timechange', {
	        id: this.options.id,
	        time: new Date(this.customTime.valueOf()),
	        event: event
	      });
	      event.stopPropagation();
	    }

	    
	  }, {
	    key: "_onDragEnd",
	    value: function _onDragEnd(event) {
	      if (!this.eventParams.dragging) return;

	      
	      this.body.emitter.emit('timechanged', {
	        id: this.options.id,
	        time: new Date(this.customTime.valueOf()),
	        event: event
	      });
	      event.stopPropagation();
	    }

	    
	  }, {
	    key: "_onMarkerChange",
	    value: function _onMarkerChange(event) {
	      this.body.emitter.emit('markerchange', {
	        id: this.options.id,
	        title: event.target.innerHTML,
	        event: event
	      });
	      event.stopPropagation();
	    }

	    
	  }, {
	    key: "_onMarkerChanged",
	    value: function _onMarkerChanged(event) {
	      this.body.emitter.emit('markerchanged', {
	        id: this.options.id,
	        title: event.target.innerHTML,
	        event: event
	      });
	      event.stopPropagation();
	    }

	    
	  }], [{
	    key: "customTimeFromTarget",
	    value: function customTimeFromTarget(event) {
	      var target = event.target;
	      while (target) {
	        if (target.hasOwnProperty('custom-time')) {
	          return target['custom-time'];
	        }
	        target = target.parentNode;
	      }
	      return null;
	    }
	  }]);
	  return CustomTime;
	}(Component);

	
	var Core = function () {
	  function Core() {
	    _classCallCheck(this, Core);
	  }
	  _createClass(Core, [{
	    key: "_create",
	    value:
	    
	    function _create(container) {
	      var _this = this,
	        _context,
	        _context2,
	        _context3;
	      this.dom = {};
	      this.dom.container = container;
	      this.dom.container.style.position = 'relative';
	      this.dom.root = document.createElement('div');
	      this.dom.background = document.createElement('div');
	      this.dom.backgroundVertical = document.createElement('div');
	      this.dom.backgroundHorizontal = document.createElement('div');
	      this.dom.centerContainer = document.createElement('div');
	      this.dom.leftContainer = document.createElement('div');
	      this.dom.rightContainer = document.createElement('div');
	      this.dom.center = document.createElement('div');
	      this.dom.left = document.createElement('div');
	      this.dom.right = document.createElement('div');
	      this.dom.top = document.createElement('div');
	      this.dom.bottom = document.createElement('div');
	      this.dom.shadowTop = document.createElement('div');
	      this.dom.shadowBottom = document.createElement('div');
	      this.dom.shadowTopLeft = document.createElement('div');
	      this.dom.shadowBottomLeft = document.createElement('div');
	      this.dom.shadowTopRight = document.createElement('div');
	      this.dom.shadowBottomRight = document.createElement('div');
	      this.dom.rollingModeBtn = document.createElement('div');
	      this.dom.loadingScreen = document.createElement('div');
	      this.dom.root.className = 'vis-timeline';
	      this.dom.background.className = 'vis-panel vis-background';
	      this.dom.backgroundVertical.className = 'vis-panel vis-background vis-vertical';
	      this.dom.backgroundHorizontal.className = 'vis-panel vis-background vis-horizontal';
	      this.dom.centerContainer.className = 'vis-panel vis-center';
	      this.dom.leftContainer.className = 'vis-panel vis-left';
	      this.dom.rightContainer.className = 'vis-panel vis-right';
	      this.dom.top.className = 'vis-panel vis-top';
	      this.dom.bottom.className = 'vis-panel vis-bottom';
	      this.dom.left.className = 'vis-content';
	      this.dom.center.className = 'vis-content';
	      this.dom.right.className = 'vis-content';
	      this.dom.shadowTop.className = 'vis-shadow vis-top';
	      this.dom.shadowBottom.className = 'vis-shadow vis-bottom';
	      this.dom.shadowTopLeft.className = 'vis-shadow vis-top';
	      this.dom.shadowBottomLeft.className = 'vis-shadow vis-bottom';
	      this.dom.shadowTopRight.className = 'vis-shadow vis-top';
	      this.dom.shadowBottomRight.className = 'vis-shadow vis-bottom';
	      this.dom.rollingModeBtn.className = 'vis-rolling-mode-btn';
	      this.dom.loadingScreen.className = 'vis-loading-screen';
	      this.dom.root.appendChild(this.dom.background);
	      this.dom.root.appendChild(this.dom.backgroundVertical);
	      this.dom.root.appendChild(this.dom.backgroundHorizontal);
	      this.dom.root.appendChild(this.dom.centerContainer);
	      this.dom.root.appendChild(this.dom.leftContainer);
	      this.dom.root.appendChild(this.dom.rightContainer);
	      this.dom.root.appendChild(this.dom.top);
	      this.dom.root.appendChild(this.dom.bottom);
	      this.dom.root.appendChild(this.dom.rollingModeBtn);
	      this.dom.centerContainer.appendChild(this.dom.center);
	      this.dom.leftContainer.appendChild(this.dom.left);
	      this.dom.rightContainer.appendChild(this.dom.right);
	      this.dom.centerContainer.appendChild(this.dom.shadowTop);
	      this.dom.centerContainer.appendChild(this.dom.shadowBottom);
	      this.dom.leftContainer.appendChild(this.dom.shadowTopLeft);
	      this.dom.leftContainer.appendChild(this.dom.shadowBottomLeft);
	      this.dom.rightContainer.appendChild(this.dom.shadowTopRight);
	      this.dom.rightContainer.appendChild(this.dom.shadowBottomRight);

	      
	      this.props = {
	        root: {},
	        background: {},
	        centerContainer: {},
	        leftContainer: {},
	        rightContainer: {},
	        center: {},
	        left: {},
	        right: {},
	        top: {},
	        bottom: {},
	        border: {},
	        scrollTop: 0,
	        scrollTopMin: 0
	      };
	      this.on('rangechange', function () {
	        if (_this.initialDrawDone === true) {
	          _this._redraw();
	        }
	      });
	      this.on('rangechanged', function () {
	        if (!_this.initialRangeChangeDone) {
	          _this.initialRangeChangeDone = true;
	        }
	      });
	      this.on('touch', _bindInstanceProperty(_context = this._onTouch).call(_context, this));
	      this.on('panmove', _bindInstanceProperty(_context2 = this._onDrag).call(_context2, this));
	      var me = this;
	      this._origRedraw = _bindInstanceProperty(_context3 = this._redraw).call(_context3, this);
	      this._redraw = availableUtils.throttle(this._origRedraw);
	      this.on('_change', function (properties) {
	        if (me.itemSet && me.itemSet.initialItemSetDrawn && properties && properties.queue == true) {
	          me._redraw();
	        } else {
	          me._origRedraw();
	        }
	      });

	      
	      
	      this.hammer = new Hammer(this.dom.root);
	      var pinchRecognizer = this.hammer.get('pinch').set({
	        enable: true
	      });
	      pinchRecognizer && disablePreventDefaultVertically(pinchRecognizer);
	      this.hammer.get('pan').set({
	        threshold: 5,
	        direction: Hammer.DIRECTION_ALL
	      });
	      this.timelineListeners = {};
	      var events = ['tap', 'doubletap', 'press', 'pinch', 'pan', 'panstart', 'panmove', 'panend'
	      
	      
	      
	      
	      
	      ];

	      _forEachInstanceProperty(events).call(events, function (type) {
	        var listener = function listener(event) {
	          if (me.isActive()) {
	            me.emit(type, event);
	          }
	        };
	        me.hammer.on(type, listener);
	        me.timelineListeners[type] = listener;
	      });

	      
	      onTouch(this.hammer, function (event) {
	        me.emit('touch', event);
	      });

	      
	      onRelease(this.hammer, function (event) {
	        me.emit('release', event);
	      });

	      
	      function onMouseWheel(event) {
	        
	        var LINE_HEIGHT = 40;
	        var PAGE_HEIGHT = 800;
	        if (this.isActive()) {
	          this.emit('mousewheel', event);
	        }

	        
	        var deltaX = 0;
	        var deltaY = 0;

	        
	        if ('detail' in event) {
	          deltaY = event.detail * -1;
	        }
	        if ('wheelDelta' in event) {
	          deltaY = event.wheelDelta;
	        }
	        if ('wheelDeltaY' in event) {
	          deltaY = event.wheelDeltaY;
	        }
	        if ('wheelDeltaX' in event) {
	          deltaX = event.wheelDeltaX * -1;
	        }

	        
	        if ('axis' in event && event.axis === event.HORIZONTAL_AXIS) {
	          deltaX = deltaY * -1;
	          deltaY = 0;
	        }

	        
	        if ('deltaY' in event) {
	          deltaY = event.deltaY * -1;
	        }
	        if ('deltaX' in event) {
	          deltaX = event.deltaX;
	        }

	        
	        if (event.deltaMode) {
	          if (event.deltaMode === 1) {
	            
	            deltaX *= LINE_HEIGHT;
	            deltaY *= LINE_HEIGHT;
	          } else {
	            
	            deltaX *= LINE_HEIGHT;
	            deltaY *= PAGE_HEIGHT;
	          }
	        }
	        
	        if (this.options.preferZoom) {
	          if (!this.options.zoomKey || event[this.options.zoomKey]) return;
	        } else {
	          if (this.options.zoomKey && event[this.options.zoomKey]) return;
	        }
	        
	        if (!this.options.verticalScroll && !this.options.horizontalScroll) return;
	        if (this.options.verticalScroll && Math.abs(deltaY) >= Math.abs(deltaX)) {
	          var current = this.props.scrollTop;
	          var adjusted = current + deltaY;
	          if (this.isActive()) {
	            var newScrollTop = this._setScrollTop(adjusted);
	            if (newScrollTop !== current) {
	              this._redraw();
	              this.emit('scroll', event);

	              
	              
	              event.preventDefault();
	            }
	          }
	        } else if (this.options.horizontalScroll) {
	          var delta = Math.abs(deltaX) >= Math.abs(deltaY) ? deltaX : deltaY;

	          
	          var diff = delta / 120 * (this.range.end - this.range.start) / 20;
	          
	          var newStart = this.range.start + diff;
	          var newEnd = this.range.end + diff;
	          var options = {
	            animation: false,
	            byUser: true,
	            event: event
	          };
	          this.range.setRange(newStart, newEnd, options);
	          event.preventDefault();
	        }
	      }

	      
	      var wheelType = "onwheel" in document.createElement("div") ? "wheel" :
	      
	      document.onmousewheel !== undefined ? "mousewheel" :
	      

	      
	      
	      this.dom.centerContainer.addEventListener ? "DOMMouseScroll" : "onmousewheel";
	      this.dom.top.addEventListener ? "DOMMouseScroll" : "onmousewheel";
	      this.dom.bottom.addEventListener ? "DOMMouseScroll" : "onmousewheel";
	      this.dom.centerContainer.addEventListener(wheelType, _bindInstanceProperty(onMouseWheel).call(onMouseWheel, this), false);
	      this.dom.top.addEventListener(wheelType, _bindInstanceProperty(onMouseWheel).call(onMouseWheel, this), false);
	      this.dom.bottom.addEventListener(wheelType, _bindInstanceProperty(onMouseWheel).call(onMouseWheel, this), false);

	      
	      function onMouseScrollSide(event) {
	        if (!me.options.verticalScroll) return;
	        event.preventDefault();
	        if (me.isActive()) {
	          var adjusted = -event.target.scrollTop;
	          me._setScrollTop(adjusted);
	          me._redraw();
	          me.emit('scrollSide', event);
	        }
	      }
	      this.dom.left.parentNode.addEventListener('scroll', _bindInstanceProperty(onMouseScrollSide).call(onMouseScrollSide, this));
	      this.dom.right.parentNode.addEventListener('scroll', _bindInstanceProperty(onMouseScrollSide).call(onMouseScrollSide, this));
	      var itemAddedToTimeline = false;

	      
	      function handleDragOver(event) {
	        var _context4;
	        if (event.preventDefault) {
	          me.emit('dragover', me.getEventProperties(event));
	          event.preventDefault(); 
	        }

	        
	        if (!(_indexOfInstanceProperty(_context4 = event.target.className).call(_context4, "timeline") > -1)) return;

	        
	        if (itemAddedToTimeline) return;
	        event.dataTransfer.dropEffect = 'move';
	        itemAddedToTimeline = true;
	        return false;
	      }

	      
	      function handleDrop(event) {
	        
	        if (event.preventDefault) {
	          event.preventDefault();
	        }
	        if (event.stopPropagation) {
	          event.stopPropagation();
	        }
	        
	        try {
	          var itemData = JSON.parse(event.dataTransfer.getData("text"));
	          if (!itemData || !itemData.content) return;
	        } catch (err) {
	          return false;
	        }
	        itemAddedToTimeline = false;
	        event.center = {
	          x: event.clientX,
	          y: event.clientY
	        };
	        if (itemData.target !== 'item') {
	          me.itemSet._onAddItem(event);
	        } else {
	          me.itemSet._onDropObjectOnItem(event);
	        }
	        me.emit('drop', me.getEventProperties(event));
	        return false;
	      }
	      this.dom.center.addEventListener('dragover', _bindInstanceProperty(handleDragOver).call(handleDragOver, this), false);
	      this.dom.center.addEventListener('drop', _bindInstanceProperty(handleDrop).call(handleDrop, this), false);
	      this.customTimes = [];

	      
	      this.touch = {};
	      this.redrawCount = 0;
	      this.initialDrawDone = false;
	      this.initialRangeChangeDone = false;

	      
	      if (!container) throw new Error('No container provided');
	      container.appendChild(this.dom.root);
	      container.appendChild(this.dom.loadingScreen);
	    }

	    
	  }, {
	    key: "setOptions",
	    value: function setOptions(options) {
	      var _context7;
	      if (options) {
	        
	        var fields = ['width', 'height', 'minHeight', 'maxHeight', 'autoResize', 'start', 'end', 'clickToUse', 'dataAttributes', 'hiddenDates', 'locale', 'locales', 'moment', 'preferZoom', 'rtl', 'zoomKey', 'horizontalScroll', 'verticalScroll', 'longSelectPressTime', 'snap'];
	        availableUtils.selectiveExtend(fields, this.options, options);
	        this.dom.rollingModeBtn.style.visibility = 'hidden';
	        if (this.options.rtl) {
	          this.dom.container.style.direction = "rtl";
	          this.dom.backgroundVertical.className = 'vis-panel vis-background vis-vertical-rtl';
	        }
	        if (this.options.verticalScroll) {
	          if (this.options.rtl) {
	            this.dom.rightContainer.className = 'vis-panel vis-right vis-vertical-scroll';
	          } else {
	            this.dom.leftContainer.className = 'vis-panel vis-left vis-vertical-scroll';
	          }
	        }
	        if (_typeof(this.options.orientation) !== 'object') {
	          this.options.orientation = {
	            item: undefined,
	            axis: undefined
	          };
	        }
	        if ('orientation' in options) {
	          if (typeof options.orientation === 'string') {
	            this.options.orientation = {
	              item: options.orientation,
	              axis: options.orientation
	            };
	          } else if (_typeof(options.orientation) === 'object') {
	            if ('item' in options.orientation) {
	              this.options.orientation.item = options.orientation.item;
	            }
	            if ('axis' in options.orientation) {
	              this.options.orientation.axis = options.orientation.axis;
	            }
	          }
	        }
	        if (this.options.orientation.axis === 'both') {
	          if (!this.timeAxis2) {
	            var timeAxis2 = this.timeAxis2 = new TimeAxis(this.body);
	            timeAxis2.setOptions = function (options) {
	              var _options = options ? availableUtils.extend({}, options) : {};
	              _options.orientation = 'top'; 
	              TimeAxis.prototype.setOptions.call(timeAxis2, _options);
	            };
	            this.components.push(timeAxis2);
	          }
	        } else {
	          if (this.timeAxis2) {
	            var _context5;
	            var index = _indexOfInstanceProperty(_context5 = this.components).call(_context5, this.timeAxis2);
	            if (index !== -1) {
	              var _context6;
	              _spliceInstanceProperty(_context6 = this.components).call(_context6, index, 1);
	            }
	            this.timeAxis2.destroy();
	            this.timeAxis2 = null;
	          }
	        }

	        
	        if (typeof options.drawPoints == 'function') {
	          options.drawPoints = {
	            onRender: options.drawPoints
	          };
	        }
	        if ('hiddenDates' in this.options) {
	          convertHiddenOptions(this.options.moment, this.body, this.options.hiddenDates);
	        }
	        if ('clickToUse' in options) {
	          if (options.clickToUse) {
	            if (!this.activator) {
	              this.activator = new Activator(this.dom.root);
	            }
	          } else {
	            if (this.activator) {
	              this.activator.destroy();
	              delete this.activator;
	            }
	          }
	        }

	        
	        this._initAutoResize();
	      }

	      
	      _forEachInstanceProperty(_context7 = this.components).call(_context7, function (component) {
	        return component.setOptions(options);
	      });

	      
	      if ('configure' in options) {
	        var _context8;
	        if (!this.configurator) {
	          this.configurator = this._createConfigurator();
	        }
	        this.configurator.setOptions(options.configure);

	        
	        var appliedOptions = availableUtils.deepExtend({}, this.options);
	        _forEachInstanceProperty(_context8 = this.components).call(_context8, function (component) {
	          availableUtils.deepExtend(appliedOptions, component.options);
	        });
	        this.configurator.setModuleOptions({
	          global: appliedOptions
	        });
	      }
	      this._redraw();
	    }

	    
	  }, {
	    key: "isActive",
	    value: function isActive() {
	      return !this.activator || this.activator.active;
	    }

	    
	  }, {
	    key: "destroy",
	    value: function destroy() {
	      var _context9;
	      
	      this.setItems(null);
	      this.setGroups(null);

	      
	      this.off();

	      
	      this._stopAutoResize();

	      
	      if (this.dom.root.parentNode) {
	        this.dom.root.parentNode.removeChild(this.dom.root);
	      }
	      this.dom = null;

	      
	      if (this.activator) {
	        this.activator.destroy();
	        delete this.activator;
	      }

	      
	      for (var event in this.timelineListeners) {
	        if (this.timelineListeners.hasOwnProperty(event)) {
	          delete this.timelineListeners[event];
	        }
	      }
	      this.timelineListeners = null;
	      this.hammer && this.hammer.destroy();
	      this.hammer = null;

	      
	      _forEachInstanceProperty(_context9 = this.components).call(_context9, function (component) {
	        return component.destroy();
	      });
	      this.body = null;
	    }

	    
	  }, {
	    key: "setCustomTime",
	    value: function setCustomTime(time, id) {
	      var _context10;
	      var customTimes = _filterInstanceProperty(_context10 = this.customTimes).call(_context10, function (component) {
	        return id === component.options.id;
	      });
	      if (customTimes.length === 0) {
	        throw new Error("No custom time bar found with id ".concat(_JSON$stringify(id)));
	      }
	      if (customTimes.length > 0) {
	        customTimes[0].setCustomTime(time);
	      }
	    }

	    
	  }, {
	    key: "getCustomTime",
	    value: function getCustomTime(id) {
	      var _context11;
	      var customTimes = _filterInstanceProperty(_context11 = this.customTimes).call(_context11, function (component) {
	        return component.options.id === id;
	      });
	      if (customTimes.length === 0) {
	        throw new Error("No custom time bar found with id ".concat(_JSON$stringify(id)));
	      }
	      return customTimes[0].getCustomTime();
	    }

	    
	  }, {
	    key: "setCustomTimeMarker",
	    value: function setCustomTimeMarker(title, id, editable) {
	      var _context12;
	      var customTimes = _filterInstanceProperty(_context12 = this.customTimes).call(_context12, function (component) {
	        return component.options.id === id;
	      });
	      if (customTimes.length === 0) {
	        throw new Error("No custom time bar found with id ".concat(_JSON$stringify(id)));
	      }
	      if (customTimes.length > 0) {
	        customTimes[0].setCustomMarker(title, editable);
	      }
	    }

	    
	  }, {
	    key: "setCustomTimeTitle",
	    value: function setCustomTimeTitle(title, id) {
	      var _context13;
	      var customTimes = _filterInstanceProperty(_context13 = this.customTimes).call(_context13, function (component) {
	        return component.options.id === id;
	      });
	      if (customTimes.length === 0) {
	        throw new Error("No custom time bar found with id ".concat(_JSON$stringify(id)));
	      }
	      if (customTimes.length > 0) {
	        return customTimes[0].setCustomTitle(title);
	      }
	    }

	    
	  }, {
	    key: "getEventProperties",
	    value: function getEventProperties(event) {
	      return {
	        event: event
	      };
	    }

	    
	  }, {
	    key: "addCustomTime",
	    value: function addCustomTime(time, id) {
	      var _context14;
	      var timestamp = time !== undefined ? availableUtils.convert(time, 'Date') : new Date();
	      var exists = _someInstanceProperty(_context14 = this.customTimes).call(_context14, function (customTime) {
	        return customTime.options.id === id;
	      });
	      if (exists) {
	        throw new Error("A custom time with id ".concat(_JSON$stringify(id), " already exists"));
	      }
	      var customTime = new CustomTime(this.body, availableUtils.extend({}, this.options, {
	        time: timestamp,
	        id: id,
	        snap: this.itemSet ? this.itemSet.options.snap : this.options.snap
	      }));
	      this.customTimes.push(customTime);
	      this.components.push(customTime);
	      this._redraw();
	      return id;
	    }

	    
	  }, {
	    key: "removeCustomTime",
	    value: function removeCustomTime(id) {
	      var _context15,
	        _this2 = this;
	      var customTimes = _filterInstanceProperty(_context15 = this.customTimes).call(_context15, function (bar) {
	        return bar.options.id === id;
	      });
	      if (customTimes.length === 0) {
	        throw new Error("No custom time bar found with id ".concat(_JSON$stringify(id)));
	      }
	      _forEachInstanceProperty(customTimes).call(customTimes, function (customTime) {
	        var _context16, _context17, _context18, _context19;
	        _spliceInstanceProperty(_context16 = _this2.customTimes).call(_context16, _indexOfInstanceProperty(_context17 = _this2.customTimes).call(_context17, customTime), 1);
	        _spliceInstanceProperty(_context18 = _this2.components).call(_context18, _indexOfInstanceProperty(_context19 = _this2.components).call(_context19, customTime), 1);
	        customTime.destroy();
	      });
	    }

	    
	  }, {
	    key: "getVisibleItems",
	    value: function getVisibleItems() {
	      return this.itemSet && this.itemSet.getVisibleItems() || [];
	    }

	    
	  }, {
	    key: "getItemsAtCurrentTime",
	    value: function getItemsAtCurrentTime(timeOfEvent) {
	      this.time = timeOfEvent;
	      return this.itemSet && this.itemSet.getItemsAtCurrentTime(this.time) || [];
	    }

	    
	  }, {
	    key: "getVisibleGroups",
	    value: function getVisibleGroups() {
	      return this.itemSet && this.itemSet.getVisibleGroups() || [];
	    }

	    
	  }, {
	    key: "fit",
	    value: function fit(options, callback) {
	      var range = this.getDataRange();

	      
	      if (range.min === null && range.max === null) {
	        return;
	      }

	      
	      var interval = range.max - range.min;
	      var min = new Date(range.min.valueOf() - interval * 0.01);
	      var max = new Date(range.max.valueOf() + interval * 0.01);
	      var animation = options && options.animation !== undefined ? options.animation : true;
	      this.range.setRange(min, max, {
	        animation: animation
	      }, callback);
	    }

	    
	  }, {
	    key: "getDataRange",
	    value: function getDataRange() {
	      
	      throw new Error('Cannot invoke abstract method getDataRange');
	    }

	    
	  }, {
	    key: "setWindow",
	    value: function setWindow(start, end, options, callback) {
	      if (typeof arguments[2] == "function") {
	        callback = arguments[2];
	        options = {};
	      }
	      var animation;
	      var range;
	      if (arguments.length == 1) {
	        range = arguments[0];
	        animation = range.animation !== undefined ? range.animation : true;
	        this.range.setRange(range.start, range.end, {
	          animation: animation
	        });
	      } else if (arguments.length == 2 && typeof arguments[1] == "function") {
	        range = arguments[0];
	        callback = arguments[1];
	        animation = range.animation !== undefined ? range.animation : true;
	        this.range.setRange(range.start, range.end, {
	          animation: animation
	        }, callback);
	      } else {
	        animation = options && options.animation !== undefined ? options.animation : true;
	        this.range.setRange(start, end, {
	          animation: animation
	        }, callback);
	      }
	    }

	    
	  }, {
	    key: "moveTo",
	    value: function moveTo(time, options, callback) {
	      if (typeof arguments[1] == "function") {
	        callback = arguments[1];
	        options = {};
	      }
	      var interval = this.range.end - this.range.start;
	      var t = availableUtils.convert(time, 'Date').valueOf();
	      var start = t - interval / 2;
	      var end = t + interval / 2;
	      var animation = options && options.animation !== undefined ? options.animation : true;
	      this.range.setRange(start, end, {
	        animation: animation
	      }, callback);
	    }

	    
	  }, {
	    key: "getWindow",
	    value: function getWindow() {
	      var range = this.range.getRange();
	      return {
	        start: new Date(range.start),
	        end: new Date(range.end)
	      };
	    }

	    
	  }, {
	    key: "zoomIn",
	    value: function zoomIn(percentage, options, callback) {
	      if (!percentage || percentage < 0 || percentage > 1) return;
	      if (typeof arguments[1] == "function") {
	        callback = arguments[1];
	        options = {};
	      }
	      var range = this.getWindow();
	      var start = range.start.valueOf();
	      var end = range.end.valueOf();
	      var interval = end - start;
	      var newInterval = interval / (1 + percentage);
	      var distance = (interval - newInterval) / 2;
	      var newStart = start + distance;
	      var newEnd = end - distance;
	      this.setWindow(newStart, newEnd, options, callback);
	    }

	    
	  }, {
	    key: "zoomOut",
	    value: function zoomOut(percentage, options, callback) {
	      if (!percentage || percentage < 0 || percentage > 1) return;
	      if (typeof arguments[1] == "function") {
	        callback = arguments[1];
	        options = {};
	      }
	      var range = this.getWindow();
	      var start = range.start.valueOf();
	      var end = range.end.valueOf();
	      var interval = end - start;
	      var newStart = start - interval * percentage / 2;
	      var newEnd = end + interval * percentage / 2;
	      this.setWindow(newStart, newEnd, options, callback);
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw() {
	      this._redraw();
	    }

	    
	  }, {
	    key: "_redraw",
	    value: function _redraw() {
	      var _context20;
	      this.redrawCount++;
	      var dom = this.dom;
	      if (!dom || !dom.container || dom.root.offsetWidth == 0) return; 

	      var resized = false;
	      var options = this.options;
	      var props = this.props;
	      updateHiddenDates(this.options.moment, this.body, this.options.hiddenDates);

	      
	      if (options.orientation == 'top') {
	        availableUtils.addClassName(dom.root, 'vis-top');
	        availableUtils.removeClassName(dom.root, 'vis-bottom');
	      } else {
	        availableUtils.removeClassName(dom.root, 'vis-top');
	        availableUtils.addClassName(dom.root, 'vis-bottom');
	      }
	      if (options.rtl) {
	        availableUtils.addClassName(dom.root, 'vis-rtl');
	        availableUtils.removeClassName(dom.root, 'vis-ltr');
	      } else {
	        availableUtils.addClassName(dom.root, 'vis-ltr');
	        availableUtils.removeClassName(dom.root, 'vis-rtl');
	      }

	      
	      dom.root.style.maxHeight = availableUtils.option.asSize(options.maxHeight, '');
	      dom.root.style.minHeight = availableUtils.option.asSize(options.minHeight, '');
	      dom.root.style.width = availableUtils.option.asSize(options.width, '');
	      var rootOffsetWidth = dom.root.offsetWidth;

	      
	      props.border.left = 1;
	      props.border.right = 1;
	      props.border.top = 1;
	      props.border.bottom = 1;

	      
	      
	      props.center.height = dom.center.offsetHeight;
	      props.left.height = dom.left.offsetHeight;
	      props.right.height = dom.right.offsetHeight;
	      props.top.height = dom.top.clientHeight || -props.border.top;
	      props.bottom.height = Math.round(dom.bottom.getBoundingClientRect().height) || dom.bottom.clientHeight || -props.border.bottom;

	      

	      
	      
	      var contentHeight = Math.max(props.left.height, props.center.height, props.right.height);
	      var autoHeight = props.top.height + contentHeight + props.bottom.height + props.border.top + props.border.bottom;
	      dom.root.style.height = availableUtils.option.asSize(options.height, "".concat(autoHeight, "px"));

	      
	      props.root.height = dom.root.offsetHeight;
	      props.background.height = props.root.height;
	      var containerHeight = props.root.height - props.top.height - props.bottom.height;
	      props.centerContainer.height = containerHeight;
	      props.leftContainer.height = containerHeight;
	      props.rightContainer.height = props.leftContainer.height;

	      
	      props.root.width = rootOffsetWidth;
	      props.background.width = props.root.width;
	      if (!this.initialDrawDone) {
	        props.scrollbarWidth = availableUtils.getScrollBarWidth();
	      }
	      var leftContainerClientWidth = dom.leftContainer.clientWidth;
	      var rightContainerClientWidth = dom.rightContainer.clientWidth;
	      if (options.verticalScroll) {
	        if (options.rtl) {
	          props.left.width = leftContainerClientWidth || -props.border.left;
	          props.right.width = rightContainerClientWidth + props.scrollbarWidth || -props.border.right;
	        } else {
	          props.left.width = leftContainerClientWidth + props.scrollbarWidth || -props.border.left;
	          props.right.width = rightContainerClientWidth || -props.border.right;
	        }
	      } else {
	        props.left.width = leftContainerClientWidth || -props.border.left;
	        props.right.width = rightContainerClientWidth || -props.border.right;
	      }
	      this._setDOM();

	      
	      
	      var offset = this._updateScrollTop();

	      
	      if (options.orientation.item != 'top') {
	        offset += Math.max(props.centerContainer.height - props.center.height - props.border.top - props.border.bottom, 0);
	      }
	      dom.center.style.transform = "translateY(".concat(offset, "px)");

	      
	      var visibilityTop = props.scrollTop == 0 ? 'hidden' : '';
	      var visibilityBottom = props.scrollTop == props.scrollTopMin ? 'hidden' : '';
	      dom.shadowTop.style.visibility = visibilityTop;
	      dom.shadowBottom.style.visibility = visibilityBottom;
	      dom.shadowTopLeft.style.visibility = visibilityTop;
	      dom.shadowBottomLeft.style.visibility = visibilityBottom;
	      dom.shadowTopRight.style.visibility = visibilityTop;
	      dom.shadowBottomRight.style.visibility = visibilityBottom;
	      if (options.verticalScroll) {
	        dom.rightContainer.className = 'vis-panel vis-right vis-vertical-scroll';
	        dom.leftContainer.className = 'vis-panel vis-left vis-vertical-scroll';
	        dom.shadowTopRight.style.visibility = "hidden";
	        dom.shadowBottomRight.style.visibility = "hidden";
	        dom.shadowTopLeft.style.visibility = "hidden";
	        dom.shadowBottomLeft.style.visibility = "hidden";
	        dom.left.style.top = '0px';
	        dom.right.style.top = '0px';
	      }
	      if (!options.verticalScroll || props.center.height < props.centerContainer.height) {
	        dom.left.style.top = "".concat(offset, "px");
	        dom.right.style.top = "".concat(offset, "px");
	        dom.rightContainer.className = dom.rightContainer.className.replace(new RegExp('(?:^|\\s)' + 'vis-vertical-scroll' + '(?:\\s|$)'), ' ');
	        dom.leftContainer.className = dom.leftContainer.className.replace(new RegExp('(?:^|\\s)' + 'vis-vertical-scroll' + '(?:\\s|$)'), ' ');
	        props.left.width = leftContainerClientWidth || -props.border.left;
	        props.right.width = rightContainerClientWidth || -props.border.right;
	        this._setDOM();
	      }

	      
	      var contentsOverflow = props.center.height > props.centerContainer.height;
	      this.hammer.get('pan').set({
	        direction: contentsOverflow ? Hammer.DIRECTION_ALL : Hammer.DIRECTION_HORIZONTAL
	      });

	      
	      this.hammer.get('press').set({
	        time: this.options.longSelectPressTime
	      });

	      
	      _forEachInstanceProperty(_context20 = this.components).call(_context20, function (component) {
	        resized = component.redraw() || resized;
	      });
	      var MAX_REDRAW = 5;
	      if (resized) {
	        if (this.redrawCount < MAX_REDRAW) {
	          this.body.emitter.emit('_change');
	          return;
	        } else {
	          console.log('WARNING: infinite loop in redraw?');
	        }
	      } else {
	        this.redrawCount = 0;
	      }

	      
	      this.body.emitter.emit("changed");
	    }

	    
	  }, {
	    key: "_setDOM",
	    value: function _setDOM() {
	      var props = this.props;
	      var dom = this.dom;
	      props.leftContainer.width = props.left.width;
	      props.rightContainer.width = props.right.width;
	      var centerWidth = props.root.width - props.left.width - props.right.width;
	      props.center.width = centerWidth;
	      props.centerContainer.width = centerWidth;
	      props.top.width = centerWidth;
	      props.bottom.width = centerWidth;

	      
	      dom.background.style.height = "".concat(props.background.height, "px");
	      dom.backgroundVertical.style.height = "".concat(props.background.height, "px");
	      dom.backgroundHorizontal.style.height = "".concat(props.centerContainer.height, "px");
	      dom.centerContainer.style.height = "".concat(props.centerContainer.height, "px");
	      dom.leftContainer.style.height = "".concat(props.leftContainer.height, "px");
	      dom.rightContainer.style.height = "".concat(props.rightContainer.height, "px");
	      dom.background.style.width = "".concat(props.background.width, "px");
	      dom.backgroundVertical.style.width = "".concat(props.centerContainer.width, "px");
	      dom.backgroundHorizontal.style.width = "".concat(props.background.width, "px");
	      dom.centerContainer.style.width = "".concat(props.center.width, "px");
	      dom.top.style.width = "".concat(props.top.width, "px");
	      dom.bottom.style.width = "".concat(props.bottom.width, "px");

	      
	      dom.background.style.left = '0';
	      dom.background.style.top = '0';
	      dom.backgroundVertical.style.left = "".concat(props.left.width + props.border.left, "px");
	      dom.backgroundVertical.style.top = '0';
	      dom.backgroundHorizontal.style.left = '0';
	      dom.backgroundHorizontal.style.top = "".concat(props.top.height, "px");
	      dom.centerContainer.style.left = "".concat(props.left.width, "px");
	      dom.centerContainer.style.top = "".concat(props.top.height, "px");
	      dom.leftContainer.style.left = '0';
	      dom.leftContainer.style.top = "".concat(props.top.height, "px");
	      dom.rightContainer.style.left = "".concat(props.left.width + props.center.width, "px");
	      dom.rightContainer.style.top = "".concat(props.top.height, "px");
	      dom.top.style.left = "".concat(props.left.width, "px");
	      dom.top.style.top = '0';
	      dom.bottom.style.left = "".concat(props.left.width, "px");
	      dom.bottom.style.top = "".concat(props.top.height + props.centerContainer.height, "px");
	      dom.center.style.left = '0';
	      dom.left.style.left = '0';
	      dom.right.style.left = '0';
	    }

	    
	  }, {
	    key: "setCurrentTime",
	    value: function setCurrentTime(time) {
	      if (!this.currentTime) {
	        throw new Error('Option showCurrentTime must be true');
	      }
	      this.currentTime.setCurrentTime(time);
	    }

	    
	  }, {
	    key: "getCurrentTime",
	    value: function getCurrentTime() {
	      if (!this.currentTime) {
	        throw new Error('Option showCurrentTime must be true');
	      }
	      return this.currentTime.getCurrentTime();
	    }

	    
	  }, {
	    key: "_toTime",
	    value: function _toTime(x) {
	      return toTime(this, x, this.props.center.width);
	    }

	    
	  }, {
	    key: "_toGlobalTime",
	    value: function _toGlobalTime(x) {
	      return toTime(this, x, this.props.root.width);
	      
	      
	    }

	    
	  }, {
	    key: "_toScreen",
	    value: function _toScreen(time) {
	      return toScreen(this, time, this.props.center.width);
	    }

	    
	  }, {
	    key: "_toGlobalScreen",
	    value: function _toGlobalScreen(time) {
	      return toScreen(this, time, this.props.root.width);
	      
	      
	    }

	    
	  }, {
	    key: "_initAutoResize",
	    value: function _initAutoResize() {
	      if (this.options.autoResize == true) {
	        this._startAutoResize();
	      } else {
	        this._stopAutoResize();
	      }
	    }

	    
	  }, {
	    key: "_startAutoResize",
	    value: function _startAutoResize() {
	      var me = this;
	      this._stopAutoResize();
	      this._onResize = function () {
	        if (me.options.autoResize != true) {
	          
	          me._stopAutoResize();
	          return;
	        }
	        if (me.dom.root) {
	          var rootOffsetHeight = me.dom.root.offsetHeight;
	          var rootOffsetWidth = me.dom.root.offsetWidth;
	          
	          
	          
	          
	          if (rootOffsetWidth != me.props.lastWidth || rootOffsetHeight != me.props.lastHeight) {
	            me.props.lastWidth = rootOffsetWidth;
	            me.props.lastHeight = rootOffsetHeight;
	            me.props.scrollbarWidth = availableUtils.getScrollBarWidth();
	            me.body.emitter.emit('_change');
	          }
	        }
	      };

	      
	      availableUtils.addEventListener(window, 'resize', this._onResize);

	      
	      if (me.dom.root) {
	        me.props.lastWidth = me.dom.root.offsetWidth;
	        me.props.lastHeight = me.dom.root.offsetHeight;
	      }
	      this.watchTimer = _setInterval(this._onResize, 1000);
	    }

	    
	  }, {
	    key: "_stopAutoResize",
	    value: function _stopAutoResize() {
	      if (this.watchTimer) {
	        clearInterval(this.watchTimer);
	        this.watchTimer = undefined;
	      }

	      
	      if (this._onResize) {
	        availableUtils.removeEventListener(window, 'resize', this._onResize);
	        this._onResize = null;
	      }
	    }

	    
	  }, {
	    key: "_onTouch",
	    value: function _onTouch(event) {
	      
	      this.touch.allowDragging = true;
	      this.touch.initialScrollTop = this.props.scrollTop;
	    }

	    
	  }, {
	    key: "_onPinch",
	    value: function _onPinch(event) {
	      
	      this.touch.allowDragging = false;
	    }

	    
	  }, {
	    key: "_onDrag",
	    value: function _onDrag(event) {
	      if (!event) return;
	      
	      
	      if (!this.touch.allowDragging) return;
	      var delta = event.deltaY;
	      var oldScrollTop = this._getScrollTop();
	      var newScrollTop = this._setScrollTop(this.touch.initialScrollTop + delta);
	      if (this.options.verticalScroll) {
	        this.dom.left.parentNode.scrollTop = -this.props.scrollTop;
	        this.dom.right.parentNode.scrollTop = -this.props.scrollTop;
	      }
	      if (newScrollTop != oldScrollTop) {
	        this.emit("verticalDrag");
	      }
	    }

	    
	  }, {
	    key: "_setScrollTop",
	    value: function _setScrollTop(scrollTop) {
	      this.props.scrollTop = scrollTop;
	      this._updateScrollTop();
	      return this.props.scrollTop;
	    }

	    
	  }, {
	    key: "_updateScrollTop",
	    value: function _updateScrollTop() {
	      
	      var scrollTopMin = Math.min(this.props.centerContainer.height - this.props.border.top - this.props.border.bottom - this.props.center.height, 0); 
	      if (scrollTopMin != this.props.scrollTopMin) {
	        
	        
	        if (this.options.orientation.item != 'top') {
	          this.props.scrollTop += scrollTopMin - this.props.scrollTopMin;
	        }
	        this.props.scrollTopMin = scrollTopMin;
	      }

	      
	      if (this.props.scrollTop > 0) this.props.scrollTop = 0;
	      if (this.props.scrollTop < scrollTopMin) this.props.scrollTop = scrollTopMin;
	      if (this.options.verticalScroll) {
	        this.dom.left.parentNode.scrollTop = -this.props.scrollTop;
	        this.dom.right.parentNode.scrollTop = -this.props.scrollTop;
	      }
	      return this.props.scrollTop;
	    }

	    
	  }, {
	    key: "_getScrollTop",
	    value: function _getScrollTop() {
	      return this.props.scrollTop;
	    }

	    
	  }, {
	    key: "_createConfigurator",
	    value: function _createConfigurator() {
	      throw new Error('Cannot invoke abstract method _createConfigurator');
	    }
	  }]);
	  return Core;
	}(); 
	Emitter(Core.prototype);

	function _createSuper$9(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$9(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$9() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	
	var CurrentTime = function (_Component) {
	  _inherits(CurrentTime, _Component);
	  var _super = _createSuper$9(CurrentTime);
	  
	  function CurrentTime(body, options) {
	    var _context;
	    var _this;
	    _classCallCheck(this, CurrentTime);
	    _this = _super.call(this);
	    _this.body = body;

	    
	    _this.defaultOptions = {
	      rtl: false,
	      showCurrentTime: true,
	      alignCurrentTime: undefined,
	      moment: moment$2,
	      locales: locales,
	      locale: 'en'
	    };
	    _this.options = availableUtils.extend({}, _this.defaultOptions);
	    _this.setOptions(options);
	    _this.options.locales = availableUtils.extend({}, locales, _this.options.locales);
	    var defaultLocales = _this.defaultOptions.locales[_this.defaultOptions.locale];
	    _forEachInstanceProperty(_context = _Object$keys(_this.options.locales)).call(_context, function (locale) {
	      _this.options.locales[locale] = availableUtils.extend({}, defaultLocales, _this.options.locales[locale]);
	    });
	    _this.offset = 0;
	    _this._create();
	    return _this;
	  }

	  
	  _createClass(CurrentTime, [{
	    key: "_create",
	    value: function _create() {
	      var bar = document.createElement('div');
	      bar.className = 'vis-current-time';
	      bar.style.position = 'absolute';
	      bar.style.top = '0px';
	      bar.style.height = '100%';
	      this.bar = bar;
	    }

	    
	  }, {
	    key: "destroy",
	    value: function destroy() {
	      this.options.showCurrentTime = false;
	      this.redraw(); 

	      this.body = null;
	    }

	    
	  }, {
	    key: "setOptions",
	    value: function setOptions(options) {
	      if (options) {
	        
	        availableUtils.selectiveExtend(['rtl', 'showCurrentTime', 'alignCurrentTime', 'moment', 'locale', 'locales'], this.options, options);
	      }
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw() {
	      if (this.options.showCurrentTime) {
	        var _context2, _context3;
	        var parent = this.body.dom.backgroundVertical;
	        if (this.bar.parentNode != parent) {
	          
	          if (this.bar.parentNode) {
	            this.bar.parentNode.removeChild(this.bar);
	          }
	          parent.appendChild(this.bar);
	          this.start();
	        }
	        var now = this.options.moment(_Date$now() + this.offset);
	        if (this.options.alignCurrentTime) {
	          now = now.startOf(this.options.alignCurrentTime);
	        }
	        var x = this.body.util.toScreen(now);
	        var locale = this.options.locales[this.options.locale];
	        if (!locale) {
	          if (!this.warned) {
	            console.warn("WARNING: options.locales['".concat(this.options.locale, "'] not found. See https:
	            this.warned = true;
	          }
	          locale = this.options.locales['en']; 
	        }

	        var title = _concatInstanceProperty(_context2 = _concatInstanceProperty(_context3 = "".concat(locale.current, " ")).call(_context3, locale.time, ": ")).call(_context2, now.format('dddd, MMMM Do YYYY, H:mm:ss'));
	        title = title.charAt(0).toUpperCase() + title.substring(1);
	        if (this.options.rtl) {
	          this.bar.style.transform = "translateX(".concat(x * -1, "px)");
	        } else {
	          this.bar.style.transform = "translateX(".concat(x, "px)");
	        }
	        this.bar.title = title;
	      } else {
	        
	        if (this.bar.parentNode) {
	          this.bar.parentNode.removeChild(this.bar);
	        }
	        this.stop();
	      }
	      return false;
	    }

	    
	  }, {
	    key: "start",
	    value: function start() {
	      var me = this;

	      
	      function update() {
	        me.stop();

	        
	        var scale = me.body.range.conversion(me.body.domProps.center.width).scale;
	        var interval = 1 / scale / 10;
	        if (interval < 30) interval = 30;
	        if (interval > 1000) interval = 1000;
	        me.redraw();
	        me.body.emitter.emit('currentTimeTick');

	        
	        me.currentTimeTimer = _setTimeout(update, interval);
	      }
	      update();
	    }

	    
	  }, {
	    key: "stop",
	    value: function stop() {
	      if (this.currentTimeTimer !== undefined) {
	        clearTimeout(this.currentTimeTimer);
	        delete this.currentTimeTimer;
	      }
	    }

	    
	  }, {
	    key: "setCurrentTime",
	    value: function setCurrentTime(time) {
	      var t = availableUtils.convert(time, 'Date').valueOf();
	      var now = _Date$now();
	      this.offset = t - now;
	      this.redraw();
	    }

	    
	  }, {
	    key: "getCurrentTime",
	    value: function getCurrentTime() {
	      return new Date(_Date$now() + this.offset);
	    }
	  }]);
	  return CurrentTime;
	}(Component);

	var findExports = {};
	var find$3 = {
	  get exports(){ return findExports; },
	  set exports(v){ findExports = v; },
	};

	var $$4 = _export;
	var $find = arrayIteration.find;

	var FIND = 'find';
	var SKIPS_HOLES$1 = true;

	
	if (FIND in []) Array(1)[FIND](function () { SKIPS_HOLES$1 = false; });

	
	
	$$4({ target: 'Array', proto: true, forced: SKIPS_HOLES$1 }, {
	  find: function find(callbackfn ) {
	    return $find(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$2 = entryVirtual$k;

	var find$2 = entryVirtual$2('Array').find;

	var isPrototypeOf$4 = objectIsPrototypeOf;
	var method$2 = find$2;

	var ArrayPrototype$2 = Array.prototype;

	var find$1 = function (it) {
	  var own = it.find;
	  return it === ArrayPrototype$2 || (isPrototypeOf$4(ArrayPrototype$2, it) && own === ArrayPrototype$2.find) ? method$2 : own;
	};

	var parent$3 = find$1;

	var find = parent$3;

	(function (module) {
		module.exports = find;
	} (find$3));

	var _findInstanceProperty = getDefaultExportFromCjs(findExports);

	var setExports = {};
	var set$2 = {
	  get exports(){ return setExports; },
	  set exports(v){ setExports = v; },
	};

	var internalMetadataExports = {};
	var internalMetadata = {
	  get exports(){ return internalMetadataExports; },
	  set exports(v){ internalMetadataExports = v; },
	};

	
	var fails$3 = fails$u;

	var arrayBufferNonExtensible = fails$3(function () {
	  if (typeof ArrayBuffer == 'function') {
	    var buffer = new ArrayBuffer(8);
	    
	    if (Object.isExtensible(buffer)) Object.defineProperty(buffer, 'a', { value: 8 });
	  }
	});

	var fails$2 = fails$u;
	var isObject$2 = isObject$g;
	var classof = classofRaw$2;
	var ARRAY_BUFFER_NON_EXTENSIBLE = arrayBufferNonExtensible;

	
	var $isExtensible = Object.isExtensible;
	var FAILS_ON_PRIMITIVES = fails$2(function () { $isExtensible(1); });

	
	
	var objectIsExtensible = (FAILS_ON_PRIMITIVES || ARRAY_BUFFER_NON_EXTENSIBLE) ? function isExtensible(it) {
	  if (!isObject$2(it)) return false;
	  if (ARRAY_BUFFER_NON_EXTENSIBLE && classof(it) == 'ArrayBuffer') return false;
	  return $isExtensible ? $isExtensible(it) : true;
	} : $isExtensible;

	var fails$1 = fails$u;

	var freezing = !fails$1(function () {
	  
	  return Object.isExtensible(Object.preventExtensions({}));
	});

	var $$3 = _export;
	var uncurryThis = functionUncurryThis;
	var hiddenKeys = hiddenKeys$6;
	var isObject$1 = isObject$g;
	var hasOwn = hasOwnProperty_1;
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
	  if (!hasOwn(it, METADATA)) {
	    
	    if (!isExtensible(it)) return 'F';
	    
	    if (!create) return 'E';
	    
	    setMetadata(it);
	  
	  } return it[METADATA].objectID;
	};

	var getWeakData = function (it, create) {
	  if (!hasOwn(it, METADATA)) {
	    
	    if (!isExtensible(it)) return true;
	    
	    if (!create) return false;
	    
	    setMetadata(it);
	  
	  } return it[METADATA].weakData;
	};

	
	var onFreeze = function (it) {
	  if (FREEZING && REQUIRED && isExtensible(it) && !hasOwn(it, METADATA)) setMetadata(it);
	  return it;
	};

	var enable = function () {
	  meta.enable = function () {  };
	  REQUIRED = true;
	  var getOwnPropertyNames = getOwnPropertyNamesModule.f;
	  var splice = uncurryThis([].splice);
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

	var bind$1 = functionBindContext;
	var call = functionCall;
	var anObject = anObject$b;
	var tryToString = tryToString$6;
	var isArrayIteratorMethod = isArrayIteratorMethod$2;
	var lengthOfArrayLike = lengthOfArrayLike$b;
	var isPrototypeOf$3 = objectIsPrototypeOf;
	var getIterator = getIterator$2;
	var getIteratorMethod = getIteratorMethod$9;
	var iteratorClose = iteratorClose$2;

	var $TypeError$1 = TypeError;

	var Result = function (stopped, result) {
	  this.stopped = stopped;
	  this.result = result;
	};

	var ResultPrototype = Result.prototype;

	var iterate$2 = function (iterable, unboundFunction, options) {
	  var that = options && options.that;
	  var AS_ENTRIES = !!(options && options.AS_ENTRIES);
	  var IS_RECORD = !!(options && options.IS_RECORD);
	  var IS_ITERATOR = !!(options && options.IS_ITERATOR);
	  var INTERRUPTED = !!(options && options.INTERRUPTED);
	  var fn = bind$1(unboundFunction, that);
	  var iterator, iterFn, index, length, result, next, step;

	  var stop = function (condition) {
	    if (iterator) iteratorClose(iterator, 'normal', condition);
	    return new Result(true, condition);
	  };

	  var callFn = function (value) {
	    if (AS_ENTRIES) {
	      anObject(value);
	      return INTERRUPTED ? fn(value[0], value[1], stop) : fn(value[0], value[1]);
	    } return INTERRUPTED ? fn(value, stop) : fn(value);
	  };

	  if (IS_RECORD) {
	    iterator = iterable.iterator;
	  } else if (IS_ITERATOR) {
	    iterator = iterable;
	  } else {
	    iterFn = getIteratorMethod(iterable);
	    if (!iterFn) throw $TypeError$1(tryToString(iterable) + ' is not iterable');
	    
	    if (isArrayIteratorMethod(iterFn)) {
	      for (index = 0, length = lengthOfArrayLike(iterable); length > index; index++) {
	        result = callFn(iterable[index]);
	        if (result && isPrototypeOf$3(ResultPrototype, result)) return result;
	      } return new Result(false);
	    }
	    iterator = getIterator(iterable, iterFn);
	  }

	  next = IS_RECORD ? iterable.next : iterator.next;
	  while (!(step = call(next, iterator)).done) {
	    try {
	      result = callFn(step.value);
	    } catch (error) {
	      iteratorClose(iterator, 'throw', error);
	    }
	    if (typeof result == 'object' && result && isPrototypeOf$3(ResultPrototype, result)) return result;
	  } return new Result(false);
	};

	var isPrototypeOf$2 = objectIsPrototypeOf;

	var $TypeError = TypeError;

	var anInstance$2 = function (it, Prototype) {
	  if (isPrototypeOf$2(Prototype, it)) return it;
	  throw $TypeError('Incorrect invocation');
	};

	var $$2 = _export;
	var global$1 = global$j;
	var InternalMetadataModule = internalMetadataExports;
	var fails = fails$u;
	var createNonEnumerableProperty = createNonEnumerableProperty$6;
	var iterate$1 = iterate$2;
	var anInstance$1 = anInstance$2;
	var isCallable = isCallable$i;
	var isObject = isObject$g;
	var setToStringTag = setToStringTag$6;
	var defineProperty = objectDefineProperty.f;
	var forEach = arrayIteration.forEach;
	var DESCRIPTORS$2 = descriptors;
	var InternalStateModule$1 = internalState;

	var setInternalState$1 = InternalStateModule$1.set;
	var internalStateGetterFor$1 = InternalStateModule$1.getterFor;

	var collection$1 = function (CONSTRUCTOR_NAME, wrapper, common) {
	  var IS_MAP = CONSTRUCTOR_NAME.indexOf('Map') !== -1;
	  var IS_WEAK = CONSTRUCTOR_NAME.indexOf('Weak') !== -1;
	  var ADDER = IS_MAP ? 'set' : 'add';
	  var NativeConstructor = global$1[CONSTRUCTOR_NAME];
	  var NativePrototype = NativeConstructor && NativeConstructor.prototype;
	  var exported = {};
	  var Constructor;

	  if (!DESCRIPTORS$2 || !isCallable(NativeConstructor)
	    || !(IS_WEAK || NativePrototype.forEach && !fails(function () { new NativeConstructor().entries().next(); }))
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

	var defineBuiltIn = defineBuiltIn$5;

	var defineBuiltIns$1 = function (target, src, options) {
	  for (var key in src) {
	    if (options && options.unsafe && target[key]) target[key] = src[key];
	    else defineBuiltIn(target, key, src[key], options);
	  } return target;
	};

	var getBuiltIn = getBuiltIn$c;
	var defineBuiltInAccessor$1 = defineBuiltInAccessor$3;
	var wellKnownSymbol = wellKnownSymbol$l;
	var DESCRIPTORS$1 = descriptors;

	var SPECIES = wellKnownSymbol('species');

	var setSpecies$1 = function (CONSTRUCTOR_NAME) {
	  var Constructor = getBuiltIn(CONSTRUCTOR_NAME);

	  if (DESCRIPTORS$1 && Constructor && !Constructor[SPECIES]) {
	    defineBuiltInAccessor$1(Constructor, SPECIES, {
	      configurable: true,
	      get: function () { return this; }
	    });
	  }
	};

	var create = objectCreate;
	var defineBuiltInAccessor = defineBuiltInAccessor$3;
	var defineBuiltIns = defineBuiltIns$1;
	var bind = functionBindContext;
	var anInstance = anInstance$2;
	var isNullOrUndefined = isNullOrUndefined$4;
	var iterate = iterate$2;
	var defineIterator = iteratorDefine;
	var createIterResultObject = createIterResultObject$3;
	var setSpecies = setSpecies$1;
	var DESCRIPTORS = descriptors;
	var fastKey = internalMetadataExports.fastKey;
	var InternalStateModule = internalState;

	var setInternalState = InternalStateModule.set;
	var internalStateGetterFor = InternalStateModule.getterFor;

	var collectionStrong$1 = {
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

	var collection = collection$1;
	var collectionStrong = collectionStrong$1;

	
	
	collection('Set', function (init) {
	  return function Set() { return init(this, arguments.length ? arguments[0] : undefined); };
	}, collectionStrong);

	var path = path$r;

	var set$1 = path.Set;

	var parent$2 = set$1;


	var set = parent$2;

	(function (module) {
		module.exports = set;
	} (set$2));

	var _Set = getDefaultExportFromCjs(setExports);

	var findIndexExports = {};
	var findIndex$3 = {
	  get exports(){ return findIndexExports; },
	  set exports(v){ findIndexExports = v; },
	};

	var $$1 = _export;
	var $findIndex = arrayIteration.findIndex;

	var FIND_INDEX = 'findIndex';
	var SKIPS_HOLES = true;

	
	if (FIND_INDEX in []) Array(1)[FIND_INDEX](function () { SKIPS_HOLES = false; });

	
	
	$$1({ target: 'Array', proto: true, forced: SKIPS_HOLES }, {
	  findIndex: function findIndex(callbackfn ) {
	    return $findIndex(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual$1 = entryVirtual$k;

	var findIndex$2 = entryVirtual$1('Array').findIndex;

	var isPrototypeOf$1 = objectIsPrototypeOf;
	var method$1 = findIndex$2;

	var ArrayPrototype$1 = Array.prototype;

	var findIndex$1 = function (it) {
	  var own = it.findIndex;
	  return it === ArrayPrototype$1 || (isPrototypeOf$1(ArrayPrototype$1, it) && own === ArrayPrototype$1.findIndex) ? method$1 : own;
	};

	var parent$1 = findIndex$1;

	var findIndex = parent$1;

	(function (module) {
		module.exports = findIndex;
	} (findIndex$3));

	var _findIndexInstanceProperty = getDefaultExportFromCjs(findIndexExports);

	function _createForOfIteratorHelper$5(o, allowArrayLike) { var it = typeof _Symbol !== "undefined" && _getIteratorMethod(o) || o["@@iterator"]; if (!it) { if (_Array$isArray$1(o) || (it = _unsupportedIterableToArray$5(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }
	function _unsupportedIterableToArray$5(o, minLen) { var _context5; if (!o) return; if (typeof o === "string") return _arrayLikeToArray$5(o, minLen); var n = _sliceInstanceProperty(_context5 = Object.prototype.toString.call(o)).call(_context5, 8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return _Array$from$1(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray$5(o, minLen); }
	function _arrayLikeToArray$5(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
	
	var EPSILON = 0.001; 

	
	function orderByStart(items) {
	  _sortInstanceProperty(items).call(items, function (a, b) {
	    return a.data.start - b.data.start;
	  });
	}

	
	function orderByEnd(items) {
	  _sortInstanceProperty(items).call(items, function (a, b) {
	    var aTime = 'end' in a.data ? a.data.end : a.data.start;
	    var bTime = 'end' in b.data ? b.data.end : b.data.start;
	    return aTime - bTime;
	  });
	}

	
	function stack(items, margin, force, shouldBailItemsRedrawFunction) {
	  var stackingResult = performStacking(items, margin.item, false, function (item) {
	    return item.stack && (force || item.top === null);
	  }, function (item) {
	    return item.stack;
	  }, function (item) {
	    return margin.axis;
	  }, shouldBailItemsRedrawFunction);

	  
	  return stackingResult === null;
	}

	
	function substack(items, margin, subgroup) {
	  var subgroupHeight = performStacking(items, margin.item, false, function (item) {
	    return item.stack;
	  }, function (item) {
	    return true;
	  }, function (item) {
	    return item.baseTop;
	  });
	  subgroup.height = subgroupHeight - subgroup.top + 0.5 * margin.item.vertical;
	}

	
	function nostack(items, margin, subgroups, isStackSubgroups) {
	  for (var _i = 0; _i < items.length; _i++) {
	    if (items[_i].data.subgroup == undefined) {
	      items[_i].top = margin.item.vertical;
	    } else if (items[_i].data.subgroup !== undefined && isStackSubgroups) {
	      var newTop = 0;
	      for (var subgroup in subgroups) {
	        if (subgroups.hasOwnProperty(subgroup)) {
	          if (subgroups[subgroup].visible == true && subgroups[subgroup].index < subgroups[items[_i].data.subgroup].index) {
	            newTop += subgroups[subgroup].height;
	            subgroups[items[_i].data.subgroup].top = newTop;
	          }
	        }
	      }
	      items[_i].top = newTop + 0.5 * margin.item.vertical;
	    }
	  }
	  if (!isStackSubgroups) {
	    stackSubgroups(items, margin, subgroups);
	  }
	}

	
	function stackSubgroups(items, margin, subgroups) {
	  var _context;
	  performStacking(_sortInstanceProperty(_context = _Object$values2(subgroups)).call(_context, function (a, b) {
	    if (a.index > b.index) return 1;
	    if (a.index < b.index) return -1;
	    return 0;
	  }), {
	    vertical: 0
	  }, true, function (item) {
	    return true;
	  }, function (item) {
	    return true;
	  }, function (item) {
	    return 0;
	  });
	  for (var _i2 = 0; _i2 < items.length; _i2++) {
	    if (items[_i2].data.subgroup !== undefined) {
	      items[_i2].top = subgroups[items[_i2].data.subgroup].top + 0.5 * margin.item.vertical;
	    }
	  }
	}

	
	function stackSubgroupsWithInnerStack(subgroupItems, margin, subgroups) {
	  var doSubStack = false;

	  
	  var subgroupOrder = [];
	  for (var subgroup in subgroups) {
	    if (subgroups[subgroup].hasOwnProperty("index")) {
	      subgroupOrder[subgroups[subgroup].index] = subgroup;
	    } else {
	      subgroupOrder.push(subgroup);
	    }
	  }
	  for (var j = 0; j < subgroupOrder.length; j++) {
	    subgroup = subgroupOrder[j];
	    if (subgroups.hasOwnProperty(subgroup)) {
	      doSubStack = doSubStack || subgroups[subgroup].stack;
	      subgroups[subgroup].top = 0;
	      for (var otherSubgroup in subgroups) {
	        if (subgroups[otherSubgroup].visible && subgroups[subgroup].index > subgroups[otherSubgroup].index) {
	          subgroups[subgroup].top += subgroups[otherSubgroup].height;
	        }
	      }
	      var items = subgroupItems[subgroup];
	      for (var _i3 = 0; _i3 < items.length; _i3++) {
	        if (items[_i3].data.subgroup !== undefined) {
	          items[_i3].top = subgroups[items[_i3].data.subgroup].top + 0.5 * margin.item.vertical;
	          if (subgroups[subgroup].stack) {
	            items[_i3].baseTop = items[_i3].top;
	          }
	        }
	      }
	      if (doSubStack && subgroups[subgroup].stack) {
	        substack(subgroupItems[subgroup], margin, subgroups[subgroup]);
	      }
	    }
	  }
	}

	
	function performStacking(items, margins, compareTimes, shouldStack, shouldOthersStack, getInitialHeight, shouldBail) {
	  
	  var getItemStart = function getItemStart(item) {
	    return item.start;
	  };
	  var getItemEnd = function getItemEnd(item) {
	    return item.end;
	  };
	  if (!compareTimes) {
	    
	    var rtl = !!(items[0] && items[0].options.rtl);
	    if (rtl) {
	      getItemStart = function getItemStart(item) {
	        return item.right;
	      };
	    } else {
	      getItemStart = function getItemStart(item) {
	        return item.left;
	      };
	    }
	    getItemEnd = function getItemEnd(item) {
	      return getItemStart(item) + item.width + margins.horizontal;
	    };
	  }
	  var itemsToPosition = [];
	  var itemsAlreadyPositioned = []; 

	  
	  
	  
	  
	  var previousStart = null;
	  var insertionIndex = 0;

	  
	  var _iterator = _createForOfIteratorHelper$5(items),
	    _step;
	  try {
	    var _loop2 = function _loop2() {
	      var item = _step.value;
	      if (shouldStack(item)) {
	        itemsToPosition.push(item);
	      } else {
	        if (shouldOthersStack(item)) {
	          var itemStart = getItemStart(item);

	          
	          
	          
	          
	          
	          
	          
	          
	          
	          
	          if (previousStart !== null && itemStart < previousStart - EPSILON) {
	            insertionIndex = 0;
	          }
	          previousStart = itemStart;
	          insertionIndex = findIndexFrom(itemsAlreadyPositioned, function (i) {
	            return getItemStart(i) - EPSILON > itemStart;
	          }, insertionIndex);
	          _spliceInstanceProperty(itemsAlreadyPositioned).call(itemsAlreadyPositioned, insertionIndex, 0, item);
	          insertionIndex++;
	        }
	      }
	    };
	    for (_iterator.s(); !(_step = _iterator.n()).done;) {
	      _loop2();
	    }

	    
	  } catch (err) {
	    _iterator.e(err);
	  } finally {
	    _iterator.f();
	  }
	  previousStart = null;
	  var previousEnd = null;
	  insertionIndex = 0;
	  var horizontalOverlapStartIndex = 0;
	  var horizontalOverlapEndIndex = 0;
	  var maxHeight = 0;
	  var _loop = function _loop() {
	    var _context2, _context3;
	    var item = itemsToPosition.shift();
	    item.top = getInitialHeight(item);
	    var itemStart = getItemStart(item);
	    var itemEnd = getItemEnd(item);
	    if (previousStart !== null && itemStart < previousStart - EPSILON) {
	      horizontalOverlapStartIndex = 0;
	      horizontalOverlapEndIndex = 0;
	      insertionIndex = 0;
	      previousEnd = null;
	    }
	    previousStart = itemStart;

	    
	    horizontalOverlapStartIndex = findIndexFrom(itemsAlreadyPositioned, function (i) {
	      return itemStart < getItemEnd(i) - EPSILON;
	    }, horizontalOverlapStartIndex);
	    
	    if (previousEnd === null || previousEnd < itemEnd - EPSILON) {
	      horizontalOverlapEndIndex = findIndexFrom(itemsAlreadyPositioned, function (i) {
	        return itemEnd < getItemStart(i) - EPSILON;
	      }, Math.max(horizontalOverlapStartIndex, horizontalOverlapEndIndex));
	    }
	    if (previousEnd !== null && previousEnd - EPSILON > itemEnd) {
	      horizontalOverlapEndIndex = findLastIndexBetween(itemsAlreadyPositioned, function (i) {
	        return itemEnd + EPSILON >= getItemStart(i);
	      }, horizontalOverlapStartIndex, horizontalOVerlapEndIndex) + 1;
	    }

	    
	    var horizontallyCollidingItems = _sortInstanceProperty(_context2 = _filterInstanceProperty(_context3 = _sliceInstanceProperty(itemsAlreadyPositioned).call(itemsAlreadyPositioned, horizontalOverlapStartIndex, horizontalOverlapEndIndex)).call(_context3, function (i) {
	      return itemStart < getItemEnd(i) - EPSILON && itemEnd - EPSILON > getItemStart(i);
	    })).call(_context2, function (a, b) {
	      return a.top - b.top;
	    });

	    
	    for (var i2 = 0; i2 < horizontallyCollidingItems.length; i2++) {
	      var otherItem = horizontallyCollidingItems[i2];
	      if (checkVerticalSpatialCollision(item, otherItem, margins)) {
	        item.top = otherItem.top + otherItem.height + margins.vertical;
	      }
	    }
	    if (shouldOthersStack(item)) {
	      
	      
	      
	      insertionIndex = findIndexFrom(itemsAlreadyPositioned, function (i) {
	        return getItemStart(i) - EPSILON > itemStart;
	      }, insertionIndex);
	      _spliceInstanceProperty(itemsAlreadyPositioned).call(itemsAlreadyPositioned, insertionIndex, 0, item);
	      insertionIndex++;
	    }

	    
	    var currentHeight = item.top + item.height;
	    if (currentHeight > maxHeight) {
	      maxHeight = currentHeight;
	    }
	    if (shouldBail && shouldBail()) {
	      return {
	        v: null
	      };
	    }
	  };
	  while (itemsToPosition.length > 0) {
	    var _ret = _loop();
	    if (_typeof(_ret) === "object") return _ret.v;
	  }
	  return maxHeight;
	}

	
	function checkVerticalSpatialCollision(a, b, margin) {
	  return a.top - margin.vertical + EPSILON < b.top + b.height && a.top + a.height + margin.vertical - EPSILON > b.top;
	}

	
	function findIndexFrom(arr, predicate, startIndex) {
	  var _context4;
	  if (!startIndex) {
	    startIndex = 0;
	  }
	  var matchIndex = _findIndexInstanceProperty(_context4 = _sliceInstanceProperty(arr).call(arr, startIndex)).call(_context4, predicate);
	  if (matchIndex === -1) {
	    return arr.length;
	  }
	  return matchIndex + startIndex;
	}

	
	function findLastIndexBetween(arr, predicate, startIndex, endIndex) {
	  if (!startIndex) {
	    startIndex = 0;
	  }
	  if (!endIndex) {
	    endIndex = arr.length;
	  }
	  for (i = endIndex - 1; i >= startIndex; i--) {
	    if (predicate(arr[i])) {
	      return i;
	    }
	  }
	  return startIndex - 1;
	}

	var stack$1 = Object.freeze({
		__proto__: null,
		nostack: nostack,
		orderByEnd: orderByEnd,
		orderByStart: orderByStart,
		stack: stack,
		stackSubgroups: stackSubgroups,
		stackSubgroupsWithInnerStack: stackSubgroupsWithInnerStack,
		substack: substack
	});

	var UNGROUPED$3 = '__ungrouped__'; 
	var BACKGROUND$2 = '__background__'; 

	var ReservedGroupIds$1 = {
	  UNGROUPED: UNGROUPED$3,
	  BACKGROUND: BACKGROUND$2
	};

	
	var Group = function () {
	  
	  function Group(groupId, data, itemSet) {
	    var _this = this;
	    _classCallCheck(this, Group);
	    this.groupId = groupId;
	    this.subgroups = {};
	    this.subgroupStack = {};
	    this.subgroupStackAll = false;
	    this.subgroupVisibility = {};
	    this.doInnerStack = false;
	    this.shouldBailStackItems = false;
	    this.subgroupIndex = 0;
	    this.subgroupOrderer = data && data.subgroupOrder;
	    this.itemSet = itemSet;
	    this.isVisible = null;
	    this.stackDirty = true; 

	    
	    
	    
	    
	    this._disposeCallbacks = [];
	    if (data && data.nestedGroups) {
	      this.nestedGroups = data.nestedGroups;
	      if (data.showNested == false) {
	        this.showNested = false;
	      } else {
	        this.showNested = true;
	      }
	    }
	    if (data && data.subgroupStack) {
	      if (typeof data.subgroupStack === "boolean") {
	        this.doInnerStack = data.subgroupStack;
	        this.subgroupStackAll = data.subgroupStack;
	      } else {
	        
	        
	        for (var key in data.subgroupStack) {
	          this.subgroupStack[key] = data.subgroupStack[key];
	          this.doInnerStack = this.doInnerStack || data.subgroupStack[key];
	        }
	      }
	    }
	    if (data && data.heightMode) {
	      this.heightMode = data.heightMode;
	    } else {
	      this.heightMode = itemSet.options.groupHeightMode;
	    }
	    this.nestedInGroup = null;
	    this.dom = {};
	    this.props = {
	      label: {
	        width: 0,
	        height: 0
	      }
	    };
	    this.className = null;
	    this.items = {}; 
	    this.visibleItems = []; 
	    this.itemsInRange = []; 
	    this.orderedItems = {
	      byStart: [],
	      byEnd: []
	    };
	    this.checkRangedItems = false; 

	    var handleCheckRangedItems = function handleCheckRangedItems() {
	      _this.checkRangedItems = true;
	    };
	    this.itemSet.body.emitter.on("checkRangedItems", handleCheckRangedItems);
	    this._disposeCallbacks.push(function () {
	      _this.itemSet.body.emitter.off("checkRangedItems", handleCheckRangedItems);
	    });
	    this._create();
	    this.setData(data);
	  }

	  
	  _createClass(Group, [{
	    key: "_create",
	    value: function _create() {
	      var label = document.createElement('div');
	      if (this.itemSet.options.groupEditable.order) {
	        label.className = 'vis-label draggable';
	      } else {
	        label.className = 'vis-label';
	      }
	      this.dom.label = label;
	      var inner = document.createElement('div');
	      inner.className = 'vis-inner';
	      label.appendChild(inner);
	      this.dom.inner = inner;
	      var foreground = document.createElement('div');
	      foreground.className = 'vis-group';
	      foreground['vis-group'] = this;
	      this.dom.foreground = foreground;
	      this.dom.background = document.createElement('div');
	      this.dom.background.className = 'vis-group';
	      this.dom.axis = document.createElement('div');
	      this.dom.axis.className = 'vis-group';

	      
	      
	      
	      this.dom.marker = document.createElement('div');
	      this.dom.marker.style.visibility = 'hidden';
	      this.dom.marker.style.position = 'absolute';
	      this.dom.marker.innerHTML = '';
	      this.dom.background.appendChild(this.dom.marker);
	    }

	    
	  }, {
	    key: "setData",
	    value: function setData(data) {
	      if (this.itemSet.groupTouchParams.isDragging) return;

	      
	      var content;
	      var templateFunction;
	      if (data && data.subgroupVisibility) {
	        for (var key in data.subgroupVisibility) {
	          this.subgroupVisibility[key] = data.subgroupVisibility[key];
	        }
	      }
	      if (this.itemSet.options && this.itemSet.options.groupTemplate) {
	        var _context;
	        templateFunction = _bindInstanceProperty(_context = this.itemSet.options.groupTemplate).call(_context, this);
	        content = templateFunction(data, this.dom.inner);
	      } else {
	        content = data && data.content;
	      }
	      if (content instanceof Element) {
	        while (this.dom.inner.firstChild) {
	          this.dom.inner.removeChild(this.dom.inner.firstChild);
	        }
	        this.dom.inner.appendChild(content);
	      } else if (content instanceof Object && content.isReactComponent) ; else if (content instanceof Object) {
	        templateFunction(data, this.dom.inner);
	      } else if (content !== undefined && content !== null) {
	        this.dom.inner.innerHTML = availableUtils.xss(content);
	      } else {
	        this.dom.inner.innerHTML = availableUtils.xss(this.groupId || ''); 
	      }

	      
	      this.dom.label.title = data && data.title || '';
	      if (!this.dom.inner.firstChild) {
	        availableUtils.addClassName(this.dom.inner, 'vis-hidden');
	      } else {
	        availableUtils.removeClassName(this.dom.inner, 'vis-hidden');
	      }
	      if (data && data.nestedGroups) {
	        if (!this.nestedGroups || this.nestedGroups != data.nestedGroups) {
	          this.nestedGroups = data.nestedGroups;
	        }
	        if (data.showNested !== undefined || this.showNested === undefined) {
	          if (data.showNested == false) {
	            this.showNested = false;
	          } else {
	            this.showNested = true;
	          }
	        }
	        availableUtils.addClassName(this.dom.label, 'vis-nesting-group');
	        if (this.showNested) {
	          availableUtils.removeClassName(this.dom.label, 'collapsed');
	          availableUtils.addClassName(this.dom.label, 'expanded');
	        } else {
	          availableUtils.removeClassName(this.dom.label, 'expanded');
	          availableUtils.addClassName(this.dom.label, 'collapsed');
	        }
	      } else if (this.nestedGroups) {
	        this.nestedGroups = null;
	        availableUtils.removeClassName(this.dom.label, 'collapsed');
	        availableUtils.removeClassName(this.dom.label, 'expanded');
	        availableUtils.removeClassName(this.dom.label, 'vis-nesting-group');
	      }
	      if (data && (data.treeLevel || data.nestedInGroup)) {
	        availableUtils.addClassName(this.dom.label, 'vis-nested-group');
	        if (data.treeLevel) {
	          availableUtils.addClassName(this.dom.label, 'vis-group-level-' + data.treeLevel);
	        } else {
	          
	          availableUtils.addClassName(this.dom.label, 'vis-group-level-unknown-but-gte1');
	        }
	      } else {
	        availableUtils.addClassName(this.dom.label, 'vis-group-level-0');
	      }

	      
	      var className = data && data.className || null;
	      if (className != this.className) {
	        if (this.className) {
	          availableUtils.removeClassName(this.dom.label, this.className);
	          availableUtils.removeClassName(this.dom.foreground, this.className);
	          availableUtils.removeClassName(this.dom.background, this.className);
	          availableUtils.removeClassName(this.dom.axis, this.className);
	        }
	        availableUtils.addClassName(this.dom.label, className);
	        availableUtils.addClassName(this.dom.foreground, className);
	        availableUtils.addClassName(this.dom.background, className);
	        availableUtils.addClassName(this.dom.axis, className);
	        this.className = className;
	      }

	      
	      if (this.style) {
	        availableUtils.removeCssText(this.dom.label, this.style);
	        this.style = null;
	      }
	      if (data && data.style) {
	        availableUtils.addCssText(this.dom.label, data.style);
	        this.style = data.style;
	      }
	    }

	    
	  }, {
	    key: "getLabelWidth",
	    value: function getLabelWidth() {
	      return this.props.label.width;
	    }

	    
	  }, {
	    key: "_didMarkerHeightChange",
	    value: function _didMarkerHeightChange() {
	      var markerHeight = this.dom.marker.clientHeight;
	      if (markerHeight != this.lastMarkerHeight) {
	        this.lastMarkerHeight = markerHeight;
	        var redrawQueue = {};
	        var redrawQueueLength = 0;
	        _forEachInstanceProperty(availableUtils).call(availableUtils, this.items, function (item, key) {
	          item.dirty = true;
	          if (item.displayed) {
	            var returnQueue = true;
	            redrawQueue[key] = item.redraw(returnQueue);
	            redrawQueueLength = redrawQueue[key].length;
	          }
	        });
	        var needRedraw = redrawQueueLength > 0;
	        if (needRedraw) {
	          var _loop = function _loop(i) {
	            _forEachInstanceProperty(availableUtils).call(availableUtils, redrawQueue, function (fns) {
	              fns[i]();
	            });
	          };
	          
	          for (var i = 0; i < redrawQueueLength; i++) {
	            _loop(i);
	          }
	        }
	        return true;
	      } else {
	        return false;
	      }
	    }

	    
	  }, {
	    key: "_calculateGroupSizeAndPosition",
	    value: function _calculateGroupSizeAndPosition() {
	      var _this$dom$foreground = this.dom.foreground,
	        offsetTop = _this$dom$foreground.offsetTop,
	        offsetLeft = _this$dom$foreground.offsetLeft,
	        offsetWidth = _this$dom$foreground.offsetWidth;
	      this.top = offsetTop;
	      this.right = offsetLeft;
	      this.width = offsetWidth;
	    }

	    
	  }, {
	    key: "_shouldBailItemsRedraw",
	    value: function _shouldBailItemsRedraw() {
	      var me = this;
	      var timeoutOptions = this.itemSet.options.onTimeout;
	      var bailOptions = {
	        relativeBailingTime: this.itemSet.itemsSettingTime,
	        bailTimeMs: timeoutOptions && timeoutOptions.timeoutMs,
	        userBailFunction: timeoutOptions && timeoutOptions.callback,
	        shouldBailStackItems: this.shouldBailStackItems
	      };
	      var bail = null;
	      if (!this.itemSet.initialDrawDone) {
	        if (bailOptions.shouldBailStackItems) {
	          return true;
	        }
	        if (Math.abs(_Date$now() - new Date(bailOptions.relativeBailingTime)) > bailOptions.bailTimeMs) {
	          if (bailOptions.userBailFunction && this.itemSet.userContinueNotBail == null) {
	            bailOptions.userBailFunction(function (didUserContinue) {
	              me.itemSet.userContinueNotBail = didUserContinue;
	              bail = !didUserContinue;
	            });
	          } else if (me.itemSet.userContinueNotBail == false) {
	            bail = true;
	          } else {
	            bail = false;
	          }
	        }
	      }
	      return bail;
	    }

	    
	  }, {
	    key: "_redrawItems",
	    value: function _redrawItems(forceRestack, lastIsVisible, margin, range) {
	      var _this2 = this;
	      var restack = forceRestack || this.stackDirty || this.isVisible && !lastIsVisible;

	      
	      if (restack) {
	        var _context2, _context3, _context4, _context5, _context6, _context7;
	        var orderedItems = {
	          byEnd: _filterInstanceProperty(_context2 = this.orderedItems.byEnd).call(_context2, function (item) {
	            return !item.isCluster;
	          }),
	          byStart: _filterInstanceProperty(_context3 = this.orderedItems.byStart).call(_context3, function (item) {
	            return !item.isCluster;
	          })
	        };
	        var orderedClusters = {
	          byEnd: _toConsumableArray(new _Set(_filterInstanceProperty(_context4 = _mapInstanceProperty(_context5 = this.orderedItems.byEnd).call(_context5, function (item) {
	            return item.cluster;
	          })).call(_context4, function (item) {
	            return !!item;
	          }))),
	          byStart: _toConsumableArray(new _Set(_filterInstanceProperty(_context6 = _mapInstanceProperty(_context7 = this.orderedItems.byStart).call(_context7, function (item) {
	            return item.cluster;
	          })).call(_context6, function (item) {
	            return !!item;
	          })))
	        };

	        
	        var getVisibleItems = function getVisibleItems() {
	          var _context8, _context9, _context10;
	          var visibleItems = _this2._updateItemsInRange(orderedItems, _filterInstanceProperty(_context8 = _this2.visibleItems).call(_context8, function (item) {
	            return !item.isCluster;
	          }), range);
	          var visibleClusters = _this2._updateClustersInRange(orderedClusters, _filterInstanceProperty(_context9 = _this2.visibleItems).call(_context9, function (item) {
	            return item.isCluster;
	          }), range);
	          return _concatInstanceProperty(_context10 = []).call(_context10, _toConsumableArray(visibleItems), _toConsumableArray(visibleClusters));
	        };

	        
	        var getVisibleItemsGroupedBySubgroup = function getVisibleItemsGroupedBySubgroup(orderFn) {
	          var visibleSubgroupsItems = {};
	          var _loop2 = function _loop2(subgroup) {
	            var _context11;
	            var items = _filterInstanceProperty(_context11 = _this2.visibleItems).call(_context11, function (item) {
	              return item.data.subgroup === subgroup;
	            });
	            visibleSubgroupsItems[subgroup] = orderFn ? _sortInstanceProperty(items).call(items, function (a, b) {
	              return orderFn(a.data, b.data);
	            }) : items;
	          };
	          for (var subgroup in _this2.subgroups) {
	            _loop2(subgroup);
	          }
	          return visibleSubgroupsItems;
	        };
	        if (typeof this.itemSet.options.order === 'function') {
	          
	          
	          var me = this;
	          if (this.doInnerStack && this.itemSet.options.stackSubgroups) {
	            
	            var visibleSubgroupsItems = getVisibleItemsGroupedBySubgroup(this.itemSet.options.order);
	            stackSubgroupsWithInnerStack(visibleSubgroupsItems, margin, this.subgroups);
	            this.visibleItems = getVisibleItems();
	            this._updateSubGroupHeights(margin);
	          } else {
	            var _context12, _context13, _context14, _context15;
	            this.visibleItems = getVisibleItems();
	            this._updateSubGroupHeights(margin);
	            
	            
	            var customOrderedItems = _sortInstanceProperty(_context12 = _filterInstanceProperty(_context13 = _sliceInstanceProperty(_context14 = this.visibleItems).call(_context14)).call(_context13, function (item) {
	              return item.isCluster || !item.isCluster && !item.cluster;
	            })).call(_context12, function (a, b) {
	              return me.itemSet.options.order(a.data, b.data);
	            });
	            this.shouldBailStackItems = stack(customOrderedItems, margin, true, _bindInstanceProperty(_context15 = this._shouldBailItemsRedraw).call(_context15, this));
	          }
	        } else {
	          
	          this.visibleItems = getVisibleItems();
	          this._updateSubGroupHeights(margin);
	          if (this.itemSet.options.stack) {
	            if (this.doInnerStack && this.itemSet.options.stackSubgroups) {
	              var _visibleSubgroupsItems = getVisibleItemsGroupedBySubgroup();
	              stackSubgroupsWithInnerStack(_visibleSubgroupsItems, margin, this.subgroups);
	            } else {
	              var _context16;
	              
	              this.shouldBailStackItems = stack(this.visibleItems, margin, true, _bindInstanceProperty(_context16 = this._shouldBailItemsRedraw).call(_context16, this));
	            }
	          } else {
	            
	            nostack(this.visibleItems, margin, this.subgroups, this.itemSet.options.stackSubgroups);
	          }
	        }
	        for (var i = 0; i < this.visibleItems.length; i++) {
	          this.visibleItems[i].repositionX();
	          if (this.subgroupVisibility[this.visibleItems[i].data.subgroup] !== undefined) {
	            if (!this.subgroupVisibility[this.visibleItems[i].data.subgroup]) {
	              this.visibleItems[i].hide();
	            }
	          }
	        }
	        if (this.itemSet.options.cluster) {
	          _forEachInstanceProperty(availableUtils).call(availableUtils, this.items, function (item) {
	            if (item.cluster && item.displayed) {
	              item.hide();
	            }
	          });
	        }
	        if (this.shouldBailStackItems) {
	          this.itemSet.body.emitter.emit('destroyTimeline');
	        }
	        this.stackDirty = false;
	      }
	    }

	    
	  }, {
	    key: "_didResize",
	    value: function _didResize(resized, height) {
	      resized = availableUtils.updateProperty(this, 'height', height) || resized;
	      
	      var labelWidth = this.dom.inner.clientWidth;
	      var labelHeight = this.dom.inner.clientHeight;
	      resized = availableUtils.updateProperty(this.props.label, 'width', labelWidth) || resized;
	      resized = availableUtils.updateProperty(this.props.label, 'height', labelHeight) || resized;
	      return resized;
	    }

	    
	  }, {
	    key: "_applyGroupHeight",
	    value: function _applyGroupHeight(height) {
	      this.dom.background.style.height = "".concat(height, "px");
	      this.dom.foreground.style.height = "".concat(height, "px");
	      this.dom.label.style.height = "".concat(height, "px");
	    }

	    
	  }, {
	    key: "_updateItemsVerticalPosition",
	    value: function _updateItemsVerticalPosition(margin) {
	      for (var i = 0, ii = this.visibleItems.length; i < ii; i++) {
	        var item = this.visibleItems[i];
	        item.repositionY(margin);
	        if (!this.isVisible && this.groupId != ReservedGroupIds$1.BACKGROUND) {
	          if (item.displayed) item.hide();
	        }
	      }
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw(range, margin, forceRestack, returnQueue) {
	      var _this3 = this,
	        _context17,
	        _context18,
	        _context21,
	        _context23,
	        _context27;
	      var resized = false;
	      var lastIsVisible = this.isVisible;
	      var height;
	      var queue = [function () {
	        forceRestack = _this3._didMarkerHeightChange.call(_this3) || forceRestack;
	      },
	      
	      _bindInstanceProperty(_context17 = this._updateSubGroupHeights).call(_context17, this, margin),
	      
	      _bindInstanceProperty(_context18 = this._calculateGroupSizeAndPosition).call(_context18, this), function () {
	        var _context19;
	        _this3.isVisible = _bindInstanceProperty(_context19 = _this3._isGroupVisible).call(_context19, _this3)(range, margin);
	      }, function () {
	        var _context20;
	        _bindInstanceProperty(_context20 = _this3._redrawItems).call(_context20, _this3)(forceRestack, lastIsVisible, margin, range);
	      },
	      
	      _bindInstanceProperty(_context21 = this._updateSubgroupsSizes).call(_context21, this), function () {
	        var _context22;
	        height = _bindInstanceProperty(_context22 = _this3._calculateHeight).call(_context22, _this3)(margin);
	      },
	      
	      _bindInstanceProperty(_context23 = this._calculateGroupSizeAndPosition).call(_context23, this), function () {
	        var _context24;
	        resized = _bindInstanceProperty(_context24 = _this3._didResize).call(_context24, _this3)(resized, height);
	      }, function () {
	        var _context25;
	        _bindInstanceProperty(_context25 = _this3._applyGroupHeight).call(_context25, _this3)(height);
	      }, function () {
	        var _context26;
	        _bindInstanceProperty(_context26 = _this3._updateItemsVerticalPosition).call(_context26, _this3)(margin);
	      }, _bindInstanceProperty(_context27 = function _context27() {
	        if (!_this3.isVisible && _this3.height) {
	          resized = false;
	        }
	        return resized;
	      }).call(_context27, this)];
	      if (returnQueue) {
	        return queue;
	      } else {
	        var result;
	        _forEachInstanceProperty(queue).call(queue, function (fn) {
	          result = fn();
	        });
	        return result;
	      }
	    }

	    
	  }, {
	    key: "_updateSubGroupHeights",
	    value: function _updateSubGroupHeights(margin) {
	      var _this4 = this;
	      if (_Object$keys(this.subgroups).length > 0) {
	        var me = this;
	        this._resetSubgroups();
	        _forEachInstanceProperty(availableUtils).call(availableUtils, this.visibleItems, function (item) {
	          if (item.data.subgroup !== undefined) {
	            me.subgroups[item.data.subgroup].height = Math.max(me.subgroups[item.data.subgroup].height, item.height + margin.item.vertical);
	            me.subgroups[item.data.subgroup].visible = typeof _this4.subgroupVisibility[item.data.subgroup] === 'undefined' ? true : Boolean(_this4.subgroupVisibility[item.data.subgroup]);
	          }
	        });
	      }
	    }

	    
	  }, {
	    key: "_isGroupVisible",
	    value: function _isGroupVisible(range, margin) {
	      return this.top <= range.body.domProps.centerContainer.height - range.body.domProps.scrollTop + margin.axis && this.top + this.height + margin.axis >= -range.body.domProps.scrollTop;
	    }

	    
	  }, {
	    key: "_calculateHeight",
	    value: function _calculateHeight(margin) {
	      
	      var height;
	      var items;
	      if (this.heightMode === 'fixed') {
	        items = availableUtils.toArray(this.items);
	      } else {
	        
	        items = this.visibleItems;
	      }
	      if (items.length > 0) {
	        var min = items[0].top;
	        var max = items[0].top + items[0].height;
	        _forEachInstanceProperty(availableUtils).call(availableUtils, items, function (item) {
	          min = Math.min(min, item.top);
	          max = Math.max(max, item.top + item.height);
	        });
	        if (min > margin.axis) {
	          
	          var offset = min - margin.axis;
	          max -= offset;
	          _forEachInstanceProperty(availableUtils).call(availableUtils, items, function (item) {
	            item.top -= offset;
	          });
	        }
	        height = Math.ceil(max + margin.item.vertical / 2);
	        if (this.heightMode !== "fitItems") {
	          height = Math.max(height, this.props.label.height);
	        }
	      } else {
	        height = this.props.label.height;
	      }
	      return height;
	    }

	    
	  }, {
	    key: "show",
	    value: function show() {
	      if (!this.dom.label.parentNode) {
	        this.itemSet.dom.labelSet.appendChild(this.dom.label);
	      }
	      if (!this.dom.foreground.parentNode) {
	        this.itemSet.dom.foreground.appendChild(this.dom.foreground);
	      }
	      if (!this.dom.background.parentNode) {
	        this.itemSet.dom.background.appendChild(this.dom.background);
	      }
	      if (!this.dom.axis.parentNode) {
	        this.itemSet.dom.axis.appendChild(this.dom.axis);
	      }
	    }

	    
	  }, {
	    key: "hide",
	    value: function hide() {
	      var label = this.dom.label;
	      if (label.parentNode) {
	        label.parentNode.removeChild(label);
	      }
	      var foreground = this.dom.foreground;
	      if (foreground.parentNode) {
	        foreground.parentNode.removeChild(foreground);
	      }
	      var background = this.dom.background;
	      if (background.parentNode) {
	        background.parentNode.removeChild(background);
	      }
	      var axis = this.dom.axis;
	      if (axis.parentNode) {
	        axis.parentNode.removeChild(axis);
	      }
	    }

	    
	  }, {
	    key: "add",
	    value: function add(item) {
	      var _context28;
	      this.items[item.id] = item;
	      item.setParent(this);
	      this.stackDirty = true;
	      
	      if (item.data.subgroup !== undefined) {
	        this._addToSubgroup(item);
	        this.orderSubgroups();
	      }
	      if (!_includesInstanceProperty(_context28 = this.visibleItems).call(_context28, item)) {
	        var range = this.itemSet.body.range; 
	        this._checkIfVisible(item, this.visibleItems, range);
	      }
	    }

	    
	  }, {
	    key: "_addToSubgroup",
	    value: function _addToSubgroup(item) {
	      var subgroupId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : item.data.subgroup;
	      if (subgroupId != undefined && this.subgroups[subgroupId] === undefined) {
	        this.subgroups[subgroupId] = {
	          height: 0,
	          top: 0,
	          start: item.data.start,
	          end: item.data.end || item.data.start,
	          visible: false,
	          index: this.subgroupIndex,
	          items: [],
	          stack: this.subgroupStackAll || this.subgroupStack[subgroupId] || false
	        };
	        this.subgroupIndex++;
	      }
	      if (new Date(item.data.start) < new Date(this.subgroups[subgroupId].start)) {
	        this.subgroups[subgroupId].start = item.data.start;
	      }
	      var itemEnd = item.data.end || item.data.start;
	      if (new Date(itemEnd) > new Date(this.subgroups[subgroupId].end)) {
	        this.subgroups[subgroupId].end = itemEnd;
	      }
	      this.subgroups[subgroupId].items.push(item);
	    }

	    
	  }, {
	    key: "_updateSubgroupsSizes",
	    value: function _updateSubgroupsSizes() {
	      var me = this;
	      if (me.subgroups) {
	        var _loop3 = function _loop3() {
	          var _context29;
	          var initialEnd = me.subgroups[subgroup].items[0].data.end || me.subgroups[subgroup].items[0].data.start;
	          var newStart = me.subgroups[subgroup].items[0].data.start;
	          var newEnd = initialEnd - 1;
	          _forEachInstanceProperty(_context29 = me.subgroups[subgroup].items).call(_context29, function (item) {
	            if (new Date(item.data.start) < new Date(newStart)) {
	              newStart = item.data.start;
	            }
	            var itemEnd = item.data.end || item.data.start;
	            if (new Date(itemEnd) > new Date(newEnd)) {
	              newEnd = itemEnd;
	            }
	          });
	          me.subgroups[subgroup].start = newStart;
	          me.subgroups[subgroup].end = new Date(newEnd - 1); 
	        };
	        for (var subgroup in me.subgroups) {
	          _loop3();
	        }
	      }
	    }

	    
	  }, {
	    key: "orderSubgroups",
	    value: function orderSubgroups() {
	      if (this.subgroupOrderer !== undefined) {
	        var sortArray = [];
	        if (typeof this.subgroupOrderer == 'string') {
	          for (var subgroup in this.subgroups) {
	            sortArray.push({
	              subgroup: subgroup,
	              sortField: this.subgroups[subgroup].items[0].data[this.subgroupOrderer]
	            });
	          }
	          _sortInstanceProperty(sortArray).call(sortArray, function (a, b) {
	            return a.sortField - b.sortField;
	          });
	        } else if (typeof this.subgroupOrderer == 'function') {
	          for (var _subgroup in this.subgroups) {
	            sortArray.push(this.subgroups[_subgroup].items[0].data);
	          }
	          _sortInstanceProperty(sortArray).call(sortArray, this.subgroupOrderer);
	        }
	        if (sortArray.length > 0) {
	          for (var i = 0; i < sortArray.length; i++) {
	            this.subgroups[sortArray[i].subgroup].index = i;
	          }
	        }
	      }
	    }

	    
	  }, {
	    key: "_resetSubgroups",
	    value: function _resetSubgroups() {
	      for (var subgroup in this.subgroups) {
	        if (this.subgroups.hasOwnProperty(subgroup)) {
	          this.subgroups[subgroup].visible = false;
	          this.subgroups[subgroup].height = 0;
	        }
	      }
	    }

	    
	  }, {
	    key: "remove",
	    value: function remove(item) {
	      var _context30, _context31;
	      delete this.items[item.id];
	      item.setParent(null);
	      this.stackDirty = true;

	      
	      var index = _indexOfInstanceProperty(_context30 = this.visibleItems).call(_context30, item);
	      if (index != -1) _spliceInstanceProperty(_context31 = this.visibleItems).call(_context31, index, 1);
	      if (item.data.subgroup !== undefined) {
	        this._removeFromSubgroup(item);
	        this.orderSubgroups();
	      }
	    }

	    
	  }, {
	    key: "_removeFromSubgroup",
	    value: function _removeFromSubgroup(item) {
	      var subgroupId = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : item.data.subgroup;
	      if (subgroupId != undefined) {
	        var subgroup = this.subgroups[subgroupId];
	        if (subgroup) {
	          var _context32;
	          var itemIndex = _indexOfInstanceProperty(_context32 = subgroup.items).call(_context32, item);
	          
	          if (itemIndex >= 0) {
	            var _context33;
	            _spliceInstanceProperty(_context33 = subgroup.items).call(_context33, itemIndex, 1);
	            if (!subgroup.items.length) {
	              delete this.subgroups[subgroupId];
	            } else {
	              this._updateSubgroupsSizes();
	            }
	          }
	        }
	      }
	    }

	    
	  }, {
	    key: "removeFromDataSet",
	    value: function removeFromDataSet(item) {
	      this.itemSet.removeItem(item.id);
	    }

	    
	  }, {
	    key: "order",
	    value: function order() {
	      var array = availableUtils.toArray(this.items);
	      var startArray = [];
	      var endArray = [];
	      for (var i = 0; i < array.length; i++) {
	        if (array[i].data.end !== undefined) {
	          endArray.push(array[i]);
	        }
	        startArray.push(array[i]);
	      }
	      this.orderedItems = {
	        byStart: startArray,
	        byEnd: endArray
	      };
	      orderByStart(this.orderedItems.byStart);
	      orderByEnd(this.orderedItems.byEnd);
	    }

	    
	  }, {
	    key: "_updateItemsInRange",
	    value: function _updateItemsInRange(orderedItems, oldVisibleItems, range) {
	      var visibleItems = [];
	      var visibleItemsLookup = {}; 

	      if (!this.isVisible && this.groupId != ReservedGroupIds$1.BACKGROUND) {
	        for (var i = 0; i < oldVisibleItems.length; i++) {
	          var item = oldVisibleItems[i];
	          if (item.displayed) item.hide();
	        }
	        return visibleItems;
	      }
	      var interval = (range.end - range.start) / 4;
	      var lowerBound = range.start - interval;
	      var upperBound = range.end + interval;

	      
	      var startSearchFunction = function startSearchFunction(value) {
	        if (value < lowerBound) {
	          return -1;
	        } else if (value <= upperBound) {
	          return 0;
	        } else {
	          return 1;
	        }
	      };

	      
	      var endSearchFunction = function endSearchFunction(data) {
	        var start = data.start,
	          end = data.end;
	        if (end < lowerBound) {
	          return -1;
	        } else if (start <= upperBound) {
	          return 0;
	        } else {
	          return 1;
	        }
	      };

	      
	      
	      
	      if (oldVisibleItems.length > 0) {
	        for (var _i = 0; _i < oldVisibleItems.length; _i++) {
	          this._checkIfVisibleWithReference(oldVisibleItems[_i], visibleItems, visibleItemsLookup, range);
	        }
	      }

	      
	      var initialPosByStart = availableUtils.binarySearchCustom(orderedItems.byStart, startSearchFunction, 'data', 'start');

	      
	      this._traceVisible(initialPosByStart, orderedItems.byStart, visibleItems, visibleItemsLookup, function (item) {
	        return item.data.start < lowerBound || item.data.start > upperBound;
	      });

	      
	      
	      if (this.checkRangedItems == true) {
	        this.checkRangedItems = false;
	        for (var _i2 = 0; _i2 < orderedItems.byEnd.length; _i2++) {
	          this._checkIfVisibleWithReference(orderedItems.byEnd[_i2], visibleItems, visibleItemsLookup, range);
	        }
	      } else {
	        
	        var initialPosByEnd = availableUtils.binarySearchCustom(orderedItems.byEnd, endSearchFunction, 'data');

	        
	        this._traceVisible(initialPosByEnd, orderedItems.byEnd, visibleItems, visibleItemsLookup, function (item) {
	          return item.data.end < lowerBound || item.data.start > upperBound;
	        });
	      }
	      var redrawQueue = {};
	      var redrawQueueLength = 0;
	      for (var _i3 = 0; _i3 < visibleItems.length; _i3++) {
	        var _item = visibleItems[_i3];
	        if (!_item.displayed) {
	          var returnQueue = true;
	          redrawQueue[_i3] = _item.redraw(returnQueue);
	          redrawQueueLength = redrawQueue[_i3].length;
	        }
	      }
	      var needRedraw = redrawQueueLength > 0;
	      if (needRedraw) {
	        var _loop4 = function _loop4(j) {
	          _forEachInstanceProperty(availableUtils).call(availableUtils, redrawQueue, function (fns) {
	            fns[j]();
	          });
	        };
	        
	        for (var j = 0; j < redrawQueueLength; j++) {
	          _loop4(j);
	        }
	      }
	      for (var _i4 = 0; _i4 < visibleItems.length; _i4++) {
	        visibleItems[_i4].repositionX();
	      }
	      return visibleItems;
	    }

	    
	  }, {
	    key: "_traceVisible",
	    value: function _traceVisible(initialPos, items, visibleItems, visibleItemsLookup, breakCondition) {
	      if (initialPos != -1) {
	        for (var i = initialPos; i >= 0; i--) {
	          var item = items[i];
	          if (breakCondition(item)) {
	            break;
	          } else {
	            if (!(item.isCluster && !item.hasItems()) && !item.cluster) {
	              if (visibleItemsLookup[item.id] === undefined) {
	                visibleItemsLookup[item.id] = true;
	                visibleItems.push(item);
	              }
	            }
	          }
	        }
	        for (var _i5 = initialPos + 1; _i5 < items.length; _i5++) {
	          var _item2 = items[_i5];
	          if (breakCondition(_item2)) {
	            break;
	          } else {
	            if (!(_item2.isCluster && !_item2.hasItems()) && !_item2.cluster) {
	              if (visibleItemsLookup[_item2.id] === undefined) {
	                visibleItemsLookup[_item2.id] = true;
	                visibleItems.push(_item2);
	              }
	            }
	          }
	        }
	      }
	    }

	    
	  }, {
	    key: "_checkIfVisible",
	    value: function _checkIfVisible(item, visibleItems, range) {
	      if (item.isVisible(range)) {
	        if (!item.displayed) item.show();
	        
	        item.repositionX();
	        visibleItems.push(item);
	      } else {
	        if (item.displayed) item.hide();
	      }
	    }

	    
	  }, {
	    key: "_checkIfVisibleWithReference",
	    value: function _checkIfVisibleWithReference(item, visibleItems, visibleItemsLookup, range) {
	      if (item.isVisible(range)) {
	        if (visibleItemsLookup[item.id] === undefined) {
	          visibleItemsLookup[item.id] = true;
	          visibleItems.push(item);
	        }
	      } else {
	        if (item.displayed) item.hide();
	      }
	    }

	    
	  }, {
	    key: "_updateClustersInRange",
	    value: function _updateClustersInRange(orderedClusters, oldVisibleClusters, range) {
	      
	      var visibleClusters = [];
	      var visibleClustersLookup = {}; 

	      if (oldVisibleClusters.length > 0) {
	        for (var i = 0; i < oldVisibleClusters.length; i++) {
	          this._checkIfVisibleWithReference(oldVisibleClusters[i], visibleClusters, visibleClustersLookup, range);
	        }
	      }
	      for (var _i6 = 0; _i6 < orderedClusters.byStart.length; _i6++) {
	        this._checkIfVisibleWithReference(orderedClusters.byStart[_i6], visibleClusters, visibleClustersLookup, range);
	      }
	      for (var _i7 = 0; _i7 < orderedClusters.byEnd.length; _i7++) {
	        this._checkIfVisibleWithReference(orderedClusters.byEnd[_i7], visibleClusters, visibleClustersLookup, range);
	      }
	      var redrawQueue = {};
	      var redrawQueueLength = 0;
	      for (var _i8 = 0; _i8 < visibleClusters.length; _i8++) {
	        var item = visibleClusters[_i8];
	        if (!item.displayed) {
	          var returnQueue = true;
	          redrawQueue[_i8] = item.redraw(returnQueue);
	          redrawQueueLength = redrawQueue[_i8].length;
	        }
	      }
	      var needRedraw = redrawQueueLength > 0;
	      if (needRedraw) {
	        
	        for (var j = 0; j < redrawQueueLength; j++) {
	          _forEachInstanceProperty(availableUtils).call(availableUtils, redrawQueue, function (fns) {
	            fns[j]();
	          });
	        }
	      }
	      for (var _i9 = 0; _i9 < visibleClusters.length; _i9++) {
	        visibleClusters[_i9].repositionX();
	      }
	      return visibleClusters;
	    }

	    
	  }, {
	    key: "changeSubgroup",
	    value: function changeSubgroup(item, oldSubgroup, newSubgroup) {
	      this._removeFromSubgroup(item, oldSubgroup);
	      this._addToSubgroup(item, newSubgroup);
	      this.orderSubgroups();
	    }

	    
	  }, {
	    key: "dispose",
	    value: function dispose() {
	      this.hide();
	      var disposeCallback;
	      while (disposeCallback = this._disposeCallbacks.pop()) {
	        disposeCallback();
	      }
	    }
	  }]);
	  return Group;
	}();

	function _createSuper$8(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$8(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$8() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	
	var BackgroundGroup = function (_Group) {
	  _inherits(BackgroundGroup, _Group);
	  var _super = _createSuper$8(BackgroundGroup);
	  
	  function BackgroundGroup(groupId, data, itemSet) {
	    var _this;
	    _classCallCheck(this, BackgroundGroup);
	    _this = _super.call(this, groupId, data, itemSet);
	    

	    _this.width = 0;
	    _this.height = 0;
	    _this.top = 0;
	    _this.left = 0;
	    return _this;
	  }

	  
	  _createClass(BackgroundGroup, [{
	    key: "redraw",
	    value: function redraw(range, margin, forceRestack) {
	      
	      var resized = false;
	      this.visibleItems = this._updateItemsInRange(this.orderedItems, this.visibleItems, range);

	      
	      this.width = this.dom.background.offsetWidth;

	      
	      this.dom.background.style.height = '0';

	      
	      for (var i = 0, ii = this.visibleItems.length; i < ii; i++) {
	        var item = this.visibleItems[i];
	        item.repositionY(margin);
	      }
	      return resized;
	    }

	    
	  }, {
	    key: "show",
	    value: function show() {
	      if (!this.dom.background.parentNode) {
	        this.itemSet.dom.background.appendChild(this.dom.background);
	      }
	    }
	  }]);
	  return BackgroundGroup;
	}(Group);

	function _createForOfIteratorHelper$4(o, allowArrayLike) { var it = typeof _Symbol !== "undefined" && _getIteratorMethod(o) || o["@@iterator"]; if (!it) { if (_Array$isArray$1(o) || (it = _unsupportedIterableToArray$4(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }
	function _unsupportedIterableToArray$4(o, minLen) { var _context8; if (!o) return; if (typeof o === "string") return _arrayLikeToArray$4(o, minLen); var n = _sliceInstanceProperty(_context8 = Object.prototype.toString.call(o)).call(_context8, 8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return _Array$from$1(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray$4(o, minLen); }
	function _arrayLikeToArray$4(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }

	
	var Item = function () {
	  
	  function Item(data, conversion, options) {
	    var _context,
	      _this = this;
	    _classCallCheck(this, Item);
	    this.id = null;
	    this.parent = null;
	    this.data = data;
	    this.dom = null;
	    this.conversion = conversion || {};
	    this.defaultOptions = {
	      locales: locales,
	      locale: 'en'
	    };
	    this.options = availableUtils.extend({}, this.defaultOptions, options);
	    this.options.locales = availableUtils.extend({}, locales, this.options.locales);
	    var defaultLocales = this.defaultOptions.locales[this.defaultOptions.locale];
	    _forEachInstanceProperty(_context = _Object$keys(this.options.locales)).call(_context, function (locale) {
	      _this.options.locales[locale] = availableUtils.extend({}, defaultLocales, _this.options.locales[locale]);
	    });
	    this.selected = false;
	    this.displayed = false;
	    this.groupShowing = true;
	    this.selectable = options && options.selectable || false;
	    this.dirty = true;
	    this.top = null;
	    this.right = null;
	    this.left = null;
	    this.width = null;
	    this.height = null;
	    this.setSelectability(data);
	    this.editable = null;
	    this._updateEditStatus();
	  }

	  
	  _createClass(Item, [{
	    key: "select",
	    value: function select() {
	      if (this.selectable) {
	        this.selected = true;
	        this.dirty = true;
	        if (this.displayed) this.redraw();
	      }
	    }

	    
	  }, {
	    key: "unselect",
	    value: function unselect() {
	      this.selected = false;
	      this.dirty = true;
	      if (this.displayed) this.redraw();
	    }

	    
	  }, {
	    key: "setData",
	    value: function setData(data) {
	      var groupChanged = data.group != undefined && this.data.group != data.group;
	      if (groupChanged && this.parent != null) {
	        this.parent.itemSet._moveToGroup(this, data.group);
	      }
	      this.setSelectability(data);
	      if (this.parent) {
	        this.parent.stackDirty = true;
	      }
	      var subGroupChanged = data.subgroup != undefined && this.data.subgroup != data.subgroup;
	      if (subGroupChanged && this.parent != null) {
	        this.parent.changeSubgroup(this, this.data.subgroup, data.subgroup);
	      }
	      this.data = data;
	      this._updateEditStatus();
	      this.dirty = true;
	      if (this.displayed) this.redraw();
	    }

	    
	  }, {
	    key: "setSelectability",
	    value: function setSelectability(data) {
	      if (data) {
	        this.selectable = typeof data.selectable === 'undefined' ? true : Boolean(data.selectable);
	      }
	    }

	    
	  }, {
	    key: "setParent",
	    value: function setParent(parent) {
	      if (this.displayed) {
	        this.hide();
	        this.parent = parent;
	        if (this.parent) {
	          this.show();
	        }
	      } else {
	        this.parent = parent;
	      }
	    }

	    
	  }, {
	    key: "isVisible",
	    value: function isVisible(range) {
	      
	      return false;
	    }

	    
	  }, {
	    key: "show",
	    value: function show() {
	      return false;
	    }

	    
	  }, {
	    key: "hide",
	    value: function hide() {
	      return false;
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw() {
	      
	    }

	    
	  }, {
	    key: "repositionX",
	    value: function repositionX() {
	      
	    }

	    
	  }, {
	    key: "repositionY",
	    value: function repositionY() {
	      
	    }

	    
	  }, {
	    key: "_repaintDragCenter",
	    value: function _repaintDragCenter() {
	      if (this.selected && this.editable.updateTime && !this.dom.dragCenter) {
	        var _context2, _context3;
	        var me = this;
	        
	        var dragCenter = document.createElement('div');
	        dragCenter.className = 'vis-drag-center';
	        dragCenter.dragCenterItem = this;
	        this.hammerDragCenter = new Hammer(dragCenter);
	        this.hammerDragCenter.on('tap', function (event) {
	          me.parent.itemSet.body.emitter.emit('click', {
	            event: event,
	            item: me.id
	          });
	        });
	        this.hammerDragCenter.on('doubletap', function (event) {
	          event.stopPropagation();
	          me.parent.itemSet._onUpdateItem(me);
	          me.parent.itemSet.body.emitter.emit('doubleClick', {
	            event: event,
	            item: me.id
	          });
	        });
	        this.hammerDragCenter.on('panstart', function (event) {
	          
	          event.stopPropagation();
	          me.parent.itemSet._onDragStart(event);
	        });
	        this.hammerDragCenter.on('panmove', _bindInstanceProperty(_context2 = me.parent.itemSet._onDrag).call(_context2, me.parent.itemSet));
	        this.hammerDragCenter.on('panend', _bindInstanceProperty(_context3 = me.parent.itemSet._onDragEnd).call(_context3, me.parent.itemSet));
	        
	        this.hammerDragCenter.get('press').set({
	          time: 10000
	        });
	        if (this.dom.box) {
	          if (this.dom.dragLeft) {
	            this.dom.box.insertBefore(dragCenter, this.dom.dragLeft);
	          } else {
	            this.dom.box.appendChild(dragCenter);
	          }
	        } else if (this.dom.point) {
	          this.dom.point.appendChild(dragCenter);
	        }
	        this.dom.dragCenter = dragCenter;
	      } else if (!this.selected && this.dom.dragCenter) {
	        
	        if (this.dom.dragCenter.parentNode) {
	          this.dom.dragCenter.parentNode.removeChild(this.dom.dragCenter);
	        }
	        this.dom.dragCenter = null;
	        if (this.hammerDragCenter) {
	          this.hammerDragCenter.destroy();
	          this.hammerDragCenter = null;
	        }
	      }
	    }

	    
	  }, {
	    key: "_repaintDeleteButton",
	    value: function _repaintDeleteButton(anchor) {
	      var editable = (this.options.editable.overrideItems || this.editable == null) && this.options.editable.remove || !this.options.editable.overrideItems && this.editable != null && this.editable.remove;
	      if (this.selected && editable && !this.dom.deleteButton) {
	        
	        var me = this;
	        var deleteButton = document.createElement('div');
	        if (this.options.rtl) {
	          deleteButton.className = 'vis-delete-rtl';
	        } else {
	          deleteButton.className = 'vis-delete';
	        }
	        var optionsLocale = this.options.locales[this.options.locale];
	        if (!optionsLocale) {
	          if (!this.warned) {
	            console.warn("WARNING: options.locales['".concat(this.options.locale, "'] not found. See https:
	            this.warned = true;
	          }
	          optionsLocale = this.options.locales['en']; 
	        }

	        deleteButton.title = optionsLocale.deleteSelected;

	        
	        this.hammerDeleteButton = new Hammer(deleteButton).on('tap', function (event) {
	          event.stopPropagation();
	          me.parent.removeFromDataSet(me);
	        });
	        anchor.appendChild(deleteButton);
	        this.dom.deleteButton = deleteButton;
	      } else if ((!this.selected || !editable) && this.dom.deleteButton) {
	        
	        if (this.dom.deleteButton.parentNode) {
	          this.dom.deleteButton.parentNode.removeChild(this.dom.deleteButton);
	        }
	        this.dom.deleteButton = null;
	        if (this.hammerDeleteButton) {
	          this.hammerDeleteButton.destroy();
	          this.hammerDeleteButton = null;
	        }
	      }
	    }

	    
	  }, {
	    key: "_repaintOnItemUpdateTimeTooltip",
	    value: function _repaintOnItemUpdateTimeTooltip(anchor) {
	      if (!this.options.tooltipOnItemUpdateTime) return;
	      var editable = (this.options.editable.updateTime || this.data.editable === true) && this.data.editable !== false;
	      if (this.selected && editable && !this.dom.onItemUpdateTimeTooltip) {
	        var onItemUpdateTimeTooltip = document.createElement('div');
	        onItemUpdateTimeTooltip.className = 'vis-onUpdateTime-tooltip';
	        anchor.appendChild(onItemUpdateTimeTooltip);
	        this.dom.onItemUpdateTimeTooltip = onItemUpdateTimeTooltip;
	      } else if (!this.selected && this.dom.onItemUpdateTimeTooltip) {
	        
	        if (this.dom.onItemUpdateTimeTooltip.parentNode) {
	          this.dom.onItemUpdateTimeTooltip.parentNode.removeChild(this.dom.onItemUpdateTimeTooltip);
	        }
	        this.dom.onItemUpdateTimeTooltip = null;
	      }

	      
	      if (this.dom.onItemUpdateTimeTooltip) {
	        
	        this.dom.onItemUpdateTimeTooltip.style.visibility = this.parent.itemSet.touchParams.itemIsDragging ? 'visible' : 'hidden';

	        
	        this.dom.onItemUpdateTimeTooltip.style.transform = 'translateX(-50%)';
	        this.dom.onItemUpdateTimeTooltip.style.left = '50%';

	        
	        var tooltipOffset = 50; 
	        var scrollTop = this.parent.itemSet.body.domProps.scrollTop;

	        
	        
	        var itemDistanceFromTop;
	        if (this.options.orientation.item == 'top') {
	          itemDistanceFromTop = this.top;
	        } else {
	          itemDistanceFromTop = this.parent.height - this.top - this.height;
	        }
	        var isCloseToTop = itemDistanceFromTop + this.parent.top - tooltipOffset < -scrollTop;
	        if (isCloseToTop) {
	          this.dom.onItemUpdateTimeTooltip.style.bottom = "";
	          this.dom.onItemUpdateTimeTooltip.style.top = "".concat(this.height + 2, "px");
	        } else {
	          this.dom.onItemUpdateTimeTooltip.style.top = "";
	          this.dom.onItemUpdateTimeTooltip.style.bottom = "".concat(this.height + 2, "px");
	        }

	        
	        var content;
	        var templateFunction;
	        if (this.options.tooltipOnItemUpdateTime && this.options.tooltipOnItemUpdateTime.template) {
	          var _context4;
	          templateFunction = _bindInstanceProperty(_context4 = this.options.tooltipOnItemUpdateTime.template).call(_context4, this);
	          content = templateFunction(this.data);
	        } else {
	          content = "start: ".concat(moment$2(this.data.start).format('MM/DD/YYYY hh:mm'));
	          if (this.data.end) {
	            content += "<br> end: ".concat(moment$2(this.data.end).format('MM/DD/YYYY hh:mm'));
	          }
	        }
	        this.dom.onItemUpdateTimeTooltip.innerHTML = availableUtils.xss(content);
	      }
	    }

	    
	  }, {
	    key: "_getItemData",
	    value: function _getItemData() {
	      return this.parent.itemSet.itemsData.get(this.id);
	    }

	    
	  }, {
	    key: "_updateContents",
	    value: function _updateContents(element) {
	      var content;
	      var changed;
	      var templateFunction;
	      var itemVisibleFrameContent;
	      var visibleFrameTemplateFunction;
	      var itemData = this._getItemData(); 

	      var frameElement = this.dom.box || this.dom.point;
	      var itemVisibleFrameContentElement = frameElement.getElementsByClassName('vis-item-visible-frame')[0];
	      if (this.options.visibleFrameTemplate) {
	        var _context5;
	        visibleFrameTemplateFunction = _bindInstanceProperty(_context5 = this.options.visibleFrameTemplate).call(_context5, this);
	        itemVisibleFrameContent = availableUtils.xss(visibleFrameTemplateFunction(itemData, itemVisibleFrameContentElement));
	      } else {
	        itemVisibleFrameContent = '';
	      }
	      if (itemVisibleFrameContentElement) {
	        if (itemVisibleFrameContent instanceof Object && !(itemVisibleFrameContent instanceof Element)) {
	          visibleFrameTemplateFunction(itemData, itemVisibleFrameContentElement);
	        } else {
	          changed = this._contentToString(this.itemVisibleFrameContent) !== this._contentToString(itemVisibleFrameContent);
	          if (changed) {
	            
	            if (itemVisibleFrameContent instanceof Element) {
	              itemVisibleFrameContentElement.innerHTML = '';
	              itemVisibleFrameContentElement.appendChild(itemVisibleFrameContent);
	            } else if (itemVisibleFrameContent != undefined) {
	              itemVisibleFrameContentElement.innerHTML = availableUtils.xss(itemVisibleFrameContent);
	            } else {
	              if (!(this.data.type == 'background' && this.data.content === undefined)) {
	                throw new Error("Property \"content\" missing in item ".concat(this.id));
	              }
	            }
	            this.itemVisibleFrameContent = itemVisibleFrameContent;
	          }
	        }
	      }
	      if (this.options.template) {
	        var _context6;
	        templateFunction = _bindInstanceProperty(_context6 = this.options.template).call(_context6, this);
	        content = templateFunction(itemData, element, this.data);
	      } else {
	        content = this.data.content;
	      }
	      if (content instanceof Object && !(content instanceof Element)) {
	        templateFunction(itemData, element);
	      } else {
	        changed = this._contentToString(this.content) !== this._contentToString(content);
	        if (changed) {
	          
	          if (content instanceof Element) {
	            element.innerHTML = '';
	            element.appendChild(content);
	          } else if (content != undefined) {
	            element.innerHTML = availableUtils.xss(content);
	          } else {
	            if (!(this.data.type == 'background' && this.data.content === undefined)) {
	              throw new Error("Property \"content\" missing in item ".concat(this.id));
	            }
	          }
	          this.content = content;
	        }
	      }
	    }

	    
	  }, {
	    key: "_updateDataAttributes",
	    value: function _updateDataAttributes(element) {
	      if (this.options.dataAttributes && this.options.dataAttributes.length > 0) {
	        var attributes = [];
	        if (_Array$isArray$1(this.options.dataAttributes)) {
	          attributes = this.options.dataAttributes;
	        } else if (this.options.dataAttributes == 'all') {
	          attributes = _Object$keys(this.data);
	        } else {
	          return;
	        }
	        var _iterator = _createForOfIteratorHelper$4(attributes),
	          _step;
	        try {
	          for (_iterator.s(); !(_step = _iterator.n()).done;) {
	            var name = _step.value;
	            var value = this.data[name];
	            if (value != null) {
	              element.setAttribute("data-".concat(name), value);
	            } else {
	              element.removeAttribute("data-".concat(name));
	            }
	          }
	        } catch (err) {
	          _iterator.e(err);
	        } finally {
	          _iterator.f();
	        }
	      }
	    }

	    
	  }, {
	    key: "_updateStyle",
	    value: function _updateStyle(element) {
	      
	      if (this.style) {
	        availableUtils.removeCssText(element, this.style);
	        this.style = null;
	      }

	      
	      if (this.data.style) {
	        availableUtils.addCssText(element, this.data.style);
	        this.style = this.data.style;
	      }
	    }

	    
	  }, {
	    key: "_contentToString",
	    value: function _contentToString(content) {
	      if (typeof content === 'string') return content;
	      if (content && 'outerHTML' in content) return content.outerHTML;
	      return content;
	    }

	    
	  }, {
	    key: "_updateEditStatus",
	    value: function _updateEditStatus() {
	      if (this.options) {
	        if (typeof this.options.editable === 'boolean') {
	          this.editable = {
	            updateTime: this.options.editable,
	            updateGroup: this.options.editable,
	            remove: this.options.editable
	          };
	        } else if (_typeof(this.options.editable) === 'object') {
	          this.editable = {};
	          availableUtils.selectiveExtend(['updateTime', 'updateGroup', 'remove'], this.editable, this.options.editable);
	        }
	      }
	      
	      if (!this.options || !this.options.editable || this.options.editable.overrideItems !== true) {
	        if (this.data) {
	          if (typeof this.data.editable === 'boolean') {
	            this.editable = {
	              updateTime: this.data.editable,
	              updateGroup: this.data.editable,
	              remove: this.data.editable
	            };
	          } else if (_typeof(this.data.editable) === 'object') {
	            
	            
	            this.editable = {};
	            availableUtils.selectiveExtend(['updateTime', 'updateGroup', 'remove'], this.editable, this.data.editable);
	          }
	        }
	      }
	    }

	    
	  }, {
	    key: "getWidthLeft",
	    value: function getWidthLeft() {
	      return 0;
	    }

	    
	  }, {
	    key: "getWidthRight",
	    value: function getWidthRight() {
	      return 0;
	    }

	    
	  }, {
	    key: "getTitle",
	    value: function getTitle() {
	      if (this.options.tooltip && this.options.tooltip.template) {
	        var _context7;
	        var templateFunction = _bindInstanceProperty(_context7 = this.options.tooltip.template).call(_context7, this);
	        return templateFunction(this._getItemData(), this.data);
	      }
	      return this.data.title;
	    }
	  }]);
	  return Item;
	}();
	Item.prototype.stack = true;

	function _createSuper$7(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$7(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$7() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	
	var BoxItem = function (_Item) {
	  _inherits(BoxItem, _Item);
	  var _super = _createSuper$7(BoxItem);
	  
	  function BoxItem(data, conversion, options) {
	    var _this;
	    _classCallCheck(this, BoxItem);
	    _this = _super.call(this, data, conversion, options);
	    _this.props = {
	      dot: {
	        width: 0,
	        height: 0
	      },
	      line: {
	        width: 0,
	        height: 0
	      }
	    };
	    
	    if (data) {
	      if (data.start == undefined) {
	        throw new Error("Property \"start\" missing in item ".concat(data));
	      }
	    }
	    return _this;
	  }

	  
	  _createClass(BoxItem, [{
	    key: "isVisible",
	    value: function isVisible(range) {
	      if (this.cluster) {
	        return false;
	      }
	      
	      var isVisible;
	      var align = this.data.align || this.options.align;
	      var widthInMs = this.width * range.getMillisecondsPerPixel();
	      if (align == 'right') {
	        isVisible = this.data.start.getTime() > range.start && this.data.start.getTime() - widthInMs < range.end;
	      } else if (align == 'left') {
	        isVisible = this.data.start.getTime() + widthInMs > range.start && this.data.start.getTime() < range.end;
	      } else {
	        
	        isVisible = this.data.start.getTime() + widthInMs / 2 > range.start && this.data.start.getTime() - widthInMs / 2 < range.end;
	      }
	      return isVisible;
	    }

	    
	  }, {
	    key: "_createDomElement",
	    value: function _createDomElement() {
	      if (!this.dom) {
	        
	        this.dom = {};

	        
	        this.dom.box = document.createElement('DIV');

	        
	        this.dom.content = document.createElement('DIV');
	        this.dom.content.className = 'vis-item-content';
	        this.dom.box.appendChild(this.dom.content);

	        
	        this.dom.line = document.createElement('DIV');
	        this.dom.line.className = 'vis-line';

	        
	        this.dom.dot = document.createElement('DIV');
	        this.dom.dot.className = 'vis-dot';

	        
	        this.dom.box['vis-item'] = this;
	        this.dirty = true;
	      }
	    }

	    
	  }, {
	    key: "_appendDomElement",
	    value: function _appendDomElement() {
	      if (!this.parent) {
	        throw new Error('Cannot redraw item: no parent attached');
	      }
	      if (!this.dom.box.parentNode) {
	        var foreground = this.parent.dom.foreground;
	        if (!foreground) throw new Error('Cannot redraw item: parent has no foreground container element');
	        foreground.appendChild(this.dom.box);
	      }
	      if (!this.dom.line.parentNode) {
	        var background = this.parent.dom.background;
	        if (!background) throw new Error('Cannot redraw item: parent has no background container element');
	        background.appendChild(this.dom.line);
	      }
	      if (!this.dom.dot.parentNode) {
	        var axis = this.parent.dom.axis;
	        if (!background) throw new Error('Cannot redraw item: parent has no axis container element');
	        axis.appendChild(this.dom.dot);
	      }
	      this.displayed = true;
	    }

	    
	  }, {
	    key: "_updateDirtyDomComponents",
	    value: function _updateDirtyDomComponents() {
	      
	      
	      
	      
	      if (this.dirty) {
	        this._updateContents(this.dom.content);
	        this._updateDataAttributes(this.dom.box);
	        this._updateStyle(this.dom.box);
	        var editable = this.editable.updateTime || this.editable.updateGroup;

	        
	        var className = (this.data.className ? ' ' + this.data.className : '') + (this.selected ? ' vis-selected' : '') + (editable ? ' vis-editable' : ' vis-readonly');
	        this.dom.box.className = "vis-item vis-box".concat(className);
	        this.dom.line.className = "vis-item vis-line".concat(className);
	        this.dom.dot.className = "vis-item vis-dot".concat(className);
	      }
	    }

	    
	  }, {
	    key: "_getDomComponentsSizes",
	    value: function _getDomComponentsSizes() {
	      return {
	        previous: {
	          right: this.dom.box.style.right,
	          left: this.dom.box.style.left
	        },
	        dot: {
	          height: this.dom.dot.offsetHeight,
	          width: this.dom.dot.offsetWidth
	        },
	        line: {
	          width: this.dom.line.offsetWidth
	        },
	        box: {
	          width: this.dom.box.offsetWidth,
	          height: this.dom.box.offsetHeight
	        }
	      };
	    }

	    
	  }, {
	    key: "_updateDomComponentsSizes",
	    value: function _updateDomComponentsSizes(sizes) {
	      if (this.options.rtl) {
	        this.dom.box.style.right = "0px";
	      } else {
	        this.dom.box.style.left = "0px";
	      }

	      
	      this.props.dot.height = sizes.dot.height;
	      this.props.dot.width = sizes.dot.width;
	      this.props.line.width = sizes.line.width;
	      this.width = sizes.box.width;
	      this.height = sizes.box.height;

	      
	      if (this.options.rtl) {
	        this.dom.box.style.right = sizes.previous.right;
	      } else {
	        this.dom.box.style.left = sizes.previous.left;
	      }
	      this.dirty = false;
	    }

	    
	  }, {
	    key: "_repaintDomAdditionals",
	    value: function _repaintDomAdditionals() {
	      this._repaintOnItemUpdateTimeTooltip(this.dom.box);
	      this._repaintDragCenter();
	      this._repaintDeleteButton(this.dom.box);
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw(returnQueue) {
	      var _context,
	        _context2,
	        _context3,
	        _this2 = this,
	        _context5;
	      var sizes;
	      var queue = [
	      
	      _bindInstanceProperty(_context = this._createDomElement).call(_context, this),
	      
	      _bindInstanceProperty(_context2 = this._appendDomElement).call(_context2, this),
	      
	      _bindInstanceProperty(_context3 = this._updateDirtyDomComponents).call(_context3, this), function () {
	        if (_this2.dirty) {
	          sizes = _this2._getDomComponentsSizes();
	        }
	      }, function () {
	        if (_this2.dirty) {
	          var _context4;
	          _bindInstanceProperty(_context4 = _this2._updateDomComponentsSizes).call(_context4, _this2)(sizes);
	        }
	      },
	      
	      _bindInstanceProperty(_context5 = this._repaintDomAdditionals).call(_context5, this)];
	      if (returnQueue) {
	        return queue;
	      } else {
	        var result;
	        _forEachInstanceProperty(queue).call(queue, function (fn) {
	          result = fn();
	        });
	        return result;
	      }
	    }

	    
	  }, {
	    key: "show",
	    value: function show(returnQueue) {
	      if (!this.displayed) {
	        return this.redraw(returnQueue);
	      }
	    }

	    
	  }, {
	    key: "hide",
	    value: function hide() {
	      if (this.displayed) {
	        var dom = this.dom;
	        if (dom.box.remove) dom.box.remove();else if (dom.box.parentNode) dom.box.parentNode.removeChild(dom.box); 

	        if (dom.line.remove) dom.line.remove();else if (dom.line.parentNode) dom.line.parentNode.removeChild(dom.line); 

	        if (dom.dot.remove) dom.dot.remove();else if (dom.dot.parentNode) dom.dot.parentNode.removeChild(dom.dot); 

	        this.displayed = false;
	      }
	    }

	    
	  }, {
	    key: "repositionXY",
	    value: function repositionXY() {
	      var rtl = this.options.rtl;
	      var repositionXY = function repositionXY(element, x, y) {
	        var _context6;
	        var rtl = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
	        if (x === undefined && y === undefined) return;
	        
	        var directionX = rtl ? x * -1 : x;

	        
	        if (y === undefined) {
	          element.style.transform = "translateX(".concat(directionX, "px)");
	          return;
	        }

	        
	        if (x === undefined) {
	          element.style.transform = "translateY(".concat(y, "px)");
	          return;
	        }
	        element.style.transform = _concatInstanceProperty(_context6 = "translate(".concat(directionX, "px, ")).call(_context6, y, "px)");
	      };
	      repositionXY(this.dom.box, this.boxX, this.boxY, rtl);
	      repositionXY(this.dom.dot, this.dotX, this.dotY, rtl);
	      repositionXY(this.dom.line, this.lineX, this.lineY, rtl);
	    }

	    
	  }, {
	    key: "repositionX",
	    value: function repositionX() {
	      var start = this.conversion.toScreen(this.data.start);
	      var align = this.data.align === undefined ? this.options.align : this.data.align;
	      var lineWidth = this.props.line.width;
	      var dotWidth = this.props.dot.width;
	      if (align == 'right') {
	        
	        this.boxX = start - this.width;
	        this.lineX = start - lineWidth;
	        this.dotX = start - lineWidth / 2 - dotWidth / 2;
	      } else if (align == 'left') {
	        
	        this.boxX = start;
	        this.lineX = start;
	        this.dotX = start + lineWidth / 2 - dotWidth / 2;
	      } else {
	        
	        this.boxX = start - this.width / 2;
	        this.lineX = this.options.rtl ? start - lineWidth : start - lineWidth / 2;
	        this.dotX = start - dotWidth / 2;
	      }
	      if (this.options.rtl) this.right = this.boxX;else this.left = this.boxX;
	      this.repositionXY();
	    }

	    
	  }, {
	    key: "repositionY",
	    value: function repositionY() {
	      var orientation = this.options.orientation.item;
	      var lineStyle = this.dom.line.style;
	      if (orientation == 'top') {
	        var lineHeight = this.parent.top + this.top + 1;
	        this.boxY = this.top || 0;
	        lineStyle.height = "".concat(lineHeight, "px");
	        lineStyle.bottom = '';
	        lineStyle.top = '0';
	      } else {
	        
	        var itemSetHeight = this.parent.itemSet.props.height; 
	        var _lineHeight = itemSetHeight - this.parent.top - this.parent.height + this.top;
	        this.boxY = this.parent.height - this.top - (this.height || 0);
	        lineStyle.height = "".concat(_lineHeight, "px");
	        lineStyle.top = '';
	        lineStyle.bottom = '0';
	      }
	      this.dotY = -this.props.dot.height / 2;
	      this.repositionXY();
	    }

	    
	  }, {
	    key: "getWidthLeft",
	    value: function getWidthLeft() {
	      return this.width / 2;
	    }

	    
	  }, {
	    key: "getWidthRight",
	    value: function getWidthRight() {
	      return this.width / 2;
	    }
	  }]);
	  return BoxItem;
	}(Item);

	function _createSuper$6(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$6(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$6() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	
	var PointItem = function (_Item) {
	  _inherits(PointItem, _Item);
	  var _super = _createSuper$6(PointItem);
	  
	  function PointItem(data, conversion, options) {
	    var _this;
	    _classCallCheck(this, PointItem);
	    _this = _super.call(this, data, conversion, options);
	    _this.props = {
	      dot: {
	        top: 0,
	        width: 0,
	        height: 0
	      },
	      content: {
	        height: 0,
	        marginLeft: 0,
	        marginRight: 0
	      }
	    };
	    
	    if (data) {
	      if (data.start == undefined) {
	        throw new Error("Property \"start\" missing in item ".concat(data));
	      }
	    }
	    return _this;
	  }

	  
	  _createClass(PointItem, [{
	    key: "isVisible",
	    value: function isVisible(range) {
	      if (this.cluster) {
	        return false;
	      }
	      
	      var widthInMs = this.width * range.getMillisecondsPerPixel();
	      return this.data.start.getTime() + widthInMs > range.start && this.data.start < range.end;
	    }

	    
	  }, {
	    key: "_createDomElement",
	    value: function _createDomElement() {
	      if (!this.dom) {
	        
	        this.dom = {};

	        
	        this.dom.point = document.createElement('div');
	        

	        
	        this.dom.content = document.createElement('div');
	        this.dom.content.className = 'vis-item-content';
	        this.dom.point.appendChild(this.dom.content);

	        
	        this.dom.dot = document.createElement('div');
	        this.dom.point.appendChild(this.dom.dot);

	        
	        this.dom.point['vis-item'] = this;
	        this.dirty = true;
	      }
	    }

	    
	  }, {
	    key: "_appendDomElement",
	    value: function _appendDomElement() {
	      if (!this.parent) {
	        throw new Error('Cannot redraw item: no parent attached');
	      }
	      if (!this.dom.point.parentNode) {
	        var foreground = this.parent.dom.foreground;
	        if (!foreground) {
	          throw new Error('Cannot redraw item: parent has no foreground container element');
	        }
	        foreground.appendChild(this.dom.point);
	      }
	      this.displayed = true;
	    }

	    
	  }, {
	    key: "_updateDirtyDomComponents",
	    value: function _updateDirtyDomComponents() {
	      
	      
	      
	      
	      if (this.dirty) {
	        this._updateContents(this.dom.content);
	        this._updateDataAttributes(this.dom.point);
	        this._updateStyle(this.dom.point);
	        var editable = this.editable.updateTime || this.editable.updateGroup;
	        
	        var className = (this.data.className ? ' ' + this.data.className : '') + (this.selected ? ' vis-selected' : '') + (editable ? ' vis-editable' : ' vis-readonly');
	        this.dom.point.className = "vis-item vis-point".concat(className);
	        this.dom.dot.className = "vis-item vis-dot".concat(className);
	      }
	    }

	    
	  }, {
	    key: "_getDomComponentsSizes",
	    value: function _getDomComponentsSizes() {
	      return {
	        dot: {
	          width: this.dom.dot.offsetWidth,
	          height: this.dom.dot.offsetHeight
	        },
	        content: {
	          width: this.dom.content.offsetWidth,
	          height: this.dom.content.offsetHeight
	        },
	        point: {
	          width: this.dom.point.offsetWidth,
	          height: this.dom.point.offsetHeight
	        }
	      };
	    }

	    
	  }, {
	    key: "_updateDomComponentsSizes",
	    value: function _updateDomComponentsSizes(sizes) {
	      
	      this.props.dot.width = sizes.dot.width;
	      this.props.dot.height = sizes.dot.height;
	      this.props.content.height = sizes.content.height;

	      
	      if (this.options.rtl) {
	        this.dom.content.style.marginRight = "".concat(this.props.dot.width / 2, "px");
	      } else {
	        this.dom.content.style.marginLeft = "".concat(this.props.dot.width / 2, "px");
	      }
	      

	      
	      this.width = sizes.point.width;
	      this.height = sizes.point.height;

	      
	      this.dom.dot.style.top = "".concat((this.height - this.props.dot.height) / 2, "px");
	      var dotWidth = this.props.dot.width;
	      var translateX = this.options.rtl ? dotWidth / 2 : dotWidth / 2 * -1;
	      this.dom.dot.style.transform = "translateX(".concat(translateX, "px");
	      this.dirty = false;
	    }

	    
	  }, {
	    key: "_repaintDomAdditionals",
	    value: function _repaintDomAdditionals() {
	      this._repaintOnItemUpdateTimeTooltip(this.dom.point);
	      this._repaintDragCenter();
	      this._repaintDeleteButton(this.dom.point);
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw(returnQueue) {
	      var _context,
	        _context2,
	        _context3,
	        _this2 = this,
	        _context5;
	      var sizes;
	      var queue = [
	      
	      _bindInstanceProperty(_context = this._createDomElement).call(_context, this),
	      
	      _bindInstanceProperty(_context2 = this._appendDomElement).call(_context2, this),
	      
	      _bindInstanceProperty(_context3 = this._updateDirtyDomComponents).call(_context3, this), function () {
	        if (_this2.dirty) {
	          sizes = _this2._getDomComponentsSizes();
	        }
	      }, function () {
	        if (_this2.dirty) {
	          var _context4;
	          _bindInstanceProperty(_context4 = _this2._updateDomComponentsSizes).call(_context4, _this2)(sizes);
	        }
	      },
	      
	      _bindInstanceProperty(_context5 = this._repaintDomAdditionals).call(_context5, this)];
	      if (returnQueue) {
	        return queue;
	      } else {
	        var result;
	        _forEachInstanceProperty(queue).call(queue, function (fn) {
	          result = fn();
	        });
	        return result;
	      }
	    }

	    
	  }, {
	    key: "repositionXY",
	    value: function repositionXY() {
	      var rtl = this.options.rtl;
	      var repositionXY = function repositionXY(element, x, y) {
	        var _context6;
	        var rtl = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
	        if (x === undefined && y === undefined) return;
	        
	        var directionX = rtl ? x * -1 : x;

	        
	        if (y === undefined) {
	          element.style.transform = "translateX(".concat(directionX, "px)");
	          return;
	        }

	        
	        if (x === undefined) {
	          element.style.transform = "translateY(".concat(y, "px)");
	          return;
	        }
	        element.style.transform = _concatInstanceProperty(_context6 = "translate(".concat(directionX, "px, ")).call(_context6, y, "px)");
	      };
	      repositionXY(this.dom.point, this.pointX, this.pointY, rtl);
	    }

	    
	  }, {
	    key: "show",
	    value: function show(returnQueue) {
	      if (!this.displayed) {
	        return this.redraw(returnQueue);
	      }
	    }

	    
	  }, {
	    key: "hide",
	    value: function hide() {
	      if (this.displayed) {
	        if (this.dom.point.parentNode) {
	          this.dom.point.parentNode.removeChild(this.dom.point);
	        }
	        this.displayed = false;
	      }
	    }

	    
	  }, {
	    key: "repositionX",
	    value: function repositionX() {
	      var start = this.conversion.toScreen(this.data.start);
	      this.pointX = start;
	      if (this.options.rtl) {
	        this.right = start - this.props.dot.width;
	      } else {
	        this.left = start - this.props.dot.width;
	      }
	      this.repositionXY();
	    }

	    
	  }, {
	    key: "repositionY",
	    value: function repositionY() {
	      var orientation = this.options.orientation.item;
	      if (orientation == 'top') {
	        this.pointY = this.top;
	      } else {
	        this.pointY = this.parent.height - this.top - this.height;
	      }
	      this.repositionXY();
	    }

	    
	  }, {
	    key: "getWidthLeft",
	    value: function getWidthLeft() {
	      return this.props.dot.width;
	    }

	    
	  }, {
	    key: "getWidthRight",
	    value: function getWidthRight() {
	      return this.props.dot.width;
	    }
	  }]);
	  return PointItem;
	}(Item);

	function _createSuper$5(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$5(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$5() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	
	var RangeItem = function (_Item) {
	  _inherits(RangeItem, _Item);
	  var _super = _createSuper$5(RangeItem);
	  
	  function RangeItem(data, conversion, options) {
	    var _this;
	    _classCallCheck(this, RangeItem);
	    _this = _super.call(this, data, conversion, options);
	    _this.props = {
	      content: {
	        width: 0
	      }
	    };
	    _this.overflow = false; 
	    
	    if (data) {
	      if (data.start == undefined) {
	        throw new Error("Property \"start\" missing in item ".concat(data.id));
	      }
	      if (data.end == undefined) {
	        throw new Error("Property \"end\" missing in item ".concat(data.id));
	      }
	    }
	    return _this;
	  }

	  
	  _createClass(RangeItem, [{
	    key: "isVisible",
	    value: function isVisible(range) {
	      if (this.cluster) {
	        return false;
	      }
	      
	      return this.data.start < range.end && this.data.end > range.start;
	    }

	    
	  }, {
	    key: "_createDomElement",
	    value: function _createDomElement() {
	      if (!this.dom) {
	        
	        this.dom = {};

	        
	        this.dom.box = document.createElement('div');
	        

	        
	        this.dom.frame = document.createElement('div');
	        this.dom.frame.className = 'vis-item-overflow';
	        this.dom.box.appendChild(this.dom.frame);

	        
	        this.dom.visibleFrame = document.createElement('div');
	        this.dom.visibleFrame.className = 'vis-item-visible-frame';
	        this.dom.box.appendChild(this.dom.visibleFrame);

	        
	        this.dom.content = document.createElement('div');
	        this.dom.content.className = 'vis-item-content';
	        this.dom.frame.appendChild(this.dom.content);

	        
	        this.dom.box['vis-item'] = this;
	        this.dirty = true;
	      }
	    }

	    
	  }, {
	    key: "_appendDomElement",
	    value: function _appendDomElement() {
	      if (!this.parent) {
	        throw new Error('Cannot redraw item: no parent attached');
	      }
	      if (!this.dom.box.parentNode) {
	        var foreground = this.parent.dom.foreground;
	        if (!foreground) {
	          throw new Error('Cannot redraw item: parent has no foreground container element');
	        }
	        foreground.appendChild(this.dom.box);
	      }
	      this.displayed = true;
	    }

	    
	  }, {
	    key: "_updateDirtyDomComponents",
	    value: function _updateDirtyDomComponents() {
	      
	      
	      
	      
	      if (this.dirty) {
	        this._updateContents(this.dom.content);
	        this._updateDataAttributes(this.dom.box);
	        this._updateStyle(this.dom.box);
	        var editable = this.editable.updateTime || this.editable.updateGroup;

	        
	        var className = (this.data.className ? ' ' + this.data.className : '') + (this.selected ? ' vis-selected' : '') + (editable ? ' vis-editable' : ' vis-readonly');
	        this.dom.box.className = this.baseClassName + className;

	        
	        
	        this.dom.content.style.maxWidth = 'none';
	      }
	    }

	    
	  }, {
	    key: "_getDomComponentsSizes",
	    value: function _getDomComponentsSizes() {
	      
	      this.overflow = window.getComputedStyle(this.dom.frame).overflow !== 'hidden';
	      this.whiteSpace = window.getComputedStyle(this.dom.content).whiteSpace !== 'nowrap';
	      return {
	        content: {
	          width: this.dom.content.offsetWidth
	        },
	        box: {
	          height: this.dom.box.offsetHeight
	        }
	      };
	    }

	    
	  }, {
	    key: "_updateDomComponentsSizes",
	    value: function _updateDomComponentsSizes(sizes) {
	      this.props.content.width = sizes.content.width;
	      this.height = sizes.box.height;
	      this.dom.content.style.maxWidth = '';
	      this.dirty = false;
	    }

	    
	  }, {
	    key: "_repaintDomAdditionals",
	    value: function _repaintDomAdditionals() {
	      this._repaintOnItemUpdateTimeTooltip(this.dom.box);
	      this._repaintDeleteButton(this.dom.box);
	      this._repaintDragCenter();
	      this._repaintDragLeft();
	      this._repaintDragRight();
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw(returnQueue) {
	      var _context,
	        _context2,
	        _context3,
	        _this2 = this,
	        _context6;
	      var sizes;
	      var queue = [
	      
	      _bindInstanceProperty(_context = this._createDomElement).call(_context, this),
	      
	      _bindInstanceProperty(_context2 = this._appendDomElement).call(_context2, this),
	      
	      _bindInstanceProperty(_context3 = this._updateDirtyDomComponents).call(_context3, this), function () {
	        if (_this2.dirty) {
	          var _context4;
	          sizes = _bindInstanceProperty(_context4 = _this2._getDomComponentsSizes).call(_context4, _this2)();
	        }
	      }, function () {
	        if (_this2.dirty) {
	          var _context5;
	          _bindInstanceProperty(_context5 = _this2._updateDomComponentsSizes).call(_context5, _this2)(sizes);
	        }
	      },
	      
	      _bindInstanceProperty(_context6 = this._repaintDomAdditionals).call(_context6, this)];
	      if (returnQueue) {
	        return queue;
	      } else {
	        var result;
	        _forEachInstanceProperty(queue).call(queue, function (fn) {
	          result = fn();
	        });
	        return result;
	      }
	    }

	    
	  }, {
	    key: "show",
	    value: function show(returnQueue) {
	      if (!this.displayed) {
	        return this.redraw(returnQueue);
	      }
	    }

	    
	  }, {
	    key: "hide",
	    value: function hide() {
	      if (this.displayed) {
	        var box = this.dom.box;
	        if (box.parentNode) {
	          box.parentNode.removeChild(box);
	        }
	        this.displayed = false;
	      }
	    }

	    
	  }, {
	    key: "repositionX",
	    value: function repositionX(limitSize) {
	      var parentWidth = this.parent.width;
	      var start = this.conversion.toScreen(this.data.start);
	      var end = this.conversion.toScreen(this.data.end);
	      var align = this.data.align === undefined ? this.options.align : this.data.align;
	      var contentStartPosition;
	      var contentWidth;

	      
	      
	      if (this.data.limitSize !== false && (limitSize === undefined || limitSize === true)) {
	        if (start < -parentWidth) {
	          start = -parentWidth;
	        }
	        if (end > 2 * parentWidth) {
	          end = 2 * parentWidth;
	        }
	      }

	      
	      var boxWidth = Math.max(Math.round((end - start) * 1000) / 1000, 1);
	      if (this.overflow) {
	        if (this.options.rtl) {
	          this.right = start;
	        } else {
	          this.left = start;
	        }
	        this.width = boxWidth + this.props.content.width;
	        contentWidth = this.props.content.width;

	        
	        
	        
	      } else {
	        if (this.options.rtl) {
	          this.right = start;
	        } else {
	          this.left = start;
	        }
	        this.width = boxWidth;
	        contentWidth = Math.min(end - start, this.props.content.width);
	      }
	      if (this.options.rtl) {
	        this.dom.box.style.transform = "translateX(".concat(this.right * -1, "px)");
	      } else {
	        this.dom.box.style.transform = "translateX(".concat(this.left, "px)");
	      }
	      this.dom.box.style.width = "".concat(boxWidth, "px");
	      if (this.whiteSpace) {
	        this.height = this.dom.box.offsetHeight;
	      }
	      switch (align) {
	        case 'left':
	          this.dom.content.style.transform = 'translateX(0)';
	          break;
	        case 'right':
	          if (this.options.rtl) {
	            var translateX = Math.max(boxWidth - contentWidth, 0) * -1;
	            this.dom.content.style.transform = "translateX(".concat(translateX, "px)");
	          } else {
	            this.dom.content.style.transform = "translateX(".concat(Math.max(boxWidth - contentWidth, 0), "px)");
	          }
	          break;
	        case 'center':
	          if (this.options.rtl) {
	            var _translateX = Math.max((boxWidth - contentWidth) / 2, 0) * -1;
	            this.dom.content.style.transform = "translateX(".concat(_translateX, "px)");
	          } else {
	            this.dom.content.style.transform = "translateX(".concat(Math.max((boxWidth - contentWidth) / 2, 0), "px)");
	          }
	          break;
	        default:
	          
	          
	          if (this.overflow) {
	            if (end > 0) {
	              contentStartPosition = Math.max(-start, 0);
	            } else {
	              contentStartPosition = -contentWidth; 
	            }
	          } else {
	            if (start < 0) {
	              contentStartPosition = -start;
	            } else {
	              contentStartPosition = 0;
	            }
	          }
	          if (this.options.rtl) {
	            var _translateX2 = contentStartPosition * -1;
	            this.dom.content.style.transform = "translateX(".concat(_translateX2, "px)");
	          } else {
	            this.dom.content.style.transform = "translateX(".concat(contentStartPosition, "px)");
	            
	          }
	      }
	    }

	    
	  }, {
	    key: "repositionY",
	    value: function repositionY() {
	      var orientation = this.options.orientation.item;
	      var box = this.dom.box;
	      if (orientation == 'top') {
	        box.style.top = "".concat(this.top, "px");
	      } else {
	        box.style.top = "".concat(this.parent.height - this.top - this.height, "px");
	      }
	    }

	    
	  }, {
	    key: "_repaintDragLeft",
	    value: function _repaintDragLeft() {
	      if ((this.selected || this.options.itemsAlwaysDraggable.range) && this.editable.updateTime && !this.dom.dragLeft) {
	        
	        var dragLeft = document.createElement('div');
	        dragLeft.className = 'vis-drag-left';
	        dragLeft.dragLeftItem = this;
	        this.dom.box.appendChild(dragLeft);
	        this.dom.dragLeft = dragLeft;
	      } else if (!this.selected && !this.options.itemsAlwaysDraggable.range && this.dom.dragLeft) {
	        
	        if (this.dom.dragLeft.parentNode) {
	          this.dom.dragLeft.parentNode.removeChild(this.dom.dragLeft);
	        }
	        this.dom.dragLeft = null;
	      }
	    }

	    
	  }, {
	    key: "_repaintDragRight",
	    value: function _repaintDragRight() {
	      if ((this.selected || this.options.itemsAlwaysDraggable.range) && this.editable.updateTime && !this.dom.dragRight) {
	        
	        var dragRight = document.createElement('div');
	        dragRight.className = 'vis-drag-right';
	        dragRight.dragRightItem = this;
	        this.dom.box.appendChild(dragRight);
	        this.dom.dragRight = dragRight;
	      } else if (!this.selected && !this.options.itemsAlwaysDraggable.range && this.dom.dragRight) {
	        
	        if (this.dom.dragRight.parentNode) {
	          this.dom.dragRight.parentNode.removeChild(this.dom.dragRight);
	        }
	        this.dom.dragRight = null;
	      }
	    }
	  }]);
	  return RangeItem;
	}(Item);
	RangeItem.prototype.baseClassName = 'vis-item vis-range';

	function _createSuper$4(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$4(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$4() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	
	var BackgroundItem = function (_Item) {
	  _inherits(BackgroundItem, _Item);
	  var _super = _createSuper$4(BackgroundItem);
	  
	  function BackgroundItem(data, conversion, options) {
	    var _this;
	    _classCallCheck(this, BackgroundItem);
	    _this = _super.call(this, data, conversion, options);
	    _this.props = {
	      content: {
	        width: 0
	      }
	    };
	    _this.overflow = false; 

	    
	    if (data) {
	      if (data.start == undefined) {
	        throw new Error("Property \"start\" missing in item ".concat(data.id));
	      }
	      if (data.end == undefined) {
	        throw new Error("Property \"end\" missing in item ".concat(data.id));
	      }
	    }
	    return _this;
	  }

	  
	  _createClass(BackgroundItem, [{
	    key: "isVisible",
	    value: function isVisible(range) {
	      
	      return this.data.start < range.end && this.data.end > range.start;
	    }

	    
	  }, {
	    key: "_createDomElement",
	    value: function _createDomElement() {
	      if (!this.dom) {
	        
	        this.dom = {};

	        
	        this.dom.box = document.createElement('div');
	        

	        
	        this.dom.frame = document.createElement('div');
	        this.dom.frame.className = 'vis-item-overflow';
	        this.dom.box.appendChild(this.dom.frame);

	        
	        this.dom.content = document.createElement('div');
	        this.dom.content.className = 'vis-item-content';
	        this.dom.frame.appendChild(this.dom.content);

	        
	        
	        

	        this.dirty = true;
	      }
	    }

	    
	  }, {
	    key: "_appendDomElement",
	    value: function _appendDomElement() {
	      if (!this.parent) {
	        throw new Error('Cannot redraw item: no parent attached');
	      }
	      if (!this.dom.box.parentNode) {
	        var background = this.parent.dom.background;
	        if (!background) {
	          throw new Error('Cannot redraw item: parent has no background container element');
	        }
	        background.appendChild(this.dom.box);
	      }
	      this.displayed = true;
	    }

	    
	  }, {
	    key: "_updateDirtyDomComponents",
	    value: function _updateDirtyDomComponents() {
	      
	      
	      
	      
	      if (this.dirty) {
	        this._updateContents(this.dom.content);
	        this._updateDataAttributes(this.dom.content);
	        this._updateStyle(this.dom.box);

	        
	        var className = (this.data.className ? ' ' + this.data.className : '') + (this.selected ? ' vis-selected' : '');
	        this.dom.box.className = this.baseClassName + className;
	      }
	    }

	    
	  }, {
	    key: "_getDomComponentsSizes",
	    value: function _getDomComponentsSizes() {
	      
	      this.overflow = window.getComputedStyle(this.dom.content).overflow !== 'hidden';
	      return {
	        content: {
	          width: this.dom.content.offsetWidth
	        }
	      };
	    }

	    
	  }, {
	    key: "_updateDomComponentsSizes",
	    value: function _updateDomComponentsSizes(sizes) {
	      
	      this.props.content.width = sizes.content.width;
	      this.height = 0; 

	      this.dirty = false;
	    }

	    
	  }, {
	    key: "_repaintDomAdditionals",
	    value: function _repaintDomAdditionals() {}

	    
	  }, {
	    key: "redraw",
	    value: function redraw(returnQueue) {
	      var _context,
	        _context2,
	        _context3,
	        _this2 = this,
	        _context6;
	      var sizes;
	      var queue = [
	      
	      _bindInstanceProperty(_context = this._createDomElement).call(_context, this),
	      
	      _bindInstanceProperty(_context2 = this._appendDomElement).call(_context2, this), _bindInstanceProperty(_context3 = this._updateDirtyDomComponents).call(_context3, this), function () {
	        if (_this2.dirty) {
	          var _context4;
	          sizes = _bindInstanceProperty(_context4 = _this2._getDomComponentsSizes).call(_context4, _this2)();
	        }
	      }, function () {
	        if (_this2.dirty) {
	          var _context5;
	          _bindInstanceProperty(_context5 = _this2._updateDomComponentsSizes).call(_context5, _this2)(sizes);
	        }
	      },
	      
	      _bindInstanceProperty(_context6 = this._repaintDomAdditionals).call(_context6, this)];
	      if (returnQueue) {
	        return queue;
	      } else {
	        var result;
	        _forEachInstanceProperty(queue).call(queue, function (fn) {
	          result = fn();
	        });
	        return result;
	      }
	    }

	    
	  }, {
	    key: "repositionY",
	    value: function repositionY(margin) {
	      
	      var height;
	      var orientation = this.options.orientation.item;

	      
	      if (this.data.subgroup !== undefined) {
	        
	        var itemSubgroup = this.data.subgroup;
	        this.dom.box.style.height = "".concat(this.parent.subgroups[itemSubgroup].height, "px");
	        if (orientation == 'top') {
	          this.dom.box.style.top = "".concat(this.parent.top + this.parent.subgroups[itemSubgroup].top, "px");
	        } else {
	          this.dom.box.style.top = "".concat(this.parent.top + this.parent.height - this.parent.subgroups[itemSubgroup].top - this.parent.subgroups[itemSubgroup].height, "px");
	        }
	        this.dom.box.style.bottom = '';
	      }
	      
	      else {
	        
	        if (this.parent instanceof BackgroundGroup) {
	          
	          height = Math.max(this.parent.height, this.parent.itemSet.body.domProps.center.height, this.parent.itemSet.body.domProps.centerContainer.height);
	          this.dom.box.style.bottom = orientation == 'bottom' ? '0' : '';
	          this.dom.box.style.top = orientation == 'top' ? '0' : '';
	        } else {
	          height = this.parent.height;
	          
	          this.dom.box.style.top = "".concat(this.parent.top, "px");
	          this.dom.box.style.bottom = '';
	        }
	      }
	      this.dom.box.style.height = "".concat(height, "px");
	    }
	  }]);
	  return BackgroundItem;
	}(Item);
	BackgroundItem.prototype.baseClassName = 'vis-item vis-background';
	BackgroundItem.prototype.stack = false;

	
	BackgroundItem.prototype.show = RangeItem.prototype.show;

	
	BackgroundItem.prototype.hide = RangeItem.prototype.hide;

	
	BackgroundItem.prototype.repositionX = RangeItem.prototype.repositionX;

	
	var Popup = function () {
	  
	  function Popup(container, overflowMethod) {
	    _classCallCheck(this, Popup);
	    this.container = container;
	    this.overflowMethod = overflowMethod || 'cap';
	    this.x = 0;
	    this.y = 0;
	    this.padding = 5;
	    this.hidden = false;

	    
	    this.frame = document.createElement('div');
	    this.frame.className = 'vis-tooltip';
	    this.container.appendChild(this.frame);
	  }

	  
	  _createClass(Popup, [{
	    key: "setPosition",
	    value: function setPosition(x, y) {
	      this.x = _parseInt(x);
	      this.y = _parseInt(y);
	    }

	    
	  }, {
	    key: "setText",
	    value: function setText(content) {
	      if (content instanceof Element) {
	        this.frame.innerHTML = '';
	        this.frame.appendChild(content);
	      } else {
	        this.frame.innerHTML = availableUtils.xss(content); 
	      }
	    }

	    
	  }, {
	    key: "show",
	    value: function show(doShow) {
	      if (doShow === undefined) {
	        doShow = true;
	      }
	      if (doShow === true) {
	        var height = this.frame.clientHeight;
	        var width = this.frame.clientWidth;
	        var maxHeight = this.frame.parentNode.clientHeight;
	        var maxWidth = this.frame.parentNode.clientWidth;
	        var left = 0,
	          top = 0;
	        if (this.overflowMethod == 'flip' || this.overflowMethod == 'none') {
	          var isLeft = false,
	            isTop = true; 

	          if (this.overflowMethod == 'flip') {
	            if (this.y - height < this.padding) {
	              isTop = false;
	            }
	            if (this.x + width > maxWidth - this.padding) {
	              isLeft = true;
	            }
	          }
	          if (isLeft) {
	            left = this.x - width;
	          } else {
	            left = this.x;
	          }
	          if (isTop) {
	            top = this.y - height;
	          } else {
	            top = this.y;
	          }
	        } else {
	          
	          top = this.y - height;
	          if (top + height + this.padding > maxHeight) {
	            top = maxHeight - height - this.padding;
	          }
	          if (top < this.padding) {
	            top = this.padding;
	          }
	          left = this.x;
	          if (left + width + this.padding > maxWidth) {
	            left = maxWidth - width - this.padding;
	          }
	          if (left < this.padding) {
	            left = this.padding;
	          }
	        }
	        this.frame.style.left = left + "px";
	        this.frame.style.top = top + "px";
	        this.frame.style.visibility = "visible";
	        this.hidden = false;
	      } else {
	        this.hide();
	      }
	    }

	    
	  }, {
	    key: "hide",
	    value: function hide() {
	      this.hidden = true;
	      this.frame.style.left = "0";
	      this.frame.style.top = "0";
	      this.frame.style.visibility = "hidden";
	    }

	    
	  }, {
	    key: "destroy",
	    value: function destroy() {
	      this.frame.parentNode.removeChild(this.frame); 
	    }
	  }]);
	  return Popup;
	}();

	var everyExports = {};
	var every$3 = {
	  get exports(){ return everyExports; },
	  set exports(v){ everyExports = v; },
	};

	var $ = _export;
	var $every = arrayIteration.every;
	var arrayMethodIsStrict = arrayMethodIsStrict$6;

	var STRICT_METHOD = arrayMethodIsStrict('every');

	
	
	$({ target: 'Array', proto: true, forced: !STRICT_METHOD }, {
	  every: function every(callbackfn ) {
	    return $every(this, callbackfn, arguments.length > 1 ? arguments[1] : undefined);
	  }
	});

	var entryVirtual = entryVirtual$k;

	var every$2 = entryVirtual('Array').every;

	var isPrototypeOf = objectIsPrototypeOf;
	var method = every$2;

	var ArrayPrototype = Array.prototype;

	var every$1 = function (it) {
	  var own = it.every;
	  return it === ArrayPrototype || (isPrototypeOf(ArrayPrototype, it) && own === ArrayPrototype.every) ? method : own;
	};

	var parent = every$1;

	var every = parent;

	(function (module) {
		module.exports = every;
	} (every$3));

	var _everyInstanceProperty = getDefaultExportFromCjs(everyExports);

	function _createForOfIteratorHelper$3(o, allowArrayLike) { var it = typeof _Symbol !== "undefined" && _getIteratorMethod(o) || o["@@iterator"]; if (!it) { if (_Array$isArray$1(o) || (it = _unsupportedIterableToArray$3(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }
	function _unsupportedIterableToArray$3(o, minLen) { var _context14; if (!o) return; if (typeof o === "string") return _arrayLikeToArray$3(o, minLen); var n = _sliceInstanceProperty(_context14 = Object.prototype.toString.call(o)).call(_context14, 8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return _Array$from$1(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray$3(o, minLen); }
	function _arrayLikeToArray$3(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
	function _createSuper$3(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$3(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$3() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	
	var ClusterItem = function (_Item) {
	  _inherits(ClusterItem, _Item);
	  var _super = _createSuper$3(ClusterItem);
	  
	  function ClusterItem(data, conversion, options) {
	    var _this;
	    _classCallCheck(this, ClusterItem);
	    var modifiedOptions = _Object$assign({}, {
	      fitOnDoubleClick: true
	    }, options, {
	      editable: false
	    });
	    _this = _super.call(this, data, conversion, modifiedOptions);
	    _this.props = {
	      content: {
	        width: 0,
	        height: 0
	      }
	    };
	    if (!data || data.uiItems == undefined) {
	      throw new Error('Property "uiItems" missing in item ' + data.id);
	    }
	    _this.id = v4();
	    _this.group = data.group;
	    _this._setupRange();
	    _this.emitter = _this.data.eventEmitter;
	    _this.range = _this.data.range;
	    _this.attached = false;
	    _this.isCluster = true;
	    _this.data.isCluster = true;
	    return _this;
	  }

	  
	  _createClass(ClusterItem, [{
	    key: "hasItems",
	    value: function hasItems() {
	      return this.data.uiItems && this.data.uiItems.length && this.attached;
	    }

	    
	  }, {
	    key: "setUiItems",
	    value: function setUiItems(items) {
	      this.detach();
	      this.data.uiItems = items;
	      this._setupRange();
	      this.attach();
	    }

	    
	  }, {
	    key: "isVisible",
	    value: function isVisible(range) {
	      var rangeWidth = this.data.end ? this.data.end - this.data.start : 0;
	      var widthInMs = this.width * range.getMillisecondsPerPixel();
	      var end = Math.max(this.data.start.getTime() + rangeWidth, this.data.start.getTime() + widthInMs);
	      return this.data.start < range.end && end > range.start && this.hasItems();
	    }

	    
	  }, {
	    key: "getData",
	    value: function getData() {
	      return {
	        isCluster: true,
	        id: this.id,
	        items: this.data.items || [],
	        data: this.data
	      };
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw(returnQueue) {
	      var _context, _context2, _context3, _context4, _context5, _context7;
	      var sizes;
	      var queue = [
	      
	      _bindInstanceProperty(_context = this._createDomElement).call(_context, this),
	      
	      _bindInstanceProperty(_context2 = this._appendDomElement).call(_context2, this),
	      
	      _bindInstanceProperty(_context3 = this._updateDirtyDomComponents).call(_context3, this), _bindInstanceProperty(_context4 = function _context4() {
	        if (this.dirty) {
	          sizes = this._getDomComponentsSizes();
	        }
	      }).call(_context4, this), _bindInstanceProperty(_context5 = function _context5() {
	        if (this.dirty) {
	          var _context6;
	          _bindInstanceProperty(_context6 = this._updateDomComponentsSizes).call(_context6, this)(sizes);
	        }
	      }).call(_context5, this),
	      
	      _bindInstanceProperty(_context7 = this._repaintDomAdditionals).call(_context7, this)];
	      if (returnQueue) {
	        return queue;
	      } else {
	        var result;
	        _forEachInstanceProperty(queue).call(queue, function (fn) {
	          result = fn();
	        });
	        return result;
	      }
	    }

	    
	  }, {
	    key: "show",
	    value: function show() {
	      if (!this.displayed) {
	        this.redraw();
	      }
	    }

	    
	  }, {
	    key: "hide",
	    value: function hide() {
	      if (this.displayed) {
	        var dom = this.dom;
	        if (dom.box.parentNode) {
	          dom.box.parentNode.removeChild(dom.box);
	        }
	        if (this.options.showStipes) {
	          if (dom.line.parentNode) {
	            dom.line.parentNode.removeChild(dom.line);
	          }
	          if (dom.dot.parentNode) {
	            dom.dot.parentNode.removeChild(dom.dot);
	          }
	        }
	        this.displayed = false;
	      }
	    }

	    
	  }, {
	    key: "repositionX",
	    value: function repositionX() {
	      var start = this.conversion.toScreen(this.data.start);
	      var end = this.data.end ? this.conversion.toScreen(this.data.end) : 0;
	      if (end) {
	        this.repositionXWithRanges(start, end);
	      } else {
	        var align = this.data.align === undefined ? this.options.align : this.data.align;
	        this.repositionXWithoutRanges(start, align);
	      }
	      if (this.options.showStipes) {
	        this.dom.line.style.display = this._isStipeVisible() ? 'block' : 'none';
	        this.dom.dot.style.display = this._isStipeVisible() ? 'block' : 'none';
	        if (this._isStipeVisible()) {
	          this.repositionStype(start, end);
	        }
	      }
	    }

	    
	  }, {
	    key: "repositionStype",
	    value: function repositionStype(start, end) {
	      this.dom.line.style.display = 'block';
	      this.dom.dot.style.display = 'block';
	      var lineOffsetWidth = this.dom.line.offsetWidth;
	      var dotOffsetWidth = this.dom.dot.offsetWidth;
	      if (end) {
	        var lineOffset = lineOffsetWidth + start + (end - start) / 2;
	        var dotOffset = lineOffset - dotOffsetWidth / 2;
	        var lineOffsetDirection = this.options.rtl ? lineOffset * -1 : lineOffset;
	        var dotOffsetDirection = this.options.rtl ? dotOffset * -1 : dotOffset;
	        this.dom.line.style.transform = "translateX(".concat(lineOffsetDirection, "px)");
	        this.dom.dot.style.transform = "translateX(".concat(dotOffsetDirection, "px)");
	      } else {
	        var _lineOffsetDirection = this.options.rtl ? start * -1 : start;
	        var _dotOffsetDirection = this.options.rtl ? (start - dotOffsetWidth / 2) * -1 : start - dotOffsetWidth / 2;
	        this.dom.line.style.transform = "translateX(".concat(_lineOffsetDirection, "px)");
	        this.dom.dot.style.transform = "translateX(".concat(_dotOffsetDirection, "px)");
	      }
	    }

	    
	  }, {
	    key: "repositionXWithoutRanges",
	    value: function repositionXWithoutRanges(start, align) {
	      
	      if (align == 'right') {
	        if (this.options.rtl) {
	          this.right = start - this.width;

	          
	          this.dom.box.style.right = this.right + 'px';
	        } else {
	          this.left = start - this.width;

	          
	          this.dom.box.style.left = this.left + 'px';
	        }
	      } else if (align == 'left') {
	        if (this.options.rtl) {
	          this.right = start;

	          
	          this.dom.box.style.right = this.right + 'px';
	        } else {
	          this.left = start;

	          
	          this.dom.box.style.left = this.left + 'px';
	        }
	      } else {
	        
	        if (this.options.rtl) {
	          this.right = start - this.width / 2;

	          
	          this.dom.box.style.right = this.right + 'px';
	        } else {
	          this.left = start - this.width / 2;

	          
	          this.dom.box.style.left = this.left + 'px';
	        }
	      }
	    }

	    
	  }, {
	    key: "repositionXWithRanges",
	    value: function repositionXWithRanges(start, end) {
	      var boxWidth = Math.round(Math.max(end - start + 0.5, 1));
	      if (this.options.rtl) {
	        this.right = start;
	      } else {
	        this.left = start;
	      }
	      this.width = Math.max(boxWidth, this.minWidth || 0);
	      if (this.options.rtl) {
	        this.dom.box.style.right = this.right + 'px';
	      } else {
	        this.dom.box.style.left = this.left + 'px';
	      }
	      this.dom.box.style.width = boxWidth + 'px';
	    }

	    
	  }, {
	    key: "repositionY",
	    value: function repositionY() {
	      var orientation = this.options.orientation.item;
	      var box = this.dom.box;
	      if (orientation == 'top') {
	        box.style.top = (this.top || 0) + 'px';
	      } else {
	        
	        box.style.top = (this.parent.height - this.top - this.height || 0) + 'px';
	      }
	      if (this.options.showStipes) {
	        if (orientation == 'top') {
	          this.dom.line.style.top = '0';
	          this.dom.line.style.height = this.parent.top + this.top + 1 + 'px';
	          this.dom.line.style.bottom = '';
	        } else {
	          
	          var itemSetHeight = this.parent.itemSet.props.height;
	          var lineHeight = itemSetHeight - this.parent.top - this.parent.height + this.top;
	          this.dom.line.style.top = itemSetHeight - lineHeight + 'px';
	          this.dom.line.style.bottom = '0';
	        }
	        this.dom.dot.style.top = -this.dom.dot.offsetHeight / 2 + 'px';
	      }
	    }

	    
	  }, {
	    key: "getWidthLeft",
	    value: function getWidthLeft() {
	      return this.width / 2;
	    }

	    
	  }, {
	    key: "getWidthRight",
	    value: function getWidthRight() {
	      return this.width / 2;
	    }

	    
	  }, {
	    key: "move",
	    value: function move() {
	      this.repositionX();
	      this.repositionY();
	    }

	    
	  }, {
	    key: "attach",
	    value: function attach() {
	      var _context8;
	      var _iterator = _createForOfIteratorHelper$3(this.data.uiItems),
	        _step;
	      try {
	        for (_iterator.s(); !(_step = _iterator.n()).done;) {
	          var item = _step.value;
	          item.cluster = this;
	        }
	      } catch (err) {
	        _iterator.e(err);
	      } finally {
	        _iterator.f();
	      }
	      this.data.items = _mapInstanceProperty(_context8 = this.data.uiItems).call(_context8, function (item) {
	        return item.data;
	      });
	      this.attached = true;
	      this.dirty = true;
	    }

	    
	  }, {
	    key: "detach",
	    value: function detach() {
	      var detachFromParent = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : false;
	      if (!this.hasItems()) {
	        return;
	      }
	      var _iterator2 = _createForOfIteratorHelper$3(this.data.uiItems),
	        _step2;
	      try {
	        for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
	          var item = _step2.value;
	          delete item.cluster;
	        }
	      } catch (err) {
	        _iterator2.e(err);
	      } finally {
	        _iterator2.f();
	      }
	      this.attached = false;
	      if (detachFromParent && this.group) {
	        this.group.remove(this);
	        this.group = null;
	      }
	      this.data.items = [];
	      this.dirty = true;
	    }

	    
	  }, {
	    key: "_onDoubleClick",
	    value: function _onDoubleClick() {
	      this._fit();
	    }

	    
	  }, {
	    key: "_setupRange",
	    value: function _setupRange() {
	      var _context9, _context10, _context11;
	      var stats = _mapInstanceProperty(_context9 = this.data.uiItems).call(_context9, function (item) {
	        return {
	          start: item.data.start.valueOf(),
	          end: item.data.end ? item.data.end.valueOf() : item.data.start.valueOf()
	        };
	      });
	      this.data.min = Math.min.apply(Math, _toConsumableArray(_mapInstanceProperty(stats).call(stats, function (s) {
	        return Math.min(s.start, s.end || s.start);
	      })));
	      this.data.max = Math.max.apply(Math, _toConsumableArray(_mapInstanceProperty(stats).call(stats, function (s) {
	        return Math.max(s.start, s.end || s.start);
	      })));
	      var centers = _mapInstanceProperty(_context10 = this.data.uiItems).call(_context10, function (item) {
	        return item.center;
	      });
	      var avg = _reduceInstanceProperty(centers).call(centers, function (sum, value) {
	        return sum + value;
	      }, 0) / this.data.uiItems.length;
	      if (_someInstanceProperty(_context11 = this.data.uiItems).call(_context11, function (item) {
	        return item.data.end;
	      })) {
	        
	        this.data.start = new Date(this.data.min);
	        this.data.end = new Date(this.data.max);
	      } else {
	        this.data.start = new Date(avg);
	        this.data.end = null;
	      }
	    }

	    
	  }, {
	    key: "_getUiItems",
	    value: function _getUiItems() {
	      var _this2 = this;
	      if (this.data.uiItems && this.data.uiItems.length) {
	        var _context12;
	        return _filterInstanceProperty(_context12 = this.data.uiItems).call(_context12, function (item) {
	          return item.cluster === _this2;
	        });
	      }
	      return [];
	    }

	    
	  }, {
	    key: "_createDomElement",
	    value: function _createDomElement() {
	      if (!this.dom) {
	        
	        this.dom = {};

	        
	        this.dom.box = document.createElement('DIV');

	        
	        this.dom.content = document.createElement('DIV');
	        this.dom.content.className = 'vis-item-content';
	        this.dom.box.appendChild(this.dom.content);
	        if (this.options.showStipes) {
	          
	          this.dom.line = document.createElement('DIV');
	          this.dom.line.className = 'vis-cluster-line';
	          this.dom.line.style.display = 'none';

	          
	          this.dom.dot = document.createElement('DIV');
	          this.dom.dot.className = 'vis-cluster-dot';
	          this.dom.dot.style.display = 'none';
	        }
	        if (this.options.fitOnDoubleClick) {
	          var _context13;
	          this.dom.box.ondblclick = _bindInstanceProperty(_context13 = ClusterItem.prototype._onDoubleClick).call(_context13, this);
	        }

	        
	        this.dom.box['vis-item'] = this;
	        this.dirty = true;
	      }
	    }

	    
	  }, {
	    key: "_appendDomElement",
	    value: function _appendDomElement() {
	      if (!this.parent) {
	        throw new Error('Cannot redraw item: no parent attached');
	      }
	      if (!this.dom.box.parentNode) {
	        var foreground = this.parent.dom.foreground;
	        if (!foreground) {
	          throw new Error('Cannot redraw item: parent has no foreground container element');
	        }
	        foreground.appendChild(this.dom.box);
	      }
	      var background = this.parent.dom.background;
	      if (this.options.showStipes) {
	        if (!this.dom.line.parentNode) {
	          if (!background) throw new Error('Cannot redraw item: parent has no background container element');
	          background.appendChild(this.dom.line);
	        }
	        if (!this.dom.dot.parentNode) {
	          var axis = this.parent.dom.axis;
	          if (!background) throw new Error('Cannot redraw item: parent has no axis container element');
	          axis.appendChild(this.dom.dot);
	        }
	      }
	      this.displayed = true;
	    }

	    
	  }, {
	    key: "_updateDirtyDomComponents",
	    value: function _updateDirtyDomComponents() {
	      
	      
	      
	      
	      if (this.dirty) {
	        this._updateContents(this.dom.content);
	        this._updateDataAttributes(this.dom.box);
	        this._updateStyle(this.dom.box);

	        
	        var className = this.baseClassName + ' ' + (this.data.className ? ' ' + this.data.className : '') + (this.selected ? ' vis-selected' : '') + ' vis-readonly';
	        this.dom.box.className = 'vis-item ' + className;
	        if (this.options.showStipes) {
	          this.dom.line.className = 'vis-item vis-cluster-line ' + (this.selected ? ' vis-selected' : '');
	          this.dom.dot.className = 'vis-item vis-cluster-dot ' + (this.selected ? ' vis-selected' : '');
	        }
	        if (this.data.end) {
	          
	          
	          this.dom.content.style.maxWidth = 'none';
	        }
	      }
	    }

	    
	  }, {
	    key: "_getDomComponentsSizes",
	    value: function _getDomComponentsSizes() {
	      var sizes = {
	        previous: {
	          right: this.dom.box.style.right,
	          left: this.dom.box.style.left
	        },
	        box: {
	          width: this.dom.box.offsetWidth,
	          height: this.dom.box.offsetHeight
	        }
	      };
	      if (this.options.showStipes) {
	        sizes.dot = {
	          height: this.dom.dot.offsetHeight,
	          width: this.dom.dot.offsetWidth
	        };
	        sizes.line = {
	          width: this.dom.line.offsetWidth
	        };
	      }
	      return sizes;
	    }

	    
	  }, {
	    key: "_updateDomComponentsSizes",
	    value: function _updateDomComponentsSizes(sizes) {
	      if (this.options.rtl) {
	        this.dom.box.style.right = "0px";
	      } else {
	        this.dom.box.style.left = "0px";
	      }

	      
	      if (!this.data.end) {
	        this.width = sizes.box.width;
	      } else {
	        this.minWidth = sizes.box.width;
	      }
	      this.height = sizes.box.height;

	      
	      if (this.options.rtl) {
	        this.dom.box.style.right = sizes.previous.right;
	      } else {
	        this.dom.box.style.left = sizes.previous.left;
	      }
	      this.dirty = false;
	    }

	    
	  }, {
	    key: "_repaintDomAdditionals",
	    value: function _repaintDomAdditionals() {
	      this._repaintOnItemUpdateTimeTooltip(this.dom.box);
	    }

	    
	  }, {
	    key: "_isStipeVisible",
	    value: function _isStipeVisible() {
	      return this.minWidth >= this.width || !this.data.end;
	    }

	    
	  }, {
	    key: "_getFitRange",
	    value: function _getFitRange() {
	      var offset = 0.05 * (this.data.max - this.data.min) / 2;
	      return {
	        fitStart: this.data.min - offset,
	        fitEnd: this.data.max + offset
	      };
	    }

	    
	  }, {
	    key: "_fit",
	    value: function _fit() {
	      if (this.emitter) {
	        var _this$_getFitRange = this._getFitRange(),
	          fitStart = _this$_getFitRange.fitStart,
	          fitEnd = _this$_getFitRange.fitEnd;
	        var fitArgs = {
	          start: new Date(fitStart),
	          end: new Date(fitEnd),
	          animation: true
	        };
	        this.emitter.emit('fit', fitArgs);
	      }
	    }

	    
	  }, {
	    key: "_getItemData",
	    value: function _getItemData() {
	      return this.data;
	    }
	  }]);
	  return ClusterItem;
	}(Item);
	ClusterItem.prototype.baseClassName = 'vis-item vis-range vis-cluster';

	function _createForOfIteratorHelper$2(o, allowArrayLike) { var it = typeof _Symbol !== "undefined" && _getIteratorMethod(o) || o["@@iterator"]; if (!it) { if (_Array$isArray$1(o) || (it = _unsupportedIterableToArray$2(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }
	function _unsupportedIterableToArray$2(o, minLen) { var _context4; if (!o) return; if (typeof o === "string") return _arrayLikeToArray$2(o, minLen); var n = _sliceInstanceProperty(_context4 = Object.prototype.toString.call(o)).call(_context4, 8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return _Array$from$1(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray$2(o, minLen); }
	function _arrayLikeToArray$2(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
	var UNGROUPED$2 = '__ungrouped__'; 
	var BACKGROUND$1 = '__background__'; 

	var ReservedGroupIds = {
	  UNGROUPED: UNGROUPED$2,
	  BACKGROUND: BACKGROUND$1
	};

	
	var ClusterGenerator = function () {
	  
	  function ClusterGenerator(itemSet) {
	    _classCallCheck(this, ClusterGenerator);
	    this.itemSet = itemSet;
	    this.groups = {};
	    this.cache = {};
	    this.cache[-1] = [];
	  }

	  
	  _createClass(ClusterGenerator, [{
	    key: "createClusterItem",
	    value: function createClusterItem(itemData, conversion, options) {
	      var newItem = new ClusterItem(itemData, conversion, options);
	      return newItem;
	    }

	    
	  }, {
	    key: "setItems",
	    value: function setItems(items, options) {
	      this.items = items || [];
	      this.dataChanged = true;
	      this.applyOnChangedLevel = false;
	      if (options && options.applyOnChangedLevel) {
	        this.applyOnChangedLevel = options.applyOnChangedLevel;
	      }
	    }

	    
	  }, {
	    key: "updateData",
	    value: function updateData() {
	      this.dataChanged = true;
	      this.applyOnChangedLevel = false;
	    }

	    
	  }, {
	    key: "getClusters",
	    value: function getClusters(oldClusters, scale, options) {
	      var _ref = typeof options === "boolean" ? {} : options,
	        maxItems = _ref.maxItems,
	        clusterCriteria = _ref.clusterCriteria;
	      if (!clusterCriteria) {
	        clusterCriteria = function clusterCriteria() {
	          return true;
	        };
	      }
	      maxItems = maxItems || 1;
	      var level = -1;
	      var granularity = 2;
	      var timeWindow = 0;
	      if (scale > 0) {
	        if (scale >= 1) {
	          return [];
	        }
	        level = Math.abs(Math.round(Math.log(100 / scale) / Math.log(granularity)));
	        timeWindow = Math.abs(Math.pow(granularity, level));
	      }

	      
	      if (this.dataChanged) {
	        var levelChanged = level != this.cacheLevel;
	        var applyDataNow = this.applyOnChangedLevel ? levelChanged : true;
	        if (applyDataNow) {
	          this._dropLevelsCache();
	          this._filterData();
	        }
	      }
	      this.cacheLevel = level;
	      var clusters = this.cache[level];
	      if (!clusters) {
	        clusters = [];
	        for (var groupName in this.groups) {
	          if (this.groups.hasOwnProperty(groupName)) {
	            var items = this.groups[groupName];
	            var iMax = items.length;
	            var i = 0;
	            while (i < iMax) {
	              
	              var item = items[i];
	              var neighbors = 1; 

	              
	              var j = i - 1;
	              while (j >= 0 && item.center - items[j].center < timeWindow / 2) {
	                if (!items[j].cluster && clusterCriteria(item.data, items[j].data)) {
	                  neighbors++;
	                }
	                j--;
	              }

	              
	              var k = i + 1;
	              while (k < items.length && items[k].center - item.center < timeWindow / 2) {
	                if (clusterCriteria(item.data, items[k].data)) {
	                  neighbors++;
	                }
	                k++;
	              }

	              
	              var l = clusters.length - 1;
	              while (l >= 0 && item.center - clusters[l].center < timeWindow) {
	                if (item.group == clusters[l].group && clusterCriteria(item.data, clusters[l].data)) {
	                  neighbors++;
	                }
	                l--;
	              }

	              
	              if (neighbors > maxItems) {
	                
	                var num = neighbors - maxItems + 1;
	                var clusterItems = [];

	                
	                
	                var m = i;
	                while (clusterItems.length < num && m < items.length) {
	                  if (clusterCriteria(items[i].data, items[m].data)) {
	                    clusterItems.push(items[m]);
	                  }
	                  m++;
	                }
	                var groupId = this.itemSet.getGroupId(item.data);
	                var group = this.itemSet.groups[groupId] || this.itemSet.groups[ReservedGroupIds.UNGROUPED];
	                var cluster = this._getClusterForItems(clusterItems, group, oldClusters, options);
	                clusters.push(cluster);
	                i += num;
	              } else {
	                delete item.cluster;
	                i += 1;
	              }
	            }
	          }
	        }
	        this.cache[level] = clusters;
	      }
	      return clusters;
	    }

	    
	  }, {
	    key: "_filterData",
	    value: function _filterData() {
	      
	      var groups = {};
	      this.groups = groups;

	      
	      for (var _i = 0, _Object$values = _Object$values2(this.items); _i < _Object$values.length; _i++) {
	        var item = _Object$values[_i];
	        
	        var groupName = item.parent ? item.parent.groupId : '';
	        var group = groups[groupName];
	        if (!group) {
	          group = [];
	          groups[groupName] = group;
	        }
	        group.push(item);

	        
	        if (item.data.start) {
	          if (item.data.end) {
	            
	            item.center = (item.data.start.valueOf() + item.data.end.valueOf()) / 2;
	          } else {
	            
	            item.center = item.data.start.valueOf();
	          }
	        }
	      }

	      
	      for (var currentGroupName in groups) {
	        if (groups.hasOwnProperty(currentGroupName)) {
	          var _context;
	          _sortInstanceProperty(_context = groups[currentGroupName]).call(_context, function (a, b) {
	            return a.center - b.center;
	          });
	        }
	      }
	      this.dataChanged = false;
	    }

	    
	  }, {
	    key: "_getClusterForItems",
	    value: function _getClusterForItems(clusterItems, group, oldClusters, options) {
	      var _context2;
	      var oldClustersLookup = _mapInstanceProperty(_context2 = oldClusters || []).call(_context2, function (cluster) {
	        var _context3;
	        return {
	          cluster: cluster,
	          itemsIds: new _Set(_mapInstanceProperty(_context3 = cluster.data.uiItems).call(_context3, function (item) {
	            return item.id;
	          }))
	        };
	      });
	      var cluster;
	      if (oldClustersLookup.length) {
	        var _iterator = _createForOfIteratorHelper$2(oldClustersLookup),
	          _step;
	        try {
	          var _loop = function _loop() {
	            var oldClusterData = _step.value;
	            if (oldClusterData.itemsIds.size === clusterItems.length && _everyInstanceProperty(clusterItems).call(clusterItems, function (clusterItem) {
	              return oldClusterData.itemsIds.has(clusterItem.id);
	            })) {
	              cluster = oldClusterData.cluster;
	              return "break";
	            }
	          };
	          for (_iterator.s(); !(_step = _iterator.n()).done;) {
	            var _ret = _loop();
	            if (_ret === "break") break;
	          }
	        } catch (err) {
	          _iterator.e(err);
	        } finally {
	          _iterator.f();
	        }
	      }
	      if (cluster) {
	        cluster.setUiItems(clusterItems);
	        if (cluster.group !== group) {
	          if (cluster.group) {
	            cluster.group.remove(cluster);
	          }
	          if (group) {
	            group.add(cluster);
	            cluster.group = group;
	          }
	        }
	        return cluster;
	      }
	      var titleTemplate = options.titleTemplate || '';
	      var conversion = {
	        toScreen: this.itemSet.body.util.toScreen,
	        toTime: this.itemSet.body.util.toTime
	      };
	      var title = titleTemplate.replace(/{count}/, clusterItems.length);
	      var clusterContent = '<div title="' + title + '">' + clusterItems.length + '</div>';
	      var clusterOptions = _Object$assign({}, options, this.itemSet.options);
	      var data = {
	        'content': clusterContent,
	        'title': title,
	        'group': group,
	        'uiItems': clusterItems,
	        'eventEmitter': this.itemSet.body.emitter,
	        'range': this.itemSet.body.range
	      };
	      cluster = this.createClusterItem(data, conversion, clusterOptions);
	      if (group) {
	        group.add(cluster);
	        cluster.group = group;
	      }
	      cluster.attach();
	      return cluster;
	    }

	    
	  }, {
	    key: "_dropLevelsCache",
	    value: function _dropLevelsCache() {
	      this.cache = {};
	      this.cacheLevel = -1;
	      this.cache[this.cacheLevel] = [];
	    }
	  }]);
	  return ClusterGenerator;
	}();

	function _createForOfIteratorHelper$1(o, allowArrayLike) { var it = typeof _Symbol !== "undefined" && _getIteratorMethod(o) || o["@@iterator"]; if (!it) { if (_Array$isArray$1(o) || (it = _unsupportedIterableToArray$1(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }
	function _unsupportedIterableToArray$1(o, minLen) { var _context34; if (!o) return; if (typeof o === "string") return _arrayLikeToArray$1(o, minLen); var n = _sliceInstanceProperty(_context34 = Object.prototype.toString.call(o)).call(_context34, 8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return _Array$from$1(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray$1(o, minLen); }
	function _arrayLikeToArray$1(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
	function _createSuper$2(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$2(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$2() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }
	var UNGROUPED$1 = '__ungrouped__'; 
	var BACKGROUND = '__background__'; 

	
	var ItemSet = function (_Component) {
	  _inherits(ItemSet, _Component);
	  var _super = _createSuper$2(ItemSet);
	  
	  function ItemSet(body, options) {
	    var _this;
	    _classCallCheck(this, ItemSet);
	    _this = _super.call(this);
	    _this.body = body;
	    _this.defaultOptions = {
	      type: null,
	      
	      orientation: {
	        item: 'bottom' 
	      },

	      align: 'auto',
	      
	      stack: true,
	      stackSubgroups: true,
	      groupOrderSwap: function groupOrderSwap(fromGroup, toGroup, groups) {
	        
	        var targetOrder = toGroup.order;
	        toGroup.order = fromGroup.order;
	        fromGroup.order = targetOrder;
	      },
	      groupOrder: 'order',
	      selectable: true,
	      multiselect: false,
	      longSelectPressTime: 251,
	      itemsAlwaysDraggable: {
	        item: false,
	        range: false
	      },
	      editable: {
	        updateTime: false,
	        updateGroup: false,
	        add: false,
	        remove: false,
	        overrideItems: false
	      },
	      groupEditable: {
	        order: false,
	        add: false,
	        remove: false
	      },
	      snap: TimeStep.snap,
	      
	      onDropObjectOnItem: function onDropObjectOnItem(objectData, item, callback) {
	        callback(item);
	      },
	      onAdd: function onAdd(item, callback) {
	        callback(item);
	      },
	      onUpdate: function onUpdate(item, callback) {
	        callback(item);
	      },
	      onMove: function onMove(item, callback) {
	        callback(item);
	      },
	      onRemove: function onRemove(item, callback) {
	        callback(item);
	      },
	      onMoving: function onMoving(item, callback) {
	        callback(item);
	      },
	      onAddGroup: function onAddGroup(item, callback) {
	        callback(item);
	      },
	      onMoveGroup: function onMoveGroup(item, callback) {
	        callback(item);
	      },
	      onRemoveGroup: function onRemoveGroup(item, callback) {
	        callback(item);
	      },
	      margin: {
	        item: {
	          horizontal: 10,
	          vertical: 10
	        },
	        axis: 20
	      },
	      showTooltips: true,
	      tooltip: {
	        followMouse: false,
	        overflowMethod: 'flip',
	        delay: 500
	      },
	      tooltipOnItemUpdateTime: false
	    };

	    
	    _this.options = availableUtils.extend({}, _this.defaultOptions);
	    _this.options.rtl = options.rtl;
	    _this.options.onTimeout = options.onTimeout;
	    _this.conversion = {
	      toScreen: body.util.toScreen,
	      toTime: body.util.toTime
	    };
	    _this.dom = {};
	    _this.props = {};
	    _this.hammer = null;
	    var me = _assertThisInitialized$1(_this);
	    _this.itemsData = null; 
	    _this.groupsData = null; 
	    _this.itemsSettingTime = null;
	    _this.initialItemSetDrawn = false;
	    _this.userContinueNotBail = null;
	    _this.sequentialSelection = false;

	    
	    _this.itemListeners = {
	      'add': function add(event, params, senderId) {
	        
	        me._onAdd(params.items);
	        if (me.options.cluster) {
	          me.clusterGenerator.setItems(me.items, {
	            applyOnChangedLevel: false
	          });
	        }
	        me.redraw();
	      },
	      'update': function update(event, params, senderId) {
	        
	        me._onUpdate(params.items);
	        if (me.options.cluster) {
	          me.clusterGenerator.setItems(me.items, {
	            applyOnChangedLevel: false
	          });
	        }
	        me.redraw();
	      },
	      'remove': function remove(event, params, senderId) {
	        
	        me._onRemove(params.items);
	        if (me.options.cluster) {
	          me.clusterGenerator.setItems(me.items, {
	            applyOnChangedLevel: false
	          });
	        }
	        me.redraw();
	      }
	    };

	    
	    _this.groupListeners = {
	      'add': function add(event, params, senderId) {
	        
	        me._onAddGroups(params.items);
	        if (me.groupsData && me.groupsData.length > 0) {
	          var _context;
	          var groupsData = me.groupsData.getDataSet();
	          _forEachInstanceProperty(_context = groupsData.get()).call(_context, function (groupData) {
	            if (groupData.nestedGroups) {
	              var _context2;
	              if (groupData.showNested != false) {
	                groupData.showNested = true;
	              }
	              var updatedGroups = [];
	              _forEachInstanceProperty(_context2 = groupData.nestedGroups).call(_context2, function (nestedGroupId) {
	                var updatedNestedGroup = groupsData.get(nestedGroupId);
	                if (!updatedNestedGroup) {
	                  return;
	                }
	                updatedNestedGroup.nestedInGroup = groupData.id;
	                if (groupData.showNested == false) {
	                  updatedNestedGroup.visible = false;
	                }
	                updatedGroups = _concatInstanceProperty(updatedGroups).call(updatedGroups, updatedNestedGroup);
	              });
	              groupsData.update(updatedGroups, senderId);
	            }
	          });
	        }
	      },
	      'update': function update(event, params, senderId) {
	        
	        me._onUpdateGroups(params.items);
	      },
	      'remove': function remove(event, params, senderId) {
	        
	        me._onRemoveGroups(params.items);
	      }
	    };
	    _this.items = {}; 
	    _this.groups = {}; 
	    _this.groupIds = [];
	    _this.selection = []; 

	    _this.popup = null;
	    _this.popupTimer = null;
	    _this.touchParams = {}; 
	    _this.groupTouchParams = {
	      group: null,
	      isDragging: false
	    };

	    
	    _this._create();
	    _this.setOptions(options);
	    _this.clusters = [];
	    return _this;
	  }

	  
	  _createClass(ItemSet, [{
	    key: "_create",
	    value: function _create() {
	      var _this2 = this,
	        _context3,
	        _context4,
	        _context5,
	        _context6,
	        _context7,
	        _context8,
	        _context9,
	        _context10,
	        _context11,
	        _context12,
	        _context13,
	        _context14,
	        _context15,
	        _context16,
	        _context17;
	      var frame = document.createElement('div');
	      frame.className = 'vis-itemset';
	      frame['vis-itemset'] = this;
	      this.dom.frame = frame;

	      
	      var background = document.createElement('div');
	      background.className = 'vis-background';
	      frame.appendChild(background);
	      this.dom.background = background;

	      
	      var foreground = document.createElement('div');
	      foreground.className = 'vis-foreground';
	      frame.appendChild(foreground);
	      this.dom.foreground = foreground;

	      
	      var axis = document.createElement('div');
	      axis.className = 'vis-axis';
	      this.dom.axis = axis;

	      
	      var labelSet = document.createElement('div');
	      labelSet.className = 'vis-labelset';
	      this.dom.labelSet = labelSet;

	      
	      this._updateUngrouped();

	      
	      var backgroundGroup = new BackgroundGroup(BACKGROUND, null, this);
	      backgroundGroup.show();
	      this.groups[BACKGROUND] = backgroundGroup;

	      
	      
	      
	      
	      this.hammer = new Hammer(this.body.dom.centerContainer);

	      
	      this.hammer.on('hammer.input', function (event) {
	        if (event.isFirst) {
	          _this2._onTouch(event);
	        }
	      });
	      this.hammer.on('panstart', _bindInstanceProperty(_context3 = this._onDragStart).call(_context3, this));
	      this.hammer.on('panmove', _bindInstanceProperty(_context4 = this._onDrag).call(_context4, this));
	      this.hammer.on('panend', _bindInstanceProperty(_context5 = this._onDragEnd).call(_context5, this));
	      this.hammer.get('pan').set({
	        threshold: 5,
	        direction: Hammer.ALL
	      });
	      
	      this.hammer.get('press').set({
	        time: 10000
	      });

	      
	      this.hammer.on('tap', _bindInstanceProperty(_context6 = this._onSelectItem).call(_context6, this));

	      
	      this.hammer.on('press', _bindInstanceProperty(_context7 = this._onMultiSelectItem).call(_context7, this));
	      
	      this.hammer.get('press').set({
	        time: 10000
	      });

	      
	      this.hammer.on('doubletap', _bindInstanceProperty(_context8 = this._onAddItem).call(_context8, this));
	      if (this.options.rtl) {
	        this.groupHammer = new Hammer(this.body.dom.rightContainer);
	      } else {
	        this.groupHammer = new Hammer(this.body.dom.leftContainer);
	      }
	      this.groupHammer.on('tap', _bindInstanceProperty(_context9 = this._onGroupClick).call(_context9, this));
	      this.groupHammer.on('panstart', _bindInstanceProperty(_context10 = this._onGroupDragStart).call(_context10, this));
	      this.groupHammer.on('panmove', _bindInstanceProperty(_context11 = this._onGroupDrag).call(_context11, this));
	      this.groupHammer.on('panend', _bindInstanceProperty(_context12 = this._onGroupDragEnd).call(_context12, this));
	      this.groupHammer.get('pan').set({
	        threshold: 5,
	        direction: Hammer.DIRECTION_VERTICAL
	      });
	      this.body.dom.centerContainer.addEventListener('mouseover', _bindInstanceProperty(_context13 = this._onMouseOver).call(_context13, this));
	      this.body.dom.centerContainer.addEventListener('mouseout', _bindInstanceProperty(_context14 = this._onMouseOut).call(_context14, this));
	      this.body.dom.centerContainer.addEventListener('mousemove', _bindInstanceProperty(_context15 = this._onMouseMove).call(_context15, this));
	      
	      this.body.dom.centerContainer.addEventListener('contextmenu', _bindInstanceProperty(_context16 = this._onDragEnd).call(_context16, this));
	      this.body.dom.centerContainer.addEventListener('mousewheel', _bindInstanceProperty(_context17 = this._onMouseWheel).call(_context17, this));

	      
	      this.show();
	    }

	    
	  }, {
	    key: "setOptions",
	    value: function setOptions(options) {
	      var _this3 = this;
	      if (options) {
	        var _context18, _context20;
	        
	        var fields = ['type', 'rtl', 'align', 'order', 'stack', 'stackSubgroups', 'selectable', 'multiselect', 'sequentialSelection', 'multiselectPerGroup', 'longSelectPressTime', 'groupOrder', 'dataAttributes', 'template', 'groupTemplate', 'visibleFrameTemplate', 'hide', 'snap', 'groupOrderSwap', 'showTooltips', 'tooltip', 'tooltipOnItemUpdateTime', 'groupHeightMode', 'onTimeout'];
	        availableUtils.selectiveExtend(fields, this.options, options);
	        if ('itemsAlwaysDraggable' in options) {
	          if (typeof options.itemsAlwaysDraggable === 'boolean') {
	            this.options.itemsAlwaysDraggable.item = options.itemsAlwaysDraggable;
	            this.options.itemsAlwaysDraggable.range = false;
	          } else if (_typeof(options.itemsAlwaysDraggable) === 'object') {
	            availableUtils.selectiveExtend(['item', 'range'], this.options.itemsAlwaysDraggable, options.itemsAlwaysDraggable);
	            
	            if (!this.options.itemsAlwaysDraggable.item) {
	              this.options.itemsAlwaysDraggable.range = false;
	            }
	          }
	        }
	        if ('sequentialSelection' in options) {
	          if (typeof options.sequentialSelection === 'boolean') {
	            this.options.sequentialSelection = options.sequentialSelection;
	          }
	        }
	        if ('orientation' in options) {
	          if (typeof options.orientation === 'string') {
	            this.options.orientation.item = options.orientation === 'top' ? 'top' : 'bottom';
	          } else if (_typeof(options.orientation) === 'object' && 'item' in options.orientation) {
	            this.options.orientation.item = options.orientation.item;
	          }
	        }
	        if ('margin' in options) {
	          if (typeof options.margin === 'number') {
	            this.options.margin.axis = options.margin;
	            this.options.margin.item.horizontal = options.margin;
	            this.options.margin.item.vertical = options.margin;
	          } else if (_typeof(options.margin) === 'object') {
	            availableUtils.selectiveExtend(['axis'], this.options.margin, options.margin);
	            if ('item' in options.margin) {
	              if (typeof options.margin.item === 'number') {
	                this.options.margin.item.horizontal = options.margin.item;
	                this.options.margin.item.vertical = options.margin.item;
	              } else if (_typeof(options.margin.item) === 'object') {
	                availableUtils.selectiveExtend(['horizontal', 'vertical'], this.options.margin.item, options.margin.item);
	              }
	            }
	          }
	        }
	        _forEachInstanceProperty(_context18 = ['locale', 'locales']).call(_context18, function (key) {
	          if (key in options) {
	            _this3.options[key] = options[key];
	          }
	        });
	        if ('editable' in options) {
	          if (typeof options.editable === 'boolean') {
	            this.options.editable.updateTime = options.editable;
	            this.options.editable.updateGroup = options.editable;
	            this.options.editable.add = options.editable;
	            this.options.editable.remove = options.editable;
	            this.options.editable.overrideItems = false;
	          } else if (_typeof(options.editable) === 'object') {
	            availableUtils.selectiveExtend(['updateTime', 'updateGroup', 'add', 'remove', 'overrideItems'], this.options.editable, options.editable);
	          }
	        }
	        if ('groupEditable' in options) {
	          if (typeof options.groupEditable === 'boolean') {
	            this.options.groupEditable.order = options.groupEditable;
	            this.options.groupEditable.add = options.groupEditable;
	            this.options.groupEditable.remove = options.groupEditable;
	          } else if (_typeof(options.groupEditable) === 'object') {
	            availableUtils.selectiveExtend(['order', 'add', 'remove'], this.options.groupEditable, options.groupEditable);
	          }
	        }

	        
	        var addCallback = function addCallback(name) {
	          var fn = options[name];
	          if (fn) {
	            if (!(typeof fn === 'function')) {
	              var _context19;
	              throw new Error(_concatInstanceProperty(_context19 = "option ".concat(name, " must be a function ")).call(_context19, name, "(item, callback)"));
	            }
	            _this3.options[name] = fn;
	          }
	        };
	        _forEachInstanceProperty(_context20 = ['onDropObjectOnItem', 'onAdd', 'onUpdate', 'onRemove', 'onMove', 'onMoving', 'onAddGroup', 'onMoveGroup', 'onRemoveGroup']).call(_context20, addCallback);
	        if (options.cluster) {
	          _Object$assign(this.options, {
	            cluster: options.cluster
	          });
	          if (!this.clusterGenerator) {
	            this.clusterGenerator = new ClusterGenerator(this);
	          }
	          this.clusterGenerator.setItems(this.items, {
	            applyOnChangedLevel: false
	          });
	          this.markDirty({
	            refreshItems: true,
	            restackGroups: true
	          });
	          this.redraw();
	        } else if (this.clusterGenerator) {
	          this._detachAllClusters();
	          this.clusters = [];
	          this.clusterGenerator = null;
	          this.options.cluster = undefined;
	          this.markDirty({
	            refreshItems: true,
	            restackGroups: true
	          });
	          this.redraw();
	        } else {
	          
	          this.markDirty();
	        }
	      }
	    }

	    
	  }, {
	    key: "markDirty",
	    value: function markDirty(options) {
	      this.groupIds = [];
	      if (options) {
	        if (options.refreshItems) {
	          _forEachInstanceProperty(availableUtils).call(availableUtils, this.items, function (item) {
	            item.dirty = true;
	            if (item.displayed) item.redraw();
	          });
	        }
	        if (options.restackGroups) {
	          _forEachInstanceProperty(availableUtils).call(availableUtils, this.groups, function (group, key) {
	            if (key === BACKGROUND) return;
	            group.stackDirty = true;
	          });
	        }
	      }
	    }

	    
	  }, {
	    key: "destroy",
	    value: function destroy() {
	      this.clearPopupTimer();
	      this.hide();
	      this.setItems(null);
	      this.setGroups(null);
	      this.hammer && this.hammer.destroy();
	      this.groupHammer && this.groupHammer.destroy();
	      this.hammer = null;
	      this.body = null;
	      this.conversion = null;
	    }

	    
	  }, {
	    key: "hide",
	    value: function hide() {
	      
	      if (this.dom.frame.parentNode) {
	        this.dom.frame.parentNode.removeChild(this.dom.frame);
	      }

	      
	      if (this.dom.axis.parentNode) {
	        this.dom.axis.parentNode.removeChild(this.dom.axis);
	      }

	      
	      if (this.dom.labelSet.parentNode) {
	        this.dom.labelSet.parentNode.removeChild(this.dom.labelSet);
	      }
	    }

	    
	  }, {
	    key: "show",
	    value: function show() {
	      
	      if (!this.dom.frame.parentNode) {
	        this.body.dom.center.appendChild(this.dom.frame);
	      }

	      
	      if (!this.dom.axis.parentNode) {
	        this.body.dom.backgroundVertical.appendChild(this.dom.axis);
	      }

	      
	      if (!this.dom.labelSet.parentNode) {
	        if (this.options.rtl) {
	          this.body.dom.right.appendChild(this.dom.labelSet);
	        } else {
	          this.body.dom.left.appendChild(this.dom.labelSet);
	        }
	      }
	    }

	    
	  }, {
	    key: "setPopupTimer",
	    value: function setPopupTimer(popup) {
	      this.clearPopupTimer();
	      if (popup) {
	        var delay = this.options.tooltip.delay || typeof this.options.tooltip.delay === 'number' ? this.options.tooltip.delay : 500;
	        this.popupTimer = _setTimeout(function () {
	          popup.show();
	        }, delay);
	      }
	    }

	    
	  }, {
	    key: "clearPopupTimer",
	    value: function clearPopupTimer() {
	      if (this.popupTimer != null) {
	        clearTimeout(this.popupTimer);
	        this.popupTimer = null;
	      }
	    }

	    
	  }, {
	    key: "setSelection",
	    value: function setSelection(ids) {
	      var _context21;
	      if (ids == undefined) {
	        ids = [];
	      }
	      if (!_Array$isArray$1(ids)) {
	        ids = [ids];
	      }
	      var idsToDeselect = _filterInstanceProperty(_context21 = this.selection).call(_context21, function (id) {
	        return _indexOfInstanceProperty(ids).call(ids, id) === -1;
	      });

	      
	      var _iterator = _createForOfIteratorHelper$1(idsToDeselect),
	        _step;
	      try {
	        for (_iterator.s(); !(_step = _iterator.n()).done;) {
	          var selectedId = _step.value;
	          var item = this.getItemById(selectedId);
	          if (item) {
	            item.unselect();
	          }
	        }

	        
	      } catch (err) {
	        _iterator.e(err);
	      } finally {
	        _iterator.f();
	      }
	      this.selection = _toConsumableArray(ids);
	      var _iterator2 = _createForOfIteratorHelper$1(ids),
	        _step2;
	      try {
	        for (_iterator2.s(); !(_step2 = _iterator2.n()).done;) {
	          var id = _step2.value;
	          var _item2 = this.getItemById(id);
	          if (_item2) {
	            _item2.select();
	          }
	        }
	      } catch (err) {
	        _iterator2.e(err);
	      } finally {
	        _iterator2.f();
	      }
	    }

	    
	  }, {
	    key: "getSelection",
	    value: function getSelection() {
	      var _context22;
	      return _concatInstanceProperty(_context22 = this.selection).call(_context22, []);
	    }

	    
	  }, {
	    key: "getVisibleItems",
	    value: function getVisibleItems() {
	      var range = this.body.range.getRange();
	      var right;
	      var left;
	      if (this.options.rtl) {
	        right = this.body.util.toScreen(range.start);
	        left = this.body.util.toScreen(range.end);
	      } else {
	        left = this.body.util.toScreen(range.start);
	        right = this.body.util.toScreen(range.end);
	      }
	      var ids = [];
	      for (var groupId in this.groups) {
	        if (this.groups.hasOwnProperty(groupId)) {
	          var group = this.groups[groupId];
	          var rawVisibleItems = group.isVisible ? group.visibleItems : [];

	          
	          
	          var _iterator3 = _createForOfIteratorHelper$1(rawVisibleItems),
	            _step3;
	          try {
	            for (_iterator3.s(); !(_step3 = _iterator3.n()).done;) {
	              var item = _step3.value;
	              
	              if (this.options.rtl) {
	                if (item.right < left && item.right + item.width > right) {
	                  ids.push(item.id);
	                }
	              } else {
	                if (item.left < right && item.left + item.width > left) {
	                  ids.push(item.id);
	                }
	              }
	            }
	          } catch (err) {
	            _iterator3.e(err);
	          } finally {
	            _iterator3.f();
	          }
	        }
	      }
	      return ids;
	    }

	    
	  }, {
	    key: "getItemsAtCurrentTime",
	    value: function getItemsAtCurrentTime(timeOfEvent) {
	      var right;
	      var left;
	      if (this.options.rtl) {
	        right = this.body.util.toScreen(timeOfEvent);
	        left = this.body.util.toScreen(timeOfEvent);
	      } else {
	        left = this.body.util.toScreen(timeOfEvent);
	        right = this.body.util.toScreen(timeOfEvent);
	      }
	      var ids = [];
	      for (var groupId in this.groups) {
	        if (this.groups.hasOwnProperty(groupId)) {
	          var group = this.groups[groupId];
	          var rawVisibleItems = group.isVisible ? group.visibleItems : [];

	          
	          
	          var _iterator4 = _createForOfIteratorHelper$1(rawVisibleItems),
	            _step4;
	          try {
	            for (_iterator4.s(); !(_step4 = _iterator4.n()).done;) {
	              var item = _step4.value;
	              if (this.options.rtl) {
	                if (item.right < left && item.right + item.width > right) {
	                  ids.push(item.id);
	                }
	              } else {
	                if (item.left < right && item.left + item.width > left) {
	                  ids.push(item.id);
	                }
	              }
	            }
	          } catch (err) {
	            _iterator4.e(err);
	          } finally {
	            _iterator4.f();
	          }
	        }
	      }
	      return ids;
	    }

	    
	  }, {
	    key: "getVisibleGroups",
	    value: function getVisibleGroups() {
	      var ids = [];
	      for (var groupId in this.groups) {
	        if (this.groups.hasOwnProperty(groupId)) {
	          var group = this.groups[groupId];
	          if (group.isVisible) {
	            ids.push(groupId);
	          }
	        }
	      }
	      return ids;
	    }

	    
	  }, {
	    key: "getItemById",
	    value: function getItemById(id) {
	      var _context23;
	      return this.items[id] || _findInstanceProperty(_context23 = this.clusters).call(_context23, function (cluster) {
	        return cluster.id === id;
	      });
	    }

	    
	  }, {
	    key: "_deselect",
	    value: function _deselect(id) {
	      var selection = this.selection;
	      for (var i = 0, ii = selection.length; i < ii; i++) {
	        if (selection[i] == id) {
	          
	          _spliceInstanceProperty(selection).call(selection, i, 1);
	          break;
	        }
	      }
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw() {
	      var margin = this.options.margin;
	      var range = this.body.range;
	      var asSize = availableUtils.option.asSize;
	      var options = this.options;
	      var orientation = options.orientation.item;
	      var resized = false;
	      var frame = this.dom.frame;

	      
	      this.props.top = this.body.domProps.top.height + this.body.domProps.border.top;
	      if (this.options.rtl) {
	        this.props.right = this.body.domProps.right.width + this.body.domProps.border.right;
	      } else {
	        this.props.left = this.body.domProps.left.width + this.body.domProps.border.left;
	      }

	      
	      frame.className = 'vis-itemset';
	      if (this.options.cluster) {
	        this._clusterItems();
	      }

	      
	      resized = this._orderGroups() || resized;

	      
	      
	      var visibleInterval = range.end - range.start;
	      var zoomed = visibleInterval != this.lastVisibleInterval || this.props.width != this.props.lastWidth;
	      var scrolled = range.start != this.lastRangeStart;
	      var changedStackOption = options.stack != this.lastStack;
	      var changedStackSubgroupsOption = options.stackSubgroups != this.lastStackSubgroups;
	      var forceRestack = zoomed || scrolled || changedStackOption || changedStackSubgroupsOption;
	      this.lastVisibleInterval = visibleInterval;
	      this.lastRangeStart = range.start;
	      this.lastStack = options.stack;
	      this.lastStackSubgroups = options.stackSubgroups;
	      this.props.lastWidth = this.props.width;
	      var firstGroup = this._firstGroup();
	      var firstMargin = {
	        item: margin.item,
	        axis: margin.axis
	      };
	      var nonFirstMargin = {
	        item: margin.item,
	        axis: margin.item.vertical / 2
	      };
	      var height = 0;
	      var minHeight = margin.axis + margin.item.vertical;

	      
	      this.groups[BACKGROUND].redraw(range, nonFirstMargin, forceRestack);
	      var redrawQueue = {};
	      var redrawQueueLength = 0;

	      
	      _forEachInstanceProperty(availableUtils).call(availableUtils, this.groups, function (group, key) {
	        if (key === BACKGROUND) return;
	        var groupMargin = group == firstGroup ? firstMargin : nonFirstMargin;
	        var returnQueue = true;
	        redrawQueue[key] = group.redraw(range, groupMargin, forceRestack, returnQueue);
	        redrawQueueLength = redrawQueue[key].length;
	      });
	      var needRedraw = redrawQueueLength > 0;
	      if (needRedraw) {
	        var redrawResults = {};
	        var _loop = function _loop(i) {
	          _forEachInstanceProperty(availableUtils).call(availableUtils, redrawQueue, function (fns, key) {
	            redrawResults[key] = fns[i]();
	          });
	        };
	        for (var i = 0; i < redrawQueueLength; i++) {
	          _loop(i);
	        }

	        
	        _forEachInstanceProperty(availableUtils).call(availableUtils, this.groups, function (group, key) {
	          if (key === BACKGROUND) return;
	          var groupResized = redrawResults[key];
	          resized = groupResized || resized;
	          height += group.height;
	        });
	        height = Math.max(height, minHeight);
	      }
	      height = Math.max(height, minHeight);

	      
	      frame.style.height = asSize(height);

	      
	      this.props.width = frame.offsetWidth;
	      this.props.height = height;

	      
	      this.dom.axis.style.top = asSize(orientation == 'top' ? this.body.domProps.top.height + this.body.domProps.border.top : this.body.domProps.top.height + this.body.domProps.centerContainer.height);
	      if (this.options.rtl) {
	        this.dom.axis.style.right = '0';
	      } else {
	        this.dom.axis.style.left = '0';
	      }
	      this.hammer.get('press').set({
	        time: this.options.longSelectPressTime
	      });
	      this.initialItemSetDrawn = true;
	      
	      resized = this._isResized() || resized;
	      return resized;
	    }

	    
	  }, {
	    key: "_firstGroup",
	    value: function _firstGroup() {
	      var firstGroupIndex = this.options.orientation.item == 'top' ? 0 : this.groupIds.length - 1;
	      var firstGroupId = this.groupIds[firstGroupIndex];
	      var firstGroup = this.groups[firstGroupId] || this.groups[UNGROUPED$1];
	      return firstGroup || null;
	    }

	    
	  }, {
	    key: "_updateUngrouped",
	    value: function _updateUngrouped() {
	      var ungrouped = this.groups[UNGROUPED$1];
	      var item;
	      var itemId;
	      if (this.groupsData) {
	        
	        if (ungrouped) {
	          ungrouped.dispose();
	          delete this.groups[UNGROUPED$1];
	          for (itemId in this.items) {
	            if (this.items.hasOwnProperty(itemId)) {
	              item = this.items[itemId];
	              item.parent && item.parent.remove(item);
	              var groupId = this.getGroupId(item.data);
	              var group = this.groups[groupId];
	              group && group.add(item) || item.hide();
	            }
	          }
	        }
	      } else {
	        
	        if (!ungrouped) {
	          var id = null;
	          var data = null;
	          ungrouped = new Group(id, data, this);
	          this.groups[UNGROUPED$1] = ungrouped;
	          for (itemId in this.items) {
	            if (this.items.hasOwnProperty(itemId)) {
	              item = this.items[itemId];
	              ungrouped.add(item);
	            }
	          }
	          ungrouped.show();
	        }
	      }
	    }

	    
	  }, {
	    key: "getLabelSet",
	    value: function getLabelSet() {
	      return this.dom.labelSet;
	    }

	    
	  }, {
	    key: "setItems",
	    value: function setItems(items) {
	      this.itemsSettingTime = new Date();
	      var me = this;
	      var ids;
	      var oldItemsData = this.itemsData;

	      
	      if (!items) {
	        this.itemsData = null;
	      } else if (isDataViewLike(items)) {
	        this.itemsData = typeCoerceDataSet(items);
	      } else {
	        throw new TypeError('Data must implement the interface of DataSet or DataView');
	      }
	      if (oldItemsData) {
	        
	        _forEachInstanceProperty(availableUtils).call(availableUtils, this.itemListeners, function (callback, event) {
	          oldItemsData.off(event, callback);
	        });

	        
	        oldItemsData.dispose();

	        
	        ids = oldItemsData.getIds();
	        this._onRemove(ids);
	      }
	      if (this.itemsData) {
	        
	        var id = this.id;
	        _forEachInstanceProperty(availableUtils).call(availableUtils, this.itemListeners, function (callback, event) {
	          me.itemsData.on(event, callback, id);
	        });

	        
	        ids = this.itemsData.getIds();
	        this._onAdd(ids);

	        
	        this._updateUngrouped();
	      }
	      this.body.emitter.emit('_change', {
	        queue: true
	      });
	    }

	    
	  }, {
	    key: "getItems",
	    value: function getItems() {
	      return this.itemsData != null ? this.itemsData.rawDS : null;
	    }

	    
	  }, {
	    key: "setGroups",
	    value: function setGroups(groups) {
	      var me = this;
	      var ids;

	      
	      if (this.groupsData) {
	        _forEachInstanceProperty(availableUtils).call(availableUtils, this.groupListeners, function (callback, event) {
	          me.groupsData.off(event, callback);
	        });

	        
	        ids = this.groupsData.getIds();
	        this.groupsData = null;
	        this._onRemoveGroups(ids); 
	      }

	      
	      if (!groups) {
	        this.groupsData = null;
	      } else if (isDataViewLike(groups)) {
	        this.groupsData = groups;
	      } else {
	        throw new TypeError('Data must implement the interface of DataSet or DataView');
	      }
	      if (this.groupsData) {
	        var _context24;
	        
	        var groupsData = this.groupsData.getDataSet();
	        _forEachInstanceProperty(_context24 = groupsData.get()).call(_context24, function (group) {
	          if (group.nestedGroups) {
	            var _context25;
	            _forEachInstanceProperty(_context25 = group.nestedGroups).call(_context25, function (nestedGroupId) {
	              var updatedNestedGroup = groupsData.get(nestedGroupId);
	              updatedNestedGroup.nestedInGroup = group.id;
	              if (group.showNested == false) {
	                updatedNestedGroup.visible = false;
	              }
	              groupsData.update(updatedNestedGroup);
	            });
	          }
	        });

	        
	        var id = this.id;
	        _forEachInstanceProperty(availableUtils).call(availableUtils, this.groupListeners, function (callback, event) {
	          me.groupsData.on(event, callback, id);
	        });

	        
	        ids = this.groupsData.getIds();
	        this._onAddGroups(ids);
	      }

	      
	      this._updateUngrouped();

	      
	      this._order();
	      if (this.options.cluster) {
	        this.clusterGenerator.updateData();
	        this._clusterItems();
	        this.markDirty({
	          refreshItems: true,
	          restackGroups: true
	        });
	      }
	      this.body.emitter.emit('_change', {
	        queue: true
	      });
	    }

	    
	  }, {
	    key: "getGroups",
	    value: function getGroups() {
	      return this.groupsData;
	    }

	    
	  }, {
	    key: "removeItem",
	    value: function removeItem(id) {
	      var _this4 = this;
	      var item = this.itemsData.get(id);
	      if (item) {
	        
	        this.options.onRemove(item, function (item) {
	          if (item) {
	            
	            
	            _this4.itemsData.remove(id);
	          }
	        });
	      }
	    }

	    
	  }, {
	    key: "_getType",
	    value: function _getType(itemData) {
	      return itemData.type || this.options.type || (itemData.end ? 'range' : 'box');
	    }

	    
	  }, {
	    key: "getGroupId",
	    value: function getGroupId(itemData) {
	      var type = this._getType(itemData);
	      if (type == 'background' && itemData.group == undefined) {
	        return BACKGROUND;
	      } else {
	        return this.groupsData ? itemData.group : UNGROUPED$1;
	      }
	    }

	    
	  }, {
	    key: "_onUpdate",
	    value: function _onUpdate(ids) {
	      var _this5 = this;
	      var me = this;
	      _forEachInstanceProperty(ids).call(ids, function (id) {
	        var itemData = me.itemsData.get(id);
	        var item = me.items[id];
	        var type = itemData ? me._getType(itemData) : null;
	        var constructor = ItemSet.types[type];
	        var selected;
	        if (item) {
	          
	          if (!constructor || !(item instanceof constructor)) {
	            
	            selected = item.selected; 
	            me._removeItem(item);
	            item = null;
	          } else {
	            me._updateItem(item, itemData);
	          }
	        }
	        if (!item && itemData) {
	          
	          if (constructor) {
	            item = new constructor(itemData, me.conversion, me.options);
	            item.id = id; 

	            me._addItem(item);
	            if (selected) {
	              _this5.selection.push(id);
	              item.select();
	            }
	          } else {
	            throw new TypeError("Unknown item type \"".concat(type, "\""));
	          }
	        }
	      });
	      this._order();
	      if (this.options.cluster) {
	        this.clusterGenerator.setItems(this.items, {
	          applyOnChangedLevel: false
	        });
	        this._clusterItems();
	      }
	      this.body.emitter.emit('_change', {
	        queue: true
	      });
	    }

	    
	  }, {
	    key: "_onRemove",
	    value: function _onRemove(ids) {
	      var count = 0;
	      var me = this;
	      _forEachInstanceProperty(ids).call(ids, function (id) {
	        var item = me.items[id];
	        if (item) {
	          count++;
	          me._removeItem(item);
	        }
	      });
	      if (count) {
	        
	        this._order();
	        this.body.emitter.emit('_change', {
	          queue: true
	        });
	      }
	    }

	    
	  }, {
	    key: "_order",
	    value: function _order() {
	      
	      
	      _forEachInstanceProperty(availableUtils).call(availableUtils, this.groups, function (group) {
	        group.order();
	      });
	    }

	    
	  }, {
	    key: "_onUpdateGroups",
	    value: function _onUpdateGroups(ids) {
	      this._onAddGroups(ids);
	    }

	    
	  }, {
	    key: "_onAddGroups",
	    value: function _onAddGroups(ids) {
	      var me = this;
	      _forEachInstanceProperty(ids).call(ids, function (id) {
	        var groupData = me.groupsData.get(id);
	        var group = me.groups[id];
	        if (!group) {
	          
	          if (id == UNGROUPED$1 || id == BACKGROUND) {
	            throw new Error("Illegal group id. ".concat(id, " is a reserved id."));
	          }
	          var groupOptions = _Object$create(me.options);
	          availableUtils.extend(groupOptions, {
	            height: null
	          });
	          group = new Group(id, groupData, me);
	          me.groups[id] = group;

	          
	          for (var itemId in me.items) {
	            if (me.items.hasOwnProperty(itemId)) {
	              var item = me.items[itemId];
	              if (item.data.group == id) {
	                group.add(item);
	              }
	            }
	          }
	          group.order();
	          group.show();
	        } else {
	          
	          group.setData(groupData);
	        }
	      });
	      this.body.emitter.emit('_change', {
	        queue: true
	      });
	    }

	    
	  }, {
	    key: "_onRemoveGroups",
	    value: function _onRemoveGroups(ids) {
	      var _this6 = this;
	      _forEachInstanceProperty(ids).call(ids, function (id) {
	        var group = _this6.groups[id];
	        if (group) {
	          group.dispose();
	          delete _this6.groups[id];
	        }
	      });
	      if (this.options.cluster) {
	        this.clusterGenerator.updateData();
	        this._clusterItems();
	      }
	      this.markDirty({
	        restackGroups: !!this.options.cluster
	      });
	      this.body.emitter.emit('_change', {
	        queue: true
	      });
	    }

	    
	  }, {
	    key: "_orderGroups",
	    value: function _orderGroups() {
	      if (this.groupsData) {
	        
	        var groupIds = this.groupsData.getIds({
	          order: this.options.groupOrder
	        });
	        groupIds = this._orderNestedGroups(groupIds);
	        var changed = !availableUtils.equalArray(groupIds, this.groupIds);
	        if (changed) {
	          
	          var groups = this.groups;
	          _forEachInstanceProperty(groupIds).call(groupIds, function (groupId) {
	            groups[groupId].hide();
	          });

	          
	          _forEachInstanceProperty(groupIds).call(groupIds, function (groupId) {
	            groups[groupId].show();
	          });
	          this.groupIds = groupIds;
	        }
	        return changed;
	      } else {
	        return false;
	      }
	    }

	    
	  }, {
	    key: "_orderNestedGroups",
	    value: function _orderNestedGroups(groupIds) {
	      var _this7 = this;
	      
	      function getOrderedNestedGroups(t, groupIds) {
	        var result = [];
	        _forEachInstanceProperty(groupIds).call(groupIds, function (groupId) {
	          result.push(groupId);
	          var groupData = t.groupsData.get(groupId);
	          if (groupData.nestedGroups) {
	            var _context26;
	            var nestedGroupIds = _mapInstanceProperty(_context26 = t.groupsData.get({
	              filter: function filter(nestedGroup) {
	                return nestedGroup.nestedInGroup == groupId;
	              },
	              order: t.options.groupOrder
	            })).call(_context26, function (nestedGroup) {
	              return nestedGroup.id;
	            });
	            result = _concatInstanceProperty(result).call(result, getOrderedNestedGroups(t, nestedGroupIds));
	          }
	        });
	        return result;
	      }
	      var topGroupIds = _filterInstanceProperty(groupIds).call(groupIds, function (groupId) {
	        return !_this7.groupsData.get(groupId).nestedInGroup;
	      });
	      return getOrderedNestedGroups(this, topGroupIds);
	    }

	    
	  }, {
	    key: "_addItem",
	    value: function _addItem(item) {
	      this.items[item.id] = item;

	      
	      var groupId = this.getGroupId(item.data);
	      var group = this.groups[groupId];
	      if (!group) {
	        item.groupShowing = false;
	      } else if (group && group.data && group.data.showNested) {
	        item.groupShowing = true;
	      }
	      if (group) group.add(item);
	    }

	    
	  }, {
	    key: "_updateItem",
	    value: function _updateItem(item, itemData) {
	      
	      item.setData(itemData);
	      var groupId = this.getGroupId(item.data);
	      var group = this.groups[groupId];
	      if (!group) {
	        item.groupShowing = false;
	      } else if (group && group.data && group.data.showNested) {
	        item.groupShowing = true;
	      }
	    }

	    
	  }, {
	    key: "_removeItem",
	    value: function _removeItem(item) {
	      var _context27, _context28;
	      
	      item.hide();

	      
	      delete this.items[item.id];

	      
	      var index = _indexOfInstanceProperty(_context27 = this.selection).call(_context27, item.id);
	      if (index != -1) _spliceInstanceProperty(_context28 = this.selection).call(_context28, index, 1);

	      
	      item.parent && item.parent.remove(item);

	      
	      if (this.popup != null) {
	        this.popup.hide();
	      }
	    }

	    
	  }, {
	    key: "_constructByEndArray",
	    value: function _constructByEndArray(array) {
	      var endArray = [];
	      for (var i = 0; i < array.length; i++) {
	        if (array[i] instanceof RangeItem) {
	          endArray.push(array[i]);
	        }
	      }
	      return endArray;
	    }

	    
	  }, {
	    key: "_onTouch",
	    value: function _onTouch(event) {
	      
	      this.touchParams.item = this.itemFromTarget(event);
	      this.touchParams.dragLeftItem = event.target.dragLeftItem || false;
	      this.touchParams.dragRightItem = event.target.dragRightItem || false;
	      this.touchParams.itemProps = null;
	    }

	    
	  }, {
	    key: "_getGroupIndex",
	    value: function _getGroupIndex(groupId) {
	      for (var i = 0; i < this.groupIds.length; i++) {
	        if (groupId == this.groupIds[i]) return i;
	      }
	    }

	    
	  }, {
	    key: "_onDragStart",
	    value: function _onDragStart(event) {
	      var _this8 = this;
	      if (this.touchParams.itemIsDragging) {
	        return;
	      }
	      var item = this.touchParams.item || null;
	      var me = this;
	      var props;
	      if (item && (item.selected || this.options.itemsAlwaysDraggable.item)) {
	        if (this.options.editable.overrideItems && !this.options.editable.updateTime && !this.options.editable.updateGroup) {
	          return;
	        }

	        
	        if (item.editable != null && !item.editable.updateTime && !item.editable.updateGroup && !this.options.editable.overrideItems) {
	          return;
	        }
	        var dragLeftItem = this.touchParams.dragLeftItem;
	        var dragRightItem = this.touchParams.dragRightItem;
	        this.touchParams.itemIsDragging = true;
	        this.touchParams.selectedItem = item;
	        if (dragLeftItem) {
	          props = {
	            item: dragLeftItem,
	            initialX: event.center.x,
	            dragLeft: true,
	            data: this._cloneItemData(item.data)
	          };
	          this.touchParams.itemProps = [props];
	        } else if (dragRightItem) {
	          props = {
	            item: dragRightItem,
	            initialX: event.center.x,
	            dragRight: true,
	            data: this._cloneItemData(item.data)
	          };
	          this.touchParams.itemProps = [props];
	        } else if (this.options.editable.add && (event.srcEvent.ctrlKey || event.srcEvent.metaKey)) {
	          
	          this._onDragStartAddItem(event);
	        } else {
	          if (this.groupIds.length < 1) {
	            
	            
	            this.redraw();
	          }
	          var baseGroupIndex = this._getGroupIndex(item.data.group);
	          var itemsToDrag = this.options.itemsAlwaysDraggable.item && !item.selected ? [item.id] : this.getSelection();
	          this.touchParams.itemProps = _mapInstanceProperty(itemsToDrag).call(itemsToDrag, function (id) {
	            var item = me.items[id];
	            var groupIndex = me._getGroupIndex(item.data.group);
	            return {
	              item: item,
	              initialX: event.center.x,
	              groupOffset: baseGroupIndex - groupIndex,
	              data: _this8._cloneItemData(item.data)
	            };
	          });
	        }
	        event.stopPropagation();
	      } else if (this.options.editable.add && (event.srcEvent.ctrlKey || event.srcEvent.metaKey)) {
	        
	        this._onDragStartAddItem(event);
	      }
	    }

	    
	  }, {
	    key: "_onDragStartAddItem",
	    value: function _onDragStartAddItem(event) {
	      var snap = this.options.snap || null;
	      var frameRect = this.dom.frame.getBoundingClientRect();

	      
	      var x = this.options.rtl ? frameRect.right - event.center.x + 10 : event.center.x - frameRect.left - 10;
	      var time = this.body.util.toTime(x);
	      var scale = this.body.util.getScale();
	      var step = this.body.util.getStep();
	      var start = snap ? snap(time, scale, step) : time;
	      var end = start;
	      var itemData = {
	        type: 'range',
	        start: start,
	        end: end,
	        content: 'new item'
	      };
	      var id = v4();
	      itemData[this.itemsData.idProp] = id;
	      var group = this.groupFromTarget(event);
	      if (group) {
	        itemData.group = group.groupId;
	      }
	      var newItem = new RangeItem(itemData, this.conversion, this.options);
	      newItem.id = id; 
	      newItem.data = this._cloneItemData(itemData);
	      this._addItem(newItem);
	      this.touchParams.selectedItem = newItem;
	      var props = {
	        item: newItem,
	        initialX: event.center.x,
	        data: newItem.data
	      };
	      if (this.options.rtl) {
	        props.dragLeft = true;
	      } else {
	        props.dragRight = true;
	      }
	      this.touchParams.itemProps = [props];
	      event.stopPropagation();
	    }

	    
	  }, {
	    key: "_onDrag",
	    value: function _onDrag(event) {
	      var _this9 = this;
	      if (this.popup != null && this.options.showTooltips && !this.popup.hidden) {
	        
	        var container = this.body.dom.centerContainer;
	        var containerRect = container.getBoundingClientRect();
	        this.popup.setPosition(event.center.x - containerRect.left + container.offsetLeft, event.center.y - containerRect.top + container.offsetTop);
	        this.popup.show(); 
	      }

	      if (this.touchParams.itemProps) {
	        var _context29;
	        event.stopPropagation();
	        var me = this;
	        var snap = this.options.snap || null;
	        var domRootOffsetLeft = this.body.dom.root.offsetLeft;
	        var xOffset = this.options.rtl ? domRootOffsetLeft + this.body.domProps.right.width : domRootOffsetLeft + this.body.domProps.left.width;
	        var scale = this.body.util.getScale();
	        var step = this.body.util.getStep();

	        
	        var selectedItem = this.touchParams.selectedItem;
	        var updateGroupAllowed = (this.options.editable.overrideItems || selectedItem.editable == null) && this.options.editable.updateGroup || !this.options.editable.overrideItems && selectedItem.editable != null && selectedItem.editable.updateGroup;
	        var newGroupBase = null;
	        if (updateGroupAllowed && selectedItem) {
	          if (selectedItem.data.group != undefined) {
	            
	            var group = me.groupFromTarget(event);
	            if (group) {
	              
	              
	              newGroupBase = this._getGroupIndex(group.groupId);
	            }
	          }
	        }

	        
	        _forEachInstanceProperty(_context29 = this.touchParams.itemProps).call(_context29, function (props) {
	          var current = me.body.util.toTime(event.center.x - xOffset);
	          var initial = me.body.util.toTime(props.initialX - xOffset);
	          var offset;
	          var initialStart;
	          var initialEnd;
	          var start;
	          var end;
	          if (_this9.options.rtl) {
	            offset = -(current - initial); 
	          } else {
	            offset = current - initial; 
	          }

	          var itemData = _this9._cloneItemData(props.item.data); 
	          if (props.item.editable != null && !props.item.editable.updateTime && !props.item.editable.updateGroup && !me.options.editable.overrideItems) {
	            return;
	          }
	          var updateTimeAllowed = (_this9.options.editable.overrideItems || selectedItem.editable == null) && _this9.options.editable.updateTime || !_this9.options.editable.overrideItems && selectedItem.editable != null && selectedItem.editable.updateTime;
	          if (updateTimeAllowed) {
	            if (props.dragLeft) {
	              
	              if (_this9.options.rtl) {
	                if (itemData.end != undefined) {
	                  initialEnd = availableUtils.convert(props.data.end, 'Date');
	                  end = new Date(initialEnd.valueOf() + offset);
	                  
	                  itemData.end = snap ? snap(end, scale, step) : end;
	                }
	              } else {
	                if (itemData.start != undefined) {
	                  initialStart = availableUtils.convert(props.data.start, 'Date');
	                  start = new Date(initialStart.valueOf() + offset);
	                  
	                  itemData.start = snap ? snap(start, scale, step) : start;
	                }
	              }
	            } else if (props.dragRight) {
	              
	              if (_this9.options.rtl) {
	                if (itemData.start != undefined) {
	                  initialStart = availableUtils.convert(props.data.start, 'Date');
	                  start = new Date(initialStart.valueOf() + offset);
	                  
	                  itemData.start = snap ? snap(start, scale, step) : start;
	                }
	              } else {
	                if (itemData.end != undefined) {
	                  initialEnd = availableUtils.convert(props.data.end, 'Date');
	                  end = new Date(initialEnd.valueOf() + offset);
	                  
	                  itemData.end = snap ? snap(end, scale, step) : end;
	                }
	              }
	            } else {
	              
	              if (itemData.start != undefined) {
	                initialStart = availableUtils.convert(props.data.start, 'Date').valueOf();
	                start = new Date(initialStart + offset);
	                if (itemData.end != undefined) {
	                  initialEnd = availableUtils.convert(props.data.end, 'Date');
	                  var duration = initialEnd.valueOf() - initialStart.valueOf();

	                  
	                  itemData.start = snap ? snap(start, scale, step) : start;
	                  itemData.end = new Date(itemData.start.valueOf() + duration);
	                } else {
	                  
	                  itemData.start = snap ? snap(start, scale, step) : start;
	                }
	              }
	            }
	          }
	          if (updateGroupAllowed && !props.dragLeft && !props.dragRight && newGroupBase != null) {
	            if (itemData.group != undefined) {
	              var newOffset = newGroupBase - props.groupOffset;

	              
	              newOffset = Math.max(0, newOffset);
	              newOffset = Math.min(me.groupIds.length - 1, newOffset);
	              itemData.group = me.groupIds[newOffset];
	            }
	          }

	          
	          itemData = _this9._cloneItemData(itemData); 
	          me.options.onMoving(itemData, function (itemData) {
	            if (itemData) {
	              props.item.setData(_this9._cloneItemData(itemData, 'Date'));
	            }
	          });
	        });
	        this.body.emitter.emit('_change');
	      }
	    }

	    
	  }, {
	    key: "_moveToGroup",
	    value: function _moveToGroup(item, groupId) {
	      var group = this.groups[groupId];
	      if (group && group.groupId != item.data.group) {
	        var oldGroup = item.parent;
	        oldGroup.remove(item);
	        oldGroup.order();
	        item.data.group = group.groupId;
	        group.add(item);
	        group.order();
	      }
	    }

	    
	  }, {
	    key: "_onDragEnd",
	    value: function _onDragEnd(event) {
	      var _this10 = this;
	      this.touchParams.itemIsDragging = false;
	      if (this.touchParams.itemProps) {
	        event.stopPropagation();
	        var me = this;
	        var itemProps = this.touchParams.itemProps;
	        this.touchParams.itemProps = null;
	        _forEachInstanceProperty(itemProps).call(itemProps, function (props) {
	          var id = props.item.id;
	          var exists = me.itemsData.get(id) != null;
	          if (!exists) {
	            
	            me.options.onAdd(props.item.data, function (itemData) {
	              me._removeItem(props.item); 
	              if (itemData) {
	                me.itemsData.add(itemData);
	              }

	              
	              me.body.emitter.emit('_change');
	            });
	          } else {
	            
	            var itemData = _this10._cloneItemData(props.item.data); 
	            me.options.onMove(itemData, function (itemData) {
	              if (itemData) {
	                
	                itemData[_this10.itemsData.idProp] = id; 
	                _this10.itemsData.update(itemData);
	              } else {
	                
	                props.item.setData(props.data);
	                me.body.emitter.emit('_change');
	              }
	            });
	          }
	        });
	      }
	    }

	    
	  }, {
	    key: "_onGroupClick",
	    value: function _onGroupClick(event) {
	      var _this11 = this;
	      var group = this.groupFromTarget(event);
	      _setTimeout(function () {
	        _this11.toggleGroupShowNested(group);
	      }, 1);
	    }

	    
	  }, {
	    key: "toggleGroupShowNested",
	    value: function toggleGroupShowNested(group) {
	      var force = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : undefined;
	      if (!group || !group.nestedGroups) return;
	      var groupsData = this.groupsData.getDataSet();
	      if (force != undefined) {
	        group.showNested = !!force;
	      } else {
	        group.showNested = !group.showNested;
	      }
	      var nestingGroup = groupsData.get(group.groupId);
	      nestingGroup.showNested = group.showNested;
	      var fullNestedGroups = group.nestedGroups;
	      var nextLevel = fullNestedGroups;
	      while (nextLevel.length > 0) {
	        var current = nextLevel;
	        nextLevel = [];
	        for (var i = 0; i < current.length; i++) {
	          var node = groupsData.get(current[i]);
	          if (node.nestedGroups) {
	            nextLevel = _concatInstanceProperty(nextLevel).call(nextLevel, node.nestedGroups);
	          }
	        }
	        if (nextLevel.length > 0) {
	          fullNestedGroups = _concatInstanceProperty(fullNestedGroups).call(fullNestedGroups, nextLevel);
	        }
	      }
	      var nestedGroups;
	      if (nestingGroup.showNested) {
	        var showNestedGroups = groupsData.get(nestingGroup.nestedGroups);
	        for (var _i = 0; _i < showNestedGroups.length; _i++) {
	          var _group = showNestedGroups[_i];
	          if (_group.nestedGroups && _group.nestedGroups.length > 0 && (_group.showNested == undefined || _group.showNested == true)) {
	            showNestedGroups.push.apply(showNestedGroups, _toConsumableArray(groupsData.get(_group.nestedGroups)));
	          }
	        }
	        nestedGroups = _mapInstanceProperty(showNestedGroups).call(showNestedGroups, function (nestedGroup) {
	          if (nestedGroup.visible == undefined) {
	            nestedGroup.visible = true;
	          }
	          nestedGroup.visible = !!nestingGroup.showNested;
	          return nestedGroup;
	        });
	      } else {
	        var _context30;
	        nestedGroups = _mapInstanceProperty(_context30 = groupsData.get(fullNestedGroups)).call(_context30, function (nestedGroup) {
	          if (nestedGroup.visible == undefined) {
	            nestedGroup.visible = true;
	          }
	          nestedGroup.visible = !!nestingGroup.showNested;
	          return nestedGroup;
	        });
	      }
	      groupsData.update(_concatInstanceProperty(nestedGroups).call(nestedGroups, nestingGroup));
	      if (nestingGroup.showNested) {
	        availableUtils.removeClassName(group.dom.label, 'collapsed');
	        availableUtils.addClassName(group.dom.label, 'expanded');
	      } else {
	        availableUtils.removeClassName(group.dom.label, 'expanded');
	        availableUtils.addClassName(group.dom.label, 'collapsed');
	      }
	    }

	    
	  }, {
	    key: "toggleGroupDragClassName",
	    value: function toggleGroupDragClassName(group) {
	      group.dom.label.classList.toggle('vis-group-is-dragging');
	      group.dom.foreground.classList.toggle('vis-group-is-dragging');
	    }

	    
	  }, {
	    key: "_onGroupDragStart",
	    value: function _onGroupDragStart(event) {
	      if (this.groupTouchParams.isDragging) return;
	      if (this.options.groupEditable.order) {
	        this.groupTouchParams.group = this.groupFromTarget(event);
	        if (this.groupTouchParams.group) {
	          event.stopPropagation();
	          this.groupTouchParams.isDragging = true;
	          this.toggleGroupDragClassName(this.groupTouchParams.group);
	          this.groupTouchParams.originalOrder = this.groupsData.getIds({
	            order: this.options.groupOrder
	          });
	        }
	      }
	    }

	    
	  }, {
	    key: "_onGroupDrag",
	    value: function _onGroupDrag(event) {
	      if (this.options.groupEditable.order && this.groupTouchParams.group) {
	        event.stopPropagation();
	        var groupsData = this.groupsData.getDataSet();
	        
	        var group = this.groupFromTarget(event);

	        
	        if (group && group.height != this.groupTouchParams.group.height) {
	          var movingUp = group.top < this.groupTouchParams.group.top;
	          var clientY = event.center ? event.center.y : event.clientY;
	          var targetGroup = group.dom.foreground.getBoundingClientRect();
	          var draggedGroupHeight = this.groupTouchParams.group.height;
	          if (movingUp) {
	            
	            if (targetGroup.top + draggedGroupHeight < clientY) {
	              return;
	            }
	          } else {
	            var targetGroupHeight = group.height;
	            
	            if (targetGroup.top + targetGroupHeight - draggedGroupHeight > clientY) {
	              return;
	            }
	          }
	        }
	        if (group && group != this.groupTouchParams.group) {
	          var _targetGroup = groupsData.get(group.groupId);
	          var draggedGroup = groupsData.get(this.groupTouchParams.group.groupId);

	          
	          if (draggedGroup && _targetGroup) {
	            this.options.groupOrderSwap(draggedGroup, _targetGroup, groupsData);
	            groupsData.update(draggedGroup);
	            groupsData.update(_targetGroup);
	          }

	          
	          var newOrder = groupsData.getIds({
	            order: this.options.groupOrder
	          });

	          
	          if (!availableUtils.equalArray(newOrder, this.groupTouchParams.originalOrder)) {
	            var origOrder = this.groupTouchParams.originalOrder;
	            var draggedId = this.groupTouchParams.group.groupId;
	            var numGroups = Math.min(origOrder.length, newOrder.length);
	            var curPos = 0;
	            var newOffset = 0;
	            var orgOffset = 0;
	            while (curPos < numGroups) {
	              
	              while (curPos + newOffset < numGroups && curPos + orgOffset < numGroups && newOrder[curPos + newOffset] == origOrder[curPos + orgOffset]) {
	                curPos++;
	              }

	              
	              if (curPos + newOffset >= numGroups) {
	                break;
	              }

	              
	              
	              if (newOrder[curPos + newOffset] == draggedId) {
	                newOffset = 1;
	              }
	              
	              else if (origOrder[curPos + orgOffset] == draggedId) {
	                orgOffset = 1;
	              }
	              
	              
	              else {
	                var slippedPosition = _indexOfInstanceProperty(newOrder).call(newOrder, origOrder[curPos + orgOffset]);
	                var switchGroup = groupsData.get(newOrder[curPos + newOffset]);
	                var shouldBeGroup = groupsData.get(origOrder[curPos + orgOffset]);
	                this.options.groupOrderSwap(switchGroup, shouldBeGroup, groupsData);
	                groupsData.update(switchGroup);
	                groupsData.update(shouldBeGroup);
	                var switchGroupId = newOrder[curPos + newOffset];
	                newOrder[curPos + newOffset] = origOrder[curPos + orgOffset];
	                newOrder[slippedPosition] = switchGroupId;
	                curPos++;
	              }
	            }
	          }
	        }
	      }
	    }

	    
	  }, {
	    key: "_onGroupDragEnd",
	    value: function _onGroupDragEnd(event) {
	      this.groupTouchParams.isDragging = false;
	      if (this.options.groupEditable.order && this.groupTouchParams.group) {
	        event.stopPropagation();

	        
	        var me = this;
	        var id = me.groupTouchParams.group.groupId;
	        var dataset = me.groupsData.getDataSet();
	        var groupData = availableUtils.extend({}, dataset.get(id)); 
	        me.options.onMoveGroup(groupData, function (groupData) {
	          if (groupData) {
	            
	            groupData[dataset._idProp] = id; 
	            dataset.update(groupData);
	          } else {
	            
	            var newOrder = dataset.getIds({
	              order: me.options.groupOrder
	            });

	            
	            if (!availableUtils.equalArray(newOrder, me.groupTouchParams.originalOrder)) {
	              var origOrder = me.groupTouchParams.originalOrder;
	              var numGroups = Math.min(origOrder.length, newOrder.length);
	              var curPos = 0;
	              while (curPos < numGroups) {
	                
	                while (curPos < numGroups && newOrder[curPos] == origOrder[curPos]) {
	                  curPos++;
	                }

	                
	                if (curPos >= numGroups) {
	                  break;
	                }

	                
	                
	                var slippedPosition = _indexOfInstanceProperty(newOrder).call(newOrder, origOrder[curPos]);
	                var switchGroup = dataset.get(newOrder[curPos]);
	                var shouldBeGroup = dataset.get(origOrder[curPos]);
	                me.options.groupOrderSwap(switchGroup, shouldBeGroup, dataset);
	                dataset.update(switchGroup);
	                dataset.update(shouldBeGroup);
	                var switchGroupId = newOrder[curPos];
	                newOrder[curPos] = origOrder[curPos];
	                newOrder[slippedPosition] = switchGroupId;
	                curPos++;
	              }
	            }
	          }
	        });
	        me.body.emitter.emit('groupDragged', {
	          groupId: id
	        });
	        this.toggleGroupDragClassName(this.groupTouchParams.group);
	        this.groupTouchParams.group = null;
	      }
	    }

	    
	  }, {
	    key: "_onSelectItem",
	    value: function _onSelectItem(event) {
	      if (!this.options.selectable) return;
	      var ctrlKey = event.srcEvent && (event.srcEvent.ctrlKey || event.srcEvent.metaKey);
	      var shiftKey = event.srcEvent && event.srcEvent.shiftKey;
	      if (ctrlKey || shiftKey) {
	        this._onMultiSelectItem(event);
	        return;
	      }
	      var oldSelection = this.getSelection();
	      var item = this.itemFromTarget(event);
	      var selection = item && item.selectable ? [item.id] : [];
	      this.setSelection(selection);
	      var newSelection = this.getSelection();

	      
	      
	      if (newSelection.length > 0 || oldSelection.length > 0) {
	        this.body.emitter.emit('select', {
	          items: newSelection,
	          event: event
	        });
	      }
	    }

	    
	  }, {
	    key: "_onMouseOver",
	    value: function _onMouseOver(event) {
	      var item = this.itemFromTarget(event);
	      if (!item) return;

	      
	      var related = this.itemFromRelatedTarget(event);
	      if (item === related) {
	        
	        return;
	      }
	      var title = item.getTitle();
	      if (this.options.showTooltips && title) {
	        if (this.popup == null) {
	          this.popup = new Popup(this.body.dom.root, this.options.tooltip.overflowMethod || 'flip');
	        }
	        this.popup.setText(title);
	        var container = this.body.dom.centerContainer;
	        var containerRect = container.getBoundingClientRect();
	        this.popup.setPosition(event.clientX - containerRect.left + container.offsetLeft, event.clientY - containerRect.top + container.offsetTop);
	        this.setPopupTimer(this.popup);
	      } else {
	        
	        
	        this.clearPopupTimer();
	        if (this.popup != null) {
	          this.popup.hide();
	        }
	      }
	      this.body.emitter.emit('itemover', {
	        item: item.id,
	        event: event
	      });
	    }

	    
	  }, {
	    key: "_onMouseOut",
	    value: function _onMouseOut(event) {
	      var item = this.itemFromTarget(event);
	      if (!item) return;

	      
	      var related = this.itemFromRelatedTarget(event);
	      if (item === related) {
	        
	        return;
	      }
	      this.clearPopupTimer();
	      if (this.popup != null) {
	        this.popup.hide();
	      }
	      this.body.emitter.emit('itemout', {
	        item: item.id,
	        event: event
	      });
	    }

	    
	  }, {
	    key: "_onMouseMove",
	    value: function _onMouseMove(event) {
	      var item = this.itemFromTarget(event);
	      if (!item) return;
	      if (this.popupTimer != null) {
	        
	        this.setPopupTimer(this.popup);
	      }
	      if (this.options.showTooltips && this.options.tooltip.followMouse && this.popup && !this.popup.hidden) {
	        var container = this.body.dom.centerContainer;
	        var containerRect = container.getBoundingClientRect();
	        this.popup.setPosition(event.clientX - containerRect.left + container.offsetLeft, event.clientY - containerRect.top + container.offsetTop);
	        this.popup.show(); 
	      }
	    }

	    
	  }, {
	    key: "_onMouseWheel",
	    value: function _onMouseWheel(event) {
	      if (this.touchParams.itemIsDragging) {
	        this._onDragEnd(event);
	      }
	    }

	    
	  }, {
	    key: "_onUpdateItem",
	    value: function _onUpdateItem(item) {
	      if (!this.options.selectable) return;
	      if (!this.options.editable.updateTime && !this.options.editable.updateGroup) return;
	      var me = this;
	      if (item) {
	        
	        var itemData = me.itemsData.get(item.id); 
	        this.options.onUpdate(itemData, function (itemData) {
	          if (itemData) {
	            me.itemsData.update(itemData);
	          }
	        });
	      }
	    }

	    
	  }, {
	    key: "_onDropObjectOnItem",
	    value: function _onDropObjectOnItem(event) {
	      var item = this.itemFromTarget(event);
	      var objectData = JSON.parse(event.dataTransfer.getData("text"));
	      this.options.onDropObjectOnItem(objectData, item);
	    }

	    
	  }, {
	    key: "_onAddItem",
	    value: function _onAddItem(event) {
	      if (!this.options.selectable) return;
	      if (!this.options.editable.add) return;
	      var me = this;
	      var snap = this.options.snap || null;

	      
	      var frameRect = this.dom.frame.getBoundingClientRect();
	      var x = this.options.rtl ? frameRect.right - event.center.x : event.center.x - frameRect.left;
	      var start = this.body.util.toTime(x);
	      var scale = this.body.util.getScale();
	      var step = this.body.util.getStep();
	      var end;
	      var newItemData;
	      if (event.type == 'drop') {
	        newItemData = JSON.parse(event.dataTransfer.getData("text"));
	        newItemData.content = newItemData.content ? newItemData.content : 'new item';
	        newItemData.start = newItemData.start ? newItemData.start : snap ? snap(start, scale, step) : start;
	        newItemData.type = newItemData.type || 'box';
	        newItemData[this.itemsData.idProp] = newItemData.id || v4();
	        if (newItemData.type == 'range' && !newItemData.end) {
	          end = this.body.util.toTime(x + this.props.width / 5);
	          newItemData.end = snap ? snap(end, scale, step) : end;
	        }
	      } else {
	        newItemData = {
	          start: snap ? snap(start, scale, step) : start,
	          content: 'new item'
	        };
	        newItemData[this.itemsData.idProp] = v4();

	        
	        if (this.options.type === 'range') {
	          end = this.body.util.toTime(x + this.props.width / 5);
	          newItemData.end = snap ? snap(end, scale, step) : end;
	        }
	      }
	      var group = this.groupFromTarget(event);
	      if (group) {
	        newItemData.group = group.groupId;
	      }

	      
	      newItemData = this._cloneItemData(newItemData); 
	      this.options.onAdd(newItemData, function (item) {
	        if (item) {
	          me.itemsData.add(item);
	          if (event.type == 'drop') {
	            me.setSelection([item.id]);
	          }
	          
	        }
	      });
	    }

	    
	  }, {
	    key: "_onMultiSelectItem",
	    value: function _onMultiSelectItem(event) {
	      var _this12 = this;
	      if (!this.options.selectable) return;
	      var item = this.itemFromTarget(event);
	      if (item) {
	        

	        var selection = this.options.multiselect ? this.getSelection() 
	        : []; 

	        var shiftKey = event.srcEvent && event.srcEvent.shiftKey || false;
	        if ((shiftKey || this.options.sequentialSelection) && this.options.multiselect) {
	          
	          var itemGroup = this.itemsData.get(item.id).group;

	          
	          var lastSelectedGroup = undefined;
	          if (this.options.multiselectPerGroup) {
	            if (selection.length > 0) {
	              lastSelectedGroup = this.itemsData.get(selection[0]).group;
	            }
	          }

	          
	          if (!this.options.multiselectPerGroup || lastSelectedGroup == undefined || lastSelectedGroup == itemGroup) {
	            selection.push(item.id);
	          }
	          var range = ItemSet._getItemRange(this.itemsData.get(selection));
	          if (!this.options.multiselectPerGroup || lastSelectedGroup == itemGroup) {
	            
	            selection = [];
	            for (var id in this.items) {
	              if (this.items.hasOwnProperty(id)) {
	                var _item = this.items[id];
	                var start = _item.data.start;
	                var end = _item.data.end !== undefined ? _item.data.end : start;
	                if (start >= range.min && end <= range.max && (!this.options.multiselectPerGroup || lastSelectedGroup == this.itemsData.get(_item.id).group) && !(_item instanceof BackgroundItem)) {
	                  selection.push(_item.id); 
	                }
	              }
	            }
	          }
	        } else {
	          
	          var index = _indexOfInstanceProperty(selection).call(selection, item.id);
	          if (index == -1) {
	            
	            selection.push(item.id);
	          } else {
	            
	            _spliceInstanceProperty(selection).call(selection, index, 1);
	          }
	        }
	        var filteredSelection = _filterInstanceProperty(selection).call(selection, function (item) {
	          return _this12.getItemById(item).selectable;
	        });
	        this.setSelection(filteredSelection);
	        this.body.emitter.emit('select', {
	          items: this.getSelection(),
	          event: event
	        });
	      }
	    }

	    
	  }, {
	    key: "itemFromElement",
	    value:
	    
	    function itemFromElement(element) {
	      var cur = element;
	      while (cur) {
	        if (cur.hasOwnProperty('vis-item')) {
	          return cur['vis-item'];
	        }
	        cur = cur.parentNode;
	      }
	      return null;
	    }

	    
	  }, {
	    key: "itemFromTarget",
	    value: function itemFromTarget(event) {
	      return this.itemFromElement(event.target);
	    }

	    
	  }, {
	    key: "itemFromRelatedTarget",
	    value: function itemFromRelatedTarget(event) {
	      return this.itemFromElement(event.relatedTarget);
	    }

	    
	  }, {
	    key: "groupFromTarget",
	    value: function groupFromTarget(event) {
	      var clientY = event.center ? event.center.y : event.clientY;
	      var groupIds = this.groupIds;
	      if (groupIds.length <= 0 && this.groupsData) {
	        groupIds = this.groupsData.getIds({
	          order: this.options.groupOrder
	        });
	      }
	      for (var i = 0; i < groupIds.length; i++) {
	        var groupId = groupIds[i];
	        var group = this.groups[groupId];
	        var foreground = group.dom.foreground;
	        var foregroundRect = foreground.getBoundingClientRect();
	        if (clientY >= foregroundRect.top && clientY < foregroundRect.top + foreground.offsetHeight) {
	          return group;
	        }
	        if (this.options.orientation.item === 'top') {
	          if (i === this.groupIds.length - 1 && clientY > foregroundRect.top) {
	            return group;
	          }
	        } else {
	          if (i === 0 && clientY < foregroundRect.top + foreground.offset) {
	            return group;
	          }
	        }
	      }
	      return null;
	    }

	    
	  }, {
	    key: "_cloneItemData",
	    value:
	    
	    function _cloneItemData(itemData, type) {
	      var clone = availableUtils.extend({}, itemData);
	      if (!type) {
	        
	        type = this.itemsData.type;
	      }
	      if (clone.start != undefined) {
	        clone.start = availableUtils.convert(clone.start, type && type.start || 'Date');
	      }
	      if (clone.end != undefined) {
	        clone.end = availableUtils.convert(clone.end, type && type.end || 'Date');
	      }
	      return clone;
	    }

	    
	  }, {
	    key: "_clusterItems",
	    value: function _clusterItems() {
	      if (!this.options.cluster) {
	        return;
	      }
	      var _this$body$range$conv = this.body.range.conversion(this.body.domProps.center.width),
	        scale = _this$body$range$conv.scale;
	      var clusters = this.clusterGenerator.getClusters(this.clusters, scale, this.options.cluster);
	      if (this.clusters != clusters) {
	        this._detachAllClusters();
	        if (clusters) {
	          var _iterator5 = _createForOfIteratorHelper$1(clusters),
	            _step5;
	          try {
	            for (_iterator5.s(); !(_step5 = _iterator5.n()).done;) {
	              var cluster = _step5.value;
	              cluster.attach();
	            }
	          } catch (err) {
	            _iterator5.e(err);
	          } finally {
	            _iterator5.f();
	          }
	          this.clusters = clusters;
	        }
	        this._updateClusters(clusters);
	      }
	    }

	    
	  }, {
	    key: "_detachAllClusters",
	    value: function _detachAllClusters() {
	      if (this.options.cluster) {
	        if (this.clusters && this.clusters.length) {
	          var _iterator6 = _createForOfIteratorHelper$1(this.clusters),
	            _step6;
	          try {
	            for (_iterator6.s(); !(_step6 = _iterator6.n()).done;) {
	              var cluster = _step6.value;
	              cluster.detach();
	            }
	          } catch (err) {
	            _iterator6.e(err);
	          } finally {
	            _iterator6.f();
	          }
	        }
	      }
	    }

	    
	  }, {
	    key: "_updateClusters",
	    value: function _updateClusters(clusters) {
	      if (this.clusters && this.clusters.length) {
	        var _context31;
	        var newClustersIds = new _Set(_mapInstanceProperty(clusters).call(clusters, function (cluster) {
	          return cluster.id;
	        }));
	        var clustersToUnselect = _filterInstanceProperty(_context31 = this.clusters).call(_context31, function (cluster) {
	          return !newClustersIds.has(cluster.id);
	        });
	        var selectionChanged = false;
	        var _iterator7 = _createForOfIteratorHelper$1(clustersToUnselect),
	          _step7;
	        try {
	          for (_iterator7.s(); !(_step7 = _iterator7.n()).done;) {
	            var _context32;
	            var cluster = _step7.value;
	            var selectedIdx = _indexOfInstanceProperty(_context32 = this.selection).call(_context32, cluster.id);
	            if (selectedIdx !== -1) {
	              var _context33;
	              cluster.unselect();
	              _spliceInstanceProperty(_context33 = this.selection).call(_context33, selectedIdx, 1);
	              selectionChanged = true;
	            }
	          }
	        } catch (err) {
	          _iterator7.e(err);
	        } finally {
	          _iterator7.f();
	        }
	        if (selectionChanged) {
	          var newSelection = this.getSelection();
	          this.body.emitter.emit('select', {
	            items: newSelection,
	            event: event
	          });
	        }
	      }
	      this.clusters = clusters || [];
	    }
	  }], [{
	    key: "_getItemRange",
	    value: function _getItemRange(itemsData) {
	      var max = null;
	      var min = null;
	      _forEachInstanceProperty(itemsData).call(itemsData, function (data) {
	        if (min == null || data.start < min) {
	          min = data.start;
	        }
	        if (data.end != undefined) {
	          if (max == null || data.end > max) {
	            max = data.end;
	          }
	        } else {
	          if (max == null || data.start > max) {
	            max = data.start;
	          }
	        }
	      });
	      return {
	        min: min,
	        max: max
	      };
	    }
	  }, {
	    key: "itemSetFromTarget",
	    value: function itemSetFromTarget(event) {
	      var target = event.target;
	      while (target) {
	        if (target.hasOwnProperty('vis-itemset')) {
	          return target['vis-itemset'];
	        }
	        target = target.parentNode;
	      }
	      return null;
	    }
	  }]);
	  return ItemSet;
	}(Component); 
	ItemSet.types = {
	  background: BackgroundItem,
	  box: BoxItem,
	  range: RangeItem,
	  point: PointItem
	};

	
	ItemSet.prototype._onAdd = ItemSet.prototype._onUpdate;

	var errorFound = false;
	var allOptions$2;
	var printStyle = 'background: #FFeeee; color: #dd0000';
	
	var Validator = function () {
	  
	  function Validator() {
	    _classCallCheck(this, Validator);
	  }

	  
	  _createClass(Validator, null, [{
	    key: "validate",
	    value: function validate(options, referenceOptions, subObject) {
	      errorFound = false;
	      allOptions$2 = referenceOptions;
	      var usedOptions = referenceOptions;
	      if (subObject !== undefined) {
	        usedOptions = referenceOptions[subObject];
	      }
	      Validator.parse(options, usedOptions, []);
	      return errorFound;
	    }

	    
	  }, {
	    key: "parse",
	    value: function parse(options, referenceOptions, path) {
	      for (var option in options) {
	        if (options.hasOwnProperty(option)) {
	          Validator.check(option, options, referenceOptions, path);
	        }
	      }
	    }

	    
	  }, {
	    key: "check",
	    value: function check(option, options, referenceOptions, path) {
	      if (referenceOptions[option] === undefined && referenceOptions.__any__ === undefined) {
	        Validator.getSuggestion(option, referenceOptions, path);
	        return;
	      }
	      var referenceOption = option;
	      var is_object = true;
	      if (referenceOptions[option] === undefined && referenceOptions.__any__ !== undefined) {
	        
	        
	        

	        
	        referenceOption = '__any__';

	        
	        
	        is_object = Validator.getType(options[option]) === 'object';
	      }
	      var refOptionObj = referenceOptions[referenceOption];
	      if (is_object && refOptionObj.__type__ !== undefined) {
	        refOptionObj = refOptionObj.__type__;
	      }
	      Validator.checkFields(option, options, referenceOptions, referenceOption, refOptionObj, path);
	    }

	    
	  }, {
	    key: "checkFields",
	    value: function checkFields(option, options, referenceOptions, referenceOption, refOptionObj, path) {
	      var log = function log(message) {
	        console.log('%c' + message + Validator.printLocation(path, option), printStyle);
	      };
	      var optionType = Validator.getType(options[option]);
	      var refOptionType = refOptionObj[optionType];
	      if (refOptionType !== undefined) {
	        
	        if (Validator.getType(refOptionType) === 'array' && _indexOfInstanceProperty(refOptionType).call(refOptionType, options[option]) === -1) {
	          log('Invalid option detected in "' + option + '".' + ' Allowed values are:' + Validator.print(refOptionType) + ' not "' + options[option] + '". ');
	          errorFound = true;
	        } else if (optionType === 'object' && referenceOption !== "__any__") {
	          path = availableUtils.copyAndExtendArray(path, option);
	          Validator.parse(options[option], referenceOptions[referenceOption], path);
	        }
	      } else if (refOptionObj['any'] === undefined) {
	        
	        log('Invalid type received for "' + option + '". Expected: ' + Validator.print(_Object$keys(refOptionObj)) + '. Received [' + optionType + '] "' + options[option] + '"');
	        errorFound = true;
	      }
	    }

	    
	  }, {
	    key: "getType",
	    value: function getType(object) {
	      var type = _typeof(object);
	      if (type === 'object') {
	        if (object === null) {
	          return 'null';
	        }
	        if (object instanceof Boolean) {
	          return 'boolean';
	        }
	        if (object instanceof Number) {
	          return 'number';
	        }
	        if (object instanceof String) {
	          return 'string';
	        }
	        if (_Array$isArray$1(object)) {
	          return 'array';
	        }
	        if (object instanceof Date) {
	          return 'date';
	        }
	        if (object.nodeType !== undefined) {
	          return 'dom';
	        }
	        if (object._isAMomentObject === true) {
	          return 'moment';
	        }
	        return 'object';
	      } else if (type === 'number') {
	        return 'number';
	      } else if (type === 'boolean') {
	        return 'boolean';
	      } else if (type === 'string') {
	        return 'string';
	      } else if (type === undefined) {
	        return 'undefined';
	      }
	      return type;
	    }

	    
	  }, {
	    key: "getSuggestion",
	    value: function getSuggestion(option, options, path) {
	      var localSearch = Validator.findInOptions(option, options, path, false);
	      var globalSearch = Validator.findInOptions(option, allOptions$2, [], true);
	      var localSearchThreshold = 8;
	      var globalSearchThreshold = 4;
	      var msg;
	      if (localSearch.indexMatch !== undefined) {
	        msg = ' in ' + Validator.printLocation(localSearch.path, option, '') + 'Perhaps it was incomplete? Did you mean: "' + localSearch.indexMatch + '"?\n\n';
	      } else if (globalSearch.distance <= globalSearchThreshold && localSearch.distance > globalSearch.distance) {
	        msg = ' in ' + Validator.printLocation(localSearch.path, option, '') + 'Perhaps it was misplaced? Matching option found at: ' + Validator.printLocation(globalSearch.path, globalSearch.closestMatch, '');
	      } else if (localSearch.distance <= localSearchThreshold) {
	        msg = '. Did you mean "' + localSearch.closestMatch + '"?' + Validator.printLocation(localSearch.path, option);
	      } else {
	        msg = '. Did you mean one of these: ' + Validator.print(_Object$keys(options)) + Validator.printLocation(path, option);
	      }
	      console.log('%cUnknown option detected: "' + option + '"' + msg, printStyle);
	      errorFound = true;
	    }

	    
	  }, {
	    key: "findInOptions",
	    value: function findInOptions(option, options, path) {
	      var recursive = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : false;
	      var min = 1e9;
	      var closestMatch = '';
	      var closestMatchPath = [];
	      var lowerCaseOption = option.toLowerCase();
	      var indexMatch = undefined;
	      for (var op in options) {
	        
	        var distance = void 0;
	        if (options[op].__type__ !== undefined && recursive === true) {
	          var result = Validator.findInOptions(option, options[op], availableUtils.copyAndExtendArray(path, op));
	          if (min > result.distance) {
	            closestMatch = result.closestMatch;
	            closestMatchPath = result.path;
	            min = result.distance;
	            indexMatch = result.indexMatch;
	          }
	        } else {
	          var _context;
	          if (_indexOfInstanceProperty(_context = op.toLowerCase()).call(_context, lowerCaseOption) !== -1) {
	            indexMatch = op;
	          }
	          distance = Validator.levenshteinDistance(option, op);
	          if (min > distance) {
	            closestMatch = op;
	            closestMatchPath = availableUtils.copyArray(path);
	            min = distance;
	          }
	        }
	      }
	      return {
	        closestMatch: closestMatch,
	        path: closestMatchPath,
	        distance: min,
	        indexMatch: indexMatch
	      };
	    }

	    
	  }, {
	    key: "printLocation",
	    value: function printLocation(path, option) {
	      var prefix = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'Problem value found at: \n';
	      var str = '\n\n' + prefix + 'options = {\n';
	      for (var i = 0; i < path.length; i++) {
	        for (var j = 0; j < i + 1; j++) {
	          str += '  ';
	        }
	        str += path[i] + ': {\n';
	      }
	      for (var _j = 0; _j < path.length + 1; _j++) {
	        str += '  ';
	      }
	      str += option + '\n';
	      for (var _i = 0; _i < path.length + 1; _i++) {
	        for (var _j2 = 0; _j2 < path.length - _i; _j2++) {
	          str += '  ';
	        }
	        str += '}\n';
	      }
	      return str + '\n\n';
	    }

	    
	  }, {
	    key: "print",
	    value: function print(options) {
	      return _JSON$stringify(options).replace(/(\")|(\[)|(\])|(,"__type__")/g, "").replace(/(\,)/g, ', ');
	    }

	    
	  }, {
	    key: "levenshteinDistance",
	    value: function levenshteinDistance(a, b) {
	      if (a.length === 0) return b.length;
	      if (b.length === 0) return a.length;
	      var matrix = [];

	      
	      var i;
	      for (i = 0; i <= b.length; i++) {
	        matrix[i] = [i];
	      }

	      
	      var j;
	      for (j = 0; j <= a.length; j++) {
	        matrix[0][j] = j;
	      }

	      
	      for (i = 1; i <= b.length; i++) {
	        for (j = 1; j <= a.length; j++) {
	          if (b.charAt(i - 1) == a.charAt(j - 1)) {
	            matrix[i][j] = matrix[i - 1][j - 1];
	          } else {
	            matrix[i][j] = Math.min(matrix[i - 1][j - 1] + 1,
	            
	            Math.min(matrix[i][j - 1] + 1,
	            
	            matrix[i - 1][j] + 1)); 
	          }
	        }
	      }

	      return matrix[b.length][a.length];
	    }
	  }]);
	  return Validator;
	}();

	
	var string$1 = 'string';
	var bool$1 = 'boolean';
	var number$1 = 'number';
	var array$1 = 'array';
	var date$1 = 'date';
	var object$1 = 'object'; 
	var dom$1 = 'dom';
	var moment$1 = 'moment';
	var any$1 = 'any';
	var allOptions$1 = {
	  configure: {
	    enabled: {
	      'boolean': bool$1
	    },
	    filter: {
	      'boolean': bool$1,
	      'function': 'function'
	    },
	    container: {
	      dom: dom$1
	    },
	    __type__: {
	      object: object$1,
	      'boolean': bool$1,
	      'function': 'function'
	    }
	  },
	  
	  align: {
	    string: string$1
	  },
	  alignCurrentTime: {
	    string: string$1,
	    'undefined': 'undefined'
	  },
	  rtl: {
	    'boolean': bool$1,
	    'undefined': 'undefined'
	  },
	  rollingMode: {
	    follow: {
	      'boolean': bool$1
	    },
	    offset: {
	      number: number$1,
	      'undefined': 'undefined'
	    },
	    __type__: {
	      object: object$1
	    }
	  },
	  onTimeout: {
	    timeoutMs: {
	      number: number$1
	    },
	    callback: {
	      'function': 'function'
	    },
	    __type__: {
	      object: object$1
	    }
	  },
	  verticalScroll: {
	    'boolean': bool$1,
	    'undefined': 'undefined'
	  },
	  horizontalScroll: {
	    'boolean': bool$1,
	    'undefined': 'undefined'
	  },
	  autoResize: {
	    'boolean': bool$1
	  },
	  throttleRedraw: {
	    number: number$1
	  },
	  
	  clickToUse: {
	    'boolean': bool$1
	  },
	  dataAttributes: {
	    string: string$1,
	    array: array$1
	  },
	  editable: {
	    add: {
	      'boolean': bool$1,
	      'undefined': 'undefined'
	    },
	    remove: {
	      'boolean': bool$1,
	      'undefined': 'undefined'
	    },
	    updateGroup: {
	      'boolean': bool$1,
	      'undefined': 'undefined'
	    },
	    updateTime: {
	      'boolean': bool$1,
	      'undefined': 'undefined'
	    },
	    overrideItems: {
	      'boolean': bool$1,
	      'undefined': 'undefined'
	    },
	    __type__: {
	      'boolean': bool$1,
	      object: object$1
	    }
	  },
	  end: {
	    number: number$1,
	    date: date$1,
	    string: string$1,
	    moment: moment$1
	  },
	  format: {
	    minorLabels: {
	      millisecond: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      second: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      minute: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      hour: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      weekday: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      day: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      week: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      month: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      year: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      __type__: {
	        object: object$1,
	        'function': 'function'
	      }
	    },
	    majorLabels: {
	      millisecond: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      second: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      minute: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      hour: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      weekday: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      day: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      week: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      month: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      year: {
	        string: string$1,
	        'undefined': 'undefined'
	      },
	      __type__: {
	        object: object$1,
	        'function': 'function'
	      }
	    },
	    __type__: {
	      object: object$1
	    }
	  },
	  moment: {
	    'function': 'function'
	  },
	  groupHeightMode: {
	    string: string$1
	  },
	  groupOrder: {
	    string: string$1,
	    'function': 'function'
	  },
	  groupEditable: {
	    add: {
	      'boolean': bool$1,
	      'undefined': 'undefined'
	    },
	    remove: {
	      'boolean': bool$1,
	      'undefined': 'undefined'
	    },
	    order: {
	      'boolean': bool$1,
	      'undefined': 'undefined'
	    },
	    __type__: {
	      'boolean': bool$1,
	      object: object$1
	    }
	  },
	  groupOrderSwap: {
	    'function': 'function'
	  },
	  height: {
	    string: string$1,
	    number: number$1
	  },
	  hiddenDates: {
	    start: {
	      date: date$1,
	      number: number$1,
	      string: string$1,
	      moment: moment$1
	    },
	    end: {
	      date: date$1,
	      number: number$1,
	      string: string$1,
	      moment: moment$1
	    },
	    repeat: {
	      string: string$1
	    },
	    __type__: {
	      object: object$1,
	      array: array$1
	    }
	  },
	  itemsAlwaysDraggable: {
	    item: {
	      'boolean': bool$1,
	      'undefined': 'undefined'
	    },
	    range: {
	      'boolean': bool$1,
	      'undefined': 'undefined'
	    },
	    __type__: {
	      'boolean': bool$1,
	      object: object$1
	    }
	  },
	  limitSize: {
	    'boolean': bool$1
	  },
	  locale: {
	    string: string$1
	  },
	  locales: {
	    __any__: {
	      any: any$1
	    },
	    __type__: {
	      object: object$1
	    }
	  },
	  longSelectPressTime: {
	    number: number$1
	  },
	  margin: {
	    axis: {
	      number: number$1
	    },
	    item: {
	      horizontal: {
	        number: number$1,
	        'undefined': 'undefined'
	      },
	      vertical: {
	        number: number$1,
	        'undefined': 'undefined'
	      },
	      __type__: {
	        object: object$1,
	        number: number$1
	      }
	    },
	    __type__: {
	      object: object$1,
	      number: number$1
	    }
	  },
	  max: {
	    date: date$1,
	    number: number$1,
	    string: string$1,
	    moment: moment$1
	  },
	  maxHeight: {
	    number: number$1,
	    string: string$1
	  },
	  maxMinorChars: {
	    number: number$1
	  },
	  min: {
	    date: date$1,
	    number: number$1,
	    string: string$1,
	    moment: moment$1
	  },
	  minHeight: {
	    number: number$1,
	    string: string$1
	  },
	  moveable: {
	    'boolean': bool$1
	  },
	  multiselect: {
	    'boolean': bool$1
	  },
	  multiselectPerGroup: {
	    'boolean': bool$1
	  },
	  onAdd: {
	    'function': 'function'
	  },
	  onDropObjectOnItem: {
	    'function': 'function'
	  },
	  onUpdate: {
	    'function': 'function'
	  },
	  onMove: {
	    'function': 'function'
	  },
	  onMoving: {
	    'function': 'function'
	  },
	  onRemove: {
	    'function': 'function'
	  },
	  onAddGroup: {
	    'function': 'function'
	  },
	  onMoveGroup: {
	    'function': 'function'
	  },
	  onRemoveGroup: {
	    'function': 'function'
	  },
	  onInitialDrawComplete: {
	    'function': 'function'
	  },
	  order: {
	    'function': 'function'
	  },
	  orientation: {
	    axis: {
	      string: string$1,
	      'undefined': 'undefined'
	    },
	    item: {
	      string: string$1,
	      'undefined': 'undefined'
	    },
	    __type__: {
	      string: string$1,
	      object: object$1
	    }
	  },
	  selectable: {
	    'boolean': bool$1
	  },
	  sequentialSelection: {
	    'boolean': bool$1
	  },
	  showCurrentTime: {
	    'boolean': bool$1
	  },
	  showMajorLabels: {
	    'boolean': bool$1
	  },
	  showMinorLabels: {
	    'boolean': bool$1
	  },
	  showWeekScale: {
	    'boolean': bool$1
	  },
	  stack: {
	    'boolean': bool$1
	  },
	  stackSubgroups: {
	    'boolean': bool$1
	  },
	  cluster: {
	    maxItems: {
	      'number': number$1,
	      'undefined': 'undefined'
	    },
	    titleTemplate: {
	      'string': string$1,
	      'undefined': 'undefined'
	    },
	    clusterCriteria: {
	      'function': 'function',
	      'undefined': 'undefined'
	    },
	    showStipes: {
	      'boolean': bool$1,
	      'undefined': 'undefined'
	    },
	    fitOnDoubleClick: {
	      'boolean': bool$1,
	      'undefined': 'undefined'
	    },
	    __type__: {
	      'boolean': bool$1,
	      object: object$1
	    }
	  },
	  snap: {
	    'function': 'function',
	    'null': 'null'
	  },
	  start: {
	    date: date$1,
	    number: number$1,
	    string: string$1,
	    moment: moment$1
	  },
	  template: {
	    'function': 'function'
	  },
	  loadingScreenTemplate: {
	    'function': 'function'
	  },
	  groupTemplate: {
	    'function': 'function'
	  },
	  visibleFrameTemplate: {
	    string: string$1,
	    'function': 'function'
	  },
	  showTooltips: {
	    'boolean': bool$1
	  },
	  tooltip: {
	    followMouse: {
	      'boolean': bool$1
	    },
	    overflowMethod: {
	      'string': ['cap', 'flip', 'none']
	    },
	    delay: {
	      number: number$1
	    },
	    template: {
	      'function': 'function'
	    },
	    __type__: {
	      object: object$1
	    }
	  },
	  tooltipOnItemUpdateTime: {
	    template: {
	      'function': 'function'
	    },
	    __type__: {
	      'boolean': bool$1,
	      object: object$1
	    }
	  },
	  timeAxis: {
	    scale: {
	      string: string$1,
	      'undefined': 'undefined'
	    },
	    step: {
	      number: number$1,
	      'undefined': 'undefined'
	    },
	    __type__: {
	      object: object$1
	    }
	  },
	  type: {
	    string: string$1
	  },
	  width: {
	    string: string$1,
	    number: number$1
	  },
	  preferZoom: {
	    'boolean': bool$1
	  },
	  zoomable: {
	    'boolean': bool$1
	  },
	  zoomKey: {
	    string: ['ctrlKey', 'altKey', 'shiftKey', 'metaKey', '']
	  },
	  zoomFriction: {
	    number: number$1
	  },
	  zoomMax: {
	    number: number$1
	  },
	  zoomMin: {
	    number: number$1
	  },
	  xss: {
	    disabled: {
	      boolean: bool$1
	    },
	    filterOptions: {
	      __any__: {
	        any: any$1
	      },
	      __type__: {
	        object: object$1
	      }
	    },
	    __type__: {
	      object: object$1
	    }
	  },
	  __type__: {
	    object: object$1
	  }
	};
	var configureOptions$1 = {
	  global: {
	    align: ['center', 'left', 'right'],
	    alignCurrentTime: ['none', 'year', 'month', 'quarter', 'week', 'isoWeek', 'day', 'date', 'hour', 'minute', 'second'],
	    direction: false,
	    autoResize: true,
	    clickToUse: false,
	    
	    editable: {
	      add: false,
	      remove: false,
	      updateGroup: false,
	      updateTime: false
	    },
	    end: '',
	    format: {
	      minorLabels: {
	        millisecond: 'SSS',
	        second: 's',
	        minute: 'HH:mm',
	        hour: 'HH:mm',
	        weekday: 'ddd D',
	        day: 'D',
	        week: 'w',
	        month: 'MMM',
	        year: 'YYYY'
	      },
	      majorLabels: {
	        millisecond: 'HH:mm:ss',
	        second: 'D MMMM HH:mm',
	        minute: 'ddd D MMMM',
	        hour: 'ddd D MMMM',
	        weekday: 'MMMM YYYY',
	        day: 'MMMM YYYY',
	        week: 'MMMM YYYY',
	        month: 'YYYY',
	        year: ''
	      }
	    },
	    groupHeightMode: ['auto', 'fixed', 'fitItems'],
	    
	    groupsDraggable: false,
	    height: '',
	    
	    locale: '',
	    longSelectPressTime: 251,
	    margin: {
	      axis: [20, 0, 100, 1],
	      item: {
	        horizontal: [10, 0, 100, 1],
	        vertical: [10, 0, 100, 1]
	      }
	    },
	    max: '',
	    maxHeight: '',
	    maxMinorChars: [7, 0, 20, 1],
	    min: '',
	    minHeight: '',
	    moveable: false,
	    multiselect: false,
	    multiselectPerGroup: false,
	    
	    
	    
	    
	    
	    
	    orientation: {
	      axis: ['both', 'bottom', 'top'],
	      item: ['bottom', 'top']
	    },
	    preferZoom: false,
	    selectable: true,
	    showCurrentTime: false,
	    showMajorLabels: true,
	    showMinorLabels: true,
	    stack: true,
	    stackSubgroups: true,
	    cluster: false,
	    
	    start: '',
	    
	    
	    
	    
	    
	    showTooltips: true,
	    tooltip: {
	      followMouse: false,
	      overflowMethod: 'flip',
	      delay: [500, 0, 99999, 100]
	    },
	    tooltipOnItemUpdateTime: false,
	    type: ['box', 'point', 'range', 'background'],
	    width: '100%',
	    zoomable: true,
	    zoomKey: ['ctrlKey', 'altKey', 'shiftKey', 'metaKey', ''],
	    zoomMax: [315360000000000, 10, 315360000000000, 1],
	    zoomMin: [10, 10, 315360000000000, 1],
	    xss: {
	      disabled: false
	    }
	  }
	};

	var htmlColors = {
	  black: '#000000',
	  navy: '#000080',
	  darkblue: '#00008B',
	  mediumblue: '#0000CD',
	  blue: '#0000FF',
	  darkgreen: '#006400',
	  green: '#008000',
	  teal: '#008080',
	  darkcyan: '#008B8B',
	  deepskyblue: '#00BFFF',
	  darkturquoise: '#00CED1',
	  mediumspringgreen: '#00FA9A',
	  lime: '#00FF00',
	  springgreen: '#00FF7F',
	  aqua: '#00FFFF',
	  cyan: '#00FFFF',
	  midnightblue: '#191970',
	  dodgerblue: '#1E90FF',
	  lightseagreen: '#20B2AA',
	  forestgreen: '#228B22',
	  seagreen: '#2E8B57',
	  darkslategray: '#2F4F4F',
	  limegreen: '#32CD32',
	  mediumseagreen: '#3CB371',
	  turquoise: '#40E0D0',
	  royalblue: '#4169E1',
	  steelblue: '#4682B4',
	  darkslateblue: '#483D8B',
	  mediumturquoise: '#48D1CC',
	  indigo: '#4B0082',
	  darkolivegreen: '#556B2F',
	  cadetblue: '#5F9EA0',
	  cornflowerblue: '#6495ED',
	  mediumaquamarine: '#66CDAA',
	  dimgray: '#696969',
	  slateblue: '#6A5ACD',
	  olivedrab: '#6B8E23',
	  slategray: '#708090',
	  lightslategray: '#778899',
	  mediumslateblue: '#7B68EE',
	  lawngreen: '#7CFC00',
	  chartreuse: '#7FFF00',
	  aquamarine: '#7FFFD4',
	  maroon: '#800000',
	  purple: '#800080',
	  olive: '#808000',
	  gray: '#808080',
	  skyblue: '#87CEEB',
	  lightskyblue: '#87CEFA',
	  blueviolet: '#8A2BE2',
	  darkred: '#8B0000',
	  darkmagenta: '#8B008B',
	  saddlebrown: '#8B4513',
	  darkseagreen: '#8FBC8F',
	  lightgreen: '#90EE90',
	  mediumpurple: '#9370D8',
	  darkviolet: '#9400D3',
	  palegreen: '#98FB98',
	  darkorchid: '#9932CC',
	  yellowgreen: '#9ACD32',
	  sienna: '#A0522D',
	  brown: '#A52A2A',
	  darkgray: '#A9A9A9',
	  lightblue: '#ADD8E6',
	  greenyellow: '#ADFF2F',
	  paleturquoise: '#AFEEEE',
	  lightsteelblue: '#B0C4DE',
	  powderblue: '#B0E0E6',
	  firebrick: '#B22222',
	  darkgoldenrod: '#B8860B',
	  mediumorchid: '#BA55D3',
	  rosybrown: '#BC8F8F',
	  darkkhaki: '#BDB76B',
	  silver: '#C0C0C0',
	  mediumvioletred: '#C71585',
	  indianred: '#CD5C5C',
	  peru: '#CD853F',
	  chocolate: '#D2691E',
	  tan: '#D2B48C',
	  lightgrey: '#D3D3D3',
	  palevioletred: '#D87093',
	  thistle: '#D8BFD8',
	  orchid: '#DA70D6',
	  goldenrod: '#DAA520',
	  crimson: '#DC143C',
	  gainsboro: '#DCDCDC',
	  plum: '#DDA0DD',
	  burlywood: '#DEB887',
	  lightcyan: '#E0FFFF',
	  lavender: '#E6E6FA',
	  darksalmon: '#E9967A',
	  violet: '#EE82EE',
	  palegoldenrod: '#EEE8AA',
	  lightcoral: '#F08080',
	  khaki: '#F0E68C',
	  aliceblue: '#F0F8FF',
	  honeydew: '#F0FFF0',
	  azure: '#F0FFFF',
	  sandybrown: '#F4A460',
	  wheat: '#F5DEB3',
	  beige: '#F5F5DC',
	  whitesmoke: '#F5F5F5',
	  mintcream: '#F5FFFA',
	  ghostwhite: '#F8F8FF',
	  salmon: '#FA8072',
	  antiquewhite: '#FAEBD7',
	  linen: '#FAF0E6',
	  lightgoldenrodyellow: '#FAFAD2',
	  oldlace: '#FDF5E6',
	  red: '#FF0000',
	  fuchsia: '#FF00FF',
	  magenta: '#FF00FF',
	  deeppink: '#FF1493',
	  orangered: '#FF4500',
	  tomato: '#FF6347',
	  hotpink: '#FF69B4',
	  coral: '#FF7F50',
	  darkorange: '#FF8C00',
	  lightsalmon: '#FFA07A',
	  orange: '#FFA500',
	  lightpink: '#FFB6C1',
	  pink: '#FFC0CB',
	  gold: '#FFD700',
	  peachpuff: '#FFDAB9',
	  navajowhite: '#FFDEAD',
	  moccasin: '#FFE4B5',
	  bisque: '#FFE4C4',
	  mistyrose: '#FFE4E1',
	  blanchedalmond: '#FFEBCD',
	  papayawhip: '#FFEFD5',
	  lavenderblush: '#FFF0F5',
	  seashell: '#FFF5EE',
	  cornsilk: '#FFF8DC',
	  lemonchiffon: '#FFFACD',
	  floralwhite: '#FFFAF0',
	  snow: '#FFFAFA',
	  yellow: '#FFFF00',
	  lightyellow: '#FFFFE0',
	  ivory: '#FFFFF0',
	  white: '#FFFFFF'
	};

	
	var ColorPicker = function () {
	  
	  function ColorPicker() {
	    var pixelRatio = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 1;
	    _classCallCheck(this, ColorPicker);
	    this.pixelRatio = pixelRatio;
	    this.generated = false;
	    this.centerCoordinates = {
	      x: 289 / 2,
	      y: 289 / 2
	    };
	    this.r = 289 * 0.49;
	    this.color = {
	      r: 255,
	      g: 255,
	      b: 255,
	      a: 1.0
	    };
	    this.hueCircle = undefined;
	    this.initialColor = {
	      r: 255,
	      g: 255,
	      b: 255,
	      a: 1.0
	    };
	    this.previousColor = undefined;
	    this.applied = false;

	    
	    this.updateCallback = function () {};
	    this.closeCallback = function () {};

	    
	    this._create();
	  }

	  
	  _createClass(ColorPicker, [{
	    key: "insertTo",
	    value: function insertTo(container) {
	      if (this.hammer !== undefined) {
	        this.hammer.destroy();
	        this.hammer = undefined;
	      }
	      this.container = container;
	      this.container.appendChild(this.frame);
	      this._bindHammer();
	      this._setSize();
	    }

	    
	  }, {
	    key: "setUpdateCallback",
	    value: function setUpdateCallback(callback) {
	      if (typeof callback === 'function') {
	        this.updateCallback = callback;
	      } else {
	        throw new Error("Function attempted to set as colorPicker update callback is not a function.");
	      }
	    }

	    
	  }, {
	    key: "setCloseCallback",
	    value: function setCloseCallback(callback) {
	      if (typeof callback === 'function') {
	        this.closeCallback = callback;
	      } else {
	        throw new Error("Function attempted to set as colorPicker closing callback is not a function.");
	      }
	    }

	    
	  }, {
	    key: "_isColorString",
	    value: function _isColorString(color) {
	      if (typeof color === 'string') {
	        return htmlColors[color];
	      }
	    }

	    
	  }, {
	    key: "setColor",
	    value: function setColor(color) {
	      var setInitial = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
	      if (color === 'none') {
	        return;
	      }
	      var rgba;

	      
	      var htmlColor = this._isColorString(color);
	      if (htmlColor !== undefined) {
	        color = htmlColor;
	      }

	      
	      if (availableUtils.isString(color) === true) {
	        if (availableUtils.isValidRGB(color) === true) {
	          var rgbaArray = color.substr(4).substr(0, color.length - 5).split(',');
	          rgba = {
	            r: rgbaArray[0],
	            g: rgbaArray[1],
	            b: rgbaArray[2],
	            a: 1.0
	          };
	        } else if (availableUtils.isValidRGBA(color) === true) {
	          var _rgbaArray = color.substr(5).substr(0, color.length - 6).split(',');
	          rgba = {
	            r: _rgbaArray[0],
	            g: _rgbaArray[1],
	            b: _rgbaArray[2],
	            a: _rgbaArray[3]
	          };
	        } else if (availableUtils.isValidHex(color) === true) {
	          var rgbObj = availableUtils.hexToRGB(color);
	          rgba = {
	            r: rgbObj.r,
	            g: rgbObj.g,
	            b: rgbObj.b,
	            a: 1.0
	          };
	        }
	      } else {
	        if (color instanceof Object) {
	          if (color.r !== undefined && color.g !== undefined && color.b !== undefined) {
	            var alpha = color.a !== undefined ? color.a : '1.0';
	            rgba = {
	              r: color.r,
	              g: color.g,
	              b: color.b,
	              a: alpha
	            };
	          }
	        }
	      }

	      
	      if (rgba === undefined) {
	        throw new Error("Unknown color passed to the colorPicker. Supported are strings: rgb, hex, rgba. Object: rgb ({r:r,g:g,b:b,[a:a]}). Supplied: " + _JSON$stringify(color));
	      } else {
	        this._setColor(rgba, setInitial);
	      }
	    }

	    
	  }, {
	    key: "show",
	    value: function show() {
	      if (this.closeCallback !== undefined) {
	        this.closeCallback();
	        this.closeCallback = undefined;
	      }
	      this.applied = false;
	      this.frame.style.display = 'block';
	      this._generateHueCircle();
	    }

	    

	    
	  }, {
	    key: "_hide",
	    value: function _hide() {
	      var _this = this;
	      var storePrevious = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
	      
	      if (storePrevious === true) {
	        this.previousColor = availableUtils.extend({}, this.color);
	      }
	      if (this.applied === true) {
	        this.updateCallback(this.initialColor);
	      }
	      this.frame.style.display = 'none';

	      
	      
	      _setTimeout(function () {
	        if (_this.closeCallback !== undefined) {
	          _this.closeCallback();
	          _this.closeCallback = undefined;
	        }
	      }, 0);
	    }

	    
	  }, {
	    key: "_save",
	    value: function _save() {
	      this.updateCallback(this.color);
	      this.applied = false;
	      this._hide();
	    }

	    
	  }, {
	    key: "_apply",
	    value: function _apply() {
	      this.applied = true;
	      this.updateCallback(this.color);
	      this._updatePicker(this.color);
	    }

	    
	  }, {
	    key: "_loadLast",
	    value: function _loadLast() {
	      if (this.previousColor !== undefined) {
	        this.setColor(this.previousColor, false);
	      } else {
	        alert("There is no last color to load...");
	      }
	    }

	    
	  }, {
	    key: "_setColor",
	    value: function _setColor(rgba) {
	      var setInitial = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
	      
	      if (setInitial === true) {
	        this.initialColor = availableUtils.extend({}, rgba);
	      }
	      this.color = rgba;
	      var hsv = availableUtils.RGBToHSV(rgba.r, rgba.g, rgba.b);
	      var angleConvert = 2 * Math.PI;
	      var radius = this.r * hsv.s;
	      var x = this.centerCoordinates.x + radius * Math.sin(angleConvert * hsv.h);
	      var y = this.centerCoordinates.y + radius * Math.cos(angleConvert * hsv.h);
	      this.colorPickerSelector.style.left = x - 0.5 * this.colorPickerSelector.clientWidth + 'px';
	      this.colorPickerSelector.style.top = y - 0.5 * this.colorPickerSelector.clientHeight + 'px';
	      this._updatePicker(rgba);
	    }

	    
	  }, {
	    key: "_setOpacity",
	    value: function _setOpacity(value) {
	      this.color.a = value / 100;
	      this._updatePicker(this.color);
	    }

	    
	  }, {
	    key: "_setBrightness",
	    value: function _setBrightness(value) {
	      var hsv = availableUtils.RGBToHSV(this.color.r, this.color.g, this.color.b);
	      hsv.v = value / 100;
	      var rgba = availableUtils.HSVToRGB(hsv.h, hsv.s, hsv.v);
	      rgba['a'] = this.color.a;
	      this.color = rgba;
	      this._updatePicker();
	    }

	    
	  }, {
	    key: "_updatePicker",
	    value: function _updatePicker() {
	      var rgba = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : this.color;
	      var hsv = availableUtils.RGBToHSV(rgba.r, rgba.g, rgba.b);
	      var ctx = this.colorPickerCanvas.getContext('2d');
	      if (this.pixelRation === undefined) {
	        this.pixelRatio = (window.devicePixelRatio || 1) / (ctx.webkitBackingStorePixelRatio || ctx.mozBackingStorePixelRatio || ctx.msBackingStorePixelRatio || ctx.oBackingStorePixelRatio || ctx.backingStorePixelRatio || 1);
	      }
	      ctx.setTransform(this.pixelRatio, 0, 0, this.pixelRatio, 0, 0);

	      
	      var w = this.colorPickerCanvas.clientWidth;
	      var h = this.colorPickerCanvas.clientHeight;
	      ctx.clearRect(0, 0, w, h);
	      ctx.putImageData(this.hueCircle, 0, 0);
	      ctx.fillStyle = 'rgba(0,0,0,' + (1 - hsv.v) + ')';
	      ctx.circle(this.centerCoordinates.x, this.centerCoordinates.y, this.r);
	      _fillInstanceProperty(ctx).call(ctx);
	      this.brightnessRange.value = 100 * hsv.v;
	      this.opacityRange.value = 100 * rgba.a;
	      this.initialColorDiv.style.backgroundColor = 'rgba(' + this.initialColor.r + ',' + this.initialColor.g + ',' + this.initialColor.b + ',' + this.initialColor.a + ')';
	      this.newColorDiv.style.backgroundColor = 'rgba(' + this.color.r + ',' + this.color.g + ',' + this.color.b + ',' + this.color.a + ')';
	    }

	    
	  }, {
	    key: "_setSize",
	    value: function _setSize() {
	      this.colorPickerCanvas.style.width = '100%';
	      this.colorPickerCanvas.style.height = '100%';
	      this.colorPickerCanvas.width = 289 * this.pixelRatio;
	      this.colorPickerCanvas.height = 289 * this.pixelRatio;
	    }

	    
	  }, {
	    key: "_create",
	    value: function _create() {
	      var _context, _context2, _context3, _context4;
	      this.frame = document.createElement('div');
	      this.frame.className = 'vis-color-picker';
	      this.colorPickerDiv = document.createElement('div');
	      this.colorPickerSelector = document.createElement('div');
	      this.colorPickerSelector.className = 'vis-selector';
	      this.colorPickerDiv.appendChild(this.colorPickerSelector);
	      this.colorPickerCanvas = document.createElement('canvas');
	      this.colorPickerDiv.appendChild(this.colorPickerCanvas);
	      if (!this.colorPickerCanvas.getContext) {
	        var noCanvas = document.createElement('DIV');
	        noCanvas.style.color = 'red';
	        noCanvas.style.fontWeight = 'bold';
	        noCanvas.style.padding = '10px';
	        noCanvas.innerHTML = 'Error: your browser does not support HTML canvas';
	        this.colorPickerCanvas.appendChild(noCanvas);
	      } else {
	        var ctx = this.colorPickerCanvas.getContext("2d");
	        this.pixelRatio = (window.devicePixelRatio || 1) / (ctx.webkitBackingStorePixelRatio || ctx.mozBackingStorePixelRatio || ctx.msBackingStorePixelRatio || ctx.oBackingStorePixelRatio || ctx.backingStorePixelRatio || 1);
	        this.colorPickerCanvas.getContext("2d").setTransform(this.pixelRatio, 0, 0, this.pixelRatio, 0, 0);
	      }
	      this.colorPickerDiv.className = 'vis-color';
	      this.opacityDiv = document.createElement('div');
	      this.opacityDiv.className = 'vis-opacity';
	      this.brightnessDiv = document.createElement('div');
	      this.brightnessDiv.className = 'vis-brightness';
	      this.arrowDiv = document.createElement('div');
	      this.arrowDiv.className = 'vis-arrow';
	      this.opacityRange = document.createElement('input');
	      try {
	        this.opacityRange.type = 'range'; 
	        this.opacityRange.min = '0';
	        this.opacityRange.max = '100';
	      }
	      
	      catch (err) {} 
	      this.opacityRange.value = '100';
	      this.opacityRange.className = 'vis-range';
	      this.brightnessRange = document.createElement('input');
	      try {
	        this.brightnessRange.type = 'range'; 
	        this.brightnessRange.min = '0';
	        this.brightnessRange.max = '100';
	      }
	      
	      catch (err) {} 
	      this.brightnessRange.value = '100';
	      this.brightnessRange.className = 'vis-range';
	      this.opacityDiv.appendChild(this.opacityRange);
	      this.brightnessDiv.appendChild(this.brightnessRange);
	      var me = this;
	      this.opacityRange.onchange = function () {
	        me._setOpacity(this.value);
	      };
	      this.opacityRange.oninput = function () {
	        me._setOpacity(this.value);
	      };
	      this.brightnessRange.onchange = function () {
	        me._setBrightness(this.value);
	      };
	      this.brightnessRange.oninput = function () {
	        me._setBrightness(this.value);
	      };
	      this.brightnessLabel = document.createElement("div");
	      this.brightnessLabel.className = "vis-label vis-brightness";
	      this.brightnessLabel.innerHTML = 'brightness:';
	      this.opacityLabel = document.createElement("div");
	      this.opacityLabel.className = "vis-label vis-opacity";
	      this.opacityLabel.innerHTML = 'opacity:';
	      this.newColorDiv = document.createElement("div");
	      this.newColorDiv.className = "vis-new-color";
	      this.newColorDiv.innerHTML = 'new';
	      this.initialColorDiv = document.createElement("div");
	      this.initialColorDiv.className = "vis-initial-color";
	      this.initialColorDiv.innerHTML = 'initial';
	      this.cancelButton = document.createElement("div");
	      this.cancelButton.className = "vis-button vis-cancel";
	      this.cancelButton.innerHTML = 'cancel';
	      this.cancelButton.onclick = _bindInstanceProperty(_context = this._hide).call(_context, this, false);
	      this.applyButton = document.createElement("div");
	      this.applyButton.className = "vis-button vis-apply";
	      this.applyButton.innerHTML = 'apply';
	      this.applyButton.onclick = _bindInstanceProperty(_context2 = this._apply).call(_context2, this);
	      this.saveButton = document.createElement("div");
	      this.saveButton.className = "vis-button vis-save";
	      this.saveButton.innerHTML = 'save';
	      this.saveButton.onclick = _bindInstanceProperty(_context3 = this._save).call(_context3, this);
	      this.loadButton = document.createElement("div");
	      this.loadButton.className = "vis-button vis-load";
	      this.loadButton.innerHTML = 'load last';
	      this.loadButton.onclick = _bindInstanceProperty(_context4 = this._loadLast).call(_context4, this);
	      this.frame.appendChild(this.colorPickerDiv);
	      this.frame.appendChild(this.arrowDiv);
	      this.frame.appendChild(this.brightnessLabel);
	      this.frame.appendChild(this.brightnessDiv);
	      this.frame.appendChild(this.opacityLabel);
	      this.frame.appendChild(this.opacityDiv);
	      this.frame.appendChild(this.newColorDiv);
	      this.frame.appendChild(this.initialColorDiv);
	      this.frame.appendChild(this.cancelButton);
	      this.frame.appendChild(this.applyButton);
	      this.frame.appendChild(this.saveButton);
	      this.frame.appendChild(this.loadButton);
	    }

	    
	  }, {
	    key: "_bindHammer",
	    value: function _bindHammer() {
	      var _this2 = this;
	      this.drag = {};
	      this.pinch = {};
	      this.hammer = new Hammer(this.colorPickerCanvas);
	      this.hammer.get('pinch').set({
	        enable: true
	      });
	      onTouch(this.hammer, function (event) {
	        _this2._moveSelector(event);
	      });
	      this.hammer.on('tap', function (event) {
	        _this2._moveSelector(event);
	      });
	      this.hammer.on('panstart', function (event) {
	        _this2._moveSelector(event);
	      });
	      this.hammer.on('panmove', function (event) {
	        _this2._moveSelector(event);
	      });
	      this.hammer.on('panend', function (event) {
	        _this2._moveSelector(event);
	      });
	    }

	    
	  }, {
	    key: "_generateHueCircle",
	    value: function _generateHueCircle() {
	      if (this.generated === false) {
	        var ctx = this.colorPickerCanvas.getContext('2d');
	        if (this.pixelRation === undefined) {
	          this.pixelRatio = (window.devicePixelRatio || 1) / (ctx.webkitBackingStorePixelRatio || ctx.mozBackingStorePixelRatio || ctx.msBackingStorePixelRatio || ctx.oBackingStorePixelRatio || ctx.backingStorePixelRatio || 1);
	        }
	        ctx.setTransform(this.pixelRatio, 0, 0, this.pixelRatio, 0, 0);

	        
	        var w = this.colorPickerCanvas.clientWidth;
	        var h = this.colorPickerCanvas.clientHeight;
	        ctx.clearRect(0, 0, w, h);

	        
	        var x, y, hue, sat;
	        this.centerCoordinates = {
	          x: w * 0.5,
	          y: h * 0.5
	        };
	        this.r = 0.49 * w;
	        var angleConvert = 2 * Math.PI / 360;
	        var hfac = 1 / 360;
	        var sfac = 1 / this.r;
	        var rgb;
	        for (hue = 0; hue < 360; hue++) {
	          for (sat = 0; sat < this.r; sat++) {
	            x = this.centerCoordinates.x + sat * Math.sin(angleConvert * hue);
	            y = this.centerCoordinates.y + sat * Math.cos(angleConvert * hue);
	            rgb = availableUtils.HSVToRGB(hue * hfac, sat * sfac, 1);
	            ctx.fillStyle = 'rgb(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ')';
	            ctx.fillRect(x - 0.5, y - 0.5, 2, 2);
	          }
	        }
	        ctx.strokeStyle = 'rgba(0,0,0,1)';
	        ctx.circle(this.centerCoordinates.x, this.centerCoordinates.y, this.r);
	        ctx.stroke();
	        this.hueCircle = ctx.getImageData(0, 0, w, h);
	      }
	      this.generated = true;
	    }

	    
	  }, {
	    key: "_moveSelector",
	    value: function _moveSelector(event) {
	      var rect = this.colorPickerDiv.getBoundingClientRect();
	      var left = event.center.x - rect.left;
	      var top = event.center.y - rect.top;
	      var centerY = 0.5 * this.colorPickerDiv.clientHeight;
	      var centerX = 0.5 * this.colorPickerDiv.clientWidth;
	      var x = left - centerX;
	      var y = top - centerY;
	      var angle = Math.atan2(x, y);
	      var radius = 0.98 * Math.min(Math.sqrt(x * x + y * y), centerX);
	      var newTop = Math.cos(angle) * radius + centerY;
	      var newLeft = Math.sin(angle) * radius + centerX;
	      this.colorPickerSelector.style.top = newTop - 0.5 * this.colorPickerSelector.clientHeight + 'px';
	      this.colorPickerSelector.style.left = newLeft - 0.5 * this.colorPickerSelector.clientWidth + 'px';

	      
	      var h = angle / (2 * Math.PI);
	      h = h < 0 ? h + 1 : h;
	      var s = radius / this.r;
	      var hsv = availableUtils.RGBToHSV(this.color.r, this.color.g, this.color.b);
	      hsv.h = h;
	      hsv.s = s;
	      var rgba = availableUtils.HSVToRGB(hsv.h, hsv.s, hsv.v);
	      rgba['a'] = this.color.a;
	      this.color = rgba;

	      
	      this.initialColorDiv.style.backgroundColor = 'rgba(' + this.initialColor.r + ',' + this.initialColor.g + ',' + this.initialColor.b + ',' + this.initialColor.a + ')';
	      this.newColorDiv.style.backgroundColor = 'rgba(' + this.color.r + ',' + this.color.g + ',' + this.color.b + ',' + this.color.a + ')';
	    }
	  }]);
	  return ColorPicker;
	}();

	
	var Configurator = function () {
	  
	  function Configurator(parentModule, defaultContainer, configureOptions) {
	    var pixelRatio = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : 1;
	    _classCallCheck(this, Configurator);
	    this.parent = parentModule;
	    this.changedOptions = [];
	    this.container = defaultContainer;
	    this.allowCreation = false;
	    this.options = {};
	    this.initialized = false;
	    this.popupCounter = 0;
	    this.defaultOptions = {
	      enabled: false,
	      filter: true,
	      container: undefined,
	      showButton: true
	    };
	    availableUtils.extend(this.options, this.defaultOptions);
	    this.configureOptions = configureOptions;
	    this.moduleOptions = {};
	    this.domElements = [];
	    this.popupDiv = {};
	    this.popupLimit = 5;
	    this.popupHistory = {};
	    this.colorPicker = new ColorPicker(pixelRatio);
	    this.wrapper = undefined;
	  }

	  
	  _createClass(Configurator, [{
	    key: "setOptions",
	    value: function setOptions(options) {
	      if (options !== undefined) {
	        
	        this.popupHistory = {};
	        this._removePopup();
	        var enabled = true;
	        if (typeof options === 'string') {
	          this.options.filter = options;
	        } else if (_Array$isArray$1(options)) {
	          this.options.filter = options.join();
	        } else if (_typeof(options) === 'object') {
	          if (options == null) {
	            throw new TypeError('options cannot be null');
	          }
	          if (options.container !== undefined) {
	            this.options.container = options.container;
	          }
	          if (_filterInstanceProperty(options) !== undefined) {
	            this.options.filter = _filterInstanceProperty(options);
	          }
	          if (options.showButton !== undefined) {
	            this.options.showButton = options.showButton;
	          }
	          if (options.enabled !== undefined) {
	            enabled = options.enabled;
	          }
	        } else if (typeof options === 'boolean') {
	          this.options.filter = true;
	          enabled = options;
	        } else if (typeof options === 'function') {
	          this.options.filter = options;
	          enabled = true;
	        }
	        if (_filterInstanceProperty(this.options) === false) {
	          enabled = false;
	        }
	        this.options.enabled = enabled;
	      }
	      this._clean();
	    }

	    
	  }, {
	    key: "setModuleOptions",
	    value: function setModuleOptions(moduleOptions) {
	      this.moduleOptions = moduleOptions;
	      if (this.options.enabled === true) {
	        this._clean();
	        if (this.options.container !== undefined) {
	          this.container = this.options.container;
	        }
	        this._create();
	      }
	    }

	    
	  }, {
	    key: "_create",
	    value: function _create() {
	      this._clean();
	      this.changedOptions = [];
	      var filter = _filterInstanceProperty(this.options);
	      var counter = 0;
	      var show = false;
	      for (var option in this.configureOptions) {
	        if (this.configureOptions.hasOwnProperty(option)) {
	          this.allowCreation = false;
	          show = false;
	          if (typeof filter === 'function') {
	            show = filter(option, []);
	            show = show || this._handleObject(this.configureOptions[option], [option], true);
	          } else if (filter === true || _indexOfInstanceProperty(filter).call(filter, option) !== -1) {
	            show = true;
	          }
	          if (show !== false) {
	            this.allowCreation = true;

	            
	            if (counter > 0) {
	              this._makeItem([]);
	            }
	            
	            this._makeHeader(option);

	            
	            this._handleObject(this.configureOptions[option], [option]);
	          }
	          counter++;
	        }
	      }
	      this._makeButton();
	      this._push();
	      
	    }

	    
	  }, {
	    key: "_push",
	    value: function _push() {
	      this.wrapper = document.createElement('div');
	      this.wrapper.className = 'vis-configuration-wrapper';
	      this.container.appendChild(this.wrapper);
	      for (var i = 0; i < this.domElements.length; i++) {
	        this.wrapper.appendChild(this.domElements[i]);
	      }
	      this._showPopupIfNeeded();
	    }

	    
	  }, {
	    key: "_clean",
	    value: function _clean() {
	      for (var i = 0; i < this.domElements.length; i++) {
	        this.wrapper.removeChild(this.domElements[i]);
	      }
	      if (this.wrapper !== undefined) {
	        this.container.removeChild(this.wrapper);
	        this.wrapper = undefined;
	      }
	      this.domElements = [];
	      this._removePopup();
	    }

	    
	  }, {
	    key: "_getValue",
	    value: function _getValue(path) {
	      var base = this.moduleOptions;
	      for (var i = 0; i < path.length; i++) {
	        if (base[path[i]] !== undefined) {
	          base = base[path[i]];
	        } else {
	          base = undefined;
	          break;
	        }
	      }
	      return base;
	    }

	    
	  }, {
	    key: "_makeItem",
	    value: function _makeItem(path) {
	      if (this.allowCreation === true) {
	        var item = document.createElement('div');
	        item.className = 'vis-configuration vis-config-item vis-config-s' + path.length;
	        for (var _len = arguments.length, domElements = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
	          domElements[_key - 1] = arguments[_key];
	        }
	        _forEachInstanceProperty(domElements).call(domElements, function (element) {
	          item.appendChild(element);
	        });
	        this.domElements.push(item);
	        return this.domElements.length;
	      }
	      return 0;
	    }

	    
	  }, {
	    key: "_makeHeader",
	    value: function _makeHeader(name) {
	      var div = document.createElement('div');
	      div.className = 'vis-configuration vis-config-header';
	      div.innerHTML = availableUtils.xss(name);
	      this._makeItem([], div);
	    }

	    
	  }, {
	    key: "_makeLabel",
	    value: function _makeLabel(name, path) {
	      var objectLabel = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
	      var div = document.createElement('div');
	      div.className = 'vis-configuration vis-config-label vis-config-s' + path.length;
	      if (objectLabel === true) {
	        div.innerHTML = availableUtils.xss('<i><b>' + name + ':</b></i>');
	      } else {
	        div.innerHTML = availableUtils.xss(name + ':');
	      }
	      return div;
	    }

	    
	  }, {
	    key: "_makeDropdown",
	    value: function _makeDropdown(arr, value, path) {
	      var select = document.createElement('select');
	      select.className = 'vis-configuration vis-config-select';
	      var selectedValue = 0;
	      if (value !== undefined) {
	        if (_indexOfInstanceProperty(arr).call(arr, value) !== -1) {
	          selectedValue = _indexOfInstanceProperty(arr).call(arr, value);
	        }
	      }
	      for (var i = 0; i < arr.length; i++) {
	        var option = document.createElement('option');
	        option.value = arr[i];
	        if (i === selectedValue) {
	          option.selected = 'selected';
	        }
	        option.innerHTML = arr[i];
	        select.appendChild(option);
	      }
	      var me = this;
	      select.onchange = function () {
	        me._update(this.value, path);
	      };
	      var label = this._makeLabel(path[path.length - 1], path);
	      this._makeItem(path, label, select);
	    }

	    
	  }, {
	    key: "_makeRange",
	    value: function _makeRange(arr, value, path) {
	      var defaultValue = arr[0];
	      var min = arr[1];
	      var max = arr[2];
	      var step = arr[3];
	      var range = document.createElement('input');
	      range.className = 'vis-configuration vis-config-range';
	      try {
	        range.type = 'range'; 
	        range.min = min;
	        range.max = max;
	      }
	      
	      catch (err) {} 
	      range.step = step;

	      
	      var popupString = '';
	      var popupValue = 0;
	      if (value !== undefined) {
	        var factor = 1.20;
	        if (value < 0 && value * factor < min) {
	          range.min = Math.ceil(value * factor);
	          popupValue = range.min;
	          popupString = 'range increased';
	        } else if (value / factor < min) {
	          range.min = Math.ceil(value / factor);
	          popupValue = range.min;
	          popupString = 'range increased';
	        }
	        if (value * factor > max && max !== 1) {
	          range.max = Math.ceil(value * factor);
	          popupValue = range.max;
	          popupString = 'range increased';
	        }
	        range.value = value;
	      } else {
	        range.value = defaultValue;
	      }
	      var input = document.createElement('input');
	      input.className = 'vis-configuration vis-config-rangeinput';
	      input.value = Number(range.value);
	      var me = this;
	      range.onchange = function () {
	        input.value = this.value;
	        me._update(Number(this.value), path);
	      };
	      range.oninput = function () {
	        input.value = this.value;
	      };
	      var label = this._makeLabel(path[path.length - 1], path);
	      var itemIndex = this._makeItem(path, label, range, input);

	      
	      if (popupString !== '' && this.popupHistory[itemIndex] !== popupValue) {
	        this.popupHistory[itemIndex] = popupValue;
	        this._setupPopup(popupString, itemIndex);
	      }
	    }

	    
	  }, {
	    key: "_makeButton",
	    value: function _makeButton() {
	      var _this = this;
	      if (this.options.showButton === true) {
	        var generateButton = document.createElement('div');
	        generateButton.className = 'vis-configuration vis-config-button';
	        generateButton.innerHTML = 'generate options';
	        generateButton.onclick = function () {
	          _this._printOptions();
	        };
	        generateButton.onmouseover = function () {
	          generateButton.className = 'vis-configuration vis-config-button hover';
	        };
	        generateButton.onmouseout = function () {
	          generateButton.className = 'vis-configuration vis-config-button';
	        };
	        this.optionsContainer = document.createElement('div');
	        this.optionsContainer.className = 'vis-configuration vis-config-option-container';
	        this.domElements.push(this.optionsContainer);
	        this.domElements.push(generateButton);
	      }
	    }

	    
	  }, {
	    key: "_setupPopup",
	    value: function _setupPopup(string, index) {
	      var _this2 = this;
	      if (this.initialized === true && this.allowCreation === true && this.popupCounter < this.popupLimit) {
	        var div = document.createElement("div");
	        div.id = "vis-configuration-popup";
	        div.className = "vis-configuration-popup";
	        div.innerHTML = availableUtils.xss(string);
	        div.onclick = function () {
	          _this2._removePopup();
	        };
	        this.popupCounter += 1;
	        this.popupDiv = {
	          html: div,
	          index: index
	        };
	      }
	    }

	    
	  }, {
	    key: "_removePopup",
	    value: function _removePopup() {
	      if (this.popupDiv.html !== undefined) {
	        this.popupDiv.html.parentNode.removeChild(this.popupDiv.html);
	        clearTimeout(this.popupDiv.hideTimeout);
	        clearTimeout(this.popupDiv.deleteTimeout);
	        this.popupDiv = {};
	      }
	    }

	    
	  }, {
	    key: "_showPopupIfNeeded",
	    value: function _showPopupIfNeeded() {
	      var _this3 = this;
	      if (this.popupDiv.html !== undefined) {
	        var correspondingElement = this.domElements[this.popupDiv.index];
	        var rect = correspondingElement.getBoundingClientRect();
	        this.popupDiv.html.style.left = rect.left + "px";
	        this.popupDiv.html.style.top = rect.top - 30 + "px"; 
	        document.body.appendChild(this.popupDiv.html);
	        this.popupDiv.hideTimeout = _setTimeout(function () {
	          _this3.popupDiv.html.style.opacity = 0;
	        }, 1500);
	        this.popupDiv.deleteTimeout = _setTimeout(function () {
	          _this3._removePopup();
	        }, 1800);
	      }
	    }

	    
	  }, {
	    key: "_makeCheckbox",
	    value: function _makeCheckbox(defaultValue, value, path) {
	      var checkbox = document.createElement('input');
	      checkbox.type = 'checkbox';
	      checkbox.className = 'vis-configuration vis-config-checkbox';
	      checkbox.checked = defaultValue;
	      if (value !== undefined) {
	        checkbox.checked = value;
	        if (value !== defaultValue) {
	          if (_typeof(defaultValue) === 'object') {
	            if (value !== defaultValue.enabled) {
	              this.changedOptions.push({
	                path: path,
	                value: value
	              });
	            }
	          } else {
	            this.changedOptions.push({
	              path: path,
	              value: value
	            });
	          }
	        }
	      }
	      var me = this;
	      checkbox.onchange = function () {
	        me._update(this.checked, path);
	      };
	      var label = this._makeLabel(path[path.length - 1], path);
	      this._makeItem(path, label, checkbox);
	    }

	    
	  }, {
	    key: "_makeTextInput",
	    value: function _makeTextInput(defaultValue, value, path) {
	      var checkbox = document.createElement('input');
	      checkbox.type = 'text';
	      checkbox.className = 'vis-configuration vis-config-text';
	      checkbox.value = value;
	      if (value !== defaultValue) {
	        this.changedOptions.push({
	          path: path,
	          value: value
	        });
	      }
	      var me = this;
	      checkbox.onchange = function () {
	        me._update(this.value, path);
	      };
	      var label = this._makeLabel(path[path.length - 1], path);
	      this._makeItem(path, label, checkbox);
	    }

	    
	  }, {
	    key: "_makeColorField",
	    value: function _makeColorField(arr, value, path) {
	      var _this4 = this;
	      var defaultColor = arr[1];
	      var div = document.createElement('div');
	      value = value === undefined ? defaultColor : value;
	      if (value !== 'none') {
	        div.className = 'vis-configuration vis-config-colorBlock';
	        div.style.backgroundColor = value;
	      } else {
	        div.className = 'vis-configuration vis-config-colorBlock none';
	      }
	      value = value === undefined ? defaultColor : value;
	      div.onclick = function () {
	        _this4._showColorPicker(value, div, path);
	      };
	      var label = this._makeLabel(path[path.length - 1], path);
	      this._makeItem(path, label, div);
	    }

	    
	  }, {
	    key: "_showColorPicker",
	    value: function _showColorPicker(value, div, path) {
	      var _this5 = this;
	      
	      div.onclick = function () {};
	      this.colorPicker.insertTo(div);
	      this.colorPicker.show();
	      this.colorPicker.setColor(value);
	      this.colorPicker.setUpdateCallback(function (color) {
	        var colorString = 'rgba(' + color.r + ',' + color.g + ',' + color.b + ',' + color.a + ')';
	        div.style.backgroundColor = colorString;
	        _this5._update(colorString, path);
	      });

	      
	      this.colorPicker.setCloseCallback(function () {
	        div.onclick = function () {
	          _this5._showColorPicker(value, div, path);
	        };
	      });
	    }

	    
	  }, {
	    key: "_handleObject",
	    value: function _handleObject(obj) {
	      var path = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
	      var checkOnly = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : false;
	      var show = false;
	      var filter = _filterInstanceProperty(this.options);
	      var visibleInSet = false;
	      for (var subObj in obj) {
	        if (obj.hasOwnProperty(subObj)) {
	          show = true;
	          var item = obj[subObj];
	          var newPath = availableUtils.copyAndExtendArray(path, subObj);
	          if (typeof filter === 'function') {
	            show = filter(subObj, path);

	            
	            if (show === false) {
	              if (!_Array$isArray$1(item) && typeof item !== 'string' && typeof item !== 'boolean' && item instanceof Object) {
	                this.allowCreation = false;
	                show = this._handleObject(item, newPath, true);
	                this.allowCreation = checkOnly === false;
	              }
	            }
	          }
	          if (show !== false) {
	            visibleInSet = true;
	            var value = this._getValue(newPath);
	            if (_Array$isArray$1(item)) {
	              this._handleArray(item, value, newPath);
	            } else if (typeof item === 'string') {
	              this._makeTextInput(item, value, newPath);
	            } else if (typeof item === 'boolean') {
	              this._makeCheckbox(item, value, newPath);
	            } else if (item instanceof Object) {
	              
	              var draw = true;
	              if (_indexOfInstanceProperty(path).call(path, 'physics') !== -1) {
	                if (this.moduleOptions.physics.solver !== subObj) {
	                  draw = false;
	                }
	              }
	              if (draw === true) {
	                
	                if (item.enabled !== undefined) {
	                  var enabledPath = availableUtils.copyAndExtendArray(newPath, 'enabled');
	                  var enabledValue = this._getValue(enabledPath);
	                  if (enabledValue === true) {
	                    var label = this._makeLabel(subObj, newPath, true);
	                    this._makeItem(newPath, label);
	                    visibleInSet = this._handleObject(item, newPath) || visibleInSet;
	                  } else {
	                    this._makeCheckbox(item, enabledValue, newPath);
	                  }
	                } else {
	                  var _label = this._makeLabel(subObj, newPath, true);
	                  this._makeItem(newPath, _label);
	                  visibleInSet = this._handleObject(item, newPath) || visibleInSet;
	                }
	              }
	            } else {
	              console.error('dont know how to handle', item, subObj, newPath);
	            }
	          }
	        }
	      }
	      return visibleInSet;
	    }

	    
	  }, {
	    key: "_handleArray",
	    value: function _handleArray(arr, value, path) {
	      if (typeof arr[0] === 'string' && arr[0] === 'color') {
	        this._makeColorField(arr, value, path);
	        if (arr[1] !== value) {
	          this.changedOptions.push({
	            path: path,
	            value: value
	          });
	        }
	      } else if (typeof arr[0] === 'string') {
	        this._makeDropdown(arr, value, path);
	        if (arr[0] !== value) {
	          this.changedOptions.push({
	            path: path,
	            value: value
	          });
	        }
	      } else if (typeof arr[0] === 'number') {
	        this._makeRange(arr, value, path);
	        if (arr[0] !== value) {
	          this.changedOptions.push({
	            path: path,
	            value: Number(value)
	          });
	        }
	      }
	    }

	    
	  }, {
	    key: "_update",
	    value: function _update(value, path) {
	      var options = this._constructOptions(value, path);
	      if (this.parent.body && this.parent.body.emitter && this.parent.body.emitter.emit) {
	        this.parent.body.emitter.emit("configChange", options);
	      }
	      this.initialized = true;
	      this.parent.setOptions(options);
	    }

	    
	  }, {
	    key: "_constructOptions",
	    value: function _constructOptions(value, path) {
	      var optionsObj = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
	      var pointer = optionsObj;

	      
	      value = value === 'true' ? true : value;
	      value = value === 'false' ? false : value;
	      for (var i = 0; i < path.length; i++) {
	        if (path[i] !== 'global') {
	          if (pointer[path[i]] === undefined) {
	            pointer[path[i]] = {};
	          }
	          if (i !== path.length - 1) {
	            pointer = pointer[path[i]];
	          } else {
	            pointer[path[i]] = value;
	          }
	        }
	      }
	      return optionsObj;
	    }

	    
	  }, {
	    key: "_printOptions",
	    value: function _printOptions() {
	      var options = this.getOptions();
	      this.optionsContainer.innerHTML = '<pre>var options = ' + _JSON$stringify(options, null, 2) + '</pre>';
	    }

	    
	  }, {
	    key: "getOptions",
	    value: function getOptions() {
	      var options = {};
	      for (var i = 0; i < this.changedOptions.length; i++) {
	        this._constructOptions(this.changedOptions[i].value, this.changedOptions[i].path, options);
	      }
	      return options;
	    }
	  }]);
	  return Configurator;
	}();

	function _createSuper$1(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct$1(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct$1() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	
	var Timeline = function (_Core) {
	  _inherits(Timeline, _Core);
	  var _super = _createSuper$1(Timeline);
	  
	  function Timeline(container, items, groups, options) {
	    var _context2, _context3, _context4, _context5, _context6, _context7, _context8;
	    var _this;
	    _classCallCheck(this, Timeline);
	    _this = _super.call(this);
	    _this.initTime = new Date();
	    _this.itemsDone = false;
	    if (!(_assertThisInitialized$1(_this) instanceof Timeline)) {
	      throw new SyntaxError('Constructor must be called with the new operator');
	    }

	    
	    if (!(_Array$isArray$1(groups) || isDataViewLike(groups)) && groups instanceof Object) {
	      var forthArgument = options;
	      options = groups;
	      groups = forthArgument;
	    }

	    
	    
	    if (options && options.throttleRedraw) {
	      console.warn("Timeline option \"throttleRedraw\" is DEPRICATED and no longer supported. It will be removed in the next MAJOR release.");
	    }
	    var me = _assertThisInitialized$1(_this);
	    _this.defaultOptions = {
	      autoResize: true,
	      longSelectPressTime: 251,
	      orientation: {
	        axis: 'bottom',
	        
	        item: 'bottom' 
	      },

	      moment: moment$2
	    };
	    _this.options = availableUtils.deepExtend({}, _this.defaultOptions);
	    options && availableUtils.setupXSSProtection(options.xss);

	    
	    _this._create(container);
	    if (!options || options && typeof options.rtl == "undefined") {
	      _this.dom.root.style.visibility = 'hidden';
	      var directionFromDom;
	      var domNode = _this.dom.root;
	      while (!directionFromDom && domNode) {
	        directionFromDom = window.getComputedStyle(domNode, null).direction;
	        domNode = domNode.parentElement;
	      }
	      _this.options.rtl = directionFromDom && directionFromDom.toLowerCase() == "rtl";
	    } else {
	      _this.options.rtl = options.rtl;
	    }
	    if (options) {
	      if (options.rollingMode) {
	        _this.options.rollingMode = options.rollingMode;
	      }
	      if (options.onInitialDrawComplete) {
	        _this.options.onInitialDrawComplete = options.onInitialDrawComplete;
	      }
	      if (options.onTimeout) {
	        _this.options.onTimeout = options.onTimeout;
	      }
	      if (options.loadingScreenTemplate) {
	        _this.options.loadingScreenTemplate = options.loadingScreenTemplate;
	      }
	    }

	    
	    var loadingScreenFragment = document.createElement('div');
	    if (_this.options.loadingScreenTemplate) {
	      var _context;
	      var templateFunction = _bindInstanceProperty(_context = _this.options.loadingScreenTemplate).call(_context, _assertThisInitialized$1(_this));
	      var loadingScreen = templateFunction(_this.dom.loadingScreen);
	      if (loadingScreen instanceof Object && !(loadingScreen instanceof Element)) {
	        templateFunction(loadingScreenFragment);
	      } else {
	        if (loadingScreen instanceof Element) {
	          loadingScreenFragment.innerHTML = '';
	          loadingScreenFragment.appendChild(loadingScreen);
	        } else if (loadingScreen != undefined) {
	          loadingScreenFragment.innerHTML = availableUtils.xss(loadingScreen);
	        }
	      }
	    }
	    _this.dom.loadingScreen.appendChild(loadingScreenFragment);

	    
	    _this.components = [];
	    _this.body = {
	      dom: _this.dom,
	      domProps: _this.props,
	      emitter: {
	        on: _bindInstanceProperty(_context2 = _this.on).call(_context2, _assertThisInitialized$1(_this)),
	        off: _bindInstanceProperty(_context3 = _this.off).call(_context3, _assertThisInitialized$1(_this)),
	        emit: _bindInstanceProperty(_context4 = _this.emit).call(_context4, _assertThisInitialized$1(_this))
	      },
	      hiddenDates: [],
	      util: {
	        getScale: function getScale() {
	          return me.timeAxis.step.scale;
	        },
	        getStep: function getStep() {
	          return me.timeAxis.step.step;
	        },
	        toScreen: _bindInstanceProperty(_context5 = me._toScreen).call(_context5, me),
	        toGlobalScreen: _bindInstanceProperty(_context6 = me._toGlobalScreen).call(_context6, me),
	        
	        toTime: _bindInstanceProperty(_context7 = me._toTime).call(_context7, me),
	        toGlobalTime: _bindInstanceProperty(_context8 = me._toGlobalTime).call(_context8, me)
	      }
	    };

	    
	    _this.range = new Range(_this.body, _this.options);
	    _this.components.push(_this.range);
	    _this.body.range = _this.range;

	    
	    _this.timeAxis = new TimeAxis(_this.body, _this.options);
	    _this.timeAxis2 = null; 
	    _this.components.push(_this.timeAxis);

	    
	    _this.currentTime = new CurrentTime(_this.body, _this.options);
	    _this.components.push(_this.currentTime);

	    
	    _this.itemSet = new ItemSet(_this.body, _this.options);
	    _this.components.push(_this.itemSet);
	    _this.itemsData = null; 
	    _this.groupsData = null; 

	    function emit(eventName, event) {
	      if (!me.hasListeners(eventName)) {
	        return;
	      }
	      me.emit(eventName, me.getEventProperties(event));
	    }
	    _this.dom.root.onclick = function (event) {
	      emit('click', event);
	    };
	    _this.dom.root.ondblclick = function (event) {
	      emit('doubleClick', event);
	    };
	    _this.dom.root.oncontextmenu = function (event) {
	      emit('contextmenu', event);
	    };
	    _this.dom.root.onmouseover = function (event) {
	      emit('mouseOver', event);
	    };
	    if (window.PointerEvent) {
	      _this.dom.root.onpointerdown = function (event) {
	        emit('mouseDown', event);
	      };
	      _this.dom.root.onpointermove = function (event) {
	        emit('mouseMove', event);
	      };
	      _this.dom.root.onpointerup = function (event) {
	        emit('mouseUp', event);
	      };
	    } else {
	      _this.dom.root.onmousemove = function (event) {
	        emit('mouseMove', event);
	      };
	      _this.dom.root.onmousedown = function (event) {
	        emit('mouseDown', event);
	      };
	      _this.dom.root.onmouseup = function (event) {
	        emit('mouseUp', event);
	      };
	    }

	    
	    _this.initialFitDone = false;
	    _this.on('changed', function () {
	      if (me.itemsData == null) return;
	      if (!me.initialFitDone && !me.options.rollingMode) {
	        me.initialFitDone = true;
	        if (me.options.start != undefined || me.options.end != undefined) {
	          if (me.options.start == undefined || me.options.end == undefined) {
	            var range = me.getItemRange();
	          }
	          var start = me.options.start != undefined ? me.options.start : range.min;
	          var end = me.options.end != undefined ? me.options.end : range.max;
	          me.setWindow(start, end, {
	            animation: false
	          });
	        } else {
	          me.fit({
	            animation: false
	          });
	        }
	      }
	      if (!me.initialDrawDone && (me.initialRangeChangeDone || !me.options.start && !me.options.end || me.options.rollingMode)) {
	        me.initialDrawDone = true;
	        me.itemSet.initialDrawDone = true;
	        me.dom.root.style.visibility = 'visible';
	        me.dom.loadingScreen.parentNode.removeChild(me.dom.loadingScreen);
	        if (me.options.onInitialDrawComplete) {
	          _setTimeout(function () {
	            return me.options.onInitialDrawComplete();
	          }, 0);
	        }
	      }
	    });
	    _this.on('destroyTimeline', function () {
	      me.destroy();
	    });

	    
	    if (options) {
	      _this.setOptions(options);
	    }
	    _this.body.emitter.on('fit', function (args) {
	      _this._onFit(args);
	      _this.redraw();
	    });

	    
	    if (groups) {
	      _this.setGroups(groups);
	    }

	    
	    if (items) {
	      _this.setItems(items);
	    }

	    
	    _this._redraw();
	    return _this;
	  }

	  
	  _createClass(Timeline, [{
	    key: "_createConfigurator",
	    value: function _createConfigurator() {
	      return new Configurator(this, this.dom.container, configureOptions$1);
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw() {
	      this.itemSet && this.itemSet.markDirty({
	        refreshItems: true
	      });
	      this._redraw();
	    }

	    
	  }, {
	    key: "setOptions",
	    value: function setOptions(options) {
	      
	      var errorFound = Validator.validate(options, allOptions$1);
	      if (errorFound === true) {
	        console.log('%cErrors have been found in the supplied options object.', printStyle);
	      }
	      Core.prototype.setOptions.call(this, options);
	      if ('type' in options) {
	        if (options.type !== this.options.type) {
	          this.options.type = options.type;

	          
	          var itemsData = this.itemsData;
	          if (itemsData) {
	            var selection = this.getSelection();
	            this.setItems(null); 
	            this.setItems(itemsData.rawDS); 
	            this.setSelection(selection); 
	          }
	        }
	      }
	    }

	    
	  }, {
	    key: "setItems",
	    value: function setItems(items) {
	      this.itemsDone = false;

	      
	      var newDataSet;
	      if (!items) {
	        newDataSet = null;
	      } else if (isDataViewLike(items)) {
	        newDataSet = typeCoerceDataSet(items);
	      } else {
	        
	        newDataSet = typeCoerceDataSet(new esnext.DataSet(items));
	      }

	      
	      if (this.itemsData) {
	        
	        this.itemsData.dispose();
	      }
	      this.itemsData = newDataSet;
	      this.itemSet && this.itemSet.setItems(newDataSet != null ? newDataSet.rawDS : null);
	    }

	    
	  }, {
	    key: "setGroups",
	    value: function setGroups(groups) {
	      
	      var newDataSet;
	      var filter = function filter(group) {
	        return group.visible !== false;
	      };
	      if (!groups) {
	        newDataSet = null;
	      } else {
	        
	        if (_Array$isArray$1(groups)) groups = new esnext.DataSet(groups);
	        newDataSet = new esnext.DataView(groups, {
	          filter: filter
	        });
	      }

	      
	      
	      
	      
	      
	      
	      
	      
	      
	      
	      
	      if (this.groupsData != null && typeof this.groupsData.setData === "function") {
	        this.groupsData.setData(null);
	      }
	      this.groupsData = newDataSet;
	      this.itemSet.setGroups(newDataSet);
	    }

	    
	  }, {
	    key: "setData",
	    value: function setData(data) {
	      if (data && data.groups) {
	        this.setGroups(data.groups);
	      }
	      if (data && data.items) {
	        this.setItems(data.items);
	      }
	    }

	    
	  }, {
	    key: "setSelection",
	    value: function setSelection(ids, options) {
	      this.itemSet && this.itemSet.setSelection(ids);
	      if (options && options.focus) {
	        this.focus(ids, options);
	      }
	    }

	    
	  }, {
	    key: "getSelection",
	    value: function getSelection() {
	      return this.itemSet && this.itemSet.getSelection() || [];
	    }

	    
	  }, {
	    key: "focus",
	    value: function focus(id, options) {
	      if (!this.itemsData || id == undefined) return;
	      var ids = _Array$isArray$1(id) ? id : [id];

	      
	      var itemsData = this.itemsData.get(ids);

	      
	      var start = null;
	      var end = null;
	      _forEachInstanceProperty(itemsData).call(itemsData, function (itemData) {
	        var s = itemData.start.valueOf();
	        var e = 'end' in itemData ? itemData.end.valueOf() : itemData.start.valueOf();
	        if (start === null || s < start) {
	          start = s;
	        }
	        if (end === null || e > end) {
	          end = e;
	        }
	      });
	      if (start !== null && end !== null) {
	        var me = this;
	        
	        var item = this.itemSet.items[ids[0]];
	        var startPos = this._getScrollTop() * -1;
	        var initialVerticalScroll = null;

	        
	        var verticalAnimationFrame = function verticalAnimationFrame(ease, willDraw, done) {
	          var verticalScroll = getItemVerticalScroll(me, item);
	          if (verticalScroll === false) {
	            return; 
	          }

	          if (!initialVerticalScroll) {
	            initialVerticalScroll = verticalScroll;
	          }
	          if (initialVerticalScroll.itemTop == verticalScroll.itemTop && !initialVerticalScroll.shouldScroll) {
	            return; 
	          } else if (initialVerticalScroll.itemTop != verticalScroll.itemTop && verticalScroll.shouldScroll) {
	            
	            initialVerticalScroll = verticalScroll;
	            startPos = me._getScrollTop() * -1;
	          }
	          var from = startPos;
	          var to = initialVerticalScroll.scrollOffset;
	          var scrollTop = done ? to : from + (to - from) * ease;
	          me._setScrollTop(-scrollTop);
	          if (!willDraw) {
	            me._redraw();
	          }
	        };

	        
	        var setFinalVerticalPosition = function setFinalVerticalPosition() {
	          var finalVerticalScroll = getItemVerticalScroll(me, item);
	          if (finalVerticalScroll.shouldScroll && finalVerticalScroll.itemTop != initialVerticalScroll.itemTop) {
	            me._setScrollTop(-finalVerticalScroll.scrollOffset);
	            me._redraw();
	          }
	        };

	        
	        
	        var finalVerticalCallback = function finalVerticalCallback() {
	          
	          setFinalVerticalPosition();

	          
	          _setTimeout(setFinalVerticalPosition, 100);
	        };

	        
	        var zoom = options && options.zoom !== undefined ? options.zoom : true;
	        var middle = (start + end) / 2;
	        var interval = zoom ? (end - start) * 1.1 : Math.max(this.range.end - this.range.start, (end - start) * 1.1);
	        var animation = options && options.animation !== undefined ? options.animation : true;
	        if (!animation) {
	          
	          initialVerticalScroll = {
	            shouldScroll: false,
	            scrollOffset: -1,
	            itemTop: -1
	          };
	        }
	        this.range.setRange(middle - interval / 2, middle + interval / 2, {
	          animation: animation
	        }, finalVerticalCallback, verticalAnimationFrame);
	      }
	    }

	    
	  }, {
	    key: "fit",
	    value: function fit(options, callback) {
	      var animation = options && options.animation !== undefined ? options.animation : true;
	      var range;
	      if (this.itemsData.length === 1 && this.itemsData.get()[0].end === undefined) {
	        
	        range = this.getDataRange();
	        this.moveTo(range.min.valueOf(), {
	          animation: animation
	        }, callback);
	      } else {
	        
	        range = this.getItemRange();
	        this.range.setRange(range.min, range.max, {
	          animation: animation
	        }, callback);
	      }
	    }

	    
	  }, {
	    key: "getItemRange",
	    value: function getItemRange() {
	      var _this2 = this;
	      
	      var range = this.getDataRange();
	      var min = range.min !== null ? range.min.valueOf() : null;
	      var max = range.max !== null ? range.max.valueOf() : null;
	      var minItem = null;
	      var maxItem = null;
	      if (min != null && max != null) {
	        var interval = max - min; 
	        if (interval <= 0) {
	          interval = 10;
	        }
	        var factor = interval / this.props.center.width;
	        var redrawQueue = {};
	        var redrawQueueLength = 0;

	        
	        _forEachInstanceProperty(availableUtils).call(availableUtils, this.itemSet.items, function (item, key) {
	          if (item.groupShowing) {
	            var returnQueue = true;
	            redrawQueue[key] = item.redraw(returnQueue);
	            redrawQueueLength = redrawQueue[key].length;
	          }
	        });
	        var needRedraw = redrawQueueLength > 0;
	        if (needRedraw) {
	          var _loop = function _loop(i) {
	            _forEachInstanceProperty(availableUtils).call(availableUtils, redrawQueue, function (fns) {
	              fns[i]();
	            });
	          };
	          
	          for (var i = 0; i < redrawQueueLength; i++) {
	            _loop(i);
	          }
	        }

	        
	        _forEachInstanceProperty(availableUtils).call(availableUtils, this.itemSet.items, function (item) {
	          var start = getStart(item);
	          var end = getEnd(item);
	          var startSide;
	          var endSide;
	          if (_this2.options.rtl) {
	            startSide = start - (item.getWidthRight() + 10) * factor;
	            endSide = end + (item.getWidthLeft() + 10) * factor;
	          } else {
	            startSide = start - (item.getWidthLeft() + 10) * factor;
	            endSide = end + (item.getWidthRight() + 10) * factor;
	          }
	          if (startSide < min) {
	            min = startSide;
	            minItem = item;
	          }
	          if (endSide > max) {
	            max = endSide;
	            maxItem = item;
	          }
	        });
	        if (minItem && maxItem) {
	          var lhs = minItem.getWidthLeft() + 10;
	          var rhs = maxItem.getWidthRight() + 10;
	          var delta = this.props.center.width - lhs - rhs; 

	          if (delta > 0) {
	            if (this.options.rtl) {
	              min = getStart(minItem) - rhs * interval / delta; 
	              max = getEnd(maxItem) + lhs * interval / delta; 
	            } else {
	              min = getStart(minItem) - lhs * interval / delta; 
	              max = getEnd(maxItem) + rhs * interval / delta; 
	            }
	          }
	        }
	      }

	      return {
	        min: min != null ? new Date(min) : null,
	        max: max != null ? new Date(max) : null
	      };
	    }

	    
	  }, {
	    key: "getDataRange",
	    value: function getDataRange() {
	      var min = null;
	      var max = null;
	      if (this.itemsData) {
	        var _context9;
	        _forEachInstanceProperty(_context9 = this.itemsData).call(_context9, function (item) {
	          var start = availableUtils.convert(item.start, 'Date').valueOf();
	          var end = availableUtils.convert(item.end != undefined ? item.end : item.start, 'Date').valueOf();
	          if (min === null || start < min) {
	            min = start;
	          }
	          if (max === null || end > max) {
	            max = end;
	          }
	        });
	      }
	      return {
	        min: min != null ? new Date(min) : null,
	        max: max != null ? new Date(max) : null
	      };
	    }

	    
	  }, {
	    key: "getEventProperties",
	    value: function getEventProperties(event) {
	      var clientX = event.center ? event.center.x : event.clientX;
	      var clientY = event.center ? event.center.y : event.clientY;
	      var centerContainerRect = this.dom.centerContainer.getBoundingClientRect();
	      var x = this.options.rtl ? centerContainerRect.right - clientX : clientX - centerContainerRect.left;
	      var y = clientY - centerContainerRect.top;
	      var item = this.itemSet.itemFromTarget(event);
	      var group = this.itemSet.groupFromTarget(event);
	      var customTime = CustomTime.customTimeFromTarget(event);
	      var snap = this.itemSet.options.snap || null;
	      var scale = this.body.util.getScale();
	      var step = this.body.util.getStep();
	      var time = this._toTime(x);
	      var snappedTime = snap ? snap(time, scale, step) : time;
	      var element = availableUtils.getTarget(event);
	      var what = null;
	      if (item != null) {
	        what = 'item';
	      } else if (customTime != null) {
	        what = 'custom-time';
	      } else if (availableUtils.hasParent(element, this.timeAxis.dom.foreground)) {
	        what = 'axis';
	      } else if (this.timeAxis2 && availableUtils.hasParent(element, this.timeAxis2.dom.foreground)) {
	        what = 'axis';
	      } else if (availableUtils.hasParent(element, this.itemSet.dom.labelSet)) {
	        what = 'group-label';
	      } else if (availableUtils.hasParent(element, this.currentTime.bar)) {
	        what = 'current-time';
	      } else if (availableUtils.hasParent(element, this.dom.center)) {
	        what = 'background';
	      }
	      return {
	        event: event,
	        item: item ? item.id : null,
	        isCluster: item ? !!item.isCluster : false,
	        items: item ? item.items || [] : null,
	        group: group ? group.groupId : null,
	        customTime: customTime ? customTime.options.id : null,
	        what: what,
	        pageX: event.srcEvent ? event.srcEvent.pageX : event.pageX,
	        pageY: event.srcEvent ? event.srcEvent.pageY : event.pageY,
	        x: x,
	        y: y,
	        time: time,
	        snappedTime: snappedTime
	      };
	    }

	    
	  }, {
	    key: "toggleRollingMode",
	    value: function toggleRollingMode() {
	      if (this.range.rolling) {
	        this.range.stopRolling();
	      } else {
	        if (this.options.rollingMode == undefined) {
	          this.setOptions(this.options);
	        }
	        this.range.startRolling();
	      }
	    }

	    
	  }, {
	    key: "_redraw",
	    value: function _redraw() {
	      Core.prototype._redraw.call(this);
	    }

	    
	  }, {
	    key: "_onFit",
	    value: function _onFit(args) {
	      var start = args.start,
	        end = args.end,
	        animation = args.animation;
	      if (!end) {
	        this.moveTo(start.valueOf(), {
	          animation: animation
	        });
	      } else {
	        this.range.setRange(start, end, {
	          animation: animation
	        });
	      }
	    }
	  }]);
	  return Timeline;
	}(Core);
	function getStart(item) {
	  return availableUtils.convert(item.data.start, 'Date').valueOf();
	}

	
	function getEnd(item) {
	  var end = item.data.end != undefined ? item.data.end : item.data.start;
	  return availableUtils.convert(end, 'Date').valueOf();
	}

	
	function getItemVerticalScroll(timeline, item) {
	  if (!item.parent) {
	    
	    return false;
	  }
	  var itemsetHeight = timeline.options.rtl ? timeline.props.rightContainer.height : timeline.props.leftContainer.height;
	  var contentHeight = timeline.props.center.height;
	  var group = item.parent;
	  var offset = group.top;
	  var shouldScroll = true;
	  var orientation = timeline.timeAxis.options.orientation.axis;
	  var itemTop = function itemTop() {
	    if (orientation == "bottom") {
	      return group.height - item.top - item.height;
	    } else {
	      return item.top;
	    }
	  };
	  var currentScrollHeight = timeline._getScrollTop() * -1;
	  var targetOffset = offset + itemTop();
	  var height = item.height;
	  if (targetOffset < currentScrollHeight) {
	    if (offset + itemsetHeight <= offset + itemTop() + height) {
	      offset += itemTop() - timeline.itemSet.options.margin.item.vertical;
	    }
	  } else if (targetOffset + height > currentScrollHeight + itemsetHeight) {
	    offset += itemTop() + height - itemsetHeight + timeline.itemSet.options.margin.item.vertical;
	  } else {
	    shouldScroll = false;
	  }
	  offset = Math.min(offset, contentHeight - itemsetHeight);
	  return {
	    shouldScroll: shouldScroll,
	    scrollOffset: offset,
	    itemTop: targetOffset
	  };
	}

	

	
	function prepareElements(JSONcontainer) {
	  
	  for (var elementType in JSONcontainer) {
	    if (JSONcontainer.hasOwnProperty(elementType)) {
	      JSONcontainer[elementType].redundant = JSONcontainer[elementType].used;
	      JSONcontainer[elementType].used = [];
	    }
	  }
	}

	
	function cleanupElements(JSONcontainer) {
	  
	  for (var elementType in JSONcontainer) {
	    if (JSONcontainer.hasOwnProperty(elementType)) {
	      if (JSONcontainer[elementType].redundant) {
	        for (var i = 0; i < JSONcontainer[elementType].redundant.length; i++) {
	          JSONcontainer[elementType].redundant[i].parentNode.removeChild(JSONcontainer[elementType].redundant[i]);
	        }
	        JSONcontainer[elementType].redundant = [];
	      }
	    }
	  }
	}

	
	function resetElements(JSONcontainer) {
	  prepareElements(JSONcontainer);
	  cleanupElements(JSONcontainer);
	  prepareElements(JSONcontainer);
	}

	
	function getSVGElement(elementType, JSONcontainer, svgContainer) {
	  var element;
	  
	  if (JSONcontainer.hasOwnProperty(elementType)) {
	    
	    
	    if (JSONcontainer[elementType].redundant.length > 0) {
	      element = JSONcontainer[elementType].redundant[0];
	      JSONcontainer[elementType].redundant.shift();
	    } else {
	      
	      element = document.createElementNS('http:
	      svgContainer.appendChild(element);
	    }
	  } else {
	    
	    element = document.createElementNS('http:
	    JSONcontainer[elementType] = {
	      used: [],
	      redundant: []
	    };
	    svgContainer.appendChild(element);
	  }
	  JSONcontainer[elementType].used.push(element);
	  return element;
	}

	
	function getDOMElement(elementType, JSONcontainer, DOMContainer, insertBefore) {
	  var element;
	  
	  if (JSONcontainer.hasOwnProperty(elementType)) {
	    
	    
	    if (JSONcontainer[elementType].redundant.length > 0) {
	      element = JSONcontainer[elementType].redundant[0];
	      JSONcontainer[elementType].redundant.shift();
	    } else {
	      
	      element = document.createElement(elementType);
	      if (insertBefore !== undefined) {
	        DOMContainer.insertBefore(element, insertBefore);
	      } else {
	        DOMContainer.appendChild(element);
	      }
	    }
	  } else {
	    
	    element = document.createElement(elementType);
	    JSONcontainer[elementType] = {
	      used: [],
	      redundant: []
	    };
	    if (insertBefore !== undefined) {
	      DOMContainer.insertBefore(element, insertBefore);
	    } else {
	      DOMContainer.appendChild(element);
	    }
	  }
	  JSONcontainer[elementType].used.push(element);
	  return element;
	}

	
	function drawPoint(x, y, groupTemplate, JSONcontainer, svgContainer, labelObj) {
	  var point;
	  if (groupTemplate.style == 'circle') {
	    point = getSVGElement('circle', JSONcontainer, svgContainer);
	    point.setAttributeNS(null, "cx", x);
	    point.setAttributeNS(null, "cy", y);
	    point.setAttributeNS(null, "r", 0.5 * groupTemplate.size);
	  } else {
	    point = getSVGElement('rect', JSONcontainer, svgContainer);
	    point.setAttributeNS(null, "x", x - 0.5 * groupTemplate.size);
	    point.setAttributeNS(null, "y", y - 0.5 * groupTemplate.size);
	    point.setAttributeNS(null, "width", groupTemplate.size);
	    point.setAttributeNS(null, "height", groupTemplate.size);
	  }
	  if (groupTemplate.styles !== undefined) {
	    point.setAttributeNS(null, "style", groupTemplate.styles);
	  }
	  point.setAttributeNS(null, "class", groupTemplate.className + " vis-point");
	  

	  if (labelObj) {
	    var label = getSVGElement('text', JSONcontainer, svgContainer);
	    if (labelObj.xOffset) {
	      x = x + labelObj.xOffset;
	    }
	    if (labelObj.yOffset) {
	      y = y + labelObj.yOffset;
	    }
	    if (labelObj.content) {
	      label.textContent = labelObj.content;
	    }
	    if (labelObj.className) {
	      label.setAttributeNS(null, "class", labelObj.className + " vis-label");
	    }
	    label.setAttributeNS(null, "x", x);
	    label.setAttributeNS(null, "y", y);
	  }
	  return point;
	}

	
	function drawBar(x, y, width, height, className, JSONcontainer, svgContainer, style) {
	  if (height != 0) {
	    if (height < 0) {
	      height *= -1;
	      y -= height;
	    }
	    var rect = getSVGElement('rect', JSONcontainer, svgContainer);
	    rect.setAttributeNS(null, "x", x - 0.5 * width);
	    rect.setAttributeNS(null, "y", y);
	    rect.setAttributeNS(null, "width", width);
	    rect.setAttributeNS(null, "height", height);
	    rect.setAttributeNS(null, "class", className);
	    if (style) {
	      rect.setAttributeNS(null, "style", style);
	    }
	  }
	}

	
	function getNavigatorLanguage() {
	  try {
	    if (!navigator) return 'en';
	    if (navigator.languages && navigator.languages.length) {
	      return navigator.languages;
	    } else {
	      return navigator.userLanguage || navigator.language || navigator.browserLanguage || 'en';
	    }
	  } catch (error) {
	    return 'en';
	  }
	}

	
	var DataScale = function () {
	  
	  function DataScale(start, end, autoScaleStart, autoScaleEnd, containerHeight, majorCharHeight) {
	    var zeroAlign = arguments.length > 6 && arguments[6] !== undefined ? arguments[6] : false;
	    var formattingFunction = arguments.length > 7 && arguments[7] !== undefined ? arguments[7] : false;
	    _classCallCheck(this, DataScale);
	    this.majorSteps = [1, 2, 5, 10];
	    this.minorSteps = [0.25, 0.5, 1, 2];
	    this.customLines = null;
	    this.containerHeight = containerHeight;
	    this.majorCharHeight = majorCharHeight;
	    this._start = start;
	    this._end = end;
	    this.scale = 1;
	    this.minorStepIdx = -1;
	    this.magnitudefactor = 1;
	    this.determineScale();
	    this.zeroAlign = zeroAlign;
	    this.autoScaleStart = autoScaleStart;
	    this.autoScaleEnd = autoScaleEnd;
	    this.formattingFunction = formattingFunction;
	    if (autoScaleStart || autoScaleEnd) {
	      var me = this;
	      var roundToMinor = function roundToMinor(value) {
	        var rounded = value - value % (me.magnitudefactor * me.minorSteps[me.minorStepIdx]);
	        if (value % (me.magnitudefactor * me.minorSteps[me.minorStepIdx]) > 0.5 * (me.magnitudefactor * me.minorSteps[me.minorStepIdx])) {
	          return rounded + me.magnitudefactor * me.minorSteps[me.minorStepIdx];
	        } else {
	          return rounded;
	        }
	      };
	      if (autoScaleStart) {
	        this._start -= this.magnitudefactor * 2 * this.minorSteps[this.minorStepIdx];
	        this._start = roundToMinor(this._start);
	      }
	      if (autoScaleEnd) {
	        this._end += this.magnitudefactor * this.minorSteps[this.minorStepIdx];
	        this._end = roundToMinor(this._end);
	      }
	      this.determineScale();
	    }
	  }

	  
	  _createClass(DataScale, [{
	    key: "setCharHeight",
	    value: function setCharHeight(majorCharHeight) {
	      this.majorCharHeight = majorCharHeight;
	    }

	    
	  }, {
	    key: "setHeight",
	    value: function setHeight(containerHeight) {
	      this.containerHeight = containerHeight;
	    }

	    
	  }, {
	    key: "determineScale",
	    value: function determineScale() {
	      var range = this._end - this._start;
	      this.scale = this.containerHeight / range;
	      var minimumStepValue = this.majorCharHeight / this.scale;
	      var orderOfMagnitude = range > 0 ? Math.round(Math.log(range) / Math.LN10) : 0;
	      this.minorStepIdx = -1;
	      this.magnitudefactor = Math.pow(10, orderOfMagnitude);
	      var start = 0;
	      if (orderOfMagnitude < 0) {
	        start = orderOfMagnitude;
	      }
	      var solutionFound = false;
	      for (var l = start; Math.abs(l) <= Math.abs(orderOfMagnitude); l++) {
	        this.magnitudefactor = Math.pow(10, l);
	        for (var j = 0; j < this.minorSteps.length; j++) {
	          var stepSize = this.magnitudefactor * this.minorSteps[j];
	          if (stepSize >= minimumStepValue) {
	            solutionFound = true;
	            this.minorStepIdx = j;
	            break;
	          }
	        }
	        if (solutionFound === true) {
	          break;
	        }
	      }
	    }

	    
	  }, {
	    key: "is_major",
	    value: function is_major(value) {
	      return value % (this.magnitudefactor * this.majorSteps[this.minorStepIdx]) === 0;
	    }

	    
	  }, {
	    key: "getStep",
	    value: function getStep() {
	      return this.magnitudefactor * this.minorSteps[this.minorStepIdx];
	    }

	    
	  }, {
	    key: "getFirstMajor",
	    value: function getFirstMajor() {
	      var majorStep = this.magnitudefactor * this.majorSteps[this.minorStepIdx];
	      return this.convertValue(this._start + (majorStep - this._start % majorStep) % majorStep);
	    }

	    
	  }, {
	    key: "formatValue",
	    value: function formatValue(current) {
	      var returnValue = current.toPrecision(5);
	      if (typeof this.formattingFunction === 'function') {
	        returnValue = this.formattingFunction(current);
	      }
	      if (typeof returnValue === 'number') {
	        return "".concat(returnValue);
	      } else if (typeof returnValue === 'string') {
	        return returnValue;
	      } else {
	        return current.toPrecision(5);
	      }
	    }

	    
	  }, {
	    key: "getLines",
	    value: function getLines() {
	      var lines = [];
	      var step = this.getStep();
	      var bottomOffset = (step - this._start % step) % step;
	      for (var i = this._start + bottomOffset; this._end - i > 0.00001; i += step) {
	        if (i != this._start) {
	          
	          lines.push({
	            major: this.is_major(i),
	            y: this.convertValue(i),
	            val: this.formatValue(i)
	          });
	        }
	      }
	      return lines;
	    }

	    
	  }, {
	    key: "followScale",
	    value: function followScale(other) {
	      var oldStepIdx = this.minorStepIdx;
	      var oldStart = this._start;
	      var oldEnd = this._end;
	      var me = this;
	      var increaseMagnitude = function increaseMagnitude() {
	        me.magnitudefactor *= 2;
	      };
	      var decreaseMagnitude = function decreaseMagnitude() {
	        me.magnitudefactor /= 2;
	      };
	      if (other.minorStepIdx <= 1 && this.minorStepIdx <= 1 || other.minorStepIdx > 1 && this.minorStepIdx > 1) ; else if (other.minorStepIdx < this.minorStepIdx) {
	        
	        this.minorStepIdx = 1;
	        if (oldStepIdx == 2) {
	          increaseMagnitude();
	        } else {
	          increaseMagnitude();
	          increaseMagnitude();
	        }
	      } else {
	        
	        this.minorStepIdx = 2;
	        if (oldStepIdx == 1) {
	          decreaseMagnitude();
	        } else {
	          decreaseMagnitude();
	          decreaseMagnitude();
	        }
	      }

	      
	      var otherZero = other.convertValue(0);
	      var otherStep = other.getStep() * other.scale;
	      var done = false;
	      var count = 0;
	      
	      while (!done && count++ < 5) {
	        
	        this.scale = otherStep / (this.minorSteps[this.minorStepIdx] * this.magnitudefactor);
	        var newRange = this.containerHeight / this.scale;

	        
	        this._start = oldStart;
	        this._end = this._start + newRange;
	        var myOriginalZero = this._end * this.scale;
	        var majorStep = this.magnitudefactor * this.majorSteps[this.minorStepIdx];
	        var majorOffset = this.getFirstMajor() - other.getFirstMajor();
	        if (this.zeroAlign) {
	          var zeroOffset = otherZero - myOriginalZero;
	          this._end += zeroOffset / this.scale;
	          this._start = this._end - newRange;
	        } else {
	          if (!this.autoScaleStart) {
	            this._start += majorStep - majorOffset / this.scale;
	            this._end = this._start + newRange;
	          } else {
	            this._start -= majorOffset / this.scale;
	            this._end = this._start + newRange;
	          }
	        }
	        if (!this.autoScaleEnd && this._end > oldEnd + 0.00001) {
	          
	          decreaseMagnitude();
	          done = false;
	          continue;
	        }
	        if (!this.autoScaleStart && this._start < oldStart - 0.00001) {
	          if (this.zeroAlign && oldStart >= 0) {
	            console.warn("Can't adhere to given 'min' range, due to zeroalign");
	          } else {
	            
	            decreaseMagnitude();
	            done = false;
	            continue;
	          }
	        }
	        if (this.autoScaleStart && this.autoScaleEnd && newRange < oldEnd - oldStart) {
	          increaseMagnitude();
	          done = false;
	          continue;
	        }
	        done = true;
	      }
	    }

	    
	  }, {
	    key: "convertValue",
	    value: function convertValue(value) {
	      return this.containerHeight - (value - this._start) * this.scale;
	    }

	    
	  }, {
	    key: "screenToValue",
	    value: function screenToValue(pixels) {
	      return (this.containerHeight - pixels) / this.scale + this._start;
	    }
	  }]);
	  return DataScale;
	}();

	function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof _Symbol !== "undefined" && _getIteratorMethod(o) || o["@@iterator"]; if (!it) { if (_Array$isArray$1(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }
	function _unsupportedIterableToArray(o, minLen) { var _context; if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = _sliceInstanceProperty(_context = Object.prototype.toString.call(o)).call(_context, 8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return _Array$from$1(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }
	function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) arr2[i] = arr[i]; return arr2; }
	function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = _Reflect$construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }
	function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !_Reflect$construct) return false; if (_Reflect$construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(_Reflect$construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

	
	var DataAxis = function (_Component) {
	  _inherits(DataAxis, _Component);
	  var _super = _createSuper(DataAxis);
	  
	  function DataAxis(body, options, svg, linegraphOptions) {
	    var _this;
	    _classCallCheck(this, DataAxis);
	    _this = _super.call(this);
	    _this.id = v4();
	    _this.body = body;
	    _this.defaultOptions = {
	      orientation: 'left',
	      
	      showMinorLabels: true,
	      showMajorLabels: true,
	      showWeekScale: false,
	      icons: false,
	      majorLinesOffset: 7,
	      minorLinesOffset: 4,
	      labelOffsetX: 10,
	      labelOffsetY: 2,
	      iconWidth: 20,
	      width: '40px',
	      visible: true,
	      alignZeros: true,
	      left: {
	        range: {
	          min: undefined,
	          max: undefined
	        },
	        format: function format(value) {
	          return "".concat(_parseFloat(value.toPrecision(3)));
	        },
	        title: {
	          text: undefined,
	          style: undefined
	        }
	      },
	      right: {
	        range: {
	          min: undefined,
	          max: undefined
	        },
	        format: function format(value) {
	          return "".concat(_parseFloat(value.toPrecision(3)));
	        },
	        title: {
	          text: undefined,
	          style: undefined
	        }
	      }
	    };
	    _this.linegraphOptions = linegraphOptions;
	    _this.linegraphSVG = svg;
	    _this.props = {};
	    _this.DOMelements = {
	      
	      lines: {},
	      labels: {},
	      title: {}
	    };
	    _this.dom = {};
	    _this.scale = undefined;
	    _this.range = {
	      start: 0,
	      end: 0
	    };
	    _this.options = availableUtils.extend({}, _this.defaultOptions);
	    _this.conversionFactor = 1;
	    _this.setOptions(options);
	    _this.width = Number("".concat(_this.options.width).replace("px", ""));
	    _this.minWidth = _this.width;
	    _this.height = _this.linegraphSVG.getBoundingClientRect().height;
	    _this.hidden = false;
	    _this.stepPixels = 25;
	    _this.zeroCrossing = -1;
	    _this.amountOfSteps = -1;
	    _this.lineOffset = 0;
	    _this.master = true;
	    _this.masterAxis = null;
	    _this.svgElements = {};
	    _this.iconsRemoved = false;
	    _this.groups = {};
	    _this.amountOfGroups = 0;

	    
	    _this._create();
	    if (_this.scale == undefined) {
	      _this._redrawLabels();
	    }
	    _this.framework = {
	      svg: _this.svg,
	      svgElements: _this.svgElements,
	      options: _this.options,
	      groups: _this.groups
	    };
	    var me = _assertThisInitialized$1(_this);
	    _this.body.emitter.on("verticalDrag", function () {
	      me.dom.lineContainer.style.top = "".concat(me.body.domProps.scrollTop, "px");
	    });
	    return _this;
	  }

	  
	  _createClass(DataAxis, [{
	    key: "addGroup",
	    value: function addGroup(label, graphOptions) {
	      if (!this.groups.hasOwnProperty(label)) {
	        this.groups[label] = graphOptions;
	      }
	      this.amountOfGroups += 1;
	    }

	    
	  }, {
	    key: "updateGroup",
	    value: function updateGroup(label, graphOptions) {
	      if (!this.groups.hasOwnProperty(label)) {
	        this.amountOfGroups += 1;
	      }
	      this.groups[label] = graphOptions;
	    }

	    
	  }, {
	    key: "removeGroup",
	    value: function removeGroup(label) {
	      if (this.groups.hasOwnProperty(label)) {
	        delete this.groups[label];
	        this.amountOfGroups -= 1;
	      }
	    }

	    
	  }, {
	    key: "setOptions",
	    value: function setOptions(options) {
	      if (options) {
	        var redraw = false;
	        if (this.options.orientation != options.orientation && options.orientation !== undefined) {
	          redraw = true;
	        }
	        var fields = ['orientation', 'showMinorLabels', 'showMajorLabels', 'icons', 'majorLinesOffset', 'minorLinesOffset', 'labelOffsetX', 'labelOffsetY', 'iconWidth', 'width', 'visible', 'left', 'right', 'alignZeros'];
	        availableUtils.selectiveDeepExtend(fields, this.options, options);
	        this.minWidth = Number("".concat(this.options.width).replace("px", ""));
	        if (redraw === true && this.dom.frame) {
	          this.hide();
	          this.show();
	        }
	      }
	    }

	    
	  }, {
	    key: "_create",
	    value: function _create() {
	      this.dom.frame = document.createElement('div');
	      this.dom.frame.style.width = this.options.width;
	      this.dom.frame.style.height = this.height;
	      this.dom.lineContainer = document.createElement('div');
	      this.dom.lineContainer.style.width = '100%';
	      this.dom.lineContainer.style.height = this.height;
	      this.dom.lineContainer.style.position = 'relative';
	      this.dom.lineContainer.style.visibility = 'visible';
	      this.dom.lineContainer.style.display = 'block';

	      
	      this.svg = document.createElementNS('http:
	      this.svg.style.position = "absolute";
	      this.svg.style.top = '0px';
	      this.svg.style.height = '100%';
	      this.svg.style.width = '100%';
	      this.svg.style.display = "block";
	      this.dom.frame.appendChild(this.svg);
	    }

	    
	  }, {
	    key: "_redrawGroupIcons",
	    value: function _redrawGroupIcons() {
	      prepareElements(this.svgElements);
	      var x;
	      var iconWidth = this.options.iconWidth;
	      var iconHeight = 15;
	      var iconOffset = 4;
	      var y = iconOffset + 0.5 * iconHeight;
	      if (this.options.orientation === 'left') {
	        x = iconOffset;
	      } else {
	        x = this.width - iconWidth - iconOffset;
	      }
	      var groupArray = _Object$keys(this.groups);
	      _sortInstanceProperty(groupArray).call(groupArray, function (a, b) {
	        return a < b ? -1 : 1;
	      });
	      var _iterator = _createForOfIteratorHelper(groupArray),
	        _step;
	      try {
	        for (_iterator.s(); !(_step = _iterator.n()).done;) {
	          var groupId = _step.value;
	          if (this.groups[groupId].visible === true && (this.linegraphOptions.visibility[groupId] === undefined || this.linegraphOptions.visibility[groupId] === true)) {
	            this.groups[groupId].getLegend(iconWidth, iconHeight, this.framework, x, y);
	            y += iconHeight + iconOffset;
	          }
	        }
	      } catch (err) {
	        _iterator.e(err);
	      } finally {
	        _iterator.f();
	      }
	      cleanupElements(this.svgElements);
	      this.iconsRemoved = false;
	    }

	    
	  }, {
	    key: "_cleanupIcons",
	    value: function _cleanupIcons() {
	      if (this.iconsRemoved === false) {
	        prepareElements(this.svgElements);
	        cleanupElements(this.svgElements);
	        this.iconsRemoved = true;
	      }
	    }

	    
	  }, {
	    key: "show",
	    value: function show() {
	      this.hidden = false;
	      if (!this.dom.frame.parentNode) {
	        if (this.options.orientation === 'left') {
	          this.body.dom.left.appendChild(this.dom.frame);
	        } else {
	          this.body.dom.right.appendChild(this.dom.frame);
	        }
	      }
	      if (!this.dom.lineContainer.parentNode) {
	        this.body.dom.backgroundHorizontal.appendChild(this.dom.lineContainer);
	      }
	      this.dom.lineContainer.style.display = 'block';
	    }

	    
	  }, {
	    key: "hide",
	    value: function hide() {
	      this.hidden = true;
	      if (this.dom.frame.parentNode) {
	        this.dom.frame.parentNode.removeChild(this.dom.frame);
	      }
	      this.dom.lineContainer.style.display = 'none';
	    }

	    
	  }, {
	    key: "setRange",
	    value: function setRange(start, end) {
	      this.range.start = start;
	      this.range.end = end;
	    }

	    
	  }, {
	    key: "redraw",
	    value: function redraw() {
	      var resized = false;
	      var activeGroups = 0;

	      
	      this.dom.lineContainer.style.top = "".concat(this.body.domProps.scrollTop, "px");
	      for (var groupId in this.groups) {
	        if (this.groups.hasOwnProperty(groupId)) {
	          if (this.groups[groupId].visible === true && (this.linegraphOptions.visibility[groupId] === undefined || this.linegraphOptions.visibility[groupId] === true)) {
	            activeGroups++;
	          }
	        }
	      }
	      if (this.amountOfGroups === 0 || activeGroups === 0) {
	        this.hide();
	      } else {
	        this.show();
	        this.height = Number(this.linegraphSVG.style.height.replace("px", ""));

	        
	        this.dom.lineContainer.style.height = "".concat(this.height, "px");
	        this.width = this.options.visible === true ? Number("".concat(this.options.width).replace("px", "")) : 0;
	        var props = this.props;
	        var frame = this.dom.frame;

	        
	        frame.className = 'vis-data-axis';

	        
	        this._calculateCharSize();
	        var orientation = this.options.orientation;
	        var showMinorLabels = this.options.showMinorLabels;
	        var showMajorLabels = this.options.showMajorLabels;
	        var backgroundHorizontalOffsetWidth = this.body.dom.backgroundHorizontal.offsetWidth;

	        
	        props.minorLabelHeight = showMinorLabels ? props.minorCharHeight : 0;
	        props.majorLabelHeight = showMajorLabels ? props.majorCharHeight : 0;
	        props.minorLineWidth = backgroundHorizontalOffsetWidth - this.lineOffset - this.width + 2 * this.options.minorLinesOffset;
	        props.minorLineHeight = 1;
	        props.majorLineWidth = backgroundHorizontalOffsetWidth - this.lineOffset - this.width + 2 * this.options.majorLinesOffset;
	        props.majorLineHeight = 1;

	        
	        if (orientation === 'left') {
	          frame.style.top = '0';
	          frame.style.left = '0';
	          frame.style.bottom = '';
	          frame.style.width = "".concat(this.width, "px");
	          frame.style.height = "".concat(this.height, "px");
	          this.props.width = this.body.domProps.left.width;
	          this.props.height = this.body.domProps.left.height;
	        } else {
	          
	          frame.style.top = '';
	          frame.style.bottom = '0';
	          frame.style.left = '0';
	          frame.style.width = "".concat(this.width, "px");
	          frame.style.height = "".concat(this.height, "px");
	          this.props.width = this.body.domProps.right.width;
	          this.props.height = this.body.domProps.right.height;
	        }
	        resized = this._redrawLabels();
	        resized = this._isResized() || resized;
	        if (this.options.icons === true) {
	          this._redrawGroupIcons();
	        } else {
	          this._cleanupIcons();
	        }
	        this._redrawTitle(orientation);
	      }
	      return resized;
	    }

	    
	  }, {
	    key: "_redrawLabels",
	    value: function _redrawLabels() {
	      var _this2 = this;
	      var resized = false;
	      prepareElements(this.DOMelements.lines);
	      prepareElements(this.DOMelements.labels);
	      var orientation = this.options['orientation'];
	      var customRange = this.options[orientation].range != undefined ? this.options[orientation].range : {};

	      
	      var autoScaleEnd = true;
	      if (customRange.max != undefined) {
	        this.range.end = customRange.max;
	        autoScaleEnd = false;
	      }
	      var autoScaleStart = true;
	      if (customRange.min != undefined) {
	        this.range.start = customRange.min;
	        autoScaleStart = false;
	      }
	      this.scale = new DataScale(this.range.start, this.range.end, autoScaleStart, autoScaleEnd, this.dom.frame.offsetHeight, this.props.majorCharHeight, this.options.alignZeros, this.options[orientation].format);
	      if (this.master === false && this.masterAxis != undefined) {
	        this.scale.followScale(this.masterAxis.scale);
	        this.dom.lineContainer.style.display = 'none';
	      } else {
	        this.dom.lineContainer.style.display = 'block';
	      }

	      
	      this.maxLabelSize = 0;
	      var lines = this.scale.getLines();
	      _forEachInstanceProperty(lines).call(lines, function (line) {
	        var y = line.y;
	        var isMajor = line.major;
	        if (_this2.options['showMinorLabels'] && isMajor === false) {
	          _this2._redrawLabel(y - 2, line.val, orientation, 'vis-y-axis vis-minor', _this2.props.minorCharHeight);
	        }
	        if (isMajor) {
	          if (y >= 0) {
	            _this2._redrawLabel(y - 2, line.val, orientation, 'vis-y-axis vis-major', _this2.props.majorCharHeight);
	          }
	        }
	        if (_this2.master === true) {
	          if (isMajor) {
	            _this2._redrawLine(y, orientation, 'vis-grid vis-horizontal vis-major', _this2.options.majorLinesOffset, _this2.props.majorLineWidth);
	          } else {
	            _this2._redrawLine(y, orientation, 'vis-grid vis-horizontal vis-minor', _this2.options.minorLinesOffset, _this2.props.minorLineWidth);
	          }
	        }
	      });

	      
	      var titleWidth = 0;
	      if (this.options[orientation].title !== undefined && this.options[orientation].title.text !== undefined) {
	        titleWidth = this.props.titleCharHeight;
	      }
	      var offset = this.options.icons === true ? Math.max(this.options.iconWidth, titleWidth) + this.options.labelOffsetX + 15 : titleWidth + this.options.labelOffsetX + 15;

	      
	      if (this.maxLabelSize > this.width - offset && this.options.visible === true) {
	        this.width = this.maxLabelSize + offset;
	        this.options.width = "".concat(this.width, "px");
	        cleanupElements(this.DOMelements.lines);
	        cleanupElements(this.DOMelements.labels);
	        this.redraw();
	        resized = true;
	      }
	      
	      else if (this.maxLabelSize < this.width - offset && this.options.visible === true && this.width > this.minWidth) {
	        this.width = Math.max(this.minWidth, this.maxLabelSize + offset);
	        this.options.width = "".concat(this.width, "px");
	        cleanupElements(this.DOMelements.lines);
	        cleanupElements(this.DOMelements.labels);
	        this.redraw();
	        resized = true;
	      } else {
	        cleanupElements(this.DOMelements.lines);
	        cleanupElements(this.DOMelements.labels);
	        resized = false;
	      }
	      return resized;
	    }

	    
	  }, {
	    key: "convertValue",
	    value: function convertValue(value) {
	      return this.scale.convertValue(value);
	    }

	    
	  }, {
	    key: "screenToValue",
	    value: function screenToValue(x) {
	      return this.scale.screenToValue(x);
	    }

	    
	  }, {
	    key: "_redrawLabel",
	    value: function _redrawLabel(y, text, orientation, className, characterHeight) {
	      
	      var label = getDOMElement('div', this.DOMelements.labels, this.dom.frame); 
	      label.className = className;
	      label.innerHTML = availableUtils.xss(text);
	      if (orientation === 'left') {
	        label.style.left = "-".concat(this.options.labelOffsetX, "px");
	        label.style.textAlign = "right";
	      } else {
	        label.style.right = "-".concat(this.options.labelOffsetX, "px");
	        label.style.textAlign = "left";
	      }
	      label.style.top = "".concat(y - 0.5 * characterHeight + this.options.labelOffsetY, "px");
	      text += '';
	      var largestWidth = Math.max(this.props.majorCharWidth, this.props.minorCharWidth);
	      if (this.maxLabelSize < text.length * largestWidth) {
	        this.maxLabelSize = text.length * largestWidth;
	      }
	    }

	    
	  }, {
	    key: "_redrawLine",
	    value: function _redrawLine(y, orientation, className, offset, width) {
	      if (this.master === true) {
	        var line = getDOMElement('div', this.DOMelements.lines, this.dom.lineContainer); 
	        line.className = className;
	        line.innerHTML = '';
	        if (orientation === 'left') {
	          line.style.left = "".concat(this.width - offset, "px");
	        } else {
	          line.style.right = "".concat(this.width - offset, "px");
	        }
	        line.style.width = "".concat(width, "px");
	        line.style.top = "".concat(y, "px");
	      }
	    }

	    
	  }, {
	    key: "_redrawTitle",
	    value: function _redrawTitle(orientation) {
	      prepareElements(this.DOMelements.title);

	      
	      if (this.options[orientation].title !== undefined && this.options[orientation].title.text !== undefined) {
	        var title = getDOMElement('div', this.DOMelements.title, this.dom.frame);
	        title.className = "vis-y-axis vis-title vis-".concat(orientation);
	        title.innerHTML = availableUtils.xss(this.options[orientation].title.text);

	        
	        if (this.options[orientation].title.style !== undefined) {
	          availableUtils.addCssText(title, this.options[orientation].title.style);
	        }
	        if (orientation === 'left') {
	          title.style.left = "".concat(this.props.titleCharHeight, "px");
	        } else {
	          title.style.right = "".concat(this.props.titleCharHeight, "px");
	        }
	        title.style.width = "".concat(this.height, "px");
	      }

	      
	      cleanupElements(this.DOMelements.title);
	    }

	    
	  }, {
	    key: "_calculateCharSize",
	    value: function _calculateCharSize() {
	      
	      if (!('minorCharHeight' in this.props)) {
	        var textMinor = document.createTextNode('0');
	        var measureCharMinor = document.createElement('div');
	        measureCharMinor.className = 'vis-y-axis vis-minor vis-measure';
	        measureCharMinor.appendChild(textMinor);
	        this.dom.frame.appendChild(measureCharMinor);
	        this.props.minorCharHeight = measureCharMinor.clientHeight;
	        this.props.minorCharWidth = measureCharMinor.clientWidth;
	        this.dom.frame.removeChild(measureCharMinor);
	      }
	      if (!('majorCharHeight' in this.props)) {
	        var textMajor = document.createTextNode('0');
	        var measureCharMajor = document.createElement('div');
	        measureCharMajor.className = 'vis-y-axis vis-major vis-measure';
	        measureCharMajor.appendChild(textMajor);
	        this.dom.frame.appendChild(measureCharMajor);
	        this.props.majorCharHeight = measureCharMajor.clientHeight;
	        this.props.majorCharWidth = measureCharMajor.clientWidth;
	        this.dom.frame.removeChild(measureCharMajor);
	      }
	      if (!('titleCharHeight' in this.props)) {
	        var textTitle = document.createTextNode('0');
	        var measureCharTitle = document.createElement('div');
	        measureCharTitle.className = 'vis-y-axis vis-title vis-measure';
	        measureCharTitle.appendChild(textTitle);
	        this.dom.frame.appendChild(measureCharTitle);
	        this.props.titleCharHeight = measureCharTitle.clientHeight;
	        this.props.titleCharWidth = measureCharTitle.clientWidth;
	        this.dom.frame.removeChild(measureCharTitle);
	      }
	    }
	  }]);
	  return DataAxis;
	}(Component);

	
	function Points(groupId, options) {
	}

	
	Points.draw = function (dataset, group, framework, offset) {
	  offset = offset || 0;
	  var callback = getCallback(framework, group);
	  for (var i = 0; i < dataset.length; i++) {
	    if (!callback) {
	      
	      drawPoint(dataset[i].screen_x + offset, dataset[i].screen_y, getGroupTemplate(group), framework.svgElements, framework.svg, dataset[i].label);
	    } else {
	      var callbackResult = callback(dataset[i], group); 
	      if (callbackResult === true || _typeof(callbackResult) === 'object') {
	        drawPoint(dataset[i].screen_x + offset, dataset[i].screen_y, getGroupTemplate(group, callbackResult), framework.svgElements, framework.svg, dataset[i].label);
	      }
	    }
	  }
	};
	Points.drawIcon = function (group, x, y, iconWidth, iconHeight, framework) {
	  var fillHeight = iconHeight * 0.5;
	  var outline = getSVGElement("rect", framework.svgElements, framework.svg);
	  outline.setAttributeNS(null, "x", x);
	  outline.setAttributeNS(null, "y", y - fillHeight);
	  outline.setAttributeNS(null, "width", iconWidth);
	  outline.setAttributeNS(null, "height", 2 * fillHeight);
	  outline.setAttributeNS(null, "class", "vis-outline");

	  
	  drawPoint(x + 0.5 * iconWidth, y, getGroupTemplate(group), framework.svgElements, framework.svg);
	};

	
	function getGroupTemplate(group, callbackResult) {
	  callbackResult = typeof callbackResult === 'undefined' ? {} : callbackResult;
	  return {
	    style: callbackResult.style || group.options.drawPoints.style,
	    styles: callbackResult.styles || group.options.drawPoints.styles,
	    size: callbackResult.size || group.options.drawPoints.size,
	    className: callbackResult.className || group.className
	  };
	}

	
	function getCallback(framework, group) {
	  var callback = undefined;
	  
	  if (framework.options && framework.options.drawPoints && framework.options.drawPoints.onRender && typeof framework.options.drawPoints.onRender == 'function') {
	    callback = framework.options.drawPoints.onRender;
	  }

	  
	  if (group.group.options && group.group.options.drawPoints && group.group.options.drawPoints.onRender && typeof group.group.options.drawPoints.onRender == 'function') {
	    callback = group.group.options.drawPoints.onRender;
	  }
	  return callback;
	}

	
	function Bargraph(groupId, options) {
	}
	Bargraph.drawIcon = function (group, x, y, iconWidth, iconHeight, framework) {
	  var fillHeight = iconHeight * 0.5;
	  var outline = getSVGElement("rect", framework.svgElements, framework.svg);
	  outline.setAttributeNS(null, "x", x);
	  outline.setAttributeNS(null, "y", y - fillHeight);
	  outline.setAttributeNS(null, "width", iconWidth);
	  outline.setAttributeNS(null, "height", 2 * fillHeight);
	  outline.setAttributeNS(null, "class", "vis-outline");
	  var barWidth = Math.round(0.3 * iconWidth);
	  var originalWidth = group.options.barChart.width;
	  var scale = originalWidth / barWidth;
	  var bar1Height = Math.round(0.4 * iconHeight);
	  var bar2Height = Math.round(0.75 * iconHeight);
	  var offset = Math.round((iconWidth - 2 * barWidth) / 3);
	  drawBar(x + 0.5 * barWidth + offset, y + fillHeight - bar1Height - 1, barWidth, bar1Height, group.className + ' vis-bar', framework.svgElements, framework.svg, group.style);
	  drawBar(x + 1.5 * barWidth + offset + 2, y + fillHeight - bar2Height - 1, barWidth, bar2Height, group.className + ' vis-bar', framework.svgElements, framework.svg, group.style);
	  if (group.options.drawPoints.enabled == true) {
	    var groupTemplate = {
	      style: group.options.drawPoints.style,
	      styles: group.options.drawPoints.styles,
	      size: group.options.drawPoints.size / scale,
	      className: group.className
	    };
	    drawPoint(x + 0.5 * barWidth + offset, y + fillHeight - bar1Height - 1, groupTemplate, framework.svgElements, framework.svg);
	    drawPoint(x + 1.5 * barWidth + offset + 2, y + fillHeight - bar2Height - 1, groupTemplate, framework.svgElements, framework.svg);
	  }
	};

	
	Bargraph.draw = function (groupIds, processedGroupData, framework) {
	  var combinedData = [];
	  var intersections = {};
	  var coreDistance;
	  var key, drawData;
	  var group;
	  var i, j;
	  var barPoints = 0;

	  
	  for (i = 0; i < groupIds.length; i++) {
	    group = framework.groups[groupIds[i]];
	    if (group.options.style === 'bar') {
	      if (group.visible === true && (framework.options.groups.visibility[groupIds[i]] === undefined || framework.options.groups.visibility[groupIds[i]] === true)) {
	        for (j = 0; j < processedGroupData[groupIds[i]].length; j++) {
	          combinedData.push({
	            screen_x: processedGroupData[groupIds[i]][j].screen_x,
	            screen_end: processedGroupData[groupIds[i]][j].screen_end,
	            screen_y: processedGroupData[groupIds[i]][j].screen_y,
	            x: processedGroupData[groupIds[i]][j].x,
	            end: processedGroupData[groupIds[i]][j].end,
	            y: processedGroupData[groupIds[i]][j].y,
	            groupId: groupIds[i],
	            label: processedGroupData[groupIds[i]][j].label
	          });
	          barPoints += 1;
	        }
	      }
	    }
	  }
	  if (barPoints === 0) {
	    return;
	  }

	  
	  _sortInstanceProperty(combinedData).call(combinedData, function (a, b) {
	    if (a.screen_x === b.screen_x) {
	      return a.groupId < b.groupId ? -1 : 1;
	    } else {
	      return a.screen_x - b.screen_x;
	    }
	  });

	  
	  Bargraph._getDataIntersections(intersections, combinedData);

	  
	  for (i = 0; i < combinedData.length; i++) {
	    group = framework.groups[combinedData[i].groupId];
	    var minWidth = group.options.barChart.minWidth != undefined ? group.options.barChart.minWidth : 0.1 * group.options.barChart.width;
	    key = combinedData[i].screen_x;
	    var heightOffset = 0;
	    if (intersections[key] === undefined) {
	      if (i + 1 < combinedData.length) {
	        coreDistance = Math.abs(combinedData[i + 1].screen_x - key);
	      }
	      drawData = Bargraph._getSafeDrawData(coreDistance, group, minWidth);
	    } else {
	      var nextKey = i + (intersections[key].amount - intersections[key].resolved);
	      if (nextKey < combinedData.length) {
	        coreDistance = Math.abs(combinedData[nextKey].screen_x - key);
	      }
	      drawData = Bargraph._getSafeDrawData(coreDistance, group, minWidth);
	      intersections[key].resolved += 1;
	      if (group.options.stack === true && group.options.excludeFromStacking !== true) {
	        if (combinedData[i].screen_y < group.zeroPosition) {
	          heightOffset = intersections[key].accumulatedNegative;
	          intersections[key].accumulatedNegative += group.zeroPosition - combinedData[i].screen_y;
	        } else {
	          heightOffset = intersections[key].accumulatedPositive;
	          intersections[key].accumulatedPositive += group.zeroPosition - combinedData[i].screen_y;
	        }
	      } else if (group.options.barChart.sideBySide === true) {
	        drawData.width = drawData.width / intersections[key].amount;
	        drawData.offset += intersections[key].resolved * drawData.width - 0.5 * drawData.width * (intersections[key].amount + 1);
	      }
	    }
	    var dataWidth = drawData.width;
	    var start = combinedData[i].screen_x;

	    
	    if (combinedData[i].screen_end != undefined) {
	      dataWidth = combinedData[i].screen_end - combinedData[i].screen_x;
	      start += dataWidth * 0.5;
	    } else {
	      start += drawData.offset;
	    }
	    drawBar(start, combinedData[i].screen_y - heightOffset, dataWidth, group.zeroPosition - combinedData[i].screen_y, group.className + ' vis-bar', framework.svgElements, framework.svg, group.style);

	    
	    if (group.options.drawPoints.enabled === true) {
	      var pointData = {
	        screen_x: combinedData[i].screen_x,
	        screen_y: combinedData[i].screen_y - heightOffset,
	        x: combinedData[i].x,
	        y: combinedData[i].y,
	        groupId: combinedData[i].groupId,
	        label: combinedData[i].label
	      };
	      Points.draw([pointData], group, framework, drawData.offset);
	      
	    }
	  }
	};

	
	Bargraph._getDataIntersections = function (intersections, combinedData) {
	  
	  var coreDistance;
	  for (var i = 0; i < combinedData.length; i++) {
	    if (i + 1 < combinedData.length) {
	      coreDistance = Math.abs(combinedData[i + 1].screen_x - combinedData[i].screen_x);
	    }
	    if (i > 0) {
	      coreDistance = Math.min(coreDistance, Math.abs(combinedData[i - 1].screen_x - combinedData[i].screen_x));
	    }
	    if (coreDistance === 0) {
	      if (intersections[combinedData[i].screen_x] === undefined) {
	        intersections[combinedData[i].screen_x] = {
	          amount: 0,
	          resolved: 0,
	          accumulatedPositive: 0,
	          accumulatedNegative: 0
	        };
	      }
	      intersections[combinedData[i].screen_x].amount += 1;
	    }
	  }
	};

	
	Bargraph._getSafeDrawData = function (coreDistance, group, minWidth) {
	  var width, offset;
	  if (coreDistance < group.options.barChart.width && coreDistance > 0) {
	    width = coreDistance < minWidth ? minWidth : coreDistance;
	    offset = 0; 
	    if (group.options.barChart.align === 'left') {
	      offset -= 0.5 * coreDistance;
	    } else if (group.options.barChart.align === 'right') {
	      offset += 0.5 * coreDistance;
	    }
	  } else {
	    
	    width = group.options.barChart.width;
	    offset = 0;
	    if (group.options.barChart.align === 'left') {
	      offset -= 0.5 * group.options.barChart.width;
	    } else if (group.options.barChart.align === 'right') {
	      offset += 0.5 * group.options.barChart.width;
	    }
	  }
	  return {
	    width: width,
	    offset: offset
	  };
	};
	Bargraph.getStackedYRange = function (combinedData, groupRanges, groupIds, groupLabel, orientation) {
	  if (combinedData.length > 0) {
	    
	    _sortInstanceProperty(combinedData).call(combinedData, function (a, b) {
	      if (a.screen_x === b.screen_x) {
	        return a.groupId < b.groupId ? -1 : 1;
	      } else {
	        return a.screen_x - b.screen_x;
	      }
	    });
	    var intersections = {};
	    Bargraph._getDataIntersections(intersections, combinedData);
	    groupRanges[groupLabel] = Bargraph._getStackedYRange(intersections, combinedData);
	    groupRanges[groupLabel].yAxisOrientation = orientation;
	    groupIds.push(groupLabel);
	  }
	};
	Bargraph._getStackedYRange = function (intersections, combinedData) {
	  var key;
	  var yMin = combinedData[0].screen_y;
	  var yMax = combinedData[0].screen_y;
	  for (var i = 0; i < combinedData.length; i++) {
	    key = combinedData[i].screen_x;
	    if (intersections[key] === undefined) {
	      yMin = yMin > combinedData[i].screen_y ? combinedData[i].screen_y : yMin;
	      yMax = yMax < combinedData[i].screen_y ? combinedData[i].screen_y : yMax;
	    } else {
	      if (combinedData[i].screen_y < 0) {
	        intersections[key].accumulatedNegative += combinedData[i].screen_y;
	      } else {
	        intersections[key].accumulatedPositive += combinedData[i].screen_y;
	      }
	    }
	  }
	  for (var xpos in intersections) {
	    if (intersections.hasOwnProperty(xpos)) {
	      yMin = yMin > intersections[xpos].accumulatedNegative ? intersections[xpos].accumulatedNegative : yMin;
	      yMin = yMin > intersections[xpos].accumulatedPositive ? intersections[xpos].accumulatedPositive : yMin;
	      yMax = yMax < intersections[xpos].accumulatedNegative ? intersections[xpos].accumulatedNegative : yMax;
	      yMax = yMax < intersections[xpos].accumulatedPositive ? intersections[xpos].accumulatedPositive : yMax;
	    }
	  }
	  return {
	    min: yMin,
	    max: yMax
	  };
	};

	
	function Line(groupId, options) {
	}
	Line.calcPath = function (dataset, group) {
	  if (dataset != null) {
	    if (dataset.length > 0) {
	      var d = [];

	      
	      if (group.options.interpolation.enabled == true) {
	        d = Line._catmullRom(dataset, group);
	      } else {
	        d = Line._linear(dataset);
	      }
	      return d;
	    }
	  }
	};
	Line.drawIcon = function (group, x, y, iconWidth, iconHeight, framework) {
	  var fillHeight = iconHeight * 0.5;
	  var path, fillPath;
	  var outline = getSVGElement("rect", framework.svgElements, framework.svg);
	  outline.setAttributeNS(null, "x", x);
	  outline.setAttributeNS(null, "y", y - fillHeight);
	  outline.setAttributeNS(null, "width", iconWidth);
	  outline.setAttributeNS(null, "height", 2 * fillHeight);
	  outline.setAttributeNS(null, "class", "vis-outline");
	  path = getSVGElement("path", framework.svgElements, framework.svg);
	  path.setAttributeNS(null, "class", group.className);
	  if (group.style !== undefined) {
	    path.setAttributeNS(null, "style", group.style);
	  }
	  path.setAttributeNS(null, "d", "M" + x + "," + y + " L" + (x + iconWidth) + "," + y + "");
	  if (group.options.shaded.enabled == true) {
	    fillPath = getSVGElement("path", framework.svgElements, framework.svg);
	    if (group.options.shaded.orientation == 'top') {
	      fillPath.setAttributeNS(null, "d", "M" + x + ", " + (y - fillHeight) + "L" + x + "," + y + " L" + (x + iconWidth) + "," + y + " L" + (x + iconWidth) + "," + (y - fillHeight));
	    } else {
	      fillPath.setAttributeNS(null, "d", "M" + x + "," + y + " " + "L" + x + "," + (y + fillHeight) + " " + "L" + (x + iconWidth) + "," + (y + fillHeight) + "L" + (x + iconWidth) + "," + y);
	    }
	    fillPath.setAttributeNS(null, "class", group.className + " vis-icon-fill");
	    if (group.options.shaded.style !== undefined && group.options.shaded.style !== "") {
	      fillPath.setAttributeNS(null, "style", group.options.shaded.style);
	    }
	  }
	  if (group.options.drawPoints.enabled == true) {
	    var groupTemplate = {
	      style: group.options.drawPoints.style,
	      styles: group.options.drawPoints.styles,
	      size: group.options.drawPoints.size,
	      className: group.className
	    };
	    drawPoint(x + 0.5 * iconWidth, y, groupTemplate, framework.svgElements, framework.svg);
	  }
	};
	Line.drawShading = function (pathArray, group, subPathArray, framework) {
	  
	  if (group.options.shaded.enabled == true) {
	    var svgHeight = Number(framework.svg.style.height.replace('px', ''));
	    var fillPath = getSVGElement('path', framework.svgElements, framework.svg);
	    var type = "L";
	    if (group.options.interpolation.enabled == true) {
	      type = "C";
	    }
	    var dFill;
	    var zero = 0;
	    if (group.options.shaded.orientation == 'top') {
	      zero = 0;
	    } else if (group.options.shaded.orientation == 'bottom') {
	      zero = svgHeight;
	    } else {
	      zero = Math.min(Math.max(0, group.zeroPosition), svgHeight);
	    }
	    if (group.options.shaded.orientation == 'group' && subPathArray != null && subPathArray != undefined) {
	      dFill = 'M' + pathArray[0][0] + "," + pathArray[0][1] + " " + this.serializePath(pathArray, type, false) + ' L' + subPathArray[subPathArray.length - 1][0] + "," + subPathArray[subPathArray.length - 1][1] + " " + this.serializePath(subPathArray, type, true) + subPathArray[0][0] + "," + subPathArray[0][1] + " Z";
	    } else {
	      dFill = 'M' + pathArray[0][0] + "," + pathArray[0][1] + " " + this.serializePath(pathArray, type, false) + ' V' + zero + ' H' + pathArray[0][0] + " Z";
	    }
	    fillPath.setAttributeNS(null, 'class', group.className + ' vis-fill');
	    if (group.options.shaded.style !== undefined) {
	      fillPath.setAttributeNS(null, 'style', group.options.shaded.style);
	    }
	    fillPath.setAttributeNS(null, 'd', dFill);
	  }
	};

	
	Line.draw = function (pathArray, group, framework) {
	  if (pathArray != null && pathArray != undefined) {
	    var path = getSVGElement('path', framework.svgElements, framework.svg);
	    path.setAttributeNS(null, "class", group.className);
	    if (group.style !== undefined) {
	      path.setAttributeNS(null, "style", group.style);
	    }
	    var type = "L";
	    if (group.options.interpolation.enabled == true) {
	      type = "C";
	    }
	    
	    path.setAttributeNS(null, 'd', 'M' + pathArray[0][0] + "," + pathArray[0][1] + " " + this.serializePath(pathArray, type, false));
	  }
	};
	Line.serializePath = function (pathArray, type, inverse) {
	  if (pathArray.length < 2) {
	    
	    return "";
	  }
	  var d = type;
	  var i;
	  if (inverse) {
	    for (i = pathArray.length - 2; i > 0; i--) {
	      d += pathArray[i][0] + "," + pathArray[i][1] + " ";
	    }
	  } else {
	    for (i = 1; i < pathArray.length; i++) {
	      d += pathArray[i][0] + "," + pathArray[i][1] + " ";
	    }
	  }
	  return d;
	};

	
	Line._catmullRomUniform = function (data) {
	  
	  var p0, p1, p2, p3, bp1, bp2;
	  var d = [];
	  d.push([Math.round(data[0].screen_x), Math.round(data[0].screen_y)]);
	  var normalization = 1 / 6;
	  var length = data.length;
	  for (var i = 0; i < length - 1; i++) {
	    p0 = i == 0 ? data[0] : data[i - 1];
	    p1 = data[i];
	    p2 = data[i + 1];
	    p3 = i + 2 < length ? data[i + 2] : p2;

	    
	    
	    
	    
	    

	    
	    bp1 = {
	      screen_x: (-p0.screen_x + 6 * p1.screen_x + p2.screen_x) * normalization,
	      screen_y: (-p0.screen_y + 6 * p1.screen_y + p2.screen_y) * normalization
	    };
	    bp2 = {
	      screen_x: (p1.screen_x + 6 * p2.screen_x - p3.screen_x) * normalization,
	      screen_y: (p1.screen_y + 6 * p2.screen_y - p3.screen_y) * normalization
	    };
	    

	    d.push([bp1.screen_x, bp1.screen_y]);
	    d.push([bp2.screen_x, bp2.screen_y]);
	    d.push([p2.screen_x, p2.screen_y]);
	  }
	  return d;
	};

	
	Line._catmullRom = function (data, group) {
	  var alpha = group.options.interpolation.alpha;
	  if (alpha == 0 || alpha === undefined) {
	    return this._catmullRomUniform(data);
	  } else {
	    var p0, p1, p2, p3, bp1, bp2, d1, d2, d3, A, B, N, M;
	    var d3powA, d2powA, d3pow2A, d2pow2A, d1pow2A, d1powA;
	    var d = [];
	    d.push([Math.round(data[0].screen_x), Math.round(data[0].screen_y)]);
	    var length = data.length;
	    for (var i = 0; i < length - 1; i++) {
	      p0 = i == 0 ? data[0] : data[i - 1];
	      p1 = data[i];
	      p2 = data[i + 1];
	      p3 = i + 2 < length ? data[i + 2] : p2;
	      d1 = Math.sqrt(Math.pow(p0.screen_x - p1.screen_x, 2) + Math.pow(p0.screen_y - p1.screen_y, 2));
	      d2 = Math.sqrt(Math.pow(p1.screen_x - p2.screen_x, 2) + Math.pow(p1.screen_y - p2.screen_y, 2));
	      d3 = Math.sqrt(Math.pow(p2.screen_x - p3.screen_x, 2) + Math.pow(p2.screen_y - p3.screen_y, 2));

	      

	      
	      

	      
	      
	      
	      

	      d3powA = Math.pow(d3, alpha);
	      d3pow2A = Math.pow(d3, 2 * alpha);
	      d2powA = Math.pow(d2, alpha);
	      d2pow2A = Math.pow(d2, 2 * alpha);
	      d1powA = Math.pow(d1, alpha);
	      d1pow2A = Math.pow(d1, 2 * alpha);
	      A = 2 * d1pow2A + 3 * d1powA * d2powA + d2pow2A;
	      B = 2 * d3pow2A + 3 * d3powA * d2powA + d2pow2A;
	      N = 3 * d1powA * (d1powA + d2powA);
	      if (N > 0) {
	        N = 1 / N;
	      }
	      M = 3 * d3powA * (d3powA + d2powA);
	      if (M > 0) {
	        M = 1 / M;
	      }
	      bp1 = {
	        screen_x: (-d2pow2A * p0.screen_x + A * p1.screen_x + d1pow2A * p2.screen_x) * N,
	        screen_y: (-d2pow2A * p0.screen_y + A * p1.screen_y + d1pow2A * p2.screen_y) * N
	      };
	      bp2 = {
	        screen_x: (d3pow2A * p1.screen_x + B * p2.screen_x - d2pow2A * p3.screen_x) * M,
	        screen_y: (d3pow2A * p1.screen_y + B * p2.screen_y - d2pow2A * p3.screen_y) * M
	      };
	      if (bp1.screen_x == 0 && bp1.screen_y == 0) {
	        bp1 = p1;
	      }
	      if (bp2.screen_x == 0 && bp2.screen_y == 0) {
	        bp2 = p2;
	      }
	      d.push([bp1.screen_x, bp1.screen_y]);
	      d.push([bp2.screen_x, bp2.screen_y]);
	      d.push([p2.screen_x, p2.screen_y]);
	    }
	    return d;
	  }
	};

	
	Line._linear = function (data) {
	  
	  var d = [];
	  for (var i = 0; i < data.length; i++) {
	    d.push([data[i].screen_x, data[i].screen_y]);
	  }
	  return d;
	};

	
	function GraphGroup(group, groupId, options, groupsUsingDefaultStyles) {
	  this.id = groupId;
	  var fields = ['sampling', 'style', 'sort', 'yAxisOrientation', 'barChart', 'drawPoints', 'shaded', 'interpolation', 'zIndex', 'excludeFromStacking', 'excludeFromLegend'];
	  this.options = availableUtils.selectiveBridgeObject(fields, options);
	  this.usingDefaultStyle = group.className === undefined;
	  this.groupsUsingDefaultStyles = groupsUsingDefaultStyles;
	  this.zeroPosition = 0;
	  this.update(group);
	  if (this.usingDefaultStyle == true) {
	    this.groupsUsingDefaultStyles[0] += 1;
	  }
	  this.itemsData = [];
	  this.visible = group.visible === undefined ? true : group.visible;
	}

	
	GraphGroup.prototype.setItems = function (items) {
	  if (items != null) {
	    this.itemsData = items;
	    if (_sortInstanceProperty(this.options) == true) {
	      availableUtils.insertSort(this.itemsData, function (a, b) {
	        return a.x > b.x ? 1 : -1;
	      });
	    }
	  } else {
	    this.itemsData = [];
	  }
	};
	GraphGroup.prototype.getItems = function () {
	  return this.itemsData;
	};

	
	GraphGroup.prototype.setZeroPosition = function (pos) {
	  this.zeroPosition = pos;
	};

	
	GraphGroup.prototype.setOptions = function (options) {
	  if (options !== undefined) {
	    var fields = ['sampling', 'style', 'sort', 'yAxisOrientation', 'barChart', 'zIndex', 'excludeFromStacking', 'excludeFromLegend'];
	    availableUtils.selectiveDeepExtend(fields, this.options, options);

	    
	    if (typeof options.drawPoints == 'function') {
	      options.drawPoints = {
	        onRender: options.drawPoints
	      };
	    }
	    availableUtils.mergeOptions(this.options, options, 'interpolation');
	    availableUtils.mergeOptions(this.options, options, 'drawPoints');
	    availableUtils.mergeOptions(this.options, options, 'shaded');
	    if (options.interpolation) {
	      if (_typeof(options.interpolation) == 'object') {
	        if (options.interpolation.parametrization) {
	          if (options.interpolation.parametrization == 'uniform') {
	            this.options.interpolation.alpha = 0;
	          } else if (options.interpolation.parametrization == 'chordal') {
	            this.options.interpolation.alpha = 1.0;
	          } else {
	            this.options.interpolation.parametrization = 'centripetal';
	            this.options.interpolation.alpha = 0.5;
	          }
	        }
	      }
	    }
	  }
	};

	
	GraphGroup.prototype.update = function (group) {
	  this.group = group;
	  this.content = group.content || 'graph';
	  this.className = group.className || this.className || 'vis-graph-group' + this.groupsUsingDefaultStyles[0] % 10;
	  this.visible = group.visible === undefined ? true : group.visible;
	  this.style = group.style;
	  this.setOptions(group.options);
	};

	
	GraphGroup.prototype.getLegend = function (iconWidth, iconHeight, framework, x, y) {
	  if (framework == undefined || framework == null) {
	    var svg = document.createElementNS('http:
	    framework = {
	      svg: svg,
	      svgElements: {},
	      options: this.options,
	      groups: [this]
	    };
	  }
	  if (x == undefined || x == null) {
	    x = 0;
	  }
	  if (y == undefined || y == null) {
	    y = 0.5 * iconHeight;
	  }
	  switch (this.options.style) {
	    case "line":
	      Line.drawIcon(this, x, y, iconWidth, iconHeight, framework);
	      break;
	    case "points": 
	    case "point":
	      Points.drawIcon(this, x, y, iconWidth, iconHeight, framework);
	      break;
	    case "bar":
	      Bargraph.drawIcon(this, x, y, iconWidth, iconHeight, framework);
	      break;
	  }
	  return {
	    icon: framework.svg,
	    label: this.content,
	    orientation: this.options.yAxisOrientation
	  };
	};
	GraphGroup.prototype.getYRange = function (groupData) {
	  var yMin = groupData[0].y;
	  var yMax = groupData[0].y;
	  for (var j = 0; j < groupData.length; j++) {
	    yMin = yMin > groupData[j].y ? groupData[j].y : yMin;
	    yMax = yMax < groupData[j].y ? groupData[j].y : yMax;
	  }
	  return {
	    min: yMin,
	    max: yMax,
	    yAxisOrientation: this.options.yAxisOrientation
	  };
	};

	
	function Legend(body, options, side, linegraphOptions) {
	  this.body = body;
	  this.defaultOptions = {
	    enabled: false,
	    icons: true,
	    iconSize: 20,
	    iconSpacing: 6,
	    left: {
	      visible: true,
	      position: 'top-left' 
	    },

	    right: {
	      visible: true,
	      position: 'top-right' 
	    }
	  };

	  this.side = side;
	  this.options = availableUtils.extend({}, this.defaultOptions);
	  this.linegraphOptions = linegraphOptions;
	  this.svgElements = {};
	  this.dom = {};
	  this.groups = {};
	  this.amountOfGroups = 0;
	  this._create();
	  this.framework = {
	    svg: this.svg,
	    svgElements: this.svgElements,
	    options: this.options,
	    groups: this.groups
	  };
	  this.setOptions(options);
	}
	Legend.prototype = new Component();
	Legend.prototype.clear = function () {
	  this.groups = {};
	  this.amountOfGroups = 0;
	};
	Legend.prototype.addGroup = function (label, graphOptions) {
	  
	  if (graphOptions.options.excludeFromLegend != true) {
	    if (!this.groups.hasOwnProperty(label)) {
	      this.groups[label] = graphOptions;
	    }
	    this.amountOfGroups += 1;
	  }
	};
	Legend.prototype.updateGroup = function (label, graphOptions) {
	  this.groups[label] = graphOptions;
	};
	Legend.prototype.removeGroup = function (label) {
	  if (this.groups.hasOwnProperty(label)) {
	    delete this.groups[label];
	    this.amountOfGroups -= 1;
	  }
	};
	Legend.prototype._create = function () {
	  this.dom.frame = document.createElement('div');
	  this.dom.frame.className = 'vis-legend';
	  this.dom.frame.style.position = "absolute";
	  this.dom.frame.style.top = "10px";
	  this.dom.frame.style.display = "block";
	  this.dom.textArea = document.createElement('div');
	  this.dom.textArea.className = 'vis-legend-text';
	  this.dom.textArea.style.position = "relative";
	  this.dom.textArea.style.top = "0px";
	  this.svg = document.createElementNS('http:
	  this.svg.style.position = 'absolute';
	  this.svg.style.top = 0 + 'px';
	  this.svg.style.width = this.options.iconSize + 5 + 'px';
	  this.svg.style.height = '100%';
	  this.dom.frame.appendChild(this.svg);
	  this.dom.frame.appendChild(this.dom.textArea);
	};

	
	Legend.prototype.hide = function () {
	  
	  if (this.dom.frame.parentNode) {
	    this.dom.frame.parentNode.removeChild(this.dom.frame);
	  }
	};

	
	Legend.prototype.show = function () {
	  
	  if (!this.dom.frame.parentNode) {
	    this.body.dom.center.appendChild(this.dom.frame);
	  }
	};
	Legend.prototype.setOptions = function (options) {
	  var fields = ['enabled', 'orientation', 'icons', 'left', 'right'];
	  availableUtils.selectiveDeepExtend(fields, this.options, options);
	};
	Legend.prototype.redraw = function () {
	  var activeGroups = 0;
	  var groupArray = _Object$keys(this.groups);
	  _sortInstanceProperty(groupArray).call(groupArray, function (a, b) {
	    return a < b ? -1 : 1;
	  });
	  for (var i = 0; i < groupArray.length; i++) {
	    var groupId = groupArray[i];
	    if (this.groups[groupId].visible == true && (this.linegraphOptions.visibility[groupId] === undefined || this.linegraphOptions.visibility[groupId] == true)) {
	      activeGroups++;
	    }
	  }
	  if (this.options[this.side].visible == false || this.amountOfGroups == 0 || this.options.enabled == false || activeGroups == 0) {
	    this.hide();
	  } else {
	    this.show();
	    if (this.options[this.side].position == 'top-left' || this.options[this.side].position == 'bottom-left') {
	      this.dom.frame.style.left = '4px';
	      this.dom.frame.style.textAlign = "left";
	      this.dom.textArea.style.textAlign = "left";
	      this.dom.textArea.style.left = this.options.iconSize + 15 + 'px';
	      this.dom.textArea.style.right = '';
	      this.svg.style.left = 0 + 'px';
	      this.svg.style.right = '';
	    } else {
	      this.dom.frame.style.right = '4px';
	      this.dom.frame.style.textAlign = "right";
	      this.dom.textArea.style.textAlign = "right";
	      this.dom.textArea.style.right = this.options.iconSize + 15 + 'px';
	      this.dom.textArea.style.left = '';
	      this.svg.style.right = 0 + 'px';
	      this.svg.style.left = '';
	    }
	    if (this.options[this.side].position == 'top-left' || this.options[this.side].position == 'top-right') {
	      this.dom.frame.style.top = 4 - Number(this.body.dom.center.style.top.replace("px", "")) + 'px';
	      this.dom.frame.style.bottom = '';
	    } else {
	      var scrollableHeight = this.body.domProps.center.height - this.body.domProps.centerContainer.height;
	      this.dom.frame.style.bottom = 4 + scrollableHeight + Number(this.body.dom.center.style.top.replace("px", "")) + 'px';
	      this.dom.frame.style.top = '';
	    }
	    if (this.options.icons == false) {
	      this.dom.frame.style.width = this.dom.textArea.offsetWidth + 10 + 'px';
	      this.dom.textArea.style.right = '';
	      this.dom.textArea.style.left = '';
	      this.svg.style.width = '0px';
	    } else {
	      this.dom.frame.style.width = this.options.iconSize + 15 + this.dom.textArea.offsetWidth + 10 + 'px';
	      this.drawLegendIcons();
	    }
	    var content = '';
	    for (i = 0; i < groupArray.length; i++) {
	      groupId = groupArray[i];
	      if (this.groups[groupId].visible == true && (this.linegraphOptions.visibility[groupId] === undefined || this.linegraphOptions.visibility[groupId] == true)) {
	        content += this.groups[groupId].content + '<br />';
	      }
	    }
	    this.dom.textArea.innerHTML = availableUtils.xss(content);
	    this.dom.textArea.style.lineHeight = 0.75 * this.options.iconSize + this.options.iconSpacing + 'px';
	  }
	};
	Legend.prototype.drawLegendIcons = function () {
	  if (this.dom.frame.parentNode) {
	    var groupArray = _Object$keys(this.groups);
	    _sortInstanceProperty(groupArray).call(groupArray, function (a, b) {
	      return a < b ? -1 : 1;
	    });

	    
	    resetElements(this.svgElements);
	    var padding = window.getComputedStyle(this.dom.frame).paddingTop;
	    var iconOffset = Number(padding.replace('px', ''));
	    var x = iconOffset;
	    var iconWidth = this.options.iconSize;
	    var iconHeight = 0.75 * this.options.iconSize;
	    var y = iconOffset + 0.5 * iconHeight + 3;
	    this.svg.style.width = iconWidth + 5 + iconOffset + 'px';
	    for (var i = 0; i < groupArray.length; i++) {
	      var groupId = groupArray[i];
	      if (this.groups[groupId].visible == true && (this.linegraphOptions.visibility[groupId] === undefined || this.linegraphOptions.visibility[groupId] == true)) {
	        this.groups[groupId].getLegend(iconWidth, iconHeight, this.framework, x, y);
	        y += iconHeight + this.options.iconSpacing;
	      }
	    }
	  }
	};

	var UNGROUPED = '__ungrouped__'; 

	
	function LineGraph(body, options) {
	  this.id = v4();
	  this.body = body;
	  this.defaultOptions = {
	    yAxisOrientation: 'left',
	    defaultGroup: 'default',
	    sort: true,
	    sampling: true,
	    stack: false,
	    graphHeight: '400px',
	    shaded: {
	      enabled: false,
	      orientation: 'bottom' 
	    },

	    style: 'line',
	    
	    barChart: {
	      width: 50,
	      sideBySide: false,
	      align: 'center' 
	    },

	    interpolation: {
	      enabled: true,
	      parametrization: 'centripetal',
	      
	      alpha: 0.5
	    },
	    drawPoints: {
	      enabled: true,
	      size: 6,
	      style: 'square' 
	    },

	    dataAxis: {},
	    
	    legend: {},
	    
	    groups: {
	      visibility: {}
	    }
	  };

	  
	  this.options = availableUtils.extend({}, this.defaultOptions);
	  this.dom = {};
	  this.props = {};
	  this.hammer = null;
	  this.groups = {};
	  this.abortedGraphUpdate = false;
	  this.updateSVGheight = false;
	  this.updateSVGheightOnResize = false;
	  this.forceGraphUpdate = true;
	  var me = this;
	  this.itemsData = null; 
	  this.groupsData = null; 

	  
	  this.itemListeners = {
	    'add': function add(event, params, senderId) {
	      
	      me._onAdd(params.items);
	    },
	    'update': function update(event, params, senderId) {
	      
	      me._onUpdate(params.items);
	    },
	    'remove': function remove(event, params, senderId) {
	      
	      me._onRemove(params.items);
	    }
	  };

	  
	  this.groupListeners = {
	    'add': function add(event, params, senderId) {
	      
	      me._onAddGroups(params.items);
	    },
	    'update': function update(event, params, senderId) {
	      
	      me._onUpdateGroups(params.items);
	    },
	    'remove': function remove(event, params, senderId) {
	      
	      me._onRemoveGroups(params.items);
	    }
	  };
	  this.items = {}; 
	  this.selection = []; 
	  this.lastStart = this.body.range.start;
	  this.touchParams = {}; 

	  this.svgElements = {};
	  this.setOptions(options);
	  this.groupsUsingDefaultStyles = [0];
	  this.body.emitter.on('rangechanged', function () {
	    me.svg.style.left = availableUtils.option.asSize(-me.props.width);
	    me.forceGraphUpdate = true;
	    
	    me.redraw.call(me);
	  });

	  
	  this._create();
	  this.framework = {
	    svg: this.svg,
	    svgElements: this.svgElements,
	    options: this.options,
	    groups: this.groups
	  };
	}
	LineGraph.prototype = new Component();

	
	LineGraph.prototype._create = function () {
	  var frame = document.createElement('div');
	  frame.className = 'vis-line-graph';
	  this.dom.frame = frame;

	  
	  this.svg = document.createElementNS('http:
	  this.svg.style.position = 'relative';
	  this.svg.style.height = ('' + this.options.graphHeight).replace('px', '') + 'px';
	  this.svg.style.display = 'block';
	  frame.appendChild(this.svg);

	  
	  this.options.dataAxis.orientation = 'left';
	  this.yAxisLeft = new DataAxis(this.body, this.options.dataAxis, this.svg, this.options.groups);
	  this.options.dataAxis.orientation = 'right';
	  this.yAxisRight = new DataAxis(this.body, this.options.dataAxis, this.svg, this.options.groups);
	  delete this.options.dataAxis.orientation;

	  
	  this.legendLeft = new Legend(this.body, this.options.legend, 'left', this.options.groups);
	  this.legendRight = new Legend(this.body, this.options.legend, 'right', this.options.groups);
	  this.show();
	};

	
	LineGraph.prototype.setOptions = function (options) {
	  if (options) {
	    var fields = ['sampling', 'defaultGroup', 'stack', 'height', 'graphHeight', 'yAxisOrientation', 'style', 'barChart', 'dataAxis', 'sort', 'groups'];
	    if (options.graphHeight === undefined && options.height !== undefined) {
	      this.updateSVGheight = true;
	      this.updateSVGheightOnResize = true;
	    } else if (this.body.domProps.centerContainer.height !== undefined && options.graphHeight !== undefined) {
	      if (_parseInt((options.graphHeight + '').replace("px", '')) < this.body.domProps.centerContainer.height) {
	        this.updateSVGheight = true;
	      }
	    }
	    availableUtils.selectiveDeepExtend(fields, this.options, options);
	    availableUtils.mergeOptions(this.options, options, 'interpolation');
	    availableUtils.mergeOptions(this.options, options, 'drawPoints');
	    availableUtils.mergeOptions(this.options, options, 'shaded');
	    availableUtils.mergeOptions(this.options, options, 'legend');
	    if (options.interpolation) {
	      if (_typeof(options.interpolation) == 'object') {
	        if (options.interpolation.parametrization) {
	          if (options.interpolation.parametrization == 'uniform') {
	            this.options.interpolation.alpha = 0;
	          } else if (options.interpolation.parametrization == 'chordal') {
	            this.options.interpolation.alpha = 1.0;
	          } else {
	            this.options.interpolation.parametrization = 'centripetal';
	            this.options.interpolation.alpha = 0.5;
	          }
	        }
	      }
	    }
	    if (this.yAxisLeft) {
	      if (options.dataAxis !== undefined) {
	        this.yAxisLeft.setOptions(this.options.dataAxis);
	        this.yAxisRight.setOptions(this.options.dataAxis);
	      }
	    }
	    if (this.legendLeft) {
	      if (options.legend !== undefined) {
	        this.legendLeft.setOptions(this.options.legend);
	        this.legendRight.setOptions(this.options.legend);
	      }
	    }
	    if (this.groups.hasOwnProperty(UNGROUPED)) {
	      this.groups[UNGROUPED].setOptions(options);
	    }
	  }

	  
	  if (this.dom.frame) {
	    
	    this.forceGraphUpdate = true;
	    this.body.emitter.emit("_change", {
	      queue: true
	    });
	  }
	};

	
	LineGraph.prototype.hide = function () {
	  
	  if (this.dom.frame.parentNode) {
	    this.dom.frame.parentNode.removeChild(this.dom.frame);
	  }
	};

	
	LineGraph.prototype.show = function () {
	  
	  if (!this.dom.frame.parentNode) {
	    this.body.dom.center.appendChild(this.dom.frame);
	  }
	};

	
	LineGraph.prototype.setItems = function (items) {
	  var me = this,
	    ids,
	    oldItemsData = this.itemsData;

	  
	  if (!items) {
	    this.itemsData = null;
	  } else if (isDataViewLike(items)) {
	    this.itemsData = typeCoerceDataSet(items);
	  } else {
	    throw new TypeError('Data must implement the interface of DataSet or DataView');
	  }
	  if (oldItemsData) {
	    
	    _forEachInstanceProperty(availableUtils).call(availableUtils, this.itemListeners, function (callback, event) {
	      oldItemsData.off(event, callback);
	    });

	    
	    oldItemsData.dispose();

	    
	    ids = oldItemsData.getIds();
	    this._onRemove(ids);
	  }
	  if (this.itemsData) {
	    
	    var id = this.id;
	    _forEachInstanceProperty(availableUtils).call(availableUtils, this.itemListeners, function (callback, event) {
	      me.itemsData.on(event, callback, id);
	    });

	    
	    ids = this.itemsData.getIds();
	    this._onAdd(ids);
	  }
	};

	
	LineGraph.prototype.setGroups = function (groups) {
	  var me = this;
	  var ids;

	  
	  if (this.groupsData) {
	    _forEachInstanceProperty(availableUtils).call(availableUtils, this.groupListeners, function (callback, event) {
	      me.groupsData.off(event, callback);
	    });

	    
	    ids = this.groupsData.getIds();
	    this.groupsData = null;
	    for (var i = 0; i < ids.length; i++) {
	      this._removeGroup(ids[i]);
	    }
	  }

	  
	  if (!groups) {
	    this.groupsData = null;
	  } else if (isDataViewLike(groups)) {
	    this.groupsData = groups;
	  } else {
	    throw new TypeError('Data must implement the interface of DataSet or DataView');
	  }
	  if (this.groupsData) {
	    
	    var id = this.id;
	    _forEachInstanceProperty(availableUtils).call(availableUtils, this.groupListeners, function (callback, event) {
	      me.groupsData.on(event, callback, id);
	    });

	    
	    ids = this.groupsData.getIds();
	    this._onAddGroups(ids);
	  }
	};
	LineGraph.prototype._onUpdate = function (ids) {
	  this._updateAllGroupData(ids);
	};
	LineGraph.prototype._onAdd = function (ids) {
	  this._onUpdate(ids);
	};
	LineGraph.prototype._onRemove = function (ids) {
	  this._onUpdate(ids);
	};
	LineGraph.prototype._onUpdateGroups = function (groupIds) {
	  this._updateAllGroupData(null, groupIds);
	};
	LineGraph.prototype._onAddGroups = function (groupIds) {
	  this._onUpdateGroups(groupIds);
	};

	
	LineGraph.prototype._onRemoveGroups = function (groupIds) {
	  for (var i = 0; i < groupIds.length; i++) {
	    this._removeGroup(groupIds[i]);
	  }
	  this.forceGraphUpdate = true;
	  this.body.emitter.emit("_change", {
	    queue: true
	  });
	};

	
	LineGraph.prototype._removeGroup = function (groupId) {
	  if (this.groups.hasOwnProperty(groupId)) {
	    if (this.groups[groupId].options.yAxisOrientation == 'right') {
	      this.yAxisRight.removeGroup(groupId);
	      this.legendRight.removeGroup(groupId);
	      this.legendRight.redraw();
	    } else {
	      this.yAxisLeft.removeGroup(groupId);
	      this.legendLeft.removeGroup(groupId);
	      this.legendLeft.redraw();
	    }
	    delete this.groups[groupId];
	  }
	};

	
	LineGraph.prototype._updateGroup = function (group, groupId) {
	  if (!this.groups.hasOwnProperty(groupId)) {
	    this.groups[groupId] = new GraphGroup(group, groupId, this.options, this.groupsUsingDefaultStyles);
	    if (this.groups[groupId].options.yAxisOrientation == 'right') {
	      this.yAxisRight.addGroup(groupId, this.groups[groupId]);
	      this.legendRight.addGroup(groupId, this.groups[groupId]);
	    } else {
	      this.yAxisLeft.addGroup(groupId, this.groups[groupId]);
	      this.legendLeft.addGroup(groupId, this.groups[groupId]);
	    }
	  } else {
	    this.groups[groupId].update(group);
	    if (this.groups[groupId].options.yAxisOrientation == 'right') {
	      this.yAxisRight.updateGroup(groupId, this.groups[groupId]);
	      this.legendRight.updateGroup(groupId, this.groups[groupId]);
	      
	      this.yAxisLeft.removeGroup(groupId);
	      this.legendLeft.removeGroup(groupId);
	    } else {
	      this.yAxisLeft.updateGroup(groupId, this.groups[groupId]);
	      this.legendLeft.updateGroup(groupId, this.groups[groupId]);
	      
	      this.yAxisRight.removeGroup(groupId);
	      this.legendRight.removeGroup(groupId);
	    }
	  }
	  this.legendLeft.redraw();
	  this.legendRight.redraw();
	};

	
	LineGraph.prototype._updateAllGroupData = function (ids, groupIds) {
	  if (this.itemsData != null) {
	    var groupsContent = {};
	    var items = this.itemsData.get();
	    var fieldId = this.itemsData.idProp;
	    var idMap = {};
	    if (ids) {
	      _mapInstanceProperty(ids).call(ids, function (id) {
	        idMap[id] = id;
	      });
	    }

	    
	    var groupCounts = {};
	    for (var i = 0; i < items.length; i++) {
	      var item = items[i];
	      var groupId = item.group;
	      if (groupId === null || groupId === undefined) {
	        groupId = UNGROUPED;
	      }
	      groupCounts.hasOwnProperty(groupId) ? groupCounts[groupId]++ : groupCounts[groupId] = 1;
	    }

	    
	    var existingItemsMap = {};
	    if (!groupIds && ids) {
	      for (groupId in this.groups) {
	        if (this.groups.hasOwnProperty(groupId)) {
	          group = this.groups[groupId];
	          var existing_items = group.getItems();
	          groupsContent[groupId] = _filterInstanceProperty(existing_items).call(existing_items, function (item) {
	            existingItemsMap[item[fieldId]] = item[fieldId];
	            return item[fieldId] !== idMap[item[fieldId]];
	          });
	          var newLength = groupCounts[groupId];
	          groupCounts[groupId] -= groupsContent[groupId].length;
	          if (groupsContent[groupId].length < newLength) {
	            groupsContent[groupId][newLength - 1] = {};
	          }
	        }
	      }
	    }

	    
	    for (i = 0; i < items.length; i++) {
	      item = items[i];
	      groupId = item.group;
	      if (groupId === null || groupId === undefined) {
	        groupId = UNGROUPED;
	      }
	      if (!groupIds && ids && item[fieldId] !== idMap[item[fieldId]] && existingItemsMap.hasOwnProperty(item[fieldId])) {
	        continue;
	      }
	      if (!groupsContent.hasOwnProperty(groupId)) {
	        groupsContent[groupId] = new Array(groupCounts[groupId]);
	      }
	      
	      var extended = availableUtils.bridgeObject(item);
	      extended.x = availableUtils.convert(item.x, 'Date');
	      extended.end = availableUtils.convert(item.end, 'Date');
	      extended.orginalY = item.y; 
	      extended.y = Number(item.y);
	      extended[fieldId] = item[fieldId];
	      var index = groupsContent[groupId].length - groupCounts[groupId]--;
	      groupsContent[groupId][index] = extended;
	    }

	    
	    for (groupId in this.groups) {
	      if (this.groups.hasOwnProperty(groupId)) {
	        if (!groupsContent.hasOwnProperty(groupId)) {
	          groupsContent[groupId] = new Array(0);
	        }
	      }
	    }

	    
	    for (groupId in groupsContent) {
	      if (groupsContent.hasOwnProperty(groupId)) {
	        if (groupsContent[groupId].length == 0) {
	          if (this.groups.hasOwnProperty(groupId)) {
	            this._removeGroup(groupId);
	          }
	        } else {
	          var group = undefined;
	          if (this.groupsData != undefined) {
	            group = this.groupsData.get(groupId);
	          }
	          if (group == undefined) {
	            group = {
	              id: groupId,
	              content: this.options.defaultGroup + groupId
	            };
	          }
	          this._updateGroup(group, groupId);
	          this.groups[groupId].setItems(groupsContent[groupId]);
	        }
	      }
	    }
	    this.forceGraphUpdate = true;
	    this.body.emitter.emit("_change", {
	      queue: true
	    });
	  }
	};

	
	LineGraph.prototype.redraw = function () {
	  var resized = false;

	  
	  this.props.width = this.dom.frame.offsetWidth;
	  this.props.height = this.body.domProps.centerContainer.height - this.body.domProps.border.top - this.body.domProps.border.bottom;

	  
	  resized = this._isResized() || resized;

	  
	  var visibleInterval = this.body.range.end - this.body.range.start;
	  var zoomed = visibleInterval != this.lastVisibleInterval;
	  this.lastVisibleInterval = visibleInterval;

	  
	  
	  if (resized == true) {
	    var _context;
	    this.svg.style.width = availableUtils.option.asSize(3 * this.props.width);
	    this.svg.style.left = availableUtils.option.asSize(-this.props.width);

	    
	    if (_indexOfInstanceProperty(_context = this.options.height + '').call(_context, "%") != -1 || this.updateSVGheightOnResize == true) {
	      this.updateSVGheight = true;
	    }
	  }

	  
	  if (this.updateSVGheight == true) {
	    if (this.options.graphHeight != this.props.height + 'px') {
	      this.options.graphHeight = this.props.height + 'px';
	      this.svg.style.height = this.props.height + 'px';
	    }
	    this.updateSVGheight = false;
	  } else {
	    this.svg.style.height = ('' + this.options.graphHeight).replace('px', '') + 'px';
	  }

	  
	  if (resized == true || zoomed == true || this.abortedGraphUpdate == true || this.forceGraphUpdate == true) {
	    resized = this._updateGraph() || resized;
	    this.forceGraphUpdate = false;
	    this.lastStart = this.body.range.start;
	    this.svg.style.left = -this.props.width + 'px';
	  } else {
	    
	    if (this.lastStart != 0) {
	      var offset = this.body.range.start - this.lastStart;
	      var range = this.body.range.end - this.body.range.start;
	      if (this.props.width != 0) {
	        var rangePerPixelInv = this.props.width / range;
	        var xOffset = offset * rangePerPixelInv;
	        this.svg.style.left = -this.props.width - xOffset + 'px';
	      }
	    }
	  }
	  this.legendLeft.redraw();
	  this.legendRight.redraw();
	  return resized;
	};
	LineGraph.prototype._getSortedGroupIds = function () {
	  
	  var grouplist = [];
	  for (var groupId in this.groups) {
	    if (this.groups.hasOwnProperty(groupId)) {
	      var group = this.groups[groupId];
	      if (group.visible == true && (this.options.groups.visibility[groupId] === undefined || this.options.groups.visibility[groupId] == true)) {
	        grouplist.push({
	          id: groupId,
	          zIndex: group.options.zIndex
	        });
	      }
	    }
	  }
	  availableUtils.insertSort(grouplist, function (a, b) {
	    var az = a.zIndex;
	    var bz = b.zIndex;
	    if (az === undefined) az = 0;
	    if (bz === undefined) bz = 0;
	    return az == bz ? 0 : az < bz ? -1 : 1;
	  });
	  var groupIds = new Array(grouplist.length);
	  for (var i = 0; i < grouplist.length; i++) {
	    groupIds[i] = grouplist[i].id;
	  }
	  return groupIds;
	};

	
	LineGraph.prototype._updateGraph = function () {
	  
	  prepareElements(this.svgElements);
	  if (this.props.width != 0 && this.itemsData != null) {
	    var group, i;
	    var groupRanges = {};
	    var changeCalled = false;
	    
	    var minDate = this.body.util.toGlobalTime(-this.body.domProps.root.width);
	    var maxDate = this.body.util.toGlobalTime(2 * this.body.domProps.root.width);

	    
	    var groupIds = this._getSortedGroupIds();
	    if (groupIds.length > 0) {
	      var groupsData = {};

	      
	      this._getRelevantData(groupIds, groupsData, minDate, maxDate);

	      
	      this._applySampling(groupIds, groupsData);

	      
	      for (i = 0; i < groupIds.length; i++) {
	        this._convertXcoordinates(groupsData[groupIds[i]]);
	      }

	      
	      this._getYRanges(groupIds, groupsData, groupRanges);

	      
	      changeCalled = this._updateYAxis(groupIds, groupRanges);

	      
	      
	      if (changeCalled == true) {
	        cleanupElements(this.svgElements);
	        this.abortedGraphUpdate = true;
	        return true;
	      }
	      this.abortedGraphUpdate = false;

	      
	      var below = undefined;
	      for (i = 0; i < groupIds.length; i++) {
	        group = this.groups[groupIds[i]];
	        if (this.options.stack === true && this.options.style === 'line') {
	          if (group.options.excludeFromStacking == undefined || !group.options.excludeFromStacking) {
	            if (below != undefined) {
	              this._stack(groupsData[group.id], groupsData[below.id]);
	              if (group.options.shaded.enabled == true && group.options.shaded.orientation !== "group") {
	                if (group.options.shaded.orientation == "top" && below.options.shaded.orientation !== "group") {
	                  below.options.shaded.orientation = "group";
	                  below.options.shaded.groupId = group.id;
	                } else {
	                  group.options.shaded.orientation = "group";
	                  group.options.shaded.groupId = below.id;
	                }
	              }
	            }
	            below = group;
	          }
	        }
	        this._convertYcoordinates(groupsData[groupIds[i]], group);
	      }

	      
	      var paths = {};
	      for (i = 0; i < groupIds.length; i++) {
	        group = this.groups[groupIds[i]];
	        if (group.options.style === 'line' && group.options.shaded.enabled == true) {
	          var dataset = groupsData[groupIds[i]];
	          if (dataset == null || dataset.length == 0) {
	            continue;
	          }
	          if (!paths.hasOwnProperty(groupIds[i])) {
	            paths[groupIds[i]] = Line.calcPath(dataset, group);
	          }
	          if (group.options.shaded.orientation === "group") {
	            var subGroupId = group.options.shaded.groupId;
	            if (_indexOfInstanceProperty(groupIds).call(groupIds, subGroupId) === -1) {
	              console.log(group.id + ": Unknown shading group target given:" + subGroupId);
	              continue;
	            }
	            if (!paths.hasOwnProperty(subGroupId)) {
	              paths[subGroupId] = Line.calcPath(groupsData[subGroupId], this.groups[subGroupId]);
	            }
	            Line.drawShading(paths[groupIds[i]], group, paths[subGroupId], this.framework);
	          } else {
	            Line.drawShading(paths[groupIds[i]], group, undefined, this.framework);
	          }
	        }
	      }

	      
	      Bargraph.draw(groupIds, groupsData, this.framework);
	      for (i = 0; i < groupIds.length; i++) {
	        group = this.groups[groupIds[i]];
	        if (groupsData[groupIds[i]].length > 0) {
	          switch (group.options.style) {
	            case "line":
	              if (!paths.hasOwnProperty(groupIds[i])) {
	                paths[groupIds[i]] = Line.calcPath(groupsData[groupIds[i]], group);
	              }
	              Line.draw(paths[groupIds[i]], group, this.framework);
	            
	            case "point":
	            
	            case "points":
	              if (group.options.style == "point" || group.options.style == "points" || group.options.drawPoints.enabled == true) {
	                Points.draw(groupsData[groupIds[i]], group, this.framework);
	              }
	              break;
	            
	          }
	        }
	      }
	    }
	  }

	  
	  cleanupElements(this.svgElements);
	  return false;
	};
	LineGraph.prototype._stack = function (data, subData) {
	  var index, dx, dy, subPrevPoint, subNextPoint;
	  index = 0;
	  
	  for (var j = 0; j < data.length; j++) {
	    subPrevPoint = undefined;
	    subNextPoint = undefined;
	    
	    for (var k = index; k < subData.length; k++) {
	      
	      if (subData[k].x === data[j].x) {
	        subPrevPoint = subData[k];
	        subNextPoint = subData[k];
	        index = k;
	        break;
	      } else if (subData[k].x > data[j].x) {
	        
	        subNextPoint = subData[k];
	        if (k == 0) {
	          subPrevPoint = subNextPoint;
	        } else {
	          subPrevPoint = subData[k - 1];
	        }
	        index = k;
	        break;
	      }
	    }
	    
	    if (subNextPoint === undefined) {
	      subPrevPoint = subData[subData.length - 1];
	      subNextPoint = subData[subData.length - 1];
	    }
	    
	    dx = subNextPoint.x - subPrevPoint.x;
	    dy = subNextPoint.y - subPrevPoint.y;
	    if (dx == 0) {
	      data[j].y = data[j].orginalY + subNextPoint.y;
	    } else {
	      data[j].y = data[j].orginalY + dy / dx * (data[j].x - subPrevPoint.x) + subPrevPoint.y; 
	    }
	  }
	};

	
	LineGraph.prototype._getRelevantData = function (groupIds, groupsData, minDate, maxDate) {
	  var group, i, j, item;
	  if (groupIds.length > 0) {
	    for (i = 0; i < groupIds.length; i++) {
	      group = this.groups[groupIds[i]];
	      var itemsData = group.getItems();
	      
	      if (_sortInstanceProperty(group.options) == true) {
	        var dateComparator = function dateComparator(a, b) {
	          return a.getTime() == b.getTime() ? 0 : a < b ? -1 : 1;
	        };
	        var first = Math.max(0, availableUtils.binarySearchValue(itemsData, minDate, 'x', 'before', dateComparator));
	        var last = Math.min(itemsData.length, availableUtils.binarySearchValue(itemsData, maxDate, 'x', 'after', dateComparator) + 1);
	        if (last <= 0) {
	          last = itemsData.length;
	        }
	        var dataContainer = new Array(last - first);
	        for (j = first; j < last; j++) {
	          item = group.itemsData[j];
	          dataContainer[j - first] = item;
	        }
	        groupsData[groupIds[i]] = dataContainer;
	      } else {
	        
	        groupsData[groupIds[i]] = group.itemsData;
	      }
	    }
	  }
	};

	
	LineGraph.prototype._applySampling = function (groupIds, groupsData) {
	  var group;
	  if (groupIds.length > 0) {
	    for (var i = 0; i < groupIds.length; i++) {
	      group = this.groups[groupIds[i]];
	      if (group.options.sampling == true) {
	        var dataContainer = groupsData[groupIds[i]];
	        if (dataContainer.length > 0) {
	          var increment = 1;
	          var amountOfPoints = dataContainer.length;

	          
	          
	          
	          var xDistance = this.body.util.toGlobalScreen(dataContainer[dataContainer.length - 1].x) - this.body.util.toGlobalScreen(dataContainer[0].x);
	          var pointsPerPixel = amountOfPoints / xDistance;
	          increment = Math.min(Math.ceil(0.2 * amountOfPoints), Math.max(1, Math.round(pointsPerPixel)));
	          var sampledData = new Array(amountOfPoints);
	          for (var j = 0; j < amountOfPoints; j += increment) {
	            var idx = Math.round(j / increment);
	            sampledData[idx] = dataContainer[j];
	          }
	          groupsData[groupIds[i]] = _spliceInstanceProperty(sampledData).call(sampledData, 0, Math.round(amountOfPoints / increment));
	        }
	      }
	    }
	  }
	};

	
	LineGraph.prototype._getYRanges = function (groupIds, groupsData, groupRanges) {
	  var groupData, group, i;
	  var combinedDataLeft = [];
	  var combinedDataRight = [];
	  var options;
	  if (groupIds.length > 0) {
	    for (i = 0; i < groupIds.length; i++) {
	      groupData = groupsData[groupIds[i]];
	      options = this.groups[groupIds[i]].options;
	      if (groupData.length > 0) {
	        group = this.groups[groupIds[i]];
	        
	        if (options.stack === true && options.style === 'bar') {
	          if (options.yAxisOrientation === 'left') {
	            combinedDataLeft = _concatInstanceProperty(combinedDataLeft).call(combinedDataLeft, groupData);
	          } else {
	            combinedDataRight = _concatInstanceProperty(combinedDataRight).call(combinedDataRight, groupData);
	          }
	        } else {
	          groupRanges[groupIds[i]] = group.getYRange(groupData, groupIds[i]);
	        }
	      }
	    }

	    
	    Bargraph.getStackedYRange(combinedDataLeft, groupRanges, groupIds, '__barStackLeft', 'left');
	    Bargraph.getStackedYRange(combinedDataRight, groupRanges, groupIds, '__barStackRight', 'right');
	  }
	};

	
	LineGraph.prototype._updateYAxis = function (groupIds, groupRanges) {
	  var resized = false;
	  var yAxisLeftUsed = false;
	  var yAxisRightUsed = false;
	  var minLeft = 1e9,
	    minRight = 1e9,
	    maxLeft = -1e9,
	    maxRight = -1e9,
	    minVal,
	    maxVal;
	  
	  if (groupIds.length > 0) {
	    
	    for (var i = 0; i < groupIds.length; i++) {
	      var group = this.groups[groupIds[i]];
	      if (group && group.options.yAxisOrientation != 'right') {
	        yAxisLeftUsed = true;
	        minLeft = 1e9;
	        maxLeft = -1e9;
	      } else if (group && group.options.yAxisOrientation) {
	        yAxisRightUsed = true;
	        minRight = 1e9;
	        maxRight = -1e9;
	      }
	    }

	    
	    for (i = 0; i < groupIds.length; i++) {
	      if (groupRanges.hasOwnProperty(groupIds[i])) {
	        if (groupRanges[groupIds[i]].ignore !== true) {
	          minVal = groupRanges[groupIds[i]].min;
	          maxVal = groupRanges[groupIds[i]].max;
	          if (groupRanges[groupIds[i]].yAxisOrientation != 'right') {
	            yAxisLeftUsed = true;
	            minLeft = minLeft > minVal ? minVal : minLeft;
	            maxLeft = maxLeft < maxVal ? maxVal : maxLeft;
	          } else {
	            yAxisRightUsed = true;
	            minRight = minRight > minVal ? minVal : minRight;
	            maxRight = maxRight < maxVal ? maxVal : maxRight;
	          }
	        }
	      }
	    }
	    if (yAxisLeftUsed == true) {
	      this.yAxisLeft.setRange(minLeft, maxLeft);
	    }
	    if (yAxisRightUsed == true) {
	      this.yAxisRight.setRange(minRight, maxRight);
	    }
	  }
	  resized = this._toggleAxisVisiblity(yAxisLeftUsed, this.yAxisLeft) || resized;
	  resized = this._toggleAxisVisiblity(yAxisRightUsed, this.yAxisRight) || resized;
	  if (yAxisRightUsed == true && yAxisLeftUsed == true) {
	    this.yAxisLeft.drawIcons = true;
	    this.yAxisRight.drawIcons = true;
	  } else {
	    this.yAxisLeft.drawIcons = false;
	    this.yAxisRight.drawIcons = false;
	  }
	  this.yAxisRight.master = !yAxisLeftUsed;
	  this.yAxisRight.masterAxis = this.yAxisLeft;
	  if (this.yAxisRight.master == false) {
	    if (yAxisRightUsed == true) {
	      this.yAxisLeft.lineOffset = this.yAxisRight.width;
	    } else {
	      this.yAxisLeft.lineOffset = 0;
	    }
	    resized = this.yAxisLeft.redraw() || resized;
	    resized = this.yAxisRight.redraw() || resized;
	  } else {
	    resized = this.yAxisRight.redraw() || resized;
	  }

	  
	  var tempGroups = ['__barStackLeft', '__barStackRight', '__lineStackLeft', '__lineStackRight'];
	  for (i = 0; i < tempGroups.length; i++) {
	    if (_indexOfInstanceProperty(groupIds).call(groupIds, tempGroups[i]) != -1) {
	      _spliceInstanceProperty(groupIds).call(groupIds, _indexOfInstanceProperty(groupIds).call(groupIds, tempGroups[i]), 1);
	    }
	  }
	  return resized;
	};

	
	LineGraph.prototype._toggleAxisVisiblity = function (axisUsed, axis) {
	  var changed = false;
	  if (axisUsed == false) {
	    if (axis.dom.frame.parentNode && axis.hidden == false) {
	      axis.hide();
	      changed = true;
	    }
	  } else {
	    if (!axis.dom.frame.parentNode && axis.hidden == true) {
	      axis.show();
	      changed = true;
	    }
	  }
	  return changed;
	};

	
	LineGraph.prototype._convertXcoordinates = function (datapoints) {
	  var toScreen = this.body.util.toScreen;
	  for (var i = 0; i < datapoints.length; i++) {
	    datapoints[i].screen_x = toScreen(datapoints[i].x) + this.props.width;
	    datapoints[i].screen_y = datapoints[i].y; 
	    if (datapoints[i].end != undefined) {
	      datapoints[i].screen_end = toScreen(datapoints[i].end) + this.props.width;
	    } else {
	      datapoints[i].screen_end = undefined;
	    }
	  }
	};

	
	LineGraph.prototype._convertYcoordinates = function (datapoints, group) {
	  var axis = this.yAxisLeft;
	  var svgHeight = Number(this.svg.style.height.replace('px', ''));
	  if (group.options.yAxisOrientation == 'right') {
	    axis = this.yAxisRight;
	  }
	  for (var i = 0; i < datapoints.length; i++) {
	    datapoints[i].screen_y = Math.round(axis.convertValue(datapoints[i].y));
	  }
	  group.setZeroPosition(Math.min(svgHeight, axis.convertValue(0)));
	};

	
	var string = 'string';
	var bool = 'boolean';
	var number = 'number';
	var array = 'array';
	var date = 'date';
	var object = 'object'; 
	var dom = 'dom';
	var moment = 'moment';
	var any = 'any';
	var allOptions = {
	  configure: {
	    enabled: {
	      'boolean': bool
	    },
	    filter: {
	      'boolean': bool,
	      'function': 'function'
	    },
	    container: {
	      dom: dom
	    },
	    __type__: {
	      object: object,
	      'boolean': bool,
	      'function': 'function'
	    }
	  },
	  
	  alignCurrentTime: {
	    string: string,
	    'undefined': 'undefined'
	  },
	  yAxisOrientation: {
	    string: ['left', 'right']
	  },
	  defaultGroup: {
	    string: string
	  },
	  sort: {
	    'boolean': bool
	  },
	  sampling: {
	    'boolean': bool
	  },
	  stack: {
	    'boolean': bool
	  },
	  graphHeight: {
	    string: string,
	    number: number
	  },
	  shaded: {
	    enabled: {
	      'boolean': bool
	    },
	    orientation: {
	      string: ['bottom', 'top', 'zero', 'group']
	    },
	    
	    groupId: {
	      object: object
	    },
	    __type__: {
	      'boolean': bool,
	      object: object
	    }
	  },
	  style: {
	    string: ['line', 'bar', 'points']
	  },
	  
	  barChart: {
	    width: {
	      number: number
	    },
	    minWidth: {
	      number: number
	    },
	    sideBySide: {
	      'boolean': bool
	    },
	    align: {
	      string: ['left', 'center', 'right']
	    },
	    __type__: {
	      object: object
	    }
	  },
	  interpolation: {
	    enabled: {
	      'boolean': bool
	    },
	    parametrization: {
	      string: ['centripetal', 'chordal', 'uniform']
	    },
	    
	    alpha: {
	      number: number
	    },
	    __type__: {
	      object: object,
	      'boolean': bool
	    }
	  },
	  drawPoints: {
	    enabled: {
	      'boolean': bool
	    },
	    onRender: {
	      'function': 'function'
	    },
	    size: {
	      number: number
	    },
	    style: {
	      string: ['square', 'circle']
	    },
	    
	    __type__: {
	      object: object,
	      'boolean': bool,
	      'function': 'function'
	    }
	  },
	  dataAxis: {
	    showMinorLabels: {
	      'boolean': bool
	    },
	    showMajorLabels: {
	      'boolean': bool
	    },
	    showWeekScale: {
	      'boolean': bool
	    },
	    icons: {
	      'boolean': bool
	    },
	    width: {
	      string: string,
	      number: number
	    },
	    visible: {
	      'boolean': bool
	    },
	    alignZeros: {
	      'boolean': bool
	    },
	    left: {
	      range: {
	        min: {
	          number: number,
	          'undefined': 'undefined'
	        },
	        max: {
	          number: number,
	          'undefined': 'undefined'
	        },
	        __type__: {
	          object: object
	        }
	      },
	      format: {
	        'function': 'function'
	      },
	      title: {
	        text: {
	          string: string,
	          number: number,
	          'undefined': 'undefined'
	        },
	        style: {
	          string: string,
	          'undefined': 'undefined'
	        },
	        __type__: {
	          object: object
	        }
	      },
	      __type__: {
	        object: object
	      }
	    },
	    right: {
	      range: {
	        min: {
	          number: number,
	          'undefined': 'undefined'
	        },
	        max: {
	          number: number,
	          'undefined': 'undefined'
	        },
	        __type__: {
	          object: object
	        }
	      },
	      format: {
	        'function': 'function'
	      },
	      title: {
	        text: {
	          string: string,
	          number: number,
	          'undefined': 'undefined'
	        },
	        style: {
	          string: string,
	          'undefined': 'undefined'
	        },
	        __type__: {
	          object: object
	        }
	      },
	      __type__: {
	        object: object
	      }
	    },
	    __type__: {
	      object: object
	    }
	  },
	  legend: {
	    enabled: {
	      'boolean': bool
	    },
	    icons: {
	      'boolean': bool
	    },
	    left: {
	      visible: {
	        'boolean': bool
	      },
	      position: {
	        string: ['top-right', 'bottom-right', 'top-left', 'bottom-left']
	      },
	      __type__: {
	        object: object
	      }
	    },
	    right: {
	      visible: {
	        'boolean': bool
	      },
	      position: {
	        string: ['top-right', 'bottom-right', 'top-left', 'bottom-left']
	      },
	      __type__: {
	        object: object
	      }
	    },
	    __type__: {
	      object: object,
	      'boolean': bool
	    }
	  },
	  groups: {
	    visibility: {
	      any: any
	    },
	    __type__: {
	      object: object
	    }
	  },
	  autoResize: {
	    'boolean': bool
	  },
	  throttleRedraw: {
	    number: number
	  },
	  
	  clickToUse: {
	    'boolean': bool
	  },
	  end: {
	    number: number,
	    date: date,
	    string: string,
	    moment: moment
	  },
	  format: {
	    minorLabels: {
	      millisecond: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      second: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      minute: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      hour: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      weekday: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      day: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      week: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      month: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      quarter: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      year: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      __type__: {
	        object: object
	      }
	    },
	    majorLabels: {
	      millisecond: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      second: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      minute: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      hour: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      weekday: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      day: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      week: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      month: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      quarter: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      year: {
	        string: string,
	        'undefined': 'undefined'
	      },
	      __type__: {
	        object: object
	      }
	    },
	    __type__: {
	      object: object
	    }
	  },
	  moment: {
	    'function': 'function'
	  },
	  height: {
	    string: string,
	    number: number
	  },
	  hiddenDates: {
	    start: {
	      date: date,
	      number: number,
	      string: string,
	      moment: moment
	    },
	    end: {
	      date: date,
	      number: number,
	      string: string,
	      moment: moment
	    },
	    repeat: {
	      string: string
	    },
	    __type__: {
	      object: object,
	      array: array
	    }
	  },
	  locale: {
	    string: string
	  },
	  locales: {
	    __any__: {
	      any: any
	    },
	    __type__: {
	      object: object
	    }
	  },
	  max: {
	    date: date,
	    number: number,
	    string: string,
	    moment: moment
	  },
	  maxHeight: {
	    number: number,
	    string: string
	  },
	  maxMinorChars: {
	    number: number
	  },
	  min: {
	    date: date,
	    number: number,
	    string: string,
	    moment: moment
	  },
	  minHeight: {
	    number: number,
	    string: string
	  },
	  moveable: {
	    'boolean': bool
	  },
	  multiselect: {
	    'boolean': bool
	  },
	  orientation: {
	    string: string
	  },
	  showCurrentTime: {
	    'boolean': bool
	  },
	  showMajorLabels: {
	    'boolean': bool
	  },
	  showMinorLabels: {
	    'boolean': bool
	  },
	  showWeekScale: {
	    'boolean': bool
	  },
	  snap: {
	    'function': 'function',
	    'null': 'null'
	  },
	  start: {
	    date: date,
	    number: number,
	    string: string,
	    moment: moment
	  },
	  timeAxis: {
	    scale: {
	      string: string,
	      'undefined': 'undefined'
	    },
	    step: {
	      number: number,
	      'undefined': 'undefined'
	    },
	    __type__: {
	      object: object
	    }
	  },
	  width: {
	    string: string,
	    number: number
	  },
	  zoomable: {
	    'boolean': bool
	  },
	  zoomKey: {
	    string: ['ctrlKey', 'altKey', 'metaKey', '']
	  },
	  zoomMax: {
	    number: number
	  },
	  zoomMin: {
	    number: number
	  },
	  zIndex: {
	    number: number
	  },
	  __type__: {
	    object: object
	  }
	};
	var configureOptions = {
	  global: {
	    alignCurrentTime: ['none', 'year', 'month', 'quarter', 'week', 'isoWeek', 'day', 'date', 'hour', 'minute', 'second'],
	    
	    sort: true,
	    sampling: true,
	    stack: false,
	    shaded: {
	      enabled: false,
	      orientation: ['zero', 'top', 'bottom', 'group'] 
	    },

	    style: ['line', 'bar', 'points'],
	    
	    barChart: {
	      width: [50, 5, 100, 5],
	      minWidth: [50, 5, 100, 5],
	      sideBySide: false,
	      align: ['left', 'center', 'right'] 
	    },

	    interpolation: {
	      enabled: true,
	      parametrization: ['centripetal', 'chordal', 'uniform'] 
	    },

	    drawPoints: {
	      enabled: true,
	      size: [6, 2, 30, 1],
	      style: ['square', 'circle'] 
	    },

	    dataAxis: {
	      showMinorLabels: true,
	      showMajorLabels: true,
	      showWeekScale: false,
	      icons: false,
	      width: [40, 0, 200, 1],
	      visible: true,
	      alignZeros: true,
	      left: {
	        
	        
	        title: {
	          text: '',
	          style: ''
	        }
	      },
	      right: {
	        
	        
	        title: {
	          text: '',
	          style: ''
	        }
	      }
	    },
	    legend: {
	      enabled: false,
	      icons: true,
	      left: {
	        visible: true,
	        position: ['top-right', 'bottom-right', 'top-left', 'bottom-left'] 
	      },

	      right: {
	        visible: true,
	        position: ['top-right', 'bottom-right', 'top-left', 'bottom-left'] 
	      }
	    },

	    autoResize: true,
	    clickToUse: false,
	    end: '',
	    format: {
	      minorLabels: {
	        millisecond: 'SSS',
	        second: 's',
	        minute: 'HH:mm',
	        hour: 'HH:mm',
	        weekday: 'ddd D',
	        day: 'D',
	        week: 'w',
	        month: 'MMM',
	        quarter: '[Q]Q',
	        year: 'YYYY'
	      },
	      majorLabels: {
	        millisecond: 'HH:mm:ss',
	        second: 'D MMMM HH:mm',
	        minute: 'ddd D MMMM',
	        hour: 'ddd D MMMM',
	        weekday: 'MMMM YYYY',
	        day: 'MMMM YYYY',
	        week: 'MMMM YYYY',
	        month: 'YYYY',
	        quarter: 'YYYY',
	        year: ''
	      }
	    },
	    height: '',
	    locale: '',
	    max: '',
	    maxHeight: '',
	    maxMinorChars: [7, 0, 20, 1],
	    min: '',
	    minHeight: '',
	    moveable: true,
	    orientation: ['both', 'bottom', 'top'],
	    showCurrentTime: false,
	    showMajorLabels: true,
	    showMinorLabels: true,
	    showWeekScale: false,
	    start: '',
	    width: '100%',
	    zoomable: true,
	    zoomKey: ['ctrlKey', 'altKey', 'metaKey', ''],
	    zoomMax: [315360000000000, 10, 315360000000000, 1],
	    zoomMin: [10, 10, 315360000000000, 1],
	    zIndex: 0
	  }
	};

	
	function Graph2d(container, items, groups, options) {
	  var _context, _context2, _context3, _context4, _context5, _context6, _context7;
	  
	  if (!(_Array$isArray$1(groups) || isDataViewLike(groups)) && groups instanceof Object) {
	    var forthArgument = options;
	    options = groups;
	    groups = forthArgument;
	  }

	  
	  
	  if (options && options.throttleRedraw) {
	    console.warn("Graph2d option \"throttleRedraw\" is DEPRICATED and no longer supported. It will be removed in the next MAJOR release.");
	  }
	  var me = this;
	  this.defaultOptions = {
	    start: null,
	    end: null,
	    autoResize: true,
	    orientation: {
	      axis: 'bottom',
	      
	      item: 'bottom' 
	    },

	    moment: moment$2,
	    width: null,
	    height: null,
	    maxHeight: null,
	    minHeight: null
	  };
	  this.options = availableUtils.deepExtend({}, this.defaultOptions);

	  
	  this._create(container);

	  
	  this.components = [];
	  this.body = {
	    dom: this.dom,
	    domProps: this.props,
	    emitter: {
	      on: _bindInstanceProperty(_context = this.on).call(_context, this),
	      off: _bindInstanceProperty(_context2 = this.off).call(_context2, this),
	      emit: _bindInstanceProperty(_context3 = this.emit).call(_context3, this)
	    },
	    hiddenDates: [],
	    util: {
	      getScale: function getScale() {
	        return me.timeAxis.step.scale;
	      },
	      getStep: function getStep() {
	        return me.timeAxis.step.step;
	      },
	      toScreen: _bindInstanceProperty(_context4 = me._toScreen).call(_context4, me),
	      toGlobalScreen: _bindInstanceProperty(_context5 = me._toGlobalScreen).call(_context5, me),
	      
	      toTime: _bindInstanceProperty(_context6 = me._toTime).call(_context6, me),
	      toGlobalTime: _bindInstanceProperty(_context7 = me._toGlobalTime).call(_context7, me)
	    }
	  };

	  
	  this.range = new Range(this.body);
	  this.components.push(this.range);
	  this.body.range = this.range;

	  
	  this.timeAxis = new TimeAxis(this.body);
	  this.components.push(this.timeAxis);
	  

	  
	  this.currentTime = new CurrentTime(this.body);
	  this.components.push(this.currentTime);

	  
	  this.linegraph = new LineGraph(this.body);
	  this.components.push(this.linegraph);
	  this.itemsData = null; 
	  this.groupsData = null; 

	  this.on('tap', function (event) {
	    me.emit('click', me.getEventProperties(event));
	  });
	  this.on('doubletap', function (event) {
	    me.emit('doubleClick', me.getEventProperties(event));
	  });
	  this.dom.root.oncontextmenu = function (event) {
	    me.emit('contextmenu', me.getEventProperties(event));
	  };

	  
	  this.initialFitDone = false;
	  this.on('changed', function () {
	    if (me.itemsData == null) return;
	    if (!me.initialFitDone && !me.options.rollingMode) {
	      me.initialFitDone = true;
	      if (me.options.start != undefined || me.options.end != undefined) {
	        if (me.options.start == undefined || me.options.end == undefined) {
	          var range = me.getItemRange();
	        }
	        var start = me.options.start != undefined ? me.options.start : range.min;
	        var end = me.options.end != undefined ? me.options.end : range.max;
	        me.setWindow(start, end, {
	          animation: false
	        });
	      } else {
	        me.fit({
	          animation: false
	        });
	      }
	    }
	    if (!me.initialDrawDone && (me.initialRangeChangeDone || !me.options.start && !me.options.end || me.options.rollingMode)) {
	      me.initialDrawDone = true;
	      me.dom.root.style.visibility = 'visible';
	      me.dom.loadingScreen.parentNode.removeChild(me.dom.loadingScreen);
	      if (me.options.onInitialDrawComplete) {
	        _setTimeout(function () {
	          return me.options.onInitialDrawComplete();
	        }, 0);
	      }
	    }
	  });

	  
	  if (options) {
	    this.setOptions(options);
	  }

	  
	  if (groups) {
	    this.setGroups(groups);
	  }

	  
	  if (items) {
	    this.setItems(items);
	  }

	  
	  this._redraw();
	}

	
	Graph2d.prototype = new Core();
	Graph2d.prototype.setOptions = function (options) {
	  
	  var errorFound = Validator.validate(options, allOptions);
	  if (errorFound === true) {
	    console.log('%cErrors have been found in the supplied options object.', printStyle);
	  }
	  Core.prototype.setOptions.call(this, options);
	};

	
	Graph2d.prototype.setItems = function (items) {
	  var initialLoad = this.itemsData == null;

	  
	  var newDataSet;
	  if (!items) {
	    newDataSet = null;
	  } else if (isDataViewLike(items)) {
	    newDataSet = typeCoerceDataSet(items);
	  } else {
	    
	    newDataSet = typeCoerceDataSet(new esnext.DataSet(items));
	  }

	  
	  if (this.itemsData) {
	    
	    this.itemsData.dispose();
	  }
	  this.itemsData = newDataSet;
	  this.linegraph && this.linegraph.setItems(newDataSet != null ? newDataSet.rawDS : null);
	  if (initialLoad) {
	    if (this.options.start != undefined || this.options.end != undefined) {
	      var start = this.options.start != undefined ? this.options.start : null;
	      var end = this.options.end != undefined ? this.options.end : null;
	      this.setWindow(start, end, {
	        animation: false
	      });
	    } else {
	      this.fit({
	        animation: false
	      });
	    }
	  }
	};

	
	Graph2d.prototype.setGroups = function (groups) {
	  
	  var newDataSet;
	  if (!groups) {
	    newDataSet = null;
	  } else if (isDataViewLike(groups)) {
	    newDataSet = groups;
	  } else {
	    
	    newDataSet = new esnext.DataSet(groups);
	  }
	  this.groupsData = newDataSet;
	  this.linegraph.setGroups(newDataSet);
	};

	
	Graph2d.prototype.getLegend = function (groupId, width, height) {
	  if (width === undefined) {
	    width = 15;
	  }
	  if (height === undefined) {
	    height = 15;
	  }
	  if (this.linegraph.groups[groupId] !== undefined) {
	    return this.linegraph.groups[groupId].getLegend(width, height);
	  } else {
	    return "cannot find group:'" + groupId + "'";
	  }
	};

	
	Graph2d.prototype.isGroupVisible = function (groupId) {
	  if (this.linegraph.groups[groupId] !== undefined) {
	    return this.linegraph.groups[groupId].visible && (this.linegraph.options.groups.visibility[groupId] === undefined || this.linegraph.options.groups.visibility[groupId] == true);
	  } else {
	    return false;
	  }
	};

	
	Graph2d.prototype.getDataRange = function () {
	  var min = null;
	  var max = null;

	  
	  for (var groupId in this.linegraph.groups) {
	    if (this.linegraph.groups.hasOwnProperty(groupId)) {
	      if (this.linegraph.groups[groupId].visible == true) {
	        for (var i = 0; i < this.linegraph.groups[groupId].itemsData.length; i++) {
	          var item = this.linegraph.groups[groupId].itemsData[i];
	          var value = availableUtils.convert(item.x, 'Date').valueOf();
	          min = min == null ? value : min > value ? value : min;
	          max = max == null ? value : max < value ? value : max;
	        }
	      }
	    }
	  }
	  return {
	    min: min != null ? new Date(min) : null,
	    max: max != null ? new Date(max) : null
	  };
	};

	
	Graph2d.prototype.getEventProperties = function (event) {
	  var clientX = event.center ? event.center.x : event.clientX;
	  var clientY = event.center ? event.center.y : event.clientY;
	  var x = clientX - availableUtils.getAbsoluteLeft(this.dom.centerContainer);
	  var y = clientY - availableUtils.getAbsoluteTop(this.dom.centerContainer);
	  var time = this._toTime(x);
	  var customTime = CustomTime.customTimeFromTarget(event);
	  var element = availableUtils.getTarget(event);
	  var what = null;
	  if (availableUtils.hasParent(element, this.timeAxis.dom.foreground)) {
	    what = 'axis';
	  } else if (this.timeAxis2 && availableUtils.hasParent(element, this.timeAxis2.dom.foreground)) {
	    what = 'axis';
	  } else if (availableUtils.hasParent(element, this.linegraph.yAxisLeft.dom.frame)) {
	    what = 'data-axis';
	  } else if (availableUtils.hasParent(element, this.linegraph.yAxisRight.dom.frame)) {
	    what = 'data-axis';
	  } else if (availableUtils.hasParent(element, this.linegraph.legendLeft.dom.frame)) {
	    what = 'legend';
	  } else if (availableUtils.hasParent(element, this.linegraph.legendRight.dom.frame)) {
	    what = 'legend';
	  } else if (customTime != null) {
	    what = 'custom-time';
	  } else if (availableUtils.hasParent(element, this.currentTime.bar)) {
	    what = 'current-time';
	  } else if (availableUtils.hasParent(element, this.dom.center)) {
	    what = 'background';
	  }
	  var value = [];
	  var yAxisLeft = this.linegraph.yAxisLeft;
	  var yAxisRight = this.linegraph.yAxisRight;
	  if (!yAxisLeft.hidden && this.itemsData.length > 0) {
	    value.push(yAxisLeft.screenToValue(y));
	  }
	  if (!yAxisRight.hidden && this.itemsData.length > 0) {
	    value.push(yAxisRight.screenToValue(y));
	  }
	  return {
	    event: event,
	    customTime: customTime ? customTime.options.id : null,
	    what: what,
	    pageX: event.srcEvent ? event.srcEvent.pageX : event.pageX,
	    pageY: event.srcEvent ? event.srcEvent.pageY : event.pageY,
	    x: x,
	    y: y,
	    time: time,
	    value: value
	  };
	};

	
	Graph2d.prototype._createConfigurator = function () {
	  return new Configurator(this, this.dom.container, configureOptions);
	};

	
	var defaultLanguage = getNavigatorLanguage();
	moment$3.locale(defaultLanguage);
	var timeline = {
	  Core: Core,
	  DateUtil: DateUtil,
	  Range: Range,
	  stack: stack$1,
	  TimeStep: TimeStep,
	  components: {
	    items: {
	      Item: Item,
	      BackgroundItem: BackgroundItem,
	      BoxItem: BoxItem,
	      ClusterItem: ClusterItem,
	      PointItem: PointItem,
	      RangeItem: RangeItem
	    },
	    BackgroundGroup: BackgroundGroup,
	    Component: Component,
	    CurrentTime: CurrentTime,
	    CustomTime: CustomTime,
	    DataAxis: DataAxis,
	    DataScale: DataScale,
	    GraphGroup: GraphGroup,
	    Group: Group,
	    ItemSet: ItemSet,
	    Legend: Legend,
	    LineGraph: LineGraph,
	    TimeAxis: TimeAxis
	  }
	};

	exports.Graph2d = Graph2d;
	exports.Timeline = Timeline;
	exports.timeline = timeline;

}));

