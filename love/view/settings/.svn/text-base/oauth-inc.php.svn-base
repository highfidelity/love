<?php
//  Copyright (c) 2009-2010, LoveMachine Inc.
//  All Rights Reserved.
//  http://www.lovemachineinc.com

// If not configured, don't provide a broken option
if (!defined('LINKEDIN_API_KEY_PUBLIC') || !defined('LINKEDIN_API_KEY_PRIVATE'))  { return; }

require_once($app_root_path.'/oauth/linkedinoauth.php');

//We got this far, show the logo
echo '<img id="linklogo" src="images/linkedin.jpg">';

$request_token = $front->getUser()->getRequest_token();
$request_token_secret = $front->getUser()->getRequest_token_secret();
$access_token = $front->getUser()->getAccess_token();
$access_token_secret = $front->getUser()->getAccess_token_secret();

if (isset($_REQUEST['oauth_token']) && (empty($access_token))) {
        //error_log('$_REQUEST: '.print_r($_REQUEST, true));

        $urlaccesstoken = preg_replace("/[^a-zA-Z0-9\_\-]/","",$_REQUEST['oauth_token']);
        $urlaccessverifier = preg_replace("/[^a-zA-Z0-9\_\-]/","",$_REQUEST['oauth_verifier']);
        
        //error_log("Found access tokens in the URL - $urlaccesstoken, $urlaccessverifier");
        //error_log("Creating API with $request_token, $request_token_secret");			

        $to = new LinkedInOAuth(
            LINKEDIN_API_KEY_PUBLIC, 
            LINKEDIN_API_KEY_PRIVATE,
            $request_token,
            $request_token_secret
        );
        
        $tok = $to->getAccessToken($urlaccessverifier);
        
        $access_token = $tok['oauth_token'];
        $access_token_secret = $tok['oauth_token_secret'];
        
        $front->getUser()->updateAccessTokens($access_token, $access_token_secret);

        //error_log("Calculated access tokens $access_token, $access_token_secret");			
}

if (empty($access_token)) {
    //error_log("Creating request");

    $to = new LinkedInOAuth(LINKEDIN_API_KEY_PUBLIC, LINKEDIN_API_KEY_PRIVATE);
    
    $maxretrycount = 1;
    $retrycount = 0;
    while ($retrycount<$maxretrycount)
    {		
        $tok = $to->getRequestToken(SERVER_URL. "settings.php");
        if (isset($tok['oauth_token'])&&
            isset($tok['oauth_token_secret']))
            break;
        
        $retrycount += 1;
        sleep($retrycount*5);
    }
    
    $request_token = $tok['oauth_token'];
    $request_token_secret = $tok['oauth_token_secret'];

    $front->getUser()->updateRequestTokens($request_token, $request_token_secret);
	}

    if(!empty($access_token) && !empty($access_token_secret)){
        $to = new LinkedInOAuth(
            LINKEDIN_API_KEY_PUBLIC,
            LINKEDIN_API_KEY_PRIVATE,
            $access_token,
            $access_token_secret
        );
        
        $profile_result = $to->oAuthRequest('http://api.linkedin.com/v1/people/~:(public-profile-url,three-current-positions:(title,company:(name)))');
        $profile_data = simplexml_load_string($profile_result);

        if(strpos($profile_data->message,'unauthorized') > 0) {
            $front->getUser()->deleteTokens();
            $request_token = null;
            $request_token_secret = null;
            $access_token = null;
            $access_token_secret = null;
        } else {
            echo "<div id='content_linkedin'>";
            echo "<a href='".$profile_data->{'public-profile-url'}."'>".$profile_data->{'public-profile-url'}."</a><br>";
            echo "<ul>";
            foreach($profile_data->{'three-current-positions'}->position as $position){
                echo "<li>".$position->title." at ".$position->company->name."</li>";
            }
            echo "</ul>";
            
            echo "<input type='checkbox' style='margin-top: 5px;'";
            echo $front->getUser()->getLinkedin_share() == '1' ? 'checked="checked"' : '';
            echo " id='linkedin_status' name='linkedin_status' value='Y' />";
            echo "Send my weekly love stats to my LinkedIn status. ex: 2 love from 2 people.";
            echo "</div>";
        }
    }
    
?>

    <?php
        $requestlink = "";
        if(empty($access_token) && !empty($request_token)){
            $to = new LinkedInOAuth(LINKEDIN_API_KEY_PUBLIC, LINKEDIN_API_KEY_PRIVATE);
            $requestlink = $to->getAuthorizeURL($request_token);
        }
    ?>
    <div id="sync_linkedin" class="settingsbutton <?php echo ((!empty($access_token)) ? 'hide"' : '"') ?> onclick="<?php echo "location.href='$requestlink';";?>" >Link profile</div>
    
    <div id="revoke_linkedin" class="settingsbutton <?php echo ((!empty($access_token)) ? '"' : 'hide"') ?>>Revoke</div>

