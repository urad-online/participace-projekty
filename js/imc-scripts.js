var imcMap;
var imcMarker;
var markersList = [];
var imcPolygonBoundaries = null;
var imcBoundariesList = [];


function imcInitializeMap(lat, lng, elemId, inputId, draggable, zoom, allowScroll, coords) {
    "use strict";

    var latlng = new google.maps.LatLng(lat, lng);

    var options = {
        zoom: zoom,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        scrollwheel: allowScroll
    };

    imcMap = new google.maps.Map(document.getElementById(elemId), options);

    imcMarker = new google.maps.Marker({
        position: latlng,
        draggable: draggable,
        map: imcMap
    });

    imcMap.setCenter(imcMarker.position);

    // If coords are set, draw and handle boundaries
    if (coords) { imcDrawBoundaries(imcMap, coords); }

    if (draggable) {

        var input = /** @type {!HTMLInputElement} */(
            document.getElementById(inputId));

        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', imcMap);

        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();

            if (place.formatted_address) {
                document.getElementById(inputId).value = place.formatted_address;
            }
            imcFindAddress(inputId, true);
        });

        google.maps.event.addListener(imcMarker, 'dragend', function (e) {
            var lat = e.latLng.lat();
            var lng = e.latLng.lng();

            imcFindAddress(inputId, false, lat, lng);
        });

        // Run geocoding on Enter
        document.getElementById(inputId).addEventListener("keydown", function(e) {

            if (e.key === 'Enter') {  //checks whether the pressed key is "Enter"
                e.preventDefault();
            }
        });
    }
}

