<?php




namespace LightnCandy;


class Flags
{
    
    const FLAG_ERROR_LOG = 1;
    const FLAG_ERROR_EXCEPTION = 2;

    
    const FLAG_JSTRUE = 8;
    const FLAG_JSOBJECT = 16;
    const FLAG_JSLENGTH = 33554432;

    
    const FLAG_THIS = 32;
    const FLAG_PARENT = 128;
    const FLAG_HBESCAPE = 256;
    const FLAG_ADVARNAME = 512;
    const FLAG_NAMEDARG = 2048;
    const FLAG_SPVARS = 4096;
    const FLAG_PREVENTINDENT = 131072;
    const FLAG_SLASH = 8388608;
    const FLAG_ELSE = 16777216;
    const FLAG_RAWBLOCK = 134217728;
    const FLAG_HANDLEBARSLAMBDA = 268435456;
    const FLAG_PARTIALNEWCONTEXT = 536870912;
    const FLAG_IGNORESTANDALONE = 1073741824;
    const FLAG_STRINGPARAMS = 2147483648;
    const FLAG_KNOWNHELPERSONLY = 4294967296;

    
    const FLAG_STANDALONEPHP = 4;
    const FLAG_EXTHELPER = 8192;
    const FLAG_ECHO = 16384;
    const FLAG_PROPERTY = 32768;
    const FLAG_METHOD = 65536;
    const FLAG_RUNTIMEPARTIAL = 1048576;
    const FLAG_NOESCAPE = 67108864;

    
    const FLAG_MUSTACHELOOKUP = 262144;
    const FLAG_ERROR_SKIPPARTIAL = 4194304;
    const FLAG_MUSTACHELAMBDA = 2097152;
    const FLAG_NOHBHELPERS = 64;
    const FLAG_MUSTACHESECTION = 8589934592;

    
    const FLAG_RENDER_DEBUG = 524288;

    
    const FLAG_BESTPERFORMANCE = 16388; 
    const FLAG_JS = 33554456; 
    const FLAG_INSTANCE = 98304; 
    const FLAG_MUSTACHE = 8597536856; 
    const FLAG_HANDLEBARS = 159390624; 
    const FLAG_HANDLEBARSJS = 192945080; 
    const FLAG_HANDLEBARSJS_FULL = 429235128; 
}
