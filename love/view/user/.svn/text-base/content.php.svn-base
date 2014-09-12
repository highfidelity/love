<div id="content" class="full-round">
    <div id="contentBody">
        <div id="basics">
        <img id="userPic" src ="<?php echo $userInfo->getPhoto(225, 225); ?>" alt = "User Profile Picture"/>
            <div id="userData">
                <div id ="nickname"><?php echo $userInfo->getNickname(); ?></div>
                <?php echo $userInfo->getUsername(); ?>
                <div id ="averages">
                    Average sent in last seven days: <?php echo $userInfo->getWeekAvgSent(); ?><br />
                    Company average: <?php echo $front->getAverageCompanyLoveSent(); ?>
                </div>
                <div id ="totals">
                    Total Love sent all time: <?php echo $userInfo->getTotalSent(); ?><br />
                    Total Love received all time: <?php echo $userInfo->getTotalReceived(); ?><br />
                    Total Unique senders: <?php echo $userInfo->getUniqueSenders(); ?>
                </div>
            </div>
        </div>
        <div style="clear:both; height: 30px; border-bottom: 1px solid #ccc;"></div>
        <div id="lowerInfo">
            <div id="lowerInfoHolder" class="info">
                <div class="love userTab">
                    <div class="baloon">
                        <div class="toolBar">
                            <ul class="balToolbarul">
                                <li class="loveCloud" title="The Love Cloud">Love Cloud</li>
                                <li class="list" title="The Love List">Love List</li>
                                <li class="trendChart" title="Where was the love?">Trend Chart</li>
                                <li class="loveConnections" title="Love Connections">Connections</li>
                            </ul>
                        </div>
                        <div class="contents">
                              <?php
                                // settings for usertabs
                                $current_username = $userInfo->getUsername();
                                $justUser = true;

                                //include('view/tofor/loveCloudDiv.php');
                                include('view/tofor/listDiv.php');
                                //include('view/tofor/trendChartDiv.php');
                                //include('view/tofor/loveConnectionsDiv.php');
                               ?>
                            <!-- Temporary placeholder div for unimplemented features -->
                            <div class="loveCloudDiv loveConnectionsDiv trendChartDiv"
                                 style="text-align: center; font-size: x-large;">
                                <span>Coming Soon!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
