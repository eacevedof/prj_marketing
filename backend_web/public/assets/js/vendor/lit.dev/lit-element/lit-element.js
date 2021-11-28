import{ReactiveElement as d}from"/-/@lit/reactive-element@v1.0.0-rc.2-810hxb6J93lP2Fvpv3EZ/dist=es2020,mode=imports,min/optimized/@lit/reactive-element.js";
export*from"/-/@lit/reactive-element@v1.0.0-rc.2-810hxb6J93lP2Fvpv3EZ/dist=es2020,mode=imports,min/optimized/@lit/reactive-element.js";
import{render as p,noChange as h}from"/-/lit-html@v2.0.0-rc.3-U0mgczQMV615aaRpIg4r/dist=es2020,mode=imports,min/optimized/lit-html.js";
export*from"/-/lit-html@v2.0.0-rc.3-U0mgczQMV615aaRpIg4r/dist=es2020,mode=imports,min/optimized/lit-html.js";
/**
 * @license
 * Copyright 2017 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */
var l,m,n,a,o,c;const v=d;((l=(c=globalThis).litElementVersions)!==null&&l!==void 0?l:c.litElementVersions=[]).push("3.0.0-rc.2");class i extends d{constructor(){super(...arguments),this.renderOptions={host:this},this.\u03A6t=void 0}createRenderRoot(){var e,t;const s=super.createRenderRoot();return(e=(t=this.renderOptions).renderBefore)!==null&&e!==void 0||(t.renderBefore=s.firstChild),s}update(e){const t=this.render();super.update(e),this.\u03A6t=p(t,this.renderRoot,this.renderOptions)}connectedCallback(){var e;super.connectedCallback(),(e=this.\u03A6t)===null||e===void 0||e.setConnected(!0)}disconnectedCallback(){var e;super.disconnectedCallback(),(e=this.\u03A6t)===null||e===void 0||e.setConnected(!1)}render(){return h}}i.finalized=!0,i._$litElement$=!0,(n=(m=globalThis).litElementHydrateSupport)===null||n===void 0||n.call(m,{LitElement:i}),(o=(a=globalThis).litElementPlatformSupport)===null||o===void 0||o.call(a,{LitElement:i});const u={K:(r,e,t)=>{r.K(e,t)},L:r=>r.L};export{i as LitElement,v as UpdatingElement,u as _\u03A6};export default null;