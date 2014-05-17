<?php

    // BP2S SSO Management
    //$httpSSOAuthRoles = $_SESSION['HTTP_SSO_AUTH_ROLES'];


    $SID = session_id();
    if (empty($SID)) session_start();

    $isValid = strpos($_SESSION['sso_roles'], 'USER'); // Check SSO Role's validity
    if ( $isValid === false ) $_SESSION['sso_roles'] = "";

    if ( !isset($_SESSION['sso_roles']) || empty($_SESSION['sso_roles']) ) {
      if ( isset($_SERVER['HTTP_SSO_AUTH_ROLES']) ) {
        $isValid = strpos($_SERVER['HTTP_SSO_AUTH_ROLES'], 'USER'); // Check SSO Role's validity
        if ($isValid !== false) {
          $httpSSOAuthRoles = $_SERVER['HTTP_SSO_AUTH_ROLES'];
          $_SESSION['sso_roles'] = $httpSSOAuthRoles;
        }
      }
    }
    else { // check for Profile changement
       if ( isset($_SERVER['HTTP_SSO_AUTH_ROLES']) ) {
         $isValid = strpos($_SERVER['HTTP_SSO_AUTH_ROLES'], 'USER'); // Check SSO Role's validity
         if ( ($isValid !== false) && ($_SESSION['sso_roles'] != $_SERVER['HTTP_SSO_AUTH_ROLES']) ) {
          $httpSSOAuthRoles = $_SERVER['HTTP_SSO_AUTH_ROLES'];
          $_SESSION['sso_roles'] = $httpSSOAuthRoles;

          $strSSOFirstName = $_SERVER['HTTP_SSO_AUTH_FIRSTNAME'];
          setcookie('firstname', $strSSOFirstName, time()+3600);
          $strSSOLastName = $_SERVER['HTTP_SSO_AUTH_LASTNAME'];
          setcookie('lastname', $strSSOLastName, time()+3600);
         }
       }
    }


    // Bug workaround
    if ( !isset($_SESSION['sso_roles']) || empty($_SESSION['sso_roles']) || ($_SESSION['sso_roles'] == "") ) {
      $_SESSION['sso_roles'] = "USERADV";
    }

    $strSSORoles = $_SESSION['sso_roles'];

    if ( !isset($_COOKIE['firstname']) ) {
      if ( isset($_SERVER['HTTP_SSO_AUTH_FIRSTNAME']) ) {
        $strSSOFirstName = $_SERVER['HTTP_SSO_AUTH_FIRSTNAME'];
        setcookie('firstname', $strSSOFirstName, time()+3600);
      }
    }

    if ( !isset($_COOKIE['lastname']) ) {
      if ( isset($_SERVER['HTTP_SSO_AUTH_LASTNAME']) ) {
        $strSSOLastName = $_SERVER['HTTP_SSO_AUTH_LASTNAME'];
        setcookie('lastname', $strSSOLastName, time()+3600);
      }
    }

    if ( isset($_COOKIE['firstname']) ) {
      $strSSOFirstName = $_COOKIE['firstname'];
      setcookie('firstname', $strSSOFirstName, time()+3600);
    }

    if ( isset($_COOKIE['lastname']) ) {
      $strSSOLastName = $_COOKIE['lastname'];
      setcookie('lastname', $strSSOLastName, time()+3600);
    }

    // User Preferences
    if ( !isset($default_archive_server) )
      if ( isset($_COOKIE['arcserver']) )
        $default_archive_server=$_COOKIE['arcserver'];

    if ( !isset($default_archive_db) )
      if ( isset($_COOKIE['arcdb']) )
        $default_archive_db=$_COOKIE['arcdb'];

    if ( !isset($default_monitored_server) )
      if ( isset($_COOKIE['monserver']) )
        $default_monitored_server=$_COOKIE['monserver'];

?>
