(function(t){function e(e){for(var i,s,r=e[0],c=e[1],l=e[2],u=0,d=[];u<r.length;u++)s=r[u],o[s]&&d.push(o[s][0]),o[s]=0;for(i in c)Object.prototype.hasOwnProperty.call(c,i)&&(t[i]=c[i]);p&&p(e);while(d.length)d.shift()();return a.push.apply(a,l||[]),n()}function n(){for(var t,e=0;e<a.length;e++){for(var n=a[e],i=!0,s=1;s<n.length;s++){var r=n[s];0!==o[r]&&(i=!1)}i&&(a.splice(e--,1),t=c(c.s=n[0]))}return t}var i={},s={app:0},o={app:0},a=[];function r(t){return c.p+"js/"+({LogIn:"LogIn"}[t]||t)+"."+{LogIn:"778397c7"}[t]+".js"}function c(e){if(i[e])return i[e].exports;var n=i[e]={i:e,l:!1,exports:{}};return t[e].call(n.exports,n,n.exports,c),n.l=!0,n.exports}c.e=function(t){var e=[],n={LogIn:1};s[t]?e.push(s[t]):0!==s[t]&&n[t]&&e.push(s[t]=new Promise(function(e,n){for(var i="css/"+({LogIn:"LogIn"}[t]||t)+"."+{LogIn:"2f38ad85"}[t]+".css",o=c.p+i,a=document.getElementsByTagName("link"),r=0;r<a.length;r++){var l=a[r],u=l.getAttribute("data-href")||l.getAttribute("href");if("stylesheet"===l.rel&&(u===i||u===o))return e()}var d=document.getElementsByTagName("style");for(r=0;r<d.length;r++){l=d[r],u=l.getAttribute("data-href");if(u===i||u===o)return e()}var p=document.createElement("link");p.rel="stylesheet",p.type="text/css",p.onload=e,p.onerror=function(e){var i=e&&e.target&&e.target.src||o,a=new Error("Loading CSS chunk "+t+" failed.\n("+i+")");a.request=i,delete s[t],p.parentNode.removeChild(p),n(a)},p.href=o;var h=document.getElementsByTagName("head")[0];h.appendChild(p)}).then(function(){s[t]=0}));var i=o[t];if(0!==i)if(i)e.push(i[2]);else{var a=new Promise(function(e,n){i=o[t]=[e,n]});e.push(i[2]=a);var l,u=document.createElement("script");u.charset="utf-8",u.timeout=120,c.nc&&u.setAttribute("nonce",c.nc),u.src=r(t),l=function(e){u.onerror=u.onload=null,clearTimeout(d);var n=o[t];if(0!==n){if(n){var i=e&&("load"===e.type?"missing":e.type),s=e&&e.target&&e.target.src,a=new Error("Loading chunk "+t+" failed.\n("+i+": "+s+")");a.type=i,a.request=s,n[1](a)}o[t]=void 0}};var d=setTimeout(function(){l({type:"timeout",target:u})},12e4);u.onerror=u.onload=l,document.head.appendChild(u)}return Promise.all(e)},c.m=t,c.c=i,c.d=function(t,e,n){c.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},c.r=function(t){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},c.t=function(t,e){if(1&e&&(t=c(t)),8&e)return t;if(4&e&&"object"===typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(c.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var i in t)c.d(n,i,function(e){return t[e]}.bind(null,i));return n},c.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return c.d(e,"a",e),e},c.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},c.p="/web/",c.oe=function(t){throw console.error(t),t};var l=window["webpackJsonp"]=window["webpackJsonp"]||[],u=l.push.bind(l);l.push=e,l=l.slice();for(var d=0;d<l.length;d++)e(l[d]);var p=u;a.push([0,"chunk-vendors"]),n()})({0:function(t,e,n){t.exports=n("56d7")},"034f":function(t,e,n){"use strict";var i=n("64a9"),s=n.n(i);s.a},"04ca":function(t,e,n){"use strict";var i=n("d25a"),s=n.n(i);s.a},"067e":function(t,e,n){"use strict";var i=n("8254"),s=n.n(i);s.a},"0a39":function(t,e,n){"use strict";var i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("div",{staticClass:"panel panel-primary"},[n("div",{staticClass:"panel-heading"},[n("span",{staticClass:"glyphicon glyphicon-list"}),t._v("事项列表\n            "),n("span",{on:{click:function(e){return e.stopPropagation(),t.parentDir(e)}}},[t._v("上一级目录")]),t._m(0)]),n("div",{staticClass:"panel-body"},t._l(t.items,function(e,i){return n("ul",{key:i,staticClass:"list-group"},[n("li",{staticClass:"list-group-item",attrs:{draggable:"true"},on:{click:function(n){n.stopPropagation(),t.getDetails(e)},dragstart:function(n){t.drag(e)},dragover:function(t){t.preventDefault()},drop:function(n){t.drop(e)}}},[n("div",{staticClass:"checkbox"},[n("input",{directives:[{name:"model",rawName:"v-model",value:e.isChecked,expression:"item.isChecked"}],attrs:{type:"checkbox",disabled:1==e.status},domProps:{checked:Array.isArray(e.isChecked)?t._i(e.isChecked,null)>-1:e.isChecked},on:{click:function(n){n.stopPropagation(),t.toggleStatus(e,i)},change:function(n){var i=e.isChecked,s=n.target,o=!!s.checked;if(Array.isArray(i)){var a=null,r=t._i(i,a);s.checked?r<0&&t.$set(e,"isChecked",i.concat([a])):r>-1&&t.$set(e,"isChecked",i.slice(0,r).concat(i.slice(r+1)))}else t.$set(e,"isChecked",o)}}}),n("label",{attrs:{for:"checkbox"}},[n("a",{staticStyle:{display:"inline-block"},attrs:{title:"事项或目录ID,单击可进入目录"},on:{click:function(n){n.stopPropagation(),t.subDir(e)}}},[n("span",[t._v(t._s(e.id))])]),n("span",{directives:[{name:"show",rawName:"v-show",value:!e.seen,expression:"!item.seen"}],attrs:{title:"事项名称"}},[t._v(t._s(e.name))]),n("input",{directives:[{name:"model",rawName:"v-model",value:e.name,expression:"item.name"},{name:"show",rawName:"v-show",value:e.seen,expression:"item.seen"}],domProps:{value:e.name},on:{click:function(t){t.stopPropagation()},keyup:function(n){if(!("button"in n)&&t._k(n.keyCode,"esc",27,n.key,["Esc","Escape"]))return null;t.save(e)},input:function(n){n.target.composing||t.$set(e,"name",n.target.value)}}})])]),n("div",{staticClass:"pull-right action-buttons"},[n("a",{class:e.visible_range==t.constant.visible_range.show_global.code?"glyphicon glyphicon-globe":"glyphicon glyphicon-globe gray",attrs:{title:e.visible_range==t.constant.visible_range.show_global.code?"全局可见,单击可改变可见范围":"仅当前目录下可见,双击可改变可见范围"},on:{dblclick:function(n){n.stopPropagation(),t.toggleVisibleRange(e)}}}),n("a",{on:{click:function(e){e.stopPropagation(),t.add()}}},[n("span",{staticClass:"glyphicon glyphicon-plus-sign",attrs:{title:"添加新事项"}})]),n("a",{on:{click:function(n){n.stopPropagation(),t.edit(e)}}},[n("span",{staticClass:"glyphicon glyphicon-pencil",attrs:{title:"编辑事项"}})]),n("a",{on:{click:function(n){n.stopPropagation(),t.save(e)}}},[n("span",{staticClass:"glyphicon glyphicon-saved",attrs:{title:"保存事项"}})]),n("a",{on:{click:function(t){t.stopPropagation()},dblclick:function(e){e.stopPropagation(),t.del(i)}}},[n("span",{staticClass:"glyphicon glyphicon-trash",attrs:{title:"双击删除事项(请慎重操作)"}})])])])])}),0)])])},s=[function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"pull-right action-buttons"},[n("div",{staticClass:"btn-group pull-right"},[n("button",{staticClass:"btn btn-default btn-xs dropdown-toggle",attrs:{type:"button","data-toggle":"dropdown"}},[n("span",{staticClass:"glyphicon glyphicon-cog",staticStyle:{"margin-right":"0px"}})]),n("ul",{staticClass:"dropdown-menu slidedown"},[n("li",[n("a",{attrs:{href:""}},[n("span",{staticClass:"glyphicon glyphicon-pencil"}),t._v("Edit")])]),n("li",[n("a",{attrs:{href:""}},[n("span",{staticClass:"glyphicon glyphicon-trash"}),t._v("Delete")])]),n("li",[n("a",{attrs:{href:""}},[n("span",{staticClass:"glyphicon glyphicon-flag"}),t._v("Flag")])])])])])}],o=n("59ad"),a=n.n(o),r=n("a745"),c=n.n(r),l=(n("55dd"),n("e814")),u=n.n(l),d=(n("7f7f"),n("cadf"),n("551c"),n("097d"),{name:"ItemsPanel",data:function(){return{constant:{settings:{perPage:10},visible_range:{show_global:{code:20,desc:"全局显示"},show_inside_parent:{code:10,desc:"只在父items下可见"}},status:{enable:{code:10,desc:"启用"},staging:{code:20,desc:"暂存"},draft:{code:30,desc:"归档"}}},runtimeState:{fid:"",drag_from:{},drag_to:{}},settings:{perPage:10},currentItem:"",items:[]}},created:function(){var t=this;this.runtimeState.fid=this.$route.query.fid?this.$route.query.fid:"",this.getItems();var e=this.$store.state;e.eventBus.$off(e.events.saveItem),e.eventBus.$on(e.events.saveItem,function(n){window.console.log(e.events.saveItem),t.save(n)})},methods:{newItem:function(){return{id:0,fid:this.runtimeState.fid,name:" ",rank:0,status:this.constant.status.enable.code,visible_range:this.constant.visible_range.show_inside_parent.code,isChecked:0,seen:!0,limit:this.settings.perPage,offset:0,notes:[],description:""}},edit:function(t){t.seen=!t.seen},save:function(t){var e=this,n=this.$store.state.urls.saveItem,i={id:t.id,name:t.name,description:t.description,fid:t.fid};this.$http.put(n,i).then(function(n){if(1!==n.body.status)   ;else{var i=n.body.data;0===u()(t.id)&&(t.id=i.id,i.rank&&(t.rank=i.rank)),t.seen=!1,t.rank&&e.sort()}})},del:function(t){var e=this,n=this.items[t],i=this.$store.state.urls.deleteItem,s={id:this.items[t].id};this.$http.delete(i,s).then(function(i){if(1!==i.body.status)   ;else{var s=e.$store.state;s.eventBus.$emit(s.events.itemDelete,n),e.items.splice(t,1)}})},getDetails:function(t){this.getNotes(t),this.getTodoList(t);var e=this.$store.state;e.eventBus.$emit(e.events.shouldGetDescription,t)},getTodoList:function(t){var e=this.$store.state;e.eventBus.$emit(e.events.shouldGetTodoList,t)},getNotes:function(t){t.offset=0,t.limit=this.settings.perPage;var e=this.$store.state;e.eventBus.$emit(e.events.shouldGetNotes,t)},toggleStatus:function(t,e){var n=this,i=this.$store.state.urls.itemDraft,s={id:t.id};this.$http.put(i,s).then(function(i){if(1!==i.body.status)   ;else{n.items.splice(e,1);var s=n.$store.state;s.eventBus.$emit(s.events.itemDelete,t)}})},toggleVisibleRange:function(t){var e=this,n=this.$store.state.urls.itemToggleVisibleRange,i={id:t.id};this.$http.put(n,i).then(function(n){if(1!==n.body.status)   ;else{var i=n.body.data;t.visible_range=i.visible_range,e.sort()}})},getItems:function(){var t=this,e=this.$store.state.urls.items,n=this.constant.status.enable.code,i={params:{fid:this.runtimeState.fid,status:[n]}};this.$http.get(e,i).then(function(e){if(1!==e.body.status)   ;else{var n=e.body.data;c()(n)&&0===n.length?n=[t.newItem()]:(n=n.map(function(e){return e.seen=!1,e.offset=0,e.limit=t.settings.perPage,e.isChecked=t.constant.status.staging.code===u()(e.status)?1:0,e}),n.length>0&&t.getDetails(n[0])),t.items=n;var i=t.$store.state;i.eventBus.$emit(i.events.itemsChange,t.items)}})},parentDir:function(){var t=this,e=this.runtimeState.fid,n=this.$store.state.urls.parentDir,i={params:{id:e}};this.$http.get(n,i).then(function(e){if(1!==e.body.status)   ;else{var n=e.body.data;t.runtimeState.fid=n.dir,t.getItems()}})},subDir:function(t){this.runtimeState.fid=t.id,this.getItems()},add:function(){this.items.unshift(this.newItem())},drag:function(t){this.runtimeState.drag_from=t},drop:function(t){var e=this,n=this.runtimeState.drag_from,i=this.runtimeState.drag_to=t,s=0,o=this.items.indexOf(t);if(a()(n.rank)<a()(i.rank))if(o=this.items.indexOf(i),o-1<0)s=a()(i.rank)+1/3;else{var r=this.items[o-1];s=(a()(r.rank)+a()(i.rank))/2}else if(a()(n.rank)>a()(i.rank))if(o+1>=this.items.length)s=a()(i.rank)-1/3;else{var c=this.items[o+1];s=(a()(i.rank)+a()(c.rank))/2}var l=this.$store.state.urls.rank,u={dragFrom:n.id,dragTo:i.id,rank:s};this.$http.put(l,u).then(function(t){if(1!==t.body.status)   ;else{var i=t.body.data;n.rank=i.rank,e.sort()}})},sort:function(){this.items.sort(function(t,e){return u()(t.visible_range)>u()(e.visible_range)?-1:t.visible_range<e.visible_range?1:a()(t.rank)>a()(e.rank)?-1:a()(t.rank)<a()(e.rank)?1:0})}}}),p=d,h=(n("8829"),n("2877")),f=Object(h["a"])(p,i,s,!1,null,"a75fb8da",null);f.options.__file="ItemsPanel.vue";e["a"]=f.exports},"0d4e":function(t,e,n){},1:function(t,e){},"56d7":function(t,e,n){"use strict";n.r(e);n("cadf"),n("551c"),n("097d");var i=n("2b0e"),s=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{attrs:{id:"app"}},[n("header",[n("router-link",{staticClass:"col-xs-3 AppHeader-navItem",attrs:{to:"/"}},[t._v("主页")]),n("router-link",{staticClass:"col-xs-3 AppHeader-navItem",attrs:{to:"/backup/notes"}},[t._v("归档笔记")]),n("router-link",{staticClass:"col-xs-3 AppHeader-navItem",attrs:{to:"/backup/items"}},[t._v("归档事项")]),n("router-link",{staticClass:"col-xs-2 AppHeader-navItem",attrs:{to:"/signUp/later"}},[t._v("补打卡")]),n("router-link",{staticClass:"col-xs-1 AppHeader-navItem",attrs:{to:"/logout"}},[t._v("登出")])],1),n("router-view")],1)},o=[],a=(n("034f"),n("2877")),r={},c=Object(a["a"])(r,s,o,!1,null,null,null);c.options.__file="App.vue";var l=c.exports,u=n("8c4f"),d=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{attrs:{id:"ffz_app_main"}},[n("div",{attrs:{title:"同时按下<CtrL>和<ENTER>,可隐藏或显示左侧导航栏",tabindex:"0"},on:{keydown:function(e){return("button"in e||!t._k(e.keyCode,"enter",13,e.key,"Enter"))&&e.ctrlKey?void t.toggleItemsTab():null}}},[n("div",{class:1===t.settings.seen_items?"col-lg-4":"hide"},[n("items-panel")],1),n("div",{class:1===t.settings.seen_items?"col-lg-8":"col-lg-12"},[n("item-description"),n("todo-lists-panel"),n("notes-panel")],1)])])},p=[],h=n("0a39"),f=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"panel-heading"},[n("div",[n("ul",t._l(t.notes,function(e,i){return n("li",{key:i,attrs:{id:"note_"+e.id}},[n("span",{staticClass:"hidden-xs",attrs:{title:"创建时间"}},[t._v(t._s(e.c_time))]),n("span",[n("select",{directives:[{name:"model",rawName:"v-model",value:e.item_id,expression:"note.item_id"}],staticClass:"items",attrs:{title:"选择其他目录,则自动将笔记移动到其他目录下了"},on:{change:[function(n){var i=Array.prototype.filter.call(n.target.options,function(t){return t.selected}).map(function(t){var e="_value"in t?t._value:t.value;return e});t.$set(e,"item_id",n.target.multiple?i:i[0])},function(n){t.mv(e,i)}]}},[e.item_id==t.item.id?t._l(t.items,function(e,i){return n("option",{key:i,attrs:{selected:""},domProps:{value:e.id}},[t._v(t._s(e.name))])}):t._l(t.items,function(e,i){return n("option",{key:i,domProps:{value:e.id}},[t._v(t._s(e.name))])})],2)]),n("div",{staticClass:"pull-right action-buttons"},[n("span",{on:{click:function(n){n.stopPropagation(),t.done(e,i)}}},[n("input",{directives:[{name:"model",rawName:"v-model",value:e.isChecked,expression:"note.isChecked"}],attrs:{type:"checkbox",title:"标记完成"},domProps:{checked:Array.isArray(e.isChecked)?t._i(e.isChecked,null)>-1:e.isChecked},on:{change:function(n){var i=e.isChecked,s=n.target,o=!!s.checked;if(Array.isArray(i)){var a=null,r=t._i(i,a);s.checked?r<0&&t.$set(e,"isChecked",i.concat([a])):r>-1&&t.$set(e,"isChecked",i.slice(0,r).concat(i.slice(r+1)))}else t.$set(e,"isChecked",o)}}})]),n("span",{on:{click:function(e){e.stopPropagation(),t.add()}}},[n("a",{staticClass:"glyphicon glyphicon-plus-sign",attrs:{title:"添加笔记"}})]),t._v("\n                     \n                    "),n("span",{on:{click:function(n){n.stopPropagation(),t.edit(e,n)}}},[n("a",{staticClass:"glyphicon glyphicon-edit",attrs:{title:"编辑笔记"}})]),t._v("\n                     \n                    "),n("span",{on:{click:function(n){n.stopPropagation(),t.save(e)}}},[n("a",{staticClass:"glyphicon glyphicon-saved",attrs:{title:"保存笔记"}})]),t._v("\n                      \n                    "),n("span",{on:{click:function(t){t.stopPropagation()},dblclick:function(e){t.del(i)}}},[n("a",{staticClass:"glyphicon glyphicon-trash",attrs:{title:"双击删除笔记"}})])]),n("div",[e.seen?n("textarea",{directives:[{name:"model",rawName:"v-model",value:e.modifiedContent,expression:"note.modifiedContent"},{name:"focus",rawName:"v-focus"}],staticClass:"col-xs-12",domProps:{value:e.modifiedContent},on:{keydown:function(n){return("button"in n||83===n.keyCode)&&n.ctrlKey?(n.preventDefault(),void t.save(e,1)):null},keyup:[function(n){if(!("button"in n)&&t._k(n.keyCode,"esc",27,n.key,["Esc","Escape"]))return null;t.save(e)},function(e){if(!("button"in e)&&t._k(e.keyCode,"enter",13,e.key,"Enter"))return null;t.h(e)}],focus:function(n){t.h(n,e)},paste:function(n){t.image(n,e)},input:function(n){n.target.composing||t.$set(e,"modifiedContent",n.target.value)}}}):t._e(),e.seen&&void 0!==e.pictures&&e.pictures.length>0?n("table",{staticClass:"table"},[n("thead"),n("tbody",t._l(e.pictures,function(t,e){return n("tr",{key:e},[n("td",[n("img",{attrs:{src:t}})])])}),0)]):t._e(),e.seen?t._e():n("div",{staticClass:"textarea",domProps:{innerHTML:t._s(e.md)},on:{dblclick:function(n){n.stopPropagation(),t.edit(e)}}})])])}),0),n("div",{directives:[{name:"show",rawName:"v-show",value:t.showMore,expression:"showMore"}],staticClass:"text-center more",on:{click:function(e){e.stopPropagation(),t.more()}}},[t._v("更多")])])])},g=[],m=n("a745"),v=n.n(m),y=n("0e54"),b=n.n(y),k=n("1487"),C=n.n(k);n("2c43");b.a.setOptions({highlight:function(t){return C.a.highlightAuto(t).value}});var _={name:"NotesPanel",directives:{focus:{inserted:function(t){t.focus()}}},data:function(){return{settings:{textarea_default_height:50},constant:{status:{enable:{code:10,desc:"有效且已保存"},disable:{code:20,desc:"未保存"}}},notes:[],item:{},items:[]}},created:function(){var t=this,e=this.$store.state;e.eventBus.$off(e.events.shouldGetNotes),e.eventBus.$on(e.events.shouldGetNotes,function(e){t.doGetNotes(e)}),e.eventBus.$off(e.events.itemsChange),e.eventBus.$on(e.events.itemsChange,function(e){t.items=e}),e.eventBus.$off(e.events.itemDelete),e.eventBus.$on(e.events.itemDelete,function(n){window.console.log(e.events.itemDelete,"item",n,"this.item",t.item),n===t.item&&(t.notes=[])})},computed:{showMore:function(){return this.item?this.item.limit:0}},methods:{now:function(){var t=new Date,e=t.getMonth()+1,n=t.getDate(),i=t.getFullYear(),s=t.getHours(),o=t.getMinutes(),a=t.getSeconds();return i+"-"+e+"-"+n+"-"+s+":"+o+":"+a},newNote:function(){var t="undefined"===typeof this.item?0:this.item.id;return{id:0,item_id:t,content:"",c_time:this.now(),seen:!0,modifiedContent:"",pictures:[]}},doGetNotes:function(t,e){var n=this;if("undefined"!==typeof t){var i=this.$store.state.urls.getNotes,s={params:{item_id:t.id,offset:t.offset,limit:t.limit}};this.$http.get(i,s).then(function(i){if(1!==i.body.status)   ;else{var s=i.body.data;if(v()(s)&&s.length<t.limit&&(t.limit=0),0===s.length){if(!t.id)return!1;s="append"===e?[]:[n.newNote()]}else s=s.map(function(t){return t.md=b()(t.content),t.seen=!1,t});t.notes="append"===e?t.notes.concat(s):s,t.offset=t.notes.length}}).then(function(){n.item=t,n.notes=t.notes})}else window.console.log("item is undefined",t)},add:function(){if("undefined"==typeof this.item||!this.item.id)return!1;var t=this.newNote();return this.notes.unshift(t),!0},image:function(t,e){if(t.clipboardData||t.originalEvent){var n=t.clipboardData||t.originalEvent.clipboardData;if(n.items){for(var i=n.items,s=i.length,o=null,a=0;a<s;a++)-1!==i[a].type.indexOf("image")&&(o=i[a].getAsFile());if(o){var r=t.target,c=this.$store.state.urls.image,l=new FormData;l.append("img",o),this.$http.post(c,l,{headers:{"Content-Type":"multipart/form-data"}}).then(function(n){if(1!==n.body.status)   ;else{var i=n.body.data;e.modifiedContent=t.target.value=r.value.substr(0,r.selectionStart+1)+"![]("+i.url+")"+r.value.substr(r.selectionStart),e.pictures=[i.url]}})}}}},h:function(t){var e=t.target,n=e.style.height,i=n.substring(0,n.length-2);i<e.scrollHeight&&(e.style.height=t.target.scrollHeight+this.settings.textarea_default_height+"px")},edit:function(t){t.modifiedContent=t.content,t.seen=!0},save:function(t,e){var n=this;if(t.item_id){t.content=t.modifiedContent;var i=this.$store.state.urls.saveNote,s={id:t.id,item_id:t.item_id,content:t.content};this.$http.post(i,s).then(function(i){if(1!==i.body.status)   ;else{var s=i.body.data;0==t.id&&(t.id=s.id),t.md=b()(t.content)}e||(t.seen=!1),n.item.offset=n.notes.length})}},del:function(t){var e=this,n=this.$store.state.urls.deleteNote,i={body:{id:this.notes[t].id}};this.$http.delete(n,i).then(function(n){1!==n.body.status||(e.notes.splice(t,1),e.item.offset=e.notes.length)})},mv:function(t,e){var n=this,i=this.$store.state.urls.moveNote,s={id:t.id,itemId:t.item_id};this.$http.put(i,s).then(function(t){1!==t.body.status||(n.notes.splice(e,1),n.item.offset=n.notes.length)})},more:function(){if(this.item){var t=this.$store.state;t.eventBus.$emit(t.events.itemUpdateLimitAndOffset,this.item),this.doGetNotes(this.item,"append")}},done:function(t,e){var n=this,i=this.$store.state.urls.noteDone,s={note_id:t.id};this.$http.put(i,s).then(function(t){1!==t.body.status||(n.notes.splice(e,1),n.item.offset=n.notes.length)})}}},w=_,P=(n("c088"),Object(a["a"])(w,f,g,!1,null,"747e2580",null));P.options.__file="NotesPanel.vue";var D=P.exports,x=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"panel-heading"},t._l(t.collections,function(e,i){return n("div",{key:i,staticClass:"collection",attrs:{id:"collections_"+e.id}},[n("ul",[n("li",{staticClass:"collection_header"},[n("span",[n("input",{attrs:{type:"checkbox",title:"选中表示今天任务完成"},on:{click:function(n){n.stopPropagation(),n.preventDefault(),t.collectionDoneToday(e,i)}}})]),n("span",[t._v("［"+t._s(e.description)+"］,共"+t._s(e.total_count)+"天,已打卡"+t._s(e.check_in_count)+"天,坚持率"+t._s((e.check_in_count/e.total_count*100).toFixed(2))+"%")])]),t._l(e.todo,function(i,s){return n("li",{key:s,attrs:{id:"todos_"+e.id}},[n("div",{staticClass:"action-buttons"},[n("span",[n("input",{attrs:{type:"checkbox",title:"选中表示今天任务完成"},on:{click:function(n){n.stopPropagation(),n.preventDefault(),t.todoDoneToday(e,i,s)}}})]),n("span",[t._v(t._s(i.description))])])])})],2)])}),0)},N=[],I={name:"TodoListsPanel",data:function(){return{collections:[],item:{},items:[]}},created:function(){var t=this,e=this.$store.state;e.eventBus.$off(e.events.shouldGetTodoList),e.eventBus.$on(e.events.shouldGetTodoList,function(e){t.getList(e)})},methods:{getList:function(t){var e=this;t!==this.item&&(this.collections=[]);var n=this.$store.state.urls.getTodoList,i={params:{item_id:t.id}};this.$http.get(n,i).then(function(n){var i=n.body.data;if(0===i.length)return!1;e.collections=i,e.item=t})},todoDoneToday:function(t,e,n){var i=this.$store.state.urls.todoDoneToday,s={todo_id:e.id};this.$http.post(i,s).then(function(e){1===e.body.status&&t.todo.splice(n,1)})},collectionDoneToday:function(t,e){var n=this,i=this.$store.state.urls.collectionDoneToday,s={collection_id:t.id};this.$http.post(i,s).then(function(t){1===t.body.status&&n.collections.splice(e,1)})}}},L=I,E=(n("04ca"),Object(a["a"])(L,x,N,!1,null,"7185ae44",null));E.options.__file="TodoListsPanel.vue";var A=E.exports,S=n("9013"),B={name:"Home",data:function(){return{settings:{seen_items:1}}},components:{ItemDescription:S["a"],ItemsPanel:h["a"],NotesPanel:D,TodoListsPanel:A},methods:{toggleItemsTab:function(){this.settings.seen_items=1==this.settings.seen_items?0:1}}},T=B,O=Object(a["a"])(T,d,p,!1,null,null,null);O.options.__file="Home.vue";var j=O.exports;i["a"].use(u["a"]);var H=new u["a"]({mode:"hash",base:"/web/",routes:[{path:"/",name:"home",component:j},{path:"/login",component:function(){return n.e("LogIn").then(n.bind(null,"9ddf"))}},{path:"/logout",component:function(){return n.e("LogIn").then(n.bind(null,"9ddf"))}},{path:"/backup/notes",component:function(){return n.e("LogIn").then(n.bind(null,"c502"))}},{path:"/signUp/later",component:function(){return n.e("LogIn").then(n.bind(null,"49d1"))}}]}),G=n("2f62");i["a"].use(G["a"]);var M=new G["a"].Store({state:{urls:{image:{path:"/image/save",description:"保存图片"},logout:{path:"/logout",description:"登陆"},login:{path:"/login/in",description:"登陆"},saveItem:{path:"/item/item",description:"保存事项"},deleteItem:{path:"/item/item",description:"删除事项"},items:{path:"/item/items",description:"获取事项列表"},itemToggleVisibleRange:{path:"item/toggle-visible-range",description:"改变事项的显示作用域"},parentDir:{path:"/item/parent-dir",description:"获取上级目录"},rank:{path:"/item/rank",description:"事项排序"},itemDraft:{path:"/item/item-draft",description:"事项归档"},getTodoList:{path:"/item/todo-list",description:"待办列表"},todoDoneToday:{path:"/item/todo-done-today",description:"完成事项列表"},collectionDoneToday:{path:"/item/collection-done-today",description:"今天完成的集合列表"},getBackupNotes:{path:"/note/item-backupNotes",description:"获取事项下的笔记"},getNotes:{path:"/note/item-notes",description:"获取事项下的笔记"},moveNote:{path:"/note/move-note",description:"改变笔记所属的事项"},saveNote:{path:"/note/note",description:"保存或更新具体一条笔记"},deleteNote:{path:"/note/note",description:"删除具体一条笔记"},noteDone:{path:"/note/note-done",description:"归档具体一条笔记"},noteTodo:{path:"/note/note-todo",description:"将具体一条笔记打回待办"}},events:{saveItem:"save-item",itemUpdateLimitAndOffset:"item-update-limit-and-offset",shouldGetDescription:"should-get-description",shouldGetTodoList:"should-get-todo-list",shouldGetNotes:"should-get-notes",itemResetLimitAndOffset:"item-reset-limit-and-offset",itemsChange:"items-change",itemDelete:"item-delete"},eventBus:new i["a"]({items:[]}),picture_index:1},mutations:{},actions:{"should-get-notes":function(t){window.console.log("should-get-notes",t)},"item-reset-limit-and-offset":function(t){t.limit=t.settings.perpage,t.offset=t.notes.length},"items-change":function(t){window.console.log(t)},"item-delete":function(t){window.console.log("item-delete","item",t),t.notes=[]}}}),F=n("28dd");i["a"].use(F["a"]),i["a"].http.interceptors.push(function(t,e){var n=t.url.description;t.url=t.url.path,e(function(t){return 403===t.status?window.location.href="/#/login":302===t.status?window.location.href="/#/":200===t.status?1!==t.body.status&&window.console.error("".concat(n,"失败，原因: ").concat(t.body.msg)):window.console.error("".concat(n,"失败(stuats code !=200)，原因: ").concat(t.status)),t})}),new i["a"]({router:H,store:M,render:function(t){return t(l)}}).$mount("#app")},"64a9":function(t,e,n){},8254:function(t,e,n){},8829:function(t,e,n){"use strict";var i=n("c103"),s=n.n(i);s.a},9013:function(t,e,n){"use strict";var i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"panel-heading",attrs:{id:"itemDescription"}},[n("div",[n("div",{staticClass:"pull-right action-buttons"},[n("span",{on:{click:function(e){e.stopPropagation(),t.edit(e)}}},[n("a",{staticClass:"glyphicon glyphicon-edit",attrs:{title:"编辑描述"}})]),t._v("\n             \n            "),n("span",{on:{click:function(e){e.stopPropagation(),t.save()}}},[n("a",{staticClass:"glyphicon glyphicon-saved",attrs:{title:"保存描述"}})])]),n("div",[t.seen?n("textarea",{directives:[{name:"model",rawName:"v-model",value:t.modifiedContent,expression:"modifiedContent"},{name:"focus",rawName:"v-focus"}],staticClass:"col-xs-12",domProps:{value:t.modifiedContent},on:{keydown:function(e){return("button"in e||83===e.keyCode)&&e.ctrlKey?(e.preventDefault(),void t.save(1)):null},keyup:[function(e){if(!("button"in e)&&t._k(e.keyCode,"esc",27,e.key,["Esc","Escape"]))return null;t.save()},function(e){if(!("button"in e)&&t._k(e.keyCode,"enter",13,e.key,"Enter"))return null;t.h(e)}],focus:function(e){t.h(e)},paste:function(e){t.image(e)},input:function(e){e.target.composing||(t.modifiedContent=e.target.value)}}}):t._e(),t.seen&&void 0!==t.pictures&&t.pictures.length>0?n("table",{staticClass:"table"},[n("thead"),n("tbody",t._l(t.pictures,function(t,e){return n("tr",{key:e},[n("td",[n("img",{attrs:{src:t}})])])}),0)]):t._e(),t.seen?t._e():n("div",{directives:[{name:"highlightjs",rawName:"v-highlightjs"}],staticClass:"textarea",domProps:{innerHTML:t._s(t.md)},on:{dblclick:function(e){e.stopPropagation(),t.edit()}}})])])])},s=[],o=(n("ac6a"),n("cadf"),n("551c"),n("097d"),n("0e54")),a=n.n(o),r=n("1487"),c=n.n(r),l=(n("2c43"),{name:"ItemDescription",directives:{focus:{inserted:function(t){t.focus()}},highlightjs:{inserted:function(t){var e=t.querySelectorAll("pre code");Array.prototype.forEach.call(e,c.a.highlightBlock)},update:function(t,e,n,i){if(n.data.domProps.innerHTML!==i.data.domProps.innerHTML){var s=t.querySelectorAll("pre code");Array.prototype.forEach.call(s,c.a.highlightBlock)}}}},data:function(){return{settings:{textarea_default_height:50},item:{},seen:0,description:"",modifiedContent:"",md:"",pictures:""}},created:function(){var t=this,e=this.$store.state;e.eventBus.$off(e.events.shouldGetDescription),e.eventBus.$on(e.events.shouldGetDescription,function(e){t.getDescription(e)})},methods:{getDescription:function(t){t.description||(t.description=" "),this.description=t.description,this.modifiedContent=this.description,this.item=t,this.md=a()(this.modifiedContent)},image:function(t){var e=this;if(t.clipboardData||t.originalEvent){var n=t.clipboardData||t.originalEvent.clipboardData;if(n.items){for(var i=n.items,s=i.length,o=null,a=0;a<s;a++)-1!==i[a].type.indexOf("image")&&(o=i[a].getAsFile());if(o){var r=t.target,c=this.$store.state.urls.image,l=new FormData;l.append("img",o),this.$http.post(c,l,{headers:{"Content-Type":"multipart/form-data"}}).then(function(n){if(1!==n.body.status)   ;else{var i=n.body.data;e.modifiedContent=t.target.value=r.value.substr(0,r.selectionStart+1)+"![]("+i.url+")"+r.value.substr(r.selectionStart),e.pictures=[i.url]}})}}}},h:function(t){var e=t.target,n=e.style.height,i=n.substring(0,n.length-2);i<e.scrollHeight&&(e.style.height=t.target.scrollHeight+this.settings.textarea_default_height+"px")},edit:function(){this.modifiedContent=this.description,this.seen=!0},save:function(t){var e=this.$store.state;this.description=this.modifiedContent,this.item.description=this.description,this.md=a()(this.description),t||(this.seen=!1),e.eventBus.$emit(e.events.saveItem,this.item)}}}),u=l,d=(n("067e"),n("2877")),p=Object(d["a"])(u,i,s,!1,null,"53f61a16",null);p.options.__file="ItemDescription.vue";e["a"]=p.exports},c088:function(t,e,n){"use strict";var i=n("0d4e"),s=n.n(i);s.a},c103:function(t,e,n){},d25a:function(t,e,n){}});
//# sourceMappingURL=app.bf6a753d.js.map