(function(t){function e(e){for(var o,a,i=e[0],c=e[1],l=e[2],d=0,f=[];d<i.length;d++)a=i[d],r[a]&&f.push(r[a][0]),r[a]=0;for(o in c)Object.prototype.hasOwnProperty.call(c,o)&&(t[o]=c[o]);u&&u(e);while(f.length)f.shift()();return s.push.apply(s,l||[]),n()}function n(){for(var t,e=0;e<s.length;e++){for(var n=s[e],o=!0,i=1;i<n.length;i++){var c=n[i];0!==r[c]&&(o=!1)}o&&(s.splice(e--,1),t=a(a.s=n[0]))}return t}var o={},r={frontend:0},s=[];function a(e){if(o[e])return o[e].exports;var n=o[e]={i:e,l:!1,exports:{}};return t[e].call(n.exports,n,n.exports,a),n.l=!0,n.exports}a.m=t,a.c=o,a.d=function(t,e,n){a.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},a.r=function(t){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},a.t=function(t,e){if(1&e&&(t=a(t)),8&e)return t;if(4&e&&"object"===typeof t&&t&&t.__esModule)return t;var n=Object.create(null);if(a.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)a.d(n,o,function(e){return t[e]}.bind(null,o));return n},a.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return a.d(e,"a",e),e},a.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},a.p="";var i=window["exactmetricsjsonp"]=window["exactmetricsjsonp"]||[],c=i.push.bind(i);i.push=e,i=i.slice();for(var l=0;l<i.length;l++)e(i[l]);var u=c;s.push([4,"chunk-frontend-vendors","chunk-common"]),n()})({4:function(t,e,n){t.exports=n("d67f")},c618:function(t,e,n){},d67f:function(t,e,n){"use strict";n.r(e);n("e260"),n("e6cf"),n("cca6"),n("a79d");var o=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("li",{staticClass:"exactmetrics-adminbar-menu-item",attrs:{id:"wp-admin-bar-exactmetrics_frontend_button"}},[n("div",{class:t.toggleButtonClass,on:{click:t.toggleStatsVisibility}},[n("span",{staticClass:"ab-icon dashicons-before dashicons-chart-bar"}),n("span",{staticClass:"exactmetrics-admin-bar-handle-text",domProps:{textContent:t._s(t.text_insights)}}),t.has_notifications?n("span",{staticClass:"exactmetrics-menu-notification-indicator",domProps:{textContent:t._s(t.notifications.length)}}):t._e()]),t.statsVisible?n("div",{staticClass:"exactmetrics-frontend-stats"},[t.noauth?n("frontend-no-auth"):t.error?n("widget-report-error",{attrs:{error:t.error}}):n("frontend-stats-content"),t.loaded?t._e():n("div",{staticClass:"exactmetrics-frontend-stats-loading"},[t._m(0)])],1):t._e()])},r=[function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"exactmetrics-roller"},[n("div"),n("div"),n("div"),n("div"),n("div"),n("div"),n("div")])}],s=(n("a4d3"),n("4de4"),n("4160"),n("e439"),n("dbb4"),n("b64b"),n("159b"),n("fc11")),a=(n("d3b7"),n("bc3a")),i=n.n(a),c=n("2b0e"),l=function(t){return new Promise((function(e){var n=new FormData,o=c["a"].prototype.$mi.page_id?c["a"].prototype.$mi.page_id:window.location.pathname;n.append("action","exactmetrics_pageinsights_refresh_report"),n.append("security",c["a"].prototype.$mi.nonce),n.append("report","pageinsights"),n.append("post_id",o),n.append("json",1),i.a.post(c["a"].prototype.$mi.ajax,n).then((function(t){e(t.data)})).catch((function(e){t.dispatch("$_app/block",!1,{root:!0}),e.response}))}))},u={fetchReportData:l},d=function(t){return new Promise((function(e){u.fetchReportData(t).then((function(n){"license_level"!==n.data.message?n.success?(t.commit("UPDATE_REPORT_DATA",{report:"pageinsights",data:n.data}),e(!0)):(c["a"].prototype.$mi_error_toast(!1,n.data.message,n.data.footer),e(!1)):e(!1)}))}))},f={getReportData:d},h=function(t){return t.date},p=function(t){return t.pageinsights},g=function(t){return t.loaded},b=function(t){return t.error},m=function(t){return t.noauth},v={date:h,pageinsights:p,loaded:g,error:b,noauth:m},_=function(t,e){e.report&&e.data&&t[e.report]&&c["a"].set(t,e.report,e.data)},w=function(t,e){e.start&&e.end&&(c["a"].set(t.date,"start",e.start),c["a"].set(t.date,"end",e.end))},x=function(t,e){c["a"].set(t.date,"interval",e)},y=function(t,e){t.loaded=e},O=function(t,e){t.error=e},j=function(t){t.noauth=!0,t.loaded=!0},D={UPDATE_REPORT_DATA:_,UPDATE_DATE:w,UPDATE_INTERVAL:x,UPDATE_LOADED:y,SET_ERROR:O,ENABLE_NOAUTH:j},C={loaded:!1,pageinsights:{},error:!1,noauth:!1},P={namespaced:!0,state:C,actions:f,getters:v,mutations:D},$=n("2f62"),E=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"exactmetrics-content-lite"},[n("frontend-stats-general")],1)},S=[],T=function(){var t=this,e=t.$createElement,n=t._self._c||e;return t.overview.infobox?n("div",{staticClass:"exactmetrics-frontend-stats-inner"},[n("h3",[n("span",{domProps:{textContent:t._s(t.text_insights_for)}}),n("span",{domProps:{textContent:t._s(t.text_your_website)}})]),n("div",{staticClass:"exactmetrics-frontend-stats-grey-area"},[n("frontend-stats-column",{attrs:{label:t.text_sessions,value:t.overview.infobox.sessions.value}},[n("frontend-infobox-change",{attrs:{change:t.infoboxSessionsData.change,color:t.infoboxSessionsData.color,direction:t.infoboxSessionsData.direction,days:t.infoboxRange}})],1),n("frontend-stats-column",{attrs:{label:t.text_pageviews,value:t.overview.infobox.pageviews.value}},[n("frontend-infobox-change",{attrs:{change:t.infoboxPageviewsData.change,color:t.infoboxPageviewsData.color,direction:t.infoboxPageviewsData.direction,days:t.infoboxRange}})],1),n("frontend-stats-column",{attrs:{label:t.text_session_duration,value:t.overview.infobox.duration.value}},[n("frontend-infobox-change",{attrs:{change:t.infoboxDurationData.change,color:t.infoboxDurationData.color,direction:t.infoboxDurationData.direction,days:t.infoboxRange}})],1),t.hasBounceData?n("frontend-stats-column",{attrs:{label:t.text_bounce_rate,value:t.overview.infobox.bounce.value}},[n("frontend-infobox-change",{attrs:{change:t.infoboxBounceData.change,color:t.infoboxBounceData.color,direction:t.infoboxBounceData.direction,days:t.infoboxRange}})],1):t._e(),!t.hasBounceData&&t.hasTotalUsersData?n("frontend-stats-column",{attrs:{label:t.text_total_users,value:t.infoboxTotalUsersData.value}},[n("frontend-infobox-change",{attrs:{change:t.infoboxTotalUsersData.change,color:t.infoboxTotalUsersData.color,direction:t.infoboxTotalUsersData.direction,days:t.infoboxRange}})],1):t._e(),n("frontend-stats-column",{staticClass:"exactmetrics-frontend-column-button"},[n("frontend-upsell")],1),n("frontend-stats-column",{staticClass:"exactmetrics-frontend-column-notifications"},[n("notifications-indicator")],1)],1)]):t._e()},A=[],R=(n("25f0"),function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{class:t.columnClass},[t.label?n("div",{staticClass:"exactmetrics-stats-label",domProps:{textContent:t._s(t.label)}}):t._e(),t.value?n("div",{staticClass:"exactmetrics-stats-value",domProps:{innerHTML:t._s(t.value)}}):t._e(),t._t("default")],2)}),U=[],M={name:"FrontendStatsColumn",props:{extraClass:{type:String,default:""},label:String,value:String},computed:{columnClass:function(){return"exactmetrics-stats-column "+this.extraClass}}},B=M,F=n("2877"),N=Object(F["a"])(B,R,U,!1,null,null,null),k=N.exports,V=n("561c"),I=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("a",{staticClass:"exactmetrics-button",attrs:{href:t.upgradeUrl},domProps:{textContent:t._s(t.text_button)}})},L=[],H={name:"FrontendUpsell",data:function(){return{text_button:Object(V["a"])("Upgrade to PRO","google-analytics-dashboard-for-wp")}},computed:{upgradeUrl:function(){return this.$getUpgradeUrl("frontend-reports","admin-bar")}}},W=H,z=Object(F["a"])(W,I,L,!1,null,null,null),K=z.exports,G=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"exactmetrics-frontend-infobox-change"},[n("div",{class:t.changeClass,domProps:{innerHTML:t._s(t.changeText)}}),t.days?n("div",{staticClass:"exactmetrics-reports-infobox-compare",domProps:{textContent:t._s(t.compare)}}):t._e()])},Z=[],Y=(n("a9e3"),{name:"FrontendInfoboxChange",props:{value:String,days:Number,change:Number,color:{default:"green",type:String},direction:{default:"up",type:String}},computed:{compare:function(){return Object(V["b"])("vs. Previous Day",Object(V["d"])("vs. Previous %s Days",this.days),this.days,"google-analytics-dashboard-for-wp")},changeClass:function(){var t="exactmetrics-reports-infobox-prev";return 0===this.change?t:t+" exactmetrics-"+this.color},changeText:function(){return this.change?""===this.direction?this.change+"%":'<span class="exactmetrics-arrow exactmetrics-'+this.direction+" exactmetrics-"+this.color+'"></span> '+this.change+"%":Object(V["a"])("No change","google-analytics-dashboard-for-wp")}}}),q=Y,J=Object(F["a"])(q,G,Z,!1,null,null,null),Q=J.exports,X=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"exactmetrics-notifications-indicator"},[n("a",{attrs:{href:t.sidebar_url,title:t.text_view_notifications}},[n("svg",{attrs:{width:"35",height:"39",viewBox:"0 0 35 39",fill:"none",xmlns:"http://www.w3.org/2000/svg"}},[n("path",{attrs:{d:"M17.6898 39C20.3431 39 22.4954 36.8177 22.4954 34.125H12.8841C12.8841 36.8177 15.0364 39 17.6898 39ZM33.8706 27.5963C32.4192 26.015 29.7035 23.6361 29.7035 15.8438C29.7035 9.9252 25.6108 5.18731 20.0922 4.02492V2.4375C20.0922 1.09154 19.0164 0 17.6898 0C16.3631 0 15.2873 1.09154 15.2873 2.4375V4.02492C9.76874 5.18731 5.67602 9.9252 5.67602 15.8438C5.67602 23.6361 2.96031 26.015 1.50893 27.5963C1.05818 28.0876 0.858356 28.6749 0.862112 29.25C0.870376 30.4992 1.83721 31.6875 3.27357 31.6875H32.1059C33.5423 31.6875 34.5099 30.4992 34.5174 29.25C34.5212 28.6749 34.3213 28.0869 33.8706 27.5963Z",fill:"#393F4C"}})]),t.has_notifications?n("span",{staticClass:"exactmetrics-notifications-unread",domProps:{textContent:t._s(t.notifications.length)}}):t._e()])])},tt=[];function et(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(t);e&&(o=o.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,o)}return n}function nt(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?et(Object(n),!0).forEach((function(e){Object(s["a"])(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):et(Object(n)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}var ot={name:"NotificationsIndicator",data:function(){return{text_view_notifications:Object(V["a"])("View notifications","google-analytics-dashboard-for-wp")}},computed:nt({},Object($["b"])({notifications:"$_notifications/notifications",view_url:"$_notifications/view_url",sidebar_url:"$_notifications/sidebar_url"}),{has_notifications:function(){return this.notifications&&this.notifications.length>0}})},rt=ot,st=Object(F["a"])(rt,X,tt,!1,null,null,null),at=st.exports;function it(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(t);e&&(o=o.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,o)}return n}function ct(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?it(Object(n),!0).forEach((function(e){Object(s["a"])(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):it(Object(n)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}var lt={name:"FrontendStatsGeneral",components:{NotificationsIndicator:at,FrontendInfoboxChange:Q,FrontendUpsell:K,FrontendStatsColumn:k},data:function(){return{text_insights_for:Object(V["a"])("Last 30 Days Analytics for ","google-analytics-dashboard-for-wp"),text_your_website:Object(V["a"])("Your Website","google-analytics-dashboard-for-wp"),text_sessions:Object(V["a"])("Sessions","google-analytics-dashboard-for-wp"),text_pageviews:Object(V["a"])("Pageviews","google-analytics-dashboard-for-wp"),text_session_duration:Object(V["a"])("Avg. Duration","google-analytics-dashboard-for-wp"),text_bounce_rate:Object(V["a"])("Bounce Rate","google-analytics-dashboard-for-wp"),text_total_users:Object(V["a"])("Total Users","google-analytics-dashboard-for-wp")}},computed:ct({},Object($["b"])({overview:"$_reports/overview"}),{text_upsell_title:function(){return this.$mi.is_admin?Object(V["a"])("More data is available","google-analytics-dashboard-for-wp"):Object(V["a"])("Want to see page-specific stats?","google-analytics-dashboard-for-wp")},infoboxRange:function(){return this.overview.infobox&&this.overview.infobox.range?this.overview.infobox.range:0},infoboxSessionsData:function(){return this.infoboxData("sessions")},infoboxPageviewsData:function(){return this.infoboxData("pageviews")},infoboxDurationData:function(){return this.infoboxData("duration")},hasBounceData:function(){var t=this.infoboxBounceData;return"undefined"!==typeof t.value},infoboxBounceData:function(){return this.infoboxData("bounce",!0)},hasTotalUsersData:function(){var t=this.infoboxTotalUsersData;return"undefined"!==typeof t.value},infoboxTotalUsersData:function(){return this.infoboxData("totalusers")}}),methods:{infoboxData:function(t){var e=arguments.length>1&&void 0!==arguments[1]&&arguments[1],n={};return this.overview.infobox&&this.overview.infobox[t]&&(n.change=this.overview.infobox[t]["prev"],n.value=this.overview.infobox[t]["value"].toString(),0===this.overview.infobox[t]["prev"]?n.direction="":this.overview.infobox[t]["prev"]>0?(n.direction="up",n.color="green"):(n.direction="down",n.color="red")),e&&("down"===n.direction?n.color="green":n.color="red"),n}},mounted:function(){var t=this;this.$mi.authed?this.$store.dispatch("$_reports/getReportData","overview").then((function(){t.$store.commit("$_frontend/UPDATE_LOADED",!0)})):this.$store.commit("$_frontend/ENABLE_NOAUTH")}},ut=lt,dt=Object(F["a"])(ut,T,A,!1,null,null,null),ft=dt.exports,ht={name:"FrontendStatsContent",components:{FrontendStatsGeneral:ft}},pt=ht,gt=Object(F["a"])(pt,E,S,!1,null,null,null),bt=gt.exports,mt=n("f284"),vt=n("d3fc"),_t=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"exactmetrics-not-authenticated-notice"},[n("h3",{domProps:{textContent:t._s(t.text_no_auth)}}),n("div",{staticClass:"exactmetrics-settings-input exactmetrics-settings-input-authenticate"},[n("p",{domProps:{textContent:t._s(t.text_auth_label)}}),n("div",[n("a",{staticClass:"exactmetrics-wp-button exactmetrics-wp-button-primary",attrs:{href:t.wizard_url},domProps:{textContent:t._s(t.text_wizard)}}),n("a",{staticClass:"exactmetrics-wp-button",attrs:{href:t.learn_link},domProps:{textContent:t._s(t.text_learn)}})])])])},wt=[],xt={name:"FrontendNoAuth",data:function(){return{text_no_auth:Object(V["a"])("Please Setup Website Analytics to See Audience Insights","google-analytics-dashboard-for-wp"),text_auth_label:Object(V["a"])("ExactMetrics, WordPress analytics plugin, helps you connect your website with Google Analytics, so you can see how people find and use your website. Over 3 million website owners use ExactMetrics to see the stats that matter and grow their business.","google-analytics-dashboard-for-wp"),text_wizard:Object(V["a"])("Connect ExactMetrics and Setup Website Analytics","google-analytics-dashboard-for-wp"),text_learn:Object(V["a"])("Learn More","google-analytics-dashboard-for-wp"),wizard_url:this.$mi.wizard_url,learn_link:this.$mi.getting_started_url}}},yt=xt,Ot=Object(F["a"])(yt,_t,wt,!1,null,null,null),jt=Ot.exports;function Dt(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(t);e&&(o=o.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,o)}return n}function Ct(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?Dt(Object(n),!0).forEach((function(e){Object(s["a"])(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):Dt(Object(n)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}var Pt={name:"ModuleFrontendReports",components:{FrontendNoAuth:jt,WidgetReportError:vt["a"],FrontendStatsContent:bt},data:function(){return{text_insights:"ExactMetrics",statsVisible:!1,page_id:this.$mi.page_id}},created:function(){var t="$_reports";t in this.$store._modules.root._children||this.$store.registerModule(t,mt["a"]);var e="$_frontend";e in this.$store._modules.root._children||this.$store.registerModule(e,P)},computed:Ct({},Object($["b"])({reportdata:"$_frontend/pageinsights",loaded:"$_frontend/loaded",error:"$_frontend/error",noauth:"$_frontend/noauth",notifications:"$_notifications/notifications"}),{toggleButtonClass:function(){var t="ab-item ab-empty-item exactmetrics-toggle";return this.statsVisible&&(t+=" exactmetrics-toggle-active"),t},has_notifications:function(){return this.notifications&&this.notifications.length>0}}),methods:{toggleStatsVisibility:function(){this.statsVisible=!this.statsVisible}}},$t=Pt,Et=Object(F["a"])($t,o,r,!1,null,null,null),St=Et.exports,Tt=n("7460"),At=n("6c6b"),Rt=(n("c618"),n("4360")),Ut={install:function(t,e){var n=e.store;t.prototype.$mi_loading_toast=function(){},t.prototype.$mi_error_toast=function(t){var e=t,o=e.type,r=void 0===o?"error":o,s=e.customContainerClass,a=void 0===s?"exactmetrics-swal":s,i=e.allowOutsideClick,c=void 0!==i&&i,l=e.allowEscapeKey,u=void 0!==l&&l,d=e.allowEnterKey,f=void 0!==d&&d,h=e.title,p=void 0===h?Object(V["a"])("Error","google-analytics-dashboard-for-wp"):h,g=e.html,b=void 0===g?Object(V["a"])("Please try again.","google-analytics-dashboard-for-wp"):g,m=e.footer,v=void 0!==m&&m;t={type:r,customContainerClass:a,allowOutsideClick:c,allowEscapeKey:u,allowEnterKey:f,title:p,html:b,footer:v},n.commit("$_frontend/SET_ERROR",{title:t.title,content:t.html,footer:t.footer})},t.prototype.$swal={close:function(){}}}},Mt=Ut;window.addEventListener("load",(function(){var t=document.getElementById("wp-admin-bar-exactmetrics_frontend_button");(c["a"].config.productionTip=!1,t)&&(Object(At["a"])({ctrl:!0}),c["a"].use(Tt["a"]),c["a"].use(Mt,{store:Rt["a"]}),Object(V["c"])(window.exactmetrics.translations,"google-analytics-dashboard-for-wp"),new c["a"]({store:Rt["a"],mounted:function(){Rt["a"].dispatch("$_notifications/getNotifications")},render:function(t){return t(St)}}).$mount(t))}))}});