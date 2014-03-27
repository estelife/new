/**
 * Created with JetBrains PhpStorm.
 * User: dmitriy
 * Date: 03.09.13
 * Time: 7:14
 * To change this template use File | Settings | File Templates.
 */
function VMap(){
	this.address=1;
	this.latlng=2;

	var prvt={
		map:null,
		markers:null,
		geocoder:null,
		events:{}
	};

	this.create=function(mapElement,lat,lng){
		if (!VMapStyle)
			return;

		if(typeof mapElement!='object' || !(mapElement instanceof jQuery) || mapElement.length<=0)
			return;

		var options=VMapStyle.mapOptions(),
			style=VMapStyle.mapStyle();

		options.center=new google.maps.LatLng(lat,lng);

		var map=new google.maps.Map(mapElement.get(0),options),
			mapType=new google.maps.StyledMapType(style,{
				name: "Карта"
			});

		map.mapTypes.set('hiphop', mapType);
		map.setMapTypeId('hiphop');

		prvt.map=map;
	};

	this.center=function(lat,lng,zoom){
		if(prvt.map){
			prvt.map.setCenter(new google.maps.LatLng(lat, lng));
			if(zoom)
				prvt.map.setZoom(zoom);
		}
	};

	this.zoom=function(zoom){
		if(prvt.map){
			prvt.map.setZoom(zoom);
		}
	};

	this.load=function(callback){
		if (!prvt.map)
			return;

		google.maps.event.addListenerOnce(prvt.map, 'idle', function(){
			callback();
		});
	};

	this.map=function(){
		return prvt.map;
	};

	this.on=function(event,callback){
		if(typeof callback=='function')
			prvt.events[event]=callback;
		else if(event in prvt.events)
			delete prvt.events[event];
	};

	this.geocoder=function(){
		if(!prvt.geocoder){
			prvt.geocoder=new google.maps.Geocoder();
		}
		return prvt.geocoder;
	};

	this.geocode=function(address,type){
		var params={};
		type=((type==this.latlng) ? 'location' : 'address');
		params[type]=address;
		this.geocoder().geocode(params,function(result,status){
			if (status == google.maps.GeocoderStatus.OK){
				var lat=result[0].geometry.location.lat(),
					lng=result[0].geometry.location.lng(),
					address=false;

				if(type=='location'){
					address={
						'city':(result[0].address_components.length>7) ?
							result[0].address_components[3].long_name :
							result[0].address_components[2].long_name,
						'street':result[0].address_components[1].long_name,
						'house':result[0].address_components[0].long_name
					};
				}

				if('geocode_true' in prvt.events)
					prvt.events.geocode_true(lat,lng,address);
			}else{
				if('geocode_false' in prvt.events)
					prvt.events.geocode_false(status);
				else
					alert("Geocode was not successful for the following reason: " + status);
			}
		});
	};

	this.markers=function(){
		if(!prvt.markers){
			prvt.markers=(function(){
				var user=null,
					icons=null,
					markers=new Array();
				return {
					icons:function(o){
						if(typeof o!='object')
							return icons;
						else
							icons=o;
					},
					user:function(marker){
						if(user)
							user.googleMarker().setMap(null);

						var icon='user';

						if(icon in icons){
							marker.googleMarker().setIcon(icons[icon]);
							var iconh=icon+'_hover';

							if(iconh in icons){
								marker.on('mouseover',function(){
									this.setZIndex(10);
									this.setIcon(icons[iconh]);
								});
								marker.on('mouseout',function(){
									this.setZIndex(1);
									this.setIcon(icons[icon]);
								});
							}
						}

						marker.googleMarker().setMap(prvt.map);
						marker.googleMarker().setVisible(true);
						user=marker;
					},
					add:function(marker,icon){
						if(!prvt.map)
							return;

						if(typeof marker!='object')
							throw 'incorrect marker type';

						var icon=icon||'default';

						if(icon && icon!='user' && (icon in icons)){
							marker.googleMarker().setIcon(icons[icon]);
							var iconh=icon+'_hover';

							if(iconh in icons){
								marker.on('mouseover',function(){
									this.setZIndex(10);
									this.setIcon(icons[iconh]);
								});
								marker.on('mouseout',function(){
									this.setZIndex(1);
									this.setIcon(icons[icon]);
								});
							}
						}

						markers.push(marker);
					},
					draw:function(){
						if(markers.length>0){
							for(var i=0;i<markers.length;i++){
								markers[i].googleMarker().setMap(prvt.map);
								markers[i].googleMarker().setVisible(true);
							}
						}
					},
					matchZoom:function(){
						//if(markers.length<=0)
							//return 11;

						var bounds = new google.maps.LatLngBounds();

						for(var i=0;i<markers.length;i++)
							bounds.extend(markers[i].googleMarker().getPosition());

						if(user)
							bounds.extend(user.googleMarker().getPosition());

						prvt.map.fitBounds(bounds);
						/*var map=$(prvt.map.b),
							width=map.width(),
							height=map.height(),
							earthRadius = 6371,
							degToRadDivisor = 57.2958,
							lngs=new Array(),
							lats=new Array();

						for(var i=0;i<markers.length;i++){
							lats.push(markers[i].option('lat'));
							lngs.push(markers[i].option('lng'));
						}

						if(user){
							lats.push(user.option('lat'));
							lngs.push(user.option('lng'));
						}

						var minLat=Math.min.apply(Math,lats),
							maxLat=Math.max.apply(Math,lats),
							minLng=Math.min.apply(Math,lngs),
							maxLng=Math.max.apply(Math,lngs);

						var dist=(earthRadius * Math.acos(Math.sin(minLat / degToRadDivisor) * Math.sin(maxLat / degToRadDivisor) + (Math.cos(minLat / degToRadDivisor) * Math.cos(maxLat / degToRadDivisor) * Math.cos((maxLng / degToRadDivisor) - (minLng / degToRadDivisor))))),
							zoom=Math.floor(7 - Math.log(1.6446 * dist / Math.sqrt(2 * (width * height))) / Math.log (2));

						zoom-=1;

						if(mapSet){
							prvt.map.setZoom(zoom);
						}*/

						//return zoom;
					},
					clear:function(){
						for(var i=0;i<markers.length;i++){
							markers[i].destroy();
						}

						markers=new Array();
					}
				}
			}());
		}

		return prvt.markers;
	};

	this.marker=function(lat,lng,id){
		//return (new function(){
			var events={},
				googleMarker=null,
				infoBox=null,
				options={
					lat:lat,
					lng:lng,
					id:id
				};
			this.option=function(key){
				return (key in options) ?
					options[key] :
					false;
			};
			this.googleMarker=function(){
				if(!prvt.map)
					return null;

				if(!googleMarker){
					googleMarker=new google.maps.Marker({
						draggable: false,
						position: new google.maps.LatLng(options.lat,options.lng),
						visible: false,
						zIndex: 1
					});
					googleMarker.bcid = id;
					googleMarker.likeProp = false;
					googleMarker.infoOpened = false;
				}
				return googleMarker;
			};
			this.on=function(event,callback){
				if(typeof callback=='function')
					google.maps.event.addListener(this.googleMarker(),event,callback);
			};
			this.visible=function(b){
				this.googleMarker().setVisible(b);
			};
			this.destroy=function(){
				this.googleMarker().setMap(null);
			};
			this.infoBox=function(){
				if(!infoBox){
					infoBox=(function(marker){
						var box=null,
							marker=marker;
						var temp={
							init:function(){
								if(typeof InfoBox!='function')
									return;

								var myOptions = {
									disableAutoPan: false,
									maxWidth: 0,
									zIndex: null,
									closeBoxMargin: '0px; position: absolute; right:0; z-index: 3; display: none',
									pixelOffset: new google.maps.Size(0, -42),
									visible: true,
									pane: "floatPane",
									enableEventPropagation: true,
									alignBottom: true
								};
								box=new InfoBox(myOptions);
								var ob=this;

								$(document).click(function(){
									ob.close();
								});
							},
							content:function(content){
								box.setContent(content);
							},
							open:function(){
								if(box){
									box.open(MAP.map(), marker.googleMarker());
									marker.visible(false);
								}
							},
							close:function(){
								if(box){
									box.close();
									marker.visible(true);
								}
							}
						};
						temp.init();
						return temp;
					}(this));
				}

				return infoBox;
			};
		//});
	};
};