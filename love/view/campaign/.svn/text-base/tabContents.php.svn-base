<div id='periods_campaign'>
        <table class="listPeriods"></table> 
        <div id="pagerCampaignPeriods"></div> 
    <div class="mainButtonsArea">
        <input type='button' id='addNewRow' class='periodButton' value='Add Campaign'/>
        <?php 
        /* #16402 temporary removal 
        <input type='button' id='checkoutCampaign' class='periodButton' value='Proceed to Checkout'/>
        */ ?>
        <input type='button' id='displayAllCampaigns' class='periodButton' value='Display All' title='Display all the campaigns of all the admin users'/>
        <input type='button' id='displayMyCampaigns' class='periodButton' value='Display My Campaigns' title='Display only my campaigns' style='display:none' />
        <span id="info_table_update"></span> 
    </div>
    
    <div id="dialogMembersOfPeriod" style="display:hidden;">
        <div id='members'>
            <table class="listMembers"></table> 
            <div class="pagerMembers"></div> 
        </div>   
    </div>
    <div class="radioArea">
    <!--- Next line hidden in #16521, could be come back later --->
        <span style="display:none;">Minimum amount $ <input type='text' value='0' id='simulationFloorInput'/></span>
        <input type='radio' name='simulation' class="simulation simulationLovesReceived"  />
        <span title='Prorata to the number of loves received in the period'>Prorata</span>
         (<a href="javascript:;" class='detailsProrata simulation' >details</a>)
        <input type='radio' name='simulation' class="simulation simulationLovesReceivedNormalized"  /> 
        <span title='Prorata to the number of loves received in the period'>Prorata Normalized</span>
         (<a href="javascript:;" class='detailsProrataNormalized simulation' >details</a>)
   <!--     <input type='radio' name='simulation' class="simulation simulationEqually"  />Equally distribute
        <span class="moreArea">
            ( <a href="javascript:;" class='moreOptionsLink simulation' >
            <span class="moreOptions options">more</span>
            <span class="hideOptions options">hide</span>
            options</a> ...)
        </span>
        -->
        <input type='button' class='set_published periodButton' value='Publish' />
        <div class="hiddenOptions" >
            <input type='radio' name='simulation'  class="simulation simulationTopPerc"/>Give to top <input type='text' value='10' id='simulationTopPercInput'/>%
            <input type='radio' name='simulation'  class="simulation simulationTopNum"/>Give to first <input type='text' value='5' id='simulationTopNumInput'/> persons
            <input type='radio' name='simulation' class="simulation simulationWinner"  />Winner(s) take all
            <input type='radio' name='simulation' class="simulation simulationCustom"  />Custom
            
        </div>
    </div>
    <div id="timeline-graph"></div>
    <input type='button' class='periodExport periodButton' value='Export CSV' />
    <div id='dialogCustom' style='display:none;'>
        <div id="repartition_graph"></div>
    </div>
</div>