/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 03.09.13
 * Time: 8:26
 * To change this template use File | Settings | File Templates.
 */
var VMapStyle=(function(){
	return {
		mapOptions:function(){
			return {
				zoom: 11,
				minZoom: 5,
				maxZoom: 16,
				scrollwheel: true,
				navigationControl: true,
				mapTypeControl: false,
				panControl: false,
				panControlOptions: {
					position: google.maps.ControlPosition.TOP_RIGHT
				},
				zoomControl: true,
				zoomControlOptions: {
					position: google.maps.ControlPosition.TOP_RIGHT,
					style: google.maps.ZoomControlStyle.DEFAULT
				},
				scaleControl: false,
				scaleControlOptions: {
					position: google.maps.ControlPosition.TOP_RIGHT
				},
				streetViewControl: false,
				streetViewControlOptions: {
					position: google.maps.ControlPosition.TOP_RIGHT
				},
				mapTypeControlOptions: {
					mapTypeIds: [google.maps.MapTypeId.ROADMAP, 'hiphop']
				},
				disableDoubleClickZoom:true
			};
		},
		mapStyle:function(){
			return [
				{stylers:[{saturation: -100},{lightness: 15}]},
				{featureType:"administrative.locality",elementType: "all",stylers:[{visibility: "off"}]},
				{featureType:"poi",elementType: "all",stylers:[{visibility:"off"}]},
				{featureType:"transit",elementType: "labels",stylers:[{visibility:"off"}]},
				{featureType:"transit.station.bus",stylers:[{visibility:"on"}]},
				{featureType: "water",stylers:[{ hue: "#c1d1e2"},{saturation: 50},{lightness: -1}]}
			]
		}
	}
}());