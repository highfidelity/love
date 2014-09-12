<?php  
//  Copyright (c) 2009, LoveMachine Inc.
//  All Rights Reserved. 
//  http://www.lovemachineinc.com

/*********************************** HTML layout begins here  *************************************/

include("head.html"); ?>

<!-- Add page-specific scripts and styles here, see head.html for global scripts and styles  -->

<!-- jquery file is for LiveValidation -->
<script type="text/javascript" src="<?php echo CONTRIB_URL; ?>js/jquery.min.js"></script>


<title>SendLove | Testing Template</title>

</head>

<body>

<?php include("format.php"); ?>

<!-- ---------------------- BEGIN MAIN CONTENT HERE ---------------------- -->
       	
            <h1>Heading 1 - h1</h1>
            <h2>Heading 2 - h2</h2>
            <h3>Heading 3 - h3</h3>
            <h4>Heading 4 - h4</h4>
            <h5>Heading 5 - h5</h5>
            <h6>Heading 6 - h6</h6>

        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce commodo adipiscing odio, et varius ligula hendrerit sed. Maecenas molestie purus tincidunt elit tempus dapibus. In massa dui, congue vitae viverra eget, dignissim vitae leo. Suspendisse porttitor tellus eget nisl vulputate sed condimentum neque sollicitudin. Praesent vitae enim est, ac facilisis orci. Nunc pharetra dui dignissim est cursus quis fringilla turpis viverra. Integer ultrices, turpis sed fermentum interdum, nibh risus luctus justo, quis aliquam nisl.</p>
                <ul>
                <li>line item 1</li>
                <li>line item 2</li>
                </ul>
                
        <p>In-line hyperlinks <a href="#" target="_blank">look like this</a></p>
        
           
<!-- ---------------------- end MAIN CONTENT HERE ---------------------- -->
<?php include("footer.php"); ?>
