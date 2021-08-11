/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/components/Toast.ts":
/*!************************************!*\
  !*** ./src/js/components/Toast.ts ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Toast": () => (/* binding */ Toast)
/* harmony export */ });


var Toast = function () {
  function Toast(message, duration) {
    if (duration === void 0) {
      duration = 5000;
    }

    this.message = message;
    this.duration = duration > 500 ? duration : 5000;
  }

  Toast.prototype.show = function () {
    var toaster = document.querySelector('.' + Toast.messageTargetClass);

    if (!toaster) {
      var toaster_1 = document.createElement('div');
      toaster_1.classList.add(Toast.messageTargetClass);
      document.querySelector(Toast.toastParentElement).appendChild(toaster_1);
    }

    toaster = document.querySelector('.' + Toast.messageTargetClass);

    if (!toaster) {
      throw new Error('Could not create Toaster element');
    }

    toaster.innerHTML = this.message;
    toaster.classList.add(Toast.toastAppearClass);
    window.setTimeout(function () {
      toaster.classList.remove(Toast.toastAppearClass);
      window.setTimeout(function () {
        return toaster.innerHTML = '';
      }, 500);
    }, this.duration);
  };

  Toast.toastParentElement = 'body';
  Toast.messageTargetClass = 'cmfive-toast-message';
  Toast.toastAppearClass = 'cmfive-toast-message-appear';
  return Toast;
}();



/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!************************************************************************!*\
  !*** ../../../../../../../modules/facilitator/assets/ts/event_view.ts ***!
  \************************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _system_templates_base_src_js_components_Toast__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../system/templates/base/src/js/components/Toast */ "./src/js/components/Toast.ts");
var __awaiter = undefined && undefined.__awaiter || function (thisArg, _arguments, P, generator) {
  function adopt(value) {
    return value instanceof P ? value : new P(function (resolve) {
      resolve(value);
    });
  }

  return new (P || (P = Promise))(function (resolve, reject) {
    function fulfilled(value) {
      try {
        step(generator.next(value));
      } catch (e) {
        reject(e);
      }
    }

    function rejected(value) {
      try {
        step(generator["throw"](value));
      } catch (e) {
        reject(e);
      }
    }

    function step(result) {
      result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected);
    }

    step((generator = generator.apply(thisArg, _arguments || [])).next());
  });
};

var __generator = undefined && undefined.__generator || function (thisArg, body) {
  var _ = {
    label: 0,
    sent: function sent() {
      if (t[0] & 1) throw t[1];
      return t[1];
    },
    trys: [],
    ops: []
  },
      f,
      y,
      t,
      g;
  return g = {
    next: verb(0),
    "throw": verb(1),
    "return": verb(2)
  }, typeof Symbol === "function" && (g[Symbol.iterator] = function () {
    return this;
  }), g;

  function verb(n) {
    return function (v) {
      return step([n, v]);
    };
  }

  function step(op) {
    if (f) throw new TypeError("Generator is already executing.");

    while (_) {
      try {
        if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
        if (y = 0, t) op = [op[0] & 2, t.value];

        switch (op[0]) {
          case 0:
          case 1:
            t = op;
            break;

          case 4:
            _.label++;
            return {
              value: op[1],
              done: false
            };

          case 5:
            _.label++;
            y = op[1];
            op = [0];
            continue;

          case 7:
            op = _.ops.pop();

            _.trys.pop();

            continue;

          default:
            if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) {
              _ = 0;
              continue;
            }

            if (op[0] === 3 && (!t || op[1] > t[0] && op[1] < t[3])) {
              _.label = op[1];
              break;
            }

            if (op[0] === 6 && _.label < t[1]) {
              _.label = t[1];
              t = op;
              break;
            }

            if (t && _.label < t[2]) {
              _.label = t[2];

              _.ops.push(op);

              break;
            }

            if (t[2]) _.ops.pop();

            _.trys.pop();

            continue;
        }

        op = body.call(thisArg, _);
      } catch (e) {
        op = [6, e];
        y = 0;
      } finally {
        f = t = 0;
      }
    }

    if (op[0] & 5) throw op[1];
    return {
      value: op[0] ? op[1] : void 0,
      done: true
    };
  }
};



var FacilitatorEventView = function () {
  function FacilitatorEventView() {}

  FacilitatorEventView.bindInteractions = function () {
    var _a, _b;

    (_a = document.querySelectorAll('.curriculum-file-checkbox')) === null || _a === void 0 ? void 0 : _a.forEach(function (checkbox) {
      checkbox.removeEventListener('change', FacilitatorEventView.checkboxChangeEvent);
      checkbox.addEventListener('change', FacilitatorEventView.checkboxChangeEvent);
    });
    (_b = document.querySelectorAll('[name="lead_facilitator"]')) === null || _b === void 0 ? void 0 : _b.forEach(function (radio) {
      radio.removeEventListener('change', FacilitatorEventView.leadFacilitatorChangeEvent);
      radio.addEventListener('change', FacilitatorEventView.leadFacilitatorChangeEvent);
    });
  };

  FacilitatorEventView.checkboxChangeEvent = function () {
    var _a, _b;

    if (this.checked) {
      (_a = document.querySelector('.curriculum-file-download-selected')) === null || _a === void 0 ? void 0 : _a.classList.remove('d-none');
      var download_all_button = document.querySelector('.curriculum-file-download-all');

      if (!(download_all_button === null || download_all_button === void 0 ? void 0 : download_all_button.classList.contains('d-none'))) {
        download_all_button.classList.add('d-none');
      }
    } else {
      var checkedBoxes = document.querySelectorAll('.curriculum-file-checkbox:is(:checked)');

      if (checkedBoxes.length == 0) {
        (_b = document.querySelector('.curriculum-file-download-all')) === null || _b === void 0 ? void 0 : _b.classList.remove('d-none');
        var download_selected_button = document.querySelector('.curriculum-file-download-selected');

        if (!(download_selected_button === null || download_selected_button === void 0 ? void 0 : download_selected_button.classList.contains('d-none'))) {
          download_selected_button.classList.add('d-none');
        }
      }
    }
  };

  FacilitatorEventView.leadFacilitatorChangeEvent = function () {
    return __awaiter(this, void 0, void 0, function () {
      var response;
      return __generator(this, function (_a) {
        switch (_a.label) {
          case 0:
            return [4, fetch('/facilitator-events/ajax_set_lead_facilitator/' + this.getAttribute('data-event-id') + '?lead_facilitator=' + this.value)];

          case 1:
            response = _a.sent();
            new _system_templates_base_src_js_components_Toast__WEBPACK_IMPORTED_MODULE_0__.Toast("Lead facilitator saved").show();
            return [2];
        }
      });
    });
  };

  return FacilitatorEventView;
}();

(function () {
  FacilitatorEventView.bindInteractions();
})();
})();

/******/ })()
;