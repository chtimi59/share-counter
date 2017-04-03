<?PHP
include 'header.php';
include 'server/PHPLog/log.php';
?>
<!DOCTYPE html>
<html>

<head>  
    <title>Share-Counter</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta charset="utf-8"> 
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Share-Counter style -->
    <link rel="stylesheet" href="css/colors.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/dialog.css">
    <link rel="stylesheet" href="css/login.css"> 
    <link rel="stylesheet" href="css/fullpage.css">
	<link rel="stylesheet" href="css/counters.css">

        
    <!-- jQuery -->
    <script src="bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Angular -->
    <!--
        Angular 1.3+ do not work for this project
        In the 1.3+ version, a transluded scope is a child of the directive's isolate scope
        rather than a child of the controller scope !
    -->    
    <script src= "bower_components/angular/angular.min.js"></script>
	<!-- ZeroClipBoard -->
    <script src= "bower_components/zeroclipboard/dist/ZeroClipboard.min.js"></script>
	<!-- col-resizable -->
    <script src= "bower_components/col-resizable/colResizable-1.5.min.js"></script>
	
    <!-- Languages -->
    <script> var S=[]; </script>
    <script src= "lang/fr.js"></script>
    <script src= "lang/en.js"></script>
    
    <!-- Various helper -->
    <script src= "client/helper.js"></script>
    
    <!-- MVC -->
    <script src= "client/app.js"></script>
    <script src= "client/filters/to_trusted.js"></script>
    
    <script src= "client/controllers/main-ctrl.js"></script>
    <script src= "client/controllers/log-ctrl.js"></script>
    <script src= "client/controllers/log-ctrl-login.js"></script>
    <script src= "client/controllers/log-ctrl-forget.js"></script>
    <script src= "client/controllers/log-ctrl-register.js"></script>
    <script src= "client/controllers/log-ctrl-settings.js"></script>
    <script src= "client/controllers/gallery-ctrl.js"></script>
    <script src= "client/controllers/counters-ctrl.js"></script>
    <script src= "client/controllers/counters-ctrl-settings.js"></script>
	<script src= "client/controllers/counters-ctrl-datas.js"></script>
    <script src= "client/controllers/valid_form_counter.js"></script>
    
    <script src= "client/directives/scd-dialog.js"></script>
    <script src= "client/directives/scd-action.js"></script>
    <script src= "client/directives/scd-center.js"></script>
    <script src= "client/directives/scd-focus.js"></script>
    
    <script src= "client/services/dialog.js"></script>
    
</head>


<?php 

/* overwrite cookies by user preferences */
print "<!-- overwrite cookies by user preferences -->\n";
print "<script>\n";
if ($USER)  print "setCookie('lang','".$USER['LANG']."');";
print "\n</script>\n\n";

/* feed main body controller */
print "<body class='bgblue' ng-app='myApp' ng-controller='main-ctrl' ng-init='";

    /* previous url */
    print 'action="'.$action.'";';
    
    /* previous url */
    print 'previousUrl="'.(isset($_GET['pre'])?$_GET['pre']:'index.php').'";';
    
    /* Absolute base Url (uses for web service) */
    print 'baseUrl="'.CurrentPageBaseUrl().'";';

    /* current url argument, i.e: '&pre="current_url"' */
    print 'preArg="'.preArg().'";';
    
    /* set if a user is logged */
    if (!$USER) {
        print "user=null;";
    } else {
        $row = array();
        $row['name']=$USER['NAME'];
        $row['email']=$USER['EMAIL'];
        $row['max_record']=$USER['MAX_RECORD'];
        print "user=".json_encode($row).';';
    }    
    
    /* Am I in a private aera? (logging needed) */
    do {
        if ($USER && $action>=ACTION_CLASS_LOG && $action<ACTION_CLASS_LOG+ACTION_CLASSRANGE) { 
            print "isPrivatePage=true;";
            break;
        }    
        if ($USER && $action>=ACTION_CLASS_COUNTER && $action<ACTION_CLASS_COUNTER+ACTION_CLASSRANGE) { 
            print "isPrivatePage=true;";
            break;
        }        
        if ($USER && $action>=ACTION_CLASS_RECORD && $action<ACTION_CLASS_RECORD+ACTION_CLASSRANGE) { 
            print "isPrivatePage=true;";
            break;
        }
        if ($action>=ACTION_CLASS_STATICPAGE && $action<ACTION_CLASS_STATICPAGE+ACTION_CLASSRANGE) { 
            print "isPrivatePage=false;";
            break;
        }
        /* else gallery */
        print "isPrivatePage=".(($USER)?"true":"false").";";
    } while(0);
    
