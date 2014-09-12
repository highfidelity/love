<?php
// js generator template file
// see love/generator.php for more details
include_once("config.php");

//untaint inputs that could alter scripts, databases or urls
$sandbox = isset($_GET['sandbox']) ? preg_replace("/[^a-zA-Z0-9]/","",$_REQUEST['sandbox']) : '';

?>
var sl_el_wrapper = {
}
sl_el_wrapper.go = function() {
        var x = document.getElementById('sl_el_BMDIV');
        if(x==null){
            var c = document.createElement('style');
            c.type = 'text/css';
            c.id = 'sl_el_BMCSS';
            document.getElementsByTagName('head')[0].appendChild(c);
            this.addStyle(c, '#sl_el_BMDIV { font-family: sans-serif !important; font-size:10px !important; width:440px; height:230px; padding:10px; position: fixed !important; position:absolute; top:20px; left:50%; margin-left:-220px; background: #FFF !important; color:#000 !important; z-index:5000000;} #sl_el_BMDIV iframe { margin: 0; padding:0; width:440px; height:95%; border:0;} #sl_el_BMDIV a { color:#000 } #sl_el_BMX { margin:0; padding:0 } #sl_el_BMCLOSE { float:right; }\
            #sl_el_BMDIV { border:1px solid #EFEFEF; -webkit-box-shadow: 0px 0px 15px rgba(0,0,0,0.6); -moz-box-shadow: 0px 0px 15px rgba(0,0,0,0.6); box-shadow: 0px 0px 15px rgba(0,0,0,0.6); -webkit-border-radius: 5px; -moz-border-radius: 5px; border-radius: 5px; }');
            var x = sl_el_wrapper.x = document.createElement('div');
            x.innerHTML = '<p id="sl_el_BMX"><a id="sl_el_BMCLOSE" href="javascript:sl_el_wrapper.go();">close</a></p>';
            document.body.appendChild(x);
            x.id = 'sl_el_BMDIV';
            sl_el_wrapper.w = document.getElementById('sl_el_BMWELCOME');
            sl_el_wrapper.addiFrame();
            // sl_el_wrapper.loadUser();
            return(false);
        } else {
            x.parentNode.removeChild(x);
            var c = document.getElementById('sl_el_BMCSS');
            if(c != null) c.parentNode.removeChild(c);
        }
    }
sl_el_wrapper.loadUser = function() {
        if(sl_el_wrapper.em) {
            sl_el_wrapper.em.parentNode.removeChild(sl_el_wrapper.em);
        }
        var em = sl_el_wrapper.em = document.createElement('script');
        em.id='sl_el_BMEM';
        em.type='text/javascript';
        em.onreadystatechange= function () {
            if (this.readyState == 'loaded') sl_el_wrapper.jsload();
        }
        em.onload = sl_el_wrapper.jsload;
        em.src='<?php echo SERVER_URL; ?>helper/getemails.php?var=sl_el_wrapper&user=1&userOnly=1';
        document.body.appendChild(em);
    }
sl_el_wrapper.jsload = function() {
    if(sl_el_wrapper.user) {
        document.getElementById("sl_el_SPUSER").innerHTML = sl_el_wrapper.user;
        sl_el_wrapper.w.style.display = '';
    } else {
        sl_el_wrapper.logout();
    }
}
sl_el_wrapper.addiFrame = function() {
        if(sl_el_wrapper.i) {
            sl_el_wrapper.i.parentNode.removeChild(sl_el_wrapper.i);
        }
        var i = sl_el_wrapper.i = document.createElement('iframe');
        i.name = i.id = "sl_el_BMIFRAME";
        i.src = '<?php echo SERVER_URL; ?>m_tofor.php';
        i.frameBorder="0";
        sl_el_wrapper.x.appendChild(i);
    }
sl_el_wrapper.getDoc = function(iframe) {
        var iframeDoc;
        if (iframe.contentDocument) {
            iframeDoc = iframe.contentDocument;
        }
        else if (iframe.contentWindow) {
            iframeDoc = iframe.contentWindow.document;
        }
        else if (window.frames[iframe.name]) {
            iframeDoc = window.frames[iframe.name].document;
        }
        return iframeDoc
    }
sl_el_wrapper.addStyle = function(style, cssStr) {
        if(style.styleSheet){// IE
            style.styleSheet.cssText = cssStr;
        } else {// w3c
            var cssText = document.createTextNode(cssStr);
            style.innerHTML = cssStr;
        }
    }
sl_el_wrapper.logout = function() {
    sl_el_wrapper.x.style.height = 285 + 'px';
    sl_el_wrapper.w.style.display = 'none';
    if(sl_el_wrapper.id) {
        var f = sl_el_wrapper.id.getElementById("sendloveForm");
        f.style.display = 'none';
    }
    if (sl_el_wrapper.i) {
        sl_el_wrapper.i.src = '<?php echo SERVER_URL; ?>m_logout.php';
    }
    sl_el_wrapper.emails = false;
    sl_el_wrapper.user = false;
    sl_el_wrapper.id = false;
}


sl_el_wrapper.go();
