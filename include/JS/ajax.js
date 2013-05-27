function getXMLHTTPRequestObject(){
    try{
        return new XMLHttpRequest();
    }
    catch(e){
        try{
            return new ActiveXObject("Microsoft.XMLHTTP");
        }
        catch(e){
            try{
                return new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch(e){
                return false;
            }
        }
    }
}
function AJAXGETRequest(url, onComplete, onEnd){
    var ajax = getXMLHTTPRequestObject();
    if(ajax){
        ajax.open("GET", url);
        ajax.onreadystatechange = function(){
            if(ajax.readyState == 4){
                if(ajax.status == 200){
                    responseXML = ajax.responseXML;
                    responseText = ajax.responseText;
                    onComplete(responseXML, responseText);
                }
                delete ajax;
                onEnd();
            }
        }
        ajax.send(null);
    }
}
function AJAXPOSTRequest(url, onComplete, onEnd, params){
    var ajax = getXMLHTTPRequestObject();
    if(ajax){
        ajax.open("POST", url);
        ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        ajax.onreadystatechange = function(){
            if(ajax.readyState == 4){
                if(ajax.status == 200){
                    responseXML = ajax.responseXML;
                    responseText = ajax.responseText;
                    onComplete(responseXML, responseText);
                }
                delete ajax;
                onEnd();
            }
        }
        ajax.send(params);
    }
}
function AJAXRequestObject(pUrl, pMethod, pPOSTParams){
    if(pUrl == undefined || pUrl == "") throw new Exception("Url is empty!");
    this.mUrl = pUrl;
    this.mMethod = pMethod != undefined ? pMethod.toUpperCase() : "GET";
    if (this.mMethod != "GET" && this.mMethod != "POST") throw new Exception("Invalid method. Only GET and POST is allowed.")
    this.mPOSTParams = pPOSTParams;
    
    this.mInProgess = false;
    this.mAjaxObject = null;
    
    var instance = this;
    
    AJAXRequestObject.prototype.abort = abort;
    AJAXRequestObject.prototype.request = request;
    
    function abort(){
        if(instance.mInProgess){
            instance.mAjaxObject.abort();
            instance.mAjaxObject = null;
            instance.mInProgess = false;
        }
    }
    function request(onComplete, onStatechange, onEnd){
        try{
            instance.mAjaxObject = new XMLHttpRequest();
        }
        catch(e){
            try{
                instance.mAjaxObject = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch(e){
                try{
                    instance.mAjaxObject = new ActiveXObject("Msxml2.XMLHTTP");
                }
                catch(e){
                    throw new Exception("XmlHttpRequest is not supported!");
                }
            }
        }
        instance.mAjaxObject.onreadystatechange = function(){
            if(onStatechange){
                onStatechange(instance.mAjaxObject.readyState);
            }
            if(instance.mAjaxObject.readyState === 4){
                var status = instance.mAjaxObject.status;
                var responseText = instance.mAjaxObject.responseText;
                var responseXML = instance.mAjaxObject.responseXML;
                instance.mAjaxObject = null;
                instance.mInProgess = false;
                onComplete(status, responseText, responseXML);
            }
        }
        instance.mAjaxObject.open(instance.mMethod, instance.mUrl, true);
        if (instance.mMethod == "POST") {
            instance.mAjaxObject.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            instance.mAjaxObject.setRequestHeader("Content-Length", instance.mPOSTParams.length);
            
        }
        instance.mInProgess = true;
        instance.mAjaxObject.send(instance.mMethod === "POST" ? instance.mPOSTParams : null);
    }
}