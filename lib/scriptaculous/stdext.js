// Stdext: useful extensions for standard JavaScript types
// Copyright (c) 2005, Michael Schuerig, michael@schuerig.de
//
// License
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or (at your option) any later version.
// See http://www.gnu.org/copyleft/lesser.html
//

var StdExt = {
  Version: '0.2.0',
  isKhtml: /Konqueror|Safari|KHTML/i.test(navigator.userAgent)
};


//----- Array


if (!Array.prototype.difference) {
  Array.prototype.difference = function(minusArray, compareFunc) {
    if (!minusArray) {
      return this;
    }
    if (!compareFunc) {
      compareFunc = Function.equality;
    }
    var diff = new Array();
    var len = this.length;
    for (var i = 0; i < len; i++) {
      var el = this[i];
      if (!minusArray.some(function(other) { return compareFunc(el, other); })) {
        diff.push(el);
      }
    }
    return diff;
  };
}

if (!Array.prototype.equals) {
  Array.prototype.equals = function(other) {
    if (!other) {
      return false;
    }
    var len = this.length;
    if (len != other.length) {
      return false;
    }
    for (var i = 0; i < len; i++) {
      if (this[i] != other[i]) {
        return false;
      }
    }
    return true;
  };
}


Array.flatten = function(array, excludeUndefined) {
  if (excludeUndefined === undefined) {
    excludeUndefined = false;
  }
  var result = [];
  var len = array.length;
  for (var i = 0; i < len; i++) {
    var el = array[i];
    if (el instanceof Array) {
      var flat = el.flatten(excludeUndefined);
      result = result.concat(flat);
    } else if (!excludeUndefined || el != undefined) {
      result.push(el);
    }
  }
  return result;
};

if (!Array.prototype.flatten) {
  Array.prototype.flatten = function(excludeUndefined) {
    return Array.flatten(this, excludeUndefined);
  }
}

if (!Array.prototype.moveElement) {
  Array.prototype.moveElement = function(fromPos, toPos) {
    if (fromPos < 0 || fromPos >= this.length) {
      throw new Error('Array.moveElement: fromPos must be < length and >= 0: ' + fromPos);
    }
    if (toPos < 0 || toPos >= this.length) {
      throw new Error('Array.moveElement: toPos must be < length and >= 0: ' + toPos);
    }
    if (fromPos === toPos) {
      return this;
    }
    var el = this.splice(fromPos, 1);
    if (el instanceof Array) {
      el = el[0];
    }
    this.splice(toPos, 0, el);
    return this;
  };
}

if (!Array.prototype.pushUnlessNull) {
  Array.prototype.pushUnlessNull = function(element) {
    if (element != undefined) {
      return this.push(element);
    } else {
      return this.length;
    }
  };
}

if (!Array.prototype.pushUnlessContains) {
  Array.prototype.pushUnlessContains = function(element) {
    if (!this.contains(element)) {
      return this.push(element);
    } else {
      return this.length;
    }
  };
}


// The array function below are from
// http://erik.eae.net/playground/arrayextras/
// Guards added to some, in order not to override existing functions


// Mozilla 1.8 has support for indexOf, lastIndexOf, forEach, filter, map, some, every
// http://developer-test.mozilla.org/docs/Core_JavaScript_1.5_Reference:Objects:Array:lastIndexOf
if (!Array.prototype.indexOf) {
  Array.prototype.indexOf = function (obj, fromIndex) {
    if (fromIndex == null) {
      fromIndex = 0;
    } else if (fromIndex < 0) {
      fromIndex = Math.max(0, this.length + fromIndex);
    }
    for (var i = fromIndex; i < this.length; i++) {
      if (this[i] === obj)
        return i;
    }
    return -1;
  };
}

// http://developer-test.mozilla.org/docs/Core_JavaScript_1.5_Reference:Objects:Array:lastIndexOf
if (!Array.prototype.lastIndexOf) {
  Array.prototype.lastIndexOf = function (obj, fromIndex) {
    if (fromIndex == null) {
      fromIndex = this.length - 1;
    } else if (fromIndex < 0) {
      fromIndex = Math.max(0, this.length + fromIndex);
    }
    for (var i = fromIndex; i >= 0; i--) {
      if (this[i] === obj)
        return i;
    }
    return -1;
  };
}


