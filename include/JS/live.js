function LiveUpdater(pLiveID, pLastUpdate, pType){
    this.mLiveID = pLiveID;
    this.mLastUpdate = pLastUpdate;
    this.mType = pType;
    this.mLastStatus = 1;
    
    var live = this;
    this.prototype = new AJAXRequestObject("live.php", "POST", "LiveID=" + this.mLiveID + "&LastUpdate=" + this.mLastUpdate + "&DataType=" + this.mType);
    
    this.start = start;
    
    function start(){
        switch(live.mType){
            case "xml":
                live.prototype.request(onCompleteXML)
                break;
        }
    }
    function onCompleteXML(status, responseText, responseXML){
        if(responseXML != undefined){
            var node = responseXML.getElementsByTagName("Update")[0];
            live.mLastStatus = node.attributes['status'].value;
            live.mLastUpdate = node.attributes['timestamp'].value;
            if(live.mLastStatus == 1 || live.mLastStatus == 0){
                 var main = responseXML.getElementsByTagName("MainScore")[0];
                 document.getElementById("score_table_home_mainscore").innerHTML = main.childNodes[0].firstChild.nodeValue;
                 document.getElementById("score_table_away_mainscore").innerHTML = main.childNodes[1].firstChild.nodeValue;

                 var subscore = responseXML.getElementsByTagName("Subscores")[0];
                 var subscoresarea = document.getElementById("score_table_subscores");

                 subscoresarea.innerHTML = null;
                 for(var i = 0; i < subscore.childNodes.length; ++i){
                     var tr = document.createElement("tr");
                     var homess = document.createElement("td");
                     homess.setAttribute("class", "score_table_subscore");
                     homess.appendChild(document.createTextNode(subscore.childNodes[i].firstChild.firstChild.nodeValue));
                     tr.appendChild(homess);
                     var name = document.createElement("td");
                     name.setAttribute("class", "score_table_subscorenames");
                     name.appendChild(document.createTextNode(subscore.childNodes[i].getAttribute("name")));
                     tr.appendChild(name);
                     var awayss = document.createElement("td");
                     awayss.setAttribute("class", "score_table_subscore");
                     awayss.appendChild(document.createTextNode(subscore.childNodes[i].lastChild.firstChild.nodeValue));
                     tr.appendChild(awayss);
                     subscoresarea.appendChild(tr);
                 }
                 var messages = responseXML.getElementsByTagName("Messages")[0];
                 var first = document.getElementById("live_msg_area").childNodes[0];
                 for(i = 0; i < messages.childNodes.length; ++i){
                     var li = document.createElement("li");
                     li.setAttribute("class", "live_messages");
                     var head = document.createElement("span");
                     head.setAttribute("class", "live_messages_head");
                     head.appendChild(document.createTextNode(messages.childNodes[i].attributes['title'].value));
                     li.appendChild(head);

                     var text = document.createElement("span");
                     text.setAttribute("class", "live_messages_text");
                     text.appendChild(document.createTextNode(messages.childNodes[i].firstChild.nodeValue))
                     li.appendChild(text);
                     if(document.getElementById("live_msg_area").hasChildNodes()){
                         messageAnimation(first.parentNode.insertBefore(li, first));
                     }
                     else{
                         messageAnimation(document.getElementById("live_msg_area").appendChild(li));
                     }
                     document.getElementById("live_notify").play();
                     first = li;
                 }
            }
            document.getElementById("live_notify").play();
            if(live.mLastStatus == 1){
                live.prototype.mPOSTParams = "LiveID=" + live.mLiveID + "&LastUpdate=" + live.mLastUpdate + "&DataType=" + live.mType
                live.start();
            }
        }
        else{
            window.setTimeout(live.start, 500);
        } 
        
    }
    
    function messageAnimation(pElement){
        var steps = 10;
        var op = pElement.style.opacity = 0;
        for(var i = 0; i < steps; ++i){
            window.setTimeout(function(){
                op += 0.1;
                pElement.style.opacity = op;
            }, i * 120);
        }
    }
}