"use strict";
Object.defineProperty(exports, "__esModule", { value: true });
exports.isFragmentReady = exports.makeFragmentData = exports.useFragment = void 0;
function useFragment(_documentNode, fragmentType) {
    return fragmentType;
}
exports.useFragment = useFragment;
function makeFragmentData(data, _fragment) {
    return data;
}
exports.makeFragmentData = makeFragmentData;
function isFragmentReady(queryNode, fragmentNode, data) {
    var _a, _b;
    var deferredFields = (_a = queryNode.__meta__) === null || _a === void 0 ? void 0 : _a.deferredFields;
    if (!deferredFields)
        return true;
    var fragDef = fragmentNode.definitions[0];
    var fragName = (_b = fragDef === null || fragDef === void 0 ? void 0 : fragDef.name) === null || _b === void 0 ? void 0 : _b.value;
    var fields = (fragName && deferredFields[fragName]) || [];
    return fields.length > 0 && fields.every(function (field) { return data && field in data; });
}
exports.isFragmentReady = isFragmentReady;
