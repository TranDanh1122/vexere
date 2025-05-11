$(document).ready(function(){
    checkUpdate()
    $('body').on('click', '*[data-check__updatecore]', function(){
        $.ajax({
            url: `/${adminDir}/plugins/marketplace/ajax/check-update_package`,
            type: 'POST',
            success: data => {
                window.location.reload();
            }
        });
    })
    $('body').on('click', '.open-wrap__update', function(){
        $(this).parent().find('.wrap-package__update').slideToggle();
    })
})
const checkUpdate = () => {
    $.ajax({
        url: `/${adminDir}/themes/marketplace/ajax/check-update`,
        type: 'POST',
        success: data => {
            let responseApi = data.data || {}
            let themeActive = data.themeInfo || {}
            if (responseApi) {
                let themeCheck = {}
                Object.keys(responseApi).forEach((key) => {
                    const theme = responseApi[key];
                    if(theme.id == themeActive.id) {
                        themeCheck = theme
                    }

                });
                if(themeCheck.id) {
                    let $checkVersion = checkVersion(themeActive.version, themeCheck.version);
                    if ($checkVersion) {
                        $('#notice-theme').css({'display': 'block'});
                        $('#notice-theme .version').text(themeCheck.version);
                        $('#notice-theme .date').text(formatDate(themeCheck.updated_at));
                    }
                }
            }
        }
    });
    // plugin
    $.ajax({
        url: `/${adminDir}/plugins/marketplace/ajax/check-update`,
        type: 'POST',
        success: data => {
            let responseApi = data.data || {}
            let installedPlugins = data.installedPlugins || {}
            if (responseApi) {
                Object.keys(responseApi).forEach((key) => {
                    const plugin = responseApi[key];
                    const pluginCheck = installedPlugins[plugin.id] || '';
                    if(pluginCheck != undefined && pluginCheck != '' && pluginCheck != null) {
                        let $checkVersion = checkVersion(pluginCheck, plugin.version);
                        if ($checkVersion) {
                            $('#notice-plugin').css({'display': 'block'});
                        }
                    }

                });
            }
        }
    });
}
const checkVersion = (currentVersion, latestVersion) => {
    const current = currentVersion.toString().split('.');
    const latest = latestVersion.toString().split('.');

    const length = Math.max(current.length, latest.length);
    for (let i = 0; i < length; i++) {
        const oldVer = ~~current[i];
        const newVer = ~~latest[i];

        if (newVer > oldVer) {
            return true;
        }
    }
    return false;
}
const formatDate = (dateString) => {
    const date = new Date(dateString);
    let day = date.getDate();
    let month = date.getMonth() + 1;
    const year = date.getFullYear();
    day = day < 10 ? ('0' + day) : day
    month = month < 10 ? ('0' + month) : month
    return formattedDate = `${day}-${month}-${year}`;
}
