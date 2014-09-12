<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="copyright" content="Copyright (c) 2010, LoveMachine Inc.  All Rights Reserved. http://www.lovemachineinc.com ">
    <title>LoveMachine</title>
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico">
    <link type="text/css" href="css/m_tofor.css" rel="Stylesheet" />
    <script type="text/javascript">
    var sl_el_wrapper = { };
    </script>
    <script src="helper/getemails.php?var=sl_el_wrapper&user=1"></script>
    <script src="js/m_autosuggest.js"></script>
    <script type="text/javascript">
        window.onload = function() {
            var f = document.getElementById("sendloveForm");
            new AutoSuggestControl(document.getElementById('to'), new Suggestions(sl_el_wrapper.emails), document.getElementById('suggestions'), document.getElementById('content'));
            f.setAttribute("autocomplete", "off");
            document.getElementById('sndLovePrvBtn').onclick = function() {
                document.getElementById('priv').value = 1;
            }

            f.onsubmit = function(e) {
                var x = document.getElementById("to");
                var for1 = document.getElementById("for");
                var priv = document.getElementById("priv");
                if(x.value.indexOf('(') !== false) {
                    var t = x.value.match(/\(([^\)]+)\)/);
                    if(t && t[1]) {
                        x.value = t[1];
                    }
                }
                return true;
            }
        }
    </script>
</head>
<body>
    <div id="content" style="clear: both;">
        <p id="markletWelcome"><span>Welcome <?php echo $front->getUser()->getNickname(); ?></span> | <a href="m_logout.php">logout</a></p>
        <div id="suggestions"></div>
        <div class="mainAction">
            <form id="sendloveForm" action="sendlove.php" method="post">
                <fieldset>
                    <!-- this is the value which will be set if a private Love is required -->
                    <input type="hidden" id="priv" name="priv" value="0" />
                    <input type="hidden" name="marklet" value="1" />
                    <p>
                        <!-- this is the receiver of Love -->
                        <label id="labelto" for="to">To</label>
                        <input type="text" id="to" name="to" value="" />
                    </p>
                    <div id="message"><?php echo ($message != '') ? $message : '&nbsp;'; ?></div>
                    <p>
                    <!-- this is the reason for Love message -->
                        <label id="labelfor" for="for">For</label>
                        <input type="text" id="for" name="for1" value="" />
                    </p>
                    <div id="submitButtons">
                        <!-- this button sends the Love message -->
                        <input type="submit" id="sndLoveBtn" value="SendLove" />
                        <!-- this button sends the Love message Quietly -->
                        <input type="submit" id="sndLovePrvBtn" value="SendLove Quietly" alt="Only you and the receiver will be able to see your message" title="Only you and the receiver will be able to see your message" />  
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</body>
</html>