function imcFindAddress(inputId, reverse, lat, lng) {
    "use strict";

    var geo = new google.maps.Geocoder();
    var latLng = new google.maps.LatLng(lat, lng);

    // This runs when typing an address to the Places - address search bar
    if (reverse) {

        var address = document.getElementById(inputId).value;
        geo.geocode( { 'address': address}, function(results, status) {

            var string;

            // If address is set up inside the bounds, from the settings panel,
            // then the initial values are always set correctly.
            if (status == google.maps.GeocoderStatus.OK) {

                if (imcPolygonBoundaries) {

                    // Check for boundaries
                    if (google.maps.geometry.poly.containsLocation(results[0].geometry.location, imcPolygonBoundaries)) {

                        imcMap.setCenter(results[0].geometry.location);

                        imcMarker.setPosition(results[0].geometry.location);

                        string = results[0].geometry.location.toString();
                        string = string.slice(1,-1).split(',');

                        document.getElementById(inputId).value = results[0].formatted_address;

                        // This runs for the Add New Issue
                        if (document.getElementById('imc_address')) {
                            document.getElementById('imc_lat').value = String(parseFloat(string[0]));
                            document.getElementById('imc_lng').value = String(parseFloat(string[1]));
                            document.getElementById('imc_address').value = results[0].formatted_address;
                            document.getElementById('map_input_id').value = results[0].formatted_address;
                            document.getElementById('imcLatValue').value = String(parseFloat(string[0]));
                            document.getElementById('imcLngValue').value = String(parseFloat(string[1]));

                        } else {

                            document.getElementById('imcLatValue').value = String(parseFloat(string[0]));
                            document.getElementById('imcLngValue').value = String(parseFloat(string[1]));
                        }

                    } else {

                        alert(imcScriptsVars.boundsAlert);
                    }

                } else {

                    imcMap.setCenter(results[0].geometry.location);

                    imcMarker.setPosition(results[0].geometry.location);

                    string = results[0].geometry.location.toString();
                    string = string.slice(1,-1).split(',');

                    document.getElementById(inputId).value = results[0].formatted_address;

                    // This runs for the Add New Issue
                    if (document.getElementById('imc_address')) {
                        document.getElementById('imc_lat').value = String(parseFloat(string[0]));
                        document.getElementById('imc_lng').value = String(parseFloat(string[1]));
                        document.getElementById('imc_address').value = results[0].formatted_address;
                        document.getElementById('map_input_id').value = results[0].formatted_address;

                    } else {

                        document.getElementById('imcLatValue').value = String(parseFloat(string[0]));
                        document.getElementById('imcLngValue').value = String(parseFloat(string[1]));
                    }

                }

            }
            else {

                document.getElementById(inputId).value = imcScriptsVars.addressAlert;
                console.log(status);
            }
        });
    }

    // This runs when dragging the marker
    else {
        geo.geocode({'latLng': latLng}, function(results, status) {

            if (status === google.maps.GeocoderStatus.OK) {
                if (results[0]) {

                    if (imcPolygonBoundaries) {

                        if (google.maps.geometry.poly.containsLocation(results[0].geometry.location, imcPolygonBoundaries)) {

                            document.getElementById(inputId).value = results[0].formatted_address;

                            // This runs for the Add New Issue
                            if (document.getElementById('imc_address')) {
                                document.getElementById('imc_lat').value = lat;
                                document.getElementById('imc_lng').value = lng;
                                document.getElementById('imc_address').value = results[0].formatted_address;
                                document.getElementById('map_input_id').value = results[0].formatted_address;

                            } else {

                                document.getElementById('imcLatValue').value = lat;
                                document.getElementById('imcLngValue').value = lng;
                            }

                            // We need to move the marker to last known position, and keep our previous values too.
                        } else {

                            alert(imcScriptsVars.boundsAlert);

                            var previousAddress = document.getElementById(inputId).value;

                            geo.geocode( { 'address': previousAddress}, function(results, status) {
                                if (status == google.maps.GeocoderStatus.OK) {
                                    imcMarker.setPosition(results[0].geometry.location);
                                } else {

                                    document.getElementById(inputId).value = imcScriptsVars.addressAlert;
                                    console.log(status);
                                }
                            });
                        }
                    } else {

                        document.getElementById(inputId).value = results[0].formatted_address;

                        // This runs for the Add New Issue
                        if (document.getElementById('imc_address')) {
                            document.getElementById('imc_lat').value = lat;
                            document.getElementById('imc_lng').value = lng;
                            document.getElementById('imc_address').value = results[0].formatted_address;
                            document.getElementById('map_input_id').value = results[0].formatted_address;

                        } else {

                            document.getElementById('imcLatValue').value = lat;
                            document.getElementById('imcLngValue').value = lng;
                        }

                    }

                }
                else {
                    document.getElementById(inputId).value = imcScriptsVars.noResults;
                    console.log(status);
                }
            }
            else {
                document.getElementById(inputId).value = imcScriptsVars.geoCoderFail;
                console.log(status);
            }
        });
    }
}


function imcDeleteAttachedImage(id) {
    "use strict";

    // For edit issue purposes
    if (document.getElementById('imcPreviousImg')) {
        document.getElementById('imcPreviousImg').remove();
        document.getElementById(id).value = "";
        document.getElementById('imcImgScenario').value = "1";
    }

    document.getElementById(id).value = "";

    jQuery("#imcReportFormSubmitErrors").html("");

    jQuery("#imcPhotoAttachedFilename").html("");
    jQuery("#imcNoPhotoAttachedLabel").show();
    jQuery("#imcPhotoAttachedLabel").hide();
    jQuery("#imcLargePhotoAttachedLabel").hide();
    jQuery("#imcReportAttachedImageThumb").remove();
}

