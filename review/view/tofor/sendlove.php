<div id="sendLove">
    <form id="sendloveForm" action="#" method="post">
        <fieldset>
            <!-- this is the value which will be set if a private Love is required -->
            <input type="hidden" id="priv" name="priv" value="0" />
            
            <!-- this is the receiver of Love -->
            <label id="labelto" for="to">TO</label>
            <input type="text" id="to" name="to" value="" />
            
            <!-- this is the reason for Love message -->
            <label id="labelfor" for="for">FOR</label>
            <input type="text" id="for" name="for" value="" />
            
            <div id="submitButtons">
                <!-- this button sends the Love message -->
                <input type="submit" id="sndLoveBtn" value="SendLove" />
                
                <!-- this button sends the Love message Quietly -->
                <input type="submit" id="sndLovePrvBtn" value="SendLove Quietly" />  
            </div>
        </fieldset>
    </form>
</div>