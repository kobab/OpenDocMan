<?php
// Report all PHP errors (bitwise 63 may be used in PHP 3)
// includes
include('config.php');

if(!isset($_POST['login']) && $GLOBALS['CONFIG']['authen'] =='mysql')
{
    ?>
<!--

        index.php - displays a login form

        -->

        <html>
        <head>
        <TITLE><?php echo $GLOBALS['CONFIG']['title']; ?></TITLE>
        <basefont face="Verdana">
        </head>

        <body bgcolor="White">

        <table cellspacing="0" cellpadding="0">
        <tr>
        <td align="left"><img src="images/logo.gif" alt="Site Logo" border=0></td>
        </tr>
        </table>

        <table border="0" cellspacing="5" cellpadding="5">
        <tr>
        <td valign="top">
        <center>
        <table border="0" cellspacing="5" cellpadding="5">
        <form action="index.php" method="post">
        <tr>
        <td>Username</td>
        <td><input type="Text" name="frmuser" size="15"></td>
        </tr>
        <tr>
        <td>Password</td>
        <td><input type="password" name="frmpass" size="15"></td>
        </tr>
        <tr>
        <td colspan="2" align="CENTER"><input type="Submit" name="login" value="Enter"></td>
        </tr>
        </form>
        </table>
        </center>
        </td>
        <td valign="top">
        Welcome to OpenDocMan.
        <p>
        Log in to begin using The system's powerful storage, publishing and revision control features.
        </td>
        <td width="20%">
        &nbsp;
    </td>
        </tr>
        </table>

        </center>

<?php
        draw_footer();
}
elseif(isset($_POST['login']))
{

$hostname=$GLOBALS['hostname'];
$user=$GLOBALS['user'];
$pass=$GLOBALS['pass'];
$database=$GLOBALS['database'];

$frmuser=$_POST['frmuser'];
$frmpass=$_POST['frmpass'];

    // check login and password
    // connect and execute query
    $connection = mysql_connect($hostname, $user, $pass) or die ("Unable to connect!");
    $query = "SELECT id, username, password from user WHERE username = '$frmuser' AND password = PASSWORD('$frmpass')";
    $result = mysql_db_query($database, $query, $connection) or die ("Error in query: $query. " . mysql_error());
    // if row exists - login/pass is correct
    if (mysql_num_rows($result) == 1)
    {
        // initiate a session
        session_start();
        // register the user's ID
        session_register('SESSION_UID');
        list($id, $username, $password) = mysql_fetch_row($result);
        global $SESSION_UID;
        $SESSION_UID = $id;
        // redirect to main page
        header('Location:out.php');
        mysql_free_result ($result);	
        // close connection
        mysql_close($connection);		
    }
    else
        // login/pass check failed
    {
        mysql_free_result ($result);	
        mysql_close($connection);
        // redirect to error page
        header('Location: error.php?ec=0');
    }

}
elseif($GLOBALS['CONFIG']['authen'] =='kerbauth')
{

    // check login and password
    // connect and execute query
    if (!isset($_COOKIE['AuthUser']))
    {
        header('Location: https://secureweb.ucdavis.edu:443/cgi-auth/sendback?'.$GLOBALS['CONFIG']['base_url']);
    }
    else
    {
        list ($userid, $id2, $id3) = split ('[-]', $_COOKIE['AuthUser']);
        //// query to get id num from username
        $connection = mysql_connect($hostname, $user, $pass) or die ('Unable to connect!');
        $query = "SELECT id FROM user WHERE username='$userid'";
        $result = mysql_db_query($database, $query, $connection) or die ('Error in query: '.$query . mysql_error());
        // if row exists then the user has an account
        if (mysql_num_rows($result) == 1)
        {
            // initiate a session
            session_start();
            // register the user's ID
            session_register('SESSION_UID');
            list($id) = mysql_fetch_row($result);
            $SESSION_UID = $id;
            // redirect to main page
            header('Location:out.php');
            mysql_free_result ($result);	
            // close connection
            mysql_close($connection);
        }
        // User passed auth, but does not have an account
        else 
        {
             header('Location:error.php?ec=19');
        }
    }
}
else
{
        echo 'Check your config';
}

?>
