
function toggleVis(el) {
    
    var element = document.getElementById(el)
    if (element.style.display == 'none') {
        element.style.display = '';
    } else {
        element.style.display = 'none';
    }
}

function toggleCBGroup(classname, check) {
    //toggle all checkboxes with classname
    var checklist = document.getElementsByTagName("input");
    for (i = 0; i < checklist.length; i++) {
        if ( (checklist[i].getAttribute("type") == 'checkbox') && (checklist[i].className == classname) ) {
           //if (checklist[i].checked) {   
	    if (!check.checked) {   
                checklist[i].checked = false;
            } else {
                checklist[i].checked = true;
            }
        } 
    }
    
}

function toggleCBs(option) {
    //toggle all checkboxes
    var checklist = document.getElementsByTagName("input");
    for (i = 0; i < checklist.length; i++) {
    if ( checklist[i].getAttribute("type") == 'checkbox' ) {
        if (option=='toggle') {
            if (checklist[i].checked) {   
                checklist[i].checked = false;
            } else {
                checklist[i].checked = true;
            }
        } 
        if (option=='select') {
            checklist[i].checked = true;
        }
        if (option=='unselect') {
            checklist[i].checked = false;
        }
    }   
    }
    
}

function toggleBox(box) {
    cbox = document.getElementById(box);
    if (cbox.checked) {   
        cbox.checked = false;
    } else {
        cbox.checked = true;
    }
    
}

function grantAll() {
    var points = $('#grant-all-text').val();
    $(".points").val(points)
}

/**
 * toggleEligible(user_id, type);
 * user_id int
 * type varchar [receiver|giver]
 */

function toggleEligible(type, imgbtn) {
    var user_id = $(imgbtn).attr('rel');
        $.ajax({
                url: 'toggle-eligibility.php',
                cache: false,
                data: {
                        'user_id': user_id,
                        type: type
                },
                success: function(d) {
                        if (d == '1') {
                                $(imgbtn).find('img').attr('src', 'images/yes.png')
                        } else {
                                $(imgbtn).find('img').attr('src', 'images/no.png')
                        }
                },
                dataType: 'text'
        });
}