function imcInitOverviewMap(json, plugin_path) {
    "use strict";

    var i;

    var map = new google.maps.Map(document.getElementById('imcOverviewMap'), {
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        zoom: 12,
        scrollwheel: false
    });

    var infoBubble = new InfoBubble({
        shadowStyle: 0,
        padding: 8,
        backgroundColor: '#ffffff',
        borderRadius: 2,
        borderWidth: 0,
        disableAutoPan: false,
        hideCloseButton: true,
        backgroundClassName: 'imc-row-no-margin imc-InfoWindowInnerStyle',
        arrowStyle: 0,
        arrowSize: 10,
        arrowPosition: 50,
        minWidth:300,
        maxWidth:300,
        minHeight: 220

    });
    var latLng = new google.maps.LatLng(json[0].lat, json[0].lng);
    var bounds = new google.maps.LatLngBounds(latLng);

    var marker;
    var iconSrc;

    if (json[0].id) {

        for (i = 0; i < json.length; i++) {

            latLng = new google.maps.LatLng(json[i].lat, json[i].lng);

            iconSrc = json[i].catIcon ? json[i].catIcon : plugin_path + "/img/ic_marker_default.png";

            if (json[i].myIssue) {

                marker = new RichMarker({
                    position: latLng,
                    id: json[i].id,
                    draggable: false,
                    map: map,
                    content:'<div class="imc-MapMarkerWrapStyle imc-CenterContents">' +
                    '<div id="marker-'+json[i].id+'" class="imc-MapMarkerBaseStyle imc-MapMarkerBaseOwnedStyle imc-BGColorAccent" style="border: 1px solid rgba(0,0,0,0.23);">' +
                    '<img src="'+ iconSrc +'" class="imc-MarkerCatStyle">' +
                    '</div>'
                });
            } else {
                marker = new RichMarker({
                    position: latLng,
                    draggable: false,
                    id: json[i].id,
                    map: map,
                    content: '<div class="imc-MapMarkerWrapStyle imc-CenterContents">' +
                    '<div id="marker-'+json[i].id+'" class="imc-MapMarkerBaseStyle imc-MapMarkerBaseDefaultStyle imc-BGColorWhite" style="border: 1px solid rgba(0,0,0,0.23);">' +
                    '<img src="'+ iconSrc +'" class="imc-MarkerCatStyle">' +
                    '</div>'
                });
            }

            var data = json[i];

            // extend the bounds to include each marker's position
            bounds.extend(marker.position);

            google.maps.event.addListener(marker, 'click', (
                function(marker) {
                    var title = data.title;
                    var id = data.id;
                    var url = data.url;
                    var photo = data.photo;
                    var cat = data.cat;
                    var votes = data.votes;

                    return function() {
                        var maxLen = 39;
                        if (title.length > maxLen) {
                            title = title.substring(0, maxLen);
                            title = title + "...";
                        }

                        if (!photo) {
                            photo = "<div class='imc-OverviewGridNoPhotoWrapperStyle'><i class='imc-EmptyStateIconStyle material-icons md-huge'>landscape</i><span class='imc-DisplayBlock imc-ReportGenericLabelStyle imc-TextColorHint'>No photo submitted</span></div>";
                        }

                        var content = "";

                        if (infoBubble) {
                            infoBubble.setContent(content);
                            infoBubble.close(map, marker);
                        }
                        content =
                            "<div class='imc-CenterContents'>" +
                            "<a class='imc-DisplayBlock imc-CenterContents imc-OverviewInfoBubbleImageStyle' href='"+ url +"'>"+ photo +"</a>" +
                            "<a class='imc-InfoWindowTitleStyle imc-LinkStyle' href='"+ url +"'>"+ title +"</a>" +
                            "</div>" +
                            "<div class='imc-CenterContents'> <span class='imc-OptionalTextLabelStyle'>"+ cat +"</span><br>" +
                            "<span class='imc-VerticalAlignMiddle imc-Text-MD imc-TextColorSecondary'>#</span><span class='imc-OptionalTextLabelStyle'>"+ id +"</span>&nbsp;&nbsp; &nbsp;&nbsp;" +
                            "<i class='material-icons md-18 imc-VerticalAlignMiddle imc-TextColorHint'>thumb_up</i> <span class='imc-OptionalTextLabelStyle'>"+ votes +"</span></div>"
                        ;

                        infoBubble.setContent(content);
                        infoBubble.open(map, marker);

                    }
                })(marker, i)
            );

            google.maps.event.addListener(marker, 'mouseover', (
                function() {
                    var id = data.id;

                    if (jQuery("#issue-"+id).hasClass('imc-OverviewListStyle')) {

                        return function() {
                            jQuery("#issue-"+id).css("border-bottom","2px solid rgba(0,0,0,0.12)").css("border", "2px solid #1ABC9C");

                        }

                    } else {
                        return function() { jQuery("#issue-"+id).css("border", "2px solid #1ABC9C"); }
                    }

                })(marker, i)
            );

            google.maps.event.addListener(marker, 'mouseout', (
                function() {
                    var id = data.id;

                    var bgColor = jQuery("#issue-"+id).css("background-color");

                    if (jQuery("#issue-"+id).hasClass('imc-OverviewListStyle')) {

                        return function() {
                            jQuery("#issue-"+id).css("border", "2px solid " + bgColor).css("border-bottom","2px solid rgba(0,0,0,0.12)").css("outline", "none");
                        }

                    } else {

                        return function() {
                            jQuery("#issue-"+id).css("border", "none").css("outline", "none");
                        }
                    }


                })(marker, i)
            );

            markersList[json[i].id] = [];
            markersList[json[i].id].push(marker);
        }
    }

    // now fit the map to the newly inclusive bounds
    map.fitBounds(bounds);
    var listener = google.maps.event.addListener(map, "idle", function() {
        if (map.getZoom() > 16) map.setZoom(16);
        google.maps.event.removeListener(listener);
    });

    // Map click event listener
    google.maps.event.addListener(map, "click", function() {
        if(infoBubble) {infoBubble.close(map, marker); }
    });
}


