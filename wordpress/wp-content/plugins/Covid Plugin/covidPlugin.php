<?php
   /*
   Plugin Name: Sora's Covid Forum Plugin
   Plugin URI: http://sorasweb.net
   description: A plugin for a Covid related forum page
   Version: 1.3
   Author: Sora
   Author URI: http://sorasweb.net
   License: GPL2
   */

    function changeName() {//When this function is called, we will use the session ID as well as the name inserted by the user to give them a new username.
    	$uniqueID = substr(session_id(), 0, 5);//The identifier given to a user is based on the first five digits of their session ID, this is tacked onto the end of the name they select to provide them a higher likelihood of a unique title without commting to the site.
    	if($_POST['name']!= ""){//So long as a name actually was inserted, we will continue(However, the javascript side of this equation is also not allowed to make it this far with a blank space, so this is moreso a failsafe in case I somehow missed some hole somewhere.)
	    $name = sanitize_text_field($_POST['name']);//Sanitize our name so that any strange injection attempts are that much harder to succeeed in.
        $_SESSION['name'] = $name . "-" . $uniqueID;//Piece our new unique title together.
    	}
    	$response = $_SESSION['name'];//Spit the new name back out so Javascript can confirm the new name and the completion of the process.
    	echo $response;
    	die();
    }

    add_action('wp_ajax_changeName', 'changeName');
    add_action('wp_ajax_nopriv_changeName', 'changeName');

    function openSession() {//When we open the site, we give all users an ID by starting a session for them to track their messages and whatnot.
        if(!session_id()){
            session_start();
			session_write_close();
        }
    }

    add_action('init', 'openSession');

    function enqueueScript() {//Set up our script to function.
        wp_register_script( "AJAX Script", WP_PLUGIN_URL . '/Covid Plugin/Ajax.js', array('jquery') );
        wp_localize_script( 'AJAX Script', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        
     
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'AJAX Script' );
    }

    add_action('init', 'enqueueScript');

    function AJAXlistForum() {//List every posted forum here, only called through Javascript
        $servername = "localhost";
    	$username = "websorac_sorabot";
    	$password = "sorabot12";
    	$dbname = "websorac_CovidDB";

    	$conn = new mysqli($servername, $username, $password, $dbname);
        $sql = "SELECT * FROM `forums`";

        if(isset($_POST['filtercov'])){
            $sql .= "WHERE `subject` = " . $_POST['filtercov'];
            $Wh = true;
        }

        if(isset($_POST['filterexp'])){
            if(!$Wh){
                $sql .= "WHERE `experience` = " . $_POST['filterexp'];
            }

            else{
                $sql .= "AND `experience` = " . $_POST['filterexp'];
            }
        }

        $sql .= "ORDER BY `id` DESC";

        $result = $conn -> query($sql);
		$query = "";

        if (mysqli_num_rows($result) > 0) {//Parse data into proper format
            while($row = mysqli_fetch_assoc($result)) {
				if($row['exp'] == "Good"){
					$exp = "Positive";
				}

				else{
					$exp = "Negative";
				}

                $query .= "<div class='forumpost' value=" . $row['id'] . "><p>[" . $row['date'] . "] " . $row['name'] . ": " . $row['PostTitle'] . "</p><p><div id='semihandler'><div class='SemiCrucial'><strong>Subject:</strong> " . $row['filtercov'] . "</div class='SemiCrucial'> <div><strong>Experience:</strong> " . $exp . "</div></div></p></div>";
            }
        }
		echo $query;
		mysqli_close($conn);
		die();
    }

    add_action('wp_ajax_getForumPosts', 'AJAXlistForum');
    add_action('wp_ajax_nopriv_getForumPosts', 'AJAXlistForum');

    function listForumsandChat() {//Sets up the page to function as it should, immediately calls a non-AJAX variant of listForum.
        $servername = "localhost";
    	$username = "websorac_sorabot";
    	$password = "sorabot12";
    	$dbname = "websorac_CovidDB";

    	$conn = new mysqli($servername, $username, $password, $dbname);
        //filtercov determines if it is covid related or vaccine related, filterexp determines whether it was a good experience or a bad experience.
        $sql = "SELECT * FROM `forums`";//We could implement a sorting feature, but that would then require changes to the AJAX variant and also time that I do not currently need to spend on the site given the number of submissions currently live.
        $sql .= "ORDER BY `id` DESC";

        $result = $conn -> query($sql);

		$query = "";//Initializing a variable to parse the results of the query so we can nestle it within the page.

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                if($row['exp'] == "Good"){
					$exp = "Positive";
				}

				else{
					$exp = "Negative";
                }
                $query .= "<div class='forumpost' value=" . $row['id'] . "><p>[" . $row['date'] . "] " . $row['name'] . ": " . $row['PostTitle'] . "</p><p><div id='semihandler'><div class='SemiCrucial'><strong>Subject:</strong> " . $row['filtercov'] . "</div class='SemiCrucial'> <div><strong>Experience:</strong> " . $exp . "</div></div></p></div>";
            }
        }

		mysqli_close($conn);//We have no more need for the database, so we close our connection here.
        echo '<div id="namechanger"><p>Would you like to change your name?</p><form action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '" method="post">
                <input id="forminput" type="text" id="name" maxlength="15">
                <input id="formbutton" type="button" value="Submit">
            </form></div>
			<div id="ForumField">' . $query . '</div><form method="post" action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '"><input type="button" id="Multifunction" value="Post"></form>
            <div id="MessageBox"></div>
            <form method="post" class="myforms" action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '">
            <input id="Message" type="text" name="Message" autocomplete="off" /> <input id="ButtonSend" name="button" type="button" id="button" value="Send">
            </form>';//Message box stays empty, it will be populated via a future function.
    }

    function FandCShortcode() {//Set up shortcode so we can get this on the page.
        ob_start();
        listForumsandChat();
		return ob_get_clean();
    }

    add_shortcode('forum', 'FandCShortcode');

    function postForums() {//Here, we upload our forum to the database. Local variable names are not consistent with other function's local variable names with similar or identical functionality, but I don't really feel like changing it.
        $servername = "localhost";
    	$username = "websorac_sorabot";
    	$password = "sorabot12";
    	$dbname = "websorac_CovidDB";

    	$conn = new mysqli($servername, $username, $password, $dbname);

        $Subject = $_POST['Subject'];
        $Experience = $_POST['Experience'];
		$PT = $_POST['PostTitle'];

        if($Experience == "Bad") {
            $negreas = $_POST['NegReason'];
        }

        else {
            $negreas = "";
        }

        $Description = $_POST['Description'];
        $sql = "SELECT * FROM `forums`";
        $result = $conn -> query($sql);
        $x = mysqli_num_rows($result);
		if($_SESSION['name'] && $_SESSION['name'] != "" && $_SESSION['name'] != NULL){//Checks to see if we can identify with a name
			$name = $_SESSION['name'];
		}
		else {//If not, use their Session ID
			$name = session_id();
		}

        $sql = "INSERT INTO `forums` (`id`, `name`, `PostTitle`, `date`, `description`, `filtercov`, `exp`, `reason`) VALUES ('" . $x . "', '" . $name . "', '" . $PT . "', '" . date('F j, Y') . "', '" . $Description . "', '" . $Subject . "', '" . $Experience . "', '" . $negreas . "')";
        
        $result = $conn -> query($sql);
		mysqli_close($conn);
    }

    add_action('wp_ajax_postToForums', 'postForums');
    add_action('wp_ajax_nopriv_postToForums', 'postForums');

    function getChat() {//Parse chat into chat box
        $servername = "localhost";
    	$username = "websorac_sorabot";
    	$password = "sorabot12";
    	$dbname = "websorac_CovidDB";

    	$conn = new mysqli($servername, $username, $password, $dbname);
        $sql = "SELECT * FROM `chat` ORDER BY `id` DESC";

        $result = $conn -> query($sql);
		$output = "";

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $output .= "<div class='ChatMsg'><p>[" . $row['time'] . "] " . $row['name'] . ": " . $row['description'] . "</p></div>";
            }
        }

        echo $output;//Get parsed daata back to javascript so it can update the chatbox.
		mysqli_close($conn);
        die();
    }

    add_action('wp_ajax_getChat', 'getChat');
    add_action('wp_ajax_nopriv_getChat', 'getChat');

    function postChat() {//Post to Chatbox
		if($_POST['Message'] != ""){
        $servername = "localhost";
    	$username = "websorac_sorabot";
    	$password = "sorabot12";
    	$dbname = "websorac_CovidDB";

    	$conn = new mysqli($servername, $username, $password, $dbname);

		$sql = "SELECT * FROM `chat`";
        $result = $conn -> query($sql);
        $x = mysqli_num_rows($result);
			
        $Description = $_POST['Message'];
        if($_SESSION['name'] && $_SESSION['name'] != "" && $_SESSION['name'] != NULL){
			$name = $_SESSION['name'];
		}
		else{
			$name = session_id();
		}
        $sql = "INSERT INTO `chat` (`id`, `name`, `time`, `description`) VALUES ('" . $x . "', '" . $name . "', '" . date('h:i:s a') . "', '" . $Description . "')";

        $result = $conn -> query($sql);
		mysqli_close($conn);
		}
    }

    add_action('wp_ajax_postChat', 'postChat');
    add_action('wp_ajax_nopriv_postChat', 'postChat');

    function forumForm() {//The form used to create a forum post
	    echo '<form method="post" action="' . esc_url( $_SERVER['REQUEST_URI'] ) . '">
		    <input type="text" placeholder="Post Title" maxlength="40" id="PostTitle">  
		    <div class="labeled">
		        <label for="subject">What is the main subject for this post?</label>
	            <select name="subject" id="subject">
		            <option value="Virus">Virus</option>
                    <option value="Pfizer">Pfizer Vaccine</option>
	                <option value="Moderna">Moderna Vaccine</option>
	                <option value="J&J">Johnson & Johnson Vaccine</option>
	            </select>
	        </div>
	        <div class="labeled">
	            <label for="Exp">Was your experience positive or negative?</label>
		        <select name="Exp" id="Exp">
		            <option value="Good">Positive</option>
                    <option value="Bad">Negative</option>
	            </select>
	        </div>
	        <div class="labeled">
	            <label for="Reason">If your experience was negative, what was the primary cause for it?</label>
		        <select name="Reason" id="Reason">
		            <option value="Unspecified">Not specified(N/A)</option>
		            <option value="Subject">The subject of the post</option>
    	            <option value="Circumstance">Circumstances surrounding the situation</option>
		        </select>
		    </div>
	        <textarea wrap="hard" id="description" placeholder="Describe your experience." autocomplete="off"></textarea>
	    </form>';
		die();
    }

    add_action('wp_ajax_ForumForm', 'ForumForm');
    add_action('wp_ajax_nopriv_ForumForm', 'ForumForm');

    function openForum(){//Open a forum post
        $servername = "localhost";
    	$username = "websorac_sorabot";
    	$password = "sorabot12";
    	$dbname = "websorac_CovidDB";

    	$conn = new mysqli($servername, $username, $password, $dbname);

        $forumno = $_POST['id'];
        $sql = "SELECT * FROM `forums` WHERE `id` = " . $forumno;//Find them based on their ID
        $result = $conn -> query($sql);

        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {

				if($row['exp'] == "Good"){
					$exp = "Positive";
				}

				else{
					$exp = "Negative";
				}
                $output = "<div class='forumfull'><h1>" . $row['name'] . "</h1><h2> " . $row['date'] . "</h2><h3><div>Subject: " . $row['filtercov'] . "</div><div>Experience: " . $exp . "</div></h3></br><p>" . $row['description'] . "</p></br></div>";
            }
        }
        echo $output;
		mysqli_close($conn);//As this subject has proven to be divisive, we will not be including direct comments as that would require somebody to moderate the site to ensure they don't get too heated over someone's differing experience with a subject(black listing words does not help much tbh)
        die();
    }

    add_action('wp_ajax_openForum', 'openForum');
    add_action('wp_ajax_nopriv_openForum', 'openForum');
?>