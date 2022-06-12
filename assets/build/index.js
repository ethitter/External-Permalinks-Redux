(()=>{"use strict";const e=window.wp.element,t=window.wp.compose,n=window.wp.data,r=window.wp.editPost,o=window.wp.primitives,l=(0,e.createElement)(o.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},(0,e.createElement)(o.Path,{d:"M15.6 7.2H14v1.5h1.6c2 0 3.7 1.7 3.7 3.7s-1.7 3.7-3.7 3.7H14v1.5h1.6c2.8 0 5.2-2.3 5.2-5.2 0-2.9-2.3-5.2-5.2-5.2zM4.7 12.4c0-2 1.7-3.7 3.7-3.7H10V7.2H8.4c-2.9 0-5.2 2.3-5.2 5.2 0 2.9 2.3 5.2 5.2 5.2H10v-1.5H8.4c-2 0-3.7-1.7-3.7-3.7zm4.6.9h5.3v-1.5H9.3v1.5z"})),a=window.wp.plugins,i=window.wp.components,s=window.wp.i18n,{metaKeys:{target:p,type:c},statusCodes:m}=externalPermalinksReduxConfig,w=(0,t.compose)([(0,n.withSelect)((e=>{const{getEditedPostAttribute:t}=e("core/editor"),n=t("meta");return{target:n[p],type:n[c]}})),(0,n.withDispatch)((e=>{const{editPost:t}=e("core/editor");return{setTarget:e=>{t({meta:{[p]:e.trim()}})},setType:e=>{t({meta:{[c]:parseInt(e,10)}})}}}))])((t=>{let{setTarget:n,setType:r,target:o,type:l}=t;return(0,e.createElement)(e.Fragment,null,(0,e.createElement)(i.TextControl,{label:(0,s.__)("Destination Address:","external-permalinks-redux"),help:(0,s.__)("To restore the original permalink, remove the link entered above.","external-permalinks-redux"),onChange:n,type:"url",value:o}),(0,e.createElement)(i.SelectControl,{label:(0,s.__)("Redirect Type:","external-permalinks-redux"),onChange:r,options:m,value:l}))})),{postTypes:d}=externalPermalinksReduxConfig,u="external-permalinks-redux",g=(0,t.compose)([(0,n.withSelect)((e=>{const{type:t}=e("core/editor").getCurrentPost();return{postType:t}}))])((t=>{let{postType:n}=t;return n?(0,e.createElement)(r.PluginDocumentSettingPanel,{name:u,title:d[n],className:u},(0,e.createElement)(w,null)):null}));(0,a.registerPlugin)(u,{icon:l,render:g})})();