// http://developer-test.mozilla.org/docs/Core_JavaScript_1.5_Reference:Objects:Array:forEach
if (!Array.prototype.forEach) {
  Array.prototype.forEach = function (f, obj) {
    var l = this.length;  // must be fixed during loop... see docs
    for (var i = 0; i < l; i++) {
      f.call(obj, this[i], i, this);
    }
  };
}

// http://developer-test.mozilla.org/docs/Core_JavaScript_1.5_Reference:Objects:Array:filter
if (!Array.prototype.filter) {
  Array.prototype.filter = function (f, obj) {
    var l = this.length;  // must be fixed during loop... see docs
    var res = [];
    for (var i = 0; i < l; i++) {
      if (f.call(obj, this[i], i, this)) {
        res.push(this[i]);
      }
    }
    return res;
  };
}

// http://developer-test.mozilla.org/docs/Core_JavaScript_1.5_Reference:Objects:Array:map
if (!Array.prototype.map) {
  Array.prototype.map = function (f, obj) {
    var l = this.length;  // must be fixed during loop... see docs
    var res = [];
    for (var i = 0; i < l; i++) {
      res.push(f.call(obj, this[i], i, this));
    }
    return res;
  };
}

// http://developer-test.mozilla.org/docs/Core_JavaScript_1.5_Reference:Objects:Array:some
if (!Array.prototype.some) {
  Array.prototype.some = function (f, obj) {
    var l = this.length;  // must be fixed during loop... see docs
    for (var i = 0; i < l; i++) {
      if (f.call(obj, this[i], i, this)) {
        return true;
      }
    }
    return false;
  };
}

// http://developer-test.mozilla.org/docs/Core_JavaScript_1.5_Reference:Objects:Array:every
if (!Array.prototype.every) {
  Array.prototype.every = function (f, obj) {
    var l = this.length;  // must be fixed during loop... see docs
    for (var i = 0; i < l; i++) {
      if (!f.call(obj, this[i], i, this)) {
        return false;
      }
    }
    return true;
  };
}

if (!Array.prototype.contains) {
  Array.prototype.contains = function (obj) {
    return this.indexOf(obj) != -1;
  };
}

if (!Array.prototype.copy) {
  Array.prototype.copy = function (obj) {
    return this.concat();
  };
}

if (!Array.prototype.insertAt) {
  Array.prototype.insertAt = function (obj, i) {
    this.splice(i, 0, obj);
  };
}

if (!Array.prototype.insertBefore) {
  Array.prototype.insertBefore = function (obj, obj2) {
    var i = this.indexOf(obj2);
    if (i == -1)
      this.push(obj);
    else
      this.splice(i, 0, obj);
  };
}

if (!Array.prototype.removeAt) {
  Array.prototype.removeAt = function (i) {
    this.splice(i, 1);
  };
}

if (!Array.prototype.remove) {
  Array.prototype.remove = function (obj) {
    var i = this.indexOf(obj);
    if (i != -1)
     this.splice(i, 1);
  };
}


//----- Date

StdExt.native_date = Date;

// khtml Date copy constructor doesn't work for dates before 1970-01-01
if (StdExt.isKhtml && (new Date(new Date(1969,1,1)).getFullYear() == 1970)) {

  function Date() {
    if (arguments.length == 0) {
      return new StdExt.native_date();
    }
    if (arguments.length == 1) {
      d = arguments[0];
      if (d instanceof StdExt.native_date) {
        return new StdExt.native_date(
          d.getFullYear(), d.getMonth(), d.getDate(),
          d.getHours(), d.getMinutes(), d.getSeconds());
      }
    }
    return new StdExt.native_date(arguments[0], arguments[1],
      arguments[2], arguments[3], arguments[4], arguments[5]);
  }
  Date.prototype = StdExt.native_date.prototype
}


//----- Function

Function.equality = function(a, b) {
  return (a == b);
};

Function.identity = function(a, b) {
  return (a === b);
};

Function.trueFunc = function() {
  return true;
};

Function.sequence = function() {
  var _funcs = Array.flatten(arguments);
  return function() {
    var lastVal;
    var len = _funcs.length;
    for (var i = 0; i < len; i++) {
      lastVal = _funcs[i].apply(this, arguments);
    }
    return lastVal;
  };
};

