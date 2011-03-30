/**
 * Horde sidebar javascript.
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 */

var HordeSidebar = {
    // Variables set in services/sidebar.php:
    // domain, path, load, refresh, tree, url, width

    toggleSidebar: function()
    {
        var expanded = $('expandedSidebar').visible(),
            expires = new Date();

        $('expandedSidebar', 'hiddenSidebar').invoke('toggle');
        if ($('themelogo')) {
            $('themelogo').toggle();
        }

        this.setMargin(!expanded);

        // Expire in one year.
        expires.setTime(expires.getTime() + 31536000000);
        document.cookie = 'horde_sidebar_expanded=' + Number(!expanded) + ';DOMAIN=' + this.domain + ';PATH=' + this.path + ';expires=' + expires.toGMTString();
    },

    updateSidebar: function()
    {
        new PeriodicalExecuter(this.loadSidebar.bind(this), this.refresh);
    },

    loadSidebar: function()
    {
        new Ajax.Request(this.url, {
            onComplete: this.onUpdateSidebar.bind(this)
        });
    },

    onUpdateSidebar: function(response)
    {
        var layout, r;

        if (response.responseJSON) {
            $(HordeSidebar.tree.opts.target).update();

            r = response.responseJSON.response;
            this.tree.renderTree(r.nodes, r.root_nodes, r.is_static);
        }
    },

    setMargin: function(expanded)
    {
        var hb = $('horde_body'),
            margin = expanded
            ? this.width
            : $('hiddenSidebar').down().getWidth();

        switch ($(document.body).getStyle('direction')) {
        case 'ltr':
            hb.setStyle({ marginLeft: margin + 'px' });
            break;

        case 'rtl':
            hb.setStyle({ marginRight: margin + 'px' });
            break;
        }
    },

    onDomLoad: function()
    {
        if ($('hiddenSidebar').visible()) {
            this.setMargin(false);
        }

        if (this.refresh) {
            this.updateSidebar.bind(this).delay(this.refresh);
        }
        if (this.load) {
            this.loadSidebar();
        }

        $('expandButton', 'hiddenSidebar').invoke('observe', 'click', this.toggleSidebar.bind(this));
    }

};

document.observe('dom:loaded', HordeSidebar.onDomLoad.bind(HordeSidebar));
