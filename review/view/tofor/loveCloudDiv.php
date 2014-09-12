<div id="loveCloudDiv">
    <!-- -- Word Cloud Here -- -->
    <div id="cloudWrapper">
        <div class="cloud-div" id="cloudDiv">
            <img width="900" height="400" src="clouds/lovecloud-<?php echo($front->getCompany()->getId()); ?>-950x400.png" id="cloudImg" usemap="#Cloud">
            <map name="Cloud" id="cloudMap"><?php echo $front->getCloud()->getCloudMap(); ?></map>
        </div>
    </div>
    
    <!-- -- Live Feed Here -- -->
    <div id="feedWrapper" style="overflow:hidden">
                    <div class="feed-div"></div>
    </div>
</div>