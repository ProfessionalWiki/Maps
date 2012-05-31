mapEditor = {
    __map:null,
    __mapObjects:[],
    __controlsDiv:null,
    __options:{
        canvas:'map-canvas',
        onRightClick:function(){}
    },
    __mapObjectOptions: {
        marker:{
            draggable:true
        },
        polyline:{
            strokeWeight:2,
            strokeOpacity:1,
            strokeColor:'#FF0000',
            editable:true
        },
        circle:{
            strokeWeight:2,
            strokeOpacity:1,
            strokeColor:'#FF0000',
            fillColor:'#FF0000',
            fillOpacity:0.5,
            editable:true
        },
        rectangle:{
            strokeWeight:2,
            strokeOpacity:1,
            strokeColor:'#FF0000',
            fillColor:'#FF0000',
            fillOpacity:0.5,
            editable:true
        },
        polygon:{
            strokeWeight:2,
            strokeOpacity:1,
            strokeColor:'#FF0000',
            fillColor:'#FF0000',
            fillOpacity:0.5,
            editable:true
        }

    },
    __mapParameters:{ //TODO look into getting this list from server dynamically
        mappingservice: {
            values:['googlemaps','openlayers']
        },
        copycoords: {
            values:['on','off']
        },
        markercluster: {
            values:['on','off']
        },
        searchmarkers:{
            values:[]
        },
        static:{
            values:['on','off']
        },
        maxzoom: {
            values:[]
        },
        minzoom: {
            values:[]
        },
        zoom:{
            values:[]
        },
        centre:{
            values:[]
        },
        title:{
            values:[]
        },
        label:{
            values:[]
        },
        icon:{
            values:[]
        },
        type:{
            values:[]
        },
        types:{
            values:[]
        },
        layers:{
            values:[]
        },
        controls:{
            values:[]
        },
        zoomstyle:{
            values:['default','small','large']
        },
        typestyle:{
            values:[]
        },
        autoinfowindows:{
            values:['on','off']
        },
        kml:{
            values:[]
        },
        gkml:{
            values:[]
        },
        fusiontables:{
            values:[]
        },
        resizable:{
            values:[]
        },
        tilt:{
            values:[]
        },
        kmlrezoom:{
            values:['on','off']
        },
        poi:{
            values:['on','off']
        }
    },
    __initControls:function(){
        //Create root controls element
        var controlDiv = this.__controlsDiv = document.createElement('div');
        controlDiv.setAttribute('class','mapeditor-controls');
        controlDiv.index = 1;

        this.__map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(controlDiv);
    },
    __initMap:function(){
        var myOptions = {
            center:new google.maps.LatLng(0, 0),
            zoom:1,
            mapTypeId:google.maps.MapTypeId.ROADMAP
        };

        this.__map = new google.maps.Map(document.getElementById(this.__options.canvas),myOptions);

        //noinspection JSUnresolvedVariable
        var drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode:null,
            drawingControl:true,
            drawingControlOptions:{
                position:google.maps.ControlPosition.TOP_CENTER
            },
            markerOptions:this.__mapObjectOptions.marker,
            circleOptions:this.__mapObjectOptions.circle,
            rectangleOptions:this.__mapObjectOptions.rectangle,
            polygonOptions:this.__mapObjectOptions.polygon,
            polylineOptions:this.__mapObjectOptions.polyline
        });
        drawingManager.setMap(this.__map);

        google.maps.event.addListener(drawingManager, 'overlaycomplete', function (e) {
            mapEditor.__mapObjects.push(e);
            mapEditor.__registerRightClickListener(e);
            google.maps.event.trigger(e.overlay, 'rightclick');
        });
    },
    __handleMetaData:function(e){
        var options = $.extend({},this.__mapObjectOptions[e.type]);
        for(var x = 0; x < e.metadata.length; x++){
            var data = e.metadata[x];
            if(data.value.trim() !== ''){
                options[data.name] = data.value;
            }
        }
        try{
            e.overlay.setOptions(options);
            return true;
        }catch(e){
            return false;
        }
    },
    __convertPositionalParametersToMetaData:function(positionalArray){
        var positionalNames = [
            'title',
            'text',
            'strokeColor',
            'strokeOpacity',
            'strokeWeight',
            'fillColor',
            'fillOpacity',
            'showOnHover'
        ]

        if(positionalArray != undefined && positionalArray.length > 0){
            if(positionalArray[0].trim().indexOf('link:') == 0){
                positionalNames.splice(0,1);
                positionalNames[0] = 'link';
            }
        }

        for(var x = 0; x < positionalArray.length; x++){
            positionalArray[x] = {
                name:positionalNames[x],
                value:positionalArray[x].trim()
            }
        }
        return positionalArray;
    },
    __registerRightClickListener:function(e){
        google.maps.event.addListener(e.overlay, 'rightclick', function () {
            mapEditor.__options.onRightClick(e);
        });
    },
    __generateWikiCode:function(){
        var code = '{{#display_line: '

        var markers = '';
        var circles = '';
        var polygons = '';
        var lines = '';
        var rectangles = '';

        for(var x = 0; x < this.__mapObjects.length; x++){
            var mapObject = this.__mapObjects[x].overlay;
            var mapObjectType = this.__mapObjects[x].type;
            var mapObjectMeta = this.__mapObjects[x].metadata;

            var metadata = '';
            if(mapObjectMeta !== undefined){
                var delimiterPosition = '';
                for(var i = 0; i < mapObjectMeta.length; i++){
                    delimiterPosition += delimiterPosition.length > 0 ? ' ~' : '~';
                    if(mapObjectMeta[i].value !== ''){
                        if(mapObjectMeta[i].name == 'link' && mapObjectMeta[i].value.indexOf('link:') == -1){
                            mapObjectMeta[i].value = 'link:'+mapObjectMeta[i].value;
                        }
                        metadata += delimiterPosition+mapObjectMeta[i].value
                        delimiterPosition = '';
                    }
                }
            }

            var data = mapObject+metadata;
            if (mapObjectType === 'marker') {
                markers += markers === '' ? data : '; '+data;
            } else if (mapObjectType === 'circle') {
                circles += circles === '' ? data : '; '+data;
            } else if (mapObjectType === 'polygon') {
                polygons += polygons === '' ? data : '; '+data;
            } else if (mapObjectType === 'polyline') {
                lines += lines === '' ? data : '; '+data;
            } else if (mapObjectType === 'rectangle') {
                rectangles += rectangles === '' ? data : '; '+data;
            }
        }

        code += markers !== '' ? markers : '';
        code += circles !== '' ? '\n|circles='+circles : '';
        code += polygons !== '' ? '\n|polygons='+polygons : '';
        code += lines !== '' ? '\n|lines='+lines : '';
        code += rectangles !== '' ? '\n|rectangles='+rectangles : '';

        //add map parameters
        for(var param in this.__mapParameters){
            var value = this.__mapParameters[param].value;
            if(value === undefined || value === ''){
                continue;
            }
            code += '\n|'+param+'='+value;
        }

        code += '\n}}\n';
        return code;
    },
    __importWikiCode:function(rawData){
        var syntaxPattern = /^{{#display_line:[\s\S]*}}[\s\n]*$/i;
        if(rawData.match(syntaxPattern) === null){ //NO MATCH
            return false;
        }else{
            try{
                var patterns = {
                    marker: /^{{#display_line:\s*(.*)/i,
                    polyline: /\|\s*lines=(.*)/i,
                    circle:/\|\s*circles=(.*)/i,
                    polygon:/\|\s*polygons=(.*)/i,
                    rectangle:/\|\s*rectangles=(.*)/i,
                    parameter:/\|\s*(.*)=(.*)/i
                };
                var mapObjects = [];
                rawData = rawData.split('\n');
                for(var j = 0; j < rawData.length; j++){
                    patterns:
                        for (var key in patterns){
                            var match = rawData[j].match(patterns[key])
                            if(match !== null && match[1].trim().length !== 0){
                                var isMapObject = false;
                                if(key !== 'parameter'){
                                    var data = match[1].split(';');
                                    for(var i = 0; i < data.length; i++){

                                        var metadata = data[i].split('~');
                                        metadata.splice(0,1);
                                        if(metadata.length > 0){
                                            data[i] = data[i].substring(0,data[i].indexOf('~'));
                                        }

                                        var options = this.__mapObjectOptions[key];
                                        var mapObject = null;
                                        if (key === 'marker') {
                                            var position = data[i].split(',');
                                            //noinspection JSValidateTypes
                                            options = $.extend({
                                                position: new google.maps.LatLng(position[0],position[1])
                                            },options);
                                            mapObject = new google.maps.Marker(options);
                                        } else if (key === 'circle') {
                                            var parts = data[i].split(':');
                                            var radius = parts[1];
                                            var position = parts[0].split(',');
                                            //noinspection JSValidateTypes
                                            options = $.extend({
                                                center: new google.maps.LatLng(position[0],position[1]),
                                                radius: parseFloat(radius)
                                            },options);
                                            mapObject = new google.maps.Circle(options);
                                        } else if (key === 'polygon') {
                                            var paths = data[i].split(':');
                                            for(var x = 0; x < paths.length; x++){
                                                var position = paths[x].split(',');
                                                paths[x] = new google.maps.LatLng(position[0],position[1]);
                                            }
                                            paths = new google.maps.MVCArray(paths);
                                            //noinspection JSValidateTypes
                                            options = $.extend({
                                                paths: paths
                                            },options);
                                            mapObject = new google.maps.Polygon(options);
                                        } else if (key === 'polyline') {
                                            var paths = data[i].split(':');
                                            for(var x = 0; x < paths.length; x++){
                                                var position = paths[x].split(',');
                                                paths[x] = new google.maps.LatLng(position[0],position[1]);
                                            }
                                            paths = new google.maps.MVCArray(paths);
                                            //noinspection JSValidateTypes
                                            options = $.extend({
                                                path: paths
                                            },options);
                                            mapObject = new google.maps.Polyline(options);
                                        } else if (key === 'rectangle') {
                                            var parts = data[i].split(':');
                                            var ne = parts[0].split(',');
                                            var sw = parts[1].split(',');
                                            sw = new google.maps.LatLng(sw[0],sw[1]);
                                            ne = new google.maps.LatLng(ne[0],ne[1]);
                                            //noinspection JSValidateTypes
                                            options = $.extend({
                                                bounds: new google.maps.LatLngBounds(sw,ne)
                                            },options);
                                            mapObject = new google.maps.Rectangle(options);
                                        }
                                        if(mapObject !== null){
                                            mapObject.setMap(this.__map);

                                            mapObject = {
                                                type:key,
                                                overlay:mapObject,
                                                metadata:this.__convertPositionalParametersToMetaData(metadata)
                                            };

                                            this.__registerRightClickListener(mapObject);
                                            this.__mapObjects.push(mapObject);
                                            this.__handleMetaData(mapObject);

                                            isMapObject = true;

                                        }
                                    }
                                }else if(!isMapObject){
                                    //handle global map parameters
                                    if(this.__mapParameters[match[1]] === undefined){
                                        this.__mapParameters[match[1]] = {};
                                    }
                                    this.__mapParameters[match[1]].value = match[2];
                                }
                            }
                        }
                }
            }catch(e){
                console.log('an error occured when parsing data');
                return false;
            }
            return true;
        }
    },
    addControlButton:function (text, onclick){
        // Set CSS for the control border
        var controlUI = $('<div class="mapeditor-control-element"></div>');
        $(controlUI).click(function(){
            onclick.call(this);
        }).appendTo(this.__controlsDiv);

        // Set CSS for the control interior
        var controlText = $('<span class="mapeditor-control-text"></span>')
            .text(text).appendTo(controlUI);
    },
    setup:function(o){
        //extend options
        $.extend(this.__options,o);

        //Override tostring methods for wiki code generation
        google.maps.LatLng.prototype.toString = function(){
            return this.lat()+','+this.lng();
        }

        google.maps.Rectangle.prototype.toString = function(){
            var bounds = this.getBounds();
            var ne = bounds.getNorthEast();
            var sw = bounds.getSouthWest();
            return ne+':'+sw;
        }

        google.maps.Marker.prototype.toString = function(){
            var position = this.getPosition();
            return position.lat()+','+position.lng()
        }

        google.maps.Circle.prototype.toString = function(){
            var center = this.getCenter();
            var radius = this.getRadius();
            return center.lat()+','+center.lng()+':'+radius
        }

        google.maps.Polygon.prototype.toString = function(){
            var polygons = '';
            this.getPath().forEach(function(e){
                polygons += ':'+e
            });
            return polygons.substr(1);
        }

        google.maps.Polyline.prototype.toString = function(){
            var lines = '';
            this.getPath().forEach(function(e){
                lines += ':'+e
            });
            return lines.substr(1);
        }

        //initialize rest
        this.__initMap();
        this.__initControls();
    }
}

$(document).ready(function(){
    mapEditor.setup({
        onRightClick:function(e){
            function openDialog(e){
                if(e.metadata !== undefined){
                    for(var x = 0; x < e.metadata.length; x++){
                        var data = e.metadata[x];
                        $(this).find('form input[name="'+data.name+'"]').val(data.value);
                    }
                }
                var i18nButtons = {};
                i18nButtons[mw.msg('mapeditor-done-button')] = function () {
                    var form = $(this).find('form');
                    form.find('.miniColors').each(function(){
                        if($(this).val() === '#'){
                            $(this).val('');
                        }
                    });
                    e.metadata = form.serializeArray();
                    $(this).dialog("close");
                    if(!mapEditor.__handleMetaData(e)){
                        alert(mw.msg('mapeditor-parser-error'));
                    }
                };
                i18nButtons[mw.msg('mapeditor-remove-button')] = function () {
                    e.overlay.setMap(null);
                    mapEditor.__mapObjects.splice(mapEditor.__mapObjects.indexOf(e.overlay),1);
                    $(this).dialog("close");
                };

                this.dialog({
                    modal:true,
                    buttons:i18nButtons,
                    open:function(){
                        if(e.metadata !== undefined){
                            var isText = true;
                            for(var x = 0; x < e.metadata.length; x++){
                                var data = e.metadata[x];
                                if(data.name === 'link' && data.value.length > 0){
                                    isText = false;
                                    break;
                                }
                            }
                            //depending on existing metadata,
                            //show either form with title/text fields or just link field
                            if(isText){
                                $(this).find('input[value="text"]').trigger('click');
                            }else{
                                $(this).find('input[value="link"]').trigger('click');
                            }
                        }else{
                            //default trigger click on text radio button
                            $(this).find('input[value="text"]').trigger('click');
                        }


                    },
                    beforeClose:function(){
                        //reset the form
                        var form = $(this).find('form');
                        form[0].reset();
                        form.find('.opacity-data-holder').text(mw.msg('mapeditor-none-text'));
                        form.find('.ui-slider').slider('value',-1);
                        form.find('.miniColors').miniColors('value','#fff').val('');
                    }
                });
            }

            if (e.type === 'marker') {
                openDialog.call($('#marker-form'),e);
            } else if (e.type === 'circle') {
                openDialog.call($('#fillable-form'),e);
            } else if (e.type === 'polygon') {
                openDialog.call($('#polygon-form'),e);
            } else if (e.type === 'polyline') {
                openDialog.call($('#strokable-form'),e);
            } else if (e.type === 'rectangle') {
                openDialog.call($('#fillable-form'),e);
            }
        }
    });

    //add custom controls
    mapEditor.addControlButton(mw.msg('mapeditor-export-button'),function(){
        var code = mapEditor.__generateWikiCode();
        $('#code-output').text(code);
        $('#code-output').dialog({
            modal:true,
            width:'80%'
        });
    });

    mapEditor.addControlButton(mw.msg('mapeditor-import-button'), function(){
        var i18nButtons = {}
        i18nButtons[mw.msg('')] = function () {
            var data = $('#code-input').val();
            if(mapEditor.__importWikiCode(data)){
                $(this).dialog('close');
            }else{
                alert('Could not parse input! make sure the input has the same structure as exported wiki code');
            }
        };
        $('#code-input-container').dialog({
            modal:true,
            width:'80%',
            buttons: i18nButtons
        });
    });

    mapEditor.addControlButton(mw.msg('mapeditor-mapparam-button'), function(){
        var i18nButtons = {}
        i18nButtons[mw.msg('mapeditor-done-button')] = function(){
            var data = $(this).find('form input').not('select').serializeArray();
            for(var x = 0; x < data.length; x++){
                mapEditor.__mapParameters[data[x].name].value = data[x].value;
            }
            $(this).dialog('close');
        };
        $('#map-parameter-form').dialog({
            modal:true,
            width: 500,
            buttons:i18nButtons
        });
    });

    mapEditor.addControlButton(mw.msg('mapeditor-clear-button'), function(){
        while(mapEditor.__mapObjects.length > 0){
            var mapObj = mapEditor.__mapObjects.pop();
            mapObj.overlay.setMap(null);
        }
        for(var param in mapEditor.__mapParameters){
            mapEditor.__mapParameters[param].value = undefined;
        }
    });

    //init map parameters
    var formselect = $('#map-parameter-form select[name="key"]');
    formselect.on('change',function(){
        var option = $(this);
        var key = option.val();
        var value = mapEditor.__mapParameters[key].value
        if(value === undefined){
            value = '';
        }

        var parent = option.parent();
        var input = $('<input type="text" name="'+key+'" value="'+value+'"></input>');
        var removeButton = $('<a href="#">[x]</a>');
        var option2 = option.clone(true);
        var container = $('<div></div>');

        option2.find('option[value="'+key+'"]').remove();
        option.attr('disabled',true);
        removeButton.on('click',function(){
            var removedKey = $(this).prevAll('select').val();
            var activeSelect = $(this).parent().parent().find('select').not('select[disabled="disabled"]');
            var option = $('<option value="'+removedKey+'">'+removedKey+'</option>')
            activeSelect.children().first().after(option);
            $(this).parent().remove();
            mapEditor.__mapParameters[key].value = undefined;
            return false;
        });

        parent.append(input);
        parent.append(removeButton);
        parent.parent().append(container);
        container.append(option2);

        input.autocomplete({
            source: mapEditor.__mapParameters[key].values,
            minLength: 0,
            autoFocus: true
        });
        input.autocomplete('search','');
    });

    for(var parameter in mapEditor.__mapParameters){
        var option = $('<option value="'+parameter+'">'+parameter+'</option>');
        formselect.append(option);
    }

    //hide link input initially
    $('input[name="link"]').attr('disabled',true).hide().prev().hide();

    //init text/link switcher
    $('input[name="switch"]').on('click',function(){
        if($(this).val() == 'link'){
            $(this).parent().next().find('input[name="title"],input[name="text"]').attr('disabled',true).hide().prev().hide();
            $(this).parent().next().find('input[name="link"]').attr('disabled',false).show().prev().show();
        }else{
            $(this).parent().next().find('input[name="title"],input[name="text"]').attr('disabled',false).show().prev().show();
            $(this).parent().next().find('input[name="link"]').attr('disabled',true).hide().prev().hide();
        }
    });

    //init enter keypress to press done on dialog.
    $('.mapeditor-dialog').on('keypress',function(e){
        if(e.keyCode === 13){
            $(this).dialog('option','buttons').Done.call(this);
        }
    });

    //init sliders
    $('input[name="strokeOpacity"],input[name="fillOpacity"]').each(function(){
        var input = $(this);
        var dataHolder = $('<span class="opacity-data-holder">'+mw.msg('mapeditor-none-text')+'</span>');
        dataHolder.css({fontSize: 12});
        var slider = $('<div></div>').slider({
            min: -1,
            slide: function(event, ui){
                if(ui.value === -1){
                    input.val('');
                    dataHolder.text(mw.msg('mapeditor-none-text'));
                }else{
                    input.val( ui.value/100 );
                    dataHolder.text(ui.value+'%');
                }
            }
        });
        input.before(dataHolder);
        input.after(slider);
    });

    //init color pickers
    $('input[name="strokeColor"],input[name="fillColor"]').miniColors().miniColors('value','').val('');
});