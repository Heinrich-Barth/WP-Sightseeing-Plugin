class wpsightseeingEditPost {

    init()
    {
        const geoLat = document.getElementById("tt_etappe_geo_lat");
        if (geoLat !== null)
            geoLat.onpaste = this.onPasteAction.bind(this);

        const geoLon = document.getElementById("tt_etappe_geo_long");
        if (geoLon !== null)
            geoLon.onpaste = this.onPasteAction.bind(this);
    }

    insertContent(id, text)
    {
        const geoLon = document.getElementById(id);
        if (geoLon !== null && text !== "")
            geoLon.value = text.trim();
    }

    removeTraillingComma(text)
    {
        if (text.endsWith('.') || text.endsWith(','))
            return text.substring(0, text.length -1);
        else 
            return text;
    }

    getClipboardData(e)
    {
        let content = null;

        if( e.clipboardData )
            content = e.clipboardData.getData('text/plain');
        else if( window.clipboardData )
            content = window.clipboardData.getData('Text');

        return content === null || content === undefined ? "" : "" + content;
    }

    splitGeo(text)
    {
        if (text === undefined || text === "")
            return null;

        const parts = text.trim().split(' ');
        if (parts.length === 2 && parts[0] !== "" && !parts[1] !== "")
            return parts;
        else
            return null;
    }

    onPasteAction(e)
    {
        console.log("HALLO");
        const asGeo = this.splitGeo(this.getClipboardData(e))
        if (asGeo !== null && asGeo.length === 2)
        {
            this.insertContent("tt_etappe_geo_lat", this.removeTraillingComma(asGeo[0]));
            this.insertContent("tt_etappe_geo_long", this.removeTraillingComma(asGeo[1]));
            e.preventDefault();
            return false;
        }
    }

}




jQuery(document).ready(function () 
{
    new wpsightseeingEditPost().init();
});