print "'>\n";
?>

    <!-- HEADER -->
    <header class="headerheight">
    <div class="responsive_margin">

        <nav class="navbar desktoponly">
            <ul class="nav navbar-nav navbar-right optional">
                    <li><a scd-action="<?PHP echo ACTION_NONE ?>"><i class="fa fa-home"></i> {{S['MENU_HOME']}}</a></li>
                    <li><a scd-action="<?PHP echo ACTION_STATIC_SDK ?>"><i class="fa fa-code"></i> {{S['MENU_SDK']}}</a></li>
                    <li ng-if="user" class="dropdown">
                        <a href="#" data-toggle="dropdown"><span class="glyphicon glyphicon-user"></span> {{user.name}} <b class="caret"></b></a>
                        <ul class="submenu dropdown-menu">
                            <li><a scd-action="<?PHP echo ACTION_COUNTER_LIST ?>">{{S['MENU_LISTCOUNTER']}}</a></li>
                            <li><a scd-action="<?PHP echo ACTION_LOGIN_SETTING ?>">{{S['MENU_SETTINGS']}}</a></li>
                            <li ng-if="lang=='en'"><a href="#" ng-click="setLan('fr')">{{S['MENU_LANGEN']}}</a></li></a></li>
                            <li ng-if="lang=='fr'"><a href="#" ng-click="setLan('en')">{{S['MENU_LANGFR']}}</a></li></a></li>
                            <li class="divider"></li>
                            <li><a href="#" ng-click="logout()">{{S['MENU_LOGOUT']}}</a></li>
                        </ul>
                    </li>
                    <li ng-if="!user"><a scd-action="<?PHP echo ACTION_LOGIN ?>"><span class="glyphicon glyphicon-log-in"></span> {{S['MENU_LOGIN']}}</a></li>
                    <li ng-if="!user && lang=='en'"><a href="#" ng-click="setLan('fr')">{{S['MENU_LANGEN']}}</a></li></a></li>
                    <li ng-if="!user && lang=='fr'"><a href="#" ng-click="setLan('en')">{{S['MENU_LANGFR']}}</a></li></a></li>
            </ul>
        </nav>
        
        <nav class="navbar mobileonly mobileonly">    
            <div class="hamburger dropdown">
            <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><i class="fa fa-bars"></i></button>
                <ul class="dropdown-menu">
                    <li ng-if="!user">      <a scd-action="<?PHP echo ACTION_LOGIN ?>"><span class="glyphicon glyphicon-log-in"></span> {{S['MENU_LOGIN']}}</a></li>
                    <li ng-if="!user" class="divider"></li>
                    <li ng-if="true">       <a scd-action="<?PHP echo ACTION_NONE ?>"><i class="fa fa-home"></i> {{S['MENU_HOME']}}</a></li>
                    <li ng-if="true">       <a scd-action="<?PHP echo ACTION_STATIC_SDK ?>"><i class="fa fa-code"></i> {{S['MENU_SDK']}}</a></li>                    
                    <li ng-if="user" class="divider"></li>
                    <li ng-if="user">       <a scd-action="<?PHP echo ACTION_COUNTER_LIST ?>"><i class="fa fa-list"></i> {{S['MENU_LISTCOUNTER']}}</a></li>
                    <li ng-if="user">       <a scd-action="<?PHP echo ACTION_LOGIN_SETTING ?>"><span class="glyphicon glyphicon-user"></span> {{S['MENU_SETTINGS']}}</a></li>
                    <li ng-if="lang=='en'"> <a href="#" ng-click="setLan('fr')"><i class="fa fa-globe"></i> {{S['MENU_LANGEN']}}</a></li></a></li>
                    <li ng-if="lang=='fr'"> <a href="#" ng-click="setLan('en')"><i class="fa fa-globe"></i> {{S['MENU_LANGFR']}}</a></li></a></li>
                    <li ng-if="user" class="divider"></li>
                    <li ng-if="user">       <a href="#" ng-click="logout()"><span class="glyphicon glyphicon-log-in"></span> {{S['MENU_LOGOUT']}}</a></li>
                </ul>
            </div>
        </nav>
        
    </div>
    </header>
        
    <!-- CONTENT -->
    <!-- 
    previous: {{ previousUrl }}<br>
    <p ng-if="isPrivatePage">private</p>
    <p ng-if="!isPrivatePage">public</p>
    -->
    <?php 
        do {
            if ($action>=ACTION_CLASS_LOG && $action<ACTION_CLASS_LOG+ACTION_CLASSRANGE) { 
                include "server/log.php";
                break;
            }    
            if ($USER && $action>=ACTION_CLASS_COUNTER && $action<ACTION_CLASS_COUNTER+ACTION_CLASSRANGE) { 
                include "server/counters.php";
                break;
            }        
            if ($USER && $action>=ACTION_CLASS_RECORD && $action<ACTION_CLASS_RECORD+ACTION_CLASSRANGE) { 
                include "server/records.php";
                break;
            }        
            if ($action>=ACTION_CLASS_STATICPAGE && $action<ACTION_CLASS_STATICPAGE+ACTION_CLASSRANGE) { 
                include "server/static.php";
                break;
            }                    
            include "server/gallery.php";
        } while(0);
    ?>
    
    <!-- DIALOG TEMPLATE -->
    <div ng-include="'client/template/dialog.html'"></div>
        
    <!-- POPUP  -->
    <div class="popup">
        <div class="responsive_margin">
            <div id="popupError" ng-click="hideError()" class="alert alert-danger">
                <strong>Error:</strong> {{popupError}}
            </div>
            <div id="popupInfo" ng-click="hideInfo()" class="alert alert-success">
                <strong>Info:</strong> {{popupInfo}}
            </div>
        </div>
    </div>
    
    <!-- FOOTER -->
    <div class="footerheight"></div> 
    <footer class="footerheight">
    <div class="responsive_margin">
        <div class="footer">
        Share-Counter<br>
        <a scd-action="<?PHP echo ACTION_STATIC_TERMS ?>">{{S['FOOTER_TERMS']}}</a>
        <a scd-action="<?PHP echo ACTION_STATIC_CONTACT ?>">{{S['FOOTER_CONTACT']}}</a><br>
        07/2017
        </div>
    </div>
    </footer>
    
    

</body>
</html>
<?PHP include 'footer.php';?>