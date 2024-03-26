Espo.loader.setContextId('lib!gridstack');
(function webpackUniversalModuleDefinition(root, factory) {
	if(typeof exports === 'object' && typeof module === 'object')
		module.exports = factory(require("jquery"), require("jquery-ui"), require("jquery-ui-touch-punch"));
	else if(typeof define === 'function' && define.amd)
		define(["jquery", "jquery-ui", "jquery-ui-touch-punch"], factory);
	else if(typeof exports === 'object')
		exports["GridStack"] = factory(require("jquery"), require("jquery-ui"), require("jquery-ui-touch-punch"));
	else
		root["GridStack"] = factory(root["jquery"], root["jquery-ui"], root["jquery-ui-touch-punch"]);
})(self, function(__WEBPACK_EXTERNAL_MODULE__273__, __WEBPACK_EXTERNAL_MODULE__946__, __WEBPACK_EXTERNAL_MODULE__858__) {
return  (() => { 
 	"use strict";
 	var __webpack_modules__ = ({

 21:
 ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({ value: true }));
exports.GridStackDD = void 0;

const gridstack_ddi_1 = __webpack_require__(334);
const gridstack_1 = __webpack_require__(270);
const utils_1 = __webpack_require__(593);


class GridStackDD extends gridstack_ddi_1.GridStackDDI {
    
    static get() {
        return gridstack_ddi_1.GridStackDDI.get();
    }
    
    remove(el) {
        this.draggable(el, 'destroy').resizable(el, 'destroy');
        if (el.gridstackNode) {
            delete el.gridstackNode._initDD; 
        }
        return this;
    }
}
exports.GridStackDD = GridStackDD;


gridstack_1.GridStack.prototype._setupAcceptWidget = function () {
    
    if (this.opts.staticGrid || (!this.opts.acceptWidgets && !this.opts.removable)) {
        GridStackDD.get().droppable(this.el, 'destroy');
        return this;
    }
    
    let cellHeight, cellWidth;
    let onDrag = (event, el, helper) => {
        let node = el.gridstackNode;
        if (!node)
            return;
        helper = helper || el;
        let parent = this.el.getBoundingClientRect();
        let { top, left } = helper.getBoundingClientRect();
        left -= parent.left;
        top -= parent.top;
        let ui = { position: { top, left } };
        if (node._temporaryRemoved) {
            node.x = Math.max(0, Math.round(left / cellWidth));
            node.y = Math.max(0, Math.round(top / cellHeight));
            delete node.autoPosition;
            this.engine.nodeBoundFix(node);
            
            if (!this.engine.willItFit(node)) {
                node.autoPosition = true; 
                if (!this.engine.willItFit(node)) {
                    GridStackDD.get().off(el, 'drag'); 
                    return; 
                }
                if (node._willFitPos) {
                    
                    utils_1.Utils.copyPos(node, node._willFitPos);
                    delete node._willFitPos;
                }
            }
            
            this._onStartMoving(helper, event, ui, node, cellWidth, cellHeight);
        }
        else {
            
            this._dragOrResize(helper, event, ui, node, cellWidth, cellHeight);
        }
    };
    GridStackDD.get()
        .droppable(this.el, {
        accept: (el) => {
            let node = el.gridstackNode;
            
            if ((node === null || node === void 0 ? void 0 : node.grid) === this)
                return true;
            if (!this.opts.acceptWidgets)
                return false;
            
            if (node === null || node === void 0 ? void 0 : node.subGrid)
                return false;
            
            let canAccept = true;
            if (typeof this.opts.acceptWidgets === 'function') {
                canAccept = this.opts.acceptWidgets(el);
            }
            else {
                let selector = (this.opts.acceptWidgets === true ? '.grid-stack-item' : this.opts.acceptWidgets);
                canAccept = el.matches(selector);
            }
            
            if (canAccept && node && this.opts.maxRow) {
                let n = { w: node.w, h: node.h, minW: node.minW, minH: node.minH }; 
                canAccept = this.engine.willItFit(n);
            }
            return canAccept;
        }
    })
        
        .on(this.el, 'dropover', (event, el, helper) => {
        
        let node = el.gridstackNode;
        
        if ((node === null || node === void 0 ? void 0 : node.grid) === this && !node._temporaryRemoved) {
            
            return false; 
        }
        
        if ((node === null || node === void 0 ? void 0 : node.grid) && node.grid !== this && !node._temporaryRemoved) {
            
            let otherGrid = node.grid;
            otherGrid._leave(el, helper);
        }
        
        cellWidth = this.cellWidth();
        cellHeight = this.getCellHeight(true);
        
        if (!node) { 
            node = this._readAttr(el);
        }
        if (!node.grid) {
            node._isExternal = true;
            el.gridstackNode = node;
        }
        
        helper = helper || el;
        let w = node.w || Math.round(helper.offsetWidth / cellWidth) || 1;
        let h = node.h || Math.round(helper.offsetHeight / cellHeight) || 1;
        
        if (node.grid && node.grid !== this) {
            
            
            if (!el._gridstackNodeOrig)
                el._gridstackNodeOrig = node; 
            el.gridstackNode = node = Object.assign(Object.assign({}, node), { w, h, grid: this });
            this.engine.cleanupNode(node)
                .nodeBoundFix(node);
            
            node._initDD =
                node._isExternal = 
                    node._temporaryRemoved = true; 
        }
        else {
            node.w = w;
            node.h = h;
            node._temporaryRemoved = true; 
        }
        
        _itemRemoving(node.el, false);
        GridStackDD.get().on(el, 'drag', onDrag);
        
        onDrag(event, el, helper);
        return false; 
    })
        
        .on(this.el, 'dropout', (event, el, helper) => {
        
        let node = el.gridstackNode;
        if (!node)
            return false;
        
        
        if (!node.grid || node.grid === this) {
            this._leave(el, helper);
        }
        return false; 
    })
        
        .on(this.el, 'drop', (event, el, helper) => {
        let node = el.gridstackNode;
        
        if ((node === null || node === void 0 ? void 0 : node.grid) === this && !node._isExternal)
            return false;
        let wasAdded = !!this.placeholder.parentElement; 
        this.placeholder.remove();
        
        
        let origNode = el._gridstackNodeOrig;
        delete el._gridstackNodeOrig;
        if (wasAdded && origNode && origNode.grid && origNode.grid !== this) {
            let oGrid = origNode.grid;
            oGrid.engine.removedNodes.push(origNode);
            oGrid._triggerRemoveEvent();
        }
        if (!node)
            return false;
        
        if (wasAdded) {
            this.engine.cleanupNode(node); 
            node.grid = this;
        }
        GridStackDD.get().off(el, 'drag');
        
        
        if (helper !== el) {
            helper.remove();
            el.gridstackNode = origNode; 
            if (wasAdded) {
                el = el.cloneNode(true);
            }
        }
        else {
            el.remove(); 
            GridStackDD.get().remove(el);
        }
        if (!wasAdded)
            return false;
        el.gridstackNode = node;
        node.el = el;
        
        utils_1.Utils.copyPos(node, this._readAttr(this.placeholder)); 
        utils_1.Utils.removePositioningStyles(el); 
        this._writeAttr(el, node);
        this.el.appendChild(el); 
        this._updateContainerHeight();
        this.engine.addedNodes.push(node); 
        this._triggerAddEvent(); 
        this._triggerChangeEvent();
        this.engine.endUpdate();
        if (this._gsEventHandler['dropped']) {
            this._gsEventHandler['dropped'](Object.assign(Object.assign({}, event), { type: 'dropped' }), origNode && origNode.grid ? origNode : undefined, node);
        }
        
        window.setTimeout(() => {
            
            if (node.el && node.el.parentElement) {
                this._prepareDragDropByNode(node);
            }
            else {
                this.engine.removeNode(node);
            }
        });
        return false; 
    });
    return this;
};

function _itemRemoving(el, remove) {
    let node = el ? el.gridstackNode : undefined;
    if (!node || !node.grid)
        return;
    remove ? node._isAboutToRemove = true : delete node._isAboutToRemove;
    remove ? el.classList.add('grid-stack-item-removing') : el.classList.remove('grid-stack-item-removing');
}

gridstack_1.GridStack.prototype._setupRemoveDrop = function () {
    if (!this.opts.staticGrid && typeof this.opts.removable === 'string') {
        let trashEl = document.querySelector(this.opts.removable);
        if (!trashEl)
            return this;
        
        
        
        if (!GridStackDD.get().isDroppable(trashEl)) {
            GridStackDD.get().droppable(trashEl, this.opts.removableOptions)
                .on(trashEl, 'dropover', (event, el) => _itemRemoving(el, true))
                .on(trashEl, 'dropout', (event, el) => _itemRemoving(el, false));
        }
    }
    return this;
};

gridstack_1.GridStack.setupDragIn = function (_dragIn, _dragInOptions) {
    let dragIn;
    let dragInOptions;
    const dragInDefaultOptions = {
        revert: 'invalid',
        handle: '.grid-stack-item-content',
        scroll: false,
        appendTo: 'body'
    };
    
    if (_dragIn) {
        dragIn = _dragIn;
        dragInOptions = Object.assign(Object.assign({}, dragInDefaultOptions), (_dragInOptions || {}));
    }
    if (typeof dragIn !== 'string')
        return;
    let dd = GridStackDD.get();
    utils_1.Utils.getElements(dragIn).forEach(el => {
        if (!dd.isDraggable(el))
            dd.dragIn(el, dragInOptions);
    });
};

gridstack_1.GridStack.prototype._prepareDragDropByNode = function (node) {
    let el = node.el;
    let dd = GridStackDD.get();
    
    if (this.opts.staticGrid || ((node.noMove || this.opts.disableDrag) && (node.noResize || this.opts.disableResize))) {
        if (node._initDD) {
            dd.remove(el); 
            delete node._initDD;
        }
        el.classList.add('ui-draggable-disabled', 'ui-resizable-disabled'); 
        return this;
    }
    if (!node._initDD) {
        
        let cellWidth;
        let cellHeight;
        
        let onStartMoving = (event, ui) => {
            
            if (this._gsEventHandler[event.type]) {
                this._gsEventHandler[event.type](event, event.target);
            }
            cellWidth = this.cellWidth();
            cellHeight = this.getCellHeight(true); 
            this._onStartMoving(el, event, ui, node, cellWidth, cellHeight);
        };
        
        let dragOrResize = (event, ui) => {
            this._dragOrResize(el, event, ui, node, cellWidth, cellHeight);
        };
        
        let onEndMoving = (event) => {
            this.placeholder.remove();
            delete node._moving;
            delete node._lastTried;
            
            let target = event.target;
            if (!target.gridstackNode || target.gridstackNode.grid !== this)
                return;
            node.el = target;
            if (node._isAboutToRemove) {
                let gridToNotify = el.gridstackNode.grid;
                if (gridToNotify._gsEventHandler[event.type]) {
                    gridToNotify._gsEventHandler[event.type](event, target);
                }
                dd.remove(el);
                gridToNotify.engine.removedNodes.push(node);
                gridToNotify._triggerRemoveEvent();
                
                delete el.gridstackNode;
                delete node.el;
                el.remove();
            }
            else {
                if (!node._temporaryRemoved) {
                    
                    utils_1.Utils.removePositioningStyles(target); 
                    this._writePosAttr(target, node);
                }
                else {
                    
                    utils_1.Utils.removePositioningStyles(target);
                    utils_1.Utils.copyPos(node, node._orig); 
                    this._writePosAttr(target, node);
                    this.engine.addNode(node);
                }
                if (this._gsEventHandler[event.type]) {
                    this._gsEventHandler[event.type](event, target);
                }
            }
            
            this._extraDragRow = 0; 
            this._updateContainerHeight(); 
            this._triggerChangeEvent();
            this.engine.endUpdate();
        };
        dd.draggable(el, {
            start: onStartMoving,
            stop: onEndMoving,
            drag: dragOrResize
        }).resizable(el, {
            start: onStartMoving,
            stop: onEndMoving,
            resize: dragOrResize
        });
        node._initDD = true; 
    }
    
    if (node.noMove || this.opts.disableDrag) {
        dd.draggable(el, 'disable');
        el.classList.add('ui-draggable-disabled');
    }
    else {
        dd.draggable(el, 'enable');
        el.classList.remove('ui-draggable-disabled');
    }
    if (node.noResize || this.opts.disableResize) {
        dd.resizable(el, 'disable');
        el.classList.add('ui-resizable-disabled');
    }
    else {
        dd.resizable(el, 'enable');
        el.classList.remove('ui-resizable-disabled');
    }
    return this;
};

gridstack_1.GridStack.prototype._onStartMoving = function (el, event, ui, node, cellWidth, cellHeight) {
    this.engine.cleanNodes()
        .beginUpdate(node);
    
    this._writePosAttr(this.placeholder, node);
    this.el.appendChild(this.placeholder);
    
    node.el = this.placeholder;
    node._lastUiPosition = ui.position;
    node._prevYPix = ui.position.top;
    node._moving = (event.type === 'dragstart'); 
    delete node._lastTried;
    if (event.type === 'dropover' && node._temporaryRemoved) {
        
        this.engine.addNode(node); 
        node._moving = true; 
    }
    
    this.engine.cacheRects(cellWidth, cellHeight, this.opts.marginTop, this.opts.marginRight, this.opts.marginBottom, this.opts.marginLeft);
    if (event.type === 'resizestart') {
        let dd = GridStackDD.get()
            .resizable(el, 'option', 'minWidth', cellWidth * (node.minW || 1))
            .resizable(el, 'option', 'minHeight', cellHeight * (node.minH || 1));
        if (node.maxW) {
            dd.resizable(el, 'option', 'maxWidth', cellWidth * node.maxW);
        }
        if (node.maxH) {
            dd.resizable(el, 'option', 'maxHeight', cellHeight * node.maxH);
        }
    }
};

gridstack_1.GridStack.prototype._leave = function (el, helper) {
    let node = el.gridstackNode;
    if (!node)
        return;
    GridStackDD.get().off(el, 'drag'); 
    
    if (node._temporaryRemoved)
        return;
    node._temporaryRemoved = true;
    this.engine.removeNode(node); 
    node.el = node._isExternal && helper ? helper : el; 
    if (this.opts.removable === true) { 
        
        _itemRemoving(el, true);
    }
    
    if (el._gridstackNodeOrig) {
        
        el.gridstackNode = el._gridstackNodeOrig;
        delete el._gridstackNodeOrig;
    }
    else if (node._isExternal) {
        
        delete node.el;
        delete el.gridstackNode;
        
        this.engine.restoreInitial();
    }
};

gridstack_1.GridStack.prototype._dragOrResize = function (el, event, ui, node, cellWidth, cellHeight) {
    let p = Object.assign({}, node._orig); 
    let resizing;
    let mLeft = this.opts.marginLeft, mRight = this.opts.marginRight, mTop = this.opts.marginTop, mBottom = this.opts.marginBottom;
    
    let mHeight = Math.round(cellHeight * 0.1), mWidth = Math.round(cellWidth * 0.1);
    mLeft = Math.min(mLeft, mWidth);
    mRight = Math.min(mRight, mWidth);
    mTop = Math.min(mTop, mHeight);
    mBottom = Math.min(mBottom, mHeight);
    if (event.type === 'drag') {
        if (node._temporaryRemoved)
            return; 
        let distance = ui.position.top - node._prevYPix;
        node._prevYPix = ui.position.top;
        utils_1.Utils.updateScrollPosition(el, ui.position, distance);
        
        let left = ui.position.left + (ui.position.left > node._lastUiPosition.left ? -mRight : mLeft);
        let top = ui.position.top + (ui.position.top > node._lastUiPosition.top ? -mBottom : mTop);
        p.x = Math.round(left / cellWidth);
        p.y = Math.round(top / cellHeight);
        
        let prev = this._extraDragRow;
        if (this.engine.collide(node, p)) {
            let row = this.getRow();
            let extra = Math.max(0, (p.y + node.h) - row);
            if (this.opts.maxRow && row + extra > this.opts.maxRow) {
                extra = Math.max(0, this.opts.maxRow - row);
            } 
            this._extraDragRow = extra; 
        }
        else
            this._extraDragRow = 0; 
        if (this._extraDragRow !== prev)
            this._updateContainerHeight();
        if (node.x === p.x && node.y === p.y)
            return; 
        
        
    }
    else if (event.type === 'resize') {
        if (p.x < 0)
            return;
        
        utils_1.Utils.updateScrollResize(event, el, cellHeight);
        
        p.w = Math.round((ui.size.width - mLeft) / cellWidth);
        p.h = Math.round((ui.size.height - mTop) / cellHeight);
        if (node.w === p.w && node.h === p.h)
            return;
        if (node._lastTried && node._lastTried.w === p.w && node._lastTried.h === p.h)
            return; 
        
        let left = ui.position.left + mLeft;
        let top = ui.position.top + mTop;
        p.x = Math.round(left / cellWidth);
        p.y = Math.round(top / cellHeight);
        resizing = true;
    }
    node._lastTried = p; 
    let rect = {
        x: ui.position.left + mLeft,
        y: ui.position.top + mTop,
        w: (ui.size ? ui.size.width : node.w * cellWidth) - mLeft - mRight,
        h: (ui.size ? ui.size.height : node.h * cellHeight) - mTop - mBottom
    };
    if (this.engine.moveNodeCheck(node, Object.assign(Object.assign({}, p), { cellWidth, cellHeight, rect, resizing }))) {
        node._lastUiPosition = ui.position;
        this.engine.cacheRects(cellWidth, cellHeight, mTop, mRight, mBottom, mLeft);
        delete node._skipDown;
        if (resizing && node.subGrid) {
            node.subGrid.onParentResize();
        } 
        this._extraDragRow = 0; 
        this._updateContainerHeight();
        let target = event.target; 
        this._writePosAttr(target, node);
        if (this._gsEventHandler[event.type]) {
            this._gsEventHandler[event.type](event, target);
        }
    }
};

gridstack_1.GridStack.prototype.movable = function (els, val) {
    if (this.opts.staticGrid)
        return this; 
    gridstack_1.GridStack.getElements(els).forEach(el => {
        let node = el.gridstackNode;
        if (!node)
            return;
        if (val)
            delete node.noMove;
        else
            node.noMove = true;
        this._prepareDragDropByNode(node); 
    });
    return this;
};

gridstack_1.GridStack.prototype.resizable = function (els, val) {
    if (this.opts.staticGrid)
        return this; 
    gridstack_1.GridStack.getElements(els).forEach(el => {
        let node = el.gridstackNode;
        if (!node)
            return;
        if (val)
            delete node.noResize;
        else
            node.noResize = true;
        this._prepareDragDropByNode(node); 
    });
    return this;
};

gridstack_1.GridStack.prototype.disable = function () {
    if (this.opts.staticGrid)
        return;
    this.enableMove(false);
    this.enableResize(false); 
    this._triggerEvent('disable');
    return this;
};

gridstack_1.GridStack.prototype.enable = function () {
    if (this.opts.staticGrid)
        return;
    this.enableMove(true);
    this.enableResize(true); 
    this._triggerEvent('enable');
    return this;
};

gridstack_1.GridStack.prototype.enableMove = function (doEnable) {
    if (this.opts.staticGrid)
        return this; 
    this.opts.disableDrag = !doEnable; 
    this.engine.nodes.forEach(n => this.movable(n.el, doEnable));
    return this;
};

gridstack_1.GridStack.prototype.enableResize = function (doEnable) {
    if (this.opts.staticGrid)
        return this; 
    this.opts.disableResize = !doEnable; 
    this.engine.nodes.forEach(n => this.resizable(n.el, doEnable));
    return this;
};


 }),

 334:
 ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({ value: true }));
exports.GridStackDDI = void 0;

class GridStackDDI {
    
    static registerPlugin(pluginClass) {
        GridStackDDI.ddi = new pluginClass();
        return GridStackDDI.ddi;
    }
    
    static get() {
        return GridStackDDI.ddi || GridStackDDI.registerPlugin(GridStackDDI);
    }
    
    
    remove(el) {
        return this; 
    }
}
exports.GridStackDDI = GridStackDDI;


 }),

 62:
 ((__unused_webpack_module, exports, __webpack_require__) => {



Object.defineProperty(exports, "__esModule", ({ value: true }));
exports.GridStackEngine = void 0;
const utils_1 = __webpack_require__(593);

class GridStackEngine {
    constructor(opts = {}) {
        this.addedNodes = [];
        this.removedNodes = [];
        this.column = opts.column || 12;
        this.maxRow = opts.maxRow;
        this._float = opts.float;
        this.nodes = opts.nodes || [];
        this.onChange = opts.onChange;
    }
    batchUpdate() {
        if (this.batchMode)
            return this;
        this.batchMode = true;
        this._prevFloat = this._float;
        this._float = true; 
        return this.saveInitial(); 
    }
    commit() {
        if (!this.batchMode)
            return this;
        this.batchMode = false;
        this._float = this._prevFloat;
        delete this._prevFloat;
        return this._packNodes()
            ._notify();
    }
    
    _useEntireRowArea(node, nn) {
        return !this.float && !this._hasLocked && (!node._moving || node._skipDown || nn.y <= node.y);
    }
    
    _fixCollisions(node, nn = node, collide, opt = {}) {
        this.sortNodes(-1); 
        collide = collide || this.collide(node, nn); 
        if (!collide)
            return false;
        
        if (node._moving && !opt.nested && !this.float) {
            if (this.swap(node, collide))
                return true;
        }
        
        let area = nn;
        if (this._useEntireRowArea(node, nn)) {
            area = { x: 0, w: this.column, y: nn.y, h: nn.h };
            collide = this.collide(node, area, opt.skip); 
        }
        let didMove = false;
        let newOpt = { nested: true, pack: false };
        while (collide = collide || this.collide(node, area, opt.skip)) { 
            let moved;
            
            
            if (collide.locked || node._moving && !node._skipDown && nn.y > node.y && !this.float &&
                
                (!this.collide(collide, Object.assign(Object.assign({}, collide), { y: node.y }), node) || !this.collide(collide, Object.assign(Object.assign({}, collide), { y: nn.y - collide.h }), node))) {
                node._skipDown = (node._skipDown || nn.y > node.y);
                moved = this.moveNode(node, Object.assign(Object.assign(Object.assign({}, nn), { y: collide.y + collide.h }), newOpt));
                if (collide.locked && moved) {
                    utils_1.Utils.copyPos(nn, node); 
                }
                else if (!collide.locked && moved && opt.pack) {
                    
                    this._packNodes();
                    nn.y = collide.y + collide.h;
                    utils_1.Utils.copyPos(node, nn);
                }
                didMove = didMove || moved;
            }
            else {
                
                moved = this.moveNode(collide, Object.assign(Object.assign(Object.assign({}, collide), { y: nn.y + nn.h, skip: node }), newOpt));
            }
            if (!moved) {
                return didMove;
            } 
            collide = undefined;
        }
        return didMove;
    }
    
    collide(skip, area = skip, skip2) {
        return this.nodes.find(n => n !== skip && n !== skip2 && utils_1.Utils.isIntercepted(n, area));
    }
    collideAll(skip, area = skip, skip2) {
        return this.nodes.filter(n => n !== skip && n !== skip2 && utils_1.Utils.isIntercepted(n, area));
    }
    
    collideCoverage(node, o, collides) {
        if (!o.rect || !node._rect)
            return;
        let r0 = node._rect; 
        let r = Object.assign({}, o.rect); 
        
        if (r.y > r0.y) {
            r.h += r.y - r0.y;
            r.y = r0.y;
        }
        else {
            r.h += r0.y - r.y;
        }
        if (r.x > r0.x) {
            r.w += r.x - r0.x;
            r.x = r0.x;
        }
        else {
            r.w += r0.x - r.x;
        }
        let collide;
        collides.forEach(n => {
            if (n.locked || !n._rect)
                return;
            let r2 = n._rect; 
            let yOver = Number.MAX_VALUE, xOver = Number.MAX_VALUE, overMax = 0.5; 
            
            
            if (r0.y < r2.y) { 
                yOver = ((r.y + r.h) - r2.y) / r2.h;
            }
            else if (r0.y + r0.h > r2.y + r2.h) { 
                yOver = ((r2.y + r2.h) - r.y) / r2.h;
            }
            if (r0.x < r2.x) { 
                xOver = ((r.x + r.w) - r2.x) / r2.w;
            }
            else if (r0.x + r0.w > r2.x + r2.w) { 
                xOver = ((r2.x + r2.w) - r.x) / r2.w;
            }
            let over = Math.min(xOver, yOver);
            if (over > overMax) {
                overMax = over;
                collide = n;
            }
        });
        return collide;
    }
    
    cacheRects(w, h, top, right, bottom, left) {
        this.nodes.forEach(n => n._rect = {
            y: n.y * h + top,
            x: n.x * w + left,
            w: n.w * w - left - right,
            h: n.h * h - top - bottom
        });
        return this;
    }
    
    swap(a, b) {
        if (!b || b.locked || !a || a.locked)
            return false;
        function _doSwap() {
            let x = b.x, y = b.y;
            b.x = a.x;
            b.y = a.y; 
            if (a.h != b.h) {
                a.x = x;
                a.y = b.y + b.h; 
            }
            else if (a.w != b.w) {
                a.x = b.x + b.w;
                a.y = y; 
            }
            else {
                a.x = x;
                a.y = y; 
            }
            a._dirty = b._dirty = true;
            return true;
        }
        let touching; 
        
        if (a.w === b.w && a.h === b.h && (a.x === b.x || a.y === b.y) && (touching = utils_1.Utils.isTouching(a, b)))
            return _doSwap();
        if (touching === false)
            return; 
        
        if (a.w === b.w && a.x === b.x && (touching || (touching = utils_1.Utils.isTouching(a, b)))) {
            if (b.y < a.y) {
                let t = a;
                a = b;
                b = t;
            } 
            return _doSwap();
        }
        if (touching === false)
            return;
        
        if (a.h === b.h && a.y === b.y && (touching || (touching = utils_1.Utils.isTouching(a, b)))) {
            if (b.x < a.x) {
                let t = a;
                a = b;
                b = t;
            } 
            return _doSwap();
        }
        return false;
    }
    isAreaEmpty(x, y, w, h) {
        let nn = { x: x || 0, y: y || 0, w: w || 1, h: h || 1 };
        return !this.collide(nn);
    }
    
    compact() {
        if (this.nodes.length === 0)
            return this;
        this.batchUpdate()
            .sortNodes();
        let copyNodes = this.nodes;
        this.nodes = []; 
        copyNodes.forEach(node => {
            if (!node.locked) {
                node.autoPosition = true;
            }
            this.addNode(node, false); 
            node._dirty = true; 
        });
        return this.commit();
    }
    
    get float() { return this._float || false; }
    
    sortNodes(dir) {
        this.nodes = utils_1.Utils.sort(this.nodes, dir, this.column);
        return this;
    }
    
    _packNodes() {
        if (this.batchMode) {
            return this;
        }
        this.sortNodes(); 
        if (this.float) {
            
            this.nodes.forEach(n => {
                if (n._updating || n._orig === undefined || n.y === n._orig.y)
                    return;
                let newY = n.y;
                while (newY > n._orig.y) {
                    --newY;
                    let collide = this.collide(n, { x: n.x, y: newY, w: n.w, h: n.h });
                    if (!collide) {
                        n._dirty = true;
                        n.y = newY;
                    }
                }
            });
        }
        else {
            
            this.nodes.forEach((n, i) => {
                if (n.locked)
                    return;
                while (n.y > 0) {
                    let newY = i === 0 ? 0 : n.y - 1;
                    let canBeMoved = i === 0 || !this.collide(n, { x: n.x, y: newY, w: n.w, h: n.h });
                    if (!canBeMoved)
                        break;
                    
                    
                    
                    n._dirty = (n.y !== newY);
                    n.y = newY;
                }
            });
        }
        return this;
    }
    
    prepareNode(node, resizing) {
        node = node || {};
        node._id = node._id || GridStackEngine._idSeq++;
        
        if (node.x === undefined || node.y === undefined || node.x === null || node.y === null) {
            node.autoPosition = true;
        }
        
        let defaults = { x: 0, y: 0, w: 1, h: 1 };
        utils_1.Utils.defaults(node, defaults);
        if (!node.autoPosition) {
            delete node.autoPosition;
        }
        if (!node.noResize) {
            delete node.noResize;
        }
        if (!node.noMove) {
            delete node.noMove;
        }
        
        if (typeof node.x == 'string') {
            node.x = Number(node.x);
        }
        if (typeof node.y == 'string') {
            node.y = Number(node.y);
        }
        if (typeof node.w == 'string') {
            node.w = Number(node.w);
        }
        if (typeof node.h == 'string') {
            node.h = Number(node.h);
        }
        if (isNaN(node.x)) {
            node.x = defaults.x;
            node.autoPosition = true;
        }
        if (isNaN(node.y)) {
            node.y = defaults.y;
            node.autoPosition = true;
        }
        if (isNaN(node.w)) {
            node.w = defaults.w;
        }
        if (isNaN(node.h)) {
            node.h = defaults.h;
        }
        return this.nodeBoundFix(node, resizing);
    }
    
    nodeBoundFix(node, resizing) {
        let before = node._orig || utils_1.Utils.copyPos({}, node);
        if (node.maxW) {
            node.w = Math.min(node.w, node.maxW);
        }
        if (node.maxH) {
            node.h = Math.min(node.h, node.maxH);
        }
        if (node.minW && node.minW <= this.column) {
            node.w = Math.max(node.w, node.minW);
        }
        if (node.minH) {
            node.h = Math.max(node.h, node.minH);
        }
        if (node.w > this.column) {
            
            
            
            if (this.column < 12 && !this._inColumnResize) {
                node.w = Math.min(12, node.w);
                this.cacheOneLayout(node, 12);
            }
            node.w = this.column;
        }
        else if (node.w < 1) {
            node.w = 1;
        }
        if (this.maxRow && node.h > this.maxRow) {
            node.h = this.maxRow;
        }
        else if (node.h < 1) {
            node.h = 1;
        }
        if (node.x < 0) {
            node.x = 0;
        }
        if (node.y < 0) {
            node.y = 0;
        }
        if (node.x + node.w > this.column) {
            if (resizing) {
                node.w = this.column - node.x;
            }
            else {
                node.x = this.column - node.w;
            }
        }
        if (this.maxRow && node.y + node.h > this.maxRow) {
            if (resizing) {
                node.h = this.maxRow - node.y;
            }
            else {
                node.y = this.maxRow - node.h;
            }
        }
        if (!utils_1.Utils.samePos(node, before)) {
            node._dirty = true;
        }
        return node;
    }
    
    getDirtyNodes(verify) {
        
        if (verify) {
            return this.nodes.filter(n => n._dirty && !utils_1.Utils.samePos(n, n._orig));
        }
        return this.nodes.filter(n => n._dirty);
    }
    
    _notify(removedNodes) {
        if (this.batchMode || !this.onChange)
            return this;
        let dirtyNodes = (removedNodes || []).concat(this.getDirtyNodes());
        this.onChange(dirtyNodes);
        return this;
    }
    
    cleanNodes() {
        if (this.batchMode)
            return this;
        this.nodes.forEach(n => {
            delete n._dirty;
            delete n._lastTried;
        });
        return this;
    }
    
    saveInitial() {
        this.nodes.forEach(n => {
            n._orig = utils_1.Utils.copyPos({}, n);
            delete n._dirty;
        });
        this._hasLocked = this.nodes.some(n => n.locked);
        return this;
    }
    
    restoreInitial() {
        this.nodes.forEach(n => {
            if (utils_1.Utils.samePos(n, n._orig))
                return;
            utils_1.Utils.copyPos(n, n._orig);
            n._dirty = true;
        });
        this._notify();
        return this;
    }
    
    addNode(node, triggerAddEvent = false) {
        let dup = this.nodes.find(n => n._id === node._id);
        if (dup)
            return dup; 
        
        node = this._inColumnResize ? this.nodeBoundFix(node) : this.prepareNode(node);
        delete node._temporaryRemoved;
        delete node._removeDOM;
        if (node.autoPosition) {
            this.sortNodes();
            for (let i = 0;; ++i) {
                let x = i % this.column;
                let y = Math.floor(i / this.column);
                if (x + node.w > this.column) {
                    continue;
                }
                let box = { x, y, w: node.w, h: node.h };
                if (!this.nodes.find(n => utils_1.Utils.isIntercepted(box, n))) {
                    node.x = x;
                    node.y = y;
                    delete node.autoPosition; 
                    break;
                }
            }
        }
        this.nodes.push(node);
        if (triggerAddEvent) {
            this.addedNodes.push(node);
        }
        this._fixCollisions(node);
        if (!this.batchMode) {
            this._packNodes()._notify();
        }
        return node;
    }
    removeNode(node, removeDOM = true, triggerEvent = false) {
        if (!this.nodes.find(n => n === node)) {
            
            return this;
        }
        if (triggerEvent) { 
            this.removedNodes.push(node);
        }
        if (removeDOM)
            node._removeDOM = true; 
        
        this.nodes = this.nodes.filter(n => n !== node);
        return this._packNodes()
            ._notify([node]);
    }
    removeAll(removeDOM = true) {
        delete this._layouts;
        if (this.nodes.length === 0)
            return this;
        removeDOM && this.nodes.forEach(n => n._removeDOM = true); 
        this.removedNodes = this.nodes;
        this.nodes = [];
        return this._notify(this.removedNodes);
    }
    
    moveNodeCheck(node, o) {
        
        if (!this.changedPosConstrain(node, o))
            return false;
        o.pack = true;
        
        if (!this.maxRow) {
            return this.moveNode(node, o);
        }
        
        let clonedNode;
        let clone = new GridStackEngine({
            column: this.column,
            float: this.float,
            nodes: this.nodes.map(n => {
                if (n === node) {
                    clonedNode = Object.assign({}, n);
                    return clonedNode;
                }
                return Object.assign({}, n);
            })
        });
        if (!clonedNode)
            return false;
        
        let canMove = clone.moveNode(clonedNode, o) && clone.getRow() <= this.maxRow;
        
        if (!canMove && !o.resizing) {
            let collide = this.collide(node, o);
            if (collide && this.swap(node, collide)) {
                this._notify();
                return true;
            }
        }
        if (!canMove)
            return false;
        
        
        clone.nodes.filter(n => n._dirty).forEach(c => {
            let n = this.nodes.find(a => a._id === c._id);
            if (!n)
                return;
            utils_1.Utils.copyPos(n, c);
            n._dirty = true;
        });
        this._notify();
        return true;
    }
    
    willItFit(node) {
        delete node._willFitPos;
        if (!this.maxRow)
            return true;
        
        let clone = new GridStackEngine({
            column: this.column,
            float: this.float,
            nodes: this.nodes.map(n => { return Object.assign({}, n); })
        });
        let n = Object.assign({}, node); 
        this.cleanupNode(n);
        delete n.el;
        delete n._id;
        delete n.content;
        delete n.grid;
        clone.addNode(n);
        if (clone.getRow() <= this.maxRow) {
            node._willFitPos = utils_1.Utils.copyPos({}, n);
            return true;
        }
        return false;
    }
    
    changedPosConstrain(node, p) {
        
        p.w = p.w || node.w;
        p.h = p.h || node.h;
        if (node.x !== p.x || node.y !== p.y)
            return true;
        
        if (node.maxW) {
            p.w = Math.min(p.w, node.maxW);
        }
        if (node.maxH) {
            p.h = Math.min(p.h, node.maxH);
        }
        if (node.minW) {
            p.w = Math.max(p.w, node.minW);
        }
        if (node.minH) {
            p.h = Math.max(p.h, node.minH);
        }
        return (node.w !== p.w || node.h !== p.h);
    }
    
    moveNode(node, o) {
        if (!node ||  !o)
            return false;
        if (o.pack === undefined)
            o.pack = true;
        
        if (typeof o.x !== 'number') {
            o.x = node.x;
        }
        if (typeof o.y !== 'number') {
            o.y = node.y;
        }
        if (typeof o.w !== 'number') {
            o.w = node.w;
        }
        if (typeof o.h !== 'number') {
            o.h = node.h;
        }
        let resizing = (node.w !== o.w || node.h !== o.h);
        let nn = utils_1.Utils.copyPos({}, node, true); 
        utils_1.Utils.copyPos(nn, o);
        nn = this.nodeBoundFix(nn, resizing);
        utils_1.Utils.copyPos(o, nn);
        if (utils_1.Utils.samePos(node, o))
            return false;
        let prevPos = utils_1.Utils.copyPos({}, node);
        
        let collides = this.collideAll(node, nn, o.skip);
        let needToMove = true;
        if (collides.length) {
            
            let collide = node._moving && !o.nested ? this.collideCoverage(node, o, collides) : collides[0];
            if (collide) {
                needToMove = !this._fixCollisions(node, nn, collide, o); 
            }
            else {
                needToMove = false; 
            }
        }
        
        if (needToMove) {
            node._dirty = true;
            utils_1.Utils.copyPos(node, nn);
        }
        if (o.pack) {
            this._packNodes()
                ._notify();
        }
        return !utils_1.Utils.samePos(node, prevPos); 
    }
    getRow() {
        return this.nodes.reduce((row, n) => Math.max(row, n.y + n.h), 0);
    }
    beginUpdate(node) {
        if (!node._updating) {
            node._updating = true;
            delete node._skipDown;
            if (!this.batchMode)
                this.saveInitial();
        }
        return this;
    }
    endUpdate() {
        let n = this.nodes.find(n => n._updating);
        if (n) {
            delete n._updating;
            delete n._skipDown;
        }
        return this;
    }
    
    save(saveElement = true) {
        var _a;
        
        let len = (_a = this._layouts) === null || _a === void 0 ? void 0 : _a.length;
        let layout = len && this.column !== (len - 1) ? this._layouts[len - 1] : null;
        let list = [];
        this.sortNodes();
        this.nodes.forEach(n => {
            let wl = layout === null || layout === void 0 ? void 0 : layout.find(l => l._id === n._id);
            let w = Object.assign({}, n);
            
            if (wl) {
                w.x = wl.x;
                w.y = wl.y;
                w.w = wl.w;
            }
            
            for (let key in w) {
                if (key[0] === '_' || w[key] === null || w[key] === undefined)
                    delete w[key];
            }
            delete w.grid;
            if (!saveElement)
                delete w.el;
            
            if (!w.autoPosition)
                delete w.autoPosition;
            if (!w.noResize)
                delete w.noResize;
            if (!w.noMove)
                delete w.noMove;
            if (!w.locked)
                delete w.locked;
            list.push(w);
        });
        return list;
    }
    
    layoutsNodesChange(nodes) {
        if (!this._layouts || this._inColumnResize)
            return this;
        
        this._layouts.forEach((layout, column) => {
            if (!layout || column === this.column)
                return this;
            if (column < this.column) {
                this._layouts[column] = undefined;
            }
            else {
                
                
                let ratio = column / this.column;
                nodes.forEach(node => {
                    if (!node._orig)
                        return; 
                    let n = layout.find(l => l._id === node._id);
                    if (!n)
                        return; 
                    
                    
                    if (node.y !== node._orig.y) {
                        n.y += (node.y - node._orig.y);
                    }
                    
                    if (node.x !== node._orig.x) {
                        n.x = Math.round(node.x * ratio);
                    }
                    
                    if (node.w !== node._orig.w) {
                        n.w = Math.round(node.w * ratio);
                    }
                    
                });
            }
        });
        return this;
    }
    
    updateNodeWidths(prevColumn, column, nodes, layout = 'moveScale') {
        var _a;
        if (!this.nodes.length || !column || prevColumn === column)
            return this;
        
        this.cacheLayout(this.nodes, prevColumn);
        this.batchUpdate(); 
        let newNodes = [];
        
        let domOrder = false;
        if (column === 1 && (nodes === null || nodes === void 0 ? void 0 : nodes.length)) {
            domOrder = true;
            let top = 0;
            nodes.forEach(n => {
                n.x = 0;
                n.w = 1;
                n.y = Math.max(n.y, top);
                top = n.y + n.h;
            });
            newNodes = nodes;
            nodes = [];
        }
        else {
            nodes = utils_1.Utils.sort(this.nodes, -1, prevColumn); 
        }
        
        
        let cacheNodes = [];
        if (column > prevColumn) {
            cacheNodes = this._layouts[column] || [];
            
            
            let lastIndex = this._layouts.length - 1;
            if (!cacheNodes.length && prevColumn !== lastIndex && ((_a = this._layouts[lastIndex]) === null || _a === void 0 ? void 0 : _a.length)) {
                prevColumn = lastIndex;
                this._layouts[lastIndex].forEach(cacheNode => {
                    let n = nodes.find(n => n._id === cacheNode._id);
                    if (n) {
                        
                        n.x = cacheNode.x;
                        n.y = cacheNode.y;
                        n.w = cacheNode.w;
                    }
                });
            }
        }
        
        cacheNodes.forEach(cacheNode => {
            let j = nodes.findIndex(n => n._id === cacheNode._id);
            if (j !== -1) {
                
                nodes[j].x = cacheNode.x;
                nodes[j].y = cacheNode.y;
                nodes[j].w = cacheNode.w;
                newNodes.push(nodes[j]);
                nodes.splice(j, 1);
            }
        });
        
        if (nodes.length) {
            if (typeof layout === 'function') {
                layout(column, prevColumn, newNodes, nodes);
            }
            else if (!domOrder) {
                let ratio = column / prevColumn;
                let move = (layout === 'move' || layout === 'moveScale');
                let scale = (layout === 'scale' || layout === 'moveScale');
                nodes.forEach(node => {
                    
                    node.x = (column === 1 ? 0 : (move ? Math.round(node.x * ratio) : Math.min(node.x, column - 1)));
                    node.w = ((column === 1 || prevColumn === 1) ? 1 :
                        scale ? (Math.round(node.w * ratio) || 1) : (Math.min(node.w, column)));
                    newNodes.push(node);
                });
                nodes = [];
            }
        }
        
        newNodes = utils_1.Utils.sort(newNodes, -1, column);
        this._inColumnResize = true; 
        this.nodes = []; 
        newNodes.forEach(node => {
            this.addNode(node, false); 
            delete node._orig; 
        });
        this.commit();
        delete this._inColumnResize;
        return this;
    }
    
    cacheLayout(nodes, column, clear = false) {
        let copy = [];
        nodes.forEach((n, i) => {
            n._id = n._id || GridStackEngine._idSeq++; 
            copy[i] = { x: n.x, y: n.y, w: n.w, _id: n._id }; 
        });
        this._layouts = clear ? [] : this._layouts || []; 
        this._layouts[column] = copy;
        return this;
    }
    
    cacheOneLayout(n, column) {
        n._id = n._id || GridStackEngine._idSeq++;
        let layout = { x: n.x, y: n.y, w: n.w, _id: n._id };
        this._layouts = this._layouts || [];
        this._layouts[column] = this._layouts[column] || [];
        let index = this._layouts[column].findIndex(l => l._id === n._id);
        index === -1 ? this._layouts[column].push(layout) : this._layouts[column][index] = layout;
        return this;
    }
    
    cleanupNode(node) {
        for (let prop in node) {
            if (prop[0] === '_' && prop !== '_id')
                delete node[prop];
        }
        return this;
    }
}
exports.GridStackEngine = GridStackEngine;

GridStackEngine._idSeq = 1;


 }),

 572:
 (function(__unused_webpack_module, exports, __webpack_require__) {



var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    Object.defineProperty(o, k2, { enumerable: true, get: function() { return m[k]; } });
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __exportStar = (this && this.__exportStar) || function(m, exports) {
    for (var p in m) if (p !== "default" && !exports.hasOwnProperty(p)) __createBinding(exports, m, p);
};
Object.defineProperty(exports, "__esModule", ({ value: true }));
__exportStar(__webpack_require__(699), exports);
__exportStar(__webpack_require__(593), exports);
__exportStar(__webpack_require__(62), exports);
__exportStar(__webpack_require__(334), exports);
__exportStar(__webpack_require__(270), exports);
__exportStar(__webpack_require__(906), exports);



 }),

 270:
 (function(__unused_webpack_module, exports, __webpack_require__) {


var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    Object.defineProperty(o, k2, { enumerable: true, get: function() { return m[k]; } });
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __exportStar = (this && this.__exportStar) || function(m, exports) {
    for (var p in m) if (p !== "default" && !exports.hasOwnProperty(p)) __createBinding(exports, m, p);
};
Object.defineProperty(exports, "__esModule", ({ value: true }));
exports.GridStack = void 0;

const gridstack_engine_1 = __webpack_require__(62);
const utils_1 = __webpack_require__(593);
const gridstack_ddi_1 = __webpack_require__(334);

__exportStar(__webpack_require__(699), exports);
__exportStar(__webpack_require__(593), exports);
__exportStar(__webpack_require__(62), exports);
__exportStar(__webpack_require__(334), exports);

const GridDefaults = {
    column: 12,
    minRow: 0,
    maxRow: 0,
    itemClass: 'grid-stack-item',
    placeholderClass: 'grid-stack-placeholder',
    placeholderText: '',
    handle: '.grid-stack-item-content',
    handleClass: null,
    styleInHead: false,
    cellHeight: 'auto',
    cellHeightThrottle: 100,
    margin: 10,
    auto: true,
    oneColumnSize: 768,
    float: false,
    staticGrid: false,
    animate: true,
    alwaysShowResizeHandle: false,
    resizable: {
        autoHide: true,
        handles: 'se'
    },
    draggable: {
        handle: '.grid-stack-item-content',
        scroll: false,
        appendTo: 'body'
    },
    disableDrag: false,
    disableResize: false,
    rtl: 'auto',
    removable: false,
    removableOptions: {
        accept: '.grid-stack-item'
    },
    marginUnit: 'px',
    cellHeightUnit: 'px',
    disableOneColumnMode: false,
    oneColumnModeDomSort: false
};

class GridStack {
    
    constructor(el, opts = {}) {
        
        this._gsEventHandler = {};
        
        this._extraDragRow = 0;
        this.el = el; 
        opts = opts || {}; 
        
        if (opts.row) {
            opts.minRow = opts.maxRow = opts.row;
            delete opts.row;
        }
        let rowAttr = utils_1.Utils.toNumber(el.getAttribute('gs-row'));
        
        if (opts.column === 'auto') {
            delete opts.column;
        }
        
        
        let anyOpts = opts;
        if (anyOpts.minWidth !== undefined) {
            opts.oneColumnSize = opts.oneColumnSize || anyOpts.minWidth;
            delete anyOpts.minWidth;
        }
        
        let defaults = Object.assign(Object.assign({}, utils_1.Utils.cloneDeep(GridDefaults)), { column: utils_1.Utils.toNumber(el.getAttribute('gs-column')) || 12, minRow: rowAttr ? rowAttr : utils_1.Utils.toNumber(el.getAttribute('gs-min-row')) || 0, maxRow: rowAttr ? rowAttr : utils_1.Utils.toNumber(el.getAttribute('gs-max-row')) || 0, staticGrid: utils_1.Utils.toBool(el.getAttribute('gs-static')) || false, _styleSheetClass: 'grid-stack-instance-' + (Math.random() * 10000).toFixed(0), alwaysShowResizeHandle: opts.alwaysShowResizeHandle || false, resizable: {
                autoHide: !(opts.alwaysShowResizeHandle || false),
                handles: 'se'
            }, draggable: {
                handle: (opts.handleClass ? '.' + opts.handleClass : (opts.handle ? opts.handle : '')) || '.grid-stack-item-content',
                scroll: false,
                appendTo: 'body'
            }, removableOptions: {
                accept: '.' + (opts.itemClass || 'grid-stack-item')
            } });
        if (el.getAttribute('gs-animate')) { 
            defaults.animate = utils_1.Utils.toBool(el.getAttribute('gs-animate'));
        }
        this.opts = utils_1.Utils.defaults(opts, defaults);
        opts = null; 
        this._initMargin(); 
        
        if (this.opts.column !== 1 && !this.opts.disableOneColumnMode && this._widthOrContainer() <= this.opts.oneColumnSize) {
            this._prevColumn = this.getColumn();
            this.opts.column = 1;
        }
        if (this.opts.rtl === 'auto') {
            this.opts.rtl = (el.style.direction === 'rtl');
        }
        if (this.opts.rtl) {
            this.el.classList.add('grid-stack-rtl');
        }
        
        let parentGridItemEl = utils_1.Utils.closestByClass(this.el, GridDefaults.itemClass);
        if (parentGridItemEl && parentGridItemEl.gridstackNode) {
            this.opts._isNested = parentGridItemEl.gridstackNode;
            this.opts._isNested.subGrid = this;
            parentGridItemEl.classList.add('grid-stack-nested');
            this.el.classList.add('grid-stack-nested');
        }
        this._isAutoCellHeight = (this.opts.cellHeight === 'auto');
        if (this._isAutoCellHeight || this.opts.cellHeight === 'initial') {
            
            this.cellHeight(undefined, false);
        }
        else {
            
            if (typeof this.opts.cellHeight == 'number' && this.opts.cellHeightUnit && this.opts.cellHeightUnit !== GridDefaults.cellHeightUnit) {
                this.opts.cellHeight = this.opts.cellHeight + this.opts.cellHeightUnit;
                delete this.opts.cellHeightUnit;
            }
            this.cellHeight(this.opts.cellHeight, false);
        }
        this.el.classList.add(this.opts._styleSheetClass);
        this._setStaticClass();
        let engineClass = this.opts.engineClass || GridStack.engineClass || gridstack_engine_1.GridStackEngine;
        this.engine = new engineClass({
            column: this.getColumn(),
            float: this.opts.float,
            maxRow: this.opts.maxRow,
            onChange: (cbNodes) => {
                let maxH = 0;
                this.engine.nodes.forEach(n => { maxH = Math.max(maxH, n.y + n.h); });
                cbNodes.forEach(n => {
                    let el = n.el;
                    if (!el)
                        return;
                    if (n._removeDOM) {
                        if (el)
                            el.remove();
                        delete n._removeDOM;
                    }
                    else {
                        this._writePosAttr(el, n);
                    }
                });
                this._updateStyles(false, maxH); 
            }
        });
        if (this.opts.auto) {
            this.batchUpdate(); 
            let elements = [];
            this.getGridItems().forEach(el => {
                let x = parseInt(el.getAttribute('gs-x'));
                let y = parseInt(el.getAttribute('gs-y'));
                elements.push({
                    el,
                    
                    i: (Number.isNaN(x) ? 1000 : x) + (Number.isNaN(y) ? 1000 : y) * this.getColumn()
                });
            });
            elements.sort((a, b) => a.i - b.i).forEach(e => this._prepareElement(e.el));
            this.commit();
        }
        this.setAnimation(this.opts.animate);
        this._updateStyles();
        if (this.opts.column != 12) {
            this.el.classList.add('grid-stack-' + this.opts.column);
        }
        
        if (this.opts.dragIn)
            GridStack.setupDragIn(this.opts.dragIn, this.opts.dragInOptions);
        delete this.opts.dragIn;
        delete this.opts.dragInOptions;
        this._setupRemoveDrop();
        this._setupAcceptWidget();
        this._updateWindowResizeEvent();
    }
    
    static init(options = {}, elOrString = '.grid-stack') {
        let el = GridStack.getGridElement(elOrString);
        if (!el) {
            if (typeof elOrString === 'string') {
                console.error('GridStack.initAll() no grid was found with selector "' + elOrString + '" - element missing or wrong selector ?' +
                    '\nNote: ".grid-stack" is required for proper CSS styling and drag/drop, and is the default selector.');
            }
            else {
                console.error('GridStack.init() no grid element was passed.');
            }
            return null;
        }
        if (!el.gridstack) {
            el.gridstack = new GridStack(el, utils_1.Utils.cloneDeep(options));
        }
        return el.gridstack;
    }
    
    static initAll(options = {}, selector = '.grid-stack') {
        let grids = [];
        GridStack.getGridElements(selector).forEach(el => {
            if (!el.gridstack) {
                el.gridstack = new GridStack(el, utils_1.Utils.cloneDeep(options));
                delete options.dragIn;
                delete options.dragInOptions; 
            }
            grids.push(el.gridstack);
        });
        if (grids.length === 0) {
            console.error('GridStack.initAll() no grid was found with selector "' + selector + '" - element missing or wrong selector ?' +
                '\nNote: ".grid-stack" is required for proper CSS styling and drag/drop, and is the default selector.');
        }
        return grids;
    }
    
    static addGrid(parent, opt = {}) {
        if (!parent)
            return null;
        
        let el = parent;
        if (!parent.classList.contains('grid-stack')) {
            let doc = document.implementation.createHTMLDocument(''); 
            doc.body.innerHTML = `<div class="grid-stack ${opt.class || ''}"></div>`;
            el = doc.body.children[0];
            parent.appendChild(el);
        }
        
        let grid = GridStack.init(opt, el);
        if (grid.opts.children) {
            let children = grid.opts.children;
            delete grid.opts.children;
            grid.load(children);
        }
        return grid;
    }
    
    static registerEngine(engineClass) {
        GridStack.engineClass = engineClass;
    }
    
    get placeholder() {
        if (!this._placeholder) {
            let placeholderChild = document.createElement('div'); 
            placeholderChild.className = 'placeholder-content';
            if (this.opts.placeholderText) {
                placeholderChild.innerHTML = this.opts.placeholderText;
            }
            this._placeholder = document.createElement('div');
            this._placeholder.classList.add(this.opts.placeholderClass, GridDefaults.itemClass, this.opts.itemClass);
            this.placeholder.appendChild(placeholderChild);
        }
        return this._placeholder;
    }
    
    addWidget(els, options) {
        
        if (arguments.length > 2) {
            console.warn('gridstack.ts: `addWidget(el, x, y, width...)` is deprecated. Use `addWidget({x, y, w, content, ...})`. It will be removed soon');
            
            let a = arguments, i = 1, opt = { x: a[i++], y: a[i++], w: a[i++], h: a[i++], autoPosition: a[i++],
                minW: a[i++], maxW: a[i++], minH: a[i++], maxH: a[i++], id: a[i++] };
            return this.addWidget(els, opt);
        }
        function isGridStackWidget(w) {
            return w.x !== undefined || w.y !== undefined || w.w !== undefined || w.h !== undefined || w.content !== undefined ? true : false;
        }
        let el;
        if (typeof els === 'string') {
            let doc = document.implementation.createHTMLDocument(''); 
            doc.body.innerHTML = els;
            el = doc.body.children[0];
        }
        else if (arguments.length === 0 || arguments.length === 1 && isGridStackWidget(els)) {
            let content = els ? els.content || '' : '';
            options = els;
            let doc = document.implementation.createHTMLDocument(''); 
            doc.body.innerHTML = `<div class="grid-stack-item ${this.opts.itemClass || ''}"><div class="grid-stack-item-content">${content}</div></div>`;
            el = doc.body.children[0];
        }
        else {
            el = els;
        }
        
        
        
        let domAttr = this._readAttr(el);
        options = utils_1.Utils.cloneDeep(options) || {}; 
        utils_1.Utils.defaults(options, domAttr);
        let node = this.engine.prepareNode(options);
        this._writeAttr(el, options);
        if (this._insertNotAppend) {
            this.el.prepend(el);
        }
        else {
            this.el.appendChild(el);
        }
        
        this._prepareElement(el, true, options);
        this._updateContainerHeight();
        
        if (node.subGrid && !node.subGrid.el) { 
            
            let autoColumn;
            let ops = node.subGrid;
            if (ops.column === 'auto') {
                ops.column = node.w;
                ops.disableOneColumnMode = true; 
                autoColumn = true;
            }
            let content = node.el.querySelector('.grid-stack-item-content');
            node.subGrid = GridStack.addGrid(content, node.subGrid);
            if (autoColumn) {
                node.subGrid._autoColumn = true;
            }
        }
        this._triggerAddEvent();
        this._triggerChangeEvent();
        return el;
    }
    
    save(saveContent = true, saveGridOpt = false) {
        
        let list = this.engine.save(saveContent);
        
        list.forEach(n => {
            if (saveContent && n.el && !n.subGrid) { 
                let sub = n.el.querySelector('.grid-stack-item-content');
                n.content = sub ? sub.innerHTML : undefined;
                if (!n.content)
                    delete n.content;
            }
            else {
                if (!saveContent) {
                    delete n.content;
                }
                
                if (n.subGrid) {
                    n.subGrid = n.subGrid.save(saveContent, true);
                }
            }
            delete n.el;
        });
        
        if (saveGridOpt) {
            let o = utils_1.Utils.cloneDeep(this.opts);
            
            if (o.marginBottom === o.marginTop && o.marginRight === o.marginLeft && o.marginTop === o.marginRight) {
                o.margin = o.marginTop;
                delete o.marginTop;
                delete o.marginRight;
                delete o.marginBottom;
                delete o.marginLeft;
            }
            if (o.rtl === (this.el.style.direction === 'rtl')) {
                o.rtl = 'auto';
            }
            if (this._isAutoCellHeight) {
                o.cellHeight = 'auto';
            }
            if (this._autoColumn) {
                o.column = 'auto';
                delete o.disableOneColumnMode;
            }
            utils_1.Utils.removeInternalAndSame(o, GridDefaults);
            o.children = list;
            return o;
        }
        return list;
    }
    
    load(layout, addAndRemove = true) {
        let items = GridStack.Utils.sort([...layout], -1, this._prevColumn || this.getColumn()); 
        this._insertNotAppend = true; 
        
        
        if (this._prevColumn && this._prevColumn !== this.opts.column && items.some(n => (n.x + n.w) > this.opts.column)) {
            this._ignoreLayoutsNodeChange = true; 
            this.engine.cacheLayout(items, this._prevColumn, true);
        }
        let removed = [];
        this.batchUpdate();
        
        if (addAndRemove) {
            let copyNodes = [...this.engine.nodes]; 
            copyNodes.forEach(n => {
                let item = items.find(w => n.id === w.id);
                if (!item) {
                    if (typeof (addAndRemove) === 'function') {
                        addAndRemove(this, n, false);
                    }
                    else {
                        removed.push(n); 
                        this.removeWidget(n.el, true, false);
                    }
                }
            });
        }
        
        items.forEach(w => {
            let item = (w.id || w.id === 0) ? this.engine.nodes.find(n => n.id === w.id) : undefined;
            if (item) {
                this.update(item.el, w);
                if (w.subGrid && w.subGrid.children) { 
                    let sub = item.el.querySelector('.grid-stack');
                    if (sub && sub.gridstack) {
                        sub.gridstack.load(w.subGrid.children); 
                        this._insertNotAppend = true; 
                    }
                }
            }
            else if (addAndRemove) {
                if (typeof (addAndRemove) === 'function') {
                    w = addAndRemove(this, w, true).gridstackNode;
                }
                else {
                    w = this.addWidget(w).gridstackNode;
                }
            }
        });
        this.engine.removedNodes = removed;
        this.commit();
        
        delete this._ignoreLayoutsNodeChange;
        delete this._insertNotAppend;
        return this;
    }
    
    batchUpdate() {
        this.engine.batchUpdate();
        return this;
    }
    
    getCellHeight(forcePixel = false) {
        if (this.opts.cellHeight && this.opts.cellHeight !== 'auto' &&
            (!forcePixel || !this.opts.cellHeightUnit || this.opts.cellHeightUnit === 'px')) {
            return this.opts.cellHeight;
        }
        
        let el = this.el.querySelector('.' + this.opts.itemClass);
        if (el) {
            let height = utils_1.Utils.toNumber(el.getAttribute('gs-h'));
            return Math.round(el.offsetHeight / height);
        }
        
        let rows = parseInt(this.el.getAttribute('gs-current-row'));
        return rows ? Math.round(this.el.getBoundingClientRect().height / rows) : this.opts.cellHeight;
    }
    
    cellHeight(val, update = true) {
        
        if (update && val !== undefined) {
            if (this._isAutoCellHeight !== (val === 'auto')) {
                this._isAutoCellHeight = (val === 'auto');
                this._updateWindowResizeEvent();
            }
        }
        if (val === 'initial' || val === 'auto') {
            val = undefined;
        }
        
        if (val === undefined) {
            let marginDiff = -this.opts.marginRight - this.opts.marginLeft
                + this.opts.marginTop + this.opts.marginBottom;
            val = this.cellWidth() + marginDiff;
        }
        let data = utils_1.Utils.parseHeight(val);
        if (this.opts.cellHeightUnit === data.unit && this.opts.cellHeight === data.h) {
            return this;
        }
        this.opts.cellHeightUnit = data.unit;
        this.opts.cellHeight = data.h;
        if (update) {
            this._updateStyles(true, this.getRow()); 
        }
        return this;
    }
    
    cellWidth() {
        return this._widthOrContainer() / this.getColumn();
    }
    
    _widthOrContainer() {
        
        
        return (this.el.clientWidth || this.el.parentElement.clientWidth || window.innerWidth);
    }
    
    commit() {
        this.engine.commit();
        this._triggerRemoveEvent();
        this._triggerAddEvent();
        this._triggerChangeEvent();
        return this;
    }
    
    compact() {
        this.engine.compact();
        this._triggerChangeEvent();
        return this;
    }
    
    column(column, layout = 'moveScale') {
        if (column < 1 || this.opts.column === column)
            return this;
        let oldColumn = this.getColumn();
        
        
        if (column === 1) {
            this._prevColumn = oldColumn;
        }
        else {
            delete this._prevColumn;
        }
        this.el.classList.remove('grid-stack-' + oldColumn);
        this.el.classList.add('grid-stack-' + column);
        this.opts.column = this.engine.column = column;
        
        let domNodes;
        if (column === 1 && this.opts.oneColumnModeDomSort) {
            domNodes = [];
            this.getGridItems().forEach(el => {
                if (el.gridstackNode) {
                    domNodes.push(el.gridstackNode);
                }
            });
            if (!domNodes.length) {
                domNodes = undefined;
            }
        }
        this.engine.updateNodeWidths(oldColumn, column, domNodes, layout);
        if (this._isAutoCellHeight)
            this.cellHeight();
        
        this._ignoreLayoutsNodeChange = true; 
        this._triggerChangeEvent();
        delete this._ignoreLayoutsNodeChange;
        return this;
    }
    
    getColumn() {
        return this.opts.column;
    }
    
    getGridItems() {
        return Array.from(this.el.children)
            .filter((el) => el.matches('.' + this.opts.itemClass) && !el.matches('.' + this.opts.placeholderClass));
    }
    
    destroy(removeDOM = true) {
        if (!this.el)
            return; 
        this._updateWindowResizeEvent(true);
        this.setStatic(true, false); 
        this.setAnimation(false);
        if (!removeDOM) {
            this.removeAll(removeDOM);
            this.el.classList.remove(this.opts._styleSheetClass);
        }
        else {
            this.el.parentNode.removeChild(this.el);
        }
        this._removeStylesheet();
        this.el.removeAttribute('gs-current-row');
        delete this.opts._isNested;
        delete this.opts;
        delete this._placeholder;
        delete this.engine;
        delete this.el.gridstack; 
        delete this.el;
        return this;
    }
    
    float(val) {
        this.engine.float = val;
        this._triggerChangeEvent();
        return this;
    }
    
    getFloat() {
        return this.engine.float;
    }
    
    getCellFromPixel(position, useDocRelative = false) {
        let box = this.el.getBoundingClientRect();
        
        let containerPos;
        if (useDocRelative) {
            containerPos = { top: box.top + document.documentElement.scrollTop, left: box.left };
            
        }
        else {
            containerPos = { top: this.el.offsetTop, left: this.el.offsetLeft };
            
        }
        let relativeLeft = position.left - containerPos.left;
        let relativeTop = position.top - containerPos.top;
        let columnWidth = (box.width / this.getColumn());
        let rowHeight = (box.height / parseInt(this.el.getAttribute('gs-current-row')));
        return { x: Math.floor(relativeLeft / columnWidth), y: Math.floor(relativeTop / rowHeight) };
    }
    
    getRow() {
        return Math.max(this.engine.getRow(), this.opts.minRow);
    }
    
    isAreaEmpty(x, y, w, h) {
        return this.engine.isAreaEmpty(x, y, w, h);
    }
    
    makeWidget(els) {
        let el = GridStack.getElement(els);
        this._prepareElement(el, true);
        this._updateContainerHeight();
        this._triggerAddEvent();
        this._triggerChangeEvent();
        return el;
    }
    
    on(name, callback) {
        
        if (name.indexOf(' ') !== -1) {
            let names = name.split(' ');
            names.forEach(name => this.on(name, callback));
            return this;
        }
        if (name === 'change' || name === 'added' || name === 'removed' || name === 'enable' || name === 'disable') {
            
            let noData = (name === 'enable' || name === 'disable');
            if (noData) {
                this._gsEventHandler[name] = (event) => callback(event);
            }
            else {
                this._gsEventHandler[name] = (event) => callback(event, event.detail);
            }
            this.el.addEventListener(name, this._gsEventHandler[name]);
        }
        else if (name === 'drag' || name === 'dragstart' || name === 'dragstop' || name === 'resizestart' || name === 'resize' || name === 'resizestop' || name === 'dropped') {
            
            
            this._gsEventHandler[name] = callback;
        }
        else {
            console.log('GridStack.on(' + name + ') event not supported, but you can still use $(".grid-stack").on(...) while jquery-ui is still used internally.');
        }
        return this;
    }
    
    off(name) {
        
        if (name.indexOf(' ') !== -1) {
            let names = name.split(' ');
            names.forEach(name => this.off(name));
            return this;
        }
        if (name === 'change' || name === 'added' || name === 'removed' || name === 'enable' || name === 'disable') {
            
            if (this._gsEventHandler[name]) {
                this.el.removeEventListener(name, this._gsEventHandler[name]);
            }
        }
        delete this._gsEventHandler[name];
        return this;
    }
    
    removeWidget(els, removeDOM = true, triggerEvent = true) {
        GridStack.getElements(els).forEach(el => {
            if (el.parentElement !== this.el)
                return; 
            let node = el.gridstackNode;
            
            if (!node) {
                node = this.engine.nodes.find(n => el === n.el);
            }
            if (!node)
                return;
            
            delete el.gridstackNode;
            gridstack_ddi_1.GridStackDDI.get().remove(el);
            this.engine.removeNode(node, removeDOM, triggerEvent);
            if (removeDOM && el.parentElement) {
                el.remove(); 
            }
        });
        if (triggerEvent) {
            this._triggerRemoveEvent();
            this._triggerChangeEvent();
        }
        return this;
    }
    
    removeAll(removeDOM = true) {
        
        this.engine.nodes.forEach(n => {
            delete n.el.gridstackNode;
            gridstack_ddi_1.GridStackDDI.get().remove(n.el);
        });
        this.engine.removeAll(removeDOM);
        this._triggerRemoveEvent();
        return this;
    }
    
    setAnimation(doAnimate) {
        if (doAnimate) {
            this.el.classList.add('grid-stack-animate');
        }
        else {
            this.el.classList.remove('grid-stack-animate');
        }
        return this;
    }
    
    setStatic(val, updateClass = true) {
        if (this.opts.staticGrid === val)
            return this;
        this.opts.staticGrid = val;
        this._setupRemoveDrop();
        this._setupAcceptWidget();
        this.engine.nodes.forEach(n => this._prepareDragDropByNode(n)); 
        if (updateClass) {
            this._setStaticClass();
        }
        return this;
    }
    
    update(els, opt) {
        
        if (arguments.length > 2) {
            console.warn('gridstack.ts: `update(el, x, y, w, h)` is deprecated. Use `update(el, {x, w, content, ...})`. It will be removed soon');
            
            let a = arguments, i = 1;
            opt = { x: a[i++], y: a[i++], w: a[i++], h: a[i++] };
            return this.update(els, opt);
        }
        GridStack.getElements(els).forEach(el => {
            if (!el || !el.gridstackNode)
                return;
            let n = el.gridstackNode;
            let w = utils_1.Utils.cloneDeep(opt); 
            delete w.autoPosition;
            
            let keys = ['x', 'y', 'w', 'h'];
            let m;
            if (keys.some(k => w[k] !== undefined && w[k] !== n[k])) {
                m = {};
                keys.forEach(k => {
                    m[k] = (w[k] !== undefined) ? w[k] : n[k];
                    delete w[k];
                });
            }
            
            if (!m && (w.minW || w.minH || w.maxW || w.maxH)) {
                m = {}; 
            }
            
            if (w.content) {
                let sub = el.querySelector('.grid-stack-item-content');
                if (sub && sub.innerHTML !== w.content) {
                    sub.innerHTML = w.content;
                }
                delete w.content;
            }
            
            let changed = false;
            let ddChanged = false;
            for (const key in w) {
                if (key[0] !== '_' && n[key] !== w[key]) {
                    n[key] = w[key];
                    changed = true;
                    ddChanged = ddChanged || (!this.opts.staticGrid && (key === 'noResize' || key === 'noMove' || key === 'locked'));
                }
            }
            
            if (m) {
                this.engine.cleanNodes()
                    .beginUpdate(n)
                    .moveNode(n, m);
                this._updateContainerHeight();
                this._triggerChangeEvent();
                this.engine.endUpdate();
            }
            if (changed) { 
                this._writeAttr(el, n);
            }
            if (ddChanged) {
                this._prepareDragDropByNode(n);
            }
        });
        return this;
    }
    
    margin(value) {
        let isMultiValue = (typeof value === 'string' && value.split(' ').length > 1);
        
        if (!isMultiValue) {
            let data = utils_1.Utils.parseHeight(value);
            if (this.opts.marginUnit === data.unit && this.opts.margin === data.h)
                return;
        }
        
        this.opts.margin = value;
        this.opts.marginTop = this.opts.marginBottom = this.opts.marginLeft = this.opts.marginRight = undefined;
        this._initMargin();
        this._updateStyles(true); 
        return this;
    }
    
    getMargin() { return this.opts.margin; }
    
    willItFit(node) {
        
        if (arguments.length > 1) {
            console.warn('gridstack.ts: `willItFit(x,y,w,h,autoPosition)` is deprecated. Use `willItFit({x, y,...})`. It will be removed soon');
            
            let a = arguments, i = 0, w = { x: a[i++], y: a[i++], w: a[i++], h: a[i++], autoPosition: a[i++] };
            return this.willItFit(w);
        }
        return this.engine.willItFit(node);
    }
    
    _triggerChangeEvent() {
        if (this.engine.batchMode)
            return this;
        let elements = this.engine.getDirtyNodes(true); 
        if (elements && elements.length) {
            if (!this._ignoreLayoutsNodeChange) {
                this.engine.layoutsNodesChange(elements);
            }
            this._triggerEvent('change', elements);
        }
        this.engine.saveInitial(); 
        return this;
    }
    
    _triggerAddEvent() {
        if (this.engine.batchMode)
            return this;
        if (this.engine.addedNodes && this.engine.addedNodes.length > 0) {
            if (!this._ignoreLayoutsNodeChange) {
                this.engine.layoutsNodesChange(this.engine.addedNodes);
            }
            
            this.engine.addedNodes.forEach(n => { delete n._dirty; });
            this._triggerEvent('added', this.engine.addedNodes);
            this.engine.addedNodes = [];
        }
        return this;
    }
    
    _triggerRemoveEvent() {
        if (this.engine.batchMode)
            return this;
        if (this.engine.removedNodes && this.engine.removedNodes.length > 0) {
            this._triggerEvent('removed', this.engine.removedNodes);
            this.engine.removedNodes = [];
        }
        return this;
    }
    
    _triggerEvent(name, data) {
        let event = data ? new CustomEvent(name, { bubbles: false, detail: data }) : new Event(name);
        this.el.dispatchEvent(event);
        return this;
    }
    
    _removeStylesheet() {
        if (this._styles) {
            utils_1.Utils.removeStylesheet(this._styles._id);
            delete this._styles;
        }
        return this;
    }
    
    _updateStyles(forceUpdate = false, maxH) {
        
        if (forceUpdate) {
            this._removeStylesheet();
        }
        this._updateContainerHeight();
        
        if (this.opts.cellHeight === 0) {
            return this;
        }
        let cellHeight = this.opts.cellHeight;
        let cellHeightUnit = this.opts.cellHeightUnit;
        let prefix = `.${this.opts._styleSheetClass} > .${this.opts.itemClass}`;
        
        if (!this._styles) {
            let id = 'gridstack-style-' + (Math.random() * 100000).toFixed();
            
            let styleLocation = this.opts.styleInHead ? undefined : this.el.parentNode;
            this._styles = utils_1.Utils.createStylesheet(id, styleLocation);
            if (!this._styles)
                return this;
            this._styles._id = id;
            this._styles._max = 0;
            
            utils_1.Utils.addCSSRule(this._styles, prefix, `min-height: ${cellHeight}${cellHeightUnit}`);
            
            let top = this.opts.marginTop + this.opts.marginUnit;
            let bottom = this.opts.marginBottom + this.opts.marginUnit;
            let right = this.opts.marginRight + this.opts.marginUnit;
            let left = this.opts.marginLeft + this.opts.marginUnit;
            let content = `${prefix} > .grid-stack-item-content`;
            let placeholder = `.${this.opts._styleSheetClass} > .grid-stack-placeholder > .placeholder-content`;
            utils_1.Utils.addCSSRule(this._styles, content, `top: ${top}; right: ${right}; bottom: ${bottom}; left: ${left};`);
            utils_1.Utils.addCSSRule(this._styles, placeholder, `top: ${top}; right: ${right}; bottom: ${bottom}; left: ${left};`);
            
            utils_1.Utils.addCSSRule(this._styles, `${prefix} > .ui-resizable-ne`, `right: ${right}`);
            utils_1.Utils.addCSSRule(this._styles, `${prefix} > .ui-resizable-e`, `right: ${right}`);
            utils_1.Utils.addCSSRule(this._styles, `${prefix} > .ui-resizable-se`, `right: ${right}; bottom: ${bottom}`);
            utils_1.Utils.addCSSRule(this._styles, `${prefix} > .ui-resizable-nw`, `left: ${left}`);
            utils_1.Utils.addCSSRule(this._styles, `${prefix} > .ui-resizable-w`, `left: ${left}`);
            utils_1.Utils.addCSSRule(this._styles, `${prefix} > .ui-resizable-sw`, `left: ${left}; bottom: ${bottom}`);
        }
        
        maxH = maxH || this._styles._max;
        if (maxH > this._styles._max) {
            let getHeight = (rows) => (cellHeight * rows) + cellHeightUnit;
            for (let i = this._styles._max + 1; i <= maxH; i++) { 
                let h = getHeight(i);
                utils_1.Utils.addCSSRule(this._styles, `${prefix}[gs-y="${i - 1}"]`, `top: ${getHeight(i - 1)}`); 
                utils_1.Utils.addCSSRule(this._styles, `${prefix}[gs-h="${i}"]`, `height: ${h}`);
                utils_1.Utils.addCSSRule(this._styles, `${prefix}[gs-min-h="${i}"]`, `min-height: ${h}`);
                utils_1.Utils.addCSSRule(this._styles, `${prefix}[gs-max-h="${i}"]`, `max-height: ${h}`);
            }
            this._styles._max = maxH;
        }
        return this;
    }
    
    _updateContainerHeight() {
        if (!this.engine || this.engine.batchMode)
            return this;
        let row = this.getRow() + this._extraDragRow; 
        
        
        
        
        
        
        
        
        
        
        this.el.setAttribute('gs-current-row', String(row));
        if (row === 0) {
            this.el.style.removeProperty('height');
            return this;
        }
        let cellHeight = this.opts.cellHeight;
        let unit = this.opts.cellHeightUnit;
        if (!cellHeight)
            return this;
        this.el.style.height = row * cellHeight + unit;
        return this;
    }
    
    _prepareElement(el, triggerAddEvent = false, node) {
        if (!node) {
            el.classList.add(this.opts.itemClass);
            node = this._readAttr(el);
        }
        el.gridstackNode = node;
        node.el = el;
        node.grid = this;
        let copy = Object.assign({}, node);
        node = this.engine.addNode(node, triggerAddEvent);
        
        if (!utils_1.Utils.same(node, copy)) {
            this._writeAttr(el, node);
        }
        this._prepareDragDropByNode(node);
        return this;
    }
    
    _writePosAttr(el, n) {
        if (n.x !== undefined && n.x !== null) {
            el.setAttribute('gs-x', String(n.x));
        }
        if (n.y !== undefined && n.y !== null) {
            el.setAttribute('gs-y', String(n.y));
        }
        if (n.w) {
            el.setAttribute('gs-w', String(n.w));
        }
        if (n.h) {
            el.setAttribute('gs-h', String(n.h));
        }
        return this;
    }
    
    _writeAttr(el, node) {
        if (!node)
            return this;
        this._writePosAttr(el, node);
        let attrs  = {
            autoPosition: 'gs-auto-position',
            minW: 'gs-min-w',
            minH: 'gs-min-h',
            maxW: 'gs-max-w',
            maxH: 'gs-max-h',
            noResize: 'gs-no-resize',
            noMove: 'gs-no-move',
            locked: 'gs-locked',
            id: 'gs-id',
            resizeHandles: 'gs-resize-handles'
        };
        for (const key in attrs) {
            if (node[key]) { 
                el.setAttribute(attrs[key], String(node[key]));
            }
            else {
                el.removeAttribute(attrs[key]);
            }
        }
        return this;
    }
    
    _readAttr(el) {
        let node = {};
        node.x = utils_1.Utils.toNumber(el.getAttribute('gs-x'));
        node.y = utils_1.Utils.toNumber(el.getAttribute('gs-y'));
        node.w = utils_1.Utils.toNumber(el.getAttribute('gs-w'));
        node.h = utils_1.Utils.toNumber(el.getAttribute('gs-h'));
        node.maxW = utils_1.Utils.toNumber(el.getAttribute('gs-max-w'));
        node.minW = utils_1.Utils.toNumber(el.getAttribute('gs-min-w'));
        node.maxH = utils_1.Utils.toNumber(el.getAttribute('gs-max-h'));
        node.minH = utils_1.Utils.toNumber(el.getAttribute('gs-min-h'));
        node.autoPosition = utils_1.Utils.toBool(el.getAttribute('gs-auto-position'));
        node.noResize = utils_1.Utils.toBool(el.getAttribute('gs-no-resize'));
        node.noMove = utils_1.Utils.toBool(el.getAttribute('gs-no-move'));
        node.locked = utils_1.Utils.toBool(el.getAttribute('gs-locked'));
        node.resizeHandles = el.getAttribute('gs-resize-handles');
        node.id = el.getAttribute('gs-id');
        
        for (const key in node) {
            if (!node.hasOwnProperty(key))
                return;
            if (!node[key] && node[key] !== 0) { 
                delete node[key];
            }
        }
        return node;
    }
    
    _setStaticClass() {
        let classes = ['grid-stack-static'];
        if (this.opts.staticGrid) {
            this.el.classList.add(...classes);
            this.el.setAttribute('gs-static', 'true');
        }
        else {
            this.el.classList.remove(...classes);
            this.el.removeAttribute('gs-static');
        }
        return this;
    }
    
    onParentResize() {
        if (!this.el || !this.el.clientWidth)
            return; 
        let changedColumn = false;
        
        if (this._autoColumn && this.opts._isNested) {
            if (this.opts.column !== this.opts._isNested.w) {
                changedColumn = true;
                this.column(this.opts._isNested.w, 'none');
            }
        }
        else {
            
            let oneColumn = !this.opts.disableOneColumnMode && this.el.clientWidth <= this.opts.oneColumnSize;
            if ((this.opts.column === 1) !== oneColumn) {
                changedColumn = true;
                if (this.opts.animate) {
                    this.setAnimation(false);
                } 
                this.column(oneColumn ? 1 : this._prevColumn);
                if (this.opts.animate) {
                    this.setAnimation(true);
                }
            }
        }
        
        if (this._isAutoCellHeight) {
            if (!changedColumn && this.opts.cellHeightThrottle) {
                if (!this._cellHeightThrottle) {
                    this._cellHeightThrottle = utils_1.Utils.throttle(() => this.cellHeight(), this.opts.cellHeightThrottle);
                }
                this._cellHeightThrottle();
            }
            else {
                
                this.cellHeight();
            }
        }
        
        this.engine.nodes.forEach(n => {
            if (n.subGrid) {
                n.subGrid.onParentResize();
            }
        });
        return this;
    }
    
    _updateWindowResizeEvent(forceRemove = false) {
        
        const workTodo = (this._isAutoCellHeight || !this.opts.disableOneColumnMode) && !this.opts._isNested;
        if (!forceRemove && workTodo && !this._windowResizeBind) {
            this._windowResizeBind = this.onParentResize.bind(this); 
            window.addEventListener('resize', this._windowResizeBind);
        }
        else if ((forceRemove || !workTodo) && this._windowResizeBind) {
            window.removeEventListener('resize', this._windowResizeBind);
            delete this._windowResizeBind; 
        }
        return this;
    }
    
    static getElement(els = '.grid-stack-item') { return utils_1.Utils.getElement(els); }
    
    static getElements(els = '.grid-stack-item') { return utils_1.Utils.getElements(els); }
    
    static getGridElement(els) { return GridStack.getElement(els); }
    
    static getGridElements(els) { return utils_1.Utils.getElements(els); }
    
    _initMargin() {
        let data;
        let margin = 0;
        
        let margins = [];
        if (typeof this.opts.margin === 'string') {
            margins = this.opts.margin.split(' ');
        }
        if (margins.length === 2) { 
            this.opts.marginTop = this.opts.marginBottom = margins[0];
            this.opts.marginLeft = this.opts.marginRight = margins[1];
        }
        else if (margins.length === 4) { 
            this.opts.marginTop = margins[0];
            this.opts.marginRight = margins[1];
            this.opts.marginBottom = margins[2];
            this.opts.marginLeft = margins[3];
        }
        else {
            data = utils_1.Utils.parseHeight(this.opts.margin);
            this.opts.marginUnit = data.unit;
            margin = this.opts.margin = data.h;
        }
        
        if (this.opts.marginTop === undefined) {
            this.opts.marginTop = margin;
        }
        else {
            data = utils_1.Utils.parseHeight(this.opts.marginTop);
            this.opts.marginTop = data.h;
            delete this.opts.margin;
        }
        if (this.opts.marginBottom === undefined) {
            this.opts.marginBottom = margin;
        }
        else {
            data = utils_1.Utils.parseHeight(this.opts.marginBottom);
            this.opts.marginBottom = data.h;
            delete this.opts.margin;
        }
        if (this.opts.marginRight === undefined) {
            this.opts.marginRight = margin;
        }
        else {
            data = utils_1.Utils.parseHeight(this.opts.marginRight);
            this.opts.marginRight = data.h;
            delete this.opts.margin;
        }
        if (this.opts.marginLeft === undefined) {
            this.opts.marginLeft = margin;
        }
        else {
            data = utils_1.Utils.parseHeight(this.opts.marginLeft);
            this.opts.marginLeft = data.h;
            delete this.opts.margin;
        }
        this.opts.marginUnit = data.unit; 
        if (this.opts.marginTop === this.opts.marginBottom && this.opts.marginLeft === this.opts.marginRight && this.opts.marginTop === this.opts.marginRight) {
            this.opts.margin = this.opts.marginTop; 
        }
        return this;
    }
    
    
    
    static setupDragIn(dragIn, dragInOptions) { }
    
    movable(els, val) { return this; }
    
    resizable(els, val) { return this; }
    
    disable() { return this; }
    
    enable() { return this; }
    
    enableMove(doEnable) { return this; }
    
    enableResize(doEnable) { return this; }
    
    _setupAcceptWidget() { return this; }
    
    _setupRemoveDrop() { return this; }
    
    _prepareDragDropByNode(node) { return this; }
    
    _onStartMoving(el, event, ui, node, cellWidth, cellHeight) { return; }
    
    _dragOrResize(el, event, ui, node, cellWidth, cellHeight) { return; }
    
    _leave(el, helper) { return; }
}
exports.GridStack = GridStack;

GridStack.Utils = utils_1.Utils;

GridStack.Engine = gridstack_engine_1.GridStackEngine;


 }),

 906:
 (function(__unused_webpack_module, exports, __webpack_require__) {


var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    Object.defineProperty(o, k2, { enumerable: true, get: function() { return m[k]; } });
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __exportStar = (this && this.__exportStar) || function(m, exports) {
    for (var p in m) if (p !== "default" && !exports.hasOwnProperty(p)) __createBinding(exports, m, p);
};
Object.defineProperty(exports, "__esModule", ({ value: true }));
exports.GridStackDDJQueryUI = exports.$ = void 0;
const gridstack_dd_1 = __webpack_require__(21);











const $ = __webpack_require__(273); 
exports.$ = $;
__webpack_require__(946);
__webpack_require__(858); 

__exportStar(__webpack_require__(21), exports);

class GridStackDDJQueryUI extends gridstack_dd_1.GridStackDD {
    resizable(el, opts, key, value) {
        let $el = $(el);
        if (opts === 'enable') {
            $el.resizable().resizable(opts);
        }
        else if (opts === 'disable' || opts === 'destroy') {
            if ($el.data('ui-resizable')) { 
                $el.resizable(opts);
            }
        }
        else if (opts === 'option') {
            $el.resizable(opts, key, value);
        }
        else {
            const grid = el.gridstackNode.grid;
            let handles = $el.data('gs-resize-handles') ? $el.data('gs-resize-handles') : grid.opts.resizable.handles;
            $el.resizable(Object.assign(Object.assign(Object.assign({}, grid.opts.resizable), { handles: handles }), {
                start: opts.start,
                stop: opts.stop,
                resize: opts.resize 
            }));
        }
        return this;
    }
    draggable(el, opts, key, value) {
        let $el = $(el);
        if (opts === 'enable') {
            $el.draggable().draggable('enable');
        }
        else if (opts === 'disable' || opts === 'destroy') {
            if ($el.data('ui-draggable')) { 
                $el.draggable(opts);
            }
        }
        else if (opts === 'option') {
            $el.draggable(opts, key, value);
        }
        else {
            const grid = el.gridstackNode.grid;
            $el.draggable(Object.assign(Object.assign({}, grid.opts.draggable), {
                containment: (grid.opts._isNested && !grid.opts.dragOut) ?
                    $(grid.el).parent() : (grid.opts.draggable.containment || null),
                start: opts.start,
                stop: opts.stop,
                drag: opts.drag 
            }));
        }
        return this;
    }
    dragIn(el, opts) {
        let $el = $(el); 
        $el.draggable(opts);
        return this;
    }
    droppable(el, opts, key, value) {
        let $el = $(el);
        if (typeof opts.accept === 'function' && !opts._accept) {
            
            opts._accept = opts.accept;
            opts.accept = ($el) => opts._accept($el.get(0));
        }
        if (opts === 'disable' || opts === 'destroy') {
            if ($el.data('ui-droppable')) { 
                $el.droppable(opts);
            }
        }
        else {
            $el.droppable(opts, key, value);
        }
        return this;
    }
    isDroppable(el) {
        let $el = $(el);
        return Boolean($el.data('ui-droppable'));
    }
    isDraggable(el) {
        let $el = $(el);
        return Boolean($el.data('ui-draggable'));
    }
    isResizable(el) {
        let $el = $(el);
        return Boolean($el.data('ui-resizable'));
    }
    on(el, name, callback) {
        let $el = $(el);
        $el.on(name, (event, ui) => { return callback(event, ui.draggable ? ui.draggable[0] : event.target, ui.helper ? ui.helper[0] : null); });
        return this;
    }
    off(el, name) {
        let $el = $(el);
        $el.off(name);
        return this;
    }
}
exports.GridStackDDJQueryUI = GridStackDDJQueryUI;

gridstack_dd_1.GridStackDD.registerPlugin(GridStackDDJQueryUI);


 }),

 699:
 ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({ value: true }));


 }),

 593:
 ((__unused_webpack_module, exports) => {



Object.defineProperty(exports, "__esModule", ({ value: true }));
exports.Utils = exports.obsoleteAttr = exports.obsoleteOptsDel = exports.obsoleteOpts = exports.obsolete = void 0;


function obsolete(self, f, oldName, newName, rev) {
    let wrapper = (...args) => {
        console.warn('gridstack.js: Function `' + oldName + '` is deprecated in ' + rev + ' and has been replaced ' +
            'with `' + newName + '`. It will be **completely** removed in v1.0');
        return f.apply(self, args);
    };
    wrapper.prototype = f.prototype;
    return wrapper;
}
exports.obsolete = obsolete;

function obsoleteOpts(opts, oldName, newName, rev) {
    if (opts[oldName] !== undefined) {
        opts[newName] = opts[oldName];
        console.warn('gridstack.js: Option `' + oldName + '` is deprecated in ' + rev + ' and has been replaced with `' +
            newName + '`. It will be **completely** removed in v1.0');
    }
}
exports.obsoleteOpts = obsoleteOpts;

function obsoleteOptsDel(opts, oldName, rev, info) {
    if (opts[oldName] !== undefined) {
        console.warn('gridstack.js: Option `' + oldName + '` is deprecated in ' + rev + info);
    }
}
exports.obsoleteOptsDel = obsoleteOptsDel;

function obsoleteAttr(el, oldName, newName, rev) {
    let oldAttr = el.getAttribute(oldName);
    if (oldAttr !== null) {
        el.setAttribute(newName, oldAttr);
        console.warn('gridstack.js: attribute `' + oldName + '`=' + oldAttr + ' is deprecated on this object in ' + rev + ' and has been replaced with `' +
            newName + '`. It will be **completely** removed in v1.0');
    }
}
exports.obsoleteAttr = obsoleteAttr;

class Utils {
    
    static getElements(els) {
        if (typeof els === 'string') {
            let list = document.querySelectorAll(els);
            if (!list.length && els[0] !== '.' && els[0] !== '#') {
                list = document.querySelectorAll('.' + els);
                if (!list.length) {
                    list = document.querySelectorAll('#' + els);
                }
            }
            return Array.from(list);
        }
        return [els];
    }
    
    static getElement(els) {
        if (typeof els === 'string') {
            if (!els.length)
                return null;
            if (els[0] === '#') {
                return document.getElementById(els.substring(1));
            }
            if (els[0] === '.' || els[0] === '[') {
                return document.querySelector(els);
            }
            
            if (!isNaN(+els[0])) { 
                return document.getElementById(els);
            }
            
            let el = document.querySelector(els);
            if (!el) {
                el = document.getElementById(els);
            }
            if (!el) {
                el = document.querySelector('.' + els);
            }
            return el;
        }
        return els;
    }
    
    static isIntercepted(a, b) {
        return !(a.y >= b.y + b.h || a.y + a.h <= b.y || a.x + a.w <= b.x || a.x >= b.x + b.w);
    }
    
    static isTouching(a, b) {
        return Utils.isIntercepted(a, { x: b.x - 0.5, y: b.y - 0.5, w: b.w + 1, h: b.h + 1 });
    }
    
    static sort(nodes, dir, column) {
        column = column || nodes.reduce((col, n) => Math.max(n.x + n.w, col), 0) || 12;
        if (dir === -1)
            return nodes.sort((a, b) => (b.x + b.y * column) - (a.x + a.y * column));
        else
            return nodes.sort((b, a) => (b.x + b.y * column) - (a.x + a.y * column));
    }
    
    static createStylesheet(id, parent) {
        let style = document.createElement('style');
        style.setAttribute('type', 'text/css');
        style.setAttribute('gs-style-id', id);
        
        if (style.styleSheet) { 
            
            style.styleSheet.cssText = '';
        }
        else {
            style.appendChild(document.createTextNode('')); 
        }
        if (!parent) {
            
            parent = document.getElementsByTagName('head')[0];
            parent.appendChild(style);
        }
        else {
            parent.insertBefore(style, parent.firstChild);
        }
        return style.sheet;
    }
    
    static removeStylesheet(id) {
        let el = document.querySelector('STYLE[gs-style-id=' + id + ']');
        if (el && el.parentNode)
            el.remove();
    }
    
    static addCSSRule(sheet, selector, rules) {
        if (typeof sheet.addRule === 'function') {
            sheet.addRule(selector, rules);
        }
        else if (typeof sheet.insertRule === 'function') {
            sheet.insertRule(`${selector}{${rules}}`);
        }
    }
    
    static toBool(v) {
        if (typeof v === 'boolean') {
            return v;
        }
        if (typeof v === 'string') {
            v = v.toLowerCase();
            return !(v === '' || v === 'no' || v === 'false' || v === '0');
        }
        return Boolean(v);
    }
    static toNumber(value) {
        return (value === null || value.length === 0) ? undefined : Number(value);
    }
    static parseHeight(val) {
        let h;
        let unit = 'px';
        if (typeof val === 'string') {
            let match = val.match(/^(-[0-9]+\.[0-9]+|[0-9]*\.[0-9]+|-[0-9]+|[0-9]+)(px|em|rem|vh|vw|%)?$/);
            if (!match) {
                throw new Error('Invalid height');
            }
            unit = match[2] || 'px';
            h = parseFloat(match[1]);
        }
        else {
            h = val;
        }
        return { h, unit };
    }
    
    
    static defaults(target, ...sources) {
        sources.forEach(source => {
            for (const key in source) {
                if (!source.hasOwnProperty(key))
                    return;
                if (target[key] === null || target[key] === undefined) {
                    target[key] = source[key];
                }
                else if (typeof source[key] === 'object' && typeof target[key] === 'object') {
                    
                    this.defaults(target[key], source[key]);
                }
            }
        });
        return target;
    }
    
    static same(a, b) {
        if (typeof a !== 'object')
            return a == b;
        if (typeof a !== typeof b)
            return false;
        
        if (Object.keys(a).length !== Object.keys(b).length)
            return false;
        for (const key in a) {
            if (a[key] !== b[key])
                return false;
        }
        return true;
    }
    
    static copyPos(a, b, doMinMax = false) {
        a.x = b.x;
        a.y = b.y;
        a.w = b.w;
        a.h = b.h;
        if (doMinMax) {
            if (b.minW)
                a.minW = b.minW;
            if (b.minH)
                a.minH = b.minH;
            if (b.maxW)
                a.maxW = b.maxW;
            if (b.maxH)
                a.maxH = b.maxH;
        }
        return a;
    }
    
    static samePos(a, b) {
        return a && b && a.x === b.x && a.y === b.y && a.w === b.w && a.h === b.h;
    }
    
    static removeInternalAndSame(a, b) {
        if (typeof a !== 'object' || typeof b !== 'object')
            return;
        for (let key in a) {
            let val = a[key];
            if (key[0] === '_' || val === b[key]) {
                delete a[key];
            }
            else if (val && typeof val === 'object' && b[key] !== undefined) {
                for (let i in val) {
                    if (val[i] === b[key][i] || i[0] === '_') {
                        delete val[i];
                    }
                }
                if (!Object.keys(val).length) {
                    delete a[key];
                }
            }
        }
    }
    
    static closestByClass(el, name) {
        while (el) {
            if (el.classList.contains(name))
                return el;
            el = el.parentElement;
        }
        return null;
    }
    
    static throttle(func, delay) {
        let isWaiting = false;
        return (...args) => {
            if (!isWaiting) {
                isWaiting = true;
                setTimeout(() => { func(...args); isWaiting = false; }, delay);
            }
        };
    }
    static removePositioningStyles(el) {
        let style = el.style;
        if (style.position) {
            style.removeProperty('position');
        }
        if (style.left) {
            style.removeProperty('left');
        }
        if (style.top) {
            style.removeProperty('top');
        }
        if (style.width) {
            style.removeProperty('width');
        }
        if (style.height) {
            style.removeProperty('height');
        }
    }
    
    static getScrollElement(el) {
        if (!el)
            return document.scrollingElement || document.documentElement; 
        const style = getComputedStyle(el);
        const overflowRegex = /(auto|scroll)/;
        if (overflowRegex.test(style.overflow + style.overflowY)) {
            return el;
        }
        else {
            return this.getScrollElement(el.parentElement);
        }
    }
    
    static updateScrollPosition(el, position, distance) {
        
        
        return;
        
        let rect = el.getBoundingClientRect();
        let innerHeightOrClientHeight = (window.innerHeight || document.documentElement.clientHeight);
        if (rect.top < 0 ||
            rect.bottom > innerHeightOrClientHeight) {
            
            
            
            let offsetDiffDown = rect.bottom - innerHeightOrClientHeight;
            let offsetDiffUp = rect.top;
            let scrollEl = this.getScrollElement(el);
            if (scrollEl !== null) {
                let prevScroll = scrollEl.scrollTop;
                if (rect.top < 0 && distance < 0) {
                    
                    if (el.offsetHeight > innerHeightOrClientHeight) {
                        scrollEl.scrollTop += distance;
                    }
                    else {
                        scrollEl.scrollTop += Math.abs(offsetDiffUp) > Math.abs(distance) ? distance : offsetDiffUp;
                    }
                }
                else if (distance > 0) {
                    
                    if (el.offsetHeight > innerHeightOrClientHeight) {
                        scrollEl.scrollTop += distance;
                    }
                    else {
                        scrollEl.scrollTop += offsetDiffDown > distance ? distance : offsetDiffDown;
                    }
                }
                
                position.top += scrollEl.scrollTop - prevScroll;
            }
        }
    }
    
    static updateScrollResize(event, el, distance) {
        
        
        return;
        const scrollEl = this.getScrollElement(el);
        const height = scrollEl.clientHeight;
        
        
        
        
        const offsetTop = (scrollEl === this.getScrollElement()) ? 0 : scrollEl.getBoundingClientRect().top;
        const pointerPosY = event.clientY - offsetTop;
        const top = pointerPosY < distance;
        const bottom = pointerPosY > height - distance;
        if (top) {
            
            
            scrollEl.scrollBy({ behavior: 'smooth', top: pointerPosY - distance });
        }
        else if (bottom) {
            scrollEl.scrollBy({ behavior: 'smooth', top: distance - (height - pointerPosY) });
        }
    }
    
    static clone(obj) {
        if (obj === null || obj === undefined || typeof (obj) !== 'object') {
            return obj;
        }
        
        if (obj instanceof Array) {
            
            return [...obj];
        }
        return Object.assign({}, obj);
    }
    
    static cloneDeep(obj) {
        
        const ret = Utils.clone(obj);
        for (const key in ret) {
            
            if (ret.hasOwnProperty(key) && typeof (ret[key]) === 'object' && key.substring(0, 2) !== '__' && !skipFields.find(k => k === key)) {
                ret[key] = Utils.cloneDeep(obj[key]);
            }
        }
        return ret;
    }
}
exports.Utils = Utils;

const skipFields = ['_isNested', 'el', 'grid', 'subGrid', 'engine'];


 }),

 273:
 ((module) => {

module.exports = __WEBPACK_EXTERNAL_MODULE__273__;

 }),

 946:
 ((module) => {

module.exports = __WEBPACK_EXTERNAL_MODULE__946__;

 }),

 858:
 ((module) => {

module.exports = __WEBPACK_EXTERNAL_MODULE__858__;

 })

 	});

 	
 	var __webpack_module_cache__ = {};
 	
 	
 	function __webpack_require__(moduleId) {
 		
 		var cachedModule = __webpack_module_cache__[moduleId];
 		if (cachedModule !== undefined) {
 			return cachedModule.exports;
 		}
 		
 		var module = __webpack_module_cache__[moduleId] = {
 			
 			
 			exports: {}
 		};
 	
 		
 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
 	
 		
 		return module.exports;
 	}
 	

 	
 	
 	
 	
 	var __webpack_exports__ = __webpack_require__(572);
 	__webpack_exports__ = __webpack_exports__.GridStack;
 	
 	return __webpack_exports__;
 })()
;
});

Espo.loader.setContextId(null);