function loadOverviewMouseEventScripts(elemId, postId) {
    // Add Event Listeners for sub php file content
    var issueItem = document.getElementById(elemId);
    issueItem.addEventListener("mouseover", function() {
        imcFocusMarker(postId);
    });

    issueItem.addEventListener("mouseleave", function() {
        imcResetMarkerFocus(postId);
    });
}

function imcFocusMarker(id) {
    markersList[id][0].setZIndex(google.maps.Marker.MAX_ZINDEX + 1);

    var content = markersList[id][0].getContent();
    var output = content.replace("1px solid rgba(0,0,0,0.23)", "2px solid rgba(0,0,0,0.6)");
    markersList[id][0].setContent(output);
}

function imcResetMarkerFocus(id) {
    markersList[id][0].setZIndex(google.maps.Marker.MAX_ZINDEX - 1);

    var content = markersList[id][0].getContent();
    var output = content.replace("2px solid rgba(0,0,0,0.6)", "1px solid rgba(0,0,0,0.23)");
    markersList[id][0].setContent(output);
}

function imcSelectBoundaries(id) {

    var i, j;
    var coords = [];

    var boundaries = [];
    var geo = imcBoundariesList[id].geojson.coordinates;

    switch (imcBoundariesList[id].geojson.type) {
        case 'MultiPolygon':

            for (i=0; i< geo.length; i++) {
                for (j=0; j< geo[i][0].length; j++) {
                    coords.push({
                        lng: parseFloat(geo[i][0][j][0]),
                        lat: parseFloat(geo[i][0][j][1])
                    });
                }
                boundaries.push(coords);
                coords = [];
            }
            break;

        case 'Polygon':

            for (i=0; i< geo.length; i++) {

                for (j=0; j< geo[i].length; j++) {
                    coords.push({
                        lng: parseFloat(geo[i][j][0]),
                        lat: parseFloat(geo[i][j][1])
                    });
                }
                boundaries.push(coords);
                coords = [];
            }
            break;

    }

    jQuery("#imc-boundaries-textarea").val(JSON.stringify(boundaries));

    imcDrawBoundaries(imcMap, boundaries);
}

function imcDrawBoundaries(map, polygons) {
    "use strict";

    if (imcPolygonBoundaries) {
        imcPolygonBoundaries.setMap(null);
    }

    imcPolygonBoundaries = new google.maps.Polygon({
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: '#FF0000',
        fillOpacity: 0.35
    });

    imcPolygonBoundaries.setPaths(polygons);
    imcPolygonBoundaries.setMap(map);
}