Function.andCombiner = function(funcs) {
  var _funcs = Array.flatten(arguments);
  if (_funcs.length == 1) {
    return _funcs[0];
  } else if (_funcs.length > 1) {
    return function() {
      var len = _funcs.length;
      for (var i = 0; i < len; i++) {
        if (! _funcs[i].apply(this, arguments)) {
          return false;
        }
      }
      return true;
    };
  } else {
    return Function.trueFunc;
  }
};


//----- Object

Object.atPath = function(obj, path, separator) {
  if (!separator) {
    separator = '.';
  }
  path = path.split(separator);
  var cur = obj;
  var len = path.length;
  for (var i = 0; i < len && cur; i++) {
    cur = cur[path[i]];
  }
  return cur;
};

Object.addDefaults = function(obj, defaults, allowUndefinedValue) {
  var allowUndef = (allowUndefinedValue !== undefined) ?
    allowUndefinedValue : false;
  for (var key in defaults) {
    if (!obj.hasOwnProperty(key) || (!allowUndef && obj[key] == undefined)) {
      obj[key] = defaults[key];
    }
  }
};


Object.equals = function(obj1, obj2) {
  if (!obj1 && !obj2) {
    return true;
  }
  if (!obj1 || !obj2) {
    return false;
  }
  // shudder...
  for (var p1 in obj1) {
    if (obj1[p1] !== obj2[p1]) {
      return false;
    }
  }
  for (var p2 in obj2) {
    if (obj1[p2] !== obj2[p2]) {
      return false;
    }
  }
  return true;
};


//----- String

if (!String.prototype.startsWith) {
  String.prototype.startsWith = function(prefix) {
//    return (this.substr(0, prefix.length) == prefix);
    return (this.indexOf(prefix) === 0);
  };
}

if (!String.prototype.endsWith) {
  String.prototype.endsWith = function(suffix) {
    var startPos = this.length - suffix.length;
    if (startPos < 0) {
      return false;
    }
    return (this.lastIndexOf(suffix, startPos) == startPos);
  };
}

if (!String.prototype.withoutPrefix) {
  String.prototype.withoutPrefix = function(prefix) {
    if (this.startsWith(prefix)) {
      return this.substr(prefix.length);
    } else {
      return this;
    }
  };
}

if (!String.prototype.withoutSuffix) {
  String.prototype.withoutSuffix = function(suffix) {
    if (this.endsWith(suffix)) {
      return this.substr(0, this.length - suffix.length);
    } else {
      return this;
    }
  };
}

if (!String.prototype.strip) {
  String.prototype.strip = function() {
    return this.replace(/^\s*(.*?)\s*$/, "$1");
  };
}

if (!String.prototype.format) {
  // This function substitutes placeholders -- {0}, {1}, ... -- with
  // its arguments. The substituted value can be formatted by a function
  // like this
  // 'The list is {0, formatList}'.format(['a', 'b', 'c', 'd'])
  // with
  // function formatList(l) {
  //   if (l.length == 0) return 'empty';
  //   if (l.length == 1) return l[0];
  //   if (l.length == 2) return l[0] + ' and ' + l[1];
  //   var s = '';
  //   for (var i = 0; i < l.length - 1; i++)
  //     s += l[i] + ', ';
  //   return s + 'and ' + l[i];
  // }
  String.prototype.format = function() {
    var args = arguments;
    return this.replace(
      /\{\{[^{}]*\}\}|\{(\d+)(,\s*([\w.]+))?\}/g,
      function(m, a1, a2, a3) {
        if (m.chatAt == '{') {
          return m.slice(1, -1);
        }
        var rpl = args[a1];
        if (a3) {
          var f = eval(a3);
          rpl = f(rpl);
        }
        return rpl ? rpl : '';
      });
  };
}

if (!String.isBlank) {
  String.isBlank = function(s) {
    return (!s || (/^\s*$/).test(s));
  };
}

if (!String.compare) {
  String.compare = function(s1, s2) {
    if (s1 == s2) {
      return 0;
    }
    if (s1 > s2) {
      return 1;
    }
    return -1;
  };
}


//----- Debugging

StdExt.inspect = function(object, showFunctions) {
  var props = [];
  if (showFunctions == undefined) {
    showFunctions = false;
  }
  for (var k in object) {
    var v = object[k];
    if (typeof v == 'function' && !showFunctions) {
      continue;
    }
    props.push(k + ': "' + v + '"');
  }
  return '{ ' + props.join("\n") + ' }';
}
