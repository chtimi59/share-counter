// rfc4122 version 4 compliant
function guid() {
    var nbr, randStr = "";
    do {
        randStr += (nbr = Math.random()).toString(16).substr(2);
    } while (randStr.length < 30);
    return [
        randStr.substr(0, 8), "-",
        randStr.substr(8, 4), "-4",
        randStr.substr(12, 3), "-",
        ((nbr*4|0)+8).toString(16), // [89ab]
        randStr.substr(15, 3), "-",
        randStr.substr(18, 12)
        ].join("").toUpperCase();
}

/* cookie wasn't available in Angular 1.2 */
function setCookie(cname, cvalue) {
	var d = new Date();
	d.setTime(d.getTime() + (30*24*60*60*1000));
	var expires = "expires="+d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}

/* cookie wasn't available in Angular 1.2 */
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return null;
}

// rfc4122 version 4 compliant
function guid() {
    var nbr, randStr = "";
    do {
        randStr += (nbr = Math.random()).toString(16).substr(2);
    } while (randStr.length < 30);
    return [
        randStr.substr(0, 8), "-",
        randStr.substr(8, 4), "-4",
        randStr.substr(12, 3), "-",
        ((nbr*4|0)+8).toString(16), // [89ab]
        randStr.substr(15, 3), "-",
        randStr.substr(18, 12)
        ].join("").toUpperCase();
}
