(()=>{var e={864:()=>{allowedEmbedTypes=["embed","youtube","vimeo","instagram","url"],allowedMediaTypes=[...allowedEmbedTypes,"image","html"],ko.components.register("light-box",{viewModel:class{images=null;currentIndex=ko.observable(0);constructor(e){this.images=e.images,this.hash=this.randomHash(),this.settings=Object.assign(Object.assign(Object.assign({},bootstrap.Modal.Default),bootstrap.Carousel.Default),{interval:!1,target:'[data-toggle="lightbox"]',gallery:"",size:"xl",constrain:!0}),this.settings=Object.assign(Object.assign({},this.settings),e.options),this.modalOptions=(()=>this.setOptionsFromSettings(bootstrap.Modal.Default))(),this.carouselOptions=(()=>this.setOptionsFromSettings(bootstrap.Carousel.Default))(),this.hasImages=ko.computed((()=>this.images().length>0)),this.additionalImageCount=ko.computed((()=>{var e=this.images().length-4;return e>0?e:0})),this.currentImage=ko.computed((()=>this.hasImages()?this.images()[this.currentIndex()]:{})),this.carouselClasses=ko.computed((()=>"lightbox-carousel carousel slide "+("fullscreen"===this.settings.size?"position-absolute w-100 translate-middle top-50 start-50":""))),this.processedImages=ko.pureComputed((()=>this.images().map(((e,t)=>{let n=e.src.replace(/\/$/,"");const a=new RegExp(`^(${allowedMediaTypes.join("|")})`,"i"),o=/^html/.test(n),i=/^image/.test(n),s=n.match(/.*youtu(.be\/|v\/|embed\/|watch\?v=)([^#&?]*).*/),r=n.match(/.*instagram.com.*/),l=s&&11===s[2].length?`https://www.youtube.com/embed/${s[2]}`:null,d=r?n+"/embed":null;let c;c=l||d||n;const p=!l&&!d;a.test(n)&&(n=n.replace(a,""));const u=this.settings.constrain?"mw-100 mh-100 h-auto w-auto m-auto top-0 end-0 bottom-0 start-0":"h-100 w-100",m=new URLSearchParams(n.split("?")[1]);let g=m.get("caption")?`<p class="lightbox-caption m-0 p-2 text-center text-white small"><em>${m.get("caption")}</em></p>`:"",b=n;if(g)try{b=new URL(n),b.searchParams.delete("caption"),b=b.toString()}catch(e){b=n}return{imgClasses:u,url:b,caption:g,isHtml:o,isForcedImage:i,isImg:p,inner:c}}))))}next=function(){if(this.hasImages()){var e=this.currentIndex()<this.images().length-1?this.currentIndex()+1:0;this.currentIndex(e)}};prev=function(){if(this.hasImages()){var e=this.currentIndex()>0?this.currentIndex()-1:this.images().length-1;this.currentIndex(e)}};attachArrowKeyHandlers(){$(document).on("keydown",(e=>{37===e.keyCode&&this.prev(),39===e.keyCode&&this.next()})).detach()}detachArrowKeyHandlers(){$(document).off("keydown")}openModal(e){this.hasImages()&&(this.currentIndex(e),$("#lightboxModal").modal("show"),this.attachArrowKeyHandlers(),$("#lightboxModal").on("hidden.bs.modal",(function(){this.detachArrowKeyHandlers()})))}closeModal(){$("#lightboxModal").modal("hide")}setOptionsFromSettings(e){return Object.keys(e).reduce(((e,t)=>Object.assign(e,{[t]:this.settings[t]})),{})}randomHash(e=8){return Array.from({length:e},(()=>Math.floor(36*Math.random()).toString(36))).join("")}},template:'\n        \x3c!-- ko if: hasImages --\x3e\n        <div class="modal lightbox fade" id="lightboxModal" tabindex="-1" aria-hidden="true">\n            <div class="modal-dialog modal-dialog-centered modal-xl">\n                <div class="modal-content border-0 bg-transparent">\n                    <div class="modal-body p-0">\n                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 p-3"\n                                data-bs-dismiss="modal" aria-label="Close" style="font-size: 2rem;z-index: 2; background: none;">\n                            <span aria-hidden="true">&times;</span>\n                        </button>\n                        \x3c!-- Carousel code here --\x3e\n                        <div class="lightbox-carousel carousel slide"\n                             data-bind="css: carouselClasses, carousel: carouselOptions">\n                            <div class="carousel-inner" data-bind="foreach: {data: processedImages, as: \'image\'}">\n                                <div class="carousel-item"\n                                     data-bind="css: {\'active\': $index() === $parent.currentIndex()}, style: { minHeight: \'100px\' }">\n                                    <div class="position-absolute top-50 start-50 translate-middle text-white">\n                                        <div class="spinner-border" role="status"></div>\n                                    </div>\n                                    <div class="ratio ratio-16x9" style="background-color: #000;">\n                                        \x3c!-- ko if: image.isImg --\x3e\n                                        <img data-bind="attr: { src: image.url, class: \'d-block \' + image.imgClasses + \' img-fluid\', style: { \'z-index\': 1, \'object-fit\': \'contain\' } }"/>\n                                        \x3c!-- /ko --\x3e\n\n                                        \x3c!-- ko ifnot: image.isImg --\x3e\n                                        <iframe data-bind="attr: { src: image.inner, title: image.isYoutube ? \'YouTube video player\' : \'Instagram Embed\', frameborder: \'0\', allow: image.isYoutube ? \'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\' : \'\', allowfullscreen: true }"></iframe>\n                                        \x3c!-- /ko --\x3e\n                                    </div>\n                                    \x3c!-- ko if: image.caption --\x3e\n                                    <p class="lightbox-caption m-0 p-2 text-center text-white small"\n                                       data-bind="html: image.caption"></p>\n                                    \x3c!-- /ko --\x3e\n                                </div>\n                            </div>\n                            \x3c!-- Carousel Controls --\x3e\n                            \x3c!-- ko if: images().length > 0 --\x3e\n                            <button class="carousel-control carousel-control-prev h-75 m-auto" type="button"\n                                    data-bs-target="#lightboxCarousel" data-bs-slide="prev" data-bind="click: prev">\n                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>\n                                <span class="visually-hidden">Previous</span>\n                            </button>\n                            <button class="carousel-control carousel-control-next h-75 m-auto" type="button"\n                                    data-bs-target="#lightboxCarousel" data-bs-slide="next" data-bind="click: next">\n                                <span class="carousel-control-next-icon" aria-hidden="true"></span>\n                                <span class="visually-hidden">Next</span>\n                            </button>\n                            \x3c!-- /ko --\x3e\n                        </div>\n                        \x3c!-- You might replace it with the carousel component in bootstrap --\x3e\n                    </div>\n                </div>\n            </div>\n        </div>\n        \x3c!-- /ko --\x3e\n        \x3c!-- Only display if there are images --\x3e\n        \x3c!-- ko if: hasImages --\x3e\n        <div class="row">\n            \x3c!-- Iterate over the first four images or all if less than four --\x3e\n            \x3c!-- ko foreach: images.slice(0, 4) --\x3e\n            <div class="col-md-3">\n                <div class="img-container-building">\n                    <img data-bind="attr: { src: src, alt: alt }, click: function() { $parent.openModal($index()) }"\n                         class="img-thumbnail-building cursor-pointer"/>\n\n                    \x3c!-- If it\'s the fourth image and there are additional images, show overlay --\x3e\n                    \x3c!-- ko if: $index() === 3 && $parent.additionalImageCount() > 0 --\x3e\n                    <div class="overlay" data-bind="click: function() { $parent.openModal($index()) }">\n                        <span class="additional-count">+\x3c!-- ko text: $parent.additionalImageCount --\x3e\n                            \x3c!-- /ko --\x3e</span>\n                    </div>\n                    \x3c!-- /ko --\x3e\n                </div>\n            </div>\n            \x3c!-- /ko --\x3e\n        </div>\n        \x3c!-- /ko --\x3e\n\n        \x3c!-- Display message if there are no images --\x3e\n        \x3c!-- ko ifnot: hasImages --\x3e\n        <div class="col-12">\n            <p class="text-center">No images available.</p>\n        </div>\n        \x3c!-- /ko --\x3e\n    '})},880:()=>{function e(e){if(/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}$/.test(e))return e;if(/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(e)){const[t,n]=e.split(" "),[a,o,i]=t.split("-"),[s,r]=n.split(":");return`${i}/${o}/${a} ${s}:${r}`}throw new Error("Invalid date format")}ko.components.register("time-slot-pill",{viewModel:function(t){const n=this;n.schedule=t.schedule,n.selectedResources=t.selectedResources,console.log(t),n.date=t.date,n.options={hour:"2-digit",minute:"2-digit"},console.log(n.date),n.startTime=ko.computed((()=>luxon.DateTime.fromFormat(e(n.date.from_),"dd/MM/yyyy HH:mm").toJSDate())),n.endTime=ko.computed((()=>luxon.DateTime.fromFormat(e(n.date.to_),"dd/MM/yyyy HH:mm").toJSDate())),n.toDateStr=e=>`${e.getDate()}. ${monthNamesShort[e.getMonth()].toLowerCase()}`,n.toTimeStr=(e,t)=>t?`${e.toLocaleTimeString("no",n.options).replace(":",".")} -\n                ${t.toLocaleTimeString("no",n.options).replace(":",".")}`:e.toLocaleTimeString("no",n.options).replace(":","."),n.removeDate=t.removeDate,n.error=ko.computed((()=>{if(!n.schedule||!n.selectedResources)return!1;const e={date:luxon.DateTime.fromJSDate(n.startTime()).toFormat("yyyy-MM-dd"),from:luxon.DateTime.fromJSDate(n.startTime()).toFormat("HH:mm:ss"),to:luxon.DateTime.fromJSDate(n.endTime()).toFormat("HH:mm:ss"),resources:n.selectedResources()},t=n.schedule().find((t=>function(e,t){if(e.date!==t.date)return!1;if("allocation"===t.type||"booking"===t.type)return!1;const n=e.from<t.to&&e.to>t.from,a=e.resources.some((e=>t.resources.some((t=>e.id===t.id))));return n&&a}(e,t)));return t?`${e.resources.find((e=>t.resources.some((t=>t.id===e.id)))).name} ikke tilgjengelig`:void 0}))},template:'\n\n        <div class="pill pill--secondary" data-bind="css: {\'pill--error\': error}">\n            \x3c!-- ko if: startTime().getMonth() === endTime().getMonth() && startTime().getFullYear() === endTime().getFullYear() && startTime().getDate() === endTime().getDate() --\x3e\n            <div class="pill-label">\n                <span className="text-primary text-bold" data-bind="text: toDateStr(startTime())"></span>\n            </div>\n            <div class="pill-divider"></div>\n            <div class="pill-time"\n                 data-bind="text: toTimeStr(startTime(), endTime()), css: {\'last-child\': !removeDate}">\n\n            </div>\n            \x3c!-- /ko --\x3e\n            \x3c!-- ko ifnot: (startTime().getMonth() === endTime().getMonth() && startTime().getFullYear() === endTime().getFullYear() && startTime().getDate() === endTime().getDate()) --\x3e\n            <div class="pill-content gap-1">\n                <div class="date"><span class="text-primary text-bold" data-bind="text: toDateStr(startTime())"></span>\n                    <span data-bind="text: toTimeStr(startTime())"></span>\n                </div>\n                <span> - </span>\n                <div class="time"><span class="text-primary text-bold" data-bind="text: toDateStr(endTime())"></span>\n                    <span data-bind="text: toTimeStr(endTime())"></span>\n                </div>\n            </div>\n\n            \x3c!-- /ko --\x3e\n\n            \x3c!-- ko if: !!removeDate --\x3e\n            <button class="pill-icon" data-bind="click: removeDate"><i class="pill-cross"></i></button>\n            \x3c!-- /ko --\x3e\n\n        </div>\n        \x3c!-- ko if: error --\x3e\n        <div class="d-flex align-items-center gap-2">\n            <svg class="font-size-small" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">\n                <path d="M6.00011 0.75C6.33292 0.75 6.63996 0.925781 6.80871 1.21406L11.8712 9.83906C12.0423 10.1297 12.0423 10.4883 11.8759 10.7789C11.7095 11.0695 11.3978 11.25 11.0626 11.25H0.937611C0.602455 11.25 0.290736 11.0695 0.12433 10.7789C-0.042076 10.4883 -0.0397323 10.1273 0.129018 9.83906L5.19152 1.21406C5.36027 0.925781 5.6673 0.75 6.00011 0.75ZM6.00011 3.75C5.68839 3.75 5.43761 4.00078 5.43761 4.3125V6.9375C5.43761 7.24922 5.68839 7.5 6.00011 7.5C6.31183 7.5 6.56261 7.24922 6.56261 6.9375V4.3125C6.56261 4.00078 6.31183 3.75 6.00011 3.75ZM6.75011 9C6.75011 8.80109 6.67109 8.61032 6.53044 8.46967C6.38979 8.32902 6.19902 8.25 6.00011 8.25C5.8012 8.25 5.61043 8.32902 5.46978 8.46967C5.32913 8.61032 5.25011 8.80109 5.25011 9C5.25011 9.19891 5.32913 9.38968 5.46978 9.53033C5.61043 9.67098 5.8012 9.75 6.00011 9.75C6.19902 9.75 6.38979 9.67098 6.53044 9.53033C6.67109 9.38968 6.75011 9.19891 6.75011 9Z"\n                      fill="#B00020"/>\n            </svg>\n            <span class="text-small text-red-error" data-bind="text: error"></span>\n        </div>\n        \x3c!-- /ko --\x3e\n    '})},940:()=>{if(!globalThis.trans){globalThis.translations=ko.observable({}),globalThis.translationsLoaded=ko.observable(!1);const e=phpGWLink("bookingfrontend/lang.php",null,!0);fetch(e).then((e=>e.json())).then((e=>{globalThis.translations(e),globalThis.translationsLoaded(!0)})),globalThis.trans=(e,t,...n)=>{const a=globalThis.translations(),o=e=>a[e]&&a[e][t]?a[e][t].replace(/%\d+/g,(e=>{const t=parseInt(e.substring(1))-1;return void 0!==n[t]?n[t]:e})):null;if(Array.isArray(e))for(let t=0;t<e.length;t++){const n=o(e[t]);if(null!==n)return n}else{const t=o(e);if(null!==t)return t}if(!Array.isArray(e)||!e.includes("common")){const e=o("common");if(null!==e)return e}return`Missing translation for [${Array.isArray(e)?e.join(", "):e}][${t}]`};class t{constructor(e,t){let n=t.templateNodes.map((e=>e.textContent));1===n.length&&n[0].includes(":")&&(n=n[0].split(":")),n=n.map((e=>e.trim())),n.length>=2?(this.tag=ko.observable(n[1]),this.group=ko.observable(n[0])):(this.tag="function"==typeof e.tag?e.tag:ko.observable(e.tag),this.group="function"==typeof e.group?e.group:ko.observable(e.group)),this.suffix=ko.observable(e.suffix||""),this.args=ko.observable(e.args),this.translations=globalThis.translations,this.translated=ko.computed((()=>{if(self.translations&&self.translations()&&Object.keys(self.translations()).length>0&&this.group&&this.tag)return globalThis.trans(this.group(),this.tag(),this.args())+this.suffix()}))}}ko.components.register("trans",{viewModel:{createViewModel:function(e,n){return new t(e,n)}},template:"\x3c!--ko text: translated()--\x3e\x3c!--/ko--\x3e"})}},16:()=>{ko.bindingHandlers.withAfterRender={init:function(e,t,n,a,o){var i=ko.unwrap(t());ko.applyBindingsToNode(e,{visible:!0},o),i&&i.afterRender&&i.afterRender.call(a,e)}}}},t={};function n(a){var o=t[a];if(void 0!==o)return o.exports;var i=t[a]={exports:{}};return e[a](i,i.exports,n),i.exports}(()=>{"use strict";function e(e,t,n,a){if(a){const e=a.split("/").filter((e=>""!==e&&!e.includes("http")));a="//"+e.slice(0,e.length-1).join("/")+"/"}const o=(a||strBaseURL).split("?");let i=o[0]+e+"?";null==t&&(t=new Object);for(const e in t)i+=e+"="+t[e]+"&";return o[1]&&(i+=o[1]),n&&(i+="&phpgw_return_as=json"),i}n(864),n(880),n(16),ko.components.register("collapsable-text",{viewModel:function(e){const t=this;t.content=e.content,t.descriptionExpanded=ko.observable(!1),t.contentSize=ko.observable(0),t.containerSize=ko.observable(0),t.contentElement=ko.observable(null),t.toggleDescription=()=>{t.descriptionExpanded(!t.descriptionExpanded())},t.afterRenderContent=e=>{console.log("after element set",e),t.contentElement(e)},t.isActive=ko.computed((()=>{const e=t.contentElement();return!e||(console.log("setting active",e.parentElement.scrollHeight,e.parentElement.clientHeight),e.parentElement.scrollHeight>e.parentElement.clientHeight||e.parentElement.scrollWidth>e.parentElement.clientWidth)}))},template:'\n        <div class="col-sm-12 d-flex flex-column collapsible-content collapsed-description"\n             data-bind="css: {\'collapsed-description-fade\': !descriptionExpanded() && isActive, \'collapsed-description\': !descriptionExpanded()}">\n            \x3c!-- ko if: content --\x3e\n            <p data-bind="html: content,withAfterRender: { afterRender: afterRenderContent}"></p>\n            \x3c!-- /ko --\x3e\n            \x3c!-- ko ifnot: content --\x3e\n            <p data-bind="template: { nodes: $componentTemplateNodes }, withAfterRender: { afterRender: afterRenderContent}"></p>\n            \x3c!-- /ko --\x3e\n        </div>\n        <div class="col-sm-12 " data-bind="visible: isActive">\n            <button class="pe-btn  pe-btn--transparent text-secondary d-flex gap-3"\n                    data-bind="click: () => toggleDescription()">\n                \x3c!-- ko if: descriptionExpanded() --\x3e\n                <span><trans params="group: \'bookingfrontend\',tag: \'show_less\'"></span>\n                \x3c!-- /ko --\x3e\n                \x3c!-- ko ifnot: descriptionExpanded() --\x3e\n                <span><trans params="group: \'bookingfrontend\',tag: \'show_more\'"></span>\n                \x3c!-- /ko --\x3e\n                <i class="fa"\n                   data-bind="css: {\'fa-chevron-up\': descriptionExpanded(), \'fa-chevron-down\': !descriptionExpanded()}"></i>\n            </button>\n        </div>\n    '}),n(940),$(document).ready((function(){$("input[type=radio][name=select_template]").change((function(){var t=$(this).val(),n=e("bookingfrontend/",{menuaction:"bookingfrontend.preferences.set"},!0);$.ajax({type:"POST",dataType:"json",data:{template_set:t},url:n,success:function(e){location.reload(!0)}})})),$("input[type=radio][name=select_language]").change((function(){var t=$(this).val(),n=e("bookingfrontend/",{menuaction:"bookingfrontend.preferences.set"},!0);$.ajax({type:"POST",dataType:"json",data:{lang:t},url:n,success:function(e){location.reload(!0)}})}))}));class t{constructor(e){this.applicationCartItems=e.applicationCartItems,this.cartElementExpanded=ko.observable(!0),this.popperInstance=ko.observable(),this.isVisible=ko.observable(!1),this.deleteItem=e.deleteItem,this.applicationCartItems.subscribe((e=>console.log(e)))}initializePopper(e){const t=e.previousElementSibling;this.popperInstance(new Popper(t,e,{placement:"bottom-end",strategy:"fixed"}))}destroyPopperInstance(){this.popperInstance()&&(this.popperInstance().destroy(),this.popperInstance(null))}togglePopper(e=void 0){let t=this.popperInstance();t&&(this.isVisible(e||!this.isVisible()),t.update())}toggleCartExpansion(){this.cartElementExpanded(!this.cartElementExpanded())}popperAttr=ko.computed((()=>this.isVisible&&this.isVisible()?{"data-show":""}:{}))}ko.components.register("shopping-basket",{viewModel:{createViewModel:(e,n)=>new t(e,n)},template:'\n        <button class="pe-btn pe-btn--transparent menu__toggler" type="button" aria-expanded="false"\n                data-bind="click: () => togglePopper()">\n    <span class="text">\n        <i class="fa-solid fa-basket-shopping"></i>\n        <span class="badge badge-light" style="position: absolute;top: -5px;"\n              data-bind="visible: applicationCartItems().length > 0, text: applicationCartItems().length">-1\n        </span>\n    </span>\n        </button>\n        <div class="poppercontent"\n             style="z-index: 11"\n             data-bind="withAfterRender: {afterRender: (d) => initializePopper(d)}, visible: applicationCartItems().length > 0 && isVisible, attr: popperAttr()">\n            <div class="dialog-container">\n                <div class="dialog-header">\n                    <span class="dialog-title">Søknader klar for innsending</span>\n                    <button type="button" class="btn-close" data-bind="click: () => togglePopper(false)"\n                            aria-label="Close"></button>\n                </div>\n                <div class="dialog-ingress">\n                    <p>Her er en oversikt over dine søknader som er klar for innsending og godkjennelse.</p>\n                </div>\n                <div class="dialog-content">\n                    \x3c!-- Content goes here --\x3e\n                    <div class="article-table-wrapper" data-bind="visible: cartElementExpanded">\n                        <div data-bind="foreach: applicationCartItems">\n\n                            <div class="article-table-header title-only">\n                                <div class="resource-name d-flex gap-2 align-items-center">\n                                    <i class="fa-solid fa-location-dot"></i><h3 class="p-0 m-0" data-bind="text: building_name"></h3>\n                                </div>\n                                <div class="resource-expand float-right">\n                                    <span class="far fa-trash-alt mr-2" data-bind="click: () => $parent.deleteItem($data)"></span>\n                                </div>\n                            </div>\n                            <div class="category-table">\n\n                                    <div class="category-header category-article-row d-flex flex-wrap">\n                                        \x3c!-- ko foreach: { data: resources, as: \'item\' } --\x3e\n\n                                        <div class="pill pill">\n                                            <i class="fa-solid fa-layer-group"></i>\n                                            <div class="pill-label" data-bind="text: item.name"></div>\n                                        </div>\n                                        \x3c!-- /ko --\x3e\n\n                                    </div>\n\n                                <div class="category-articles">\n                                    <div class="category-article-row d-flex  flex-wrap">\n                                        \x3c!-- ko foreach: { data: dates, as: \'s\' } --\x3e\n                                        <time-slot-pill params="date: s"></time-slot-pill>\n                                        \x3c!-- /ko --\x3e\n\n                                    </div>\n                                </div>\n\n                            </div>\n                        </div>\n                    </div>\n                </div>\n                <div class="dialog-actions">\n                    <a class="pe-btn pe-btn-primary pe-btn-colour-primary link-text link-text-white d-flex gap-3"\n                       data-bind="attr: { href: phpGWLink(\'bookingfrontend/\', {menuaction:\'bookingfrontend.uiapplication.add_contact\' }, false) } ">\n                        <div class="text-bold">\n                            <trans params="group: \'bookingfrontend\', tag:\'complete_applications\'"></trans>\n                        </div>\n                        <div class="text-bold d-flex align-items-center">\n                            <i class="fa-solid fa-arrow-right-long"></i>\n                        </div>\n                    </a>\n                </div>\n            </div>\n        </div>\n    '});let a=new class{constructor(){this.applicationCartItems=ko.observableArray([]),this.applicationCartItemsEmpty=ko.observable(!1),this.visible=ko.observable(!0),this.initializeCart()}initializeCart(){this.getApplicationsCartItems()}deleteItem(t){var n,a,o;console.log("got delete request",this,t),(n=trans("bookingfrontend","delete_rentobject"),a=trans("bookingfrontend","delete_rentobject_body"),o=[trans("bookingfrontend","cancel"),trans("bookingfrontend","delete")],new Promise(((e,t)=>{const i=document.createElement("dialog");i.className="options-dialog";const s=document.createElement("div");s.className="options-dialog-title",s.textContent=n,i.appendChild(s);const r=document.createElement("div");r.className="options-dialog-body",r.textContent=a,i.appendChild(r);const l=document.createElement("div");l.className="options-dialog-options",o.forEach(((e,t)=>{const n=document.createElement("button");n.textContent=e,n.className="pe-btn  pe-btn--transparent pe-btn-text-primary",n.onclick=()=>{i.close(t)},l.appendChild(n)})),i.appendChild(l),i.addEventListener("close",(()=>{i.returnValue?e(i.returnValue):t(new Error("Dialog closed without selection")),i.remove()})),document.body.appendChild(i),i.showModal()}))).then((n=>{if(1==+n){const n=e("bookingfrontend/",{menuaction:"bookingfrontend.uiapplication.delete_partial"},!0);fetch(n,{method:"POST",headers:{"Content-Type":"application/json"},body:JSON.stringify({id:t.id})}).then((e=>e.json())).then((()=>this.getApplicationsCartItems())).catch((e=>console.error("Error:",e)))}})).catch((e=>{}))}getApplicationsCartItems(){const t=e("bookingfrontend/",{menuaction:"bookingfrontend.uiapplication.get_partials"},!0);fetch(t).then((e=>e.json())).then((e=>this.updateCartItems(e))).catch((e=>console.error("Error fetching cart items:",e)))}updateCartItems(e){const{list:t,total_sum:n}=e;this.applicationCartItemsEmpty(t.length<1),this.applicationCartItems(t.map((e=>({...e,dates:e.dates.map((e=>{const t=new Date(e.from_.replace(" ","T"));t.setHours(e.from_.substring(11,13)),t.setMinutes(e.from_.substring(14,16));const n=new Date(e.to_.replace(" ","T"));return n.setHours(e.to_.substring(11,13)),n.setMinutes(e.to_.substring(14,16)),{date:formatSingleDateWithoutHours(t),from_:e.from_,to_:e.to_,periode:formatPeriodeHours(t,n)}})),resources:ko.observableArray(e.resources.map((e=>({...e})))),joinedResources:e.resources.map((e=>e.name)).join(", ")}))))}};var o;o=()=>{const e=document.getElementById("application-cart-container")||document.getElementById("applications-cart-content");e&&(console.log("INITIALISING CART"),ko.applyBindings(a,e))},"complete"===document.readyState||"interactive"===document.readyState?setTimeout(o,1):document.addEventListener("DOMContentLoaded",o);var i=3,s=0,r=15,l=10,d={};$(".navbar-search").removeClass("d-none"),document.location.origin,window.location.pathname.split("/")[1],$(".termAcceptDocsUrl").attr("data-bind","text: docName, attr: {'href': itemLink }"),$(".maleInput").attr("data-bind","textInput: inputCountMale, attr: {'name': malename }, value: 1"),$(".femaleInput").attr("data-bind","textInput: inputCountFemale, attr: {'name': femalename }, value: 1");var c,p=CreateUrlParams(window.location.search);function u(){var e=this;e.showErrorMessages=ko.observable(!1),e.applicationCartItems=ko.observableArray(),e.selectedResources=ko.observableArray([$("#resource_id").val()]),e.activityId=ko.observable(),e.date=ko.observableArray(),e.aboutArrangement=ko.observable(""),e.agegroupList=ko.observableArray(),e.disabledDatesList=ko.observableArray(),e.specialRequirements=ko.observable(""),e.attachment=ko.observable(),e.termAcceptDocs=ko.observableArray(),e.imageArray=ko.observableArray(),e.selectedResource=ko.observable(),e.selectedResource.subscribe((t=>{(()=>{let t=phpGWLink("bookingfrontend/",{menuaction:"bookingfrontend.uidocument_resource.index_images",filter_owner_id:e.selectedResource().id,length:-1},!0);$.getJSON(t,(function(t){t.ResultSet.Result.length>0&&e.imageArray(t.ResultSet.Result.map(((t,n)=>({src:phpGWLink("bookingfrontend/",{menuaction:"bookingfrontend.uidocument_resource.download",id:t.id,filter_owner_id:e.selectedResource().id},!1),alt:t.description}))))}))})()})),e.resourceDescription=ko.computed((()=>{const t=e.selectedResource();if(!t)return;const n=getCookie("selected_lang")||"no",a=t.description_json,o=(new DOMParser).parseFromString(a[n]||a.no,"text/html").documentElement.textContent;if(!o)return;const i=(new DOMParser).parseFromString(o,"text/html"),s=e=>{[...e.childNodes].forEach((e=>{var t;1===e.nodeType&&(!(t=e).innerHTML.trim()||"<br>"===t.innerHTML.trim()||/^(&nbsp;|\s)*$/.test(t.innerHTML)?e.remove():s(e))}))};return s(i.body),i.body.innerHTML})),e.termAccept=ko.computed((function(){return!ko.utils.arrayFirst(e.termAcceptDocs(),(function(e){return 0==e.checkedStatus()}))})),e.termAcceptedDocs=ko.computed((function(){for(var t=[],n=0;n<e.termAcceptDocs().length;n++)e.termAcceptDocs()[n].checkedStatus()&&t.push("building::"+e.termAcceptDocs()[n].docId);return t})),e.removeDocs=function(){for(var t=e.termAcceptDocs().length,n=[],a=0;a<t;a++)n.push(e.termAcceptDocs()[a].docName);for(a=0;a<t;a++)e.removeDoc(n[a])},e.removeDoc=function(t){e.termAcceptDocs.remove((function(e){return e.docName==t}))}}function m(e,t){var n={menuaction:"bookingfrontend.uiapplication.set_block",resource_id:$("#resource_id").val(),from_:dateFormat(e,"yyyy-mm-dd HH:MM"),to_:dateFormat(t,"yyyy-mm-dd HH:MM")};$.getJSON(phpGWLink("bookingfrontend/",n,!0),(function(e){"reserved"==e.status&&(alert("Opptatt"),window.location.replace(phpGWLink("bookingfrontend/",{menuaction:"bookingfrontend.uiresource.show",building_id:p.building_id,id:$("#resource_id").val()})))}))}function g(){var e=new Date,t=new Date(e.getFullYear(),e.getMonth()+i,0),n=phpGWLink("bookingfrontend/",{menuaction:"bookingfrontend.uibooking.get_freetime",building_id:p.building_id,resource_id:$("#resource_id").val(),start_date:formatSingleDateWithoutHours(new Date),end_date:formatSingleDateWithoutHours(t)},!0);$.getJSON(n,(function(e){for(var t in e){for(var n=0;n<e[t].length;n++)void 0!==e[t][n].applicationLink&&(e[t][n].applicationLink=phpGWLink("bookingfrontend/",e[t][n].applicationLink));d[t]=e[t]}})).done((function(){var e=$("#resource_id").val();if(void 0!==d[e]){for(var t,n,a=d[e],o=[-1],i=0;i<a.length;i++)0==a[i].overlap&&(t=new Date(parseInt(a[i].start)),n=dateFormat(t,"yyyy/mm/dd"),o.push(n));1==o.length&&($("#from_").val(""),$("#to_").val(""),$("#start_date").val(""),$("#selected_period").html("")),$("#start_date").datetimepicker("setOptions",{allowDates:o})}}))}ko.validation.locale("nb-NO"),$(document).ready((function(){var e;c=new u,ko.applyBindings(c,document.getElementById("new-application-page"));let t=phpGWLink("bookingfrontend/",{menuaction:"bookingfrontend.uiapplication.add",building_id:p.building_id,phpgw_return_as:"json"},!0);$.getJSON(t,(function(n){$("#inputTargetAudience").val(n.audience[0].id),e=n.application.activity_id,c.activityId(e);for(var a=0;a<n.agegroups.length;a++)c.agegroupList.push({name:n.agegroups[a].name,agegroupLabel:n.agegroups[a].name,inputCountMale:ko.observable("").extend({number:!0}),inputCountFemale:ko.observable("").extend({number:!0}),malename:"male["+n.agegroups[a].id+"]",femalename:"female["+n.agegroups[a].id+"]",id:n.agegroups[a].id});if(null!=initialAgegroups)for(a=0;a<initialAgegroups.length;a++){var o=initialAgegroups[a].agegroup_id,i=ko.utils.arrayFirst(c.agegroupList(),(function(e){return e.id==o}));i&&(i.inputCountMale(initialAgegroups[a].male),i.inputCountFemale(initialAgegroups[a].female))}for(a=0;a<n.audience.length;a++)$.inArray(n.audience[a].id,initialAudience)>-1&&$("#audienceDropdownBtn").text(n.audience[a].name),$("#inputTargetAudience").val(n.audience[a].id);if(t=phpGWLink("bookingfrontend/",{menuaction:"bookingfrontend.uiresource.index_json",filter_building_id:p.building_id,sort:"name",phpgw_return_as:"json"},!0),$.getJSON(t,(function(e){for(var t=0;t<e.results.length;t++)console.log($("#resource_id")),$("#resource_id").val()==e.results[t].id&&(c.selectedResource(e.results[t]),e.results[t].booking_day_default_lenght&&-1!=e.results[t].booking_day_default_lenght&&(s=e.results[t].booking_day_default_lenght),e.results[t].booking_time_default_end&&-1!=e.results[t].booking_time_default_end&&(l=e.results[t].booking_time_default_end),e.results[t].booking_time_default_start&&-1!=e.results[t].booking_time_default_start&&(r=e.results[t].booking_time_default_start),$("#resource_list").hide()),s||(l=r+1)})).done((function(){!function(){var e,t;if($("#resource_id").val()||($("#time_select").hide(),$("#item-description").html(""),$("#selected_period").html("<b>Velg leieobjekt</b>")),null!=initialDates)for(var n=0;n<initialDates.length;n++){var a=initialDates[n].from_.replace(" ","T")+"Z",o=initialDates[n].to_.replace(" ","T")+"Z";c.date.push({from_:formatDateToDateTimeString(new Date(a)),to_:formatDateToDateTimeString(new Date(o)),formatedPeriode:formatDate(new Date(a),new Date(o))})}else void 0!==p.start&&void 0!==p.end&&p.start.length>0&&p.end.length>0&&c.date.push({from_:formatDateToDateTimeString(new Date(parseInt(p.start))),to_:formatDateToDateTimeString(new Date(parseInt(p.end))),formatedPeriode:formatDate(new Date(parseInt(p.start)),new Date(parseInt(p.end)))});if(null!=initialDates){for(n=0;n<initialDates.length;n++)a=initialDates[n].from_.replace(" ","T")+"Z",o=initialDates[n].to_.replace(" ","T")+"Z",e=new Date(a),t=new Date(o);initialDates.length>0&&(r=e.getHours(),l=t.getHours(),$("#start_date").datetimepicker("destroy"))}else void 0!==p.start&&void 0!==p.end&&p.start.length>0&&p.end.length>0&&(e=new Date(parseInt(p.start)),-1!=s&&0!=s||(r=e.getHours()),e.setHours(r,0),(t=new Date(parseInt(p.start))).setDate(t.getDate()+s),-1!=s&&0!=s||(t=new Date(parseInt(p.end)),l=t.getHours()),t.setHours(l,0),$("#time_select").hide());$("#resource_id").val()&&e&&($("#from_").val(formatSingleDate(e)),$("#to_").val(formatSingleDate(t)),$("#start_date").val(formatSingleDateWithoutHours(e)),$("#start_date").prop("disabled",!0),"function"==typeof post_handle_order_table&&setTimeout((function(){post_handle_order_table()}),500)),$("#bookingStartTime").val(r),$("#bookingEndTime").val(l),$("#resource_id").val()&&(g(),void 0!==e&&m(e,t))}()})),$("#resource_id").val()){var d={menuaction:"bookingfrontend.uidocument_view.regulations","owner[]":"building::"+p.building_id,sort:"name"};t=phpGWLink("bookingfrontend/",d,!0),t+="&owner[]=resource::"+$("#resource_id").val(),$.getJSON(t,(function(e){for(var t=0;t<e.data.length;t++){var n=!1;null!=initialAcceptedDocs&&"on"==initialAcceptedDocs[t]&&(n=!0),c.termAcceptDocs.push({docName:e.data[t].name,itemLink:RemoveCharacterFromURL(e.data[t].link,"amp;"),checkedStatus:ko.observable(n),docId:e.data[t].id.replace(/^\D+/g,"")})}}))}})).done((function(){"undefined"!=typeof initialAudience&&null!=initialAudience&&$("#inputTargetAudience").val(initialAudience)})),$("#application_form").submit((function(e){c.termAccept()||(alert(errorAcceptedDocs),e.preventDefault())}))})),$("#start_date").datetimepicker({onSelectDate:function(e,t){var n=new Date(e);n.setHours(r,0);var a=new Date(e);a.setDate(e.getDate()+s),a.setHours(l,0),n.getTime()<a.getTime()?($("#from_").val(formatSingleDate(n)),$("#to_").val(formatSingleDate(a)),$("#selected_period").html(formatPeriodeHours(n,a))):n.getTime()>=a.getTime()&&alert("Starttid må være tidligere enn sluttid"),m(n,a)}}),$(document).ready((function(){$("#resource_id").change((function(){if(l=10,r=15,(s=0)||(l=r+1),c.removeDocs(),$(this).val()){var e={menuaction:"bookingfrontend.uiresource.read_single",id:$(this).val()};getJsonURL=phpGWLink("bookingfrontend/",e,!0),$.getJSON(getJsonURL,(function(e){null!==e.booking_day_default_lenght&&-1!=e.booking_day_default_lenght&&(s=e.booking_day_default_lenght),null!==e.booking_time_default_end&&-1!=e.booking_time_default_end&&(l=e.booking_time_default_end),null!==e.booking_time_default_start&&-1!=e.booking_time_default_start&&(r=e.booking_time_default_start),Number(e.booking_month_horizon)>i+1&&(i=Number(e.booking_month_horizon)+1),$("#item-description").html(e.description),$("#bookingStartTime").val(r),$("#bookingEndTime").val(l),$("#from_").val(),$("#to_").val(),$("#selected_period").html("<b>Velg dato</b>"),$("#start_date").val("")})).done((function(){$("#time_select").show()})),e={menuaction:"bookingfrontend.uidocument_view.regulations","owner[]":"building::"+p.building_id,sort:"name"},getJsonURL=phpGWLink("bookingfrontend/",e,!0),getJsonURL+="&owner[]=resource::"+$("#resource_id").val(),$.getJSON(getJsonURL,(function(e){for(var t=0;t<e.data.length;t++){var n=!1;null!=initialAcceptedDocs&&"on"==initialAcceptedDocs[t]&&(n=!0),c.termAcceptDocs.push({docName:e.data[t].name,itemLink:RemoveCharacterFromURL(e.data[t].link,"amp;"),checkedStatus:ko.observable(n),docId:e.data[t].id.replace(/^\D+/g,"")})}})),g()}else $("#time_select").hide(),$("#item-description").html(""),$("#selected_period").html("<b>Velg leieobjekt</b>")}))})),window.onload=function(){const e=document.getElementById("submit-error");document.getElementById("inputTargetAudience"),form.addEventListener("submit",(function(t){$("#resource_id").val()&&c.date()||(t.preventDefault(),t.stopPropagation(),$("#inputTargetAudience").val()||$("#inputTargetAudience").val(-1),e.style.display="block",setTimeout((function(){e.style.display="none"}),5e3))}))}})()})();