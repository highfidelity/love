<div class="mainAction">
    <form id="sendloveForm" action="#" method="post">
        <fieldset>
            <!-- this is the value which will be set if a private Love is required -->
            <input type="hidden" id="priv" name="priv" value="0" />
            
            <p>
            <!-- this is the receiver of Love -->
            <label id="labelto" for="to">To</label>
            <input type="text" id="to" name="to" value="" />
            </p>

            <p>
            <!-- this is the reason for Love message -->
            <label id="labelfor" for="for">For</label>
            <input type="text" id="for" name="for" value="" />
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