<!-- LOG PAGE -->
<?PHP 
print "<div class='responsive_margin' ng-controller='log-ctrl' ng-init='";

	// VERIF ?
	if (isset($_GET['verif'])) {
        print 'getTmpUserData("'.$_GET['verif'].'"); ';
	} else {
		switch($action) {
			case ACTION_LOGIN_SETTING:
				print 'currentTab="SETTINGS"; ';
				break;
			case ACTION_LOGIN:
			default:
				print 'currentTab="LOGIN"; ';
				break;			
		}
	}
	
print "'>";
?>
<div scd-center="{{height[currentTab]}}" class="login_box">
<div class="logo"></div>
<div ng-if="!busy && currentTab=='LOGIN'"     ng-include="'client/template/login.html'"></div>
<div ng-if="!busy && currentTab=='FORGET'"    ng-include="'client/template/login_forget.html'"></div>
<div ng-if="!busy && currentTab=='REGISTER'"  ng-include="'client/template/login_register.html'"></div>
<div ng-if="!busy && currentTab=='SETTINGS'"  ng-include="'client/template/login_settings.html'"></div>
<div ng-if="busy"><img src="css/spinner.gif" /></div>
</div>

<?PHP 
print "</div>";


