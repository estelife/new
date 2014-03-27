/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 03.09.13
 * Time: 8:26
 * To change this template use File | Settings | File Templates.
 */
var VMapStyle=(function(){
	if (typeof google == 'undefined')
		return null;

	return {
		mapOptions:function(){
			return {
				zoom: 11,
				minZoom: 5,
				maxZoom: 20,
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
				{
					"featureType": "road.arterial",
					"stylers": [
						{ "color": "#f9f9f9" }
					]
				},{
					"featureType": "road.highway",
					"elementType": "geometry.fill",
					"stylers": [
						{ "color": "#7f7f7f" }
					]
				},{
					"featureType": "water",
					"elementType": "geometry",
					"stylers": [
						{ "color": "#c3d2e3" }
					]
				},{
					"featureType": "road.local",
					"elementType": "geometry.fill",
					"stylers": [
						{ "color": "#d1d1d1" }
					]
				},{
					"featureType": "administrative.locality",
					"stylers": [
						{ "color": "#222222" },
						{ "weight": 0.4 }
					]
				}
			]
		}
	}
}());