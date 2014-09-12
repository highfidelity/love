<?php ob_start();
//
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com
//

include_once("config.php");
require_once("class.session_handler.php");
include_once("helper/check_session.php");
include_once("functions.php");

/* Only super admins can access this page. */
if (!isSuperAdmin()) {
    header("location:login.php?redir=".urlencode($_SERVER['REQUEST_URI']));
    return;
}

/* Ajax requests must have HTTP referer set correctly. */
if (isset($_POST['ajax']) && !checkReferer()) die;

$con=mysql_connect(DB_SERVER,DB_USER,DB_PASSWORD);
mysql_select_db(DB_NAME,$con);

/************************************** Start Pagination ************************************/
$limit = 20;
$page = isset($_REQUEST["page"]) ? $_REQUEST["page"] : 1; //Get the page number to show, set default to 1
/********************************************************************************************/

if (isset($_POST['ajax']) && !empty($_POST['update']) && !empty($_POST['feature'])) {
    // AJAX request from ourselves to update company features
    $id = intval($_POST['update']);
    if ($id <= 0) { echo "fail"; die; }

    $rc = mysql_query("select features from ".COMPANY." where id='$id'",$con);
    if (!$rc) { echo "fail"; die; }
    $row = mysql_fetch_row($rc);
    $features = $row[0];

    if ($_POST['feature'] == "bulkinvite") $features ^= FEATURE_BULK_INVITE;
    if ($_POST['feature'] == "removeusers") $features ^= FEATURE_REMOVE_USERS;

    $rc = mysql_query("update ".COMPANY." set features='$features' where id='$id'",$con);

    echo $rc ? "ok" : "fail";
    die();
} else if(isset($_POST['ajax'])) {
    // AJAX request from ourselves to retrieve company list
    $query= "select count(*) from ".COMPANY;
    $sqlRs = mysql_query($query,$con);
    $row = mysql_fetch_row($sqlRs);
	$cPages = ceil($row[0]/$limit); 

    $query= "select id, name, features from ".COMPANY." order by name limit " . ($page-1)*$limit . ",$limit";
    $sqlRs = mysql_query($query,$con);

    // Construct json
    $companies = array(array($page, $cPages));
    while ($row = mysql_fetch_assoc($sqlRs)) {
        $companies[] = array($row['id'], $row['name'], $row['features']);
    }
                      
    $json = json_encode($companies);
    echo $json;     
    die();
} 
/*********************************** HTML layout begins here  *************************************/

include("head.html"); ?>

<!-- Add page-specific scripts and styles here, see head.html for global scripts and styles  -->

<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>jquery.autocomplete.js"></script>
<script type ="text/javascript">
    var page = <?php echo $page ?>;

    $(document).ready(function(){
	    GetCompanyList(<?php echo $page?>);    
    });

    function AppendPagination(page, cPages)
    {
        var pagination = '<tr bgcolor="#FFFFFF" class="row-company-live"><td colspan="5" style="text-align:center;">Pages : &nbsp;';
        if (page > 1) { 
            pagination += '<a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=' + (page-1) + '">Prev</a> &nbsp;'; 
        } 
        for (var i = 1; i <= cPages; i++) { 
            if (i == page) { 
                pagination += i + " &nbsp;"; 
            }else{ 
                pagination += '<a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=' + i + '" >' + i + '</a> &nbsp;'; 
            } 
        }
        if (page < cPages) { 
            pagination += '<a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=' + (page+1) + '">Next</a> &nbsp;'; 
        } 
        pagination += '</td></tr>';
        $('.table-companies').append(pagination);
    }

    function AppendRow(json, odd)
    {
        var row;
    
        row = '<tr class="row-company-live ';
        if (odd) { row += 'rowodd' } else { row += 'roweven' }
        row += '">';
        row += '<td>' + json[1] + '</td>';
        row += '<td><input type="checkbox" name="bulkinvite" class="bulkinvite" value="'+json[0]+'" ';
        if ((json[2] & <?php echo FEATURE_BULK_INVITE ?>) != 0) row += 'checked="checked"';
        row += '/></td>';
        row += '<td><input type="checkbox" name="removeusers" class="removeusers" value="'+json[0]+'" ';
        if ((json[2] & <?php echo FEATURE_REMOVE_USERS ?>) != 0) row += 'checked="checked"';
        row += '/></td>';

        $('.table-companies tbody').append(row);
    }

    function GetCompanyList(page) {
        $.ajax({
            type: "POST",
            url: '<?php echo $_SERVER['PHP_SELF']?>',
            data: 'ajax=1&page='+page,
            dataType: 'json',
            success: function(json) {
                page = json[0][0]|0;
                var cPages = json[0][1]|0;

                if (!json[1]) return;

                /* Output the history rows. json elements: 0=>id, 1=from, 2=to, 3=for, 4=date */
                $('.row-company-live').remove();
                var odd = true;
                for (var i = 1; i < json.length; i++) {
                    AppendRow(json[i], odd);
                    odd = !odd;
                }
                AppendPagination(page, cPages);

                $('.table-companies a').click(function(e){
                    page = $(this).attr('href').match(/page=\d+/)[0].substr(5);
                    GetCompanyList(page);
                    e.stopPropagation();
                    return false;
                });
                $('.table-companies input[type="checkbox"]').click(function(e){
                    $.ajax({
                        type: "POST",
                        url: '<?php echo $_SERVER['PHP_SELF']?>',
                        data: 'ajax=1&update='+$(this).val()+'&feature='+$(this).attr('class')+'&page='+page,
                        success: function() {
                            GetCompanyList(page);
                        }
                    });
                    e.stopPropagation();
                    return false;
                });
            },
            error: function(xhdr, status, err) {
                $('.row-company-live').remove();
                $('.table-companies').append('<tr class="row-company-live"><td colspan="4">Error loading data: '+err+'</td></tr>');
            }
        });
    }
</script> 

<!-- jquery file is for LiveValidation -->
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>js/jquery.min.js"></script>


<title>SendLove | Manage Company Features</title>

</head>

<body>

<?php include("format.php"); ?>

<!-- ---------------------- BEGIN MAIN CONTENT HERE ---------------------- -->

           
            <br/>
            <br/>
            <br/>
            <h1>Manage Company Features</h1>

                <table width="100%" border="0" cellpadding="5" cellspacing="1" bgcolor="#88809A" class="table-companies">
                <thead>
                <tr class="table-hdng">
                    <td>Company</td>
                    <td>Bulk Invite</td>
                    <td>Bulk Invite</td>
                </tr>
                </thead>
                <tbody>
                </tbody>
                </table>

<!-- ---------------------- end MAIN CONTENT HERE ---------------------- -->
<?php include("footer.php"); ?>

