<!-- STATIC PAGE -->
<?PHP 

switch($action) {
	case ACTION_STATIC_SDK:
		print "<div ng-include=\"'client/template/sdk.'+lang+'.html'\"></div>";
		break;
	case ACTION_STATIC_TERMS:
		print "<div ng-include=\"'client/template/terms.'+lang+'.html'\"></div>";
		break;
	case ACTION_STATIC_CONTACT:
		print "<div ng-include=\"'client/template/contact.'+lang+'.html'\"></div>";
		break;		
	default;
		break;
}

?>
	