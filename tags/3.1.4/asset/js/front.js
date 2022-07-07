/**
 * Init google map api
 * @var maps[{}]
 */
(function () {
    for (let i = 0; i < maps.length; i++) {
        map        = new google.maps.Map(document.getElementById('map-multi-marker-' + maps[i].map_id), maps[i].map_option);
        infoWindow = new google.maps.InfoWindow();
        // Close event
        google.maps.event.addListener(map, 'click', function () {
            infoWindow.close();
        });
        // Resize event
        window.onresize = function () {
            var center = map.getCenter();
            google.maps.event.trigger(map, "resize");
            map.setCenter(center);
        };
        // Set markers
        var markers     = maps[i].markers;
        for (var j = 0; j < markers.length; j++) {
            set_markers(markers[j].id, new google.maps.LatLng(markers[j].lat, markers[j].lng), markers[j].icon);
        }
    }
}());

/**
 * Set markers
 * @param id
 * @param latLng
 * @param icon
 * @var localize[{}]
 */
function set_markers(id, latLng, icon) {
    var marker = new google.maps.Marker({
        position: latLng,
        map: map,
        icon: icon
    });
    // Event click
    google.maps.event.addListener(marker, 'click', function () {
        // Open tooltip
        infoWindow.open(map, marker);
        //Add loader
        infoWindow.setContent('<div id="infobulle"><div class="loader-marker-c" style="padding: 30px 20px 30px 40px;text-align:center;"><i class="fa fa-refresh fa-spin fa-3x fa-fw" style="color:#C7C7C7;"></i><span class="sr-only"></span></div></div>');
        // Async request
        var xhr = new XMLHttpRequest();
        xhr.open('POST', localize.ajax_url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
        xhr.send('action=mmm_async_content_marker&id=' + id);
        xhr.onload = function () {
            if (xhr.status >= 200 && xhr.status < 400) {
                // Success!
                var response = xhr.responseText;
                infoWindow.setContent('<div id="infobulle">' + response + '</div>');
            }
        };
    });
}