var element = document.createElement("script");
element.src = root+"js/lib/script-loader/dist/scriptLoader.min.js";
element.type = "text/javascript";
element.defer = "defer";
document.body.appendChild(element);
element.onload = function()
{

    loadScript(dependances);

}
