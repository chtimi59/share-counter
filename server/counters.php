<!-- COUNTERS PAGE -->

<?PHP 
print "<div class='responsive_margin' ng-controller='counters-ctrl' ng-init='";
print "'>";
?>

<div class="logospacer"></div>
<div class="counter_box">
<div class="logo"></div>
<div class="busy" ng-if="busy"><img src="css/spinner.gif" /></div>    
<div ng-if="currentTab=='LIST'"       ng-include="'client/template/counters_list.html'"></div>
<div ng-if="currentTab=='SETTINGS'"   ng-include="'client/template/counters_settings.html'"></div>
<div ng-if="currentTab=='COUNTER'"    ng-include="'client/template/counters_datas.html'"></div>
<div ng-if="currentTab=='WRITEHELP'"  ng-include="'client/template/counters_wkey.'+lang+'.html'"></div>
<div ng-if="currentTab=='READHELP'"   ng-include="'client/template/counters_rkey.'+lang+'.html'"></div>
</div>

<?PHP 
print "</div>";
