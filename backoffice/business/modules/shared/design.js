<script type="text/javascript">
    // Create portal
    window.portal = new ZoneSet($F('_wcmClass'), $F('id'), {});
    
    wcmDesign_Init = function ()
    {
        // Update module list
        var moduleList = $('_wcmDesign_widgets');
        var index = 0;

        modules.each(
            function (module) {
                if (module) {
                    var o = document.createElement("option");

                    if (module.title != "") {
                        o.value = index;
                        o.text = module.title;
                    }
                    index++;

                    try {
                        moduleList.add(o);
                    }
                    catch(e) {
                        moduleList.add(o, null);
                    }
                }
            });

        // Update page content
        wcmDesign_UpdatePageContent($F('templateId'));
    }

    wcmDesign_UpdatePageContentZones = function ()
    {
        var html = '';
        $('portal').descendants().each(function(item) {
            if (new RegExp('^portal-column').test(item.className)) {
                html += '<option value="' + item.id + '">' + item.title + '</option>';
            }
        });
        $('_wcmDesign_zones').update(html);
    }


    wcmDesign_UpdatePageContent = function (templateCode)
    {
        wcmBizAjaxController.call(  "biz.updatePageContent", {
                elementId: "_wcmDesign_Page",
                bizobjectId: $F('id'),
                templateId: templateCode,
                widgetMode: $F('_widgetMode')
        });

        wcmDesign_UpdatePageContentZones();
        portal.update();
    }

    wcmDesign_Init();


    // register on save and checkin
    wcmActionController.registerCallback('save', function() { portal.serializeForSubmit($('mainForm'), '_zones'); });
    wcmActionController.registerCallback('checkin', function() { portal.serializeForSubmit($('mainForm'), '_zones'); });

</script>
