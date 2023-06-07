

const mapsRemoveChildren = function (parent) {
    if (parent === null)
        return;

    while (parent.firstChild)
        parent.removeChild(parent.firstChild);
}


const CustomMaps = {

    mapid: "map",

    containerId: "mapContainer",

    maps_markers: "maps_markers",

    markers: {},

    map: null,

    pos : [],

    zoom : 16,

    initCoordinates: [51.3806163904022, 12.499801813548224],
    
    currentId : -1,

    ids : { },

    createInstance() 
    {
        CustomMaps.pos = CustomMaps.readAsJson();
        if (Object.keys(CustomMaps.pos).length !== 0)
            CustomMaps.init();
    },

    updateCooinates : function()
    {
        const elem = document.getElementById("tt-tour-current");
        if (elem === null)
            return null;

        try
        {
            const lat = parseFloat(elem.getAttribute("data-lat"));
            const long = parseFloat(elem.getAttribute("data-long"));
            const id = parseInt(elem.getAttribute("data-id"));

            CustomMaps.initCoordinates = [lat, long];
            CustomMaps.currentId = id;
        }
        catch (e)
        {
            console.warn(e.message);
        }
    },

    init: function () 
    {
        CustomMaps.updateCooinates();
        CustomMaps.buildMap();
        CustomMaps.addOverlay(CustomMaps.pos);
        CustomMaps.addMarkers(CustomMaps.pos);

        CustomMaps.hasCurrentStop();
    },

    onSelectChange : function(e)
    {   
        const value = e.target.value;
        for (let elem of document.getElementsByClassName("tt-overlay-etappe"))
        {
            if (value === "" || elem.getAttribute("data-tours").indexOf(value) !== -1)
                elem.classList.remove("tt-hide");
            else
                elem.classList.add("tt-hide");
        }
    },

    appendSelect : function(div, tourNames)
    {
        if (tourNames.length === 0)
            return;

        const select = document.createElement("select");
        div.appendChild(select);

        if (tourNames.length > 1)
        {
            const opt = document.createElement("option");
            opt.value = "";
            opt.label = "Alle Etappen aller Touren"
            opt.onclick = CustomMaps.onSelect;
            select.appendChild(opt);
        }
        else
            select.setAttribute("disabled", "");

        for (let name of tourNames)
        {
            const opt = document.createElement("option");
            opt.value = name;
            opt.label = name;
            select.appendChild(opt);
            CustomMaps.ids[name] = opt.value;
        }

        select.onchange = CustomMaps.onSelectChange;
    },

    appendEtappen(div, names, json)
    {
        for (let etappe of names)
            div.append(this.createAddressElement(json[etappe]));
    },

    createAddressElement: function(data)
    {
        const elem = document.createElement("div");
        elem.setAttribute("class", "tt-overlay-etappe");
        elem.setAttribute("data-tours", data.tours === undefined ? "" : data.tours.toString());  

        const frag = document.createDocumentFragment();
        
        let p1 = document.createElement("strong");
        p1.innerText = data.text;
        frag.appendChild(p1);

        p1 = document.createElement("address");
        if (data.address === "")
            p1.innerHTML = `<small>&nbsp;</small>`;
        else
            p1.innerHTML = `<small>${data.address}</small>`;
            
        frag.appendChild(p1);

        elem.appendChild(frag);

        const mask = document.createElement("div");
        mask.setAttribute("data-marker-url", data.url);
        mask.setAttribute("class", "tt-mask");
        mask.setAttribute("id", "tt_map_marker_" + data.id);
        mask.onmouseover = CustomMaps.onMouseOver;
        mask.onclick = CustomMaps.onMouseOver;

        elem.appendChild(mask);
        return elem;
    },

    onMouseOver : function(e)
    {
        let elem = e.target.nodeName.toLowerCase() === "div" ? e.target : e.target.parentNode;
        if (elem.nodeName.toLowerCase() !== "div")
            return;

        const url = elem.getAttribute("data-marker-url");
        if (CustomMaps.markers[url] !== undefined)
        {
            CustomMaps.map.flyTo(CustomMaps.markers[url].getLatLng(), 16);
            CustomMaps.markers[url].openPopup();
        }
    },

    addOverlay : function(json)
    {
        const div = document.createElement("div");
        div.setAttribute("class", "tt-overlay");

        let names = Object.keys(json);
        names.sort();

        this.appendSelect(div, this.getTours(json));
        this.appendEtappen(div, names, json);

        document.getElementById(CustomMaps.containerId).appendChild(div);
    },

    getTours : function(json)
    {
        let tours = [];
        for (let etappe of Object.keys(json))
        {
            for (let _tour of json[etappe].tours)
            {
                if(_tour !== "" && !tours.includes(_tour))
                    tours.push(_tour);
            }
        }
    
        tours.sort();
        return tours;
    },

    buildMap: function () {
        document.getElementById(CustomMaps.containerId).classList.remove("tt-hide");
        const mapConf = {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
            scrollWheelZoom: false
        }

        CustomMaps.map = L.map(CustomMaps.mapid);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', mapConf).addTo(CustomMaps.map);
        L.control.scale().addTo(CustomMaps.map);
        CustomMaps.map.scrollWheelZoom.disable();
        CustomMaps.map.on('locationerror', CustomMaps.onLocationError);
        CustomMaps.map.on('locationfound', CustomMaps.onLocationFound);
        CustomMaps.map.locate({setView: true, maxZoom: 17});
    },

    hasCurrentStop : function()
    {
        const elem = document.getElementById("tt_map_marker_" + CustomMaps.currentId);
        if (elem !== null)
        {
            elem.parentNode.classList.add("tt-overlay-etappe-current");
            return true;
        }
        else
            return false;
    },

    onLocationFound :  function(e) 
    {
        const radius = e.accuracy;
        L.circle(e.latlng, 200, {color: "red", opacity:.3}).addTo(CustomMaps.map);
        L.circle(e.latlng, radius, {color: "#3388ff", opacity:.3}).addTo(CustomMaps.map);
    },
    
    onLocationError : function()
    {
        CustomMaps.map.setView(CustomMaps.initCoordinates, CustomMaps.zoom)
    },

    createMakerPopupText: function(data)
    {
        return `<a class="tt-map-is-mobile" href="geo:${data.lat},${data.lng}"><b>${data.text}</b><br>${data.address}</a>
            <a class="tt-map-is-desktop" href="${data.url}"><b>${data.text}</b><br>${data.address}</a>`;
    },

    addMarkers: function (markers) 
    {
        for (let key of Object.keys(markers))
        {
            const _elem = markers[key];

            let marker = L.marker([_elem.lat, _elem.lng]).bindPopup(CustomMaps.createMakerPopupText(_elem))
                .addTo(CustomMaps.map)
                .on('click', CustomMaps.onMarkerClick);

            CustomMaps.markers[_elem.url] = marker;
        }
    },

    onMarkerClick: function (e) {
        CustomMaps.map.flyTo(e.target._latlng);
    },

    error: function (err) {
        console.error(err);
        document.getElementById(CustomMaps.mapid).classList.add("tt-hide");
    },

    getNameAndAddress(elem)
    {
        let res = {
            name: "",
            address: ""
        };
        const list = elem.querySelectorAll("span");
        if (list === null || list.length === 0)
            return res;

        res.name = list[0].innerText.trim();
        if (list.length >= 2)
            res.address = list[1].innerText.trim();

        return res;
    },

    readJsonFomMulti : function(res, tour)
    {
        let tourName = tour.querySelector(".tt-tour-name").innerText;
        if (tourName === null || tourName === undefined)
            tourName = "";

        for (let ort of document.getElementsByClassName("tt-titleBox")) 
        {
            const _n = CustomMaps.getNameAndAddress(ort);
            if (_n.name === "")
                continue;

            const name = _n.name;
            if (res[name] === undefined)
            {
                res[name] = {
                    lat: ort.getAttribute("data-geo-lat"),
                    lng: ort.getAttribute("data-geo-long"),
                    url: ort.getAttribute("href"),
                    address: _n.address,
                    id: ort.getAttribute("data-id"),
                    text: ort.querySelector("span").innerText,
                    tours: []
                };
            }
            
            if (tourName !== "")
                res[name].tours.push(tourName);
        }

    },

    readAsJsonSingle : function(res, tour)
    {
        for (let elem of tour.getElementsByClassName("tt-box")) 
        {
            let tourNames = [];
            const names = elem.getElementsByClassName("tt-tour-name-single");
            for (let _name of names)
            {
                const val = _name.innerText.trim();
                if (val !== "" && !tourNames.includes(val))
                    tourNames.push(val);
            }

            const ort = elem.querySelector(".tt-titleBox");
            if (ort === null)
                continue;

            const _n = CustomMaps.getNameAndAddress(ort);
            if (_n.name === "")
                continue;

            res[_n.name] = {
                lat: ort.getAttribute("data-geo-lat"),
                lng: ort.getAttribute("data-geo-long"),
                url: ort.getAttribute("href"),
                id: ort.getAttribute("data-id"),
                address: _n.address,
                text: ort.querySelector("span").innerText,
                tours: tourNames
            };
        }
    },

    readAsJson: function ()
    {
        let res = {};

        const list = document.getElementsByClassName("tt-wrap");
        if (list === null || list.length === 0)
            return res;

        for (let tour of list)
        {
            if (!tour.hasAttribute("data-is-single-list"))
                this.readJsonFomMulti(res, tour);
            else
                this.readAsJsonSingle(res, tour);
        }
        return res;
    }
};


(function () {
    if (document.getElementById("mapContainer") !== null)
        setTimeout(() => CustomMaps.createInstance(), 100);
})();