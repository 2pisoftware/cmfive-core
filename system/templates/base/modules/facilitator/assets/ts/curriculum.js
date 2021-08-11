/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/Events.ts":
/*!**************************!*\
  !*** ./src/js/Events.ts ***!
  \**************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Events": () => (/* binding */ Events)
/* harmony export */ });
var Events = function () {
  function Events() {}

  Events.dispatchDomUpdateEvent = function (target) {
    var eventBusElement = window.cmfiveEventBus;

    if (eventBusElement) {
      eventBusElement.dispatchEvent(new CustomEvent('dom-update', {
        detail: target
      }));
    }
  };

  return Events;
}();



/***/ }),

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
  !*** ../../../../../../../modules/facilitator/assets/ts/curriculum.ts ***!
  \************************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _system_templates_base_src_js_Events__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../../../system/templates/base/src/js/Events */ "./src/js/Events.ts");
/* harmony import */ var _system_templates_base_src_js_components_Toast__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../../../../system/templates/base/src/js/components/Toast */ "./src/js/components/Toast.ts");
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




var BridgeCurriculumInterface = function () {
  function BridgeCurriculumInterface() {}

  BridgeCurriculumInterface.setupBindings = function () {
    var _a, _b, _c, _d, _e, _f;

    var curriculumList = document.querySelector('.curriculum-list-container');
    (_a = curriculumList === null || curriculumList === void 0 ? void 0 : curriculumList.querySelectorAll(BridgeCurriculumInterface.linkTarget)) === null || _a === void 0 ? void 0 : _a.forEach(function (c) {
      c.removeEventListener('click', BridgeCurriculumInterface.linkClickAction);
      c.addEventListener('click', BridgeCurriculumInterface.linkClickAction);
    });
    (_b = document.querySelector('.expand-all')) === null || _b === void 0 ? void 0 : _b.removeEventListener('click', function () {
      return BridgeCurriculumInterface.expandCollapsehandler(false);
    });
    (_c = document.querySelector('.expand-all')) === null || _c === void 0 ? void 0 : _c.addEventListener('click', function () {
      return BridgeCurriculumInterface.expandCollapsehandler(false);
    });
    (_d = document.querySelector('.collapse-all')) === null || _d === void 0 ? void 0 : _d.removeEventListener('click', function () {
      return BridgeCurriculumInterface.expandCollapsehandler(true);
    });
    (_e = document.querySelector('.collapse-all')) === null || _e === void 0 ? void 0 : _e.addEventListener('click', function () {
      return BridgeCurriculumInterface.expandCollapsehandler(true);
    });
    var target_id = new URLSearchParams(document.location.search.substring(1)).get('target_id');

    if (target_id) {
      var target = document.getElementById("category_item_" + target_id);

      if (target) {
        var loopGate = 0;
        var dragItemContainer = target.closest('.drag-item-container');
        var collapseParent = void 0;

        do {
          loopGate++;
          collapseParent = dragItemContainer.closest('.collapse:not(.show)');

          if (collapseParent != null) {
            collapseParent.classList.add('show');
          }

          dragItemContainer = collapseParent === null || collapseParent === void 0 ? void 0 : collapseParent.closest('.drag-item-container');

          if (dragItemContainer != null) {
            var indicator = dragItemContainer.querySelector('.list-group-item .expand-indicator');

            if (indicator != null) {
              indicator.setAttribute('aria-expanded', 'true');

              if (indicator.classList.contains('collapsed')) {
                indicator.classList.remove('collapsed');
              }
            }
          }
        } while (collapseParent && loopGate < 50);

        (_f = document.querySelector('a[data-id="' + target_id + '"]')) === null || _f === void 0 ? void 0 : _f.dispatchEvent(new Event('click'));
      }
    }
  };

  BridgeCurriculumInterface.expandCollapsehandler = function (collapse) {
    var _a, _b;

    (_a = document.querySelectorAll('.curriculum-list-container .collapse')) === null || _a === void 0 ? void 0 : _a.forEach(function (c) {
      if (collapse) {
        c.classList.remove('show');
      } else {
        if (!c.classList.contains('show')) {
          c.classList.add('show');
        }
      }
    });
    (_b = document.querySelectorAll('.curriculum-list-container .expand-indicator')) === null || _b === void 0 ? void 0 : _b.forEach(function (e) {
      e.setAttribute('aria-expanded', collapse ? 'false' : 'true');

      if (collapse) {
        if (!e.classList.contains('collapsed')) {
          e.classList.add('collapsed');
        }
      } else {
        e.classList.remove('collapsed');
      }
    });
  };

  BridgeCurriculumInterface.getPreviousSibling = function (source, target_class) {
    var sibling = source === null || source === void 0 ? void 0 : source.previousElementSibling;

    while (sibling) {
      if (sibling.matches(target_class)) {
        break;
      }

      sibling = sibling.previousElementSibling;
    }

    return sibling;
  };

  BridgeCurriculumInterface.getNextSibling = function (source, target_class) {
    var sibling = source === null || source === void 0 ? void 0 : source.nextElementSibling;

    while (sibling) {
      if (sibling.matches(target_class)) {
        break;
      }

      sibling = sibling.nextElementSibling;
    }

    return sibling;
  };

  BridgeCurriculumInterface.getDescendants = function (node) {
    var data = {};
    node === null || node === void 0 ? void 0 : node.forEach(function (element) {
      var _a;

      var element_id = (_a = element.querySelector('.list-group-item')) === null || _a === void 0 ? void 0 : _a.id;
      data[element_id] = {};

      if (element.querySelector('.collapse')) {
        data[element_id] = BridgeCurriculumInterface.getDescendants(element.querySelectorAll(':scope > .collapse > ul > .drag-item-container'));
      }
    });
    return data;
  };

  BridgeCurriculumInterface.linkTarget = '.category-title-link';
  BridgeCurriculumInterface.fileContainer = '.curriculum-file-container';
  BridgeCurriculumInterface.fileSummary = '.curriculum-file-summary';
  BridgeCurriculumInterface.fileList = '.curriculum-files';
  BridgeCurriculumInterface.detailsContainer = '.curriculum-details-container';
  BridgeCurriculumInterface.detailHeader = '.curriculum-detail-header';
  BridgeCurriculumInterface.detailDescription = '.curriculum-detail-description';
  BridgeCurriculumInterface.currentDraggedElementID = '';
  BridgeCurriculumInterface.targetPosition = '';

  BridgeCurriculumInterface.newFileClickAction = function () {
    return __awaiter(this, void 0, void 0, function () {
      return __generator(this, function (_a) {
        return [2];
      });
    });
  };

  BridgeCurriculumInterface.linkClickAction = function () {
    var _a;

    return __awaiter(this, void 0, void 0, function () {
      var container, details_container, timeout, response, json_response, header, description, summary, curriculum_files, index, _i, _b, element, div, header_1, button_container, download_file, meta_container, lang, size, extension, description_1;

      return __generator(this, function (_c) {
        switch (_c.label) {
          case 0:
            container = document.querySelector(BridgeCurriculumInterface.fileContainer);
            details_container = document.querySelector(BridgeCurriculumInterface.detailsContainer);

            if (!container || !details_container) {
              return [2];
            }

            container.classList.remove('show');
            timeout = window.setTimeout(function () {
              return details_container.classList.remove('show');
            }, 100);
            (_a = document.querySelectorAll('li.list-group-item')) === null || _a === void 0 ? void 0 : _a.forEach(function (l) {
              return l.classList.remove('active');
            });
            this.closest('li.list-group-item').classList.add('active');
            return [4, fetch('/facilitator-curriculum/ajax_info/' + this.getAttribute('data-id'))];

          case 1:
            response = _c.sent();
            if (!response.ok) return [3, 3];
            return [4, response.json()];

          case 2:
            json_response = _c.sent();

            if (json_response.hasOwnProperty('data')) {
              header = details_container.querySelector(BridgeCurriculumInterface.detailHeader);

              if (header) {
                header.innerHTML = this.innerHTML;
              }

              description = details_container.querySelector(BridgeCurriculumInterface.detailDescription);

              if (description) {
                description.innerHTML = '';

                if (json_response.data.category.description) {
                  description.innerHTML = json_response.data.category.description;
                }
              }

              summary = container.querySelector(BridgeCurriculumInterface.fileSummary);

              if (summary) {
                summary.innerHTML = json_response.message;
              }

              curriculum_files = container.querySelector('.curriculum-files');
              curriculum_files.classList.remove('empty');

              while (curriculum_files.firstElementChild) {
                curriculum_files.removeChild(curriculum_files.lastElementChild);
              }

              if (json_response.data.files.length) {
                index = 1;

                for (_i = 0, _b = json_response.data.files; _i < _b.length; _i++) {
                  element = _b[_i];
                  div = document.createElement('div');
                  div.classList.add('curriculum-file');
                  header_1 = document.createElement('p');
                  header_1.classList.add('file-header');
                  header_1.innerHTML = element.title;
                  button_container = document.createElement('div');
                  button_container.classList.add('button-container');
                  download_file = document.createElement('button');
                  download_file.classList.add('btn', 'btn-sm', 'btn-info');
                  download_file.innerHTML = '<i class="bi bi-download"></i>';
                  download_file.setAttribute('data-link-target', '/file/atdownload/' + element.attachment_id);
                  button_container.appendChild(download_file);
                  header_1.appendChild(button_container);
                  div.appendChild(header_1);
                  meta_container = document.createElement('div');
                  meta_container.classList.add('meta-container');

                  if (element.language) {
                    lang = document.createElement('span');
                    lang.innerHTML = element.language;
                    meta_container.appendChild(lang);
                  }

                  if (element.file_size) {
                    size = document.createElement('span');
                    size.innerHTML = element.file_size;
                    meta_container.appendChild(size);
                  }

                  extension = document.createElement('i');
                  extension.classList.add('bi');

                  switch (element.extension) {
                    case 'pdf':
                      extension.classList.add('bi-file-earmark-pdf');
                      break;

                    case 'doc':
                    case 'docx':
                      extension.classList.add('bi-file-earmark-word');
                      break;

                    case 'ppt':
                    case 'pptx':
                      extension.classList.add('bi-file-earmark-ppt');
                      break;

                    case 'xls':
                    case 'xlsx':
                      extension.classList.add('bi-file-earmark-excel');
                      break;

                    default:
                      extension.classList.add('bi-file-earmark');
                  }

                  meta_container.appendChild(extension);
                  div.appendChild(meta_container);
                  description_1 = document.createElement('p');

                  if (element.description) {
                    description_1.classList.add('file-description');
                    description_1.innerHTML = '<span>Details</span>' + element.description;
                  } else {
                    if (index < json_response.data.files.length) {
                      description_1.innerHTML = '&nbsp;';
                    }
                  }

                  div.appendChild(description_1);
                  curriculum_files.appendChild(div);
                  index++;
                }
              } else {
                curriculum_files.classList.add('empty');
              }

              window.clearTimeout(timeout);
              details_container.classList.add('show');
              window.setTimeout(function () {
                return container.classList.add('show');
              }, 100);
              _system_templates_base_src_js_Events__WEBPACK_IMPORTED_MODULE_0__.Events.dispatchDomUpdateEvent(container);
            }

            return [3, 4];

          case 3:
            new _system_templates_base_src_js_components_Toast__WEBPACK_IMPORTED_MODULE_1__.Toast('Could not complete request: ' + response.status + ' ' + response.statusText).show();
            _c.label = 4;

          case 4:
            return [2];
        }
      });
    });
  };

  return BridgeCurriculumInterface;
}();

(function () {
  return BridgeCurriculumInterface.setupBindings();
})();
})();

/******/ })()
;