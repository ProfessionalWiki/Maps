 /**
  * Javascript utility functions for Maps and it's extensions
  *
  * @file MapUtilityFunctions.js
  * @ingroup Maps
  *
  * @author Robert Buzink
  * @author Yaron Koren   
  * @author Jeroen De Dauw
  */

/*
function addLoadEvent(func) {
	var oldonload = window.onload;
	if (typeof oldonload == 'function') {
		window.onload = function() {
			oldonload();
			func();
		};
	}
	else { 
		window.onload = func;
	}
}
*/

function convertLatToDMS (val) {
	return Math.abs(val) + "° " + ( val < 0 ? "S" : "N" );
}

function convertLngToDMS (val) {
	return Math.abs(val) + "° " + ( val < 0 ? "W" : "E" );

}