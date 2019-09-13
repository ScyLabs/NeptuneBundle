if(webpActive === undefined){
    var webpActive = false;
}
var LazyLoad = (function(webpActive){

    var self = {};
    self.ImageObserver = null;
    self.lazyObjects = null;

    /*! modernizr 3.6.0 (Custom Build) | MIT *
     * https://modernizr.com/download/?-webp-setclasses !*/
    !function(e,n,A){function o(e,n){return typeof e===n}function t(){var e,n,A,t,a,i,l;for(var f in r)if(r.hasOwnProperty(f)){if(e=[],n=r[f],n.name&&(e.push(n.name.toLowerCase()),n.options&&n.options.aliases&&n.options.aliases.length))for(A=0;A<n.options.aliases.length;A++)e.push(n.options.aliases[A].toLowerCase());for(t=o(n.fn,"function")?n.fn():n.fn,a=0;a<e.length;a++)i=e[a],l=i.split("."),1===l.length?Modernizr[l[0]]=t:(!Modernizr[l[0]]||Modernizr[l[0]]instanceof Boolean||(Modernizr[l[0]]=new Boolean(Modernizr[l[0]])),Modernizr[l[0]][l[1]]=t),s.push((t?"":"no-")+l.join("-"))}}function a(e){var n=u.className,A=Modernizr._config.classPrefix||"";if(c&&(n=n.baseVal),Modernizr._config.enableJSClass){var o=new RegExp("(^|\\s)"+A+"no-js(\\s|$)");n=n.replace(o,"$1"+A+"js$2")}Modernizr._config.enableClasses&&(n+=" "+A+e.join(" "+A),c?u.className.baseVal=n:u.className=n)}function i(e,n){if("object"==typeof e)for(var A in e)f(e,A)&&i(A,e[A]);else{e=e.toLowerCase();var o=e.split("."),t=Modernizr[o[0]];if(2==o.length&&(t=t[o[1]]),"undefined"!=typeof t)return Modernizr;n="function"==typeof n?n():n,1==o.length?Modernizr[o[0]]=n:(!Modernizr[o[0]]||Modernizr[o[0]]instanceof Boolean||(Modernizr[o[0]]=new Boolean(Modernizr[o[0]])),Modernizr[o[0]][o[1]]=n),a([(n&&0!=n?"":"no-")+o.join("-")]),Modernizr._trigger(e,n)}return Modernizr}var s=[],r=[],l={_version:"3.6.0",_config:{classPrefix:"",enableClasses:!0,enableJSClass:!0,usePrefixes:!0},_q:[],on:function(e,n){var A=this;setTimeout(function(){n(A[e])},0)},addTest:function(e,n,A){r.push({name:e,fn:n,options:A})},addAsyncTest:function(e){r.push({name:null,fn:e})}},Modernizr=function(){};Modernizr.prototype=l,Modernizr=new Modernizr;var f,u=n.documentElement,c="svg"===u.nodeName.toLowerCase();!function(){var e={}.hasOwnProperty;f=o(e,"undefined")||o(e.call,"undefined")?function(e,n){return n in e&&o(e.constructor.prototype[n],"undefined")}:function(n,A){return e.call(n,A)}}(),l._l={},l.on=function(e,n){this._l[e]||(this._l[e]=[]),this._l[e].push(n),Modernizr.hasOwnProperty(e)&&setTimeout(function(){Modernizr._trigger(e,Modernizr[e])},0)},l._trigger=function(e,n){if(this._l[e]){var A=this._l[e];setTimeout(function(){var e,o;for(e=0;e<A.length;e++)(o=A[e])(n)},0),delete this._l[e]}},Modernizr._q.push(function(){l.addTest=i}),Modernizr.addAsyncTest(function(){function e(e,n,A){function o(n){var o=n&&"load"===n.type?1==t.width:!1,a="webp"===e;i(e,a&&o?new Boolean(o):o),A&&A(n)}var t=new Image;t.onerror=o,t.onload=o,t.src=n}var n=[{uri:"data:image/webp;base64,UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoBAAEAAwA0JaQAA3AA/vuUAAA=",name:"webp"},{uri:"data:image/webp;base64,UklGRkoAAABXRUJQVlA4WAoAAAAQAAAAAAAAAAAAQUxQSAwAAAABBxAR/Q9ERP8DAABWUDggGAAAADABAJ0BKgEAAQADADQlpAADcAD++/1QAA==",name:"webp.alpha"},{uri:"data:image/webp;base64,UklGRlIAAABXRUJQVlA4WAoAAAASAAAAAAAAAAAAQU5JTQYAAAD/////AABBTk1GJgAAAAAAAAAAAAAAAAAAAGQAAABWUDhMDQAAAC8AAAAQBxAREYiI/gcA",name:"webp.animation"},{uri:"data:image/webp;base64,UklGRh4AAABXRUJQVlA4TBEAAAAvAAAAAAfQ//73v/+BiOh/AAA=",name:"webp.lossless"}],A=n.shift();e(A.name,A.uri,function(A){if(A&&"load"===A.type)for(var o=0;o<n.length;o++)e(n[o].name,n[o].uri)})}),t(),a(s),delete l.addTest,delete l.addAsyncTest;for(var p=0;p<Modernizr._q.length;p++)Modernizr._q[p]();e.Modernizr=Modernizr}(window,document);


    var webP = null;


    Modernizr.on('webp', function(result) {
        webP = result;
    });

    self.load = function(DOMObject){
        self.lazyObjects = [].slice.call(DOMObject.querySelectorAll('.photo:not(.loaded),.lazy,video'));
        [].slice.call(DOMObject.querySelectorAll('video')).forEach(function(video){
            video.pause();
        });

        let active = true;
        if('IntersectionObserver' in window){
            self.ImageObserver = new IntersectionObserver(function(entries,observer){
                entries.forEach(function(entry){
                    let lazyObject = entry.target;

                    if(entry.isIntersecting){
                        if(typeof(lazyObject.play)  === 'function'){
                            if(lazyObject.paused)
                                playVideo(lazyObject);
                            return;
                        }
                        chargePhoto(lazyObject);
                        self.ImageObserver.unobserve(lazyObject);


                    }
                    else if (typeof(lazyObject.play) === 'function'){
                        if(!lazyObject.paused){
                            pauseVideo(lazyObject);
                        }
                    }
                })
            });
            self.lazyObjects.forEach(function(lazyObject){
                self.ImageObserver.observe(lazyObject);
            });

        }else{
            const lazyLoad = function () {
                if(active === false)
                    active = true;
                setTimeout(function () {
                    self.lazyObjects.forEach(function (lazyObject) {
                        if ((lazyObject.getBoundingClientRect().top <= window.innerHeight && lazyObject.getBoundingClientRect().bottom >= 0) && getComputedStyle(lazyObject).display !== "none") {

                            if(typeof(lazyObject.play) === 'function'){
                                if(lazyObject.paused)
                                    playVideo(lazyObject);
                                return;
                            }
                            chargePhoto(lazyObject);
                            self.lazyObjects = self.lazyObjects.filter(function (object) {
                                return object !== lazyObject;
                            });

                            if(self.lazyObjects.length === 0){
                                DOMObject.removeEventListener('scroll',lazyLoad);
                                window.removeEventListener('resize',lazyLoad);
                                window.removeEventListener('orientationchange',lazyLoad);
                            }
                        }
                        else if (typeof(lazyObject.play) === 'function' && !lazyObject.paused){
                            pauseVideo(lazyObject);
                        }
                    });


                },200)
            };
            setTimeout(function(){
                lazyLoad();
            },500);
            DOMObject.addEventListener("scroll", lazyLoad);
            window.addEventListener("resize", lazyLoad);
            window.addEventListener("orientationchange", lazyLoad);

        }
    };

    document.addEventListener('DOMContentLoaded',function(){
        self.load(document);
    });

    function playVideo(video){
        video.play();
    }
    function pauseVideo(video){
        video.pause();
    }


    function chargePhoto(photo){

        var id = photo.getAttribute('data-id');
        var number = new RegExp(/([0-9])+/);
        if(typeof(id) == 'undefined' || id === null || !number.test(id))
            id = 1;
        if(typeof(id) != 'undefined' && id !== null && number.test(id)){

            var w = parseInt(photo.offsetWidth);
            var h = parseInt(photo.offsetHeight);

            if(photo.classList.contains('paralax')){
                h = window.innerHeight;
            }
            var multiplicator =   (photo.getAttribute('data-multiplicator') !== null) ? parseInt(photo.getAttribute('data-multiplicator')) : null ;
            var photoName = photo.getAttribute('data-name');
            var monochrome = photo.getAttribute('data-monochrome');
            var truncate = photo.getAttribute('data-truncate');
            var url = ((typeof(root) != 'undefined') ? adminRoot : '/') +'photo-show/'+id+'/'+w;

            if(photoName === null)
                photoName = '0.jpeg';

            let splitted = photoName.split('.');
            let ext = splitted[splitted.length - 1];

            splitted[splitted.length - 1] = (ext == 'jpg' || ext == 'jpeg' && (webpActive && webP)) ? 'webp' : ext;
            photoName = splitted.join('.');

            if(h > 0  ||( multiplicator !== null  && number.test(multiplicator)) || truncate !== null || photoName !== null){
                url += '/'+h;
            }
            if(multiplicator !== null && number.test(multiplicator) || truncate !== null || photoName !== null){
                if(multiplicator === null){
                    multiplicator = 100;
                }
                url += '/'+multiplicator;
            }
            if(truncate !== null){
                url += "/1";
            }
            else if(photoName !== null || monochrome !== null){
                url += '/0';
            }
            var preg = new RegExp(/[a-zA-Z0-9]{6}-[a-fA-F0-9]{6}/);

            if(monochrome !== null && preg.test(monochrome)){

                url += '/' + monochrome;
            }

            if(photoName !== null){
                url += '/'+photoName;
            }
            var img = photo.getElementsByTagName('img');
            photo.classList.add('loaded');
            if(photo.classList.contains('paralax') || photo.classList.contains('background')){
                photo.style.backgroundImage = 'url("'+url+'")';
            }
            else if(img.length){
                img[0].src = url;
            }
        }
        else{
            var src = photo.getAttribute('data-src');
            photo.classList.remove('lazy');
            if(src === null) return;

            if(photo.classList.contains('paralax') || photo.classList.contains('background')){
                photo.style.backgroundImage = 'url("'+src+'")';
            }
            else{
                photo.src = src;
            }
        }

    }

    return self;

})(webpActive);